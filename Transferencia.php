<?php
include("cabecera.php");
?>

<style>
    label { font-weight: bold; }

    #modalTransferencia {
        z-index: 1060 !important;
    }

    #modalTransferencia .modal-content {
        background-color: #fff;
        border-radius: 10px;
        border: 2px solid #2a2f5b;
    }

    #modalTransferencia .modal-dialog {
        box-shadow: 0 4px 10px #2a2f5b;
        max-width: 650px;
    }

    /* Tarjetas origen / destino */
    .card-ubicacion {
        border: 2px solid #dee2e6;
        border-radius: 10px;
        padding: 15px;
        background: #f8f9fa;
        height: 100%;
    }

    .card-ubicacion.origen  { border-color: #0d6efd; }
    .card-ubicacion.destino { border-color: #198754; }

    .card-ubicacion h6 {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
    }

    .card-ubicacion.origen  h6 { color: #0d6efd; }
    .card-ubicacion.destino h6 { color: #198754; }

    /* Flecha central */
    .flecha-transferencia {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: #6c757d;
        padding-top: 35px;
    }

    /* Badge stock disponible */
    #stockDisponible {
        display: none;
        font-size: 12px;
        margin-top: 5px;
    }
</style>

<div class="container">
    <div class="page-inner">

        <!-- Formulario de transferencia -->
        <div class="card text-start mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title">Transferencia de Stock</h4>
                    <button class="btn btn-primary rounded-5" id="btnNuevaTransferencia">
                        Nueva Transferencia <i class="fas fa-exchange-alt"></i>
                    </button>
                </div>
                <hr>

                <!-- Resumen visual rápido (instrucciones) -->
                <div class="alert alert-info py-2 px-3 mb-0" style="font-size:13px;">
                    <i class="fas fa-info-circle me-1"></i>
                    Selecciona el <strong>origen</strong>, elige el <strong>artículo</strong> con stock disponible,
                    luego indica el <strong>destino</strong> y la <strong>cantidad</strong> a mover.
                    Si el destino ya tiene stock del artículo, se sumará automáticamente.
                </div>
            </div>
        </div>

        <!-- Historial -->
        <div class="card text-start">
            <div class="card-body">
                <h4 class="card-title">Historial de Transferencias</h4>
                <hr>
                <div class="table-responsive">
                    <table id="tbl-historial" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Artículo</th>
                                <th>Origen</th>
                                <th>Destino</th>
                                <th>Cantidad</th>
                                <th>Motivo</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal Transferencia -->
<div class="modal fade" id="modalTransferencia" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="contenidoTransferencia"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
<script src="assets/js/scriptNotify.js"></script>

<script>
const SUCURSAL_ID = "<?php echo $_SESSION['sucursal_id'] ?? ''; ?>";
const URL_LOGICA  = "logica/clssTransferencia.php";

// ================================================
// HISTORIAL DataTable
// ================================================
$(document).ready(function () {
    $("#tbl-historial").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: URL_LOGICA,
            type: "POST",
            data: function (d) {
                d.accion      = "LISTAR_HISTORIAL";
                d.sucursal_id = SUCURSAL_ID;
            },
            error: function (xhr) { console.error("Error:", xhr.responseText); }
        },
        columns: [
            { data: "id" },
            { data: "nombre_articulo" },
            { data: "origen" },
            { data: "destino" },
            { data: "cantidad",  orderable: false },
            { data: "motivo",    orderable: false },
            { data: "fecha" }
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        language: {
            sProcessing:     "Procesando...",
            sLengthMenu:     "Mostrar _MENU_ registros",
            sZeroRecords:    "Sin transferencias registradas",
            sEmptyTable:     "Sin transferencias aún",
            sInfo:           "Mostrando _START_ al _END_ de _TOTAL_ registros",
            sInfoEmpty:      "Mostrando 0 registros",
            sInfoFiltered:   "(filtrado de _MAX_)",
            sSearch:         "Buscar:",
            sLoadingRecords: "Cargando...",
            oPaginate: { sFirst: "Primero", sPrevious: "Anterior", sNext: "Siguiente", sLast: "Último" }
        }
    });
});

// ================================================
// ABRIR MODAL
// ================================================
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("btnNuevaTransferencia").addEventListener("click", abrirModalTransferencia);
});

function abrirModalTransferencia() {
    document.getElementById("contenidoTransferencia").innerHTML = buildModalHTML();
    new bootstrap.Modal(document.getElementById("modalTransferencia")).show();
    inicializarModal();
}

// ================================================
// CONSTRUIR HTML DEL MODAL
// ================================================
function buildModalHTML() {
    return `
    <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-exchange-alt me-2"></i>Nueva Transferencia</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <div class="card-sub text-center mb-3">
            Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.
        </div>

        <div class="row g-2">

            <!-- ORIGEN -->
            <div class="col-5">
                <div class="card-ubicacion origen">
                    <h6><i class="fas fa-map-marker-alt me-1"></i> Origen</h6>

                    <div class="mb-2">
                        <label class="form-label form-label-sm">Locación <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="selectLocacionOrigen">
                            <option value="">Cargando...</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label form-label-sm">Estructura</label>
                        <select class="form-select form-select-sm" id="selectEstructuraOrigen">
                            <option value="">-- Sin estructura --</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label form-label-sm">Artículo <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="selectArticulo">
                            <option value="">Seleccione locación primero</option>
                        </select>
                        <div id="stockDisponible" class="badge bg-secondary mt-1"></div>
                        <div class="invalid-feedback" id="error-articulo"></div>
                    </div>
                </div>
            </div>

            <!-- FLECHA -->
            <div class="col-2 flecha-transferencia">
                <i class="fas fa-arrow-right"></i>
            </div>

            <!-- DESTINO -->
            <div class="col-5">
                <div class="card-ubicacion destino">
                    <h6><i class="fas fa-map-pin me-1"></i> Destino</h6>

                    <div class="mb-2">
                        <label class="form-label form-label-sm">Locación <span class="text-danger">*</span></label>
                        <select class="form-select form-select-sm" id="selectLocacionDestino">
                            <option value="">Seleccione</option>
                        </select>
                        <div class="invalid-feedback" id="error-locacion-destino"></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label form-label-sm">Estructura</label>
                        <select class="form-select form-select-sm" id="selectEstructuraDestino">
                            <option value="">-- Sin estructura --</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <!-- Cantidad y motivo -->
        <div class="row g-3 mt-1">
            <div class="col-4">
                <label class="form-label">Cantidad <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="inputCantidad"
                       placeholder="0" min="1" step="1">
                <div class="invalid-feedback" id="error-cantidad"></div>
            </div>
            <div class="col-8">
                <label class="form-label">Motivo <small class="text-muted fw-normal">(opcional)</small></label>
                <input type="text" class="form-control" id="inputMotivo"
                       placeholder="Ej: Reabastecimiento punto de venta">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-danger rounded-5" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary rounded-5" id="btnConfirmarTransferencia">
            <i class="fas fa-exchange-alt me-1"></i> Transferir
        </button>
    </div>`;
}

// ================================================
// INICIALIZAR MODAL — lógica encadenada
// ================================================
function inicializarModal() {

    // Cargar locaciones en ambos selects
    fnAjax("LISTAR_LOCACIONES", {}).then(function (res) {
        const opcionesBase = '<option value="">Seleccione locación</option>';
        let opciones = opcionesBase;
        if (res.success) {
            res.data.forEach(function (loc) {
                opciones += `<option value="${loc.id}">[${loc.tipo}] ${loc.nombre}</option>`;
            });
        }
        document.getElementById("selectLocacionOrigen").innerHTML  = opciones;
        document.getElementById("selectLocacionDestino").innerHTML = opciones;
    });

    // Origen: cambio de locación → estructuras + artículos
    document.getElementById("selectLocacionOrigen").addEventListener("change", function () {
        const locacion_id = this.value;
        resetEstructura("selectEstructuraOrigen");
        resetArticulos();
        if (!locacion_id) return;

        cargarEstructuras(locacion_id, "selectEstructuraOrigen", function () {
            cargarArticulos();
        });
    });

    document.getElementById("selectEstructuraOrigen").addEventListener("change", function () {
        cargarArticulos();
    });

    // Destino: cambio de locación → estructuras
    document.getElementById("selectLocacionDestino").addEventListener("change", function () {
        const locacion_id = this.value;
        resetEstructura("selectEstructuraDestino");
        if (!locacion_id) return;
        cargarEstructuras(locacion_id, "selectEstructuraDestino");
    });

    // Artículo seleccionado → mostrar stock disponible
    document.getElementById("selectArticulo").addEventListener("change", function () {
        const opt    = this.options[this.selectedIndex];
        const badge  = document.getElementById("stockDisponible");
        const stock  = opt.dataset.stock;
        if (this.value && stock !== undefined) {
            badge.style.display = "inline-block";
            badge.textContent   = "Stock disponible: " + stock;
        } else {
            badge.style.display = "none";
        }
    });

    // Botón confirmar
    document.getElementById("btnConfirmarTransferencia").addEventListener("click", async function () {
        if (!validarCampos()) return;

        const datos = {
            articulo_id:           document.getElementById("selectArticulo").value,
            locacion_origen_id:    document.getElementById("selectLocacionOrigen").value,
            estructura_origen_id:  document.getElementById("selectEstructuraOrigen").value  || null,
            locacion_destino_id:   document.getElementById("selectLocacionDestino").value,
            estructura_destino_id: document.getElementById("selectEstructuraDestino").value || null,
            cantidad:              document.getElementById("inputCantidad").value,
            motivo:                document.getElementById("inputMotivo").value
        };

        // Confirmar antes de ejecutar
        const confirm = await Swal.fire({
            title:              '¿Confirmar transferencia?',
            html:               `Mover <strong>${datos.cantidad}</strong> unidad(es) del artículo seleccionado.`,
            icon:               'question',
            showCancelButton:   true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor:  '#6c757d',
            confirmButtonText:  'Sí, transferir',
            cancelButtonText:   'Cancelar'
        });

        if (!confirm.isConfirmed) return;

        try {
            const res = await fnAjax("TRANSFERIR", { data: JSON.stringify(datos) });
            if (res.success) {
                await Swal.fire({ title: "¡Listo!", text: res.message, icon: "success", timer: 1600, showConfirmButton: false });
                bootstrap.Modal.getInstance(document.getElementById("modalTransferencia")).hide();
                $("#tbl-historial").DataTable().ajax.reload();
            } else {
                Swal.fire("Aviso", res.message || "No se pudo realizar la transferencia.", "warning");
            }
        } catch (e) {
            Swal.fire("Error", e.message, "error");
        }
    });
}

// ================================================
// HELPERS
// ================================================
function cargarEstructuras(locacion_id, selectId, callback) {
    fnAjax("LISTAR_ESTRUCTURAS", { locacion_id: locacion_id }).then(function (res) {
        const sel = document.getElementById(selectId);
        sel.innerHTML = '<option value="">-- Sin estructura --</option>';
        if (res.success && res.data.length > 0) {
            res.data.forEach(function (est) {
                sel.innerHTML += `<option value="${est.id}">[${est.tipo}] ${est.nombre}</option>`;
            });
        }
        if (callback) callback();
    });
}

function cargarArticulos() {
    const locacion_id   = document.getElementById("selectLocacionOrigen").value;
    const estructura_id = document.getElementById("selectEstructuraOrigen").value || null;
    const sel           = document.getElementById("selectArticulo");

    if (!locacion_id) { resetArticulos(); return; }

    sel.innerHTML = '<option value="">Cargando...</option>';

    fnAjax("LISTAR_ARTICULOS_LOCACION", {
        locacion_id:   locacion_id,
        estructura_id: estructura_id
    }).then(function (res) {
        sel.innerHTML = '<option value="">Seleccione artículo</option>';
        if (res.success && res.data.length > 0) {
            res.data.forEach(function (art) {
                sel.innerHTML += `<option value="${art.articulo_id}" data-stock="${art.stock}">
                    ${art.nombre} (Stock: ${art.stock})
                </option>`;
            });
        } else {
            sel.innerHTML = '<option value="">Sin artículos con stock en esta ubicación</option>';
        }
        document.getElementById("stockDisponible").style.display = "none";
    });
}

function resetEstructura(selectId) {
    document.getElementById(selectId).innerHTML = '<option value="">-- Sin estructura --</option>';
}

function resetArticulos() {
    document.getElementById("selectArticulo").innerHTML = '<option value="">Seleccione locación primero</option>';
    document.getElementById("stockDisponible").style.display = "none";
}

function validarCampos() {
    let valid = true;

    const articulo = document.getElementById("selectArticulo");
    const errArt   = document.getElementById("error-articulo");
    if (!articulo.value) {
        valid = false;
        articulo.classList.add("is-invalid");
        errArt.textContent = "Seleccione un artículo.";
    } else {
        articulo.classList.remove("is-invalid");
        errArt.textContent = "";
    }

    const locDest  = document.getElementById("selectLocacionDestino");
    const errDest  = document.getElementById("error-locacion-destino");
    if (!locDest.value) {
        valid = false;
        locDest.classList.add("is-invalid");
        errDest.textContent = "Seleccione una locación destino.";
    } else {
        locDest.classList.remove("is-invalid");
        errDest.textContent = "";
    }

    const cantidad  = document.getElementById("inputCantidad");
    const errCant   = document.getElementById("error-cantidad");
    const stockMax  = parseFloat(document.getElementById("selectArticulo").options[document.getElementById("selectArticulo").selectedIndex]?.dataset?.stock ?? 0);
    if (!cantidad.value || isNaN(cantidad.value) || parseFloat(cantidad.value) <= 0) {
        valid = false;
        cantidad.classList.add("is-invalid");
        errCant.textContent = "Ingrese una cantidad válida mayor a 0.";
    } else if (parseFloat(cantidad.value) > stockMax) {
        valid = false;
        cantidad.classList.add("is-invalid");
        errCant.textContent = `Cantidad supera el stock disponible (${stockMax}).`;
    } else {
        cantidad.classList.remove("is-invalid");
        errCant.textContent = "";
    }

    return valid;
}

function fnAjax(accion, extraData) {
    return new Promise(function (resolve, reject) {
        $.ajax({
            method: "POST",
            url: URL_LOGICA,
            data: Object.assign({ accion: accion, sucursal_id: SUCURSAL_ID }, extraData)
        }).done(function (response) {
            try {
                resolve(typeof response === "string" ? JSON.parse(response) : response);
            } catch (e) {
                reject(new Error("Respuesta invalida: " + response));
            }
        }).fail(function (xhr) {
            reject(new Error("Error de conexion: " + xhr.status));
        });
    });
}
</script>

<?php include("pie.php"); ?>