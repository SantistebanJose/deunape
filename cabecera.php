<?php

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

?>


<!DOCTYPE html>
<html lang="en">

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
    <!-- Efectos Navideños -->
    <!-- <link rel="stylesheet" href="assets/css/christmas-effects.css" /> 
    <link rel="stylesheet" href="assets/css/new-year.css"/> -->



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
                            height="30" /> <span style="color: white; font-size: 14px;">Caracol Soft - <strong>LB RODRI</strong></span>
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

                        
                            <li class="nav-item ">
                                <a
                                    data-bs-toggle="collapse"
                                    href="#dashboard"
                                    class="collapsed"
                                    aria-expanded="false">
                                    <i class="fas fa-cog"></i>
                                    <p>Adiministrador</p>
                                    <span class="caret"></span>
                                </a>
                                <div class="collapse" id="dashboard">
                                    <ul class="nav nav-collapse">
                                        <li>
                                            <a href="Empleados.php">
                                                <span class="sub-item">Trabajadores</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="usuario.php">
                                                <span class="sub-item">Usuarios</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="persona.php">
                                                <span class="sub-item">Personas</span>
                                            </a>
                                        </li>

                                        <li>
                                            <a href="articulos.php">
                                                <span class="sub-item">Artículos</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="mantenimiento.php">
                                                <span class="sub-item">Mantenimientos</span>
                                            </a>
                                        </li>



                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a
                                    data-bs-toggle="collapse"
                                    href="#compras"
                                    class="collapsed"
                                    aria-expanded="false">
                                    <i class="fas fa-store-alt"></i>
                                    <p>Negocio</p>
                                    <span class="caret"></span>
                                </a>
                                <div class="collapse" id="compras">
                                    <ul class="nav nav-collapse">

                                        <li>
                                            <a href="compra.php">
                                                <span class="sub-item">Gestionar de Compras</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="cajaChica.php">
                                                <span class="sub-item">Caja Chica</span>
                                            </a>
                                        </li>

                                        <li>
                                            <a href="manejoCaja.php">
                                                <span class="sub-item">Manejo de Caja</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            



                            <li class="nav-item">
                                <a
                                    data-bs-toggle="collapse"
                                    href="#facturador_sunat"
                                    class="collapsed"
                                    aria-expanded="false">
                                    <i class="fab fa-stripe-s"></i>
                                    <p>Facturador SUNAT</p>
                                    <span class="caret"></span>
                                </a>
                                <div class="collapse" id="facturador_sunat">
                                    <ul class="nav nav-collapse">

                                        <li>
                                            <a href="emisor.php">
                                                <span class="sub-item">Datos de Emisor</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="listVentasForPagosSunat.php">
                                                <span class="sub-item">Declarar Comprobantes a SUNAT </span>
                                            </a>
                                        </li>

                                        <li>
                                            <a href="listComprobantesDeclarados.php">
                                                <span class="sub-item">Comprobantes Declarados</span>
                                            </a>
                                        </li>
                            
                                        <li>
                                            <a href="comprobantes_no_declarados.php">
                                                <span class="sub-item" style="color: red;">Comprobantes NO Declarados</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            

                            <li class="nav-item">
                                <a
                                    data-bs-toggle="collapse"
                                    href="#etl"
                                    class="collapsed"
                                    aria-expanded="false">
                                    <i class="fas fa-file-powerpoint"></i>
                                    <p>Datos</p>
                                    <span class="caret"></span>
                                </a>
                                <div class="collapse" id="etl">
                                    <ul class="nav nav-collapse">
                                        <li>

                                            <a href="etl.php">
                                                <span class="sub-item">ETL para Power BI</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a
                                    data-bs-toggle="collapse"
                                    href="#deuda"
                                    class="collapsed"
                                    aria-expanded="false">
                                    <i class="fas fa-user-lock"></i>
                                    <p>Crédito</p>
                                    <span class="caret"></span>
                                </a>
                                <div class="collapse" id="deuda">
                                    <ul class="nav nav-collapse">
                                        <li>
                                            <a href="pagoCredito.php">
                                                <span class="sub-item">Realizar Abono a Crédito</span>
                                            </a>
                                        </li>

                                        <li>
                                            <a href="historialClientes.php">
                                                <span class="sub-item">Historial de Clientes </span>
                                            </a>
                                        </li>


                                    </ul>
                                </div>
                            </li>
                        
                        
                        <li class="nav-item">
                            <a
                                data-bs-toggle="collapse"
                                href="#reserva"
                                class="collapsed"
                                aria-expanded="false">
                                <i class="fas fa-toolbox"></i>
                                <p>Reserva</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="reserva">
                                <ul class="nav nav-collapse">
                                    <li>
                                        <a href="venta_reserva_corte.php">
                                            <span class="sub-item">Materiales / Corte / Ploteo / Impresión / Escaneo</span>
                                        </a>
                                    </li>

                                    <li>
                                        <a href="venta_corte_material.php">
                                            <span class="sub-item">Atención de reservas</span>
                                        </a>
                                    </li>


                                </ul>
                            </div>
                        </li>
                        
                        <li class="nav-item">
                            <a
                                data-bs-toggle="collapse"
                                href="#reservaweb"
                                class="collapsed"
                                aria-expanded="false">
                                <i class="fas fa-cloud-download-alt"></i>
                                <p>Reserva WEB</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="reservaweb">
                                <ul class="nav nav-collapse">
                                    <li>
                                        <a href="listadoWeb.php">
                                            <span class="sub-item">Listado de Reserva por la Web </span>
                                        </a>
                                    </li>


                                </ul>
                            </div>
                        </li>
                         
                        
                        <li class="nav-item">
                            <a
                                data-bs-toggle="collapse"
                                href="#venta"
                                class="collapsed"
                                aria-expanded="false">
                                <i class="fas fa-cart-plus"></i>
                                <p>Venta</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="venta">
                                <ul class="nav nav-collapse">
                                    <li>
                                        <a href="venta_rapida_v2.php">
                                            <span class="sub-item">Punto de Venta Rapida</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="listadoVenta.php">
                                            <span class="sub-item">Listado de Ventas</span>
                                        </a>
                                    </li>



                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a
                                data-bs-toggle="collapse"
                                href="#pago"
                                class="collapsed"
                                aria-expanded="false">
                                <i class="fas fa-credit-card"></i>
                                <p>Pago</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="pago">
                                <ul class="nav nav-collapse">
                                    <li>
                                        <a href="listadoPagos.php">
                                            <span class="sub-item">Listado de Pagos</span>
                                        </a>
                                    </li>



                                </ul>
                            </div>
                        </li>

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
                        <a href="index.html" class="logo">
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
                <nav
                    class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
                    <div class="container-fluid">
                        <nav
                            class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">

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
                                    href=""
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
                                                <a class="col-6 col-md-4 p-0" href="venta_rapida_v2.php">
                                                    <div class="quick-actions-item">
                                                        <div class="avatar-item bg-primary rounded-circle">
                                                            <i class="fas fa-users"></i>
                                                        </div>
                                                        <span class="text">Venta Rapida</span>
                                                    </div>
                                                </a>
                                                <a class="col-6 col-md-4 p-0" href="venta_reserva_corte.php">
                                                    <div class="quick-actions-item">
                                                        <div
                                                            class="avatar-item bg-success rounded-circle">
                                                            <i class="fab fa-whatsapp"></i>
                                                        </div>
                                                        <span class="text">Venta Por Reserva</span>
                                                    </div>
                                                </a>
                                                <a class="col-6 col-md-4 p-0" href="venta_corte_material.php">
                                                    <div class="quick-actions-item">
                                                        <div class="avatar-item bg-secondary rounded-circle">
                                                            <i class="fas fa-luggage-cart"></i>
                                                        </div>
                                                        <span class="text">Atender Reserva</span>
                                                    </div>
                                                </a>
                                                
                                                    <a class="col-6 col-md-4 p-0" href="pagoCredito.php">
                                                        <div class="quick-actions-item">
                                                            <div
                                                                class="avatar-item bg-black rounded-circle">
                                                                <i class="fas fa-credit-card"></i>
                                                            </div>
                                                            <span class="text">Pagos al Crédito</span>
                                                        </div>
                                                    </a>
                                                    <a class="col-6 col-md-4 p-0" href="manejoCaja.php">
                                                        <div class="quick-actions-item">
                                                            <div
                                                                class="avatar-item bg-warning rounded-circle">
                                                                <i class="fas fa-toolbox"></i>
                                                            </div>
                                                            <span class="text">Manejo de Caja</span>
                                                        </div>
                                                    </a>
                                                    <a class="col-6 col-md-4 p-0" href="cajaChica.php">
                                                        <div class="quick-actions-item">
                                                            <div
                                                                class="avatar-item bg-info rounded-circle">
                                                                <i class="fas fa-box-open"></i>
                                                            </div>
                                                            <span class="text">Caja Chica</span>
                                                        </div>
                                                    </a>

                                                

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
                                        <span class="fw-bold"><?php echo $nombre ? $nombre : 'Error'; ?></span>
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
                                                    <h4><?php echo $nombre; ?></h4>
                                                    <p class="text-muted"><?php echo $correo ? $correo : 'Sin correo'; ?></p>

                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <!--<div class="dropdown-divider"></div> -->
                                            <!-- <a class="dropdown-item" href="#">Mi Perfil</a> -->
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

           <!-- <script src="assets/js/christmas-effects.js"></script> -->
                        <script src="assets/js/new-year.js"></script>

            <script>
                
                document.addEventListener("DOMContentLoaded", function() {
                    let menuItems = document.querySelectorAll(".nav-item a");
                    let currentPath = window.location.pathname.split("/").pop(); // Obtiene solo el nombre del archivo

                    menuItems.forEach(item => {
                        let menuPath = item.getAttribute("href").split("/").pop(); // Obtiene solo el nombre del archivo

                        if (currentPath.includes(menuPath) && menuPath !== "") {
                            document.querySelectorAll(".nav-item").forEach(nav => nav.classList.remove("active")); // Remueve la clase active de todos
                            item.closest(".nav-item").classList.add("active"); // Agrega active al elemento correcto
                        }
                    });
                });
            </script>