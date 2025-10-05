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
            $sub_array[] = $row["nombre"]; // Columna 1: Nombre
                        
            // Columna 2: Generar usuarios dinámicamente
            $usuarios_html = '<a onclick="modalAsignar(' . $row["id"] . ')" style="cursor: pointer;">
                                <div>
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
                        ? "../../../assets/images/user/" . $foto 
                        : "../../../assets/images/user/default.jpg";
                    
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
                $usuarios_html .= '<a onclick="modalAsignar(' . $row["id"] . ')" style="cursor: pointer;"><span class="badge bg-light-warning f-12">Sin usarios</span></a>';
            }
            
            $usuarios_html .= '</div></div></a>';
            $sub_array[] = $usuarios_html;
            
            // Columna 3: Estado
            if ($row["estado"] == 1) {
                $sub_array[] = '<div class="text-success"><i class="fas fa-circle f-10 m-r-10"></i>Activo</div>';
            } else {
                $sub_array[] = '<div class="text-secondary"><i class="fas fa-circle f-10 m-r-10"></i>Inactivo</div>';
            }
            
            // Columna 4: Acciones
            $sub_array[] = '<ul class="list-inline me-auto mb-0">
                                <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Editar">
                                    <a style="cursor: pointer;" class="avtar avtar-xs btn-link-success btn-pc-default" 
                                    onclick="editarPerfil(' . $row["id"] . ')">
                                        <i class="ti ti-edit-circle f-18"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Eliminar">
                                    <a style="cursor: pointer;" class="avtar avtar-xs btn-link-danger btn-pc-default" 
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
                $resultado_permisos = $perfil->guardar_permisos_perfil($perfil_id, $permisos);
                
                if ($resultado_permisos) {
                    echo json_encode([
                        'success' => true,
                        'message' => "Perfil {$mensaje_accion} correctamente",
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
            $permisos_perfil = $perfil->obtener_permisos_perfil($perfil_id);
            
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

    case "obtener_usuarios_disponibles":
        try {
            $perfil_id = $_POST['perfil_id'];
            
            if (empty($perfil_id)) {
                throw new Exception('ID del perfil es requerido');
            }
            
            $usuarios = $perfil->obtener_usuarios_disponibles($perfil_id);
            
            // Formatear para Select2
            $data_select2 = array_map(function($usuario) {
                return [
                    'id' => $usuario['id'],
                    'text' => $usuario['nombre_completo']
                ];
            }, $usuarios);
            
            echo json_encode([
                'success' => true,
                'data' => $data_select2
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        break;

    case "asignar_usuarios":
        try {
            $perfil_id = $_POST['perfil_id'];
            $usuarios_ids = $_POST['usuarios_ids'];
            
            if (empty($perfil_id)) {
                throw new Exception('ID del perfil es requerido');
            }
            
            if (empty($usuarios_ids) || !is_array($usuarios_ids)) {
                throw new Exception('Debe seleccionar al menos un usuario');
            }
            
            $resultado = $perfil->asignar_usuarios_perfil($perfil_id, $usuarios_ids);
            
            if ($resultado['success']) {
                echo json_encode([
                    'success' => true,
                    'message' => $resultado['message']
                ]);
            } else {
                throw new Exception($resultado['message']);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        break;

    case "obtener_usuarios":
        $datos = $perfil->obtener_usuarios($_POST['perfil_id']);
        $data = Array();
        
        foreach($datos as $row){
            $sub_array = array();

            $sub_array[] = '<div class="d-flex align-items-center">
                                  <div class="flex-shrink-0"><img
                                              src="../../../assets/images/user/'. $row["foto_perfil"] . '"
                                              alt="user image"
                                              class="img-radius wid-40">
                                  </div>
                                  <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0">'. $row["nombres"] . ' ' . $row["apellidos"] .'</h6>
                                  </div>
                            </div>';
            
            // Columna 4: Acciones
            $sub_array[] = '<ul class="list-inline me-auto mb-0">
                                <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Eliminar">
                                    <a style="cursor: pointer;" class="avtar avtar-xs btn-link-danger btn-pc-default" 
                                    onclick="eliminarUsuario(' . $row["up_id"] . ')">
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
    
    case "eliminar_usuario_perfil":
        try {
            $usuario_perfil_id = $_POST['usuario_perfil_id'];

            $resultado = $perfil->eliminar_usuario_perfil($usuario_perfil_id);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Asignación eliminada correctamente.'
                ]);
            } else {
                throw new Exception('Error al eliminar la asignación');
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        break;

    case "listar_perfil_combo":
        $datos = $perfil->listar_perfil_combo();
        if(is_array($datos)==true and count($datos)>0){
            $html= "<option value=''>-- Seleccionar --</option>";
            foreach($datos as $row){
                $html.= "<option value='".$row['id']."'>".$row['nombre']."</option>";
            }
            echo $html;
        }
        break;
}