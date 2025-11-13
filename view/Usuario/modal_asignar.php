<div class="modal" id="modal_asignar" role="dialog" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <svg class="pc-icon icon-svg-primary wid-20 me-2">
                        <use xlink:href="#custom-share-bold"></use>
                    </svg>
                    <span id="modalPerfilLabel">Perfiles asignados</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="form-asignar">
                    <input type="hidden" name="usuario_id" id="usuario_id" value="">
                    <div class="col-md-12 col-sm-12">
                        <div class="row align-items-center">
                            <div class="col">
                                <label class="form-label">Seleccionar Perfil <span class="text-danger">*</span></label>
                                <select class="select2 form-control" 
                                        name="perfil_id" 
                                        id="perfil_id"
                                        style="width: 100%;">
                                </select>
                                <small class="text-muted">Si el perfil es <b>CLIENTE</b>, se eliminarán todos los perfiles asignados</small>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-primary btn-sm" type="button" onclick="guardarAsignacion()">Asignar</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body table-border-style mt-5">                        
                        <div class="table-responsive">
                            <table class="table table-hover" id="tabla-perfil">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-semibold">Perfil</th>
                                        <th class="fw-semibold">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>