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
                    <div class="tab-content mt-2 mb-3" id="pills-without-border-tabContent">
                        <div class="tab-pane fade show active" id="pills-pagosDiarios" role="tabpanel" aria-labelledby="ventaDiaria">
                            <div class="card text-start">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table
                                            id="TablaVentaDiaria"
                                            class="dataTable display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>N° TICKET</th>
                                                    <th>DÍA</th>
                                                    <th>FECHA</th>
                                                    <th>HORA</th>
                                                    <th>TOTAL Original(S/)</th>
                                                    <th>TOTAL FINAL (S/)</th>
                                                    <th>pérdida</th>
                                                    <th>Accion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sucursal_id = isset($_SESSION['sucursal_id']) ? $_SESSION['sucursal_id'] : null;
                                                foreach (fnListForPagos($sucursal_id) as $datos) {
                                                    $datosJSON = json_encode($datos);
                                                    $ventaJSON = json_decode($datos["js_venta"], true);

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
                                                            <a
                                                                    name=""
                                                                    id=""
                                                                    onclick='abrirModalDetallePago(<?php echo $datosJSON ?>)'
                                                                    class="btn btn-success btn-round btn-round btn-sm"
                                                                    role="button">DETALLE</a>
                                                                <a
                                                                    href="javascript:void(0);"
                                                                    onclick='fn_abrir_pdf(<?php echo $datos["venta_id"] ?>)'
                                                                    class="btn btn-secondary btn-round btn-sm mx-1"
                                                                    role="button" aria-label="PDF">
                                                                    PDF
                                                                </a>
                                                            </div>

                                                        </td>
                                                        
                                                    </tr>

                                                <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-pagoSemanal" role="tabpanel" aria-labelledby="pills-profile-tab-nobd">
                            <div class="card text-start">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table
                                            id="TablaVentaSemanal"
                                            class="dataTable display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>N° TICKET</th>
                                                    <th>DÍA</th>
                                                    <th>FECHA</th>
                                                    <th>HORA</th>
                                                    <th>TOTAL Original(S/)</th>
                                                    <th>TOTAL FINAL (S/)</th>
                                                    <th>pérdida</th>
                                                    <th>Accion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sucursal_id = isset($_SESSION['sucursal_id']) ? $_SESSION['sucursal_id'] : null;
                                                foreach (fnListForPagosSemanales($sucursal_id) as $datos) {
                                                    $datosJSON = json_encode($datos);
                                                    $ventaJSON = json_decode($datos["js_venta"], true);
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
                                                            <a
                                                                    name=""
                                                                    id=""
                                                                    onclick='abrirModalDetallePago(<?php echo $datosJSON ?>)'
                                                                    class="btn btn-success btn-round btn-round btn-sm"
                                                                    role="button">DETALLE</a>
                                                                <a
                                                                    href="javascript:void(0);"
                                                                    onclick='fn_abrir_pdf(<?php echo $datos["venta_id"] ?>)'
                                                                    class="btn btn-secondary btn-round btn-sm mx-1"
                                                                    role="button" aria-label="PDF">
                                                                    PDF
                                                                </a>
                                                            </div>

                                                        </td>
                                                    </tr>

                                                <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-todosLosPagos" role="tabpanel" aria-labelledby="pills-contact-tab-nobd">
                            <div class="card text-start">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table
                                            id="TablaVentaSemanal"
                                            class="dataTable display table table-striped table-hover">
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
                                                    <th>pérdida</th>
                                                    <th>Accion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach (fnListForAllPagos() as $datos) {
                                                    $datosJSON = json_encode($datos);
                                                    $ventaJSON = json_decode($datos["js_venta"], true);
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
                                                            <a
                                                                    name=""
                                                                    id=""
                                                                    onclick='abrirModalDetallePago(<?php echo $datosJSON ?>)'
                                                                    class="btn btn-success btn-round btn-round btn-sm"
                                                                    role="button">DETALLE</a>
                                                                <a
                                                                    href="javascript:void(0);"
                                                                    onclick='fn_abrir_pdf(<?php echo $datos["venta_id"] ?>)'
                                                                    class="btn btn-secondary btn-round btn-sm mx-1"
                                                                    role="button" aria-label="PDF">
                                                                    PDF
                                                                </a>
                                                            </div>

                                                        </td>
                                                    </tr>

                                                <?php
                                                }
                                                ?>
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
<!-- Button trigger modal -->


<!-- Modal Detalle Venta Articulo -->
<style>
    /* Tamaño por defecto para pantallas grandes (computadoras) */
    .modal-dialog-custom {
        max-width: 900px;
        /* Este sería el tamaño 'normal' para computadoras */
        margin: 0 auto;
        /* Centra el modal */
    }

    /* Tamaño para pantallas medianas (tabletas) */
    @media (max-width: 768px) {
        .modal-dialog-custom {
            max-width: 80%;
            /* 80% del ancho de la pantalla en tabletas */
        }
    }

    /* Tamaño para pantallas pequeñas (teléfonos móviles) */
    @media (max-width: 576px) {
        .modal-dialog-custom {
            width: 100%;
            /* Asegura que el modal ocupe todo el ancho disponible en móviles */
            margin: 0 10px;
            /* Da un poco de espacio a los lados en móviles */
            max-width: 100%;
            /* No permite que el modal se haga más grande que el 100% */
        }
    }

    /* Asegura que el contenido del modal no se desborde */
    .modal-content {
        padding: 15px;
        /* Espaciado dentro del modal para que el contenido no esté pegado a los bordes */
    }

    .dataTable {
        overflow-x: auto;
        /* Para permitir desplazamiento horizontal si es necesario */
    }
</style>

<div
    class="modal fade"
    id="modalDetallePago"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-custom" role="document"> <!-- Usamos la clase personalizada aquí -->
        <div class="modal-content">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="card-body">
                <h4 class="card-title text-center" style="font-size: 28px;">Venta de S/ <strong id="idMontoVenta"></strong></h4>
                <hr>
                <div class="card-sub text-center">
                    Aquí podrás revisar los datos de la venta.
                </div>
                <div class="row justify-content-center align-items-center sm-2">
                    <div class="col-sm-6">
                        <div class="card text-start">
                            <div class="card-body">
                                <h4 class="card-title"><i class="fas fa-user"> </i> Cliente</h4>
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
                                <h4 class="card-text"><i class="fas fa-credit-card"> </i> Monto Final: S/ <strong id="idMontoFinalVenta"></strong> </h4>
                                <p>La venta real fue de <strong id="idTotalOriginal"></strong></p>
                                <div><strong>Atendido Por: </strong> <span id="idUsuario">3- USUARIO</span></div>
                                <div><strong>Fecha:</strong> <span id="idFechaVenta"></span></div>
                                <div><strong>Hora:</strong> <span id="idHoraVenta">19:00:00</span></div>
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
                                        <div class="card-sub">
                                            Revisa el detalle de la venta :)
                                        </div>
                                        <div class="table-responsive">
                                            <table
                                                id="tablaDetalle"
                                                class="table table-head-bg-secondary mt-4">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">descripción</th>
                                                        <th scope="col">corte</th>
                                                        <th scope="col">Cant</th>
                                                        <th scope="col">P.Uni</th>
                                                        <th scope="col">Sub Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="">

                                                </tbody>
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
                                        <div>
                                            <table id="tablita" class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th style="border-bottom: 1px solid #000;"><i class="fas fa-credit-card"></i> Pagos</th>
                                                        <th style="border-bottom: 1px solid #000;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="idTbodyTablita">

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
</div>

<script>
    function fn_abrir_pdf(id_venta) {
        window.open("ticket.php?id=" + parseInt(id_venta), "_blank");
    }
    function abrirModalDetallePago(jsonDatos) {
        $('#modalDetallePago').modal('show');
        console.log(jsonDatos);
        ///////////////////
        var js_venta = JSON.parse(jsonDatos["js_venta"])
        console.log(js_venta);
        ///////////////////
        document.getElementById("nombreCliente").innerText = js_venta["cliente"];
        document.getElementById("docCliente").innerText = js_venta["numero_doc_cliente"];
        document.getElementById("numCelCliente").innerText = js_venta["telefonomovil_cliente"];
        document.getElementById("emailCliente").innerText = js_venta["email_cliente"];

        document.getElementById("idMontoVenta").innerText = (js_venta["monto_venta_final"]).toFixed(2);
        document.getElementById("idMontoFinalVenta").innerText = js_venta["monto_venta_final"].toFixed(2);
        document.getElementById("idTotalOriginal").innerText = "S/ " + parseFloat(js_venta["total"]).toFixed(2);
        document.getElementById("idFechaVenta").innerText = js_venta["fecha"];
        document.getElementById("idHoraVenta").innerText = js_venta["hora"];
        document.getElementById("idUsuario").innerText = js_venta["usuario"];

        /////////////////////////////
        var list_js_detalle_venta = JSON.parse(jsonDatos["js_detalle_venta"])
        var tabla = document.getElementById("tablaDetalle").getElementsByTagName("tbody")[0];
        tabla.innerHTML = '';

        for (let i = 0; i < list_js_detalle_venta.length; i++) {
            let articulo = list_js_detalle_venta[i];
            let nuevaFila = tabla.insertRow();
            console.log(articulo);
            let min = articulo["minutos"] !== null ? articulo["minutos"] : '';


            //nuevaFila.insertCell(0).textContent = articulo["minutos"] !== null ? articulo["minutos"] : '-'; // Minutos            
            //nuevaFila.insertCell(1).textContent = articulo["costo_por_minuto"] !== null ? articulo["costo_por_minuto"] : '-'; // Costo x Minuto
            let totalCorte = (articulo["minutos"] === null && articulo["costo_por_minuto"] === null) ?
                '-' : // Si ambos son null, mostramos una línea
                (articulo["minutos"] && articulo["costo_por_minuto"]) ?
                (articulo["costo_por_minuto"] * articulo["minutos"]) : articulo["sub_total"] || '-';

            let totalCorteRedondeado = (totalCorte !== '-') ? "S/ " + (totalCorte.toFixed(2)) : totalCorte;
            let texto = "";
            if (articulo["minutos"] !== null || articulo["costo_por_minuto"] !== null) {
                texto = articulo["descripcion_articulo"] + "\n" + "<span style='color:blue'> <b>[" + min + " Minutos X " + articulo["costo_por_minuto"] + " = " + totalCorte.toFixed(2) + "]</b></span>";

            } else {
                texto = articulo["descripcion_articulo"];
            }
            texto = articulo["descripcion_articulo"];

            nuevaFila.insertCell(0).innerHTML = texto;
            nuevaFila.insertCell(1).textContent = totalCorteRedondeado;
            nuevaFila.insertCell(2).textContent = articulo["cantidad"] || '-';
            nuevaFila.insertCell(3).textContent = articulo["precio_unitario_articulo"] != null ? "S/ " + articulo["precio_unitario_articulo"] : '-';
            nuevaFila.insertCell(4).textContent = "S/ " + articulo["sub_total"] || '-';
        }
        var lis_pagos = JSON.parse(jsonDatos["js_detalle_forma_pago"]);

        /////// PAGOSSS
        var tableRowsPagos = '';
        lis_pagos.forEach(function(pago) {
            tableRowsPagos += `
                            <tr style="border-bottom: 1px solid #000;">
                                <td><strong style='color:${pago.COLOR}'>${pago.FORMA_PAGO}</strong></td>
                                <td><strong> S/ ${pago.MONTO.toFixed(2)} </strong></td>
                            </tr>
                        `;
        });
        document.getElementById("idTbodyTablita").innerHTML = tableRowsPagos;

    }
</script>





<!-- Incluir el CSS de DataTables -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">

<!-- Incluir jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- Incluir el JS de DataTables -->
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {

        fnDataTables();
    });

    function fnDataTables() {
        $(".dataTable").DataTable({
            "order": [
                [0, 'desc']
            ],
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



<?php
include("pie.php");
?>