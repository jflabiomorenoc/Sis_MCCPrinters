<div class="modal fade" id="modal_archivos" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <svg class="pc-icon icon-svg-primary wid-20 me-2">
                        <use xlink:href="#custom-layer"></use>
                    </svg>
                    Archivos del equipo <span class="text-muted fw-normal" id="modal_archivos_equipo"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="cerrarModalArchivos();" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="archivo_equipo_id" value="">

                <!-- Card: Adjuntar archivos (solo con permiso de editar) -->
                <div class="card mb-3" id="card_subir_archivo">
                    <div class="card-body">
                        <input type="file"
                            id="fileInputArchivo"
                            accept="image/*,
                                    application/pdf,
                                    application/msword,
                                    application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                    application/vnd.ms-excel,
                                    application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
                                    application/vnd.ms-powerpoint,
                                    application/vnd.openxmlformats-officedocument.presentationml.presentation"
                            multiple
                            style="display: none;">

                        <div class="file-upload-area" id="fileUploadAreaArchivo">
                            <i class="bi bi-cloud-upload"></i>
                            <p class="mb-1 mt-2">Arrastra archivos aquí o haz clic para seleccionar</p>
                            <small class="text-muted">
                                🖼️ Imágenes (máx. 5MB) &nbsp;|&nbsp;
                                📄 PDF (máx. 10MB) &nbsp;|&nbsp;
                                📝 Word, Excel, PPT (máx. 20MB)
                            </small>
                        </div>

                        <!-- Área de vista previa -->
                        <div id="previewContainerArchivo" class="preview-container-modal"></div>

                        <div class="text-end mt-3" id="div_subir_archivos" style="display: none;">
                            <button type="button" class="btn btn-primary btn-sm" id="btnSubirArchivos" onclick="subirArchivos()">
                                <i class="ti ti-upload me-1"></i>Subir archivos
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Lista de archivos -->
                <div class="position-relative">
                    <div class="position-absolute top-50 start-50 translate-middle" id="loading_archivos" style="z-index: 5;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>

                    <div class="table-responsive" id="contenedor_tabla_archivos" style="visibility: hidden; min-height: 150px;">
                        <table class="table table-hover mb-0" id="tabla-archivos" style="width: 100%;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-top-0">Archivo</th>
                                    <th class="border-top-0 text-center">Tipo</th>
                                    <th class="border-top-0 text-center">Fecha</th>
                                    <th class="border-top-0 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" onclick="cerrarModalArchivos();" data-bs-dismiss="modal">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
