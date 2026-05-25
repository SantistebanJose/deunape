<?php
include("cabecera.php");
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=DM+Mono:wght@400;500&display=swap');

* { box-sizing: border-box; }

:root {
    --brand:      #1a2346;
    --brand-light:#2a3a6e;
    --accent:     #3d6bff;
    --radius:     10px;
    --radius-lg:  14px;
}

body, .page-inner { font-family: 'DM Sans', sans-serif !important; }

/* ── Layout ── */
.lc-page { display: flex; flex-direction: column; gap: 20px; padding-bottom: 48px; }

/* ── Topbar ── */
.lc-topbar {
    display: flex; align-items: center; justify-content: space-between; padding: 8px 0 0;
}
.lc-topbar-left h2 {
    font-size: 20px; font-weight: 600; letter-spacing: -.3px;
    color: var(--brand); margin: 0;
}
.lc-topbar-left p { font-size: 13px; color: #6b7280; margin: 3px 0 0; }

.btn-new-loc {
    display: inline-flex; align-items: center; gap: 7px;
    background: var(--brand); color: #fff; border: none;
    border-radius: var(--radius); padding: 10px 18px;
    font-size: 13px; font-weight: 500; cursor: pointer;
    font-family: 'DM Sans', sans-serif; transition: background .15s;
}
.btn-new-loc:hover { background: var(--brand-light); }

/* ── Stats ── */
.lc-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
.lc-stat {
    background: #f8f9fb; border-radius: var(--radius);
    padding: 14px 16px; border: 0.5px solid #e5e7eb;
}
.lc-stat-label {
    font-size: 11px; color: #6b7280; font-weight: 600;
    text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px;
}
.lc-stat-val { font-size: 22px; font-weight: 600; letter-spacing: -.5px; }
.lc-stat-val.navy  { color: var(--brand); }
.lc-stat-val.green { color: #3B6D11; }
.lc-stat-val.blue  { color: #185FA5; }
.lc-stat-val.amber { color: #854F0B; }

/* ── Filters ── */
.lc-filters {
    display: flex; gap: 8px; align-items: center; flex-wrap: wrap;
}
.lc-search {
    padding: 8px 12px; border: 0.5px solid #d1d5db; border-radius: var(--radius);
    font-size: 13px; font-family: 'DM Sans', sans-serif; outline: none;
    background: #f8f9fb; color: #111827; width: 220px; transition: border-color .15s;
}
.lc-search:focus { border-color: var(--accent); background: #fff; }
.lc-select {
    padding: 8px 12px; border: 0.5px solid #d1d5db; border-radius: var(--radius);
    font-size: 13px; font-family: 'DM Sans', sans-serif; outline: none;
    background: #f8f9fb; color: #111827; cursor: pointer; transition: border-color .15s;
}
.lc-select:focus { border-color: var(--accent); }
.lc-filter-count { font-size: 13px; color: #6b7280; margin-left: auto; }

/* ── Cards grid ── */
.lc-grid { display: flex; flex-direction: column; gap: 14px; }

/* ── Location Card ── */
.lc-card {
    background: #fff; border: 0.5px solid #e5e7eb;
    border-radius: var(--radius-lg); overflow: hidden;
    transition: border-color .15s;
}
.lc-card:hover { border-color: #d1d5db; }

/* card header — clickable */
.lc-card-hdr {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 16px; cursor: pointer; user-select: none;
}
.lc-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.icon-ALMACEN    { background: #eaf3de; }
.icon-SUCURSAL   { background: #e6f1fb; }
.icon-PUNTO_VENTA{ background: #faeeda; }

.lc-info { flex: 1; min-width: 0; }
.lc-name {
    font-size: 14px; font-weight: 500; color: #111827;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.lc-sub { font-size: 11px; color: #9ca3af; margin-top: 2px; }

.lc-meta { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }

/* tipo badge */
.lc-tipo-badge {
    display: inline-block; padding: 2px 8px; border-radius: 6px;
    font-size: 10px; font-weight: 600; letter-spacing: .3px;
}
.lc-tipo-ALMACEN    { background: #eaf3de; color: #3B6D11; }
.lc-tipo-SUCURSAL   { background: #e6f1fb; color: #185FA5; }
.lc-tipo-PUNTO_VENTA{ background: #faeeda; color: #854F0B; }

/* estado dot */
.est-dot {
    width: 7px; height: 7px; border-radius: 50%; display: inline-block; flex-shrink: 0;
}
.est-dot.on  { background: #3B6D11; }
.est-dot.off { background: #A32D2D; }

/* estructura count pill */
.est-count-pill {
    background: #f3f4f6; border: 0.5px solid #e5e7eb;
    border-radius: 20px; padding: 2px 9px;
    font-size: 11px; color: #6b7280; font-weight: 500; white-space: nowrap;
}

/* action buttons */
.lc-actions { display: flex; gap: 5px; flex-shrink: 0; }
.lc-ibtn {
    width: 28px; height: 28px; border-radius: 7px; border: 0.5px solid #e5e7eb;
    background: #fff; cursor: pointer; display: flex; align-items: center;
    justify-content: center; font-size: 12px; transition: all .15s; color: #6b7280;
}
.lc-ibtn-edit:hover { background: #e6f1fb; border-color: #185FA5; color: #185FA5; }
.lc-ibtn-del:hover  { background: #fcebeb; border-color: #A32D2D; color: #A32D2D; }

/* chevron */
.lc-chevron {
    font-size: 10px; color: #9ca3af; transition: transform .22s;
    flex-shrink: 0; margin-left: 4px;
}
.lc-chevron.open { transform: rotate(180deg); }

/* ── Card body (collapsible) ── */
.lc-card-body { display: none; border-top: 0.5px solid #f3f4f6; }
.lc-card-body.open { display: block; }

/* detail strip */
.lc-detail-strip {
    display: flex; gap: 20px; padding: 10px 16px;
    border-bottom: 0.5px solid #f3f4f6; background: #fafafa; flex-wrap: wrap;
}
.lc-detail-item { font-size: 12px; color: #6b7280; }
.lc-detail-item strong { color: #374151; font-weight: 500; }

/* estructuras section */
.lc-est-section { padding: 14px 16px; }
.lc-est-hdr {
    display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;
}
.lc-est-title {
    font-size: 11px; font-weight: 600; text-transform: uppercase;
    letter-spacing: .5px; color: #6b7280;
}

.btn-add-est {
    display: inline-flex; align-items: center; gap: 5px;
    background: var(--brand); color: #fff; border: none;
    border-radius: 8px; padding: 5px 12px;
    font-size: 11px; font-weight: 500; cursor: pointer;
    font-family: 'DM Sans', sans-serif; transition: background .15s;
}
.btn-add-est:hover { background: var(--brand-light); }

/* mini table */
.est-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.est-table thead tr { background: #f8f9fb; }
.est-table th {
    padding: 8px 10px; font-size: 10px; font-weight: 600; text-transform: uppercase;
    letter-spacing: .4px; color: #6b7280; text-align: left;
    border-bottom: 0.5px solid #e5e7eb;
}
.est-table td {
    padding: 9px 10px; border-bottom: 0.5px solid #f3f4f6; vertical-align: middle;
}
.est-table tbody tr:last-child td { border-bottom: none; }
.est-table tbody tr:hover td { background: #fafafa; }

.est-tipo-badge {
    display: inline-block; padding: 2px 7px; border-radius: 5px;
    font-size: 10px; font-weight: 600; letter-spacing: .3px;
}
.est-tipo-ANDAMIO { background: #eaf3de; color: #3B6D11; }
.est-tipo-ESTANTE { background: #e6f1fb; color: #185FA5; }
.est-tipo-OTRO    { background: #f3f4f6; color: #6b7280; }

.lc-mono { font-family: 'DM Mono', monospace; font-size: 11px; color: #9ca3af; }

.est-empty {
    padding: 20px; text-align: center;
    color: #9ca3af; font-size: 12px; font-style: italic;
}

/* ── MODALES ── */
.lc-modal-content {
    border-radius: var(--radius-lg) !important;
    border: 0.5px solid #e5e7eb !important;
    box-shadow: 0 20px 60px rgba(26,35,70,.15) !important;
    font-family: 'DM Sans', sans-serif;
}
.lc-modal-header {
    background: #fff; border-bottom: 0.5px solid #f3f4f6;
    padding: 16px 20px; display: flex; align-items: center;
}
.lc-modal-title { font-size: 15px; font-weight: 600; color: var(--brand); }
.lc-modal-badge {
    background: #e6f1fb; color: #185FA5;
    font-size: 10px; padding: 2px 9px; border-radius: 20px;
    font-weight: 600; margin-left: 8px;
}
.lc-modal-body   { padding: 20px; background: #fff; }
.lc-modal-footer {
    background: #f8f9fb; border-top: 0.5px solid #f3f4f6; padding: 12px 20px;
}

/* form */
.lc-flbl {
    display: block; font-size: 10px; font-weight: 600; color: #6b7280;
    text-transform: uppercase; letter-spacing: .5px; margin-bottom: 5px;
}
.lc-flbl .req { color: #E24B4A; }
.lc-finp {
    width: 100%; padding: 8px 11px; border: 0.5px solid #d1d5db;
    border-radius: var(--radius); background: #f8f9fb; font-size: 13px;
    font-family: 'DM Sans', sans-serif; color: #111827;
    outline: none; transition: border-color .15s, background .15s;
}
.lc-finp:focus { border-color: var(--accent); background: #fff; }
.lc-fhint { font-size: 11px; color: #9ca3af; margin-top: 3px; }
.lc-frow  { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.lc-fmb   { margin-bottom: 14px; }
.lc-ferr  { color: #E24B4A; font-size: 11px; margin-top: 3px; display: none; }

/* loc-context strip inside est modal */
.lc-ctx-strip {
    background: #f8f9fb; border-radius: var(--radius);
    padding: 8px 12px; margin-bottom: 14px;
    font-size: 12px; color: #6b7280; border: 0.5px solid #e5e7eb;
}
.lc-ctx-strip strong { color: #111827; font-weight: 500; }

/* footer buttons */
.btn-modal-cancel {
    padding: 8px 16px; border: 0.5px solid #d1d5db; border-radius: var(--radius);
    background: #fff; font-size: 13px; font-weight: 500;
    font-family: 'DM Sans', sans-serif; color: #374151;
    cursor: pointer; transition: background .15s;
}
.btn-modal-cancel:hover { background: #f3f4f6; }
.btn-modal-save {
    padding: 8px 20px; border: none; border-radius: var(--radius);
    background: var(--brand); color: #fff; font-size: 13px; font-weight: 500;
    font-family: 'DM Sans', sans-serif; cursor: pointer; transition: background .15s;
}
.btn-modal-save:hover { background: var(--brand-light); }

/* empty state global */
.lc-empty-state {
    text-align: center; padding: 48px 24px;
    color: #9ca3af; font-size: 13px;
}
.lc-empty-state i { font-size: 32px; margin-bottom: 12px; display: block; color: #d1d5db; }
</style>

<div class="container">
<div class="page-inner">
<div class="lc-page">

    <!-- ── Topbar ── -->
    <div class="lc-topbar">
        <div class="lc-topbar-left">
            <h2>Locaciones y Estructuras</h2>
            <p>Registra cada locación y agrega sus andamios o estantes directamente</p>
        </div>
        <button class="btn-new-loc" id="btnNuevaLocacion">
            <i class="fas fa-plus" style="font-size:11px"></i> Nueva locación
        </button>
    </div>

    <!-- ── Stats ── -->
    <div class="lc-stats">
        <div class="lc-stat">
            <div class="lc-stat-label">Locaciones</div>
            <div class="lc-stat-val navy" id="stat-locaciones">—</div>
        </div>
        <div class="lc-stat">
            <div class="lc-stat-label">Estructuras totales</div>
            <div class="lc-stat-val blue" id="stat-estructuras">—</div>
        </div>
        <div class="lc-stat">
            <div class="lc-stat-label">Andamios</div>
            <div class="lc-stat-val green" id="stat-andamios">—</div>
        </div>
        <div class="lc-stat">
            <div class="lc-stat-label">Estantes</div>
            <div class="lc-stat-val amber" id="stat-estantes">—</div>
        </div>
    </div>

    <!-- ── Filters ── -->
    <div class="lc-filters">
        <input type="text" class="lc-search" id="searchLoc" placeholder="Buscar locación…">
        <select class="lc-select" id="filterTipo">
            <option value="">Todos los tipos</option>
            <option value="ALMACEN">Almacén</option>
            <option value="SUCURSAL">Sucursal</option>
            <option value="PUNTO_VENTA">Punto de venta</option>
        </select>
        <select class="lc-select" id="filterEstado">
            <option value="">Todos los estados</option>
            <option value="1">Activos</option>
            <option value="0">Inactivos</option>
        </select>
        <span class="lc-filter-count" id="filterCount"></span>
    </div>

    <!-- ── Cards ── -->
    <div class="lc-grid" id="lcGrid">
        <div class="lc-empty-state">
            <i class="fas fa-warehouse"></i>
            Cargando locaciones…
        </div>
    </div>

</div><!-- /.lc-page -->
</div>
</div>

<!-- ════════════════════ MODAL LOCACIÓN ════════════════════ -->
<div class="modal fade" id="modalLocacion" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content lc-modal-content">
            <div class="lc-modal-header modal-header">
                <span class="lc-modal-title" id="tituloModalLoc">Nueva Locación</span>
                <span class="lc-modal-badge" id="badgeModalLoc">Nuevo</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="lc-modal-body modal-body">
                <p style="font-size:12px;color:#9ca3af;margin-bottom:16px">
                    Los campos con <span style="color:#E24B4A;font-weight:600">*</span> son obligatorios.
                </p>
                <input type="hidden" id="loc_id">

                <div class="lc-frow lc-fmb">
                    <div>
                        <label class="lc-flbl">Nombre <span class="req">*</span></label>
                        <input type="text" class="lc-finp" id="loc_nombre" placeholder="Ej: Almacén Central Lima">
                        <div class="lc-ferr" id="err_loc_nombre">El nombre es obligatorio.</div>
                    </div>
                    <div>
                        <label class="lc-flbl">Tipo <span class="req">*</span></label>
                        <select class="lc-finp" id="loc_tipo">
                            <option value="">Seleccione...</option>
                            <option value="ALMACEN">Almacén</option>
                            <option value="SUCURSAL">Sucursal</option>
                            <option value="PUNTO_VENTA">Punto de venta</option>
                        </select>
                        <div class="lc-ferr" id="err_loc_tipo">Selecciona un tipo.</div>
                    </div>
                </div>

                <div class="lc-fmb">
                    <label class="lc-flbl">Dirección</label>
                    <input type="text" class="lc-finp" id="loc_direccion" placeholder="Ej: Av. Argentina 4200, Lima">
                </div>

                <div class="lc-frow lc-fmb">
                    <div>
                        <label class="lc-flbl">Descripción</label>
                        <input type="text" class="lc-finp" id="loc_descripcion" placeholder="Descripción breve">
                    </div>
                    <div>
                        <label class="lc-flbl">Estado</label>
                        <select class="lc-finp" id="loc_estado">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="lc-modal-footer modal-footer" style="gap:8px">
                <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn-modal-save" id="btnGuardarLoc">Guardar locación</button>
            </div>
        </div>
    </div>
</div>

<!-- ════════════════════ MODAL ESTRUCTURA ════════════════════ -->
<div class="modal fade" id="modalEstructura" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content lc-modal-content">
            <div class="lc-modal-header modal-header">
                <span class="lc-modal-title" id="tituloModalEst">Nueva Estructura</span>
                <span class="lc-modal-badge" id="badgeModalEst">Nuevo</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="lc-modal-body modal-body">
                <input type="hidden" id="est_id">
                <input type="hidden" id="est_locacion_id">

                <!-- contexto de locación -->
                <div class="lc-ctx-strip" id="ctxLocacion">
                    Locación: <strong id="ctxLocNombre">—</strong>
                </div>

                <div class="lc-frow lc-fmb">
                    <div>
                        <label class="lc-flbl">Nombre <span class="req">*</span></label>
                        <input type="text" class="lc-finp" id="est_nombre" placeholder="Ej: Andamio A1">
                        <div class="lc-ferr" id="err_est_nombre">El nombre es obligatorio.</div>
                    </div>
                    <div>
                        <label class="lc-flbl">Tipo <span class="req">*</span></label>
                        <select class="lc-finp" id="est_tipo">
                            <option value="">Seleccione...</option>
                            <option value="ANDAMIO">Andamio</option>
                            <option value="ESTANTE">Estante</option>
                            <option value="OTRO">Otro</option>
                        </select>
                        <div class="lc-ferr" id="err_est_tipo">Selecciona un tipo.</div>
                    </div>
                </div>

                <div class="lc-frow">
                    <div>
                        <label class="lc-flbl">Referencia / Código</label>
                        <input type="text" class="lc-finp" id="est_codigo" placeholder="Ej: AND-001">
                        <div class="lc-fhint">Identificador interno opcional</div>
                    </div>
                    <div>
                        <label class="lc-flbl">Posición</label>
                        <input type="number" class="lc-finp" id="est_posicion"
                               placeholder="Ej: 1" min="0" step="1">
                        <div class="lc-fhint">Nivel dentro de la locación</div>
                    </div>
                </div>
            </div>
            <div class="lc-modal-footer modal-footer" style="gap:8px">
                <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn-modal-save" id="btnGuardarEst">Guardar estructura</button>
            </div>
        </div>
    </div>
</div>

<!-- deps -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/scriptNotify.js"></script>

<script>
const SUCURSAL_ID = "<?php echo $_SESSION['sucursal_id'] ?? ''; ?>";
const URL_LOGICA  = "logica/clssLocaciones.php";

/* ── estado local ── */
let DATA_LOCACIONES = [];   // [{...loc, estructuras:[...]}]
let expandedIds     = new Set();

const TIPO_ICON = {
    ALMACEN:     '🏭',
    SUCURSAL:    '🏢',
    PUNTO_VENTA: '🛒',
};

/* ══════════════════════════════════════════════════════════
   CARGA INICIAL
══════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    cargarTodo();

    document.getElementById('searchLoc') .addEventListener('input',  renderCards);
    document.getElementById('filterTipo') .addEventListener('change', renderCards);
    document.getElementById('filterEstado').addEventListener('change', renderCards);
    document.getElementById('btnNuevaLocacion').addEventListener('click', () => abrirModalLoc());
    document.getElementById('btnGuardarLoc')  .addEventListener('click', guardarLoc);
    document.getElementById('btnGuardarEst')  .addEventListener('click', guardarEst);
});

async function cargarTodo() {
    try {
        const res = await fnAjax('LISTAR_TODO', {});
        if (res.success) {
            DATA_LOCACIONES = res.data;
            actualizarStats(res.stats);
            renderCards();
        } else {
            mostrarError('No se pudieron cargar las locaciones.');
        }
    } catch(e) {
        mostrarError(e.message);
    }
}

/* ══════════════════════════════════════════════════════════
   STATS
══════════════════════════════════════════════════════════ */
function actualizarStats(s) {
    document.getElementById('stat-locaciones') .textContent = s?.locaciones  ?? '—';
    document.getElementById('stat-estructuras').textContent = s?.estructuras ?? '—';
    document.getElementById('stat-andamios')   .textContent = s?.andamios    ?? '—';
    document.getElementById('stat-estantes')   .textContent = s?.estantes    ?? '—';
}

/* ══════════════════════════════════════════════════════════
   RENDER CARDS
══════════════════════════════════════════════════════════ */
function renderCards() {
    const q    = document.getElementById('searchLoc').value.toLowerCase();
    const ft   = document.getElementById('filterTipo').value;
    const fe   = document.getElementById('filterEstado').value;

    const filtradas = DATA_LOCACIONES.filter(l => {
        if (ft && l.tipo !== ft) return false;
        if (fe !== '' && String(l.estado ? 1 : 0) !== fe) return false;
        if (q) {
            const haystack = [l.nombre, l.tipo, l.direccion, l.descripcion]
                             .join(' ').toLowerCase();
            if (!haystack.includes(q)) return false;
        }
        return true;
    });

    document.getElementById('filterCount').textContent =
        `${filtradas.length} locación${filtradas.length !== 1 ? 'es' : ''}`;

    const grid = document.getElementById('lcGrid');

    if (!filtradas.length) {
        grid.innerHTML = `
            <div class="lc-empty-state">
                <i class="fas fa-warehouse"></i>
                No se encontraron locaciones con esos filtros.
            </div>`;
        return;
    }

    grid.innerHTML = filtradas.map(l => buildCard(l)).join('');
}

/* ── construir HTML de una card ── */
function buildCard(l) {
    const ests   = l.estructuras ?? [];
    const isOpen = expandedIds.has(l.id);
    const icono  = TIPO_ICON[l.tipo] ?? '📦';

    const tipoLabel = { ALMACEN:'ALMACÉN', SUCURSAL:'SUCURSAL', PUNTO_VENTA:'PTO. VENTA' }[l.tipo] ?? l.tipo;

    const estRows = ests.length
        ? `<table class="est-table">
            <thead><tr>
                <th style="width:36px">ID</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Posición</th>
                <th>Referencia</th>
                <th style="width:68px"></th>
            </tr></thead>
            <tbody>
            ${ests.map(e => `
                <tr>
                    <td><span class="lc-mono">#${e.id}</span></td>
                    <td style="font-size:12px;font-weight:500;color:#111827">${e.nombre}</td>
                    <td><span class="est-tipo-badge est-tipo-${e.tipo}">${e.tipo}</span></td>
                    <td><span class="lc-mono">${e.posicion ?? '—'}</span></td>
                    <td><span class="lc-mono" style="font-size:11px">${e.referencia ?? '—'}</span></td>
                    <td>
                        <div class="lc-actions">
                            <button class="lc-ibtn lc-ibtn-edit" title="Editar estructura"
                                onclick="abrirModalEst(${l.id}, ${e.id})">
                                <i class="fas fa-pen"></i>
                            </button>
                            <button class="lc-ibtn lc-ibtn-del" title="Eliminar estructura"
                                onclick="eliminarEst(${e.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>`).join('')}
            </tbody>
           </table>`
        : `<div class="est-empty">
               Sin estructuras — agrega la primera con el botón de arriba.
           </div>`;

    return `
    <div class="lc-card" id="card-${l.id}">
        <!-- header clickable -->
        <div class="lc-card-hdr" onclick="toggleCard(${l.id})">
            <div class="lc-icon icon-${l.tipo}">${icono}</div>
            <div class="lc-info">
                <div class="lc-name">${l.nombre}</div>
                <div class="lc-sub">${l.direccion || l.descripcion || '—'}</div>
            </div>
            <div class="lc-meta">
                <span class="lc-tipo-badge lc-tipo-${l.tipo}">${tipoLabel}</span>
                <span class="est-dot ${l.estado ? 'on' : 'off'}"></span>
                <span class="est-count-pill">
                    ${ests.length} estructura${ests.length !== 1 ? 's' : ''}
                </span>
            </div>
            <div class="lc-actions" onclick="event.stopPropagation()">
                <button class="lc-ibtn lc-ibtn-edit" title="Editar locación"
                    onclick="abrirModalLoc(${l.id})">
                    <i class="fas fa-pen"></i>
                </button>
                <button class="lc-ibtn lc-ibtn-del" title="Eliminar locación"
                    onclick="eliminarLoc(${l.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <span class="lc-chevron ${isOpen ? 'open' : ''}">▼</span>
        </div>

        <!-- body colapsable -->
        <div class="lc-card-body ${isOpen ? 'open' : ''}" id="body-${l.id}">
            <div class="lc-detail-strip">
                <div class="lc-detail-item">
                    Tipo: <strong>${tipoLabel}</strong>
                </div>
                ${l.descripcion
                    ? `<div class="lc-detail-item">Desc: <strong>${l.descripcion}</strong></div>`
                    : ''}
                <div class="lc-detail-item">
                    Estado: <strong>${l.estado ? 'Activo' : 'Inactivo'}</strong>
                </div>
            </div>
            <div class="lc-est-section">
                <div class="lc-est-hdr">
                    <span class="lc-est-title">
                        Estructuras (${ests.length})
                    </span>
                    <button class="btn-add-est" onclick="abrirModalEst(${l.id}, null)">
                        <i class="fas fa-plus" style="font-size:10px"></i> Agregar estructura
                    </button>
                </div>
                ${estRows}
            </div>
        </div>
    </div>`;
}

/* ── toggle expand ── */
function toggleCard(id) {
    if (expandedIds.has(id)) expandedIds.delete(id);
    else expandedIds.add(id);
    renderCards();
}

/* ══════════════════════════════════════════════════════════
   MODAL LOCACIÓN
══════════════════════════════════════════════════════════ */
function abrirModalLoc(id = null) {
    const loc = id ? DATA_LOCACIONES.find(l => l.id === id) : null;
    const editing = !!loc;

    document.getElementById('tituloModalLoc').textContent = editing ? 'Editar Locación' : 'Nueva Locación';
    document.getElementById('badgeModalLoc') .textContent = editing ? 'Editando' : 'Nuevo';
    document.getElementById('loc_id')        .value = loc?.id || '';
    document.getElementById('loc_nombre')    .value = loc?.nombre || '';
    document.getElementById('loc_tipo')      .value = loc?.tipo || '';
    document.getElementById('loc_direccion') .value = loc?.direccion || '';
    document.getElementById('loc_descripcion').value = loc?.descripcion || '';
    document.getElementById('loc_estado')    .value = loc ? (loc.estado ? '1' : '0') : '1';

    // limpiar errores
    ['err_loc_nombre', 'err_loc_tipo'].forEach(id => {
        const el = document.getElementById(id);
        el.style.display = 'none';
        document.getElementById(id.replace('err_', '')).style.borderColor = '';
    });

    new bootstrap.Modal(document.getElementById('modalLocacion')).show();
}

async function guardarLoc() {
    const nombre = document.getElementById('loc_nombre').value.trim();
    const tipo   = document.getElementById('loc_tipo').value;

    let valid = true;

    const setErr = (errId, inputId, show) => {
        document.getElementById(errId).style.display = show ? 'block' : 'none';
        document.getElementById(inputId).style.borderColor = show ? '#E24B4A' : '';
        if (show) valid = false;
    };

    setErr('err_loc_nombre', 'loc_nombre', !nombre);
    setErr('err_loc_tipo',   'loc_tipo',   !tipo);

    if (!valid) return;

    const id = document.getElementById('loc_id').value;
    const accion = id ? 'ACTUALIZAR_LOC' : 'REGISTRAR_LOC';

    const payload = {
        data: JSON.stringify({
            id,
            nombre,
            tipo,
            direccion:   document.getElementById('loc_direccion').value.trim() || null,
            descripcion: document.getElementById('loc_descripcion').value.trim() || null,
            estado:      document.getElementById('loc_estado').value === '1',
        })
    };

    try {
        const res = await fnAjax(accion, payload);
        if (res.success) {
            await Swal.fire({
                title: id ? '¡Actualizado!' : '¡Registrado!',
                text: res.message, icon: 'success',
                timer: 1500, showConfirmButton: false
            });
            bootstrap.Modal.getInstance(document.getElementById('modalLocacion')).hide();

            // si es nueva locación, expandirla automáticamente
            if (!id && res.id) expandedIds.add(res.id);

            await cargarTodo();
        } else {
            Swal.fire('Aviso', res.message || 'Error al guardar.', 'warning');
        }
    } catch(e) { Swal.fire('Error', e.message, 'error'); }
}

async function eliminarLoc(id) {
    const loc = DATA_LOCACIONES.find(l => l.id === id);
    const result = await Swal.fire({
        title: '¿Eliminar locación?',
        text: `"${loc?.nombre}" y su configuración serán eliminadas.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#A32D2D',
        cancelButtonColor:  '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText:  'Cancelar',
    });

    if (!result.isConfirmed) return;

    try {
        const res = await fnAjax('ELIMINAR_LOC', { id });
        if (res.success) {
            Swal.fire({ title: 'Eliminada', text: res.message,
                        icon: 'success', timer: 1300, showConfirmButton: false });
            expandedIds.delete(id);
            await cargarTodo();
        } else {
            Swal.fire('No se puede eliminar', res.message, 'warning');
        }
    } catch(e) { Swal.fire('Error', e.message, 'error'); }
}

/* ══════════════════════════════════════════════════════════
   MODAL ESTRUCTURA
══════════════════════════════════════════════════════════ */
function abrirModalEst(locId, estId = null) {
    const loc = DATA_LOCACIONES.find(l => l.id === locId);
    const est = estId ? (loc?.estructuras ?? []).find(e => e.id === estId) : null;
    const editing = !!est;

    document.getElementById('tituloModalEst').textContent = editing ? 'Editar Estructura' : 'Nueva Estructura';
    document.getElementById('badgeModalEst') .textContent = editing ? 'Editando' : 'Nuevo';
    document.getElementById('est_id')        .value = est?.id || '';
    document.getElementById('est_locacion_id').value = locId;
    document.getElementById('ctxLocNombre')  .textContent = loc?.nombre || '—';
    document.getElementById('est_nombre')    .value = est?.nombre || '';
    document.getElementById('est_tipo')      .value = est?.tipo || '';
    document.getElementById('est_codigo')    .value = est?.referencia || '';
    document.getElementById('est_posicion')  .value = est?.posicion ?? '';

    // limpiar errores
    ['err_est_nombre', 'err_est_tipo'].forEach(id => {
        document.getElementById(id).style.display = 'none';
        document.getElementById(id.replace('err_', '')).style.borderColor = '';
    });

    new bootstrap.Modal(document.getElementById('modalEstructura')).show();
}

async function guardarEst() {
    const nombre = document.getElementById('est_nombre').value.trim();
    const tipo   = document.getElementById('est_tipo').value;

    let valid = true;
    const setErr = (errId, inputId, show) => {
        document.getElementById(errId).style.display = show ? 'block' : 'none';
        document.getElementById(inputId).style.borderColor = show ? '#E24B4A' : '';
        if (show) valid = false;
    };
    setErr('err_est_nombre', 'est_nombre', !nombre);
    setErr('err_est_tipo',   'est_tipo',   !tipo);
    if (!valid) return;

    const id      = document.getElementById('est_id').value;
    const locId   = document.getElementById('est_locacion_id').value;
    const posVal  = document.getElementById('est_posicion').value;
    const accion  = id ? 'ACTUALIZAR_EST' : 'REGISTRAR_EST';

    const payload = {
        data: JSON.stringify({
            id,
            locacion_id: locId,
            nombre,
            tipo,
            referencia: document.getElementById('est_codigo').value.trim() || null,
            posicion:   posVal !== '' ? parseInt(posVal) : null,
        })
    };

    try {
        const res = await fnAjax(accion, payload);
        if (res.success) {
            await Swal.fire({
                title: id ? '¡Actualizado!' : '¡Registrado!',
                text: res.message, icon: 'success',
                timer: 1500, showConfirmButton: false
            });
            bootstrap.Modal.getInstance(document.getElementById('modalEstructura')).hide();

            // mantener la card abierta
            expandedIds.add(parseInt(locId));
            await cargarTodo();
        } else {
            Swal.fire('Aviso', res.message || 'Error al guardar.', 'warning');
        }
    } catch(e) { Swal.fire('Error', e.message, 'error'); }
}

async function eliminarEst(id) {
    const result = await Swal.fire({
        title: '¿Eliminar estructura?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#A32D2D',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    });
    if (!result.isConfirmed) return;

    try {
        const res = await fnAjax('ELIMINAR_EST', { id });
        if (res.success) {
            Swal.fire({ title: 'Eliminada', text: res.message,
                        icon: 'success', timer: 1200, showConfirmButton: false });
            await cargarTodo();
        } else {
            Swal.fire('No se puede eliminar', res.message, 'warning');
        }
    } catch(e) { Swal.fire('Error', e.message, 'error'); }
}

/* ══════════════════════════════════════════════════════════
   UTILS
══════════════════════════════════════════════════════════ */
function fnAjax(accion, extraData) {
    return new Promise((resolve, reject) => {
        $.ajax({
            method: 'POST',
            url: URL_LOGICA,
            data: Object.assign({ accion, sucursal_id: SUCURSAL_ID }, extraData),
        })
        .done(response => {
            try {
                resolve(typeof response === 'string' ? JSON.parse(response) : response);
            } catch(e) {
                reject(new Error('Respuesta inválida del servidor: ' + response));
            }
        })
        .fail(xhr => {
            reject(new Error('Error de conexión: ' + xhr.status + ' ' + xhr.statusText));
        });
    });
}

function mostrarError(msg) {
    document.getElementById('lcGrid').innerHTML = `
        <div class="lc-empty-state">
            <i class="fas fa-exclamation-circle" style="color:#E24B4A"></i>
            ${msg}
        </div>`;
}
</script>

<?php include("pie.php"); ?>