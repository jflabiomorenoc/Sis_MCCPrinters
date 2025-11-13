<div class="modal fade" id="modal_ticket" role="dialog" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <svg class="pc-icon icon-svg-primary wid-20 me-2">
                        <use xlink:href="#custom-layer"></use>
                    </svg>
                    <span id="modalTicketLabel"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="cerrarModal();" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="form-ticket">
                    <input type="hidden" name="ticket_id" id="ticket_id" value="">
                    
                    <!-- Card 1: Información Básica del Equipo -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="ti ti-info-circle me-2"></i>Información básica del ticket</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 mb-3">
                                    <label class="form-label">Tipo <span class="text-danger">*</span></label>
                                    <select class="select2 form-control form-control-sm" 
                                        name="tipo_incidencia" 
                                        id="tipo_ticket">
                                        <option value="">--Seleccionar--</option>
                                        <option value="correctivo">CORRECTIVO</option>
                                        <option value="preventivo">PREVENTIVO</option>
                                    </select>
                                </div>

                                <div class="col-md-12 col-sm-12 mb-3">
                                    <label class="form-label">Cliente <span class="text-danger">*</span></label>
                                    <select class="select2 form-control form-control-sm" 
                                        name="cliente_id" 
                                        id="cliente_id"
                                        style="width: 100%;">
                                        <option value="">-- Seleccionar --</option>
                                    </select>
                                </div>

                                <div class="col-md-12 col-sm-12 mb-3">
                                    <label class="form-label">Contrato <span class="text-danger">*</span></label>
                                    <select class="select2 form-control form-control-sm" 
                                        name="contrato_id" 
                                        id="contrato_id"
                                        style="width: 100%;">
                                        <option value="">-- Seleccionar --</option>
                                    </select>
                                </div>

                                <div class="col-md-12 col-sm-12 mb-3">
                                    <label class="form-label">Equipo</label>
                                    <select class="select2 form-control form-control-sm" 
                                        name="equipo_id" 
                                        id="equipo_id"
                                        style="width: 100%;">
                                        <option value="">-- Seleccionar --</option>
                                    </select>
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3">
                                    <label class="form-label">Fecha de inicio <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" type="datetime-local"  name="fecha_incidencia"  id="fecha_incidencia" 
                                    value="<?php date_default_timezone_set('America/Lima'); echo date('Y-m-d') . 'T' . date('H:i'); ?>"/>
                                </div>

                                <div class="col-md-8 col-sm-12 mb-3">
                                    <label class="form-label">Técnico <span class="text-danger">*</span></label>
                                    <select class="select2 form-control form-control-sm" 
                                        name="tecnico_id" 
                                        id="tecnico_id"
                                        style="width: 100%;">
                                        <option value="">-- Seleccionar --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Observaciones -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="ti ti-notes me-2"></i>Detalle <span class="text-danger">*</span></h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <textarea class="form-control form-control-sm" id="descripcion_problema" name="descripcion_problema" rows="3" placeholder="Ingrese observaciones adicionales sobre el ticket..."></textarea>
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
                <button type="button" class="btn btn-primary" onclick="guardarTicket()">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>