<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios, Roles y Permisos</title>
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    
    <style>
        .card-header {
            cursor: pointer;
        }
        .badge-permission {
            margin: 2px;
            font-size: 0.85rem;
        }
        .select2-container {
            width: 100% !important;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    
    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1><i class="fas fa-user-shield"></i> Gestión de Usuarios y Permisos</h1>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                
                <!-- Tabs -->
                <ul class="nav nav-tabs" id="mainTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="usuarios-tab" data-toggle="tab" href="#usuarios" role="tab">
                            <i class="fas fa-users"></i> Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="roles-tab" data-toggle="tab" href="#roles" role="tab">
                            <i class="fas fa-user-tag"></i> Roles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="permisos-tab" data-toggle="tab" href="#permisos" role="tab">
                            <i class="fas fa-key"></i> Permisos
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="mainTabsContent">
                    
                    <!-- TAB: USUARIOS -->
                    <div class="tab-pane fade show active" id="usuarios" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Listado de Usuarios</h3>
                            </div>
                            <div class="card-body">
                                <table id="tablaUsuarios" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Usuario</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Roles</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Datos cargados vía AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- TAB: ROLES -->
                    <div class="tab-pane fade" id="roles" role="tabpanel">
                        <div class="row">
                            <!-- Lista de Roles -->
                            <div class="col-md-5">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Roles del Sistema</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-primary btn-sm" onclick="nuevoRol()">
                                                <i class="fas fa-plus"></i> Nuevo Rol
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <table id="tablaRoles" class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Rol</th>
                                                    <th>Nivel</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Datos cargados vía AJAX -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Permisos del Rol Seleccionado -->
                            <div class="col-md-7">
                                <div class="card" id="cardPermisosRol" style="display: none;">
                                    <div class="card-header">
                                        <h3 class="card-title">Permisos del Rol: <strong id="nombreRolSeleccionado"></strong></h3>
                                    </div>
                                    <div class="card-body">
                                        <div id="listaPermisosRol">
                                            <!-- Permisos agrupados por módulo -->
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="button" class="btn btn-success" onclick="guardarPermisosRol()">
                                            <i class="fas fa-save"></i> Guardar Cambios
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB: PERMISOS -->
                    <div class="tab-pane fade" id="permisos" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Catálogo de Permisos</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="nuevoPermiso()">
                                        <i class="fas fa-plus"></i> Nuevo Permiso
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="tablaPermisos" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Módulo</th>
                                            <th>Descripción</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Datos cargados vía AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>

</div>

<!-- MODAL: Asignar Roles a Usuario -->
<div class="modal fade" id="modalAsignarRoles" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Asignar Roles</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="usuarioIdRoles">
                <h6>Usuario: <strong id="usuarioNombreRoles"></strong></h6>
                <hr>
                <div id="listaRolesAsignar">
                    <!-- Checkboxes de roles -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarRolesUsuario()">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Asignar Permisos Específicos a Usuario -->
<div class="modal fade" id="modalAsignarPermisos" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Permisos Específicos del Usuario</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="usuarioIdPermisos">
                <h6>Usuario: <strong id="usuarioNombrePermisos"></strong></h6>
                <hr>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    Los permisos específicos sobrescriben los permisos heredados de los roles.
                    Use con precaución.
                </div>
                
                <div id="listaPermisosAsignar">
                    <!-- Lista de permisos con opciones conceder/revocar -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarPermisosUsuario()">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Crear/Editar Rol -->
<div class="modal fade" id="modalRol" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tituloModalRol">Nuevo Rol</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="formRol">
                <div class="modal-body">
                    <input type="hidden" id="rolId" name="rol_id">
                    
                    <div class="form-group">
                        <label>Código *</label>
                        <input type="text" class="form-control" id="rolCodigo" name="rol_codigo" required>
                        <small class="form-text text-muted">Único, sin espacios (ej: SUPERVISOR)</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Nombre *</label>
                        <input type="text" class="form-control" id="rolNombre" name="rol_nombre" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea class="form-control" id="rolDescripcion" name="rol_descripcion" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Nivel Jerárquico</label>
                        <input type="number" class="form-control" id="rolNivel" name="rol_nivel" value="50">
                        <small class="form-text text-muted">0 = mayor jerarquía, 100 = menor</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: Crear/Editar Permiso -->
<div class="modal fade" id="modalPermiso" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tituloModalPermiso">Nuevo Permiso</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="formPermiso">
                <div class="modal-body">
                    <input type="hidden" id="permisoId" name="permiso_id">
                    
                    <div class="form-group">
                        <label>Código *</label>
                        <input type="text" class="form-control" id="permisoCodigo" name="permiso_codigo" required>
                        <small class="form-text text-muted">Único, snake_case (ej: admin_usuarios)</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Nombre *</label>
                        <input type="text" class="form-control" id="permisoNombre" name="permiso_nombre" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Módulo</label>
                        <input type="text" class="form-control" id="permisoModulo" name="permiso_modulo">
                        <small class="form-text text-muted">Módulo al que pertenece (opcional)</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea class="form-control" id="permisoDescripcion" name="permiso_descripcion" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="assets/js/gestion_permisos.js"></script>

</body>
</html>