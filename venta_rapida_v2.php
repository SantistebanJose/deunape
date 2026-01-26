<?php
include("cabecera.php");
include("logica/clssVenta.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

$sucursal_id = $_SESSION["sucursal_id"];
?>

<style>
    /* ===== VARIABLES DE COLOR ===== */
    :root {
        --primary-color: #2a2f5b;
        --primary-dark: #1a1f3a;
        --success-color: #28a745;
        --success-dark: #1e7e34;
        --danger-color: #dc3545;
        --warning-color: #ffc107;
        --light-bg: #f8f9fa;
        --card-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ===== UTILIDADES GENERALES ===== */
    .card {
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        border: none;
        margin-bottom: 1.5rem;
    }

    .card-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        border-radius: 12px 12px 0 0 !important;
        padding: 1.25rem;
    }

    .btn-round {
        border-radius: 25px;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        transition: var(--transition);
    }

    .btn-round:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* ===== TABLA DE PRODUCTOS ===== */
    #productosTable {
        font-size: 14px;
        margin-bottom: 0;
    }

    #productosTable thead {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    }

    #productosTable thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        color: white !important;
        border: none;
        padding: 15px 12px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
    }

    #productosTable tbody tr {
        transition: var(--transition);
        border-bottom: 1px solid #dee2e6;
    }

    #productosTable tbody tr:hover {
        background-color: #f0f8ff;
        transform: scale(1.002);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    #productosTable tbody td {
        padding: 12px;
        vertical-align: middle;
    }

    #productosTable .badge.bg-danger {
        background-color: #ffe5e5 !important;
        color: var(--danger-color) !important;
        border: 1px solid var(--danger-color);
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    #productosTable .text-success {
        color: var(--success-color) !important;
        font-weight: 700 !important;
        font-size: 16px !important;
    }

    #productosTable .btn-success.btn-sm {
        background: linear-gradient(135deg, var(--success-color) 0%, var(--success-dark) 100%) !important;
        border: none !important;
        border-radius: 20px !important;
        padding: 8px 16px !important;
        font-weight: 600 !important;
        font-size: 12px !important;
        transition: var(--transition) !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
    }

    #productosTable .btn-success.btn-sm:hover:not(:disabled) {
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3) !important;
    }

    #productosTable .btn-success.btn-sm:disabled {
        background: #6c757d !important;
        cursor: not-allowed;
        opacity: 0.6;
    }

    /* ===== TABLA DE ARTÍCULOS (VENTA) ===== */
    #tabla_articulos th:nth-child(1),
    #tabla_articulos td:nth-child(1),
    #tabla_articulos th:nth-child(6),
    #tabla_articulos td:nth-child(6),
    #tabla_articulos th:nth-child(7),
    #tabla_articulos td:nth-child(7) {
        display: none !important;
    }

    /* ===== FILTROS ===== */
    .table-filters .form-select {
        border-radius: 25px;
        border: 2px solid var(--primary-color);
        transition: var(--transition);
    }

    .table-filters .form-select:focus {
        border-color: var(--primary-dark);
        box-shadow: 0 0 0 0.2rem rgba(42, 47, 91, 0.25);
    }

    /* ===== MODALES ===== */
    .modal-content {
        border-radius: 15px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 1.25rem;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }

    #modalCliente .modal-content {
        background-color: #f0f8ff;
        border: 2px solid var(--primary-color);
    }

    /* ===== CARDS DE TOTALES ===== */
    .card-stats {
        border-radius: 15px;
        transition: var(--transition);
    }

    .card-stats:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .card-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
    }

    /* ===== VALIDACIÓN ===== */
    .error-input {
        border: 2px solid var(--danger-color);
        animation: shake 0.3s;
    }

    .error-message {
        color: var(--danger-color);
        font-size: 0.9em;
        margin-top: 5px;
        animation: fadeIn 0.3s;
    }

    .input-autocompleted {
        animation: pulseGreen 0.5s ease;
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-10px);
        }

        75% {
            transform: translateX(10px);
        }
    }

    @keyframes pulseGreen {
        0% {
            background-color: #d4edda;
        }

        100% {
            background-color: white;
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ===== PAGINACIÓN ===== */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin: 20px 0;
    }

    .pagination button {
        transition: var(--transition);
    }

    .pagination button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* ===== SECCIÓN DE VUELTO ===== */
    #seccionVuelto {
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #vuelto {
        font-size: 1.2rem;
        font-weight: 700;
    }

    #totalAPagar {
        background-color: #f8f9fa;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        #productosTable {
            font-size: 12px;
        }

        #productosTable th,
        #productosTable td {
            padding: 8px 4px;
        }

        .card-stats {
            margin-bottom: 1rem;
        }

        #seccionVuelto .btn-group {
            flex-wrap: wrap;
        }

        #seccionVuelto .btn-group .btn {
            margin: 2px;
        }
    }

    /* ===== SPINNER API ===== */
    .spinner-api {
        color: var(--primary-color);
    }

    /* ===== SUGERENCIAS AUTOCOMPLETE ===== */
    #sugerencias {
        max-height: 200px;
        overflow-y: auto;
        z-index: 1050;
    }

    #sugerencias .list-group-item {
        cursor: pointer;
    }

    #sugerencias .list-group-item:hover {
        background-color: #f0f8ff;
    }
</style>

<div class="container">
    <div class="page-inner">
        <!-- HEADER PRINCIPAL -->
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <i class="fas fa-shopping-cart"></i> Punto de Venta Rápida
                </h4>
                <p class="text-muted">Sistema de ventas optimizado para atención al cliente</p>

                <!-- SECCIÓN DE SERVICIOS -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-cube"></i> Servicios Disponibles</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center flex-wrap gap-2">
                            <?php foreach (listarMovimientos($sucursal_id) as $datos): ?>
                                <button class="btn btn-secondary btn-round btn-sm"
                                    onclick='fn_servicios(<?php echo json_encode($datos) ?>)'>
                                    <i class="fas fa-external-link-alt"></i> <?php echo $datos["descripcion"] ?>
                                </button>
                            <?php endforeach; ?>

                            <button class="btn btn-secondary btn-round btn-sm" id="btnAbrirModalSolo">
                                <i class="fas fa-cut"></i> SOLO CORTE
                            </button>
                            <button class="btn btn-secondary btn-round btn-sm" id="btnAbrirModalSolov2">
                                <i class="fas fa-print"></i> IMPRESIÓN 3D
                            </button>
                        </div>
                    </div>
                </div>

                <!-- FILTROS DE BÚSQUEDA -->
                <div class="table-filters mt-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <select id="filterCategoria" class="form-select">
                                <option value="">Todas las Categorías</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="filterTipo" class="form-select">
                                <option value="">Todos los Tipos</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="filterDimension" class="form-select">
                                <option value="">Todas las Dimensiones</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="filterColor" class="form-select">
                                <option value="">Todos los Colores</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" id="searchInput" class="form-control"
                                placeholder="🔍 Buscar artículo..." onkeyup="filterProducts()">
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <button id="clearFilters" class="btn btn-warning btn-round">
                            <i class="fas fa-broom"></i> Limpiar Filtros
                        </button>
                    </div>
                </div>

                <!-- TABLA DE PRODUCTOS -->
                <div class="table-responsive mt-4">
                    <table class="table table-hover align-middle" id="productosTable">
                        <thead>
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
                            <!-- Productos dinámicos -->
                        </tbody>
                    </table>
                </div>

                <!-- PAGINACIÓN -->
                <div class="pagination">
                    <button id="prevPage" class="btn btn-secondary btn-round" onclick="changePage(-1)">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </button>
                    <button id="nextPage" class="btn btn-secondary btn-round" onclick="changePage(1)">
                        Siguiente <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- TABLA DE ARTÍCULOS SELECCIONADOS -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-list"></i> Detalle de Venta</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabla_articulos" class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Artículo</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Subtotal (S/)</th>
                                <th>Acción</th>
                                <th>ID_MOV</th>
                                <th>NOTA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Artículos dinámicos -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TOTALES Y BOTÓN DE PAGO -->
        <div class="card">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card card-stats">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Artículos</h5>
                                <h3 class="text-primary mb-0">
                                    S/ <span id="id_subtotal_articulos">0.00</span>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-primary card-stats">
                            <div class="card-body text-center">
                                <h5 class="card-title text-white">Total General</h5>
                                <h3 class="text-white mb-0">
                                    S/ <span id="id_subtotal_general">0.00</span>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button id="btnRealizarPago" disabled type="button"
                        class="btn btn-success btn-lg btn-round px-5">
                        <i class="fas fa-cash-register"></i> Procesar Venta
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: SOLO CORTE -->
<div class="modal fade" id="modalSoloCorte" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-cut"></i> Servicio de Corte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <label class="form-label fw-bold">Minutos de Corte</label>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <button id="btnRestarSoloCorte" class="btn btn-danger btn-round">-</button>
                        <input id="cantidad_solocorte" type="number" class="form-control text-center"
                            value="0" style="width: 100px;">
                        <button id="btnSumarSoloCorte" class="btn btn-success btn-round">+</button>
                    </div>
                </div>
                <hr>
                <div class="text-center mb-3">
                    <label class="form-label fw-bold">Precio (S/)</label>
                    <input id="precioSoloCorte" type="number" class="form-control text-center"
                        value="1.5" style="width: 150px; margin: 0 auto;">
                    <div class="d-flex justify-content-center gap-2 mt-2">
                        <button id="btnIncremento05SoloCorte" class="btn btn-outline-primary btn-sm">+0.5</button>
                        <button id="btnIncremento1SoloCorte" class="btn btn-outline-primary btn-sm">+1</button>
                        <button id="btnIncremento2SoloCorte" class="btn btn-outline-primary btn-sm">+2</button>
                        <button id="btnIncremento5SoloCorte" class="btn btn-outline-primary btn-sm">+5</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-round" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success btn-round" id="btn_agregar_solocorte">
                    <i class="fas fa-plus"></i> Agregar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: IMPRESIÓN 3D -->
<div class="modal fade" id="modalSoloCorteMaquina2" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-print"></i> Servicio de Impresión 3D</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <label class="form-label fw-bold">Tiempo de Impresión (minutos)</label>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <button class="btn btn-danger btn-round btn-sm" onclick='fnAumentoOrResta("-")'>
                            <i class="fas fa-minus"></i>
                        </button>
                        <input id="cantidad_solocortev2" type="number" class="form-control text-center"
                            value="10" style="width: 100px;">
                        <button class="btn btn-success btn-round btn-sm" onclick='fnAumentoOrResta("+")'>
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-center gap-2 mt-2 flex-wrap">
                        <button class="btn btn-outline-primary btn-sm btn-round" onclick="fnAumentarMin(15)">15 Min</button>
                        <button class="btn btn-outline-primary btn-sm btn-round" onclick="fnAumentarMin(30)">30 Min</button>
                        <button class="btn btn-outline-primary btn-sm btn-round" onclick="fnAumentarMin(45)">45 Min</button>
                        <button class="btn btn-outline-primary btn-sm btn-round" onclick="fnAumentarMin(60)">1 Hora</button>
                        <button class="btn btn-outline-primary btn-sm btn-round" onclick="fnAumentarMin(120)">2 Horas</button>
                        <button class="btn btn-outline-primary btn-sm btn-round" onclick="fnAumentarMin(180)">3 Horas</button>
                    </div>
                </div>
                <hr>
                <div class="text-center mb-3">
                    <label class="form-label fw-bold">Precio (S/)</label>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <input id="precioSoloCortev2" type="number" class="form-control text-center"
                            value="1.5" style="width: 120px;">
                        <button class="btn btn-danger btn-sm" onclick="limpiar()">
                            <i class="fas fa-broom"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-center gap-2 mt-2">
                        <button class="btn btn-outline-primary btn-sm btn-round" onclick="fnAumentaPrecioImpresion(0.5)">+0.5</button>
                        <button class="btn btn-outline-primary btn-sm btn-round" onclick="fnAumentaPrecioImpresion(1)">+1</button>
                        <button class="btn btn-outline-primary btn-sm btn-round" onclick="fnAumentaPrecioImpresion(2)">+2</button>
                        <button class="btn btn-outline-primary btn-sm btn-round" onclick="fnAumentaPrecioImpresion(5)">+5</button>
                    </div>
                </div>
                <hr>
                <div>
                    <label class="form-label fw-bold">Nota</label>
                    <textarea id="nota_impresion" class="form-control" rows="3"
                        placeholder="Ej: Modelo específico, color, etc."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-round" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success btn-round" id="btn_agregar_solocortev2">
                    <i class="fas fa-plus"></i> Agregar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: CANTIDAD Y CORTE -->
<div class="modal fade" id="modalCantidad" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configurar Artículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6 id="nombreArticulo" class="text-center fw-bold mb-3">Artículo</h6>

                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <label class="form-label text-center d-block">Cantidad</label>
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            <button id="btnRestarCantidad" class="btn btn-danger btn-round">-</button>
                            <input id="inputCantidad" type="number" class="form-control text-center"
                                value="1" style="width: 100px;">
                            <button id="btnSumarCantidad" class="btn btn-success btn-round">+</button>
                        </div>
                    </div>
                </div>

                <div id="seccionCorte" class="card bg-light" style="display: none;">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Minutos de Corte</label>
                                <div class="d-flex align-items-center gap-2">
                                    <button id="btnRestarCorte" class="btn btn-danger btn-sm btn-round">-</button>
                                    <input id="cantidadCorte" type="number" class="form-control text-center" value="0">
                                    <button id="btnSumarCorte" class="btn btn-success btn-sm btn-round">+</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Precio Corte (S/)</label>
                                <input id="precioCorte" type="number" class="form-control text-center" value="1.5">
                                <div class="d-flex gap-1 mt-2">
                                    <button id="btnIncremento05" class="btn btn-outline-primary btn-sm flex-fill">+0.5</button>
                                    <button id="btnIncremento1" class="btn btn-outline-primary btn-sm flex-fill">+1</button>
                                    <button id="btnIncremento2" class="btn btn-outline-primary btn-sm flex-fill">+2</button>
                                    <button id="btnIncremento5" class="btn btn-outline-primary btn-sm flex-fill">+5</button>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div>
                            <label class="form-label">Detalle</label>
                            <textarea id="idTextAreaDetalleInsert" class="form-control" rows="2"
                                placeholder="Medidas, restante, observaciones..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-round" data-bs-dismiss="modal">Cancelar</button>
                <button id="btnConfirmarCantidad" class="btn btn-success btn-round">
                    <i class="fas fa-check"></i> Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: REGISTRAR CLIENTE -->
<div class="modal fade" id="modalCliente" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Registrar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-pills nav-secondary mb-3" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="pills-persona-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-persona" type="button">
                            <i class="fas fa-user"></i> Persona
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="pills-empresa-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-empresa" type="button">
                            <i class="fas fa-building"></i> Empresa
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- TAB PERSONA -->
                    <div class="tab-pane fade show active" id="pills-persona">
                        <div class="mb-3">
                            <label class="form-label">
                                DNI <span class="text-danger">*</span>
                                <small class="text-muted">(Autocompletado)</small>
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="numeroDocumentoPersona"
                                    placeholder="8 dígitos" maxlength="8">
                                <button class="btn btn-outline-primary" type="button" id="btnBuscarDNI">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="error-numeroDocumentoPersona"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nombres <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombresPersona">
                            <div class="invalid-feedback" id="error-nombresPersona"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="apellidosPersona">
                            <div class="invalid-feedback" id="error-apellidosPersona"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefonoPersona" maxlength="9">
                            <div class="invalid-feedback" id="error-telefonoPersona"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="emailPersona">
                            <div class="invalid-feedback" id="error-emailPersona"></div>
                        </div>
                    </div>

                    <!-- TAB EMPRESA -->
                    <div class="tab-pane fade" id="pills-empresa">
                        <div class="mb-3">
                            <label class="form-label">
                                RUC <span class="text-danger">*</span>
                                <small class="text-muted">(Autocompletado)</small>
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="numeroDocumentoEmpresa"
                                    placeholder="11 dígitos" maxlength="11">
                                <button class="btn btn-outline-primary" type="button" id="btnBuscarRUC">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="error-numeroDocumentoEmpresa"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nombre Comercial <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombreComercial">
                            <div class="invalid-feedback" id="error-nombreComercial"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Razón Social <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="razonSocial">
                            <div class="invalid-feedback" id="error-razonSocial"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="emailEmpresa">
                            <div class="invalid-feedback" id="error-emailEmpresa"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefonoEmpresa" maxlength="9">
                            <div class="invalid-feedback" id="error-telefonoEmpresa"></div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <small>Los campos con <span class="text-danger">*</span> son obligatorios</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-round" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success btn-round" id="btnRegistrarCliente">
                    <i class="fas fa-save"></i> Registrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: REALIZAR PAGO -->
<div class="modal fade" id="modalRealizarPago" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="w-100 text-center">
                    <h4 class="mb-1"><i class="fas fa-credit-card"></i> Procesar Pago</h4>
                    <h2 class="text-success mb-0">S/ <span id="idMontoVentaTitulo">0.00</span></h2>
                </div>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <small class="text-muted">
                        ID Venta: <span id="idVenta">#</span> |
                        ID Cliente: <span id="idPersona">#</span> |
                        Usuario: <span id="idUsuario"><?php echo $id_usuario_s ?></span>
                    </small>
                </div>

                <!-- ACORDEÓN DATOS CLIENTE -->
                <div class="accordion mb-3" id="accordionCliente">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                <i class="fas fa-user me-2"></i> Datos del Cliente
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                <div class="mb-3">
                                    <label class="form-label">Cliente</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="nombreCliente"
                                            placeholder="Buscar cliente por nombre o DNI">
                                        <button type="button" class="btn btn-primary" id="btnAbrirModalCliente">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                    </div>
                                    <div id="sugerencias" class="list-group position-absolute w-100"></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" id="idUpdateNumTelefonoCliente">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" id="idUpdateCorreoCliente">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MONTOS -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Monto Original</label>
                        <div class="input-group">
                            <span class="input-group-text">S/</span>
                            <input type="text" class="form-control" id="montoTotal" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Monto Final (con descuento)</label>
                        <div class="input-group">
                            <span class="input-group-text">S/</span>
                            <input type="number" class="form-control" id="montoTotalFinal"
                                placeholder="Opcional" step="0.01">
                        </div>
                    </div>
                </div>

                <!-- TABS PAGO -->
                <ul class="nav nav-pills nav-secondary mb-3" role="tablist">
                    <li class="nav-item flex-fill">
                        <a class="nav-link active text-center" id="pills-home-tab-icon"
                            data-bs-toggle="pill" href="#pago-directo">
                            <i class="fas fa-money-bill-wave"></i> Pago Directo
                        </a>
                    </li>
                    <li class="nav-item flex-fill">
                        <a class="nav-link text-center" id="pills-profile-tab-icon"
                            data-bs-toggle="pill" href="#pago-credito">
                            <i class="fas fa-credit-card"></i> Pago a Crédito
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- PAGO DIRECTO -->
                    <div class="tab-pane fade show active" id="pago-directo">
                        <form id="form-pago-directo">
                            <div class="text-center mb-3">
                                <label class="form-label fw-bold">Tipo de Comprobante</label>
                                <div class="btn-group d-flex" role="group">
                                    <input type="radio" class="btn-check" name="icon-input"
                                        id="boleta" value="boleta" checked>
                                    <label class="btn btn-outline-primary flex-fill" for="boleta">Boleta</label>

                                    <input type="radio" class="btn-check" name="icon-input"
                                        id="factura" value="factura">
                                    <label class="btn btn-outline-primary flex-fill" for="factura">Factura</label>
                                </div>
                            </div>

                            <div class="text-center mb-3">
                                <button id="btnAgregarPago" class="btn btn-secondary btn-sm btn-round" type="button">
                                    <i class="fas fa-plus"></i> Agregar Forma de Pago
                                </button>
                            </div>

                            <div class="card bg-light mb-2">
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <select class="form-select form-select-sm" name="formaPago" id="formaPagoSelect">
                                                <?php foreach (listarFormaPago() as $datosFormaPago): ?>
                                                    <option value="<?php echo $datosFormaPago["id"] ?>">
                                                        <?php echo $datosFormaPago["nombre"] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="number" class="form-control form-control-sm"
                                                placeholder="Monto S/" min="0" name="monto" id="montoSelect_0" step="0.01">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="contenedorPagos"></div>

                            <!-- SECCIÓN DE VUELTO -->
                            <div id="seccionVuelto" class="card border-success mt-3" style="display: none;">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-calculator"></i> Cálculo de Vuelto</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Total a Pagar</label>
                                            <div class="input-group">
                                                <span class="input-group-text">S/</span>
                                                <input type="text" class="form-control" id="totalAPagar" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Paga con</label>
                                            <div class="input-group">
                                                <span class="input-group-text">S/</span>
                                                <input type="number" class="form-control" id="pagaCon"
                                                    placeholder="0.00" step="0.01" min="0">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Vuelto</label>
                                            <div class="input-group">
                                                <span class="input-group-text">S/</span>
                                                <input type="text" class="form-control fw-bold text-success"
                                                    id="vuelto" readonly value="0.00">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Botones rápidos -->
                                    <div class="text-center mt-3">
                                        <small class="text-muted d-block mb-2">Pago rápido:</small>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-success" onclick="setPagaCon(10)">S/ 10</button>
                                            <button type="button" class="btn btn-outline-success" onclick="setPagaCon(20)">S/ 20</button>
                                            <button type="button" class="btn btn-outline-success" onclick="setPagaCon(50)">S/ 50</button>
                                            <button type="button" class="btn btn-outline-success" onclick="setPagaCon(100)">S/ 100</button>
                                            <button type="button" class="btn btn-outline-success" onclick="setPagaCon(200)">S/ 200</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <button class="btn btn-success btn-lg btn-round px-5" onclick="fn_pagar_directo()">
                                <i class="fas fa-check-circle"></i> Confirmar Pago
                            </button>
                        </div>
                    </div>

                    <!-- PAGO CRÉDITO -->
                    <div class="tab-pane fade" id="pago-credito">
                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                Si el cliente realiza un pago inicial, regístralo.
                                Si no, deja en blanco y procede con el crédito.
                            </small>
                        </div>

                        <form id="form-pago-credito">
                            <div class="text-center mb-3">
                                <button id="btnAgregarPagoCredito" class="btn btn-secondary btn-sm btn-round" type="button">
                                    <i class="fas fa-plus"></i> Agregar Pago Inicial
                                </button>
                            </div>

                            <div class="card bg-light mb-2">
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <select class="form-select form-select-sm" name="formaPagoCredito[]"
                                                id="formaPagoCreditoSelect_0">
                                                <?php foreach (listarFormaPago() as $datosFormaPago): ?>
                                                    <option value="<?php echo $datosFormaPago["id"] ?>">
                                                        <?php echo $datosFormaPago["nombre"] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="number" class="form-control form-control-sm"
                                                placeholder="Monto inicial S/" min="0"
                                                name="montoCredito[]" id="montoSelectCredito_0" step="0.01">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="contenedorPagosCredito"></div>
                        </form>

                        <div class="text-center mt-4">
                            <button class="btn btn-warning btn-lg btn-round px-5" onclick="fn_pagar_credito()">
                                <i class="fas fa-handshake"></i> Registrar Crédito
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-round" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL GENÉRICO -->
<div class="modal fade" id="modalGenerico" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body" id="modalContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-round" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/scriptNotify.js"></script>

<script>
    // ===== CONFIGURACIÓN INICIAL =====
    const products = <?php echo json_encode(listarProductosVenta1($sucursal_id)); ?>;
    let currentPage = 1;
    const itemsPerPage = 6;
    let filteredProducts = products;

    const allCategories = [...new Set(products.map(p => p.categoria))];
    const allTypes = [...new Set(products.map(p => p.tipo))];
    const allDimensions = [...new Set(products.map(p => p.dimension))];
    const allColores = [...new Set(products.map(p => p.color))];

    // ===== INICIALIZACIÓN =====
    document.addEventListener('DOMContentLoaded', function() {
        populateFilters();
        renderPage();
        initEventListeners();
        initVueltoListeners();
    });

    // ===== POBLAR FILTROS =====
    function populateFilters() {
        const categorySelect = document.getElementById('filterCategoria');
        const typeSelect = document.getElementById('filterTipo');
        const dimensionSelect = document.getElementById('filterDimension');
        const colorSelect = document.getElementById('filterColor');

        allCategories.forEach(cat => {
            categorySelect.innerHTML += `<option value="${cat}">${cat}</option>`;
        });
        allTypes.forEach(type => {
            typeSelect.innerHTML += `<option value="${type}">${type}</option>`;
        });
        allDimensions.forEach(dim => {
            dimensionSelect.innerHTML += `<option value="${dim}">${dim}</option>`;
        });
        allColores.forEach(color => {
            colorSelect.innerHTML += `<option value="${color}">${color}</option>`;
        });
    }

    // ===== RENDERIZAR PRODUCTOS =====
    function renderProducts(productsToDisplay) {
        const container = document.getElementById('productoContainer');
        container.innerHTML = '';

        productsToDisplay.forEach(product => {
            const stock = parseFloat(product.stock);
            const sinStock = stock === 0.00;

            const row = document.createElement('tr');
            row.className = sinStock ? 'table-danger' : '';

            row.innerHTML = `
            <td class="${sinStock ? 'text-danger fw-bold' : ''}">
                <strong>${product.articulo}</strong> 
                ${sinStock ? '<span class="badge bg-danger ms-2">SIN STOCK</span>' : ''}
            </td>
            <td><span class="badge bg-info">${product.categoria}</span></td>
            <td>${product.tipo}</td>
            <td>${product.dimension}</td>
            <td>${product.color}</td>
            <td class="text-center ${sinStock ? 'text-danger fw-bold' : ''}">
                <strong>${product.stock}</strong>
            </td>
            <td class="text-end">
                <strong class="text-success">S/ ${product.precio_venta}</strong>
            </td>
            <td class="text-center">
                <button class="btn btn-success btn-sm btn-round" 
                        onclick='fn_agregar_venta(${JSON.stringify(product).replace(/'/g, "&#39;")})'
                        ${sinStock ? 'disabled' : ''}>
                    <i class="fas fa-plus"></i> Agregar
                </button>
            </td>
        `;
            container.appendChild(row);
        });

        if (productsToDisplay.length === 0) {
            container.innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <p class="mb-0">No se encontraron productos</p>
                </td>
            </tr>
        `;
        }
    }

    // ===== PAGINACIÓN =====
    function changePage(direction) {
        currentPage += direction;
        const totalPgs = totalPages();
        if (currentPage < 1) currentPage = 1;
        if (currentPage > totalPgs) currentPage = totalPgs;
        renderPage();
    }

    function totalPages() {
        return Math.ceil(filteredProducts.length / itemsPerPage);
    }

    function renderPage() {
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const productsToDisplay = filteredProducts.slice(start, end);

        renderProducts(productsToDisplay);

        document.getElementById('prevPage').disabled = currentPage === 1;
        document.getElementById('nextPage').disabled = currentPage === totalPages();
    }

    // ===== FILTRAR PRODUCTOS =====
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
                (product.articulo.toLowerCase().includes(searchText) ||
                    product.categoria.toLowerCase().includes(searchText) ||
                    product.tipo.toLowerCase().includes(searchText))
            );
        });

        currentPage = 1;
        renderPage();
    }

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

    // ===== EVENT LISTENERS =====
    function initEventListeners() {
        document.getElementById('filterCategoria').addEventListener('change', filterProducts);
        document.getElementById('filterTipo').addEventListener('change', filterProducts);
        document.getElementById('filterDimension').addEventListener('change', filterProducts);
        document.getElementById('filterColor').addEventListener('change', filterProducts);
        document.getElementById('clearFilters').addEventListener('click', clearFilters);

        initSoloCorteModal();
        initImpresion3DModal();
        initPagoModal();
        initClienteModal();
    }

    // ===== CÁLCULO DE VUELTO =====
    function initVueltoListeners() {
        // Cuando cambie el select principal de forma de pago
        document.getElementById('formaPagoSelect').addEventListener('change', detectarEfectivo);

        // Cuando cambie el monto principal (NUEVO)
        document.getElementById('montoSelect_0').addEventListener('input', detectarEfectivo);

        // Cuando se ingrese el monto con el que paga
        document.getElementById('pagaCon').addEventListener('input', calcularVuelto);

        // Cuando cambie el monto total final (con descuento)
        document.getElementById('montoTotalFinal').addEventListener('input', function() {
            if (document.getElementById('seccionVuelto').style.display === 'block') {
                actualizarTotalAPagar();
            }
        });
    }

    function detectarEfectivo() {
        let hayEfectivo = false;
        let montoEfectivo = 0;

        // Verificar el select principal y su monto
        const selectPrincipal = document.getElementById('formaPagoSelect');
        const montoPrincipal = parseFloat(document.getElementById('montoSelect_0').value) || 0;

        if (montoPrincipal > 0) {
            const textoPrincipal = selectPrincipal.options[selectPrincipal.selectedIndex].text.toUpperCase();
            if (textoPrincipal.includes('EFECTIVO')) {
                hayEfectivo = true;
                montoEfectivo += montoPrincipal;
            }
        }

        // Verificar los selects adicionales
        const contenedor = document.getElementById('contenedorPagos');
        const selectsAdicionales = contenedor.querySelectorAll('select[name^="formaPago_"]');

        selectsAdicionales.forEach((select, index) => {
            const inputMonto = select.closest('.card').querySelector('input[type="number"]');
            const monto = parseFloat(inputMonto?.value) || 0;

            if (monto > 0) {
                const texto = select.options[select.selectedIndex].text.toUpperCase();
                if (texto.includes('EFECTIVO')) {
                    hayEfectivo = true;
                    montoEfectivo += monto;
                }
            }
        });

        const seccionVuelto = document.getElementById('seccionVuelto');

        if (hayEfectivo) {
            seccionVuelto.style.display = 'block';
            actualizarTotalAPagar();
            console.log(`✅ Se detectó EFECTIVO: S/ ${montoEfectivo.toFixed(2)}`);
        } else {
            seccionVuelto.style.display = 'none';
            document.getElementById('pagaCon').value = '';
            document.getElementById('vuelto').value = '0.00';
            console.log('❌ No hay EFECTIVO seleccionado con monto');
        }
    }

    function actualizarTotalAPagar() {
        const montoFinal = parseFloat(document.getElementById('montoTotalFinal').value) ||
            parseFloat(document.getElementById('montoTotal').value);
        document.getElementById('totalAPagar').value = montoFinal.toFixed(2);
        calcularVuelto();
    }

    function calcularVuelto() {
        const totalAPagar = parseFloat(document.getElementById('totalAPagar').value) || 0;
        const pagaCon = parseFloat(document.getElementById('pagaCon').value) || 0;
        const vuelto = pagaCon - totalAPagar;

        const inputVuelto = document.getElementById('vuelto');

        if (vuelto < 0) {
            inputVuelto.value = '0.00';
            inputVuelto.classList.remove('text-success');
            inputVuelto.classList.add('text-danger');
        } else {
            inputVuelto.value = vuelto.toFixed(2);
            inputVuelto.classList.remove('text-danger');
            inputVuelto.classList.add('text-success');
        }
    }

    function setPagaCon(monto) {
        const totalAPagar = parseFloat(document.getElementById('totalAPagar').value) || 0;

        if (monto < totalAPagar) {
            document.getElementById('pagaCon').value = totalAPagar.toFixed(2);
        } else {
            document.getElementById('pagaCon').value = monto.toFixed(2);
        }

        calcularVuelto();
    }

    // ===== SOLO CORTE =====
    function initSoloCorteModal() {
        document.getElementById('btnAbrirModalSolo').addEventListener('click', function(e) {
            e.preventDefault();
            const modal = new bootstrap.Modal(document.getElementById('modalSoloCorte'));
            document.getElementById('cantidad_solocorte').value = 0;
            document.getElementById('precioSoloCorte').value = 1.5;
            modal.show();
        });

        document.getElementById('btnSumarSoloCorte').addEventListener('click', function() {
            let val = parseInt(document.getElementById('cantidad_solocorte').value);
            document.getElementById('cantidad_solocorte').value = val === 0 ? 10 : val + 1;
        });

        document.getElementById('btnRestarSoloCorte').addEventListener('click', function() {
            let val = parseInt(document.getElementById('cantidad_solocorte').value);
            if (val > 0) document.getElementById('cantidad_solocorte').value = val - 1;
        });

        ['05', '1', '2', '5'].forEach(inc => {
            document.getElementById(`btnIncremento${inc}SoloCorte`).addEventListener('click', function() {
                let precio = parseFloat(document.getElementById('precioSoloCorte').value);
                document.getElementById('precioSoloCorte').value = (precio + parseFloat(inc.replace('0', '.'))).toFixed(2);
            });
        });

        document.getElementById('btn_agregar_solocorte').addEventListener('click', agregarSoloCorte);
    }

    function agregarSoloCorte() {
        const minutos = parseInt(document.getElementById('cantidad_solocorte').value) || 0;
        const tarifa = parseFloat(document.getElementById('precioSoloCorte').value) || 0;

        if (minutos <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Error',
                text: 'Ingresa minutos válidos',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        const datos = [{
            id: '0',
            articulo: 'SOLO CORTE',
            cantidad: '-',
            precio_unitario: '-',
            subtotal: minutos * tarifa,
            idmovimiento: 6,
            minutos: minutos,
            tarifa: tarifa
        }];

        agregarATabla(datos);
        bootstrap.Modal.getInstance(document.getElementById('modalSoloCorte')).hide();
        showNotification("success");
    }

    // ===== IMPRESIÓN 3D =====
    function initImpresion3DModal() {
        document.getElementById('btnAbrirModalSolov2').addEventListener('click', function(e) {
            e.preventDefault();
            const modal = new bootstrap.Modal(document.getElementById('modalSoloCorteMaquina2'));
            document.getElementById('cantidad_solocortev2').value = 10;
            document.getElementById('precioSoloCortev2').value = 1.5;
            document.getElementById('nota_impresion').value = '';
            modal.show();
        });

        document.getElementById('btn_agregar_solocortev2').addEventListener('click', agregarImpresion3D);
    }

    function fnAumentoOrResta(accion) {
        let val = parseInt(document.getElementById('cantidad_solocortev2').value);
        document.getElementById('cantidad_solocortev2').value = accion === '+' ? val + 1 : (val > 1 ? val - 1 : val);
    }

    function fnAumentarMin(minutos) {
        document.getElementById('cantidad_solocortev2').value = minutos;
    }

    function fnAumentaPrecioImpresion(monto) {
        let precio = parseFloat(document.getElementById('precioSoloCortev2').value);
        document.getElementById('precioSoloCortev2').value = (precio + monto).toFixed(2);
    }

    function limpiar() {
        document.getElementById('precioSoloCortev2').value = 0;
    }

    function agregarImpresion3D() {
        const minutos = parseInt(document.getElementById('cantidad_solocortev2').value) || 0;
        const tarifa = parseFloat(document.getElementById('precioSoloCortev2').value) || 0;
        const nota = document.getElementById('nota_impresion').value || '';

        if (minutos <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Error',
                text: 'Ingresa minutos válidos',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        const datos = [{
            id: '0',
            articulo: 'IMPRESIÓN 3D' + (nota ? ` - ${nota}` : ''),
            cantidad: '-',
            precio_unitario: '-',
            subtotal: minutos * tarifa,
            idmovimiento: 15,
            minutos: minutos,
            tarifa: tarifa
        }];

        agregarATabla(datos);
        bootstrap.Modal.getInstance(document.getElementById('modalSoloCorteMaquina2')).hide();
        showNotification("success");
    }

    // ===== AGREGAR ARTÍCULO =====
    function fn_agregar_venta(articulo) {
        const modal = new bootstrap.Modal(document.getElementById('modalCantidad'));

        document.getElementById('nombreArticulo').textContent = `Artículo: ${articulo.articulo}`;
        document.getElementById('inputCantidad').value = 1;
        document.getElementById('cantidadCorte').value = 0;
        document.getElementById('precioCorte').value = articulo.corte ? 1.5 : 0;
        document.getElementById('idTextAreaDetalleInsert').value = '';

        const seccionCorte = document.getElementById('seccionCorte');
        seccionCorte.style.display = articulo.corte ? 'block' : 'none';

        document.getElementById('btnRestarCantidad').onclick = () => {
            let cant = parseInt(document.getElementById('inputCantidad').value);
            if (cant > 1) {
                document.getElementById('inputCantidad').value = cant - 1;
                if (cant - 1 === 1 && articulo.corte) {
                    seccionCorte.style.display = 'block';
                    document.getElementById('precioCorte').value = 1.5;
                } else if (cant - 1 > 1) {
                    seccionCorte.style.display = 'none';
                }
            }
        };

        document.getElementById('btnSumarCantidad').onclick = () => {
            let cant = parseInt(document.getElementById('inputCantidad').value);
            if (cant + 1 > articulo.stock) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stock insuficiente',
                    text: `Solo hay ${articulo.stock} unidades disponibles`,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                document.getElementById('inputCantidad').value = cant + 1;
                if (cant + 1 > 1) {
                    seccionCorte.style.display = 'none';
                    document.getElementById('cantidadCorte').value = 0;
                    document.getElementById('precioCorte').value = 0;
                }
            }
        };

        document.getElementById('btnRestarCorte').onclick = () => {
            let val = parseInt(document.getElementById('cantidadCorte').value);
            if (val > 0) document.getElementById('cantidadCorte').value = val - 1;
        };

        document.getElementById('btnSumarCorte').onclick = () => {
            let val = parseInt(document.getElementById('cantidadCorte').value);
            document.getElementById('cantidadCorte').value = val === 0 ? 10 : val + 1;
        };

        ['05', '1', '2', '5'].forEach(inc => {
            document.getElementById(`btnIncremento${inc}`).onclick = () => {
                let precio = parseFloat(document.getElementById('precioCorte').value);
                document.getElementById('precioCorte').value = (precio + parseFloat(inc.replace('0', '.'))).toFixed(2);
            };
        });

        document.getElementById('btnConfirmarCantidad').onclick = () => {
            const cantidad = parseInt(document.getElementById('inputCantidad').value);
            const minutos = parseInt(document.getElementById('cantidadCorte').value) || 0;
            const precioCorte = parseFloat(document.getElementById('precioCorte').value) || 0;
            const nota = document.getElementById('idTextAreaDetalleInsert').value;

            if (cantidad > articulo.stock) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stock insuficiente',
                    text: `Solo hay ${articulo.stock} unidades disponibles`,
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            const costoCorte = minutos * precioCorte;
            const subtotal = (cantidad * articulo.precio_venta) + costoCorte;

            const datos = [{
                id: articulo.id,
                articulo: articulo.articulo + (nota ? ` - ${nota}` : ''),
                cantidad: cantidad,
                precio_unitario: articulo.precio_venta,
                subtotal: subtotal,
                idmovimiento: 1,
                minutos: minutos || '-',
                tarifa: precioCorte || '-',
                nota: nota
            }];

            agregarATabla(datos);
            modal.hide();
            showNotification("success");
        };

        modal.show();
    }

    // ===== AGREGAR A TABLA =====
    function agregarATabla(datos) {
        const tbody = document.getElementById('tabla_articulos').getElementsByTagName('tbody')[0];

        datos.forEach(item => {
            const fila = tbody.insertRow();

            fila.insertCell(0).textContent = item.id;
            fila.insertCell(1).textContent = item.articulo;
            fila.insertCell(2).textContent = item.cantidad;
            fila.insertCell(3).textContent = item.precio_unitario;
            fila.insertCell(4).textContent = item.subtotal.toFixed(2);

            const accionCell = fila.insertCell(5);

            const btnEliminar = document.createElement('button');
            btnEliminar.className = 'btn btn-danger btn-sm btn-round';
            btnEliminar.innerHTML = '<i class="fas fa-trash"></i>';
            btnEliminar.onclick = () => {
                Swal.fire({
                    title: '¿Eliminar artículo?',
                    text: 'Esta acción no se puede deshacer',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fila.remove();
                        calcularTotales();
                        showNotification("success");
                    }
                });
            };
            accionCell.appendChild(btnEliminar);

            fila.insertCell(6).textContent = item.idmovimiento;
            fila.insertCell(7).textContent = item.nota || '';
        });

        calcularTotales();
    }

    // ===== CALCULAR TOTALES =====
    function calcularTotales() {
        const filas = document.querySelectorAll('#tabla_articulos tbody tr');
        let totalArticulos = 0;
        let totalGeneral = 0;

        filas.forEach(fila => {
            const cantidad = parseFloat(fila.cells[2].textContent) || 0;
            const precioUnit = parseFloat(fila.cells[3].textContent) || 0;
            const subtotal = parseFloat(fila.cells[4].textContent) || 0;

            totalArticulos += cantidad * precioUnit;
            totalGeneral += subtotal;
        });

        document.getElementById('id_subtotal_articulos').textContent = totalArticulos.toFixed(2);
        document.getElementById('id_subtotal_general').textContent = totalGeneral.toFixed(2);

        document.getElementById('btnRealizarPago').disabled = totalGeneral === 0;
    }

    // ===== SERVICIOS GENÉRICOS =====
    function fn_servicios(servicio) {
        const medidas = servicio.medidas.slice(1, -1).split(',');

        let html = `
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">${servicio.descripcion}</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Cantidad</label>
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-danger btn-round" onclick="ajustarCantidad('${servicio.descripcion}', -1)">-</button>
                        <input type="number" id="cant_${servicio.descripcion}" class="form-control text-center" 
                               value="1" style="width: 100px;">
                        <button class="btn btn-success btn-round" onclick="ajustarCantidad('${servicio.descripcion}', 1)">+</button>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Dimensiones</label>
                    <div class="d-flex flex-wrap gap-2 justify-content-center" id="dims_${servicio.descripcion}">
    `;

        medidas.forEach(m => {
            html += `
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="${m}" id="dim_${m}">
                <label class="form-check-label" for="dim_${m}">${m}</label>
            </div>
        `;
        });

        html += `
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Monto (S/)</label>
                    <input type="number" id="monto_${servicio.descripcion}" class="form-control" 
                           placeholder="0.00" step="0.01">
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Detalle</label>
                    <textarea id="detalle_${servicio.descripcion}" class="form-control" rows="2"></textarea>
                </div>
                
                <div class="text-center">
                    <button class="btn btn-success btn-round" onclick="agregarServicio('${servicio.descripcion}', ${servicio.id})">
                        <i class="fas fa-plus"></i> Agregar
                    </button>
                </div>
            </div>
        </div>
    `;

        document.getElementById('modalContent').innerHTML = html;
        new bootstrap.Modal(document.getElementById('modalGenerico')).show();
    }

    function ajustarCantidad(servicio, incremento) {
        const input = document.getElementById(`cant_${servicio}`);
        let val = parseInt(input.value);
        val = Math.max(1, val + incremento);
        input.value = val;
    }

    function agregarServicio(nombre, idMov) {
        const cantidad = parseInt(document.getElementById(`cant_${nombre}`).value);
        const monto = parseFloat(document.getElementById(`monto_${nombre}`).value) || 0;
        const detalle = document.getElementById(`detalle_${nombre}`).value;

        const dimensiones = Array.from(document.querySelectorAll(`#dims_${nombre} input:checked`))
            .map(cb => cb.value)
            .join(', ');

        if (monto <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Error',
                text: 'Ingresa un monto válido',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        const articulo = dimensiones ?
            `${nombre} (${dimensiones})${detalle ? ' - ' + detalle : ''}` :
            nombre + (detalle ? ' - ' + detalle : '');

        const datos = [{
            id: '0',
            articulo: articulo,
            cantidad: cantidad,
            precio_unitario: '-',
            subtotal: monto,
            idmovimiento: idMov
        }];

        agregarATabla(datos);
        bootstrap.Modal.getInstance(document.getElementById('modalGenerico')).hide();
        showNotification("success");
    }

    // ===== MODAL PAGO =====
    function initPagoModal() {
        document.getElementById('btnRealizarPago').addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('modalRealizarPago'));
            const total = document.getElementById('id_subtotal_general').textContent;

            document.getElementById('montoTotal').value = total;
            document.getElementById('idMontoVentaTitulo').textContent = total;
            document.getElementById('montoTotalFinal').value = '';

            modal.show();
        });

        let contadorPagos = 1;
        document.getElementById('btnAgregarPago').addEventListener('click', function() {
            const container = document.getElementById('contenedorPagos');
            const div = document.createElement('div');
            div.className = 'card bg-light mb-2';
            div.innerHTML = `
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-5">
                        <select class="form-select form-select-sm" name="formaPago_${contadorPagos}" 
                                onchange="detectarEfectivo()">
                            <?php foreach (listarFormaPago() as $fp): ?>
                                <option value="<?php echo $fp['id'] ?>"><?php echo $fp['nombre'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="number" class="form-control form-control-sm" name="monto_${contadorPagos}" 
                               placeholder="Monto S/" min="0" step="0.01">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm w-100" 
                                onclick="this.closest('.card').remove(); detectarEfectivo();">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
            container.appendChild(div);
            contadorPagos++;
            detectarEfectivo();
        });

        let contadorCredito = 1;
        document.getElementById('btnAgregarPagoCredito').addEventListener('click', function() {
            const container = document.getElementById('contenedorPagosCredito');
            const div = document.createElement('div');
            div.className = 'card bg-light mb-2';
            div.innerHTML = `
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-5">
                        <select class="form-select form-select-sm" name="formaPagoCredito[]">
                            <?php foreach (listarFormaPago() as $fp): ?>
                                <option value="<?php echo $fp['id'] ?>"><?php echo $fp['nombre'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="number" class="form-control form-control-sm" name="montoCredito[]" 
                               placeholder="Monto S/" min="0" step="0.01">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm w-100" 
                                onclick="this.closest('.card').remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
            container.appendChild(div);
            contadorCredito++;
        });
    }

    // ===== PROCESAR PAGO DIRECTO =====
    async function fn_pagar_directo() {
        const formData = $('#form-pago-directo').serializeArray();
        const montoOriginal = parseFloat(document.getElementById('montoTotal').value);
        const montoFinal = parseFloat(document.getElementById('montoTotalFinal').value) || montoOriginal;
        const tipoComprobante = document.querySelector('input[name="icon-input"]:checked').value;
        const idPersona = document.getElementById('idPersona').textContent.trim();

        if (tipoComprobante === 'factura' && idPersona === '#') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Para factura necesitas seleccionar un cliente',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        let formasPago = [];
        let totalPagos = 0;
        let formaPago = null;

        formData.forEach(item => {
            if (item.name.startsWith('formaPago')) {
                formaPago = item.value;
            } else if (item.name.startsWith('monto')) {
                const monto = parseFloat(item.value);
                if (formaPago && monto > 0) {
                    formasPago.push({
                        id_forma_pago: formaPago,
                        monto_forma_pago: monto
                    });
                    totalPagos += monto;
                    formaPago = null;
                }
            }
        });

        if (formasPago.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debes agregar al menos una forma de pago',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        if (totalPagos !== montoFinal) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Los montos ingresados no coinciden con el total',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        // VALIDAR VUELTO SI HAY EFECTIVO
        const seccionVuelto = document.getElementById('seccionVuelto');
        if (seccionVuelto.style.display === 'block') {
            const pagaCon = parseFloat(document.getElementById('pagaCon').value) || 0;
            const vuelto = parseFloat(document.getElementById('vuelto').value) || 0;

            if (pagaCon === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atención',
                    text: 'Ingresa el monto con el que paga el cliente',
                    timer: 2000,
                    showConfirmButton: false
                });
                document.getElementById('pagaCon').focus();
                return;
            }

            if (vuelto < 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Monto insuficiente',
                    text: 'El cliente debe pagar con un monto mayor o igual al total',
                    timer: 2000,
                    showConfirmButton: false
                });
                document.getElementById('pagaCon').focus();
                return;
            }

            // Mostrar confirmación con el vuelto
            if (vuelto > 0) {
                const resultado = await Swal.fire({
                    title: 'Confirmar Vuelto',
                    html: `
                    <div class="text-start">
                        <p><strong>Total:</strong> S/ ${montoFinal.toFixed(2)}</p>
                        <p><strong>Paga con:</strong> S/ ${pagaCon.toFixed(2)}</p>
                        <p class="text-success fs-4"><strong>Vuelto:</strong> S/ ${vuelto.toFixed(2)}</p>
                    </div>
                `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Confirmar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#28a745'
                });

                if (!resultado.isConfirmed) {
                    return;
                }
            }
        }

        const venta = {
            tipo_comprobante: tipoComprobante,
            usuario_id: <?php echo $_SESSION['id']; ?>,
            cliente_id: idPersona === '#' ? 9897 : parseInt(idPersona),
            monto_original: montoOriginal,
            monto_venta_final: montoFinal
        };

        const articulos = obtenerArticulos();

        $.ajax({
            url: 'logica/clssInsertPA.php',
            type: 'POST',
            data: {
                accion: 'FINALIZARVENTARAPIDO',
                jsDatosVenta: JSON.stringify(venta),
                js_articulos: JSON.stringify(articulos),
                js_detalle_pago: JSON.stringify(formasPago)
            },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.estado) {
                        Swal.fire({
                            title: 'Venta Exitosa',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.open(`ticket.php?id=${result.id_venta_generado}`, '_blank');
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: result.mensaje
                        });
                    }
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al procesar la respuesta'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error en la comunicación con el servidor'
                });
            }
        });
    }

    // ===== PROCESAR PAGO CRÉDITO =====
    function fn_pagar_credito() {
        const idPersona = document.getElementById('idPersona').textContent.trim();

        if (idPersona === '#') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debes seleccionar un cliente para venta a crédito',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        const formData = $('#form-pago-credito').serializeArray();
        const montoOriginal = parseFloat(document.getElementById('montoTotal').value);
        const montoFinal = parseFloat(document.getElementById('montoTotalFinal').value) || montoOriginal;

        let detalleDeuda = [];
        let montoInicial = 0;
        let formaPago = null;

        formData.forEach(item => {
            if (item.name === 'formaPagoCredito[]') {
                formaPago = item.value;
            } else if (item.name === 'montoCredito[]') {
                const monto = parseFloat(item.value) || 0;
                if (formaPago && monto > 0) {
                    detalleDeuda.push({
                        id_forma_pago: formaPago,
                        monto_forma_pago: monto
                    });
                    montoInicial += monto;
                    formaPago = null;
                }
            }
        });

        const venta = {
            usuario_id: <?php echo $_SESSION['id']; ?>,
            cliente_id: parseInt(idPersona),
            monto_original: montoOriginal,
            monto_venta_final: montoFinal,
            monto_inicial: montoInicial
        };

        const articulos = obtenerArticulos();

        $.ajax({
            url: 'logica/clssInsertPA.php',
            type: 'POST',
            data: {
                accion: 'FINALIZARVENTACREDITORAPIDO',
                jsDatosVenta: JSON.stringify(venta),
                js_articulos: JSON.stringify(articulos),
                js_detalle_deuda: detalleDeuda.length > 0 ? JSON.stringify(detalleDeuda) : null
            },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.estado) {
                        Swal.fire({
                            title: 'Venta a Crédito Registrada',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.open(`ticket.php?id=${result.id_venta_generado}`, '_blank');
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: result.mensaje
                        });
                    }
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al procesar la respuesta'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error en la comunicación con el servidor'
                });
            }
        });
    }

    // ===== OBTENER ARTÍCULOS =====
    function obtenerArticulos() {
        const filas = document.querySelectorAll('#tabla_articulos tbody tr');
        const articulos = [];

        filas.forEach(fila => {
            const art = {
                articulo_id: fila.cells[0].textContent === '0' ? null : parseInt(fila.cells[0].textContent),
                minutos: null,
                costoxminuto: null,
                precio_unitario: isNaN(parseFloat(fila.cells[3].textContent)) ? null : parseFloat(fila.cells[3].textContent),
                cantidad: isNaN(parseInt(fila.cells[2].textContent)) ? null : parseInt(fila.cells[2].textContent),
                sub_total: parseFloat(fila.cells[4].textContent),
                movimiento_id: parseInt(fila.cells[6].textContent),
                nota_archivo: fila.cells[1].textContent + (fila.cells[7].textContent ? ' / ' + fila.cells[7].textContent : '')
            };
            articulos.push(art);
        });

        return articulos;
    }

    // ===== CLIENTE MODAL =====
    function initClienteModal() {
        document.getElementById('btnAbrirModalCliente').addEventListener('click', function() {
            new bootstrap.Modal(document.getElementById('modalCliente')).show();
        });

        document.getElementById('btnBuscarDNI').addEventListener('click', buscarDNI);
        document.getElementById('btnBuscarRUC').addEventListener('click', buscarRUC);
        document.getElementById('btnRegistrarCliente').addEventListener('click', registrarCliente);
        document.getElementById('nombreCliente').addEventListener('input', buscarCliente);
    }

    async function buscarDNI() {
        const dni = document.getElementById('numeroDocumentoPersona').value.trim();

        if (dni.length !== 8) {
            Swal.fire({
                icon: 'warning',
                title: 'DNI inválido',
                text: 'Ingresa un DNI de 8 dígitos',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        try {
            const response = await fetch(`https://graphperu.daustinn.com/api/query/${dni}`);
            const data = await response.json();

            if (data && data.names) {
                document.getElementById('nombresPersona').value = data.names;
                document.getElementById('apellidosPersona').value = data.surnames;

                Swal.fire({
                    icon: 'success',
                    title: 'DNI encontrado',
                    text: data.fullName,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        } catch (e) {
            Swal.fire({
                icon: 'warning',
                title: 'No encontrado',
                text: 'Ingresa los datos manualmente',
                timer: 2000,
                showConfirmButton: false
            });
        }
    }

    async function buscarRUC() {
        const ruc = document.getElementById('numeroDocumentoEmpresa').value.trim();

        if (ruc.length !== 11) {
            Swal.fire({
                icon: 'warning',
                title: 'RUC inválido',
                text: 'Ingresa un RUC de 11 dígitos',
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        try {
            const response = await fetch(`https://graphperu.daustinn.com/api/query/${ruc}`);
            const data = await response.json();

            if (data && data.name) {
                document.getElementById('razonSocial').value = data.name;
                document.getElementById('nombreComercial').value = data.name;

                Swal.fire({
                    icon: 'success',
                    title: 'RUC encontrado',
                    text: data.name,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        } catch (e) {
            Swal.fire({
                icon: 'warning',
                title: 'No encontrado',
                text: 'Ingresa los datos manualmente',
                timer: 2000,
                showConfirmButton: false
            });
        }
    }

    function buscarCliente() {
        const query = document.getElementById('nombreCliente').value.trim();
        const sugerencias = document.getElementById('sugerencias');

        if (query.length === 0) {
            sugerencias.innerHTML = '';
            return;
        }

        $.ajax({
            method: 'POST',
            url: 'logica/clssFiltro.php',
            data: {
                accion: 'FILTROPERSONA',
                data: query
            },
            success: function(response) {
                try {
                    const resultados = JSON.parse(response);
                    sugerencias.innerHTML = '';

                    resultados.forEach(persona => {
                        const item = document.createElement('div');
                        item.className = 'list-group-item list-group-item-action';
                        item.textContent = persona.persona_concatenada;
                        item.style.cursor = 'pointer';

                        item.onclick = () => {
                            document.getElementById('nombreCliente').value = persona.persona_concatenada;
                            document.getElementById('idPersona').textContent = persona.id;
                            document.getElementById('idUpdateNumTelefonoCliente').value = persona.telefonomovil || '';
                            document.getElementById('idUpdateCorreoCliente').value = persona.email || '';
                            sugerencias.innerHTML = '';
                        };

                        sugerencias.appendChild(item);
                    });
                } catch (e) {
                    console.error('Error:', e);
                }
            }
        });
    }

    function registrarCliente() {
        const esPersona = document.getElementById('pills-persona-tab').classList.contains('active');

        if (esPersona) {
            const dni = document.getElementById('numeroDocumentoPersona').value.trim();
            const nombres = document.getElementById('nombresPersona').value.trim();
            const apellidos = document.getElementById('apellidosPersona').value.trim();

            if (!dni || !nombres || !apellidos) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos requeridos',
                    text: 'Completa los campos obligatorios',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            const datos = {
                numero_documento: dni,
                nombres: nombres,
                apellidos: apellidos,
                telefono_movil: document.getElementById('telefonoPersona').value || null,
                email: document.getElementById('emailPersona').value || null
            };

            registrarPersona(datos);
        } else {
            const ruc = document.getElementById('numeroDocumentoEmpresa').value.trim();
            const razon = document.getElementById('razonSocial').value.trim();
            const comercial = document.getElementById('nombreComercial').value.trim();

            if (!ruc || !razon || !comercial) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos requeridos',
                    text: 'Completa los campos obligatorios',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            const datos = {
                numero_documento: ruc,
                razon_social: razon,
                nombre_comercial: comercial,
                telefono_movil: document.getElementById('telefonoEmpresa').value || null,
                email: document.getElementById('emailEmpresa').value || null
            };

            registrarPersona(datos);
        }
    }

    function registrarPersona(datos) {
        $.ajax({
            method: 'POST',
            url: 'logica/clssPersona.php',
            data: {
                accion: 'REGISTRARPERSONARAPIDO',
                data: JSON.stringify(datos)
            },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.success) {
                        const id = result.persona_id || result.empresa_id;
                        const nombre = datos.nombres ?
                            `${datos.numero_documento} - ${datos.nombres} ${datos.apellidos}` :
                            `${datos.numero_documento} - ${datos.razon_social}`;

                        document.getElementById('idPersona').textContent = id;
                        document.getElementById('nombreCliente').value = nombre;

                        bootstrap.Modal.getInstance(document.getElementById('modalCliente')).hide();

                        Swal.fire({
                            icon: 'success',
                            title: 'Cliente registrado',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: result.message
                        });
                    }
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al procesar la respuesta'
                    });
                }
            }
        });
    }
</script>

<?php include("pie.php"); ?>