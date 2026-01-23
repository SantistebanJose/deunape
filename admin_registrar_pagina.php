<?php
include("cabecera.php");
/**
 * admin_registrar_pagina.php
 * Sistema para registrar nuevas páginas PHP en el sistema de permisos
 */

require_once("logica/bd.php");
require_once("MenuManager.php");

// Verificar acceso
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$menuManager = new MenuManager($_SESSION['nombre_rol'] ?? $_SESSION['rol']);

if (!$menuManager->hasPermission('admin_roles')) {
    $_SESSION['error'] = "No tienes permisos";
    header("Location: index.php");
    exit();
}


// Obtener todos los permisos disponibles
$permisos = executeQuerymenumanager("SELECT * FROM permisos WHERE activo = true ORDER BY categoria, nombre");

// Obtener todos los roles
$roles = executeQuerymenumanager("SELECT * FROM roles WHERE activo = true ORDER BY nombre");

// Obtener páginas ya registradas
$paginasRegistradas = executeQuerymenumanager("
    SELECT pe.*, p.codigo as permiso_codigo, p.nombre as permiso_nombre 
    FROM paginas_especificas pe 
    LEFT JOIN permisos p ON pe.permiso_id = p.id 
    WHERE pe.activo = true 
    ORDER BY pe.nombre_archivo
");

// Obtener páginas públicas
$paginasPublicas = executeQuerymenumanager("
    SELECT * FROM paginas_publicas 
    WHERE activo = true 
    ORDER BY nombre_archivo
");


?>

<style>
    .form-section {
        background: white;
        padding: 25px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px 20px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .page-item {
        padding: 15px;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        margin-bottom: 10px;
        background: white;
        transition: all 0.2s;
    }

    .page-item:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .badge-custom {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85em;
    }

    .help-text {
        background: #e3f2fd;
        padding: 15px;
        border-left: 4px solid #2196f3;
        border-radius: 4px;
        margin-bottom: 20px;
    }

    .role-checkbox {
        margin-right: 15px;
        margin-bottom: 10px;
    }
</style>

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">
                <i class="fas fa-file-code"></i> Registrar Nueva Página PHP
            </h4>
        </div>

        <div class="row">
            <div class="col-md-6">
                <!-- Formulario para Página Específica -->
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-lock"></i> Registrar Página con Permisos
                    </div>

                    <div class="help-text">
                        <strong>¿Cuándo usar esto?</strong><br>
                        Usa este formulario para páginas que requieren permisos específicos.
                        Por ejemplo: reportes, configuraciones, módulos administrativos.
                    </div>

                    <form id="formPaginaEspecifica">
                        <div class="form-group">
                            <label>Nombre del Archivo PHP <span class="text-danger">*</span></label>
                            <input type="text"
                                class="form-control"
                                name="nombre_archivo"
                                placeholder="ejemplo: mi_reporte.php"
                                required>
                            <small class="text-muted">Incluye la extensión .php</small>
                        </div>

                        <div class="form-group">
                            <label>Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control"
                                name="descripcion"
                                rows="2"
                                placeholder="Breve descripción de la página"
                                required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Permiso Requerido</label>
                            <select class="form-control" name="permiso_id">
                                <option value="">Sin permiso específico</option>
                                <?php
                                $categoriaActual = '';
                                foreach ($permisos as $permiso):
                                    if ($categoriaActual != $permiso['categoria']) {
                                        if ($categoriaActual != '') echo '</optgroup>';
                                        echo '<optgroup label="' . ucfirst($permiso['categoria']) . '">';
                                        $categoriaActual = $permiso['categoria'];
                                    }
                                ?>
                                    <option value="<?= $permiso['id'] ?>">
                                        <?= $permiso['nombre'] ?> (<?= $permiso['codigo'] ?>)
                                    </option>
                                <?php endforeach; ?>
                                <?php if ($categoriaActual != '') echo '</optgroup>'; ?>
                            </select>
                            <small class="text-muted">Opcional: Permiso necesario para acceder</small>
                        </div>

                        <div class="form-group">
                            <label>Roles que pueden acceder <span class="text-danger">*</span></label>
                            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                <?php foreach ($roles as $rol): ?>
                                    <div class="role-checkbox">
                                        <label>
                                            <input type="checkbox"
                                                name="roles[]"
                                                value="<?= $rol['id_rol'] ?>">
                                            <?= htmlspecialchars($rol['nombre']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <small class="text-muted">Selecciona al menos un rol</small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Registrar Página
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Formulario para Página Pública -->
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-globe"></i> Registrar Página Pública
                    </div>

                    <div class="help-text">
                        <strong>¿Cuándo usar esto?</strong><br>
                        Usa este formulario para páginas accesibles para TODOS los usuarios autenticados.
                        Por ejemplo: dashboard, perfil, ayuda.
                    </div>

                    <form id="formPaginaPublica">
                        <div class="form-group">
                            <label>Nombre del Archivo PHP <span class="text-danger">*</span></label>
                            <input type="text"
                                class="form-control"
                                name="nombre_archivo"
                                placeholder="ejemplo: mi_dashboard.php"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control"
                                name="descripcion"
                                rows="2"
                                required></textarea>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Registrar Página Pública
                        </button>
                    </form>
                </div>

                <!-- Script de ayuda -->
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-code"></i> Código de Protección
                    </div>

                    <p>Agrega este código al inicio de tu nueva página PHP:</p>

                    <pre style="background: #2d2d30; color: #d4d4d4; padding: 15px; border-radius: 6px; overflow-x: auto;"><code>&lt;?php
session_start();
require_once("logica/bd.php");
require_once("MenuManager.php");

// Verificar sesión
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Verificar permisos
$menuManager = new MenuManager($_SESSION['nombre_rol']);
$menuManager->requirePageAccess('<span style="color: #4ec9b0;">NOMBRE_ARCHIVO.php</span>');

// Tu código aquí...
?&gt;</code></pre>
                </div>
            </div>
        </div>

        <!-- Lista de Páginas Registradas -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-list"></i> Páginas con Permisos (<?= count($paginasRegistradas) ?>)
                    </div>

                    <?php foreach ($paginasRegistradas as $pagina): ?>
                        <div class="page-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        <i class="fas fa-file-code text-primary"></i>
                                        <?= htmlspecialchars($pagina['nombre_archivo']) ?>
                                    </h6>
                                    <small class="text-muted"><?= htmlspecialchars($pagina['descripcion']) ?></small>
                                    <?php if ($pagina['permiso_codigo']): ?>
                                        <br>
                                        <span class="badge badge-custom bg-info mt-1">
                                            <?= htmlspecialchars($pagina['permiso_codigo']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <button class="btn btn-danger btn-sm"
                                    onclick="eliminarPagina(<?= $pagina['id'] ?>, 'especifica')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (empty($paginasRegistradas)): ?>
                        <p class="text-muted text-center py-4">No hay páginas específicas registradas</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-globe"></i> Páginas Públicas (<?= count($paginasPublicas) ?>)
                    </div>

                    <?php foreach ($paginasPublicas as $pagina): ?>
                        <div class="page-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        <i class="fas fa-globe text-success"></i>
                                        <?= htmlspecialchars($pagina['nombre_archivo']) ?>
                                    </h6>
                                    <small class="text-muted"><?= htmlspecialchars($pagina['descripcion']) ?></small>
                                </div>
                                <button class="btn btn-danger btn-sm"
                                    onclick="eliminarPagina(<?= $pagina['id'] ?>, 'publica')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (empty($paginasPublicas)): ?>
                        <p class="text-muted text-center py-4">No hay páginas públicas registradas</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>




<script>
    // Registrar página específica
    $('#formPaginaEspecifica').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('action', 'registrarPaginaEspecifica');

        $.ajax({
            url: 'paginas_api.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
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

                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error al registrar página');
            }
        });
    });

    // Registrar página pública
    $('#formPaginaPublica').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('action', 'registrarPaginaPublica');

        $.ajax({
            url: 'paginas_api.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
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

                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    });

    // Eliminar página
    function eliminarPagina(id, tipo) {
        if (!confirm('¿Eliminar esta página del sistema?')) return;

        $.ajax({
            url: 'paginas_api.php',
            method: 'POST',
            data: {
                action: 'eliminarPagina',
                id: id,
                tipo: tipo
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
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

                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    }
</script>

<?php include("template/pie.php"); ?>