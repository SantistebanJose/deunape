<?php
/**
 * SCRIPT_MIGRACION_HELPER.php
 * Script de ayuda para migrar del sistema antiguo al nuevo
 * NO EJECUTAR EN PRODUCCIÓN - Solo para desarrollo
 */

// Este script te ayuda a:
// 1. Ver qué páginas necesitan configurarse
// 2. Generar código de configuración automáticamente
// 3. Verificar la compatibilidad

session_start();

if (!isset($_SESSION['rol'])) {
    die("Debes estar logueado para usar este script");
}

require_once('classes/MenuManager.php');
$menuManager = new MenuManager($_SESSION['rol']);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Script de Migración - Helper</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        .code-block {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            white-space: pre-wrap;
        }
        .section {
            margin: 30px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>🔧 Helper de Migración al Nuevo Sistema</h1>
        <p class="lead">Esta herramienta te ayuda a migrar del sistema antiguo al nuevo sistema de menús</p>

        <!-- ============================================ -->
        <!-- SECCIÓN 1: Información actual -->
        <!-- ============================================ -->
        <div class="section">
            <h2>📊 1. Tu Configuración Actual</h2>
            <?php
            $stats = $menuManager->getUserStats();
            ?>
            <table class="table table-bordered">
                <tr>
                    <th>Tu rol:</th>
                    <td><span class="badge bg-primary"><?php echo htmlspecialchars($stats['role']); ?></span></td>
                </tr>
                <tr>
                    <th>Permisos totales:</th>
                    <td><?php echo $stats['total_permissions']; ?></td>
                </tr>
                <tr>
                    <th>Menús disponibles:</th>
                    <td><?php echo $stats['accessible_menus']; ?></td>
                </tr>
            </table>
        </div>

        <!-- ============================================ -->
        <!-- SECCIÓN 2: Escanear archivos PHP -->
        <!-- ============================================ -->
        <div class="section">
            <h2>📁 2. Archivos PHP en el proyecto</h2>
            <p>Estos son los archivos .php encontrados que podrían necesitar configuración:</p>
            <?php
            $phpFiles = glob('*.php');
            $excludeFiles = ['login.php', 'logout.php', 'index.php', 'cabecera.php', 'cabecera_nueva.php', 
                           'SCRIPT_MIGRACION_HELPER.php', 'EJEMPLO_PAGINA_PROTEGIDA.php'];
            
            echo '<div class="row">';
            foreach ($phpFiles as $file) {
                if (!in_array($file, $excludeFiles)) {
                    $pageInfo = $menuManager->getPageInfo($file);
                    $isConfigured = ($pageInfo !== null);
                    $canAccess = $menuManager->canAccessSpecificPage($file);
                    
                    echo '<div class="col-md-6 mb-3">';
                    echo '<div class="card">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($file) . '</h5>';
                    
                    if ($isConfigured) {
                        echo '<span class="badge bg-success">✓ Configurado</span> ';
                    } else {
                        echo '<span class="badge bg-warning">⚠ Sin configurar</span> ';
                    }
                    
                    if ($canAccess) {
                        echo '<span class="badge bg-info">Tienes acceso</span>';
                    } else {
                        echo '<span class="badge bg-danger">Sin acceso</span>';
                    }
                    
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            }
            echo '</div>';
            ?>
        </div>

        <!-- ============================================ -->
        <!-- SECCIÓN 3: Generar configuración -->
        <!-- ============================================ -->
        <div class="section">
            <h2>⚙️ 3. Generar Configuración para Páginas</h2>
            <p>Copia este código y agrégalo a tu <code>menu_config.php</code> en el array <code>$specificPagesConfig</code>:</p>
            
            <?php
            $phpFiles = glob('*.php');
            $newConfig = [];
            
            foreach ($phpFiles as $file) {
                if (!in_array($file, $excludeFiles)) {
                    $pageInfo = $menuManager->getPageInfo($file);
                    if (!$pageInfo) {
                        // Generar configuración sugerida
                        $cleanName = str_replace(['.php', '_', '-'], ['', ' ', ' '], $file);
                        $cleanName = ucwords($cleanName);
                        
                        $newConfig[$file] = [
                            'roles' => ['administrador'],  // Ajustar según necesidad
                            'permission' => strtolower(str_replace(['.php', ' '], ['', '_'], $cleanName)),
                            'description' => 'Página: ' . $cleanName
                        ];
                    }
                }
            }
            
            if (!empty($newConfig)) {
                echo '<div class="code-block">';
                echo "// Agregar esto a \$specificPagesConfig en menu_config.php\n\n";
                foreach ($newConfig as $file => $config) {
                    echo "'{$file}' => [\n";
                    echo "    'roles' => ['" . implode("', '", $config['roles']) . "'],\n";
                    echo "    'permission' => '{$config['permission']}',\n";
                    echo "    'description' => '{$config['description']}'\n";
                    echo "],\n\n";
                }
                echo '</div>';
            } else {
                echo '<div class="alert alert-success">✓ Todas las páginas ya están configuradas</div>';
            }
            ?>
        </div>

        <!-- ============================================ -->
        <!-- SECCIÓN 4: Código para proteger páginas -->
        <!-- ============================================ -->
        <div class="section">
            <h2>🔒 4. Código para Proteger tus Páginas</h2>
            <p>Agrega este código al inicio de cada página PHP que quieras proteger:</p>
            
            <div class="code-block"><?php echo htmlspecialchars('<?php
// Al inicio del archivo, después de session_start()
require_once(\'classes/MenuManager.php\');
$menuManager = new MenuManager($_SESSION[\'rol\']);

// Proteger esta página
$menuManager->requirePageAccess(basename(__FILE__), \'index.php\');

// Función de compatibilidad (opcional)
function tienePermiso($moduloPermiso = \'\', $permisoEspecifico = \'\') {
    global $menuManager;
    return $menuManager->tienePermiso($moduloPermiso, $permisoEspecifico);
}
?>'); ?></div>
        </div>

        <!-- ============================================ -->
        <!-- SECCIÓN 5: Ejemplos de uso -->
        <!-- ============================================ -->
        <div class="section">
            <h2>💡 5. Ejemplos de Uso</h2>
            
            <h4>Verificar permiso en el código:</h4>
            <div class="code-block"><?php echo htmlspecialchars('<?php if ($menuManager->hasPermission(\'admin_usuarios\')): ?>
    <button>Administrar Usuarios</button>
<?php endif; ?>'); ?></div>

            <h4>Verificar si puede acceder a otra página:</h4>
            <div class="code-block"><?php echo htmlspecialchars('<?php
if ($menuManager->canAccessSpecificPage(\'compra.php\')) {
    echo \'<a href="compra.php">Ir a Compras</a>\';
}
?>'); ?></div>

            <h4>Mostrar solo si tiene el rol:</h4>
            <div class="code-block"><?php echo htmlspecialchars('<?php if (tienePermiso(\'administrador\')): ?>
    <div>Contenido solo para administradores</div>
<?php endif; ?>'); ?></div>
        </div>

        <!-- ============================================ -->
        <!-- SECCIÓN 6: Checklist de migración -->
        <!-- ============================================ -->
        <div class="section">
            <h2>✅ 6. Checklist de Migración</h2>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="check1">
                <label class="form-check-label" for="check1">
                    Copiar <code>config/menu_config.php</code> a tu carpeta config/
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="check2">
                <label class="form-check-label" for="check2">
                    Copiar <code>classes/MenuManager.php</code> a tu carpeta classes/
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="check3">
                <label class="form-check-label" for="check3">
                    Actualizar tu cabecera.php con el nuevo código
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="check4">
                <label class="form-check-label" for="check4">
                    Configurar todas las páginas en <code>$specificPagesConfig</code>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="check5">
                <label class="form-check-label" for="check5">
                    Agregar protección a páginas críticas con <code>requirePageAccess()</code>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="check6">
                <label class="form-check-label" for="check6">
                    Probar con diferentes roles de usuario
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="check7">
                <label class="form-check-label" for="check7">
                    Revisar los logs de accesos no autorizados
                </label>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- SECCIÓN 7: Menús actuales -->
        <!-- ============================================ -->
        <div class="section">
            <h2>📋 7. Tus Menús Actuales</h2>
            <?php
            $menus = $menuManager->getFilteredMenus();
            foreach ($menus as $menuId => $menu) {
                echo '<div class="card mb-3">';
                echo '<div class="card-header">';
                echo '<i class="' . $menu['icon'] . '"></i> ' . htmlspecialchars($menu['title']);
                echo '</div>';
                echo '<div class="card-body">';
                echo '<ul class="list-group">';
                foreach ($menu['items'] as $item) {
                    echo '<li class="list-group-item">';
                    echo htmlspecialchars($item['title']) . ' → ' . htmlspecialchars($item['link']);
                    if (isset($item['permission'])) {
                        echo ' <span class="badge bg-secondary">' . htmlspecialchars($item['permission']) . '</span>';
                    }
                    echo '</li>';
                }
                echo '</ul>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- ============================================ -->
        <!-- SECCIÓN 8: Comparación antes/después -->
        <!-- ============================================ -->
        <div class="section">
            <h2>🔄 8. Comparación: Antes vs Después</h2>
            
            <div class="row">
                <div class="col-md-6">
                    <h4>❌ Código Antiguo:</h4>
                    <div class="code-block"><?php echo htmlspecialchars('<?php
// cabecera.php
include(\'logica/helper_permisos.php\');
$permisos_usuario = inicializarSistemaPermisos();

// En el menú
<?php if (tienePermiso(\'administrador\')): ?>
    <li>
        <a href="usuarios.php">
            <span>Usuarios</span>
        </a>
    </li>
<?php endif; ?>
'); ?></div>
                </div>
                
                <div class="col-md-6">
                    <h4>✅ Código Nuevo:</h4>
                    <div class="code-block"><?php echo htmlspecialchars('<?php
// cabecera.php
require_once(\'classes/MenuManager.php\');
$menuManager = new MenuManager($_SESSION[\'rol\']);

// En el menú - Automático!
<?php echo $menuManager->renderMenu(); ?>

// La configuración está en menu_config.php
'); ?></div>
                </div>
            </div>
        </div>

        <div class="alert alert-info mt-4">
            <strong>💡 Tip:</strong> Guarda esta página como referencia durante la migración. 
            Puedes volver a ejecutarla en cualquier momento para verificar el progreso.
        </div>

        <div class="text-center mt-5 mb-5">
            <a href="index.php" class="btn btn-primary btn-lg">Volver al Dashboard</a>
            <a href="EJEMPLO_PAGINA_PROTEGIDA.php" class="btn btn-success btn-lg">Ver Ejemplo de Página</a>
        </div>
    </div>

    <script src="assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
</body>
</html>