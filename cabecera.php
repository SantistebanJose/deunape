<?php
/**
 * cabecera.php
 * Header del sistema con MenuManager integrado
 * Versión mejorada con colores coherentes al login
 */
include('logica/clssConsultas.php');

session_start();

// Verificar si hay sesión activa
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$flagRespuesta = fnVerificarUsarioSession($_SESSION['id']);

if ($flagRespuesta == 0) {
    // Usuario bloqueado
    $ape_usuario = $_SESSION['ape'];
    $id_usuario_s = $_SESSION['id'];
    $usuario = $_SESSION['usuario'];
    $nombre = $_SESSION['nombre'];
    $correo = $_SESSION['correo'] ?? '';
    $rol = $_SESSION['nombre_rol'] ?? 'Sin rol';
    
    echo '<div style="text-align: center; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 20px; border-radius: 10px; font-size: 18px; font-weight: bold;">
        <i class="fas fa-exclamation-triangle" style="margin-right: 10px;"></i> 
        Usuario BLOQUEADO - ' . strtoupper($nombre) . ' ' . strtoupper($ape_usuario) . ' [' . strtoupper($usuario) . '] 😞 ❌
      </div>
      <br>
      <div style="text-align: center;">
        <img src="assets/img/mebloqueaste.png" alt="Usuario Bloqueado" />
      </div>
      <br>
      <div style="text-align: center;"> <b>Comunicate con los dueños para que te den acceso</b> </div>';
    exit();
}

// Usuario activo - cargar datos
$ape_usuario = $_SESSION['ape'];
$id_usuario_s = $_SESSION['id'];
$nombre = $_SESSION['nombre'];
$correo = $_SESSION['correo'] ?? '';
$usuario = $_SESSION['usuario'] ?? '';

// ✅ OBTENER ROL DEL USUARIO
// Prioridad: nombre_rol > rol > 'Sin rol'
$rol = $_SESSION['nombre_rol'] ?? $_SESSION['rol'] ?? 'Sin rol';

// Si no hay rol en sesión, obtenerlo de la BD
if ($rol === 'Sin rol' && isset($_SESSION['id'])) {
    include("logica/bd.php");
    
    try {
        $query = "
            SELECT r.nombre_rol
            FROM usuario u
            INNER JOIN roles r ON u.id_rol = r.id_rol
            WHERE u.id = $1
        ";
        
        $stmt = $conectar->prepare($query);
        $stmt->execute([$_SESSION['id']]);
        $userRol = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($userRol) {
            $rol = $userRol['nombre_rol'];
            $_SESSION['nombre_rol'] = $rol;
        }
    } catch (PDOException $e) {
        error_log("Error obteniendo rol en cabecera: " . $e->getMessage());
    }
}

// ✅ INICIALIZAR MENUMANAGER
require_once('MenuManager.php');

$menuManager = null;
try {
    $menuManager = new MenuManager($rol);
} catch (Exception $e) {
    error_log("Error inicializando MenuManager: " . $e->getMessage());
    // Continuar sin MenuManager (modo degradado)
}

// Función de compatibilidad
function tienePermiso($moduloPermiso = '', $permisoEspecifico = '') {
    global $menuManager;
    if ($menuManager === null) {
        return false;
    }
    return $menuManager->tienePermiso($moduloPermiso, $permisoEspecifico);
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>CAPTAIN <?php echo " | ". $_SESSION["nombre_comercial"]?></title>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="assets/img/logo-captain.svg" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["assets/css/fonts.min.css"],
            },
            active: function() {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="assets/css/demo.css" />
    <link rel="stylesheet" href="assets/css/stylePerzo.css" />
    
    <!-- Estilos personalizados coherentes con login -->
    <style>
        :root {
            --primary-blue: #0033A0;
            --primary-blue-light: #0055CC;
            --primary-green: #2ecc71;
            --primary-green-dark: #27ae60;
            --dark-bg: #1a2035;
            --sidebar-bg: linear-gradient(180deg, #0033A0 0%, #002080 100%);
        }

        /* Sidebar con gradiente azul peruano */
        .sidebar[data-background-color="dark"] {
            background: var(--sidebar-bg) !important;
            box-shadow: 2px 0 15px rgba(0, 51, 160, 0.15);
        }

        .sidebar .logo-header[data-background-color="dark"] {
            background: linear-gradient(135deg, #002080 0%, #0033A0 100%) !important;
            border-bottom: 3px solid var(--primary-green);
        }

        /* Texto del menú principal - más visible */
        .sidebar .nav-item > a {
            color: #ffffff !important;
            font-weight: 500;
        }

        .sidebar .nav-item > a span {
            color: #ffffff !important;
        }

        .sidebar .nav-item > a i {
            color: #ffffff !important;
        }

        /* Hover en items del menú */
        .sidebar .nav-item a:hover {
            background: rgba(46, 204, 113, 0.2) !important;
            border-left: 4px solid var(--primary-green);
            padding-left: 16px;
            transition: all 0.3s ease;
        }

        .sidebar .nav-item a:hover span,
        .sidebar .nav-item a:hover i {
            color: #ffffff !important;
        }

        /* Item activo del menú */
        .sidebar .nav-item.active > a {
            background: linear-gradient(90deg, rgba(46, 204, 113, 0.25) 0%, rgba(46, 204, 113, 0.1) 100%) !important;
            border-left: 4px solid var(--primary-green);
            font-weight: 600;
            color: #ffffff !important;
        }

        .sidebar .nav-item.active > a span,
        .sidebar .nav-item.active > a i {
            color: #ffffff !important;
        }

        /* Navbar header con degradado */
        .main-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important;
            box-shadow: 0 4px 20px rgba(0, 51, 160, 0.08);
            border-bottom: 3px solid var(--primary-green);
        }

        .navbar-header {
            background: transparent !important;
        }

        /* Badge de bienvenida con colores peruanos */
        .navbar-header span {
            color: #2c3e50;
        }

        .navbar-header strong {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }

        /* Botón home con efecto */
        .navbar-nav .btn {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-light) 100%);
            color: white !important;
            border-radius: 10px;
            padding: 8px 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 51, 160, 0.2);
        }

        .navbar-nav .btn:hover {
            background: linear-gradient(135deg, #002080 0%, var(--primary-blue) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 51, 160, 0.3);
        }

        /* Dropdown de acciones rápidas */
        .quick-actions-header {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-light) 100%) !important;
            color: white !important;
            border-radius: 10px 10px 0 0;
        }

        .quick-actions-items .col-6:hover {
            transform: translateY(-3px);
            transition: all 0.3s ease;
        }

        /* Avatar y perfil de usuario */
        .topbar-user .avatar-sm {
            border: 3px solid var(--primary-green);
            box-shadow: 0 4px 10px rgba(46, 204, 113, 0.2);
        }

        .profile-username .fw-bold {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-green) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Dropdown del usuario */
        .dropdown-user {
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 51, 160, 0.15);
            border: 2px solid rgba(46, 204, 113, 0.2);
        }

        .user-box {
            background: linear-gradient(135deg, rgba(0, 51, 160, 0.05) 0%, rgba(46, 204, 113, 0.05) 100%);
            border-radius: 10px;
            padding: 15px;
        }

        .user-box .avatar-lg {
            border: 3px solid var(--primary-green);
            border-radius: 10px;
        }

        .user-box h4 {
            color: var(--primary-blue);
            font-weight: 700;
        }

        .dropdown-user .dropdown-item {
            border-radius: 8px;
            margin: 5px 10px;
            transition: all 0.3s ease;
        }

        .dropdown-user .dropdown-item:hover {
            background: linear-gradient(90deg, rgba(0, 51, 160, 0.1) 0%, rgba(46, 204, 113, 0.1) 100%);
            padding-left: 25px;
            color: var(--primary-blue);
        }

        /* Toggle buttons con estilo peruano */
        .btn-toggle {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-light) 100%);
            color: white;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-toggle:hover {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--primary-green-dark) 100%);
            transform: rotate(90deg);
        }

        /* Iconos con color verde */
        .topbar-icon .nav-link i {
            color: var(--primary-green);
            transition: all 0.3s ease;
        }

        .topbar-icon .nav-link:hover i {
            color: var(--primary-blue);
            transform: scale(1.2);
        }

        /* Submenu collapse */
        .nav-collapse .nav-link {
            padding-left: 35px !important;
            color: #e8f4f8 !important;
            font-size: 0.95rem;
        }

        .nav-collapse .nav-link span {
            color: #e8f4f8 !important;
        }

        .nav-collapse .nav-link i {
            color: #e8f4f8 !important;
        }

        .nav-collapse .nav-link:hover {
            background: rgba(46, 204, 113, 0.2) !important;
            border-left: 3px solid var(--primary-green);
            color: #ffffff !important;
        }

        .nav-collapse .nav-link:hover span,
        .nav-collapse .nav-link:hover i {
            color: #ffffff !important;
        }

        /* Flechas de los menús desplegables */
        .sidebar .nav-item > a[data-bs-toggle="collapse"]::after {
            color: #ffffff !important;
        }

        /* Badge y etiquetas en el sidebar */
        .sidebar .badge {
            background: var(--primary-green) !important;
            color: #ffffff !important;
        }

        /* Animación de carga suave */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .main-header {
            animation: fadeIn 0.5s ease-out;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .navbar-header span {
                font-size: 0.85rem;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" data-background-color="dark">
            <div class="sidebar-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="index.php" class="logo">
                        <span style="color: white; font-size: 12px; font-weight: 600;">
                            <?php echo $_SESSION["nombre_comercial"]?> | <strong>DeUnaPe </strong>
                        </span>
                    </a>
                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar">
                            <i class="gg-menu-right"></i>
                        </button>
                        <button class="btn btn-toggle sidenav-toggler">
                            <i class="gg-menu-left"></i>
                        </button>
                    </div>
                    <button class="topbar-toggler more">
                        <i class="gg-more-vertical-alt"></i>
                    </button>
                </div>
            </div>
            
            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="sidebar-content">
                    <ul class="nav nav-secondary">
                        <?php 
                        // ✅ Renderizar el menú dinámicamente
                        if ($menuManager !== null) {
                            echo $menuManager->renderMenu(); 
                        } else {
                            echo '<li class="nav-item"><p style="color: white; padding: 15px;">Error cargando menú</p></li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        <!-- End Sidebar -->

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <div class="logo-header" data-background-color="dark">
                        <a href="index.php" class="logo">
                            <img src="assets/img/kaiadmin/logo_light.svg" alt="navbar brand" height="20" />
                        </a>
                        <div class="nav-toggle">
                            <button class="btn btn-toggle toggle-sidebar">
                                <i class="gg-menu-right"></i>
                            </button>
                            <button class="btn btn-toggle sidenav-toggler">
                                <i class="gg-menu-left"></i>
                            </button>
                        </div>
                        <button class="topbar-toggler more">
                            <i class="gg-more-vertical-alt"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Navbar Header -->
                <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
                    <div class="container-fluid">
                        <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                        </nav>
                        
                        <span>
                            <i class="fab fa-telegram-plane" style="color: #2ecc71;"></i> Hola 👋,
                        </span> 
                        <span>
                            <strong><?php echo " ". $_SESSION["nombre_comercial"]." "?></strong>, 
                            Realiza todos tus movimientos desde el sistema de <strong>DeUnaPe</strong> 😎
                        </span>
                        
                        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                            <a name="" id="" class="btn" href="index.php" role="button">
                                <i class="fas fa-home"></i> Inicio
                            </a>
                            
                            <li class="nav-item topbar-icon dropdown hidden-caret">
                                <a class="nav-link" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                                    <i class="fas fa-layer-group"></i>
                                </a>
                                <div class="dropdown-menu quick-actions animated fadeIn">
                                    <div class="quick-actions-header">
                                        <span class="title mb-1">Accesos Rápidos</span>
                                        <span class="subtitle op-7">Todo en un solo click.</span>
                                    </div>
                                    <div class="quick-actions-scroll scrollbar-outer">
                                        <div class="quick-actions-items">
                                            <div class="row m-0">
                                                <?php 
                                                // ✅ Renderizar accesos rápidos
                                                if ($menuManager !== null) {
                                                    echo $menuManager->renderQuickAccess(); 
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            
                            <li class="nav-item topbar-user dropdown hidden-caret">
                                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                                    <div class="avatar-sm">
                                        <img src="assets/img/usuario.png" alt="..." class="avatar-img rounded-circle" />
                                    </div>
                                    <span class="profile-username">
                                        <span class="op-7">Hola,</span>
                                        <span class="fw-bold"><?php echo htmlspecialchars($nombre); ?></span>
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-user animated fadeIn">
                                    <div class="dropdown-user-scroll scrollbar-outer">
                                        <li>
                                            <div class="user-box">
                                                <div class="avatar-lg">
                                                    <img src="assets/img/usuario.png" alt="image profile" class="avatar-img rounded" />
                                                </div>
                                                <div class="u-text">
                                                    <h4><?php echo htmlspecialchars($nombre . ' ' . $ape_usuario); ?></h4>
                                                    <p class="text-muted"><?php echo htmlspecialchars($correo ? $correo : 'Sin correo'); ?></p>
                                                    <p class="text-muted">
                                                        <small>Rol: <strong style="color: #2ecc71;"><?php echo htmlspecialchars($rol); ?></strong></small>
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="logica/logout.php">
                                                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                            </a>
                                        </li>
                                    </div>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
                <!-- End Navbar -->
            </div>

            <br>

            <script>
                // Script para marcar el menú activo
                document.addEventListener("DOMContentLoaded", function() {
                    let menuItems = document.querySelectorAll(".nav-item a");
                    let currentPath = window.location.pathname.split("/").pop();

                    menuItems.forEach(item => {
                        let menuPath = item.getAttribute("href");
                        if (menuPath) {
                            menuPath = menuPath.split("/").pop().split("?")[0];
                            
                            if (currentPath === menuPath && menuPath !== "") {
                                document.querySelectorAll(".nav-item").forEach(nav => {
                                    nav.classList.remove("active");
                                });
                                
                                let navItem = item.closest(".nav-item");
                                if (navItem) {
                                    navItem.classList.add("active");
                                    
                                    let collapse = item.closest(".collapse");
                                    if (collapse) {
                                        collapse.classList.add("show");
                                        let parentLink = collapse.previousElementSibling;
                                        if (parentLink) {
                                            parentLink.classList.remove("collapsed");
                                            parentLink.setAttribute("aria-expanded", "true");
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
            </script>