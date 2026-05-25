<?php
include("cabecera.php");
$sucursal_id = isset($_SESSION['sucursal_id']) ? (int)$_SESSION['sucursal_id'] : null;

?>

<div class="container">
    <div class="page-inner">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title"> <i class="fas fa-box-open"></i> Caja Chica </h4>
                <hr>
                <ul class="nav nav-pills nav-secondary nav-pills-no-bd" id="pills-tab-without-border" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-home-tab-nobd" data-bs-toggle="pill" href="#pills-home-nobd" role="tab" aria-controls="pills-home-nobd" aria-selected="true">
                            <i class="fas fa-calculator"></i> Caja Chica
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-profile-tab-nobd" data-bs-toggle="pill" href="#pills-profile-nobd" role="tab" aria-controls="pills-profile-nobd" aria-selected="false">
                            <i class="fas fa-align-left"></i> Historial de Caja
                        </a>
                    </li>
                </ul>

                <div class="tab-content mt-2 mb-3" id="pills-without-border-tabContent">

                    <!-- TAB: CAJA CHICA ACTIVA -->
                    <div class="tab-pane fade show active" id="pills-home-nobd" role="tabpanel" aria-labelledby="pills-home-tab-nobd">
                        <?php
                        // ✅ FILTRO POR SUCURSAL
                        if (empty(fnListadoCajaChica($sucursal_id))) {
                        ?>
                            <div class="card-sub card-annoucement card-round">
                                <div class="card-body text-center">
                                    <h3 class="card-title">
                                        <strong>
                                            Hola <?php echo $nombre; ?> 🖐️
                                        </strong>
                                    </h3>                                    <div class="card-desc">
                                        No tienes ninguna <strong>caja aperturada</strong> 😅. Puedes aperturar una caja chica haciendo clic en el botón <strong>Apertura de Caja Chica</strong> 😎.
                                    </div>
                                    <div class="card-detail">
                                        <a onclick='fnAbrirModalAperturaCaja()' class="btn btn-secondary btn-round" role="button">
                                            <i class="fas fa-box-open"></i> Apertura de Caja Chica
                                        </a>
                                    </div>
                                </div>
                            </div>

                        <?php
                        } else {
                            // ✅ FILTRO POR SUCURSAL
                            $cajaActiva = fnListadoCajaChica($sucursal_id)[0];
                        ?>
                            <div class="row justify-content-center align-items-start g-2">

                                <!-- Columna Info Caja -->
                                <div class="col-12 col-sm-6 col-md-6 col-xl-5" style="position: sticky; top: 0; z-index: 10;">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h4 class="text-dark fw-bold">Caja Chica</h4>
                                                    <h6 class="text-secondary fw-bold">
                                                        Monto Inicial fue de S/<?php echo $cajaActiva["monto"]; ?>
                                                    </h6>
                                                    <p class="text-muted">Esta caja se encuentra aperturada</p>
                                                    <p class="text-muted">Aperturada por <strong><?php echo $cajaActiva["responsable"]; ?></strong></p>
                                                </div>
                                                <h3 class="text-success fw-bold">S/<?php echo $cajaActiva["saldo_v2"]; ?></h3>
                                                <div class="dropdown-secondary">
                                                    <button
                                                        class="btn btn-sm btn-label-secondary dropdown-toggle btn-round"
                                                        type="button"
                                                        id="dropdownMenuButton"
                                                        data-bs-toggle="dropdown"
                                                        aria-haspopup="true"
                                                        aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item" href="#" onclick="fnAbrirModalRegistroDeEgresoCajaChica()">
                                                            <i class="fas fa-caret-right"></i> Registro de Egreso de caja
                                                        </a>
                                                        <a class="dropdown-item" href="#" onclick="fnAbrirModalRegistroDeIngresoCajaChica()">
                                                            <i class="fas fa-caret-right"></i> Registro de Ingreso de caja
                                                        </a>
                                                        <a class="dropdown-item" href="#" onclick="fnAbrirSwasCierreCaja()">
                                                            <i class="fas fa-caret-right"></i> Cierre de Caja
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-success"
                                                    role="progressbar"
                                                    aria-valuenow="<?php echo $cajaActiva["porcentaje"]; ?>"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100"
                                                    style="width: <?php echo $cajaActiva["porcentaje"]; ?>%;">
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-2">
                                                <p class="text-muted mb-0">Porcentaje del gasto realizado en caja chica.</p>
                                                <p class="text-muted mb-0"><strong><?php echo $cajaActiva["porcentaje"]; ?>%</strong></p>
                                            </div>
                                            <hr>
                                            <div class="row justify-content-center align-items-center sm-2">
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <small class="form-text text-muted"><strong>Fecha de Apertura</strong></small>
                                                        <input type="text" disabled class="form-control" value="<?php echo $cajaActiva["fecha_apertura"]; ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <small class="form-text text-muted"><strong>Hora de Apertura</strong></small>
                                                        <input type="text" disabled class="form-control" value="<?php echo $cajaActiva["hora_apertura"]; ?>" />
                                                    </div>
                                                </div>
                                            </div>

                                            <div style="display:none;"><strong>ID:</strong> <span id="idCaja_id"><?php echo $cajaActiva["id"]; ?></span></div>
                                            <div><strong>Monto de Apertura: <span style="color:green;">S/ <?php echo $cajaActiva["monto"]; ?></span></strong></div>
                                            <div><strong>Saldo de caja: <span style="color:blue;">S/ <span id="idMontoSaldo"><?php echo $cajaActiva["saldo_v2"]; ?></span></span></strong></div>
                                            <div><strong>Egresos de Caja: <span style="color:red;">S/ <?php echo $cajaActiva["egresos_de_caja"]; ?></span></strong></div>
                                            <div><strong>Porcentaje de Gasto:</strong> <?php echo $cajaActiva["porcentaje"]; ?>%</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Columna Egresos -->
                                <div class="col-12 col-sm-6 col-md-6 col-xl-7">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-title" style="display:flex; justify-content:space-between; align-items:center;">
                                                <span>Egresos de Caja Chica</span>
                                            </div>
                                            <ol class="activity-feed">
                                                <?php
                                                $datosDetalleCaja = ($cajaActiva["js_detalle_caja"] !== null)
                                                    ? json_decode($cajaActiva["js_detalle_caja"], true)
                                                    : null;

                                                if (empty($datosDetalleCaja)) {
                                                ?>
                                                    <div>Sin Registros de caja</div>
                                                <?php
                                                } else {
                                                    foreach ($datosDetalleCaja as $datos) { ?>
                                                        <li class="feed-item feed-item-secondary">
                                                            <time class="date"><?php echo $datos["hora_registro"]; ?></time>
                                                            <div>
                                                                <?php
                                                                $color = ($datos["tipo_movimiento"] == 'EGRESO') ? 'orange' : 'green';
                                                                echo '<strong style="color:' . $color . ';">' . $datos["tipo_movimiento"] . '</strong> - ' . $datos["concepto"];
                                                                ?>
                                                            </div>
                                                            <div class="text-secondary">Registrado por: <b><?php echo $datos["responsable"]; ?></b></div>
                                                            <div>Monto: <strong>S/ <?php echo number_format($datos["monto"], 2); ?></strong></div>
                                                        </li>
                                                    <?php
                                                    }
                                                }
                                                ?>
                                            </ol>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        <?php } ?>
                    </div>

                    <!-- TAB: HISTORIAL DE CAJA -->
                    <div class="tab-pane fade" id="pills-profile-nobd" role="tabpanel" aria-labelledby="pills-profile-tab-nobd">
                        <div class="card text-start">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="TablaVentaDiaria" class="dataTable display table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Responsable</th>
                                                <th>Día de semana</th>
                                                <th>F. Apertura</th>
                                                <th>Hora de Apertura</th>
                                                <th>F. Cierre</th>
                                                <th>Hora de Cierre</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // ✅ FILTRO POR SUCURSAL
                                            foreach (fnListadoCajaChicaCerradas($sucursal_id) as $datos) {
                                                $datosJSON = json_encode($datos);
                                            ?>
                                                <tr>
                                                    <td><?php echo $datos["id"]; ?></td>
                                                    <td><?php echo $datos["responsable"]; ?></td>
                                                    <td><?php echo $datos["dia_semana"]; ?></td>
                                                    <td><?php echo $datos["fecha_apertura"]; ?></td>
                                                    <td><?php echo $datos["hora_apertura"]; ?></td>
                                                    <td><?php echo $datos["fecha_cierre"]; ?></td>
                                                    <td><?php echo $datos["hora_cierre"]; ?></td>
                                                    <td>
                                                        <div class="mt-2 text-center">
                                                            <a onclick='abrirDetalleCajaChica(<?php echo $datosJSON; ?>)'
                                                               class="btn btn-secondary btn-round btn-sm" role="button">
                                                                <i class="fas fa-external-link-square-alt"></i>
                                                            </a>
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

                </div>
            </div>
        </div>
    </div>
</div>


<!-- MODAL: APERTURA DE CAJA -->
<div class="modal fade" id="modalAperturarCaja" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-custom" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="card-body">
                    <h4 class="card-title text-center" style="font-size:28px;">
                        <i class="fas fa-box-open"></i> Apertura de Caja Chica
                    </h4>
                    <hr>
                    <div class="card-sub text-center">
                        Aquí podrás registrar la apertura de caja. Ten en cuenta el monto correspondiente.
                    </div>
                    <div class="row justify-content-center align-items-center sm-2">
                        <div class="col-sm-12">
                            <div class="card text-start">
                                <div class="card-body">
                                    <h4 class="card-title">Monto</h4>
                                    <div class="mb-3">
                                        <input type="number" class="form-control" id="idMontoAperturaCajaChica" placeholder="" />
                                        <small class="form-text text-muted">Este será el monto para tu caja chica.</small>
                                    </div>
                                    <div class="card-sub text-center">
                                        Recuerda que el monto máximo para cada adquisición con cargo a la Caja Chica no debe exceder del 10% de una UIT.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <a class="btn btn-success btn-round" onclick='fnRegistrarAperturaDeCaja()' role="button">
                                Aperturar Caja <i class="fas fa-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- MODAL: EGRESO -->
<div class="modal fade" id="modalRegistrarEgresoDeCajaCHica" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-custom" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="card-body">
                    <h6 class="card-title text-center" style="font-size:20px;">
                        <i class="fas fa-boxes"></i> Registro de Egreso de Caja Chica
                    </h6>
                    <div class="card-sub text-center">
                        Aquí podrás registrar los <strong>EGRESOS</strong> de Caja Chica.
                    </div>
                    <div class="row justify-content-center align-items-center sm-2">
                        <div class="card-title text-center" style="color:green;">
                            Saldo Disponible: S/ <span id="montoSaldoDisponible">0.00</span>
                        </div>
                        <div class="col-sm-12">
                            <div class="card text-start">
                                <div class="card-body">
                                    <div class="row justify-content-center align-items-center g-2">
                                        <div class="col-12 col-sm-6">
                                            <div class="mb-3">
                                                <label for="idSelectConceptoEgreso" class="form-label">
                                                    <strong><i class="fas fa-angle-down"></i> Concepto</strong>
                                                </label>
                                                <select class="form-select form-select-md w-100" id="idSelectConceptoEgreso">
                                                    <option selected>Seleccione Concepto</option>
                                                    <?php
                                                    // ✅ FILTRO POR SUCURSAL
                                                    foreach (fnListadoConceptosEgresos("C", $sucursal_id) as $datos) { ?>
                                                        <option value="<?php echo $datos["id"]; ?>">
                                                            <?php echo $datos["titulo"]; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <div class="mb-3">
                                                <label for="idMontoCajaChica" class="form-label">
                                                    <strong>Monto (S/) de Egreso</strong>
                                                </label>
                                                <input type="number" class="form-control form-control-md w-100" id="idMontoCajaChica" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><strong><i class="fas fa-sticky-note"></i> Nota</strong></label>
                                        <textarea class="form-control" id="idDetalleNotaCajaChica" rows="3"
                                            placeholder="Ej: Pago de Luz, Pasajes, etc."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <a class="btn btn-success btn-round" onclick='fnRegistrarEgresoCajaChica()' role="button">
                            Registrar Egreso <i class="fas fa-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- MODAL: INGRESO -->
<div class="modal fade" id="modalRegistrarIngresoDeCajaCHica" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-custom" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="card-body">
                    <h6 class="card-title text-center" style="font-size:20px;">
                        <i class="fas fa-boxes"></i> Registro de Ingreso de Caja Chica
                    </h6>
                    <div class="card-sub text-center">
                        Aquí podrás registrar los <strong>INGRESOS</strong> de Caja Chica.
                    </div>
                    <div class="row justify-content-center align-items-center sm-2">
                        <div class="card-title text-center" style="color:green;">
                            Saldo Disponible: S/ <span id="montoSaldoDisponibleIngreso">0.00</span>
                        </div>
                        <div class="col-sm-12">
                            <div class="card text-start">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="idMontoIngresoCajaChica" class="form-label">
                                            <strong>Monto (S/) de Ingreso</strong>
                                        </label>
                                        <input type="number" class="form-control form-control-md w-100" id="idMontoIngresoCajaChica" />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><strong><i class="fas fa-sticky-note"></i> Nota</strong></label>
                                        <textarea class="form-control" id="idIngresoDetalleNotaCajaChica" rows="3"
                                            placeholder="Ej: Vuelto de tienda, sencillo, etc."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <a class="btn btn-success btn-round" onclick='fnRegistrarIngresoDeCaja()' role="button">
                            Registrar Ingreso <i class="fas fa-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- MODAL: DETALLE CAJA CERRADA -->
<div class="modal fade" id="modalDetalleCajaChica" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-custom" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="card-body">
                    <h4 class="card-title text-center" style="font-size:28px;">
                        <i class="fas fa-box-open"></i> Detalle Caja Chica
                    </h4>
                    <hr>
                    <div class="card text-start">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-box-open"></i> Aperturada con <span style="color:green;" id="idDetMontoApertura"></span>
                            </h6>
                            <hr>
                            <div><strong>Apertura Por: </strong><span id="idResponsable"></span></div>
                            <div><strong>Fecha de Apertura: </strong><span id="idDetFechaApertura"></span></div>
                            <div><strong>Hora de Apertura: </strong><span id="idDetidHoraApertura"></span></div>
                        </div>
                    </div>
                    <div class="card text-start">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-box"></i> Saldo de caja <span style="color:orange;" id="idDetSaldoCaja"></span>
                            </h6>
                            <hr>
                            <div><strong>Fecha de Cierre: </strong><span id="idDetFechaCierre"></span></div>
                            <div><strong>Hora de Cierre: </strong><span id="idDetHoraCierre"></span></div>
                            <div><strong>Egresos de Caja: </strong>S/ <span id="idDetEgresosCaja"></span></div>
                        </div>
                    </div>
                    <div class="card text-start">
                        <div class="card-body">
                            <div class="accordion accordion-flush" id="accordionFlushExample">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="flush-headingOne">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#flush-collapseOne"
                                            aria-expanded="false" aria-controls="flush-collapseOne">
                                            <strong><i class="fas fa-book-reader"></i> Detalle de los Egresos de Caja</strong>
                                        </button>
                                    </h2>
                                    <div id="flush-collapseOne" class="accordion-collapse collapse"
                                        aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                        <div class="accordion-body">
                                            <div class="card-sub">Revisa los <strong>EGRESOS</strong> registrados en caja.</div>
                                            <ul id="idContenidoUlDetalleCaja"></ul>
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

<?php include("pie.php"); ?>


<script>
$(document).ready(function () {
    fnDataTables();
});

function fnDataTables() {
    $(".dataTable").DataTable({
        "order": [[0, 'desc']],
        language: {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sSearch":         "Buscar:",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sPrevious": "Anterior",
                "sNext":     "Siguiente",
                "sLast":     "Último"
            }
        }
    });
}

function fnAbrirModalAperturaCaja() {
    $('#modalAperturarCaja').modal('show');
}

function fnAbrirModalRegistroDeEgresoCajaChica() {
    let montoSaldo = parseFloat(document.getElementById("idMontoSaldo").innerText);
    document.getElementById("montoSaldoDisponible").innerText = montoSaldo.toFixed(2);
    $('#modalRegistrarEgresoDeCajaCHica').modal('show');
}

function fnAbrirModalRegistroDeIngresoCajaChica() {
    let montoSaldo = parseFloat(document.getElementById("idMontoSaldo").innerText);
    document.getElementById("montoSaldoDisponibleIngreso").innerText = montoSaldo.toFixed(2);
    $('#modalRegistrarIngresoDeCajaCHica').modal('show');
}

function abrirDetalleCajaChica(jsonDatos) {
    $('#modalDetalleCajaChica').modal('show');
    document.getElementById("idResponsable").innerText        = jsonDatos["responsable"];
    document.getElementById("idDetFechaApertura").innerText   = jsonDatos["fecha_apertura"];
    document.getElementById("idDetidHoraApertura").innerText  = jsonDatos["hora_apertura"];
    document.getElementById("idDetMontoApertura").innerText   = "S/ " + jsonDatos["monto"];
    document.getElementById("idDetFechaCierre").innerText     = jsonDatos["fecha_cierre"];
    document.getElementById("idDetHoraCierre").innerText      = jsonDatos["hora_cierre"];
    document.getElementById("idDetSaldoCaja").innerText       = "S/ " + jsonDatos["saldo_v2"];
    document.getElementById("idDetEgresosCaja").innerText     = jsonDatos["egresos_de_caja"];

    var js_datos_detalle = JSON.parse(jsonDatos["js_detalle_caja"]);
    var detalleFilas = '';

    if (!js_datos_detalle || Object.keys(js_datos_detalle).length === 0 ||
        Object.values(js_datos_detalle).every(v => v === null || v === "")) {
        detalleFilas = `<li><span style="color:#2a2f5b"><i class="fas fa-clock"></i> Sin Registro de Egresos</span></li>`;
    } else {
        js_datos_detalle.forEach(function(item) {
            let color = item["tipo_movimiento"] === "EGRESO" ? "red" : "green";
            detalleFilas += `
            <li>
                <span style="color:#2a2f5b"><i class="fas fa-clock"></i> ${item["hora_registro"]}</span>
                - <strong><span style="color:${color}">${item["tipo_movimiento"]}</span></strong>
                - ${item["concepto"]}
                <b><span style="color:${color}">[S/ ${parseFloat(item["monto"]).toFixed(2)}]</span></b>
            </li>`;
        });
    }
    document.getElementById("idContenidoUlDetalleCaja").innerHTML = detalleFilas;
}

function fnAbrirSwasCierreCaja() {
    swal({
        title: "¿Estás seguro de que deseas cerrar la caja?",
        type: "warning",
        buttons: {
            cancel: { visible: true, text: "No Cerrar Caja!", className: "btn btn-danger" },
            confirm: { text: "Sí, Cerrar Caja!", className: "btn btn-success" }
        },
        content: {
            element: "div",
            attributes: { innerHTML: `<div style="text-align:center;">Recuerda que después de cerrar la caja, no podrás registrar más egresos hasta su apertura.</div>` }
        }
    }).then((willDelete) => {
        if (willDelete) {
            swal({
                title: "Caja Cerrada",
                icon: "success",
                buttons: { confirm: { className: "btn btn-success" } },
                content: {
                    element: "div",
                    attributes: { innerHTML: `<div style="text-align:center;">¡Caja cerrada con éxito!</div>` }
                }
            }).then(() => {
                $.ajax({
                    url: 'logica/clssInsertPA.php',
                    type: 'POST',
                    data: {
                        accion: 'CIERREDECAJACHICA',
                        caja_id: parseInt(document.getElementById("idCaja_id").innerText)
                    },
                    success: function(response) {
                        console.log("Respuesta del servidor: ", response);
                        location.reload();
                    }
                });
            });
        } else {
            swal({
                buttons: { confirm: { className: "btn btn-success" } },
                content: {
                    element: "div",
                    attributes: { innerHTML: `<div style="text-align:center;">Sigues con la Caja Aperturada.</div>` }
                }
            });
        }
    });
}

function fnRegistrarAperturaDeCaja() {
    var monto = parseFloat(document.getElementById("idMontoAperturaCajaChica").value);

    if (isNaN(monto)) {
        swal("Upps", "Debes ingresar el monto para aperturar caja 😥", {
            icon: "error", buttons: { confirm: { className: "btn btn-danger" } }
        });
        return;
    }

    var jsDatoCaja = {
        "responsable_id": <?php echo $id_usuario_s; ?>,
        "responsable":    "<?php echo $nombre . ', ' . $ape_usuario; ?>",
        "monto":          monto,
        "sucursal_id":    <?php echo $sucursal_id; ?>  // ✅ AGREGADO
    };

    $.ajax({
        url: 'logica/clssInsertPA.php',
        type: 'POST',
        data: { accion: 'APERTURACAJA', jsDatoCaja: JSON.stringify(jsDatoCaja) },
        success: function(response) {
            try {
                var result = JSON.parse(response);
                if (result.estado === true) {
                    swal({ title: "¡Caja Aperturada con Éxito!", text: result.mensaje, icon: "success", buttons: false, timer: 1500 })
                        .then(() => location.reload());
                } else {
                    swal("Error", result.mensaje, { icon: "error", buttons: { confirm: { className: "btn btn-danger" } } });
                }
            } catch(e) {
                swal("Error", "No se pudo procesar la respuesta del servidor.", { icon: "error", buttons: { confirm: { className: "btn btn-danger" } } });
            }
        },
        error: function() {
            swal("Error", "Hubo un problema con la solicitud.", { icon: "error", buttons: { confirm: { className: "btn btn-danger" } } });
        }
    });
}

function fnRegistrarEgresoCajaChica() {
    var conceptoSelect   = document.getElementById("idSelectConceptoEgreso");
    var concepto         = conceptoSelect.selectedIndex === 0 ? 25 : conceptoSelect.value;
    var concepto_egreso  = conceptoSelect.selectedIndex === 0 ? "OTROS EGRESOS" : conceptoSelect.options[conceptoSelect.selectedIndex].text;
    var monto_caja_chica = parseFloat(document.getElementById("idMontoCajaChica").value);
    var nota_cajaChica   = document.getElementById("idDetalleNotaCajaChica").value;
    var montoSaldo       = parseFloat(document.getElementById("idMontoSaldo").innerText);

    if (isNaN(monto_caja_chica)) {
        swal("Upps", "Debes ingresar el monto para registrar el egreso 😥", {
            icon: "error", buttons: { confirm: { className: "btn btn-danger" } }
        });
        return;
    }
    if (monto_caja_chica > montoSaldo) {
        swal("Upps", "El monto ingresado supera el saldo de Caja 😥", {
            icon: "error", buttons: { confirm: { className: "btn btn-danger" } }
        });
        return;
    }

    var jsDetalleCaja = {
        "caja_id":          parseInt(document.getElementById("idCaja_id").innerText),
        "tipo_movimiento":  "EGRESO",
        "responsable_id":   <?php echo $id_usuario_s; ?>,
        "responsable":      "<?php echo $nombre . ', ' . $ape_usuario; ?>",
        "monto_caja_chica": monto_caja_chica,
        "nota_caja_chica":  nota_cajaChica.length === 0 ? null : nota_cajaChica,
        "concepto_id":      concepto,
        "concepto_egreso":  concepto_egreso,
        "sucursal_id":      <?php echo $sucursal_id; ?>  // ✅ AGREGADO
    };

    $.ajax({
        url: 'logica/clssInsertPA.php',
        type: 'POST',
        data: { accion: 'INSERTDETALLECAJACHICA', jsDetalleCaja: JSON.stringify(jsDetalleCaja) },
        success: function(response) {
            try {
                var result = JSON.parse(response);
                if (result.estado === true) {
                    swal({ title: "¡Egreso Registrado con Éxito!", text: result.mensaje, icon: "success", buttons: false, timer: 1500 })
                        .then(() => location.reload());
                } else {
                    swal("Error", result.mensaje, { icon: "error", buttons: { confirm: { className: "btn btn-danger" } } });
                }
            } catch(e) {
                swal("Error", "No se pudo procesar la respuesta.", { icon: "error", buttons: { confirm: { className: "btn btn-danger" } } });
            }
        },
        error: function() {
            swal("Error", "Hubo un problema con la solicitud.", { icon: "error", buttons: { confirm: { className: "btn btn-danger" } } });
        }
    });
}

function fnRegistrarIngresoDeCaja() {
    var nota_cajaChica = document.getElementById("idIngresoDetalleNotaCajaChica").value;

    var jsDetalleCaja = {
        "caja_id":          parseInt(document.getElementById("idCaja_id").innerText),
        "tipo_movimiento":  "INGRESO",
        "responsable_id":   <?php echo $id_usuario_s; ?>,
        "responsable":      "<?php echo $nombre . ', ' . $ape_usuario; ?>",
        "monto_caja_chica": parseFloat(document.getElementById("idMontoIngresoCajaChica").value),
        "nota_caja_chica":  nota_cajaChica.length === 0 ? null : nota_cajaChica,
        "concepto_id":      1,
        "concepto_egreso":  "INGRESO DE DINERO A CAJA",
        "sucursal_id":      <?php echo $sucursal_id; ?>  // ✅ AGREGADO
    };

    $.ajax({
        url: 'logica/clssInsertPA.php',
        type: 'POST',
        data: { accion: 'INSERTDETALLECAJACHICA', jsDetalleCaja: JSON.stringify(jsDetalleCaja) },
        success: function(response) {
            try {
                var result = JSON.parse(response);
                if (result.estado === true) {
                    swal({ title: "¡Ingreso Registrado con Éxito!", text: result.mensaje, icon: "success", buttons: false, timer: 1500 })
                        .then(() => location.reload());
                } else {
                    swal("Error", result.mensaje, { icon: "error", buttons: { confirm: { className: "btn btn-danger" } } });
                }
            } catch(e) {
                swal("Error", "No se pudo procesar la respuesta.", { icon: "error", buttons: { confirm: { className: "btn btn-danger" } } });
            }
        },
        error: function() {
            swal("Error", "Hubo un problema con la solicitud.", { icon: "error", buttons: { confirm: { className: "btn btn-danger" } } });
        }
    });
}
</script>
