<?php
// menu_config.php
const ROLES = [
    'ADMINISTRADOR' => 'ADMINISTRADOR',
    'VENDEDOR' => 'VENDEDOR',
    'ALMACENERO' => 'almacenero',
    'CAJERO' => 'cajero',
    'GERENTE' => 'gerente',
];

/**
 * Configuración principal del menú
 */
$menuConfig = [
    'moduloAdmin' => [
        'roles' => ['ADMINISTRADOR'],
        'icon' => 'fas fa-cog',
        'title' => 'ADMINISTRADOR',
        'order' => 1,
        'items' => [
            [
                'title' => 'Trabajadores',
                'link' => 'Empleados.php',
                'permission' => 'admin_trabajadores'
            ],
            [
                'title' => 'Usuarios',
                'link' => 'usuario.php',
                'permission' => 'admin_usuarios'
            ],
            [
                'title' => 'Personas',
                'link' => 'persona.php',
                'permission' => 'admin_personas'
            ],
            [
                'title' => 'Artículos',
                'link' => 'articulos.php',
                'permission' => 'admin_articulos'
            ],
            [
                'title' => 'Mantenimientos',
                'link' => 'mantenimiento.php',
                'permission' => 'admin_mantenimientos'
            ],
            [
                'title' => 'Presentaciones',
                'link' => 'presentaciones.php',
                'permission' => 'admin_presentaciones'
            ],
            [
                'title' => 'Roles y Permisos',
                'link' => 'roles.php',
                'permission' => 'admin_roles'
            ],
        ]
    ],
    
    'moduloNegocio' => [
        'roles' => ['ADMINISTRADOR', 'gerente', 'cajero'],
        'icon' => 'fas fa-store-alt',
        'title' => 'Negocio',
        'order' => 2,
        'items' => [
            [
                'title' => 'Gestionar de Compras',
                'link' => 'compra.php',
                'permission' => 'negocio_compras'
            ],
            [
                'title' => 'Caja Chica',
                'link' => 'cajaChica.php',
                'permission' => 'negocio_caja_chica'
            ],
            [
                'title' => 'Manejo de Caja',
                'link' => 'manejoCaja.php',
                'permission' => 'negocio_manejo_caja'
            ],
        ]
    ],
    
    'moduloFacturadorSunat' => [
        'roles' => ['ADMINISTRADOR', 'gerente', 'cajero'],
        'icon' => 'fab fa-stripe-s',
        'title' => 'Facturador SUNAT',
        'order' => 3,
        'items' => [
            [
                'title' => 'Datos de Emisor',
                'link' => 'emisor.php',
                'permission' => 'sunat_emisor'
            ],
            [
                'title' => 'Declarar Comprobantes a SUNAT',
                'link' => 'listVentasForPagosSunat.php',
                'permission' => 'sunat_declarar'
            ],
            [
                'title' => 'Comprobantes Declarados',
                'link' => 'listComprobantesDeclarados.php',
                'permission' => 'sunat_declarados'
            ],
            [
                'title' => 'Comprobantes NO Declarados',
                'link' => 'comprobantes_no_declarados.php',
                'permission' => 'sunat_no_declarados',
                'style' => 'color: red;'
            ],
        ]
    ],
    
    'moduloDatos' => [
        'roles' => ['ADMINISTRADOR', 'gerente'],
        'icon' => 'fas fa-file-powerpoint',
        'title' => 'Datos',
        'order' => 4,
        'items' => [
            [
                'title' => 'ETL para Power BI',
                'link' => 'etl.php',
                'permission' => 'datos_etl'
            ],
        ]
    ],
    
    'moduloCredito' => [
        'roles' => ['ADMINISTRADOR', 'gerente', 'cajero', 'VENDEDOR'],
        'icon' => 'fas fa-user-lock',
        'title' => 'Crédito',
        'order' => 5,
        'items' => [
            [
                'title' => 'Realizar Abono a Crédito',
                'link' => 'pagoCredito.php',
                'permission' => 'credito_abono'
            ],
            [
                'title' => 'Historial de Clientes',
                'link' => 'historialClientes.php',
                'permission' => 'credito_historial'
            ],
        ]
    ],
    
    'moduloReserva' => [
        'roles' => ['ADMINISTRADOR', 'gerente', 'VENDEDOR'],
        'icon' => 'fas fa-toolbox',
        'title' => 'Reserva',
        'order' => 6,
        'items' => [
            [
                'title' => 'Materiales / Corte / Ploteo / Impresión / Escaneo',
                'link' => 'venta_reserva_corte.php',
                'permission' => 'reserva_materiales'
            ],
            [
                'title' => 'Atención de reservas',
                'link' => 'venta_corte_material.php',
                'permission' => 'reserva_atencion'
            ],
        ]
    ],
    
    'moduloReservaWeb' => [
        'roles' => ['ADMINISTRADOR', 'gerente', 'VENDEDOR'],
        'icon' => 'fas fa-cloud-download-alt',
        'title' => 'Reserva WEB',
        'order' => 7,
        'items' => [
            [
                'title' => 'Listado de Reserva por la Web',
                'link' => 'listadoWeb.php',
                'permission' => 'reserva_web_listado'
            ],
        ]
    ],
    
    'moduloVenta' => [
        'roles' => ['ADMINISTRADOR', 'gerente', 'VENDEDOR', 'cajero'],
        'icon' => 'fas fa-cart-plus',
        'title' => 'Venta',
        'order' => 8,
        'items' => [
            [
                'title' => 'Punto de Venta Rapida',
                'link' => 'venta_rapida_v2.php',
                'permission' => 'venta_rapida'
            ],
            [
                'title' => 'Listado de Ventas',
                'link' => 'listadoVenta.php',
                'permission' => 'venta_listado'
            ],
        ]
    ],
    
    'moduloPago' => [
        'roles' => ['ADMINISTRADOR', 'gerente', 'cajero'],
        'icon' => 'fas fa-credit-card',
        'title' => 'Pago',
        'order' => 9,
        'items' => [
            [
                'title' => 'Listado de Pagos',
                'link' => 'listadoPagos.php',
                'permission' => 'pago_listado'
            ],
        ]
    ],
];

/**
 * Configuración de páginas específicas que no están en menús
 * Estas son páginas independientes que necesitan control de acceso
 */
$specificPagesConfig = [
    // Páginas de ejemplo y desarrollo
    'ejemplo_nuevo.php' => [
        'roles' => ['ADMINISTRADOR'],
        'permission' => 'access_example_pages',
        'description' => 'Página de ejemplo para nuevas funcionalidades'
    ],
    
    // Páginas administrativas especiales
    'admin_config.php' => [
        'roles' => ['ADMINISTRADOR'],
        'permission' => 'system_administration',
        'description' => 'Configuración administrativa del sistema'
    ],
    
    // Páginas de desarrollo/testing
    'test_functions.php' => [
        'roles' => ['ADMINISTRADOR'],
        'permission' => 'development_testing',
        'description' => 'Página para pruebas de desarrollo'
    ],
    
    // APIs o endpoints especiales
    'api_data_export.php' => [
        'roles' => ['ADMINISTRADOR', 'gerente'],
        'permission' => 'api_data_export',
        'description' => 'API para exportación de datos'
    ],
    
    // Páginas de configuración de usuarios
    'user_management.php' => [
        'roles' => ['ADMINISTRADOR'],
        'permission' => 'user_management',
        'description' => 'Gestión de usuarios del sistema'
    ],
    
    // Páginas de mantenimiento
    'system_maintenance.php' => [
        'roles' => ['ADMINISTRADOR'],
        'permission' => 'system_maintenance',
        'description' => 'Herramientas de mantenimiento del sistema'
    ],
    
    // Páginas de backup/restore
    'backup_manager.php' => [
        'roles' => ['ADMINISTRADOR'],
        'permission' => 'backup_management',
        'description' => 'Gestión de respaldos del sistema'
    ],
];

/**
 * Configuración de accesos rápidos
 */
$quickAccessConfig = [
    'ventaRapida' => [
        'roles' => ['ADMINISTRADOR', 'gerente', 'VENDEDOR', 'cajero'],
        'title' => 'Venta Rapida',
        'link' => 'venta_rapida_v2.php',
        'icon' => 'fas fa-users',
        'color' => 'bg-primary',
        'permission' => 'venta_rapida'
    ],
    'ventaReserva' => [
        'roles' => ['ADMINISTRADOR', 'gerente', 'VENDEDOR'],
        'title' => 'Venta Por Reserva',
        'link' => 'venta_reserva_corte.php',
        'icon' => 'fab fa-whatsapp',
        'color' => 'bg-success',
        'permission' => 'reserva_materiales'
    ],
    'atenderReserva' => [
        'roles' => ['ADMINISTRADOR', 'gerente', 'VENDEDOR'],
        'title' => 'Atender Reserva',
        'link' => 'venta_corte_material.php',
        'icon' => 'fas fa-luggage-cart',
        'color' => 'bg-secondary',
        'permission' => 'reserva_atencion'
    ],
    'pagoCredito' => [
        'roles' => ['ADMINISTRADOR', 'gerente', 'cajero', 'VENDEDOR'],
        'title' => 'Pagos al Crédito',
        'link' => 'pagoCredito.php',
        'icon' => 'fas fa-credit-card',
        'color' => 'bg-black',
        'permission' => 'credito_abono'
    ],
    'manejoCaja' => [
        'roles' => ['ADMINISTRADOR', 'gerente', 'cajero'],
        'title' => 'Manejo de Caja',
        'link' => 'manejoCaja.php',
        'icon' => 'fas fa-toolbox',
        'color' => 'bg-warning',
        'permission' => 'negocio_manejo_caja'
    ],
    'cajaChica' => [
        'roles' => ['ADMINISTRADOR', 'gerente', 'cajero'],
        'title' => 'Caja Chica',
        'link' => 'cajaChica.php',
        'icon' => 'fas fa-box-open',
        'color' => 'bg-info',
        'permission' => 'negocio_caja_chica'
    ],
];

/**
 * Permisos específicos por rol
 */
$rolePermissions = [
    'ADMINISTRADOR' => [
        // Admin
        'admin_trabajadores',
        'admin_usuarios',
        'admin_personas',
        'admin_articulos',
        'admin_mantenimientos',
        'admin_presentaciones',
        'admin_roles',
        // Negocio
        'negocio_compras',
        'negocio_caja_chica',
        'negocio_manejo_caja',
        // SUNAT
        'sunat_emisor',
        'sunat_declarar',
        'sunat_declarados',
        'sunat_no_declarados',
        // Datos
        'datos_etl',
        // Crédito
        'credito_abono',
        'credito_historial',
        // Reserva
        'reserva_materiales',
        'reserva_atencion',
        // Reserva Web
        'reserva_web_listado',
        // Venta
        'venta_rapida',
        'venta_listado',
        // Pago
        'pago_listado',
        // Sistema
        'access_example_pages',
        'system_administration',
        'development_testing',
        'api_data_export',
        'user_management',
        'system_maintenance',
        'backup_management',
    ],
    
    'gerente' => [
        // Negocio
        'negocio_compras',
        'negocio_caja_chica',
        'negocio_manejo_caja',
        // SUNAT
        'sunat_emisor',
        'sunat_declarar',
        'sunat_declarados',
        'sunat_no_declarados',
        // Datos
        'datos_etl',
        // Crédito
        'credito_abono',
        'credito_historial',
        // Reserva
        'reserva_materiales',
        'reserva_atencion',
        // Reserva Web
        'reserva_web_listado',
        // Venta
        'venta_rapida',
        'venta_listado',
        // Pago
        'pago_listado',
        // Sistema limitado
        'api_data_export',
    ],
    
    'VENDEDOR' => [
        // Crédito
        'credito_abono',
        'credito_historial',
        // Reserva
        'reserva_materiales',
        'reserva_atencion',
        // Reserva Web
        'reserva_web_listado',
        // Venta
        'venta_rapida',
        'venta_listado',
    ],
    
    'cajero' => [
        // Negocio
        'negocio_caja_chica',
        'negocio_manejo_caja',
        // SUNAT
        'sunat_declarar',
        'sunat_declarados',
        // Crédito
        'credito_abono',
        'credito_historial',
        // Venta
        'venta_rapida',
        'venta_listado',
        // Pago
        'pago_listado',
    ],
    
    'almacenero' => [
        // Reserva
        'reserva_atencion',
    ],
];

/**
 * Páginas públicas que no requieren autenticación especial
 * Estas páginas están disponibles para todos los usuarios autenticados
 */
$publicPages = [
    'index.php',
    'dashboard.php',
    'profile.php',
    'help.php',
    'about.php'
];

return [
    'menu' => $menuConfig,
    'specificPages' => $specificPagesConfig,
    'publicPages' => $publicPages,
    'quickAccess' => $quickAccessConfig,
    'permissions' => $rolePermissions,
    'roles' => ROLES
];