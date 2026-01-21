<?php

/**
 * classes/MenuManager.php
 * Clase para gestionar menús, permisos y páginas específicas
 * Adaptada para el sistema Caracol Captain
 */

class MenuManager
{
    private $config;
    private $userRole;
    private $userPermissions;

    public function __construct($userRole)
    {
        // Cargar configuración
        $configPath = 'menu_config.php';
        if (!file_exists($configPath)) {
            throw new Exception("Archivo de configuración no encontrado: {$configPath}");
        }
        
        $this->config = require($configPath);
        $this->userRole = $userRole;
        $this->userPermissions = $this->getUserPermissions($userRole);
    }

    /**
     * Obtiene los permisos del usuario según su rol
     */
    private function getUserPermissions($role)
    {
        return $this->config['permissions'][$role] ?? [];
    }

    /**
     * Verifica si el usuario tiene un permiso específico
     */
    public function hasPermission($permission)
    {
        return in_array($permission, $this->userPermissions);
    }

    /**
     * Verifica si el usuario puede acceder a un menú
     */
    public function canAccessMenu($menuId)
    {
        $menu = $this->config['menu'][$menuId] ?? null;

        if (!$menu) {
            return false;
        }

        return in_array($this->userRole, $menu['roles']);
    }

    /**
     * Verifica si el usuario puede acceder a un elemento del menú
     */
    public function canAccessMenuItem($item)
    {
        // Si no tiene permiso específico definido, permitir acceso
        if (!isset($item['permission'])) {
            return true;
        }

        return $this->hasPermission($item['permission']);
    }

    /**
     * Verifica si el usuario puede acceder a una página específica
     */
    public function canAccessSpecificPage($pageName)
    {
        // Verificar si la página está en las páginas públicas
        if (in_array($pageName, $this->config['publicPages'])) {
            return true;
        }

        // Verificar si la página está configurada en páginas específicas
        if (isset($this->config['specificPages'][$pageName])) {
            $pageConfig = $this->config['specificPages'][$pageName];

            // Verificar rol
            if (!in_array($this->userRole, $pageConfig['roles'])) {
                return false;
            }

            // Verificar permiso específico si está definido
            if (isset($pageConfig['permission'])) {
                return $this->hasPermission($pageConfig['permission']);
            }

            return true;
        }

        // Si no está configurada, buscar en los menús
        return $this->canAccessPage($pageName);
    }

    /**
     * Obtiene información de una página específica
     */
    public function getPageInfo($pageName)
    {
        if (isset($this->config['specificPages'][$pageName])) {
            return $this->config['specificPages'][$pageName];
        }

        return null;
    }

    /**
     * Obtiene todas las páginas específicas accesibles por el usuario
     */
    public function getAccessibleSpecificPages()
    {
        $accessiblePages = [];

        foreach ($this->config['specificPages'] as $pageName => $pageConfig) {
            if ($this->canAccessSpecificPage($pageName)) {
                $accessiblePages[$pageName] = $pageConfig;
            }
        }

        return $accessiblePages;
    }

    /**
     * Obtiene los menús filtrados según los permisos del usuario
     */
    public function getFilteredMenus()
    {
        $filteredMenus = [];

        foreach ($this->config['menu'] as $menuId => $menu) {
            if ($this->canAccessMenu($menuId)) {
                $filteredMenu = $menu;

                // Filtrar elementos del menú según permisos
                $filteredMenu['items'] = array_filter($menu['items'], function ($item) {
                    return $this->canAccessMenuItem($item);
                });

                // Solo incluir el menú si tiene elementos después del filtrado
                if (!empty($filteredMenu['items'])) {
                    $filteredMenus[$menuId] = $filteredMenu;
                }
            }
        }

        // Ordenar menús según el orden definido
        uasort($filteredMenus, function ($a, $b) {
            $orderA = $a['order'] ?? 999;
            $orderB = $b['order'] ?? 999;
            return $orderA <=> $orderB;
        });

        return $filteredMenus;
    }

    /**
     * Obtiene los accesos rápidos filtrados según permisos
     */
    public function getFilteredQuickAccess()
    {
        $filteredQuickAccess = [];

        foreach ($this->config['quickAccess'] as $accessId => $access) {
            // Verificar rol
            if (!in_array($this->userRole, $access['roles'])) {
                continue;
            }

            // Verificar permiso si está definido
            if (isset($access['permission']) && !$this->hasPermission($access['permission'])) {
                continue;
            }

            $filteredQuickAccess[$accessId] = $access;
        }

        return $filteredQuickAccess;
    }

    /**
     * Genera el HTML del menú
     */
    public function renderMenu()
    {
        $menus = $this->getFilteredMenus();
        $html = '';

        foreach ($menus as $menuId => $menu) {
            $html .= '<li class="nav-item">';
            $html .= '<a data-bs-toggle="collapse" href="#' . $menuId . '" class="collapsed" aria-expanded="false">';
            $html .= '<i class="' . $menu['icon'] . '"></i>';
            $html .= '<p>' . $menu['title'] . '</p>';
            $html .= '<span class="caret"></span>';
            $html .= '</a>';
            $html .= '<div class="collapse" id="' . $menuId . '">';
            $html .= '<ul class="nav nav-collapse">';

            foreach ($menu['items'] as $item) {
                $target = isset($item['target']) ? ' target="' . $item['target'] . '"' : '';
                $style = isset($item['style']) ? ' style="' . $item['style'] . '"' : '';

                // Construir el link con parámetros si existen
                $link = $item['link'];
                if (isset($item['params']) && !empty($item['params'])) {
                    $link .= '?' . http_build_query($item['params']);
                }

                $html .= '<li>';
                $html .= '<a href="' . htmlspecialchars($link) . '"' . $target . '>';
                $html .= '<span class="sub-item"' . $style . '>' . $item['title'] . '</span>';
                $html .= '</a>';
                $html .= '</li>';
            }

            $html .= '</ul>';
            $html .= '</div>';
            $html .= '</li>';
        }

        return $html;
    }

    /**
     * Genera el HTML de accesos rápidos
     */
    public function renderQuickAccess()
    {
        $quickAccess = $this->getFilteredQuickAccess();
        $html = '';

        foreach ($quickAccess as $access) {
            $html .= '<a class="col-6 col-md-4 p-0" href="' . htmlspecialchars($access['link']) . '">';
            $html .= '<div class="quick-actions-item">';
            $html .= '<div class="avatar-item ' . $access['color'] . ' rounded-circle">';
            $html .= '<i class="' . $access['icon'] . '"></i>';
            $html .= '</div>';
            $html .= '<span class="text">' . htmlspecialchars($access['title']) . '</span>';
            $html .= '</div>';
            $html .= '</a>';
        }

        return $html;
    }

    /**
     * Verifica si el usuario puede acceder a una página específica (busca en menús)
     */
    public function canAccessPage($page)
    {
        // Buscar la página en todos los menús
        foreach ($this->config['menu'] as $menuId => $menu) {
            if ($this->canAccessMenu($menuId)) {
                foreach ($menu['items'] as $item) {
                    if ($item['link'] === $page && $this->canAccessMenuItem($item)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Middleware para proteger páginas automáticamente
     */
    public function requirePageAccess($pageName, $redirectTo = 'index.php')
    {
        if (!$this->canAccessSpecificPage($pageName)) {
            $pageInfo = $this->getPageInfo($pageName);
            $requiredPermission = $pageInfo['permission'] ?? 'acceso a esta página';

            $_SESSION['error'] = "No tienes permisos para acceder a esta página. Permiso requerido: " . $requiredPermission;
            $_SESSION['error_details'] = [
                'page' => $pageName,
                'user_role' => $this->userRole,
                'required_permission' => $requiredPermission,
                'user_permissions' => $this->userPermissions
            ];

            // Registrar intento de acceso no autorizado
            $this->logUnauthorizedAccess($pageName);

            header("Location: " . $redirectTo . "?error=access_denied");
            exit();
        }
    }

    /**
     * Registra intento de acceso no autorizado
     */
    public function logUnauthorizedAccess($pageName)
    {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'user_role' => $this->userRole,
            'attempted_page' => $pageName,
            'user_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ];

        // Escribir a log file
        error_log("Unauthorized access attempt: " . json_encode($logData));
        
        // También se podría guardar en base de datos si es necesario
        // $this->saveToDatabase($logData);
    }

    /**
     * Obtiene información del rol del usuario
     */
    public function getUserRole()
    {
        return $this->userRole;
    }

    /**
     * Obtiene todos los permisos del usuario
     */
    public function getAllUserPermissions()
    {
        return $this->userPermissions;
    }

    /**
     * Obtiene estadísticas del usuario
     */
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

    /**
     * Método de compatibilidad con helper_permisos
     * Permite usar tienePermiso() de manera similar
     */
    public function tienePermiso($moduloPermiso = '', $permisoEspecifico = '')
    {
        // Si no se proporciona permiso específico, verificar solo el módulo
        if (empty($permisoEspecifico)) {
            // Buscar si el módulo existe como rol
            if (empty($moduloPermiso)) {
                return true; // Sin restricción
            }
            return $this->userRole === $moduloPermiso;
        }

        // Verificar permiso específico
        return $this->hasPermission($permisoEspecifico);
    }
}