<div class="modal" id="modal_archivo" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <span id="modal_archivo_title">Cancelar ticket</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="cerrarModal();" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="form-contrato-archivo">
                    <input type="hidden" name="contrato_id" id="contrato_id" value="">

                    <!-- Card: Adjuntar Fotos (diseño unificado) -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <input type="file" 
                                id="fileInputModal" 
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
                            
                            <!-- Área de arrastre (igual que el formulario) -->
                            <div class="file-upload-area" id="fileUploadAreaModal">
                                <i class="bi bi-cloud-upload"></i>
                                <p class="mb-1 mt-2">Arrastra archivos aquí o haz clic para seleccionar</p>
                                <small class="text-muted">
                                    🖼️ Imágenes (máx. 5MB) &nbsp;|&nbsp; 
                                    📄 PDF (máx. 10MB) &nbsp;|&nbsp; 
                                    📝 Word, Excel, PPT (máx. 20MB)
                                </small>
                            </div>
                            
                            <!-- Área de vista previa -->
                            <div id="previewContainerModal" class="preview-container-modal"></div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" onclick="cerrarModalArchivo();" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btnGuardarModal" onclick="guardarArchivo()">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>