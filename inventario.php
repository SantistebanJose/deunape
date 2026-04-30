<?php
include("cabecera.php");
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=DM+Mono:wght@400;500&display=swap');

* { box-sizing: border-box; }

:root {
    --brand: #1a2346;
    --brand-light: #2a3a6e;
    --brand-accent: #3d6bff;
    --radius: 10px;
    --radius-lg: 14px;
}

body, .page-inner {
    font-family: 'DM Sans', sans-serif !important;
}

/* ── Page layout ── */
.inv-page { display: flex; flex-direction: column; gap: 20px; padding-bottom: 40px; }

/* ── Header ── */
.inv-hdr {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 0 0;
}
.inv-hdr-left h2 {
    font-size: 20px;
    font-weight: 600;
    letter-spacing: -0.3px;
    color: var(--brand);
    margin: 0;
}
.inv-hdr-left p {
    font-size: 13px;
    color: #6b7280;
    margin: 3px 0 0;
}
.btn-add-inv {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: var(--brand);
    color: #fff;
    border: none;
    border-radius: var(--radius);
    padding: 10px 18px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    font-family: 'DM Sans', sans-serif;
    transition: background .15s;
}
.btn-add-inv:hover { background: var(--brand-light); }
.btn-add-inv i { font-size: 12px; }

/* ── Stats row ── */
.inv-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}
.inv-stat {
    background: #f8f9fb;
    border-radius: var(--radius);
    padding: 14px 16px;
    border: 0.5px solid #e5e7eb;
}
.inv-stat-label {
    font-size: 11px;
    color: #6b7280;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 6px;
}
.inv-stat-val { font-size: 22px; font-weight: 600; letter-spacing: -0.5px; }
.inv-stat-val.navy { color: var(--brand); }
.inv-stat-val.green { color: #3B6D11; }
.inv-stat-val.amber { color: #854F0B; }
.inv-stat-val.blue  { color: #185FA5; }

/* ── Search bar ── */
.inv-searchbar { display: flex; align-items: center; gap: 10px; }
.inv-search-wrap { position: relative; flex: 1; }
.inv-search-wrap i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 13px;
}
.inv-search-wrap input {
    width: 100%;
    padding: 9px 12px 9px 36px;
    border: 0.5px solid #d1d5db;
    border-radius: var(--radius);
    font-size: 13px;
    font-family: 'DM Sans', sans-serif;
    background: #fff;
    outline: none;
    transition: border-color .15s;
}
.inv-search-wrap input:focus { border-color: var(--brand-accent); }

/* ── Table card ── */
.inv-table-card {
    background: #fff;
    border: 0.5px solid #e5e7eb;
    border-radius: var(--radius-lg);
    overflow: hidden;
}
.inv-table-card table {
    width: 100%;
    border-collapse: collapse;
    font-family: 'DM Sans', sans-serif;
}
.inv-table-card thead tr { background: #f8f9fb; }
.inv-table-card th {
    padding: 11px 16px;
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
    text-align: left;
    text-transform: uppercase;
    letter-spacing: .5px;
    border-bottom: 0.5px solid #e5e7eb;
}
.inv-table-card td {
    padding: 12px 16px;
    font-size: 13px;
    border-bottom: 0.5px solid #f3f4f6;
    vertical-align: middle;
}
.inv-table-card tbody tr:last-child td { border-bottom: none; }
.inv-table-card tbody tr:hover td { background: #fafafa; }

/* Article cell */
.inv-art-name { font-weight: 500; font-size: 13px; color: #111827; }
.inv-art-sub  { font-size: 11px; color: #9ca3af; margin-top: 1px; font-family: 'DM Mono', monospace; }

/* ID cell */
.inv-id { font-size: 12px; color: #9ca3af; font-family: 'DM Mono', monospace; }

/* Location pill */
.inv-loc-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #e6f1fb;
    color: #185FA5;
    border-radius: 20px;
    padding: 3px 10px;
    font-size: 12px;
    font-weight: 500;
}
.inv-loc-pill i { font-size: 10px; }

/* Structure */
.inv-struct { font-size: 12px; color: #6b7280; }
.inv-no-struct { font-size: 11px; color: #d1d5db; font-style: italic; }

/* Type badge */
.inv-type {
    display: inline-block;
    padding: 3px 9px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .3px;
}
.inv-type-ANDAMIO { background: #eaf3de; color: #3B6D11; }
.inv-type-ESTANTE  { background: #e6f1fb; color: #185FA5; }
.inv-type-dash     { color: #d1d5db; font-size: 13px; }

/* Stock badge */
.inv-stock {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    font-family: 'DM Mono', monospace;
}
.inv-stock-ok   { background: #eaf3de; color: #3B6D11; }
.inv-stock-zero { background: #fcebeb; color: #A32D2D; }
.inv-stock-low  { background: #faeeda; color: #854F0B; }

/* Action buttons */
.inv-actions { display: flex; gap: 6px; }
.inv-btn-icon {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    border: 0.5px solid #e5e7eb;
    background: #fff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transition: all .15s;
    color: #6b7280;
}
.inv-btn-edit:hover  { background: #e6f1fb; border-color: #185FA5; color: #185FA5; }
.inv-btn-del:hover   { background: #fcebeb; border-color: #A32D2D; color: #A32D2D; }

/* ── Modal ── */
#modalInventario .modal-content {
    border-radius: var(--radius-lg) !important;
    border: 0.5px solid #e5e7eb !important;
    box-shadow: 0 20px 60px rgba(26,35,70,.15) !important;
    font-family: 'DM Sans', sans-serif;
    overflow: hidden;
}
#modalInventario .modal-header {
    background: #fff;
    border-bottom: 0.5px solid #f3f4f6;
    padding: 18px 22px;
    align-items: center;
}
#modalInventario .modal-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--brand);
}
.inv-modal-badge {
    display: inline-block;
    background: #e6f1fb;
    color: #185FA5;
    font-size: 11px;
    padding: 3px 10px;
    border-radius: 20px;
    font-weight: 500;
    margin-left: 8px;
}
#modalInventario .modal-body { padding: 22px; background: #fff; }
#modalInventario .modal-footer {
    background: #f8f9fb;
    border-top: 0.5px solid #f3f4f6;
    padding: 14px 22px;
}

/* Form elements */
.inv-form-label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 6px;
}
.inv-form-label .req { color: #E24B4A; }
.inv-form-control {
    width: 100%;
    padding: 9px 12px;
    border: 0.5px solid #d1d5db;
    border-radius: var(--radius);
    background: #f8f9fb;
    font-size: 13px;
    font-family: 'DM Sans', sans-serif;
    color: #111827;
    outline: none;
    transition: border-color .15s, background .15s;
}
.inv-form-control:focus {
    border-color: var(--brand-accent);
    background: #fff;
}
.inv-form-hint { font-size: 11px; color: #9ca3af; margin-top: 4px; }
.inv-form-row  { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.inv-mb-3 { margin-bottom: 16px; }

/* Autocomplete suggestions */
.inv-suggest-wrap { position: relative; }
#sugerenciasArticulo {
    position: absolute;
    width: 100%;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1080;
    border: 0.5px solid #d1d5db;
    border-top: none;
    border-radius: 0 0 var(--radius) var(--radius);
    background: #fff;
    box-shadow: 0 8px 24px rgba(0,0,0,.08);
}
#sugerenciasArticulo .inv-sug-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 9px 12px;
    font-size: 13px;
    cursor: pointer;
    border-bottom: 0.5px solid #f3f4f6;
    transition: background .1s;
}
#sugerenciasArticulo .inv-sug-item:last-child { border-bottom: none; }
#sugerenciasArticulo .inv-sug-item:hover { background: #f8f9fb; }
#sugerenciasArticulo .inv-sug-price {
    font-size: 12px;
    color: #6b7280;
    font-family: 'DM Mono', monospace;
}
#sugerenciasArticulo .inv-sug-empty {
    padding: 10px 12px;
    font-size: 13px;
    color: #9ca3af;
    text-align: center;
}

/* Footer buttons */
.inv-btn-cancel {
    padding: 9px 18px;
    border: 0.5px solid #d1d5db;
    border-radius: var(--radius);
    background: #fff;
    font-size: 13px;
    font-weight: 500;
    font-family: 'DM Sans', sans-serif;
    color: #374151;
    cursor: pointer;
    transition: background .15s;
}
.inv-btn-cancel:hover { background: #f3f4f6; }
.inv-btn-save {
    padding: 9px 22px;
    border: none;
    border-radius: var(--radius);
    background: var(--brand);
    color: #fff;
    font-size: 13px;
    font-weight: 500;
    font-family: 'DM Sans', sans-serif;
    cursor: pointer;
    transition: background .15s;
}
.inv-btn-save:hover { background: var(--brand-light); }

/* DataTables overrides */
#tbl-inventario_wrapper .dataTables_filter input {
    border: 0.5px solid #d1d5db !important;
    border-radius: var(--radius) !important;
    padding: 6px 10px !important;
    font-family: 'DM Sans', sans-serif !important;
    font-size: 13px !important;
    outline: none !important;
}
#tbl-inventario_wrapper .dataTables_filter input:focus { border-color: var(--brand-accent) !important; }
#tbl-inventario_wrapper .dataTables_length select {
    border: 0.5px solid #d1d5db !important;
    border-radius: 8px !important;
    padding: 5px 8px !important;
    font-family: 'DM Sans', sans-serif !important;
    font-size: 13px !important;
}
#tbl-inventario_wrapper .dataTables_info,
#tbl-inventario_wrapper .dataTables_length label,
#tbl-inventario_wrapper .dataTables_filter label {
    font-size: 13px !important;
    font-family: 'DM Sans', sans-serif !important;
    color: #6b7280 !important;
}
#tbl-inventario_wrapper .paginate_button {
    border-radius: 8px !important;
    font-family: 'DM Sans', sans-serif !important;
    font-size: 13px !important;
}
#tbl-inventario_wrapper .paginate_button.current,
#tbl-inventario_wrapper .paginate_button.current:hover {
    background: var(--brand) !important;
    border-color: var(--brand) !important;
    color: #fff !important;
}
</style>

<div class="container">
    <div class="page-inner">
        <div class="inv-page">

            <!-- Header -->
            <div class="inv-hdr">
                <div class="inv-hdr-left">
                    <h2>Distribución de Inventario</h2>
                    <p>Gestión de stock por locación y estructura</p>
                </div>
                <button class="btn-add-inv" id="btnAbrirModalInventario">
                    Agregar distribución &nbsp;<i class="fas fa-plus"></i>
                </button>
            </div>

            <!-- Stats -->
            <div class="inv-stats" id="inv-stats">
                <div class="inv-stat">
                    <div class="inv-stat-label">Total registros</div>
                    <div class="inv-stat-val navy" id="stat-total">—</div>
                </div>
                <div class="inv-stat">
                    <div class="inv-stat-label">Con stock</div>
                    <div class="inv-stat-val green" id="stat-ok">—</div>
                </div>
                <div class="inv-stat">
                    <div class="inv-stat-label">Sin stock</div>
                    <div class="inv-stat-val amber" id="stat-zero">—</div>
                </div>
                <div class="inv-stat">
                    <div class="inv-stat-label">Locaciones activas</div>
                    <div class="inv-stat-val blue" id="stat-loc">—</div>
                </div>
            </div>

            <!-- Table card -->
            <div class="inv-table-card">
                <div style="padding:14px 16px;border-bottom:0.5px solid #f3f4f6;">
                    <table id="tbl-inventario" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Artículo</th>
                                <th>Locación</th>
                                <th>Estructura</th>
                                <th>Tipo</th>
                                <th>Stock</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

        </div><!-- /.inv-page -->
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalInventario" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="contenidoInventario"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link  rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/scriptNotify.js"></script>

<script>
const SUCURSAL_ID = "<?php echo $_SESSION['sucursal_id'] ?? ''; ?>";
const URL_LOGICA  = "logica/clssInventario.php";

// ================================================
// DATATABLE
// ================================================
let dt;
$(document).ready(function () {
    dt = $("#tbl-inventario").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: URL_LOGICA,
            type: "POST",
            data: function (d) {
                d.accion      = "LISTAR";
                d.sucursal_id = SUCURSAL_ID;
            },
            dataSrc: function (json) {
                // Actualizar stats desde cabecera (si el backend los devuelve)
                if (json.stats) {
                    document.getElementById('stat-total').textContent = json.stats.total  ?? '—';
                    document.getElementById('stat-ok').textContent    = json.stats.ok     ?? '—';
                    document.getElementById('stat-zero').textContent  = json.stats.zero   ?? '—';
                    document.getElementById('stat-loc').textContent   = json.stats.locaciones ?? '—';
                } else {
                    // Fallback: estimar desde los datos
                    document.getElementById('stat-total').textContent = json.recordsTotal;
                }
                return json.data;
            },
            error: function (xhr) { console.error("Error DataTables:", xhr.responseText); }
        },
        columns: [
            { data: "id" },
            { data: "nombre_articulo" },
            { data: "nombre_locacion" },
            { data: "nombre_estructura" },
            { data: "tipo_estructura" },
            { data: "stock" },
            { data: "acciones", orderable: false, searchable: false }
        ],
        pageLength: 10,
        language: {
            sProcessing:     "Procesando...",
            sLengthMenu:     "Mostrar _MENU_ registros",
            sZeroRecords:    "No se encontraron resultados",
            sEmptyTable:     "Ningún dato disponible",
            sInfo:           "Registros _START_ al _END_ de _TOTAL_",
            sInfoEmpty:      "Registros 0 al 0 de 0",
            sInfoFiltered:   "(de _MAX_ totales)",
            sSearch:         "Buscar:",
            sLoadingRecords: "Cargando...",
            oPaginate: { sFirst:"Primero", sPrevious:"Anterior", sNext:"Siguiente", sLast:"Último" }
        }
    });
});

// ================================================
// ABRIR MODAL — REGISTRAR
// ================================================
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("btnAbrirModalInventario").addEventListener("click", abrirModalRegistro);
});

function abrirModalRegistro() {
    document.getElementById("contenidoInventario").innerHTML = buildFormHTML("Distribuir Artículo", "Nuevo", "btnAccionInventario", "Registrar");
    new bootstrap.Modal(document.getElementById("modalInventario")).show();
    inicializarFormulario();

    document.getElementById("btnAccionInventario").addEventListener("click", async function () {
        if (!validarCampos()) return;
        const datos = recogerDatos();
        try {
            const res = await fnAjax("REGISTRAR", { data: JSON.stringify(datos) });
            if (res.success) {
                await Swal.fire({ title: "¡Registrado!", text: res.message, icon: "success", timer: 1500, showConfirmButton: false });
                dt.ajax.reload();
            } else {
                Swal.fire("Aviso", res.message || "Error al registrar", "warning");
            }
        } catch (e) { Swal.fire("Error", e.message, "error"); }
    });
}

// ================================================
// EDITAR
// ================================================
function fn_editar_inventario(rowJson) {
    const row = JSON.parse(rowJson);
    document.getElementById("contenidoInventario").innerHTML = buildFormHTML("Editar Distribución", "Editando", "btnAccionInventario", "Actualizar");
    new bootstrap.Modal(document.getElementById("modalInventario")).show();

    inicializarFormulario(function () {
        document.getElementById("inventario_id").value = row.id;
        document.getElementById("articulo_id").value   = row.articulo_id;
        document.getElementById("inputArticulo").value = row.nombre_articulo;
        document.getElementById("inputStock").value    = row.stock;

        // Asignar locación y disparar change — el listener ya existe
        const selLoc = document.getElementById("selectLocacion");
        selLoc.value = row.locacion_id;
        selLoc.dispatchEvent(new Event("change"));

        // Esperar a que LISTAR_ESTRUCTURAS responda y luego asignar estructura
        if (row.estructura_id) {
            const selEst = document.getElementById("selectEstructura");
            const observer = new MutationObserver(function () {
                const opt = selEst.querySelector(`option[value="${row.estructura_id}"]`);
                if (opt) {
                    selEst.value = row.estructura_id;
                    observer.disconnect();
                }
            });
            observer.observe(selEst, { childList: true });
        }
    });

    document.getElementById("btnAccionInventario").addEventListener("click", async function () {
        if (!validarCampos()) return;
        const datos = recogerDatos();
        datos.id = document.getElementById("inventario_id").value;
        try {
            const res = await fnAjax("ACTUALIZAR", { data: JSON.stringify(datos) });
            if (res.success) {
                await Swal.fire({ title: "¡Actualizado!", text: res.message, icon: "success", timer: 1500, showConfirmButton: false });
                dt.ajax.reload(null, false);
            } else {
                Swal.fire("Error", res.message || "Error al actualizar", "error");
            }
        } catch (e) { Swal.fire("Error", e.message, "error"); }
    });
}
// ================================================
// ELIMINAR
// ================================================
function fn_eliminar_inventario(id) {
    Swal.fire({
        title: '¿Eliminar registro?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#A32D2D',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const res = await fnAjax("ELIMINAR", { id });
                if (res.success) { dt.ajax.reload(null, false); }
                else { Swal.fire("Error", res.message, "error"); }
            } catch (e) { Swal.fire("Error", e.message, "error"); }
        }
    });
}

// ================================================
// BUILD FORM HTML
// ================================================
function buildFormHTML(titulo, badge, btnId, btnLabel) {
    return `
    <div class="modal-header">
        <span class="modal-title"><i class="fas fa-boxes me-2"></i>${titulo}</span>
        <div style="display:flex;align-items:center;gap:10px">
            <span class="inv-modal-badge">${badge}</span>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
    </div>
    <div class="modal-body">
        <p style="font-size:12px;color:#9ca3af;margin-bottom:18px">
            Los campos con <span style="color:#E24B4A;font-weight:600">*</span> son obligatorios.
        </p>

        <input type="hidden" id="inventario_id">
        <input type="hidden" id="articulo_id">

        <!-- Artículo -->
        <div class="inv-mb-3 inv-suggest-wrap">
            <label class="inv-form-label">Artículo <span class="req">*</span></label>
            <input type="text" class="inv-form-control" id="inputArticulo"
                   placeholder="Escribe para buscar artículos..." autocomplete="off">
            <div id="sugerenciasArticulo"></div>
            <div class="inv-form-hint">Mínimo 2 caracteres para buscar</div>
            <div style="color:#E24B4A;font-size:12px;margin-top:4px;display:none" id="error-articulo"></div>
        </div>

        <!-- Locación + Estructura -->
        <div class="inv-form-row inv-mb-3">
            <div>
                <label class="inv-form-label">Locación <span class="req">*</span></label>
                <select class="inv-form-control" id="selectLocacion">
                    <option value="">Cargando...</option>
                </select>
                <div style="color:#E24B4A;font-size:12px;margin-top:4px;display:none" id="error-locacion"></div>
            </div>
            <div>
                <label class="inv-form-label">Estructura</label>
                <select class="inv-form-control" id="selectEstructura">
                    <option value="">— Sin estructura —</option>
                </select>
            </div>
        </div>

        <!-- Stock -->
        <div class="inv-mb-3">
            <label class="inv-form-label">Stock a distribuir <span class="req">*</span></label>
            <input type="number" class="inv-form-control" id="inputStock"
                   placeholder="0" min="0" step="1" style="max-width:180px">
            <div style="color:#E24B4A;font-size:12px;margin-top:4px;display:none" id="error-stock"></div>
        </div>
    </div>
    <div class="modal-footer" style="gap:10px">
        <button type="button" class="inv-btn-cancel" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="inv-btn-save" id="${btnId}">${btnLabel}</button>
    </div>`;
}

// ================================================
// INICIALIZAR FORMULARIO
// ================================================
function inicializarFormulario(callback) {
    // 1. Registrar el listener PRIMERO, antes de cualquier async
    const selectLocacion = document.getElementById("selectLocacion");

    selectLocacion.addEventListener("change", function () {
        const locacion_id = this.value;
        const selEst = document.getElementById("selectEstructura");
        selEst.innerHTML = '<option value="">— Sin estructura —</option>';
        if (!locacion_id) return;

        fnAjax("LISTAR_ESTRUCTURAS", { locacion_id }).then(res => {
            if (res.success && res.data.length > 0) {
                res.data.forEach(est => {
                    const opt = document.createElement("option");
                    opt.value       = est.id;
                    opt.textContent = `[${est.tipo}] ${est.nombre}`;
                    selEst.appendChild(opt);
                });
            }
        }).catch(e => console.error("Error cargando estructuras:", e));
    });

    // 2. Autocomplete
    let timer;
    document.getElementById("inputArticulo").addEventListener("input", function () {
        const term  = this.value.trim();
        const lista = document.getElementById("sugerenciasArticulo");
        clearTimeout(timer);
        document.getElementById("articulo_id").value = '';
        if (term.length < 2) { lista.innerHTML = ''; return; }
        timer = setTimeout(() => {
            fnAjax("BUSCAR_ARTICULO", { term }).then(res => {
                lista.innerHTML = '';
                if (res.success && res.data.length > 0) {
                    res.data.forEach(art => {
                        const item = document.createElement("div");
                        item.className = "inv-sug-item";
                        item.innerHTML = `
                            <span>${art.nombre}</span>
                            <span class="inv-sug-price">S/ ${parseFloat(art.precio_venta || 0).toFixed(2)}</span>`;
                        item.addEventListener("click", () => {
                            document.getElementById("inputArticulo").value = art.nombre;
                            document.getElementById("articulo_id").value   = art.id;
                            lista.innerHTML = '';
                        });
                        lista.appendChild(item);
                    });
                } else {
                    lista.innerHTML = '<div class="inv-sug-empty">No se encontraron artículos</div>';
                }
            });
        }, 300);
    });

    document.addEventListener("click", function (e) {
        if (!e.target.closest(".inv-suggest-wrap")) {
            const l = document.getElementById("sugerenciasArticulo");
            if (l) l.innerHTML = '';
        }
    });

    // 3. Cargar locaciones y LUEGO llamar callback
    fnAjax("LISTAR_LOCACIONES", {}).then(function (res) {
        const sel = document.getElementById("selectLocacion");
        sel.innerHTML = '<option value="">Seleccione una locación</option>';
        if (res.success && res.data.length > 0) {
            res.data.forEach(loc => {
                const opt = document.createElement("option");
                opt.value       = loc.id;
                opt.textContent = `[${loc.tipo}] ${loc.nombre}`;
                sel.appendChild(opt);
            });
        } else {
            sel.innerHTML = '<option value="">Sin locaciones disponibles</option>';
        }
        // callback DESPUÉS de poblar locaciones,
        // y el listener ya está registrado desde el paso 1
        if (typeof callback === 'function') callback();
    }).catch(e => console.error("Error cargando locaciones:", e));
}
// ================================================
// VALIDAR
// ================================================
function validarCampos() {
    let valid = true;

    const artId  = document.getElementById("articulo_id").value;
    const eArt   = document.getElementById("error-articulo");
    const iArt   = document.getElementById("inputArticulo");
    if (!artId) {
        valid = false;
        iArt.style.borderColor = '#E24B4A';
        eArt.textContent = "Selecciona un artículo de la lista.";
        eArt.style.display = 'block';
    } else {
        iArt.style.borderColor = '';
        eArt.style.display = 'none';
    }

    const selLoc = document.getElementById("selectLocacion");
    const eLoc   = document.getElementById("error-locacion");
    if (!selLoc.value) {
        valid = false;
        selLoc.style.borderColor = '#E24B4A';
        eLoc.textContent = "Selecciona una locación.";
        eLoc.style.display = 'block';
    } else {
        selLoc.style.borderColor = '';
        eLoc.style.display = 'none';
    }

    const iStock = document.getElementById("inputStock");
    const eStock = document.getElementById("error-stock");
    if (iStock.value === '' || isNaN(iStock.value) || parseFloat(iStock.value) < 0) {
        valid = false;
        iStock.style.borderColor = '#E24B4A';
        eStock.textContent = "Ingresa un stock válido (≥ 0).";
        eStock.style.display = 'block';
    } else {
        iStock.style.borderColor = '';
        eStock.style.display = 'none';
    }

    return valid;
}

// ================================================
// RECOGER DATOS
// ================================================
function recogerDatos() {
    return {
        articulo_id:   document.getElementById("articulo_id").value,
        locacion_id:   document.getElementById("selectLocacion").value,
        estructura_id: document.getElementById("selectEstructura").value || null,
        stock:         document.getElementById("inputStock").value
    };
}

// ================================================
// AJAX HELPER
// ================================================
function fnAjax(accion, extraData) {
    return new Promise((resolve, reject) => {
        $.ajax({
            method: "POST",
            url: URL_LOGICA,
            data: Object.assign({ accion, sucursal_id: SUCURSAL_ID }, extraData)
        }).done(response => {
            try { resolve(typeof response === "string" ? JSON.parse(response) : response); }
            catch (e) { reject(new Error("Respuesta inválida: " + response)); }
        }).fail(xhr => { reject(new Error("Error de conexión: " + xhr.status)); });
    });
}
</script>

<?php include("pie.php"); ?>