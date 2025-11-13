let ticket_id;
let selectedFiles = [];
let selectedFilesModal = []; 
let equipo_id;
let tipo_equipo;
let estado;

$(document).ready(function () {

    $('#btnNuevo').on('click', function() {
        $('#commentForm').addClass('active');
        $(this).hide();
    });
    
    // Cancelar
    $('#btnCancelar').on('click', function() {
        resetForm();
    });
    
    // Click en área de carga (formulario)
    $('#fileUploadArea').on('click', function() {
        $('#fileInput').click();
    });
    
    // Selección de archivos (formulario)
    $('#fileInput').on('change', function(e) {
        handleFiles(e.target.files);
    });
    
    // Drag and drop (formulario)
    $('#fileUploadArea').on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });

    $('#fileUploadArea').on('dragleave', function() {
        $(this).removeClass('dragover');
    });
    
    $('#fileUploadArea').on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        handleFiles(e.originalEvent.dataTransfer.files);
    });

    // Submit formulario
    $('#formComentario').on('submit', function(e) {
        e.preventDefault();
        const texto = $('#comentarioTexto').val();
        enviarComentarioAlServidor(texto, selectedFiles);
    });

    // ==================== MODAL ====================
    
    // Click en área de carga (modal)
    $('#fileUploadAreaModal').on('click', function() {
        $('#fileInputModal').click();
    });
    
    // Cerrar modal
    $('#closeModal, #imageModal').on('click', function(e) {
        if (e.target === this) {
            $('#imageModal').removeClass('active');
        }
    });

    // Cuando se seleccionan archivos (modal)
    $('#fileInputModal').on('change', function(e) {
        handleFilesModal(e.target.files);
    });

    // Drag and drop (modal)
    $('#fileUploadAreaModal').on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });
    
    $('#fileUploadAreaModal').on('dragleave', function() {
        $(this).removeClass('dragover');
    });

    $('#fileUploadAreaModal').on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        handleFilesModal(e.originalEvent.dataTransfer.files);
    });

    // Cerrar modal
    $('#closeModal, #imageModal').on('click', function(e) {
        if (e.target === this) {
            $('#imageModal').removeClass('active');
        }
    });

    $('#liTicket').addClass('active');
    
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
    
    ticket_id = $.urlParam('v');

    $(document).on('permisosUsuarioCargados', function() {
        aplicarPermisosUI('Tickets');

        // Cargar información del ticket
        obtenerInfoTicket(ticket_id);
        cargarComentarios(ticket_id);
    });

});

function obtenerInfoTicket(ticket_id) {
    $.post("../../controller/ticket.php?op=obtener", { id: ticket_id }, function (data) {
        data = JSON.parse(data);

        $('#lblLiTicket').html(data.numero_ticket);
        $('#lblNumTicket').html(data.numero_ticket);
        $('#lblInicio').html(formatearFecha(data.fecha_incidencia));
        $('#lblContrato').html(data.numero_contrato);
        $('#lblEquipo').html(data.numero_serie || '-');
        $('#lblCliente').html(data.nombre_cliente);
        $('#lblTecnico').html(data.nombre_tecnico || 'SIN ASIGNAR');
        $('#lblCierre').html(formatearFecha(data.fecha_atencion) || '-');

        estado = data.estado;
        
        // Estilo del estado
        const estadoClass = {
            'pendiente': 'bg-light-secondary',
            'en_proceso': 'bg-light-warning',
            'resuelto': 'bg-light-success',
            'cancelado': 'bg-light-danger'
        };
        
        $('#lblEstado').removeClass('bg-light-warning bg-light-success bg-light-info bg-light-danger bg-light-secondary');
        $('#lblEstado').addClass(estadoClass[estado] || 'bg-light-secondary');
        $('#lblEstado').html(estado.toUpperCase().replace("_", " "));

        // Descripción del problema
        if (data.descripcion_problema && data.descripcion_problema.trim() !== '') {
            $('#lblDescripcion').html('<p class="mb-0">' + data.descripcion_problema.replace(/\n/g, '<br>') + '</p>');
        } else {
            $('#lblDescripcion').html('<p class="mb-0 text-muted fst-italic">Sin descripción registrada</p>');
        }

        equipo_id = data.equipo_id;

        if (equipo_id) {
            tipo_equipo = data.tipo_equipo;
        }

        // Validar estado y mostrar/ocultar botones
        validarAccionesSegunEstado(estado);
    });
}

function cargarComentarios(ticket_id) {
    $.ajax({
        url: '../../controller/ticket.php?op=listar_comentarios',
        type: 'POST',
        data: { ticket_id: ticket_id },
        dataType: 'json',
        success: function(comentarios) {
            $('#comentariosContainer').empty();
            
            if (comentarios.length === 0) {
                $('#comentariosContainer').html(`
                    <div class="card-body text-center py-5">
                        <i class="bi bi-chat-left-text text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">No hay comentarios</h5>
                        <p class="text-muted">Sé el primero en agregar un comentario a este ticket</p>
                    </div>
                `);
                return;
            }
            
            comentarios.forEach(function(comentario) {
                mostrarComentario(comentario, estado);
            });
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar comentarios:', error);
            $('#comentariosContainer').html(`
                <div class="card-body text-center py-5">
                    <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-danger">Error al cargar comentarios</h5>
                    <p class="text-muted">Por favor, intenta recargar la página</p>
                </div>
            `);
        }
    });
}

function mostrarComentario(comentario, estadoTicket) {
    const fecha = new Date(comentario.created_at);
    const fechaFormateada = formatearFechaComentario(fecha);
    
    const tipoBadgeClass = {
        'nota': 'bg-light-warning',
        'accion': 'bg-light-info',
        'sistema': 'bg-light-secondary'
    };
    const badgeClass = tipoBadgeClass[comentario.tipo] || 'bg-light-secondary';
    
    // Construir HTML de archivos adjuntos
    let archivosHTML = '';
    if (comentario.fotos && comentario.fotos.length > 0) {
        archivosHTML = '<div class="comment-files mt-2">';
        
        comentario.fotos.forEach(function(archivo) {
            const tipoArchivo = archivo.tipo_archivo || 'image/jpeg';
            
            if (tipoArchivo.startsWith('image/')) {
                // Mostrar imagen
                archivosHTML += `
                    <div class="comment-file-item image-item" onclick="openModal('../../${archivo.ruta_archivo}')">
                        <img src="../../${archivo.ruta_archivo}" alt="${archivo.nombre_archivo}">
                    </div>
                `;
            } else if (tipoArchivo === 'application/pdf') {
                // Mostrar PDF
                archivosHTML += `
                    <div class="comment-file-item pdf-item" onclick="window.open('../../${archivo.ruta_archivo}', '_blank')">
                        <i class="bi bi-file-pdf-fill text-danger"></i>
                        <span class="file-name">${archivo.nombre_archivo}</span>
                    </div>
                `;
            } else if (tipoArchivo.startsWith('video/')) {
                // Mostrar video
                archivosHTML += `
                    <div class="comment-file-item video-item">
                        <video controls>
                            <source src="../../${archivo.ruta_archivo}" type="${tipoArchivo}">
                            Tu navegador no soporta la reproducción de videos.
                        </video>
                        <span class="file-name">${archivo.nombre_archivo}</span>
                    </div>
                `;
            }
        });
        
        archivosHTML += '</div>';
    }

    const puedeEliminar = tienePermiso('Tickets', 'eliminar');
    
    // Mostrar botón de eliminar solo si puede eliminar Y el ticket no está finalizado/cancelado
    let botonEliminar = '';
    if (comentario.puede_eliminar && estadoTicket !== 'cancelado' && estadoTicket !== 'resuelto' && puedeEliminar) {
        botonEliminar = `
            <button type="button" 
                    class="btn btn-sm btn-outline-danger btn-eliminar-com" 
                    onclick="eliminarComentario(${comentario.id})"
                    data-bs-toggle="tooltip" 
                    title="Eliminar comentario">
                <i class="fas fa-trash-alt"></i>
            </button>
        `;
    }
    
    const comentarioHTML = `
        <div class="border-bottom card-body" data-comentario-id="${comentario.id}">
            <div class="row">
                <div class="col-sm-auto mb-3 mb-sm-0">
                    <div class="d-sm-inline-block d-flex align-items-center">
                        <img class="wid-60 img-radius mb-2" 
                             src="../../assets/images/user/${comentario.foto_perfil}" 
                             alt="Avatar de ${comentario.nombres}">
                    </div>
                </div>
                <div class="col">
                    <div class="row">
                        <div class="col">
                            <h5 class="d-inline-block mb-0">${comentario.nombres} ${comentario.apellidos}</h5>
                            <span class="badge ${badgeClass} ms-2">${comentario.tipo.toUpperCase()}</span>
                            <p class="text-muted small mb-2">${fechaFormateada}</p>
                        </div>
                        <div class="col-auto">
                            ${botonEliminar}
                        </div>
                    </div>
                    <div class="comment-body">
                        <p class="mb-2">${comentario.comentario.replace(/\n/g, '<br>')}</p>
                        ${archivosHTML}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#comentariosContainer').append(comentarioHTML);
}

// Función para eliminar comentario
function eliminarComentario(comentario_id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás revertir esta acción",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../../controller/ticket.php?op=eliminar_comentario',
                type: 'POST',
                data: { 
                    comentario_id: comentario_id,
                    ticket_id: ticket_id 
                },
                dataType: 'json',
                success: function(response) {
                    if (response && response.success == 1) {
                        // Eliminar el comentario del DOM con animación
                        $(`[data-comentario-id="${comentario_id}"]`).fadeOut(300, function() {
                            $(this).remove();
                            
                            // Si no quedan comentarios, mostrar mensaje
                            if ($('#comentariosContainer').children().length === 0) {
                                $('#comentariosContainer').html(`
                                    <div class="card-body text-center py-5">
                                        <i class="bi bi-chat-left-text text-muted" style="font-size: 3rem;"></i>
                                        <h5 class="mt-3 text-muted">No hay comentarios</h5>
                                        <p class="text-muted">Sé el primero en agregar un comentario a este ticket</p>
                                    </div>
                                `);
                            }
                        });
                        
                        getMessage("success", response.message || "Comentario eliminado correctamente");
                    } else {
                        getMessage("error", response.message || "Error al eliminar el comentario");
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                    getMessage("error", "Error al procesar la solicitud");
                }
            });
        }
    });
}

function formatearFechaComentario(fecha) {
    const ahora = new Date();
    const diff = ahora - fecha;
    const minutos = Math.floor(diff / 60000);
    const horas = Math.floor(diff / 3600000);
    const dias = Math.floor(diff / 86400000);
    
    if (minutos < 1) return 'Justo ahora';
    if (minutos < 60) return `Hace ${minutos} minuto${minutos > 1 ? 's' : ''}`;
    if (horas < 24) return `Hace ${horas} hora${horas > 1 ? 's' : ''}`;
    if (dias < 7) return `Hace ${dias} día${dias > 1 ? 's' : ''}`;
    
    const opciones = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return fecha.toLocaleDateString('es-ES', opciones);
}

function validarAccionesSegunEstado(estado) {
    const $btnEditar = $('#btnEditarTicket');
    const $btnEnProceso = $('#btnEnProcesoTicket');
    const $btnCancelar = $('#btnCancelarTicket');
    const $btnFinalizar = $('#btnFinalizarTicket');
    const $btnNuevo = $('#btnNuevo');
    const $dropdownAccion = $('#dropdownAccion');
    
    // Ocultar todos los botones por defecto
    $dropdownAccion.hide();
    $btnEditar.hide();
    $btnEnProceso.hide();
    $btnCancelar.hide();
    $btnFinalizar.hide();
    $btnNuevo.attr('style', 'display: none !important'); // Con !important
    
    // Ocultar dropdown completo si está cancelado o resuelto
    if (estado === 'cancelado' || estado === 'resuelto') {
        $dropdownAccion.hide();
        // Forzar ocultamiento con !important
        $btnNuevo.attr('style', 'display: none !important');
        return;
    }

    // Verificar permisos del usuario
    const puedeCrear = tienePermiso('Tickets', 'crear');
    const puedeEditar = tienePermiso('Tickets', 'editar');

    // Mostrar dropdown solo si hay acciones disponibles
    let hayAccionesDisponibles = false;
    
    // Mostrar botones según el estado
    if (estado === 'en_proceso') {
        
        if (puedeCrear) {
            $btnNuevo.removeAttr('style').show();
        }

        if (puedeEditar) {
            $btnCancelar.show();
            $btnFinalizar.show();
            hayAccionesDisponibles = true;
        }

    } else if (estado === 'pendiente') {
        if (puedeEditar) {
            $btnEnProceso.show();
            $btnCancelar.show();
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

function cancelarTicket() {
    $('#fo_ticket_id').val(ticket_id);
    $('#accion_tipo').val('cancelar');
    
    // Limpiar campos
    $('#observaciones').val('');
    $('#contador_bn').val('');
    $('#contador_color').val('');
    
    // Ocultar card de contadores
    $('#card-contadores').hide();
    
    // Cambiar título y texto del modal
    $('#modalTicketLabel').text('Cancelar ticket');
    
    // Mostrar modal
    $('#modal_observacion').modal('show');
}

function finalizarTicket() {
    $('#fo_ticket_id').val(ticket_id);
    $('#accion_tipo').val('finalizar');
    
    // Limpiar campos
    $('#observaciones').val('');
    $('#contador_bn').val('');
    $('#contador_color').val('');
    
    // Mostrar card de contadores
    $('#card-contadores').show();
    
    // Cambiar título y texto del modal
    $('#modalTicketLabel').text('Finalizar ticket');

    validarCamposColor(tipo_equipo)
    
    // Mostrar modal
    $('#modal_observacion').modal('show');
}

function validarCamposColor(tipo_equipo) {
    let $labelFinal = $("label[for='contador_color']");
    let $inputFinal = $("#contador_color");
    
    if (tipo_equipo === 'color') {

        if ($labelFinal.find('.text-danger').length === 0) {
            $labelFinal.append(' <span class="text-danger">*</span>');
        }
        
        $inputFinal.prop('disabled', false).removeClass('bg-light');
        
    } else if (tipo_equipo === 'bn') {
        
        $labelFinal.find('.text-danger').remove();
        $inputFinal.prop('disabled', true).addClass('bg-light').val('');
        
    }
}

function guardarAccion() {
    let accionTipo = $('#accion_tipo').val();
    
    if (accionTipo === 'cancelar') {
        guardarCancelar();
    } else if (accionTipo === 'finalizar') {
        guardarFinalizar();
    }
}

function guardarCancelar() {
    let observaciones = $("#observaciones").val().trim();
    let ticketId = $('#fo_ticket_id').val();

    // Validación
    if (!observaciones) {
        getMessage("warning", "El campo observaciones es requerido");
        $("#observaciones").focus();
        return false;
    }

    if (!ticketId) {
        getMessage("error", "No se encontró el ID del ticket");
        return false;
    }

    Swal.fire({
        text: '¿Deseas cancelar el ticket?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            // Crear FormData
            let formData = new FormData();
            formData.append('fo_ticket_id', ticketId);
            formData.append('observaciones', observaciones);
            formData.append('accion', 'cancelar');
            
            // Agregar imágenes si existen
            if (selectedFilesModal && selectedFilesModal.length > 0) {
                selectedFilesModal.forEach(function(file) {
                    formData.append('imagenes[]', file);
                });
                
            }
            

            $.ajax({
                url: '../../controller/ticket.php?op=gestionar_estado_ticket',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response && response.success == 1) {
                        // Cerrar modal inmediatamente
                        $('#modal_observacion').modal('hide');
                        
                        // Mostrar mensaje de éxito
                        getMessage("success", response.message);
                        
                        // Limpiar el modal
                        cerrarModal();
                        
                        // Recargar información inmediatamente sin timeout
                        obtenerInfoTicket(ticket_id);
                        cargarComentarios(ticket_id);
                        resetForm();
                        
                    } else {
                        getMessage("error", response.message || 'Error desconocido');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                    getMessage("error", "Error al procesar la solicitud");
                }
            });
        }
    });
}

function guardarFinalizar() {
    let observaciones = $("#observaciones").val().trim();
    let ticketId = $('#fo_ticket_id').val();
    let contadorBN = $("#contador_bn").val().trim();
    let contadorColor = $("#contador_color").val().trim();

    if (!observaciones) {
        getMessage("warning", "El campo observaciones es requerido");
        $("#observaciones").focus();
        return false;
    }

    if (!contadorBN) {
        getMessage("warning", "El contador final BN es requerido");
        $("#contador_bn").focus();
        return false;
    }

    if (!contadorColor && tipo_equipo == 'color') {
        getMessage("warning", "El contador final Color es requerido");
        $("#contador_color").focus();
        return false;
    }

    if (!ticketId) {
        getMessage("error", "No se encontró el ID del ticket");
        return false;
    }

    Swal.fire({
        text: '¿Deseas finalizar el ticket?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, finalizar',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            // Crear FormData
            let formData = new FormData();
            formData.append('fo_ticket_id', ticketId);
            formData.append('observaciones', observaciones);
            formData.append('accion', 'finalizar');
            formData.append('contador_bn', contadorBN);
            
            // Solo enviar contador_color si tiene valor
            if (contadorColor) {
                formData.append('contador_color', contadorColor);
            }
            
            // Agregar imágenes si existen
            if (selectedFilesModal && selectedFilesModal.length > 0) {
                selectedFilesModal.forEach(function(file) {
                    formData.append('imagenes[]', file);
                });
            }

            $.ajax({
                url: '../../controller/ticket.php?op=gestionar_estado_ticket',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response && response.success == 1) {
                        // Cerrar modal inmediatamente
                        $('#modal_observacion').modal('hide');
                        
                        // Mostrar mensaje de éxito
                        getMessage("success", response.message);
                        
                        // Limpiar el modal
                        cerrarModal();
                        
                        // Recargar información inmediatamente sin timeout
                        obtenerInfoTicket(ticket_id);
                        cargarComentarios(ticket_id);
                        resetForm();
                        
                    } else {
                        getMessage("error", response.message || 'Error desconocido');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                    getMessage("error", "Error al procesar la solicitud");
                }
            });
        }
    });
}

function cerrarModal() {
    // Limpiar campos
    $('#observaciones').val('');
    $('#contador_bn').val('');
    $('#contador_color').val('');
    $('#accion_tipo').val('');
    
    // Limpiar archivos
    selectedFilesModal = [];
    $('#previewContainerModal').empty();
    
    // NO llamar a .modal('hide') aquí porque ya se llamó antes
    // Solo limpiar el backdrop si quedó
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    $('body').css('padding-right', '');
}

function handleFiles(files) {
    $.each(files, function(i, file) {
        // Validar tipo (imágenes, PDFs y videos)
        const tiposPermitidos = [
            'image/jpeg',
            'image/jpg', 
            'image/png',
            'image/gif',
            'image/webp',
            'application/pdf',
            'video/mp4',
            'video/webm',
            'video/ogg',
            'video/quicktime' // MOV
        ];
        
        const esImagenVideoPdf = tiposPermitidos.includes(file.type) || 
                                file.type.startsWith('image/') || 
                                file.type.startsWith('video/');
        
        if (!esImagenVideoPdf) {
            getMessage('warning', 'Solo se permiten imágenes, PDFs y videos');
            return;
        }
        
        // Validar tamaño según tipo
        let tamajoMaximo = 5 * 1024 * 1024; // 5MB por defecto
        
        if (file.type.startsWith('video/')) {
            tamajoMaximo = 50 * 1024 * 1024; // 50MB para videos
        } else if (file.type === 'application/pdf') {
            tamajoMaximo = 10 * 1024 * 1024; // 10MB para PDFs
        }
        
        if (file.size > tamajoMaximo) {
            const tamañoMB = Math.round(tamajoMaximo / (1024 * 1024));
            getMessage('warning', `El archivo debe ser menor a ${tamañoMB}MB`);
            return;
        }
        
        // Agregar archivo
        selectedFiles.push(file);
        createPreview(file, selectedFiles.length - 1);
    });
}

function handleFilesModal(files) {
    $.each(files, function(i, file) {
        // Validar tipo
        const tiposPermitidos = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp',
            'application/pdf',
            'video/mp4',
            'video/webm',
            'video/ogg',
            'video/quicktime'
        ];
        
        const esValido = tiposPermitidos.includes(file.type) || 
                        file.type.startsWith('image/') || 
                        file.type.startsWith('video/');
        
        if (!esValido) {
            getMessage('warning', 'Solo se permiten imágenes, PDFs y videos');
            return;
        }
        
        // Validar tamaño según tipo
        let tamajoMaximo = 5 * 1024 * 1024;
        
        if (file.type.startsWith('video/')) {
            tamajoMaximo = 50 * 1024 * 1024;
        } else if (file.type === 'application/pdf') {
            tamajoMaximo = 10 * 1024 * 1024;
        }
        
        if (file.size > tamajoMaximo) {
            const tamañoMB = Math.round(tamajoMaximo / (1024 * 1024));
            getMessage('warning', `El archivo debe ser menor a ${tamañoMB}MB`);
            return;
        }
        
        selectedFilesModal.push(file);
        createPreviewModal(file, selectedFilesModal.length - 1);
    });
}

// Crear vista previa
function createPreview(file, index) {
    const reader = new FileReader();
    
    reader.onload = function(e) {
        let previewContent = '';
        
        // Determinar el tipo de archivo y crear preview apropiado
        if (file.type.startsWith('image/')) {
            // Preview de imagen
            previewContent = `
                <img src="${e.target.result}" alt="Preview">
                <button type="button" class="remove-btn" onclick="removeFile(${index})">
                    <i class="fas fa-trash-alt"></i>
                </button>
            `;
        } else if (file.type === 'application/pdf') {
            // Preview de PDF
            previewContent = `
                <div class="file-preview-pdf">
                    <i class="bi bi-file-pdf-fill text-danger" style="font-size: 40px;"></i>
                    <span class="file-name">${file.name}</span>
                    <span class="file-size">${formatFileSize(file.size)}</span>
                </div>
                <button type="button" class="remove-btn" onclick="removeFile(${index})">
                    <i class="fas fa-trash-alt"></i>
                </button>
            `;
        } else if (file.type.startsWith('video/')) {
            // Preview de video
            previewContent = `
                <video class="video-preview" controls>
                    <source src="${e.target.result}" type="${file.type}">
                    Tu navegador no soporta la reproducción de videos.
                </video>
                <div class="file-overlay">
                    <span class="file-name">${file.name}</span>
                    <span class="file-size">${formatFileSize(file.size)}</span>
                </div>
                <button type="button" class="remove-btn" onclick="removeFile(${index})">
                    <i class="fas fa-trash-alt"></i>
                </button>
            `;
        }
        
        const preview = $('<div>', {
            class: 'preview-item',
            'data-index': index,
            'data-type': file.type,
            html: previewContent
        });
        
        $('#previewContainer').append(preview);
    };
    
    reader.onerror = function(error) {
        console.error('Error al leer archivo:', error);
        getMessage('error', 'Error al cargar el archivo');
    };
    
    reader.readAsDataURL(file);
}

function createPreviewModal(file, index) {
    const reader = new FileReader();
    
    reader.onload = function(e) {
        let previewContent = '';
        
        if (file.type.startsWith('image/')) {
            previewContent = `
                <img src="${e.target.result}" alt="Preview">
                <button type="button" class="remove-btn-modal" onclick="removeFileModal(${index})">
                    <i class="fas fa-trash-alt"></i>
                </button>
            `;
        } else if (file.type === 'application/pdf') {
            previewContent = `
                <div class="file-preview-pdf">
                    <i class="bi bi-file-pdf-fill text-danger" style="font-size: 40px;"></i>
                    <span class="file-name">${file.name}</span>
                    <span class="file-size">${formatFileSize(file.size)}</span>
                </div>
                <button type="button" class="remove-btn-modal" onclick="removeFileModal(${index})">
                    <i class="fas fa-trash-alt"></i>
                </button>
            `;
        } else if (file.type.startsWith('video/')) {
            previewContent = `
                <video class="video-preview" controls>
                    <source src="${e.target.result}" type="${file.type}">
                </video>
                <div class="file-overlay">
                    <span class="file-name">${file.name}</span>
                    <span class="file-size">${formatFileSize(file.size)}</span>
                </div>
                <button type="button" class="remove-btn-modal" onclick="removeFileModal(${index})">
                    <i class="fas fa-trash-alt"></i>
                </button>
            `;
        }
        
        const preview = $('<div>', {
            class: 'preview-item-modal',
            'data-index': index,
            'data-type': file.type,
            html: previewContent
        });
        
        $('#previewContainerModal').append(preview);
    };
    
    reader.onerror = function(error) {
        console.error('Error al leer archivo:', error);
        getMessage('error', 'Error al cargar el archivo');
    };
    
    reader.readAsDataURL(file);
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// Eliminar archivo
function removeFile(index) {
    selectedFiles.splice(index, 1);
    $('[data-index="' + index + '"]').remove();
    
    // Reindexar
    $('.preview-item').each(function(i) {
        $(this).attr('data-index', i);
        $(this).find('.remove-btn').attr('onclick', 'removeFile(' + i + ')');
    });
}

function removeFileModal(index) {
    // Eliminar del array
    selectedFilesModal.splice(index, 1);
    
    // Eliminar preview
    $(`.preview-item-modal[data-index="${index}"]`).remove();
    
    // Re-indexar los elementos restantes
    $('.preview-item-modal').each(function(newIndex) {
        $(this).attr('data-index', newIndex);
        $(this).find('.remove-btn-modal').attr('onclick', `removeFileModal(${newIndex})`);
    });
}

// Actualizar la función enviarComentarioAlServidor
function enviarComentarioAlServidor(texto, imagenes) {
    const formData = new FormData();
    formData.append('comentario', texto);
    formData.append('ticket_id', ticket_id);
    formData.append('tipo', 'nota');
    
    // Agregar imágenes
    $.each(imagenes, function(i, file) {
        formData.append('imagenes[]', file);
    });
    
    // DEBUG: Ver qué se está enviando
    console.log('Ticket ID:', ticket_id);
    console.log('Texto:', texto);
    console.log('Número de imágenes:', imagenes.length);
    
    $.ajax({
        url: '../../controller/ticket.php?op=agregar_comentario',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            $('#formComentario button[type="submit"]')
                .prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm me-2"></span>Enviando...');
        },
        success: function(response) {
            console.log('Respuesta del servidor:', response);
            
            if (response.success == 1) {
                // Recargar comentarios
                cargarComentarios(ticket_id);
                obtenerInfoTicket(ticket_id);
                resetForm();
                getMessage("success", "Comentario agregado exitosamente");
            } else {
                getMessage("error", response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error completo:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: error
            });
            
            // Mostrar el error real
            let errorMsg = 'Error al agregar comentario';
            if (xhr.responseText) {
                try {
                    let errorData = JSON.parse(xhr.responseText);
                    errorMsg = errorData.message || errorMsg;
                } catch(e) {
                    errorMsg = xhr.responseText.substring(0, 200);
                }
            }
            
            getMessage("error", errorMsg);
        },
        complete: function() {
            $('#formComentario button[type="submit"]')
                .prop('disabled', false)
                .html('<i class="bi bi-send"></i> Publicar comentario');
        }
    });
}

// Abrir modal de imagen
function openModal(src) {
    $('#modalImage').attr('src', src);
    $('#imageModal').addClass('active');
}

// Reset form
function resetForm() {
    $('#commentForm').removeClass('active');
    $('#formComentario')[0].reset();
    selectedFiles = [];
    $('#previewContainer').empty();
    $('#fileInput').val('');
    $('#formComentario button[type="submit"]').prop('disabled', false).html('<i class="bi bi-send"></i> Publicar comentario');
}

function estadoTicket(pStrEstado){
    Swal.fire({
        text: `¿Desea actualizar el estado del ticket?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, actualizar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../../controller/ticket.php?op=estado_ticket',
                type: 'POST',
                dataType: 'json',
                data: {
                    ticket_id : ticket_id,
                    estado       : pStrEstado
                },
                success: function(response) {
                    if (response.success) {
                        getMessage("success", response.message || "Error desconocido");
                        
                        obtenerInfoTicket(ticket_id);
                        cargarComentarios(ticket_id);
                        
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