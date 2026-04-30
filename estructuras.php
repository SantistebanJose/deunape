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
.est-page { display: flex; flex-direction: column; gap: 20px; padding-bottom: 40px; }

/* ── Header ── */
.est-hdr {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 0 0;
}
.est-hdr-left h2 {
    font-size: 20px;
    font-weight: 600;
    letter-spacing: -0.3px;
    color: var(--brand);
    margin: 0;
}
.est-hdr-left p {
    font-size: 13px;
    color: #6b7280;
    margin: 3px 0 0;
}
.btn-add-est {
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
.btn-add-est:hover { background: var(--brand-light); }
.btn-add-est i { font-size: 12px; }

/* ── Stats row ── */
.est-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}
.est-stat {
    background: #f8f9fb;
    border-radius: var(--radius);
    padding: 14px 16px;
    border: 0.5px solid #e5e7eb;
}
.est-stat-label {
    font-size: 11px;
    color: #6b7280;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 6px;
}
.est-stat-val { font-size: 22px; font-weight: 600; letter-spacing: -0.5px; }
.est-stat-val.navy  { color: var(--brand); }
.est-stat-val.green { color: #3B6D11; }
.est-stat-val.amber { color: #854F0B; }
.est-stat-val.blue  { color: #185FA5; }

/* ── Table card ── */
.est-table-card {
    background: #fff;
    border: 0.5px solid #e5e7eb;
    border-radius: var(--radius-lg);
    overflow: hidden;
}
.est-table-card table {
    width: 100%;
    border-collapse: collapse;
    font-family: 'DM Sans', sans-serif;
}
.est-table-card thead tr { background: #f8f9fb; }
.est-table-card th {
    padding: 11px 16px;
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
    text-align: left;
    text-transform: uppercase;
    letter-spacing: .5px;
    border-bottom: 0.5px solid #e5e7eb;
}
.est-table-card td {
    padding: 12px 16px;
    font-size: 13px;
    border-bottom: 0.5px solid #f3f4f6;
    vertical-align: middle;
}
.est-table-card tbody tr:last-child td { border-bottom: none; }
.est-table-card tbody tr:hover td { background: #fafafa; }

/* ID cell */
.est-id { font-size: 12px; color: #9ca3af; font-family: 'DM Mono', monospace; }

/* Nombre cell */
.est-name     { font-weight: 500; font-size: 13px; color: #111827; }
.est-name-sub { font-size: 11px; color: #9ca3af; margin-top: 1px; font-family: 'DM Mono', monospace; }

/* Locación pill */
.est-loc-pill {
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
.est-loc-pill i { font-size: 10px; }

/* Tipo badge */
.est-tipo {
    display: inline-block;
    padding: 3px 9px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .3px;
}
.est-tipo-ANDAMIO { background: #eaf3de; color: #3B6D11; }
.est-tipo-ESTANTE  { background: #e6f1fb; color: #185FA5; }
.est-tipo-OTRO     { background: #f3f4f6; color: #6b7280; }

/* Posición / Capacidad badge */
.est-cap {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    font-family: 'DM Mono', monospace;
    background: #f3f4f6;
    color: #374151;
}
.est-cap-null { color: #d1d5db; font-size: 11px; font-style: italic; }

/* Action buttons */
.est-actions { display: flex; gap: 6px; }
.est-btn-icon {
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
.est-btn-edit:hover { background: #e6f1fb; border-color: #185FA5; color: #185FA5; }
.est-btn-del:hover  { background: #fcebeb; border-color: #A32D2D; color: #A32D2D; }

/* ── Modal ── */
#modalEstructura .modal-content {
    border-radius: var(--radius-lg) !important;
    border: 0.5px solid #e5e7eb !important;
    box-shadow: 0 20px 60px rgba(26,35,70,.15) !important;
    font-family: 'DM Sans', sans-serif;
    overflow: hidden;
}
#modalEstructura .modal-header {
    background: #fff;
    border-bottom: 0.5px solid #f3f4f6;
    padding: 18px 22px;
    align-items: center;
}
#modalEstructura .modal-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--brand);
}
.est-modal-badge {
    display: inline-block;
    background: #e6f1fb;
    color: #185FA5;
    font-size: 11px;
    padding: 3px 10px;
    border-radius: 20px;
    font-weight: 500;
    margin-left: 8px;
}
#modalEstructura .modal-body   { padding: 22px; background: #fff; }
#modalEstructura .modal-footer {
    background: #f8f9fb;
    border-top: 0.5px solid #f3f4f6;
    padding: 14px 22px;
}

/* Form elements */
.est-form-label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 6px;
}
.est-form-label .req { color: #E24B4A; }
.est-form-control {
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
.est-form-control:focus {
    border-color: var(--brand-accent);
    background: #fff;
}
.est-form-hint { font-size: 11px; color: #9ca3af; margin-top: 4px; }
.est-form-row  { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.est-mb-3 { margin-bottom: 16px; }
.est-field-error {
    color: #E24B4A;
    font-size: 12px;
    margin-top: 4px;
    display: none;
}

/* Footer buttons */
.est-btn-cancel {
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
.est-btn-cancel:hover { background: #f3f4f6; }
.est-btn-save {
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
.est-btn-save:hover { background: var(--brand-light); }

/* DataTables overrides */
#tbl-estructuras_wrapper .dataTables_filter input {
    border: 0.5px solid #d1d5db !important;
    border-radius: var(--radius) !important;
    padding: 6px 10px !important;
    font-family: 'DM Sans', sans-serif !important;
    font-size: 13px !important;
    outline: none !important;
}
#tbl-estructuras_wrapper .dataTables_filter input:focus { border-color: var(--brand-accent) !important; }
#tbl-estructuras_wrapper .dataTables_length select {
    border: 0.5px solid #d1d5db !important;
    border-radius: 8px !important;
    padding: 5px 8px !important;
    font-family: 'DM Sans', sans-serif !important;
    font-size: 13px !important;
}
#tbl-estructuras_wrapper .dataTables_info,
#tbl-estructuras_wrapper .dataTables_length label,
#tbl-estructuras_wrapper .dataTables_filter label {
    font-size: 13px !important;
    font-family: 'DM Sans', sans-serif !important;
    color: #6b7280 !important;
}
#tbl-estructuras_wrapper .paginate_button {
    border-radius: 8px !important;
    font-family: 'DM Sans', sans-serif !important;
    font-size: 13px !important;
}
#tbl-estructuras_wrapper .paginate_button.current,
#tbl-estructuras_wrapper .paginate_button.current:hover {
    background: var(--brand) !important;
    border-color: var(--brand) !important;
    color: #fff !important;
}
</style>

<div class="container">
    <div class="page-inner">
        <div class="est-page">

            <!-- Header -->
            <div class="est-hdr">
                <div class="est-hdr-left">
                    <h2>Estructuras de Almacén</h2>
                    <p>Gestión de andamios, estantes y otras estructuras por locación</p>
                </div>
                <button class="btn-add-est" id="btnAbrirModalEstructura">
                    Nueva estructura &nbsp;<i class="fas fa-plus"></i>
                </button>
            </div>

            <!-- Stats -->
            <div class="est-stats">
                <div class="est-stat">
                    <div class="est-stat-label">Total estructuras</div>
                    <div class="est-stat-val navy" id="stat-total">—</div>
                </div>
                <div class="est-stat">
                    <div class="est-stat-label">Andamios</div>
                    <div class="est-stat-val green" id="stat-andamios">—</div>
                </div>
                <div class="est-stat">
                    <div class="est-stat-label">Estantes</div>
                    <div class="est-stat-val amber" id="stat-estantes">—</div>
                </div>
                <div class="est-stat">
                    <div class="est-stat-label">Locaciones con estructuras</div>
                    <div class="est-stat-val blue" id="stat-locaciones">—</div>
                </div>
            </div>

            <!-- Table card -->
            <div class="est-table-card">
                <div style="padding:14px 16px;border-bottom:0.5px solid #f3f4f6;">
                    <table id="tbl-estructuras" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Locación</th>
                                <th>Tipo</th>
                                <th>Posición</th>
                                <th>Referencia</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

        </div><!-- /.est-page -->
    </div>
</div>

<!-- Modal — el contenido se inyecta dinámicamente -->
<div class="modal fade" id="modalEstructura" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="contenidoEstructura"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link  rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/scriptNotify.js"></script>

<script>
const SUCURSAL_ID = "<?php echo $_SESSION['sucursal_id'] ?? ''; ?>";
const URL_LOGICA  = "logica/clssEstructuras.php";

// ============================================================
// DATATABLE
// ============================================================
let dt;

$(document).ready(function () {
    dt = $("#tbl-estructuras").DataTable({
        processing : true,
        serverSide : true,
        ajax: {
            url  : URL_LOGICA,
            type : "POST",
            data : function (d) {
                d.accion      = "LISTAR";
                d.sucursal_id = SUCURSAL_ID;
            },
            // FIX ① — siempre retorna json.data aunque no vengan stats
            dataSrc: function (json) {
                if (json.stats) {
                    document.getElementById('stat-total').textContent      = json.stats.total      ?? '—';
                    document.getElementById('stat-andamios').textContent   = json.stats.andamios   ?? '—';
                    document.getElementById('stat-estantes').textContent   = json.stats.estantes   ?? '—';
                    document.getElementById('stat-locaciones').textContent = json.stats.locaciones ?? '—';
                } else {
                    document.getElementById('stat-total').textContent = json.recordsTotal ?? '—';
                }
                return json.data ?? [];
            },
            error: function (xhr) {
                console.error("Error DataTables:", xhr.responseText);
            }
        },
        columns: [
            {
                data: "id",
                render: v => `<span class="est-id">#${v}</span>`
            },
            {
                data: "nombre",
                render: (v, _, row) => `
                    <div class="est-name">${v}</div>
                    ${row.codigo ? `<div class="est-name-sub">${row.codigo}</div>` : ''}`
            },
            {
                data: "nombre_locacion",
                render: v => v
                    ? `<span class="est-loc-pill"><i class="fas fa-map-marker-alt"></i>${v}</span>`
                    : `<span style="color:#d1d5db;font-size:12px;">—</span>`
            },
            {
                data: "tipo",
                render: v => v
                    ? `<span class="est-tipo est-tipo-${v}">${v}</span>`
                    : `<span style="color:#d1d5db">—</span>`
            },
            {
                data: "capacidad",
                render: v => (v !== null && v !== '')
                    ? `<span class="est-cap"><i class="fas fa-sort-numeric-up" style="font-size:10px;opacity:.6"></i>${v}</span>`
                    : `<span class="est-cap-null">—</span>`
            },
            {
                data: "codigo",
                render: v => v
                    ? `<span style="font-size:12px;color:#6b7280;font-family:'DM Mono',monospace">${v}</span>`
                    : `<span style="color:#d1d5db;font-size:11px;font-style:italic">—</span>`
            },
            {
                // FIX ② — pasar id numérico directo; no serializar row completo inline
                data: "id",
                orderable : false,
                searchable: false,
                render: (v, _, row) => `
                    <div class="est-actions">
                        <button class="est-btn-icon est-btn-edit" title="Editar"
                            data-id="${v}" onclick="fn_editar_estructura(${v})">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="est-btn-icon est-btn-del" title="Eliminar"
                            onclick="fn_eliminar_estructura(${v})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>`
            }
        ],
        pageLength: 10,
        language: {
            sProcessing  : "Procesando...",
            sLengthMenu  : "Mostrar _MENU_ registros",
            sZeroRecords : "No se encontraron resultados",
            sEmptyTable  : "Ningún dato disponible",
            sInfo        : "Registros _START_ al _END_ de _TOTAL_",
            sInfoEmpty   : "Registros 0 al 0 de 0",
            sInfoFiltered: "(de _MAX_ totales)",
            sSearch      : "Buscar:",
            sLoadingRecords: "Cargando...",
            oPaginate    : { sFirst:"Primero", sPrevious:"Anterior", sNext:"Siguiente", sLast:"Último" }
        }
    });
});

// ============================================================
// HELPER — abre/cierra modal limpiamente
// FIX ③ — destruir instancia previa para evitar listeners apilados
// ============================================================
function abrirModal(html) {
    const el = document.getElementById('modalEstructura');

    // Destruir instancia Bootstrap anterior si existe
    const instancia = bootstrap.Modal.getInstance(el);
    if (instancia) instancia.dispose();

    document.getElementById('contenidoEstructura').innerHTML = html;
    new bootstrap.Modal(el).show();
}

// ============================================================
// REGISTRAR — abrir modal
// ============================================================
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("btnAbrirModalEstructura")
        .addEventListener("click", abrirModalRegistro);
});

function abrirModalRegistro() {
    abrirModal(buildFormHTML("Nueva Estructura", "Nuevo", "btnAccionEstructura", "Registrar"));
    cargarLocaciones();

    document.getElementById("btnAccionEstructura").addEventListener("click", async function () {
        if (!validarCampos()) return;

        const datos = recogerDatos();   // NO lleva id

        try {
            const res = await fnAjax("REGISTRAR", { data: JSON.stringify(datos) });
            if (res.success) {
                await Swal.fire({ title: "¡Registrado!", text: res.message,
                                  icon: "success", timer: 1500, showConfirmButton: false });
                bootstrap.Modal.getInstance(document.getElementById("modalEstructura")).hide();
                dt.ajax.reload();
            } else {
                Swal.fire("Aviso", res.message || "Error al registrar", "warning");
            }
        } catch (e) { Swal.fire("Error", e.message, "error"); }
    });
}

// ============================================================
// EDITAR
// FIX ④ — buscar la fila por id en DataTables en lugar de
//          serializar/deserializar el JSON en el onclick HTML
// ============================================================
function fn_editar_estructura(id) {
    // Buscar la fila en los datos de DataTables (evita problemas de escape)
    const rowData = dt.rows().data().toArray().find(r => r.id == id);

    if (!rowData) {
        Swal.fire("Error", "No se encontraron los datos de la estructura.", "error");
        return;
    }

    abrirModal(buildFormHTML("Editar Estructura", "Editando", "btnAccionEstructura", "Actualizar"));

    // Cargar locaciones y luego rellenar el form
    cargarLocaciones(function () {
        document.getElementById("estructura_id").value  = rowData.id;
        document.getElementById("inputNombre").value    = rowData.nombre      || '';
        document.getElementById("inputCodigo").value    = rowData.codigo      || '';  // referencia
        document.getElementById("selectTipo").value     = rowData.tipo        || '';
        document.getElementById("inputPosicion").value  = rowData.capacidad   ?? ''; // posicion
        document.getElementById("selectLocacion").value = rowData.locacion_id || '';
    });

    document.getElementById("btnAccionEstructura").addEventListener("click", async function () {
        if (!validarCampos()) return;

        const datos = recogerDatos();
        // FIX ⑤ — incluir id DENTRO del objeto que se serializa a JSON
        datos.id = document.getElementById("estructura_id").value;

        try {
            const res = await fnAjax("ACTUALIZAR", { data: JSON.stringify(datos) });
            if (res.success) {
                await Swal.fire({ title: "¡Actualizado!", text: res.message,
                                  icon: "success", timer: 1500, showConfirmButton: false });
                bootstrap.Modal.getInstance(document.getElementById("modalEstructura")).hide();
                dt.ajax.reload(null, false);
            } else {
                Swal.fire("Error", res.message || "Error al actualizar", "error");
            }
        } catch (e) { Swal.fire("Error", e.message, "error"); }
    });
}

// ============================================================
// ELIMINAR
// ============================================================
function fn_eliminar_estructura(id) {
    Swal.fire({
        title: '¿Eliminar estructura?',
        text : "Esta acción no se puede deshacer.",
        icon : 'warning',
        showCancelButton     : true,
        confirmButtonColor   : '#A32D2D',
        cancelButtonColor    : '#6b7280',
        confirmButtonText    : 'Sí, eliminar',
        cancelButtonText     : 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const res = await fnAjax("ELIMINAR", { id });
                if (res.success) {
                    Swal.fire({ title: "Eliminado", text: res.message,
                                icon: "success", timer: 1200, showConfirmButton: false });
                    dt.ajax.reload(null, false);
                } else {
                    Swal.fire("No se puede eliminar", res.message, "warning");
                }
            } catch (e) { Swal.fire("Error", e.message, "error"); }
        }
    });
}

// ============================================================
// BUILD FORM HTML
// ============================================================
function buildFormHTML(titulo, badge, btnId, btnLabel) {
    return `
    <div class="modal-header">
        <span class="modal-title"><i class="fas fa-layer-group me-2"></i>${titulo}</span>
        <div style="display:flex;align-items:center;gap:10px">
            <span class="est-modal-badge">${badge}</span>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
    </div>
    <div class="modal-body">
        <p style="font-size:12px;color:#9ca3af;margin-bottom:18px">
            Los campos con <span style="color:#E24B4A;font-weight:600">*</span> son obligatorios.
        </p>

        <input type="hidden" id="estructura_id">

        <!-- Nombre + Tipo -->
        <div class="est-form-row est-mb-3">
            <div>
                <label class="est-form-label">Nombre <span class="req">*</span></label>
                <input type="text" class="est-form-control" id="inputNombre"
                       placeholder="Ej: Andamio A1">
                <div class="est-field-error" id="error-nombre"></div>
            </div>
            <div>
                <label class="est-form-label">Tipo <span class="req">*</span></label>
                <select class="est-form-control" id="selectTipo">
                    <option value="">Seleccione un tipo</option>
                    <option value="ANDAMIO">ANDAMIO</option>
                    <option value="ESTANTE">ESTANTE</option>
                    <option value="OTRO">OTRO</option>
                </select>
                <div class="est-field-error" id="error-tipo"></div>
            </div>
        </div>

        <!-- Locación + Referencia -->
        <div class="est-form-row est-mb-3">
            <div>
                <label class="est-form-label">Locación <span class="req">*</span></label>
                <select class="est-form-control" id="selectLocacion">
                    <option value="">Cargando...</option>
                </select>
                <div class="est-field-error" id="error-locacion"></div>
            </div>
            <div>
                <label class="est-form-label">Referencia / Código</label>
                <input type="text" class="est-form-control" id="inputCodigo"
                       placeholder="Ej: AND-001">
                <div class="est-form-hint">Identificador interno opcional</div>
            </div>
        </div>

        <!-- Posición -->
        <div class="est-mb-3">
            <label class="est-form-label">Posición</label>
            <input type="number" class="est-form-control" id="inputPosicion"
                   placeholder="Ej: 1, 2, 3…" min="0" step="1"
                   style="max-width:220px">
            <div class="est-form-hint">Número de posición o nivel dentro de la locación</div>
        </div>
    </div>
    <div class="modal-footer" style="gap:10px">
        <button type="button" class="est-btn-cancel" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="est-btn-save" id="${btnId}">${btnLabel}</button>
    </div>`;
}

// ============================================================
// CARGAR LOCACIONES
// FIX ⑥ — el callback se ejecuta DESPUÉS de poblar el select,
//          garantizando que los valores se asignen cuando el DOM está listo
// ============================================================
function cargarLocaciones(callback) {
    fnAjax("LISTAR_LOCACIONES", {}).then(function (res) {
        const sel = document.getElementById("selectLocacion");
        if (!sel) return;

        sel.innerHTML = '<option value="">Seleccione una locación</option>';

        if (res.success && Array.isArray(res.data) && res.data.length > 0) {
            res.data.forEach(loc => {
                const opt = document.createElement('option');
                opt.value       = loc.id;
                opt.textContent = `[${loc.tipo}] ${loc.nombre}`;
                sel.appendChild(opt);
            });
        } else {
            sel.innerHTML = '<option value="">Sin locaciones disponibles</option>';
        }

        if (typeof callback === 'function') callback();  // ← callback DESPUÉS de poblar
    }).catch(function (e) {
        console.error("Error cargando locaciones:", e);
        const sel = document.getElementById("selectLocacion");
        if (sel) sel.innerHTML = '<option value="">Error al cargar locaciones</option>';
        if (typeof callback === 'function') callback();
    });
}

// ============================================================
// VALIDAR CAMPOS
// ============================================================
function validarCampos() {
    let valid = true;

    const iNombre = document.getElementById("inputNombre");
    const eNombre = document.getElementById("error-nombre");
    if (!iNombre.value.trim()) {
        valid = false;
        iNombre.style.borderColor = '#E24B4A';
        eNombre.textContent = "El nombre es obligatorio.";
        eNombre.style.display = 'block';
    } else {
        iNombre.style.borderColor = '';
        eNombre.style.display = 'none';
    }

    const sTipo = document.getElementById("selectTipo");
    const eTipo = document.getElementById("error-tipo");
    if (!sTipo.value) {
        valid = false;
        sTipo.style.borderColor = '#E24B4A';
        eTipo.textContent = "Selecciona un tipo.";
        eTipo.style.display = 'block';
    } else {
        sTipo.style.borderColor = '';
        eTipo.style.display = 'none';
    }

    const sLoc = document.getElementById("selectLocacion");
    const eLoc = document.getElementById("error-locacion");
    if (!sLoc.value) {
        valid = false;
        sLoc.style.borderColor = '#E24B4A';
        eLoc.textContent = "Selecciona una locación.";
        eLoc.style.display = 'block';
    } else {
        sLoc.style.borderColor = '';
        eLoc.style.display = 'none';
    }

    return valid;
}

// ============================================================
// RECOGER DATOS
// Mapeo: inputCodigo  → codigo  (PHP lo guarda en referencia)
//        inputPosicion → capacidad (PHP lo guarda en posicion)
// ============================================================
function recogerDatos() {
    const posVal = document.getElementById("inputPosicion").value;
    return {
        nombre      : document.getElementById("inputNombre").value.trim(),
        codigo      : document.getElementById("inputCodigo").value.trim() || null,
        tipo        : document.getElementById("selectTipo").value,
        locacion_id : document.getElementById("selectLocacion").value,
        capacidad   : posVal !== '' ? posVal : null
    };
}

// ============================================================
// AJAX HELPER
// ============================================================
function fnAjax(accion, extraData) {
    return new Promise((resolve, reject) => {
        $.ajax({
            method: "POST",
            url   : URL_LOGICA,
            data  : Object.assign({ accion, sucursal_id: SUCURSAL_ID }, extraData)
        })
        .done(response => {
            try {
                resolve(typeof response === "string" ? JSON.parse(response) : response);
            } catch (e) {
                reject(new Error("Respuesta inválida del servidor: " + response));
            }
        })
        .fail(xhr => {
            reject(new Error("Error de conexión: " + xhr.status + " " + xhr.statusText));
        });
    });
}
</script>

<?php include("pie.php"); ?>