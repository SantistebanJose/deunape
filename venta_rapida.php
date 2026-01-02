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
                                <ul class="nav d-flex">
                                    <li class="nav-item me-3">
                                        <button class="btn btn-secondary btn-round" id="btnAbrirModalPloteo">Ploteo</button>
                                    </li>
                                    <li class="nav-item me-3">
                                        <button class="btn btn-secondary btn-round" id="btnAbrirModalImprimir">Imprimir</button>
                                    </li>
                                    <li class="nav-item me-3">
                                        <button class="btn btn-secondary btn-round" id="btnAbrirModalEscaneo">Escaneo</button>
                                    </li>
                                    <li class="nav-item me-3">
                                        <button class="btn btn-secondary btn-round" id="btnAbrirModalSolo">Solo Corte</button>
                                    </li>
                                    <li class="nav-item me-3">
                                        <button class="btn btn-secondary btn-round" id="btnAbrirModalSolov2">Solo Corte</button>
                                    </li>

                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="table-filters mb-3">
                                    <div class="row justify-content-center align-items-center g-2">
                                        <div class="col-md-3">
                                            <select id="filterCategoria" class="form-select" style="border-radius: 25px; border: 2px solid #6861ce;">
                                                <option value="">Filtrar por Categoría</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select id="filterTipo" class="form-select" style="border-radius: 25px; border: 2px solid #6861ce;">
                                                <option value="">Filtrar por Tipo</option>

                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select id="filterDimension" class="form-select" style="border-radius: 25px; border: 2px solid #6861ce;">
                                                <option value="">Filtrar por Dimensión</option>

                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <button
                                                name=""
                                                id="clearFilters"
                                                class="btn btn-warning btn-round btn-round btn-md"
                                                href="#"
                                                role="button"><i class="fas fa-broom"></i> Limpiar Filtros</b>
                                        </div>
                                    </div>

                                </div>
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

                                                        <div class="mt-2 text-center">
                                                            <a
                                                                name=""
                                                                id=""
                                                                class="btn btn-secondary btn-round btn-sm"

                                                                onclick='fn_agregar_venta(<?php echo $datosArticuloJSON; ?>)'
                                                                role="button"> <i class="fas fa-plus"></i></a>
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
                    class="card" ">
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
                                                <button id="btnIncremento1SoloCortev2" class="btn btn-outline-primary btn-sm me-1 btn-round" style="font-size: 0.9rem;"onclick="fnAumentaPrecioImpresion(1)">+1</button>
                                                <button id="btnIncremento2SoloCortev2" class="btn btn-outline-primary btn-sm me-1 btn-round" style="font-size: 0.9rem;"onclick="fnAumentaPrecioImpresion(2)">+2</button>
                                                <button id="btnIncremento5SoloCortev2" class="btn btn-outline-primary btn-sm btn-round" style="font-size: 0.9rem;"onclick="fnAumentaPrecioImpresion(5)">+5</button>
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
                                            <h6 class="fw-bold text-center mb-4">Opciones de Corte</h6>
                                            <div class="mb-4">
                                                <div class="text-center" style="flex: 1;">
                                                    <p class="mb-1">Minutos Corte</p>
                                                    <div class="d-flex justify-content-center align-items-center mb-2">
                                                        <button id="btnRestarCorte" class="btn btn-danger btn-round">-</button>
                                                        <input id="cantidadCorte" type="number" class="form-control text-center mx-2 " value="0" style="width: 80px; font-size: 1.2rem;" />
                                                        <button id="btnSumarCorte" class="btn btn-success btn-round">+</button>
                                                    </div>
                                                </div>

                                                <!-- Línea divisoria -->
                                                <hr>

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
                <div class="modal fade" id="modalCliente" tabindex="-1" aria-labelledby="modalClienteLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalClienteLabel">Registrar Cliente</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Pils para seleccionar entre Persona y Empresa -->
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
                                    <!-- Formulario Persona -->
                                    <div class="tab-pane fade show active" id="pills-persona" role="tabpanel" aria-labelledby="pills-persona-tab">
                                        <div class="mb-3">
                                            <label for="numeroDocumentoPersona" class="form-label">Número de Documento <span class="fw-bold text-danger">*</span></label>
                                            <input type="text" class="form-control" id="numeroDocumentoPersona" placeholder="Número de Documento">
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
                                            <input type="text" class="form-control" id="telefonoPersona" placeholder="Teléfono Móvil">
                                            <div class="invalid-feedback" id="error-telefonoPersona"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="emailPersona" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="emailPersona" placeholder="Email">
                                            <div class="invalid-feedback" id="error-emailPersona"></div>
                                        </div>
                                    </div>

                                    <!-- Formulario Empresa -->
                                    <div class="tab-pane fade" id="pills-empresa" role="tabpanel" aria-labelledby="pills-empresa-tab">
                                        <div class="mb-3">
                                            <label for="numeroDocumentoEmpresa" class="form-label">Número de Ruc <span class="fw-bold text-danger">*</span></label>
                                            <input type="text" class="form-control" id="numeroDocumentoEmpresa" placeholder="Número de Documento">
                                            <div class="invalid-feedback" id="error-numeroDocumentoEmpresa"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="nombreComercial" class="form-label">Nombre Comercial <span class="fw-bold text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nombreComercial" placeholder="Nombre Comercial">
                                            <div class="invalid-feedback" id="error-nombreComercial"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="razonSocial" class="form-label">Razón Social <span class="fw-bold text-danger">*</span> </label>
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
                                            <input type="text" class="form-control" id="telefonoEmpresa" placeholder="Teléfono Móvil">
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
                                        <th scope="col">Total I/C</th>
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
                            <h5 id="label_total_cortes" class="card-title">Total Cortes S/:</h5>
                            <span id="id_subtotal_cortes" style="font-size: 1.3rem;" aria-labelledby="label_total_cortes">00.00</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-stats card-round">
                        <div class="card-body text-center">
                            <h5 id="label_total_articulos" class="card-title">Total Artículos S/:</h5>
                            <span id="id_subtotal_articulos" style="font-size: 1.3rem;" aria-labelledby="label_total_articulos">00.00</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-primary card-stats card-round">
                        <div class="card-body text-center">
                            <h5 id="label_total_general" class="card-title">Total S/:</h5>
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



<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
<script src="assets/js/scriptNotify.js"></script>

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
    });
</script>


<script>
    $(document).ready(function() {
        var table = $("#multi-filter-select").DataTable({
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

        // Llenar el filtro de Categoría con valores únicos
        table.column(1).data().unique().sort().each(function(d, j) {
            if (d !== "") {
                $('#filterCategoria').append('<option value="' + d + '">' + d + '</option>');
            }
        });

        // Llenar el filtro de Dimensión con valores únicos
        table.column(3).data().unique().sort().each(function(d, j) {
            if (d !== "") {
                $('#filterDimension').append('<option value="' + d + '">' + d + '</option>');
            }
        });

        // Llenar el filtro de Tipo con valores únicos
        table.column(2).data().unique().sort().each(function(d, j) {
            if (d !== "") {
                $('#filterTipo').append('<option value="' + d + '">' + d + '</option>');
            }
        });

        // Filtrar por Categoría
        $('#filterCategoria').on('change', function() {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            table.column(1).search(val ? "^" + val + "$" : "", true, false).draw();
        });

        // Filtrar por Tipo
        $('#filterTipo').on('change', function() {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            table.column(2).search(val ? "^" + val + "$" : "", true, false).draw();
        });

        // Filtrar por Dimensión
        $('#filterDimension').on('change', function() {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            table.column(3).search(val ? "^" + val + "$" : "", true, false).draw();
        });

        // Limpiar los filtros al hacer clic en el botón
        $('#clearFilters').on('click', function() {
            // Limpiar las selecciones de los filtros
            $('#filterCategoria').val('');
            $('#filterTipo').val('');
            $('#filterDimension').val('');

            $('#multi-filter-select input').val(''); // Limpiar búsqueda global en DataTables
            // Restablecer los filtros de la tabla

            // Restablecer los filtros de la tabla
            table.columns().search('').draw();

        });

    });
</script>

<!-- FRANCO -->




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

        // Abrir el modal y manejar el evento de agregar corte
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
            document.getElementById("cantidad_solocorte").value = 0;
            document.getElementById("precioSoloCorte").value = 1.5;
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
            fn_solo_corte_tabla(datosImpresion3D);
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
            datosArticulo.costo_por_minuto = parseFloat(precioCorte.value, 10) || '-';
            datosArticulo.id_movimiento = 1;

            if (datosArticulo.corte && datosArticulo.cantidad == 1) {
                const inputMonto = document.getElementById('cantidadCorte');
                const divContainer = inputMonto.closest('.d-flex');


                // Validar que el monto haya sido ingresado y sea mayor a 0
                if (isNaN(datosArticulo.minutos) || datosArticulo.minutos <= 0) {
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

            modalCantidad.hide();
            fn_agregar_articulo_tabla(datosArticulo);
            showNotification("success");
        };

        // Mostrar el modal
        modalCantidad.show();
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

        // Función para manejar el botón de editar
        botonEditar.addEventListener("click", () => {
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
                }).done(function(response) {
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
                                item.addEventListener("click", function() {
                                    // Establecer el valor del input con el nombre seleccionado
                                    nombreCliente.value = persona.persona_concatenada;
                                    persona_id.textContent = persona.id
                                    numero_telefono.value = persona.telefonomovil
                                    correo.value = persona.email

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
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.error("Error en la solicitud AJAX:", textStatus, errorThrown);
                    sugerencias.innerHTML = ""; // Limpiar las sugerencias en caso de fallo
                });
            } else {
                // Limpiar las sugerencias si no hay texto
                sugerencias.innerHTML = "";
            }
        });

        // Cerrar las sugerencias si se hace clic fuera del input o sugerencias
        document.addEventListener("click", function(e) {
            if (!nombreCliente.contains(e.target) && !sugerencias.contains(e.target)) {
                sugerencias.innerHTML = "";
            }
        });
    });
</script>



<script>
    document.getElementById("btnRealizarPago").addEventListener("click", function() {


        // Mostrar el modal manualmente
        const modal = new bootstrap.Modal(document.getElementById("modalRealizarPago"));
        modal.show();

        const subtotalGeneral = document.getElementById("id_subtotal_general").textContent;
        document.getElementById("montoTotal").value = subtotalGeneral; // Asignar el monto total
        document.getElementById("idMontoVentaTitulo").textContent = subtotalGeneral;

    });
</script>

<!--Ploteo-->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let ploteoEditando = null; // Variable para guardar el ploteo que se está editando
        document.getElementById('btnAbrirModalPloteo').addEventListener('click', function() {
            document.getElementById('modalContent').innerHTML = `
                <div class="text-center">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Servicio de Ploteo</h4>
                            <div>ID: <span id="id_mov_escaneo">4</span></div>
                            <div class="card-sub">Aquí ingresa lo que mandaron a Ploteo</div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Cantidad de Ploteo</p>
                            <div class="d-flex align-items-center justify-content-center">
                                <button id="btn_menos_ploteo" class="btn btn-danger btn-round me-2">-</button>
                                <input id="input_cantidad_ploteo" class="text-center" type="text" value="1" style="width: 50px;height: 40px;" oninput="validarNumero(event)">
                                <button id="btn_mas_ploteo" class="btn btn-success btn-round ms-2">+</button>
                            </div>
                        </div>

                        <div class="card-body">
                            <p class="card-text">Dimensión</p>
                            <div class="selectgroup selectgroup-pills" >
                                <label class="selectgroup-item">
                                    <input
                                        type="checkbox"
                                        name="value"
                                        value="A0"
                                        class="selectgroup-input"
                                    
                                    />
                                    <span class="selectgroup-button">A0</span>
                                </label>
                                <label class="selectgroup-item">
                                    <input
                                        type="checkbox"
                                        name="value"
                                        value="A1"
                                        class="selectgroup-input"
                                    />
                                    <span class="selectgroup-button">A1</span>
                                    </label>
                                <label class="selectgroup-item">
                                    <input
                                        type="checkbox"
                                        name="value"
                                        value="A2"
                                        class="selectgroup-input"
                                    />
                                    <span class="selectgroup-button">A2</span>
                                </label>
                                <label class="selectgroup-item">
                                    <input
                                        type="checkbox"
                                        name="value"
                                        value="A3"
                                        class="selectgroup-input"
                                    />
                                    <span class="selectgroup-button">A3</span>
                                </label>

                                <label class="selectgroup-item">
                                    <input
                                        type="checkbox"
                                        name="value"
                                        value="A4"
                                        class="selectgroup-input"
                                    />
                                    <span class="selectgroup-button">A4</span>
                                </label>

                                <label class="selectgroup-item">
                                    <input
                                        type="checkbox"
                                        name="value"
                                        value="A5"
                                        class="selectgroup-input"
                                    />
                                    <span class="selectgroup-button">A5</span>
                                </label>

                                <label class="selectgroup-item">
                                    <input
                                        type="checkbox"
                                        name="value"
                                        value="A6"
                                        class="selectgroup-input"
                                    />
                                    <span class="selectgroup-button">A6</span>
                                </label>
                               
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <p class="card-text">Monto (S/)</p>
                            <input type="number" id="monto_ploteo" class="form-control" placeholder="Monto (S/)">
                        </div>

                       
                        <div class="text-center">
                            <button class="btn btn-secondary rounded-5" id="btnAgregarPloteo" role="button">Añadir a la Venta</button>
                        </div>
                        
                        <br>
                    </div>
                </div>
            `;

            // 1. Manejar Incremento y Decremento de Ploteos
            document.getElementById("btn_mas_ploteo").addEventListener("click", function() {
                let cantidad = parseInt(document.getElementById("input_cantidad_ploteo").value);
                document.getElementById("input_cantidad_ploteo").value = cantidad + 1;
            });

            document.getElementById("btn_menos_ploteo").addEventListener("click", function() {
                let cantidad = parseInt(document.getElementById("input_cantidad_ploteo").value);
                if (cantidad > 1) {
                    document.getElementById("input_cantidad_ploteo").value = cantidad - 1;
                }
            });

            // Funcionalidad para Añadir Escaneo a la Tabla
            document.getElementById('btnAgregarPloteo').addEventListener('click', function() {
                const cantidadPloteos = parseInt(document.getElementById('input_cantidad_ploteo').value) || 1;
                const montoPloteo = parseFloat(document.getElementById('monto_ploteo').value) || 0;

                const inputMonto = document.getElementById('monto_ploteo');
                const mensajeErrorExistente = document.querySelector('.error-message');
                if (mensajeErrorExistente) mensajeErrorExistente.remove();
                inputMonto.classList.remove('error-input');
                let textoDimensiones = obtenerValoresSeleccionados();
                // Validar que el monto haya sido ingresado y sea mayor a 0
                if (isNaN(montoPloteo) || montoPloteo <= 0) {
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
                const datosPloteo = [{
                    id: '0', // ID del ploteo
                    cantidad: cantidadPloteos, // Cantidad de ploteos
                    monto: '-', // Monto
                    subtotal: montoPloteo, // Subtotal
                    articulo: textoDimensiones ? 'PLOTEO (' + textoDimensiones + ')' : 'PLOTEO',
                    idmovimiento: 2,
                    dimension: textoDimensiones
                }];

                fn_ploteo_tabla(datosPloteo);
                document.getElementById('input_cantidad_ploteo').value = 0; // Reset cantidad
                document.getElementById('monto_ploteo').value = ''; // Reset monto
                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalGenerico'));
                if (modal) modal.hide();
                showNotification("success");
            });

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('modalGenerico'));
            modal.show();
        });

        function obtenerValoresSeleccionados() {
            let seleccionados = [];
            document.querySelectorAll('.selectgroup-input:checked').forEach((checkbox) => {
                seleccionados.push(checkbox.value);
            });
            return seleccionados.join(", "); // Convierte el array en un string separado por comas
        }





        // 3. Función para Agregar a la Tabla de Ploteos
        function fn_ploteo_tabla(datosPloteo) {
            var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];

            datosPloteo.forEach(ploteo => {
                let nuevaFila = tabla.insertRow();

                // Agregar celdas para los datos de ploteo
                nuevaFila.insertCell(0).textContent = ploteo.id; // ID
                nuevaFila.insertCell(1).textContent = '-'; // Cantidad de Ploteos
                nuevaFila.insertCell(2).textContent = '-'; // Monto unitario
                nuevaFila.insertCell(3).textContent = '-'; // Subtotal
                nuevaFila.insertCell(4).textContent = ploteo.articulo; // Artículo (Ploteo)
                nuevaFila.insertCell(5).textContent = ploteo.cantidad; // Se puede agregar más detalles si se requiere
                nuevaFila.insertCell(6).textContent = ploteo.monto; // Otro dato
                nuevaFila.insertCell(7).textContent = ploteo.subtotal.toFixed(2); // Subtotal (multiplied)

                let accionCell = nuevaFila.insertCell(8);
                nuevaFila.insertCell(9).textContent = ploteo.idmovimiento; // Subtotal (multiplied)
                nuevaFila.insertCell(10).textContent = ploteo.dimension; // Subtotal (multiplied)

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

                    document.getElementById('modalContent').innerHTML = `
                       <div class="text-center">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Servicio de Ploteo</h4>
                                    <div>ID: <span id="id_mov_escaneoEditar">${ploteo.idmovimiento}</span></div>
                                    <div class="card-sub">Aquí ingresa lo que mandaron a Ploteo</div>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">Cantidad de Ploteo</p>
                                    <div class="d-flex align-items-center justify-content-center">
                                    <button id="btn_menos_ploteoEditar" class="btn btn-danger btn-round me-2">-</button>
                                    <input id="input_cantidad_ploteoEditar" class="text-center" type="text" value="${ploteo.cantidad}" style="width: 40px;" oninput="validarNumero(event)">
                                    <button id="btn_mas_ploteoEditar" class="btn btn-success btn-round ms-2">+</button>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <p class="card-text">Dimensión</p>
                                    <div class="selectgroup selectgroup-pills" >
                                        <label class="selectgroup-item">
                                            <input
                                                type="checkbox"
                                                name="value"
                                                value="A0"
                                                class="selectgroup-input"
                                            
                                            />
                                            <span class="selectgroup-button">A0</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input
                                                type="checkbox"
                                                name="value"
                                                value="A1"
                                                class="selectgroup-input"
                                            />
                                            <span class="selectgroup-button">A1</span>
                                            </label>
                                        <label class="selectgroup-item">
                                            <input
                                                type="checkbox"
                                                name="value"
                                                value="A2"
                                                class="selectgroup-input"
                                            />
                                            <span class="selectgroup-button">A2</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input
                                                type="checkbox"
                                                name="value"
                                                value="A3"
                                                class="selectgroup-input"
                                            />
                                            <span class="selectgroup-button">A3</span>
                                        </label>

                                        <label class="selectgroup-item">
                                            <input
                                                type="checkbox"
                                                name="value"
                                                value="A4"
                                                class="selectgroup-input"
                                            />
                                            <span class="selectgroup-button">A4</span>
                                        </label>

                                        <label class="selectgroup-item">
                                            <input
                                                type="checkbox"
                                                name="value"
                                                value="A5"
                                                class="selectgroup-input"
                                            />
                                            <span class="selectgroup-button">A5</span>
                                        </label>

                                        <label class="selectgroup-item">
                                            <input
                                                type="checkbox"
                                                name="value"
                                                value="A6"
                                                class="selectgroup-input"
                                            />
                                            <span class="selectgroup-button">A6</span>
                                        </label>
                                    
                                    </div>
                                </div>

                                <div class="card-body">
                                    <p class="card-text">Monto (S/)</p>
                                    <input type="number" id="monto_ploteoeditar" class="form-control" value="${ploteo.subtotal}">
                                </div>
                                <div class="text-center mb-3">
                                    <button class="btn btn-secondary rounded-5" id="btnAgregarploteoEditar" role="button">Actualizar</button>
                                </div>
                            </div>
                       </div>
                    `;

                    document.getElementById('btn_menos_ploteoEditar').addEventListener('click', () => {
                        let cantidad = parseInt(document.getElementById('input_cantidad_ploteoEditar').value);
                        if (cantidad > 1) document.getElementById('input_cantidad_ploteoEditar').value = cantidad - 1;
                    });

                    document.getElementById('btn_mas_ploteoEditar').addEventListener('click', () => {
                        let cantidad = parseInt(document.getElementById('input_cantidad_ploteoEditar').value);
                        document.getElementById('input_cantidad_ploteoEditar').value = cantidad + 1;
                    });

                    // Mostrar el modal
                    const modal = new bootstrap.Modal(document.getElementById('modalGenerico'));
                    modal.show();

                    // Rellenar los campos con los valores actuales del ploteo
                    document.getElementById("input_cantidad_ploteoEditar").value = ploteo.cantidad;
                    document.getElementById("monto_ploteoeditar").value = ploteo.subtotal;

                    if (ploteo.dimension) {
                        let dimensionesSeleccionadas = ploteo.dimension.split(", "); // Convertir string a array si es necesario
                        document.querySelectorAll('.selectgroup-input').forEach((checkbox) => {
                            if (dimensionesSeleccionadas.includes(checkbox.value)) {
                                checkbox.checked = true; // Marcar el checkbox si su valor está en la lista
                            }
                        });
                    }

                    // Guardar la referencia a la fila del ploteo
                    ploteo.fila = nuevaFila; // Guardar referencia a la fila
                    ploteoEditando = ploteo; // Guardar referencia al ploteo que se está editando


                    document.getElementById('btnAgregarploteoEditar').addEventListener('click', function() {
                        const cantidadPloteos = parseInt(document.getElementById('input_cantidad_ploteoEditar').value) || 1;
                        const montoPloteo = parseFloat(document.getElementById('monto_ploteoeditar').value) || 0;

                        let dimensionesSeleccionadas = [];
                        document.querySelectorAll('.selectgroup-input:checked').forEach((checkbox) => {
                            dimensionesSeleccionadas.push(checkbox.value);
                        });

                        // Convertir el array a un string separado por comas
                        let textoDimensiones = dimensionesSeleccionadas.join(", ");

                        const inputMonto = document.getElementById('monto_ploteoeditar');
                        const mensajeErrorExistente = document.querySelector('.error-message');
                        if (mensajeErrorExistente) mensajeErrorExistente.remove();
                        inputMonto.classList.remove('error-input');

                        // Validar que el monto haya sido ingresado y sea mayor a 0
                        if (isNaN(montoPloteo) || montoPloteo <= 0) {
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

                        // Actualizamos los valores de la fila existente
                        ploteoEditando.cantidad = cantidadPloteos;
                        ploteoEditando.subtotal = montoPloteo;
                        ploteoEditando.dimension = textoDimensiones;
                        ploteoEditando.articulo = textoDimensiones ? 'PLOTEO (' + textoDimensiones + ')' : 'PLOTEO',

                            // Actualizamos la fila de la tabla
                            ploteoEditando.fila.cells[4].textContent = ploteoEditando.articulo; // Cantidad
                        ploteoEditando.fila.cells[5].textContent = ploteoEditando.cantidad; // Cantidad
                        ploteoEditando.fila.cells[7].textContent = ploteoEditando.subtotal.toFixed(2); // Subtotal
                        ploteoEditando.fila.cells[10].textContent = ploteoEditando.dimension; // Subtotal

                        // Limpiar los campos
                        document.getElementById('input_cantidad_ploteoEditar').value = 0;
                        document.getElementById('monto_ploteoeditar').value = 0;

                        // Resetear el botón y quitar la referencia al ploteo editado
                        ploteoEditando = null; // Reiniciar la referencia
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalGenerico'));
                        if (modal) modal.hide(); // Cierra el modal si existe
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
    });
</script>

<!--Impresion-->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let impresionEditando = null; // Variable para guardar la impresión que se está editando

        document.getElementById('btnAbrirModalImprimir').addEventListener('click', function() {
            document.getElementById('modalContent').innerHTML = `
                <div class="text-center">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Servicio de Impresión</h4>
                            <div>ID: <span id="id_mov_escaneo">4</span></div>
                            <div class="card-sub">Aquí ingresa lo que mandaron a Imprimir</div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Cantidad a Imprimir</p>
                            <div class="d-flex align-items-center justify-content-center">
                                <button id="btn_menos_impresion" class="btn btn-danger btn-round me-2">-</button>
                                <input id="input_numero_impresion" class="text-center" type="text" value="1" style="width: 40px;" oninput="validarNumero(event)">
                                <button id="btn_mas_impresion" class="btn btn-success btn-round ms-2">+</button>
                            </div>
                        </div>

                         <div class="card-body">
                            <p class="card-text">Dimensión</p>
                            <div class="selectgroup selectgroup-pills" >
                                <label class="selectgroup-item">
                                    <input
                                        type="checkbox"
                                        name="value"
                                        value="A0"
                                        class="selectgroup-input"
                                    
                                    />
                                    <span class="selectgroup-button">A0</span>
                                </label>
                                <label class="selectgroup-item">
                                    <input
                                        type="checkbox"
                                        name="value"
                                        value="A1"
                                        class="selectgroup-input"
                                    />
                                    <span class="selectgroup-button">A1</span>
                                    </label>
                                <label class="selectgroup-item">
                                    <input
                                        type="checkbox"
                                        name="value"
                                        value="A2"
                                        class="selectgroup-input"
                                    />
                                    <span class="selectgroup-button">A2</span>
                                </label>
                                <label class="selectgroup-item">
                                    <input
                                        type="checkbox"
                                        name="value"
                                        value="A3"
                                        class="selectgroup-input"
                                    />
                                    <span class="selectgroup-button">A3</span>
                                </label>

                                <label class="selectgroup-item">
                                    <input
                                        type="checkbox"
                                        name="value"
                                        value="A4"
                                        class="selectgroup-input"
                                    />
                                    <span class="selectgroup-button">A4</span>
                                </label>

                                <label class="selectgroup-item">
                                    <input
                                        type="checkbox"
                                        name="value"
                                        value="A5"
                                        class="selectgroup-input"
                                    />
                                    <span class="selectgroup-button">A5</span>
                                </label>

                                <label class="selectgroup-item">
                                    <input
                                        type="checkbox"
                                        name="value"
                                        value="A6"
                                        class="selectgroup-input"
                                    />
                                    <span class="selectgroup-button">A6</span>
                                </label>
                               
                            </div>
                        </div>

                        <div class="card-body">
                            <p class="card-text">Monto (S/)</p>
                            <input type="number" id="monto_impresion" class="form-control" placeholder="Monto (S/)">
                        </div>
                        <div class="text-center">
                            <button class="btn btn-secondary rounded-5" id="btnAgregarImpresion" role="button">Añadir a la Venta</button>
                        </div>
                        <br>
                    </div>
                </div>
            `;

            // 1. Manejar Incremento y Decremento de Impresiones
            document.getElementById("btn_mas_impresion").addEventListener("click", function() {
                let cantidad = parseInt(document.getElementById("input_numero_impresion").value);
                document.getElementById("input_numero_impresion").value = cantidad + 1;
            });

            document.getElementById("btn_menos_impresion").addEventListener("click", function() {
                let cantidad = parseInt(document.getElementById("input_numero_impresion").value);
                if (cantidad > 1) {
                    document.getElementById("input_numero_impresion").value = cantidad - 1;
                }
            });

            // Funcionalidad para Añadir Escaneo a la Tabla
            document.getElementById('btnAgregarImpresion').addEventListener('click', function() {
                const cantidadImpresiones = parseInt(document.getElementById('input_numero_impresion').value) || 1;
                const montoImpresion = parseFloat(document.getElementById('monto_impresion').value) || 0;

                const inputMonto = document.getElementById('monto_impresion');
                const mensajeErrorExistente = document.querySelector('.error-message');
                if (mensajeErrorExistente) mensajeErrorExistente.remove();
                inputMonto.classList.remove('error-input');
                let textoDimensiones = obtenerValoresSeleccionados();

                // Validar que el monto haya sido ingresado y sea mayor a 0
                if (isNaN(montoImpresion) || montoImpresion <= 0) {
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

                // Si no estamos editando, agregar una nueva impresión
                const datosImpresion = [{
                    id: '0', // ID de la impresión
                    cantidad: cantidadImpresiones, // Cantidad de impresiones
                    monto: '-', // Monto
                    subtotal: montoImpresion, // Subtotal
                    articulo: textoDimensiones ? 'IMPRESIÓN (' + textoDimensiones + ')' : 'IMPRESIÓN',
                    idmovimiento: 3, // ID movimiento para impresión
                    dimension: textoDimensiones
                }];
                fn_impresion_tabla(datosImpresion);
                document.getElementById('input_numero_impresion').value = 1; // Reset cantidad
                document.getElementById('monto_impresion').value = ''; // Reset monto

                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalGenerico'));
                if (modal) modal.hide();
            });

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('modalGenerico'));
            modal.show();
        });

        function obtenerValoresSeleccionados() {
            let seleccionados = [];
            document.querySelectorAll('.selectgroup-input:checked').forEach((checkbox) => {
                seleccionados.push(checkbox.value);
            });
            return seleccionados.join(", "); // Convierte el array en un string separado por comas
        }

        // 3. Función para Agregar a la Tabla de Impresiones
        function fn_impresion_tabla(datosImpresion) {
            var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];

            datosImpresion.forEach(impresion => {
                let nuevaFila = tabla.insertRow();

                // Agregar celdas para los datos de la impresión
                nuevaFila.insertCell(0).textContent = impresion.id; // ID
                nuevaFila.insertCell(1).textContent = '-'; // Cantidad de Impresiones
                nuevaFila.insertCell(2).textContent = '-'; // Monto unitario
                nuevaFila.insertCell(3).textContent = '-'; // Subtotal
                nuevaFila.insertCell(4).textContent = impresion.articulo; // Artículo (Impresión)
                nuevaFila.insertCell(5).textContent = impresion.cantidad; // Cantidad
                nuevaFila.insertCell(6).textContent = impresion.monto; // Monto
                nuevaFila.insertCell(7).textContent = impresion.subtotal.toFixed(2); // Subtotal

                let accionCell = nuevaFila.insertCell(8);
                nuevaFila.insertCell(9).textContent = impresion.idmovimiento; // ID de movimiento
                nuevaFila.insertCell(10).textContent = impresion.dimension; // Subtotal (multiplied)

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
                    document.getElementById('modalContent').innerHTML = `
                        <div class="text-center">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Servicio de Impresión</h4>
                                    <div>ID: <span id="id_mov_escaneoEditar">${impresion.idmovimiento}</span></div>
                                    <div class="card-sub">Aquí ingresa lo que mandaron a Imprimir</div>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">Cantidad a Imprimir</p>
                                    <div class="d-flex align-items-center justify-content-center">
                                    <button id="btn_menos_impresionEditar" class="btn btn-danger btn-round me-2">-</button>
                                    <input id="input_numero_impresionEditar" class="text-center" type="text" value="${impresion.cantidad}" style="width: 40px;" oninput="validarNumero(event)">
                                    <button id="btn_mas_impresionEditar" class="btn btn-success btn-round ms-2">+</button>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <p class="card-text">Dimensión</p>
                                    <div class="selectgroup selectgroup-pills" >
                                        <label class="selectgroup-item">
                                            <input
                                                type="checkbox"
                                                name="value"
                                                value="A0"
                                                class="selectgroup-input"
                                            
                                            />
                                            <span class="selectgroup-button">A0</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input
                                                type="checkbox"
                                                name="value"
                                                value="A1"
                                                class="selectgroup-input"
                                            />
                                            <span class="selectgroup-button">A1</span>
                                            </label>
                                        <label class="selectgroup-item">
                                            <input
                                                type="checkbox"
                                                name="value"
                                                value="A2"
                                                class="selectgroup-input"
                                            />
                                            <span class="selectgroup-button">A2</span>
                                        </label>
                                        <label class="selectgroup-item">
                                            <input
                                                type="checkbox"
                                                name="value"
                                                value="A3"
                                                class="selectgroup-input"
                                            />
                                            <span class="selectgroup-button">A3</span>
                                        </label>

                                        <label class="selectgroup-item">
                                            <input
                                                type="checkbox"
                                                name="value"
                                                value="A4"
                                                class="selectgroup-input"
                                            />
                                            <span class="selectgroup-button">A4</span>
                                        </label>

                                        <label class="selectgroup-item">
                                            <input
                                                type="checkbox"
                                                name="value"
                                                value="A5"
                                                class="selectgroup-input"
                                            />
                                            <span class="selectgroup-button">A5</span>
                                        </label>

                                        <label class="selectgroup-item">
                                            <input
                                                type="checkbox"
                                                name="value"
                                                value="A6"
                                                class="selectgroup-input"
                                            />
                                            <span class="selectgroup-button">A6</span>
                                        </label>
                                    
                                    </div>
                                </div>

                                <div class="card-body">
                                    <p class="card-text">Monto (S/)</p>
                                    <input type="number" id="monto_impresionEditar" class="form-control" value="${impresion.subtotal}">
                                </div>
                                <div class="text-center mb-3">
                                    <button class="btn btn-secondary rounded-5" id="btnAgregarimpresionEditar" role="button">Actualizar</button>
                                </div>
                            </div>
                        </div>
                    `;

                    document.getElementById('btn_menos_impresionEditar').addEventListener('click', () => {
                        let cantidad = parseInt(document.getElementById('input_numero_impresionEditar').value);
                        if (cantidad > 1) document.getElementById('input_numero_impresionEditar').value = cantidad - 1;
                    });

                    document.getElementById('btn_mas_impresionEditar').addEventListener('click', () => {
                        let cantidad = parseInt(document.getElementById('input_numero_impresionEditar').value);
                        document.getElementById('input_numero_impresionEditar').value = cantidad + 1;
                    });

                    // Mostrar el modal
                    const modal = new bootstrap.Modal(document.getElementById('modalGenerico'));
                    modal.show();
                    // Rellenar los campos con los valores actuales de la impresión
                    document.getElementById("input_numero_impresionEditar").value = impresion.cantidad;
                    document.getElementById("monto_impresionEditar").value = impresion.subtotal;

                    if (impresion.dimension) {
                        let dimensionesSeleccionadas = impresion.dimension.split(", "); // Convertir string a array si es necesario
                        document.querySelectorAll('.selectgroup-input').forEach((checkbox) => {
                            if (dimensionesSeleccionadas.includes(checkbox.value)) {
                                checkbox.checked = true; // Marcar el checkbox si su valor está en la lista
                            }
                        });
                    }
                    // Guardar la referencia a la fila de la impresión
                    impresion.fila = nuevaFila; // Guardar referencia a la fila
                    impresionEditando = impresion; // Guardar referencia a la impresión que se está editando

                    document.getElementById('btnAgregarimpresionEditar').addEventListener('click', function() {
                        const cantidadImpresion = parseInt(document.getElementById('input_numero_impresionEditar').value) || 1;
                        const montoImpresion = parseFloat(document.getElementById('monto_impresionEditar').value) || 0;

                        let dimensionesSeleccionadas = [];
                        document.querySelectorAll('.selectgroup-input:checked').forEach((checkbox) => {
                            dimensionesSeleccionadas.push(checkbox.value);
                        });

                        // Convertir el array a un string separado por comas
                        let textoDimensiones = dimensionesSeleccionadas.join(", ");


                        const inputMonto = document.getElementById('monto_impresionEditar');
                        const mensajeErrorExistente = document.querySelector('.error-message');
                        if (mensajeErrorExistente) mensajeErrorExistente.remove();
                        inputMonto.classList.remove('error-input');

                        // Validar que el monto haya sido ingresado y sea mayor a 0
                        if (isNaN(montoImpresion) || montoImpresion <= 0) {
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
                        // Actualizamos los valores de la fila existente
                        impresionEditando.cantidad = cantidadImpresion;
                        impresionEditando.subtotal = montoImpresion;
                        impresionEditando.articulo = textoDimensiones ? 'IMPRESIÓN (' + textoDimensiones + ')' : 'IMPRESIÓN',
                            impresionEditando.dimension = textoDimensiones;

                        // Actualizamos la fila de la tabla
                        impresionEditando.fila.cells[4].textContent = impresionEditando.articulo; // Cantidad
                        impresionEditando.fila.cells[5].textContent = impresionEditando.cantidad; // Cantidad
                        impresionEditando.fila.cells[7].textContent = impresionEditando.subtotal.toFixed(2); // Subtotal
                        impresionEditando.fila.cells[10].textContent = impresionEditando.dimension; // Subtotal

                        // Limpiar los campos
                        document.getElementById('input_numero_impresionEditar').value = 1; // Reset cantidad
                        document.getElementById('monto_impresionEditar').value = 0; // Reset monto

                        impresionEditando = null; // Reiniciar la referencia
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalGenerico'));
                        if (modal) modal.hide(); // Cierra el modal si existe
                        showNotification("success");
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
    });
</script>

<!--Escaneo-->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let escaneoEditando = null; // Variable para guardar el escaneo que se está editando

        // Abrir modal con el contenido específico para Escaneo
        document.getElementById('btnAbrirModalEscaneo').addEventListener('click', function() {
            document.getElementById('modalContent').innerHTML = `
                <div class="text-center">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Servicio de Escaneo</h4>
                            <div>ID: <span id="id_mov_escaneo">4</span></div>
                            <div class="card-sub">Aquí ingresa lo que mandaron a Escanear</div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Cantidad de Escaneo</p>
                            <div class="d-flex align-items-center justify-content-center">
                                <button id="btn_menos_escaneo" class="btn btn-danger btn-round me-2">-</button>
                                <input id="input_numero_escaneo" class="text-center" type="text" value="1" style="width: 40px;" oninput="validarNumero(event)">
                                <button id="btn_mas_escaneo" class="btn btn-success btn-round ms-2">+</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Monto (S/)</p>
                            <input type="number" id="monto_escaneo" class="form-control" placeholder="Monto (S/)">
                        </div>
                        <div class="text-center">
                            <button class="btn btn-secondary rounded-5" id="btnAgregarescaneo" role="button">Añadir a la Venta</button>
                        </div>
                        <br>
                    </div>
                </div>
            `;

            // Funcionalidades de Incremento y Decremento
            document.getElementById("btn_mas_escaneo").addEventListener("click", function() {
                let cantidad = parseInt(document.getElementById("input_numero_escaneo").value);
                document.getElementById("input_numero_escaneo").value = cantidad + 1;
            });

            document.getElementById("btn_menos_escaneo").addEventListener("click", function() {
                let cantidad = parseInt(document.getElementById("input_numero_escaneo").value);
                if (cantidad > 1) {
                    document.getElementById("input_numero_escaneo").value = cantidad - 1;
                }
            });

            // Funcionalidad para Añadir Escaneo a la Tabla
            document.getElementById('btnAgregarescaneo').addEventListener('click', function() {
                const cantidadEscaneos = parseInt(document.getElementById('input_numero_escaneo').value) || 1;
                const montoEscaneo = parseFloat(document.getElementById('monto_escaneo').value) || 0;

                const inputMonto = document.getElementById('monto_escaneo');
                const mensajeErrorExistente = document.querySelector('.error-message');
                if (mensajeErrorExistente) mensajeErrorExistente.remove();
                inputMonto.classList.remove('error-input');

                // Validar que el monto haya sido ingresado y sea mayor a 0
                if (isNaN(montoEscaneo) || montoEscaneo <= 0) {
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

                const datosEscaneo = [{
                    id: '0', // ID del escaneo
                    cantidad: cantidadEscaneos,
                    monto: '-', // Monto unitario
                    subtotal: montoEscaneo,
                    articulo: 'ESCANEO',
                    idmovimiento: 5, // ID movimiento
                }];

                fn_escaneo_tabla(datosEscaneo);

                // Reset campos
                document.getElementById('input_numero_escaneo').value = 1;
                document.getElementById('monto_escaneo').value = '';

                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalGenerico'));
                if (modal) modal.hide();
                showNotification("success");
            });

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('modalGenerico'));
            modal.show();
        });


        // 3. Función para Agregar a la Tabla de Escaneos
        function fn_escaneo_tabla(datosEscaneo) {
            var tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];

            datosEscaneo.forEach(escaneo => {
                let nuevaFila = tabla.insertRow();

                // Agregar celdas para los datos del escaneo
                nuevaFila.insertCell(0).textContent = escaneo.id; // ID
                nuevaFila.insertCell(1).textContent = '-'; // Cantidad de Escaneos
                nuevaFila.insertCell(2).textContent = '-'; // Monto unitario
                nuevaFila.insertCell(3).textContent = '-'; // Subtotal
                nuevaFila.insertCell(4).textContent = escaneo.articulo; // Artículo (Escaneo)
                nuevaFila.insertCell(5).textContent = escaneo.cantidad; // Cantidad
                nuevaFila.insertCell(6).textContent = escaneo.monto; // Monto
                nuevaFila.insertCell(7).textContent = escaneo.subtotal.toFixed(2); // Subtotal

                let accionCell = nuevaFila.insertCell(8);
                nuevaFila.insertCell(9).textContent = escaneo.idmovimiento; // ID de movimiento

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

                    document.getElementById('modalContent').innerHTML = `
                    <div class="text-center">
                                            <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Servicio de Escaneo</h4>
                            <div>ID: <span id="id_mov_escaneoEditar">${escaneo.idmovimiento}</span></div>
                            <div class="card-sub">Aquí ingresa lo que mandaron a Escanear</div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Cantidad de Escaneo</p>
                            <div class="d-flex align-items-center justify-content-center">
                            <button id="btn_menos_escaneoEditar" class="btn btn-danger btn-round me-2">-</button>
                            <input id="input_numero_escaneoEditar" class="text-center" type="text" value="${escaneo.cantidad}" style="width: 40px;" oninput="validarNumero(event)">
                            <button id="btn_mas_escaneoEditar" class="btn btn-success btn-round ms-2">+</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Monto (S/)</p>
                            <input type="number" id="monto_escaneoEditar" class="form-control" value="${escaneo.subtotal}">
                        </div>
                        <div class="text-center mb-3">
                            <button class="btn btn-secondary rounded-5" id="btnAgregarescaneoEditar" role="button">Actualizar</button>
                        </div>
                        </div>
                    </div>
                    `;
                    document.getElementById('btn_menos_escaneoEditar').addEventListener('click', () => {
                        let cantidad = parseInt(document.getElementById('input_numero_escaneoEditar').value);
                        if (cantidad > 1) document.getElementById('input_numero_escaneoEditar').value = cantidad - 1;
                    });

                    document.getElementById('btn_mas_escaneoEditar').addEventListener('click', () => {
                        let cantidad = parseInt(document.getElementById('input_numero_escaneoEditar').value);
                        document.getElementById('input_numero_escaneoEditar').value = cantidad + 1;
                    });

                    // Mostrar el modal
                    const modal = new bootstrap.Modal(document.getElementById('modalGenerico'));
                    modal.show();
                    // Rellenar los campos con los valores actuales del escaneo
                    document.getElementById("input_numero_escaneoEditar").value = escaneo.cantidad;
                    document.getElementById("monto_escaneoEditar").value = escaneo.subtotal;




                    // Guardar la referencia a la fila del escaneo
                    escaneo.fila = nuevaFila; // Guardar referencia a la fila
                    escaneoEditando = escaneo; // Guardar referencia al escaneo que se está editando

                    document.getElementById('btnAgregarescaneoEditar').addEventListener('click', function() {
                        const cantidadEscaneos = parseInt(document.getElementById('input_numero_escaneoEditar').value) || 1;
                        const montoEscaneo = parseFloat(document.getElementById('monto_escaneoEditar').value) || 0;

                        const inputMonto = document.getElementById('monto_escaneoEditar');
                        const mensajeErrorExistente = document.querySelector('.error-message');
                        if (mensajeErrorExistente) mensajeErrorExistente.remove();
                        inputMonto.classList.remove('error-input');

                        // Validar que el monto haya sido ingresado y sea mayor a 0
                        if (isNaN(montoEscaneo) || montoEscaneo <= 0) {
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

                        // Actualizamos los valores de la fila existente
                        escaneoEditando.cantidad = cantidadEscaneos;
                        escaneoEditando.subtotal = montoEscaneo;

                        // Actualizamos la fila de la tabla
                        escaneoEditando.fila.cells[5].textContent = escaneoEditando.cantidad; // Cantidad
                        escaneoEditando.fila.cells[7].textContent = escaneoEditando.subtotal.toFixed(2); // Subtotal

                        // Limpiar los campos
                        document.getElementById('input_numero_escaneoEditar').value = 1; // Reset cantidad
                        document.getElementById('monto_escaneoEditar').value = 0; // Reset monto

                        escaneoEditando = null; // Reiniciar la referencia
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalGenerico'));
                        if (modal) modal.hide(); // Cierra el modal si existe
                        showNotification("success");
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
    });
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
            if (js_detalle_pago.length === 0) {
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
                                    //window.open("/caracol_soft_vysam/ticket.php?id=" + parseInt(result.id_venta_generado), "_blank");
                                    window.open("ticket.php?id=" + parseInt(idVenta), "_blank");
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
                "nota_archivo": row.cells[10] ? row.cells[10].textContent.trim() || "Sin nota" : "Sin nota"
            };

            // Agregar el artículo al array
            datos.articulos.push(articulo);
        });

        // Mostrar los datos en la consola para verificar
        console.log(JSON.stringify(datos));

        return datos.articulos;

    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // Lógica para borrar los datos cuando se cambia entre los Pills
        const personaTab = document.getElementById("pills-persona-tab");
        const empresaTab = document.getElementById("pills-empresa-tab");

        const btnAbrirModalCliente = document.getElementById("btnAbrirModalCliente");
        const modalCliente = new bootstrap.Modal(document.getElementById("modalCliente"));

        // Agrega un evento click para abrir el modal manualmente
        btnAbrirModalCliente.addEventListener("click", function() {
            console.log("click")

            modalCliente.show(); // Muestra el modal manualmente
        });

        personaTab.addEventListener('click', () => {
            // Limpiar datos de la pestaña Empresa
            document.getElementById('numeroDocumentoEmpresa').value = '';
            document.getElementById('nombreComercial').value = '';
            document.getElementById('razonSocial').value = '';
            document.getElementById('telefonoEmpresa').value = '';
            document.getElementById('emailEmpresa').value = '';
            resetErrors();
        });

        empresaTab.addEventListener('click', () => {
            // Limpiar datos de la pestaña Persona
            document.getElementById('numeroDocumentoPersona').value = '';
            document.getElementById('nombresPersona').value = '';
            document.getElementById('apellidosPersona').value = '';
            document.getElementById('telefonoPersona').value = '';
            document.getElementById('emailPersona').value = '';
            resetErrors();
        });

        function resetErrors() {
            // Limpiar las clases 'is-invalid' y los mensajes de error
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

            const condicion = document.getElementById('condicion');
            const errorCondicion = document.getElementById('error-condicion');

            // Verificar si la opción seleccionada es válida
            if (condicion.value === '') {
                valid = false; // La variable valid debe ser parte de tu lógica de validación general
                condicion.classList.add('is-invalid');
                errorCondicion.textContent = 'Debe seleccionar una opción válida.';
            } else {
                condicion.classList.remove('is-invalid');
                errorCondicion.textContent = '';
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
            document.getElementById('idPersona').textContent = id_persona
            document.getElementById('nombreCliente').value = nombre
            document.getElementById('idUpdateNumTelefonoCliente').textContent = numero_celular
            document.getElementById('idUpdateCorreoCliente').value = correo
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