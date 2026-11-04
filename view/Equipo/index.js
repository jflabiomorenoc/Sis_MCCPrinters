$(document).ready(function () {

    $(document).on('permisosUsuarioCargados', function() {
        aplicarPermisosUI('Equipos');
    });

    listarEquipo();

    cargarProveedorDisponible()

    // ==================== MODAL DE ARCHIVOS ====================

    // Click en área de carga
    $('#fileUploadAreaArchivo').on('click', function() {
        $('#fileInputArchivo').click();
    });

    // Selección de archivos
    $('#fileInputArchivo').on('change', function(e) {
        handleFilesArchivo(e.target.files);
        $(this).val('');
    });

    // Drag and drop
    $('#fileUploadAreaArchivo').on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });

    $('#fileUploadAreaArchivo').on('dragleave', function() {
        $(this).removeClass('dragover');
    });

    $('#fileUploadAreaArchivo').on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        handleFilesArchivo(e.originalEvent.dataTransfer.files);
    });

    // Limpiar al cerrar el modal (también con Esc o clic fuera)
    $('#modal_archivos').on('hidden.bs.modal', function() {
        limpiarSeleccionArchivos();
    });
});

let selectedFilesArchivo = [];
let tabla_archivos;

// Evento change simplificado
$("#tipo_equipo").on('change', function() {
    actualizarCamposColor($(this).val());
});

$("#categoria_equipo").on('change', function() {
    actualizarCamposCategoria($(this).val());
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
            { width: "14%" , targets: 3, className: 'text-center'},
            { width: "12%" , targets: 4, className: 'text-center'},
            { width: "14%" , targets: 5, className: 'text-left'},
            { width: "8%" , targets: 6, className: 'text-center'},
            { width: "12%" , targets: 7, className: 'text-center'},
            { width: "10%" , targets: 8, className: 'text-center'}
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

// Muestra el tipo de impresión y los contadores solo cuando la categoría es impresora
function actualizarCamposCategoria(categoria) {
    if (categoria === 'impresora') {
        $('#div_tipo_impresion').show();
        $('#card_contadores_impresion').show();
        actualizarCamposColor($('#tipo_equipo').val());
    } else {
        $('#div_tipo_impresion').hide();
        $('#card_contadores_impresion').hide();

        // Limpiar los datos que solo aplican a impresoras
        $('#tipo_equipo').val('');
        $('#contador_inicial_bn, #contador_actual_bn, #contador_inicial_color, #contador_actual_color').val('');
    }
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

    // form.reset() no actualiza el texto mostrado por Select2
    $('#proveedor_id').val('').trigger('change');

    // Resetear campos de impresora y de color al estado inicial
    actualizarCamposCategoria('');
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
        $('#categoria_equipo').val(data.categoria_equipo);
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

        // Actualizar campos según la categoría y el tipo de impresión
        actualizarCamposCategoria(data.categoria_equipo);
    });

    $('#modal_equipo').modal('show');
}

function guardarEquipo() {
    // Capturar el formulario y crear FormData
    var formData = new FormData($("#form-equipo")[0]);
 
    let campos = [
        "#categoria_equipo",
        "#marca",
        "#modelo",
        "#condicion",
        "#proveedor_id",
        "#fecha_compra",
        "#costo_dolares",
        "#costo_soles"
    ];

    // Tipo de impresión y contadores solo aplican a impresoras
    if ($("#categoria_equipo").val() == "impresora") {
        campos.splice(3, 0, "#tipo_equipo");
        campos.push("#contador_inicial_bn");
        campos.push("#contador_actual_bn");

        if ($("#tipo_equipo").val() == "color") {
            campos.push("#contador_inicial_color");
            campos.push("#contador_actual_color");
        }
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
    $('#proveedor_id').val('').trigger('change');

    // Resetear checkbox
    $('#estado_equipo').prop('checked', true);
    
    // Limpiar el usuario_id oculto
    $('#equipor_id').val('');

    // Ocultar los campos que solo aplican a impresoras
    actualizarCamposCategoria('');

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

// ==================== ARCHIVOS DEL EQUIPO ====================

// Abrir el modal con los archivos del equipo
function verArchivos(equipo_id, nombre_equipo) {
    $('#archivo_equipo_id').val(equipo_id);
    $('#modal_archivos_equipo').text('- ' + nombre_equipo);

    // Adjuntar solo con permiso de editar
    $('#card_subir_archivo').toggle(tienePermiso('Equipos', 'editar'));

    limpiarSeleccionArchivos();

    // Ocultar la tabla anterior mientras cargan los archivos de este equipo
    $('#contenedor_tabla_archivos').css('visibility', 'hidden');
    $('#loading_archivos').show();

    $('#modal_archivos').one('shown.bs.modal', function() {
        if (tabla_archivos) {
            recargarArchivos();
        } else {
            listarArchivos();
        }
    });
    $('#modal_archivos').modal('show');
}

// Actualiza solo los datos de la tabla, sin volver a crearla
function recargarArchivos() {
    tabla_archivos.ajax.reload(null, false);
}

function mostrarTablaArchivos() {
    $('#loading_archivos').hide();
    $('#contenedor_tabla_archivos').css('visibility', 'visible');
}

// La tabla se crea una sola vez; el equipo se toma del campo oculto en cada petición
function listarArchivos() {
    tabla_archivos = $('#tabla-archivos').DataTable({
        "processing": false,
        "aServerSide": true,
        "searching": true,
        lengthChange: false,
        "ordering": false,
        "iDisplayLength": 10,
        "autoWidth": false,

        columnDefs: [
            { width: "55%", targets: 0, className: 'text-left' },
            { width: "10%", targets: 1, className: 'text-center' },
            { width: "20%", targets: 2, className: 'text-center' },
            { width: "15%", targets: 3, className: 'text-center' }
        ],

        "ajax": {
            url: '../../controller/equipo.php?op=listar_archivos',
            type: "post",
            data: function(d) {
                d.equipo_id = $('#archivo_equipo_id').val();
            },
            dataType: "json",
            error: function(e) {
                console.log(e.responseText);
                mostrarTablaArchivos();
            }
        },

        "drawCallback": function() {
            mostrarTablaArchivos();
        },

        "language": {
            "search": "Buscar:",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "infoEmpty": "Mostrando 0 a 0 de 0 registros",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "loadingRecords": "Cargando...",
            "zeroRecords": '<div class="text-center py-4"><h5 class="text-muted">No hay datos disponibles</h5></div>',
            "emptyTable": '<div class="text-center py-4"><h5>No hay archivos adjuntos</h5><p class="text-muted">Adjunta el primer archivo de este equipo</p></div>',
            "paginate": {
                "first": '<i class="fas fa-angle-double-left"></i>',
                "last": '<i class="fas fa-angle-double-right"></i>',
                "next": '<i class="fas fa-angle-right"></i>',
                "previous": '<i class="fas fa-angle-left"></i>'
            }
        },

        "dom": '<"row mb-3"<"col-sm-12 col-md-6"><"col-sm-12 col-md-6"<"d-flex justify-content-end"f>>>' +
               '<"row"<"col-sm-12"tr>>' +
               '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
    });
}

// Validar y agregar los archivos seleccionados a la vista previa
function handleFilesArchivo(files) {
    // Extensión permitida => tamaño máximo en MB
    const extensionesPermitidas = {
        jpg: 5, jpeg: 5, png: 5, gif: 5, webp: 5, bmp: 5,
        pdf: 10,
        doc: 20, docx: 20, xls: 20, xlsx: 20, ppt: 20, pptx: 20
    };

    $.each(files, function(i, file) {
        const extension = file.name.split('.').pop().toLowerCase();

        if (!extensionesPermitidas.hasOwnProperty(extension)) {
            getMessage('warning', `Archivo no permitido: ${file.name}`);
            return;
        }

        const tamanoMaximo = extensionesPermitidas[extension];

        if (file.size > tamanoMaximo * 1024 * 1024) {
            getMessage('warning', `"${file.name}" debe ser menor a ${tamanoMaximo}MB`);
            return;
        }

        selectedFilesArchivo.push(file);
        createPreviewArchivo(file, selectedFilesArchivo.length - 1);
    });

    $('#div_subir_archivos').toggle(selectedFilesArchivo.length > 0);
}

function createPreviewArchivo(file, index) {
    const extension = file.name.split('.').pop().toLowerCase();

    // Configuración visual por tipo de documento
    const tipoConfig = {
        pdf:   { icon: 'bi bi-file-pdf-fill',   color: '#dc3545', label: 'PDF' },
        word:  { icon: 'bi bi-file-word-fill',  color: '#2b579a', label: 'Word' },
        excel: { icon: 'bi bi-file-excel-fill', color: '#217346', label: 'Excel' },
        ppt:   { icon: 'bi bi-file-ppt-fill',   color: '#d24726', label: 'PowerPoint' }
    };

    let config = null;
    if (extension === 'pdf')                        config = tipoConfig.pdf;
    else if (['doc', 'docx'].includes(extension))   config = tipoConfig.word;
    else if (['xls', 'xlsx'].includes(extension))   config = tipoConfig.excel;
    else if (['ppt', 'pptx'].includes(extension))   config = tipoConfig.ppt;

    const botonEliminar = `
        <button type="button" class="remove-btn-modal" onclick="removeFileArchivo(${index})">
            <i class="fas fa-trash-alt"></i>
        </button>
    `;

    const agregarPreview = function(contenido) {
        $('#previewContainerArchivo').append($('<div>', {
            class: 'preview-item-modal',
            'data-index': index,
            html: contenido
        }));
    };

    if (config) {
        agregarPreview(`
            <div class="file-preview-doc">
                <i class="${config.icon}" style="font-size: 40px; color: ${config.color};"></i>
                <span style="color:${config.color}; font-weight:600; font-size:11px;">${config.label}</span>
                <span class="file-name">${$('<div>').text(file.name).html()}</span>
                <span class="file-size">${formatFileSize(file.size)}</span>
            </div>
            ${botonEliminar}
        `);
        return;
    }

    // Imágenes
    const reader = new FileReader();

    reader.onload = function(e) {
        agregarPreview(`<img src="${e.target.result}" alt="Vista previa">${botonEliminar}`);
    };

    reader.onerror = function() {
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

function removeFileArchivo(index) {
    selectedFilesArchivo.splice(index, 1);

    $(`#previewContainerArchivo .preview-item-modal[data-index="${index}"]`).remove();

    // Re-indexar los elementos restantes
    $('#previewContainerArchivo .preview-item-modal').each(function(newIndex) {
        $(this).attr('data-index', newIndex);
        $(this).find('.remove-btn-modal').attr('onclick', `removeFileArchivo(${newIndex})`);
    });

    $('#div_subir_archivos').toggle(selectedFilesArchivo.length > 0);
}

function limpiarSeleccionArchivos() {
    selectedFilesArchivo = [];
    $('#previewContainerArchivo').empty();
    $('#div_subir_archivos').hide();
    $('#fileInputArchivo').val('');
}

function cerrarModalArchivos() {
    limpiarSeleccionArchivos();
    $('#modal_archivos').modal('hide');
}

function subirArchivos() {
    let equipo_id = $('#archivo_equipo_id').val();

    if (selectedFilesArchivo.length === 0) {
        getMessage('warning', 'Seleccione al menos un archivo');
        return false;
    }

    let formData = new FormData();
    formData.append('equipo_id', equipo_id);

    selectedFilesArchivo.forEach(function(file) {
        formData.append('archivos[]', file);
    });

    $.ajax({
        url: '../../controller/equipo.php?op=guardar_archivo_equipo',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        beforeSend: function() {
            $('#btnSubirArchivos').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Subiendo...');
        },
        success: function(response) {
            if (response && response.success == 1) {
                getMessage("success", response.message);

                limpiarSeleccionArchivos();
                recargarArchivos();

                // Actualizar la cantidad de archivos en la lista de equipos
                tabla.ajax.reload(null, false);
            } else {
                getMessage("error", response.message || 'Error desconocido');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', xhr.responseText);
            getMessage("error", "Error al procesar la solicitud");
        },
        complete: function() {
            $('#btnSubirArchivos').prop('disabled', false).html('<i class="ti ti-upload me-1"></i>Subir archivos');
        }
    });
}

function eliminarArchivoEquipo(equipo_archivo_id) {
    Swal.fire({
        text: `¿Deseas eliminar el archivo?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../../controller/equipo.php?op=eliminar_archivo',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: equipo_archivo_id
                },
                success: function(response) {
                    if (response.success) {
                        getMessage("success", response.message || "Archivo eliminado correctamente");

                        recargarArchivos();

                        // Actualizar la cantidad de archivos en la lista de equipos
                        tabla.ajax.reload(null, false);
                    } else {
                        getMessage("error", response.message);
                    }
                },
                error: function(xhr, status, error) {
                    getMessage("error", 'Error al eliminar el archivo');
                }
            });
        }
    });
}
