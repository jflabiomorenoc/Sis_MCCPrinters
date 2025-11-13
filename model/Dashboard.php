<?php
date_default_timezone_set('America/Bogota');

class Dashboard extends Conectar {
    
    // Determinar tipo de usuario y construir condiciones WHERE
    private function obtener_condiciones_filtro($usuario_id, $rol_usuario) {
        $condiciones = [
            'contratos' => '',
            'tickets' => '',
            'params' => []
        ];
        
        // Si es administrador, sin filtros
        if ($rol_usuario == 1) {
            return $condiciones;
        }
        
        // Si es usuario normal, verificar sus perfiles
        require_once("Usuario.php");
        $usuario = new Usuario();
        $info_perfil = $usuario->obtener_perfil_usuario($usuario_id);
        
        if ($info_perfil['es_tecnico']) {
            // TÉCNICO: Ver asignados a él o sin asignar
            $condiciones['contratos'] = 'AND (tecnico_id = :usuario_id OR tecnico_id IS NULL)';
            $condiciones['tickets'] = 'AND (tecnico_id = :usuario_id OR tecnico_id IS NULL)';
            $condiciones['params'] = [':usuario_id' => $_SESSION['cliente_id']];
            
        } else if ($info_perfil['es_cliente']) {
            // CLIENTE: Ver solo los suyos
            $condiciones['contratos'] = 'AND cliente_id = :usuario_id';
            $condiciones['tickets'] = 'AND contrato_id IN (SELECT id FROM mccp_contrato_alquiler WHERE cliente_id = :usuario_id)';
            $condiciones['params'] = [':usuario_id' => $_SESSION['cliente_id']];
        }
        
        return $condiciones;
    }
    
    public function obtener_estadisticas_dashboard($usuario_id, $rol_usuario) {
        $conectar = parent::conexion();
        parent::set_names();
        
        // Obtener condiciones de filtro
        $filtros = $this->obtener_condiciones_filtro($usuario_id, $rol_usuario);
        
        $sql = "SELECT 
                    -- Estadísticas de contratos
                    (SELECT COUNT(*) 
                     FROM mccp_contrato_alquiler 
                     WHERE 1=1 {$filtros['contratos']}) as total_contratos,
                    
                    (SELECT COUNT(*) 
                     FROM mccp_contrato_alquiler 
                     WHERE estado = 'vigente' {$filtros['contratos']}) as contratos_vigentes,
                    
                    (SELECT COUNT(*) 
                     FROM mccp_contrato_alquiler 
                     WHERE estado = 'finalizado' {$filtros['contratos']}) as contratos_finalizados,
                    
                    (SELECT COUNT(*) 
                     FROM mccp_contrato_alquiler 
                     WHERE estado = 'cancelado' {$filtros['contratos']}) as contratos_suspendidos,
                    
                    -- Estadísticas de tickets
                    (SELECT COUNT(*) 
                     FROM mccp_incidencia 
                     WHERE 1=1 {$filtros['tickets']}) as total_tickets,
                    
                    (SELECT COUNT(*) 
                     FROM mccp_incidencia 
                     WHERE estado = 'pendiente' {$filtros['tickets']}) as tickets_pendientes,
                    
                    (SELECT COUNT(*) 
                     FROM mccp_incidencia 
                     WHERE estado = 'en_proceso' {$filtros['tickets']}) as tickets_en_proceso,
                    
                    (SELECT COUNT(*) 
                     FROM mccp_incidencia 
                     WHERE estado = 'resuelto' {$filtros['tickets']}) as tickets_resueltos";
        
        $stmt = $conectar->prepare($sql);
        
        // Vincular parámetros si existen
        foreach ($filtros['params'] as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function construir_filtro_usuario($usuario_id, $rol_usuario, $tipo = 'contratos') {        
        if ($rol_usuario == 1) {
            return ['sql' => '', 'params' => []];
        }
        
        require_once("Usuario.php");
        $usuario = new Usuario();
        $info_perfil = $usuario->obtener_perfil_usuario($usuario_id);
        
        if ($info_perfil['es_tecnico']) {
            if ($tipo == 'contratos') {
                $sql = 'AND (ca.tecnico_id = :usuario_id OR tecnico_id IS NULL)';

                return [
                    'sql' => $sql,
                    'params' => [':usuario_id' => $usuario_id]
                ];
            }

            if ($tipo == 'tickets') {
                $sql = 'AND i.tecnico_id = :usuario_id';

                return [
                    'sql' => $sql,
                    'params' => [':usuario_id' => $usuario_id]
                ];
            }
        }
        
        if ($info_perfil['es_cliente']) {
            if ($tipo == 'contratos') {
                return [
                    'sql' => 'AND ca.cliente_id = :usuario_id',
                    'params' => [':usuario_id' => $_SESSION['cliente_id']]
                ];
            }
            if ($tipo == 'tickets') {
                return [
                    'sql' => 'AND i.contrato_id IN (SELECT id FROM mccp_contrato_alquiler WHERE cliente_id = :usuario_id)',
                    'params' => [':usuario_id' => $_SESSION['cliente_id']]
                ];
            }
        }
        
        return ['sql' => '', 'params' => []];
    }
}