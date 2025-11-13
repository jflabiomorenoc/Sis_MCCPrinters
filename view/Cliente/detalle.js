let cliente_id;

$(document).ready(function () {

    $('#liCliente').addClass('active');
    
    $.urlParam = function(name) {
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        if (!results) return 0;
        
        var encrypted = results[1];
        // Hacer una petición al servidor para desencriptar
        var decrypted = null;
        
        $.ajax({
            url: '../../config/decrypt.php',
            type: 'POST',
            data: { encrypted: encrypted },
            async: false, // Importante para obtener el valor de inmediato
            success: function(response) {
                decrypted = response;
            }
        });
        
        return decrypted || 0;
    }
    
    cliente_id = $.urlParam('v');

    $(document).on('permisosUsuarioCargados', function() {
        aplicarPermisosUI('Clientes');

        // Cargar información del clientes
        obtenerInfoCliente(cliente_id)
        obtenerDireccionesCliente(cliente_id)
    });
    
    $('#btnNuevaDireccion').attr('onclick', 'nuevaDireccion(' + cliente_id + ')');
});

function obtenerInfoCliente(cliente_id) {

    $.post("../../controller/cliente.php?op=obtener", { id : cliente_id }, function (data) {
        data = JSON.parse(data);

        let mStrNombre;

        if (data.tipo_ruc == 1) {
            mStrNombre = data.razon_social;
            $('#lblTipo').addClass('bg-light-info');
        } else {
            mStrNombre = data.nombre_cliente + ' ' + data.apellido_paterno + ' ' + data.apellido_materno;
            $('#lblTipo').addClass('bg-light-secondary');
        }

        $('#lblLiCliente').html(mStrNombre);
        $('#lblNomCliente').html(mStrNombre);
        
        $('#lblRuc').html(data.ruc);
        $('#lblTipo').html(data.nom_tipo_ruc);
        
        if (data.estado == 1) {
            $('#lblEstado').addClass('bg-light-success');
        } else {
            $('#lblEstado').addClass('bg-light-danger');
        }

        $('#lblEstado').html(data.nom_estado);
    });
}

function nuevaDireccion(cliente_id){

    $("#form-direccion")[0].reset();
    $('#direccion_id').val('');

    $('#cliente_id').val(cliente_id)
    $('#lblDireccion').html('Nueva dirección');
    $('#modal_direccion').modal('show');
}

function editarDireccion(direccion_id){

    $.post("../../controller/cliente.php?op=obtener_direccion", { direccion_id : direccion_id }, function (data) {
        data = JSON.parse(data);

        $("#direccion_id").val(data.id)
        $("#cliente_id").val(data.cliente_id)
        $("#departamento").val(data.departamento)
        $("#provincia").val(data.provincia)
        $("#distrito").val(data.distrito)
        $("#direccion").val(data.direccion)
        $("#referencia").val(data.referencia)

        if (data.es_principal == 1) {
            $('#es_principal').prop('checked', true); 
        } else {
            $('#es_principal').prop('checked', false); 
        }
    });

    $('#lblDireccion').html('Editar dirección')
    $('#modal_direccion').modal('show');
}

function nuevoContacto(direccion_id){

    $("#form-contacto")[0].reset();
    $('#contacto_id').val('');

    $('#c_direccion_id').val(direccion_id);

    $('#lblContacto').html('Nuevo contacto')
    $('#modal_contacto').modal('show');
}

function editarContacto(contacto_id){

    $.post("../../controller/cliente.php?op=obtener_contacto", { contacto_id : contacto_id }, function (data) {
        data = JSON.parse(data);

        $("#contacto_id").val(data.id);
        $("#c_direccion_id").val(data.direccion_id);
        $("#nombre_contacto").val(data.nombre_contacto);
        $("#cargo_contacto").val(data.cargo_contacto);
        $("#email_contacto").val(data.email_contacto);
        $("#telefono_contacto").val(data.telefono_contacto);
        $("#fecha_cumple").val(data.fecha_cumple);
    });

    $('#lblContacto').html('Editar contacto')
    $('#modal_contacto').modal('show');
}

// Función para obtener y mostrar direcciones
function obtenerDireccionesCliente(cliente_id) {
    $.post("../../controller/cliente.php?op=listar_direcciones", 
        { cliente_id: cliente_id }, 
        function (data) {
            data = JSON.parse(data);
            
            let html = '';
            
            if (data.length > 0) {
                data.forEach(function(direccion) {
                    html += generarHtmlDireccion(direccion);
                });
            } else {
                html = `
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="ti ti-info-circle me-2"></i>
                            No hay direcciones registradas para este cliente
                        </div>
                    </div>
                `;
            }
            
            $('#contenedorDirecciones').html(html);
            
            // Cargar contactos para cada dirección
            data.forEach(function(direccion) {
                obtenerContactosDireccion(direccion.id);
            });
        }
    );
}

// Generar HTML de cada dirección
function generarHtmlDireccion(direccion) {
    let badgeClass  = direccion.es_principal == 1 ? 'bg-light-success' : 'bg-light-secondary';
    let badgeText   = direccion.es_principal == 1 ? 'PRINCIPAL' : 'SECUNDARIA';
    let cardStatus  = direccion.es_principal == 1 ? 'close-ticket' : 'open-ticket';
    let cardClass   = direccion.es_principal == 1 ? 'address-card main' : 'address-card';
    
    let ubicacion = [direccion.departamento, direccion.provincia, direccion.distrito]
        .filter(Boolean)
        .join(', ');

    // Verificar permisos del usuario
    const puedeCrear = tienePermiso('Clientes', 'crear');
    const puedeEditar = tienePermiso('Clientes', 'editar');
    const puedeEliminar = tienePermiso('Clientes', 'eliminar');
    
    // Generar menú dropdown según permisos
    let menuOpciones = '';
    
    // Opción Editar - Solo si tiene permiso
    if (puedeEditar) {
        menuOpciones += `
            <a class="dropdown-item" style="cursor: pointer;" onclick="editarDireccion(${direccion.id})">
                <i class="ti ti-pencil me-2"></i>Editar
            </a>`;
    }
    
    // Opción Eliminar - Solo si tiene permiso Y NO es principal
    if (puedeEliminar && direccion.es_principal != 1) {
        menuOpciones += `
            <a class="dropdown-item text-danger" style="cursor: pointer;" onclick="eliminarDireccion(${direccion.id})">
                <i class="ti ti-trash me-2"></i>Eliminar
            </a>`;
    }
    
    // Mostrar dropdown solo si hay opciones disponibles
    let dropdownHTML = '';
    if (menuOpciones) {
        dropdownHTML = `
            <div class="dropdown">
                <a class="avtar avtar-s btn-link-secondary dropdown-toggle arrow-none" style="cursor: pointer;" 
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="ti ti-dots-vertical f-18"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    ${menuOpciones}
                </div>
            </div>`;
    }
    
    // Botón Agregar Contacto - Solo si tiene permiso de crear
    let btnAgregarContacto = puedeCrear ? `
        <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2"
                onclick="nuevoContacto(${direccion.id})">
            <i class="ph-duotone ph-plus-circle"></i> 
            Agregar
        </button>` : '';
    
    return `
        <div class="col-xl-6">
            <div class="ticket-card ${cardStatus} card ${cardClass} mb-3 rounded p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="badge ${badgeClass} rounded-pill">${badgeText}</span>
                    ${dropdownHTML}
                </div>
                <h5 class="mb-1">${direccion.direccion}</h5>
                <p class="mb-1 text-muted text-uppercase"><i class="ti ti-map-pin me-1"></i> ${ubicacion}</p>
                ${direccion.referencia ? `<p class="mb-1 text-muted">${direccion.referencia}</p>` : ''}
                <hr />
                
                <div class="d-flex align-items-center justify-content-between pb-2">
                    <h5 class="mb-0">Contactos (${direccion.total_contactos})</h5>
                    ${btnAgregarContacto}
                </div>
                <ul class="list-group list-group-flush" id="contactos_${direccion.id}">
                    <li class="list-group-item text-center">
                        <div class="spinner-border spinner-border-sm text-secondary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    `;
}

// Obtener contactos de una dirección específica
function obtenerContactosDireccion(direccion_id) {
    $.post("../../controller/cliente.php?op=listar_contactos", 
        { direccion_id: direccion_id }, 
        function (data) {
            data = JSON.parse(data);
            
            let html = '';
            
            if (data.length > 0) {
                data.forEach(function(contacto) {
                    html += generarHtmlContacto(contacto);
                });
            } else {
                html = `
                    <li class="list-group-item text-center text-muted">
                        <i class="ti ti-users-off me-2"></i>
                        No hay contactos registrados
                    </li>
                `;
            }
            
            $(`#contactos_${direccion_id}`).html(html);
        }
    );
}

// Generar HTML de cada contacto
function generarHtmlContacto(contacto) {
    // Verificar permisos del usuario
    const puedeEditar = tienePermiso('Clientes', 'editar');
    const puedeEliminar = tienePermiso('Clientes', 'eliminar');
    
    // Generar menú dropdown según permisos
    let menuOpciones = '';
    
    // Opción Editar - Solo si tiene permiso
    if (puedeEditar) {
        menuOpciones += `
            <a class="dropdown-item" style="cursor: pointer;" onclick="editarContacto(${contacto.id})">
                <i class="ti ti-pencil me-2"></i>Editar
            </a>`;
    }
    
    // Opción Eliminar - Solo si tiene permiso
    if (puedeEliminar) {
        menuOpciones += `
            <a class="dropdown-item text-danger" style="cursor: pointer;" onclick="eliminarContacto(${contacto.id})">
                <i class="ti ti-trash me-2"></i>Eliminar
            </a>`;
    }
    
    // Mostrar dropdown solo si hay opciones disponibles
    let dropdownHTML = '';
    if (menuOpciones) {
        dropdownHTML = `
            <div class="flex-shrink-0">
                <div class="dropdown">
                    <a class="avtar avtar-s btn-link-secondary dropdown-toggle arrow-none" style="cursor: pointer;"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ti ti-dots-vertical f-18"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        ${menuOpciones}
                    </div>
                </div>
            </div>`;
    }
    
    return `
        <li class="list-group-item mt-1" style="padding: 8px; background: #f8f9fa; border-radius: 8px;">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <h6 class="fw-semibold mb-1">${contacto.nombre_contacto}</h6>
                    ${contacto.cargo_contacto ? `<span class="text-muted d-block mb-1"><small>${contacto.cargo_contacto}</small></span>` : ''}
                    ${contacto.telefono_contacto ? `<span class="d-block"><small><i class="ti ti-phone me-1"></i>${contacto.telefono_contacto}</small></span>` : ''}
                    ${contacto.email_contacto ? `<span class="d-block"><small><i class="ti ti-mail me-1"></i>${contacto.email_contacto}</small></span>` : ''}
                </div>
                ${dropdownHTML}
            </div>
        </li>
    `;
}

function guardarDireccion() {
    // Capturar el formulario y crear FormData
    var formData = new FormData($("#form-direccion")[0]);

    let campos = [
        "#departamento",
        "#provincia",
        "#distrito",
        "#direccion"
    ];

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
    formData.set('es_principal', $("#es_principal").is(':checked') ? '1' : '0');    
    
    // Enviar por AJAX
    $.ajax({
        url: '../../controller/cliente.php?op=ingresar_editar_direccion',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json', // IMPORTANTE: Esperar JSON
        beforeSend: function() {
            $('button[onclick="guardarDireccion()"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');
        },
        success: function (datos) {
            
            if (datos && datos.success == 1) {
                
                $('#modal_direccion').modal('hide');
                
                $("#form-direccion")[0].reset();
                getMessage("success", datos.message || "Dirección registrada correctamente");

                obtenerDireccionesCliente(cliente_id)

            } else {
                getMessage("error", datos.message || "Error al guardar la dirección");
            }
        },
        error: function(xhr, status, error) {
            getMessage("error", 'Error al comunicarse con el servidor');
        },
        complete: function() {
            $('button[onclick="guardarDireccion()"]').prop('disabled', false).html('Guardar');
        }
    });
}

function eliminarDireccion(direccion_id) {
    Swal.fire({
        text: `¿Deseas eliminar la dirección?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../../controller/cliente.php?op=eliminar_direccion',
                type: 'POST',
                dataType: 'json',
                data: {
                    direccion_id  : direccion_id,
                },
                success: function(response) {
                    if (response.success) {
                        getMessage("success", response.message || "Error desconocido");
                        
                        obtenerDireccionesCliente(cliente_id)
                        
                    } else {
                        getMessage("error", response.message);
                    }
                },
                error: function(xhr, status, error) {
                    getMessage("error", 'Error al eliminar la dirección');
                }
            });
        }
    });
}

function guardarContacto() {
    // Capturar el formulario y crear FormData
    var formData = new FormData($("#form-contacto")[0]);

    let campos = [
        "#nombre_contacto",
        "#cargo_contacto",
        "#email_contacto",
        "#telefono_contacto"
    ];

    // Validación básica de campos requeridos
    for (let i = 0; i < campos.length; i++) {
        if ($(campos[i]).val().trim() === "") {
            let nombreCampo = campos[i].replace("#", "").replace("_", " ");
            getMessage("warning", "El campo " + nombreCampo + " es requerido");
            $(campos[i]).focus();
            return false;
        }
    }
    
    // Enviar por AJAX
    $.ajax({
        url: '../../controller/cliente.php?op=ingresar_editar_contacto',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json', // IMPORTANTE: Esperar JSON
        beforeSend: function() {
            $('button[onclick="guardarContacto()"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');
        },
        success: function (datos) {
            
            if (datos && datos.success == 1) {
                
                $('#modal_contacto').modal('hide');
                
                $("#form-contacto")[0].reset();
                getMessage("success", datos.message || "Contacto registrado correctamente");

                obtenerDireccionesCliente(cliente_id)

            } else {
                getMessage("error", datos.message || "Error al guardar el contacto");
            }
        },
        error: function(xhr, status, error) {
            getMessage("error", 'Error al comunicarse con el servidor');
        },
        complete: function() {
            $('button[onclick="guardarContacto()"]').prop('disabled', false).html('Guardar');
        }
    });
}

function eliminarContacto(contacto_id) {
    Swal.fire({
        text: `¿Deseas eliminar el contacto?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../../controller/cliente.php?op=eliminar_contacto',
                type: 'POST',
                dataType: 'json',
                data: {
                    contacto_id  : contacto_id,
                },
                success: function(response) {
                    if (response.success) {
                        getMessage("success", response.message || "Error desconocido");
                        
                        obtenerDireccionesCliente(cliente_id)
                        
                    } else {
                        getMessage("error", response.message);
                    }
                },
                error: function(xhr, status, error) {
                    getMessage("error", 'Error al eliminar contacto');
                }
            });
        }
    });
}