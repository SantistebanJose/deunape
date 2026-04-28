<?php
include("cabecera.php");
?>

<style>
    label {
        font-weight: bold;
    }

    .error-message {
        color: red;
        font-size: 0.9em;
        margin-top: 5px;
    }

    #modalInventario {
        z-index: 1060 !important;
    }

    #modalInventario .modal-content {
        background-color: rgb(255, 255, 255);
        border-radius: 10px;
        border: 2px solid #2a2f5b;
    }

    #modalInventario .modal-dialog {
        box-shadow: 0 4px 10px #2a2f5b;
    }

    #modalInventario .modal-header {
        background-color: rgb(255, 255, 255);
        color: #2a2f5b;
    }

    #modalInventario .btn-close {
        background-color: #f0f8ff;
    }

    #sugerenciasArticulo {
        position: absolute;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1070;
        border: 1px solid #ddd;
        border-top: none;
        border-radius: 0 0 6px 6px;
        background: #fff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    #sugerenciasArticulo .list-group-item {
        cursor: pointer;
        padding: 8px 12px;
        font-size: 13px;
    }

    #sugerenciasArticulo .list-group-item:hover {
        background-color: #e9ecef;
    }

    .articulo-container {
        position: relative;
    }
</style>

<div class="container">
    <div class="page-inner">
        <div class="card text-start">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title">Distribución de Inventario</h4>
                    <button class="btn btn-success rounded-5" id="btnAbrirModalInventario">
                        Agregar Distribución <i class="fas fa-plus"></i>
                    </button>
                </div>
                <hr>
                <div class="row justify-content-center align-items-center">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table id="tbl-inventario" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Artículo</th>
                                        <th>Locación</th>
                                        <th>Estructura</th>
                                        <th>Tipo</th>
                                        <th>Stock</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Inventario -->
<div class="modal fade" id="modalInventario" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="contenidoInventario"></div>
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
    const URL_LOGICA  = "logica/clssInventario.php";

    // ================================================
    // DATATABLE
    // ================================================
    $(document).ready(function () {
        $("#tbl-inventario").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: URL_LOGICA,
                type: "POST",
                data: function (d) {
                    d.accion      = "LISTAR";
                    d.sucursal_id = SUCURSAL_ID;
                },
                error: function (xhr) {
                    console.error("Error DataTables:", xhr.responseText);
                }
            },
            columns: [
                { data: "id" },
                { data: "nombre_articulo" },
                { data: "nombre_locacion" },
                { data: "nombre_estructura" },
                { data: "tipo_estructura" },
                { data: "stock" },
                { data: "acciones", orderable: false, searchable: false }
            ],
            pageLength: 10,
            language: {
                sProcessing:     "Procesando...",
                sLengthMenu:     "Mostrar _MENU_ registros",
                sZeroRecords:    "No se encontraron resultados",
                sEmptyTable:     "Ningún dato disponible en esta tabla",
                sInfo:           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                sInfoEmpty:      "Mostrando registros del 0 al 0 de un total de 0 registros",
                sInfoFiltered:   "(filtrado de un total de _MAX_ registros)",
                sSearch:         "Buscar:",
                sLoadingRecords: "Cargando...",
                oPaginate: {
                    sFirst:    "Primero",
                    sPrevious: "Anterior",
                    sNext:     "Siguiente",
                    sLast:     "Último"
                }
            }
        });
    });

    // ================================================
    // ABRIR MODAL — REGISTRAR
    // ================================================
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("btnAbrirModalInventario").addEventListener("click", function () {
            abrirModalRegistro();
        });
    });

    function abrirModalRegistro() {
        document.getElementById("contenidoInventario").innerHTML = buildFormHTML("Distribuir Artículo", "btnAccionInventario", "Registrar");
        new bootstrap.Modal(document.getElementById("modalInventario")).show();
        inicializarFormulario();

        document.getElementById("btnAccionInventario").addEventListener("click", async function () {
            if (!validarCampos()) return;
            const datos = recogerDatos();
            try {
                const res = await fnAjax("REGISTRAR", { data: JSON.stringify(datos) });
                if (res.success) {
                    await Swal.fire({ title: "¡Registrado!", text: res.message, icon: "success", timer: 1500, showConfirmButton: false });
                    location.reload();
                } else {
                    Swal.fire("Aviso", res.message || "Error al registrar", "warning");
                }
            } catch (e) {
                Swal.fire("Error", e.message, "error");
            }
        });
    }

    // ================================================
    // EDITAR
    // ================================================
    function fn_editar_inventario(rowJson) {
        const row = JSON.parse(rowJson);

        document.getElementById("contenidoInventario").innerHTML = buildFormHTML("Editar Distribución", "btnAccionInventario", "Actualizar");
        new bootstrap.Modal(document.getElementById("modalInventario")).show();

        inicializarFormulario(function () {
            document.getElementById("inventario_id").value  = row.id;
            document.getElementById("articulo_id").value    = row.articulo_id;
            document.getElementById("inputArticulo").value  = row.nombre_articulo;
            document.getElementById("inputStock").value     = row.stock;

            const selLoc = document.getElementById("selectLocacion");
            selLoc.value = row.locacion_id;
            selLoc.dispatchEvent(new Event("change"));

            if (row.estructura_id) {
                setTimeout(function () {
                    document.getElementById("selectEstructura").value = row.estructura_id;
                }, 600);
            }
        });

        document.getElementById("btnAccionInventario").addEventListener("click", async function () {
            if (!validarCampos()) return;
            const datos = recogerDatos();
            datos.id    = document.getElementById("inventario_id").value;
            try {
                const res = await fnAjax("ACTUALIZAR", { data: JSON.stringify(datos) });
                if (res.success) {
                    await Swal.fire({ title: "¡Actualizado!", text: res.message, icon: "success", timer: 1500, showConfirmButton: false });
                    location.reload();
                } else {
                    Swal.fire("Error", res.message || "Error al actualizar", "error");
                }
            } catch (e) {
                Swal.fire("Error", e.message, "error");
            }
        });
    }

    // ================================================
    // ELIMINAR
    // ================================================
    function fn_eliminar_inventario(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se eliminará este registro de inventario.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await fnAjax("ELIMINAR", { id: id });
                    if (res.success) {
                        location.reload();
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                } catch (e) {
                    Swal.fire("Error", e.message, "error");
                }
            }
        });
    }

    // ================================================
    // HELPERS
    // ================================================
    function buildFormHTML(titulo, btnId, btnLabel) {
        return `
        <div class="modal-body">
            <div class="card text-start">
                <div class="card-body">
                    <h4 class="card-title text-center"><i class="fas fa-boxes"></i> ${titulo}</h4>
                    <div class="card-sub text-center mb-3">
                        Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.
                    </div>
                    <hr>

                    <input type="hidden" id="inventario_id">
                    <input type="hidden" id="articulo_id">

                    <!-- Artículo autocomplete -->
                    <div class="mb-3 articulo-container">
                        <label class="form-label">Artículo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="inputArticulo"
                               placeholder="Escribe para buscar artículos..." autocomplete="off">
                        <div id="sugerenciasArticulo" class="list-group"></div>
                        <div class="invalid-feedback" id="error-articulo"></div>
                    </div>

                    <!-- Locación -->
                    <div class="mb-3">
                        <label class="form-label">Locación / Almacén <span class="text-danger">*</span></label>
                        <select class="form-select" id="selectLocacion">
                            <option value="">Cargando locaciones...</option>
                        </select>
                        <div class="invalid-feedback" id="error-locacion"></div>
                    </div>

                    <!-- Estructura -->
                    <div class="mb-3">
                        <label class="form-label">Estructura (Andamio / Estante)</label>
                        <select class="form-select" id="selectEstructura">
                            <option value="">-- Sin estructura --</option>
                        </select>
                    </div>

                    <!-- Stock -->
                    <div class="mb-3">
                        <label class="form-label">Stock a distribuir <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="inputStock"
                               placeholder="0" min="0" step="1">
                        <div class="invalid-feedback" id="error-stock"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger rounded-5" data-bs-dismiss="modal">Salir</button>
            <button type="button" class="btn btn-success rounded-5" id="${btnId}">${btnLabel}</button>
        </div>`;
    }

    function inicializarFormulario(callback) {
        // Cargar locaciones
        fnAjax("LISTAR_LOCACIONES", {}).then(function (res) {
            const sel = document.getElementById("selectLocacion");
            sel.innerHTML = '<option value="">Seleccione una locación</option>';
            if (res.success && res.data.length > 0) {
                res.data.forEach(function (loc) {
                    sel.innerHTML += `<option value="${loc.id}">[${loc.tipo}] ${loc.nombre}</option>`;
                });
            } else {
                sel.innerHTML = '<option value="">Sin locaciones disponibles</option>';
            }
            if (callback) callback();
        });

        // Cambio de locación → cargar estructuras
        document.getElementById("selectLocacion").addEventListener("change", function () {
            const locacion_id = this.value;
            const selEst      = document.getElementById("selectEstructura");
            selEst.innerHTML  = '<option value="">-- Sin estructura --</option>';
            if (!locacion_id) return;

            fnAjax("LISTAR_ESTRUCTURAS", { locacion_id: locacion_id }).then(function (res) {
                if (res.success && res.data.length > 0) {
                    res.data.forEach(function (est) {
                        selEst.innerHTML += `<option value="${est.id}">[${est.tipo}] ${est.nombre}</option>`;
                    });
                }
            });
        });

        // Autocomplete artículo — busca por nombre
        let timer;
        document.getElementById("inputArticulo").addEventListener("input", function () {
            const term  = this.value.trim();
            const lista = document.getElementById("sugerenciasArticulo");
            clearTimeout(timer);
            document.getElementById("articulo_id").value = '';

            if (term.length < 2) { lista.innerHTML = ''; return; }

            timer = setTimeout(function () {
                fnAjax("BUSCAR_ARTICULO", { term: term, sucursal_id: SUCURSAL_ID }).then(function (res) {
                    lista.innerHTML = '';
                    if (res.success && res.data.length > 0) {
                        res.data.forEach(function (art) {
                            const item = document.createElement("a");
                            item.className = "list-group-item list-group-item-action";
                            item.innerHTML = `
                                <span>${art.nombre}</span>
                                <span class="text-muted float-end">S/ ${parseFloat(art.precio_venta || 0).toFixed(2)}</span>
                            `;
                            item.addEventListener("click", function () {
                                document.getElementById("inputArticulo").value = art.nombre;
                                document.getElementById("articulo_id").value   = art.id;
                                lista.innerHTML = '';
                            });
                            lista.appendChild(item);
                        });
                    } else {
                        lista.innerHTML = '<a class="list-group-item text-muted">No se encontraron artículos</a>';
                    }
                });
            }, 300);
        });

        // Cerrar sugerencias al click fuera
        document.addEventListener("click", function (e) {
            if (!e.target.closest(".articulo-container")) {
                const lista = document.getElementById("sugerenciasArticulo");
                if (lista) lista.innerHTML = '';
            }
        });
    }

    function validarCampos() {
        let valid = true;

        const articulo_id   = document.getElementById("articulo_id").value;
        const inputArticulo = document.getElementById("inputArticulo");
        const errorArticulo = document.getElementById("error-articulo");
        if (!articulo_id) {
            valid = false;
            inputArticulo.classList.add("is-invalid");
            errorArticulo.textContent = "Debe seleccionar un artículo de la lista.";
        } else {
            inputArticulo.classList.remove("is-invalid");
            errorArticulo.textContent = "";
        }

        const selectLocacion = document.getElementById("selectLocacion");
        const errorLocacion  = document.getElementById("error-locacion");
        if (!selectLocacion.value) {
            valid = false;
            selectLocacion.classList.add("is-invalid");
            errorLocacion.textContent = "Debe seleccionar una locación.";
        } else {
            selectLocacion.classList.remove("is-invalid");
            errorLocacion.textContent = "";
        }

        const inputStock = document.getElementById("inputStock");
        const errorStock = document.getElementById("error-stock");
        if (inputStock.value === '' || isNaN(inputStock.value) || parseFloat(inputStock.value) < 0) {
            valid = false;
            inputStock.classList.add("is-invalid");
            errorStock.textContent = "El stock debe ser un número mayor o igual a 0.";
        } else {
            inputStock.classList.remove("is-invalid");
            errorStock.textContent = "";
        }

        return valid;
    }

    function recogerDatos() {
        return {
            articulo_id:   document.getElementById("articulo_id").value,
            locacion_id:   document.getElementById("selectLocacion").value,
            estructura_id: document.getElementById("selectEstructura").value || null,
            stock:         document.getElementById("inputStock").value
        };
    }

    function fnAjax(accion, extraData) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                method: "POST",
                url: URL_LOGICA,
                data: Object.assign({ accion: accion, sucursal_id: SUCURSAL_ID }, extraData)
            }).done(function (response) {
                try {
                    const json = (typeof response === "string") ? JSON.parse(response) : response;
                    resolve(json);
                } catch (e) {
                    reject(new Error("Respuesta invalida del servidor: " + response));
                }
            }).fail(function (xhr) {
                reject(new Error("Error de conexion: " + xhr.status));
            });
        });
    }
</script>

<?php
include("pie.php");
?>