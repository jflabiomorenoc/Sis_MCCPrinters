$(document).ready(function(){
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
        
        // CONFIGURACIÓN RESPONSIVE MEJORADA
        "responsive": {
            "details": {
                "type": 'column',
                "target": 0
            }
        },
        columnDefs: [
            { width: "10px", targets: 0, className: 'dtr-control', orderable: false, data: null, defaultContent: ''},
            { width: "35%" , targets: 1, className: 'text-left font-weight-bold', responsivePriority: 1},
            { width: "30%" , targets: 2, className: 'text-center', responsivePriority: 3},
            { width: "20%" , targets: 3, className: 'text-center', responsivePriority: 2},
            { width: "15%" , targets: 4, className: 'text-center', responsivePriority: 2}
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
                    html += `
                        <tr>
                            <td>
                                <strong>${modulo.nombre}</strong>
                                <input type="hidden" name="modulo_id[]" value="${modulo.id}">
                            </td>
                            <td>
                                <div class="form-check mb-2">
                                    <input class="form-check-input input-primary" 
                                           type="checkbox" 
                                           id="ver_${modulo.id}"
                                           name="permisos[${modulo.id}][ver]"
                                           value="1"
                                           ${modulo.puede_ver ? 'checked' : ''}>
                                </div>
                            </td>
                            <td>
                                <div class="form-check mb-2">
                                    <input class="form-check-input input-primary" 
                                           type="checkbox" 
                                           id="crear_${modulo.id}"
                                           name="permisos[${modulo.id}][crear]"
                                           value="1"
                                           ${modulo.puede_crear ? 'checked' : ''}>
                                </div>
                            </td>
                            <td>
                                <div class="form-check mb-2">
                                    <input class="form-check-input input-primary" 
                                           type="checkbox" 
                                           id="editar_${modulo.id}"
                                           name="permisos[${modulo.id}][editar]"
                                           value="1"
                                           ${modulo.puede_editar ? 'checked' : ''}>
                                </div>
                            </td>
                            <td>
                                <div class="form-check mb-2">
                                    <input class="form-check-input input-primary" 
                                           type="checkbox" 
                                           id="eliminar_${modulo.id}"
                                           name="permisos[${modulo.id}][eliminar]"
                                           value="1"
                                           ${modulo.puede_eliminar ? 'checked' : ''}>
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
// Función para guardar solo permisos (mantener la función original)
function guardarPerfil() {
    const formData = new FormData($('#form-perfil')[0]);
    
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
        },
        error: function(xhr, status, error) {
            getMessage("error", "Error al guardar el perfil");
        }
    });
}

// Función para seleccionar/deseleccionar todos los permisos de un tipo
function toggleTodosPermisos(tipo) {
    const checkboxes = $(`input[name*="[${tipo}]"]`);
    const todosMarcados = checkboxes.length === checkboxes.filter(':checked').length;
    
    checkboxes.prop('checked', !todosMarcados);
}

// Función para eliminar perfil
function eliminarPerfil(perfil_id, nombre_perfil) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Deseas eliminar el perfil "${nombre_perfil}"? Esta acción no se puede deshacer.`,
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
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        // Recargar tabla de perfiles si existe
                        if (typeof tablaPerfiles !== 'undefined') {
                            tablaPerfiles.ajax.reload();
                        }
                        
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
                        text: 'Error al eliminar el perfil'
                    });
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