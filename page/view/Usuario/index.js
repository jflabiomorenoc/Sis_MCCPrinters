let rol_usuario_original;
let id;

$(document).ready(function () {
     // Al inicio deshabilitado
    $('#perfil_usuario').prop('disabled', true);
    $('#div_perfil').hide();
    $('#div_cliente').hide();

    // PERFIL (se carga vía ajax)
    $.post("../../controller/perfil.php?op=listar_perfil_combo", function (data, status) {
        $('#perfil_usuario').html(data);

        $('#perfil_usuario').select2({
            placeholder: '-- Seleccionar --',
            width: '100%',
            dropdownParent: $('#modal_usuario'),
            language: {
                noResults: function () { return "No se encontraron resultados"; },
                searching: function () { return "Buscando..."; }
            }
        });
    });

    // CLIENTE (se carga vía ajax)
    cargarClientesDisponibles()

    $('#rol_usuario1, #rol_usuario2').on('change', function () {
        id = $(this).attr('id');
        let usuario_id = $('#usuario_id').val();
        
        if ($(this).is(':checked')) {
            let mostrarPerfil = false;
            
            if (!usuario_id || usuario_id === '') {
                // NUEVO USUARIO
                mostrarPerfil = (id == 'rol_usuario2');
            } else {
                // EDICIÓN
                if (rol_usuario_original == '1' && id == 'rol_usuario2') {
                    mostrarPerfil = true; // Admin → Normal
                } else {
                    mostrarPerfil = false; // Otros casos
                }
            }
            
            // Aplicar
            if (mostrarPerfil) {
                $('#perfil_usuario').prop('disabled', false);
                $('#div_perfil').show();
            } else {
                $('#perfil_usuario').prop('disabled', true).val(null).trigger('change');
                $('#div_perfil').hide();
                $('#div_cliente').hide();
                $('#cliente_id').val(null).trigger('change');
            }
        }
    });

    // Evento para cuando cambia el perfil
    $('#perfil_usuario').on('change', function () {
        let perfil = $(this).val();

        // Solo mostrar cliente si el rol es Normal (2) y el perfil es 1
        if (perfil == "1") {
            $('#div_cliente').show();
        } else {
            $('#div_cliente').hide();
            $('#cliente_id').val(null).trigger('change');
        }
    });

    listarUsuario();
});

function cargarClientesDisponibles() {
    $.post("../../controller/usuario.php?op=listar_usuario_cliente", function (data, status) {
        $('#cliente_id').html(data);

        $('#cliente_id').select2({
            placeholder: '-- Seleccionar --',
            width: '100%',
            dropdownParent: $('#modal_usuario'),
            language: {
                noResults: function () { return "No se encontraron resultados"; },
                searching: function () { return "Buscando..."; }
            }
        });
    });
}

// Inicializar Dropzone
Dropzone.autoDiscover = false;

var myDropzone = new Dropzone("#upload-form", {
    url: "../../controller/usuario.php?op=registrar", // Solo para inicializar
    paramName: "file",
    maxFilesize: 10, // MB
    acceptedFiles: ".jpg,.jpeg,.png,.gif,.bmp,.webp,.avif",
    dictDefaultMessage: "Arrastra los archivos aquí para subirlos o haz clic",
    autoProcessQueue: false,
    uploadMultiple: false,
    maxFiles: 1,
    addRemoveLinks: true,
    dictRemoveFile: "Eliminar",

    init: function () {
        this.on("addedfile", function (file) {
            if (this.files.length > 1) {
                this.removeFile(this.files[0]); // Elimina el primero (antiguo)
            }
            // Ocultar el mensaje e ícono cuando se agrega un archivo
            $('#upload-form .dz-message').hide();
        });

        this.on("removedfile", function (file) {
            // Mostrar el mensaje e ícono si se elimina el archivo
            if (this.files.length === 0) {
                $('#upload-form .dz-message').show();
            }
        });

        this.on("success", function (file, response) {
            console.log("Archivo subido exitosamente", response);
        });

        this.on("error", function (file, errorMessage) {
            console.log("Error al subir el archivo", errorMessage);
        });

        // Guardamos referencia para usar fuera
        window.dropzoneInstance = this;

    }
});

function listarUsuario() {
    // Destruir la tabla si ya existe
    if ($.fn.DataTable.isDataTable('#data_usuario')) {
        $('#data_usuario').DataTable().clear().destroy();
    }
    
    tabla = $('#data_usuario').DataTable({
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
            url: '../../controller/usuario.php?op=listar',
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
            "emptyTable": '<div class="text-center py-4"><h5>No hay usuarios registrados</h5><p class="text-muted">Comienza agregando un nuevo usuario</p></div>',
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
    $('#div_perfil').hide();
    $('#div_cliente').hide();

    $('#form-usuario')[0].reset();
    $('#usuario_id').val('');
    $('#modalUsuarioLabel').text('Nuevo usuario');

    rol_usuario_original = null;

    $('#modal_usuario').modal('show');
}

function editarUsuario(id) {
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
            $('#perfil_usuario').prop('disabled', false);
            $('#div_cliente').show();

            $("#perfil_usuario").val(1);
            $("#select2-perfil_usuario-container").html('Cliente');

            $("#cliente_id").val(data.cliente_id);
            $("#select2-cliente_id-container").html(data.nombre_cliente);
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
}

function guardarUsuario() {
    // Capturar el formulario y crear FormData
    var formData = new FormData($("#form-usuario")[0]);
    
    // Verificar si es edición o registro nuevo
    let esEdicion = $('#usuario_id').val() !== '';

    const campos = [
        "#nombres",
        "#apellidos",
        "#usuario",
        "#numero_contacto",
        "#email"
    ];
    
    // Si no es edición, también validar contraseña
    if (!esEdicion) {
        campos.push("#password");
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

    let rol_usuario = $('input[name="rol_usuario"]:checked').val();
    let perfil_usuario = $('#perfil_usuario').val();
    let cliente_id = $('#cliente_id').val();

    if (rol_usuario === '2') {
        let debeValidarPerfil = false;
        
        if (!esEdicion) {
            // Nuevo usuario Normal debe tener perfil
            debeValidarPerfil = true;
            console.log('→ Validar perfil: Nuevo usuario Normal');
        } else if (rol_usuario_original == '1') {
            // Cambió de Admin → Normal, debe seleccionar perfil
            debeValidarPerfil = true;
            console.log('→ Validar perfil: Cambió de Admin a Normal');
        } else {
            console.log('→ NO validar perfil: Era Normal y sigue Normal');
        }
        
        if (debeValidarPerfil) {
            if (perfil_usuario === '' || perfil_usuario === null) {
                $("#perfil_usuario").focus();
                getMessage("warning", 'Seleccione un Perfil');
                return;
            }

            // Validar cliente solo si el perfil es "1"
            if (perfil_usuario === '1' && (cliente_id === '' || cliente_id === null)) {
                $("#cliente_id").focus();
                getMessage("warning", 'Seleccione un Cliente');
                return;
            }
        }
    }

    // En caso de edición, añadir explícitamente el id
    if (esEdicion) {
        formData.append('usuario_id', $('#usuario_id').val());
    }
    
    // Añadir el estado del checkbox correctamente
    formData.set('estado_usuario', $("#estado_usuario").is(':checked') ? '1' : '0');
    
    // Añadir foto al FormData si hay alguna
    if (typeof myDropzone !== 'undefined' && myDropzone.files.length > 0) {
        formData.append("foto_perfil", myDropzone.files[0]);
    }
    
    // Enviar por AJAX
    $.ajax({
        url: '../../controller/usuario.php?op=ingresar_editar_usuario',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json', // IMPORTANTE: Esperar JSON
        beforeSend: function() {
            $('button[onclick="guardarUsuario()"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');
        },
        success: function (datos) {
            
            if (datos && datos.success == 1) {

                if ($('#cliente_id').hasClass("select2-hidden-accessible")) {
                    $('#cliente_id').select2('destroy');
                }
                $('#cliente_id').empty();

                cargarClientesDisponibles();

                cerrarModal();
                $("#form-usuario")[0].reset();
                getMessage("success", datos.message || "Usuario guardado correctamente");

                // Recargar tabla de perfiles si existe
                if (typeof tabla !== 'undefined') {
                    tabla.ajax.reload();
                }
            } else {
                getMessage("error", datos.message || "Error al guardar el usuario");
            }
        },
        error: function(xhr, status, error) {
            getMessage("error", 'Error al comunicarse con el servidor');
        },
        complete: function() {
            $('button[onclick="guardarUsuario()"]').prop('disabled', false).html('Guardar');
        }
    });
}

function cerrarModal() {
    $("#form-usuario")[0].reset();
    
    // Limpiar Dropzone y mostrar mensaje
    if (window.dropzoneInstance) {
        window.dropzoneInstance.removeAllFiles(true);
        $('#upload-form .dz-message').show();
    }
    
    // Resetear selects de Select2
    $('#rol_usuario').val(null).trigger('change');
    $('#perfil_usuario').val(null).trigger('change');
    $('#cliente_id').val(null).trigger('change');
    
    // Deshabilitar perfil y ocultar cliente
    $('#perfil_usuario').prop('disabled', true);
    $('#div_cliente').hide();
    
    // Resetear checkbox
    $('#estado_usuario').prop('checked', true);
    
    // Limpiar el usuario_id oculto
    $('#usuario_id').val('');

    $('#modal_usuario').modal('hide');
}