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
                        (perfil_id, modulo_id, puede_ver, puede_crear, puede_editar, puede_eliminar, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt_insert = $conectar->prepare($sql_insert);

            // Instanciar clase Modulo para obtener los módulos
            require_once("Modulo.php"); // Asegúrate de que la ruta sea correcta
            $moduloModel = new Modulo();
            // Obtener todos los módulos activos
            $modulos = $moduloModel->listar_modulos();
            
            foreach ($modulos as $modulo) {
                $modulo_id = $modulo['id'];
                
                if ($modulo['nombre'] == 'Dashboard') {
                    // Para Dashboard: siempre ver=1, otros permisos=0
                    $puede_ver = 1;
                    $puede_crear = 0;
                    $puede_editar = 0;
                    $puede_eliminar = 0;
                } else {
                    // Para otros módulos: usar los permisos enviados
                    $puede_ver = isset($permisos[$modulo_id]['ver']) ? 1 : 0;
                    $puede_crear = isset($permisos[$modulo_id]['crear']) ? 1 : 0;
                    $puede_editar = isset($permisos[$modulo_id]['editar']) ? 1 : 0;
                    $puede_eliminar = isset($permisos[$modulo_id]['eliminar']) ? 1 : 0;
                    
                    // Validación: si tiene crear, editar o eliminar, debe tener ver
                    if ($puede_crear || $puede_editar || $puede_eliminar) {
                        $puede_ver = 1;
                    }
                }
                
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

    public function obtener_permisos_perfil($perfil_id) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $sql = "SELECT pm.modulo_id, pm.puede_ver, pm.puede_crear, pm.puede_editar, pm.puede_eliminar
                    FROM mccp_perfil_modulo pm
                    WHERE pm.perfil_id = ?";
            
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $perfil_id);
            $stmt->execute();
            
            $permisos = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $permisos[$row['modulo_id']] = [
                    'ver' => $row['puede_ver'],
                    'crear' => $row['puede_crear'],
                    'editar' => $row['puede_editar'],
                    'eliminar' => $row['puede_eliminar']
                ];
            }
            
            return $permisos;
            
        } catch (Exception $e) {
            error_log("Error al obtener permisos del perfil: " . $e->getMessage());
            return [];
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

    // Obtener usuarios disponibles (estado=1, rol=2, no asignados al perfil)
    public function obtener_usuarios_disponibles($perfil_id) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            if ($perfil_id == 1) {
                // Para perfil 1: Solo usuarios sin ningún perfil asignado
                $sql = "SELECT 
                            u.id,
                            CONCAT(u.nombres, ' ', u.apellidos) as nombre_completo,
                            u.email
                        FROM mccp_usuario u
                        LEFT JOIN mccp_usuario_perfil up ON u.id = up.usuario_id
                        WHERE u.estado = 1
                        AND u.rol_usuario = 2
                        AND up.usuario_id IS NULL
                        ORDER BY u.nombres, u.apellidos";
                
                $stmt = $conectar->prepare($sql);
                
            } else {
                // Para otros perfiles: Excluir usuarios con este perfil o con perfil 1
                $sql = "SELECT 
                            u.id,
                            CONCAT(u.nombres, ' ', u.apellidos) as nombre_completo,
                            u.email
                        FROM mccp_usuario u
                        LEFT JOIN mccp_usuario_perfil up_actual 
                            ON u.id = up_actual.usuario_id 
                            AND up_actual.perfil_id = ?
                        LEFT JOIN mccp_usuario_perfil up_perfil_uno 
                            ON u.id = up_perfil_uno.usuario_id 
                            AND up_perfil_uno.perfil_id = 1
                        WHERE u.estado = 1
                        AND u.rol_usuario = 2
                        AND up_actual.usuario_id IS NULL
                        AND up_perfil_uno.usuario_id IS NULL
                        ORDER BY u.nombres, u.apellidos";
                
                $stmt = $conectar->prepare($sql);
                $stmt->bindValue(1, $perfil_id);
            }
            
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error al obtener usuarios disponibles: " . $e->getMessage());
            return [];
        }
    }

    public function asignar_usuarios_perfil($perfil_id, $usuarios_ids) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $conectar->beginTransaction();
            
            $sql_insert = "INSERT INTO mccp_usuario_perfil (usuario_id, perfil_id, created_at) 
                        VALUES (?, ?, NOW())";
            $stmt_insert = $conectar->prepare($sql_insert);
            
            $asignados = 0;
            $duplicados = 0;
            
            foreach ($usuarios_ids as $usuario_id) {
                // Verificar si ya existe la asignación
                $sql_check = "SELECT COUNT(*) as count FROM mccp_usuario_perfil 
                            WHERE usuario_id = ? AND perfil_id = ?";
                $stmt_check = $conectar->prepare($sql_check);
                $stmt_check->bindValue(1, $usuario_id);
                $stmt_check->bindValue(2, $perfil_id);
                $stmt_check->execute();
                $existe = $stmt_check->fetch(PDO::FETCH_ASSOC);
                
                if ($existe['count'] == 0) {
                    $stmt_insert->bindValue(1, $usuario_id);
                    $stmt_insert->bindValue(2, $perfil_id);
                    $stmt_insert->execute();
                    $asignados++;
                } else {
                    $duplicados++;
                }
            }
            
            $conectar->commit();
            
            // Preparar mensaje
            $mensaje = '';
            if ($asignados > 0) {
                $mensaje = "Asignación realizada correctamente";
            }
            if ($duplicados > 0) {
                if ($mensaje) $mensaje .= ". ";
                $mensaje .= "$duplicados usuario(s) ya estaban asignados";
            }
            
            return [
                'success' => true,
                'message' => $mensaje,
                'asignados' => $asignados,
                'duplicados' => $duplicados
            ];
            
        } catch (Exception $e) {
            $conectar->rollBack();
            error_log("Error al asignar usuarios: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al asignar usuarios: ' . $e->getMessage()
            ];
        }
    }

    public function obtener_usuarios($perfil_id) {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT
                up.id as up_id,
                u.id,
                u.usuario,
                u.nombres,
                u.apellidos,
                u.apellidos,
                u.foto_perfil
            FROM mccp_usuario u
            INNER JOIN mccp_usuario_perfil up ON u.id = up.usuario_id
            WHERE up.perfil_id = ? 
            AND u.estado = '1'
            ORDER BY u.nombres ASC";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $perfil_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function eliminar_usuario_perfil($usuario_perfil_id) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $conectar->beginTransaction();
            
            // Primero eliminar los permisos asociados
            $sql_del = "DELETE FROM mccp_usuario_perfil WHERE id = ?";
            $sql_del = $conectar->prepare($sql_del);
            $sql_del->bindValue(1, $usuario_perfil_id);
            $sql_del->execute();
            
            $conectar->commit();
            return true;
            
        } catch (Exception $e) {
            $conectar->rollBack();
            error_log("Error al eliminar asignación: " . $e->getMessage());
            return false;
        }
    }

    public function listar_perfil_combo(){
        $conectar= parent::conexion();
        parent::set_names();
        $sql="SELECT 
        id,
        nombre
        FROM mccp_perfil
        WHERE estado = 1";
        $sql=$conectar->prepare($sql);
        $sql->execute();
        return $resultado=$sql->fetchAll();
    }
}