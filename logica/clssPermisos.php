<?php
require_once('bd.php');

/**
 * Clase para manejar permisos basados en JSON
 * Compatible con PDO y PostgreSQL
 */
class Permisos {
    
    /**
     * Obtiene los permisos de un rol
     */
    public static function obtenerPermisosRol($id_rol) {
        global $conectar;
        
        try {
            $query = "SELECT permisos FROM roles WHERE id_rol = :id_rol AND estado = 1";
            $stmt = $conectar->prepare($query);
            $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado && isset($resultado['permisos'])) {
                return json_decode($resultado['permisos'], true);
            }
            
            return array();
        } catch (Exception $e) {
            error_log("Error en obtenerPermisosRol: " . $e->getMessage());
            return array();
        }
    }
    
    /**
     * Verifica si un rol tiene acceso a un módulo
     */
    public static function tieneAccesoModulo($id_rol, $modulo_id) {
        $permisos = self::obtenerPermisosRol($id_rol);
        
        if (isset($permisos[$modulo_id]) && isset($permisos[$modulo_id]['ver'])) {
            return $permisos[$modulo_id]['ver'] === true;
        }
        
        return false;
    }
    
    /**
     * Verifica si un rol tiene acceso a un submódulo
     */
    public static function tieneAccesoSubmodulo($id_rol, $modulo_id, $submodulo_id) {
        $permisos = self::obtenerPermisosRol($id_rol);
        
        if (isset($permisos[$modulo_id]['submenu'][$submodulo_id])) {
            return $permisos[$modulo_id]['submenu'][$submodulo_id] === true;
        }
        
        return false;
    }
    
    /**
     * Verifica si un rol tiene acceso a una URL específica
     */
    public static function tieneAccesoURL($id_rol, $url) {
        $permisos = self::obtenerPermisosRol($id_rol);
        
        // Cargar configuración del menú
        $menu_config_path = __DIR__ . '/../config/menu_config.php';
        if (!file_exists($menu_config_path)) {
            error_log("menu_config.php no encontrado en: " . $menu_config_path);
            return false;
        }
        
        $menu_config = include($menu_config_path);
        
        // Buscar la URL en el menú
        foreach ($menu_config as $modulo_id => $modulo) {
            if (isset($modulo['submenu'])) {
                foreach ($modulo['submenu'] as $submodulo_id => $submodulo) {
                    if ($submodulo['url'] === $url) {
                        return self::tieneAccesoSubmodulo($id_rol, $modulo_id, $submodulo_id);
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * Obtiene todos los módulos accesibles para un rol
     */
    public static function obtenerModulosAccesibles($id_rol) {
        $permisos = self::obtenerPermisosRol($id_rol);
        
        // Cargar configuración del menú
        $menu_config_path = __DIR__ . '/../config/menu_config.php';
        if (!file_exists($menu_config_path)) {
            error_log("menu_config.php no encontrado en: " . $menu_config_path);
            return array();
        }
        
        $menu_config = include($menu_config_path);
        
        $modulos_accesibles = array();
        
        foreach ($menu_config as $modulo_id => $modulo) {
            if (self::tieneAccesoModulo($id_rol, $modulo_id)) {
                $modulo['id'] = $modulo_id;
                $modulo['submodulos_accesibles'] = array();
                
                // Obtener submódulos accesibles
                if (isset($modulo['submenu'])) {
                    foreach ($modulo['submenu'] as $submodulo_id => $submodulo) {
                        if (self::tieneAccesoSubmodulo($id_rol, $modulo_id, $submodulo_id)) {
                            $submodulo['id'] = $submodulo_id;
                            $modulo['submodulos_accesibles'][] = $submodulo;
                        }
                    }
                }
                
                // Solo agregar módulos con al menos un submódulo accesible
                if (count($modulo['submodulos_accesibles']) > 0) {
                    $modulos_accesibles[] = $modulo;
                }
            }
        }
        
        // Ordenar por orden
        usort($modulos_accesibles, function($a, $b) {
            return $a['orden'] - $b['orden'];
        });
        
        return $modulos_accesibles;
    }
    
    /**
     * Actualiza los permisos de un rol
     */
    public static function actualizarPermisos($id_rol, $permisos_json) {
        global $conectar;
        
        try {
            $query = "UPDATE roles SET permisos = :permisos, updated_at = CURRENT_TIMESTAMP WHERE id_rol = :id_rol";
            $stmt = $conectar->prepare($query);
            $stmt->bindParam(':permisos', $permisos_json, PDO::PARAM_STR);
            $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error en actualizarPermisos: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene todos los roles de una sucursal
     */
    public static function obtenerRolesPorSucursal($sucursal_id) {
        global $conectar;
        
        try {
            $query = "SELECT * FROM roles WHERE sucursal_id = :sucursal_id AND estado = 1 ORDER BY id_rol";
            $stmt = $conectar->prepare($query);
            $stmt->bindParam(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $roles = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $row['permisos_decodificados'] = json_decode($row['permisos'], true);
                $roles[] = $row;
            }
            
            return $roles;
        } catch (Exception $e) {
            error_log("Error en obtenerRolesPorSucursal: " . $e->getMessage());
            return array();
        }
    }
    
    /**
     * Crea un nuevo rol
     */
    public static function crearRol($sucursal_id, $nombre_rol, $descripcion, $permisos_json) {
        global $conectar;
        
        try {
            $query = "INSERT INTO roles (sucursal_id, nombre_rol, descripcion, permisos, estado) 
                      VALUES (:sucursal_id, :nombre_rol, :descripcion, :permisos, 1) 
                      RETURNING id_rol";
            
            $stmt = $conectar->prepare($query);
            $stmt->bindParam(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre_rol', $nombre_rol, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':permisos', $permisos_json, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['id_rol'];
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Error en crearRol: " . $e->getMessage());
            return false;
        }
    }
}

// Funciones de compatibilidad con código existente
function fnObtenerPermisosRol($id_rol) {
    return Permisos::obtenerPermisosRol($id_rol);
}

function fnTieneAccesoModulo($id_rol, $modulo_id) {
    return Permisos::tieneAccesoModulo($id_rol, $modulo_id);
}

function fnTieneAccesoURL($id_rol, $url) {
    return Permisos::tieneAccesoURL($id_rol, $url);
}

function fnObtenerModulosAccesibles($id_rol) {
    return Permisos::obtenerModulosAccesibles($id_rol);
}
?>