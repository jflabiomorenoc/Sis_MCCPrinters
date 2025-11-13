<?php
require_once("../config/encrypt.php");
require_once("../config/conexion.php");
require_once("../model/Contrato.php");
require_once("../model/Perfil.php");
$contrato = new Contrato();

// Obtener permisos del usuario para el módulo Contratos   
$perfil = new Perfil();
$permisos = $perfil->obtenerPermisosModulo($_SESSION['id'], 'Contratos');

switch($_GET["op"]){

    case "listar":
        // Verificar sesión
        if (!isset($_SESSION['id'])) {
            echo json_encode([
                "draw" => 1,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            ]);
            exit;
        }
        
        $datos = $contrato->listar();
        $data = Array();
        
        foreach($datos as $row){
            $link = 'contrato-detalle.php?v='.Encryption::encrypt($row['id']);
            $sub_array = array();
            
            $sub_array[] = $row["numero_contrato"];
            $sub_array[] = $row["cliente"];            
            $sub_array[] = date("d/m/Y", strtotime($row['fecha_inicio']));
            $sub_array[] = $row["fecha_culminacion"] ? date("d/m/Y", strtotime($row['fecha_culminacion'])) : '-';
        
            // Columna Estado
            if ($row["estado"] == 'pendiente') {
                $sub_array[] = '<a style="cursor: pointer" onclick="estadoContrato(' . $row["id"] . ', 1)"><div class="text-secondary f-12"><i class="fas fa-circle f-10 m-r-10"></i>PENDIENTE</div></a>';
            } else if ($row["estado"] == 'vigente') {
                $sub_array[] = '<a style="cursor: pointer" onclick="estadoContrato(' . $row["id"] . ', 1)"><div class="text-warning f-12"><i class="fas fa-circle f-10 m-r-10"></i>VIGENTE</div></a>';
            } else if ($row["estado"] == 'finalizado') {
                $sub_array[] = '<a style="cursor: pointer" onclick="estadoContrato(' . $row["id"] . ', 2)"><div class="text-success f-12"><i class="fas fa-circle f-10 m-r-10"></i>FINALIZADO</div></a>';
            } else {
                $sub_array[] = '<a style="cursor: pointer" onclick="estadoContrato(' . $row["id"] . ', 3)"><div class="text-danger f-12"><i class="fas fa-circle f-10 m-r-10"></i>CANCELADO</div></a>';
            }
            
            // Verificar si el contrato está bloqueado por su estado
            $estaBloqueado = ($row['estado'] === 'cancelado' || $row['estado'] === 'finalizado');
            
            // Construir columna de acciones con permisos
            $acciones = '<ul class="list-inline me-auto mb-0">';
            
            // Botón Ver (siempre visible si tiene permiso de ver el módulo)
            if ($permisos['puede_ver'] == 1) {
                $acciones .= '
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Ver detalle">
                        <a class="avtar avtar-xs btn-link-warning btn-pc-default" 
                        style="cursor: pointer" 
                        href="' . $link . '">
                            <i class="ti ti-eye f-18"></i>
                        </a>
                    </li>';
            }
            
            // Botón Editar (solo si tiene permiso Y el contrato no está bloqueado)
            if ($permisos['puede_editar'] == 1) {
                if ($estaBloqueado) {
                    // Mostrar deshabilitado si está bloqueado
                    $acciones .= '
                        <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="No disponible">
                            <a class="avtar avtar-xs btn-link-success btn-pc-default disabled pe-none opacity-50" 
                            style="cursor: not-allowed">
                                <i class="ti ti-edit-circle f-18"></i>
                            </a>
                        </li>';
                } else {
                    // Mostrar activo si no está bloqueado
                    $acciones .= '
                        <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Editar">
                            <a class="avtar avtar-xs btn-link-success btn-pc-default" 
                            style="cursor: pointer"
                            onclick="editarContrato(' . $row["id"] . ')">
                                <i class="ti ti-edit-circle f-18"></i>
                            </a>
                        </li>';
                }
            }
            
            $acciones .= '</ul>';
            $sub_array[] = $acciones;
            
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

    case "ingresar_editar_contrato":
        try {
            // Capturar datos del formulario
            $contrato_id        = !empty($_POST["contrato_id"]) ? $_POST["contrato_id"] : null;
            $cliente_id         = $_POST['cliente_id'];
            $tecnico_id         = !empty($_POST["tecnico_id"]) ? $_POST["tecnico_id"] : null;
            $fecha_inicio       = $_POST['fecha_inicio'];
            $fecha_culminacion  = !empty($_POST["fecha_culminacion"]) ? $_POST["fecha_culminacion"] : null;

            $observaciones      = !empty($_POST['observaciones']) ? $_POST['observaciones'] : null;

            if ($contrato_id) {
                // Actualizar proveedor existente
                $contrato->actualizarContrato(
                    $contrato_id,
                    $cliente_id,
                    $tecnico_id,
                    $fecha_inicio,
                    $fecha_culminacion,
                    mb_strtoupper($observaciones, 'UTF-8')
                );
                // actualizarproveedor maneja su propio JSON response
            } else {
                $contrato->insertarContrato(
                    $cliente_id,
                    $tecnico_id,
                    $fecha_inicio,
                    $fecha_culminacion,
                    mb_strtoupper($observaciones, 'UTF-8')
                );
            }
            // No hay exit aquí porque los métodos ya enviaron la respuesta
            
        } catch (Exception $e) {
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($jsonData);
        }
        break;

    case "obtener":
        $datos = $contrato->obtener_contrato_por_id($_POST["id"]);
        if (is_array($datos) == true and count($datos) > 0) {
           foreach ($datos as $row) {
                $output["id"]                  = $row["id"];
                $output["numero_contrato"]     = $row["numero_contrato"];
                $output["cliente_id"]          = $row["cliente_id"];
                $output["nombre_cliente"]      = $row["nombre_cliente"];
                $output["tecnico_id"]          = $row["tecnico_id"];
                $output["nombre_tecnico"]      = $row["nombre_tecnico"];
                $output["fecha_inicio"]        = $row['fecha_inicio'];  // Mantener formato YYYY-MM-DD
                $output["fecha_culminacion"]   = $row['fecha_culminacion'];  // Mantener formato YYYY-MM-DD
                $output["estado"]              = $row["estado"];
                $output["observaciones"]       = $row["observaciones"];
            }
            echo json_encode($output);
        }
        break;
    
    case "estado_contrato":
        try {

            $resultado = $contrato->editar_estado_contrato($_POST['id'], $_POST['estado']);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Actualización de estado correcta'
                ]);
            } else {
                throw new Exception('Error al actualizar estado');
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        break;

    case "listar_equipos":
        $contrato_id = $_POST['contrato_id'];
        $datos = $contrato->obtener_equipos_contrato($contrato_id);
        $data = Array();
        
        foreach($datos as $row) {
            $sub_array = array();
            
            // Columna 1: Serie
            $sub_array[] = '<strong>' . $row["numero_serie"] . '</strong>';
            
            // Columna 2: Marca/Modelo
            $sub_array[] = $row["marca"] . ' ' . $row["modelo"];
            
            // Columna 3: IP Asignada
            $sub_array[] = !empty($row["ip_equipo"]) ? $row["ip_equipo"] : '-';
            
            // Columna 4: Ubicación
            $sub_array[] = !empty($row["area_ubicacion"]) ? $row["area_ubicacion"] : '-';
            
            // Columna 5: Contador BN
            $sub_array[] = $row["contador_inicial_bn"];
            
            // Columna 6: Contador Color
            $sub_array[] = $row["contador_inicial_color"];
            
            // Columna 7: Estado
            $estadoClass = $row['estado'] === 'vigente' ? 'warning' : 
                        ($row['estado'] === 'finalizado' ? 'success' : 'danger');
            $sub_array[] = '<span class="badge bg-light-' . $estadoClass . ' f-12">' . 
                        strtoupper($row["estado"]) . '</span>';
            
            // Columna 8: Acciones con permisos y estados
            $estaBloqueado = ($row['estado'] === 'cancelado' || $row['estado'] === 'finalizado');

            // Verificar permisos del usuario
            $puedeEditar = ($permisos['puede_editar'] == 1);
            $puedeEliminar = ($permisos['puede_eliminar'] == 1);

            $acciones = '<ul class="list-inline mb-0">';

            // ========== BOTÓN EDITAR - SIEMPRE VISIBLE ==========
            // Determinar si debe estar deshabilitado (por falta de permiso O por estado bloqueado)
            $editarDeshabilitado = !$puedeEditar || $estaBloqueado;

            if ($editarDeshabilitado) {
                // Mostrar DESHABILITADO (sin permiso o equipo bloqueado)
                $tooltipEditar = !$puedeEditar ? 'Sin permiso para editar' : 'No disponible';
                $acciones .= '
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="' . $tooltipEditar . '">
                        <a class="avtar avtar-xs btn-link-warning btn-pc-default disabled" 
                        style="cursor: not-allowed; opacity: 0.5;">
                            <i class="ti ti-edit-circle f-18"></i>
                        </a>
                    </li>';
            } else {
                // Mostrar ACTIVO (tiene permiso y equipo NO bloqueado)
                $acciones .= '
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Editar">
                        <a class="avtar avtar-xs btn-link-warning btn-pc-default" 
                        style="cursor: pointer"
                        onclick="editarEquipo(' . $row["contrato_equipo_id"] . ')">
                            <i class="ti ti-edit-circle f-18"></i>
                        </a>
                    </li>';
            }

            // ========== BOTÓN ELIMINAR - SIEMPRE VISIBLE ==========
            // Determinar si debe estar deshabilitado (por falta de permiso O por estado bloqueado)
            $eliminarDeshabilitado = !$puedeEliminar || $estaBloqueado;

            if ($eliminarDeshabilitado) {
                // Mostrar DESHABILITADO (sin permiso o equipo bloqueado)
                $tooltipEliminar = !$puedeEliminar ? 'Sin permiso para eliminar' : 'No disponible';
                $acciones .= '
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="' . $tooltipEliminar . '">
                        <a class="avtar avtar-xs btn-link-danger btn-pc-default disabled" 
                        style="cursor: not-allowed; opacity: 0.5;">
                            <i class="ti ti-trash f-18"></i>
                        </a>
                    </li>';
            } else {
                // Mostrar ACTIVO (tiene permiso y equipo NO bloqueado)
                $acciones .= '
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Eliminar">
                        <a class="avtar avtar-xs btn-link-danger btn-pc-default" 
                        style="cursor: pointer"
                        onclick="eliminarEquipo(' . $row["contrato_equipo_id"] . ')">
                            <i class="ti ti-trash f-18"></i>
                        </a>
                    </li>';
            }

            $acciones .= '</ul>';
            $sub_array[] = $acciones;
            
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

    case "guardar_equipo":
        try {
            $contrato_equipo_id = !empty($_POST['contrato_equipo_id']) ? $_POST['contrato_equipo_id'] : null;
            $contrato_id = $_POST['contrato_id'];
            $direccion_id = $_POST['direccion_id'];
            $equipo_id = $_POST['equipo_id'];
            $ip_equipo = !empty($_POST['ip_equipo']) ? $_POST['ip_equipo'] : null;
            $area_ubicacion = !empty($_POST['area_ubicacion']) ? $_POST['area_ubicacion'] : null;
            
            // Normalizar contadores - convertir vacíos a 0
            $contador_inicial_bn    = (!empty($_POST['contador_inicial_bn']) && $_POST['contador_inicial_bn'] !== '') ? intval($_POST['contador_inicial_bn']) : 0;
            $contador_final_bn      = (!empty($_POST['contador_final_bn']) && $_POST['contador_final_bn'] !== '') ? intval($_POST['contador_final_bn']) : 0;
            $contador_inicial_color = (!empty($_POST['contador_inicial_color']) && $_POST['contador_inicial_color'] !== '') ? intval($_POST['contador_inicial_color']) : 0;
            $contador_final_color   = (!empty($_POST['contador_final_color']) && $_POST['contador_final_color'] !== '') ? intval($_POST['contador_final_color']) : 0;
            
            if ($contrato_equipo_id) {
                $resultado = $contrato->actualizar_equipo_contrato(
                    $contrato_equipo_id,
                    $direccion_id, 
                    $equipo_id, 
                    $ip_equipo, 
                    mb_strtoupper($area_ubicacion, 'UTF-8'),
                    $contador_inicial_bn, 
                    $contador_final_bn,
                    $contador_inicial_color, 
                    $contador_final_color
                );
            } else {
                $resultado = $contrato->insertar_equipo_contrato(
                    $contrato_id, 
                    $direccion_id,
                    $equipo_id,
                    $ip_equipo, 
                    mb_strtoupper($area_ubicacion, 'UTF-8'), 
                    $contador_inicial_bn, 
                    $contador_final_bn,
                    $contador_inicial_color, 
                    $contador_final_color
                );
            }
        } catch (Exception $e) {
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($jsonData);
        }
        break;

    case "obtener_equipo":
        $id = $_POST['id'];
        $datos = $contrato->obtener_equipo_contrato($id);
        echo json_encode($datos);
        break;

    case "eliminar_equipo":
        try {
            $id = $_POST['id'];            
            $resultado = $contrato->eliminar_equipo_contrato($id);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Equipo eliminado correctamente'
                ]);
            } else {
                throw new Exception('Error al eliminar el equipo');
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        break;

    case "combo_contratos_cliente":
        $cliente_id = $_POST['cliente_id'];
        $contrato_id_actual = !empty($_POST['contrato_id_actual']) ? $_POST['contrato_id_actual'] : null;
        
        $datos = $contrato->get_contratos_por_cliente($cliente_id, $contrato_id_actual);
        
        if(is_array($datos) && count($datos) > 0){
            $html = "<option value=''>-- Seleccionar --</option>";
            foreach($datos as $row){
                $selected = ($contrato_id_actual && $row['id'] == $contrato_id_actual) ? 'selected' : '';
                // Agregar data-tecnico para almacenar el tecnico_id
                $tecnico_id = $row['tecnico_id'] ? $row['tecnico_id'] : '';
                $html .= "<option value='".$row['id']."' data-tecnico='".$tecnico_id."' ".$selected.">".$row['numero_contrato']."</option>";
            }
            echo $html;
        } else {
            echo "<option value=''>No hay contratos disponibles</option>";
        }
        break;

    case "combo_equipos_contrato":
        $contrato_id = $_POST['contrato_id'];
        $equipo_id_actual = !empty($_POST['equipo_id_actual']) ? $_POST['equipo_id_actual'] : null;
        
        $datos = $contrato->get_equipos_por_contrato($contrato_id, $equipo_id_actual);
        
        if(is_array($datos) && count($datos) > 0){
            $html = "<option value=''>-- Seleccionar --</option>";
            foreach($datos as $row){
                $selected = ($equipo_id_actual && $row['equipo_id'] == $equipo_id_actual) ? 'selected' : '';
                $equipoInfo = $row['marca']." ".$row['modelo']." - ".$row['numero_serie'];
                $html .= "<option value='".$row['equipo_id']."' ".$selected.">".$equipoInfo."</option>";
            }
            echo $html;
        } else {
            echo "<option value=''>No hay equipos disponibles</option>";
        }
        break;
}