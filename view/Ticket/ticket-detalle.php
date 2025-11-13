<?php
require_once("../../config/conexion.php");

// Verificar sesión ANTES de cualquier output
if(!isset($_SESSION["id"])){
    header("Location: " . Conectar::ruta());
    exit;
}

// Solo si la sesión existe, incluir los archivos
include "../MainHead/head.php";
include "../MainNav/nav.php";
include "../MainHeader/header.php";
?>      
        <style>
            /* Estilos para formulario de comentarios */
            .comment-form {
                display: none;
                margin-bottom: 20px;
                padding: 20px;
                background: #f8f9fa;
                border-radius: 8px;
            }

            .comment-form.active {
                display: block;
            }

            /* Estilos unificados para carga de archivos */
            .file-upload-area {
                border: 2px dashed #dee2e6;
                border-radius: 8px;
                padding: 30px 20px;
                text-align: center;
                cursor: pointer;
                transition: all 0.3s;
                background: #fff;
            }

            .file-upload-area:hover {
                border-color: #0d6efd;
                background: #f8f9ff;
            }

            .file-upload-area.dragover {
                border-color: #0d6efd;
                background: #e7f1ff;
            }

            .file-upload-area i {
                font-size: 3rem;
                color: #6c757d;
            }

            .preview-container,
            .preview-container-modal {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                gap: 12px;
                margin-top: 15px;
                min-height: 50px;
            }

            /* ====================================
            ITEMS DE PREVIEW
            ==================================== */

            .preview-item,
            .preview-item-modal {
                position: relative;
                width: 100%;
                height: 140px;
                border-radius: 10px;
                overflow: hidden;
                border: 2px solid #e9ecef;
                background: #fff;
                transition: all 0.3s ease;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }

            .preview-item:hover,
            .preview-item-modal:hover {
                transform: translateY(-4px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                border-color: #0d6efd;
            }

            /* Preview de Imágenes */
            .preview-item img,
            .preview-item-modal img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            /* Preview de PDF */
            .file-preview-pdf {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                height: 100%;
                padding: 15px;
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            }

            .file-preview-pdf i {
                font-size: 48px;
                margin-bottom: 8px;
                filter: drop-shadow(0 2px 4px rgba(220, 53, 69, 0.3));
            }

            .file-preview-pdf .file-name {
                font-size: 11px;
                font-weight: 500;
                text-align: center;
                margin-top: 8px;
                overflow: hidden;
                text-overflow: ellipsis;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                max-width: 100%;
                color: #495057;
                line-height: 1.3;
            }

            .file-preview-pdf .file-size {
                font-size: 10px;
                color: #6c757d;
                margin-top: 4px;
                font-weight: 600;
            }

            /* Preview de Video */
            .preview-item video.video-preview,
            .preview-item-modal video.video-preview {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .file-overlay {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                background: linear-gradient(to top, rgba(0, 0, 0, 0.85), transparent);
                padding: 8px 10px;
                display: flex;
                flex-direction: column;
            }

            .file-overlay .file-name {
                font-size: 10px;
                font-weight: 500;
                color: white;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                text-shadow: 0 1px 2px rgba(0,0,0,0.5);
            }

            .file-overlay .file-size {
                font-size: 9px;
                color: rgba(255, 255, 255, 0.9);
                margin-top: 2px;
                font-weight: 600;
            }

            /* Botones de Eliminar en Preview */
            .preview-item .remove-btn,
            .preview-item-modal .remove-btn-modal {
                position: absolute;
                top: 8px;
                right: 8px;
                background: rgba(220, 53, 69, 0.95);
                color: white;
                border: none;
                border-radius: 50%;
                width: 28px;
                height: 28px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                font-size: 16px;
                padding: 0;
                transition: all 0.2s ease;
                z-index: 10;
                box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
            }

            .preview-item .remove-btn:hover,
            .preview-item-modal .remove-btn-modal:hover {
                background: #dc3545;
                transform: scale(1.15) rotate(90deg);
                box-shadow: 0 4px 12px rgba(220, 53, 69, 0.6);
            }

            /* Botones de acción unificados */
            .file-action-buttons {
                display: flex;
                gap: 10px;
                margin-top: 15px;
            }

            .file-action-buttons .btn {
                flex: 1;
            }

            /* Modal para vista de imágenes */
            .image-modal {
                display: none;
                position: fixed;
                z-index: 9999;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.9);
            }

            .image-modal.active {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .image-modal img {
                max-width: 90%;
                max-height: 90vh;
                border-radius: 8px;
            }

            .image-modal .close-modal {
                position: absolute;
                top: 20px;
                right: 30px;
                color: white;
                font-size: 40px;
                cursor: pointer;
                transition: transform 0.2s;
            }

            .image-modal .close-modal:hover {
                transform: scale(1.1);
            }

            .comment-body img {
                max-width: 150px;
                border-radius: 4px;
                cursor: pointer;
                transition: transform 0.2s;
            }

            .comment-body img:hover {
                transform: scale(1.05);
            }

            .wid-60 {
                width: 60px;
                height: 60px;
            }

            .img-radius {
                border-radius: 50%;
            }

            .comment-files {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                gap: 12px;
                margin-top: 15px;
                padding: 12px;
                background: #f8f9fa;
                border-radius: 8px;
            }

            .comment-file-item {
                position: relative;
                border: 2px solid #dee2e6;
                border-radius: 10px;
                overflow: hidden;
                cursor: pointer;
                transition: all 0.3s ease;
                background: #fff;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }

            .comment-file-item:hover {
                transform: translateY(-4px);
                border-color: #0d6efd;
                box-shadow: 0 6px 16px rgba(13, 110, 253, 0.2);
            }

            /* Imagen en comentario */
            .comment-file-item.image-item {
                width: 90%;
                height: 120px;
            }

            .comment-file-item.image-item img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.3s ease;
            }

            .comment-file-item.image-item:hover img {
                transform: scale(1.05);
            }

            /* PDF en comentario */
            .comment-file-item.pdf-item {
                width: 90%;
                min-height: 120px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 15px;
                background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
                border: 2px solid #e74c3c;
            }

            .comment-file-item.pdf-item i {
                font-size: 48px;
                color: #e74c3c;
                margin-bottom: 10px;
                transition: transform 0.3s ease;
                filter: drop-shadow(0 2px 4px rgba(231, 76, 60, 0.2));
            }

            .comment-file-item.pdf-item:hover {
                background: linear-gradient(135deg, #fff5f5 0%, #ffebeb 100%);
                border-color: #c0392b;
            }

            .comment-file-item.pdf-item:hover i {
                transform: scale(1.1);
                color: #c0392b;
            }

            .comment-file-item .file-name {
                font-size: 11px;
                font-weight: 500;
                text-align: center;
                overflow: hidden;
                text-overflow: ellipsis;
                display: -webkit-box;
                -webkit-box-orient: vertical;
                max-width: 100%;
                color: #495057;
                line-height: 1.3;
                padding: 0 5px;
            }

            /* Video en comentario */
            .comment-file-item.video-item {
                width: 100%;
                background: #000;
            }

            .comment-file-item.video-item video {
                width: 100%;
                max-height: 200px;
                display: block;
            }

            .comment-file-item.video-item .file-name {
                display: block;
                padding: 8px 10px;
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                font-size: 11px;
                font-weight: 500;
                text-align: center;
                color: #495057;
            }

            /* Badge de cantidad de archivos */
            .files-count-badge {
                position: absolute;
                top: 10px;
                left: 10px;
                background: rgba(13, 110, 253, 0.95);
                color: white;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: 600;
                z-index: 5;
                box-shadow: 0 2px 6px rgba(13, 110, 253, 0.4);
            }
        </style>
        <div class="pc-container">
            <div class="pc-content"><!-- [ breadcrumb ] start -->
                <div class="page-header">
                    <div class="page-block">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="../Dashboard/">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="../Ticket/">Tickets</a></li>
                                    <li class="breadcrumb-item" aria-current="page" id="lblLiTicket"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div>
                                        <h4 id="lblNumTicket"></h4>
                                        <div class="mt-2">
                                            <span class="badge rounded-pill" id="lblEstado"></span>
                                        </div>
                                    </div>
                                    <div class="dropdown" id="dropdownAccion">
                                        <a class="avtar avtar-s btn-link-secondary dropdown-toggle arrow-none" style="cursor: pointer;" 
                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="ti ti-dots-vertical f-18"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" style="cursor: pointer;" id="btnEditarTicket" onclick="editarTicket()">
                                                <i class="ti ti-pencil me-2"></i>Editar
                                            </a>
                                            <a class="dropdown-item text-danger" style="cursor: pointer; display:none;" id="btnCancelarTicket" onclick="cancelarTicket()">
                                                <i class="ti ti-circle-x me-2"></i>Cancelar
                                            </a>
                                            <a class="dropdown-item text-warning" style="cursor: pointer;" id="btnEnProcesoTicket" onclick="estadoTicket('en_proceso')">
                                                <i class="ti ti-check me-2"></i>En proceso
                                            </a>
                                            <a class="dropdown-item text-success" style="cursor: pointer; display:none;" id="btnFinalizarTicket" onclick="finalizarTicket()">
                                                <i class="ti ti-check me-2"></i>Finalizar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <h6>INICIO</h6>
                                            <div id="lblInicio"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <h6>CONTRATO</h6>
                                            <div id="lblContrato"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <h6>EQUIPO</h6>
                                            <div id="lblEquipo"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <h6>CLIENTE</h6>
                                            <div id="lblCliente"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <h6>TÉCNICO RESPONSABLE</h6>
                                            <div id="lblTecnico"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <h6>FECHA DE CIERRE</h6>
                                            <div id="lblCierre"></div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <hr class="my-3">
                                        <div class="mb-2">
                                            <h6>DESCRIPCIÓN DEL PROBLEMA</h6>
                                            <div class="p-3 bg-light rounded" id="lblDescripcion">
                                                <p class="mb-0 text-muted">Sin descripción</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Comentarios</h5>
                                <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2" style="cursor: pointer; display:none !important;" id="btnNuevo">
                                    <i class="ti ti-plus me-1"></i>Agregar comentario
                                </button>
                            </div>

                            <div class="card-body comment-form" id="commentForm">
                                <h6 class="mb-3">Nuevo Comentario</h6>
                                <form id="formComentario">
                                    <div class="mb-3">
                                        <label class="form-label">Comentario <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="comentarioTexto" rows="4" placeholder="Escribe tu comentario aquí..." required></textarea>
                                    </div>
                                    
                                    <!-- Área de carga unificada -->
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0"><i class="ti ti-photo me-2"></i>Adjuntar Fotos (Opcional)</h6>
                                        </div>
                                        <div class="card-body">
                                            <!-- Input para archivos (actualizado) -->
                                            <input type="file" 
                                                id="fileInput" 
                                                accept="image/*,video/*,application/pdf" 
                                                multiple 
                                                style="display: none;">

                                            <!-- Área de carga -->
                                            <div class="file-upload-area" id="fileUploadArea">
                                                <i class="bi bi-cloud-upload"></i>
                                                <p class="mb-1 mt-2">Arrastra imágenes, videos o PDFs aquí o haz clic para seleccionar</p>
                                                <small class="text-muted">Imágenes (máx. 5MB) | Videos (máx. 50MB) | PDFs (máx. 10MB)</small>
                                            </div>
                                                                                        
                                            <!-- Previsualización -->
                                            <div class="preview-container" id="previewContainer"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex gap-2 mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-send"></i> Publicar comentario
                                        </button>
                                        <button type="button" class="btn btn-secondary" id="btnCancelar">
                                            Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div id="comentariosContainer"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para vista previa de imágenes -->
        <div class="image-modal" id="imageModal">
            <span class="close-modal" id="closeModal">&times;</span>
            <img id="modalImage" src="" alt="Vista previa">
        </div>

        <?php include "modal_observacion.php"; ?>
    
<?php
include "../MainFooter/footer.php";
?>

<script src="detalle.js"></script>