<?php
//venta_rapida_v2.php — rediseño con layout 3 columnas igual a cotizacion.php
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

    /* === HEADER === */
    .venta-header {
        background: var(--gradient-main);
        border-radius: 18px;
        color: white;
        padding: 28px 32px 22px;
        margin-bottom: 28px;
        box-shadow: var(--shadow-hover);
        position: relative;
        overflow: hidden;
    }

    .venta-header::before {
        content: "🛒";
        position: absolute;
        right: 28px;
        top: 18px;
        font-size: 3.5rem;
        opacity: 1;
        pointer-events: none;
    }

    .venta-header h3 {
        font-weight: 800;
        letter-spacing: -.5px;
        margin-bottom: 4px;
    }

    .venta-header p {
        opacity: .85;
        margin-bottom: 0;
        font-size: .97rem;
    }

    /* === PANELES BLANCOS === */
    .panel-izq {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-card);
        padding: 22px 20px;
        position: relative;
    }

    .panel-der {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-card);
        padding: 22px 20px;
        height: 100%;
    }

    .tabla-venta-wrapper {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-card);
        padding: 20px;
        height: 100%;
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

    /* === SERVICIOS === */
    .servicios-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 14px;
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
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-servicio:hover {
        background: var(--gradient-main);
        border-color: transparent;
        color: white;
        box-shadow: 0 3px 10px rgba(102, 126, 234, .3);
        transform: translateY(-1px);
    }

    .btn-servicio-special {
        background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
        border-color: transparent;
        color: var(--primary);
    }

    .btn-servicio-special:hover {
        color: var(--primary);
    }

    /* === FILTROS === */
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
        font-size: .9rem;
        pointer-events: none;
    }

    /* === RESULTADOS DROPDOWN === */
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
        z-index: 1000;
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
        opacity: .5;
    }

    #resultadosProductos::-webkit-scrollbar-thumb:hover {
        background: var(--accent);
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
        letter-spacing: .3px;
    }

    .prod-info {
        font-size: .72rem;
        color: var(--text-muted);
        margin-top: 3px;
    }

    .prod-precio {
        font-weight: 800;
        color: white;
        font-size: .88rem;
        white-space: nowrap;
        background: var(--gradient-main);
        border-radius: 20px;
        padding: 4px 12px;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(0, 51, 160, .25);
    }

    .col-lg-4 {
        overflow: visible !important;
    }

    .panel-izq {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-card);
        padding: 22px 20px;
        position: relative;
        overflow: visible !important;
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

    /* === TABS MODO AGREGAR === */
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

    /* === TABLA === */
    #tabla_articulos {
        width: 100%;
        border-collapse: collapse;
    }

    #tabla_articulos thead tr {
        background: var(--primary);
        color: white;
    }

    #tabla_articulos thead th {
        padding: 11px 14px;
        font-size: .82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        white-space: nowrap;
    }

    #tabla_articulos thead th:first-child {
        border-radius: 8px 0 0 8px;
    }

    #tabla_articulos thead th:last-child {
        border-radius: 0 8px 8px 0;
    }

    #tabla_articulos tbody tr {
        border-bottom: 1px solid #f0f3ff;
        transition: background .15s;
    }

    #tabla_articulos tbody tr:hover {
        background: #f8f9ff;
    }

    #tabla_articulos tbody td {
        padding: 10px 14px;
        font-size: .88rem;
        vertical-align: middle;
    }

    /* Ocultar columnas internas: ID(0), ID_MOV(6), NOTA(7), IMPUESTO(8), MODALIDAD(9), TASA(10) */
    #tabla_articulos th:nth-child(1),
    #tabla_articulos td:nth-child(1),
    #tabla_articulos th:nth-child(7),
    #tabla_articulos td:nth-child(7),
    #tabla_articulos th:nth-child(8),
    #tabla_articulos td:nth-child(8),
    #tabla_articulos th:nth-child(9),
    #tabla_articulos td:nth-child(9),
    #tabla_articulos th:nth-child(10),
    #tabla_articulos td:nth-child(10),
    #tabla_articulos th:nth-child(11),
    #tabla_articulos td:nth-child(11) {
        display: none !important;
    }

    .col-subtotal {
        font-weight: 700;
        color: var(--success);
        font-size: .93rem;
    }

    /* === RESUMEN PANEL DER === */
    .resumen-linea {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f0f3ff;
        font-size: .9rem;
    }

    .resumen-linea:last-child {
        border-bottom: none;
    }

    .resumen-linea .lbl {
        color: var(--text-muted);
        font-weight: 500;
    }

    .resumen-linea .val {
        font-weight: 700;
        color: var(--primary);
    }

    .resumen-total-grande {
        background: var(--gradient-main);
        border-radius: 14px;
        padding: 14px 18px;
        color: white;
        text-align: center;
        margin: 16px 0 10px;
    }

    .resumen-total-grande .label-total {
        font-size: .85rem;
        opacity: .85;
    }

    .resumen-total-grande .monto-total {
        font-size: 2.1rem;
        font-weight: 800;
        letter-spacing: -1px;
    }

    /* === DESGLOSE IMPUESTOS (panel der) === */
    .impuestos-box {
        background: #f8f9ff;
        border: 1.5px solid var(--border-soft);
        border-radius: 12px;
        padding: 10px 14px;
        margin-top: 10px;
    }

    .impuestos-box .imp-titulo {
        font-size: .75rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 5px;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .impuestos-box .imp-linea {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: .82rem;
        padding: 5px 0;
        border-bottom: 1px solid #edf0ff;
    }

    .impuestos-box .imp-linea:last-child {
        border-bottom: none;
    }

    .impuestos-box .imp-linea .imp-lbl {
        color: #555;
        font-weight: 500;
    }

    .impuestos-box .imp-linea .imp-val {
        font-weight: 700;
        color: var(--primary);
        min-width: 70px;
        text-align: right;
    }

    .impuestos-box .imp-linea.igv-line {
        border-top: 1px dashed #dde0f0;
        margin-top: 2px;
        padding-top: 7px;
    }

    .impuestos-box .imp-linea.igv-line .imp-lbl {
        color: #dc3545;
        font-weight: 700;
    }

    .impuestos-box .imp-linea.igv-line .imp-val {
        color: #dc3545;
    }

    .impuestos-box .imp-linea.icbper-line .imp-lbl {
        color: #f7971e;
        font-weight: 700;
    }

    .impuestos-box .imp-linea.icbper-line .imp-val {
        color: #f7971e;
    }

    .impuestos-box .imp-linea .imp-val.cero {
        color: #bbb;
        font-weight: 400;
    }

    /* === BOTONES === */
    .btn-primary-custom {
        background: var(--gradient-main);
        border: none;
        color: white;
        border-radius: 12px;
        padding: 10px 20px;
        font-weight: 700;
        font-size: .92rem;
        transition: all .25s;
        box-shadow: 0 3px 12px rgba(102, 126, 234, .3);
        cursor: pointer;
        width: 100%;
        display: block;
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, .4);
        color: white;
    }

    .btn-success-custom {
        background: var(--gradient-success);
        border: none;
        color: white;
        border-radius: 12px;
        padding: 10px 20px;
        font-weight: 700;
        font-size: .92rem;
        transition: all .25s;
        box-shadow: 0 3px 12px rgba(17, 153, 142, .25);
        cursor: pointer;
        width: 100%;
        display: block;
    }

    .btn-success-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(17, 153, 142, .35);
        color: white;
    }

    .btn-success-custom:disabled {
        background: #ccc;
        box-shadow: none;
        transform: none;
        cursor: not-allowed;
    }

    .btn-warning-custom {
        background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
        border: none;
        color: var(--primary);
        border-radius: 12px;
        padding: 10px 20px;
        font-weight: 700;
        font-size: .92rem;
        transition: all .25s;
        cursor: pointer;
        width: 100%;
        display: block;
    }

    .btn-warning-custom:hover {
        transform: translateY(-2px);
        color: var(--primary);
    }

    /* === EMPTY STATE === */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-muted);
    }

    .empty-state .empty-icon {
        font-size: 3rem;
        margin-bottom: 10px;
        opacity: .45;
    }

    .empty-state p {
        font-size: .9rem;
    }

    /* === BTN ELIMINAR FILA === */
    .btn-eliminar-fila {
        background: #fee2e2;
        border: none;
        color: var(--danger);
        border-radius: 8px;
        padding: 5px 10px;
        font-size: .8rem;
        cursor: pointer;
        transition: all .2s;
    }

    .btn-eliminar-fila:hover {
        background: var(--danger);
        color: white;
    }

    /* === QTY INLINE EN TABLA === */
    .qty-inline {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--bg-card);
        border-radius: 10px;
        padding: 3px 6px;
        border: 1.5px solid var(--border-soft);
    }

    .qty-inline-btn {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: none;
        font-weight: 900;
        font-size: .85rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all .18s;
        line-height: 1;
    }

    .qty-inline-minus {
        background: #fee2e2;
        color: var(--danger);
    }

    .qty-inline-plus {
        background: #d1fae5;
        color: var(--success);
    }

    .qty-inline-btn:hover {
        transform: scale(1.18);
    }

    .qty-inline-val {
        min-width: 28px;
        text-align: center;
        font-weight: 800;
        font-size: .88rem;
        color: var(--primary);
    }

    /* === FORM MANUAL === */
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

    /* === MODALES === */
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

    .modal-header-success .modal-title {
        font-weight: 800;
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

    .pago-row {
        background: var(--bg-card);
        border: 1.5px solid var(--border-soft);
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 10px;
    }

    #seccionCorte {
        background: var(--bg-card);
        border: 1.5px solid var(--border-soft);
        border-radius: 14px;
        padding: 16px;
        margin-top: 12px;
    }

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

    /* === DESGLOSE IMPUESTOS EN MODAL PAGO === */
    .impuestos-modal-box {
        background: #f8f9ff;
        border: 1.5px solid var(--border-soft);
        border-radius: 14px;
        padding: 14px 18px;
        margin-bottom: 14px;
    }

    .impuestos-modal-box .titulo {
        font-size: .83rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .impuestos-modal-box .linea {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 5px 0;
        border-bottom: 1px dashed #e3e6f5;
        font-size: .85rem;
    }

    .impuestos-modal-box .linea:last-child {
        border-bottom: none;
        padding-top: 8px;
        margin-top: 4px;
        font-size: .92rem;
    }

    .impuestos-modal-box .linea .etiqueta {
        color: var(--text-muted);
    }

    .impuestos-modal-box .linea .monto {
        font-weight: 700;
        color: var(--primary);
    }

    .impuestos-modal-box .linea.igv .monto {
        color: #dc3545;
    }

    .impuestos-modal-box .linea.icbper .monto {
        color: #f7971e;
    }

    .impuestos-modal-box .linea.exo .monto {
        color: var(--success);
    }

    .impuestos-modal-box .linea.total-imp {
        background: var(--gradient-main);
        border-radius: 8px;
        padding: 8px 12px;
        margin-top: 4px;
    }

    .impuestos-modal-box .linea.total-imp .etiqueta {
        color: rgba(255, 255, 255, .85);
        font-weight: 700;
    }

    .impuestos-modal-box .linea.total-imp .monto {
        color: white;
        font-size: 1rem;
    }

    /* === QTY === */
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

    /* === PRICE PILLS === */
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

    /* === PAGO RÁPIDO === */
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

    /* === TABS MODALES === */
    .nav-venta .nav-link {
        border-radius: 10px;
        font-weight: 600;
        font-size: .88rem;
        color: var(--text-muted);
        padding: 8px 18px;
        transition: all .2s;
        border: none;
    }

    .nav-venta .nav-link.active {
        background: var(--gradient-main);
        color: white;
        box-shadow: 0 3px 10px rgba(102, 126, 234, .3);
    }

    .nav-venta .nav-link:not(.active):hover {
        background: #f0f3ff;
        color: var(--primary);
    }

    /* === SUGERENCIAS === */
    #sugerencias {
        max-height: 200px;
        overflow-y: auto;
        z-index: 1050;
        border: 1.5px solid var(--border-soft);
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, .1);
    }

    #sugerencias .list-group-item {
        cursor: pointer;
        font-size: .88rem;
        border-color: #f0f3ff;
        padding: 10px 16px;
    }

    #sugerencias .list-group-item:hover {
        background: #f0f3ff;
        color: var(--primary);
    }

    #modalCliente {
        z-index: 1060 !important;
    }

    #panelBuscar {
        position: relative;
        overflow: visible;
    }

    /* === ANIMACIONES === */
    .fade-in-row {
        animation: fadeInRow .3s ease;
    }

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

    .pulse-total {
        animation: pulseTotal .35s ease;
    }

    @keyframes pulseTotal {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }
    }

    .search-hint {
        font-size: .78rem;
        color: var(--text-muted);
        padding: 20px 0 8px;
        text-align: center;
        opacity: .7;
    }

    /* === RESPONSIVE === */
    @media(max-width:768px) {
        .venta-header {
            padding: 18px 16px;
        }

        .venta-header::before {
            display: none;
        }

        #tabla_articulos thead {
            display: none;
        }

        #tabla_articulos tbody td {
            display: block;
            text-align: right;
            padding: 6px 12px;
        }

        #tabla_articulos tbody td::before {
            content: attr(data-label);
            float: left;
            font-weight: 600;
            color: var(--text-muted);
            font-size: .8rem;
        }

        #tabla_articulos tbody tr {
            margin-bottom: 12px;
            border: 1.5px solid var(--border-soft);
            border-radius: 10px;
        }
    }
</style>

<div class="container">
    <div class="page-inner">

        <!-- HEADER -->
        <div class="venta-header">
            <h3><i class="fas fa-shopping-cart me-2"></i> Punto de Venta Rápida</h3>
            <p>Sistema de ventas optimizado para atención al cliente. Agrega artículos y procesa el pago.</p>
        </div>

        <!-- ====== LAYOUT 3 COLUMNAS ====== -->
        <div class="row g-3" style="overflow:visible;">

            <!-- ── COL IZQUIERDA: servicios + búsqueda ── -->
            <div class="col-lg-4">
                <div class="panel-izq">

                    <div class="section-title"><i class="fas fa-cube"></i> Servicios</div>
                    <div class="servicios-wrap">
                        <?php foreach (listarMovimientos($sucursal_id) as $datos): ?>
                            <button class="btn-servicio" onclick='fn_servicios(<?php echo json_encode($datos) ?>)'>
                                <i class="fas fa-external-link-alt"></i> <?php echo $datos["descripcion"] ?>
                            </button>
                        <?php endforeach; ?>
                        <button class="btn-servicio btn-servicio-special" id="btnAbrirModalSolo">
                            <i class="fas fa-cut"></i> SOLO CORTE
                        </button>
                        <button class="btn-servicio btn-servicio-special" id="btnAbrirModalSolov2">
                            <i class="fas fa-print"></i> IMPRESIÓN 3D
                        </button>
                    </div>

                    <hr style="border-color:#f0f3ff;margin:14px 0;">

                    <div class="section-title"><i class="fas fa-filter"></i> Filtros</div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <select id="filterCategoria" class="filtro-input" onchange="filterProducts()">
                                <option value="">Categoría</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <select id="filterTipo" class="filtro-input" onchange="filterProducts()">
                                <option value="">Tipo</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <select id="filterDimension" class="filtro-input" onchange="filterProducts()">
                                <option value="">Dimensión</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <select id="filterColor" class="filtro-input" onchange="filterProducts()">
                                <option value="">Color</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-end mb-3">
                        <button onclick="clearFilters()" style="background:transparent;border:none;font-size:.78rem;color:var(--accent);font-weight:600;cursor:pointer;">
                            <i class="fas fa-broom me-1"></i>Limpiar filtros
                        </button>
                    </div>

                    <hr style="border-color:#f0f3ff;margin:0 0 14px;">

                    <!-- TABS BUSCAR / MANUAL -->
                    <div class="add-mode-tabs">
                        <button class="add-mode-tab active" id="tabBuscar" onclick="switchAddMode('buscar')">
                            <i class="fas fa-search me-1"></i>Buscar Artículo
                        </button>
                        <button class="add-mode-tab" id="tabManual" onclick="switchAddMode('manual')">
                            <i class="fas fa-pencil-alt me-1"></i>Manual
                        </button>
                    </div>

                    <!-- PANEL BUSCAR -->
                    <div class="add-panel active" id="panelBuscar">
                        <div class="search-wrapper">
                            <input type="text" id="searchInput" placeholder="Buscar por nombre, categoría o tipo..." oninput="filterProducts()">
                            <i class="fas fa-search search-icon-abs"></i>
                        </div>
                        <div id="resultadosProductos" style="display:none;"></div>
                        <div id="searchHint" class="search-hint">
                            <i class="fas fa-keyboard me-1" style="color:var(--accent);"></i>
                            Escribe para buscar entre tus productos
                        </div>
                    </div>

                    <!-- PANEL MANUAL -->
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
                                    <label>Precio (S/) <span style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;border-radius:6px;font-size:.7rem;padding:1px 7px;font-weight:700;">IGV 18%</span></label>
                                    <input type="number" id="manualPrecio" class="form-control mt-1" step="0.01" min="0" placeholder="0.00">
                                </div>
                            </div>
                            <button class="btn-primary-custom mt-1" onclick="agregarManual()">
                                <i class="fas fa-plus me-1"></i> Agregar a la Venta
                            </button>
                        </div>
                    </div>

                </div>
            </div><!-- /col izq -->

            <!-- ── COL CENTRO: tabla artículos ── -->
            <div class="col-lg-5">
                <div class="tabla-venta-wrapper">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0" style="color:var(--primary);">
                            <i class="fas fa-list-ul me-1" style="color:var(--accent);"></i>
                            Detalle de Venta
                            <span class="badge ms-1" style="background:#f0f3ff;color:var(--primary);font-size:.75rem;" id="contadorItems">0 ítems</span>
                        </h6>
                        <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="limpiarVenta()" style="font-size:.78rem;">
                            <i class="fas fa-trash me-1"></i>Limpiar
                        </button>
                    </div>

                    <div id="emptyStateVenta" class="empty-state">
                        <div class="empty-icon">🛒</div>
                        <p>Aún no hay artículos.<br>Busca o agrega manualmente.</p>
                    </div>

                    <div class="table-responsive" id="wrapperTablaVenta" style="display:none;">
                        <table id="tabla_articulos">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Artículo</th>
                                    <th style="text-align:center;">Cant.</th>
                                    <th>P. Unit.</th>
                                    <th>Subtotal</th>
                                    <th style="text-align:center;">Acc.</th>
                                    <th>ID_MOV</th>
                                    <th>NOTA</th>
                                    <th>IMPUESTO</th>
                                    <th>MODALIDAD</th>
                                    <th>TASA</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div><!-- /col centro -->

            <!-- ── COL DERECHA: resumen ── -->
            <div class="col-lg-3">
                <div class="panel-der">
                    <h6 class="fw-bold mb-3" style="color:var(--primary);">
                        <i class="fas fa-calculator me-1" style="color:var(--accent);"></i> Resumen
                    </h6>

                    <div class="resumen-linea">
                        <span class="lbl">Total ítems</span>
                        <span class="val" id="resumenItems">0</span>
                    </div>
                    <div class="resumen-linea">
                        <span class="lbl">Unidades</span>
                        <span class="val" id="resumenCantidad">0</span>
                    </div>
                    <div class="resumen-linea">
                        <span class="lbl">Subtotal artículos</span>
                        <span class="val" id="id_subtotal_articulos">S/ 0.00</span>
                    </div>

                    <div class="resumen-total-grande">
                        <div class="label-total">TOTAL GENERAL</div>
                        <div class="monto-total" id="id_subtotal_general_display">S/ 0.00</div>
                        <input type="hidden" id="id_subtotal_general" value="0.00">
                    </div>

                    <!-- DESGLOSE IMPUESTOS EN PANEL DERECHO -->
                    <div class="impuestos-box" id="boxImpuestosResumen">
                        <div class="imp-titulo"><i class="fas fa-receipt" style="color:var(--accent);"></i> Detalle tributario</div>
                        <div id="contenidoImpuestosResumen">
                            <div class="imp-linea"><span class="imp-lbl">Op. Gravadas</span><span class="imp-val cero">S/ 0.00</span></div>
                            <div class="imp-linea"><span class="imp-lbl">Op. Exoneradas</span><span class="imp-val cero">S/ 0.00</span></div>
                            <div class="imp-linea"><span class="imp-lbl">Op. Inafectas</span><span class="imp-val cero">S/ 0.00</span></div>
                            <div class="imp-linea igv-line"><span class="imp-lbl">IGV (18%)</span><span class="imp-val cero">S/ 0.00</span></div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-2">
                        <button class="btn-success-custom" id="btnRealizarPago" disabled onclick="abrirModalPago()">
                            <i class="fas fa-cash-register me-1"></i> Procesar Venta
                        </button>
                    </div>
                </div>
            </div><!-- /col der -->

        </div><!-- /row -->
    </div><!-- /page-inner -->
</div>


<!-- =============== MODAL: SOLO CORTE =============== -->
<div class="modal fade" id="modalSoloCorte" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-gradient">
                <h5 class="modal-title"><i class="fas fa-cut me-2"></i>Servicio de Corte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <label class="fw-bold mb-3 d-block" style="color:var(--primary);font-size:.85rem;"><i class="fas fa-clock me-1" style="color:var(--accent);"></i>Minutos de Corte</label>
                    <div class="qty-control mx-auto" style="width:fit-content;">
                        <button class="qty-btn qty-btn-minus" id="btnRestarSoloCorte">−</button>
                        <input id="cantidad_solocorte" type="number" value="0">
                        <button class="qty-btn qty-btn-plus" id="btnSumarSoloCorte">+</button>
                    </div>
                </div>
                <hr style="border-color:var(--border-soft);">
                <div class="text-center">
                    <label class="fw-bold mb-2 d-block" style="color:var(--primary);font-size:.85rem;"><i class="fas fa-tag me-1" style="color:var(--success);"></i>Precio (S/)</label>
                    <input id="precioSoloCorte" type="number" class="form-control text-center mx-auto fw-bold" value="1.5" style="width:140px;font-size:1.1rem;">
                    <div class="price-pills mt-2">
                        <button class="price-pill" id="btnIncremento05SoloCorte">+0.5</button>
                        <button class="price-pill" id="btnIncremento1SoloCorte">+1</button>
                        <button class="price-pill" id="btnIncremento2SoloCorte">+2</button>
                        <button class="price-pill" id="btnIncremento5SoloCorte">+5</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--border-soft);">
                <button class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn-success-custom" id="btn_agregar_solocorte" style="width:auto;padding:8px 22px;">
                    <i class="fas fa-plus me-1"></i>Agregar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- =============== MODAL: IMPRESIÓN 3D =============== -->
<div class="modal fade" id="modalSoloCorteMaquina2" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-gradient">
                <h5 class="modal-title"><i class="fas fa-print me-2"></i>Impresión 3D</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <label class="fw-bold mb-2 d-block" style="color:var(--primary);font-size:.85rem;">Tiempo (min)</label>
                    <div class="qty-control mx-auto" style="width:fit-content;">
                        <button class="qty-btn qty-btn-minus" onclick='fnAumentoOrResta("-")'>−</button>
                        <input id="cantidad_solocortev2" type="number" value="10">
                        <button class="qty-btn qty-btn-plus" onclick='fnAumentoOrResta("+")'>+</button>
                    </div>
                    <div class="price-pills mt-2">
                        <button class="price-pill" onclick="fnAumentarMin(15)">15m</button>
                        <button class="price-pill" onclick="fnAumentarMin(30)">30m</button>
                        <button class="price-pill" onclick="fnAumentarMin(60)">1h</button>
                        <button class="price-pill" onclick="fnAumentarMin(120)">2h</button>
                        <button class="price-pill" onclick="fnAumentarMin(180)">3h</button>
                    </div>
                </div>
                <hr style="border-color:var(--border-soft);">
                <div class="text-center mb-3">
                    <label class="fw-bold mb-2 d-block" style="color:var(--primary);font-size:.85rem;">Precio (S/)</label>
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
                    <label class="fw-bold mb-1" style="font-size:.83rem;color:var(--primary);"><i class="fas fa-sticky-note me-1" style="color:var(--warning);"></i>Nota</label>
                    <textarea id="nota_impresion" class="form-control mt-1" rows="2" placeholder="Modelo, color, etc." style="border-radius:10px;border:1.5px solid var(--border-soft);font-size:.87rem;"></textarea>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--border-soft);">
                <button class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn-success-custom" id="btn_agregar_solocortev2" style="width:auto;padding:8px 22px;">
                    <i class="fas fa-plus me-1"></i>Agregar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- =============== MODAL: CANTIDAD Y CORTE =============== -->
<div class="modal fade" id="modalCantidad" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-gradient">
                <h5 class="modal-title"><i class="fas fa-cogs me-2"></i>Configurar Artículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <h6 id="nombreArticulo" class="text-center fw-bold mb-4" style="color:var(--primary);background:var(--bg-card);padding:10px;border-radius:10px;font-size:1rem;"></h6>
                <div class="text-center mb-3">
                    <label class="fw-bold mb-2 d-block" style="font-size:.85rem;color:var(--primary);">Cantidad</label>
                    <div class="qty-control mx-auto" style="width:fit-content;">
                        <button class="qty-btn qty-btn-minus" id="btnRestarCantidad">−</button>
                        <input id="inputCantidad" type="number" value="1">
                        <button class="qty-btn qty-btn-plus" id="btnSumarCantidad">+</button>
                    </div>
                </div>
                <div id="seccionCorte" style="display:none;">
                    <hr style="border-color:var(--border-soft);">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fw-bold mb-2" style="font-size:.83rem;color:var(--primary);"><i class="fas fa-cut me-1" style="color:var(--accent);"></i>Min. Corte</label>
                            <div class="qty-control">
                                <button class="qty-btn qty-btn-minus" id="btnRestarCorte">−</button>
                                <input id="cantidadCorte" type="number" value="0">
                                <button class="qty-btn qty-btn-plus" id="btnSumarCorte">+</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold mb-2" style="font-size:.83rem;color:var(--primary);"><i class="fas fa-tag me-1" style="color:var(--success);"></i>Precio Corte (S/)</label>
                            <input id="precioCorte" type="number" class="form-control text-center fw-bold" value="1.5">
                            <div class="price-pills mt-2">
                                <button class="price-pill" id="btnIncremento05">+0.5</button>
                                <button class="price-pill" id="btnIncremento1">+1</button>
                                <button class="price-pill" id="btnIncremento2">+2</button>
                                <button class="price-pill" id="btnIncremento5">+5</button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="fw-bold mb-1" style="font-size:.83rem;color:var(--primary);"><i class="fas fa-sticky-note me-1" style="color:var(--warning);"></i>Detalle</label>
                        <textarea id="idTextAreaDetalleInsert" class="form-control mt-1" rows="2" placeholder="Medidas, restante, observaciones..." style="border-radius:10px;font-size:.87rem;"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--border-soft);">
                <button class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnConfirmarCantidad" class="btn-success-custom" style="width:auto;padding:8px 22px;">
                    <i class="fas fa-check me-1"></i>Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- =============== MODAL: REGISTRAR CLIENTE =============== -->
<div class="modal fade" id="modalCliente" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-gradient">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Registrar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <ul class="nav nav-venta mb-3">
                    <li class="nav-item"><button class="nav-link active" id="pills-persona-tab" data-bs-toggle="pill" data-bs-target="#pills-persona" type="button"><i class="fas fa-user me-1"></i>Persona</button></li>
                    <li class="nav-item ms-2"><button class="nav-link" id="pills-empresa-tab" data-bs-toggle="pill" data-bs-target="#pills-empresa" type="button"><i class="fas fa-building me-1"></i>Empresa</button></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="pills-persona">
                        <div class="mb-3"><label class="fw-bold" style="font-size:.83rem;color:var(--primary);">DNI <span class="text-danger">*</span></label>
                            <div class="input-group mt-1"><input type="text" class="form-control" id="numeroDocumentoPersona" placeholder="8 dígitos" maxlength="8">
                                <button class="btn" type="button" id="btnBuscarDNI" style="background:var(--gradient-main);color:white;border:none;border-radius:0 10px 10px 0;"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                        <div class="mb-3"><label class="fw-bold" style="font-size:.83rem;color:var(--primary);">Nombres <span class="text-danger">*</span></label><input type="text" class="form-control mt-1" id="nombresPersona"></div>
                        <div class="mb-3"><label class="fw-bold" style="font-size:.83rem;color:var(--primary);">Apellidos <span class="text-danger">*</span></label><input type="text" class="form-control mt-1" id="apellidosPersona"></div>
                        <div class="row g-3">
                            <div class="col-6"><label class="fw-bold" style="font-size:.83rem;color:var(--primary);">Teléfono</label><input type="text" class="form-control mt-1" id="telefonoPersona" maxlength="9"></div>
                            <div class="col-6"><label class="fw-bold" style="font-size:.83rem;color:var(--primary);">Email</label><input type="email" class="form-control mt-1" id="emailPersona"></div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="pills-empresa">
                        <div class="mb-3"><label class="fw-bold" style="font-size:.83rem;color:var(--primary);">RUC <span class="text-danger">*</span></label>
                            <div class="input-group mt-1"><input type="text" class="form-control" id="numeroDocumentoEmpresa" placeholder="11 dígitos" maxlength="11">
                                <button class="btn" type="button" id="btnBuscarRUC" style="background:var(--gradient-main);color:white;border:none;border-radius:0 10px 10px 0;"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                        <div class="mb-3"><label class="fw-bold" style="font-size:.83rem;color:var(--primary);">Nombre Comercial <span class="text-danger">*</span></label><input type="text" class="form-control mt-1" id="nombreComercial"></div>
                        <div class="mb-3"><label class="fw-bold" style="font-size:.83rem;color:var(--primary);">Razón Social <span class="text-danger">*</span></label><input type="text" class="form-control mt-1" id="razonSocial"></div>
                        <div class="row g-3">
                            <div class="col-6"><label class="fw-bold" style="font-size:.83rem;">Email</label><input type="email" class="form-control mt-1" id="emailEmpresa"></div>
                            <div class="col-6"><label class="fw-bold" style="font-size:.83rem;">Teléfono</label><input type="text" class="form-control mt-1" id="telefonoEmpresa" maxlength="9"></div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 p-2" style="background:#f0f3ff;border-radius:10px;font-size:.8rem;">
                    <i class="fas fa-info-circle me-1" style="color:var(--accent);"></i>Los campos con <span class="text-danger">*</span> son obligatorios
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--border-soft);">
                <button class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn-primary-custom" id="btnRegistrarCliente" style="width:auto;padding:8px 22px;"><i class="fas fa-save me-1"></i>Registrar</button>
            </div>
        </div>
    </div>
</div>

<!-- =============== MODAL: REALIZAR PAGO =============== -->
<div class="modal fade" id="modalRealizarPago" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-success">
                <div class="w-100 text-center">
                    <h5 class="mb-1 fw-bold"><i class="fas fa-credit-card me-2"></i>Procesar Pago</h5>
                    <h2 class="mb-0 fw-bold">S/ <span id="idMontoVentaTitulo">0.00</span></h2>
                </div>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3"><small style="color:var(--text-muted);">ID Cliente: <strong><span id="idPersona">#</span></strong> &nbsp;|&nbsp; Usuario: <strong><span id="idUsuario"><?php echo $id_usuario_s ?></span></strong></small></div>

                <!-- DESGLOSE IMPUESTOS EN MODAL PAGO -->
                <div class="impuestos-modal-box" id="impuestosModalBox" style="display:none;">
                    <div class="titulo"><i class="fas fa-file-invoice me-1" style="color:var(--accent);"></i>Desglose tributario de la venta</div>
                    <div id="impuestosModalContenido"></div>
                </div>

                <!-- CLIENTE -->
                <div class="accordion mb-3" id="accordionCliente">
                    <div class="accordion-item" style="border-radius:12px;overflow:hidden;border:1.5px solid var(--border-soft);">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" style="font-size:.9rem;color:var(--primary);">
                                <i class="fas fa-user me-2" style="color:var(--accent);"></i>Datos del Cliente
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse">
                            <div class="accordion-body" style="background:var(--bg-card);">
                                <div class="mb-3">
                                    <label class="fw-bold" style="font-size:.83rem;color:var(--primary);">Cliente</label>
                                    <div class="input-group mt-1">
                                        <input type="text" class="form-control" id="nombreCliente" placeholder="Buscar cliente por nombre o DNI">
                                        <button type="button" class="btn" id="btnAbrirModalCliente" style="background:var(--gradient-main);color:white;border:none;border-radius:0 10px 10px 0;"><i class="fas fa-user-plus"></i></button>
                                    </div>
                                    <div id="sugerencias" class="list-group position-absolute w-100"></div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-6"><label class="fw-bold" style="font-size:.83rem;">Teléfono</label><input type="text" class="form-control mt-1" id="idUpdateNumTelefonoCliente"></div>
                                    <div class="col-6"><label class="fw-bold" style="font-size:.83rem;">Email</label><input type="email" class="form-control mt-1" id="idUpdateCorreoCliente"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MONTOS -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold" style="font-size:.83rem;color:var(--primary);">Monto Original</label>
                        <div class="input-group mt-1">
                            <span class="input-group-text" style="background:#f0f3ff;border:1.5px solid var(--border-soft);border-radius:10px 0 0 10px;">S/</span>
                            <input type="text" class="form-control fw-bold" id="montoTotal" readonly style="border:1.5px solid var(--border-soft);">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold" style="font-size:.83rem;color:var(--primary);">Monto Final (con descuento)</label>
                        <div class="input-group mt-1">
                            <span class="input-group-text" style="background:#f0f3ff;border:1.5px solid var(--border-soft);border-radius:10px 0 0 10px;">S/</span>
                            <input type="number" class="form-control" id="montoTotalFinal" placeholder="Opcional" step="0.01" style="border:1.5px solid var(--border-soft);">
                        </div>
                    </div>
                </div>

                <!-- TABS PAGO -->
                <ul class="nav nav-venta mb-3">
                    <li class="nav-item flex-fill"><a class="nav-link active text-center" data-bs-toggle="pill" href="#pago-directo"><i class="fas fa-money-bill-wave me-1"></i>Pago Directo</a></li>
                    <li class="nav-item flex-fill ms-2"><a class="nav-link text-center" data-bs-toggle="pill" href="#pago-credito"><i class="fas fa-credit-card me-1"></i>A Crédito</a></li>
                </ul>

                <div class="tab-content">
                    <!-- PAGO DIRECTO -->
                    <div class="tab-pane fade show active" id="pago-directo">
                        <form id="form-pago-directo">
                            <div class="text-center mb-3">
                                <label class="fw-bold d-block mb-2" style="font-size:.83rem;color:var(--primary);">Tipo de Comprobante</label>
                                <div class="btn-group">
                                    <input type="radio" class="btn-check" name="icon-input" id="boleta" value="boleta" checked>
                                    <label class="btn btn-outline-primary" for="boleta" style="border-radius:10px 0 0 10px;font-weight:700;">Boleta</label>
                                    <input type="radio" class="btn-check" name="icon-input" id="factura" value="factura">
                                    <label class="btn btn-outline-primary" for="factura" style="border-radius:0 10px 10px 0;font-weight:700;">Factura</label>
                                </div>
                            </div>
                            <div class="text-center mb-3">
                                <button id="btnAgregarPago" class="btn btn-outline-secondary btn-sm rounded-pill" type="button">
                                    <i class="fas fa-plus me-1"></i>Agregar Forma de Pago
                                </button>
                            </div>
                            <div class="pago-row">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <select class="form-control" name="formaPago" id="formaPagoSelect">
                                            <?php foreach (listarFormaPago_v2($sucursal_id) as $fp): ?>
                                                <option value="<?php echo $fp["id"] ?>"><?php echo $fp["nombre"] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6"><input type="number" class="form-control" placeholder="Monto S/" min="0" name="monto" id="montoSelect_0" step="0.01"></div>
                                </div>
                            </div>
                            <div id="contenedorPagos"></div>

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
                        <div class="text-center mt-4">
                            <button class="btn-success-custom" onclick="fn_pagar_directo()" style="width:auto;padding:12px 36px;font-size:1rem;">
                                <i class="fas fa-check-circle me-2"></i>Confirmar Pago
                            </button>
                        </div>
                    </div>

                    <!-- PAGO CRÉDITO -->
                    <div class="tab-pane fade" id="pago-credito">
                        <div class="p-3 mb-3" style="background:#e8f5e9;border-left:4px solid var(--success);border-radius:10px;font-size:.87rem;">
                            <i class="fas fa-info-circle me-1" style="color:var(--success);"></i>Si el cliente realiza un pago inicial, regístralo. Si no, deja en blanco.
                        </div>
                        <form id="form-pago-credito">
                            <div class="text-center mb-3">
                                <button id="btnAgregarPagoCredito" class="btn btn-outline-secondary btn-sm rounded-pill" type="button">
                                    <i class="fas fa-plus me-1"></i>Agregar Pago Inicial
                                </button>
                            </div>
                            <div class="pago-row">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <select class="form-control" name="formaPagoCredito[]" id="formaPagoCreditoSelect_0">
                                            <?php foreach (listarFormaPago_v2($sucursal_id) as $fp): ?>
                                                <option value="<?php echo $fp["id"] ?>"><?php echo $fp["nombre"] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6"><input type="number" class="form-control" placeholder="Monto S/" min="0" name="montoCredito[]" id="montoSelectCredito_0" step="0.01"></div>
                                </div>
                            </div>
                            <div id="contenedorPagosCredito"></div>
                        </form>
                        <div class="text-center mt-4">
                            <button class="btn-warning-custom" onclick="fn_pagar_credito()" style="width:auto;padding:12px 36px;font-size:1rem;">
                                <i class="fas fa-handshake me-2"></i>Registrar Crédito
                            </button>
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

<!-- =============== MODAL GENÉRICO (servicios) =============== -->
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


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/scriptNotify.js"></script>
<script>
    const products = <?php echo json_encode(listarProductosVenta1($sucursal_id)); ?>;

    document.addEventListener('DOMContentLoaded', () => {
        populateFilters();
        initEventListeners();
        initVueltoListeners();
        checkCotizacion();
    });

    /* ============================================================
       COTIZACIÓN
       ============================================================ */
    function checkCotizacion() {
        const payload = sessionStorage.getItem('cotizacion_a_venta');
        if (!payload) return;
        try {
            const cot = JSON.parse(payload);
            sessionStorage.removeItem('cotizacion_a_venta');
            if (cot.cliente) {
                document.getElementById('nombreCliente').value = cot.cliente.persona_concatenada;
                document.getElementById('idPersona').textContent = cot.cliente.id;
            }
            cot.items.forEach(i => agregarATabla([{
                id: i.productoId || '0',
                articulo: i.descripcion,
                cantidad: i.cantidad,
                precio_unitario: i.precioUnit,
                subtotal: i.cantidad * i.precioUnit,
                idmovimiento: 1,
                nota: i.nota || '',
                impuesto: 'IGV',
                modalidad: 'PORCENTAJE',
                tasa: 0.18
            }]));
            Swal.fire({
                icon: 'success',
                title: '¡Cotización cargada!',
                html: `<strong>${cot.codigo}</strong> — ${cot.items.length} artículos precargados`,
                timer: 2500,
                showConfirmButton: false
            });
        } catch (e) {}
    }

    /* ============================================================
       SWITCH TABS
       ============================================================ */
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

    /* ============================================================
       FILTROS
       ============================================================ */
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
        const q = document.getElementById('searchInput').value.trim().toLowerCase();
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
            (!q || p.articulo.toLowerCase().includes(q) ||
                (p.categoria || '').toLowerCase().includes(q) ||
                (p.tipo || '').toLowerCase().includes(q))
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
        <div class="prod-precio">S/ ${parseFloat(p.precio_venta).toFixed(2)}</div>`;
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

    document.addEventListener('click', e => {
        const box = document.getElementById('resultadosProductos');
        const inp = document.getElementById('searchInput');
        if (box && !box.contains(e.target) && !inp.contains(e.target)) box.style.display = 'none';
    });

    /* ============================================================
       AGREGAR MANUAL
       ============================================================ */
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
        // Artículo manual: sin ID → se tratará como IGV 18% en el cálculo
        agregarATabla([{
            id: '0',
            articulo: desc,
            cantidad: cant,
            precio_unitario: precio,
            subtotal: cant * precio,
            idmovimiento: 1,
            nota: '',
            impuesto: 'IGV',
            modalidad: 'PORCENTAJE',
            tasa: 0.18
        }]);
        document.getElementById('manualDescripcion').value = '';
        document.getElementById('manualCantidad').value = 1;
        document.getElementById('manualPrecio').value = '';
        showNotification && showNotification("success");
    }

    /* ============================================================
       MODAL CANTIDAD
       ============================================================ */
    function fn_agregar_venta(articulo) {
        const modal = new bootstrap.Modal(document.getElementById('modalCantidad'));
        document.getElementById('nombreArticulo').textContent = articulo.articulo;
        document.getElementById('inputCantidad').value = 1;
        document.getElementById('cantidadCorte').value = 0;
        document.getElementById('precioCorte').value = articulo.corte ? 1.5 : 0;
        document.getElementById('idTextAreaDetalleInsert').value = '';
        const sc = document.getElementById('seccionCorte');
        sc.style.display = articulo.corte ? 'block' : 'none';

        document.getElementById('btnRestarCantidad').onclick = () => {
            let c = parseInt(document.getElementById('inputCantidad').value);
            if (c > 1) {
                document.getElementById('inputCantidad').value = c - 1;
                if (c - 1 === 1 && articulo.corte) {
                    sc.style.display = 'block';
                    document.getElementById('precioCorte').value = 1.5;
                } else if (c - 1 > 1) {
                    sc.style.display = 'none';
                }
            }
        };
        document.getElementById('btnSumarCantidad').onclick = () => {
            let c = parseInt(document.getElementById('inputCantidad').value);
            if (c + 1 > articulo.stock) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stock insuficiente',
                    text: `Solo hay ${articulo.stock} unidades`,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                document.getElementById('inputCantidad').value = c + 1;
                if (c + 1 > 1) {
                    sc.style.display = 'none';
                    document.getElementById('cantidadCorte').value = 0;
                    document.getElementById('precioCorte').value = 0;
                }
            }
        };
        document.getElementById('btnRestarCorte').onclick = () => {
            let v = parseInt(document.getElementById('cantidadCorte').value);
            if (v > 0) document.getElementById('cantidadCorte').value = v - 1;
        };
        document.getElementById('btnSumarCorte').onclick = () => {
            let v = parseInt(document.getElementById('cantidadCorte').value);
            document.getElementById('cantidadCorte').value = v === 0 ? 10 : v + 1;
        };
        ['05', '1', '2', '5'].forEach(inc => {
            document.getElementById(`btnIncremento${inc}`).onclick = () => {
                let p = parseFloat(document.getElementById('precioCorte').value);
                document.getElementById('precioCorte').value = (p + parseFloat(inc.replace('0', '.'))).toFixed(2);
            };
        });

        document.getElementById('btnConfirmarCantidad').onclick = () => {
            const cant = parseInt(document.getElementById('inputCantidad').value);
            const min = parseInt(document.getElementById('cantidadCorte').value) || 0;
            const pCorte = parseFloat(document.getElementById('precioCorte').value) || 0;
            const nota = document.getElementById('idTextAreaDetalleInsert').value;
            if (cant > articulo.stock) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stock insuficiente',
                    text: `Solo hay ${articulo.stock} unidades`,
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
            agregarATabla([{
                id: articulo.id,
                articulo: articulo.articulo + (nota ? ` - ${nota}` : ''),
                cantidad: cant,
                precio_unitario: articulo.precio_venta,
                subtotal: (cant * articulo.precio_venta) + (min * pCorte),
                idmovimiento: 1,
                nota,
                stock: articulo.stock,
                impuesto: articulo.impuesto || 'IGV',
                modalidad: articulo.modalidad_porcentaje || 'PORCENTAJE',
                tasa: parseFloat(articulo.impuesto_porcentaje) || 0.18
            }]);
            modal.hide();
            document.getElementById('resultadosProductos').style.display = 'none';
            document.getElementById('searchInput').value = '';
            const hint = document.getElementById('searchHint');
            if (hint) hint.style.display = 'block';
            showNotification && showNotification("success");
        };
        modal.show();
    }

    /* ============================================================
       TABLA — agregar filas
       ============================================================ */
    function agregarATabla(datos) {
        const tbody = document.getElementById('tabla_articulos').getElementsByTagName('tbody')[0];

        datos.forEach(item => {
            const cantidadEsNumero = !isNaN(parseInt(item.cantidad)) && item.cantidad !== '-';
            const stockMax = item.stock !== undefined ? parseInt(item.stock) : Infinity;

            // ── DETECTAR DUPLICADO ──
            if (item.id !== '0' && item.id !== 0 && cantidadEsNumero) {
                const filas = tbody.querySelectorAll('tr');
                let duplicado = null;
                filas.forEach(f => {
                    if (f.cells[0].textContent == item.id) duplicado = f;
                });
                if (duplicado) {
                    const qtyEl = duplicado.cells[2].querySelector('.qty-inline-val');
                    const precioUnit = parseFloat(duplicado.cells[3].textContent) || null;
                    const stockGuardado = parseInt(duplicado.dataset.stock) || Infinity;
                    if (qtyEl) {
                        const actual = parseInt(qtyEl.textContent);
                        const nueva = actual + parseInt(item.cantidad);
                        if (nueva > stockGuardado) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Stock insuficiente',
                                html: `Solo hay <b>${stockGuardado}</b> unidades disponibles.<br>Ya tienes <b>${actual}</b> en el detalle.`,
                                timer: 2500,
                                showConfirmButton: false
                            });
                            return;
                        }
                        qtyEl.textContent = nueva;
                        recalcularFila(duplicado, precioUnit);
                        duplicado.style.transition = 'background .2s';
                        duplicado.style.background = '#e8f5e9';
                        setTimeout(() => {
                            duplicado.style.background = '';
                        }, 600);
                        showNotification && showNotification("success");
                    }
                    return;
                }
            }

            // ── INSERTAR NUEVA FILA ──
            const fila = tbody.insertRow();
            fila.className = 'fade-in-row';
            fila.dataset.stock = stockMax;

            // col 0 — ID (oculta)
            fila.insertCell(0).textContent = item.id;

            // col 1 — Artículo
            const tdArt = fila.insertCell(1);
            tdArt.textContent = item.articulo;
            tdArt.setAttribute('data-label', 'Artículo');

            // col 2 — Cantidad
            const tdC = fila.insertCell(2);
            tdC.setAttribute('data-label', 'Cant.');
            if (cantidadEsNumero) {
                const cantInit = parseInt(item.cantidad);
                const precioUnit = parseFloat(item.precio_unitario) || null;
                tdC.innerHTML = `<div class="qty-inline">
                <button class="qty-inline-btn qty-inline-minus" title="Restar">−</button>
                <span class="qty-inline-val">${cantInit}</span>
                <button class="qty-inline-btn qty-inline-plus" title="Sumar">+</button>
            </div>`;
                const btnMinus = tdC.querySelector('.qty-inline-minus');
                const btnPlus = tdC.querySelector('.qty-inline-plus');
                const valEl = tdC.querySelector('.qty-inline-val');
                btnMinus.addEventListener('click', () => {
                    let v = parseInt(valEl.textContent);
                    if (v > 1) {
                        valEl.textContent = v - 1;
                        recalcularFila(fila, precioUnit);
                    }
                });
                btnPlus.addEventListener('click', () => {
                    let v = parseInt(valEl.textContent);
                    const stockActual = parseInt(fila.dataset.stock) || Infinity;
                    if (v >= stockActual) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Stock máximo alcanzado',
                            text: `Solo hay ${stockActual} unidades disponibles.`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        return;
                    }
                    valEl.textContent = v + 1;
                    recalcularFila(fila, precioUnit);
                });
            } else {
                tdC.textContent = item.cantidad;
            }

            // col 3 — Precio unitario
            const tdP = fila.insertCell(3);
            tdP.textContent = item.precio_unitario;
            tdP.setAttribute('data-label', 'P. Unit.');

            // col 4 — Subtotal
            const tdS = fila.insertCell(4);
            tdS.className = 'col-subtotal';
            tdS.textContent = 'S/ ' + parseFloat(item.subtotal).toFixed(2);
            tdS.setAttribute('data-label', 'Subtotal');

            // col 5 — Acciones
            const tdA = fila.insertCell(5);
            tdA.setAttribute('data-label', 'Acc.');
            tdA.style.textAlign = 'center';
            const btn = document.createElement('button');
            btn.className = 'btn-eliminar-fila';
            btn.innerHTML = '<i class="fas fa-trash-alt"></i>';
            btn.onclick = () => {
                Swal.fire({
                        title: '¿Eliminar?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí',
                        cancelButtonText: 'Cancelar'
                    })
                    .then(r => {
                        if (r.isConfirmed) {
                            fila.remove();
                            calcularTotales();
                            showNotification && showNotification("success");
                        }
                    });
            };
            tdA.appendChild(btn);

            // col 6 — ID Movimiento (oculta)
            fila.insertCell(6).textContent = item.idmovimiento;

            // col 7 — Nota (oculta)
            fila.insertCell(7).textContent = item.nota || '';

            // col 8 — Impuesto (oculta) → IGV | ICBPER | EXONERADO | INAFECTO
            fila.insertCell(8).textContent = (item.impuesto || 'IGV').toUpperCase();

            // col 9 — Modalidad (oculta) → PORCENTAJE | MONTO FIJO
            fila.insertCell(9).textContent = (item.modalidad || 'PORCENTAJE').toUpperCase();

            // col 10 — Tasa (oculta) → 0.18 | 0.50 etc.
            fila.insertCell(10).textContent = item.tasa !== undefined ? item.tasa : 0.18;
        });

        calcularTotales();
    }

    function recalcularFila(fila, precioUnit) {
        if (precioUnit === null || isNaN(precioUnit)) {
            calcularTotales();
            return;
        }
        const valEl = fila.cells[2].querySelector('.qty-inline-val');
        if (!valEl) {
            calcularTotales();
            return;
        }
        const nuevaCant = parseInt(valEl.textContent) || 1;
        fila.cells[4].textContent = 'S/ ' + (nuevaCant * precioUnit).toFixed(2);
        calcularTotales();
    }

    /* ============================================================
       CALCULAR TOTALES + DESGLOSE IMPUESTOS EN PANEL DER
       ============================================================ */
    function calcularTotales() {
        const filas = document.querySelectorAll('#tabla_articulos tbody tr');
        let totalArt = 0,
            totalGen = 0,
            totalUnid = 0;

        filas.forEach(f => {
            const qtyEl = f.cells[2].querySelector('.qty-inline-val');
            const cant = parseFloat(qtyEl ? qtyEl.textContent : f.cells[2].textContent) || 0;
            const precio = parseFloat(f.cells[3].textContent) || 0;
            const sub = parseFloat((f.cells[4].textContent || '').replace('S/ ', '')) || 0;
            totalArt += cant * precio;
            totalGen += sub;
            if (!isNaN(cant)) totalUnid += cant;
        });

        // Calcular el total REAL incluyendo ICBPER
        const imp = calcularImpuestos();
        const totalReal = imp.total;

        document.getElementById('id_subtotal_articulos').textContent = 'S/ ' + totalArt.toFixed(2);
        // El hidden y el display usan el total real (con ICBPER)
        document.getElementById('id_subtotal_general').value = totalReal.toFixed(2);
        document.getElementById('resumenItems').textContent = filas.length;
        document.getElementById('resumenCantidad').textContent = totalUnid + ' unid.';

        const el = document.getElementById('id_subtotal_general_display');
        el.textContent = 'S/ ' + totalReal.toFixed(2);
        el.classList.remove('pulse-total');
        void el.offsetWidth;
        el.classList.add('pulse-total');

        const hay = filas.length > 0;
        document.getElementById('btnRealizarPago').disabled = !hay;
        document.getElementById('emptyStateVenta').style.display = hay ? 'none' : 'block';
        document.getElementById('wrapperTablaVenta').style.display = hay ? 'block' : 'none';
        document.getElementById('contadorItems').textContent = filas.length + (filas.length === 1 ? ' ítem' : ' ítems');

        // Actualizar desglose en panel derecho
        actualizarDesgloseResumen();
    }

    function limpiarVenta() {
        const filas = document.querySelectorAll('#tabla_articulos tbody tr');
        if (!filas.length) return;
        Swal.fire({
                title: '¿Limpiar lista?',
                text: 'Se eliminarán todos los artículos.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, limpiar',
                cancelButtonText: 'Cancelar'
            })
            .then(r => {
                if (r.isConfirmed) {
                    document.querySelector('#tabla_articulos tbody').innerHTML = '';
                    calcularTotales();
                }
            });
    }

    /* ============================================================
       CALCULAR IMPUESTOS — función central
       ============================================================
       Reglas:
       - IGV + PORCENTAJE  : precio_venta YA incluye IGV → base = subtotal / (1 + tasa)
       - ICBPER + MONTO FIJO: S/ fijo por unidad (0.50 desde 2022), además el
         producto también tributa IGV → descomponer subtotal y sumar ICBPER aparte
       - EXONERADO          : sin IGV, suma a opExoneradas
       - INAFECTO           : sin IGV, suma a opInafectas
       - Artículo manual (id=0): se asume IGV 18%
       ============================================================ */
    function calcularImpuestos() {
        const filas = Array.from(document.querySelectorAll('#tabla_articulos tbody tr'));

        let opGravadas = 0; // Base imponible (sin IGV)
        let montoIGV = 0; // IGV total
        let opExoneradas = 0; // Exoneradas
        let opInafectas = 0; // Inafectas
        let montoICBPER = 0; // Bolsas plásticas

        filas.forEach(fila => {
            const qtyEl = fila.cells[2].querySelector('.qty-inline-val');
            const cantRaw = qtyEl ? qtyEl.textContent : fila.cells[2].textContent;
            const cantidad = parseFloat(cantRaw) || 0;
            const subtotal = parseFloat((fila.cells[4].textContent || '').replace('S/ ', '')) || 0;
            const impuesto = (fila.cells[8].textContent || 'IGV').toUpperCase().trim();
            const modalidad = (fila.cells[9].textContent || 'PORCENTAJE').toUpperCase().trim();
            const tasa = parseFloat(fila.cells[10].textContent) || 0.18;

            if (impuesto === 'IGV') {
                // precio_venta incluye IGV → descomponer
                const base = subtotal / (1 + tasa);
                opGravadas += base;
                montoIGV += subtotal - base;

            } else if (impuesto === 'ICBPER') {
                // ICBPER es adicional al precio (monto fijo por unidad)
                if (modalidad === 'MONTO FIJO') {
                    montoICBPER += cantidad * tasa; // ej: 5 bolsas × 0.50 = 2.50
                } else {
                    montoICBPER += subtotal * tasa;
                }
                // Las bolsas también pagan IGV 18% sobre su precio
                const base = subtotal / 1.18;
                opGravadas += base;
                montoIGV += subtotal - base;

            } else if (impuesto === 'EXONERADO') {
                opExoneradas += subtotal;

            } else if (impuesto === 'INAFECTO') {
                opInafectas += subtotal;

            } else {
                // Fallback: tratar como gravado IGV 18%
                const base = subtotal / 1.18;
                opGravadas += base;
                montoIGV += subtotal - base;
            }
        });

        const totalSinICBPER = opGravadas + montoIGV + opExoneradas + opInafectas;
        const totalConICBPER = totalSinICBPER + montoICBPER;

        return {
            gravadas: opGravadas,
            igv: montoIGV,
            exoneradas: opExoneradas,
            inafectas: opInafectas,
            icbper: montoICBPER,
            totalSinICBPER,
            total: totalConICBPER
        };
    }

    /* ── Desglose en panel derecho — siempre visible, 3 líneas fijas ── */
    function actualizarDesgloseResumen() {
        const cont = document.getElementById('contenidoImpuestosResumen');
        const filas = document.querySelectorAll('#tabla_articulos tbody tr');

        // Si no hay ítems, resetear a ceros
        if (!filas.length) {
            cont.innerHTML = `
            <div class="imp-linea"><span class="imp-lbl">Op. Gravadas</span><span class="imp-val cero">S/ 0.00</span></div>
            <div class="imp-linea"><span class="imp-lbl">Op. Exoneradas</span><span class="imp-val cero">S/ 0.00</span></div>
            <div class="imp-linea"><span class="imp-lbl">Op. Inafectas</span><span class="imp-val cero">S/ 0.00</span></div>
            <div class="imp-linea igv-line"><span class="imp-lbl">IGV (18%)</span><span class="imp-val cero">S/ 0.00</span></div>`;
            return;
        }

        const imp = calcularImpuestos();

        const fmtVal = (n) => n > 0 ?
            `<span class="imp-val">S/ ${n.toFixed(2)}</span>` :
            `<span class="imp-val cero">S/ 0.00</span>`;

        let html = `
        <div class="imp-linea"><span class="imp-lbl">Op. Gravadas</span>${fmtVal(imp.gravadas)}</div>
        <div class="imp-linea"><span class="imp-lbl">Op. Exoneradas</span>${fmtVal(imp.exoneradas)}</div>
        <div class="imp-linea"><span class="imp-lbl">Op. Inafectas</span>${fmtVal(imp.inafectas)}</div>
        <div class="imp-linea igv-line"><span class="imp-lbl">IGV (18%)</span>${fmtVal(imp.igv)}</div>`;

        if (imp.icbper > 0)
            html += `<div class="imp-linea icbper-line"><span class="imp-lbl">ICBPER (bolsas)</span><span class="imp-val">S/ ${imp.icbper.toFixed(2)}</span></div>`;

        cont.innerHTML = html;
    }

    /* ── Desglose en modal de pago — siempre visible, 3 líneas fijas ── */
    function mostrarImpuestosEnModal() {
        const box = document.getElementById('impuestosModalBox');
        const cont = document.getElementById('impuestosModalContenido');
        const imp = calcularImpuestos();

        const fmtLinea = (lbl, n, extraClass = '') =>
            `<div class="linea ${extraClass}">
            <span class="etiqueta">${lbl}</span>
            <span class="monto${n === 0 ? ' text-muted fw-normal' : ''}">S/ ${n.toFixed(2)}</span>
        </div>`;

        let html = fmtLinea('Op. Gravadas', imp.gravadas);
        html += fmtLinea('Op. Exoneradas', imp.exoneradas, 'exo');
        html += fmtLinea('Op. Inafectas', imp.inafectas);
        html += fmtLinea('IGV (18%)', imp.igv, 'igv');

        if (imp.icbper > 0)
            html += fmtLinea('ICBPER (bolsas plásticas)', imp.icbper, 'icbper');

        html += `<div class="linea total-imp"><span class="etiqueta">TOTAL</span><span class="monto">S/ ${imp.total.toFixed(2)}</span></div>`;

        cont.innerHTML = html;
        box.style.display = 'block';
    }

    /* ============================================================
       ABRIR MODAL PAGO
       ============================================================ */
    function abrirModalPago() {
        // imp.total = subtotal artículos + ICBPER (si hay bolsas)
        const imp = calcularImpuestos();
        const totalReal = imp.total.toFixed(2);

        // Actualizar también el hidden y el display del panel derecho
        document.getElementById('id_subtotal_general').value = totalReal;
        document.getElementById('id_subtotal_general_display').textContent = 'S/ ' + totalReal;

        document.getElementById('montoTotal').value = totalReal;
        document.getElementById('idMontoVentaTitulo').textContent = totalReal;
        document.getElementById('montoTotalFinal').value = '';
        mostrarImpuestosEnModal();
        new bootstrap.Modal(document.getElementById('modalRealizarPago')).show();
    }

    /* ============================================================
       SOLO CORTE
       ============================================================ */
    function initSoloCorteModal() {
        document.getElementById('btnAbrirModalSolo').addEventListener('click', e => {
            e.preventDefault();
            document.getElementById('cantidad_solocorte').value = 0;
            document.getElementById('precioSoloCorte').value = 1.5;
            new bootstrap.Modal(document.getElementById('modalSoloCorte')).show();
        });
        document.getElementById('btnSumarSoloCorte').onclick = () => {
            let v = parseInt(document.getElementById('cantidad_solocorte').value);
            document.getElementById('cantidad_solocorte').value = v === 0 ? 10 : v + 1;
        };
        document.getElementById('btnRestarSoloCorte').onclick = () => {
            let v = parseInt(document.getElementById('cantidad_solocorte').value);
            if (v > 0) document.getElementById('cantidad_solocorte').value = v - 1;
        };
        ['05', '1', '2', '5'].forEach(inc => {
            document.getElementById(`btnIncremento${inc}SoloCorte`).onclick = () => {
                let p = parseFloat(document.getElementById('precioSoloCorte').value);
                document.getElementById('precioSoloCorte').value = (p + parseFloat(inc.replace('0', '.'))).toFixed(2);
            };
        });
        document.getElementById('btn_agregar_solocorte').addEventListener('click', () => {
            const min = parseInt(document.getElementById('cantidad_solocorte').value) || 0;
            const tar = parseFloat(document.getElementById('precioSoloCorte').value) || 0;
            if (min <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Error',
                    text: 'Ingresa minutos válidos',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
            agregarATabla([{
                id: '0',
                articulo: 'SOLO CORTE',
                cantidad: '-',
                precio_unitario: '-',
                subtotal: min * tar,
                idmovimiento: 6,
                nota: '',
                impuesto: 'IGV',
                modalidad: 'PORCENTAJE',
                tasa: 0.18
            }]);
            bootstrap.Modal.getInstance(document.getElementById('modalSoloCorte')).hide();
            showNotification && showNotification("success");
        });
    }

    /* ============================================================
       IMPRESIÓN 3D
       ============================================================ */
    function initImpresion3DModal() {
        document.getElementById('btnAbrirModalSolov2').addEventListener('click', e => {
            e.preventDefault();
            document.getElementById('cantidad_solocortev2').value = 10;
            document.getElementById('precioSoloCortev2').value = 1.5;
            document.getElementById('nota_impresion').value = '';
            new bootstrap.Modal(document.getElementById('modalSoloCorteMaquina2')).show();
        });
        document.getElementById('btn_agregar_solocortev2').addEventListener('click', () => {
            const min = parseInt(document.getElementById('cantidad_solocortev2').value) || 0;
            const tar = parseFloat(document.getElementById('precioSoloCortev2').value) || 0;
            const nota = document.getElementById('nota_impresion').value || '';
            if (min <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Error',
                    text: 'Ingresa minutos válidos',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
            agregarATabla([{
                id: '0',
                articulo: 'IMPRESIÓN 3D' + (nota ? ` - ${nota}` : ''),
                cantidad: '-',
                precio_unitario: '-',
                subtotal: min * tar,
                idmovimiento: 15,
                nota: '',
                impuesto: 'IGV',
                modalidad: 'PORCENTAJE',
                tasa: 0.18
            }]);
            bootstrap.Modal.getInstance(document.getElementById('modalSoloCorteMaquina2')).hide();
            showNotification && showNotification("success");
        });
    }

    function fnAumentoOrResta(a) {
        let v = parseInt(document.getElementById('cantidad_solocortev2').value);
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

    /* ============================================================
       SERVICIOS GENÉRICOS
       ============================================================ */
    function fn_servicios(servicio) {
        const medidas = servicio.medidas.slice(1, -1).split(',');
        document.getElementById('modalContent').innerHTML = `
    <div class="modal-header modal-header-gradient"><h5 class="modal-title fw-bold"><i class="fas fa-cube me-2"></i>${servicio.descripcion}</h5></div>
    <div class="p-4">
      <div class="mb-4 text-center">
        <label class="fw-bold mb-2 d-block" style="font-size:.85rem;color:var(--primary);">Cantidad</label>
        <div class="qty-control mx-auto" style="width:fit-content;">
          <button class="qty-btn qty-btn-minus" onclick="ajustarCantidad('${servicio.descripcion}',-1)">−</button>
          <input type="number" id="cant_${servicio.descripcion}" value="1">
          <button class="qty-btn qty-btn-plus" onclick="ajustarCantidad('${servicio.descripcion}',1)">+</button>
        </div>
      </div>
      <div class="mb-3">
        <label class="fw-bold mb-2 d-block" style="font-size:.85rem;color:var(--primary);">Dimensiones</label>
        <div class="d-flex flex-wrap gap-3 justify-content-center" id="dims_${servicio.descripcion}">
          ${medidas.map(m=>`<div class="form-check"><input class="form-check-input" type="checkbox" value="${m}" id="dim_${m}"><label class="form-check-label" for="dim_${m}" style="font-size:.88rem;">${m}</label></div>`).join('')}
        </div>
      </div>
      <div class="mb-3"><label class="fw-bold mb-1" style="font-size:.85rem;color:var(--primary);">Monto (S/)</label><input type="number" id="monto_${servicio.descripcion}" class="form-control mt-1" placeholder="0.00" step="0.01" style="border-radius:10px;border:1.5px solid var(--border-soft);"></div>
      <div class="mb-4"><label class="fw-bold mb-1" style="font-size:.85rem;color:var(--primary);">Detalle</label><textarea id="detalle_${servicio.descripcion}" class="form-control mt-1" rows="2" style="border-radius:10px;font-size:.87rem;"></textarea></div>
      <div class="text-center"><button class="btn-success-custom" onclick="agregarServicio('${servicio.descripcion}',${servicio.id})" style="width:auto;padding:10px 28px;"><i class="fas fa-plus me-1"></i>Agregar</button></div>
    </div>`;
        new bootstrap.Modal(document.getElementById('modalGenerico')).show();
    }

    function ajustarCantidad(s, inc) {
        const i = document.getElementById(`cant_${s}`);
        i.value = Math.max(1, parseInt(i.value) + inc);
    }

    function agregarServicio(nombre, idMov) {
        const cant = parseInt(document.getElementById(`cant_${nombre}`).value);
        const monto = parseFloat(document.getElementById(`monto_${nombre}`).value) || 0;
        const detalle = document.getElementById(`detalle_${nombre}`).value;
        const dims = Array.from(document.querySelectorAll(`#dims_${nombre} input:checked`)).map(c => c.value).join(', ');
        if (monto <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Error',
                text: 'Ingresa un monto válido',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }
        const art = dims ? `${nombre} (${dims})${detalle?' - '+detalle:''}` : nombre + (detalle ? ' - ' + detalle : '');
        agregarATabla([{
            id: '0',
            articulo: art,
            cantidad: cant,
            precio_unitario: '-',
            subtotal: monto,
            idmovimiento: idMov,
            nota: '',
            impuesto: 'IGV',
            modalidad: 'PORCENTAJE',
            tasa: 0.18
        }]);
        bootstrap.Modal.getInstance(document.getElementById('modalGenerico')).hide();
        showNotification && showNotification("success");
    }

    /* ============================================================
       VUELTO
       ============================================================ */
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
        const sv = document.getElementById('seccionVuelto');
        if (hay) {
            sv.style.display = 'block';
            actualizarTotalAPagar();
        } else {
            sv.style.display = 'none';
            document.getElementById('pagaCon').value = '';
            document.getElementById('vuelto').value = '0.00';
        }
    }

    function actualizarTotalAPagar() {
        const m = parseFloat(document.getElementById('montoTotalFinal').value) || parseFloat(document.getElementById('montoTotal').value);

        // Sumar todos los montos de formas de pago que NO son efectivo
        let montoNoEfectivo = 0;

        // Fila principal
        const spPrincipal = document.getElementById('formaPagoSelect');
        const mpPrincipal = parseFloat(document.getElementById('montoSelect_0').value) || 0;
        if (mpPrincipal > 0 && !spPrincipal.options[spPrincipal.selectedIndex].text.toUpperCase().includes('EFECTIVO')) {
            montoNoEfectivo += mpPrincipal;
        }

        // Filas adicionales
        document.querySelectorAll('#contenedorPagos .pago-row').forEach(row => {
            const sel = row.querySelector('select');
            const mval = parseFloat(row.querySelector('input[type="number"]')?.value) || 0;
            if (sel && mval > 0 && !sel.options[sel.selectedIndex].text.toUpperCase().includes('EFECTIVO')) {
                montoNoEfectivo += mval;
            }
        });

        const restante = Math.max(0, m - montoNoEfectivo);
        document.getElementById('totalAPagar').value = restante.toFixed(2);
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

    /* ============================================================
       MODAL PAGO (agregar filas adicionales)
       ============================================================ */
    function initPagoModal() {
        let cP = 1;
        document.getElementById('btnAgregarPago').addEventListener('click', () => {
            const c = document.getElementById('contenedorPagos');
            const d = document.createElement('div');
            d.className = 'pago-row';
            d.innerHTML = `<div class="row g-2">
            <div class="col-md-5">
                <select class="form-control" name="formaPago_${cP}" onchange="detectarEfectivo()">
                    <?php foreach (listarFormaPago_v2($sucursal_id) as $fp): ?>
                    <option value="<?php echo $fp['id'] ?>"><?php echo $fp['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5"><input type="number" class="form-control" name="monto_${cP}" placeholder="Monto S/" min="0" step="0.01" oninput="detectarEfectivo()"></div>
            <div class="col-md-2"><button type="button" class="btn btn-sm btn-outline-danger rounded-pill w-100" onclick="this.closest('.pago-row').remove();detectarEfectivo();"><i class="fas fa-times"></i></button></div>
        </div>`;
            c.appendChild(d);
            cP++;
        });

        let cC = 1;
        document.getElementById('btnAgregarPagoCredito').addEventListener('click', () => {
            const c = document.getElementById('contenedorPagosCredito');
            const d = document.createElement('div');
            d.className = 'pago-row';
            d.innerHTML = `<div class="row g-2">
            <div class="col-md-5">
                <select class="form-control" name="formaPagoCredito[]">
                    <?php foreach (listarFormaPago_v2($sucursal_id) as $fp): ?>
                    <option value="<?php echo $fp['id'] ?>"><?php echo $fp['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5"><input type="number" class="form-control" name="montoCredito[]" placeholder="Monto S/" min="0" step="0.01"></div>
            <div class="col-md-2"><button type="button" class="btn btn-sm btn-outline-danger rounded-pill w-100" onclick="this.closest('.pago-row').remove()"><i class="fas fa-times"></i></button></div>
        </div>`;
            c.appendChild(d);
            cC++;
        });
    }

    /* ============================================================
       OBTENER ARTÍCULOS — incluye datos tributarios por línea
       ============================================================ */
    function obtenerArticulos() {
        return Array.from(document.querySelectorAll('#tabla_articulos tbody tr')).map(f => {
            const qtyEl = f.cells[2].querySelector('.qty-inline-val');
            const cantRaw = qtyEl ? qtyEl.textContent : f.cells[2].textContent;
            const subtotal = parseFloat((f.cells[4].textContent || '').replace('S/ ', '')) || 0;
            const impuesto = (f.cells[8].textContent || 'IGV').toUpperCase().trim();
            const modalidad = (f.cells[9].textContent || 'PORCENTAJE').toUpperCase().trim();
            const tasa = parseFloat(f.cells[10].textContent) || 0.18;

            // Calcular impuesto de esta línea
            let base_imponible = 0,
                igv_linea = 0,
                icbper_linea = 0;
            const cantidad = parseFloat(cantRaw) || 0;

            if (impuesto === 'IGV') {
                base_imponible = subtotal / (1 + tasa);
                igv_linea = subtotal - base_imponible;
            } else if (impuesto === 'ICBPER') {
                base_imponible = subtotal / 1.18;
                igv_linea = subtotal - base_imponible;
                icbper_linea = modalidad === 'MONTO FIJO' ? cantidad * tasa : subtotal * tasa;
            } else if (impuesto === 'EXONERADO' || impuesto === 'INAFECTO') {
                base_imponible = subtotal;
            }

            return {
                articulo_id: f.cells[0].textContent === '0' ? null : parseInt(f.cells[0].textContent),
                minutos: null,
                costoxminuto: null,
                precio_unitario: isNaN(parseFloat(f.cells[3].textContent)) ? null : parseFloat(f.cells[3].textContent),
                cantidad: isNaN(parseInt(cantRaw)) ? null : parseInt(cantRaw),
                sub_total: subtotal,
                movimiento_id: parseInt(f.cells[6].textContent),
                nota_archivo: f.cells[1].textContent + (f.cells[7].textContent ? ' / ' + f.cells[7].textContent : ''),
                // ── Campos tributarios ──
                tipo_impuesto: impuesto,
                base_imponible: parseFloat(base_imponible.toFixed(4)),
                igv: parseFloat(igv_linea.toFixed(4)),
                icbper: parseFloat(icbper_linea.toFixed(4))
            };
        });
    }

    /* ============================================================
       PAGO DIRECTO
       ============================================================ */
    async function fn_pagar_directo() {
        const fd = $('#form-pago-directo').serializeArray();
        const montoO = parseFloat(document.getElementById('montoTotal').value);
        const montoF = parseFloat(document.getElementById('montoTotalFinal').value) || montoO;
        const tipoC = document.querySelector('input[name="icon-input"]:checked').value;
        const idP = document.getElementById('idPersona').textContent.trim();

        if (tipoC === 'factura' && idP === '#') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Para factura necesitas un cliente',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        let fps = [],
            total = 0,
            fp = null;
        fd.forEach(i => {
            if (i.name.startsWith('formaPago')) {
                fp = i.value;
            } else if (i.name.startsWith('monto')) {
                const m = parseFloat(i.value);
                if (fp && m > 0) {
                    fps.push({
                        id_forma_pago: fp,
                        monto_forma_pago: m
                    });
                    total += m;
                    fp = null;
                }
            }
        });
        if (!fps.length) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Agrega al menos una forma de pago',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }
        if (Math.abs(total - montoF) > 0.01) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Los montos no coinciden con el total',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

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
                document.getElementById('pagaCon').focus();
                return;
            }
            if (vuelto > 0) {
                const r = await Swal.fire({
                    title: 'Confirmar Vuelto',
                    html: `<div class="text-start"><p><b>Total:</b> S/ ${montoF.toFixed(2)}</p><p><b>Paga con:</b> S/ ${pagaCon.toFixed(2)}</p><p class="text-success fs-4"><b>Vuelto:</b> S/ ${vuelto.toFixed(2)}</p></div>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Confirmar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#11998e'
                });
                if (!r.isConfirmed) return;
            }
        }

        // Resumen tributario total
        const imp = calcularImpuestos();

        const venta = {
            tipo_comprobante: tipoC,
            usuario_id: <?php echo $_SESSION['id']; ?>,
            cliente_id: idP === '#' ? 9897 : parseInt(idP),
            monto_original: montoO,
            monto_venta_final: montoF,
            // ── Campos tributarios totales ──
            op_gravadas: parseFloat(imp.gravadas.toFixed(2)),
            igv: parseFloat(imp.igv.toFixed(2)),
            op_exoneradas: parseFloat(imp.exoneradas.toFixed(2)),
            op_inafectas: parseFloat(imp.inafectas.toFixed(2)),
            icbper: parseFloat(imp.icbper.toFixed(2))
        };

        $.ajax({
            url: 'logica/clssInsertPA.php',
            type: 'POST',
            data: {
                accion: 'FINALIZARVENTARAPIDO',
                jsDatosVenta: JSON.stringify(venta),
                js_articulos: JSON.stringify(obtenerArticulos()),
                js_detalle_pago: JSON.stringify(fps)
            },
            success: r => {
                try {
                    const res = JSON.parse(r);
                    if (res.estado) {
                        /**
                         * 
                         */
                        Swal.fire({
                                title: 'Venta Exitosa',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            })
                            .then(() => {
                                //window.open(`ticket.php?id=${res.id_venta_generado}`, '_blank'); location.reload();
                                location.reload();
                                console.log("Ubillus KCHUDO")
                            });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.mensaje
                        });
                    }
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al procesar la respuesta'
                    });
                }
            },
            error: () => Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error en la comunicación con el servidor'
            })
        });
    }

    /* ============================================================
       PAGO CRÉDITO
       ============================================================ */
    function fn_pagar_credito() {
        const idP = document.getElementById('idPersona').textContent.trim();
        if (idP === '#') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debes seleccionar un cliente para venta a crédito',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        const fd = $('#form-pago-credito').serializeArray();
        const montoO = parseFloat(document.getElementById('montoTotal').value);
        const montoF = parseFloat(document.getElementById('montoTotalFinal').value) || montoO;
        let dd = [],
            mi = 0,
            fp = null;

        fd.forEach(i => {
            if (i.name === 'formaPagoCredito[]') {
                fp = i.value;
            } else if (i.name === 'montoCredito[]') {
                const m = parseFloat(i.value) || 0;
                if (fp && m > 0) {
                    dd.push({
                        id_forma_pago: fp,
                        monto_forma_pago: m
                    });
                    mi += m;
                    fp = null;
                }
            }
        });

        // Resumen tributario total
        const imp = calcularImpuestos();

        const venta = {
            usuario_id: <?php echo $_SESSION['id']; ?>,
            cliente_id: parseInt(idP),
            monto_original: montoO,
            monto_venta_final: montoF,
            monto_inicial: mi,
            // ── Campos tributarios totales ──
            op_gravadas: parseFloat(imp.gravadas.toFixed(2)),
            igv: parseFloat(imp.igv.toFixed(2)),
            op_exoneradas: parseFloat(imp.exoneradas.toFixed(2)),
            op_inafectas: parseFloat(imp.inafectas.toFixed(2)),
            icbper: parseFloat(imp.icbper.toFixed(2))
        };

        $.ajax({
            url: 'logica/clssInsertPA.php',
            type: 'POST',
            data: {
                accion: 'FINALIZARVENTACREDITORAPIDO',
                jsDatosVenta: JSON.stringify(venta),
                js_articulos: JSON.stringify(obtenerArticulos()),
                js_detalle_deuda: dd.length > 0 ? JSON.stringify(dd) : null
            },
            success: r => {
                try {
                    const res = JSON.parse(r);
                    if (res.estado) {
                        Swal.fire({
                                title: 'Crédito Registrado',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            })
                            .then(() => {
                                //window.open(`ticket.php?id=${res.id_venta_generado}`, '_blank'); location.reload(); 
                                location.reload();
                                console.log("Ubillus KCHUDO")
                            });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.mensaje
                        });
                    }
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al procesar la respuesta'
                    });
                }
            },
            error: () => Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error en la comunicación con el servidor'
            })
        });
    }

    /* ============================================================
       CLIENTE
       ============================================================ */
    function initClienteModal() {
        document.getElementById('btnAbrirModalCliente').addEventListener('click', () => new bootstrap.Modal(document.getElementById('modalCliente')).show());
        document.getElementById('btnBuscarDNI').addEventListener('click', buscarDNI);
        document.getElementById('btnBuscarRUC').addEventListener('click', buscarRUC);
        document.getElementById('btnRegistrarCliente').addEventListener('click', registrarCliente);
        document.getElementById('nombreCliente').addEventListener('input', buscarCliente);
    }
    async function buscarDNI() {
        const dni = document.getElementById('numeroDocumentoPersona').value.trim();
        if (dni.length !== 8) {
            Swal.fire({
                icon: 'warning',
                title: 'DNI inválido',
                text: 'Ingresa 8 dígitos',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }
        try {
            const r = await fetch(`https://graphperu.daustinn.com/api/query/${dni}`);
            const d = await r.json();
            if (d && d.names) {
                document.getElementById('nombresPersona').value = d.names;
                document.getElementById('apellidosPersona').value = d.surnames;
                Swal.fire({
                    icon: 'success',
                    title: 'DNI encontrado',
                    text: d.fullName,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        } catch (e) {
            Swal.fire({
                icon: 'warning',
                title: 'No encontrado',
                text: 'Ingresa los datos manualmente',
                timer: 2000,
                showConfirmButton: false
            });
        }
    }
    async function buscarRUC() {
        const ruc = document.getElementById('numeroDocumentoEmpresa').value.trim();
        if (ruc.length !== 11) {
            Swal.fire({
                icon: 'warning',
                title: 'RUC inválido',
                text: 'Ingresa 11 dígitos',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }
        try {
            const r = await fetch(`https://graphperu.daustinn.com/api/query/${ruc}`);
            const d = await r.json();
            if (d && d.name) {
                document.getElementById('razonSocial').value = d.name;
                document.getElementById('nombreComercial').value = d.name;
                Swal.fire({
                    icon: 'success',
                    title: 'RUC encontrado',
                    text: d.name,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        } catch (e) {
            Swal.fire({
                icon: 'warning',
                title: 'No encontrado',
                text: 'Ingresa los datos manualmente',
                timer: 2000,
                showConfirmButton: false
            });
        }
    }

    function buscarCliente() {
        const q = document.getElementById('nombreCliente').value.trim();
        const s = document.getElementById('sugerencias');
        if (!q.length) {
            s.innerHTML = '';
            return;
        }
        $.ajax({
            method: 'POST',
            url: 'logica/clssFiltro.php',
            data: {
                accion: 'FILTROPERSONA',
                data: q,
                sucursal_id: <?php echo $sucursal_id; ?>
            },
            success: r => {
                try {
                    const arr = JSON.parse(r);
                    s.innerHTML = '';
                    arr.forEach(p => {
                        const item = document.createElement('div');
                        item.className = 'list-group-item list-group-item-action';
                        item.textContent = p.persona_concatenada;
                        item.onclick = () => {
                            document.getElementById('nombreCliente').value = p.persona_concatenada;
                            document.getElementById('idPersona').textContent = p.id;
                            document.getElementById('idUpdateNumTelefonoCliente').value = p.telefonomovil || '';
                            document.getElementById('idUpdateCorreoCliente').value = p.email || '';
                            s.innerHTML = '';
                        };
                        s.appendChild(item);
                    });
                } catch (e) {}
            }
        });
    }

    function registrarCliente() {
        const esP = document.getElementById('pills-persona-tab').classList.contains('active');
        if (esP) {
            const dni = document.getElementById('numeroDocumentoPersona').value.trim();
            const nom = document.getElementById('nombresPersona').value.trim();
            const ape = document.getElementById('apellidosPersona').value.trim();
            if (!dni || !nom || !ape) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos requeridos',
                    text: 'Completa los campos obligatorios',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
            registrarPersona({
                numero_documento: dni,
                nombres: nom,
                apellidos: ape,
                telefono_movil: document.getElementById('telefonoPersona').value || null,
                email: document.getElementById('emailPersona').value || null
            });
        } else {
            const ruc = document.getElementById('numeroDocumentoEmpresa').value.trim();
            const rz = document.getElementById('razonSocial').value.trim();
            const nc = document.getElementById('nombreComercial').value.trim();
            if (!ruc || !rz || !nc) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos requeridos',
                    text: 'Completa los campos obligatorios',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
            registrarPersona({
                numero_documento: ruc,
                razon_social: rz,
                nombre_comercial: nc,
                telefono_movil: document.getElementById('telefonoEmpresa').value || null,
                email: document.getElementById('emailEmpresa').value || null
            });
        }
    }

    function registrarPersona(datos) {
        $.ajax({
            method: 'POST',
            url: 'logica/clssPersona.php',
            data: {
                accion: 'REGISTRARPERSONARAPIDO',
                data: JSON.stringify(datos)
            },
            success: r => {
                try {
                    const res = JSON.parse(r);
                    if (res.success) {
                        const id = res.persona_id || res.empresa_id;
                        const nombre = datos.nombres ? `${datos.numero_documento} - ${datos.nombres} ${datos.apellidos}` : `${datos.numero_documento} - ${datos.razon_social}`;
                        document.getElementById('idPersona').textContent = id;
                        document.getElementById('nombreCliente').value = nombre;
                        bootstrap.Modal.getInstance(document.getElementById('modalCliente')).hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Cliente registrado',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message
                        });
                    }
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al procesar la respuesta'
                    });
                }
            }
        });
    }

    /* ============================================================
       INIT
       ============================================================ */
    function initEventListeners() {
        initSoloCorteModal();
        initImpresion3DModal();
        initPagoModal();
        initClienteModal();
    }
</script>

<?php include("pie.php"); ?>