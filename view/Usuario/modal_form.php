<div class="modal fade" id="modal_usuario" role="dialog" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <svg class="pc-icon icon-svg-primary wid-20 me-2">
                        <use xlink:href="#custom-user-square"></use>
                    </svg>
                    <span id="modalUsuarioLabel">Nuevo usuario</span>
                    
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="cerrarModal();" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="form-usuario">
                    <div class="row">
                        <div class="col-md-8 col-sm-12">
                            <div class="row">
                                <input type="hidden" name="usuario_id" id="usuario_id" value="">
                            
                                <div class="col-md-6 col-sm-12 mb-2">
                                    <label class="form-label">Nombres</label>
                                    <input class="form-control form-control-sm" type="text" name="nombres" id="nombres"/>
                                </div>
                                <div class="col-md-6 col-sm-12 mb-2">
                                    <label class="form-label">Apellidos</label>
                                    <input class="form-control form-control-sm" type="text" name="apellidos" id="apellidos"/>
                                </div>
                                <div class="col-md-6 col-sm-12 mb-2">
                                    <label class="form-label">Usuario</label>
                                    <input class="form-control form-control-sm" type="text" name="usuario" id="usuario"/>
                                </div>
                                <div class="col-md-6 col-sm-12 mb-2">
                                    <label class="form-label">Contraseña</label>
                                    <input class="form-control form-control-sm" type="password" name="password" id="password"/>
                                </div>

                                <div class="col-md-12 col-sm-12 mt-1">
                                    <label class="form-label">Rol</label>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio" name="rol_usuario" value="1" class="form-check-input input-primary" id="rol_usuario1" checked>
                                                    <label class="form-check-label d-block" for="rol_usuario1">
                                                        <span>
                                                            <span class="h5 d-block">
                                                                Administrador
                                                            </span> 
                                                            <span class="f-12 text-muted">
                                                                Acceso a todos los módulos del sistema
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="border card p-3">
                                                <div class="form-check">
                                                    <input type="radio" name="rol_usuario" value="2" class="form-check-input input-primary" id="rol_usuario2">
                                                    <label class="form-check-label d-block" for="rol_usuario2">
                                                        <span>
                                                            <span class="h5 d-block">
                                                                Normal
                                                            </span> 
                                                            <span class="f-12 text-muted">
                                                                Acceso limitado al sistema definido por perfiles
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 col-sm-12 mb-2" id="div_perfil">
                                    <label class="form-label">Perfil</label>
                                    <select class="select2 form-control form-control-sm" name="perfil_usuario" id="perfil_usuario">
                                        
                                    </select>
                                </div>

                                <div class="col-md-12 col-sm-12 mb-2" id="div_cliente">
                                    <label class="form-label">Cliente</label>
                                    <select class="select2 form-control form-control-sm" name="cliente_id" id="cliente_id">

                                    </select>
                                </div>

                                <div class="col-md-6 col-sm-12 mb-2">
                                    <label class="form-label">N° contacto</label>
                                    <input class="form-control form-control-sm" type="text" name="numero_contacto" id="numero_contacto"/>
                                </div>

                                <div class="col-md-6 col-sm-12 mb-2">
                                    <label class="form-label">Correo electrónico</label>
                                    <input class="form-control form-control-sm" type="text" name="email" id="email"/>
                                </div>
                                
                                <div class="col-md-6 mt-2 mb-3">
                                    <div class="form-check form-switch custom-switch-v1 form-check-inline">
                                        <input type="checkbox" class="form-check-input input-primary" id="estado_usuario" name="estado_usuario" checked>
                                        <label class="form-check-label" for="estado_perfil">Activo</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <h6>Foto de perfil</h6>
                            <div class="dropzone dz-custom-style" id="upload-form">
                                <div class="dz-message">
                                    <i class="bx bx-cloud-upload upload-icon"></i>
                                    <p>Arrastra o selecciona la foto del usuario</p>
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
                <button type="button" class="btn btn-primary" onclick="guardarUsuario()">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>