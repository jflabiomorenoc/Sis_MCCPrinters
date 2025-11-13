<div class="modal fade" id="modal_proveedor" role="dialog" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <svg class="pc-icon icon-svg-primary wid-20 me-2">
                        <use xlink:href="#custom-user"></use>
                    </svg>
                    <span id="modalProveedorLabel"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="cerrarModal();" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="form-proveedor">
                    <input type="hidden" name="proveedor_id" id="proveedor_id" value="">
                    
                    <!-- Card 2: Información de Identificación -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="ti ti-id me-2"></i>Información de Identificación</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6 mb-2">
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
                                <div class="col-lg-6 mb-2">
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
                                    <input class="form-control form-control-sm" type="text" name="nombre_proveedor" id="nombre_proveedor" placeholder="Ej: Juan"/>
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3" id="div_apaterno" style="display: none;">
                                    <label class="form-label">Apellido paterno <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="apellido_paterno" id="apellido_paterno" placeholder="Ej: Pérez"/>
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3" id="div_amaterno" style="display: none;">
                                    <label class="form-label">Apellido materno <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="apellido_materno" id="apellido_materno" placeholder="Ej: García"/>
                                </div>

                                <div class="col-md-4 col-sm-6 mb-3">
                                    <label class="form-label">
                                        <i class="ti ti-circle-check text-success me-1"></i>Estado
                                    </label>
                                    <div class="form-check form-switch custom-switch-v1 mt-2">
                                        <input type="checkbox" class="form-check-input input-primary" id="estado_proveedor" name="estado_proveedor" checked>
                                        <label class="form-check-label" for="estado">Activo</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Información de Contacto -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="ti ti-phone me-2"></i>Información de Contacto</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 col-sm-12 mb-3">
                                    <label class="form-label">Contacto</label>
                                    <input class="form-control form-control-sm" type="text" name="contacto" id="contacto" placeholder="Nombre del contacto"/>
                                    <small class="text-muted">Opcional: persona de contacto</small>
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3">
                                    <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                        <input class="form-control form-control-sm" type="email" name="email" id="email" placeholder="correo@ejemplo.com"/>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3">
                                    <label class="form-label">Teléfono <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="ti ti-phone"></i></span>
                                        <input class="form-control form-control-sm" type="text" name="telefono" id="telefono" placeholder="987 654 321" maxlength="15"/>
                                    </div>
                                </div>

                                <div class="col-md-12 col-sm-12 mb-3">
                                    <label class="form-label">Dirección <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="ti ti-map-pin"></i></span>
                                        <input class="form-control form-control-sm" type="text" name="direccion" id="direccion" placeholder="Av. Principal 123, Distrito, Ciudad"/>
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
                <button type="button" class="btn btn-primary" onclick="guardarProveedor()">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>