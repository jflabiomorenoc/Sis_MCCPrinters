<?php
require_once("../config/conexion.php");
require_once("../model/Cliente.php");
$cliente = new Cliente();

switch($_GET["op"]){

    case "listar":
        $datos = $cliente->listar();
        $data = Array();
        
        foreach($datos as $row){
            $sub_array = array();
            
            $sub_array[] = $row["razon_social"];
            $sub_array[] = $row["tipo_ruc"];
            $sub_array[] = $row["ruc"];
            $sub_array[] = $row["contacto_principal"];
            $sub_array[] = $row["email_contacto"];
            $sub_array[] = $row["telefono_contacto"];
        
            // Columna Estado
            if ($row["estado"] == 1) {
                $sub_array[] = '<div class="text-success"><i class="fas fa-circle f-10 m-r-10"></i>Activo</div>';
            } else {
                $sub_array[] = '<div class="text-secondary"><i class="fas fa-circle f-10 m-r-10"></i>Inactivo</div>';
            }  
            
            // Columna 4: Acciones
            $sub_array[] = '<ul class="list-inline me-auto mb-0">
                                <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Editar">
                                    <a class="avtar avtar-xs btn-link-success btn-pc-default" style="cursor: pointer"
                                    onclick="editarUsuario(' . $row["id"] . ')">
                                        <i class="ti ti-edit-circle f-18"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Eliminar">
                                    <a class="avtar avtar-xs btn-link-danger btn-pc-default" style="cursor: pointer"
                                    onclick="inactivarUsuario(' . $row["id"] . ')">
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

    case "ingresar_editar_cliente":
        try {
            // Capturar datos del formulario
            $cliente_id         = !empty($_POST["cliente_id"]) ? $_POST["cliente_id"] : null;
            $tipo_ruc           = $_POST['tipo_ruc'];
            $ruc                = $_POST['ruc'];
            $razon_social       = !empty($_POST['razon_social'])     ? $_POST['razon_social']       : null;
            $nombre_cliente     = !empty($_POST['nombre_cliente'])   ? $_POST['nombre_cliente']     : null;
            $apellido_paterno   = !empty($_POST['apellido_paterno']) ? $_POST['apellido_paterno']   : null;
            $apellido_materno   = !empty($_POST['apellido_materno']) ? $_POST['apellido_materno']   : null;

            $departamento       = !empty($_POST['departamento']) ? $_POST['departamento'] : null;
            $provincia          = !empty($_POST['provincia'])    ? $_POST['provincia']    : null;
            $distrito           = !empty($_POST['distrito'])     ? $_POST['distrito']     : null;
            $direccion          = !empty($_POST['direccion'])    ? $_POST['direccion']    : null;
            $referencia         = !empty($_POST['referencia'])   ? $_POST['referencia']   : null;

            $contacto_principal = $_POST['contacto_principal'];
            $cargo_contacto     = $_POST['cargo_contacto'];
            $email_contacto     = $_POST['email_contacto'];
            $telefono_contacto  = $_POST['telefono_contacto'];

            $contacto_1         = !empty($_POST['contacto_1'])          ? $_POST['contacto_1']          : null;
            $telefono_contacto_1= !empty($_POST['telefono_contacto_1']) ? $_POST['telefono_contacto_1'] : null;
            $contacto_2         = !empty($_POST['contacto_2'])          ? $_POST['contacto_2']          : null;
            $telefono_contacto_2= !empty($_POST['telefono_contacto_2']) ? $_POST['telefono_contacto_2'] : null;
            
            $estado_cliente     = isset($_POST['estado_cliente']) ? $_POST['estado_cliente'] : '1';

            /* // Validar si el usuario ya existe
            $usuario_existe = $usuario->verificarUsuarioExiste($usuario_nombre, $usuario_id);
            if ($usuario_existe) {
                $jsonData['success'] = 0;
                $jsonData['message'] = 'El nombre de usuario ya está registrado';
                header('Content-type: application/json; charset=utf-8');
                echo json_encode($jsonData);
                exit;
            } */

            // Insertar o actualizar usuario
            if ($cliente_id) {
                /* // Actualizar usuario existente
                $resultado = $cliente->actualizarCliente(
                    $cliente_id,
                    $tipo_ruc,
                    $ruc,
                    $razon_social,
                    $nombre_cliente,
                    $apellido_paterno,
                    $apellido_materno,
                    $contacto_principal,
                    $cargo_contacto,
                    $email_contacto,
                    $telefono_contacto,
                    $contacto_1,
                    $telefono_contacto_1,
                    $contacto_2,
                    $telefono_contacto_2,
                    $estado_cliente
                );
                
                if ($resultado) {
                    $jsonData['success'] = 1;
                    $jsonData['message'] = 'Usuario actualizado correctamente';
                } else {
                    $jsonData['success'] = 0;
                    $jsonData['message'] = 'Error al actualizar el usuario';
                } */
            } else {
                // Insertar nuevo usuario
                $cliente->insertarCliente(
                    $tipo_ruc,
                    $ruc,
                    $razon_social,
                    $nombre_cliente,
                    $apellido_paterno,
                    $apellido_materno,
                    $departamento,
                    $provincia,
                    $distrito,
                    $direccion,
                    $referencia,
                    $contacto_principal,
                    $cargo_contacto,
                    $email_contacto,
                    $telefono_contacto,
                    $contacto_1,
                    $telefono_contacto_1,
                    $contacto_2,
                    $telefono_contacto_2,
                    $estado_cliente
                );
                // La respuesta JSON se maneja dentro del método insertarUsuario
                exit;
            }
            
        } catch (Exception $e) {
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
        }
        
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsonData);
        break;
}