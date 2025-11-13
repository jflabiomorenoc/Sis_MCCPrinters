<?php
require_once("../config/encrypt.php");
require_once("../config/conexion.php");
require_once("../model/Cliente.php");
require_once("../model/Perfil.php");
$cliente = new Cliente();

// Obtener permisos del usuario para el módulo clientes   
$perfil = new Perfil();
$permisos = $perfil->obtenerPermisosModulo($_SESSION['id'], 'Clientes');

switch($_GET["op"]){

    case "listar":
        $datos = $cliente->listar();
        $data = Array();
        
        foreach($datos as $row){

            $link = 'cliente-detalle.php?v='.Encryption::encrypt($row['id']);

            $sub_array = array();
            
            $sub_array[] = $row["cliente"];

            if ($row["tipo_ruc"] == "1") {
                $sub_array[] = '<span class="badge bg-light-info f-12">JURÍDICO</span>';
            } else {
                $sub_array[] = '<span class="badge bg-light-dark f-12">NATURAL</span>';
            }

            $sub_array[] = $row["ruc"];
            
            $sub_array[] = $row["info_direccion"];

            $sub_array[] = $row["usuario"] ? $row["usuario"] : '-';
        
            // Columna Estado con validación de permisos
            if ($permisos['puede_editar'] == 1) {
                // Usuario CON permiso - Mostrar clickeable
                if ($row["estado"] == 1) {
                    $sub_array[] = '
                        <a style="cursor: pointer" 
                        onclick="estadoCliente(' . $row["id"] . ', 2)">
                            <div class="text-success f-12">
                                <i class="fas fa-circle f-10 m-r-10"></i>ACTIVO
                            </div>
                        </a>';
                } else {
                    $sub_array[] = '
                        <a style="cursor: pointer" 
                        onclick="estadoCliente(' . $row["id"] . ', 1)">
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
                                    
            $acciones = '<ul class="list-inline me-auto mb-0">';
            
            // Botón Ver (siempre visible si tiene permiso de ver el módulo)
            if ($permisos['puede_ver'] == 1) {
                $acciones .= '
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Ver detalle">
                        <a class="avtar avtar-xs btn-link-warning btn-pc-default" style="cursor: pointer" href="'.$link.'">
                            <i class="ti ti-eye f-18"></i>
                        </a>
                    </li>';
            }
            
            // Botón Editar (solo si tiene permiso Y el contrato no está bloqueado)
            if ($permisos['puede_editar'] == 1) {
                $acciones .= '<li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Editar">
                                    <a class="avtar avtar-xs btn-link-success btn-pc-default" style="cursor: pointer"
                                    onclick="editarCliente(' . $row["id"] . ')">
                                        <i class="ti ti-edit-circle f-18"></i>
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

    case "ingresar_editar_cliente":
        try {
            // Capturar datos del formulario
            $cliente_id         = !empty($_POST["cliente_id"]) ? $_POST["cliente_id"] : null;
            $tipo_ruc           = $_POST['tipo_ruc'];
            $ruc                = $_POST['ruc'];
            
            // NUEVO: Determinar qué campos usar según tipo_ruc
            if ($tipo_ruc == 1) {
                // Si es RUC (empresa): usar razon_social, anular nombres
                $razon_social       = !empty($_POST['razon_social']) ? $_POST['razon_social'] : null;
                $nombre_cliente     = null;
                $apellido_paterno   = null;
                $apellido_materno   = null;
            } else if ($tipo_ruc == 2) {
                // Si es DNI (persona): usar nombres, anular razon_social
                $razon_social       = null;
                $nombre_cliente     = !empty($_POST['nombre_cliente'])   ? $_POST['nombre_cliente']   : null;
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

            $departamento       = !empty($_POST['departamento']) ? $_POST['departamento'] : null;
            $provincia          = !empty($_POST['provincia'])    ? $_POST['provincia']    : null;
            $distrito           = !empty($_POST['distrito'])     ? $_POST['distrito']     : null;
            $direccion          = !empty($_POST['direccion'])    ? $_POST['direccion']    : null;
            $referencia         = !empty($_POST['referencia'])   ? $_POST['referencia']   : null;
            
            $estado_cliente     = isset($_POST['estado_cliente']) ? $_POST['estado_cliente'] : '1';

            // Validar si el ruc ya existe
            $cliente_existe = $cliente->verificarClienteExiste($ruc, $cliente_id);
            if ($cliente_existe) {
                $jsonData['success'] = 0;
                $jsonData['message'] = 'El cliente ya está registrado';
                header('Content-type: application/json; charset=utf-8');
                echo json_encode($jsonData);
                exit;
            }

            // Insertar o actualizar cliente
            if ($cliente_id) {
                // Actualizar cliente existente
                $cliente->actualizarCliente(
                    $cliente_id,
                    $tipo_ruc,
                    $ruc,
                    mb_strtoupper($razon_social, 'UTF-8'),
                    mb_strtoupper($nombre_cliente, 'UTF-8'),
                    mb_strtoupper($apellido_paterno, 'UTF-8'),
                    mb_strtoupper($apellido_materno, 'UTF-8'),
                    $estado_cliente
                );
                // actualizarCliente maneja su propio JSON response
            } else {
                // Insertar nuevo cliente
                $cliente->insertarCliente(
                    $tipo_ruc,
                    $ruc,
                    mb_strtoupper($razon_social, 'UTF-8'),
                    mb_strtoupper($nombre_cliente, 'UTF-8'),
                    mb_strtoupper($apellido_paterno, 'UTF-8'),
                    mb_strtoupper($apellido_materno, 'UTF-8'),
                    $departamento,
                    $provincia,
                    $distrito,
                    mb_strtoupper($direccion, 'UTF-8'),
                    mb_strtoupper($referencia, 'UTF-8'),
                    $estado_cliente
                );
                // insertarCliente maneja su propio JSON response
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
        $datos = $cliente->obtener_cliente_por_id($_POST["id"]);
        if (is_array($datos) == true and count($datos) > 0) {
           foreach ($datos as $row) {
                $output["id"]               = $row["id"];
                $output["tipo_ruc"]         = $row["tipo_ruc"];
                $output["nom_tipo_ruc"]     = $row["nom_tipo_ruc"];
                $output["ruc"]              = $row["ruc"];
                $output["razon_social"]     = $row["razon_social"];
                $output["nombre_cliente"]   = $row["nombre_cliente"];
                $output["apellido_paterno"] = $row["apellido_paterno"];
                $output["apellido_materno"] = $row["apellido_materno"];
                $output["estado"]           = $row["estado"];
                $output["nom_estado"]       = $row["nom_estado"];
            }
            echo json_encode($output);
        }
        break;

    case "listar_direcciones":
        $datos = $cliente->obtener_direcciones_cliente($_POST["cliente_id"]);
        echo json_encode($datos);
        break;

    case "listar_contactos":
        $datos = $cliente->obtener_contactos_direccion($_POST["direccion_id"]);
        echo json_encode($datos);
        break;

    case "estado_cliente":
        try {

            $resultado = $cliente->editar_estado_cliente($_POST['cliente_id'], $_POST['estado']);
            
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

    case "ingresar_editar_direccion":
        try {
            $cliente_id     = $_POST['cliente_id'];
            $direccion_id   = !empty($_POST["direccion_id"]) ? $_POST["direccion_id"] : null;
            $departamento   = $_POST['departamento'];
            $provincia      = $_POST['provincia'];
            $distrito       = $_POST['distrito'];
            $direccion      = $_POST['direccion'];
            $referencia     = !empty($_POST['referencia']) ? $_POST['referencia'] : null;
            $es_principal   =  isset($_POST['es_principal']) ? $_POST['es_principal'] : '0';

            // Insertar o actualizar dirección
            if ($direccion_id) {
                $cliente->actualizarDireccion(
                    $cliente_id,
                    $direccion_id,
                    $departamento,
                    $provincia,
                    $distrito,
                    mb_strtoupper($direccion, 'UTF-8'),
                    mb_strtoupper($referencia, 'UTF-8'),
                    $es_principal
                );
            } else {
                $cliente->insertarDireccion(
                    $cliente_id,
                    $departamento,
                    $provincia,
                    $distrito,
                    mb_strtoupper($direccion, 'UTF-8'),
                    mb_strtoupper($referencia, 'UTF-8'),
                    $es_principal
                );
            }            
        } catch (Exception $e) {
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($jsonData);
        }
        break;

    case "eliminar_direccion":
        try {

            $resultado = $cliente->eliminar_direccion($_POST['direccion_id']);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Dirección eliminada correctamente'
                ]);
            } else {
                throw new Exception('Error al eliminar dirección');
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        break;
        
    case "obtener_direccion":
        $datos = $cliente->obtener_direccion_x_id($_POST["direccion_id"]);
        if (is_array($datos) == true and count($datos) > 0) {
           foreach ($datos as $row) {
                $output["id"]            = $row["id"];        
                $output["cliente_id"]    = $row["cliente_id"];  
                $output["direccion"]     = $row["direccion"];   
                $output["distrito"]      = $row["distrito"];    
                $output["provincia"]     = $row["provincia"];   
                $output["departamento"]  = $row["departamento"];
                $output["referencia"]    = $row["referencia"]; 
                $output["es_principal"]  = $row["es_principal"];
            }
            echo json_encode($output);
        }
        break;

    case "ingresar_editar_contacto":
        try {
            $c_direccion_id     = $_POST['c_direccion_id'];
            $contacto_id        = !empty($_POST["contacto_id"]) ? $_POST["contacto_id"] : null;
            $nombre_contacto    = $_POST['nombre_contacto'];
            $cargo_contacto     = $_POST['cargo_contacto'];
            $email_contacto     = $_POST['email_contacto'];
            $telefono_contacto  = $_POST['telefono_contacto'];
            $fecha_cumple       = !empty($_POST['fecha_cumple']) ? $_POST['fecha_cumple'] : null;

            // Insertar o actualizar dirección
            if ($contacto_id) {
                $cliente->actualizarContacto(
                    $contacto_id,
                    mb_strtoupper($nombre_contacto, 'UTF-8'),
                    mb_strtoupper($cargo_contacto, 'UTF-8'),
                    mb_strtoupper($email_contacto, 'UTF-8'),
                    $telefono_contacto,
                    $fecha_cumple
                );
            } else {
                $cliente->insertarContacto(
                    $c_direccion_id,
                    mb_strtoupper($nombre_contacto, 'UTF-8'),
                    mb_strtoupper($cargo_contacto, 'UTF-8'),
                    mb_strtoupper($email_contacto, 'UTF-8'),
                    $telefono_contacto,
                    $fecha_cumple
                );
            }            
        } catch (Exception $e) {
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($jsonData);
        }
        break;

    case "eliminar_contacto":
        try {

            $resultado = $cliente->eliminar_contacto($_POST['contacto_id']);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Contacto eliminado correctamente'
                ]);
            } else {
                throw new Exception('Error al eliminar contacto');
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        break;

    case "obtener_contacto":
        $datos = $cliente->obtener_contacto_x_id($_POST["contacto_id"]);
        if (is_array($datos) == true and count($datos) > 0) {
           foreach ($datos as $row) {
                $output["id"]                 = $row["id"];        
                $output["direccion_id"]       = $row["direccion_id"];  
                $output["nombre_contacto"]    = $row["nombre_contacto"];   
                $output["cargo_contacto"]     = $row["cargo_contacto"];    
                $output["email_contacto"]     = $row["email_contacto"];   
                $output["telefono_contacto"]  = $row["telefono_contacto"];
                $output["fecha_cumple"]       = $row["fecha_cumple"]; 
            }
            echo json_encode($output);
        }
        break;

    case "combo_cliente":
        // Recibir el cliente_id actual (si existe)
        $cliente_id_actual = !empty($_POST['cliente_id_actual']) ? $_POST['cliente_id_actual'] : null;
        
        // Obtener clientes disponibles
        $datos = $cliente->combo_cliente($cliente_id_actual);
        
        if(is_array($datos) == true and count($datos) > 0){
            $html = "<option value=''>-- Seleccionar --</option>";
            foreach($datos as $row){
                $html .= "<option value='".$row['id']."'>".$row['nombre_cliente']."</option>";
            }
            echo $html;
        } else {
            echo "<option value=''>-- No hay proveedores disponibles --</option>";
        }
        break;

    case "combo_cliente_contrato":
        // Recibir el cliente_id actual (si existe)
        $cliente_id_actual = !empty($_POST['cliente_id_actual']) ? $_POST['cliente_id_actual'] : null;
        
        // Obtener clientes disponibles
        $datos = $cliente->combo_cliente_contrato($cliente_id_actual);
        
        if(is_array($datos) == true and count($datos) > 0){
            $html = "<option value=''>-- Seleccionar --</option>";
            foreach($datos as $row){
                $html .= "<option value='".$row['id']."'>".$row['nombre_cliente']."</option>";
            }
            echo $html;
        } else {
            echo "<option value=''>-- No hay proveedores disponibles --</option>";
        }
        break;
    
    case "combo_direccion":
        // Recibir el cliente_id y direccion_id_actual (si existe)
        $cliente_id = !empty($_POST['cliente_id']) ? $_POST['cliente_id'] : null;
        $direccion_id_actual = !empty($_POST['direccion_id_actual']) ? $_POST['direccion_id_actual'] : null;
        
        if ($cliente_id) {
            // Obtener direcciones del cliente
            $datos = $cliente->combo_direccion_cliente($cliente_id, $direccion_id_actual);
            
            if(is_array($datos) == true and count($datos) > 0){
                $html = "<option value=''>-- Seleccionar dirección --</option>";
                foreach($datos as $row){
                    $es_principal = $row['es_principal'] == 1 ? ' [PRINCIPAL]' : '';
                    $html .= "<option value='".$row['id']."'>".$row['direccion_completa'].$es_principal."</option>";
                }
                echo $html;
            } else {
                echo "<option value=''>-- Este cliente no tiene direcciones registradas --</option>";
            }
        } else {
            echo "<option value=''>-- Seleccione un cliente primero --</option>";
        }
        break;
}