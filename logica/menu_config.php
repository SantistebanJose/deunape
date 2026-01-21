<?php
/**
 * Configuración del menú del sistema
 * Estructura semi-estructurada para fácil mantenimiento
 */

return [
    'administrador' => [
        'nombre' => 'Administrador',
        'icono' => 'fas fa-cog',
        'orden' => 1,
        'submenu' => [
            'trabajadores' => [
                'nombre' => 'Trabajadores',
                'url' => 'Empleados.php',
                'orden' => 1
            ],
            'usuarios' => [
                'nombre' => 'Usuarios',
                'url' => 'usuario.php',
                'orden' => 2
            ],
            'personas' => [
                'nombre' => 'Personas',
                'url' => 'persona.php',
                'orden' => 3
            ],
            'articulos' => [
                'nombre' => 'Artículos',
                'url' => 'articulos.php',
                'orden' => 4
            ],
            'mantenimientos' => [
                'nombre' => 'Mantenimientos',
                'url' => 'mantenimiento.php',
                'orden' => 5
            ],
            'presentaciones' => [
                'nombre' => 'Presentaciones',
                'url' => 'presentaciones.php',
                'orden' => 6
            ]
        ]
    ],
    
    'negocio' => [
        'nombre' => 'Negocio',
        'icono' => 'fas fa-store-alt',
        'orden' => 2,
        'submenu' => [
            'compras' => [
                'nombre' => 'Gestionar de Compras',
                'url' => 'compra.php',
                'orden' => 1
            ],
            'caja_chica' => [
                'nombre' => 'Caja Chica',
                'url' => 'cajaChica.php',
                'orden' => 2
            ],
            'manejo_caja' => [
                'nombre' => 'Manejo de Caja',
                'url' => 'manejoCaja.php',
                'orden' => 3
            ]
        ]
    ],
    
    'facturador_sunat' => [
        'nombre' => 'Facturador SUNAT',
        'icono' => 'fab fa-stripe-s',
        'orden' => 3,
        'submenu' => [
            'emisor' => [
                'nombre' => 'Datos de Emisor',
                'url' => 'emisor.php',
                'orden' => 1
            ],
            'declarar_sunat' => [
                'nombre' => 'Declarar Comprobantes a SUNAT',
                'url' => 'listVentasForPagosSunat.php',
                'orden' => 2
            ],
            'comp_declarados' => [
                'nombre' => 'Comprobantes Declarados',
                'url' => 'listComprobantesDeclarados.php',
                'orden' => 3
            ],
            'comp_no_declarados' => [
                'nombre' => 'Comprobantes NO Declarados',
                'url' => 'comprobantes_no_declarados.php',
                'orden' => 4,
                'estilo' => 'color: red;' // Estilo especial
            ]
        ]
    ],
    
    'datos' => [
        'nombre' => 'Datos',
        'icono' => 'fas fa-file-powerpoint',
        'orden' => 4,
        'submenu' => [
            'etl' => [
                'nombre' => 'ETL para Power BI',
                'url' => 'etl.php',
                'orden' => 1
            ]
        ]
    ],
    
    'credito' => [
        'nombre' => 'Crédito',
        'icono' => 'fas fa-user-lock',
        'orden' => 5,
        'submenu' => [
            'pago_credito' => [
                'nombre' => 'Realizar Abono a Crédito',
                'url' => 'pagoCredito.php',
                'orden' => 1
            ],
            'historial_clientes' => [
                'nombre' => 'Historial de Clientes',
                'url' => 'historialClientes.php',
                'orden' => 2
            ]
        ]
    ],
    
    'reserva' => [
        'nombre' => 'Reserva',
        'icono' => 'fas fa-toolbox',
        'orden' => 6,
        'submenu' => [
            'reserva_material' => [
                'nombre' => 'Materiales / Corte / Ploteo / Impresión / Escaneo',
                'url' => 'venta_reserva_corte.php',
                'orden' => 1
            ],
            'atencion_reserva' => [
                'nombre' => 'Atención de reservas',
                'url' => 'venta_corte_material.php',
                'orden' => 2
            ]
        ]
    ],
    
    'reserva_web' => [
        'nombre' => 'Reserva WEB',
        'icono' => 'fas fa-cloud-download-alt',
        'orden' => 7,
        'submenu' => [
            'reserva_web_list' => [
                'nombre' => 'Listado de Reserva por la Web',
                'url' => 'listadoWeb.php',
                'orden' => 1
            ]
        ]
    ],
    
    'venta' => [
        'nombre' => 'Venta',
        'icono' => 'fas fa-cart-plus',
        'orden' => 8,
        'submenu' => [
            'venta_rapida' => [
                'nombre' => 'Punto de Venta Rapida',
                'url' => 'venta_rapida_v2.php',
                'orden' => 1
            ],
            'listado_ventas' => [
                'nombre' => 'Listado de Ventas',
                'url' => 'listadoVenta.php',
                'orden' => 2
            ]
        ]
    ],
    
    'pago' => [
        'nombre' => 'Pago',
        'icono' => 'fas fa-credit-card',
        'orden' => 9,
        'submenu' => [
            'listado_pagos' => [
                'nombre' => 'Listado de Pagos',
                'url' => 'listadoPagos.php',
                'orden' => 1
            ]
        ]
    ]
];
?>