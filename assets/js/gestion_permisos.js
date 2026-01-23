/**
 * JavaScript para Gestión de Usuarios, Roles y Permisos
 * Sistema Caracol Captain - VERSIÓN CORREGIDA
 */

let tablaUsuarios, tablaRoles, tablaPermisos;
let rolSeleccionado = null;
let todosLosPermisos = [];

$(document).ready(function() {
    inicializarDataTables();
    cargarDatos();
    configurarEventos();
});

// ============================================================================
// INICIALIZACIÓN
// ============================================================================

function inicializarDataTables() {
    // Configuración común de idioma (CORREGIDO: HTTPS en lugar de HTTP)
    const configuracionIdioma = {
        url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
    };

    // DataTable de Usuarios
    tablaUsuarios = $('#tablaUsuarios').DataTable({
        language: configuracionIdioma,
        processing: true,
        columns: [
            { data: 'usuario_id' },
            { data: 'username' },
            { data: 'nombre_completo' },
            { data: 'email' },
            { 
                data: 'roles',
                render: function(data) {
                    if (!data || data.length === 0) return '<span class="badge badge-secondary">Sin roles</span>';
                    return data.map(r => `<span class="badge badge-primary">${r.rol_nombre}</span>`).join(' ');
                }
            },
            { 
                data: 'activo',
                render: function(data) {
                    return data ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>';
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-info" onclick="asignarRoles(${row.usuario_id}, '${row.nombre_completo}')" title="Asignar Roles">
                                <i class="fas fa-user-tag"></i>
                            </button>
                            <button class="btn btn-warning" onclick="asignarPermisos(${row.usuario_id}, '${row.nombre_completo}')" title="Permisos Específicos">
                                <i class="fas fa-key"></i>
                            </button>
                            <button class="btn btn-primary" onclick="verDetalleUsuario(${row.usuario_id})" title="Ver Detalle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });

    // DataTable de Roles
    tablaRoles = $('#tablaRoles').DataTable({
        language: configuracionIdioma,
        paging: false,
        searching: false,
        info: false,
        processing: true,
        columns: [
            { data: 'rol_nombre' },
            { data: 'rol_nivel' },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-info" onclick="verPermisosRol(${row.rol_id}, '${row.rol_nombre}')" title="Ver Permisos">
                                <i class="fas fa-list"></i>
                            </button>
                            <button class="btn btn-primary" onclick="editarRol(${row.rol_id})" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });

    // DataTable de Permisos
    tablaPermisos = $('#tablaPermisos').DataTable({
        language: configuracionIdioma,
        processing: true,
        columns: [
            { data: 'permiso_codigo' },
            { data: 'permiso_nombre' },
            { data: 'permiso_modulo' },
            { data: 'permiso_descripcion' },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-primary" onclick="editarPermiso(${row.permiso_id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                    `;
                }
            }
        ]
    });
}

function configurarEventos() {
    // Evento para guardar rol
    $('#formRol').on('submit', function(e) {
        e.preventDefault();
        guardarRol();
    });

    // Evento para guardar permiso
    $('#formPermiso').on('submit', function(e) {
        e.preventDefault();
        guardarPermiso();
    });
}

// ============================================================================
// CARGA DE DATOS
// ============================================================================

function cargarDatos() {
    cargarUsuarios();
    cargarRoles();
    cargarPermisos();
}

function cargarUsuarios() {
    $.ajax({
        url: 'logica/permisos_api.php',
        method: 'GET',
        data: { action: 'obtenerUsuarios' },
        dataType: 'json',
        beforeSend: function() {
            // Mostrar indicador de carga
            tablaUsuarios.processing(true);
        },
        success: function(response) {
            console.log('Respuesta usuarios:', response);
            if (response.success) {
                tablaUsuarios.clear().rows.add(response.data).draw();
            } else {
                mostrarError('Error al cargar usuarios: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', status, error);
            console.error('Respuesta:', xhr.responseText);
            mostrarError('Error de conexión al cargar usuarios. Verifica que el archivo permisos_api.php existe y la ruta es correcta.');
        },
        complete: function() {
            tablaUsuarios.processing(false);
        }
    });
}

function cargarRoles() {
    $.ajax({
        url: 'logica/permisos_api.php',
        method: 'GET',
        data: { action: 'obtenerRoles' },
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta roles:', response);
            if (response.success) {
                tablaRoles.clear().rows.add(response.data).draw();
            } else {
                mostrarError('Error al cargar roles: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX roles:', status, error);
            mostrarError('Error de conexión al cargar roles');
        }
    });
}

function cargarPermisos() {
    $.ajax({
        url: 'logica/permisos_api.php',
        method: 'GET',
        data: { action: 'obtenerPermisos' },
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta permisos:', response);
            if (response.success) {
                todosLosPermisos = response.data;
                tablaPermisos.clear().rows.add(response.data).draw();
            } else {
                mostrarError('Error al cargar permisos: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX permisos:', status, error);
            mostrarError('Error de conexión al cargar permisos');
        }
    });
}

// ============================================================================
// GESTIÓN DE USUARIOS
// ============================================================================

function asignarRoles(usuarioId, nombreUsuario) {
    $('#usuarioIdRoles').val(usuarioId);
    $('#usuarioNombreRoles').text(nombreUsuario);
    
    // Cargar roles disponibles y roles del usuario
    $.ajax({
        url: 'logica/permisos_api.php',
        method: 'GET',
        data: { 
            action: 'obtenerRolesUsuario',
            usuario_id: usuarioId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const rolesUsuario = response.data.map(r => r.rol_id);
                
                // Obtener todos los roles
                $.ajax({
                    url: 'logica/permisos_api.php',
                    method: 'GET',
                    data: { action: 'obtenerRoles' },
                    dataType: 'json',
                    success: function(responseRoles) {
                        if (responseRoles.success) {
                            let html = '';
                            responseRoles.data.forEach(function(rol) {
                                const checked = rolesUsuario.includes(rol.rol_id) ? 'checked' : '';
                                html += `
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" 
                                               id="rol_${rol.rol_id}" value="${rol.rol_id}" ${checked}>
                                        <label class="custom-control-label" for="rol_${rol.rol_id}">
                                            <strong>${rol.rol_nombre}</strong>
                                            <br><small class="text-muted">${rol.rol_descripcion || ''}</small>
                                        </label>
                                    </div>
                                    <hr>
                                `;
                            });
                            $('#listaRolesAsignar').html(html);
                            $('#modalAsignarRoles').modal('show');
                        }
                    }
                });
            }
        },
        error: function() {
            mostrarError('Error al cargar roles del usuario');
        }
    });
}

function guardarRolesUsuario() {
    const usuarioId = $('#usuarioIdRoles').val();
    const rolesSeleccionados = [];
    
    $('#listaRolesAsignar input[type="checkbox"]:checked').each(function() {
        rolesSeleccionados.push($(this).val());
    });
    
    $.ajax({
        url: 'logica/permisos_api.php',
        method: 'POST',
        data: {
            action: 'asignarRolesUsuario',
            usuario_id: usuarioId,
            roles: JSON.stringify(rolesSeleccionados)
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                mostrarExito('Roles asignados correctamente');
                $('#modalAsignarRoles').modal('hide');
                cargarUsuarios();
            } else {
                mostrarError('Error al asignar roles: ' + response.message);
            }
        },
        error: function() {
            mostrarError('Error de conexión al asignar roles');
        }
    });
}

function asignarPermisos(usuarioId, nombreUsuario) {
    $('#usuarioIdPermisos').val(usuarioId);
    $('#usuarioNombrePermisos').text(nombreUsuario);
    
    // Cargar permisos del usuario
    $.ajax({
        url: 'logica/permisos_api.php',
        method: 'GET',
        data: { 
            action: 'obtenerPermisosUsuario',
            usuario_id: usuarioId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const permisosUsuario = response.data;
                
                // Agrupar permisos por módulo
                const permisosPorModulo = {};
                todosLosPermisos.forEach(function(permiso) {
                    const modulo = permiso.permiso_modulo || 'Otros';
                    if (!permisosPorModulo[modulo]) {
                        permisosPorModulo[modulo] = [];
                    }
                    permisosPorModulo[modulo].push(permiso);
                });
                
                let html = '';
                Object.keys(permisosPorModulo).sort().forEach(function(modulo) {
                    html += `<h6 class="mt-3"><strong>${modulo}</strong></h6>`;
                    html += '<div class="row">';
                    
                    permisosPorModulo[modulo].forEach(function(permiso) {
                        const permisoUsuario = permisosUsuario.find(p => p.permiso_id === permiso.permiso_id);
                        let estadoPermiso = 'ninguno';
                        let colorBadge = 'secondary';
                        
                        if (permisoUsuario) {
                            if (permisoUsuario.origen === 'USUARIO_CONCEDIDO') {
                                estadoPermiso = 'concedido';
                                colorBadge = 'success';
                            } else if (permisoUsuario.origen === 'USUARIO_REVOCADO') {
                                estadoPermiso = 'revocado';
                                colorBadge = 'danger';
                            } else {
                                estadoPermiso = 'heredado';
                                colorBadge = 'info';
                            }
                        }
                        
                        html += `
                            <div class="col-md-6 mb-2">
                                <div class="card card-outline">
                                    <div class="card-body p-2">
                                        <strong>${permiso.permiso_nombre}</strong>
                                        <br><small class="text-muted">${permiso.permiso_codigo}</small>
                                        <br>
                                        <span class="badge badge-${colorBadge}">${estadoPermiso.toUpperCase()}</span>
                                        <div class="btn-group btn-group-sm float-right">
                                            <button class="btn btn-success" onclick="concederPermiso(${usuarioId}, ${permiso.permiso_id})" title="Conceder">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-danger" onclick="revocarPermiso(${usuarioId}, ${permiso.permiso_id})" title="Revocar">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <button class="btn btn-secondary" onclick="quitarPermisoEspecifico(${usuarioId}, ${permiso.permiso_id})" title="Heredar de rol">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    
                    html += '</div>';
                });
                
                $('#listaPermisosAsignar').html(html);
                $('#modalAsignarPermisos').modal('show');
            }
        },
        error: function() {
            mostrarError('Error al cargar permisos del usuario');
        }
    });
}

function concederPermiso(usuarioId, permisoId) {
    $.ajax({
        url: 'logica/permisos_api.php',
        method: 'POST',
        data: {
            action: 'asignarPermisoUsuario',
            usuario_id: usuarioId,
            permiso_id: permisoId,
            concedido: true
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                mostrarExito('Permiso concedido correctamente');
                // Recargar la vista de permisos
                asignarPermisos(usuarioId, $('#usuarioNombrePermisos').text());
            } else {
                mostrarError('Error al conceder permiso: ' + response.message);
            }
        },
        error: function() {
            mostrarError('Error de conexión');
        }
    });
}

function revocarPermiso(usuarioId, permisoId) {
    $.ajax({
        url: 'logica/permisos_api.php',
        method: 'POST',
        data: {
            action: 'asignarPermisoUsuario',
            usuario_id: usuarioId,
            permiso_id: permisoId,
            concedido: false
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                mostrarExito('Permiso revocado correctamente');
                // Recargar la vista de permisos
                asignarPermisos(usuarioId, $('#usuarioNombrePermisos').text());
            } else {
                mostrarError('Error al revocar permiso: ' + response.message);
            }
        },
        error: function() {
            mostrarError('Error de conexión');
        }
    });
}

function quitarPermisoEspecifico(usuarioId, permisoId) {
    // Eliminar permiso específico para que herede de roles
    mostrarConfirmacion(
        '¿Eliminar permiso específico?',
        'El usuario heredará el permiso de sus roles asignados.',
        function() {
            $.ajax({
                url: 'logica/permisos_api.php',
                method: 'POST',
                data: {
                    action: 'eliminarPermisoUsuario',
                    usuario_id: usuarioId,
                    permiso_id: permisoId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        mostrarExito('Permiso específico eliminado');
                        asignarPermisos(usuarioId, $('#usuarioNombrePermisos').text());
                    } else {
                        mostrarError('Error: ' + response.message);
                    }
                },
                error: function() {
                    mostrarError('Error de conexión');
                }
            });
        }
    );
}

function verDetalleUsuario(usuarioId) {
    // Mostrar modal con detalle completo del usuario
    $.ajax({
        url: 'logica/permisos_api.php',
        method: 'GET',
        data: { 
            action: 'obtenerDetalleUsuario',
            usuario_id: usuarioId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                let html = '<h5>Roles:</h5>';
                html += '<ul>';
                response.data.roles.forEach(function(rol) {
                    html += `<li><strong>${rol.rol_nombre}</strong></li>`;
                });
                html += '</ul>';
                
                html += '<h5>Permisos Efectivos:</h5>';
                html += '<div class="row">';
                response.data.permisos.forEach(function(permiso) {
                    html += `<div class="col-6 mb-1"><span class="badge badge-info">${permiso.permiso_nombre}</span></div>`;
                });
                html += '</div>';
                
                Swal.fire({
                    title: 'Detalle del Usuario',
                    html: html,
                    width: 800
                });
            }
        }
    });
}

// ============================================================================
// GESTIÓN DE ROLES
// ============================================================================

function nuevoRol() {
    $('#formRol')[0].reset();
    $('#rolId').val('');
    $('#tituloModalRol').text('Nuevo Rol');
    $('#modalRol').modal('show');
}

function editarRol(rolId) {
    $.ajax({
        url: 'logica/permisos_api.php',
        method: 'GET',
        data: { 
            action: 'obtenerRol',
            rol_id: rolId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const rol = response.data;
                $('#rolId').val(rol.rol_id);
                $('#rolCodigo').val(rol.rol_codigo);
                $('#rolNombre').val(rol.rol_nombre);
                $('#rolDescripcion').val(rol.rol_descripcion);
                $('#rolNivel').val(rol.rol_nivel);
                $('#tituloModalRol').text('Editar Rol');
                $('#modalRol').modal('show');
            }
        }
    });
}

function guardarRol() {
    const formData = $('#formRol').serialize();
    
    $.ajax({
        url: 'logica/permisos_api.php',
        method: 'POST',
        data: formData + '&action=guardarRol',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                mostrarExito('Rol guardado correctamente');
                $('#modalRol').modal('hide');
                cargarRoles();
            } else {
                mostrarError('Error al guardar rol: ' + response.message);
            }
        },
        error: function() {
            mostrarError('Error de conexión al guardar rol');
        }
    });
}

function verPermisosRol(rolId, rolNombre) {
    rolSeleccionado = rolId;
    $('#nombreRolSeleccionado').text(rolNombre);
    
    $.ajax({
        url: 'logica/permisos_api.php',
        method: 'GET',
        data: { 
            action: 'obtenerPermisosRol',
            rol_id: rolId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const permisosRol = response.data.map(p => p.permiso_id);
                
                // Agrupar permisos por módulo
                const permisosPorModulo = {};
                todosLosPermisos.forEach(function(permiso) {
                    const modulo = permiso.permiso_modulo || 'Otros';
                    if (!permisosPorModulo[modulo]) {
                        permisosPorModulo[modulo] = [];
                    }
                    permisosPorModulo[modulo].push(permiso);
                });
                
                let html = '';
                Object.keys(permisosPorModulo).sort().forEach(function(modulo) {
                    html += `
                        <div class="card card-outline card-primary collapsed-card">
                            <div class="card-header" data-card-widget="collapse" style="cursor: pointer;">
                                <h5 class="card-title"><strong>${modulo}</strong></h5>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                    `;
                    
                    permisosPorModulo[modulo].forEach(function(permiso) {
                        const checked = permisosRol.includes(permiso.permiso_id) ? 'checked' : '';
                        html += `
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input permiso-checkbox" 
                                       id="permiso_${permiso.permiso_id}" 
                                       value="${permiso.permiso_id}" ${checked}>
                                <label class="custom-control-label" for="permiso_${permiso.permiso_id}">
                                    <strong>${permiso.permiso_nombre}</strong>
                                    <br><small class="text-muted">${permiso.permiso_codigo}</small>
                                </label>
                            </div>
                        `;
                    });
                    
                    html += `
                            </div>
                        </div>
                    `;
                });
                
                $('#listaPermisosRol').html(html);
                $('#cardPermisosRol').show();
            }
        }
    });
}

function guardarPermisosRol() {
    const permisosSeleccionados = [];
    
    $('#listaPermisosRol input[type="checkbox"]:checked').each(function() {
        permisosSeleccionados.push($(this).val());
    });
    
    $.ajax({
        url: 'logica/permisos_api.php',
        method: 'POST',
        data: {
            action: 'asignarPermisosRol',
            rol_id: rolSeleccionado,
            permisos: JSON.stringify(permisosSeleccionados)
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                mostrarExito('Permisos del rol actualizados correctamente');
            } else {
                mostrarError('Error al actualizar permisos: ' + response.message);
            }
        },
        error: function() {
            mostrarError('Error de conexión al actualizar permisos');
        }
    });
}

// ============================================================================
// GESTIÓN DE PERMISOS
// ============================================================================

function nuevoPermiso() {
    $('#formPermiso')[0].reset();
    $('#permisoId').val('');
    $('#tituloModalPermiso').text('Nuevo Permiso');
    $('#modalPermiso').modal('show');
}

function editarPermiso(permisoId) {
    $.ajax({
        url: 'logica/permisos_api.php',
        method: 'GET',
        data: { 
            action: 'obtenerPermiso',
            permiso_id: permisoId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const permiso = response.data;
                $('#permisoId').val(permiso.permiso_id);
                $('#permisoCodigo').val(permiso.permiso_codigo);
                $('#permisoNombre').val(permiso.permiso_nombre);
                $('#permisoModulo').val(permiso.permiso_modulo);
                $('#permisoDescripcion').val(permiso.permiso_descripcion);
                $('#tituloModalPermiso').text('Editar Permiso');
                $('#modalPermiso').modal('show');
            }
        }
    });
}

function guardarPermiso() {
    const formData = $('#formPermiso').serialize();
    
    $.ajax({
        url: 'logica/permisos_api.php',
        method: 'POST',
        data: formData + '&action=guardarPermiso',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                mostrarExito('Permiso guardado correctamente');
                $('#modalPermiso').modal('hide');
                cargarPermisos();
            } else {
                mostrarError('Error al guardar permiso: ' + response.message);
            }
        },
        error: function() {
            mostrarError('Error de conexión al guardar permiso');
        }
    });
}

// ============================================================================
// UTILIDADES
// ============================================================================

function mostrarExito(mensaje) {
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: mensaje,
        timer: 2000,
        showConfirmButton: false
    });
}

function mostrarError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje
    });
}

function mostrarConfirmacion(titulo, texto, callback) {
    Swal.fire({
        title: titulo,
        text: texto,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    });
}