<?php
/**
 * CONTROLADOR DE ROLES Y PERMISOS
 * Maneja todas las operaciones CRUD de roles y asignación de permisos
 */

include("bd.php");

// Habilitar CORS si es necesario
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if (isset($_POST["accion"])) {
    $accion = $_POST["accion"];
    controladorRoles($accion);
}

function controladorRoles($accion)
{
    switch ($accion) {
        case 'LISTAR_ROLES':
            $sucursal_id = isset($_POST["sucursal_id"]) ? $_POST["sucursal_id"] : null;
            $result = fnListarRoles($sucursal_id);
            echo json_encode($result);
            break;

        case 'LISTAR_MODULOS':
            $result = fnListarModulos();
            echo json_encode($result);
            break;

        case 'LISTAR_SUBMODULOS':
            $id_modulo = isset($_POST["id_modulo"]) ? $_POST["id_modulo"] : null;
            if ($id_modulo) {
                $result = fnListarSubmodulosPorModulo($id_modulo);
            } else {
                $result = fnListarTodosSubmodulos();
            }
            echo json_encode($result);
            break;

        case 'OBTENER_PERMISOS_ROL':
            if (isset($_POST["id_rol"])) {
                $id_rol = $_POST["id_rol"];
                $result = fnObtenerPermisosRol($id_rol);
                echo json_encode($result);
            }
            break;

        case 'CREAR_ROL':
            if (isset($_POST["nombre_rol"]) && isset($_POST["sucursal_id"])) {
                $nombre = $_POST["nombre_rol"];
                $descripcion = isset($_POST["descripcion"]) ? $_POST["descripcion"] : '';
                $sucursal_id = $_POST["sucursal_id"];
                $result = fnCrearRol($nombre, $descripcion, $sucursal_id);
                echo json_encode($result);
            }
            break;

        case 'ACTUALIZAR_ROL':
            if (isset($_POST["id_rol"]) && isset($_POST["nombre_rol"])) {
                $id_rol = $_POST["id_rol"];
                $nombre = $_POST["nombre_rol"];
                $descripcion = isset($_POST["descripcion"]) ? $_POST["descripcion"] : '';
                $result = fnActualizarRol($id_rol, $nombre, $descripcion);
                echo json_encode($result);
            }
            break;

        case 'ELIMINAR_ROL':
            if (isset($_POST["id_rol"])) {
                $id_rol = $_POST["id_rol"];
                $result = fnEliminarRol($id_rol);
                echo json_encode($result);
            }
            break;

        case 'GUARDAR_PERMISOS':
            if (isset($_POST["id_rol"]) && isset($_POST["permisos"])) {
                $id_rol = $_POST["id_rol"];
                $permisos = json_decode($_POST["permisos"], true);
                $result = fnGuardarPermisos($id_rol, $permisos);
                echo json_encode($result);
            }
            break;

        case 'ASIGNAR_ROL_USUARIO':
            if (isset($_POST["usuario_id"]) && isset($_POST["id_rol"])) {
                $usuario_id = $_POST["usuario_id"];
                $id_rol = $_POST["id_rol"];
                $result = fnAsignarRolUsuario($usuario_id, $id_rol);
                echo json_encode($result);
            }
            break;

        case 'CLONAR_ROL':
            if (isset($_POST["id_rol"]) && isset($_POST["nuevo_nombre"])) {
                $id_rol = $_POST["id_rol"];
                $nuevo_nombre = $_POST["nuevo_nombre"];
                $result = fnClonarRol($id_rol, $nuevo_nombre);
                echo json_encode($result);
            }
            break;

        default:
            echo json_encode(['error' => 'Acción no válida']);
            break;
    }
}

// ============================================
// FUNCIONES DE GESTIÓN DE ROLES
// ============================================

/**
 * Crear un nuevo rol
 */
function fnCrearRol($nombre, $descripcion, $sucursal_id)
{
    global $conectar;
    try {
        $query = "
            INSERT INTO roles (nombre_rol, descripcion, sucursal_id, permisos, estado)
            VALUES (:nombre, :descripcion, :sucursal_id, '{}'::jsonb, 1)
            RETURNING id_rol
        ";
        
        $stmt = $conectar->prepare($query);
        $stmt->execute([
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'sucursal_id' => $sucursal_id
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'success' => true,
            'message' => 'Rol creado correctamente',
            'id_rol' => $result['id_rol']
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Error al crear rol: ' . $e->getMessage()
        ];
    }
}

/**
 * Actualizar un rol existente
 */
function fnActualizarRol($id_rol, $nombre, $descripcion)
{
    global $conectar;
    try {
        $query = "
            UPDATE roles 
            SET nombre_rol = :nombre,
                descripcion = :descripcion,
                updated_at = CURRENT_TIMESTAMP
            WHERE id_rol = :id_rol
        ";
        
        $stmt = $conectar->prepare($query);
        $stmt->execute([
            'id_rol' => $id_rol,
            'nombre' => $nombre,
            'descripcion' => $descripcion
        ]);
        
        return [
            'success' => true,
            'message' => 'Rol actualizado correctamente'
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Error al actualizar rol: ' . $e->getMessage()
        ];
    }
}

/**
 * Eliminar un rol (soft delete)
 */
function fnEliminarRol($id_rol)
{
    global $conectar;
    try {
        // Verificar que el rol no esté asignado a usuarios
        $query_check = "SELECT COUNT(*) as usuarios FROM usuario WHERE id_rol = :id_rol";
        $stmt = $conectar->prepare($query_check);
        $stmt->execute(['id_rol' => $id_rol]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['usuarios'] > 0) {
            return [
                'success' => false,
                'message' => 'No se puede eliminar: hay ' . $result['usuarios'] . ' usuario(s) con este rol'
            ];
        }
        
        $query = "UPDATE roles SET estado = 0 WHERE id_rol = :id_rol";
        $stmt = $conectar->prepare($query);
        $stmt->execute(['id_rol' => $id_rol]);
        
        return [
            'success' => true,
            'message' => 'Rol eliminado correctamente'
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Error al eliminar rol: ' . $e->getMessage()
        ];
    }
}

/**
 * Guardar permisos de un rol
 */
function fnGuardarPermisos($id_rol, $permisos)
{
    global $conectar;
    try {
        $conectar->beginTransaction();
        
        // 1. Eliminar permisos existentes
        $conectar->exec("DELETE FROM permisos_modulos WHERE id_rol = $id_rol");
        $conectar->exec("DELETE FROM permisos_submodulos WHERE id_rol = $id_rol");
        
        // 2. Insertar permisos de módulos
        if (isset($permisos['modulos'])) {
            $stmt_modulo = $conectar->prepare("
                INSERT INTO permisos_modulos (id_rol, id_modulo, puede_ver)
                VALUES (:id_rol, :id_modulo, :puede_ver)
            ");
            
            foreach ($permisos['modulos'] as $id_modulo => $puede_ver) {
                if ($puede_ver) {
                    $stmt_modulo->execute([
                        'id_rol' => $id_rol,
                        'id_modulo' => $id_modulo,
                        'puede_ver' => 1
                    ]);
                }
            }
        }
        
        // 3. Insertar permisos de submódulos
        if (isset($permisos['submodulos'])) {
            $stmt_submodulo = $conectar->prepare("
                INSERT INTO permisos_submodulos (id_rol, id_submodulo, puede_ver)
                VALUES (:id_rol, :id_submodulo, :puede_ver)
            ");
            
            foreach ($permisos['submodulos'] as $id_submodulo => $puede_ver) {
                if ($puede_ver) {
                    $stmt_submodulo->execute([
                        'id_rol' => $id_rol,
                        'id_submodulo' => $id_submodulo,
                        'puede_ver' => 1
                    ]);
                }
            }
        }
        
        $conectar->commit();
        
        return [
            'success' => true,
            'message' => 'Permisos guardados correctamente'
        ];
    } catch (PDOException $e) {
        $conectar->rollBack();
        return [
            'success' => false,
            'message' => 'Error al guardar permisos: ' . $e->getMessage()
        ];
    }
}

/**
 * Asignar rol a un usuario
 */
function fnAsignarRolUsuario($usuario_id, $id_rol)
{
    global $conectar;
    try {
        $query = "UPDATE usuario SET id_rol = :id_rol WHERE id = :usuario_id";
        $stmt = $conectar->prepare($query);
        $stmt->execute([
            'id_rol' => $id_rol,
            'usuario_id' => $usuario_id
        ]);
        
        return [
            'success' => true,
            'message' => 'Rol asignado correctamente al usuario'
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Error al asignar rol: ' . $e->getMessage()
        ];
    }
}

/**
 * Clonar un rol existente con sus permisos
 */
function fnClonarRol($id_rol, $nuevo_nombre)
{
    global $conectar;
    try {
        $conectar->beginTransaction();
        
        // 1. Obtener datos del rol original
        $query = "SELECT * FROM roles WHERE id_rol = :id_rol";
        $stmt = $conectar->prepare($query);
        $stmt->execute(['id_rol' => $id_rol]);
        $rol_original = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // 2. Crear nuevo rol
        $query_insert = "
            INSERT INTO roles (nombre_rol, descripcion, sucursal_id, permisos, estado)
            VALUES (:nombre, :descripcion, :sucursal_id, :permisos, 1)
            RETURNING id_rol
        ";
        
        $stmt = $conectar->prepare($query_insert);
        $stmt->execute([
            'nombre' => $nuevo_nombre,
            'descripcion' => 'Copia de: ' . $rol_original['descripcion'],
            'sucursal_id' => $rol_original['sucursal_id'],
            'permisos' => $rol_original['permisos']
        ]);
        
        $nuevo_id_rol = $stmt->fetch(PDO::FETCH_ASSOC)['id_rol'];
        
        // 3. Copiar permisos de módulos
        $conectar->exec("
            INSERT INTO permisos_modulos (id_rol, id_modulo, puede_ver)
            SELECT $nuevo_id_rol, id_modulo, puede_ver
            FROM permisos_modulos
            WHERE id_rol = $id_rol
        ");
        
        // 4. Copiar permisos de submódulos
        $conectar->exec("
            INSERT INTO permisos_submodulos (id_rol, id_submodulo, puede_ver)
            SELECT $nuevo_id_rol, id_submodulo, puede_ver
            FROM permisos_submodulos
            WHERE id_rol = $id_rol
        ");
        
        $conectar->commit();
        
        return [
            'success' => true,
            'message' => 'Rol clonado correctamente',
            'id_rol' => $nuevo_id_rol
        ];
    } catch (PDOException $e) {
        $conectar->rollBack();
        return [
            'success' => false,
            'message' => 'Error al clonar rol: ' . $e->getMessage()
        ];
    }
}

/**
 * Listar todos los submódulos del sistema
 */
function fnListarTodosSubmodulos()
{
    $query = "SELECT * FROM submodulos WHERE estado = 1 ORDER BY id_modulo, orden";
    return executeQuery($query);
}
?>