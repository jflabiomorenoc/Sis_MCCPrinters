<?php
require_once("../config/conexion.php");
require_once("../model/Proveedor.php");
require_once("../model/Perfil.php");
$proveedor = new Proveedor();

// Obtener permisos del usuario para el módulo proveedores   
$perfil = new Perfil();
$permisos = $perfil->obtenerPermisosModulo($_SESSION['id'], 'Proveedores');

switch($_GET["op"]){

    case "listar":
        $datos = $proveedor->listar();
        $data = Array();
        
        foreach($datos as $row){

            $sub_array = array();
            
            $sub_array[] = $row["proveedor"];

            if ($row["tipo_ruc"] == "1") {
                $sub_array[] = '<span class="badge bg-light-info f-12">JURÍDICO</span>';
            } else {
                $sub_array[] = '<span class="badge bg-light-dark f-12">NATURAL</span>';
            }

            $sub_array[] = $row["ruc"];

            $sub_array[] = $row["contacto"] ? $row["contacto"] : '-';
            
            $sub_array[] = $row["direccion"];

            // Columna Estado con validación de permisos
            if ($permisos['puede_editar'] == 1) {
                // Usuario CON permiso - Mostrar clickeable
                if ($row["estado"] == 1) {
                    $sub_array[] = '
                        <a style="cursor: pointer" 
                        onclick="estadoProveedor(' . $row["id"] . ', 2)">
                            <div class="text-success f-12">
                                <i class="fas fa-circle f-10 m-r-10"></i>ACTIVO
                            </div>
                        </a>';
                } else {
                    $sub_array[] = '
                        <a style="cursor: pointer" 
                        onclick="estadoProveedor(' . $row["id"] . ', 1)">
                            <div class="text-secondary f-12">
                                <i class="fas fa-circle f-10 m-r-10"></i>INACTIVO
                            </div>
                        </a>';
                }
            } else {
                // Usuario SIN permiso - Mostrar solo texto (sin onclick)
                if ($row["estado"] == 1) {
                    $sub_array[] = '
                        <div class="text-success f-12" 
                            style="cursor: not-allowed; opacity: 0.6;" 
                            data-bs-toggle="tooltip" 
                            title="Sin permiso para cambiar estado">
                            <i class="fas fa-circle f-10 m-r-10"></i>ACTIVO
                        </div>';
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
                                    onclick="editarProveedor(' . $row["id"] . ')">
                                        <i class="ti ti-edit-circle f-18"></i>
                                    </a>
                                </li>
                            </ul>';  
            } else {
                $sub_array[] = '
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Sin permiso para editar">
                        <a class="avtar avtar-xs btn-link-warning btn-pc-default disabled" 
                        style="cursor: not-allowed; opacity: 0.5;">
                            <i class="ti ti-edit-circle f-18"></i>
                        </a>
                    </li>';
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

    case "ingresar_editar_proveedor":
        try {
            // Capturar datos del formulario
            $proveedor_id  = !empty($_POST["proveedor_id"]) ? $_POST["proveedor_id"] : null;
            $tipo_ruc      = $_POST['tipo_ruc'];
            $ruc           = $_POST['ruc'];
            
            // NUEVO: Determinar qué campos usar según tipo_ruc
            if ($tipo_ruc == 1) {
                // Si es RUC (empresa): usar razon_social, anular nombres
                $razon_social       = !empty($_POST['razon_social']) ? $_POST['razon_social'] : null;
                $nombre_proveedor   = null;
                $apellido_paterno   = null;
                $apellido_materno   = null;
            } else if ($tipo_ruc == 2) {
                // Si es DNI (persona): usar nombres, anular razon_social
                $razon_social       = null;
                $nombre_proveedor   = !empty($_POST['nombre_proveedor']) ? $_POST['nombre_proveedor'] : null;
                $apellido_paterno   = !empty($_POST['apellido_paterno']) ? $_POST['apellido_paterno'] : null;
                $apellido_materno   = !empty($_POST['apellido_materno']) ? $_POST['apellido_materno'] : null;
            } else {
                // Tipo no válido
                $jsonData['success'] = 0;
                $jsonData['message'] = 'Tipo de documento no válido';
                header('Content-type: application/json; charset=utf-8');
                echo json_encode($jsonData);
                exit;
            }

            $contacto    = !empty($_POST['contacto']) ? $_POST['contacto'] : null;
            $email       = $_POST['email'];
            $telefono    = $_POST['telefono'];
            $direccion   = $_POST['direccion'];
            $estado_proveedor = isset($_POST['estado_proveedor']) ? $_POST['estado_proveedor'] : '1';

            // Validar si el ruc ya existe
            $proveedor_existe = $proveedor->verificarProveedorExiste($ruc, $proveedor_id);
            if ($proveedor_existe) {
                $jsonData['success'] = 0;
                $jsonData['message'] = 'El proveedor ya está registrado';
                header('Content-type: application/json; charset=utf-8');
                echo json_encode($jsonData);
                exit;
            }

            if ($proveedor_id) {
                // Actualizar proveedor existente
                $proveedor->actualizarProveedor(
                    $proveedor_id,
                    $tipo_ruc,
                    $ruc,
                    mb_strtoupper($razon_social, 'UTF-8'),
                    mb_strtoupper($nombre_proveedor, 'UTF-8'),
                    mb_strtoupper($apellido_paterno, 'UTF-8'),
                    mb_strtoupper($apellido_materno, 'UTF-8'),
                    mb_strtoupper($contacto, 'UTF-8'),
                    mb_strtoupper($email, 'UTF-8'),
                    $telefono,
                    mb_strtoupper($direccion, 'UTF-8'),
                    $estado_proveedor
                );
                // actualizarproveedor maneja su propio JSON response
            } else {
                $proveedor->insertarProveedor(
                    $tipo_ruc,
                    $ruc,
                    mb_strtoupper($razon_social, 'UTF-8'),
                    mb_strtoupper($nombre_proveedor, 'UTF-8'),
                    mb_strtoupper($apellido_paterno, 'UTF-8'),
                    mb_strtoupper($apellido_materno, 'UTF-8'),
                    mb_strtoupper($contacto, 'UTF-8'),
                    mb_strtoupper($email, 'UTF-8'),
                    $telefono,
                    mb_strtoupper($direccion, 'UTF-8'),
                    $estado_proveedor
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
        $datos = $proveedor->obtener_proveedor_por_id($_POST["id"]);
        if (is_array($datos) == true and count($datos) > 0) {
           foreach ($datos as $row) {
                $output["id"]               = $row["id"];
                $output["tipo_ruc"]         = $row["tipo_ruc"];
                $output["nom_tipo_ruc"]     = $row["nom_tipo_ruc"];
                $output["ruc"]              = $row["ruc"];
                $output["razon_social"]     = $row["razon_social"];
                $output["nombre_proveedor"] = $row["nombre_proveedor"];
                $output["apellido_paterno"] = $row["apellido_paterno"];
                $output["apellido_materno"] = $row["apellido_materno"];
                $output["direccion"]        = $row["direccion"];
                $output["telefono"]         = $row["telefono"];
                $output["email"]            = $row["email"];
                $output["contacto"]         = $row["contacto"];
                $output["estado"]           = $row["estado"];
                $output["nom_estado"]       = $row["nom_estado"];
            }
            echo json_encode($output);
        }
        break;

    case "estado_proveedor":
        try {

            $resultado = $proveedor->editar_estado_proveedor($_POST['proveedor_id'], $_POST['estado']);
            
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
    case "combo_proveedor":
        // Recibir el cliente_id actual (si existe)
        $proveedor_id_actual = !empty($_POST['proveedor_id_actual']) ? $_POST['proveedor_id_actual'] : null;
        
        // Obtener clientes disponibles
        $datos = $proveedor->combo_proveedor($proveedor_id_actual);
        
        if(is_array($datos) == true and count($datos) > 0){
            $html = "<option value=''>-- Seleccionar --</option>";
            foreach($datos as $row){
                $html .= "<option value='".$row['id']."'>".$row['nombre_proveedor']."</option>";
            }
            echo $html;
        } else {
            echo "<option value=''>-- No hay proveedores disponibles --</option>";
        }
        break;
}