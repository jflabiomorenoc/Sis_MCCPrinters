<?php
date_default_timezone_set('America/Bogota');
class Contrato extends Conectar{

    public function listar($ver_todos = 0) {
        $conectar=parent::conexion();
        parent::set_names();

        require_once("Dashboard.php");
        $dashboard = new Dashboard();

        // Si ver_todos = 1, no aplicar filtro técnico
        if ($_SESSION['rol_usuario'] != 1 && $ver_todos == 0) {
            $filtro = $dashboard->construir_filtro_usuario($_SESSION['id'], $_SESSION['rol_usuario'], 'contratos');
        } else {
            $filtro = ['sql' => '', 'params' => []];
        }

        $sql = "SELECT 
        ca.id,
        ca.numero_contrato,
        ca.fecha_inicio,
        ca.fecha_culminacion,
        ca.estado,
        ca.cliente_id,
        CASE 
            WHEN c.razon_social IS NOT NULL AND c.razon_social <> '' 
                THEN c.razon_social
            ELSE CONCAT(c.nombre_cliente, ' ', c.apellido_paterno, ' ', c.apellido_materno)
        END AS cliente
        FROM mccp_contrato_alquiler ca
        JOIN mccp_cliente c ON c.id = ca.cliente_id
        WHERE 1=1 {$filtro['sql']}
        ORDER BY ca.numero_contrato DESC";

        $stmt = $conectar->prepare($sql);
        
        foreach ($filtro['params'] as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function insertarContrato($cliente_id, $tecnico_id, $fecha_inicio, $fecha_culminacion, $observaciones) {
        $conectar=parent::conexion();
        parent::set_names();

        try {
            $sql = "INSERT INTO mccp_contrato_alquiler (cliente_id, tecnico_id, fecha_inicio, fecha_culminacion, observaciones, estado, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, 'pendiente', NOW(), NOW())";

            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $cliente_id);
            $sql->bindValue(2, $tecnico_id);
            $sql->bindValue(3, $fecha_inicio);
            $sql->bindValue(4, $fecha_culminacion);
            $sql->bindValue(5, $observaciones);

            if($sql->execute()) {
                $jsonData['success'] = 1;
                $jsonData['message'] = 'Contrato registrado correctamente';
            } else {
                $jsonData['success'] = 0;
                $jsonData['message'] = 'Error al registrar el contrato';
            }
            
        } catch (Exception $e) {
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
        }
        
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsonData);   
    }

    public function obtener_contrato_por_id($contrato_id) {
        $conectar= parent::conexion();
        parent::set_names();
        $sql="SELECT
            ca.id,
            ca.numero_contrato,
            ca.cliente_id,
            CASE 
                WHEN c.razon_social IS NOT NULL AND c.razon_social <> '' 
                    THEN c.razon_social
                ELSE CONCAT(c.nombre_cliente, ' ', c.apellido_paterno, ' ', c.apellido_materno)
            END AS nombre_cliente,
            ca.tecnico_id,
            CASE
                WHEN ca.tecnico_id IS NOT NULL
                    THEN CONCAT(u.nombres, ' ', u.apellidos)
                ELSE NULL
            END nombre_tecnico,   
            ca.fecha_inicio,
            ca.fecha_culminacion,
            ca.estado,
            ca.observaciones
        FROM mccp_contrato_alquiler ca
        INNER JOIN mccp_cliente c ON c.id = ca.cliente_id
        LEFT JOIN mccp_usuario u ON u.id = ca.tecnico_id
        WHERE ca.id = ?";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1, $contrato_id);
        $sql->execute();
        return $resultado=$sql->fetchAll();
    }
    
    public function actualizarContrato($contrato_id, $cliente_id, $tecnico_id, $fecha_inicio, $fecha_culminacion, $observaciones) {
        $conectar = parent::conexion();
        parent::set_names();

        try {
            $sql_upd = "UPDATE mccp_contrato_alquiler SET 
                    cliente_id = ?, 
                    tecnico_id = ?,
                    fecha_inicio = ?,
                    fecha_culminacion = ?,
                    observaciones = ?,
                    updated_at = NOW() 
                    WHERE id = ?";

            $sql_upd = $conectar->prepare($sql_upd);
            $sql_upd->bindValue(1, $cliente_id);
            $sql_upd->bindValue(2, $tecnico_id);
            $sql_upd->bindValue(3, $fecha_inicio);
            $sql_upd->bindValue(4, $fecha_culminacion);
            $sql_upd->bindValue(5, $observaciones);
            $sql_upd->bindValue(6, $contrato_id);

            if($sql_upd->execute()){
                $jsonData['success'] = 1;
                $jsonData['message'] = 'Contrato actualizado correctamente';
            } else {
                $jsonData['success'] = 0;
                $jsonData['message'] = 'Error al actualizar contrato';
            }
            
        } catch (Exception $e) {
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
        }
        
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsonData);
    }

    public function editar_estado_contrato($contrato_id, $estado) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $conectar->beginTransaction();
            
            $sql_upd = "UPDATE mccp_contrato_alquiler SET estado = ? WHERE id = ?";
            $sql_upd = $conectar->prepare($sql_upd);
            $sql_upd->bindValue(1, $estado);
            $sql_upd->bindValue(2, $contrato_id);
            $sql_upd->execute();
            
            $conectar->commit();
            return true;
            
        } catch (Exception $e) {
            $conectar->rollBack();
            error_log("Error al actualizar estado: " . $e->getMessage());
            return false;
        }
    }
    
    public function obtener_equipos_contrato($contrato_id) {
        $conectar = parent::conexion();
        parent::set_names();
        
        $sql = "SELECT 
            ce.id as contrato_equipo_id,
            ce.contrato_id,
            ce.equipo_id,
            ce.ip_equipo,
            ce.area_ubicacion,
            ce.contador_inicial_bn,
            ce.contador_inicial_color,
            ce.contador_final_bn,
            ce.contador_final_color,
            ce.estado,
            eq.marca,
            eq.modelo,
            eq.numero_serie,
            eq.tipo_equipo
        FROM mccp_contrato_equipo ce
        INNER JOIN mccp_equipo eq ON ce.equipo_id = eq.id
        WHERE ce.contrato_id = ? AND
        ce.estado != 'retirado'
        ORDER BY ce.created_at DESC";
        
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $contrato_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertar_equipo_contrato($contrato_id, $direccion_id, $equipo_id, $ip_equipo, $area_ubicacion, $contador_inicial_bn, $contador_final_bn, $contador_inicial_color, $contador_final_color) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $sql = "INSERT INTO mccp_contrato_equipo 
                    (contrato_id, direccion_id, equipo_id, ip_equipo, area_ubicacion, contador_inicial_bn, contador_final_bn, contador_inicial_color, contador_final_color, fecha_inicio, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), NOW())";
            
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $contrato_id);
            $stmt->bindValue(2, $direccion_id);
            $stmt->bindValue(3, $equipo_id);
            $stmt->bindValue(4, $ip_equipo);
            $stmt->bindValue(5, $area_ubicacion);
            $stmt->bindValue(6, $contador_inicial_bn);
            $stmt->bindValue(7, $contador_final_bn);
            $stmt->bindValue(8, $contador_inicial_color);
            $stmt->bindValue(9, $contador_final_color);
            
            if($stmt->execute()) {
                $jsonData['success'] = 1;
                $jsonData['message'] = 'Equipo agregado correctamente';
            } else {
                $jsonData['success'] = 0;
                $jsonData['message'] = 'Error al agregar el equipo';
            }
            
        } catch (Exception $e) {
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
        }
        
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsonData);   
    }

    public function actualizar_equipo_contrato($contrato_equipo_id, $direccion_id, $equipo_id, $ip_equipo, $area_ubicacion, $contador_inicial_bn, $contador_final_bn, $contador_inicial_color, $contador_final_color) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $sql = "UPDATE mccp_contrato_equipo 
                    SET direccion_id = ?,
                        equipo_id = ?, 
                        ip_equipo = ?, 
                        area_ubicacion = ?, 
                        contador_inicial_bn = ?, 
                        contador_final_bn = ?, 
                        contador_inicial_color = ?,
                        contador_final_color = ?,
                        updated_at = NOW()
                    WHERE id = ?";
            
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $direccion_id);
            $stmt->bindValue(2, $equipo_id);
            $stmt->bindValue(3, $ip_equipo);
            $stmt->bindValue(4, $area_ubicacion);
            $stmt->bindValue(5, $contador_inicial_bn);
            $stmt->bindValue(6, $contador_final_bn);
            $stmt->bindValue(7, $contador_inicial_color);
            $stmt->bindValue(8, $contador_final_color);
            $stmt->bindValue(9, $contrato_equipo_id);
            
            if($stmt->execute()){
                $jsonData['success'] = 1;
                $jsonData['message'] = 'Equipo actualizado correctamente';
            } else {
                $jsonData['success'] = 0;
                $jsonData['message'] = 'Error al actualizar equipo';
            }
            
        } catch (Exception $e) {
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
        }
        
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsonData);
    }

    public function obtener_equipo_contrato($contrato_equipo_id) {
        $conectar = parent::conexion();
        parent::set_names();
        
        $sql = "SELECT 
		mccp_contrato_alquiler.cliente_id,
        mccp_contrato_equipo.direccion_id,
        mccp_contrato_equipo.ip_equipo,
        mccp_contrato_equipo.area_ubicacion,
        mccp_contrato_equipo.contador_inicial_bn,
        mccp_contrato_equipo.contador_inicial_color,
        mccp_contrato_equipo.contador_final_bn,
        mccp_contrato_equipo.contador_final_color,
        mccp_contrato_equipo.equipo_id,
        CONCAT(mccp_equipo.marca, ' ', mccp_equipo.modelo, ' - ', mccp_equipo.numero_serie) as nombre_equipo       
        FROM mccp_contrato_equipo 
        INNER JOIN mccp_equipo ON mccp_equipo.id = mccp_contrato_equipo.equipo_id
        INNER JOIN mccp_contrato_alquiler ON mccp_contrato_alquiler.id = mccp_contrato_equipo.contrato_id
        WHERE mccp_contrato_equipo.id = ?";
        
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $contrato_equipo_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function eliminar_equipo_contrato($contrato_equipo_id) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $conectar->beginTransaction();
            
            $sql = "UPDATE mccp_contrato_equipo 
                    SET estado = 'retirado', fecha_retiro = NOW() 
                    WHERE id = ?";
            
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $contrato_equipo_id);
            $stmt->execute();
            
            $conectar->commit();
            return true;
            
        } catch (Exception $e) {
            $conectar->rollBack();
            error_log("Error al eliminar el equipo: " . $e->getMessage());
            return false;
        }
    }

    public function get_contratos_por_cliente($cliente_id, $contrato_id_actual = null) {
        $conectar = parent::conexion();
        parent::set_names();
        
        $sql = "SELECT 
                    id,
                    numero_contrato,
                    estado,
                    fecha_inicio,
                    fecha_culminacion,
                    tecnico_id
                FROM mccp_contrato_alquiler
                WHERE cliente_id = ?
                AND estado IN ('vigente', 'pendiente')
                ORDER BY 
                    FIELD(estado, 'vigente', 'pendiente'),
                    fecha_inicio DESC";
        
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $cliente_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener equipos vigentes de un contrato
    public function get_equipos_por_contrato($contrato_id, $equipo_id_actual = null) {
        $conectar = parent::conexion();
        parent::set_names();
        
        $sql = "SELECT 
                    ce.equipo_id,
                    e.marca,
                    e.modelo,
                    e.numero_serie,
                    ce.ip_equipo,
                    ce.area_ubicacion
                FROM mccp_contrato_equipo ce
                INNER JOIN mccp_equipo e ON ce.equipo_id = e.id
                WHERE ce.contrato_id = ?
                AND ce.estado = 'vigente'
                ORDER BY e.marca, e.modelo";
        
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $contrato_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}