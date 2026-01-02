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
                <h4 class="card-title"><i class="fas fa-align-left"></i> Listado de Ventas</h4>
                <div class="card-sub">
                    Selecciona de acuerdo a las ventas que necesites :)
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills nav-secondary nav-pills-no-bd" id="pills-tab-without-border" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="ventaDiaria" data-bs-toggle="pill" href="#pills-ventaDiaria" role="tab" aria-controls="pills-ventaDiaria" aria-selected="true"><i class="fas fa-clock"></i> Ventas del Día</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="ventaSemanal" data-bs-toggle="pill" href="#pills-ventaSemanal" role="tab" aria-controls="pills-ventaSemanal" aria-selected="false"><i class="fas fa-calendar-alt"></i> Ventas de la Semana</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-contact-tab-nobd" data-bs-toggle="pill" href="#pills-contact-nobd" role="tab" aria-controls="pills-contact-nobd" aria-selected="false"><i class="fas fa-chart-bar"></i> Todas las Ventas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="ventaRango" data-bs-toggle="pill" href="#pills-ventaRango" role="tab" aria-controls="pills-ventaRango" aria-selected="false"><i class="fas fa-calendar-week"></i> Ventas por Rango</a>
                        </li>
                    </ul>
                    
                    <div class="tab-content mt-2 mb-3" id="pills-without-border-tabContent">
                        <!-- VENTAS DEL DÍA -->
                        <div class="tab-pane fade show active" id="pills-ventaDiaria" role="tabpanel" aria-labelledby="ventaDiaria">
                            <div class="card text-start">
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <strong>Total del Día:</strong> S/ <span id="totalDiario">0.00</span>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="TablaVentaDiaria" class="dataTable display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>N° Ticket</th>
                                                    <th>Cliente</th>
                                                    <th>DÍA</th>
                                                    <th>FECHA</th>
                                                    <th>HORA</th>
                                                    <th>TOTAL(S/)</th>
                                                    <th>TOTAL FINAL (S/)</th>
                                                    <th>pérdida</th>
                                                    <th>ESTADO</th>
                                                    <th>Accion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sucursal_id = isset($_SESSION['sucursal_id']) ? $_SESSION['sucursal_id'] : null;
                                                $totalDiario = 0;
                                                foreach (fnListForVentasDiarias($sucursal_id) as $datos) {
                                                    $datos['accion_ajax'] = 'DETALLEVENTA_VENTA_ID';
                                                    $datosJSON = json_encode($datos);
                                                    $totalDiario += floatval($datos["monto_venta_final"]);
                                                ?>
                                                    <tr>
                                                        <td><?php echo $datos["venta_id"] ?></td>
                                                        <td><?php echo $datos["codigo_tiket"] ?></td>
                                                        <td><?php echo $datos["cliente"] ?></td>
                                                        <td><?php echo $datos["dia_nombre"] ?></td>
                                                        <td><?php echo $datos["fecha"] ?></td>
                                                        <td><?php echo $datos["hora"] ?></td>
                                                        <td><?php echo "S/ " . $datos["total"] ?></td>
                                                        <td><?php echo "S/ " . $datos["monto_venta_final"] ?></td>
                                                        <td><?php echo "S/ " . $datos["perdida_utilidad"] ?></td>
                                                        <td><?php echo $datos["estado_pago"] ?></td>
                                                        <td>
                                                            <div class="mt-2 text-center d-flex justify-content-center">
                                                                <a onclick='abrirModalDetalle(<?php echo $datosJSON ?>)' class="btn btn-success btn-sm btn-round" role="button">DETALLE</a>
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

                        <!-- VENTAS DE LA SEMANA -->
                        <div class="tab-pane fade" id="pills-ventaSemanal" role="tabpanel" aria-labelledby="pills-profile-tab-nobd">
                            <div class="card text-start">
                                <div class="card-body">
                                    <div class="alert alert-success">
                                        <strong>Total de la Semana:</strong> S/ <span id="totalSemanal">0.00</span>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="TablaVentaSemanal" class="dataTable display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>N° Ticket</th>
                                                    <th>Cliente</th>
                                                    <th>DÍA</th>
                                                    <th>FECHA</th>
                                                    <th>HORA</th>
                                                    <th>TOTAL(S/)</th>
                                                    <th>TOTAL FINAL (S/)</th>
                                                    <th>pérdida</th>
                                                    <th>ESTADO</th>
                                                    <th>Accion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sucursal_id = isset($_SESSION['sucursal_id']) ? $_SESSION['sucursal_id'] : null;
                                                $totalSemanal = 0;
                                                foreach (fnListForVentasSemanales($sucursal_id) as $datos) {
                                                    $datos['accion_ajax'] = 'DETALLEVENTA_VENTA_ID';
                                                    $datosJSON = json_encode($datos);
                                                    $totalSemanal += floatval($datos["monto_venta_final"]);
                                                ?>
                                                    <tr>
                                                        <td><?php echo $datos["venta_id"] ?></td>
                                                        <td><?php echo $datos["codigo_tiket"] ?></td>
                                                        <td><?php echo $datos["cliente"] ?></td>
                                                        <td><?php echo $datos["dia_nombre"] ?></td>
                                                        <td><?php echo $datos["fecha"] ?></td>
                                                        <td><?php echo $datos["hora"] ?></td>
                                                        <td><?php echo "S/ " . $datos["total"] ?></td>
                                                        <td><?php echo "S/ " . $datos["monto_venta_final"] ?></td>
                                                        <td><?php echo "S/ " . $datos["perdida_utilidad"] ?></td>
                                                        <td><?php echo $datos["estado_pago"] ?></td>
                                                        <td>
                                                            <div class="mt-2 text-center d-flex justify-content-center">
                                                                <a onclick='abrirModalDetalle(<?php echo $datosJSON ?>)' class="btn btn-success btn-sm btn-round" role="button">DETALLE</a>
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

                        <!-- TODAS LAS VENTAS -->
                        <div class="tab-pane fade" id="pills-contact-nobd" role="tabpanel" aria-labelledby="pills-contact-tab-nobd">
                            <div class="card text-start">
                                <div class="card-body">
                                    <div class="alert alert-primary">
                                        <strong>Total General:</strong> S/ <span id="totalGeneral">0.00</span>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="TablaVentaGeneral" class="dataTable display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>N° Ticket</th>
                                                    <th>Cliente</th>
                                                    <th>DÍA</th>
                                                    <th>FECHA</th>
                                                    <th>HORA</th>
                                                    <th>TOTAL(S/)</th>
                                                    <th>TOTAL FINAL (S/)</th>
                                                    <th>pérdida</th>
                                                    <th>ESTADO</th>
                                                    <th>Accion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sucursal_id = isset($_SESSION['sucursal_id']) ? $_SESSION['sucursal_id'] : null;
                                                $totalGeneral = 0;
                                                foreach (fnListForVentasTodasLasVentas($sucursal_id) as $datos) {
                                                    $datos['accion_ajax'] = 'DETALLEVENTA_VENTA_ID';
                                                    $datosJSON = json_encode($datos);
                                                    $totalGeneral += floatval($datos["monto_venta_final"]);
                                                ?>
                                                    <tr>
                                                        <td><?php echo $datos["venta_id"] ?></td>
                                                        <td><?php echo $datos["codigo_tiket"] ?></td>
                                                        <td><?php echo $datos["cliente"] ?></td>
                                                        <td><?php echo $datos["dia_nombre"] ?></td>
                                                        <td><?php echo $datos["fecha"] ?></td>
                                                        <td><?php echo $datos["hora"] ?></td>
                                                        <td><?php echo "S/ " . $datos["total"] ?></td>
                                                        <td><?php echo "S/ " . $datos["monto_venta_final"] ?></td>
                                                        <td><?php echo "S/ " . $datos["perdida_utilidad"] ?></td>
                                                        <td><?php echo $datos["estado_pago"] ?></td>
                                                        <td>
                                                            <div class="mt-2 text-center d-flex justify-content-center">
                                                                <a onclick='abrirModalDetalle(<?php echo $datosJSON ?>)' class="btn btn-success btn-sm btn-round" role="button">DETALLE</a>
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

                        <!-- VENTAS POR RANGO DE FECHAS -->
                        <div class="tab-pane fade" id="pills-ventaRango" role="tabpanel" aria-labelledby="ventaRango">
                            <div class="card text-start">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="fechaInicio">Fecha Inicio:</label>
                                            <input type="date" id="fechaInicio" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="fechaFin">Fecha Fin:</label>
                                            <input type="date" id="fechaFin" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label>&nbsp;</label>
                                            <button onclick="filtrarPorRango()" class="btn btn-primary form-control">
                                                <i class="fas fa-search"></i> Buscar
                                            </button>
                                        </div>
                                    </div>
                                    <div class="alert alert-warning" id="alertRango" style="display:none;">
                                        <strong>Total del Rango:</strong> S/ <span id="totalRango">0.00</span>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="TablaVentaRango" class="dataTable display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>N° Ticket</th>
                                                    <th>Cliente</th>
                                                    <th>DÍA</th>
                                                    <th>FECHA</th>
                                                    <th>HORA</th>
                                                    <th>TOTAL(S/)</th>
                                                    <th>TOTAL FINAL (S/)</th>
                                                    <th>pérdida</th>
                                                    <th>ESTADO</th>
                                                    <th>Accion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
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

<!-- Modal Detalle Venta -->
<style>
    .modal-dialog-custom {
        max-width: 900px;
        margin: 0 auto;
    }
    @media (max-width: 768px) {
        .modal-dialog-custom {
            max-width: 80%;
        }
    }
    @media (max-width: 576px) {
        .modal-dialog-custom {
            width: 100%;
            margin: 0 10px;
            max-width: 100%;
        }
    }
    .modal-content {
        padding: 15px;
    }
    .dataTable {
        overflow-x: auto;
    }
</style>

<div class="modal fade" id="modalDetalleVenta" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-custom" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="card-body">
                <h4 class="card-title text-center" style="font-size: 28px;">Venta de S/ <strong id="idMontoVenta"></strong></h4>
                <hr>
                <p class="card-text text-center" id="idUtilidad"></p>
                <div class="card-sub text-center">
                    Aquí podrás revisar los datos de la venta.
                </div>
                <div class="row justify-content-center align-items-center sm-2">
                    <div class="col-sm-6">
                        <div class="card text-start">
                            <div class="card-body" style="color:indigo">
                                <h4 class="card-title" style="color:indigo"><i class="fas fa-user"></i> Cliente</h4>
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
                                <h4 class="card-text" style="color:green"><i class="fas fa-credit-card"></i> Monto Final: S/ <strong id="idMontoFinalVenta"></strong></h4>
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
                        <div class="card-title">Detalle de Venta</div>
                        <div class="card-sub">Revisa el detalle de la venta :)</div>
                        <div class="table-responsive">
                            <table id="tablaDetalle" class="table table-head-bg-secondary mt-4">
                                <thead>
                                    <tr>
                                        <th scope="col">descripción</th>
                                        <th scope="col">corte</th>
                                        <th scope="col">Cant</th>
                                        <th scope="col">P.Uni</th>
                                        <th scope="col">Sub Total</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
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

<script>
function fn_abrir_pdf(id_venta) {
    window.open("ticket.php?id=" + parseInt(id_venta), "_blank");
}

function abrirModalDetalle(datosJsonVenta) {
    $('#modalDetalleVenta').modal('show');
    document.getElementById("nombreCliente").innerText = datosJsonVenta.cliente;
    document.getElementById("docCliente").innerText = datosJsonVenta.numero_doc_cliente;
    document.getElementById("numCelCliente").innerText = datosJsonVenta.telefonomovil_cliente;
    document.getElementById("emailCliente").innerText = datosJsonVenta.email_cliente;
    document.getElementById("idMontoVenta").innerText = datosJsonVenta.monto_venta_final;
    document.getElementById("idMontoFinalVenta").innerText = datosJsonVenta.monto_venta_final;
    document.getElementById("idTotalOriginal").innerText = "S/ " + datosJsonVenta.total;
    document.getElementById("idFechaVenta").innerText = datosJsonVenta.fecha;
    document.getElementById("idHoraVenta").innerText = datosJsonVenta.hora;
    document.getElementById("idUsuario").innerText = datosJsonVenta.usuario;

    if (datosJsonVenta.perdida_utilidad < 0) {
        document.getElementById("idUtilidad").innerHTML = "<span style='color:red'> En esta venta, PERDISTE un margen de utilidad de <strong>S/" + (parseFloat(datosJsonVenta.perdida_utilidad) * -1.00).toFixed(2) + ".</strong> </span>";
    } else {
        if (datosJsonVenta.estado_pago === "CREDITO" && datosJsonVenta.estado_final === "VENTA REALIZADA AL CREDITO - AUN DEBE") {
            document.getElementById("idUtilidad").innerHTML = "<b>" + datosJsonVenta.estado_final + "</b><br> <span style='color:green'>  En esta venta fue realizada a CRÉDITO. Tiene abonado S/ " + datosJsonVenta.acumulado_deuda + " </span> <span style='color:orange'><br><strong>Para más información, Revisar en la seccion de Crédito [Historial Clientes] </span>";
        } else if (datosJsonVenta.estado_pago === "CREDITO" && datosJsonVenta.estado_final === "PAGADO - CREDTIO") {
            document.getElementById("idUtilidad").innerHTML = "<b>" + datosJsonVenta.estado_final + "</b><br><span style='color:orange'> En esta venta fue realizada a CRÉDITO. <strong style = 'color:green'>Pagó su DEUDA</span>";
        } else {
            document.getElementById("idUtilidad").innerHTML = "<span style='color:green'> <b> En esta venta, no hiciste rebajas :)</b> </span>";
        }
    }

    $.ajax({
        url: 'logica/clssConsultas.php',
        type: 'POST',
        data: {
            accion: datosJsonVenta.accion_ajax,
            venta_id: datosJsonVenta.venta_id
        },
        dataType: 'json',
        success: function(datosArticulo) {
            var tabla = document.getElementById("tablaDetalle").getElementsByTagName("tbody")[0];
            tabla.innerHTML = '';
            for (let i = 0; i < datosArticulo.length; i++) {
                let articulo = datosArticulo[i];
                let nuevaFila = tabla.insertRow();
                let min = articulo["minutos"] !== null ? articulo["minutos"] : '';
                let totalCorte = (articulo["minutos"] === null && articulo["costo_por_minuto"] === null) ? '-' : 
                    (articulo["minutos"] && articulo["costo_por_minuto"]) ? 
                    (articulo["costo_por_minuto"] * articulo["minutos"]) : articulo["sub_total"] || '-';
                let totalCorteRedondeado = (totalCorte !== '-') ? "S/ " + (totalCorte) : totalCorte;
                let texto = articulo["descripcion"];
                nuevaFila.insertCell(0).innerHTML = texto;
                nuevaFila.insertCell(1).textContent = totalCorteRedondeado;
                nuevaFila.insertCell(2).textContent = articulo["cantidad"] || '-';
                nuevaFila.insertCell(3).textContent = articulo["precio_unitario_articulo"] != null ? "S/ " + articulo["precio_unitario_articulo"] : '-';
                nuevaFila.insertCell(4).textContent = "S/ " + articulo["sub_total"] || '-';
            }
        },
        error: function(xhr, status, error) {
            console.error("Error al obtener los detalles de la venta:", error);
        }
    });
}

function filtrarPorRango() {
    var fechaInicio = document.getElementById('fechaInicio').value;
    var fechaFin = document.getElementById('fechaFin').value;
    
    if (!fechaInicio || !fechaFin) {
        alert('Por favor selecciona ambas fechas');
        return;
    }
    
    if (fechaInicio > fechaFin) {
        alert('La fecha de inicio no puede ser mayor que la fecha fin');
        return;
    }
    
    $.ajax({
        url: 'logica/clssConsultas.php',
        type: 'POST',
        data: {
            accion: 'VENTAS_POR_RANGO',
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin
        },
        dataType: 'json',
        success: function(datos) {
            if ($.fn.DataTable.isDataTable('#TablaVentaRango')) {
                $('#TablaVentaRango').DataTable().destroy();
            }
            
            $('#TablaVentaRango tbody').empty();
            
            var total = 0;
            var tbody = '';
            
            datos.forEach(function(dato) {
                total += parseFloat(dato.monto_venta_final);
                dato.accion_ajax = 'DETALLEVENTA_VENTA_ID';
                var datosJSON = JSON.stringify(dato);
                
                tbody += '<tr>' +
                    '<td>' + dato.venta_id + '</td>' +
                    '<td>' + dato.codigo_tiket + '</td>' +
                    '<td>' + dato.cliente + '</td>' +
                    '<td>' + dato.dia_nombre + '</td>' +
                    '<td>' + dato.fecha + '</td>' +
                    '<td>' + dato.hora + '</td>' +
                    '<td>S/ ' + dato.total + '</td>' +
                    '<td>S/ ' + dato.monto_venta_final + '</td>' +
                    '<td>S/ ' + dato.perdida_utilidad + '</td>' +
                    '<td>' + dato.estado_pago + '</td>' +
                    '<td>' +
                        '<div class="mt-2 text-center d-flex justify-content-center">' +
                            '<a onclick=\'abrirModalDetalle(' + datosJSON + ')\' class="btn btn-success btn-sm btn-round" role="button">DETALLE</a>' +
                            '<a href="javascript:void(0);" onclick="fn_abrir_pdf(' + dato.venta_id + ')" class="btn btn-secondary btn-round btn-sm mx-1" role="button">PDF</a>' +
                        '</div>' +
                    '</td>' +
                '</tr>';
            });
            
            $('#TablaVentaRango tbody').html(tbody);
            
            $('#TablaVentaRango').DataTable({
                "order": [[0, 'desc']],
                language: {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "Ningún dato disponible en esta tabla",
                    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sSearch": "Buscar:",
                    "sUrl": "",
                    "sInfoThousands": ",",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sPrevious": "Anterior",
                        "sNext": "Siguiente",
                        "sLast": "Último"
                    },
                    "oAria": {
                        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    }
                }
            });
            
            document.getElementById('totalRango').innerText = total.toFixed(2);
            document.getElementById('alertRango').style.display = 'block';
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
            console.error("Respuesta:", xhr.responseText);
            alert('Error al cargar las ventas. Revisa la consola para más detalles.');
        }
    });
}$(document).ready(function() {
    document.getElementById('totalDiario').innerText = '<?php echo number_format($totalDiario, 2); ?>';
    document.getElementById('totalSemanal').innerText = '<?php echo number_format($totalSemanal, 2); ?>';
    document.getElementById('totalGeneral').innerText = '<?php echo number_format($totalGeneral, 2); ?>';
    
    fnDataTables();
});

function fnDataTables() {
    $(".dataTable").DataTable({
        "order": [[0, 'desc']],
        language: {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sPrevious": "Anterior",
                "sNext": "Siguiente",
                "sLast": "Último"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    });
}
</script>

<?php include("pie.php"); ?>