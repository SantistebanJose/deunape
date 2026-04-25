<?php
#emisor.php
include("cabecera.php");


//askjdnakjsnd
$sucursal_id = isset($_SESSION['sucursal_id']) ? $_SESSION['sucursal_id'] : null;

if (!$sucursal_id) {
    echo '<div class="alert alert-danger">Error: No se ha establecido una sucursal activa.</div>';
    exit;
}

$datosEmisor  = fnListadoDeEmisor($sucursal_id);
$emisorExiste = !empty($datosEmisor);
$e            = $emisorExiste ? $datosEmisor[0] : [];
?>

<div class="container">
    <div class="page-inner">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="card-title mb-0">
                        <i class="fab fa-staylinked"></i> Datos del Emisor del Facturador SUNAT
                    </h5>
                    <div class="badge bg-info text-white">
                        <i class="fas fa-store"></i> Sucursal ID: <?php echo $sucursal_id; ?>
                    </div>
                </div>

                <div class="card-sub">
                    Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.
                </div>

                <?php if (!$emisorExiste): ?>
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>¡Atención!</strong> No hay datos de emisor registrados para esta sucursal.
                        Por favor, complete la información.
                    </div>
                <?php endif; ?>

                <div class="tab-content mt-3" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-persona" role="tabpanel">
                        <div class="row justify-content-center align-items-center g-2">

                            <!-- TIPO DOCUMENTO -->
                            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="mb-3">
                                    <label class="form-label"><b>Tipo de Documento <span class="fw-bold text-danger">*</span></b></label>
                                    <select class="form-select" id="idTipoDocumento" disabled>
                                        <option value="">Seleccione...</option>
                                        <option value="6" <?php echo ($emisorExiste && $e["tipo_documento"] == "6") ? 'selected' : ''; ?>>6 - RUC</option>
                                        <option value="1" <?php echo ($emisorExiste && $e["tipo_documento"] == "1") ? 'selected' : ''; ?>>1 - DNI</option>
                                    </select>
                                </div>
                            </div>

                            <!-- RUC -->
                            <div class="col-12 col-sm-6 col-md-8 col-lg-8">
                                <div class="mb-3">
                                    <label class="form-label"><b>RUC <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" class="form-control" id="idRuc"
                                           value="<?php echo $emisorExiste ? htmlspecialchars($e["ruc"]) : ''; ?>"
                                           disabled maxlength="11" placeholder="20123456789">
                                </div>
                            </div>

                            <!-- RAZÓN SOCIAL -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label"><b>Razón Social <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" class="form-control" id="idRazonSocial"
                                           value="<?php echo $emisorExiste ? htmlspecialchars($e["razon_social"]) : ''; ?>"
                                           disabled placeholder="MI EMPRESA S.A.C.">
                                </div>
                            </div>

                            <!-- NOMBRE COMERCIAL -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label"><b>Nombre Comercial <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" class="form-control" id="idNombreComercial"
                                           value="<?php echo $emisorExiste ? htmlspecialchars($e["nombre_comercial"]) : ''; ?>"
                                           disabled placeholder="MI NEGOCIO">
                                </div>
                            </div>

                            <hr>

                            <!-- USUARIO SOL -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label"><b>Usuario SOL <span class="fw-bold text-danger">*</span></b></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="idUsuarioSol"
                                               value="<?php echo $emisorExiste ? htmlspecialchars($e["usuario_sol"]) : ''; ?>"
                                               disabled placeholder="MODDATOS">
                                    </div>
                                </div>
                            </div>

                            <!-- CLAVE SOL -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label"><b>Clave Sol <span class="fw-bold text-danger">*</span></b></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="idClaveSol"
                                               value="<?php echo $emisorExiste ? htmlspecialchars($e["clave_sol"]) : ''; ?>"
                                               disabled placeholder="••••••••">
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <div class="col-12">
                                <h6 class="text-primary"><i class="fas fa-file-invoice"></i> Configuración de Comprobantes</h6>
                            </div>

                            <!-- LOGO — Drag & Drop -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <b><i class="fas fa-image"></i> Logo de la Sucursal</b>
                                        <small class="text-muted d-block">Para boletas y facturas</small>
                                    </label>

                                    <div id="dropLogo" class="drop-zone drop-zone--disabled">
                                        <input type="file" id="idLogoSucursal" class="drop-zone__input"
                                               accept="image/jpeg,image/jpg,image/png" disabled>
                                        <div class="drop-zone__content">
                                            <i class="fas fa-cloud-upload-alt drop-zone__icon"></i>
                                            <p class="drop-zone__text">Arrastra tu logo aquí<br>
                                                <small>o <span class="drop-zone__link">haz clic para seleccionar</span></small>
                                            </p>
                                            <p class="drop-zone__hint">JPG, PNG — máx. 2MB</p>
                                        </div>
                                        <div class="drop-zone__preview" id="previewLogo" style="display:none;">
                                            <img id="previewLogoImg" src="" alt="Vista previa">
                                            <button type="button" class="drop-zone__remove" onclick="clearDropZone('logo')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <?php if ($emisorExiste && !empty($e["ruta_logo"])): ?>
                                        <div class="mt-2 drop-zone__current">
                                            <img src="<?php echo htmlspecialchars($e["ruta_logo"]); ?>"
                                                 alt="Logo actual" class="img-thumbnail" style="max-height:70px;">
                                            <small class="text-muted ms-2">Logo actual</small>
                                        </div>
                                        <input type="hidden" id="idRutaLogoActual" value="<?php echo htmlspecialchars($e["ruta_logo"]); ?>">
                                    <?php else: ?>
                                        <input type="hidden" id="idRutaLogoActual" value="">
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- FIRMA DIGITAL — Drag & Drop -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <b><i class="fas fa-file-signature"></i> Firma Digital SUNAT</b>
                                        <small class="text-muted d-block">Certificado digital (.pfx / .p12)</small>
                                    </label>

                                    <div id="dropFirma" class="drop-zone drop-zone--disabled drop-zone--pfx">
                                        <input type="file" id="idFirmaDigital" class="drop-zone__input"
                                               accept=".pfx,.p12" disabled>
                                        <div class="drop-zone__content">
                                            <i class="fas fa-file-certificate drop-zone__icon"></i>
                                            <p class="drop-zone__text">Arrastra tu certificado aquí<br>
                                                <small>o <span class="drop-zone__link">haz clic para seleccionar</span></small>
                                            </p>
                                            <p class="drop-zone__hint">PFX, P12 — máx. 5MB</p>
                                        </div>
                                        <div class="drop-zone__preview drop-zone__preview--file" id="previewFirma" style="display:none;">
                                            <i class="fas fa-file-certificate fa-2x text-success"></i>
                                            <span id="previewFirmaNombre" class="ms-2 fw-bold"></span>
                                            <button type="button" class="drop-zone__remove" onclick="clearDropZone('firma')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <?php if ($emisorExiste && !empty($e["direccion_firma_digital"])): ?>
                                        <div class="mt-2 alert alert-success py-1 mb-0">
                                            <i class="fas fa-check-circle"></i>
                                            <small>Certificado activo:
                                                <strong><?php echo basename($e["direccion_firma_digital"]); ?></strong>
                                            </small>
                                            <input type="hidden" id="idRutaFirmaActual" value="<?php echo htmlspecialchars($e["direccion_firma_digital"]); ?>">
                                        </div>
                                    <?php else: ?>
                                        <div class="mt-2 alert alert-warning py-1 mb-0">
                                            <i class="fas fa-exclamation-circle"></i>
                                            <small>Sin certificado cargado</small>
                                        </div>
                                        <input type="hidden" id="idRutaFirmaActual" value="">
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- CONTRASEÑA CERTIFICADO -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label for="idPasswordFirma" class="form-label">
                                        <b><i class="fas fa-key"></i> Contraseña del Certificado</b>
                                    </label>
                                    <input type="password" class="form-control" id="idPasswordFirma"
                                           value="<?php echo $emisorExiste ? htmlspecialchars($e["contraseña_firma_digital"]) : ''; ?>"
                                           disabled placeholder="••••••••">
                                    <small class="text-muted">Contraseña del archivo .pfx/.p12</small>
                                </div>
                            </div>

                            <!-- SERIES DE COMPROBANTES -->
                            <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <b><i class="fas fa-hashtag"></i> Serie Boleta</b>
                                        <small class="text-muted d-block">Ej: B001, B002, B003</small>
                                    </label>
                                    <input type="text" class="form-control text-uppercase" id="idSerieBoleta"
                                           maxlength="4" placeholder="B001"
                                           value="<?php echo $emisorExiste ? htmlspecialchars($e['serie_boleta'] ?? 'B001') : 'B001'; ?>"
                                           disabled>
                                    <small class="text-muted">1 letra + 3 números</small>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <b><i class="fas fa-hashtag"></i> Serie Factura</b>
                                        <small class="text-muted d-block">Ej: F001, F002, F003</small>
                                    </label>
                                    <input type="text" class="form-control text-uppercase" id="idSerieFactura"
                                           maxlength="4" placeholder="F001"
                                           value="<?php echo $emisorExiste ? htmlspecialchars($e['serie_factura'] ?? 'F001') : 'F001'; ?>"
                                           disabled>
                                    <small class="text-muted">1 letra + 3 números</small>
                                </div>
                            </div>

                            <!-- ✅ AMBIENTE SUNAT -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label for="idAmbiente" class="form-label">
                                        <b><i class="fas fa-server"></i> Ambiente SUNAT <span class="fw-bold text-danger">*</span></b>
                                    </label>
                                    <select class="form-select" id="idAmbiente" disabled>
                                        <option value="beta"       <?php echo ($emisorExiste && ($e["ambiente"] ?? 'beta') == 'beta')       ? 'selected' : ''; ?>>🧪 Beta (Pruebas)</option>
                                        <option value="produccion" <?php echo ($emisorExiste && ($e["ambiente"] ?? '') == 'produccion') ? 'selected' : ''; ?>>🚀 Producción</option>
                                    </select>
                                    <?php if ($emisorExiste && ($e["ambiente"] ?? 'beta') === 'produccion'): ?>
                                        <small class="text-danger fw-bold">
                                            <i class="fas fa-exclamation-triangle"></i> Modo producción activo — los comprobantes son reales
                                        </small>
                                    <?php else: ?>
                                        <small class="text-muted">Modo pruebas — los comprobantes no tienen validez tributaria</small>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- TOGGLE CONTRASEÑAS -->
                            <div class="col-12">
                                <button type="button" id="togglePassword" class="btn btn-link">
                                    <i class="fas fa-eye"></i> Mostrar Contraseñas
                                </button>
                            </div>

                            <hr>

                            <!-- DEPARTAMENTO -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label"><b>Departamento <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" class="form-control" id="idDepartamento"
                                           value="<?php echo $emisorExiste ? htmlspecialchars($e["departamento"]) : ''; ?>"
                                           disabled placeholder="LAMBAYEQUE">
                                </div>
                            </div>

                            <!-- PROVINCIA -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label"><b>Provincia <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" class="form-control" id="idProvincia"
                                           value="<?php echo $emisorExiste ? htmlspecialchars($e["provincia"]) : ''; ?>"
                                           disabled placeholder="CHICLAYO">
                                </div>
                            </div>

                            <!-- DISTRITO -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label"><b>Distrito</b></label>
                                    <input type="text" class="form-control" id="idDistrito"
                                           value="<?php echo $emisorExiste ? htmlspecialchars($e["distrito"]) : ''; ?>"
                                           disabled placeholder="CHICLAYO">
                                </div>
                            </div>

                            <!-- UBIGEO -->
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label"><b>Ubigeo</b></label>
                                    <input type="text" class="form-control" id="idUbigeo"
                                           value="<?php echo $emisorExiste ? htmlspecialchars($e["ubigeo"]) : ''; ?>"
                                           disabled placeholder="140101">
                                </div>
                            </div>

                            <!-- DIRECCIÓN -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label"><b>Dirección Fiscal <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" class="form-control" id="idDireccion"
                                           value="<?php echo $emisorExiste ? htmlspecialchars($e["direccion"]) : ''; ?>"
                                           disabled placeholder="AV. PRINCIPAL NRO. 123">
                                </div>
                            </div>

                        </div>

                        <br>
                        <div class="col-12 text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button id="idBtnHabilitar"
                                        class="btn btn-warning btn-round text"
                                        onclick="habilitarCampos()"
                                        role="button">
                                    <i class="fas fa-edit"></i> Habilitar Cambios
                                </button>

                                <a style="display: none;"
                                   id="idBtneGuardar"
                                   class="btn btn-success btn-round text"
                                   onclick="fn_guardar_cambios()"
                                   role="button">
                                    <i class="fas fa-save"></i> Guardar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("pie.php"); ?>

<script>
    const SUCURSAL_ID = <?php echo json_encode($sucursal_id); ?>;

    function habilitarCampos() {
        const inputs = document.querySelectorAll("#pills-persona input, #pills-persona select");
        document.getElementById("idBtneGuardar").style.display = "block";
        document.getElementById("idBtnHabilitar").style.display = "none";

        inputs.forEach(input => {
            input.disabled = false;
        });

        swal({
            title: "Campos Habilitados",
            text: "Ahora puedes editar los datos del emisor",
            icon: "info",
            buttons: false,
            timer: 1500
        });
    }

    // Toggle contraseñas
    const passwordFields = document.querySelectorAll("#idUsuarioSol, #idClaveSol, #idPasswordFirma");
    const togglePasswordButton = document.getElementById("togglePassword");

    togglePasswordButton.addEventListener("click", function() {
        passwordFields.forEach(function(field) {
            field.type = field.type === "password" ? "text" : "password";
        });

        const icon = togglePasswordButton.querySelector('i');
        if (icon.classList.contains('fa-eye')) {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
            togglePasswordButton.innerHTML = '<i class="fas fa-eye-slash"></i> Ocultar Contraseñas';
        } else {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
            togglePasswordButton.innerHTML = '<i class="fas fa-eye"></i> Mostrar Contraseñas';
        }
    });

    function fn_guardar_cambios() {

        const tipoDocumento   = document.getElementById("idTipoDocumento").value.trim();
        const ruc             = document.getElementById("idRuc").value.trim();
        const razonSocial     = document.getElementById("idRazonSocial").value.trim();
        const nombreComercial = document.getElementById("idNombreComercial").value.trim();
        const usuarioSol      = document.getElementById("idUsuarioSol").value.trim();
        const claveSol        = document.getElementById("idClaveSol").value.trim();
        const departamento    = document.getElementById("idDepartamento").value.trim();
        const provincia       = document.getElementById("idProvincia").value.trim();
        const direccion       = document.getElementById("idDireccion").value.trim();
        const ambiente        = document.getElementById("idAmbiente").value; // ✅ NUEVO

        // Validar campos obligatorios
        if (!tipoDocumento || !ruc || !razonSocial || !nombreComercial || !usuarioSol || !claveSol ||
            !departamento || !provincia || !direccion) {
            swal("Error", "Por favor, complete todos los campos obligatorios (*)", {
                icon: "error",
                buttons: { confirm: { className: "btn btn-danger" } }
            });
            return;
        }

        if (tipoDocumento === "6" && (ruc.length !== 11 || !/^\d+$/.test(ruc))) {
            swal("Error", "El RUC debe tener exactamente 11 dígitos numéricos", {
                icon: "error",
                buttons: { confirm: { className: "btn btn-danger" } }
            });
            return;
        }

        if (tipoDocumento === "1" && (ruc.length !== 8 || !/^\d+$/.test(ruc))) {
            swal("Error", "El DNI debe tener exactamente 8 dígitos numéricos", {
                icon: "error",
                buttons: { confirm: { className: "btn btn-danger" } }
            });
            return;
        }

        // Confirmar si cambia a producción
        if (ambiente === 'produccion') {
            if (!confirm('⚠️ Estás activando el modo PRODUCCIÓN.\nLos comprobantes emitidos tendrán validez tributaria real.\n¿Deseas continuar?')) {
                return;
            }
        }

        const formData = new FormData();
        formData.append('accion',          'EDITAR_EMISOR');
        formData.append('sucursal_id',     SUCURSAL_ID);
        formData.append('tipo_documento',  tipoDocumento);
        formData.append('ruc',             ruc);
        formData.append('razon_social',    razonSocial);
        formData.append('nombre_comercial',nombreComercial);
        formData.append('usuario_sol',     usuarioSol);
        formData.append('clave_sol',       claveSol);
        formData.append('password_firma',  document.getElementById("idPasswordFirma").value.trim());
        formData.append('ambiente',        ambiente); // ✅ NUEVO
        formData.append('serie_boleta',    document.getElementById('idSerieBoleta').value.trim().toUpperCase());
        formData.append('serie_factura',   document.getElementById('idSerieFactura').value.trim().toUpperCase());
        formData.append('departamento',    departamento);
        formData.append('provincia',       provincia);
        formData.append('distrito',        document.getElementById("idDistrito").value.trim());
        formData.append('ubigeo',          document.getElementById("idUbigeo").value.trim());
        formData.append('direccion',       direccion);

        // Archivos
        const logoFile  = document.getElementById("idLogoSucursal").files[0];
        const firmaFile = document.getElementById("idFirmaDigital").files[0];

        if (logoFile) {
            if (logoFile.size > 2 * 1024 * 1024) {
                swal("Error", "El logo no debe superar los 2MB", {
                    icon: "error",
                    buttons: { confirm: { className: "btn btn-danger" } }
                });
                return;
            }
            formData.append('logo_sucursal', logoFile);
        } else {
            formData.append('ruta_logo_actual', document.getElementById("idRutaLogoActual").value);
        }

        if (firmaFile) {
            if (firmaFile.size > 5 * 1024 * 1024) {
                swal("Error", "El certificado digital no debe superar los 5MB", {
                    icon: "error",
                    buttons: { confirm: { className: "btn btn-danger" } }
                });
                return;
            }
            formData.append('firma_digital', firmaFile);
        } else {
            formData.append('ruta_firma_actual', document.getElementById("idRutaFirmaActual").value);
        }

        $.ajax({
            url        : 'logica/clssInsertPA.php',
            type       : 'POST',
            data       : formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                swal({
                    title             : "Guardando...",
                    text              : "Subiendo archivos y guardando datos, por favor espere",
                    icon              : "info",
                    buttons           : false,
                    closeOnClickOutside: false,
                    closeOnEsc        : false
                });
            },
            success: function(response) {
                try {
                    var result = JSON.parse(response);
                    if (result.estado === true) {
                        swal({
                            title  : "¡Emisor Actualizado!",
                            text   : result.mensaje || "Los datos del emisor se guardaron correctamente",
                            icon   : "success",
                            buttons: false,
                            timer  : 1500
                        }).then(() => { location.reload(); });
                    } else {
                        swal("Error", result.mensaje || "No se pudo guardar el emisor", {
                            icon   : "error",
                            buttons: { confirm: { className: "btn btn-danger" } }
                        });
                    }
                } catch (e) {
                    console.error("Respuesta recibida:", response);
                    swal("Error", "No se pudo procesar la respuesta del servidor.", {
                        icon   : "error",
                        buttons: { confirm: { className: "btn btn-danger" } }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("Response:", xhr.responseText);
                swal("Error", "Hubo un problema al guardar los datos: " + error, {
                    icon   : "error",
                    buttons: { confirm: { className: "btn btn-danger" } }
                });
            }
        });
    }
    // ── Drag & Drop ──────────────────────────────────────────────
    function initDropZone(zoneId, inputId, tipo) {
        const zone  = document.getElementById(zoneId);
        const input = document.getElementById(inputId);

        // Clic en la zona abre el selector
        zone.addEventListener('click', () => { if (!input.disabled) input.click(); });

        input.addEventListener('change', () => {
            if (input.files[0]) handleFile(input.files[0], tipo);
        });

        zone.addEventListener('dragover', e => {
            e.preventDefault();
            if (!input.disabled) zone.classList.add('drop-zone--over');
        });
        zone.addEventListener('dragleave', () => zone.classList.remove('drop-zone--over'));
        zone.addEventListener('drop', e => {
            e.preventDefault();
            zone.classList.remove('drop-zone--over');
            if (input.disabled) return;
            const file = e.dataTransfer.files[0];
            if (file) {
                // Asignar al input
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                handleFile(file, tipo);
            }
        });
    }

    function handleFile(file, tipo) {
        if (tipo === 'logo') {
            const max = 2 * 1024 * 1024;
            const allowed = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowed.includes(file.type)) {
                swal("Formato inválido", "Solo se permiten JPG o PNG para el logo", "error"); return;
            }
            if (file.size > max) {
                swal("Archivo muy grande", "El logo no debe superar los 2MB", "error"); return;
            }
            // Mostrar preview
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('previewLogoImg').src = e.target.result;
                document.getElementById('previewLogo').style.display = 'flex';
                document.querySelector('#dropLogo .drop-zone__content').style.display = 'none';
            };
            reader.readAsDataURL(file);

        } else {
            const max = 5 * 1024 * 1024;
            const allowed = ['.pfx', '.p12'];
            const ext = '.' + file.name.split('.').pop().toLowerCase();
            if (!allowed.includes(ext)) {
                swal("Formato inválido", "Solo se permiten PFX o P12 para el certificado", "error"); return;
            }
            if (file.size > max) {
                swal("Archivo muy grande", "El certificado no debe superar los 5MB", "error"); return;
            }
            document.getElementById('previewFirmaNombre').textContent = file.name;
            document.getElementById('previewFirma').style.display = 'flex';
            document.querySelector('#dropFirma .drop-zone__content').style.display = 'none';
        }
    }

    function clearDropZone(tipo) {
        if (tipo === 'logo') {
            document.getElementById('idLogoSucursal').value = '';
            document.getElementById('previewLogo').style.display = 'none';
            document.querySelector('#dropLogo .drop-zone__content').style.display = 'flex';
        } else {
            document.getElementById('idFirmaDigital').value = '';
            document.getElementById('previewFirma').style.display = 'none';
            document.querySelector('#dropFirma .drop-zone__content').style.display = 'flex';
        }
    }

    // Inicializar zonas
    initDropZone('dropLogo',  'idLogoSucursal', 'logo');
    initDropZone('dropFirma', 'idFirmaDigital', 'firma');

    // Habilitar zonas al habilitar campos
    const _habilitarOriginal = habilitarCampos;
    habilitarCampos = function() {
        _habilitarOriginal();
        document.getElementById('dropLogo').classList.remove('drop-zone--disabled');
        document.getElementById('dropFirma').classList.remove('drop-zone--disabled');
        document.getElementById('idSerieBoleta').disabled  = false;
        document.getElementById('idSerieFactura').disabled = false;
    };
</script>

<style>
/* ── Drag & Drop Zone ── */
.drop-zone {
    position: relative;
    border: 2px dashed #ced4da;
    border-radius: 12px;
    padding: 24px 16px;
    text-align: center;
    cursor: pointer;
    transition: border-color .2s, background .2s;
    background: #fafbff;
    min-height: 130px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.drop-zone:not(.drop-zone--disabled):hover,
.drop-zone--over {
    border-color: #667eea;
    background: #f0f3ff;
}
.drop-zone--disabled {
    cursor: not-allowed;
    opacity: .6;
    background: #f5f5f5;
}
.drop-zone--pfx { border-color: #a8d5b5; background: #f0fff4; }
.drop-zone--pfx:not(.drop-zone--disabled):hover { border-color: #11998e; background: #e6faf5; }

.drop-zone__input {
    display: none;
}
.drop-zone__content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    pointer-events: none;
}
.drop-zone__icon {
    font-size: 2rem;
    color: #667eea;
}
.drop-zone--pfx .drop-zone__icon { color: #11998e; }
.drop-zone__text {
    margin: 0;
    font-size: .88rem;
    color: #555;
    line-height: 1.4;
}
.drop-zone__link {
    color: #667eea;
    font-weight: 600;
    text-decoration: underline;
}
.drop-zone__hint {
    margin: 0;
    font-size: .75rem;
    color: #9ca3af;
}

/* Preview imagen (logo) */
.drop-zone__preview {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
}
.drop-zone__preview img {
    max-height: 80px;
    max-width: 160px;
    border-radius: 8px;
    object-fit: contain;
    box-shadow: 0 2px 8px rgba(0,0,0,.12);
}
/* Preview archivo (pfx) */
.drop-zone__preview--file {
    font-size: .88rem;
    color: #065f46;
}

.drop-zone__remove {
    background: #fee2e2;
    border: none;
    color: #dc2626;
    border-radius: 50%;
    width: 26px;
    height: 26px;
    font-size: .75rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background .15s;
    flex-shrink: 0;
}
.drop-zone__remove:hover { background: #fca5a5; }

.drop-zone__current {
    display: flex;
    align-items: center;
}
</style>