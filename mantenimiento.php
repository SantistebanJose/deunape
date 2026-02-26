<?php
include("cabecera.php");
$sucursal_id = $_SESSION["sucursal_id"];
?>
<style>
    #sugerencias {
        max-height: 200px;
        overflow-y: auto;
        z-index: 1050;
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

    .modal-content {
        padding: 15px;
    }

    .dataTable {
        overflow-x: auto;
    }
</style>
<div class="container">
    <div class="page-inner">
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

                    <!-- ===================== TAB SERVICIOS ===================== -->
                    <div class="tab-pane fade show active" id="pills-servicios" role="tabpanel" aria-labelledby="pills-servicios-tab">
                        <div class="card text-start">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h4 class="card-title"><i class="fas fa-cubes"></i> Servicios del Negocio</h4>
                                    <button class="btn btn-success rounded-5" id="btnAgregarServicio"> <i class="fas fa-plus-circle"> </i> Agregar Servicio </button>
                                </div>
                                <hr>
                                <div class="row justify-content-center align-items-center md-2">
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table id="multi-filter-select" class="display table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Servicio</th>
                                                        <th>Tamaños</th>
                                                        <th>Accion</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach (listarMovimientos($sucursal_id) as $datosTipo) {
                                                        $datos = json_encode($datosTipo);
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $datosTipo["id"] ?></td>
                                                            <td><?php echo $datosTipo["descripcion"] ?></td>
                                                            <td><?php echo !empty($datosTipo["medidas"]) ? $datosTipo["medidas"] : '-'; ?></td>
                                                            <td>
                                                                <div class="mt-2 text-center">
                                                                    <a name="edit" class="btn btn-warning btn-round ml-2"
                                                                        onclick='fn_editar_servicio(<?php echo $datos; ?>)' role="button">
                                                                        <i class="fa fa-edit"></i>
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

                    <!-- ===================== TAB CATEGORIA ===================== -->
                    <div class="tab-pane fade" id="pills-categoria-articulo" role="tabpanel" aria-labelledby="pills-categoria-articulo-tab">
                        <div class="card text-start">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h4 class="card-title"><i class="fas fa-tag"></i> Categoria de Articulo</h4>
                                    <button class="btn btn-success rounded-5" id="btnAgregarCategoria"> <i class="fas fa-plus-circle"> </i> Agregar Categoria</button>
                                </div>
                                <hr>
                                <div class="row justify-content-center align-items-center md-2">
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table id="multi-filter-select2" class="display table table-striped table-hover">
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
                                                    foreach (listarCategoriaArticuloMantenimiento($sucursal_id) as $datosCategoria) {
                                                        $datosCategoriaJSON = json_encode($datosCategoria);
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $datosCategoria["id"] ?></td>
                                                            <td><?php echo $datosCategoria["abreviatura"] ?></td>
                                                            <td><?php echo $datosCategoria["descripcion"] ?></td>
                                                            <td>
                                                                <div class="mt-2 text-center">
                                                                    <a class="btn btn-warning btn-round ml-2"
                                                                        onclick='fn_editar_categoria(<?php echo $datosCategoriaJSON; ?>)' role="button">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                    <?php if (is_null($datosCategoria["deleted_at"])) { ?>
                                                                        <a class="btn btn-dark btn-round ml-2"
                                                                            onclick='fn_bloquear_categoria(<?php echo $datosCategoria["id"]; ?>)' role="button">
                                                                            <i class="fa fa-lock"></i>
                                                                        </a>
                                                                    <?php } else { ?>
                                                                        <a class="btn btn-secondary btn-round ml-2"
                                                                            onclick='fn_desbloquear_categoria(<?php echo $datosCategoria["id"]; ?>)' role="button">
                                                                            <i class="fa fa-unlock"></i>
                                                                        </a>
                                                                    <?php } ?>
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

                    <!-- ===================== TAB TIPO ===================== -->
                    <div class="tab-pane fade" id="pills-tipo-articulo" role="tabpanel" aria-labelledby="pills-tipo-articulo-tab">
                        <div class="card text-start">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h4 class="card-title"><i class="fas fa-cogs"></i> Tipo de Articulo</h4>
                                    <button class="btn btn-success rounded-5" id="btnAgregarTipo"> <i class="fas fa-plus-circle"> </i> Agregar Tipo </button>
                                </div>
                                <hr>
                                <div class="row justify-content-center align-items-center md-2">
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table id="multi-filter-select3" class="display table table-striped table-hover">
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
                                                                    <a class="btn btn-warning btn-round ml-2"
                                                                        onclick='fn_editar_tipo(<?php echo $datosTipoJSON; ?>)' role="button">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                    <?php if (is_null($datosTipo["deleted_at"])) { ?>
                                                                        <a class="btn btn-dark btn-round ml-2"
                                                                            onclick='fn_bloquear_tipo(<?php echo $datosTipo["id"]; ?>)' role="button">
                                                                            <i class="fa fa-lock"></i>
                                                                        </a>
                                                                    <?php } else { ?>
                                                                        <a class="btn btn-secondary btn-round ml-2"
                                                                            onclick='fn_desbloquear_tipo(<?php echo $datosTipo["id"]; ?>)' role="button">
                                                                            <i class="fa fa-unlock"></i>
                                                                        </a>
                                                                    <?php } ?>
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

                    <!-- ===================== TAB ESCALA ===================== -->
                    <div class="tab-pane fade" id="pills-escala-articulo" role="tabpanel" aria-labelledby="pills-escala-articulo-tab">
                        <div class="card text-start">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h4 class="card-title"><i class="fas fa-sort-amount-up"></i> Escala de Articulo</h4>
                                    <button class="btn btn-success rounded-5" id="btnAgregarEscala"><i class="fas fa-plus-circle"> </i> Agregar Escala </button>
                                </div>
                                <hr>
                                <div class="row justify-content-center align-items-center md-2">
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table id="multi-filter-select4" class="display table table-striped table-hover">
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
                                                    foreach (listarEscalaArticuloMantenimiento($sucursal_id) as $datosEscala) {
                                                        $datosEscalaJSON = json_encode($datosEscala);
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $datosEscala["id"] ?></td>
                                                            <td><?php echo $datosEscala["abreviatura"] ?? '-'; ?></td>
                                                            <td><?php echo $datosEscala["descripcion"] ?? '-'; ?></td>
                                                            <td>
                                                                <div class="mt-2 text-center">
                                                                    <a class="btn btn-warning btn-round ml-2"
                                                                        onclick='fn_editar_escala(<?php echo $datosEscalaJSON; ?>)' role="button">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                    <?php if (is_null($datosEscala["deleted_at"])) { ?>
                                                                        <a class="btn btn-dark btn-round ml-2"
                                                                            onclick='fn_bloquear_escala(<?php echo $datosEscala["id"]; ?>)' role="button">
                                                                            <i class="fa fa-lock"></i>
                                                                        </a>
                                                                    <?php } else { ?>
                                                                        <a class="btn btn-secondary btn-round ml-2"
                                                                            onclick='fn_desbloquear_escala(<?php echo $datosEscala["id"]; ?>)' role="button">
                                                                            <i class="fa fa-unlock"></i>
                                                                        </a>
                                                                    <?php } ?>
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

                    <!-- ===================== TAB DIMENSION ===================== -->
                    <div class="tab-pane fade" id="pills-dimension-articulo" role="tabpanel" aria-labelledby="pills-dimension-articulo-tab">
                        <div class="card text-start">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h4 class="card-title"><i class="fas fa-ruler"></i> Dimensión de Articulo</h4>
                                    <button class="btn btn-success rounded-5" id="btnAgregarDimension"> <i class="fas fa-plus-circle"> </i> Agregar Dimensión</button>
                                </div>
                                <hr>
                                <div class="row justify-content-center align-items-center md-2">
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table id="multi-filter-select5" class="display table table-striped table-hover">
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
                                                    foreach (listarDimensionArticuloMantenimiento($sucursal_id) as $datosDimension) {
                                                        $datosDimensionJSON = json_encode($datosDimension);
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $datosDimension["id"] ?></td>
                                                            <td><?php echo $datosDimension["medida"] ?? '-'; ?></td>
                                                            <td><?php echo $datosDimension["descripcion"] ?? '-'; ?></td>
                                                            <td>
                                                                <div class="mt-2 text-center">
                                                                    <a class="btn btn-warning btn-round ml-2"
                                                                        onclick='fn_editar_dimension(<?php echo $datosDimensionJSON; ?>)' role="button">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                    <?php if (is_null($datosDimension["deleted_at"])) { ?>
                                                                        <a class="btn btn-dark btn-round ml-2"
                                                                            onclick='fn_bloquear_dimension(<?php echo $datosDimension["id"]; ?>)' role="button">
                                                                            <i class="fa fa-lock"></i>
                                                                        </a>
                                                                    <?php } else { ?>
                                                                        <a class="btn btn-secondary btn-round ml-2"
                                                                            onclick='fn_desbloquear_dimension(<?php echo $datosDimension["id"]; ?>)' role="button">
                                                                            <i class="fa fa-unlock"></i>
                                                                        </a>
                                                                    <?php } ?>
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
    </div>
</div>

<!-- Modal Genérico -->
<div class="modal fade" id="modalGenerico" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalArticuloLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-custom" role="document">
        <div class="modal-content" id="contenidoGenerico"></div>
    </div>
</div>

<!-- CSS DataTables y SweetAlert2 -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.min.css">

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>

<script>
// ============================================
// VARIABLE GLOBAL PARA SUCURSAL_ID
// ============================================
const SUCURSAL_ID = <?php echo $sucursal_id; ?>;

// Configuración de idioma compartida para DataTables
const dtLang = {
    "sProcessing": "Procesando...",
    "sLengthMenu": "Mostrar _MENU_ registros",
    "sZeroRecords": "No se encontraron resultados",
    "sEmptyTable": "Ningún dato disponible en esta tabla",
    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
    "sSearch": "Buscar:",
    "sLoadingRecords": "Cargando...",
    "oPaginate": { "sFirst": "Primero", "sPrevious": "Anterior", "sNext": "Siguiente", "sLast": "Último" }
};

// Inicialización de DataTables
$(document).ready(function() {
    ["#multi-filter-select", "#multi-filter-select2", "#multi-filter-select3", "#multi-filter-select4", "#multi-filter-select5"].forEach(function(id) {
        $(id).DataTable({
            pageLength: 5,
            language: dtLang,
            initComplete: function() {
                this.api().columns().every(function() {
                    var column = this;
                    var select = $('<select class="form-select"><option value=""></option></select>')
                        .appendTo($(column.footer()).empty())
                        .on("change", function() {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? "^" + val + "$" : "", true, false).draw();
                        });
                    column.data().unique().sort().each(function(d) {
                        select.append('<option value="' + d + '">' + d + "</option>");
                    });
                });
            }
        });
    });
});

// ============================================
// HELPER: HTML de checkboxes de medidas
// ============================================
function getMedidasHTML(seleccionadas) {
    const medidas = ['A0', 'A1', 'A2', 'A3', 'A4', 'A5', 'A6'];
    return medidas.map(function(m) {
        const checked = seleccionadas.includes(m) ? 'checked' : '';
        return `
            <label class="selectgroup-item">
                <input type="checkbox" value="${m}" class="selectgroup-input medida-check" ${checked} />
                <span class="selectgroup-button">${m}</span>
            </label>`;
    }).join('');
}

// ============================================
// HELPER: Leer medidas seleccionadas del modal
// ============================================
function getMedidasSeleccionadas() {
    return Array.from(document.querySelectorAll(".medida-check:checked")).map(el => el.value);
}

// ============================================
// HELPER: AJAX genérico
// ============================================
function enviarAjax(url, accion, jsDatos, onSuccess) {
    $.ajax({
        url: url,
        type: 'POST',
        data: { accion: accion, jsDatos: JSON.stringify(jsDatos) },
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (result.estado === true) {
                    Swal.fire({ title: "¡Éxito!", text: result.mensaje, icon: "success", timer: 1500, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    Swal.fire({ title: "Error", text: result.mensaje, icon: "error" });
                }
            } catch(e) {
                Swal.fire({ title: "Error", text: "No se pudo procesar la respuesta del servidor.", icon: "error" });
            }
        },
        error: function() {
            Swal.fire({ title: "Error", text: "Hubo un problema con la solicitud.", icon: "error" });
        }
    });
}

// ============================================
// HELPER: Mostrar modal genérico
// ============================================
function mostrarModal(html) {
    document.getElementById("contenidoGenerico").innerHTML = html;
    new bootstrap.Modal(document.getElementById("modalGenerico")).show();
}

// ============================================
// INICIALIZACIÓN DE EVENTOS
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById("btnAgregarServicio").addEventListener("click", abrirModalRegistroServicio);
    document.getElementById("btnAgregarCategoria").addEventListener("click", abrirModalRegistroCategoria);
    document.getElementById("btnAgregarTipo").addEventListener("click", abrirModalRegistroTipo);
    document.getElementById("btnAgregarEscala").addEventListener("click", abrirModalRegistroEscala);
    document.getElementById("btnAgregarDimension").addEventListener("click", abrirModalRegistroDimension);
});

// ============================================
// MODAL REGISTRAR SERVICIO (medidas opcionales)
// ============================================
function abrirModalRegistroServicio() {
    mostrarModal(`
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="card-body">
            <h4 class="card-title text-center" style="font-size:28px;"><i class="fas fa-cubes"></i> Registrar Servicio</h4>
            <div class="card-sub text-center mb-2">
                Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.
            </div>
            <div class="card text-start">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-sm-12">
                            <label class="form-label"><strong>Descripción <span class="fw-bold text-danger">*</span></strong></label>
                            <textarea class="form-control" id="svcDescripcion" placeholder="Ej: Costura, Remalle, Trabajos académicos..."></textarea>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label">
                                <strong>Medidas <span class="text-muted fw-normal">(Opcional — selecciona solo si aplica)</span></strong>
                            </label>
                            <div class="selectgroup selectgroup-pills" id="medidasSeleccion">
                                ${getMedidasHTML([])}
                            </div>
                        </div>
                        <div class="col-12 text-center mt-2">
                            <button id="btnRegistrarServicio" class="btn btn-success btn-round">
                                Registrar <i class="fas fa-check"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `);

    document.getElementById("btnRegistrarServicio").addEventListener("click", function() {
        const descripcion = document.getElementById("svcDescripcion").value.trim();
        if (!descripcion) {
            Swal.fire({ title: "¡Ups!", text: "Debes ingresar la descripción del servicio.", icon: "warning" });
            return;
        }
        enviarAjax('logica/clssMantenimiento.php', 'INSERT_SERVICIOS', {
            descripcion: descripcion,
            medidas: getMedidasSeleccionadas(), // puede ser [] si no se seleccionó ninguna
            sucursal_id: SUCURSAL_ID
        });
    });
}

// ============================================
// MODAL EDITAR SERVICIO (medidas opcionales)
// ============================================
function fn_editar_servicio(datosServicio) {
    // Convertir medidas de string PostgreSQL "{A3,A4}" a array JS
    let medidasArray = [];
    if (typeof datosServicio.medidas === 'string' && datosServicio.medidas.length > 0) {
        medidasArray = datosServicio.medidas.replace(/[{}]/g, '').split(',').filter(Boolean);
    } else if (Array.isArray(datosServicio.medidas)) {
        medidasArray = datosServicio.medidas;
    }

    mostrarModal(`
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="card-body">
            <h4 class="card-title text-center" style="font-size:28px;"><i class="fas fa-edit"></i> Editar Servicio</h4>
            <div class="card-sub text-center mb-2">
                Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.
            </div>
            <div class="card text-start">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-sm-12">
                            <label class="form-label"><strong>Descripción <span class="fw-bold text-danger">*</span></strong></label>
                            <textarea class="form-control" id="svcDescripcion">${datosServicio.descripcion || ''}</textarea>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label">
                                <strong>Medidas <span class="text-muted fw-normal">(Opcional — selecciona solo si aplica)</span></strong>
                            </label>
                            <div class="selectgroup selectgroup-pills" id="medidasSeleccion">
                                ${getMedidasHTML(medidasArray)}
                            </div>
                        </div>
                        <div class="col-12 text-center mt-2">
                            <button id="btnEditarServicio" class="btn btn-success btn-round">
                                Actualizar <i class="fas fa-check"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `);

    document.getElementById("btnEditarServicio").addEventListener("click", function() {
        const descripcion = document.getElementById("svcDescripcion").value.trim();
        if (!descripcion) {
            Swal.fire({ title: "¡Ups!", text: "Debes ingresar la descripción del servicio.", icon: "warning" });
            return;
        }
        enviarAjax('logica/clssMantenimiento.php', 'EDITAR_SERVICIO', {
            id: datosServicio.id,
            descripcion: descripcion,
            medidas: getMedidasSeleccionadas(), // puede ser [] si no se seleccionó ninguna
            sucursal_id: SUCURSAL_ID
        });
    });
}

// ============================================
// MODALES PARA TIPO, CATEGORIA, ESCALA, DIMENSION
// ============================================
function abrirModalRegistroTipo() {
    mostrarModal(`
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="card-body">
            <h4 class="card-title text-center" style="font-size:28px;"><i class="fas fa-cogs"></i> Registro de Tipo</h4>
            <div class="card text-start"><div class="card-body"><div class="row g-2">
                <div class="col-sm-12">
                    <label class="form-label"><strong>Nombre <span class="fw-bold text-danger">*</span></strong></label>
                    <input type="text" class="form-control" id="tipoNombre" placeholder="Ej: Material" />
                </div>
                <div class="col-sm-12">
                    <label class="form-label"><strong>Descripción</strong></label>
                    <textarea class="form-control" id="tipoDescripcion"></textarea>
                </div>
                <div class="col-12 text-center mt-2">
                    <button id="btnRegistrarTipo" class="btn btn-success btn-round">Registrar <i class="fas fa-check"></i></button>
                </div>
            </div></div></div>
        </div>
    `);
    document.getElementById("btnRegistrarTipo").addEventListener("click", function() {
        const nombre = document.getElementById("tipoNombre").value.trim();
        if (!nombre) { Swal.fire({ title: "¡Ups!", text: "Debes ingresar el nombre del Tipo.", icon: "warning" }); return; }
        enviarAjax('logica/clssMantenimiento.php', 'REGISTAR_TIPO_ARTICULO', {
            nombre, descripcion: document.getElementById("tipoDescripcion").value.trim(), sucursal_id: SUCURSAL_ID
        });
    });
}

function fn_editar_tipo(datosTipo) {
    mostrarModal(`
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="card-body">
            <h4 class="card-title text-center" style="font-size:28px;"><i class="fas fa-cogs"></i> Editar Tipo</h4>
            <div class="card text-start"><div class="card-body"><div class="row g-2">
                <div class="col-sm-12">
                    <label class="form-label"><strong>Nombre <span class="fw-bold text-danger">*</span></strong></label>
                    <input type="text" class="form-control" id="tipoNombre" value="${datosTipo.abreviatura || ''}" />
                </div>
                <div class="col-sm-12">
                    <label class="form-label"><strong>Descripción</strong></label>
                    <textarea class="form-control" id="tipoDescripcion">${datosTipo.descripcion || ''}</textarea>
                </div>
                <div class="col-12 text-center mt-2">
                    <button id="btnEditarTipo" class="btn btn-success btn-round">Actualizar <i class="fas fa-check"></i></button>
                </div>
            </div></div></div>
        </div>
    `);
    document.getElementById("btnEditarTipo").addEventListener("click", function() {
        const nombre = document.getElementById("tipoNombre").value.trim();
        if (!nombre) { Swal.fire({ title: "¡Ups!", text: "Debes ingresar el nombre del Tipo.", icon: "warning" }); return; }
        enviarAjax('logica/clssMantenimiento.php', 'EDITAR_TIPO_ARTICULO', {
            id: datosTipo.id, nombre, descripcion: document.getElementById("tipoDescripcion").value.trim()
        });
    });
}

function abrirModalRegistroCategoria() {
    mostrarModal(`
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="card-body">
            <h4 class="card-title text-center" style="font-size:28px;"><i class="fas fa-tag"></i> Registro de Categoría</h4>
            <div class="card text-start"><div class="card-body"><div class="row g-2">
                <div class="col-sm-12">
                    <label class="form-label"><strong>Nombre <span class="fw-bold text-danger">*</span></strong></label>
                    <input type="text" class="form-control" id="catNombre" placeholder="Ej: Papelería" />
                </div>
                <div class="col-sm-12">
                    <label class="form-label"><strong>Descripción</strong></label>
                    <textarea class="form-control" id="catDescripcion"></textarea>
                </div>
                <div class="col-12 text-center mt-2">
                    <button id="btnRegistrarCategoria" class="btn btn-success btn-round">Registrar <i class="fas fa-check"></i></button>
                </div>
            </div></div></div>
        </div>
    `);
    document.getElementById("btnRegistrarCategoria").addEventListener("click", function() {
        const nombre = document.getElementById("catNombre").value.trim();
        if (!nombre) { Swal.fire({ title: "¡Ups!", text: "Debes ingresar el nombre de la Categoría.", icon: "warning" }); return; }
        enviarAjax('logica/clssMantenimiento.php', 'REGISTAR_CATEGORIA_ARTICULO', {
            nombre, descripcion: document.getElementById("catDescripcion").value.trim(), sucursal_id: SUCURSAL_ID
        });
    });
}

function fn_editar_categoria(datosCategoria) {
    mostrarModal(`
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="card-body">
            <h4 class="card-title text-center" style="font-size:28px;"><i class="fas fa-tag"></i> Editar Categoría</h4>
            <div class="card text-start"><div class="card-body"><div class="row g-2">
                <div class="col-sm-12">
                    <label class="form-label"><strong>Nombre <span class="fw-bold text-danger">*</span></strong></label>
                    <input type="text" class="form-control" id="catNombre" value="${datosCategoria.abreviatura || ''}" />
                </div>
                <div class="col-sm-12">
                    <label class="form-label"><strong>Descripción</strong></label>
                    <textarea class="form-control" id="catDescripcion">${datosCategoria.descripcion || ''}</textarea>
                </div>
                <div class="col-12 text-center mt-2">
                    <button id="btnEditarCategoria" class="btn btn-success btn-round">Actualizar <i class="fas fa-check"></i></button>
                </div>
            </div></div></div>
        </div>
    `);
    document.getElementById("btnEditarCategoria").addEventListener("click", function() {
        const nombre = document.getElementById("catNombre").value.trim();
        if (!nombre) { Swal.fire({ title: "¡Ups!", text: "Debes ingresar el nombre de la Categoría.", icon: "warning" }); return; }
        enviarAjax('logica/clssMantenimiento.php', 'EDITAR_CATEGORIA_ARTICULO', {
            id: datosCategoria.id, nombre, descripcion: document.getElementById("catDescripcion").value.trim()
        });
    });
}

function abrirModalRegistroEscala() {
    mostrarModal(`
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="card-body">
            <h4 class="card-title text-center" style="font-size:28px;"><i class="fas fa-sort-amount-up"></i> Registro de Escala</h4>
            <div class="card text-start"><div class="card-body"><div class="row g-2">
                <div class="col-sm-12">
                    <label class="form-label"><strong>Nombre <span class="fw-bold text-danger">*</span></strong></label>
                    <input type="text" class="form-control" id="escNombre" placeholder="Ej: Grande" />
                </div>
                <div class="col-sm-12">
                    <label class="form-label"><strong>Descripción</strong></label>
                    <textarea class="form-control" id="escDescripcion"></textarea>
                </div>
                <div class="col-12 text-center mt-2">
                    <button id="btnRegistrarEscala" class="btn btn-success btn-round">Registrar <i class="fas fa-check"></i></button>
                </div>
            </div></div></div>
        </div>
    `);
    document.getElementById("btnRegistrarEscala").addEventListener("click", function() {
        const nombre = document.getElementById("escNombre").value.trim();
        if (!nombre) { Swal.fire({ title: "¡Ups!", text: "Debes ingresar el nombre de la Escala.", icon: "warning" }); return; }
        enviarAjax('logica/clssMantenimiento.php', 'REGISTAR_ESCALA_ARTICULO', {
            nombre, descripcion: document.getElementById("escDescripcion").value.trim(), sucursal_id: SUCURSAL_ID
        });
    });
}

function fn_editar_escala(datosEscala) {
    mostrarModal(`
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="card-body">
            <h4 class="card-title text-center" style="font-size:28px;"><i class="fas fa-sort-amount-up"></i> Editar Escala</h4>
            <div class="card text-start"><div class="card-body"><div class="row g-2">
                <div class="col-sm-12">
                    <label class="form-label"><strong>Nombre <span class="fw-bold text-danger">*</span></strong></label>
                    <input type="text" class="form-control" id="escNombre" value="${datosEscala.abreviatura || ''}" />
                </div>
                <div class="col-sm-12">
                    <label class="form-label"><strong>Descripción</strong></label>
                    <textarea class="form-control" id="escDescripcion">${datosEscala.descripcion || ''}</textarea>
                </div>
                <div class="col-12 text-center mt-2">
                    <button id="btnEditarEscala" class="btn btn-success btn-round">Actualizar <i class="fas fa-check"></i></button>
                </div>
            </div></div></div>
        </div>
    `);
    document.getElementById("btnEditarEscala").addEventListener("click", function() {
        const nombre = document.getElementById("escNombre").value.trim();
        if (!nombre) { Swal.fire({ title: "¡Ups!", text: "Debes ingresar el nombre de la Escala.", icon: "warning" }); return; }
        enviarAjax('logica/clssMantenimiento.php', 'EDITAR_ESCALA_ARTICULO', {
            id: datosEscala.id, nombre, descripcion: document.getElementById("escDescripcion").value.trim()
        });
    });
}

function abrirModalRegistroDimension() {
    mostrarModal(`
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="card-body">
            <h4 class="card-title text-center" style="font-size:28px;"><i class="fas fa-ruler"></i> Registro de Dimensión</h4>
            <div class="card text-start"><div class="card-body"><div class="row g-2">
                <div class="col-sm-12">
                    <label class="form-label"><strong>Nombre <span class="fw-bold text-danger">*</span></strong></label>
                    <input type="text" class="form-control" id="dimNombre" placeholder="Ej: 21x29.7cm" />
                </div>
                <div class="col-sm-12">
                    <label class="form-label"><strong>Descripción</strong></label>
                    <textarea class="form-control" id="dimDescripcion"></textarea>
                </div>
                <div class="col-12 text-center mt-2">
                    <button id="btnRegistrarDimension" class="btn btn-success btn-round">Registrar <i class="fas fa-check"></i></button>
                </div>
            </div></div></div>
        </div>
    `);
    document.getElementById("btnRegistrarDimension").addEventListener("click", function() {
        const nombre = document.getElementById("dimNombre").value.trim();
        if (!nombre) { Swal.fire({ title: "¡Ups!", text: "Debes ingresar el nombre de la Dimensión.", icon: "warning" }); return; }
        enviarAjax('logica/clssMantenimiento.php', 'REGISTAR_DIMENSION_ARTICULO', {
            nombre, descripcion: document.getElementById("dimDescripcion").value.trim(), sucursal_id: SUCURSAL_ID
        });
    });
}

function fn_editar_dimension(datosDimension) {
    mostrarModal(`
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="card-body">
            <h4 class="card-title text-center" style="font-size:28px;"><i class="fas fa-ruler"></i> Editar Dimensión</h4>
            <div class="card text-start"><div class="card-body"><div class="row g-2">
                <div class="col-sm-12">
                    <label class="form-label"><strong>Nombre <span class="fw-bold text-danger">*</span></strong></label>
                    <input type="text" class="form-control" id="dimNombre" value="${datosDimension.medida || ''}" />
                </div>
                <div class="col-sm-12">
                    <label class="form-label"><strong>Descripción</strong></label>
                    <textarea class="form-control" id="dimDescripcion">${datosDimension.descripcion || ''}</textarea>
                </div>
                <div class="col-12 text-center mt-2">
                    <button id="btnEditarDimension" class="btn btn-success btn-round">Actualizar <i class="fas fa-check"></i></button>
                </div>
            </div></div></div>
        </div>
    `);
    document.getElementById("btnEditarDimension").addEventListener("click", function() {
        const nombre = document.getElementById("dimNombre").value.trim();
        if (!nombre) { Swal.fire({ title: "¡Ups!", text: "Debes ingresar el nombre de la Dimensión.", icon: "warning" }); return; }
        enviarAjax('logica/clssMantenimiento.php', 'EDITAR_DIMENSION_ARTICULO', {
            id: datosDimension.id, nombre, descripcion: document.getElementById("dimDescripcion").value.trim()
        });
    });
}

// ============================================
// BLOQUEAR / DESBLOQUEAR
// ============================================
function toggleEstado(tabla, accionBloquear, accionDesbloquear, accion, id) {
    if (accion === 'bloquear') {
        Swal.fire({
            title: "¿Estás seguro?",
            text: `Se bloqueará este registro.`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, bloquear",
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#d33"
        }).then(result => {
            if (result.isConfirmed) {
                $.post('logica/clssMantenimiento.php', { accion: accionBloquear, id: id }, function() {
                    Swal.fire("¡Bloqueado!", "", "success").then(() => location.reload());
                });
            }
        });
    } else {
        $.post('logica/clssMantenimiento.php', { accion: accionDesbloquear, id: id }, function() {
            Swal.fire("¡Desbloqueado!", "", "success").then(() => location.reload());
        });
    }
}

function fn_bloquear_tipo(id)         { toggleEstado('tipo', 'BLOQUEAR_TIPO', 'DESBLOQUEAR_TIPO', 'bloquear', id); }
function fn_desbloquear_tipo(id)      { toggleEstado('tipo', 'BLOQUEAR_TIPO', 'DESBLOQUEAR_TIPO', 'desbloquear', id); }
function fn_bloquear_categoria(id)    { toggleEstado('categoria', 'BLOQUEAR_CATEGORIA', 'DESBLOQUEAR_CATEGORIA', 'bloquear', id); }
function fn_desbloquear_categoria(id) { toggleEstado('categoria', 'BLOQUEAR_CATEGORIA', 'DESBLOQUEAR_CATEGORIA', 'desbloquear', id); }
function fn_bloquear_escala(id)       { toggleEstado('escala', 'BLOQUEAR_ESCALA', 'DESBLOQUEAR_ESCALA', 'bloquear', id); }
function fn_desbloquear_escala(id)    { toggleEstado('escala', 'BLOQUEAR_ESCALA', 'DESBLOQUEAR_ESCALA', 'desbloquear', id); }
function fn_bloquear_dimension(id)    { toggleEstado('dimension', 'BLOQUEAR_DIMENSION', 'DESBLOQUEAR_DIMENSION', 'bloquear', id); }
function fn_desbloquear_dimension(id) { toggleEstado('dimension', 'BLOQUEAR_DIMENSION', 'DESBLOQUEAR_DIMENSION', 'desbloquear', id); }
</script>

<?php include("pie.php"); ?>