<?php
require_once("../config/conexion.php");
require_once("../model/Perfil.php");
$perfil = new Perfil();

switch($_GET["op"]){   
    case "listar":
        $datos = $perfil->listar();
        $data = Array();
        
        foreach($datos as $row){
            $sub_array = array();
            $sub_array[] = ''; // Columna 0: vacía para control responsive
            $sub_array[] = $row["nombre"]; // Columna 1: Nombre
            
            // Columna 2: Generar usuarios dinámicamente
            $usuarios_html = '<div class="">
                                <div class="user-group able-user-group">';
            
            $total_usuarios = intval($row["total_usuarios"]);
            
            if ($total_usuarios > 0 && !empty($row["usuarios_data"])) {
                $usuarios_array = explode(';;', $row["usuarios_data"]);
                $mostrar_usuarios = array_slice($usuarios_array, 0, 5); // Máximo 5
                
                foreach ($mostrar_usuarios as $usuario_data) {
                    $datos_usuario = explode('|', $usuario_data);
                    $nombre = $datos_usuario[0];
                    $apellido = $datos_usuario[1];
                    $foto = $datos_usuario[2];
                    
                    // Ruta de la foto (ajusta según tu estructura)
                    $foto_path = ($foto && $foto != 'default.jpg') 
                        ? "../../../uploads/usuarios/" . $foto 
                        : "../../../assets/images/user/default-avatar.jpg";
                    
                    $usuarios_html .= '<img src="' . $foto_path . '" alt="user-image" class="avtar" 
                                        data-bs-toggle="tooltip" title="' . $nombre . ' ' . $apellido . '">';
                }
                
                // Si hay más de 5 usuarios, mostrar el contador
                if ($total_usuarios > 5) {
                    $restantes = $total_usuarios - 5;
                    $usuarios_html .= '<span class="avtar bg-light-primary text-primary text-sm">+' . $restantes . '</span>';
                }
            } else {
                // No hay usuarios asignados
                $usuarios_html .= '<span class="badge bg-light-warning f-12">Sin usarios</span>';
            }
            
            $usuarios_html .= '</div></div>';
            $sub_array[] = $usuarios_html;
            
            // Columna 3: Estado
            if ($row["estado"] == 1) {
                $sub_array[] = '<span class="badge bg-light-success f-12">Activo</span>';
            } else {
                $sub_array[] = '<span class="badge bg-light-danger f-12">Inactivo</span>';
            }
            
            // Columna 4: Acciones
            $sub_array[] = '<ul class="list-inline me-auto mb-0">
                                <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Ver">
                                    <a href="#" class="avtar avtar-xs btn-link-secondary btn-pc-default" 
                                    onclick="verPerfil(' . $row["id"] . ')">
                                        <i class="ti ti-eye f-18"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Editar">
                                    <a href="#" class="avtar avtar-xs btn-link-success btn-pc-default" 
                                    onclick="editarPerfil(' . $row["id"] . ')">
                                        <i class="ti ti-edit-circle f-18"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Eliminar">
                                    <a href="#" class="avtar avtar-xs btn-link-danger btn-pc-default" 
                                    onclick="eliminarPerfil(' . $row["id"] . ')">
                                        <i class="ti ti-trash f-18"></i>
                                    </a>
                                </li>
                            </ul>';
            
            $data[] = $sub_array;
        }
        
        $results = array(
            "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );
        echo json_encode($results);
        break;

    case "guardar_perfil":
        try {
            $perfil_id = isset($_POST['perfil_id']) ? $_POST['perfil_id'] : null;
            $nombre_perfil = $_POST['nombre_perfil'];
            $estado_perfil = isset($_POST['estado_perfil']) ? 1 : 0;
            $permisos = isset($_POST['permisos']) ? $_POST['permisos'] : [];
            
            if (empty($nombre_perfil)) {
                throw new Exception('El nombre del perfil es requerido');
            }
            
            // Si es edición, verificar que el perfil existe
            if (!empty($perfil_id)) {
                $perfil_existente = $perfil->obtener_perfil_por_id($perfil_id);
                if (!$perfil_existente) {
                    throw new Exception('El perfil no existe');
                }
                
                // Actualizar perfil
                $resultado_perfil = $perfil->editar($perfil_id, $nombre_perfil, $estado_perfil);
                $mensaje_accion = 'actualizado';
            } else {
                // Verificar que no existe un perfil con el mismo nombre
                $perfil_duplicado = $perfil->verificar_nombre_perfil($nombre_perfil);
                if ($perfil_duplicado) {
                    throw new Exception('Ya existe un perfil con ese nombre');
                }
                
                // Crear nuevo perfil
                $resultado_perfil = $perfil->insertar($nombre_perfil, $estado_perfil);
                $perfil_id = $resultado_perfil; // El método insert debe retornar el ID del nuevo perfil
                $mensaje_accion = 'creado';
            }
            
            if ($resultado_perfil) {
                // Guardar permisos
                $resultado_permisos = $modulo->guardar_permisos_perfil($perfil_id, $permisos);
                
                if ($resultado_permisos) {
                    echo json_encode([
                        'success' => true,
                        'message' => "Perfil {$mensaje_accion} correctamente con sus permisos",
                        'perfil_id' => $perfil_id
                    ]);
                } else {
                    // Si falló al guardar permisos pero el perfil se creó/actualizó
                    echo json_encode([
                        'success' => true,
                        'message' => "Perfil {$mensaje_accion} correctamente, pero hubo un error al asignar permisos",
                        'perfil_id' => $perfil_id,
                        'warning' => true
                    ]);
                }
            } else {
                throw new Exception("Error al {$mensaje_accion} el perfil");
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        break;

    case "obtener_perfil":
        try {
            $perfil_id = $_POST['perfil_id'];
            
            if (empty($perfil_id)) {
                throw new Exception('ID del perfil es requerido');
            }
            
            // Obtener datos del perfil
            $datos_perfil = $perfil->obtener_perfil_por_id($perfil_id);
            
            if (!$datos_perfil) {
                throw new Exception('Perfil no encontrado');
            }
            
            // Obtener permisos del perfil
            $permisos_perfil = $modulo->obtener_permisos_perfil($perfil_id);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'perfil' => $datos_perfil,
                    'permisos' => $permisos_perfil
                ]
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        break;

    case "eliminar_perfil":
        try {
            $perfil_id = $_POST['perfil_id'];
            
            if (empty($perfil_id)) {
                throw new Exception('ID del perfil es requerido');
            }
            
            // Verificar que el perfil no esté siendo usado por usuarios
            $usuarios_con_perfil = $perfil->verificar_perfil_en_uso($perfil_id);
            if ($usuarios_con_perfil > 0) {
                throw new Exception('No se puede eliminar el perfil porque está siendo usado por usuarios');
            }
            
            $resultado = $perfil->eliminar($perfil_id);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Perfil eliminado correctamente'
                ]);
            } else {
                throw new Exception('Error al eliminar el perfil');
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        break;
}