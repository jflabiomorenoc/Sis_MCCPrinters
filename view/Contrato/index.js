$(document).ready(function () {

    $(document).on('permisosUsuarioCargados', function() {
        aplicarPermisosUI('Contratos');
    });

    listarContrato();
    $('#direccion_id').prop('disabled', true);
    
    // Evento para limpiar al cerrar el modal
    $('#modal_contrato').on('hidden.bs.modal', function () {
        // Destruir Select2 de cliente
        if ($('#cliente_id').hasClass("select2-hidden-accessible")) {
            $('#cliente_id').select2('destroy');
        }
        
        // Destruir Select2 de dirección
        if ($('#direccion_id').hasClass("select2-hidden-accessible")) {
            $('#direccion_id').select2('destroy');
        }
        
        // Destruir Select2 de técnico
        if ($('#tecnico_id').hasClass("select2-hidden-accessible")) {
            $('#tecnico_id').select2('destroy');
        }
        
        // Resetear el formulario
        $('#form-contrato')[0].reset();
        
        // Restablecer estados
        $('#direccion_id').prop('disabled', true);
        $('#cliente_id').data('modo-edicion', false);
    });
});

function cargarClienteDisponibles(cliente_id_actual = null) {
    $.post("../../controller/cliente.php?op=combo_cliente", 
        { cliente_id_actual: cliente_id_actual }, 
        function (data, status) {
            $('#cliente_id').html(data);
            
            // Destruir Select2 anterior si existe
            if ($('#cliente_id').hasClass("select2-hidden-accessible")) {
                $('#cliente_id').select2('destroy');
            }
            
            $('#cliente_id').select2({
                placeholder: '-- Seleccionar --',
                width: '100%',
                dropdownParent: $('#modal_contrato'),
                language: {
                    noResults: function () { return "No se encontraron resultados"; },
                    searching: function () { return "Buscando..."; }
                }
            });
            
            // Si hay un cliente_id_actual, seleccionarlo
            if (cliente_id_actual) {
                $('#cliente_id').val(cliente_id_actual).trigger('change');
            }
        }
    );
}

// Función para cargar técnicos disponibles
function cargarTecnicosDisponibles(tecnico_id_actual = null) {
    $.post("../../controller/usuario.php?op=combo_tecnico", 
        { tecnico_id_actual: tecnico_id_actual }, 
        function (data, status) {
            $('#tecnico_id').html(data);

            $('#tecnico_id').select2({
                placeholder: '-- Seleccionar --',
                width: '100%',
                dropdownParent: $('#modal_contrato'),
                language: {
                    noResults: function () { return "No se encontraron técnicos"; },
                    searching: function () { return "Buscando..."; }
                }
            });
            
            // Si hay un tecnico_id_actual, seleccionarlo
            if (tecnico_id_actual) {
                $('#tecnico_id').val(tecnico_id_actual).trigger('change');
            }
        }
    );
}

let tabla;
let verTodos = false;

function listarContrato() {
       
    tabla = $('#data_contrato').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "searching": true,
        lengthChange: false,
        colReorder: true,
        "ordering": false,
        "iDisplayLength": 10,
        columnDefs: [
            { width: "15%" , targets: 0, className: 'text-center'},
            { width: "25%" , targets: 1, className: 'text-left'},
            { width: "15%" , targets: 2, className: 'text-center'},
            { width: "15%" , targets: 3, className: 'text-center'},
            { width: "15%" , targets: 4, className: 'text-center'},
            { width: "15%" , targets: 5, className: 'text-center'}
        ],
        
        "ajax":{
            url: '../../controller/contrato.php?op=listar',
            type : "post",
            dataType : "json",						
            data: function (d) {
                d.ver_todos = verTodos ? 1 : 0; // 🔹 parámetro adicional
            },
            error: function(e) {
                console.log(e.responseText);
            }
        },
        
        // IDIOMA MEJORADO
        "language": {
            "processing": '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Cargando...</span></div></div>',
            "search": "Buscar:",
            "lengthMenu": "Mostrar _MENU_ registros",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "loadingRecords": "Cargando...",
            "zeroRecords": '<div class="text-center py-4"><i class="fas fa-inbox fa-3x text-muted mb-3"></i><h5 class="text-muted">No hay datos disponibles</h5><p class="text-muted">No se encontraron registros que mostrar</p></div>',
            "emptyTable": '<div class="text-center py-4"><h5>No hay contratos registrados</h5><p class="text-muted">Comienza agregando un nuevo contrato</p></div>',
            "paginate": {
                "first": '<i class="fas fa-angle-double-left"></i>',
                "last": '<i class="fas fa-angle-double-right"></i>',
                "next": '<i class="fas fa-angle-right"></i>',
                "previous": '<i class="fas fa-angle-left"></i>'
            }
        },
        
        // CONFIGURACIÓN DE DISEÑO
        "dom": '<"row mb-3"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"<"d-flex justify-content-end"f>>>' +
               '<"row"<"col-sm-12"tr>>' +
               '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
    });
}

function toggleVerTodos() {
    verTodos = !verTodos;
    $('#btnVerTodos')
        .text(verTodos ? 'Ver asignados' : 'Ver todos')
        .toggleClass('btn-outline-secondary btn-outline-success');
    
    // 🔹 recarga sin parpadeo
    tabla.ajax.reload(null, false);
}

function modalNuevo() {
    $('#form-contrato')[0].reset();
    $('#contrato_id').val('');
    $('#modalContratoLabel').text('Nuevo contrato');

    // Marcar como modo nuevo (NO edición)
    $('#cliente_id').data('modo-edicion', false);

    // Deshabilitar y limpiar direcciones
    $('#direccion_id').prop('disabled', true);
    $('#direccion_id').html('<option value="">-- Seleccionar --</option>');

    // Cargar clientes
    cargarClienteDisponibles();
    
    // Cargar técnicos
    cargarTecnicosDisponibles();

    $('#modal_contrato').modal('show');
    
    // Activar modo edición después de un delay
    setTimeout(function() {
        $('#cliente_id').data('modo-edicion', true);
    }, 500);
}

function editarContrato(id) {
    $("#contrato_id").val(id);
    $('#modalContratoLabel').html('Editar Contrato');
    
    // Marcar como modo edición desde el inicio
    $('#cliente_id').data('modo-edicion', true);
    
    $.post("../../controller/contrato.php?op=obtener", { id: id }, function (data) {
        data = JSON.parse(data);
        
        // Cargar clientes primero
        cargarClienteDisponibles(data.cliente_id);
        
        // Cargar técnicos
        cargarTecnicosDisponibles(data.tecnico_id);
        
        // Llenar otros campos
        $('#fecha_inicio').val(data.fecha_inicio);
        $('#fecha_culminacion').val(data.fecha_culminacion);
        $('#observaciones').val(data.observaciones);
    });
    
    $('#modal_contrato').modal('show');
}

function guardarContrato() {
    // Capturar el formulario y crear FormData
    var formData = new FormData($("#form-contrato")[0]);

    let campos = [
        "#cliente_id",
        "#fecha_inicio"
    ];

    // Validación básica de campos requeridos
    for (let i = 0; i < campos.length; i++) {
        if ($(campos[i]).val().trim() === "") {
            let nombreCampo = campos[i].replace("#", "").replace("_", " ").replace("id", "");
            getMessage("warning", "El campo " + nombreCampo + " es requerido");
            $(campos[i]).focus();
            return false;
        }
    }
    
    // Enviar por AJAX
    $.ajax({
        url: '../../controller/contrato.php?op=ingresar_editar_contrato',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json', // IMPORTANTE: Esperar JSON
        beforeSend: function() {
            $('button[onclick="guardarContrato()"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');
        },
        success: function (datos) {
            
            if (datos && datos.success == 1) {
                cerrarModal();
                $("#form-contrato")[0].reset();
                getMessage("success", datos.message || "Contrato guardado correctamente");

                // Recargar tabla de perfiles si existe
                if (typeof tabla !== 'undefined') {
                    tabla.ajax.reload();
                }
            } else {
                getMessage("error", datos.message || "Error al guardar el contrato");
            }
        },
        error: function(xhr, status, error) {
            getMessage("error", 'Error al comunicarse con el servidor');
        },
        complete: function() {
            $('button[onclick="guardarContrato()"]').prop('disabled', false).html('Guardar');
        }
    });
}

function cerrarModal() {
    $("#form-contrato")[0].reset();
    
    // Limpiar el contrato_id oculto
    $('#contrato_id').val('');

    $('#modal_contrato').modal('hide');
}