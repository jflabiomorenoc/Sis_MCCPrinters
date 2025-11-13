<?php
date_default_timezone_set('America/Bogota');
class Ticket extends Conectar{

    public function listar($ver_todos = 0) {
        $conectar = parent::conexion();
        parent::set_names();

        require_once("Dashboard.php");
        $dashboard = new Dashboard();

        // Si ver_todos = 1, no aplicar filtro técnico
        if ($_SESSION['rol_usuario'] != 1 && $ver_todos == 0) {
            $filtro = $dashboard->construir_filtro_usuario($_SESSION['id'], $_SESSION['rol_usuario'], 'tickets');
        } else {
            $filtro = ['sql' => '', 'params' => []];
        }

        $sql = "SELECT
            i.id,
            i.tipo_incidencia,
            i.numero_ticket,
            CASE 
                WHEN c.razon_social IS NOT NULL AND c.razon_social <> '' 
                    THEN c.razon_social
                ELSE CONCAT(c.nombre_cliente, ' ', c.apellido_paterno, ' ', c.apellido_materno)
            END AS cliente,
            i.contrato_id,
            ca.numero_contrato,
            i.equipo_id,
            CASE 
                WHEN i.equipo_id IS NOT NULL
                    THEN e.numero_serie
                ELSE '-'
            END numero_serie,
            i.fecha_incidencia,
            i.fecha_atencion,
            i.tiempo_atencion,
            i.tecnico_id,
            u.nombres,
            u.apellidos,
            u.foto_perfil,
            i.estado
            FROM mccp_incidencia i
            JOIN mccp_cliente c ON c.id = i.cliente_id
            JOIN mccp_contrato_alquiler ca ON ca.id = i.contrato_id
            LEFT JOIN mccp_equipo e ON e.id = i.equipo_id
            LEFT JOIN mccp_usuario u ON u.id = i.tecnico_id
            WHERE 1=1 {$filtro['sql']}
            ORDER BY i.estado ASC, i.numero_ticket DESC";

        $stmt = $conectar->prepare($sql);

        foreach ($filtro['params'] as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function insertatTicket($tipo_incidencia, $cliente_id, $contrato_id, $equipo_id, $fecha_incidencia, $tecnico_id, $descripcion_problema) {
        $conectar=parent::conexion();
        parent::set_names();

        try {
            $sql = "INSERT INTO mccp_incidencia (tipo_incidencia, cliente_id, contrato_id, equipo_id, fecha_incidencia, tecnico_id, descripcion_problema, estado, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'pendiente', NOW(), NOW())";

            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $tipo_incidencia);
            $sql->bindValue(2, $cliente_id);
            $sql->bindValue(3, $contrato_id);
            $sql->bindValue(4, $equipo_id);
            $sql->bindValue(5, $fecha_incidencia);
            $sql->bindValue(6, $tecnico_id);
            $sql->bindValue(7, $descripcion_problema);

            if($sql->execute()) {
                $jsonData['success'] = 1;
                $jsonData['message'] = 'Ticket registrado correctamente';
            } else {
                $jsonData['success'] = 0;
                $jsonData['message'] = 'Error al registrar el ticket';
            }
            
        } catch (Exception $e) {
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
        }
        
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsonData);   
    }

    public function obtener_ticket_por_id($ticket_id) {
        $conectar= parent::conexion();
        parent::set_names();
        $sql="SELECT
            i.id,
            i.tipo_incidencia,
            i.numero_ticket,
            i.cliente_id,
            CASE 
                WHEN c.razon_social IS NOT NULL AND c.razon_social <> '' 
                    THEN c.razon_social
                ELSE CONCAT(c.nombre_cliente, ' ', c.apellido_paterno, ' ', c.apellido_materno)
            END AS nombre_cliente,
            i.contrato_id,
            ca.numero_contrato,
            i.equipo_id,
            e.numero_serie,
            e.tipo_equipo,
            i.tecnico_id,
            CASE
                WHEN i.tecnico_id IS NOT NULL
                    THEN CONCAT(u.nombres, ' ', u.apellidos)
                ELSE NULL
            END nombre_tecnico,   
            i.fecha_incidencia,
            i.estado,
            i.descripcion_problema,
            i.fecha_atencion
        FROM mccp_incidencia i
        INNER JOIN mccp_contrato_alquiler ca ON ca.id = i.contrato_id
        INNER JOIN mccp_cliente c ON c.id = i.cliente_id
        LEFT JOIN mccp_usuario u ON u.id = i.tecnico_id
        LEFT JOIN mccp_equipo e ON e.id = i.equipo_id
        WHERE i.id = ?";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1, $ticket_id);
        $sql->execute();
        return $resultado=$sql->fetchAll();
    }

    public function actualizarTicket($ticket_id, $tipo_incidencia, $cliente_id, $contrato_id,  $equipo_id, $fecha_incidencia, $tecnico_id, $descripcion_problema, $fecha_atencion, $contador_final_bn, $contador_final_color, $observaciones){
        $conectar = parent::conexion();
        parent::set_names();

        try {
            $sql_upd = "UPDATE mccp_incidencia SET
                    tipo_incidencia = ?, 
                    cliente_id = ?, 
                    contrato_id = ?,
                    equipo_id = ?,
                    fecha_incidencia = ?,
                    tecnico_id = ?,
                    descripcion_problema = ?,
                    fecha_atencion = ?,
                    contador_final_bn = ?,
                    contador_final_color = ?,
                    observaciones = ?,
                    updated_at = NOW()
                    WHERE id = ?";

            $sql_upd = $conectar->prepare($sql_upd);
            $sql_upd->bindValue(1, $tipo_incidencia);
            $sql_upd->bindValue(2, $cliente_id);
            $sql_upd->bindValue(3, $contrato_id);
            $sql_upd->bindValue(4, $equipo_id);
            $sql_upd->bindValue(5, $fecha_incidencia);
            $sql_upd->bindValue(6, $tecnico_id);
            $sql_upd->bindValue(7, $descripcion_problema);
            $sql_upd->bindValue(8, $fecha_atencion);
            $sql_upd->bindValue(9, $contador_final_bn);
            $sql_upd->bindValue(10, $contador_final_color);
            $sql_upd->bindValue(11, $observaciones);
            $sql_upd->bindValue(12, $ticket_id);

            if($sql_upd->execute()){
                $jsonData['success'] = 1;
                $jsonData['message'] = 'Ticket actualizado correctamente';
            } else {
                $jsonData['success'] = 0;
                $jsonData['message'] = 'Error al actualizar ticket';
            }
            
        } catch (Exception $e) {
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
        }
        
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsonData);
    }

    public function obtener_info_equipo($equipo_id, $contrato_id) {
        $conectar = parent::conexion();
        parent::set_names();
        
        $sql = "SELECT 
            ce.id,
            ce.contrato_id,
            ce.equipo_id,
            ce.ip_equipo,
            ce.area_ubicacion,
            ce.contador_inicial_bn,
            ce.contador_inicial_color,
            e.numero_serie,
            e.tipo_equipo,
            e.condicion,
            CONCAT(
                d.direccion, ', ',
                d.distrito, ', ',
                d.provincia, ', ',
                d.departamento
            ) AS direccion
            FROM mccp_contrato_equipo ce
            INNER JOIN mccp_equipo e ON e.id = ce.equipo_id
            INNER JOIN mccp_direccion_cliente d ON d.id = ce.direccion_id
            WHERE ce.equipo_id = ? 
            AND ce.contrato_id = ?
            LIMIT 1";
        
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $equipo_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $contrato_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtener_info_contrato($contrato_id) {
        $conectar = parent::conexion();
        parent::set_names();
        
        $sql = "SELECT 
            ca.id,
            ca.numero_contrato,
            ca.cliente_id,
            ca.fecha_inicio,
            ca.fecha_culminacion,
            ca.estado,
            ca.observaciones,
            CASE 
                WHEN c.razon_social IS NOT NULL AND c.razon_social <> '' 
                    THEN c.razon_social
                ELSE CONCAT(c.nombre_cliente, ' ', c.apellido_paterno, ' ', c.apellido_materno)
            END AS cliente
            FROM mccp_contrato_alquiler ca
            INNER JOIN mccp_cliente c ON c.id = ca.cliente_id
            WHERE ca.id = ?
            LIMIT 1";
        
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $contrato_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function gestionarEstadoTicket($ticket_id, $observaciones, $accion, $contador_bn = null, $contador_color = null) {
        $conectar = parent::conexion(); 
        parent::set_names();
        
        try {
            // Validar acción
            if (!in_array($accion, ['cancelar', 'finalizar'])) {
                return array(
                    'success' => 0,
                    'message' => 'Acción no válida'
                );
            }
            
            // Determinar el estado según la acción
            $estado = ($accion === 'cancelar') ? 'cancelado' : 'resuelto';
            
            // Construir el SQL base
            $sql_upd = "UPDATE mccp_incidencia SET 
                        estado = ?, 
                        observaciones = ?, 
                        updated_at = NOW()";
            
            $params = [$estado, $observaciones];
            
            // Si es finalizar, agregar los contadores, fecha y tiempo de atención
            if ($accion === 'finalizar') {
                // Validar que el contador BN esté presente
                if ($contador_bn === null) {
                    return array(
                        'success' => 0,
                        'message' => 'El contador BN es requerido para finalizar'
                    );
                }
                
                // Calcular el tiempo de atención en horas
                $sql_tiempo = "SELECT 
                                TIMESTAMPDIFF(HOUR, fecha_incidencia, NOW()) as horas_enteras,
                                TIMESTAMPDIFF(MINUTE, fecha_incidencia, NOW()) % 60 as minutos_restantes
                            FROM mccp_incidencia 
                            WHERE id = ?";
                $stmt_tiempo = $conectar->prepare($sql_tiempo);
                $stmt_tiempo->bindValue(1, $ticket_id);
                $stmt_tiempo->execute();
                $tiempo_data = $stmt_tiempo->fetch(PDO::FETCH_ASSOC);
                
                // Calcular tiempo total en horas con decimales (ej: 24.50 = 24 horas y 30 minutos)
                $tiempo_atencion = $tiempo_data['horas_enteras'] + ($tiempo_data['minutos_restantes'] / 60);
                
                $sql_upd .= ", contador_final_bn = ?, contador_final_color = ?, fecha_atencion = NOW(), tiempo_atencion = ?";
                $params[] = $contador_bn;
                $params[] = $contador_color;
                $params[] = round($tiempo_atencion, 2); // Redondear a 2 decimales
            }
            
            $sql_upd .= " WHERE id = ?";
            $params[] = $ticket_id;
            
            // Ejecutar la consulta
            $stmt = $conectar->prepare($sql_upd);
            
            foreach ($params as $index => $value) {
                $stmt->bindValue($index + 1, $value);
            }
            
            if($stmt->execute() && $stmt->rowCount() > 0){
                $mensaje = ($accion === 'cancelar') 
                    ? 'Ticket cancelado correctamente' 
                    : 'Ticket finalizado correctamente';
                
                $response = array(
                    'success' => 1,
                    'message' => $mensaje,
                    'accion' => $accion
                );
                
                // Agregar tiempo de atención a la respuesta si es finalizar
                if ($accion === 'finalizar') {
                    $response['tiempo_atencion'] = round($tiempo_atencion, 2);
                    $response['tiempo_atencion_formateado'] = $this->formatearTiempoAtencion($tiempo_atencion);
                }
                
                return $response;
            } else {
                return array(
                    'success' => 0,
                    'message' => 'No se pudo procesar el ticket o el ticket no existe'
                );
            }
            
        } catch (Exception $e) {
            return array(
                'success' => 0,
                'message' => 'Error: ' . $e->getMessage()
            );
        }
    }

    // Función auxiliar para formatear el tiempo de atención
    private function formatearTiempoAtencion($horas) {
        $horas_enteras = floor($horas);
        $minutos = round(($horas - $horas_enteras) * 60);
        
        $dias = floor($horas_enteras / 24);
        $horas_restantes = $horas_enteras % 24;
        
        $partes = [];
        
        if ($dias > 0) {
            $partes[] = $dias . ($dias == 1 ? ' día' : ' días');
        }
        if ($horas_restantes > 0) {
            $partes[] = $horas_restantes . ($horas_restantes == 1 ? ' hora' : ' horas');
        }
        if ($minutos > 0) {
            $partes[] = $minutos . ($minutos == 1 ? ' minuto' : ' minutos');
        }
        
        return implode(', ', $partes) ?: '0 minutos';
    }

    public function listar_comentarios($ticket_id) {
        $conectar = parent::conexion();
        parent::set_names();
        
        $sql = "SELECT 
                    ic.id,
                    ic.incidencia_id,
                    ic.usuario_id,
                    ic.comentario,
                    ic.tipo,
                    ic.created_at,
                    u.nombres,
                    u.apellidos,
                    u.foto_perfil
                FROM mccp_incidencia_comentario ic
                INNER JOIN mccp_usuario u ON u.id = ic.usuario_id
                WHERE ic.incidencia_id = ?
                ORDER BY ic.created_at DESC";
        
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $ticket_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listar_fotos_comentario($comentario_id) {
        $conectar = parent::conexion();
        parent::set_names();
        
        $sql = "SELECT 
                    id,
                    comentario_id,
                    nombre_archivo,
                    ruta_archivo,
                    tipo_archivo,
                    created_at
                FROM mccp_incidencia_foto
                WHERE comentario_id = ?
                ORDER BY created_at ASC";
        
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $comentario_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertar_comentario($ticket_id, $usuario_id, $comentario, $tipo = 'accion') {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $sql = "INSERT INTO mccp_incidencia_comentario (incidencia_id, usuario_id, comentario, tipo, created_at) 
                    VALUES (?, ?, ?, ?, NOW())";
            
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $ticket_id);
            $stmt->bindValue(2, $usuario_id);
            $stmt->bindValue(3, $comentario);
            $stmt->bindValue(4, $tipo);
            
            if($stmt->execute()) {
                $comentario_id = $conectar->lastInsertId();
                
                return array(
                    'success' => 1,
                    'message' => 'Comentario agregado correctamente',
                    'comentario_id' => $comentario_id
                );
            } else {
                return array(
                    'success' => 0,
                    'message' => 'Error al agregar el comentario'
                );
            }
            
        } catch (Exception $e) {
            return array(
                'success' => 0,
                'message' => 'Error: ' . $e->getMessage()
            );
        }
    }

    // Actualizar para incluir tipo de archivo
    public function insertar_archivo_comentario($comentario_id, $nombre_archivo, $ruta_archivo, $tipo_archivo) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $sql = "INSERT INTO mccp_incidencia_foto (comentario_id, nombre_archivo, ruta_archivo, tipo_archivo, created_at) 
                    VALUES (?, ?, ?, ?, NOW())";
            
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $comentario_id);
            $stmt->bindValue(2, $nombre_archivo);
            $stmt->bindValue(3, $ruta_archivo);
            $stmt->bindValue(4, $tipo_archivo);
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            return false;
        }
    }

    // Mantener la función antigua para compatibilidad (si es necesario)
    public function insertar_foto_comentario($comentario_id, $nombre_archivo, $ruta_archivo) {
        return $this->insertar_archivo_comentario($comentario_id, $nombre_archivo, $ruta_archivo, 'image/jpeg');
    }

    public function editar_estado_ticket($ticket_id, $estado) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $conectar->beginTransaction();
            
            $sql_upd = "UPDATE mccp_incidencia SET estado = ?, updated_at = NOW() WHERE id = ?";
            $sql_upd = $conectar->prepare($sql_upd);
            $sql_upd->bindValue(1, $estado);
            $sql_upd->bindValue(2, $ticket_id);
            $sql_upd->execute();
            
            $conectar->commit();
            return true;
            
        } catch (Exception $e) {
            $conectar->rollBack();
            error_log("Error al actualizar estado: " . $e->getMessage());
            return false;
        }
    }

    public function eliminarComentario($comentario_id) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            // Primero, obtener las fotos asociadas para eliminarlas del servidor
            $sql_fotos = "SELECT ruta_archivo FROM mccp_incidencia_foto WHERE comentario_id = ?";
            $stmt_fotos = $conectar->prepare($sql_fotos);
            $stmt_fotos->bindValue(1, $comentario_id);
            $stmt_fotos->execute();
            $fotos = $stmt_fotos->fetchAll(PDO::FETCH_ASSOC);
            
            // Eliminar archivos físicos
            foreach ($fotos as $foto) {
                $ruta_completa = '../' . $foto['ruta_archivo'];
                if (file_exists($ruta_completa)) {
                    unlink($ruta_completa);
                }
            }
            
            // Eliminar registros de fotos de la base de datos
            $sql_del_fotos = "DELETE FROM mccp_incidencia_foto WHERE comentario_id = ?";
            $stmt_del_fotos = $conectar->prepare($sql_del_fotos);
            $stmt_del_fotos->bindValue(1, $comentario_id);
            $stmt_del_fotos->execute();
            
            // Eliminar el comentario
            $sql_del_comentario = "DELETE FROM mccp_incidencia_comentario WHERE id = ?";
            $stmt_del_comentario = $conectar->prepare($sql_del_comentario);
            $stmt_del_comentario->bindValue(1, $comentario_id);
            
            if ($stmt_del_comentario->execute() && $stmt_del_comentario->rowCount() > 0) {
                return array(
                    'success' => 1,
                    'message' => 'Comentario eliminado correctamente'
                );
            } else {
                return array(
                    'success' => 0,
                    'message' => 'No se pudo eliminar el comentario o no existe'
                );
            }
            
        } catch (Exception $e) {
            return array(
                'success' => 0,
                'message' => 'Error: ' . $e->getMessage()
            );
        }
    }
}