<?php
/**
 * Clase MenuPermisos
 * Gestión completa del sistema de menús, roles y permisos desde base de datos
 * 
 * @author Sistema Caracol Captain
 * @version 1.0
 */

class MenuPermisos {
    private $conn;
    private $usuario_id;
    
    /**
     * Constructor
     * @param PDO $conexion Conexión a la base de datos
     * @param int $usuario_id ID del usuario actual
     */
    public function __construct($conexion, $usuario_id = null) {
        $this->conn = $conexion;
        $this->usuario_id = $usuario_id;
    }
    
    /**
     * Obtiene el menú completo para un usuario específico
     * @param int $usuario_id ID del usuario (opcional, usa el del constructor si no se especifica)
     * @return array Array con los módulos y sus items
     */
    public function obtenerMenuUsuario($usuario_id = null) {
        $usuario_id = $usuario_id ?? $this->usuario_id;
        
        if (!$usuario_id) {
            return [];
        }
        
        $sql = "
            SELECT DISTINCT
                m.modulo_id,
                m.modulo_codigo,
                m.modulo_nombre,
                m.modulo_icono,
                m.modulo_orden,
                m.modulo_color,
                mi.item_id,
                mi.item_titulo,
                mi.item_link,
                mi.item_icono,
                mi.item_orden,
                mi.item_estilo
            FROM usuario_roles ur
            JOIN rol_permisos rp ON ur.rol_id = rp.rol_id AND rp.rp_activo = true
            JOIN menu_items mi ON rp.permiso_id = mi.permiso_requerido_id AND mi.item_activo = true
            JOIN modulos m ON mi.modulo_id = m.modulo_id AND m.modulo_activo = true
            WHERE ur.usuario_id = :usuario_id
              AND ur.ur_activo = true
              AND (ur.fecha_expiracion IS NULL OR ur.fecha_expiracion > CURRENT_TIMESTAMP)
            ORDER BY m.modulo_orden, mi.item_orden
        ";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['usuario_id' => $usuario_id]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Organizar los resultados por módulo
        $menu = [];
        foreach ($resultados as $row) {
            $modulo_id = $row['modulo_id'];
            
            if (!isset($menu[$modulo_id])) {
                $menu[$modulo_id] = [
                    'modulo_codigo' => $row['modulo_codigo'],
                    'modulo_nombre' => $row['modulo_nombre'],
                    'modulo_icono' => $row['modulo_icono'],
                    'modulo_orden' => $row['modulo_orden'],
                    'modulo_color' => $row['modulo_color'],
                    'items' => []
                ];
            }
            
            if ($row['item_id']) {
                $menu[$modulo_id]['items'][] = [
                    'item_id' => $row['item_id'],
                    'titulo' => $row['item_titulo'],
                    'link' => $row['item_link'],
                    'icono' => $row['item_icono'],
                    'orden' => $row['item_orden'],
                    'estilo' => $row['item_estilo']
                ];
            }
        }
        
        return array_values($menu);
    }
    
    /**
     * Verifica si un usuario tiene un permiso específico
     * @param string $permiso_codigo Código del permiso a verificar
     * @param int $usuario_id ID del usuario (opcional)
     * @return bool true si tiene el permiso, false en caso contrario
     */
    public function tienePermiso($permiso_codigo, $usuario_id = null) {
        $usuario_id = $usuario_id ?? $this->usuario_id;
        
        if (!$usuario_id) {
            return false;
        }
        
        $sql = "SELECT fn_usuario_tiene_permiso(:usuario_id, :permiso_codigo) as tiene_permiso";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            'usuario_id' => $usuario_id,
            'permiso_codigo' => $permiso_codigo
        ]);
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return (bool) $resultado['tiene_permiso'];
    }
    
    /**
     * Obtiene todos los permisos de un usuario
     * @param int $usuario_id ID del usuario
     * @return array Array con los permisos del usuario
     */
    public function obtenerPermisosUsuario($usuario_id = null) {
        $usuario_id = $usuario_id ?? $this->usuario_id;
        
        if (!$usuario_id) {
            return [];
        }
        
        $sql = "
            SELECT DISTINCT
                permiso_codigo,
                permiso_nombre,
                origen,
                origen_detalle
            FROM v_permisos_usuario
            WHERE usuario_id = :usuario_id
            ORDER BY permiso_codigo
        ";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['usuario_id' => $usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtiene los roles de un usuario
     * @param int $usuario_id ID del usuario
     * @return array Array con los roles del usuario
     */
    public function obtenerRolesUsuario($usuario_id = null) {
        $usuario_id = $usuario_id ?? $this->usuario_id;
        
        if (!$usuario_id) {
            return [];
        }
        
        $sql = "SELECT * FROM fn_obtener_roles_usuario(:usuario_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['usuario_id' => $usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Asigna un rol a un usuario
     * @param int $usuario_id ID del usuario
     * @param int $rol_id ID del rol
     * @param int $asignado_por ID del usuario que asigna
     * @return bool true si se asignó correctamente
     */
    public function asignarRolUsuario($usuario_id, $rol_id, $asignado_por = null) {
        try {
            $sql = "
                INSERT INTO usuario_roles (usuario_id, rol_id, asignado_por)
                VALUES (:usuario_id, :rol_id, :asignado_por)
                ON CONFLICT (usuario_id, rol_id) 
                DO UPDATE SET 
                    ur_activo = true,
                    fecha_asignacion = CURRENT_TIMESTAMP
            ";
            
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                'usuario_id' => $usuario_id,
                'rol_id' => $rol_id,
                'asignado_por' => $asignado_por
            ]);
        } catch (Exception $e) {
            error_log("Error asignando rol: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Remueve un rol de un usuario
     * @param int $usuario_id ID del usuario
     * @param int $rol_id ID del rol
     * @return bool true si se removió correctamente
     */
    public function removerRolUsuario($usuario_id, $rol_id) {
        try {
            $sql = "
                UPDATE usuario_roles 
                SET ur_activo = false
                WHERE usuario_id = :usuario_id AND rol_id = :rol_id
            ";
            
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                'usuario_id' => $usuario_id,
                'rol_id' => $rol_id
            ]);
        } catch (Exception $e) {
            error_log("Error removiendo rol: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Asigna un permiso específico a un usuario
     * @param int $usuario_id ID del usuario
     * @param int $permiso_id ID del permiso
     * @param bool $concedido true para conceder, false para revocar
     * @param string $razon Razón de la asignación
     * @param int $asignado_por ID del usuario que asigna
     * @return bool true si se asignó correctamente
     */
    public function asignarPermisoUsuario($usuario_id, $permiso_id, $concedido = true, $razon = null, $asignado_por = null) {
        try {
            $sql = "
                INSERT INTO usuario_permisos (usuario_id, permiso_id, up_concedido, up_razon, asignado_por)
                VALUES (:usuario_id, :permiso_id, :up_concedido, :up_razon, :asignado_por)
                ON CONFLICT (usuario_id, permiso_id) 
                DO UPDATE SET 
                    up_concedido = :up_concedido,
                    up_razon = :up_razon,
                    fecha_asignacion = CURRENT_TIMESTAMP
            ";
            
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                'usuario_id' => $usuario_id,
                'permiso_id' => $permiso_id,
                'up_concedido' => $concedido,
                'up_razon' => $razon,
                'asignado_por' => $asignado_por
            ]);
        } catch (Exception $e) {
            error_log("Error asignando permiso: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene todos los roles disponibles
     * @return array Array con todos los roles
     */
    public function obtenerTodosRoles() {
        $sql = "
            SELECT rol_id, rol_codigo, rol_nombre, rol_descripcion, rol_nivel
            FROM roles
            WHERE rol_activo = true
            ORDER BY rol_nivel
        ";
        
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtiene todos los permisos disponibles
     * @return array Array con todos los permisos
     */
    public function obtenerTodosPermisos() {
        $sql = "
            SELECT permiso_id, permiso_codigo, permiso_nombre, permiso_descripcion, permiso_modulo
            FROM permisos
            WHERE permiso_activo = true
            ORDER BY permiso_modulo, permiso_codigo
        ";
        
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtiene los permisos de un rol específico
     * @param int $rol_id ID del rol
     * @return array Array con los permisos del rol
     */
    public function obtenerPermisosRol($rol_id) {
        $sql = "
            SELECT p.permiso_id, p.permiso_codigo, p.permiso_nombre, p.permiso_descripcion
            FROM rol_permisos rp
            JOIN permisos p ON rp.permiso_id = p.permiso_id
            WHERE rp.rol_id = :rol_id AND rp.rp_activo = true AND p.permiso_activo = true
            ORDER BY p.permiso_codigo
        ";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['rol_id' => $rol_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Asigna un permiso a un rol
     * @param int $rol_id ID del rol
     * @param int $permiso_id ID del permiso
     * @param int $asignado_por ID del usuario que asigna
     * @return bool true si se asignó correctamente
     */
    public function asignarPermisoRol($rol_id, $permiso_id, $asignado_por = null) {
        try {
            $sql = "
                INSERT INTO rol_permisos (rol_id, permiso_id, asignado_por)
                VALUES (:rol_id, :permiso_id, :asignado_por)
                ON CONFLICT (rol_id, permiso_id) 
                DO UPDATE SET 
                    rp_activo = true,
                    fecha_asignacion = CURRENT_TIMESTAMP
            ";
            
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                'rol_id' => $rol_id,
                'permiso_id' => $permiso_id,
                'asignado_por' => $asignado_por
            ]);
        } catch (Exception $e) {
            error_log("Error asignando permiso a rol: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Remueve un permiso de un rol
     * @param int $rol_id ID del rol
     * @param int $permiso_id ID del permiso
     * @return bool true si se removió correctamente
     */
    public function removerPermisoRol($rol_id, $permiso_id) {
        try {
            $sql = "
                UPDATE rol_permisos 
                SET rp_activo = false
                WHERE rol_id = :rol_id AND permiso_id = :permiso_id
            ";
            
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                'rol_id' => $rol_id,
                'permiso_id' => $permiso_id
            ]);
        } catch (Exception $e) {
            error_log("Error removiendo permiso de rol: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Genera el HTML del menú para el usuario actual
     * @param string $template Plantilla HTML a usar (opcional)
     * @return string HTML del menú
     */
    public function generarHTMLMenu($template = null) {
        $menu = $this->obtenerMenuUsuario();
        
        if (empty($menu)) {
            return '<div class="alert alert-warning">No tiene acceso a ningún módulo.</div>';
        }
        
        $html = '';
        
        foreach ($menu as $modulo) {
            $html .= '<li class="nav-item has-treeview">';
            $html .= '  <a href="#" class="nav-link">';
            $html .= '    <i class="nav-icon ' . htmlspecialchars($modulo['modulo_icono']) . '"></i>';
            $html .= '    <p>' . htmlspecialchars($modulo['modulo_nombre']);
            $html .= '      <i class="right fas fa-angle-left"></i>';
            $html .= '    </p>';
            $html .= '  </a>';
            
            if (!empty($modulo['items'])) {
                $html .= '  <ul class="nav nav-treeview">';
                
                foreach ($modulo['items'] as $item) {
                    $estilo = $item['estilo'] ? ' style="' . htmlspecialchars($item['estilo']) . '"' : '';
                    $html .= '    <li class="nav-item">';
                    $html .= '      <a href="' . htmlspecialchars($item['link']) . '" class="nav-link"' . $estilo . '>';
                    
                    if ($item['icono']) {
                        $html .= '        <i class="' . htmlspecialchars($item['icono']) . ' nav-icon"></i>';
                    } else {
                        $html .= '        <i class="far fa-circle nav-icon"></i>';
                    }
                    
                    $html .= '        <p>' . htmlspecialchars($item['titulo']) . '</p>';
                    $html .= '      </a>';
                    $html .= '    </li>';
                }
                
                $html .= '  </ul>';
            }
            
            $html .= '</li>';
        }
        
        return $html;
    }
    
    /**
     * Verifica si el usuario puede acceder a una página específica
     * @param string $pagina Nombre de la página (ej: 'usuario.php')
     * @param int $usuario_id ID del usuario (opcional)
     * @return bool true si puede acceder, false en caso contrario
     */
    public function puedeAccederPagina($pagina, $usuario_id = null) {
        $usuario_id = $usuario_id ?? $this->usuario_id;
        
        if (!$usuario_id) {
            return false;
        }
        
        // Buscar el permiso requerido para la página
        $sql = "
            SELECT p.permiso_codigo
            FROM menu_items mi
            JOIN permisos p ON mi.permiso_requerido_id = p.permiso_id
            WHERE mi.item_link = :pagina AND mi.item_activo = true
            LIMIT 1
        ";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['pagina' => $pagina]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$resultado) {
            // Si no se encuentra la página en el menú, verificar páginas públicas
            return $this->esPaginaPublica($pagina);
        }
        
        return $this->tienePermiso($resultado['permiso_codigo'], $usuario_id);
    }
    
    /**
     * Verifica si una página es pública (no requiere permisos especiales)
     * @param string $pagina Nombre de la página
     * @return bool true si es pública
     */
    private function esPaginaPublica($pagina) {
        $paginasPublicas = ['index.php', 'dashboard.php', 'profile.php', 'help.php', 'about.php'];
        return in_array($pagina, $paginasPublicas);
    }
}