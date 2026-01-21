<?php
require_once('bd.php');
require_once('clssPermisos.php');

header('Content-Type: application/json');

// Procesar POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if ($data['action'] == 'guardar') {
        $id_rol = (int)$data['id_rol'];
        $permisos = $data['permisos'];
        
        // Convertir a JSON
        $permisos_json = json_encode($permisos, JSON_UNESCAPED_UNICODE);
        
        // Actualizar en la base de datos
        $resultado = Permisos::actualizarPermisos($id_rol, $permisos_json);
        
        if ($resultado) {
            echo json_encode([
                'success' => true, 
                'message' => 'Permisos actualizados correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Error al actualizar permisos'
            ]);
        }
    }
    
    if (isset($data['action']) && $data['action'] == 'crear_rol') {
        $sucursal_id = (int)$data['sucursal_id'];
        $nombre_rol = $data['nombre_rol'];
        $descripcion = $data['descripcion'];
        $permisos = isset($data['permisos']) ? $data['permisos'] : array();
        
        $permisos_json = json_encode($permisos, JSON_UNESCAPED_UNICODE);
        
        $id_nuevo_rol = Permisos::crearRol($sucursal_id, $nombre_rol, $descripcion, $permisos_json);
        
        if ($id_nuevo_rol) {
            echo json_encode([
                'success' => true, 
                'message' => 'Rol creado correctamente',
                'id_rol' => $id_nuevo_rol
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Error al crear rol'
            ]);
        }
    }
}

// Procesar GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['action']) && $_GET['action'] == 'obtener_permisos') {
        $id_rol = (int)$_GET['id_rol'];
        $permisos = Permisos::obtenerPermisosRol($id_rol);
        
        echo json_encode($permisos);
    }
    
    if (isset($_GET['action']) && $_GET['action'] == 'obtener_roles_sucursal') {
        $sucursal_id = (int)$_GET['sucursal_id'];
        $roles = Permisos::obtenerRolesPorSucursal($sucursal_id);
        
        echo json_encode($roles);
    }
}
?>