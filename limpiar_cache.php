<?php
session_start();

// Limpiar caché de permisos
foreach ($_SESSION as $key => $value) {
    if (strpos($key, 'permissions_') === 0 || 
        strpos($key, 'filtered_menus_') === 0 || 
        strpos($key, 'quick_access_') === 0) {
        unset($_SESSION[$key]);
    }
}

echo "✅ Caché limpiado<br><br>";
echo "<a href='diagnostico3.php'>Volver al diagnóstico</a> | ";
echo "<a href='test_sistema.php'>Ir a test</a>";
?>