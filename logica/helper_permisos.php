<?php
/**
 * Helper para verificación de permisos
 * Incluir este archivo en las páginas que requieran control de acceso
 */

function verificarPermisoUsuario($modulo = '', $submodulo = '') {
    if (!isset($_SESSION['id'])) {
        header("Location: login.php");
        exit();
    }
    
    // Intentar incluir bd.php desde diferentes rutas
    if (file_exists('logica/bd.php')) {
        include_once('logica/bd.php');
    } elseif (file_exists('bd.php')) {
        include_once('bd.php');
    } elseif (file_exists('../bd.php')) {
        include_once('../bd.php');
    }
    
    global $conectar;
    
    if (!$conectar) {
        error_log("Error: No hay conexión a la base de datos");
        mostrarAccesoDenegado('Error de conexión a la base de datos');
        return false;
    }
    
    try {
        
        // Obtener el rol del usuario
        $queryRol = "SELECT id_rol FROM usuario WHERE id = :id_usuario AND deleted_at IS NULL";
        $stmt = $conectar->prepare($queryRol);
        $stmt->execute([':id_usuario' => $_SESSION['id']]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario || !$usuario['id_rol']) {
            mostrarAccesoDenegado('Usuario sin rol asignado');
            return false;
        }
        
        // Obtener permisos del rol
        $queryPermisos = "SELECT permisos FROM roles WHERE id_rol = :id_rol AND estado = 1";
        $stmt = $conectar->prepare($queryPermisos);
        $stmt->execute([':id_rol' => $usuario['id_rol']]);
        $rol = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$rol) {
            mostrarAccesoDenegado('Rol no encontrado o inactivo');
            return false;
        }
        
        $permisos = json_decode($rol['permisos'], true);
        
        // Verificar permiso de módulo
        if ($modulo && !$submodulo) {
            if (!isset($permisos['modulos'][$modulo]) || !$permisos['modulos'][$modulo]) {
                mostrarAccesoDenegado('No tienes permiso para acceder a este módulo');
                return false;
            }
        }
        
        // Verificar permiso de submódulo
        if ($submodulo) {
            if (!isset($permisos['submodulos'][$submodulo]) || !$permisos['submodulos'][$submodulo]) {
                mostrarAccesoDenegado('No tienes permiso para acceder a esta sección');
                return false;
            }
        }
        
        return true;
        
    } catch (PDOException $e) {
        error_log("Error en verificarPermisoUsuario: " . $e->getMessage());
        mostrarAccesoDenegado('Error al verificar permisos');
        return false;
    }
}

function mostrarAccesoDenegado($mensaje = 'Acceso Denegado') {
    $nombre = $_SESSION['nombre'] ?? 'Usuario';
    $ape_usuario = $_SESSION['ape'] ?? '';
    $usuario = $_SESSION['usuario'] ?? '';
    
    echo '
    <div style="text-align: center; background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 20px; border-radius: 10px; font-size: 18px; font-weight: bold; margin: 20px;">
        <i class="fas fa-exclamation-triangle" style="margin-right: 10px; font-size: 50px;"></i>
        <h2>ACCESO DENEGADO</h2>
        <p>' . strtoupper($nombre) . ' ' . strtoupper($ape_usuario) . ' [' . strtoupper($usuario) . ']</p>
        <p>' . $mensaje . '</p>
    </div>
    <div style="text-align: center;">
        <img src="assets/img/acceso-denegado.png" alt="Acceso Denegado" style="max-width: 400px;" />
    </div>
    <div style="text-align: center; margin-top: 20px;">
        <a href="index.php" class="btn btn-primary">Volver al Inicio</a>
    </div>
    ';
    exit();
}

function obtenerPermisosUsuario($id_usuario) {
    if (!$id_usuario) {
        return null;
    }
    
    // Intentar incluir bd.php desde diferentes rutas
    if (file_exists('logica/bd.php')) {
        include_once('logica/bd.php');
    } elseif (file_exists('bd.php')) {
        include_once('bd.php');
    } elseif (file_exists('../bd.php')) {
        include_once('../bd.php');
    }
    
    global $conectar;
    
    if (!$conectar) {
        error_log("Error: No hay conexión a la base de datos en obtenerPermisosUsuario");
        return null;
    }
    
    try {
        
        $queryRol = "SELECT id_rol FROM usuario WHERE id = :id_usuario AND deleted_at IS NULL";
        $stmt = $conectar->prepare($queryRol);
        $stmt->execute([':id_usuario' => $id_usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario || !$usuario['id_rol']) {
            return null;
        }
        
        $queryPermisos = "SELECT permisos FROM roles WHERE id_rol = :id_rol AND estado = 1";
        $stmt = $conectar->prepare($queryPermisos);
        $stmt->execute([':id_rol' => $usuario['id_rol']]);
        $rol = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$rol) {
            return null;
        }
        
        return json_decode($rol['permisos'], true);
        
    } catch (PDOException $e) {
        error_log("Error en obtenerPermisosUsuario: " . $e->getMessage());
        return null;
    }
}

function tienePermiso($modulo = '', $submodulo = '') {
    if (!isset($_SESSION['id']) || !$_SESSION['id']) {
        return false;
    }
    
    $permisos = obtenerPermisosUsuario($_SESSION['id']);
    
    if (!$permisos) {
        return false;
    }
    
    if ($modulo && !$submodulo) {
        return isset($permisos['modulos'][$modulo]) && $permisos['modulos'][$modulo];
    }
    
    if ($submodulo) {
        return isset($permisos['submodulos'][$submodulo]) && $permisos['submodulos'][$submodulo];
    }
    
    return false;
}

/**
 * Función de inicialización - llamar al inicio de cabecera.php
 */
function inicializarSistemaPermisos() {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    if (!isset($_SESSION['id'])) {
        return null;
    }
    
    return obtenerPermisosUsuario($_SESSION['id']);
}