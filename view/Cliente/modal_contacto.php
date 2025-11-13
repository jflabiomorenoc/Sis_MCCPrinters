<div class="modal fade" id="modal_contacto" role="dialog" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-xs" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <i data-feather="user" class="icon-svg-primary wid-20 me-2"></i>
                    <span id="lblContacto"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="form-contacto">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="row">
                                <input type="hidden" name="c_direccion_id" id="c_direccion_id" value="">
                                <input type="hidden" name="contacto_id" id="contacto_id" value="">

                                <div class="col-md-12 col-sm-12 mb-3" id="div_departamento">
                                    <label class="form-label">Nombre completo <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="nombre_contacto" id="nombre_contacto"/>
                                </div>

                                <div class="col-md-6 col-sm-12 mb-3" id="div_provincia">
                                    <label class="form-label">Cargo <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="cargo_contacto" id="cargo_contacto"/>
                                </div>

                                <div class="col-md-6 col-sm-12 mb-3" id="div_distrito">
                                    <label class="form-label">Correo electrónico <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="email_contacto" id="email_contacto"/>
                                </div>

                                <div class="col-md-6 col-sm-12 mb-3" id="div_dirección">
                                    <label class="form-label">Teléfono <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="telefono_contacto" id="telefono_contacto"/>
                                </div>

                                <div class="col-md-6 col-sm-12 mb-3" id="div_referencia">
                                    <label class="form-label">Fecha nacimiento</label>
                                    <input class="form-control form-control-sm" type="date" name="fecha_cumple" id="fecha_cumple"/>
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
                <button type="button" class="btn btn-primary" onclick="guardarContacto()">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>