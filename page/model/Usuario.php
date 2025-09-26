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
}