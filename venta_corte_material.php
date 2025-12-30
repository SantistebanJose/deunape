<?php
include("cabecera.php");
include("logica/clssVenta.php");
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

$sucursal_id = $_SESSION["sucursal_id"];
?>

<style>
    .nav-item .nav-link:hover {
        background-color: #495057 !important;
        /* Cambia el color de fondo al pasar el mouse */
        color: #f8f9fa;
        /* Cambia el color del texto */
        cursor: pointer;
        /* Muestra un cursor de mano para indicar que es clickeable */
    }

    #tabla_articulos th:nth-child(1),
    #tabla_articulos td:nth-child(1),
    #tabla_articulos th:nth-child(10),
    #tabla_articulos td:nth-child(10),
    #tabla_articulos th:nth-child(11),
    #tabla_articulos td:nth-child(11),
    #tabla_articulos th:nth-child(12),
    #tabla_articulos td:nth-child(12) {
        display: none !important;
    }

    .error-input {
        border: 2px solid red;
    }

    .modal-header {
        background-color: rgb(255, 255, 255);
        /* Fondo azul */
        color: #2a2f5b;
        /* Texto blanco */
    }

    .error-message {
        color: red;
        font-size: 0.9em;
        margin-top: 5px;
    }

    .pagination {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 5px;
        margin: 10px 0;
    }

    .pagination a {
        text-decoration: none;
        padding: 8px 12px;
        border: 1px solid #ddd;
        color: #333;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .pagination a:hover {
        background-color: #f0f0f0;
    }

    .pagination a.active {
        background-color: #007bff;
        color: white;
    }

    /* Hacer que la paginación se ajuste en pantallas pequeñas */
    @media (max-width: 768px) {
        .pagination {
            font-size: 12px;
        }

        .pagination a {
            padding: 6px 10px;
        }

        table {
            font-size: 14px;
        }
    }

    @media (max-width: 480px) {
        .pagination {
            font-size: 10px;
        }

        .pagination a {
            padding: 5px 8px;
        }

        table {
            font-size: 12px;
        }
    }
</style>

<div
    class="container">
    <div class="page-inner">
        <div
            class="card">

            <div class="card-body">

                <h4 class="card-title"><i class="fas fa-child"></i> Atender Reservas</h4>
                <div class="mb-3">

                    <div class="card-sub">
                        Aquí podrás <strong>Atender la reservas realizados por tus clientes.</strong> Si te piden más Articulos, lo puedes seguir agrengando en la parte de abajo.
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table
                                        id="multi-filter-select"
                                        class="display table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Cliente</th>
                                                <th>Fecha</th>
                                                <th>Hora</th>
                                                <th>Estado</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php
                                            foreach (listarVentaReservaCorte() as $datosReserva) {
                                                $datosReservaJSON = json_encode($datosReserva);
                                            ?>
                                                <tr>

                                                    <td><?php echo $datosReserva["venta_id"] ?></td>
                                                    <td><i class="fas fa-user-check"></i> <?php echo $datosReserva["cliente"] ?></td>
                                                    <td><?php echo $datosReserva["fecha"] ?></td>
                                                    <td><?php echo $datosReserva["hora"] ?></td>
                                                    <td><?php echo $datosReserva["estado_venta"] ?></td>
                                                    <th>

                                                        <div class="mt-2 text-center">
                                                            <a
                                                                name=""
                                                                id=""
                                                                href="#panelDetalles"
                                                                class="btn btn-primary btn-round"

                                                                onclick='fn_consultarVenta(<?php echo $datosReservaJSON; ?>)'
                                                                role="button"><i class="fas fa-eye"></i> Ver</a>
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



                </div>
                <div
                    class="card">
                </div>


                <!-- Modal Solo Corte -->
                <div class="modal fade" id="modalSoloCorte" tabindex="-1" aria-labelledby="modalSoloCorteLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">


                            <div class="modal-body" id="contenido_solo_corte">

                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>
        <hr>

        <div class="row " id="panelAdicionarMas" style="display:none;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="card-title">Agrega Más Articulos, Impresiones, Escaneos a la Venta</div>
                    </div>


                    <div class="card-body">
                        <div class="d-flex justify-content-center flex-wrap text">
                            <ul class="nav d-flex flex-wrap justify-content-center">
                                <?php
                                foreach (listarMovimientos($sucursal_id) as $datos) {
                                    $datosJSON = json_encode($datos);
                                ?>
                                    <li class="nav-item me-3 mb-2">
                                        <button class="btn btn-secondary btn-round btn-sm btn-lg-sm" onclick='fn_servicios(<?php echo $datosJSON ?>)'>
                                            <i class="fas fa-external-link-alt"></i> <?php echo $datos["descripcion"] ?>
                                        </button>
                                    </li>
                                <?php
                                }
                                ?>
                                <li class="nav-item me-3 mb-2">
                                    <button class="btn btn-secondary btn-round btn-sm btn-lg-sm" id="btnAbrirModalSolo"><i class="fas fa-cut"></i> SOLO CORTE</button>
                                </li>
                                <li class="nav-item me-3">
                                    <button class="btn btn-secondary btn-round btn-sm btn-lg-sm" id="btnAbrirModalSolov2"><i class="fas fa-print"></i> IMPRESIÓN 3D</button>
                                </li>
                            </ul>
                        </div>


                        <br>

                        <div class="table-filters mb-3">
                            <div class="row justify-content-center align-items-center g-2">
                                <div class="col-md-2">
                                    <select id="filterCategoria" class="form-select" style="border-radius: 25px; border: 2px solid #6861ce;">
                                        <option value="">Filtrar por Categoría</option>
                                        <!-- Aquí se agregarán las opciones de categorías dinámicamente -->
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="filterTipo" class="form-select" style="border-radius: 25px; border: 2px solid #6861ce;">
                                        <option value="">Filtrar por Tipo</option>
                                        <!-- Aquí se agregarán las opciones de tipo dinámicamente -->
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="filterDimension" class="form-select" style="border-radius: 25px; border: 2px solid #6861ce;">
                                        <option value="">Filtrar por Dimensión</option>
                                        <!-- Aquí se agregarán las opciones de dimensión dinámicamente -->
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="filterColor" class="form-select" style="border-radius: 25px; border: 2px solid #6861ce;">
                                        <option value="">Filtrar por Color</option>
                                        <!-- Aquí se agregarán las opciones de dimensión dinámicamente -->
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button id="clearFilters" class="btn btn-warning btn-round btn-md" role="button"><i class="fas fa-broom"></i> Limpiar Filtros</button>
                                </div>
                            </div>
                        </div>

                        <!-- Buscador de texto -->
                        <div class="d-flex justify-content-center mt-4">
                            <input type="text" id="searchInput" class="form-control" placeholder="Buscar Articulo..." onkeyup="filterProducts()">
                        </div>


                        <!-- Contenedor para las tarjetas de productos -->
                        <div class="container mt-4">
                            <div class="row" id="productoContainer">
                                <!-- Los productos se generarán dinámicamente aquí -->
                            </div>
                        </div>

                        <!-- Paginación -->
                        <div id="pagination" class="d-flex justify-content-center mt-4">
                            <button id="prevPage" class="btn btn-secondary btn-round mx-2" onclick="changePage(-1)"><i class="fas fa-chevron-left"></i> Anterior </button>
                            <button id="nextPage" class="btn btn-secondary btn-round mx-2" onclick="changePage(1)">Siguiente <i class="fas fa-chevron-right"></i></button>
                        </div>




                        <script>
                            const products = <?php echo json_encode(listarProductosVenta1()); ?>;
                            let currentPage = 1;
                            const itemsPerPage = 6;
                            let filteredProducts = products; // Productos que se mostrarán después del filtro
                            const allCategories = [...new Set(products.map(product => product.categoria))]; // Lista única de categorías
                            const allTypes = [...new Set(products.map(product => product.tipo))]; // Lista única de tipos
                            const allDimensions = [...new Set(products.map(product => product.dimension))]; // Lista única de dimensiones
                            const allColores = [...new Set(products.map(product => product.color))]; // Lista única de colores

                            // Función para llenar los selectores de filtro
                            function populateFilters() {
                                const categorySelect = document.getElementById('filterCategoria');
                                const typeSelect = document.getElementById('filterTipo');
                                const dimensionSelect = document.getElementById('filterDimension');
                                const colorSelect = document.getElementById('filterColor');

                                allCategories.forEach(category => {
                                    categorySelect.innerHTML += `<option value="${category}">${category}</option>`;
                                });

                                allTypes.forEach(type => {
                                    typeSelect.innerHTML += `<option value="${type}">${type}</option>`;
                                });

                                allDimensions.forEach(dimension => {
                                    dimensionSelect.innerHTML += `<option value="${dimension}">${dimension}</option>`;
                                });
                                allColores.forEach(color => {
                                    colorSelect.innerHTML += `<option value="${color}">${color}</option>`;
                                });
                            }

                            // Función para renderizar los productos
                            function renderProducts(productsToDisplay) {
                                const container = document.getElementById('productoContainer');
                                container.innerHTML = '';
                                productsToDisplay.forEach(product => {
                                    const stock = parseFloat(product.stock);
                                    const flag_titulo = stock === 0.00 ? product.articulo + " - " + "SIN STOCK" : product.articulo;
                                    const flag_color = stock === 0.00 ? "#f31832" : "";
                                    const card = document.createElement('div');
                                    card.classList.add('col-md-4', 'mb-3');
                                    card.innerHTML = `
                                            <div class="card card-post card-round" style="color:${flag_color}">
                                                <div class="card-body">
                                                    <div class="d-flex">
                                                        <div class="info-post ms-2">
                                                            <p class="username">${flag_titulo}</p>
                                                        </div>
                                                    </div>
                                                    <div class="separator-solid"></div>
                                                    <p class="card-category text-info mb-1">
                                                        ${product.categoria}
                                                    </p>
                                                    <div class="card-text"><b>Tipo:</b> ${product.tipo}</div>
                                                    <div class="card-text"><b>Dimensión:</b> ${product.dimension}</div>
                                                    <div class="card-text"><b>Color:</b> ${product.color}</div>
                                                    <div class="card-text"><b>Stock:</b> ${product.stock}</div>

                                                    
                                                    <div class="separator-solid"></div>
                                                    <div class="row justify-content-between align-items-center g-2">
                                                        <div class="col-sm-8" style="font-size:18px"><b>S/${product.precio_venta}</b></div>
                                                        <div class="col-sm-4 d-flex justify-content-center">
                                                            <a href="#" class="btn btn-success btn-sm btn-round" onclick='fn_agregar_venta(${JSON.stringify(product)})'>
                                                                <i class="fas fa-plus"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                    container.appendChild(card);
                                });
                            }

                            // Función para cambiar la página
                            function changePage(direction) {
                                currentPage += direction;
                                if (currentPage < 1) currentPage = 1;
                                if (currentPage > totalPages()) currentPage = totalPages();
                                renderPage();
                            }

                            // Función para calcular el total de páginas
                            function totalPages() {
                                return Math.ceil(filteredProducts.length / itemsPerPage);
                            }

                            // Función para renderizar la página actual
                            function renderPage() {
                                const start = (currentPage - 1) * itemsPerPage;
                                const end = start + itemsPerPage;
                                const productsToDisplay = filteredProducts.slice(start, end);

                                renderProducts(productsToDisplay);

                                document.getElementById('prevPage').disabled = currentPage === 1;
                                document.getElementById('nextPage').disabled = currentPage === totalPages();
                            }

                            // Función para filtrar los productos por las opciones seleccionadas
                            function filterProducts() {
                                const searchText = document.getElementById('searchInput').value.toLowerCase();
                                const category = document.getElementById('filterCategoria').value;
                                const type = document.getElementById('filterTipo').value;
                                const dimension = document.getElementById('filterDimension').value;
                                const color = document.getElementById('filterColor').value;

                                filteredProducts = products.filter(product => {
                                    return (
                                        (category === '' || product.categoria === category) &&
                                        (type === '' || product.tipo === type) &&
                                        (dimension === '' || product.dimension === dimension) &&
                                        (color === '' || product.color === color) &&
                                        (product.articulo.toLowerCase().includes(searchText) || product.categoria.toLowerCase().includes(searchText) || product.tipo.toLowerCase().includes(searchText))
                                    );
                                });

                                currentPage = 1; // Volver a la primera página después de filtrar
                                renderPage();
                            }

                            // Función para limpiar los filtros
                            function clearFilters() {
                                document.getElementById('filterCategoria').value = '';
                                document.getElementById('filterTipo').value = '';
                                document.getElementById('filterDimension').value = '';
                                document.getElementById('filterColor').value = '';
                                document.getElementById('searchInput').value = '';
                                filteredProducts = products;
                                currentPage = 1;
                                renderPage();
                            }

                            // Inicializar los filtros y la página
                            populateFilters();
                            renderPage();

                            // Asociar eventos de filtro
                            document.getElementById('filterCategoria').addEventListener('change', filterProducts);
                            document.getElementById('filterTipo').addEventListener('change', filterProducts);
                            document.getElementById('filterDimension').addEventListener('change', filterProducts);
                            document.getElementById('filterColor').addEventListener('change', filterProducts);
                            document.getElementById('clearFilters').addEventListener('click', clearFilters);
                        </script>




                    </div>
                </div>
            </div>
        </div>
        <div id="panelDetalles" class="card border-primary" style="display:none;">
            <div class="card-body">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-center align-items-center md-2">
                            <h4 class="card-title">Reserva</h4>

                            <div class="card-sub">
                                Aquí podras el visualizar datos del cliente y de quien realizó la reserva
                            </div>
                            <div class="col-md-6">
                                <div class="card-body">
                                    <i class="fas fa-user-tie"></i> CLIENTE
                                    <div class="card-title" id="idClienteReservaDetalle"></div>
                                    <hr>
                                    <div><i class="fas fa-user-tie"></i><strong> Número de Celular:</strong> <span id="idNumCelClienteReserva">942781324</span> </div>
                                    <div><i class="fas fa-envelope"></i><strong> Correo Cliente:</strong> <span id="idCorreoClienteReserva">frvf2000@gmail.com</span> </div>
                                    <div><i class="fas fa-address-card"></i><strong> N° de Documento:</strong> <span id="idNumDocClienteReserva">73578005</span> </div>
                                </div>
                            </div>

                            <div class="col-md-6" style="border-left: 0.5px solid #6e6e6e; padding-left: 10px;">
                                <div class="card-body">
                                    <i class="fas fa-user-clock"></i> ATENDIDO POR
                                    <h4 class="card-title" id="idUsuarioReservaDetalle"></h4>
                                    <div>ID VENTA: <span id="idVentaReserva"></span></div>
                                    <hr>
                                    <div class="mb-3">
                                        <input
                                            type="date"
                                            class="form-control form-control-sm text-center"
                                            name=""
                                            id="idFechaReservaDetalle"
                                            aria-describedby="helpId"
                                            readonly />
                                    </div>
                                    <div class="mb-3">
                                        <input
                                            type="text"
                                            class="form-control form-control-sm text-center"
                                            name=""
                                            id="idHoraReservaDetalle"
                                            aria-describedby="helpId"
                                            readonly />

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="card text-start">
                    <div class="card-body">
                        <h4 class="card-title">Detalle de Venta</h4>
                        <div class="table-responsive">
                            <table id="tabla_articulos" class="table table-sm mt-3">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">MINUTOS</th>
                                        <th scope="col">Tarifa</th>
                                        <th scope="col">Total corte</th>
                                        <th scope="col">Articulo</th>
                                        <th scope="col">Cantidad</th>
                                        <th scope="col">Precio Unitario</th>
                                        <th scope="col">Sub Total (S/)</th>
                                        <th scope="col">Accion</th>
                                        <th scope="col">IDMOVIMIENTO</th>
                                        <th scope="col">IDRELARTICULO</th>
                                        <th scope="col">NOTA ARCHIVO</th>

                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card card-stats card-round">
                            <div class="card-body text-center">
                                <h5 id="label_total_cortes" class="card-title">Cortes (S/)</h5>
                                <span id="id_subtotal_cortes" style="font-size: 1.3rem;" aria-labelledby="label_total_cortes">xx.xx</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-stats card-round">
                            <div class="card-body text-center">
                                <h5 id="label_total_articulos" class="card-title">Artículos (S/)</h5>
                                <span id="id_subtotal_articulos" style="font-size: 1.3rem;" aria-labelledby="label_total_articulos">xx.xx</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-primary card-stats card-round">
                            <div class="card-body text-center">
                                <h5 id="label_total_general" class="card-title">Total Venta (S/)</h5>
                                <span id="id_subtotal_general" style="font-size: 1.3rem;" aria-labelledby="label_total_general">xx.xx</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <button id="btnRealizarPago" type="button" class="btn btn-success btn-round" style="width: 50%; height: 50px; font-size: 15px;">
                            <i class="fas fa-credit-card"></i> Realizar Pago
                        </button>
                    </div>

                </div>
            </div>
        </div>


    </div>


</div>
<div class=" modal fade" id="modalSoloCorteMaquina2" tabindex="-1" aria-labelledby="modalSoloCorteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-body">
                <div class="col-12 p-4 bg-light rounded">

                    <h4 class="card-title text-center"><i class="fas fa-print"></i> Máquina de Impresión 3D</h4>

                    <div class="card-sub text-center">
                        Aqui podras agregar los minutos y el precio del servicio de impresión.
                    </div>
                    <div class="mb-4">
                        <!-- Minutos Corte -->
                        <div class="text-center" style="flex: 1;">

                            <p class="mb-1"><strong>Minutos impresión</strong></p>
                            <div class="d-flex justify-content-center align-items-center mb-2">
                                <button id="btnRestarSoloCortev2" class="btn btn-danger btn-sm btn-round" onclick='fnAumentoOrResta("-")'><i class="fas fa-minus"></i></button>
                                <input id="cantidad_solocortev2" type="number" class="form-control text-center mx-2" value="10" style="width: 80px; font-size: 1.2rem;" />
                                <button id="btnSumarSoloCortev2" class="btn btn-success btn-sm btn-round" onclick='fnAumentoOrResta("+")'><i class="fas fa-plus"></i></button>

                            </div>
                        </div>


                        <div class="d-flex justify-content-center">
                            <button id="btnIncremento05SoloCortev2" class="btn btn-outline-primary btn-sm me-1 btn-round" style="font-size: 0.9rem;" onclick="fnAumentarMin(15)">15 Min.</button>
                            <button id="btnIncremento1SoloCortev2" class="btn btn-outline-primary btn-sm me-1 btn-round" style="font-size: 0.9rem;" onclick="fnAumentarMin(30)">30 Min.</button>
                            <button id="btnIncremento2SoloCortev2" class="btn btn-outline-primary btn-sm me-1 btn-round" style="font-size: 0.9rem;" onclick="fnAumentarMin(45)">45 Min.</button>
                            <button id="btnIncremento2SoloCortev2" class="btn btn-outline-primary btn-sm me-1 btn-round" style="font-size: 0.9rem;" onclick="fnAumentarMin(60)">1 Hor.</button>
                            <button id="btnIncremento2SoloCortev2" class="btn btn-outline-primary btn-sm me-1 btn-round" style="font-size: 0.9rem;" onclick="fnAumentarMin(120)">2 Hor.</button>
                            <button id="btnIncremento2SoloCortev2" class="btn btn-outline-primary btn-sm me-1 btn-round" style="font-size: 0.9rem;" onclick="fnAumentarMin(180)">3 Hor.</button>
                        </div>
                        <div class="card-sub text-center">
                            Puedes Seleccionar los minutos de manera rápida
                        </div>
                        <script>
                            function fnAumentoOrResta(accion) {
                                if (accion === "+") {
                                    var x = parseFloat(document.getElementById("cantidad_solocortev2").value)
                                    var x = x + 1;
                                    document.getElementById("cantidad_solocortev2").value = x;
                                } else if (accion === "-") {
                                    var x = parseFloat(document.getElementById("cantidad_solocortev2").value)
                                    var x = x - 1;
                                    document.getElementById("cantidad_solocortev2").value = x;
                                }

                            }

                            function fnAumentarMin(dato) {
                                document.getElementById("cantidad_solocortev2").value = dato;
                            }

                            function fnAumentaPrecioImpresion(dato) {
                                const acum = parseFloat(document.getElementById("precioSoloCortev2").value);
                                document.getElementById("precioSoloCortev2").value = acum + parseFloat(dato);
                            }

                            function limpiar() {
                                document.getElementById("precioSoloCortev2").value = 0;
                            }
                        </script>

                        <!-- Línea divisoria -->
                        <hr>


                        <!-- Precio Corte -->
                        <div class="text-center" style="flex: 1;">
                            <p class="mb-1"><strong>Precio Impresión</strong></p>
                            <div class="w-100 d-flex justify-content-center mb-1">

                                <input id="precioSoloCortev2" type="number" class="form-control text-center mx-2" value="1.5" style="width: 90px; font-size: 1.2rem;" />
                                <button id="" class="btn btn-danger btn-sm" onclick="limpiar()"><i class="fas fa-broom"></i></button>
                            </div>
                            <br>
                            <div class="d-flex justify-content-center">


                                <button id="btnIncremento05SoloCortev2" class="btn btn-outline-primary btn-sm me-1 btn-round" style="font-size: 0.9rem;" onclick="fnAumentaPrecioImpresion(0.5)">+0.5</button>
                                <button id="btnIncremento1SoloCortev2" class="btn btn-outline-primary btn-sm me-1 btn-round" style="font-size: 0.9rem;" onclick="fnAumentaPrecioImpresion(1)">+1</button>
                                <button id="btnIncremento2SoloCortev2" class="btn btn-outline-primary btn-sm me-1 btn-round" style="font-size: 0.9rem;" onclick="fnAumentaPrecioImpresion(2)">+2</button>
                                <button id="btnIncremento5SoloCortev2" class="btn btn-outline-primary btn-sm btn-round" style="font-size: 0.9rem;" onclick="fnAumentaPrecioImpresion(5)">+5</button>
                            </div>
                        </div>
                        <!-- Precio Corte -->
                        <hr>
                        <div class="text-center">
                            <p class="mb-1"><strong>Nota</strong></p>
                            <textarea class="form-control" name="" id="" rows="3" placeholder="Escribe por ejemplo: Maquina de 3D"></textarea>

                        </div>

                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-secondary rounded-5" id="btn_agregar_solocortev2"><i class="fas fa-plus-circle"></i> Agregar</button>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger rounded-5" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Unificado -->
<div class="modal fade " data-bs-backdrop="static" id="modalCantidad" tabindex="-1" aria-labelledby="modalCantidadCorteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-body" id="contenid_cantidad">

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger rounded-5" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>

    </div>
</div>


<!-- Modal  -->
<div class="modal fade" id="modalGenerico" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalGenericoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-body" id="modalContent">
                <!-- Contenido dinámico se cargará aquí -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger rounded-5" data-bs-dismiss="modal">Cerrar</button>

            </div>
        </div>
    </div>
</div>
<!-- Modal Body -->
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
                            <h4 class="card-title fs-1 fw-bold"> <i class="fas fa-credit-card"></i> Realizar Pago </h4>
                            <h1 class="card-title fw-bold" style="font-size: 3rem;"> S/ <span id="idMontoVentaTitulo">#</span></h1>
                        </div>
                        <!--<div class="card-body text-center">
                            <h1 class="card-title">S/ xx.xx</h1>
                        </div>-->

                        <div class="card-sub">
                            Aquí realiza tus pagos
                        </div>
                        <div>
                            <span>ID Venta: <span id="idVenta">#</span></span> |
                            <span>ID Cliente: <span id="idPersona">#</span></span> |
                            <span>ID Usuario Reserva: <span id="idUsuario">#</span>
                                <br>
                                <span><strong>Atendiendo la Transacción:</strong> <span id="idAtencionFinal"><?php echo $id_usuario_s . "-" . $nombre . ", " . $ape_usuario ?></span></span>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label for="" class="form-label"><strong><i class="fas fa-user-tie"></i> Cliente</strong></label>
                            <input
                                type="text"
                                class="form-control"
                                name=""
                                id="nombreCliente"
                                aria-describedby="helpId"
                                placeholder="AGREGAR EL NOMBRE DEL CLIENTE" readonly />
                        </div>
                        <div class="row justify-content-center align-items-center g-2">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><i class="fas fa-phone-square"></i> <strong> Número de Telefono</strong></label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        name=""
                                        id="idUpdateNumTelefonoCliente"
                                        aria-describedby="helpId"
                                        placeholder="" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><i class="fas fa-envelope"></i><strong> Correo Electronico</strong></label>
                                    <input
                                        type="email"
                                        class="form-control"
                                        name=""
                                        id="idUpdateCorreoCliente"
                                        aria-describedby="helpId"
                                        placeholder="" />
                                </div>
                            </div>
                        </div>
                        <hr>

                        <!-- Monto Total -->
                        <div
                            class="row justify-content-center align-items-center md-2">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="montoTotal" class="form-label"><strong>Monto Original de Venta</strong> </label>
                                    <div class="input-group">
                                        <span class="input-group-text">S/</span>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="montoTotal"
                                            readonly />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="montoVentaFinal" class="form-label"><strong>Monto Final de Venta</strong></label>
                                    <div class="input-group">
                                        <span class="input-group-text">S/</span>
                                        <input
                                            type="number"
                                            class="form-control"
                                            id="montoTotalFinal"
                                            placeholder="Monto con descuento a clientes" />
                                    </div>
                                </div>
                            </div>

                        </div>


                        <div class="card-body">
                            <ul class="nav nav-pills nav-secondary  nav-pills-no-bd nav-pills-icons justify-content-center" id="pills-tab-with-icon" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="pills-home-tab-icon" data-bs-toggle="pill" href="#pago-directo" role="tab" aria-controls="pago-directo" aria-selected="true">
                                        Pago Directo
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-profile-tab-icon" data-bs-toggle="pill" href="#pago-credito" role="tab" aria-controls="pago-credito" aria-selected="false">
                                        Pago Crédito
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content mt-2 mb-3" id="pills-with-icon-tabContent">
                                <div class="tab-pane fade show active" id="pago-directo" role="tabpanel" aria-labelledby="pills-home-tab-icon">
                                    <form id="form-pago-directo">
                                        <div id="panel_forma_pago" class="mb-3">
                                            <div class="card-sub">
                                                <div class="text-center">
                                                    Aquí podrás elegir si realizan pagos Directo.
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <div class="form-group">
                                                    <label class="form-label d-block"><strong>Seleccione Tipo de Comprobante</strong></label>
                                                    <div class="selectgroup selectgroup-secondary selectgroup-pills">
                                                        <label class="selectgroup-item">
                                                            <input
                                                                type="radio"
                                                                name="icon-input"
                                                                value="boleta"
                                                                checked=""
                                                                class="selectgroup-input"
                                                                id="boleta" />
                                                            <span class="selectgroup-button selectgroup-button-icon">Boleta</span>
                                                        </label>
                                                        <label class="selectgroup-item">
                                                            <input
                                                                type="radio"
                                                                name="icon-input"
                                                                value="factura"
                                                                class="selectgroup-input"
                                                                id="factura" />
                                                            <span class="selectgroup-button selectgroup-button-icon">Factura</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div id="facturaInputs" style="display: none;">
                                                    <div class="card text-start">

                                                        <div class="card-body">
                                                            <h4 class="card-title" style="font-size: 14px;">Ingresé datos del Ruc para Factura</h4>
                                                            <div class="row justify-content-center align-items-center g-2">
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <input type="text" id="idRucReceptor" class="form-control" placeholder="Ingrese el RUC" />
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <input type="text" id="idRazonSocialReceptor" class="form-control" placeholder="Ingrese la Razón Social" />
                                                                    </div>
                                                                </div>


                                                            </div>
                                                        </div>
                                                    </div>





                                                </div>
                                            </div>

                                            <script>
                                                const boletaRadio = document.getElementById('boleta');
                                                const facturaRadio = document.getElementById('factura');
                                                const facturaInputs = document.getElementById('facturaInputs');


                                                function toggleFacturaInputs() {
                                                    if (facturaRadio.checked) {
                                                        facturaInputs.style.display = 'block';

                                                    } else {
                                                        facturaInputs.style.display = 'none';
                                                    }
                                                }

                                                boletaRadio.addEventListener('change', toggleFacturaInputs);
                                                facturaRadio.addEventListener('change', toggleFacturaInputs);

                                                toggleFacturaInputs();
                                            </script>

                                            <!--<label for="" class="form-label"><strong>Forma de Pago</strong></label> -->
                                            <div class="text-center">
                                                <button id="btnAgregarPago" class="btn btn-secondary btn-sm" type="button"> <i class="fas fa-plus"></i> Agregar Monto (S/) Forma de Pago</button>
                                            </div>
                                            <br>
                                            <!-- Botón de agregar más formas de pago -->


                                            <div class="d-flex align-items-center">
                                                <!-- Select de formas de pago -->
                                                <select class="form-select form-select-md" name="formaPago" id="formaPagoSelect">
                                                    <?php
                                                    foreach (listarFormaPago() as $datosFormaPago) {
                                                        $datosFormaPagoJSON = json_encode($datosFormaPago);
                                                    ?>
                                                        <option value="<?php echo $datosFormaPago["id"] ?>"><?php echo $datosFormaPago["nombre"] ?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>

                                                <!-- Caja de texto para monto -->
                                                <input type="number" class="form-control form-control-md ms-2" placeholder="Monto" min="0" name="monto" id="montoSelect_0">
                                            </div>

                                            <!-- Contenedor para los selects adicionales -->
                                            <div id="contenedorPagos" class="mt-3"></div>
                                        </div>

                                        <hr>

                                        <!-- Monto Total -->
                                        <!--
                                        <div id="panelMontos" class="row justify-content-center align-items-center g-2">
                                            <div class="col-md-4">
                                                <label for="" class="form-label"><b>Falta Pagar</b></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">S/</span>
                                                    <input type="number" class="form-control" name="faltaPagar" placeholder="" readonly />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="" class="form-label"><b>Paga Con</b></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">S/</span>
                                                    <input type="number" class="form-control" name="pagaCon" placeholder="" />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="" class="form-label"><b>Vuelto (S/)</b></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">S/</span>
                                                    <input type="number" class="form-control" name="vuelto" placeholder="" />
                                                </div>
                                            </div>
                                            <br>
                                        </div>
                                        -->
                                    </form>
                                    <div class="text-center">
                                        <a class="btn btn-success btn-round ms-2" href="#" role="button" onclick="fn_pagar_directo()"> <i class="fas fa-hand-holding-usd"></i> Pagar</a>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="pago-credito" role="tabpanel" aria-labelledby="pills-profile-tab-icon">

                                    <div class="card-sub">

                                        <div class="text-center">
                                            <strong>Aquí podrás elegir si realizan pagos al Crédito.</strong>
                                            <br>
                                            Si un cliente te deja pagado algo de la venta, <strong>REGISTRALO</strong>.
                                            <br>
                                            Si no, dejalo en blanco y darle click al boton <br><strong>Realizar Pago a Credito</strong>
                                        </div>
                                    </div>



                                    <!-- Formulario para el pago a crédito -->
                                    <form id="form-pago-credito">

                                        <!-- Botón de agregar más formas de pago -->
                                        <div class="text-center">
                                            <button id="btnAgregarPagoCredito" class="btn btn-secondary btn-sm" type="button"> <i class="fas fa-plus"></i> Agregar Monto (S/) Forma de Pago</button>
                                        </div>
                                        <br>


                                        <!-- Primer campo de pago -->
                                        <div class="d-flex align-items-center" id="pagoCredito_0">
                                            <!-- Select de formas de pago -->
                                            <select class="form-select form-select-md" name="formaPagoCredito[]" id="formaPagoCreditoSelect_0">
                                                <?php
                                                foreach (listarFormaPago() as $datosFormaPago) {
                                                    echo '<option value="' . $datosFormaPago["id"] . '">' . $datosFormaPago["nombre"] . '</option>';
                                                }
                                                ?>
                                            </select>

                                            <!-- Caja de texto para monto -->
                                            <input type="number" class="form-control form-control-md ms-2" placeholder="Monto" min="0" name="montoCredito[]" id="montoSelectCredito_0">
                                        </div>

                                        <!-- Contenedor para los selects adicionales -->
                                        <div id="contenedorPagosCredito" class="mt-3"></div>

                                        <br>

                                        <!-- Botón para realizar el pago -->
                                        <div class="text-center">
                                            <a class="btn btn-success btn-round ms-2" href="#" role="button" onclick="fn_pagar_credito()"> <i class="fas fa-hands-helping"></i> Realizar Pago a Credito</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
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

<!-- Optional: Place to the bottom of scripts -->
<script>
    const myModal = new bootstrap.Modal(
        document.getElementById("modalId"),
        options,
    );
</script>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="assets/js/scriptNotify.js"></script>

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

        var tabla_2 = $("#multi-filter-select2").DataTable({
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

        // Llenar el filtro de Categoría con valores únicos
        tabla_2.column(1).data().unique().sort().each(function(d, j) {
            if (d !== "") {
                $('#filterCategoria').append('<option value="' + d + '">' + d + '</option>');
            }
        });

        // Llenar el filtro de Dimensión con valores únicos
        tabla_2.column(3).data().unique().sort().each(function(d, j) {
            if (d !== "") {
                $('#filterDimension').append('<option value="' + d + '">' + d + '</option>');
            }
        });

        // Llenar el filtro de Tipo con valores únicos
        tabla_2.column(2).data().unique().sort().each(function(d, j) {
            if (d !== "") {
                $('#filterTipo').append('<option value="' + d + '">' + d + '</option>');
            }
        });

        // Filtrar por Categoría
        $('#filterCategoria').on('change', function() {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            tabla_2.column(1).search(val ? "^" + val + "$" : "", true, false).draw();
        });

        // Filtrar por Tipo
        $('#filterTipo').on('change', function() {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            tabla_2.column(2).search(val ? "^" + val + "$" : "", true, false).draw();
        });

        // Filtrar por Dimensión
        $('#filterDimension').on('change', function() {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            tabla_2.column(3).search(val ? "^" + val + "$" : "", true, false).draw();
        });

        // Limpiar los filtros al hacer clic en el botón
        $('#clearFilters').on('click', function() {
            // Limpiar las selecciones de los filtros
            $('#filterCategoria').val('');
            $('#filterTipo').val('');
            $('#filterDimension').val('');

            // Restablecer los filtros de la tabla
            tabla_2.columns().search('').draw();
        });

    });
</script>

<script>
    function fn_servicios(jsDatos) {
        let ploteoEditando = null; // Variable para guardar el ploteo que se está editando
        console.log(jsDatos);

        // Obtener el array de medidas, eliminando los corchetes y separando por coma
        const medidasArray = jsDatos["medidas"]
            .slice(1, -1) // Elimina las llaves '{' y '}'
            .split(','); // Divide por coma (',') para obtener los elementos como array

        console.log(medidasArray);

        let modalContent = `
        <div class="text-center">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Servicio de ${jsDatos["descripcion"]}</h4>
                    <div>ID: <span id="id_mov_${jsDatos["descripcion"]}">${jsDatos["id"]}</span></div>
                    <div class="card-sub">Aquí ingresa lo que mandaron a ${jsDatos["descripcion"]} de manera general</div>
                </div>
                <div class="card-body">
                    <p class="card-text">Cantidad de ${jsDatos["descripcion"]}</p>
                    <div class="d-flex align-items-center justify-content-center">
                        <button id="btn_menos_${jsDatos["descripcion"]}" class="btn btn-danger btn-round me-2">-</button>
                        <input id="input_cantidad_${jsDatos["descripcion"]}" class="text-center" type="text" value="1" style="width: 50px;height: 40px;" oninput="validarNumero(event)">
                        <button id="btn_mas_${jsDatos["descripcion"]}" class="btn btn-success btn-round ms-2">+</button>
                    </div>
                </div>

                <div class="card-body">
                    <p class="card-text">Dimensión</p>
                    <div id = "contenedor-medidas" class="selectgroup selectgroup-pills">`;
        
                    medidasArray.forEach((elemento) => {
                        modalContent += `
                        <label class="selectgroup-item">
                            <input
                                type="checkbox"
                                name="value"
                                value="${elemento}"
                                class="selectgroup-input"    
                            />
                            <span class="selectgroup-button">${elemento}</span>
                        </label>
                    `;
                    });

        modalContent += `
                    </div>
                </div>
                
                <div class="card-body">
                    <p class="card-text">Monto (S/)</p>
                    <input type="number" id="monto_${jsDatos["descripcion"]}" class="form-control" placeholder="Monto (S/)">
                </div>


                <div class="card-body">
                    <p class="card-text">Detalle</p>
                    <textarea rows="3" cols="30" id="datelle_material_${jsDatos["descripcion"]}" class="form-control" placeholder="Agrega como: Corte por Material Restante"></textarea>
                </div>


                <div class="text-center">
                    <button class="btn btn-secondary rounded-5" id="btnAgregar${jsDatos["descripcion"]}" role="button">Añadir a la Venta</button>
                </div>

                <br>
            </div>
        </div>
    `;


        document.getElementById('modalContent').innerHTML = modalContent;


        setTimeout(() => {

            document.getElementById("btn_mas_" + jsDatos["descripcion"]).addEventListener("click", function() {
                let cantidad = parseInt(document.getElementById("input_cantidad_" + jsDatos["descripcion"]).value);
                document.getElementById("input_cantidad_" + jsDatos["descripcion"]).value = cantidad + 1;
            });

            document.getElementById("btn_menos_" + jsDatos["descripcion"]).addEventListener("click", function() {
                let cantidad = parseInt(document.getElementById("input_cantidad_" + jsDatos["descripcion"]).value);
                if (cantidad > 1) {
                    document.getElementById("input_cantidad_" + jsDatos["descripcion"]).value = cantidad - 1;
                }
            });


            document.getElementById('btnAgregar' + jsDatos["descripcion"]).addEventListener('click', function() {
                const cantidad = parseInt(document.getElementById('input_cantidad_' + jsDatos["descripcion"]).value) || 1;
                const monto = parseFloat(document.getElementById('monto_' + jsDatos["descripcion"]).value) || 0;
                const detalle_nota = document.getElementById('datelle_material_' + jsDatos["descripcion"]).value;
                const inputMonto = document.getElementById('monto_' + jsDatos["descripcion"]);
                const mensajeErrorExistente = document.querySelector('.error-message');
                if (mensajeErrorExistente) mensajeErrorExistente.remove();
                inputMonto.classList.remove('error-input');
                let textoDimensiones = obtenerValoresSeleccionados();
                let detalle_notaFormato = detalle_nota ? " / " + detalle_nota  : "";
                console.log("Texto Dimensiones: " + textoDimensiones);
                // Validar que el monto haya sido ingresado y sea mayor a 0
                if (isNaN(monto) || monto <= 0) {
                    // Añadir clase para resaltar el error
                    inputMonto.classList.add('error-input');

                    // Crear mensaje de error
                    const mensajeError = document.createElement('div');
                    mensajeError.textContent = 'Por favor, ingresa un monto válido mayor a 0.';
                    mensajeError.classList.add('error-message');

                    // Insertar mensaje debajo del input
                    inputMonto.parentNode.appendChild(mensajeError);
                    return; // Detener ejecución si el monto no es válido
                }

                // Si no estamos editando, agregar un nuevo ploteo
                console.log("Datos CTMRE")
                console.log(jsDatos)
                const datos = [{
                    id: '0', // ID del ploteo
                    descripcion:textoDimensiones ? jsDatos["descripcion"] + ' (' + textoDimensiones + ') / '+ detalle_nota : jsDatos["descripcion"] + detalle_notaFormato ,
                    medidas: medidasArray,
                    cantidad: cantidad,
                    monto: '-', // Monto
                    subtotal: monto, // Subtotal
                    articulo: textoDimensiones ? jsDatos["descripcion"] + ' (' + textoDimensiones + ') / '+ detalle_nota : jsDatos["descripcion"] + detalle_notaFormato ,
                    idmovimiento: jsDatos["id"],
                    dimension: textoDimensiones,
                    nota_archivo: textoDimensiones.trim() === "" ? "Sin nota" : textoDimensiones ? ' (' + textoDimensiones + ') / '+ detalle_nota : jsDatos["descripcion"] + detalle_notaFormato 
                }];
                console.log(datos)

                const data = datos[0];
                let venta_id_lbl = document.getElementById('idVentaReserva').textContent;
                try {

                    const response = fn_insert_movimiento(venta_id_lbl, data["idmovimiento"], data["cantidad"], data["nota_archivo"], data["subtotal"]);
                    console.log("Movimiento insertado con éxito:", response);


                    document.getElementById('input_cantidad_' + jsDatos["descripcion"]).value = 0; // Reset cantidad
                    document.getElementById('monto_' + jsDatos["descripcion"]).value = ''; // Reset monto
                    // Cerrar modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalGenerico'));
                    if (modal) modal.hide();


                } catch (error) {
                    // Manejar el error
                    console.error("Error al insertar movimiento:", error.message);
                }

                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalGenerico'));
                if (modal) modal.hide();
                showNotification("success");
            });

        }, 0); // Usar setTimeout para asegurar que el DOM esté listo

        // Mostrar el modal
        const modal = new bootstrap.Modal(document.getElementById('modalGenerico'));
        modal.show();
    }
</script>
<script>
    function obtenerValoresSeleccionados() {
        let seleccionados = [];
        document.querySelectorAll('#contenedor-medidas .selectgroup-input:checked').forEach((checkbox) => {
            seleccionados.push(checkbox.value);
        });
        return seleccionados.join(", "); // Convierte el array en un string separado por comas
    }
</script>

<!--FRANCO -->
<script>
    function validarNumero(event) {
        // Eliminar cualquier cosa que no sea un número
        event.target.value = event.target.value.replace(/[^0-9]/g, '');
    }

    const btnMas = document.getElementById('btn_mas');
    const btnContador = document.getElementById('id_contador');
    const inputNumero = document.getElementById('input_numero');

    const btnMasImpresion = document.getElementById('btn_mas_impresion');
    const btnContadorImpresion = document.getElementById('id_contador_impresion');
    const inputNumeroImpresion = document.getElementById('input_numero_impresion');

    btnMas.addEventListener('click', () => {
        let currentValue = parseInt(inputNumero.value);
        if (!isNaN(currentValue) && currentValue > 1) {
            inputNumero.value = currentValue - 1;
        }
    });

    btnMasImpresion.addEventListener('click', () => {
        let currentValue = parseInt(inputNumeroImpresion.value);
        if (!isNaN(currentValue) && currentValue > 1) {
            inputNumeroImpresion.value = currentValue - 1;
        }
    });


    btnContador.addEventListener('click', () => {
        let currentValue = parseInt(inputNumero.value);
        if (!isNaN(currentValue)) {
            inputNumero.value = currentValue + 1;
        }
    });

    btnContadorImpresion.addEventListener('click', () => {
        let currentValue = parseInt(inputNumeroImpresion.value);
        if (!isNaN(currentValue)) {
            inputNumeroImpresion.value = currentValue + 1;
        }
    });
</script>
<!--FRANCO -->
<script>
    // Variables para manejar los selects y montos adicionales
    const btnAgregarPago = document.getElementById('btnAgregarPago');
    const contenedorPagos = document.getElementById('contenedorPagos');
    let contador = 1; // Para numerar los campos adicionales

    // Evento para agregar más selects con montos
    btnAgregarPago.addEventListener('click', function() {
        // Crear un contenedor para el nuevo select y su campo de monto
        const nuevoContenedor = document.createElement('div');
        nuevoContenedor.classList.add('d-flex', 'align-items-center', 'mb-2');

        // Crear un nuevo select
        const nuevoSelect = document.createElement('select');
        nuevoSelect.classList.add('form-select', 'form-select-md', 'me-2');
        nuevoSelect.name = 'formaPago_' + contador; // Agregar nombre dinámico
        nuevoSelect.innerHTML = `<?php
                                    foreach (listarFormaPago() as $datosFormaPago) {
                                        echo '<option value="' . $datosFormaPago["id"] . '">' . $datosFormaPago["nombre"] . '</option>';
                                    }
                                    ?>`;

        // Crear una nueva caja de texto para el monto
        const nuevoInputMonto = document.createElement('input');
        nuevoInputMonto.type = 'number';
        nuevoInputMonto.classList.add('form-control', 'form-control-md', 'ms-2');
        nuevoInputMonto.placeholder = 'Monto';
        nuevoInputMonto.min = '0';
        nuevoInputMonto.name = 'monto_' + contador; // Agregar nombre dinámico
        nuevoInputMonto.id = 'montoSelect_' + contador;

        // Crear un botón de eliminación pequeño
        const btnEliminar = document.createElement('button');
        btnEliminar.type = 'button';
        btnEliminar.classList.add('btn', 'btn-danger', 'btn-sm', 'ms-2', 'btn-round'); // Clase btn-sm para hacerlo más pequeño
        btnEliminar.innerHTML = '<i class="fas fa-times"></i>'; // Texto del botón
        btnEliminar.addEventListener('click', function() {
            contenedorPagos.removeChild(nuevoContenedor); // Eliminar el contenedor
        });

        // Agregar el select, el input y el botón de eliminación al contenedor
        nuevoContenedor.appendChild(nuevoSelect);
        nuevoContenedor.appendChild(nuevoInputMonto);
        nuevoContenedor.appendChild(btnEliminar);

        // Agregar el contenedor al contenedor principal
        contenedorPagos.appendChild(nuevoContenedor);

        // Incrementar el contador para los nuevos inputs
        contador++;
    });

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
        btnEliminarCredito.classList.add('btn', 'btn-danger', 'btn-sm', 'ms-2', 'btn-round'); // Clase btn-sm para hacerlo más pequeño
        btnEliminarCredito.innerHTML = '<i class="fas fa-times"></i>'; // Texto del botón
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

<!-- FRANCO -->
<script>
    function fn_pagar_directo() {

        const radiosFormaPago = document.querySelectorAll('input[name="icon-input"]');
        //

        let radioSeleccionado;
        radiosFormaPago.forEach(radio => {
            if (radio.checked) {
                radioSeleccionado = radio.value;
            }
        });
        var datosSerializados = $('#form-pago-directo').serializeArray();

        console.log(datosSerializados); // Ver los datos serializados como un array de objetos

        //////////////////////////////////////////////////////
        var numTelefonoUpdate = document.getElementById('idUpdateNumTelefonoCliente').value;
        //////////////////////////////////////////////////////////////////////////
        var idVenta = document.getElementById('idVenta').textContent;
        var idPersona = document.getElementById('idPersona').textContent;
        var idUsuario = document.getElementById('idUsuario').textContent;
        var idAtencionFinal = document.getElementById('idAtencionFinal').textContent;
        var numUpdateTelefonoPersona = document.getElementById('idUpdateNumTelefonoCliente').value;
        ////

        var montoOriginal = parseFloat(document.getElementById('montoTotal').value);
        var montoFinal = parseFloat(document.getElementById('montoTotalFinal').value);

        if (isNaN(montoFinal)) {
            montoFinal = montoOriginal;
        };



        ///////////////////////////////////////////////////////

        var js_detalle_pago = [];

        var formaPago = null;
        var monto = null;
        var acumMontos = 0;
        for (var i = 0; i < datosSerializados.length; i++) {
            var dato = datosSerializados[i];

            if (dato.name.startsWith('formaPago')) {
                formaPago = dato.value;
            }

            if (dato.name.startsWith('monto')) {
                monto = parseFloat(dato.value);
                acumMontos = acumMontos + monto;
            }
            if (formaPago && monto) {
                js_detalle_pago.push({
                    "venta_id": idVenta,
                    "id_forma_pago": formaPago,
                    "monto_forma_pago": monto
                });
                formaPago = null;
                monto = null;
            }
        };
        var js_detalles_receptor_factura;
        js_detalles_receptor_factura = {
            "ruc": document.getElementById("idRucReceptor").value,
            "razon_social": document.getElementById("idRazonSocialReceptor").value
        }
        var js_venta = {
            "tipo_comprobante": radioSeleccionado,
            "js_detalles_receptor_factura": js_detalles_receptor_factura,
            "venta_id": idVenta,
            "atencion_final_usuario": idAtencionFinal,
            "numUpdateTelefonoPersona": numUpdateTelefonoPersona,
            "monto_original": montoOriginal,
            "monto_venta_final": montoFinal,
            "js_detalle_pagos": js_detalle_pago
        };
        console.log("Datos de la venta")
        console.log(js_venta)
        var js_for_pago = {
            "venta_id": idVenta,
            "monto_original": montoOriginal,
            "monto_venta_final": montoFinal,
            "comentario": ""
        };
        //monto_forma_pago
        

        console.log("js_detalles_receptor_factura: " + js_detalles_receptor_factura)
        console.log(js_detalles_receptor_factura)
        console.log(radioSeleccionado)
        //
        if (radioSeleccionado === "factura" && (document.getElementById("idRucReceptor").value).trim()==="") {
            console.log("ando aquiii")
            swal("Ups!, Falta emitir una FACTURA debes de ingresar el Ruc y Razon Social","Agrega los datos correspondientes", {
                icon: "error",
                buttons: {
                    confirm: {
                        className: "btn btn-danger",
                    },
                },
            });
        }else if (js_detalle_pago.length === 0) {
            swal("Ups!, Falta Agregar los monto de acuerdo a forma de Pago", "Agrega los montos :)", {
                icon: "error",
                buttons: {
                    confirm: {
                        className: "btn btn-danger",
                    },
                },
            });
            console.log("Falta Agregar los Metodos de Pago");
        } else if (acumMontos > montoFinal) {
            swal("Ups!, Los montos ingresados son MAYORES al Monto final de la venta", "Agrega correctamente los montos :)", {
                icon: "error",
                buttons: {
                    confirm: {
                        className: "btn btn-danger",
                    },
                },
            });

        } else if (acumMontos < montoFinal) {
            swal("Ups!, Los montos ingresados son MENORES al Monto final de la venta", "Agrega correctamente los montos :)", {
                icon: "error",
                buttons: {
                    confirm: {
                        className: "btn btn-danger",
                    },
                },
            });

        } else {
            console.log("js_detalle_pago", js_detalle_pago);
            console.log("CELULAR UPDATE", numTelefonoUpdate);

            console.log("js_detalle_pago final: ", js_detalle_pago);

            $.ajax({
                url: 'logica/clssInsertPA.php',
                type: 'POST',
                data: {
                    accion: 'FINALIZARVENTA',
                    jsDatosVenta: JSON.stringify(js_venta)
                },
                success: function(response) {

                    console.log("Respuesta del servidor: ", response);

                    try {
                        var result = JSON.parse(response);
                        if (result.estado === true) {
                            swal({
                                title: "Pagado con Exito!",
                                text: result.mensaje,
                                icon: "success",
                                buttons: false,
                                timer: 1500
                            }).then(() => {
                                //window.open("http://localhost/caracol_soft_vysam/ticket.php?id=" + parseInt(idVenta), "_blank");
                                window.open("ticket.php?id=" + parseInt(idVenta), "_blank");
                                location.reload();
                            });;
                        } else {
                            swal("Error", result.mensaje, {
                                icon: "error",
                                buttons: {
                                    confirm: {
                                        className: "btn btn-danger",
                                    },
                                },
                            });
                        }
                    } catch (e) {
                        console.log("Error al parsear el JSON: ", e);
                        swal("Error", "No se pudo procesar la respuesta del servidor.", {
                            icon: "error",
                            buttons: {
                                confirm: {
                                    className: "btn btn-danger",
                                },
                            },
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Error: " + error);
                    swal("Error", "Hubo un problema con la solicitud.", {
                        icon: "error",
                        buttons: {
                            confirm: {
                                className: "btn btn-danger",
                            },
                        },
                    });
                }
            });
        }
    }

    function fn_pagar_credito() {


        //////////////////////////////////////////////////////
        var numTelefonoUpdate = document.getElementById('idUpdateNumTelefonoCliente').value;
        //////////////////////////////////////////////////////////////////////////
        var idVenta = document.getElementById('idVenta').textContent;
        var idPersona = document.getElementById('idPersona').textContent;
        var idUsuario = document.getElementById('idUsuario').textContent;
        var idAtencionFinal = document.getElementById('idAtencionFinal').textContent;
        var numUpdateTelefonoPersona = document.getElementById('idUpdateNumTelefonoCliente').value;
        ////

        var montoOriginal = parseFloat(document.getElementById('montoTotal').value);
        var montoFinal = parseFloat(document.getElementById('montoTotalFinal').value);

        if (isNaN(montoFinal)) {
            montoFinal = montoOriginal;
        };

        var datosSerializadosCredito = $('#form-pago-credito').serializeArray();
        console.log(datosSerializadosCredito);

        var js_detalle_deuda = [];

        var formaPagoCredito = null;
        var montoCredito = null;
        var acumMontos = 0;
        for (var i = 0; i < datosSerializadosCredito.length; i++) {
            var dato = datosSerializadosCredito[i];

            if (dato.name.startsWith('formaPagoCredito[]')) {
                formaPagoCredito = dato.value;
            }

            if (dato.name.startsWith('montoCredito[]')) {
                montoCredito = parseFloat(dato.value);
                acumMontos = acumMontos + montoCredito;
            }
            if (formaPagoCredito && montoCredito) {
                js_detalle_deuda.push({
                    "venta_id": idVenta,
                    "id_forma_pago": formaPagoCredito,
                    "monto_forma_pago": montoCredito
                });
                formaPagoCredito = null;
                montoCredito = null;
            }
        };
        if (isNaN(acumMontos)) {
            acumMontos = 0;
        }
        if (js_detalle_deuda.length === 0) {
            js_detalle_deuda = null;
        }


        var js_venta = {
            "venta_id": idVenta,
            "atencion_final_usuario": idAtencionFinal,
            "numUpdateTelefonoPersona": numUpdateTelefonoPersona,
            "monto_original": montoOriginal,
            "monto_venta_final": montoFinal,
            "monto_inicial": acumMontos,
            "js_detalle_deuda": js_detalle_deuda
        };
        console.log(js_venta);
        $.ajax({
            url: 'logica/clssInsertPA.php',
            type: 'POST',
            data: {
                accion: 'FINALIZARVENTACREDITO',
                jsDatosVenta: JSON.stringify(js_venta)
            },
            success: function(response) {

                console.log("Respuesta del servidor: ", response);

                try {
                    var result = JSON.parse(response);
                    if (result.estado === true) {
                        swal({
                            title: "Pagado con Exito!",
                            text: result.mensaje,
                            icon: "success",
                            buttons: false,
                            timer: 1500
                        }).then(() => {
                            window.open("ticket.php?id=" + parseInt(idVenta), "_blank");
                            location.reload();
                        });;
                    } else {
                        swal("Error", result.mensaje, {
                            icon: "error",
                            buttons: {
                                confirm: {
                                    className: "btn btn-danger",
                                },
                            },
                        });
                    }
                } catch (e) {
                    console.log("Error al parsear el JSON: ", e);
                    swal("Error", "No se pudo procesar la respuesta del servidor.", {
                        icon: "error",
                        buttons: {
                            confirm: {
                                className: "btn btn-danger",
                            },
                        },
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log("Error: " + error);
                swal("Error", "Hubo un problema con la solicitud.", {
                    icon: "error",
                    buttons: {
                        confirm: {
                            className: "btn btn-danger",
                        },
                    },
                });
            }
        });






    }
</script>

<!-- SOLO CORTE -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Escuchar el evento del botón para abrir el modal
        document.getElementById("btnAbrirModalSolo").addEventListener("click", function(event) {
            event.preventDefault(); // Prevenir el comportamiento por defecto

            // Seleccionar el contenedor del contenido dinámico
            const contenidoCorte = document.getElementById("contenido_solo_corte");

            // Generar el contenido dinámico, incluyendo el botón "Agregar"
            contenidoCorte.innerHTML = `
                <div class="col-12  bg-light rounded">
                    <h4 class="card-title text-center"><i class="fas fa-cut"></i> Opciones de Corte</h4>

                                    <div class="card-sub text-center">
                                Aqui podras agregar los minutos y el precio del servicio solo corte.
                                </div>                    <div class="mb-4">
                        <!-- Minutos Corte -->
                        <div class="text-center" style="flex: 1;">
                            <p class="mb-1">Minutos Corte</p>
                            <div class="d-flex justify-content-center align-items-center mb-2">
                                <button id="btnRestarSoloCorte" class="btn btn-danger btn-round">-</button>
                                <input id="cantidad_solocorte" type="number" class="form-control text-center mx-2" value="0" style="width: 80px; font-size: 1.2rem;" />
                                <button id="btnSumarSoloCorte" class="btn btn-success btn-round">+</button>
                            </div>
                        </div>
                        
                        <!-- Línea divisoria -->
                        <hr>
                        
                        <!-- Precio Corte -->
                        <div class="text-center" style="flex: 1;">
                            <p class="mb-1">Precio Corte</p>
                            <div class="w-100 d-flex justify-content-center mb-1">
                                <input id="precioSoloCorte" type="number" class="form-control text-center mx-2" value="1.5" style="width: 90px; font-size: 1.2rem;" />
                            </div>
                            <div class="d-flex justify-content-center">
                                <button id="btnIncremento05SoloCorte" class="btn btn-outline-primary btn-sm me-1" style="font-size: 0.9rem;">+0.5</button>
                                <button id="btnIncremento1SoloCorte" class="btn btn-outline-primary btn-sm me-1" style="font-size: 0.9rem;">+1</button>
                                <button id="btnIncremento2SoloCorte" class="btn btn-outline-primary btn-sm me-1" style="font-size: 0.9rem;">+2</button>
                                <button id="btnIncremento5SoloCorte" class="btn btn-outline-primary btn-sm" style="font-size: 0.9rem;">+5</button>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mb-3 mt-3">
                                        <button type="button" class="btn btn-secondary rounded-5" id="btnAgregarSoloCorte">Agregar</button>
                                    </div>
                    <!-- Botón Agregar -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger rounded-5" data-bs-dismiss="modal">Cerrar</button>
                    </div>

                </div>
            `;

            // Resetear valores iniciales
            document.getElementById("cantidad_solocorte").value = 0;
            document.getElementById("precioSoloCorte").value = 1.5;


            const modalElement = document.getElementById("modalSoloCorte");
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: "static", // Evita que se cierre al hacer clic fuera
                keyboard: false, // Evita que se cierre con la tecla 'Esc'
            });
            modal.show(); // Mostrar el modal
            // Asignar eventos a los elementos generados dinámicamente
            asignarEventosSoloCorte();
        });



        document.getElementById('btnAbrirModalSolov2').addEventListener('click', function(event) {
            event.preventDefault(); // Prevenir el comportamiento por defecto del botón
            let navLinks = document.querySelectorAll(".nav-link");

            // Remover la clase 'active' de todas las pestañas
            navLinks.forEach(function(link) {
                link.classList.remove("active");
            });

            // Desactivar todos los panes (contenido de las pestañas)
            let tabPanes = document.querySelectorAll(".tab-pane");
            tabPanes.forEach(function(pane) {
                pane.classList.remove("show", "active");
            });
            // Mostrar el modal de Solo Corte
            const modalElement = document.getElementById('modalSoloCorteMaquina2');
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: 'static', // Evita que se cierre al hacer clic fuera
                keyboard: false // Evita que se cierre con la tecla 'Esc'
            });


            // Seleccionar el botón "Agregar"
            const btn_agregar = document.getElementById('btn_agregar_solocortev2');
            btn_agregar.textContent = 'Agregar';

            btn_agregar.replaceWith(btn_agregar.cloneNode(true));

            // Seleccionar nuevamente el botón clonado
            const nuevoBtnAgregar = document.getElementById('btn_agregar_solocortev2');

            // Volver a agregar el evento para agregar datos
            nuevoBtnAgregar.addEventListener("click", fn_agregar_impresion_a_tabla);

            // Limpiar los campos del formulario

            modal.show(); // Muestra el modal
        });

        async function fn_agregar_impresion_a_tabla() {
            console.log("Holass")
            const cantidadMinutos = parseInt(document.getElementById('cantidad_solocortev2').value) || 0;
            const tarifa = parseFloat(document.getElementById('precioSoloCortev2').value) || 0;

            const inputMonto = document.getElementById('cantidad_solocortev2');
            const divContainer = inputMonto.closest('.d-flex');
            const mensajeErrorExistente = document.querySelector('.error-message');
            if (mensajeErrorExistente) mensajeErrorExistente.remove();
            inputMonto.classList.remove('error-input');

            if (isNaN(cantidadMinutos) || cantidadMinutos <= 0) {
                inputMonto.classList.add('error-input');

                const mensajeError =
                    `
                    <div class="error-message text-center">
                        Por favor, ingresa un monto válido mayor a 0.
                    </div>
                `;

                divContainer.insertAdjacentHTML('afterend', mensajeError);

                return;
            }

            const datosImpresion3D = {
                id: '0',
                minutos: cantidadMinutos,
                costo_por_minuto: tarifa,
                costo: cantidadMinutos * tarifa,
                articulo: 'MAQUINA DE IMPRESION  3D',
                id_movimiento: 15,
                precio_venta: null,
                cantidad: null,
            };

            console.log(datosImpresion3D);
            //fn_solo_corte_tabla(datosImpresion3D);
            document.getElementById('cantidad_solocortev2').value = '10';
            document.getElementById('precioSoloCortev2').value = '1.5'; // Valor inicial

            const modalElement = document.getElementById('modalSoloCorteMaquina2');
            const modal = bootstrap.Modal.getInstance(modalElement);
            modal.hide();
            showNotification("success");


            const venta_id_lbl = document.getElementById("idVentaReserva").textContent;

            try {
                // Llamar a la función para insertar el movimiento
                const response = await fn_adicionar_articulo(venta_id_lbl, datosImpresion3D);
                console.log("Movimiento insertado con éxito:", response);

                // Resetear valores
                document.getElementById("cantidad_solocortev2").value = 10;
                document.getElementById("precioSoloCortev2").value = 1.5;


                const modalElement = document.getElementById("modalSoloCorte");
                const modal = bootstrap.Modal.getInstance(modalElement);

                fn_consultarVenta([{
                    venta_id: venta_id_lbl
                }]);

                modal.hide();



            } catch (error) {
                console.error("Error al insertar movimiento:", error.message);
            }


        }
    });


    // Función para asignar eventos a los botones dinámicos
    function asignarEventosSoloCorte() {
        // Eventos para sumar y restar minutos
        document.getElementById("btnSumarSoloCorte").addEventListener("click", function() {
            let cantidad = parseInt(document.getElementById("cantidad_solocorte").value);
            if (cantidad == 0) {
                document.getElementById("cantidad_solocorte").value = 10;
            } else {
                document.getElementById("cantidad_solocorte").value = cantidad + 1;
            }
        });

        document.getElementById("btnRestarSoloCorte").addEventListener("click", function() {
            let cantidad = parseInt(document.getElementById("cantidad_solocorte").value);
            if (cantidad > 0) {
                document.getElementById("cantidad_solocorte").value = cantidad - 1;
            }
        });

        // Eventos para incrementar precio
        document.getElementById("btnIncremento05SoloCorte").addEventListener("click", function() {
            let precio = parseFloat(document.getElementById("precioSoloCorte").value);
            document.getElementById("precioSoloCorte").value = (precio + 0.5).toFixed(2);
        });

        document.getElementById("btnIncremento1SoloCorte").addEventListener("click", function() {
            let precio = parseFloat(document.getElementById("precioSoloCorte").value);
            document.getElementById("precioSoloCorte").value = (precio + 1).toFixed(2);
        });

        document.getElementById("btnIncremento2SoloCorte").addEventListener("click", function() {
            let precio = parseFloat(document.getElementById("precioSoloCorte").value);
            document.getElementById("precioSoloCorte").value = (precio + 2).toFixed(2);
        });

        document.getElementById("btnIncremento5SoloCorte").addEventListener("click", function() {
            let precio = parseFloat(document.getElementById("precioSoloCorte").value);
            document.getElementById("precioSoloCorte").value = (precio + 5).toFixed(2);
        });

        // Evento para el botón "Agregar"
        document.getElementById("btnAgregarSoloCorte").addEventListener("click", agregarDatosCorte);
    }

    // Función para manejar el evento de agregar datos
    async function agregarDatosCorte() {
        const cantidadMinutos = parseInt(document.getElementById("cantidad_solocorte").value) || 0;
        const tarifa = parseFloat(document.getElementById("precioSoloCorte").value) || 0;

        const inputMonto = document.getElementById('cantidad_solocorte');
        const divContainer = inputMonto.closest('.d-flex');
        const mensajeErrorExistente = document.querySelector('.error-message');
        if (mensajeErrorExistente) mensajeErrorExistente.remove();
        inputMonto.classList.remove('error-input');

        // Validar que el monto haya sido ingresado y sea mayor a 0
        if (isNaN(cantidadMinutos) || cantidadMinutos <= 0) {
            // Añadir clase para resaltar el error
            inputMonto.classList.add('error-input');

            // Crear mensaje de error dinámicamente
            const mensajeError = `
                <div class="error-message text-center">
                    Por favor, ingresa un monto válido mayor a 0.
                </div>
            `;

            // Insertar el mensaje de error debajo del contenedor principal
            divContainer.insertAdjacentHTML('afterend', mensajeError);

            return; // Detener ejecución si el monto no es válido
        }

        // Crear el objeto datosCorte
        const datosCorte = {
            id: '0', // Id del corte
            minutos: cantidadMinutos, // Minutos registrados
            costo_por_minuto: tarifa, // Costo por minuto
            costo: cantidadMinutos * tarifa,
            articulo: 'CORTE MATERIAL',
            id_movimiento: 6,
            precio_venta: null,
            cantidad: null,
        };

        const venta_id_lbl = document.getElementById("idVentaReserva").textContent;

        try {
            // Llamar a la función para insertar el movimiento
            const response = await fn_adicionar_articulo(venta_id_lbl, datosCorte);
            console.log("Movimiento insertado con éxito:", response);

            // Resetear valores
            document.getElementById("cantidad_solocorte").value = 0;
            document.getElementById("precioSoloCorte").value = 1.5;

            // Ocultar el modal
            const modalElement = document.getElementById("modalSoloCorte");
            const modal = bootstrap.Modal.getInstance(modalElement);
            modal.hide();

            // Actualizar la venta
            fn_consultarVenta([{
                venta_id: venta_id_lbl
            }]);
        } catch (error) {
            console.error("Error al insertar movimiento:", error.message);
        }
    }
</script>

<!--Cargar Datos tabla-->
<script>
    let datosDeVenta = [];
    let datosArticuloNuevos = [];

    function fn_obtener_total() {
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
        var lbl_subtotal_articulos = document.getElementById("id_subtotal_articulos");
        var lbl_subtotal_general = document.getElementById("id_subtotal_general");

        lbl_subtotal_cortes.innerText = totalCorte.toFixed(2);
        lbl_subtotal_articulos.innerText = totalArticulos.toFixed(2);
        lbl_subtotal_general.innerText = total.toFixed(2);




    }


    function fn_consultarVenta(datosArticulo) {
        // Limpiar Tabla
        var tabla = document.getElementById("tabla_articulos");
        var tbody = tabla.getElementsByTagName("tbody")[0];
        tbody.innerHTML = '';
        /////////////////////////////////////////////////////
        var panelDetalle = document.getElementById("panelDetalles");
        var panelAgregar = document.getElementById("panelAdicionarMas");
        panelDetalle.style.display = "block";
        panelAgregar.style.display = "block";

        const venta_id = Array.isArray(datosArticulo) ? datosArticulo[0].venta_id : datosArticulo.venta_id;

        console.log("Venta ID procesado:", venta_id);
        ///////////////////// ///////////////////// ///////////////////// /////////////////////
        $.ajax({
            method: "POST",
            url: "logica/clssVentaCorte.php",
            data: {
                "accion": "CONSULTARRESERVA",
                "venta_id": venta_id,
            }
        }).done(async function(text) {
            var Data = JSON.parse(text);

            // Almacena los datos originales
            console.log("datos venta", datosDeVenta)
            if (Array.isArray(datosArticulo) && datosArticulo.length > 0) {
                llenarDatosModal(
                    datosDeVenta['venta_id'],
                    datosDeVenta['id_persona'],
                    datosDeVenta['cliente'],
                    datosDeVenta['usuario_id'],
                    datosDeVenta['telefonomovil_cliente'],
                    datosDeVenta['email_cliente']
                );

                llenarDatosPanelCliente(
                    datosDeVenta['venta_id'],
                    datosDeVenta['cliente'],
                    datosDeVenta['fecha'],
                    datosDeVenta['hora'],
                    datosDeVenta['usuario'],
                    datosDeVenta['telefonomovil_cliente'],
                    datosDeVenta['email_cliente'],
                    datosDeVenta['numero_doc_cliente']
                );
            } else {
                datosDeVenta = datosArticulo;
                llenarDatosModal(
                    datosArticulo['venta_id'],
                    datosArticulo['id_persona'],
                    datosArticulo['cliente'],
                    datosArticulo['usuario_id'],
                    datosArticulo['telefonomovil_cliente'],
                    datosArticulo['email_cliente']
                );
                llenarDatosPanelCliente(
                    datosArticulo['venta_id'],
                    datosArticulo['cliente'],
                    datosArticulo['fecha'],
                    datosArticulo['hora'],
                    datosArticulo['usuario'],
                    datosArticulo['telefonomovil_cliente'],
                    datosArticulo['email_cliente'],
                    datosArticulo['numero_doc_cliente'],
                );
            }



            console.log(Data);

            // Iterar sobre los datos devueltos (Data) y agregar los artículos a la tabla
            Data.forEach(item => {
                fn_agregar_articulo_tabla(item);
            });
        });
    }

    //es datosMoviminto, solo pa no malograr la logica de mrd de adriann
    function fn_editar_modal(datosArticulo) {
        var medidas = ["A1", "A2", "A3", "A4", "A5", "A6"];
        console.log(datosArticulo);

        let modalContent = `
        <div class="text-center">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Servicio de ${datosArticulo["articulo_nombre"]}</h4>
                    <div>ID: <span id="id_mov_editar_${datosArticulo["movimiento_id"]}">${datosArticulo["movimiento_id"]}</span></div>
                    <div class="card-sub">Aquí ingresa lo que mandaron a Ploteo</div>
                </div>
                <div class="card-body">
                    <p class="card-text">Cantidad de Ploteo</p>
                    <div class="d-flex align-items-center justify-content-center">
                        <button id="btn_menos_editar_${datosArticulo["movimiento_id"]}" class="btn btn-danger btn-round me-2">-</button>
                        <input id="input_cantidad_editar_${datosArticulo["movimiento_id"]}" class="text-center" type="text" value="${datosArticulo["cantidad"]}" style="width: 40px;" oninput="validarNumero(event)">
                        <button id="btn_mas_editar_${datosArticulo["movimiento_id"]}" class="btn btn-success btn-round ms-2">+</button>
                    </div>
                </div>

                <div class="card-body">
                    <p class="card-text">Dimensión</p>
                    <div class="selectgroup selectgroup-pills">
    `;

        medidas.forEach((x) => {
            modalContent += `
            <label id = "contenedor-medidas-update" class="selectgroup-item">
                <input type="checkbox" name="value" value="${x}" class="selectgroup-input" />
                <span class="selectgroup-button">${x}</span>
            </label>`;
        });

        modalContent += `
                    </div>
                    </div>

                    <div class="card-body">
                        <p class="card-text">Monto (S/)</p>
                        <input type="number" id="monto_peditar${datosArticulo["movimiento_id"]}" class="form-control" value="${datosArticulo["sub_total"]}">
                    </div>
                    <div class="text-center mb-3">
                        <button class="btn btn-secondary rounded-5" id="btnAgregarploteoEditar" role="button">Actualizar</button>
                    </div>
                </div>
            </div>
        `;

        if (datosArticulo["nota_archivo"] && datosArticulo["nota_archivo"] !== 'Sin nota') {
            let dimensionesSeleccionadas = datosArticulo["nota_archivo"].split(", "); // Convertir string a array si es necesario
            document.querySelectorAll('.selectgroup-input').forEach((checkbox) => {
                if (dimensionesSeleccionadas.includes(checkbox.value)) {
                    checkbox.checked = true; // Marcar el checkbox si su valor está en la lista
                }
            });
        }



        // Mostrar el modal
        const modal = new bootstrap.Modal(document.getElementById('modalGenerico'));
        modal.show();

        // Esperar a que el modal sea completamente visible
        document.getElementById('modalGenerico').addEventListener('shown.bs.modal', () => {
            // Ahora que el modal está completamente cargado, agregar el eventListener
            document.getElementById('btnAgregarploteoEditar').addEventListener('click', async function() {
                const cantidadPloteos = parseInt(document.getElementById(`input_cantidad_editar_${datosArticulo["movimiento_id"]}`).value) || 1;
                const montoPloteo = parseFloat(document.getElementById(`monto_peditar${datosArticulo["movimiento_id"]}`).value) || 0;
                let venta_id_lbl = document.getElementById('idVentaReserva').textContent;

                let dimensionesSeleccionadas = [];
                document.querySelectorAll('.selectgroup-input:checked').forEach((checkbox) => {
                    dimensionesSeleccionadas.push(checkbox.value);
                });
                let textoDimensiones = dimensionesSeleccionadas.join(", ");

                datosArticulo["cantidad"] = cantidadPloteos;
                datosArticulo["nota_archivo"] = textoDimensiones.trim() === "" ? "Sin nota" : textoDimensiones;
                datosArticulo["sub_total"] = montoPloteo.toFixed(2);

                // Limpiar los campos
                document.getElementById(`input_cantidad_editar_${datosArticulo["movimiento_id"]}`).value = 1;
                document.getElementById(`monto_editar_${datosArticulo["movimiento_id"]}`).value = 0;

                const response = await fn_editar_movimiento(datosArticulo);
                console.log(response);

                fn_consultarVenta([{
                    venta_id: venta_id_lbl
                }]);

                // Resetear el botón y quitar la referencia al ploteo editado
                const modalInstance = bootstrap.Modal.getInstance(document.getElementById('modalGenerico'));
                if (modalInstance) modalInstance.hide(); // Cierra el modal si existe
            });
        });
    }

    function mostrarModalEditar(datosArticulo) {
        // Construcción del contenido del modal
        document.getElementById('modalContent').innerHTML = `
        <div class="text-center">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Servicio de Impresión</h4>
                    <div>ID: <span id="id_mov_escaneoEditar">${datosArticulo["articulo_nombre"]}</span></div>
                    <div class="card-sub">Aquí ingresa lo que mandaron a Imprimir</div>
                </div>
                <div class="card-body">
                    <p class="card-text">Cantidad a Imprimir</p>
                    <div class="d-flex align-items-center justify-content-center">
                    <button id="btn_menos_impresionEditar" class="btn btn-danger btn-round me-2">-</button>
                    <input id="input_numero_impresionEditar" class="text-center" type="text" value="${datosArticulo["cantidad"]}" style="width: 40px;" oninput="validarNumero(event)">
                    <button id="btn_mas_impresionEditar" class="btn btn-success btn-round ms-2">+</button>
                    </div>
                </div>

                <div class="card-body">
                    <p class="card-text">Dimensión</p>
                    <div class="selectgroup selectgroup-pills">
                        ${['A0', 'A1', 'A2', 'A3', 'A4', 'A5', 'A6'].map(dim => `
                            <label class="selectgroup-item">
                                <input type="checkbox" name="value" value="${dim}" class="selectgroup-input"/>
                                <span class="selectgroup-button">${dim}</span>
                            </label>
                        `).join('')}
                    </div>
                </div>

                <div class="card-body">
                    <p class="card-text">Monto (S/)</p>
                    <input type="number" id="monto_impresionEditar" class="form-control" value="${datosArticulo["sub_total"]}">
                </div>
                <div class="text-center mb-3">
                    <button class="btn btn-secondary rounded-5" id="btnAgregarimpresionEditar" role="button">Actualizar</button>
                </div>
            </div>
        </div>
    `;

        // Lógica para el botón menos
        document.getElementById('btn_menos_impresionEditar').addEventListener('click', () => {
            let cantidad = parseInt(document.getElementById('input_numero_impresionEditar').value);
            if (cantidad > 1) document.getElementById('input_numero_impresionEditar').value = cantidad - 1;
        });

        // Lógica para el botón más
        document.getElementById('btn_mas_impresionEditar').addEventListener('click', () => {
            let cantidad = parseInt(document.getElementById('input_numero_impresionEditar').value);
            document.getElementById('input_numero_impresionEditar').value = cantidad + 1;
        });

        // Marcar checkboxes según dimensiones seleccionadas en datosArticulo
        if (datosArticulo["nota_archivo"] && datosArticulo["nota_archivo"] !== 'Sin nota') {
            let dimensionesSeleccionadas = datosArticulo["nota_archivo"].split(", ");
            document.querySelectorAll('.selectgroup-input').forEach((checkbox) => {
                if (dimensionesSeleccionadas.includes(checkbox.value)) {
                    checkbox.checked = true;
                }
            });
        }

        // Mostrar el modal
        const modal = new bootstrap.Modal(document.getElementById('modalGenerico'));
        modal.show();

        // Lógica para el botón "Actualizar"
        document.getElementById('btnAgregarimpresionEditar').addEventListener('click', async function() {
            const cantidadImpresion = parseInt(document.getElementById('input_numero_impresionEditar').value) || 1;
            const montoImpresion = parseFloat(document.getElementById('monto_impresionEditar').value) || 0;
            let venta_id_lbl = document.getElementById('idVentaReserva').textContent;

            let dimensionesSeleccionadas = [];
            document.querySelectorAll('.selectgroup-input:checked').forEach((checkbox) => {
                dimensionesSeleccionadas.push(checkbox.value);
            });
            let textoDimensiones = dimensionesSeleccionadas.join(", ");

            // Actualizar datosArticulo
            datosArticulo["cantidad"] = cantidadImpresion;
            datosArticulo["nota_archivo"] = textoDimensiones.trim() === "" ? "Sin nota" : textoDimensiones;
            datosArticulo["sub_total"] = montoImpresion.toFixed(2);

            // Limpiar los campos
            document.getElementById('input_numero_impresionEditar').value = 1;
            document.getElementById('monto_impresionEditar').value = 0;

            // Llamar a la función que guarda los cambios
            const response = await fn_editar_movimiento(datosArticulo);
            console.log(response);

            // Consultar la venta actualizada
            fn_consultarVenta([{
                venta_id: venta_id_lbl
            }]);

            // Cerrar el modal
            const modalInstance = bootstrap.Modal.getInstance(document.getElementById('modalGenerico'));
            if (modalInstance) modalInstance.hide();
        });
    }


    function fn_agregar_articulo_tabla(datosArticulo) {
        var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];
        //tabla.innerHTML = "";

        // Insertamos una nueva fila en la tabla
        let nuevaFila = tabla.insertRow();

        // Colocamos los valores de las celdas
        nuevaFila.insertCell(0).textContent = datosArticulo["articulo_id"]; // ID
        nuevaFila.insertCell(1).textContent = datosArticulo["minutos"] || '-'; // Minutos
        nuevaFila.insertCell(2).textContent = datosArticulo["costo_por_minuto"] || '-'; // Costo x Minuto
        nuevaFila.insertCell(3).textContent = datosArticulo["costo_por_minuto"] * datosArticulo["minutos"] || '-'; // Costo x Minuto
        nuevaFila.insertCell(4).textContent = datosArticulo["articulo_nombre"]; // Artículo
        nuevaFila.insertCell(5).textContent = datosArticulo["cantidad"] || '-'; // Cantidad
        nuevaFila.insertCell(6).textContent = datosArticulo["precio_unitario_articulo"] || '-';; // Precio unitario
        nuevaFila.insertCell(7).textContent = parseFloat(datosArticulo["sub_total"]).toFixed(2); // Subtotal

        // Celda para acciones
        let accionCell = nuevaFila.insertCell(8);
        nuevaFila.insertCell(9).textContent = datosArticulo["movimiento_id"]; // Subtotal
        nuevaFila.insertCell(10).textContent = datosArticulo["rel_venta_articulo_id"]; // Precio unitario
        nuevaFila.insertCell(11).textContent = datosArticulo["nota_archivo"]
        let botonEditar = document.createElement("button");
        botonEditar.classList.add("btn", "btn-warning", "btn-round", "ms-2", "text-white", "px-3", "py-2");
        botonEditar.innerHTML = '<i class="fas fa-edit"></i>'; // Ícono de editar con texto

        // Agregar el botón de editar a la celda de acciones
        //accionCell.appendChild(botonEditar);

        switch (datosArticulo["movimiento_id"]) {
            case 1:
                botonEditar.addEventListener("click", () => {
                    const modalCantidad = new bootstrap.Modal(document.getElementById('modalCantidad'));
                    const contenidoModal = `
                        <div class="container-fluid">
                            <h4 class="card-title text-center" id="modalCantidadCorteLabel">Configurar Cantidad o Corte</h4>

                                <div class="card-sub text-center">
                                    Aquí ingresa la cantidad y/o corte del articulo
                                </div>
                            <!-- Sección de cantidad -->
                            <div class="row mb-3">
                                <div class="col-12 p-3 bg-light rounded">
                                    <h6 id="nombreArticuloEditar" class="fw-bold text-center mb-3">${datosArticulo["articulo_nombre"] || "Sin nombre"}</h6>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <button id="btnRestarCantidadEditar" class="btn btn-danger btn-round">-</button>
                                        <input id="inputCantidadEditar" type="number" class="form-control text-center mx-2" value="1" style="width: 80px; font-size: 1.2rem;" />
                                        <button id="btnSumarCantidadEditar" class="btn btn-success btn-round">+</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección de corte (solo visible si cantidad = 1 y corte es true) -->
                            <div id="seccionCorteEditar" class="row mb-3" style="display: ${datosArticulo["corte"] && parseInt(datosArticulo["cantidad"]) === 1 ? 'block' : 'none'};"> 
                                <div class="col-12 p-4 bg-light rounded">
                                    <h6 class="fw-bold text-center mb-4">Opciones de Corte</h6>
                                    <div class="mb-4">
                                        <div class="text-center" style="flex: 1;">
                                            <p class="mb-1">Minutos Corte</p>
                                            <div class="d-flex justify-content-center align-items-center mb-2">
                                                <button id="btnRestarCorteEditar" class="btn btn-danger btn-round">-</button> 
                                                <input id="cantidadCorteEditar" type="number" class="form-control text-center mx-2" value="0" style="width: 80px; font-size: 1.2rem;" /> 
                                                <button id="btnSumarCorteEditar" class="btn btn-success btn-round">+</button> 
                                            </div>
                                        </div>

                                        <!-- Línea divisoria -->
                                        <hr>

                                        <div class="text-center" style="flex: 1;">
                                            <p class="mb-1">Precio Corte</p>
                                            <div class="w-100 d-flex justify-content-center mb-1">
                                                <input id="precioCorteEditar" type="number" class="form-control text-center mx-2" value="1.5" style="width: 90px; font-size: 1.2rem;" /> 
                                            </div>
                                            <div class="d-flex justify-content-center">
                                                <button id="btnIncremento05Editar" class="btn btn-outline-primary btn-sm me-1" style="font-size: 0.9rem;">+0.5</button> 
                                                <button id="btnIncremento1Editar" class="btn btn-outline-primary btn-sm me-1" style="font-size: 0.9rem;">+1</button>
                                                <button id="btnIncremento2Editar" class="btn btn-outline-primary btn-sm me-1" style="font-size: 0.9rem;">+2</button>
                                                <button id="btnIncremento5Editar" class="btn btn-outline-primary btn-sm" style="font-size: 0.9rem;">+5</button> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                              <div class="text-center mb-3">
                        <button id="btnConfirmarEditarCantidad" class="btn btn-secondary rounded-5" style="width: 120px;">Confirmar</button>
                    </div>

                            
                        </div>
                    `;

                    const contenedorCantidad = document.getElementById('contenid_cantidad');
                    contenedorCantidad.innerHTML = contenidoModal;

                    // Abrir el modal
                    modalCantidad.show();
                    // Abrir el modal con la cantidad actual, nombre del artículo, y datos adicionales de corte
                    document.getElementById("nombreArticuloEditar").textContent = datosArticulo["articulo_nombre"];
                    document.getElementById("inputCantidadEditar").value = datosArticulo["cantidad"];
                    document.getElementById("cantidadCorteEditar").value = datosArticulo["minutos"] || 0;

                    // Mostrar los valores actuales de corte si es que existen
                    function actualizarVisibilidadCorte() {
                        const cantidad = parseInt(document.getElementById("inputCantidadEditar").value, 10);
                        const seccionCorte = document.getElementById("seccionCorteEditar");
                        if (cantidad === 1 && datosArticulo["corte"]) {
                            seccionCorte.style.display = 'block';
                        } else {
                            seccionCorte.style.display = 'none';
                        }
                    }

                    // Botón Restar Cantidad
                    document.getElementById("btnRestarCantidadEditar").addEventListener("click", function() {
                        let cantidad = parseInt(document.getElementById("inputCantidadEditar").value, 10);
                        if (cantidad > 1) {
                            document.getElementById("inputCantidadEditar").value = cantidad - 1;
                        }
                        actualizarVisibilidadCorte(); // Actualizar visibilidad de la sección de corte
                    });

                    // Botón Sumar Cantidad
                    document.getElementById("btnSumarCantidadEditar").addEventListener("click", function() {
                        let cantidad = parseInt(document.getElementById("inputCantidadEditar").value, 10);
                        document.getElementById("inputCantidadEditar").value = cantidad + 1;
                        actualizarVisibilidadCorte(); // Actualizar visibilidad de la sección de corte
                    });

                    // Botón Restar Minutos Corte
                    document.getElementById("btnRestarCorteEditar").addEventListener("click", function() {
                        let corte = parseInt(document.getElementById("cantidadCorteEditar").value, 10);
                        if (corte > 0) {
                            document.getElementById("cantidadCorteEditar").value = corte - 1;
                        }
                    });

                    // Botón Sumar Minutos Corte
                    document.getElementById("btnSumarCorteEditar").addEventListener("click", function() {
                        let corte = parseInt(document.getElementById("cantidadCorteEditar").value, 10);
                        if (corte == 1) {
                            document.getElementById("cantidadCorteEditar").value = 10;

                        } else {
                            document.getElementById("cantidadCorteEditar").value = corte + 1;

                        }
                    });

                    // Incremento de precio por corte
                    document.getElementById("btnIncremento05Editar").addEventListener("click", function() {
                        let precio = parseFloat(document.getElementById("precioCorteEditar").value);
                        document.getElementById("precioCorteEditar").value = (precio + 0.5).toFixed(2);
                    });

                    document.getElementById("btnIncremento1Editar").addEventListener("click", function() {
                        let precio = parseFloat(document.getElementById("precioCorteEditar").value);
                        document.getElementById("precioCorteEditar").value = (precio + 1).toFixed(2);
                    });

                    document.getElementById("btnIncremento2Editar").addEventListener("click", function() {
                        let precio = parseFloat(document.getElementById("precioCorteEditar").value);
                        document.getElementById("precioCorteEditar").value = (precio + 2).toFixed(2);
                    });

                    document.getElementById("btnIncremento5Editar").addEventListener("click", function() {
                        let precio = parseFloat(document.getElementById("precioCorteEditar").value);
                        document.getElementById("precioCorteEditar").value = (precio + 5).toFixed(2);
                    });

                    // Guardar el artículo actual para hacer la modificación posteriormente
                    document.getElementById("btnConfirmarEditarCantidad").addEventListener('click', async function() {
                        // Lógica para actualizar cantidad y precios
                        datosArticulo["cantidad"] = parseInt(document.getElementById("inputCantidadEditar").value);

                        if (datosArticulo["corte"] && datosArticulo["cantidad"] > 1) {

                        }

                        if (datosArticulo["corte"]) {
                            if (datosArticulo["cantidad"] == 1) {
                                datosArticulo["costo_por_minuto"] = (parseFloat(document.getElementById("precioCorteEditar").value) === 0) ? '-' : parseFloat(document.getElementById("precioCorteEditar").value);
                                datosArticulo["minutos"] = parseInt(document.getElementById("cantidadCorteEditar").value) || '-';
                            }
                            if (datosArticulo["cantidad"] > 1) {
                                datosArticulo["costo_por_minuto"] = '-';
                                datosArticulo["minutos"] = '-';
                            }

                        }


                        let venta_id_lbl = document.getElementById('idVentaReserva').textContent;

                        // Recalcular el subtotal considerando el precio de corte y minutos de corte
                        let subtotal = datosArticulo["cantidad"] * datosArticulo["precio_unitario_articulo"];
                        subtotal += (datosArticulo["costo_por_minuto"] * datosArticulo["minutos"]) || 0;
                        datosArticulo["sub_total"] = subtotal;
                        console.log(datosArticulo);

                        const response = await fn_editar_articulo(datosArticulo);
                        console.log(response);


                        fn_consultarVenta([{
                            venta_id: venta_id_lbl
                        }]);

                        // Cerramos el modal
                        $('#modalCantidad').modal('hide');

                    });

                    // Mostrar el modal
                });

                break;

            case 15:
                botonEditar.addEventListener("click", () => {
                    //modalSoloCorteMaquina2 
                    const modalElement = document.getElementById('modalSoloCorteMaquina2');
                    const modal = new bootstrap.Modal(modalElement, {
                        backdrop: 'static',
                        keyboard: false
                    });

                    document.getElementById("cantidad_solocortev2").value = parseInt(datosArticulo["minutos"]);
                    document.getElementById("precioSoloCortev2").value = parseFloat(datosArticulo["costo_por_minuto"]);

                    //////////////////////

                    let cant_solo_corte_v2 = document.getElementById("cantidad_solocortev2").value;
                    let precio_corte_v2 = document.getElementById("precioSoloCortev2").value;

                    console.log(datosArticulo);
                    const btnAgregar = document.getElementById("btn_agregar_solocortev2");
                    btnAgregar.textContent = 'Actualizar';

                    btnAgregar.addEventListener("click", async function() {
                        const nuevosMinutos = parseInt(document.getElementById("cantidad_solocortev2").value) || 10;
                        const nuevoPrecio = parseFloat(document.getElementById("precioSoloCortev2").value) || 1.5;

                        datosArticulo["minutos"] = parseInt(document.getElementById("cantidad_solocortev2").value) || 0;
                        datosArticulo["costo_por_minuto"] = parseFloat(document.getElementById("precioSoloCortev2").value) || 1.5;
                        datosArticulo["sub_total"] = datosArticulo["minutos"] * datosArticulo["costo_por_minuto"];
                        let venta_id_lbl = document.getElementById('idVentaReserva').textContent;

                        const response = await fn_editar_articulo(datosArticulo);
                        console.log(response);
                        fn_consultarVenta([{
                            venta_id: venta_id_lbl
                        }]);
                        modal.hide();
                        // Volver a colocar el botón "Agregar" y restaurar el evento de agregar

                    });
                    modal.show();


                });
                break;
            case 6:
                botonEditar.addEventListener("click", () => {

                    // Mostrar el modal
                    const modalElement = document.getElementById('modalSoloCorte');
                    const modal = new bootstrap.Modal(modalElement, {
                        backdrop: 'static',
                        keyboard: false
                    });

                    modal.show();
                    const contenidoCorte = document.getElementById("contenido_solo_corte");

                    // Generamos el contenido dinámico para mostrar el formulario de corte
                    contenidoCorte.innerHTML = `
                        <div class="col-12  bg-light rounded">
                                <h4 class="card-title text-center"><i class="fas fa-cut"></i> Opciones de Corte</h4>

                                                                    <div class="card-sub text-center">
                                                                Aqui podras agregar los minutos y el precio del servicio solo corte.
                                                                </div>                            <div class="mb-4">
                                <!-- Minutos Corte -->
                                <div class="text-center" style="flex: 1;">
                                    <p class="mb-1">Minutos Corte</p>
                                    <div class="d-flex justify-content-center align-items-center mb-2">
                                        <button id="btnRestarSoloCorteEditar" class="btn btn-danger btn-round">-</button>
                                        <input id="cantidad_solocorte" type="number" class="form-control text-center mx-2" value="${datosArticulo.minutos}" style="width: 80px; font-size: 1.2rem;" />
                                        <button id="btnSumarSoloCorteEditar" class="btn btn-success btn-round">+</button>
                                    </div>
                                </div>
                                
                                <!-- Línea divisoria -->
                                <hr>
                                
                                <!-- Precio Corte -->
                                <div class="text-center" style="flex: 1;">
                                    <p class="mb-1">Precio Corte</p>
                                    <div class="w-100 d-flex justify-content-center mb-1">
                                        <input id="precioSoloCorte" type="number" class="form-control text-center mx-2" value="${datosArticulo.costo_por_minuto}" style="width: 90px; font-size: 1.2rem;" />
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button id="btnIncremento05SoloCorteEditar" class="btn btn-outline-primary btn-sm me-1" style="font-size: 0.9rem;">+0.5</button>
                                        <button id="btnIncremento1SoloCorteEditar" class="btn btn-outline-primary btn-sm me-1" style="font-size: 0.9rem;">+1</button>
                                        <button id="btnIncremento2SoloCorteEditar" class="btn btn-outline-primary btn-sm me-1" style="font-size: 0.9rem;">+2</button>
                                        <button id="btnIncremento5SoloCorteEditar" class="btn btn-outline-primary btn-sm" style="font-size: 0.9rem;">+5</button>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mt-3 mb-3">
                                <button id="btnActualizarSoloCorte" class="btn btn-secondary rounded-5">Actualizar</button>
                            </div>

                       
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger rounded-5" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    `;

                    const cantidadInput = document.getElementById("cantidad_solocorte");
                    const precioInput = document.getElementById("precioSoloCorte");

                    // Asignar eventos de los botones de incrementar y decrementar
                    const btnRestar = document.getElementById("btnRestarSoloCorteEditar");
                    const btnSumar = document.getElementById("btnSumarSoloCorteEditar");
                    const btnIncremento05 = document.getElementById("btnIncremento05SoloCorteEditar");
                    const btnIncremento1 = document.getElementById("btnIncremento1SoloCorteEditar");
                    const btnIncremento2 = document.getElementById("btnIncremento2SoloCorteEditar");
                    const btnIncremento5 = document.getElementById("btnIncremento5SoloCorteEditar");

                    // Botones de cantidad
                    btnRestar.addEventListener("click", () => {
                        let cantidad = parseInt(cantidadInput.value) || 0;
                        cantidadInput.value = Math.max(cantidad - 1, 0); // Evita valores negativos
                    });

                    btnSumar.addEventListener("click", () => {
                        let cantidad = parseInt(cantidadInput.value) || 0;
                        cantidadInput.value = cantidad + 1;
                    });

                    // Botones de incremento en precio
                    btnIncremento05.addEventListener("click", () => {
                        let precio = parseFloat(precioInput.value) || 0;
                        precioInput.value = precio + 0.5;
                    });

                    btnIncremento1.addEventListener("click", () => {
                        let precio = parseFloat(precioInput.value) || 0;
                        precioInput.value = precio + 1;
                    });

                    btnIncremento2.addEventListener("click", () => {
                        let precio = parseFloat(precioInput.value) || 0;
                        precioInput.value = precio + 2;
                    });

                    btnIncremento5.addEventListener("click", () => {
                        let precio = parseFloat(precioInput.value) || 0;
                        precioInput.value = precio + 5;
                    });


                    // Crear el botón "Actualizar" dinámicamente
                    const btnActualizar = document.getElementById("btnActualizarSoloCorte");

                    // Eliminar el evento original del botón de "Agregar"
                    // Llenamos el modal con los datos del corte
                    document.getElementById("cantidad_solocorte").value = datosArticulo["minutos"] || 0; // Minutos corte
                    document.getElementById("precioSoloCorte").value = datosArticulo["costo_por_minuto"] || 1.5; // Precio corte

                    // Actualizar el corte en la tabla cuando se presiona "Actualizar"
                    btnActualizar.addEventListener("click", async function() {
                        const nuevosMinutos = parseInt(document.getElementById("cantidad_solocorte").value) || 0;
                        const nuevoPrecio = parseFloat(document.getElementById("precioSoloCorte").value) || 1.5;

                        datosArticulo["minutos"] = parseInt(document.getElementById("cantidad_solocorte").value) || 0;
                        datosArticulo["costo_por_minuto"] = parseFloat(document.getElementById("precioSoloCorte").value) || 1.5;
                        datosArticulo["sub_total"] = datosArticulo["minutos"] * datosArticulo["costo_por_minuto"];
                        let venta_id_lbl = document.getElementById('idVentaReserva').textContent;

                        const response = await fn_editar_articulo(datosArticulo);
                        console.log(response);


                        fn_consultarVenta([{
                            venta_id: venta_id_lbl
                        }]);
                        modal.hide();

                        // Volver a colocar el botón "Agregar" y restaurar el evento de agregar

                    });


                });
                break;
            default:

                botonEditar.addEventListener("click", () => {
                    //fn_editar_modal(datosArticulo);
                    mostrarModalEditar(datosArticulo);
                });


                break;
        }


        let botonEliminar = document.createElement("button");
        botonEliminar.classList.add("btn", "btn-danger", "btn-round", "ms-2", "px-3", "py-2");
        botonEliminar.innerHTML = '<i class="fas fa-trash"></i>'; // Ícono de eliminar con texto

        accionCell.appendChild(botonEliminar);



        // Función para manejar el botón de eliminar
        botonEliminar.addEventListener("click", async () => {
            const result = await Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });

            if (result.isConfirmed) {
                let venta_id_lbl = document.getElementById('idVentaReserva').textContent;

                // Determinar qué función llamar según el tipo de movimiento
                if (datosArticulo["movimiento_id"] == 1) {
                    await fn_eliminar_articulo(datosArticulo["rel_venta_articulo_id"]);
                } else {
                    await fn_eliminar_movimiento(datosArticulo["rel_venta_articulo_id"]);
                }

                // Actualizar los datos de la venta
                await fn_consultarVenta([{
                    venta_id: venta_id_lbl
                }]);

                // Mostrar notificación de éxito

            }
        });
        // Llamamos la función para recalcular los totales si es necesario
        fn_obtener_total();
    }
</script>

<!--Agregar desde tabla Modal-->
<script>
    function fn_agregar_venta(datosArticulo) {
        const modalCantidad = new bootstrap.Modal(document.getElementById('modalCantidad'));

        // Crear el contenido como un string HTML
        const contenidoModal = `
        <div class="container-fluid">
            <h4 class="card-title text-center" id="modalCantidadCorteLabel">Configurar Cantidad o Corte</h4>

                                <div class="card-sub text-center">
                                    Aquí ingresa la cantidad y/o corte del articulo
                                </div>
            <!-- Sección de cantidad -->
            <div class="row mb-3">
                <div class="col-12 p-3 bg-light rounded">
                    <h6 id="nombreArticulo" class="fw-bold text-center mb-3">${datosArticulo.articulo || "Sin nombre"}</h6>
                    <div class="d-flex justify-content-center align-items-center">
                        <button id="btnRestarCantidad" class="btn btn-danger btn-round">-</button>
                        <input id="inputCantidad" type="number" class="form-control text-center mx-2" value="1" style="width: 80px; font-size: 1.2rem;" />
                        <button id="btnSumarCantidad" class="btn btn-success btn-round">+</button>
                    </div>
                </div>
            </div>

            <!-- Sección de corte (solo visible si cantidad = 1 y corte es true) -->
            <div id="seccionCorte" class="row mb-3" style="display: ${datosArticulo.corte ? 'block' : 'none'};">
                <div class="col-12 p-4 bg-light rounded">
                   
                    <div class="mb-4">
                        <div class="text-center" style="flex: 1;">
                            <p class="mb-1">Minutos Corte</p>
                            <div class="d-flex justify-content-center align-items-center mb-2">
                                <button id="btnRestarCorte" class="btn btn-danger btn-round">-</button>
                                <input id="cantidadCorte" type="number" class="form-control text-center mx-2" value="0" style="width: 80px; font-size: 1.2rem;" />
                                <button id="btnSumarCorte" class="btn btn-success btn-round">+</button>
                            </div>
                        </div>

                        <!-- Línea divisoria -->
                        <hr>

                        <div class="text-center" style="flex: 1;">
                            <p class="mb-1">Precio Corte</p>
                            <div class="w-100 d-flex justify-content-center mb-1">
                                <input id="precioCorte" type="number" class="form-control text-center mx-2" value="1.5" style="width: 90px; font-size: 1.2rem;" />
                            </div>
                            <div class="d-flex justify-content-center">
                                <button id="btnIncremento05" class="btn btn-outline-primary btn-sm me-1" style="font-size: 0.9rem;">+0.5</button>
                                <button id="btnIncremento1" class="btn btn-outline-primary btn-sm me-1" style="font-size: 0.9rem;">+1</button>
                                <button id="btnIncremento2" class="btn btn-outline-primary btn-sm me-1" style="font-size: 0.9rem;">+2</button>
                                <button id="btnIncremento5" class="btn btn-outline-primary btn-sm" style="font-size: 0.9rem;">+5</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mb-3">
                <button id="btnConfirmar" class="btn btn-secondary rounded-5" style="width: 120px;">Confirmar</button>
            </div>
          
           
        </div>
        `;

        // Inyectar el contenido al modal
        const contenedorCantidad = document.getElementById('contenid_cantidad');
        contenedorCantidad.innerHTML = contenidoModal;

        // Abrir el modal
        modalCantidad.show();

        // Funciones para los botones dentro del modal
        document.getElementById('btnRestarCantidad').addEventListener('click', function() {
            let inputCantidad = document.getElementById('inputCantidad');
            if (inputCantidad.value > 1) {
                inputCantidad.value--;
            }
            // Actualizar la visibilidad de la sección de corte
            actualizarVisibilidadCorte(datosArticulo);
        });

        document.getElementById('btnSumarCantidad').addEventListener('click', function() {
            let cantidad = parseInt(document.getElementById('inputCantidad').value, 10) + 1; // Aumentar primero
            let cantidadStock = datosArticulo.stock; // Stock disponible

            let inputCantidad = document.getElementById('inputCantidad');
            if (cantidad > cantidadStock) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stock insuficiente',
                    text: `No puedes agregar más de ${cantidadStock} unidades.`,
                    confirmButtonText: 'Entendido'
                });
            } else {
                inputCantidad.value++;
                actualizarVisibilidadCorte(datosArticulo);
            }

            // Actualizar la visibilidad de la sección de corte

        });

        document.getElementById('btnRestarCorte').addEventListener('click', function() {
            let cantidadCorte = document.getElementById('cantidadCorte');
            if (cantidadCorte.value > 0) {
                cantidadCorte.value--;
            }
        });

        document.getElementById('btnSumarCorte').onclick = () => {
            let corte = parseInt(cantidadCorte.value, 10);
            if (corte == 0) {
                cantidadCorte.value = 10;
            } else {
                cantidadCorte.value = corte + 1;
            }
        };

        // Lógica para los botones de incremento de precio de corte
        document.getElementById('btnIncremento05').addEventListener('click', function() {
            let precioCorte = document.getElementById('precioCorte');
            precioCorte.value = parseFloat(precioCorte.value) + 0.5;
        });

        document.getElementById('btnIncremento1').addEventListener('click', function() {
            let precioCorte = document.getElementById('precioCorte');
            precioCorte.value = parseFloat(precioCorte.value) + 1;
        });

        document.getElementById('btnIncremento2').addEventListener('click', function() {
            let precioCorte = document.getElementById('precioCorte');
            precioCorte.value = parseFloat(precioCorte.value) + 2;
        });

        document.getElementById('btnIncremento5').addEventListener('click', function() {
            let precioCorte = document.getElementById('precioCorte');
            precioCorte.value = parseFloat(precioCorte.value) + 5;
        });

        // Función para confirmar cantidad (se ejecuta al hacer click en el botón Confirmar)
        document.getElementById('btnConfirmar').addEventListener('click', async function() {
            let cantidadSeleccionada = parseInt(document.getElementById('inputCantidad').value, 10);
            let cantidadStock = datosArticulo.stock;

            // Validar que la cantidad no supere el stock
            if (cantidadSeleccionada > cantidadStock) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stock insuficiente',
                    text: `Solo hay ${cantidadStock} unidades disponibles.`,
                    confirmButtonText: 'Entendido'
                });
                return; // Detener ejecución
            }

            try {
                const inputCantidad = document.getElementById('inputCantidad');

                const cantidadCorte = document.getElementById('cantidadCorte');

                const precioCorte = document.getElementById('precioCorte');

                if (!datosArticulo.corte) {
                    cantidadCorte.value = null;
                    precioCorte.value = null;
                }

                if (datosArticulo.corte && inputCantidad.value > 1) {
                    cantidadCorte.value = null;
                    precioCorte.value = null;
                }



                // Actualizando datosArticulo con los valores de los inputs
                datosArticulo.cantidad = parseInt(inputCantidad.value, 10);
                datosArticulo.minutos = parseInt(cantidadCorte.value, 10) || '-';
                datosArticulo.costo_por_minuto = parseFloat(precioCorte.value, 10) || '-';
                datosArticulo.id_movimiento = 1;

                let venta_id_lbl = document.getElementById('idVentaReserva').textContent;
                console.log(datosArticulo);
                // Llamada al servidor para agregar el artículo
                const response = await fn_adicionar_articulo(venta_id_lbl, datosArticulo);

                // Cerrar el modal
                modalCantidad.hide();

                // Consultar la venta actualizada
                fn_consultarVenta([{
                    venta_id: venta_id_lbl
                }]);

            } catch (error) {
                console.error("Error al confirmar cantidad:", error);
            }
        });

        // Función para actualizar la visibilidad de la sección de corte
        function actualizarVisibilidadCorte(datosArticulo) {
            const cantidad = parseInt(document.getElementById('inputCantidad').value);
            const seccionCorte = document.getElementById('seccionCorte');
            // Mostrar u ocultar la sección de corte dependiendo de la cantidad y el valor de corte en datosArticulo
            if (cantidad === 1 && datosArticulo.corte) {
                seccionCorte.style.display = 'block';
            } else {
                seccionCorte.style.display = 'none';
            }
        }
    }
</script>



<!--Fn Insert, Update y Delete-->
<script>
    function fn_insert_movimiento(venta_id, movimiento_id, cantidad, nota_archivo, sub_total) {
        return new Promise((resolve, reject) => {
            try {
                const datos = {
                    "venta_id": venta_id,
                    "movimiento_id": movimiento_id,
                    "cantidad": cantidad,
                    "sub_total": sub_total,
                    "nota_archivo": nota_archivo
                };

                $.ajax({
                        method: "POST",
                        url: "logica/clssVentaCorte.php",
                        data: {
                            "accion": "INSERTMOVIMIENTO",
                            "data": JSON.stringify(datos)
                        }
                    })
                    .done(function(response) {
                        const jsonResponse = JSON.parse(response); // Si el servidor devuelve un JSON
                        if (jsonResponse.success) {
                            showNotification("success");
                            fn_consultarVenta([{
                                venta_id: venta_id
                            }]);
                            resolve(jsonResponse); // Éxito: Resuelve la promesa
                        } else {
                            showNotification("error");
                            reject(new Error(jsonResponse.mensaje || "Error desconocido")); // Error del servidor
                        }
                    })
                    .fail(function(error) {
                        reject(new Error(error.responseText || "Error en la solicitud AJAX")); // Error en la solicitud
                    });
            } catch (error) {
                reject(new Error(error.message)); // Error inesperado
            }
        });
    }

    function fn_adicionar_articulo(venta_id, datosArticulo) {
        return new Promise((resolve, reject) => {
            const datos = {
                "venta_id": venta_id, // Puedes cambiar este valor dinámicamente si es necesario
                "articulo_id": datosArticulo['id'], // También este valor puede ser dinámico
                "cantidad": datosArticulo['cantidad'],
                "sub_total": calcularSubTotal(datosArticulo),
                "minutos": datosArticulo['minutos'],
                "precio_unitario": datosArticulo['precio_venta'],
                "costoxminuto": datosArticulo['costo_por_minuto'],
                "movimiento_id": datosArticulo['id_movimiento'],

            };

            console.log(datos);
            $.ajax({
                method: "POST",
                url: "logica/clssVentaCorte.php",
                data: {
                    "accion": "ADICIONARARTICULO",
                    "data": JSON.stringify(datos)
                }
            }).done(function(response) {
                const jsonResponse = JSON.parse(response); // Si el servidor devuelve un JSON
                console.log(jsonResponse)
                if (jsonResponse.success) {
                    showNotification("success");
                    resolve(jsonResponse); // Éxito: Resuelve la promesa
                } else {
                    showNotification("error");
                    swal("Upps", jsonResponse.message, {
                        icon: "error",
                        buttons: {
                            confirm: {
                                className: "btn btn-danger",
                            },
                        },
                    });

                    reject(new Error(jsonResponse.message || "Error desconocido")); // Error del servidor
                }

            }).fail(function(error) {
                console.error("Error:", error.responseText);
                // Rechazamos la promesa si hay un error
                reject(error);
            });
        });
    }

    function calcularSubTotal(datosArticulo) {
        let cantidad = datosArticulo['cantidad'] === '-' || datosArticulo['cantidad'] === null ? 0 : parseInt(datosArticulo['cantidad']);;
        let precio_venta = datosArticulo['precio_venta'] === '-' || datosArticulo['precio_venta'] === null ? 0 : parseFloat(datosArticulo['precio_venta']);
        let minutos = datosArticulo['minutos'] === '-' || datosArticulo['minutos'] === null ? 0 : parseInt(datosArticulo['minutos']);
        let costo_por_minuto = datosArticulo['costo_por_minuto'] === '-' || datosArticulo['costo_por_minuto'] === null ? 0 : parseFloat(datosArticulo['costo_por_minuto']);
        console.log(cantidad);
        console.log(precio_venta);

        console.log(minutos);

        console.log(costo_por_minuto);


        // Calcular subtotal: cantidad * precio_venta + minutos * costo_por_minuto
        return (cantidad * precio_venta) + (minutos * costo_por_minuto);
    }

    function fn_eliminar_articulo(id_rel_articulo) {
        return new Promise((resolve, reject) => {
            $.ajax({
                method: "POST",
                url: "logica/clssVentaCorte.php",
                data: {
                    "accion": "ELIMINARARTICULO", // Acción que se realizará en PHP
                    "id_rel_articulo": id_rel_articulo
                }
            }).done(function(response) {
                const jsonResponse = JSON.parse(response); // Si el servidor devuelve un JSON
                if (jsonResponse.success) {
                    showNotification("success");
                    resolve(jsonResponse); // Éxito: Resuelve la promesa
                } else {
                    showNotification("error");
                    reject(new Error(jsonResponse.mensaje || "Error desconocido")); // Error del servidor
                }
            }).fail(function(error) {
                console.error("Error:", error.responseText);
                // Rechazamos la promesa si hay un error
                reject(error);
            });
        });
    }

    function fn_eliminar_movimiento(id_rel_articulo) {
        return new Promise((resolve, reject) => {
            $.ajax({
                method: "POST",
                url: "logica/clssVentaCorte.php",
                data: {
                    "accion": "ELIMINARMOVIMIENTO", // Acción que se realizará en PHP
                    "id_rel_articulo": id_rel_articulo
                }
            }).done(function(response) {
                const jsonResponse = JSON.parse(response); // Si el servidor devuelve un JSON
                if (jsonResponse.success) {
                    showNotification("success");
                    resolve(jsonResponse); // Éxito: Resuelve la promesa
                } else {
                    showNotification("error");
                    reject(new Error(jsonResponse.mensaje || "Error desconocido")); // Error del servidor
                }
            }).fail(function(error) {
                console.error("Error:", error.responseText);
                // Rechazamos la promesa si hay un error
                reject(error);
            });
        });
    }

    function fn_editar_articulo(datos) {
        return new Promise((resolve, reject) => {
            $.ajax({
                method: "POST",
                url: "logica/clssVentaCorte.php",
                data: {
                    "accion": "EDITARARTICULO", // Acción que se realizará en PHP
                    "data": JSON.stringify(datos) // Los datos a enviar para editar el artículo
                }
            }).done(function(response) {
                const jsonResponse = JSON.parse(response); // Si el servidor devuelve un JSON
                if (jsonResponse.success) {
                    showNotification("success");
                    resolve(jsonResponse); // Éxito: Resuelve la promesa
                } else {
                    showNotification("error");
                    reject(new Error(jsonResponse.mensaje || "Error desconocido")); // Error del servidor
                }
            }).fail(function(error) {
                console.error("Error:", error.responseText);
                // Rechazamos la promesa si hay un error
                reject(error);
            });
        });
    }

    function fn_editar_movimiento(datos) {
        return new Promise((resolve, reject) => {
            $.ajax({
                method: "POST",
                url: "logica/clssVentaCorte.php",
                data: {
                    "accion": "EDITARMOVIMIENTO", // Acción que se realizará en PHP
                    "data": JSON.stringify(datos) // Los datos a enviar para editar el artículo
                }
            }).done(function(response) {
                const jsonResponse = JSON.parse(response); // Si el servidor devuelve un JSON
                if (jsonResponse.success) {
                    showNotification("success");
                    resolve(jsonResponse); // Éxito: Resuelve la promesa
                } else {
                    showNotification("error");
                    reject(new Error(jsonResponse.mensaje || "Error desconocido")); // Error del servidor
                }
            }).fail(function(error) {
                console.error("Error:", error.responseText);
                // Rechazamos la promesa si hay un error
                reject(error);
            });
        });
    }
</script>

<script>
    document.getElementById("btnRealizarPago").addEventListener("click", function() {

        document.getElementById("idRucReceptor").value = "",
            document.getElementById("idRazonSocialReceptor").value = "";
        // Mostrar el modal manualmente
        const modal = new bootstrap.Modal(document.getElementById("modalRealizarPago"));
        modal.show();

        const subtotalGeneral = document.getElementById("id_subtotal_general").textContent;
        document.getElementById("montoTotal").value = subtotalGeneral; // Asignar el monto total
        document.getElementById("idMontoVentaTitulo").textContent = subtotalGeneral;

    });

    function llenarDatosModal(idVenta, idPersona, nombreCliente, idUsuario, numeroCelular, email) {
        // Actualizamos el contenido del modal con los datos proporcionados
        document.getElementById("idUsuario").textContent = idUsuario; // Para el ID de la venta
        document.getElementById("idVenta").textContent = idVenta; // Para el ID de la venta
        document.getElementById("idPersona").textContent = idPersona; // Para el ID del cliente
        document.getElementById("nombreCliente").value = nombreCliente; // Para el nombre del cliente

        document.getElementById("idUpdateNumTelefonoCliente").value = numeroCelular;
        document.getElementById("idUpdateCorreoCliente").value = email;

    }

    function llenarDatosPanelCliente(idVenta, cliente, fechaReserva, horaReserva, usuario, telefonomovil, email, numeroDoc) {
        // Actualizamos el contenido del modal con los datos proporcionados
        document.getElementById("idVentaReserva").textContent = idVenta;

        document.getElementById("idClienteReservaDetalle").textContent = cliente;

        document.getElementById("idNumCelClienteReserva").textContent = telefonomovil;
        document.getElementById("idCorreoClienteReserva").textContent = email;
        document.getElementById("idNumDocClienteReserva").textContent = numeroDoc;


        document.getElementById("idClienteReservaDetalle").textContent = cliente;
        document.getElementById("idFechaReservaDetalle").value = fechaReserva;
        document.getElementById("idHoraReservaDetalle").value = horaReserva;
        document.getElementById("idUsuarioReservaDetalle").textContent = usuario;
    }
</script>


<?php
include("pie.php");
?>