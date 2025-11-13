<!-- Modal Ver Equipo -->
<div class="modal fade" id="modal_ver_equipo" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <svg class="pc-icon icon-svg-primary wid-20 me-2">
                        <use xlink:href="#custom-layer"></use>
                    </svg>
                    Información del Equipo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div id="loading_equipo" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted">Cargando información del equipo...</p>
                </div>

                <div id="contenido_equipo" style="display: none;">
                    <!-- Card 1: Información del Equipo -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="ti ti-device-desktop me-2"></i>Datos del Equipo</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Número de Serie</label>
                                    <p class="fw-semibold mb-0" id="equipo_numero_serie">-</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Tipo de Equipo</label>
                                    <p class="mb-0">
                                        <span id="equipo_tipo" class="badge">-</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Condición</label>
                                    <p class="mb-0">
                                        <span id="equipo_condicion" class="badge">-</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">IP del Equipo</label>
                                    <p class="fw-semibold mb-0" id="equipo_ip">-</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Ubicación -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="ti ti-map-pin me-2"></i>Ubicación</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="text-muted small">Dirección</label>
                                    <p class="mb-0" id="equipo_direccion">-</p>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="text-muted small">Área de Ubicación</label>
                                    <p class="fw-semibold mb-0" id="equipo_area">-</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Contadores -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="ti ti-calculator me-2"></i>Contadores</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Contador Inicial B/N</label>
                                    <p class="fw-semibold mb-0 fs-5" id="equipo_contador_bn">-</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Contador Inicial Color</label>
                                    <p class="fw-semibold mb-0 fs-5" id="equipo_contador_color">-</p>
                                </div>
                            </div>
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