<?php
date_default_timezone_set('America/Bogota');
class Usuario extends Conectar{
    
    public function login($usuario, $password) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $query_login = "SELECT
                id,
                nombres,
                apellidos,
                usuario,
                password,
                rol_usuario,
                CASE WHEN rol_usuario = 1
                    THEN 'Administrador'
                    ELSE 'Usuario'
                END desc_rol,
                cliente_id,
                foto_perfil,
                numero_contacto,
                email,
                estado
            FROM mccp_usuario
            WHERE usuario = ? AND estado = 1";  // ✅ AGREGADO: Validar estado activo
            
            $query_login = $conectar->prepare($query_login);
            $query_login->bindValue(1, $usuario);
            $query_login->execute();
            $result_login = $query_login->fetch(PDO::FETCH_ASSOC);

            // Usuario no encontrado
            if (!$result_login) {
                $jsonData = [
                    'success' => 0,
                    'mensaje' => 'Usuario no registrado o inactivo'
                ];
                header('Content-type: application/json; charset=utf-8');
                echo json_encode($jsonData);
                return;
            }

            // Verificar estado del usuario
            if ($result_login["estado"] == 2 || $result_login["estado"] == 0) {
                $jsonData = [
                    'success' => 0,
                    'mensaje' => 'Usuario inactivo. Contacte al administrador'
                ];
                header('Content-type: application/json; charset=utf-8');
                echo json_encode($jsonData);
                return;
            }

            // Verificar contraseña
            if (!password_verify($password, $result_login["password"])) {
                $jsonData = [
                    'success' => 0,
                    'mensaje' => 'Contraseña incorrecta'
                ];
                header('Content-type: application/json; charset=utf-8');
                echo json_encode($jsonData);
                return;
            }

            // ✅ Login exitoso - Crear sesión
            $_SESSION["id"]                 = $result_login["id"];
            $_SESSION["nombres"]            = $result_login["nombres"];
            $_SESSION["apellidos"]          = $result_login["apellidos"];
            $_SESSION["usuario"]            = $result_login["usuario"];
            $_SESSION["rol_usuario"]        = $result_login["rol_usuario"];
            $_SESSION["desc_rol"]           = $result_login["desc_rol"];
            $_SESSION["cliente_id"]         = $result_login["cliente_id"];
            $_SESSION["foto_perfil"]        = $result_login["foto_perfil"];
            $_SESSION["numero_contacto"]    = $result_login["numero_contacto"];
            $_SESSION["email"]              = $result_login["email"];
            $_SESSION["estado"]             = $result_login["estado"];

            // Actualizar último acceso
            $update = "UPDATE mccp_usuario SET ultimo_acceso = NOW() WHERE id = ?";
            $stmt_update = $conectar->prepare($update);
            $stmt_update->bindValue(1, $result_login["id"]);
            $stmt_update->execute();

            // Respuesta exitosa
            $jsonData = [
                'success' => 1,
                'mensaje' => 'Acceso correcto',
                'usuario' => $result_login["nombres"] . ' ' . $result_login["apellidos"]
            ];
            
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($jsonData);
            
        } catch (Exception $e) {
            $jsonData = [
                'success' => 0,
                'mensaje' => 'Error al procesar la solicitud'
            ];
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($jsonData);
        }
    }

    public function obtener_perfil_usuario($usuario_id) {
        $conectar = parent::conexion();
        parent::set_names();
        
        $sql = "SELECT perfil_id 
                FROM mccp_usuario_perfil 
                WHERE usuario_id = ?";
        
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $usuario_id);
        $stmt->execute();
        
        $perfiles = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        return [
            'es_cliente' => in_array(1, $perfiles),
            'es_tecnico' => in_array(2, $perfiles),
            'perfiles' => $perfiles
        ];
    }

    public function listar() {
        $conectar=parent::conexion();
        parent::set_names();

        $sql = "SELECT 
            u.id, u.nombres, u.apellidos,
            u.usuario, u.rol_usuario,
            CASE WHEN u.rol_usuario = 1
                THEN 'ADMINISTRADOR'
                END
            nombre_rol, 
            u.foto_perfil,
            u.numero_contacto, u.email,
            u.estado, u.ultimo_acceso,
            COUNT(up.usuario_id) as total_perfiles
        FROM mccp_usuario u
        LEFT JOIN mccp_usuario_perfil up ON u.id = up.usuario_id
        GROUP BY 1,2,3,4,5,6,7,8,9,10,11
        ORDER BY u.nombres ASC";

        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Método para obtener el perfil de un usuario (cuando tiene solo 1)
    public function obtenerPerfilUsuario($usuario_id) {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT 
                    up.perfil_id,
                    p.nombre as nombre_perfil
                FROM mccp_usuario_perfil up
                INNER JOIN mccp_perfil p ON up.perfil_id = p.id
                WHERE up.usuario_id = ?
                LIMIT 1";
        
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usuario_id);
        $sql->execute();
        
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function obtener_perfiles($usuario_id) {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT
                up.id as up_id,
                p.id,
                p.nombre
            FROM mccp_perfil p
            INNER JOIN mccp_usuario_perfil up ON p.id = up.perfil_id
            WHERE up.usuario_id = ? 
            AND p.estado = '1'
            ORDER BY p.nombre ASC";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $usuario_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function listar_usuario_cliente($cliente_id_actual = null) {
        $conectar = parent::conexion();
        parent::set_names();
        
        // Query base
        $sql = "SELECT 
                    c.id,
                    CASE 
                        WHEN c.razon_social IS NOT NULL THEN c.razon_social
                        ELSE CONCAT(c.nombre_cliente, ' ', c.apellido_paterno, ' ', c.apellido_materno)
                    END AS nombre_cliente
                FROM mccp_cliente c
                WHERE c.estado = 1 
                AND c.id NOT IN (
                    SELECT cliente_id 
                    FROM mccp_usuario 
                    WHERE cliente_id IS NOT NULL";
        
        // Si hay cliente actual, excluirlo de la lista de "ya asignados"
        if ($cliente_id_actual) {
            $sql .= " AND cliente_id != ?";
        }
        
        $sql .= ") ORDER BY nombre_cliente";
        
        $stmt = $conectar->prepare($sql);
        
        // Bind del parámetro si existe
        if ($cliente_id_actual) {
            $stmt->bindValue(1, $cliente_id_actual);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para obtener usuario por ID (necesario para obtener foto anterior)
    public function obtener_usuario_por_id($usuario_id) {
        $conectar= parent::conexion();
        parent::set_names();
        $sql="SELECT 
                u.id,
                u.nombres,
                u.apellidos,
                u.usuario,
                u.rol_usuario,
                CASE 
                    WHEN u.rol_usuario = '1' THEN 'Administrador'
                    ELSE 'Normal'
                END AS nom_rol,
                u.cliente_id,
                CASE 
                    WHEN c.razon_social IS NOT NULL THEN c.razon_social
                    ELSE CONCAT(c.nombre_cliente, ' ', c.apellido_paterno, ' ', c.apellido_materno)
                END AS nombre_cliente,
                u.email,
                u.numero_contacto,
                u.estado,
                u.foto_perfil,
                -- Obtener el perfil mínimo
                CASE 
                    WHEN u.rol_usuario = 2 THEN up.min_perfil_id
                    ELSE NULL
                END AS perfil_id
            FROM mccp_usuario u
            LEFT JOIN mccp_cliente c ON c.id = u.cliente_id
            LEFT JOIN (
                SELECT usuario_id, MIN(perfil_id) as min_perfil_id
                FROM mccp_usuario_perfil
                GROUP BY usuario_id
            ) up ON up.usuario_id = u.id
            WHERE u.id = ?";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1, $usuario_id);
        $sql->execute();
        return $resultado=$sql->fetchAll();
    }
    public function verificarUsuarioExiste($usuario, $usuario_id = null) {
        $conectar = parent::conexion();
        parent::set_names();

        if ($usuario_id) {
            // Si es edición, excluir el ID actual de la búsqueda
            $sql = "SELECT COUNT(*) as total FROM mccp_usuario WHERE usuario = ? AND id != ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $usuario);
            $sql->bindValue(2, $usuario_id);
        } else {
            // Si es nuevo registro
            $sql = "SELECT COUNT(*) as total FROM mccp_usuario WHERE usuario = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $usuario);
        }
        
        $sql->execute();
        $resultado = $sql->fetch(PDO::FETCH_ASSOC);
        
        return $resultado['total'] > 0;
    }

    // Método para obtener datos de un usuario por ID
    public function obtenerUsuarioPorId($usuario_id) {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT * FROM mccp_usuario WHERE id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usuario_id);
        $sql->execute();
        
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    // Método corregido para insertar usuario
    public function insertarUsuario($nombres, $apellidos, $usuario, $password, $rol_usuario, $perfil_usuario, $cliente_id, $numero_contacto, $email, $estado, $foto_perfil) {
        $conectar = parent::conexion();
        parent::set_names();

        try {
            $sql = "INSERT INTO mccp_usuario (nombres, apellidos, usuario, password, rol_usuario, cliente_id, numero_contacto, email, estado, foto_perfil, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $nombres);
            $sql->bindValue(2, $apellidos);
            $sql->bindValue(3, $usuario);
            $sql->bindValue(4, $password);
            $sql->bindValue(5, $rol_usuario);
            $sql->bindValue(6, $cliente_id);
            $sql->bindValue(7, $numero_contacto);
            $sql->bindValue(8, $email);
            $sql->bindValue(9, $estado);
            $sql->bindValue(10, $foto_perfil);

            if($sql->execute()){
                $usuario_id = $conectar->lastInsertId();

                // Si se seleccionó un perfil, insertarlo en mccp_usuario_perfil
                if (!empty($perfil_usuario)) {
                    $sql_perfil = "INSERT INTO mccp_usuario_perfil (usuario_id, perfil_id, created_at) 
                                VALUES (?, ?, NOW())";
                    $stmt_perfil = $conectar->prepare($sql_perfil);
                    $stmt_perfil->bindValue(1, $usuario_id);
                    $stmt_perfil->bindValue(2, $perfil_usuario);
                    $stmt_perfil->execute();
                }

                $jsonData['success'] = 1;
                $jsonData['message'] = 'Usuario registrado correctamente';
            } else {
                $jsonData['success'] = 0;
                $jsonData['message'] = 'Error al registrar el usuario';
            }
            
        } catch (Exception $e) {
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
        }
        
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsonData);   
    }

    // Método para actualizar usuario
    public function actualizarUsuario($usuario_id, $nombres, $apellidos, $usuario, $password, $rol_usuario, $perfil_usuario, $cliente_id, $numero_contacto, $email, $estado, $foto_perfil) {
        $conectar = parent::conexion();
        parent::set_names();

        try {
            // NUEVO: Obtener el rol actual ANTES de actualizar
            $sql_rol_actual = "SELECT rol_usuario FROM mccp_usuario WHERE id = ?";
            $stmt_rol = $conectar->prepare($sql_rol_actual);
            $stmt_rol->bindValue(1, $usuario_id);
            $stmt_rol->execute();
            $rol_actual = $stmt_rol->fetchColumn();

            // NUEVO: Si cambia de rol 2 a rol 1, eliminar perfiles ANTES del UPDATE
            if ($rol_actual == 2 && $rol_usuario == 1) {
                $sql_delete = "DELETE FROM mccp_usuario_perfil WHERE usuario_id = ?";
                $stmt_delete = $conectar->prepare($sql_delete);
                $stmt_delete->bindValue(1, $usuario_id);
                $stmt_delete->execute();
                
                // Forzar cliente_id a NULL cuando cambia a rol 1
                $cliente_id = null;
            }

            // Si se proporcionó nueva contraseña, actualizar también la contraseña
            if (!empty($password)) {
                $sql = "UPDATE mccp_usuario SET 
                        nombres = ?, 
                        apellidos = ?, 
                        usuario = ?, 
                        password = ?, 
                        rol_usuario = ?, 
                        cliente_id = ?, 
                        numero_contacto = ?, 
                        email = ?, 
                        estado = ?, 
                        foto_perfil = ?, 
                        updated_at = NOW() 
                        WHERE id = ?";
                
                $sql = $conectar->prepare($sql);
                $sql->bindValue(1, $nombres);
                $sql->bindValue(2, $apellidos);
                $sql->bindValue(3, $usuario);
                $sql->bindValue(4, $password);
                $sql->bindValue(5, $rol_usuario);
                $sql->bindValue(6, $cliente_id);
                $sql->bindValue(7, $numero_contacto);
                $sql->bindValue(8, $email);
                $sql->bindValue(9, $estado);
                $sql->bindValue(10, $foto_perfil);
                $sql->bindValue(11, $usuario_id);
            } else {
                // Sin actualizar la contraseña
                $sql = "UPDATE mccp_usuario SET 
                        nombres = ?, 
                        apellidos = ?, 
                        usuario = ?, 
                        rol_usuario = ?, 
                        cliente_id = ?, 
                        numero_contacto = ?, 
                        email = ?, 
                        estado = ?, 
                        foto_perfil = ?, 
                        updated_at = NOW() 
                        WHERE id = ?";
                
                $sql = $conectar->prepare($sql);
                $sql->bindValue(1, $nombres);
                $sql->bindValue(2, $apellidos);
                $sql->bindValue(3, $usuario);
                $sql->bindValue(4, $rol_usuario);
                $sql->bindValue(5, $cliente_id);
                $sql->bindValue(6, $numero_contacto);
                $sql->bindValue(7, $email);
                $sql->bindValue(8, $estado);
                $sql->bindValue(9, $foto_perfil);
                $sql->bindValue(10, $usuario_id);
            }

            $resultado = $sql->execute();

            // Actualizar perfiles usando el procedimiento almacenado SOLO si rol = 2
            if ($resultado && !empty($perfil_usuario) && $rol_usuario == 2) {
                // Llamar al procedimiento almacenado
                $sql_proc = "CALL mccp_asignar_perfil_usuario(?, ?)";
                $stmt_proc = $conectar->prepare($sql_proc);
                $stmt_proc->bindValue(1, $usuario_id, PDO::PARAM_INT);
                $stmt_proc->bindValue(2, $perfil_usuario, PDO::PARAM_INT);
                $stmt_proc->execute();
            }

            return $resultado;
            
        } catch (Exception $e) {
            // NUEVO: Log del error para debugging
            error_log("Error en actualizarUsuario: " . $e->getMessage());
            return false;
        }
    }

    public function editar_estado_usuario($usuario_id, $estado) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $conectar->beginTransaction();
            
            $sql_upd = "UPDATE mccp_usuario SET estado = ? WHERE id = ?";
            $sql_upd = $conectar->prepare($sql_upd);
            $sql_upd->bindValue(1, $estado);
            $sql_upd->bindValue(2, $usuario_id);
            $sql_upd->execute();
            
            $conectar->commit();
            return true;
            
        } catch (Exception $e) {
            $conectar->rollBack();
            error_log("Error al actualizar estado: " . $e->getMessage());
            return false;
        }
    }

    public function combo_tecnico($tecnico_id_actual = null) {
        $conectar = parent::conexion();
        parent::set_names();
        
        $sql = "SELECT DISTINCT
            u.id,
            CONCAT(
                u.nombres, 
                ' ', 
                u.apellidos
            ) AS nombre_completo
        FROM mccp_usuario u
        INNER JOIN mccp_usuario_perfil up ON u.id = up.usuario_id
        WHERE up.perfil_id = 2
        AND u.estado = 1
        ORDER BY nombre_completo ASC";
        
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verificar si la contraseña actual es correcta
    public function verificar_password_actual($usuario_id, $password_actual) {
        $conectar = parent::conexion();
        parent::set_names();
        
        $sql = "SELECT password 
                FROM mccp_usuario 
                WHERE id = ? AND estado = 1";
        
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $usuario_id);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado) {
            // Verificar el hash de la contraseña
            return password_verify($password_actual, $resultado['password']);
        }
        
        return false;
    }

    // Actualizar contraseña del usuario
    public function cambiar_password($usuario_id, $password_nueva) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            // Encriptar la nueva contraseña
            $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
            
            $sql = "UPDATE mccp_usuario 
                    SET password = ?,
                        updated_at = NOW()
                    WHERE id = ?";
            
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $password_hash);
            $stmt->bindValue(2, $usuario_id);
            
            if ($stmt->execute()) {
                return [
                    'success' => 1,
                    'message' => 'Contraseña actualizada correctamente'
                ];
            } else {
                return [
                    'success' => 0,
                    'message' => 'Error al actualizar la contraseña'
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => 0,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    // Función adicional: Validar fortaleza de contraseña (opcional)
    public function validar_fortaleza_password($password) {
        $errores = [];
        
        if (strlen($password) < 8) {
            $errores[] = 'La contraseña debe tener mínimo 8 caracteres';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errores[] = 'Debe contener al menos una letra minúscula';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errores[] = 'Debe contener al menos una letra mayúscula';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errores[] = 'Debe contener al menos un número';
        }
        
        return [
            'valida' => empty($errores),
            'errores' => $errores
        ];
    }
}