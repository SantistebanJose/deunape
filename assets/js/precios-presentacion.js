// ============================================
// SISTEMA DE PRECIOS POR PRESENTACIÓN - VERSIÓN CORREGIDA
// ============================================

let presentacionesPrecios = [];

/**
 * Cargar presentaciones disponibles desde la BD
 */
function cargarPresentacionesDisponibles() {
    console.log("📡 === CARGANDO PRESENTACIONES DISPONIBLES ===");
    console.log("Sucursal ID:", SUCURSAL_ID);
    console.log("Tipo de SUCURSAL_ID:", typeof SUCURSAL_ID);
    
    // Verificar que SUCURSAL_ID existe
    if (!SUCURSAL_ID) {
        console.error("❌ SUCURSAL_ID no definido!");
        swal("Error", "No se ha establecido una sucursal activa", {
            icon: "error",
            buttons: { confirm: { className: "btn btn-danger" } }
        });
        return;
    }
    
    return $.ajax({
        url: 'logica/clssInsertPA.php',
        type: 'POST',
        data: {
            accion: 'LISTAR_PRESENTACIONES',
            sucursal_id: SUCURSAL_ID
        },
        dataType: 'json',
        beforeSend: function() {
            console.log("📤 Enviando solicitud...");
        },
        success: function(response) {
            console.log("📥 Respuesta completa:", response);
            console.log("📥 Tipo de respuesta:", typeof response);
            
            // Intentar parsear si es string
            if (typeof response === 'string') {
                console.warn("⚠️ Respuesta es string, intentando parsear...");
                try {
                    response = JSON.parse(response);
                    console.log("✅ JSON parseado:", response);
                } catch (e) {
                    console.error("❌ Error al parsear JSON:", e);
                    console.error("Respuesta original:", response);
                    swal("Error", "Respuesta del servidor no válida", {
                        icon: "error",
                        buttons: { confirm: { className: "btn btn-danger" } }
                    });
                    return;
                }
            }
            
            if (response.estado && response.datos) {
                console.log(`✅ Se cargaron ${response.datos.length} presentaciones`);
                if (response.datos.length === 0) {
                    console.warn("⚠️ No hay presentaciones disponibles en la BD");
                    const select = $('#selectPresentacion');
                    select.empty();
                    select.append('<option value="">No hay presentaciones registradas</option>');
                } else {
                    renderizarSelectorPresentaciones(response.datos);
                }
            } else {
                console.error("❌ Error en la respuesta:", response.mensaje || "Sin mensaje de error");
                swal("Error", response.mensaje || "No se pudieron cargar las presentaciones", {
                    icon: "error",
                    buttons: { confirm: { className: "btn btn-danger" } }
                });
                // Actualizar selector con mensaje de error
                const select = $('#selectPresentacion');
                select.empty();
                select.append('<option value="">Error al cargar presentaciones</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error("❌ Error AJAX completo:");
            console.error("  - Status:", status);
            console.error("  - Error:", error);
            console.error("  - Response Text:", xhr.responseText);
            console.error("  - Status Code:", xhr.status);
            
            swal("Error", "No se pudieron cargar las presentaciones. Consulte la consola para más detalles.", {
                icon: "error",
                buttons: { confirm: { className: "btn btn-danger" } }
            });
            
            // Actualizar selector con mensaje de error
            const select = $('#selectPresentacion');
            select.empty();
            select.append('<option value="">Error de conexión</option>');
        }
    });
}

/**
 * Renderizar el selector de presentaciones
 */
function renderizarSelectorPresentaciones(presentacionesDisponibles) {
    console.log("🎨 === RENDERIZANDO SELECTOR ===");
    console.log("Presentaciones a renderizar:", presentacionesDisponibles);
    
    const select = $('#selectPresentacion');
    
    if (!select.length) {
        console.error("❌ No se encontró el elemento #selectPresentacion");
        return;
    }
    
    select.empty();
    select.append('<option value="">Seleccione una presentación</option>');
    
    if (!presentacionesDisponibles || presentacionesDisponibles.length === 0) {
        console.warn("⚠️ No hay presentaciones disponibles");
        select.append('<option value="" disabled>No hay presentaciones registradas</option>');
        return;
    }
    
    presentacionesDisponibles.forEach(function(pres) {
        const textoOpcion = `${pres.presentacion} (${pres.codigo}) - ${pres.cantidad_numero} unidades`;
        select.append(
            `<option value="${pres.id}" 
                     data-codigo="${pres.codigo}" 
                     data-presentacion="${pres.presentacion}"
                     data-cantidad="${pres.cantidad_numero}">
                ${textoOpcion}
            </option>`
        );
        console.log(`  ➕ Agregada: ${textoOpcion}`);
    });
    
    console.log("✅ Selector renderizado con", select.find('option').length - 1, "opciones");
}

/**
 * Configurar eventos de presentaciones
 * ⚠️ CRÍTICO: Esta función debe llamarse DESPUÉS de que el DOM esté listo
 */
function configurarEventosPrecios() {
    console.log("⚙️ === CONFIGURANDO EVENTOS DE PRECIOS ===");
    
    // ✅ SOLUCIÓN 1: Usar delegación de eventos para elementos dinámicos
    $(document).off('click', '#btnAgregarPresentacion').on('click', '#btnAgregarPresentacion', function() {
        console.log("🖱️ Click en Agregar Presentación");
        agregarPresentacion();
    });
    
    $(document).off('change', '#selectPresentacion').on('change', '#selectPresentacion', function() {
        const selectedOption = $(this).find('option:selected');
        const codigo = selectedOption.data('codigo') || '';
        const cantidad = selectedOption.data('cantidad') || '1.00';
        const presentacion = selectedOption.data('presentacion') || '';
        
        console.log("📋 Presentación seleccionada:", {
            id: $(this).val(),
            presentacion: presentacion,
            codigo: codigo,
            cantidad: cantidad
        });
        
        $('#inputNuevoCodigo').val(codigo);
        $('#inputNuevaCantidad').val(cantidad);
    });
    
    $(document).off('keypress', '#inputNuevoPrecio').on('keypress', '#inputNuevoPrecio', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            agregarPresentacion();
        }
    });
    
    console.log("✅ Eventos configurados con delegación");
    
    // ✅ SOLUCIÓN 2: Cargar presentaciones disponibles automáticamente
    console.log("📡 Cargando presentaciones disponibles...");
    cargarPresentacionesDisponibles();
}

/**
 * Agregar una nueva presentación de precio
 */
function agregarPresentacion() {
    console.log("➕ === AGREGANDO PRESENTACIÓN ===");
    
    const selectPresentacion = $('#selectPresentacion');
    const inputPrecio = $('#inputNuevoPrecio');
    
    const presentacionId = selectPresentacion.val();
    const selectedOption = selectPresentacion.find('option:selected');
    const presentacionTexto = selectedOption.text().trim();
    const presentacionNombre = selectedOption.data('presentacion') || presentacionTexto;
    const codigo = $('#inputNuevoCodigo').val().trim();
    const cantidad = parseFloat($('#inputNuevaCantidad').val());
    const precio = parseFloat(inputPrecio.val());
    
    console.log("Datos capturados:", {
        presentacionId,
        presentacionTexto,
        presentacionNombre,
        codigo,
        cantidad,
        precio
    });
    
    // Validaciones
    if (!presentacionId || presentacionId === "") {
        swal("Error", "Debe seleccionar una presentación", {
            icon: "warning",
            buttons: { confirm: { className: "btn btn-warning" } }
        });
        return;
    }
    
    if (isNaN(cantidad) || cantidad <= 0) {
        swal("Error", "La cantidad debe ser mayor a 0", {
            icon: "warning",
            buttons: { confirm: { className: "btn btn-warning" } }
        });
        return;
    }
    
    if (isNaN(precio) || precio < 0) {
        swal("Error", "El precio debe ser mayor o igual a 0", {
            icon: "warning",
            buttons: { confirm: { className: "btn btn-warning" } }
        });
        return;
    }
    
    // Verificar si ya existe
    const existe = presentacionesPrecios.find(p => String(p.presentacion_id) === String(presentacionId));
    if (existe) {
        swal("Error", "Esta presentación ya ha sido agregada", {
            icon: "warning",
            buttons: { confirm: { className: "btn btn-warning" } }
        });
        return;
    }
    
    // Agregar a la lista
    const nuevaPresentacion = {
        presentacion_id: String(presentacionId),
        presentacion_nombre: presentacionNombre,
        codigo: codigo,
        cantidad: cantidad,
        precio: precio
    };
    
    presentacionesPrecios.push(nuevaPresentacion);
    
    console.log("✅ Presentación agregada:", nuevaPresentacion);
    console.log("Total presentaciones:", presentacionesPrecios.length);
    console.log("Array completo:", presentacionesPrecios);
    
    // Limpiar campos
    selectPresentacion.val('');
    $('#inputNuevoCodigo').val('');
    $('#inputNuevaCantidad').val('1.00');
    inputPrecio.val('0.00');
    
    // Renderizar tabla
    renderizarTablaPresentaciones();
    actualizarContadorPresentaciones();
    
    swal("¡Agregado!", "Presentación de precio agregada correctamente", {
        icon: "success",
        buttons: false,
        timer: 1000
    });
}

/**
 * Renderizar la tabla de presentaciones
 */
function renderizarTablaPresentaciones() {
    console.log("🎨 === RENDERIZANDO TABLA ===");
    console.log("Presentaciones a mostrar:", presentacionesPrecios);
    
    const tbody = $('#tablaPresentacionesBody');
    const noMsg = $('#noPreciosMsg');
    
    // Si no hay presentaciones, mostrar mensaje
    if (presentacionesPrecios.length === 0) {
        console.log("⚠️ No hay presentaciones para mostrar");
        if (tbody.length > 0) tbody.empty();
        if (noMsg.length > 0) noMsg.show();
        return;
    }
    
    // Ocultar mensaje y renderizar tabla
    if (noMsg.length > 0) noMsg.hide();
    if (tbody.length === 0) {
        console.error("❌ No se encontró #tablaPresentacionesBody");
        return;
    }
    
    tbody.empty();
    
    presentacionesPrecios.forEach(function(pres, index) {
        const row = `
            <tr>
                <td><strong>${pres.presentacion_nombre}</strong></td>
                <td><span class="badge bg-info">${pres.codigo}</span></td>
                <td>${pres.cantidad} unidades</td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">S/.</span>
                        <input type="number" step="0.01" class="form-control" 
                               value="${pres.precio}" 
                               onchange="actualizarPrecioPresentacion(${index}, this.value)">
                    </div>
                </td>
                <td class="text-center">
                    <button class="btn btn-danger btn-sm" onclick="eliminarPresentacion(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
        console.log(`  ✅ Fila ${index + 1}:`, pres.presentacion_nombre);
    });
    
    console.log("✅ Tabla renderizada completamente");
}

/**
 * Actualizar contador de presentaciones
 */
function actualizarContadorPresentaciones() {
    const contador = $('#contadorPresentaciones');
    if (contador.length > 0) {
        contador.text(presentacionesPrecios.length);
        console.log("📊 Contador actualizado:", presentacionesPrecios.length);
    }
}

/**
 * Actualizar precio de una presentación
 */
function actualizarPrecioPresentacion(index, nuevoPrecio) {
    const precio = parseFloat(nuevoPrecio);
    if (!isNaN(precio) && precio >= 0) {
        presentacionesPrecios[index].precio = precio;
        console.log("💰 Precio actualizado:", presentacionesPrecios[index]);
    } else {
        console.warn("⚠️ Precio inválido:", nuevoPrecio);
    }
}

/**
 * Eliminar una presentación
 */
function eliminarPresentacion(index) {
    console.log("🗑️ Solicitando eliminar presentación:", presentacionesPrecios[index]);
    
    Swal.fire({
        title: '¿Eliminar presentación?',
        text: "Esta presentación se quitará de la lista",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            presentacionesPrecios.splice(index, 1);
            console.log("✅ Presentación eliminada. Total:", presentacionesPrecios.length);
            renderizarTablaPresentaciones();
            actualizarContadorPresentaciones();
            swal("¡Eliminado!", "Presentación eliminada", {
                icon: "success",
                buttons: false,
                timer: 1000
            });
        }
    });
}

/**
 * Obtener JSON de presentaciones para enviar a BD
 */
function obtenerJsonPrecios() {
    console.log("📦 === OBTENER JSON PRECIOS (MEJORADO) ===");
    console.log("Presentaciones actuales:", presentacionesPrecios);
    
    if (!Array.isArray(presentacionesPrecios) || presentacionesPrecios.length === 0) {
        console.log("⚠️ No hay presentaciones para convertir");
        return null;
    }
    
    try {
        const preciosData = presentacionesPrecios.map(function(pres) {
            return {
                unidadescompra_id: parseInt(pres.presentacion_id),
                precio: parseFloat(pres.precio)
            };
        });
        
        const jsonString = JSON.stringify(preciosData);
        console.log("✅ JSON generado:", jsonString);
        console.log("📊 Total registros:", preciosData.length);
        
        // Validar que el JSON es parseable
        try {
            JSON.parse(jsonString);
            console.log("✅ JSON válido");
        } catch (e) {
            console.error("❌ JSON inválido:", e);
            return null;
        }
        
        return jsonString;
        
    } catch (error) {
        console.error("❌ Error al generar JSON:", error);
        return null;
    }
}

// ============================================
// EXPONER FUNCIÓN DE DIAGNÓSTICO GLOBALMENTE
// ============================================
window.diagnosticarSistemaPrecios = diagnosticarSistemaPrecios;

console.log("✅ Sistema de precios mejorado cargado");
console.log("💡 Tip: Ejecuta diagnosticarSistemaPrecios() en la consola para ver el estado");

/**
 * ✅ FUNCIÓN CORREGIDA: Cargar precios de un artículo desde la BD
 * Esta función hace AJAX para obtener los precios actuales
 */
function cargarPreciosArticulo(articuloId) {
    console.log("🔍 === CARGAR PRECIOS ARTÍCULO (VERSIÓN CORREGIDA) ===");
    console.log("📌 Artículo ID:", articuloId);
    console.log("📌 Sucursal ID:", SUCURSAL_ID);
    
    // ✅ VALIDACIONES PREVIAS
    if (!articuloId) {
        console.error("❌ No hay artículo ID");
        presentacionesPrecios = [];
        renderizarTablaPresentaciones();
        actualizarContadorPresentaciones();
        return;
    }
    
    if (!SUCURSAL_ID) {
        console.error("❌ SUCURSAL_ID no definido");
        swal("Error", "No se ha establecido una sucursal activa", {
            icon: "error",
            buttons: { confirm: { className: "btn btn-danger" } }
        });
        return;
    }
    
    // ✅ LIMPIAR ARRAY ANTES DE CARGAR
    presentacionesPrecios = [];
    
    console.log("📡 Iniciando petición AJAX...");
    
    return $.ajax({
        url: 'logica/clssInsertPA.php',
        type: 'POST',
        data: {
            accion: 'OBTENER_PRECIOS_ARTICULO',
            articulo_id: articuloId,
            sucursal_id: SUCURSAL_ID
        },
        dataType: 'json', // ✅ Forzar tipo de respuesta
        beforeSend: function() {
            console.log("⏳ Enviando petición...");
            // Opcional: Mostrar loading
        },
        success: function(response) {
            console.log("📥 === RESPUESTA RECIBIDA ===");
            console.log("Response completo:", response);
            
            // ✅ PARSEAR SI ES NECESARIO
            let result = response;
            if (typeof response === 'string') {
                console.log("⚠️ Response es string, parseando...");
                try {
                    result = JSON.parse(response);
                } catch (e) {
                    console.error("❌ Error al parsear:", e);
                    console.error("String recibido:", response);
                    
                    swal("Error", "Respuesta del servidor inválida", {
                        icon: "error",
                        buttons: { confirm: { className: "btn btn-danger" } }
                    });
                    
                    presentacionesPrecios = [];
                    renderizarTablaPresentaciones();
                    actualizarContadorPresentaciones();
                    return;
                }
            }
            
            console.log("📊 Estado:", result.estado);
            console.log("📊 Total registros:", result.total);
            console.log("📊 Datos:", result.datos);
            
            // ✅ VERIFICAR ESTADO Y DATOS
            if (result.estado && result.datos && Array.isArray(result.datos)) {
                
                if (result.datos.length === 0) {
                    console.log("⚠️ No hay precios guardados para este artículo");
                    presentacionesPrecios = [];
                } else {
                    console.log(`✅ Cargando ${result.datos.length} registros...`);
                    
                    // ✅ MAPEAR DATOS CON VALIDACIONES
                    presentacionesPrecios = result.datos
                        .filter(function(item) {
                            // Filtrar registros inválidos
                            if (!item.unidadescompra_id) {
                                console.warn("⚠️ Registro sin unidadescompra_id:", item);
                                return false;
                            }
                            return true;
                        })
                        .map(function(item) {
                            // Construir nombre completo
                            const nombreCompleto = item.presentacion 
                                ? `${item.presentacion}${item.codigo ? ' (' + item.codigo + ')' : ''}${item.cantidad_numero ? ' - ' + item.cantidad_numero + ' unidades' : ''}`
                                : 'Sin nombre';
                            
                            console.log(`  ✅ Cargado: ${nombreCompleto} - S/. ${item.precio}`);
                            
                            return {
                                presentacion_id: String(item.unidadescompra_id),
                                presentacion_nombre: nombreCompleto,
                                codigo: item.codigo || '',
                                cantidad: parseFloat(item.cantidad_numero) || 1.00,
                                precio: parseFloat(item.precio) || 0
                            };
                        });
                    
                    console.log("✅ Presentaciones mapeadas:", presentacionesPrecios);
                }
                
                // ✅ RENDERIZAR SIEMPRE
                renderizarTablaPresentaciones();
                actualizarContadorPresentaciones();
                
                console.log("✅ Carga completada exitosamente");
                
            } else {
                console.error("❌ Respuesta inválida o sin datos");
                console.error("Estado:", result.estado);
                console.error("Mensaje:", result.mensaje);
                
                presentacionesPrecios = [];
                renderizarTablaPresentaciones();
                actualizarContadorPresentaciones();
            }
        },
        error: function(xhr, status, error) {
            console.error("❌ === ERROR AJAX ===");
            console.error("Status:", status);
            console.error("Error:", error);
            console.error("Status Code:", xhr.status);
            console.error("Response Text:", xhr.responseText);
            
            swal("Error", "No se pudieron cargar los precios. Revise la consola para más detalles.", {
                icon: "error",
                buttons: { confirm: { className: "btn btn-danger" } }
            });
            
            presentacionesPrecios = [];
            renderizarTablaPresentaciones();
            actualizarContadorPresentaciones();
        },
        complete: function() {
            console.log("🏁 Petición completada");
        }
    });
}
/**
 * ⚠️ MANTENER POR COMPATIBILIDAD - Redirige a cargarPreciosArticulo
 * Esta función se mantiene para no romper código existente
 */
function cargarPreciosDesdeJson(preciosJson, articuloId) {
    console.log("⚠️ cargarPreciosDesdeJson llamada (redirigiendo a cargarPreciosArticulo)");
    console.log("JSON recibido (se ignorará):", preciosJson);
    console.log("Artículo ID:", articuloId);
    
    // Redirigir a la función correcta
    cargarPreciosArticulo(articuloId);
}

/**
 * Limpiar todas las presentaciones
 */
function limpiarPrecios() {
    console.log("🧹 === LIMPIANDO PRECIOS ===");
    presentacionesPrecios = [];
    renderizarTablaPresentaciones();
    actualizarContadorPresentaciones();
    console.log("✅ Precios limpiados");
}

// ============================================
// FUNCIÓN DE INICIALIZACIÓN
// ============================================
$(document).ready(function() {
    console.log("🚀 === SISTEMA DE PRECIOS INICIALIZADO ===");
    console.log("Sucursal ID global:", SUCURSAL_ID);
});