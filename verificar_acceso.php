<?php
/**
 * MIDDLEWARE DE VERIFICACIÓN DE PERMISOS
 * 
 * Incluir este archivo al inicio de cada página protegida:
 * include('logica/verificar_acceso.php');
 * 
 * Este archivo verificará automáticamente si el usuario tiene permiso
 * para acceder a la página actual
 */

// Asegurar que la sesión está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario está logueado
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Incluir las funciones de consultas
require_once(__DIR__ . '/clssConsultas.php');

// Obtener la URL actual
$url_actual = basename($_SERVER['PHP_SELF']);

// Páginas públicas que no requieren verificación de permisos
$paginas_publicas = [
    'index.php',
    'login.php',
    'logout.php',
    'pie.php',
    'cabecera.php'
];

// Si es una página pública, permitir acceso
if (in_array($url_actual, $paginas_publicas)) {
    return;
}

// Verificar permisos del usuario
$tiene_permiso = fnTienePermisoPagina($_SESSION['id'], $url_actual);

if (!$tiene_permiso) {
    // Mostrar página de acceso denegado
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Acceso Denegado</title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
        <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: 'Public Sans', sans-serif;
            }
            .access-denied-container {
                background: white;
                padding: 60px;
                border-radius: 20px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                text-align: center;
                max-width: 600px;
            }
            .access-denied-icon {
                font-size: 120px;
                color: #f44336;
                margin-bottom: 30px;
            }
            .access-denied-title {
                font-size: 36px;
                font-weight: bold;
                color: #333;
                margin-bottom: 20px;
            }
            .access-denied-message {
                font-size: 18px;
                color: #666;
                margin-bottom: 30px;
            }
            .btn-volver {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 15px 40px;
                border-radius: 50px;
                text-decoration: none;
                font-weight: bold;
                font-size: 16px;
                display: inline-block;
                transition: transform 0.3s;
            }
            .btn-volver:hover {
                transform: translateY(-3px);
                color: white;
                box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            }
            .page-info {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 10px;
                margin: 20px 0;
                font-family: monospace;
                color: #666;
            }
        </style>
    </head>
    <body>
        <div class="access-denied-container">
            <div class="access-denied-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h1 class="access-denied-title">Acceso Denegado</h1>
            <p class="access-denied-message">
                Lo sentimos, no tienes permisos para acceder a esta página.
            </p>
            <div class="page-info">
                <strong>Página solicitada:</strong> <?php echo htmlspecialchars($url_actual); ?>
            </div>
            <p class="access-denied-message" style="font-size: 14px; margin-top: 20px;">
                Si crees que deberías tener acceso a esta sección, 
                contacta con tu administrador para que te asigne los permisos necesarios.
            </p>
            <a href="index.php" class="btn-volver">
                <i class="fas fa-home"></i> Volver al Inicio
            </a>
        </div>

        <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    </body>
    </html>
    <?php
    exit();
}
?>