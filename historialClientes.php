<?php
include("cabecera.php");
?>

<div
    class="container">

    <div class="page-inner">
        <div class="card text-start">
            <div class="card-body">
                
                <h4 class="card-title"><i class="fas fa-users"></i> Clientes</h4>
                <div class="card-sub">
                    Busca a tu cliente con deuda y, al hacer clic en 'Ver deuda', podrás comenzar a abonar el monto pendiente.
                </div>
                <div class="table-responsive">
                    <table
                        id="TablaVentaSemanal"
                        class="dataTable display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>monto deuda (S/)</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sucursal_id = isset($_SESSION['sucursal_id']) ? $_SESSION['sucursal_id'] : null;
                            foreach (fnListForClientesDeudaPagasAndNoPagadas($sucursal_id) as $datos) {
                                $datosJSON = json_encode($datos);
                            ?>
                                <tr>
                                    <td><?php echo $datos["cliente_id"] ?></td>
                                    <td><i class="fas fa-user"></i><?php echo " - " . $datos["cliente"] ?></td>
                                    <td><?php echo "S/ ".$datos["monto_deuda_pendiente"] ?></td>
                                    <td>
                                        <div class="mt-2 text-center">

                                            <a
                                                name=""
                                                id=""
                                                onclick='fnAbrirModalPagarCredito(<?php echo $datosJSON ?>)'
                                                class="btn btn-success btn-round"
                                                role="button"> <i class="fas fa-chart-bar"></i> Revisar Historial</a>
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

    /* Asegura que el modal siempre esté centrado */
    .modal-dialog {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        margin: auto;
        /* Centra el modal */
    }

    /* Para permitir desplazamiento horizontal si es necesario */
    .dataTable {
        overflow-x: auto;
    }
</style>

<div
    class="modal fade"
    id="idModalRelizarPago"
    tabindex="-1"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-custom" role="document"><!-- Aquí se añadió modal-lg -->
        <div class="modal-content">

            <div class="modal-body">
                <div class="container-fluid">

                    <h4 class="card-title text-center" style="font-size: 28px;">Deuda de S/ <strong id="idMontoDeuda"></strong></h4>

                    <div
                        class="row justify-content-center align-items-center sm-2">
                        <div class="col-md-6">
                            <div class="card text-start">
                                <div class="card-body">
                                    <h4 class="card-title"><i class="fas fa-user"></i> Cliente </h4>
                                    <hr>
                                    <div><span id="idNombreDeudor">Cliente</span></div>
                                    <div><strong>N° DOCUMENTO:</strong> <span id="docCliente"></span></div>
                                    <div><strong>Número de Celular:</strong> <span id="numCelCliente"></span></div>
                                    <div><strong>Correo:</strong> <span id="emailCliente"></span></div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <div class="card text-start">
                                <div class="card-body">
                                    <h4 class="card-title" id=""><i class="fas fa-credit-card"></i> Datos de Deuda</h4>
                                    <hr>
                                    <div><strong id="idCantidadDeudas"> </strong></div>
                                    <div>Acumulado en deuda (S/): <strong id="idMontoDeudaDeMrd">41.00</strong></div>
                                    <hr>
                                    <br>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title"><i class="fas fa-stream"></i> Pagos Realizados</h4>
                            <hr>
                            <div>
                                <div id="accordionExample" class="accordion">
                                    <!-- Los acordeones se agregarán dinámicamente aquí -->
                                </div>
                                <hr>
                                <div id="paginationControls" class="text-center">
                                    <button id="prevPage" class="btn btn-primary" onclick="changePage('prev')">Anterior</button>
                                    <button id="nextPage" class="btn btn-primary" onclick="changePage('next')">Siguiente</button>

                                    <button
                                        type="button"
                                        class="btn btn-danger"
                                        data-bs-dismiss="modal">
                                        Salir
                                    </button>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <br>
        </div>
    </div>
</div>

<style>
    #idModalDetalle {
        background-color: rgba(0, 0, 0, 0.5);
        /* Fondo oscuro con transparencia */
    }

    #idModalDetalle .modal-content {
        background-color: #fff;
        /* Fondo blanco dentro del modal */
        border-radius: 10px;
        /* Bordes redondeados para el contenido */
    }

    #idModalDetalle .modal-dialog {
        max-width: 50%;
        /* Ajusta el ancho máximo a lo que necesites */
        width: 90%;
        /* O también puedes usar un porcentaje, por ejemplo, el 90% del ancho de la ventana */
    }
</style>



<div
    class="modal fade"
    id="idModalDetalle"
    tabindex="-1"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-custom" role="document"><!-- Aquí se añadió modal-lg -->
        <div class="modal-content">
            <div class="modal-body">
                <div class="container-fluid">
                    <div id="panelDetalleVentaAbono">
                        <div class="card text-start">
                            <div class="card-body">
                                <h4 class="card-title"><i class="fas fa-pen-square"></i> Deuda</h4>
                                <hr>
                                <div><strong>VENTA ID:</strong> <span id="idVentaClienteModal">#</span></div>
                                <div><strong>ACUMULADO A DEUDA:</strong> <span id="idAcumDeudaModal">#</span></div>
                                <div><strong>PENDIENTE A DEUDA:</strong> <span id="idAcumDeudaModal">#</span></div>

                            </div>
                        </div>


                        <div
                            class="row justify-content-center align-items-center sm-2">
                            <div class="col-md-6">
                                <div class="card text-start">
                                    <div class="card-body">
                                        <h4 class="card-title"><i class="fas fa-check"></i> Detalle</h4>
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">First</th>
                                                    <th scope="col">Last</th>
                                                    <th scope="col">Handle</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th scope="row">1</th>
                                                    <td>Mark</td>
                                                    <td>Otto</td>
                                                    <td>@mdo</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="card text-start">
                                    <div class="card-body">
                                        <h4 class="card-title"><i class="fas fa-credit-card"></i> Pagos Realizados</h4>
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">First</th>
                                                    <th scope="col">Last</th>
                                                    <th scope="col">Handle</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th scope="row">1</th>
                                                    <td>Mark</td>
                                                    <td>Otto</td>
                                                    <td>@mdo</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <button
                    type="button"
                    class="btn btn-danger"
                    data-bs-dismiss="modal">
                    Salir
                </button>
            </div>
            <br>
        </div>
    </div>
</div>




<!-- Incluir el CSS de DataTables -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">

<!-- Incluir jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- Incluir el JS de DataTables -->
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script>
    // Variables para manejar los selects y montos adicionales de pago a crédito
    const btnAgregarPagoCredito = document.getElementById('btnAgregarPagoCredito');
    const contenedorPagosCredito = document.getElementById('contenedorPagosCredito');
    let contadorCredito = 1; // Para numerar los campos adicionales de pago a crédito

    // Evento para agregar más selects con montos de pago a crédito
    btnAgregarPagoCredito.addEventListener('click', function() {
        // Crear un contenedor para el nuevo select y su campo de monto
        const nuevoContenedorCredito = document.createElement('div');
        nuevoContenedorCredito.classList.add('d-flex', 'align-items-center', 'mb-2');
        nuevoContenedorCredito.id = 'pagoCredito_' + contadorCredito; // ID único para cada contenedor

        // Crear un nuevo select para el pago a crédito
        const nuevoSelectCredito = document.createElement('select');
        nuevoSelectCredito.classList.add('form-select', 'form-select-md', 'me-2');
        nuevoSelectCredito.name = 'formaPagoCredito[]'; // Nombre único para el array
        nuevoSelectCredito.id = 'formaPagoCreditoSelect_' + contadorCredito; // ID único para el select
        nuevoSelectCredito.innerHTML = `<?php
                                        foreach (listarFormaPago() as $datosFormaPago) {
                                            echo '<option value="' . $datosFormaPago["id"] . '">' . $datosFormaPago["nombre"] . '</option>';
                                        }
                                        ?>`;

        // Crear una nueva caja de texto para el monto de pago a crédito
        const nuevoInputMontoCredito = document.createElement('input');
        nuevoInputMontoCredito.type = 'number';
        nuevoInputMontoCredito.classList.add('form-control', 'form-control-md', 'ms-2');
        nuevoInputMontoCredito.placeholder = 'Monto';
        nuevoInputMontoCredito.min = '0';
        nuevoInputMontoCredito.name = 'montoCredito[]'; // Nombre único para el array
        nuevoInputMontoCredito.id = 'montoSelectCredito_' + contadorCredito; // ID único para el campo de monto

        // Crear un botón de eliminación pequeño
        const btnEliminarCredito = document.createElement('button');
        btnEliminarCredito.type = 'button';
        btnEliminarCredito.classList.add('btn', 'btn-danger', 'btn-sm', 'ms-2'); // Clase btn-sm para hacerlo más pequeño
        btnEliminarCredito.textContent = '-'; // Texto del botón
        btnEliminarCredito.addEventListener('click', function() {
            contenedorPagosCredito.removeChild(nuevoContenedorCredito); // Eliminar el contenedor
        });

        // Agregar el select, el input y el botón de eliminación al contenedor
        nuevoContenedorCredito.appendChild(nuevoSelectCredito);
        nuevoContenedorCredito.appendChild(nuevoInputMontoCredito);
        nuevoContenedorCredito.appendChild(btnEliminarCredito);

        // Agregar el contenedor al contenedor principal
        contenedorPagosCredito.appendChild(nuevoContenedorCredito);

        // Incrementar el contador para los nuevos inputs
        contadorCredito++;
    });
</script>

<script>
    function fnAbrirModalPagarCredito(jsonDatosCliente) {
        //idNombreDeudor
        document.getElementById("idNombreDeudor").innerText = jsonDatosCliente.cliente;
        document.getElementById("idMontoDeuda").innerText = jsonDatosCliente.monto_deuda_pendiente;
        document.getElementById("docCliente").innerText = jsonDatosCliente.numero_documento;
        document.getElementById("numCelCliente").innerText = jsonDatosCliente.telefonomovil;
        document.getElementById("emailCliente").innerText = jsonDatosCliente.email;
        $.ajax({
            url: 'logica/clssConsultas.php',
            type: 'POST',
            data: {
                accion: "VENTAIDCLIENTEDEMRD",
                cliente_id: jsonDatosCliente.cliente_id
            },
            dataType: 'json',
            success: function(datosVenta) {
                console.log("DATOS DE MRD:", datosVenta)
                var select = $('#selectDeudasCliente');
                select.empty();

                var contador = 0;
                var acumDeuda = 0;
                if (datosVenta && datosVenta.length > 0) {
                    $.each(datosVenta, function(index, item) {
                        contador = contador + 1;
                        acumDeuda = acumDeuda + parseFloat(item.deuda_pendiente) |
                            select.append('<option value="' + item.id_venta + '"data-pendiente="' + item.deuda_pendiente + '"data-total_venta="' + item.monto + '" data-acumulado="' + item.acumulado + '" data-descripcion="' + item.formato + '" >' + item.formato + '</option>');
                    });
                } else {
                    select.append('<option disabled>No hay deudas disponibles :)</option>');
                }
                var mensaje = "";
                if (contador == 0) {
                    mensaje = "<span style = 'color:green'> Tiene <b>0 deudas 😀</b></span>"
                }else if(contador == 1){
                    mensaje = "<span style = 'color:red'> Tiene <b>" + contador + " deuda 😞</b></span>"
                } else {
                    mensaje = "<span style = 'color:red'> Tiene <b>" + contador + " deudas 😞</b></span>"
                }
                document.getElementById("idCantidadDeudas").innerHTML = mensaje;
                document.getElementById("idMontoDeudaDeMrd").innerText = acumDeuda.toFixed(2);

            },
            error: function(xhr, status, error) {
                console.error("Error al obtener los detalles de la venta:", error);
            }
        });
        fn_listarPagosAbono(jsonDatosCliente.cliente_id);
        $('#idModalRelizarPago').modal('show');


    }
    $('#selectDeudasCliente').focus(function() {
        fn_limpiarTabla();
    });


    function fn_limpiarTabla() {
        var tabla = document.getElementById("idTablitaDetalle").getElementsByTagName("tbody")[0];
        tabla.innerHTML = '';
        $('#panelDetalle').hide();

    }

    function fn_listForDetalleDeuda() {
        $('#panelDetalle').show();

        var idVenta = $('#selectDeudasCliente').val();
        document.getElementById("idVentaCliente").innerText = idVenta;

        ///////////////////////////////////////////
        var selectedOption = $('#selectDeudasCliente option:selected');
        var acumuladoDeuda = selectedOption.data('acumulado');
        var pendienteDeuda = selectedOption.data('pendiente');
        var totalVenta = selectedOption.data('total_venta');

        document.getElementById("idAcumDeuda").innerHTML = "<span style='color:green'><b>S/" + acumuladoDeuda; + "<b></span>";
        document.getElementById("idPendienteDeuda").innerHTML = "<span style='color:red'><b>S/" + pendienteDeuda; + "<b></span>";
        document.getElementById("idTotalVentaDeuda").innerText = totalVenta;

        $.ajax({
            url: 'logica/clssConsultas.php',
            type: 'POST',
            data: {
                accion: "DETALLEVENTA_VENTA_ID",
                venta_id: idVenta
            },
            dataType: 'json',
            success: function(datosArticulo) {
                console.log("Detalles de venta: ", datosArticulo);
                var tabla = document.getElementById("idTablitaDetalle").getElementsByTagName("tbody")[0];
                tabla.innerHTML = '';

                for (let i = 0; i < datosArticulo.length; i++) {
                    let articulo = datosArticulo[i];
                    let nuevaFila = tabla.insertRow();
                    console.log(articulo);
                    let min = articulo["minutos"] !== null ? articulo["minutos"] : '';

                    let totalCorte = (articulo["minutos"] === null && articulo["costo_por_minuto"] === null) ?
                        '-' : // Si ambos son null, mostramos una línea
                        (articulo["minutos"] && articulo["costo_por_minuto"]) ?
                        (articulo["costo_por_minuto"] * articulo["minutos"]) : articulo["sub_total"] || '-';

                    let totalCorteRedondeado = (totalCorte !== '-') ? totalCorte.toFixed(2) : totalCorte;
                    let texto = "";
                    if (articulo["minutos"] !== null || articulo["costo_por_minuto"] !== null) {
                        texto = articulo["descripcion"] + "\n" + "<span style='color:blue'> <b>[" + min + " Minutos X " + articulo["costo_por_minuto"] + " = " + totalCorte.toFixed(2) + "]</b></span>";

                    } else {
                        texto = articulo["descripcion"];
                    }

                    nuevaFila.insertCell(0).innerHTML = texto;
                    nuevaFila.insertCell(1).textContent = totalCorteRedondeado;
                    nuevaFila.insertCell(2).textContent = articulo["cantidad"] || '-';
                    nuevaFila.insertCell(3).textContent = articulo["precio_unitario_articulo"] || '-';
                    nuevaFila.insertCell(4).textContent = articulo["sub_total"] || '-';
                }


            },
            error: function(xhr, status, error) {
                console.error("Error al obtener los detalles de la venta:", error);
            }
        });

    }

    function fn_solicitudAjax() {
        $.ajax({
            url: 'logica/clssConsultas.php',
            type: 'POST',
            data: {
                accion: "PAGOS_ABONADOS_CLIENTE_ID",
                cliente_id: datosCliente
            },
            dataType: 'json',
            success: function(datos) {
                console.log("DATOS DE MRD ABONOS:", datos);

            },
            error: function(xhr, status, error) {
                console.error("Error al obtener los detalles de la venta:", error);
            }
        });
    }

    function fn_verDetalle(idVenta) {
        //
        $('#idModalDetalle').modal('show');

    }

    let acordeonesData = []; // Aquí almacenaremos todos los acordeones
    let currentPage = 1; // Página actual
    const itemsPerPage = 5; // Número de acordeones por página

    function fn_listarPagosAbono(datosCliente) {
        $.ajax({
            url: 'logica/clssConsultas.php',
            type: 'POST',
            data: {
                accion: "PAGOS_ABONADOS_CLIENTE_ID",
                cliente_id: datosCliente
            },
            dataType: 'json',
            success: function(datos) {
                console.log("DATOS DE MRD ABONOS:", datos);

                // Almacena los acordeones completos
                acordeonesData = datos;

                // Llama a la función para mostrar los acordeones de la página actual
                displayAcordeones();
            },
            error: function(xhr, status, error) {
                console.error("Error al obtener los abonos:", error);
            }
        });
    }

    // Función para mostrar acordeones de acuerdo a la página actual
    function displayAcordeones() {
        const contenedor = document.getElementById('accordionExample');
        contenedor.innerHTML = "";

        // Calcula los índices para dividir los acordeones
        const start = (currentPage - 1) * itemsPerPage;
        const end = currentPage * itemsPerPage;

        // Selecciona los acordeones que se mostrarán en la página actual
        const acordeonesToShow = acordeonesData.slice(start, end);

        // Mapeamos los datos para crear los acordeones
        const promesas = acordeonesToShow.map(function(item, index) {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    url: 'logica/clssConsultas.php',
                    type: 'POST',
                    data: {
                        accion: "DETALLE_ABONO_DEUDA_CLIENTEDDRMD",
                        abono_id: item.id_general
                    },
                    dataType: 'json',
                    success: function(datosDetalle) {

                        var pagos = JSON.parse(item.js_detalle_forma_pago);

                        var tableRows = '';
                        var tableRowsPagos = '';

                        datosDetalle.forEach(function(detalle) {
                            tableRows += `
                            <tr style="border-bottom: 1px solid #000;">
                                <td>${detalle.formato}</td>
                                <td></td>
                            </tr>
                        `;
                        });

                        pagos.forEach(function(pago) {
                            tableRowsPagos += `
                            <tr style="border-bottom: 1px solid #000;">
                                <td><strong style='color:${pago.COLOR}'>${pago.FORMA_PAGO}</strong></td>
                                <td><strong> S/ ${pago.MONTO.toFixed(2)} </strong></td>
                            </tr>
                        `;
                        });

                        var estado = item.estado_deuda === "PAGADO" ? `
                        <div class="text-center">
                            <button class="btn btn-success rounded" disabled>${item.estado_deuda}</button>
                        </div>
                    ` : `
                        <div class="text-center">
                            <button class="btn btn-danger rounded" disabled>${item.estado_deuda}</button>
                        </div>
                    `;

                        var datosVentaPagoInicial = `
                        <div><strong>Cliente:</strong> ${item.cliente}</div>
                        <div><strong>Fecha:</strong> ${item.fecha}</div>
                        <div><strong>Hora:</strong> ${item.hora}</div>
                        <div><strong>Monto Venta:</strong> <span style='color:blue'>S/${item.monto_deuda}</span></div>
                        <div><strong>Pago Inicial:</strong> <span style='color:green'>S/${item.monto}</span></div>
                        ${estado}
                        <hr>
                    `;

                        var datosVentaPagoAbono = `
                        <div><strong>Cliente:</strong> ${item.cliente}</div>
                        <div><strong>Fecha:</strong> ${item.fecha}</div>
                        <div><strong>Hora:</strong> ${item.hora}</div>
                        <div><strong>Monto:</strong> <span style='color:green'>S/${item.monto}</span></div>
                        <hr>
                        <div>
                            <table id="tablita" class="table table-sm">
                                <thead>
                                    <tr>
                                        <th style="border-bottom: 1px solid #000;"><i class="fas fa-shopping-bag"></i> Venta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${tableRows}
                                </tbody>
                            </table>
                            <hr>
                        </div>
                    `;

                        var concatenar = item.estacion === "PAGO INICIAL" ? datosVentaPagoInicial : datosVentaPagoAbono;

                        var acordeonHTML = `
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading${start + index + 1}">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse${start + index + 1}" aria-expanded="false" aria-controls="collapse${start + index + 1}">
                                    ${item.formato}
                                </button>
                            </h2>
                            <div id="collapse${start + index + 1}" class="accordion-collapse collapse" aria-labelledby="heading${start + index + 1}"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    ${concatenar}
                                    <div>
                                        <table id="tablita" class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th style="border-bottom: 1px solid #000;"><i class="fas fa-credit-card"></i> Pagos</th>
                                                    <th style="border-bottom: 1px solid #000;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${tableRowsPagos}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                        resolve(acordeonHTML);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error al obtener los detalles del abono:", error);
                        reject(error);
                    }
                });
            });
        });

        // Una vez que todos los acordeones están listos, los agregamos al contenedor
        Promise.all(promesas).then(function(acordeonesHTML) {
            contenedor.innerHTML = acordeonesHTML.join('');
        }).catch(function(error) {
            console.error("Error en alguna de las peticiones:", error);
        });

        // Actualiza los controles de la paginación
        updatePaginationControls();
    }

    // Función para cambiar de página
    function changePage(direction) {
        if (direction === 'prev' && currentPage > 1) {
            currentPage--;
        } else if (direction === 'next' && currentPage * itemsPerPage < acordeonesData.length) {
            currentPage++;
        }

        // Llamamos a displayAcordeones para actualizar la vista
        displayAcordeones();
    }

    // Función para actualizar los controles de paginación
    function updatePaginationControls() {
        document.getElementById('prevPage').disabled = currentPage === 1;
        document.getElementById('nextPage').disabled = currentPage * itemsPerPage >= acordeonesData.length;
    }

    // Llamamos a la función para obtener los pagos del cliente
    fn_listarPagosAbono(datosCliente);
</script>

<script>
    $(document).ready(function() {
        // Inicializar DataTable para todas las tablas con la clase 'dataTable'
        $('.dataTable').DataTable({
            "columnDefs": [{
                "targets": [0], // Índice de la columna que quieres ocultar (empieza desde 0)
                "visible": false // Oculta la columna
            }],
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
    });
</script>
<?php
include("pie.php");
?>