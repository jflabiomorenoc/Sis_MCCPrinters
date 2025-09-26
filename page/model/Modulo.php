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
                    COALESCE(p.puede_ver, 0) as puede_ver,
                    COALESCE(p.puede_crear, 0) as puede_crear,
                    COALESCE(p.puede_editar, 0) as puede_editar,
                    COALESCE(p.puede_eliminar, 0) as puede_eliminar
                FROM mccp_modulo m
                LEFT JOIN mccp_perfil_modulo p ON m.id = p.modulo_id AND p.perfil_id = ?
                WHERE m.estado = 1
                ORDER BY m.nombre ASC";
        
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
                ORDER BY nombre ASC";
        
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>