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
            e.marca,
            e.modelo,
            i.contrato_equipo_id,
            i.fecha_incidencia,
            i.fecha_atencion,
            i.tiempo_atencion,
            i.tecnico_id,
            i.tecnico_id_fin,
            u.nombres,
            u.apellidos,
            u.foto_perfil,
            uf.nombres as nombres_uf,
            uf.apellidos as apellidos_uf,
            uf.foto_perfil as foto_perfil_uf,
            i.estado
            FROM mccp_incidencia i
            JOIN mccp_cliente c ON c.id = i.cliente_id
            JOIN mccp_contrato_alquiler ca ON ca.id = i.contrato_id
            LEFT JOIN mccp_equipo e ON e.id = i.equipo_id
            LEFT JOIN mccp_usuario u ON u.id = i.tecnico_id
            LEFT JOIN mccp_usuario uf ON uf.id = i.tecnico_id_fin
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

        // El equipo es opcional: sin equipo no hay registro en el contrato
        $contrato_equipo_id = null;

        if ($equipo_id) {
            $sql = "SELECT id
                    FROM mccp_contrato_equipo
                    WHERE contrato_id = ? AND
                          equipo_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $contrato_id);
            $sql->bindValue(2, $equipo_id);
            $sql->execute();
            $contrato = $sql->fetch(PDO::FETCH_ASSOC);

            $contrato_equipo_id = $contrato ? $contrato['id'] : null;
        }

        try {
            $sql = "INSERT INTO mccp_incidencia (tipo_incidencia, cliente_id, contrato_id, equipo_id, contrato_equipo_id, fecha_incidencia, tecnico_id, descripcion_problema, estado, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pendiente', NOW(), NOW())";

            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $tipo_incidencia);
            $sql->bindValue(2, $cliente_id);
            $sql->bindValue(3, $contrato_id);
            $sql->bindValue(4, $equipo_id);
            $sql->bindValue(5, $contrato_equipo_id);
            $sql->bindValue(6, $fecha_incidencia);
            $sql->bindValue(7, $tecnico_id);
            $sql->bindValue(8, $descripcion_problema);

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
            i.contrato_equipo_id,
            e.numero_serie,
            e.marca,
            e.modelo,
            e.categoria_equipo,
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
            i.fecha_atencion,
            i.observaciones,
            i.contador_final_bn,
            i.contador_final_color
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

    public function actualizarTicket($ticket_id, $tipo_incidencia, $cliente_id, $contrato_id,  $equipo_id, $fecha_incidencia, $tecnico_id, $descripcion_problema, $fecha_atencion = null, $contador_final_bn = null, $contador_final_color = null, $observaciones = null){
        $conectar = parent::conexion();
        parent::set_names();

        try {
            // Estado y datos de cierre actuales del ticket
            $sql_actual = "SELECT estado, fecha_atencion, contador_final_bn, contador_final_color, observaciones
                           FROM mccp_incidencia
                           WHERE id = ?";
            $stmt_actual = $conectar->prepare($sql_actual);
            $stmt_actual->bindValue(1, $ticket_id);
            $stmt_actual->execute();
            $actual = $stmt_actual->fetch(PDO::FETCH_ASSOC);

            if (!$actual) {
                return $this->responderJson(0, 'El ticket no existe');
            }

            if ($actual['estado'] === 'cancelado') {
                return $this->responderJson(0, 'Un ticket cancelado no se puede editar');
            }

            $es_resuelto = ($actual['estado'] === 'resuelto');

            if ($es_resuelto && $_SESSION['rol_usuario'] != 1) {
                return $this->responderJson(0, 'Solo el administrador puede editar un ticket finalizado');
            }

            // Los contadores solo aplican a impresoras
            $requiere_contadores = $this->equipoEsImpresora($equipo_id);

            if (!$requiere_contadores) {
                $contador_final_bn = null;
                $contador_final_color = null;
            }

            // Validaciones de los datos de cierre (solo ticket finalizado)
            if ($es_resuelto) {
                $inicio_ts = strtotime($fecha_incidencia);
                $fin_ts = $fecha_atencion ? strtotime($fecha_atencion) : false;

                if ($fin_ts === false) {
                    return $this->responderJson(0, 'La fecha y hora de finalización es requerida');
                }

                if ($fin_ts > time()) {
                    return $this->responderJson(0, 'La fecha de finalización no puede ser futura');
                }

                if ($inicio_ts !== false && $fin_ts < $inicio_ts) {
                    return $this->responderJson(0, 'La fecha de finalización no puede ser anterior a la fecha de inicio del ticket');
                }

                if (empty($observaciones)) {
                    return $this->responderJson(0, 'El campo observaciones es requerido');
                }

                if ($requiere_contadores && $contador_final_bn === null) {
                    return $this->responderJson(0, 'El contador final BN es requerido');
                }
            }

            $sql = "SELECT id
                    FROM mccp_contrato_equipo
                    WHERE contrato_id = ? AND
                          equipo_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $contrato_id);
            $sql->bindValue(2, $equipo_id);
            $sql->execute();
            $contrato = $sql->fetch(PDO::FETCH_ASSOC);

            $contrato_equipo_id = $contrato ? $contrato['id'] : null;

            $sql_upd = "UPDATE mccp_incidencia SET
                    tipo_incidencia = ?,
                    cliente_id = ?,
                    contrato_id = ?,
                    equipo_id = ?,
                    contrato_equipo_id = ?,
                    fecha_incidencia = ?,
                    tecnico_id = ?,
                    descripcion_problema = ?,
                    updated_at = NOW()";

            $params = [
                $tipo_incidencia,
                $cliente_id,
                $contrato_id,
                $equipo_id,
                $contrato_equipo_id,
                $fecha_incidencia,
                $tecnico_id,
                $descripcion_problema
            ];

            // Ticket finalizado: actualizar también los datos de cierre y el tiempo de atención
            if ($es_resuelto) {
                // Tiempo total en horas con decimales (ej: 24.50 = 24 horas y 30 minutos)
                $segundos = $fin_ts - $inicio_ts;
                $tiempo_atencion = round(floor($segundos / 3600) + (floor(($segundos % 3600) / 60) / 60), 2);

                $sql_upd .= ", fecha_atencion = ?, contador_final_bn = ?, contador_final_color = ?, observaciones = ?, tiempo_atencion = ?";
                $params[] = date('Y-m-d H:i:s', $fin_ts);
                $params[] = $contador_final_bn;
                $params[] = $contador_final_color;
                $params[] = $observaciones;
                $params[] = $tiempo_atencion;
            }

            $sql_upd .= " WHERE id = ?";
            $params[] = $ticket_id;

            $sql_upd = $conectar->prepare($sql_upd);

            foreach ($params as $index => $value) {
                $sql_upd->bindValue($index + 1, $value);
            }

            if ($sql_upd->execute()) {
                // Dejar constancia en el historial si cambió algún dato de cierre
                if ($es_resuelto) {
                    $this->registrarModificacionCierre($ticket_id, $actual, date('Y-m-d H:i:s', $fin_ts), $contador_final_bn, $contador_final_color, $observaciones);
                }

                return $this->responderJson(1, 'Ticket actualizado correctamente');
            }

            return $this->responderJson(0, 'Error al actualizar ticket');

        } catch (Exception $e) {
            return $this->responderJson(0, 'Error: ' . $e->getMessage());
        }
    }

    // Indica si el equipo es una impresora (únicas con contadores)
    private function equipoEsImpresora($equipo_id) {
        if (empty($equipo_id)) {
            return false;
        }

        $conectar = parent::conexion();
        parent::set_names();

        $stmt = $conectar->prepare("SELECT categoria_equipo FROM mccp_equipo WHERE id = ?");
        $stmt->bindValue(1, $equipo_id);
        $stmt->execute();

        return $stmt->fetchColumn() === 'impresora';
    }

    // Indica si el equipo del ticket es una impresora (requiere contadores al finalizar)
    public function ticketRequiereContadores($ticket_id) {
        $conectar = parent::conexion();
        parent::set_names();

        $stmt = $conectar->prepare("SELECT equipo_id FROM mccp_incidencia WHERE id = ?");
        $stmt->bindValue(1, $ticket_id);
        $stmt->execute();

        return $this->equipoEsImpresora($stmt->fetchColumn());
    }

    private function responderJson($success, $message) {
        $jsonData['success'] = $success;
        $jsonData['message'] = $message;

        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsonData);
    }

    // Inserta un comentario con los datos de cierre que cambiaron al editar un ticket finalizado
    private function registrarModificacionCierre($ticket_id, $actual, $fecha_atencion, $contador_bn, $contador_color, $observaciones) {
        $cambios = [];

        if (strtotime($actual['fecha_atencion']) !== strtotime($fecha_atencion)) {
            $cambios[] = "<b>Fecha de finalización: </b>" . date('d/m/Y h:i A', strtotime($actual['fecha_atencion'])) . " → " . date('d/m/Y h:i A', strtotime($fecha_atencion));
        }

        if ((string) $actual['contador_final_bn'] !== (string) $contador_bn) {
            $cambios[] = "<b>Contador Final BN: </b>" . ($actual['contador_final_bn'] ?? '-') . " → " . ($contador_bn ?? '-');
        }

        if ((string) $actual['contador_final_color'] !== (string) $contador_color) {
            $cambios[] = "<b>Contador Final Color: </b>" . ($actual['contador_final_color'] ?? '-') . " → " . ($contador_color ?? '-');
        }

        if ((string) $actual['observaciones'] !== (string) $observaciones) {
            $cambios[] = "<b>Observaciones actualizadas: </b>" . htmlspecialchars($observaciones, ENT_QUOTES, 'UTF-8');
        }

        if (empty($cambios)) {
            return;
        }

        $comentario = "<b>Ticket modificado por el administrador</b>\n" . implode("\n", $cambios);
        $this->insertar_comentario($ticket_id, $_SESSION['id'], $comentario, 'accion');
    }

    public function obtener_estado_ticket($ticket_id) {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT estado FROM mccp_incidencia WHERE id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $ticket_id);
        $sql->execute();
        return $sql->fetchColumn();
    }

    // Estado del ticket y autor de un comentario
    public function obtener_contexto_comentario($comentario_id) {
        $conectar = parent::conexion();
        parent::set_names();

        $sql = "SELECT c.usuario_id, i.estado
                FROM mccp_incidencia_comentario c
                INNER JOIN mccp_incidencia i ON i.id = c.incidencia_id
                WHERE c.id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $comentario_id);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function eliminarTicket($ticket_id) {
        $conectar = parent::conexion();
        parent::set_names();

        try {
            $conectar->beginTransaction();

            // Archivos adjuntos de todos los comentarios del ticket
            $sql_fotos = "SELECT f.ruta_archivo
                          FROM mccp_incidencia_foto f
                          INNER JOIN mccp_incidencia_comentario c ON c.id = f.comentario_id
                          WHERE c.incidencia_id = ?";
            $stmt_fotos = $conectar->prepare($sql_fotos);
            $stmt_fotos->bindValue(1, $ticket_id);
            $stmt_fotos->execute();
            $fotos = $stmt_fotos->fetchAll(PDO::FETCH_ASSOC);

            // Los comentarios y sus fotos se eliminan en cascada
            $sql_del = "DELETE FROM mccp_incidencia WHERE id = ?";
            $stmt_del = $conectar->prepare($sql_del);
            $stmt_del->bindValue(1, $ticket_id);

            if ($stmt_del->execute() && $stmt_del->rowCount() > 0) {
                $conectar->commit();

                // Eliminar archivos físicos una vez confirmada la eliminación en base de datos
                foreach ($fotos as $foto) {
                    $ruta_completa = '../' . $foto['ruta_archivo'];
                    if (file_exists($ruta_completa)) {
                        unlink($ruta_completa);
                    }
                }

                return array(
                    'success' => 1,
                    'message' => 'Ticket eliminado correctamente'
                );
            }

            $conectar->rollBack();
            return array(
                'success' => 0,
                'message' => 'No se pudo eliminar el ticket o no existe'
            );

        } catch (Exception $e) {
            if ($conectar->inTransaction()) {
                $conectar->rollBack();
            }
            return array(
                'success' => 0,
                'message' => 'Error: ' . $e->getMessage()
            );
        }
    }

    public function obtener_info_equipo($contrato_equipo_id) {
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
            e.marca,
            e.modelo,
            e.categoria_equipo,
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
            WHERE ce.id = ?
            LIMIT 1";
        
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $contrato_equipo_id, PDO::PARAM_INT);
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

    public function gestionarEstadoTicket($ticket_id, $observaciones, $accion, $contador_bn = null, $contador_color = null, $fecha_finalizacion = null) {
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
                // Los contadores solo aplican a impresoras
                $requiere_contadores = $this->ticketRequiereContadores($ticket_id);

                if (!$requiere_contadores) {
                    $contador_bn = null;
                    $contador_color = null;
                }

                // Validar que el contador BN esté presente
                if ($requiere_contadores && $contador_bn === null) {
                    return array(
                        'success' => 0,
                        'message' => 'El contador BN es requerido para finalizar'
                    );
                }
                
                // Validar que la fecha de finalización esté presente
                if ($fecha_finalizacion === null) {
                    return array(
                        'success' => 0,
                        'message' => 'La fecha y hora de finalización es requerida'
                    );
                }

                // Calcular el tiempo de atención en horas
                $sql_tiempo = "SELECT
                                TIMESTAMPDIFF(HOUR, fecha_incidencia, ?) as horas_enteras,
                                TIMESTAMPDIFF(MINUTE, fecha_incidencia, ?) % 60 as minutos_restantes
                            FROM mccp_incidencia
                            WHERE id = ?";
                $stmt_tiempo = $conectar->prepare($sql_tiempo);
                $stmt_tiempo->bindValue(1, $fecha_finalizacion);
                $stmt_tiempo->bindValue(2, $fecha_finalizacion);
                $stmt_tiempo->bindValue(3, $ticket_id);
                $stmt_tiempo->execute();
                $tiempo_data = $stmt_tiempo->fetch(PDO::FETCH_ASSOC);

                // La fecha de finalización no puede ser anterior al inicio del ticket
                if ($tiempo_data['horas_enteras'] < 0 || $tiempo_data['minutos_restantes'] < 0) {
                    return array(
                        'success' => 0,
                        'message' => 'La fecha de finalización no puede ser anterior a la fecha de inicio del ticket'
                    );
                }

                // Calcular tiempo total en horas con decimales (ej: 24.50 = 24 horas y 30 minutos)
                $tiempo_atencion = $tiempo_data['horas_enteras'] + ($tiempo_data['minutos_restantes'] / 60);
                
                $sql_upd .= ", tecnico_id_fin = " . $_SESSION['id'] . ", contador_final_bn = ?, contador_final_color = ?, fecha_atencion = ?, tiempo_atencion = ?";
                $params[] = $contador_bn;
                $params[] = $contador_color;
                $params[] = $fecha_finalizacion;
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