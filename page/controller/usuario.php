<?php
require_once("../config/conexion.php");
require_once("../model/Usuario.php");
$usuario = new Usuario();

switch($_GET["op"]){
    //Envío de datos para login
    case "login":
        $usuario->login(htmlspecialchars($_POST['inputuser'],ENT_QUOTES,'UTF-8'),htmlspecialchars($_POST['inputpassword'],ENT_QUOTES,'UTF-8'));
        break;
    
    case "listar":
        $datos = $usuario->listar();
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
            
            $sub_array[] = $row["numero_contacto"];
            $sub_array[] = $row["usuario"];

            if ($row['ultimo_acceso']) {
                $sub_array[] = ''. date("d/m/Y", strtotime($row['ultimo_acceso'])).'
                            <span class="text-muted text-sm d-block">'. date("h:i A", strtotime($row['ultimo_acceso'])).'</span>';
            } else {
                $sub_array[] = '-';
            }
            
        
            // Columna Estado
            if ($row["estado"] == 1) {
                $sub_array[] = '<div class="text-success"><i class="fas fa-circle f-10 m-r-10"></i>Activo</div>';
            } else {
                $sub_array[] = '<div class="text-secondary"><i class="fas fa-circle f-10 m-r-10"></i>Inactivo</div>';
            }

            if ($row["total_perfiles"] == 0 && $row["rol_usuario"] == "1") {
                $sub_array[] = '<span class="badge bg-light-warning f-12">' . $row["nombre_rol"] . '</span>';
            } else if ($row["total_perfiles"] == 0 && $row["rol_usuario"] == "2") {
                $sub_array[] = '<span class="badge bg-light-danger f-12"> Asignar perfil </span>';
            } else if ($row["total_perfiles"] == 1 && $row["rol_usuario"] == "2") {
                // Obtener el nombre del perfil cuando es solo 1
                $perfil_info = $usuario->obtenerPerfilUsuario($row["id"]);
                
                if ($perfil_info && !empty($perfil_info['nombre_perfil'])) {
                    $sub_array[] = '<span class="badge bg-light-secondary f-12">' . 
                                htmlspecialchars($perfil_info['nombre_perfil']) . 
                                '</span>';
                } else {
                    $sub_array[] = '<span class="badge bg-light-info f-12">1 perfil</span>';
                }
            } else {
                $sub_array[] = '<span class="badge bg-light-info f-12">' . $row["total_perfiles"] . ' perfiles </span>';
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

    case "listar_usuario_cliente":
        $datos = $usuario->listar_usuario_cliente();
        if(is_array($datos)==true and count($datos)>0){
            $html= "<option value=''>-- Seleccionar --</option>";
            foreach($datos as $row){
                $html.= "<option value='".$row['id']."'>".$row['nombre_cliente']."</option>";
            }
            echo $html;
        }
        break;

    case "obtener":
        $datos = $usuario->obtener_usuario_por_id($_POST["id"]);
        if (is_array($datos) == true and count($datos) > 0) {
           foreach ($datos as $row) {
                $output["id"]               = $row["id"];
                $output["nombres"]          = $row["nombres"];
                $output["apellidos"]        = $row["apellidos"];
                $output["usuario"]          = $row["usuario"];
                $output["nom_rol"]          = $row["nom_rol"];
                $output["rol_usuario"]      = $row["rol_usuario"];
                $output["cliente_id"]       = $row["cliente_id"];
                $output["nombre_cliente"]   = $row["nombre_cliente"];
                $output["email"]            = $row["email"];
                $output["numero_contacto"]  = $row["numero_contacto"];
                $output["estado"]           = $row["estado"];
                $output["foto_perfil"]      = $row["foto_perfil"];
            }
            echo json_encode($output);
        }
        break;

    case "ingresar_editar_usuario":
        try {
            // Capturar datos del formulario
            $usuario_id = !empty($_POST["usuario_id"]) ? $_POST["usuario_id"] : null;
            $nombres = $_POST['nombres'];
            $apellidos = $_POST['apellidos'];
            $usuario_nombre = $_POST['usuario'];
            $password = !empty($_POST['password']) ? $_POST['password'] : null;
            $rol_usuario = $_POST['rol_usuario'];
            $perfil_usuario = !empty($_POST['perfil_usuario']) ? $_POST['perfil_usuario'] : null;
            $cliente_id = !empty($_POST['cliente_id']) ? $_POST['cliente_id'] : null;
            $numero_contacto = $_POST['numero_contacto'];
            $email = $_POST['email'];
            $estado_usuario = isset($_POST['estado_usuario']) ? $_POST['estado_usuario'] : '1';

            // Validar si el usuario ya existe
            $usuario_existe = $usuario->verificarUsuarioExiste($usuario_nombre, $usuario_id);
            if ($usuario_existe) {
                $jsonData['success'] = 0;
                $jsonData['message'] = 'El nombre de usuario ya está registrado';
                header('Content-type: application/json; charset=utf-8');
                echo json_encode($jsonData);
                exit;
            }

            // Manejo de la imagen
            $foto_perfil = "default.png";
            $foto_anterior = null;

            // Si es edición, obtener la foto anterior
            if ($usuario_id) {
                $datos_usuario = $usuario->obtenerUsuarioPorId($usuario_id);
                if ($datos_usuario) {
                    $foto_anterior = $datos_usuario['foto_perfil'];
                }
            }

            // Verificar si se subió una nueva foto (Dropzone envía como foto_perfil)
            if (isset($_FILES["foto_perfil"]) && $_FILES["foto_perfil"]["error"] === UPLOAD_ERR_OK) {
                $archivo_tmp = $_FILES["foto_perfil"]["tmp_name"];
                $nombre_original = $_FILES["foto_perfil"]["name"];
                $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
                $extensiones_permitidas = array("jpg", "jpeg", "png", "gif", "bmp", "webp", "avif");
                
                if (in_array($extension, $extensiones_permitidas)) {
                    // Generar nombre único
                    $nombre_archivo = uniqid() . "_" . time() . "." . $extension;
                    $ruta_destino = "../../assets/images/user/" . $nombre_archivo;
                    
                    // Crear directorio si no existe
                    if (!file_exists("../../assets/images/user/")) {
                        mkdir("../../assets/images/user/", 0777, true);
                    }
                    
                    // Mover archivo
                    if (move_uploaded_file($archivo_tmp, $ruta_destino)) {
                        $foto_perfil = $nombre_archivo;
                        
                        // Eliminar foto anterior si existe y no es default.png
                        if ($foto_anterior && $foto_anterior !== "default.png") {
                            $ruta_anterior = "../../assets/images/user/" . $foto_anterior;
                            if (file_exists($ruta_anterior)) {
                                @unlink($ruta_anterior);
                            }
                        }
                    } else {
                        $jsonData['success'] = 0;
                        $jsonData['message'] = 'Error al subir la imagen';
                        header('Content-type: application/json; charset=utf-8');
                        echo json_encode($jsonData);
                        exit;
                    }
                } else {
                    $jsonData['success'] = 0;
                    $jsonData['message'] = 'Formato de imagen no permitido';
                    header('Content-type: application/json; charset=utf-8');
                    echo json_encode($jsonData);
                    exit;
                }
            } else {
                // Si es edición y no se subió nueva imagen, mantener la anterior
                if ($usuario_id && $foto_anterior) {
                    $foto_perfil = $foto_anterior;
                }
            }

            // Procesar contraseña solo si se proporcionó
            $password_hash = null;
            if (!empty($password)) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 10]);
            }

            // Insertar o actualizar usuario
            if ($usuario_id) {
                // Actualizar usuario existente
                $resultado = $usuario->actualizarUsuario(
                    $usuario_id, 
                    $nombres, 
                    $apellidos, 
                    $usuario_nombre, 
                    $password_hash, 
                    $rol_usuario, 
                    $perfil_usuario, 
                    $cliente_id, 
                    $numero_contacto, 
                    $email, 
                    $estado_usuario, 
                    $foto_perfil
                );
                
                if ($resultado) {
                    $jsonData['success'] = 1;
                    $jsonData['message'] = 'Usuario actualizado correctamente';
                } else {
                    $jsonData['success'] = 0;
                    $jsonData['message'] = 'Error al actualizar el usuario';
                }
            } else {
                // Insertar nuevo usuario
                $usuario->insertarUsuario(
                    $nombres, 
                    $apellidos, 
                    $usuario_nombre, 
                    $password_hash, 
                    $rol_usuario, 
                    $perfil_usuario, 
                    $cliente_id, 
                    $numero_contacto, 
                    $email, 
                    $estado_usuario, 
                    $foto_perfil
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