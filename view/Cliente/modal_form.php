<div class="modal fade" id="modal_cliente" role="dialog" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <svg class="pc-icon icon-svg-primary wid-20 me-2">
                        <use xlink:href="#custom-user"></use>
                    </svg>
                    <span id="modalClienteLabel"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="cerrarModal();" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="form-cliente">
                    <input type="hidden" name="cliente_id" id="cliente_id" value="">
                    
                    <!-- Card 1: Información de Identificación -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="ti ti-id me-2"></i>Información de Identificación</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Tipo de RUC -->
                                <div class="col-lg-6 mb-3">
                                    <div class="border card p-3 h-100" style="cursor: pointer;" onclick="$('#tipo_ruc1').prop('checked', true).trigger('change')">
                                        <div class="form-check">
                                            <input type="radio" name="tipo_ruc" value="1" class="form-check-input input-primary" id="tipo_ruc1" checked>
                                            <label class="form-check-label d-block" for="tipo_ruc1" style="cursor: pointer;">
                                                <span>
                                                    <span class="h5 d-block">
                                                        <i class="ti ti-building me-1"></i>Jurídico
                                                    </span> 
                                                    <span class="text-muted small">
                                                        Inscrito a nombre de una empresa o entidad legal
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <div class="border card p-3 h-100" style="cursor: pointer;" onclick="$('#tipo_ruc2').prop('checked', true).trigger('change')">
                                        <div class="form-check">
                                            <input type="radio" name="tipo_ruc" value="2" class="form-check-input input-primary" id="tipo_ruc2">
                                            <label class="form-check-label d-block" for="tipo_ruc2" style="cursor: pointer;">
                                                <span>
                                                    <span class="h5 d-block">
                                                        <i class="ti ti-user me-1"></i>Natural
                                                    </span> 
                                                    <span class="text-muted small">
                                                        Inscrito a nombre de una persona individual
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- N° Documento -->
                                <div class="col-md-12 col-sm-12 mb-3">
                                    <label class="form-label">N° documento <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="ruc" id="ruc" placeholder="Ingrese RUC o DNI" maxlength="11"/>
                                </div>

                                <!-- Para Persona Jurídica -->
                                <div class="col-md-12 col-sm-12 mb-3" id="div_razon_social">
                                    <label class="form-label">Razón social <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="razon_social" id="razon_social" placeholder="Ej: Corporación ABC S.A.C."/>
                                </div>

                                <!-- Para Persona Natural -->
                                <div class="col-md-4 col-sm-12 mb-3" id="div_nombre" style="display: none;">
                                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="nombre_cliente" id="nombre_cliente" placeholder="Ej: Juan"/>
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3" id="div_apaterno" style="display: none;">
                                    <label class="form-label">Apellido paterno <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="apellido_paterno" id="apellido_paterno" placeholder="Ej: Pérez"/>
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3" id="div_amaterno" style="display: none;">
                                    <label class="form-label">Apellido materno <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="apellido_materno" id="apellido_materno" placeholder="Ej: García"/>
                                </div>

                                <!-- Estado -->
                                <div class="col-md-12 col-sm-12 mb-3">
                                    <label class="form-label">
                                        <i class="ti ti-circle-check text-success me-1"></i>Estado
                                    </label>
                                    <div class="form-check form-switch custom-switch-v1 mt-2">
                                        <input type="checkbox" class="form-check-input input-primary" id="estado_cliente" name="estado_cliente" checked>
                                        <label class="form-check-label" for="estado_cliente">Activo</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Dirección Principal (Solo para Nuevo) -->
                    <div class="card mb-3" id="card_direccion_principal">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="ti ti-map-pin me-2"></i>Dirección Principal
                                <span class="badge bg-success ms-2">Nueva</span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
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

                                <div class="col-md-6 col-sm-12 mb-3" id="div_dirección">
                                    <label class="form-label">Dirección <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="ti ti-home"></i></span>
                                        <input class="form-control form-control-sm" type="text" name="direccion" id="direccion" placeholder="Av. Principal 123, Oficina 501"/>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12 mb-3" id="div_referencia">
                                    <label class="form-label">Referencia</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="ti ti-map-pin"></i></span>
                                        <input class="form-control form-control-sm" type="text" name="referencia" id="referencia" placeholder="Frente al parque principal"/>
                                    </div>
                                    <small class="text-muted">Opcional: punto de referencia para ubicar la dirección</small>
                                </div>
                            </div>

                            <div class="alert alert-info mb-0">
                                <i class="ti ti-info-circle me-2"></i>
                                <small>Esta será la dirección principal del cliente. Podrás agregar más direcciones después de crear el cliente.</small>
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