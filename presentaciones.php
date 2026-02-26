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
    .card-presentacion {
        transition: transform 0.2s;
        cursor: pointer;
    }
    
    .card-presentacion:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(104, 97, 206, 0.3);
    }
    
    .badge-codigo {
        font-size: 14px;
        padding: 8px 15px;
        border-radius: 20px;
    }
    
    .cantidad-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 5px 15px;
        border-radius: 15px;
        font-weight: bold;
    }
</style>

<div class="container">
    <div class="page-inner">
        <div class="card text-start">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h4 class="card-title">
                        <i class="fas fa-box-open"></i> Presentaciones de Venta
                    </h4>
                    <button class="btn btn-success rounded-5" id="btnAgregarPresentacion">
                        <i class="fas fa-plus-circle"></i> Nueva Presentación
                    </button>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Las presentaciones</strong> son las diferentes formas en que vendes tus productos 
                    (Ej: DOCENA, MEDIA DOCENA, POR MAYOR, etc.)
                </div>

                <div class="row g-3" id="listaPresentaciones">
                    <!-- Se llenará dinámicamente con JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Agregar/Editar Presentación -->
<div class="modal fade" id="modalPresentacion" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitulo">
                    <i class="fas fa-box-open"></i> Nueva Presentación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formPresentacion">
                    <input type="hidden" id="presentacionId">
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <strong>Código</strong> <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="inputCodigo" 
                               placeholder="Ej: DOC, MED, MAYOR" maxlength="20" required>
                        <small class="form-text text-muted">Abreviatura única (máx. 20 caracteres)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <strong>Nombre de Presentación</strong> <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="inputPresentacion" 
                               placeholder="Ej: DOCENA, MEDIA DOCENA, POR MAYOR" maxlength="100" required>
                        <small class="form-text text-muted">Nombre completo de la presentación</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <strong>Cantidad de Unidades</strong> <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control" id="inputCantidad" 
                               placeholder="Ej: 12, 6, 100" step="0.01" min="0.01" required>
                        <small class="form-text text-muted">Número de unidades en esta presentación</small>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <small>
                            <strong>Nota:</strong> El código debe ser único. Si ya existe una presentación 
                            con el mismo código, no se podrá guardar.
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btnGuardarPresentacion">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    var SUCURSAL_ID = <?php echo json_encode($sucursal_id); ?>;
    var modalPresentacion;
    var modoEdicion = false;

    $(document).ready(function() {
        modalPresentacion = new bootstrap.Modal(document.getElementById('modalPresentacion'));
        cargarPresentaciones();
        
        // Evento para abrir modal de nueva presentación
        $('#btnAgregarPresentacion').on('click', function() {
            abrirModalNuevo();
        });
        
        // Evento para guardar presentación
        $('#btnGuardarPresentacion').on('click', function() {
            guardarPresentacion();
        });
    });

    function cargarPresentaciones() {
        $.ajax({
            url: 'logica/clssPresentaciones.php',
            type: 'POST',
            data: {
                accion: 'LISTAR',
                sucursal_id: SUCURSAL_ID
            },
            dataType: 'json',
            success: function(response) {
                if (response.estado) {
                    renderizarPresentaciones(response.datos);
                } else {
                    Swal.fire('Error', response.mensaje, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                Swal.fire('Error', 'No se pudieron cargar las presentaciones', 'error');
            }
        });
    }

    function renderizarPresentaciones(presentaciones) {
        var container = $('#listaPresentaciones');
        container.empty();
        
        if (presentaciones.length === 0) {
            container.html(`
                <div class="col-12">
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <h5>No hay presentaciones registradas</h5>
                        <p>Crea tu primera presentación haciendo clic en el botón "Nueva Presentación"</p>
                    </div>
                </div>
            `);
            return;
        }
        
        presentaciones.forEach(function(pres) {
            var card = `
                <div class="col-md-4">
                    <div class="card card-presentacion border-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge badge-codigo bg-primary">${pres.codigo}</span>
                                <div>
                                    <button class="btn btn-warning btn-sm" onclick="editarPresentacion(${pres.id})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="eliminarPresentacion(${pres.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <h5 class="card-title">${pres.presentacion}</h5>
                            
                            <div class="mt-3">
                                <span class="cantidad-badge">
                                    <i class="fas fa-cubes"></i> ${pres.cantidad_numero} unidades
                                </span>
                            </div>
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> 
                                    Creado: ${formatearFecha(pres.created_at)}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.append(card);
        });
    }

    function abrirModalNuevo() {
        modoEdicion = false;
        $('#modalTitulo').html('<i class="fas fa-plus-circle"></i> Nueva Presentación');
        $('#presentacionId').val('');
        $('#inputCodigo').val('').prop('disabled', false);
        $('#inputPresentacion').val('');
        $('#inputCantidad').val('');
        modalPresentacion.show();
    }

    function editarPresentacion(id) {
        modoEdicion = true;
        $('#modalTitulo').html('<i class="fas fa-edit"></i> Editar Presentación');
        
        $.ajax({
            url: 'logica/clssPresentaciones.php',
            type: 'POST',
            data: {
                accion: 'OBTENER',
                id: id
            },
            dataType: 'json',
            success: function(response) {
                if (response.estado) {
                    var pres = response.datos;
                    $('#presentacionId').val(pres.id);
                    $('#inputCodigo').val(pres.codigo).prop('disabled', true);
                    $('#inputPresentacion').val(pres.presentacion);
                    $('#inputCantidad').val(pres.cantidad_numero);
                    modalPresentacion.show();
                } else {
                    Swal.fire('Error', response.mensaje, 'error');
                }
            }
        });
    }

    function guardarPresentacion() {
        var id = $('#presentacionId').val();
        var codigo = $('#inputCodigo').val().trim().toUpperCase();
        var presentacion = $('#inputPresentacion').val().trim().toUpperCase();
        var cantidad = parseFloat($('#inputCantidad').val());
        
        // Validaciones
        if (!codigo || !presentacion || isNaN(cantidad) || cantidad <= 0) {
            Swal.fire('Error', 'Por favor complete todos los campos correctamente', 'warning');
            return;
        }
        
        if (codigo.length > 20) {
            Swal.fire('Error', 'El código no puede tener más de 20 caracteres', 'warning');
            return;
        }
        
        if (presentacion.length > 100) {
            Swal.fire('Error', 'El nombre de presentación no puede tener más de 100 caracteres', 'warning');
            return;
        }
        
        var datos = {
            accion: modoEdicion ? 'EDITAR' : 'CREAR',
            codigo: codigo,
            presentacion: presentacion,
            cantidad_numero: cantidad,
            sucursal_id: SUCURSAL_ID
        };
        
        if (modoEdicion) {
            datos.id = id;
        }
        
        $.ajax({
            url: 'logica/clssPresentaciones.php',
            type: 'POST',
            data: datos,
            dataType: 'json',
            success: function(response) {
                if (response.estado) {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: response.mensaje,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function() {
                        modalPresentacion.hide();
                        cargarPresentaciones();
                    });
                } else {
                    Swal.fire('Error', response.mensaje, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                Swal.fire('Error', 'No se pudo guardar la presentación', 'error');
            }
        });
    }
    

    function eliminarPresentacion(id) {
        Swal.fire({
            title: '¿Eliminar presentación?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'logica/clssPresentaciones.php',
                    type: 'POST',
                    data: {
                        accion: 'ELIMINAR',
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.estado) {
                            Swal.fire({
                                title: '¡Eliminado!',
                                text: response.mensaje,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(function() {
                                cargarPresentaciones();
                            });
                        } else {
                            Swal.fire('Error', response.mensaje, 'error');
                        }
                    }
                });
            }
        });
    }

    function formatearFecha(fecha) {
        if (!fecha) return 'N/A';
        var date = new Date(fecha);
        return date.toLocaleDateString('es-PE', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    }
</script>

<?php
include("pie.php");
?>