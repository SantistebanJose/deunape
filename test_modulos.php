<?php
/**
 * Script de prueba para verificar que los módulos y submódulos estén en la BD
 * Ejecutar desde el navegador: http://localhost/tu_proyecto/test_modulos.php
 * ELIMINAR después de verificar
 */

include('logica/bd.php');

echo "<h1>🔍 Verificación de Módulos y Submódulos</h1>";
echo "<style>
    body { font-family: Arial; padding: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background-color: #4CAF50; color: white; }
    tr:nth-child(even) { background-color: #f2f2f2; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
</style>";

try {
    // 1. Verificar tabla roles
    echo "<h2>1️⃣ Tabla ROLES</h2>";
    $queryRoles = "SELECT COUNT(*) as total FROM roles";
    $stmt = $conectar->query($queryRoles);
    $resultRoles = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($resultRoles['total'] > 0) {
        echo "<p class='success'>✅ Tabla 'roles' existe y tiene {$resultRoles['total']} registro(s)</p>";
        
        // Listar roles
        $queryListRoles = "SELECT * FROM roles ORDER BY id_rol";
        $stmt = $conectar->query($queryListRoles);
        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Estado</th><th>Permisos (primeros 100 chars)</th></tr>";
        foreach ($roles as $rol) {
            $permisosPreview = substr($rol['permisos'], 0, 100) . '...';
            echo "<tr>";
            echo "<td>{$rol['id_rol']}</td>";
            echo "<td>{$rol['nombre_rol']}</td>";
            echo "<td>{$rol['descripcion']}</td>";
            echo "<td>" . ($rol['estado'] == 1 ? '✅ Activo' : '❌ Inactivo') . "</td>";
            echo "<td><small>{$permisosPreview}</small></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>❌ La tabla 'roles' existe pero está vacía. Ejecuta el script SQL de inserción.</p>";
    }
    
    // 2. Verificar tabla modulos
    echo "<h2>2️⃣ Tabla MODULOS</h2>";
    $queryModulos = "SELECT COUNT(*) as total FROM modulos WHERE estado = 1";
    $stmt = $conectar->query($queryModulos);
    $resultModulos = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($resultModulos['total'] > 0) {
        echo "<p class='success'>✅ Tabla 'modulos' tiene {$resultModulos['total']} módulo(s) activo(s)</p>";
        
        // Listar módulos
        $queryListModulos = "SELECT * FROM modulos WHERE estado = 1 ORDER BY orden";
        $stmt = $conectar->query($queryListModulos);
        $modulos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Identificador</th><th>Icono</th><th>Orden</th></tr>";
        foreach ($modulos as $modulo) {
            echo "<tr>";
            echo "<td>{$modulo['id_modulo']}</td>";
            echo "<td>{$modulo['nombre_modulo']}</td>";
            echo "<td>{$modulo['identificador']}</td>";
            echo "<td><i class='{$modulo['icono']}'></i> {$modulo['icono']}</td>";
            echo "<td>{$modulo['orden']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>❌ La tabla 'modulos' está vacía o no existe. Ejecuta el script SQL.</p>";
        echo "<p>Script a ejecutar: <strong>insert_modulos.sql</strong></p>";
    }
    
    // 3. Verificar tabla submodulos
    echo "<h2>3️⃣ Tabla SUBMODULOS</h2>";
    $querySubmodulos = "SELECT COUNT(*) as total FROM submodulos WHERE estado = 1";
    $stmt = $conectar->query($querySubmodulos);
    $resultSubmodulos = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($resultSubmodulos['total'] > 0) {
        echo "<p class='success'>✅ Tabla 'submodulos' tiene {$resultSubmodulos['total']} submódulo(s) activo(s)</p>";
        
        // Listar submódulos por módulo
        $queryListSubmodulos = "
            SELECT s.*, m.nombre_modulo 
            FROM submodulos s 
            JOIN modulos m ON s.id_modulo = m.id_modulo 
            WHERE s.estado = 1 
            ORDER BY m.orden, s.orden
        ";
        $stmt = $conectar->query($queryListSubmodulos);
        $submodulos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table>";
        echo "<tr><th>ID</th><th>Módulo</th><th>Nombre</th><th>Identificador</th><th>URL</th></tr>";
        foreach ($submodulos as $sub) {
            echo "<tr>";
            echo "<td>{$sub['id_submodulo']}</td>";
            echo "<td>{$sub['nombre_modulo']}</td>";
            echo "<td>{$sub['nombre_submodulo']}</td>";
            echo "<td>{$sub['identificador']}</td>";
            echo "<td><small>{$sub['url']}</small></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>❌ La tabla 'submodulos' está vacía. Ejecuta el script SQL.</p>";
    }
    
    // 4. Verificar usuarios con rol
    echo "<h2>4️⃣ USUARIOS con ROL asignado</h2>";
    $queryUsuarios = "
        SELECT u.id, u.username, u.id_rol, r.nombre_rol, p.nombres, p.apellidos
        FROM usuario u
        LEFT JOIN roles r ON u.id_rol = r.id_rol
        LEFT JOIN persona p ON u.persona_id = p.id
        WHERE u.deleted_at IS NULL
        ORDER BY u.id
    ";
    $stmt = $conectar->query($queryUsuarios);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($usuarios) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Username</th><th>Nombre</th><th>Rol Asignado</th><th>Estado</th></tr>";
        foreach ($usuarios as $user) {
            $rolAsignado = $user['id_rol'] ? "<span class='success'>{$user['nombre_rol']}</span>" : "<span class='error'>❌ SIN ROL</span>";
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['username']}</td>";
            echo "<td>{$user['nombres']} {$user['apellidos']}</td>";
            echo "<td>{$rolAsignado}</td>";
            echo "<td>" . ($user['id_rol'] ? '✅' : '⚠️') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Contar usuarios sin rol
        $sinRol = 0;
        foreach ($usuarios as $user) {
            if (!$user['id_rol']) $sinRol++;
        }
        
        if ($sinRol > 0) {
            echo "<p class='warning'>⚠️ Hay {$sinRol} usuario(s) sin rol asignado. Ejecuta:</p>";
            echo "<pre>UPDATE usuario SET id_rol = 1 WHERE id = TU_ID_USUARIO;</pre>";
        } else {
            echo "<p class='success'>✅ Todos los usuarios tienen rol asignado</p>";
        }
    } else {
        echo "<p class='warning'>⚠️ No hay usuarios en la tabla</p>";
    }
    
    // 5. Test de la función del backend
    echo "<h2>5️⃣ TEST de Función Backend</h2>";
    include_once('logica/clssRoles.php');
    
    $testModulos = fnListarModulosConSubmodulos();
    
    if (count($testModulos) > 0) {
        echo "<p class='success'>✅ La función fnListarModulosConSubmodulos() funciona correctamente</p>";
        echo "<p>Módulos obtenidos: " . count($testModulos) . "</p>";
        echo "<pre style='background: #f4f4f4; padding: 10px; border-radius: 5px;'>";
        print_r($testModulos[0]); // Mostrar el primer módulo como ejemplo
        echo "</pre>";
    } else {
        echo "<p class='error'>❌ La función fnListarModulosConSubmodulos() no devuelve datos</p>";
    }
    
    // Resumen final
    echo "<hr>";
    echo "<h2>📊 RESUMEN</h2>";
    $problemas = [];
    
    if ($resultRoles['total'] == 0) $problemas[] = "No hay roles creados";
    if ($resultModulos['total'] == 0) $problemas[] = "No hay módulos creados";
    if ($resultSubmodulos['total'] == 0) $problemas[] = "No hay submódulos creados";
    if ($sinRol > 0) $problemas[] = "{$sinRol} usuario(s) sin rol asignado";
    if (count($testModulos) == 0) $problemas[] = "La función backend no funciona";
    
    if (count($problemas) == 0) {
        echo "<p class='success' style='font-size: 20px;'>✅ TODO ESTÁ CORRECTO - El sistema de roles debería funcionar</p>";
        echo "<p><a href='roles.php' style='padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Ir a Gestión de Roles</a></p>";
    } else {
        echo "<p class='error' style='font-size: 20px;'>❌ HAY PROBLEMAS QUE CORREGIR:</p>";
        echo "<ul>";
        foreach ($problemas as $problema) {
            echo "<li class='error'>{$problema}</li>";
        }
        echo "</ul>";
        echo "<p><strong>SOLUCIÓN:</strong> Ejecuta el archivo <code>insert_modulos.sql</code> en tu base de datos PostgreSQL</p>";
    }
    
    echo "<hr>";
    echo "<p><strong>⚠️ IMPORTANTE:</strong> Elimina este archivo (test_modulos.php) después de verificar</p>";
    
} catch (PDOException $e) {
    echo "<p class='error'>❌ ERROR DE BASE DE DATOS: " . $e->getMessage() . "</p>";
    echo "<p>Verifica que:</p>";
    echo "<ul>";
    echo "<li>Las tablas existan en la base de datos</li>";
    echo "<li>La conexión en bd.php sea correcta</li>";
    echo "<li>El usuario de BD tenga permisos</li>";
    echo "</ul>";
}
?>