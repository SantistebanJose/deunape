<?php
include("cabecera.php");
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}
?>
<div class="container">
    <div class="page-inner">
        <div class="card text-start">
            <div class="card-body">
                <h4 class="card-title"><i class="fas fa-align-left"></i> Listado de Pagos</h4>
                <div class="card-sub">
                    Selecciona de acuerdo a los pagos realizados y revisa sus detalles :)
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills nav-secondary nav-pills-no-bd" id="pills-tab-without-border" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="ventaDiaria" data-bs-toggle="pill" href="#pills-pagosDiarios" role="tab" aria-controls="pills-pagosDiarios" aria-selected="true"><i class="fas fa-clock"></i> Pagos del Día</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="ventaSemanal" data-bs-toggle="pill" href="#pills-pagoSemanal" role="tab" aria-controls="pills-pagoSemanal" aria-selected="false"><i class="fas fa-calendar-alt"></i> Pagos de la Semana</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-contact-tab-nobd" data-bs-toggle="pill" href="#pills-todosLosPagos" role="tab" aria-controls="pills-todosLosPagos" aria-selected="false"><i class="fas fa-chart-bar"></i> Todos los Pagos</a>
                        </li>
                    </ul>

                    <!-- ── PANEL DE KPIs ── -->
                    <div id="panelKpis" class="row g-3 my-3">
                        <div class="col-6 col-md-3">
                            <div class="kpi-card kpi-green">
                                <div class="kpi-icon"><i class="fas fa-dollar-sign"></i></div>
                                <div class="kpi-label">Ingresos Totales</div>
                                <div class="kpi-value">S/ <span id="kpiIngresos">0.00</span></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="kpi-card kpi-blue">
                                <div class="kpi-icon"><i class="fas fa-shopping-cart"></i></div>
                                <div class="kpi-label">Transacciones</div>
                                <div class="kpi-value"><span id="kpiTransacciones">0</span></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="kpi-card kpi-orange">
                                <div class="kpi-icon"><i class="fas fa-receipt"></i></div>
                                <div class="kpi-label">Ticket Promedio</div>
                                <div class="kpi-value">S/ <span id="kpiPromedio">0.00</span></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="kpi-card kpi-red">
                                <div class="kpi-icon"><i class="fas fa-tags"></i></div>
                                <div class="kpi-label">Descuentos</div>
                                <div class="kpi-value">S/ <span id="kpiDescuentos">0.00</span></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="kpi-card kpi-purple">
                                <div class="kpi-icon"><i class="fas fa-file-invoice"></i></div>
                                <div class="kpi-label">Boletas</div>
                                <div class="kpi-value"><span id="kpiBoletas">0</span></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="kpi-card kpi-yellow">
                                <div class="kpi-icon"><i class="fas fa-file-alt"></i></div>
                                <div class="kpi-label">Facturas</div>
                                <div class="kpi-value"><span id="kpiFacturas">0</span></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="kpi-card kpi-teal">
                                <div class="kpi-icon"><i class="fas fa-arrow-up"></i></div>
                                <div class="kpi-label">Pago Más Alto</div>
                                <div class="kpi-value">S/ <span id="kpiMaxPago">0.00</span></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="kpi-card kpi-gray">
                                <div class="kpi-icon"><i class="fas fa-calendar-day"></i></div>
                                <div class="kpi-label">Días con Pagos</div>
                                <div class="kpi-value"><span id="kpiDias">0</span></div>
                            </div>
                        </div>
                    </div>
                    <!-- Badge período -->
                    <div class="mb-3">
                        <span class="badge bg-secondary px-3 py-2" id="kpiPeriodoLabel">
                            <i class="fas fa-calendar"></i> Pagos del Día
                        </span>
                    </div>

                    <div class="tab-content mt-2 mb-3" id="pills-without-border-tabContent">

                        <!-- PAGOS DEL DÍA -->
                        <div class="tab-pane fade show active" id="pills-pagosDiarios" role="tabpanel" aria-labelledby="ventaDiaria">
                            <div class="card text-start">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="TablaPagosDiarios" class="dataTable display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>N° TICKET</th>
                                                    <th>DÍA</th>
                                                    <th>FECHA</th>
                                                    <th>HORA</th>
                                                    <th>TOTAL Original(S/)</th>
                                                    <th>TOTAL FINAL (S/)</th>
                                                    <th>Pérdida</th>
                                                    <th>Accion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sucursal_id = isset($_SESSION['sucursal_id']) ? $_SESSION['sucursal_id'] : null;
                                                foreach (fnListForPagos($sucursal_id) as $datos) {
                                                    $datosJSON = json_encode($datos);
                                                ?>
                                                    <tr>
                                                        <td><?php echo $datos["pago_id"] ?></td>
                                                        <td><?php echo $datos["serie_correltavio_referencial"] ?></td>
                                                        <td><?php echo $datos["dia_nombre"] ?></td>
                                                        <td><?php echo $datos["fecha"] ?></td>
                                                        <td><?php echo $datos["hora"] ?></td>
                                                        <td><?php echo "S/ " . $datos["monto_venta_original"] ?></td>
                                                        <td><?php echo "S/ " . $datos["monto_venta_final"] ?></td>
                                                        <td><?php echo "S/ " . $datos["utilidad"] ?></td>
                                                        <td>
                                                            <div class="mt-2 text-center d-flex justify-content-center">
                                                                <a onclick='abrirModalDetallePago(<?php echo $datosJSON ?>)' class="btn btn-success btn-round btn-sm" role="button">DETALLE</a>
                                                                <a href="javascript:void(0);" onclick='fn_abrir_pdf(<?php echo $datos["venta_id"] ?>)' class="btn btn-secondary btn-round btn-sm mx-1" role="button">PDF</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PAGOS DE LA SEMANA -->
                        <div class="tab-pane fade" id="pills-pagoSemanal" role="tabpanel" aria-labelledby="ventaSemanal">
                            <div class="card text-start">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="TablaPagosSemanal" class="dataTable display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>N° TICKET</th>
                                                    <th>DÍA</th>
                                                    <th>FECHA</th>
                                                    <th>HORA</th>
                                                    <th>TOTAL Original(S/)</th>
                                                    <th>TOTAL FINAL (S/)</th>
                                                    <th>Pérdida</th>
                                                    <th>Accion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach (fnListForPagosSemanales($sucursal_id) as $datos) {
                                                    $datosJSON = json_encode($datos);
                                                ?>
                                                    <tr>
                                                        <td><?php echo $datos["pago_id"] ?></td>
                                                        <td><?php echo $datos["serie_correltavio_referencial"] ?></td>
                                                        <td><?php echo $datos["dia_nombre"] ?></td>
                                                        <td><?php echo $datos["fecha"] ?></td>
                                                        <td><?php echo $datos["hora"] ?></td>
                                                        <td><?php echo "S/ " . $datos["monto_venta_original"] ?></td>
                                                        <td><?php echo "S/ " . $datos["monto_venta_final"] ?></td>
                                                        <td><?php echo "S/ " . $datos["utilidad"] ?></td>
                                                        <td>
                                                            <div class="mt-2 text-center d-flex justify-content-center">
                                                                <a onclick='abrirModalDetallePago(<?php echo $datosJSON ?>)' class="btn btn-success btn-round btn-sm" role="button">DETALLE</a>
                                                                <a href="javascript:void(0);" onclick='fn_abrir_pdf(<?php echo $datos["venta_id"] ?>)' class="btn btn-secondary btn-round btn-sm mx-1" role="button">PDF</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TODOS LOS PAGOS -->
                        <div class="tab-pane fade" id="pills-todosLosPagos" role="tabpanel" aria-labelledby="pills-contact-tab-nobd">
                            <div class="card text-start">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="TablaTodosLosPagos" class="dataTable display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>N° TICKET</th>
                                                    <th>Tipo</th>
                                                    <th>DÍA</th>
                                                    <th>FECHA</th>
                                                    <th>HORA</th>
                                                    <th>TOTAL Original(S/)</th>
                                                    <th>TOTAL FINAL (S/)</th>
                                                    <th>Pérdida</th>
                                                    <th>Accion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach (fnListForAllPagos() as $datos) {
                                                    $datosJSON = json_encode($datos);
                                                ?>
                                                    <tr>
                                                        <td><?php echo $datos["pago_id"] ?></td>
                                                        <td><?php echo $datos["serie_correltavio_referencial"] ?></td>
                                                        <td><?php echo $datos["tipo_comprobante"] ?></td>
                                                        <td><?php echo $datos["dia_nombre"] ?></td>
                                                        <td><?php echo $datos["fecha"] ?></td>
                                                        <td><?php echo $datos["hora"] ?></td>
                                                        <td><?php echo "S/ " . $datos["monto_venta_original"] ?></td>
                                                        <td><?php echo "S/ " . $datos["monto_venta_final"] ?></td>
                                                        <td><?php echo "S/ " . $datos["utilidad"] ?></td>
                                                        <td>
                                                            <div class="mt-2 text-center d-flex justify-content-center">
                                                                <a onclick='abrirModalDetallePago(<?php echo $datosJSON ?>)' class="btn btn-success btn-round btn-sm" role="button">DETALLE</a>
                                                                <a href="javascript:void(0);" onclick='fn_abrir_pdf(<?php echo $datos["venta_id"] ?>)' class="btn btn-secondary btn-round btn-sm mx-1" role="button">PDF</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div><!-- fin tab-content -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalle Pago -->
<div class="modal fade" id="modalDetallePago" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-custom" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="card-body">
                <h4 class="card-title text-center" style="font-size:28px;">Venta de S/ <strong id="idMontoVenta"></strong></h4>
                <hr>
                <div class="card-sub text-center">Aquí podrás revisar los datos de la venta.</div>
                <div class="row justify-content-center align-items-center sm-2">
                    <div class="col-sm-6">
                        <div class="card text-start">
                            <div class="card-body">
                                <h4 class="card-title"><i class="fas fa-user"></i> Cliente</h4>
                                <p class="card-text" id="nombreCliente"></p>
                                <hr>
                                <div><strong>N° DOCUMENTO:</strong> <span id="docCliente"></span></div>
                                <div><strong>Número de Celular:</strong> <span id="numCelCliente"></span></div>
                                <div><strong>Correo:</strong> <span id="emailCliente"></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card text-start">
                            <div class="card-body">
                                <h4 class="card-text"><i class="fas fa-credit-card"></i> Monto Final: S/ <strong id="idMontoFinalVenta"></strong></h4>
                                <p>La venta real fue de <strong id="idTotalOriginal"></strong></p>
                                <div><strong>Atendido Por:</strong> <span id="idUsuario"></span></div>
                                <div><strong>Fecha:</strong> <span id="idFechaVenta"></span></div>
                                <div><strong>Hora:</strong> <span id="idHoraVenta"></span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="accordion accordion-flush" id="accordionFlushExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="flush-headingOne">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                        <strong><i class="fas fa-cart-arrow-down"></i> Detalle de Venta</strong>
                                    </button>
                                </h2>
                                <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                    <div class="accordion-body">
                                        <div class="card-sub">Revisa el detalle de la venta :)</div>
                                        <div class="table-responsive">
                                            <table id="tablaDetalle" class="table table-head-bg-secondary mt-4">
                                                <thead>
                                                    <tr>
                                                        <th>Descripción</th>
                                                        <th>Corte</th>
                                                        <th>Cant</th>
                                                        <th>P.Uni</th>
                                                        <th>Sub Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="flush-headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                                        <strong><i class="fas fa-donate"></i> Forma de Pagos</strong>
                                    </button>
                                </h2>
                                <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                                    <div class="accordion-body">
                                        <table id="tablita" class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th style="border-bottom:1px solid #000;"><i class="fas fa-credit-card"></i> Pagos</th>
                                                    <th style="border-bottom:1px solid #000;"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="idTbodyTablita"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.modal-dialog-custom { max-width: 900px; margin: 0 auto; }
@media (max-width: 768px) { .modal-dialog-custom { max-width: 80%; } }
@media (max-width: 576px) { .modal-dialog-custom { width: 100%; margin: 0 10px; max-width: 100%; } }
.modal-content { padding: 15px; }

/* ── KPI Cards ── */
.kpi-card {
    border-radius: 14px;
    padding: 18px 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 5px;
    box-shadow: 0 2px 10px rgba(0,0,0,.07);
    transition: transform .15s, box-shadow .15s;
}
.kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 18px rgba(0,0,0,.13);
}
.kpi-icon  { font-size: 1.6rem; opacity: .8; }
.kpi-label { font-size: .70rem; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; opacity: .70; }
.kpi-value { font-size: 1.30rem; font-weight: 800; line-height: 1.2; }

.kpi-green  { background: #d1fae5; color: #065f46; }
.kpi-blue   { background: #dbeafe; color: #1e40af; }
.kpi-orange { background: #ffedd5; color: #9a3412; }
.kpi-red    { background: #fee2e2; color: #991b1b; }
.kpi-purple { background: #ede9fe; color: #5b21b6; }
.kpi-yellow { background: #fef9c3; color: #854d0e; }
.kpi-teal   { background: #ccfbf1; color: #134e4a; }
.kpi-gray   { background: #f3f4f6; color: #374151; }
</style>

<script>
// ── Calcular y mostrar KPIs ────────────────────────────────────
function fnActualizarKpis(datos, label) {
    var totalIngresos   = 0;
    var totalDescuentos = 0;
    var maxPago         = 0;
    var boletas         = 0;
    var facturas        = 0;
    var diasUnicos      = new Set();

    datos.forEach(function(d) {
        var monto     = parseFloat(d.monto_venta_final)    || 0;
        var original  = parseFloat(d.monto_venta_original) || 0;
        var diferencia = original - monto;

        totalIngresos   += monto;
        totalDescuentos += (diferencia > 0 ? diferencia : 0);
        if (monto > maxPago) maxPago = monto;

        var tipo = (d.tipo_comprobante || '').toUpperCase();
        if (tipo.includes('BOLETA'))  boletas++;
        if (tipo.includes('FACTURA')) facturas++;

        diasUnicos.add(d.fecha);
    });

    var promedio = datos.length > 0 ? (totalIngresos / datos.length) : 0;

    document.getElementById('kpiIngresos').innerText      = totalIngresos.toFixed(2);
    document.getElementById('kpiTransacciones').innerText = datos.length;
    document.getElementById('kpiPromedio').innerText      = promedio.toFixed(2);
    document.getElementById('kpiDescuentos').innerText    = totalDescuentos.toFixed(2);
    document.getElementById('kpiBoletas').innerText       = boletas;
    document.getElementById('kpiFacturas').innerText      = facturas;
    document.getElementById('kpiMaxPago').innerText       = maxPago.toFixed(2);
    document.getElementById('kpiDias').innerText          = diasUnicos.size;
    document.getElementById('kpiPeriodoLabel').innerHTML  = '<i class="fas fa-calendar"></i> ' + label;
}

// ── Leer KPIs desde tabla ya renderizada en PHP ───────────────
// columnas: 0=ID, 1=ticket, 2=tipo(solo en todos), 3=dia, 4=fecha, 5=hora, 6=original, 7=final, 8=perdida
function fnKpisDesdeTabla(tablaId, label, tieneTipo) {
    var filas = document.querySelectorAll('#' + tablaId + ' tbody tr');
    var datos = [];
    filas.forEach(function(tr) {
        var celdas = tr.querySelectorAll('td');
        if (celdas.length < 8) return;

        if (tieneTipo) {
            // Tabla "Todos los pagos" tiene columna Tipo en posición 2
            datos.push({
                monto_venta_final:    celdas[7].innerText.replace('S/', '').trim(),
                monto_venta_original: celdas[6].innerText.replace('S/', '').trim(),
                tipo_comprobante:     celdas[2].innerText.trim(),
                fecha:                celdas[4].innerText.trim()
            });
        } else {
            datos.push({
                monto_venta_final:    celdas[6].innerText.replace('S/', '').trim(),
                monto_venta_original: celdas[5].innerText.replace('S/', '').trim(),
                tipo_comprobante:     '',
                fecha:                celdas[3].innerText.trim()
            });
        }
    });
    fnActualizarKpis(datos, label);
}

// ── PDF ────────────────────────────────────────────────────────
function fn_abrir_pdf(id_venta) {
    fetch("ticket.php?accion=token&id=" + parseInt(id_venta))
        .then(r => r.json())
        .then(urls => {
            Swal.fire({
                title: '¿Cómo deseas ver el comprobante?',
                html: `
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:8px;">
                    <button onclick="window.open('${urls.ticket}','_blank');Swal.close();"
                        style="background:#2a2f5b;color:white;border:none;border-radius:12px;padding:14px 10px;cursor:pointer;font-weight:700;font-size:.88rem;">
                        🖨️ Ticket<br><small style="font-weight:400;opacity:.8;">80mm / POS</small>
                    </button>
                    <button onclick="window.open('${urls.a4}','_blank');Swal.close();"
                        style="background:#2a2f5b;color:white;border:none;border-radius:12px;padding:14px 10px;cursor:pointer;font-weight:700;font-size:.88rem;">
                        📄 A4<br><small style="font-weight:400;opacity:.8;">Hoja completa</small>
                    </button>
                    <button onclick="window.open('${urls.a5}','_blank');Swal.close();"
                        style="background:#2a2f5b;color:white;border:none;border-radius:12px;padding:14px 10px;cursor:pointer;font-weight:700;font-size:.88rem;">
                        📋 A5<br><small style="font-weight:400;opacity:.8;">Medio oficio</small>
                    </button>
                    <button onclick="window.open('${urls.pantalla}','_blank');Swal.close();"
                        style="background:#11998e;color:white;border:none;border-radius:12px;padding:14px 10px;cursor:pointer;font-weight:700;font-size:.88rem;">
                        🌐 Pantalla<br><small style="font-weight:400;opacity:.8;">HTML / WhatsApp</small>
                    </button>
                </div>`,
                showConfirmButton: false,
                showCloseButton: true,
                width: 360,
            });
        });
}

// ── Modal detalle pago ─────────────────────────────────────────
function abrirModalDetallePago(jsonDatos) {
    $('#modalDetallePago').modal('show');

    var js_venta = JSON.parse(jsonDatos["js_venta"]);

    document.getElementById("nombreCliente").innerText     = js_venta["cliente"];
    document.getElementById("docCliente").innerText        = js_venta["numero_doc_cliente"];
    document.getElementById("numCelCliente").innerText     = js_venta["telefonomovil_cliente"];
    document.getElementById("emailCliente").innerText      = js_venta["email_cliente"];
    document.getElementById("idMontoVenta").innerText      = parseFloat(js_venta["monto_venta_final"]).toFixed(2);
    document.getElementById("idMontoFinalVenta").innerText = parseFloat(js_venta["monto_venta_final"]).toFixed(2);
    document.getElementById("idTotalOriginal").innerText   = "S/ " + parseFloat(js_venta["total"]).toFixed(2);
    document.getElementById("idFechaVenta").innerText      = js_venta["fecha"];
    document.getElementById("idHoraVenta").innerText       = js_venta["hora"];
    document.getElementById("idUsuario").innerText         = js_venta["usuario"];

    // Detalle de artículos
    var list_js_detalle_venta = JSON.parse(jsonDatos["js_detalle_venta"]);
    var tabla = document.getElementById("tablaDetalle").getElementsByTagName("tbody")[0];
    tabla.innerHTML = '';

    for (let i = 0; i < list_js_detalle_venta.length; i++) {
        let a = list_js_detalle_venta[i];
        let totalCorte = (a.minutos === null && a.costo_por_minuto === null) ? '-' :
            (a.minutos && a.costo_por_minuto) ? (a.costo_por_minuto * a.minutos) : a.sub_total || '-';
        let tcR  = (totalCorte !== '-') ? "S/ " + parseFloat(totalCorte).toFixed(2) : totalCorte;
        let fila = tabla.insertRow();
        fila.insertCell(0).innerHTML   = a.descripcion_articulo;
        fila.insertCell(1).textContent = tcR;
        fila.insertCell(2).textContent = a.cantidad || '-';
        fila.insertCell(3).textContent = a.precio_unitario_articulo != null ? "S/ " + a.precio_unitario_articulo : '-';
        fila.insertCell(4).textContent = "S/ " + a.sub_total || '-';
    }

    // Forma de pagos
    var lis_pagos = JSON.parse(jsonDatos["js_detalle_forma_pago"]);
    var tableRowsPagos = '';
    lis_pagos.forEach(function(pago) {
        tableRowsPagos += `
            <tr style="border-bottom:1px solid #000;">
                <td><strong style="color:${pago.COLOR}">${pago.FORMA_PAGO}</strong></td>
                <td><strong>S/ ${parseFloat(pago.MONTO).toFixed(2)}</strong></td>
            </tr>`;
    });
    document.getElementById("idTbodyTablita").innerHTML = tableRowsPagos;
}

// ── Init ───────────────────────────────────────────────────────
$(document).ready(function() {
    fnDataTables();

    // KPIs iniciales — pestaña activa es "Pagos del Día"
    setTimeout(function() {
        fnKpisDesdeTabla('TablaPagosDiarios', 'Pagos del Día', false);
    }, 400);

    // Recalcular KPIs al cambiar de pestaña
    $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
        var target = $(e.target).attr('href');
        if      (target === '#pills-pagosDiarios')  fnKpisDesdeTabla('TablaPagosDiarios',  'Pagos del Día',      false);
        else if (target === '#pills-pagoSemanal')    fnKpisDesdeTabla('TablaPagosSemanal',  'Pagos de la Semana', false);
        else if (target === '#pills-todosLosPagos')  fnKpisDesdeTabla('TablaTodosLosPagos', 'Todos los Pagos',    true);
    });
});

function fnDataTables() {
    $(".dataTable").DataTable({
        order: [[0, 'desc']],
        language: {
            sProcessing:"Procesando...", sLengthMenu:"Mostrar _MENU_ registros",
            sZeroRecords:"No se encontraron resultados", sEmptyTable:"Sin datos",
            sInfo:"Mostrando _START_ al _END_ de _TOTAL_ registros",
            sInfoEmpty:"0 registros", sInfoFiltered:"(filtrado de _MAX_)",
            sSearch:"Buscar:", sLoadingRecords:"Cargando...",
            oPaginate:{sFirst:"Primero",sPrevious:"Anterior",sNext:"Siguiente",sLast:"Último"}
        }
    });
}
</script>

<?php include("pie.php"); ?>