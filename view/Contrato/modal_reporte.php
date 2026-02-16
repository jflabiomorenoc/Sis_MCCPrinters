<style>
    /* Reducir el ALTO del contenedor de selección */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 30px !important;
        height: auto !important;
        padding: 6px 12px !important;
        font-size: 0.875rem !important; /* Tamaño de letra menor */
    }

    /* QUITAR el box-shadow y cambiar border de focus */
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: #dc2626 !important;
        box-shadow: none !important;
        outline: none !important;
    }

    /* Border normal (sin focus) */
    .select2-container--bootstrap-5 .select2-selection {
        border: 1px solid #ced4da !important;
    }

    /* Reducir tamaño de las etiquetas/tags seleccionados */
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
        font-size: 0.7rem !important;
        padding: 2px 8px !important;
        margin: 2px !important;
    }

    /* Reducir altura de cada opción en el dropdown */
    .select2-container--bootstrap-5 .select2-results__option {
        padding: 6px 12px !important;
        font-size: 0.75rem !important; /* Tamaño de letra menor */
        color: #212529 !important;
    }

    /* Placeholder en color gris */
    .select2-container--bootstrap-5 .select2-selection__placeholder {
        color: #6c757d !important;
        font-size: 0.875rem !important; /* Tamaño de letra menor */
    }

    /* Campo de búsqueda interno */
    .select2-container--bootstrap-5 .select2-search--inline .select2-search__field {
        height: 28px !important;
        margin: 2px 0 !important;
        font-size: 0.8rem !important; /* Tamaño de letra menor */
        color: #000 !important;
    }

    .select2-container--bootstrap-5 .select2-results__option--highlighted {
        color: white !important;
    }

    .select2-container--bootstrap-5 .select2-results__option--selected {
        background-color: #4e73df !important; /* Rojo claro */
        color: white !important; /* Texto rojo */
        font-weight: 500 !important;
    }
</style>

<div class="modal fade" id="modal_reporte" role="dialog" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-xs" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <svg class="pc-icon icon-svg-primary wid-20 me-2">
                        <use xlink:href="#custom-layer"></use>
                    </svg>
                    <span id="modalReporteLabel"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="cerrarModal();" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="form-reporte">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 mb-3">
                                <label class="form-label">Estado de equipo en contrato <span class="text-danger">*</span></label>
                                <select class="select2 form-control" multiple="multiple" name="estado_equipo[]" id="estado_equipo">
                                    <option></option> <!-- Opción vacía necesaria para el placeholder -->
                                    <option value="vigente" selected>Vigente</option>
                                    <option value="finalizado" selected>Finalizado</option>
                                    <option value="cancelado" selected>Cancelado</option>
                                    <option value="retirado" selected>Retirado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" onclick="cerrarModal();" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="descargarReporte()">
                    Descargar
                </button>
            </div>
        </div>
    </div>
</div>