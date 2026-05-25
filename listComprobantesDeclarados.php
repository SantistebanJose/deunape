<?php
include("cabecera.php");
?>

<style>
    /* ── Modal XML ── */
    #modal_xml .modal-dialog { max-width: 860px; }

    .xml-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #1e1e2e;
        padding: 10px 16px;
        border-radius: 10px 10px 0 0;
    }
    .xml-toolbar span { color: #cdd6f4; font-size: .82rem; font-family: monospace; }
    .btn-copiar-xml {
        background: #313244; border: none; color: #cdd6f4;
        border-radius: 6px; padding: 4px 12px; font-size: .78rem;
        cursor: pointer; transition: background .15s;
    }
    .btn-copiar-xml:hover { background: #45475a; }
    .btn-copiar-xml.copiado { background: #a6e3a1; color: #1e1e2e; }

    #xml_contenido {
        background: #1e1e2e;
        color: #cdd6f4;
        font-family: 'Fira Code', 'Cascadia Code', monospace;
        font-size: .75rem;
        line-height: 1.6;
        padding: 16px;
        border-radius: 0 0 10px 10px;
        max-height: 520px;
        overflow-y: auto;
        white-space: pre;
        tab-size: 2;
    }

    .xml-tag      { color: #89b4fa; }
    .xml-attr     { color: #f38ba8; }
    .xml-value    { color: #a6e3a1; }
    .xml-text     { color: #cdd6f4; }
    .xml-comment  { color: #6c7086; font-style: italic; }
    .xml-decl     { color: #fab387; }

    #xml_spinner {
        text-align: center;
        padding: 40px 0;
        color: #6c7086;
    }

    /* ══════════════════════════════════════════
       BOTONES DE ACCIÓN
    ══════════════════════════════════════════ */
    .acciones-grupo {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-accion-xml {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        font-size: .75rem;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: filter .15s, transform .1s;
        background: #1a7a3c;
        color: #fff;
        white-space: nowrap;
    }
    .btn-accion-xml:hover  { filter: brightness(1.15); transform: translateY(-1px); }
    .btn-accion-xml:active { transform: translateY(0); }

    .pdf-group {
        display: inline-flex;
        align-items: stretch;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,.18);
    }

    .btn-pdf-main {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        font-size: .75rem;
        font-weight: 600;
        border: none;
        border-radius: 6px 0 0 6px;
        cursor: pointer;
        transition: filter .15s, transform .1s;
        background: #495057;
        color: #fff;
        white-space: nowrap;
        border-right: 1px solid rgba(255,255,255,0.15);
    }
    .btn-pdf-main:hover  { filter: brightness(1.2); transform: translateY(-1px); }
    .btn-pdf-main:active { transform: translateY(0); }

    .btn-pdf-arrow {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        border: none;
        border-radius: 0 6px 6px 0;
        cursor: pointer;
        background: #3d4349;
        color: #fff;
        transition: filter .15s;
    }
    .btn-pdf-arrow:hover { filter: brightness(1.3); }
    .btn-pdf-arrow::after { display: none !important; }

    .pdf-group .dropdown-menu {
        min-width: 195px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 8px 24px rgba(0,0,0,.13);
        padding: 5px;
        margin-top: 4px !important;
    }
    .pdf-group .dropdown-menu .dropdown-item {
        border-radius: 6px;
        padding: 7px 12px;
        color: #374151;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: background .12s;
        font-size: .78rem;
        font-weight: 500;
    }
    .pdf-group .dropdown-menu .dropdown-item:hover {
        background: #f1f5f9;
        color: #111827;
    }
    .pdf-group .dropdown-menu .dropdown-item i { width: 14px; text-align: center; opacity: .65; }
    .pdf-group .dropdown-divider { margin: 4px 0; border-color: #e2e8f0; }
    .fmt-badge {
        margin-left: auto;
        font-size: .64rem;
        background: #e2e8f0;
        color: #64748b;
        border-radius: 4px;
        padding: 1px 6px;
        font-weight: 700;
        letter-spacing: .3px;
    }

    /* ══════════════════════════════════════════
       FILTRO DE FECHAS — TABS
    ══════════════════════════════════════════ */
    .filtro-tabs-wrap {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-bottom: 14px;
    }

    .filtro-tab {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 16px;
        border-radius: 50px;
        border: 1px solid #dee2e6;
        background: #fff;
        color: #495057;
        font-size: .82rem;
        font-weight: 500;
        cursor: pointer;
        transition: background .15s, color .15s, border-color .15s, box-shadow .15s;
        white-space: nowrap;
        user-select: none;
    }
    .filtro-tab:hover { background: #f1f3f5; border-color: #adb5bd; }
    .filtro-tab.activo {
        background: #3b1fa8;
        color: #fff;
        border-color: #3b1fa8;
        box-shadow: 0 2px 8px rgba(59,31,168,.35);
    }
    .filtro-tab i { font-size: .85rem; opacity: .85; }

    /* Panel rango */
    #panel-rango-fechas {
        display: none;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 10px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 14px;
    }
    #panel-rango-fechas.visible { display: flex; }

    #panel-rango-fechas label {
        font-size: .76rem;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: .4px;
        display: block;
        margin-bottom: 4px;
    }
    #panel-rango-fechas input[type="date"] {
        border: 1px solid #ced4da;
        border-radius: 8px;
        padding: 6px 10px;
        font-size: .83rem;
        color: #212529;
        background: #fff;
        outline: none;
        transition: border-color .15s;
    }
    #panel-rango-fechas input[type="date"]:focus {
        border-color: #3b1fa8;
        box-shadow: 0 0 0 3px rgba(59,31,168,.12);
    }
    .btn-aplicar-rango {
        padding: 7px 18px;
        background: #3b1fa8;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: .82rem;
        font-weight: 600;
        cursor: pointer;
        transition: filter .15s;
    }
    .btn-aplicar-rango:hover { filter: brightness(1.15); }
    .btn-limpiar-rango {
        padding: 7px 14px;
        background: transparent;
        color: #6c757d;
        border: 1px solid #ced4da;
        border-radius: 8px;
        font-size: .82rem;
        cursor: pointer;
        transition: background .12s;
    }
    .btn-limpiar-rango:hover { background: #e9ecef; }

    /* Barra resumen */
    #resumen-declarado {
        display: none;
        flex-wrap: wrap;
        align-items: center;
        gap: 20px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 12px;
        padding: 12px 18px;
        margin-bottom: 14px;
    }
    #resumen-declarado.visible { display: flex; }

    .res-item { display: flex; flex-direction: column; gap: 1px; }
    .res-lbl  { font-size: .72rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .4px; }
    .res-val  { font-size: 1.15rem; font-weight: 700; color: #111827; }
    .res-val.monto { color: #15803d; }
    .res-periodo { font-size: .8rem; color: #374151; margin-left: auto; }
</style>

<div class="container">
    <div class="page-inner">
        <div class="card text-start">
            <div class="card-body">
                <h4 class="card-title">
                    <i class="fab fa-staylinked"></i> Ventas Declaradas a SUNAT
                </h4>
                <div class="card-sub mb-3">
                    Marque <strong>en el botón verde</strong> los comprobantes que desea <strong>revisar.</strong>
                </div>

                <!-- ══ TABS DE FILTRO ══ -->
                <div class="filtro-tabs-wrap">
                    <button class="filtro-tab activo" data-modo="hoy" onclick="ftab_cambiar('hoy', this)">
                        <i class="fas fa-clock"></i> Ventas Declaradas del Día
                    </button>
                    <button class="filtro-tab" data-modo="semana" onclick="ftab_cambiar('semana', this)">
                        <i class="fas fa-calendar-week"></i> Ventas Declaradas de la Semana
                    </button>
                    <button class="filtro-tab" data-modo="todas" onclick="ftab_cambiar('todas', this)">
                        <i class="fas fa-chart-bar"></i> Todas las Ventas Declaradas
                    </button>
                    <button class="filtro-tab" data-modo="rango" onclick="ftab_cambiar('rango', this)">
                        <i class="fas fa-calendar"></i> Ventas Declaradas por Rango
                    </button>
                </div>

                <!-- ══ PANEL RANGO PERSONALIZADO ══ -->
                <div id="panel-rango-fechas">
                    <div>
                        <label>Desde</label>
                        <input type="date" id="ftab_desde">
                    </div>
                    <div>
                        <label>Hasta</label>
                        <input type="date" id="ftab_hasta">
                    </div>
                    <button class="btn-aplicar-rango" onclick="ftab_aplicar_rango()">
                        <i class="fas fa-search me-1"></i> Filtrar
                    </button>
                    <button class="btn-limpiar-rango" onclick="ftab_limpiar_rango()">Limpiar</button>
                </div>

                <!-- ══ BARRA DE RESUMEN ══ -->
                <div id="resumen-declarado">
                    <div class="res-item">
                        <span class="res-lbl">Comprobantes</span>
                        <span class="res-val" id="res-cantidad">—</span>
                    </div>
                    <div class="res-item">
                        <span class="res-lbl">Total Declarado a SUNAT</span>
                        <span class="res-val monto" id="res-total">—</span>
                    </div>
                    <span class="res-periodo" id="res-etiqueta"></span>
                </div>

                <!-- ══ TABLA ══ -->
                <div class="tablita-responsive">
                    <div class="table-responsive">
                        <table id="tabla_boletas" class="dataTable display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>SERIE</th>
                                    <th>CORRELATIVO</th>
                                    <th>Fecha Emisión</th>
                                    <th>Total</th>
                                    <th>SUNAT</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (listComprobantesDeclarados($_SESSION["sucursal_id"]) as $datos): ?>
                                    <tr>
                                        <td><?php echo $datos["id"] ?></td>
                                        <td><?php echo $datos["serie"] ?></td>
                                        <td><?php echo $datos["correlativo_texto"] ?></td>
                                        <td><?php echo $datos["fecha_emision"] ?></td>
                                        <td>S/ <?php echo number_format($datos["total"], 2) ?></td>
                                        <td>
                                            <?php if (!empty($datos["mensaje_sunat"])): ?>
                                                <span class="badge bg-success" style="font-size:.72rem;">
                                                    <i class="fas fa-check-circle me-1"></i><?php echo htmlspecialchars($datos["mensaje_sunat"]) ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="acciones-grupo">

                                                <!-- Botón XML -->
                                                <button
                                                    class="btn-accion-xml"
                                                    onclick='fn_ver_xml(
                                                        <?php echo json_encode($datos["nombrexml"] ?? ""); ?>,
                                                        <?php echo (int)($_SESSION["sucursal_id"] ?? 1); ?>
                                                    )'
                                                    title="Ver XML enviado a SUNAT">
                                                    <i class="fas fa-code"></i> XML
                                                </button>

                                                <!-- PDF + Dropdown formatos -->
                                                <div class="pdf-group dropdown">
                                                    <button
                                                        class="btn-pdf-main"
                                                        onclick='fn_abrir_pdf(<?php echo $datos["venta_id"] ?>)'
                                                        title="Abrir ticket PDF (80mm)">
                                                        <i class="fas fa-file-pdf"></i> PDF
                                                    </button>
                                                    <button
                                                        class="btn-pdf-arrow dropdown-toggle"
                                                        data-bs-toggle="dropdown"
                                                        aria-expanded="false"
                                                        title="Elegir formato de impresión">
                                                        <i class="fas fa-chevron-down" style="font-size:.55rem;"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="#"
                                                               onclick='fn_abrir_pdf(<?php echo $datos["venta_id"] ?>, "ticket"); return false;'>
                                                                <i class="fas fa-receipt"></i>
                                                                Ticket POS
                                                                <span class="fmt-badge">80mm</span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#"
                                                               onclick='fn_abrir_pdf(<?php echo $datos["venta_id"] ?>, "a4"); return false;'>
                                                                <i class="fas fa-file-pdf"></i>
                                                                Hoja completa
                                                                <span class="fmt-badge">A4</span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#"
                                                               onclick='fn_abrir_pdf(<?php echo $datos["venta_id"] ?>, "a5"); return false;'>
                                                                <i class="fas fa-file-alt"></i>
                                                                Media hoja
                                                                <span class="fmt-badge">A5</span>
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item" href="#"
                                                               onclick='fn_abrir_pdf(<?php echo $datos["venta_id"] ?>, "pantalla"); return false;'>
                                                                <i class="fas fa-mobile-alt"></i>
                                                                Ver en pantalla
                                                                <span class="fmt-badge">QR</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>

                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ══ MODAL XML ══ -->
<div class="modal fade" id="modal_xml" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:14px; overflow:hidden;">
            <div class="modal-header" style="background:#2a2f5b; color:white; border:none;">
                <h5 class="modal-title mb-0">
                    <i class="fas fa-code me-2"></i>
                    XML enviado a SUNAT — <span id="xml_nombre" style="font-family:monospace; font-size:.9rem;"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" style="background:#1e1e2e;">

                <div id="xml_spinner">
                    <div class="spinner-border text-secondary" role="status"></div>
                    <p class="mt-2" style="color:#6c7086; font-size:.85rem;">Cargando XML...</p>
                </div>

                <div id="xml_visor" style="display:none;">
                    <div class="xml-toolbar">
                        <span id="xml_toolbar_nombre"></span>
                        <button class="btn-copiar-xml" onclick="fn_copiar_xml()" id="btn_copiar">
                            <i class="fas fa-copy me-1"></i>Copiar
                        </button>
                    </div>
                    <pre id="xml_contenido"></pre>
                </div>

                <div id="xml_error" style="display:none; padding:32px; text-align:center;">
                    <i class="fas fa-exclamation-circle fa-2x text-danger mb-2"></i>
                    <p id="xml_error_msg" style="color:#f38ba8;"></p>
                </div>

            </div>
            <div class="modal-footer" style="background:#181825; border:none;">
                <small style="color:#6c7086;">
                    <i class="fas fa-info-circle me-1"></i>
                    Archivo guardado en <code style="color:#89b4fa;">sucursales/{sucursal}/xml/</code>
                </small>
                <button type="button" class="btn btn-sm btn-secondary ms-auto" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    $(".dataTable").DataTable({
        language: {
            sProcessing   : "Procesando...",
            sLengthMenu   : "Mostrar _MENU_ registros",
            sZeroRecords  : "No se encontraron resultados",
            sEmptyTable   : "Ningún dato disponible",
            sInfo         : "Registros del _START_ al _END_ de _TOTAL_",
            sInfoEmpty    : "Registros del 0 al 0 de 0",
            sInfoFiltered : "(filtrado de _MAX_ registros)",
            sSearch       : "Buscar:",
            oPaginate     : { sFirst:"Primero", sPrevious:"Anterior", sNext:"Siguiente", sLast:"Último" }
        },
        order: [[0, "desc"]]
    });

    // Aplicar filtro "Hoy" al iniciar
    setTimeout(function() {
        const hoy = ftab_hoy();
        ftab_aplicar(hoy, hoy, 'Hoy — ' + hoy.split('-').reverse().join('/'));
    }, 300);
});

/* ══════════════════════════════════════════════════════════
   FILTRO POR FECHAS
══════════════════════════════════════════════════════════ */

let _ftab_filtro_activo = null;

function ftab_hoy() {
    return new Date().toISOString().split('T')[0]; // YYYY-MM-DD
}

function ftab_inicio_semana() {
    const d   = new Date();
    const dia = d.getDay() === 0 ? 6 : d.getDay() - 1; // lunes = 0
    d.setDate(d.getDate() - dia);
    return d.toISOString().split('T')[0];
}

// Normaliza DD/MM/YYYY → YYYY-MM-DD para poder comparar
function ftab_iso(texto) {
    if (!texto) return '';
    if (texto.includes('/')) {
        const [d, m, y] = texto.split('/');
        return `${y}-${m.padStart(2,'0')}-${d.padStart(2,'0')}`;
    }
    return texto;
}

// Aplica el filtro de rango en el DataTable
function ftab_aplicar(desde, hasta, etiqueta) {
    // Quitar filtro anterior
    if (_ftab_filtro_activo !== null) {
        const idx = $.fn.dataTable.ext.search.indexOf(_ftab_filtro_activo);
        if (idx > -1) $.fn.dataTable.ext.search.splice(idx, 1);
    }
    _ftab_filtro_activo = function(settings, data) {
        const fecha = ftab_iso(data[3]); // columna índice 3 = Fecha Emisión
        if (!fecha) return true;
        return (!desde || fecha >= desde) && (!hasta || fecha <= hasta);
    };
    $.fn.dataTable.ext.search.push(_ftab_filtro_activo);
    $('#tabla_boletas').DataTable().draw();
    ftab_mostrar_resumen(etiqueta);
}

// Quita cualquier filtro activo
function ftab_quitar_filtro() {
    if (_ftab_filtro_activo !== null) {
        const idx = $.fn.dataTable.ext.search.indexOf(_ftab_filtro_activo);
        if (idx > -1) $.fn.dataTable.ext.search.splice(idx, 1);
        _ftab_filtro_activo = null;
    }
    if ($.fn.DataTable.isDataTable('#tabla_boletas')) {
        $('#tabla_boletas').DataTable().draw();
    }
    document.getElementById('resumen-declarado').classList.remove('visible');
}

// Calcula y muestra la barra de totales
function ftab_mostrar_resumen(etiqueta) {
    const tabla = $('#tabla_boletas').DataTable();
    let cantidad = 0, total = 0;
    tabla.rows({ search: 'applied' }).every(function() {
        const d = this.data();
        cantidad++;
        // columna 4 = "S/ 1,234.56"
        const monto = parseFloat(
            (d[4] || '').replace('S/','').replace(/,/g,'').trim()
        ) || 0;
        total += monto;
    });
    document.getElementById('res-cantidad').textContent = cantidad;
    document.getElementById('res-total').textContent =
        'S/ ' + total.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('res-etiqueta').textContent = etiqueta;
    document.getElementById('resumen-declarado').classList.add('visible');
}

// Cambia el tab activo y ejecuta el filtro correspondiente
function ftab_cambiar(modo, btn) {
    document.querySelectorAll('.filtro-tab').forEach(t => t.classList.remove('activo'));
    btn.classList.add('activo');

    const panelRango = document.getElementById('panel-rango-fechas');
    panelRango.classList.toggle('visible', modo === 'rango');

    const hoy = ftab_hoy();

    if (modo === 'hoy') {
        ftab_aplicar(hoy, hoy,
            'Hoy — ' + hoy.split('-').reverse().join('/'));

    } else if (modo === 'semana') {
        const inicio = ftab_inicio_semana();
        ftab_aplicar(inicio, hoy,
            'Esta semana (' +
            inicio.split('-').reverse().join('/') + ' → ' +
            hoy.split('-').reverse().join('/') + ')');

    } else if (modo === 'todas') {
        ftab_quitar_filtro();

    } else if (modo === 'rango') {
        ftab_quitar_filtro();
    }
}

// Botón "Filtrar" del panel de rango
function ftab_aplicar_rango() {
    const desde = document.getElementById('ftab_desde').value;
    const hasta  = document.getElementById('ftab_hasta').value;
    if (!desde && !hasta) {
        alert('Selecciona al menos una fecha.');
        return;
    }
    const etq =
        (desde ? desde.split('-').reverse().join('/') : '...') +
        ' → ' +
        (hasta  ? hasta.split('-').reverse().join('/')  : '...');
    ftab_aplicar(desde || '', hasta || '', etq);
}

// Botón "Limpiar" del panel de rango
function ftab_limpiar_rango() {
    document.getElementById('ftab_desde').value = '';
    document.getElementById('ftab_hasta').value  = '';
    ftab_quitar_filtro();
}

/* ══════════════════════════════════════════════════════════
   VER XML
══════════════════════════════════════════════════════════ */
let _xmlTextoActual = '';

function fn_ver_xml(nombrexml, sucursal) {
    if (!nombrexml) { alert('Este comprobante no tiene XML registrado.'); return; }
    document.getElementById('xml_spinner').style.display = 'block';
    document.getElementById('xml_visor').style.display   = 'none';
    document.getElementById('xml_error').style.display   = 'none';
    document.getElementById('xml_nombre').textContent    = nombrexml + '.XML';
    _xmlTextoActual = '';
    $('#modal_xml').modal('show');

    fetch(`ticket.php?accion=xml&nombrexml=${encodeURIComponent(nombrexml)}&sucursal=${sucursal}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('xml_spinner').style.display = 'none';
            if (data.error) {
                document.getElementById('xml_error_msg').textContent = data.error;
                document.getElementById('xml_error').style.display   = 'block';
                return;
            }
            _xmlTextoActual = data.xml;
            document.getElementById('xml_toolbar_nombre').textContent = nombrexml + '.XML';
            document.getElementById('xml_contenido').innerHTML = colorearXML(data.xml);
            document.getElementById('xml_visor').style.display = 'block';
        })
        .catch(err => {
            document.getElementById('xml_spinner').style.display = 'none';
            document.getElementById('xml_error_msg').textContent = 'Error de conexión: ' + err.message;
            document.getElementById('xml_error').style.display   = 'block';
        });
}

/* ══════════════════════════════════════════════════════════
   COPIAR XML
══════════════════════════════════════════════════════════ */
function fn_copiar_xml() {
    if (!_xmlTextoActual) return;
    navigator.clipboard.writeText(_xmlTextoActual).then(() => {
        const btn = document.getElementById('btn_copiar');
        btn.classList.add('copiado');
        btn.innerHTML = '<i class="fas fa-check me-1"></i>Copiado';
        setTimeout(() => {
            btn.classList.remove('copiado');
            btn.innerHTML = '<i class="fas fa-copy me-1"></i>Copiar';
        }, 2000);
    });
}

/* ══════════════════════════════════════════════════════════
   COLOREAR XML
══════════════════════════════════════════════════════════ */
function colorearXML(xml) {
    const esc = s => s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    return esc(xml)
        .replace(/(&lt;\?xml[^?]*\?&gt;)/g, '<span class="xml-decl">$1</span>')
        .replace(/(&lt;!--[\s\S]*?--&gt;)/g, '<span class="xml-comment">$1</span>')
        .replace(/(&lt;\/[\w:]+&gt;)/g, '<span class="xml-tag">$1</span>')
        .replace(/(&lt;[\w:]+)((?:\s+[\w:]+="[^"]*")*)(\/?)(&gt;)/g, (m, tag, attrs, self, end) => {
            const atColored = attrs.replace(/([\w:]+)(="[^"]*")/g,
                '<span class="xml-attr">$1</span><span class="xml-value">$2</span>');
            return `<span class="xml-tag">${tag}</span>${atColored}<span class="xml-tag">${self}${end}</span>`;
        });
}

/* ══════════════════════════════════════════════════════════
   ABRIR PDF
══════════════════════════════════════════════════════════ */
function fn_abrir_pdf(id_venta, formato) {
    formato = formato || 'ticket';
    fetch("ticket.php?accion=token&id=" + parseInt(id_venta))
        .then(r => r.json())
        .then(data => {
            const url = data[formato] || data.ticket;
            if (!url) { alert('No se pudo generar el enlace.'); return; }
            window.open(url, "_blank");
        })
        .catch(() => alert('Error al conectar con el servidor.'));
}
</script>

<?php include("pie.php"); ?>