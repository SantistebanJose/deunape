<?php
session_start();
include("logica/bd.php");

function executeQuery(string $query, array $params = []): array
{
    global $conectar;
    try {
        $orden = $conectar->prepare($query);
        $orden->execute($params);
        $datos = $orden->fetchAll(PDO::FETCH_ASSOC);
        $orden->closeCursor();
        return $datos;
    } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
        return [];
    }
}

echo "<h1>🔍 Diagnóstico del Sistema de Permisos</h1>";
echo "<style>
    body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
    .success { color: #4ec9b0; }
    .error { color: #f48771; }
    .warning { color: #dcdcaa; }
    h2 { color: #569cd6; border-bottom: 2px solid #569cd6; padding-bottom: 5px; }
    pre { background: #2d2d30; padding: 10px; border-left: 3px solid #007acc; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #3e3e42; padding: 8px; text-align: left; }
    th { background: #2d2d30; }
</style>";

// ============================================
// 1. VERIFICAR SESIÓN
// ============================================
echo "<h2>1️⃣ Información de Sesión</h2>";
if (isset($_SESSION['id'])) {
    echo "<span class='success'>✓ Sesión activa</span><br>";
    echo "<strong>ID Usuario:</strong> " . $_SESSION['id'] . "<br>";
    echo "<strong>Usuario:</strong> " . ($_SESSION['usuario'] ?? 'No definido') . "<br>";
    echo "<strong>Nombre:</strong> " . ($_SESSION['nombre'] ?? 'No definido') . "<br>";
    echo "<strong>ID Rol:</strong> " . ($_SESSION['id_rol'] ?? 'No definido') . "<br>";
    echo "<strong>Nombre Rol:</strong> " . ($_SESSION['nombre_rol'] ?? 'No definido') . "<br>";
    echo "<strong>Rol (alternativo):</strong> " . ($_SESSION['rol'] ?? 'No definido') . "<br>";
} else {
    echo "<span class='error'>✗ No hay sesión activa</span><br>";
    die("Por favor inicia sesión primero");
}

//$userId = $_SESSION['id'];
$userId = $_SESSION['id'];
$rolName = $_SESSION['nombre_rol'] ?? $_SESSION['rol'] ?? 'DESCONOCIDO';

// ============================================
// 2. VERIFICAR USUARIO EN BD
// ============================================
echo "<hr>";
echo json_encode($_SESSION);
echo "<hr>";
echo "<h2>2️⃣ Verificación de Usuario en BD</h2>";
try {
    $query = "
        SELECT u.id, u.username, u.id_rol, r.nombre as rol_nombre, r.id_rol as rol_id
        FROM usuario u
        LEFT JOIN roles r ON u.id_rol = r.id_rol
        WHERE u.id = :id_entrada
    ";

    $user = executeQuery($query,params:["id_entrada" => $userId]);
    
    echo  "aquiiiiii";
    echo "<hr>";
    echo json_encode($user);

    
    echo   "<hr>";
    echo "<br>";
    echo "ctmre";
    echo $user[0]["rol_nombre"];
    echo   "<hr>";

    if ($user) {
        echo "<span class='success'>✓ Usuario encontrado</span><br>";
        echo "<pre>";
        print_r($user);
        echo "</pre>";
        
        if ($user[0]['rol_nombre']) {
            echo "<span class='success'>✓ Rol asignado correctamente: " . $user[0]['rol_nombre'] . "</span><br>";
        } else {
            echo "<span class='error'>✗ El usuario NO tiene un rol asignado en la BD</span><br>";
        }
    } else {
        echo "<span class='error'>✗ Usuario no encontrado en BD</span><br>";
    }
} catch (PDOException $e) {
    echo "<span class='error'>✗ Error: " . $e->getMessage() . "</span><br>";
}

// ============================================
// 3. VERIFICAR ROLES EN BD
// ============================================
echo "<h2>3️⃣ Roles Disponibles en BD</h2>";
try {
    $query = "SELECT id_rol, nombre, descripcion, activo FROM roles ORDER BY id_rol";
    $stmt = $conectar->prepare($query);
    $stmt->execute();
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($roles) {
        echo "<span class='success'>✓ " . count($roles) . " roles encontrados</span><br>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Activo</th></tr>";
        foreach ($roles as $role) {
            $highlight = ($role['nombre'] == $rolName) ? "style='background:#2a4a2a;'" : "";
            echo "<tr $highlight>";
            echo "<td>" . $role['id_rol'] . "</td>";
            echo "<td>" . $role['nombre'] . "</td>";
            echo "<td>" . $role['descripcion'] . "</td>";
            echo "<td>" . ($role['activo'] ? 'Sí' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<span class='error'>✗ No hay roles en la BD</span><br>";
    }
} catch (PDOException $e) {
    echo "<span class='error'>✗ Error: " . $e->getMessage() . "</span><br>";
}

// ============================================
// 4. VERIFICAR PERMISOS EN BD
// ============================================
echo "<h2>4️⃣ Permisos Totales en BD</h2>";
try {
    $query = "SELECT COUNT(*) as total FROM permisos WHERE activo = true";
    $stmt = $conectar->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<span class='success'>✓ Total de permisos activos: " . $result['total'] . "</span><br>";
    
    // Mostrar algunos permisos
    $query = "SELECT id, codigo, nombre, categoria FROM permisos WHERE activo = true LIMIT 10";
    $stmt = $conectar->prepare($query);
    $stmt->execute();
    $permisos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table>";
    echo "<tr><th>ID</th><th>Código</th><th>Nombre</th><th>Categoría</th></tr>";
    foreach ($permisos as $permiso) {
        echo "<tr>";
        echo "<td>" . $permiso['id'] . "</td>";
        echo "<td>" . $permiso['codigo'] . "</td>";
        echo "<td>" . $permiso['nombre'] . "</td>";
        echo "<td>" . $permiso['categoria'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<em>... y " . ($result['total'] - 10) . " más</em><br>";
    
} catch (PDOException $e) {
    echo "<span class='error'>✗ Error: " . $e->getMessage() . "</span><br>";
}

// ============================================
// 5. VERIFICAR ROL_PERMISO
// ============================================
echo "<h2>5️⃣ Permisos Asignados a Roles</h2>";
try {
    $query = "
        SELECT r.nombre as rol, COUNT(rp.permiso_id) as total_permisos
        FROM roles r
        LEFT JOIN rol_permiso rp ON r.id_rol = rp.rol_id
        WHERE r.activo = true
        GROUP BY r.id_rol, r.nombre
        ORDER BY r.id_rol
    ";
    
    $stmt = $conectar->prepare($query);
    $stmt->execute();
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table>";
    echo "<tr><th>Rol</th><th>Permisos Asignados</th></tr>";
    foreach ($stats as $stat) {
        $highlight = ($stat['rol'] == $rolName) ? "style='background:#2a4a2a;'" : "";
        echo "<tr $highlight>";
        echo "<td>" . $stat['rol'] . "</td>";
        echo "<td>" . $stat['total_permisos'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "<span class='error'>✗ Error: " . $e->getMessage() . "</span><br>";
}

// ============================================
// 6. QUERY EXACTA DEL MENUMANAGER
// ============================================
echo "<h2>6️⃣ Query Exacta que Usa MenuManager</h2>";
try {
    echo "<strong>Buscando permisos para rol:</strong> <span class='warning'>$rolName</span><br>";
    
    $query = "
        SELECT DISTINCT p.codigo
        FROM permisos p
        INNER JOIN rol_permiso rp ON p.id = rp.permiso_id
        INNER JOIN roles r ON rp.rol_id = r.id_rol
        WHERE r.nombre = :nombre_entrada AND p.activo = true AND r.activo = true
    ";
    

    echo "<pre>$query</pre>";
    echo "<strong>Parámetro:</strong> <span class='warning'>$rolName</span><br><br>";
    
    
    $permisos = executeQuery($query,params:["nombre_entrada" =>$rolName])[0];

    if ($permisos) {
        echo "<span class='success'>✓ " . count($permisos) . " permisos encontrados</span><br>";
        echo "<pre>";
        print_r($permisos);
        echo "</pre>";
    } else {
        echo "<span class='error'>✗ NO se encontraron permisos con esta query</span><br>";
        
        // Diagnóstico adicional
        echo "<h3>🔍 Diagnóstico Detallado:</h3>";
        
        // ¿Existe el rol?
        $query2 = "SELECT id_rol, nombre FROM roles WHERE nombre = $1";
        $stmt2 = $conectar->prepare($query2);
        $stmt2->execute([$rolName]);
        $rolExists = $stmt2->fetch(PDO::FETCH_ASSOC);
        
        if ($rolExists) {
            echo "<span class='success'>✓ El rol '$rolName' existe (ID: " . $rolExists['id_rol'] . ")</span><br>";
            
            // ¿Tiene permisos asignados?
            $query3 = "SELECT COUNT(*) as total FROM rol_permiso WHERE rol_id = $1";
            $stmt3 = $conectar->prepare($query3);
            $stmt3->execute([$rolExists['id_rol']]);
            $permCount = $stmt3->fetch(PDO::FETCH_ASSOC);
            
            echo "<span class='warning'>→ Permisos asignados directamente al rol_id " . $rolExists['id_rol'] . ": " . $permCount['total'] . "</span><br>";
            
            if ($permCount['total'] == 0) {
                echo "<span class='error'>✗ PROBLEMA ENCONTRADO: El rol existe pero NO tiene permisos asignados en rol_permiso</span><br>";
                echo "<span class='warning'>💡 Solución: Ejecutar los INSERTs de permisos</span><br>";
            }
            
        } else {
            echo "<span class='error'>✗ El rol '$rolName' NO existe en la tabla roles</span><br>";
            echo "<span class='warning'>💡 Verifica el nombre exacto del rol (mayúsculas/minúsculas)</span><br>";
        }
    }
    
} catch (PDOException $e) {
    echo "<span class='error'>✗ Error: " . $e->getMessage() . "</span><br>";
}

// ============================================
// 7. VERIFICAR MENÚS
// ============================================
echo "<h2>7️⃣ Menús Asignados al Rol</h2>";
try {
    $query = "
        SELECT m.codigo, m.titulo, COUNT(mi.id) as items
        FROM menus m
        INNER JOIN menu_rol mr ON m.id = mr.menu_id
        INNER JOIN roles r ON mr.rol_id = r.id_rol
        LEFT JOIN menu_items mi ON m.id = mi.menu_id AND mi.activo = true
        WHERE r.nombre = :nombre_eeee AND m.activo = true
        GROUP BY m.id, m.codigo, m.titulo
        ORDER BY m.orden
    ";

    $menus = executeQuery($query,params:["nombre_eeee"=>$rolName]);
    if ($menus[0]) {
        echo "<span class='success'>✓ " . count($menus) . " menús encontrados</span><br>";
        echo "<table>";
        echo "<tr><th>Código</th><th>Título</th><th>Items</th></tr>";
        foreach ($menus as $menu) {
            echo "<tr>";
            echo "<td>" . $menu['codigo'] . "</td>";
            echo "<td>" . $menu['titulo'] . "</td>";
            echo "<td>" . $menu['items'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<span class='error'>✗ No hay menús asignados a este rol</span><br>";
    }
    
} catch (PDOException $e) {
    echo "<span class='error'>✗ Error: " . $e->getMessage() . "</span><br>";
}

// ============================================
// 8. CACHÉ DE SESIÓN
// ============================================
echo "<h2>8️⃣ Caché en Sesión</h2>";
$cacheKeys = ['permissions_', 'filtered_menus_', 'quick_access_'];
$foundCache = false;

foreach ($_SESSION as $key => $value) {
    foreach ($cacheKeys as $prefix) {
        if (strpos($key, $prefix) === 0) {
            echo "<strong>$key:</strong><br>";
            echo "<pre>";
            print_r($value);
            echo "</pre>";
            $foundCache = true;
        }
    }
}

if (!$foundCache) {
    echo "<span class='warning'>⚠ No hay caché en sesión</span><br>";
}

echo "<br><a href='limpiar_cache.php' style='color:#4ec9b0;'>🧹 Limpiar Caché</a> | ";
echo "<a href='test_sistema.php' style='color:#4ec9b0;'>📊 Ver Test Sistema</a>";