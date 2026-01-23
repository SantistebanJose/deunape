<?php
/**
 * diagnostico_menu.php
 * Diagnóstico específico para menús faltantes
 */

session_start();
require_once("logica/bd.php");
require_once("MenuManager.php");


echo "<h1>🔍 Diagnóstico de Menús</h1>";
echo "<style>
    body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
    .success { color: #4ec9b0; font-weight: bold; }
    .error { color: #f48771; font-weight: bold; }
    .warning { color: #dcdcaa; }
    .box { background: #2d2d30; padding: 15px; margin: 15px 0; border-left: 3px solid #569cd6; }
    pre { background: #1e1e1e; padding: 10px; overflow-x: auto; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #3e3e42; padding: 8px; text-align: left; }
    th { background: #1e1e1e; }
</style>";

if (!isset($_SESSION['id'])) {
    die("Inicia sesión primero");
}

$rolName = $_SESSION['nombre_rol'] ?? $_SESSION['rol'] ?? 'DESCONOCIDO';

echo "<div class='box'>";
echo "<h2>📋 Sesión Actual</h2>";
echo "<strong>Rol:</strong> <span class='warning'>$rolName</span><br>";
echo "</div>";

// ==========================================
// 1. VERIFICAR MENÚ moduloAdmin
// ==========================================
echo "<h2>1️⃣ Verificar Menú 'moduloAdmin'</h2>";
echo "<div class='box'>";

$query = "SELECT * FROM menus WHERE codigo = 'moduloAdmin'";
$menu = executeQuerymenumanager($query);

if (!empty($menu)) {
    $menuData = $menu[0];
    echo "<span class='success'>✅ Menú 'moduloAdmin' existe</span><br>";
    echo "<strong>ID:</strong> {$menuData['id']}<br>";
    echo "<strong>Título:</strong> {$menuData['titulo']}<br>";
    echo "<strong>Activo:</strong> " . ($menuData['activo'] ? 'Sí' : 'No') . "<br><br>";
    
    $menuId = $menuData['id'];
    
    // ¿Está asignado al rol?
    $query2 = "
        SELECT COUNT(*) as tiene 
        FROM menu_rol mr
        INNER JOIN roles r ON mr.rol_id = r.id_rol
        WHERE mr.menu_id = :menu_id AND r.nombre = :rol_nombre
    ";
    
    $asignado = executeQuerymenumanager($query2, [
        'menu_id' => $menuId,
        'rol_nombre' => $rolName
    ]);
    
    if (!empty($asignado) && $asignado[0]['tiene'] > 0) {
        echo "<span class='success'>✅ Menú ASIGNADO al rol '$rolName'</span><br>";
    } else {
        echo "<span class='error'>❌ Menú NO asignado al rol '$rolName'</span><br>";
        echo "<span class='warning'>💡 Ejecuta esta solución:</span><br>";
        echo "<pre>INSERT INTO menu_rol (menu_id, rol_id)
SELECT 
    {$menuId},
    (SELECT id_rol FROM roles WHERE nombre = '$rolName');</pre>";
    }
    
} else {
    echo "<span class='error'>❌ Menú 'moduloAdmin' NO existe</span><br>";
}
echo "</div>";

// ==========================================
// 2. ITEMS DEL MENÚ moduloAdmin
// ==========================================
echo "<h2>2️⃣ Items del Menú 'moduloAdmin'</h2>";
echo "<div class='box'>";

if (!empty($menu)) {
    $query = "
        SELECT 
            mi.id,
            mi.titulo,
            mi.link,
            mi.permiso_id,
            p.codigo as permiso_codigo,
            p.nombre as permiso_nombre,
            mi.activo
        FROM menu_items mi
        LEFT JOIN permisos p ON mi.permiso_id = p.id
        WHERE mi.menu_id = :menu_id
        ORDER BY mi.orden
    ";
    
    $items = executeQuerymenumanager($query, ['menu_id' => $menuId]);
    
    if (!empty($items)) {
        echo "<span class='success'>✅ " . count($items) . " items encontrados</span><br><br>";
        
        echo "<table>";
        echo "<tr><th>Título</th><th>Link</th><th>Permiso</th><th>Activo</th></tr>";
        foreach ($items as $item) {
            $activo = $item['activo'] ? '✅' : '❌';
            echo "<tr>";
            echo "<td>{$item['titulo']}</td>";
            echo "<td>{$item['link']}</td>";
            echo "<td>{$item['permiso_codigo']}</td>";
            echo "<td>$activo</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<span class='error'>❌ No hay items en este menú</span><br>";
    }
}
echo "</div>";

// ==========================================
// 3. VERIFICAR PERMISOS DEL ROL
// ==========================================
echo "<h2>3️⃣ Permisos del Rol</h2>";
echo "<div class='box'>";

$query = "
    SELECT p.codigo, p.nombre
    FROM permisos p
    INNER JOIN rol_permiso rp ON p.id = rp.permiso_id
    INNER JOIN roles r ON rp.rol_id = r.id_rol
    WHERE r.nombre = :rol_nombre AND p.codigo LIKE 'admin_%'
    ORDER BY p.codigo
";

$permisos = executeQuerymenumanager($query, ['rol_nombre' => $rolName]);

if (!empty($permisos)) {
    echo "<span class='success'>✅ Permisos 'admin_*': " . count($permisos) . "</span><br><br>";
    
    echo "<table>";
    echo "<tr><th>Código</th><th>Nombre</th></tr>";
    foreach ($permisos as $p) {
        echo "<tr>";
        echo "<td>{$p['codigo']}</td>";
        echo "<td>{$p['nombre']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<span class='error'>❌ No tiene permisos admin_*</span><br>";
}
echo "</div>";

// ==========================================
// 4. QUERY EXACTA QUE USA MenuManager
// ==========================================
echo "<h2>4️⃣ Query Exacta de MenuManager</h2>";
echo "<div class='box'>";

$query = "
    SELECT DISTINCT m.id, m.codigo, m.titulo, m.icono, m.orden
    FROM menus m
    INNER JOIN menu_rol mr ON m.id = mr.menu_id
    INNER JOIN roles r ON mr.rol_id = r.id_rol
    WHERE r.nombre = :nombre_rol AND m.activo = true AND r.activo = true
    ORDER BY m.orden ASC
";

echo "<strong>Query:</strong><br>";
echo "<pre>" . htmlspecialchars($query) . "</pre>";

$menusDelRol = executeQuerymenumanager($query, ['nombre_rol' => $rolName]);

echo "<strong>Resultado:</strong><br>";
if (!empty($menusDelRol)) {
    echo "<span class='success'>✅ " . count($menusDelRol) . " menús encontrados</span><br><br>";
    
    echo "<table>";
    echo "<tr><th>Código</th><th>Título</th></tr>";
    foreach ($menusDelRol as $m) {
        $highlight = $m['codigo'] === 'moduloAdmin' ? 'style="background:#2a4a2a;"' : '';
        echo "<tr $highlight>";
        echo "<td>{$m['codigo']}</td>";
        echo "<td>{$m['titulo']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    $tieneAdmin = false;
    foreach ($menusDelRol as $m) {
        if ($m['codigo'] === 'moduloAdmin') {
            $tieneAdmin = true;
            break;
        }
    }
    
    if (!$tieneAdmin) {
        echo "<br><span class='error'>❌ 'moduloAdmin' NO está en la lista</span><br>";
        echo "<span class='error'>🔥 ESTE ES EL PROBLEMA</span><br>";
    } else {
        echo "<br><span class='success'>✅ 'moduloAdmin' está en la lista</span><br>";
    }
} else {
    echo "<span class='error'>❌ No se encontraron menús</span><br>";
}
echo "</div>";

// ==========================================
// 5. SOLUCIÓN RÁPIDA
// ==========================================
echo "<h2>5️⃣ Solución Rápida</h2>";
echo "<div class='box'>";

if (!empty($menu)) {
    $menuId = $menu[0]['id'];
    
    echo "<p>Ejecuta este SQL para asignar el menú al rol:</p>";
    echo "<pre>-- Asignar menú 'moduloAdmin' al rol '$rolName'
INSERT INTO menu_rol (menu_id, rol_id)
SELECT 
    $menuId,
    (SELECT id_rol FROM roles WHERE nombre = '$rolName')
ON CONFLICT DO NOTHING;</pre>";

    echo "<p>O ejecuta este SQL que funciona siempre:</p>";
    echo "<pre>-- Asignar moduloAdmin al ADMINISTRADOR
INSERT INTO menu_rol (menu_id, rol_id)
SELECT m.id, r.id_rol 
FROM menus m, roles r
WHERE m.codigo = 'moduloAdmin' AND r.nombre = '$rolName';</pre>";
}
echo "</div>";

echo "<br><hr>";
echo "<a href='admin_roles_permisos.php' style='color:#569cd6;'>← Volver</a>";