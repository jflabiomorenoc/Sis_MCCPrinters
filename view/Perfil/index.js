let tabla_usuario;

$(document).ready(function(){

    $(document).on('permisosUsuarioCargados', function() {
        aplicarPermisosUI('Perfiles');
    });
    
    listarPerfil()
    cargarModulosPermisos();
});

function listarPerfil(){
    tabla = $('#data_perfil').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "searching": true,
        lengthChange: false,
        colReorder: true,
        "ordering": false,
        "iDisplayLength": 10,
        columnDefs: [
            { width: "35%" , targets: 0, className: 'text-left font-weight-bold', responsivePriority: 1},
            { width: "30%" , targets: 1, className: 'text-center', responsivePriority: 3},
            { width: "20%" , targets: 2, className: 'text-center', responsivePriority: 2},
            { width: "15%" , targets: 3, className: 'text-center', responsivePriority: 2}
        ],
        
        "ajax":{
            url: '../../controller/perfil.php?op=listar',
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
            "emptyTable": '<div class="text-center py-4"><h5>No hay perfiles registrados</h5><p class="text-muted">Comienza agregando un nuevo perfil</p></div>',
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

function modalAsignar(perfil_id) {

    $('#perfil_id').val(perfil_id);

    // Limpiar Select2 si existe
    if ($('#usuario_id').hasClass("select2-hidden-accessible")) {
        $('#usuario_id').select2('destroy');
    }
    $('#usuario_id').empty();

    cargarUsuariosDisponibles(perfil_id);

    cargarUsuariosPorPerfil(perfil_id);

    $('#modal_asignar').modal('show');
}

let perfil_id_actual = null;

// Función para mostrar skeleton loader
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
    
    $('#tabla-usuario tbody').html(skeletonHTML);
}

// Función para ocultar skeleton loader
function ocultarSkeletonLoader() {
    $('#tabla-usuario tbody .skeleton-row').remove();
}

function cargarUsuariosPorPerfil(perfil_id) {
    perfil_id_actual = perfil_id;
    
    if ($.fn.DataTable.isDataTable('#tabla-usuario')) {
        // Mostrar skeleton antes de recargar
        mostrarSkeletonLoader();
        
        // Recargar los datos
        tabla_usuario.ajax.reload(function() {
            // El skeleton se ocultará automáticamente al dibujar la tabla
        }, false);
    } else {
        // Inicializar la tabla por primera vez
        tabla_usuario = $('#tabla-usuario').DataTable({
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
                url: '../../controller/perfil.php?op=obtener_usuarios',
                type: "post",
                dataType: "json",
                data: function(d) {
                    d.perfil_id = perfil_id_actual;
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
                "emptyTable": '<div class="text-center py-4"><h5>No hay usuarios asignados</h5><p class="text-muted">Este perfil no tiene usuarios asignados</p></div>',
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

function cargarUsuariosDisponibles(perfil_id) {
    $.ajax({
        url: '../../controller/perfil.php?op=obtener_usuarios_disponibles',
        type: 'POST',
        dataType: 'json',
        data: { perfil_id: perfil_id },
        success: function(response) {
            if (response.success) {
                // Inicializar Select2
                $('#usuario_id').select2({
                    placeholder: 'Seleccionar usuarios...',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#modal_asignar'),
                    language: {
                        noResults: function() {
                            return "No se encontraron usuarios disponibles";
                        },
                        searching: function() {
                            return "Buscando...";
                        }
                    },
                    data: response.data
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: response.message || 'No hay usuarios disponibles'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar usuarios:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al cargar los usuarios disponibles'
            });
        }
    });
}

function guardarAsignacion(){
    let perfil_id    = $('#perfil_id').val();
    let usuarios_ids = $('#usuario_id').val();

    if (!usuarios_ids || usuarios_ids.length === 0) {
        getMessage("warning", "Debe seleccionar al menos un usuario");
        return false;
    }

    // Enviar datos al servidor
    $.ajax({
        url: '../../controller/perfil.php?op=asignar_usuarios',
        type: 'POST',
        dataType: 'json',
        data: {
            perfil_id: perfil_id,
            usuarios_ids: usuarios_ids
        },
        success: function(response) {
            Swal.close();
            
            if (response.success) {
                
                tabla.ajax.reload();

                getMessage("success", response.message);
                
                if ($('#usuario_id').hasClass("select2-hidden-accessible")) {
                    $('#usuario_id').select2('destroy');
                }
            
                $('#usuario_id').empty();

                cargarUsuariosDisponibles(perfil_id);
                
                if (tabla_usuario) {
                    tabla_usuario.ajax.reload();
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

function eliminarUsuario(usuario_perfil_id){
    Swal.fire({
        text: `¿Eliminar la asignación de este usuario al perfil?`,
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
                        let perfil_id = $('#perfil_id').val();
                        // Inicializar Select2 con usuarios disponibles
                        cargarUsuariosDisponibles(perfil_id);

                        tabla.ajax.reload();

                        getMessage("success", response.message || "Error desconocido");
                        
                        // Recargar tabla de perfiles si existe
                        if (typeof tabla_usuario !== 'undefined') {
                            tabla_usuario.ajax.reload();
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

function modalNuevo() {
    $('#form-perfil')[0].reset();
    $('#perfil_id').val('');
    $('#modalPerfilLabel').text('Nuevo Perfil');
    cargarModulosPermisos();
    $('#modal_perfil').modal('show');
}

function editarPerfil(perfil_id) {

    $('#perfil_id').val(perfil_id);
    $('#modalPerfilLabel').text('Editar Perfil');
    $('#modal_perfil').modal('show');
    
    if (!perfil_id) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'ID de perfil no válido'
        });
        return;
    }  
    
    // Cargar datos del perfil
    $.ajax({
        url: '../../controller/perfil.php?op=obtener_perfil',
        type: 'POST',
        dataType: 'json',
        data: {
            perfil_id: perfil_id
        },
        success: function(response) {
            if (response.success) {
                const perfil = response.data.perfil;

                $('#nombre_perfil').val(perfil.nombre);
                $('#estado_perfil').prop('checked', perfil.estado == 1);
                
                // Cargar módulos con permisos
                cargarModulosPermisos(perfil.id);

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al cargar los datos del perfil'
            });
        }
    });
}

function cargarModulosPermisos(perfil_id = null) {
    $.ajax({
        url: '../../controller/modulo.php?op=listar_modulos_permisos',
        type: 'POST',
        dataType: 'json',
        data: {
            perfil_id: perfil_id
        },
        success: function(data) {
            let html = '';
            
            if (data.length > 0) {
                data.forEach(function(modulo) {
                    const esDashboard = modulo.id == 1;
                    const esReportes = modulo.id == 9;
                    const esSoloLectura = esDashboard || esReportes;
                    
                    // Configuración del checkbox "Ver"
                    let checkboxVerConfig = '';
                    if (esDashboard) {
                        // Dashboard: siempre checked y disabled
                        checkboxVerConfig = 'checked disabled';
                    } else if (esReportes) {
                        // Reportes: habilitado pero no forzado
                        checkboxVerConfig = modulo.puede_ver ? 'checked' : '';
                    } else {
                        // Otros módulos: normal
                        checkboxVerConfig = modulo.puede_ver ? 'checked' : '';
                    }
                    
                    html += `
                        <tr data-modulo-id="${modulo.id}" data-solo-lectura="${esSoloLectura}">
                            <td>
                                <strong>${modulo.nombre}</strong>
                                <input type="hidden" name="modulo_id[]" value="${modulo.id}">
                                ${esDashboard ? '<small class="text-muted d-block">Solo lectura</small>' : ''}
                                ${esReportes ? '<small class="text-muted d-block">Solo lectura</small>' : ''}
                            </td>
                            <td>
                                <div class="form-check mb-2">
                                    <input class="form-check-input input-primary checkbox-ver" 
                                           type="checkbox" 
                                           id="ver_${modulo.id}"
                                           name="permisos[${modulo.id}][ver]"
                                           value="1"
                                           ${checkboxVerConfig}>
                                </div>
                            </td>
                            <td>
                                <div class="form-check mb-2">
                                    <input class="form-check-input input-primary checkbox-crear" 
                                           type="checkbox" 
                                           id="crear_${modulo.id}"
                                           name="permisos[${modulo.id}][crear]"
                                           value="1"
                                           ${modulo.puede_crear ? 'checked' : ''}
                                           ${esSoloLectura ? 'disabled' : ''}
                                           data-modulo-id="${modulo.id}">
                                </div>
                            </td>
                            <td>
                                <div class="form-check mb-2">
                                    <input class="form-check-input input-primary checkbox-editar" 
                                           type="checkbox" 
                                           id="editar_${modulo.id}"
                                           name="permisos[${modulo.id}][editar]"
                                           value="1"
                                           ${modulo.puede_editar ? 'checked' : ''}
                                           ${esSoloLectura ? 'disabled' : ''}
                                           data-modulo-id="${modulo.id}">
                                </div>
                            </td>
                            <td>
                                <div class="form-check mb-2">
                                    <input class="form-check-input input-primary checkbox-eliminar" 
                                           type="checkbox" 
                                           id="eliminar_${modulo.id}"
                                           name="permisos[${modulo.id}][eliminar]"
                                           value="1"
                                           ${modulo.puede_eliminar ? 'checked' : ''}
                                           ${esSoloLectura ? 'disabled' : ''}
                                           data-modulo-id="${modulo.id}">
                                </div>
                            </td>
                        </tr>
                    `;
                });
            } else {
                html = `
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle fa-2x mb-2"></i><br>
                            No hay módulos registrados
                        </td>
                    </tr>
                `;
            }
            
            $('#tabla-permisos tbody').html(html);
            
            // Configurar eventos después de cargar el HTML
            configurarEventosPermisos();
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar módulos:', error);
            $('#tabla-permisos tbody').html(`
                <tr>
                    <td colspan="5" class="text-center text-danger py-4">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i><br>
                        Error al cargar los módulos
                    </td>
                </tr>
            `);
        }
    });
}

function configurarEventosPermisos() {
    // Evento para checkboxes de crear, editar y eliminar
    $(document).off('change', '.checkbox-crear, .checkbox-editar, .checkbox-eliminar').on('change', '.checkbox-crear, .checkbox-editar, .checkbox-eliminar', function() {
        const moduloId = $(this).data('modulo-id');
        const fila = $(this).closest('tr');
        const esDashboard = fila.data('es-dashboard');
        
        // No aplicar lógica si es dashboard
        if (esDashboard) {
            return;
        }
        
        // Si se marca cualquier permiso de acción, marcar automáticamente "ver"
        if ($(this).is(':checked')) {
            const checkboxVer = $(`#ver_${moduloId}`);
            checkboxVer.prop('checked', true);
            
            // Agregar efecto visual
            checkboxVer.parent().addClass('highlight-auto-check');
            setTimeout(() => {
                checkboxVer.parent().removeClass('highlight-auto-check');
            }, 1000);
        }
        
        // Verificar si se debe desmarcar "ver" cuando se desmarcan todos los otros permisos
        verificarDesmarcarVer(moduloId);
    });
    
    // Evento para checkbox de ver
    $(document).off('change', '.checkbox-ver').on('change', '.checkbox-ver', function() {
        const moduloId = $(this).attr('id').replace('ver_', '');
        const fila = $(this).closest('tr');
        const esDashboard = fila.data('es-dashboard');
        
        // No permitir desmarcar "ver" en dashboard
        if (esDashboard && !$(this).is(':checked')) {
            $(this).prop('checked', true);
            getMessage("warning", "El permiso de ver en Dashboard no se puede desactivar")
            return;
        }
        
        // Si se desmarca "ver", desmarcar todos los demás permisos
        if (!$(this).is(':checked')) {
            $(`#crear_${moduloId}, #editar_${moduloId}, #eliminar_${moduloId}`).prop('checked', false);
        }
    });
}

// Verificar si se debe desmarcar "ver" cuando no hay otros permisos activos
function verificarDesmarcarVer(moduloId) {
    const crear = $(`#crear_${moduloId}`).is(':checked');
    const editar = $(`#editar_${moduloId}`).is(':checked');
    const eliminar = $(`#eliminar_${moduloId}`).is(':checked');
    
    // Si ningún permiso de acción está marcado, se puede desmarcar "ver"
    // (excepto en dashboard que siempre debe estar marcado)
    const fila = $(`#ver_${moduloId}`).closest('tr');
    const esDashboard = fila.data('es-dashboard');
    
    if (!crear && !editar && !eliminar && !esDashboard) {
        // Opcionalmente desmarcar "ver" automáticamente
        // $(`#ver_${moduloId}`).prop('checked', false);
    }
}

function guardarPerfil() {
    const formData = new FormData($('#form-perfil')[0]);

    const campos = [
      "#nombre_perfil"
    ];

    for (let i = 0; i < campos.length; i++) {
        if ($(campos[i]).val().trim() === "") {
            getMessage("warning", "Complete todos los datos")

            $(campos[i]).focus();
            return false;
        }
    }
    
    $.ajax({
        url: '../../controller/perfil.php?op=guardar_perfil',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {

            $('#modal_perfil').modal('hide');

            if (response.success) {
                getMessage("success", response.message || "Error desconocido");
            } else {
                getMessage("error", response.message || "Error desconocido");
            }

            // Recargar tabla de perfiles si existe
            if (typeof tabla !== 'undefined') {
                tabla.ajax.reload();
            }
        },
        error: function(xhr, status, error) {
            getMessage("error", "Error al guardar el perfil");
        }
    });
}

// Función mejorada para seleccionar/deseleccionar todos los permisos de un tipo
function toggleTodosPermisos(tipo) {
    const checkboxes = $(`input[name*="[${tipo}]"]:not(:disabled)`);
    const todosMarcados = checkboxes.length === checkboxes.filter(':checked').length;
    
    checkboxes.each(function() {
        const moduloId = $(this).data('modulo-id') || $(this).attr('id').split('_')[1];
        const fila = $(this).closest('tr');
        const esDashboard = fila.data('es-dashboard');
        
        // Lógica especial para dashboard
        if (esDashboard) {
            if (tipo === 'ver') {
                $(this).prop('checked', true); // Dashboard siempre con ver marcado
            }
            return; // Salir para dashboard
        }
        
        // Para otros módulos
        $(this).prop('checked', !todosMarcados);
        
        // Si se está marcando crear, editar o eliminar, marcar también ver
        if (!todosMarcados && (tipo === 'crear' || tipo === 'editar' || tipo === 'eliminar')) {
            $(`#ver_${moduloId}`).prop('checked', true);
        }
        
        // Si se está desmarcando ver, desmarcar todo
        if (todosMarcados && tipo === 'ver') {
            $(`#crear_${moduloId}, #editar_${moduloId}, #eliminar_${moduloId}`).prop('checked', false);
        }
    });
}

// Función para eliminar perfil
function eliminarPerfil(perfil_id) {
    Swal.fire({
        text: `¿Deseas eliminar el perfil?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../../controller/perfil.php?op=eliminar_perfil',
                type: 'POST',
                dataType: 'json',
                data: {
                    perfil_id: perfil_id
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
                    getMessage("error", 'Error al eliminar el perfil');
                }
            });
        }
    });
}

// Función para validar formulario
function validarFormulario() {
    let esValido = true;
    let mensajes = [];
    
    // Validar nombre del perfil
    const nombrePerfil = $('#nombre_perfil').val().trim();
    if (!nombrePerfil) {
        mensajes.push('El nombre del perfil es requerido');
        esValido = false;
    } else if (nombrePerfil.length < 3) {
        mensajes.push('El nombre del perfil debe tener al menos 3 caracteres');
        esValido = false;
    } else if (nombrePerfil.length > 50) {
        mensajes.push('El nombre del perfil no puede exceder 50 caracteres');
        esValido = false;
    }
    
    // Validar que al menos un permiso esté seleccionado
    const permisosSeleccionados = $('input[name*="permisos"]:checked').length;
    if (permisosSeleccionados === 0) {
        mensajes.push('Debe seleccionar al menos un permiso');
        esValido = false;
    }
    
    if (!esValido) {
        Swal.fire({
            icon: 'warning',
            title: 'Formulario incompleto',
            html: mensajes.join('<br>')
        });
    }
    
    return esValido;
}

// Event listeners para el modal
$(document).ready(function() {
    
    // Validación en tiempo real del nombre del perfil
    $('#nombre_perfil').on('input', function() {
        const valor = $(this).val().trim();
        const grupo = $(this).parent();
        
        // Remover clases de validación previas
        grupo.removeClass('has-success has-error');
        $('.feedback-message').remove();
        
        if (valor.length > 0) {
            if (valor.length < 3) {
                grupo.addClass('has-error');
                grupo.append('<div class="feedback-message text-danger small">Mínimo 3 caracteres</div>');
            } else if (valor.length > 50) {
                grupo.addClass('has-error');
                grupo.append('<div class="feedback-message text-danger small">Máximo 50 caracteres</div>');
            }
        }
    });
    
    // Contador de permisos seleccionados
    $(document).on('change', 'input[name*="permisos"]', function() {
        const totalPermisos = $('input[name*="permisos"]').length;
        const permisosSeleccionados = $('input[name*="permisos"]:checked').length;
        
        // Actualizar indicador visual si existe
        $('#permisos-contador').text(`${permisosSeleccionados}/${totalPermisos} permisos seleccionados`);
        
        // Cambiar color del botón según selección
        const botonGuardar = $('#btnGuardarPerfil');
        if (permisosSeleccionados === 0) {
            botonGuardar.removeClass('btn-success').addClass('btn-secondary');
        } else {
            botonGuardar.removeClass('btn-secondary').addClass('btn-success');
        }
    });
    
    // Limpiar formulario al cerrar modal
    $('#modalPerfil').on('hidden.bs.modal', function() {
        $('#form-perfil')[0].reset();
        $('#perfil_id').val('');
        $('.has-success, .has-error').removeClass('has-success has-error');
        $('.feedback-message').remove();
    });
    
});

function botonDeshabilitado(pAccion) {
    getMessage("warning", `¡Perfil predefinido! no se puede ${pAccion}`);
}