<?php
require_once("../config/encrypt.php");
require_once("../config/conexion.php");
require_once("../model/Ticket.php");
require_once("../model/Perfil.php");
$ticket = new Ticket();

// Obtener permisos del usuario para el módulo tickets   
$perfil = new Perfil();
$permisos = $perfil->obtenerPermisosModulo($_SESSION['id'], 'Tickets');
$es_admin = ($_SESSION['rol_usuario'] == 1);

switch($_GET["op"]){

    case "listar":
        $ver_todos = isset($_POST['ver_todos']) ? intval($_POST['ver_todos']) : 0;
        $datos = $ticket->listar($ver_todos);
        $data = array();
        
        foreach($datos as $row){

            $link = 'ticket-detalle.php?v='.Encryption::encrypt($row['id']);

            $sub_array = array();
            
            $sub_array[] = '<a style="cursor: pointer; color : black;" class="fw-semibold" href="'.$link.'">' . $row["numero_ticket"] . '</a>';
            
            // Columna Estado
            if ($row["estado"] == 'pendiente') {
                $sub_array[] = '<a><div class="text-secondary f-12"><i class="fas fa-circle f-10 m-r-10"></i>PENDIENTE</div></a>';
            } else if ($row["estado"] == 'resuelto') {
                $sub_array[] = '<a><div class="text-success f-12"><i class="fas fa-circle f-10 m-r-10"></i>RESUELTO</div></a>';
            } else {
                $sub_array[] = '<a><div class="text-danger f-12"><i class="fas fa-circle f-10 m-r-10"></i>CANCELADO</div></a>';
            }
            
            if ($row["tipo_incidencia"] == "correctivo") {
                $sub_array[] = '<span class="badge bg-light-info f-12">MANTENIMIENTO CORRECTIVO</span>';
            } else if ($row["tipo_incidencia"] == "preventivo") {
                $sub_array[] = '<span class="badge bg-light-warning f-12">MANTENIMIENTO PREVENTIVO</span>';
            } else if ($row["tipo_incidencia"] == "asistencia_preventiva") {
                $sub_array[] = '<span class="badge bg-light-success f-12">ASISTENCIA PREVENTIVA</span>';
            } else {
                $sub_array[] = '<span class="badge bg-light-secondary f-12">RECOJO DE EQUIPOS</span>';
            }

            $sub_array[] = $row["cliente"];
            
            $sub_array[] = '<a style="cursor: pointer" class="fw-semibold" onclick="verContrato(' . $row["contrato_id"] . ')">' . $row["numero_contrato"] . '</a>';

            if ($row['equipo_id']) {
                // Sin número de serie se identifica el equipo por marca y modelo
                $equipo_texto = !empty($row["numero_serie"]) ? $row["numero_serie"] : trim($row["marca"] . ' ' . $row["modelo"]);
                $sub_array[] = '<a style="cursor: pointer" class="fw-semibold" onclick="verEquipo(' . $row["contrato_equipo_id"] . ')">' . $equipo_texto . '</a>';
            } else {
                $sub_array[] = $row["numero_serie"];
            }

            $sub_array[] = ''. date("d/m/Y", strtotime($row['fecha_incidencia'])).'
                            <span class="text-muted text-sm d-block">'. date("h:i A", strtotime($row['fecha_incidencia'])).'</span>';
            
            if ($row['fecha_atencion']) {
                $sub_array[] = ''. date("d/m/Y", strtotime($row['fecha_atencion'])).'
                            <span class="text-muted text-sm d-block">'. date("h:i A", strtotime($row['fecha_atencion'])).'</span>';
            } else {
                $sub_array[] = '-';
            }

            $sub_array[] = $row['tiempo_atencion'] ? formatearTiempoAtencion($row['tiempo_atencion']) : '-';         

            $sub_array[] = '<div class="d-flex align-items-center">
                                <div class="flex-shrink-0"><img
                                            src="../../assets/images/user/'. $row["foto_perfil"] . '"
                                            alt="user image"
                                            class="img-radius wid-40">
                                </div>
                                <div class="flex-grow-1 ms-1">
                                        <p class="mb-0 fs-6 fw-bold">'. $row["nombres"] . ' ' . $row["apellidos"] .'</p>
                                </div>
                            </div>';

            if ($row["tecnico_id_fin"] != NULL) {
                $sub_array[] = '<div class="d-flex align-items-center">
                                <div class="flex-shrink-0"><img
                                            src="../../assets/images/user/'. $row["foto_perfil_uf"] . '"
                                            alt="user image"
                                            class="img-radius wid-40">
                                </div>
                                <div class="flex-grow-1 ms-1">
                                        <p class="mb-0 fs-6 fw-bold">'. $row["nombres_uf"] . ' ' . $row["apellidos_uf"] .'</p>
                                </div>
                            </div>';
            } else {
                $sub_array[] = '-';
            }
            
            // Cancelado: no se edita. Finalizado: solo lo edita el administrador
            $estaBloqueado = ($row['estado'] === 'cancelado' || ($row['estado'] === 'resuelto' && !$es_admin));
            $tooltipVer = 'Ver detalle';
            $tooltipEditar = $estaBloqueado ? 'No disponible' : 'Editar';
            $claseDisabled = $estaBloqueado ? 'disabled pe-none opacity-50' : '';
            
            // Construir columna de acciones con permisos
            $acciones = '<ul class="list-inline me-auto mb-0">';
            
            // Botón Ver (siempre visible si tiene permiso de ver el módulo)
            if ($permisos['puede_ver'] == 1) {
                $acciones .= '
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="' . $tooltipVer . '">
                        <a class="avtar avtar-xs btn-link-warning btn-pc-default" style="cursor: pointer" href="' . $link . '">
                            <i class="ti ti-eye f-18"></i>
                        </a>
                    </li>';
            }
            
            // Botón Editar (solo si tiene permiso Y el contrato no está bloqueado)
            if ($permisos['puede_editar'] == 1) {
                $acciones .= '<li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="' . $tooltipEditar . '">
                                <a class="avtar avtar-xs btn-link-success btn-pc-default ' . $claseDisabled . '" 
                                style="cursor: ' . ($estaBloqueado ? 'not-allowed' : 'pointer') . '"
                                ' . ($estaBloqueado ? '' : 'onclick="editarTicket(' . $row["id"] . ')"') . '>
                                    <i class="ti ti-edit-circle f-18"></i>
                                </a>
                            </li>';
            }

            // Botón Eliminar (solo administrador, en cualquier estado)
            if ($es_admin) {
                $acciones .= '<li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Eliminar">
                                <a style="cursor: pointer;" class="avtar avtar-xs btn-link-danger btn-pc-default"
                                onclick="eliminarTicket(' . $row["id"] . ')">
                                    <i class="ti ti-trash f-18"></i>
                                </a>
                            </li>';
            }

            $acciones .= '</ul>';
            $sub_array[] = $acciones;            
            
            // AGREGAR ESTADO COMO DATA ATTRIBUTE PARA EL ROW
            $sub_array['DT_RowData'] = array('estado' => $row['estado']);
            
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

    case "ingresar_editar_ticket":
        try {
            // Capturar datos del formulario
            $ticket_id        = !empty($_POST["ticket_id"]) ? $_POST["ticket_id"] : null;
            $tipo_incidencia  = $_POST['tipo_incidencia'];
            $cliente_id       = $_POST['cliente_id'];
            $contrato_id      = $_POST['contrato_id'];
            $equipo_id        = !empty($_POST["equipo_id"]) ? $_POST["equipo_id"] : null;
            $fecha_incidencia     = $_POST['fecha_incidencia'];
            $tecnico_id       = !empty($_POST["tecnico_id"]) ? $_POST["tecnico_id"] : null;

            $descripcion_problema  = !empty($_POST['descripcion_problema']) ? $_POST['descripcion_problema'] : null;

            $fecha_atencion       = !empty($_POST["fecha_atencion"]) ? str_replace('T', ' ', $_POST["fecha_atencion"]) . ':00' : null;
            $contador_final_bn    = !empty($_POST["contador_final_bn"]) ? $_POST["contador_final_bn"] : null;
            $contador_final_color = !empty($_POST["contador_final_color"]) ? $_POST["contador_final_color"] : null;
            $observaciones        = !empty($_POST["observaciones"]) ? $_POST["observaciones"] : null;

            if ($ticket_id) {
                $ticket->actualizarTicket(
                    $ticket_id,
                    $tipo_incidencia,
                    $cliente_id,
                    $contrato_id, 
                    $equipo_id,
                    $fecha_incidencia,
                    $tecnico_id,
                    mb_strtoupper($descripcion_problema, 'UTF-8'),
                    $fecha_atencion,     
                    $contador_final_bn, 
                    $contador_final_color,
                    $observaciones       
                );
            } else {
                $ticket->insertatTicket(
                    $tipo_incidencia,
                    $cliente_id,
                    $contrato_id, 
                    $equipo_id,
                    $fecha_incidencia,
                    $tecnico_id,
                    mb_strtoupper($descripcion_problema, 'UTF-8')
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
        $datos = $ticket->obtener_ticket_por_id($_POST["id"]);
        if (is_array($datos) == true and count($datos) > 0) {
           foreach ($datos as $row) {
                $output["tipo_incidencia"]      = $row["tipo_incidencia"];
                $output["numero_ticket"]        = $row["numero_ticket"];
                $output["cliente_id"]           = $row["cliente_id"];
                $output["nombre_cliente"]       = $row["nombre_cliente"];
                $output["tecnico_id"]           = $row["tecnico_id"];
                $output["nombre_tecnico"]       = $row["nombre_tecnico"];
                $output["contrato_id"]          = $row["contrato_id"];
                $output["numero_contrato"]      = $row["numero_contrato"];
                $output["equipo_id"]            = $row["equipo_id"];
                $output["contrato_equipo_id"]   = $row["contrato_equipo_id"];
                $output["categoria_equipo"]     = $row["categoria_equipo"];
                $output["tipo_equipo"]          = $row["tipo_equipo"];
                $output["numero_serie"]         = $row["numero_serie"];
                $output["marca"]                = $row["marca"];
                $output["modelo"]               = $row["modelo"];
                $output["fecha_incidencia"]     = $row["fecha_incidencia"];
                $output["fecha_atencion"]       = $row["fecha_atencion"];
                $output["estado"]               = $row["estado"];
                $output["descripcion_problema"] = $row["descripcion_problema"];
                $output["observaciones"]        = $row["observaciones"];
                $output["contador_final_bn"]    = $row["contador_final_bn"];
                $output["contador_final_color"] = $row["contador_final_color"];
            }
            echo json_encode($output);
        }
        break;

    case "ver_equipo":
        try {
            $contrato_equipo_id = isset($_POST['contrato_equipo_id']) ? intval($_POST['contrato_equipo_id']) : 0;
            
            if ($contrato_equipo_id <= 0) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Parámetros inválidos'
                ]);
                exit;
            }
            
            $data = $ticket->obtener_info_equipo($contrato_equipo_id);
            
            if ($data) {
                echo json_encode([
                    'status' => 'success',
                    'data' => $data
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No se encontró información del equipo'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        break;

    case "ver_contrato":
        try {
            $contrato_id = isset($_POST['contrato_id']) ? intval($_POST['contrato_id']) : 0;
            
            if ($contrato_id <= 0) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Parámetro inválido'
                ]);
                exit;
            }
            
            $data = $ticket->obtener_info_contrato($contrato_id);
            
            if ($data) {
                echo json_encode([
                    'status' => 'success',
                    'data' => $data
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No se encontró información del contrato'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        break;

    case "listar_comentarios":
        $ticket_id = $_POST["ticket_id"];
        $usuario_actual = $_SESSION['id']; // ID del usuario logueado
        
        $comentarios = $ticket->listar_comentarios($ticket_id);
        
        // Para cada comentario, obtener sus fotos
        foreach ($comentarios as &$comentario) {
            $comentario['fotos'] = $ticket->listar_fotos_comentario($comentario['id']);
            // Agregar flag para saber si puede eliminar
            $comentario['puede_eliminar'] = ($comentario['usuario_id'] == $usuario_actual);
        }
        
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($comentarios);
        break;

    case "gestionar_estado_ticket":
        try {
            $ticket_id = $_POST['fo_ticket_id'];
            $observaciones = $_POST['observaciones'];
            $usuario_id = $_SESSION['id'];
            $accion = $_POST['accion']; // 'cancelar' o 'finalizar'
            
            // Datos adicionales para finalizar
            $contador_bn = isset($_POST['contador_bn']) && !empty($_POST['contador_bn']) ? $_POST['contador_bn'] : null;
            $contador_color = isset($_POST['contador_color']) && !empty($_POST['contador_color']) ? $_POST['contador_color'] : null;
            
            $fecha_finalizacion = !empty($_POST['fecha_finalizacion']) ? str_replace('T', ' ', $_POST['fecha_finalizacion']) . ':00' : null;

            // Validaciones específicas para finalizar
            if ($accion === 'finalizar') {
                // Los contadores solo aplican a impresoras
                $requiere_contadores = $ticket->ticketRequiereContadores($ticket_id);

                if (!$requiere_contadores) {
                    $contador_bn = null;
                    $contador_color = null;
                }

                if ($requiere_contadores && $contador_bn === null) {
                    header('Content-type: application/json; charset=utf-8');
                    echo json_encode([
                        'success' => 0,
                        'message' => 'El contador final BN es requerido'
                    ]);
                    exit;
                }
                // contador_color es opcional, no validamos

                if ($fecha_finalizacion === null || strtotime($fecha_finalizacion) === false) {
                    header('Content-type: application/json; charset=utf-8');
                    echo json_encode([
                        'success' => 0,
                        'message' => 'La fecha y hora de finalización es requerida'
                    ]);
                    exit;
                }

                if (strtotime($fecha_finalizacion) > time()) {
                    header('Content-type: application/json; charset=utf-8');
                    echo json_encode([
                        'success' => 0,
                        'message' => 'La fecha de finalización no puede ser futura'
                    ]);
                    exit;
                }
            }

            // Gestionar el ticket según la acción
            $result = $ticket->gestionarEstadoTicket($ticket_id, $observaciones, $accion, $contador_bn, $contador_color, $fecha_finalizacion);
            
            if ($result['success'] == 1) {
                // Preparar el comentario según el tipo de acción
                if ($accion === 'cancelar') {
                    $comentario = $observaciones;
                    $tipo_comentario = 'accion';
                } else if ($accion === 'finalizar') {
                    $comentario = $observaciones;
                    if ($requiere_contadores) {
                        $comentario .= "\n<b>Contador Final BN: </b>" . $contador_bn . "\n";
                        if ($contador_color !== null) {
                            $comentario .= "<b>Contador Final Color: </b>" . $contador_color;
                        } else {
                            $comentario .= "<b>Contador Final Color: </b> -";
                        }
                    }
                    $tipo_comentario = 'accion';
                }
                
                // Insertar comentario
                $responseComentario = $ticket->insertar_comentario($ticket_id, $usuario_id, $comentario, $tipo_comentario);
                
                if ($responseComentario['success'] == 1) {
                    $comentario_id = $responseComentario['comentario_id'];
                    
                    // Si hay archivos, procesarlos (imágenes, videos, PDFs)
                    if (isset($_FILES['imagenes']) && is_array($_FILES['imagenes']['name']) && !empty($_FILES['imagenes']['name'][0])) {
                        
                        // Directorios según tipo de archivo
                        $upload_dirs = [
                            'images' => '../assets/images/coment/',
                            'videos' => '../assets/videos/coment/',
                            'pdfs' => '../assets/pdfs/coment/'
                        ];
                        
                        // Crear directorios si no existen
                        foreach ($upload_dirs as $dir) {
                            if (!is_dir($dir)) {
                                mkdir($dir, 0777, true);
                            }
                        }
                        
                        // Tipos de archivo permitidos con sus configuraciones
                        $tipos_permitidos = [
                            'image/jpeg' => ['dir' => 'images', 'max_size' => 5],
                            'image/jpg' => ['dir' => 'images', 'max_size' => 5],
                            'image/png' => ['dir' => 'images', 'max_size' => 5],
                            'image/gif' => ['dir' => 'images', 'max_size' => 5],
                            'image/webp' => ['dir' => 'images', 'max_size' => 5],
                            'application/pdf' => ['dir' => 'pdfs', 'max_size' => 10],
                            'video/mp4' => ['dir' => 'videos', 'max_size' => 50],
                            'video/webm' => ['dir' => 'videos', 'max_size' => 50],
                            'video/ogg' => ['dir' => 'videos', 'max_size' => 50],
                            'video/quicktime' => ['dir' => 'videos', 'max_size' => 50]
                        ];
                        
                        $archivos_subidos = 0;
                        $archivos_error = 0;
                        $errores = [];
                        
                        // Procesar cada archivo
                        foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                            if ($_FILES['imagenes']['error'][$key] == UPLOAD_ERR_OK) {
                                $tipo_archivo = $_FILES['imagenes']['type'][$key];
                                $nombre_original = $_FILES['imagenes']['name'][$key];
                                $tamanio_archivo = $_FILES['imagenes']['size'][$key];
                                
                                // Validar tipo de archivo
                                $es_tipo_valido = false;
                                foreach ($tipos_permitidos as $mime => $config) {
                                    if ($tipo_archivo === $mime || strpos($tipo_archivo, explode('/', $mime)[0] . '/') === 0) {
                                        $es_tipo_valido = true;
                                        $dir_config = $config;
                                        break;
                                    }
                                }
                                
                                if (!$es_tipo_valido) {
                                    $archivos_error++;
                                    $errores[] = "$nombre_original: Tipo de archivo no permitido";
                                    continue;
                                }
                                
                                // Validar tamaño
                                $max_size_mb = $dir_config['max_size'];
                                $max_size_bytes = $max_size_mb * 1024 * 1024;
                                
                                if ($tamanio_archivo > $max_size_bytes) {
                                    $archivos_error++;
                                    $errores[] = "$nombre_original: Supera el tamaño máximo de {$max_size_mb}MB";
                                    continue;
                                }
                                
                                // Determinar directorio
                                $upload_dir = $upload_dirs[$dir_config['dir']];
                                
                                // Generar nombre único
                                $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
                                $nombre_unico = uniqid() . '_' . time() . '_' . $key . '.' . $extension;
                                $ruta_destino = $upload_dir . $nombre_unico;
                                
                                // Mover archivo
                                if (move_uploaded_file($tmp_name, $ruta_destino)) {
                                    // Guardar en BD con ruta relativa
                                    $ruta_relativa = str_replace('../', '', $upload_dir) . $nombre_unico;
                                    
                                    if ($ticket->insertar_archivo_comentario($comentario_id, $nombre_original, $ruta_relativa, $tipo_archivo)) {
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
                                $nombre_original = $_FILES['imagenes']['name'][$key];
                                $errores[] = "$nombre_original: Error de carga (" . $_FILES['imagenes']['error'][$key] . ")";
                            }
                        }
                        
                        $result['archivos_subidos'] = $archivos_subidos;
                        $result['archivos_error'] = $archivos_error;
                        
                        if ($archivos_error > 0) {
                            $result['warning'] = "$archivos_error archivo(s) no pudieron ser subidos";
                            $result['errores_detalle'] = $errores;
                        }
                    } else {
                        $result['archivos_subidos'] = 0;
                    }
                    
                    $result['comentario_insertado'] = true;
                } else {
                    $result['comentario_insertado'] = false;
                    $result['comentario_error'] = $responseComentario['message'];
                }
            }
            
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($result);
            exit;
            
        } catch (Exception $e) {
            header('Content-type: application/json; charset=utf-8');
            echo json_encode([
                'success' => 0,
                'message' => 'Error: ' . $e->getMessage()
            ]);
            exit;
        }
        break;

    case "agregar_comentario":
        try {
            $ticket_id = $_POST['ticket_id'];
            $usuario_id = $_SESSION['id'];
            $comentario = $_POST['comentario'];
            $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : 'nota';

            // Cancelado: no admite comentarios. Finalizado: solo el administrador
            $estado_ticket = $ticket->obtener_estado_ticket($ticket_id);
            if ($estado_ticket === false || $estado_ticket === 'cancelado' || ($estado_ticket === 'resuelto' && !$es_admin)) {
                header('Content-type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => 0,
                    'message' => 'No se pueden agregar comentarios a este ticket'
                ]);
                exit;
            }

            // Insertar comentario y obtener respuesta
            $response = $ticket->insertar_comentario($ticket_id, $usuario_id, $comentario, $tipo);
            
            // Si el comentario se insertó correctamente y hay archivos, procesarlos
            if ($response['success'] == 1 && !empty($_FILES['imagenes']['name'][0])) {
                $comentario_id = $response['comentario_id'];
                
                // Directorios según tipo de archivo
                $upload_dirs = [
                    'images' => '../assets/images/coment/',
                    'videos' => '../assets/videos/coment/',
                    'pdfs' => '../assets/pdfs/coment/'
                ];
                
                // Crear directorios si no existen
                foreach ($upload_dirs as $dir) {
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                }
                
                // Tipos de archivo permitidos con sus configuraciones
                $tipos_permitidos = [
                    'image/jpeg' => ['dir' => 'images', 'max_size' => 5],
                    'image/jpg' => ['dir' => 'images', 'max_size' => 5],
                    'image/png' => ['dir' => 'images', 'max_size' => 5],
                    'image/gif' => ['dir' => 'images', 'max_size' => 5],
                    'image/webp' => ['dir' => 'images', 'max_size' => 5],
                    'application/pdf' => ['dir' => 'pdfs', 'max_size' => 10],
                    'video/mp4' => ['dir' => 'videos', 'max_size' => 50],
                    'video/webm' => ['dir' => 'videos', 'max_size' => 50],
                    'video/ogg' => ['dir' => 'videos', 'max_size' => 50],
                    'video/quicktime' => ['dir' => 'videos', 'max_size' => 50]
                ];
                
                $archivos_subidos = 0;
                $archivos_error = 0;
                $errores = [];
                
                // Procesar cada archivo
                foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['imagenes']['error'][$key] == UPLOAD_ERR_OK) {
                        $tipo_archivo = $_FILES['imagenes']['type'][$key];
                        $nombre_original = $_FILES['imagenes']['name'][$key];
                        $tamanio_archivo = $_FILES['imagenes']['size'][$key];
                        
                        // Validar tipo de archivo
                        $es_tipo_valido = false;
                        foreach ($tipos_permitidos as $mime => $config) {
                            if ($tipo_archivo === $mime || strpos($tipo_archivo, explode('/', $mime)[0] . '/') === 0) {
                                $es_tipo_valido = true;
                                $dir_config = $config;
                                break;
                            }
                        }
                        
                        if (!$es_tipo_valido) {
                            $archivos_error++;
                            $errores[] = "$nombre_original: Tipo de archivo no permitido";
                            continue;
                        }
                        
                        // Validar tamaño
                        $max_size_mb = $dir_config['max_size'];
                        $max_size_bytes = $max_size_mb * 1024 * 1024;
                        
                        if ($tamanio_archivo > $max_size_bytes) {
                            $archivos_error++;
                            $errores[] = "$nombre_original: Supera el tamaño máximo de {$max_size_mb}MB";
                            continue;
                        }
                        
                        // Determinar directorio
                        $upload_dir = $upload_dirs[$dir_config['dir']];
                        
                        // Generar nombre único
                        $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
                        $nombre_unico = uniqid() . '_' . time() . '_' . $key . '.' . $extension;
                        $ruta_destino = $upload_dir . $nombre_unico;
                        
                        // Mover archivo
                        if (move_uploaded_file($tmp_name, $ruta_destino)) {
                            // Guardar en BD con ruta relativa
                            $ruta_relativa = str_replace('../', '', $upload_dir) . $nombre_unico;
                            
                            if ($ticket->insertar_archivo_comentario($comentario_id, $nombre_original, $ruta_relativa, $tipo_archivo)) {
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
                        $nombre_original = $_FILES['imagenes']['name'][$key];
                        $errores[] = "$nombre_original: Error de carga (" . $_FILES['imagenes']['error'][$key] . ")";
                    }
                }
                
                $response['archivos_subidos'] = $archivos_subidos;
                $response['archivos_error'] = $archivos_error;
                
                if ($archivos_error > 0) {
                    $response['warning'] = "$archivos_error archivo(s) no pudieron ser subidos";
                    $response['errores_detalle'] = $errores;
                }
            }
            
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($response);
            exit;
            
        } catch (Exception $e) {
            $jsonData = array(
                'success' => 0,
                'message' => 'Error: ' . $e->getMessage()
            );
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($jsonData);
            exit;
        }
        break;

    case "eliminar_comentario":
        try {
            if (!isset($_POST['comentario_id']) || !isset($_POST['ticket_id'])) {
                throw new Exception('Datos incompletos');
            }
            
            $comentario_id = intval($_POST['comentario_id']);
            $ticket_id = intval($_POST['ticket_id']);
            $usuario_id = $_SESSION['id'];
            
            // Cancelado: no se eliminan comentarios. Finalizado: solo el administrador y únicamente los suyos
            $contexto = $ticket->obtener_contexto_comentario($comentario_id);
            if (!$contexto) {
                throw new Exception('El comentario no existe');
            }

            if ($contexto['estado'] === 'cancelado' || ($contexto['estado'] === 'resuelto' && (!$es_admin || $contexto['usuario_id'] != $usuario_id))) {
                header('Content-type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => 0,
                    'message' => 'No se puede eliminar este comentario'
                ]);
                exit;
            }

            // Eliminar el comentario
            $result = $ticket->eliminarComentario($comentario_id);
            
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($result);
            exit;
            
        } catch (Exception $e) {
            header('Content-type: application/json; charset=utf-8');
            echo json_encode([
                'success' => 0,
                'message' => 'Error: ' . $e->getMessage()
            ]);
            exit;
        }
        break;

    case "eliminar_ticket":
        try {
            header('Content-type: application/json; charset=utf-8');

            if (!$es_admin) {
                echo json_encode([
                    'success' => 0,
                    'message' => 'Solo el administrador puede eliminar tickets'
                ]);
                exit;
            }

            if (empty($_POST['ticket_id'])) {
                throw new Exception('Datos incompletos');
            }

            $result = $ticket->eliminarTicket(intval($_POST['ticket_id']));
            echo json_encode($result);
            exit;

        } catch (Exception $e) {
            echo json_encode([
                'success' => 0,
                'message' => 'Error: ' . $e->getMessage()
            ]);
            exit;
        }
        break;
}

function formatearTiempoAtencion($horas) {
    if (!$horas || $horas <= 0) {
        return '-';
    }
    
    $horas_enteras = floor($horas);
    $minutos = round(($horas - $horas_enteras) * 60);
    
    $dias = floor($horas_enteras / 24);
    $horas_restantes = $horas_enteras % 24;
    
    $partes = [];
    
    if ($dias > 0) {
        $partes[] = $dias . 'd';
    }
    if ($horas_restantes > 0) {
        $partes[] = $horas_restantes . 'h';
    }
    if ($minutos > 0 || empty($partes)) {
        $partes[] = $minutos . 'm';
    }
    
    return implode(' ', $partes);
}