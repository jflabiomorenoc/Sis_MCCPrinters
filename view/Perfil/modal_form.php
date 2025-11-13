<div class="modal fade" id="modal_perfil" role="dialog" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <svg class="pc-icon icon-svg-primary wid-20 me-2">
                        <use xlink:href="#custom-share-bold"></use>
                    </svg>
                    <span id="modalPerfilLabel">Añadir perfil</span>
                    
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="form-perfil">
                    <div class="row">
                        <input type="hidden" name="perfil_id" id="perfil_id" value="">
                        <div class="col-md-12 col-sm-12 mb-2">
                            <label class="form-label">Nombre de perfil</label>
                            <input class="form-control form-control-sm" type="text" name="nombre_perfil" id="nombre_perfil"/>
                        </div>
                        <div class="col-md-12 col-sm-12 mt-2 mb-3">
                            <div class="form-check form-switch custom-switch-v1 form-check-inline">
                                <input type="checkbox" class="form-check-input input-primary" id="estado_perfil" name="estado_perfil" checked>
                                <label class="form-check-label" for="estado_perfil">Activo</label>
                            </div>
                        </div>
                    </div>

                    <div class="card-body table-border-style mt-3">
                        <div class="card-header d-flex justify-content-center align-items-center">
                            
                            <!-- Botones de selección masiva -->
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="toggleTodosPermisos('ver')" title="Seleccionar/Deseleccionar todos los permisos de Ver">
                                    Ver
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="toggleTodosPermisos('crear')" title="Seleccionar/Deseleccionar todos los permisos de Crear">
                                    Crear
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="toggleTodosPermisos('editar')" title="Seleccionar/Deseleccionar todos los permisos de Editar">
                                    Editar
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="toggleTodosPermisos('eliminar')" title="Seleccionar/Deseleccionar todos los permisos de Eliminar">
                                    Eliminar
                                </button>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover" id="tabla-permisos">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-semibold">Módulo</th>
                                        <th class="text-center fw-semibold">
                                            Ver
                                        </th>
                                        <th class="text-center fw-semibold">
                                            Crear
                                        </th>
                                        <th class="text-center fw-semibold">
                                            Editar
                                        </th>
                                        <th class="text-center fw-semibold">
                                            Eliminar
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- El contenido se carga dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="guardarPerfil()">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>