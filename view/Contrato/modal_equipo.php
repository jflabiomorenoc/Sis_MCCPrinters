<div class="modal fade" id="modal_equipo" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_equipo_title">Agregar Equipo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-contrato-equipo">
                    <input type="hidden" name="contrato_equipo_id" id="contrato_equipo_id">
                    <input type="hidden" name="contrato_id" id="contrato_id_equipo" value="">

                    <div class="col-md-12 col-sm-12 mb-3">
                        <label class="form-label">Dirección <span class="text-danger">*</span></label>
                        <select class="select2 form-control form-control-sm" 
                            name="direccion_id" 
                            id="direccion_id"
                            style="width: 100%;">
                            <option value="">-- Seleccionar --</option>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Equipo <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm select2-equipo" 
                                    name="equipo_id" 
                                    id="equipo_id"
                                    style="width: 100%;">
                                <option value="">-- Seleccionar Equipo --</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">IP Equipo</label>
                            <input class="form-control form-control-sm ip" type="text" name="ip_equipo" id="ip_equipo" placeholder="192.168.1.100"/>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Área/Ubicación</label>
                            <input class="form-control form-control-sm" type="text" name="area_ubicacion" id="area_ubicacion" placeholder="Ej: Recepción, Oficina 2"/>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contador inicial BN <span class="text-danger">*</span></label>
                            <input class="form-control form-control-sm" type="number" name="contador_inicial_bn" id="contador_inicial_bn" placeholder="0"/>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contador final BN <span class="text-danger">*</span></label>
                            <input class="form-control form-control-sm" type="number" name="contador_final_bn" id="contador_final_bn" placeholder="0"/>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contador inicial Color </label>
                            <input class="form-control form-control-sm" type="number" name="contador_inicial_color" id="contador_inicial_color" placeholder="0"/>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contador final Color </label>
                            <input class="form-control form-control-sm" type="number" name="contador_final_color" id="contador_final_color" placeholder="0"/>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarEquipo" onclick="guardarEquipo()">Guardar</button>
            </div>
        </div>
    </div>
</div>