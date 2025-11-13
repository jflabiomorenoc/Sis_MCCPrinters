<!-- Modal Ver Contrato -->
<div class="modal fade" id="modal_ver_contrato" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <svg class="pc-icon icon-svg-primary wid-20 me-2">
                        <use xlink:href="#custom-document"></use>
                    </svg>
                    Información del Contrato
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div id="loading_contrato" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted">Cargando información del contrato...</p>
                </div>

                <div id="contenido_contrato" style="display: none;">
                    <!-- Card 1: Información del Contrato -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="ti ti-file-text me-2"></i>Datos del Contrato</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Número de Contrato</label>
                                    <p class="fw-semibold mb-0" id="contrato_numero">-</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Nombre del Cliente</label>
                                    <p class="fw-semibold mb-0" id="contrato_cliente">-</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Fecha de Inicio</label>
                                    <p class="fw-semibold mb-0" id="contrato_fecha_inicio">-</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Fecha de Culminación</label>
                                    <p class="fw-semibold mb-0" id="contrato_fecha_culminacion">-</p>
                                </div>
                                <div class="col-md-12">
                                    <div class="alert alert-info mb-0" id="contrato_duracion_info" style="display: none;">
                                        <i class="ti ti-info-circle me-2"></i>
                                        <span id="contrato_duracion_texto"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Observaciones (si existen) -->
                    <div class="card mb-3" id="card_observaciones" style="display: none;">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="ti ti-notes me-2"></i>Observaciones</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0" id="contrato_observaciones">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>