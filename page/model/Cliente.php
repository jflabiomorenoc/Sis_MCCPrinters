<?php
date_default_timezone_set('America/Bogota');
class Cliente extends Conectar{

    public function listar() {
        $conectar=parent::conexion();
        parent::set_names();

        $sql = "SELECT 
        id,
        tipo_ruc,
        ruc,
        razon_social,
        nombre_cliente,
        apellido_paterno,
        apellido_materno,
        contacto_principal,
        cargo_contacto,
        email_contacto,
        telefono_contacto,
        estado
        FROM mccp_cliente
        ORDER BY nombre_cliente ASC";

        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function insertarCliente($tipo_ruc, $ruc, $razon_social, $nombre_cliente, $apellido_paterno, $apellido_materno, $departamento, $provincia, $distrito, $direccion, $referencia, $contacto_principal, $cargo_contacto, $email_contacto, $telefono_contacto, $contacto_1, $telefono_contacto_1, $contacto_2, $telefono_contacto_2,  $estado_cliente) {
        $conectar=parent::conexion();
        parent::set_names();

        try {
            $sql = "INSERT INTO mccp_cliente (tipo_ruc, ruc, razon_social, nombre_cliente, apellido_paterno, apellido_materno, contacto_principal, cargo_contacto, email_contacto, telefono_contacto, contacto_1, telefono_contacto_1, contacto_2, telefono_contacto_2, estado, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $tipo_ruc);
            $sql->bindValue(2, $ruc);
            $sql->bindValue(3, $razon_social);
            $sql->bindValue(4, $nombre_cliente);
            $sql->bindValue(5, $apellido_paterno);
            $sql->bindValue(6, $apellido_materno);
            $sql->bindValue(7, $contacto_principal);
            $sql->bindValue(8, $cargo_contacto);
            $sql->bindValue(9, $email_contacto);
            $sql->bindValue(10, $telefono_contacto);
            $sql->bindValue(11, $contacto_1);
            $sql->bindValue(12, $telefono_contacto_1);
            $sql->bindValue(13, $contacto_2);
            $sql->bindValue(14, $telefono_contacto_2);
            $sql->bindValue(15, $estado_cliente);

            if($sql->execute()){
                $cliente_id = $conectar->lastInsertId();

                // Si se ingresa una direccion, insertarlo en mccp_direccion_cliente
                if (!empty($direccion)) {
                    $sql_perfil = "INSERT INTO mccp_direccion_cliente (cliente_id, direccion, distrito, provincia, departamento, es_principal, referencia, created_at, updated_at) 
                                VALUES (?, ?, ?, ?, ?, 1, ?, NOW(), NOW())";
                    $stmt_perfil = $conectar->prepare($sql_perfil);
                    $stmt_perfil->bindValue(1, $cliente_id);
                    $stmt_perfil->bindValue(2, $direccion);
                    $stmt_perfil->bindValue(3, $distrito);
                    $stmt_perfil->bindValue(4, $provincia);
                    $stmt_perfil->bindValue(5, $departamento);
                    $stmt_perfil->bindValue(6, $referencia);
                    $stmt_perfil->execute();
                }

                $jsonData['success'] = 1;
                $jsonData['message'] = 'Cliente registrado correctamente';
            } else {
                $jsonData['success'] = 0;
                $jsonData['message'] = 'Error al registrar el cliente';
            }
            
        } catch (Exception $e) {
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
        }
        
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsonData);   
    }
}

?>