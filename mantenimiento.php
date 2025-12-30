<?php
include("cabecera.php");
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

    .error-input {
        border: 2px solid red;
    }

    .error-message {
        color: red;
        font-size: 0.9em;
        margin-top: 5px;
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
    class="container">


    <div class="page-inner">
        <!-- 
        <a
            name=""
            id=""
            class="btn btn-success btn-round"
            onclick='abriModalRegistroCompra()'
            role="button">Nueva Compra <i class="fas fa-plus"> </i></a>
        <br>
        <br>    
        -->
        <div class="card" id="card-compras">
            <div class="card-body" id="card-body-compras">
                <ul class="nav nav-pills nav-secondary nav-pills-no-bd" id="pills-tab-without-border" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-servicios-tab" data-bs-toggle="pill" href="#pills-servicios" role="tab" aria-controls="pills-servicios" aria-selected="true"><i class="fas fa-cubes"></i> servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-categoria-articulo-tab" data-bs-toggle="pill" href="#pills-categoria-articulo" role="tab" aria-controls="pills-categoria-articulo" aria-selected="false"><i class="fas fa-tag"></i> Categoría de Articulo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-tipo-articulo-tab" data-bs-toggle="pill" href="#pills-tipo-articulo" role="tab" aria-controls="pills-tipo-articulo" aria-selected="false"><i class="fas fa-cogs"></i> Tipo de Articulo</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="pills-escala-articulo-tab" data-bs-toggle="pill" href="#pills-escala-articulo" role="tab" aria-controls="pills-escala-articulo" aria-selected="false"><i class="fas fa-sort-amount-up"></i> Escala de Articulo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="pills-dimension-articulo-tab" data-bs-toggle="pill" href="#pills-dimension-articulo" role="tab" aria-controls="pills-dimension-articulo" aria-selected="false"><i class="fas fa-ruler"></i> Dimensión de Articulo</a>
                    </li>
                </ul>
                <div class="tab-content mt-2 mb-3" id="pills-without-border-tabContent">
                    <div class="tab-pane fade show active" id="pills-servicios" role="tabpanel" aria-labelledby="pills-servicios-tab">
                        <div class="card text-start">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h4 class="card-title"><i class="fas fa-cubes"></i> Servicios del Negocio</h4>

                                    <button class="btn btn-success rounded-5" id="btnAgregarServicio"> <i class="fas fa-plus-circle"> </i> Agregar Servicio </button>
                                </div>
                                <hr>
                                <div
                                    class="row justify-content-center align-items-center md-2">

                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table
                                                id="multi-filter-select"
                                                class="display table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Servicio</th>
                                                        <th>Tamaños </th>
                                                        <th>Accion</th>
                                                    </tr>
                                                </thead>

                                                <tbody>

                                                    <?php
                                                    foreach (listarMovimientos() as $datosTipo) {
                                                        $datos = json_encode($datosTipo);


                                                    ?>
                                                        <tr>
                                                            <td><?php echo $datosTipo["id"] ?></td>
                                                            <td><?php echo $datosTipo["descripcion"] ?></td>
                                                            <td><?php echo $datosTipo["medidas"] ?></td>
                                                            <td>
                                                                <div class="mt-2 text-center">
                                                                    <!-- Botón de Editar (con ícono amarillo) -->
                                                                    <a name="edit" id="edit" class="btn btn-warning btn-round ml-2"
                                                                        onclick='fn_editar_servicio(<?php echo $datos; ?>)' role="button">
                                                                        <i class="fa fa-edit"></i>
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
                    <div class="tab-pane fade" id="pills-categoria-articulo" role="tabpanel" aria-labelledby="pills-categoria-articulo-tab">
                        <div class="card text-start">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h4 class="card-title"><i class="fas fa-tag"></i> Categoria de Articulo</h4>
                                    <button class="btn btn-success rounded-5" id="btnAgregarCategoria"> <i class="fas fa-plus-circle"> </i> Agregar Categoria</button>
                                </div>
                                <hr>
                                <div
                                    class="row justify-content-center align-items-center md-2">

                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table
                                                id="multi-filter-select2"
                                                class="display table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Nombre</th>
                                                        <th>Descripción</th>
                                                        <th>Accion</th>
                                                    </tr>
                                                </thead>

                                                <tbody>

                                                    <?php
                                                    foreach (listarCategoriaArticuloMantenimiento() as $datosCategoria) {
                                                        $datosCategoriaJSON = json_encode($datosCategoria);


                                                    ?>
                                                        <tr>
                                                            <td><?php echo $datosCategoria["id"] ?></td>
                                                            <td><?php echo $datosCategoria["abreviatura"] ?></td>
                                                            <td><?php echo $datosCategoria["descripcion"] ?></td>
                                                            <td>
                                                                <div class="mt-2 text-center">
                                                                    <!-- Botón de Editar (con ícono amarillo) -->
                                                                    <a name="edit" id="edit" class="btn btn-warning btn-round ml-2"
                                                                        onclick='fn_editar_categoria(<?php echo $datosCategoriaJSON; ?>)' role="button">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>

                                                                    <!-- Botón de Activar/Bloquear -->
                                                                    <?php if (is_null($datosCategoria["deleted_at"])) { ?>
                                                                        <!-- Botón para bloquear -->
                                                                        <a name="block" id="block" class="btn btn-dark btn-round ml-2"
                                                                            onclick='fn_bloquear_categoria(<?php echo $datosCategoria["id"]; ?>)' role="button">
                                                                            <i class="fa fa-lock"></i>
                                                                        </a>
                                                                    <?php } else { ?>
                                                                        <!-- Botón para activar -->
                                                                        <a name="activate" id="activate" class="btn btn-secondary btn-round ml-2"
                                                                            onclick='fn_desbloquear_categoria(<?php echo $datosCategoria["id"]; ?>)' role="button">
                                                                            <i class="fa fa-unlock"></i>
                                                                        </a>
                                                                    <?php } ?>
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
                    <div class="tab-pane fade" id="pills-tipo-articulo" role="tabpanel" aria-labelledby="pills-tipo-articulo-tab">
                        <div class="card text-start">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h4 class="card-title"><i class="fas fa-cogs"></i> Tipo de Articulo</h4>
                                    <button class="btn btn-success rounded-5" id="btnAgregarTipo"> <i class="fas fa-plus-circle"> </i> Agregar Tipo </button>
                                </div>
                                <hr>
                                <div
                                    class="row justify-content-center align-items-center md-2">

                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table
                                                id="multi-filter-select3"
                                                class="display table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Nombre</th>
                                                        <th>Descripción</th>
                                                        <th>Accion</th>
                                                    </tr>
                                                </thead>

                                                <tbody>

                                                    <?php
                                                    foreach (listarTipoArticuloMantenimiento($sucursal_id) as $datosTipo) {
                                                        $datosTipoJSON = json_encode($datosTipo);


                                                    ?>
                                                        <tr>
                                                            <td><?php echo $datosTipo["id"] ?></td>
                                                            <td><?php echo $datosTipo["abreviatura"] ?? '-'; ?></td>
                                                            <td><?php echo $datosTipo["descripcion"] ?? '-'; ?></td>
                                                            <td>
                                                                <div class="mt-2 text-center">
                                                                    <!-- Botón de Editar (con ícono amarillo) -->
                                                                    <a name="edit" id="edit" class="btn btn-warning btn-round ml-2"
                                                                        onclick='fn_editar_tipo(<?php echo $datosTipoJSON; ?>)' role="button">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>

                                                                    <!-- Botón de Activar/Bloquear -->
                                                                    <?php if (is_null($datosTipo["deleted_at"])) { ?>
                                                                        <!-- Botón para bloquear -->
                                                                        <a name="block" id="block" class="btn btn-dark btn-round ml-2"
                                                                            onclick='fn_bloquear_tipo(<?php echo $datosTipo["id"]; ?>)' role="button">
                                                                            <i class="fa fa-lock"></i>
                                                                        </a>
                                                                    <?php } else { ?>
                                                                        <!-- Botón para activar -->
                                                                        <a name="activate" id="activate" class="btn btn-secondary btn-round ml-2"
                                                                            onclick='fn_desbloquear_tipo(<?php echo $datosTipo["id"]; ?>)' role="button">
                                                                            <i class="fa fa-unlock"></i>
                                                                        </a>
                                                                    <?php } ?>
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
                    <div class="tab-pane fade" id="pills-escala-articulo" role="tabpanel" aria-labelledby="pills-escala-articulo-tab">
                        <div class="card text-start">

                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h4 class="card-title"><i class="fas fa-sort-amount-up"></i> Escala de Articulo</h4>
                                    <button class="btn btn-success rounded-5" id="btnAgregarEscala"><i class="fas fa-plus-circle"> </i> Agregar Escala </button>
                                </div>
                                <hr>
                                <div
                                    class="row justify-content-center align-items-center md-2">

                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table
                                                id="multi-filter-select4"
                                                class="display table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Nombre</th>
                                                        <th>Descripción</th>
                                                        <th>Accion</th>
                                                    </tr>
                                                </thead>

                                                <tbody>

                                                    <?php
                                                    foreach (listarEscalaArticuloMantenimiento() as $datosEscala) {
                                                        $datosEscalaJSON = json_encode($datosEscala);


                                                    ?>
                                                        <tr>
                                                            <td><?php echo $datosEscala["id"] ?></td>
                                                            <td><?php echo $datosEscala["abreviatura"] ?? '-'; ?></td>
                                                            <td><?php echo $datosEscala["descripcion"] ?? '-'; ?></td>
                                                            <td>
                                                                <div class="mt-2 text-center">
                                                                    <!-- Botón de Editar (con ícono amarillo) -->
                                                                    <a name="edit" id="edit" class="btn btn-warning btn-round ml-2"
                                                                        onclick='fn_editar_escala(<?php echo $datosEscalaJSON; ?>)' role="button">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>

                                                                    <!-- Botón de Activar/Bloquear -->
                                                                    <?php if (is_null($datosEscala["deleted_at"])) { ?>
                                                                        <!-- Botón para bloquear -->
                                                                        <a name="block" id="block" class="btn btn-dark btn-round ml-2"
                                                                            onclick='fn_bloquear_escala(<?php echo $datosEscala["id"]; ?>)' role="button">
                                                                            <i class="fa fa-lock"></i>
                                                                        </a>
                                                                    <?php } else { ?>
                                                                        <!-- Botón para activar -->
                                                                        <a name="activate" id="activate" class="btn btn-secondary btn-round ml-2"
                                                                            onclick='fn_desbloquear_escala(<?php echo $datosEscala["id"]; ?>)' role="button">
                                                                            <i class="fa fa-unlock"></i>
                                                                        </a>
                                                                    <?php } ?>
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
                    <div class="tab-pane fade" id="pills-dimension-articulo" role="tabpanel" aria-labelledby="pills-dimension-articulo-tab">
                        <div class="card text-start">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h4 class="card-title"><i class="fas fa-ruler"></i> Dimensión de Articulo</h4>
                                    <button class="btn btn-success rounded-5" id="btnAgregarDimension"> <i class="fas fa-plus-circle"> </i> Agregar Dimensión</button>
                                </div>
                                <hr>
                                <div
                                    class="row justify-content-center align-items-center md-2">

                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table
                                                id="multi-filter-select5"
                                                class="display table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Nombre</th>
                                                        <th>Descripción</th>
                                                        <th>Accion</th>
                                                    </tr>
                                                </thead>

                                                <tbody>

                                                    <?php
                                                    foreach (listarDimensionArticuloMantenimiento() as $datosDimension) {
                                                        $datosDimensionJSON = json_encode($datosDimension);


                                                    ?>
                                                        <tr>
                                                            <td><?php echo $datosDimension["id"] ?></td>
                                                            <td><?php echo $datosDimension["medida"] ?? '-'; ?></td>
                                                            <td><?php echo $datosDimension["descripcion"] ?? '-'; ?></td>
                                                            <td>
                                                                <div class="mt-2 text-center">
                                                                    <!-- Botón de Editar (con ícono amarillo) -->
                                                                    <a name="edit" id="edit" class="btn btn-warning btn-round ml-2"
                                                                        onclick='fn_editar_dimension(<?php echo $datosDimensionJSON; ?>)' role="button">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>

                                                                    <!-- Botón de Activar/Bloquear -->
                                                                    <?php if (is_null($datosDimension["deleted_at"])) { ?>
                                                                        <!-- Botón para bloquear -->
                                                                        <a name="block" id="block" class="btn btn-dark btn-round ml-2"
                                                                            onclick='fn_bloquear_dimension(<?php echo $datosDimension["id"]; ?>)' role="button">
                                                                            <i class="fa fa-lock"></i>
                                                                        </a>
                                                                    <?php } else { ?>
                                                                        <!-- Botón para activar -->
                                                                        <a name="activate" id="activate" class="btn btn-secondary btn-round ml-2"
                                                                            onclick='fn_desbloquear_dimension(<?php echo $datosDimension["id"]; ?>)' role="button">
                                                                            <i class="fa fa-unlock"></i>
                                                                        </a>
                                                                    <?php } ?>
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
</div>

<div class="modal fade" id="modalGenerico" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalArticuloLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-dialog-custom" role="document">


        <div class="modal-content" id="contenidoGenerico">
        </div>
    </div>
</div>




<!-- Incluir el CSS de DataTables -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.min.css">

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.min.js"></script>


<!-- Incluir jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Incluir el JS de DataTables -->
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>


<script>
    /*
    window.onload = function() {
        const activeTab = localStorage.getItem('activeTab');
        if (activeTab) {
            const tab = document.getElementById(activeTab);
            if (tab) {
                // Si existe, lo activamos
                const bootstrapTab = new bootstrap.Tab(tab);
                bootstrapTab.show();
            }
        }
    };

    // Guardar el "pill" seleccionado en localStorage
    const pills = document.querySelectorAll('.nav-link');
    pills.forEach(pill => {
        pill.addEventListener('click', function() {
            const activeTabId = this.id;
            localStorage.setItem('activeTab', activeTabId);
        });
    });
    */
</script>
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



        $("#multi-filter-select2").DataTable({
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

        $("#multi-filter-select3").DataTable({
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
        $("#multi-filter-select4").DataTable({
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
    document.getElementById("btnAgregarTipo").addEventListener("click", function() {
        document.getElementById("contenidoGenerico").innerHTML = `
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="card-body">
                <h4 class="card-title text-center" style="font-size: 28px;"><i class="fas fa-cogs"></i> Registro de Tipo</h4>
            <div class="card-sub text-center">
                Aquí podrás <strong>registrar</strong> los tipos de Artículos <strong>NUEVOS.</strong>
            </div>
            <div class="card-sub text-center">
                Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.
            </div>
            <div class="card text-start">

                <div class="card-body">
                    <div
                        class="row justify-content-center align-items-center g-2">

                        <div class="col-sm-12">
                            <div class="mb-3">
                                <label for="" class="form-label"><strong>Ingrese Nombre de Tipo <span class="fw-bold text-danger">*</span></strong> </label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="idRegistroNombreTipo"
                                    id="idRegistroNombreTipo"
                                    aria-describedby="helpId"
                                    placeholder="Articulo 1" />
                            </div>

                        </div>

                        <div class="col-sm-12">
                            <div class="mb-3">
                                <label for="" class="form-label"> <strong>Descripcion</strong></label>
                                <textarea
                                    type="text"
                                    class="form-control"
                                    name="idRegistroDescripcion"
                                    id="idRegistroDescripcion"
                                    > </textarea>
                            </div>

                        </div>
                        <div class="text-center">
                            <a
                                name=""
                                id="btnRegistrarTipo"
                                class="btn btn-success btn-round"

                                role="button">Registrar <i class="fas fa-check"> </i></a>
                        </div>
                    </div>
                </div>


            </div>
            </div>
                
                
                
    
        `;

        const modal = new bootstrap.Modal(document.getElementById("modalGenerico"));
        modal.show();

        // Agregar evento de validación al botón "Registrar"

        document.getElementById("btnRegistrarTipo").addEventListener("click", async function() {
            if ((document.getElementById("idRegistroNombreTipo").value).length > 0) {

                var jsDatos = {
                    "nombre": document.getElementById("idRegistroNombreTipo").value,
                    "descripcion": document.getElementById("idRegistroDescripcion").value
                };
                console.log(jsDatos);

                $.ajax({
                    url: 'logica/clssMantenimiento.php',
                    type: 'POST',
                    data: {
                        accion: 'REGISTAR_TIPO_ARTICULO',
                        jsDatos: JSON.stringify(jsDatos)
                    },
                    success: function(response) {
                        console.log("Respuesta del servidor : ", response);
                        try {
                            var result = JSON.parse(response);
                            if (result.estado === true) {
                                swal({
                                    title: "Registrado con Exito!",
                                    text: result.mensaje,
                                    icon: "success",
                                    buttons: false,
                                    timer: 1500
                                }).then(() => {
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


            } else {
                swal("Ups!, Debes de ingresar el nombre del Tipo 😩", {
                    icon: "error",
                    buttons: {
                        confirm: {
                            className: "btn btn-danger",
                        },
                    },
                });
            }


        });

    });

    document.getElementById("btnAgregarCategoria").addEventListener("click", function() {
        document.getElementById("contenidoGenerico").innerHTML = `
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="card-body">
                        <h4 class="card-title text-center" style="font-size: 28px;"><i class="fas fa-tag"></i> Registro de Categoria</h4>
                    <div class="card-sub text-center">
                        Aquí podrás <strong>registrar</strong> los tipos de Artículos <strong>NUEVOS.</strong>
                    </div>
                    <div class="card-sub text-center">
                        Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.
                    </div>
                    <div class="card text-start">

                        <div class="card-body">
                            <div
                                class="row justify-content-center align-items-center g-2">

                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label for="" class="form-label"><strong>Ingrese Nombre de Categoria <span class="fw-bold text-danger">*</span></strong></label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="idRegistroNombreTipo"
                                            id="idRegistroNombreTipo"
                                            aria-describedby="helpId"
                                            placeholder="Articulo 1" />
                                    </div>

                                </div>

                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label for="" class="form-label"> <strong>Descripcion</strong></label>
                                        <textarea
                                            type="text"
                                            class="form-control"
                                            name="idRegistroDescripcion"
                                            id="idRegistroDescripcion"
                                            > </textarea>
                                    </div>

                                </div>
                                <div class="text-center">
                                    <a
                                        name=""
                                        id="btnRegistrarCategoria"
                                        class="btn btn-success btn-round"

                                        role="button">Registrar <i class="fas fa-check"> </i></a>
                                </div>
                            </div>
                        </div>


                    </div>
                    </div>
                        
                        
                        
            
                `;

        const modal = new bootstrap.Modal(document.getElementById("modalGenerico"));
        modal.show();

        // Agregar evento de validación al botón "Registrar"

        document.getElementById("btnRegistrarCategoria").addEventListener("click", async function() {
            if ((document.getElementById("idRegistroNombreTipo").value).length > 0) {

                var jsDatos = {
                    "nombre": document.getElementById("idRegistroNombreTipo").value,
                    "descripcion": document.getElementById("idRegistroDescripcion").value
                };
                console.log(jsDatos);

                $.ajax({
                    url: 'logica/clssMantenimiento.php',
                    type: 'POST',
                    data: {
                        accion: 'REGISTAR_CATEGORIA_ARTICULO',
                        jsDatos: JSON.stringify(jsDatos)
                    },
                    success: function(response) {
                        console.log("Respuesta del servidor : ", response);
                        try {
                            var result = JSON.parse(response);
                            if (result.estado === true) {
                                swal({
                                    title: "Registrado con Exito!",
                                    text: result.mensaje,
                                    icon: "success",
                                    buttons: false,
                                    timer: 1500
                                }).then(() => {
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


            } else {
                swal("Ups!, Debes de ingresar el nombre del Tipo 😩", {
                    icon: "error",
                    buttons: {
                        confirm: {
                            className: "btn btn-danger",
                        },
                    },
                });
            }


        });

    });

    document.getElementById("btnAgregarEscala").addEventListener("click", function() {
        document.getElementById("contenidoGenerico").innerHTML = `
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="card-body">
                        <h4 class="card-title text-center" style="font-size: 28px;"><i class="fas fa-sort"></i> Registro de Escala</h4>
                    <div class="card-sub text-center">
                        Aquí podrás <strong>registrar</strong> las escalas de Artículos <strong>NUEVOS.</strong>
                    </div>
                    <div class="card-sub text-center">
                        Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.
                    </div>
                    <div class="card text-start">

                        <div class="card-body">
                            <div
                                class="row justify-content-center align-items-center g-2">

                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label for="" class="form-label"><strong>Ingrese Nombre de Escala <span class="fw-bold text-danger">*</span> </strong></label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="idRegistroNombreTipo"
                                            id="idRegistroNombreTipo"
                                            aria-describedby="helpId"
                                            placeholder="Articulo 1" />
                                    </div>

                                </div>

                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label for="" class="form-label"> <strong>Descripcion</strong></label>
                                        <textarea
                                            type="text"
                                            class="form-control"
                                            name="idRegistroDescripcion"
                                            id="idRegistroDescripcion"
                                            > </textarea>
                                    </div>

                                </div>
                                <div class="text-center">
                                    <a
                                        name=""
                                        id="btnRegistrarEscala"
                                        class="btn btn-success btn-round"

                                        role="button">Registrar <i class="fas fa-check"> </i></a>
                                </div>
                            </div>
                        </div>


                    </div>
                    </div>
                        
                        
                        
            
                `;

        const modal = new bootstrap.Modal(document.getElementById("modalGenerico"));
        modal.show();

        // Agregar evento de validación al botón "Registrar"

        document.getElementById("btnRegistrarEscala").addEventListener("click", async function() {
            if ((document.getElementById("idRegistroNombreTipo").value).length > 0) {

                var jsDatos = {
                    "nombre": document.getElementById("idRegistroNombreTipo").value,
                    "descripcion": document.getElementById("idRegistroDescripcion").value
                };
                console.log(jsDatos);

                $.ajax({
                    url: 'logica/clssMantenimiento.php',
                    type: 'POST',
                    data: {
                        accion: 'REGISTAR_ESCALA_ARTICULO',
                        jsDatos: JSON.stringify(jsDatos)
                    },
                    success: function(response) {
                        console.log("Respuesta del servidor : ", response);
                        try {
                            var result = JSON.parse(response);
                            if (result.estado === true) {
                                swal({
                                    title: "Registrado con Exito!",
                                    text: result.mensaje,
                                    icon: "success",
                                    buttons: false,
                                    timer: 1500
                                }).then(() => {
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


            } else {
                swal("Ups!, Debes de ingresar el nombre del Tipo 😩", {
                    icon: "error",
                    buttons: {
                        confirm: {
                            className: "btn btn-danger",
                        },
                    },
                });
            }


        });

    });

    document.getElementById("btnAgregarServicio").addEventListener("click", function() {
        document.getElementById("contenidoGenerico").innerHTML = `
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="card-body">
            <h4 class="card-title text-center" style="font-size: 28px;"><i class="fas fa-ruler"></i> Registrar Servicios</h4>
            <div class="card-sub text-center">
                Aquí podrás <strong>registrar</strong> los nuevos <strong>SERVICIOS de tu negocio.</strong>
            </div>
            <div class="card-sub text-center">
                Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.
            </div>
            <div class="card text-start">
                <div class="card-body">
                    <div class="row justify-content-center align-items-center g-2">
                        <div class="col-sm-12">
                            <div class="mb-3">
                                <label for="" class="form-label"><strong>Descripción <span class="fw-bold text-danger">*</span> </strong></label>
                                <textarea
                                    class="form-control"
                                    name="idRegistroDescripcion"
                                    id="idRegistroDescripcion"></textarea>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="mb-3">
                                <label for="" class="form-label"><strong>Medidas (Selecciona las que apliquen) <span class="fw-bold text-danger">*</span></strong></label>
                                <div class="selectgroup selectgroup-pills" id="medidasSeleccion">
                                    <label class="selectgroup-item">
                                        <input type="checkbox" value="A0" class="selectgroup-input" />
                                        <span class="selectgroup-button">A0</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="checkbox" value="A1" class="selectgroup-input" />
                                        <span class="selectgroup-button">A1</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="checkbox" value="A2" class="selectgroup-input" />
                                        <span class="selectgroup-button">A2</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="checkbox" value="A3" class="selectgroup-input" />
                                        <span class="selectgroup-button">A3</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="checkbox" value="A4" class="selectgroup-input" />
                                        <span class="selectgroup-button">A4</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="checkbox" value="A5" class="selectgroup-input" />
                                        <span class="selectgroup-button">A5</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="checkbox" value="A6" class="selectgroup-input" />
                                        <span class="selectgroup-button">A6</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <a
                                name=""
                                id="btnRegistrarTipo"
                                class="btn btn-success btn-round"
                                role="button">Registrar <i class="fas fa-check"> </i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;

        const modal = new bootstrap.Modal(document.getElementById("modalGenerico"));
        modal.show();

        // Agregar evento de validación al botón "Registrar"
        document.getElementById("btnRegistrarTipo").addEventListener("click", async function() {
            const descripcion = document.getElementById("idRegistroDescripcion").value;

            // Capturar todas las medidas seleccionadas (checkboxes marcados)
            let medidasArray = [];
            const checkboxes = document.querySelectorAll("#medidasSeleccion input[type='checkbox']:checked");
            checkboxes.forEach(function(checkbox) {
                medidasArray.push(checkbox.value);
            });

            if (descripcion.length > 0 && medidasArray.length > 0) {
                var jsDatos = {
                    "descripcion": descripcion,
                    "medidas": medidasArray // Ahora es un array de valores seleccionados
                };
                console.log(jsDatos);

                $.ajax({
                    url: 'logica/clssInsertPA.php',
                    type: 'POST',
                    data: {
                        accion: 'INSERT_SERVICIOS',
                        jsDatos: JSON.stringify(jsDatos)
                    },
                    success: function(response) {
                        console.log("Respuesta del servidor: ", response);
                        try {
                            var result = JSON.parse(response);
                            if (result.estado === true) {
                                swal({
                                    title: "Registrado con Éxito!",
                                    text: result.mensaje,
                                    icon: "success",
                                    buttons: false,
                                    timer: 1500
                                }).then(() => {
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
            } else {
                swal("Ups!, Debes ingresar tanto la descripción como seleccionar al menos una medida 😩", {
                    icon: "error",
                    buttons: {
                        confirm: {
                            className: "btn btn-danger",
                        },
                    },
                });
            }
        });
    });



    document.getElementById("btnAgregarDimension").addEventListener("click", function() {
        document.getElementById("contenidoGenerico").innerHTML = `
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="card-body">
                        <h4 class="card-title text-center" style="font-size: 28px;"><i class="fas fa-ruler"></i> Registro de Dimensión</h4>
                    <div class="card-sub text-center">
                        Aquí podrás <strong>registrar</strong> las dimensiones de Artículos <strong>NUEVOS.</strong>
                    </div>
                    <div class="card-sub text-center">
                        Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.
                    </div>
                    <div class="card text-start">

                        <div class="card-body">
                            <div
                                class="row justify-content-center align-items-center g-2">

                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label for="" class="form-label"><strong>Ingrese Nombre de Dimensión <span class="fw-bold text-danger">*</span> </strong></label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="idRegistroNombreTipo"
                                            id="idRegistroNombreTipo"
                                            aria-describedby="helpId"
                                            placeholder="Articulo 1" />
                                    </div>

                                </div>

                                <div class="col-sm-12">
                                    <div class="mb-3">
                                        <label for="" class="form-label"> <strong>Descripcion</strong></label>
                                        <textarea
                                            type="text"
                                            class="form-control"
                                            name="idRegistroDescripcion"
                                            id="idRegistroDescripcion"
                                            > </textarea>
                                    </div>

                                </div>
                                <div class="text-center">
                                    <a
                                        name=""
                                        id="btnRegistrarTipo"
                                        class="btn btn-success btn-round"

                                        role="button">Registrar <i class="fas fa-check"> </i></a>
                                </div>
                            </div>
                        </div>


                    </div>
                    </div>
                        
                        
                        
            
                `;

        const modal = new bootstrap.Modal(document.getElementById("modalGenerico"));
        modal.show();

        // Agregar evento de validación al botón "Registrar"

        document.getElementById("btnRegistrarTipo").addEventListener("click", async function() {
            if ((document.getElementById("idRegistroNombreTipo").value).length > 0) {

                var jsDatos = {
                    "nombre": document.getElementById("idRegistroNombreTipo").value,
                    "descripcion": document.getElementById("idRegistroDescripcion").value
                };
                console.log(jsDatos);

                $.ajax({
                    url: 'logica/clssMantenimiento.php',
                    type: 'POST',
                    data: {
                        accion: 'REGISTAR_DIMENSION_ARTICULO',
                        jsDatos: JSON.stringify(jsDatos)
                    },
                    success: function(response) {
                        console.log("Respuesta del servidor : ", response);
                        try {
                            var result = JSON.parse(response);
                            if (result.estado === true) {
                                swal({
                                    title: "Registrado con Exito!",
                                    text: result.mensaje,
                                    icon: "success",
                                    buttons: false,
                                    timer: 1500
                                }).then(() => {
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


            } else {
                swal("Ups!, Debes de ingresar el nombre del Tipo 😩", {
                    icon: "error",
                    buttons: {
                        confirm: {
                            className: "btn btn-danger",
                        },
                    },
                });
            }


        });

    });
</script>

<script>
    function fn_editar_servicio(datosServicio) {
        // Crear el contenido para el modal de edición
        document.getElementById("contenidoGenerico").innerHTML = `
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="card-body">
            <h4 class="card-title text-center" style="font-size: 28px;"><i class="fas fa-cogs"></i> Editar Servicio</h4>
        <div class="card-sub text-center">
            Aquí podrás <strong>editar</strong> los servicios <strong>existentes.</strong>
        </div>
        <div class="card-sub text-center">
            Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.
        </div>
        <div class="card text-start">

            <div class="card-body">
                <div class="row justify-content-center align-items-center g-2">

                    <div class="col-sm-12">
                        <div class="mb-3">
                            <label for="" class="form-label"> <strong>Descripcion</strong></label>
                            <textarea
                                type="text"
                                class="form-control"
                                name="idEditarDescripcion"
                                id="idEditarDescripcion"
                            >${datosServicio.descripcion}</textarea>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="mb-3">
                            <label for="" class="form-label"><strong>Seleccione Medidas</strong></label>
                            <div class="selectgroup selectgroup-pills" id="medidasSeleccionadas">
                                ${['A0', 'A1', 'A2', 'A3', 'A4', 'A5', 'A6'].map(function (medida) {
                                    const isChecked = datosServicio.medidas.includes(medida) ? 'checked' : '';
                                    return `
                                        <label class="selectgroup-item">
                                            <input
                                                type="checkbox"
                                                name="medidas"
                                                value="${medida}"
                                                class="selectgroup-input"
                                                ${isChecked}
                                            />
                                            <span class="selectgroup-button">${medida}</span>
                                        </label>
                                    `;
                                }).join('')}
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <a
                            name=""
                            id="btnEditarServicio"
                            class="btn btn-success btn-round"
                            role="button">Actualizar <i class="fas fa-check"> </i></a>
                    </div>
                </div>
            </div>

        </div>
        </div>
    `;

        const modal = new bootstrap.Modal(document.getElementById("modalGenerico"));
        modal.show();

        // Agregar evento de validación al botón "Actualizar"
        document.getElementById("btnEditarServicio").addEventListener("click", async function() {

            const descripcionServicio = document.getElementById("idEditarDescripcion").value;
            const medidasSeleccionadas = Array.from(document.querySelectorAll('input[name="medidas"]:checked')).map(input => input.value);

            if (descripcionServicio.length > 0) {
                var jsDatos = {
                    "id": datosServicio.id,
                    "descripcion": descripcionServicio,
                    "medidas": medidasSeleccionadas
                };

                console.log(jsDatos);

                $.ajax({
                    url: 'logica/clssInsertPA.php',
                    type: 'POST',
                    data: {
                        accion: 'EDITAR_SERVICIO',
                        jsDatos: JSON.stringify(jsDatos)
                    },
                    success: function(response) {
                        console.log("Respuesta del servidor : ", response);
                        try {
                            var result = JSON.parse(response);
                            if (result.estado === true) {
                                swal({
                                    title: "Actualizado con Éxito!",
                                    text: result.mensaje,
                                    icon: "success",
                                    buttons: false,
                                    timer: 1500
                                }).then(() => {
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
            } else {
                swal("Ups!, Debes de ingresar el nombre del servicio 😩", {
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

    function fn_editar_tipo(datosTipo) {
        document.getElementById("contenidoGenerico").innerHTML = `
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="card-body">
                <h4 class="card-title text-center" style="font-size: 28px;"><i class="fas fa-cogs"></i> Editar Tipo</h4>
            <div class="card-sub text-center">
                Aquí podrás <strong>editar</strong> los tipos de Artículos <strong>NUEVOS.</strong>
            </div>
            <div class="card-sub text-center">
                Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.
            </div>
            <div class="card text-start">

                <div class="card-body">
                    <div
                        class="row justify-content-center align-items-center g-2">

                        <div class="col-sm-12">
                            <div class="mb-3">
                                <label for="" class="form-label"><strong>Ingrese Nombre de Tipo <span class="fw-bold text-danger">*</span></strong> </label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="idEditarNombreTipo"
                                    id="idEditarNombreTipo"
                                    aria-describedby="helpId"
                                    value="${datosTipo.abreviatura}"
                                    placeholder="Articulo 1" />
                            </div>

                        </div>

                        <div class="col-sm-12">
                            <div class="mb-3">
                                <label for="" class="form-label"> <strong>Descripcion</strong></label>
                                <textarea
                                    type="text"
                                    class="form-control"
                                    name="idEditarDescripcion"
                                    id="idEditarDescripcion"
                                    ></textarea>
                            </div>

                        </div>
                        <div class="text-center">
                            <a
                                name=""
                                id="btnEditarTipo"
                                class="btn btn-success btn-round"

                                role="button">Actualizar <i class="fas fa-check"> </i></a>
                        </div>
                    </div>
                </div>


            </div>
            </div>
                
                
                
    
        `;

        const modal = new bootstrap.Modal(document.getElementById("modalGenerico"));
        modal.show();
        document.getElementById("idEditarDescripcion").value = datosTipo.descripcion;

        // Agregar evento de validación al botón "Registrar"

        document.getElementById("btnEditarTipo").addEventListener("click", async function() {
            if ((document.getElementById("idEditarNombreTipo").value).length > 0) {

                var jsDatos = {
                    "id": datosTipo.id,
                    "nombre": document.getElementById("idEditarNombreTipo").value,
                    "descripcion": document.getElementById("idEditarDescripcion").value
                };
                console.log(jsDatos);

                $.ajax({
                    url: 'logica/clssMantenimiento.php',
                    type: 'POST',
                    data: {
                        accion: 'EDITAR_TIPO_ARTICULO',
                        jsDatos: JSON.stringify(jsDatos)
                    },
                    success: function(response) {
                        console.log("Respuesta del servidor : ", response);
                        try {
                            var result = JSON.parse(response);
                            if (result.estado === true) {
                                swal({
                                    title: "Registrado con Exito!",
                                    text: result.mensaje,
                                    icon: "success",
                                    buttons: false,
                                    timer: 1500
                                }).then(() => {
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


            } else {
                swal("Ups!, Debes de ingresar el nombre del Tipo 😩", {
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

    function fn_editar_categoria(datosCategoria) {
        console.log(datosCategoria)
        document.getElementById("contenidoGenerico").innerHTML = `
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="card-body">
                <h4 class="card-title text-center" style="font-size: 28px;"><i class="fas fa-cogs"></i> Editar Categoria</h4>
            <div class="card-sub text-center">
                Aquí podrás <strong>editar</strong> las categorias de Artículos <strong>NUEVOS.</strong>
            </div>
            <div class="card-sub text-center">
                Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.
            </div>
            <div class="card text-start">

                <div class="card-body">
                    <div
                        class="row justify-content-center align-items-center g-2">

                        <div class="col-sm-12">
                            <div class="mb-3">
                                <label for="" class="form-label"><strong>Ingrese Nombre de Tipo <span class="fw-bold text-danger">*</span></strong> </label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="idEditarNombreCategoria"
                                    id="idEditarNombreCategoria"
                                    aria-describedby="helpId"
                                    value="${datosCategoria.abreviatura}"
                                    placeholder="Articulo 1" />
                            </div>

                        </div>

                        <div class="col-sm-12">
                            <div class="mb-3">
                                <label for="" class="form-label"> <strong>Descripcion</strong></label>
                                <textarea
                                    type="text"
                                    class="form-control"
                                    name="idEditarDescripcion"
                                    id="idEditarDescripcion"
                                    ></textarea>
                            </div>

                        </div>
                        <div class="text-center">
                            <a
                                name=""
                                id="btnEditarCategoria"
                                class="btn btn-success btn-round"

                                role="button">Actualizar <i class="fas fa-check"> </i></a>
                        </div>
                    </div>
                </div>


            </div>
            </div>
                
                
                
    
        `;

        const modal = new bootstrap.Modal(document.getElementById("modalGenerico"));
        modal.show();
        document.getElementById("idEditarDescripcion").value = datosCategoria.descripcion;

        // Agregar evento de validación al botón "Registrar"

        document.getElementById("btnEditarCategoria").addEventListener("click", async function() {
            if ((document.getElementById("idEditarNombreCategoria").value).length > 0) {

                var jsDatos = {
                    "id": datosCategoria.id,
                    "nombre": document.getElementById("idEditarNombreCategoria").value,
                    "descripcion": document.getElementById("idEditarDescripcion").value
                };
                console.log(jsDatos);

                $.ajax({
                    url: 'logica/clssMantenimiento.php',
                    type: 'POST',
                    data: {
                        accion: 'EDITAR_CATEGORIA_ARTICULO',
                        jsDatos: JSON.stringify(jsDatos)
                    },
                    success: function(response) {
                        console.log("Respuesta del servidor : ", response);
                        try {
                            var result = JSON.parse(response);
                            if (result.estado === true) {
                                swal({
                                    title: "Registrado con Exito!",
                                    text: result.mensaje,
                                    icon: "success",
                                    buttons: false,
                                    timer: 1500
                                }).then(() => {
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


            } else {
                swal("Ups!, Debes de ingresar el nombre del Tipo 😩", {
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

    function fn_editar_escala(datosEscala) {
        document.getElementById("contenidoGenerico").innerHTML = `
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="card-body">
                <h4 class="card-title text-center" style="font-size: 28px;"><i class="fas fa-cogs"></i> Editar Escala</h4>
            <div class="card-sub text-center">
                Aquí podrás <strong>editar</strong> las escalas de Artículos <strong>NUEVOS.</strong>
            </div>
            <div class="card-sub text-center">
                Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.
            </div>
            <div class="card text-start">

                <div class="card-body">
                    <div
                        class="row justify-content-center align-items-center g-2">

                        <div class="col-sm-12">
                            <div class="mb-3">
                                <label for="" class="form-label"><strong>Ingrese Nombre de Tipo <span class="fw-bold text-danger">*</span></strong> </label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="idEditarNombreEscala"
                                    id="idEditarNombreEscala"
                                    aria-describedby="helpId"
                                    value="${datosEscala.abreviatura}"
                                    placeholder="Articulo 1" />
                            </div>

                        </div>

                        <div class="col-sm-12">
                            <div class="mb-3">
                                <label for="" class="form-label"> <strong>Descripcion</strong></label>
                                <textarea
                                    type="text"
                                    class="form-control"
                                    name="idEditarDescripcion"
                                    id="idEditarDescripcion"
                                    > </textarea>
                            </div>

                        </div>
                        <div class="text-center">
                            <a
                                name=""
                                id="btnEditarEscala"
                                class="btn btn-success btn-round"

                                role="button">Actualizar <i class="fas fa-check"> </i></a>
                        </div>
                    </div>
                </div>


            </div>
            </div>
                      
    
        `;

        const modal = new bootstrap.Modal(document.getElementById("modalGenerico"));
        modal.show();
        document.getElementById("idEditarDescripcion").value = datosEscala.descripcion;
        // Agregar evento de validación al botón "Registrar"

        document.getElementById("btnEditarEscala").addEventListener("click", async function() {
            if ((document.getElementById("idEditarNombreEscala").value).length > 0) {

                var jsDatos = {
                    "id": datosEscala.id,
                    "nombre": document.getElementById("idEditarNombreEscala").value,
                    "descripcion": document.getElementById("idEditarDescripcion").value
                };
                console.log(jsDatos);

                $.ajax({
                    url: 'logica/clssMantenimiento.php',
                    type: 'POST',
                    data: {
                        accion: 'EDITAR_ESCALA_ARTICULO',
                        jsDatos: JSON.stringify(jsDatos)
                    },
                    success: function(response) {
                        console.log("Respuesta del servidor : ", response);
                        try {
                            var result = JSON.parse(response);
                            if (result.estado === true) {
                                swal({
                                    title: "Registrado con Exito!",
                                    text: result.mensaje,
                                    icon: "success",
                                    buttons: false,
                                    timer: 1500
                                }).then(() => {
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


            } else {
                swal("Ups!, Debes de ingresar el nombre de la Escala 😩", {
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

    function fn_editar_dimension(datosdimension) {
        document.getElementById("contenidoGenerico").innerHTML = `
            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="card-body">
                <h4 class="card-title text-center" style="font-size: 28px;"><i class="fas fa-cogs"></i> Editar Dimensión</h4>
            <div class="card-sub text-center">
                Aquí podrás <strong>editar</strong> las dimensiones de Artículos <strong>NUEVOS.</strong>
            </div>
            <div class="card-sub text-center">
                Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.
            </div>
            <div class="card text-start">

                <div class="card-body">
                    <div
                        class="row justify-content-center align-items-center g-2">

                        <div class="col-sm-12">
                            <div class="mb-3">
                                <label for="" class="form-label"><strong>Ingrese Nombre de Tipo <span class="fw-bold text-danger">*</span></strong> </label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="idEditarNombreDimension"
                                    id="idEditarNombreDimension"
                                    aria-describedby="helpId"
                                    value="${datosdimension.medida}"
                                    placeholder="Articulo 1" />
                            </div>

                        </div>

                        <div class="col-sm-12">
                            <div class="mb-3">
                                <label for="" class="form-label"> <strong>Descripcion</strong></label>
                                <textarea
                                    type="text"
                                    class="form-control"
                                    name="idEditarDescripcion"
                                    id="idEditarDescripcion"
                                   
                                    > </textarea>
                            </div>

                        </div>
                        <div class="text-center">
                            <a
                                name=""
                                id="btnEditarDimension"
                                class="btn btn-success btn-round"

                                role="button">Actualizar <i class="fas fa-check"> </i></a>
                        </div>
                    </div>
                </div>


            </div>
            </div>
                     
    
        `;

        const modal = new bootstrap.Modal(document.getElementById("modalGenerico"));
        modal.show();

        // Agregar evento de validación al botón "Registrar"
        document.getElementById("idEditarDescripcion").value = datosdimension.descripcion;

        document.getElementById("btnEditarDimension").addEventListener("click", async function() {
            if ((document.getElementById("idEditarNombreDimension").value).length > 0) {

                var jsDatos = {
                    "id": datosdimension.id,
                    "nombre": document.getElementById("idEditarNombreDimension").value,
                    "descripcion": document.getElementById("idEditarDescripcion").value
                };
                console.log(jsDatos);

                $.ajax({
                    url: 'logica/clssMantenimiento.php',
                    type: 'POST',
                    data: {
                        accion: 'EDITAR_DIMENSION_ARTICULO',
                        jsDatos: JSON.stringify(jsDatos)
                    },
                    success: function(response) {
                        console.log("Respuesta del servidor : ", response);
                        try {
                            var result = JSON.parse(response);
                            if (result.estado === true) {
                                swal({
                                    title: "Registrado con Exito!",
                                    text: result.mensaje,
                                    icon: "success",
                                    buttons: false,
                                    timer: 1500
                                }).then(() => {
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


            } else {
                swal("Ups!, Debes de ingresar el nombre de la Dimensión 😩", {
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
    function fn_bloquear_tipo(datosTipo) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, bloquear',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    method: "POST",
                    url: "logica/clssMantenimiento.php",
                    data: {
                        "accion": "BLOQUEAR_TIPO",
                        "id": datosTipo
                    }
                }).done(function(response) {

                    var result = JSON.parse(response);
                    console.log(response);

                    // Verificar si el resultado contiene éxito o error
                    if (result.success === true) {
                        location.reload();
                    } else if (result.error === true) {
                        // Si existe un error, mostrar el mensaje devuelto por el servidor
                        swal("Error", result.message, {
                            icon: "error",
                            buttons: {
                                confirm: {
                                    className: "btn btn-danger",
                                },
                            },
                        });
                    }

                }).fail(function(error) {
                    console.error("Error:", error.responseText);
                });
            }
        });
    }

    function fn_desbloquear_tipo(datosTipo) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, desbloquear',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "logica/clssMantenimiento.php",
                    data: {
                        "accion": "DESBLOQUEAR_TIPO",
                        "id": datosTipo
                    }
                }).done(function(response) {

                    var result = JSON.parse(response);
                    console.log(response);

                    // Verificar si el resultado contiene éxito o error
                    if (result.success === true) {
                        location.reload();
                    } else if (result.error === true) {
                        // Si existe un error, mostrar el mensaje devuelto por el servidor
                        swal("Error", result.message, {
                            icon: "error",
                            buttons: {
                                confirm: {
                                    className: "btn btn-danger",
                                },
                            },
                        });
                    }

                }).fail(function(error) {
                    console.error("Error:", error.responseText);
                });


            }
        });
    }

    function fn_bloquear_categoria(datosCategoria) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, bloquear',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    method: "POST",
                    url: "logica/clssMantenimiento.php",
                    data: {
                        "accion": "BLOQUEAR_CATEGORIA",
                        "id": datosCategoria
                    }
                }).done(function(response) {

                    var result = JSON.parse(response);
                    console.log(response);

                    // Verificar si el resultado contiene éxito o error
                    if (result.success === true) {
                        location.reload();
                    } else if (result.error === true) {
                        // Si existe un error, mostrar el mensaje devuelto por el servidor
                        swal("Error", result.message, {
                            icon: "error",
                            buttons: {
                                confirm: {
                                    className: "btn btn-danger",
                                },
                            },
                        });
                    }

                }).fail(function(error) {
                    console.error("Error:", error.responseText);
                });
            }
        });
    }

    function fn_desbloquear_categoria(datosCategoria) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, desbloquear',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "logica/clssMantenimiento.php",
                    data: {
                        "accion": "DESBLOQUEAR_CATEGORIA",
                        "id": datosCategoria
                    }
                }).done(function(response) {

                    var result = JSON.parse(response);
                    console.log(response);

                    // Verificar si el resultado contiene éxito o error
                    if (result.success === true) {
                        location.reload();
                    } else if (result.error === true) {
                        // Si existe un error, mostrar el mensaje devuelto por el servidor
                        swal("Error", result.message, {
                            icon: "error",
                            buttons: {
                                confirm: {
                                    className: "btn btn-danger",
                                },
                            },
                        });
                    }

                }).fail(function(error) {
                    console.error("Error:", error.responseText);
                });


            }
        });
    }

    function fn_bloquear_escala(datosEscala) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, bloquear',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    method: "POST",
                    url: "logica/clssMantenimiento.php",
                    data: {
                        "accion": "BLOQUEAR_ESCALA",
                        "id": datosEscala
                    }
                }).done(function(response) {

                    var result = JSON.parse(response);
                    console.log(response);

                    // Verificar si el resultado contiene éxito o error
                    if (result.success === true) {
                        location.reload();
                    } else if (result.error === true) {
                        // Si existe un error, mostrar el mensaje devuelto por el servidor
                        swal("Error", result.message, {
                            icon: "error",
                            buttons: {
                                confirm: {
                                    className: "btn btn-danger",
                                },
                            },
                        });
                    }

                }).fail(function(error) {
                    console.error("Error:", error.responseText);
                });
            }
        });
    }

    function fn_desbloquear_escala(datosEscala) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, desbloquear',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "logica/clssMantenimiento.php",
                    data: {
                        "accion": "DESBLOQUEAR_ESCALA",
                        "id": datosEscala
                    }
                }).done(function(response) {

                    var result = JSON.parse(response);
                    console.log(response);

                    // Verificar si el resultado contiene éxito o error
                    if (result.success === true) {
                        location.reload();
                    } else if (result.error === true) {
                        // Si existe un error, mostrar el mensaje devuelto por el servidor
                        swal("Error", result.message, {
                            icon: "error",
                            buttons: {
                                confirm: {
                                    className: "btn btn-danger",
                                },
                            },
                        });
                    }

                }).fail(function(error) {
                    console.error("Error:", error.responseText);
                });


            }
        });
    }

    function fn_bloquear_dimension(datosdimension) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, bloquear',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    method: "POST",
                    url: "logica/clssMantenimiento.php",
                    data: {
                        "accion": "BLOQUEAR_DIMENSION",
                        "id": datosdimension
                    }
                }).done(function(response) {

                    var result = JSON.parse(response);
                    console.log(response);

                    // Verificar si el resultado contiene éxito o error
                    if (result.success === true) {
                        location.reload();
                    } else if (result.error === true) {
                        // Si existe un error, mostrar el mensaje devuelto por el servidor
                        swal("Error", result.message, {
                            icon: "error",
                            buttons: {
                                confirm: {
                                    className: "btn btn-danger",
                                },
                            },
                        });
                    }

                }).fail(function(error) {
                    console.error("Error:", error.responseText);
                });
            }
        });
    }

    function fn_desbloquear_dimension(datosdimension) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, desbloquear',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "logica/clssMantenimiento.php",
                    data: {
                        "accion": "DESBLOQUEAR_DIMENSION",
                        "id": datosdimension
                    }
                }).done(function(response) {

                    var result = JSON.parse(response);
                    console.log(response);

                    // Verificar si el resultado contiene éxito o error
                    if (result.success === true) {
                        location.reload();
                    } else if (result.error === true) {
                        // Si existe un error, mostrar el mensaje devuelto por el servidor
                        swal("Error", result.message, {
                            icon: "error",
                            buttons: {
                                confirm: {
                                    className: "btn btn-danger",
                                },
                            },
                        });
                    }

                }).fail(function(error) {
                    console.error("Error:", error.responseText);
                });


            }
        });
    }
</script>
<?php
include("pie.php");
?>