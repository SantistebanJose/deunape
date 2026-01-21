<?php
include('cabecera.php');
?>

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Gestión de Roles y Permisos</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="index.php">
                        <i class="icon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Administrador</a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Roles</a>
                </li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Lista de Roles</h4>
                            <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal" data-bs-target="#modalAgregarRol">
                                <i class="fa fa-plus"></i>
                                Agregar Rol
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tablaRoles" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre del Rol</th>
                                        <th>Descripción</th>
                                        <th>Estado</th>
                                        <th>Fecha Creación</th>
                                        <th style="width: 10%">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyTablaRoles">
                                    <!-- Datos cargados dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Rol -->
<div class="modal fade" id="modalAgregarRol" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Rol</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formAgregarRol">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nombre del Rol *</label>
                                <input type="text" class="form-control" id="nombreRol" name="nombreRol" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Estado *</label>
                                <select class="form-control" id="estadoRol" name="estadoRol">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Descripción</label>
                                <textarea class="form-control" id="descripcionRol" name="descripcionRol" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h5 class="mb-3">Permisos del Rol</h5>
                    
                    <div id="contenedorPermisos">
                        <!-- Los permisos se cargarán aquí dinámicamente -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="guardarRol()">Guardar Rol</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Rol -->
<div class="modal fade" id="modalEditarRol" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Rol</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarRol">
                    <input type="hidden" id="editIdRol" name="editIdRol">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nombre del Rol *</label>
                                <input type="text" class="form-control" id="editNombreRol" name="editNombreRol" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Estado *</label>
                                <select class="form-control" id="editEstadoRol" name="editEstadoRol">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Descripción</label>
                                <textarea class="form-control" id="editDescripcionRol" name="editDescripcionRol" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h5 class="mb-3">Permisos del Rol</h5>
                    
                    <div id="contenedorPermisosEditar">
                        <!-- Los permisos se cargarán aquí dinámicamente -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="actualizarRol()">Actualizar Rol</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    cargarRoles();
    
    // Cargar permisos cuando se abre el modal de agregar
    $('#modalAgregarRol').on('shown.bs.modal', function () {
        cargarModulosYSubmodulos('contenedorPermisos');
    });
});

function cargarRoles() {
    $.ajax({
        url: 'logica/clssRoles.php',
        type: 'POST',
        data: { accion: 'LISTAR_ROLES' },
        dataType: 'json',
        success: function(response) {
            let html = '';
            response.forEach(function(rol) {
                let estadoBadge = rol.estado == 1 
                    ? '<span class="badge bg-success">Activo</span>' 
                    : '<span class="badge bg-danger">Inactivo</span>';
                
                html += `
                    <tr>
                        <td>${rol.id_rol}</td>
                        <td><strong>${rol.nombre_rol}</strong></td>
                        <td>${rol.descripcion || 'Sin descripción'}</td>
                        <td>${estadoBadge}</td>
                        <td>${new Date(rol.created_at).toLocaleDateString()}</td>
                        <td>
                            <div class="form-button-action">
                                <button type="button" class="btn btn-link btn-primary btn-lg" 
                                        onclick="abrirModalEditar(${rol.id_rol})" 
                                        data-bs-toggle="tooltip" title="Editar">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-link btn-danger" 
                                        onclick="eliminarRol(${rol.id_rol})" 
                                        data-bs-toggle="tooltip" title="Eliminar">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            $('#bodyTablaRoles').html(html);
            
            if ($.fn.DataTable.isDataTable('#tablaRoles')) {
                $('#tablaRoles').DataTable().destroy();
            }
            
            $('#tablaRoles').DataTable({
                pageLength: 10,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                }
            });
        }
    });
}

function cargarModulosYSubmodulos(contenedor = 'contenedorPermisos', permisosActuales = null) {
    $.ajax({
        url: 'logica/clssRoles.php',
        type: 'POST',
        data: { accion: 'LISTAR_MODULOS_SUBMODULOS' },
        dataType: 'json',
        success: function(modulos) {
            if (!modulos || modulos.length === 0) {
                $(`#${contenedor}`).html('<div class="alert alert-warning">No hay módulos disponibles. Ejecuta el script SQL de inserción de módulos.</div>');
                return;
            }
            
            let html = '<div class="accordion" id="accordionPermisos' + contenedor + '">';
            
            modulos.forEach(function(modulo, index) {
                let moduloChecked = '';
                if (permisosActuales && permisosActuales.modulos) {
                    moduloChecked = permisosActuales.modulos[modulo.identificador] ? 'checked' : '';
                }
                
                html += `
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button ${index === 0 ? '' : 'collapsed'}" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#collapse${contenedor}${modulo.id_modulo}">
                                <div class="form-check me-3">
                                    <input class="form-check-input modulo-check" type="checkbox" 
                                           id="modulo_${contenedor}_${modulo.id_modulo}" 
                                           data-modulo="${modulo.identificador}"
                                           ${moduloChecked}
                                           onclick="event.stopPropagation();">
                                </div>
                                <i class="${modulo.icono} me-2"></i>
                                ${modulo.nombre_modulo}
                            </button>
                        </h2>
                        <div id="collapse${contenedor}${modulo.id_modulo}" 
                             class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" 
                             data-bs-parent="#accordionPermisos${contenedor}">
                            <div class="accordion-body">
                `;
                
                if (modulo.submodulos && modulo.submodulos.length > 0) {
                    html += '<div class="row">';
                    modulo.submodulos.forEach(function(submodulo) {
                        let submoduloChecked = '';
                        if (permisosActuales && permisosActuales.submodulos) {
                            submoduloChecked = permisosActuales.submodulos[submodulo.identificador] ? 'checked' : '';
                        }
                        
                        html += `
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input submodulo-check" type="checkbox" 
                                           id="sub_${contenedor}_${submodulo.id_submodulo}"
                                           data-modulo="${modulo.identificador}"
                                           data-submodulo="${submodulo.identificador}"
                                           ${submoduloChecked}>
                                    <label class="form-check-label" for="sub_${contenedor}_${submodulo.id_submodulo}">
                                        ${submodulo.nombre_submodulo}
                                    </label>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                } else {
                    html += '<p class="text-muted">No hay submódulos disponibles</p>';
                }
                
                html += `
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            $(`#${contenedor}`).html(html);
            
            // Sincronizar checkboxes
            $(`#${contenedor} .modulo-check`).on('change', function() {
                let modulo = $(this).data('modulo');
                let checked = $(this).is(':checked');
                $(`#${contenedor} .submodulo-check[data-modulo="${modulo}"]`).prop('checked', checked);
            });
            
            $(`#${contenedor} .submodulo-check`).on('change', function() {
                let modulo = $(this).data('modulo');
                let totalSubs = $(`#${contenedor} .submodulo-check[data-modulo="${modulo}"]`).length;
                let checkedSubs = $(`#${contenedor} .submodulo-check[data-modulo="${modulo}"]:checked`).length;
                
                if (checkedSubs > 0) {
                    $(`#${contenedor} .modulo-check[data-modulo="${modulo}"]`).prop('checked', true);
                } else {
                    $(`#${contenedor} .modulo-check[data-modulo="${modulo}"]`).prop('checked', false);
                }
            });
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar módulos:', error);
            console.error('Response:', xhr.responseText);
            $(`#${contenedor}`).html('<div class="alert alert-danger">Error al cargar los módulos. Verifica la consola.</div>');
        }
    });
}

function obtenerPermisos() {
    let permisos = {
        modulos: {},
        submodulos: {}
    };
    
    // Obtener checkboxes de AMBOS contenedores (agregar y editar)
    $('.modulo-check:checked').each(function() {
        let modulo = $(this).data('modulo');
        permisos.modulos[modulo] = true;
    });
    
    $('.submodulo-check:checked').each(function() {
        let submodulo = $(this).data('submodulo');
        permisos.submodulos[submodulo] = true;
    });
    
    console.log('Permisos obtenidos:', permisos);
    return permisos;
}

function guardarRol() {
    let nombreRol = $('#nombreRol').val().trim();
    let descripcion = $('#descripcionRol').val().trim();
    let estado = $('#estadoRol').val();
    let permisos = obtenerPermisos();
    
    if (!nombreRol) {
        Swal.fire('Error', 'El nombre del rol es obligatorio', 'error');
        return;
    }
    
    console.log('Datos a enviar:', {
        nombre_rol: nombreRol,
        descripcion: descripcion,
        estado: estado,
        permisos: permisos
    });
    
    $.ajax({
        url: 'logica/clssRoles.php',
        type: 'POST',
        data: {
            accion: 'CREAR_ROL',
            nombre_rol: nombreRol,
            descripcion: descripcion,
            estado: estado,
            permisos: JSON.stringify(permisos)
        },
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta del servidor:', response);
            if (response.success) {
                Swal.fire('Éxito', 'Rol creado correctamente', 'success');
                $('#modalAgregarRol').modal('hide');
                $('#formAgregarRol')[0].reset();
                cargarRoles();
            } else {
                Swal.fire('Error', response.message || 'Error al crear el rol', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error AJAX:', error);
            console.error('Response:', xhr.responseText);
            Swal.fire('Error', 'Error de comunicación con el servidor', 'error');
        }
    });
}

function abrirModalEditar(idRol) {
    $.ajax({
        url: 'logica/clssRoles.php',
        type: 'POST',
        data: { accion: 'OBTENER_ROL', id_rol: idRol },
        dataType: 'json',
        success: function(rol) {
            $('#editIdRol').val(rol.id_rol);
            $('#editNombreRol').val(rol.nombre_rol);
            $('#editDescripcionRol').val(rol.descripcion);
            $('#editEstadoRol').val(rol.estado);
            
            let permisos = rol.permisos ? JSON.parse(rol.permisos) : null;
            cargarModulosYSubmodulos('contenedorPermisosEditar', permisos);
            
            $('#modalEditarRol').modal('show');
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener rol:', error);
            Swal.fire('Error', 'No se pudo cargar la información del rol', 'error');
        }
    });
}

function actualizarRol() {
    let idRol = $('#editIdRol').val();
    let nombreRol = $('#editNombreRol').val().trim();
    let descripcion = $('#editDescripcionRol').val().trim();
    let estado = $('#editEstadoRol').val();
    let permisos = obtenerPermisos();
    
    if (!nombreRol) {
        Swal.fire('Error', 'El nombre del rol es obligatorio', 'error');
        return;
    }
    
    $.ajax({
        url: 'logica/clssRoles.php',
        type: 'POST',
        data: {
            accion: 'ACTUALIZAR_ROL',
            id_rol: idRol,
            nombre_rol: nombreRol,
            descripcion: descripcion,
            estado: estado,
            permisos: JSON.stringify(permisos)
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('Éxito', 'Rol actualizado correctamente', 'success');
                $('#modalEditarRol').modal('hide');
                cargarRoles();
            } else {
                Swal.fire('Error', response.message || 'Error al actualizar el rol', 'error');
            }
        }
    });
}

function eliminarRol(idRol) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede revertir",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'logica/clssRoles.php',
                type: 'POST',
                data: { accion: 'ELIMINAR_ROL', id_rol: idRol },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Eliminado', 'El rol ha sido eliminado', 'success');
                        cargarRoles();
                    } else {
                        Swal.fire('Error', response.message || 'Error al eliminar el rol', 'error');
                    }
                }
            });
        }
    });
}
</script>

<?php include("pie.php"); ?>