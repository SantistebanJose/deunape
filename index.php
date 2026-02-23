<?php include("cabecera.php") ?>

<div class="container px-4 py-4">
    <div class="page-inner">
        
        <!-- Header Mejorado con Mejor Visibilidad -->
        <div class="dashboard-header mb-4">
            <div class="row align-items-center">
                <div class="col-lg-8 col-md-7 col-12 mb-3 mb-md-0">
                    <div class="header-content">
                        <div class="icon-badge">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        <div class="header-text">
                            <h1 class="dashboard-title">Panel de Acceso Rápido</h1>
                            <p class="dashboard-subtitle">Gestiona tus procesos de negocio de manera eficiente. Selecciona una opción para comenzar.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5 col-12">
                    <div class="time-badge">
                        <i class="far fa-clock"></i>
                        <span id="currentDate"></span>
                        <span class="time" id="currentTime"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Principal -->
        <div class="main-access-card">
            
            <!-- Header del Card 
            <div class="access-card-header">
                <i class="fas fa-bolt"></i>
                <span>Accesos Rápidos</span>
            </div> -->

            <!-- Contenido -->
            <div class="access-card-body">
                
                <!-- Módulo de Ventas -->
                <div class="module-section">
                    <div class="module-header">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Módulo de Ventas</span>
                    </div>
                    
                    <div class="access-grid">
                        <a href="venta_rapida_v2.php" class="access-item access-blue">
                            <div class="access-icon">
                            <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="access-info">
                                <h3>Venta Rápida</h3>
                                <p>Realiza ventas al instante</p>
                            </div>
                            <div class="access-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>

                        <a href="venta_reserva_corte.php" class="access-item access-green">
                            <div class="access-icon">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div class="access-info">
                                <h3>Venta Por Reserva</h3>
                                <p>Gestiona reservas de clientes</p>
                            </div>
                            <div class="access-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>

                        <a href="venta_corte_material.php" class="access-item access-purple">
                            <div class="access-icon">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <div class="access-info">
                                <h3>Atender Reserva</h3>
                                <p>Procesa pedidos reservados</p>
                            </div>
                            <div class="access-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Módulo Financiero -->
                <div class="module-section">
                    <div class="module-header">
                        <i class="fas fa-dollar-sign"></i>
                        <span>Módulo Financiero</span>
                    </div>
                    
                    <div class="access-grid">
                        <a href="cajaChica.php" class="access-item access-red">
                            <div class="access-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="access-info">
                                <h3>Caja Chica</h3>
                                <p>Control de gastos menores</p>
                            </div>
                            <div class="access-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>

                        <a href="manejoCaja.php" class="access-item access-orange">
                            <div class="access-icon">
                                <i class="fas fa-coins"></i>
                            </div>
                            <div class="access-info">
                                <h3>Manejo de Caja</h3>
                                <p>Administra tu caja principal</p>
                            </div>
                            <div class="access-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>

                        <a href="pagoCredito.php" class="access-item access-dark">
                            <div class="access-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div class="access-info">
                                <h3>Pagos al Crédito</h3>
                                <p>Gestiona créditos y cobros</p>
                            </div>
                            <div class="access-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Herramientas -->
                <div class="module-section">
                    <div class="module-header">
                        <i class="fas fa-tools"></i>
                        <span>Herramientas</span>
                    </div>
                    
                    <div class="access-grid">
                        <a href="generador_etiquetas.php" class="access-item access-cyan">
                            <div class="access-icon">
                                <i class="fas fa-tags"></i>
                            </div>
                            <div class="access-info">
                                <h3>Etiquetas de Precios</h3>
                                <p>Genera etiquetas para productos</p>
                            </div>
                            <div class="access-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- JavaScript para el reloj en tiempo real -->
<script>
function updateDateTime() {
    const now = new Date();
    
    // Formato de fecha: DD/MM/YYYY
    const day = String(now.getDate()).padStart(2, '0');
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const year = now.getFullYear();
    const dateString = `${day}/${month}/${year}`;
    
    // Formato de hora: HH:MM:SS
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const timeString = `${hours}:${minutes}:${seconds}`;
    
    // Actualizar elementos del DOM
    const dateElement = document.getElementById('currentDate');
    const timeElement = document.getElementById('currentTime');
    
    if (dateElement) dateElement.textContent = dateString;
    if (timeElement) timeElement.textContent = timeString;
}

// Actualizar inmediatamente al cargar la página
updateDateTime();

// Actualizar cada segundo
setInterval(updateDateTime, 1000);
</script>

<style>
/* ================================================
   ESTILOS ULTRA MODERNOS - INDEX DEUNAPE
================================================ */

/* Reset y Base */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.page-inner {
    max-width: 1400px;
    margin: 0 auto;
}

/* ================================================
   HEADER DASHBOARD - MEJORADO PARA VISIBILIDAD
================================================ */
.dashboard-header {
    animation: slideDown 0.6s ease-out;
    background: #ffffff;
    padding: 1.5rem;
    border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.header-content {
    display: flex;
    align-items: flex-start;
    gap: 1.25rem;
}

.icon-badge {
    width: 65px;
    height: 65px;
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    box-shadow: 0 8px 20px rgba(46, 204, 113, 0.3);
    flex-shrink: 0;
}

.header-text {
    flex: 1;
    padding-top: 0.25rem;
}

.dashboard-title {
    font-size: 1.85rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 0.5rem 0;
    line-height: 1.2;
}

.dashboard-subtitle {
    font-size: 1rem;
    color: #7f8c8d;
    margin: 0;
    line-height: 1.6;
    max-width: 600px;
}

.time-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    color: white;
    padding: 0.85rem 1.5rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.95rem;
    box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
    width: 100%;
    max-width: 300px;
    margin-left: auto;
}

.time-badge i {
    font-size: 1.2rem;
}

.time-badge .time {
    font-weight: 700;
    font-size: 1.05rem;
    letter-spacing: 0.5px;
    font-family: 'Courier New', monospace;
}

/* ================================================
   CARD PRINCIPAL
================================================ */
.main-access-card {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    animation: fadeInUp 0.7s ease-out;
    margin-top: 1.5rem;
}

.access-card-header {
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
    padding: 1.5rem 2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    color: white;
    font-size: 1.3rem;
    font-weight: 700;
    border-bottom: 4px solid #1e8449;
}

.access-card-header i {
    font-size: 1.5rem;
}

.access-card-body {
    padding: 2.5rem;
}

/* ================================================
   MÓDULOS Y SECCIONES
================================================ */
.module-section {
    margin-bottom: 3rem;
}

.module-section:last-child {
    margin-bottom: 0;
}

.module-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #ecf0f1;
    font-size: 1.15rem;
    font-weight: 700;
    color: #2c3e50;
}

.module-header i {
    font-size: 1.3rem;
    color: #2ecc71;
}

/* ================================================
   GRID DE ACCESOS
================================================ */
.access-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1.5rem;
}

/* ================================================
   ITEMS DE ACCESO - MEJORADO PARA ICONOS VISIBLES
================================================ */
.access-item {
    position: relative;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1.75rem 1.5rem;
    background: #ffffff;
    border: 2px solid #e8e8e8;
    border-radius: 16px;
    text-decoration: none;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    cursor: pointer;
}

.access-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: currentColor;
    opacity: 0;
    transition: opacity 0.4s ease;
    z-index: 0;
}

.access-item:hover::before {
    opacity: 0.05;
}

.access-item:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    border-color: currentColor;
}

.access-icon {
    position: relative;
    width: 70px;
    height: 70px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 16px;
    font-size: 2rem;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1;
}

.access-icon i {
    position: relative;
    z-index: 2;
    font-weight: 900 !important;
}

.access-item:hover .access-icon {
    transform: scale(1.15) rotate(-5deg);
}

.access-info {
    flex: 1;
    position: relative;
    z-index: 1;
}

.access-info h3 {
    font-size: 1.15rem;
    font-weight: 700;
    margin: 0 0 0.35rem 0;
    color: #2c3e50;
    transition: color 0.3s ease;
}

.access-info p {
    font-size: 0.9rem;
    color: #7f8c8d;
    margin: 0;
    line-height: 1.4;
}

.access-arrow {
    position: relative;
    font-size: 1.5rem;
    opacity: 0;
    transform: translateX(-15px);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1;
}

.access-item:hover .access-arrow {
    opacity: 1;
    transform: translateX(0);
}

/* ================================================
   COLORES ESPECÍFICOS - MEJORADOS PARA VISIBILIDAD
================================================ */

/* Azul */
.access-blue {
    color: #3498db;
}

.access-blue .access-icon {
    background: linear-gradient(135deg, rgba(52, 152, 219, 0.25) 0%, rgba(41, 128, 185, 0.25) 100%);
    color: #2471a3;
    box-shadow: 0 8px 20px rgba(52, 152, 219, 0.3);
}

.access-blue .access-icon i {
    color: #1f5d8c;
    font-weight: 900;
}

/* Verde */
.access-green {
    color: #2ecc71;
}

.access-green .access-icon {
    background: linear-gradient(135deg, rgba(46, 204, 113, 0.2) 0%, rgba(39, 174, 96, 0.2) 100%);
    color: #27ae60;
    box-shadow: 0 8px 20px rgba(46, 204, 113, 0.25);
}

/* Púrpura */
.access-purple {
    color: #9b59b6;
}

.access-purple .access-icon {
    background: linear-gradient(135deg, rgba(155, 89, 182, 0.2) 0%, rgba(142, 68, 173, 0.2) 100%);
    color: #8e44ad;
    box-shadow: 0 8px 20px rgba(155, 89, 182, 0.25);
}

/* Rojo */
.access-red {
    color: #e74c3c;
}

.access-red .access-icon {
    background: linear-gradient(135deg, rgba(231, 76, 60, 0.2) 0%, rgba(192, 57, 43, 0.2) 100%);
    color: #c0392b;
    box-shadow: 0 8px 20px rgba(231, 76, 60, 0.25);
}

/* Naranja */
.access-orange {
    color: #f39c12;
}

.access-orange .access-icon {
    background: linear-gradient(135deg, rgba(243, 156, 18, 0.2) 0%, rgba(230, 126, 34, 0.2) 100%);
    color: #e67e22;
    box-shadow: 0 8px 20px rgba(243, 156, 18, 0.25);
}

/* Oscuro */
.access-dark {
    color: #34495e;
}

.access-dark .access-icon {
    background: linear-gradient(135deg, rgba(52, 73, 94, 0.2) 0%, rgba(44, 62, 80, 0.2) 100%);
    color: #2c3e50;
    box-shadow: 0 8px 20px rgba(52, 73, 94, 0.25);
}

/* Cian */
.access-cyan {
    color: #1abc9c;
}

.access-cyan .access-icon {
    background: linear-gradient(135deg, rgba(26, 188, 156, 0.2) 0%, rgba(22, 160, 133, 0.2) 100%);
    color: #16a085;
    box-shadow: 0 8px 20px rgba(26, 188, 156, 0.25);
}

/* ================================================
   ANIMACIONES
================================================ */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ================================================
   RESPONSIVE DESIGN
================================================ */

/* Tablets grandes (992px - 1199px) */
@media (max-width: 1199px) {
    .access-grid {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    }
}

/* Tablets (768px - 991px) */
@media (max-width: 991px) {
    .dashboard-header {
        padding: 1.25rem;
    }
    
    .dashboard-title {
        font-size: 1.6rem;
    }
    
    .dashboard-subtitle {
        font-size: 0.95rem;
    }
    
    .icon-badge {
        width: 60px;
        height: 60px;
        font-size: 1.8rem;
    }
    
    .time-badge {
        max-width: 100%;
    }
    
    .access-card-header {
        font-size: 1.2rem;
        padding: 1.25rem 1.75rem;
    }
    
    .access-card-body {
        padding: 2rem;
    }
    
    .access-grid {
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.25rem;
    }
}

/* Móviles grandes (576px - 767px) */
@media (max-width: 767px) {
    .dashboard-header {
        padding: 1rem;
    }
    
    .header-content {
        gap: 1rem;
    }
    
    .icon-badge {
        width: 55px;
        height: 55px;
        font-size: 1.6rem;
    }
    
    .dashboard-title {
        font-size: 1.4rem;
    }
    
    .dashboard-subtitle {
        font-size: 0.9rem;
    }
    
    .time-badge {
        padding: 0.75rem 1.25rem;
        font-size: 0.9rem;
    }
    
    .access-card-header {
        font-size: 1.1rem;
        padding: 1rem 1.5rem;
    }
    
    .access-card-body {
        padding: 1.5rem;
    }
    
    .module-section {
        margin-bottom: 2.5rem;
    }
    
    .module-header {
        font-size: 1.05rem;
        margin-bottom: 1.25rem;
    }
    
    .access-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .access-item {
        padding: 1.5rem 1.25rem;
    }
    
    .access-icon {
        width: 65px;
        height: 65px;
        font-size: 1.8rem;
    }
    
    .access-info h3 {
        font-size: 1.05rem;
    }
    
    .access-info p {
        font-size: 0.85rem;
    }
}

/* Móviles pequeños (menos de 576px) */
@media (max-width: 575px) {
    .container-fluid {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    
    .dashboard-header {
        padding: 1rem;
    }
    
    .header-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .icon-badge {
        width: 50px;
        height: 50px;
        font-size: 1.4rem;
    }
    
    .dashboard-title {
        font-size: 1.25rem;
    }
    
    .dashboard-subtitle {
        font-size: 0.85rem;
        line-height: 1.5;
    }
    
    .time-badge {
        padding: 0.65rem 1rem;
        font-size: 0.85rem;
        width: 100%;
        max-width: 100%;
    }
    
    .time-badge .time {
        font-size: 1rem;
    }
    
    .main-access-card {
        border-radius: 16px;
    }
    
    .access-card-header {
        font-size: 1rem;
        padding: 0.85rem 1.25rem;
        gap: 0.75rem;
    }
    
    .access-card-header i {
        font-size: 1.2rem;
    }
    
    .access-card-body {
        padding: 1.25rem;
    }
    
    .module-section {
        margin-bottom: 2rem;
    }
    
    .module-header {
        font-size: 1rem;
        gap: 0.6rem;
        margin-bottom: 1rem;
    }
    
    .module-header i {
        font-size: 1.15rem;
    }
    
    .access-item {
        padding: 1.25rem 1rem;
        gap: 1rem;
        border-radius: 14px;
    }
    
    .access-icon {
        width: 60px;
        height: 60px;
        font-size: 1.6rem;
        border-radius: 12px;
    }
    
    .access-info h3 {
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }
    
    .access-info p {
        font-size: 0.8rem;
        line-height: 1.3;
    }
    
    .access-arrow {
        font-size: 1.2rem;
    }
}

/* Móviles muy pequeños (menos de 400px) */
@media (max-width: 399px) {
    .dashboard-title {
        font-size: 1.15rem;
    }
    
    .dashboard-subtitle {
        font-size: 0.8rem;
    }
    
    .access-card-body {
        padding: 1rem;
    }
    
    .access-item {
        padding: 1rem 0.85rem;
    }
    
    .access-icon {
        width: 55px;
        height: 55px;
        font-size: 1.5rem;
    }
    
    .access-info h3 {
        font-size: 0.95rem;
    }
    
    .access-info p {
        font-size: 0.75rem;
    }
}

/* ================================================
   MEJORAS DE RENDIMIENTO
================================================ */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Optimización para pantallas táctiles */
@media (hover: none) and (pointer: coarse) {
    .access-item:active {
        transform: scale(0.98);
    }
}
</style>

<?php include("pie.php") ?>