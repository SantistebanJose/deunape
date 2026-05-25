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
                    <h4 class="card-title mb-0">Guías de Remisión</h4>
                    <div>
                        <button class="btn btn-success btn-sm me-1" onclick="abrirModalGuia('09')">
                            <i class="fas fa-plus"></i> Nueva GR Remitente
                        </button>
                        <button class="btn btn-info btn-sm text-white" onclick="abrirModalGuia('31')">
                            <i class="fas fa-truck"></i> Nueva GR Transportista
                        </button>
                    </div>
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
                        <label class="form-label small fw-bold">Tipo</label>
                        <select id="filtro_tipo" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="09">Remitente</option>
                            <option value="31">Transportista</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Estado</label>
                        <select id="filtro_estado" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="1">Enviados</option>
                            <option value="0">Pendientes</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Buscar</label>
                        <input type="text" id="filtro_buscar" class="form-control form-control-sm" placeholder="Serie, destinatario...">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button class="btn btn-primary btn-sm w-100" onclick="cargarGuias()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="table-responsive">
                    <table class="table table-hover table-sm" id="tablaGuias">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Tipo</th>
                                <th>Serie-Correlativo</th>
                                <th>Fecha Emisión</th>
                                <th>Fecha Traslado</th>
                                <th>Destinatario</th>
                                <th>Placa</th>
                                <th>Peso (KG)</th>
                                <th>Comprobante Ref.</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyGuias">
                            <tr><td colspan="11" class="text-center text-muted">Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     MODAL REGISTRO DE GUÍA
════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalGuia" tabindex="-1" aria-labelledby="tituloModalGuia" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-success text-white" id="modalGuiaHeader">
                <h5 class="modal-title" id="tituloModalGuia">Nueva Guía de Remisión</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="gr_tipo_comprobante" value="09">

                <!-- ── Datos básicos ─────────────────────────────────── -->
                <h6 class="fw-bold border-bottom pb-1 mb-3">📋 Datos del Traslado</h6>
                <div class="row g-2 mb-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Fecha Emisión</label>
                        <input type="date" id="gr_fecha_emision" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Fecha Traslado</label>
                        <input type="date" id="gr_fecha_traslado" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Motivo Traslado</label>
                        <select id="gr_motivo_traslado" class="form-select form-select-sm">
                            <option value="01">01 - Venta</option>
                            <option value="02">02 - Compra</option>
                            <option value="04">04 - Traslado entre establecimientos</option>
                            <option value="08">08 - Importación</option>
                            <option value="09">09 - Exportación</option>
                            <option value="13">13 - Otros</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="divModalidadTraslado">
                        <label class="form-label small fw-bold">Modalidad Traslado</label>
                        <select id="gr_modalidad_traslado" class="form-select form-select-sm" onchange="toggleTransportista()">
                            <option value="02">02 - Transporte Privado</option>
                            <option value="01">01 - Transporte Público</option>
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Peso Bruto Total (KG)</label>
                        <input type="number" id="gr_peso_bruto_total" class="form-control form-control-sm" step="0.01" min="0" value="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Venta / Comprobante Relacionado</label>
                        <input type="number" id="gr_venta_id" class="form-control form-control-sm" placeholder="ID Venta (opcional)"
                               oninput="if(this.value) cargarItemsVenta(this.value)">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Serie Ref.</label>
                        <input type="text" id="gr_comprobante_ref_serie" class="form-control form-control-sm" placeholder="F001 / B001">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Correlativo Ref.</label>
                        <input type="text" id="gr_comprobante_ref_correlativo" class="form-control form-control-sm" placeholder="00000001">
                    </div>
                </div>

                <!-- GR Remitente relacionada (solo tipo 31) -->
                <div class="row g-2 mb-3" id="divGuiaRemitenteRef" style="display:none!important">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Serie GR Remitente Relacionada</label>
                        <input type="text" id="gr_guia_remitente_serie" class="form-control form-control-sm" placeholder="T001">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Correlativo GR Remitente</label>
                        <input type="text" id="gr_guia_remitente_correlativo" class="form-control form-control-sm" placeholder="00000001">
                    </div>
                </div>

                <!-- ── Puntos de Partida / Llegada ───────────────────── -->
                <h6 class="fw-bold border-bottom pb-1 mb-3 mt-2">📍 Puntos de Traslado</h6>
                <div class="row g-2 mb-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Ubigeo Partida</label>
                        <input type="text" id="gr_ubigeo_partida" class="form-control form-control-sm" maxlength="6" placeholder="150101">
                    </div>
                    <div class="col-md-9">
                        <label class="form-label small fw-bold">Dirección Partida</label>
                        <input type="text" id="gr_direccion_partida" class="form-control form-control-sm" placeholder="Av. Ejemplo 123">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Ubigeo Llegada</label>
                        <input type="text" id="gr_ubigeo_llegada" class="form-control form-control-sm" maxlength="6" placeholder="150101">
                    </div>
                    <div class="col-md-9">
                        <label class="form-label small fw-bold">Dirección Llegada</label>
                        <input type="text" id="gr_direccion_llegada" class="form-control form-control-sm" placeholder="Av. Destino 456">
                    </div>
                </div>

                <!-- ── Destinatario ──────────────────────────────────── -->
                <h6 class="fw-bold border-bottom pb-1 mb-3 mt-2">🏢 Destinatario</h6>
                <div class="row g-2 mb-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Tipo Doc.</label>
                        <select id="gr_dest_tipo_doc" class="form-select form-select-sm">
                            <option value="6">RUC</option>
                            <option value="1">DNI</option>
                            <option value="4">Carnet Extranjería</option>
                            <option value="7">Pasaporte</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">N° Documento</label>
                        <input type="text" id="gr_dest_numero_doc" class="form-control form-control-sm" maxlength="15">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Razón Social / Nombre</label>
                        <input type="text" id="gr_dest_razon_social" class="form-control form-control-sm">
                    </div>
                </div>

                <!-- ── Vehículo y Conductor ──────────────────────────── -->
                <h6 class="fw-bold border-bottom pb-1 mb-3 mt-2">🚛 Vehículo y Conductor</h6>
                <div class="row g-2 mb-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Placa Vehículo</label>
                        <input type="text" id="gr_placa_vehiculo" class="form-control form-control-sm text-uppercase" maxlength="10" placeholder="ABC-123">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">N° Licencia Conductor</label>
                        <input type="text" id="gr_conductor_licencia" class="form-control form-control-sm" maxlength="20">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Tipo Doc. Conductor</label>
                        <select id="gr_conductor_tipo_doc" class="form-select form-select-sm">
                            <option value="1">DNI</option>
                            <option value="4">Carnet Extranjería</option>
                            <option value="7">Pasaporte</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Doc. Conductor</label>
                        <input type="text" id="gr_conductor_doc" class="form-control form-control-sm" maxlength="15">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Nombres Conductor</label>
                        <input type="text" id="gr_conductor_nombres" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Apellidos Conductor</label>
                        <input type="text" id="gr_conductor_apellidos" class="form-control form-control-sm">
                    </div>
                </div>

                <!-- Transportista externo (solo modalidad pública) -->
                <div id="divTransportista" style="display:none">
                    <h6 class="fw-bold border-bottom pb-1 mb-3 mt-2 text-info">🏭 Transportista (Modalidad Pública)</h6>
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">RUC Transportista</label>
                            <input type="text" id="gr_transportista_ruc" class="form-control form-control-sm" maxlength="11">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Razón Social Transportista</label>
                            <input type="text" id="gr_transportista_razon_social" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>

                <!-- ── Detalle de Bienes ──────────────────────────────── -->
                <h6 class="fw-bold border-bottom pb-1 mb-3 mt-2">📦 Bienes a Trasladar</h6>
                <div class="table-responsive mb-2">
                    <table class="table table-sm table-bordered" id="tablaDetallesGuia">
                        <thead class="table-secondary">
                            <tr>
                                <th style="width:50px">#</th>
                                <th>Código</th>
                                <th>Descripción</th>
                                <th style="width:100px">Cantidad</th>
                                <th style="width:100px">Unidad</th>
                                <th style="width:50px">✕</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyDetallesGuia"></tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="agregarFilaDetalle()">
                    <i class="fas fa-plus"></i> Agregar Ítem
                </button>
            </div>

            <div class="modal-footer">
                <div id="divAlertaGuia" class="me-auto" style="display:none"></div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnGuardarGuia" onclick="guardarGuia()">
                    <i class="fas fa-paper-plane"></i> Registrar y Enviar a SUNAT
                </button>
            </div>

        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     MODAL DETALLE / VISTA GUÍA
════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalVerGuia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Detalle de Guía de Remisión</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoVerGuia">
                <p class="text-center text-muted">Cargando...</p>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     JAVASCRIPT
════════════════════════════════════════════════════════════ -->
<script>
// ── Constantes de catalogos SUNAT ────────────────────────────
const MOTIVOS_TRASLADO = {
    '01':'Venta','02':'Compra','04':'Traslado entre establecimientos',
    '08':'Importación','09':'Exportación','13':'Otros'
};
const MODALIDADES = { '01':'Transporte Público', '02':'Transporte Privado' };

// ── Inicialización ────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const hoy = new Date().toISOString().split('T')[0];
    const primerDiaMes = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];
    document.getElementById('filtro_desde').value = primerDiaMes;
    document.getElementById('filtro_hasta').value = hoy;
    cargarGuias();
});

// ═══════════════════════════════════════════════════════════
// CARGAR TABLA DE GUÍAS
// ═══════════════════════════════════════════════════════════
function cargarGuias() {
    const params = new URLSearchParams({
        accion:           'LISTAR_GUIAS',
        desde:            document.getElementById('filtro_desde').value,
        hasta:            document.getElementById('filtro_hasta').value,
        tipo_comprobante: document.getElementById('filtro_tipo').value,
        estado:           document.getElementById('filtro_estado').value,
        buscar:           document.getElementById('filtro_buscar').value,
    });

    document.getElementById('tbodyGuias').innerHTML =
        '<tr><td colspan="11" class="text-center"><div class="spinner-border spinner-border-sm text-secondary"></div> Cargando...</td></tr>';

    fetch('logica/clssGuiaRemisionHandler.php?' + params)
        .then(r => r.json())
        .then(res => {
            if (!res.estado) {
                document.getElementById('tbodyGuias').innerHTML =
                    `<tr><td colspan="11" class="text-center text-danger">${res.mensaje}</td></tr>`;
                return;
            }
            renderTablaGuias(res.datos);
        })
        .catch(e => {
            document.getElementById('tbodyGuias').innerHTML =
                `<tr><td colspan="11" class="text-center text-danger">Error: ${e.message}</td></tr>`;
        });
}

function renderTablaGuias(datos) {
    if (!datos.length) {
        document.getElementById('tbodyGuias').innerHTML =
            '<tr><td colspan="11" class="text-center text-muted">Sin resultados para los filtros seleccionados</td></tr>';
        return;
    }
    let html = '';
    datos.forEach((g, i) => {
        const badgeTipo   = g.tipo_comprobante === '31'
            ? '<span class="badge bg-info">Transportista</span>'
            : '<span class="badge bg-success">Remitente</span>';
        const badgeEstado = g.estado_envio == 1
            ? '<span class="badge bg-success">Enviado</span>'
            : '<span class="badge bg-danger">Pendiente</span>';

        html += `<tr>
            <td>${i + 1}</td>
            <td>${badgeTipo}</td>
            <td><strong>${g.serie_correlativo}</strong></td>
            <td>${g.fecha_emision}</td>
            <td>${g.fecha_traslado}</td>
            <td>${g.razon_social_destinatario}<br><small class="text-muted">${g.numero_doc_destinatario}</small></td>
            <td>${g.placa_vehiculo || '-'}</td>
            <td class="text-end">${parseFloat(g.peso_bruto_total || 0).toFixed(2)} ${g.unidad_peso}</td>
            <td>${g.comprobante_ref && g.comprobante_ref !== '-' ? g.comprobante_ref : '-'}</td>
            <td>${badgeEstado}</td>
            <td>
                <button class="btn btn-xs btn-outline-dark btn-sm" onclick="verGuia(${g.id})" title="Ver">
                    <i class="fas fa-eye"></i>
                </button>
                ${g.estado_envio == 0
                    ? `<button class="btn btn-xs btn-outline-danger btn-sm ms-1" onclick="anularGuia(${g.id})" title="Anular">
                        <i class="fas fa-times"></i>
                       </button>`
                    : ''}
            </td>
        </tr>`;
    });
    document.getElementById('tbodyGuias').innerHTML = html;
}

// ═══════════════════════════════════════════════════════════
// ABRIR MODAL NUEVA GUÍA
// ═══════════════════════════════════════════════════════════
function abrirModalGuia(tipo = '09') {
    limpiarFormularioGuia();
    document.getElementById('gr_tipo_comprobante').value = tipo;

    const hoy = new Date().toISOString().split('T')[0];
    document.getElementById('gr_fecha_emision').value  = hoy;
    document.getElementById('gr_fecha_traslado').value = hoy;

    // Ajustar UI según tipo
    const header = document.getElementById('modalGuiaHeader');
    const titulo = document.getElementById('tituloModalGuia');

    if (tipo === '31') {
        header.className = 'modal-header bg-info text-white';
        titulo.textContent = 'Nueva Guía de Remisión — Transportista (31)';
        document.getElementById('divModalidadTraslado').style.display = 'none';
        document.getElementById('divTransportista').style.display      = '';
        document.getElementById('divGuiaRemitenteRef').style.cssText   = '';
    } else {
        header.className = 'modal-header bg-success text-white';
        titulo.textContent = 'Nueva Guía de Remisión — Remitente (09)';
        document.getElementById('divModalidadTraslado').style.display = '';
        document.getElementById('divTransportista').style.display      = 'none';
        document.getElementById('divGuiaRemitenteRef').style.cssText   = 'display:none!important';
    }

    agregarFilaDetalle(); // fila inicial

    const modal = new bootstrap.Modal(document.getElementById('modalGuia'));
    modal.show();
}

function toggleTransportista() {
    const mod = document.getElementById('gr_modalidad_traslado').value;
    document.getElementById('divTransportista').style.display = (mod === '01') ? '' : 'none';
}

// ═══════════════════════════════════════════════════════════
// TABLA DE ÍTEMS
// ═══════════════════════════════════════════════════════════
let itemCounter = 0;

function agregarFilaDetalle(codigo = '', nombre = '', cantidad = 1, unidad = 'NIU') {
    itemCounter++;
    const tbody = document.getElementById('tbodyDetallesGuia');
    const tr = document.createElement('tr');
    tr.id = 'fila_item_' + itemCounter;
    tr.innerHTML = `
        <td class="text-center align-middle">${tbody.rows.length + 1}</td>
        <td><input type="text" class="form-control form-control-sm" placeholder="001" value="${codigo}"></td>
        <td><input type="text" class="form-control form-control-sm" placeholder="Descripción del bien" value="${nombre}" required></td>
        <td><input type="number" class="form-control form-control-sm text-end" min="0.01" step="0.01" value="${cantidad}"></td>
        <td>
            <select class="form-select form-select-sm">
                <option value="NIU" ${unidad==='NIU'?'selected':''}>NIU - Unidad</option>
                <option value="KGM" ${unidad==='KGM'?'selected':''}>KGM - Kilogramo</option>
                <option value="LTR" ${unidad==='LTR'?'selected':''}>LTR - Litro</option>
                <option value="MTR" ${unidad==='MTR'?'selected':''}>MTR - Metro</option>
                <option value="BX"  ${unidad==='BX' ?'selected':''}>BX - Caja</option>
                <option value="DZN" ${unidad==='DZN'?'selected':''}>DZN - Docena</option>
            </select>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarFila('fila_item_${itemCounter}')">✕</button>
        </td>
    `;
    tbody.appendChild(tr);
    renumerarFilas();
}

function eliminarFila(id) {
    const fila = document.getElementById(id);
    if (fila) fila.remove();
    renumerarFilas();
}

function renumerarFilas() {
    document.querySelectorAll('#tbodyDetallesGuia tr td:first-child').forEach((td, i) => {
        td.textContent = i + 1;
    });
}

// Prellenar ítems desde una venta existente
function cargarItemsVenta(venta_id) {
    if (!venta_id) return;
    fetch(`logica/clssGuiaRemisionHandler.php?accion=OBTENER_ITEMS_VENTA&venta_id=${venta_id}`)
        .then(r => r.json())
        .then(res => {
            if (!res.estado) return;

            // Limpiar tabla y rellenar
            document.getElementById('tbodyDetallesGuia').innerHTML = '';
            itemCounter = 0;
            res.items.forEach(it => {
                agregarFilaDetalle(it.codigo_producto, it.nombre, it.cantidad, it.unidad || 'NIU');
            });

            // Prellenar destinatario si viene en los ítems
            const primero = res.items[0];
            if (primero) {
                if (primero.numero_doc_destinatario)
                    document.getElementById('gr_dest_numero_doc').value    = primero.numero_doc_destinatario;
                if (primero.razon_social_destinatario)
                    document.getElementById('gr_dest_razon_social').value  = primero.razon_social_destinatario;
            }
        });
}

// ═══════════════════════════════════════════════════════════
// GUARDAR Y ENVIAR GUÍA A SUNAT
// ═══════════════════════════════════════════════════════════
function guardarGuia() {
    ocultarAlerta();
    const tipo = document.getElementById('gr_tipo_comprobante').value;

    // Recopilar ítems
    const items = [];
    document.querySelectorAll('#tbodyDetallesGuia tr').forEach(tr => {
        const celdas = tr.querySelectorAll('input, select');
        if (celdas.length >= 4) {
            items.push({
                codigo_producto: celdas[0].value.trim() || '001',
                nombre:          celdas[1].value.trim(),
                cantidad:        parseFloat(celdas[2].value) || 1,
                unidad:          celdas[3].value || 'NIU',
            });
        }
    });

    if (!items.length || !items[0].nombre) {
        mostrarAlerta('Debe agregar al menos un ítem con descripción.', 'danger');
        return;
    }
    if (!document.getElementById('gr_dest_numero_doc').value.trim()) {
        mostrarAlerta('El número de documento del destinatario es requerido.', 'danger');
        return;
    }

    const jsGuia = JSON.stringify({
        emisor: {},   // El backend lo lee de BD via $conectar según sesión/sucursal
        cabecera: {
            tipo_comprobante:              tipo,
            fecha_emision:                 document.getElementById('gr_fecha_emision').value,
            fecha_traslado:                document.getElementById('gr_fecha_traslado').value,
            motivo_traslado:               document.getElementById('gr_motivo_traslado').value,
            modalidad_traslado:            tipo === '09' ? document.getElementById('gr_modalidad_traslado').value : '01',
            peso_bruto_total:              parseFloat(document.getElementById('gr_peso_bruto_total').value) || 0,
            ubigeo_partida:                document.getElementById('gr_ubigeo_partida').value.trim(),
            direccion_partida:             document.getElementById('gr_direccion_partida').value.trim(),
            placa_vehiculo:                document.getElementById('gr_placa_vehiculo').value.trim().toUpperCase(),
            conductor_tipo_doc:            document.getElementById('gr_conductor_tipo_doc').value,
            conductor_doc:                 document.getElementById('gr_conductor_doc').value.trim(),
            conductor_nombres:             document.getElementById('gr_conductor_nombres').value.trim(),
            conductor_apellidos:           document.getElementById('gr_conductor_apellidos').value.trim(),
            conductor_licencia:            document.getElementById('gr_conductor_licencia').value.trim(),
            transportista_ruc:             document.getElementById('gr_transportista_ruc').value.trim(),
            transportista_razon_social:    document.getElementById('gr_transportista_razon_social').value.trim(),
            comprobante_ref_tipo:          document.getElementById('gr_comprobante_ref_serie').value ? '01' : '',
            comprobante_ref_serie:         document.getElementById('gr_comprobante_ref_serie').value.trim(),
            comprobante_ref_correlativo:   document.getElementById('gr_comprobante_ref_correlativo').value.trim(),
            guia_remitente_serie:          document.getElementById('gr_guia_remitente_serie').value.trim(),
            guia_remitente_correlativo:    document.getElementById('gr_guia_remitente_correlativo').value.trim(),
            venta_id:                      document.getElementById('gr_venta_id').value || null,
        },
        destinatario: {
            tipo_documento:    document.getElementById('gr_dest_tipo_doc').value,
            numero_doc:        document.getElementById('gr_dest_numero_doc').value.trim(),
            razon_social:      document.getElementById('gr_dest_razon_social').value.trim(),
            ubigeo_llegada:    document.getElementById('gr_ubigeo_llegada').value.trim(),
            direccion_llegada: document.getElementById('gr_direccion_llegada').value.trim(),
        },
        detalles: items,
    });

    const accion = tipo === '31' ? 'REGISTRAR_GUIA_TRANSPORTISTA' : 'REGISTRAR_GUIA_REMITENTE';

    const btn = document.getElementById('btnGuardarGuia');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Enviando a SUNAT...';

    const fd = new FormData();
    fd.append('accion', accion);
    fd.append('jsGuia', jsGuia);

    fetch('logica/clssGuiaRemisionHandler.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Registrar y Enviar a SUNAT';

            if (res.estado) {
                mostrarAlerta('✅ ' + res.mensaje, 'success');
                cargarGuias();
                setTimeout(() => bootstrap.Modal.getInstance(document.getElementById('modalGuia')).hide(), 2000);
            } else {
                mostrarAlerta('❌ ' + res.mensaje, 'danger');
            }
        })
        .catch(e => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Registrar y Enviar a SUNAT';
            mostrarAlerta('Error de conexión: ' + e.message, 'danger');
        });
}

// ═══════════════════════════════════════════════════════════
// VER DETALLE DE UNA GUÍA
// ═══════════════════════════════════════════════════════════
function verGuia(id) {
    document.getElementById('contenidoVerGuia').innerHTML =
        '<p class="text-center"><div class="spinner-border text-secondary"></div></p>';
    new bootstrap.Modal(document.getElementById('modalVerGuia')).show();

    fetch(`logica/clssGuiaRemisionHandler.php?accion=OBTENER_GUIA&id=${id}`)
        .then(r => r.json())
        .then(res => {
            if (!res.estado) {
                document.getElementById('contenidoVerGuia').innerHTML =
                    `<div class="alert alert-danger">${res.mensaje}</div>`;
                return;
            }
            const g = res.guia;
            const badgeEstado = g.estado_envio == 1
                ? '<span class="badge bg-success fs-6">ENVIADO A SUNAT</span>'
                : '<span class="badge bg-danger fs-6">PENDIENTE</span>';

            let htmlItems = '';
            if (res.items && res.items.length) {
                res.items.forEach(it => {
                    htmlItems += `<tr>
                        <td>${it.item}</td>
                        <td>${it.codigo_producto}</td>
                        <td>${it.nombre}</td>
                        <td class="text-end">${parseFloat(it.cantidad).toFixed(2)}</td>
                        <td>${it.unidad}</td>
                    </tr>`;
                });
            }

            document.getElementById('contenidoVerGuia').innerHTML = `
                <div class="row g-2 mb-3">
                    <div class="col-6"><strong>Serie-Correlativo:</strong> ${g.serie}-${g.correlativo_texto}</div>
                    <div class="col-6 text-end">${badgeEstado}</div>
                    <div class="col-4"><strong>Tipo:</strong> ${g.tipo_guia}</div>
                    <div class="col-4"><strong>Fecha Emisión:</strong> ${g.fecha_emision}</div>
                    <div class="col-4"><strong>Fecha Traslado:</strong> ${g.fecha_traslado}</div>
                    <div class="col-6"><strong>Motivo:</strong> ${MOTIVOS_TRASLADO[g.motivo_traslado] || g.motivo_traslado}</div>
                    <div class="col-6"><strong>Modalidad:</strong> ${MODALIDADES[g.modalidad_traslado] || g.modalidad_traslado}</div>
                    <div class="col-6"><strong>Destinatario:</strong> ${g.razon_social_destinatario} (${g.numero_doc_destinatario})</div>
                    <div class="col-6"><strong>Placa:</strong> ${g.placa_vehiculo || '-'}</div>
                    <div class="col-6"><strong>Conductor:</strong> ${g.conductor_nombres || ''} ${g.conductor_apellidos || ''}</div>
                    <div class="col-6"><strong>Licencia:</strong> ${g.conductor_licencia || '-'}</div>
                    <div class="col-6"><strong>Partida:</strong> ${g.direccion_partida}</div>
                    <div class="col-6"><strong>Llegada:</strong> ${g.direccion_llegada}</div>
                    <div class="col-12"><strong>Mensaje SUNAT:</strong> <span class="text-muted">${g.mensaje_sunat || '-'}</span></div>
                </div>
                ${htmlItems ? `
                <h6 class="fw-bold">Bienes Trasladados</h6>
                <table class="table table-sm table-bordered">
                    <thead class="table-secondary">
                        <tr><th>#</th><th>Código</th><th>Descripción</th><th>Cantidad</th><th>Unidad</th></tr>
                    </thead>
                    <tbody>${htmlItems}</tbody>
                </table>` : ''}
            `;
        });
}

// ═══════════════════════════════════════════════════════════
// ANULAR GUÍA
// ═══════════════════════════════════════════════════════════
function anularGuia(id) {
    if (!confirm('¿Seguro que desea anular esta guía? (Solo se marcará en el sistema, SUNAT no acepta anulación de GR.)')) return;
    const fd = new FormData();
    fd.append('accion', 'ANULAR_GUIA');
    fd.append('id', id);
    fetch('logica/clssGuiaRemisionHandler.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            alert(res.mensaje);
            if (res.estado) cargarGuias();
        });
}

// ═══════════════════════════════════════════════════════════
// UTILIDADES
// ═══════════════════════════════════════════════════════════
function limpiarFormularioGuia() {
    itemCounter = 0;
    document.getElementById('tbodyDetallesGuia').innerHTML = '';
    ['gr_venta_id','gr_fecha_emision','gr_fecha_traslado','gr_ubigeo_partida','gr_direccion_partida',
     'gr_ubigeo_llegada','gr_direccion_llegada','gr_dest_numero_doc','gr_dest_razon_social',
     'gr_placa_vehiculo','gr_conductor_licencia','gr_conductor_doc','gr_conductor_nombres',
     'gr_conductor_apellidos','gr_transportista_ruc','gr_transportista_razon_social',
     'gr_comprobante_ref_serie','gr_comprobante_ref_correlativo',
     'gr_guia_remitente_serie','gr_guia_remitente_correlativo','gr_peso_bruto_total'
    ].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = id === 'gr_peso_bruto_total' ? '0' : '';
    });
    ocultarAlerta();
}

function mostrarAlerta(msg, tipo) {
    const div = document.getElementById('divAlertaGuia');
    div.style.display = '';
    div.innerHTML = `<div class="alert alert-${tipo} alert-sm py-1 px-2 mb-0">${msg}</div>`;
}

function ocultarAlerta() {
    const div = document.getElementById('divAlertaGuia');
    if (div) { div.style.display = 'none'; div.innerHTML = ''; }
}
</script>

<?php
include("pie.php");
?>