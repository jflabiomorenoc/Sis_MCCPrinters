$(document).ready(function () {

    $(document).on('permisosUsuarioCargados', function() {
        aplicarPermisosUI('Equipos');
    });

    listarEquipo();

    cargarProveedorDisponible()
});

// Evento change simplificado
$("#tipo_equipo").on('change', function() {
    actualizarCamposColor($(this).val());
});

function cargarProveedorDisponible(proveedor_id_actual = null) {
    $.post("../../controller/proveedor.php?op=combo_proveedor", 
        { proveedor_id_actual: proveedor_id_actual }, 
        function (data, status) {
            $('#proveedor_id').html(data);

            $('#proveedor_id').select2({
                placeholder: '-- Seleccionar --',
                width: '100%',
                dropdownParent: $('#modal_equipo'),
                language: {
                    noResults: function () { return "No se encontraron resultados"; },
                    searching: function () { return "Buscando..."; }
                }
            });

            // Si hay un cliente_id_actual, seleccionarlo
            if (proveedor_id_actual) {
                $('#proveedor_id').val(proveedor_id_actual).trigger('change');
            }
        }
    );
}

function listarEquipo() {
    // Destruir la tabla si ya existe
    if ($.fn.DataTable.isDataTable('#data_equipo')) {
        $('#data_equipo').DataTable().clear().destroy();
    }
    
    tabla = $('#data_equipo').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "searching": true,
        lengthChange: false,
        colReorder: true,
        "ordering": false,
        "iDisplayLength": 10,
        columnDefs: [
            { width: "10%" , targets: 0, className: 'text-center'},
            { width: "10%" , targets: 1, className: 'text-center'},
            { width: "10%" , targets: 2, className: 'text-center'},
            { width: "15%" , targets: 3, className: 'text-center'},
            { width: "15%" , targets: 4, className: 'text-center'},
            { width: "10%" , targets: 5, className: 'text-left'},
            { width: "10%" , targets: 6, className: 'text-center'},
            { width: "10%" , targets: 7, className: 'text-center'}
        ],
        
        "ajax":{
            url: '../../controller/equipo.php?op=listar',
            type : "post",
            dataType : "json",						
            error: function(e){
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
            "emptyTable": '<div class="text-center py-4"><h5>No hay equipos registrados</h5><p class="text-muted">Comienza agregando un nuevo equipo</p></div>',
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

// Función para actualizar el estado de los campos de color
function actualizarCamposColor(tipoEquipo) {
    let $label1 = $("label[for='contador_inicial_color']");
    let $label2 = $("label[for='contador_actual_color']");
    let $input1 = $("#contador_inicial_color");
    let $input2 = $("#contador_actual_color");

    if (tipoEquipo == "color") {
        // Agregar asterisco si no existe
        if ($label1.find('.text-danger').length === 0) {
            $label1.append(' <span class="text-danger">*</span>');
        }
        if ($label2.find('.text-danger').length === 0) {
            $label2.append(' <span class="text-danger">*</span>');
        }
        
        // Habilitar campos
        $input1.prop('disabled', false).removeClass('bg-light');
        $input2.prop('disabled', false).removeClass('bg-light');
        
    } else if (tipoEquipo == "bn") {
        // Remover asterisco
        $label1.find('.text-danger').remove();
        $label2.find('.text-danger').remove();
        
        // Deshabilitar campos
        $input1.prop('disabled', true).addClass('bg-light');
        $input2.prop('disabled', true).addClass('bg-light');
        
        // Limpiar valores solo si NO estamos en modo edición
        if (!$('#equipo_id').val()) {
            $input1.val('');
            $input2.val('');
        }
        
    } else {
        // Si no seleccionó ninguno
        $label1.find('.text-danger').remove();
        $label2.find('.text-danger').remove();
        $input1.prop('disabled', false).removeClass('bg-light');
        $input2.prop('disabled', false).removeClass('bg-light');
    }
}

function modalNuevo() {
    $('#form-equipo')[0].reset();
    $('#equipo_id').val('');
    $('#modalEquipoLabel').text('Nuevo equipo');
    
    // Resetear campos de color al estado inicial
    actualizarCamposColor('');

    $('#modal_equipo').modal('show');
}

function editarEquipo(id) {
    $("#equipo_id").val(id);
    $('#modalEquipoLabel').html('Editar equipo');

    $.post("../../controller/equipo.php?op=obtener", { id : id }, function (data) {
        data = JSON.parse(data);

        $('#marca').val(data.marca);
        $('#modelo').val(data.modelo);
        $('#numero_serie').val(data.numero_serie);
        $('#tipo_equipo').val(data.tipo_equipo);
        $('#condicion').val(data.condicion);

        if(data.estado == 'inactivo'){
            $('#estado').prop('checked', false); 
        } else {
            $('#estado').prop('checked', true); 
        }

        $('#proveedor_id').val(data.proveedor_id);
        $("#select2-proveedor_id-container").html(data.proveedor);

        $('#fecha_compra').val(data.fecha_compra);
        $('#costo_dolares').val(data.costo_dolares);
        $('#costo_soles').val(data.costo_soles);
        $('#contador_inicial_bn').val(data.contador_inicial_bn);
        $('#contador_inicial_color').val(data.contador_inicial_color);
        $('#contador_actual_bn').val(data.contador_actual_bn);
        $('#contador_actual_color').val(data.contador_actual_color);
        $('#observaciones').val(data.observaciones);

        // Actualizar estado de campos según el tipo
        actualizarCamposColor(data.tipo_equipo);
    });

    $('#modal_equipo').modal('show');
}

function guardarEquipo() {
    // Capturar el formulario y crear FormData
    var formData = new FormData($("#form-equipo")[0]);
 
    let campos = [
        "#marca",
        "#modelo",
        "#numero_serie",
        "#tipo_equipo",
        "#condicion",
        "#proveedor_id",
        "#fecha_compra",
        "#costo_dolares",
        "#costo_soles",
        "#contador_inicial_bn",
        "#contador_actual_bn"
    ];

    if ($("#tipo_equipo").val() == "color") {
        campos.push("#contador_inicial_color");
        campos.push("#contador_actual_color");
    }

    // Validación básica de campos requeridos
    for (let i = 0; i < campos.length; i++) {
        if ($(campos[i]).val().trim() === "") {
            let nombreCampo = campos[i].replace("#", "").replace("_", " ");
            getMessage("warning", "El campo " + nombreCampo + " es requerido");
            $(campos[i]).focus();
            return false;
        }
    }

    // Añadir el estado del checkbox correctamente
    formData.set('estado', $("#estado").is(':checked') ? 'activo' : 'inactivo');
    
    // Enviar por AJAX
    $.ajax({
        url: '../../controller/equipo.php?op=ingresar_editar_equipo',
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
                cerrarModal();
                $("#form-equipo")[0].reset();
                getMessage("success", datos.message || "Equipo guardado correctamente");

                // Recargar tabla de perfiles si existe
                if (typeof tabla !== 'undefined') {
                    tabla.ajax.reload();
                }
            } else {
                getMessage("error", datos.message || "Error al guardar el equipo");
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

function cerrarModal() {
    $("#form-equipo")[0].reset();
    
    // Resetear checkbox
    $('#estado_equipo').prop('checked', true);
    
    // Limpiar el usuario_id oculto
    $('#equipor_id').val('');

    $('#modal_equipo').modal('hide');
}

function estadoEquipo(equipo_id, pAccion) {

    let estado = pAccion == 1 ? 'activar' : 'inactivar';

    Swal.fire({
        text: `¿Deseas ${estado} el equipo?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../../controller/equipo.php?op=estado_equipo',
                type: 'POST',
                dataType: 'json',
                data: {
                    equipo_id : equipo_id,
                    estado       : pAccion
                },
                success: function(response) {
                    if (response.success) {
                        getMessage("success", response.message || "Error desconocido");
                        
                        // Recargar tabla de perfiles si existe
                        if (typeof tabla !== 'undefined') {
                            tabla.ajax.reload();
                        }
                        
                    } else {
                        getMessage("error", response.message);
                    }
                },
                error: function(xhr, status, error) {
                    getMessage("error", 'Error al actualizar el estado');
                }
            });
        }
    });
}