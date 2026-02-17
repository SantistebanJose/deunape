<?php
include("cabecera.php");

// Verificar que el usuario esté autenticado y tenga una sucursal asignada
if (!isset($_SESSION['sucursal_id']) || empty($_SESSION['sucursal_id'])) {
    echo '<div class="alert alert-danger">Error: No se pudo determinar la sucursal del usuario.</div>';
    exit;
}

$sucursal_id_usuario = $_SESSION['sucursal_id'];
?>

<style>
    body {
        background: #f4f6f9;
        min-height: 100vh;
    }

    .container-main {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }

    .header-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        margin-bottom: 25px;
        border-left: 5px solid #6861ce;
    }

    .header-card h1 {
        color: #2c3e50;
        font-weight: bold;
        margin-bottom: 10px;
        font-size: 28px;
    }

    .header-card p {
        color: #6c757d;
        margin-bottom: 0;
        font-size: 15px;
    }

    .sucursal-badge {
        display: inline-block;
        background: linear-gradient(135deg, #6861ce 0%, #5651b8 100%);
        color: white;
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        margin-top: 10px;
    }

    .control-panel {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        margin-bottom: 25px;
    }

    .preview-container {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        min-height: 500px;
    }

    .etiqueta-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-top: 20px;
    }

    .etiqueta-item {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 20px;
        background: white;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .etiqueta-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(104, 97, 206, 0.15);
        border-color: #6861ce;
    }

    .etiqueta-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: #6861ce;
    }

    .etiqueta-codigo {
        font-size: 13px;
        font-weight: 700;
        color: #6861ce;
        margin-bottom: 10px;
        font-family: 'Courier New', monospace;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .etiqueta-nombre {
        font-size: 15px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 15px;
        line-height: 1.5;
        min-height: 45px;
    }

    .etiqueta-precio {
        font-size: 32px;
        font-weight: bold;
        color: #28a745;
        text-align: center;
        background: #f8f9fa;
        padding: 12px;
        border-radius: 8px;
        border: 2px solid #e9ecef;
    }

    .etiqueta-precio::before {
        content: 'S/ ';
        font-size: 20px;
        color: #28a745;
    }

    .btn-generar {
        background: #6861ce;
        border: none;
        padding: 12px 35px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 8px;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(104, 97, 206, 0.3);
    }

    .btn-generar:hover {
        background: #5651b8;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(104, 97, 206, 0.4);
    }

    .btn-volver {
        background: #6c757d;
        border: none;
        padding: 12px 35px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 8px;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(108, 117, 125, 0.3);
        margin-right: 15px;
    }

    .btn-volver:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.4);
    }

    .control-group {
        background: #f8f9fa;
        padding: 18px;
        border-radius: 10px;
        margin-bottom: 15px;
        border: 1px solid #e9ecef;
    }

    .control-group label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
        font-size: 14px;
        display: block;
    }

    .form-select, .form-control {
        border-radius: 8px;
        border: 2px solid #e0e0e0;
        padding: 10px 15px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-select:focus, .form-control:focus {
        border-color: #6861ce;
        box-shadow: 0 0 0 0.2rem rgba(104, 97, 206, 0.15);
        outline: none;
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 80px;
        margin-bottom: 20px;
        color: #d1d5db;
    }

    .stats-card {
        background: linear-gradient(135deg, #6861ce 0%, #5651b8 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        margin-bottom: 15px;
        box-shadow: 0 2px 8px rgba(104, 97, 206, 0.3);
    }

    .stats-card h3 {
        font-size: 40px;
        margin: 0;
        font-weight: bold;
    }

    .stats-card p {
        margin: 5px 0 0 0;
        opacity: 0.95;
        font-size: 14px;
        font-weight: 500;
    }

    .section-title {
        color: #2c3e50;
        font-weight: 700;
        margin-bottom: 20px;
        font-size: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title i {
        color: #6861ce;
    }

    /* Estilos para impresión PDF */
    @media print {
        body {
            background: white !important;
            padding: 0 !important;
        }
        
        .control-panel, .header-card, .btn-generar, .btn-volver {
            display: none !important;
        }
        
        .preview-container {
            box-shadow: none !important;
            padding: 10px !important;
        }
        
        .etiqueta-grid {
            gap: 10px !important;
        }

        .section-title {
            display: none !important;
        }
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    @media (max-width: 768px) {
        .etiqueta-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .stats-card h3 {
            font-size: 32px;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-volver {
            margin-right: 0;
            margin-bottom: 10px;
        }
    }
</style>

<div class="container-main">
    <!-- Header -->
    <div class="header-card">
        <h1><i class="fas fa-tags"></i> Generador de Etiquetas de Precios</h1>
        <p>Crea etiquetas profesionales para tus artículos y descárgalas en PDF</p>
        <?php
        // Mostrar información de la sucursal
        $nombreSucursal = isset($_SESSION['sucursal_nombre']) ? $_SESSION['sucursal_nombre'] : 'Sucursal #' . $sucursal_id_usuario;
        echo '<div class="sucursal-badge"><i class="fas fa-store"></i> ' . htmlspecialchars($nombreSucursal) . '</div>';
        ?>
    </div>

    <!-- Panel de Control -->
    <div class="control-panel">
        <h4 class="section-title">
            <i class="fas fa-sliders-h"></i> Configuración de Etiquetas
        </h4>
        
        <div class="row">
            <div class="col-md-3">
                <div class="stats-card">
                    <h3 id="totalArticulos">0</h3>
                    <p>Artículos para Etiquetar</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="control-group">
                    <label><i class="fas fa-th"></i> Columnas por página</label>
                    <select class="form-select" id="columnasSelect">
                        <option value="2">2 columnas</option>
                        <option value="3" selected>3 columnas</option>
                        <option value="4">4 columnas</option>
                        <option value="5">5 columnas</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="control-group">
                    <label><i class="fas fa-search"></i> Buscar artículo</label>
                    <input type="text" class="form-control" id="buscarArticulo" placeholder="Nombre o código...">
                </div>
            </div>
            <div class="col-md-3">
                <div class="control-group">
                    <label><i class="fas fa-filter"></i> Filtrar por categoría</label>
                    <select class="form-select" id="filtroCategoria">
                        <option value="">Todas las categorías</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="action-buttons mt-4">
            <button class="btn btn-volver" onclick="window.location.href='articulos.php'">
                <i class="fas fa-arrow-left"></i> Volver a Artículos
            </button>
            <button class="btn btn-generar" id="btnGenerarPDF">
                <i class="fas fa-file-pdf"></i> Descargar Etiquetas en PDF
            </button>
        </div>
    </div>

    <!-- Vista Previa -->
    <div class="preview-container" id="previewContainer">
        <h4 class="section-title">
            <i class="fas fa-eye"></i> Vista Previa de Etiquetas
        </h4>
        
        <div class="empty-state" id="emptyState" style="display: none;">
            <i class="fas fa-box-open"></i>
            <h4>No hay artículos para mostrar</h4>
            <p>No se encontraron artículos con los filtros aplicados en tu sucursal</p>
        </div>

        <div class="etiqueta-grid" id="etiquetasGrid"></div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    // Datos filtrados por sucursal desde la base de datos
    let articulosData = <?php 
        // Obtener artículos filtrados por sucursal
        $articulos = listarArticuloSinview();
        $datosParaJS = [];
        
        foreach ($articulos as $art) {
            // Filtrar por sucursal_id y precio de venta mayor a 0
            if (isset($art['sucursal_id']) && 
                $art['sucursal_id'] == $sucursal_id_usuario && 
                floatval($art['precio_venta']) > 0) {
                
                $datosParaJS[] = [
                    'codigo' => 'ART-' . str_pad($art['articulo_id'], 4, '0', STR_PAD_LEFT),
                    'nombre' => $art['articulo'],
                    'precio' => floatval($art['precio_venta']),
                    'categoria' => $art['categoria'] ?? 'Sin categoría',
                    'sucursal_id' => $art['sucursal_id']
                ];
            }
        }
        echo json_encode($datosParaJS);
    ?>;

    let articulosFiltrados = [...articulosData];

    // Inicializar la aplicación
    document.addEventListener('DOMContentLoaded', function() {
        console.log(`Artículos cargados para la sucursal: ${articulosData.length}`);
        cargarCategorias();
        renderizarEtiquetas();
        configurarEventos();
        actualizarEstadisticas();
    });

    function cargarCategorias() {
        const categorias = [...new Set(articulosData.map(a => a.categoria))];
        const select = document.getElementById('filtroCategoria');
        
        categorias.sort().forEach(cat => {
            const option = document.createElement('option');
            option.value = cat;
            option.textContent = cat;
            select.appendChild(option);
        });
    }

    function renderizarEtiquetas() {
        const grid = document.getElementById('etiquetasGrid');
        const emptyState = document.getElementById('emptyState');
        const columnas = document.getElementById('columnasSelect').value;
        
        grid.innerHTML = '';
        
        if (articulosFiltrados.length === 0) {
            emptyState.style.display = 'block';
            grid.style.display = 'none';
            return;
        }
        
        emptyState.style.display = 'none';
        grid.style.display = 'grid';
        grid.style.gridTemplateColumns = `repeat(${columnas}, 1fr)`;
        
        articulosFiltrados.forEach(articulo => {
            const etiqueta = document.createElement('div');
            etiqueta.className = 'etiqueta-item';
            etiqueta.innerHTML = `
                <div class="etiqueta-codigo">${articulo.codigo}</div>
                <div class="etiqueta-nombre">${articulo.nombre}</div>
                <div class="etiqueta-precio">${articulo.precio.toFixed(2)}</div>
            `;
            grid.appendChild(etiqueta);
        });
    }

    function configurarEventos() {
        // Cambio de columnas
        document.getElementById('columnasSelect').addEventListener('change', function() {
            renderizarEtiquetas();
        });

        // Búsqueda
        document.getElementById('buscarArticulo').addEventListener('input', function(e) {
            const termino = e.target.value.toLowerCase();
            aplicarFiltros(termino, document.getElementById('filtroCategoria').value);
        });

        // Filtro de categoría
        document.getElementById('filtroCategoria').addEventListener('change', function(e) {
            const categoria = e.target.value;
            aplicarFiltros(document.getElementById('buscarArticulo').value.toLowerCase(), categoria);
        });

        // Generar PDF
        document.getElementById('btnGenerarPDF').addEventListener('click', generarPDF);
    }

    function aplicarFiltros(termino, categoria) {
        articulosFiltrados = articulosData.filter(articulo => {
            const coincideBusqueda = termino === '' || 
                articulo.nombre.toLowerCase().includes(termino) ||
                articulo.codigo.toLowerCase().includes(termino);
            
            const coincideCategoria = categoria === '' || articulo.categoria === categoria;
            
            return coincideBusqueda && coincideCategoria;
        });
        
        renderizarEtiquetas();
        actualizarEstadisticas();
    }

    function actualizarEstadisticas() {
        document.getElementById('totalArticulos').textContent = articulosFiltrados.length;
    }

    function generarPDF() {
        if (articulosFiltrados.length === 0) {
            alert('No hay etiquetas para generar. Por favor, verifica los filtros.');
            return;
        }

        const btn = document.getElementById('btnGenerarPDF');
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando PDF...';
        btn.disabled = true;

        try {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');
            
            const columnas = parseInt(document.getElementById('columnasSelect').value);
            const margen = 10;
            const pageWidth = 210; // A4 width in mm
            const pageHeight = 297; // A4 height in mm
            const contentWidth = pageWidth - (margen * 2);
            const gapHorizontal = 5; // Espacio horizontal entre etiquetas
            const gapVertical = 5; // Espacio vertical entre etiquetas
            const etiquetaWidth = (contentWidth - ((columnas - 1) * gapHorizontal)) / columnas;
            const etiquetaHeight = 35; // Altura de cada etiqueta en mm
            
            // Calcular cuántas filas completas caben en una página
            const espacioDisponible = pageHeight - (margen * 2);
            const filasPorPagina = Math.floor(espacioDisponible / (etiquetaHeight + gapVertical));
            const etiquetasPorPagina = filasPorPagina * columnas;
            
            console.log(`Configuración: ${columnas} columnas, ${filasPorPagina} filas por página, ${etiquetasPorPagina} etiquetas por página`);
            
            let paginaActual = 0;
            
            // Procesar todas las etiquetas
            articulosFiltrados.forEach((articulo, i) => {
                // Si necesitamos una nueva página
                if (i > 0 && i % etiquetasPorPagina === 0) {
                    pdf.addPage();
                    paginaActual++;
                    console.log(`Nueva página agregada: ${paginaActual + 1}`);
                }
                
                // Calcular posición dentro de la página actual
                const posEnPagina = i % etiquetasPorPagina;
                const fila = Math.floor(posEnPagina / columnas);
                const columna = posEnPagina % columnas;
                
                // Calcular coordenadas X e Y
                const xPosition = margen + (columna * (etiquetaWidth + gapHorizontal));
                const yPosition = margen + (fila * (etiquetaHeight + gapVertical));
                
                // Dibujar borde de la etiqueta
                pdf.setDrawColor(104, 97, 206);
                pdf.setLineWidth(0.5);
                pdf.rect(xPosition, yPosition, etiquetaWidth, etiquetaHeight);
                
                // Línea superior morada (decorativa)
                pdf.setFillColor(104, 97, 206);
                pdf.rect(xPosition, yPosition, etiquetaWidth, 2, 'F');
                
                // Código del artículo
                pdf.setFontSize(9);
                pdf.setTextColor(104, 97, 206);
                pdf.setFont('courier', 'bold');
                pdf.text(articulo.codigo, xPosition + etiquetaWidth / 2, yPosition + 7, { align: 'center' });
                
                // Nombre del artículo (con manejo de texto largo)
                pdf.setFontSize(8);
                pdf.setTextColor(44, 62, 80);
                pdf.setFont('helvetica', 'normal');
                const nombreLineas = pdf.splitTextToSize(articulo.nombre, etiquetaWidth - 6);
                const maxLineas = 3; // Máximo 3 líneas para el nombre
                const lineasAMostrar = nombreLineas.slice(0, maxLineas);
                
                lineasAMostrar.forEach((linea, idx) => {
                    pdf.text(linea, xPosition + etiquetaWidth / 2, yPosition + 13 + (idx * 3.5), { align: 'center' });
                });
                
                // Precio
                pdf.setFontSize(16);
                pdf.setTextColor(40, 167, 69);
                pdf.setFont('helvetica', 'bold');
                const precioTexto = 'S/ ' + articulo.precio.toFixed(2);
                pdf.text(precioTexto, xPosition + etiquetaWidth / 2, yPosition + etiquetaHeight - 5, { align: 'center' });
            });

            // Guardar el PDF
            const fecha = new Date().toISOString().split('T')[0];
            const nombreArchivo = `etiquetas-precios-sucursal-${fecha}.pdf`;
            pdf.save(nombreArchivo);

            // Mensaje de éxito
            btn.innerHTML = '<i class="fas fa-check"></i> ¡PDF Generado!';
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 2000);

        } catch (error) {
            console.error('Error al generar PDF:', error);
            
            btn.innerHTML = '<i class="fas fa-times"></i> Error al generar';
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 2000);
            
            alert('Hubo un error al generar el PDF: ' + error.message);
        }
    }
</script>

<?php
include("pie.php");
?>