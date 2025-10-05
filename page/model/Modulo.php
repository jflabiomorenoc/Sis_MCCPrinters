<?php
class Modulo extends Conectar {
    
    // Obtener módulos con sus permisos para un perfil específico
    public function obtener_modulos_con_permisos($perfil_id = null) {
        $conectar = parent::conexion();
        parent::set_names();
        
        $sql = "SELECT 
                    m.id,
                    m.nombre,
                    m.estado,
                    CASE 
                        WHEN LOWER(m.nombre) = 'Dashboard' THEN 1
                        ELSE COALESCE(p.puede_ver, 0)
                    END as puede_ver,
                    CASE 
                        WHEN LOWER(m.nombre) = 'Dashboard' THEN 0
                        ELSE COALESCE(p.puede_crear, 0)
                    END as puede_crear,
                    CASE 
                        WHEN LOWER(m.nombre) = 'Dashboard' THEN 0
                        ELSE COALESCE(p.puede_editar, 0)
                    END as puede_editar,
                    CASE 
                        WHEN LOWER(m.nombre) = 'Dashboard' THEN 0
                        ELSE COALESCE(p.puede_eliminar, 0)
                    END as puede_eliminar,
                    CASE 
                        WHEN LOWER(m.nombre) = 'Dashboard' THEN 1
                        ELSE 0
                    END as es_dashboard
                FROM mccp_modulo m
                LEFT JOIN mccp_perfil_modulo p ON m.id = p.modulo_id AND p.perfil_id = ?
                WHERE m.estado = 1
                ORDER BY 
                    CASE WHEN LOWER(m.nombre) = 'Dashboard' THEN 0 ELSE 1 END,
                    m.id ASC";
        
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $perfil_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Listar todos los módulos activos
    public function listar_modulos() {
        $conectar = parent::conexion();
        parent::set_names();
        
        $sql = "SELECT id, nombre, estado 
                FROM mccp_modulo 
                WHERE estado = 1 
                ORDER BY id ASC";
        
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>