// ============================================
// SISTEMA DE PRECIOS POR PRESENTACIÓN
// ============================================

// Variables globales para precios
let preciosPresentacion = [];

/**
 * Renderiza la tabla de precios por presentación
 */
function renderizarTablaPresentaciones() {
    const tbody = document.getElementById('tablaPresentacionesBody');
    const contador = document.getElementById('contadorPresentaciones');
    const noPreciosMsg = document.getElementById('noPreciosMsg');
    
    if (!tbody) return;
    
    // Actualizar contador
    if (contador) {
        contador.textContent = preciosPresentacion.length;
    }
    
    // Mostrar mensaje si no hay precios
    if (preciosPresentacion.length === 0) {
        tbody.innerHTML = '';
        if (noPreciosMsg) {
            noPreciosMsg.style.display = 'block';
        }
        return;
    }
    
    if (noPreciosMsg) {
        noPreciosMsg.style.display = 'none';
    }
    
    // Generar filas de la tabla
    let html = '';
    preciosPresentacion.forEach((precio, index) => {
        html += `
            <tr>
                <td>
                    <input type="text" 
                           class="form-control form-control-sm" 
                           value="${precio.presentacion}" 
                           onchange="actualizarPresentacion(${index}, 'presentacion', this.value)"
                           placeholder="Ej: DOCENA, MEDIA DC, POR MAYOR">
                </td>
                <td>
                    <input type="text" 
                           class="form-control form-control-sm" 
                           value="${precio.codigo || ''}" 
                           onchange="actualizarPresentacion(${index}, 'codigo', this.value)"
                           placeholder="Código">
                </td>
                <td>
                    <input type="number" 
                           step="0.01" 
                           class="form-control form-control-sm text-end" 
                           value="${precio.cantidad}" 
                           onchange="actualizarPresentacion(${index}, 'cantidad', this.value)"
                           min="0.01">
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">S/.</span>
                        <input type="number" 
                               step="0.01" 
                               class="form-control text-end" 
                               value="${precio.precio}" 
                               onchange="actualizarPresentacion(${index}, 'precio', this.value)"
                               min="0">
                    </div>
                </td>
                <td class="text-center">
                    <button class="btn btn-danger btn-sm" 
                            onclick="eliminarPresentacion(${index})" 
                            title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

/**
 * Agregar una nueva presentación de precio
 */
function agregarPresentacion() {
    const inputPresentacion = document.getElementById('inputNuevaPresentacion');
    const inputCodigo = document.getElementById('inputNuevoCodigo');
    const inputCantidad = document.getElementById('inputNuevaCantidad');
    const inputPrecio = document.getElementById('inputNuevoPrecio');
    
    // Validaciones
    if (!inputPresentacion || !inputPresentacion.value.trim()) {
        swal("Error", "Debe ingresar un nombre de presentación", {
            icon: "warning",
            buttons: { confirm: { className: "btn btn-warning" } }
        });
        return;
    }
    
    if (!inputCantidad || !inputCantidad.value || parseFloat(inputCantidad.value) <= 0) {
        swal("Error", "Debe ingresar una cantidad válida mayor a 0", {
            icon: "warning",
            buttons: { confirm: { className: "btn btn-warning" } }
        });
        return;
    }
    
    if (!inputPrecio || !inputPrecio.value || parseFloat(inputPrecio.value) < 0) {
        swal("Error", "Debe ingresar un precio válido", {
            icon: "warning",
            buttons: { confirm: { className: "btn btn-warning" } }
        });
        return;
    }
    
    // Agregar nueva presentación
    const nuevaPresentacion = {
        presentacion: inputPresentacion.value.trim().toUpperCase(),
        codigo: inputCodigo && inputCodigo.value ? inputCodigo.value.trim() : '',
        cantidad: parseFloat(inputCantidad.value),
        precio: parseFloat(inputPrecio.value)
    };
    
    preciosPresentacion.push(nuevaPresentacion);
    
    // Limpiar inputs
    inputPresentacion.value = '';
    if (inputCodigo) inputCodigo.value = '';
    inputCantidad.value = '1.00';
    inputPrecio.value = '0.00';
    
    // Re-renderizar
    renderizarTablaPresentaciones();
    
    // Feedback
    swal("¡Agregado!", "Presentación agregada correctamente", {
        icon: "success",
        buttons: false,
        timer: 1000
    });
}

/**
 * Actualizar un campo de una presentación existente
 */
function actualizarPresentacion(index, campo, valor) {
    if (index >= 0 && index < preciosPresentacion.length) {
        if (campo === 'cantidad' || campo === 'precio') {
            preciosPresentacion[index][campo] = parseFloat(valor) || 0;
        } else {
            preciosPresentacion[index][campo] = valor;
        }
        renderizarTablaPresentaciones();
    }
}

/**
 * Eliminar una presentación
 */
function eliminarPresentacion(index) {
    Swal.fire({
        title: '¿Eliminar presentación?',
        text: "Esta presentación de precio será eliminada",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            preciosPresentacion.splice(index, 1);
            renderizarTablaPresentaciones();
            
            swal("Eliminado", "Presentación eliminada", {
                icon: "success",
                buttons: false,
                timer: 1000
            });
        }
    });
}

/**
 * Obtener el JSON de precios para enviar al servidor
 */
function obtenerJsonPrecios() {
    if (preciosPresentacion.length === 0) {
        return null;
    }
    return JSON.stringify(preciosPresentacion);
}

/**
 * Cargar precios desde JSON (para modo edición)
 */
function cargarPreciosDesdeJson(jsonString) {
    preciosPresentacion = [];
    
    if (!jsonString) {
        renderizarTablaPresentaciones();
        return;
    }
    
    try {
        const data = JSON.parse(jsonString);
        if (Array.isArray(data)) {
            preciosPresentacion = data;
        }
    } catch (e) {
        console.error('Error al parsear JSON de precios:', e);
    }
    
    renderizarTablaPresentaciones();
}

/**
 * Configurar event listeners para los botones de precios
 */
function configurarEventosPrecios() {
    const btnAgregar = document.getElementById('btnAgregarPresentacion');
    const inputPresentacion = document.getElementById('inputNuevaPresentacion');
    const inputCantidad = document.getElementById('inputNuevaCantidad');
    const inputPrecio = document.getElementById('inputNuevoPrecio');
    
    if (btnAgregar) {
        btnAgregar.addEventListener('click', agregarPresentacion);
    }
    
    // Enter en cualquier input agrega la presentación
    [inputPresentacion, inputCantidad, inputPrecio].forEach(input => {
        if (input) {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    agregarPresentacion();
                }
            });
        }
    });
}

/**
 * Limpiar todos los precios
 */
function limpiarPrecios() {
    preciosPresentacion = [];
    renderizarTablaPresentaciones();
}