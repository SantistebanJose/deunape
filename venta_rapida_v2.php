<?php
include("cabecera.php");
include("logica/clssVenta.php");


if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

$sucursal_id = $_SESSION["sucursal_id"];

?>
<style>
    #sugerencias {
        max-height: 200px;
        overflow-y: auto;
        z-index: 1050;
        /* Para asegurar que esté sobre otros elementos */
    }

    #sugerencias .list-group-item {
        cursor: pointer;
    }

    #tabla_articulos th:nth-child(1),
    #tabla_articulos td:nth-child(1),
    #tabla_articulos th:nth-child(10),
    #tabla_articulos td:nth-child(10),
    #tabla_articulos th:nth-child(11),
    #tabla_articulos td:nth-child(11) {
        display: none !important;
    }

    .error-input {
        border: 2px solid red;
    }

    .error-message {
        color: red;
        font-size: 0.9em;
        margin-top: 5px;
    }

    #modalCliente {
        z-index: 1060 !important;
        /* Asegúrate de que sea más alto que el de los demás modales */
    }

    .modal-header {
        background-color: rgb(255, 255, 255);
        /* Fondo azul */
        color: #2a2f5b;
        /* Texto blanco */
    }

    /* Estilo para cambiar el color de fondo y bordes del modal */
    #modalCliente .modal-content {
        background-color: #f0f8ff;
        /* Color de fondo claro (puedes cambiarlo) */
        border-radius: 10px;
        /* Bordes redondeados */
        border: 2px solid #2a2f5b;
        /* Borde azul para darle más protagonismo */
    }

    /* Agregar una sombra para resaltar más el modal */
    #modalCliente .modal-dialog {
        box-shadow: 0 4px 10px #2a2f5b;
        /* Sombra azul para resaltar el modal */
    }

    /* Título del modal más grande y con un color diferente */
    #modalCliente .modal-header {
        background-color: #2a2f5b;
        /* Fondo azul */
        color: white;
        /* Texto blanco */
    }

    #modalCliente .btn-close {
        background-color: #f0f8ff;
        /* Botón de cerrar rojo */
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



    #productosTable {
    font-size: 14px;
}

#productosTable thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: #2a2f5b !important;
    color: white !important;
}

#productosTable tbody tr {
    transition: all 0.2s ease;
}

#productosTable tbody tr:hover {
    background-color: #f0f8ff;
    transform: scale(1.01);
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

/* Responsivo para móviles */
@media (max-width: 768px) {
    #productosTable {
        font-size: 12px;
    }
    
    #productosTable th,
    #productosTable td {
        padding: 8px 4px;
    }
    
    #productosTable .btn-sm {
        padding: 4px 8px;
        font-size: 11px;
    }
}



/* Estilos para el spinner de carga */
.spinner-api {
    color: #6861ce;
}

/* Animación para cuando se autocompletan los campos */
.input-autocompleted {
    animation: pulseGreen 0.5s ease;
}

@keyframes pulseGreen {
    0% {
        background-color: #d4edda;
    }
    100% {
        background-color: white;
    }
}
</style>

<div
    class="container">
    <div class="page-inner">
        <div
            class="card">

            <div class="card-body">

                <h4 class="card-title"><i class="fas fa-dolly"></i> Venta Rapida</h4>
                <div class="mb-3">
                    <div class="card-sub">
                        Aquí podrás realizar ventas de cuando un cliente viene a realizar corte y/o compra de materiales.
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h6 class="card-title"><i class="fas fa-chess-queen"></i> Artículos</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-center flex-wrap">
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

                                        <li class="nav-item me-3">
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
                                <div class="table-responsive mt-4">
                                    <table class="table table-hover table-striped align-middle" id="productosTable">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Artículo</th>
                                                <th>Categoría</th>
                                                <th>Tipo</th>
                                                <th>Dimensión</th>
                                                <th>Color</th>
                                                <th class="text-center">Stock</th>
                                                <th class="text-end">Precio</th>
                                                <th class="text-center">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody id="productoContainer">
                                            <!-- Los productos se generarán dinámicamente aquí -->
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Paginación -->
                                <div id="pagination" class="d-flex justify-content-center mt-4">
                                    <button id="prevPage" class="btn btn-secondary btn-round mx-2" onclick="changePage(-1)">
                                        <i class="fas fa-chevron-left"></i> Anterior
                                    </button>
                                    <button id="nextPage" class="btn btn-secondary btn-round mx-2" onclick="changePage(1)">
                                        Siguiente <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>




                                <script>
                                    const products = <?php echo json_encode(listarProductosVenta1($sucursal_id)); ?>;
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
                                            const flag_color = stock === 0.00 ? "text-danger fw-bold" : "";
                                            const flag_badge = stock === 0.00 ? '<span class="badge bg-danger ms-2">SIN STOCK</span>' : "";
                                            
                                            const row = document.createElement('tr');
                                            row.className = stock === 0.00 ? 'table-danger' : '';
                                            
                                            row.innerHTML = `
                                                <td class="${flag_color}">
                                                    <strong>${product.articulo}</strong> 
                                                    ${flag_badge}
                                                </td>
                                                <td><span class="badge bg-info">${product.categoria}</span></td>
                                                <td>${product.tipo}</td>
                                                <td>${product.dimension}</td>
                                                <td>${product.color}</td>
                                                <td class="text-center ${flag_color}">
                                                    <strong>${product.stock}</strong>
                                                </td>
                                                <td class="text-end">
                                                    <strong class="text-success">S/ ${product.precio_venta}</strong>
                                                </td>
                                                <td class="text-center">
                                                    <button 
                                                        class="btn btn-success btn-sm btn-round" 
                                                        onclick='fn_agregar_venta(${JSON.stringify(product).replace(/'/g, "&#39;")})'
                                                        ${stock === 0.00 ? 'disabled' : ''}>
                                                        <i class="fas fa-plus"></i> Agregar
                                                    </button>
                                                </td>
                                            `;
                                            container.appendChild(row);
                                        });
                                        // Si no hay productos, mostrar mensaje
                                        if (productsToDisplay.length === 0) {
                                            const row = document.createElement('tr');
                                            row.innerHTML = `
                                                <td colspan="8" class="text-center text-muted py-4">
                                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                                    <p class="mb-0">No se encontraron productos</p>
                                                </td>
                                            `;
                                            container.appendChild(row);
                                        }

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


                <div class=" modal fade" id="modalSoloCorte" tabindex="-1" aria-labelledby="modalSoloCorteLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">

                            <div class="modal-body">
                                <div class="col-12 p-4 bg-light rounded">
                                    <h4 class="card-title text-center"><i class="fas fa-cut"></i> Opciones de Corte</h4>

                                    <div class="card-sub text-center">
                                        Aqui podras agregar los minutos y el precio del servicio solo corte.
                                    </div>
                                    <div class="mb-4">
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
                                        <div class="text-center mt-3">
                                            <button type="button" class="btn btn-secondary rounded-5" id="btn_agregar_solocorte">Agregar</button>
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

                            <div class="modal-body">
                                <h4 class="card-title text-center" id="modalCantidadCorteLabel">Configurar Cantidad o Corte</h4>

                                <div class="card-sub text-center">
                                    Aquí ingresa la cantidad y/o corte del articulo
                                </div>
                                <div class="container-fluid">
                                    <!-- Sección de cantidad -->
                                    <div class="row mb-3">
                                        <div class="col-12 p-3 bg-light rounded">
                                            <h6 id="nombreArticulo" class="fw-bold text-center mb-3">Nombre del artículo</h6>
                                            <div class="d-flex justify-content-center align-items-center">
                                                <button id="btnRestarCantidad" class="btn btn-danger btn-round">-</button>
                                                <input id="inputCantidad" type="number" class="form-control text-center mx-2 " value="1" style="width: 80px; font-size: 1.2rem;" />
                                                <button id="btnSumarCantidad" class="btn btn-success btn-round">+</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sección de corte (solo visible si cantidad = 1) -->
                                    <div id="seccionCorte" class="row mb-3" style="display: none;">
                                        <div class="col-12 p-4 bg-light rounded">
                                            <div class="mb-4">
                                                <div class="text-center" style="flex: 1;">
                                                    <div class="row justify-content-center align-items-center g-2">
                                                        <div class="col-sm-6">
                                                            <p class="mb-1">Minutos Corte</p>
                                                            <div class="d-flex justify-content-center align-items-center mb-2">
                                                                <button id="btnRestarCorte" class="btn btn-danger btn-round">-</button>
                                                                <input id="cantidadCorte" type="number" class="form-control text-center mx-2 " value="0" style="width: 80px; font-size: 1.2rem;" />
                                                                <button id="btnSumarCorte" class="btn btn-success btn-round">+</button>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="text-center" style="flex: 1;">
                                                                <p class="mb-1">Precio Corte</p>
                                                                <div class="w-100 d-flex justify-content-center mb-1">
                                                                    <input id="precioCorte" type="number" class="form-control text-center mx-2 " value="1.5" style="width: 90px; font-size: 1.2rem;" />
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
                                                <hr>

                                                <div class="mb-3">
                                                    <p class="mb-1">Detalle</p>
                                                    <textarea class="form-control" name="" id="idTextAreaDetalleInsert" rows="3" placeholder="Describa medidas, restante, etc."></textarea>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center mb-3">
                                        <button id="btnConfirmarCantidad" class="btn btn-secondary rounded-5" style="width: 120px;">Confirmar</button>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <!-- Botón Confirmar a la izquierda -->
                                <button type="button" class="btn btn-danger rounded-5" data-bs-dismiss="modal">Cerrar</button>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal para registrar Cliente -->
                <!-- Modal para registrar Cliente -->
                <div class="modal fade" id="modalCliente" tabindex="-1" aria-labelledby="modalClienteLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalClienteLabel">Registrar Cliente</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Pills para seleccionar entre Persona y Empresa -->
                            <ul class="nav nav-pills nav-secondary nav-pills-no-bd" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active" id="pills-persona-tab" data-bs-toggle="pill" data-bs-target="#pills-persona" type="button" role="tab" aria-controls="pills-persona" aria-selected="true">Persona</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="pills-empresa-tab" data-bs-toggle="pill" data-bs-target="#pills-empresa" type="button" role="tab" aria-controls="pills-empresa" aria-selected="false">Empresa</button>
                                </li>
                            </ul>
                            <hr>
                            <div class="tab-content mt-3" id="pills-tabContent">
                                <!-- ============ FORMULARIO PERSONA ============ -->
                                <div class="tab-pane fade show active" id="pills-persona" role="tabpanel" aria-labelledby="pills-persona-tab">
                                    <div class="mb-3">
                                        <label for="numeroDocumentoPersona" class="form-label">
                                            Número de DNI <span class="fw-bold text-danger">*</span>
                                            <small class="text-muted">(Autocompletado)</small>
                                        </label>
                                        <div class="input-group">
                                            <input type="text" 
                                                class="form-control" 
                                                id="numeroDocumentoPersona" 
                                                placeholder="Ingrese DNI de 8 dígitos"
                                                maxlength="8">
                                            <button class="btn btn-outline-primary" 
                                                    type="button" 
                                                    id="btnBuscarDNI"
                                                    title="Buscar DNI">
                                                <i class="fas fa-search"></i> Buscar
                                            </button>
                                        </div>
                                        <div class="invalid-feedback" id="error-numeroDocumentoPersona"></div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="nombresPersona" class="form-label">Nombres <span class="fw-bold text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nombresPersona" placeholder="Nombres">
                                        <div class="invalid-feedback" id="error-nombresPersona"></div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="apellidosPersona" class="form-label">Apellidos <span class="fw-bold text-danger">*</span></label>
                                        <input type="text" class="form-control" id="apellidosPersona" placeholder="Apellidos">
                                        <div class="invalid-feedback" id="error-apellidosPersona"></div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="telefonoPersona" class="form-label">Teléfono Móvil</label>
                                        <input type="text" class="form-control" id="telefonoPersona" placeholder="Teléfono Móvil" maxlength="9">
                                        <div class="invalid-feedback" id="error-telefonoPersona"></div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="emailPersona" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="emailPersona" placeholder="Email">
                                        <div class="invalid-feedback" id="error-emailPersona"></div>
                                    </div>
                                </div>

                                <!-- ============ FORMULARIO EMPRESA ============ -->
                                <div class="tab-pane fade" id="pills-empresa" role="tabpanel" aria-labelledby="pills-empresa-tab">
                                    <div class="mb-3">
                                        <label for="numeroDocumentoEmpresa" class="form-label">
                                            Número de RUC <span class="fw-bold text-danger">*</span>
                                            <small class="text-muted">(Autocompletado)</small>
                                        </label>
                                        <div class="input-group">
                                            <input type="text" 
                                                class="form-control" 
                                                id="numeroDocumentoEmpresa" 
                                                placeholder="Ingrese RUC de 11 dígitos"
                                                maxlength="11">
                                            <button class="btn btn-outline-primary" 
                                                    type="button" 
                                                    id="btnBuscarRUC"
                                                    title="Buscar RUC">
                                                <i class="fas fa-search"></i> Buscar
                                            </button>
                                        </div>
                                        <div class="invalid-feedback" id="error-numeroDocumentoEmpresa"></div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="nombreComercial" class="form-label">Nombre Comercial <span class="fw-bold text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nombreComercial" placeholder="Nombre Comercial">
                                        <div class="invalid-feedback" id="error-nombreComercial"></div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="razonSocial" class="form-label">Razón Social <span class="fw-bold text-danger">*</span></label>
                                        <input type="text" class="form-control" id="razonSocial" placeholder="Razón Social">
                                        <div class="invalid-feedback" id="error-razonSocial"></div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="emailEmpresa" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="emailEmpresa" placeholder="Email">
                                        <div class="invalid-feedback" id="error-emailEmpresa"></div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="telefonoEmpresa" class="form-label">Teléfono Móvil</label>
                                        <input type="text" class="form-control" id="telefonoEmpresa" placeholder="Teléfono Móvil" maxlength="9">
                                        <div class="invalid-feedback" id="error-telefonoEmpresa"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-light p-3" role="alert">
                                <p class="mb-0">Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-success" id="btnRegistrarCliente">Registrar</button>
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
                                            <h4 class="card-title fs-1 fw-bold"><i class="fas fa-credit-card"></i> Realizar Pago </h4>
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
                                            <span>ID Usuario Reserva: <span id="idUsuario"><?php echo $id_usuario_s ?></span>
                                                <br>
                                                <span><strong>Atendiendo la Transacción:</strong> <span id="idAtencionFinal"><?php echo $id_usuario_s . "-" . $nombre . ", " . $ape_usuario ?></span></span>
                                        </div>
                                        <hr>
                                        <div class="accordion" id="accordionExample">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingOne">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                        Datos del Cliente
                                                    </button>
                                                </h2>
                                                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                    <div class="accordion-body">
                                                        <div class="mb-3">
                                                            <label for="" class="form-label"><strong><i class="fas fa-user-tie"></i> Cliente</strong></label>
                                                            <div class="d-flex align-items-center">
                                                                <input
                                                                    type="text"
                                                                    class="form-control"
                                                                    id="nombreCliente"
                                                                    placeholder="AGREGAR EL NOMBRE DEL CLIENTE O DNI" />
                                                                <!-- Botón "+" al lado del input -->
                                                                <button type="button" class="btn btn-primary ms-2 rounded-5" id="btnAbrirModalCliente">
                                                                    <i class="fas fa-user-plus"></i> <!-- Ícono "+" -->
                                                                </button>
                                                            </div>
                                                            <!-- Contenedor para las sugerencias -->
                                                            <div id="sugerencias" class="list-group position-absolute w-100"></div>
                                                        </div>
                                                        <div class="row justify-content-center align-items-center g-2">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="" class="form-label"><i class="fas fa-phone-square"></i><strong> Número de Telefono</strong></label>
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
                                                                    <label for="" class="form-label"><i class="fas fa-envelope"></i> <strong>Correo Electronico</strong></label>
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
                                                    </div>
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
                                                    <label for="montoVentaFinal" class="form-label">Monto Final de Venta</label>
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
                                                    <a class="nav-link active rounded-5" id="pills-home-tab-icon" data-bs-toggle="pill" href="#pago-directo" role="tab" aria-controls="pago-directo" aria-selected="true">
                                                        Pago Directo
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link rounded-5" id="pills-profile-tab-icon" data-bs-toggle="pill" href="#pago-credito" role="tab" aria-controls="pago-credito" aria-selected="false">
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
                                                                    <div
                                                                        class="selectgroup selectgroup-secondary selectgroup-pills">

                                                                        <label class="selectgroup-item">
                                                                            <input
                                                                                type="radio"
                                                                                name="icon-input"
                                                                                value="boleta"
                                                                                checked=""
                                                                                class="selectgroup-input" />
                                                                            <span
                                                                                class="selectgroup-button selectgroup-button-icon">Boleta</span>
                                                                        </label>
                                                                        <label class="selectgroup-item">
                                                                            <input
                                                                                type="radio"
                                                                                name="icon-input"
                                                                                value="factura"
                                                                                class="selectgroup-input" />
                                                                            <span
                                                                                class="selectgroup-button selectgroup-button-icon">Factura</span>
                                                                        </label>

                                                                    </div>
                                                                </div>
                                                            </div>


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
                                                        <a class="btn btn-success rounded-5" href="#" role="button" onclick="fn_pagar_directo()"><i class="fas fa-hand-holding-usd"></i> Pagar</a>
                                                    </div>
                                                </div>

                                                <div class="tab-pane fade" id="pago-credito" role="tabpanel" aria-labelledby="pills-profile-tab-icon">
                                                    <div class="card-sub">
                                                        <div class="text-center">
                                                            Aquí podrás elegir si realizan pagos al Crédito.
                                                        </div>
                                                    </div>

                                                    <div class="card-sub">
                                                        <div class="text-center">

                                                            Si un cliente te deja pagado algo de la venta, <strong>REGISTRALO</strong>.
                                                            <br>
                                                            Si no, deja en blanco y darle click al boton Realizar <br><strong>Pago a Credito</strong>
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
                                                            <a class="btn btn-success rounded-5" href="#" role="button" onclick="fn_pagar_credito()"><i class="fas fa-hands-helping"></i> Realizar Pago a Credito</a>
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
                                    class="btn btn-danger rounded-5"
                                    data-bs-dismiss="modal">
                                    Salir
                                </button>

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
                    <div class="card-body">
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
                                        <th scope="col">Total Corte o Impresión 3D </th>
                                        <th scope="col">Articulo</th>
                                        <th scope="col">Cantidad</th>
                                        <th scope="col">Precio Unitario</th>
                                        <th scope="col">Sub Total (S/)</th>
                                        <th scope="col">Accion</th>
                                        <th scope="col">IDMOVIMIENTO</th>
                                        <th scope="col">NOTA ARCHIVO</th>

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


        <div class="card p-2">
            <div class="row mt-2">
                <div class="col-md-4">
                    <div class="card card-stats card-round">
                        <div class="card-body text-center">
                            <h5 id="label_total_cortes" class="card-title">Total en Cortes y Impresion 3D S/</h5>
                            <span id="id_subtotal_cortes" style="font-size: 1.3rem;" aria-labelledby="label_total_cortes">00.00</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-stats card-round">
                        <div class="card-body text-center">
                            <h5 id="label_total_articulos" class="card-title">Total Artículos S/</h5>
                            <span id="id_subtotal_articulos" style="font-size: 1.3rem;" aria-labelledby="label_total_articulos">00.00</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-primary card-stats card-round">
                        <div class="card-body text-center">
                            <h5 id="label_total_general" class="card-title">Total S/</h5>
                            <span id="id_subtotal_general" style="font-size: 1.3rem;" aria-labelledby="label_total_general">00.00 </span>
                        </div>
                    </div>
                </div>
                <div class="card-body text-center">
                    <button id="btnRealizarPago" disabled="true" type="button" class="btn btn-success btn-round" style="width: 50%; height: 50px; font-size: 15px;">
                        <i class="fas fa-shopping-basket"></i> Realizar Venta
                    </button>
                </div>

            </div>
        </div>
    </div>


</div>

<!-- Modal  -->
<div class="modal fade" id="modalGenerico" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalGenericoLabel" aria-hidden="false">
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



<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
<script src="assets/js/scriptNotify.js"></script>
<script>
// Si usas los botones integrados, agrega estos eventos:
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Evento para el botón de buscar DNI
    const btnBuscarDNI = document.getElementById('btnBuscarDNI');
    if (btnBuscarDNI) {
        btnBuscarDNI.addEventListener('click', async function() {
            const inputDNI = document.getElementById('numeroDocumentoPersona');
            const dni = inputDNI.value.trim();
            
            // Validar que sea un DNI válido
            if (dni.length !== 8 || !/^\d{8}$/.test(dni)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'DNI inválido',
                    text: 'Por favor, ingrese un DNI de 8 dígitos',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
                return;
            }
            
            // Mostrar spinner en el botón
            const iconoOriginal = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Buscando...';
            this.disabled = true;
            
            // Disparar el evento blur para activar la búsqueda
            inputDNI.dispatchEvent(new Event('blur'));
            
            // Restaurar el botón después de 2 segundos
            setTimeout(() => {
                this.innerHTML = iconoOriginal;
                this.disabled = false;
            }, 2000);
        });
    }

    // Evento para el botón de buscar RUC
    const btnBuscarRUC = document.getElementById('btnBuscarRUC');
    if (btnBuscarRUC) {
        btnBuscarRUC.addEventListener('click', async function() {
            const inputRUC = document.getElementById('numeroDocumentoEmpresa');
            const ruc = inputRUC.value.trim();
            
            // Validar que sea un RUC válido
            if (ruc.length !== 11 || !/^\d{11}$/.test(ruc)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'RUC inválido',
                    text: 'Por favor, ingrese un RUC de 11 dígitos',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
                return;
            }
            
            // Mostrar spinner en el botón
            const iconoOriginal = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Buscando...';
            this.disabled = true;
            
            // Disparar el evento blur para activar la búsqueda
            inputRUC.dispatchEvent(new Event('blur'));
            
            // Restaurar el botón después de 2 segundos
            setTimeout(() => {
                this.innerHTML = iconoOriginal;
                this.disabled = false;
            }, 2000);
        });
    }
});
</script>

document.getElementById('btnBuscarRUC')?.addEventListener('click', function() {
    document.getElementById('numeroDocumentoEmpresa').dispatchEvent(new Event('blur'));
});
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Obtener elementos de los tabs
        const pagoDirectoTab = document.getElementById("pills-home-tab-icon");
        const pagoCreditoTab = document.getElementById("pills-profile-tab-icon");


        // Obtener elementos del acordeón
        const collapseOne = document.getElementById("collapseOne");
        const accordionButton = document.querySelector(".accordion-button"); // Botón del acordeón
        const nombreCliente = document.getElementById("nombreCliente");
        const telefonoCliente = document.getElementById("idUpdateNumTelefonoCliente");
        const correoCliente = document.getElementById("idUpdateCorreoCliente");
        const idPersona = document.getElementById("idPersona");
        // Función para resetear los valores del formulario
        function resetearValores() {
            nombreCliente.value = "";
            telefonoCliente.value = "";
            correoCliente.value = "";
            idPersona.textContent = "#";
        }

        // Evento cuando se selecciona "Pago Directo"
        pagoDirectoTab.addEventListener("click", function() {
            resetearValores(); // Reiniciar valores
            collapseOne.classList.remove("show"); // Ocultar acordeón
            accordionButton.classList.add("collapsed"); // Agregar clase "collapsed"
            accordionButton.setAttribute("aria-expanded", "false"); // Cambiar atributo
        });

        // Evento cuando se selecciona "Pago Crédito"
        pagoCreditoTab.addEventListener("click", function() {
            resetearValores(); // Reiniciar valores
            collapseOne.classList.add("show"); // Mostrar acordeón
            accordionButton.classList.remove("collapsed"); // Quitar clase "collapsed"
            accordionButton.setAttribute("aria-expanded", "true"); // Cambiar atributo
        });
        ////////


    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Registrar eventos para los botones de incremento y decremento (fuera del modal)
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

        // Para los incrementos en el precio
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

        // Abrir el modal y manejar el evento de agregar corte
        document.getElementById('btnAbrirModalSolo').addEventListener('click', function(event) {
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
            const modalElement = document.getElementById('modalSoloCorte');
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: 'static', // Evita que se cierre al hacer clic fuera
                keyboard: false // Evita que se cierre con la tecla 'Esc'
            });

            // Seleccionar el botón "Agregar"
            const btn_agregar = document.getElementById('btn_agregar_solocorte');
            btn_agregar.textContent = 'Agregar';

            btn_agregar.replaceWith(btn_agregar.cloneNode(true));

            // Seleccionar nuevamente el botón clonado
            const nuevoBtnAgregar = document.getElementById('btn_agregar_solocorte');

            // Volver a agregar el evento para agregar datos
            nuevoBtnAgregar.addEventListener("click", agregarDatosCorte);


            // Limpiar los campos del formulario
            document.getElementById("cantidad_solocorte").value = 0;
            document.getElementById("precioSoloCorte").value = 1.5;
            modal.show(); // Muestra el modal
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



            const btn_agregar = document.getElementById('btn_agregar_solocortev2');
            btn_agregar.textContent = 'Agregar';

            btn_agregar.replaceWith(btn_agregar.cloneNode(true));


            const nuevoBtnAgregar = document.getElementById('btn_agregar_solocortev2');


            nuevoBtnAgregar.addEventListener("click", fn_agregar_impresion_a_tabla);


            modal.show(); // Muestra el modal
        });

        function fn_agregar_impresion_a_tabla() {
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
            const datosImpresion3D = [{
                id: '0',
                minutos: cantidadMinutos,
                tarifa: tarifa,
                costo: cantidadMinutos * tarifa,
                articulo: 'MAQUINA DE IMPRESION  3D',
                idmovimiento: 15,
            }];

            console.log(datosImpresion3D);
            fn_solo_impresion_tabla(datosImpresion3D);
            document.getElementById('cantidad_solocortev2').value = '10';
            document.getElementById('precioSoloCortev2').value = '1.5'; // Valor inicial

            const modalElement = document.getElementById('modalSoloCorteMaquina2');
            const modal = bootstrap.Modal.getInstance(modalElement);
            modal.hide();
            showNotification("success");

        }
        // Función que maneja el evento de agregar datos
        function agregarDatosCorte() {
            const cantidadMinutos = parseInt(document.getElementById('cantidad_solocorte').value) || 0;
            const tarifa = parseFloat(document.getElementById('precioSoloCorte').value) || 0;

            const inputMonto = document.getElementById('cantidad_solocorte');
            const divContainer = inputMonto.closest('.d-flex');
            const mensajeErrorExistente = document.querySelector('.error-message');
            if (mensajeErrorExistente) mensajeErrorExistente.remove();
            inputMonto.classList.remove('error-input');


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
            const datosCorte = [{
                id: '0', // Id del corte
                minutos: cantidadMinutos, // Minutos registrados
                tarifa: tarifa, // Costo por minuto
                costo: cantidadMinutos * tarifa,
                articulo: 'SOLO CORTE',
                idmovimiento: 6,
            }];

            console.log(datosCorte);

            // Llamar a la función fn_solo_corte_tabla para agregar a la tabla
            fn_solo_corte_tabla(datosCorte);

            // Reiniciar los minutos a 0 en la interfaz
            document.getElementById('cantidad_solocorte').value = '0';
            document.getElementById('precioSoloCorte').value = '1.5'; // Valor inicial

            // Ocultar el modal
            const modalElement = document.getElementById('modalSoloCorte');
            const modal = bootstrap.Modal.getInstance(modalElement);
            modal.hide();
            showNotification("success");
        }

        function fn_solo_impresion_tabla(datosCorte) {
            var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];

            datosCorte.forEach(corte => {
                let nuevaFila = tabla.insertRow();

                nuevaFila.insertCell(0).textContent = corte.id; // ID
                nuevaFila.insertCell(1).textContent = corte.minutos; // Minutos
                nuevaFila.insertCell(2).textContent = corte.tarifa; // Costo x Minuto
                nuevaFila.insertCell(3).textContent = corte.costo; // Costo x Minuto
                nuevaFila.insertCell(4).textContent = corte.articulo; // Artículo
                nuevaFila.insertCell(5).textContent = '-'; // Cantidad fija por corte
                nuevaFila.insertCell(6).textContent = '-'; // Precio unitario
                nuevaFila.insertCell(7).textContent = (corte.costo).toFixed(2); // Subtotal

                let accionCell = nuevaFila.insertCell(8);
                nuevaFila.insertCell(9).textContent = corte.idmovimiento; // Subtotal

                // 1. Botón de Editar
                let botonEditar = document.createElement("button");
                botonEditar.classList.add("btn", "btn-warning", "btn-round", "ms-2", "text-white", "px-3", "py-2");
                botonEditar.innerHTML = '<i class="fas fa-edit"></i>'; // Ícono de editar con texto

                // Agregar el botón de editar a la celda de acciones
                accionCell.appendChild(botonEditar);

                // 2. Botón de Eliminar
                let botonEliminar = document.createElement("button");
                botonEliminar.classList.add("btn", "btn-danger", "btn-round", "ms-2", "px-3", "py-2");
                botonEliminar.innerHTML = '<i class="fas fa-trash"></i>'; // Ícono de eliminar con texto

                accionCell.appendChild(botonEliminar);

                botonEditar.addEventListener("click", () => {
                    // Llenamos el modal con los datos del corte

                    document.getElementById("cantidad_solocortev2").value = corte.minutos || 0; // Minutos corte
                    document.getElementById("precioSoloCortev2").value = corte.tarifa || 1.5; // Precio corte

                    // Mostrar el modal
                    const modalElement = document.getElementById('modalSoloCorteMaquina2');
                    const modal = new bootstrap.Modal(modalElement, {
                        backdrop: 'static',
                        keyboard: false
                    });

                    modal.show();

                    // El botón de agregar se convierte en "Actualizar" para modificar los valores
                    const btn_agregar = document.getElementById('btn_agregar_solocortev2');
                    btn_agregar.textContent = 'Actualizar'; // Cambiar texto del botón
                    btn_agregar.removeEventListener("click", fn_agregar_impresion_a_tabla);

                    // Actualizar el corte en la tabla cuando se presiona "Actualizar"
                    btn_agregar.addEventListener("click", function() {
                        corte.minutos = parseInt(document.getElementById("cantidad_solocortev2").value) || 0;
                        corte.tarifa = parseFloat(document.getElementById("precioSoloCortev2").value) || 1.5;
                        corte.costo = corte.minutos * corte.tarifa; // Recalcular el costo

                        // Actualizar las celdas de la fila con los nuevos valores
                        nuevaFila.cells[1].textContent = corte.minutos; // Minutos
                        nuevaFila.cells[2].textContent = corte.tarifa; // Costo x Minuto
                        nuevaFila.cells[3].textContent = corte.costo.toFixed(2); // Costo total

                        // Recalcular el subtotal
                        nuevaFila.cells[7].textContent = corte.costo.toFixed(2); // Subtotal

                        // Cerrar el modal
                        modal.hide();
                        showNotification("success");
                        fn_obtener_total(); // Recalcular los totales después de editar
                    });
                });

                // Función para manejar el botón de eliminar
                botonEliminar.addEventListener("click", () => {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "Esta acción no se puede deshacer.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Si el usuario confirma, eliminamos la fila
                            const fila = botonEliminar.closest("tr");
                            fila.remove(); // Eliminar la fila

                            // Recalcular los totales
                            fn_obtener_total();

                            // Mostrar mensaje de éxito
                            showNotification("success");
                        }
                    });
                });
            });

            fn_obtener_total();
        }

        function fn_solo_corte_tabla(datosCorte) {
            var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];

            datosCorte.forEach(corte => {
                let nuevaFila = tabla.insertRow();

                nuevaFila.insertCell(0).textContent = corte.id; // ID
                nuevaFila.insertCell(1).textContent = corte.minutos; // Minutos
                nuevaFila.insertCell(2).textContent = corte.tarifa; // Costo x Minuto
                nuevaFila.insertCell(3).textContent = corte.costo; // Costo x Minuto
                nuevaFila.insertCell(4).textContent = corte.articulo; // Artículo
                nuevaFila.insertCell(5).textContent = '-'; // Cantidad fija por corte
                nuevaFila.insertCell(6).textContent = '-'; // Precio unitario
                nuevaFila.insertCell(7).textContent = (corte.costo).toFixed(2); // Subtotal

                let accionCell = nuevaFila.insertCell(8);
                nuevaFila.insertCell(9).textContent = corte.idmovimiento; // Subtotal

                // 1. Botón de Editar
                let botonEditar = document.createElement("button");
                botonEditar.classList.add("btn", "btn-warning", "btn-round", "ms-2", "text-white", "px-3", "py-2");
                botonEditar.innerHTML = '<i class="fas fa-edit"></i>'; // Ícono de editar con texto

                // Agregar el botón de editar a la celda de acciones
                accionCell.appendChild(botonEditar);

                // 2. Botón de Eliminar
                let botonEliminar = document.createElement("button");
                botonEliminar.classList.add("btn", "btn-danger", "btn-round", "ms-2", "px-3", "py-2");
                botonEliminar.innerHTML = '<i class="fas fa-trash"></i>'; // Ícono de eliminar con texto

                accionCell.appendChild(botonEliminar);

                botonEditar.addEventListener("click", () => {
                    // Llenamos el modal con los datos del corte

                    document.getElementById("cantidad_solocorte").value = corte.minutos || 0; // Minutos corte
                    document.getElementById("precioSoloCorte").value = corte.tarifa || 1.5; // Precio corte

                    // Mostrar el modal
                    const modalElement = document.getElementById('modalSoloCorte');
                    const modal = new bootstrap.Modal(modalElement, {
                        backdrop: 'static',
                        keyboard: false
                    });

                    modal.show();

                    // El botón de agregar se convierte en "Actualizar" para modificar los valores
                    const btn_agregar = document.getElementById('btn_agregar_solocorte');
                    btn_agregar.textContent = 'Actualizar'; // Cambiar texto del botón
                    btn_agregar.removeEventListener("click", agregarDatosCorte);

                    // Actualizar el corte en la tabla cuando se presiona "Actualizar"
                    btn_agregar.addEventListener("click", function() {
                        corte.minutos = parseInt(document.getElementById("cantidad_solocorte").value) || 0;
                        corte.tarifa = parseFloat(document.getElementById("precioSoloCorte").value) || 1.5;
                        corte.costo = corte.minutos * corte.tarifa; // Recalcular el costo

                        // Actualizar las celdas de la fila con los nuevos valores
                        nuevaFila.cells[1].textContent = corte.minutos; // Minutos
                        nuevaFila.cells[2].textContent = corte.tarifa; // Costo x Minuto
                        nuevaFila.cells[3].textContent = corte.costo.toFixed(2); // Costo total

                        // Recalcular el subtotal
                        nuevaFila.cells[7].textContent = corte.costo.toFixed(2); // Subtotal

                        // Cerrar el modal
                        modal.hide();
                        showNotification("success");
                        fn_obtener_total(); // Recalcular los totales después de editar
                    });
                });

                // Función para manejar el botón de eliminar
                botonEliminar.addEventListener("click", () => {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "Esta acción no se puede deshacer.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Si el usuario confirma, eliminamos la fila
                            const fila = botonEliminar.closest("tr");
                            fila.remove(); // Eliminar la fila

                            // Recalcular los totales
                            fn_obtener_total();

                            // Mostrar mensaje de éxito
                            showNotification("success");
                        }
                    });
                });
            });

            fn_obtener_total();
        }

    });
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

    function fn_agregar_venta(datosArticulo) {
        console.log("ARTICULO DE MRD");
        console.log(datosArticulo)
        //if (verificarSiArticuloExiste(datosArticulo['id'])) {
        // Swal.fire({
        //    icon: 'info',
        //  title: '¡Artículo ya registrado!',
        //text: 'Este artículo ya está en la tabla.',
        //confirmButtonText: 'Aceptar'
        //});
        //} else {
        const modalCantidad = new bootstrap.Modal(document.getElementById('modalCantidad'));

        // Configurar el nombre del artículo
        const nombreArticulo = document.getElementById('nombreArticulo');
        nombreArticulo.textContent = `Artículo: ${datosArticulo.articulo || "Sin nombre"}`;
        // Resetear valores del modal
        const inputCantidad = document.getElementById('inputCantidad');
        const seccionCorte = document.getElementById('seccionCorte');
        const cantidadCorte = document.getElementById('cantidadCorte');
        const precioCorte = document.getElementById('precioCorte');
        const textAreatInsert = document.getElementById("idTextAreaDetalleInsert").value;
        datosArticulo.nota = textAreatInsert;
        const mensajeErrorExistente = document.querySelector('.error-message');
        if (mensajeErrorExistente) mensajeErrorExistente.remove();
        cantidadCorte.classList.remove('error-input');

        inputCantidad.value = 1; // Cantidad por defecto
        cantidadCorte.value = 0; // Resetear minutos corte
        precioCorte.value = 0; // Precio por defecto

        // Mostrar u ocultar la sección de corte según datosArticulo.corte
        if (datosArticulo.corte) {
            precioCorte.value = 1.5;
            seccionCorte.style.display = 'block';
        } else {
            seccionCorte.style.display = 'none';
        }

        // Configurar botones de cantidad
        document.getElementById('btnRestarCantidad').onclick = () => {
            let cantidad = parseInt(inputCantidad.value, 10);

            // Restar si la cantidad es mayor a 1
            if (cantidad > 1) {
                inputCantidad.value = cantidad - 1; // Restar cantidad
            }

            // Verificar si la cantidad es 1 y el artículo tiene corte
            if (inputCantidad.value == 1 && datosArticulo.corte) {
                precioCorte.value = 1.5;
                seccionCorte.style.display = 'block'; // Mostrar sección de corte

            } else if (inputCantidad.value > 1) {
                // Ocultar sección de corte si la cantidad es mayor a 1
                precioCorte.value = 0;
                cantidadCorte.value = 0;
                seccionCorte.style.display = 'none';
            }
        };

        document.getElementById('btnSumarCantidad').onclick = () => {
            let cantidad = parseInt(inputCantidad.value, 10) + 1; // Aumentar primero
            let cantidadStock = datosArticulo.stock; // Stock disponible

            if (cantidad > cantidadStock) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stock insuficiente',
                    text: `No puedes agregar más de ${cantidadStock} unidades.`,
                    confirmButtonText: 'Entendido'
                });
            } else {
                inputCantidad.value = cantidad; // Solo actualiza si es válido

                if (cantidad === 1 && datosArticulo.corte) {
                    precioCorte.value = 1.5;
                    seccionCorte.style.display = 'block';
                } else {
                    precioCorte.value = 0;
                    cantidadCorte.value = 0;
                    seccionCorte.style.display = 'none';
                }
            }
        };

        // Configurar botones de corte
        document.getElementById('btnRestarCorte').onclick = () => {
            let corte = parseInt(cantidadCorte.value, 10); // Cambié textContent por value
            if (corte > 0) cantidadCorte.value = corte - 1; // Cambié textContent por value
        };

        document.getElementById('btnSumarCorte').onclick = () => {
            let corte = parseInt(cantidadCorte.value, 10); // Cambié textContent por value
            if (corte == 0) {
                cantidadCorte.value = 10; // Cambié textContent por value
            } else {
                cantidadCorte.value = corte + 1; // Cambié textContent por value
            }
        };

        // Botones para modificar precio
        document.getElementById('btnIncremento05').onclick = () => {
            precioCorte.value = (parseFloat(precioCorte.value) + 0.5).toFixed(2);
        };
        document.getElementById('btnIncremento1').onclick = () => {
            precioCorte.value = (parseFloat(precioCorte.value) + 1).toFixed(2);
        };
        document.getElementById('btnIncremento2').onclick = () => {
            precioCorte.value = (parseFloat(precioCorte.value) + 2).toFixed(2);
        };
        document.getElementById('btnIncremento5').onclick = () => {
            precioCorte.value = (parseFloat(precioCorte.value) + 5).toFixed(2);
        };
        

        // Confirmar cantidad y agregar a la tabla
        document.getElementById('btnConfirmarCantidad').onclick = () => {
            let cantidadSeleccionada = parseInt(inputCantidad.value, 10);
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

            
            datosArticulo.cantidad = parseInt(inputCantidad.value, 10);
            datosArticulo.minutos = parseInt(cantidadCorte.value, 10) || '-';
            datosArticulo.costo_por_minuto = isNaN(datosArticulo.minutos) === true ? '-' : parseFloat(precioCorte.value, 10);
            datosArticulo.id_movimiento = 1;

            if (datosArticulo.corte && datosArticulo.cantidad == 1) {
                const inputMonto = document.getElementById('cantidadCorte');
                const divContainer = inputMonto.closest('.d-flex');

            }

            let textAreatInsertv2 = document.getElementById("idTextAreaDetalleInsert").value;
            datosArticulo.nota = textAreatInsertv2;
            modalCantidad.hide();
            fn_agregar_articulo_tabla(datosArticulo);
            showNotification("success");
        };

        // Mostrar el modal
        modalCantidad.show();
        document.getElementById("idTextAreaDetalleInsert").value = "";

    }
    //}





    function fn_agregar_articulo_tabla(datosArticulo) {
        var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];

        // Insertamos una nueva fila en la tabla
        let nuevaFila = tabla.insertRow();
        console.log(datosArticulo);
        // Colocamos los valores de las celdas
        nuevaFila.insertCell(0).textContent = datosArticulo["id"]; // ID
        nuevaFila.insertCell(1).textContent = datosArticulo["minutos"] || '-'; // Minutos
        nuevaFila.insertCell(2).textContent = datosArticulo["costo_por_minuto"] || '-'; // Costo x Minuto
        nuevaFila.insertCell(3).textContent = datosArticulo["costo_por_minuto"] * datosArticulo["minutos"] || '-'; // Costo x Minuto
        nuevaFila.insertCell(4).textContent = datosArticulo["articulo"]; // Artículo
        nuevaFila.insertCell(5).textContent = datosArticulo["cantidad"]; // Cantidad
        nuevaFila.insertCell(6).textContent = datosArticulo["precio_venta"]; // Precio unitario

        let totalCorte = (datosArticulo["costo_por_minuto"] * datosArticulo["minutos"]) || 0;
        // Cálculo base del subtotal: cantidad * precio de venta
        let subtotal = datosArticulo["cantidad"] * datosArticulo["precio_venta"];

        // Sumar el "total corte" al subtotal si existe
        subtotal += totalCorte;

        // Asignamos el subtotal a la celda 7
        nuevaFila.insertCell(7).textContent = subtotal.toFixed(2); // Subtotal con 2 decimales
        // Celda para acciones
        let accionCell = nuevaFila.insertCell(8);
        // 3. Botón de Corte (si aplica)

        // 1. Botón de Editar
        let botonEditar = document.createElement("button");
        botonEditar.classList.add("btn", "btn-warning", "btn-round", "ms-2", "text-white", "px-3", "py-2");
        botonEditar.innerHTML = '<i class="fas fa-edit"></i>'; // Ícono de editar con texto

        // Agregar el botón de editar a la celda de acciones
        accionCell.appendChild(botonEditar);
        nuevaFila.insertCell(9).textContent = datosArticulo["id_movimiento"]; // Precio unitario
        nuevaFila.insertCell(10).textContent = datosArticulo["nota"]; 
        // Función para manejar el botón de editar
        botonEditar.addEventListener("click", () => {
            //document.getElementById("modalCantidadCorteLabel").innerText= "Hola BB";
            // Abrir el modal con la cantidad actual, nombre del artículo, y datos adicionales de corte

            document.getElementById("nombreArticulo").textContent = datosArticulo["articulo"];
            document.getElementById("inputCantidad").value = datosArticulo["cantidad"];

            // Mostrar los valores actuales de corte si es que existen


            // Mostrar u ocultar la sección de corte según datosArticulo.corte (solo se muestra si corte es true)
            const seccionCorte = document.getElementById("seccionCorte");
            if (datosArticulo["corte"] && datosArticulo["cantidad"] == 1) {
                document.getElementById("cantidadCorte").value =
                    datosArticulo["minutos"] === '-' ? 0 : (datosArticulo["minutos"] || 0);

                document.getElementById("precioCorte").value =
                    datosArticulo["costo_por_minuto"] === '-' ? 1.5 : (datosArticulo["costo_por_minuto"] || 1.5);
                seccionCorte.style.display = "block";
            } else {
                document.getElementById("cantidadCorte").value =
                    datosArticulo["minutos"] === '-' ? 0 : (datosArticulo["minutos"] || 0);
                datosArticulo["costo_por_minuto"] === '-' ? 0 : (datosArticulo["minutos"] || 0);
                seccionCorte.style.display = "none";
            }

            // Guardar el artículo actual para hacer la modificación posteriormente
            document.getElementById("btnConfirmarCantidad").onclick = function() {
                // Actualizamos la cantidad, minutos de corte y precio de corte en el objeto datosArticulo
                const cantidad = parseInt(document.getElementById("inputCantidad").value);
                const minutos = parseInt(document.getElementById("cantidadCorte").value) || '-';
                const precio = parseFloat(document.getElementById("precioCorte").value) || '-';

                if (datosArticulo["corte"] && cantidad == 1) {
                    const inputMonto = document.getElementById('cantidadCorte');
                    const divContainer = inputMonto.closest('.d-flex');


                    // Validar que el monto haya sido ingresado y sea mayor a 0
                    if (isNaN(minutos) || minutos <= 0) {
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
                }

                datosArticulo["cantidad"] = parseInt(document.getElementById("inputCantidad").value);
                datosArticulo["minutos"] = parseInt(document.getElementById("cantidadCorte").value) || '-';
                datosArticulo["costo_por_minuto"] = parseFloat(document.getElementById("precioCorte").value) || '-';

                // Actualizamos la celda de cantidad y subtotal en la tabla
                nuevaFila.cells[5].textContent = datosArticulo["cantidad"];
                nuevaFila.cells[1].textContent = datosArticulo["minutos"] || '-';
                nuevaFila.cells[2].textContent = datosArticulo["costo_por_minuto"] || '-';
                nuevaFila.cells[3].textContent = datosArticulo["minutos"] * datosArticulo["costo_por_minuto"] || '-';


                // Recalcular el subtotal considerando el precio de corte y minutos de corte
                let subtotal = datosArticulo["cantidad"] * datosArticulo["precio_venta"];
                subtotal += (datosArticulo["costo_por_minuto"] * datosArticulo["minutos"]) || 0;
                subtotal += (datosArticulo["minutosCorte"] * datosArticulo["precioCorte"]) || 0; // Considerar precio de corte

                nuevaFila.cells[7].textContent = subtotal.toFixed(2);

                // Cerramos el modal
                $('#modalCantidad').modal('hide');
                fn_obtener_total(); // Recalcular los totales después de editar
            };

            // Mostrar el modal
            $('#modalCantidad').modal('show');
        });
        // 2. Botón de Eliminar
        let botonEliminar = document.createElement("button");
        botonEliminar.classList.add("btn", "btn-danger", "btn-round", "ms-2", "px-3", "py-2");
        botonEliminar.innerHTML = '<i class="fas fa-trash"></i>'; // Ícono de eliminar con texto

        accionCell.appendChild(botonEliminar);

        // Función para manejar el botón de eliminar
        botonEliminar.addEventListener("click", () => {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Si el usuario confirma, eliminamos la fila
                    const fila = botonEliminar.closest("tr");
                    fila.remove(); // Eliminar la fila

                    // Recalcular los totales
                    fn_obtener_total();

                    // Mostrar mensaje de éxito
                    showNotification("success");
                }
            });
        });

        // Llamamos la función para recalcular los totales si es necesario
        fn_obtener_total();
    }



    function fn_limpiar_modal() {
        const acordeonContainer = document.getElementById('acordeonContainer');
        const globalContainer = document.getElementById('globalContainer');

        // Limpiar los contenedores donde se muestran los cortes y cantidades
        acordeonContainer.innerHTML = "";
        globalContainer.innerHTML = "";


    }

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

        const btnReserva = document.getElementById("btnRealizarPago");
        btnReserva.disabled = (total === 0);



    }

    function fn_obtener_totav2() {
        var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];
        var filas = tabla.getElementsByTagName("tr");
        var totalCorte = 0;
        var totalArticulos = 0;
        var total = 0;

        for (var i = 0; i < filas.length; i++) {
            var celdas = filas[i].getElementsByTagName("td");
            for (var i = 0; i < filas.length; i++) {
                var celdas = filas[i].getElementsByTagName("td");
                console.log("Fila " + (i + 1));
                for (var j = 0; j < celdas.length; j++) {
                    console.log("Celda " + (j) + ": " + celdas[j].innerText);
                }
            }
            console.log("i: " + i);
            console.log("Celdas: ", celdas);


            var subtotal_corte = (celdas[3].innerText) === '-' ? 0 : parseFloat(celdas[3].innerText);
            console.log("Subtotal en la celda[3]: " + subtotal);
            totalCorte += subtotal_corte;

            var pu_articulo = (celdas[5].innerText) === '-' ? 0 : parseFloat(celdas[5].innerText);
            var pu_articulo = (celdas[5].innerText) === '-' ? 0 : parseFloat(celdas[5].innerText);





            var cantidad = parseFloat(celdas[5].innerText) || 0;
            var monto = parseFloat(celdas[6].innerText) || 0;
            console.log("Cantidad: " + cantidad + ", Monto: " + monto);
            totalArticulos += cantidad * monto;

            // Verificar el valor de la celda 7 (Subtotal final)
            var totalFila = parseFloat(celdas[7].innerText) || 0;
            console.log("Total en la celda[7]: " + totalFila);
            total += totalFila;
        }

        var lbl_subtotal_cortes = document.getElementById("id_subtotal_cortes");
        var lbl_subtotal_articulos = document.getElementById("id_subtotal_articulos");
        var lbl_subtotal_general = document.getElementById("id_subtotal_general");

        console.log("Total Corte: " + totalCorte);
        console.log("Total Artículos: " + totalArticulos);
        console.log("Total General: " + total);

        lbl_subtotal_cortes.innerText = totalCorte.toFixed(2);
        lbl_subtotal_articulos.innerText = totalArticulos.toFixed(2);
        lbl_subtotal_general.innerText = total.toFixed(2);

        const btnReserva = document.getElementById("btnRealizarPago");
        btnReserva.disabled = (total === 0);
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

<!--Tabla-->
<script>
    document.addEventListener("DOMContentLoaded", function() {
    const nombreCliente = document.getElementById("nombreCliente");
    const sugerencias = document.getElementById("sugerencias");
    const numero_telefono = document.getElementById("idUpdateNumTelefonoCliente");
    const correo = document.getElementById("idUpdateCorreoCliente");
    const persona_id = document.getElementById("idPersona");
    
    nombreCliente.addEventListener("input", function() {
        const query = nombreCliente.value.trim();
        
        if (query.length > 0) {
            $.ajax({
                method: "POST",
                url: "logica/clssFiltro.php",
                data: {
                    "accion": "FILTROPERSONA",
                    "data": query
                }
            }).done(function(response) {
                try {
                    const resultados = JSON.parse(response);
                    sugerencias.innerHTML = "";

                    if (resultados.length > 0) {
                        resultados.forEach(persona => {
                            const item = document.createElement("div");
                            item.classList.add("list-group-item", "list-group-item-action");
                            item.style.cursor = "pointer";
                            item.textContent = persona.persona_concatenada;

                            // Mejorar el evento click
                            item.addEventListener("click", function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                
                                // Establecer valores
                                nombreCliente.value = persona.persona_concatenada;
                                persona_id.textContent = persona.id;
                                numero_telefono.value = persona.telefonomovil || '';
                                correo.value = persona.email || '';

                                // Limpiar sugerencias
                                sugerencias.innerHTML = "";
                                
                                console.log("Cliente seleccionado:", persona);
                            });

                            sugerencias.appendChild(item);
                        });
                    } else {
                        const noResults = document.createElement("div");
                        noResults.classList.add("list-group-item", "text-muted");
                        noResults.textContent = "Sin resultados";
                        sugerencias.appendChild(noResults);
                    }
                } catch (e) {
                    console.error("Error al procesar los resultados:", e);
                    sugerencias.innerHTML = "";
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error en la solicitud AJAX:", textStatus, errorThrown);
                sugerencias.innerHTML = "";
            });
        } else {
            sugerencias.innerHTML = "";
        }
    });

    // Cerrar sugerencias al hacer clic fuera
    document.addEventListener("click", function(e) {
        if (!nombreCliente.contains(e.target) && !sugerencias.contains(e.target)) {
            sugerencias.innerHTML = "";
        }
    });
});
</script>



<script>
    var selectComprobante = "";
    document.getElementById("btnRealizarPago").addEventListener("click", function() {
        // Mostrar el modal manualmente

        const modal = new bootstrap.Modal(document.getElementById("modalRealizarPago"));
        const radiosFormaPago = document.querySelectorAll('input[name="icon-input"]');


        radiosFormaPago.forEach(radio => {
            radio.addEventListener('change', function() {
                if (radio.checked) {
                    selectComprobante = radio.value;
                    if (radio.value === "factura") {
                        const collapseOne = document.getElementById("collapseOne");

                        console.log("factura")
                        collapseOne.classList.add("show"); // Mostrar acordeón
                    } else {
                        collapseOne.classList.remove("show"); // Ocultar acordeón
                    }

                }
            });
        });


        modal.show();



        const subtotalGeneral = document.getElementById("id_subtotal_general").textContent;
        document.getElementById("montoTotal").value = subtotalGeneral; // Asignar el monto total
        document.getElementById("idMontoVentaTitulo").textContent = subtotalGeneral;

    });
</script>

<!-- FRANCO -->
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
                    <div  id = "contenedor-medidas" class="selectgroup selectgroup-pills">`;

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
                let detalle_notaFormato = detalle_nota ? " / " + detalle_nota : "";
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

                console.log("Datos de mrd")
                console.log(jsDatos)

                console.log("Medidas: " + textoDimensiones);

                // Si no estamos editando, agregar un nuevo ploteo
                const datos = [{
                    id: '0', // ID del ploteo
                    descripcion: jsDatos["descripcion"],
                    medidas: medidasArray,
                    cantidad: cantidad,
                    monto: '-', // Monto
                    subtotal: monto, // Subtotal
                    articulo: textoDimensiones ? jsDatos["descripcion"] + ' (' + textoDimensiones + ') / ' + detalle_nota : jsDatos["descripcion"] + detalle_notaFormato,
                    idmovimiento: jsDatos["id"],
                    dimension: textoDimensiones
                }];

                fn_datos_a_tabla(datos);
                document.getElementById('input_cantidad_' + jsDatos["descripcion"]).value = 0; // Reset cantidad
                document.getElementById('monto_' + jsDatos["descripcion"]).value = ''; // Reset monto
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


<!-- FRANCO -->
<script>
    function obtenerValoresSeleccionados() {
        let seleccionados = [];
        document.querySelectorAll('#contenedor-medidas .selectgroup-input:checked').forEach((checkbox) => {
            seleccionados.push(checkbox.value);
        });
        return seleccionados.join(", ");
    }

    function fn_datos_a_tabla(datos) {
        var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];

        datos.forEach(elemento => {
            console.log("Elemento de mrd");
            console.log(elemento);
            let nuevaFila = tabla.insertRow();

            // Agregar celdas para los datos de ploteo
            nuevaFila.insertCell(0).textContent = elemento.id; // ID
            nuevaFila.insertCell(1).textContent = '-'; // Cantidad de Ploteos
            nuevaFila.insertCell(2).textContent = '-'; // Monto unitario
            nuevaFila.insertCell(3).textContent = '-'; // Subtotal
            nuevaFila.insertCell(4).textContent = elemento.articulo; // Artículo (Ploteo)
            nuevaFila.insertCell(5).textContent = elemento.cantidad; // Se puede agregar más detalles si se requiere
            nuevaFila.insertCell(6).textContent = elemento.monto; // Otro dato
            nuevaFila.insertCell(7).textContent = elemento.subtotal.toFixed(2); // Subtotal (multiplied)

            let accionCell = nuevaFila.insertCell(8);
            nuevaFila.insertCell(9).textContent = elemento.idmovimiento; // Movimiento ID
            nuevaFila.insertCell(10).textContent = elemento.dimension; // Dimensión

            // 1. Botón de Editar
            let botonEditar = document.createElement("button");
            botonEditar.classList.add("btn", "btn-warning", "btn-round", "ms-2", "text-white", "px-3", "py-2");
            botonEditar.innerHTML = '<i class="fas fa-edit"></i>';
            accionCell.appendChild(botonEditar);

            // 2. Botón de Eliminar
            let botonEliminar = document.createElement("button");
            botonEliminar.classList.add("btn", "btn-danger", "btn-round", "ms-2", "px-3", "py-2");
            botonEliminar.innerHTML = '<i class="fas fa-trash"></i>';
            accionCell.appendChild(botonEliminar);

            botonEditar.addEventListener("click", () => {
                let modalContent = `<!-- Aquí comienza el contenido del modal -->
               <div class="text-center">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Servicio de ${elemento.descripcion}</h4>
                            <div>ID: <span id="id_mov_${elemento.descripcion}">${elemento.idmovimiento}</span></div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Cantidad de <b> ${elemento.descripcion}</b></p>
                            <div class="d-flex align-items-center justify-content-center">
                                <button id="btn_menos_${elemento.descripcion}" class="btn btn-danger btn-round me-2">-</button>
                                <input id="input_cantidad_${elemento.descripcion}" class="text-center" type="text" value="${elemento.cantidad}" style="width: 40px;" oninput="validarNumero(event)">
                                <button id="btn_mas_${elemento.descripcion}" class="btn btn-success btn-round ms-2">+</button>
                            </div>
                        </div>

                        <div class="card-body">
                            <p class="card-text">Dimensión</p>
                            <div class="selectgroup selectgroup-pills">`;

                // Aquí agregamos dinámicamente los checkboxes para cada medida
                elemento.medidas.forEach((x) => {
                    modalContent += `
                <label id="contenedor-medidas-update" class="selectgroup-item">
                    <input type="checkbox" name="value" value="${x}" class="selectgroup-input" />
                    <span class="selectgroup-button">${x}</span>
                </label>`;
                });

                let cadena_despues_slash = elemento["articulo"].match(/\/\s*(.*)/);
                let resultado_cadena_sn_slash = cadena_despues_slash ? cadena_despues_slash[1] : "";
                let parteAntesDelSlash = elemento["articulo"].split("/")[0].trim();

                modalContent += `</div></div> <!-- Fin del contenido de dimensiones -->
                        <div class="card-body">
                            <p class="card-text">Monto (S/)</p>
                            <input type="number" id="monto_editar${elemento["descripcion"]}" class="form-control" value="${elemento.subtotal}">
                        </div>

                        <div class="card-body">
                            <p class="card-text">Detalle</p>
                            <textarea rows="3" cols="30" id="datelle_material_editar${elemento["articulo"]}" class="form-control"> ${resultado_cadena_sn_slash}</textarea>
                        </div>


                        <div class="text-center mb-3">
                            <button class="btn btn-secondary rounded-5" id="btnEditar${elemento["descripcion"]}" role="button">Actualizar</button>
                        </div>
                    </div>
                </div>`;

                document.getElementById('modalContent').innerHTML = modalContent;

                // Manejo de incremento y decremento de cantidad
                document.getElementById(`btn_menos_${elemento.descripcion}`).addEventListener('click', () => {
                    let cantidad = parseInt(document.getElementById(`input_cantidad_${elemento.descripcion}`).value);
                    if (cantidad > 1) document.getElementById(`input_cantidad_${elemento.descripcion}`).value = cantidad - 1;
                });

                document.getElementById(`btn_mas_${elemento.descripcion}`).addEventListener('click', () => {
                    let cantidad = parseInt(document.getElementById(`input_cantidad_${elemento.descripcion}`).value);
                    document.getElementById(`input_cantidad_${elemento.descripcion}`).value = cantidad + 1;
                });
                if (elemento.dimension) {
                    let dimensionesSeleccionadas = elemento.dimension.split(", "); // Convertir string a array si es necesario
                    document.querySelectorAll('.selectgroup-input').forEach((checkbox) => {
                        if (dimensionesSeleccionadas.includes(checkbox.value)) {
                            checkbox.checked = true; // Marcar el checkbox si su valor está en la lista
                        }
                    });
                }

                // Mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('modalGenerico'));
                modal.show();

                // Evento para actualizar el ploteo
                document.getElementById('btnEditar' + elemento["descripcion"]).addEventListener('click', function() {
                    const cantidad = parseInt(document.getElementById(`input_cantidad_${elemento.descripcion}`).value) || 1;
                    const monto = parseFloat(document.getElementById('monto_editar' + elemento["descripcion"]).value) || 0;
                    const articulo_titulo = document.getElementById("datelle_material_editar" + elemento["articulo"]).value;
                    let dimensionesSeleccionadas = [];
                    const detalle_ = document.getElementById("datelle_material_editar" + elemento["articulo"]).value;
                    const detalle_formato_update = detalle_ ? " / " + detalle_ : "";
                    document.querySelectorAll('#contenedor-medidas-update .selectgroup-input:checked').forEach((checkbox) => {
                        dimensionesSeleccionadas.push(checkbox.value);
                    });

                    let textoDimensiones = dimensionesSeleccionadas.join(", ");

                    // Validación del monto
                    if (isNaN(monto) || monto <= 0) {
                        alert("Por favor, ingresa un monto válido mayor a 0.");
                        return;
                    }

                    // Actualizamos los valores de la fila
                    elemento.cantidad = cantidad;
                    elemento.subtotal = monto;
                    elemento.dimension = textoDimensiones;
                    elemento.articulo = textoDimensiones ? elemento.descripcion + `(${textoDimensiones})` + detalle_formato_update : elemento["descripcion"] + detalle_formato_update;


                    // Actualizamos la fila de la tabla
                    nuevaFila.cells[4].textContent = elemento.articulo;
                    nuevaFila.cells[5].textContent = elemento.cantidad;
                    nuevaFila.cells[7].textContent = elemento.subtotal.toFixed(2);
                    nuevaFila.cells[10].textContent = elemento.dimension;

                    // Limpiar los campos
                    document.getElementById(`input_cantidad_${elemento.descripcion}`).value = '';
                    document.getElementById('monto_editar' + elemento.descripcion).value = '';
                    fn_obtener_total(); // Recalcular totales
                    // Cerrar el modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalGenerico'));
                    if (modal) modal.hide();
                });
            });

            // Función de eliminar
            botonEliminar.addEventListener("click", () => {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Si el usuario confirma, eliminamos la fila
                        const fila = botonEliminar.closest("tr");
                        fila.remove(); // Eliminar la fila

                        // Recalcular los totales
                        fn_obtener_total();

                        // Mostrar mensaje de éxito
                        showNotification("success");
                    }
                });
            });
        });

        fn_obtener_total(); // Recalcular totales
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
        btnEliminar.classList.add('btn', 'btn-danger', 'btn-sm', 'ms-2'); // Clase btn-sm para hacerlo más pequeño
        btnEliminar.textContent = '-'; // Texto del botón
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

<!-- FRANCO -->
<script>
    function fn_pagar_directo() {
        try {
            const radiosFormaPago = document.querySelectorAll('input[name="icon-input"]');
            //

            let radioSeleccionado;
            radiosFormaPago.forEach(radio => {
                if (radio.checked) {
                    radioSeleccionado = radio.value;
                }
            });
            const collapseOne = document.getElementById("collapseOne");




            var datosSerializados = $('#form-pago-directo').serializeArray();

            console.log(datosSerializados); // Ver los datos serializados como un array de objetos

            //////////////////////////////////////////////////////
            var numTelefonoUpdate = document.getElementById('idUpdateNumTelefonoCliente').value;
            //////////////////////////////////////////////////////////////////////////
            var idVenta = 0

            var idPersona = document.getElementById('idPersona').textContent.trim() === "#" ? "9897" : document.getElementById('idPersona').textContent.trim();
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
            var js_articulos = []
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
                        "id_forma_pago": formaPago,
                        "monto_forma_pago": monto
                    });
                    formaPago = null;
                    monto = null;
                }
            };

            js_articulos = obtener_json_articulos();

            var js_venta = {
                "tipo_comprobante": radioSeleccionado,
                "usuario_id": parseInt(idUsuario),
                "cliente_id": parseInt(idPersona),
                "atencion_final_usuario": idAtencionFinal,
                "numerotelefono_cliente_venta": numUpdateTelefonoPersona,
                "monto_original": montoOriginal,
                "monto_venta_final": montoFinal,

            };



            var js_for_pago = {
                "monto_original": montoOriginal,
                "monto_venta_final": montoFinal,
                "comentario": ""
            };

            //monto_forma_pago

            if (selectComprobante === "factura" && (document.getElementById('idPersona').textContent.trim() === "#")) {

                swal("Ups!, Para la Factura necesitas el RUC del Cliente :)", {
                    icon: "error",
                    buttons: {
                        confirm: {
                            className: "btn btn-danger",
                        },
                    },
                });


            } else if (js_detalle_pago.length === 0) {
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
                console.log("js_articulo", js_articulos);

                console.log("js_detalle_pago final: ", js_detalle_pago);
                console.log("js_venta final: ", js_venta);

                $.ajax({
                    url: 'logica/clssInsertPA.php',
                    type: 'POST',
                    data: {
                        accion: 'FINALIZARVENTARAPIDO',
                        jsDatosVenta: JSON.stringify(js_venta),
                        js_articulos: JSON.stringify(js_articulos),
                        js_detalle_pago: JSON.stringify(js_detalle_pago),
                    },
                    success: function(response) {

                        console.log("Respuesta del servidor: ", response);

                        try {
                            var result = JSON.parse(response);
                            if (result.estado === true) {
                                console.log(result)
                                console.log(result.id_venta_generado)

                                Swal.fire({
                                    title: "Pagado con Éxito!",
                                    html: `<p style="text-align: center;"> Venta Realizada con Exitó</p>`, // Usa "html" en lugar de "text"
                                    icon: "success",
                                    buttons: false,
                                    timer: 1500
                                }).then(() => {
                                    window.open("/libreria-bazar-rodri/ticket.php?id=" + parseInt(result.id_venta_generado), "_blank");
                                    //window.open("ticket.php?id=" + parseInt(result.id_venta_generado), "_blank");
                                    location.reload();

                                });
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
        } catch (error) {
            console.error("Error en el proceso de pago:", error);
        }
    }

    function fn_pagar_credito() {


        //////////////////////////////////////////////////////
        if (document.getElementById('idPersona').textContent.trim() === "#") return Swal.fire("Error", "Debe ingresar un cliente", "warning");

        var numTelefonoUpdate = document.getElementById('idUpdateNumTelefonoCliente').value;
        //////////////////////////////////////////////////////////////////////////
        var idVenta = document.getElementById('idVenta').textContent;
        var idPersona = document.getElementById('idPersona').textContent.trim();
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
        js_articulos = obtener_json_articulos();

        var js_venta = {
            "usuario_id": parseInt(idUsuario),
            "cliente_id": parseInt(idPersona),
            "atencion_final_usuario": idAtencionFinal,
            "numerotelefono_cliente_venta": numUpdateTelefonoPersona,
            "monto_original": montoOriginal,
            "monto_venta_final": montoFinal,
            "monto_inicial": acumMontos
        };

        console.log(js_venta);
        console.log(js_detalle_deuda);
        $.ajax({
            url: 'logica/clssInsertPA.php',
            type: 'POST',
            data: {
                accion: 'FINALIZARVENTACREDITORAPIDO',
                jsDatosVenta: JSON.stringify(js_venta),
                js_articulos: JSON.stringify(js_articulos),
                js_detalle_deuda: JSON.stringify(js_detalle_deuda)

            },
            success: function(response) {

                console.log("Respuesta del servidor: ", response);

                try {
                    var result = JSON.parse(response);
                    if (result.estado === true) {
                        Swal.fire({
                            title: "Venta Realizada el Credito con Éxito!",
                            html: `<div style="text-align: center;">Venta Realizada</div>`, // Se usa "html" en lugar de "text"
                            icon: "success",
                            buttons: false,
                            timer: 1500
                        }).then(() => {
                            //window.open("http://localhost/caracol_soft_vysam/ticket.php?id=" + parseInt(result.id_venta_generado), "_blank");
                            window.open("ticket.php?id=" + parseInt(result.id_venta_generado), "_blank");
                            //laocation.reload();
                        });
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

<script>
    function obtener_json_articulos() {
        var idCliente = document.getElementById('idPersona').textContent.trim();
        var total = document.getElementById("montoTotal").value;

        const userId = <?php echo $_SESSION['id']; ?>;
        console.log(idCliente);
        console.log(userId);

        const datos = {
            "usuario_id": userId, // Puedes cambiar este valor dinámicamente si es necesario
            "cliente_id": idCliente, // También este valor puede ser dinámico
            "total": total,
            "articulos": []
        };

        // Obtener todas las filas de la tabla (excepto el encabezado)
        const rows = document.querySelectorAll("#tabla_articulos tbody tr");

        // Recorrer todas las filas y obtener los datos de cada columna
        rows.forEach(function(row) {
            const articulo = {
                "articulo_id": row.cells[0].textContent.trim() === '0' || row.cells[0].textContent.trim() === '' ? null : parseInt(row.cells[0].textContent.trim()) || null,
                "minutos": isNaN(parseFloat(row.cells[1].textContent)) ? null : parseFloat(row.cells[1].textContent),
                "costoxminuto": isNaN(parseFloat(row.cells[2].textContent)) ? null : parseFloat(row.cells[2].textContent),
                "precio_unitario": isNaN(parseFloat(row.cells[6].textContent)) ? null : parseFloat(row.cells[6].textContent),
                "cantidad": isNaN(parseInt(row.cells[5].textContent)) ? null : parseInt(row.cells[5].textContent),
                "sub_total": isNaN(parseFloat(row.cells[7].textContent)) ? null : parseFloat(row.cells[7].textContent),
                "movimiento_id": isNaN(parseInt(row.cells[9].textContent)) ? null : parseInt(row.cells[9].textContent),
                "nota_archivo": row.cells[10] ? row.cells[4].textContent + " / " + row.cells[10].textContent.trim() || "Sin nota" : "Sin nota"
            };


            // Agregar el artículo al array
            datos.articulos.push(articulo);
        });

        // Mostrar los datos en la consola para verificar
        console.log(JSON.stringify(datos));

        return datos.articulos;

    }
</script>
<!-- SOLO LA PARTE DEL SCRIPT QUE NECESITAS REEMPLAZAR -->
<!-- Busca este script en tu archivo original (línea ~1087) y reemplázalo con este: -->

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // Referencias a los campos del formulario PERSONA
    const numeroDocumentoPersona = document.getElementById('numeroDocumentoPersona');
    const nombresPersona = document.getElementById('nombresPersona');
    const apellidosPersona = document.getElementById('apellidosPersona');
    
    // Referencias a los campos del formulario EMPRESA
    const numeroDocumentoEmpresa = document.getElementById('numeroDocumentoEmpresa');
    const nombreComercial = document.getElementById('nombreComercial');
    const razonSocial = document.getElementById('razonSocial');
    
    // Agregar spinner de carga
    function mostrarCargando(inputElement, mostrar = true) {
        const spinner = inputElement.parentElement.querySelector('.spinner-api');
        if (mostrar) {
            if (!spinner) {
                const spinnerHtml = '<span class="spinner-api spinner-border spinner-border-sm ms-2" role="status"></span>';
                inputElement.insertAdjacentHTML('afterend', spinnerHtml);
            }
        } else {
            if (spinner) spinner.remove();
        }
    }
    
    // Función para consultar la API de GraphPeru
    async function consultarAPI(documento) {
        const url = `https://graphperu.daustinn.com/api/query/${documento}`;
        
        try {
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error('Error en la consulta');
            }
            
            const data = await response.json();
            
            console.log('Respuesta de la API:', data); // Para debug
            
            // Verificar si hay datos válidos
            if (data && (data.names || data.fullName || data.name)) {
                return data;
            } else {
                return null;
            }
            
        } catch (error) {
            console.error('Error al consultar API:', error);
            return null;
        }
    }
    
    // Función para autocompletar DNI
    async function autocompletarDNI() {
        const dni = numeroDocumentoPersona.value.trim();
        
        // Validar que sea un DNI válido (8 dígitos)
        if (dni.length !== 8 || !/^\d{8}$/.test(dni)) {
            return;
        }
        
        // Mostrar loading
        mostrarCargando(numeroDocumentoPersona, true);
        
        const datos = await consultarAPI(dni);
        
        // Ocultar loading
        mostrarCargando(numeroDocumentoPersona, false);
        
        if (datos && datos.names && datos.surnames) {
            // Autocompletar nombres y apellidos
            nombresPersona.value = datos.names;
            apellidosPersona.value = datos.surnames;
            
            // Agregar animación de autocompletado
            nombresPersona.classList.add('input-autocompleted');
            apellidosPersona.classList.add('input-autocompleted');
            
            // Quitar animación después de 500ms
            setTimeout(() => {
                nombresPersona.classList.remove('input-autocompleted');
                apellidosPersona.classList.remove('input-autocompleted');
            }, 500);
            
            // Quitar errores si existían
            numeroDocumentoPersona.classList.remove('is-invalid');
            nombresPersona.classList.remove('is-invalid');
            apellidosPersona.classList.remove('is-invalid');
            
            // Mostrar notificación de éxito
            Swal.fire({
                icon: 'success',
                title: 'DNI encontrado',
                text: `${datos.fullName}`,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            // No se encontraron datos
            Swal.fire({
                icon: 'warning',
                title: 'DNI no encontrado',
                text: 'Por favor, ingrese los datos manualmente',
                timer: 2500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    }
    
    // Función para autocompletar RUC - CORREGIDA
    async function autocompletarRUC() {
        const ruc = numeroDocumentoEmpresa.value.trim();
        
        // Validar que sea un RUC válido (11 dígitos)
        if (ruc.length !== 11 || !/^\d{11}$/.test(ruc)) {
            return;
        }
        
        // Mostrar loading
        mostrarCargando(numeroDocumentoEmpresa, true);
        
        const datos = await consultarAPI(ruc);
        
        // Ocultar loading
        mostrarCargando(numeroDocumentoEmpresa, false);
        
        console.log('Datos recibidos para RUC:', datos); // Para debug
        
        if (datos) {
            // La API devuelve diferentes estructuras según el tipo de documento
            // Para RUC (11 dígitos), los datos vienen en formato:
            // { name: "...", address: "...", state: "...", condition: "..." }
            
            let razonSocialData = null;
            let nombreComercialData = null;
            
            // Intentar obtener el nombre de diferentes campos posibles
            if (datos.name) {
                // Formato de RUC: usa 'name' directamente
                razonSocialData = datos.name;
                nombreComercialData = datos.name;
            } else if (datos.fullName) {
                // Formato alternativo
                razonSocialData = datos.fullName;
                nombreComercialData = datos.fullName;
            } else if (datos.names) {
                // Otro formato posible
                razonSocialData = datos.names;
                nombreComercialData = datos.names;
            }
            
            if (razonSocialData) {
                // Autocompletar razón social y nombre comercial
                razonSocial.value = razonSocialData;
                nombreComercial.value = nombreComercialData;
                
                // Agregar animación de autocompletado
                razonSocial.classList.add('input-autocompleted');
                nombreComercial.classList.add('input-autocompleted');
                
                // Quitar animación después de 500ms
                setTimeout(() => {
                    razonSocial.classList.remove('input-autocompleted');
                    nombreComercial.classList.remove('input-autocompleted');
                }, 500);
                
                // Quitar errores si existían
                numeroDocumentoEmpresa.classList.remove('is-invalid');
                nombreComercial.classList.remove('is-invalid');
                razonSocial.classList.remove('is-invalid');
                
                // Mostrar notificación de éxito con información adicional
                let mensajeDetalle = razonSocialData;
                if (datos.state) {
                    mensajeDetalle += ` - ${datos.state}`;
                }
                if (datos.condition) {
                    mensajeDetalle += ` (${datos.condition})`;
                }
                
                Swal.fire({
                    icon: 'success',
                    title: 'RUC encontrado',
                    text: mensajeDetalle,
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            } else {
                // No se encontraron datos válidos
                console.warn('No se encontró el campo "name" en la respuesta:', datos);
                Swal.fire({
                    icon: 'warning',
                    title: 'RUC no encontrado',
                    html: 'Por favor, ingrese los datos manualmente<br><small>El RUC puede no estar registrado en SUNAT</small>',
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        } else {
            // Error en la consulta
            Swal.fire({
                icon: 'error',
                title: 'Error en la búsqueda',
                html: 'No se pudo consultar el RUC<br><small>Por favor, intente nuevamente</small>',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    }
    
    // Evento blur para DNI (se dispara al perder el foco)
    if (numeroDocumentoPersona) {
        numeroDocumentoPersona.addEventListener('blur', autocompletarDNI);
    }
    
    // Evento blur para RUC (se dispara al perder el foco)
    if (numeroDocumentoEmpresa) {
        numeroDocumentoEmpresa.addEventListener('blur', autocompletarRUC);
    }
    
    // Eventos para los botones de búsqueda
    const btnBuscarDNI = document.getElementById('btnBuscarDNI');
    if (btnBuscarDNI) {
        btnBuscarDNI.addEventListener('click', function() {
            const dni = numeroDocumentoPersona.value.trim();
            
            if (dni.length !== 8 || !/^\d{8}$/.test(dni)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'DNI inválido',
                    text: 'Por favor, ingrese un DNI de 8 dígitos',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
                return;
            }
            
            // Mostrar spinner en el botón
            const iconoOriginal = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Buscando...';
            this.disabled = true;
            
            // Ejecutar búsqueda
            autocompletarDNI().finally(() => {
                setTimeout(() => {
                    this.innerHTML = iconoOriginal;
                    this.disabled = false;
                }, 1000);
            });
        });
    }
    
    const btnBuscarRUC = document.getElementById('btnBuscarRUC');
    if (btnBuscarRUC) {
        btnBuscarRUC.addEventListener('click', function() {
            const ruc = numeroDocumentoEmpresa.value.trim();
            
            if (ruc.length !== 11 || !/^\d{11}$/.test(ruc)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'RUC inválido',
                    text: 'Por favor, ingrese un RUC de 11 dígitos',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
                return;
            }
            
            // Mostrar spinner en el botón
            const iconoOriginal = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Buscando...';
            this.disabled = true;
            
            // Ejecutar búsqueda
            autocompletarRUC().finally(() => {
                setTimeout(() => {
                    this.innerHTML = iconoOriginal;
                    this.disabled = false;
                }, 1000);
            });
        });
    }
});
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // Lógica para borrar los datos cuando se cambia entre los Pills
        const personaTab = document.getElementById("pills-persona-tab");
        const empresaTab = document.getElementById("pills-empresa-tab");

        const btnAbrirModalCliente = document.getElementById("btnAbrirModalCliente");
        const modalClienteEl = document.getElementById("modalCliente");
        const modalCliente = modalClienteEl ? new bootstrap.Modal(modalClienteEl) : null;

        // Agrega un evento click para abrir el modal manualmente (si existe el botón)
        if (btnAbrirModalCliente && modalCliente) {
            btnAbrirModalCliente.addEventListener("click", function() {
                console.log("click")
                modalCliente.show(); // Muestra el modal manualmente
            });
        }

        if (personaTab) {
            personaTab.addEventListener('click', () => {
                // Limpiar datos de la pestaña Empresa
                document.getElementById('numeroDocumentoEmpresa').value = '';
                document.getElementById('nombreComercial').value = '';
                document.getElementById('razonSocial').value = '';
                document.getElementById('telefonoEmpresa').value = '';
                document.getElementById('emailEmpresa').value = '';
                resetErrors();
            });
        }

        if (empresaTab) {
            empresaTab.addEventListener('click', () => {
                // Limpiar datos de la pestaña Persona
                document.getElementById('numeroDocumentoPersona').value = '';
                document.getElementById('nombresPersona').value = '';
                document.getElementById('apellidosPersona').value = '';
                document.getElementById('telefonoPersona').value = '';
                document.getElementById('emailPersona').value = '';
                resetErrors();
            });
        }

        function resetErrors() {
            const inputs = document.querySelectorAll('.form-control');
            const errorMessages = document.querySelectorAll('.invalid-feedback');

            inputs.forEach(input => {
                input.classList.remove('is-invalid');
            });

            errorMessages.forEach(message => {
                message.textContent = '';
            });
        }
        function limpiarcampos() {
            document.getElementById('numeroDocumentoEmpresa').value = '';
            document.getElementById('nombreComercial').value = '';
            document.getElementById('razonSocial').value = '';
            document.getElementById('telefonoEmpresa').value = '';
            document.getElementById('emailEmpresa').value = '';
            document.getElementById('numeroDocumentoPersona').value = '';
            document.getElementById('nombresPersona').value = '';
            document.getElementById('apellidosPersona').value = '';
            document.getElementById('telefonoPersona').value = '';
            document.getElementById('emailPersona').value = '';
        }

        // Seleccionando los elementos de los formularios
        const formPersona = document.getElementById('pills-persona');
        const formEmpresa = document.getElementById('pills-empresa');

        const btnRegistrarCliente = document.getElementById('btnRegistrarCliente');

        // Función para validar los campos
        function validarCamposPersona() {
            let valid = true;

            // Validar el número de documento (solo si tiene datos)
            const numeroDocumentoPersona = document.getElementById('numeroDocumentoPersona');
            const errorNumeroDocumentoPersona = document.getElementById('error-numeroDocumentoPersona');
            if (numeroDocumentoPersona.value.trim() === '') {
                valid = false;
                numeroDocumentoPersona.classList.add('is-invalid');
                errorNumeroDocumentoPersona.textContent = 'El DNI es obligatorio.';
            } else if (!/^\d{8}$/.test(numeroDocumentoPersona.value)) {
                valid = false;
                numeroDocumentoPersona.classList.add('is-invalid');
                errorNumeroDocumentoPersona.textContent = 'Debe ser un DNI válido (8 dígitos).';
            } else {
                numeroDocumentoPersona.classList.remove('is-invalid');
                errorNumeroDocumentoPersona.textContent = '';
            }

            // Validar los nombres (solo si tiene datos y sin números)
            const nombresPersona = document.getElementById('nombresPersona');
            const errorNombresPersona = document.getElementById('error-nombresPersona');
            if (nombresPersona.value.trim() == '') {
                valid = false;
                nombresPersona.classList.add('is-invalid');
                errorNombresPersona.textContent = 'Los nombres es obligatorio.';
            } else if (/[^a-zA-Z\s]/.test(nombresPersona.value)) {
                valid = false;
                nombresPersona.classList.add('is-invalid');
                errorNombresPersona.textContent = 'Los nombres no pueden contener números.';
            } else {
                nombresPersona.classList.remove('is-invalid');
                errorNombresPersona.textContent = '';
            }

            // Validar los apellidos (solo si tiene datos y sin números)
            const apellidosPersona = document.getElementById('apellidosPersona');
            const errorApellidosPersona = document.getElementById('error-apellidosPersona');
            if (apellidosPersona.value.trim() == '') {
                valid = false;
                apellidosPersona.classList.add('is-invalid');
                errorApellidosPersona.textContent = 'Los apellidos es obligatorio.';
            } else if (/[^a-zA-Z\s]/.test(apellidosPersona.value)) {
                valid = false;
                apellidosPersona.classList.add('is-invalid');
                errorApellidosPersona.textContent = 'Los apellidos no pueden contener números.';
            } else {
                apellidosPersona.classList.remove('is-invalid');
                errorApellidosPersona.textContent = '';
            }

            // Validar el teléfono (solo si tiene datos y es un número válido)
            const telefonoPersona = document.getElementById('telefonoPersona');
            const errorTelefonoPersona = document.getElementById('error-telefonoPersona');
            if (telefonoPersona.value.trim() !== '' && !/^\d{9}$/.test(telefonoPersona.value)) {
                valid = false;
                telefonoPersona.classList.add('is-invalid');
                errorTelefonoPersona.textContent = 'El teléfono debe tener 9 dígitos.';
            } else {
                telefonoPersona.classList.remove('is-invalid');
                errorTelefonoPersona.textContent = '';
            }

            // Validar el email (solo si tiene datos y es un correo válido)
            const emailPersona = document.getElementById('emailPersona');
            const errorEmailPersona = document.getElementById('error-emailPersona');
            if (emailPersona.value.trim() !== '' && !/\S+@\S+\.\S+/.test(emailPersona.value)) {
                valid = false;
                emailPersona.classList.add('is-invalid');
                errorEmailPersona.textContent = 'Debe ser un correo electrónico válido.';
            } else {
                emailPersona.classList.remove('is-invalid');
                errorEmailPersona.textContent = '';
            }

            return valid;
        }


        function validarCamposEmpresa() {
            let valid = true;

            // Validar RUC (solo si tiene datos)
            const numeroDocumentoEmpresa = document.getElementById('numeroDocumentoEmpresa');
            const errorNumeroDocumentoEmpresa = document.getElementById('error-numeroDocumentoEmpresa');
            if (numeroDocumentoEmpresa.value.trim() === '') {
                valid = false;
                numeroDocumentoEmpresa.classList.add('is-invalid');
                errorNumeroDocumentoEmpresa.textContent = 'El RUC es obligatorio.';
            } else if (!/^\d{11}$/.test(numeroDocumentoEmpresa.value)) {
                valid = false;
                numeroDocumentoEmpresa.classList.add('is-invalid');
                errorNumeroDocumentoEmpresa.textContent = 'Debe ser un RUC válido (11 dígitos).';
            } else {
                numeroDocumentoEmpresa.classList.remove('is-invalid');
                errorNumeroDocumentoEmpresa.textContent = '';
            }

            // Validar nombre comercial (solo si tiene datos)
            const nombreComercial = document.getElementById('nombreComercial');
            const errorNombreComercial = document.getElementById('error-nombreComercial');
            if (nombreComercial.value.trim() == '') {
                valid = false;
                nombreComercial.classList.add('is-invalid');
                errorNombreComercial.textContent = 'Este campo es obligatorio.';
            } else {
                nombreComercial.classList.remove('is-invalid');
                errorNombreComercial.textContent = '';
            }

            // Validar razón social (solo si tiene datos)
            const razonSocial = document.getElementById('razonSocial');
            const errorRazonSocial = document.getElementById('error-razonSocial');
            if (razonSocial.value.trim() == '') {
                valid = false;
                razonSocial.classList.add('is-invalid');
                errorRazonSocial.textContent = 'Este campo es obligatorio.';
            } else {
                razonSocial.classList.remove('is-invalid');
                errorRazonSocial.textContent = '';
            }

            // Validar teléfono (solo si tiene datos)
            const telefonoEmpresa = document.getElementById('telefonoEmpresa');
            const errorTelefonoEmpresa = document.getElementById('error-telefonoEmpresa');
            if (telefonoEmpresa.value.trim() !== '' && !/^\d{9}$/.test(telefonoEmpresa.value)) {
                valid = false;
                telefonoEmpresa.classList.add('is-invalid');
                errorTelefonoEmpresa.textContent = 'El teléfono debe tener 9 dígitos.';
            } else {
                telefonoEmpresa.classList.remove('is-invalid');
                errorTelefonoEmpresa.textContent = '';
            }

            // Validar email (solo si tiene datos)
            const emailEmpresa = document.getElementById('emailEmpresa');
            const errorEmailEmpresa = document.getElementById('error-emailEmpresa');
            if (emailEmpresa.value.trim() !== '' && !/\S+@\S+\.\S+/.test(emailEmpresa.value)) {
                valid = false;
                emailEmpresa.classList.add('is-invalid');
                errorEmailEmpresa.textContent = 'Debe ser un correo electrónico válido.';
            } else {
                emailEmpresa.classList.remove('is-invalid');
                errorEmailEmpresa.textContent = '';
            }

            return valid;
        }



        // Registrar cliente
        btnRegistrarCliente.addEventListener('click', async function() {
            let datos = {};

            if (document.getElementById('pills-persona-tab').classList.contains('active')) {
                // Recolectar los datos del formulario Persona
                if (validarCamposPersona()) {
                    datos = {
                        "numero_documento": document.getElementById('numeroDocumentoPersona').value,
                        "nombres": document.getElementById('nombresPersona').value,
                        "apellidos": document.getElementById('apellidosPersona').value,
                        "telefono_movil": document.getElementById('telefonoPersona').value || null,
                        "email": document.getElementById('emailPersona').value
                    };

                    // Llamar a la función AJAX para registrar la persona
                    try {
                        console.log(datos);
                        const response = await fnRegistrarPersona(datos);
                        console.log("Persona insertado con éxito:", response);
                        const nombreencadenado = `${document.getElementById('numeroDocumentoPersona').value} - ${document.getElementById('nombresPersona').value} ${document.getElementById('apellidosPersona').value}`;
                        console.log(nombreencadenado);
                        console.log(response.persona_id);



                        enviardatos(response.persona_id, nombreencadenado, document.getElementById('telefonoPersona').value || 'Sin numero', document.getElementById('emailPersona').value || 'Sin Correo');
                        limpiarcampos();
                        showNotification("success");


                        modalCliente.hide();
                    } catch (error) {
                        console.error("Error en el registro:", error.message);
                        swal("Error", error.message || "Ocurrió un error inesperado", {
                            icon: "error",
                            buttons: {
                                confirm: {
                                    className: "btn btn-danger",
                                },
                            },
                        });
                    }


                }
            } else if (document.getElementById('pills-empresa-tab').classList.contains('active')) {
                // Recolectar los datos del formulario Empresa
                if (validarCamposEmpresa()) {
                    datos = {
                        "numero_documento": document.getElementById('numeroDocumentoEmpresa').value,
                        "nombre_comercial": document.getElementById('nombreComercial').value,
                        "razon_social": document.getElementById('razonSocial').value,
                        "telefono_movil": document.getElementById('telefonoEmpresa').value,
                        "email": document.getElementById('emailEmpresa').value
                    };

                    try {
                        console.log(datos);
                        // Llamar a la función AJAX para registrar la empresa
                        const response = await fnRegistrarEmpresa(datos);
                        console.log("Empresa insertado con éxito:", response);
                        const nombreencadenado = `${document.getElementById('numeroDocumentoEmpresa').value} - ${document.getElementById('razonSocial').value}`;
                        console.log(nombreencadenado);
                        console.log(response.empresa_id);

                        enviardatos(response.empresa_id, nombreencadenado);
                        limpiarcampos();
                        showNotification("success");

                        modalCliente.hide();

                    } catch (error) {
                        console.error("Error en el registro:", error.message);

                        swal("Error", error.message || "Ocurrió un error inesperado", {
                            icon: "error",
                            buttons: {
                                confirm: {
                                    className: "btn btn-danger",
                                },
                            },
                        });
                    }

                }
            }
        });

        function enviardatos(id_persona, nombre, numero_celular, correo) {
            document.getElementById('idPersona').textContent = id_persona;
            document.getElementById('nombreCliente').value = nombre;
            document.getElementById('idUpdateNumTelefonoCliente').value = numero_celular || '';
            document.getElementById('idUpdateCorreoCliente').value = correo || '';
        }

        function fnRegistrarPersona(datos) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    method: "POST",
                    url: "logica/clssPersona.php", // El archivo PHP donde se maneja el registro de persona
                    data: {
                        "accion": "REGISTRARPERSONARAPIDO", // Acción que se realiza en el backend
                        "data": JSON.stringify(datos) // Los datos de la persona como JSON
                    }
                }).done(function(response) {
                    console.log(response);
                    const jsonResponse = JSON.parse(response); // Convertir la respuesta a JSON
                    if (jsonResponse.success) {
                        resolve(jsonResponse); // Resolvemos la promesa en caso de éxito
                    } else {
                        reject(new Error(jsonResponse.message || "Error desconocido")); // Si hay error en la respuesta del servidor
                    }
                }).fail(function(error) {
                    console.error("Error:", error.responseText);
                    reject(error); // Rechazamos la promesa si ocurre un error en la solicitud AJAX
                });
            });
        }

        function fnRegistrarEmpresa(datos) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    method: "POST",
                    url: "logica/clssPersona.php", // El archivo PHP donde se maneja el registro de empresa
                    data: {
                        "accion": "REGISTRARPERSONARAPIDO", // Acción que se realiza en el backend
                        "data": JSON.stringify(datos) // Los datos de la empresa como JSON
                    }
                }).done(function(response) {
                    console.log(response);
                    const jsonResponse = JSON.parse(response); // Convertir la respuesta a JSON
                    if (jsonResponse.success) {
                        resolve(jsonResponse); // Resolvemos la promesa en caso de éxito
                    } else {
                        reject(new Error(jsonResponse.message || "Error desconocido")); // Si hay error en la respuesta del servidor
                    }
                }).fail(function(error) {
                    console.error("Error:", error.responseText);
                    reject(error); // Rechazamos la promesa si ocurre un error en la solicitud AJAX
                });
            });
        }



    });
</script>




<?php
include("pie.php");
?>