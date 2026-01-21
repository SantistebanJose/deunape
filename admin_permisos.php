<?php
include('logica/clssConsultas.php');
include('logica/clssPermisos.php');

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Solo administradores
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 1) {
    die('Acceso denegado');
}

$flagRespuesta = fnVerificarUsarioSession($_SESSION['id']);
if ($flagRespuesta == 0) {
    die('Usuario bloqueado');
}

$nombre = $_SESSION['nombre'];
$ape_usuario = $_SESSION['ape'];
$correo = $_SESSION['correo'];
$sucursal_id = $_SESSION['sucursal_id'] ?? 1; // Ajusta según tu sistema

// Cargar configuración del menú
$menu_config = include('config/menu_config.php');

// Obtener roles de la sucursal
$roles = Permisos::obtenerRolesPorSucursal($sucursal_id);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Permisos - Caracol Captain</title>
    <link rel="icon" href="assets/img/caracoles.png" type="image/x-icon" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        body {
            background-color: #f5f5f5;
        }
        .container-permisos {
            max-width: 1400px;
            margin: 50px auto;
        }
        .card-permisos {
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .modulo-section {
            margin-bottom: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
        }
        .modulo-header {
            background: linear-gradient(135deg, #1a2035 0%, #2d3561 100%);
            color: white;
            padding: 15px 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modulo-header:hover {
            background: linear-gradient(135deg, #2d3561 0%, #1a2035 100%);
        }
        .modulo-header i {
            margin-right: 10px;
        }
        .modulo-body {
            background: white;
            padding: 15px;
        }
        .submenu-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.3s;
        }
        .submenu-item:last-child {
            border-bottom: none;
        }
        .submenu-item:hover {
            background-color: #f8f9fa;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
            margin-right: 15px;
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
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #4CAF50;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        .btn-save-permisos {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 25px;
            color: white;
            transition: transform 0.3s;
        }
        .btn-save-permisos:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .role-selector {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .select-all-section {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .badge-custom {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container-permisos">
        <!-- Header -->
        <div class="text-center mb-4">
            <h1><i class="fas fa-shield-alt"></i> Administración de Permisos</h1>
            <p class="text-muted">Gestiona los permisos de acceso por rol de usuario</p>
        </div>

        <!-- Selector de Rol -->
        <div class="role-selector">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <label class="form-label"><strong>Seleccionar Rol:</strong></label>
                    <select class="form-control form-control-lg" id="selectRol" onchange="cargarPermisos()">
                        <option value="">-- Seleccione un rol --</option>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?= $rol['id_rol'] ?>" data-permisos='<?= htmlspecialchars($rol['permisos']) ?>'>
                                <?= $rol['nombre_rol'] ?> 
                                <small class="text-muted">(<?= $rol['descripcion'] ?>)</small>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-success btn-lg" onclick="crearNuevoRol()" style="margin-top: 25px;">
                        <i class="fas fa-plus"></i> Crear Nuevo Rol
                    </button>
                </div>
            </div>
        </div>

        <!-- Contenedor de Permisos -->
        <div id="permisos-container" style="display: none;">
            <!-- Acciones rápidas -->
            <div class="select-all-section">
                <div class="row">
                    <div class="col-md-6">
                        <button class="btn btn-sm btn-primary" onclick="seleccionarTodos()">
                            <i class="fas fa-check-double"></i> Seleccionar Todos
                        </button>
                        <button class="btn btn-sm btn-secondary" onclick="deseleccionarTodos()">
                            <i class="fas fa-times"></i> Deseleccionar Todos
                        </button>
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-save-permisos" onclick="guardarPermisos()">
                            <i class="fas fa-save"></i> Guardar Permisos
                        </button>
                    </div>
                </div>
            </div>

            <!-- Módulos -->
            <?php foreach ($menu_config as $modulo_id => $modulo): ?>
                <div class="modulo-section">
                    <div class="modulo-header" onclick="toggleModulo('<?= $modulo_id ?>')">
                        <div>
                            <i class="<?= $modulo['icono'] ?>"></i>
                            <strong><?= $modulo['nombre'] ?></strong>
                            <span class="badge badge-custom bg-info ms-2" id="count-<?= $modulo_id ?>">0/<?= count($modulo['submenu']) ?></span>
                        </div>
                        <div>
                            <label class="switch" onclick="event.stopPropagation();">
                                <input type="checkbox" 
                                       class="modulo-check" 
                                       data-modulo="<?= $modulo_id ?>"
                                       onchange="toggleTodoModulo('<?= $modulo_id ?>')">
                                <span class="slider"></span>
                            </label>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="modulo-body" id="body-<?= $modulo_id ?>" style="display: none;">
                        <?php foreach ($modulo['submenu'] as $sub_id => $submodulo): ?>
                            <div class="submenu-item">
                                <label class="switch">
                                    <input type="checkbox" 
                                           class="permiso-check submenu-check-<?= $modulo_id ?>" 
                                           data-modulo="<?= $modulo_id ?>"
                                           data-submenu="<?= $sub_id ?>"
                                           onchange="actualizarContador('<?= $modulo_id ?>')">
                                    <span class="slider"></span>
                                </label>
                                <div class="flex-grow-1">
                                    <strong><?= $submodulo['nombre'] ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-link"></i> <?= $submodulo['url'] ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Botón Guardar Final -->
            <div class="text-center mt-4">
                <button class="btn btn-save-permisos btn-lg" onclick="guardarPermisos()">
                    <i class="fas fa-save"></i> Guardar Todos los Cambios
                </button>
            </div>
        </div>
    </div>

    <script src="assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let rolActual = 0;
        const menuConfig = <?= json_encode($menu_config) ?>;

        function toggleModulo(moduloId) {
            const body = document.getElementById('body-' + moduloId);
            const icon = body.previousElementSibling.querySelector('.fa-chevron-down');
            
            if (body.style.display === 'none') {
                body.style.display = 'block';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                body.style.display = 'none';
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }

        function cargarPermisos() {
            const selectRol = document.getElementById('selectRol');
            rolActual = selectRol.value;
            
            if (!rolActual) {
                document.getElementById('permisos-container').style.display = 'none';
                return;
            }

            const selectedOption = selectRol.options[selectRol.selectedIndex];
            const permisos = JSON.parse(selectedOption.getAttribute('data-permisos') || '{}');

            // Desmarcar todos
            document.querySelectorAll('.permiso-check').forEach(check => {
                check.checked = false;
            });

            // Marcar permisos del rol
            for (let moduloId in permisos) {
                if (permisos[moduloId].submenu) {
                    for (let subId in permisos[moduloId].submenu) {
                        if (permisos[moduloId].submenu[subId] === true) {
                            const checkbox = document.querySelector(`[data-modulo="${moduloId}"][data-submenu="${subId}"]`);
                            if (checkbox) checkbox.checked = true;
                        }
                    }
                }
                actualizarContador(moduloId);
            }

            // Actualizar checks de módulos
            document.querySelectorAll('.modulo-check').forEach(check => {
                const moduloId = check.dataset.modulo;
                const submenuChecks = document.querySelectorAll(`.submenu-check-${moduloId}`);
                const todosChecked = Array.from(submenuChecks).every(sub => sub.checked);
                check.checked = todosChecked && submenuChecks.length > 0;
            });

            document.getElementById('permisos-container').style.display = 'block';
        }

        function actualizarContador(moduloId) {
            const submenuChecks = document.querySelectorAll(`.submenu-check-${moduloId}`);
            const checked = Array.from(submenuChecks).filter(check => check.checked).length;
            const total = submenuChecks.length;
            
            const counter = document.getElementById('count-' + moduloId);
            if (counter) {
                counter.textContent = `${checked}/${total}`;
                counter.className = 'badge badge-custom ms-2 ' + (checked === total ? 'bg-success' : checked > 0 ? 'bg-warning' : 'bg-info');
            }
        }

        function toggleTodoModulo(moduloId) {
            const moduloCheck = document.querySelector(`.modulo-check[data-modulo="${moduloId}"]`);
            const submenuChecks = document.querySelectorAll(`.submenu-check-${moduloId}`);
            
            submenuChecks.forEach(check => {
                check.checked = moduloCheck.checked;
            });
            
            actualizarContador(moduloId);
        }

        function seleccionarTodos() {
            document.querySelectorAll('.permiso-check').forEach(check => check.checked = true);
            document.querySelectorAll('.modulo-check').forEach(check => check.checked = true);
            
            for (let moduloId in menuConfig) {
                actualizarContador(moduloId);
            }
        }

        function deseleccionarTodos() {
            document.querySelectorAll('.permiso-check').forEach(check => check.checked = false);
            document.querySelectorAll('.modulo-check').forEach(check => check.checked = false);
            
            for (let moduloId in menuConfig) {
                actualizarContador(moduloId);
            }
        }

        function guardarPermisos() {
            if (!rolActual) {
                Swal.fire('Error', 'Seleccione un rol primero', 'error');
                return;
            }

            // Construir objeto de permisos
            const permisos = {};

            for (let moduloId in menuConfig) {
                const submenuChecks = document.querySelectorAll(`.submenu-check-${moduloId}:checked`);
                
                if (submenuChecks.length > 0) {
                    permisos[moduloId] = {
                        ver: true,
                        submenu: {}
                    };

                    submenuChecks.forEach(check => {
                        const subId = check.dataset.submenu;
                        permisos[moduloId].submenu[subId] = true;
                    });
                }
            }

            // Enviar al servidor
            fetch('logica/ajax_permisos.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    action: 'guardar',
                    id_rol: rolActual,
                    permisos: permisos
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Guardado!',
                        text: 'Los permisos se han actualizado correctamente',
                        timer: 2000
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Error al guardar: ' + error, 'error');
            });
        }

        function crearNuevoRol() {
            Swal.fire({
                title: 'Crear Nuevo Rol',
                html: `
                    <input id="nuevo-rol-nombre" class="swal2-input" placeholder="Nombre del Rol">
                    <textarea id="nuevo-rol-desc" class="swal2-textarea" placeholder="Descripción"></textarea>
                `,
                showCancelButton: true,
                confirmButtonText: 'Crear',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const nombre = document.getElementById('nuevo-rol-nombre').value;
                    const descripcion = document.getElementById('nuevo-rol-desc').value;
                    
                    if (!nombre) {
                        Swal.showValidationMessage('El nombre es requerido');
                        return false;
                    }
                    
                    return { nombre, descripcion };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Aquí implementarías la creación del rol
                    Swal.fire('Info', 'Funcionalidad de creación en desarrollo', 'info');
                }
            });
        }
    </script>
</body>
</html>