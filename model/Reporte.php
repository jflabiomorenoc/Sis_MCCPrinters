<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Reporte extends Conectar {

    public function generarExcelClientes() {
        require '../vendor/autoload.php';
        
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT
                    c.id,
                    dc.id as direccion_id,
                    CASE WHEN c.tipo_ruc = 1
                        THEN 'JURÍDICO'
                        ELSE 'NATURAL'
                    END tipo_ruc,
                    c.ruc,
                    CASE WHEN c.tipo_ruc = 1
                        THEN c.razon_social
                        ELSE CONCAT(c.nombre_cliente, ' ', c.apellido_paterno, ' ', c.apellido_materno)
                    END razon_social,
                    UPPER(dc.departamento) departamento,
                    UPPER(dc.provincia) provincia,
                    UPPER(dc.distrito) distrito,
                    UPPER(dc.direccion) direccion,
                    IFNULL(cd.nombre_contacto, '-') contacto,
                    IFNULL(cd.telefono_contacto, '-') telefono_contacto,
                    IFNULL(cd.fecha_cumple, '-') fecha_cumple
                FROM mccp_direccion_cliente dc
                LEFT JOIN mccp_contacto_direccion cd ON cd.direccion_id = dc.id
                LEFT JOIN mccp_cliente c ON c.id = dc.cliente_id
                WHERE c.estado = 1
                ORDER BY c.id ASC, dc.id ASC";

        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $spreadsheet = new Spreadsheet();
        $hojaActiva = $spreadsheet->getActiveSheet();
        $hojaActiva->setTitle("Clientes");

        // Configurar encabezados
        $encabezados = [
            'A' => ['NRO.', 15],
            'B' => ['JURIDICO / NATURAL', 20],
            'C' => ['N° DOCUMENTO', 20],
            'D' => ['RAZON SOCIAL', 65],
            'E' => ['DEPARTAMENTO', 30],
            'F' => ['PROVINCIA', 30],
            'G' => ['DISTRITO', 30],
            'H' => ['DIRECCION / PRINCIPAL / SECUNDARIAS', 65],
            'I' => ['CONTACTO', 30],
            'J' => ['TELÉFONO', 20],
            'K' => ['FECHA CUMPLEAÑOS', 15]
        ];

        foreach ($encabezados as $columna => [$titulo, $ancho]) {
            $hojaActiva->getColumnDimension($columna)->setWidth($ancho);
            $hojaActiva->setCellValue("{$columna}1", $titulo);
        }

        // Aplicar formato a las cabeceras
        $hojaActiva->getStyle('A1:K1')->getFont()->setBold(true);
        $hojaActiva->getStyle('A1:K1')->getFont()->getColor()->setARGB(Color::COLOR_BLACK);
        $hojaActiva->getStyle('A1:K1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $hojaActiva->getStyle('A1:K1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $hojaActiva->getStyle('A1:K1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
        $hojaActiva->setAutoFilter('A1:K1');

        // Aplicar alineación centrada a varias columnas
        $columnasCentradas = ['A', 'B', 'C', 'E', 'F', 'G', 'J', 'K'];
        foreach ($columnasCentradas as $col) {
            $hojaActiva->getStyle($col)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Ajustar el texto en ciertas columnas
        $columnasAjuste = ['D', 'E', 'F', 'H', 'I', 'K'];
        foreach ($columnasAjuste as $col) {
            $hojaActiva->getStyle($col)->getAlignment()->setWrapText(true);
        }

        // Agrupar datos y preparar para combinar celdas
        $grupos = [];
        $nro = 1;
        
        foreach ($resultados as $row) {
            $clienteKey = $row['id'];
            $direccionKey = $row['direccion_id'];
            
            if (!isset($grupos[$clienteKey])) {
                $grupos[$clienteKey] = [
                    'nro' => $nro++,
                    'tipo_ruc' => $row['tipo_ruc'],
                    'ruc' => $row['ruc'],
                    'razon_social' => $row['razon_social'],
                    'direcciones' => []
                ];
            }
            
            if (!isset($grupos[$clienteKey]['direcciones'][$direccionKey])) {
                $grupos[$clienteKey]['direcciones'][$direccionKey] = [
                    'departamento' => $row['departamento'],
                    'provincia' => $row['provincia'],
                    'distrito' => $row['distrito'],
                    'direccion' => $row['direccion'],
                    'contactos' => []
                ];
            }
            
            if (!empty($row['contacto']) && $row['contacto'] != '-') {
                $grupos[$clienteKey]['direcciones'][$direccionKey]['contactos'][] = [
                    'nombre' => $row['contacto'],
                    'telefono' => $row['telefono_contacto'],
                    'cumpleanos' => $row['fecha_cumple']
                ];
            }
        }

        // Llenar datos y combinar celdas
        $fila = 2;
        
        foreach ($grupos as $cliente) {
            $filaInicioCliente = $fila;
            
            foreach ($cliente['direcciones'] as $direccion) {
                $filaInicioDireccion = $fila;
                $totalContactos = max(1, count($direccion['contactos']));
                
                // Llenar contactos (o dejar vacío si no hay)
                if (count($direccion['contactos']) > 0) {
                    foreach ($direccion['contactos'] as $contacto) {
                        $hojaActiva->setCellValue('I' . $fila, $contacto['nombre']);
                        $hojaActiva->setCellValue('J' . $fila, $contacto['telefono']);
                        
                        // Formatear fecha de cumpleaños
                        $fechaCumple = $contacto['cumpleanos'];
                        if ($fechaCumple != '-' && $fechaCumple != null) {
                            $hojaActiva->setCellValue('K' . $fila, date('d/m/Y', strtotime($fechaCumple)));
                        } else {
                            $hojaActiva->setCellValue('K' . $fila, '-');
                        }
                        
                        $fila++;
                    }
                } else {
                    $hojaActiva->setCellValue('I' . $fila, '-');
                    $hojaActiva->setCellValue('J' . $fila, '-');
                    $hojaActiva->setCellValue('K' . $fila, '-');
                    $fila++;
                }
                
                // Combinar celdas de dirección (E, F, G, H)
                if ($totalContactos > 1) {
                    $filaFinDireccion = $fila - 1;
                    $hojaActiva->mergeCells("E{$filaInicioDireccion}:E{$filaFinDireccion}");
                    $hojaActiva->mergeCells("F{$filaInicioDireccion}:F{$filaFinDireccion}");
                    $hojaActiva->mergeCells("G{$filaInicioDireccion}:G{$filaFinDireccion}");
                    $hojaActiva->mergeCells("H{$filaInicioDireccion}:H{$filaFinDireccion}");
                }
                
                // Establecer valores de dirección
                $hojaActiva->setCellValue('E' . $filaInicioDireccion, $direccion['departamento']);
                $hojaActiva->setCellValue('F' . $filaInicioDireccion, $direccion['provincia']);
                $hojaActiva->setCellValue('G' . $filaInicioDireccion, $direccion['distrito']);
                $hojaActiva->setCellValue('H' . $filaInicioDireccion, $direccion['direccion']);
            }
            
            // Combinar celdas de cliente (A, B, C, D)
            $filaFinCliente = $fila - 1;
            if ($filaFinCliente > $filaInicioCliente) {
                $hojaActiva->mergeCells("A{$filaInicioCliente}:A{$filaFinCliente}");
                $hojaActiva->mergeCells("B{$filaInicioCliente}:B{$filaFinCliente}");
                $hojaActiva->mergeCells("C{$filaInicioCliente}:C{$filaFinCliente}");
                $hojaActiva->mergeCells("D{$filaInicioCliente}:D{$filaFinCliente}");
            }
            
            // Establecer valores de cliente
            $hojaActiva->setCellValue('A' . $filaInicioCliente, $cliente['nro']);
            $hojaActiva->setCellValue('B' . $filaInicioCliente, $cliente['tipo_ruc']);
            $hojaActiva->setCellValue('C' . $filaInicioCliente, $cliente['ruc']);
            $hojaActiva->setCellValue('D' . $filaInicioCliente, $cliente['razon_social']);
        }

        // Aplicar alineación vertical centrada a todas las celdas combinadas
        $hojaActiva->getStyle("A2:K" . ($fila - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Aplicar bordes a todas las celdas con datos
        $rangoConDatos = "A1:K" . ($fila - 1);
        $styleBordes = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ];
        $hojaActiva->getStyle($rangoConDatos)->applyFromArray($styleBordes);

        // Configurar la descarga del archivo
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="clientes.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function generarExcelEquipos() {
        require '../vendor/autoload.php';

        $conectar = parent::conexion();
        parent::set_names();
        $sql="SELECT
                e.id,
                e.marca,
                e.modelo,
                IFNULL(e.numero_serie, '-') numero_serie,
                CASE e.categoria_equipo
                    WHEN 'impresora' THEN 'IMPRESORA'
                    WHEN 'monitor' THEN 'MONITOR'
                    WHEN 'cpu' THEN 'CPU'
                    WHEN 'laptop' THEN 'LAPTOP'
                    WHEN 'accesorios' THEN 'ACCESORIOS'
                END categoria_equipo,
                CASE e.tipo_equipo
                    WHEN 'bn' THEN 'BLANCO/NEGRO'
                    WHEN 'color' THEN 'COLOR'
                    ELSE '-'
                END tipo_equipo,
                UPPER(e.condicion) condicion,
                CASE WHEN p.tipo_ruc = 1
                    THEN p.razon_social
                    ELSE CONCAT(p.nombre_proveedor, ' ', p.apellido_paterno, ' ', p.apellido_materno)
                END proveedor,
                e.fecha_compra,
                e.costo_dolares,
                e.costo_soles,
                IFNULL(e.contador_inicial_bn, '-') contador_inicial_bn,
                IFNULL(e.contador_inicial_color, '-') contador_inicial_color,
                UPPER(e.estado) estado,
                IFNULL(ca_vigente.numero_contrato, '-') numero_contrato,
                IFNULL(ce_vigente.cliente, '-') cliente,
                ca_vigente.fecha_inicio,
                ca_vigente.fecha_culminacion,
                (
                    SELECT COUNT(*)
                    FROM mccp_incidencia
                    WHERE equipo_id = e.id AND
                        tipo_incidencia = 'preventivo' AND
                        estado = 'resuelto'
                ) total_preventivo,
                (
                    SELECT COUNT(*)
                    FROM mccp_incidencia
                    WHERE equipo_id = e.id AND
                        tipo_incidencia = 'correctivo' AND
                        estado = 'resuelto'
                ) total_correctivo,
                IFNULL((
                    SELECT contador_final_bn
                    FROM mccp_incidencia
                    WHERE equipo_id = e.id AND
                        estado = 'resuelto'
                    ORDER BY id DESC
                    LIMIT 1
                ), '-') ultimo_contador_bn,
                IFNULL((
                    SELECT contador_final_color
                    FROM mccp_incidencia
                    WHERE equipo_id = e.id AND
                        estado = 'resuelto'
                    ORDER BY id DESC
                    LIMIT 1
                ), '-') ultimo_contador_color
            FROM mccp_equipo e
            JOIN mccp_proveedor p ON p.id = e.proveedor_id
            LEFT JOIN (
                SELECT ce.equipo_id, ce.contrato_id, 
                    CASE WHEN c.tipo_ruc = 1
                        THEN c.razon_social
                        ELSE CONCAT(c.nombre_cliente, ' ', c.apellido_paterno, ' ', c.apellido_materno)
                    END cliente
                FROM mccp_contrato_equipo ce
                JOIN mccp_contrato_alquiler ca ON ca.id = ce.contrato_id
                JOIN mccp_cliente c ON c.id = ca.cliente_id
                WHERE ca.estado = 'vigente' AND
                    ce.estado = 'vigente' AND
                    ce.id = (
                        SELECT MAX(ce2.id)
                        FROM mccp_contrato_equipo ce2
                        JOIN mccp_contrato_alquiler ca2 ON ca2.id = ce2.contrato_id
                        WHERE ce2.equipo_id = ce.equipo_id AND
                            ce2.estado = 'vigente' AND
                            ca2.estado = 'vigente'
                    )
            ) ce_vigente ON ce_vigente.equipo_id = e.id
            LEFT JOIN mccp_contrato_alquiler ca_vigente ON ca_vigente.id = ce_vigente.contrato_id";

        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $spreadsheet = new Spreadsheet();
        $hojaActiva = $spreadsheet->getActiveSheet();
        $hojaActiva->setTitle("Equipos");

        // Configurar encabezados
        $encabezados = [
            'A' => ['NRO.', 15],
            'B' => ['CATEGORÍA', 20],
            'C' => ['MARCA', 20],
            'D' => ['MODELO', 20],
            'E' => ['N° SERIE', 15],
            'F' => ['TIPO DE IMPRESIÓN', 20],
            'G' => ['CONDICIÓN', 20],
            'H' => ['PROVEEDOR', 50],
            'I' => ['FECHA COMPRA', 12],
            'J' => ['COSTO $', 12],
            'K' => ['COSTO S/', 12],
            'L' => ['CONTADOR INICIAL BN', 12],
            'M' => ['CONTADOR INICIAL COLOR', 12],
            'N' => ['ESTADO', 12],
            'O' => ['CONTRATO', 15],
            'P' => ['CLIENTE', 60],
            'Q' => ['FECHA INICIO', 12],
            'R' => ['FECHA FIN', 12],
            'S' => ['CANTIDAD INCIDENCIAS PREVENTIVAS', 13],
            'T' => ['CANTIDAD INCIDENCIAS CORRECTIVAS', 13],
            'U' => ['CONTADOR FINAL BN', 13],
            'V' => ['CONTADOR FINAL COLOR', 13],
        ];

        foreach ($encabezados as $columna => [$titulo, $ancho]) {
            $hojaActiva->getColumnDimension($columna)->setWidth($ancho);
            $hojaActiva->setCellValue("{$columna}1", $titulo);
        }

        // Aplicar formato a las cabeceras
        $hojaActiva->getStyle('A1:V1')->getFont()->setBold(true);
        $hojaActiva->getStyle('A1:V1')->getFont()->getColor()->setARGB(Color::COLOR_BLACK);
        $hojaActiva->getStyle('A1:V1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $hojaActiva->getStyle('A1:V1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $hojaActiva->getStyle('A1:V1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00'); // Amarillo
        $hojaActiva->setAutoFilter('A1:V1');

        // Aplicar alineación centrada a varias columnas
        $columnasCentradas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'Q', 'R', 'S', 'T', 'U', 'V'];
        foreach ($columnasCentradas as $col) {
            $hojaActiva->getStyle($col)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Ajustar el texto en ciertas columnas
        $columnasAjuste = ['H', 'I', 'L', 'M', 'P', 'Q', 'R', 'S', 'T', 'U', 'V'];
        foreach ($columnasAjuste as $col) {
            $hojaActiva->getStyle($col)->getAlignment()->setWrapText(true);
        }

        // Llenar datos y combinar celdas
        $fila = 2;
        $nro = 1;

        foreach ($resultados as $row) {
            $hojaActiva->setCellValue('A' . $fila, $nro++);
            $hojaActiva->setCellValue('B' . $fila, $row['categoria_equipo']);
            $hojaActiva->setCellValue('C' . $fila, $row['marca']);
            $hojaActiva->setCellValue('D' . $fila, $row['modelo']);
            $hojaActiva->setCellValue('E' . $fila, $row['numero_serie']);
            $hojaActiva->setCellValue('F' . $fila, $row['tipo_equipo']);
            $hojaActiva->setCellValue('G' . $fila, $row['condicion']);
            $hojaActiva->setCellValue('H' . $fila, $row['proveedor']);
            $hojaActiva->setCellValue('I' . $fila, date('d/m/Y', strtotime($row['fecha_compra'])));
            $hojaActiva->setCellValue('J' . $fila, $row['costo_dolares']);
            $hojaActiva->setCellValue('K' . $fila, $row['costo_soles']);
            $hojaActiva->setCellValue('L' . $fila, $row['contador_inicial_bn']);
            $hojaActiva->setCellValue('M' . $fila, $row['contador_inicial_color']);
            $hojaActiva->setCellValue('N' . $fila, $row['estado']);
            $hojaActiva->setCellValue('O' . $fila, $row['numero_contrato']);
            $hojaActiva->setCellValue('P' . $fila, $row['cliente']);
            $hojaActiva->setCellValue('Q' . $fila, $row['fecha_inicio'] ? date('d/m/Y', strtotime($row['fecha_inicio'])) : '-');
            $hojaActiva->setCellValue('R' . $fila, $row['fecha_culminacion'] ? date('d/m/Y', strtotime($row['fecha_culminacion'])) : '-');
            $hojaActiva->setCellValue('S' . $fila, $row['total_preventivo']);
            $hojaActiva->setCellValue('T' . $fila, $row['total_correctivo']);
            $hojaActiva->setCellValue('U' . $fila, $row['ultimo_contador_bn']);
            $hojaActiva->setCellValue('V' . $fila, $row['ultimo_contador_color']);

            $fila++;
        }

        // Después de llenar todos los datos, aplica el formato al rango completo
        $ultimaFila = $fila - 1;

        // Formato para dólares (columna J)
        $hojaActiva->getStyle('J2:J' . $ultimaFila)->getNumberFormat()->setFormatCode('[$$] #,##0.00');

        // Formato para soles (columna K)
        $hojaActiva->getStyle('K2:K' . $ultimaFila)->getNumberFormat()->setFormatCode('[$S/-es-PE] #,##0.00');

        // Aplicar bordes a todas las celdas con datos
        $rangoConDatos = "A1:V" . ($fila - 1);
        $styleBordes = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ];
        $hojaActiva->getStyle($rangoConDatos)->applyFromArray($styleBordes);

        // Configurar la descarga del archivo
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="equipos.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function generarExcelContratos($estados = []) {
        require '../vendor/autoload.php';

        $conectar = parent::conexion();
        parent::set_names();

        // Construir la cláusula WHERE para filtrar por estados
        $whereEstados = "";
        if (!empty($estados)) {
            $placeholders = str_repeat('?,', count($estados) - 1) . '?';
            $whereEstados = " WHERE ce.estado IN ($placeholders)";
        }
    
        $sql="SELECT
                ca.id,
                ca.numero_contrato,
                ca.fecha_inicio,
                ca.fecha_culminacion,
                ca.cliente_id,
                CASE WHEN c.tipo_ruc = 1
                    THEN c.razon_social
                    ELSE CONCAT(c.nombre_cliente, ' ', c.apellido_paterno, ' ', c.apellido_materno)
                END cliente,
                ca.tecnico_id,
                CASE WHEN ca.tecnico_id IS NOT NULL
                    THEN CONCAT(u.nombres, ' ', u.apellidos)
                    ELSE '-'
                END responsable,
                e.id equipo_id,
                UPPER(e.categoria_equipo) categoria_equipo,
                e.marca,
                e.modelo,
                e.numero_serie,
                CASE WHEN e.tipo_equipo = 'bn' THEN 'BLANCO/NEGRO'
                    WHEN e.tipo_equipo = 'color' THEN 'COLOR'
                    ELSE '-'
                END tipo_equipo,
                UPPER(e.condicion) condicion,
                ce.ip_equipo,
                dc.direccion,
                ce.area_ubicacion,
                ce.created_at fecha_agregado,
                UPPER(ce.mantenimiento_preventivo) mantenimiento_preventivo,
                ce.contador_inicial_bn,
                ce.contador_inicial_color,
                UPPER(ce.estado) estado_equipo,
                IFNULL((
                    SELECT contador_final_bn
                    FROM mccp_incidencia
                    WHERE equipo_id = e.id AND
                        contrato_id = ca.id AND
                        estado = 'resuelto'
                    ORDER BY id DESC
                    LIMIT 1
                ), '-') ultimo_contador_bn,
                IFNULL((
                    SELECT contador_final_color
                    FROM mccp_incidencia
                    WHERE equipo_id = e.id AND
                        contrato_id = ca.id AND
                        estado = 'resuelto'
                    ORDER BY id DESC
                    LIMIT 1
                ), '-') ultimo_contador_color,
                UPPER(ca.estado) estado,
                (
                    SELECT COUNT(*)
                    FROM mccp_incidencia
                    WHERE equipo_id = ce.equipo_id AND
                        contrato_id = ce.contrato_id AND
                        tipo_incidencia = 'preventivo' AND
                        estado = 'resuelto'
                ) total_preventivo,
                (
                    SELECT COUNT(*)
                    FROM mccp_incidencia
                    WHERE equipo_id = ce.equipo_id AND
                        contrato_id = ce.contrato_id AND
                        tipo_incidencia = 'correctivo' AND
                        estado = 'resuelto'
                ) total_correctivo
            FROM mccp_contrato_equipo ce
            JOIN mccp_contrato_alquiler ca ON ce.contrato_id = ca.id 
            JOIN mccp_equipo e ON ce.equipo_id = e.id
            JOIN mccp_cliente c ON ca.cliente_id = c.id
            LEFT JOIN mccp_direccion_cliente dc ON dc.id = ce.direccion_id
            LEFT JOIN mccp_usuario u ON ca.tecnico_id = u.id
            " . $whereEstados . "
            ORDER BY ca.numero_contrato DESC, e.id ASC";
        
        $stmt = $conectar->prepare($sql);
        
        // Vincular los parámetros si hay estados
        if (!empty($estados)) {
            $stmt->execute($estados);
        } else {
            $stmt->execute();
        }
        
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $spreadsheet = new Spreadsheet();
        $hojaActiva = $spreadsheet->getActiveSheet();
        $hojaActiva->setTitle("Contratos");
        
        // Configurar encabezados
        $encabezados = [
            'A' => ['NRO.', 10],
            'B' => ['N° CONTRATO', 15],
            'C' => ['FECHA INICIO', 13],
            'D' => ['FECHA FIN', 13],
            'E' => ['CLIENTE', 50],
            'F' => ['TÉCNICO RESPONSABLE', 30],
            'G' => ['CATEGORÍA', 15],
            'H' => ['MARCA', 20],
            'I' => ['MODELO', 20],
            'J' => ['N° SERIE', 13],
            'K' => ['TIPO', 17],
            'L' => ['CONDICIÓN', 15],
            'M' => ['IP', 15],
            'N' => ['DIRECCIÓN EQUIPO', 60],
            'O' => ['UBICACIÓN', 60],
            'P' => ['FECHA AGREGADO', 13],
            'Q' => ['MANTENIMIENTO PREVENTIVO', 18],
            'R' => ['CONTADOR INICIAL BN', 15],
            'S' => ['CONTADOR INICIAL COLOR', 15],
            'T' => ['CONTADOR FINAL BN', 15],
            'U' => ['CONTADOR FINAL COLOR', 15],
            'V' => ['ESTADO EQUIPO', 15],
            'W' => ['ESTADO CONTRATO', 15],
            'X' => ['CANTIDAD INCIDENCIAS PREVENTIVAS', 13],
            'Y' => ['CANTIDAD INCIDENCIAS CORRECTIVAS', 13]
        ];

        foreach ($encabezados as $columna => [$titulo, $ancho]) {
            $hojaActiva->getColumnDimension($columna)->setWidth($ancho);
            $hojaActiva->setCellValue("{$columna}1", $titulo);
        }
        
        // Aplicar formato a las cabeceras
        $hojaActiva->getStyle('A1:Y1')->getFont()->setBold(true);
        $hojaActiva->getStyle('A1:Y1')->getFont()->getColor()->setARGB(Color::COLOR_BLACK);
        $hojaActiva->getStyle('A1:Y1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $hojaActiva->getStyle('A1:Y1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $hojaActiva->getStyle('A1:Y1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
        $hojaActiva->setAutoFilter('A1:Y1');
        
        // Aplicar alineación centrada a varias columnas
        $columnasCentradas = ['A', 'B', 'C', 'D', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y'];
        foreach ($columnasCentradas as $col) {
            $hojaActiva->getStyle($col)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
        
        // Ajustar el texto en ciertas columnas
        $columnasAjuste = ['C', 'D', 'E', 'F', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y'];
        foreach ($columnasAjuste as $col) {
            $hojaActiva->getStyle($col)->getAlignment()->setWrapText(true);
        }
        
        // Agrupar datos por contrato
        $grupos = [];
        $nro = 1;
        
        foreach ($resultados as $row) {
            $contratoId = $row['id'];
            
            if (!isset($grupos[$contratoId])) {
                $grupos[$contratoId] = [
                    'nro' => $nro++,
                    'numero_contrato' => $row['numero_contrato'],
                    'fecha_inicio' => $row['fecha_inicio'],
                    'fecha_culminacion' => $row['fecha_culminacion'],
                    'cliente' => $row['cliente'],
                    'responsable' => $row['responsable'],
                    'estado' => $row['estado'],
                    'equipos' => []
                ];
            }
            
            $grupos[$contratoId]['equipos'][] = [
                'categoria_equipo' => $row['categoria_equipo'],
                'marca' => $row['marca'],
                'modelo' => $row['modelo'],
                'numero_serie' => $row['numero_serie'],
                'tipo_equipo' => $row['tipo_equipo'],
                'condicion' => $row['condicion'],
                'ip_equipo' => $row['ip_equipo'],
                'direccion' => $row['direccion'],
                'area_ubicacion' => $row['area_ubicacion'],
                'fecha_agregado' => $row['fecha_agregado'],
                'mantenimiento_preventivo' => $row['mantenimiento_preventivo'],
                'contador_inicial_bn' => $row['contador_inicial_bn'],
                'contador_inicial_color' => $row['contador_inicial_color'],
                'ultimo_contador_bn' => $row['ultimo_contador_bn'],
                'ultimo_contador_color' => $row['ultimo_contador_color'],
                'estado_equipo' => $row['estado_equipo'],
                'total_preventivo' => $row['total_preventivo'],
                'total_correctivo' => $row['total_correctivo']
            ];
        }
        
        // Llenar datos y combinar celdas
        $fila = 2;
        
        foreach ($grupos as $contrato) {
            $filaInicioContrato = $fila;
            $totalEquipos = count($contrato['equipos']);
            
            // Llenar equipos
            foreach ($contrato['equipos'] as $equipo) {
                // Los contadores solo aplican a impresoras
                $esImpresora = ($equipo['categoria_equipo'] === 'IMPRESORA');

                $hojaActiva->setCellValue('G' . $fila, $equipo['categoria_equipo']);
                $hojaActiva->setCellValue('H' . $fila, $equipo['marca']);
                $hojaActiva->setCellValue('I' . $fila, $equipo['modelo']);
                $hojaActiva->setCellValue('J' . $fila, $equipo['numero_serie'] ?: '-');
                $hojaActiva->setCellValue('K' . $fila, $equipo['tipo_equipo']);
                $hojaActiva->setCellValue('L' . $fila, $equipo['condicion']);
                $hojaActiva->setCellValue('M' . $fila, $equipo['ip_equipo']);
                $hojaActiva->setCellValue('N' . $fila, $equipo['direccion']);
                $hojaActiva->setCellValue('O' . $fila, $equipo['area_ubicacion']);
                $hojaActiva->setCellValue('P' . $fila, $equipo['fecha_agregado'] ? date('d/m/Y', strtotime($equipo['fecha_agregado'])) : '-');
                $hojaActiva->setCellValue('Q' . $fila, $equipo['mantenimiento_preventivo']);
                $hojaActiva->setCellValue('R' . $fila, $esImpresora ? $equipo['contador_inicial_bn'] : '-');
                $hojaActiva->setCellValue('S' . $fila, $esImpresora ? $equipo['contador_inicial_color'] : '-');
                $hojaActiva->setCellValue('T' . $fila, $esImpresora ? $equipo['ultimo_contador_bn'] : '-');
                $hojaActiva->setCellValue('U' . $fila, $esImpresora ? $equipo['ultimo_contador_color'] : '-');
                $hojaActiva->setCellValue('V' . $fila, $equipo['estado_equipo']);
                $hojaActiva->setCellValue('X' . $fila, $equipo['total_preventivo']);
                $hojaActiva->setCellValue('Y' . $fila, $equipo['total_correctivo']);
                $fila++;
            }
            
            // Combinar celdas del contrato
            $filaFinContrato = $fila - 1;
            if ($filaFinContrato > $filaInicioContrato) {
                $hojaActiva->mergeCells("A{$filaInicioContrato}:A{$filaFinContrato}");
                $hojaActiva->mergeCells("B{$filaInicioContrato}:B{$filaFinContrato}");
                $hojaActiva->mergeCells("C{$filaInicioContrato}:C{$filaFinContrato}");
                $hojaActiva->mergeCells("D{$filaInicioContrato}:D{$filaFinContrato}");
                $hojaActiva->mergeCells("E{$filaInicioContrato}:E{$filaFinContrato}");
                $hojaActiva->mergeCells("F{$filaInicioContrato}:F{$filaFinContrato}");
                $hojaActiva->mergeCells("W{$filaInicioContrato}:W{$filaFinContrato}");
            }
            
            // Establecer valores del contrato
            $hojaActiva->setCellValue('A' . $filaInicioContrato, $contrato['nro']);
            $hojaActiva->setCellValue('B' . $filaInicioContrato, $contrato['numero_contrato']);
            $hojaActiva->setCellValue('C' . $filaInicioContrato, $contrato['fecha_inicio'] ? date('d/m/Y', strtotime($contrato['fecha_inicio'])) : '-');
            $hojaActiva->setCellValue('D' . $filaInicioContrato, $contrato['fecha_culminacion'] ? date('d/m/Y', strtotime($contrato['fecha_culminacion'])) : '-');
            $hojaActiva->setCellValue('E' . $filaInicioContrato, $contrato['cliente']);
            $hojaActiva->setCellValue('F' . $filaInicioContrato, $contrato['responsable']);
            $hojaActiva->setCellValue('W' . $filaInicioContrato, $contrato['estado']);
        }
        
        // Aplicar alineación vertical centrada a todas las celdas
        $hojaActiva->getStyle("A2:Y" . ($fila - 1))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        // Aplicar bordes a todas las celdas con datos
        $rangoConDatos = "A1:Y" . ($fila - 1);
        $styleBordes = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ];
        $hojaActiva->getStyle($rangoConDatos)->applyFromArray($styleBordes);
        
        // Configurar la descarga del archivo
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="contratos.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function generarExcelTickets() {
        require '../vendor/autoload.php';
        
        $conectar = parent::conexion();
        parent::set_names();

        // Aumentar el límite de GROUP_CONCAT para no truncar comentarios largos
        $conectar->exec("SET SESSION group_concat_max_len = 1000000");

        $sql = "SELECT
                i.numero_ticket,
                CASE i.tipo_incidencia
                    WHEN 'correctivo' THEN 'MANTENIMIENTO CORRECTIVO'
                    WHEN 'preventivo' THEN 'MANTENIMIENTO PREVENTIVO'
                    WHEN 'asistencia_preventiva' THEN 'ASISTENCIA PREVENTIVA'
                    WHEN 'recojo_equipos' THEN 'RECOJO DE EQUIPOS'
                END tipo_incidencia,
                i.fecha_incidencia,
                i.fecha_atencion,
                i.cliente_id,
                CASE WHEN c.tipo_ruc = 1
                    THEN c.razon_social
                    ELSE CONCAT(c.nombre_cliente, ' ', c.apellido_paterno, ' ', c.apellido_materno)
                END cliente,
                i.contrato_id,
                ca.numero_contrato,
                i.equipo_id,
                UPPER(e.categoria_equipo) categoria_equipo,
                e.marca,
                e.modelo,
                CASE WHEN e.tipo_equipo = 'bn'
                    THEN 'BLANCO/NEGRO'
                    WHEN e.tipo_equipo = 'color'
                    THEN 'COLOR'
                    ELSE '-'
                END tipo_equipo,
                e.numero_serie,
                ce.area_ubicacion,
                ce.ip_equipo,
                ce.direccion_id,
                dc.direccion,
                CASE WHEN e.categoria_equipo = 'impresora'
                    THEN IFNULL(ce.contador_inicial_bn, '-')
                    ELSE '-'
                END contador_inicial_bn,
                CASE WHEN e.categoria_equipo = 'impresora'
                    THEN IFNULL(ce.contador_inicial_color, '-')
                    ELSE '-'
                END contador_inicial_color,
                CASE WHEN e.categoria_equipo = 'impresora'
                    THEN IFNULL(i.contador_final_bn, '-')
                    ELSE '-'
                END contador_final_bn,
                CASE WHEN e.categoria_equipo = 'impresora'
                    THEN IFNULL(i.contador_final_color, '-')
                    ELSE '-'
                END contador_final_color,
                UPPER(i.estado) estado,
                i.tecnico_id,
                CASE WHEN i.tecnico_id IS NOT NULL
                    THEN CONCAT(u.nombres, ' ', u.apellidos)
                    ELSE '-'
                END tecnico,
                i.tecnico_id_fin,
                CASE WHEN i.tecnico_id_fin IS NOT NULL
                    THEN CONCAT(uf.nombres, ' ', uf.apellidos)
                    ELSE '-'
                END tecnico_fin,
                IFNULL(com.informe, '-') informe,
                i.tiempo_atencion
            FROM mccp_incidencia i
            JOIN mccp_cliente c ON c.id = i.cliente_id
            JOIN mccp_contrato_alquiler ca ON ca.id = i.contrato_id
            LEFT JOIN mccp_contrato_equipo ce ON ce.contrato_id = ca.id AND ce.equipo_id = i.equipo_id AND ce.id = i.contrato_equipo_id
            LEFT JOIN mccp_direccion_cliente dc ON dc.id = ce.direccion_id
            LEFT JOIN mccp_equipo e ON e.id = i.equipo_id
            LEFT JOIN mccp_usuario u ON i.tecnico_id = u.id
            LEFT JOIN mccp_usuario uf ON i.tecnico_id_fin = uf.id
            LEFT JOIN (
                SELECT 
                    incidencia_id,
                    GROUP_CONCAT(
                        CASE 
                            WHEN texto = '' THEN NULL
                            WHEN RIGHT(texto, 1) = '.' THEN texto
                            ELSE CONCAT(texto, '.')
                        END
                        ORDER BY ord ASC SEPARATOR '\n'
                    ) informe
                FROM (
                    SELECT 
                        mic.incidencia_id,
                        mic.id AS ord,
                        REGEXP_REPLACE(
                            SUBSTRING_INDEX(mic.comentario, '<b>', 1), 
                            '^[[:space:]]+|[[:space:]]+$', 
                            ''
                        ) AS texto
                    FROM mccp_incidencia_comentario mic
                ) t
                GROUP BY incidencia_id
            ) com ON com.incidencia_id = i.id
            ORDER BY i.numero_ticket DESC";


        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $spreadsheet = new Spreadsheet();
        $hojaActiva = $spreadsheet->getActiveSheet();
        $hojaActiva->setTitle("Tickets");

        // Configurar encabezados
        $encabezados = [
            'A' => ['NRO.', 10],
            'B' => ['N° TICKET', 15],
            'C' => ['TIPO', 30],
            'D' => ['FECHA Y HORA', 20],
            'E' => ['CLIENTE', 50],
            'F' => ['CONTRATO', 15],
            'G' => ['CATEGORÍA', 15],
            'H' => ['MARCA', 20],
            'I' => ['MODELO', 20],
            'J' => ['TIPO', 17],
            'K' => ['N° SERIE', 13],
            'L' => ['AREA/UBICACIÓN', 20],
            'M' => ['IP', 15],
            'N' => ['DIRECCIÓN EQUIPO', 60],
            'O' => ['CONTADOR INICIAL BN', 15],
            'P' => ['CONTADOR INICIAL COLOR', 15],
            'Q' => ['CONTADOR FINAL BN', 15],
            'R' => ['CONTADOR FINAL COLOR', 15],
            'S' => ['ESTADO', 15],
            'T' => ['TECNICO', 30],
            'U' => ['RESUELTO POR', 30],
            'V' => ['FECHA Y HORA CIERRE', 20],
            'W' => ['TIEMP ATENCIÓN', 20],
            'X' => ['INFORME', 80]
        ];
        
        foreach ($encabezados as $columna => [$titulo, $ancho]) {
            $hojaActiva->getColumnDimension($columna)->setWidth($ancho);
            $hojaActiva->setCellValue("{$columna}1", $titulo);
        }
        
        // Aplicar formato a las cabeceras
        $hojaActiva->getStyle('A1:X1')->getFont()->setBold(true);
        $hojaActiva->getStyle('A1:X1')->getFont()->getColor()->setARGB(Color::COLOR_BLACK);
        $hojaActiva->getStyle('A1:X1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $hojaActiva->getStyle('A1:X1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $hojaActiva->getStyle('A1:X1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
        $hojaActiva->setAutoFilter('A1:X1');
        
        // Aplicar alineación centrada a varias columnas
        $columnasCentradas = ['A', 'B', 'C', 'D', 'F', 'G', 'H', 'I', 'J', 'K', 'M', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'];
        foreach ($columnasCentradas as $col) {
            $hojaActiva->getStyle($col)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
        
        // Ajustar el texto en ciertas columnas
        $columnasAjuste = ['E', 'L', 'N', 'O', 'P', 'Q', 'R', 'W', 'X'];
        foreach ($columnasAjuste as $col) {
            $hojaActiva->getStyle($col)->getAlignment()->setWrapText(true);
        }

        // Llenar datos y combinar celdas
        $fila = 2;
        $nro = 1;
        
        foreach ($resultados as $row) {
            $hojaActiva->setCellValue('A' . $fila, $nro++);
            $hojaActiva->setCellValue('B' . $fila, $row['numero_ticket']);
            $hojaActiva->setCellValue('C' . $fila, $row['tipo_incidencia']);
            $hojaActiva->setCellValue('D' . $fila, date('d/m/Y h:i A', strtotime($row['fecha_incidencia'])));
            $hojaActiva->setCellValue('E' . $fila, $row['cliente']);
            $hojaActiva->setCellValue('F' . $fila, $row['numero_contrato']);
            $hojaActiva->setCellValue('G' . $fila, $row['categoria_equipo'] ?: '-');
            $hojaActiva->setCellValue('H' . $fila, $row['marca']);
            $hojaActiva->setCellValue('I' . $fila, $row['modelo']);
            $hojaActiva->setCellValue('J' . $fila, $row['tipo_equipo']);
            $hojaActiva->setCellValue('K' . $fila, $row['numero_serie']);
            $hojaActiva->setCellValue('L' . $fila, $row['area_ubicacion']);
            $hojaActiva->setCellValue('M' . $fila, $row['ip_equipo']);
            $hojaActiva->setCellValue('N' . $fila, $row['direccion']);
            $hojaActiva->setCellValue('O' . $fila, $row['contador_inicial_bn']);
            $hojaActiva->setCellValue('P' . $fila, $row['contador_inicial_color']);
            $hojaActiva->setCellValue('Q' . $fila, $row['contador_final_bn']);
            $hojaActiva->setCellValue('R' . $fila, $row['contador_final_color']);
            $hojaActiva->setCellValue('S' . $fila, $row['estado']);
            $hojaActiva->setCellValue('T' . $fila, $row['tecnico']);
            $hojaActiva->setCellValue('U' . $fila, $row['tecnico_fin']);
            $hojaActiva->setCellValue('X' . $fila, $row['informe']);
            $hojaActiva->setCellValue('V' . $fila, $row['fecha_atencion'] ? date('d/m/Y h:i A', strtotime($row['fecha_atencion'])) : '-');
            
            $decimal = $row['tiempo_atencion']; // horas en decimal

            $dias = floor($decimal / 24);
            $horasRestantes = $decimal - ($dias * 24);
            $horas = floor($horasRestantes);
            $minutos = floor(($horasRestantes - $horas) * 60);
            $formato = sprintf('%d días %02d:%02d', $dias, $horas, $minutos);

            $hojaActiva->setCellValue('W' . $fila, $formato);

            $fila++;
        }

        // Aplicar bordes a todas las celdas con datos
        $rangoConDatos = "A1:X" . ($fila - 1);
        $styleBordes = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ];

        $hojaActiva->getStyle($rangoConDatos)->applyFromArray($styleBordes);

        // Centrar verticalmente todas las celdas con datos
        $hojaActiva->getStyle($rangoConDatos)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Configurar la descarga del archivo
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="tickets.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}

?>