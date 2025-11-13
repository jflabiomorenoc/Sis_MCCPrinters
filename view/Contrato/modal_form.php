<div class="modal fade" id="modal_contrato" role="dialog" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <svg class="pc-icon icon-svg-primary wid-20 me-2">
                        <use xlink:href="#custom-layer"></use>
                    </svg>
                    <span id="modalContratoLabel"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="cerrarModal();" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="form-contrato">
                    <input type="hidden" name="contrato_id" id="contrato_id" value="">
                    
                    <!-- Card 1: Información Básica del Equipo -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="ti ti-info-circle me-2"></i>Información básica del contrato</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 mb-3">
                                    <label class="form-label">Cliente <span class="text-danger">*</span></label>
                                    <select class="select2 form-control form-control-sm" 
                                        name="cliente_id" 
                                        id="cliente_id"
                                        style="width: 100%;">
                                        <option value="">-- Seleccionar --</option>
                                    </select>
                                </div>

                                <div class="col-md-6 col-sm-12 mb-3">
                                    <label class="form-label">Responsable</label>
                                    <select class="select2 form-control form-control-sm" 
                                        name="tecnico_id" 
                                        id="tecnico_id"
                                        style="width: 100%;">
                                        <option value="">-- Seleccionar --</option>
                                    </select>
                                </div>
                            
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <label class="form-label">Fecha de inicio <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" type="date" name="fecha_inicio" id="fecha_inicio"/>
                                </div>

                                <div class="col-md-3 col-sm-6 mb-3">
                                    <label class="form-label">Fecha de culminación </label>
                                    <input class="form-control form-control-sm" type="date" name="fecha_culminacion" id="fecha_culminacion"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Observaciones -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="ti ti-notes me-2"></i>Observaciones</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <textarea class="form-control form-control-sm" id="observaciones" name="observaciones" rows="3" placeholder="Ingrese observaciones adicionales sobre el contrato..."></textarea>
                                    <small class="text-muted">Opcional: información adicional sobre el contrato</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" onclick="cerrarModal();" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="guardarContrato()">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>