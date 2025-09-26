<?php
date_default_timezone_set('America/Bogota');

class Dashboard extends Conectar{
 
    public function obtener_estadisticas_dashboard(){
        $conectar = parent::conexion();
        parent::set_names();
        
        $sql = "SELECT 
                    -- Estadísticas de contratos
                    (SELECT COUNT(*) FROM mccp_contrato_alquiler) as total_contratos,
                    (SELECT COUNT(*) FROM mccp_contrato_alquiler WHERE estado = 'vigente') as contratos_vigentes,
                    (SELECT COUNT(*) FROM mccp_contrato_alquiler WHERE estado = 'finalizado') as contratos_finalizados,
                    (SELECT COUNT(*) FROM mccp_contrato_alquiler WHERE estado = 'suspendido') as contratos_suspendidos,
                    
                    -- Estadísticas de tickets
                    (SELECT COUNT(*) FROM mccp_incidencia) as total_tickets,
                    (SELECT COUNT(*) FROM mccp_incidencia WHERE estado = 'pendiente') as tickets_pendientes,
                    (SELECT COUNT(*) FROM mccp_incidencia WHERE estado = 'en_proceso') as tickets_en_proceso,
                    (SELECT COUNT(*) FROM mccp_incidencia WHERE estado = 'resuelto') as tickets_resueltos";
        
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
}