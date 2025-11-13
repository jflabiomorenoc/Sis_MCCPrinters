<?php
date_default_timezone_set('America/Bogota');
class Proveedor extends Conectar{

    public function listar() {
        $conectar=parent::conexion();
        parent::set_names();

        $sql = "SELECT 
            p.id,
            p.tipo_ruc,
            p.ruc,
            CASE 
                WHEN p.razon_social IS NOT NULL AND p.razon_social <> '' 
                    THEN p.razon_social
                ELSE CONCAT(p.nombre_proveedor, ' ', p.apellido_paterno, ' ', p.apellido_materno)
            END AS proveedor,
            p.direccion,
            p.telefono,
            p.email,
            p.contacto,
            p.estado
        FROM 
            mccp_proveedor p
        ORDER BY 
            proveedor ASC";

        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function verificarProveedorExiste($ruc, $proveedor_id = null) {
        $conectar = parent::conexion();
        parent::set_names();

        if ($proveedor_id) {
            // Si es edición, excluir el ID actual de la búsqueda
            $sql = "SELECT COUNT(*) as total FROM mccp_proveedor WHERE ruc = ? AND id != ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $ruc);
            $sql->bindValue(2, $proveedor_id);
        } else {
            // Si es nuevo registro
            $sql = "SELECT COUNT(*) as total FROM mccp_proveedor WHERE ruc = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $ruc);
        }
        
        $sql->execute();
        $resultado = $sql->fetch(PDO::FETCH_ASSOC);
        
        return $resultado['total'] > 0;
    }

    public function insertarProveedor($tipo_ruc, $ruc, $razon_social, $nombre_proveedor, $apellido_paterno, $apellido_materno, $contacto, $email, $telefono, $direccion, $estado_proveedor) {
        $conectar=parent::conexion();
        parent::set_names();

        try {
            $sql = "INSERT INTO mccp_proveedor (tipo_ruc, ruc, razon_social, nombre_proveedor, apellido_paterno, apellido_materno, direccion, telefono, email, contacto, estado, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $tipo_ruc);
            $sql->bindValue(2, $ruc);
            $sql->bindValue(3, $razon_social ? $razon_social : null);
            $sql->bindValue(4, $nombre_proveedor ? $nombre_proveedor : null);
            $sql->bindValue(5, $apellido_paterno ? $apellido_paterno : null);
            $sql->bindValue(6, $apellido_materno ? $apellido_materno : null);
            $sql->bindValue(7, $direccion);
            $sql->bindValue(8, $telefono);
            $sql->bindValue(9, $email);
            $sql->bindValue(10, $contacto);
            $sql->bindValue(11, $estado_proveedor);

            if($sql->execute()) {
                $jsonData['success'] = 1;
                $jsonData['message'] = 'Proveedor registrado correctamente';
            } else {
                $jsonData['success'] = 0;
                $jsonData['message'] = 'Error al registrar el proveedor';
            }
            
        } catch (Exception $e) {
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
        }
        
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsonData);   
    }

    public function actualizarProveedor($proveedor_id, $tipo_ruc, $ruc, $razon_social, $nombre_proveedor, $apellido_paterno, $apellido_materno, $contacto, $email, $telefono, $direccion, $estado_proveedor) {
        $conectar = parent::conexion();
        parent::set_names();

        try {
            $sql_upd = "UPDATE mccp_proveedor SET 
                    tipo_ruc = ?, 
                    ruc = ?, 
                    razon_social = ?,
                    nombre_proveedor = ?,
                    apellido_paterno = ?,
                    apellido_materno = ?,
                    contacto = ?, 
                    email = ?, 
                    telefono = ?, 
                    direccion = ?, 
                    estado = ?,
                    updated_at = NOW() 
                    WHERE id = ?";

            $sql_upd = $conectar->prepare($sql_upd);
            $sql_upd->bindValue(1, $tipo_ruc);
            $sql_upd->bindValue(2, $ruc);
            $sql_upd->bindValue(3, $razon_social ? $razon_social : null);
            $sql_upd->bindValue(4, $nombre_proveedor ? $nombre_proveedor : null);
            $sql_upd->bindValue(5, $apellido_paterno ? $apellido_paterno : null);
            $sql_upd->bindValue(6, $apellido_materno ? $apellido_materno : null);
            $sql_upd->bindValue(7, $contacto);
            $sql_upd->bindValue(8, $email);
            $sql_upd->bindValue(9, $telefono);
            $sql_upd->bindValue(10, $direccion);            
            $sql_upd->bindValue(11, $estado_proveedor);
            $sql_upd->bindValue(12, $proveedor_id);

            if($sql_upd->execute()){
                $jsonData['success'] = 1;
                $jsonData['message'] = 'Proveedor actualizado correctamente';
            } else {
                $jsonData['success'] = 0;
                $jsonData['message'] = 'Error al actualizar proveedor';
            }
            
        } catch (Exception $e) {
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
        }
        
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsonData);
    }

    public function obtener_proveedor_por_id($proveedor_id) {
        $conectar= parent::conexion();
        parent::set_names();
        $sql="SELECT
            id,
            CASE WHEN tipo_ruc = 1
                THEN 'JURÍDICO'
                ELSE 'NATURAL'
            END nom_tipo_ruc,
            tipo_ruc,
            ruc,
            razon_social,
            nombre_proveedor,
            apellido_paterno,
            apellido_materno,
            direccion,
            telefono,
            email,
            contacto,
            estado,
            CASE WHEN estado = 1
                THEN 'ACTIVO'
                ELSE 'INACTIVO'
            END nom_estado
        FROM mccp_proveedor
        WHERE id = ?";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1, $proveedor_id);
        $sql->execute();
        return $resultado=$sql->fetchAll();
    }

    public function editar_estado_proveedor($proveedor_id, $estado) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $conectar->beginTransaction();
            
            $sql_upd = "UPDATE mccp_proveedor SET estado = ? WHERE id = ?";
            $sql_upd = $conectar->prepare($sql_upd);
            $sql_upd->bindValue(1, $estado);
            $sql_upd->bindValue(2, $proveedor_id);
            $sql_upd->execute();
            
            $conectar->commit();
            return true;
            
        } catch (Exception $e) {
            $conectar->rollBack();
            error_log("Error al actualizar estado: " . $e->getMessage());
            return false;
        }
    }

    public function combo_proveedor($proveedor_id_actual = null) {
        $conectar = parent::conexion();
        parent::set_names();
        
        // Query base
        $sql = "SELECT 
            p.id,
            CASE 
                WHEN p.razon_social IS NOT NULL THEN p.razon_social
                ELSE CONCAT(p.nombre_proveedor, ' ', p.apellido_paterno, ' ', p.apellido_materno)
            END AS nombre_proveedor
        FROM mccp_proveedor p
        WHERE p.estado = 1 
        ORDER BY nombre_proveedor";
        $stmt = $conectar->prepare($sql);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}