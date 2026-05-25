<?php
include("cabecera.php");
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=DM+Mono:wght@400;500&display=swap');

* { box-sizing: border-box; }

:root {
    --brand:       #1a2346;
    --brand-light: #2a3a6e;
    --accent:      #3d6bff;
    --radius:      10px;
    --radius-lg:   14px;
}

body, .page-inner { font-family: 'DM Sans', sans-serif !important; }

/* ── Page ── */
.tr-page { display: flex; flex-direction: column; gap: 20px; padding-bottom: 48px; }

/* ── Header card ── */
.tr-hcard {
    background: #fff; border: 0.5px solid #e5e7eb;
    border-radius: var(--radius-lg); padding: 20px 22px;
}
.tr-hcard-top {
    display: flex; align-items: center;
    justify-content: space-between; margin-bottom: 14px;
}
.tr-hcard-top h2 {
    font-size: 20px; font-weight: 600;
    letter-spacing: -.3px; color: var(--brand); margin: 0;
}
.btn-nueva {
    display: inline-flex; align-items: center; gap: 7px;
    background: var(--brand); color: #fff; border: none;
    border-radius: var(--radius); padding: 10px 18px;
    font-size: 13px; font-weight: 500; cursor: pointer;
    font-family: 'DM Sans', sans-serif; transition: background .15s;
}
.btn-nueva:hover { background: var(--brand-light); }
.tr-info-strip {
    display: flex; align-items: center; gap: 8px;
    background: #e6f1fb; border-radius: 8px;
    padding: 10px 14px; font-size: 13px; color: #0C447C;
}

/* ── Stats ── */
.tr-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
.tr-stat {
    background: #f8f9fb; border: 0.5px solid #e5e7eb;
    border-radius: var(--radius); padding: 14px 16px;
}
.tr-stat-lbl {
    font-size: 11px; color: #6b7280; font-weight: 600;
    text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px;
}
.tr-stat-val { font-size: 22px; font-weight: 600; letter-spacing: -.5px; }
.c-navy  { color: var(--brand); }
.c-green { color: #3B6D11; }
.c-blue  { color: #185FA5; }

/* ── Historial ── */
.tr-hist-card {
    background: #fff; border: 0.5px solid #e5e7eb;
    border-radius: var(--radius-lg); overflow: hidden;
}
.tr-hist-hdr { padding: 16px 20px; border-bottom: 0.5px solid #f3f4f6; }
.tr-hist-hdr h3 { font-size: 15px; font-weight: 600; color: var(--brand); margin: 0; }

/* DataTables overrides */
#tbl-historial_wrapper .dataTables_filter input,
#tbl-historial_wrapper .dataTables_length select {
    border: 0.5px solid #d1d5db !important; border-radius: 8px !important;
    padding: 6px 10px !important; font-family: 'DM Sans', sans-serif !important;
    font-size: 13px !important; outline: none !important;
}
#tbl-historial_wrapper .dataTables_filter input:focus { border-color: var(--accent) !important; }
#tbl-historial_wrapper .dataTables_info,
#tbl-historial_wrapper .dataTables_length label,
#tbl-historial_wrapper .dataTables_filter label {
    font-size: 13px !important; font-family: 'DM Sans', sans-serif !important; color: #6b7280 !important;
}
#tbl-historial_wrapper .paginate_button {
    border-radius: 8px !important; font-family: 'DM Sans', sans-serif !important; font-size: 13px !important;
}
#tbl-historial_wrapper .paginate_button.current,
#tbl-historial_wrapper .paginate_button.current:hover {
    background: var(--brand) !important; border-color: var(--brand) !important; color: #fff !important;
}
#tbl-historial { font-family: 'DM Sans', sans-serif; font-size: 13px; }
.badge-cant {
    display: inline-block; padding: 3px 9px;
    background: #e6f1fb; color: #0C447C;
    border-radius: 20px; font-size: 12px; font-weight: 600;
    font-family: 'DM Mono', monospace;
}
.hist-ruta { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; font-size: 12px; }
.hist-ruta-loc { font-weight: 500; color: #111827; }
.hist-ruta-est { font-size: 11px; color: #9ca3af; font-family: 'DM Mono', monospace; }

/* ═══════════════════════════════════════
   MODAL
═══════════════════════════════════════ */
#modalTransferencia .modal-dialog { max-width: 650px; }
#modalTransferencia .modal-content {
    border-radius: var(--radius-lg) !important;
    border: 0.5px solid #e5e7eb !important;
    box-shadow: 0 24px 64px rgba(26,35,70,.18) !important;
    font-family: 'DM Sans', sans-serif; overflow: hidden;
}

/* header modal */
.tr-mhdr {
    display: flex; align-items: center; justify-content: space-between;
    padding: 17px 22px; border-bottom: 0.5px solid #f3f4f6; background: #fff;
}
.tr-mhdr-icon {
    width: 38px; height: 38px; border-radius: 10px;
    background: #e6f1fb; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.tr-mhdr-icon i { color: #185FA5; font-size: 15px; }
.tr-mhdr-title { font-size: 15px; font-weight: 600; color: var(--brand); margin-bottom: 1px; }
.tr-mhdr-sub   { font-size: 12px; color: #6b7280; }

/* stepper */
.tr-stepper {
    display: flex; align-items: center; padding: 13px 22px;
    border-bottom: 0.5px solid #f3f4f6; background: #fafafa;
}
.tr-step { display: flex; align-items: center; gap: 7px; flex: 1; }
.tr-snum {
    width: 26px; height: 26px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 600; flex-shrink: 0; transition: all .2s;
}
.tr-snum.active { background: var(--brand); color: #fff; }
.tr-snum.done   { background: #3B6D11; color: #eaf3de; }
.tr-snum.idle   { background: #fff; border: 0.5px solid #d1d5db; color: #9ca3af; }
.tr-slbl { font-size: 12px; font-weight: 500; color: #9ca3af; transition: color .2s; }
.tr-slbl.active { color: var(--brand); }
.tr-slbl.done   { color: #3B6D11; }
.tr-ssep { height: 1px; background: #e5e7eb; flex: 1; margin: 0 8px; }
.tr-ssep.done { background: #3B6D11; }

/* body */
.tr-mbody { padding: 22px; background: #fff; }
.tr-panel { display: none; }
.tr-panel.on { display: block; }

/* form */
.tr-flbl {
    display: block; font-size: 10px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .5px; color: #6b7280; margin-bottom: 5px;
}
.tr-flbl .req { color: #E24B4A; }
.tr-flbl .opt { font-weight: 400; color: #9ca3af; text-transform: none; letter-spacing: 0; }
.tr-finp {
    width: 100%; padding: 8px 11px; border: 0.5px solid #d1d5db;
    border-radius: var(--radius); background: #f8f9fb; font-size: 13px;
    font-family: 'DM Sans', sans-serif; color: #111827;
    outline: none; transition: border-color .15s, background .15s; appearance: none;
}
.tr-finp:focus { border-color: var(--accent); background: #fff; }
.tr-fmb  { margin-bottom: 13px; }
.tr-ferr { color: #E24B4A; font-size: 11px; margin-top: 4px; display: none; }
.tr-ferr.on { display: block; }
.tr-fhint { font-size: 11px; color: #9ca3af; margin-top: 3px; }

/* ── PASO 1: búsqueda ── */
.tr-art-results {
    border: 0.5px solid #e5e7eb; border-radius: var(--radius-lg);
    overflow: hidden; margin-top: 6px; display: none;
}
.tr-art-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 11px 14px; cursor: pointer;
    border-bottom: 0.5px solid #f3f4f6; transition: background .12s;
}
.tr-art-row:last-child { border-bottom: none; }
.tr-art-row:hover { background: #fafafa; }
.tr-art-row.selected { background: #e6f1fb; border-left: 3px solid #185FA5; }
.tr-art-name { font-size: 13px; font-weight: 500; color: #111827; }
.tr-art-code { font-size: 11px; color: #9ca3af; font-family: 'DM Mono', monospace; margin-top: 1px; }
.tr-art-qty  { font-size: 15px; font-weight: 600; color: var(--brand); text-align: right; }
.tr-art-qlbl { font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: .3px; }
.tr-art-msg  { padding: 18px; text-align: center; font-size: 13px; color: #9ca3af; font-style: italic; }

.tr-breakdown {
    border: 0.5px solid #e5e7eb; border-radius: var(--radius-lg);
    overflow: hidden; margin-top: 10px; display: none;
}
.tr-breakdown-hdr {
    padding: 8px 13px; background: #f8f9fb;
    font-size: 10px; font-weight: 600; text-transform: uppercase;
    letter-spacing: .4px; color: #6b7280; border-bottom: 0.5px solid #e5e7eb;
}
.tr-ubic-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 9px 13px; border-bottom: 0.5px solid #f3f4f6; font-size: 12px;
}
.tr-ubic-row:last-child { border-bottom: none; }
.tr-ubic-loc  { font-weight: 500; color: #111827; }
.tr-ubic-est  { font-size: 11px; color: #9ca3af; margin-top: 1px; font-family: 'DM Mono', monospace; }
.tr-ubic-qty  {
    font-family: 'DM Mono', monospace; font-size: 12px; font-weight: 600;
    color: #0C447C; background: #e6f1fb; padding: 2px 9px; border-radius: 20px;
}

/* ── PASO 2 ── */
.tr-art-ctx {
    background: #f8f9fb; border: 0.5px solid #e5e7eb;
    border-radius: var(--radius); padding: 11px 14px;
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px; margin-bottom: 16px;
}
.tr-art-ctx-lbl  { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; color: #6b7280; margin-bottom: 3px; }
.tr-art-ctx-name { font-size: 14px; font-weight: 500; color: #111827; }
.tr-art-ctx-stk  { font-size: 12px; color: #6b7280; margin-top: 2px; }
.tr-btn-change {
    font-size: 11px; color: #185FA5; background: #e6f1fb;
    border: none; border-radius: 7px; padding: 5px 11px;
    cursor: pointer; font-family: 'DM Sans', sans-serif; white-space: nowrap;
}
.tr-btn-change:hover { background: #B5D4F4; }

.tr-ubic-grid { display: grid; grid-template-columns: 1fr 38px 1fr; gap: 10px; align-items: start; }
.tr-ubic-card { border: 0.5px solid #e5e7eb; border-radius: var(--radius-lg); overflow: hidden; }
.tr-ubic-chdr { padding: 10px 14px; display: flex; align-items: center; gap: 7px; border-bottom: 0.5px solid #f3f4f6; }
.tr-ubic-chdr.origen  { background: #e6f1fb; }
.tr-ubic-chdr.destino { background: #eaf3de; }
.tr-ubic-clbl { font-size: 11px; font-weight: 700; letter-spacing: .4px; text-transform: uppercase; }
.tr-ubic-clbl.origen  { color: #0C447C; }
.tr-ubic-clbl.destino { color: #27500A; }
.tr-ubic-cbody { padding: 13px; }
.tr-ubic-arrow { display: flex; align-items: center; justify-content: center; padding-top: 48px; color: #9ca3af; }
.tr-ubic-arrow i { font-size: 18px; }

.tr-stock-badge {
    display: none; margin-top: 6px; font-size: 11px;
    background: #eaf3de; color: #27500A;
    border-radius: 20px; padding: 3px 10px; font-weight: 500;
}
.tr-stock-badge.on { display: inline-block; }

/* ── PASO 3 ── */
.tr-resumen-flow { display: grid; grid-template-columns: 1fr 40px 1fr; gap: 10px; align-items: center; margin-bottom: 14px; }
.tr-rbox { border: 0.5px solid #e5e7eb; border-radius: var(--radius-lg); padding: 13px; }
.tr-rbox.origen  { border-top: 3px solid #185FA5; border-radius: 0 0 var(--radius-lg) var(--radius-lg); }
.tr-rbox.destino { border-top: 3px solid #3B6D11; border-radius: 0 0 var(--radius-lg) var(--radius-lg); }
.tr-rtag { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 4px; }
.tr-rtag.origen  { color: #185FA5; }
.tr-rtag.destino { color: #3B6D11; }
.tr-rname { font-size: 13px; font-weight: 500; color: #111827; margin-bottom: 2px; }
.tr-rsub  { font-size: 11px; color: #9ca3af; }
.tr-rarrow {
    width: 34px; height: 34px; border-radius: 50%;
    background: #f3f4f6; border: 0.5px solid #e5e7eb;
    display: flex; align-items: center; justify-content: center; margin: 0 auto;
    color: #9ca3af; font-size: 13px;
}
.tr-art-mini {
    border: 0.5px solid #e5e7eb; border-radius: var(--radius-lg);
    padding: 13px; margin-bottom: 14px;
    display: flex; align-items: center; justify-content: space-between; gap: 14px;
}
.tr-bar-wrap   { flex: 1; max-width: 160px; }
.tr-bar-labels { display: flex; justify-content: space-between; font-size: 10px; color: #9ca3af; margin-bottom: 4px; }
.tr-bar-bg     { height: 6px; background: #f3f4f6; border-radius: 3px; overflow: hidden; }
.tr-bar-fill   { height: 100%; background: #3B6D11; border-radius: 3px; transition: width .3s, background .2s; }

.tr-cant-row   { display: grid; grid-template-columns: 170px 1fr; gap: 12px; align-items: start; }
.tr-cant-input { font-size: 26px !important; font-weight: 500 !important; padding: 10px 14px !important; text-align: center !important; }
.tr-cant-rem   { font-size: 12px; margin-top: 5px; }
.tr-cr-ok   { color: #3B6D11; }
.tr-cr-warn { color: #854F0B; }
.tr-cr-bad  { color: #A32D2D; }

/* nav footer */
.tr-mnav {
    display: flex; align-items: center; justify-content: space-between;
    padding: 13px 22px; border-top: 0.5px solid #f3f4f6; background: #fafafa;
}
.tr-btn-back {
    padding: 8px 16px; border: 0.5px solid #d1d5db; border-radius: var(--radius);
    background: #fff; font-size: 13px; font-weight: 500;
    font-family: 'DM Sans', sans-serif; color: #374151;
    cursor: pointer; transition: background .15s;
}
.tr-btn-back:hover:not(:disabled) { background: #f3f4f6; }
.tr-btn-back:disabled { opacity: .4; cursor: not-allowed; }
.tr-nav-hint { font-size: 12px; color: #9ca3af; }
.tr-btn-next {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 20px; border: none; border-radius: var(--radius);
    background: var(--brand); color: #fff; font-size: 13px; font-weight: 500;
    font-family: 'DM Sans', sans-serif; cursor: pointer; transition: background .15s;
}
.tr-btn-next:hover { background: var(--brand-light); }
.tr-btn-next.green { background: #3B6D11; }
.tr-btn-next.green:hover { background: #27500A; }
</style>

<div class="container">
<div class="page-inner">
<div class="tr-page">

    <div class="tr-hcard">
        <div class="tr-hcard-top">
            <h2><i class="fas fa-exchange-alt me-2" style="font-size:17px"></i>Transferencia de Stock</h2>
            <button class="btn-nueva" id="btnNuevaTransferencia">
                <i class="fas fa-plus" style="font-size:11px"></i> Nueva transferencia
            </button>
        </div>
        <div class="tr-info-strip">
            <i class="fas fa-info-circle"></i>
            Busca el artículo, revisa dónde está el stock, elige <strong>origen → destino</strong> e indica la <strong>cantidad</strong>.
        </div>
    </div>

    <div class="tr-stats">
        <div class="tr-stat"><div class="tr-stat-lbl">Transferencias hoy</div><div class="tr-stat-val c-navy" id="stat-hoy">—</div></div>
        <div class="tr-stat"><div class="tr-stat-lbl">Unidades movidas hoy</div><div class="tr-stat-val c-green" id="stat-unidades">—</div></div>
        <div class="tr-stat"><div class="tr-stat-lbl">Total histórico</div><div class="tr-stat-val c-blue" id="stat-total">—</div></div>
    </div>

    <div class="tr-hist-card">
        <div class="tr-hist-hdr"><h3>Historial de transferencias</h3></div>
        <div style="padding:14px">
            <table id="tbl-historial" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th><th>Artículo</th><th>Origen</th>
                        <th>Destino</th><th>Cantidad</th><th>Motivo</th><th>Fecha</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>
</div>
</div>

<!-- ════════════════ MODAL ════════════════ -->
<div class="modal fade" id="modalTransferencia" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="tr-mhdr">
                <div style="display:flex;align-items:center;gap:11px">
                    <div class="tr-mhdr-icon"><i class="fas fa-exchange-alt"></i></div>
                    <div>
                        <div class="tr-mhdr-title">Transferencia de stock</div>
                        <div class="tr-mhdr-sub" id="mhdr-sub">Paso 1 de 3 — Busca el artículo</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="tr-stepper">
                <div class="tr-step"><div class="tr-snum active" id="sn1">1</div><div class="tr-slbl active" id="sl1">Artículo</div></div>
                <div class="tr-ssep" id="sep12"></div>
                <div class="tr-step"><div class="tr-snum idle" id="sn2">2</div><div class="tr-slbl" id="sl2">Origen y destino</div></div>
                <div class="tr-ssep" id="sep23"></div>
                <div class="tr-step"><div class="tr-snum idle" id="sn3">3</div><div class="tr-slbl" id="sl3">Cantidad</div></div>
            </div>

            <div class="tr-mbody">

                <!-- ══ PASO 1 ══ -->
                <div class="tr-panel on" id="panel1">
                    <div class="tr-fmb">
                        <label class="tr-flbl">Buscar artículo <span class="req">*</span></label>
                        <input type="text" class="tr-finp" id="art-search"
                               placeholder="Escribe nombre o código..." autocomplete="off">
                        <div class="tr-ferr" id="e-art">Selecciona un artículo de la lista.</div>
                    </div>
                    <div class="tr-art-results" id="art-results"></div>
                    <div class="tr-breakdown"   id="art-breakdown">
                        <div class="tr-breakdown-hdr">Distribución de stock en la sucursal</div>
                        <div id="breakdown-rows"></div>
                    </div>
                    <div class="tr-fhint" id="art-hint" style="display:none;margin-top:10px">
                        Haz clic en <strong>Siguiente</strong> para continuar con este artículo.
                    </div>
                </div>

                <!-- ══ PASO 2 ══ -->
                <div class="tr-panel" id="panel2">
                    <div class="tr-art-ctx">
                        <div>
                            <div class="tr-art-ctx-lbl">Artículo seleccionado</div>
                            <div class="tr-art-ctx-name" id="ctx-art-name">—</div>
                            <div class="tr-art-ctx-stk"  id="ctx-art-stock">—</div>
                        </div>
                        <button class="tr-btn-change" onclick="goToStep(1)">← Cambiar artículo</button>
                    </div>

                    <div class="tr-ubic-grid">
                        <div class="tr-ubic-card">
                            <div class="tr-ubic-chdr origen">
                                <i class="fas fa-map-marker-alt" style="color:#185FA5;font-size:12px"></i>
                                <span class="tr-ubic-clbl origen">Origen</span>
                            </div>
                            <div class="tr-ubic-cbody">
                                <div class="tr-fmb">
                                    <label class="tr-flbl">Locación <span class="req">*</span></label>
                                    <select class="tr-finp" id="s2-loc-o">
                                        <option value="">Seleccione...</option>
                                    </select>
                                    <div class="tr-ferr" id="e-loc-o">Selecciona la locación de origen.</div>
                                </div>
                                <div>
                                    <label class="tr-flbl">Estructura <span class="opt">(opcional)</span></label>
                                    <select class="tr-finp" id="s2-est-o"></select>
                                </div>
                                <div class="tr-stock-badge" id="origen-badge">Stock disponible: —</div>
                            </div>
                        </div>

                        <div class="tr-ubic-arrow"><i class="fas fa-arrow-right"></i></div>

                        <div class="tr-ubic-card">
                            <div class="tr-ubic-chdr destino">
                                <i class="fas fa-map-pin" style="color:#27500A;font-size:12px"></i>
                                <span class="tr-ubic-clbl destino">Destino</span>
                            </div>
                            <div class="tr-ubic-cbody">
                                <div class="tr-fmb">
                                    <label class="tr-flbl">Locación <span class="req">*</span></label>
                                    <select class="tr-finp" id="s2-loc-d">
                                        <option value="">Selecciona origen primero</option>
                                    </select>
                                    <div class="tr-ferr" id="e-loc-d">Selecciona la locación de destino.</div>
                                </div>
                                <div>
                                    <label class="tr-flbl">Estructura <span class="opt">(opcional)</span></label>
                                    <select class="tr-finp" id="s2-est-d">
                                        <option value="">— Sin estructura —</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tr-ferr" id="e-misma" style="margin-top:10px;text-align:center"></div>
                </div>

                <!-- ══ PASO 3 ══ -->
                <div class="tr-panel" id="panel3">
                    <div class="tr-resumen-flow">
                        <div class="tr-rbox origen">
                            <div class="tr-rtag origen">Origen</div>
                            <div class="tr-rname" id="r-o-name">—</div>
                            <div class="tr-rsub"  id="r-o-sub"></div>
                        </div>
                        <div><div class="tr-rarrow"><i class="fas fa-arrow-right" style="font-size:12px"></i></div></div>
                        <div class="tr-rbox destino">
                            <div class="tr-rtag destino">Destino</div>
                            <div class="tr-rname" id="r-d-name">—</div>
                            <div class="tr-rsub"  id="r-d-sub"></div>
                        </div>
                    </div>

                    <div class="tr-art-mini">
                        <div>
                            <div style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;color:#6b7280;margin-bottom:3px">Artículo</div>
                            <div style="font-size:13px;font-weight:500;color:#111827" id="r-art-name">—</div>
                            <div style="font-size:12px;color:#6b7280;margin-top:2px" id="r-art-stock">—</div>
                        </div>
                        <div class="tr-bar-wrap">
                            <div class="tr-bar-labels"><span id="bar-used">0</span><span id="bar-total">—</span></div>
                            <div class="tr-bar-bg"><div class="tr-bar-fill" id="bar-fill" style="width:0%"></div></div>
                        </div>
                    </div>

                    <div class="tr-cant-row">
                        <div>
                            <label class="tr-flbl">Cantidad <span class="req">*</span></label>
                            <input type="number" class="tr-finp tr-cant-input"
                                   id="s3-cantidad" placeholder="0" min="1" step="1">
                            <div class="tr-ferr" id="e-cantidad"></div>
                            <div class="tr-cant-rem" id="cant-remain"></div>
                        </div>
                        <div>
                            <label class="tr-flbl">Motivo <span class="opt">(opcional)</span></label>
                            <input type="text" class="tr-finp" id="s3-motivo"
                                   placeholder="Ej: Reabastecimiento punto de venta">
                        </div>
                    </div>
                </div>

            </div><!-- /.tr-mbody -->

            <div class="tr-mnav">
                <button class="tr-btn-back" id="btn-back" disabled>
                    <i class="fas fa-arrow-left me-1" style="font-size:10px"></i> Atrás
                </button>
                <span class="tr-nav-hint" id="nav-hint">Busca el artículo para continuar</span>
                <button class="tr-btn-next" id="btn-next">
                    Siguiente <i class="fas fa-arrow-right ms-1" style="font-size:10px"></i>
                </button>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/scriptNotify.js"></script>

<script>
const SUCURSAL_ID = "<?php echo $_SESSION['sucursal_id'] ?? ''; ?>";
const URL_LOGICA  = "logica/clssTransferencia.php";

/* ── estado ── */
let currentStep       = 1;
let selectedArt       = null;   // {id, nombre, codigo, stockTotal, breakdown:[...]}
let stockOrigenActual = 0;
let dtHistorial       = null;
let searchTimer       = null;
let todasLasLocaciones = [];    // cache para poblar destino

/* ══════════════════════
   INIT
══════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    initDT();
    cargarStats();

    document.getElementById('btnNuevaTransferencia').addEventListener('click', abrirModal);
    document.getElementById('btn-back').addEventListener('click', goBack);
    document.getElementById('btn-next').addEventListener('click', goNext);

    document.getElementById('art-search').addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => buscarArticulo(this.value.trim()), 350);
    });

    document.getElementById('s2-loc-o').addEventListener('change', onOrigenLocChange);
    document.getElementById('s2-est-o').addEventListener('change', onOrigenEstChange);
    document.getElementById('s2-loc-d').addEventListener('change', onDestinoLocChange);
    document.getElementById('s3-cantidad').addEventListener('input', onCantidadInput);
});

/* ══════════════════════
   DATATABLE
══════════════════════ */
function initDT() {
    dtHistorial = $('#tbl-historial').DataTable({
        processing: true, serverSide: true,
        ajax: {
            url: URL_LOGICA, type: 'POST',
            data: d => { d.accion = 'LISTAR_HISTORIAL'; d.sucursal_id = SUCURSAL_ID; },
            error: xhr => console.error('DT:', xhr.responseText),
        },
        columns: [
            { data: 'id', width: '42px' },
            { data: 'nombre_articulo' },
            { data: 'origen',   render: renderRuta },
            { data: 'destino',  render: renderRuta },
            { data: 'cantidad', orderable: false, render: v => `<span class="badge-cant">${v}</span>` },
            { data: 'motivo',   orderable: false, render: v => v || '<span style="color:#9ca3af">—</span>' },
            { data: 'fecha' },
        ],
        order: [[0, 'desc']], pageLength: 10,
        language: {
            sProcessing:'Procesando...', sLengthMenu:'Mostrar _MENU_ registros',
            sZeroRecords:'Sin transferencias', sEmptyTable:'Sin transferencias aún',
            sInfo:'_START_ al _END_ de _TOTAL_', sInfoEmpty:'0 registros',
            sInfoFiltered:'(de _MAX_)', sSearch:'Buscar:', sLoadingRecords:'Cargando...',
            oPaginate:{ sFirst:'Primero', sPrevious:'Anterior', sNext:'Siguiente', sLast:'Último' }
        }
    });
}

function renderRuta(v) {
    if (!v) return '<span style="color:#9ca3af">—</span>';
    const p = v.split(' → ');
    return `<div class="hist-ruta">
        <span class="hist-ruta-loc">${p[0]}</span>
        ${p[1] ? `<span style="color:#d1d5db">›</span><span class="hist-ruta-est">${p[1]}</span>` : ''}
    </div>`;
}

/* ══════════════════════
   STATS
══════════════════════ */
async function cargarStats() {
    try {
        const res = await fnAjax('GET_STATS', {});
        if (res.success) {
            document.getElementById('stat-hoy')     .textContent = res.data.hoy     ?? '—';
            document.getElementById('stat-unidades').textContent = res.data.unidades ?? '—';
            document.getElementById('stat-total')   .textContent = res.data.total    ?? '—';
        }
    } catch(e) {}
}

/* ══════════════════════
   ABRIR / RESET MODAL
══════════════════════ */
async function abrirModal() {
    selectedArt       = null;
    stockOrigenActual = 0;

    /* resetear UI paso 1 */
    document.getElementById('art-search').value = '';
    document.getElementById('art-results').innerHTML = '';
    document.getElementById('art-results').style.display  = 'none';
    document.getElementById('art-breakdown').style.display = 'none';
    document.getElementById('breakdown-rows').innerHTML   = '';
    document.getElementById('art-hint').style.display     = 'none';
    document.getElementById('e-art').classList.remove('on');

    goToStep(1);
    new bootstrap.Modal(document.getElementById('modalTransferencia')).show();

    /* precargar locaciones para destino */
    try {
        const res = await fnAjax('LISTAR_LOCACIONES', {});
        todasLasLocaciones = res.success ? res.data : [];
    } catch(e) { todasLasLocaciones = []; }
}

/* ══════════════════════
   STEPPER
══════════════════════ */
function goToStep(n) {
    document.querySelectorAll('.tr-panel').forEach(p => p.classList.remove('on'));
    document.getElementById('panel' + n).classList.add('on');
    currentStep = n;

    [1, 2, 3].forEach(i => {
        const num = document.getElementById('sn' + i);
        const lbl = document.getElementById('sl' + i);
        if      (i < n)  { num.className = 'tr-snum done';   lbl.className = 'tr-slbl done'; }
        else if (i === n){ num.className = 'tr-snum active'; lbl.className = 'tr-slbl active'; }
        else             { num.className = 'tr-snum idle';   lbl.className = 'tr-slbl'; }
        if (i < 3) document.getElementById('sep' + i + (i + 1)).className =
            'tr-ssep' + (i < n ? ' done' : '');
    });

    const subs = [
        'Paso 1 de 3 — Busca el artículo',
        'Paso 2 de 3 — Define origen y destino',
        'Paso 3 de 3 — Indica la cantidad y confirma',
    ];
    document.getElementById('mhdr-sub').textContent = subs[n - 1];
    document.getElementById('btn-back').disabled = (n === 1);

    const hints = [
        'Selecciona un artículo para continuar',
        'Completa origen y destino',
        'Ingresa la cantidad y transfiere',
    ];
    document.getElementById('nav-hint').textContent = hints[n - 1];

    const btn = document.getElementById('btn-next');
    if (n === 3) {
        btn.innerHTML = '<i class="fas fa-exchange-alt me-1" style="font-size:11px"></i> Transferir';
        btn.className = 'tr-btn-next green';
    } else {
        btn.innerHTML = 'Siguiente <i class="fas fa-arrow-right ms-1" style="font-size:10px"></i>';
        btn.className = 'tr-btn-next';
    }
}

function goBack() { if (currentStep > 1) goToStep(currentStep - 1); }
function goNext() {
    if      (currentStep === 1) paso1Next();
    else if (currentStep === 2) paso2Next();
    else                        ejecutarTransferencia();
}

/* ══════════════════════
   PASO 1 — BÚSQUEDA
══════════════════════ */
async function buscarArticulo(q) {
    const box = document.getElementById('art-results');

    // fix: limpiar estado previo al escribir
    document.getElementById('e-art').classList.remove('on');
    document.getElementById('art-breakdown').style.display = 'none';
    document.getElementById('art-hint').style.display      = 'none';
    selectedArt = null;

    if (q.length < 2) { box.style.display = 'none'; return; }

    box.style.display = 'block';
    box.innerHTML = '<div class="tr-art-msg">Buscando...</div>';

    try {
        const res = await fnAjax('BUSCAR_ARTICULO', { q });
        if (!res.success || !res.data.length) {
            box.innerHTML = '<div class="tr-art-msg">Sin resultados para esa búsqueda.</div>';
            return;
        }
        box.innerHTML = res.data.map(a => `
            <div class="tr-art-row" data-id="${a.id}" onclick="selectArticulo(${a.id})">
                <div>
                    <div class="tr-art-name">${a.nombre}</div>
                    ${a.codigo ? `<div class="tr-art-code">${a.codigo}</div>` : ''}
                </div>
                <div>
                    <div class="tr-art-qty">${a.stock_total}</div>
                    <div class="tr-art-qlbl">total sucursal</div>
                </div>
            </div>`).join('');
    } catch(e) {
        box.innerHTML = '<div class="tr-art-msg">Error al buscar. Intenta de nuevo.</div>';
    }
}

async function selectArticulo(id) {
    document.querySelectorAll('.tr-art-row').forEach(r => r.classList.remove('selected'));
    document.querySelector(`.tr-art-row[data-id="${id}"]`)?.classList.add('selected');
    document.getElementById('e-art').classList.remove('on'); // ← fix: limpiar error al seleccionar

    try {
        const res = await fnAjax('STOCK_POR_UBICACION', { articulo_id: id });
        if (!res.success) return;

        selectedArt = {
            id,
            nombre:     res.articulo.nombre,
            codigo:     res.articulo.codigo,
            stockTotal: res.articulo.stock_total,
            breakdown:  res.data,
        };

        document.getElementById('breakdown-rows').innerHTML = res.data.length
            ? res.data.map(r => `
                <div class="tr-ubic-row">
                    <div>
                        <div class="tr-ubic-loc">${r.locacion_nombre}</div>
                        <div class="tr-ubic-est">${r.estructura_nombre || 'sin estructura'}</div>
                    </div>
                    <span class="tr-ubic-qty">${r.stock}</span>
                </div>`).join('')
            : '<div class="tr-art-msg">Sin stock en ninguna ubicación.</div>';

        document.getElementById('art-breakdown').style.display = 'block';
        document.getElementById('art-hint').style.display      = 'block';
    } catch(e) { 
        console.error('selectArticulo error:', e);
        alert('Error: ' + e.message); // temporal
    }
}

function paso1Next() {
    if (!selectedArt) { document.getElementById('e-art').classList.add('on'); return; }
    document.getElementById('e-art').classList.remove('on');

    /* contexto paso 2 */
    document.getElementById('ctx-art-name') .textContent = selectedArt.nombre;
    document.getElementById('ctx-art-stock').textContent =
        `Stock total en sucursal: ${selectedArt.stockTotal} unidades`;

    /* origen — solo locaciones que tienen stock del artículo */
    const locsConStock = [...new Map(
        selectedArt.breakdown.map(r => [r.locacion_id, r.locacion_nombre])
    )];
    const selO = document.getElementById('s2-loc-o');
    selO.innerHTML = '<option value="">Seleccione...</option>';
    locsConStock.forEach(([id, nombre]) => {
        selO.innerHTML += `<option value="${id}">${nombre}</option>`;
    });

    /* reset resto */
    document.getElementById('s2-est-o').innerHTML = '<option value="">— Sin estructura —</option>';
    document.getElementById('s2-loc-d').innerHTML = '<option value="">Selecciona origen primero</option>';
    document.getElementById('s2-est-d').innerHTML = '<option value="">— Sin estructura —</option>';
    document.getElementById('origen-badge').className = 'tr-stock-badge';
    ['e-loc-o','e-loc-d','e-misma'].forEach(id => document.getElementById(id).classList.remove('on'));

    goToStep(2);
}

/* ══════════════════════
   PASO 2 — ORIGEN / DESTINO
══════════════════════ */
function onOrigenLocChange() {
    const locId = this.value;

    /* estructuras: solo las que tienen stock del artículo en esa locación */
    const estConStock = selectedArt.breakdown.filter(r =>
        String(r.locacion_id) === locId && r.estructura_id
    );
    const selEO = document.getElementById('s2-est-o');
    selEO.innerHTML = '<option value="">— Sin estructura —</option>';
    estConStock.forEach(r => {
        selEO.innerHTML += `<option value="${r.estructura_id}">${r.estructura_nombre}</option>`;
    });

    actualizarBadgeOrigen(locId, '');
    popularDestino(locId);
    document.getElementById('e-misma').classList.remove('on');
}

function onOrigenEstChange() {
    const locId = document.getElementById('s2-loc-o').value;
    actualizarBadgeOrigen(locId, this.value);
}

function actualizarBadgeOrigen(locId, estId) {
    const badge = document.getElementById('origen-badge');
    if (!locId) { badge.className = 'tr-stock-badge'; stockOrigenActual = 0; return; }

    const row = selectedArt.breakdown.find(r =>
        String(r.locacion_id)   === String(locId) &&
        String(r.estructura_id  ?? '') === String(estId ?? '')
    );

    if (row) {
        stockOrigenActual   = parseFloat(row.stock);
        badge.textContent   = `Stock disponible: ${row.stock} unidades`;
        badge.className     = 'tr-stock-badge on';
    } else {
        stockOrigenActual   = 0;
        badge.className     = 'tr-stock-badge';
    }
}

function popularDestino(origenLocId) {
    /* todas las locaciones de la sucursal EXCEPTO la de origen */
    const selD = document.getElementById('s2-loc-d');
    selD.innerHTML = '<option value="">Seleccione...</option>';
    todasLasLocaciones
        .filter(l => String(l.id) !== String(origenLocId))
        .forEach(l => {
            selD.innerHTML += `<option value="${l.id}">[${l.tipo}] ${l.nombre}</option>`;
        });
    document.getElementById('s2-est-d').innerHTML = '<option value="">— Sin estructura —</option>';
}

async function onDestinoLocChange() {
    const locId = this.value;
    const selED = document.getElementById('s2-est-d');
    selED.innerHTML = '<option value="">— Sin estructura —</option>';
    if (!locId) return;
    try {
        const res = await fnAjax('LISTAR_ESTRUCTURAS', { locacion_id: locId });
        if (res.success) res.data.forEach(e => {
            selED.innerHTML += `<option value="${e.id}">[${e.tipo}] ${e.nombre}</option>`;
        });
    } catch(e) {}
    document.getElementById('e-misma').classList.remove('on');
}

function paso2Next() {
    const lo = document.getElementById('s2-loc-o').value;
    const ld = document.getElementById('s2-loc-d').value;
    const eo = document.getElementById('s2-est-o').value;
    const ed = document.getElementById('s2-est-d').value;

    let ok = true;
    if (!lo) { showErr('e-loc-o', 'Selecciona la locación de origen.');  ok = false; } else hideErr('e-loc-o');
    if (!ld) { showErr('e-loc-d', 'Selecciona la locación de destino.'); ok = false; } else hideErr('e-loc-d');
    if (!ok) return;
    if (lo === ld && eo === ed) { showErr('e-misma', 'Origen y destino no pueden ser idénticos.'); return; }
    hideErr('e-misma');

    /* rellenar resumen */
    const txt = id => document.getElementById(id);
    document.getElementById('r-o-name').textContent = txt('s2-loc-o').options[txt('s2-loc-o').selectedIndex].text;
    document.getElementById('r-o-sub') .textContent = eo ? txt('s2-est-o').options[txt('s2-est-o').selectedIndex].text : 'Sin estructura';
    document.getElementById('r-d-name').textContent = txt('s2-loc-d').options[txt('s2-loc-d').selectedIndex].text;
    document.getElementById('r-d-sub') .textContent = ed ? txt('s2-est-d').options[txt('s2-est-d').selectedIndex].text : 'Sin estructura';
    document.getElementById('r-art-name') .textContent = selectedArt.nombre;
    document.getElementById('r-art-stock').textContent = `Disponible en origen: ${stockOrigenActual} unidades`;
    document.getElementById('bar-total')  .textContent = `${stockOrigenActual}`;
    document.getElementById('bar-used')   .textContent = '0';
    document.getElementById('bar-fill').style.width     = '0%';
    document.getElementById('bar-fill').style.background = '#3B6D11';
    document.getElementById('s3-cantidad').value          = '';
    document.getElementById('s3-cantidad').dataset.max    = stockOrigenActual;
    document.getElementById('cant-remain').textContent    = '';
    document.getElementById('e-cantidad').classList.remove('on');

    goToStep(3);
}

/* ══════════════════════
   PASO 3 — CANTIDAD
══════════════════════ */
function onCantidadInput() {
    const cant = parseFloat(this.value || 0);
    const max  = parseFloat(this.dataset.max || 0);
    const pct  = max > 0 ? Math.min(100, Math.round(cant / max * 100)) : 0;
    const fill = document.getElementById('bar-fill');

    fill.style.width      = pct + '%';
    fill.style.background = cant > max ? '#E24B4A' : cant / max > 0.8 ? '#854F0B' : '#3B6D11';
    document.getElementById('bar-used').textContent = Math.round(cant) || 0;

    const rem = document.getElementById('cant-remain');
    document.getElementById('e-cantidad').classList.remove('on');

    if (cant > 0 && cant <= max) {
        const q = Math.round(max - cant);
        rem.textContent = `Quedarán ${q} unidad${q !== 1 ? 'es' : ''} en origen`;
        rem.className   = 'tr-cant-rem ' + (q < 10 ? 'tr-cr-warn' : 'tr-cr-ok');
    } else if (cant > max) {
        rem.textContent = 'Supera el stock disponible';
        rem.className   = 'tr-cant-rem tr-cr-bad';
    } else {
        rem.textContent = ''; rem.className = 'tr-cant-rem';
    }
}

/* ══════════════════════
   EJECUTAR
══════════════════════ */
async function ejecutarTransferencia() {
    const cant = parseFloat(document.getElementById('s3-cantidad').value || 0);
    const max  = parseFloat(document.getElementById('s3-cantidad').dataset.max || 0);

    if (!cant || cant <= 0) { showErr('e-cantidad', 'Ingresa una cantidad mayor a 0.'); return; }
    if (cant > max)         { showErr('e-cantidad', `Supera el stock disponible (${max}).`); return; }
    hideErr('e-cantidad');

    const datos = {
        articulo_id:           selectedArt.id,
        locacion_origen_id:    document.getElementById('s2-loc-o').value,
        estructura_origen_id:  document.getElementById('s2-est-o').value  || null,
        locacion_destino_id:   document.getElementById('s2-loc-d').value,
        estructura_destino_id: document.getElementById('s2-est-d').value || null,
        cantidad:              cant,
        motivo:                document.getElementById('s3-motivo').value.trim(),
    };

    const confirm = await Swal.fire({
        title: '¿Confirmar transferencia?',
        html:  `Mover <strong>${cant}</strong> unidad(es) de <strong>${selectedArt.nombre}</strong><br>
                de <em>${document.getElementById('r-o-name').textContent}</em>
                a <em>${document.getElementById('r-d-name').textContent}</em>.`,
        icon: 'question', showCancelButton: true,
        confirmButtonColor: '#3B6D11', cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-exchange-alt me-1"></i> Sí, transferir',
        cancelButtonText: 'Cancelar',
    });
    if (!confirm.isConfirmed) return;

    try {
        const res = await fnAjax('TRANSFERIR', { data: JSON.stringify(datos) });
        if (res.success) {
            await Swal.fire({ title: '¡Transferido!', text: res.message, icon: 'success', timer: 1800, showConfirmButton: false });
            bootstrap.Modal.getInstance(document.getElementById('modalTransferencia')).hide();
            dtHistorial.ajax.reload(null, false);
            cargarStats();
        } else {
            Swal.fire('Aviso', res.message || 'No se pudo realizar la transferencia.', 'warning');
        }
    } catch(e) { Swal.fire('Error', e.message, 'error'); }
}

/* ══════════════════════
   HELPERS
══════════════════════ */
function showErr(id, msg) { const el = document.getElementById(id); el.textContent = msg; el.classList.add('on'); }
function hideErr(id) { document.getElementById(id).classList.remove('on'); }

function fnAjax(accion, extraData) {
    return new Promise((resolve, reject) => {
        $.ajax({
            method: 'POST', url: URL_LOGICA,
            data: Object.assign({ accion, sucursal_id: SUCURSAL_ID }, extraData),
        })
        .done(r => {
            try { resolve(typeof r === 'string' ? JSON.parse(r) : r); }
            catch(e) { reject(new Error('Respuesta inválida: ' + r)); }
        })
        .fail(xhr => reject(new Error('Error de conexión: ' + xhr.status)));
    });
}
</script>

<?php include("pie.php"); ?>