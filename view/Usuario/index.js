let rol_usuario_original;
let id;
let tabla_perfil;

$(document).ready(function () {

    $(document).on('permisosUsuarioCargados', function() {
        aplicarPermisosUI('Usuarios');
    });

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

    $('#rol_usuario1, #rol_usuario2').on('change', function () {
        id = $(this).attr('id');
        let usuario_id = $('#usuario_id').val();
        let perfil_usuario = $('#perfil_usuario').val();
        
        if ($(this).is(':checked')) {
            let mostrarPerfil = false;
            
            if (!usuario_id || usuario_id === '') {
                // NUEVO USUARIO
                mostrarPerfil = (id == 'rol_usuario2');
            } else {
                // EDICIÓN
                if (rol_usuario_original == '1' && id == 'rol_usuario2') {
                    mostrarPerfil = true; // Admin → Normal
                } else if (perfil_usuario == 1 && id == 'rol_usuario2') {
                    mostrarPerfil = true;
                } else {
                    mostrarPerfil = false;
                }
            }
            
            // Aplicar
            if (mostrarPerfil) {
                $('#perfil_usuario').prop('disabled', false);
                $('#div_perfil').show();

                if(perfil_usuario == 1) {
                   $('#div_cliente').show();
                }
            } else {
                $('#div_perfil').hide();
                $('#div_cliente').hide();

                if(perfil_usuario != 1) {
                    $('#perfil_usuario').prop('disabled', true).val(null).trigger('change');
                    $('#cliente_id').val(null).trigger('change');
                }
            }
        }
    });

    // Evento para cuando cambia el perfil
    $('#perfil_usuario').on('change', function () {
        let perfil = $(this).val();

        // Solo mostrar cliente si el rol es Normal (2) y el perfil es 1
        if (perfil == "1") {
            // CLIENTE (se carga vía ajax)
            cargarClientesDisponibles(null);
            $('#div_cliente').show();
        } else {
            $('#div_cliente').hide();
            $('#cliente_id').val(null).trigger('change');
        }
    });

    listarUsuario();
});

function cargarClientesDisponibles(cliente_id_actual = null) {
    $.post("../../controller/usuario.php?op=listar_usuario_cliente", 
        { cliente_id_actual: cliente_id_actual }, 
        function (data, status) {
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
            
            // Si hay un cliente_id_actual, seleccionarlo
            if (cliente_id_actual) {
                $('#cliente_id').val(cliente_id_actual).trigger('change');
            }
        }
    );
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

    cargarClientesDisponibles(null);

    $('#modal_usuario').modal('show');
}

function editarUsuario(id) {
    if ($('#cliente_id').hasClass("select2-hidden-accessible")) {
        $('#cliente_id').select2('destroy');
    }
    $('#cliente_id').empty();

    $('#div_perfil').hide();
    $('#div_cliente').hide();

    $('#modalUsuarioLabel').html('Editar usuario');
    $("#usuario_id").val(id);

    $.post("../../controller/usuario.php?op=obtener", { id : id }, function (data) {
        data = JSON.parse(data);

        $('#nombres').val(data.nombres);
        $('#apellidos').val(data.apellidos);
        $('#usuario').val(data.usuario);

        if (data.rol_usuario == 1) {
            $('#rol_usuario1').prop('checked', true); 
            $('#rol_usuario2').prop('checked', false); 
        } else {
            $('#rol_usuario1').prop('checked', false); 
            $('#rol_usuario2').prop('checked', true); 
        }

        // GUARDAR EL ROL ORIGINAL
        rol_usuario_original = data.rol_usuario;

        if (rol_usuario_original == 2 && data.perfil_id == null) {
            $('#div_perfil').show();
            $('#perfil_usuario').prop('disabled', false);
        }

        if (data.cliente_id || data.perfil_id == 1) {
            // Cargar la lista con el cliente actual incluido
            cargarClientesDisponibles(data.cliente_id);

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
            dropzoneInstance.emit("thumbnail", mockFile, "../../assets/images/user/" + data.foto_perfil);
            dropzoneInstance.emit("complete", mockFile);

            // Marca que no es archivo nuevo
            dropzoneInstance.files.push(mockFile);

            dropzoneInstance.emit("thumbnail", mockFile, "../../assets/images/user/" + data.foto_perfil);

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
    var formData = new FormData($("#form-usuario")[0]);
    let esEdicion = $('#usuario_id').val() !== '';
    
    const campos = ["#nombres", "#apellidos", "#usuario", "#numero_contacto", "#email"];
    
    if (!esEdicion) {
        campos.push("#password");
    }
    
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
            debeValidarPerfil = true;
        } else if (rol_usuario_original == '1') {
            debeValidarPerfil = true;
        } else if (rol_usuario_original == '2' && ($('#div_perfil').is(':visible'))) {
            debeValidarPerfil = true;
        } else if (perfil_usuario == 1 && (cliente_id === '' || cliente_id === null)) {
            debeValidarPerfil = true;
        }
        
        if (debeValidarPerfil) {
            if (perfil_usuario === '' || perfil_usuario === null) {
                $("#perfil_usuario").focus();
                getMessage("warning", 'Seleccione un Perfil');
                return;
            }
            
            if (perfil_usuario === '1' && (cliente_id === '' || cliente_id === null)) {
                $("#cliente_id").focus();
                getMessage("warning", 'Seleccione un Cliente');
                return;
            }
        }
    }

    for (let pair of formData.entries()) {
        console.log(pair[0] + ':', pair[1]);
    }
    
    if (esEdicion) {
        formData.append('usuario_id', $('#usuario_id').val());
    }
    
    formData.set('estado_usuario', $("#estado_usuario").is(':checked') ? '1' : '0');
    
    if (typeof myDropzone !== 'undefined' && myDropzone.files.length > 0) {
        formData.append("foto_perfil", myDropzone.files[0]);
    }
    
    $.ajax({
        url: '../../controller/usuario.php?op=ingresar_editar_usuario',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        beforeSend: function() {
            $('button[onclick="guardarUsuario()"]').prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');
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

function asignarPerfil(usuario_id) {
    $('#usuario_id').val(usuario_id);

    // Limpiar Select2 si existe
    if ($('#perfil_id').hasClass("select2-hidden-accessible")) {
        $('#perfil_id').select2('destroy');
    }
    $('#usuario_id').empty();

    cargarPerfilesDisponibles(usuario_id);

    cargarPerfilesporUsuario(usuario_id);

    $('#modal_asignar').modal('show');
}

function cargarPerfilesDisponibles(usuario_id) {
    $.post("../../controller/perfil.php?op=listar_perfiles_x_usuario", 
        { usuario_id: usuario_id }, 
        function (data, status) {
            $('#perfil_id').html(data);

            $('#perfil_id').select2({
                placeholder: '-- Seleccionar --',
                width: '100%',
                dropdownParent: $('#modal_asignar'),
                language: {
                    noResults: function () { return "No se encontraron resultados"; },
                    searching: function () { return "Buscando..."; }
                }
            });
        }
    );
}

function mostrarSkeletonLoader() {
    const skeletonHTML = `
        <tr class="skeleton-row">
            <td><div class="skeleton-text"></div></td>
            <td><div class="skeleton-badge"></div></td>
        </tr>
        <tr class="skeleton-row">
            <td><div class="skeleton-text"></div></td>
            <td><div class="skeleton-badge"></div></td>
        </tr>
        <tr class="skeleton-row">
            <td><div class="skeleton-text"></div></td>
            <td><div class="skeleton-badge"></div></td>
        </tr>
    `;
    
    $('#tabla-perfil tbody').html(skeletonHTML);
}

// Función para ocultar skeleton loader
function ocultarSkeletonLoader() {
    $('#tabla-perfil tbody .skeleton-row').remove();
}

function cargarPerfilesporUsuario(usuario_id) {
    usuario_id_actual = usuario_id;
    
    if ($.fn.DataTable.isDataTable('#tabla-perfil')) {
        // Mostrar skeleton antes de recargar
        mostrarSkeletonLoader();
        
        // Recargar los datos
        tabla_perfil.ajax.reload(function() {
            // El skeleton se ocultará automáticamente al dibujar la tabla
        }, false);
    } else {
        // Inicializar la tabla por primera vez
        tabla_perfil = $('#tabla-perfil').DataTable({
            "aProcessing": true,
            "aServerSide": true,
            "searching": false,
            lengthChange: false,
            colReorder: true,
            "ordering": false,
            info: false,
            "iDisplayLength": 5,
            columnDefs: [
                { width: "80%" , targets: 0, className: 'text-left font-weight-bold'},
                { width: "20%" , targets: 1, className: 'text-center'},
            ],
            "ajax":{
                url: '../../controller/usuario.php?op=obtener_perfiles',
                type: "post",
                dataType: "json",
                data: function(d) {
                    d.usuario_id = usuario_id_actual;
                },
                // Deshabilitar el processing por defecto
                beforeSend: function() {
                    mostrarSkeletonLoader();
                },					
                error: function(e){
                    console.log(e.responseText);
                    ocultarSkeletonLoader();
                }
            },
            "processing": false, // Deshabilitar el mensaje de processing
            "drawCallback": function(settings) {
                // Ocultar skeleton cuando termine de dibujar
                ocultarSkeletonLoader();
            },
            "language": {
                "search": "Buscar:",
                "lengthMenu": "Mostrar _MENU_ registros",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "loadingRecords": "Cargando...",
                "zeroRecords": '<div class="text-center py-4"><i class="fas fa-inbox fa-3x text-muted mb-3"></i><h5 class="text-muted">No hay datos disponibles</h5><p class="text-muted">No se encontraron registros que mostrar</p></div>',
                "emptyTable": '<div class="text-center py-4"><h5>No hay perfiles asignados</h5><p class="text-muted">Este usuario no tiene perfiles asignados</p></div>',
                "paginate": {
                    "first": '<i class="fas fa-angle-double-left"></i>',
                    "last": '<i class="fas fa-angle-double-right"></i>',
                    "next": '<i class="fas fa-angle-right"></i>',
                    "previous": '<i class="fas fa-angle-left"></i>'
                }
            },
            "dom": '<"row"<"col-sm-12"tr>>' +
                   '<"row mt-3"<"col-sm-12 d-flex justify-content-center"p>>',
        });
    }
}

function guardarAsignacion(){
    let usuario_id    = $('#usuario_id').val();
    let perfil_id = $('#perfil_id').val();

    if (!perfil_id ) {
        getMessage("warning", "Debe seleccionar al menos un perfil");
        return false;
    }

    // Enviar datos al servidor
    $.ajax({
        url: '../../controller/perfil.php?op=asignar_perfil',
        type: 'POST',
        dataType: 'json',
        data: {
            usuario_id: usuario_id,
            perfil_id: perfil_id,
        },
        success: function(response) {
            Swal.close();
            
            if (response.success) {
                
                tabla.ajax.reload();

                getMessage("success", response.message);
                
                if ($('#perfil_id').hasClass("select2-hidden-accessible")) {
                    $('#perfil_id').select2('destroy');
                }
            
                $('#perfil_id').empty();

                cargarPerfilesDisponibles(usuario_id);
                
                if (tabla_perfil) {
                    tabla_perfil.ajax.reload();
                }
                
            } else {
                getMessage("error", response.message);
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            getMessage("error", 'Error al asignar los usuarios. Por favor, intente nuevamente.');
        }
    });
}

function eliminarPerfil(usuario_perfil_id){
    Swal.fire({
        text: `¿Eliminar la asignación de este perfil al usuario?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../../controller/perfil.php?op=eliminar_usuario_perfil',
                type: 'POST',
                dataType: 'json',
                data: {
                    usuario_perfil_id : usuario_perfil_id
                },
                success: function(response) {
                    if (response.success) {
                        let usuario_id = $('#usuario_id').val();
                        // Inicializar Select2 con usuarios disponibles
                        cargarPerfilesDisponibles(usuario_id);

                        tabla.ajax.reload();

                        getMessage("success", response.message || "Error desconocido");
                        
                        // Recargar tabla de perfiles si existe
                        if (typeof tabla_perfil !== 'undefined') {
                            tabla_perfil.ajax.reload();
                        }
                        
                    } else {
                        getMessage("error", response.message);
                    }
                },
                error: function(xhr, status, error) {
                    getMessage("error", 'Error al eliminar el perfil');
                }
            });
        }
    });
}

// Función para eliminar perfil
function estadoUsuario(usuario_id, pAccion) {

    let estado = pAccion == 1 ? 'activar' : 'inactivar';

    Swal.fire({
        text: `¿Deseas ${estado} el usuario?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../../controller/usuario.php?op=estado_usuario',
                type: 'POST',
                dataType: 'json',
                data: {
                    usuario_id  : usuario_id,
                    estado      : pAccion
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
