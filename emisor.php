<?php
include("cabecera.php");

// ✅ OBTENER SUCURSAL_ID DE LA SESIÓN
$sucursal_id = isset($_SESSION['sucursal_id']) ? $_SESSION['sucursal_id'] : null;

// ✅ VERIFICAR QUE EXISTE SUCURSAL
if (!$sucursal_id) {
    echo '<div class="alert alert-danger">Error: No se ha establecido una sucursal activa.</div>';
    exit;
}

// ✅ OBTENER DATOS DEL EMISOR DE ESTA SUCURSAL
$datosEmisor = fnListadoDeEmisor($sucursal_id);

// ✅ VERIFICAR SI EXISTE EMISOR PARA ESTA SUCURSAL
$emisorExiste = !empty($datosEmisor);
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
                    <!-- Formulario Persona -->
                    <div class="tab-pane fade show active" id="pills-persona" role="tabpanel" aria-labelledby="pills-persona-tab">
                        <div class="row justify-content-center align-items-center g-2">

                            <div class="col-12 col-sm-6 col-md-4 col-lg-4">
                                <div class="mb-3">
                                    <label for="" class="form-label"><b>Tipo de Documento <span class="fw-bold text-danger">*</span></b></label>
                                    <select class="form-select" id="idTipoDocumento" disabled>
                                        <option value="">Seleccione...</option>
                                        <option value="6" <?php echo ($emisorExiste && $datosEmisor[0]["tipo_documento"] == "6") ? 'selected' : ''; ?>>6 - RUC</option>
                                        <option value="1" <?php echo ($emisorExiste && $datosEmisor[0]["tipo_documento"] == "1") ? 'selected' : ''; ?>>1 - DNI</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-8 col-lg-8">
                                <div class="mb-3">
                                    <label for="" class="form-label"><b>RUC <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="idRuc" 
                                           value="<?php echo $emisorExiste ? $datosEmisor[0]["ruc"] : ''; ?>" 
                                           disabled
                                           maxlength="11"
                                           placeholder="20123456789">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><b>Razón Social <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="idRazonSocial" 
                                           value="<?php echo $emisorExiste ? $datosEmisor[0]["razon_social"] : ''; ?>" 
                                           disabled
                                           placeholder="MI EMPRESA S.A.C.">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><b>Nombre Comercial <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="idNombreComercial" 
                                           value="<?php echo $emisorExiste ? $datosEmisor[0]["nombre_comercial"] : ''; ?>" 
                                           disabled
                                           placeholder="MI NEGOCIO">
                                </div>
                            </div>

                            <hr>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label for="horasPersona" class="form-label"><b>Usuario SOL <span class="fw-bold text-danger">*</span></b></label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control" 
                                               id="idUsuarioSol" 
                                               value="<?php echo $emisorExiste ? $datosEmisor[0]["usuario_sol"] : ''; ?>" 
                                               disabled
                                               placeholder="MODDATOS">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label for="diasPersona" class="form-label"><b>Clave Sol <span class="fw-bold text-danger">*</span></b></label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control" 
                                               id="idClaveSol" 
                                               value="<?php echo $emisorExiste ? $datosEmisor[0]["clave_sol"] : ''; ?>" 
                                               disabled
                                               placeholder="••••••••">
                                    </div>
                                </div>
                            </div>

                            <!-- Botón para alternar entre mostrar y ocultar las contraseñas -->
                            <button type="button" id="togglePassword" class="btn btn-link mt-2">
                                <i class="fas fa-eye"></i> Mostrar
                            </button>

                            <script>
                                const passwordFields = document.querySelectorAll("#idUsuarioSol, #idClaveSol");
                                const togglePasswordButton = document.getElementById("togglePassword");

                                togglePasswordButton.addEventListener("click", function() {
                                    passwordFields.forEach(function(field) {
                                        const type = field.type === "password" ? "text" : "password";
                                        field.type = type;
                                    });

                                    const icon = togglePasswordButton.querySelector('i');
                                    if (icon.classList.contains('fa-eye')) {
                                        icon.classList.remove('fa-eye');
                                        icon.classList.add('fa-eye-slash');
                                        togglePasswordButton.innerHTML = '<i class="fas fa-eye-slash"></i> Ocultar';
                                    } else {
                                        icon.classList.remove('fa-eye-slash');
                                        icon.classList.add('fa-eye');
                                        togglePasswordButton.innerHTML = '<i class="fas fa-eye"></i> Mostrar';
                                    }
                                });
                            </script>

                            <hr>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><b>Departamento <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="idDepartamento" 
                                           value="<?php echo $emisorExiste ? $datosEmisor[0]["departamento"] : ''; ?>" 
                                           disabled
                                           placeholder="LAMBAYEQUE">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label"><b>Provincia <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="idProvincia" 
                                           value="<?php echo $emisorExiste ? $datosEmisor[0]["provincia"] : ''; ?>" 
                                           disabled
                                           placeholder="CHICLAYO">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><b>Distrito</b></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="idDistrito" 
                                           value="<?php echo $emisorExiste ? $datosEmisor[0]["distrito"] : ''; ?>" 
                                           disabled
                                           placeholder="CHICLAYO">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-6 col-lg-6">
                                <div class="mb-3">
                                    <label for="" class="form-label"><b>Ubigeo</b></label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="idUbigeo" 
                                           value="<?php echo $emisorExiste ? $datosEmisor[0]["ubigeo"] : ''; ?>" 
                                           disabled
                                           placeholder="140101">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="" class="form-label"><b>Dirección Fiscal <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="idDireccion" 
                                           value="<?php echo $emisorExiste ? $datosEmisor[0]["direccion"] : ''; ?>" 
                                           disabled
                                           placeholder="AV. PRINCIPAL NRO. 123">
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
                                   name="" 
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

<?php
include("pie.php");
?>

<script>
    // ✅ VARIABLE GLOBAL SUCURSAL_ID
    const SUCURSAL_ID = <?php echo json_encode($sucursal_id); ?>;
    
    console.log("🏢 Sucursal ID activa:", SUCURSAL_ID);

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

    function fn_guardar_cambios() {
        console.log("💾 === GUARDANDO CAMBIOS ===");
        
        // ✅ VALIDACIONES
        const tipoDocumento = document.getElementById("idTipoDocumento").value.trim();
        const ruc = document.getElementById("idRuc").value.trim();
        const razonSocial = document.getElementById("idRazonSocial").value.trim();
        const nombreComercial = document.getElementById("idNombreComercial").value.trim();
        const usuarioSol = document.getElementById("idUsuarioSol").value.trim();
        const claveSol = document.getElementById("idClaveSol").value.trim();
        const departamento = document.getElementById("idDepartamento").value.trim();
        const provincia = document.getElementById("idProvincia").value.trim();
        const direccion = document.getElementById("idDireccion").value.trim();

        // Validar campos obligatorios
        if (!tipoDocumento || !ruc || !razonSocial || !nombreComercial || !usuarioSol || !claveSol || 
            !departamento || !provincia || !direccion) {
            swal("Error", "Por favor, complete todos los campos obligatorios (*)", {
                icon: "error",
                buttons: {
                    confirm: {
                        className: "btn btn-danger",
                    },
                },
            });
            return;
        }

        // Validar RUC (11 dígitos si es tipo 6)
        if (tipoDocumento === "6" && (ruc.length !== 11 || !/^\d+$/.test(ruc))) {
            swal("Error", "El RUC debe tener exactamente 11 dígitos numéricos", {
                icon: "error",
                buttons: {
                    confirm: {
                        className: "btn btn-danger",
                    },
                },
            });
            return;
        }

        // Validar DNI (8 dígitos si es tipo 1)
        if (tipoDocumento === "1" && (ruc.length !== 8 || !/^\d+$/.test(ruc))) {
            swal("Error", "El DNI debe tener exactamente 8 dígitos numéricos", {
                icon: "error",
                buttons: {
                    confirm: {
                        className: "btn btn-danger",
                    },
                },
            });
            return;
        }

        // ✅ PREPARAR DATOS CON SUCURSAL_ID Y TIPO_DOCUMENTO
        var datos_emisor = {
            "sucursal_id": SUCURSAL_ID,
            "tipo_documento": tipoDocumento, // ✅ NUEVO CAMPO
            "ruc": ruc,
            "razon_social": razonSocial,
            "nombre_comercial": nombreComercial,
            "usuario_sol": usuarioSol,
            "clave_sol": claveSol,
            "departamento": departamento,
            "provincia": provincia,
            "distrito": document.getElementById("idDistrito").value.trim(),
            "ubigeo": document.getElementById("idUbigeo").value.trim(),
            "direccion": direccion
        };

        console.log("📤 Datos a enviar:", datos_emisor);

        // ✅ ENVIAR CON AJAX
        $.ajax({
            url: 'logica/clssInsertPA.php',
            type: 'POST',
            data: {
                accion: 'EDITAR_EMISOR',
                jsDatos: JSON.stringify(datos_emisor)
            },
            beforeSend: function() {
                swal({
                    title: "Guardando...",
                    text: "Por favor espere",
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
                            title: "¡Emisor Actualizado!",
                            text: result.mensaje || "Los datos del emisor se guardaron correctamente",
                            icon: "success",
                            buttons: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        swal("Error", result.mensaje || "No se pudo guardar el emisor", {
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
                swal("Error", "Hubo un problema al guardar los datos: " + error, {
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