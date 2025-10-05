$(document).ready(function () {

    $('#div_nombre').hide();
    $('#div_apaterno').hide();
    $('#div_amaterno').hide();

    $('#div_departamento').hide();
    $('#div_provincia').hide();
    $('#div_distrito').hide();
    $('#div_dirección').hide();
    $('#div_referencia').hide();

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

    listarCliente();
});

function listarCliente() {
    // Destruir la tabla si ya existe
    if ($.fn.DataTable.isDataTable('#data_cliente')) {
        $('#data_cliente').DataTable().clear().destroy();
    }
    
    tabla = $('#data_cliente').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "searching": true,
        lengthChange: false,
        colReorder: true,
        "ordering": false,
        "iDisplayLength": 10,
        columnDefs: [
            { width: "22%" , targets: 0, className: 'text-left font-weight-bold', responsivePriority: 1},
            { width: "13%" , targets: 1, className: 'text-center', responsivePriority: 1},
            { width: "13%" , targets: 2, className: 'text-center', responsivePriority: 3},
            { width: "13%" , targets: 3, className: 'text-center', responsivePriority: 2},
            { width: "13%" , targets: 4, className: 'text-center', responsivePriority: 2},
            { width: "13%" , targets: 5, className: 'text-center', responsivePriority: 2},
            { width: "13%" , targets: 6, className: 'text-center', responsivePriority: 2}
        ],
        
        "ajax":{
            url: '../../controller/cliente.php?op=listar',
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
            "emptyTable": '<div class="text-center py-4"><h5>No hay clientes registrados</h5><p class="text-muted">Comienza agregando un nuevo cliente</p></div>',
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
    $('#form-cliente')[0].reset();
    $('#cliente_id').val('');
    $('#modalClienteLabel').text('Nuevo cliente');

    $('#div_departamento').show();
    $('#div_provincia').show();
    $('#div_distrito').show();
    $('#div_dirección').show();
    $('#div_referencia').show();


    $('#modal_cliente').modal('show');
}

/* function editarUsuario(id) {
    $('#div_perfil').hide();
    $('#div_cliente').hide();

    $('#modalUsuarioLabel').html('Editar usuario');
    $("#usuario_id").val(id);

    $.post("../../controller/usuario.php?op=obtener", { id : id }, function (data) {
        data = JSON.parse(data);

        $('#nombres').val(data.nombres);
        $('#apellidos').val(data.apellidos);
        $('#usuario').val(data.usuario);
        $("#select2-rol_usuario-container").html(data.nom_rol); 

        if (data.rol_usuario == 1) {
            $('#rol_usuario1').prop('checked', true); 
            $('#rol_usuario2').prop('checked', false); 
        } else {
            $('#rol_usuario1').prop('checked', false); 
            $('#rol_usuario2').prop('checked', true); 
        }

        // GUARDAR EL ROL ORIGINAL
        rol_usuario_original = data.rol_usuario;

        if (data.cliente_id) {
            $('#div_perfil').show();
        }

        $('#email').val(data.email);
        $('#numero_contacto').val(data.numero_contacto);

        if (data.foto_perfil) {
            var mockFile = {
                name: "foto_perfil.png", // Nombre ficticio
                size: 7024, // Tamaño ficticio en bytes
                type: "image/png"
            };

            // Agrega archivo ficticio a Dropzone
            dropzoneInstance.emit("addedfile", mockFile);
            dropzoneInstance.emit("thumbnail", mockFile, "../../../assets/images/user/" + data.foto_perfil);
            dropzoneInstance.emit("complete", mockFile);

            // Marca que no es archivo nuevo
            dropzoneInstance.files.push(mockFile);

            dropzoneInstance.emit("thumbnail", mockFile, "../../../assets/images/user/" + data.foto_perfil);

            // Limita la imagen después de insertarla
            $(mockFile.previewElement).find("img").css({
                width: "100%",
                height: "auto",
                "object-fit": "cover",
                "max-height": "120px"
            });
        }
       
        if(data.estado == '2'){
            $('#estado_usuario').prop('checked', false); 
        } else {
            $('#estado_usuario').prop('checked', true); 
        }
        
    });

    $('#modal_usuario').modal('show');
} */

function guardarCliente() {
    // Capturar el formulario y crear FormData
    var formData = new FormData($("#form-cliente")[0]);
    
    // Verificar si es edición o registro nuevo
    let esEdicion = $('#cliente_id').val() !== '';

    let tipo_ruc = $('input[name="tipo_ruc"]:checked').val();

    let campos = [
        "#ruc",
        "#departamento",
        "#provincia",
        "#distrito",
        "#direccion",
        "#contacto_principal",
        "#cargo_contacto",
        "#email_contacto",
        "#telefono_contacto"
    ];
    
    // Campos específicos de cada tipo
    const camposJuridico = ["#razon_social"];
    const camposNatural = ["#nombre_cliente", "#apellido_paterno", "#apellido_materno"];
    
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

    // Validación básica de campos requeridos
    for (let i = 0; i < campos.length; i++) {
        if ($(campos[i]).val().trim() === "") {
            let nombreCampo = campos[i].replace("#", "").replace("_", " ");
            getMessage("warning", "El campo " + nombreCampo + " es requerido");
            $(campos[i]).focus();
            return false;
        }
    }

    // En caso de edición, añadir explícitamente el id
    if (esEdicion) {
        formData.append('cliente_id', $('#cliente_id').val());
    }
    
    // Añadir el estado del checkbox correctamente
    formData.set('estado_cliente', $("#estado_cliente").is(':checked') ? '1' : '0');
    
    // Enviar por AJAX
    $.ajax({
        url: '../../controller/cliente.php?op=ingresar_editar_cliente',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json', // IMPORTANTE: Esperar JSON
        beforeSend: function() {
            $('button[onclick="guardarCliente()"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');
        },
        success: function (datos) {
            
            if (datos && datos.success == 1) {
                cerrarModal();
                $("#form-cliente")[0].reset();
                getMessage("success", datos.message || "Cliente guardado correctamente");

                // Recargar tabla de perfiles si existe
                if (typeof tabla !== 'undefined') {
                    tabla.ajax.reload();
                }
            } else {
                getMessage("error", datos.message || "Error al guardar el cliente");
            }
        },
        error: function(xhr, status, error) {
            getMessage("error", 'Error al comunicarse con el servidor');
        },
        complete: function() {
            $('button[onclick="guardarCliente()"]').prop('disabled', false).html('Guardar');
        }
    });
}

function cerrarModal() {
    $("#form-cliente")[0].reset();
    
    // Resetear checkbox
    $('#estado_cliente').prop('checked', true);
    
    // Limpiar el usuario_id oculto
    $('#cliente_id').val('');

    $('#modal_cliente').modal('hide');
}