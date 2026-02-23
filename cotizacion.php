<?php
//cotizacion.php
include("cabecera.php");
include("logica/clssVenta.php");
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
        --gradient-main: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-success: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        --shadow-card: 0 4px 20px rgba(42,47,91,0.10);
        --shadow-hover: 0 8px 30px rgba(102,126,234,0.18);
    }

    /* ===== LAYOUT ===== */
    .cotizacion-header {
        background: var(--gradient-main);
        border-radius: 18px;
        color: white;
        padding: 28px 32px 22px;
        margin-bottom: 28px;
        box-shadow: var(--shadow-hover);
        position: relative;
        overflow: hidden;
    }
    .cotizacion-header::before {
        content: "📚";
        position: absolute;
        right: 28px;
        top: 18px;
        font-size: 3.5rem;
        opacity: 0.18;
        pointer-events: none;
    }
    .cotizacion-header h3 {
        font-weight: 800;
        letter-spacing: -0.5px;
        margin-bottom: 4px;
    }
    .cotizacion-header p {
        opacity: 0.85;
        margin-bottom: 0;
        font-size: 0.97rem;
    }

    /* ===== ESTADO BADGE ===== */
    .badge-estado {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge-pendiente  { background: #fff3cd; color: #856404; }
    .badge-aprobada   { background: #d1fae5; color: #065f46; }
    .badge-convertida { background: #dbeafe; color: #1e40af; }
    .badge-cancelada  { background: #fee2e2; color: #991b1b; }

    /* ===== PANEL IZQUIERDO — búsqueda/agregar ===== */
    .panel-busqueda {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-card);
        padding: 24px 20px;
        height: 100%;
    }
    .panel-busqueda .section-title {
        font-weight: 700;
        color: var(--primary);
        font-size: 1rem;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .panel-busqueda .section-title i {
        color: var(--accent);
    }

    /* ===== TABS MODO AGREGAR ===== */
    .add-mode-tabs { display: flex; gap: 6px; margin-bottom: 14px; }
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
    .add-mode-tab:hover { border-color: var(--accent); color: var(--accent); }
    .add-mode-tab.active {
        background: var(--gradient-main);
        border-color: transparent;
        color: white;
        box-shadow: 0 3px 10px rgba(102,126,234,.3);
    }
    .add-panel { display: none; }
    .add-panel.active { display: block; }

    /* ===== BUSCADOR ARTÍCULO ===== */
    .search-wrapper { position: relative; }
    #searchArticulo {
        border: 2px solid var(--border-soft);
        border-radius: 12px;
        padding: 10px 42px 10px 16px;
        font-size: 0.9rem;
        transition: border-color .2s;
        width: 100%;
    }
    #searchArticulo:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(102,126,234,.12);
        outline: none;
    }
    .search-icon {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--accent);
        font-size: .9rem;
        pointer-events: none;
    }
    #resultadosBusqueda {
        max-height: 260px;
        overflow-y: auto;
        border: 1.5px solid var(--border-soft);
        border-radius: 12px;
        margin-top: 6px;
        background: white;
        box-shadow: 0 6px 20px rgba(0,0,0,.10);
        position: absolute;
        width: 100%;
        z-index: 500;
    }
    .resultado-item {
        padding: 10px 15px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f5;
        transition: background .15s;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .resultado-item:hover { background: #f0f3ff; }
    .resultado-item:last-child { border-bottom: none; }
    .resultado-item .articulo-nombre { font-weight: 600; font-size: .88rem; color: var(--primary); }
    .resultado-item .articulo-info   { font-size: .75rem; color: var(--text-muted); }
    .resultado-item .articulo-precio { font-weight: 700; color: var(--success); font-size: .9rem; white-space: nowrap; margin-left: 8px; }

    /* ===== ARTÍCULO MANUAL ===== */
    .form-manual label { font-size: .85rem; font-weight: 600; color: var(--primary); }
    .form-manual .form-control {
        border-radius: 10px;
        border: 1.5px solid var(--border-soft);
        font-size: .9rem;
        padding: 8px 12px;
    }
    .form-manual .form-control:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(102,126,234,.12);
    }

    /* ===== TABLA COTIZACIÓN ===== */
    .tabla-cotizacion-wrapper {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-card);
        padding: 20px;
    }
    #tablaCotizacion { width: 100%; }
    #tablaCotizacion thead tr { background: var(--primary); color: white; }
    #tablaCotizacion thead th {
        padding: 11px 14px;
        font-size: .82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        white-space: nowrap;
    }
    #tablaCotizacion thead th:first-child { border-radius: 8px 0 0 8px; }
    #tablaCotizacion thead th:last-child  { border-radius: 0 8px 8px 0; }
    #tablaCotizacion tbody tr {
        border-bottom: 1px solid #f0f3ff;
        transition: background .15s;
    }
    #tablaCotizacion tbody tr:hover { background: #f8f9ff; }
    #tablaCotizacion tbody td { padding: 10px 14px; font-size: .88rem; vertical-align: middle; }
    .col-num  { width: 36px; text-align: center; color: var(--text-muted); font-size: .8rem; }
    .col-desc { font-weight: 600; color: var(--primary); }
    .col-desc .desc-cat { font-size: .75rem; color: var(--text-muted); font-weight: 400; }
    .col-cant input {
        width: 70px;
        text-align: center;
        border: 1.5px solid var(--border-soft);
        border-radius: 8px;
        padding: 5px 4px;
        font-weight: 600;
    }
    .col-precio input {
        width: 90px;
        border: 1.5px solid var(--border-soft);
        border-radius: 8px;
        padding: 5px 8px;
        font-weight: 600;
    }
    .col-subtotal { font-weight: 700; color: var(--success); font-size: .93rem; }
    .col-accion button { padding: 5px 10px; border-radius: 8px; font-size: .8rem; }

    /* ===== PANEL DERECHO — resumen y cliente ===== */
    .panel-resumen {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-card);
        padding: 22px 20px;
    }
    .resumen-linea {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #f0f3ff;
        font-size: .9rem;
    }
    .resumen-linea:last-child { border-bottom: none; }
    .resumen-linea .lbl { color: var(--text-muted); font-weight: 500; }
    .resumen-linea .val { font-weight: 700; color: var(--primary); }
    .resumen-total-grande {
        background: var(--gradient-main);
        border-radius: 14px;
        padding: 14px 18px;
        color: white;
        text-align: center;
        margin: 16px 0 10px;
    }
    .resumen-total-grande .label-total { font-size: .85rem; opacity: .85; }
    .resumen-total-grande .monto-total  { font-size: 2.1rem; font-weight: 800; letter-spacing: -1px; }

    /* ===== LISTA DE COTIZACIONES GUARDADAS ===== */
    .cotizacion-card {
        border: 1.5px solid var(--border-soft);
        border-radius: 14px;
        padding: 14px 16px;
        margin-bottom: 12px;
        transition: all .2s;
        cursor: pointer;
        background: white;
    }
    .cotizacion-card:hover { border-color: var(--accent); box-shadow: var(--shadow-hover); }
    .cotizacion-card.activa { border-color: var(--accent); background: #f5f7ff; }
    .cotizacion-card .cc-header { display: flex; justify-content: space-between; align-items: flex-start; }
    .cotizacion-card .cc-codigo { font-weight: 800; color: var(--primary); font-size: .95rem; }
    .cotizacion-card .cc-cliente { font-size: .82rem; color: var(--text-muted); margin-top: 3px; }
    .cotizacion-card .cc-monto   { font-weight: 700; color: var(--success); font-size: 1.05rem; }
    .cotizacion-card .cc-fecha   { font-size: .75rem; color: var(--text-muted); margin-top: 6px; }

    /* ===== BOTONES ===== */
    .btn-primary-custom {
        background: var(--gradient-main);
        border: none;
        color: white;
        border-radius: 12px;
        padding: 10px 20px;
        font-weight: 700;
        font-size: .92rem;
        transition: all .25s;
        box-shadow: 0 3px 12px rgba(102,126,234,.3);
    }
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102,126,234,.4);
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
        box-shadow: 0 3px 12px rgba(17,153,142,.25);
    }
    .btn-success-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(17,153,142,.35);
        color: white;
    }
    .btn-warning-custom {
        background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
        border: none;
        color: #2a2f5b;
        border-radius: 12px;
        padding: 10px 20px;
        font-weight: 700;
        font-size: .92rem;
        transition: all .25s;
    }
    .btn-warning-custom:hover { transform: translateY(-2px); color: #2a2f5b; }

    /* ===== CLIENTE BUSCADOR ===== */
    #buscadorCliente {
        border: 2px solid var(--border-soft);
        border-radius: 12px;
        padding: 9px 14px;
        font-size: .9rem;
        transition: border-color .2s;
        width: 100%;
    }
    #buscadorCliente:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(102,126,234,.12);
        outline: none;
    }
    #sugerenciasCliente {
        max-height: 180px;
        overflow-y: auto;
        border: 1.5px solid var(--border-soft);
        border-radius: 10px;
        background: white;
        box-shadow: 0 4px 14px rgba(0,0,0,.09);
        position: absolute;
        width: calc(100% - 24px);
        z-index: 200;
    }
    #sugerenciasCliente .sug-item {
        padding: 9px 14px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f5;
        font-size: .87rem;
        transition: background .12s;
    }
    #sugerenciasCliente .sug-item:hover { background: #f0f3ff; }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-muted);
    }
    .empty-state .empty-icon { font-size: 3rem; margin-bottom: 10px; opacity: .45; }
    .empty-state p { font-size: .9rem; }

    /* ===== DESCUENTO ===== */
    .descuento-wrapper {
        background: #fff8e1;
        border: 1.5px solid #ffe082;
        border-radius: 12px;
        padding: 12px 16px;
        margin: 10px 0;
    }
    .descuento-wrapper label { font-weight: 700; font-size: .85rem; color: #856404; }
    .descuento-wrapper .form-control {
        border-radius: 8px;
        border: 1.5px solid #ffe082;
        background: white;
        font-weight: 600;
    }

    /* ===== TABS SUPERIOR ===== */
    .nav-cotizacion .nav-link {
        border-radius: 10px;
        font-weight: 600;
        font-size: .88rem;
        color: var(--text-muted);
        padding: 8px 18px;
        transition: all .2s;
        border: none;
    }
    .nav-cotizacion .nav-link.active {
        background: var(--gradient-main);
        color: white;
        box-shadow: 0 3px 10px rgba(102,126,234,.3);
    }
    .nav-cotizacion .nav-link:not(.active):hover {
        background: #f0f3ff;
        color: var(--primary);
    }

    /* ===== NOTAS ===== */
    #notasCotizacion {
        border: 1.5px solid var(--border-soft);
        border-radius: 10px;
        font-size: .88rem;
        resize: vertical;
        min-height: 60px;
    }
    #notasCotizacion:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(102,126,234,.10);
        outline: none;
    }

    /* ===== ANIMACIONES ===== */
    .fade-in-row { animation: fadeInRow .3s ease; }
    @keyframes fadeInRow {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .pulse-total { animation: pulseTotal .35s ease; }
    @keyframes pulseTotal {
        0%,100% { transform: scale(1); }
        50%      { transform: scale(1.05); }
    }

    /* ===== MODAL CONVERSIÓN ===== */
    #modalConvertir .modal-header {
        background: var(--gradient-success);
        color: white;
        border-bottom: none;
    }
    #modalConvertir .btn-close { filter: brightness(0) invert(1); }

    /* ===== RESPONSIVE ===== */
    @media(max-width: 768px) {
        .cotizacion-header { padding: 18px 16px; }
        .cotizacion-header::before { display: none; }
        #tablaCotizacion thead { display: none; }
        #tablaCotizacion tbody td {
            display: block;
            text-align: right;
            padding: 6px 12px;
        }
        #tablaCotizacion tbody td::before {
            content: attr(data-label);
            float: left;
            font-weight: 600;
            color: var(--text-muted);
            font-size: .8rem;
        }
        #tablaCotizacion tbody tr { margin-bottom: 12px; border: 1.5px solid var(--border-soft); border-radius: 10px; }
    }
</style>

<div class="container-fluid">
    <div class="page-inner">

        <!-- HEADER -->
        <div class="cotizacion-header">
            <h3><i class="fas fa-file-invoice-dollar me-2"></i> Cotizador de Listas Escolares</h3>
            <p>Genera cotizaciones rápidas para útiles escolares. Si el cliente acepta, convierte a venta con un clic.</p>
        </div>

        <!-- TABS PRINCIPALES -->
        <ul class="nav nav-cotizacion mb-3" id="tabsCotizacion" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="tab-nueva-tab" data-bs-toggle="pill"
                        data-bs-target="#tab-nueva" type="button">
                    <i class="fas fa-plus me-1"></i> Nueva Cotización
                </button>
            </li>
            <li class="nav-item ms-2">
                <button class="nav-link" id="tab-historial-tab" data-bs-toggle="pill"
                        data-bs-target="#tab-historial" type="button">
                    <i class="fas fa-history me-1"></i> Historial
                    <span class="badge bg-secondary ms-1" id="badgeHistorial">0</span>
                </button>
            </li>
        </ul>

        <div class="tab-content">

            <!-- ============================================================
                 TAB: NUEVA COTIZACIÓN
            ============================================================ -->
            <div class="tab-pane fade show active" id="tab-nueva" role="tabpanel">
                <div class="row g-3">

                    <!-- ── COLUMNA IZQUIERDA: búsqueda y agregar ── -->
                    <div class="col-lg-4">
                        <div class="panel-busqueda">

                            <!-- DATOS COTIZACIÓN -->
                            <div class="section-title"><i class="fas fa-file-alt"></i> Datos de la Cotización</div>
                            <div class="mb-2">
                                <label class="form-label fw-bold" style="font-size:.82rem;">
                                    <i class="fas fa-hashtag text-secondary me-1"></i>Código
                                </label>
                                <input type="text" id="codigoCotizacion" class="form-control" readonly
                                       placeholder="Se genera automáticamente"
                                       style="border-radius:10px;font-size:.83rem;background:#f8f9ff;color:var(--text-muted);">
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold" style="font-size:.82rem;">
                                    <i class="fas fa-tag text-secondary me-1"></i>Nombre de la Lista
                                </label>
                                <input type="text" id="nombreLista" class="form-control"
                                       placeholder="Ej: Lista 4to Primaria — Colegio San Juan"
                                       style="border-radius:10px;font-size:.85rem;">
                            </div>

                            <!-- CLIENTE -->
                            <div class="mb-2 position-relative">
                                <label class="form-label fw-bold" style="font-size:.82rem;">
                                    <i class="fas fa-user text-secondary me-1"></i>Cliente <span style="font-weight:400;color:var(--text-muted);">(opcional)</span>
                                </label>
                                <input type="text" id="buscadorCliente" placeholder="Buscar cliente por nombre o DNI..."
                                       style="border-radius:10px;font-size:.85rem;">
                                <div id="sugerenciasCliente" style="display:none;"></div>
                                <input type="hidden" id="clienteSeleccionadoId">
                                <div id="clienteSeleccionadoInfo" class="mt-1" style="display:none;">
                                    <span class="d-flex align-items-center gap-2"
                                          style="background:#e8f5e9;color:#1b5e20;font-size:.81rem;padding:6px 12px;border-radius:8px;font-weight:600;">
                                        <i class="fas fa-user-check"></i>
                                        <span id="clienteSeleccionadoNombre" style="flex:1;"></span>
                                        <button onclick="limpiarCliente()" class="btn-close"
                                                style="font-size:.55rem;" aria-label="Quitar cliente"></button>
                                    </span>
                                </div>
                            </div>

                            <hr style="border-color:#f0f3ff;margin:14px 0;">

                            <!-- TABS MODO AGREGAR -->
                            <div class="add-mode-tabs">
                                <button class="add-mode-tab active" id="tabBuscar" onclick="switchAddMode('buscar')">
                                    <i class="fas fa-search me-1"></i>Buscar Artículo
                                </button>
                                <button class="add-mode-tab" id="tabManual" onclick="switchAddMode('manual')">
                                    <i class="fas fa-pencil-alt me-1"></i>Ingresar Manual
                                </button>
                            </div>

                            <!-- PANEL: BUSCAR EN SISTEMA -->
                            <div class="add-panel active" id="panelBuscar">
                                <div class="search-wrapper">
                                    <input type="text" id="searchArticulo"
                                           placeholder="Escribe nombre, categoría o tipo...">
                                    <i class="fas fa-search search-icon"></i>
                                </div>
                                <div id="resultadosBusqueda"></div>
                                <div id="searchHint" class="mt-2 text-center"
                                     style="font-size:.78rem;color:var(--text-muted);padding:18px 0;">
                                    <i class="fas fa-keyboard me-1" style="color:var(--accent);"></i>
                                    Escribe para buscar entre tus productos
                                </div>
                            </div>

                            <!-- PANEL: AGREGAR MANUAL -->
                            <div class="add-panel" id="panelManual">
                                <div class="form-manual">
                                    <div class="mb-2">
                                        <label>Descripción <span class="text-danger">*</span></label>
                                        <input type="text" id="manualDescripcion" class="form-control"
                                               placeholder="Ej: Cuaderno 100 hojas rayado">
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <label>Cantidad</label>
                                            <input type="number" id="manualCantidad" class="form-control"
                                                   value="1" min="1">
                                        </div>
                                        <div class="col-6">
                                            <label>Precio Unit. (S/)</label>
                                            <input type="number" id="manualPrecio" class="form-control"
                                                   step="0.01" min="0" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label>Categoría (opcional)</label>
                                        <input type="text" id="manualCategoria" class="form-control"
                                               placeholder="Ej: Cuadernos, Colores...">
                                    </div>
                                    <button class="btn btn-primary-custom w-100 mt-1" onclick="agregarManual()">
                                        <i class="fas fa-plus me-1"></i> Agregar a la Lista
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- ── COLUMNA CENTRO: tabla de items ── -->
                    <div class="col-lg-5">
                        <div class="tabla-cotizacion-wrapper">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0" style="color:var(--primary);">
                                    <i class="fas fa-list-ul me-1 text-accent" style="color:var(--accent);"></i>
                                    Ítems de la Cotización
                                    <span class="badge ms-1" style="background:#f0f3ff;color:var(--primary);font-size:.75rem;"
                                          id="contadorItems">0 items</span>
                                </h6>
                                <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="limpiarTodo()"
                                        id="btnLimpiarTodo" style="font-size:.78rem;">
                                    <i class="fas fa-trash me-1"></i>Limpiar
                                </button>
                            </div>

                            <div id="emptyStateCotizacion" class="empty-state">
                                <div class="empty-icon">🛒</div>
                                <p>Aún no hay artículos.<br>Busca o agrega manualmente.</p>
                            </div>

                            <div class="table-responsive" id="wrapperTabla" style="display:none;">
                                <table id="tablaCotizacion">
                                    <thead>
                                        <tr>
                                            <th class="col-num">#</th>
                                            <th>Descripción</th>
                                            <th style="width:80px;text-align:center;">Cant.</th>
                                            <th style="width:100px;">P. Unit.</th>
                                            <th style="width:90px;">Subtotal</th>
                                            <th style="width:60px;text-align:center;">Acc.</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyCotizacion">
                                    </tbody>
                                </table>
                            </div>

                            <!-- NOTAS -->
                            <div class="mt-3" id="seccionNotas" style="display:none;">
                                <label class="fw-bold" style="font-size:.84rem;color:var(--primary);">
                                    <i class="fas fa-sticky-note me-1 text-warning"></i>Notas / Observaciones
                                </label>
                                <textarea id="notasCotizacion" class="form-control mt-1"
                                          placeholder="Ej: Cliente necesita todo para el lunes..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- ── COLUMNA DERECHA: resumen ── -->
                    <div class="col-lg-3">
                        <div class="panel-resumen">
                            <h6 class="fw-bold mb-3" style="color:var(--primary);">
                                <i class="fas fa-calculator me-1" style="color:var(--accent);"></i> Resumen
                            </h6>

                            <div class="resumen-linea">
                                <span class="lbl">Total ítems</span>
                                <span class="val" id="resumenItems">0</span>
                            </div>
                            <div class="resumen-linea">
                                <span class="lbl">Cantidad total</span>
                                <span class="val" id="resumenCantidad">0 unid.</span>
                            </div>
                            <div class="resumen-linea">
                                <span class="lbl">Subtotal</span>
                                <span class="val" id="resumenSubtotal">S/ 0.00</span>
                            </div>

                            <!-- DESCUENTO -->
                            <div class="descuento-wrapper mt-2">
                                <label><i class="fas fa-percent me-1"></i>Descuento (%)</label>
                                <div class="d-flex gap-2 mt-1">
                                    <input type="number" id="descuentoPorcentaje" class="form-control form-control-sm"
                                           min="0" max="100" step="0.5" placeholder="0"
                                           oninput="recalcularTotales()">
                                    <input type="number" id="descuentoMonto" class="form-control form-control-sm"
                                           min="0" step="0.01" placeholder="S/ 0.00"
                                           oninput="recalcularDesdeDescuentoMonto()">
                                </div>
                                <small class="text-muted" style="font-size:.75rem;">
                                    % o monto fijo — el otro se actualiza solo
                                </small>
                            </div>

                            <div class="resumen-linea">
                                <span class="lbl">Descuento</span>
                                <span class="val text-danger" id="resumenDescuento">- S/ 0.00</span>
                            </div>

                            <div class="resumen-total-grande">
                                <div class="label-total">TOTAL COTIZACIÓN</div>
                                <div class="monto-total" id="resumenTotal">S/ 0.00</div>
                            </div>

                            <div class="d-grid gap-2 mt-2">
                                <button class="btn btn-primary-custom" onclick="guardarCotizacion()">
                                    <i class="fas fa-save me-1"></i> Guardar Cotización
                                </button>
                                <button class="btn btn-warning-custom" onclick="imprimirCotizacion()" id="btnImprimir" disabled>
                                    <i class="fas fa-print me-1"></i> Imprimir / PDF
                                </button>
                                <button class="btn btn-success-custom" onclick="abrirModalConvertir()" id="btnConvertir" disabled>
                                    <i class="fas fa-shopping-cart me-1"></i> Convertir a Venta
                                </button>
                            </div>

                            <!-- WHATSAPP -->
                            <div class="mt-3" id="seccionWhatsapp" style="display:none;">
                                <hr style="border-color:#f0f3ff;">
                                <label class="fw-bold" style="font-size:.83rem;color:var(--primary);">
                                    <i class="fab fa-whatsapp me-1" style="color:#25D366;"></i>Enviar por WhatsApp
                                </label>
                                <div class="d-flex gap-2 mt-1">
                                    <input type="text" id="telefonoWhatsapp" class="form-control form-control-sm"
                                           placeholder="9XXXXXXXX" maxlength="9"
                                           style="border-radius:8px;font-size:.85rem;">
                                    <button class="btn btn-sm fw-bold" onclick="enviarWhatsApp()"
                                            style="background:#25D366;color:white;border-radius:8px;white-space:nowrap;">
                                        <i class="fab fa-whatsapp"></i>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div><!-- /tab-nueva -->

            <!-- ============================================================
                 TAB: HISTORIAL
            ============================================================ -->
            <div class="tab-pane fade" id="tab-historial" role="tabpanel">
                <div class="row g-3">
                    <div class="col-lg-4">
                        <div style="background:white;border-radius:16px;box-shadow:var(--shadow-card);padding:20px;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0" style="color:var(--primary);">
                                    <i class="fas fa-list me-1" style="color:var(--accent);"></i> Cotizaciones Guardadas
                                </h6>
                                <button class="btn btn-sm btn-outline-danger rounded-pill"
                                        onclick="limpiarHistorial()" style="font-size:.78rem;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <!-- Filtro estado -->
                            <select id="filtroEstado" class="form-select form-select-sm mb-3"
                                    onchange="renderHistorial()" style="border-radius:10px;font-size:.85rem;">
                                <option value="">Todos los estados</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="aprobada">Aprobada</option>
                                <option value="convertida">Convertida a venta</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                            <div id="listaHistorial">
                                <div class="empty-state"><div class="empty-icon">📋</div><p>Sin cotizaciones guardadas.</p></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div id="detalleCotizacion"
                             style="background:white;border-radius:16px;box-shadow:var(--shadow-card);padding:22px;">
                            <div class="empty-state">
                                <div class="empty-icon">👈</div>
                                <p>Selecciona una cotización del panel izquierdo para ver su detalle.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /tab-historial -->

        </div><!-- /tab-content -->
    </div>
</div>


<!-- ============================================================
     MODAL: CONVERTIR A VENTA
============================================================ -->
<div class="modal fade" id="modalConvertir" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:18px;overflow:hidden;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-exchange-alt me-2"></i>Convertir Cotización a Venta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert" style="background:#e8f5e9;border-left:4px solid #4caf50;border-radius:10px;">
                    <strong><i class="fas fa-info-circle me-1 text-success"></i>¿Confirmar conversión?</strong>
                    <p class="mb-0 mt-1" style="font-size:.88rem;">
                        La cotización pasará al módulo de venta con todos los artículos precargados.
                        Desde allí podrás completar el pago normalmente.
                    </p>
                </div>

                <div class="p-3" style="background:#f8f9ff;border-radius:12px;margin-top:10px;">
                    <div class="d-flex justify-content-between mb-2">
                        <span style="font-size:.87rem;color:var(--text-muted);">Código cotización</span>
                        <span class="fw-bold" id="modalCodigo" style="color:var(--primary);">—</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span style="font-size:.87rem;color:var(--text-muted);">Ítems</span>
                        <span class="fw-bold" id="modalItemsCount" style="color:var(--primary);">—</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span style="font-size:.87rem;color:var(--text-muted);">Total</span>
                        <span class="fw-bold" id="modalTotalConvertir"
                              style="color:var(--success);font-size:1.1rem;">S/ —</span>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="fw-bold" style="font-size:.85rem;color:var(--primary);">
                        <i class="fas fa-sticky-note me-1 text-warning"></i>Notas adicionales para la venta
                    </label>
                    <textarea id="notasConversion" class="form-control mt-1"
                              rows="2" placeholder="Opcional..."
                              style="border-radius:10px;font-size:.87rem;"></textarea>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f0f3ff;">
                <button class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button class="btn btn-success-custom" onclick="confirmarConversion()">
                    <i class="fas fa-check me-1"></i>Confirmar — Ir a Venta
                </button>
            </div>
        </div>
    </div>
</div>


<!-- ============================================================
     MODAL: IMPRIMIR COTIZACIÓN
============================================================ -->
<div class="modal fade" id="modalImprimir" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:var(--primary);color:white;border-bottom:none;">
                <h5 class="modal-title fw-bold"><i class="fas fa-print me-2"></i>Vista Previa de Cotización</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        style="filter:brightness(0) invert(1);"></button>
            </div>
            <div class="modal-body p-0">
                <div id="contenidoImpresion" style="padding:32px;">
                    <!-- Se llena dinámicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cerrar</button>
                <button class="btn btn-primary-custom" onclick="window.print()">
                    <i class="fas fa-print me-1"></i>Imprimir
                </button>
            </div>
        </div>
    </div>
</div>


<!-- ============================================================
     ESTILOS DE IMPRESIÓN
============================================================ -->
<style>
@media print {
    body * { visibility: hidden; }
    #contenidoImpresion, #contenidoImpresion * { visibility: visible; }
    #contenidoImpresion { position: fixed; left: 0; top: 0; width: 100%; padding: 20px !important; }
    .modal-footer, .modal-header { display: none !important; }
}
</style>


<!-- ============================================================
     SCRIPTS
============================================================ -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/scriptNotify.js"></script>

<script>
/* ================================================================
   ESTADO GLOBAL
================================================================ */
let cotizacionActual = {
    id: null,
    codigo: '',
    nombreLista: '',
    cliente: null,
    items: [],
    descuentoPct: 0,
    descuentoMonto: 0,
    notas: '',
    estado: 'pendiente',
    fechaCreacion: null
};

let productosCache = <?php echo json_encode(listarProductosVenta1()); ?>;
let cotizacionesGuardadas = JSON.parse(localStorage.getItem('cotizaciones_escolares') || '[]');

/* ================================================================
   INICIALIZACIÓN
================================================================ */
document.addEventListener('DOMContentLoaded', () => {
    generarCodigo();
    renderHistorial();
    actualizarBadgeHistorial();
    initBuscadores();
    document.getElementById('nombreLista').addEventListener('input', () => {
        cotizacionActual.nombreLista = document.getElementById('nombreLista').value;
    });
});

/* ================================================================
   SWITCH MODO AGREGAR (Buscar / Manual)
================================================================ */
function switchAddMode(mode) {
    document.querySelectorAll('.add-mode-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.add-panel').forEach(p => p.classList.remove('active'));
    if (mode === 'buscar') {
        document.getElementById('tabBuscar').classList.add('active');
        document.getElementById('panelBuscar').classList.add('active');
        setTimeout(() => document.getElementById('searchArticulo').focus(), 50);
    } else {
        document.getElementById('tabManual').classList.add('active');
        document.getElementById('panelManual').classList.add('active');
        setTimeout(() => document.getElementById('manualDescripcion').focus(), 50);
    }
}

function generarCodigo() {
    const ts   = Date.now().toString(36).toUpperCase();
    const rand = Math.random().toString(36).substring(2, 5).toUpperCase();
    cotizacionActual.codigo = `COT-${ts}-${rand}`;
    cotizacionActual.fechaCreacion = new Date().toISOString();
    document.getElementById('codigoCotizacion').value = cotizacionActual.codigo;
}

/* ================================================================
   BUSCADORES
================================================================ */
function initBuscadores() {
    // ── Artículo ──
    const searchInput = document.getElementById('searchArticulo');
    const resultsBox  = document.getElementById('resultadosBusqueda');

    searchInput.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        resultsBox.innerHTML = '';
        const hint = document.getElementById('searchHint');
        if (!q) {
            resultsBox.style.display = 'none';
            if (hint) hint.style.display = 'block';
            return;
        }
        if (hint) hint.style.display = 'none';

        const matches = productosCache.filter(p =>
            p.articulo.toLowerCase().includes(q) ||
            (p.categoria && p.categoria.toLowerCase().includes(q)) ||
            (p.tipo && p.tipo.toLowerCase().includes(q))
        ).slice(0, 12);

        if (!matches.length) {
            resultsBox.innerHTML = '<div class="resultado-item"><span class="articulo-info">Sin resultados para "' + q + '"</span></div>';
            resultsBox.style.display = 'block'; return;
        }
        matches.forEach(p => {
            const div = document.createElement('div');
            div.className = 'resultado-item';
            const sinStock = parseFloat(p.stock) === 0;
            div.innerHTML = `
                <div>
                    <div class="articulo-nombre">${p.articulo}${sinStock ? ' <span style="color:#dc3545;font-size:.72rem;">(Sin stock)</span>' : ''}</div>
                    <div class="articulo-info">${p.categoria || ''} ${p.tipo ? '· ' + p.tipo : ''} · Stock: ${p.stock}</div>
                </div>
                <div class="articulo-precio">S/ ${parseFloat(p.precio_venta).toFixed(2)}</div>`;
            div.addEventListener('click', () => {
                agregarDesdeProducto(p);
                searchInput.value = '';
                resultsBox.style.display = 'none';
                resultsBox.innerHTML = '';
                if (hint) hint.style.display = 'block';
            });
            resultsBox.appendChild(div);
        });
        resultsBox.style.display = 'block';
    });

    document.addEventListener('click', e => {
        if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
            resultsBox.style.display = 'none';
            const hint = document.getElementById('searchHint');
            if (hint && !searchInput.value) hint.style.display = 'block';
        }
    });

    // ── Cliente ──
    const buscCliente = document.getElementById('buscadorCliente');
    const sugCliente  = document.getElementById('sugerenciasCliente');

    buscCliente.addEventListener('input', function () {
        const q = this.value.trim();
        if (q.length < 2) { sugCliente.style.display = 'none'; return; }
        $.ajax({
            method: 'POST',
            url: 'logica/clssFiltro.php',
            data: { accion: 'FILTROPERSONA', data: q }
        }).done(res => {
            try {
                const arr = JSON.parse(res);
                sugCliente.innerHTML = '';
                if (!arr.length) {
                    sugCliente.innerHTML = '<div class="sug-item text-muted">Sin resultados</div>';
                    sugCliente.style.display = 'block'; return;
                }
                arr.slice(0, 8).forEach(p => {
                    const d = document.createElement('div');
                    d.className = 'sug-item';
                    d.textContent = p.persona_concatenada;
                    d.addEventListener('click', () => {
                        seleccionarCliente(p);
                        buscCliente.value = '';
                        sugCliente.style.display = 'none';
                    });
                    sugCliente.appendChild(d);
                });
                sugCliente.style.display = 'block';
            } catch(e) { sugCliente.style.display = 'none'; }
        });
    });

    document.addEventListener('click', e => {
        if (!buscCliente.contains(e.target) && !sugCliente.contains(e.target))
            sugCliente.style.display = 'none';
    });
}

function seleccionarCliente(p) {
    cotizacionActual.cliente = p;
    document.getElementById('clienteSeleccionadoId').value    = p.id;
    document.getElementById('clienteSeleccionadoNombre').textContent = p.persona_concatenada;
    document.getElementById('clienteSeleccionadoInfo').style.display = 'block';
    document.getElementById('telefonoWhatsapp').value = p.telefonomovil || '';
    document.getElementById('seccionWhatsapp').style.display = 'block';
}

function limpiarCliente() {
    cotizacionActual.cliente = null;
    document.getElementById('clienteSeleccionadoId').value  = '';
    document.getElementById('clienteSeleccionadoNombre').textContent = '';
    document.getElementById('clienteSeleccionadoInfo').style.display = 'none';
    document.getElementById('seccionWhatsapp').style.display = 'none';
}

/* ================================================================
   AGREGAR ÍTEMS
================================================================ */
function agregarDesdeProducto(p) {
    // Si ya existe, aumentar cantidad
    const idx = cotizacionActual.items.findIndex(i => i.productoId === p.id);
    if (idx !== -1) {
        cotizacionActual.items[idx].cantidad++;
        renderTabla();
        recalcularTotales();
        return;
    }
    cotizacionActual.items.push({
        id: Date.now(),
        productoId: p.id,
        descripcion: p.articulo,
        categoria: p.categoria || '',
        cantidad: 1,
        precioUnit: parseFloat(p.precio_venta),
        esManual: false
    });
    renderTabla();
    recalcularTotales();
    showNotification && showNotification('success');
}

function agregarManual() {
    const desc  = document.getElementById('manualDescripcion').value.trim();
    const cant  = parseInt(document.getElementById('manualCantidad').value) || 1;
    const precio= parseFloat(document.getElementById('manualPrecio').value) || 0;
    const cat   = document.getElementById('manualCategoria').value.trim();

    if (!desc) {
        Swal.fire({ icon:'warning', title:'Falta descripción', text:'Ingresa el nombre del artículo.', confirmButtonText:'Ok' });
        return;
    }
    cotizacionActual.items.push({
        id: Date.now(),
        productoId: null,
        descripcion: desc,
        categoria: cat,
        cantidad: cant,
        precioUnit: precio,
        esManual: true
    });
    // Limpiar
    document.getElementById('manualDescripcion').value = '';
    document.getElementById('manualCantidad').value    = 1;
    document.getElementById('manualPrecio').value      = '';
    document.getElementById('manualCategoria').value   = '';

    renderTabla();
    recalcularTotales();
    showNotification && showNotification('success');
}

/* ================================================================
   RENDER TABLA
================================================================ */
function renderTabla() {
    const tbody   = document.getElementById('tbodyCotizacion');
    const empty   = document.getElementById('emptyStateCotizacion');
    const wrapper = document.getElementById('wrapperTabla');
    const notas   = document.getElementById('seccionNotas');

    if (!cotizacionActual.items.length) {
        empty.style.display   = 'block';
        wrapper.style.display = 'none';
        notas.style.display   = 'none';
        document.getElementById('contadorItems').textContent = '0 items';
        return;
    }
    empty.style.display   = 'none';
    wrapper.style.display = 'block';
    notas.style.display   = 'block';
    document.getElementById('contadorItems').textContent = cotizacionActual.items.length + ' items';

    tbody.innerHTML = '';
    cotizacionActual.items.forEach((item, idx) => {
        const sub = (item.cantidad * item.precioUnit).toFixed(2);
        const tr  = document.createElement('tr');
        tr.className = 'fade-in-row';
        tr.innerHTML = `
            <td class="col-num" data-label="#">${idx + 1}</td>
            <td class="col-desc" data-label="Artículo">
                ${item.descripcion}
                ${item.categoria ? `<div class="desc-cat">${item.categoria}</div>` : ''}
                ${item.esManual ? '<span class="badge bg-warning text-dark ms-1" style="font-size:.68rem;">Manual</span>' : ''}
            </td>
            <td class="col-cant" data-label="Cantidad">
                <input type="number" value="${item.cantidad}" min="1"
                       onchange="actualizarCantidad(${item.id}, this.value)"
                       class="form-control form-control-sm">
            </td>
            <td class="col-precio" data-label="P. Unit.">
                <div class="input-group input-group-sm">
                    <span class="input-group-text" style="font-size:.75rem;padding:3px 6px;">S/</span>
                    <input type="number" value="${item.precioUnit.toFixed(2)}" min="0" step="0.01"
                           onchange="actualizarPrecio(${item.id}, this.value)"
                           class="form-control">
                </div>
            </td>
            <td class="col-subtotal" data-label="Subtotal">S/ ${sub}</td>
            <td data-label="Acc." style="text-align:center;">
                <button class="btn btn-sm btn-outline-danger" onclick="eliminarItem(${item.id})"
                        title="Eliminar" style="border-radius:7px;padding:4px 9px;">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>`;
        tbody.appendChild(tr);
    });
}

function actualizarCantidad(id, val) {
    const item = cotizacionActual.items.find(i => i.id === id);
    if (item) { item.cantidad = Math.max(1, parseInt(val) || 1); renderTabla(); recalcularTotales(); }
}
function actualizarPrecio(id, val) {
    const item = cotizacionActual.items.find(i => i.id === id);
    if (item) { item.precioUnit = Math.max(0, parseFloat(val) || 0); renderTabla(); recalcularTotales(); }
}
function eliminarItem(id) {
    cotizacionActual.items = cotizacionActual.items.filter(i => i.id !== id);
    renderTabla();
    recalcularTotales();
}
function limpiarTodo() {
    if (!cotizacionActual.items.length) return;
    Swal.fire({
        title: '¿Limpiar lista?',
        text: 'Se eliminarán todos los ítems.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, limpiar',
        cancelButtonText: 'Cancelar'
    }).then(r => {
        if (r.isConfirmed) {
            cotizacionActual.items = [];
            renderTabla();
            recalcularTotales();
        }
    });
}

/* ================================================================
   TOTALES Y DESCUENTO
================================================================ */
function recalcularTotales() {
    const subtotal = cotizacionActual.items.reduce((a, i) => a + i.cantidad * i.precioUnit, 0);
    const pct      = parseFloat(document.getElementById('descuentoPorcentaje').value) || 0;
    const descto   = subtotal * (pct / 100);
    const total    = Math.max(0, subtotal - descto);

    cotizacionActual.descuentoPct   = pct;
    cotizacionActual.descuentoMonto = descto;

    document.getElementById('descuentoMonto').value = descto.toFixed(2);

    document.getElementById('resumenItems').textContent     = cotizacionActual.items.length;
    document.getElementById('resumenCantidad').textContent  =
        cotizacionActual.items.reduce((a, i) => a + i.cantidad, 0) + ' unid.';
    document.getElementById('resumenSubtotal').textContent  = 'S/ ' + subtotal.toFixed(2);
    document.getElementById('resumenDescuento').textContent = '- S/ ' + descto.toFixed(2);

    const elTotal = document.getElementById('resumenTotal');
    elTotal.textContent = 'S/ ' + total.toFixed(2);
    elTotal.classList.remove('pulse-total');
    void elTotal.offsetWidth;
    elTotal.classList.add('pulse-total');

    const habilitado = cotizacionActual.items.length > 0;
    document.getElementById('btnImprimir').disabled  = !habilitado;
    document.getElementById('btnConvertir').disabled = !habilitado;
    document.getElementById('btnLimpiarTodo').style.display = habilitado ? '' : 'none';
}

function recalcularDesdeDescuentoMonto() {
    const subtotal = cotizacionActual.items.reduce((a, i) => a + i.cantidad * i.precioUnit, 0);
    if (!subtotal) return;
    const monto = parseFloat(document.getElementById('descuentoMonto').value) || 0;
    const pct   = Math.min(100, (monto / subtotal) * 100);
    document.getElementById('descuentoPorcentaje').value = pct.toFixed(2);
    recalcularTotales();
}

/* ================================================================
   GUARDAR COTIZACIÓN
================================================================ */
function guardarCotizacion() {
    if (!cotizacionActual.items.length) {
        Swal.fire({ icon:'warning', title:'Lista vacía', text:'Agrega al menos un artículo.', confirmButtonText:'Ok' });
        return;
    }
    cotizacionActual.nombreLista = document.getElementById('nombreLista').value.trim() || 'Sin nombre';
    cotizacionActual.notas       = document.getElementById('notasCotizacion').value.trim();

    const subtotal = cotizacionActual.items.reduce((a, i) => a + i.cantidad * i.precioUnit, 0);
    const total    = Math.max(0, subtotal - cotizacionActual.descuentoMonto);
    cotizacionActual.total = total;

    // Actualizar o insertar
    const idx = cotizacionesGuardadas.findIndex(c => c.codigo === cotizacionActual.codigo);
    const snapshot = JSON.parse(JSON.stringify(cotizacionActual));
    if (idx !== -1) {
        cotizacionesGuardadas[idx] = snapshot;
    } else {
        cotizacionesGuardadas.unshift(snapshot);
    }
    localStorage.setItem('cotizaciones_escolares', JSON.stringify(cotizacionesGuardadas));

    actualizarBadgeHistorial();
    renderHistorial();

    Swal.fire({
        icon: 'success',
        title: '¡Cotización guardada!',
        html: `<strong>${cotizacionActual.codigo}</strong><br>Total: <strong>S/ ${total.toFixed(2)}</strong>`,
        timer: 2000,
        showConfirmButton: false
    });
}

/* ================================================================
   HISTORIAL
================================================================ */
function actualizarBadgeHistorial() {
    document.getElementById('badgeHistorial').textContent = cotizacionesGuardadas.length;
}

function renderHistorial() {
    const contenedor = document.getElementById('listaHistorial');
    const filtro     = document.getElementById('filtroEstado').value;
    const lista      = filtro
        ? cotizacionesGuardadas.filter(c => c.estado === filtro)
        : cotizacionesGuardadas;

    if (!lista.length) {
        contenedor.innerHTML = '<div class="empty-state"><div class="empty-icon">📋</div><p>Sin cotizaciones guardadas.</p></div>';
        return;
    }
    contenedor.innerHTML = '';
    lista.forEach(c => {
        const div = document.createElement('div');
        div.className = 'cotizacion-card';
        div.innerHTML = `
            <div class="cc-header">
                <div>
                    <div class="cc-codigo">${c.codigo}</div>
                    <div class="cc-cliente">
                        <i class="fas fa-user me-1" style="font-size:.7rem;"></i>
                        ${c.cliente ? c.cliente.persona_concatenada : 'Sin cliente'}
                    </div>
                </div>
                <div class="text-end">
                    <div class="cc-monto">S/ ${parseFloat(c.total || 0).toFixed(2)}</div>
                    <span class="badge-estado badge-${c.estado}">${labelEstado(c.estado)}</span>
                </div>
            </div>
            <div class="cc-fecha">
                <i class="fas fa-tag me-1"></i>${c.nombreLista || 'Sin nombre'}
                &nbsp;·&nbsp;
                <i class="fas fa-clock me-1"></i>${formatFecha(c.fechaCreacion)}
                &nbsp;·&nbsp;
                <span style="font-size:.72rem;">${c.items.length} ítems</span>
            </div>`;
        div.addEventListener('click', () => mostrarDetalleCotizacion(c));
        contenedor.appendChild(div);
    });
}

function mostrarDetalleCotizacion(c) {
    // Marcar activa
    document.querySelectorAll('.cotizacion-card').forEach(el => el.classList.remove('activa'));
    event.currentTarget.classList.add('activa');

    const det = document.getElementById('detalleCotizacion');
    const subtotal = c.items.reduce((a, i) => a + i.cantidad * i.precioUnit, 0);

    let filas = c.items.map((item, idx) => `
        <tr>
            <td style="padding:8px 12px;font-size:.82rem;color:#999;">${idx + 1}</td>
            <td style="padding:8px 12px;font-weight:600;font-size:.88rem;">
                ${item.descripcion}
                ${item.categoria ? `<div style="font-size:.72rem;color:#999;">${item.categoria}</div>` : ''}
            </td>
            <td style="padding:8px 12px;text-align:center;font-size:.88rem;">${item.cantidad}</td>
            <td style="padding:8px 12px;font-size:.88rem;">S/ ${item.precioUnit.toFixed(2)}</td>
            <td style="padding:8px 12px;font-weight:700;color:var(--success);font-size:.88rem;">
                S/ ${(item.cantidad * item.precioUnit).toFixed(2)}
            </td>
        </tr>`).join('');

    const botonesAccion = c.estado !== 'convertida' ? `
        <button class="btn btn-success-custom btn-sm me-2"
                onclick="cargarParaEditar('${c.codigo}')">
            <i class="fas fa-edit me-1"></i>Editar / Reabrir
        </button>
        <button class="btn btn-sm" onclick="cambiarEstado('${c.codigo}','cancelada')"
                style="background:#fee2e2;color:#991b1b;border-radius:10px;font-weight:700;font-size:.82rem;">
            <i class="fas fa-ban me-1"></i>Cancelar
        </button>` : `
        <span class="badge-estado badge-convertida" style="font-size:.85rem;padding:8px 16px;">
            <i class="fas fa-check me-1"></i>Ya fue convertida a venta
        </span>`;

    det.innerHTML = `
        <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1" style="color:var(--primary);">
                    ${c.codigo} &nbsp;
                    <span class="badge-estado badge-${c.estado}">${labelEstado(c.estado)}</span>
                </h5>
                <div style="font-size:.85rem;color:var(--text-muted);">
                    <i class="fas fa-tag me-1"></i>${c.nombreLista || 'Sin nombre'}
                    &nbsp;·&nbsp;
                    <i class="fas fa-calendar me-1"></i>${formatFecha(c.fechaCreacion)}
                    &nbsp;·&nbsp;
                    <i class="fas fa-user me-1"></i>${c.cliente ? c.cliente.persona_concatenada : 'Sin cliente asignado'}
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">${botonesAccion}</div>
        </div>
        <div class="table-responsive">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:var(--primary);color:white;">
                        <th style="padding:8px 12px;font-size:.78rem;border-radius:8px 0 0 0;">#</th>
                        <th style="padding:8px 12px;font-size:.78rem;">Descripción</th>
                        <th style="padding:8px 12px;font-size:.78rem;text-align:center;">Cant.</th>
                        <th style="padding:8px 12px;font-size:.78rem;">P. Unit.</th>
                        <th style="padding:8px 12px;font-size:.78rem;border-radius:0 8px 0 0;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>
        </div>
        <div class="mt-3 p-3" style="background:#f8f9ff;border-radius:12px;">
            <div class="d-flex justify-content-between"><span style="color:#999;font-size:.85rem;">Subtotal</span><span class="fw-bold">S/ ${subtotal.toFixed(2)}</span></div>
            <div class="d-flex justify-content-between"><span style="color:#999;font-size:.85rem;">Descuento (${c.descuentoPct || 0}%)</span><span class="fw-bold text-danger">- S/ ${parseFloat(c.descuentoMonto || 0).toFixed(2)}</span></div>
            <div class="d-flex justify-content-between mt-1 pt-2" style="border-top:1px solid #e3e6f5;">
                <span class="fw-bold" style="color:var(--primary);">TOTAL</span>
                <span class="fw-bold" style="color:var(--success);font-size:1.1rem;">S/ ${parseFloat(c.total || 0).toFixed(2)}</span>
            </div>
        </div>
        ${c.notas ? `<div class="mt-2 p-2" style="background:#fff8e1;border-radius:10px;font-size:.83rem;"><i class="fas fa-sticky-note me-1 text-warning"></i>${c.notas}</div>` : ''}`;
}

function cargarParaEditar(codigo) {
    const c = cotizacionesGuardadas.find(x => x.codigo === codigo);
    if (!c) return;
    cotizacionActual = JSON.parse(JSON.stringify(c));
    document.getElementById('codigoCotizacion').value = c.codigo;
    document.getElementById('nombreLista').value = c.nombreLista || '';
    document.getElementById('descuentoPorcentaje').value = c.descuentoPct || 0;
    document.getElementById('notasCotizacion').value = c.notas || '';
    if (c.cliente) seleccionarCliente(c.cliente);

    renderTabla();
    recalcularTotales();

    // Cambiar a tab nueva cotización
    document.getElementById('tab-nueva-tab').click();

    Swal.fire({
        icon: 'info',
        title: 'Cotización cargada',
        text: `Estás editando ${codigo}`,
        timer: 1800,
        showConfirmButton: false
    });
}

function cambiarEstado(codigo, nuevoEstado) {
    const idx = cotizacionesGuardadas.findIndex(c => c.codigo === codigo);
    if (idx === -1) return;
    cotizacionesGuardadas[idx].estado = nuevoEstado;
    localStorage.setItem('cotizaciones_escolares', JSON.stringify(cotizacionesGuardadas));
    renderHistorial();
    mostrarDetalleCotizacion(cotizacionesGuardadas[idx]);
}

function limpiarHistorial() {
    if (!cotizacionesGuardadas.length) return;
    Swal.fire({
        title: '¿Limpiar historial?',
        text: 'Se eliminarán TODAS las cotizaciones guardadas.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar todo',
        cancelButtonText: 'Cancelar'
    }).then(r => {
        if (r.isConfirmed) {
            cotizacionesGuardadas = [];
            localStorage.setItem('cotizaciones_escolares', JSON.stringify([]));
            renderHistorial();
            actualizarBadgeHistorial();
            document.getElementById('detalleCotizacion').innerHTML =
                '<div class="empty-state"><div class="empty-icon">👈</div><p>Selecciona una cotización.</p></div>';
        }
    });
}

/* ================================================================
   CONVERSIÓN A VENTA
================================================================ */
function abrirModalConvertir() {
    if (!cotizacionActual.items.length) return;
    const subtotal = cotizacionActual.items.reduce((a, i) => a + i.cantidad * i.precioUnit, 0);
    const total    = Math.max(0, subtotal - cotizacionActual.descuentoMonto);

    document.getElementById('modalCodigo').textContent      = cotizacionActual.codigo;
    document.getElementById('modalItemsCount').textContent  = cotizacionActual.items.length + ' ítems';
    document.getElementById('modalTotalConvertir').textContent = 'S/ ' + total.toFixed(2);

    new bootstrap.Modal(document.getElementById('modalConvertir')).show();
}

function confirmarConversion() {
    // Marcar como convertida en el historial
    const idx = cotizacionesGuardadas.findIndex(c => c.codigo === cotizacionActual.codigo);
    if (idx !== -1) {
        cotizacionesGuardadas[idx].estado = 'convertida';
        localStorage.setItem('cotizaciones_escolares', JSON.stringify(cotizacionesGuardadas));
    }

    // Guardar en sessionStorage para que la página de venta la recoja
    const notasConv = document.getElementById('notasConversion').value.trim();
    const payload = {
        codigo: cotizacionActual.codigo,
        cliente: cotizacionActual.cliente,
        items: cotizacionActual.items.map(i => ({
            ...i,
            nota: notasConv || ''
        })),
        total: parseFloat(document.getElementById('resumenTotal').textContent.replace('S/ ', ''))
    };
    sessionStorage.setItem('cotizacion_a_venta', JSON.stringify(payload));

    bootstrap.Modal.getInstance(document.getElementById('modalConvertir')).hide();

    Swal.fire({
        title: '¡Cotización convertida!',
        html: `Se precargará en la página de venta.<br><strong>${payload.items.length} artículos</strong> · Total: <strong>S/ ${payload.total.toFixed(2)}</strong>`,
        icon: 'success',
        confirmButtonText: '<i class="fas fa-shopping-cart me-1"></i>Ir a Ventas',
        confirmButtonColor: '#11998e'
    }).then(r => {
        if (r.isConfirmed) {
            // Aquí puedes ajustar la URL a tu módulo de ventas
            window.location.href = 'venta_rapida.php?desde_cotizacion=1';
        }
    });
}

/* ================================================================
   IMPRIMIR
================================================================ */
function imprimirCotizacion() {
    if (!cotizacionActual.items.length) return;
    const subtotal  = cotizacionActual.items.reduce((a, i) => a + i.cantidad * i.precioUnit, 0);
    const total     = Math.max(0, subtotal - cotizacionActual.descuentoMonto);
    const fechaHoy  = new Date().toLocaleDateString('es-PE', { year:'numeric', month:'long', day:'numeric' });
    const nombreLista = document.getElementById('nombreLista').value.trim() || 'Lista Escolar';

    const filas = cotizacionActual.items.map((item, idx) => `
        <tr style="border-bottom:1px solid #f0f0f0;">
            <td style="padding:7px 10px;font-size:12px;color:#888;">${idx + 1}</td>
            <td style="padding:7px 10px;font-size:12px;font-weight:600;">${item.descripcion}${item.categoria ? '<br><span style="font-size:10px;color:#aaa;">'+item.categoria+'</span>' : ''}</td>
            <td style="padding:7px 10px;font-size:12px;text-align:center;">${item.cantidad}</td>
            <td style="padding:7px 10px;font-size:12px;text-align:right;">S/ ${item.precioUnit.toFixed(2)}</td>
            <td style="padding:7px 10px;font-size:12px;text-align:right;font-weight:700;">S/ ${(item.cantidad * item.precioUnit).toFixed(2)}</td>
        </tr>`).join('');

    document.getElementById('contenidoImpresion').innerHTML = `
        <div style="font-family:'Helvetica Neue',Arial,sans-serif;max-width:700px;margin:0 auto;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;">
                <div>
                    <h2 style="margin:0;color:#2a2f5b;font-size:22px;font-weight:800;">COTIZACIÓN</h2>
                    <div style="color:#667eea;font-weight:700;font-size:14px;">${cotizacionActual.codigo}</div>
                    <div style="font-size:12px;color:#888;margin-top:4px;">${nombreLista}</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:12px;color:#888;">Fecha: <strong>${fechaHoy}</strong></div>
                    ${cotizacionActual.cliente ? `<div style="font-size:12px;color:#888;">Cliente: <strong>${cotizacionActual.cliente.persona_concatenada}</strong></div>` : ''}
                    <div style="font-size:11px;color:#aaa;margin-top:4px;">Válido por 7 días</div>
                </div>
            </div>
            <table style="width:100%;border-collapse:collapse;margin-bottom:18px;">
                <thead>
                    <tr style="background:#2a2f5b;color:white;">
                        <th style="padding:8px 10px;font-size:11px;text-align:left;">#</th>
                        <th style="padding:8px 10px;font-size:11px;text-align:left;">Descripción</th>
                        <th style="padding:8px 10px;font-size:11px;text-align:center;">Cant.</th>
                        <th style="padding:8px 10px;font-size:11px;text-align:right;">P. Unit.</th>
                        <th style="padding:8px 10px;font-size:11px;text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>
            <div style="display:flex;justify-content:flex-end;">
                <table style="width:240px;">
                    <tr><td style="padding:4px 8px;font-size:12px;color:#888;">Subtotal:</td><td style="padding:4px 8px;font-size:12px;text-align:right;font-weight:600;">S/ ${subtotal.toFixed(2)}</td></tr>
                    ${cotizacionActual.descuentoMonto > 0 ? `<tr><td style="padding:4px 8px;font-size:12px;color:#888;">Descuento (${cotizacionActual.descuentoPct}%):</td><td style="padding:4px 8px;font-size:12px;text-align:right;color:#dc3545;font-weight:600;">- S/ ${cotizacionActual.descuentoMonto.toFixed(2)}</td></tr>` : ''}
                    <tr style="border-top:2px solid #2a2f5b;">
                        <td style="padding:8px;font-size:14px;font-weight:800;color:#2a2f5b;">TOTAL:</td>
                        <td style="padding:8px;font-size:16px;font-weight:800;text-align:right;color:#11998e;">S/ ${total.toFixed(2)}</td>
                    </tr>
                </table>
            </div>
            ${cotizacionActual.notas ? `<div style="margin-top:16px;padding:10px 14px;background:#fff8e1;border-radius:8px;font-size:12px;"><strong>Notas:</strong> ${cotizacionActual.notas}</div>` : ''}
            <div style="margin-top:24px;text-align:center;font-size:11px;color:#ccc;">
                Esta cotización es referencial y no constituye un comprobante de pago.
            </div>
        </div>`;
    new bootstrap.Modal(document.getElementById('modalImprimir')).show();
}

/* ================================================================
   WHATSAPP
================================================================ */
function enviarWhatsApp() {
    const tel = document.getElementById('telefonoWhatsapp').value.trim();
    if (!tel || tel.length < 9) {
        Swal.fire({ icon:'warning', title:'Teléfono inválido', text:'Ingresa 9 dígitos.', confirmButtonText:'Ok' });
        return;
    }
    const subtotal = cotizacionActual.items.reduce((a, i) => a + i.cantidad * i.precioUnit, 0);
    const total    = Math.max(0, subtotal - cotizacionActual.descuentoMonto);
    const lista    = document.getElementById('nombreLista').value.trim() || 'Lista Escolar';

    let msg = `📚 *COTIZACIÓN DE LISTA ESCOLAR*\n`;
    msg += `*${cotizacionActual.codigo}*\n`;
    msg += `📝 ${lista}\n\n`;
    cotizacionActual.items.forEach((item, idx) => {
        msg += `${idx + 1}. ${item.descripcion} × ${item.cantidad} → S/ ${(item.cantidad * item.precioUnit).toFixed(2)}\n`;
    });
    msg += `\n💰 *TOTAL: S/ ${total.toFixed(2)}*`;
    if (cotizacionActual.notas) msg += `\n\n📌 ${cotizacionActual.notas}`;

    const num = '51' + tel;
    const url = `https://wa.me/${num}?text=${encodeURIComponent(msg)}`;
    window.open(url, '_blank');
}

/* ================================================================
   HELPERS
================================================================ */
function labelEstado(e) {
    const m = { pendiente:'Pendiente', aprobada:'Aprobada', convertida:'Convertida', cancelada:'Cancelada' };
    return m[e] || e;
}
function formatFecha(iso) {
    if (!iso) return '—';
    return new Date(iso).toLocaleDateString('es-PE', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' });
}
</script>

<?php include("pie.php"); ?>