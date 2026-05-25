<?php
include("cabecera.php");
include("logica/clssVenta.php");
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}
$sucursal_id = $_SESSION["sucursal_id"];
?>
<style>
    :root {
        --primary: #2a2f5b;
        --primary-light: #3d4480;
        --accent: #667eea;
        --accent2: #764ba2;
        --success: #11998e;
        --success-light: #38ef7d;
        --warning: #f7971e;
        --danger: #dc3545;
        --bg-card: #f8f9ff;
        --border-soft: #e3e6f5;
        --text-muted: #6c757d;
        --gradient-main: #0033A0;
        --gradient-success: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        --shadow-card: 0 4px 20px rgba(42, 47, 91, 0.10);
        --shadow-hover: 0 8px 30px rgba(102, 126, 234, 0.18);
    }

    /* ===== HEADER ===== */
    .page-header {
        background: var(--gradient-main);
        border-radius: 18px;
        color: white;
        padding: 26px 30px 20px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-hover);
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: "📋";
        position: absolute;
        right: 28px;
        top: 14px;
        font-size: 3.5rem;
        opacity: .18;
        pointer-events: none;
    }

    .page-header h3 {
        font-weight: 800;
        margin-bottom: 4px;
    }

    .page-header p {
        opacity: .85;
        margin-bottom: 0;
        font-size: .95rem;
    }

    /* ===== CARDS BLANCAS ===== */
    .panel-white {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-card);
        padding: 22px 20px;
        margin-bottom: 20px;
    }

    .section-title {
        font-weight: 700;
        color: var(--primary);
        font-size: 1rem;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-title i {
        color: var(--accent);
    }

    /* ===== DATATABLE RESERVAS ===== */
    .tabla-reservas-wrap {
        overflow-x: auto;
    }

    #multi-filter-select thead tr th {
        background: var(--gradient-main) !important;
        color: white !important;
        font-weight: 700;
        border: none;
        padding: 12px 14px;
    }

    #multi-filter-select tbody tr:hover {
        background: #f0f3ff;
    }

    #multi-filter-select tbody tr td {
        padding: 10px 14px;
        vertical-align: middle;
        border-color: #f0f3ff;
    }

    #multi-filter-select tfoot tr td select {
        border: 1.5px solid var(--border-soft);
        border-radius: 8px;
        padding: 4px 8px;
        font-size: .78rem;
    }

    .badge-estado {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: .75rem;
        font-weight: 700;
    }

    .badge-reserva {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-atendida {
        background: #d1fae5;
        color: #065f46;
    }

    .btn-ver {
        background: var(--gradient-main);
        border: none;
        color: white;
        border-radius: 10px;
        padding: 6px 16px;
        font-size: .82rem;
        font-weight: 700;
        transition: all .2s;
        cursor: pointer;
    }

    .btn-ver:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, .35);
        color: white;
    }

    /* ===== PANEL DETALLES ===== */
    .panel-cliente {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-card);
        padding: 20px;
    }

    .info-block {
        margin-bottom: 10px;
        font-size: .88rem;
    }

    .info-block .lbl {
        color: var(--text-muted);
        font-size: .75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .info-block .val {
        font-weight: 700;
        color: var(--primary);
    }

    /* ===== TABLA DETALLE VENTA ===== */
    #tabla_articulos thead tr th {
        background: var(--gradient-main) !important;
        color: white !important;
        font-weight: 700;
        border: none;
        padding: 10px 12px;
        font-size: .82rem;
    }

    #tabla_articulos tbody tr:hover {
        background: #f8f9ff;
    }

    #tabla_articulos tbody tr td {
        padding: 9px 12px;
        vertical-align: middle;
        border-color: #f0f3ff;
        font-size: .85rem;
    }

    #tabla_articulos th:nth-child(1),
    #tabla_articulos td:nth-child(1),
    #tabla_articulos th:nth-child(10),
    #tabla_articulos td:nth-child(10),
    #tabla_articulos th:nth-child(11),
    #tabla_articulos td:nth-child(11),
    #tabla_articulos th:nth-child(12),
    #tabla_articulos td:nth-child(12) {
        display: none !important;
    }

    /* ===== RESUMEN TOTALES ===== */
    .resumen-stat {
        background: white;
        border-radius: 14px;
        box-shadow: var(--shadow-card);
        padding: 16px;
        text-align: center;
    }

    .resumen-stat .stat-lbl {
        font-size: .78rem;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .resumen-stat .stat-val {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--primary);
    }

    .resumen-stat.total-card {
        background: var(--gradient-main);
    }

    .resumen-stat.total-card .stat-lbl {
        color: rgba(255, 255, 255, .8);
    }

    .resumen-stat.total-card .stat-val {
        color: white;
    }

    /* ===== PANEL AGREGAR (venta rápida style) ===== */
    .panel-agregar {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-card);
        margin-bottom: 20px;
        overflow: visible;
    }

    .panel-agregar-header {
        background: var(--gradient-main);
        color: white;
        border-radius: 16px 16px 0 0;
        padding: 16px 22px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .panel-agregar-header h6 {
        font-weight: 800;
        margin: 0;
    }

    .panel-agregar-body {
        padding: 20px;
        overflow: visible;
        position: relative;
    }

    /* ===== SERVICIOS ===== */
    .servicios-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-bottom: 16px;
    }

    .btn-servicio {
        background: white;
        border: 2px solid var(--border-soft);
        border-radius: 20px;
        padding: 6px 14px;
        font-weight: 700;
        font-size: .78rem;
        color: var(--primary);
        transition: all .2s;
        cursor: pointer;
    }

    .btn-servicio:hover {
        background: var(--gradient-main);
        border-color: transparent;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 3px 10px rgba(102, 126, 234, .3);
    }

    .btn-servicio-special {
        background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
        border-color: transparent;
        color: var(--primary);
    }

    .btn-servicio-special:hover {
        color: var(--primary);
    }

    /* ===== FILTROS ===== */
    .filtro-input {
        border: 2px solid var(--border-soft);
        border-radius: 12px;
        padding: 8px 12px;
        font-size: .85rem;
        width: 100%;
        transition: border-color .2s;
        background: white;
    }

    .filtro-input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, .12);
        outline: none;
    }

    /* ===== TABS MODO AGREGAR ===== */
    .add-mode-tabs {
        display: flex;
        gap: 6px;
        margin-bottom: 14px;
    }

    .add-mode-tab {
        flex: 1;
        padding: 8px 6px;
        border: 2px solid var(--border-soft);
        border-radius: 10px;
        background: white;
        color: var(--text-muted);
        font-weight: 600;
        font-size: .78rem;
        cursor: pointer;
        transition: all .2s;
        text-align: center;
    }

    .add-mode-tab:hover {
        border-color: var(--accent);
        color: var(--accent);
    }

    .add-mode-tab.active {
        background: var(--gradient-main);
        border-color: transparent;
        color: white;
        box-shadow: 0 3px 10px rgba(102, 126, 234, .3);
    }

    .add-panel {
        display: none;
    }

    .add-panel.active {
        display: block;
    }

    /* ===== SEARCH WRAPPER ===== */
    .search-wrapper {
        position: relative;
        width: 100%;
    }

    #searchInput {
        border: 2px solid var(--border-soft);
        border-radius: 12px;
        padding: 10px 40px 10px 16px;
        font-size: .9rem;
        width: 100%;
        transition: border-color .2s, box-shadow .2s;
        background: #fafbff;
    }

    #searchInput:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, .12);
        outline: none;
        background: white;
    }

    .search-icon-abs {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--accent);
        pointer-events: none;
    }

    /* ===== DROPDOWN RESULTADOS ===== */
    #resultadosProductos {
        max-height: 320px;
        overflow-y: auto;
        border: 1.5px solid var(--border-soft);
        border-radius: 14px;
        margin-top: 4px;
        background: white;
        box-shadow: 0 8px 30px rgba(0, 0, 0, .13);
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        z-index: 1050;
    }

    #resultadosProductos::-webkit-scrollbar {
        width: 6px;
    }

    #resultadosProductos::-webkit-scrollbar-track {
        background: #f0f3ff;
        border-radius: 10px;
    }

    #resultadosProductos::-webkit-scrollbar-thumb {
        background: var(--accent);
        border-radius: 10px;
    }

    .producto-item {
        padding: 11px 16px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f5;
        transition: background .15s;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .producto-item:hover {
        background: #f0f3ff;
    }

    .producto-item:last-child {
        border-bottom: none;
    }

    .prod-nombre {
        font-weight: 700;
        font-size: .85rem;
        color: #1a1a2e;
        line-height: 1.3;
        text-transform: uppercase;
    }

    .prod-info {
        font-size: .72rem;
        color: var(--text-muted);
        margin-top: 3px;
    }

    .prod-precio-badge {
        font-weight: 800;
        color: white;
        font-size: .88rem;
        white-space: nowrap;
        background: var(--gradient-main);
        border-radius: 20px;
        padding: 4px 12px;
        flex-shrink: 0;
    }

    .sin-stock-badge {
        display: inline-block;
        background: #fee2e2;
        color: var(--danger);
        border-radius: 12px;
        padding: 1px 8px;
        font-size: .68rem;
        font-weight: 700;
    }

    .search-hint {
        font-size: .78rem;
        color: var(--text-muted);
        padding: 16px 0 4px;
        text-align: center;
        opacity: .7;
    }

    /* ===== FORM MANUAL ===== */
    .form-manual label {
        font-size: .83rem;
        font-weight: 600;
        color: var(--primary);
    }

    .form-manual .form-control {
        border: 1.5px solid var(--border-soft);
        border-radius: 10px;
        font-size: .88rem;
        padding: 8px 12px;
    }

    .form-manual .form-control:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, .12);
    }

    /* ===== MODALES ===== */
    .modal-content {
        border-radius: 18px;
        border: none;
        box-shadow: 0 14px 50px rgba(0, 0, 0, .2);
        overflow: hidden;
    }

    .modal-header-gradient {
        background: var(--gradient-main);
        color: white;
        padding: 18px 22px;
        border-bottom: none;
    }

    .modal-header-gradient .btn-close {
        filter: brightness(0) invert(1);
    }

    .modal-header-gradient .modal-title {
        font-weight: 800;
    }

    .modal-header-success {
        background: var(--gradient-success);
        color: white;
        padding: 18px 22px;
        border-bottom: none;
    }

    .modal-header-success .btn-close {
        filter: brightness(0) invert(1);
    }

    .modal-body .form-control,
    .modal-body .form-select {
        border: 1.5px solid var(--border-soft);
        border-radius: 10px;
        font-size: .9rem;
        padding: 9px 14px;
    }

    .modal-body .form-control:focus,
    .modal-body .form-select:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, .12);
    }

    .modal-body .input-group-text {
        border: 1.5px solid var(--border-soft);
        border-radius: 10px 0 0 10px;
        background: #f0f3ff;
    }

    /* ===== QTY CONTROL ===== */
    .qty-control {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: var(--bg-card);
        border-radius: 14px;
        padding: 8px 14px;
        border: 1.5px solid var(--border-soft);
    }

    .qty-control input {
        width: 72px;
        text-align: center;
        border: none;
        background: transparent;
        font-weight: 800;
        font-size: 1.1rem;
        color: var(--primary);
        outline: none;
    }

    .qty-btn {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: none;
        font-weight: 800;
        font-size: 1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all .2s;
    }

    .qty-btn-minus {
        background: #fee2e2;
        color: var(--danger);
    }

    .qty-btn-plus {
        background: #d1fae5;
        color: var(--success);
    }

    .qty-btn:hover {
        transform: scale(1.15);
    }

    /* ===== PRICE PILLS ===== */
    .price-pills {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 8px;
    }

    .price-pill {
        background: #f0f3ff;
        border: 1.5px solid var(--border-soft);
        border-radius: 16px;
        padding: 4px 14px;
        font-size: .78rem;
        font-weight: 700;
        color: var(--primary);
        cursor: pointer;
        transition: all .2s;
    }

    .price-pill:hover {
        background: var(--gradient-main);
        border-color: transparent;
        color: white;
    }

    /* ===== TABS MODAL PAGO ===== */
    .nav-pago .nav-link {
        border-radius: 10px;
        font-weight: 600;
        font-size: .88rem;
        color: var(--text-muted);
        padding: 8px 18px;
        transition: all .2s;
        border: none;
    }

    .nav-pago .nav-link.active {
        background: var(--gradient-main);
        color: white;
        box-shadow: 0 3px 10px rgba(102, 126, 234, .3);
    }

    .nav-pago .nav-link:not(.active):hover {
        background: #f0f3ff;
        color: var(--primary);
    }

    /* ===== FORMA PAGO ROW ===== */
    .pago-row {
        background: var(--bg-card);
        border: 1.5px solid var(--border-soft);
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 8px;
    }

    .pago-row select,
    .pago-row input {
        border: 1.5px solid var(--border-soft);
        border-radius: 10px;
        padding: 8px 12px;
        font-size: .88rem;
    }

    .pago-row select:focus,
    .pago-row input:focus {
        border-color: var(--accent);
        outline: none;
    }

    /* ===== IMPUESTOS MODAL ===== */
    .impuestos-box {
        background: #f8f9ff;
        border: 1.5px solid var(--border-soft);
        border-radius: 14px;
        padding: 14px 18px;
        margin-bottom: 16px;
    }

    .impuestos-box .imp-titulo {
        font-size: .78rem;
        font-weight: 800;
        color: var(--primary);
        text-transform: uppercase;
        letter-spacing: .6px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .imp-linea {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 5px 0;
        border-bottom: 1px dashed var(--border-soft);
        font-size: .84rem;
    }

    .imp-linea:last-child {
        border-bottom: none;
    }

    .imp-lbl {
        color: var(--text-muted);
        font-weight: 500;
    }

    .imp-val {
        font-weight: 700;
        color: var(--primary);
    }

    .imp-val.cero {
        color: #adb5bd;
        font-weight: 400;
    }

    .imp-linea.igv-line .imp-lbl {
        color: #b45309;
        font-weight: 700;
    }

    .imp-linea.igv-line .imp-val {
        color: #b45309;
    }

    .imp-linea.icbper-line .imp-lbl {
        color: #0891b2;
    }

    .imp-linea.icbper-line .imp-val {
        color: #0891b2;
    }

    .imp-linea.total-imp {
        background: var(--gradient-main);
        border-radius: 10px;
        padding: 8px 12px;
        margin-top: 6px;
    }

    .imp-linea.total-imp .imp-lbl {
        color: rgba(255, 255, 255, .85);
        font-weight: 700;
        font-size: .9rem;
    }

    .imp-linea.total-imp .imp-val {
        color: white;
        font-size: 1.05rem;
    }

    /* ===== VUELTO ===== */
    #seccionVuelto {
        background: #f0fff8;
        border: 1.5px solid #a7f3d0;
        border-radius: 14px;
        padding: 16px;
        margin-top: 14px;
        animation: slideDown .3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-14px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .pago-rapido-btns {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .pago-rapido-btn {
        background: #e8f5e9;
        border: 1.5px solid #a7d7a8;
        border-radius: 16px;
        padding: 5px 14px;
        font-size: .82rem;
        font-weight: 700;
        color: var(--success);
        cursor: pointer;
        transition: all .2s;
    }

    .pago-rapido-btn:hover {
        background: var(--gradient-success);
        border-color: transparent;
        color: white;
    }

    /* ===== BOTONES GLOBALES ===== */
    .btn-success-custom {
        background: var(--gradient-success);
        border: none;
        color: white;
        border-radius: 12px;
        padding: 10px 24px;
        font-weight: 700;
        font-size: .92rem;
        transition: all .25s;
        box-shadow: 0 3px 12px rgba(17, 153, 142, .25);
        cursor: pointer;
    }

    .btn-success-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(17, 153, 142, .35);
        color: white;
    }

    .btn-primary-custom {
        background: var(--gradient-main);
        border: none;
        color: white;
        border-radius: 12px;
        padding: 10px 24px;
        font-weight: 700;
        font-size: .92rem;
        transition: all .25s;
        cursor: pointer;
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        color: white;
    }

    /* ===== ANIMACIONES ===== */
    @keyframes fadeInRow {
        from {
            opacity: 0;
            transform: translateY(-6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in-panel {
        animation: fadeInRow .35s ease;
    }

    @media(max-width:768px) {
        .page-header::before {
            display: none;
        }

        .page-header {
            padding: 16px;
        }
    }
</style>

<div class="container">
    <div class="page-inner">

        <!-- HEADER -->
        <div class="page-header">
            <h3><i class="fas fa-tasks me-2"></i>Atender Reservas</h3>
            <p>Aquí puedes atender las reservas de tus clientes. Agrega artículos adicionales y procesa el pago con desglose de impuestos.</p>
        </div>

        <!-- ===== TABLA DE RESERVAS ===== -->
        <div class="panel-white">
            <div class="section-title"><i class="fas fa-list-alt"></i>Reservas Pendientes</div>
            <div class="tabla-reservas-wrap">
                <table id="multi-filter-select" class="display table table-hover w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sucursal_id = isset($_SESSION['sucursal_id']) ? $_SESSION['sucursal_id'] : null;
                        foreach (listarVentaReservaCorte($sucursal_id) as $datosReserva):
                            $datosReservaJSON = json_encode($datosReserva);
                        ?>
                            <tr>
                                <td><strong style="color:var(--primary);">#<?php echo $datosReserva["venta_id"] ?></strong></td>
                                <td><i class="fas fa-user me-1" style="color:var(--accent);"></i><?php echo $datosReserva["cliente"] ?></td>
                                <td><?php echo $datosReserva["fecha"] ?></td>
                                <td><?php echo $datosReserva["hora"] ?></td>
                                <td><span class="badge-estado badge-reserva"><?php echo $datosReserva["estado_venta"] ?></span></td>
                                <td>
                                    <a href="#panelDetalles" class="btn-ver" onclick='fn_consultarVenta(<?php echo $datosReservaJSON; ?>)'>
                                        <i class="fas fa-eye me-1"></i>Ver Reserva
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- ===== PANEL DETALLES ===== -->
        <div id="panelDetalles" style="display:none;" class="fade-in-panel">

            <!-- Info Cliente + Atendido por -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="panel-cliente h-100">
                        <div class="section-title"><i class="fas fa-user-tie"></i>Cliente</div>
                        <div class="info-block">
                            <div class="lbl">Nombre</div>
                            <div class="val" id="idClienteReservaDetalle" style="font-size:1.1rem;"></div>
                        </div>
                        <hr style="border-color:var(--border-soft);">
                        <div class="row g-2">
                            <div class="col-sm-4">
                                <div class="info-block">
                                    <div class="lbl"><i class="fas fa-phone me-1"></i>Teléfono</div>
                                    <div class="val" id="idNumCelClienteReserva">—</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="info-block">
                                    <div class="lbl"><i class="fas fa-envelope me-1"></i>Correo</div>
                                    <div class="val" id="idCorreoClienteReserva" style="font-size:.82rem;">—</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="info-block">
                                    <div class="lbl"><i class="fas fa-id-card me-1"></i>Documento</div>
                                    <div class="val" id="idNumDocClienteReserva">—</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel-cliente h-100">
                        <div class="section-title"><i class="fas fa-user-clock"></i>Atendido Por</div>
                        <div class="info-block">
                            <div class="lbl">Usuario</div>
                            <div class="val" id="idUsuarioReservaDetalle" style="font-size:1.05rem;"></div>
                        </div>
                        <div class="info-block">
                            <div class="lbl">ID Venta</div>
                            <div class="val" id="idVentaReserva" style="font-size:1.1rem;color:var(--accent);"></div>
                        </div>
                        <div class="row g-2 mt-1">
                            <div class="col-sm-6">
                                <label class="lbl ms-1" style="font-size:.72rem;color:var(--text-muted);font-weight:600;">FECHA</label>
                                <input type="date" class="filtro-input mt-1" id="idFechaReservaDetalle" readonly style="background:#f8f9ff;">
                            </div>
                            <div class="col-sm-6">
                                <label class="lbl ms-1" style="font-size:.72rem;color:var(--text-muted);font-weight:600;">HORA</label>
                                <input type="text" class="filtro-input mt-1" id="idHoraReservaDetalle" readonly style="background:#f8f9ff;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla detalle de la reserva -->
            <div class="panel-white">
                <div class="section-title"><i class="fas fa-receipt"></i>Detalle de Venta</div>
                <div class="table-responsive">
                    <table id="tabla_articulos" class="table table-hover w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Minutos</th>
                                <th>Tarifa</th>
                                <th>T.Corte</th>
                                <th>Artículo</th>
                                <th>Cant.</th>
                                <th>P.Unit.</th>
                                <th>Subtotal (S/)</th>
                                <th>Acción</th>
                                <th>IDMOV</th>
                                <th>IDREL</th>
                                <th>NOTA</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <!-- Resumen totales + botón pago -->
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="resumen-stat">
                        <div class="stat-lbl"><i class="fas fa-cut me-1"></i>Servicios (S/)</div>
                        <div class="stat-val" id="id_subtotal_cortes">0.00</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="resumen-stat">
                        <div class="stat-lbl"><i class="fas fa-box me-1"></i>Artículos (S/)</div>
                        <div class="stat-val" id="id_subtotal_articulos">0.00</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="resumen-stat total-card">
                        <div class="stat-lbl">Total Venta (S/)</div>
                        <div class="stat-val" id="id_subtotal_general">0.00</div>
                    </div>
                </div>
            </div>
            <div class="text-center mb-4">
                <button id="btnRealizarPago" type="button" class="btn-success-custom" style="padding:14px 40px;font-size:1rem;">
                    <i class="fas fa-credit-card me-2"></i>Realizar Pago
                </button>
            </div>

        </div><!-- /panelDetalles -->

        <!-- ===== PANEL AGREGAR MÁS (estilo venta rápida) ===== -->
        <div id="panelAdicionarMas" class="panel-agregar" style="display:none;">
            <div class="panel-agregar-header">
                <h6><i class="fas fa-plus-circle me-2"></i>Agregar Más Artículos o Servicios</h6>
                <span style="opacity:.8;font-size:.82rem;">Busca artículos o agrégalos manualmente</span>
            </div>
            <div class="panel-agregar-body">

                <!-- Servicios -->
                <div class="section-title"><i class="fas fa-cube"></i>Servicios Rápidos</div>
                <div class="servicios-wrap">
                    <?php foreach (listarMovimientos($sucursal_id) as $datos): ?>
                        <button class="btn-servicio" onclick='fn_servicios(<?php echo json_encode($datos) ?>)'>
                            <i class="fas fa-external-link-alt me-1"></i><?php echo $datos["descripcion"] ?>
                        </button>
                    <?php endforeach; ?>
                    <button class="btn-servicio btn-servicio-special" id="btnAbrirModalSolo">
                        <i class="fas fa-cut me-1"></i>SOLO CORTE
                    </button>
                    <button class="btn-servicio btn-servicio-special" id="btnAbrirModalSolov2">
                        <i class="fas fa-print me-1"></i>IMPRESIÓN 3D
                    </button>
                </div>

                <hr style="border-color:var(--border-soft);margin:16px 0;">

                <!-- Filtros -->
                <div class="section-title"><i class="fas fa-filter"></i>Filtrar Productos</div>
                <div class="row g-2 mb-3">
                    <div class="col-md-3">
                        <select id="filterCategoria" class="filtro-input" onchange="filterProducts()">
                            <option value="">Categoría</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filterTipo" class="filtro-input" onchange="filterProducts()">
                            <option value="">Tipo</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filterDimension" class="filtro-input" onchange="filterProducts()">
                            <option value="">Dimensión</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="filterColor" class="filtro-input" onchange="filterProducts()">
                            <option value="">Color</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button onclick="clearFilters()" class="filtro-input" style="background:linear-gradient(135deg,#f7971e,#ffd200);color:var(--primary);cursor:pointer;font-weight:700;border:none;">
                            <i class="fas fa-broom"></i>
                        </button>
                    </div>
                </div>

                <!-- Tabs buscar / manual -->
                <div class="add-mode-tabs">
                    <button class="add-mode-tab active" id="tabBuscar" onclick="switchAddMode('buscar')">
                        <i class="fas fa-search me-1"></i>Buscar Artículo
                    </button>
                    <button class="add-mode-tab" id="tabManual" onclick="switchAddMode('manual')">
                        <i class="fas fa-pencil-alt me-1"></i>Manual
                    </button>
                </div>

                <!-- Panel buscar -->
                <div class="add-panel active" id="panelBuscar" style="position:relative;">
                    <div class="search-wrapper">
                        <input type="text" id="searchInput" placeholder="Buscar por nombre, categoría..." oninput="filterProducts()">
                        <i class="fas fa-search search-icon-abs"></i>
                    </div>
                    <div id="resultadosProductos" style="display:none;"></div>
                    <div id="searchHint" class="search-hint">
                        <i class="fas fa-keyboard me-1" style="color:var(--accent);"></i>
                        Escribe para buscar entre tus productos
                    </div>
                </div>

                <!-- Panel manual -->
                <div class="add-panel" id="panelManual">
                    <div class="form-manual">
                        <div class="mb-2">
                            <label>Descripción <span class="text-danger">*</span></label>
                            <input type="text" id="manualDescripcion" class="form-control mt-1" placeholder="Ej: Servicio especial...">
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label>Cantidad</label>
                                <input type="number" id="manualCantidad" class="form-control mt-1" value="1" min="1">
                            </div>
                            <div class="col-6">
                                <label>Precio (S/)</label>
                                <input type="number" id="manualPrecio" class="form-control mt-1" step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                        <button class="btn-primary-custom mt-1" onclick="agregarManual()" style="width:100%;">
                            <i class="fas fa-plus me-1"></i>Agregar a la Venta
                        </button>
                    </div>
                </div>

            </div>
        </div><!-- /panelAdicionarMas -->

    </div><!-- /page-inner -->
</div><!-- /container -->


<!-- ======= MODAL SOLO CORTE ======= -->
<div class="modal fade" id="modalSoloCorte" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-gradient">
                <h5 class="modal-title"><i class="fas fa-cut me-2"></i>Servicio de Corte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="contenido_solo_corte"></div>
        </div>
    </div>
</div>

<!-- ======= MODAL IMPRESIÓN 3D ======= -->
<div class="modal fade" id="modalSoloCorteMaquina2" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-gradient">
                <h5 class="modal-title"><i class="fas fa-print me-2"></i>Impresión 3D</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <label class="fw-bold mb-2 d-block" style="font-size:.85rem;color:var(--primary);">Tiempo (min)</label>
                    <div class="qty-control mx-auto" style="width:fit-content;">
                        <button class="qty-btn qty-btn-minus" onclick='fnAumentoOrResta("-")'>−</button>
                        <input id="cantidad_solocortev2" type="number" value="10">
                        <button class="qty-btn qty-btn-plus" onclick='fnAumentoOrResta("+")'>+</button>
                    </div>
                    <div class="price-pills mt-2">
                        <button class="price-pill" onclick="fnAumentarMin(15)">15m</button>
                        <button class="price-pill" onclick="fnAumentarMin(30)">30m</button>
                        <button class="price-pill" onclick="fnAumentarMin(45)">45m</button>
                        <button class="price-pill" onclick="fnAumentarMin(60)">1h</button>
                        <button class="price-pill" onclick="fnAumentarMin(120)">2h</button>
                        <button class="price-pill" onclick="fnAumentarMin(180)">3h</button>
                    </div>
                </div>
                <hr style="border-color:var(--border-soft);">
                <div class="text-center mb-3">
                    <label class="fw-bold mb-2 d-block" style="font-size:.85rem;color:var(--primary);">Precio (S/)</label>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <input id="precioSoloCortev2" type="number" class="form-control text-center fw-bold" value="1.5" style="width:130px;font-size:1.1rem;">
                        <button class="btn btn-sm btn-outline-secondary rounded-pill" onclick="limpiar()"><i class="fas fa-broom"></i></button>
                    </div>
                    <div class="price-pills mt-2">
                        <button class="price-pill" onclick="fnAumentaPrecioImpresion(0.5)">+0.5</button>
                        <button class="price-pill" onclick="fnAumentaPrecioImpresion(1)">+1</button>
                        <button class="price-pill" onclick="fnAumentaPrecioImpresion(2)">+2</button>
                        <button class="price-pill" onclick="fnAumentaPrecioImpresion(5)">+5</button>
                    </div>
                </div>
                <div>
                    <label class="fw-bold mb-1" style="font-size:.83rem;color:var(--primary);">Nota</label>
                    <textarea class="form-control mt-1" id="nota_impresion3d" rows="2" placeholder="Modelo, color, etc." style="font-size:.87rem;"></textarea>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--border-soft);">
                <button class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn-success-custom" id="btn_agregar_solocortev2"><i class="fas fa-plus me-1"></i>Agregar</button>
            </div>
        </div>
    </div>
</div>

<!-- ======= MODAL CANTIDAD Y CORTE ======= -->
<div class="modal fade" id="modalCantidad" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-gradient">
                <h5 class="modal-title"><i class="fas fa-cogs me-2"></i>Configurar Artículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="contenid_cantidad"></div>
            <div class="modal-footer" style="border-top:1px solid var(--border-soft);">
                <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- ======= MODAL GENÉRICO ======= -->
<div class="modal fade" id="modalGenerico" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0" id="modalContent"></div>
            <div class="modal-footer" style="border-top:1px solid var(--border-soft);">
                <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- ======= MODAL REALIZAR PAGO (con desglose de impuestos) ======= -->
<div class="modal fade" id="modalRealizarPago" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-success">
                <div class="w-100 text-center">
                    <h5 class="mb-1 fw-bold"><i class="fas fa-credit-card me-2"></i>Realizar Pago</h5>
                    <h2 class="mb-0 fw-bold">S/ <span id="idMontoVentaTitulo">0.00</span></h2>
                </div>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 mb-3" style="background:var(--bg-card);border-radius:12px;font-size:.85rem;">
                    <span>ID Venta: <strong><span id="idVenta">#</span></strong></span> &nbsp;|&nbsp;
                    <span>ID Cliente: <strong><span id="idPersona">#</span></strong></span> &nbsp;|&nbsp;
                    <span>Atendiendo: <strong><span id="idAtencionFinal"><?php echo $id_usuario_s . "-" . $nombre . ", " . $ape_usuario ?></span></strong></span>
                    <span style="display:none;" id="idUsuario"></span>
                </div>

                <div class="mb-3">
                    <label class="fw-bold mb-1" style="font-size:.83rem;color:var(--primary);"><i class="fas fa-user-tie me-1"></i>Cliente</label>
                    <input type="text" class="form-control mt-1" id="nombreCliente" placeholder="Nombre del cliente" readonly>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold mb-1" style="font-size:.83rem;color:var(--primary);"><i class="fas fa-phone me-1"></i>Teléfono</label>
                        <input type="number" class="form-control mt-1" id="idUpdateNumTelefonoCliente" placeholder="—">
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold mb-1" style="font-size:.83rem;color:var(--primary);"><i class="fas fa-envelope me-1"></i>Correo</label>
                        <input type="email" class="form-control mt-1" id="idUpdateCorreoCliente" placeholder="—">
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold mb-1" style="font-size:.83rem;">Monto Original (S/)</label>
                        <div class="input-group mt-1"><span class="input-group-text">S/</span><input type="text" class="form-control" id="montoTotal" readonly></div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold mb-1" style="font-size:.83rem;">Monto Final (S/) <small style="color:var(--text-muted);">con descuento</small></label>
                        <div class="input-group mt-1"><span class="input-group-text">S/</span><input type="number" class="form-control" id="montoTotalFinal" placeholder="0.00"></div>
                    </div>
                </div>

                <!-- DESGLOSE DE IMPUESTOS -->
                <div class="impuestos-box" id="impuestosBox">
                    <div class="imp-titulo"><i class="fas fa-file-invoice-dollar" style="color:var(--accent);"></i>Desglose Tributario</div>
                    <div id="impuestosContenido">
                        <div class="imp-linea"><span class="imp-lbl">Op. Gravadas (Base IGV)</span><span class="imp-val cero" id="imp_gravadas">S/ 0.00</span></div>
                        <div class="imp-linea"><span class="imp-lbl">Op. Exoneradas</span><span class="imp-val cero" id="imp_exoneradas">S/ 0.00</span></div>
                        <div class="imp-linea"><span class="imp-lbl">Op. Inafectas</span><span class="imp-val cero" id="imp_inafectas">S/ 0.00</span></div>
                        <div class="imp-linea igv-line"><span class="imp-lbl">IGV (18%)</span><span class="imp-val" id="imp_igv">S/ 0.00</span></div>
                        <div class="imp-linea icbper-line" id="imp_icbper_row" style="display:none;"><span class="imp-lbl">ICBPER (bolsas)</span><span class="imp-val" id="imp_icbper">S/ 0.00</span></div>
                        <div class="imp-linea total-imp"><span class="imp-lbl">TOTAL</span><span class="imp-val" id="imp_total">S/ 0.00</span></div>
                    </div>
                </div>

                <!-- Tabs pago -->
                <ul class="nav nav-pago mb-3">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pago-directo" type="button"><i class="fas fa-money-bill-wave me-1"></i>Pago Directo</button></li>
                    <li class="nav-item ms-2"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pago-credito" type="button"><i class="fas fa-credit-card me-1"></i>Pago Crédito</button></li>
                </ul>
                <div class="tab-content">

                    <!-- PAGO DIRECTO -->
                    <div class="tab-pane fade show active" id="pago-directo">
                        <form id="form-pago-directo">
                            <div class="p-3 mb-3" style="background:var(--bg-card);border-radius:12px;">
                                <label class="fw-bold mb-2 d-block" style="font-size:.83rem;color:var(--primary);">Tipo de Comprobante</label>
                                <div class="btn-group">
                                    <input type="radio" class="btn-check" name="icon-input" id="boleta" value="boleta" checked>
                                    <label class="btn btn-outline-primary" for="boleta" style="border-radius:10px 0 0 10px;font-weight:700;">Boleta</label>
                                    <input type="radio" class="btn-check" name="icon-input" id="factura" value="factura">
                                    <label class="btn btn-outline-primary" for="factura" style="border-radius:0 10px 10px 0;font-weight:700;">Factura</label>
                                </div>
                                <div id="facturaInputs" style="display:none;" class="mt-3">
                                    <div class="row g-2">
                                        <div class="col-md-6"><input type="text" id="idRucReceptor" class="form-control" placeholder="RUC"></div>
                                        <div class="col-md-6"><input type="text" id="idRazonSocialReceptor" class="form-control" placeholder="Razón Social"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <button id="btnAgregarPago" class="btn btn-sm btn-outline-secondary rounded-pill" type="button"><i class="fas fa-plus me-1"></i>Agregar forma de pago</button>
                            </div>
                            <div class="pago-row">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <select class="form-control" name="formaPago" id="formaPagoSelect" onchange="detectarEfectivo()">
                                            <?php foreach (listarFormaPago() as $fp): ?><option value="<?php echo $fp["id"] ?>"><?php echo $fp["nombre"] ?></option><?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="number" placeholder="Monto S/" min="0" name="monto" id="montoSelect_0" class="form-control" step="0.01" oninput="detectarEfectivo()">
                                    </div>
                                </div>
                            </div>
                            <div id="contenedorPagos" class="mt-2"></div>

                            <!-- SECCIÓN VUELTO -->
                            <div id="seccionVuelto" style="display:none;">
                                <h6 class="fw-bold mb-3" style="color:var(--success);"><i class="fas fa-calculator me-1"></i>Cálculo de Vuelto</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="fw-bold" style="font-size:.82rem;color:var(--primary);">Total a Pagar</label>
                                        <div class="input-group mt-1"><span class="input-group-text" style="font-size:.82rem;">S/</span><input type="text" class="form-control fw-bold" id="totalAPagar" readonly style="background:#f8f9ff;"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="fw-bold" style="font-size:.82rem;color:var(--primary);">Paga con</label>
                                        <div class="input-group mt-1"><span class="input-group-text" style="font-size:.82rem;">S/</span><input type="number" class="form-control fw-bold" id="pagaCon" placeholder="0.00" step="0.01"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="fw-bold" style="font-size:.82rem;color:var(--success);">Vuelto</label>
                                        <div class="input-group mt-1"><span class="input-group-text" style="font-size:.82rem;">S/</span><input type="text" class="form-control fw-bold text-success" id="vuelto" readonly value="0.00"></div>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <small class="text-muted d-block mb-2">Pago rápido:</small>
                                    <div class="pago-rapido-btns">
                                        <button type="button" class="pago-rapido-btn" onclick="setPagaCon(10)">S/ 10</button>
                                        <button type="button" class="pago-rapido-btn" onclick="setPagaCon(20)">S/ 20</button>
                                        <button type="button" class="pago-rapido-btn" onclick="setPagaCon(50)">S/ 50</button>
                                        <button type="button" class="pago-rapido-btn" onclick="setPagaCon(100)">S/ 100</button>
                                        <button type="button" class="pago-rapido-btn" onclick="setPagaCon(200)">S/ 200</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <button class="btn-success-custom" onclick="fn_pagar_directo()"><i class="fas fa-hand-holding-usd me-2"></i>Confirmar Pago</button>
                        </div>
                    </div>

                    <!-- PAGO CRÉDITO -->
                    <div class="tab-pane fade" id="pago-credito">
                        <div class="p-3 mb-3" style="background:#e8f5e9;border-left:4px solid var(--success);border-radius:10px;font-size:.87rem;">
                            <i class="fas fa-info-circle me-1" style="color:var(--success);"></i>Si el cliente deja un pago parcial, regístralo. Si no, deja en blanco.
                        </div>
                        <form id="form-pago-credito">
                            <div class="mb-2">
                                <button id="btnAgregarPagoCredito" class="btn btn-sm btn-outline-secondary rounded-pill" type="button"><i class="fas fa-plus me-1"></i>Agregar pago parcial</button>
                            </div>
                            <div class="pago-row">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <select class="form-control" name="formaPagoCredito[]" id="formaPagoCreditoSelect_0">
                                            <?php foreach (listarFormaPago() as $fp): ?><option value="<?php echo $fp["id"] ?>"><?php echo $fp["nombre"] ?></option><?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="number" placeholder="Monto S/" min="0" name="montoCredito[]" id="montoSelectCredito_0" class="form-control" step="0.01">
                                    </div>
                                </div>
                            </div>
                            <div id="contenedorPagosCredito" class="mt-2"></div>
                        </form>
                        <div class="text-center mt-3">
                            <button class="btn-primary-custom" onclick="fn_pagar_credito()"><i class="fas fa-hands-helping me-2"></i>Registrar Pago a Crédito</button>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--border-soft);">
                <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Cerrar</button>
            </div>
        </div>
    </div>
</div>


<!-- ======= SCRIPTS ======= -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/scriptNotify.js"></script>

<script>
    /* ================================================================
   PRODUCTOS (para búsqueda en panel agregar)
================================================================ */
    const products = <?php echo json_encode(listarProductosVenta1($sucursal_id)); ?>;

    /* ================================================================
       DATATABLE
    ================================================================ */
    $(document).ready(function() {
        const dtLang = {
            sProcessing: "Procesando...",
            sLengthMenu: "Mostrar _MENU_ registros",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Sin datos",
            sInfo: "Mostrando _START_ a _END_ de _TOTAL_",
            sInfoEmpty: "0 registros",
            sSearch: "Buscar:",
            sLoadingRecords: "Cargando...",
            oPaginate: {
                sFirst: "Primero",
                sPrevious: "Anterior",
                sNext: "Siguiente",
                sLast: "Último"
            }
        };
        $("#multi-filter-select").DataTable({
            pageLength: 8,
            language: dtLang,
            initComplete: function() {
                this.api().columns().every(function() {
                    const col = this;
                    $('<select class="form-select"><option value=""></option></select>')
                        .appendTo($(col.footer()).empty())
                        .on("change", function() {
                            const v = $.fn.dataTable.util.escapeRegex($(this).val());
                            col.search(v ? "^" + v + "$" : "", true, false).draw();
                        });
                    col.data().unique().sort().each(function(d) {
                        $(col.footer()).find("select").append('<option value="' + d + '">' + d + "</option>");
                    });
                });
            }
        });

        // Comprobante toggle
        document.getElementById('boleta').addEventListener('change', toggleFacturaInputs);
        document.getElementById('factura').addEventListener('change', toggleFacturaInputs);
        toggleFacturaInputs();

        // Init todo
        populateFilters();
        initSoloCorteModal();
        initImpresion3DModal();
        initFormasPago();
        initVueltoListeners();

        // Click fuera cierra dropdown
        document.addEventListener('click', e => {
            const box = document.getElementById('resultadosProductos');
            const inp = document.getElementById('searchInput');
            if (box && !box.contains(e.target) && inp && !inp.contains(e.target)) {
                box.style.display = 'none';
            }
        });
    });

    function toggleFacturaInputs() {
        document.getElementById('facturaInputs').style.display = document.getElementById('factura').checked ? 'block' : 'none';
    }

    /* ================================================================
       FILTROS Y BÚSQUEDA DROPDOWN (estilo venta rápida)
    ================================================================ */
    function populateFilters() {
        const cats = [...new Set(products.map(p => p.categoria))];
        const tipos = [...new Set(products.map(p => p.tipo))];
        const dims = [...new Set(products.map(p => p.dimension))];
        const cols = [...new Set(products.map(p => p.color))];
        cats.forEach(v => document.getElementById('filterCategoria').innerHTML += `<option value="${v}">${v}</option>`);
        tipos.forEach(v => document.getElementById('filterTipo').innerHTML += `<option value="${v}">${v}</option>`);
        dims.forEach(v => document.getElementById('filterDimension').innerHTML += `<option value="${v}">${v}</option>`);
        cols.forEach(v => document.getElementById('filterColor').innerHTML += `<option value="${v}">${v}</option>`);
    }

    function filterProducts() {
        const q = (document.getElementById('searchInput').value || '').trim().toLowerCase();
        const cat = document.getElementById('filterCategoria').value;
        const tip = document.getElementById('filterTipo').value;
        const dim = document.getElementById('filterDimension').value;
        const col = document.getElementById('filterColor').value;
        const hint = document.getElementById('searchHint');
        const box = document.getElementById('resultadosProductos');

        if (!q && !cat && !tip && !dim && !col) {
            box.style.display = 'none';
            if (hint) hint.style.display = 'block';
            return;
        }
        if (hint) hint.style.display = 'none';

        const filtered = products.filter(p =>
            (!cat || p.categoria === cat) &&
            (!tip || p.tipo === tip) &&
            (!dim || p.dimension === dim) &&
            (!col || p.color === col) &&
            (!q || p.articulo.toLowerCase().includes(q) || (p.categoria || '').toLowerCase().includes(q) || (p.tipo || '').toLowerCase().includes(q))
        );

        box.innerHTML = '';
        if (!filtered.length) {
            box.innerHTML = '<div class="producto-item"><span class="prod-info">Sin resultados</span></div>';
            box.style.display = 'block';
            return;
        }

        filtered.slice(0, 15).forEach(p => {
            const sinStock = parseFloat(p.stock) === 0;
            const div = document.createElement('div');
            div.className = 'producto-item';
            div.innerHTML = `<div>
            <div class="prod-nombre">${p.articulo}${sinStock ? '<span class="sin-stock-badge ms-1">Sin stock</span>' : ''}</div>
            <div class="prod-info">${p.categoria||''} ${p.tipo ? '· '+p.tipo : ''} · Stock: ${p.stock}</div>
        </div>
        <div class="prod-precio-badge">S/ ${parseFloat(p.precio_venta).toFixed(2)}</div>`;
            if (!sinStock) {
                div.addEventListener('click', () => fn_agregar_venta(p));
            } else {
                div.style.opacity = '.5';
                div.style.cursor = 'default';
            }
            box.appendChild(div);
        });
        box.style.display = 'block';
    }

    function clearFilters() {
        ['filterCategoria', 'filterTipo', 'filterDimension', 'filterColor'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('searchInput').value = '';
        document.getElementById('resultadosProductos').style.display = 'none';
        const hint = document.getElementById('searchHint');
        if (hint) hint.style.display = 'block';
    }

    /* ================================================================
       TABS BUSCAR / MANUAL
    ================================================================ */
    function switchAddMode(mode) {
        document.querySelectorAll('.add-mode-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.add-panel').forEach(p => p.classList.remove('active'));
        if (mode === 'buscar') {
            document.getElementById('tabBuscar').classList.add('active');
            document.getElementById('panelBuscar').classList.add('active');
            setTimeout(() => document.getElementById('searchInput').focus(), 50);
        } else {
            document.getElementById('tabManual').classList.add('active');
            document.getElementById('panelManual').classList.add('active');
            setTimeout(() => document.getElementById('manualDescripcion').focus(), 50);
        }
    }

    /* ================================================================
       AGREGAR MANUAL
    ================================================================ */
    function agregarManual() {
        const desc = document.getElementById('manualDescripcion').value.trim();
        const cant = parseInt(document.getElementById('manualCantidad').value) || 1;
        const precio = parseFloat(document.getElementById('manualPrecio').value) || 0;
        if (!desc) {
            Swal.fire({
                icon: 'warning',
                title: 'Falta descripción',
                text: 'Ingresa el nombre del artículo.',
                confirmButtonText: 'Ok'
            });
            return;
        }
        const vid = document.getElementById('idVentaReserva').textContent;
        const datos = {
            id: '0',
            articulo: desc,
            cantidad: cant,
            precio_venta: precio,
            id_movimiento: 1,
            minutos: null,
            costo_por_minuto: null
        };
        fn_adicionar_articulo(vid, datos).then(() => {
            document.getElementById('manualDescripcion').value = '';
            document.getElementById('manualCantidad').value = 1;
            document.getElementById('manualPrecio').value = '';
            fn_consultarVenta([{
                venta_id: vid
            }]);
        }).catch(e => console.error(e));
    }

    /* ================================================================
       SOLO CORTE
    ================================================================ */
    function initSoloCorteModal() {
        document.getElementById('btnAbrirModalSolo').addEventListener('click', e => {
            e.preventDefault();
            const c = document.getElementById('contenido_solo_corte');
            c.innerHTML = `
        <div class="text-center mb-4">
          <h6 class="fw-bold mb-3" style="color:var(--primary);"><i class="fas fa-cut me-1"></i>Minutos de Corte</h6>
          <div class="qty-control mx-auto" style="width:fit-content;">
            <button class="qty-btn qty-btn-minus" id="btnRestarSoloCorte">−</button>
            <input id="cantidad_solocorte" type="number" value="0">
            <button class="qty-btn qty-btn-plus" id="btnSumarSoloCorte">+</button>
          </div>
        </div>
        <hr style="border-color:var(--border-soft);">
        <div class="text-center mb-3">
          <h6 class="fw-bold mb-2" style="color:var(--primary);">Precio Corte (S/)</h6>
          <input id="precioSoloCorte" type="number" class="form-control text-center mx-auto fw-bold" value="1.5" style="width:140px;font-size:1.1rem;">
          <div class="price-pills mt-2">
            <button class="price-pill" id="btnInc05SC">+0.5</button>
            <button class="price-pill" id="btnInc1SC">+1</button>
            <button class="price-pill" id="btnInc2SC">+2</button>
            <button class="price-pill" id="btnInc5SC">+5</button>
          </div>
        </div>
        <div class="text-center mt-3 mb-2">
          <button class="btn-success-custom" id="btnAgregarSoloCorte"><i class="fas fa-plus me-1"></i>Agregar</button>
        </div>
        <div class="text-center mt-2">
          <button class="btn btn-outline-secondary rounded-pill btn-sm" data-bs-dismiss="modal">Cerrar</button>
        </div>`;

            new bootstrap.Modal(document.getElementById('modalSoloCorte'), {
                backdrop: 'static'
            }).show();

            document.getElementById('btnSumarSoloCorte').onclick = () => {
                let v = parseInt(document.getElementById('cantidad_solocorte').value);
                document.getElementById('cantidad_solocorte').value = v === 0 ? 10 : v + 1;
            };
            document.getElementById('btnRestarSoloCorte').onclick = () => {
                let v = parseInt(document.getElementById('cantidad_solocorte').value);
                if (v > 0) document.getElementById('cantidad_solocorte').value = v - 1;
            };
            document.getElementById('btnInc05SC').onclick = () => {
                let p = parseFloat(document.getElementById('precioSoloCorte').value);
                document.getElementById('precioSoloCorte').value = (p + 0.5).toFixed(2);
            };
            document.getElementById('btnInc1SC').onclick = () => {
                let p = parseFloat(document.getElementById('precioSoloCorte').value);
                document.getElementById('precioSoloCorte').value = (p + 1).toFixed(2);
            };
            document.getElementById('btnInc2SC').onclick = () => {
                let p = parseFloat(document.getElementById('precioSoloCorte').value);
                document.getElementById('precioSoloCorte').value = (p + 2).toFixed(2);
            };
            document.getElementById('btnInc5SC').onclick = () => {
                let p = parseFloat(document.getElementById('precioSoloCorte').value);
                document.getElementById('precioSoloCorte').value = (p + 5).toFixed(2);
            };
            document.getElementById('btnAgregarSoloCorte').addEventListener('click', agregarDatosCorte);
        });
    }

    async function agregarDatosCorte() {
        const min = parseInt(document.getElementById('cantidad_solocorte').value) || 0;
        const tar = parseFloat(document.getElementById('precioSoloCorte').value) || 0;
        if (min <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Minutos inválidos',
                text: 'Ingresa minutos mayores a 0',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }
        const d = {
            id: '0',
            minutos: min,
            costo_por_minuto: tar,
            costo: min * tar,
            articulo: 'CORTE MATERIAL',
            id_movimiento: 6,
            precio_venta: null,
            cantidad: null
        };
        const vid = document.getElementById('idVentaReserva').textContent;
        try {
            await fn_adicionar_articulo(vid, d);
            document.getElementById('cantidad_solocorte').value = 0;
            document.getElementById('precioSoloCorte').value = 1.5;
            bootstrap.Modal.getInstance(document.getElementById('modalSoloCorte')).hide();
            fn_consultarVenta([{
                venta_id: vid
            }]);
        } catch (e) {
            console.error(e);
        }
    }

    /* ================================================================
       IMPRESIÓN 3D
    ================================================================ */
    function initImpresion3DModal() {
        document.getElementById('btnAbrirModalSolov2').addEventListener('click', e => {
            e.preventDefault();
            document.getElementById('cantidad_solocortev2').value = 10;
            document.getElementById('precioSoloCortev2').value = 1.5;
            if (document.getElementById('nota_impresion3d')) document.getElementById('nota_impresion3d').value = '';
            const btn = document.getElementById('btn_agregar_solocortev2');
            btn.innerHTML = '<i class="fas fa-plus me-1"></i>Agregar';
            btn.replaceWith(btn.cloneNode(true));
            document.getElementById('btn_agregar_solocortev2').addEventListener('click', fn_agregar_impresion_a_tabla);
            new bootstrap.Modal(document.getElementById('modalSoloCorteMaquina2'), {
                backdrop: 'static'
            }).show();
        });
    }

    async function fn_agregar_impresion_a_tabla() {
        const min = parseInt(document.getElementById('cantidad_solocortev2').value) || 0;
        const tar = parseFloat(document.getElementById('precioSoloCortev2').value) || 0;
        if (min <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Minutos inválidos',
                text: 'Ingresa minutos mayores a 0',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }
        const d = {
            id: '0',
            minutos: min,
            costo_por_minuto: tar,
            costo: min * tar,
            articulo: 'MAQUINA DE IMPRESION 3D',
            id_movimiento: 15,
            precio_venta: null,
            cantidad: null
        };
        const vid = document.getElementById('idVentaReserva').textContent;
        try {
            await fn_adicionar_articulo(vid, d);
            document.getElementById('cantidad_solocortev2').value = 10;
            document.getElementById('precioSoloCortev2').value = 1.5;
            bootstrap.Modal.getInstance(document.getElementById('modalSoloCorteMaquina2')).hide();
            fn_consultarVenta([{
                venta_id: vid
            }]);
            showNotification && showNotification('success');
        } catch (e) {
            console.error(e);
        }
    }

    function fnAumentoOrResta(a) {
        let v = parseFloat(document.getElementById('cantidad_solocortev2').value);
        document.getElementById('cantidad_solocortev2').value = a === '+' ? v + 1 : (v > 1 ? v - 1 : v);
    }

    function fnAumentarMin(m) {
        document.getElementById('cantidad_solocortev2').value = m;
    }

    function fnAumentaPrecioImpresion(m) {
        let p = parseFloat(document.getElementById('precioSoloCortev2').value);
        document.getElementById('precioSoloCortev2').value = (p + m).toFixed(2);
    }

    function limpiar() {
        document.getElementById('precioSoloCortev2').value = 0;
    }

    /* ================================================================
       AGREGAR ARTÍCULO DESDE BÚSQUEDA (NUEVO - estilo venta rápida)
    ================================================================ */
    function fn_agregar_venta(articulo) {
        const modal = new bootstrap.Modal(document.getElementById('modalCantidad'));
        document.getElementById('contenid_cantidad').innerHTML = `
    <div class="p-2">
      <h6 class="text-center fw-bold mb-3" style="color:var(--primary);background:var(--bg-card);padding:10px;border-radius:10px;">${articulo.articulo}</h6>
      <div class="text-center mb-3">
        <label class="fw-bold mb-2 d-block" style="font-size:.83rem;">Cantidad</label>
        <div class="qty-control mx-auto" style="width:fit-content;">
          <button class="qty-btn qty-btn-minus" id="btnRCAG">−</button>
          <input id="inpCAG" type="number" value="1">
          <button class="qty-btn qty-btn-plus" id="btnSCAG">+</button>
        </div>
      </div>
      <div id="secCAG" style="display:${articulo.corte ? 'block' : 'none'};">
        <hr style="border-color:var(--border-soft);">
        <div class="text-center mb-2">
          <label class="fw-bold" style="font-size:.83rem;">Min. Corte</label>
          <div class="qty-control mx-auto mt-2" style="width:fit-content;">
            <button class="qty-btn qty-btn-minus" id="btnRMAG">−</button>
            <input id="inpMAG" type="number" value="0">
            <button class="qty-btn qty-btn-plus" id="btnSMAG">+</button>
          </div>
        </div>
        <div class="text-center">
          <label class="fw-bold" style="font-size:.83rem;">Precio Corte</label>
          <input id="inpPAG" type="number" class="form-control text-center mx-auto fw-bold mt-1" value="1.5" style="width:120px;">
          <div class="price-pills mt-2">
            <button class="price-pill" onclick="document.getElementById('inpPAG').value=(parseFloat(document.getElementById('inpPAG').value)+0.5).toFixed(2)">+0.5</button>
            <button class="price-pill" onclick="document.getElementById('inpPAG').value=(parseFloat(document.getElementById('inpPAG').value)+1).toFixed(2)">+1</button>
            <button class="price-pill" onclick="document.getElementById('inpPAG').value=(parseFloat(document.getElementById('inpPAG').value)+2).toFixed(2)">+2</button>
            <button class="price-pill" onclick="document.getElementById('inpPAG').value=(parseFloat(document.getElementById('inpPAG').value)+5).toFixed(2)">+5</button>
          </div>
        </div>
      </div>
      <div class="text-center mt-3">
        <button class="btn-success-custom" id="btnConfAG"><i class="fas fa-check me-1"></i>Confirmar</button>
      </div>
    </div>`;
        modal.show();

        document.getElementById('btnRCAG').onclick = () => {
            let v = parseInt(document.getElementById('inpCAG').value);
            if (v > 1) {
                document.getElementById('inpCAG').value = v - 1;
                if (v - 1 === 1 && articulo.corte) document.getElementById('secCAG').style.display = 'block';
                else if (v - 1 > 1) document.getElementById('secCAG').style.display = 'none';
            }
        };
        document.getElementById('btnSCAG').onclick = () => {
            let c = parseInt(document.getElementById('inpCAG').value);
            if (c + 1 > articulo.stock) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stock insuficiente',
                    text: `Máx ${articulo.stock}`,
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
            document.getElementById('inpCAG').value = c + 1;
            if (c + 1 > 1) document.getElementById('secCAG').style.display = 'none';
        };
        document.getElementById('btnRMAG').onclick = () => {
            let v = parseInt(document.getElementById('inpMAG').value);
            if (v > 0) document.getElementById('inpMAG').value = v - 1;
        };
        document.getElementById('btnSMAG').onclick = () => {
            let v = parseInt(document.getElementById('inpMAG').value);
            document.getElementById('inpMAG').value = v === 0 ? 10 : v + 1;
        };

        document.getElementById('btnConfAG').addEventListener('click', async () => {
            const cant = parseInt(document.getElementById('inpCAG').value);
            const min = parseInt(document.getElementById('inpMAG').value) || 0;
            const pCorte = parseFloat(document.getElementById('inpPAG').value) || 0;
            if (cant > articulo.stock) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stock insuficiente',
                    text: `Máx ${articulo.stock}`,
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
            if (articulo.corte && cant === 1 && min <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Faltan minutos',
                    text: 'Ingresa minutos de corte',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
            articulo.cantidad = cant;
            articulo.minutos = min || null;
            articulo.costo_por_minuto = pCorte || null;
            articulo.id_movimiento = 1;
            const vid = document.getElementById('idVentaReserva').textContent;
            try {
                await fn_adicionar_articulo(vid, articulo);
                modal.hide();
                document.getElementById('resultadosProductos').style.display = 'none';
                document.getElementById('searchInput').value = '';
                const hint = document.getElementById('searchHint');
                if (hint) hint.style.display = 'block';
                fn_consultarVenta([{
                    venta_id: vid
                }]);
            } catch (e) {
                console.error(e);
            }
        });
    }

    /* ================================================================
       SERVICIOS GENÉRICOS
    ================================================================ */
    function fn_servicios(jsDatos) {
        const medidas = jsDatos.medidas.slice(1, -1).split(',');
        document.getElementById('modalContent').innerHTML = `
    <div class="modal-header modal-header-gradient"><h5 class="modal-title fw-bold"><i class="fas fa-cube me-2"></i>${jsDatos.descripcion}</h5></div>
    <div class="p-4">
      <div class="text-center mb-3">
        <label class="fw-bold mb-2 d-block" style="font-size:.85rem;color:var(--primary);">Cantidad</label>
        <div class="qty-control mx-auto" style="width:fit-content;">
          <button class="qty-btn qty-btn-minus" onclick="ajustarCantidadSv('${jsDatos.descripcion}',-1)">−</button>
          <input type="number" id="cant_${jsDatos.descripcion}" value="1">
          <button class="qty-btn qty-btn-plus" onclick="ajustarCantidadSv('${jsDatos.descripcion}',1)">+</button>
        </div>
      </div>
      <div class="mb-3">
        <label class="fw-bold mb-2 d-block" style="font-size:.85rem;color:var(--primary);">Dimensión</label>
        <div id="dims_sv_${jsDatos.descripcion}" class="d-flex flex-wrap gap-2 justify-content-center">
          ${medidas.map(m=>`<div class="form-check"><input class="form-check-input" type="checkbox" value="${m}" id="dim_sv_${m}"><label class="form-check-label" for="dim_sv_${m}" style="font-size:.88rem;">${m}</label></div>`).join('')}
        </div>
      </div>
      <div class="mb-3"><label class="fw-bold mb-1" style="font-size:.85rem;color:var(--primary);">Monto (S/)</label><input type="number" id="monto_${jsDatos.descripcion}" class="form-control mt-1" placeholder="0.00"></div>
      <div class="mb-3"><label class="fw-bold mb-1" style="font-size:.85rem;color:var(--primary);">Detalle</label><textarea id="det_${jsDatos.descripcion}" class="form-control mt-1" rows="2" style="font-size:.87rem;"></textarea></div>
      <div class="text-center"><button class="btn-success-custom" onclick="agregarServicioSv('${jsDatos.descripcion}',${jsDatos.id})"><i class="fas fa-plus me-1"></i>Agregar</button></div>
    </div>`;
        new bootstrap.Modal(document.getElementById('modalGenerico')).show();
    }

    function ajustarCantidadSv(s, inc) {
        const i = document.getElementById(`cant_${s}`);
        i.value = Math.max(1, parseInt(i.value) + inc);
    }

    function agregarServicioSv(nombre, idMov) {
        const monto = parseFloat(document.getElementById(`monto_${nombre}`).value) || 0;
        const det = document.getElementById(`det_${nombre}`).value;
        const dims = Array.from(document.querySelectorAll(`#dims_sv_${nombre} input:checked`)).map(c => c.value).join(', ');
        if (monto <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Error',
                text: 'Monto inválido',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }
        const vid = document.getElementById('idVentaReserva').textContent;
        fn_insert_movimiento(vid, idMov, parseInt(document.getElementById(`cant_${nombre}`).value), (dims || 'Sin nota') + (det ? ' / ' + det : ''), monto);
        bootstrap.Modal.getInstance(document.getElementById('modalGenerico')).hide();
        showNotification && showNotification('success');
    }

    /* ================================================================
       CONSULTAR VENTA
    ================================================================ */
    let datosDeVenta = [];

    function fn_consultarVenta(datosArticulo) {
        const tbody = document.getElementById('tabla_articulos').getElementsByTagName('tbody')[0];
        tbody.innerHTML = '';
        document.getElementById('panelDetalles').style.display = 'block';
        document.getElementById('panelAdicionarMas').style.display = 'block';
        document.getElementById('panelDetalles').classList.add('fade-in-panel');
        const venta_id = Array.isArray(datosArticulo) ? datosArticulo[0].venta_id : datosArticulo.venta_id;
        $.ajax({
            method: 'POST',
            url: 'logica/clssVentaCorte.php',
            data: {
                accion: 'CONSULTARRESERVA',
                venta_id
            }
        }).done(function(text) {
            const Data = JSON.parse(text);
            if (Array.isArray(datosArticulo) && datosArticulo.length > 0) {
                llenarDatosModal(datosDeVenta['venta_id'], datosDeVenta['id_persona'], datosDeVenta['cliente'], datosDeVenta['usuario_id'], datosDeVenta['telefonomovil_cliente'], datosDeVenta['email_cliente']);
                llenarDatosPanelCliente(datosDeVenta['venta_id'], datosDeVenta['cliente'], datosDeVenta['fecha'], datosDeVenta['hora'], datosDeVenta['usuario'], datosDeVenta['telefonomovil_cliente'], datosDeVenta['email_cliente'], datosDeVenta['numero_doc_cliente']);
            } else {
                datosDeVenta = datosArticulo;
                llenarDatosModal(datosArticulo['venta_id'], datosArticulo['id_persona'], datosArticulo['cliente'], datosArticulo['usuario_id'], datosArticulo['telefonomovil_cliente'], datosArticulo['email_cliente']);
                llenarDatosPanelCliente(datosArticulo['venta_id'], datosArticulo['cliente'], datosArticulo['fecha'], datosArticulo['hora'], datosArticulo['usuario'], datosArticulo['telefonomovil_cliente'], datosArticulo['email_cliente'], datosArticulo['numero_doc_cliente']);
            }
            Data.forEach(item => fn_agregar_articulo_tabla(item));
            setTimeout(() => document.getElementById('panelDetalles').scrollIntoView({
                behavior: 'smooth'
            }), 200);
        });
    }

    function llenarDatosModal(idVenta, idPersona, nombreCliente, idUsuario, numCel, email) {
        document.getElementById('idUsuario').textContent = idUsuario;
        document.getElementById('idVenta').textContent = idVenta;
        document.getElementById('idPersona').textContent = idPersona;
        document.getElementById('nombreCliente').value = nombreCliente;
        document.getElementById('idUpdateNumTelefonoCliente').value = numCel;
        document.getElementById('idUpdateCorreoCliente').value = email;
    }

    function llenarDatosPanelCliente(idVenta, cliente, fecha, hora, usuario, tel, email, numDoc) {
        document.getElementById('idVentaReserva').textContent = idVenta;
        document.getElementById('idClienteReservaDetalle').textContent = cliente;
        document.getElementById('idNumCelClienteReserva').textContent = tel || '—';
        document.getElementById('idCorreoClienteReserva').textContent = email || '—';
        document.getElementById('idNumDocClienteReserva').textContent = numDoc || '—';
        document.getElementById('idFechaReservaDetalle').value = fecha;
        document.getElementById('idHoraReservaDetalle').value = hora;
        document.getElementById('idUsuarioReservaDetalle').textContent = usuario;
    }

    /* ================================================================
       TABLA ARTÍCULOS
    ================================================================ */
    function fn_obtener_total() {
        const filas = document.getElementById('tabla_articulos').getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        let tCorte = 0,
            tArt = 0,
            total = 0;
        for (let i = 0; i < filas.length; i++) {
            const c = filas[i].cells;
            tCorte += parseFloat(c[3].textContent) || 0;
            tArt += (parseFloat(c[5].textContent) * parseFloat(c[6].textContent)) || 0;
            total += parseFloat(c[7].textContent) || 0;
        }
        document.getElementById('id_subtotal_cortes').textContent = tCorte.toFixed(2);
        document.getElementById('id_subtotal_articulos').textContent = tArt.toFixed(2);
        document.getElementById('id_subtotal_general').textContent = total.toFixed(2);
    }

    function fn_agregar_articulo_tabla(d) {
        const tbody = document.getElementById('tabla_articulos').getElementsByTagName('tbody')[0];
        const fila = tbody.insertRow();
        fila.insertCell(0).textContent = d.articulo_id;
        fila.insertCell(1).textContent = d.minutos || '-';
        fila.insertCell(2).textContent = d.costo_por_minuto || '-';
        fila.insertCell(3).textContent = d.costo_por_minuto * d.minutos || '-';
        fila.insertCell(4).textContent = d.articulo_nombre;
        fila.insertCell(5).textContent = d.cantidad || '-';
        fila.insertCell(6).textContent = d.precio_unitario_articulo || '-';
        fila.insertCell(7).textContent = parseFloat(d.sub_total).toFixed(2);
        const acc = fila.insertCell(8);
        fila.insertCell(9).textContent = d.movimiento_id;
        fila.insertCell(10).textContent = d.rel_venta_articulo_id;
        fila.insertCell(11).textContent = d.nota_archivo;

        // Botón Editar
        const btnEd = document.createElement('button');
        btnEd.className = 'btn btn-warning btn-sm rounded-pill me-1 text-white';
        btnEd.innerHTML = '<i class="fas fa-edit"></i>';
        acc.appendChild(btnEd);
        btnEd.addEventListener('click', () => fnEditarItem(d, fila));

        // Botón Eliminar
        const btnEl = document.createElement('button');
        btnEl.className = 'btn btn-danger btn-sm rounded-pill';
        btnEl.innerHTML = '<i class="fas fa-trash"></i>';
        acc.appendChild(btnEl);
        btnEl.addEventListener('click', async () => {
            const r = await Swal.fire({
                title: '¿Eliminar?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar'
            });
            if (r.isConfirmed) {
                const vid = document.getElementById('idVentaReserva').textContent;
                d.movimiento_id == 1 ? await fn_eliminar_articulo(d.rel_venta_articulo_id) : await fn_eliminar_movimiento(d.rel_venta_articulo_id);
                fn_consultarVenta([{
                    venta_id: vid
                }]);
            }
        });
        fn_obtener_total();
    }

    function fnEditarItem(d, fila) {
        const vid = document.getElementById('idVentaReserva').textContent;
        if (d.movimiento_id == 1) {
            const m = new bootstrap.Modal(document.getElementById('modalCantidad'));
            document.getElementById('contenid_cantidad').innerHTML = `
        <div class="p-2">
          <h6 class="text-center fw-bold mb-3" style="color:var(--primary);background:var(--bg-card);padding:10px;border-radius:10px;">${d.articulo_nombre}</h6>
          <div class="text-center mb-3">
            <label class="fw-bold mb-2 d-block" style="font-size:.83rem;">Cantidad</label>
            <div class="qty-control mx-auto" style="width:fit-content;">
              <button class="qty-btn qty-btn-minus" id="btnRCE">−</button>
              <input id="inpCE" type="number" value="${d.cantidad||1}">
              <button class="qty-btn qty-btn-plus" id="btnSCE">+</button>
            </div>
          </div>
          <div id="secCE" style="display:${d.corte&&parseInt(d.cantidad||1)===1?'block':'none'};">
            <hr style="border-color:var(--border-soft);">
            <div class="text-center mb-2"><label class="fw-bold" style="font-size:.83rem;">Min. Corte</label>
              <div class="qty-control mx-auto mt-2" style="width:fit-content;">
                <button class="qty-btn qty-btn-minus" id="btnRKE">−</button>
                <input id="inpME" type="number" value="${d.minutos||0}">
                <button class="qty-btn qty-btn-plus" id="btnSKE">+</button>
              </div>
            </div>
            <div class="text-center"><label class="fw-bold" style="font-size:.83rem;">Precio Corte</label>
              <input id="inpPE" type="number" class="form-control text-center mx-auto fw-bold mt-1" value="${d.costo_por_minuto||1.5}" style="width:120px;">
              <div class="price-pills mt-2">
                <button class="price-pill" onclick="document.getElementById('inpPE').value=(parseFloat(document.getElementById('inpPE').value)+0.5).toFixed(2)">+0.5</button>
                <button class="price-pill" onclick="document.getElementById('inpPE').value=(parseFloat(document.getElementById('inpPE').value)+1).toFixed(2)">+1</button>
                <button class="price-pill" onclick="document.getElementById('inpPE').value=(parseFloat(document.getElementById('inpPE').value)+2).toFixed(2)">+2</button>
                <button class="price-pill" onclick="document.getElementById('inpPE').value=(parseFloat(document.getElementById('inpPE').value)+5).toFixed(2)">+5</button>
              </div>
            </div>
          </div>
          <div class="text-center mt-3">
            <button class="btn-success-custom" id="btnConfEd"><i class="fas fa-check me-1"></i>Confirmar</button>
          </div>
        </div>`;
            m.show();
            document.getElementById('btnRCE').onclick = () => {
                let v = parseInt(document.getElementById('inpCE').value);
                if (v > 1) document.getElementById('inpCE').value = v - 1;
            };
            document.getElementById('btnSCE').onclick = () => {
                document.getElementById('inpCE').value = parseInt(document.getElementById('inpCE').value) + 1;
            };
            document.getElementById('btnRKE').onclick = () => {
                let v = parseInt(document.getElementById('inpME').value);
                if (v > 0) document.getElementById('inpME').value = v - 1;
            };
            document.getElementById('btnSKE').onclick = () => {
                let v = parseInt(document.getElementById('inpME').value);
                document.getElementById('inpME').value = v === 0 ? 10 : v + 1;
            };
            document.getElementById('btnConfEd').addEventListener('click', async () => {
                d.cantidad = parseInt(document.getElementById('inpCE').value);
                if (d.corte) {
                    d.minutos = parseInt(document.getElementById('inpME').value) || 0;
                    d.costo_por_minuto = parseFloat(document.getElementById('inpPE').value) || 0;
                }
                d.sub_total = (d.cantidad * d.precio_unitario_articulo) + ((d.minutos || 0) * (d.costo_por_minuto || 0));
                await fn_editar_articulo(d);
                fn_consultarVenta([{
                    venta_id: vid
                }]);
                m.hide();
            });
        } else if (d.movimiento_id == 15) {
            document.getElementById('cantidad_solocortev2').value = parseInt(d.minutos);
            document.getElementById('precioSoloCortev2').value = parseFloat(d.costo_por_minuto);
            const btn = document.getElementById('btn_agregar_solocortev2');
            btn.innerHTML = '<i class="fas fa-save me-1"></i>Actualizar';
            btn.replaceWith(btn.cloneNode(true));
            document.getElementById('btn_agregar_solocortev2').addEventListener('click', async () => {
                d.minutos = parseInt(document.getElementById('cantidad_solocortev2').value) || 0;
                d.costo_por_minuto = parseFloat(document.getElementById('precioSoloCortev2').value) || 0;
                d.sub_total = d.minutos * d.costo_por_minuto;
                await fn_editar_articulo(d);
                fn_consultarVenta([{
                    venta_id: vid
                }]);
                bootstrap.Modal.getInstance(document.getElementById('modalSoloCorteMaquina2')).hide();
            });
            new bootstrap.Modal(document.getElementById('modalSoloCorteMaquina2'), {
                backdrop: 'static'
            }).show();
        } else if (d.movimiento_id == 6) {
            const c = document.getElementById('contenido_solo_corte');
            c.innerHTML = `
        <div class="text-center mb-4">
          <h6 class="fw-bold mb-3" style="color:var(--primary);">Min. Corte</h6>
          <div class="qty-control mx-auto" style="width:fit-content;">
            <button class="qty-btn qty-btn-minus" id="btnRSCE">−</button>
            <input id="cantidad_solocorte" type="number" value="${d.minutos||0}">
            <button class="qty-btn qty-btn-plus" id="btnSSCE">+</button>
          </div>
        </div>
        <hr style="border-color:var(--border-soft);">
        <div class="text-center mb-3">
          <h6 class="fw-bold mb-2" style="color:var(--primary);">Precio Corte (S/)</h6>
          <input id="precioSoloCorte" type="number" class="form-control text-center mx-auto fw-bold" value="${d.costo_por_minuto||1.5}" style="width:140px;font-size:1.1rem;">
          <div class="price-pills mt-2">
            <button class="price-pill" onclick="document.getElementById('precioSoloCorte').value=(parseFloat(document.getElementById('precioSoloCorte').value)+0.5).toFixed(2)">+0.5</button>
            <button class="price-pill" onclick="document.getElementById('precioSoloCorte').value=(parseFloat(document.getElementById('precioSoloCorte').value)+1).toFixed(2)">+1</button>
            <button class="price-pill" onclick="document.getElementById('precioSoloCorte').value=(parseFloat(document.getElementById('precioSoloCorte').value)+2).toFixed(2)">+2</button>
            <button class="price-pill" onclick="document.getElementById('precioSoloCorte').value=(parseFloat(document.getElementById('precioSoloCorte').value)+5).toFixed(2)">+5</button>
          </div>
        </div>
        <div class="text-center mt-3 mb-2">
          <button class="btn-success-custom" id="btnActSC"><i class="fas fa-save me-1"></i>Actualizar</button>
        </div>
        <div class="text-center"><button class="btn btn-outline-secondary rounded-pill btn-sm" data-bs-dismiss="modal">Cerrar</button></div>`;
            const modal = new bootstrap.Modal(document.getElementById('modalSoloCorte'), {
                backdrop: 'static'
            });
            modal.show();
            document.getElementById('btnRSCE').onclick = () => {
                let v = parseInt(document.getElementById('cantidad_solocorte').value);
                if (v > 0) document.getElementById('cantidad_solocorte').value = v - 1;
            };
            document.getElementById('btnSSCE').onclick = () => {
                document.getElementById('cantidad_solocorte').value = parseInt(document.getElementById('cantidad_solocorte').value) + 1;
            };
            document.getElementById('btnActSC').addEventListener('click', async () => {
                d.minutos = parseInt(document.getElementById('cantidad_solocorte').value) || 0;
                d.costo_por_minuto = parseFloat(document.getElementById('precioSoloCorte').value) || 0;
                d.sub_total = d.minutos * d.costo_por_minuto;
                await fn_editar_articulo(d);
                fn_consultarVenta([{
                    venta_id: vid
                }]);
                modal.hide();
            });
        } else {
            document.getElementById('modalContent').innerHTML = `
        <div class="modal-header modal-header-gradient"><h5 class="modal-title fw-bold">${d.articulo_nombre}</h5></div>
        <div class="p-4">
          <div class="mb-3"><label class="fw-bold" style="font-size:.83rem;color:var(--primary);">Cantidad</label>
            <div class="qty-control mx-auto mt-2" style="width:fit-content;">
              <button class="qty-btn qty-btn-minus" onclick="let v=parseInt(document.getElementById('inpGE').value);if(v>1)document.getElementById('inpGE').value=v-1;">−</button>
              <input id="inpGE" type="number" value="${d.cantidad||1}">
              <button class="qty-btn qty-btn-plus" onclick="document.getElementById('inpGE').value=parseInt(document.getElementById('inpGE').value)+1;">+</button>
            </div>
          </div>
          <div class="mb-3"><label class="fw-bold" style="font-size:.83rem;color:var(--primary);">Monto (S/)</label>
            <input type="number" id="inpMontoGE" class="form-control mt-1" value="${d.sub_total}">
          </div>
          <div class="text-center"><button class="btn-success-custom" id="btnActGE"><i class="fas fa-save me-1"></i>Actualizar</button></div>
        </div>`;
            const mg = new bootstrap.Modal(document.getElementById('modalGenerico'));
            mg.show();
            document.getElementById('btnActGE').addEventListener('click', async () => {
                d.cantidad = parseInt(document.getElementById('inpGE').value);
                d.sub_total = parseFloat(document.getElementById('inpMontoGE').value);
                await fn_editar_movimiento(d);
                fn_consultarVenta([{
                    venta_id: vid
                }]);
                mg.hide();
            });
        }
    }

    /* ================================================================
       CÁLCULO DE IMPUESTOS
    ================================================================ */
    function calcularImpuestos() {
        const filas = Array.from(document.querySelectorAll('#tabla_articulos tbody tr'));
        let opGravadas = 0,
            montoIGV = 0,
            opExoneradas = 0,
            opInafectas = 0,
            montoICBPER = 0;

        filas.forEach(fila => {
            const subtotal = parseFloat(fila.cells[7].textContent) || 0;
            const impuesto = (fila.cells[9] ? fila.cells[9].textContent : 'IGV').toUpperCase().trim();
            const tasa = 0.18; // IGV estándar

            if (impuesto === 'EXONERADO') {
                opExoneradas += subtotal;
            } else if (impuesto === 'INAFECTO') {
                opInafectas += subtotal;
            } else if (impuesto === 'ICBPER') {
                const base = subtotal / 1.18;
                opGravadas += base;
                montoIGV += subtotal - base;
                montoICBPER += 0.50; // tarifa fija por unidad
            } else {
                // IGV o fallback
                const base = subtotal / (1 + tasa);
                opGravadas += base;
                montoIGV += subtotal - base;
            }
        });

        const total = opGravadas + montoIGV + opExoneradas + opInafectas + montoICBPER;
        return {
            gravadas: opGravadas,
            igv: montoIGV,
            exoneradas: opExoneradas,
            inafectas: opInafectas,
            icbper: montoICBPER,
            total
        };
    }

    function mostrarImpuestosEnModal() {
        const imp = calcularImpuestos();
        const fmt = (n) => `S/ ${n.toFixed(2)}`;
        const cls = (n) => n > 0 ? 'imp-val' : 'imp-val cero';

        document.getElementById('imp_gravadas').textContent = fmt(imp.gravadas);
        document.getElementById('imp_gravadas').className = cls(imp.gravadas);
        document.getElementById('imp_exoneradas').textContent = fmt(imp.exoneradas);
        document.getElementById('imp_exoneradas').className = cls(imp.exoneradas);
        document.getElementById('imp_inafectas').textContent = fmt(imp.inafectas);
        document.getElementById('imp_inafectas').className = cls(imp.inafectas);
        document.getElementById('imp_igv').textContent = fmt(imp.igv);
        document.getElementById('imp_total').textContent = fmt(imp.total);

        const icbperRow = document.getElementById('imp_icbper_row');
        if (imp.icbper > 0) {
            document.getElementById('imp_icbper').textContent = fmt(imp.icbper);
            icbperRow.style.display = 'flex';
        } else {
            icbperRow.style.display = 'none';
        }
    }

    /* ================================================================
       FORMAS DE PAGO
    ================================================================ */
    function initFormasPago() {
        let cnt = 1;
        document.getElementById('btnAgregarPago').addEventListener('click', () => {
            const d = document.createElement('div');
            d.className = 'pago-row mb-2';
            d.innerHTML = `<div class="row g-2">
            <div class="col-md-5"><select class="form-control" name="formaPago_${cnt}" onchange="detectarEfectivo()"><?php foreach (listarFormaPago() as $fp) echo '<option value="' . $fp["id"] . '">' . $fp["nombre"] . '</option>'; ?></select></div>
            <div class="col-md-5"><input type="number" class="form-control" placeholder="Monto S/" name="monto_${cnt}" id="montoSelect_${cnt}" step="0.01" oninput="detectarEfectivo()"></div>
            <div class="col-md-2"><button type="button" class="btn btn-sm btn-danger rounded-pill w-100" onclick="this.closest('.pago-row').remove();detectarEfectivo();"><i class="fas fa-times"></i></button></div>
        </div>`;
            document.getElementById('contenedorPagos').appendChild(d);
            cnt++;
        });

        let cntC = 1;
        document.getElementById('btnAgregarPagoCredito').addEventListener('click', () => {
            const d = document.createElement('div');
            d.className = 'pago-row mb-2';
            d.innerHTML = `<div class="row g-2">
            <div class="col-md-5"><select class="form-control" name="formaPagoCredito[]"><?php foreach (listarFormaPago() as $fp) echo '<option value="' . $fp["id"] . '">' . $fp["nombre"] . '</option>'; ?></select></div>
            <div class="col-md-5"><input type="number" class="form-control" placeholder="Monto S/" name="montoCredito[]" step="0.01"></div>
            <div class="col-md-2"><button type="button" class="btn btn-sm btn-danger rounded-pill w-100" onclick="this.closest('.pago-row').remove()"><i class="fas fa-times"></i></button></div>
        </div>`;
            document.getElementById('contenedorPagosCredito').appendChild(d);
            cntC++;
        });

        document.getElementById('btnRealizarPago').addEventListener('click', () => {
            document.getElementById('idRucReceptor').value = '';
            document.getElementById('idRazonSocialReceptor').value = '';
            const total = document.getElementById('id_subtotal_general').textContent;
            document.getElementById('montoTotal').value = total;
            document.getElementById('montoTotalFinal').value = '';
            document.getElementById('idMontoVentaTitulo').textContent = total;
            mostrarImpuestosEnModal();
            new bootstrap.Modal(document.getElementById('modalRealizarPago')).show();
        });
    }

    /* ================================================================
       VUELTO
    ================================================================ */
    function initVueltoListeners() {
        document.getElementById('formaPagoSelect').addEventListener('change', detectarEfectivo);
        document.getElementById('montoSelect_0').addEventListener('input', detectarEfectivo);
        document.getElementById('pagaCon').addEventListener('input', calcularVuelto);
        document.getElementById('montoTotalFinal').addEventListener('input', () => {
            if (document.getElementById('seccionVuelto').style.display !== 'none') actualizarTotalAPagar();
        });
    }

    function detectarEfectivo() {
        let hay = false;
        const sp = document.getElementById('formaPagoSelect');
        const mp = parseFloat(document.getElementById('montoSelect_0').value) || 0;
        if (mp > 0 && sp.options[sp.selectedIndex].text.toUpperCase().includes('EFECTIVO')) hay = true;
        document.querySelectorAll('#contenedorPagos .pago-row').forEach(row => {
            const sel = row.querySelector('select');
            const m = parseFloat(row.querySelector('input[type="number"]')?.value) || 0;
            if (m > 0 && sel && sel.options[sel.selectedIndex].text.toUpperCase().includes('EFECTIVO')) hay = true;
        });
        if (hay) {
            document.getElementById('seccionVuelto').style.display = 'block';
            actualizarTotalAPagar();
        } else {
            document.getElementById('seccionVuelto').style.display = 'none';
            document.getElementById('pagaCon').value = '';
            document.getElementById('vuelto').value = '0.00';
        }
    }

    function actualizarTotalAPagar() {
        const m = parseFloat(document.getElementById('montoTotalFinal').value) || parseFloat(document.getElementById('montoTotal').value);
        document.getElementById('totalAPagar').value = m.toFixed(2);
        calcularVuelto();
    }

    function calcularVuelto() {
        const t = parseFloat(document.getElementById('totalAPagar').value) || 0;
        const p = parseFloat(document.getElementById('pagaCon').value) || 0;
        const v = p - t;
        const el = document.getElementById('vuelto');
        if (v < 0) {
            el.value = '0.00';
            el.className = 'form-control fw-bold text-danger';
        } else {
            el.value = v.toFixed(2);
            el.className = 'form-control fw-bold text-success';
        }
    }

    function setPagaCon(m) {
        const t = parseFloat(document.getElementById('totalAPagar').value) || 0;
        document.getElementById('pagaCon').value = Math.max(m, t).toFixed(2);
        calcularVuelto();
    }

    /* ================================================================
       PAGAR DIRECTO
    ================================================================ */
    async function fn_pagar_directo() {
        const radios = document.querySelectorAll('input[name="icon-input"]');
        let radioSel;
        radios.forEach(r => {
            if (r.checked) radioSel = r.value;
        });
        const datos = $('#form-pago-directo').serializeArray();
        const idVenta = document.getElementById('idVenta').textContent;
        const idPersona = document.getElementById('idPersona').textContent;
        const idAtencionFinal = document.getElementById('idAtencionFinal').textContent;
        const numTel = document.getElementById('idUpdateNumTelefonoCliente').value;
        const montoOrig = parseFloat(document.getElementById('montoTotal').value);
        let montoFinal = parseFloat(document.getElementById('montoTotalFinal').value);
        if (isNaN(montoFinal)) montoFinal = montoOrig;

        // Vuelto check
        const sv = document.getElementById('seccionVuelto');
        if (sv.style.display !== 'none') {
            const pagaCon = parseFloat(document.getElementById('pagaCon').value) || 0;
            const vuelto = parseFloat(document.getElementById('vuelto').value) || 0;
            if (!pagaCon) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atención',
                    text: 'Ingresa el monto con que paga el cliente',
                    timer: 2000,
                    showConfirmButton: false
                });
                document.getElementById('pagaCon').focus();
                return;
            }
            if (vuelto < 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Monto insuficiente',
                    text: 'El cliente debe pagar un monto mayor o igual al total',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
            if (vuelto > 0) {
                const r = await Swal.fire({
                    title: 'Confirmar Vuelto',
                    html: `<p><b>Total:</b> S/ ${montoFinal.toFixed(2)}</p><p><b>Paga con:</b> S/ ${pagaCon.toFixed(2)}</p><p class="text-success fs-4"><b>Vuelto:</b> S/ ${vuelto.toFixed(2)}</p>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Confirmar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#11998e'
                });
                if (!r.isConfirmed) return;
            }
        }

        let jsPagos = [];
        let fp = null,
            m = null,
            acum = 0;
        datos.forEach(d => {
            if (d.name.startsWith('formaPago')) fp = d.value;
            if (d.name.startsWith('monto')) {
                m = parseFloat(d.value);
                acum += m;
            }
            if (fp && m) {
                jsPagos.push({
                    venta_id: idVenta,
                    id_forma_pago: fp,
                    monto_forma_pago: m
                });
                fp = null;
                m = null;
            }
        });
        if (radioSel === 'factura' && !document.getElementById('idRucReceptor').value.trim()) {
            Swal.fire({
                icon: 'error',
                title: 'Falta RUC',
                text: 'Ingresa RUC y Razón Social para factura'
            });
            return;
        }
        if (!jsPagos.length) {
            Swal.fire({
                icon: 'error',
                title: 'Sin montos',
                text: 'Agrega al menos una forma de pago'
            });
            return;
        }
        if (acum > montoFinal) {
            Swal.fire({
                icon: 'error',
                title: 'Montos incorrectos',
                text: 'Los montos superan el total'
            });
            return;
        }
        if (acum < montoFinal) {
            Swal.fire({
                icon: 'error',
                title: 'Montos incorrectos',
                text: 'Los montos son menores al total'
            });
            return;
        }

        $.ajax({
            url: 'logica/clssInsertPA.php',
            type: 'POST',
            data: {
                accion: 'FINALIZARVENTA',
                jsDatosVenta: JSON.stringify({
                    tipo_comprobante: radioSel,
                    js_detalles_receptor_factura: {
                        ruc: document.getElementById('idRucReceptor').value,
                        razon_social: document.getElementById('idRazonSocialReceptor').value
                    },
                    venta_id: idVenta,
                    atencion_final_usuario: idAtencionFinal,
                    numUpdateTelefonoPersona: numTel,
                    monto_original: montoOrig,
                    monto_venta_final: montoFinal,
                    js_detalle_pagos: jsPagos
                })
            },
            success: r => {
                try {
                    const res = JSON.parse(r);
                    if (res.estado === true) Swal.fire({
                        title: '¡Pagado!',
                        text: res.mensaje,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        fn_abrir_pdf(idVenta);
                    });
                    else Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.mensaje
                    });
                } catch (e) {}
            },
            error: () => Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error en la comunicación'
            })
        });
    }

    /* ================================================================
       PAGAR CRÉDITO
    ================================================================ */
    function fn_pagar_credito() {
        const idVenta = document.getElementById('idVenta').textContent;
        const idAtencionFinal = document.getElementById('idAtencionFinal').textContent;
        const numTel = document.getElementById('idUpdateNumTelefonoCliente').value;
        const montoOrig = parseFloat(document.getElementById('montoTotal').value);
        let montoFinal = parseFloat(document.getElementById('montoTotalFinal').value);
        if (isNaN(montoFinal)) montoFinal = montoOrig;
        const datos = $('#form-pago-credito').serializeArray();
        let jsDeuda = [];
        let fp = null,
            m = null,
            acum = 0;
        datos.forEach(d => {
            if (d.name.startsWith('formaPagoCredito')) fp = d.value;
            if (d.name.startsWith('montoCredito')) {
                m = parseFloat(d.value);
                acum += m;
            }
            if (fp && m) {
                jsDeuda.push({
                    venta_id: idVenta,
                    id_forma_pago: fp,
                    monto_forma_pago: m
                });
                fp = null;
                m = null;
            }
        });
        if (isNaN(acum)) acum = 0;
        if (!jsDeuda.length) jsDeuda = null;
        $.ajax({
            url: 'logica/clssInsertPA.php',
            type: 'POST',
            data: {
                accion: 'FINALIZARVENTACREDITO',
                jsDatosVenta: JSON.stringify({
                    venta_id: idVenta,
                    atencion_final_usuario: idAtencionFinal,
                    numUpdateTelefonoPersona: numTel,
                    monto_original: montoOrig,
                    monto_venta_final: montoFinal,
                    monto_inicial: acum,
                    js_detalle_deuda: jsDeuda
                })
            },
            success: r => {
                try {
                    const res = JSON.parse(r);
                    if (res.estado === true) Swal.fire({
                        title: '¡Crédito registrado!',
                        text: res.mensaje,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        
                        fn_abrir_pdf(idVenta);
                        
                    });
                    else Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.mensaje
                    });
                } catch (e) {}
            },
            error: () => Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error en la comunicación'
            })
        });
    }

    /* ================================================================
       FUNCIONES AJAX
    ================================================================ */
    function fn_insert_movimiento(venta_id, movimiento_id, cantidad, nota_archivo, sub_total) {
        return new Promise((resolve, reject) => {
            $.ajax({
                    method: 'POST',
                    url: 'logica/clssVentaCorte.php',
                    data: {
                        accion: 'INSERTMOVIMIENTO',
                        data: JSON.stringify({
                            venta_id,
                            movimiento_id,
                            cantidad,
                            sub_total,
                            nota_archivo
                        })
                    }
                })
                .done(r => {
                    const j = JSON.parse(r);
                    if (j.success) {
                        showNotification && showNotification('success');
                        fn_consultarVenta([{
                            venta_id
                        }]);
                        resolve(j);
                    } else reject(new Error(j.mensaje));
                })
                .fail(e => reject(e));
        });
    }

    function calcularSubTotal(d) {
        const c = d.cantidad === '−' || !d.cantidad ? 0 : parseInt(d.cantidad);
        const p = d.precio_venta === '−' || !d.precio_venta ? 0 : parseFloat(d.precio_venta);
        const mi = d.minutos === '−' || !d.minutos ? 0 : parseInt(d.minutos);
        const cm = d.costo_por_minuto === '−' || !d.costo_por_minuto ? 0 : parseFloat(d.costo_por_minuto);
        return (c * p) + (mi * cm);
    }

    function fn_adicionar_articulo(venta_id, d) {
        return new Promise((resolve, reject) => {
            $.ajax({
                    method: 'POST',
                    url: 'logica/clssVentaCorte.php',
                    data: {
                        accion: 'ADICIONARARTICULO',
                        data: JSON.stringify({
                            venta_id,
                            articulo_id: d.id,
                            cantidad: d.cantidad,
                            sub_total: calcularSubTotal(d),
                            minutos: d.minutos,
                            precio_unitario: d.precio_venta,
                            costoxminuto: d.costo_por_minuto,
                            movimiento_id: d.id_movimiento
                        })
                    }
                })
                .done(r => {
                    const j = JSON.parse(r);
                    if (j.success) {
                        showNotification && showNotification('success');
                        resolve(j);
                    } else {
                        showNotification && showNotification('error');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: j.message
                        });
                        reject(new Error(j.message));
                    }
                })
                .fail(e => reject(e));
        });
    }

    function fn_eliminar_articulo(id) {
        return new Promise((resolve, reject) => {
            $.ajax({
                    method: 'POST',
                    url: 'logica/clssVentaCorte.php',
                    data: {
                        accion: 'ELIMINARARTICULO',
                        id_rel_articulo: id
                    }
                })
                .done(r => {
                    const j = JSON.parse(r);
                    j.success ? resolve(j) : reject(new Error(j.mensaje));
                })
                .fail(e => reject(e));
        });
    }

    function fn_eliminar_movimiento(id) {
        return new Promise((resolve, reject) => {
            $.ajax({
                    method: 'POST',
                    url: 'logica/clssVentaCorte.php',
                    data: {
                        accion: 'ELIMINARMOVIMIENTO',
                        id_rel_articulo: id
                    }
                })
                .done(r => {
                    const j = JSON.parse(r);
                    j.success ? resolve(j) : reject(new Error(j.mensaje));
                })
                .fail(e => reject(e));
        });
    }

    function fn_editar_articulo(datos) {
        return new Promise((resolve, reject) => {
            $.ajax({
                    method: 'POST',
                    url: 'logica/clssVentaCorte.php',
                    data: {
                        accion: 'EDITARARTICULO',
                        data: JSON.stringify(datos)
                    }
                })
                .done(r => {
                    const j = JSON.parse(r);
                    j.success ? resolve(j) : reject(new Error(j.mensaje));
                })
                .fail(e => reject(e));
        });
    }

    function fn_editar_movimiento(datos) {
        return new Promise((resolve, reject) => {
            $.ajax({
                    method: 'POST',
                    url: 'logica/clssVentaCorte.php',
                    data: {
                        accion: 'EDITARMOVIMIENTO',
                        data: JSON.stringify(datos)
                    }
                })
                .done(r => {
                    const j = JSON.parse(r);
                    j.success ? resolve(j) : reject(new Error(j.mensaje));
                })
                .fail(e => reject(e));
        });
    }

    function fn_abrir_pdf(id_venta) {
        fetch("ticket.php?accion=token&id=" + parseInt(id_venta))
            .then(r => r.json())
            .then(urls => {
                Swal.fire({
                    title: '¿Cómo deseas ver el comprobante?',
                    html: `
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:8px;">
                    <button onclick="window.open('${urls.ticket}','_blank');Swal.close();location.reload();" 
                        style="background:#2a2f5b;color:white;border:none;border-radius:12px;padding:14px 10px;cursor:pointer;font-weight:700;font-size:.88rem;">
                        🖨️ Ticket<br><small style="font-weight:400;opacity:.8;">80mm / POS</small>
                    </button>
                    <button onclick="window.open('${urls.a4}','_blank');Swal.close();location.reload();" 
                        style="background:#2a2f5b;color:white;border:none;border-radius:12px;padding:14px 10px;cursor:pointer;font-weight:700;font-size:.88rem;">
                        📄 A4<br><small style="font-weight:400;opacity:.8;">Hoja completa</small>
                    </button>
                    <button onclick="window.open('${urls.a5}','_blank');Swal.close();location.reload();" 
                        style="background:#2a2f5b;color:white;border:none;border-radius:12px;padding:14px 10px;cursor:pointer;font-weight:700;font-size:.88rem;">
                        📋 A5<br><small style="font-weight:400;opacity:.8;">Medio oficio</small>
                    </button>
                    <button onclick="window.open('${urls.pantalla}','_blank');Swal.close();location.reload();" 
                        style="background:#11998e;color:white;border:none;border-radius:12px;padding:14px 10px;cursor:pointer;font-weight:700;font-size:.88rem;">
                        🌐 Pantalla<br><small style="font-weight:400;opacity:.8;">HTML / WhatsApp</small>
                    </button>
                </div>`,
                    showConfirmButton: false,
                    showCloseButton: true,
                    width: 360,
                });
            });
    }
</script>

<?php include("pie.php"); ?>