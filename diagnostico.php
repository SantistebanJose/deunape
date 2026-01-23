<?php
/**
 * Archivo de Diagnóstico - Sistema de Gestión de Permisos
 * Este archivo ayuda a identificar problemas de configuración
 */

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico del Sistema</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        .warning {
            color: #ffc107;
            font-weight: bold;
        }
        .info {
            color: #17a2b8;
        }
        h2 {
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .check-item {
            padding: 10px;
            margin: 5px 0;
            border-left: 4px solid #ddd;
            padding-left: 15px;
        }
        .check-item.ok {
            border-left-color: #28a745;
            background: #d4edda;
        }
        .check-item.fail {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        .check-item.warn {
            border-left-color: #ffc107;
            background: #fff3cd;
        }
        pre {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 3px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>🔍 Diagnóstico del Sistema de Gestión de Permisos</h1>
    
    <?php
    $errores = 0;
    $advertencias = 0;
    $exitos = 0;
    
    // ===========================================================================
    // 1. VERIFICAR ARCHIVOS REQUERIDOS
    // ===========================================================================
    echo '<div class="test-section">';
    echo '<h2>1. Verificación de Archivos</h2>';
    
    $archivos_requeridos = [
        'logica/bd.php' => 'Archivo de conexión a base de datos',
        'classes/MenuPermisos.php' => 'Clase de gestión de permisos',
        'logica/permisos_api.php' => 'API de permisos',
        'assets/js/gestion_permisos.js' => 'JavaScript principal'
    ];
    
    foreach ($archivos_requeridos as $archivo => $descripcion) {
        echo '<div class="check-item ' . (file_exists($archivo) ? 'ok' : 'fail') . '">';
        if (file_exists($archivo)) {
            echo '<span class="success">✓</span> ';
            echo "<strong>$descripcion:</strong> $archivo ";
            echo '<span class="info">(Tamaño: ' . filesize($archivo) . ' bytes)</span>';
            $exitos++;
        } else {
            echo '<span class="error">✗</span> ';
            echo "<strong>$descripcion:</strong> $archivo <span class='error'>NO ENCONTRADO</span>";
            $errores++;
        }
        echo '</div>';
    }
    echo '</div>';
    
    // ===========================================================================
    // 2. VERIFICAR FUNCIÓN DE CONEXIÓN
    // ===========================================================================
    echo '<div class="test-section">';
    echo '<h2>2. Verificación de Conexión a Base de Datos</h2>';
    
    if (file_exists('logica/bd.php')) {
        require_once 'logica/bd.php';
        
        echo '<div class="check-item ' . (function_exists('obtenerConexionPDO') ? 'ok' : 'fail') . '">';
        if (function_exists('obtenerConexionPDO')) {
            echo '<span class="success">✓</span> Función obtenerConexionPDO() existe';
            $exitos++;
            
            // Intentar conectar
            try {
                $conn = obtenerConexionPDO();
                if ($conn) {
                    echo '</div>';
                    echo '<div class="check-item ok">';
                    echo '<span class="success">✓</span> Conexión a base de datos exitosa';
                    $exitos++;
                    
                    // Verificar tablas
                    echo '</div>';
                    echo '<h3>Tablas de la Base de Datos:</h3>';
                    
                    $tablas_requeridas = [
                        'usuarios' => 'SELECT COUNT(*) FROM usuarios',
                        'roles' => 'SELECT COUNT(*) FROM roles',
                        'permisos' => 'SELECT COUNT(*) FROM permisos',
                        'usuario_roles' => 'SELECT COUNT(*) FROM usuario_roles',
                        'rol_permisos' => 'SELECT COUNT(*) FROM rol_permisos',
                        'usuario_permisos' => 'SELECT COUNT(*) FROM usuario_permisos'
                    ];
                    
                    foreach ($tablas_requeridas as $tabla => $query) {
                        try {
                            $stmt = $conn->query($query);
                            $count = $stmt->fetchColumn();
                            echo '<div class="check-item ok">';
                            echo "<span class='success'>✓</span> Tabla <strong>$tabla</strong> existe ($count registros)";
                            echo '</div>';
                            $exitos++;
                        } catch (PDOException $e) {
                            echo '<div class="check-item fail">';
                            echo "<span class='error'>✗</span> Tabla <strong>$tabla</strong> no encontrada o error: " . $e->getMessage();
                            echo '</div>';
                            $errores++;
                        }
                    }
                } else {
                    echo '</div>';
                    echo '<div class="check-item fail">';
                    echo '<span class="error">✗</span> No se pudo conectar a la base de datos';
                    $errores++;
                }
            } catch (Exception $e) {
                echo '</div>';
                echo '<div class="check-item fail">';
                echo '<span class="error">✗</span> Error al conectar: ' . $e->getMessage();
                $errores++;
            }
        } else {
            echo '<span class="error">✗</span> Función obtenerConexionPDO() NO EXISTE';
            echo '<br><small>El archivo bd.php debe contener esta función</small>';
            $errores++;
        }
        echo '</div>';
    } else {
        echo '<div class="check-item fail">';
        echo '<span class="error">✗</span> No se puede verificar (archivo bd.php no encontrado)';
        $errores++;
        echo '</div>';
    }
    echo '</div>';
    
    // ===========================================================================
    // 3. VERIFICAR CLASE MENUPERMISOS
    // ===========================================================================
    echo '<div class="test-section">';
    echo '<h2>3. Verificación de Clase MenuPermisos</h2>';
    
    if (file_exists('classes/MenuPermisos.php')) {
        require_once 'classes/MenuPermisos.php';
        
        echo '<div class="check-item ' . (class_exists('MenuPermisos') ? 'ok' : 'fail') . '">';
        if (class_exists('MenuPermisos')) {
            echo '<span class="success">✓</span> Clase MenuPermisos existe';
            $exitos++;
            
            // Verificar métodos requeridos
            $metodos_requeridos = [
                'obtenerTodosRoles',
                'obtenerTodosPermisos',
                'obtenerRolesUsuario',
                'obtenerPermisosUsuario',
                'obtenerPermisosRol',
                'asignarRolUsuario',
                'asignarPermisoUsuario',
                'asignarPermisoRol',
                'tienePermiso'
            ];
            
            echo '</div>';
            foreach ($metodos_requeridos as $metodo) {
                echo '<div class="check-item ' . (method_exists('MenuPermisos', $metodo) ? 'ok' : 'fail') . '">';
                if (method_exists('MenuPermisos', $metodo)) {
                    echo "<span class='success'>✓</span> Método $metodo() existe";
                    $exitos++;
                } else {
                    echo "<span class='error'>✗</span> Método $metodo() NO EXISTE";
                    $errores++;
                }
                echo '</div>';
            }
        } else {
            echo '<span class="error">✗</span> Clase MenuPermisos NO EXISTE';
            $errores++;
        }
        echo '</div>';
    } else {
        echo '<div class="check-item fail">';
        echo '<span class="error">✗</span> No se puede verificar (archivo MenuPermisos.php no encontrado)';
        $errores++;
        echo '</div>';
    }
    echo '</div>';
    
    // ===========================================================================
    // 4. VERIFICAR SESIÓN
    // ===========================================================================
    echo '<div class="test-section">';
    echo '<h2>4. Verificación de Sesión</h2>';
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    echo '<div class="check-item ' . (session_status() === PHP_SESSION_ACTIVE ? 'ok' : 'fail') . '">';
    if (session_status() === PHP_SESSION_ACTIVE) {
        echo '<span class="success">✓</span> Sesión PHP activa';
        $exitos++;
    } else {
        echo '<span class="error">✗</span> Sesión PHP NO activa';
        $errores++;
    }
    echo '</div>';
    
    echo '<div class="check-item ' . (isset($_SESSION['usuario_id']) ? 'ok' : 'warn') . '">';
    if (isset($_SESSION['usuario_id'])) {
        echo '<span class="success">✓</span> Variable usuario_id en sesión: ' . $_SESSION['usuario_id'];
        $exitos++;
    } else {
        echo '<span class="warning">⚠</span> Variable usuario_id NO está en sesión (el usuario no ha iniciado sesión)';
        $advertencias++;
    }
    echo '</div>';
    
    echo '</div>';
    
    // ===========================================================================
    // 5. PROBAR API
    // ===========================================================================
    echo '<div class="test-section">';
    echo '<h2>5. Prueba de API</h2>';
    
    if (file_exists('logica/permisos_api.php')) {
        echo '<div class="check-item ok">';
        echo '<span class="success">✓</span> Archivo API existe';
        echo '<br><small>Para probar, accede a: <code>logica/permisos_api.php?action=obtenerRoles</code></small>';
        $exitos++;
        echo '</div>';
        
        // Intentar hacer una llamada interna
        echo '<div class="check-item info">';
        echo '<span class="info">ℹ</span> Intenta acceder manualmente a estas URLs para probar:';
        echo '<pre>';
        echo "GET: logica/permisos_api.php?action=obtenerRoles\n";
        echo "GET: logica/permisos_api.php?action=obtenerPermisos\n";
        echo "GET: logica/permisos_api.php?action=obtenerUsuarios\n";
        echo '</pre>';
        echo '</div>';
    } else {
        echo '<div class="check-item fail">';
        echo '<span class="error">✗</span> Archivo API no encontrado';
        $errores++;
        echo '</div>';
    }
    echo '</div>';
    
    // ===========================================================================
    // 6. CONFIGURACIÓN PHP
    // ===========================================================================
    echo '<div class="test-section">';
    echo '<h2>6. Configuración de PHP</h2>';
    
    $config_items = [
        ['PDO Extension', extension_loaded('pdo')],
        ['PDO PostgreSQL Driver', extension_loaded('pdo_pgsql')],
        ['JSON Extension', extension_loaded('json')],
        ['Session Support', function_exists('session_start')],
    ];
    
    foreach ($config_items as $item) {
        list($nombre, $estado) = $item;
        echo '<div class="check-item ' . ($estado ? 'ok' : 'fail') . '">';
        if ($estado) {
            echo "<span class='success'>✓</span> $nombre habilitado";
            $exitos++;
        } else {
            echo "<span class='error'>✗</span> $nombre NO habilitado";
            $errores++;
        }
        echo '</div>';
    }
    
    echo '<div class="check-item info">';
    echo '<span class="info">ℹ</span> Versión de PHP: ' . phpversion();
    echo '</div>';
    
    echo '</div>';
    
    // ===========================================================================
    // RESUMEN
    // ===========================================================================
    echo '<div class="test-section">';
    echo '<h2>📊 Resumen del Diagnóstico</h2>';
    
    $total = $exitos + $errores + $advertencias;
    
    echo '<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin: 20px 0;">';
    
    echo '<div style="text-align: center; padding: 20px; background: #d4edda; border-radius: 5px;">';
    echo '<div style="font-size: 48px; color: #28a745;">✓</div>';
    echo '<div style="font-size: 32px; font-weight: bold; color: #28a745;">' . $exitos . '</div>';
    echo '<div>Exitosos</div>';
    echo '</div>';
    
    echo '<div style="text-align: center; padding: 20px; background: #fff3cd; border-radius: 5px;">';
    echo '<div style="font-size: 48px; color: #ffc107;">⚠</div>';
    echo '<div style="font-size: 32px; font-weight: bold; color: #ffc107;">' . $advertencias . '</div>';
    echo '<div>Advertencias</div>';
    echo '</div>';
    
    echo '<div style="text-align: center; padding: 20px; background: #f8d7da; border-radius: 5px;">';
    echo '<div style="font-size: 48px; color: #dc3545;">✗</div>';
    echo '<div style="font-size: 32px; font-weight: bold; color: #dc3545;">' . $errores . '</div>';
    echo '<div>Errores</div>';
    echo '</div>';
    
    echo '</div>';
    
    if ($errores > 0) {
        echo '<div class="check-item fail" style="font-size: 16px; padding: 20px;">';
        echo '<strong>⚠️ SE ENCONTRARON ERRORES CRÍTICOS</strong><br>';
        echo 'El sistema no funcionará correctamente hasta que se solucionen todos los errores marcados arriba.';
        echo '</div>';
    } else if ($advertencias > 0) {
        echo '<div class="check-item warn" style="font-size: 16px; padding: 20px;">';
        echo '<strong>⚠️ SE ENCONTRARON ADVERTENCIAS</strong><br>';
        echo 'El sistema puede funcionar, pero se recomienda revisar las advertencias.';
        echo '</div>';
    } else {
        echo '<div class="check-item ok" style="font-size: 16px; padding: 20px;">';
        echo '<strong>✅ SISTEMA LISTO</strong><br>';
        echo 'Todos los componentes están correctamente configurados. El sistema debería funcionar sin problemas.';
        echo '</div>';
    }
    
    echo '</div>';
    
    // ===========================================================================
    // PRÓXIMOS PASOS
    // ===========================================================================
    echo '<div class="test-section">';
    echo '<h2>🔧 Próximos Pasos</h2>';
    
    if ($errores > 0) {
        echo '<ol>';
        echo '<li>Corrige todos los errores marcados en rojo arriba</li>';
        echo '<li>Verifica que todos los archivos existan en las rutas correctas</li>';
        echo '<li>Asegúrate de que la base de datos esté configurada correctamente</li>';
        echo '<li>Vuelve a ejecutar este diagnóstico para verificar las correcciones</li>';
        echo '</ol>';
    } else {
        echo '<ol>';
        echo '<li>Accede a la página principal: <code>gestion_permisos.php</code></li>';
        echo '<li>Abre las DevTools del navegador (F12) para ver los logs</li>';
        echo '<li>Verifica que no haya errores en la consola</li>';
        echo '<li>Prueba a crear roles, permisos y asignarlos a usuarios</li>';
        echo '</ol>';
    }
    
    echo '</div>';
    ?>
    
    <div class="test-section">
        <h2>📝 Información Adicional</h2>
        <p>Si después de corregir los errores el sistema sigue sin funcionar:</p>
        <ul>
            <li>Revisa los logs del servidor web (Apache/Nginx)</li>
            <li>Revisa los logs de PHP</li>
            <li>Verifica los permisos de archivos y carpetas</li>
            <li>Asegúrate de que el servidor web pueda escribir en la carpeta de sesiones</li>
        </ul>
        
        <p><strong>Archivos corregidos disponibles:</strong></p>
        <ul>
            <li><code>gestion_permisos_fixed.js</code> - JavaScript corregido</li>
            <li><code>permisos_api_fixed.php</code> - API corregida</li>
            <li><code>GUIA_SOLUCION_PROBLEMAS.md</code> - Guía detallada</li>
        </ul>
    </div>
    
    <div style="text-align: center; margin-top: 40px; padding: 20px; background: #e7f3ff; border-radius: 5px;">
        <p style="margin: 0; color: #666;">
            <strong>Diagnóstico generado:</strong> <?php echo date('Y-m-d H:i:s'); ?>
        </p>
    </div>
</body>
</html>