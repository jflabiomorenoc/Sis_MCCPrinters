let contrato_id;

$(document).ready(function () {

    $('#liContrato').addClass('active');
    
    $.urlParam = function(name) {
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        if (!results) return 0;
        
        var encrypted = results[1];
        var decrypted = null;
        
        $.ajax({
            url: '../../config/decrypt.php',
            type: 'POST',
            data: { encrypted: encrypted },
            async: false,
            success: function(response) {
                decrypted = response;
            }
        });
        
        return decrypted || 0;
    }
    
    contrato_id = $.urlParam('v');
    $('#contrato_id_equipo').val(contrato_id);
    $('#contrato_id_incidencia').val(contrato_id);

    $(document).on('permisosUsuarioCargados', function() {
        aplicarPermisosUI('Contratos');

        // Cargar información del contrato
        obtenerInfoContrato(contrato_id);
        obtenerEquiposContrato(contrato_id);
    });
    
    // Eventos de botones
    $('#btnNuevo').on('click', function() {
        abrirModalEquipo();
    });

});

function obtenerInfoContrato(contrato_id) {
    $.post("../../controller/contrato.php?op=obtener", { id: contrato_id }, function (data) {
        data = JSON.parse(data);

        $('#lblLiContrato').html(data.numero_contrato);
        $('#lblNumContrato').html(data.numero_contrato);
        $('#lblInicio').html(formatearFecha(data.fecha_inicio));
        $('#lblFin').html(formatearFecha(data.fecha_culminacion));
        $('#lblCliente').html(data.nombre_cliente);
        $('#lblTecnico').html(data.nombre_tecnico || 'Sin asignar');
        
        // Estilo del estado
        const estadoClass = {
            'pendiente': 'bg-light-secondary',
            'vigente': 'bg-light-warning',
            'finalizado': 'bg-light-success',
            'cancelado': 'bg-light-danger'
        };
        
        $('#lblEstado').removeClass('bg-light-warning bg-light-success bg-light-info bg-light-danger bg-light-secondary');
        $('#lblEstado').addClass(estadoClass[data.estado] || 'bg-light-secondary');
        $('#lblEstado').html(data.estado.toUpperCase());

        // ✅ Validar estado Y permisos antes de mostrar botones
        validarAccionesSegunEstado(data.estado);
        cargarDireccionesCliente(data.cliente_id);
    });
}

function cargarDireccionesCliente(cliente_id, direccion_id_actual = null) {
    if (!cliente_id) {
        console.error('No se proporcionó cliente_id');
        return;
    }
    
    $.post("../../controller/cliente.php?op=combo_direccion", 
        { 
            cliente_id: cliente_id,
            direccion_id_actual: direccion_id_actual 
        }, 
        function (data, status) {
            $('#direccion_id').html(data);
            $('#direccion_id').prop('disabled', false);
            
            // ELIMINAR la opción vacía si hay otras opciones disponibles
            const totalOpciones = $('#direccion_id option').length;
            if (totalOpciones > 1) {
                $('#direccion_id option[value=""]').remove();
            }
            
            // Destruir Select2 anterior si existe
            if ($('#direccion_id').hasClass("select2-hidden-accessible")) {
                $('#direccion_id').select2('destroy');
            }
            
            // Obtener el valor a seleccionar
            let valorSeleccionar = null;
            if (direccion_id_actual) {
                valorSeleccionar = direccion_id_actual;
            } else {
                valorSeleccionar = $('#direccion_id option:first').val();
            }
            
            // Establecer el valor ANTES de inicializar Select2
            if (valorSeleccionar) {
                $('#direccion_id').val(valorSeleccionar);
            }
            
            // Inicializar Select2
            $('#direccion_id').select2({
                width: '100%',
                dropdownParent: $('#modal_equipo'),
                language: {
                    noResults: function () { return "No se encontraron direcciones"; },
                    searching: function () { return "Buscando..."; }
                }
                // NO uses placeholder aquí porque fuerza una opción vacía
            });
    
        }
    ).fail(function(xhr, status, error) {
        console.error('Error al cargar direcciones:', error);
    });
}

function validarAccionesSegunEstado(estado) {
    const $btnEditar = $('#btnEditarContrato');
    const $btnCancelar = $('#btnCancelarContrato');
    const $btnFinalizar = $('#btnFinalizarContrato');
    const $btnNuevo = $('#btnNuevo');
    const $dropdownAccion = $('#dropdownAccion');
    
    // Ocultar todos los botones por defecto
    $btnEditar.hide();
    $btnCancelar.hide();
    $btnNuevo.attr('style', 'display: none !important');
    $btnFinalizar.hide();
    $dropdownAccion.hide();
    
    // Ocultar dropdown completo si está cancelado o finalizado
    if (estado === 'cancelado' || estado === 'finalizado') {
        $dropdownAccion.hide();
        $btnNuevo.attr('style', 'display: none !important');
        return;
    }
    
    // Verificar permisos del usuario
    const puedeCrear = tienePermiso('Contratos', 'crear');
    const puedeEditar = tienePermiso('Contratos', 'editar');
    
    // Mostrar dropdown solo si hay acciones disponibles
    let hayAccionesDisponibles = false;
    
    // Mostrar botones según el estado Y los permisos
    if (estado === 'vigente') {
        // Botón Agregar Equipo - solo si tiene permiso de crear
        if (puedeCrear) {
            $btnNuevo.removeAttr('style').show();
        }
        
        // Botones Cancelar y Finalizar - si tiene permiso de editar
        if (puedeEditar) {
            $btnCancelar.show();
            $btnFinalizar.show();
            hayAccionesDisponibles = true;
        }
        
    } else if (estado === 'pendiente') {
        // Botón Agregar Equipo - solo si tiene permiso de crear
        if (puedeCrear) {
            $btnNuevo.removeAttr('style').show();
        }
        
        // Botón Editar - solo si tiene permiso de editar
        if (puedeEditar) {
            $btnEditar.show();
            hayAccionesDisponibles = true;
        }
    }
    
    // Mostrar u ocultar dropdown según si hay acciones disponibles
    if (hayAccionesDisponibles) {
        $dropdownAccion.show();
    } else {
        $dropdownAccion.hide();
    }
}

function estadoContrato(pStrEstado) {

    let mStrText;

    switch(pStrEstado){
        case 'cancelado':
            mStrText = 'cancelar';
            break;
        case 'finalizado':
             mStrText = 'finalizar';
             break;
    }

    Swal.fire({
        text: `¿Deseas ${mStrText} el equipo?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: `Sí, ${mStrText}`,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../../controller/contrato.php?op=estado_contrato',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: contrato_id,
                    estado : pStrEstado
                },
                success: function(response) {
                    if (response.success) {
                        obtenerInfoContrato(contrato_id)
                        obtenerEquiposContrato(contrato_id);
                        getMessage("success", response.message || "Error desconocido");                        
                    } else {
                        getMessage("error", response.message);
                    }
                },
                error: function(xhr, status, error) {
                    getMessage("error", 'Error al eliminar el equipo');
                }
            });
        }
    });
}

// Obtener equipos del contrato
function obtenerEquiposContrato(contrato_id) {
    // Destruir la tabla si ya existe
    if ($.fn.DataTable.isDataTable('#tabla-equipos')) {
        $('#tabla-equipos').DataTable().clear().destroy();
    }
    
    tabla = $('#tabla-equipos').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "searching": true,
        lengthChange: false,
        colReorder: true,
        "ordering": false,
        "iDisplayLength": 10,
        columnDefs: [
            { width: "12%", targets: 0, className: 'text-left' },    // Serie
            { width: "15%", targets: 1, className: 'text-center' },  // Marca/Modelo
            { width: "10%", targets: 2, className: 'text-center' },  // IP
            { width: "12%", targets: 3, className: 'text-center' },  // Ubicación
            { width: "10%", targets: 4, className: 'text-center' },  // Contador BN
            { width: "10%", targets: 5, className: 'text-center' },  // Contador Color
            { width: "10%", targets: 6, className: 'text-center' },  // Estado
            { width: "11%", targets: 7, className: 'text-center' }   // Acciones
        ],
        
        "ajax": {
            url: '../../controller/contrato.php?op=listar_equipos',
            type: "post",
            data: { contrato_id: contrato_id },
            dataType: "json",
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
            "emptyTable": '<div class="text-center py-4"><h5>No hay equipos asignados</h5><p class="text-muted">Comienza agregando un nuevo equipo</p></div>',
            "paginate": {
                "first": '<i class="fas fa-angle-double-left"></i>',
                "last": '<i class="fas fa-angle-double-right"></i>',
                "next": '<i class="fas fa-angle-right"></i>',
                "previous": '<i class="fas fa-angle-left"></i>'
            }
        },
        
        // CONFIGURACIÓN DE DISEÑO
        "dom": '<"row mb-3"<"col-sm-12 col-md-6"><"col-sm-12 col-md-6"<"d-flex justify-content-end"f>>>' +
               '<"row"<"col-sm-12"tr>>' +
               '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
    });
}

// Abrir modal para agregar equipo
function abrirModalEquipo(contrato_equipo_id = null) {
    $('#form-contrato-equipo')[0].reset();
    $('#contrato_equipo_id').val('');
    $('#modal_equipo_title').text('Agregar Equipo');
    
    if (contrato_equipo_id) {
        // Modo edición - Obtener datos primero
        $.post("../../controller/contrato.php?op=obtener_equipo", { id: contrato_equipo_id }, function(data) {
            data = JSON.parse(data);
            
            $('#contrato_equipo_id').val(contrato_equipo_id);
            
            // Cargar equipos y preseleccionar el actual
            cargarEquiposDisponibles(data.equipo_id);
            
            $('#ip_equipo').val(data.ip_equipo);
            $('#area_ubicacion').val(data.area_ubicacion);
            $('#contador_inicial_bn').val(data.contador_inicial_bn);
            $('#contador_final_bn').val(data.contador_final_bn);
            $('#contador_inicial_color').val(data.contador_inicial_color);
            $('#contador_final_color').val(data.contador_final_color);
            
            $('#modal_equipo_title').text('Editar Equipo');

            cargarDireccionesCliente(data.cliente_id, data.direccion_id)
        });
    } else {
        // Modo nuevo
        cargarEquiposDisponibles();
    }
    
    $('#modal_equipo').modal('show');
}

// Cargar equipos disponibles
function cargarEquiposDisponibles(equipo_id_actual = null) {
    $.post("../../controller/equipo.php?op=combo_equipo", 
        { equipo_id_actual: equipo_id_actual }, 
        function(data) {
            // Destruir Select2 si existe
            if ($('#equipo_id').hasClass("select2-hidden-accessible")) {
                $('#equipo_id').select2('destroy');
            }
            
            // Cargar opciones HTML
            $('#equipo_id').html(data);
            
            // Reinicializar Select2
            $('#equipo_id').select2({
                placeholder: '-- Seleccionar --',
                width: '100%',
                dropdownParent: $('#modal_equipo'),
                language: {
                    noResults: function() { return "No hay equipos disponibles"; },
                    searching: function() { return "Buscando..."; }
                }
            });

            // Seleccionar el equipo actual
            if (equipo_id_actual) {
                $('#equipo_id').val(equipo_id_actual).trigger('change');
            }
        }
    );
}

// Evento cuando cambia el equipo seleccionado
$('#equipo_id').on('change', function() {
    const equipo_id = $(this).val();
    
    if (equipo_id) {
        // Obtener información del equipo
        $.post("../../controller/equipo.php?op=obtener", 
            { id: equipo_id }, 
            function(data) {
                data = JSON.parse(data);
                
                // Validar campos de color según el tipo de equipo
                validarCamposColor(data.tipo_equipo);
            }
        );
    } else {
        // Si no hay equipo seleccionado, resetear campos
        validarCamposColor(null);
    }
});

// Función para validar y habilitar/deshabilitar campos de color
function validarCamposColor(tipo_equipo) {
    let $labelInicial = $("label[for='contador_inicial_color']");
    let $labelFinal = $("label[for='contador_final_color']");
    let $inputInicial = $("#contador_inicial_color");
    let $inputFinal = $("#contador_final_color");
    
    if (tipo_equipo === 'color') {
        // Agregar asterisco (campo requerido)
        if ($labelInicial.find('.text-danger').length === 0) {
            $labelInicial.append(' <span class="text-danger">*</span>');
        }
        if ($labelFinal.find('.text-danger').length === 0) {
            $labelFinal.append(' <span class="text-danger">*</span>');
        }
        
        // Habilitar campos
        $inputInicial.prop('disabled', false).removeClass('bg-light');
        $inputFinal.prop('disabled', false).removeClass('bg-light');
        
    } else if (tipo_equipo === 'bn') {
        // Remover asterisco
        $labelInicial.find('.text-danger').remove();
        $labelFinal.find('.text-danger').remove();
        
        // Deshabilitar campos
        $inputInicial.prop('disabled', true).addClass('bg-light').val('');
        $inputFinal.prop('disabled', true).addClass('bg-light').val('');
        
    } else {
        // Si no hay tipo, habilitar pero no requerir
        $labelInicial.find('.text-danger').remove();
        $labelFinal.find('.text-danger').remove();
        $inputInicial.prop('disabled', false).removeClass('bg-light');
        $inputFinal.prop('disabled', false).removeClass('bg-light');
    }
}

// Guardar equipo
function guardarEquipo() {
    var formData = new FormData($("#form-contrato-equipo")[0]);

    let campos = [
        "#equipo_id",
        /* "#ip_equipo", */
        /* "#area_ubicacion", */
        "#contador_inicial_bn",
        "#contador_final_bn"
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

    formData.set('direccion_id', $('#direccion_id').val());

    // Enviar por AJAX
    $.ajax({
        url: '../../controller/contrato.php?op=guardar_equipo',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json', // IMPORTANTE: Esperar JSON
        beforeSend: function() {
            $('button[onclick="guardarEquipo()"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');
        },
        success: function (datos) {
            
            if (datos && datos.success == 1) {
                $('#modal_equipo').modal('hide');
                obtenerInfoContrato(contrato_id)
                obtenerEquiposContrato(contrato_id);
                $("#form-contrato-equipo")[0].reset();
                getMessage("success", datos.message || "Equipo asignado correctamente");
            } else {
                getMessage("error", datos.message || "Error al asignar el equipo");
            }
        },
        error: function(xhr, status, error) {
            getMessage("error", 'Error al comunicarse con el servidor');
        },
        complete: function() {
            $('button[onclick="guardarEquipo()"]').prop('disabled', false).html('Guardar');
        }
    });
}

// Editar equipo
function editarEquipo(contrato_equipo_id) {
    abrirModalEquipo(contrato_equipo_id);
}

// Eliminar equipo
function eliminarEquipo(contrato_equipo_id) {
    Swal.fire({
        text: `¿Deseas eliminar el equipo?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../../controller/contrato.php?op=eliminar_equipo',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: contrato_equipo_id
                },
                success: function(response) {
                    if (response.success) {
                        obtenerInfoContrato(contrato_id)
                        obtenerEquiposContrato(contrato_id);
                        getMessage("success", response.message || "Error desconocido");                        
                    } else {
                        getMessage("error", response.message);
                    }
                },
                error: function(xhr, status, error) {
                    getMessage("error", 'Error al eliminar el equipo');
                }
            });
        }
    });
}