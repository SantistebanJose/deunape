<?php
include("cabecera.php");
$sucursal_id = isset($_SESSION['sucursal_id']) ? $_SESSION['sucursal_id'] : null;

// Verificar que existe sucursal_id
if (!$sucursal_id) {
    echo '<div class="alert alert-danger">Error: No se ha establecido una sucursal activa.</div>';
    exit;
}
?>

<style>
    .modal-dialog-custom {
        max-width: 95%;
    }
    
    .image-item {
        position: relative;
        margin-bottom: 15px;
        padding: 10px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        background: #f8f9fa;
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .image-item:hover {
        border-color: #6861ce;
        box-shadow: 0 2px 8px rgba(104, 97, 206, 0.2);
    }
    
    .image-item.principal {
        border-color: #28a745;
        background: #e8f5e9;
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
    }
    
    .btn-remove-image {
        position: absolute;
        top: 5px;
        right: 5px;
        padding: 2px 8px;
        font-size: 12px;
        z-index: 10;
    }
    
    .btn-set-principal {
        position: absolute;
        top: 5px;
        right: 45px;
        padding: 2px 8px;
        font-size: 12px;
        z-index: 10;
    }
    
    .preview-image {
        width: 100%;
        max-height: 150px;
        object-fit: contain;
        border-radius: 8px;
        margin-top: 8px;
    }
    
    .image-counter {
        font-size: 12px;
        font-weight: bold;
        color: #6861ce;
        background: white;
        padding: 2px 8px;
        border-radius: 5px;
        position: absolute;
        top: 5px;
        left: 5px;
    }
    
    .image-counter.principal {
        background: #28a745;
        color: white;
    }
    
    .badge-source {
        position: absolute;
        top: 5px;
        left: 70px;
        font-size: 10px;
        padding: 3px 8px;
        border-radius: 5px;
        z-index: 5;
    }
    
    .badge-source.web {
        background: #17a2b8;
        color: white;
    }
    
    .badge-source.drive {
        background: #4285f4;
        color: white;
    }
    
    .no-images-msg {
        text-align: center;
        padding: 40px 20px;
        color: #999;
        border: 2px dashed #ddd;
        border-radius: 10px;
    }
    
    .images-section {
        max-height: 600px;
        overflow-y: auto;
        padding-right: 10px;
    }
    
    .images-section::-webkit-scrollbar {
        width: 8px;
    }
    
    .images-section::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .images-section::-webkit-scrollbar-thumb {
        background: #6861ce;
        border-radius: 10px;
    }
    
    .nav-tabs .nav-link {
        color: #6c757d;
    }
    
    .nav-tabs .nav-link.active {
        color: #6861ce;
        font-weight: bold;
    }
    
    .badge-principal {
        background: #28a745;
        color: white;
        padding: 3px 8px;
        border-radius: 5px;
        font-size: 11px;
        margin-left: 5px;
    }
</style>

<div class="container">
    <div class="page-inner">
        <div class="card text-start">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title"><i class="fas fa-chess-queen"> </i> Artículos</h4>
                    <button class="btn btn-success rounded-5" id="btnAbrirModalGenerico"> <i class="fas fa-plus-circle"> </i> Agregar Artículo </button>
                </div>
                <hr>
                <div class="row justify-content-center align-items-center md-2">
                    <div class="col-sm-12">
                        <div class="table-filters mb-3">
                            <div class="row justify-content-center align-items-center g-2">
                                <div class="col-md-2">
                                    <select id="filterCategoria" class="form-select" style="border-radius: 25px; border: 2px solid #6861ce;">
                                        <option value="">Filtrar por Categoría</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="filterTipo" class="form-select" style="border-radius: 25px; border: 2px solid #6861ce;">
                                        <option value="">Filtrar por Tipo</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="filterDimension" class="form-select" style="border-radius: 25px; border: 2px solid #6861ce;">
                                        <option value="">Filtrar por Dimensión</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select id="filterColor" class="form-select" style="border-radius: 25px; border: 2px solid #6861ce;">
                                        <option value="">Filtrar por Color</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button id="clearFilters" class="btn btn-secondary btn-round btn-md" role="button"><i class="fas fa-broom"></i> Limpiar Filtros</button>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table id="multi-filter-select" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Artículo</th>
                                        <th>Categoría</th>
                                        <th>Tipo</th>
                                        <th>Dimensión</th>
                                        <th>Color</th>
                                        <th>Stock</th>
                                        <th>Precio de Venta</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach (listarArticuloSinview($sucursal_id) as $datosArticulo) {
                                        $datosArticuloJSON = json_encode($datosArticulo);
                                    ?>
                                        <tr>
                                            <td><?php echo $datosArticulo["articulo_id"] ?></td>
                                            <td><?php echo $datosArticulo["articulo"] ?></td>
                                            <td><?php echo $datosArticulo["categoria"] ?? '-'; ?></td>
                                            <td><?php echo $datosArticulo["tipo"] ?? '-'; ?></td>
                                            <td><?php echo $datosArticulo["dimension"] ?? '-'; ?></td>
                                            <td><?php echo $datosArticulo["color_v2"] ?? '-'; ?></td>
                                            <td><?php echo $datosArticulo["stock"] ?></td>
                                            <td><?php echo $datosArticulo["precio_venta"] ?></td>
                                            <th>
                                                <div class="mt-2 text-center">
                                                    <a name="edit" id="edit" class="btn btn-warning btn-round ml-2"
                                                        onclick='fn_editar_articulo(<?php echo $datosArticuloJSON; ?>)' role="button">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <?php if (is_null($datosArticulo["disponibilidad_venta_fh"])) { ?>
                                                        <a name="block" id="block" class="btn btn-dark btn-round ml-2"
                                                            onclick='fn_bloquear_articulo(<?php echo $datosArticulo["id"]; ?>)' role="button">
                                                            <i class="fa fa-lock"></i>
                                                        </a>
                                                    <?php } else { ?>
                                                        <a name="activate" id="activate" class="btn btn-secondary btn-round ml-2"
                                                            onclick='fn_desbloquear_articulo(<?php echo $datosArticulo["id"]; ?>)' role="button">
                                                            <i class="fa fa-unlock"></i>
                                                        </a>
                                                    <?php } ?>
                                                    <a name="edit" id="eliminate" class="btn btn-danger btn-round ml-2"
                                                        onclick='fn_eliminar_articulo(<?php echo $datosArticulo["articulo_id"] ?>)' role="button">
                                                        <i class="fas fa-times-circle"></i>
                                                    </a>
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
    </div>
</div>

<div class="modal fade" id="modalArticulo" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalArticuloLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-custom modal-xl" role="document">
        <div class="modal-content" id="contenidoArticulo"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
<script src="assets/js/scriptNotify.js"></script>
<script src="assets/js/precios-presentacion.js"></script>
<link rel="stylesheet"  href="estilos-precios.css">


<script>
    // Variable global para almacenar la sucursal_id
    var SUCURSAL_ID = <?php echo json_encode($sucursal_id); ?>;
    
    // Variables globales para almacenar las imágenes
    let imagenesArticulo = [];
    let imagenPrincipalIndex = 0;

    $(document).ready(function() {
        var table = $("#multi-filter-select").DataTable({
            pageLength: 20,
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

        // Filtros dinámicos
        table.column(2).data().unique().sort().each(function(d, j) {
            if (d !== "" && d !== "-") $('#filterCategoria').append('<option value="' + d + '">' + d + '</option>');
        });

        table.column(3).data().unique().sort().each(function(d, j) {
            if (d !== "" && d !== "-") $('#filterTipo').append('<option value="' + d + '">' + d + '</option>');
        });

        table.column(4).data().unique().sort().each(function(d, j) {
            if (d !== "" && d !== "-") $('#filterDimension').append('<option value="' + d + '">' + d + '</option>');
        });

        table.column(5).data().unique().sort().each(function(d, j) {
            if (d !== "" && d !== "-") $('#filterColor').append('<option value="' + d + '">' + d + '</option>');
        });

        $('#filterCategoria').on('change', function() {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            table.column(2).search(val ? "^" + val + "$" : "", true, false).draw();
        });

        $('#filterTipo').on('change', function() {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            table.column(3).search(val ? "^" + val + "$" : "", true, false).draw();
        });

        $('#filterDimension').on('change', function() {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            table.column(4).search(val ? "^" + val + "$" : "", true, false).draw();
        });

        $('#filterColor').on('change', function() {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());
            table.column(5).search(val ? "^" + val + "$" : "", true, false).draw();
        });

        $('#clearFilters').on('click', function() {
            $('#filterCategoria, #filterTipo, #filterDimension, #filterColor').val('');
            table.columns().search('').draw();
        });
    });

    function extraerIdDrive(url) {
        url = url.trim();
        let match = url.match(/\/file\/d\/([a-zA-Z0-9_-]+)/);
        if (match) return match[1];
        match = url.match(/[?&]id=([a-zA-Z0-9_-]+)/);
        if (match) return match[1];
        if (url.length > 20 && !url.includes('/') && !url.includes('?')) {
            return url;
        }
        return null;
    }

    function convertirUrlDrive(url) {
        const fileId = extraerIdDrive(url);
        if (!fileId) return null;
        return `https://drive.google.com/uc?export=view&id=${fileId}`;
    }

    // ============================================
// FUNCIÓN ACTUALIZADA PARA GENERAR FORMULARIO
// Reemplaza la función generarFormularioArticulo() existente
// ============================================

function generarFormularioArticulo(isEdit, datosArticulo) {
    isEdit = isEdit || false;
    datosArticulo = datosArticulo || null;
    
    var html = '<div class="card border-primary">';
    html += '<button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close" style="z-index: 1000;"></button>';
    html += '<div class="card-body">';
    html += '<h4 class="card-title text-center mb-3" style="font-size: 28px;">';
    html += '<i class="fas fa-shopping-bag"></i> ' + (isEdit ? 'Modificar' : 'Registro de') + ' Artículos';
    html += '</h4>';
    html += '<div class="card-sub text-center mb-3">';
    html += isEdit ? 'Modifica los datos del artículo' : 'Aquí podrás <strong>registrar</strong> los Artículos <strong>NUEVOS.</strong>';
    html += '</div>';
    
    // TABS PRINCIPALES
    html += '<ul class="nav nav-tabs nav-tabs-custom mb-3" role="tablist">';
    html += '<li class="nav-item" role="presentation">';
    html += '<button class="nav-link active" id="datos-tab" data-bs-toggle="tab" data-bs-target="#datos-panel" type="button" role="tab">';
    html += '<i class="fas fa-info-circle"></i> Datos del Artículo';
    html += '</button>';
    html += '</li>';
    html += '<li class="nav-item" role="presentation">';
    html += '<button class="nav-link" id="precios-tab" data-bs-toggle="tab" data-bs-target="#precios-panel" type="button" role="tab">';
    html += '<i class="fas fa-tags"></i> Precios por Presentación <span class="badge bg-primary ms-1" id="contadorPresentaciones">0</span>';
    html += '</button>';
    html += '</li>';
    html += '<li class="nav-item" role="presentation">';
    html += '<button class="nav-link" id="imagenes-tab" data-bs-toggle="tab" data-bs-target="#imagenes-panel" type="button" role="tab">';
    html += '<i class="fas fa-images"></i> Imágenes <span class="badge bg-success ms-1" id="contadorImagenes">0</span>';
    html += '</button>';
    html += '</li>';
    html += '</ul>';
    
    html += '<div class="tab-content">';
    
    // ============================================
    // TAB 1: DATOS DEL ARTÍCULO
    // ============================================
    html += '<div class="tab-pane fade show active" id="datos-panel" role="tabpanel">';
    html += '<div class="row g-3">';
    
    html += '<div class="col-md-12">';
    html += '<label class="form-label"><strong>Nombre de Artículo</strong> <span class="text-danger">*</span></label>';
    html += '<input type="text" class="form-control" id="idRegistroNombreArticulo" placeholder="Ej: Blusas Dama Manga Corta" />';
    html += '</div>';
    
    html += '<div class="col-md-4">';
    html += '<label class="form-label"><strong>Categoría</strong></label>';
    html += '<select class="form-select form-select-sm" id="idRegistoCategoria">';
    html += '<option value="">Seleccione Categoría</option>';
    html += '<?php foreach (listarCategoria($sucursal_id) as $datos) { ?>';
    html += '<option value="<?php echo $datos["id"] ?>"><?php echo $datos["abreviatura"] ?></option>';
    html += '<?php } ?>';
    html += '</select>';
    html += '</div>';
    
    html += '<div class="col-md-4">';
    html += '<label class="form-label"><strong>Tipo de Artículo</strong></label>';
    html += '<select class="form-select form-select-sm" id="idRegistoTipo">';
    html += '<option value="">Seleccione Tipo</option>';
    html += '<?php foreach (listarTipoArticulos($sucursal_id) as $datos) { ?>';
    html += '<option value="<?php echo $datos["id"] ?>"><?php echo $datos["abreviatura"] ?></option>';
    html += '<?php } ?>';
    html += '</select>';
    html += '</div>';
    
    html += '<div class="col-md-4">';
    html += '<label class="form-label"><strong>Dimensión</strong></label>';
    html += '<select class="form-select form-select-sm" id="idRegistroDimension">';
    html += '<option value="">Seleccione Dimensión</option>';
    html += '<?php foreach (listarDimension($sucursal_id) as $datos) { ?>';
    html += '<option value="<?php echo $datos["id"] ?>"><?php echo $datos["medida"] ?></option>';
    html += '<?php } ?>';
    html += '</select>';
    html += '</div>';
    
    html += '<div class="col-md-3">';
    html += '<label class="form-label"><strong>Escala</strong></label>';
    html += '<select class="form-select form-select-sm" id="idRegistroEscala">';
    html += '<option value="">Seleccione Escala</option>';
    html += '<?php foreach (listarEscala($sucursal_id) as $datos) { ?>';
    html += '<option value="<?php echo $datos["id"] ?>"><?php echo $datos["abreviatura"] ?></option>';
    html += '<?php } ?>';
    html += '</select>';
    html += '</div>';
    
    html += '<div class="col-md-3">';
    html += '<label class="form-label"><strong>Marca</strong></label>';
    html += '<input type="text" class="form-control" id="idRegistroMarca" placeholder="Ej: Artesco" />';
    html += '</div>';
    
    html += '<div class="col-md-3">';
    html += '<label class="form-label"><strong>Color</strong></label>';
    html += '<input type="text" class="form-control" id="idRegistroColor" placeholder="Rojo, verde, azul..." />';
    html += '</div>';
    
    html += '<div class="col-md-3">';
    html += '<label class="form-label"><strong>Stock</strong></label>';
    html += '<input type="number" class="form-control" id="idRegistrarStock" placeholder="0" value="0" />';
    html += '</div>';
    
    html += '<div class="col-md-4">';
    html += '<label class="form-label"><strong>Precio Compra</strong></label>';
    html += '<div class="input-group">';
    html += '<span class="input-group-text">S/.</span>';
    html += '<input type="number" step="0.01" class="form-control" id="idRegistrarPrecioCompra" placeholder="0.00" value="0" />';
    html += '</div>';
    html += '</div>';
    
    html += '<div class="col-md-4">';
    html += '<label class="form-label"><strong>Precio Venta (Base)</strong></label>';
    html += '<div class="input-group">';
    html += '<span class="input-group-text">S/.</span>';
    html += '<input type="number" step="0.01" class="form-control" id="idRegistrarPrecioVenta" placeholder="0.00" value="0" />';
    html += '</div>';
    html += '<small class="form-text text-muted">Este es el precio base unitario</small>';
    html += '</div>';
    
    html += '<div class="col-md-4">';
    html += '<label class="form-label"><strong>Requiere Corte</strong></label>';
    html += '<div class="d-flex gap-3 mt-2">';
    html += '<div class="form-check">';
    html += '<input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1" value="Si" />';
    html += '<label class="form-check-label" for="flexRadioDefault1">Sí</label>';
    html += '</div>';
    html += '<div class="form-check">';
    html += '<input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" value="No" checked />';
    html += '<label class="form-check-label" for="flexRadioDefault2">No</label>';


    
    html += '</div>';
    html += '</div>';
    html += '</div>';
    
    html += '</div>';
    html += '</div>';
    
    // ============================================
    // TAB 2: PRECIOS POR PRESENTACIÓN
    // ============================================
    html += '<div class="tab-pane fade" id="precios-panel" role="tabpanel">';
    html += '<div class="alert alert-info">';
    html += '<i class="fas fa-info-circle"></i> <strong>Precios por Presentación:</strong> Define diferentes precios según la cantidad (Ej: DOCENA, MEDIA DC, POR MAYOR)';
    html += '</div>';
    
    // Formulario para agregar presentación
    html += '<div class="card mb-3">';
    html += '<div class="card-header bg-primary text-white">';
    html += '<h6 class="mb-0"><i class="fas fa-plus-circle"></i> Agregar Nueva Presentación</h6>';
    html += '</div>';
    html += '<div class="card-body">';
    html += '<div class="row g-2">';
    
    html += '<div class="col-md-3">';
    html += '<label class="form-label"><strong>Presentación *</strong></label>';
    html += '<input type="text" class="form-control" id="inputNuevaPresentacion" placeholder="Ej: DOCENA, MEDIA DC" />';
    html += '<small class="text-muted">Nombre descriptivo</small>';
    html += '</div>';
    
    html += '<div class="col-md-2">';
    html += '<label class="form-label"><strong>Código</strong></label>';
    html += '<input type="text" class="form-control" id="inputNuevoCodigo" placeholder="Ej: DC" />';
    html += '<small class="text-muted">Opcional</small>';
    html += '</div>';
    
    html += '<div class="col-md-2">';
    html += '<label class="form-label"><strong>Cantidad *</strong></label>';
    html += '<input type="number" step="0.01" class="form-control" id="inputNuevaCantidad" placeholder="1.00" value="1.00" min="0.01" />';
    html += '<small class="text-muted">Unidades</small>';
    html += '</div>';
    
    html += '<div class="col-md-3">';
    html += '<label class="form-label"><strong>Precio *</strong></label>';
    html += '<div class="input-group">';
    html += '<span class="input-group-text">S/.</span>';
    html += '<input type="number" step="0.01" class="form-control" id="inputNuevoPrecio" placeholder="0.00" value="0.00" min="0" />';
    html += '</div>';
    html += '</div>';
    
    html += '<div class="col-md-2 d-flex align-items-end">';
    html += '<button type="button" class="btn btn-success w-100" id="btnAgregarPresentacion">';
    html += '<i class="fas fa-plus"></i> Agregar';
    html += '</button>';
    html += '</div>';
    
    html += '</div>';
    html += '</div>';
    html += '</div>';
    
    // Tabla de presentaciones
    html += '<div class="card">';
    html += '<div class="card-header bg-success text-white">';
    html += '<h6 class="mb-0"><i class="fas fa-list"></i> Presentaciones Registradas</h6>';
    html += '</div>';
    html += '<div class="card-body p-0">';
    
    html += '<div id="noPreciosMsg" class="text-center py-4">';
    html += '<i class="fas fa-tags fa-3x text-muted mb-2"></i>';
    html += '<p class="text-muted">No hay presentaciones de precios agregadas aún</p>';
    html += '</div>';
    
    html += '<div class="table-responsive">';
    html += '<table class="table table-hover mb-0">';
    html += '<thead class="table-light">';
    html += '<tr>';
    html += '<th style="width: 30%;">Presentación</th>';
    html += '<th style="width: 15%;">Código</th>';
    html += '<th style="width: 15%;">Cantidad</th>';
    html += '<th style="width: 25%;">Precio</th>';
    html += '<th style="width: 15%;" class="text-center">Acciones</th>';
    html += '</tr>';
    html += '</thead>';
    html += '<tbody id="tablaPresentacionesBody">';
    html += '</tbody>';
    html += '</table>';
    html += '</div>';
    
    html += '</div>';
    html += '</div>';
    
    html += '</div>';
    
    // ============================================
    // TAB 3: IMÁGENES (Mantener el código original)
    // ============================================
    html += '<div class="tab-pane fade" id="imagenes-panel" role="tabpanel">';
    
    html += '<ul class="nav nav-tabs mb-3" role="tablist">';
    html += '<li class="nav-item" role="presentation">';
    html += '<button class="nav-link active" id="url-tab" data-bs-toggle="tab" data-bs-target="#url-panel" type="button" role="tab">';
    html += '<i class="fas fa-link"></i> URL Web';
    html += '</button>';
    html += '</li>';
    html += '<li class="nav-item" role="presentation">';
    html += '<button class="nav-link" id="drive-tab" data-bs-toggle="tab" data-bs-target="#drive-panel" type="button" role="tab">';
    html += '<i class="fab fa-google-drive"></i> Google Drive';
    html += '</button>';
    html += '</li>';
    html += '</ul>';
    
    html += '<div class="tab-content">';
    
    html += '<div class="tab-pane fade show active" id="url-panel" role="tabpanel">';
    html += '<label class="form-label"><strong>URL de Imagen Web</strong></label>';
    html += '<div class="input-group mb-2">';
    html += '<span class="input-group-text"><i class="fas fa-link"></i></span>';
    html += '<input type="url" class="form-control" id="idNuevaUrlImagen" placeholder="https://ejemplo.com/imagen.jpg" />';
    html += '<button class="btn btn-primary" type="button" id="btnAgregarImagen">';
    html += '<i class="fas fa-plus"></i> Agregar';
    html += '</button>';
    html += '</div>';
    html += '<small class="form-text text-muted">Ingrese la URL completa de una imagen en internet</small>';
    html += '</div>';
    
    html += '<div class="tab-pane fade" id="drive-panel" role="tabpanel">';
    html += '<label class="form-label"><strong>Enlace de Google Drive</strong></label>';
    html += '<div class="input-group mb-2">';
    html += '<span class="input-group-text"><i class="fab fa-google-drive"></i></span>';
    html += '<input type="text" class="form-control" id="idNuevaUrlDrive" placeholder="https://drive.google.com/file/d/..." />';
    html += '<button class="btn btn-primary" type="button" id="btnAgregarDrive">';
    html += '<i class="fas fa-plus"></i> Agregar';
    html += '</button>';
    html += '</div>';
    html += '<small class="form-text text-muted">';
    html += '<strong>Pasos:</strong> 1) Sube la imagen a Drive, 2) Clic derecho > "Obtener enlace", 3) Configura como "Cualquier persona con el enlace", 4) Pega el enlace aquí';
    html += '</small>';
    html += '</div>';
    
    html += '</div>';
    
    html += '<hr>';
    
    html += '<div class="mb-3">';
    html += '<label class="form-label"><strong>Imágenes Agregadas</strong></label>';
    html += '<div class="images-section" id="listaImagenes">';
    html += '<div class="no-images-msg">';
    html += '<i class="fas fa-image fa-3x mb-2"></i>';
    html += '<p>No hay imágenes agregadas aún</p>';
    html += '</div>';
    html += '</div>';
    html += '</div>';
    
    html += '</div>';
    
    html += '</div>'; // Fin tab-content
    
    // Botones finales
    html += '<div class="row mt-4">';
    html += '<div class="col-12 text-center">';
    html += '<button id="' + (isEdit ? 'btnEditarArticulo' : 'btnRegistrarArticulo') + '" class="btn btn-success btn-lg btn-round">';
    html += isEdit ? '<i class="fas fa-save"></i> Guardar Cambios' : '<i class="fas fa-check"></i> Registrar Artículo';
    html += '</button>';
    html += '</div>';
    html += '</div>';
    
    html += '</div>';
    html += '</div>';
    
    return html;
}
    function renderizarListaImagenes() {
        var container = document.getElementById('listaImagenes');
        var contador = document.getElementById('contadorImagenes');
        
        if (!container || !contador) return;
        
        contador.textContent = imagenesArticulo.length;
        
        if (imagenesArticulo.length === 0) {
            container.innerHTML = '<div class="no-images-msg">' +
                '<i class="fas fa-image fa-3x mb-2"></i>' +
                '<p>No hay imágenes o videos agregados aún</p>' +
                '</div>';
            return;
        }
        
        var html = '';
        for (var i = 0; i < imagenesArticulo.length; i++) {
            var img = imagenesArticulo[i];
            var esPrincipal = i === imagenPrincipalIndex;
            var source = img.source || 'web';
            var tipoMedio = img.tipo || 'imagen';
            var sourceLabel = source === 'drive' ? 'Drive' : 'Web';
            var sourceClass = source === 'drive' ? 'drive' : 'web';
            var sourceIcon = source === 'drive' ? 'fab fa-google-drive' : 'fas fa-globe';
            
            html += '<div class="image-item ' + (esPrincipal ? 'principal' : '') + '" data-index="' + i + '" onclick="establecerImagenPrincipal(' + i + ')">';
            html += '<span class="image-counter ' + (esPrincipal ? 'principal' : '') + '">';
            html += '#' + (i + 1) + (esPrincipal ? ' ★ PRINCIPAL' : '');
            html += '</span>';
            
            html += '<span class="badge-source ' + sourceClass + '">';
            html += '<i class="' + sourceIcon + '"></i> ' + sourceLabel;
            html += '</span>';
            
            if (tipoMedio === 'video') {
                html += '<span class="badge-source" style="left: 140px; background: #e91e63; color: white;">';
                html += '<i class="fas fa-video"></i> VIDEO';
                html += '</span>';
            }
            
            if (!esPrincipal) {
                html += '<button class="btn btn-success btn-sm btn-set-principal" onclick="event.stopPropagation(); establecerImagenPrincipal(' + i + ')" title="Marcar como principal">';
                html += '<i class="fas fa-star"></i>';
                html += '</button>';
            }
            
            html += '<button class="btn btn-danger btn-sm btn-remove-image" onclick="event.stopPropagation(); eliminarImagen(' + i + ')">';
            html += '<i class="fas fa-trash"></i>';
            html += '</button>';
            
            html += '<div class="mt-4">';
            html += '<input type="url" class="form-control form-control-sm mb-2" value="' + img.url + '" onchange="actualizarUrlImagen(' + i + ', this.value)" onclick="event.stopPropagation()" placeholder="URL del medio" />';
            
            if (tipoMedio === 'video') {
                html += '<video controls class="preview-image" style="max-height: 200px;">';
                html += '<source src="' + img.url + '" type="video/mp4">';
                html += 'Tu navegador no soporta el elemento de video.';
                html += '</video>';
            } else {
                html += '<img src="' + img.url + '" class="preview-image" onerror="this.src=\'https://via.placeholder.com/300x200?text=Error+al+cargar\'" alt="Imagen ' + (i + 1) + '" />';
            }
            
            html += '</div>';
            html += '</div>';
        }
        
        container.innerHTML = html;
    }

    function establecerImagenPrincipal(index) {
        if (index >= 0 && index < imagenesArticulo.length) {
            imagenPrincipalIndex = index;
            renderizarListaImagenes();
            
            swal("¡Imagen principal actualizada!", "La imagen #" + (index + 1) + " es ahora la principal", {
                icon: "success",
                buttons: false,
                timer: 1000
            });
        }
    }

    function esVideo(url) {
        const extensionesVideo = ['.mp4', '.webm', '.ogg', '.mov', '.avi', '.mkv'];
        const urlLower = url.toLowerCase();
        return extensionesVideo.some(ext => urlLower.includes(ext));
    }

    function detectarTipoMedio(url) {
        return esVideo(url) ? 'video' : 'imagen';
    }

    function agregarImagen() {
        var input = document.getElementById('idNuevaUrlImagen');
        if (!input) return;
        
        var url = input.value.trim();
        
        if (!url) {
            swal("Error", "Por favor ingresa una URL", {
                icon: "warning",
                buttons: { confirm: { className: "btn btn-warning" } }
            });
            return;
        }
        
        if (!url.startsWith('http://') && !url.startsWith('https://')) {
            swal("Error", "La URL debe comenzar con http:// o https://", {
                icon: "error",
                buttons: { confirm: { className: "btn btn-danger" } }
            });
            return;
        }
        
        var nuevoIndex = imagenesArticulo.length + 1;
        var tipoMedio = detectarTipoMedio(url);
        
        imagenesArticulo.push({
            index: nuevoIndex,
            url: url,
            source: 'web',
            tipo: tipoMedio
        });
        
        input.value = '';
        renderizarListaImagenes();
        
        var mensaje = tipoMedio === 'video' ? 'El video se ha agregado correctamente' : 'La imagen web se ha agregado correctamente';
        swal("¡Medio agregado!", mensaje, {
            icon: "success",
            buttons: false,
            timer: 1000
        });
    }

    function agregarImagenDrive() {
        var input = document.getElementById('idNuevaUrlDrive');
        if (!input) return;
        
        var urlOriginal = input.value.trim();
        
        if (!urlOriginal) {
            swal("Error", "Por favor ingresa un enlace de Google Drive", {
                icon: "warning",
                buttons: { confirm: { className: "btn btn-warning" } }
            });
            return;
        }
        
        var urlConvertida = convertirUrlDrive(urlOriginal);
        
        if (!urlConvertida) {
            swal("Error", "El enlace de Drive no es válido...", {
                icon: "error",
                buttons: { confirm: { className: "btn btn-danger" } }
            });
            return;
        }
        
        var nuevoIndex = imagenesArticulo.length + 1;
        var tipoMedio = detectarTipoMedio(urlConvertida);
        
        imagenesArticulo.push({
            index: nuevoIndex,
            url: urlConvertida,
            source: 'drive',
            originalUrl: urlOriginal,
            tipo: tipoMedio
        });
        
        input.value = '';
        renderizarListaImagenes();
        
        var mensaje = tipoMedio === 'video' ? 'El video de Drive se ha agregado' : 'La imagen de Drive se ha agregado';
        swal("¡Medio agregado!", mensaje, {
            icon: "success",
            buttons: false,
            timer: 1000
        });
    }

    function eliminarImagen(index) {
        Swal.fire({
            title: '¿Eliminar imagen?',
            text: "Esta imagen se quitará de la lista",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                imagenesArticulo.splice(index, 1);
                
                if (imagenPrincipalIndex === index) {
                    imagenPrincipalIndex = 0;
                } else if (imagenPrincipalIndex > index) {
                    imagenPrincipalIndex--;
                }
                
                for (var i = 0; i < imagenesArticulo.length; i++) {
                    imagenesArticulo[i].index = i + 1;
                }
                
                renderizarListaImagenes();
            }
        });
    }

    function actualizarUrlImagen(index, nuevaUrl) {
        if (imagenesArticulo[index]) {
            imagenesArticulo[index].url = nuevaUrl;
            renderizarListaImagenes();
        }
    }

    function obtenerJsonImagenes() {
        if (imagenesArticulo.length === 0) {
            return null;
        }
        
        var imagenesOrdenadas = imagenesArticulo.slice();
        if (imagenPrincipalIndex !== 0) {
            var imagenPrincipal = imagenesOrdenadas.splice(imagenPrincipalIndex, 1)[0];
            imagenesOrdenadas.unshift(imagenPrincipal);
        }
        
        var imagenesConIndice = [];
        for (var i = 0; i < imagenesOrdenadas.length; i++) {
            imagenesConIndice.push({
                index: i + 1,
                url: imagenesOrdenadas[i].url,
                source: imagenesOrdenadas[i].source || 'web',
                tipo: imagenesOrdenadas[i].tipo || 'imagen'
            });
        }
        
        return JSON.stringify(imagenesConIndice);
    }

    function cargarImagenesDesdeJson(jsonString) {
        imagenesArticulo = [];
        imagenPrincipalIndex = 0;
        
        if (!jsonString) {
            renderizarListaImagenes();
            return;
        }
        
        try {
            var data = JSON.parse(jsonString);
            if (Array.isArray(data)) {
                imagenesArticulo = data;
                imagenPrincipalIndex = 0;
            }
        } catch (e) {
            console.error('Error al parsear JSON de imágenes:', e);
        }
        
        renderizarListaImagenes();
    }

    function configurarEventosImagenes() {
        var btnAgregar = document.getElementById('btnAgregarImagen');
        var inputUrl = document.getElementById('idNuevaUrlImagen');
        
        var btnAgregarDrive = document.getElementById('btnAgregarDrive');
        var inputDrive = document.getElementById('idNuevaUrlDrive');
        
        if (btnAgregar) {
            btnAgregar.addEventListener('click', agregarImagen);
        }
        
        if (inputUrl) {
            inputUrl.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    agregarImagen();
                }
            });
        }
        
        if (btnAgregarDrive) {
            btnAgregarDrive.addEventListener('click', agregarImagenDrive);
        }
        
        if (inputDrive) {
            inputDrive.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    agregarImagenDrive();
                }
            });
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("btnAbrirModalGenerico").addEventListener("click", function() {
            imagenesArticulo = [];
            imagenPrincipalIndex = 0;
            document.getElementById("contenidoArticulo").innerHTML = generarFormularioArticulo(false);
            
            var modal = new bootstrap.Modal(document.getElementById("modalArticulo"));
            modal.show();

            configurarEventosImagenes();
            renderizarListaImagenes();

            document.getElementById("btnRegistrarArticulo").addEventListener("click", function() {
                var nombreArticulo = document.getElementById("idRegistroNombreArticulo");
                
                if (!nombreArticulo || nombreArticulo.value.trim().length === 0) {
                    swal("Ups!, Debes ingresar el nombre del Artículo", {
                        icon: "error",
                        buttons: { confirm: { className: "btn btn-danger" } }
                    });
                    return;
                }

                var categoriaSelect = document.getElementById("idRegistoCategoria");
                
                if (!categoriaSelect || categoriaSelect.value === "") {
                    swal("Ups!, Para registrar un Artículo, debes elegir una Categoría", {
                        icon: "error",
                        buttons: { confirm: { className: "btn btn-danger" } }
                    });
                    return;
                }

                var tipoSelect = document.getElementById("idRegistoTipo");
                var dimensionSelect = document.getElementById("idRegistroDimension");
                var escalaSelect = document.getElementById("idRegistroEscala");
                
                var radios = document.getElementsByName("flexRadioDefault");
                var selectedValue = "No";
                for (var i = 0; i < radios.length; i++) {
                    if (radios[i].checked) {
                        selectedValue = radios[i].value;
                        break;
                    }
                }

                var stock = document.getElementById("idRegistrarStock");
                var precioVenta = document.getElementById("idRegistrarPrecioVenta");
                var precioCompra = document.getElementById("idRegistrarPrecioCompra");
                var marca = document.getElementById("idRegistroMarca");
                var color = document.getElementById("idRegistroColor");

                var jsArticulo = {
                    "nombre": nombreArticulo.value.trim(),
                    "categoria_id": categoriaSelect.value === "" ? null : categoriaSelect.value,
                    "tipo_id": tipoSelect && tipoSelect.value !== "" ? tipoSelect.value : null,
                    "dimension_id": dimensionSelect && dimensionSelect.value !== "" ? dimensionSelect.value : null,
                    "escala_id": escalaSelect && escalaSelect.value !== "" ? escalaSelect.value : null,
                    "corte": selectedValue === "Si",
                    "color": color && color.value.trim() !== "" ? color.value.trim() : null,
                    "stock": stock && stock.value !== "" ? parseFloat(stock.value) : 0,
                    "precio_venta": precioVenta && precioVenta.value !== "" ? parseFloat(precioVenta.value) : 0,
                    "precio_compra": precioCompra && precioCompra.value !== "" ? parseFloat(precioCompra.value) : 0,
                    "marca": marca && marca.value.trim() !== "" ? marca.value.trim() : null,
                    "sucursal_id": SUCURSAL_ID,
                    "json_url_img": obtenerJsonImagenes()
                };

                console.log("Datos a enviar:", jsArticulo);

                $.ajax({
                    url: 'logica/clssInsertPA.php',
                    type: 'POST',
                    data: {
                        accion: 'REGISTAR_ARTICULO_COMPLETO',
                        jsDatosArticulo: JSON.stringify(jsArticulo)
                    },
                    success: function(response) {
                        console.log("Respuesta del servidor:", response);
                        try {
                            var result = JSON.parse(response);
                            if (result.estado === true) {
                                swal({
                                    title: "¡Registrado con Éxito!",
                                    text: result.mensaje || "El artículo se ha registrado correctamente",
                                    icon: "success",
                                    buttons: false,
                                    timer: 1500
                                }).then(function() {
                                    location.reload();
                                });
                            } else {
                                swal("Error", result.mensaje || "No se pudo registrar el artículo", {
                                    icon: "error",
                                    buttons: { confirm: { className: "btn btn-danger" } }
                                });
                            }
                        } catch (e) {
                            console.error("Error al parsear JSON:", e);
                            console.error("Respuesta recibida:", response);
                            swal("Error", "No se pudo procesar la respuesta del servidor.", {
                                icon: "error",
                                buttons: { confirm: { className: "btn btn-danger" } }
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error AJAX:", error);
                        console.error("Status:", status);
                        console.error("Response:", xhr.responseText);
                        swal("Error", "Hubo un problema con la solicitud: " + error, {
                            icon: "error",
                            buttons: { confirm: { className: "btn btn-danger" } }
                        });
                    }
                });
            });
        });
    });

    function fn_editar_articulo(datosArticulo) {
        console.log("=== DATOS RECIBIDOS PARA EDITAR ===");
        console.log("Datos completos:", datosArticulo);
        
        imagenesArticulo = [];
        imagenPrincipalIndex = 0;
        document.getElementById("contenidoArticulo").innerHTML = generarFormularioArticulo(true, datosArticulo);

        var setSelectValueById = function(elementId, value) {
            var select = document.getElementById(elementId);
            if (select) {
                if (value === null || value === undefined || value === "") {
                    select.value = "";
                } else {
                    select.value = value;
                }
            }
        };

        setTimeout(function() {
            setSelectValueById("idRegistoCategoria", datosArticulo.categoria_id);
            setSelectValueById("idRegistoTipo", datosArticulo.tipo_id);
            setSelectValueById("idRegistroDimension", datosArticulo.dimension_id);
            setSelectValueById("idRegistroEscala", datosArticulo.escala_id);
            
            var nombreInput = document.getElementById("idRegistroNombreArticulo");
            if (nombreInput) nombreInput.value = datosArticulo.articulo || '';
            
            var marcaInput = document.getElementById("idRegistroMarca");
            if (marcaInput) marcaInput.value = datosArticulo.marca || '';
            
            var colorInput = document.getElementById("idRegistroColor");
            if (colorInput) colorInput.value = datosArticulo.color || '';
            
            var stockInput = document.getElementById("idRegistrarStock");
            if (stockInput) stockInput.value = datosArticulo.stock || '0';
            
            var precioCompraInput = document.getElementById("idRegistrarPrecioCompra");
            if (precioCompraInput) precioCompraInput.value = datosArticulo.precio_compra || '0';
            
            var precioVentaInput = document.getElementById("idRegistrarPrecioVenta");
            if (precioVentaInput) precioVentaInput.value = datosArticulo.precio_venta || '0';

            if (datosArticulo.corte) {
                var radioSi = document.getElementById("flexRadioDefault1");
                if (radioSi) radioSi.checked = true;
            } else {
                var radioNo = document.getElementById("flexRadioDefault2");
                if (radioNo) radioNo.checked = true;
            }

            cargarImagenesDesdeJson(datosArticulo.json_url_img);
        }, 100);

        var modal = new bootstrap.Modal(document.getElementById("modalArticulo"));
        modal.show();

        setTimeout(function() {
            configurarEventosImagenes();
        }, 150);

        setTimeout(function() {
            var btnEditar = document.getElementById("btnEditarArticulo");
            if (btnEditar) {
                btnEditar.addEventListener("click", function() {
                    var nombreArticulo = document.getElementById("idRegistroNombreArticulo");
                    
                    if (!nombreArticulo || nombreArticulo.value.trim().length === 0) {
                        swal("Ups!, Debes ingresar el nombre del Artículo", {
                            icon: "error",
                            buttons: { confirm: { className: "btn btn-danger" } }
                        });
                        return;
                    }

                    var categoriaSelect = document.getElementById("idRegistoCategoria");
                    var tipoSelect = document.getElementById("idRegistoTipo");
                    var dimensionSelect = document.getElementById("idRegistroDimension");
                    var escalaSelect = document.getElementById("idRegistroEscala");
                    
                    var radios = document.getElementsByName("flexRadioDefault");
                    var selectedValue = "No";
                    for (var i = 0; i < radios.length; i++) {
                        if (radios[i].checked) {
                            selectedValue = radios[i].value;
                            break;
                        }
                    }

                    var stock = document.getElementById("idRegistrarStock");
                    var precioVenta = document.getElementById("idRegistrarPrecioVenta");
                    var precioCompra = document.getElementById("idRegistrarPrecioCompra");
                    var marca = document.getElementById("idRegistroMarca");
                    var color = document.getElementById("idRegistroColor");

                    var jsArticulo = {
                        "id": datosArticulo.articulo_id,
                        "nombre": nombreArticulo.value.trim(),
                        "categoria_id": categoriaSelect && categoriaSelect.value !== "" ? categoriaSelect.value : null,
                        "tipo_id": tipoSelect && tipoSelect.value !== "" ? tipoSelect.value : null,
                        "dimension_id": dimensionSelect && dimensionSelect.value !== "" ? dimensionSelect.value : null,
                        "escala_id": escalaSelect && escalaSelect.value !== "" ? escalaSelect.value : null,
                        "corte": selectedValue === "Si",
                        "color": color && color.value.trim() !== "" ? color.value.trim() : null,
                        "stock": stock && stock.value !== "" ? parseFloat(stock.value) : 0,
                        "precio_venta": precioVenta && precioVenta.value !== "" ? parseFloat(precioVenta.value) : 0,
                        "precio_compra": precioCompra && precioCompra.value !== "" ? parseFloat(precioCompra.value) : 0,
                        "marca": marca && marca.value.trim() !== "" ? marca.value.trim() : null,
                        "sucursal_id": SUCURSAL_ID,
                        "json_url_img": obtenerJsonImagenes()
                    };

                    console.log("Datos a actualizar:", jsArticulo);

                    $.ajax({
                        url: 'logica/clssInsertPA.php',
                        type: 'POST',
                        data: {
                            accion: 'EDITAR_ARTICULO_COMPLETO',
                            jsDatosArticulo: JSON.stringify(jsArticulo)
                        },
                        success: function(response) {
                            console.log("Respuesta del servidor:", response);
                            try {
                                var result = JSON.parse(response);
                                if (result.estado === true) {
                                    swal({
                                        title: "¡Actualizado con Éxito!",
                                        text: result.mensaje || "El artículo se ha actualizado correctamente",
                                        icon: "success",
                                        buttons: false,
                                        timer: 1500
                                    }).then(function() {
                                        location.reload();
                                    });
                                } else {
                                    swal("Error", result.mensaje || "No se pudo actualizar el artículo", {
                                        icon: "error",
                                        buttons: { confirm: { className: "btn btn-danger" } }
                                    });
                                }
                            } catch (e) {
                                console.error("Error al parsear JSON:", e);
                                console.error("Respuesta recibida:", response);
                                swal("Error", "No se pudo procesar la respuesta del servidor.", {
                                    icon: "error",
                                    buttons: { confirm: { className: "btn btn-danger" } }
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error AJAX:", error);
                            console.error("Response:", xhr.responseText);
                            swal("Error", "Hubo un problema con la solicitud: " + error, {
                                icon: "error",
                                buttons: { confirm: { className: "btn btn-danger" } }
                            });
                        }
                    });
                });
            }
        }, 150);
    }

    function fn_bloquear_articulo(datosArticulo) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción quitará la disponibilidad para venta del artículo.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, Quitar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "logica/clssInsertPA.php",
                    data: {
                        "accion": "BLOQUEAR_ARTICULO",
                        "id": datosArticulo,
                        "sucursal_id": SUCURSAL_ID
                    }
                }).done(function(response) {
                    var result = JSON.parse(response);
                    console.log(response);

                    if (result.success === true) {
                        location.reload();
                    } else if (result.error === true) {
                        swal("Error", result.message, {
                            icon: "error",
                            buttons: { confirm: { className: "btn btn-danger" } }
                        });
                    }
                }).fail(function(error) {
                    console.error("Error:", error.responseText);
                });
            }
        });
    }

    function fn_eliminar_articulo(idArticulo) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción eliminará el artículo.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, Eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "logica/clssInsertPA.php",
                    data: {
                        "accion": "ELIMINAR_ARTICULO",
                        "id": idArticulo,
                        "sucursal_id": SUCURSAL_ID
                    }
                }).done(function(response) {
                    var result = JSON.parse(response);
                    console.log(response);

                    if (result.estado === true) {
                        swal("Artículo Eliminado!, No podrás volver a usarlo", {
                            icon: "error",
                            buttons: { confirm: { className: "btn btn-danger" } }
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        swal("Error", result.message, {
                            icon: "error",
                            buttons: { confirm: { className: "btn btn-danger" } }
                        });
                    }
                }).fail(function(error) {
                    console.error("Error:", error.responseText);
                });
            }
        });
    }

    function fn_desbloquear_articulo(datosArticulo) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción habilitará la disponibilidad para venta del artículo.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, habilitar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "logica/clssInsertPA.php",
                    data: {
                        "accion": "DESBLOQUEAR_ARTICULO",
                        "id": datosArticulo,
                        "sucursal_id": SUCURSAL_ID
                    }
                }).done(function(response) {
                    var result = JSON.parse(response);
                    console.log(response);

                    if (result.success === true) {
                        location.reload();
                    } else if (result.error === true) {
                        swal("Error", result.message, {
                            icon: "error",
                            buttons: { confirm: { className: "btn btn-danger" } }
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