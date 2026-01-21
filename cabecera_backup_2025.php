<?php

/**
 * cabecera.php
 * Nueva cabecera del sistema usando MenuManager
 * Caracol Captain - Sistema de gestión
 */

include('logica/clssConsultas.php');

session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$flagRespuesta = fnVerificarUsarioSession($_SESSION['id']);
if ($flagRespuesta == 0) {
    $ape_usuario = $_SESSION['ape'];
    $id_usuario_s = $_SESSION['id'];
    $rol = $_SESSION['rol'];
    $usuario = $_SESSION['usuario'];
    $nombre = $_SESSION['nombre'];
    $correo = $_SESSION['correo'];
    echo '<div style="text-align: center; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 20px; border-radius: 10px; font-size: 18px; font-weight: bold;">
        <i class="fas fa-exclamation-triangle" style="margin-right: 10px;"></i> 
        Usuario BLOQUEADO - ' . strtoupper($nombre) . ' ' . strtoupper($ape_usuario) . ' [' . strtoupper($usuario) . '] 😞 ❌
      </div>
      <br>
      <div style="text-align: center;">
        <img src="assets/img/mebloqueaste.png" alt="Usuario Bloqueado" />
        </div>
     <br>
    <div style="text-align: center;"> <b>Comunicate con los dueños para que te den acceso</b> </div>

      ';
    exit();
} else {
    $ape_usuario = $_SESSION['ape'];
    $id_usuario_s = $_SESSION['id'];
    $rol = $_SESSION['rol'];
    $nombre = $_SESSION['nombre'];
    $correo = $_SESSION['correo'];
}

// Inicializar MenuManager con el rol del usuario
require_once('/MenuManager.php');
$menuManager = new MenuManager($rol);

// Función de compatibilidad para mantener el código existente
function tienePermiso($moduloPermiso = '', $permisoEspecifico = '') {
    global $menuManager;
    return $menuManager->tienePermiso($moduloPermiso, $permisoEspecifico);
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Caracol Captain</title>
    <meta charset="UTF-8">

    <meta
        content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
        name="viewport" />
    <link
        rel="icon"
        href="assets/img/caracoles.png"
        type="image/x-icon" />

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

    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link rel="stylesheet" href="assets/css/demo.css" />
    <link rel="stylesheet" href="assets/css/stylePerzo.css" />
    <!-- Efectos especiales -->
    <!-- <link rel="stylesheet" href="assets/css/christmas-effects.css" /> -->
    <!-- <link rel="stylesheet" href="assets/css/new-year.css"/> -->

</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" data-background-color="dark">
            <div class="sidebar-logo">
                <!-- Logo Header -->
                <div class="logo-header" data-background-color="dark">

                    <a href="index.php" class="logo">
                        <img
                            src="assets/img/caracoles.png"
                            alt="navbar brand"
                            class="navbar-brand"
                            height="30" /> 
                        <span style="color: white; font-size: 14px;">Caracol Soft - <strong>LB RODRI</strong></span>
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
                <!-- End Logo Header -->
            </div>
            
            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="sidebar-content">
                    <ul class="nav nav-secondary">
                        <?php 
                        // Renderizar el menú dinámicamente usando MenuManager
                        echo $menuManager->renderMenu(); 
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        <!-- End Sidebar -->

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <div class="logo-header" data-background-color="dark">
                        <a href="index.php" class="logo">
                            <img
                                src="assets/img/kaiadmin/logo_light.svg"
                                alt="navbar brand"
                                class="navbar-brand"
                                height="20" />
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
                    <!-- End Logo Header -->
                </div>
                
                <!-- Navbar Header -->
                <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
                    <div class="container-fluid">
                        <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                        </nav>

                        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                            <a
                                name=""
                                id=""
                                class="btn"
                                href="index.php"
                                role="button"><i class="fas fa-home"></i>
                            </a>
                            
                            <li class="nav-item topbar-icon dropdown hidden-caret">
                                <a
                                    class="nav-link"
                                    data-bs-toggle="dropdown"
                                    href="#"
                                    aria-expanded="false">
                                    <i class="fas fa-layer-group"></i>
                                </a>
                                <div class="dropdown-menu quick-actions animated fadeIn">
                                    <div class="quick-actions-header" style="background-color: #1a2035;">
                                        <span class="title mb-1">Accesos Rápidos</span>
                                        <span class="subtitle op-7">Todo en un solo click.</span>
                                    </div>
                                    <div class="quick-actions-scroll scrollbar-outer">
                                        <div class="quick-actions-items">
                                            <div class="row m-0">
                                                <?php 
                                                // Renderizar accesos rápidos dinámicamente
                                                echo $menuManager->renderQuickAccess(); 
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            
                            <li class="nav-item topbar-user dropdown hidden-caret">
                                <a
                                    class="dropdown-toggle profile-pic"
                                    data-bs-toggle="dropdown"
                                    href="#"
                                    aria-expanded="false">
                                    <div class="avatar-sm">
                                        <img
                                            src="assets/img/usuario.png"
                                            alt="..."
                                            class="avatar-img rounded-circle" />
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
                                                    <img
                                                        src="assets/img/usuario.png"
                                                        alt="image profile"
                                                        class="avatar-img rounded" />
                                                </div>
                                                <div class="u-text">
                                                    <h4><?php echo htmlspecialchars($nombre); ?></h4>
                                                    <p class="text-muted"><?php echo htmlspecialchars($correo ? $correo : 'Sin correo'); ?></p>
                                                    <p class="text-muted"><small>Rol: <?php echo htmlspecialchars($rol); ?></small></p>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="logica/logout.php">Salir</a>
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

            <!-- Efectos especiales -->
            <!-- <script src="assets/js/christmas-effects.js"></script> -->
            <!-- <script src="assets/js/new-year.js"></script> -->

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
                                // Remover active de todos
                                document.querySelectorAll(".nav-item").forEach(nav => {
                                    nav.classList.remove("active");
                                });
                                
                                // Agregar active al elemento correcto
                                let navItem = item.closest(".nav-item");
                                if (navItem) {
                                    navItem.classList.add("active");
                                    
                                    // Si está dentro de un collapse, abrirlo
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