<?php
/**
 * api/roles_api.php
 * API para gestionar roles y permisos
 */

session_start();
require_once("logica/bd.php");
require_once("MenuManager.php");

header('Content-Type: application/json');

// Verificar autenticación
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

// Verificar permiso de administración
$menuManager = new MenuManager($_SESSION['nombre_rol'] ?? $_SESSION['rol']);
if (!$menuManager->hasPermission('admin_roles')) {
    echo json_encode(['success' => false, 'message' => 'Sin permisos']);
    exit;
}

function executeQuery($query, $params = []) {
    global $conectar;
    try {
        $stmt = $conectar->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en API: " . $e->getMessage());
        return false;
    }
}

function executeUpdate($query, $params = []) {
    global $conectar;
    try {
        $stmt = $conectar->prepare($query);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Error en update: " . $e->getMessage());
        return false;
    }
}

$action = $_POST['action'] ?? '';

switch ($action) {
    
    // ==========================================
    // Obtener permisos de un rol
    // ==========================================
    case 'getPermisosRol':
        $rolId = $_POST['rol_id'] ?? 0;
        
        $query = "SELECT permiso_id FROM rol_permiso WHERE rol_id = :rol_id";
        $result = executeQuery($query, ['rol_id' => $rolId]);
        
        if ($result !== false) {
            $permisos = array_column($result, 'permiso_id');
            echo json_encode([
                'success' => true,
                'permisos' => $permisos
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al cargar permisos']);
        }
        break;
    
    // ==========================================
    // Asignar permiso a rol
    // ==========================================
    case 'asignarPermiso':
        $rolId = $_POST['rol_id'] ?? 0;
        $permisoId = $_POST['permiso_id'] ?? 0;
        
        $query = "
            INSERT INTO rol_permiso (rol_id, permiso_id) 
            VALUES (:rol_id, :permiso_id)
        ";
        
        $result = executeUpdate($query, [
            'rol_id' => $rolId,
            'permiso_id' => $permisoId
        ]);
        
        if ($result) {
            // Limpiar cache
            MenuManager::clearAllCache();
            
            echo json_encode([
                'success' => true,
                'message' => 'Permiso asignado correctamente'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al asignar permiso']);
        }
        break;
    
    // ==========================================
    // Quitar permiso de rol
    // ==========================================
    case 'quitarPermiso':
        $rolId = $_POST['rol_id'] ?? 0;
        $permisoId = $_POST['permiso_id'] ?? 0;
        
        $query = "
            DELETE FROM rol_permiso 
            WHERE rol_id = :rol_id AND permiso_id = :permiso_id
        ";
        
        $result = executeUpdate($query, [
            'rol_id' => $rolId,
            'permiso_id' => $permisoId
        ]);
        
        if ($result) {
            // Limpiar cache
            MenuManager::clearAllCache();
            
            echo json_encode([
                'success' => true,
                'message' => 'Permiso removido correctamente'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al remover permiso']);
        }
        break;
    
    // ==========================================
    // Asignar todos los permisos a un rol
    // ==========================================
    case 'asignarTodos':
        $rolId = $_POST['rol_id'] ?? 0;
        
        // Primero limpiamos los permisos existentes
        $query1 = "DELETE FROM rol_permiso WHERE rol_id = :rol_id";
        executeUpdate($query1, ['rol_id' => $rolId]);
        
        // Luego asignamos todos
        $query2 = "
            INSERT INTO rol_permiso (rol_id, permiso_id)
            SELECT :rol_id, id FROM permisos WHERE activo = true
        ";
        
        $result = executeUpdate($query2, ['rol_id' => $rolId]);
        
        if ($result) {
            MenuManager::clearAllCache();
            
            echo json_encode([
                'success' => true,
                'message' => 'Todos los permisos asignados correctamente'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al asignar permisos']);
        }
        break;
    
    // ==========================================
    // Quitar todos los permisos de un rol
    // ==========================================
    case 'quitarTodos':
        $rolId = $_POST['rol_id'] ?? 0;
        
        $query = "DELETE FROM rol_permiso WHERE rol_id = :rol_id";
        $result = executeUpdate($query, ['rol_id' => $rolId]);
        
        if ($result !== false) {
            MenuManager::clearAllCache();
            
            echo json_encode([
                'success' => true,
                'message' => 'Todos los permisos removidos correctamente'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al remover permisos']);
        }
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}