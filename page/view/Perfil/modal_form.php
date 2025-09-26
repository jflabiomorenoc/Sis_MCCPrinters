<div class="modal fade" id="modal_perfil" role="dialog" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <i data-feather="plus-circle" class="icon-svg-primary wid-20 me-2"></i>
                    <span id="modalPerfilLabel">Añadir perfil</span>
                    
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="form-perfil">
                    <input type="hidden" name="perfil_id" id="perfil_id" value="">
                    <div class="col-md-12 col-sm-12">
                        <div class="col-md-12 col-sm-12 mb-3">
                            <label class="form-label">Nombre de perfil</label>
                            <input class="form-control" type="text" name="nombre_perfil" id="nombre_perfil"/>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch custom-switch-v1 form-check-inline">
                                <input type="checkbox" class="form-check-input input-primary" id="estado_perfil" name="estado_perfil">
                                <label class="form-check-label" for="estado_perfil">Activo</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body table-border-style mt-5">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="d-block m-t-5 mb-0">Configuración de permisos</h5>
                            
                            <!-- Botones de selección masiva -->
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="toggleTodosPermisos('ver')" title="Seleccionar/Deseleccionar todos los permisos de Ver">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="toggleTodosPermisos('crear')" title="Seleccionar/Deseleccionar todos los permisos de Crear">
                                    <i class="fas fa-plus"></i> Crear
                                </button>
                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="toggleTodosPermisos('editar')" title="Seleccionar/Deseleccionar todos los permisos de Editar">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="toggleTodosPermisos('eliminar')" title="Seleccionar/Deseleccionar todos los permisos de Eliminar">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover" id="tabla-permisos">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-semibold">Módulo</th>
                                        <th class="text-center fw-semibold">
                                            <i class="fas fa-eye text-info me-1"></i>Ver
                                        </th>
                                        <th class="text-center fw-semibold">
                                            <i class="fas fa-plus text-success me-1"></i>Crear
                                        </th>
                                        <th class="text-center fw-semibold">
                                            <i class="fas fa-edit text-warning me-1"></i>Editar
                                        </th>
                                        <th class="text-center fw-semibold">
                                            <i class="fas fa-trash text-danger me-1"></i>Eliminar
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