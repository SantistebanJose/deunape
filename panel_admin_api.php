<?php

/**
 * panel_admin_api.php
 * API COMPLETA para Panel Administrativo
 */

session_start();
require_once("logica/bd.php");
require_once("MenuManager.php");

header('Content-Type: application/json');

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

try {
    switch ($action) {

        // ==========================================
        // ROLES
        // ==========================================
        case 'crearRol':
            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $sucursal_id = $_SESSION["sucursal_id"];

            if (empty($nombre) || empty($descripcion)) {
                throw new Exception('Campos incompletos');
            }

            $query = "INSERT INTO roles (nombre, descripcion, activo, sucursal_id) VALUES (:nombre, :descripcion, true,:sucursal_id)";
            $stmt = $conectar->prepare($query);
            $stmt->execute([
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'sucursal_id' => $sucursal_id
            ]);

            MenuManager::clearAllCache();
            echo json_encode(['success' => true, 'message' => 'Rol creado']);
            break;

        case 'eliminarRol':
            $id = $_POST['id'] ?? 0;

            $stmt = $conectar->prepare("DELETE FROM rol_permiso WHERE rol_id = :id");
            $stmt->execute(['id' => $id]);

            $stmt = $conectar->prepare("DELETE FROM menu_rol WHERE rol_id = :id");
            $stmt->execute(['id' => $id]);

            $stmt = $conectar->prepare("DELETE FROM roles WHERE id_rol = :id");
            $stmt->execute(['id' => $id]);

            MenuManager::clearAllCache();
            echo json_encode(['success' => true]);
            break;

        // ==========================================
        // PERMISOS
        // ==========================================
        case 'crearPermiso':
            $codigo = $_POST['codigo'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $categoria = $_POST['categoria'] ?? '';

            if (empty($codigo) || empty($nombre) || empty($descripcion) || empty($categoria)) {
                throw new Exception('Campos incompletos');
            }

            $query = "INSERT INTO permisos (codigo, nombre, descripcion, categoria, activo) VALUES (:codigo, :nombre, :descripcion, :categoria, true)";
            $stmt = $conectar->prepare($query);
            $stmt->execute([
                'codigo' => $codigo,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'categoria' => $categoria
            ]);

            MenuManager::clearAllCache();
            echo json_encode(['success' => true, 'message' => 'Permiso creado']);
            break;

        case 'eliminarPermiso':
            $id = $_POST['id'] ?? 0;

            $stmt = $conectar->prepare("DELETE FROM rol_permiso WHERE permiso_id = :id");
            $stmt->execute(['id' => $id]);

            $stmt = $conectar->prepare("DELETE FROM permisos WHERE id = :id");
            $stmt->execute(['id' => $id]);

            MenuManager::clearAllCache();
            echo json_encode(['success' => true]);
            break;

        // ==========================================
        // ASIGNACIÓN DE PERMISOS
        // ==========================================
        case 'getPermisosRol':
            $rolId = $_POST['rol_id'] ?? 0;

            $query = "SELECT permiso_id FROM rol_permiso WHERE rol_id = :rol_id";
            $stmt = $conectar->prepare($query);
            $stmt->execute(['rol_id' => $rolId]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $permisos = array_column($result, 'permiso_id');
            echo json_encode(['success' => true, 'permisos' => $permisos]);
            break;

        case 'asignarPermiso':
            $rolId = $_POST['rol_id'] ?? 0;
            $permisoId = $_POST['permiso_id'] ?? 0;

            $check = $conectar->prepare("SELECT COUNT(*) as total FROM rol_permiso WHERE rol_id = :rol_id AND permiso_id = :permiso_id");
            $check->execute(['rol_id' => $rolId, 'permiso_id' => $permisoId]);
            $exists = $check->fetch(PDO::FETCH_ASSOC);

            if ($exists['total'] == 0) {
                $query = "INSERT INTO rol_permiso (rol_id, permiso_id) VALUES (:rol_id, :permiso_id)";
                $stmt = $conectar->prepare($query);
                $stmt->execute(['rol_id' => $rolId, 'permiso_id' => $permisoId]);
            }

            MenuManager::clearAllCache();
            echo json_encode(['success' => true, 'message' => 'Permiso asignado']);
            break;

        case 'quitarPermiso':
            $rolId = $_POST['rol_id'] ?? 0;
            $permisoId = $_POST['permiso_id'] ?? 0;

            $query = "DELETE FROM rol_permiso WHERE rol_id = :rol_id AND permiso_id = :permiso_id";
            $stmt = $conectar->prepare($query);
            $stmt->execute(['rol_id' => $rolId, 'permiso_id' => $permisoId]);

            MenuManager::clearAllCache();
            echo json_encode(['success' => true, 'message' => 'Permiso removido']);
            break;

        case 'asignarTodosPermisos':
            $rolId = $_POST['rol_id'] ?? 0;

            $stmt = $conectar->prepare("DELETE FROM rol_permiso WHERE rol_id = :rol_id");
            $stmt->execute(['rol_id' => $rolId]);

            $query = "INSERT INTO rol_permiso (rol_id, permiso_id) SELECT :rol_id, id FROM permisos WHERE activo = true";
            $stmt = $conectar->prepare($query);
            $stmt->execute(['rol_id' => $rolId]);

            MenuManager::clearAllCache();
            echo json_encode(['success' => true, 'message' => 'Todos asignados']);
            break;

        case 'quitarTodosPermisos':
            $rolId = $_POST['rol_id'] ?? 0;

            $stmt = $conectar->prepare("DELETE FROM rol_permiso WHERE rol_id = :rol_id");
            $stmt->execute(['rol_id' => $rolId]);

            MenuManager::clearAllCache();
            echo json_encode(['success' => true, 'message' => 'Todos removidos']);
            break;

        // ==========================================
        // USUARIOS
        // ==========================================
        case 'crearUsuario':
            $nombres = $_POST['nombres'] ?? '';
            $apellidos = $_POST['apellidos'] ?? '';
            $email = $_POST['email'] ?? null;
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $idRol = $_POST['id_rol'] ?? 0;

            if (empty($nombres) || empty($apellidos) || empty($username) || empty($password) || empty($idRol)) {
                throw new Exception('Campos incompletos');
            }

            $checkQuery = "SELECT COUNT(*) as total FROM usuario WHERE username = :username";
            $stmt = $conectar->prepare($checkQuery);
            $stmt->execute(['username' => $username]);
            $exists = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($exists['total'] > 0) {
                throw new Exception('Username ya existe');
            }

            $conectar->beginTransaction();

            try {
                $queryPersona = "INSERT INTO persona (nombres, apellidos, email) VALUES (:nombres, :apellidos, :email)";
                $stmtPersona = $conectar->prepare($queryPersona);
                $stmtPersona->execute([
                    'nombres' => $nombres,
                    'apellidos' => $apellidos,
                    'email' => $email
                ]);

                $personaId = $conectar->lastInsertId();
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $queryUsuario = "INSERT INTO usuario (username, password, persona_id, id_rol, activo) VALUES (:username, :password, :persona_id, :id_rol, true)";
                $stmtUsuario = $conectar->prepare($queryUsuario);
                $stmtUsuario->execute([
                    'username' => $username,
                    'password' => $passwordHash,
                    'persona_id' => $personaId,
                    'id_rol' => $idRol
                ]);

                $conectar->commit();
                echo json_encode(['success' => true, 'message' => 'Usuario creado']);
            } catch (Exception $e) {
                $conectar->rollBack();
                throw $e;
            }
            break;

        case 'cambiarRolUsuario':
            $userId = $_POST['usuario_id'] ?? 0;
            $nuevoRolId = $_POST['nuevo_rol_id'] ?? 0;

            if (empty($userId) || empty($nuevoRolId)) {
                throw new Exception('Datos incompletos');
            }

            $query = "UPDATE usuario SET id_rol = :nuevo_rol WHERE id = :user_id";
            $stmt = $conectar->prepare($query);
            $stmt->execute(['nuevo_rol' => $nuevoRolId, 'user_id' => $userId]);

            echo json_encode(['success' => true, 'message' => 'Rol actualizado']);
            break;

        case 'eliminarUsuario':
            $id = $_POST['id'] ?? 0;

            $query = "SELECT persona_id FROM usuario WHERE id = :id";
            $stmt = $conectar->prepare($query);
            $stmt->execute(['id' => $id]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                $stmt = $conectar->prepare("DELETE FROM usuario WHERE id = :id");
                $stmt->execute(['id' => $id]);

                $stmt = $conectar->prepare("DELETE FROM persona WHERE id = :id");
                $stmt->execute(['id' => $usuario['persona_id']]);
            }

            echo json_encode(['success' => true]);
            break;

        // ==========================================
        // MENÚS
        // ==========================================
        case 'crearMenu':
            $codigo = $_POST['codigo'] ?? '';
            $titulo = $_POST['titulo'] ?? '';
            $icono = $_POST['icono'] ?? '';
            $orden = $_POST['orden'] ?? 10;

            if (empty($codigo) || empty($titulo) || empty($icono)) {
                throw new Exception('Campos incompletos');
            }

            $query = "INSERT INTO menus (codigo, titulo, icono, orden, activo) VALUES (:codigo, :titulo, :icono, :orden, true)";
            $stmt = $conectar->prepare($query);
            $stmt->execute([
                'codigo' => $codigo,
                'titulo' => $titulo,
                'icono' => $icono,
                'orden' => $orden
            ]);

            MenuManager::clearAllCache();
            echo json_encode(['success' => true, 'message' => 'Menú creado']);
            break;

        case 'eliminarMenu':
            $id = $_POST['id'] ?? 0;

            $stmt = $conectar->prepare("DELETE FROM menu_items WHERE menu_id = :id");
            $stmt->execute(['id' => $id]);

            $stmt = $conectar->prepare("DELETE FROM menu_rol WHERE menu_id = :id");
            $stmt->execute(['id' => $id]);

            $stmt = $conectar->prepare("DELETE FROM menus WHERE id = :id");
            $stmt->execute(['id' => $id]);

            MenuManager::clearAllCache();
            echo json_encode(['success' => true]);
            break;

        // ==========================================
        // MENU ITEMS
        // ==========================================
        case 'crearItem':
            $menuId = $_POST['menu_id'] ?? 0;
            $titulo = $_POST['titulo'] ?? '';
            $link = $_POST['link'] ?? '';
            $permisoId = !empty($_POST['permiso_id']) ? $_POST['permiso_id'] : null;
            $orden = $_POST['orden'] ?? 1;

            if (empty($menuId) || empty($titulo) || empty($link)) {
                throw new Exception('Campos incompletos');
            }

            $query = "INSERT INTO menu_items (menu_id, titulo, link, permiso_id, orden, activo) VALUES (:menu_id, :titulo, :link, :permiso_id, :orden, true)";
            $stmt = $conectar->prepare($query);
            $stmt->execute([
                'menu_id' => $menuId,
                'titulo' => $titulo,
                'link' => $link,
                'permiso_id' => $permisoId,
                'orden' => $orden
            ]);

            MenuManager::clearAllCache();
            echo json_encode(['success' => true, 'message' => 'Item creado']);
            break;
        case 'getItems':
            $menuId = $_POST['menu_id'] ?? 0;

            $query = "SELECT mi.*, p.codigo as permiso_codigo 
              FROM menu_items mi 
              LEFT JOIN permisos p ON mi.permiso_id = p.id 
              WHERE mi.menu_id = :menu_id 
              ORDER BY mi.orden";
            $stmt = $conectar->prepare($query);
            $stmt->execute(['menu_id' => $menuId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'items' => $items]);
            break;

        case 'editarItem':
            $id = $_POST['id'] ?? 0;
            $titulo = $_POST['titulo'] ?? '';
            $link = $_POST['link'] ?? '';
            $permisoId = !empty($_POST['permiso_id']) ? $_POST['permiso_id'] : null;
            $orden = $_POST['orden'] ?? 1;

            if (empty($id) || empty($titulo) || empty($link)) {
                throw new Exception('Campos incompletos');
            }

            $query = "UPDATE menu_items SET titulo = :titulo, link = :link, permiso_id = :permiso_id, orden = :orden WHERE id = :id";
            $stmt = $conectar->prepare($query);
            $stmt->execute([
                'titulo' => $titulo,
                'link' => $link,
                'permiso_id' => $permisoId,
                'orden' => $orden,
                'id' => $id
            ]);

            MenuManager::clearAllCache();
            echo json_encode(['success' => true, 'message' => 'Item actualizado']);
            break;

        case 'eliminarItem':
            $id = $_POST['id'] ?? 0;

            $stmt = $conectar->prepare("DELETE FROM menu_items WHERE id = :id");
            $stmt->execute(['id' => $id]);

            MenuManager::clearAllCache();
            echo json_encode(['success' => true]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
