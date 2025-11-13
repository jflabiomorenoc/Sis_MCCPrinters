<?php
require_once("../config/conexion.php");
require_once("../model/Usuario.php");
require_once("../model/Perfil.php");
$usuario = new Usuario();

switch($_GET["op"]){

    case "login":
        
        // Validar que existan los parámetros
        if (!isset($_POST['inputuser']) || !isset($_POST['inputpassword'])) {
            header('Content-type: application/json; charset=utf-8');
            echo json_encode([
                'success' => 0,
                'mensaje' => 'Datos incompletos'
            ]);
            exit;
        }
        
        // Limpiar datos de entrada
        $usuario_input = trim(htmlspecialchars($_POST['inputuser'], ENT_QUOTES, 'UTF-8'));
        $password = $_POST['inputpassword'];
        
        // Validar que no estén vacíos
        if (empty($usuario_input) || empty($password)) {
            header('Content-type: application/json; charset=utf-8');
            echo json_encode([
                'success' => 0,
                'mensaje' => 'Complete todos los campos'
            ]);
            exit;
        }
        
        // Llamar al método login
        $usuario->login($usuario_input, $password);
        break;
    
    case "listar":
        $datos = $usuario->listar();
        $data = Array();

        // Obtener permisos del usuario para el módulo equipo   
        $perfil = new Perfil();
        $permisos = $perfil->obtenerPermisosModulo($_SESSION['id'], 'Usuarios');
        
        foreach($datos as $row){
            $sub_array = array();
            $sub_array[] = '<div class="d-flex align-items-center">
                                  <div class="flex-shrink-0"><img
                                              src="../../assets/images/user/'. $row["foto_perfil"] . '"
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
            
            // Columna Estado con validación de permisos
            if ($permisos['puede_editar'] == 1) {
                // Usuario CON permiso - Mostrar clickeable
                if ($row["estado"] == 1) {
                    $sub_array[] = '
                        <a style="cursor: pointer" 
                        onclick="estadoUsuario(' . $row["id"] . ', 2)">
                            <div class="text-success f-12">
                                <i class="fas fa-circle f-10 m-r-10"></i>ACTIVO
                            </div>
                        </a>';
                } else {
                    $sub_array[] = '
                        <a style="cursor: pointer" 
                        onclick="estadoUsuario(' . $row["id"] . ', 1)">
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

            // Verificar permisos una sola vez al inicio del foreach
            $puedeEditar = ($permisos['puede_editar'] == 1);

            // Columna de Perfiles/Rol con validación de permisos
            if ($row["total_perfiles"] == 0 && $row["rol_usuario"] == "1") {
                // Administrador - Solo mostrar badge sin onclick
                $sub_array[] = '<span class="badge bg-light-warning f-12">' . $row["nombre_rol"] . '</span>';
                
            } else if ($row["total_perfiles"] == 0 && $row["rol_usuario"] == "2") {
                // Usuario sin perfiles - Mostrar "ASIGNAR"
                if ($puedeEditar) {
                    // Con permiso - Clickeable
                    $sub_array[] = '<a onclick="asignarPerfil(' . $row["id"] . ')" style="cursor: pointer;"><span class="badge bg-light-danger f-12"> ASIGNAR </span></a>';
                } else {
                    // Sin permiso - Solo visual
                    $sub_array[] = '<span class="badge bg-light-danger f-12" style="cursor: not-allowed; opacity: 0.6;" data-bs-toggle="tooltip" title="Sin permiso para asignar perfiles"> ASIGNAR </span>';
                }
                
            } else if ($row["total_perfiles"] == 1 && $row["rol_usuario"] == "2") {
                // Usuario con 1 perfil - Mostrar nombre del perfil
                $perfil_info = $usuario->obtenerPerfilUsuario($row["id"]);
                
                if ($perfil_info && !empty($perfil_info['nombre_perfil'])) {
                    $badgeText = htmlspecialchars($perfil_info['nombre_perfil']);
                    $badgeClass = 'bg-light-secondary';
                } else {
                    $badgeText = '1 PERFIL';
                    $badgeClass = 'bg-light-info';
                }
                
                if ($puedeEditar) {
                    // Con permiso - Clickeable
                    $sub_array[] = '<a onclick="asignarPerfil(' . $row["id"] . ')" style="cursor: pointer;"><span class="badge ' . $badgeClass . ' f-12">' . $badgeText . '</span></a>';
                } else {
                    // Sin permiso - Solo visual
                    $sub_array[] = '<span class="badge ' . $badgeClass . ' f-12" style="cursor: not-allowed; opacity: 0.6;" data-bs-toggle="tooltip" title="Sin permiso para editar perfiles">' . $badgeText . '</span>';
                }
                
            } else {
                // Usuario con múltiples perfiles - Mostrar cantidad
                if ($puedeEditar) {
                    // Con permiso - Clickeable
                    $sub_array[] = '<a onclick="asignarPerfil(' . $row["id"] . ')" style="cursor: pointer;"><span class="badge bg-light-info f-12">' . $row["total_perfiles"] . ' PERFILES </span></a>';
                } else {
                    // Sin permiso - Solo visual
                    $sub_array[] = '<span class="badge bg-light-info f-12" style="cursor: not-allowed; opacity: 0.6;" data-bs-toggle="tooltip" title="Sin permiso para editar perfiles">' . $row["total_perfiles"] . ' PERFILES </span>';
                }
            }
            
            if ($puedeEditar) {
                $sub_array[] = '<ul class="list-inline me-auto mb-0">
                                <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Editar">
                                    <a class="avtar avtar-xs btn-link-success btn-pc-default" style="cursor: pointer"
                                    onclick="editarUsuario(' . $row["id"] . ')">
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

    case "listar_usuario_cliente":
        // Recibir el cliente_id actual (si existe)
        $cliente_id_actual = !empty($_POST['cliente_id_actual']) ? $_POST['cliente_id_actual'] : null;
        
        // Obtener clientes disponibles
        $datos = $usuario->listar_usuario_cliente($cliente_id_actual);
        
        if(is_array($datos) == true and count($datos) > 0){
            $html = "<option value=''>-- Seleccionar --</option>";
            foreach($datos as $row){
                $html .= "<option value='".$row['id']."'>".$row['nombre_cliente']."</option>";
            }
            echo $html;
        } else {
            echo "<option value=''>-- No hay clientes disponibles --</option>";
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
                $output["perfil_id"]        = $row["perfil_id"];
            }
            echo json_encode($output);
        }
        break;

    //Utilizado: Asignar Perfil TABLA USUARIO
    case "obtener_perfiles":
        $datos = $usuario->obtener_perfiles($_POST['usuario_id']);
        $data = Array();
        
        foreach($datos as $row){
            $sub_array = array();

            $sub_array[] = '<span class="badge bg-light-secondary f-12">' . $row["nombre"] . '</span>';
            
            if ($permisos['puede_eliminar'] == 1) {
                $sub_array[] = '<ul class="list-inline me-auto mb-0">
                                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Eliminar">
                                        <a style="cursor: pointer;" class="avtar avtar-xs btn-link-danger btn-pc-default" 
                                        onclick="eliminarPerfil(' . $row["up_id"] . ')">
                                            <i class="ti ti-trash f-18"></i>
                                        </a>
                                    </li>
                                </ul>';  
            } else {
                $sub_array[] = '
                    <ul class="list-inline me-auto mb-0">
                        <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Sin permiso para eliminar">
                            <a class="avtar avtar-xs btn-link-warning btn-pc-default disabled" 
                            style="cursor: not-allowed; opacity: 0.5;">
                                <i class="ti ti-trash f-18"></i>
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
                    mb_strtoupper($nombres, 'UTF-8'),
                    mb_strtoupper($apellidos, 'UTF-8'),
                    mb_strtolower($usuario_nombre, 'UTF-8'), 
                    $password_hash, 
                    $rol_usuario, 
                    $perfil_usuario, 
                    $cliente_id, 
                    $numero_contacto, 
                    mb_strtolower($email, 'UTF-8'), 
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
                    mb_strtoupper($nombres, 'UTF-8'),
                    mb_strtoupper($apellidos, 'UTF-8'),
                    mb_strtolower($usuario_nombre, 'UTF-8'),
                    $password_hash, 
                    $rol_usuario, 
                    $perfil_usuario, 
                    $cliente_id, 
                    $numero_contacto, 
                    mb_strtolower($email, 'UTF-8'), 
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

    case "estado_usuario":
        try {

            $resultado = $usuario->editar_estado_usuario($_POST['usuario_id'], $_POST['estado']);
            
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

    case "combo_tecnico":
        // Recibir el tecnico_id actual (si existe)
        $tecnico_id_actual = !empty($_POST['tecnico_id_actual']) ? $_POST['tecnico_id_actual'] : null;
        
        // Obtener técnicos disponibles (perfil_id = 1)
        $datos = $usuario->combo_tecnico($tecnico_id_actual);
        
        if(is_array($datos) == true and count($datos) > 0){
            $html = "<option value=''>-- Seleccionar técnico --</option>";
            foreach($datos as $row){
                $html .= "<option value='".$row['id']."'>".$row['nombre_completo']."</option>";
            }
            echo $html;
        } else {
            echo "<option value=''>-- No hay técnicos disponibles --</option>";
        }
        break;

    case "cambiar_password":
    // Verificar sesión
        if (!isset($_SESSION['id'])) {
            echo json_encode([
                'success' => 0,
                'message' => 'Sesión no válida'
            ]);
            exit;
        }
        
        try {
            $usuario_id = $_SESSION['id'];
            $password_actual = $_POST['password_actual'];
            $password_nueva = $_POST['password_nueva'];
            
            // Validar que los campos no estén vacíos
            if (empty($password_actual) || empty($password_nueva)) {
                echo json_encode([
                    'success' => 0,
                    'message' => 'Todos los campos son obligatorios'
                ]);
                exit;
            }
            
            // Validar longitud mínima
            if (strlen($password_nueva) < 8) {
                echo json_encode([
                    'success' => 0,
                    'message' => 'La nueva contraseña debe tener mínimo 8 caracteres'
                ]);
                exit;
            }
            
            // Verificar que la contraseña actual sea correcta
            $password_valida = $usuario->verificar_password_actual($usuario_id, $password_actual);
            
            if (!$password_valida) {
                echo json_encode([
                    'success' => 0,
                    'message' => 'La contraseña actual es incorrecta'
                ]);
                exit;
            }
            
            // Verificar que la nueva contraseña sea diferente a la actual
            if ($password_actual === $password_nueva) {
                echo json_encode([
                    'success' => 0,
                    'message' => 'La nueva contraseña debe ser diferente a la actual'
                ]);
                exit;
            }
            
            // Opcional: Validar fortaleza de la contraseña
            $validacion = $usuario->validar_fortaleza_password($password_nueva);
            if (!$validacion['valida']) {
                echo json_encode([
                    'success' => 0,
                    'message' => 'Contraseña débil',
                    'errores' => $validacion['errores']
                ]);
                exit;
            }
            
            // Actualizar la contraseña
            $resultado = $usuario->cambiar_password($usuario_id, $password_nueva);
            
            // Registrar en log (opcional)
            if ($resultado['success'] == 1) {
                error_log("Usuario ID {$usuario_id} cambió su contraseña en " . date('Y-m-d H:i:s'));
            }
            
            echo json_encode($resultado);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => 0,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ]);
        }
        break;
}