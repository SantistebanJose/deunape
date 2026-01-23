<?php

/**
 * admin_roles_permisos.php
 * Panel de administración de roles y permisos
 */

include("cabecera.php");
require_once("logica/bd.php");
require_once("MenuManager.php");

// Verificar acceso
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$menuManager = new MenuManager($_SESSION['nombre_rol'] ?? $_SESSION['rol']);

// Verificar permiso de administración
if (!$menuManager->hasPermission('admin_roles')) {
    $_SESSION['error'] = "No tienes permisos para acceder a esta página";
    header("Location: index.php");
    exit();
}


// Obtener todos los roles
$roles = executeQuerymenumanager("SELECT * FROM roles WHERE activo = true ORDER BY nombre");

// Obtener todos los permisos agrupados por categoría
$permisos = executeQuerymenumanager("
    SELECT * FROM permisos 
    WHERE activo = true 
    ORDER BY categoria, nombre
");

// Agrupar permisos por categoría
$permisosPorCategoria = [];
foreach ($permisos as $permiso) {
    $categoria = $permiso['categoria'];
    if (!isset($permisosPorCategoria[$categoria])) {
        $permisosPorCategoria[$categoria] = [];
    }
    $permisosPorCategoria[$categoria][] = $permiso;
}


?>

<style>
    .permission-card {
        border: 1px solid #e0e6ed;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .permission-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 12px 20px;
        border-radius: 6px;
        margin-bottom: 15px;
        font-weight: 600;
        font-size: 1.1em;
    }

    .permission-item {
        padding: 10px 15px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: background 0.2s;
    }

    .permission-item:hover {
        background: #f8f9fa;
    }

    .permission-item:last-child {
        border-bottom: none;
    }

    .permission-info {
        flex: 1;
    }

    .permission-code {
        font-family: monospace;
        background: #e8ecef;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.85em;
        margin-right: 10px;
        color: #495057;
    }

    .role-tab {
        cursor: pointer;
        padding: 12px 20px;
        border: 2px solid #e0e6ed;
        border-radius: 8px 8px 0 0;
        margin-right: 5px;
        background: white;
        transition: all 0.3s;
        color: #495057;
    }

    .role-tab:hover {
        background: #f8f9fa;
        border-color: #5a6c7d;
    }

    .role-tab.active {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        border-bottom: 2px solid transparent;
    }

    .role-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75em;
        font-weight: 600;
        margin-left: 8px;
        background: #5a6c7d;
        color: white;
    }

    .role-tab.active .role-badge {
        background: rgba(255,255,255,0.3);
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 24px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #2c3e50;
    }

    input:checked+.slider:before {
        transform: translateX(26px);
    }

    .loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }

    .loading-spinner {
        color: white;
        font-size: 2em;
    }

    .stats-card {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .stats-card h2 {
        font-size: 2.5em;
        margin: 0;
        font-weight: 700;
    }

    .stats-card p {
        margin: 5px 0 0 0;
        opacity: 0.9;
        font-size: 0.95em;
    }

    .category-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.8em;
        font-weight: 600;
        text-transform: uppercase;
    }

    .category-admin {
        background: #c0392b;
        color: white;
    }

    .category-negocio {
        background: #2980b9;
        color: white;
    }

    .category-sunat {
        background: #27ae60;
        color: white;
    }

    .category-datos {
        background: #d68910;
        color: white;
    }

    .category-credito {
        background: #8e44ad;
        color: white;
    }

    .category-reserva {
        background: #16a085;
        color: white;
    }

    .category-venta {
        background: #d35400;
        color: white;
    }

    .category-sistema {
        background: #34495e;
        color: white;
    }

    .btn-success {
        background: #27ae60;
        border-color: #27ae60;
    }

    .btn-success:hover {
        background: #229954;
        border-color: #229954;
    }

    .btn-danger {
        background: #c0392b;
        border-color: #c0392b;
    }

    .btn-danger:hover {
        background: #a93226;
        border-color: #a93226;
    }

    .btn-primary {
        background: #2c3e50;
        border-color: #2c3e50;
    }

    .btn-primary:hover {
        background: #1a252f;
        border-color: #1a252f;
    }
</style>

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">
                <i class="fas fa-shield-alt"></i> Administración de Roles y Permisos
            </h4>
            <ul class="breadcrumbs">
                <li class="nav-home"><a href="index.php"><i class="flaticon-home"></i></a></li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item">Administración</li>
                <li class="separator"><i class="flaticon-right-arrow"></i></li>
                <li class="nav-item active">Roles y Permisos</li>
            </ul>
        </div>

        <!-- Loading Overlay -->
        <div class="loading-overlay" id="loadingOverlay">
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row">
            <div class="col-md-3">
                <div class="stats-card">
                    <h2><?= count($roles) ?></h2>
                    <p>Roles Activos</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #2980b9 0%, #3498db 100%);">
                    <h2><?= count($permisos) ?></h2>
                    <p>Permisos Totales</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #16a085 0%, #1abc9c 100%);">
                    <h2><?= count($permisosPorCategoria) ?></h2>
                    <p>Categorías</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);">
                    <h2 id="totalAsignados">0</h2>
                    <p>Permisos Asignados</p>
                </div>
            </div>
        </div>

        <!-- Tabs de Roles -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex" style="border-bottom: 2px solid #e0e6ed; margin-bottom: 20px;">
                    <?php foreach ($roles as $index => $rol): ?>
                        <div class="role-tab <?= $index === 0 ? 'active' : '' ?>"
                            onclick="selectRole(<?= $rol['id_rol'] ?>, '<?= htmlspecialchars($rol['nombre']) ?>')">
                            <?= htmlspecialchars($rol['nombre']) ?>
                            <span class="role-badge" id="badge-<?= $rol['id_rol'] ?>">0</span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <input type="hidden" id="currentRoleId" value="<?= $roles[0]['id_rol'] ?? '' ?>">
                <input type="hidden" id="currentRoleName" value="<?= $roles[0]['nombre'] ?? '' ?>">

                <!-- Acciones Masivas -->
                <div class="mb-3">
                    <button class="btn btn-success btn-sm" onclick="asignarTodos()">
                        <i class="fas fa-check-double"></i> Asignar Todos
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="quitarTodos()">
                        <i class="fas fa-times"></i> Quitar Todos
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="cargarPermisos()">
                        <i class="fas fa-sync"></i> Recargar
                    </button>
                </div>

                <!-- Lista de Permisos por Categoría -->
                <div id="permisosContainer">
                    <?php foreach ($permisosPorCategoria as $categoria => $permisosCategoria): ?>
                        <div class="permission-card">
                            <div class="permission-header">
                                <span class="category-badge category-<?= $categoria ?>">
                                    <?= ucfirst($categoria) ?>
                                </span>
                                <span style="float: right;">
                                    <?= count($permisosCategoria) ?> permisos
                                </span>
                            </div>

                            <?php foreach ($permisosCategoria as $permiso): ?>
                                <div class="permission-item">
                                    <div class="permission-info">
                                        <span class="permission-code">
                                            <?= htmlspecialchars($permiso['codigo']) ?>
                                        </span>
                                        <strong><?= htmlspecialchars($permiso['nombre']) ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($permiso['descripcion']) ?>
                                        </small>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox"
                                            class="permission-switch"
                                            data-permiso-id="<?= $permiso['id'] ?>"
                                            onchange="togglePermiso(<?= $permiso['id'] ?>, this.checked)">
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

</div>




<script>
    // Al cargar la página
    $(document).ready(function() {
        cargarPermisos();
    });

    function selectRole(rolId, rolNombre) {
        // Actualizar tabs
        $('.role-tab').removeClass('active');
        event.target.classList.add('active');

        // Actualizar valores ocultos
        $('#currentRoleId').val(rolId);
        $('#currentRoleName').val(rolNombre);

        // Cargar permisos del rol
        cargarPermisos();
    }

    function cargarPermisos() {
        const rolId = $('#currentRoleId').val();

        $('#loadingOverlay').css('display', 'flex');

        $.ajax({
            url: 'roles_api.php',
            method: 'POST',
            data: {
                action: 'getPermisosRol',
                rol_id: rolId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Resetear todos los switches
                    $('.permission-switch').prop('checked', false);

                    // Activar permisos asignados
                    response.permisos.forEach(function(permisoId) {
                        $(`.permission-switch[data-permiso-id="${permisoId}"]`).prop('checked', true);
                    });

                    // Actualizar badge
                    $('#badge-' + rolId).text(response.permisos.length);
                    $('#totalAsignados').text(response.permisos.length);
                }
            },
            complete: function() {
                $('#loadingOverlay').hide();
            }
        });
    }

    function togglePermiso(permisoId, asignar) {
        const rolId = $('#currentRoleId').val();
        const rolNombre = $('#currentRoleName').val();

        $.ajax({
            url: 'roles_api.php',
            method: 'POST',
            data: {
                action: asignar ? 'asignarPermiso' : 'quitarPermiso',
                rol_id: rolId,
                permiso_id: permisoId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Actualizar contador
                    cargarPermisos();

                    // Mostrar notificación
                    $.notify({
                        icon: asignar ? 'fas fa-check' : 'fas fa-times',
                        message: response.message
                    }, {
                        type: asignar ? 'success' : 'warning',
                        placement: {
                            from: 'top',
                            align: 'right'
                        },
                        timer: 2000
                    });
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error al actualizar permiso');
            }
        });
    }

    function asignarTodos() {
        if (!confirm('¿Asignar TODOS los permisos a este rol?')) return;

        const rolId = $('#currentRoleId').val();
        $('#loadingOverlay').css('display', 'flex');

        $.ajax({
            url: 'roles_api.php',
            method: 'POST',
            data: {
                action: 'asignarTodos',
                rol_id: rolId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    cargarPermisos();
                    $.notify({
                        icon: 'fas fa-check',
                        message: response.message
                    }, {
                        type: 'success',
                        placement: {
                            from: 'top',
                            align: 'right'
                        }
                    });
                }
            },
            complete: function() {
                $('#loadingOverlay').hide();
            }
        });
    }

    function quitarTodos() {
        if (!confirm('¿Quitar TODOS los permisos de este rol?')) return;

        const rolId = $('#currentRoleId').val();
        $('#loadingOverlay').css('display', 'flex');

        $.ajax({
            url: 'roles_api.php',
            method: 'POST',
            data: {
                action: 'quitarTodos',
                rol_id: rolId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    cargarPermisos();
                    $.notify({
                        icon: 'fas fa-times',
                        message: response.message
                    }, {
                        type: 'warning',
                        placement: {
                            from: 'top',
                            align: 'right'
                        }
                    });
                }
            },
            complete: function() {
                $('#loadingOverlay').hide();
            }
        });
    }
</script>

<?php include("pie.php"); ?>