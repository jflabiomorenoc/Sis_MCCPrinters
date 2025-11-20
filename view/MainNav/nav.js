// Variable global para almacenar permisos
var permisosUsuario = {};
var permisosModuloCargados = false;
var esAdministrador = false;
var perfilesUsuario = [];  // ✅ NUEVO: Array con los perfiles del usuario

$(document).ready(function() {
    cargarMenuDinamico();
});

function cargarMenuDinamico() {
    $.ajax({
        url: '../../controller/perfil.php?op=obtener_menu',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success == 1) {
                // Guardar si es administrador
                esAdministrador = response.es_admin;
                
                // ✅ NUEVO: Guardar perfiles del usuario
                if (response.perfiles) {
                    perfilesUsuario = response.perfiles;
                }
                
                // Guardar permisos en variable global
                response.modulos.forEach(function(modulo) {
                    permisosUsuario[modulo.modulo_nombre] = {
                        puede_ver: modulo.puede_ver,
                        puede_crear: modulo.puede_crear,
                        puede_editar: modulo.puede_editar,
                        puede_eliminar: modulo.puede_eliminar
                    };
                });
                
                // Marcar que los permisos ya están cargados
                permisosModuloCargados = true;
                
                // Construir menú
                construirMenu(response.modulos, response.es_admin);
                
                // Marcar módulo activo
                marcarModuloActivo();
                
                // Disparar evento personalizado cuando los permisos estén listos
                $(document).trigger('permisosUsuarioCargados');
                
            } else {
                window.location.href = '../../index.php';
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar menú:', error);
        }
    });
}

// ✅ NUEVA FUNCIÓN: Verificar si es administrador
function esUsuarioAdministrador() {
    return esAdministrador === true;
}

function tienePerfil(perfil_id) {
    return perfilesUsuario.includes(parseInt(perfil_id));
}

function esUsuarioTecnico() {
    return tienePerfil(2);  // perfil_id = 2 (Técnico)
}

function construirMenu(modulos, esAdmin) {
    const $navbar = $('.pc-navbar');
    
    // Limpiar menú actual (mantener solo el caption)
    $navbar.find('.pc-item:not(.pc-caption)').remove();
    
    // Mapeo de módulos a sus configuraciones
    const modulosConfig = {
        'Dashboard': {
            url: '../Dashboard/',
            icono: '#custom-status-up',
            id: ''
        },
        'Contratos': {
            url: '../Contrato/',
            icono: '#custom-bill',
            id: 'liContrato'
        },
        'Tickets': {
            url: '../Ticket/',
            icono: '#custom-flag',
            id: 'liTicket'
        },
        'Clientes': {
            url: '../Cliente/',
            icono: '#custom-user',
            id: 'liCliente'
        },
        'Proveedores': {
            url: '../Proveedor/',
            icono: '#custom-user',
            id: ''
        },
        'Equipos': {
            url: '../Equipo/',
            icono: '#custom-layer',
            id: ''
        },
        'Usuarios': {
            url: '../Usuario/',
            icono: '#custom-user-square',
            id: ''
        },
        'Perfiles': {
            url: '../Perfil/',
            icono: '#custom-share-bold',
            id: ''
        },
        'Reportes': {
            url: '#!',
            icono: '#custom-note-1',
            id: '',
            submenu: [
                { nombre: 'Reporte 1', url: '../demo/layout-vertical.html' },
                { nombre: 'Reporte 2', url: '../demo/layout-horizontal.html' }
            ]
        }
    };
    
    // Construir items del menú
    modulos.forEach(function(modulo) {
        const config = modulosConfig[modulo.modulo_nombre];
        
        if (!config) return; // Si no está en la config, saltar
        
        let menuItem = '';
        
        if (config.submenu) {
            // Menú con submenú
            menuItem = `
                <li class="pc-item pc-hasmenu" ${config.id ? 'id="' + config.id + '"' : ''}>
                    <a href="${config.url}" class="pc-link">
                        <span class="pc-micon">
                            <svg class="pc-icon">
                                <use xlink:href="${config.icono}"></use>
                            </svg>
                        </span>
                        <span class="pc-mtext">${modulo.modulo_nombre}</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        ${config.submenu.map(sub => 
                            `<li class="pc-item"><a class="pc-link" href="${sub.url}">${sub.nombre}</a></li>`
                        ).join('')}
                    </ul>
                </li>
            `;
        } else {
            // Menú simple
            menuItem = `
                <li class="pc-item" ${config.id ? 'id="' + config.id + '"' : ''}>
                    <a href="${config.url}" class="pc-link">
                        <span class="pc-micon">
                            <svg class="pc-icon">
                                <use xlink:href="${config.icono}"></use>
                            </svg>
                        </span>
                        <span class="pc-mtext">${modulo.modulo_nombre}</span>
                    </a>
                </li>
            `;
        }
        
        $navbar.append(menuItem);
    });
    
    // Reinicializar feather icons si se usan
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

function marcarModuloActivo() {
    // Obtener la URL actual
    const urlActual = window.location.pathname;
    
    // Buscar el elemento del menú que coincida con la URL
    $('.pc-navbar .pc-item a').each(function() {
        const href = $(this).attr('href');
        
        if (href && urlActual.includes(href.replace('../', ''))) {
            $(this).closest('.pc-item').addClass('active');
        }
    });
}

// Función para verificar permisos en tiempo real
function tienePermiso(modulo, accion = 'ver') {
    // Si está en memoria
    if (permisosUsuario[modulo]) {
        switch(accion) {
            case 'ver':
                return permisosUsuario[modulo].puede_ver == 1;
            case 'crear':
                return permisosUsuario[modulo].puede_crear == 1;
            case 'editar':
                return permisosUsuario[modulo].puede_editar == 1;
            case 'eliminar':
                return permisosUsuario[modulo].puede_eliminar == 1;
            default:
                return false;
        }
    }
    
    return false;
}

// Función async para verificar permisos desde el servidor
async function verificarPermisoServidor(modulo, accion = 'ver') {
    try {
        const response = await $.ajax({
            url: '../../controller/perfil.php?op=verificar_permiso',
            type: 'POST',
            data: {
                modulo: modulo,
                accion: accion
            },
            dataType: 'json'
        });
        
        return response.tiene_permiso;
        
    } catch (error) {
        console.error('Error al verificar permiso:', error);
        return false;
    }
}

function aplicarPermisosUI(modulo) {

    $('.btn-reporte').hide();

    // Si no tiene permiso para ver, redirigir
    if (!tienePermiso(modulo, 'ver')) {
        window.location.href = '../error.php';
        return;
    }
    
    // Ocultar botón crear si no tiene permiso
    if (!tienePermiso(modulo, 'crear')) {
        $('[data-permiso="crear"]').hide();
        $('#btnAgregar, #btnNuevo, .btn-crear').hide();
        $('#btnAgregar, #btnNuevo, .btn-crear').attr('style', 'display: none !important');
    }
    
    // Ocultar botones editar si no tiene permiso
    if (!tienePermiso(modulo, 'editar')) {
        $('[data-permiso="editar"]').hide();
        $('.btn-editar').hide();
    }
    
    // Ocultar botones eliminar si no tiene permiso
    if (!tienePermiso(modulo, 'eliminar')) {
        $('[data-permiso="eliminar"]').hide();
        $('.btn-eliminar').hide();
    }

    $('#btnVerTodos').hide();

    // ✅ NUEVO: Mostrar botón "Ver todos" SOLO para técnicos
    if (esUsuarioTecnico() && !esAdministrador) {
        $('#btnVerTodos').show();
        $('.reporte-ticket').show();
    } else if (esAdministrador) {
        $('.btn-reporte').show();
        $('#btnVerTodos').hide();
    }
}