<?php
require_once("../config/conexion.php");
require_once("../model/Equipo.php");
require_once("../model/Perfil.php");
$equipo = new Equipo();

// Obtener permisos del usuario para el módulo equipo   
$perfil = new Perfil();
$permisos = $perfil->obtenerPermisosModulo($_SESSION['id'], 'Equipos');

switch($_GET["op"]){

    case "listar":
        $datos = $equipo->listar();
        $data = Array();
        
        foreach($datos as $row){

            $sub_array = array();
            
            $sub_array[] = $row["numero_serie"];
            $sub_array[] = $row["marca"];
            $sub_array[] = $row["modelo"];

            if ($row["tipo_equipo"] == "bn") {
                $sub_array[] = '<span class="badge bg-light-dark f-12">'. $row["nombre_tipo"] .'</span>';
            } else {
                $sub_array[] = '<span class="badge bg-light-info f-12">'. $row["nombre_tipo"] .'</span>';
            }

            if ($row["condicion"] == "nuevo") {
                $sub_array[] = '<span class="badge bg-light-success f-12">'. $row["nombre_condicion"] .'</span>';
            } else {
                $sub_array[] = '<span class="badge bg-light-warning f-12">'. $row["nombre_condicion"] .'</span>';
            }

            $sub_array[] = $row["proveedor"];

            // Columna Estado con validación de permisos
            if ($permisos['puede_editar'] == 1) {
                // Columna Estado
                if ($row["estado"] == 'activo') {
                    $sub_array[] = '<a style="cursor: pointer" onclick="estadoEquipo(' . $row["id"] . ', 2)"><div class="text-success f-12"><i class="fas fa-circle f-10 m-r-10"></i>ACTIVO</div></a>';
                } else if ($row["estado"] == 'mantenimiento') {
                    $sub_array[] = '<a><div class="text-info f-12"><i class="fas fa-circle f-10 m-r-10"></i>MANTENIMIENTO</div></a>';
                } else if ($row["estado"] == 'asignado') {
                    $sub_array[] = '<a><div class="text-warning f-12"><i class="fas fa-circle f-10 m-r-10"></i>ASIGNADO</div></a>';
                } else {
                    $sub_array[] = '<a style="cursor: pointer" onclick="estadoEquipo(' . $row["id"] . ', 1)"><div class="text-secondary f-12"><i class="fas fa-circle f-10 m-r-10"></i>INACTIVO</div></a>';
                }
            } else {

                // Columna Estado
                if ($row["estado"] == 'activo') {
                    $sub_array[] = '
                        <div class="text-success f-12" 
                            style="cursor: not-allowed; opacity: 0.6;" 
                            data-bs-toggle="tooltip" 
                            title="Sin permiso para cambiar estado">
                            <i class="fas fa-circle f-10 m-r-10"></i>ACTIVO
                        </div>';
                } else if ($row["estado"] == 'mantenimiento') {
                    $sub_array[] = '<a><div class="text-info f-12"><i class="fas fa-circle f-10 m-r-10"></i>MANTENIMIENTO</div></a>';
                } else if ($row["estado"] == 'asignado') {
                    $sub_array[] = '<a><div class="text-warning f-12"><i class="fas fa-circle f-10 m-r-10"></i>ASIGNADO</div></a>';
                } else {
                    $sub_array[] = '
                        <div class="text-secondary f-12" 
                            style="cursor: not-allowed; opacity: 0.6;" 
                            data-bs-toggle="tooltip" 
                            title="Sin permiso para cambiar estado">
                            <i class="fas fa-circle f-10 m-r-10"></i>INACTIVO
                        </div>';
                }
            }

            if ($permisos['puede_editar'] == 1) {
                $sub_array[] = '<ul class="list-inline me-auto mb-0">
                                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Editar">
                                        <a class="avtar avtar-xs btn-link-success btn-pc-default" style="cursor: pointer"
                                        onclick="editarEquipo(' . $row["id"] . ')">
                                            <i class="ti ti-edit-circle f-18"></i>
                                        </a>
                                    </li>
                                </ul>';  
            } else {
                $sub_array[] = '
                <ul class="list-inline me-auto mb-0">
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Sin permiso para editar">
                        <a class="avtar avtar-xs btn-link-warning btn-pc-default disabled" 
                        style="cursor: not-allowed; opacity: 0.5;">
                            <i class="ti ti-edit-circle f-18"></i>
                        </a>
                    </li>
                </ul>';
            }                      
            
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

    case "ingresar_editar_equipo":
        try {
            // Capturar datos del formulario
            $equipo_id  = !empty($_POST["equipo_id"]) ? $_POST["equipo_id"] : null;
            $modelo         = $_POST['modelo'];
            $marca          = $_POST['marca'];
            $numero_serie   = $_POST['numero_serie'];
            $tipo_equipo    = $_POST['tipo_equipo'];
            $condicion      = $_POST['condicion'];

            $estado = isset($_POST['estado']) ? $_POST['estado'] : 'activo';

            $proveedor_id           = $_POST["proveedor_id"];

            $fecha_compra           = $_POST["fecha_compra"];
            $costo_dolares          = $_POST["costo_dolares"];
            $costo_soles            = $_POST["costo_soles"];
            $contador_inicial_bn    = $_POST["contador_inicial_bn"];
            $contador_actual_bn     = $_POST["contador_actual_bn"];

            $contador_inicial_color = !empty($_POST['contador_inicial_color']) ? $_POST['contador_inicial_color'] : null;
            $contador_actual_color  = !empty($_POST['contador_actual_color']) ? $_POST['contador_actual_color'] : null;

            $observaciones          = !empty($_POST['observaciones']) ? $_POST['observaciones'] : null;

            // Validar si el ruc ya existe
            $equipo_existe = $equipo->verificarEquipoExiste($numero_serie, $equipo_id);
            if ($equipo_existe) {
                $jsonData['success'] = 0;
                $jsonData['message'] = 'El equipo ya está registrado';
                header('Content-type: application/json; charset=utf-8');
                echo json_encode($jsonData);
                exit;
            }

            if ($equipo_id) {
                // Actualizar proveedor existente
                $equipo->actualizarEquipo(
                    $equipo_id,
                    mb_strtoupper($marca, 'UTF-8'),
                    mb_strtoupper($modelo, 'UTF-8'),
                    mb_strtoupper($numero_serie, 'UTF-8'),
                    $tipo_equipo,
                    $condicion,
                    $proveedor_id,
                    $fecha_compra,      
                    $costo_dolares,
                    $costo_soles, 
                    $contador_inicial_bn,
                    $contador_actual_bn, 
                    $contador_inicial_color,
                    $contador_actual_color,
                    $estado,
                    $observaciones
                );
                // actualizarproveedor maneja su propio JSON response
            } else {
                $equipo->insertarEquipo(
                    mb_strtoupper($marca, 'UTF-8'),
                    mb_strtoupper($modelo, 'UTF-8'),
                    mb_strtoupper($numero_serie, 'UTF-8'),
                    $tipo_equipo,
                    $condicion,
                    $proveedor_id,
                    $fecha_compra,      
                    $costo_dolares,
                    $costo_soles, 
                    $contador_inicial_bn,
                    $contador_actual_bn, 
                    $contador_inicial_color,
                    $contador_actual_color,
                    $estado,
                    $observaciones
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
        $datos = $equipo->obtener_equipo_por_id($_POST["id"]);
        if (is_array($datos) == true and count($datos) > 0) {
           foreach ($datos as $row) {
                $output["id"]                   = $row["id"];
                $output["marca"]                = $row["marca"];
                $output["modelo"]               = $row["modelo"];
                $output["numero_serie"]         = $row["numero_serie"];
                $output["tipo_equipo"]          = $row["tipo_equipo"];
                $output["condicion"]            = $row["condicion"];
                $output["estado"]               = $row["estado"];
                $output["proveedor_id"]         = $row["proveedor_id"];
                $output["proveedor"]            = $row["proveedor"];
                $output["fecha_compra"]         = $row["fecha_compra"];
                $output["costo_dolares"]        = $row["costo_dolares"];
                $output["costo_soles"]          = $row["costo_soles"];
                $output["contador_inicial_bn"]  = $row["contador_inicial_bn"];
                $output["contador_inicial_color"] = $row["contador_inicial_color"];
                $output["contador_actual_bn"]   = $row["contador_actual_bn"];
                $output["contador_actual_color"]   = $row["contador_actual_color"];
                $output["observaciones"]   = $row["observaciones"];
            }
            echo json_encode($output);
        }
        break;

    case "estado_equipo":
        try {

            $resultado = $equipo->editar_estado_equipo($_POST['equipo_id'], $_POST['estado']);
            
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

    //Combo utilizado en formulario de equipo
    case "combo_equipo":
        // Recibir el cliente_id actual (si existe)
        $equipo_id_actual = !empty($_POST['equipo_id_actual']) ? $_POST['equipo_id_actual'] : null;
        
        // Obtener clientes disponibles
        $datos = $equipo->combo_equipo($equipo_id_actual);
        
        if(is_array($datos) == true and count($datos) > 0){
            $html = "<option value=''>-- Seleccionar --</option>";
            foreach($datos as $row){
                $html .= "<option value='".$row['id']."'>".$row['nombre']."</option>";
            }
            echo $html;
        } else {
            echo "<option value=''>-- No hay equipos disponibles --</option>";
        }
        break;
}