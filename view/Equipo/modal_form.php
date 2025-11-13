<div class="modal fade" id="modal_equipo" role="dialog" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <svg class="pc-icon icon-svg-primary wid-20 me-2">
                        <use xlink:href="#custom-layer"></use>
                    </svg>
                    <span id="modalEquipoLabel"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="cerrarModal();" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="form-equipo">
                    <input type="hidden" name="equipo_id" id="equipo_id" value="">
                    
                    <!-- Card 1: Información Básica del Equipo -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="ti ti-info-circle me-2"></i>Información Básica del Equipo</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 col-sm-6 mb-3">
                                    <label class="form-label">Marca <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="marca" id="marca" placeholder="Ej: HP"/>
                                </div>

                                <div class="col-md-4 col-sm-6 mb-3">
                                    <label class="form-label">Modelo <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="modelo" id="modelo" placeholder="Ej: LaserJet Pro"/>
                                </div>
                            
                                <div class="col-md-4 col-sm-6 mb-3">
                                    <label class="form-label">N° serie <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" type="text" name="numero_serie" id="numero_serie" placeholder="Ej: SN123456"/>
                                </div>

                                <div class="col-md-4 col-sm-6 mb-3">
                                    <label class="form-label">Tipo <span class="text-danger">*</span></label>
                                    <select class="select2 form-control form-control-sm" 
                                        name="tipo_equipo" 
                                        id="tipo_equipo"
                                        style="width: 100%;">
                                        <option value="">--Seleccionar--</option>
                                        <option value="bn">BLANCO/NEGRO</option>
                                        <option value="color">COLOR</option>
                                    </select>
                                </div>

                                <div class="col-md-4 col-sm-6 mb-3">
                                    <label class="form-label">Condición <span class="text-danger">*</span></label>
                                    <select class="select2 form-control form-control-sm" 
                                        name="condicion" 
                                        id="condicion"
                                        style="width: 100%;">
                                        <option value="">--Seleccionar--</option>
                                        <option value="nuevo">NUEVO</option>
                                        <option value="seminuevo">SEMINUEVO</option>
                                    </select>
                                </div>

                                <div class="col-md-4 col-sm-6 mb-3">
                                    <label class="form-label">
                                        <i class="ti ti-circle-check text-success me-1"></i>Estado
                                    </label>
                                    <div class="form-check form-switch custom-switch-v1 mt-2">
                                        <input type="checkbox" class="form-check-input input-primary" id="estado" name="estado" checked>
                                        <label class="form-check-label" for="estado">Activo</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Información de Compra -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="ti ti-shopping-cart me-2"></i>Información de Compra</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 mb-3">
                                    <label class="form-label">Proveedor <span class="text-danger">*</span></label>
                                    <select class="select2 form-control form-control-sm" 
                                        name="proveedor_id" 
                                        id="proveedor_id"
                                        style="width: 100%;">
                                        <option value="">Seleccionar...</option>
                                    </select>
                                </div>

                                <div class="col-md-4 col-sm-6 mb-3">
                                    <label class="form-label">Fecha de compra <span class="text-danger">*</span></label>
                                    <input class="form-control form-control-sm" type="date" name="fecha_compra" id="fecha_compra"/>
                                </div>

                                <div class="col-md-4 col-sm-6 mb-3">
                                    <label class="form-label">Costo ($) <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">$</span>
                                        <input class="form-control form-control-sm" type="number" step="0.01" name="costo_dolares" id="costo_dolares" placeholder="0.00"/>
                                    </div>
                                </div>

                                <div class="col-md-4 col-sm-6 mb-3">
                                    <label class="form-label">Costo (S/) <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">S/</span>
                                        <input class="form-control form-control-sm" type="number" step="0.01" name="costo_soles" id="costo_soles" placeholder="0.00"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Contadores -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="ti ti-calculator me-2"></i>Contadores de Impresión</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3 bg-light h-100">
                                        <h6 class="text-muted mb-3">
                                            <i class="ti ti-printer me-1"></i>Blanco y Negro
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <label class="form-label">Contador inicial <span class="text-danger">*</span></label>
                                                <input class="form-control form-control-sm" type="number" name="contador_inicial_bn" id="contador_inicial_bn" placeholder="0"/>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label class="form-label">Contador actual <span class="text-danger">*</span></label>
                                                <input class="form-control form-control-sm" type="number" name="contador_actual_bn" id="contador_actual_bn" placeholder="0"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3 bg-light h-100">
                                        <h6 class="text-muted mb-3">
                                            <i class="ti ti-color-swatch me-1"></i>Color
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <label class="form-label" for="contador_inicial_color">Contador inicial </label>
                                                <input class="form-control form-control-sm" type="number" name="contador_inicial_color" id="contador_inicial_color" placeholder="0"/>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label class="form-label" for="contador_actual_color">Contador actual </label>
                                                <input class="form-control form-control-sm" type="number" name="contador_actual_color" id="contador_actual_color" placeholder="0"/>
                                            </div>
                                        </div>
                                    </div>
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
                                    <textarea class="form-control form-control-sm" id="observaciones" name="observaciones" rows="3" placeholder="Ingrese observaciones adicionales sobre el equipo..."></textarea>
                                    <small class="text-muted">Opcional: información adicional sobre el equipo</small>
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
                <button type="button" class="btn btn-primary" onclick="guardarEquipo()">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>