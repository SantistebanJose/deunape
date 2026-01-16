<?php
include("cabecera.php");
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
    
    label {
        font-weight: bold;
    }
    
    .error-input {
        border: 2px solid red;
    }

    .error-message {
        color: red;
        font-size: 0.9em;
        margin-top: 5px;
    }

    #modalCliente {
        z-index: 1060 !important;
    }

    #modalCliente .modal-content {
        background-color: rgb(255, 255, 255);
        border-radius: 10px;
        border: 2px solid #2a2f5b;
    }

    #modalCliente .modal-dialog {
        box-shadow: 0 4px 10px #2a2f5b;
    }

    #modalCliente .modal-header {
        background-color: rgb(255, 255, 255);
        color: #2a2f5b;
    }
    
    #modalCliente .btn-close {
        background-color: #f0f8ff;
    }

    .pagination {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 5px;
        margin: 10px 0;
    }

    .pagination a {
        text-decoration: none;
        padding: 8px 12px;
        border: 1px solid #ddd;
        color: #333;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .pagination a:hover {
        background-color: #f0f0f0;
    }

    .pagination a.active {
        background-color: #007bff;
        color: white;
    }

    @media (max-width: 768px) {
        .pagination {
            font-size: 12px;
        }

        .pagination a {
            padding: 6px 10px;
        }

        table {
            font-size: 14px;
        }
    }

    @media (max-width: 480px) {
        .pagination {
            font-size: 10px;
        }

        .pagination a {
            padding: 5px 8px;
        }

        table {
            font-size: 12px;
        }
    }

    .btn-buscar-api {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        padding: 5px 15px;
        font-size: 12px;
    }

    .documento-container {
        position: relative;
    }
</style>

<div class="container">
    <div class="page-inner">
        <div class="card text-start">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title">Personas</h4>
                    <button class="btn btn-success rounded-5" id="btnAbrirModalGenerico">Agregar Persona <i class="fas fa-plus"></i></button>
                </div>
                <hr>
                <div class="row justify-content-center align-items-center md-2">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table id="multi-filter-select" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>N° Documento</th>
                                        <th>Nombre</th>
                                        <th>CONDICION</th>
                                        <th>N° TELEFONO</th>
                                        <th>Accion</th>
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

<!-- Modal para registrar Cliente -->
<div class="modal fade" id="modalCliente" tabindex="-1" data-bs-backdrop="static" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="contenidoUsuario"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
<script src="assets/js/scriptNotify.js"></script>

<script>
    $(document).ready(function() {
        $("#multi-filter-select").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": "logica/listar_personas.php",
                "type": "POST"
            },
            columns: [
                { "data": "id" },
                { "data": "numero_documento" },
                { "data": "nombre" },
                { "data": "condicion" },
                { "data": "telefono" },
                { "data": "acciones", "orderable": false, "searchable": false }
            ],
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
                }
            }
        });
    });

    // Función para buscar en la API
    async function buscarEnAPI(numeroDocumento, tipo) {
        try {
            const response = await fetch(`https://graphperu.daustinn.com/api/query/${numeroDocumento}`);
            
            if (!response.ok) {
                throw new Error('No se pudo consultar la API');
            }
            
            const data = await response.json();
            console.log('Respuesta completa de la API:', data); // Para debug
            return data;
        } catch (error) {
            console.error('Error al consultar API:', error);
            return null;
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("btnAbrirModalGenerico").addEventListener("click", function () {
            abrirModalRegistro();
        });
    });

    function abrirModalRegistro() {
        document.getElementById("contenidoUsuario").innerHTML = `
            <div class="modal-body">
                <div class="card text-start">
                    <div class="card-body">
                        <h4 class="card-title text-center"><i class="fas fa-user"></i> Registrar Persona</h4>
                        <div class="card-sub text-center">
                            Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.
                        </div>
                        
                        <ul class="nav nav-pills nav-secondary nav-pills-no-bd" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" id="pills-persona-tab" data-bs-toggle="pill" data-bs-target="#pills-persona" type="button" role="tab">Cliente | Empleado</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="pills-empresa-tab" data-bs-toggle="pill" data-bs-target="#pills-empresa" type="button" role="tab">Empresa | Proveedor</button>
                            </li>
                        </ul>
                        <hr>
                        
                        <div class="tab-content mt-3" id="pills-tabContent">
                            <!-- Formulario Persona -->
                            <div class="tab-pane fade show active" id="pills-persona" role="tabpanel">
                                <div class="mb-3 documento-container">
                                    <label for="numeroDocumentoPersona" class="form-label"><b>Número de Documento (DNI) <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" class="form-control" id="numeroDocumentoPersona" placeholder="Número de Documento" maxlength="8">
                                    <button type="button" class="btn btn-primary btn-sm btn-buscar-api" id="btnBuscarPersona">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                    <div class="invalid-feedback" id="error-numeroDocumentoPersona"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="nombresPersona" class="form-label"><b>Nombres <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" class="form-control" id="nombresPersona" placeholder="Nombres">
                                    <div class="invalid-feedback" id="error-nombresPersona"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="apellidosPersona" class="form-label"><b>Apellidos <span class="fw-bold text-danger">*</span></b></label>
                                    <input type="text" class="form-control" id="apellidosPersona" placeholder="Apellidos">
                                    <div class="invalid-feedback" id="error-apellidosPersona"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><b>Condición <span class="fw-bold text-danger">*</span></b></label>
                                    <select class="form-select required" id="condicionPersona">
                                        <option value="">Seleccione una opción</option>
                                        <option value="CLIENTE">CLIENTE</option>
                                        <option value="EMPLEADO">EMPLEADO</option>
                                    </select>
                                    <div id="error-condicionPersona" class="error-message"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="telefonoPersona" class="form-label"><b>Teléfono Móvil</b></label>
                                    <input type="text" class="form-control" id="telefonoPersona" placeholder="Teléfono Móvil">
                                    <div class="invalid-feedback" id="error-telefonoPersona"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="emailPersona" class="form-label"><b>Email</b></label>
                                    <input type="email" class="form-control" id="emailPersona" placeholder="Email">
                                    <div class="invalid-feedback" id="error-emailPersona"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="direccionPersona" class="form-label"><b>Dirección</b></label>
                                    <input type="text" class="form-control" id="direccionPersona" placeholder="Dirección">
                                    <div class="invalid-feedback" id="error-direccionPersona"></div>
                                </div>
                            </div>

                            <!-- Formulario Empresa -->
                            <div class="tab-pane fade" id="pills-empresa" role="tabpanel">
                                <div class="mb-3 documento-container">
                                    <label for="numeroDocumentoEmpresa" class="form-label">Número de RUC <span class="fw-bold text-danger">*</span></label>
                                    <input type="text" class="form-control" id="numeroDocumentoEmpresa" placeholder="Número de RUC" maxlength="11">
                                    <button type="button" class="btn btn-primary btn-sm btn-buscar-api" id="btnBuscarEmpresa">
                                        <i class="fas fa-search"></i> Buscar
                                    </button>
                                    <div class="invalid-feedback" id="error-numeroDocumentoEmpresa"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="nombreComercial" class="form-label">Nombre Comercial <span class="fw-bold text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombreComercial" placeholder="Nombre Comercial">
                                    <div class="invalid-feedback" id="error-nombreComercial"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="razonSocial" class="form-label">Razón Social <span class="fw-bold text-danger">*</span></label>
                                    <input type="text" class="form-control" id="razonSocial" placeholder="Razón Social">
                                    <div class="invalid-feedback" id="error-razonSocial"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Condición <span class="fw-bold text-danger">*</span></label>
                                    <select class="form-select required" id="condicionEmpresa">
                                        <option value="">Seleccione una opción</option>
                                        <option value="EMPRESA">EMPRESA</option>
                                        <option value="PROVEEDOR">PROVEEDOR</option>
                                    </select>
                                    <div id="error-condicionEmpresa" class="error-message"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="emailEmpresa" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="emailEmpresa" placeholder="Email">
                                    <div class="invalid-feedback" id="error-emailEmpresa"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="telefonoEmpresa" class="form-label">Teléfono Móvil</label>
                                    <input type="text" class="form-control" id="telefonoEmpresa" placeholder="Teléfono Móvil">
                                    <div class="invalid-feedback" id="error-telefonoEmpresa"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="direccionEmpresa" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="direccionEmpresa" placeholder="Dirección">
                                    <div class="invalid-feedback" id="error-direccionEmpresa"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger rounded-5" data-bs-dismiss="modal">Salir</button>
                <button type="button" class="btn btn-success rounded-5" id="btnRegistrarCliente">Registrar</button>
            </div>
        `;

        const modal = new bootstrap.Modal(document.getElementById("modalCliente"));
        modal.show();

        // ============================================
        // EVENT LISTENER PARA BUSCAR PERSONA POR DNI
        // ============================================
        document.getElementById("btnBuscarPersona").addEventListener("click", async function() {
            const dni = document.getElementById("numeroDocumentoPersona").value.trim();
            
            if (dni.length !== 8) {
                swal("Advertencia", "Ingrese un DNI válido de 8 dígitos", "warning");
                return;
            }

            // Mostrar loading
            Swal.fire({
                title: 'Consultando...',
                text: 'Buscando información del DNI',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const resultado = await buscarEnAPI(dni, 'persona');
            Swal.close();

            console.log("Resultado DNI:", resultado); // Para debug

            if (resultado && resultado.names && resultado.surnames) {
                // Llenar los campos con la información de la API
                const nombres = resultado.names || '';
                const apellidos = resultado.surnames || '';
                
                document.getElementById("nombresPersona").value = nombres;
                document.getElementById("apellidosPersona").value = apellidos;
                
                swal("Éxito", `Datos encontrados: ${resultado.fullName || nombres + ' ' + apellidos}`, "success");
            } else {
                swal("Información", "No se encontraron datos. Complete el formulario manualmente.", "info");
            }
        });

        // ==============================================
        // EVENT LISTENER PARA BUSCAR EMPRESA POR RUC - CORREGIDO
        // ==============================================
        document.getElementById("btnBuscarEmpresa").addEventListener("click", async function() {
            const ruc = document.getElementById("numeroDocumentoEmpresa").value.trim();
            
            if (ruc.length !== 11) {
                swal("Advertencia", "Ingrese un RUC válido de 11 dígitos", "warning");
                return;
            }

            // Mostrar loading
            Swal.fire({
                title: 'Consultando...',
                text: 'Buscando información del RUC en SUNAT',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const resultado = await buscarEnAPI(ruc, 'empresa');
            Swal.close();

            console.log("Resultado RUC completo:", resultado); // Para debug

            if (resultado) {
                // La API devuelve diferentes estructuras según el tipo de documento
                // Para RUC (11 dígitos), los campos son: name, address, state, condition
                
                let razonSocialData = null;
                let nombreComercialData = null;
                let direccionData = null;
                
                // Intentar obtener el nombre de diferentes campos posibles
                if (resultado.name) {
                    // Formato de RUC: usa 'name' directamente
                    razonSocialData = resultado.name;
                    nombreComercialData = resultado.name;
                } else if (resultado.razonSocial) {
                    // Formato alternativo
                    razonSocialData = resultado.razonSocial;
                    nombreComercialData = resultado.nombreComercial || resultado.razonSocial;
                } else if (resultado.fullName) {
                    // Otro formato posible
                    razonSocialData = resultado.fullName;
                    nombreComercialData = resultado.fullName;
                } else if (resultado.names) {
                    // Para RUC tipo persona natural
                    razonSocialData = resultado.names;
                    nombreComercialData = resultado.names;
                }
                
                // Obtener dirección si está disponible
                if (resultado.address && resultado.address !== '-') {
                    direccionData = resultado.address;
                }
                
                if (razonSocialData) {
                    // Llenar los campos con la información de la API
                    document.getElementById("razonSocial").value = razonSocialData;
                    document.getElementById("nombreComercial").value = nombreComercialData;
                    
                    // Llenar dirección si está disponible
                    if (direccionData) {
                        document.getElementById("direccionEmpresa").value = direccionData;
                    }
                    
                    // Construir mensaje de éxito con información adicional
                    let mensajeDetalle = razonSocialData;
                    if (resultado.state) {
                        mensajeDetalle += `\nEstado: ${resultado.state}`;
                    }
                    if (resultado.condition) {
                        mensajeDetalle += `\nCondición: ${resultado.condition}`;
                    }
                    
                    swal({
                        title: "Éxito",
                        text: `Datos encontrados correctamente\n\n${mensajeDetalle}`,
                        icon: "success"
                    });
                } else {
                    console.warn('No se encontró información válida en la respuesta:', resultado);
                    swal("Información", "No se encontraron datos completos. Por favor, complete el formulario manualmente.", "info");
                }
            } else {
                swal("Error", "No se pudo consultar el RUC. Verifique el número e intente nuevamente.", "error");
            }
        });

        // Resto del código (event listeners de tabs, validaciones, registro, etc.)
        const personaTab = document.getElementById("pills-persona-tab");
        const empresaTab = document.getElementById("pills-empresa-tab");

        personaTab.addEventListener('click', () => {
            document.getElementById('numeroDocumentoEmpresa').value = '';
            document.getElementById('nombreComercial').value = '';
            document.getElementById('razonSocial').value = '';
            document.getElementById('telefonoEmpresa').value = '';
            document.getElementById('emailEmpresa').value = '';
            document.getElementById('direccionEmpresa').value = '';
            document.getElementById('condicionEmpresa').selectedIndex = 0;
            resetErrors();
        });

        empresaTab.addEventListener('click', () => {
            document.getElementById('numeroDocumentoPersona').value = '';
            document.getElementById('nombresPersona').value = '';
            document.getElementById('apellidosPersona').value = '';
            document.getElementById('telefonoPersona').value = '';
            document.getElementById('emailPersona').value = '';
            document.getElementById('direccionPersona').value = '';
            document.getElementById('condicionPersona').selectedIndex = 0;
            resetErrors();
        });

        function resetErrors() {
            const inputs = document.querySelectorAll('.form-control');
            const errorMessages = document.querySelectorAll('.invalid-feedback');
            inputs.forEach(input => input.classList.remove('is-invalid'));
            errorMessages.forEach(message => message.textContent = '');
        }

        function validarCamposPersona() {
            let valid = true;

            const numeroDocumentoPersona = document.getElementById('numeroDocumentoPersona');
            const errorNumeroDocumentoPersona = document.getElementById('error-numeroDocumentoPersona');
            if (numeroDocumentoPersona.value.trim() === '') {
                valid = false;
                numeroDocumentoPersona.classList.add('is-invalid');
                errorNumeroDocumentoPersona.textContent = 'El DNI es obligatorio.';
            } else if (!/^\d{8}$/.test(numeroDocumentoPersona.value)) {
                valid = false;
                numeroDocumentoPersona.classList.add('is-invalid');
                errorNumeroDocumentoPersona.textContent = 'Debe ser un DNI válido (8 dígitos).';
            } else {
                numeroDocumentoPersona.classList.remove('is-invalid');
                errorNumeroDocumentoPersona.textContent = '';
            }

            const nombresPersona = document.getElementById('nombresPersona');
            const errorNombresPersona = document.getElementById('error-nombresPersona');
            if (nombresPersona.value.trim() == '') {
                valid = false;
                nombresPersona.classList.add('is-invalid');
                errorNombresPersona.textContent = 'Los nombres son obligatorios.';
            } else if(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/.test(nombresPersona.value)) {
                valid = false;
                nombresPersona.classList.add('is-invalid');
                errorNombresPersona.textContent = 'Los nombres no pueden contener números.';
            } else {
                nombresPersona.classList.remove('is-invalid');
                errorNombresPersona.textContent = '';
            }

            const apellidosPersona = document.getElementById('apellidosPersona');
            const errorApellidosPersona = document.getElementById('error-apellidosPersona');
            if (apellidosPersona.value.trim() == '') {
                valid = false;
                apellidosPersona.classList.add('is-invalid');
                errorApellidosPersona.textContent = 'Los apellidos son obligatorios.';
            } else if(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/.test(apellidosPersona.value)) {
                valid = false;
                apellidosPersona.classList.add('is-invalid');
                errorApellidosPersona.textContent = 'Los apellidos no pueden contener números.';
            } else {
                apellidosPersona.classList.remove('is-invalid');
                errorApellidosPersona.textContent = '';
            }

            const telefonoPersona = document.getElementById('telefonoPersona');
            const errorTelefonoPersona = document.getElementById('error-telefonoPersona');
            if (telefonoPersona.value.trim() !== '' && !/^\d{9}$/.test(telefonoPersona.value)) {
                valid = false;
                telefonoPersona.classList.add('is-invalid');
                errorTelefonoPersona.textContent = 'El teléfono debe tener 9 dígitos.';
            } else {
                telefonoPersona.classList.remove('is-invalid');
                errorTelefonoPersona.textContent = '';
            }

            const emailPersona = document.getElementById('emailPersona');
            const errorEmailPersona = document.getElementById('error-emailPersona');
            if (emailPersona.value.trim() !== '' && !/\S+@\S+\.\S+/.test(emailPersona.value)) {
                valid = false;
                emailPersona.classList.add('is-invalid');
                errorEmailPersona.textContent = 'Debe ser un correo electrónico válido.';
            } else {
                emailPersona.classList.remove('is-invalid');
                errorEmailPersona.textContent = '';
            }

            const condicion = document.getElementById('condicionPersona');
            const errorCondicion = document.getElementById('error-condicionPersona');
            if (condicion.value === '') {
                valid = false;
                condicion.classList.add('is-invalid');
                errorCondicion.textContent = 'Debe seleccionar una opción válida.';
            } else {
                condicion.classList.remove('is-invalid');
                errorCondicion.textContent = '';
            }

            return valid;
        }

        function validarCamposEmpresa() {
            let valid = true;

            const numeroDocumentoEmpresa = document.getElementById('numeroDocumentoEmpresa');
            const errorNumeroDocumentoEmpresa = document.getElementById('error-numeroDocumentoEmpresa');
            if (numeroDocumentoEmpresa.value.trim() === '') {
                valid = false;
                numeroDocumentoEmpresa.classList.add('is-invalid');
                errorNumeroDocumentoEmpresa.textContent = 'El RUC es obligatorio.';
            } else if (!/^\d{11}$/.test(numeroDocumentoEmpresa.value)) {
                valid = false;
                numeroDocumentoEmpresa.classList.add('is-invalid');
                errorNumeroDocumentoEmpresa.textContent = 'Debe ser un RUC válido (11 dígitos).';
            } else {
                numeroDocumentoEmpresa.classList.remove('is-invalid');
                errorNumeroDocumentoEmpresa.textContent = '';
            }

            const nombreComercial = document.getElementById('nombreComercial');
            const errorNombreComercial = document.getElementById('error-nombreComercial');
            if (nombreComercial.value.trim() == '') {
                valid = false;
                nombreComercial.classList.add('is-invalid');
                errorNombreComercial.textContent = 'Este campo es obligatorio.';
            } else {
                nombreComercial.classList.remove('is-invalid');
                errorNombreComercial.textContent = '';
            }

            const razonSocial = document.getElementById('razonSocial');
            const errorRazonSocial = document.getElementById('error-razonSocial');
            if (razonSocial.value.trim() == '') {
                valid = false;
                razonSocial.classList.add('is-invalid');
                errorRazonSocial.textContent = 'Este campo es obligatorio.';
            } else {
                razonSocial.classList.remove('is-invalid');
                errorRazonSocial.textContent = '';
            }

            const telefonoEmpresa = document.getElementById('telefonoEmpresa');
            const errorTelefonoEmpresa = document.getElementById('error-telefonoEmpresa');
            if (telefonoEmpresa.value.trim() !== '' && !/^\d{9}$/.test(telefonoEmpresa.value)) {
                valid = false;
                telefonoEmpresa.classList.add('is-invalid');
                errorTelefonoEmpresa.textContent = 'El teléfono debe tener 9 dígitos.';
            } else {
                telefonoEmpresa.classList.remove('is-invalid');
                errorTelefonoEmpresa.textContent = '';
            }

            const emailEmpresa = document.getElementById('emailEmpresa');
            const errorEmailEmpresa = document.getElementById('error-emailEmpresa');
            if (emailEmpresa.value.trim() !== '' && !/\S+@\S+\.\S+/.test(emailEmpresa.value)) {
                valid = false;
                emailEmpresa.classList.add('is-invalid');
                errorEmailEmpresa.textContent = 'Debe ser un correo electrónico válido.';
            } else {
                emailEmpresa.classList.remove('is-invalid');
                errorEmailEmpresa.textContent = '';
            }

            const condicion = document.getElementById('condicionEmpresa');
            const errorCondicion = document.getElementById('error-condicionEmpresa');
            if (condicion.value === '') {
                valid = false;
                condicion.classList.add('is-invalid');
                errorCondicion.textContent = 'Debe seleccionar una opción válida.';
            } else {
                condicion.classList.remove('is-invalid');
                errorCondicion.textContent = '';
            }

            return valid;
        }

        document.getElementById("btnRegistrarCliente").addEventListener('click', async function () {
            let datos = {};
            
            if (document.getElementById('pills-persona-tab').classList.contains('active')) {
                if (validarCamposPersona()) {
                    datos = {
                        "numero_documento": document.getElementById('numeroDocumentoPersona').value,
                        "nombres": document.getElementById('nombresPersona').value,
                        "apellidos": document.getElementById('apellidosPersona').value,
                        "telefono_movil": document.getElementById('telefonoPersona').value || null,
                        "email": document.getElementById('emailPersona').value,
                        "direccion": document.getElementById('direccionPersona').value,
                        "condicion": document.getElementById('condicionPersona').value
                    };

                    try {
                        const response = await fnRegistrarPersona(datos);
                        swal({
                            title: "Registro con Éxito!",
                            text: "Persona registrada correctamente",
                            icon: "success",
                            buttons: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } catch (error) {
                        swal("Error", error.message || "Ocurrió un error inesperado", {
                            icon: "error",
                            buttons: {
                                confirm: { className: "btn btn-danger" }
                            }
                        });
                    }
                }
            } else if (document.getElementById('pills-empresa-tab').classList.contains('active')) {
                if (validarCamposEmpresa()) {
                    datos = {
                        "numero_documento": document.getElementById('numeroDocumentoEmpresa').value,
                        "nombre_comercial": document.getElementById('nombreComercial').value,
                        "razon_social": document.getElementById('razonSocial').value,
                        "telefono_movil": document.getElementById('telefonoEmpresa').value,
                        "email": document.getElementById('emailEmpresa').value,
                        "direccion": document.getElementById('direccionEmpresa').value,
                        "condicion": document.getElementById('condicionEmpresa').value
                    };

                    try {
                        const response = await fnRegistrarEmpresa(datos);
                        swal({
                            title: "Registro con Éxito!",
                            text: 'Empresa registrada correctamente',
                            icon: "success",
                            buttons: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } catch (error) {
                        swal("Error", error.message || "Ocurrió un error inesperado", {
                            icon: "error",
                            buttons: {
                                confirm: { className: "btn btn-danger" }
                            }
                        });
                    }
                }
            }
        });

        function fnRegistrarPersona(datos) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    method: "POST",
                    url: "logica/clssPersona.php",
                    data: {
                        "accion": "REGISTRARPERSONA",
                        "data": JSON.stringify(datos)
                    }
                }).done(function (response) {
                    const jsonResponse = JSON.parse(response);
                    if (jsonResponse.success) {
                        resolve(jsonResponse);
                    } else {
                        reject(new Error(jsonResponse.message || "Error desconocido"));
                    }
                }).fail(function (error) {
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
                        "accion": "REGISTRARPERSONA",
                        "data": JSON.stringify(datos)
                    }
                }).done(function (response) {
                    const jsonResponse = JSON.parse(response);
                    if (jsonResponse.success) {
                        resolve(jsonResponse);
                    } else {
                        reject(new Error(jsonResponse.mensaje || "Error desconocido"));
                    }
                }).fail(function (error) {
                    reject(error);
                });
            });
        }
    }

    function fn_editar_usuario(datosUsuario) {
        document.getElementById("contenidoUsuario").innerHTML = `
            <div class="modal-body">
                <div class="card text-start">
                    <div class="card-body">
                        <h4 class="card-title text-center"><i class="fas fa-user"></i> Editar Persona</h4>
                        <div class="card-sub text-center">
                            Los campos con <span class="fw-bold text-danger">*</span> son obligatorios.
                        </div>
                        
                        <ul class="nav nav-pills nav-secondary nav-pills-no-bd" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" id="pills-persona-tab" data-bs-toggle="pill" data-bs-target="#pills-persona" type="button" role="tab">Cliente | Empleado</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" id="pills-empresa-tab" data-bs-toggle="pill" data-bs-target="#pills-empresa" type="button" role="tab">Empresa | Proveedor</button>
                            </li>
                        </ul>
                        <hr>
                        
                        <div class="tab-content mt-3" id="pills-tabContent">
                            <!-- Formulario Persona -->
                            <div class="tab-pane fade show active" id="pills-persona" role="tabpanel">
                                <div class="mb-3">
                                    <label for="numeroDocumentoPersona" class="form-label">Número de Documento <span class="fw-bold text-danger">*</span></label>
                                    <input type="text" class="form-control" id="numeroDocumentoPersona" placeholder="Número de Documento">
                                    <div class="error-message" id="error-numeroDocumentoPersona"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="nombresPersona" class="form-label">Nombres <span class="fw-bold text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombresPersona" placeholder="Nombres">
                                    <div class="error-message" id="error-nombresPersona"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="apellidosPersona" class="form-label">Apellidos <span class="fw-bold text-danger">*</span></label>
                                    <input type="text" class="form-control" id="apellidosPersona" placeholder="Apellidos">
                                    <div class="error-message" id="error-apellidosPersona"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Condición <span class="fw-bold text-danger">*</span></label>
                                    <select class="form-select required" id="condicionPersona">
                                        <option value="">Seleccione una opción</option>
                                        <option value="CLIENTE">CLIENTE</option>
                                        <option value="EMPLEADO">EMPLEADO</option>
                                    </select>
                                    <div id="error-condicionPersona" class="error-message"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="telefonoPersona" class="form-label">Teléfono Móvil</label>
                                    <input type="text" class="form-control" id="telefonoPersona" placeholder="Teléfono Móvil">
                                </div>
                                <div class="mb-3">
                                    <label for="emailPersona" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="emailPersona" placeholder="Email">
                                </div>
                                <div class="mb-3">
                                    <label for="direccionPersona" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="direccionPersona" placeholder="Dirección">
                                </div>
                            </div>

                            <!-- Formulario Empresa -->
                            <div class="tab-pane fade" id="pills-empresa" role="tabpanel">
                                <div class="mb-3">
                                    <label for="numeroDocumentoEmpresa" class="form-label">Número de RUC <span class="fw-bold text-danger">*</span></label>
                                    <input type="text" class="form-control" id="numeroDocumentoEmpresa" placeholder="Número de Documento">
                                    <div class="error-message" id="error-numeroDocumentoEmpresa"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="nombreComercial" class="form-label">Nombre Comercial <span class="fw-bold text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nombreComercial" placeholder="Nombre Comercial">
                                    <div class="error-message" id="error-nombreComercial"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="razonSocial" class="form-label">Razón Social <span class="fw-bold text-danger">*</span></label>
                                    <input type="text" class="form-control" id="razonSocial" placeholder="Razón Social">
                                    <div class="error-message" id="error-razonSocial"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Condición <span class="fw-bold text-danger">*</span></label>
                                    <select class="form-select required" id="condicionEmpresa">
                                        <option value="">Seleccione una opción</option>
                                        <option value="EMPRESA">EMPRESA</option>
                                        <option value="PROVEEDOR">PROVEEDOR</option>
                                    </select>
                                    <div id="error-condicionEmpresa" class="error-message"></div>
                                </div>
                                <div class="mb-3">
                                    <label for="emailEmpresa" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="emailEmpresa" placeholder="Email">
                                </div>
                                <div class="mb-3">
                                    <label for="telefonoEmpresa" class="form-label">Teléfono Móvil</label>
                                    <input type="text" class="form-control" id="telefonoEmpresa" placeholder="Teléfono Móvil">
                                </div>
                                <div class="mb-3">
                                    <label for="direccionEmpresa" class="form-label">Dirección</label>
                                    <input type="text" class="form-control" id="direccionEmpresa" placeholder="Dirección">
                                </div>
                            </div>
                            <p id="txtcondicion" style="display: none;"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger rounded-5" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success rounded-5" id="btnEditarCliente">Actualizar</button>
            </div>
        `;
        
        const modal = new bootstrap.Modal(document.getElementById("modalCliente"));
        modal.show();

        obtenerDatosUsuario(datosUsuario.id);

        function obtenerDatosUsuario(id) {
            $.ajax({
                method: "POST",
                url: "logica/clssPersona.php",
                data: {
                    "accion": "OBTENERPERSONA",
                    "id": id
                },
                success: function(response) {
                    var result = JSON.parse(response);

                    if (result.success === true) {
                        const usuario = result.data;
                        document.getElementById("txtcondicion").textContent = usuario.condicion;

                        if (usuario.condicion === "CLIENTE" || usuario.condicion === "EMPLEADO") {
                            document.getElementById("pills-persona-tab").classList.add("active");
                            document.getElementById("pills-empresa-tab").classList.remove("active");
                            document.getElementById("pills-persona").classList.add("show", "active");
                            document.getElementById("pills-empresa").classList.remove("show", "active");

                            document.getElementById("numeroDocumentoPersona").value = usuario.numero_documento;
                            document.getElementById("nombresPersona").value = usuario.nombres;
                            document.getElementById("apellidosPersona").value = usuario.apellidos;
                            document.getElementById("telefonoPersona").value = usuario.telefonomovil;
                            document.getElementById("emailPersona").value = usuario.email;
                            document.getElementById("direccionPersona").value = usuario.direccion;
                            document.getElementById("condicionPersona").value = usuario.condicion;

                            document.getElementById("pills-empresa-tab").style.display = "none";
                            document.getElementById("pills-empresa").style.display = "none";
                        } else if (usuario.condicion === "PROVEEDOR" || usuario.condicion === "EMPRESA") {
                            document.getElementById("pills-empresa-tab").classList.add("active");
                            document.getElementById("pills-persona-tab").classList.remove("active");
                            document.getElementById("pills-empresa").classList.add("show", "active");
                            document.getElementById("pills-persona").classList.remove("show", "active");

                            document.getElementById("numeroDocumentoEmpresa").value = usuario.numero_documento;
                            document.getElementById("nombreComercial").value = usuario.nombre_comercial;
                            document.getElementById("razonSocial").value = usuario.razon_social;
                            document.getElementById("emailEmpresa").value = usuario.email;
                            document.getElementById("telefonoEmpresa").value = usuario.telefonomovil;
                            document.getElementById("direccionEmpresa").value = usuario.direccion;
                            document.getElementById("condicionEmpresa").value = usuario.condicion;

                            document.getElementById("pills-persona-tab").style.display = "none";
                            document.getElementById("pills-persona").style.display = "none";
                        }
                    } else {
                        swal("Error", result.message, {
                            icon: "error",
                            buttons: { confirm: { className: "btn btn-danger" } }
                        });
                    }
                }
            });
        }

        document.getElementById("btnEditarCliente").addEventListener("click", async function () {
            const condicion = document.getElementById("txtcondicion").textContent;
            let datos = {};
            
            if (condicion === "CLIENTE" || condicion === "EMPLEADO") {
                datos = {
                    "id": datosUsuario.id,
                    "numero_documento": document.getElementById('numeroDocumentoPersona').value,
                    "nombres": document.getElementById('nombresPersona').value,
                    "apellidos": document.getElementById('apellidosPersona').value,
                    "telefono_movil": document.getElementById('telefonoPersona').value || null,
                    "email": document.getElementById('emailPersona').value,
                    "direccion": document.getElementById('direccionPersona').value,
                    "condicion": document.getElementById('condicionPersona').value
                };

                fnActualizarPersona(datos);
            } else if(condicion === "PROVEEDOR" || condicion === "EMPRESA") {
                datos = {
                    "id": datosUsuario.id,
                    "numero_documento": document.getElementById('numeroDocumentoEmpresa').value,
                    "nombre_comercial": document.getElementById('nombreComercial').value,
                    "razon_social": document.getElementById('razonSocial').value,
                    "telefono_movil": document.getElementById('telefonoEmpresa').value,
                    "email": document.getElementById('emailEmpresa').value,
                    "direccion": document.getElementById('direccionEmpresa').value,
                    "condicion": document.getElementById('condicionEmpresa').value
                };

                fnActualizarEmpresa(datos);
            }
        });

        function fnActualizarPersona(datos) {
            $.ajax({
                method: "POST",
                url: "logica/clssPersona.php",
                data: {
                    "accion": "ACTUALIZARPERSONA",
                    "data": JSON.stringify(datos)
                }
            }).done(function (response) {
                const jsonResponse = JSON.parse(response);
                if (jsonResponse.success) {
                    swal({
                        title: "Actualización Exitosa!",
                        text: 'Persona actualizada correctamente',
                        icon: "success",
                        buttons: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    swal("Error", jsonResponse.message, {
                        icon: "error",
                        buttons: { confirm: { className: "btn btn-danger" } }
                    });
                }
            });
        }

        function fnActualizarEmpresa(datos) {
            $.ajax({
                method: "POST",
                url: "logica/clssPersona.php",
                data: {
                    "accion": "ACTUALIZARPERSONA",
                    "data": JSON.stringify(datos)
                }
            }).done(function (response) {
                const jsonResponse = JSON.parse(response);
                if (jsonResponse.success) {
                    swal({
                        title: "Actualización Exitosa!",
                        text: 'Empresa actualizada correctamente',
                        icon: "success",
                        buttons: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    swal("Error", jsonResponse.message, {
                        icon: "error",
                        buttons: { confirm: { className: "btn btn-danger" } }
                    });
                }
            });
        }
    }

    function fn_bloquear_usuario(datosUsuario) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción bloqueará a la persona.",
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
                    url: "logica/clssPersona.php",
                    data: {
                        "accion": "BLOQUEARPERSONA",
                        "id": datosUsuario
                    }
                }).done(function (response) {
                    var result = JSON.parse(response);
                    if (result.success === true) {
                        location.reload();
                    } else {
                        swal("Error", result.message, {
                            icon: "error",
                            buttons: { confirm: { className: "btn btn-danger" } }
                        });
                    }
                });
            }
        });
    }

    function fn_desbloquear_usuario(datosUsuario) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción desbloqueará a la persona.",
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
                    url: "logica/clssPersona.php",
                    data: {
                        "accion": "DESBLOQUEARPERSONA",
                        "id": datosUsuario
                    }
                }).done(function (response) {
                    var result = JSON.parse(response);
                    if (result.success === true) {
                        location.reload();
                    } else {
                        swal("Error", result.message, {
                            icon: "error",
                            buttons: { confirm: { className: "btn btn-danger" } }
                        });
                    }
                });
            }
        });
    }

    function fn_eliminar_usuario(datosUsuario) {
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
                $.ajax({
                    method: "POST",
                    url: "logica/clssPersona.php",
                    data: {
                        "accion": "ELIMINARPERSONA",
                        "id": datosUsuario
                    }
                }).done(function (response) {
                    var result = JSON.parse(response);
                    if (result.success === true) {
                        location.reload();
                    } else {
                        swal("Error", result.message, {
                            icon: "error",
                            buttons: { confirm: { className: "btn btn-danger" } }
                        });
                    }
                });
            }
        });
    }
</script>

<?php
include("pie.php");
?>