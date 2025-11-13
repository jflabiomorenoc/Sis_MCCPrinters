$(document).ready(function () {
    $(document).on('permisosUsuarioCargados', function() {
        aplicarPermisosUI('Tickets');
    });

    listarTicket();
});

let tabla;
let verTodos = false;

function listarTicket() {
    
    tabla = $('#data_ticket').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "searching": true,
        lengthChange: false,
        
        // ColReorder con columnas fijas
        colReorder: {
            enable: true,
            realtime: true,
            fixedColumnsLeft: 0,
            fixedColumnsRight: 1  // Fijar columna de acciones
        },
        
        // HABILITAR ORDERING pero con orden inicial
        "ordering": true,
        "order": [], // NO establecer orden por defecto, usar el del servidor
        
        "iDisplayLength": 10,
        "autoWidth": false, 
        "responsive": true, 
        
        columnDefs: [
            { width: "8.1%", targets: 0, className: 'text-center', orderable: true },
            { width: "8.1%", targets: 1, className: 'text-center', orderable: true },
            { width: "19%", targets: 2, className: 'text-left', orderable: true },
            { width: "8.1%", targets: 3, className: 'text-center', orderable: true },
            { width: "8.1%", targets: 4, className: 'text-center', orderable: true },
            { width: "8.1%", targets: 5, className: 'text-center', orderable: true },
            { width: "8.1%", targets: 6, className: 'text-center', orderable: true },
            { width: "8.1%", targets: 7, className: 'text-center', orderable: true },
            { width: "8.1%", targets: 8, className: 'text-center', orderable: true },
            { width: "8.1%", targets: 9, className: 'text-center', orderable: true },
            { width: "8.1%", targets: 10, className: 'text-center', orderable: false } // Acciones NO ordenables
        ],
        
        "ajax": {
            url: '../../controller/ticket.php?op=listar',
            type: "post",
            dataType: "json",
            data: function (d) {
                d.ver_todos = verTodos ? 1 : 0; // 🔹 parámetro adicional
            },
            error: function(e) {
                console.log(e.responseText);
            }
        },
        
        // CALLBACK PARA COLOREAR FILAS Y AGREGAR BORDE
        "createdRow": function(row, data, dataIndex) {
            // Obtener el estado desde el data attribute
            let estado = $(row).data('estado');
            
            // Definir color del borde según el estado
            let borderColor = '';
            let bgColor = '';
            
            switch(estado) {
                case 'pendiente':
                    borderColor = '#6c757d'; // secondary
                    break;
                case 'en_proceso':
                    borderColor = '#ffc107'; // warning
                    break;
                case 'resuelto':
                    borderColor = '#198754'; // success
                    break;
                case 'cancelado':
                    borderColor = '#dc3545'; // danger
                    break;
            }
            
            // Aplicar estilos al row
            $(row).css({
                'border-left': '4px solid ' + borderColor,
                'background-color': bgColor
            });
            
            // Agregar clase para identificar el estado
            $(row).addClass('ticket-' + estado);
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
            "emptyTable": '<div class="text-center py-4"><h5>No hay tickets registrados</h5><p class="text-muted">Comienza agregando un nuevo ticket</p></div>',
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

function verContrato(contrato_id) {
    // Mostrar modal
    $('#modal_ver_contrato').modal('show');
    
    // Mostrar loading y ocultar contenido
    $('#loading_contrato').show();
    $('#contenido_contrato').hide();
    
    // Realizar petición AJAX
    $.ajax({
        url: '../../controller/ticket.php?op=ver_contrato',
        type: 'POST',
        data: {
            contrato_id: contrato_id
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                const data = response.data;
                
                // Llenar información del contrato
                $('#contrato_numero').text(data.numero_contrato || '-');
                $('#contrato_cliente').text(data.cliente || '-');
                
                // Fechas
                $('#contrato_fecha_inicio').text(formatDate(data.fecha_inicio) || '-');
                $('#contrato_fecha_culminacion').text(formatDate(data.fecha_culminacion) || '-');
                
                // Calcular y mostrar duración si hay ambas fechas
                if (data.fecha_inicio && data.fecha_culminacion) {
                    const duracion = calcularDuracion(data.fecha_inicio, data.fecha_culminacion);
                    $('#contrato_duracion_texto').text(duracion);
                    $('#contrato_duracion_info').show();
                } else {
                    $('#contrato_duracion_info').hide();
                }
                
                // Estado con badge y colores
                let estadoBadge = '';
                switch(data.estado) {
                    case 'pendiente':
                        estadoBadge = '<span class="badge bg-secondary"><i class="fas fa-circle f-10 me-2"></i>PENDIENTE</span>';
                        break;
                    case 'vigente':
                        estadoBadge = '<span class="badge bg-success"><i class="fas fa-circle f-10 me-2"></i>VIGENTE</span>';
                        break;
                    case 'finalizado':
                        estadoBadge = '<span class="badge bg-info"><i class="fas fa-circle f-10 me-2"></i>FINALIZADO</span>';
                        break;
                    case 'cancelado':
                        estadoBadge = '<span class="badge bg-danger"><i class="fas fa-circle f-10 me-2"></i>CANCELADO</span>';
                        break;
                    default:
                        estadoBadge = '<span class="badge bg-secondary">-</span>';
                }
                $('#contrato_estado').html(estadoBadge);
                
                // Observaciones (mostrar solo si existen)
                if (data.observaciones && data.observaciones.trim() !== '') {
                    $('#contrato_observaciones').text(data.observaciones);
                    $('#card_observaciones').show();
                } else {
                    $('#card_observaciones').hide();
                }
                
                // Ocultar loading y mostrar contenido
                $('#loading_contrato').hide();
                $('#contenido_contrato').show();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo cargar la información del contrato'
                });
                $('#modal_ver_contrato').modal('hide');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al cargar la información del contrato'
            });
            $('#modal_ver_contrato').modal('hide');
        }
    });
}

// Función auxiliar para formatear fechas
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString + 'T00:00:00');
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

// Función para calcular duración del contrato
function calcularDuracion(fechaInicio, fechaFin) {
    const inicio = new Date(fechaInicio + 'T00:00:00');
    const fin = new Date(fechaFin + 'T00:00:00');
    
    const diffTime = Math.abs(fin - inicio);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    const years = Math.floor(diffDays / 365);
    const months = Math.floor((diffDays % 365) / 30);
    const days = (diffDays % 365) % 30;
    
    let duracionTexto = 'Duración del contrato: ';
    
    if (years > 0) {
        duracionTexto += `${years} año${years > 1 ? 's' : ''}`;
    }
    if (months > 0) {
        duracionTexto += `${years > 0 ? ', ' : ''}${months} mes${months > 1 ? 'es' : ''}`;
    }
    if (days > 0 && years === 0) {
        duracionTexto += `${months > 0 ? ' y ' : ''}${days} día${days > 1 ? 's' : ''}`;
    }
    
    duracionTexto += ` (${diffDays} días en total)`;
    
    return duracionTexto;
}

function verEquipo(equipo_id, contrato_id) {
    // Mostrar modal
    $('#modal_ver_equipo').modal('show');
    
    // Mostrar loading y ocultar contenido
    $('#loading_equipo').show();
    $('#contenido_equipo').hide();
    
    // Realizar petición AJAX
    $.ajax({
        url: '../../controller/ticket.php?op=ver_equipo',
        type: 'POST',
        data: {
            equipo_id: equipo_id,
            contrato_id: contrato_id
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                const data = response.data;
                
                // Llenar información del equipo
                $('#equipo_numero_serie').text(data.numero_serie || '-');
                $('#equipo_ip').text(data.ip_equipo || '-');
                $('#equipo_area').text(data.area_ubicacion || '-');
                $('#equipo_direccion').text(data.direccion || '-');
                
                // Tipo de equipo con badge
                if (data.tipo_equipo === 'bn') {
                    $('#equipo_tipo').removeClass().addClass('badge bg-secondary').text('BLANCO Y NEGRO');
                } else {
                    $('#equipo_tipo').removeClass().addClass('badge bg-info').text('COLOR');
                }
                
                // Condición con badge
                if (data.condicion === 'nuevo') {
                    $('#equipo_condicion').removeClass().addClass('badge bg-success').text('NUEVO');
                } else if (data.condicion === 'seminuevo') {
                    $('#equipo_condicion').removeClass().addClass('badge bg-warning').text('SEMINUEVO');
                } else {
                    $('#equipo_condicion').removeClass().addClass('badge bg-secondary').text('USADO');
                }
                
                // Contadores
                $('#equipo_contador_bn').text(formatNumber(data.contador_inicial_bn) || '0');
                $('#equipo_contador_color').text(formatNumber(data.contador_inicial_color) || '0');
                
                // Ocultar loading y mostrar contenido
                $('#loading_equipo').hide();
                $('#contenido_equipo').show();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo cargar la información del equipo'
                });
                $('#modal_ver_equipo').modal('hide');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al cargar la información del equipo'
            });
            $('#modal_ver_equipo').modal('hide');
        }
    });
}

// Función auxiliar para formatear números
function formatNumber(num) {
    if (!num) return '-';
    return parseInt(num).toLocaleString('es-PE');
}

$('#cliente_id').on('change', function() {
    let cliente_id = $(this).val();
    
    // Limpiar contratos y equipos
    $('#contrato_id').html('<option value="">-- Seleccionar --</option>').prop('disabled', true);
    $('#equipo_id').html('<option value="">-- Seleccionar --</option>').prop('disabled', true);
    
    if (cliente_id) {
        // Cargar contratos del cliente seleccionado
        cargarContratosCliente(cliente_id);
    }
});

// Evento cuando cambia el contrato
$('#contrato_id').on('change', function() {
    let contrato_id = $(this).val();
    
    // Limpiar equipos
    $('#equipo_id').html('<option value="">-- Seleccionar --</option>').prop('disabled', true);
    
    if (contrato_id) {
        // Obtener el tecnico_id del data attribute de la opción seleccionada
        let tecnico_id = $(this).find('option:selected').data('tecnico');
        
        // Cargar equipos del contrato seleccionado
        cargarEquiposContrato(contrato_id);
        
        // Si hay un técnico asignado al contrato, seleccionarlo
        if (tecnico_id && tecnico_id !== '') {
            $('#tecnico_id').val(tecnico_id).trigger('change');
        } else {
            // Si no hay técnico asignado, limpiar la selección
            $('#tecnico_id').val('').trigger('change');
        }
    } else {
        // Si no hay contrato seleccionado, limpiar técnico
        $('#tecnico_id').val('').trigger('change');
    }
});

// Cargar combo de clientes que tienen contratos vigentes/pendientes
function cargarClienteContratos(cliente_id_actual = null) {
    $.post("../../controller/cliente.php?op=combo_cliente_contrato", 
        { cliente_id_actual: cliente_id_actual }, 
        function (data, status) {
            $('#cliente_id').html(data);
            
            // Destruir Select2 anterior si existe
            if ($('#cliente_id').hasClass("select2-hidden-accessible")) {
                $('#cliente_id').select2('destroy');
            }
            
            $('#cliente_id').select2({
                placeholder: '-- Seleccionar cliente --',
                width: '100%',
                dropdownParent: $('#modal_ticket'),
                language: {
                    noResults: function () { return "No se encontraron clientes"; },
                    searching: function () { return "Buscando..."; }
                }
            });
            
            // Si hay un cliente_id_actual, seleccionarlo y cargar sus contratos
            if (cliente_id_actual) {
                $('#cliente_id').val(cliente_id_actual).trigger('change');
            }
        }
    );
}

// Cargar contratos del cliente seleccionado (vigentes y pendientes)
function cargarContratosCliente(cliente_id, contrato_id_actual = null) {
    $.post("../../controller/contrato.php?op=combo_contratos_cliente", 
        { cliente_id: cliente_id, contrato_id_actual: contrato_id_actual }, 
        function (data, status) {
            $('#contrato_id').html(data);
            $('#contrato_id').prop('disabled', false);
            
            // Destruir Select2 anterior si existe
            if ($('#contrato_id').hasClass("select2-hidden-accessible")) {
                $('#contrato_id').select2('destroy');
            }
            
            $('#contrato_id').select2({
                placeholder: '-- Seleccionar contrato --',
                width: '100%',
                dropdownParent: $('#modal_ticket'),
                language: {
                    noResults: function () { return "No se encontraron contratos"; },
                    searching: function () { return "Buscando..."; }
                }
            });
            
            // Si hay un contrato_id_actual, seleccionarlo y cargar sus equipos
            if (contrato_id_actual) {
                $('#contrato_id').val(contrato_id_actual).trigger('change');
            }
        }
    );
}

// Cargar equipos del contrato seleccionado (solo vigentes)
function cargarEquiposContrato(contrato_id, equipo_id_actual = null) {
    $.post("../../controller/contrato.php?op=combo_equipos_contrato", 
        { contrato_id: contrato_id, equipo_id_actual: equipo_id_actual }, 
        function (data, status) {
            $('#equipo_id').html(data);
            $('#equipo_id').prop('disabled', false);
            
            // Destruir Select2 anterior si existe
            if ($('#equipo_id').hasClass("select2-hidden-accessible")) {
                $('#equipo_id').select2('destroy');
            }
            
            $('#equipo_id').select2({
                placeholder: '-- Seleccionar equipo --',
                width: '100%',
                dropdownParent: $('#modal_ticket'),
                language: {
                    noResults: function () { return "No se encontraron equipos"; },
                    searching: function () { return "Buscando..."; }
                }
            });
            
            // Si hay un equipo_id_actual, seleccionarlo
            if (equipo_id_actual) {
                $('#equipo_id').val(equipo_id_actual).trigger('change');
            }
        }
    );
}

function cargarTecnicosDisponibles(tecnico_id_actual = null) {
    $.post("../../controller/usuario.php?op=combo_tecnico", 
        { tecnico_id_actual: tecnico_id_actual }, 
        function (data, status) {
            $('#tecnico_id').html(data);

            $('#tecnico_id').select2({
                placeholder: '-- Seleccionar --',
                width: '100%',
                dropdownParent: $('#modal_ticket'),
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

function modalNuevo() {
    $('#form-ticket')[0].reset();
    $('#ticket_id').val('');
    $('#modalTicketLabel').text('Nuevo ticket');

    // Deshabilitar y limpiar contrato
    $('#contrato_id').prop('disabled', true);
    $('#contrato_id').html('<option value="">-- Seleccionar --</option>');

    // Deshabilitar y limpiar equipo
    $('#equipo_id').prop('disabled', true);
    $('#equipo_id').html('<option value="">-- Seleccionar --</option>');

    // Cargar clientes
    cargarClienteContratos();
    cargarTecnicosDisponibles();

    $('#modal_ticket').modal('show');
}

function editarTicket(id) {
    $("#ticket_id").val(id);
    $('#modalTicketLabel').html('Editar ticket');
    
    $.post("../../controller/ticket.php?op=obtener", { id: id }, function (data) {
        data = JSON.parse(data);

        $('#tipo_ticket').val(data.tipo_incidencia);
        $('#fecha_incidencia').val(data.fecha_incidencia);
        $('#descripcion_problema').val(data.descripcion_problema);
        
        // Guardar tecnico_id original antes de cargar
        const tecnicoOriginal = data.tecnico_id;
        
        // Cargar técnicos primero
        cargarTecnicosDisponibles(tecnicoOriginal);
        
        // Iniciar cascada de carga: Cliente -> Contrato -> Equipo
        // Pasar el tecnico_id para que no se sobrescriba
        cargarClienteParaEditar(data.cliente_id, data.contrato_id, data.equipo_id, tecnicoOriginal);
    });
    
    $('#modal_ticket').modal('show');
}

function cargarClienteParaEditar(cliente_id, contrato_id, equipo_id, tecnico_id_original) {
    $.post("../../controller/cliente.php?op=combo_cliente_contrato", 
        { cliente_id_actual: cliente_id }, 
        function (data) {
            $('#cliente_id').html(data);
            
            if ($('#cliente_id').hasClass("select2-hidden-accessible")) {
                $('#cliente_id').select2('destroy');
            }
            
            $('#cliente_id').select2({
                placeholder: '-- Seleccionar cliente --',
                width: '100%',
                dropdownParent: $('#modal_ticket'),
                language: {
                    noResults: function () { return "No se encontraron clientes"; },
                    searching: function () { return "Buscando..."; }
                }
            });
            
            // Seleccionar cliente (sin disparar change para evitar limpiar los demás campos)
            $('#cliente_id').val(cliente_id).trigger('change.select2');
            
            // Cargar contratos pasando el tecnico_id_original
            cargarContratoParaEditar(cliente_id, contrato_id, equipo_id, tecnico_id_original);
        }
    );
}

function cargarContratoParaEditar(cliente_id, contrato_id, equipo_id, tecnico_id_original) {
    $.post("../../controller/contrato.php?op=combo_contratos_cliente", 
        { cliente_id: cliente_id, contrato_id_actual: contrato_id }, 
        function (data) {
            $('#contrato_id').html(data);
            $('#contrato_id').prop('disabled', false);
            
            if ($('#contrato_id').hasClass("select2-hidden-accessible")) {
                $('#contrato_id').select2('destroy');
            }
            
            $('#contrato_id').select2({
                placeholder: '-- Seleccionar contrato --',
                width: '100%',
                dropdownParent: $('#modal_ticket'),
                language: {
                    noResults: function () { return "No se encontraron contratos"; },
                    searching: function () { return "Buscando..."; }
                }
            });
            
            // Seleccionar contrato
            $('#contrato_id').val(contrato_id).trigger('change.select2');
            
            // SOLO cargar técnico del contrato si NO hay tecnico_id_original
            if (!tecnico_id_original) {
                let tecnico_contrato = $('#contrato_id').find('option:selected').data('tecnico');
                if (tecnico_contrato && tecnico_contrato !== '') {
                    $('#tecnico_id').val(tecnico_contrato).trigger('change.select2');
                }
            } else {
                // Si hay tecnico_id_original, usarlo (ya se cargó antes)
                setTimeout(function() {
                    $('#tecnico_id').val(tecnico_id_original).trigger('change.select2');
                }, 300);
            }
            
            // Cargar equipos
            cargarEquipoParaEditar(contrato_id, equipo_id);
        }
    );
}

// Paso 3: Cargar equipo (PARA EDITAR)
function cargarEquipoParaEditar(contrato_id, equipo_id) {
    $.post("../../controller/contrato.php?op=combo_equipos_contrato", 
        { contrato_id: contrato_id, equipo_id_actual: equipo_id }, 
        function (data) {
            $('#equipo_id').html(data);
            $('#equipo_id').prop('disabled', false);
            
            if ($('#equipo_id').hasClass("select2-hidden-accessible")) {
                $('#equipo_id').select2('destroy');
            }
            
            $('#equipo_id').select2({
                placeholder: '-- Seleccionar equipo --',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modal_ticket'),
                language: {
                    noResults: function () { return "No se encontraron equipos"; },
                    searching: function () { return "Buscando..."; }
                }
            });
            
            // Seleccionar equipo
            $('#equipo_id').val(equipo_id).trigger('change.select2');
        }
    );
}

function guardarTicket() {
    // Capturar el formulario y crear FormData
    var formData = new FormData($("#form-ticket")[0]);

    let campos = [
        "#tipo_ticket",
        "#cliente_id",
        "#contrato_id",
        "#fecha_incidencia",
        "#tecnico_id",
        "#descripcion_problema"
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
        url: '../../controller/ticket.php?op=ingresar_editar_ticket',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json', // IMPORTANTE: Esperar JSON
        beforeSend: function() {
            $('button[onclick="guardarTicket()"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');
        },
        success: function (datos) {
            
            if (datos && datos.success == 1) {
                cerrarModal();
                $("#form-ticket")[0].reset();
                getMessage("success", datos.message || "Ticket guardado correctamente");

                // Recargar tabla de perfiles si existe
                if (typeof tabla !== 'undefined') {
                    tabla.ajax.reload();
                }
            } else {
                getMessage("error", datos.message || "Error al guardar el ticket");
            }
        },
        error: function(xhr, status, error) {
            getMessage("error", 'Error al comunicarse con el servidor');
        },
        complete: function() {
            $('button[onclick="guardarTicket()"]').prop('disabled', false).html('Guardar');
        }
    });
}

function cerrarModal() {
    $("#form-ticket")[0].reset();
    
    // Limpiar el contrato_id oculto
    $('#ticket_id').val('');

    $('#modal_ticket').modal('hide');
}