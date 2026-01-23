<?php
/**
 * api/paginas_api.php
 * API para registrar y gestionar páginas del sistema
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

$menuManager = new MenuManager($_SESSION['nombre_rol'] ?? $_SESSION['rol']);
if (!$menuManager->hasPermission('admin_roles')) {
    echo json_encode(['success' => false, 'message' => 'Sin permisos']);
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    
    // ==========================================
    // Registrar página específica (con permisos)
    // ==========================================
    case 'registrarPaginaEspecifica':
        $nombreArchivo = $_POST['nombre_archivo'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $permisoId = !empty($_POST['permiso_id']) ? $_POST['permiso_id'] : null;
        $roles = $_POST['roles'] ?? [];
        
        // Validar
        if (empty($nombreArchivo) || empty($descripcion) || empty($roles)) {
            echo json_encode(['success' => false, 'message' => 'Campos incompletos']);
            exit;
        }
        
        try {
            // Insertar página
            $query = "
                INSERT INTO paginas_especificas (nombre_archivo, descripcion, permiso_id, activo)
                VALUES (:nombre_archivo, :descripcion, :permiso_id, true)
            ";
            
            $stmt = $conectar->prepare($query);
            $stmt->execute([
                'nombre_archivo' => $nombreArchivo,
                'descripcion' => $descripcion,
                'permiso_id' => $permisoId
            ]);
            
            $paginaId = $conectar->lastInsertId();
            
            // Asignar roles
            $query2 = "INSERT INTO pagina_rol (pagina_id, rol_id) VALUES (:pagina_id, :rol_id)";
            $stmt2 = $conectar->prepare($query2);
            
            foreach ($roles as $rolId) {
                $stmt2->execute([
                    'pagina_id' => $paginaId,
                    'rol_id' => $rolId
                ]);
            }
            
            // Limpiar cache
            MenuManager::clearAllCache();
            
            echo json_encode([
                'success' => true,
                'message' => 'Página registrada correctamente'
            ]);
            
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        break;
    
    // ==========================================
    // Registrar página pública
    // ==========================================
    case 'registrarPaginaPublica':
        $nombreArchivo = $_POST['nombre_archivo'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        
        if (empty($nombreArchivo) || empty($descripcion)) {
            echo json_encode(['success' => false, 'message' => 'Campos incompletos']);
            exit;
        }
        
        try {
            $query = "
                INSERT INTO paginas_publicas (nombre_archivo, descripcion, activo)
                VALUES (:nombre_archivo, :descripcion, true)
            ";
            
            $stmt = $conectar->prepare($query);
            $stmt->execute([
                'nombre_archivo' => $nombreArchivo,
                'descripcion' => $descripcion
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Página pública registrada correctamente'
            ]);
            
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        break;
    
    // ==========================================
    // Eliminar página
    // ==========================================
    case 'eliminarPagina':
        $id = $_POST['id'] ?? 0;
        $tipo = $_POST['tipo'] ?? '';
        
        try {
            if ($tipo === 'especifica') {
                // Primero eliminar roles asignados
                $query1 = "DELETE FROM pagina_rol WHERE pagina_id = :id";
                $stmt1 = $conectar->prepare($query1);
                $stmt1->execute(['id' => $id]);
                
                // Luego eliminar página
                $query2 = "DELETE FROM paginas_especificas WHERE id = :id";
                $stmt2 = $conectar->prepare($query2);
                $stmt2->execute(['id' => $id]);
                
            } else if ($tipo === 'publica') {
                $query = "DELETE FROM paginas_publicas WHERE id = :id";
                $stmt = $conectar->prepare($query);
                $stmt->execute(['id' => $id]);
            }
            
            MenuManager::clearAllCache();
            
            echo json_encode([
                'success' => true,
                'message' => 'Página eliminada correctamente'
            ]);
            
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}