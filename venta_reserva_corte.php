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
        --card-shadow: 0 2px 12px rgba(0,0,0,0.08);
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
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
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
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .card-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
    }

    /* ===== TARJETAS DE PRODUCTOS ===== */
    .card-post {
        transition: var(--transition);
    }

    .card-post:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
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

    /* ===== SUGERENCIAS AUTOCOMPLETE ===== */
    #sugerencias {
        max-height: 200px;
        overflow-y: auto;
        z-index: 1050;
    }

    #sugerencias .list-group-item {
        cursor: pointer;
        transition: var(--transition);
    }

    #sugerencias .list-group-item:hover {
        background-color: #f0f8ff;
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

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .card-stats {
            margin-bottom: 1rem;
        }
        
        .pagination {
            font-size: 12px;
        }
        
        .pagination button {
            padding: 6px 10px;
        }
    }

    
</style>

<div class="container">
    <div class="page-inner">
        <!-- HEADER PRINCIPAL -->
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <i class="fas fa-bookmark"></i> Venta Por Reserva
                </h4>
                <p class="text-muted">Registra los pedidos que te envían por WhatsApp. Solo reservas para recoger después.</p>

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
                <div class="card mt-4">
                    <div class="card-body">
                        <!-- Fila de filtros -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <select id="filterCategoria" class="form-select">
                                    <option value="">Filtrar por Categoría</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="filterTipo" class="form-select">
                                    <option value="">Filtrar por Tipo</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="filterDimension" class="form-select">
                                    <option value="">Filtrar por Dimensión</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select id="filterColor" class="form-select">
                                    <option value="">Filtrar por Color</option>
                                </select>
                            </div>
                        </div>

                        <!-- Barra de búsqueda y botón limpiar -->
                        <div class="row g-3">
                            <div class="col-md-10">
                                <input type="text" id="searchInput" class="form-control"
                                    placeholder="Buscar Articulo..." onkeyup="filterProducts()">
                            </div>
                            <div class="col-md-2">
                                <button id="clearFilters" class="btn btn-warning w-100">
                                    <i class="fas fa-broom"></i> Limpiar Filtros
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PRODUCTOS EN TARJETAS -->
                <div class="container mt-4">
                    <div class="row" id="productoContainer">
                        <!-- Los productos se generarán dinámicamente aquí -->
                    </div>
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
                <h6 class="mb-0"><i class="fas fa-list"></i> Detalle de Reserva</h6>
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

        <!-- TOTALES Y BOTÓN DE RESERVA -->
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
                    <button id="btnRealizarReserva" disabled type="button" 
                            class="btn btn-success btn-lg btn-round px-5">
                        <i class="fas fa-bookmark"></i> Realizar Reserva
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

<!-- MODAL: REALIZAR RESERVA -->
<div class="modal fade" id="modalRealizarPago" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="w-100 text-center">
                    <h4 class="mb-1"><i class="fas fa-bookmark"></i> Realizar Reserva</h4>
                    <h2 class="text-success mb-0">S/ <span id="idMontoVentaTitulo">0.00</span></h2>
                </div>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <small class="text-muted">
                        ID Cliente: <span id="idPersona">#</span>
                    </small>
                </div>

                <div class="mb-3 position-relative">
                    <label for="nombreCliente" class="form-label">
                        <i class="fas fa-user-tie"></i> <strong>Cliente</strong>
                    </label>
                    <div class="d-flex align-items-center">
                        <input type="text" class="form-control" id="nombreCliente" 
                               placeholder="Buscar cliente por nombre o DNI">
                        <button type="button" class="btn btn-primary ms-2 btn-round" id="btnAbrirModalCliente">
                            <i class="fas fa-user-plus"></i>
                        </button>
                    </div>
                    <div id="sugerencias" class="list-group position-absolute w-100"></div>
                </div>

                <div class="mb-3">
                    <label for="montoTotal" class="form-label"><strong>Monto Total (S/)</strong></label>
                    <div class="input-group">
                        <span class="input-group-text">S/</span>
                        <input type="text" class="form-control" id="montoTotal" readonly>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button class="btn btn-success btn-lg btn-round px-5" id="Reservar">
                        <i class="fas fa-bookmark"></i> Confirmar Reserva
                    </button>
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
const itemsPerPage = 8;
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
        
        const card = document.createElement('div');
        card.classList.add('col-6', 'col-md-3', 'mb-3');
        card.innerHTML = `
            <div class="card card-post card-round h-100 ${sinStock ? 'border-danger' : ''}">
                <div class="card-body p-2">
                    <h6 class="fw-bold mb-1 small ${sinStock ? 'text-danger' : ''}">${product.articulo}</h6>
                    ${sinStock ? '<span class="badge bg-danger badge-sm mb-1">SIN STOCK</span>' : ''}
                    
                    <p class="text-primary mb-1" style="font-size: 0.7rem;">${product.categoria}</p>
                    
                    <div style="font-size: 0.65rem;" class="text-muted mb-1">
                        <div><b>Tipo:</b> ${product.tipo}</div>
                        <div><b>Dim:</b> ${product.dimension}</div>
                        <div><b>Color:</b> ${product.color}</div>
                        <div><b>Stock:</b> <span class="${sinStock ? 'text-danger fw-bold' : ''}">${product.stock}</span></div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="fw-bold text-success" style="font-size: 0.9rem;">S/${product.precio_venta}</span>
                        <button class="btn btn-success btn-sm py-1 px-2" 
                                onclick='fn_agregar_venta(${JSON.stringify(product).replace(/'/g, "&#39;")})'
                                ${sinStock ? 'disabled' : ''}>
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(card);
    });

    if (productsToDisplay.length === 0) {
        container.innerHTML = `
            <div class="col-12 text-center text-muted py-4">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <p class="mb-0">No se encontraron productos</p>
            </div>
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
    initClienteModal();
    initReservaModal();
}

// ===== FUNCIONES DE IMPRESIÓN 3D =====
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

</script>

<script>
// ===== MODAL SOLO CORTE =====
function initSoloCorteModal() {
    const btnAbrir = document.getElementById('btnAbrirModalSolo');
    const btnAgregar = document.getElementById('btn_agregar_solocorte');
    const btnSumar = document.getElementById('btnSumarSoloCorte');
    const btnRestar = document.getElementById('btnRestarSoloCorte');
    const btnInc05 = document.getElementById('btnIncremento05SoloCorte');
    const btnInc1 = document.getElementById('btnIncremento1SoloCorte');
    const btnInc2 = document.getElementById('btnIncremento2SoloCorte');
    const btnInc5 = document.getElementById('btnIncremento5SoloCorte');

    btnSumar.addEventListener('click', () => {
        let cantidad = parseInt(document.getElementById('cantidad_solocorte').value);
        document.getElementById('cantidad_solocorte').value = cantidad === 0 ? 10 : cantidad + 1;
    });

    btnRestar.addEventListener('click', () => {
        let cantidad = parseInt(document.getElementById('cantidad_solocorte').value);
        if (cantidad > 0) document.getElementById('cantidad_solocorte').value = cantidad - 1;
    });

    btnInc05.addEventListener('click', () => {
        let precio = parseFloat(document.getElementById('precioSoloCorte').value);
        document.getElementById('precioSoloCorte').value = (precio + 0.5).toFixed(2);
    });

    btnInc1.addEventListener('click', () => {
        let precio = parseFloat(document.getElementById('precioSoloCorte').value);
        document.getElementById('precioSoloCorte').value = (precio + 1).toFixed(2);
    });

    btnInc2.addEventListener('click', () => {
        let precio = parseFloat(document.getElementById('precioSoloCorte').value);
        document.getElementById('precioSoloCorte').value = (precio + 2).toFixed(2);
    });

    btnInc5.addEventListener('click', () => {
        let precio = parseFloat(document.getElementById('precioSoloCorte').value);
        document.getElementById('precioSoloCorte').value = (precio + 5).toFixed(2);
    });

    btnAbrir.addEventListener('click', () => {
        document.getElementById('cantidad_solocorte').value = 0;
        document.getElementById('precioSoloCorte').value = 1.5;
        
        const modal = new bootstrap.Modal(document.getElementById('modalSoloCorte'));
        modal.show();
    });

    btnAgregar.addEventListener('click', () => {
        const minutos = parseInt(document.getElementById('cantidad_solocorte').value) || 0;
        const tarifa = parseFloat(document.getElementById('precioSoloCorte').value) || 0;
        const inputMonto = document.getElementById('cantidad_solocorte');
        
        // Limpiar errores previos
        const mensajeErrorExistente = document.querySelector('.error-message');
        if (mensajeErrorExistente) mensajeErrorExistente.remove();
        inputMonto.classList.remove('error-input');

        if (minutos <= 0) {
            inputMonto.classList.add('error-input');
            const divContainer = inputMonto.closest('.d-flex');
            const mensajeError = `<div class="error-message text-center mt-2">Por favor, ingresa un monto válido mayor a 0.</div>`;
            divContainer.insertAdjacentHTML('afterend', mensajeError);
            return;
        }

        const datosCorte = [{
            id: '0',
            minutos: minutos,
            tarifa: tarifa,
            costo: minutos * tarifa,
            articulo: 'SOLO CORTE',
            idmovimiento: 6,
            cantidad: '-',
            precio_unitario: '-',
            nota: ''
        }];

        fn_agregar_servicio_tabla(datosCorte);
        
        document.getElementById('cantidad_solocorte').value = 0;
        document.getElementById('precioSoloCorte').value = 1.5;
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalSoloCorte'));
        modal.hide();
        
        showNotification("success");
    });
}

// ===== MODAL IMPRESIÓN 3D =====
function initImpresion3DModal() {
    const btnAbrir = document.getElementById('btnAbrirModalSolov2');
    const btnAgregar = document.getElementById('btn_agregar_solocortev2');

    btnAbrir.addEventListener('click', () => {
        document.getElementById('cantidad_solocortev2').value = 10;
        document.getElementById('precioSoloCortev2').value = 1.5;
        document.getElementById('nota_impresion').value = '';
        
        const modal = new bootstrap.Modal(document.getElementById('modalSoloCorteMaquina2'));
        modal.show();
    });

    btnAgregar.addEventListener('click', () => {
        const minutos = parseInt(document.getElementById('cantidad_solocortev2').value) || 0;
        const tarifa = parseFloat(document.getElementById('precioSoloCortev2').value) || 0;
        const nota = document.getElementById('nota_impresion').value;
        const inputMonto = document.getElementById('cantidad_solocortev2');
        
        const mensajeErrorExistente = document.querySelector('.error-message');
        if (mensajeErrorExistente) mensajeErrorExistente.remove();
        inputMonto.classList.remove('error-input');

        if (minutos <= 0) {
            inputMonto.classList.add('error-input');
            const divContainer = inputMonto.closest('.d-flex');
            const mensajeError = `<div class="error-message text-center mt-2">Por favor, ingresa minutos válidos mayores a 0.</div>`;
            divContainer.insertAdjacentHTML('afterend', mensajeError);
            return;
        }

        const datosImpresion = [{
            id: '0',
            minutos: minutos,
            tarifa: tarifa,
            costo: minutos * tarifa,
            articulo: 'IMPRESIÓN 3D',
            idmovimiento: 15,
            cantidad: '-',
            precio_unitario: '-',
            nota: nota || ''
        }];

        fn_agregar_servicio_tabla(datosImpresion);
        
        document.getElementById('cantidad_solocortev2').value = 10;
        document.getElementById('precioSoloCortev2').value = 1.5;
        document.getElementById('nota_impresion').value = '';
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalSoloCorteMaquina2'));
        modal.hide();
        
        showNotification("success");
    });
}

// ===== AGREGAR ARTÍCULO =====
function fn_agregar_venta(datosArticulo) {
    const modalCantidad = new bootstrap.Modal(document.getElementById('modalCantidad'));
    const nombreArticulo = document.getElementById('nombreArticulo');
    nombreArticulo.textContent = `Artículo: ${datosArticulo.articulo || "Sin nombre"}`;

    const inputCantidad = document.getElementById('inputCantidad');
    const seccionCorte = document.getElementById('seccionCorte');
    const cantidadCorte = document.getElementById('cantidadCorte');
    const precioCorte = document.getElementById('precioCorte');
    const textArea = document.getElementById('idTextAreaDetalleInsert');

    inputCantidad.value = 1;
    cantidadCorte.value = 0;
    precioCorte.value = 1.5;
    textArea.value = '';

    const mensajeErrorExistente = document.querySelector('.error-message');
    if (mensajeErrorExistente) mensajeErrorExistente.remove();
    cantidadCorte.classList.remove('error-input');

    if (datosArticulo.corte) {
        seccionCorte.style.display = 'block';
    } else {
        seccionCorte.style.display = 'none';
    }

    // Botones de cantidad
    document.getElementById('btnRestarCantidad').onclick = () => {
        let cantidad = parseInt(inputCantidad.value, 10);
        if (cantidad > 1) {
            inputCantidad.value = cantidad - 1;
        }
        if (inputCantidad.value == 1 && datosArticulo.corte) {
            precioCorte.value = 1.5;
            seccionCorte.style.display = 'block';
        } else if (inputCantidad.value > 1) {
            precioCorte.value = 0;
            cantidadCorte.value = 0;
            seccionCorte.style.display = 'none';
        }
    };

    document.getElementById('btnSumarCantidad').onclick = () => {
        let cantidad = parseInt(inputCantidad.value, 10) + 1;
        let cantidadStock = datosArticulo.stock;

        if (cantidad > cantidadStock) {
            Swal.fire({
                icon: 'warning',
                title: 'Stock insuficiente',
                text: `No puedes agregar más de ${cantidadStock} unidades.`,
                confirmButtonText: 'Entendido'
            });
        } else {
            inputCantidad.value = cantidad;
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

    // Botones de corte
    document.getElementById('btnRestarCorte').onclick = () => {
        let corte = parseInt(cantidadCorte.value, 10);
        if (corte > 0) cantidadCorte.value = corte - 1;
    };

    document.getElementById('btnSumarCorte').onclick = () => {
        let corte = parseInt(cantidadCorte.value, 10);
        cantidadCorte.value = corte === 0 ? 10 : corte + 1;
    };

    // Botones de incremento de precio
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

    // Confirmar cantidad
    document.getElementById('btnConfirmarCantidad').onclick = () => {
        let cantidadSeleccionada = parseInt(inputCantidad.value, 10);
        let cantidadStock = datosArticulo.stock;

        if (cantidadSeleccionada > cantidadStock) {
            Swal.fire({
                icon: 'warning',
                title: 'Stock insuficiente',
                text: `Solo hay ${cantidadStock} unidades disponibles.`,
                confirmButtonText: 'Entendido'
            });
            return;
        }

        datosArticulo.cantidad = cantidadSeleccionada;
        datosArticulo.minutos = parseInt(cantidadCorte.value, 10) || 0;
        datosArticulo.costo_por_minuto = datosArticulo.minutos > 0 ? parseFloat(precioCorte.value, 10) : 0;
        datosArticulo.id_movimiento = 1;
        datosArticulo.nota = textArea.value || '';

        if (datosArticulo.corte && cantidadSeleccionada === 1 && datosArticulo.minutos <= 0) {
            const inputMonto = document.getElementById('cantidadCorte');
            inputMonto.classList.add('error-input');
            const divContainer = inputMonto.closest('.d-flex');
            const mensajeError = `<div class="error-message text-center mt-2">Por favor, ingresa minutos de corte válidos.</div>`;
            divContainer.insertAdjacentHTML('afterend', mensajeError);
            return;
        }

        modalCantidad.hide();
        fn_agregar_articulo_tabla(datosArticulo);
        showNotification("success");
    };

    modalCantidad.show();
}

// ===== AGREGAR ARTÍCULO A TABLA =====
function fn_agregar_articulo_tabla(datosArticulo) {
    const tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];
    const nuevaFila = tabla.insertRow();

    const totalCorte = (datosArticulo.costo_por_minuto * datosArticulo.minutos) || 0;
    const subtotal = (datosArticulo.cantidad * datosArticulo.precio_venta) + totalCorte;

    nuevaFila.insertCell(0).textContent = datosArticulo.id;
    nuevaFila.insertCell(1).textContent = datosArticulo.articulo;
    nuevaFila.insertCell(2).textContent = datosArticulo.cantidad;
    nuevaFila.insertCell(3).textContent = datosArticulo.precio_venta;
    nuevaFila.insertCell(4).textContent = subtotal.toFixed(2);
    
    const accionCell = nuevaFila.insertCell(5);
    nuevaFila.insertCell(6).textContent = datosArticulo.id_movimiento;
    nuevaFila.insertCell(7).textContent = datosArticulo.nota;

    // Botón Editar
    const botonEditar = document.createElement("button");
    botonEditar.classList.add("btn", "btn-warning", "btn-sm", "btn-round", "me-1");
    botonEditar.innerHTML = '<i class="fas fa-edit"></i>';
    accionCell.appendChild(botonEditar);

    botonEditar.addEventListener("click", () => {
        document.getElementById("nombreArticulo").textContent = datosArticulo.articulo;
        document.getElementById("inputCantidad").value = datosArticulo.cantidad;
        document.getElementById("idTextAreaDetalleInsert").value = datosArticulo.nota;

        const seccionCorte = document.getElementById("seccionCorte");
        if (datosArticulo.corte && datosArticulo.cantidad === 1) {
            document.getElementById("cantidadCorte").value = datosArticulo.minutos || 0;
            document.getElementById("precioCorte").value = datosArticulo.costo_por_minuto || 1.5;
            seccionCorte.style.display = "block";
        } else {
            seccionCorte.style.display = "none";
        }

        document.getElementById("btnConfirmarCantidad").onclick = function() {
            const cantidad = parseInt(document.getElementById("inputCantidad").value);
            const minutos = parseInt(document.getElementById("cantidadCorte").value) || 0;
            const precio = parseFloat(document.getElementById("precioCorte").value) || 0;

            datosArticulo.cantidad = cantidad;
            datosArticulo.minutos = minutos;
            datosArticulo.costo_por_minuto = precio;
            datosArticulo.nota = document.getElementById("idTextAreaDetalleInsert").value;

            const totalCorte = (precio * minutos) || 0;
            const subtotal = (cantidad * datosArticulo.precio_venta) + totalCorte;

            nuevaFila.cells[2].textContent = cantidad;
            nuevaFila.cells[4].textContent = subtotal.toFixed(2);
            nuevaFila.cells[7].textContent = datosArticulo.nota;

            const modal = bootstrap.Modal.getInstance(document.getElementById('modalCantidad'));
            modal.hide();
            fn_obtener_total();
        };

        const modal = new bootstrap.Modal(document.getElementById('modalCantidad'));
        modal.show();
    });

    // Botón Eliminar
    const botonEliminar = document.createElement("button");
    botonEliminar.classList.add("btn", "btn-danger", "btn-sm", "btn-round");
    botonEliminar.innerHTML = '<i class="fas fa-trash"></i>';
    accionCell.appendChild(botonEliminar);

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
                nuevaFila.remove();
                fn_obtener_total();
                showNotification("success");
            }
        });
    });

    fn_obtener_total();
}

// ===== AGREGAR SERVICIO A TABLA =====
function fn_agregar_servicio_tabla(datosServicio) {
    const tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];

    datosServicio.forEach(servicio => {
        const nuevaFila = tabla.insertRow();
        const subtotal = servicio.costo || servicio.subtotal || 0;

        nuevaFila.insertCell(0).textContent = servicio.id;
        nuevaFila.insertCell(1).textContent = servicio.articulo;
        nuevaFila.insertCell(2).textContent = servicio.cantidad || '-';
        nuevaFila.insertCell(3).textContent = servicio.precio_unitario || '-';
        nuevaFila.insertCell(4).textContent = subtotal.toFixed(2);
        
        const accionCell = nuevaFila.insertCell(5);
        nuevaFila.insertCell(6).textContent = servicio.idmovimiento;
        nuevaFila.insertCell(7).textContent = servicio.nota || '';

        // Botón Editar
        const botonEditar = document.createElement("button");
        botonEditar.classList.add("btn", "btn-warning", "btn-sm", "btn-round", "me-1");
        botonEditar.innerHTML = '<i class="fas fa-edit"></i>';
        accionCell.appendChild(botonEditar);

        botonEditar.addEventListener("click", () => {
            // Lógica de edición según el tipo de servicio
            Swal.fire({
                icon: 'info',
                title: 'Edición de servicio',
                text: 'Funcionalidad de edición en desarrollo'
            });
        });

        // Botón Eliminar
        const botonEliminar = document.createElement("button");
        botonEliminar.classList.add("btn", "btn-danger", "btn-sm", "btn-round");
        botonEliminar.innerHTML = '<i class="fas fa-trash"></i>';
        accionCell.appendChild(botonEliminar);

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
                    nuevaFila.remove();
                    fn_obtener_total();
                    showNotification("success");
                }
            });
        });
    });

    fn_obtener_total();
}

// ===== SERVICIOS GENÉRICOS =====
function fn_servicios(jsDatos) {
    const medidasArray = jsDatos.medidas.slice(1, -1).split(',');
    
    let modalContent = `
        <div class="text-center">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Servicio de ${jsDatos.descripcion}</h4>
                    <p class="text-muted">ID Movimiento: ${jsDatos.id}</p>
                </div>
                
                <div class="card-body">
                    <label class="form-label fw-bold">Cantidad</label>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <button id="btn_menos_${jsDatos.descripcion}" class="btn btn-danger btn-round btn-sm">-</button>
                        <input id="input_cantidad_${jsDatos.descripcion}" class="form-control text-center" type="number" value="1" style="width: 80px;">
                        <button id="btn_mas_${jsDatos.descripcion}" class="btn btn-success btn-round btn-sm">+</button>
                    </div>
                </div>

                <div class="card-body">
                    <label class="form-label fw-bold">Dimensión</label>
                    <div class="selectgroup selectgroup-pills">`;

    medidasArray.forEach(elemento => {
        modalContent += `
            <label class="selectgroup-item">
                <input type="checkbox" name="dimension" value="${elemento}" class="selectgroup-input" />
                <span class="selectgroup-button">${elemento}</span>
            </label>`;
    });

    modalContent += `
                    </div>
                </div>
                
                <div class="card-body">
                    <label class="form-label fw-bold">Monto (S/)</label>
                    <input type="number" id="monto_${jsDatos.descripcion}" class="form-control" placeholder="Ingrese monto">
                </div>

                <div class="card-body">
                    <label class="form-label fw-bold">Detalle</label>
                    <textarea id="detalle_${jsDatos.descripcion}" class="form-control" rows="3" 
                              placeholder="Observaciones adicionales..."></textarea>
                </div>

                <div class="text-center mb-3">
                    <button class="btn btn-success btn-round" id="btnAgregar${jsDatos.descripcion}">
                        <i class="fas fa-plus"></i> Añadir
                    </button>
                </div>
            </div>
        </div>`;

    document.getElementById('modalContent').innerHTML = modalContent;

    setTimeout(() => {
        document.getElementById(`btn_mas_${jsDatos.descripcion}`).addEventListener('click', () => {
            let cantidad = parseInt(document.getElementById(`input_cantidad_${jsDatos.descripcion}`).value);
            document.getElementById(`input_cantidad_${jsDatos.descripcion}`).value = cantidad + 1;
        });

        document.getElementById(`btn_menos_${jsDatos.descripcion}`).addEventListener('click', () => {
            let cantidad = parseInt(document.getElementById(`input_cantidad_${jsDatos.descripcion}`).value);
            if (cantidad > 1) document.getElementById(`input_cantidad_${jsDatos.descripcion}`).value = cantidad - 1;
        });

        document.getElementById(`btnAgregar${jsDatos.descripcion}`).addEventListener('click', () => {
            const cantidad = parseInt(document.getElementById(`input_cantidad_${jsDatos.descripcion}`).value) || 1;
            const monto = parseFloat(document.getElementById(`monto_${jsDatos.descripcion}`).value) || 0;
            const detalle = document.getElementById(`detalle_${jsDatos.descripcion}`).value;
            
            let dimensionesSeleccionadas = [];
            document.querySelectorAll('.selectgroup-input:checked').forEach(checkbox => {
                dimensionesSeleccionadas.push(checkbox.value);
            });
            const textoDimensiones = dimensionesSeleccionadas.join(", ");

            if (monto <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Monto inválido',
                    text: 'Por favor ingresa un monto mayor a 0'
                });
                return;
            }

            const datos = [{
                id: '0',
                descripcion: jsDatos.descripcion,
                cantidad: cantidad,
                subtotal: monto,
                articulo: textoDimensiones ? `${jsDatos.descripcion} (${textoDimensiones})` : jsDatos.descripcion,
                idmovimiento: jsDatos.id,
                dimension: textoDimensiones,
                nota: detalle,
                precio_unitario: '-'
            }];

            fn_agregar_servicio_tabla(datos);
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalGenerico'));
            if (modal) modal.hide();
            showNotification("success");
        });
    }, 0);

    const modal = new bootstrap.Modal(document.getElementById('modalGenerico'));
    modal.show();
}

// ===== CALCULAR TOTALES =====
function fn_obtener_total() {
    const tabla = document.getElementById("tabla_articulos").getElementsByTagName("tbody")[0];
    const filas = tabla.getElementsByTagName("tr");
    
    let total = 0;
    for (let i = 0; i < filas.length; i++) {
        const subtotal = parseFloat(filas[i].cells[4].textContent) || 0;
        total += subtotal;
    }

    document.getElementById("id_subtotal_articulos").textContent = total.toFixed(2);
    document.getElementById("id_subtotal_general").textContent = total.toFixed(2);

    const btnReserva = document.getElementById("btnRealizarReserva");
    btnReserva.disabled = (total === 0);
}

// ===== MODAL CLIENTE =====
function initClienteModal() {
    const nombreCliente = document.getElementById("nombreCliente");
    const sugerencias = document.getElementById("sugerencias");
    const personaId = document.getElementById("idPersona");
    const btnAbrir = document.getElementById("btnAbrirModalCliente");
    const btnRegistrar = document.getElementById("btnRegistrarCliente");

    btnAbrir.addEventListener("click", () => {
        const modal = new bootstrap.Modal(document.getElementById("modalCliente"));
        modal.show();
    });

    // Autocompletado de cliente
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
                            item.textContent = persona.persona_concatenada;
                            item.style.cursor = "pointer";

                            item.addEventListener("click", function() {
                                nombreCliente.value = persona.persona_concatenada;
                                personaId.textContent = persona.id;
                                sugerencias.innerHTML = "";
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
                    console.error("Error al procesar resultados:", e);
                    sugerencias.innerHTML = "";
                }
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

    // Registrar cliente
    btnRegistrar.addEventListener('click', async () => {
        let datos = {};
        
        if (document.getElementById('pills-persona-tab').classList.contains('active')) {
            if (validarCamposPersona()) {
                datos = {
                    "numero_documento": document.getElementById('numeroDocumentoPersona').value,
                    "nombres": document.getElementById('nombresPersona').value,
                    "apellidos": document.getElementById('apellidosPersona').value,
                    "telefono_movil": document.getElementById('telefonoPersona').value || null,
                    "email": document.getElementById('emailPersona').value
                };

                try {
                    const response = await fnRegistrarPersona(datos);
                    const nombreConcatenado = `${datos.numero_documento} - ${datos.nombres} ${datos.apellidos}`;
                    
                    nombreCliente.value = nombreConcatenado;
                    personaId.textContent = response.persona_id;
                    
                    limpiarCamposCliente();
                    showNotification("success");
                    
                    const modal = bootstrap.Modal.getInstance(document.getElementById("modalCliente"));
                    modal.hide();
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Ocurrió un error al registrar'
                    });
                }
            }
        } else {
            if (validarCamposEmpresa()) {
                datos = {
                    "numero_documento": document.getElementById('numeroDocumentoEmpresa').value,
                    "nombre_comercial": document.getElementById('nombreComercial').value,
                    "razon_social": document.getElementById('razonSocial').value,
                    "telefono_movil": document.getElementById('telefonoEmpresa').value,
                    "email": document.getElementById('emailEmpresa').value
                };

                try {
                    const response = await fnRegistrarEmpresa(datos);
                    const nombreConcatenado = `${datos.numero_documento} - ${datos.razon_social}`;
                    
                    nombreCliente.value = nombreConcatenado;
                    personaId.textContent = response.empresa_id;
                    
                    limpiarCamposCliente();
                    showNotification("success");
                    
                    const modal = bootstrap.Modal.getInstance(document.getElementById("modalCliente"));
                    modal.hide();
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Ocurrió un error al registrar'
                    });
                }
            }
        }
    });
}

// ===== VALIDACIONES CLIENTE =====
function validarCamposPersona() {
    let valid = true;

    const numeroDocumento = document.getElementById('numeroDocumentoPersona');
    const errorNumeroDocumento = document.getElementById('error-numeroDocumentoPersona');
    if (numeroDocumento.value.trim() === '' || !/^\d{8}$/.test(numeroDocumento.value)) {
        valid = false;
        numeroDocumento.classList.add('is-invalid');
        errorNumeroDocumento.textContent = 'DNI debe tener 8 dígitos.';
    } else {
        numeroDocumento.classList.remove('is-invalid');
        errorNumeroDocumento.textContent = '';
    }

    const nombres = document.getElementById('nombresPersona');
    const errorNombres = document.getElementById('error-nombresPersona');
    if (nombres.value.trim() === '') {
        valid = false;
        nombres.classList.add('is-invalid');
        errorNombres.textContent = 'Los nombres son obligatorios.';
    } else {
        nombres.classList.remove('is-invalid');
        errorNombres.textContent = '';
    }

    const apellidos = document.getElementById('apellidosPersona');
    const errorApellidos = document.getElementById('error-apellidosPersona');
    if (apellidos.value.trim() === '') {
        valid = false;
        apellidos.classList.add('is-invalid');
        errorApellidos.textContent = 'Los apellidos son obligatorios.';
    } else {
        apellidos.classList.remove('is-invalid');
        errorApellidos.textContent = '';
    }

    return valid;
}

function validarCamposEmpresa() {
    let valid = true;

    const numeroDocumento = document.getElementById('numeroDocumentoEmpresa');
    const errorNumeroDocumento = document.getElementById('error-numeroDocumentoEmpresa');
    if (numeroDocumento.value.trim() === '' || !/^\d{11}$/.test(numeroDocumento.value)) {
        valid = false;
        numeroDocumento.classList.add('is-invalid');
        errorNumeroDocumento.textContent = 'RUC debe tener 11 dígitos.';
    } else {
        numeroDocumento.classList.remove('is-invalid');
        errorNumeroDocumento.textContent = '';
    }

    const nombreComercial = document.getElementById('nombreComercial');
    const errorNombreComercial = document.getElementById('error-nombreComercial');
    if (nombreComercial.value.trim() === '') {
        valid = false;
        nombreComercial.classList.add('is-invalid');
        errorNombreComercial.textContent = 'El nombre comercial es obligatorio.';
    } else {
        nombreComercial.classList.remove('is-invalid');
        errorNombreComercial.textContent = '';
    }

    const razonSocial = document.getElementById('razonSocial');
    const errorRazonSocial = document.getElementById('error-razonSocial');
    if (razonSocial.value.trim() === '') {
        valid = false;
        razonSocial.classList.add('is-invalid');
        errorRazonSocial.textContent = 'La razón social es obligatoria.';
    } else {
        razonSocial.classList.remove('is-invalid');
        errorRazonSocial.textContent = '';
    }

    return valid;
}

function limpiarCamposCliente() {
    document.getElementById('numeroDocumentoPersona').value = '';
    document.getElementById('nombresPersona').value = '';
    document.getElementById('apellidosPersona').value = '';
    document.getElementById('telefonoPersona').value = '';
    document.getElementById('emailPersona').value = '';
    document.getElementById('numeroDocumentoEmpresa').value = '';
    document.getElementById('nombreComercial').value = '';
    document.getElementById('razonSocial').value = '';
    document.getElementById('telefonoEmpresa').value = '';
    document.getElementById('emailEmpresa').value = '';
}

function fnRegistrarPersona(datos) {
    return new Promise((resolve, reject) => {
        $.ajax({
            method: "POST",
            url: "logica/clssPersona.php",
            data: {
                "accion": "REGISTRARPERSONARAPIDO",
                "data": JSON.stringify(datos)
            }
        }).done(function(response) {
            const jsonResponse = JSON.parse(response);
            if (jsonResponse.success) {
                resolve(jsonResponse);
            } else {
                reject(new Error(jsonResponse.message || "Error desconocido"));
            }
        }).fail(function(error) {
            reject(error);
        });
    });
}

function fnRegistrarEmpresa(datos) {
    return new Promise((resolve, reject) => {
        $.ajax({
            method: "POST",
            url: "logica/clssPersona.php",
            data: {
                "accion": "REGISTRARPERSONARAPIDO",
                "data": JSON.stringify(datos)
            }
        }).done(function(response) {
            const jsonResponse = JSON.parse(response);
            if (jsonResponse.success) {
                resolve(jsonResponse);
            } else {
                reject(new Error(jsonResponse.message || "Error desconocido"));
            }
        }).fail(function(error) {
            reject(error);
        });
    });
}

// ===== REALIZAR RESERVA =====
function initReservaModal() {
    const btnRealizarReserva = document.getElementById("btnRealizarReserva");
    const btnReservar = document.getElementById("Reservar");

    btnRealizarReserva.addEventListener("click", () => {
        const subtotalGeneral = document.getElementById("id_subtotal_general").textContent;
        document.getElementById("montoTotal").value = subtotalGeneral;
        document.getElementById("idMontoVentaTitulo").textContent = subtotalGeneral;

        const modal = new bootstrap.Modal(document.getElementById("modalRealizarPago"));
        modal.show();
    });

    btnReservar.addEventListener("click", () => {
        const idCliente = document.getElementById('idPersona').textContent.trim();
        const total = document.getElementById("montoTotal").value;
        const userId = <?php echo $_SESSION['id']; ?>;

        if (idCliente === '#') {
            Swal.fire({
                icon: 'warning',
                title: 'Cliente requerido',
                text: 'Por favor selecciona o registra un cliente'
            });
            return;
        }

        const datos = {
            "usuario_id": userId,
            "cliente_id": idCliente,
            "total": total,
            "articulos": []
        };

        const rows = document.querySelectorAll("#tabla_articulos tbody tr");
        rows.forEach(row => {
            const articulo = {
                "articulo_id": row.cells[0].textContent,
                "minutos": 0,
                "costoxminuto": 0,
                "precio_unitario": parseFloat(row.cells[3].textContent) || 0,
                "cantidad": parseInt(row.cells[2].textContent) || 1,
                "sub_total": parseFloat(row.cells[4].textContent),
                "movimiento_id": parseInt(row.cells[6].textContent),
                "nota_archivo": row.cells[7].textContent || "Sin nota"
            };
            datos.articulos.push(articulo);
        });

        $.ajax({
            method: "POST",
            url: "logica/clssVentaCorte.php",
            data: {
                "accion": "REGISTRARRESERVA",
                "data": JSON.stringify(datos)
            }
        }).done(function(response) {
            const result = JSON.parse(response);
            if (result.success === true) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Reserva exitosa!',
                    text: 'La reserva se registró correctamente',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo procesar la reserva'
                });
            }
        }).fail(function(error) {
            console.error("Error:", error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al procesar la reserva'
            });
        });
    });
}
</script>
<?php include("pie.php"); ?>