<?php
require_once("../config/conexion.php");
require_once("../model/Equipo.php");
require_once("../model/Perfil.php");
$equipo = new Equipo();

// Obtener permisos del usuario para el módulo equipo   
$perfil = new Perfil();
$permisos = $perfil->obtenerPermisosModulo($_SESSION['id'], 'Equipos');

switch($_GET["op"]){

    case "listar":
        $datos = $equipo->listar();
        $data = Array();
        
        foreach($datos as $row){

            $sub_array = array();
            
            $sub_array[] = $row["numero_serie"] ?: '-';
            $sub_array[] = $row["marca"];
            $sub_array[] = $row["modelo"];

            // Columna Categoría (para impresoras se muestra debajo el tipo de impresión)
            $badge_categoria = [
                'impresora'  => 'bg-light-primary',
                'monitor'    => 'bg-light-info',
                'cpu'        => 'bg-light-success',
                'laptop'     => 'bg-light-dark',
                'accesorios' => 'bg-light-warning'
            ];
            $categoria = '<span class="badge ' . ($badge_categoria[$row["categoria_equipo"]] ?? 'bg-light-secondary') . ' f-12">'. $row["nombre_categoria"] .'</span>';

            if ($row["categoria_equipo"] == "impresora" && $row["nombre_tipo"]) {
                $categoria .= '<span class="text-muted text-sm d-block">'. $row["nombre_tipo"] .'</span>';
            }

            $sub_array[] = $categoria;

            if ($row["condicion"] == "nuevo") {
                $sub_array[] = '<span class="badge bg-light-success f-12">'. $row["nombre_condicion"] .'</span>';
            } else {
                $sub_array[] = '<span class="badge bg-light-warning f-12">'. $row["nombre_condicion"] .'</span>';
            }

            $sub_array[] = $row["proveedor"];

            // Columna Archivos: cantidad de archivos adjuntos (abre el modal de archivos)
            $nombre_equipo = htmlspecialchars(trim($row["marca"] . ' ' . $row["modelo"]), ENT_QUOTES, 'UTF-8');
            $clase_archivos = $row["total_archivos"] > 0 ? 'bg-light-primary' : 'bg-light-secondary';
            $sub_array[] = '<a style="cursor: pointer" data-bs-toggle="tooltip" title="Ver archivos" onclick="verArchivos(' . $row["id"] . ', \'' . $nombre_equipo . '\')">
                                <span class="badge ' . $clase_archivos . ' f-12"><i class="ti ti-paperclip me-1"></i>' . $row["total_archivos"] . '</span>
                            </a>';

            // Columna Estado con validación de permisos
            if ($permisos['puede_editar'] == 1) {
                // Columna Estado
                if ($row["estado"] == 'activo') {
                    $sub_array[] = '<a style="cursor: pointer" onclick="estadoEquipo(' . $row["id"] . ', 2)"><div class="text-success f-12"><i class="fas fa-circle f-10 m-r-10"></i>ACTIVO</div></a>';
                } else if ($row["estado"] == 'mantenimiento') {
                    $sub_array[] = '<a><div class="text-info f-12"><i class="fas fa-circle f-10 m-r-10"></i>MANTENIMIENTO</div></a>';
                } else if ($row["estado"] == 'asignado') {
                    $sub_array[] = '<a><div class="text-warning f-12"><i class="fas fa-circle f-10 m-r-10"></i>ASIGNADO</div></a>';
                } else {
                    $sub_array[] = '<a style="cursor: pointer" onclick="estadoEquipo(' . $row["id"] . ', 1)"><div class="text-secondary f-12"><i class="fas fa-circle f-10 m-r-10"></i>INACTIVO</div></a>';
                }
            } else {

                // Columna Estado
                if ($row["estado"] == 'activo') {
                    $sub_array[] = '
                        <div class="text-success f-12" 
                            style="cursor: not-allowed; opacity: 0.6;" 
                            data-bs-toggle="tooltip" 
                            title="Sin permiso para cambiar estado">
                            <i class="fas fa-circle f-10 m-r-10"></i>ACTIVO
                        </div>';
                } else if ($row["estado"] == 'mantenimiento') {
                    $sub_array[] = '<a><div class="text-info f-12"><i class="fas fa-circle f-10 m-r-10"></i>MANTENIMIENTO</div></a>';
                } else if ($row["estado"] == 'asignado') {
                    $sub_array[] = '<a><div class="text-warning f-12"><i class="fas fa-circle f-10 m-r-10"></i>ASIGNADO</div></a>';
                } else {
                    $sub_array[] = '
                        <div class="text-secondary f-12" 
                            style="cursor: not-allowed; opacity: 0.6;" 
                            data-bs-toggle="tooltip" 
                            title="Sin permiso para cambiar estado">
                            <i class="fas fa-circle f-10 m-r-10"></i>INACTIVO
                        </div>';
                }
            }

            if ($permisos['puede_editar'] == 1) {
                $sub_array[] = '<ul class="list-inline me-auto mb-0">
                                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Editar">
                                        <a class="avtar avtar-xs btn-link-success btn-pc-default" style="cursor: pointer"
                                        onclick="editarEquipo(' . $row["id"] . ')">
                                            <i class="ti ti-edit-circle f-18"></i>
                                        </a>
                                    </li>
                                </ul>';  
            } else {
                $sub_array[] = '
                <ul class="list-inline me-auto mb-0">
                    <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Sin permiso para editar">
                        <a class="avtar avtar-xs btn-link-warning btn-pc-default disabled" 
                        style="cursor: not-allowed; opacity: 0.5;">
                            <i class="ti ti-edit-circle f-18"></i>
                        </a>
                    </li>
                </ul>';
            }                      
            
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

    case "ingresar_editar_equipo":
        try {
            // Capturar datos del formulario
            $equipo_id  = !empty($_POST["equipo_id"]) ? $_POST["equipo_id"] : null;
            $modelo         = $_POST['modelo'];
            $marca          = $_POST['marca'];
            // El N° de serie es opcional: vacío se guarda como NULL
            $numero_serie   = isset($_POST['numero_serie']) && trim($_POST['numero_serie']) !== '' ? trim($_POST['numero_serie']) : null;
            $categoria_equipo = isset($_POST['categoria_equipo']) ? $_POST['categoria_equipo'] : '';
            $condicion      = $_POST['condicion'];

            $estado = isset($_POST['estado']) ? $_POST['estado'] : 'activo';

            $proveedor_id           = $_POST["proveedor_id"];

            $fecha_compra           = $_POST["fecha_compra"];
            $costo_dolares          = $_POST["costo_dolares"];
            $costo_soles            = $_POST["costo_soles"];

            $observaciones          = !empty($_POST['observaciones']) ? $_POST['observaciones'] : null;

            if (!in_array($categoria_equipo, ['impresora', 'monitor', 'cpu', 'laptop', 'accesorios'])) {
                header('Content-type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => 0,
                    'message' => 'La categoría del equipo es requerida'
                ]);
                exit;
            }

            // Tipo de impresión y contadores: solo aplican a impresoras
            if ($categoria_equipo === 'impresora') {
                $tipo_equipo            = isset($_POST['tipo_equipo']) ? $_POST['tipo_equipo'] : '';
                $contador_inicial_bn    = isset($_POST['contador_inicial_bn']) && $_POST['contador_inicial_bn'] !== '' ? $_POST['contador_inicial_bn'] : null;
                $contador_actual_bn     = isset($_POST['contador_actual_bn']) && $_POST['contador_actual_bn'] !== '' ? $_POST['contador_actual_bn'] : null;
                $contador_inicial_color = !empty($_POST['contador_inicial_color']) ? $_POST['contador_inicial_color'] : null;
                $contador_actual_color  = !empty($_POST['contador_actual_color']) ? $_POST['contador_actual_color'] : null;

                if (!in_array($tipo_equipo, ['bn', 'color'])) {
                    header('Content-type: application/json; charset=utf-8');
                    echo json_encode([
                        'success' => 0,
                        'message' => 'El tipo de impresión es requerido'
                    ]);
                    exit;
                }

                if ($contador_inicial_bn === null || $contador_actual_bn === null) {
                    header('Content-type: application/json; charset=utf-8');
                    echo json_encode([
                        'success' => 0,
                        'message' => 'Los contadores BN son requeridos'
                    ]);
                    exit;
                }
            } else {
                $tipo_equipo            = null;
                $contador_inicial_bn    = null;
                $contador_actual_bn     = null;
                $contador_inicial_color = null;
                $contador_actual_color  = null;
            }

            // Validar si el número de serie ya existe (solo si se ingresó)
            if ($numero_serie !== null) {
                $equipo_existe = $equipo->verificarEquipoExiste(mb_strtoupper($numero_serie, 'UTF-8'), $equipo_id);
                if ($equipo_existe) {
                    $jsonData['success'] = 0;
                    $jsonData['message'] = 'El equipo ya está registrado';
                    header('Content-type: application/json; charset=utf-8');
                    echo json_encode($jsonData);
                    exit;
                }
            }

            if ($equipo_id) {
                // Actualizar proveedor existente
                $equipo->actualizarEquipo(
                    $equipo_id,
                    mb_strtoupper($marca, 'UTF-8'),
                    mb_strtoupper($modelo, 'UTF-8'),
                    $numero_serie !== null ? mb_strtoupper($numero_serie, 'UTF-8') : null,
                    $categoria_equipo,
                    $tipo_equipo,
                    $condicion,
                    $proveedor_id,
                    $fecha_compra,
                    $costo_dolares,
                    $costo_soles,
                    $contador_inicial_bn,
                    $contador_actual_bn,
                    $contador_inicial_color,
                    $contador_actual_color,
                    $estado,
                    $observaciones
                );
                // actualizarproveedor maneja su propio JSON response
            } else {
                $equipo->insertarEquipo(
                    mb_strtoupper($marca, 'UTF-8'),
                    mb_strtoupper($modelo, 'UTF-8'),
                    $numero_serie !== null ? mb_strtoupper($numero_serie, 'UTF-8') : null,
                    $categoria_equipo,
                    $tipo_equipo,
                    $condicion,
                    $proveedor_id,
                    $fecha_compra,
                    $costo_dolares,
                    $costo_soles,
                    $contador_inicial_bn,
                    $contador_actual_bn,
                    $contador_inicial_color,
                    $contador_actual_color,
                    $estado,
                    $observaciones
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
        $datos = $equipo->obtener_equipo_por_id($_POST["id"]);
        if (is_array($datos) == true and count($datos) > 0) {
           foreach ($datos as $row) {
                $output["id"]                   = $row["id"];
                $output["marca"]                = $row["marca"];
                $output["modelo"]               = $row["modelo"];
                $output["numero_serie"]         = $row["numero_serie"];
                $output["categoria_equipo"]     = $row["categoria_equipo"];
                $output["tipo_equipo"]          = $row["tipo_equipo"];
                $output["condicion"]            = $row["condicion"];
                $output["estado"]               = $row["estado"];
                $output["proveedor_id"]         = $row["proveedor_id"];
                $output["proveedor"]            = $row["proveedor"];
                $output["fecha_compra"]         = $row["fecha_compra"];
                $output["costo_dolares"]        = $row["costo_dolares"];
                $output["costo_soles"]          = $row["costo_soles"];
                $output["contador_inicial_bn"]  = $row["contador_inicial_bn"];
                $output["contador_inicial_color"] = $row["contador_inicial_color"];
                $output["contador_actual_bn"]   = $row["contador_actual_bn"];
                $output["contador_actual_color"]   = $row["contador_actual_color"];
                $output["observaciones"]   = $row["observaciones"];
            }
            echo json_encode($output);
        }
        break;

    case "estado_equipo":
        try {

            $resultado = $equipo->editar_estado_equipo($_POST['equipo_id'], $_POST['estado']);
            
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

    //Combo utilizado en formulario de equipo
    case "combo_equipo":
        // Recibir el cliente_id actual (si existe)
        $equipo_id_actual = !empty($_POST['equipo_id_actual']) ? $_POST['equipo_id_actual'] : null;
        
        // Obtener clientes disponibles
        $categoria_equipo = !empty($_POST['categoria_equipo']) ? $_POST['categoria_equipo'] : null;

        $datos = $equipo->combo_equipo($equipo_id_actual, $categoria_equipo);
        
        if(is_array($datos) == true and count($datos) > 0){
            $html = "<option value=''>-- Seleccionar --</option>";
            foreach($datos as $row){
                $html .= "<option value='".$row['id']."'>".$row['nombre']."</option>";
            }
            echo $html;
        } else {
            echo "<option value=''>-- No hay equipos disponibles --</option>";
        }
        break;

    case "listar_archivos":
        $equipo_id = intval($_POST['equipo_id']);
        $datos = $equipo->obtener_archivos_equipo($equipo_id);
        $data = array();

        // Etiqueta y color del badge según el tipo de archivo
        $tipos_badge = [
            'application/pdf' => ['label' => 'PDF', 'class' => 'bg-danger'],
            'application/msword' => ['label' => 'DOC', 'class' => 'bg-info'],
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['label' => 'DOCX', 'class' => 'bg-info'],
            'application/vnd.ms-excel' => ['label' => 'XLS', 'class' => 'bg-success'],
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['label' => 'XLSX', 'class' => 'bg-success'],
            'application/vnd.ms-powerpoint' => ['label' => 'PPT', 'class' => 'bg-warning'],
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => ['label' => 'PPTX', 'class' => 'bg-warning'],
        ];

        // Ícono según el tipo de archivo
        $tipos_icono = [
            'application/pdf' => 'ti ti-file-type-pdf text-danger',
            'application/msword' => 'ti ti-file-type-doc text-primary',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'ti ti-file-type-doc text-primary',
            'application/vnd.ms-excel' => 'ti ti-file-type-xls text-success',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'ti ti-file-type-xls text-success',
            'application/vnd.ms-powerpoint' => 'ti ti-file-type-ppt text-warning',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'ti ti-file-type-ppt text-warning',
        ];

        foreach ($datos as $row) {
            $sub_array = array();
            $tipo = (string) $row['tipo_archivo'];

            if (str_starts_with($tipo, 'image/')) {
                $icono = 'ti ti-photo text-success';
                $badge = ['label' => strtoupper(explode('/', $tipo)[1] ?? 'IMG'), 'class' => 'bg-success'];
            } else {
                $icono = $tipos_icono[$tipo] ?? 'ti ti-file text-muted';
                $badge = $tipos_badge[$tipo] ?? ['label' => strtoupper(explode('/', $tipo)[1] ?? '-'), 'class' => 'bg-secondary'];
            }

            // Columna 1: nombre con ícono dinámico
            $sub_array[] = '<a href="../../' . htmlspecialchars($row['ruta_archivo']) . '" target="_blank" class="fw-bold text-dark text-decoration-none">
                                <i class="' . $icono . ' me-1"></i>' . htmlspecialchars($row['nombre_archivo']) . '
                            </a>';

            // Columna 2: tipo
            $sub_array[] = '<span class="badge ' . $badge['class'] . '">' . $badge['label'] . '</span>';

            // Columna 3: fecha
            $sub_array[] = date("d/m/Y", strtotime($row['created_at'])) . '
                            <span class="text-muted text-sm d-block">' . date("h:i A", strtotime($row['created_at'])) . '</span>';

            // Columna 4: acciones (eliminar solo con permiso de editar)
            $acciones = '<ul class="list-inline mb-0">
                                <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Descargar">
                                    <a class="avtar avtar-xs btn-link-warning btn-pc-default"
                                    style="cursor: pointer"
                                    href="../../' . htmlspecialchars($row['ruta_archivo']) . '" download="' . htmlspecialchars($row['nombre_archivo']) . '">
                                        <i class="ti ti-download f-18"></i>
                                    </a>
                                </li>';

            if ($permisos['puede_editar'] == 1) {
                $acciones .= '<li class="list-inline-item align-bottom" data-bs-toggle="tooltip" title="Eliminar">
                                    <a class="avtar avtar-xs btn-link-danger btn-pc-default"
                                    style="cursor: pointer"
                                    onclick="eliminarArchivoEquipo(' . $row["equipo_archivo_id"] . ')">
                                        <i class="ti ti-trash f-18"></i>
                                    </a>
                                </li>';
            }

            $acciones .= '</ul>';
            $sub_array[] = $acciones;

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

    case "guardar_archivo_equipo":
        header('Content-type: application/json; charset=utf-8');

        if ($permisos['puede_editar'] != 1) {
            echo json_encode(['success' => 0, 'message' => 'No tiene permiso para adjuntar archivos']);
            exit;
        }

        $equipo_id = isset($_POST['equipo_id']) ? intval($_POST['equipo_id']) : 0;

        if ($equipo_id <= 0 || !isset($_FILES['archivos']) || !is_array($_FILES['archivos']['name']) || empty($_FILES['archivos']['name'][0])) {
            echo json_encode(['success' => 0, 'message' => 'No se enviaron archivos']);
            exit;
        }

        $upload_dir = "../assets/equipo/$equipo_id/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Extensiones permitidas: tipo canónico, tamaño máximo (MB) y verificación del contenido real
        $word  = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        $excel = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $ppt   = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';

        $extensiones_permitidas = [
            'jpg'  => ['tipo' => 'image/jpeg', 'max_size' => 5,  'verificar' => 'image'],
            'jpeg' => ['tipo' => 'image/jpeg', 'max_size' => 5,  'verificar' => 'image'],
            'png'  => ['tipo' => 'image/png',  'max_size' => 5,  'verificar' => 'image'],
            'gif'  => ['tipo' => 'image/gif',  'max_size' => 5,  'verificar' => 'image'],
            'webp' => ['tipo' => 'image/webp', 'max_size' => 5,  'verificar' => 'image'],
            'bmp'  => ['tipo' => 'image/bmp',  'max_size' => 5,  'verificar' => 'image'],
            'pdf'  => ['tipo' => 'application/pdf', 'max_size' => 10, 'verificar' => 'pdf'],
            'doc'  => ['tipo' => 'application/msword', 'max_size' => 20, 'verificar' => null],
            'docx' => ['tipo' => $word,  'max_size' => 20, 'verificar' => null],
            'xls'  => ['tipo' => 'application/vnd.ms-excel', 'max_size' => 20, 'verificar' => null],
            'xlsx' => ['tipo' => $excel, 'max_size' => 20, 'verificar' => null],
            'ppt'  => ['tipo' => 'application/vnd.ms-powerpoint', 'max_size' => 20, 'verificar' => null],
            'pptx' => ['tipo' => $ppt,   'max_size' => 20, 'verificar' => null],
        ];

        $archivos_subidos = 0;
        $archivos_error   = 0;
        $errores          = [];

        foreach ($_FILES['archivos']['tmp_name'] as $key => $tmp_name) {
            $nombre_original = $_FILES['archivos']['name'][$key];

            if ($_FILES['archivos']['error'][$key] != UPLOAD_ERR_OK) {
                $archivos_error++;
                $errores[] = "$nombre_original: Error de carga (" . $_FILES['archivos']['error'][$key] . ")";
                continue;
            }

            $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));

            if (!isset($extensiones_permitidas[$extension])) {
                $archivos_error++;
                $errores[] = "$nombre_original: Tipo de archivo no permitido";
                continue;
            }

            $config = $extensiones_permitidas[$extension];

            if ($_FILES['archivos']['size'][$key] > $config['max_size'] * 1024 * 1024) {
                $archivos_error++;
                $errores[] = "$nombre_original: Supera el tamaño máximo de {$config['max_size']}MB";
                continue;
            }

            // Validar el contenido real del archivo (imágenes y PDF)
            if ($config['verificar'] && class_exists('finfo')) {
                $mime_real = (new finfo(FILEINFO_MIME_TYPE))->file($tmp_name);
                $contenido_valido = ($config['verificar'] === 'image')
                    ? str_starts_with($mime_real, 'image/')
                    : ($mime_real === 'application/pdf');

                if (!$contenido_valido) {
                    $archivos_error++;
                    $errores[] = "$nombre_original: El contenido no corresponde al tipo de archivo";
                    continue;
                }
            }

            // Nombre depurado y único
            $nombre_base  = substr(preg_replace('/[^A-Za-z0-9_\-]/', '_', pathinfo($nombre_original, PATHINFO_FILENAME)), 0, 80);
            $nombre_unico = $nombre_base . '_' . time() . '_' . $key . '.' . $extension;
            $ruta_destino = $upload_dir . $nombre_unico;

            if (!move_uploaded_file($tmp_name, $ruta_destino)) {
                $archivos_error++;
                $errores[] = "$nombre_original: Error al subir archivo";
                continue;
            }

            $ruta_relativa = str_replace('../', '', $upload_dir) . $nombre_unico;

            if ($equipo->insertar_archivo_equipo($equipo_id, $nombre_original, $ruta_relativa, $config['tipo'])) {
                $archivos_subidos++;
            } else {
                unlink($ruta_destino);
                $archivos_error++;
                $errores[] = "$nombre_original: Error al guardar en base de datos";
            }
        }

        $result = [];
        if ($archivos_subidos > 0 && $archivos_error === 0) {
            $result['success'] = 1;
            $result['message'] = "$archivos_subidos archivo(s) guardado(s) correctamente";
        } elseif ($archivos_subidos > 0) {
            $result['success'] = 1;
            $result['message'] = "$archivos_subidos archivo(s) guardado(s), $archivos_error con error";
            $result['errores_detalle'] = $errores;
        } else {
            $result['success'] = 0;
            $result['message'] = "No se pudo guardar ningún archivo";
            $result['errores_detalle'] = $errores;
        }

        echo json_encode($result);
        exit;
        break;

    case "eliminar_archivo":
        header('Content-type: application/json; charset=utf-8');

        if ($permisos['puede_editar'] != 1) {
            echo json_encode(['success' => false, 'message' => 'No tiene permiso para eliminar archivos']);
            exit;
        }

        $resultado = $equipo->eliminar_archivo_equipo(intval($_POST['id']));
        echo json_encode($resultado);
        break;
}