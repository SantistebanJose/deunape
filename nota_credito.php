<?php
include("cabecera.php");
?>

<div class="container">
    <div class="page-inner">

        <!-- ═══════════════════════════════════════════════════
             TARJETA PRINCIPAL — LISTADO
        ════════════════════════════════════════════════════ -->
        <div class="card text-start">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Notas de Crédito</h4>
                    <button class="btn btn-danger btn-sm" onclick="abrirModalNC()">
                        <i class="fas fa-plus"></i> Nueva Nota de Crédito
                    </button>
                </div>

                <!-- Filtros -->
                <div class="row mb-3 g-2">
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Desde</label>
                        <input type="date" id="filtro_desde" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Hasta</label>
                        <input type="date" id="filtro_hasta" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Estado</label>
                        <select id="filtro_estado" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="1">Enviados</option>
                            <option value="0">Pendientes</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Buscar</label>
                        <input type="text" id="filtro_buscar" class="form-control form-control-sm"
                               placeholder="Serie, cliente, RUC...">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary btn-sm w-100" onclick="cargarNotas()">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="table-responsive">
                    <table class="table table-hover table-sm" id="tablaNotas">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Serie-Correlativo</th>
                                <th>Fecha</th>
                                <th>Comprobante Origen</th>
                                <th>Cliente</th>
                                <th>Motivo</th>
                                <th class="text-end">Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyNotas">
                            <tr><td colspan="9" class="text-center text-muted">Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     MODAL REGISTRO NOTA DE CRÉDITO
════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalNC" tabindex="-1" aria-labelledby="tituloModalNC" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="tituloModalNC">Nueva Nota de Crédito</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- ── PASO 1: Buscar comprobante origen ──────────────── -->
                <h6 class="fw-bold border-bottom pb-1 mb-3">🔍 Comprobante de Origen</h6>
                <div class="row g-2 mb-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Tipo</label>
                        <select id="nc_origen_tipo" class="form-select form-select-sm">
                            <option value="01">Factura (01)</option>
                            <option value="03">Boleta (03)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Serie</label>
                        <input type="text" id="nc_origen_serie" class="form-control form-control-sm text-uppercase"
                               placeholder="F001 / B001" maxlength="4">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Correlativo</label>
                        <input type="text" id="nc_origen_correlativo" class="form-control form-control-sm"
                               placeholder="00000001" maxlength="8">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-outline-primary btn-sm w-100" onclick="buscarComprobante()">
                            <i class="fas fa-search"></i> Buscar comprobante
                        </button>
                    </div>
                </div>

                <!-- Resultado de búsqueda del comprobante -->
                <div id="divComprobanteEncontrado" class="alert alert-success py-2 px-3 mb-3" style="display:none">
                    <div class="row">
                        <div class="col-md-4"><strong>Cliente:</strong> <span id="nc_info_cliente"></span></div>
                        <div class="col-md-3"><strong>Doc:</strong> <span id="nc_info_doc_cliente"></span></div>
                        <div class="col-md-2"><strong>Fecha:</strong> <span id="nc_info_fecha"></span></div>
                        <div class="col-md-3 text-end"><strong>Total:</strong> S/ <span id="nc_info_total"></span></div>
                    </div>
                </div>
                <div id="divComprobanteNoEncontrado" class="alert alert-warning py-2 px-3 mb-3" style="display:none">
                    ⚠️ No se encontró el comprobante. Verifica el tipo, serie y correlativo.
                </div>

                <!-- ── PASO 2: Datos de la nota ───────────────────────── -->
                <h6 class="fw-bold border-bottom pb-1 mb-3 mt-2">📋 Datos de la Nota de Crédito</h6>
                <div class="row g-2 mb-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Fecha Emisión</label>
                        <input type="date" id="nc_fecha_emision" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Tipo de Nota (Catálogo 09)</label>
                        <select id="nc_tipo_nota" class="form-select form-select-sm">
                            <option value="01">01 - Anulación de la operación</option>
                            <option value="02">02 - Anulación por error en el RUC</option>
                            <option value="03">03 - Corrección por error en la descripción</option>
                            <option value="04">04 - Descuento global</option>
                            <option value="05">05 - Descuento por ítem</option>
                            <option value="06">06 - Devolución total</option>
                            <option value="07">07 - Devolución por ítem</option>
                            <option value="08">08 - Bonificación</option>
                            <option value="13">13 - Ajustes de operaciones de exportación</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Motivo / Descripción</label>
                        <input type="text" id="nc_motivo" class="form-control form-control-sm"
                               placeholder="Descripción del motivo de la nota de crédito">
                    </div>
                </div>

                <!-- ── PASO 3: Ítems ──────────────────────────────────── -->
                <h6 class="fw-bold border-bottom pb-1 mb-3 mt-2">📦 Ítems a Acreditar</h6>
                <div class="table-responsive mb-2">
                    <table class="table table-sm table-bordered" id="tablaItemsNC">
                        <thead class="table-secondary">
                            <tr>
                                <th style="width:40px">#</th>
                                <th>Código</th>
                                <th>Descripción</th>
                                <th style="width:90px">Cantidad</th>
                                <th style="width:80px">Unidad</th>
                                <th style="width:110px">V. Unit. s/IGV</th>
                                <th style="width:80px">Impuesto</th>
                                <th style="width:100px">Total</th>
                                <th style="width:40px">✕</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyItemsNC"></tbody>
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="7" class="text-end">Subtotal (sin IGV):</td>
                                <td class="text-end" id="nc_subtotal">0.00</td>
                                <td></td>
                            </tr>
                            <tr class="table-light fw-bold">
                                <td colspan="7" class="text-end">IGV (18%):</td>
                                <td class="text-end" id="nc_igv_total">0.00</td>
                                <td></td>
                            </tr>
                            <tr class="table-danger fw-bold">
                                <td colspan="7" class="text-end">TOTAL A ACREDITAR:</td>
                                <td class="text-end" id="nc_total">0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="agregarItemNC()">
                    <i class="fas fa-plus"></i> Agregar ítem
                </button>

            </div>

            <div class="modal-footer">
                <div id="divAlertaNC" class="me-auto" style="display:none"></div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnGuardarNC" onclick="guardarNC()">
                    <i class="fas fa-paper-plane"></i> Registrar y Enviar a SUNAT
                </button>
            </div>

        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     MODAL VER NOTA DE CRÉDITO
════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalVerNC" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Detalle de Nota de Crédito</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoVerNC">
                <p class="text-center text-muted">Cargando...</p>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     JAVASCRIPT
════════════════════════════════════════════════════════════ -->
<script>
const HANDLER = 'logica/clssComprobante.php';

const TIPOS_NOTA = {
    '01':'Anulación de la operación',
    '02':'Anulación por error en RUC',
    '03':'Corrección en descripción',
    '04':'Descuento global',
    '05':'Descuento por ítem',
    '06':'Devolución total',
    '07':'Devolución por ítem',
    '08':'Bonificación',
    '13':'Ajustes exportación',
};

// Estado compartido del comprobante origen encontrado
let comprobanteOrigen = null;
let itemCounter = 0;

// ── Inicialización ────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const hoy          = new Date().toISOString().split('T')[0];
    const primerDiaMes = new Date(new Date().getFullYear(), new Date().getMonth(), 1)
                         .toISOString().split('T')[0];
    document.getElementById('filtro_desde').value = primerDiaMes;
    document.getElementById('filtro_hasta').value = hoy;
    cargarNotas();
});

// ═══════════════════════════════════════════════════════════
// CARGAR TABLA DE NOTAS
// ═══════════════════════════════════════════════════════════
function cargarNotas() {
    const params = new URLSearchParams({
        accion:  'LISTAR_NOTAS_CREDITO',
        desde:   document.getElementById('filtro_desde').value,
        hasta:   document.getElementById('filtro_hasta').value,
        estado:  document.getElementById('filtro_estado').value,
        buscar:  document.getElementById('filtro_buscar').value,
    });

    document.getElementById('tbodyNotas').innerHTML =
        `<tr><td colspan="9" class="text-center">
            <div class="spinner-border spinner-border-sm text-secondary"></div> Cargando...
         </td></tr>`;

    fetch(HANDLER + '?' + params)
        .then(r => r.json())
        .then(res => {
            if (!res.estado) {
                document.getElementById('tbodyNotas').innerHTML =
                    `<tr><td colspan="9" class="text-center text-danger">${res.mensaje}</td></tr>`;
                return;
            }
            renderTablaNotas(res.datos);
        })
        .catch(e => {
            document.getElementById('tbodyNotas').innerHTML =
                `<tr><td colspan="9" class="text-center text-danger">Error: ${e.message}</td></tr>`;
        });
}

function renderTablaNotas(datos) {
    if (!datos.length) {
        document.getElementById('tbodyNotas').innerHTML =
            '<tr><td colspan="9" class="text-center text-muted">Sin resultados</td></tr>';
        return;
    }
    let html = '';
    datos.forEach((n, i) => {
        const badgeEstado = n.estado_envio == 1 || n.estado_envio === true
            ? '<span class="badge bg-success">Enviado</span>'
            : '<span class="badge bg-danger">Pendiente</span>';

        html += `<tr>
            <td>${i + 1}</td>
            <td><strong>${n.serie}-${n.correlativo_texto}</strong></td>
            <td>${n.fecha_emision}</td>
            <td>
                <span class="badge bg-secondary">${n.tipo_comp_ref === '01' ? 'Factura' : 'Boleta'}</span>
                ${n.serie_correletaivo_ref || '-'}
            </td>
            <td>${n.razon_social ?? n.numero_doc_cliente}<br>
                <small class="text-muted">${n.numero_doc_cliente}</small>
            </td>
            <td><small>${TIPOS_NOTA[n.codmotivo] ?? n.codmotivo ?? '-'}</small></td>
            <td class="text-end">S/ ${parseFloat(n.total ?? 0).toFixed(2)}</td>
            <td>${badgeEstado}</td>
            <td>
                <button class="btn btn-sm btn-outline-dark" onclick="verNC(${n.id})" title="Ver">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        </tr>`;
    });
    document.getElementById('tbodyNotas').innerHTML = html;
}

// ═══════════════════════════════════════════════════════════
// ABRIR MODAL
// ═══════════════════════════════════════════════════════════
function abrirModalNC() {
    limpiarFormNC();
    const hoy = new Date().toISOString().split('T')[0];
    document.getElementById('nc_fecha_emision').value = hoy;
    new bootstrap.Modal(document.getElementById('modalNC')).show();
}

// ═══════════════════════════════════════════════════════════
// BUSCAR COMPROBANTE ORIGEN
// ═══════════════════════════════════════════════════════════
function buscarComprobante() {
    const tipo  = document.getElementById('nc_origen_tipo').value;
    const serie = document.getElementById('nc_origen_serie').value.trim().toUpperCase();
    const corr  = document.getElementById('nc_origen_correlativo').value.trim();

    if (!serie || !corr) {
        alert('Ingresa la serie y correlativo del comprobante origen.');
        return;
    }

    document.getElementById('divComprobanteEncontrado').style.display    = 'none';
    document.getElementById('divComprobanteNoEncontrado').style.display  = 'none';

    const params = new URLSearchParams({
        accion:           'BUSCAR_COMPROBANTE',
        tipo_comprobante: tipo,
        serie:            serie,
        correlativo:      corr,
    });

    fetch(HANDLER + '?' + params)
        .then(r => r.json())
        .then(res => {
            if (!res.estado || !res.comprobante) {
                document.getElementById('divComprobanteNoEncontrado').style.display = '';
                comprobanteOrigen = null;
                limpiarItemsNC();
                return;
            }

            comprobanteOrigen = res.comprobante;
            document.getElementById('nc_info_cliente').textContent    = res.comprobante.cliente ?? res.comprobante.razon_social ?? '';
            document.getElementById('nc_info_doc_cliente').textContent = res.comprobante.numero_doc_cliente ?? '';
            document.getElementById('nc_info_fecha').textContent      = res.comprobante.fecha_emision ?? '';
            document.getElementById('nc_info_total').textContent      = parseFloat(res.comprobante.total ?? 0).toFixed(2);
            document.getElementById('divComprobanteEncontrado').style.display = '';

            // Prellenar motivo por defecto
            document.getElementById('nc_motivo').value = TIPOS_NOTA[document.getElementById('nc_tipo_nota').value] ?? '';

            // Prellenar ítems si el handler los devuelve
            if (res.items && res.items.length) {
                limpiarItemsNC();
                res.items.forEach(it => agregarItemNC(it));
            } else {
                // Al menos una fila vacía
                if (!document.getElementById('tbodyItemsNC').rows.length) agregarItemNC();
            }
        })
        .catch(e => alert('Error buscando comprobante: ' + e.message));
}

// Autocompletar motivo cuando cambia el tipo de nota
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('nc_tipo_nota')?.addEventListener('change', function() {
        document.getElementById('nc_motivo').value = TIPOS_NOTA[this.value] ?? '';
    });
});

// ═══════════════════════════════════════════════════════════
// TABLA DE ÍTEMS
// ═══════════════════════════════════════════════════════════
function agregarItemNC(item = null) {
    itemCounter++;
    const tbody = document.getElementById('tbodyItemsNC');
    const tr    = document.createElement('tr');
    tr.id = 'nc_fila_' + itemCounter;

    const codigo   = item?.codigo_producto ?? item?.articulo_id ?? '';
    const nombre   = item?.nombre ?? item?.descripcion ?? '';
    const cantidad = item?.cantidad ?? 1;
    const unidad   = item?.unidad_medida ?? item?.unidad ?? 'NIU';
    const vuSinIgv = item?.valor_unitario ?? item?.pu_sin_igv ?? 0;
    const tipoImp  = item?.tipo_impuesto ?? 'IGV';

    tr.innerHTML = `
        <td class="text-center align-middle ncItem">${tbody.rows.length + 1}</td>
        <td><input type="text"   class="form-control form-control-sm" placeholder="001" value="${codigo}"></td>
        <td><input type="text"   class="form-control form-control-sm" placeholder="Descripción" value="${nombre}" required></td>
        <td><input type="number" class="form-control form-control-sm text-end nc-cant"
                   min="0.01" step="0.01" value="${cantidad}" oninput="recalcularTotalesNC()"></td>
        <td>
            <select class="form-select form-select-sm">
                <option value="NIU" ${unidad==='NIU'?'selected':''}>NIU</option>
                <option value="KGM" ${unidad==='KGM'?'selected':''}>KGM</option>
                <option value="LTR" ${unidad==='LTR'?'selected':''}>LTR</option>
                <option value="MTR" ${unidad==='MTR'?'selected':''}>MTR</option>
                <option value="BX"  ${unidad==='BX' ?'selected':''}>BX</option>
            </select>
        </td>
        <td><input type="number" class="form-control form-control-sm text-end nc-vu"
                   min="0" step="0.000001" value="${parseFloat(vuSinIgv).toFixed(6)}"
                   oninput="recalcularTotalesNC()"></td>
        <td>
            <select class="form-select form-select-sm nc-imp" onchange="recalcularTotalesNC()">
                <option value="IGV"       ${tipoImp==='IGV'      ?'selected':''}>IGV 18%</option>
                <option value="EXONERADO" ${tipoImp==='EXONERADO'?'selected':''}>Exonerado</option>
                <option value="INAFECTO"  ${tipoImp==='INAFECTO' ?'selected':''}>Inafecto</option>
            </select>
        </td>
        <td class="text-end align-middle nc-total fw-bold">0.00</td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger"
                    onclick="eliminarItemNC('nc_fila_${itemCounter}')">✕</button>
        </td>
    `;
    tbody.appendChild(tr);
    recalcularTotalesNC();
}

function eliminarItemNC(id) {
    document.getElementById(id)?.remove();
    renumerarItemsNC();
    recalcularTotalesNC();
}

function renumerarItemsNC() {
    document.querySelectorAll('#tbodyItemsNC .ncItem').forEach((td, i) => {
        td.textContent = i + 1;
    });
}

function recalcularTotalesNC() {
    let subtotal = 0;
    let igvTotal = 0;

    document.querySelectorAll('#tbodyItemsNC tr').forEach(tr => {
        const cant    = parseFloat(tr.querySelector('.nc-cant')?.value  ?? 0);
        const vu      = parseFloat(tr.querySelector('.nc-vu')?.value    ?? 0);
        const tipoImp = tr.querySelector('.nc-imp')?.value ?? 'IGV';
        const tdTotal = tr.querySelector('.nc-total');
        if (!tdTotal) return;

        const valorTotal = Math.round(cant * vu * 100) / 100;
        const igvItem    = tipoImp === 'IGV' ? Math.round(valorTotal * 0.18 * 100) / 100 : 0;

        subtotal += valorTotal;
        igvTotal += igvItem;
        tdTotal.textContent = (valorTotal + igvItem).toFixed(2);
    });

    document.getElementById('nc_subtotal').textContent  = subtotal.toFixed(2);
    document.getElementById('nc_igv_total').textContent = igvTotal.toFixed(2);
    document.getElementById('nc_total').textContent     = (subtotal + igvTotal).toFixed(2);
}

function limpiarItemsNC() {
    document.getElementById('tbodyItemsNC').innerHTML = '';
    itemCounter = 0;
    recalcularTotalesNC();
}

// ═══════════════════════════════════════════════════════════
// GUARDAR Y ENVIAR NOTA DE CRÉDITO
// ═══════════════════════════════════════════════════════════
function guardarNC() {
    ocultarAlertaNC();

    if (!comprobanteOrigen) {
        mostrarAlertaNC('Primero busca y selecciona el comprobante de origen.', 'warning');
        return;
    }

    const items = [];
    let valido  = true;

    document.querySelectorAll('#tbodyItemsNC tr').forEach((tr, idx) => {
        const inputs  = tr.querySelectorAll('input');
        const selects = tr.querySelectorAll('select');
        const nombre  = inputs[1]?.value.trim();

        if (!nombre) { valido = false; return; }

        const cant    = parseFloat(inputs[2]?.value ?? 1);
        const vu      = parseFloat(inputs[3]?.value ?? 0);
        const unidad  = selects[0]?.value ?? 'NIU';
        const tipoImp = selects[1]?.value ?? 'IGV';
        const porc    = tipoImp === 'IGV' ? 0.18 : 0;

        items.push({
            item:            idx + 1,
            codigo_producto: inputs[0]?.value.trim() || '001',
            nombre:          nombre,
            cantidad:        cant,
            unidad:          unidad,
            valor_unitario:  vu,
            precio_lista:    Math.round(vu * (1 + porc) * 100) / 100,
            valor_total:     Math.round(cant * vu * 100) / 100,
            igv:             Math.round(cant * vu * porc * 100) / 100,
            icbper:          0,
            factor_icbper:   0.50,
            total_antes_impuestos: Math.round(cant * vu * 100) / 100,
            total_impuestos: Math.round(cant * vu * porc * 100) / 100,
            tipo_impuesto:   tipoImp,
            porcentaje_div:  porc,
            codigos: tipoImp === 'EXONERADO'
                ? ['E','20','9997','EXO','VAT']
                : tipoImp === 'INAFECTO'
                ? ['O','30','9998','INA','FRE']
                : ['S','10','1000','IGV','VAT'],
        });
    });

    if (!valido || !items.length) {
        mostrarAlertaNC('Completa al menos un ítem con descripción.', 'danger');
        return;
    }

    // Armar el JSON igual al que espera clssComprobante.php → REGISTRARNOTACREDITO
    const jsComprobantes = JSON.stringify({
        emisor:   {},   // el backend lo resuelve desde la sesión / BD
        cliente: {
            tipo_documento:     comprobanteOrigen.tipo_documento_cliente ?? '6',
            numero_doc_cliente: comprobanteOrigen.numero_doc_cliente     ?? '',
            cliente:            comprobanteOrigen.cliente ?? comprobanteOrigen.razon_social ?? '',
            razon_social:       comprobanteOrigen.cliente ?? comprobanteOrigen.razon_social ?? '',
            direccion:          comprobanteOrigen.direccion ?? '',
        },
        cabecera: {
            venta_id:                 comprobanteOrigen.venta_id          ?? null,
            tipo_operacion:           '0101',
            tipo_comprobante:         '07',
            moneda:                   comprobanteOrigen.moneda             ?? 'PEN',
            forma_pago:               comprobanteOrigen.forma_pago         ?? 'Contado',
            fecha_emision:            document.getElementById('nc_fecha_emision').value,
            hora_emision:             new Date().toTimeString().split(' ')[0],
            fecha_vencimiento:        document.getElementById('nc_fecha_emision').value,
            tipo_comp_ref:            comprobanteOrigen.tipo_comprobante   ?? '03',
            serie_correletaivo_ref:   (comprobanteOrigen.serie ?? '') + '-' + (comprobanteOrigen.correlativo_texto ?? ''),
            serie_ref:                comprobanteOrigen.serie              ?? '',
            correlativo_ref:          comprobanteOrigen.correlativo_texto  ?? '',
            tipo_nota:                document.getElementById('nc_tipo_nota').value,
            motivo:                   document.getElementById('nc_motivo').value.trim(),
            motivo_nota:              document.getElementById('nc_motivo').value.trim(),
            // Totales los recalcula el backend (paso 6b de clssComprobante)
            total_op_gravadas:        0,
            igv:                      0,
            total_op_exoneradas:      0,
            total_op_inafectas:       0,
            total_antes_impuestos:    0,
            total_impuestos:          0,
            total_despues_impuestos:  0,
            total_a_pagar:            0,
        },
        detalles: items,
    });

    const btn = document.getElementById('btnGuardarNC');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Enviando a SUNAT...';

    const fd = new FormData();
    fd.append('accion',         'REGISTRARNOTACREDITO');
    fd.append('jsComprobantes', jsComprobantes);

    fetch(HANDLER, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Registrar y Enviar a SUNAT';
            if (res.estado) {
                mostrarAlertaNC('✅ ' + res.mensaje, 'success');
                cargarNotas();
                setTimeout(() => bootstrap.Modal.getInstance(document.getElementById('modalNC')).hide(), 2000);
            } else {
                mostrarAlertaNC('❌ ' + res.mensaje, 'danger');
            }
        })
        .catch(e => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Registrar y Enviar a SUNAT';
            mostrarAlertaNC('Error de conexión: ' + e.message, 'danger');
        });
}

// ═══════════════════════════════════════════════════════════
// VER DETALLE
// ═══════════════════════════════════════════════════════════
function verNC(id) {
    document.getElementById('contenidoVerNC').innerHTML =
        '<p class="text-center"><div class="spinner-border text-secondary"></div></p>';
    new bootstrap.Modal(document.getElementById('modalVerNC')).show();

    fetch(`${HANDLER}?accion=OBTENER_NOTA_CREDITO&id=${id}`)
        .then(r => r.json())
        .then(res => {
            if (!res.estado) {
                document.getElementById('contenidoVerNC').innerHTML =
                    `<div class="alert alert-danger">${res.mensaje}</div>`;
                return;
            }
            const n = res.comprobante;
            const badgeEstado = (n.estado_envio == 1 || n.estado_envio === true)
                ? '<span class="badge bg-success fs-6">ENVIADO A SUNAT</span>'
                : '<span class="badge bg-danger fs-6">PENDIENTE</span>';

            document.getElementById('contenidoVerNC').innerHTML = `
                <div class="row g-2 mb-3">
                    <div class="col-6"><strong>Serie-Correlativo:</strong> ${n.serie}-${n.correlativo_texto}</div>
                    <div class="col-6 text-end">${badgeEstado}</div>
                    <div class="col-4"><strong>Fecha:</strong> ${n.fecha_emision}</div>
                    <div class="col-4"><strong>Tipo Nota:</strong> ${TIPOS_NOTA[n.codmotivo] ?? n.codmotivo}</div>
                    <div class="col-4"><strong>Comprobante Orig.:</strong> ${n.serie_correletaivo_ref ?? '-'}</div>
                    <div class="col-6"><strong>Cliente:</strong> ${n.cliente ?? n.razon_social ?? '-'}</div>
                    <div class="col-6"><strong>Doc. Cliente:</strong> ${n.numero_doc_cliente}</div>
                    <div class="col-4"><strong>Op. Gravadas:</strong> S/ ${parseFloat(n.op_gravadas ?? 0).toFixed(2)}</div>
                    <div class="col-4"><strong>IGV:</strong> S/ ${parseFloat(n.igv ?? 0).toFixed(2)}</div>
                    <div class="col-4"><strong>Total:</strong> <span class="text-danger fw-bold">S/ ${parseFloat(n.total ?? 0).toFixed(2)}</span></div>
                    <div class="col-12"><strong>Mensaje SUNAT:</strong> <span class="text-muted">${n.mensaje_sunat ?? '-'}</span></div>
                </div>
            `;
        });
}

// ═══════════════════════════════════════════════════════════
// UTILIDADES
// ═══════════════════════════════════════════════════════════
function limpiarFormNC() {
    comprobanteOrigen = null;
    itemCounter       = 0;
    document.getElementById('tbodyItemsNC').innerHTML               = '';
    document.getElementById('nc_origen_serie').value               = '';
    document.getElementById('nc_origen_correlativo').value         = '';
    document.getElementById('nc_motivo').value                     = '';
    document.getElementById('divComprobanteEncontrado').style.display   = 'none';
    document.getElementById('divComprobanteNoEncontrado').style.display = 'none';
    recalcularTotalesNC();
    ocultarAlertaNC();
}

function mostrarAlertaNC(msg, tipo) {
    const div = document.getElementById('divAlertaNC');
    div.style.display = '';
    div.innerHTML = `<div class="alert alert-${tipo} alert-sm py-1 px-2 mb-0">${msg}</div>`;
}

function ocultarAlertaNC() {
    const div = document.getElementById('divAlertaNC');
    if (div) { div.style.display = 'none'; div.innerHTML = ''; }
}
</script>

<?php
include("pie.php");
?>