<div class="modal" id="modal_observacion" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <span id="modalTicketLabel">Cancelar ticket</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="cerrarModal();" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="form-ticket-cancelar">
                    <input type="hidden" name="fo_ticket_id" id="fo_ticket_id" value="">
                    <input type="hidden" name="accion_tipo" id="accion_tipo" value="">
            
                    <!-- Card: Observaciones -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="ti ti-notes me-2"></i>Observaciones <span class="text-danger">*</span></h6>
                        </div>
                        <div class="card-body">
                            <textarea class="form-control form-control-sm" id="observaciones" name="observaciones" rows="3" placeholder="Ingrese observaciones sobre el ticket..."></textarea>
                        </div>
                    </div>

                    <!-- Card: Contadores (solo para finalizar) -->
                    <div class="card mb-3" id="card-contadores" style="display: none;">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="ti ti-printer me-2"></i>Contadores Finales</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label for="contador_bn" class="form-label">Contador final BN <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control form-control-sm" id="contador_bn" name="contador_bn" placeholder="Ej: 12345" min="0">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="contador_color" class="form-label">Contador final Color (Opcional)</label>
                                    <input type="number" class="form-control form-control-sm" id="contador_color" name="contador_color" placeholder="Ej: 6789" min="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card: Adjuntar Fotos (diseño unificado) -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="ti ti-photo me-2"></i>Adjuntar Fotos (Opcional)</h6>
                        </div>
                        <div class="card-body">
                            <input type="file" 
                                    id="fileInputModal" 
                                    accept="image/*,video/*,application/pdf" 
                                    multiple 
                                    style="display: none;">
                            
                            <!-- Área de arrastre (igual que el formulario) -->
                            <div class="file-upload-area" id="fileUploadAreaModal">
                                <i class="bi bi-cloud-upload"></i>
                                <p class="mb-1 mt-2">Arrastra imágenes, videos o PDFs aquí o haz clic para seleccionar</p>
                                <small class="text-muted">Imágenes (máx. 5MB) | Videos (máx. 50MB) | PDFs (máx. 10MB)</small>
                            </div>
                            
                            <!-- Área de vista previa -->
                            <div id="previewContainerModal" class="preview-container-modal"></div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" onclick="cerrarModal();" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btnGuardarModal" onclick="guardarAccion()">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>