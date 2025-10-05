<?php
date_default_timezone_set('America/Bogota');
class Usuario extends Conectar{
    
    public function login($usuario,$password){
        $conectar=parent::conexion();
        parent::set_names();
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
            foto_perfil,
            numero_contacto,
            email,
            estado
        FROM mccp_usuario
        WHERE (usuario = ? OR email = ?)";
        $query_login=$conectar->prepare($query_login);
        $query_login->bindValue(1,$usuario);
        $query_login->bindValue(2,$usuario);
        $query_login->execute();
        $result_login = $query_login->fetch();

        if (is_array($result_login) and count($result_login)>0){

            $_SESSION["id"]                 = $result_login["id"];         
            $_SESSION["nombres"]            = $result_login["nombres"];        
            $_SESSION["apellidos"]          = $result_login["apellidos"];     
            $_SESSION["usuario"]            = $result_login["usuario"];        
            $_SESSION["rol_usuario"]        = $result_login["rol_usuario"];    
            $_SESSION["desc_rol"]           = $result_login["desc_rol"];    
            $_SESSION["foto_perfil"]        = $result_login["foto_perfil"];   
            $_SESSION["numero_contacto"]    = $result_login["numero_contacto"];
            $_SESSION["email"]              = $result_login["email"];         
            $_SESSION["estado"]             = $result_login["estado"];        

            $pass_valid = $result_login["password"];

            if ($_SESSION["estado"] == 2) {
                $jsonData['success'] = 2;
                $jsonData['mensaje'] = 'Usuario inactivo';
                session_destroy();
            } else {
                if (password_verify($password, $pass_valid)) {

                    $update = "UPDATE mccp_usuario SET ultimo_acceso=now() WHERE id = ?";
                    $update = $conectar->prepare($update);
                    $update->bindValue(1, $result_login["id"]);
                    $result1 = $update->execute(); 

                    $jsonData['success'] = 1;
                    $jsonData['mensaje'] = 'Acceso correcto';
                } else {
                    $jsonData['success'] = 0;
                    $jsonData['mensaje'] = 'Contraseña incorrecta';
                    session_destroy();
                }
            }
        } else{
            $jsonData['success'] = 0;
            $jsonData['mensaje'] = 'Usuario no registrado';
        }
         header('Content-type: application/json; charset=utf-8');
         echo json_encode($jsonData);  
    }

    public function listar() {
        $conectar=parent::conexion();
        parent::set_names();

        $sql = "SELECT 
            u.id, u.nombres, u.apellidos,
            u.usuario, u.rol_usuario,
            CASE WHEN u.rol_usuario = 1
                THEN 'Adminsitrador'
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

    public function listar_usuario_cliente(){
        $conectar= parent::conexion();
        parent::set_names();
        $sql="SELECT 
            c.id,
            CASE 
                WHEN c.razon_social IS NOT NULL THEN c.razon_social
                ELSE CONCAT(c.nombre_cliente, ' ', c.apellido_paterno, ' ', c.apellido_materno)
            END AS nombre_cliente
        FROM mccp_cliente c
        LEFT JOIN mccp_usuario u ON c.id = u.cliente_id
        WHERE c.estado = 1 
        AND u.cliente_id IS NULL";
        $sql=$conectar->prepare($sql);
        $sql->execute();
        return $resultado=$sql->fetchAll();
    }

    // Método para obtener usuario por ID (necesario para obtener foto anterior)
    public function obtener_usuario_por_id($usuario_id) {
        $conectar= parent::conexion();
        parent::set_names();
        $sql="SELECT 
            mccp_usuario.id,
            mccp_usuario.nombres,
            mccp_usuario.apellidos,
            mccp_usuario.usuario,
            mccp_usuario.rol_usuario,
            CASE WHEN mccp_usuario.rol_usuario = '1'
                THEN 'Administrador'
                ELSE 'Normal'
            END nom_rol,
            mccp_usuario.cliente_id,
            CASE 
                WHEN c.razon_social IS NOT NULL THEN c.razon_social
                ELSE CONCAT(c.nombre_cliente, ' ', c.apellido_paterno, ' ', c.apellido_materno)
            END AS nombre_cliente,
            mccp_usuario.email,
            mccp_usuario.numero_contacto,
            mccp_usuario.estado,
            mccp_usuario.foto_perfil
            FROM 
            mccp_usuario
            LEFT JOIN mccp_cliente c ON c.id = mccp_usuario.cliente_id
            WHERE mccp_usuario.id = ?";
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

            // Actualizar perfil si es necesario
            if ($resultado && !empty($perfil_usuario)) {
                // Primero eliminar el perfil anterior
                $sql_delete = "DELETE FROM mccp_usuario_perfil WHERE usuario_id = ?";
                $stmt_delete = $conectar->prepare($sql_delete);
                $stmt_delete->bindValue(1, $usuario_id);
                $stmt_delete->execute();

                // Insertar el nuevo perfil
                $sql_perfil = "INSERT INTO mccp_usuario_perfil (usuario_id, perfil_id, created_at) 
                            VALUES (?, ?, NOW())";
                $stmt_perfil = $conectar->prepare($sql_perfil);
                $stmt_perfil->bindValue(1, $usuario_id);
                $stmt_perfil->bindValue(2, $perfil_usuario);
                $stmt_perfil->execute();
            }

            return $resultado;
            
        } catch (Exception $e) {
            return false;
        }
    }
}