$(document).ready(function () {
    
    $(document).on('permisosUsuarioCargados', function() {
        aplicarPermisosUI('Proveedores');
    });

    $('#tipo_ruc1, #tipo_ruc2').on('change', function () {
        id = $(this).attr('id');
        let usuario_id = $('#usuario_id').val();
        
        if ($(this).is(':checked')) {
            if (id == 'tipo_ruc1') {
                $('#div_razon_social').show();

                $('#div_nombre').hide();
                $('#div_apaterno').hide();
                $('#div_amaterno').hide();
            } else {
                $('#div_razon_social').hide();

                $('#div_nombre').show();
                $('#div_apaterno').show();
                $('#div_amaterno').show();
            }
        }
    });

    listarProveedor();
});

function listarProveedor() {
    // Destruir la tabla si ya existe
    if ($.fn.DataTable.isDataTable('#data_proveedor')) {
        $('#data_proveedor').DataTable().clear().destroy();
    }
    
    tabla = $('#data_proveedor').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "searching": true,
        lengthChange: false,
        colReorder: true,
        "ordering": false,
        "iDisplayLength": 10,
        columnDefs: [
            { width: "20%" , targets: 0, className: 'text-left font-weight-bold'},
            { width: "10%" , targets: 1, className: 'text-center'},
            { width: "10%" , targets: 2, className: 'text-center'},
            { width: "15%" , targets: 3, className: 'text-center'},
            { width: "15%" , targets: 4, className: 'text-left'},
            { width: "10%" , targets: 5, className: 'text-center'},
            { width: "10%" , targets: 6, className: 'text-center'}
        ],
        
        "ajax":{
            url: '../../controller/proveedor.php?op=listar',
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
            "emptyTable": '<div class="text-center py-4"><h5>No hay proveedores registrados</h5><p class="text-muted">Comienza agregando un nuevo proveedor</p></div>',
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

function modalNuevo() {
    $('#form-proveedor')[0].reset();
    $('#proveedor_id').val('');
    $('#modalProveedorLabel').text('Nuevo proveedor');

    $('#div_razon_social').show();
    $('#div_nombre').hide();
    $('#div_apaterno').hide();
    $('#div_amaterno').hide();

    $('#modal_proveedor').modal('show');
}

function editarProveedor(id) {

    $("#proveedor_id").val(id);

    $('#modalProveedorLabel').html('Editar proveedor');

    $.post("../../controller/proveedor.php?op=obtener", { id : id }, function (data) {
        data = JSON.parse(data);

        if (data.tipo_ruc == 1) {
            $('#tipo_ruc1').prop('checked', true); 
            $('#tipo_ruc2').prop('checked', false); 

            $('#div_razon_social').show();

            $('#div_nombre').hide();
            $('#div_apaterno').hide();
            $('#div_amaterno').hide();
        } else {
            $('#tipo_ruc1').prop('checked', false); 
            $('#tipo_ruc2').prop('checked', true);

            $('#div_razon_social').hide();

            $('#div_nombre').show();
            $('#div_apaterno').show();
            $('#div_amaterno').show();
        }

        $('#ruc').val(data.ruc);
        $('#nombre_proveedor').val(data.nombre_proveedor);
        $('#apellido_paterno').val(data.apellido_paterno);
        $('#apellido_materno').val(data.apellido_materno);
        $('#razon_social').val(data.razon_social);

        $('#direccion').val(data.direccion);
        $('#telefono').val(data.telefono);
        $('#email').val(data.email);
        $('#contacto').val(data.contacto);
       
        if(data.estado == '2'){
            $('#estado_proveedor').prop('checked', false); 
        } else {
            $('#estado_proveedor').prop('checked', true); 
        }
        
    });

    $('#modal_proveedor').modal('show');
}

function guardarProveedor() {
    // Capturar el formulario y crear FormData
    var formData = new FormData($("#form-proveedor")[0]);
 
    let tipo_ruc = $('input[name="tipo_ruc"]:checked').val();

    let campos = [
        "#ruc"
    ];
    
    // Campos específicos de cada tipo
    const camposJuridico = ["#razon_social"];
    const camposNatural = ["#nombre_proveedor", "#apellido_paterno", "#apellido_materno"];
    
    // Agregar campos según tipo y remover los del otro tipo
    if (tipo_ruc == '1') {
        // Agregar campos jurídicos
        campos = [...campos, ...camposJuridico];
        // Asegurar que NO estén los campos naturales
        campos = campos.filter(campo => !camposNatural.includes(campo));
    } else {
        // Agregar campos naturales
        campos = [...campos, ...camposNatural];
        // Asegurar que NO estén los campos jurídicos
        campos = campos.filter(campo => !camposJuridico.includes(campo));
    }

    campos.push("#email");
    campos.push("#telefono");
    campos.push("#direccion");

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
    formData.set('estado_proveedor', $("#estado_proveedor").is(':checked') ? '1' : '0');
    
    // Enviar por AJAX
    $.ajax({
        url: '../../controller/proveedor.php?op=ingresar_editar_proveedor',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json', // IMPORTANTE: Esperar JSON
        beforeSend: function() {
            $('button[onclick="guardarProveedor()"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');
        },
        success: function (datos) {
            
            if (datos && datos.success == 1) {
                cerrarModal();
                $("#form-proveedor")[0].reset();
                getMessage("success", datos.message || "Proveedor guardado correctamente");

                // Recargar tabla de perfiles si existe
                if (typeof tabla !== 'undefined') {
                    tabla.ajax.reload();
                }
            } else {
                getMessage("error", datos.message || "Error al guardar el proveedor");
            }
        },
        error: function(xhr, status, error) {
            getMessage("error", 'Error al comunicarse con el servidor');
        },
        complete: function() {
            $('button[onclick="guardarProveedor()"]').prop('disabled', false).html('Guardar');
        }
    });
}

function cerrarModal() {
    $("#form-proveedor")[0].reset();
    
    // Resetear checkbox
    $('#estado_proveedor').prop('checked', true);
    
    // Limpiar el usuario_id oculto
    $('#proveedor_id').val('');

    $('#modal_proveedor').modal('hide');
}

function estadoProveedor(proveedor_id, pAccion) {

    let estado = pAccion == 1 ? 'activar' : 'inactivar';

    Swal.fire({
        text: `¿Deseas ${estado} el proveedor?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../../controller/proveedor.php?op=estado_proveedor',
                type: 'POST',
                dataType: 'json',
                data: {
                    proveedor_id : proveedor_id,
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
