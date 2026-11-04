<?php
require_once("../config/encrypt.php");
require_once("../config/conexion.php");
require_once("../model/Contrato.php");
require_once("../model/Perfil.php");
$contrato = new Contrato();

// Obtener permisos del usuario para el módulo Contratos   
$perfil = new Perfil();
$permisos = $perfil->obtenerPermisosModulo($_SESSION['id'], 'Contratos');

switch($_GET["op"]){

    case "listar":
        $ver_todos = isset($_POST['ver_todos']) ? intval($_POST['ver_todos']) : 0;
        // Verificar sesión
        if (!isset($_SESSION['id'])) {
            echo json_encode([
                "draw" => 1,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            ]);
            exit;
        }
        
        $datos = $contrato->listar($ver_todos);
        $data = Array();
        
        foreach($datos as $row){
            $link = 'contrato-detalle.php?v='.Encryption::encrypt($row['id']);
            $sub_array = array();
            
            $sub_array[] = $row["numero_contrato"];
            $sub_array[] = $row["cliente"];            
            $sub_array[] = date("d/m/Y", strtotime($row['fecha_inicio']));
            $sub_array[] = $row["fecha_culminacion"] ? date("d/m/Y", strtotime($row['fecha_culminacion'])) : '-';
        
            // Columna Estado
            if ($row["estado"] == 'pendiente') {
                $sub_array[] = '<a style="cursor: pointer" onclick="estadoContrato(' . $row["id"] . ', 1)"><div class="text-secondary f-12"><i class="fas fa-circle f-10 m-r-10"></i>PENDIENTE</div></a>';
            } else if ($row["estado"] == 'vigente') {
                $sub_array[] = '<a style="cursor: pointer" onclick="estadoContrato(' . $row["id"] . ', 1)"><div class="text-warning f-12"><i class="fas fa-circle f-10 m-r-10"></i>VIGENTE</div></a>';
            } else if ($row["estado"] == 'finalizado') {
                $sub_array[] = '<a style="cursor: pointer" onclick="estadoContrato(' . $row["id"] . ', 2)"><div class="text-success f-12"><i class="fas fa-circle f-10 m-r-10"></i>FINALIZADO</div></a>';
            } else {
                $sub_array[] = '<a style="cursor: pointer" onclick="estadoContrato(' . $row["id"] . ', 3)"><div class="text-danger f-12"><i class="fas fa-circle f-10 m-r-10"></i>CANCELADO</div></a>';
            }
            
            // Verificar si el contrato está bloqueado por su estado
            $estaBloqueado = ($row['estado'] === 'cancelado' || $row['estado'] === 'finalizado');
            
            // Construir columna de acciones con permisos
            $acciones = '<ul class="list-inline me-auto mb-0">';
            
            // Botón Ver (siempre visible si tiene permiso de ver el módulo)
            if ($permisos['puede_ver'] == 1) {
                $acciones .= '
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Ver detalle">
                        <a class="avtar avtar-xs btn-link-warning btn-pc-default" 
                        style="cursor: pointer" 
                        href="' . $link . '">
                            <i class="ti ti-eye f-18"></i>
                        </a>
                    </li>';
            }
            
            // Botón Editar (solo si tiene permiso Y el contrato no está bloqueado)
            if ($permisos['puede_editar'] == 1) {
                if ($estaBloqueado) {
                    // Mostrar deshabilitado si está bloqueado
                    $acciones .= '
                        <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="No disponible">
                            <a class="avtar avtar-xs btn-link-success btn-pc-default disabled pe-none opacity-50" 
                            style="cursor: not-allowed">
                                <i class="ti ti-edit-circle f-18"></i>
                            </a>
                        </li>';
                } else {
                    // Mostrar activo si no está bloqueado
                    $acciones .= '
                        <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Editar">
                            <a class="avtar avtar-xs btn-link-success btn-pc-default" 
                            style="cursor: pointer"
                            onclick="editarContrato(' . $row["id"] . ')">
                                <i class="ti ti-edit-circle f-18"></i>
                            </a>
                        </li>';
                }
            }
            
            $acciones .= '</ul>';
            $sub_array[] = $acciones;
            
            $data[] = $sub_array;
        }
        
        $results = array(
            "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );
        echo json_encode($results);
        break;

    case "ingresar_editar_contrato":
        try {
            // Capturar datos del formulario
            $contrato_id        = !empty($_POST["contrato_id"]) ? $_POST["contrato_id"] : null;
            $cliente_id         = $_POST['cliente_id'];
            $tecnico_id         = !empty($_POST["tecnico_id"]) ? $_POST["tecnico_id"] : null;
            $fecha_inicio       = $_POST['fecha_inicio'];
            $fecha_culminacion  = !empty($_POST["fecha_culminacion"]) ? $_POST["fecha_culminacion"] : null;

            $observaciones      = !empty($_POST['observaciones']) ? $_POST['observaciones'] : null;

            if ($contrato_id) {
                // Actualizar proveedor existente
                $contrato->actualizarContrato(
                    $contrato_id,
                    $cliente_id,
                    $tecnico_id,
                    $fecha_inicio,
                    $fecha_culminacion,
                    mb_strtoupper($observaciones, 'UTF-8')
                );
                // actualizarproveedor maneja su propio JSON response
            } else {
                $contrato->insertarContrato(
                    $cliente_id,
                    $tecnico_id,
                    $fecha_inicio,
                    $fecha_culminacion,
                    mb_strtoupper($observaciones, 'UTF-8')
                );
            }
            // No hay exit aquí porque los métodos ya enviaron la respuesta
            
        } catch (Exception $e) {
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($jsonData);
        }
        break;

    case "obtener":
        $datos = $contrato->obtener_contrato_por_id($_POST["id"]);
        if (is_array($datos) == true and count($datos) > 0) {
           foreach ($datos as $row) {
                $output["id"]                  = $row["id"];
                $output["numero_contrato"]     = $row["numero_contrato"];
                $output["cliente_id"]          = $row["cliente_id"];
                $output["nombre_cliente"]      = $row["nombre_cliente"];
                $output["tecnico_id"]          = $row["tecnico_id"];
                $output["nombre_tecnico"]      = $row["nombre_tecnico"];
                $output["fecha_inicio"]        = $row['fecha_inicio'];  // Mantener formato YYYY-MM-DD
                $output["fecha_culminacion"]   = $row['fecha_culminacion'];  // Mantener formato YYYY-MM-DD
                $output["estado"]              = $row["estado"];
                $output["observaciones"]       = $row["observaciones"];
            }
            echo json_encode($output);
        }
        break;
    
    case "estado_contrato":
        try {

            $resultado = $contrato->editar_estado_contrato($_POST['id'], $_POST['estado']);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Actualización de estado correcta'
                ]);
            } else {
                throw new Exception('Error al actualizar estado');
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        break;

    case "listar_equipos":
        $contrato_id = $_POST['contrato_id'];
        $datos = $contrato->obtener_equipos_contrato($contrato_id);
        $data = Array();
        
        foreach($datos as $row) {
            $sub_array = array();
            
            // Columna 1: Serie
            $sub_array[] = !empty($row["numero_serie"]) ? '<strong>' . $row["numero_serie"] . '</strong>' : '-';
            
            // Columna 2: Categoría
            $categorias = [
                'impresora'  => 'IMPRESORA',
                'monitor'    => 'MONITOR',
                'cpu'        => 'CPU',
                'laptop'     => 'LAPTOP',
                'accesorios' => 'ACCESORIOS'
            ];
            $badge_categoria = [
                'impresora'  => 'bg-light-primary',
                'monitor'    => 'bg-light-info',
                'cpu'        => 'bg-light-success',
                'laptop'     => 'bg-light-dark',
                'accesorios' => 'bg-light-warning'
            ];
            $sub_array[] = '<span class="badge ' . ($badge_categoria[$row["categoria_equipo"]] ?? 'bg-light-secondary') . ' f-12">' . ($categorias[$row["categoria_equipo"]] ?? '-') . '</span>';

            // Columna 3: Marca/Modelo
            $sub_array[] = $row["marca"] . ' ' . $row["modelo"];
            
            // Columna 3: IP Asignada
            $sub_array[] = !empty($row["ip_equipo"]) ? $row["ip_equipo"] : '-';
            
            // Columna 4: Ubicación
            $sub_array[] = !empty($row["area_ubicacion"]) ? $row["area_ubicacion"] : '-';
            
            // Columna 5: Fecha en que se agregó el equipo al contrato
            $sub_array[] = !empty($row["fecha_agregado"]) ? date('d/m/Y', strtotime($row["fecha_agregado"])) : '-';

            // Columna 6: Mantenimiento preventivo
            $sub_array[] = ucfirst($row["mantenimiento_preventivo"]);

            // Columnas 7 y 8: Contadores (solo aplican a impresoras)
            $esImpresora = ($row["categoria_equipo"] === 'impresora');
            $sub_array[] = $esImpresora ? $row["contador_inicial_bn"] : '-';
            $sub_array[] = $esImpresora ? $row["contador_inicial_color"] : '-';

            // Columna 9: Estado
            $estadoClass = $row['estado'] === 'vigente' ? 'warning' : 
                        ($row['estado'] === 'finalizado' ? 'success' : 'danger');
            $sub_array[] = '<span class="badge bg-light-' . $estadoClass . ' f-12">' . 
                        strtoupper($row["estado"]) . '</span>';
            
            // Columna 10: Acciones con permisos y estados
            $estaBloqueado = ($row['estado'] === 'cancelado' || $row['estado'] === 'finalizado');

            // Verificar permisos del usuario
            $puedeEditar = ($permisos['puede_editar'] == 1);
            $puedeEliminar = ($permisos['puede_eliminar'] == 1);

            $acciones = '<ul class="list-inline mb-0">';

            // ========== BOTÓN EDITAR - SIEMPRE VISIBLE ==========
            // Determinar si debe estar deshabilitado (por falta de permiso O por estado bloqueado)
            $editarDeshabilitado = !$puedeEditar || $estaBloqueado;

            if ($editarDeshabilitado) {
                // Mostrar DESHABILITADO (sin permiso o equipo bloqueado)
                $tooltipEditar = !$puedeEditar ? 'Sin permiso para editar' : 'No disponible';
                $acciones .= '
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="' . $tooltipEditar . '">
                        <a class="avtar avtar-xs btn-link-warning btn-pc-default disabled" 
                        style="cursor: not-allowed; opacity: 0.5;">
                            <i class="ti ti-edit-circle f-18"></i>
                        </a>
                    </li>';
            } else {
                // Mostrar ACTIVO (tiene permiso y equipo NO bloqueado)
                $acciones .= '
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Editar">
                        <a class="avtar avtar-xs btn-link-warning btn-pc-default" 
                        style="cursor: pointer"
                        onclick="editarEquipo(' . $row["contrato_equipo_id"] . ')">
                            <i class="ti ti-edit-circle f-18"></i>
                        </a>
                    </li>';
            }

            // ========== BOTÓN ELIMINAR - SIEMPRE VISIBLE ==========
            // Determinar si debe estar deshabilitado (por falta de permiso O por estado bloqueado)
            $eliminarDeshabilitado = !$puedeEliminar || $estaBloqueado;

            if ($eliminarDeshabilitado) {
                // Mostrar DESHABILITADO (sin permiso o equipo bloqueado)
                $tooltipEliminar = !$puedeEliminar ? 'Sin permiso para eliminar' : 'No disponible';
                $acciones .= '
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="' . $tooltipEliminar . '">
                        <a class="avtar avtar-xs btn-link-danger btn-pc-default disabled" 
                        style="cursor: not-allowed; opacity: 0.5;">
                            <i class="ti ti-trash f-18"></i>
                        </a>
                    </li>';
            } else {
                // Mostrar ACTIVO (tiene permiso y equipo NO bloqueado)
                $acciones .= '
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Eliminar">
                        <a class="avtar avtar-xs btn-link-danger btn-pc-default" 
                        style="cursor: pointer"
                        onclick="eliminarEquipo(' . $row["contrato_equipo_id"] . ')">
                            <i class="ti ti-trash f-18"></i>
                        </a>
                    </li>';
            }

            $acciones .= '</ul>';
            $sub_array[] = $acciones;
            
            $data[] = $sub_array;
        }
        
        $results = array(
            "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );
        echo json_encode($results);
        break;

    case "guardar_equipo":
        try {
            $contrato_equipo_id = !empty($_POST['contrato_equipo_id']) ? $_POST['contrato_equipo_id'] : null;
            $contrato_id = $_POST['contrato_id'];
            $direccion_id = $_POST['direccion_id'];
            $equipo_id = $_POST['equipo_id'];
            $ip_equipo = !empty($_POST['ip_equipo']) ? $_POST['ip_equipo'] : null;
            $area_ubicacion = !empty($_POST['area_ubicacion']) ? $_POST['area_ubicacion'] : null;

            // Mantenimiento preventivo (por defecto: ninguno)
            $frecuencias = ['ninguno', 'mensual', 'bimensual', 'trimestral', 'semestral', 'anual'];
            $mantenimiento_preventivo = (!empty($_POST['mantenimiento_preventivo']) && in_array($_POST['mantenimiento_preventivo'], $frecuencias, true))
                ? $_POST['mantenimiento_preventivo']
                : 'ninguno';

            // Normalizar contadores - convertir vacíos a 0
            $contador_inicial_bn    = (!empty($_POST['contador_inicial_bn']) && $_POST['contador_inicial_bn'] !== '') ? intval($_POST['contador_inicial_bn']) : 0;
            $contador_final_bn      = (!empty($_POST['contador_final_bn']) && $_POST['contador_final_bn'] !== '') ? intval($_POST['contador_final_bn']) : 0;
            $contador_inicial_color = (!empty($_POST['contador_inicial_color']) && $_POST['contador_inicial_color'] !== '') ? intval($_POST['contador_inicial_color']) : 0;
            $contador_final_color   = (!empty($_POST['contador_final_color']) && $_POST['contador_final_color'] !== '') ? intval($_POST['contador_final_color']) : 0;
            
            if ($contrato_equipo_id) {
                $resultado = $contrato->actualizar_equipo_contrato(
                    $contrato_equipo_id,
                    $direccion_id, 
                    $equipo_id, 
                    $ip_equipo, 
                    mb_strtoupper($area_ubicacion, 'UTF-8'),
                    $contador_inicial_bn, 
                    $contador_final_bn,
                    $contador_inicial_color,
                    $contador_final_color,
                    $mantenimiento_preventivo
                );
            } else {
                $resultado = $contrato->insertar_equipo_contrato(
                    $contrato_id, 
                    $direccion_id,
                    $equipo_id,
                    $ip_equipo, 
                    mb_strtoupper($area_ubicacion, 'UTF-8'), 
                    $contador_inicial_bn, 
                    $contador_final_bn,
                    $contador_inicial_color,
                    $contador_final_color,
                    $mantenimiento_preventivo
                );
            }
        } catch (Exception $e) {
            $jsonData['success'] = 0;
            $jsonData['message'] = 'Error: ' . $e->getMessage();
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($jsonData);
        }
        break;

    case "obtener_equipo":
        $id = $_POST['id'];
        $datos = $contrato->obtener_equipo_contrato($id);
        echo json_encode($datos);
        break;

    case "eliminar_equipo":
        try {
            $id = $_POST['id'];            
            $resultado = $contrato->eliminar_equipo_contrato($id);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Equipo eliminado correctamente'
                ]);
            } else {
                throw new Exception('Error al eliminar el equipo');
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        break;

    case "combo_contratos_cliente":
        $cliente_id = $_POST['cliente_id'];
        $contrato_id_actual = !empty($_POST['contrato_id_actual']) ? $_POST['contrato_id_actual'] : null;
        
        $datos = $contrato->get_contratos_por_cliente($cliente_id, $contrato_id_actual);
        
        if(is_array($datos) && count($datos) > 0){
            $html = "<option value=''>-- Seleccionar --</option>";
            foreach($datos as $row){
                $selected = ($contrato_id_actual && $row['id'] == $contrato_id_actual) ? 'selected' : '';
                // Agregar data-tecnico para almacenar el tecnico_id
                $tecnico_id = $row['tecnico_id'] ? $row['tecnico_id'] : '';
                $html .= "<option value='".$row['id']."' data-tecnico='".$tecnico_id."' ".$selected.">".$row['numero_contrato']."</option>";
            }
            echo $html;
        } else {
            echo "<option value=''>No hay contratos disponibles</option>";
        }
        break;

    case "combo_equipos_contrato":
        $contrato_id = $_POST['contrato_id'];
        $equipo_id_actual = !empty($_POST['equipo_id_actual']) ? $_POST['equipo_id_actual'] : null;
        
        $datos = $contrato->get_equipos_por_contrato($contrato_id, $equipo_id_actual);
        
        if(is_array($datos) && count($datos) > 0){
            $html = "<option value=''>-- Seleccionar --</option>";
            foreach($datos as $row){
                $selected = ($equipo_id_actual && $row['equipo_id'] == $equipo_id_actual) ? 'selected' : '';
                $equipoInfo = $row['marca']." ".$row['modelo'].(!empty($row['numero_serie']) ? " - ".$row['numero_serie'] : "");
                $html .= "<option value='".$row['equipo_id']."' data-categoria='".$row['categoria_equipo']."' data-tipo='".$row['tipo_equipo']."' ".$selected.">".$equipoInfo."</option>";
            }
            echo $html;
        } else {
            echo "<option value=''>No hay equipos disponibles</option>";
        }
        break;

    case "listar_archivos":
        $contrato_id = $_POST['contrato_id'];
        $datos = $contrato->obtener_archivos_contrato($contrato_id);
        $data = array();

        // Mapa de tipos MIME a etiquetas visuales con color
        $tipos_badge = [
            // PDF
            'application/pdf'   => ['label' => 'PDF',   'class' => 'bg-danger'],
            
            // Imágenes
            'image/jpeg'        => ['label' => 'JPG',   'class' => 'bg-success'],
            'image/jpg'         => ['label' => 'JPG',   'class' => 'bg-success'],
            'image/png'         => ['label' => 'PNG',   'class' => 'bg-success'],
            'image/gif'         => ['label' => 'GIF',   'class' => 'bg-success'],
            'image/webp'        => ['label' => 'WEBP',  'class' => 'bg-success'],
            'image/bmp'         => ['label' => 'BMP',   'class' => 'bg-success'],
            'image/svg+xml'     => ['label' => 'SVG',   'class' => 'bg-success'],
            'image/tiff'        => ['label' => 'TIFF',  'class' => 'bg-success'],

            // Word
            'application/msword' => ['label' => 'DOC',  'class' => 'bg-info'],
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' 
                                => ['label' => 'DOCX', 'class' => 'bg-info'],

            // Excel
            'application/vnd.ms-excel' => ['label' => 'XLS', 'class' => 'bg-success'],
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' 
                                    => ['label' => 'XLSX', 'class' => 'bg-success'],

            // PowerPoint
            'application/vnd.ms-powerpoint' => ['label' => 'PPT', 'class' => 'bg-warning'],
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' 
                                            => ['label' => 'PPTX', 'class' => 'bg-warning'],
        ];

        // Mapa de iconos por tipo
        $tipos_icono = [
            'application/pdf'    => 'ti ti-file-type-pdf text-danger',
            'application/msword' => 'ti ti-file-type-doc text-primary',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'ti ti-file-type-doc text-primary',
            'application/vnd.ms-excel' => 'ti ti-file-type-xls text-success',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'ti ti-file-type-xls text-success',
            'application/vnd.ms-powerpoint' => 'ti ti-file-type-ppt text-warning',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'ti ti-file-type-ppt text-warning',
        ];

        foreach ($datos as $row) {
            $sub_array = array();
            $tipo = $row['tipo_archivo'];

            // Ícono según tipo (imágenes usan ícono genérico de imagen)
            if (str_starts_with($tipo, 'image/')) {
                $icono = 'ti ti-photo text-success';
            } else {
                $icono = $tipos_icono[$tipo] ?? 'ti ti-file text-muted';
            }

            // Columna 1: nombre con ícono dinámico
            $url_archivo = $row['ruta_archivo'];
            $sub_array[] = '<a href="../../' . $url_archivo . '" target="_blank" class="fw-bold text-dark text-decoration-none">
                                <i class="' . $icono . ' me-1"></i>' . htmlspecialchars($row['nombre_archivo']) . '
                            </a>';

            // Columna 2: badge
            $badge = isset($tipos_badge[$tipo])
                        ? $tipos_badge[$tipo]
                        : ['label' => strtoupper(explode('/', $tipo)[1] ?? $tipo), 'class' => 'bg-secondary'];

            $sub_array[] = '<span class="badge ' . $badge['class'] . '">' . $badge['label'] . '</span>';

            // Columna 3: fecha
            $sub_array[] = date("d/m/Y", strtotime($row['created_at'])) . '
                            <span class="text-muted text-sm d-block">' . date("h:i A", strtotime($row['created_at'])) . '</span>';

            // Columna 4: acciones
            $sub_array[] = '<ul class="list-inline mb-0">
                                <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Descargar">
                                    <a class="avtar avtar-xs btn-link-warning btn-pc-default" 
                                    style="cursor: pointer"
                                    onclick="descargarArchivo(\'' . $row['ruta_archivo'] . '\')">
                                        <i class="ti ti-download f-18"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Eliminar">
                                    <a class="avtar avtar-xs btn-link-danger btn-pc-default" 
                                    style="cursor: pointer"
                                    onclick="eliminarArchivo(' . $row["contrato_archivo_id"] . ')">
                                        <i class="ti ti-trash f-18"></i>
                                    </a>
                                </li>
                            </ul>';

            $data[] = $sub_array;
        }

        $results = array(
            "draw"            => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
            "recordsTotal"    => count($data),
            "recordsFiltered" => count($data),
            "data"            => $data
        );
        echo json_encode($results);
        break;

    case "eliminar_archivo":
        try {
            $id = $_POST['id'];            
            $resultado = $contrato->eliminar_archivo_contrato($id);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Archivo eliminado correctamente'
                ]);
            } else {
                throw new Exception('Error al eliminar el equipo');
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        break;

    case "guardar_archivo_contrato":
    
        $contrato_id = $_POST['contrato_id'];
        $result = ['success' => 0, 'message' => 'No se procesaron archivos']; // 👈 inicializar siempre

        if (isset($_FILES['imagenes']) && is_array($_FILES['imagenes']['name']) && !empty($_FILES['imagenes']['name'][0])) {
            
            $upload_dirs = [
                'images' => "../assets/contrato/$contrato_id/",
                'pdfs'   => "../assets/contrato/$contrato_id/",
                'docs'   => "../assets/contrato/$contrato_id/",
            ];
            
            foreach ($upload_dirs as $dir) {
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
            }
            
            $tipos_permitidos = [
                // Imágenes
                'image/jpeg'        => ['dir' => 'images', 'max_size' => 5],
                'image/jpg'         => ['dir' => 'images', 'max_size' => 5],
                'image/png'         => ['dir' => 'images', 'max_size' => 5],
                'image/gif'         => ['dir' => 'images', 'max_size' => 5],
                'image/webp'        => ['dir' => 'images', 'max_size' => 5],
                'image/bmp'         => ['dir' => 'images', 'max_size' => 5],
                // PDF
                'application/pdf'   => ['dir' => 'pdfs',   'max_size' => 10],
                // Word
                'application/msword' => ['dir' => 'docs',  'max_size' => 20],
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['dir' => 'docs', 'max_size' => 20],
                // Excel
                'application/vnd.ms-excel' => ['dir' => 'docs', 'max_size' => 20],
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['dir' => 'docs', 'max_size' => 20],
                // PowerPoint
                'application/vnd.ms-powerpoint' => ['dir' => 'docs', 'max_size' => 20],
                'application/vnd.openxmlformats-officedocument.presentationml.presentation' => ['dir' => 'docs', 'max_size' => 20],
            ];
            
            $archivos_subidos = 0;
            $archivos_error   = 0;
            $errores          = [];
            
            foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['imagenes']['error'][$key] == UPLOAD_ERR_OK) {
                    $tipo_archivo    = $_FILES['imagenes']['type'][$key];
                    $nombre_original = $_FILES['imagenes']['name'][$key];
                    $tamanio_archivo = $_FILES['imagenes']['size'][$key];
                    
                    $es_tipo_valido = false;
                    $dir_config     = null;

                    foreach ($tipos_permitidos as $mime => $config) {
                        if ($tipo_archivo === $mime || strpos($tipo_archivo, explode('/', $mime)[0] . '/') === 0) {
                            $es_tipo_valido = true;
                            $dir_config     = $config;
                            break;
                        }
                    }
                    
                    if (!$es_tipo_valido) {
                        $archivos_error++;
                        $errores[] = "$nombre_original: Tipo de archivo no permitido";
                        continue;
                    }
                    
                    $max_size_bytes = $dir_config['max_size'] * 1024 * 1024;
                    if ($tamanio_archivo > $max_size_bytes) {
                        $archivos_error++;
                        $errores[] = "$nombre_original: Supera el tamaño máximo de {$dir_config['max_size']}MB";
                        continue;
                    }
                    
                    $upload_dir = $upload_dirs[$dir_config['dir']];
                    $extension  = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
                    $nombre_unico =  pathinfo($nombre_original, PATHINFO_FILENAME) . '_'  . time() . '_' . $key . '.' . $extension;
                    $ruta_destino  = $upload_dir . $nombre_unico;
                    
                    if (move_uploaded_file($tmp_name, $ruta_destino)) {
                        $ruta_relativa = str_replace('../', '', $upload_dir) . $nombre_unico;
                        
                        if ($contrato->insertar_archivo_contrato($contrato_id, $nombre_original, $ruta_relativa, $tipo_archivo)) {
                            $archivos_subidos++;
                        } else {
                            $archivos_error++;
                            $errores[] = "$nombre_original: Error al guardar en base de datos";
                        }
                    } else {
                        $archivos_error++;
                        $errores[] = "$nombre_original: Error al subir archivo";
                    }

                } else {
                    $archivos_error++;
                    $errores[] = $_FILES['imagenes']['name'][$key] . ": Error de carga (" . $_FILES['imagenes']['error'][$key] . ")";
                }
            }
            
            // 👇 Aquí estaba el problema principal: nunca se asignaba success ni message
            if ($archivos_subidos > 0 && $archivos_error === 0) {
                $result['success'] = 1;
                $result['message'] = "$archivos_subidos archivo(s) guardado(s) correctamente";
            } elseif ($archivos_subidos > 0 && $archivos_error > 0) {
                $result['success'] = 1; // parcialmente exitoso
                $result['message'] = "$archivos_subidos archivo(s) guardado(s), $archivos_error con error";
                $result['errores_detalle'] = $errores;
            } else {
                $result['success'] = 0;
                $result['message'] = "No se pudo guardar ningún archivo";
                $result['errores_detalle'] = $errores;
            }

            $result['archivos_subidos'] = $archivos_subidos;
            $result['archivos_error']   = $archivos_error;

        } else {
            $result['success'] = 0;
            $result['message'] = 'No se enviaron archivos';
        }

        header('Content-type: application/json; charset=utf-8');
        echo json_encode($result);
        exit;

        break;
}