<div class="modal fade" id="modal_direccion" role="dialog" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <i data-feather="map" class="icon-svg-primary wid-20 me-2"></i>
                    <span id="lblDireccion"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="form-direccion">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="row">
                                <input type="hidden" name="cliente_id" id="cliente_id" value="">
                                <input type="hidden" name="direccion_id" id="direccion_id" value="">

                                <div class="col-md-4 col-sm-12 mb-3" id="div_departamento">
                                    <label class="form-label">Departamento <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="departamento" id="departamento"/>
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3" id="div_provincia">
                                    <label class="form-label">Provincia <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="provincia" id="provincia"/>
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3" id="div_distrito">
                                    <label class="form-label">Distrito <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="distrito" id="distrito"/>
                                </div>

                                <div class="col-md-6 col-sm-12 mb-3" id="div_dirección">
                                    <label class="form-label">Dirección <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="direccion" id="direccion"/>
                                </div>

                                <div class="col-md-6 col-sm-12 mb-3" id="div_referencia">
                                    <label class="form-label">Referencia</label>
                                    <input class="form-control form-control-sm" type="text" name="referencia" id="referencia"/>
                                </div>
                                
                                <div class="col-lg-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="es_principal" id="es_principal"> 
                                        <label class="form-check-label" for="es_principal">Establecer como dirección principal</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                                        <div>Si marca como principal, las demás direcciones pasarán a ser secundarias.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="guardarDireccion()">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>