<?php
include("cabecera.php");
$sucursal_id = isset($_SESSION['sucursal_id']) ? $_SESSION['sucursal_id'] : null;
if (!$sucursal_id) {
    echo '<div class="alert alert-danger">Error: No se ha establecido una sucursal activa.</div>';
    exit;
}
?>

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
                    <li class="nav-item" id="nav-item-registro">
                        <a class="nav-link active" id="pills-home-tab-nobd" data-bs-toggle="pill" href="#pills-home-nobd" role="tab" aria-controls="pills-home-nobd" aria-selected="true" id="link-registro-compras"><i class="fas fa-bars"></i> Listado de Compras</a>
                    </li>
                    <li class="nav-item" id="nav-item-listado">
                        <a class="nav-link" id="pills-profile-tab-nobd" data-bs-toggle="pill" href="#pills-profile-nobd" role="tab" aria-controls="pills-profile-nobd" aria-selected="false" id="link-listado-compras"><i class="fas fa-box-open"></i> Registro de Compras </a>
                    </li>
                </ul>
                <div class="tab-content mt-2 mb-3" id="pills-without-border-tabContent">
                    <div class="tab-pane fade show active" id="pills-home-nobd" role="tabpanel" aria-labelledby="pills-home-tab-nobd" id="tab-registro-compras">

                        <!-- ── PANEL DE FILTROS ─────────────────────────────────── -->
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="fas fa-filter"></i> Filtros de Búsqueda
                                </h5>
                                <div class="row g-2 align-items-end">

                                    <!-- Proveedor -->
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-truck"></i> Proveedor
                                        </label>
                                        <input type="text" id="filtro-proveedor" class="form-control"
                                            placeholder="Buscar proveedor..." />
                                    </div>

                                    <!-- Realizada por -->
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-user"></i> Realizada Por
                                        </label>
                                        <input type="text" id="filtro-usuario" class="form-control"
                                            placeholder="Usuario..." />
                                    </div>

                                    <!-- Fecha desde -->
                                    <div class="col-12 col-sm-6 col-md-2">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-calendar-alt"></i> Fecha Desde
                                        </label>
                                        <input type="date" id="filtro-fecha-desde" class="form-control" />
                                    </div>

                                    <!-- Fecha hasta -->
                                    <div class="col-12 col-sm-6 col-md-2">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-calendar-alt"></i> Fecha Hasta
                                        </label>
                                        <input type="date" id="filtro-fecha-hasta" class="form-control" />
                                    </div>

                                    <!-- Botones -->
                                    <div class="col-12 col-sm-12 col-md-2 d-flex gap-2">
                                        <button class="btn btn-primary btn-round flex-fill"
                                            onclick="fnAplicarFiltros()">
                                            <i class="fas fa-search"></i> Filtrar
                                        </button>
                                        <button class="btn btn-secondary btn-round flex-fill"
                                            onclick="fnLimpiarFiltros()">
                                            <i class="fas fa-times"></i> Limpiar
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- ── TARJETAS DE RESUMEN ───────────────────────────────── -->
                        <div class="row g-2 mb-3" id="cards-stats-compras">

                            <!-- Compras encontradas -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="card h-100" style="background:#e8f5e9; border-left:4px solid #4caf50;">
                                    <div class="card-body py-3">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="fas fa-list-ol" style="color:#4caf50;font-size:1.3rem;"></i>
                                            <small class="text-muted fw-semibold">Compras encontradas</small>
                                        </div>
                                        <h4 class="mb-0 fw-bold" id="stat-compras-encontradas"
                                            style="color:#2e7d32;">...</h4>
                                    </div>
                                </div>
                            </div>

                            <!-- Total por Rango -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="card h-100" style="background:#e3f2fd; border-left:4px solid #1976d2;">
                                    <div class="card-body py-3">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="fas fa-calendar-check" style="color:#1976d2;font-size:1.3rem;"></i>
                                            <small class="text-muted fw-semibold">Total por Rango (S/)</small>
                                        </div>
                                        <h4 class="mb-0 fw-bold" id="stat-total-rango"
                                            style="color:#0d47a1;">...</h4>
                                    </div>
                                </div>
                            </div>

                            <!-- Total por Productos -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="card h-100" style="background:#fff8e1; border-left:4px solid #f9a825;">
                                    <div class="card-body py-3">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="fas fa-boxes" style="color:#f9a825;font-size:1.3rem;"></i>
                                            <small class="text-muted fw-semibold">Total por Productos (S/)</small>
                                        </div>
                                        <h4 class="mb-0 fw-bold" id="stat-total-productos"
                                            style="color:#e65100;">...</h4>
                                    </div>
                                </div>
                            </div>

                            <!-- Gran Total Histórico -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="card h-100" style="background:#fce4ec; border-left:4px solid #c2185b;">
                                    <div class="card-body py-3">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <i class="fas fa-coins" style="color:#c2185b;font-size:1.3rem;"></i>
                                            <small class="text-muted fw-semibold">Gran Total Histórico (S/)</small>
                                        </div>
                                        <h4 class="mb-0 fw-bold" id="stat-gran-total"
                                            style="color:#880e4f;">...</h4>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- ── TABLA DE COMPRAS ──────────────────────────────────── -->
                        <div class="card text-start">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="TablaVentaDiaria"
                                        class="display table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Realizada por</th>
                                                <th>Proveedor</th>
                                                <th>Fecha Compra</th>
                                                <th>Total</th>
                                                <th>Total Por Productos</th> <!-- ← AGREGAR -->
                                                <th>Fecha Registro</th>
                                                <th>Hora</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody-compras">
                                            <?php
                                            foreach (fnListadoCompras($sucursal_id) as $datos) {
                                                $datosJSON = json_encode($datos);
                                            ?>
                                                <tr>
                                                    <td><?php echo $datos["compra_id"] ?></td>
                                                    <td><?php echo $datos["realizada_por"] ?></td>
                                                    <td><?php echo $datos["proveedor"] ?></td>
                                                    <td><?php echo $datos["fecha_compra"] ?></td>
                                                    <td><?php echo $datos["total"] ?></td>
                                                    <td><?php echo $datos["fecha_registro"] ?></td>
                                                    <td><?php echo $datos["hora"] ?></td>
                                                    <td>
                                                        <div class="mt-2 text-center">
                                                            <a onclick='abrirDetalle(<?php echo $datosJSON ?>)'
                                                                class="btn btn-secondary btn-round btn-sm"
                                                                role="button">
                                                                <i class="fas fa-external-link-square-alt"></i>
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

                    </div><!-- /tab-pane listado -->


                    <div class="tab-pane fade" id="pills-profile-nobd" role="tabpanel" aria-labelledby="pills-profile-tab-nobd" id="tab-listado-compras">
                        <div class="card text-start">

                            <div class="card-body">
                                <h4 class="card-title"><i class="fas fa-shopping-bag"></i> Registro de Compras</h4>
                                <div class="card-sub">
                                    Aquí podrás Registrar la Compras que realizas a tus proveedores. Una vez registrado, el <strong>Stock de tus productos</strong> tambien se actualiza.
                                </div>
                                <div class="row justify-content-center align-items-start g-2">

                                    <div class="col-12 col-sm-6" style="position: sticky; top: 0; z-index: 10;">
                                        <div class="card text-start">
                                            <div class="card-body">
                                                <h4 class="card-title"><i class="fas fa-shipping-fast"> </i> Datos de la Compra</h4>
                                                <hr>
                                                <div id="idUsuarioCompra" style="display: none;"> <?php echo $id_usuario_s ?></div>
                                                <div><i class="far fa-user"> </i><strong> <?php echo $id_usuario_s . " - " . $nombre . ", " . $ape_usuario ?> </strong> </div>
                                                <hr>
                                                <div class="mb-3">
                                                    <label for="" class="form-label"><strong>Proveedor</strong></label>
                                                    <div class="input-group position-relative">
                                                        <input type="text" id="proveedor" class="form-control" placeholder="Escribe al proveedor" />
                                                        <ul id="suggestions" class="list-group position-absolute w-100 mt-1" style="max-height: 200px; overflow-y: auto; z-index: 9999; background-color: white;"></ul>
                                                        <input type="hidden" id="proveedor_id" />
                                                        <button class="btn btn-outline-secondary" type="button" onclick="fnAbrirModalRegistroProveedor()">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                    <small id="helpId" class="form-text text-muted">
                                                        <div class="card-sub">
                                                            Si no encuentras a tu proveedor,<strong> Registra a tu proveedor con el botón de Más <i class="fas fa-plus"></i> </strong>
                                                        </div>
                                                    </small>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="" class="form-label"><strong>Fecha de Compra</strong></label>
                                                    <input type="date" id="idFechaCompra" class="form-control" />
                                                </div>

                                                <div class="row justify-content-center align-items-center">
                                                    <div class="col-12 col-md-6">
                                                        <div class="mb-3">
                                                            <label for="" class="form-label"><strong>N° de Comprobante</strong></label>
                                                            <input type="text" id="idCompraNumComprabante" class="form-control" />
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <div class="form-group">
                                                            <label for="" class="form-label"><strong>Total de Compra</strong></label>
                                                            <div class="input-group mb-3">
                                                                <span class="input-group-text">S/</span>
                                                                <input id="idCompraTotalDeCompra" type="number" step="0.0001" class="form-control" />

                                                            </div>
                                                        </div>
                                                    </div>



                                                </div>


                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-sm-6">
                                        <div class="card text-start">
                                            <div class="card-body">
                                                <h4 class="card-title"><i class="fas fa-cart-plus"></i> Articulos</h4>
                                                <hr>
                                                <div class="mb-3">

                                                    <label for="" class="form-label"><strong> Articulo</strong></label>
                                                    <div class="input-group position-relative">
                                                        <textarea
                                                            type="text"
                                                            class="form-control"
                                                            name=""
                                                            id="idBuscarArticulos"
                                                            aria-describedby="helpId"
                                                            placeholder="Escribe el articulo que buscas"></textarea>
                                                        <!-- Contenedor de las Articulos, estilo ajustado -->
                                                        <ul id="listadoArticulosBusqueda" class="list-group position-absolute w-100 mt-1" style="max-height: 200px; overflow-y: auto; z-index: 9999; background-color: white;"></ul>
                                                        <button class="btn btn-outline-secondary" type="button" onclick='fnAbrirModalRegistroArticulos()'>
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>

                                                    <small id="helpId" class="form-text text-muted">
                                                        <div class="card-sub">
                                                            Si no encuentras algun Articulo,<strong> Registra el Articulo con el boton de Más <i class="fas fa-plus"></i> </strong>
                                                        </div>
                                                    </small>
                                                </div>
                                                <hr>
                                                <div id="idPanelProducto">
                                                    <div class="mb-3">
                                                        <input type="hidden" id="idArticuloEncontrado" />
                                                    </div>
                                                    <div class="row">
                                                        <div class="card-body">
                                                            <ul class="nav nav-pills nav-secondary nav-pills-no-bd" id="pills-tab-without-border" role="tablist">
                                                                <li class="nav-item">
                                                                    <a class="nav-link active" id="pills-home-tab-icon" data-bs-toggle="pill" href="#pills-home-icon" role="tab" aria-controls="pills-home-icon" aria-selected="true">
                                                                        Cantidades Exactas
                                                                    </a>
                                                                </li>
                                                                <li class="nav-item">
                                                                    <a class="nav-link" id="pills-profile-tab-icon" data-bs-toggle="pill" href="#pills-profile-icon" role="tab" aria-controls="pills-profile-icon" aria-selected="false">
                                                                        Cajas
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                            <hr>
                                                            <div class="tab-content mt-2 mb-3" id="pills-with-icon-tabContent">
                                                                <div class="tab-pane fade show active" id="pills-home-icon" role="tabpanel" aria-labelledby="pills-home-tab-icon">
                                                                    <div class="row justify-content-center align-items-center">
                                                                        <!-- Cantidad de Artículos -->
                                                                        <div class="col-12 col-sm-6 col-md-3">
                                                                            <div class="form-group">
                                                                                <label for="ca-idCantidadArticulos" class="form-label"><strong>Cantidad</strong></label>
                                                                                <div class="input-group mb-3">
                                                                                    <span class="input-group-text">#</span>
                                                                                    <input
                                                                                        id="ca-idCantidadArticulos"
                                                                                        type="number"
                                                                                        class="form-control"
                                                                                        aria-label="Cantidad de Artículos" />
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Total de Compra -->
                                                                        <div class="col-12 col-sm-6 col-md-3">
                                                                            <div class="form-group">
                                                                                <label for="ca-idTotalCompraArticulo" class="form-label"><strong>Total de Compra</strong></label>
                                                                                <div class="input-group mb-3">
                                                                                    <span class="input-group-text">S/</span>
                                                                                    <input id="ca-idTotalCompraArticulo" type="number" step="0.0001" class="form-control" />
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- P.U Calculado -->
                                                                        <div class="col-12 col-sm-6 col-md-3">
                                                                            <div class="form-group">
                                                                                <label for="ca-idPrecioCalculado" class="form-label"><strong>P.U Calculado</strong></label>
                                                                                <div class="input-group mb-3">
                                                                                    <span class="input-group-text">S/</span>
                                                                                    <input id="ca-idPrecioCalculado" type="number" step="0.0001" class="form-control" readonly />

                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Precio de Venta -->
                                                                        <div class="col-12 col-sm-6 col-md-3">
                                                                            <div class="form-group">
                                                                                <label for="ca-idPrecioVenta" class="form-label"><strong>P.U Venta</strong></label>
                                                                                <div class="input-group mb-3">
                                                                                    <span class="input-group-text">S/</span>
                                                                                    <input id="ca-idPrecioVenta" type="number" step="0.0001" class="form-control" />

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!-- DESTINO DE INVENTARIO — Cantidades Exactas -->
                                                                        <div class="col-12 col-sm-6 col-md-3">
                                                                            <div class="form-group">
                                                                                <label class="form-label"><strong>Locación Destino <span class="text-danger">*</span></strong></label>
                                                                                <select class="form-select form-select-sm" id="ca-idLocacion">
                                                                                    <option value="">Seleccione locación...</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-sm-6 col-md-3">
                                                                            <div class="form-group">
                                                                                <label class="form-label"><strong>Estructura</strong></label>
                                                                                <select class="form-select form-select-sm" id="ca-idEstructura">
                                                                                    <option value="">— Sin estructura —</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <!-- DESTINO DE INVENTARIO — Cajas (mismo bloque, IDs con prefijo caj-) -->
                                                                        <div class="col-12 col-sm-6 col-md-4">
                                                                            <div class="form-group">
                                                                                <label class="form-label"><strong>Locación Destino <span class="text-danger">*</span></strong></label>
                                                                                <select class="form-select form-select-sm" id="caj-idLocacion">
                                                                                    <option value="">Seleccione locación...</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-sm-6 col-md-4">
                                                                            <div class="form-group">
                                                                                <label class="form-label"><strong>Estructura</strong></label>
                                                                                <select class="form-select form-select-sm" id="caj-idEstructura">
                                                                                    <option value="">— Sin estructura —</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Botón Agregar -->
                                                                        <div class="col-12 col-sm-6 col-md-3 d-flex justify-content-center align-items-center">
                                                                            <a
                                                                                class="btn btn-success btn-round"
                                                                                onclick='fn_agregar_cantidad_exacta()'
                                                                                role="button">
                                                                                Agregar <i class="fas fa-plus"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>

                                                                </div>

                                                                <div class="tab-pane fade" id="pills-profile-icon" role="tabpanel" aria-labelledby="pills-profile-tab-icon">
                                                                    <div
                                                                        class="row justify-content-center align-items-center">
                                                                        <div class="col-12 col-sm-6 col-md-3">
                                                                            <div class="form-group">
                                                                                <label for="" class="form-label"><strong>Cantidad de Cajas</strong></label>
                                                                                <div class="input-group mb-3">
                                                                                    <span class="input-group-text">#</span>
                                                                                    <input
                                                                                        id="caj-idCantidadCajas"
                                                                                        type="number"
                                                                                        class="form-control"
                                                                                        aria-label="Amount (to the nearest dollar)" />
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                        <div class="col-12 col-sm-6 col-md-3">
                                                                            <div class="form-group">
                                                                                <label for="" class="form-label"><strong>Unidades Por Cajas</strong></label>
                                                                                <div class="input-group mb-3">
                                                                                    <span class="input-group-text">#</span>
                                                                                    <input
                                                                                        id="caj-idUnidadesPorCaja"
                                                                                        type="number"
                                                                                        class="form-control"
                                                                                        aria-label="Amount (to the nearest dollar)" />
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                        <div class="col-12 col-sm-6 col-md-3">
                                                                            <div class="form-group">
                                                                                <label for="" class="form-label"><strong>P.U de Caja (S/)</strong></label>
                                                                                <div class="input-group mb-3">
                                                                                    <span class="input-group-text">S/</span>
                                                                                    <input id="caj-idPrecioUnitarioCaja" type="number" step="0.0001" class="form-control" />

                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-12 col-sm-6 col-md-3">
                                                                            <div class="form-group">
                                                                                <label for="" class="form-label"><strong>Total (S/)</strong></label>
                                                                                <div class="input-group mb-3">
                                                                                    <span class="input-group-text">S/</span>
                                                                                    <input id="caj-idTotalCajas" type="number" step="0.0001" class="form-control" readonly />

                                                                                </div>
                                                                            </div>
                                                                        </div>


                                                                        <div class="col-12 col-sm-6 col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="" class="form-label"><strong>P.U Calculado</strong></label>
                                                                                <div class="input-group mb-3">
                                                                                    <span class="input-group-text">S/</span>
                                                                                    <input id="caj-idPrecioUnitarioCalculado" type="number" step="0.0001" class="form-control" readonly />

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-sm-6 col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="" class="form-label"><strong>Unidades Calculadas</strong></label>
                                                                                <div class="input-group mb-3">
                                                                                    <span class="input-group-text">#</span>
                                                                                    <input
                                                                                        readonly
                                                                                        id="caj-idUnidadesCalculadas"
                                                                                        type="number"
                                                                                        class="form-control"
                                                                                        aria-label="" />

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-sm-6 col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="" class="form-label"><strong>P.U VENTA (S/)</strong></label>
                                                                                <div class="input-group mb-3">
                                                                                    <span class="input-group-text">S/</span>
                                                                                    <input id="caj-idPrecioVenta" type="text" step="0.0001" class="form-control" />


                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-sm-6 col-md-4 text-center">
                                                                            <a
                                                                                name=""
                                                                                id=""
                                                                                class="btn btn-success btn-round"
                                                                                onclick='fn_agregar_cantidad_cajas()'
                                                                                role="button">Agregar <i class="fas fa-plus"> </i></a>
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

                                <div class="card text-start">

                                    <div class="card-body">
                                        <h4 class="card-title">Articulos de Compra</h4>
                                        <hr>
                                        <div
                                            class="table-responsive-sm">
                                            <table
                                                id="idTablitaCompra"
                                                class="table">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">ID</th>
                                                        <th scope="col">Articulo</th>
                                                        <th scope="col">Cantidad</th>
                                                        <th scope="col">Precio Unitario</th>
                                                        <th scope="col">Sub Total</th>
                                                        <th scope="col">Precio Venta</th>
                                                        <th scope="col"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>


                                <div style="text-align: right;">
                                    <a
                                        name=""
                                        id=""
                                        class="btn btn-danger btn-round"
                                        onclick="window.location.reload(); return false;"
                                        role="button">Cancelar</a>
                                    <a
                                        name=""
                                        id=""
                                        class="btn btn-success btn-round"
                                        onclick='fn_registrar_compra()'
                                        role="button">Guardar <i class="fas fa-check"> </i></a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>

<div
    class="modal fade"
    id="modalRegistroCompra"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-custom" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="card border-primary">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="card-body">

                        <h4 class="card-title text-center" style="font-size: 28px;"><i class="fas fa-shopping-bag"></i> Registro de Compras</h4>
                        <hr>
                        <div class="card-sub text-center">
                            Aquí podrás Registrar la Compras que realizas a tus proveedores. Una vez registrado, el <strong>Stock de tus productos</strong> tambien se actualiza.
                        </div>
                        <div class="row justify-content-center align-items-center sm-2">
                            <div class="col-sm-6">
                                <div class="card text-start">
                                    <div class="card-body">
                                        <h4 class="card-title">Compra</h4>
                                        <div class="mb-3">
                                            <label for="" class="form-label">Name</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                name=""
                                                id=""
                                                aria-describedby="helpId"
                                                placeholder="" />
                                            <small id="helpId" class="form-text text-muted">Help text</small>
                                        </div>

                                    </div>
                                </div>

                            </div>
                            <div class="col-sm-6">
                                <div class="card text-start">
                                    <div class="card-body">
                                        <h4 class="card-title">Productos</h4>
                                        <p class="card-text">Body</p>
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


<div
    class="modal fade"
    id="modalRegistroArticulos"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-custom" role="document"> <!-- Usamos la clase personalizada aquí -->
        <div class="modal-content">


            <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="card-body">
                <h4 class="card-title text-center" style="font-size: 28px;"><i class="fas fa-shopping-bag"></i> Registro de Articulos</h4>
                <hr>
                <div class="card-sub text-center">
                    Aquí podrás <strong>registrar</strong> los Artículos <strong>NUEVOS.</strong>
                </div>
                <div class="card text-start">

                    <div class="card-body">
                        <div
                            class="row justify-content-center align-items-center g-2">

                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Ingrese Nombre de Articulo</strong></label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="idRegistroNombreArticulo"
                                        id="idRegistroNombreArticulo"
                                        aria-describedby="helpId"
                                        placeholder="Articulo 1" />
                                </div>

                            </div>


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Categoría</strong></label>
                                    <select
                                        class="form-select form-select-sm"
                                        name="idRegistoCategoria"
                                        id="idRegistoCategoria">
                                        <option selected>Selccione Categoría</option>
                                        <?php foreach (listarCategoria($sucursal_id) as $datos) {
                                        ?>
                                            <option value="<?php echo $datos["id"] ?>"><?php echo $datos["abreviatura"] ?></option>

                                        <?php
                                        } ?>
                                    </select>
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Tipo de Artículo</strong></label>
                                    <select
                                        class="form-select form-select-sm"
                                        name="idRegistoTipo"
                                        id="idRegistoTipo">
                                        <option selected>Selccione Tipo de Articulo</option>
                                        <?php foreach (listarTipoArticulos($sucursal_id) as $datos) {
                                        ?>
                                            <option value="<?php echo $datos["id"] ?>"><?php echo $datos["abreviatura"] ?></option>

                                        <?php
                                        } ?>
                                    </select>
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Dimensión</strong></label>
                                    <select
                                        class="form-select form-select-sm"
                                        name="idRegistroDimension"
                                        id="idRegistroDimension">
                                        <option selected>Selccione Dimensión</option>
                                        <?php foreach (listarDimension($sucursal_id) as $datos) {
                                        ?>
                                            <option value="<?php echo $datos["id"] ?>"><?php echo $datos["medida"] ?></option>

                                        <?php
                                        } ?>
                                    </select>
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Escala</strong></label>
                                    <select
                                        class="form-select form-select-sm"
                                        name="idRegistroEscala"
                                        id="idRegistroEscala">
                                        <option selected>Selccione Escala</option>
                                        <?php foreach (listarEscala($sucursal_id) as $datos) {
                                        ?>
                                            <option value="<?php echo $datos["id"] ?>"><?php echo $datos["abreviatura"] ?></option>

                                        <?php
                                        } ?>
                                    </select>
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><strong>Marca de Articulo</strong></label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="idRegistroMarca"
                                        id="idRegistroMarca"
                                        aria-describedby="helpId"
                                        placeholder="Ejemplo: Artesco" />
                                </div>
                            </div>


                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"> <strong>Color</strong></label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="idRegistroColor"
                                        id="idRegistroColor"
                                        aria-describedby="helpId"
                                        placeholder="Rojo, verde, azul, Etc." />
                                </div>

                            </div>


                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="" class="form-label"><strong>Requiere Corte</strong></label>
                                    <div class="d-flex">
                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                name="flexRadioDefault"
                                                id="flexRadioDefault1"
                                                value="Si" />
                                            <label
                                                class="form-check-label"
                                                for="flexRadioDefault1">
                                                Si
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="radio"
                                                name="flexRadioDefault"
                                                id="flexRadioDefault2"
                                                value="No"
                                                checked />
                                            <label
                                                class="form-check-label"
                                                for="flexRadioDefault2">
                                                No
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">

                            </div>
                            <div class="text-center">
                                <a
                                    name=""
                                    id=""
                                    class="btn btn-success btn-round"
                                    onclick='fn_registrar_articulo()'
                                    role="button">Registrar <i class="fas fa-check"> </i></a>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

</div>

<div
    class="modal fade"
    id="idModalRegistrarProveedor"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>

                <div class="card-body">
                    <h4 class="card-title text-center" style="font-size: 28px;"><i class="fas fa-diagnoses"></i> Registro de Proveedor</h4>
                    <hr>
                    <div class="card-sub text-center">
                        Aquí podrás registrar a tu proveedor.
                    </div>


                    <div class="card text-start">
                        <div class="card-body">
                            <div class="row justify-content-center align-items-center">
                                <div class="col-sm-4">
                                    <div class="mb-3">
                                        <label for="" class="form-label"><strong>RUC</strong></label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name=""
                                            id="idRucProveedor"
                                            aria-describedby="helpId"
                                            placeholder="2837617" />
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="mb-3">
                                        <label for="" class="form-label"><strong>Nombre Comercial</strong></label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name=""
                                            id="idNombreComercialProveedor"
                                            aria-describedby="helpId"
                                            placeholder="Proveedor 001" />
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="mb-3">
                                        <label for="" class="form-label"><strong>Razón social</strong></label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name=""
                                            id="idRazonSocialProveedor"
                                            aria-describedby="helpId"
                                            placeholder="Proveedor S.A.C" />
                                    </div>
                                </div>




                                <div class="col-sm-4">
                                    <div class="mb-3">
                                        <label for="" class="form-label"><strong>Número de Telefono Fijo</strong></label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name=""
                                            id="idNumTelefonoFijoProveedor"
                                            aria-describedby="helpId"
                                            placeholder="" />
                                    </div>
                                </div>


                                <div class="col-sm-4">
                                    <div class="mb-3">
                                        <label for="" class="form-label"><strong>Número de Celular</strong></label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name=""
                                            id="idNumCelularProveedor"
                                            aria-describedby="helpId"
                                            placeholder="" />
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="mb-3">
                                        <label for="" class="form-label"><strong>Correo</strong></label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name=""
                                            id="idCorreoProveedor"
                                            aria-describedby="helpId"
                                            placeholder="" />
                                    </div>
                                </div>



                            </div>
                            <div class="text-center">
                                <a
                                    name=""
                                    id=""
                                    class="btn btn-success btn-round"
                                    onclick='fnRegistrarProveedor()'
                                    role="button">Registrar <i class="fas fa-plus"> </i></a>
                            </div>

                        </div>
                    </div>


                </div>

            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     MODAL DETALLE DE COMPRA — sin acordeón
     ============================================================ -->

<div class="modal fade" id="modalDetalleCompra" tabindex="-1" role="dialog"
    aria-labelledby="modalDetalleCompraLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-sm" style="border-radius:14px; overflow:hidden;">

            <!-- ── HEADER ── -->
            <div class="modal-header border-0 px-4 py-3" style="background:var(--bs-secondary-bg,#f8f9fa);">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3"
                        style="width:40px; height:40px; background:#e8f0fe;">
                        <i class="fas fa-shopping-bag" style="color:#3b5bdb; font-size:17px;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-semibold" id="modalDetalleCompraLabel">
                            Detalle de compra <span id="idMontoVenta" class="text-primary"></span>
                        </h6>
                        <small class="text-muted" id="idNumeroCompra">Cargando...</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <span class="badge rounded-pill" id="idBadgeEstado"
                        style="background:#d3f9d8; color:#2b8a3e; font-size:11px; padding:5px 12px;">
                        Completada
                    </span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
            </div>

            <!-- ── BODY ── -->
            <div class="modal-body p-4 d-flex flex-column gap-3">

                <!-- Tarjetas métricas -->
                <div class="row g-2">
                    <div class="col-4">
                        <div class="rounded-3 p-3" style="background:var(--bs-secondary-bg,#f8f9fa);">
                            <p class="mb-1 text-muted" style="font-size:11px; text-transform:uppercase; letter-spacing:.04em;">
                                Monto registrado
                            </p>
                            <p class="mb-0 fw-semibold" style="font-size:20px;" id="idMontoRegistrado">S/ —</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="rounded-3 p-3" style="background:var(--bs-secondary-bg,#f8f9fa);">
                            <p class="mb-1 text-muted" style="font-size:11px; text-transform:uppercase; letter-spacing:.04em;">
                                Total artículos
                            </p>
                            <p class="mb-0 fw-semibold" style="font-size:20px;" id="idMontoTotalArticulos">S/ —</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="rounded-3 p-3" style="background:var(--bs-secondary-bg,#f8f9fa);">
                            <p class="mb-1 text-muted" style="font-size:11px; text-transform:uppercase; letter-spacing:.04em;">
                                Artículos
                            </p>
                            <p class="mb-0 fw-semibold" style="font-size:20px;" id="idCantidadArticulos">—</p>
                        </div>
                    </div>
                </div>

                <!-- Proveedor + Registro -->
                <div class="row g-2">
                    <div class="col-sm-6">
                        <div class="border rounded-3 p-3 h-100" style="border-color:rgba(0,0,0,.1) !important;">
                            <p class="mb-2 text-muted d-flex align-items-center gap-2" style="font-size:13px; font-weight:500;">
                                <i class="fas fa-store" style="font-size:13px;"></i> Proveedor
                            </p>
                            <div class="d-flex align-items-center gap-2">
                                <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                                    style="width:34px; height:34px; background:#fff3bf; font-size:12px; font-weight:600; color:#e67700;"
                                    id="idProveedorAvatar">??</div>
                                <div>
                                    <p class="mb-0 fw-semibold" style="font-size:13px;" id="idProveedorNombre">—</p>
                                    <p class="mb-0 text-muted" style="font-size:11px;">
                                        N° Doc: <span id="docProveedor">—</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="border rounded-3 p-3 h-100" style="border-color:rgba(0,0,0,.1) !important;">
                            <p class="mb-2 text-muted d-flex align-items-center gap-2" style="font-size:13px; font-weight:500;">
                                <i class="fas fa-user-clock" style="font-size:13px;"></i> Registro
                            </p>
                            <div class="d-flex flex-column gap-1">
                                <div class="d-flex justify-content-between" style="font-size:12px;">
                                    <span class="text-muted">Registrado por</span>
                                    <span class="fw-semibold" id="idUsuario">—</span>
                                </div>
                                <div class="d-flex justify-content-between" style="font-size:12px;">
                                    <span class="text-muted">Fecha de compra</span>
                                    <span id="idFechaComprav2">—</span>
                                </div>
                                <div class="d-flex justify-content-between" style="font-size:12px;">
                                    <span class="text-muted">Fecha de registro</span>
                                    <span id="idFechaRegistro">—</span>
                                </div>
                                <div class="d-flex justify-content-between" style="font-size:12px;">
                                    <span class="text-muted">Hora</span>
                                    <span id="idHoraRegistro">—</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detalle de artículos — directo, sin acordeón -->
                <div class="border rounded-3 overflow-hidden" style="border-color:rgba(0,0,0,.1) !important;">
                    <div class="px-3 py-2 d-flex align-items-center gap-2"
                        style="background:var(--bs-secondary-bg,#f8f9fa); border-bottom:1px solid rgba(0,0,0,.07);">
                        <i class="fas fa-list-ul text-muted" style="font-size:13px;"></i>
                        <span style="font-size:13px; font-weight:500;">Detalle de artículos</span>
                    </div>

                    <ul class="list-unstyled mb-0 px-3" id="idContenidoUlDetalle">
                        <!-- Los <li> se generan desde JS — mismo id que antes -->
                    </ul>

                    <div class="d-flex justify-content-between align-items-center px-3 py-2"
                        style="border-top:1px solid rgba(0,0,0,.07); background:var(--bs-secondary-bg,#f8f9fa);">
                        <span class="text-muted" style="font-size:13px;">Total</span>
                        <span class="fw-semibold" style="font-size:15px;" id="idTotalDetalleCompra">S/ —</span>
                    </div>
                </div>

            </div><!-- /modal-body -->

            <!-- ── FOOTER ── -->
            <div class="modal-footer border-0 px-4 py-3 gap-2"
                style="background:var(--bs-secondary-bg,#f8f9fa); justify-content:flex-end;">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cerrar
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Imprimir
                </button>
            </div>

        </div>
    </div>
</div>

<!-- ============================================================
     JS
     ============================================================ -->
<script>
    /* Avatar de iniciales del proveedor */
    function setProveedorAvatar(nombre) {
        const el = document.getElementById('idProveedorAvatar');
        if (!el || !nombre) return;
        const partes = nombre.trim().split(' ');
        el.textContent = partes.length >= 2 ?
            (partes[0][0] + partes[1][0]).toUpperCase() :
            nombre.substring(0, 2).toUpperCase();
    }

    /* Ejemplo de cómo llenar el modal desde tu función existente:

    function fnAbrirDetalleCompra(datos) {
      document.getElementById('idMontoVenta').textContent           = 'C-' + datos.id;
      document.getElementById('idNumeroCompra').textContent         = 'Compra #C-' + datos.id;
      document.getElementById('idMontoRegistrado').textContent      = 'S/ ' + datos.monto;
      document.getElementById('idMontoTotalArticulos').textContent  = 'S/ ' + datos.total_articulos;
      document.getElementById('idCantidadArticulos').textContent    = datos.articulos.length + ' items';
      document.getElementById('idProveedorNombre').textContent      = datos.proveedor;
      document.getElementById('docProveedor').textContent           = datos.ruc_proveedor;
      document.getElementById('idUsuario').textContent              = datos.usuario;
      document.getElementById('idFechaComprav2').textContent        = datos.fecha_compra;
      document.getElementById('idFechaRegistro').textContent        = datos.fecha_registro;
      document.getElementById('idHoraRegistro').textContent         = datos.hora_registro;
      document.getElementById('idTotalDetalleCompra').textContent   = 'S/ ' + datos.total_articulos;
      setProveedorAvatar(datos.proveedor);

      const ul = document.getElementById('idContenidoUlDetalle');
      ul.innerHTML = '';
      datos.articulos.forEach((art, i) => {
        const isLast = i === datos.articulos.length - 1;
        ul.innerHTML += `
          <li class="py-2 d-flex justify-content-between align-items-center ${isLast ? '' : 'border-bottom'}">
            <div>
              <p class="mb-0 fw-semibold" style="font-size:13px;">${art.nombre}</p>
              <p class="mb-0 text-muted" style="font-size:11px;">Cód: ${art.codigo} · ${art.cantidad} unid.</p>
            </div>
            <span class="fw-semibold" style="font-size:13px;">S/ ${art.subtotal}</span>
          </li>`;
      });

      new bootstrap.Modal(document.getElementById('modalDetalleCompra')).show();
    }
    */
</script>


<!-- Incluir el CSS de DataTables -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">

<!-- Incluir jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Incluir el JS de DataTables -->
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>




<script>
    const SUCURSAL_ID = <?php echo json_encode($sucursal_id); ?>;
    console.log("🏢 Sucursal ID activa:", SUCURSAL_ID);
    var variable_global_js_articulo;
    var listaArticulosCantidades = [];
    $(document).ready(function() {
        var selectedIndexProveedor = -1; // Índice de la sugerencia seleccionada para proveedor
        var selectedIndexArticulo = -1; // Índice de la sugerencia seleccionada para artículos

        // Búsqueda de proveedores (campo de texto para proveedor)
        $('#proveedor').on('input', function() {
            var busqueda = $(this).val();
            if (busqueda.length >= 2) { // Si hay al menos 2 caracteres
                $.ajax({
                    url: 'logica/clssConsultas.php',
                    type: 'POST',
                    data: {
                        accion: "BUSQUEDAD_PROVEEDOR",
                        cadenaBusqueda: busqueda, // El valor de lo que escribe el usuario
                        sucursal_id: SUCURSAL_ID
                    },
                    dataType: 'json',
                    success: function(data) {
                        var suggestions = '';
                        $.each(data, function(index, proveedor) {
                            suggestions += '<li class="list-group-item list-group-item-action suggestion-item-proveedor" data-id="' + proveedor.id + '" data-name="' + proveedor.nombre_comercial + '">' + proveedor.nombre_comercial + '</li>';
                        });

                        $('#suggestions').html(suggestions);
                        selectedIndexProveedor = -1; // Resetea el índice de selección
                    }
                });
            } else {
                $('#suggestions').html('');
            }
        });

        // Manejo de clic en proveedor (asignar solo en proveedor)
        $(document).on('click', '.suggestion-item-proveedor', function() {
            var selectedId = $(this).data('id');
            var selectedName = $(this).data('name');

            $('#proveedor').val(selectedName); // Pasa el nombre al campo proveedor
            $('#proveedor_id').val(selectedId); // Guarda el ID del proveedor
            $('#suggestions').html(''); // Limpiar las sugerencias
        });

        // Manejo de teclas para proveedor (solo si el foco está en #proveedor)
        $('#proveedor').on('keydown', function(e) {
            var items = $('.suggestion-item-proveedor');
            if (items.length > 0) {
                // Flecha hacia abajo
                if (e.keyCode === 40) {
                    selectedIndexProveedor = (selectedIndexProveedor + 1) % items.length; // Avanza el índice
                    highlightItemProveedor();
                }
                // Flecha hacia arriba
                else if (e.keyCode === 38) {
                    selectedIndexProveedor = (selectedIndexProveedor - 1 + items.length) % items.length; // Retrocede el índice
                    highlightItemProveedor();
                }
                // Tecla Enter (si el proveedor tiene seleccionado un item)
                else if (e.keyCode === 13 && selectedIndexProveedor >= 0) {
                    var selectedItem = items.eq(selectedIndexProveedor);
                    var selectedId = selectedItem.data('id');
                    var selectedName = selectedItem.data('name');

                    $('#proveedor').val(selectedName); // Pasa el nombre al campo proveedor
                    $('#proveedor_id').val(selectedId); // Guarda el ID del proveedor
                    $('#suggestions').html(''); // Limpiar las sugerencias
                }
            }
        });

        // Resaltar el item seleccionado para proveedor
        function highlightItemProveedor() {
            $('.suggestion-item-proveedor').removeClass('active'); // Elimina la clase "active" de todos los items
            var selectedItem = $('.suggestion-item-proveedor').eq(selectedIndexProveedor);
            selectedItem.addClass('active'); // Agrega la clase "active" al item seleccionado
        }



        // Inicialización de índice seleccionado
        var selectedIndexArticulo = -1;

        // Búsqueda de artículos (campo de texto para artículos)
        $('#idBuscarArticulos').on('input', function() {
            var busqueda = $(this).val();
            console.log(SUCURSAL_ID);
            if (busqueda.length >= 2) { // Si hay al menos 2 caracteres
                $.ajax({
                    url: 'logica/clssConsultas.php',
                    type: 'POST',
                    data: {
                        accion: "BUSQUEDAD_FILTRO_ARTICULOS",
                        cadenaBusqueda: busqueda, // El valor de lo que escribe el usuario
                        sucursal_id: SUCURSAL_ID

                    },
                    dataType: 'json',
                    success: function(data) {
                        console.log(data)
                        var suggestions = '';
                        $.each(data, function(index, articulo) {
                            var articuloJson = JSON.stringify(articulo);
                            var articuloJsonEscapado = encodeURIComponent(articuloJson);
                            suggestions += '<li class="list-group-item list-group-item-action suggestion-item-articulo" data-json-articulo="' + articuloJsonEscapado + '" data-id="' + articulo.id + '" data-name="' + articulo.articulo_formato + '">' + articulo.articulo_formato + '</li>';
                        });

                        $('#listadoArticulosBusqueda').html(suggestions);
                        selectedIndexArticulo = -1; // Resetea el índice de selección
                    }
                });
            } else {
                $('#listadoArticulosBusqueda').html('');
            }
        });

        // Manejo de clic en artículo (asignar solo en artículo)
        $(document).on('click', '.suggestion-item-articulo', function() {
            var selectedId = $(this).data('id');
            var selectedName = $(this).data('name');
            var articuloJsonString = decodeURIComponent($(this).data('json-articulo'));
            var jsArticulo = JSON.parse(articuloJsonString);
            variable_global_js_articulo = jsArticulo;
            //idBuscarArticulos
            $('#idBuscarArticulos').val(selectedName); // Pasa el nombre al campo idBuscarArticulos
            $('#idArticuloEncontrado').val(selectedId); // Guarda el ID del artículo
            $('#listadoArticulosBusqueda').html(''); // Limpiar las sugerencias

            console.log("jsdeMrd: " + variable_global_js_articulo);
        });

        // Manejo de teclas para artículos (solo si el foco está en #idBuscarArticulos)
        $('#idBuscarArticulos').on('keydown', function(e) {
            var items = $('.suggestion-item-articulo');

            if (items.length > 0) {
                // Flecha hacia abajo
                if (e.keyCode === 40) {
                    selectedIndexArticulo = (selectedIndexArticulo + 1) % items.length; // Avanza el índice
                    highlightItemArticulo(); // Resalta el artículo
                }
                // Flecha hacia arriba
                else if (e.keyCode === 38) {
                    selectedIndexArticulo = (selectedIndexArticulo - 1 + items.length) % items.length; // Retrocede el índice
                    highlightItemArticulo(); // Resalta el artículo
                }
                // Tecla Enter (si el artículo tiene seleccionado un item)
                else if (e.keyCode === 13 && selectedIndexArticulo >= 0) {
                    e.preventDefault(); // Prevenir el comportamiento predeterminado (enviar formulario)

                    var selectedItem = items.eq(selectedIndexArticulo); // Asegurarse de que selecciona el item resaltado
                    var selectedId = selectedItem.data('id');
                    var selectedName = selectedItem.data('name');

                    // Obtener el JSON del artículo seleccionado
                    var articuloJsonStringTecl = decodeURIComponent(selectedItem.data('json-articulo'));
                    var jsArticuloTecl = JSON.parse(articuloJsonStringTecl);
                    variable_global_js_articulo = jsArticuloTecl;

                    // Actualizar los campos con la información del artículo seleccionado
                    $('#idBuscarArticulos').val(selectedName);
                    $('#idArticuloEncontrado').val(selectedId);
                    $('#listadoArticulosBusqueda').html(''); // Limpiar las sugerencias
                }
            }
        });

        // Resaltar el item seleccionado para artículos
        function highlightItemArticulo() {
            $('.suggestion-item-articulo').removeClass('active'); // Elimina la clase "active" de todos los items
            var selectedItem = $('.suggestion-item-articulo').eq(selectedIndexArticulo);
            selectedItem.addClass('active'); // Agrega la clase "active" al item seleccionado
        }

    });
    // ── Cargar locaciones en los selectores de destino ──────────
    $.ajax({
        url: 'logica/clssInventario.php',
        type: 'POST',
        data: {
            accion: 'LISTAR_LOCACIONES',
            sucursal_id: SUCURSAL_ID
        },
        dataType: 'json',
        success: function(res) {
            if (res.success && res.data.length > 0) {
                var opts = '<option value="">Seleccione locación...</option>';
                res.data.forEach(function(loc) {
                    opts += '<option value="' + loc.id + '">[' + loc.tipo + '] ' + loc.nombre + '</option>';
                });
                $('#ca-idLocacion, #caj-idLocacion').html(opts);
            }
        }
    });

    // ── Cascada: al cambiar locación, cargar estructuras ─────────
    $(document).on('change', '#ca-idLocacion', function() {
        fnCargarEstructuras($(this).val(), '#ca-idEstructura');
    });
    $(document).on('change', '#caj-idLocacion', function() {
        fnCargarEstructuras($(this).val(), '#caj-idEstructura');
    });

    function fnCargarEstructuras(locacion_id, targetSel) {
        $(targetSel).html('<option value="">— Sin estructura —</option>');
        if (!locacion_id) return;
        $.ajax({
            url: 'logica/clssInventario.php',
            type: 'POST',
            data: {
                accion: 'LISTAR_ESTRUCTURAS',
                locacion_id: locacion_id
            },
            dataType: 'json',
            success: function(res) {
                if (res.success && res.data.length > 0) {
                    res.data.forEach(function(est) {
                        $(targetSel).append(
                            '<option value="' + est.id + '">[' + est.tipo + '] ' + est.nombre + '</option>'
                        );
                    });
                }
            }
        });
    }
</script>

<!-- vamos hacer unos calculos de mrd -->
<script>
    $(document).ready(function() {
        $(document).on('input', '#ca-idCantidadArticulos, #ca-idTotalCompraArticulo', function() {
            var cantidadArticulos = parseFloat($("#ca-idCantidadArticulos").val());
            var totalCompraArticulo = parseFloat($("#ca-idTotalCompraArticulo").val());

            if (!isNaN(cantidadArticulos) && !isNaN(totalCompraArticulo)) {
                var resultado = totalCompraArticulo / cantidadArticulos;
                console.log(resultado);
                $("#ca-idPrecioCalculado").val(resultado.toFixed(4)); // Cambiado de 2 a 4
            } else {
                $("#ca-idPrecioCalculado").val("0.0000"); // Cambiado de 0.00 a 0.0000
            }
        });
    });
    ////////////////////////////////////////////////////
    $(document).ready(function() {
        $(document).on('input', '#caj-idCantidadCajas, #caj-idUnidadesPorCaja, #caj-idPrecioUnitarioCaja', function() {
            var cantidadCajas = parseFloat($("#caj-idCantidadCajas").val());
            var unidadesPorCaja = parseFloat($("#caj-idUnidadesPorCaja").val());
            var precioUnitarioCaja = parseFloat($("#caj-idPrecioUnitarioCaja").val());

            if (!isNaN(cantidadCajas) && !isNaN(unidadesPorCaja) && !isNaN(precioUnitarioCaja)) {
                var totalCajas = precioUnitarioCaja * cantidadCajas;
                var unidadesTotales = unidadesPorCaja * cantidadCajas;
                var puCalculado = totalCajas / unidadesTotales;

                $("#caj-idTotalCajas").val(totalCajas.toFixed(4)); // Cambiado de 2 a 4
                $("#caj-idUnidadesCalculadas").val(unidadesTotales.toFixed(4)); // Cambiado de 2 a 4
                $("#caj-idPrecioUnitarioCalculado").val(puCalculado.toFixed(4)); // Cambiado de 2 a 4
            } else {
                $("#caj-idUnidadesCalculadas").val("0.0000");
                $("#caj-idPrecioUnitarioCalculado").val("0.0000");
                $("#caj-idTotalCajas").val("0.0000");
            }
        });
    });
</script>
<script>
    function showNotification(estado, comentario) {
        // Configurar el contenido de la notificación
        var content = {};
        var state = ""; // Tipo de estado (success, error, etc.)
        var placementFrom = "bottom"; // Posición vertical
        var placementAlign = "right"; // Posición horizontal

        // Personalizar contenido según el estado
        switch (estado) {
            case 'Agregar':
                content.message = comentario;
                content.title = "¡Agregado con Éxito!";
                content.icon = "fa fa-check-circle"; // Ícono para éxito
                state = "success";
                break;
            case 'Eliminar':
                content.message = "Operación exitosa.";
                content.title = "¡Eliminado!";
                content.icon = "fa fa-times-circle"; // Ícono para éxito
                state = "danger";
                break;
            case 'success':
                content.message = "Operación exitosa.";
                content.title = "¡Éxito!";
                content.icon = "fa fa-check-circle"; // Ícono para éxito
                state = "success";
                break;
            case 'error':
                content.message = "Ocurrió un error inesperado.";
                content.title = "¡Error!";
                content.icon = "fa fa-times-circle"; // Ícono para error
                state = "danger";
                break;
            case 'warning':
                content.message = "Advertencia: Revisa los datos.";
                content.title = "¡Advertencia!";
                content.icon = "fa fa-exclamation-triangle"; // Ícono para advertencia
                state = "warning";
                break;
            case 'info':
                content.message = "Este es un mensaje informativo.";
                content.title = "Información";
                content.icon = "fa fa-info-circle"; // Ícono para información
                state = "info";
                break;
            default:
                content.message = "Estado desconocido.";
                content.title = "Notificación";
                content.icon = "fa fa-question-circle"; // Ícono por defecto
                state = "info";
        }

        // Mostrar la notificación
        $.notify(content, {
            type: state, // Estado
            placement: {
                from: placementFrom, // Posición vertical
                align: placementAlign // Posición horizontal
            },
            time: 1000, // Duración de la animación
            delay: 3000, // Tiempo para ocultar (ms)
        });
    }
</script>
<script>
    function fn_agregar_cantidad_exacta() {
        try {
            var articulo_id = document.getElementById("idArticuloEncontrado").value;
            var articulo = document.getElementById("idBuscarArticulos").value;
            var cantidad = document.getElementById("ca-idCantidadArticulos").value;
            var total_compra = document.getElementById("ca-idTotalCompraArticulo").value;
            var precio_calculado = document.getElementById("ca-idPrecioCalculado").value;
            var precio_venta = parseFloat(document.getElementById("ca-idPrecioVenta").value).toFixed(4);

            // ── Destino de inventario ────────────────────────────────
            var locacion_id = document.getElementById("ca-idLocacion").value;
            var locacion_texto = $('#ca-idLocacion option:selected').text();
            var estructura_id = document.getElementById("ca-idEstructura").value || null;
            var estructura_txt = estructura_id ? $('#ca-idEstructura option:selected').text() : '';

            console.log("Precio de Venta:", precio_venta);

            if (variable_global_js_articulo === null && (isNaN(precio_venta) || precio_venta === "")) {
                swal("Error", "El producto no tiene precio de venta (S/)", {
                    icon: "error",
                    buttons: {
                        confirm: {
                            className: "btn btn-danger"
                        }
                    }
                });
                return;
            }

            if (precio_venta === "" || isNaN(precio_venta)) {
                precio_venta = variable_global_js_articulo.precio_venta;
            }

            if (precio_venta === null) {
                swal("Error", "El producto no tiene precio de venta (S/)", {
                    icon: "error",
                    buttons: {
                        confirm: {
                            className: "btn btn-danger"
                        }
                    }
                });
                return;
            }

            // ── Validaciones ─────────────────────────────────────────
            if (articulo_id === "" || isNaN(cantidad) || isNaN(total_compra) || isNaN(precio_calculado)) {
                alert("Por favor, completa todos los campos del artículo.");
                return;
            }

            if (!locacion_id) {
                swal("Upps", "Debes seleccionar una locación destino para el artículo.", {
                    icon: "warning",
                    buttons: {
                        confirm: {
                            className: "btn btn-warning"
                        }
                    }
                });
                return;
            }

            // ── Armar objeto ─────────────────────────────────────────
            var js_cantidad_exacta = {
                "articulo_id": articulo_id,
                "cantidad_": cantidad,
                "precio_unitario_": precio_calculado,
                "sub_total_": total_compra,
                "precio_venta_": precio_venta,
                // destino
                "locacion_id": locacion_id,
                "locacion_texto": locacion_texto,
                "estructura_id": estructura_id,
                "estructura_txt": estructura_txt,
                // flags
                "ca": "si",
                "ca-cantidad_exacta": "si",
                "ca-cantidad_articulos": cantidad,
                "ca-total_compra": total_compra,
                "ca-precio_unitario_calculado_articulo": precio_calculado,
                "ca-precio_venta": precio_venta,
                "caja": "no",
                "caj-caja": "no",
                "caj-cantidad_cajas": 0,
                "caj-unidades_por_caja": 0,
                "caj-precio_unitario_de_caja": 0,
                "caj-total": 0,
                "caj-precio_unitario_articulo_calculado": 0,
                "caj-unidades_calculadas": 0,
                "caj-precio_venta_por_articulo": 0,
                "json_producto": variable_global_js_articulo
            };

            // ── Formatear nombre del artículo ────────────────────────
            var articuloJsFormat = variable_global_js_articulo === null ?
                document.getElementById("idBuscarArticulos").value :
                variable_global_js_articulo.articulo +
                " | Tipo: " + variable_global_js_articulo.tipo +
                " | Dimensión: " + variable_global_js_articulo.dimension;

            // ── Badge de destino ─────────────────────────────────────
            var destinoBadge =
                '<span class="badge bg-info text-dark ms-1">' +
                '<i class="fas fa-map-marker-alt me-1"></i>' +
                locacion_texto +
                (estructura_id ? ' / ' + estructura_txt : '') +
                '</span>';

            // ── Insertar fila en la tabla ────────────────────────────
            var tablita = document.getElementById("idTablitaCompra").getElementsByTagName('tbody')[0];
            var newRow = tablita.insertRow(tablita.rows.length);

            var cell1 = newRow.insertCell(0); // ID
            var cell2 = newRow.insertCell(1); // Artículo + destino
            var cell3 = newRow.insertCell(2); // Cantidad
            var cell4 = newRow.insertCell(3); // Precio Unitario
            var cell5 = newRow.insertCell(4); // Sub Total
            var cell6 = newRow.insertCell(5); // Precio Venta
            var cell7 = newRow.insertCell(6); // Botón eliminar

            var btnEliminar = document.createElement("button");
            btnEliminar.innerHTML = '<i class="fas fa-times"></i>';
            btnEliminar.classList.add("btn", "btn-danger", "btn-round", "btn-sm");
            btnEliminar.onclick = function() {
                var row = this.closest('tr');
                var indice = Array.from(tablita.rows).indexOf(row);
                row.remove();
                listaArticulosCantidades.splice(indice, 1);
                console.log("Lista actualizada:", listaArticulosCantidades);
                showNotification('Eliminar');
            };

            cell1.innerHTML = articulo_id;
            cell2.innerHTML = articuloJsFormat +
                ' <span class="badge bg-info text-dark ms-1" title="Destino">' +
                locacion_texto + (estructura_id ? ' / ' + estructura_txt : '') +
                '</span>';
            cell3.innerHTML = cantidad;
            cell4.innerHTML = precio_calculado;
            cell5.innerHTML = (cantidad * precio_calculado).toFixed(4);
            cell6.innerHTML = precio_venta;
            cell7.appendChild(btnEliminar);

            listaArticulosCantidades.push(js_cantidad_exacta);

            showNotification('Agregar',
                variable_global_js_articulo === null ?
                'Artículo agregado' :
                'Artículo ' + variable_global_js_articulo.articulo
            );

            console.log("Lista actualizada:", listaArticulosCantidades);

            // ── Limpiar campos ───────────────────────────────────────
            document.getElementById("idBuscarArticulos").value = "";
            document.getElementById("idArticuloEncontrado").value = "";
            document.getElementById("ca-idCantidadArticulos").value = "";
            document.getElementById("ca-idTotalCompraArticulo").value = "";
            document.getElementById("ca-idPrecioCalculado").value = "";
            document.getElementById("ca-idPrecioVenta").value = "";
            document.getElementById("ca-idLocacion").value = "";
            document.getElementById("ca-idEstructura").innerHTML =
                '<option value="">— Sin estructura —</option>';
            variable_global_js_articulo = null;

        } catch (error) {
            console.error("Error en fn_agregar_cantidad_exacta:", error);
        }
    }
    //////////////////////////////////////////////////////////////////////////////////
    function fn_agregar_cantidad_cajas() {
        var articulo_id = document.getElementById("idArticuloEncontrado").value;
        var articulo = document.getElementById("idBuscarArticulos").value;
        ////////////////////////////////////////////////////////////////
        var cantidad_cajas = document.getElementById("caj-idCantidadCajas").value;
        var unidades_por_caja = document.getElementById("caj-idUnidadesPorCaja").value;
        var pu_caja = document.getElementById("caj-idPrecioUnitarioCaja").value;
        var total_calculado_cajas = document.getElementById("caj-idTotalCajas").value;

        var pu_calculado_articulo_x_caja = document.getElementById("caj-idPrecioUnitarioCalculado").value;
        var unidades_calculadas = document.getElementById("caj-idUnidadesCalculadas").value;

        var locacion_id = document.getElementById("caj-idLocacion").value;
        var locacion_text = $('#caj-idLocacion option:selected').text();
        var estructura_id = document.getElementById("caj-idEstructura").value || null;
        var estructura_txt = $('#caj-idEstructura option:selected').text();

        if (!locacion_id) {
            alert("Debes seleccionar una locación destino.");
            return;
        }



        if (articulo_id === "" || isNaN(cantidad_cajas) || isNaN(unidades_por_caja) || isNaN(pu_caja)) {
            alert("Por favor, completa todos los campos.");
            return; // Salir de la función si algún campo está vacío
        }
        var precio_venta_articulos = parseFloat(document.getElementById("caj-idPrecioVenta").value);


        console.log("Precio de Venta " + precio_venta_articulos);

        if (variable_global_js_articulo === null && (isNaN(precio_venta_articulos) || precio_venta_articulos === "")) {
            swal("Error", "El Articulo no tiene precio de venta (S/)", {
                icon: "error",
                buttons: {
                    confirm: {
                        className: "btn btn-danger",
                    },
                },
            });
        } else {
            if (precio_venta_articulos === "" || isNaN(precio_venta_articulos)) {
                precio_venta_articulos = variable_global_js_articulo.precio_venta;
            }
            if (precio_venta_articulos === null) {
                console.log("Es nuloooo como tu culoo")
                swal("Error", "El Articulo no tiene precio de venta (S/)", {
                    icon: "error",
                    buttons: {
                        confirm: {
                            className: "btn btn-danger",
                        },
                    },
                });
            } else {
                console.log("Variable Global de mrd:");
                console.log(variable_global_js_articulo);



                var js_cantidad_cajas = {
                    "articulo_id": articulo_id,
                    "cantidad_": unidades_calculadas,
                    "precio_unitario_": pu_calculado_articulo_x_caja,
                    "sub_total_": total_calculado_cajas,
                    "precio_venta_": precio_venta_articulos,
                    "ca": "no",
                    "ca-cantidad_exacta": "no",
                    "ca-cantidad_articulos": 0,
                    "ca-total_compra": 0,
                    "ca-precio_unitario_calculado_articulo": 0,
                    "ca-precio_venta": 0,
                    "caja": "si",
                    "caj-caja": "si",
                    "caj-cantidad_cajas": cantidad_cajas,
                    "caj-unidades_por_caja": unidades_por_caja,
                    "caj-precio_unitario_de_caja": pu_caja,
                    "caj-total": total_calculado_cajas,
                    "caj-precio_unitario_articulo_calculado": pu_calculado_articulo_x_caja,
                    "caj-unidades_calculadas": unidades_calculadas,
                    "caj-precio_venta_por_articulo": precio_venta_articulos,
                    "json_producto": variable_global_js_articulo,
                    // En js_cantidad_cajas, agrega:
                    "locacion_id": locacion_id,
                    "locacion_texto": locacion_text,
                    "estructura_id": estructura_id,
                    "estructura_txt": estructura_txt
                };


                var articuloJsFormat;
                if (variable_global_js_articulo === null) {

                    articuloJsFormat = document.getElementById("idBuscarArticulos").value;
                } else {
                    articuloJsFormat = variable_global_js_articulo.articulo + " | Tipo: " + variable_global_js_articulo.tipo + " | Dimesion: " + variable_global_js_articulo.dimension;
                }


                var tablita = document.getElementById("idTablitaCompra").getElementsByTagName('tbody')[0];
                var newRow = tablita.insertRow(tablita.rows.length);
                var cell1 = newRow.insertCell(0); // ID
                var cell2 = newRow.insertCell(1); // Artículo
                var cell3 = newRow.insertCell(2); // Cantidad
                var cell4 = newRow.insertCell(3); // Precio Unitario
                var cell5 = newRow.insertCell(4); // Sub Total
                var cell6 = newRow.insertCell(5); // Precio Venta
                var cell7 = newRow.insertCell(6); // Celda para el botón

                ////////////////////////////////////////////////////////
                var btnEliminar = document.createElement("button");
                //btn btn-success btn-round
                btnEliminar.innerHTML = '<i class="fas fa-trash"></i>';
                btnEliminar.classList.add("btn", "btn-danger", "btn-round", "btn-sm");

                // Cambiar la forma en que se llama a la función eliminarFilaDeMrd
                btnEliminar.onclick = function() {

                    var row = this.closest('tr'); // Obtiene la fila más cercana al botón
                    row.remove(); // Elimina la fila del DOM
                    ///////////////////////////////7
                    var indiceFila = row.rowIndex;
                    listaArticulosCantidades.splice(indiceFila, 1);
                    console.log("Lista de mrd de Json Cantidad de Exacta");
                    console.log(listaArticulosCantidades);
                    showNotification('eliminar');

                };
                //////////////////////////////////////////////////////

                // Insertar los valores en las celdas
                cell1.innerHTML = articulo_id;
                cell2.innerHTML = articuloJsFormat;
                cell3.innerHTML = unidades_calculadas;
                cell4.innerHTML = pu_calculado_articulo_x_caja;
                cell5.innerHTML = (pu_calculado_articulo_x_caja * unidades_calculadas).toFixed(4); // Sub Total = Cantidad * Precio Unitario
                cell6.innerHTML = precio_venta_articulos;
                cell7.appendChild(btnEliminar);


                if (variable_global_js_articulo === null) {
                    console.log("Estoy Auqiiiaoaia")
                    showNotification('Agregar', 'Agregado con Exito !!!');
                } else {
                    showNotification('Agregar', 'Articulo ' + variable_global_js_articulo.articulo);
                }

                ///////////////////////////
                listaArticulosCantidades.push(js_cantidad_cajas);

                console.log(listaArticulosCantidades);

                document.getElementById("idBuscarArticulos").value = "";
                document.getElementById("caj-idCantidadCajas").value = "";
                document.getElementById("caj-idUnidadesPorCaja").value = "";
                document.getElementById("caj-idPrecioUnitarioCaja").value = "";
                document.getElementById("caj-idTotalCajas").value = "";

                ///////////////////////////////////////////////////////////////

                document.getElementById("caj-idPrecioUnitarioCalculado").value = "";
                document.getElementById("caj-idUnidadesCalculadas").value = "";
                document.getElementById("caj-idPrecioVenta").value = "";
                document.getElementById("caj-idLocacion").value = "";
                document.getElementById("caj-idEstructura").innerHTML = '<option value="">— Sin estructura —</option>';

            }

        }

    }
</script>



<script>
    function abriModalRegistroCompra() {
        $('#modalRegistroCompra').modal('show');

        fnAbrirModalRegistroArticulos()
    }

    function fnAbrirModalRegistroArticulos() {
        $('#modalRegistroArticulos').modal('show');
    }

    function fnAbrirModalRegistroProveedor() {
        $('#idModalRegistrarProveedor').modal('show');

    }

    //////////////////
    function fnRegistrarProveedor() {
        if ((document.getElementById("idNombreComercialProveedor").value).length <= 0 ||
            document.getElementById("idNombreComercialProveedor").value === "") {
            swal("Upps", "Debes de ingresar el nombre comercial del proveedor 😥", {
                icon: "error",
                buttons: {
                    confirm: {
                        className: "btn btn-danger",
                    },
                },
            });
        } else if ((document.getElementById("idRucProveedor").value).length <= 0 ||
            document.getElementById("idNombreComercialProveedor").value === "") {
            swal("Upps", "Debes de ingresar el RUC del Proveedor 😥", {
                icon: "error",
                buttons: {
                    confirm: {
                        className: "btn btn-danger",
                    },
                },
            });
        } else {
            var jsDatosProveedor = {
                "sucursal_id": SUCURSAL_ID, // ✅ AGREGAR ESTE CAMPO
                "nombre_comercial": document.getElementById("idNombreComercialProveedor").value,
                "razon_social": document.getElementById("idRazonSocialProveedor").value,
                "numero_documento": document.getElementById("idRucProveedor").value,
                "telefonofijo": document.getElementById("idNumTelefonoFijoProveedor").value === "" ?
                    null : document.getElementById("idNumTelefonoFijoProveedor").value,
                "telefonomovil": document.getElementById("idNumCelularProveedor").value === "" ?
                    null : document.getElementById("idNumCelularProveedor").value,
                "email": document.getElementById("idCorreoProveedor").value === "" ?
                    null : document.getElementById("idCorreoProveedor").value,
                "tipo_persona": "JURIDICA",
                "condicion": "PROVEEDOR"
            };

            console.log("📤 Datos de proveedor:", jsDatosProveedor);

            $.ajax({
                url: 'logica/clssInsertPA.php',
                type: 'POST',
                data: {
                    accion: 'INSERTPROVEEDORALMOMENTODECOMPRA',
                    jsDatosProveedor: JSON.stringify(jsDatosProveedor)
                },
                beforeSend: function() {
                    swal({
                        title: "Guardando...",
                        text: "Registrando proveedor",
                        icon: "info",
                        buttons: false,
                        closeOnClickOutside: false
                    });
                },
                success: function(response) {
                    console.log("📥 Respuesta del servidor:", response);

                    try {
                        var result = JSON.parse(response);
                        if (result.estado === true) {
                            document.getElementById("proveedor").value = result.proveedor;
                            document.getElementById("proveedor_id").value = result.ultimo_id_proveedor;

                            swal({
                                title: "¡Proveedor Registrado!",
                                text: result.mensaje,
                                icon: "success",
                                buttons: false,
                                timer: 1500
                            }).then(() => {
                                $('#idModalRegistrarProveedor').modal('hide');
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
                        console.error("❌ Error al parsear JSON:", e);
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
                    console.error("❌ Error:", error);
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
    //////////////
    function fn_registrar_articulo() {
        if ((document.getElementById("idRegistroNombreArticulo").value).length > 0) {
            let categoriaSelect = document.getElementById("idRegistoCategoria");
            let categoria = categoriaSelect.selectedIndex === 0 ? null : categoriaSelect.value;

            let tipoSelect = document.getElementById("idRegistoTipo");
            let tipo = tipoSelect.selectedIndex === 0 ? null : tipoSelect.value;

            let dimensionSelect = document.getElementById("idRegistroDimension");
            let dimension = dimensionSelect.selectedIndex === 0 ? null : dimensionSelect.value;

            let escalaSelect = document.getElementById("idRegistroEscala");
            let escala = escalaSelect.selectedIndex === 0 ? null : escalaSelect.value;

            let radios = document.getElementsByName("flexRadioDefault");
            let selectedValue = "";
            for (let i = 0; i < radios.length; i++) {
                if (radios[i].checked) {
                    selectedValue = radios[i].value;
                    break;
                }
            }
            let corte = selectedValue === "Si" ? true : false;

            let colorEscrito = document.getElementById("idRegistroColor").value;
            let color = (colorEscrito).length > 0 ? colorEscrito : null;

            let marcaEscrita = document.getElementById("idRegistroMarca").value;
            let marca = (marcaEscrita).length > 0 ? marcaEscrita : null;

            var jsArticulo = {
                "sucursal_id": SUCURSAL_ID, // ✅ AGREGAR ESTE CAMPO
                "nombre": document.getElementById("idRegistroNombreArticulo").value,
                "categoria_id": categoria,
                "tipo_id": tipo,
                "dimension_id": dimension,
                "escala_id": escala,
                "corte": corte,
                "color": color,
                "marca": document.getElementById("idRegistroMarca").value
            };

            console.log("📤 Datos de artículo:", jsArticulo);

            $.ajax({
                url: 'logica/clssInsertPA.php',
                type: 'POST',
                data: {
                    accion: 'REGISTAR_ARTICULO',
                    jsDatosArticulo: JSON.stringify(jsArticulo)
                },
                beforeSend: function() {
                    swal({
                        title: "Guardando...",
                        text: "Registrando artículo",
                        icon: "info",
                        buttons: false,
                        closeOnClickOutside: false
                    });
                },
                success: function(response) {
                    console.log("📥 Respuesta del servidor:", response);

                    try {
                        var result = JSON.parse(response);
                        if (result.estado === true) {
                            swal({
                                title: "¡Registrado con Éxito!",
                                text: result.mensaje,
                                icon: "success",
                                buttons: false,
                                timer: 1500
                            }).then(() => {
                                variable_global_js_articulo = null;
                                $('#idBuscarArticulos').val(result.articulo_formato);
                                $('#idArticuloEncontrado').val(result.ultimo_id);

                                // Limpiar formulario
                                document.getElementById("idRegistoCategoria").selectedIndex = 0;
                                document.getElementById("idRegistoTipo").selectedIndex = 0;
                                document.getElementById("idRegistroDimension").selectedIndex = 0;
                                document.getElementById("idRegistroEscala").selectedIndex = 0;
                                document.getElementById("idRegistroColor").value = "";
                                document.getElementById("idRegistroMarca").value = "";
                                document.getElementById("idRegistroNombreArticulo").value = "";

                                $('#modalRegistroArticulos').modal('hide');
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
                        console.error("❌ Error al parsear JSON:", e);
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
                    console.error("❌ Error:", error);
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
            swal("Ups!, Debes de ingresar el nombre del Artículo 😩", {
                icon: "error",
                buttons: {
                    confirm: {
                        className: "btn btn-danger",
                    },
                },
            });
        }
    }
</script>

<script>
    function fn_registrar_compra() {
        var json_compra = {
            sucursal_id: SUCURSAL_ID, // ✅ AGREGAR ESTE CAMPO
            usuario_id: parseInt(document.getElementById("idUsuarioCompra").innerText),
            proveedor_id: parseInt(document.getElementById("proveedor_id").value),
            fecha: document.getElementById("idFechaCompra").value,
            numero_comprobante: document.getElementById("idCompraNumComprabante").value,
            total: parseFloat(document.getElementById("idCompraTotalDeCompra").value),
            js_detalle_compra: listaArticulosCantidades,
        };

        console.log("📤 Datos de compra a enviar:", json_compra);
        console.log("📋 Detalle de compra:", listaArticulosCantidades);

        if (document.getElementById("idFechaCompra").value === "" ||
            (document.getElementById("idFechaCompra").value).length === 0) {
            swal("Ups!", "Necesitas ingresar la fecha de compra para realizar el registro.", {
                icon: "error",
                buttons: {
                    confirm: {
                        className: "btn btn-danger",
                    },
                },
            });
        } else {
            $.ajax({
                url: 'logica/clssInsertPA.php',
                type: 'POST',
                data: {
                    accion: 'REGISTRAR_COMPRA',
                    jsDatosCompra: JSON.stringify(json_compra)
                },
                beforeSend: function() {
                    swal({
                        title: "Guardando...",
                        text: "Registrando compra, por favor espere",
                        icon: "info",
                        buttons: false,
                        closeOnClickOutside: false,
                        closeOnEsc: false
                    });
                },
                success: function(response) {
                    console.log("📥 Respuesta del servidor:", response);

                    try {
                        var result = JSON.parse(response);
                        if (result.estado === true) {
                            swal({
                                title: "¡Compra Registrada!",
                                text: "Compra registrada junto con los artículos",
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
                        console.error("❌ Error al parsear JSON:", e);
                        console.error("Respuesta recibida:", response);
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
                    console.error("❌ Error AJAX:", error);
                    console.error("Response:", xhr.responseText);
                    swal("Error", "Hubo un problema con la solicitud: " + error, {
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
</script>

<script>
    function abrirDetalle(json_datos) {
        $('#modalDetalleCompra').modal('show');
        console.log("Datos de compra:", json_datos);

        // ── Header ───────────────────────────────────────────────
        document.getElementById("idMontoVenta").innerText = '#' + (json_datos.compra_id || '');
        document.getElementById("idNumeroCompra").innerText = 'Compra registrada el ' + (json_datos.fecha_registro || '');

        // ── Registro ─────────────────────────────────────────────
        document.getElementById("idUsuario").innerText = json_datos.realizada_por || '—';
        document.getElementById("idFechaComprav2").innerText = json_datos.fecha_compra || '—';
        document.getElementById("idFechaRegistro").innerText = json_datos.fecha_registro || '—';
        document.getElementById("idHoraRegistro").innerText = json_datos.hora || '—';

        // ── Proveedor ────────────────────────────────────────────
        document.getElementById("docProveedor").innerText = json_datos.proveedor_num_doc || '—';
        document.getElementById("idProveedorNombre").innerText = json_datos.nombre_comercial_proveedor || json_datos.proveedor || '—';
        setProveedorAvatar(json_datos.nombre_comercial_proveedor || json_datos.proveedor || '');

        // ── Monto registrado ─────────────────────────────────────
        var montoReg = parseFloat(json_datos.total || 0);
        document.getElementById("idMontoRegistrado").innerText =
            montoReg > 0 ? 'S/ ' + montoReg.toFixed(2) : 'S/ —';

        // ── Detalle de artículos ─────────────────────────────────
        var totalArticulo = 0;
        var tablaFilas = '';
        var cantItems = 0;

        try {
            var detalle = typeof json_datos.js_detalle_compra === 'string' ?
                JSON.parse(json_datos.js_detalle_compra) :
                json_datos.js_detalle_compra;

            cantItems = detalle.length;

            detalle.forEach(function(item, i) {
                totalArticulo += parseFloat(item.sub_total_ || 0);
                var isLast = i === detalle.length - 1;

                var cadenaCantidades;
                if (item.caja === "si") {
                    cadenaCantidades =
                        'Por cajas: ' + item['caj-cantidad_cajas'] + ' cajas × ' +
                        item['caj-unidades_por_caja'] + ' und = <strong>' +
                        item.cantidad_ + ' und</strong> | P.U caja: S/' +
                        item['caj-precio_unitario_de_caja'] + ' | <strong>Total: S/' +
                        parseFloat(item.sub_total_).toFixed(2) + '</strong>';
                } else {
                    cadenaCantidades =
                        item['cantidad_'] + ' und × S/' +
                        item['ca-precio_unitario_calculado_articulo'] +
                        ' = <strong>S/' + parseFloat(item.sub_total_).toFixed(2) + '</strong>';
                }

                var nombreArticulo = item.json_producto ?
                    item.json_producto.articulo :
                    'Artículo';

                tablaFilas +=
                    '<li class="py-2 d-flex justify-content-between align-items-start ' +
                    (isLast ? '' : 'border-bottom') + '">' +
                    '<div>' +
                    '<p class="mb-0 fw-semibold" style="font-size:13px;">' + nombreArticulo + '</p>' +
                    '<p class="mb-0 text-muted" style="font-size:11px;">' + cadenaCantidades + '</p>' +
                    '</div>' +
                    '<span class="fw-semibold ms-3" style="font-size:13px;white-space:nowrap;">S/ ' +
                    parseFloat(item.sub_total_).toFixed(2) +
                    '</span>' +
                    '</li>';
            });

        } catch (e) {
            console.error("Error parseando detalle:", e);
            tablaFilas = '<li class="py-2 text-muted">No se pudo cargar el detalle.</li>';
        }

        document.getElementById("idContenidoUlDetalle").innerHTML = tablaFilas;

        // ── Tarjetas métricas ────────────────────────────────────
        document.getElementById("idMontoTotalArticulos").innerText = 'S/ ' + totalArticulo.toFixed(2);
        document.getElementById("idCantidadArticulos").innerText = cantItems + (cantItems === 1 ? ' item' : ' items');
        document.getElementById("idTotalDetalleCompra").innerText = 'S/ ' + totalArticulo.toFixed(2);
    }
</script>
<!-- ============================================================
     JAVASCRIPT DE FILTROS - agregar al final de la página,
     junto con los demás bloques <script> existentes
     ============================================================ -->
<script>
    // ── Estado del DataTable ─────────────────────────────────────
    var dtCompras = null;

    $(document).ready(function() {
        fnCargarStats();
        fnCargarTablaInicial();
    });

    function fnCargarTablaInicial() {
        $.ajax({
            url: 'logica/clssFiltrosCompras.php',
            type: 'POST',
            data: {
                accion: 'FILTRAR_COMPRAS',
                sucursal_id: SUCURSAL_ID
            },
            dataType: 'json',
            success: function(data) {
                fnRenderizarTabla(data);
            },
            error: function(xhr, status, error) {
                console.error('❌ Error AJAX fnCargarTablaInicial:', xhr.responseText);
            }
        });
    }
    // ── Aplicar filtros ──────────────────────────────────────────
    function fnAplicarFiltros() {
        var proveedor = $('#filtro-proveedor').val().trim();
        var usuario = $('#filtro-usuario').val().trim();
        var fechaDesde = $('#filtro-fecha-desde').val();
        var fechaHasta = $('#filtro-fecha-hasta').val();

        // Mostrar spinner en tarjetas
        fnMostrarLoadingStats();

        // Cargar estadísticas
        fnCargarStats(proveedor, usuario, fechaDesde, fechaHasta);

        // Cargar tabla filtrada
        $.ajax({
            url: 'logica/clssFiltrosCompras.php',
            type: 'POST',
            data: {
                accion: 'FILTRAR_COMPRAS',
                sucursal_id: SUCURSAL_ID,
                proveedor: proveedor,
                usuario: usuario,
                fecha_desde: fechaDesde,
                fecha_hasta: fechaHasta
            },
            dataType: 'json',
            success: function(data) {
                fnRenderizarTabla(data);
            },
            error: function() {
                console.error('Error al filtrar compras');
            }
        });
    }

    // ── Limpiar filtros ──────────────────────────────────────────
    function fnLimpiarFiltros() {
        $('#filtro-proveedor').val('');
        $('#filtro-usuario').val('');
        $('#filtro-fecha-desde').val('');
        $('#filtro-fecha-hasta').val('');

        fnCargarStats();

        // Recargar datos originales (sin filtros = todos los de la sucursal)
        $.ajax({
            url: 'logica/clssFiltrosCompras.php',
            type: 'POST',
            data: {
                accion: 'FILTRAR_COMPRAS',
                sucursal_id: SUCURSAL_ID
            },
            dataType: 'json',
            success: function(data) {
                fnRenderizarTabla(data);
            }
        });
    }

    // ── Renderizar filas en la tabla ─────────────────────────────
    function fnRenderizarTabla(data) {
        if ($.fn.DataTable.isDataTable('#TablaVentaDiaria')) {
            $('#TablaVentaDiaria').DataTable().destroy();
        }

        var tbody = $('#tbody-compras');
        tbody.empty();

        if (!data || data.length === 0) {
            tbody.append(
                '<tr><td colspan="9" class="text-center text-muted py-3">' +
                '<i class="fas fa-inbox"></i> Sin resultados para los filtros aplicados' +
                '</td></tr>'
            );
        } else {
            $.each(data, function(i, row) {

                // ── Calcular total por productos ─────────────────
                var totalProductos = 0;
                try {
                    var detalle = typeof row.js_detalle_compra === 'string' ?
                        JSON.parse(row.js_detalle_compra) :
                        row.js_detalle_compra;

                    if (Array.isArray(detalle)) {
                        detalle.forEach(function(item) {
                            totalProductos += parseFloat(item.sub_total_ || 0);
                        });
                    }
                } catch (e) {
                    totalProductos = 0;
                }

                // ── Guardar JSON en data-attribute (sin problemas de escape) ──
                var $tr = $('<tr>');
                $tr.append('<td>' + (row.compra_id || '') + '</td>');
                $tr.append('<td>' + (row.realizada_por || '') + '</td>');
                $tr.append('<td>' + (row.proveedor || '') + '</td>');
                $tr.append('<td>' + (row.fecha_compra || '') + '</td>');
                $tr.append('<td>' + (row.total || '') + '</td>');
                $tr.append('<td>S/ ' + totalProductos.toFixed(2) + '</td>');
                $tr.append('<td>' + (row.fecha_registro || '') + '</td>');
                $tr.append('<td>' + (row.hora || '') + '</td>');

                // ── Botón usando data-index para recuperar el objeto ──
                var $btnTd = $('<td><div class="mt-2 text-center"></div></td>');
                var $btn = $('<a class="btn btn-secondary btn-round btn-sm" role="button">' +
                    '<i class="fas fa-external-link-square-alt"></i></a>');

                // Guardar el objeto completo en el elemento con $.data (sin serializar a HTML)
                $btn.data('compra', row);
                $btn.on('click', function() {
                    abrirDetalle($(this).data('compra'));
                });

                $btnTd.find('div').append($btn);
                $tr.append($btnTd);

                tbody.append($tr);
            });
        }

        fnInicializarDTCompras();
    }
    // ── Inicializar / Reinicializar DataTable ────────────────────
    function fnInicializarDTCompras() {
        if ($.fn.DataTable.isDataTable('#TablaVentaDiaria')) {
            $('#TablaVentaDiaria').DataTable().destroy();
        }
        return $('#TablaVentaDiaria').DataTable({
            order: [
                [0, 'desc']
            ],
            pageLength: 10,
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, 'Todos']
            ],
            language: {
                sProcessing: "Procesando...",
                sLengthMenu: "Mostrar _MENU_ registros",
                sZeroRecords: "No se encontraron resultados",
                sEmptyTable: "Ningún dato disponible",
                sInfo: "Mostrando _START_ al _END_ de _TOTAL_ registros",
                sInfoEmpty: "Mostrando 0 registros",
                sInfoFiltered: "(filtrado de _MAX_ registros)",
                sSearch: "Buscar:",
                oPaginate: {
                    sFirst: "Primero",
                    sPrevious: "Anterior",
                    sNext: "Siguiente",
                    sLast: "Último"
                }
            }
        });
    }
    // ── Cargar estadísticas (tarjetas) ───────────────────────────
    function fnCargarStats(proveedor, usuario, fechaDesde, fechaHasta) {
        proveedor = proveedor || '';
        usuario = usuario || '';
        fechaDesde = fechaDesde || '';
        fechaHasta = fechaHasta || '';

        $.ajax({
            url: 'logica/clssFiltrosCompras.php',
            type: 'POST',
            data: {
                accion: 'STATS_COMPRAS',
                sucursal_id: SUCURSAL_ID,
                proveedor: proveedor,
                usuario: usuario,
                fecha_desde: fechaDesde,
                fecha_hasta: fechaHasta
            },
            dataType: 'json',
            success: function(stats) {
                fnActualizarCards(stats);
            },
            error: function() {
                console.error('Error al cargar estadísticas');
            }
        });
    }

    // ── Actualizar tarjetas con los datos recibidos ──────────────
    function fnActualizarCards(stats) {
        var encontradas = parseInt(stats.total_compras_filtrado || 0);
        var totalRango = parseFloat(stats.total_productos_filtrado || 0).toFixed(2); // ← mismo campo
        var totalProd = parseFloat(stats.total_productos_filtrado || 0).toFixed(2);
        var granTotal = parseFloat(stats.gran_total_historico || 0).toFixed(2);

        $('#stat-compras-encontradas').text(encontradas);
        $('#stat-total-rango').text('S/ ' + fnFormatearMonto(totalRango));
        $('#stat-total-productos').text('S/ ' + fnFormatearMonto(totalProd));
        $('#stat-gran-total').text('S/ ' + fnFormatearMonto(granTotal));
    }

    // ── Loading placeholder en tarjetas ─────────────────────────
    function fnMostrarLoadingStats() {
        $('#stat-compras-encontradas').html('<span class="spinner-border spinner-border-sm"></span>');
        $('#stat-total-rango').html('<span class="spinner-border spinner-border-sm"></span>');
        $('#stat-total-productos').html('<span class="spinner-border spinner-border-sm"></span>');
        $('#stat-gran-total').html('<span class="spinner-border spinner-border-sm"></span>');
    }

    // ── Formatea número con separador de miles ───────────────────
    function fnFormatearMonto(num) {
        return parseFloat(num).toLocaleString('es-PE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // ── Permitir buscar con Enter en los inputs de filtro ────────
    $(document).on('keydown', '#filtro-proveedor, #filtro-usuario, #filtro-fecha-desde, #filtro-fecha-hasta', function(e) {
        if (e.key === 'Enter') fnAplicarFiltros();
    });
</script>




<?php
include("pie.php");
?>