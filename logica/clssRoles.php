<?php
include("bd.php");

session_start();

if (isset($_POST["accion"])) {
    $accion = $_POST["accion"];
    controladorRoles($accion);
}

function controladorRoles($accion) {
    switch ($accion) {
        case 'LISTAR_ROLES':
            $result = fnListarRoles();
            echo json_encode($result);
            break;
            
        case 'LISTAR_MODULOS_SUBMODULOS':
            $result = fnListarModulosConSubmodulos();
            echo json_encode($result);
            break;
            
        case 'CREAR_ROL':
            $nombre_rol = $_POST['nombre_rol'];
            $descripcion = $_POST['descripcion'] ?? '';
            $estado = $_POST['estado'];
            $permisos = $_POST['permisos'];
            $sucursal_id = $_SESSION['sucursal_id'] ?? 1;
            
            $result = fnCrearRol($nombre_rol, $descripcion, $estado, $permisos, $sucursal_id);
            echo json_encode($result);
            break;
            
        case 'OBTENER_ROL':
            $id_rol = $_POST['id_rol'];
            $result = fnObtenerRol($id_rol);
            echo json_encode($result);
            break;
            
        case 'ACTUALIZAR_ROL':
            $id_rol = $_POST['id_rol'];
            $nombre_rol = $_POST['nombre_rol'];
            $descripcion = $_POST['descripcion'] ?? '';
            $estado = $_POST['estado'];
            $permisos = $_POST['permisos'];
            
            $result = fnActualizarRol($id_rol, $nombre_rol, $descripcion, $estado, $permisos);
            echo json_encode($result);
            break;
            
        case 'ELIMINAR_ROL':
            $id_rol = $_POST['id_rol'];
            $result = fnEliminarRol($id_rol);
            echo json_encode($result);
            break;
            
        case 'VERIFICAR_PERMISO':
            $id_usuario = $_SESSION['id'] ?? null;
            $modulo = $_POST['modulo'] ?? '';
            $submodulo = $_POST['submodulo'] ?? '';
            
            $result = fnVerificarPermiso($id_usuario, $modulo, $submodulo);
            echo json_encode($result);
            break;
    }
}

function executeQuery(string $query, array $params = []): array {
    global $conectar;
    try {
        $stmt = $conectar->prepare($query);
        $stmt->execute($params);
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $datos;
    } catch (PDOException $e) {
        error_log("Error en executeQuery: " . $e->getMessage());
        return [];
    }
}

function fnListarRoles(): array {
    $query = "
        SELECT 
            id_rol,
            sucursal_id,
            nombre_rol,
            descripcion,
            permisos,
            estado,
            created_at,
            updated_at
        FROM roles
        ORDER BY id_rol DESC
    ";
    return executeQuery($query);
}

function fnListarModulosConSubmodulos(): array {
    $query = "
        SELECT 
            m.id_modulo,
            m.nombre_modulo,
            m.icono,
            m.identificador,
            m.orden,
            m.estado,
            (
                SELECT json_agg(
                    json_build_object(
                        'id_submodulo', s.id_submodulo,
                        'nombre_submodulo', s.nombre_submodulo,
                        'url', s.url,
                        'identificador', s.identificador,
                        'orden', s.orden,
                        'estado', s.estado
                    ) ORDER BY s.orden
                )
                FROM submodulos s
                WHERE s.id_modulo = m.id_modulo AND s.estado = 1
            ) as submodulos
        FROM modulos m
        WHERE m.estado = 1
        ORDER BY m.orden
    ";
    
    try {
        $result = executeQuery($query);
        
        // Decodificar el JSON de submodulos
        foreach ($result as &$modulo) {
            if ($modulo['submodulos']) {
                $modulo['submodulos'] = json_decode($modulo['submodulos'], true);
            } else {
                $modulo['submodulos'] = [];
            }
        }
        
        error_log("Módulos encontrados: " . count($result));
        return $result;
        
    } catch (Exception $e) {
        error_log("Error en fnListarModulosConSubmodulos: " . $e->getMessage());
        return [];
    }
}

function fnCrearRol($nombre_rol, $descripcion, $estado, $permisos, $sucursal_id): array {
    global $conectar;
    
    try {
        $query = "
            INSERT INTO roles (sucursal_id, nombre_rol, descripcion, permisos, estado, created_at, updated_at)
            VALUES (:sucursal_id, :nombre_rol, :descripcion, :permisos::jsonb, :estado, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
            RETURNING id_rol
        ";
        
        $stmt = $conectar->prepare($query);
        $stmt->execute([
            ':sucursal_id' => $sucursal_id,
            ':nombre_rol' => $nombre_rol,
            ':descripcion' => $descripcion,
            ':permisos' => $permisos,
            ':estado' => $estado
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            // Insertar en permisos_modulos y permisos_submodulos
            $id_rol = $result['id_rol'];
            $permisosArray = json_decode($permisos, true);
            
            fnGuardarPermisosDetallados($id_rol, $permisosArray);
            
            return ['success' => true, 'id_rol' => $id_rol];
        }
        
        return ['success' => false, 'message' => 'Error al crear el rol'];
        
    } catch (PDOException $e) {
        error_log("Error en fnCrearRol: " . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function fnObtenerRol($id_rol): array {
    $query = "
        SELECT 
            id_rol,
            sucursal_id,
            nombre_rol,
            descripcion,
            permisos,
            estado,
            created_at,
            updated_at
        FROM roles
        WHERE id_rol = :id_rol
    ";
    
    $result = executeQuery($query, [':id_rol' => $id_rol]);
    return $result[0] ?? [];
}

function fnActualizarRol($id_rol, $nombre_rol, $descripcion, $estado, $permisos): array {
    global $conectar;
    
    try {
        $query = "
            UPDATE roles
            SET nombre_rol = :nombre_rol,
                descripcion = :descripcion,
                estado = :estado,
                permisos = :permisos::jsonb,
                updated_at = CURRENT_TIMESTAMP
            WHERE id_rol = :id_rol
        ";
        
        $stmt = $conectar->prepare($query);
        $stmt->execute([
            ':id_rol' => $id_rol,
            ':nombre_rol' => $nombre_rol,
            ':descripcion' => $descripcion,
            ':estado' => $estado,
            ':permisos' => $permisos
        ]);
        
        // Actualizar permisos detallados
        $permisosArray = json_decode($permisos, true);
        
        // Eliminar permisos anteriores
        $conectar->prepare("DELETE FROM permisos_modulos WHERE id_rol = :id_rol")->execute([':id_rol' => $id_rol]);
        $conectar->prepare("DELETE FROM permisos_submodulos WHERE id_rol = :id_rol")->execute([':id_rol' => $id_rol]);
        
        // Insertar nuevos permisos
        fnGuardarPermisosDetallados($id_rol, $permisosArray);
        
        return ['success' => true];
        
    } catch (PDOException $e) {
        error_log("Error en fnActualizarRol: " . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function fnEliminarRol($id_rol): array {
    global $conectar;
    
    try {
        // Verificar si hay usuarios con este rol
        $queryCheck = "SELECT COUNT(*) as total FROM usuario WHERE id_rol = :id_rol";
        $stmt = $conectar->prepare($queryCheck);
        $stmt->execute([':id_rol' => $id_rol]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['total'] > 0) {
            return ['success' => false, 'message' => 'No se puede eliminar. Hay usuarios asignados a este rol.'];
        }
        
        // Eliminar permisos relacionados
        $conectar->prepare("DELETE FROM permisos_modulos WHERE id_rol = :id_rol")->execute([':id_rol' => $id_rol]);
        $conectar->prepare("DELETE FROM permisos_submodulos WHERE id_rol = :id_rol")->execute([':id_rol' => $id_rol]);
        
        // Eliminar el rol
        $query = "DELETE FROM roles WHERE id_rol = :id_rol";
        $stmt = $conectar->prepare($query);
        $stmt->execute([':id_rol' => $id_rol]);
        
        return ['success' => true];
        
    } catch (PDOException $e) {
        error_log("Error en fnEliminarRol: " . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function fnGuardarPermisosDetallados($id_rol, $permisos) {
    global $conectar;
    
    try {
        // Guardar permisos de módulos
        if (isset($permisos['modulos'])) {
            foreach ($permisos['modulos'] as $identificador => $value) {
                // Obtener id_modulo por identificador
                $queryModulo = "SELECT id_modulo FROM modulos WHERE identificador = :identificador";
                $stmt = $conectar->prepare($queryModulo);
                $stmt->execute([':identificador' => $identificador]);
                $modulo = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($modulo) {
                    $queryInsert = "
                        INSERT INTO permisos_modulos (id_rol, id_modulo, puede_ver)
                        VALUES (:id_rol, :id_modulo, 1)
                        ON CONFLICT (id_rol, id_modulo) DO NOTHING
                    ";
                    $stmtInsert = $conectar->prepare($queryInsert);
                    $stmtInsert->execute([
                        ':id_rol' => $id_rol,
                        ':id_modulo' => $modulo['id_modulo']
                    ]);
                }
            }
        }
        
        // Guardar permisos de submódulos
        if (isset($permisos['submodulos'])) {
            foreach ($permisos['submodulos'] as $identificador => $value) {
                // Obtener id_submodulo por identificador
                $querySubmodulo = "SELECT id_submodulo FROM submodulos WHERE identificador = :identificador";
                $stmt = $conectar->prepare($querySubmodulo);
                $stmt->execute([':identificador' => $identificador]);
                $submodulo = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($submodulo) {
                    $queryInsert = "
                        INSERT INTO permisos_submodulos (id_rol, id_submodulo, puede_ver)
                        VALUES (:id_rol, :id_submodulo, 1)
                        ON CONFLICT (id_rol, id_submodulo) DO NOTHING
                    ";
                    $stmtInsert = $conectar->prepare($queryInsert);
                    $stmtInsert->execute([
                        ':id_rol' => $id_rol,
                        ':id_submodulo' => $submodulo['id_submodulo']
                    ]);
                }
            }
        }
        
    } catch (PDOException $e) {
        error_log("Error en fnGuardarPermisosDetallados: " . $e->getMessage());
    }
}

function fnVerificarPermiso($id_usuario, $modulo = '', $submodulo = ''): array {
    global $conectar;
    
    try {
        // Obtener el rol del usuario
        $queryRol = "SELECT id_rol FROM usuario WHERE id = :id_usuario";
        $stmt = $conectar->prepare($queryRol);
        $stmt->execute([':id_usuario' => $id_usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario || !$usuario['id_rol']) {
            return ['permitido' => false, 'message' => 'Usuario sin rol asignado'];
        }
        
        $id_rol = $usuario['id_rol'];
        
        // Obtener permisos del rol
        $queryPermisos = "SELECT permisos FROM roles WHERE id_rol = :id_rol AND estado = 1";
        $stmt = $conectar->prepare($queryPermisos);
        $stmt->execute([':id_rol' => $id_rol]);
        $rol = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$rol) {
            return ['permitido' => false, 'message' => 'Rol no encontrado o inactivo'];
        }
        
        $permisos = json_decode($rol['permisos'], true);
        
        // Verificar permiso de módulo
        if ($modulo && !$submodulo) {
            $permitido = isset($permisos['modulos'][$modulo]) && $permisos['modulos'][$modulo];
            return ['permitido' => $permitido];
        }
        
        // Verificar permiso de submódulo
        if ($submodulo) {
            $permitido = isset($permisos['submodulos'][$submodulo]) && $permisos['submodulos'][$submodulo];
            return ['permitido' => $permitido];
        }
        
        return ['permitido' => false];
        
    } catch (PDOException $e) {
        error_log("Error en fnVerificarPermiso: " . $e->getMessage());
        return ['permitido' => false, 'message' => $e->getMessage()];
    }
}