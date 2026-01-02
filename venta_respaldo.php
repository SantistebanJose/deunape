<?php
include("cabecera.php");
$sucursal_id = isset($_SESSION['sucursal_id']) ? $_SESSION['sucursal_id'] : null;
include("logica/clssVenta.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

?>
<style>
    #sugerencias {
        max-height: 200px;
        overflow-y: auto;
        z-index: 1050; /* Para asegurar que esté sobre otros elementos */
    }

    #sugerencias .list-group-item {
        cursor: pointer;
    }
    #tabla_articulos th:nth-child(1),
#tabla_articulos td:nth-child(1) {
    display: none;
}
</style>

<div
    class="container">
    <div class="page-inner">
        <div
            class="card"
        ">

            <div class="card-body">
                <h4 class="card-title">Venta</h4>
                <div class="mb-3">
                    <div class="card-sub">
                        Aquí podrás realizar ventas de cuando un cliente viene a realizar corte y/o compra de materiales.
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4 class="card-title">Artículos</h4>
                            <button type="button" class="btn btn-success" onclick="agregarCorte()" >Solo Corte</button>
                        </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table
                                        id="multi-filter-select"
                                        class="display table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Articulo</th>
                                                <th>Categoria</th>
                                                <th>Tipo</th>
                                                <th>Dimension</th>
                                                <th>Stock</th>
                                                <th>Precio de Venta</th>
                                                <th>Accion</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>Articulo</th>
                                                <th>Categoria</th>
                                                <th>Tipo</th>
                                                <th>Dimension</th>
                                                <th>Stock</th>
                                                <th>Precio de Venta</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>

                                            <?php
                                            foreach (listarProductosVenta1($sucursal_id) as $datosArticulo) {
                                                $datosArticuloJSON = json_encode($datosArticulo);


                                            ?>
                                                <tr>
                                                    <td><?php echo $datosArticulo["articulo"] ?></td>
                                                    <td><?php echo $datosArticulo["categoria"] ?></td>
                                                    <td><?php echo $datosArticulo["tipo"] ?></td>
                                                    <td><?php echo $datosArticulo["dimension"] ?></td>
                                                    <td><?php echo $datosArticulo["stock"] ?></td>
                                                    <td><?php echo $datosArticulo["precio_venta"] ?></td>
                                                    <th>
                                                        <div class="d-flex align-items-center justify-content-center">
                                                            <button id="rest_<?php echo $datosArticulo["id"] ?>" class="btn btn-danger btn-round me-2">-</button>
                                                            <span id="cantidad_<?php echo $datosArticulo["id"] ?>" class="mx-2">1</span>
                                                            <button id="add_<?php echo $datosArticulo["id"] ?>" class="btn btn-success btn-round ms-2">+</button>
                                                        </div>
                                                        <div class="mt-2 text-center">
                                                            <a
                                                                name=""
                                                                id=""
                                                                class="btn btn-secondary btn-round"

                                                                onclick='fn_agregar_venta(<?php echo $datosArticuloJSON; ?>)'
                                                                role="button">Agregar</a>
                                                        </div>
                                                    </th>
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

                    <!-- 
                        <label for="" class="form-label">Movimiento</label>
                        <select
                        class="form-select form-select-md"
                        name=""
                        id="">
                        <option selected>Seleccione</option>
                        
                        <?php
                        /**
                         foreach (listarMovimientos2() as $movimiento): ?>
                            <option value="<?php echo htmlspecialchars($movimiento['id']); ?>">
                                <?php echo htmlspecialchars($movimiento['descripcion']); ?>
                            </option>
                        <?php endforeach  
                         */
                        ?>

                    </select>
                    -->

                </div>
                <div
                    class="card"
                ">
                </div>





<!-- Modal Solo Corte -->
<div class="modal fade" id="modalSoloCorte" tabindex="-1" aria-labelledby="modalSoloCorteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSoloCorteLabel">Corte de Minutos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-body text-center">
                    <p class="card-text">Minutos Corte</p> 
                    <div class="row">
                        <div class="col">
                            <button id="btn_menos_solocorte" class="btn btn-danger btn-round ms-2" type="button">-</button>
                        </div>
                        <div id="cantidad_solocorte" class="col">0</div>
                        <div class="col">
                            <button id="btn_mas_solocorte" class="btn btn-success btn-round ms-2" type="button">+</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btn_agregar_solocorte">Agregar</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal -->
    <div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="miModalLabel">Agregar Corte de Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
            <!-- Acordeones dinámicos -->
            <div class="accordion" id="acordeonContainer">
                <!-- Se llenará dinámicamente -->
            </div>
            <!-- Sección global -->
            <div id="globalContainer" class="mt-3">
                <!-- Se llenará dinámicamente -->
            </div>
        </div>

      <!-- Modal Footer -->
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn_no">No</button>
            <button type="button" class="btn btn-primary" id="btn_si">Sí</button>
         </div>

        </div>
            </div>
                </div>
               

               


            </div>
            
        </div>
        <hr>
                <div
                    class="row ">
                    
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Detalle Materiales / Corte</div>
                            </div>
                            <div class="card-body" > 
                                <div class="card-sub">
                                    Aquí la venta de los materiales
                                </div>
                                <div class="table-responsive">
                                <table id="tabla_articulos" class="table mt-3">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">MINUTOS</th>
                                            <th scope="col">Tarifa</th>
                                            <th scope="col">Total Corte</th>
                                            <th scope="col">Articulo</th>
                                            <th scope="col">Cantidad</th>
                                            <th scope="col">Precio Unitario</th>
                                            <th scope="col">Sub Total (S/)</th>
                                            <th scope="col">Accion</th>
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
        <div class="row">
            <div class="col-md-3">
                <div class="card card-stats card-round">
                    <div class="card-body text-center">
                        <h5 id="label_total_cortes" class="card-title">Total Cortes S/:</h5>
                        <span id="id_subtotal_cortes" style="font-size: 1.3rem;" aria-labelledby="label_total_cortes">xx.xx</span>
                    </div>
                </div>  
            </div>
            <div class="col-md-3">
                    <div class="card card-stats card-round">
                    <div class="card-body text-center">
                        <h5 id="label_total_articulos" class="card-title">Total Artículos S/:</h5>
                        <span id="id_subtotal_articulos" style="font-size: 1.3rem;"  aria-labelledby="label_total_articulos">xx.xx</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                    <div class="card card-primary card-stats card-round">
                    <div class="card-body text-center">
                        <h5 id="label_total_general" class="card-title">Total S/:</h5>
                        <span id="id_subtotal_general" style="font-size: 1.3rem;" aria-labelledby="label_total_general">xx.xx</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <button id="btnRealizarReserva" type="button" class="btn btn-success btn-block card card-stats card-round">
                    <div class="card-body text-center">
                        <h5 id="label_total_general" class="card-title">Realizar Reserva</h5>
                    </div>
                </button>
            </div>
        </div>
    </div>


</div>


<!-- Modal Body -->
<!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
<div
    class="modal fade"
    id="modalRealizarPago"
    tabindex="-1"
    data-bs-backdrop="static"
    data-bs-keyboard="false"

    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true">
    <div
        class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg"
        role="document">
        <div class="modal-content">

            <div class="modal-body">
                <div
                    class="card border-primary">
                    <div class="card-body">

                        <div class="card-body text-center">
                            <h4 class="card-title">Realizar Reserva</h4>
                        </div>
                       <!--<div class="card-body text-center">
                            <h1 class="card-title">S/ xx.xx</h1>
                        </div>-->

                        <div class="card-sub">
                            Aquí realiza tus pagos
                        </div>
                        <div>
                            <span>ID Cliente: <span id="idPersona">#</span></span>
                        </div>
                        <hr>
                        <div class="mb-3 position-relative">
                            <label for="nombreCliente" class="form-label">Cliente</label>
                            <input
                                type="text"
                                class="form-control"
                                id="nombreCliente"
                                placeholder="AGREGAR EL NOMBRE DEL CLIENTE O DNI" />
                            <!-- Contenedor para las sugerencias -->
                            <div id="sugerencias" class="list-group position-absolute w-100"></div>
                        </div>
                          <!-- Monto Total -->
                        <div class="mb-3">
                            <label for="montoTotal" class="form-label">Monto Total</label>
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="montoTotal"
                                    placeholder="Monto total de la venta"
                                    readonly />
                            </div>
                        </div>

                      
                        <div class="text-center">
                            <a class="btn btn-success" id="Reservar" role="button">Reservar</a>
                        </div>


                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Salir
                </button>

            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>


<script>
    $(document).ready(function() {
        // Inicialización de DataTables en español
        $("#basic-datatables").DataTable({
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

        $("#multi-filter-select").DataTable({
            pageLength: 5,
            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
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
            },
            initComplete: function() {
                this.api()
                    .columns()
                    .every(function() {
                        var column = this;
                        var select = $('<select class="form-select"><option value=""></option></select>')
                            .appendTo($(column.footer()).empty())
                            .on("change", function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                column.search(val ? "^" + val + "$" : "", true, false).draw();
                            });

                        column.data().unique().sort().each(function(d, j) {
                            select.append('<option value="' + d + '">' + d + "</option>");
                        });
                    });
            }
        });

        // Agregar fila en español
        $("#add-row").DataTable({
            pageLength: 5,
            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
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

        var action =
            '<td> <div class="form-button-action"> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Editar tarea"> <i class="fa fa-edit"></i> </button> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Eliminar"> <i class="fa fa-times"></i> </button> </div> </td>';

        $("#addRowButton").click(function() {
            $("#add-row")
                .dataTable()
                .fnAddData([
                    $("#addName").val(),
                    $("#addPosition").val(),
                    $("#addOffice").val(),
                    action,
                ]);
            $("#addRowModal").modal("hide");
        });
    });
</script>

<script>
     document.addEventListener('DOMContentLoaded', function() {
         // Incremento de minutos
        document.getElementById('btn_mas_solocorte').addEventListener('click', function() {
            let cantidad = parseInt(document.getElementById('cantidad_solocorte').textContent);
            if(cantidad >0){
                cantidad++;
                document.getElementById('cantidad_solocorte').textContent = cantidad;
            }else{
                document.getElementById('cantidad_solocorte').textContent = 10;

            }
            
        });

        // Decremento de minutos
        document.getElementById('btn_menos_solocorte').addEventListener('click', function() {
            let cantidad = parseInt(document.getElementById('cantidad_solocorte').textContent);
            if (cantidad > 0) {
                cantidad--;
                document.getElementById('cantidad_solocorte').textContent = cantidad;
            }
        });

     })
   

</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
            const botonesSumar = document.querySelectorAll('[id^="add_"]');
            const botonesRestar = document.querySelectorAll('[id^="rest_"]');

            botonesSumar.forEach(boton => {
                boton.addEventListener('click', function() {
                    const id = boton.id.split('_')[1];
                    const spanCantidad = document.getElementById(`cantidad_${id}`);
                    let cantidad = parseInt(spanCantidad.textContent);
                    cantidad++;
                    spanCantidad.textContent = cantidad;
                });
            });


            botonesRestar.forEach(boton => {
                boton.addEventListener('click', function() {
                    const id = boton.id.split('_')[1];
                    const spanCantidad = document.getElementById(`cantidad_${id}`);
                    let cantidad = parseInt(spanCantidad.textContent);
                    if (cantidad > 1) {
                        cantidad--;
                        spanCantidad.textContent = cantidad;
                    }
                });
            });
        });

        function fn_agregar_venta(datosArticulo){
        
            if(verificarSiArticuloExiste(datosArticulo['id'])){
                Swal.fire({
                    icon: 'info',
                    title: '¡Artículo ya registrado!',
                    text: 'Este artículo ya está en la tabla.',
                    confirmButtonText: 'Aceptar'
                });

            
            }else{
                if (datosArticulo['corte']){
                fn_preguntar_corte(datosArticulo);
            
                }else{
                    fn_agregar_articulo_tabla(datosArticulo);
                    
                }
            }

        }

    function fn_preguntar_corte(datosArticulo) {
        const modal = new bootstrap.Modal(document.getElementById('miModal'), {
            backdrop: 'static', // Evita que se cierre al hacer clic fuera
            keyboard: false     // Evita que se cierre con la tecla 'Esc'
        });
        modal.show();

        // Obtener cantidad de productos
        const cantidad = parseInt(document.getElementById("cantidad_" + datosArticulo["id"]).textContent) || 0;

        // Obtener contenedores
        const acordeonContainer = document.getElementById('acordeonContainer');
        const globalContainer = document.getElementById('globalContainer');

        // Limpiar contenido anterior
        acordeonContainer.innerHTML = "";
        globalContainer.innerHTML = "";

        // Mostrar acordeones solo si hay más de 1 producto
        if (cantidad > 1) {
            for (let i = 1; i <= cantidad; i++) {
                const acordeon = `
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading${i}">
                            <button class="accordion-button collapsed" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#collapse${i}" 
                                    aria-expanded="false" aria-controls="collapse${i}">
                                ${datosArticulo["articulo"]} - Corte ${i}
                            </button>
                        </h2>
                        <div id="collapse${i}" class="accordion-collapse collapse" 
                            aria-labelledby="heading${i}" data-bs-parent="#acordeonContainer">
                            <div class="accordion-body text-center">
                                <p class="card-text">Minutos Corte</p>
                                <div class="row">
                                    <div class="col">
                                        <button id="btn_menos_${i}" class="btn btn-danger btn-round ms-2" type="button">-</button>
                                    </div>
                                    <div id="cantidad_${i}" class="col">0</div>
                                    <div class="col">
                                        <button id="btn_mas_${i}" class="btn btn-success btn-round ms-2" type="button">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
                acordeonContainer.innerHTML += acordeon;
            }


            document.querySelectorAll('.accordion-button').forEach(button => {
                button.classList.add('collapsed'); // Asegura que esté colapsado
                button.setAttribute('aria-expanded', 'false'); // Marca como cerrado
            });

            // Eventos dinámicos para cada acordeón
            for (let i = 1; i <= cantidad; i++) {
                const btnMas = document.getElementById(`btn_mas_${i}`);
                const btnMenos = document.getElementById(`btn_menos_${i}`);
                const cantidadElemento = document.getElementById(`cantidad_${i}`);
                btnMenos.disabled = true;

                btnMas.addEventListener("click", () => {
                    btnMenos.disabled = false;
                    let valorActual = parseInt(cantidadElemento.innerText) || 0;
                    if (valorActual > 0){
                        cantidadElemento.innerText = valorActual + 1;
                    }else{
                        cantidadElemento.innerText = 10;
                    }
                });

                btnMenos.addEventListener("click", () => {
                    let valorActual = parseInt(cantidadElemento.innerText) || 0;
                    if (valorActual > 0) {
                        cantidadElemento.innerText = valorActual - 1;
                    }
                    btnMenos.disabled = valorActual === 1;

                    
                });
            }
        }

        // Sección global (siempre visible)
        const globalSection = `
            <div class="card-body text-center">
                <p class="card-text">Minutos Corte Todos</p>
                <div class="row">
                    <div class="col">
                        <button id="btn_menos_global" class="btn btn-danger btn-round ms-2" type="button">-</button>
                    </div>
                    <div id="cantidad_global" class="col">0</div>
                    <div class="col">
                        <button id="btn_mas_global" class="btn btn-success btn-round ms-2" type="button">+</button>
                    </div>
                </div>
            </div>`;
        globalContainer.innerHTML = globalSection;

        // Eventos para botones globales
        const btnMasGlobal = document.getElementById('btn_mas_global');
        const btnMenosGlobal = document.getElementById('btn_menos_global');
        const cantidadGlobal = document.getElementById('cantidad_global');
        btnMenosGlobal.disabled = true;

        btnMasGlobal.addEventListener("click", () => {
            btnMenosGlobal.disabled = false
            let valorActual = parseInt(cantidadGlobal.innerText) || 0;
            if (valorActual > 0){
                cantidadGlobal.innerText = valorActual + 1;
            }else{
                cantidadGlobal.innerText = 10;
            }
        
        });

        btnMenosGlobal.addEventListener("click", () => {
            let valorActual = parseInt(cantidadGlobal.innerText) || 0;
            if (valorActual > 0) {
                cantidadGlobal.innerText = valorActual - 1;
            }
            btnMenosGlobal.disabled = valorActual === 1;
        });

        // Eventos para los botones Si y No
        const btnSi = document.getElementById('btn_si');
        const btnNo = document.getElementById('btn_no');

        btnSi.replaceWith(btnSi.cloneNode(true));
        btnNo.replaceWith(btnNo.cloneNode(true));

        const btnSiNuevo = document.getElementById('btn_si');
        const btnNoNuevo = document.getElementById('btn_no');

        btnSiNuevo.addEventListener("click", () => {
            let tipoCorte = 'individual';
            let datosCorte = [];

            // Si es corte global
            let minutosGlobal = parseInt(document.getElementById('cantidad_global').innerText) || 0;
            if (minutosGlobal > 0) {
                tipoCorte = 'global';
                for (let i = 0; i < cantidad; i++) {
                    datosCorte.push({
                        id: datosArticulo["id"],
                        minutos: minutosGlobal,
                        costo: minutosGlobal * 1.5
                    });
                }
            } else {
                // Para corte individual
                for (let i = 1; i <= cantidad; i++) {
                    let minutos = parseInt(document.getElementById(`cantidad_${i}`).innerText) || 0;
                    datosCorte.push({
                        id: datosArticulo["id"],
                        minutos: minutos,
                        costo: minutos * 1.5
                    });
                }
            }

            modal.hide(); // Cerrar modal
            console.log(datosCorte);
            fn_agregar_articulo_tabla(datosArticulo, tipoCorte, datosCorte);

        });

        btnNoNuevo.addEventListener("click", () => {
            modal.hide();
            fn_agregar_articulo_tabla(datosArticulo);
            
        });
    }

    function agregarCorte() {
        const modalElement = document.getElementById('modalSoloCorte');
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: 'static', // Evita que se cierre al hacer clic fuera
            keyboard: false     // Evita que se cierre con la tecla 'Esc'
        });
        modal.show(); // Muestra el modal

        // Evento para cuando se hace clic en el botón "Agregar"
        const btn_agregar = document.getElementById('btn_agregar_solocorte');
        btn_agregar.addEventListener("click", () => {
            const cantidadMinutos = parseInt(document.getElementById('cantidad_solocorte').textContent) || 0;
            const datosArticulo = {}; // Aquí deberías obtener los datos del artículo (p. ej., desde una variable global o un formulario)
            const tarifa = 1.5
            // Crear el objeto datosCorte
            const datosCorte = [
                {
                    id: '#', // Id del corte
                    minutos: cantidadMinutos, // Minutos registrados
                    tarifa: tarifa, // Costo por minuto
                    consto: cantidadMinutos * tarifa
                }
            ];

            // Llamar a la función fn_agregar_articulo_tabla
            fn_agregar_articulo_tabla(datosArticulo, 'solocorte', datosCorte);

            // Reiniciar los minutos a 0 en la interfaz
            document.getElementById('cantidad_solocorte').textContent = 0;

            // Ocultar el modal
            modal.hide();
        });
    }

    function fn_agregar_articulo_tabla(datosArticulo,tipoCorte = 'none', datosCorte = []) {
        
        var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];
        if (tipoCorte === 'global') {
            datosCorte.forEach(corte => {
                let nuevaFila = tabla.insertRow();

                nuevaFila.insertCell(0).textContent = corte.id; // ID
                nuevaFila.insertCell(1).textContent = corte.minutos; // Minutos
                nuevaFila.insertCell(2).textContent = 1.5; // Minutos
                nuevaFila.insertCell(3).textContent = corte.costo; // Costo x Minuto
                nuevaFila.insertCell(4).textContent = datosArticulo["articulo"]; // Artículo
                nuevaFila.insertCell(5).textContent = 1; // Cantidad fija por corte
                nuevaFila.insertCell(6).textContent = datosArticulo["precio_venta"]; // Precio unitario
                nuevaFila.insertCell(7).textContent = (corte.costo + parseFloat(datosArticulo["precio_venta"])).toFixed(2); // Subtotal

                let accionCell = nuevaFila.insertCell(8);

                let botonEliminar = document.createElement("button");
                botonEliminar.classList.add("btn", "btn-warning", "btn-round", "ms-2");
                let iconoBasura = document.createElement("i");
                iconoBasura.classList.add("fas", "fa-trash"); // Font Awesome icon for trash
                // Añadir los botones a la celda de acciones
                botonEliminar.appendChild(iconoBasura);
                accionCell.appendChild(botonEliminar);
                  // Función para manejar el botón de eliminar
                  botonEliminar.addEventListener("click", () => {
                    const fila = botonEliminar.closest("tr");
                    fila.remove(); // Eliminar la fila
                    fn_obtener_total(); // Recalcular los totales después de eliminar
                });

            });
        } else if (tipoCorte === 'individual') {
            let cantidadRestante = parseInt(document.getElementById("cantidad_" + datosArticulo["id"]).textContent) || 0;
            var contadorCantidad  = 0 ;
            datosCorte.forEach((corte, index) => {
                if (corte.minutos > 0 ){
                    let nuevaFila = tabla.insertRow();
                    nuevaFila.insertCell(0).textContent = corte.id; // ID
                    nuevaFila.insertCell(1).textContent = corte.minutos > 0 ? corte.minutos : '-'; // Minutos
                    nuevaFila.insertCell(2).textContent = 1.5; // Minutos
                    nuevaFila.insertCell(3).textContent = corte.costo > 0 ? corte.costo : '-'; // Costo x Minuto
                    nuevaFila.insertCell(4).textContent = datosArticulo["articulo"]; // Artículo
                    nuevaFila.insertCell(5).textContent = 1; // Cantidad
                    nuevaFila.insertCell(6).textContent = datosArticulo["precio_venta"]; // Precio unitario
                    let subtotal = (corte.costo > 0 ? corte.costo : 0) + parseFloat(datosArticulo["precio_venta"]);
                    nuevaFila.insertCell(7).textContent = (subtotal).toFixed(2);; // Subtotal

                    let accionCell = nuevaFila.insertCell(8);

                    let botonEliminar = document.createElement("button");
                    botonEliminar.classList.add("btn", "btn-warning", "btn-round", "ms-2");
                    let iconoBasura = document.createElement("i");
                    iconoBasura.classList.add("fas", "fa-trash"); // Font Awesome icon for trash
                    // Añadir los botones a la celda de acciones
                    botonEliminar.appendChild(iconoBasura);
                    accionCell.appendChild(botonEliminar);
                        // Función para manejar el botón de eliminar
                        botonEliminar.addEventListener("click", () => {
                        const fila = botonEliminar.closest("tr");
                        fila.remove(); // Eliminar la fila
                        fn_obtener_total(); // Recalcular los totales después de eliminar
                    });
                
                }else{
                    contadorCantidad ++;
                }
            });

            if (contadorCantidad > 0){
                let nuevaFila = tabla.insertRow();

                // Agregar el input a la celda
                nuevaCelda.appendChild(inputElemento);
                nuevaFila.insertCell(0).textContent = datosArticulo["id"]; // ID
                nuevaFila.insertCell(1).textContent = '-'; // Minutos
                nuevaFila.insertCell(2).textContent = 1.5; // Minutos
                nuevaFila.insertCell(3).textContent = '-'; // Costo x Minuto
                nuevaFila.insertCell(4).textContent = datosArticulo["articulo"]; // Artículo
               nuevaFila.insertCell(5).textContent = contadorCantidad; // Cantidad
                nuevaFila.insertCell(6).textContent = datosArticulo["precio_venta"]; // Precio unitario
                nuevaFila.insertCell(7).textContent = (contadorCantidad * parseFloat(datosArticulo["precio_venta"])).toFixed(2); // Subtotal
                

                let accionCell = nuevaFila.insertCell(8);
                let botonMas = document.createElement("button");
                botonMas.classList.add("btn", "btn-success","btn-round","ms-2");
                botonMas.textContent = "+";
                
                let botonMenos = document.createElement("button");
                botonMenos.classList.add("btn", "btn-danger","btn-round","ms-2");
                botonMenos.textContent = "-";

                let botonEliminar = document.createElement("button");
                botonEliminar.classList.add("btn", "btn-warning", "btn-round", "ms-2");
                let iconoBasura = document.createElement("i");
                iconoBasura.classList.add("fas", "fa-trash"); // Font Awesome icon for trash
                // Añadir los botones a la celda de acciones
                botonEliminar.appendChild(iconoBasura);
                accionCell.appendChild(botonMas);
                accionCell.appendChild(botonMenos);
                accionCell.appendChild(botonEliminar);


                // ** Agregar los eventos de los botones: **

                // Función para actualizar el subtotal
                function actualizarSubtotal(fila) {
                    const cantidad = parseInt(fila.cells[4].textContent) || 0;
                    const precioUnitario = parseFloat(fila.cells[5].textContent) || 0;
                    const subtotalCell = fila.cells[6];
                    subtotalCell.textContent = (cantidad * precioUnitario).toFixed(2);
                    fn_obtener_total(); // Recalcular los totales
                }

                // Función para manejar el botón de "+"
                botonMas.addEventListener("click", () => {
                    const fila = botonMas.closest("tr");
                    let cantidadCell = fila.cells[4];
                    let cantidad = parseInt(cantidadCell.textContent) || 0;
                    cantidadCell.textContent = cantidad + 1;
                    actualizarSubtotal(fila);
                });

                // Función para manejar el botón de "-"
                botonMenos.addEventListener("click", () => {
                    const fila = botonMenos.closest("tr");
                    let cantidadCell = fila.cells[4];
                    let cantidad = parseInt(cantidadCell.textContent) || 0;
                    if (cantidad > 1) {
                        cantidadCell.textContent = cantidad - 1;
                        actualizarSubtotal(fila);
                    }
                });

                // Función para manejar el botón de eliminar
                botonEliminar.addEventListener("click", () => {
                    const fila = botonEliminar.closest("tr");
                    fila.remove(); // Eliminar la fila
                    fn_obtener_total(); // Recalcular los totales después de eliminar
                });
                
            }
        } else if (tipoCorte === 'solocorte') {
            datosCorte.forEach(corte => {
                let nuevaFila = tabla.insertRow();

                nuevaFila.insertCell(0).textContent = corte.id; // ID
                nuevaFila.insertCell(1).textContent = corte.minutos; // Minutos
                nuevaFila.insertCell(2).textContent = corte.tarifa; // Minutos
                nuevaFila.insertCell(3).textContent = corte.costo; // Costo x Minuto
                nuevaFila.insertCell(4).textContent = 'Corte'; // Artículo
                nuevaFila.insertCell(5).textContent = '-'; // Cantidad fija por corte
                nuevaFila.insertCell(6).textContent = '-'; // Precio unitario
                nuevaFila.insertCell(7).textContent = (corte.costo).toFixed(2); // Subtotal

                let accionCell = nuevaFila.insertCell(8);

                let botonEliminar = document.createElement("button");
                botonEliminar.classList.add("btn", "btn-warning", "btn-round", "ms-2");
                let iconoBasura = document.createElement("i");
                iconoBasura.classList.add("fas", "fa-trash"); // Font Awesome icon for trash
                // Añadir los botones a la celda de acciones
                botonEliminar.appendChild(iconoBasura);
                accionCell.appendChild(botonEliminar);
                  // Función para manejar el botón de eliminar
                  botonEliminar.addEventListener("click", () => {
                    const fila = botonEliminar.closest("tr");
                    fila.remove(); // Eliminar la fila
                    fn_obtener_total(); // Recalcular los totales después de eliminar
                });

            });
            

        } else {
            // Si no se ingresa tiempo
            let nuevaFila = tabla.insertRow();

            

            nuevaFila.insertCell(0).textContent = datosArticulo["id"]; // ID
            nuevaFila.insertCell(1).textContent = '-'; // Minutos
            nuevaFila.insertCell(2).textContent = '-'; // Minutos

            nuevaFila.insertCell(3).textContent = '-'; // Costo x Minuto
            nuevaFila.insertCell(4).textContent = datosArticulo["articulo"]; // Artículo
            let cantidad = document.getElementById("cantidad_" + datosArticulo["id"]).textContent || 0;
            // let nuevaCelda = nuevaFila.insertCell(4);
             //let inputElemento = document.createElement('input');
             //inputElemento.type = 'text'; // Tipo de input
            // inputElemento.value = cantidad; // Asignar el valor del contadorCantidad
            //nuevaCelda.appendChild(inputElemento); // Añadir el input a la celda
            nuevaFila.insertCell(5).textContent = cantidad; // Cantidad
            nuevaFila.insertCell(6).textContent = datosArticulo["precio_venta"]; // Precio unitario
            nuevaFila.insertCell(7).textContent = (cantidad * parseFloat(datosArticulo["precio_venta"])).toFixed(2); // Subtotal

            let accionCell = nuevaFila.insertCell(8);
            let botonMas = document.createElement("button");
            botonMas.classList.add("btn", "btn-success","btn-round","ms-2");
            botonMas.textContent = "+";
            
            let botonMenos = document.createElement("button");
            botonMenos.classList.add("btn", "btn-danger","btn-round","ms-2");
            botonMenos.textContent = "-";

            let botonEliminar = document.createElement("button");
            botonEliminar.classList.add("btn", "btn-warning", "btn-round", "ms-2");
            let iconoBasura = document.createElement("i");
            iconoBasura.classList.add("fas", "fa-trash"); // Font Awesome icon for trash
            // Añadir los botones a la celda de acciones
            botonEliminar.appendChild(iconoBasura);
            accionCell.appendChild(botonMas);
            accionCell.appendChild(botonMenos);
            accionCell.appendChild(botonEliminar);

            // ** Agregar los eventos de los botones: **

            // Función para actualizar el subtotal
            function actualizarSubtotal(fila) {
                const cantidad = parseInt(fila.cells[4].textContent) || 0;
                const precioUnitario = parseFloat(fila.cells[5].textContent) || 0;
                const subtotalCell = fila.cells[6];
                subtotalCell.textContent = (cantidad * precioUnitario).toFixed(2);
                fn_obtener_total(); // Recalcular los totales
            }

            // Función para manejar el botón de "+"
            botonMas.addEventListener("click", () => {
                const fila = botonMas.closest("tr");
                let cantidadCell = fila.cells[4];
                let cantidad = parseInt(cantidadCell.textContent) || 0;
                cantidadCell.textContent = cantidad + 1;
                actualizarSubtotal(fila);
            });

            // Función para manejar el botón de "-"
            botonMenos.addEventListener("click", () => {
                const fila = botonMenos.closest("tr");
                let cantidadCell = fila.cells[4];
                let cantidad = parseInt(cantidadCell.textContent) || 0;
                if (cantidad > 1) {
                    cantidadCell.textContent = cantidad - 1;
                    actualizarSubtotal(fila);
                }
            });

            // Función para manejar el botón de eliminar
            botonEliminar.addEventListener("click", () => {
                const fila = botonEliminar.closest("tr");
                fila.remove(); // Eliminar la fila
                fn_obtener_total(); // Recalcular los totales después de eliminar
            });
        }
        fn_obtener_total();
    }
    
    function fn_obtener_total () {
        var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];
        var filas = tabla.getElementsByTagName("tr");
        var totalCorte = 0;
        var totalArticulos = 0;
        var total = 0;

        for (var i = 0; i < filas.length; i++) {
            var celdas = filas[i].getElementsByTagName("td");
            totalCorte += parseFloat(celdas[3].innerText) || 0;
            totalArticulos += (parseFloat(celdas[5].innerText) * parseFloat(celdas[6].innerText)) || 0;
            total += parseFloat(celdas[7].innerText) || 0;
        }

        var lbl_subtotal_cortes = document.getElementById("id_subtotal_cortes");
        var lbl_subtotal_articulos= document.getElementById("id_subtotal_articulos");
        var lbl_subtotal_general = document.getElementById("id_subtotal_general");

        lbl_subtotal_cortes.innerText = totalCorte.toFixed(2);
        lbl_subtotal_articulos.innerText = totalArticulos.toFixed(2);
        lbl_subtotal_general.innerText = total.toFixed(2);


   

    }

    function verificarSiArticuloExiste(idArticulo) {
        var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];
        var filas = tabla.getElementsByTagName("tr");

        for (var i = 0; i < filas.length; i++) {
            var celdas = filas[i].getElementsByTagName("td");
            var idFila = celdas[0].textContent; // Suponiendo que el ID está en la primera celda

            if (idFila == idArticulo) {
                return true; // Si se encuentra una coincidencia, retorna true
            }
        }
        return false; // Si no se encuentra ninguna coincidencia, retorna false
    }
    
</script>

<script>
    document.getElementById("btnRealizarReserva").addEventListener("click", function () {
  

        // Mostrar el modal manualmente
        const modal = new bootstrap.Modal(document.getElementById("modalRealizarPago"));
        modal.show();

        const subtotalGeneral = document.getElementById("id_subtotal_general").textContent;
        document.getElementById("montoTotal").value = subtotalGeneral; // Asignar el monto total

    });

    document.addEventListener("DOMContentLoaded", function () {
        const nombreCliente = document.getElementById("nombreCliente");
        const sugerencias = document.getElementById("sugerencias");
        const persona_id = document.getElementById("idPersona");
        nombreCliente.addEventListener("input", function () {
            const query = nombreCliente.value.trim();
            console.log(query)
            if (query.length > 0) {
                // Realiza la solicitud AJAX con jQuery
                $.ajax({
                    method: "POST",
                    url: "logica/clssFiltro.php",
                    data: {
                        "accion": "FILTROPERSONA",
                        "data": query
                    }
                }).done(function (response) {
                    try {
                        // Parsear la respuesta como JSON
                        console.log(response)
                        const resultados = JSON.parse(response);

                        // Limpiar las sugerencias actuales
                        sugerencias.innerHTML = "";

                        // Verificar si hay resultados
                        if (resultados.length > 0) {
                            resultados.forEach(persona => {
                                // Crear un elemento de lista para cada resultado
                                const item = document.createElement("div");
                                item.classList.add("list-group-item");
                                item.textContent = persona.persona_concatenada;

                                // Acción al seleccionar un resultado
                                item.addEventListener("click", function () {
                                    // Establecer el valor del input con el nombre seleccionado
                                    nombreCliente.value = persona.persona_concatenada;
                                    persona_id.textContent = persona.id
 

                                    // Limpiar las sugerencias
                                    sugerencias.innerHTML = "";
                                });

                                // Agregar el elemento a la lista de sugerencias
                                sugerencias.appendChild(item);
                            });
                        } else {
                            // Mostrar un mensaje si no hay resultados
                            const noResults = document.createElement("div");
                            noResults.classList.add("list-group-item", "text-muted");
                            noResults.textContent = "Sin resultados";
                            sugerencias.appendChild(noResults);
                        }
                    } catch (e) {
                        console.error("Error al procesar los resultados:", e);
                        sugerencias.innerHTML = ""; // Limpiar las sugerencias en caso de error
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.error("Error en la solicitud AJAX:", textStatus, errorThrown);
                    sugerencias.innerHTML = ""; // Limpiar las sugerencias en caso de fallo
                });
            } else {
                // Limpiar las sugerencias si no hay texto
                sugerencias.innerHTML = "";
            }
        });

        // Cerrar las sugerencias si se hace clic fuera del input o sugerencias
        document.addEventListener("click", function (e) {
            if (!nombreCliente.contains(e.target) && !sugerencias.contains(e.target)) {
                sugerencias.innerHTML = "";
            }
        });
    });


   

   
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    // Evento para el botón "Reservar"
    document.getElementById("Reservar").addEventListener("click", function () {
        var idCliente = document.getElementById('idPersona').textContent.trim();
        var total = document.getElementById("montoTotal").value;

        const userId = <?php echo $_SESSION['id']; ?>;
        console.log(idCliente);
        console.log(userId);

        const datos = {
            "usuario_id": userId,  // Puedes cambiar este valor dinámicamente si es necesario
            "cliente_id": idCliente,  // También este valor puede ser dinámico
            "total": total,
            "articulos": []
        };

        // Obtener todas las filas de la tabla (excepto el encabezado)
        const rows = document.querySelectorAll("#tabla_articulos tbody tr");

        // Recorrer todas las filas y obtener los datos de cada columna
        rows.forEach(function(row) {
            const articulo = {
                "articulo_id": row.cells[0].textContent,  // El ID del artículo
                "minutos": row.cells[1].textContent, 
                "costoxminuto": row.cells[2].textContent, 
                "precio_unitario": parseFloat(row.cells[6].textContent),  // Precio Unitario
                "cantidad": parseInt(row.cells[5].textContent),  // Cantidad
                "sub_total": parseFloat(row.cells[7].textContent)  // Subtotal
            };

            // Agregar el artículo al array
            datos.articulos.push(articulo);
        });

        // Mostrar los datos en la consola para verificar
        console.log(JSON.stringify(datos));

        $.ajax({
            method: "POST",
            url: "logica/clssVentaCorte.php",
            data: {
                "accion": "REGISTRARRESERVA",
                "data": JSON.stringify(datos)
            }
        }).done(function (response) {
            console.log(response);
            if(response.success){
                alert("Reserva registrada correctamente.");
            }
           
        }).fail(function (error) {
            console.error("Error:", error.responseText);
            alert("Error al registrar la reserva.");
        });
});


});
</script>



<?php
include("pie.php");
?>