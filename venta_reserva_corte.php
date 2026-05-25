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
    content: "📋";
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
    overflow: visible !important;
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

  #tabla_articulos th:nth-child(1),
  #tabla_articulos td:nth-child(1),
  #tabla_articulos th:nth-child(7),
  #tabla_articulos td:nth-child(7),
  #tabla_articulos th:nth-child(8),
  #tabla_articulos td:nth-child(8) {
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

  #seccionCorte {
    background: var(--bg-card);
    border: 1.5px solid var(--border-soft);
    border-radius: 14px;
    padding: 16px;
    margin-top: 12px;
  }

  /* === QTY CONTROL (modales) === */
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

  /* === BADGE RESERVA en resumen === */
  .reserva-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
    padding: 8px 14px;
    font-size: .78rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
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

<style>
  /* === PRECIOS POR MAYOR === */
  .precio-mayor-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
  }

  .precio-mayor-pill {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
    background: white;
    border: 2px solid var(--border-soft);
    border-radius: 14px;
    padding: 8px 14px;
    cursor: pointer;
    transition: all .2s;
    min-width: 90px;
  }

  .precio-mayor-pill:hover {
    border-color: var(--accent);
    box-shadow: 0 3px 10px rgba(102, 126, 234, .15);
    transform: translateY(-1px);
  }

  .precio-mayor-pill.active {
    background: var(--gradient-main);
    border-color: transparent;
    color: white;
    box-shadow: 0 4px 14px rgba(0, 51, 160, .3);
    transform: translateY(-1px);
  }

  .precio-mayor-pill .pill-nombre {
    font-weight: 700;
    font-size: .78rem;
    text-transform: uppercase;
    letter-spacing: .3px;
  }

  .precio-mayor-pill .pill-precio {
    font-weight: 800;
    font-size: .92rem;
  }

  .precio-mayor-pill.active .pill-nombre,
  .precio-mayor-pill.active .pill-precio {
    color: white;
  }
</style>
<div class="container">
  <div class="page-inner">

    <!-- HEADER -->
    <div class="venta-header">
      <h3><i class="fas fa-bookmark me-2"></i> Venta por Reserva</h3>
      <p>Registra pedidos enviados por WhatsApp. Reservas para recoger después.</p>
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
                  <label>Precio (S/)</label>
                  <input type="number" id="manualPrecio" class="form-control mt-1" step="0.01" min="0" placeholder="0.00">
                </div>
              </div>
              <button class="btn-primary-custom mt-1" onclick="agregarManual()">
                <i class="fas fa-plus me-1"></i> Agregar a la Reserva
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
              Detalle de Reserva
              <span class="badge ms-1" style="background:#f0f3ff;color:var(--primary);font-size:.75rem;" id="contadorItems">0 ítems</span>
            </h6>
            <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="limpiarVenta()" style="font-size:.78rem;">
              <i class="fas fa-trash me-1"></i>Limpiar
            </button>
          </div>

          <div id="emptyStateVenta" class="empty-state">
            <div class="empty-icon">📋</div>
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

          <!-- Badge indicador de reserva -->
          <div class="reserva-badge">
            <i class="fas fa-bookmark"></i> Modo Reserva — Pago al recoger
          </div>

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
            <div class="label-total">TOTAL RESERVA</div>
            <div class="monto-total" id="id_subtotal_general_display">S/ 0.00</div>
            <input type="hidden" id="id_subtotal_general" value="0.00">
          </div>

          <div class="d-grid gap-2 mt-2">
            <button class="btn-success-custom" id="btnRealizarPago" disabled onclick="abrirModalReserva()">
              <i class="fas fa-bookmark me-1"></i> Confirmar Reserva
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
        <!-- SECCIÓN PRECIOS POR MAYOR -->
        <div id="seccionPreciosMayor" style="display:none;">
          <hr style="border-color:var(--border-soft);">
          <div class="text-center mb-1">
            <label class="fw-bold d-block mb-2" style="font-size:.85rem;color:var(--primary);">
              <i class="fas fa-boxes me-1" style="color:var(--accent);"></i>
              Presentación / Precio al por mayor
            </label>
            <div id="pillsPreciosMayor" class="precio-mayor-pills"></div>
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

<!-- =============== MODAL: CONFIRMAR RESERVA =============== -->
<div class="modal fade" id="modalRealizarPago" tabindex="-1" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header modal-header-success">
        <div class="w-100 text-center">
          <h5 class="mb-1 fw-bold"><i class="fas fa-bookmark me-2"></i>Confirmar Reserva</h5>
          <h2 class="mb-0 fw-bold">S/ <span id="idMontoVentaTitulo">0.00</span></h2>
        </div>
      </div>
      <div class="modal-body p-4">
        <div class="mb-3"><small style="color:var(--text-muted);">ID Cliente: <strong><span id="idPersona">#</span></strong> &nbsp;|&nbsp; Usuario: <strong><span id="idUsuario"><?php echo $id_usuario_s ?></span></strong></small></div>

        <!-- CLIENTE -->
        <div class="mb-3">
          <label class="fw-bold" style="font-size:.83rem;color:var(--primary);">
            <i class="fas fa-user-tie me-1" style="color:var(--accent);"></i>Cliente <span class="text-danger">*</span>
          </label>
          <div class="input-group mt-1">
            <input type="text" class="form-control" id="nombreCliente" placeholder="Buscar cliente por nombre o DNI">
            <button type="button" class="btn" id="btnAbrirModalCliente" style="background:var(--gradient-main);color:white;border:none;border-radius:0 10px 10px 0;"><i class="fas fa-user-plus"></i></button>
          </div>
          <div id="sugerencias" class="list-group position-absolute" style="width:calc(100% - 64px);z-index:1055;"></div>
        </div>

        <!-- MONTO -->
        <div class="mb-3">
          <label class="fw-bold" style="font-size:.83rem;color:var(--primary);">Monto Total (S/)</label>
          <div class="input-group mt-1">
            <span class="input-group-text" style="background:#f0f3ff;border:1.5px solid var(--border-soft);border-radius:10px 0 0 10px;">S/</span>
            <input type="text" class="form-control fw-bold" id="montoTotal" readonly style="border:1.5px solid var(--border-soft);font-size:1.1rem;">
          </div>
        </div>

        <!-- INFO reserva -->
        <div class="p-3" style="background:#e8f5e9;border-left:4px solid var(--success);border-radius:10px;font-size:.85rem;">
          <i class="fas fa-info-circle me-1" style="color:var(--success);"></i>
          Esta reserva quedará <strong>pendiente de pago</strong> hasta que el cliente la recoja.
        </div>

        <div class="text-center mt-4">
          <button class="btn-success-custom" id="btnConfirmarReserva" style="width:auto;padding:12px 36px;font-size:1rem;">
            <i class="fas fa-bookmark me-2"></i>Registrar Reserva
          </button>
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
  const products = <?php echo json_encode(listarProductosVenta2($sucursal_id)); ?>;
  const unidadesCompra = <?php echo json_encode(listarUndiadesDeCompra($sucursal_id)); ?>;
  const mapaUnidades = {};
  unidadesCompra.forEach(u => {
    mapaUnidades[u.id] = {
      presentacion: u.presentacion,
      cantidad: u.cantidad_numero
    };
  });

  document.addEventListener('DOMContentLoaded', () => {
    populateFilters();
    initEventListeners();
  });

  /* --- SWITCH TABS --- */
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

  /* --- FILTROS --- */
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
    const filtered = products.filter(p => (!cat || p.categoria === cat) && (!tip || p.tipo === tip) && (!dim || p.dimension === dim) && (!col || p.color === col) && (!q || p.articulo.toLowerCase().includes(q) || (p.categoria || '').toLowerCase().includes(q) || (p.tipo || '').toLowerCase().includes(q)));
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
      div.innerHTML = `<div><div class="prod-nombre">${p.articulo}${sinStock?'<span class="sin-stock-badge ms-1">Sin stock</span>':''}</div><div class="prod-info">${p.categoria||''} ${p.tipo?'· '+p.tipo:''} · Stock: ${p.stock}</div></div><div class="prod-precio">S/ ${parseFloat(p.precio_venta).toFixed(2)}</div>`;
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

  /* --- AGREGAR MANUAL --- */
  function agregarManual() {
    const desc = document.getElementById('manualDescripcion').value.trim();
    const cant = parseInt(document.getElementById('manualCantidad').value) || 1;
    const precio = parseFloat(document.getElementById('manualPrecio').value) || 0;
    if (!desc) {
      Swal.fire({ icon: 'warning', title: 'Falta descripción', text: 'Ingresa el nombre del artículo.', confirmButtonText: 'Ok' });
      return;
    }
    agregarATabla([{ id: '0', articulo: desc, cantidad: cant, precio_unitario: precio, subtotal: cant * precio, idmovimiento: 1, nota: '' }]);
    document.getElementById('manualDescripcion').value = '';
    document.getElementById('manualCantidad').value = 1;
    document.getElementById('manualPrecio').value = '';
    showNotification && showNotification("success");
  }

  /* --- MODAL CANTIDAD --- */
  function fn_agregar_venta(articulo) {
    const modal = new bootstrap.Modal(document.getElementById('modalCantidad'));
    document.getElementById('nombreArticulo').textContent = articulo.articulo;
    document.getElementById('inputCantidad').value = 1;
    document.getElementById('cantidadCorte').value = 0;
    document.getElementById('precioCorte').value = articulo.corte ? 1.5 : 0;
    document.getElementById('idTextAreaDetalleInsert').value = '';
    const sc = document.getElementById('seccionCorte');
    sc.style.display = articulo.corte ? 'block' : 'none';

    let precioSeleccionado = parseFloat(articulo.precio_venta);
    let nombrePresentacionSeleccionada = 'Unidad';
    const seccionPrecios = document.getElementById('seccionPreciosMayor');
    const contenedorPills = document.getElementById('pillsPreciosMayor');
    contenedorPills.innerHTML = '';

    let precios = [];
    try {
      precios = typeof articulo.precios_json === 'string' ? JSON.parse(articulo.precios_json) : (articulo.precios_json || []);
    } catch (e) { precios = []; }

    if (precios.length > 0) {
      seccionPrecios.style.display = 'block';
      const pillNormal = document.createElement('button');
      pillNormal.type = 'button';
      pillNormal.className = 'precio-mayor-pill active';
      pillNormal.dataset.precio = articulo.precio_venta;
      pillNormal.dataset.nombre = 'Unidad';
      pillNormal.dataset.cantidad = 1;
      pillNormal.innerHTML = `<span class="pill-nombre">PRECIO NORMAL</span><span class="pill-precio">S/ ${parseFloat(articulo.precio_venta).toFixed(2)}</span>`;
      contenedorPills.appendChild(pillNormal);
      precios.forEach(p => {
        const unidad = mapaUnidades[p.unidadescompra_id];
        const nombreUnidad = unidad ? unidad.presentacion : `ID ${p.unidadescompra_id}`;
        const cantUnidad = unidad ? parseInt(unidad.cantidad) : 1;
        const pill = document.createElement('button');
        pill.type = 'button';
        pill.className = 'precio-mayor-pill';
        pill.dataset.precio = p.precio;
        pill.dataset.nombre = nombreUnidad;
        pill.dataset.cantidad = cantUnidad;
        pill.innerHTML = `<span class="pill-nombre">${nombreUnidad}</span><span class="pill-precio">S/ ${parseFloat(p.precio).toFixed(2)}</span><span style="font-size:.68rem;opacity:.7;">(${cantUnidad} unid.)</span>`;
        contenedorPills.appendChild(pill);
      });
      contenedorPills.querySelectorAll('.precio-mayor-pill').forEach(pill => {
        pill.addEventListener('click', () => {
          contenedorPills.querySelectorAll('.precio-mayor-pill').forEach(p => p.classList.remove('active'));
          pill.classList.add('active');
          precioSeleccionado = parseFloat(pill.dataset.precio);
          nombrePresentacionSeleccionada = pill.dataset.nombre;
          document.getElementById('inputCantidad').value = parseInt(pill.dataset.cantidad) || 1;
        });
      });
    } else {
      seccionPrecios.style.display = 'none';
    }

    document.getElementById('btnRestarCantidad').onclick = () => {
      let c = parseInt(document.getElementById('inputCantidad').value);
      if (c > 1) {
        document.getElementById('inputCantidad').value = c - 1;
        if (c - 1 === 1 && articulo.corte) { sc.style.display = 'block'; document.getElementById('precioCorte').value = 1.5; }
        else if (c - 1 > 1) sc.style.display = 'none';
      }
    };
    document.getElementById('btnSumarCantidad').onclick = () => {
      let c = parseInt(document.getElementById('inputCantidad').value);
      if (c + 1 > articulo.stock) {
        Swal.fire({ icon: 'warning', title: 'Stock insuficiente', text: `Solo hay ${articulo.stock} unidades`, timer: 2000, showConfirmButton: false });
      } else {
        document.getElementById('inputCantidad').value = c + 1;
        if (c + 1 > 1) { sc.style.display = 'none'; document.getElementById('cantidadCorte').value = 0; document.getElementById('precioCorte').value = 0; }
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
        Swal.fire({ icon: 'warning', title: 'Stock insuficiente', text: `Solo hay ${articulo.stock} unidades`, timer: 2000, showConfirmButton: false });
        return;
      }
      const cantPillActual = parseInt(contenedorPills.querySelector('.precio-mayor-pill.active').dataset.cantidad) || 1;
      const precioUnitario = precioSeleccionado / cantPillActual;
      const subtotalArticulo = cant * precioUnitario;
      const subtotalCorte = min * pCorte;
      const nombreFinal = articulo.articulo + (nombrePresentacionSeleccionada !== 'Unidad' ? ` [${nombrePresentacionSeleccionada}]` : '') + (nota ? ` - ${nota}` : '');
      agregarATabla([{ id: articulo.id, articulo: nombreFinal, cantidad: cant, precio_unitario: precioUnitario, subtotal: subtotalArticulo + subtotalCorte, idmovimiento: 1, nota, stock: articulo.stock }]);
      modal.hide();
      document.getElementById('resultadosProductos').style.display = 'none';
      document.getElementById('searchInput').value = '';
      const hint = document.getElementById('searchHint');
      if (hint) hint.style.display = 'block';
      showNotification && showNotification("success");
    };

    modal.show();
  }

  /* --- TABLA --- */
  function agregarATabla(datos) {
    const tbody = document.getElementById('tabla_articulos').getElementsByTagName('tbody')[0];
    datos.forEach(item => {
      const cantidadEsNumero = !isNaN(parseInt(item.cantidad)) && item.cantidad !== '-';
      const stockMax = item.stock !== undefined ? parseInt(item.stock) : Infinity;
      if (item.id !== '0' && item.id !== 0 && cantidadEsNumero) {
        const filas = tbody.querySelectorAll('tr');
        let duplicado = null;
        filas.forEach(f => { if (f.cells[0].textContent == item.id) duplicado = f; });
        if (duplicado) {
          const qtyEl = duplicado.cells[2].querySelector('.qty-inline-val');
          const precioUnit = parseFloat(duplicado.cells[3].textContent) || null;
          const stockGuardado = parseInt(duplicado.dataset.stock) || Infinity;
          if (qtyEl) {
            const actual = parseInt(qtyEl.textContent);
            const nueva = actual + parseInt(item.cantidad);
            if (nueva > stockGuardado) {
              Swal.fire({ icon: 'warning', title: 'Stock insuficiente', html: `Solo hay <b>${stockGuardado}</b> unidades disponibles.<br>Ya tienes <b>${actual}</b> en la reserva.`, timer: 2500, showConfirmButton: false });
              return;
            }
            qtyEl.textContent = nueva;
            recalcularFila(duplicado, precioUnit);
            duplicado.style.transition = 'background .2s';
            duplicado.style.background = '#e8f5e9';
            setTimeout(() => { duplicado.style.background = ''; }, 600);
            showNotification && showNotification("success");
          }
          return;
        }
      }
      const fila = tbody.insertRow();
      fila.className = 'fade-in-row';
      fila.dataset.stock = stockMax;
      fila.insertCell(0).textContent = item.id;
      const tdArt = fila.insertCell(1);
      tdArt.textContent = item.articulo;
      tdArt.setAttribute('data-label', 'Artículo');
      const tdC = fila.insertCell(2);
      tdC.setAttribute('data-label', 'Cant.');
      if (cantidadEsNumero) {
        const cantInit = parseInt(item.cantidad);
        const precioUnit = parseFloat(item.precio_unitario) || null;
        tdC.innerHTML = `<div class="qty-inline"><button class="qty-inline-btn qty-inline-minus" title="Restar">−</button><span class="qty-inline-val">${cantInit}</span><button class="qty-inline-btn qty-inline-plus" title="Sumar">+</button></div>`;
        const btnMinus = tdC.querySelector('.qty-inline-minus');
        const btnPlus = tdC.querySelector('.qty-inline-plus');
        const valEl = tdC.querySelector('.qty-inline-val');
        btnMinus.addEventListener('click', () => {
          let v = parseInt(valEl.textContent);
          if (v > 1) { valEl.textContent = v - 1; recalcularFila(fila, precioUnit); }
        });
        btnPlus.addEventListener('click', () => {
          let v = parseInt(valEl.textContent);
          const stockActual = parseInt(fila.dataset.stock) || Infinity;
          if (v >= stockActual) {
            Swal.fire({ icon: 'warning', title: 'Stock máximo alcanzado', text: `Solo hay ${stockActual} unidades disponibles.`, timer: 2000, showConfirmButton: false });
            return;
          }
          valEl.textContent = v + 1;
          recalcularFila(fila, precioUnit);
        });
      } else {
        tdC.textContent = item.cantidad;
      }
      const tdP = fila.insertCell(3);
      tdP.textContent = item.precio_unitario;
      tdP.setAttribute('data-label', 'P. Unit.');
      const tdS = fila.insertCell(4);
      tdS.className = 'col-subtotal';
      tdS.textContent = 'S/ ' + parseFloat(item.subtotal).toFixed(2);
      tdS.setAttribute('data-label', 'Subtotal');
      const tdA = fila.insertCell(5);
      tdA.setAttribute('data-label', 'Acc.');
      tdA.style.textAlign = 'center';
      const btn = document.createElement('button');
      btn.className = 'btn-eliminar-fila';
      btn.innerHTML = '<i class="fas fa-trash-alt"></i>';
      btn.onclick = () => {
        Swal.fire({ title: '¿Eliminar?', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d', confirmButtonText: 'Sí', cancelButtonText: 'Cancelar' })
          .then(r => { if (r.isConfirmed) { fila.remove(); calcularTotales(); showNotification && showNotification("success"); } });
      };
      tdA.appendChild(btn);
      fila.insertCell(6).textContent = item.idmovimiento;
      fila.insertCell(7).textContent = item.nota || '';
    });
    calcularTotales();
  }

  function recalcularFila(fila, precioUnit) {
    if (precioUnit === null || isNaN(precioUnit)) { calcularTotales(); return; }
    const valEl = fila.cells[2].querySelector('.qty-inline-val');
    if (!valEl) { calcularTotales(); return; }
    const nuevaCant = parseInt(valEl.textContent) || 1;
    fila.cells[4].textContent = 'S/ ' + (nuevaCant * precioUnit).toFixed(2);
    calcularTotales();
  }

  function calcularTotales() {
    const filas = document.querySelectorAll('#tabla_articulos tbody tr');
    let totalArt = 0, totalGen = 0, totalUnid = 0;
    filas.forEach(f => {
      const qtyEl = f.cells[2].querySelector('.qty-inline-val');
      const cant = parseFloat(qtyEl ? qtyEl.textContent : f.cells[2].textContent) || 0;
      const precio = parseFloat(f.cells[3].textContent) || 0;
      const sub = parseFloat((f.cells[4].textContent || '').replace('S/ ', '')) || 0;
      totalArt += cant * precio;
      totalGen += sub;
      if (!isNaN(cant)) totalUnid += cant;
    });
    document.getElementById('id_subtotal_articulos').textContent = 'S/ ' + totalArt.toFixed(2);
    document.getElementById('id_subtotal_general').value = totalGen.toFixed(2);
    document.getElementById('resumenItems').textContent = filas.length;
    document.getElementById('resumenCantidad').textContent = totalUnid + ' unid.';
    const el = document.getElementById('id_subtotal_general_display');
    el.textContent = 'S/ ' + totalGen.toFixed(2);
    el.classList.remove('pulse-total');
    void el.offsetWidth;
    el.classList.add('pulse-total');
    const hay = filas.length > 0;
    document.getElementById('btnRealizarPago').disabled = !hay;
    document.getElementById('emptyStateVenta').style.display = hay ? 'none' : 'block';
    document.getElementById('wrapperTablaVenta').style.display = hay ? 'block' : 'none';
    document.getElementById('contadorItems').textContent = filas.length + (filas.length === 1 ? ' ítem' : ' ítems');
  }

  function limpiarVenta() {
    const filas = document.querySelectorAll('#tabla_articulos tbody tr');
    if (!filas.length) return;
    Swal.fire({ title: '¿Limpiar reserva?', text: 'Se eliminarán todos los artículos.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d', confirmButtonText: 'Sí, limpiar', cancelButtonText: 'Cancelar' })
      .then(r => { if (r.isConfirmed) { document.querySelector('#tabla_articulos tbody').innerHTML = ''; calcularTotales(); } });
  }

  function abrirModalReserva() {
    const total = document.getElementById('id_subtotal_general').value;
    document.getElementById('montoTotal').value = total;
    document.getElementById('idMontoVentaTitulo').textContent = total;
    new bootstrap.Modal(document.getElementById('modalRealizarPago')).show();
  }

  /* --- SOLO CORTE --- */
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
      if (min <= 0) { Swal.fire({ icon: 'warning', title: 'Error', text: 'Ingresa minutos válidos', timer: 2000, showConfirmButton: false }); return; }
      agregarATabla([{ id: '0', articulo: 'SOLO CORTE', cantidad: '-', precio_unitario: '-', subtotal: min * tar, idmovimiento: 6, nota: '' }]);
      bootstrap.Modal.getInstance(document.getElementById('modalSoloCorte')).hide();
      showNotification && showNotification("success");
    });
  }

  /* --- IMPRESIÓN 3D --- */
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
      if (min <= 0) { Swal.fire({ icon: 'warning', title: 'Error', text: 'Ingresa minutos válidos', timer: 2000, showConfirmButton: false }); return; }
      agregarATabla([{ id: '0', articulo: 'IMPRESIÓN 3D' + (nota ? ` - ${nota}` : ''), cantidad: '-', precio_unitario: '-', subtotal: min * tar, idmovimiento: 15, nota: '' }]);
      bootstrap.Modal.getInstance(document.getElementById('modalSoloCorteMaquina2')).hide();
      showNotification && showNotification("success");
    });
  }

  function fnAumentoOrResta(a) {
    let v = parseInt(document.getElementById('cantidad_solocortev2').value);
    document.getElementById('cantidad_solocortev2').value = a === '+' ? v + 1 : (v > 1 ? v - 1 : v);
  }
  function fnAumentarMin(m) { document.getElementById('cantidad_solocortev2').value = m; }
  function fnAumentaPrecioImpresion(m) {
    let p = parseFloat(document.getElementById('precioSoloCortev2').value);
    document.getElementById('precioSoloCortev2').value = (p + m).toFixed(2);
  }
  function limpiar() { document.getElementById('precioSoloCortev2').value = 0; }

  /* --- SERVICIOS GENÉRICOS --- */
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
    if (monto <= 0) { Swal.fire({ icon: 'warning', title: 'Error', text: 'Ingresa un monto válido', timer: 2000, showConfirmButton: false }); return; }
    const art = dims ? `${nombre} (${dims})${detalle?' - '+detalle:''}` : nombre + (detalle ? ' - ' + detalle : '');
    agregarATabla([{ id: '0', articulo: art, cantidad: cant, precio_unitario: '-', subtotal: monto, idmovimiento: idMov, nota: '' }]);
    bootstrap.Modal.getInstance(document.getElementById('modalGenerico')).hide();
    showNotification && showNotification("success");
  }

  /* ============================================================
     CONFIRMAR RESERVA — con confirmación Sí/No
     ============================================================ */
  function initReservaModal() {
    // CAMBIO 1: async añadido al listener
    document.getElementById('btnConfirmarReserva').addEventListener('click', async () => {
      const idCliente = document.getElementById('idPersona').textContent.trim();
      const total = document.getElementById('montoTotal').value;

      if (idCliente === '#') {
        Swal.fire({ icon: 'warning', title: 'Cliente requerido', text: 'Selecciona o registra un cliente para la reserva', timer: 2000, showConfirmButton: false });
        document.getElementById('nombreCliente').focus();
        return;
      }

      const articulos = Array.from(document.querySelectorAll('#tabla_articulos tbody tr')).map(row => {
        const qtyEl = row.cells[2].querySelector('.qty-inline-val');
        const cantRaw = qtyEl ? qtyEl.textContent : row.cells[2].textContent;
        return {
          articulo_id: row.cells[0].textContent === '0' ? null : parseInt(row.cells[0].textContent),
          minutos: 0,
          costoxminuto: 0,
          precio_unitario: isNaN(parseFloat(row.cells[3].textContent)) ? null : parseFloat(row.cells[3].textContent),
          cantidad: isNaN(parseInt(cantRaw)) ? null : parseInt(cantRaw),
          sub_total: parseFloat((row.cells[4].textContent || '').replace('S/ ', '')) || 0,
          movimiento_id: parseInt(row.cells[6].textContent),
          nota_archivo: row.cells[1].textContent + (row.cells[7].textContent ? ' / ' + row.cells[7].textContent : ''),
          sucursal_id: <?php echo $sucursal_id; ?>
        };
      });

      const datos = {
        usuario_id: <?php echo $_SESSION['id']; ?>,
        cliente_id: idCliente,
        total,
        sucursal_id: <?php echo $sucursal_id; ?>,
        articulos
      };

      // CAMBIO 2: confirmación antes de procesar
      const confirmResult = await Swal.fire({
        title: '¿Confirmar reserva?',
        html: `<div style="font-size:.95rem;">
          <p class="mb-1">Total: <strong style="color:#11998e;font-size:1.1rem;">S/ ${parseFloat(total).toFixed(2)}</strong></p>
          <p class="mb-0 text-muted" style="font-size:.82rem;">¿Deseas registrar esta reserva?</p>
        </div>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check me-1"></i>Sí, Adelante',
        cancelButtonText: '<i class="fas fa-times me-1"></i>No, Nueva Venta',
        confirmButtonColor: '#11998e',
        cancelButtonColor: '#dc3545',
        reverseButtons: true
      });

      if (!confirmResult.isConfirmed) {
        if (confirmResult.isDismissed && confirmResult.dismiss === Swal.DismissReason.cancel) {
          // "No, Nueva Venta" → cerrar modal y limpiar todo
          bootstrap.Modal.getInstance(document.getElementById('modalRealizarPago')).hide();
          document.querySelector('#tabla_articulos tbody').innerHTML = '';
          calcularTotales();
          document.getElementById('nombreCliente').value = '';
          document.getElementById('idPersona').textContent = '#';
        }
        return;
      }

      // CAMBIO 3: el resto igual que antes
      const btnR = document.getElementById('btnConfirmarReserva');
      btnR.disabled = true;
      btnR.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Procesando...';

      $.ajax({
        method: 'POST',
        url: 'logica/clssVentaCorte.php',
        data: {
          accion: 'REGISTRARRESERVA',
          data: JSON.stringify(datos)
        },
        success: r => {
          try {
            const res = JSON.parse(r);
            if (res.success) {
              Swal.fire({ icon: 'success', title: '¡Reserva registrada!', text: 'La reserva quedó pendiente de pago.', timer: 1500, showConfirmButton: false })
                .then(() => location.reload());
            } else {
              Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'No se pudo registrar la reserva' });
            }
          } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error al procesar la respuesta' });
          }
          btnR.disabled = false;
          btnR.innerHTML = '<i class="fas fa-bookmark me-2"></i>Registrar Reserva';
        },
        error: () => {
          Swal.fire({ icon: 'error', title: 'Error', text: 'Error en la comunicación con el servidor' });
          btnR.disabled = false;
          btnR.innerHTML = '<i class="fas fa-bookmark me-2"></i>Registrar Reserva';
        }
      });
    });
  }

  /* --- CLIENTE --- */
  function initClienteModal() {
    document.getElementById('btnAbrirModalCliente').addEventListener('click', () => new bootstrap.Modal(document.getElementById('modalCliente')).show());
    document.getElementById('btnBuscarDNI').addEventListener('click', buscarDNI);
    document.getElementById('btnBuscarRUC').addEventListener('click', buscarRUC);
    document.getElementById('btnRegistrarCliente').addEventListener('click', registrarCliente);
    document.getElementById('nombreCliente').addEventListener('input', buscarCliente);
    document.addEventListener('click', e => {
      const s = document.getElementById('sugerencias');
      const n = document.getElementById('nombreCliente');
      if (s && !s.contains(e.target) && !n.contains(e.target)) s.innerHTML = '';
    });
  }

  async function buscarDNI() {
    const dni = document.getElementById('numeroDocumentoPersona').value.trim();
    if (dni.length !== 8) { Swal.fire({ icon: 'warning', title: 'DNI inválido', text: 'Ingresa 8 dígitos', timer: 2000, showConfirmButton: false }); return; }
    try {
      const r = await fetch(`https://graphperu.daustinn.com/api/query/${dni}`);
      const d = await r.json();
      if (d && d.names) {
        document.getElementById('nombresPersona').value = d.names;
        document.getElementById('apellidosPersona').value = d.surnames;
        Swal.fire({ icon: 'success', title: 'DNI encontrado', text: d.fullName, timer: 2000, showConfirmButton: false });
      }
    } catch (e) {
      Swal.fire({ icon: 'warning', title: 'No encontrado', text: 'Ingresa los datos manualmente', timer: 2000, showConfirmButton: false });
    }
  }

  async function buscarRUC() {
    const ruc = document.getElementById('numeroDocumentoEmpresa').value.trim();
    if (ruc.length !== 11) { Swal.fire({ icon: 'warning', title: 'RUC inválido', text: 'Ingresa 11 dígitos', timer: 2000, showConfirmButton: false }); return; }
    try {
      const r = await fetch(`https://graphperu.daustinn.com/api/query/${ruc}`);
      const d = await r.json();
      if (d && d.name) {
        document.getElementById('razonSocial').value = d.name;
        document.getElementById('nombreComercial').value = d.name;
        Swal.fire({ icon: 'success', title: 'RUC encontrado', text: d.name, timer: 2000, showConfirmButton: false });
      }
    } catch (e) {
      Swal.fire({ icon: 'warning', title: 'No encontrado', text: 'Ingresa los datos manualmente', timer: 2000, showConfirmButton: false });
    }
  }

  function buscarCliente() {
    const q = document.getElementById('nombreCliente').value.trim();
    const s = document.getElementById('sugerencias');
    s.innerHTML = '';
    if (!q.length) return;
    $.ajax({
      method: 'POST',
      url: 'logica/clssFiltro.php',
      data: { accion: 'FILTROPERSONA', data: q, sucursal_id: <?php echo $sucursal_id; ?> },
      success: r => {
        try {
          const arr = JSON.parse(r);
          arr.forEach(p => {
            const item = document.createElement('div');
            item.className = 'list-group-item list-group-item-action';
            item.textContent = p.persona_concatenada;
            item.onclick = () => {
              document.getElementById('nombreCliente').value = p.persona_concatenada;
              document.getElementById('idPersona').textContent = p.id;
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
      if (!dni || !nom || !ape) { Swal.fire({ icon: 'warning', title: 'Campos requeridos', text: 'Completa los campos obligatorios', timer: 2000, showConfirmButton: false }); return; }
      registrarPersona({ numero_documento: dni, nombres: nom, apellidos: ape, telefono_movil: document.getElementById('telefonoPersona').value || null, email: document.getElementById('emailPersona').value || null });
    } else {
      const ruc = document.getElementById('numeroDocumentoEmpresa').value.trim();
      const rz = document.getElementById('razonSocial').value.trim();
      const nc = document.getElementById('nombreComercial').value.trim();
      if (!ruc || !rz || !nc) { Swal.fire({ icon: 'warning', title: 'Campos requeridos', text: 'Completa los campos obligatorios', timer: 2000, showConfirmButton: false }); return; }
      registrarPersona({ numero_documento: ruc, razon_social: rz, nombre_comercial: nc, telefono_movil: document.getElementById('telefonoEmpresa').value || null, email: document.getElementById('emailEmpresa').value || null });
    }
  }

  function registrarPersona(datos) {
    $.ajax({
      method: 'POST',
      url: 'logica/clssPersona.php',
      data: { accion: 'REGISTRARPERSONARAPIDO', data: JSON.stringify(datos) },
      success: r => {
        try {
          const res = JSON.parse(r);
          if (res.success) {
            const id = res.persona_id || res.empresa_id;
            const nombre = datos.nombres ? `${datos.numero_documento} - ${datos.nombres} ${datos.apellidos}` : `${datos.numero_documento} - ${datos.razon_social}`;
            document.getElementById('idPersona').textContent = id;
            document.getElementById('nombreCliente').value = nombre;
            bootstrap.Modal.getInstance(document.getElementById('modalCliente')).hide();
            Swal.fire({ icon: 'success', title: 'Cliente registrado', timer: 1500, showConfirmButton: false });
          } else Swal.fire({ icon: 'error', title: 'Error', text: res.message });
        } catch (e) {
          Swal.fire({ icon: 'error', title: 'Error', text: 'Error al procesar la respuesta' });
        }
      }
    });
  }

  function initEventListeners() {
    initSoloCorteModal();
    initImpresion3DModal();
    initReservaModal();
    initClienteModal();
  }
</script>

<?php include("pie.php"); ?>