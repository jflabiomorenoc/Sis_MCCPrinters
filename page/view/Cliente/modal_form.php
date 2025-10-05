<div class="modal fade" id="modal_cliente" role="dialog" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <i data-feather="plus-circle" class="icon-svg-primary wid-20 me-2"></i>
                    <span id="modalClienteLabel">Nuevo usuario</span>
                    
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="cerrarModal();" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="form-cliente">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="row">
                                <input type="hidden" name="cliente_id" id="cliente_id" value="">

                                <div class="col-md-12 col-sm-12 mt-1">
                                    <label class="form-label">Tipo de RUC</label>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio" name="tipo_ruc" value="1" class="form-check-input input-primary" id="tipo_ruc1" checked>
                                                    <label class="form-check-label d-block" for="tipo_ruc1">
                                                        <span>
                                                            <span class="h5 d-block">
                                                                Jurídico
                                                            </span> 
                                                            <span class="f-12 text-muted">
                                                                Inscrito a nombre de una empresa o entidad legal
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio" name="tipo_ruc" value="2" class="form-check-input input-primary" id="tipo_ruc2">
                                                    <label class="form-check-label d-block" for="tipo_ruc2">
                                                        <span>
                                                            <span class="h5 d-block">
                                                                Natural
                                                            </span> 
                                                            <span class="f-12 text-muted">
                                                                Inscrito a nombre de una persona individual
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="col-md-3 col-sm-12 mb-2">
                                    <label class="form-label">N° documento <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="ruc" id="ruc"/>
                                </div>

                                <div class="col-md-3 col-sm-12 mb-2" id="div_nombre">
                                    <label class="form-label">Nombre <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="nombre_cliente" id="nombre_cliente"/>
                                </div>

                                <div class="col-md-3 col-sm-12 mb-2" id="div_apaterno">
                                    <label class="form-label">Apellido paterno <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="apellido_paterno" id="apellido_paterno"/>
                                </div>

                                <div class="col-md-3 col-sm-12 mb-2" id="div_amaterno">
                                    <label class="form-label">Apellido materno <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="apellido_materno" id="apellido_materno"/>
                                </div>

                                <div class="col-md-9 col-sm-12 mb-2" id="div_razon_social">
                                    <label class="form-label">Razón social <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="razon_social" id="razon_social"/>
                                </div>

                                <div class="col-md-4 col-sm-12 mb-2" id="div_departamento">
                                    <label class="form-label">Departamento <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="departamento" id="departamento"/>
                                </div>

                                <div class="col-md-4 col-sm-12 mb-2" id="div_provincia">
                                    <label class="form-label">Provincia <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="provincia" id="provincia"/>
                                </div>

                                <div class="col-md-4 col-sm-12 mb-2" id="div_distrito">
                                    <label class="form-label">Distrito <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="distrito" id="distrito"/>
                                </div>

                                <div class="col-md-6 col-sm-12 mb-2" id="div_dirección">
                                    <label class="form-label">Dirección <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="direccion" id="direccion"/>
                                </div>

                                <div class="col-md-6 col-sm-12 mb-2" id="div_referencia">
                                    <label class="form-label">Referencia</label>
                                    <input class="form-control form-control-sm" type="text" name="referencia" id="referencia"/>
                                </div>

                                <div class="col-md-3 col-sm-12 mb-2">
                                    <label class="form-label">Contacto principal <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="contacto_principal" id="contacto_principal"/>
                                </div>

                                <div class="col-md-3 col-sm-12 mb-2">
                                    <label class="form-label">Cargo <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="cargo_contacto" id="cargo_contacto"/>
                                </div>

                                <div class="col-md-3 col-sm-12 mb-2">
                                    <label class="form-label">Correo electrónico <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="email_contacto" id="email_contacto"/>
                                </div>

                                <div class="col-md-3 col-sm-12 mb-2">
                                    <label class="form-label">N° contacto <span style="color: red;">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="telefono_contacto" id="telefono_contacto"/>
                                </div>

                                <div class="col-md-3 col-sm-12 mb-2">
                                    <label class="form-label">Contacto 1</label>
                                    <input class="form-control form-control-sm" type="text" name="contacto_1" id="contacto_1"/>
                                </div>

                                <div class="col-md-3 col-sm-12 mb-2">
                                    <label class="form-label">N° contacto</label>
                                    <input class="form-control form-control-sm" type="text" name="telefono_contacto_1" id="telefono_contacto_1"/>
                                </div>

                                <div class="col-md-3 col-sm-12 mb-2">
                                    <label class="form-label">Contacto 2</label>
                                    <input class="form-control form-control-sm" type="text" name="contacto_2" id="contacto_2"/>
                                </div>

                                <div class="col-md-3 col-sm-12 mb-2">
                                    <label class="form-label">N° contacto</label>
                                    <input class="form-control form-control-sm" type="text" name="telefono_contacto_2" id="telefono_contacto_2"/>
                                </div>
                                
                                <div class="col-md-6 mt-2 mb-3">
                                    <div class="form-check form-switch custom-switch-v1 form-check-inline">
                                        <input type="checkbox" class="form-check-input input-primary" id="estado_cliente" name="estado_cliente" checked>
                                        <label class="form-check-label" for="estado_perfil">Activo</label>
                                    </div>
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
                <button type="button" class="btn btn-primary" onclick="guardarCliente()">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>