<?php
/**
 * API para Gestión de Usuarios, Roles y Permisos
 * Sistema Caracol Captain - VERSIÓN CORREGIDA
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

// Manejo de errores mejorado
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores en producción

// Función para responder con error
function responderError($mensaje, $codigo = 500) {
    http_response_code($codigo);
    echo json_encode([
        'success' => false,
        'message' => $mensaje
    ]);
    exit;
}

try {
    // Incluir archivos necesarios
    $archivo_bd = 'logica/bd.php';
    $archivo_clase = 'classes/MenuPermisos.php';
    
    if (!file_exists($archivo_bd)) {
        responderError("Archivo no encontrado: $archivo_bd. Verifica la ruta.", 404);
    }
    
    if (!file_exists($archivo_clase)) {
        responderError("Archivo no encontrado: $archivo_clase. Verifica la ruta.", 404);
    }
    
    require_once $archivo_bd;
    require_once $archivo_clase;
    
    // Iniciar sesión
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Obtener conexión PDO
    if (!function_exists('obtenerConexionPDO')) {
        responderError('La función obtenerConexionPDO no existe. Verifica tu archivo bd.php', 500);
    }
    
    $conn = obtenerConexionPDO();
    
    if (!$conn) {
        responderError('No se pudo establecer conexión con la base de datos', 500);
    }
    
    // Usuario actual (ajusta según tu sistema de autenticación)
    $usuario_actual_id = $_SESSION['usuario_id'] ?? null;
    
    // Crear instancia de MenuPermisos
    $menuPermisos = new MenuPermisos($conn, $usuario_actual_id);
    
    // Obtener acción
    $action = $_REQUEST['action'] ?? '';
    
    if (empty($action)) {
        responderError('No se especificó ninguna acción', 400);
    }
    
    switch ($action) {
        
        // ====================================================================
        // USUARIOS
        // ====================================================================
        
        case 'obtenerUsuarios':
            try {
                // Consulta para obtener usuarios con sus roles
                $sql = "
                    SELECT 
                        u.id as usuario_id,
                        u.username,
                        u.nombre as nombre_completo,
                        u.email,
                        u.activo,
                        COALESCE(
                            json_agg(
                                json_build_object(
                                    'rol_id', r.rol_id,
                                    'rol_nombre', r.rol_nombre,
                                    'rol_codigo', r.rol_codigo
                                )
                            ) FILTER (WHERE r.rol_id IS NOT NULL),
                            '[]'
                        ) as roles
                    FROM usuarios u
                    LEFT JOIN usuario_roles ur ON u.id = ur.usuario_id AND ur.ur_activo = true
                    LEFT JOIN roles r ON ur.rol_id = r.rol_id AND r.rol_activo = true
                    GROUP BY u.id, u.username, u.nombre, u.email, u.activo
                    ORDER BY u.username
                ";
                
                $stmt = $conn->query($sql);
                $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Decodificar JSON de roles
                foreach ($usuarios as &$usuario) {
                    $usuario['roles'] = json_decode($usuario['roles'], true);
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $usuarios
                ]);
            } catch (PDOException $e) {
                responderError('Error en la consulta de usuarios: ' . $e->getMessage(), 500);
            }
            break;
            
        case 'obtenerDetalleUsuario':
            $usuario_id = $_GET['usuario_id'] ?? 0;
            
            if (!$usuario_id) {
                responderError('ID de usuario no válido', 400);
            }
            
            $roles = $menuPermisos->obtenerRolesUsuario($usuario_id);
            $permisos = $menuPermisos->obtenerPermisosUsuario($usuario_id);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'roles' => $roles,
                    'permisos' => $permisos
                ]
            ]);
            break;
            
        case 'obtenerRolesUsuario':
            $usuario_id = $_GET['usuario_id'] ?? 0;
            
            if (!$usuario_id) {
                responderError('ID de usuario no válido', 400);
            }
            
            $roles = $menuPermisos->obtenerRolesUsuario($usuario_id);
            
            echo json_encode([
                'success' => true,
                'data' => $roles
            ]);
            break;
            
        case 'obtenerPermisosUsuario':
            $usuario_id = $_GET['usuario_id'] ?? 0;
            
            if (!$usuario_id) {
                responderError('ID de usuario no válido', 400);
            }
            
            $permisos = $menuPermisos->obtenerPermisosUsuario($usuario_id);
            
            echo json_encode([
                'success' => true,
                'data' => $permisos
            ]);
            break;
            
        case 'asignarRolesUsuario':
            $usuario_id = $_POST['usuario_id'] ?? 0;
            $roles = json_decode($_POST['roles'] ?? '[]', true);
            
            if (!$usuario_id) {
                responderError('ID de usuario no válido', 400);
            }
            
            try {
                // Primero, desactivar todos los roles actuales
                $sql = "UPDATE usuario_roles SET ur_activo = false WHERE usuario_id = :usuario_id";
                $stmt = $conn->prepare($sql);
                $stmt->execute(['usuario_id' => $usuario_id]);
                
                // Luego, asignar los nuevos roles
                $exito = true;
                foreach ($roles as $rol_id) {
                    $resultado = $menuPermisos->asignarRolUsuario($usuario_id, $rol_id, $usuario_actual_id);
                    if (!$resultado) {
                        $exito = false;
                        break;
                    }
                }
                
                echo json_encode([
                    'success' => $exito,
                    'message' => $exito ? 'Roles asignados correctamente' : 'Error al asignar roles'
                ]);
            } catch (Exception $e) {
                responderError('Error al asignar roles: ' . $e->getMessage(), 500);
            }
            break;
            
        case 'asignarPermisoUsuario':
            $usuario_id = $_POST['usuario_id'] ?? 0;
            $permiso_id = $_POST['permiso_id'] ?? 0;
            $concedido = filter_var($_POST['concedido'] ?? true, FILTER_VALIDATE_BOOLEAN);
            $razon = $_POST['razon'] ?? null;
            
            if (!$usuario_id || !$permiso_id) {
                responderError('Datos incompletos', 400);
            }
            
            $resultado = $menuPermisos->asignarPermisoUsuario(
                $usuario_id, 
                $permiso_id, 
                $concedido, 
                $razon, 
                $usuario_actual_id
            );
            
            echo json_encode([
                'success' => $resultado,
                'message' => $resultado ? 'Permiso asignado' : 'Error al asignar permiso'
            ]);
            break;
            
        case 'eliminarPermisoUsuario':
            $usuario_id = $_POST['usuario_id'] ?? 0;
            $permiso_id = $_POST['permiso_id'] ?? 0;
            
            if (!$usuario_id || !$permiso_id) {
                responderError('Datos incompletos', 400);
            }
            
            try {
                $sql = "
                    DELETE FROM usuario_permisos 
                    WHERE usuario_id = :usuario_id 
                    AND permiso_id = :permiso_id
                ";
                
                $stmt = $conn->prepare($sql);
                $resultado = $stmt->execute([
                    'usuario_id' => $usuario_id,
                    'permiso_id' => $permiso_id
                ]);
                
                echo json_encode([
                    'success' => $resultado,
                    'message' => $resultado ? 'Permiso específico eliminado' : 'Error al eliminar'
                ]);
            } catch (PDOException $e) {
                responderError('Error al eliminar permiso: ' . $e->getMessage(), 500);
            }
            break;
            
        // ====================================================================
        // ROLES
        // ====================================================================
        
        case 'obtenerRoles':
            $roles = $menuPermisos->obtenerTodosRoles();
            
            echo json_encode([
                'success' => true,
                'data' => $roles
            ]);
            break;
            
        case 'obtenerRol':
            $rol_id = $_GET['rol_id'] ?? 0;
            
            if (!$rol_id) {
                responderError('ID de rol no válido', 400);
            }
            
            try {
                $sql = "SELECT * FROM roles WHERE rol_id = :rol_id";
                $stmt = $conn->prepare($sql);
                $stmt->execute(['rol_id' => $rol_id]);
                $rol = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$rol) {
                    responderError('Rol no encontrado', 404);
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $rol
                ]);
            } catch (PDOException $e) {
                responderError('Error al obtener rol: ' . $e->getMessage(), 500);
            }
            break;
            
        case 'guardarRol':
            $rol_id = $_POST['rol_id'] ?? null;
            $rol_codigo = trim($_POST['rol_codigo'] ?? '');
            $rol_nombre = trim($_POST['rol_nombre'] ?? '');
            $rol_descripcion = trim($_POST['rol_descripcion'] ?? '');
            $rol_nivel = intval($_POST['rol_nivel'] ?? 50);
            
            if (empty($rol_codigo) || empty($rol_nombre)) {
                responderError('Código y nombre son requeridos', 400);
            }
            
            try {
                if ($rol_id) {
                    // Actualizar rol existente
                    $sql = "
                        UPDATE roles 
                        SET rol_codigo = :rol_codigo,
                            rol_nombre = :rol_nombre,
                            rol_descripcion = :rol_descripcion,
                            rol_nivel = :rol_nivel,
                            actualizado_por = :actualizado_por,
                            fecha_actualizacion = CURRENT_TIMESTAMP
                        WHERE rol_id = :rol_id
                    ";
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([
                        'rol_codigo' => $rol_codigo,
                        'rol_nombre' => $rol_nombre,
                        'rol_descripcion' => $rol_descripcion,
                        'rol_nivel' => $rol_nivel,
                        'actualizado_por' => $usuario_actual_id,
                        'rol_id' => $rol_id
                    ]);
                } else {
                    // Crear nuevo rol
                    $sql = "
                        INSERT INTO roles (rol_codigo, rol_nombre, rol_descripcion, rol_nivel, creado_por)
                        VALUES (:rol_codigo, :rol_nombre, :rol_descripcion, :rol_nivel, :creado_por)
                    ";
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([
                        'rol_codigo' => $rol_codigo,
                        'rol_nombre' => $rol_nombre,
                        'rol_descripcion' => $rol_descripcion,
                        'rol_nivel' => $rol_nivel,
                        'creado_por' => $usuario_actual_id
                    ]);
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Rol guardado correctamente'
                ]);
            } catch (PDOException $e) {
                // Verificar si es error de clave duplicada
                if ($e->getCode() == 23505) { // PostgreSQL unique violation
                    responderError('Ya existe un rol con ese código', 400);
                }
                responderError('Error al guardar: ' . $e->getMessage(), 500);
            }
            break;
            
        case 'obtenerPermisosRol':
            $rol_id = $_GET['rol_id'] ?? 0;
            
            if (!$rol_id) {
                responderError('ID de rol no válido', 400);
            }
            
            $permisos = $menuPermisos->obtenerPermisosRol($rol_id);
            
            echo json_encode([
                'success' => true,
                'data' => $permisos
            ]);
            break;
            
        case 'asignarPermisosRol':
            $rol_id = $_POST['rol_id'] ?? 0;
            $permisos = json_decode($_POST['permisos'] ?? '[]', true);
            
            if (!$rol_id) {
                responderError('ID de rol no válido', 400);
            }
            
            try {
                // Primero, desactivar todos los permisos actuales
                $sql = "UPDATE rol_permisos SET rp_activo = false WHERE rol_id = :rol_id";
                $stmt = $conn->prepare($sql);
                $stmt->execute(['rol_id' => $rol_id]);
                
                // Luego, asignar los nuevos permisos
                $exito = true;
                foreach ($permisos as $permiso_id) {
                    $resultado = $menuPermisos->asignarPermisoRol($rol_id, $permiso_id, $usuario_actual_id);
                    if (!$resultado) {
                        $exito = false;
                        break;
                    }
                }
                
                echo json_encode([
                    'success' => $exito,
                    'message' => $exito ? 'Permisos actualizados' : 'Error al actualizar permisos'
                ]);
            } catch (Exception $e) {
                responderError('Error al asignar permisos: ' . $e->getMessage(), 500);
            }
            break;
            
        // ====================================================================
        // PERMISOS
        // ====================================================================
        
        case 'obtenerPermisos':
            $permisos = $menuPermisos->obtenerTodosPermisos();
            
            echo json_encode([
                'success' => true,
                'data' => $permisos
            ]);
            break;
            
        case 'obtenerPermiso':
            $permiso_id = $_GET['permiso_id'] ?? 0;
            
            if (!$permiso_id) {
                responderError('ID de permiso no válido', 400);
            }
            
            try {
                $sql = "SELECT * FROM permisos WHERE permiso_id = :permiso_id";
                $stmt = $conn->prepare($sql);
                $stmt->execute(['permiso_id' => $permiso_id]);
                $permiso = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$permiso) {
                    responderError('Permiso no encontrado', 404);
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $permiso
                ]);
            } catch (PDOException $e) {
                responderError('Error al obtener permiso: ' . $e->getMessage(), 500);
            }
            break;
            
        case 'guardarPermiso':
            $permiso_id = $_POST['permiso_id'] ?? null;
            $permiso_codigo = trim($_POST['permiso_codigo'] ?? '');
            $permiso_nombre = trim($_POST['permiso_nombre'] ?? '');
            $permiso_modulo = trim($_POST['permiso_modulo'] ?? '');
            $permiso_descripcion = trim($_POST['permiso_descripcion'] ?? '');
            
            if (empty($permiso_codigo) || empty($permiso_nombre)) {
                responderError('Código y nombre son requeridos', 400);
            }
            
            try {
                if ($permiso_id) {
                    // Actualizar permiso existente
                    $sql = "
                        UPDATE permisos 
                        SET permiso_codigo = :permiso_codigo,
                            permiso_nombre = :permiso_nombre,
                            permiso_modulo = :permiso_modulo,
                            permiso_descripcion = :permiso_descripcion
                        WHERE permiso_id = :permiso_id
                    ";
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([
                        'permiso_codigo' => $permiso_codigo,
                        'permiso_nombre' => $permiso_nombre,
                        'permiso_modulo' => $permiso_modulo,
                        'permiso_descripcion' => $permiso_descripcion,
                        'permiso_id' => $permiso_id
                    ]);
                } else {
                    // Crear nuevo permiso
                    $sql = "
                        INSERT INTO permisos (permiso_codigo, permiso_nombre, permiso_modulo, permiso_descripcion)
                        VALUES (:permiso_codigo, :permiso_nombre, :permiso_modulo, :permiso_descripcion)
                    ";
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([
                        'permiso_codigo' => $permiso_codigo,
                        'permiso_nombre' => $permiso_nombre,
                        'permiso_modulo' => $permiso_modulo,
                        'permiso_descripcion' => $permiso_descripcion
                    ]);
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Permiso guardado correctamente'
                ]);
            } catch (PDOException $e) {
                // Verificar si es error de clave duplicada
                if ($e->getCode() == 23505) { // PostgreSQL unique violation
                    responderError('Ya existe un permiso con ese código', 400);
                }
                responderError('Error al guardar: ' . $e->getMessage(), 500);
            }
            break;
            
        // ====================================================================
        // MENÚ
        // ====================================================================
        
        case 'obtenerMenuUsuario':
            $usuario_id = $_GET['usuario_id'] ?? $usuario_actual_id;
            $menu = $menuPermisos->obtenerMenuUsuario($usuario_id);
            
            echo json_encode([
                'success' => true,
                'data' => $menu
            ]);
            break;
            
        case 'verificarPermiso':
            $permiso_codigo = $_GET['permiso_codigo'] ?? '';
            $usuario_id = $_GET['usuario_id'] ?? $usuario_actual_id;
            
            if (empty($permiso_codigo)) {
                responderError('Código de permiso requerido', 400);
            }
            
            $tiene_permiso = $menuPermisos->tienePermiso($permiso_codigo, $usuario_id);
            
            echo json_encode([
                'success' => true,
                'tiene_permiso' => $tiene_permiso
            ]);
            break;
            
        default:
            responderError('Acción no válida: ' . $action, 400);
            break;
    }
    
} catch (Exception $e) {
    responderError('Error del servidor: ' . $e->getMessage(), 500);
}