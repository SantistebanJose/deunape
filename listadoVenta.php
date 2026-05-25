<?php
include("cabecera.php");
$sucursal_id = isset($_SESSION['sucursal_id']) ? $_SESSION['sucursal_id'] : null;

$datosDiarias   = fnListForVentasDiarias($sucursal_id);
$datosSemanales = fnListForVentasSemanales($sucursal_id);
$datosGeneral   = fnListForVentasTodasLasVentas($sucursal_id);

foreach ($datosDiarias   as &$d) { $d['accion_ajax'] = 'DETALLEVENTA_VENTA_ID'; } unset($d);
foreach ($datosSemanales as &$d) { $d['accion_ajax'] = 'DETALLEVENTA_VENTA_ID'; } unset($d);
foreach ($datosGeneral   as &$d) { $d['accion_ajax'] = 'DETALLEVENTA_VENTA_ID'; } unset($d);

// JSON seguro para insertar en JS
$jsDiarias   = json_encode($datosDiarias,   JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
$jsSemanal   = json_encode($datosSemanales, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
$jsGeneral   = json_encode($datosGeneral,   JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
?>

<!-- jQuery PRIMERO — todo lo demás depende de él -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.kpi-card{border-radius:14px;padding:18px 12px;display:flex;flex-direction:column;
  align-items:center;text-align:center;gap:5px;box-shadow:0 2px 10px rgba(0,0,0,.07);
  transition:transform .15s,box-shadow .15s;}
.kpi-card:hover{transform:translateY(-4px);box-shadow:0 6px 18px rgba(0,0,0,.13);}
.kpi-icon{font-size:1.6rem;opacity:.8;}
.kpi-label{font-size:.70rem;font-weight:700;text-transform:uppercase;letter-spacing:.6px;opacity:.70;}
.kpi-value{font-size:1.30rem;font-weight:800;line-height:1.2;}
.kpi-green {background:#d1fae5;color:#065f46;}
.kpi-blue  {background:#dbeafe;color:#1e40af;}
.kpi-orange{background:#ffedd5;color:#9a3412;}
.kpi-red   {background:#fee2e2;color:#991b1b;}
.kpi-purple{background:#ede9fe;color:#5b21b6;}
.kpi-yellow{background:#fef9c3;color:#854d0e;}
.kpi-teal  {background:#ccfbf1;color:#134e4a;}
.kpi-gray  {background:#f3f4f6;color:#374151;}
.modal-dialog-custom{max-width:900px;margin:0 auto;}
@media(max-width:768px){.modal-dialog-custom{max-width:80%;}}
@media(max-width:576px){.modal-dialog-custom{width:100%;margin:0 10px;max-width:100%;}}
.modal-content{padding:15px;}
th.sortable{cursor:pointer;user-select:none;white-space:nowrap;}
th.sortable:hover{background:rgba(0,0,0,.04);}
.pg-btn{border:1px solid #dee2e6;background:#fff;border-radius:6px;
  padding:3px 9px;font-size:13px;cursor:pointer;color:#333;transition:background .12s;}
.pg-btn:hover{background:#e9ecef;}
.pg-btn.active{background:#2a2f5b;color:#fff;border-color:#2a2f5b;}
.pg-btn:disabled{opacity:.4;cursor:default;}
</style>

<div class="container">
 <div class="page-inner">
  <div class="card text-start">
   <div class="card-body">
    <h4 class="card-title"><i class="fas fa-align-left"></i> Listado de Ventas</h4>
    <div class="card-sub">Selecciona de acuerdo a las ventas que necesites :)</div>
    <div class="card-body">

     <!-- Tabs -->
     <ul class="nav nav-pills nav-secondary nav-pills-no-bd" role="tablist">
      <li class="nav-item">
       <a class="nav-link active" data-bs-toggle="pill" href="#tab-diaria" role="tab">
        <i class="fas fa-clock"></i> Ventas del Día</a>
      </li>
      <li class="nav-item">
       <a class="nav-link" data-bs-toggle="pill" href="#tab-semanal" role="tab">
        <i class="fas fa-calendar-alt"></i> Ventas de la Semana</a>
      </li>
      <li class="nav-item">
       <a class="nav-link" data-bs-toggle="pill" href="#tab-general" role="tab">
        <i class="fas fa-chart-bar"></i> Todas las Ventas</a>
      </li>
      <li class="nav-item">
       <a class="nav-link" data-bs-toggle="pill" href="#tab-rango" role="tab">
        <i class="fas fa-calendar-week"></i> Ventas por Rango</a>
      </li>
     </ul>

     <!-- KPIs -->
     <div class="row g-3 my-3">
      <div class="col-6 col-md-3"><div class="kpi-card kpi-green">
       <div class="kpi-icon"><i class="fas fa-dollar-sign"></i></div>
       <div class="kpi-label">Ingresos</div>
       <div class="kpi-value">S/ <span id="kpiIngresos">0.00</span></div>
      </div></div>
      <div class="col-6 col-md-3"><div class="kpi-card kpi-blue">
       <div class="kpi-icon"><i class="fas fa-shopping-cart"></i></div>
       <div class="kpi-label">Transacciones</div>
       <div class="kpi-value"><span id="kpiTransacciones">0</span></div>
      </div></div>
      <div class="col-6 col-md-3"><div class="kpi-card kpi-orange">
       <div class="kpi-icon"><i class="fas fa-receipt"></i></div>
       <div class="kpi-label">Ticket Promedio</div>
       <div class="kpi-value">S/ <span id="kpiPromedio">0.00</span></div>
      </div></div>
      <div class="col-6 col-md-3"><div class="kpi-card kpi-red">
       <div class="kpi-icon"><i class="fas fa-tags"></i></div>
       <div class="kpi-label">Descuentos</div>
       <div class="kpi-value">S/ <span id="kpiDescuentos">0.00</span></div>
      </div></div>
      <div class="col-6 col-md-3"><div class="kpi-card kpi-purple">
       <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
       <div class="kpi-label">Pagadas</div>
       <div class="kpi-value"><span id="kpiPagadas">0</span></div>
      </div></div>
      <div class="col-6 col-md-3"><div class="kpi-card kpi-yellow">
       <div class="kpi-icon"><i class="fas fa-clock"></i></div>
       <div class="kpi-label">Al Crédito</div>
       <div class="kpi-value"><span id="kpiCredito">0</span></div>
      </div></div>
      <div class="col-6 col-md-3"><div class="kpi-card kpi-teal">
       <div class="kpi-icon"><i class="fas fa-arrow-up"></i></div>
       <div class="kpi-label">Venta Más Alta</div>
       <div class="kpi-value">S/ <span id="kpiMaxVenta">0.00</span></div>
      </div></div>
      <div class="col-6 col-md-3"><div class="kpi-card kpi-gray">
       <div class="kpi-icon"><i class="fas fa-calendar-day"></i></div>
       <div class="kpi-label">Días con Ventas</div>
       <div class="kpi-value"><span id="kpiDias">0</span></div>
      </div></div>
     </div>
     <div class="mb-3">
      <span class="badge bg-secondary px-3 py-2" id="kpiLabel">
       <i class="fas fa-calendar"></i> Ventas del Día
      </span>
     </div>

     <!-- Tab content -->
     <div class="tab-content mt-2 mb-3">

      <!-- Día -->
      <div class="tab-pane fade show active" id="tab-diaria" role="tabpanel">
       <div class="card"><div class="card-body" id="contenedor-diaria">
        <?php echo buildTablaHTML('diaria'); ?>
       </div></div>
      </div>

      <!-- Semana -->
      <div class="tab-pane fade" id="tab-semanal" role="tabpanel">
       <div class="card"><div class="card-body" id="contenedor-semanal">
        <?php echo buildTablaHTML('semanal'); ?>
       </div></div>
      </div>

      <!-- General -->
      <div class="tab-pane fade" id="tab-general" role="tabpanel">
       <div class="card"><div class="card-body" id="contenedor-general">
        <?php echo buildTablaHTML('general'); ?>
       </div></div>
      </div>

      <!-- Rango -->
      <div class="tab-pane fade" id="tab-rango" role="tabpanel">
       <div class="card"><div class="card-body">
        <div class="row g-2 align-items-end mb-3">
         <div class="col-md-4">
          <label class="form-label fw-semibold"><i class="fas fa-calendar-alt"></i> Fecha Inicio</label>
          <input type="date" id="fechaInicio" class="form-control">
         </div>
         <div class="col-md-4">
          <label class="form-label fw-semibold"><i class="fas fa-calendar-alt"></i> Fecha Fin</label>
          <input type="date" id="fechaFin" class="form-control">
         </div>
         <div class="col-md-4">
          <label class="form-label">&nbsp;</label>
          <button onclick="filtrarPorRango()" class="btn btn-primary btn-round w-100">
           <i class="fas fa-search"></i> Buscar
          </button>
         </div>
        </div>
        <div id="contenedor-rango">
         <p class="text-muted text-center py-4">
          <i class="fas fa-calendar-alt fa-2x d-block mb-2 opacity-50"></i>
          Selecciona un rango de fechas y haz clic en Buscar.
         </p>
        </div>
       </div></div>
      </div>

     </div><!-- /tab-content -->
    </div>
   </div>
  </div>
 </div>
</div>

<!-- Modal Detalle Venta -->
<div class="modal fade" id="modalDetalleVenta" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered modal-dialog-custom" role="document">
  <div class="modal-content">
   <button type="button" class="btn-close position-absolute top-0 end-0 m-2"
           data-bs-dismiss="modal" aria-label="Close"></button>
   <div class="card-body">
    <h4 class="card-title text-center" style="font-size:28px;">
     Venta de S/ <strong id="idMontoVenta"></strong></h4>
    <hr>
    <p class="card-text text-center" id="idUtilidad"></p>
    <div class="card-sub text-center">Aquí podrás revisar los datos de la venta.</div>
    <div class="row justify-content-center align-items-center sm-2">
     <div class="col-sm-6">
      <div class="card text-start"><div class="card-body" style="color:indigo">
       <h4 class="card-title" style="color:indigo"><i class="fas fa-user"></i> Cliente</h4>
       <p class="card-text" id="nombreCliente"></p><hr>
       <div><strong>N° DOCUMENTO:</strong> <span id="docCliente"></span></div>
       <div><strong>Celular:</strong> <span id="numCelCliente"></span></div>
       <div><strong>Correo:</strong> <span id="emailCliente"></span></div>
      </div></div>
     </div>
     <div class="col-sm-6">
      <div class="card text-start"><div class="card-body">
       <h4 class="card-text" style="color:green">
        <i class="fas fa-credit-card"></i> Monto Final: S/ <strong id="idMontoFinalVenta"></strong></h4>
       <p>La venta real fue de <strong id="idTotalOriginal"></strong></p>
       <div><strong>Atendido Por:</strong> <span id="idUsuario"></span></div>
       <div><strong>Fecha:</strong> <span id="idFechaVenta"></span></div>
       <div><strong>Hora:</strong> <span id="idHoraVenta"></span></div>
      </div></div>
     </div>
    </div>
    <div class="card"><div class="card-body">
     <div class="card-title">Detalle de Venta</div>
     <div class="table-responsive">
      <table id="tablaDetalle" class="table table-head-bg-secondary mt-4">
       <thead><tr>
        <th>Descripción</th><th>Corte</th>
        <th>Cant</th><th>P.Uni</th><th>Sub Total</th>
       </tr></thead>
       <tbody></tbody>
      </table>
     </div>
    </div></div>
   </div>
  </div>
 </div>
</div>

<?php
/* Genera sólo el esqueleto HTML de la tabla — sin JS inline */
function buildTablaHTML($key) {
    return '
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
      <div class="d-flex align-items-center gap-2">
        <label class="text-muted" style="font-size:13px;">Mostrar</label>
        <select class="form-select form-select-sm tv-pagesize" data-key="'.$key.'" style="width:72px;">
          <option value="10">10</option>
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="-1">Todos</option>
        </select>
      </div>
      <input type="text" class="form-control form-control-sm tv-busq" data-key="'.$key.'"
             style="max-width:220px;" placeholder="🔍 Buscar...">
    </div>
    <div class="table-responsive">
      <table class="table table-striped table-hover mb-2">
        <thead><tr>
          <th class="sortable" data-key="'.$key.'" data-col="venta_id">ID <span>↕</span></th>
          <th class="sortable" data-key="'.$key.'" data-col="codigo_tiket">N° Ticket <span>↕</span></th>
          <th class="sortable" data-key="'.$key.'" data-col="cliente">Cliente <span>↕</span></th>
          <th class="sortable" data-key="'.$key.'" data-col="dia_nombre">Día <span>↕</span></th>
          <th class="sortable" data-key="'.$key.'" data-col="fecha">Fecha <span>↕</span></th>
          <th>Hora</th>
          <th class="sortable" data-key="'.$key.'" data-col="total">Total <span>↕</span></th>
          <th class="sortable" data-key="'.$key.'" data-col="monto_venta_final">Total Final <span>↕</span></th>
          <th class="sortable" data-key="'.$key.'" data-col="perdida_utilidad">Pérdida <span>↕</span></th>
          <th>Estado</th>
          <th>Acción</th>
        </tr></thead>
        <tbody class="tv-tbody" data-key="'.$key.'"></tbody>
      </table>
    </div>
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-1">
      <span class="tv-info text-muted" data-key="'.$key.'" style="font-size:13px;"></span>
      <div class="tv-pag d-flex gap-1 flex-wrap" data-key="'.$key.'"></div>
    </div>';
}
?>

<script>
$(function () {

    /* ══════════════════════════════════════════
       DATOS desde PHP
    ══════════════════════════════════════════ */
    var DATOS = {
        diaria:  <?= $jsDiarias ?>,
        semanal: <?= $jsSemanal ?>,
        general: <?= $jsGeneral ?>
    };

    /* ══════════════════════════════════════════
       ESTADO por tabla
    ══════════════════════════════════════════ */
    var ST = {};

    function initTabla(key, data) {
        ST[key] = {
            data:     data,
            filtered: data.slice(),
            page:     0,
            pageSize: 10,
            sortCol:  'venta_id',
            sortAsc:  false
        };
        renderTabla(key);
    }

    /* ══════════════════════════════════════════
       FILTRAR
    ══════════════════════════════════════════ */
    function filtrar(key) {
        var st   = ST[key];
        if (!st) return;

        // Leer búsqueda y pageSize usando data-key
        var busq = $('.tv-busq[data-key="'+key+'"]').val().toLowerCase();
        var ps   = parseInt($('.tv-pagesize[data-key="'+key+'"]').val());
        st.pageSize = ps;

        var cols = ['venta_id','codigo_tiket','cliente','dia_nombre',
                    'fecha','hora','total','monto_venta_final',
                    'perdida_utilidad','estado_pago'];

        st.filtered = st.data.filter(function(r) {
            return cols.some(function(c) {
                return String(r[c]||'').toLowerCase().indexOf(busq) !== -1;
            });
        });

        sortFiltered(key);
        st.page = 0;
        renderTabla(key);
    }

    /* ══════════════════════════════════════════
       ORDENAR
    ══════════════════════════════════════════ */
    function sortFiltered(key) {
        var st  = ST[key];
        var col = st.sortCol;
        var asc = st.sortAsc;
        st.filtered.sort(function(a, b) {
            var va = a[col]||'', vb = b[col]||'';
            var na = parseFloat(va), nb = parseFloat(vb);
            if (!isNaN(na) && !isNaN(nb)) return asc ? na-nb : nb-na;
            return asc
                ? String(va).localeCompare(String(vb),'es')
                : String(vb).localeCompare(String(va),'es');
        });
    }

    /* ══════════════════════════════════════════
       RENDERIZAR
    ══════════════════════════════════════════ */
    function renderTabla(key) {
        var st    = ST[key];
        var total = st.filtered.length;
        var ps    = st.pageSize === -1 ? total : st.pageSize;
        var ini   = st.page * (ps || 1);
        var fin   = Math.min(ini + (ps || total), total);
        var filas = st.filtered.slice(ini, fin);

        var $tbody = $('.tv-tbody[data-key="'+key+'"]');
        $tbody.empty();

        if (filas.length === 0) {
            $tbody.html('<tr><td colspan="11" class="text-center text-muted py-3">' +
                '<i class="fas fa-inbox"></i> Sin resultados</td></tr>');
        } else {
            filas.forEach(function(r) {
                var $tr = $('<tr>');
                $tr.append('<td>'+esc(r.venta_id)+'</td>');
                $tr.append('<td>'+esc(r.codigo_tiket)+'</td>');
                $tr.append('<td>'+esc(r.cliente)+'</td>');
                $tr.append('<td>'+esc(r.dia_nombre)+'</td>');
                $tr.append('<td>'+esc(r.fecha)+'</td>');
                $tr.append('<td>'+esc(r.hora)+'</td>');
                $tr.append('<td>S/ '+esc(r.total)+'</td>');
                $tr.append('<td>S/ '+esc(r.monto_venta_final)+'</td>');
                $tr.append('<td>S/ '+esc(r.perdida_utilidad)+'</td>');
                $tr.append('<td>'+badge(r.estado_pago)+'</td>');

                var $btnDet = $('<button class="btn btn-success btn-sm btn-round">DETALLE</button>');
                var $btnPdf = $('<button class="btn btn-secondary btn-sm btn-round ms-1">PDF</button>');

                // Guardar dato directo en el elemento jQuery — sin serializar
                $btnDet.data('row', r).on('click', function() {
                    abrirModalDetalle($(this).data('row'));
                });
                $btnPdf.data('vid', r.venta_id).on('click', function() {
                    fn_abrir_pdf($(this).data('vid'));
                });

                var $tdAccion = $('<td><div class="d-flex justify-content-center"></div></td>');
                $tdAccion.find('div').append($btnDet).append($btnPdf);
                $tr.append($tdAccion);
                $tbody.append($tr);
            });
        }

        // Info
        $('.tv-info[data-key="'+key+'"]').text(
            total === 0 ? '0 registros'
                : 'Mostrando '+(ini+1)+' al '+fin+' de '+total+' registros'
        );

        renderPag(key, total, ps);
    }

    /* ══════════════════════════════════════════
       PAGINACIÓN
    ══════════════════════════════════════════ */
    function renderPag(key, total, ps) {
        var st     = ST[key];
        var $pag   = $('.tv-pag[data-key="'+key+'"]').empty();
        if (!ps || ps <= 0 || total <= ps) return;

        var totalPags = Math.ceil(total / ps);

        function mkBtn(label, page, disabled, active) {
            var $b = $('<button class="pg-btn'+(active?' active':'')+'">' + label + '</button>');
            $b.prop('disabled', disabled);
            if (!disabled) $b.on('click', function() {
                st.page = page; renderTabla(key);
            });
            return $b;
        }

        $pag.append(mkBtn('«', 0, st.page===0, false));
        $pag.append(mkBtn('‹', st.page-1, st.page===0, false));
        var pIni = Math.max(0, st.page-2);
        var pFin = Math.min(totalPags-1, st.page+2);
        for (var p = pIni; p <= pFin; p++) {
            $pag.append(mkBtn(p+1, p, false, p===st.page));
        }
        $pag.append(mkBtn('›', st.page+1, st.page>=totalPags-1, false));
        $pag.append(mkBtn('»', totalPags-1, st.page>=totalPags-1, false));
    }

    /* ══════════════════════════════════════════
       HELPERS
    ══════════════════════════════════════════ */
    function badge(estado) {
        var e = (estado||'').toUpperCase();
        var cls = e==='PAGADO' ? 'bg-success'
                : e==='CREDITO'? 'bg-warning text-dark'
                : e==='ANULADO'? 'bg-danger'
                : 'bg-secondary';
        return '<span class="badge '+cls+'">'+esc(estado)+'</span>';
    }
    function esc(v) {
        if (v==null) return '—';
        return String(v).replace(/&/g,'&amp;').replace(/</g,'&lt;')
                        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    /* ══════════════════════════════════════════
       KPIs
    ══════════════════════════════════════════ */
    function actualizarKpis(datos, label) {
        var ing=0, desc=0, maxV=0, pag=0, cred=0, dias={};
        datos.forEach(function(d) {
            var m = parseFloat(d.monto_venta_final)||0;
            var p = parseFloat(d.perdida_utilidad)||0;
            ing  += m;
            desc += (p<0 ? Math.abs(p) : 0);
            if (m>maxV) maxV=m;
            var ep = (d.estado_pago||'').toUpperCase();
            if (ep==='PAGADO')  pag++;
            if (ep==='CREDITO') cred++;
            if (d.fecha) dias[d.fecha]=1;
        });
        var prom = datos.length ? ing/datos.length : 0;
        $('#kpiIngresos').text(ing.toFixed(2));
        $('#kpiTransacciones').text(datos.length);
        $('#kpiPromedio').text(prom.toFixed(2));
        $('#kpiDescuentos').text(desc.toFixed(2));
        $('#kpiPagadas').text(pag);
        $('#kpiCredito').text(cred);
        $('#kpiMaxVenta').text(maxV.toFixed(2));
        $('#kpiDias').text(Object.keys(dias).length);
        $('#kpiLabel').html('<i class="fas fa-calendar"></i> '+label);
    }

    /* ══════════════════════════════════════════
       EVENTOS — delegados para capturar elementos
       generados dinámicamente también
    ══════════════════════════════════════════ */
    $(document).on('input', '.tv-busq', function() {
        filtrar($(this).data('key'));
    });
    $(document).on('change', '.tv-pagesize', function() {
        filtrar($(this).data('key'));
    });
    $(document).on('click', 'th.sortable', function() {
        var key = $(this).data('key');
        var col = $(this).data('col');
        var st  = ST[key];
        if (!st) return;
        if (st.sortCol === col) {
            st.sortAsc = !st.sortAsc;
        } else {
            st.sortCol = col;
            st.sortAsc = true;
        }
        // Actualizar flechas
        $(this).closest('table').find('th.sortable span').text('↕');
        $(this).find('span').text(st.sortAsc ? '▲' : '▼');
        sortFiltered(key);
        st.page = 0;
        renderTabla(key);
    });

    // KPIs al cambiar tab
    $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
        var href = $(e.target).attr('href');
        if (href==='#tab-diaria')  actualizarKpis(DATOS.diaria,  'Ventas del Día');
        if (href==='#tab-semanal') actualizarKpis(DATOS.semanal, 'Ventas de la Semana');
        if (href==='#tab-general') actualizarKpis(DATOS.general, 'Todas las Ventas');
    });

    /* ══════════════════════════════════════════
       INIT — arrancar las 3 tablas
    ══════════════════════════════════════════ */
    initTabla('diaria',  DATOS.diaria);
    initTabla('semanal', DATOS.semanal);
    initTabla('general', DATOS.general);
    actualizarKpis(DATOS.diaria, 'Ventas del Día');

    /* ══════════════════════════════════════════
       FILTRO POR RANGO — expuesto globalmente
    ══════════════════════════════════════════ */
    var HTML_RANGO = <?= json_encode(buildTablaHTML('rango')) ?>;

    window.filtrarPorRango = function() {
        var fi = $('#fechaInicio').val();
        var ff = $('#fechaFin').val();
        if (!fi || !ff) { Swal.fire('Atención','Selecciona ambas fechas.','warning'); return; }
        if (fi > ff)    { Swal.fire('Error','La fecha inicio no puede ser mayor que la fecha fin.','error'); return; }

        Swal.fire({ title:'Buscando...', allowOutsideClick:false,
                    didOpen: function(){ Swal.showLoading(); } });

        $.ajax({
            url:      'logica/clssConsultas.php',
            type:     'POST',
            data:     { 
                accion:       'VENTAS_POR_RANGO', 
                fecha_inicio: fi, 
                fecha_fin:    ff,
                sucursal_id:  <?= (int)($sucursal_id ?? 0) ?>   // ← ESTA ES LA LÍNEA NUEVA
            },
            dataType: 'json',
            success: function(datos) {
                Swal.close();
                datos.forEach(function(d){ d.accion_ajax='DETALLEVENTA_VENTA_ID'; });
                $('#contenedor-rango').html(HTML_RANGO);
                initTabla('rango', datos);
                actualizarKpis(datos, 'Rango: '+fi+' → '+ff);
            },
            error: function(xhr) {
                Swal.close();
                console.error('Error rango:', xhr.responseText);
                Swal.fire('Error','No se pudieron cargar las ventas.','error');
            }
        });
    };

    /* ══════════════════════════════════════════
       PDF
    ══════════════════════════════════════════ */
    window.fn_abrir_pdf = function(id_venta) {
        fetch('ticket.php?accion=token&id='+parseInt(id_venta))
            .then(function(r){ return r.json(); })
            .then(function(u){
                Swal.fire({
                    title: '¿Cómo deseas ver el comprobante?',
                    html:
                        '<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:8px;">'+
                        '<button onclick="window.open(\''+u.ticket+'\',\'_blank\');Swal.close();" style="background:#2a2f5b;color:white;border:none;border-radius:12px;padding:14px 10px;cursor:pointer;font-weight:700;font-size:.88rem;">🖨️ Ticket<br><small style=\'font-weight:400;opacity:.8;\'>80mm / POS</small></button>'+
                        '<button onclick="window.open(\''+u.a4+'\',\'_blank\');Swal.close();" style="background:#2a2f5b;color:white;border:none;border-radius:12px;padding:14px 10px;cursor:pointer;font-weight:700;font-size:.88rem;">📄 A4<br><small style=\'font-weight:400;opacity:.8;\'>Hoja completa</small></button>'+
                        '<button onclick="window.open(\''+u.a5+'\',\'_blank\');Swal.close();" style="background:#2a2f5b;color:white;border:none;border-radius:12px;padding:14px 10px;cursor:pointer;font-weight:700;font-size:.88rem;">📋 A5<br><small style=\'font-weight:400;opacity:.8;\'>Medio oficio</small></button>'+
                        '<button onclick="window.open(\''+u.pantalla+'\',\'_blank\');Swal.close();" style="background:#11998e;color:white;border:none;border-radius:12px;padding:14px 10px;cursor:pointer;font-weight:700;font-size:.88rem;">🌐 Pantalla<br><small style=\'font-weight:400;opacity:.8;\'>HTML / WhatsApp</small></button>'+
                        '</div>',
                    showConfirmButton:false, showCloseButton:true, width:360
                });
            });
    };

    /* ══════════════════════════════════════════
       MODAL DETALLE
    ══════════════════════════════════════════ */
    window.abrirModalDetalle = function(d) {
        $('#modalDetalleVenta').modal('show');
        $('#nombreCliente').text(d.cliente||'—');
        $('#docCliente').text(d.numero_doc_cliente||'—');
        $('#numCelCliente').text(d.telefonomovil_cliente||'—');
        $('#emailCliente').text(d.email_cliente||'—');
        $('#idMontoVenta').text(d.monto_venta_final||'0');
        $('#idMontoFinalVenta').text(d.monto_venta_final||'0');
        $('#idTotalOriginal').text('S/ '+(d.total||'0'));
        $('#idFechaVenta').text(d.fecha||'—');
        $('#idHoraVenta').text(d.hora||'—');
        $('#idUsuario').text(d.usuario||'—');

        var perdida = parseFloat(d.perdida_utilidad)||0;
        var $u = $('#idUtilidad');
        if (perdida < 0) {
            $u.html("<span style='color:red'>En esta venta, PERDISTE un margen de utilidad de <strong>S/"+
                (perdida*-1).toFixed(2)+".</strong></span>");
        } else if ((d.estado_pago||'').toUpperCase()==='CREDITO') {
            var ef = d.estado_final||'';
            $u.html(ef==='VENTA REALIZADA AL CREDITO - AUN DEBE'
                ? "<b>"+ef+"</b><br><span style='color:green'>Abonado S/ "+d.acumulado_deuda+
                  "</span><span style='color:orange'><br><strong>Revisar sección Crédito</strong></span>"
                : "<b>"+ef+"</b><br><span style='color:orange'>Crédito. <strong style='color:green'>Deuda pagada</strong></span>");
        } else {
            $u.html("<span style='color:green'><b>En esta venta, no hiciste rebajas :)</b></span>");
        }

        $.ajax({
            url:'logica/clssConsultas.php', type:'POST',
            data:{ accion:d.accion_ajax, venta_id:d.venta_id },
            dataType:'json',
            success:function(arts){
                var $tbody = $('#tablaDetalle tbody').empty();
                arts.forEach(function(a){
                    var tc = (a.minutos!=null && a.costo_por_minuto!=null)
                        ? 'S/ '+(a.costo_por_minuto*a.minutos)
                        : (a.minutos===null && a.costo_por_minuto===null ? '-' : 'S/ '+(a.sub_total||'-'));
                    var $tr = $('<tr>');
                    $tr.append($('<td>').html(a.descripcion));
                    $tr.append($('<td>').text(tc));
                    $tr.append($('<td>').text(a.cantidad||'-'));
                    $tr.append($('<td>').text(a.precio_unitario_articulo!=null?'S/ '+a.precio_unitario_articulo:'-'));
                    $tr.append($('<td>').text('S/ '+(a.sub_total||'-')));
                    $tbody.append($tr);
                });
            }
        });
    };

}); // fin $(function)
</script>

<?php include("pie.php"); ?>
