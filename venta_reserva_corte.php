<?php
include("cabecera.php");
include("logica/clssVenta.php");

if (isset($_GET['id'])) { $id = $_GET['id']; }
$sucursal_id = $_SESSION["sucursal_id"];
?>
<style>
:root {
    --primary:#2a2f5b; --primary-light:#3d4480;
    --accent:#667eea;  --accent2:#764ba2;
    --success:#11998e; --success-light:#38ef7d;
    --warning:#f7971e; --danger:#dc3545;
    --bg-card:#f8f9ff; --border-soft:#e3e6f5;
    --text-muted:#6c757d;
    --gradient-main:#0033A0;
    --gradient-success:linear-gradient(135deg,#11998e 0%,#38ef7d 100%);
    --shadow-card:0 4px 20px rgba(42,47,91,0.10);
    --shadow-hover:0 8px 30px rgba(102,126,234,0.18);
}

/* === HEADER === */
.reserva-header{background:var(--gradient-main);border-radius:18px;color:white;padding:28px 32px 22px;margin-bottom:28px;box-shadow:var(--shadow-hover);position:relative;overflow:hidden;}
.reserva-header::before{content:"📋";position:absolute;right:28px;top:18px;font-size:3.5rem;opacity:.18;pointer-events:none;}
.reserva-header h3{font-weight:800;letter-spacing:-.5px;margin-bottom:4px;}
.reserva-header p{opacity:.85;margin-bottom:0;font-size:.97rem;}

/* === PANELES BLANCOS === */
.panel-izq,.panel-der{background:white;border-radius:16px;box-shadow:var(--shadow-card);padding:22px 20px;height:100%;}
.panel-centro{background:white;border-radius:16px;box-shadow:var(--shadow-card);padding:20px;height:100%;}
.section-title{font-weight:700;color:var(--primary);font-size:1rem;margin-bottom:14px;display:flex;align-items:center;gap:8px;}
.section-title i{color:var(--accent);}

/* === SERVICIOS === */
.servicios-wrap{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px;}
.btn-servicio{background:white;border:2px solid var(--border-soft);border-radius:20px;padding:6px 14px;font-weight:700;font-size:.78rem;color:var(--primary);transition:all .2s;cursor:pointer;display:inline-flex;align-items:center;gap:5px;}
.btn-servicio:hover{background:var(--gradient-main);border-color:transparent;color:white;box-shadow:0 3px 10px rgba(102,126,234,.3);transform:translateY(-1px);}
.btn-servicio-special{background:linear-gradient(135deg,#f7971e 0%,#ffd200 100%);border-color:transparent;color:var(--primary);}
.btn-servicio-special:hover{color:var(--primary);}

/* === FILTROS === */
.filtro-input{border:2px solid var(--border-soft);border-radius:12px;padding:8px 12px;font-size:.85rem;width:100%;transition:border-color .2s;background:white;}
.filtro-input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(102,126,234,.12);outline:none;}
.search-wrapper{position:relative;}
#searchInput{border:2px solid var(--border-soft);border-radius:12px;padding:10px 40px 10px 16px;font-size:.9rem;width:100%;transition:border-color .2s;}
#searchInput:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(102,126,234,.12);outline:none;}
.search-icon-abs{position:absolute;right:14px;top:50%;transform:translateY(-50%);color:var(--accent);font-size:.9rem;pointer-events:none;}

/* === TARJETAS DE PRODUCTOS === */
.producto-card{background:white;border:1.5px solid var(--border-soft);border-radius:14px;padding:12px;transition:all .2s;cursor:default;height:100%;}
.producto-card:hover{box-shadow:0 6px 20px rgba(102,126,234,.18);border-color:var(--accent);transform:translateY(-2px);}
.producto-card.sin-stock{border-color:#fee2e2;background:#fff8f8;}
.prod-nombre{font-weight:700;font-size:.85rem;color:var(--primary);line-height:1.3;margin-bottom:4px;}
.prod-cat{font-size:.7rem;color:var(--accent);font-weight:600;margin-bottom:4px;}
.prod-meta{font-size:.68rem;color:var(--text-muted);line-height:1.5;}
.prod-footer{display:flex;justify-content:space-between;align-items:center;margin-top:8px;padding-top:8px;border-top:1px solid #f0f3ff;}
.prod-precio{font-weight:800;color:var(--success);font-size:.95rem;}
.sin-stock-badge{display:inline-block;background:#fee2e2;color:var(--danger);border-radius:12px;padding:1px 8px;font-size:.65rem;font-weight:700;margin-bottom:4px;}
.btn-agregar-prod{width:30px;height:30px;border-radius:50%;border:none;background:var(--gradient-success);color:white;font-weight:800;font-size:.9rem;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s;box-shadow:0 2px 8px rgba(17,153,142,.3);}
.btn-agregar-prod:hover{transform:scale(1.15);box-shadow:0 4px 14px rgba(17,153,142,.4);}
.btn-agregar-prod:disabled{background:#ccc;box-shadow:none;transform:none;cursor:not-allowed;}

/* === PAGINACIÓN === */
.paginacion-wrap{display:flex;justify-content:space-between;align-items:center;margin-top:14px;padding-top:12px;border-top:1px solid var(--border-soft);}
.btn-pag{background:white;border:1.5px solid var(--border-soft);border-radius:10px;padding:6px 14px;font-size:.8rem;font-weight:600;color:var(--primary);cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:5px;}
.btn-pag:hover:not(:disabled){background:var(--gradient-main);border-color:transparent;color:white;}
.btn-pag:disabled{opacity:.4;cursor:not-allowed;}
.pag-info{font-size:.78rem;color:var(--text-muted);font-weight:600;}

/* === PANEL DER: ITEMS SELECCIONADOS === */
.item-reserva{background:var(--bg-card);border:1.5px solid var(--border-soft);border-radius:12px;padding:10px 12px;margin-bottom:8px;display:flex;justify-content:space-between;align-items:flex-start;gap:8px;animation:fadeInRow .25s ease;}
.item-reserva-info{flex:1;min-width:0;}
.item-reserva-nombre{font-weight:700;font-size:.83rem;color:var(--primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.item-reserva-meta{font-size:.71rem;color:var(--text-muted);margin-top:2px;}
.item-reserva-precio{font-weight:800;color:var(--success);font-size:.88rem;white-space:nowrap;}
.btn-quitar{background:#fee2e2;border:none;color:var(--danger);border-radius:8px;padding:4px 8px;font-size:.75rem;cursor:pointer;transition:all .2s;flex-shrink:0;}
.btn-quitar:hover{background:var(--danger);color:white;}

/* === TABLA OCULTA (backend) === */
#tabla_articulos{display:none;}
#tabla_articulos th:nth-child(1),#tabla_articulos td:nth-child(1),
#tabla_articulos th:nth-child(6),#tabla_articulos td:nth-child(6),
#tabla_articulos th:nth-child(7),#tabla_articulos td:nth-child(7){display:none!important;}

/* === RESUMEN === */
.resumen-linea{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f0f3ff;font-size:.9rem;}
.resumen-linea:last-child{border-bottom:none;}
.resumen-linea .lbl{color:var(--text-muted);font-weight:500;}
.resumen-linea .val{font-weight:700;color:var(--primary);}
.resumen-total-grande{background:var(--gradient-main);border-radius:14px;padding:14px 18px;color:white;text-align:center;margin:16px 0 10px;}
.resumen-total-grande .label-total{font-size:.85rem;opacity:.85;}
.resumen-total-grande .monto-total{font-size:2.1rem;font-weight:800;letter-spacing:-1px;}

/* === EMPTY STATE === */
.empty-state{text-align:center;padding:32px 16px;color:var(--text-muted);}
.empty-state .empty-icon{font-size:2.5rem;margin-bottom:8px;opacity:.4;}
.empty-state p{font-size:.83rem;}

/* === BOTONES === */
.btn-success-custom{background:var(--gradient-success);border:none;color:white;border-radius:12px;padding:10px 20px;font-weight:700;font-size:.92rem;transition:all .25s;box-shadow:0 3px 12px rgba(17,153,142,.25);cursor:pointer;width:100%;display:block;}
.btn-success-custom:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(17,153,142,.35);color:white;}
.btn-success-custom:disabled{background:#ccc;box-shadow:none;transform:none;cursor:not-allowed;}
.btn-primary-custom{background:var(--gradient-main);border:none;color:white;border-radius:12px;padding:10px 20px;font-weight:700;font-size:.92rem;transition:all .25s;box-shadow:0 3px 12px rgba(102,126,234,.3);cursor:pointer;width:100%;display:block;}
.btn-primary-custom:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(102,126,234,.4);color:white;}
.btn-warning-custom{background:linear-gradient(135deg,#f7971e 0%,#ffd200 100%);border:none;color:var(--primary);border-radius:12px;padding:10px 20px;font-weight:700;font-size:.92rem;transition:all .25s;cursor:pointer;width:100%;display:block;}
.btn-warning-custom:hover{transform:translateY(-2px);color:var(--primary);}

/* === MODALES === */
.modal-content{border-radius:18px;border:none;box-shadow:0 14px 50px rgba(0,0,0,.2);overflow:hidden;}
.modal-header-gradient{background:var(--gradient-main);color:white;padding:18px 22px;border-bottom:none;}
.modal-header-gradient .btn-close{filter:brightness(0) invert(1);}
.modal-header-gradient .modal-title{font-weight:800;}
.modal-header-success{background:var(--gradient-success);color:white;padding:18px 22px;border-bottom:none;}
.modal-header-success .btn-close{filter:brightness(0) invert(1);}
.modal-body .form-control,.modal-body .form-select{border:1.5px solid var(--border-soft);border-radius:10px;font-size:.9rem;padding:9px 14px;}
.modal-body .form-control:focus,.modal-body .form-select:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(102,126,234,.12);}

/* === QTY CONTROL === */
.qty-control{display:inline-flex;align-items:center;gap:10px;background:var(--bg-card);border-radius:14px;padding:8px 14px;border:1.5px solid var(--border-soft);}
.qty-control input{width:72px;text-align:center;border:none;background:transparent;font-weight:800;font-size:1.1rem;color:var(--primary);outline:none;}
.qty-btn{width:34px;height:34px;border-radius:50%;border:none;font-weight:800;font-size:1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s;}
.qty-btn-minus{background:#fee2e2;color:var(--danger);}
.qty-btn-plus{background:#d1fae5;color:var(--success);}
.qty-btn:hover{transform:scale(1.15);}
.price-pills{display:flex;gap:6px;flex-wrap:wrap;justify-content:center;margin-top:8px;}
.price-pill{background:#f0f3ff;border:1.5px solid var(--border-soft);border-radius:16px;padding:4px 14px;font-size:.78rem;font-weight:700;color:var(--primary);cursor:pointer;transition:all .2s;}
.price-pill:hover{background:var(--gradient-main);border-color:transparent;color:white;}

/* === SUGERENCIAS === */
#sugerencias{max-height:200px;overflow-y:auto;z-index:1050;border:1.5px solid var(--border-soft);border-radius:12px;box-shadow:0 6px 20px rgba(0,0,0,.1);}
#sugerencias .list-group-item{cursor:pointer;font-size:.88rem;border-color:#f0f3ff;padding:10px 16px;}
#sugerencias .list-group-item:hover{background:#f0f3ff;color:var(--primary);}
#modalCliente{z-index:1060!important;}

/* === TABS MODALES === */
.nav-reserva .nav-link{border-radius:10px;font-weight:600;font-size:.88rem;color:var(--text-muted);padding:8px 18px;transition:all .2s;border:none;}
.nav-reserva .nav-link.active{background:var(--gradient-main);color:white;box-shadow:0 3px 10px rgba(102,126,234,.3);}
.nav-reserva .nav-link:not(.active):hover{background:#f0f3ff;color:var(--primary);}

/* === ANIMACIONES === */
.fade-in-row{animation:fadeInRow .3s ease;}
@keyframes fadeInRow{from{opacity:0;transform:translateY(-6px);}to{opacity:1;transform:translateY(0);}}
.pulse-total{animation:pulseTotal .35s ease;}
@keyframes pulseTotal{0%,100%{transform:scale(1);}50%{transform:scale(1.05);}}

/* === RESPONSIVE === */
@media(max-width:768px){
    .reserva-header{padding:18px 16px;}
    .reserva-header::before{display:none;}
    .producto-card{padding:8px;}
}
</style>

<div class="container">
<div class="page-inner">

    <!-- HEADER -->
    <div class="reserva-header">
        <h3><i class="fas fa-bookmark me-2"></i> Venta por Reserva</h3>
        <p>Registra pedidos enviados por WhatsApp. Reservas para recoger después.</p>
    </div>

    <!-- ====== LAYOUT 3 COLUMNAS ====== -->
    <div class="row g-3">

        <!-- ── COL IZQUIERDA: servicios + filtros + búsqueda ── -->
        <div class="col-lg-3">
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
            <div class="d-flex flex-column gap-2 mb-2">
                <select id="filterCategoria" class="filtro-input" onchange="filterProducts()">
                    <option value="">Categoría</option>
                </select>
                <select id="filterTipo" class="filtro-input" onchange="filterProducts()">
                    <option value="">Tipo</option>
                </select>
                <select id="filterDimension" class="filtro-input" onchange="filterProducts()">
                    <option value="">Dimensión</option>
                </select>
                <select id="filterColor" class="filtro-input" onchange="filterProducts()">
                    <option value="">Color</option>
                </select>
            </div>
            <div class="text-end mb-3">
                <button id="clearFilters" style="background:transparent;border:none;font-size:.78rem;color:var(--accent);font-weight:600;cursor:pointer;">
                    <i class="fas fa-broom me-1"></i>Limpiar filtros
                </button>
            </div>

            <hr style="border-color:#f0f3ff;margin:0 0 14px;">

            <div class="section-title"><i class="fas fa-search"></i> Buscar</div>
            <div class="search-wrapper">
                <input type="text" id="searchInput" placeholder="Nombre, categoría..." oninput="filterProducts()">
                <i class="fas fa-search search-icon-abs"></i>
            </div>

          </div>
        </div><!-- /col izq -->

        <!-- ── COL CENTRO: tarjetas de productos con paginación ── -->
        <div class="col-lg-6">
          <div class="panel-centro">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0" style="color:var(--primary);">
                    <i class="fas fa-th me-1" style="color:var(--accent);"></i>
                    Productos
                    <span class="badge ms-1" style="background:#f0f3ff;color:var(--primary);font-size:.72rem;" id="totalProductosBadge">0</span>
                </h6>
            </div>

            <div id="productoContainer" class="row g-2">
                <!-- productos dinámicos -->
            </div>

            <div id="emptyProducts" class="empty-state" style="display:none;">
                <div class="empty-icon">🔍</div>
                <p>No se encontraron productos<br>con esos filtros.</p>
            </div>

            <div class="paginacion-wrap" id="paginacionWrap" style="display:none;">
                <button class="btn-pag" id="prevPage" onclick="changePage(-1)" disabled>
                    <i class="fas fa-chevron-left"></i> Anterior
                </button>
                <span class="pag-info" id="pagInfo">Página 1 de 1</span>
                <button class="btn-pag" id="nextPage" onclick="changePage(1)">
                    Siguiente <i class="fas fa-chevron-right"></i>
                </button>
            </div>
          </div>
        </div><!-- /col centro -->

        <!-- ── COL DERECHA: artículos seleccionados + resumen ── -->
        <div class="col-lg-3">
          <div class="panel-der">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0" style="color:var(--primary);">
                    <i class="fas fa-list-ul me-1" style="color:var(--accent);"></i>
                    Reserva
                    <span class="badge ms-1" style="background:#f0f3ff;color:var(--primary);font-size:.72rem;" id="contadorItems">0 ítems</span>
                </h6>
                <button onclick="limpiarReserva()" style="background:transparent;border:none;font-size:.75rem;color:var(--danger);font-weight:600;cursor:pointer;">
                    <i class="fas fa-trash me-1"></i>Limpiar
                </button>
            </div>

            <!-- Lista visual de ítems -->
            <div id="listaItemsReserva">
                <div id="emptyStateReserva" class="empty-state">
                    <div class="empty-icon">📋</div>
                    <p>Aún no hay artículos.<br>Agrega desde el catálogo.</p>
                </div>
            </div>

            <!-- TABLA OCULTA (para lógica de backend) -->
            <table id="tabla_articulos">
                <thead><tr>
                    <th>ID</th><th>Artículo</th><th>Cantidad</th>
                    <th>Precio Unit.</th><th>Subtotal</th>
                    <th>Acción</th><th>ID_MOV</th><th>NOTA</th>
                </tr></thead>
                <tbody></tbody>
            </table>

            <!-- RESUMEN -->
            <div id="resumenPanel" style="display:none;">
                <hr style="border-color:#f0f3ff;">
                <div class="resumen-linea">
                    <span class="lbl">Total ítems</span>
                    <span class="val" id="resumenItems">0</span>
                </div>
                <div class="resumen-total-grande">
                    <div class="label-total">TOTAL RESERVA</div>
                    <div class="monto-total" id="id_subtotal_general_display">S/ 0.00</div>
                    <input type="hidden" id="id_subtotal_general" value="0.00">
                    <span id="id_subtotal_articulos" style="display:none;">0.00</span>
                </div>
                <div class="d-grid gap-2">
                    <button class="btn-success-custom" id="btnRealizarReserva" onclick="abrirModalReserva()">
                        <i class="fas fa-bookmark me-1"></i> Confirmar Reserva
                    </button>
                </div>
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
        <button class="btn-success-custom" id="btn_agregar_solocorte" style="width:auto;padding:8px 22px;"><i class="fas fa-plus me-1"></i>Agregar</button>
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
            <button class="price-pill" onclick="fnAumentarMin(45)">45m</button>
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
        <button class="btn-success-custom" id="btn_agregar_solocortev2" style="width:auto;padding:8px 22px;"><i class="fas fa-plus me-1"></i>Agregar</button>
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
        <button id="btnConfirmarCantidad" class="btn-success-custom" style="width:auto;padding:8px 22px;"><i class="fas fa-check me-1"></i>Confirmar</button>
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
        <ul class="nav nav-reserva mb-3">
          <li class="nav-item"><button class="nav-link active" id="pills-persona-tab" data-bs-toggle="pill" data-bs-target="#pills-persona" type="button"><i class="fas fa-user me-1"></i>Persona</button></li>
          <li class="nav-item ms-2"><button class="nav-link" id="pills-empresa-tab" data-bs-toggle="pill" data-bs-target="#pills-empresa" type="button"><i class="fas fa-building me-1"></i>Empresa</button></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane fade show active" id="pills-persona">
            <div class="mb-3"><label class="fw-bold" style="font-size:.83rem;color:var(--primary);">DNI <span class="text-danger">*</span></label>
              <div class="input-group mt-1"><input type="text" class="form-control" id="numeroDocumentoPersona" placeholder="8 dígitos" maxlength="8">
              <button class="btn" type="button" id="btnBuscarDNI" style="background:var(--gradient-main);color:white;border:none;border-radius:0 10px 10px 0;"><i class="fas fa-search"></i></button></div>
              <div class="invalid-feedback" id="error-numeroDocumentoPersona"></div></div>
            <div class="mb-3"><label class="fw-bold" style="font-size:.83rem;color:var(--primary);">Nombres <span class="text-danger">*</span></label><input type="text" class="form-control mt-1" id="nombresPersona"><div class="invalid-feedback" id="error-nombresPersona"></div></div>
            <div class="mb-3"><label class="fw-bold" style="font-size:.83rem;color:var(--primary);">Apellidos <span class="text-danger">*</span></label><input type="text" class="form-control mt-1" id="apellidosPersona"><div class="invalid-feedback" id="error-apellidosPersona"></div></div>
            <div class="row g-3">
              <div class="col-6"><label class="fw-bold" style="font-size:.83rem;color:var(--primary);">Teléfono</label><input type="text" class="form-control mt-1" id="telefonoPersona" maxlength="9"></div>
              <div class="col-6"><label class="fw-bold" style="font-size:.83rem;color:var(--primary);">Email</label><input type="email" class="form-control mt-1" id="emailPersona"></div>
            </div>
          </div>
          <div class="tab-pane fade" id="pills-empresa">
            <div class="mb-3"><label class="fw-bold" style="font-size:.83rem;color:var(--primary);">RUC <span class="text-danger">*</span></label>
              <div class="input-group mt-1"><input type="text" class="form-control" id="numeroDocumentoEmpresa" placeholder="11 dígitos" maxlength="11">
              <button class="btn" type="button" id="btnBuscarRUC" style="background:var(--gradient-main);color:white;border:none;border-radius:0 10px 10px 0;"><i class="fas fa-search"></i></button></div>
              <div class="invalid-feedback" id="error-numeroDocumentoEmpresa"></div></div>
            <div class="mb-3"><label class="fw-bold" style="font-size:.83rem;color:var(--primary);">Nombre Comercial <span class="text-danger">*</span></label><input type="text" class="form-control mt-1" id="nombreComercial"><div class="invalid-feedback" id="error-nombreComercial"></div></div>
            <div class="mb-3"><label class="fw-bold" style="font-size:.83rem;color:var(--primary);">Razón Social <span class="text-danger">*</span></label><input type="text" class="form-control mt-1" id="razonSocial"><div class="invalid-feedback" id="error-razonSocial"></div></div>
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
        <div class="mb-3">
          <label class="fw-bold" style="font-size:.83rem;color:var(--primary);">
            <i class="fas fa-user-tie me-1" style="color:var(--accent);"></i>Cliente <span class="text-danger">*</span>
          </label>
          <div class="input-group mt-1">
            <input type="text" class="form-control" id="nombreCliente" placeholder="Buscar cliente por nombre o DNI">
            <button type="button" class="btn" id="btnAbrirModalCliente" style="background:var(--gradient-main);color:white;border:none;border-radius:0 10px 10px 0;">
              <i class="fas fa-user-plus"></i>
            </button>
          </div>
          <div id="sugerencias" class="list-group position-absolute" style="width:calc(100% - 48px);left:24px;"></div>
        </div>
        <div class="mb-2">
          <small style="color:var(--text-muted);">ID Cliente: <strong><span id="idPersona">#</span></strong></small>
        </div>
        <div class="mb-3">
          <label class="fw-bold" style="font-size:.83rem;color:var(--primary);">Monto Total (S/)</label>
          <div class="input-group mt-1">
            <span class="input-group-text" style="background:#f0f3ff;border:1.5px solid var(--border-soft);border-radius:10px 0 0 10px;">S/</span>
            <input type="text" class="form-control fw-bold" id="montoTotal" readonly style="border:1.5px solid var(--border-soft);font-size:1.1rem;">
          </div>
        </div>
        <div class="p-3 mt-2" style="background:#e8f5e9;border-left:4px solid var(--success);border-radius:10px;font-size:.85rem;">
          <i class="fas fa-info-circle me-1" style="color:var(--success);"></i>
          Esta reserva quedará pendiente de pago hasta que el cliente la recoja.
        </div>
        <div class="text-center mt-4">
          <button class="btn-success-custom" id="Reservar" style="width:auto;padding:12px 36px;font-size:1rem;">
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
/* ================================================================
   DATOS Y PAGINACIÓN
================================================================ */
const products = <?php echo json_encode(listarProductosVenta1($sucursal_id)); ?>;
let filteredProducts = [...products];
let currentPage = 1;
const itemsPerPage = 8;

document.addEventListener('DOMContentLoaded', () => {
    populateFilters();
    renderPage();
    initEventListeners();
});

/* --- FILTROS --- */
function populateFilters() {
    const cats=[...new Set(products.map(p=>p.categoria))];
    const tipos=[...new Set(products.map(p=>p.tipo))];
    const dims=[...new Set(products.map(p=>p.dimension))];
    const cols=[...new Set(products.map(p=>p.color))];
    cats.forEach(v=>document.getElementById('filterCategoria').innerHTML+=`<option value="${v}">${v}</option>`);
    tipos.forEach(v=>document.getElementById('filterTipo').innerHTML+=`<option value="${v}">${v}</option>`);
    dims.forEach(v=>document.getElementById('filterDimension').innerHTML+=`<option value="${v}">${v}</option>`);
    cols.forEach(v=>document.getElementById('filterColor').innerHTML+=`<option value="${v}">${v}</option>`);
}

function filterProducts() {
    const q=document.getElementById('searchInput').value.toLowerCase();
    const cat=document.getElementById('filterCategoria').value;
    const tip=document.getElementById('filterTipo').value;
    const dim=document.getElementById('filterDimension').value;
    const col=document.getElementById('filterColor').value;
    filteredProducts=products.filter(p=>(!cat||p.categoria===cat)&&(!tip||p.tipo===tip)&&(!dim||p.dimension===dim)&&(!col||p.color===col)&&(!q||p.articulo.toLowerCase().includes(q)||(p.categoria||'').toLowerCase().includes(q)||(p.tipo||'').toLowerCase().includes(q)));
    currentPage=1; renderPage();
}

document.getElementById('clearFilters').addEventListener('click', () => {
    ['filterCategoria','filterTipo','filterDimension','filterColor'].forEach(id=>document.getElementById(id).value='');
    document.getElementById('searchInput').value='';
    filteredProducts=[...products]; currentPage=1; renderPage();
});

/* --- RENDER PRODUCTOS --- */
function renderPage() {
    const container=document.getElementById('productoContainer');
    const empty=document.getElementById('emptyProducts');
    const pag=document.getElementById('paginacionWrap');
    const badge=document.getElementById('totalProductosBadge');
    badge.textContent=filteredProducts.length;

    if(!filteredProducts.length){container.innerHTML='';empty.style.display='block';pag.style.display='none';return;}
    empty.style.display='none';

    const start=(currentPage-1)*itemsPerPage;
    const page=filteredProducts.slice(start,start+itemsPerPage);
    container.innerHTML='';

    page.forEach(p=>{
        const sinStock=parseFloat(p.stock)===0;
        const col=document.createElement('div'); col.className='col-6 col-md-3 mb-2';
        col.innerHTML=`
        <div class="producto-card${sinStock?' sin-stock':''}">
            ${sinStock?'<span class="sin-stock-badge">Sin stock</span>':''}
            <div class="prod-nombre">${p.articulo}</div>
            <div class="prod-cat">${p.categoria||''}</div>
            <div class="prod-meta">
                <div><b>Tipo:</b> ${p.tipo||'-'}</div>
                <div><b>Dim:</b> ${p.dimension||'-'}</div>
                <div><b>Color:</b> ${p.color||'-'}</div>
                <div><b>Stock:</b> <span class="${sinStock?'text-danger fw-bold':''}">${p.stock}</span></div>
            </div>
            <div class="prod-footer">
                <span class="prod-precio">S/ ${parseFloat(p.precio_venta).toFixed(2)}</span>
                <button class="btn-agregar-prod" ${sinStock?'disabled':''} onclick='fn_agregar_venta(${JSON.stringify(p).replace(/'/g,"&#39;")})'>
                    <i class="fas fa-plus" style="font-size:.75rem;"></i>
                </button>
            </div>
        </div>`;
        container.appendChild(col);
    });

    const total=Math.ceil(filteredProducts.length/itemsPerPage);
    document.getElementById('prevPage').disabled=currentPage===1;
    document.getElementById('nextPage').disabled=currentPage===total;
    document.getElementById('pagInfo').textContent=`Página ${currentPage} de ${total}`;
    pag.style.display=filteredProducts.length>itemsPerPage?'flex':'none';
}

function changePage(dir){
    currentPage+=dir;
    const total=Math.ceil(filteredProducts.length/itemsPerPage);
    if(currentPage<1)currentPage=1; if(currentPage>total)currentPage=total;
    renderPage();
}

/* ================================================================
   AGREGAR A TABLA (lógica interna)
================================================================ */
function agregarATabla(item) {
    const tbody=document.getElementById('tabla_articulos').getElementsByTagName('tbody')[0];
    const fila=tbody.insertRow();
    fila.insertCell(0).textContent=item.id;
    fila.insertCell(1).textContent=item.articulo;
    fila.insertCell(2).textContent=item.cantidad||'-';
    fila.insertCell(3).textContent=item.precio_unitario||'-';
    fila.insertCell(4).textContent=parseFloat(item.subtotal).toFixed(2);
    fila.insertCell(5).textContent=''; // acciones - vacío aquí
    fila.insertCell(6).textContent=item.idmovimiento;
    fila.insertCell(7).textContent=item.nota||'';
    return fila;
}

/* ================================================================
   RENDER LISTA VISUAL DERECHA
================================================================ */
function agregarItemVisual(item, filaTabla) {
    const lista=document.getElementById('listaItemsReserva');
    const empty=document.getElementById('emptyStateReserva');
    if(empty) empty.remove();

    const div=document.createElement('div'); div.className='item-reserva';
    div.dataset.subtotal=parseFloat(item.subtotal).toFixed(2);
    div.innerHTML=`
        <div class="item-reserva-info">
            <div class="item-reserva-nombre" title="${item.articulo}">${item.articulo}</div>
            <div class="item-reserva-meta">${item.cantidad!=='-'?`Cant: ${item.cantidad} · `:''}${item.idmovimiento===6||item.idmovimiento===15?'Servicio':'S/ '+parseFloat(item.precio_unitario||0).toFixed(2)}</div>
        </div>
        <div class="d-flex align-items-center gap-1">
            <span class="item-reserva-precio">S/ ${parseFloat(item.subtotal).toFixed(2)}</span>
            <button class="btn-quitar"><i class="fas fa-times"></i></button>
        </div>`;

    div.querySelector('.btn-quitar').onclick=()=>{
        Swal.fire({title:'¿Eliminar?',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc3545',cancelButtonColor:'#6c757d',confirmButtonText:'Sí',cancelButtonText:'Cancelar'})
        .then(r=>{if(r.isConfirmed){div.remove();if(filaTabla)filaTabla.remove();calcularTotales();showNotification&&showNotification("success");}});
    };
    lista.appendChild(div);
    calcularTotales();
}

function calcularTotales() {
    const items=document.querySelectorAll('.item-reserva');
    let total=0;
    items.forEach(i=>total+=parseFloat(i.dataset.subtotal)||0);

    const display=document.getElementById('id_subtotal_general_display');
    const hidden=document.getElementById('id_subtotal_general');
    display.textContent='S/ '+total.toFixed(2);
    hidden.value=total.toFixed(2);
    document.getElementById('id_subtotal_articulos').textContent=total.toFixed(2);
    document.getElementById('resumenItems').textContent=items.length;
    document.getElementById('contadorItems').textContent=items.length+(items.length===1?' ítem':' ítems');

    const resumen=document.getElementById('resumenPanel');
    const hayItems=items.length>0;
    resumen.style.display=hayItems?'block':'none';

    if(hayItems){display.classList.remove('pulse-total');void display.offsetWidth;display.classList.add('pulse-total');}

    if(!hayItems){
        const lista=document.getElementById('listaItemsReserva');
        if(!lista.querySelector('#emptyStateReserva')){
            lista.innerHTML='<div id="emptyStateReserva" class="empty-state"><div class="empty-icon">📋</div><p>Aún no hay artículos.<br>Agrega desde el catálogo.</p></div>';
        }
    }
}

function limpiarReserva() {
    const items=document.querySelectorAll('.item-reserva');
    if(!items.length) return;
    Swal.fire({title:'¿Limpiar reserva?',text:'Se eliminarán todos los artículos.',icon:'warning',showCancelButton:true,confirmButtonColor:'#dc3545',cancelButtonColor:'#6c757d',confirmButtonText:'Sí, limpiar',cancelButtonText:'Cancelar'})
    .then(r=>{if(r.isConfirmed){document.querySelector('#tabla_articulos tbody').innerHTML='';document.querySelectorAll('.item-reserva').forEach(i=>i.remove());calcularTotales();}});
}

function abrirModalReserva() {
    const total=document.getElementById('id_subtotal_general').value;
    document.getElementById('montoTotal').value=total;
    document.getElementById('idMontoVentaTitulo').textContent=total;
    new bootstrap.Modal(document.getElementById('modalRealizarPago')).show();
}

/* ================================================================
   SOLO CORTE
================================================================ */
function initSoloCorteModal() {
    document.getElementById('btnAbrirModalSolo').addEventListener('click',e=>{e.preventDefault();document.getElementById('cantidad_solocorte').value=0;document.getElementById('precioSoloCorte').value=1.5;new bootstrap.Modal(document.getElementById('modalSoloCorte')).show();});
    document.getElementById('btnSumarSoloCorte').onclick=()=>{let v=parseInt(document.getElementById('cantidad_solocorte').value);document.getElementById('cantidad_solocorte').value=v===0?10:v+1;};
    document.getElementById('btnRestarSoloCorte').onclick=()=>{let v=parseInt(document.getElementById('cantidad_solocorte').value);if(v>0)document.getElementById('cantidad_solocorte').value=v-1;};
    ['05','1','2','5'].forEach(inc=>{document.getElementById(`btnIncremento${inc}SoloCorte`).onclick=()=>{let p=parseFloat(document.getElementById('precioSoloCorte').value);document.getElementById('precioSoloCorte').value=(p+parseFloat(inc.replace('0','.'))).toFixed(2);};});
    document.getElementById('btn_agregar_solocorte').addEventListener('click',()=>{
        const min=parseInt(document.getElementById('cantidad_solocorte').value)||0;
        const tar=parseFloat(document.getElementById('precioSoloCorte').value)||0;
        if(min<=0){Swal.fire({icon:'warning',title:'Error',text:'Ingresa minutos válidos',timer:2000,showConfirmButton:false});return;}
        const item={id:'0',articulo:'SOLO CORTE',cantidad:'-',precio_unitario:'-',subtotal:min*tar,idmovimiento:6,nota:''};
        const fila=agregarATabla(item); agregarItemVisual(item,fila);
        bootstrap.Modal.getInstance(document.getElementById('modalSoloCorte')).hide();
        showNotification&&showNotification("success");
    });
}

/* ================================================================
   IMPRESIÓN 3D
================================================================ */
function initImpresion3DModal() {
    document.getElementById('btnAbrirModalSolov2').addEventListener('click',e=>{e.preventDefault();document.getElementById('cantidad_solocortev2').value=10;document.getElementById('precioSoloCortev2').value=1.5;document.getElementById('nota_impresion').value='';new bootstrap.Modal(document.getElementById('modalSoloCorteMaquina2')).show();});
    document.getElementById('btn_agregar_solocortev2').addEventListener('click',()=>{
        const min=parseInt(document.getElementById('cantidad_solocortev2').value)||0;
        const tar=parseFloat(document.getElementById('precioSoloCortev2').value)||0;
        const nota=document.getElementById('nota_impresion').value||'';
        if(min<=0){Swal.fire({icon:'warning',title:'Error',text:'Ingresa minutos válidos',timer:2000,showConfirmButton:false});return;}
        const item={id:'0',articulo:'IMPRESIÓN 3D'+(nota?` - ${nota}`:''),cantidad:'-',precio_unitario:'-',subtotal:min*tar,idmovimiento:15,nota};
        const fila=agregarATabla(item); agregarItemVisual(item,fila);
        bootstrap.Modal.getInstance(document.getElementById('modalSoloCorteMaquina2')).hide();
        showNotification&&showNotification("success");
    });
}
function fnAumentoOrResta(a){let v=parseInt(document.getElementById('cantidad_solocortev2').value);document.getElementById('cantidad_solocortev2').value=a==='+'?v+1:(v>1?v-1:v);}
function fnAumentarMin(m){document.getElementById('cantidad_solocortev2').value=m;}
function fnAumentaPrecioImpresion(m){let p=parseFloat(document.getElementById('precioSoloCortev2').value);document.getElementById('precioSoloCortev2').value=(p+m).toFixed(2);}
function limpiar(){document.getElementById('precioSoloCortev2').value=0;}

/* ================================================================
   MODAL CANTIDAD ARTÍCULO
================================================================ */
function fn_agregar_venta(articulo) {
    const modal=new bootstrap.Modal(document.getElementById('modalCantidad'));
    document.getElementById('nombreArticulo').textContent=articulo.articulo;
    document.getElementById('inputCantidad').value=1; document.getElementById('cantidadCorte').value=0;
    document.getElementById('precioCorte').value=articulo.corte?1.5:0; document.getElementById('idTextAreaDetalleInsert').value='';
    const sc=document.getElementById('seccionCorte'); sc.style.display=articulo.corte?'block':'none';
    document.getElementById('btnRestarCantidad').onclick=()=>{let c=parseInt(document.getElementById('inputCantidad').value);if(c>1){document.getElementById('inputCantidad').value=c-1;if(c-1===1&&articulo.corte){sc.style.display='block';document.getElementById('precioCorte').value=1.5;}else if(c-1>1){sc.style.display='none';document.getElementById('cantidadCorte').value=0;document.getElementById('precioCorte').value=0;}}};
    document.getElementById('btnSumarCantidad').onclick=()=>{let c=parseInt(document.getElementById('inputCantidad').value);if(c+1>articulo.stock){Swal.fire({icon:'warning',title:'Stock insuficiente',text:`Solo hay ${articulo.stock} unidades`,timer:2000,showConfirmButton:false});}else{document.getElementById('inputCantidad').value=c+1;if(c+1>1){sc.style.display='none';document.getElementById('cantidadCorte').value=0;document.getElementById('precioCorte').value=0;}}};
    document.getElementById('btnRestarCorte').onclick=()=>{let v=parseInt(document.getElementById('cantidadCorte').value);if(v>0)document.getElementById('cantidadCorte').value=v-1;};
    document.getElementById('btnSumarCorte').onclick=()=>{let v=parseInt(document.getElementById('cantidadCorte').value);document.getElementById('cantidadCorte').value=v===0?10:v+1;};
    ['05','1','2','5'].forEach(inc=>{document.getElementById(`btnIncremento${inc}`).onclick=()=>{let p=parseFloat(document.getElementById('precioCorte').value);document.getElementById('precioCorte').value=(p+parseFloat(inc.replace('0','.'))).toFixed(2);};});
    document.getElementById('btnConfirmarCantidad').onclick=()=>{
        const cant=parseInt(document.getElementById('inputCantidad').value);
        const min=parseInt(document.getElementById('cantidadCorte').value)||0;
        const pCorte=parseFloat(document.getElementById('precioCorte').value)||0;
        const nota=document.getElementById('idTextAreaDetalleInsert').value;
        if(cant>articulo.stock){Swal.fire({icon:'warning',title:'Stock insuficiente',text:`Solo hay ${articulo.stock} unidades`,timer:2000,showConfirmButton:false});return;}
        const subtotal=(cant*articulo.precio_venta)+(min*pCorte);
        const item={id:articulo.id,articulo:articulo.articulo+(nota?` - ${nota}`:''),cantidad:cant,precio_unitario:articulo.precio_venta,subtotal,idmovimiento:1,nota,id_movimiento:1,minutos:min||0,costo_por_minuto:pCorte||0};
        modal.hide();
        const fila=agregarATabla(item); agregarItemVisual(item,fila);
        showNotification&&showNotification("success");
    };
    modal.show();
}

/* ================================================================
   SERVICIOS GENÉRICOS
================================================================ */
function fn_servicios(jsDatos) {
    const medidas=jsDatos.medidas.slice(1,-1).split(',');
    document.getElementById('modalContent').innerHTML=`
    <div class="modal-header modal-header-gradient"><h5 class="modal-title fw-bold"><i class="fas fa-cube me-2"></i>${jsDatos.descripcion}</h5></div>
    <div class="p-4">
      <div class="mb-4 text-center">
        <label class="fw-bold mb-2 d-block" style="font-size:.85rem;color:var(--primary);">Cantidad</label>
        <div class="qty-control mx-auto" style="width:fit-content;">
          <button class="qty-btn qty-btn-minus" onclick="ajustarCantidad('${jsDatos.descripcion}',-1)">−</button>
          <input type="number" id="cant_${jsDatos.descripcion}" value="1">
          <button class="qty-btn qty-btn-plus" onclick="ajustarCantidad('${jsDatos.descripcion}',1)">+</button>
        </div>
      </div>
      <div class="mb-3">
        <label class="fw-bold mb-2 d-block" style="font-size:.85rem;color:var(--primary);">Dimensiones</label>
        <div class="d-flex flex-wrap gap-3 justify-content-center" id="dims_${jsDatos.descripcion}">
          ${medidas.map(m=>`<div class="form-check"><input class="form-check-input" type="checkbox" value="${m}" id="dim_${m}"><label class="form-check-label" for="dim_${m}" style="font-size:.88rem;">${m}</label></div>`).join('')}
        </div>
      </div>
      <div class="mb-3"><label class="fw-bold mb-1" style="font-size:.85rem;color:var(--primary);">Monto (S/)</label><input type="number" id="monto_${jsDatos.descripcion}" class="form-control mt-1" placeholder="0.00" step="0.01" style="border-radius:10px;border:1.5px solid var(--border-soft);"></div>
      <div class="mb-4"><label class="fw-bold mb-1" style="font-size:.85rem;color:var(--primary);">Detalle</label><textarea id="detalle_${jsDatos.descripcion}" class="form-control mt-1" rows="2" style="border-radius:10px;font-size:.87rem;"></textarea></div>
      <div class="text-center"><button class="btn-success-custom" onclick="agregarServicio('${jsDatos.descripcion}',${jsDatos.id})" style="width:auto;padding:10px 28px;"><i class="fas fa-plus me-1"></i>Agregar</button></div>
    </div>`;
    new bootstrap.Modal(document.getElementById('modalGenerico')).show();
}
function ajustarCantidad(s,inc){const i=document.getElementById(`cant_${s}`);i.value=Math.max(1,parseInt(i.value)+inc);}
function agregarServicio(nombre,idMov){
    const cant=parseInt(document.getElementById(`cant_${nombre}`).value);
    const monto=parseFloat(document.getElementById(`monto_${nombre}`).value)||0;
    const detalle=document.getElementById(`detalle_${nombre}`).value;
    const dims=Array.from(document.querySelectorAll(`#dims_${nombre} input:checked`)).map(c=>c.value).join(', ');
    if(monto<=0){Swal.fire({icon:'warning',title:'Error',text:'Ingresa un monto válido',timer:2000,showConfirmButton:false});return;}
    const art=dims?`${nombre} (${dims})${detalle?' - '+detalle:''}`:nombre+(detalle?' - '+detalle:'');
    const item={id:'0',articulo:art,cantidad:cant,precio_unitario:'-',subtotal:monto,idmovimiento:idMov,nota:''};
    const fila=agregarATabla(item); agregarItemVisual(item,fila);
    bootstrap.Modal.getInstance(document.getElementById('modalGenerico')).hide();
    showNotification&&showNotification("success");
}

/* ================================================================
   MODAL CLIENTE
================================================================ */
function initClienteModal() {
    document.getElementById('btnAbrirModalCliente').addEventListener('click',()=>new bootstrap.Modal(document.getElementById('modalCliente')).show());
    document.getElementById('btnBuscarDNI').addEventListener('click',buscarDNI);
    document.getElementById('btnBuscarRUC').addEventListener('click',buscarRUC);
    document.getElementById('btnRegistrarCliente').addEventListener('click',registrarCliente);
    document.getElementById('nombreCliente').addEventListener('input',buscarClienteAuto);
    document.addEventListener('click',e=>{const s=document.getElementById('sugerencias');const n=document.getElementById('nombreCliente');if(s&&!s.contains(e.target)&&!n.contains(e.target))s.innerHTML='';});
}

async function buscarDNI(){const dni=document.getElementById('numeroDocumentoPersona').value.trim();if(dni.length!==8){Swal.fire({icon:'warning',title:'DNI inválido',text:'Ingresa 8 dígitos',timer:2000,showConfirmButton:false});return;}try{const r=await fetch(`https://graphperu.daustinn.com/api/query/${dni}`);const d=await r.json();if(d&&d.names){document.getElementById('nombresPersona').value=d.names;document.getElementById('apellidosPersona').value=d.surnames;Swal.fire({icon:'success',title:'DNI encontrado',text:d.fullName,timer:2000,showConfirmButton:false});}}catch(e){Swal.fire({icon:'warning',title:'No encontrado',text:'Ingresa los datos manualmente',timer:2000,showConfirmButton:false});}}
async function buscarRUC(){const ruc=document.getElementById('numeroDocumentoEmpresa').value.trim();if(ruc.length!==11){Swal.fire({icon:'warning',title:'RUC inválido',text:'Ingresa 11 dígitos',timer:2000,showConfirmButton:false});return;}try{const r=await fetch(`https://graphperu.daustinn.com/api/query/${ruc}`);const d=await r.json();if(d&&d.name){document.getElementById('razonSocial').value=d.name;document.getElementById('nombreComercial').value=d.name;Swal.fire({icon:'success',title:'RUC encontrado',text:d.name,timer:2000,showConfirmButton:false});}}catch(e){Swal.fire({icon:'warning',title:'No encontrado',text:'Ingresa los datos manualmente',timer:2000,showConfirmButton:false});}}

function buscarClienteAuto(){
    const q=document.getElementById('nombreCliente').value.trim();
    const s=document.getElementById('sugerencias'); s.innerHTML='';
    if(!q.length)return;
    $.ajax({method:'POST',url:'logica/clssFiltro.php',data:{accion:'FILTROPERSONA',data:q,sucursal_id:<?php echo $sucursal_id;?>},success:r=>{
        try{const arr=JSON.parse(r);arr.forEach(p=>{const item=document.createElement('div');item.className='list-group-item list-group-item-action';item.textContent=p.persona_concatenada;item.onclick=()=>{document.getElementById('nombreCliente').value=p.persona_concatenada;document.getElementById('idPersona').textContent=p.id;s.innerHTML='';};s.appendChild(item);});}catch(e){}
    }});
}

function registrarCliente(){
    const esP=document.getElementById('pills-persona-tab').classList.contains('active');
    if(esP){
        const dni=document.getElementById('numeroDocumentoPersona').value.trim();
        const nom=document.getElementById('nombresPersona').value.trim();
        const ape=document.getElementById('apellidosPersona').value.trim();
        if(!dni||nom===''||ape===''){Swal.fire({icon:'warning',title:'Campos requeridos',text:'Completa los campos obligatorios',timer:2000,showConfirmButton:false});return;}
        fnRegistrarAPI({numero_documento:dni,nombres:nom,apellidos:ape,telefono_movil:document.getElementById('telefonoPersona').value||null,email:document.getElementById('emailPersona').value||null},`${dni} - ${nom} ${ape}`,'persona_id');
    }else{
        const ruc=document.getElementById('numeroDocumentoEmpresa').value.trim();
        const rz=document.getElementById('razonSocial').value.trim();
        const nc=document.getElementById('nombreComercial').value.trim();
        if(!ruc||!rz||!nc){Swal.fire({icon:'warning',title:'Campos requeridos',text:'Completa los campos obligatorios',timer:2000,showConfirmButton:false});return;}
        fnRegistrarAPI({numero_documento:ruc,razon_social:rz,nombre_comercial:nc,telefono_movil:document.getElementById('telefonoEmpresa').value||null,email:document.getElementById('emailEmpresa').value||null},`${ruc} - ${rz}`,'empresa_id');
    }
}
function fnRegistrarAPI(datos,nombreConcatenado,idKey){
    $.ajax({method:'POST',url:'logica/clssPersona.php',data:{accion:'REGISTRARPERSONARAPIDO',data:JSON.stringify(datos)},success:r=>{
        try{const res=JSON.parse(r);if(res.success){document.getElementById('idPersona').textContent=res[idKey];document.getElementById('nombreCliente').value=nombreConcatenado;bootstrap.Modal.getInstance(document.getElementById('modalCliente')).hide();Swal.fire({icon:'success',title:'Cliente registrado',timer:1500,showConfirmButton:false});}else Swal.fire({icon:'error',title:'Error',text:res.message});}catch(e){}
    }});
}

/* ================================================================
   CONFIRMAR RESERVA
================================================================ */
function initReservaModal(){
    document.getElementById('Reservar').addEventListener('click',()=>{
        const idCliente=document.getElementById('idPersona').textContent.trim();
        const total=document.getElementById('montoTotal').value;
        if(idCliente==='#'){Swal.fire({icon:'warning',title:'Cliente requerido',text:'Selecciona o registra un cliente',timer:2000,showConfirmButton:false});return;}
        const datos={usuario_id:<?php echo $_SESSION['id'];?>,cliente_id:idCliente,total,articulos:[]};
        document.querySelectorAll('#tabla_articulos tbody tr').forEach(row=>{
            datos.articulos.push({articulo_id:row.cells[0].textContent,minutos:0,costoxminuto:0,precio_unitario:parseFloat(row.cells[3].textContent)||0,cantidad:parseInt(row.cells[2].textContent)||1,sub_total:parseFloat(row.cells[4].textContent),movimiento_id:parseInt(row.cells[6].textContent),nota_archivo:row.cells[7].textContent||'Sin nota'});
        });
        $.ajax({method:'POST',url:'logica/clssVentaCorte.php',data:{accion:'REGISTRARRESERVA',data:JSON.stringify(datos)},
        success:r=>{try{const res=JSON.parse(r);if(res.success){Swal.fire({icon:'success',title:'¡Reserva exitosa!',text:'La reserva se registró correctamente',timer:1500,showConfirmButton:false}).then(()=>location.reload());}else Swal.fire({icon:'error',title:'Error',text:'No se pudo procesar la reserva'});}catch(e){Swal.fire({icon:'error',title:'Error',text:'Error al procesar la respuesta'});}},
        error:()=>Swal.fire({icon:'error',title:'Error',text:'Error en la comunicación con el servidor'})});
    });
}

function initEventListeners(){initSoloCorteModal();initImpresion3DModal();initClienteModal();initReservaModal();}
</script>
<?php include("pie.php"); ?>