<?php
date_default_timezone_set('America/Bogota');
class Cliente extends Conectar{

    public function listar() {
        $conectar=parent::conexion();
        parent::set_names();

        $sql = "SELECT 
            c.id,
            c.tipo_ruc,
            c.ruc,
            CASE 
                WHEN c.razon_social IS NOT NULL AND c.razon_social <> '' 
                    THEN c.razon_social
                ELSE CONCAT(c.nombre_cliente, ' ', c.apellido_paterno, ' ', c.apellido_materno)
            END AS cliente,
            dc.direccion AS direccion_principal,
            dc.departamento,
            dc.provincia,
            dc.distrito,
            dc.referencia,
            CONCAT(dc.direccion, ', ', dc.distrito) info_direccion,
            u.usuario,
            c.estado
        FROM 
            mccp_cliente c
        LEFT JOIN 
            mccp_direccion_cliente dc 
                ON dc.cliente_id = c.id 
                AND dc.es_principal = 1
        LEFT JOIN 
            mccp_usuario u 
                ON u.cliente_id = c.id
        ORDER BY 
            cliente ASC";

        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function insertarCliente($tipo_ruc, $ruc, $razon_social, $nombre_cliente, $apellido_paterno, $apellido_materno, $departamento, $provincia, $distrito, $direccion, $referencia, $estado_cliente) {
        $conectar=parent::conexion();
        parent::set_names();

        try {
            $sql = "INSERT INTO mccp_cliente (tipo_ruc, ruc, razon_social, nombre_cliente, apellido_paterno, apellido_materno, estado, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $tipo_ruc);
            $sql->bindValue(2, $ruc);
            $sql->bindValue(3, $razon_social ? $razon_social : null);
            $sql->bindValue(4, $nombre_cliente ? $nombre_cliente : null);
            $sql->bindValue(5, $apellido_paterno ? $apellido_paterno : null);
            $sql->bindValue(6, $apellido_materno ? $apellido_materno : null);
            $sql->bindValue(7, $estado_cliente);

            if($sql->execute()){
                $cliente_id = $conectar->lastInsertId();

                // Si se ingresa una direccion, insertarlo en mccp_direccion_cliente
                if (!empty($direccion)) {
                    $sql_direccion = "INSERT INTO mccp_direccion_cliente (cliente_id, direccion, distrito, provincia, departamento, referencia, es_principal, created_at, updated_at) 
                                VALUES (?, ?, ?, ?, ?, ?, 1, NOW(), NOW())";
                    $sql_direccion = $conectar->prepare($sql_direccion);
                    $sql_direccion->bindValue(1, $cliente_id);
                    $sql_direccion->bindValue(2, $direccion);
                    $sql_direccion->bindValue(3, $distrito);
                    $sql_direccion->bindValue(4, $provincia);
                    $sql_direccion->bindValue(5, $departamento);
                    $sql_direccion->bindValue(6, $referencia);
                    $sql_direccion->execute();
                }

                $jsonData['success'] = 1;
                $jsonData['message'] = 'Cliente registrado correctamente';
            } else {
                $jsonData['success'] = 0;
                $jsonData['message'] = 'Error al registrar el cliente';
            }
            
        } catch (Exception $e) {
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
        }
        
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsonData);   
    }

    public function obtener_cliente_por_id($cliente_id) {
        $conectar= parent::conexion();
        parent::set_names();
        $sql="SELECT
            id,
            CASE WHEN tipo_ruc = 1
                THEN 'JURÍDICO'
                ELSE 'NATURAL'
            END nom_tipo_ruc,
            tipo_ruc,
            ruc,
            razon_social,
            nombre_cliente,
            apellido_paterno,
            apellido_materno,
            estado,
            CASE WHEN estado = 1
                THEN 'ACTIVO'
                ELSE 'INACTIVO'
            END nom_estado
        FROM mccp_cliente
        WHERE id = ?";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1, $cliente_id);
        $sql->execute();
        return $resultado=$sql->fetchAll();
    }

    // Obtener direcciones del cliente con sus contactos
    public function obtener_direcciones_cliente($cliente_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT
            d.id,
            d.cliente_id,
            d.direccion,
            d.distrito,
            d.provincia,
            d.departamento,
            d.referencia,
            d.es_principal,
            COUNT(c.id) as total_contactos
        FROM mccp_direccion_cliente d
        LEFT JOIN mccp_contacto_direccion c ON d.id = c.direccion_id
        WHERE d.cliente_id = ?
        GROUP BY d.id, d.cliente_id, d.direccion, d.distrito, d.provincia, 
                d.departamento, d.referencia, d.es_principal
        ORDER BY d.es_principal DESC, d.id ASC";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $cliente_id);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener contactos de una dirección específica
    public function obtener_contactos_direccion($direccion_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT
            id,
            direccion_id,
            nombre_contacto,
            cargo_contacto,
            email_contacto,
            telefono_contacto,
            fecha_cumple
        FROM mccp_contacto_direccion
        WHERE direccion_id = ?
        ORDER BY id ASC";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $direccion_id);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function verificarClienteExiste($ruc, $cliente_id = null) {
        $conectar = parent::conexion();
        parent::set_names();

        if ($cliente_id) {
            // Si es edición, excluir el ID actual de la búsqueda
            $sql = "SELECT COUNT(*) as total FROM mccp_cliente WHERE ruc = ? AND id != ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $ruc);
            $sql->bindValue(2, $cliente_id);
        } else {
            // Si es nuevo registro
            $sql = "SELECT COUNT(*) as total FROM mccp_cliente WHERE ruc = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $ruc);
        }
        
        $sql->execute();
        $resultado = $sql->fetch(PDO::FETCH_ASSOC);
        
        return $resultado['total'] > 0;
    }

    public function actualizarCliente($cliente_id, $tipo_ruc, $ruc, $razon_social, $nombre_cliente, $apellido_paterno, $apellido_materno, $estado) {
        $conectar = parent::conexion();
        parent::set_names();

        try {
            $sql_upd = "UPDATE mccp_cliente SET 
                    tipo_ruc = ?, 
                    ruc = ?, 
                    razon_social = ?, 
                    nombre_cliente = ?, 
                    apellido_paterno = ?, 
                    apellido_materno = ?, 
                    estado = ?,
                    updated_at = NOW() 
                    WHERE id = ?";

            $sql_upd = $conectar->prepare($sql_upd);
            $sql_upd->bindValue(1, $tipo_ruc);
            $sql_upd->bindValue(2, $ruc);
            $sql_upd->bindValue(3, $razon_social ? $razon_social : null);
            $sql_upd->bindValue(4, $nombre_cliente ? $nombre_cliente : null);
            $sql_upd->bindValue(5, $apellido_paterno ? $apellido_paterno : null);
            $sql_upd->bindValue(6, $apellido_materno ? $apellido_materno : null);

            $sql_upd->bindValue(7, $estado);
            $sql_upd->bindValue(8, $cliente_id);

            if($sql_upd->execute()){
                $jsonData['success'] = 1;
                $jsonData['message'] = 'Cliente actualizado correctamente';
            } else {
                $jsonData['success'] = 0;
                $jsonData['message'] = 'Error al actualizar cliente';
            }
            
        } catch (Exception $e) {
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
        }
        
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsonData);
    }

    public function editar_estado_cliente($cliente_id, $estado) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $conectar->beginTransaction();
            
            $sql_upd = "UPDATE mccp_cliente SET estado = ? WHERE id = ?";
            $sql_upd = $conectar->prepare($sql_upd);
            $sql_upd->bindValue(1, $estado);
            $sql_upd->bindValue(2, $cliente_id);
            $sql_upd->execute();
            
            $conectar->commit();
            return true;
            
        } catch (Exception $e) {
            $conectar->rollBack();
            error_log("Error al actualizar estado: " . $e->getMessage());
            return false;
        }
    }

    public function insertarDireccion($cliente_id, $departamento, $provincia, $distrito, $direccion, $referencia, $es_principal) {
        $conectar=parent::conexion();
        parent::set_names();

        try {
            $sql_insert = "INSERT INTO mccp_direccion_cliente (cliente_id, direccion, distrito, provincia, departamento, referencia, es_principal, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

            $sql_insert = $conectar->prepare($sql_insert);
            $sql_insert->bindValue(1, $cliente_id);
            $sql_insert->bindValue(2, $direccion);
            $sql_insert->bindValue(3, $distrito);
            $sql_insert->bindValue(4, $provincia);
            $sql_insert->bindValue(5, $departamento);
            $sql_insert->bindValue(6, $referencia);
            $sql_insert->bindValue(7, $es_principal);

            if($sql_insert->execute()){

                $direccion_id = $conectar->lastInsertId();

                // Si se ingresa una direccion, insertarlo en mccp_direccion_cliente
                if ($es_principal == 1) {
                    $sql_upd = "UPDATE mccp_direccion_cliente SET es_principal = 0, updated_at = NOW() WHERE cliente_id = ? AND id != ?";
                    $sql_upd = $conectar->prepare($sql_upd);
                    $sql_upd->bindValue(1, $cliente_id);
                    $sql_upd->bindValue(2, $direccion_id);
                    $sql_upd->execute();
                }

                $jsonData['success'] = 1;
                $jsonData['message'] = 'Dirección registrada correctamente';
            } else {
                $jsonData['success'] = 0;
                $jsonData['message'] = 'Error al registrar la dirección';
            }
            
        } catch (Exception $e) {
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
        }
        
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsonData);   
    }

    public function actualizarDireccion($cliente_id, $direccion_id, $departamento, $provincia, $distrito, $direccion, $referencia, $es_principal) {
        $conectar = parent::conexion();
        parent::set_names();

        try {
            // Iniciar transacción
            $conectar->beginTransaction();

            // VALIDACIÓN 1: Si es_principal = 0, verificar que exista al menos otra dirección principal
            if ($es_principal == 0) {
                $sql_check = "SELECT COUNT(*) as total 
                            FROM mccp_direccion_cliente 
                            WHERE cliente_id = ? 
                            AND id != ? 
                            AND es_principal = 1";
                
                $stmt_check = $conectar->prepare($sql_check);
                $stmt_check->bindValue(1, $cliente_id);
                $stmt_check->bindValue(2, $direccion_id);
                $stmt_check->execute();
                $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
                
                if ($result['total'] == 0) {
                    $jsonData['success'] = 0;
                    $jsonData['message'] = 'Debe existir al menos una dirección principal';
                    
                    header('Content-type: application/json; charset=utf-8');
                    echo json_encode($jsonData);
                    return;
                }
            }

            // VALIDACIÓN 2: Si es_principal = 1, actualizar las demás direcciones a 0
            if ($es_principal == 1) {
                $sql_reset = "UPDATE mccp_direccion_cliente 
                            SET es_principal = 0 
                            WHERE cliente_id = ? 
                            AND id != ?";
                
                $stmt_reset = $conectar->prepare($sql_reset);
                $stmt_reset->bindValue(1, $cliente_id);
                $stmt_reset->bindValue(2, $direccion_id);
                $stmt_reset->execute();
            }

            // Actualizar la dirección (corregido el typo "provincipa" a "provincia")
            $sql_upd = "UPDATE mccp_direccion_cliente 
                        SET direccion = ?, 
                            distrito = ?, 
                            provincia = ?, 
                            departamento = ?, 
                            referencia = ?, 
                            es_principal = ?, 
                            updated_at = NOW()
                        WHERE id = ?";

            $sql_upd = $conectar->prepare($sql_upd);
            $sql_upd->bindValue(1, $direccion);
            $sql_upd->bindValue(2, $distrito);
            $sql_upd->bindValue(3, $provincia);
            $sql_upd->bindValue(4, $departamento);
            $sql_upd->bindValue(5, $referencia);
            $sql_upd->bindValue(6, $es_principal);
            $sql_upd->bindValue(7, $direccion_id);

            if ($sql_upd->execute()) {
                // Confirmar transacción
                $conectar->commit();
                
                $jsonData['success'] = 1;
                $jsonData['message'] = 'Dirección actualizada correctamente';
            } else {
                $conectar->rollBack();
                $jsonData['success'] = 0;
                $jsonData['message'] = 'Error al actualizar la dirección';
            }
            
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            if ($conectar->inTransaction()) {
                $conectar->rollBack();
            }
            
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
        }
        
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsonData);   
    }

    public function eliminar_direccion($direccion_id) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $conectar->beginTransaction();
            
            $sql_del = "DELETE FROM mccp_direccion_cliente WHERE id = ?";
            $sql_del = $conectar->prepare($sql_del);
            $sql_del->bindValue(1, $direccion_id);
            $sql_del->execute();
            
            $conectar->commit();
            return true;
            
        } catch (Exception $e) {
            $conectar->rollBack();
            error_log("Error al eliminar dirección: " . $e->getMessage());
            return false;
        }
    }

    public function obtener_direccion_x_id($direccion_id) {
        $conectar = parent::conexion();
        parent::set_names();
        
        $sql="SELECT
        id,
        cliente_id,
        direccion,
        referencia,
        distrito,
        provincia,
        departamento,
        es_principal
        FROM mccp_direccion_cliente
        WHERE id = ?";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1, $direccion_id);
        $sql->execute();
        return $resultado=$sql->fetchAll();
    }

    public function insertarContacto($c_direccion_id, $nombre_contacto, $cargo_contacto, $email_contacto, $telefono_contacto, $fecha_cumple) {
        $conectar=parent::conexion();
        parent::set_names();

        try {
            $sql_insert = "INSERT INTO mccp_contacto_direccion (direccion_id, nombre_contacto, cargo_contacto, email_contacto, telefono_contacto, fecha_cumple, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";

            $sql_insert = $conectar->prepare($sql_insert);
            $sql_insert->bindValue(1, $c_direccion_id);
            $sql_insert->bindValue(2, $nombre_contacto);
            $sql_insert->bindValue(3, $cargo_contacto);
            $sql_insert->bindValue(4, $email_contacto);
            $sql_insert->bindValue(5, $telefono_contacto);
            $sql_insert->bindValue(6, $fecha_cumple);

            if($sql_insert->execute()){
                $jsonData['success'] = 1;
                $jsonData['message'] = 'Contacto registrado correctamente';
            } else {
                $jsonData['success'] = 0;
                $jsonData['message'] = 'Error al registrar el contacto';
            }
            
        } catch (Exception $e) {
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
        }
        
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsonData);   
    }

    public function actualizarContacto($contacto_id, $nombre_contacto, $cargo_contacto, $email_contacto, $telefono_contacto, $fecha_cumple) {
        $conectar = parent::conexion();
        parent::set_names();

        try {
            // Iniciar transacción
            $conectar->beginTransaction();

            // Actualizar la dirección (corregido el typo "provincipa" a "provincia")
            $sql_upd = "UPDATE mccp_contacto_direccion 
                        SET nombre_contacto     = ?, 
                            cargo_contacto      = ?, 
                            email_contacto      = ?, 
                            telefono_contacto   = ?,
                            fecha_cumple        = ?, 
                            updated_at = NOW()
                        WHERE id = ?";

            $sql_upd = $conectar->prepare($sql_upd);
            $sql_upd->bindValue(1, $nombre_contacto);
            $sql_upd->bindValue(2, $cargo_contacto);
            $sql_upd->bindValue(3, $email_contacto);
            $sql_upd->bindValue(4, $telefono_contacto);
            $sql_upd->bindValue(5, $fecha_cumple);
            $sql_upd->bindValue(6, $contacto_id);

            if ($sql_upd->execute()) {
                // Confirmar transacción
                $conectar->commit();
                
                $jsonData['success'] = 1;
                $jsonData['message'] = 'Contacto actualizado correctamente';
            } else {
                $conectar->rollBack();
                $jsonData['success'] = 0;
                $jsonData['message'] = 'Error al actualizar el contacto';
            }
            
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            if ($conectar->inTransaction()) {
                $conectar->rollBack();
            }
            
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
        }
        
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($jsonData);   
    }

    public function eliminar_contacto($contacto_id) {
        $conectar = parent::conexion();
        parent::set_names();
        
        try {
            $conectar->beginTransaction();
            
            $sql_del = "DELETE FROM mccp_contacto_direccion WHERE id = ?";
            $sql_del = $conectar->prepare($sql_del);
            $sql_del->bindValue(1, $contacto_id);
            $sql_del->execute();
            
            $conectar->commit();
            return true;
            
        } catch (Exception $e) {
            $conectar->rollBack();
            error_log("Error al eliminar contacto: " . $e->getMessage());
            return false;
        }
    }

    public function obtener_contacto_x_id($contacto_id) {
        $conectar = parent::conexion();
        parent::set_names();
        
        $sql="SELECT
        id,
        direccion_id,
        nombre_contacto,
        cargo_contacto,
        email_contacto,
        telefono_contacto,
        fecha_cumple
        FROM mccp_contacto_direccion
        WHERE id = ?";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1, $contacto_id);
        $sql->execute();
        return $resultado=$sql->fetchAll();
    }

    public function combo_cliente($cliente_id_actual = null) {
        $conectar = parent::conexion();
        parent::set_names();
        
        // Query base
        $sql = "SELECT 
            c.id,
            CASE 
                WHEN c.razon_social IS NOT NULL THEN c.razon_social
                ELSE CONCAT(c.nombre_cliente, ' ', c.apellido_paterno, ' ', c.apellido_materno)
            END AS nombre_cliente
        FROM mccp_cliente c
        WHERE c.estado = 1 
        ORDER BY nombre_cliente";
        $stmt = $conectar->prepare($sql);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function combo_cliente_contrato($cliente_id_actual = null) {
        $conectar = parent::conexion();
        parent::set_names();
        
        // Query base
        $sql = "SELECT 
            c.id,
            CASE 
                WHEN c.razon_social IS NOT NULL THEN c.razon_social
                ELSE CONCAT(c.nombre_cliente, ' ', c.apellido_paterno, ' ', c.apellido_materno)
            END AS nombre_cliente
        FROM mccp_cliente c
        INNER JOIN mccp_contrato_alquiler ca ON ca.cliente_id = c.id
        WHERE c.estado = 1 AND
       	ca.estado IN ('pendiente','vigente')
        ORDER BY nombre_cliente;";
        $stmt = $conectar->prepare($sql);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function combo_direccion_cliente($cliente_id, $direccion_id_actual = null) {
        $conectar = parent::conexion();
        parent::set_names();
        
        $sql = "SELECT 
            id,
            CONCAT(
                direccion, 
                ', ',
                distrito,
                ', ',
                provincia,
                ', ',
                departamento
            ) AS direccion_completa,
            es_principal
        FROM mccp_direccion_cliente
        WHERE cliente_id = ?
        ORDER BY es_principal DESC, id ASC";
        
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $cliente_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>