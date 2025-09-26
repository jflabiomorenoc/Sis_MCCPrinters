<?php
date_default_timezone_set('America/Bogota');

class Perfil extends Conectar{
 
    public function listar(){
        $conectar = parent::conexion();
        parent::set_names();
        
        $sql = "SELECT 
                    p.id,
                    p.nombre,
                    p.estado,
                    p.created_at,
                    GROUP_CONCAT(
                        CONCAT(u.nombres, '|', u.apellidos, '|', IFNULL(u.foto_perfil, 'default.jpg'))
                        SEPARATOR ';;'
                    ) as usuarios_data,
                    COUNT(up.usuario_id) as total_usuarios
                FROM mccp_perfil p
                LEFT JOIN mccp_usuario_perfil up ON p.id = up.perfil_id
                LEFT JOIN mccp_usuario u ON up.usuario_id = u.id AND u.estado = '1'
                GROUP BY p.id, p.nombre, p.estado, p.created_at
                ORDER BY p.nombre ASC";

        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function insertar($nombre_perfil, $estado_perfil) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $sql = "INSERT INTO mccp_perfil (nombre, estado, created_at, updated_at) 
                    VALUES (?, ?, NOW(), NOW())";
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $nombre_perfil);
            $stmt->bindValue(2, $estado_perfil);
            
            if ($stmt->execute()) {
                return $conectar->lastInsertId();
            }
            return false;
            
        } catch (Exception $e) {
            error_log("Error al insertar perfil: " . $e->getMessage());
            return false;
        }
    }

    public function guardar_permisos_perfil($perfil_id, $permisos) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $conectar->beginTransaction();
            
            // Eliminar permisos existentes para este perfil
            $sql_delete = "DELETE FROM mccp_perfil_modulo WHERE perfil_id = ?";
            $stmt_delete = $conectar->prepare($sql_delete);
            $stmt_delete->bindValue(1, $perfil_id);
            $stmt_delete->execute();
            
            // Insertar nuevos permisos
            $sql_insert = "INSERT INTO mccp_perfil_modulo 
                        (perfil_id, modulo_id, puede_ver, puede_crear, puede_editar, puede_eliminar, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
            $stmt_insert = $conectar->prepare($sql_insert);
            
            // Instanciar clase Modulo para obtener los módulos
            require_once("Modulo.php"); // Asegúrate de que la ruta sea correcta
            $moduloModel = new Modulo();
            // Obtener todos los módulos activos
            $modulos = $moduloModel->listar_modulos();
            
            foreach ($modulos as $modulo) {
                $modulo_id = $modulo['id'];
                
                // Verificar permisos para este módulo (default 0 si no existe)
                $puede_ver = isset($permisos[$modulo_id]['ver']) ? 1 : 0;
                $puede_crear = isset($permisos[$modulo_id]['crear']) ? 1 : 0;
                $puede_editar = isset($permisos[$modulo_id]['editar']) ? 1 : 0;
                $puede_eliminar = isset($permisos[$modulo_id]['eliminar']) ? 1 : 0;
                
                $stmt_insert->bindValue(1, $perfil_id);
                $stmt_insert->bindValue(2, $modulo_id);
                $stmt_insert->bindValue(3, $puede_ver);
                $stmt_insert->bindValue(4, $puede_crear);
                $stmt_insert->bindValue(5, $puede_editar);
                $stmt_insert->bindValue(6, $puede_eliminar);
                $stmt_insert->execute();
            }
            
            $conectar->commit();
            return true;
            
        } catch (Exception $e) {
            $conectar->rollBack();
            error_log("Error al guardar permisos: " . $e->getMessage());
            return false;
        }
    }

    public function editar($perfil_id, $nombre_perfil, $estado_perfil) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $sql = "UPDATE mccp_perfil 
                    SET nombre = ?, estado = ?, updated_at = NOW()
                    WHERE id = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $nombre_perfil);
            $stmt->bindValue(2, $estado_perfil);
            $stmt->bindValue(3, $perfil_id);
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Error al editar perfil: " . $e->getMessage());
            return false;
        }
    }

    public function obtener_perfil_por_id($perfil_id) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $sql = "SELECT id, nombre, estado, created_at, updated_at 
                    FROM mccp_perfil 
                    WHERE id = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $perfil_id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error al obtener perfil: " . $e->getMessage());
            return false;
        }
    }

    public function verificar_nombre_perfil($nombre_perfil, $perfil_id = null) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            if ($perfil_id) {
                $sql = "SELECT COUNT(*) as count FROM mccp_perfil 
                        WHERE nombre = ? AND id != ?";
                $stmt = $conectar->prepare($sql);
                $stmt->bindValue(1, $nombre_perfil);
                $stmt->bindValue(2, $perfil_id);
            } else {
                $sql = "SELECT COUNT(*) as count FROM mccp_perfil 
                        WHERE nombre = ?";
                $stmt = $conectar->prepare($sql);
                $stmt->bindValue(1, $nombre_perfil);
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
            
        } catch (Exception $e) {
            error_log("Error al verificar nombre de perfil: " . $e->getMessage());
            return false;
        }
    }

    public function verificar_perfil_en_uso($perfil_id) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $sql = "SELECT COUNT(*) as count FROM mccp_usuario_perfil 
                    WHERE perfil_id = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $perfil_id);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
            
        } catch (Exception $e) {
            error_log("Error al verificar perfil en uso: " . $e->getMessage());
            return 0;
        }
    }

    public function eliminar($perfil_id) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $conectar->beginTransaction();
            
            // Primero eliminar los permisos asociados
            $sql_permisos = "DELETE FROM mccp_perfil_modulo WHERE perfil_id = ?";
            $stmt_permisos = $conectar->prepare($sql_permisos);
            $stmt_permisos->bindValue(1, $perfil_id);
            $stmt_permisos->execute();
            
            // Luego eliminar el perfil
            $sql_perfil = "DELETE FROM mccp_perfil WHERE id = ?";
            $stmt_perfil = $conectar->prepare($sql_perfil);
            $stmt_perfil->bindValue(1, $perfil_id);
            $stmt_perfil->execute();
            
            $conectar->commit();
            return true;
            
        } catch (Exception $e) {
            $conectar->rollBack();
            error_log("Error al eliminar perfil: " . $e->getMessage());
            return false;
        }
    }
}