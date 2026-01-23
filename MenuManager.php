<?php
/**
 * MenuManager.php
 * VERSIÓN SIMPLIFICADA - Usa executeQuerymenumanager() global
 */

require_once("logica/bd.php");

// Función global que ya tienes
function executeQuerymenumanager(string $query, array $params = []): array
{
    global $conectar;
    try {
        $orden = $conectar->prepare($query);
        $orden->execute($params);
        $datos = $orden->fetchAll(PDO::FETCH_ASSOC);
        $orden->closeCursor();
        return $datos;
    } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
        return [];
    }
}

class MenuManager
{
    private $userRole;
    private $userPermissions = [];
    private $cacheEnabled = true;
    private $cacheTime = 3600;
    
    public function __construct($userRole)
    {
        $this->userRole = $userRole;
        $this->loadUserPermissions();
    }
    
    /**
     * ✅ Carga permisos usando executeQuerymenumanager()
     */
    private function loadUserPermissions()
    {
        $cacheKey = "permissions_" . $this->userRole;
        
        // Verificar cache
        if ($this->cacheEnabled && isset($_SESSION[$cacheKey])) {
            $cached = $_SESSION[$cacheKey];
            if (time() - $cached['time'] < $this->cacheTime) {
                $this->userPermissions = $cached['data'];
                return;
            }
        }
        
        // ✅ Cargar permisos desde BD
        $query = "
            SELECT DISTINCT p.codigo
            FROM permisos p
            INNER JOIN rol_permiso rp ON p.id = rp.permiso_id
            INNER JOIN roles r ON rp.rol_id = r.id_rol
            WHERE r.nombre = :nombre_rol AND p.activo = true AND r.activo = true
        ";
        
        $results = executeQuerymenumanager($query, ['nombre_rol' => $this->userRole]);
        
        $this->userPermissions = [];
        foreach ($results as $row) {
            $this->userPermissions[] = $row['codigo'];
        }
        
        // Guardar en cache
        if ($this->cacheEnabled) {
            $_SESSION[$cacheKey] = [
                'data' => $this->userPermissions,
                'time' => time()
            ];
        }
        
        // Log para debug
        error_log("MenuManager: Permisos cargados para rol '{$this->userRole}': " . count($this->userPermissions));
    }
    
    public function hasPermission($permission)
    {
        return in_array($permission, $this->userPermissions);
    }
    
    public function canAccessMenu($menuId)
    {
        $query = "
            SELECT COUNT(*) as tiene_acceso
            FROM menus m
            INNER JOIN menu_rol mr ON m.id = mr.menu_id
            INNER JOIN roles r ON mr.rol_id = r.id_rol
            WHERE m.id = :menu_id AND r.nombre = :nombre_rol AND m.activo = true AND r.activo = true
        ";
        
        $result = executeQuerymenumanager($query, [
            'menu_id' => $menuId,
            'nombre_rol' => $this->userRole
        ]);
        
        return !empty($result) && $result[0]['tiene_acceso'] > 0;
    }
    
    public function canAccessMenuItem($item)
    {
        if (empty($item['permiso_codigo'])) {
            return true;
        }
        return $this->hasPermission($item['permiso_codigo']);
    }
    
    public function canAccessSpecificPage($pageName)
    {
        // Verificar si es página pública
        $query = "
            SELECT COUNT(*) as es_publica
            FROM paginas_publicas
            WHERE nombre_archivo = :nombre_archivo AND activo = true
        ";
        
        $result = executeQuerymenumanager($query, ['nombre_archivo' => $pageName]);
        if (!empty($result) && $result[0]['es_publica'] > 0) {
            return true;
        }
        
        // Verificar página específica
        $query = "
            SELECT pe.id, p.codigo as permiso_codigo
            FROM paginas_especificas pe
            LEFT JOIN permisos p ON pe.permiso_id = p.id
            INNER JOIN pagina_rol pr ON pe.id = pr.pagina_id
            INNER JOIN roles r ON pr.rol_id = r.id_rol
            WHERE pe.nombre_archivo = :nombre_archivo 
                AND r.nombre = :nombre_rol
                AND pe.activo = true 
                AND r.activo = true
        ";
        
        $result = executeQuerymenumanager($query, [
            'nombre_archivo' => $pageName,
            'nombre_rol' => $this->userRole
        ]);
        
        if (empty($result)) {
            return $this->canAccessPage($pageName);
        }
        
        if (!empty($result[0]['permiso_codigo'])) {
            return $this->hasPermission($result[0]['permiso_codigo']);
        }
        
        return true;
    }
    
    public function getPageInfo($pageName)
    {
        $query = "
            SELECT pe.*, p.codigo as permiso_codigo, p.nombre as permiso_nombre
            FROM paginas_especificas pe
            LEFT JOIN permisos p ON pe.permiso_id = p.id
            WHERE pe.nombre_archivo = :nombre_archivo AND pe.activo = true
        ";
        
        $result = executeQuerymenumanager($query, ['nombre_archivo' => $pageName]);
        return !empty($result) ? $result[0] : null;
    }
    
    public function getAccessibleSpecificPages()
    {
        $query = "
            SELECT DISTINCT pe.*
            FROM paginas_especificas pe
            INNER JOIN pagina_rol pr ON pe.id = pr.pagina_id
            INNER JOIN roles r ON pr.rol_id = r.id_rol
            WHERE r.nombre = :nombre_rol AND pe.activo = true
        ";
        
        return executeQuerymenumanager($query, ['nombre_rol' => $this->userRole]);
    }
    
    public function getFilteredMenus()
    {
        $cacheKey = "filtered_menus_" . $this->userRole;
        
        // Verificar cache
        if ($this->cacheEnabled && isset($_SESSION[$cacheKey])) {
            $cached = $_SESSION[$cacheKey];
            if (time() - $cached['time'] < $this->cacheTime) {
                return $cached['data'];
            }
        }
        
        // Cargar menús
        $query = "
            SELECT DISTINCT m.id, m.codigo, m.titulo, m.icono, m.orden
            FROM menus m
            INNER JOIN menu_rol mr ON m.id = mr.menu_id
            INNER JOIN roles r ON mr.rol_id = r.id_rol
            WHERE r.nombre = :nombre_rol AND m.activo = true AND r.activo = true
            ORDER BY m.orden ASC
        ";
        
        $menus = executeQuerymenumanager($query, ['nombre_rol' => $this->userRole]);
        $filteredMenus = [];
        
        foreach ($menus as $menu) {
            $items = $this->getMenuItems($menu['id']);
            
            if (!empty($items)) {
                $menu['items'] = $items;
                $filteredMenus[$menu['codigo']] = $menu;
            }
        }
        
        // Guardar en cache
        if ($this->cacheEnabled) {
            $_SESSION[$cacheKey] = [
                'data' => $filteredMenus,
                'time' => time()
            ];
        }
        
        return $filteredMenus;
    }
    
    private function getMenuItems($menuId)
    {
        $query = "
            SELECT 
                mi.id,
                mi.titulo,
                mi.link,
                mi.parametros,
                mi.target,
                mi.estilo,
                mi.orden,
                p.codigo as permiso_codigo
            FROM menu_items mi
            LEFT JOIN permisos p ON mi.permiso_id = p.id
            WHERE mi.menu_id = :menu_id AND mi.activo = true
            ORDER BY mi.orden ASC
        ";
        
        $allItems = executeQuerymenumanager($query, ['menu_id' => $menuId]);
        $items = [];
        
        foreach ($allItems as $item) {
            if ($this->canAccessMenuItem($item)) {
                if (!empty($item['parametros'])) {
                    $item['params'] = json_decode($item['parametros'], true);
                }
                $items[] = $item;
            }
        }
        
        return $items;
    }
    
    public function getFilteredQuickAccess()
    {
        $cacheKey = "quick_access_" . $this->userRole;
        
        // Verificar cache
        if ($this->cacheEnabled && isset($_SESSION[$cacheKey])) {
            $cached = $_SESSION[$cacheKey];
            if (time() - $cached['time'] < $this->cacheTime) {
                return $cached['data'];
            }
        }
        
        // Cargar accesos rápidos
        $query = "
            SELECT DISTINCT 
                ar.id,
                ar.codigo,
                ar.titulo,
                ar.link,
                ar.icono,
                ar.color,
                ar.orden,
                p.codigo as permiso_codigo
            FROM accesos_rapidos ar
            INNER JOIN acceso_rapido_rol arr ON ar.id = arr.acceso_rapido_id
            INNER JOIN roles r ON arr.rol_id = r.id_rol
            LEFT JOIN permisos p ON ar.permiso_id = p.id
            WHERE r.nombre = :nombre_rol AND ar.activo = true AND r.activo = true
            ORDER BY ar.orden ASC
        ";
        
        $allAccess = executeQuerymenumanager($query, ['nombre_rol' => $this->userRole]);
        $filteredQuickAccess = [];
        
        foreach ($allAccess as $acceso) {
            if (empty($acceso['permiso_codigo']) || $this->hasPermission($acceso['permiso_codigo'])) {
                $filteredQuickAccess[$acceso['codigo']] = $acceso;
            }
        }
        
        // Guardar en cache
        if ($this->cacheEnabled) {
            $_SESSION[$cacheKey] = [
                'data' => $filteredQuickAccess,
                'time' => time()
            ];
        }
        
        return $filteredQuickAccess;
    }
    
    public function renderMenu()
    {
        $menus = $this->getFilteredMenus();
        $html = '';
        
        foreach ($menus as $menuCodigo => $menu) {
            $html .= '<li class="nav-item">';
            $html .= '<a data-bs-toggle="collapse" href="#' . htmlspecialchars($menuCodigo) . '" class="collapsed" aria-expanded="false">';
            $html .= '<i class="' . htmlspecialchars($menu['icono']) . '"></i>';
            $html .= '<p>' . htmlspecialchars($menu['titulo']) . '</p>';
            $html .= '<span class="caret"></span>';
            $html .= '</a>';
            $html .= '<div class="collapse" id="' . htmlspecialchars($menuCodigo) . '">';
            $html .= '<ul class="nav nav-collapse">';
            
            foreach ($menu['items'] as $item) {
                $target = !empty($item['target']) ? ' target="' . htmlspecialchars($item['target']) . '"' : '';
                $style = !empty($item['estilo']) ? ' style="' . htmlspecialchars($item['estilo']) . '"' : '';
                
                $link = $item['link'];
                if (isset($item['params']) && !empty($item['params'])) {
                    $link .= '?' . http_build_query($item['params']);
                }
                
                $html .= '<li>';
                $html .= '<a href="' . htmlspecialchars($link) . '"' . $target . '>';
                $html .= '<span class="sub-item"' . $style . '>' . htmlspecialchars($item['titulo']) . '</span>';
                $html .= '</a>';
                $html .= '</li>';
            }
            
            $html .= '</ul>';
            $html .= '</div>';
            $html .= '</li>';
        }
        
        return $html;
    }
    
    public function renderQuickAccess()
    {
        $quickAccess = $this->getFilteredQuickAccess();
        $html = '';
        
        foreach ($quickAccess as $acceso) {
            $html .= '<a class="col-6 col-md-4 p-0" href="' . htmlspecialchars($acceso['link']) . '">';
            $html .= '<div class="quick-actions-item">';
            $html .= '<div class="avatar-item ' . htmlspecialchars($acceso['color']) . ' rounded-circle">';
            $html .= '<i class="' . htmlspecialchars($acceso['icono']) . '"></i>';
            $html .= '</div>';
            $html .= '<span class="text">' . htmlspecialchars($acceso['titulo']) . '</span>';
            $html .= '</div>';
            $html .= '</a>';
        }
        
        return $html;
    }
    
    public function canAccessPage($page)
    {
        $query = "
            SELECT COUNT(*) as tiene_acceso
            FROM menu_items mi
            INNER JOIN menus m ON mi.menu_id = m.id
            INNER JOIN menu_rol mr ON m.id = mr.menu_id
            INNER JOIN roles r ON mr.rol_id = r.id_rol
            LEFT JOIN permisos p ON mi.permiso_id = p.id
            WHERE mi.link = :link 
                AND r.nombre = :nombre_rol 
                AND mi.activo = true 
                AND m.activo = true
                AND r.activo = true
        ";
        
        $result = executeQuerymenumanager($query, [
            'link' => $page,
            'nombre_rol' => $this->userRole
        ]);
        
        if (empty($result) || $result[0]['tiene_acceso'] == 0) {
            return false;
        }
        
        $query2 = "
            SELECT p.codigo as permiso_codigo
            FROM menu_items mi
            LEFT JOIN permisos p ON mi.permiso_id = p.id
            WHERE mi.link = :link AND mi.activo = true
            LIMIT 1
        ";
        
        $result2 = executeQuerymenumanager($query2, ['link' => $page]);
        
        if (!empty($result2) && !empty($result2[0]['permiso_codigo'])) {
            return $this->hasPermission($result2[0]['permiso_codigo']);
        }
        
        return true;
    }
    
    public function requirePageAccess($pageName, $redirectTo = 'index.php')
    {
        if (!$this->canAccessSpecificPage($pageName)) {
            $pageInfo = $this->getPageInfo($pageName);
            $requiredPermission = $pageInfo['permiso_nombre'] ?? 'acceso a esta página';
            
            $_SESSION['error'] = "No tienes permisos para acceder a esta página. Permiso requerido: " . $requiredPermission;
            $_SESSION['error_details'] = [
                'page' => $pageName,
                'user_role' => $this->userRole,
                'required_permission' => $requiredPermission,
                'user_permissions' => $this->userPermissions
            ];
            
            $this->logUnauthorizedAccess($pageName, $requiredPermission);
            
            header("Location: " . $redirectTo . "?error=access_denied");
            exit();
        }
    }
    
    public function logUnauthorizedAccess($pageName, $requiredPermission = '')
    {
        global $conectar;
        
        $query = "
            INSERT INTO log_accesos_no_autorizados 
            (usuario_id, rol_usuario, pagina_solicitada, permiso_requerido, ip_address, user_agent) 
            VALUES (:usuario_id, :rol_usuario, :pagina_solicitada, :permiso_requerido, :ip_address, :user_agent)
        ";
        
        $usuarioId = $_SESSION['id'] ?? null;
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        try {
            $stmt = $conectar->prepare($query);
            $stmt->execute([
                'usuario_id' => $usuarioId,
                'rol_usuario' => $this->userRole,
                'pagina_solicitada' => $pageName,
                'permiso_requerido' => $requiredPermission,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent
            ]);
        } catch (PDOException $e) {
            error_log("Error logging unauthorized access: " . $e->getMessage());
        }
        
        error_log("Unauthorized access attempt - User: {$usuarioId}, Role: {$this->userRole}, Page: {$pageName}");
    }
    
    public function getUserRole()
    {
        return $this->userRole;
    }
    
    public function getAllUserPermissions()
    {
        return $this->userPermissions;
    }
    
    public function getUserStats()
    {
        return [
            'role' => $this->userRole,
            'total_permissions' => count($this->userPermissions),
            'accessible_menus' => count($this->getFilteredMenus()),
            'accessible_specific_pages' => count($this->getAccessibleSpecificPages()),
            'quick_access_items' => count($this->getFilteredQuickAccess())
        ];
    }
    
    public function tienePermiso($moduloPermiso = '', $permisoEspecifico = '')
    {
        if (empty($permisoEspecifico)) {
            if (empty($moduloPermiso)) {
                return true;
            }
            return $this->userRole === $moduloPermiso;
        }
        
        return $this->hasPermission($permisoEspecifico);
    }
    
    public function clearCache()
    {
        $keys = [
            "permissions_" . $this->userRole,
            "filtered_menus_" . $this->userRole,
            "quick_access_" . $this->userRole
        ];
        
        foreach ($keys as $key) {
            if (isset($_SESSION[$key])) {
                unset($_SESSION[$key]);
            }
        }
    }
    
    public static function clearAllCache()
    {
        if (!isset($_SESSION)) {
            return;
        }
        
        foreach ($_SESSION as $key => $value) {
            if (strpos($key, 'permissions_') === 0 || 
                strpos($key, 'filtered_menus_') === 0 || 
                strpos($key, 'quick_access_') === 0) {
                unset($_SESSION[$key]);
            }
        }
    }
    
    public function getAllRoles()
    {
        $query = "SELECT * FROM roles WHERE activo = true ORDER BY nombre";
        return executeQuerymenumanager($query);
    }
    
    public function getAllPermisos()
    {
        $query = "SELECT * FROM permisos WHERE activo = true ORDER BY categoria, nombre";
        return executeQuerymenumanager($query);
    }
    
    public function rolTienePermiso($rolId, $permisoId)
    {
        $query = "
            SELECT COUNT(*) as tiene 
            FROM rol_permiso 
            WHERE rol_id = :rol_id AND permiso_id = :permiso_id
        ";
        
        $result = executeQuerymenumanager($query, [
            'rol_id' => $rolId,
            'permiso_id' => $permisoId
        ]);
        
        return !empty($result) && $result[0]['tiene'] > 0;
    }
    
    public function asignarPermiso($rolId, $permisoId)
    {
        global $conectar;
        
        $query = "
            INSERT INTO rol_permiso (rol_id, permiso_id) 
            VALUES (:rol_id, :permiso_id)
        ";
        
        try {
            $stmt = $conectar->prepare($query);
            $result = $stmt->execute([
                'rol_id' => $rolId,
                'permiso_id' => $permisoId
            ]);
            
            if ($result) {
                self::clearAllCache();
            }
            
            return $result;
        } catch (PDOException $e) {
            // Si es duplicado, no es error
            if ($e->getCode() == '23000') {
                return true;
            }
            error_log("Error asignar permiso: " . $e->getMessage());
            return false;
        }
    }
    
    public function removerPermiso($rolId, $permisoId)
    {
        global $conectar;
        
        $query = "
            DELETE FROM rol_permiso 
            WHERE rol_id = :rol_id AND permiso_id = :permiso_id
        ";
        
        try {
            $stmt = $conectar->prepare($query);
            $result = $stmt->execute([
                'rol_id' => $rolId,
                'permiso_id' => $permisoId
            ]);
            
            if ($result) {
                self::clearAllCache();
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error remover permiso: " . $e->getMessage());
            return false;
        }
    }
}