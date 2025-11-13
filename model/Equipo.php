<?php
date_default_timezone_set('America/Bogota');
class Equipo extends Conectar{

    public function listar() {
        $conectar=parent::conexion();
        parent::set_names();

        $sql = "SELECT 
        e.id,
        e.marca,
        e.modelo,
        e.numero_serie,
        e.tipo_equipo,
        CASE WHEN e.tipo_equipo = 'bn'
            THEN 'BLANCO/NEGRO'
            ELSE 'COLOR'
        END nombre_tipo,
        e.condicion,
        CASE 
            WHEN e.condicion = 'nuevo' THEN 'NUEVO'
            WHEN e.condicion = 'seminuevo' THEN 'SEMINUEVO'
        END nombre_condicion,
        e.estado,
        e.proveedor_id,
        CASE 
            WHEN p.razon_social IS NOT NULL AND p.razon_social <> '' 
                THEN p.razon_social
            ELSE CONCAT(p.nombre_proveedor, ' ', p.apellido_paterno, ' ', p.apellido_materno)
        END AS proveedor
        FROM mccp_equipo e
        JOIN mccp_proveedor p ON p.id = e.proveedor_id
        ORDER BY e.numero_serie ASC";

        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function verificarEquipoExiste($numero_serie, $equipo_id = null) {
        $conectar = parent::conexion();
        parent::set_names();

        if ($equipo_id) {
            // Si es edición, excluir el ID actual de la búsqueda
            $sql = "SELECT COUNT(*) as total FROM mccp_equipo WHERE numero_serie = ? AND id != ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $numero_serie);
            $sql->bindValue(2, $equipo_id);
        } else {
            // Si es nuevo registro
            $sql = "SELECT COUNT(*) as total FROM mccp_equipo WHERE numero_serie = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $numero_serie);
        }
        
        $sql->execute();
        $resultado = $sql->fetch(PDO::FETCH_ASSOC);
        
        return $resultado['total'] > 0;
    }

    public function insertarEquipo($marca, $modelo, $numero_serie, $tipo_equipo, $condicion, $proveedor_id, $fecha_compra, $costo_dolares, $costo_soles,  $contador_inicial_bn, $contador_actual_bn,  $contador_inicial_color, $contador_actual_color, $estado, $observaciones) {
        $conectar=parent::conexion();
        parent::set_names();

        try {
            $sql = "INSERT INTO mccp_equipo (marca, modelo, numero_serie, tipo_equipo, condicion, proveedor_id, fecha_compra, costo_dolares, costo_soles, contador_inicial_bn, contador_actual_bn, contador_inicial_color, contador_actual_color, estado, observaciones, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $marca);
            $sql->bindValue(2, $modelo);
            $sql->bindValue(3, $numero_serie);
            $sql->bindValue(4, $tipo_equipo);
            $sql->bindValue(5, $condicion);
            $sql->bindValue(6, $proveedor_id);
            $sql->bindValue(7, $fecha_compra);
            $sql->bindValue(8, $costo_dolares);
            $sql->bindValue(9, $costo_soles);
            $sql->bindValue(10, $contador_inicial_bn);
            $sql->bindValue(11, $contador_actual_bn);
            $sql->bindValue(12, $contador_inicial_color);
            $sql->bindValue(13, $contador_actual_color);
            $sql->bindValue(14, $estado);
            $sql->bindValue(15, $observaciones);

            if($sql->execute()) {
                $jsonData['success'] = 1;
                $jsonData['message'] = 'Equipo registrado correctamente';
            } else {
                $jsonData['success'] = 0;
                $jsonData['message'] = 'Error al registrar el equipo';
            }
            
        } catch (Exception $e) {
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
        }
        
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsonData);   
    }

    public function actualizarEquipo($equipo_id, $marca, $modelo, $numero_serie, $tipo_equipo, $condicion, $proveedor_id, $fecha_compra, $costo_dolares, $costo_soles,  $contador_inicial_bn, $contador_actual_bn,  $contador_inicial_color, $contador_actual_color, $estado, $observaciones) {
        $conectar = parent::conexion();
        parent::set_names();

        try {

            // Obtener el estado actual del equipo
            $sql_estado = "SELECT estado FROM mccp_equipo WHERE id = ?";
            $stmt_estado = $conectar->prepare($sql_estado);
            $stmt_estado->bindValue(1, $equipo_id);
            $stmt_estado->execute();
            $equipo_actual = $stmt_estado->fetch(PDO::FETCH_ASSOC);
            
            // Si el estado recibido no es 'activo' ni 'inactivo', mantener el estado actual
            if ($equipo_actual['estado'] != 'activo' && $equipo_actual['estado'] != 'inactivo') {
                $estado = $equipo_actual['estado'];
            }

            $sql_upd = "UPDATE mccp_equipo SET 
                    marca = ?, 
                    modelo = ?, 
                    numero_serie = ?,
                    tipo_equipo = ?,
                    condicion = ?,
                    proveedor_id = ?,
                    fecha_compra = ?, 
                    costo_dolares = ?, 
                    costo_soles = ?, 
                    contador_inicial_bn = ?,
                    contador_actual_bn = ?,
                    contador_inicial_color = ?,
                    contador_actual_color = ?,
                    estado = ?,
                    observaciones = ?,
                    updated_at = NOW() 
                    WHERE id = ?";

            $sql_upd = $conectar->prepare($sql_upd);
            $sql_upd->bindValue(1, $marca);
            $sql_upd->bindValue(2, $modelo);
            $sql_upd->bindValue(3, $numero_serie);
            $sql_upd->bindValue(4, $tipo_equipo);
            $sql_upd->bindValue(5, $condicion);
            $sql_upd->bindValue(6, $proveedor_id);
            $sql_upd->bindValue(7, $fecha_compra);
            $sql_upd->bindValue(8, $costo_dolares);
            $sql_upd->bindValue(9, $costo_soles);
            $sql_upd->bindValue(10, $contador_inicial_bn);
            $sql_upd->bindValue(11, $contador_actual_bn); 
            $sql_upd->bindValue(12, $contador_inicial_color); 
            $sql_upd->bindValue(13, $contador_actual_color);           
            $sql_upd->bindValue(14, $estado);
            $sql_upd->bindValue(15, $observaciones); 
            $sql_upd->bindValue(16, $equipo_id);

            if($sql_upd->execute()){
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

    public function obtener_equipo_por_id($equipo_id) {
        $conectar= parent::conexion();
        parent::set_names();
        $sql="SELECT
            mccp_equipo.id,
	        mccp_equipo.marca,	
	        mccp_equipo.modelo,
	        mccp_equipo.numero_serie,
	        mccp_equipo.tipo_equipo,
	        mccp_equipo.condicion,
	        mccp_equipo.estado,
	        mccp_equipo.proveedor_id,
            CASE 
                WHEN p.razon_social IS NOT NULL AND p.razon_social <> '' 
                    THEN p.razon_social
                ELSE CONCAT(p.nombre_proveedor, ' ', p.apellido_paterno, ' ', p.apellido_materno)
            END AS proveedor,
	        mccp_equipo.fecha_compra,
	        mccp_equipo.costo_dolares,
	        mccp_equipo.costo_soles,
	        mccp_equipo.contador_inicial_bn,
	        mccp_equipo.contador_inicial_color,
	        mccp_equipo.contador_actual_bn,
	        mccp_equipo.contador_actual_color,	
	        mccp_equipo.observaciones
        FROM mccp_equipo
        JOIN mccp_proveedor p ON p.id = mccp_equipo.proveedor_id
        WHERE mccp_equipo.id = ?";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1, $equipo_id);
        $sql->execute();
        return $resultado=$sql->fetchAll();
    }
    
    public function editar_estado_equipo($equipo_id, $estado) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $conectar->beginTransaction();

            $estado = $estado == '1' ? 'activo' : 'inactivo';
            
            $sql_upd = "UPDATE mccp_equipo SET estado = ? WHERE id = ?";
            $sql_upd = $conectar->prepare($sql_upd);
            $sql_upd->bindValue(1, $estado);
            $sql_upd->bindValue(2, $equipo_id);
            $sql_upd->execute();
            
            $conectar->commit();
            return true;
            
        } catch (Exception $e) {
            $conectar->rollBack();
            error_log("Error al actualizar estado: " . $e->getMessage());
            return false;
        }
    }

    public function combo_equipo($equipo_id_actual = null) {
        $conectar = parent::conexion();
        parent::set_names();
        
        $sql = "SELECT 
                id,
                CONCAT(marca, ' ', modelo, ' - ', numero_serie) as nombre
            FROM mccp_equipo
            WHERE estado = 'activo'";

        if ($equipo_id_actual) {
            $sql .= " OR id = ?";  // Incluir el equipo actual aunque esté asignado
        }

        $sql .= " ORDER BY marca ASC";
        $stmt = $conectar->prepare($sql);

        if ($equipo_id_actual) {
            $stmt->bindValue(1, $equipo_id_actual);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}