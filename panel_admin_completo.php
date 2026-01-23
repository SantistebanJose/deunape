<?php

/**
 * panel_admin_completo.php
 * Panel Administrativo COMPLETO - TODO-EN-UNO
 * Gestiona: Roles, Permisos, Asignación, Usuarios, Menús, Items
 */

include("cabecera.php");
require_once("logica/bd.php");
require_once("MenuManager.php");

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$menuManager = new MenuManager($_SESSION['nombre_rol'] ?? $_SESSION['rol']);

if (!$menuManager->hasPermission('admin_roles')) {
    header("Location: index.php");
    exit();
}

// Obtener datos
$sucursal_id = $_SESSION["sucursal_id"];
$roles = executeQuerymenumanager(query: "SELECT * FROM roles WHERE sucursal_id = :s ORDER BY nombre", params: ["s" => $sucursal_id]);
$permisos = executeQuerymenumanager("SELECT * FROM permisos ORDER BY categoria, codigo");
$menus = executeQuerymenumanager("SELECT * FROM menus ORDER BY orden");
$usuarios = executeQuerymenumanager(
    query: "
    SELECT u.id, u.username, u.id_rol, p.nombres, p.apellidos, p.email, r.nombre as nombre_rol
    FROM usuario u
    LEFT JOIN persona p ON u.persona_id = p.id
    LEFT JOIN roles r ON u.id_rol = r.id_rol
    WHERE u.sucursal_id = :sucursal_id
    ORDER BY u.username
",
    params: ["sucursal_id" => $_SESSION["sucursal_id"]]
);

// Agrupar permisos por categoría
$permisosPorCategoria = [];
foreach ($permisos as $permiso) {
    $cat = $permiso['categoria'];
    if (!isset($permisosPorCategoria[$cat])) {
        $permisosPorCategoria[$cat] = [];
    }
    $permisosPorCategoria[$cat][] = $permiso;
}
?>

<style>
    .admin-header {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }

    .stat-box {
        background: white;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid #2c3e50;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .stat-box h2 {
        margin: 0;
        font-size: 2.5em;
        color: #2c3e50;
    }

    .tabs {
        display: flex;
        border-bottom: 2px solid #e0e0e0;
        margin-bottom: 20px;
        overflow-x: auto;
    }

    .tab {
        padding: 15px 25px;
        cursor: pointer;
        background: white;
        border: none;
        font-weight: 600;
        color: #7f8c8d;
        transition: all 0.3s;
        border-bottom: 3px solid transparent;
        white-space: nowrap;
    }

    .tab:hover {
        background: #f8f9fa;
    }

    .tab.active {
        color: #2c3e50;
        border-bottom-color: #2c3e50;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .card {
        background: white;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .card h3 {
        margin: 0 0 20px 0;
        color: #2c3e50;
        padding-bottom: 10px;
        border-bottom: 2px solid #e0e0e0;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: #2c3e50;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-primary {
        background: #2c3e50;
        color: white;
    }

    .btn-success {
        background: #27ae60;
        color: white;
    }

    .btn-danger {
        background: #e74c3c;
        color: white;
    }

    .btn-warning {
        background: #f39c12;
        color: white;
    }

    .btn-sm {
        padding: 5px 10px;
        font-size: 0.9em;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table th {
        background: #2c3e50;
        color: white;
        padding: 12px;
        text-align: left;
    }

    table td {
        padding: 10px 12px;
        border-bottom: 1px solid #e0e0e0;
    }

    table tr:hover {
        background: #f8f9fa;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.85em;
        font-weight: 600;
    }

    .badge-success {
        background: #27ae60;
        color: white;
    }

    .badge-danger {
        background: #e74c3c;
        color: white;
    }

    .badge-info {
        background: #3498db;
        color: white;
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

    .permission-card {
        border: 1px solid #e0e6ed;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        background: white;
    }

    .permission-header {
        background: #2c3e50;
        color: white;
        padding: 12px 20px;
        border-radius: 6px;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .permission-item {
        padding: 10px 15px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .permission-item:hover {
        background: #f8f9fa;
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
    }

    .role-tab {
        cursor: pointer;
        padding: 12px 20px;
        border: 2px solid #e0e6ed;
        border-radius: 8px 8px 0 0;
        margin-right: 5px;
        background: white;
        transition: all 0.3s;
        display: inline-block;
    }

    .role-tab:hover {
        background: #f8f9fa;
    }

    .role-tab.active {
        background: #2c3e50;
        color: white;
    }

    .role-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75em;
        font-weight: 600;
        margin-left: 8px;
        background: #95a5a6;
        color: white;
    }

    .role-tab.active .role-badge {
        background: rgba(255, 255, 255, 0.3);
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

    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background: white;
        margin: 100px auto;
        padding: 30px;
        border-radius: 8px;
        max-width: 500px;
    }

    .close {
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
</style>

<div class="container">
    <div class="page-inner">

        <!-- Loading -->
        <div class="loading-overlay" id="loadingOverlay">
            <div style="color: white; font-size: 2em;">
                <i class="fas fa-spinner fa-spin"></i>
            </div>
        </div>

        <!-- Header -->
        <div class="admin-header">
            <h1><i class="fas fa-crown"></i> Panel Administrativo Completo</h1>
            <p>Gestiona TODO el sistema desde aquí</p>
        </div>

        <!-- Estadísticas -->
        <div class="stats-row">
            <div class="stat-box">
                <h2><?= count($roles) ?></h2>
                <p><i class="fas fa-users-cog"></i> Roles</p>
            </div>
            <div class="stat-box">
                <h2><?= count($permisos) ?></h2>
                <p><i class="fas fa-key"></i> Permisos</p>
            </div>
            <div class="stat-box">
                <h2><?= count($usuarios) ?></h2>
                <p><i class="fas fa-user"></i> Usuarios</p>
            </div>
            <div class="stat-box">
                <h2><?= count($menus) ?></h2>
                <p><i class="fas fa-bars"></i> Menús</p>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab active" onclick="switchTab('roles')">
                <i class="fas fa-users-cog"></i> Roles
            </button>
            <button class="tab" onclick="switchTab('permisos')">
                <i class="fas fa-key"></i> Permisos
            </button>
            <button class="tab" onclick="switchTab('asignar-permisos')">
                <i class="fas fa-link"></i> Asignar Permisos
            </button>
            <button class="tab" onclick="switchTab('usuarios')">
                <i class="fas fa-user"></i> Usuarios
            </button>

            <?php
            if ($_SESSION["usuario"] === 'ADMINADMINSUPERSUCURSAL' && $_SESSION["id"] = 22) {
            ?>
                <button class="tab" onclick="switchTab('menus')">
                    <i class="fas fa-bars"></i> Menús
                </button>
                <button class="tab" onclick="switchTab('items')">
                    <i class="fas fa-list"></i> Items
                </button>
            <?php
            }
            ?>


        </div>

        <!-- TAB 1: ROLES -->
        <div id="tab-roles" class="tab-content active">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <h3>Crear Rol</h3>
                        <form id="formRol" onsubmit="guardarRol(event)">
                            <div class="form-group">
                                <label>Nombre *</label>
                                <input type="text" name="nombre" required>
                            </div>
                            <div class="form-group">
                                <label>Descripción *</label>
                                <textarea name="descripcion" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <h3>Roles (<?= count($roles) ?>)</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Permisos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($roles as $rol): ?>
                                    <?php
                                    $countP = executeQuerymenumanager("SELECT COUNT(*) as total FROM rol_permiso WHERE rol_id = :id", ['id' => $rol['id_rol']]);
                                    ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($rol['nombre']) ?></strong></td>
                                        <td><?= htmlspecialchars($rol['descripcion']) ?></td>
                                        <td><span class="badge badge-info"><?= $countP[0]['total'] ?></span></td>
                                        <td>
                                            <button class="btn btn-danger btn-sm" onclick="eliminarRol(<?= $rol['id_rol'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: PERMISOS -->
        <div id="tab-permisos" class="tab-content">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <h3>Crear Permiso</h3>
                        <form id="formPermiso" onsubmit="guardarPermiso(event)">
                            <div class="form-group">
                                <label>Código *</label>
                                <input type="text" name="codigo" required placeholder="modulo.accion">
                            </div>
                            <div class="form-group">
                                <label>Nombre *</label>
                                <input type="text" name="nombre" required>
                            </div>
                            <div class="form-group">
                                <label>Descripción *</label>
                                <textarea name="descripcion" rows="2" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Categoría *</label>
                                <input type="text" name="categoria" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <h3>Permisos (<?= count($permisos) ?>)</h3>
                        <input type="text" id="buscarPermiso" onkeyup="filtrarPermisos()" placeholder="Buscar..." style="width:100%; padding:10px; margin-bottom:15px;">
                        <div style="max-height: 500px; overflow-y: auto;">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Categoría</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaPermisos">
                                    <?php foreach ($permisos as $p): ?>
                                        <tr class="permiso-row">
                                            <td><code><?= htmlspecialchars($p['codigo']) ?></code></td>
                                            <td><?= htmlspecialchars($p['nombre']) ?></td>
                                            <td><span class="badge badge-info"><?= $p['categoria'] ?></span></td>
                                            <td>
                                                <button class="btn btn-danger btn-sm" onclick="eliminarPermiso(<?= $p['id'] ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: ASIGNAR PERMISOS -->
        <div id="tab-asignar-permisos" class="tab-content">
            <div class="card">
                <h3>Asignar Permisos a Roles</h3>
                <div style="border-bottom: 2px solid #e0e6ed; margin-bottom: 20px;">
                    <?php foreach ($roles as $index => $rol): ?>
                        <div class="role-tab <?= $index === 0 ? 'active' : '' ?>" onclick="selectRole(<?= $rol['id_rol'] ?>)">
                            <?= htmlspecialchars($rol['nombre']) ?>
                            <span class="role-badge" id="badge-<?= $rol['id_rol'] ?>">0</span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" id="currentRoleId" value="<?= $roles[0]['id_rol'] ?? '' ?>">
                <div class="mb-3">
                    <button class="btn btn-success btn-sm" onclick="asignarTodos()">Asignar Todos</button>
                    <button class="btn btn-danger btn-sm" onclick="quitarTodos()">Quitar Todos</button>
                    <button class="btn btn-primary btn-sm" onclick="cargarPermisos()">Recargar</button>
                    <span id="totalAsignados" style="margin-left: 15px; font-weight: bold;">0 permisos</span>
                </div>
                <div id="permisosContainer">
                    <?php foreach ($permisosPorCategoria as $categoria => $permisosCategoria): ?>
                        <div class="permission-card">
                            <div class="permission-header">
                                <?= ucfirst($categoria) ?> (<?= count($permisosCategoria) ?>)
                            </div>
                            <?php foreach ($permisosCategoria as $permiso): ?>
                                <div class="permission-item">
                                    <div class="permission-info">
                                        <span class="permission-code"><?= htmlspecialchars($permiso['codigo']) ?></span>
                                        <strong><?= htmlspecialchars($permiso['nombre']) ?></strong>
                                        <br><small><?= htmlspecialchars($permiso['descripcion']) ?></small>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" class="permission-switch" data-permiso-id="<?= $permiso['id'] ?>" onchange="togglePermiso(<?= $permiso['id'] ?>, this.checked)">
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- TAB 4: USUARIOS -->
        <div id="tab-usuarios" class="tab-content">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <h3>Crear Usuario</h3>
                        <form id="formUsuario" onsubmit="guardarUsuario(event)">
                            <div class="form-group">
                                <label>Nombres *</label>
                                <input type="text" name="nombres" required>
                            </div>
                            <div class="form-group">
                                <label>Apellidos *</label>
                                <input type="text" name="apellidos" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email">
                            </div>
                            <div class="form-group">
                                <label>Username *</label>
                                <input type="text" name="username" required>
                            </div>
                            <div class="form-group">
                                <label>Password *</label>
                                <input type="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label>Rol *</label>
                                <select name="id_rol" required>
                                    <option value="">Selecciona rol</option>
                                    <?php foreach ($roles as $rol): ?>
                                        <option value="<?= $rol['id_rol'] ?>"><?= htmlspecialchars($rol['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <h3>Usuarios (<?= count($usuarios) ?>)</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $user): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($user['username']) ?></strong></td>
                                        <td><?= htmlspecialchars($user['nombres'] . ' ' . $user['apellidos']) ?></td>
                                        <td><?= htmlspecialchars($user['email'] ?? '-') ?></td>
                                        <td><span class="badge badge-info"><?= htmlspecialchars($user['nombre_rol'] ?? 'Sin rol') ?></span></td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" onclick="cambiarRol(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username'], ENT_QUOTES) ?>')">
                                                <i class="fas fa-user-tag"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="eliminarUsuario(<?= $user['id'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <?php
        if ($_SESSION["usuario"] === 'ADMINADMINSUPERSUCURSAL' && $_SESSION["id"] = 22) {
        ?>
            <!-- TAB 5: MENÚS -->
            <div id="tab-menus" class="tab-content">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <h3>Crear Menú</h3>
                            <form id="formMenu" onsubmit="guardarMenu(event)">
                                <div class="form-group">
                                    <label>Código *</label>
                                    <input type="text" name="codigo" required>
                                </div>
                                <div class="form-group">
                                    <label>Título *</label>
                                    <input type="text" name="titulo" required>
                                </div>
                                <div class="form-group">
                                    <label>Icono *</label>
                                    <input type="text" name="icono" required placeholder="fas fa-box">
                                </div>
                                <div class="form-group">
                                    <label>Orden *</label>
                                    <input type="number" name="orden" required value="10">
                                </div>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <h3>Menús (<?= count($menus) ?>)</h3>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Título</th>
                                        <th>Icono</th>
                                        <th>Items</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($menus as $menu): ?>
                                        <?php
                                        $countI = executeQuerymenumanager("SELECT COUNT(*) as total FROM menu_items WHERE menu_id = :id", ['id' => $menu['id']]);
                                        ?>
                                        <tr>
                                            <td><code><?= htmlspecialchars($menu['codigo']) ?></code></td>
                                            <td><?= htmlspecialchars($menu['titulo']) ?></td>
                                            <td><i class="<?= htmlspecialchars($menu['icono']) ?>"></i></td>
                                            <td><span class="badge badge-info"><?= $countI[0]['total'] ?></span></td>
                                            <td>
                                                <button class="btn btn-danger btn-sm" onclick="eliminarMenu(<?= $menu['id'] ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- TAB 6: ITEMS -->
            <div id="tab-items" class="tab-content">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <h3 id="tituloFormItem">Crear Item</h3>
                            <form id="formItem" onsubmit="guardarItem(event)">
                                <input type="hidden" name="item_id" id="item_id" value="">
                                <div class="form-group">
                                    <label>Menú *</label>
                                    <select name="menu_id" id="select_menu_id" required onchange="cargarItems(this.value)">
                                        <option value="">Selecciona</option>
                                        <?php foreach ($menus as $menu): ?>
                                            <option value="<?= $menu['id'] ?>"><?= htmlspecialchars($menu['titulo']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Título *</label>
                                    <input type="text" name="titulo" id="item_titulo" required>
                                </div>
                                <div class="form-group">
                                    <label>Link *</label>
                                    <input type="text" name="link" id="item_link" required placeholder="archivo.php">
                                </div>
                                <div class="form-group">
                                    <label>Permiso</label>
                                    <select name="permiso_id" id="item_permiso_id">
                                        <option value="">Sin permiso</option>
                                        <?php foreach ($permisos as $p): ?>
                                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['codigo']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Orden *</label>
                                    <input type="number" name="orden" id="item_orden" required value="1">
                                </div>
                                <button type="submit" class="btn btn-primary" id="btnGuardarItem">Guardar</button>
                                <button type="button" class="btn btn-danger" id="btnCancelarItem" onclick="cancelarEdicionItem()" style="display:none;">Cancelar</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <h3>Items de Menú <span id="menuSeleccionadoNombre"></span></h3>
                            <p id="mensajeSeleccionarMenu">Selecciona un menú para ver sus items</p>
                            <div id="tablaItemsContainer" style="display:none;">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Orden</th>
                                            <th>Título</th>
                                            <th>Link</th>
                                            <th>Permiso</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaItems">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php

        }
        ?>





    </div>
</div>

<!-- Modal Cambiar Rol -->
<div class="modal" id="modalCambiarRol">
    <div class="modal-content">
        <span class="close" onclick="cerrarModal()">&times;</span>
        <h3>Cambiar Rol</h3>
        <form id="formCambiarRol" onsubmit="guardarCambioRol(event)">
            <input type="hidden" id="usuario_id" name="usuario_id">
            <div class="form-group">
                <label>Usuario: <strong id="usuario_nombre"></strong></label>
            </div>
            <div class="form-group">
                <label>Nuevo Rol *</label>
                <select name="nuevo_rol_id" required>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= $rol['id_rol'] ?>"><?= htmlspecialchars($rol['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
            <button type="button" class="btn btn-danger" onclick="cerrarModal()">Cancelar</button>
        </form>
    </div>
</div>

<script>
    // NAVEGACIÓN
    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + tabName).classList.add('active');
        event.target.classList.add('active');
        if (tabName === 'asignar-permisos') cargarPermisos();
    }

    // ROLES
    function guardarRol(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('action', 'crearRol');
        $.ajax({
            url: 'panel_admin_api.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $.notify({
                        icon: 'fas fa-check',
                        message: 'Rol creado'
                    }, {
                        type: 'success'
                    });
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    }

    function eliminarRol(id) {
        if (!confirm('¿Eliminar?')) return;
        $.post('panel_admin_api.php', {
            action: 'eliminarRol',
            id: id
        }, function(response) {
            if (response.success) {
                location.reload();
            }
        }, 'json');
    }

    // PERMISOS
    function guardarPermiso(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('action', 'crearPermiso');
        $.ajax({
            url: 'panel_admin_api.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $.notify({
                        icon: 'fas fa-check',
                        message: 'Permiso creado'
                    }, {
                        type: 'success'
                    });
                    setTimeout(() => location.reload(), 1000);
                }
            }
        });
    }

    function eliminarPermiso(id) {
        if (!confirm('¿Eliminar?')) return;
        $.post('panel_admin_api.php', {
            action: 'eliminarPermiso',
            id: id
        }, function(response) {
            if (response.success) location.reload();
        }, 'json');
    }

    function filtrarPermisos() {
        const filtro = document.getElementById('buscarPermiso').value.toLowerCase();
        document.querySelectorAll('.permiso-row').forEach(fila => {
            fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
        });
    }

    // ASIGNAR PERMISOS
    function selectRole(rolId) {
        document.querySelectorAll('.role-tab').forEach(tab => tab.classList.remove('active'));
        event.target.classList.add('active');
        document.getElementById('currentRoleId').value = rolId;
        cargarPermisos();
    }

    function cargarPermisos() {
        const rolId = document.getElementById('currentRoleId').value;
        if (!rolId) return;
        document.getElementById('loadingOverlay').style.display = 'flex';
        $.ajax({
            url: 'panel_admin_api.php',
            method: 'POST',
            data: {
                action: 'getPermisosRol',
                rol_id: rolId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    document.querySelectorAll('.permission-switch').forEach(sw => sw.checked = false);
                    response.permisos.forEach(permisoId => {
                        const sw = document.querySelector(`.permission-switch[data-permiso-id="${permisoId}"]`);
                        if (sw) sw.checked = true;
                    });
                    const badge = document.getElementById('badge-' + rolId);
                    if (badge) badge.textContent = response.permisos.length;
                    document.getElementById('totalAsignados').textContent = response.permisos.length + ' permisos';
                }
            },
            complete: function() {
                document.getElementById('loadingOverlay').style.display = 'none';
            }
        });
    }

    function togglePermiso(permisoId, asignar) {
        const rolId = document.getElementById('currentRoleId').value;
        $.ajax({
            url: 'panel_admin_api.php',
            method: 'POST',
            data: {
                action: asignar ? 'asignarPermiso' : 'quitarPermiso',
                rol_id: rolId,
                permiso_id: permisoId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    cargarPermisos();
                    $.notify({
                        icon: asignar ? 'fas fa-check' : 'fas fa-times',
                        message: response.message
                    }, {
                        type: asignar ? 'success' : 'warning',
                        timer: 1500
                    });
                }
            }
        });
    }

    function asignarTodos() {
        if (!confirm('¿Asignar TODOS?')) return;
        const rolId = document.getElementById('currentRoleId').value;
        $.post('panel_admin_api.php', {
            action: 'asignarTodosPermisos',
            rol_id: rolId
        }, function(response) {
            if (response.success) cargarPermisos();
        }, 'json');
    }

    function quitarTodos() {
        if (!confirm('¿Quitar TODOS?')) return;
        const rolId = document.getElementById('currentRoleId').value;
        $.post('panel_admin_api.php', {
            action: 'quitarTodosPermisos',
            rol_id: rolId
        }, function(response) {
            if (response.success) cargarPermisos();
        }, 'json');
    }

    // USUARIOS
    function guardarUsuario(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('action', 'crearUsuario');
        $.ajax({
            url: 'panel_admin_api.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $.notify({
                        icon: 'fas fa-check',
                        message: 'Usuario creado'
                    }, {
                        type: 'success'
                    });
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    }

    function cambiarRol(userId, username) {
        document.getElementById('usuario_id').value = userId;
        document.getElementById('usuario_nombre').textContent = username;
        document.getElementById('modalCambiarRol').style.display = 'block';
    }

    function guardarCambioRol(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('action', 'cambiarRolUsuario');
        $.ajax({
            url: 'panel_admin_api.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $.notify({
                        icon: 'fas fa-check',
                        message: 'Rol cambiado'
                    }, {
                        type: 'success'
                    });
                    cerrarModal();
                    setTimeout(() => location.reload(), 1000);
                }
            }
        });
    }

    function eliminarUsuario(id) {
        if (!confirm('¿Eliminar?')) return;
        $.post('panel_admin_api.php', {
            action: 'eliminarUsuario',
            id: id
        }, function(response) {
            if (response.success) location.reload();
        }, 'json');
    }

    function cerrarModal() {
        document.getElementById('modalCambiarRol').style.display = 'none';
    }

    // MENÚS
    function guardarMenu(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('action', 'crearMenu');
        $.ajax({
            url: 'panel_admin_api.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $.notify({
                        icon: 'fas fa-check',
                        message: 'Menú creado'
                    }, {
                        type: 'success'
                    });
                    setTimeout(() => location.reload(), 1000);
                }
            }
        });
    }

    function eliminarMenu(id) {
        if (!confirm('¿Eliminar?')) return;
        $.post('panel_admin_api.php', {
            action: 'eliminarMenu',
            id: id
        }, function(response) {
            if (response.success) location.reload();
        }, 'json');
    }

    // ITEMS
    function guardarItem(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        formData.append('action', 'crearItem');
        $.ajax({
            url: 'panel_admin_api.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $.notify({
                        icon: 'fas fa-check',
                        message: 'Item creado'
                    }, {
                        type: 'success'
                    });
                    setTimeout(() => location.reload(), 1000);
                }
            }
        });
    }

    // INIT
    document.addEventListener('DOMContentLoaded', function() {
        const currentRoleId = document.getElementById('currentRoleId');
        if (currentRoleId && currentRoleId.value) {
            cargarPermisos();
        }
    });

    window.onclick = function(event) {
        const modal = document.getElementById('modalCambiarRol');
        if (event.target == modal) {
            cerrarModal();
        }
    }

    // ITEMS - FUNCIONES MEJORADAS
    let menuActualSeleccionado = null;

    function cargarItems(menuId) {
        if (!menuId) {
            document.getElementById('mensajeSeleccionarMenu').style.display = 'block';
            document.getElementById('tablaItemsContainer').style.display = 'none';
            return;
        }

        menuActualSeleccionado = menuId;

        // Obtener nombre del menú
        const selectMenu = document.getElementById('select_menu_id');
        const menuNombre = selectMenu.options[selectMenu.selectedIndex].text;
        document.getElementById('menuSeleccionadoNombre').textContent = '(' + menuNombre + ')';

        document.getElementById('loadingOverlay').style.display = 'flex';

        $.ajax({
            url: 'panel_admin_api.php',
            method: 'POST',
            data: {
                action: 'getItems',
                menu_id: menuId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    mostrarItems(response.items);
                    document.getElementById('mensajeSeleccionarMenu').style.display = 'none';
                    document.getElementById('tablaItemsContainer').style.display = 'block';
                }
            },
            complete: function() {
                document.getElementById('loadingOverlay').style.display = 'none';
            }
        });
    }

    function mostrarItems(items) {
        const tbody = document.getElementById('tablaItems');
        tbody.innerHTML = '';

        if (items.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No hay items para este menú</td></tr>';
            return;
        }

        items.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
            <td><span class="badge badge-info">${item.orden}</span></td>
            <td><strong>${escapeHtml(item.titulo)}</strong></td>
            <td><code>${escapeHtml(item.link)}</code></td>
            <td>${item.permiso_codigo ? '<span class="badge badge-success">' + escapeHtml(item.permiso_codigo) + '</span>' : '<span class="badge badge-secondary">Sin permiso</span>'}</td>
            <td>
                <button class="btn btn-warning btn-sm" onclick="editarItem(${item.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm" onclick="eliminarItem(${item.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
            tbody.appendChild(tr);
        });
    }

    function guardarItem(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const itemId = document.getElementById('item_id').value;

        formData.append('action', itemId ? 'editarItem' : 'crearItem');
        if (itemId) {
            formData.append('id', itemId);
        }

        $.ajax({
            url: 'panel_admin_api.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $.notify({
                        icon: 'fas fa-check',
                        message: itemId ? 'Item actualizado' : 'Item creado'
                    }, {
                        type: 'success'
                    });

                    // Limpiar formulario
                    cancelarEdicionItem();

                    // Recargar items si hay un menú seleccionado
                    const menuId = document.getElementById('select_menu_id').value;
                    if (menuId) {
                        cargarItems(menuId);
                    }
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    }

    function editarItem(id) {
        // Buscar el item en la tabla actual
        $.ajax({
            url: 'panel_admin_api.php',
            method: 'POST',
            data: {
                action: 'getItems',
                menu_id: menuActualSeleccionado
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const item = response.items.find(i => i.id == id);
                    if (item) {
                        // Llenar formulario
                        document.getElementById('item_id').value = item.id;
                        document.getElementById('select_menu_id').value = item.menu_id;
                        document.getElementById('item_titulo').value = item.titulo;
                        document.getElementById('item_link').value = item.link;
                        document.getElementById('item_permiso_id').value = item.permiso_id || '';
                        document.getElementById('item_orden').value = item.orden;

                        // Cambiar título y botones
                        document.getElementById('tituloFormItem').textContent = 'Editar Item';
                        document.getElementById('btnGuardarItem').textContent = 'Actualizar';
                        document.getElementById('btnCancelarItem').style.display = 'inline-block';

                        // Scroll al formulario
                        document.getElementById('formItem').scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                }
            }
        });
    }

    function cancelarEdicionItem() {
        document.getElementById('formItem').reset();
        document.getElementById('item_id').value = '';
        document.getElementById('tituloFormItem').textContent = 'Crear Item';
        document.getElementById('btnGuardarItem').textContent = 'Guardar';
        document.getElementById('btnCancelarItem').style.display = 'none';
    }

    function eliminarItem(id) {
        if (!confirm('¿Estás seguro de eliminar este item?')) return;

        $.post('panel_admin_api.php', {
            action: 'eliminarItem',
            id: id
        }, function(response) {
            if (response.success) {
                $.notify({
                    icon: 'fas fa-check',
                    message: 'Item eliminado'
                }, {
                    type: 'success'
                });

                // Recargar items
                if (menuActualSeleccionado) {
                    cargarItems(menuActualSeleccionado);
                }
            }
        }, 'json');
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text ? text.replace(/[&<>"']/g, m => map[m]) : '';
    }
</script>

<?php include("pie.php"); ?>