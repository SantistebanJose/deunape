<?php
include("cabecera.php");
?>

<style>
    /* ── Modal XML ── */
    #modal_xml .modal-dialog { max-width: 860px; }

    .xml-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #1e1e2e;
        padding: 10px 16px;
        border-radius: 10px 10px 0 0;
    }
    .xml-toolbar span { color: #cdd6f4; font-size: .82rem; font-family: monospace; }
    .btn-copiar-xml {
        background: #313244; border: none; color: #cdd6f4;
        border-radius: 6px; padding: 4px 12px; font-size: .78rem;
        cursor: pointer; transition: background .15s;
    }
    .btn-copiar-xml:hover { background: #45475a; }
    .btn-copiar-xml.copiado { background: #a6e3a1; color: #1e1e2e; }

    #xml_contenido {
        background: #1e1e2e;
        color: #cdd6f4;
        font-family: 'Fira Code', 'Cascadia Code', monospace;
        font-size: .75rem;
        line-height: 1.6;
        padding: 16px;
        border-radius: 0 0 10px 10px;
        max-height: 520px;
        overflow-y: auto;
        white-space: pre;
        tab-size: 2;
    }

    /* Coloreado XML básico */
    .xml-tag      { color: #89b4fa; }
    .xml-attr     { color: #f38ba8; }
    .xml-value    { color: #a6e3a1; }
    .xml-text     { color: #cdd6f4; }
    .xml-comment  { color: #6c7086; font-style: italic; }
    .xml-decl     { color: #fab387; }

    /* Spinner modal */
    #xml_spinner {
        text-align: center;
        padding: 40px 0;
        color: #6c7086;
    }
</style>

<div class="container">
    <div class="page-inner">
        <div class="card text-start">
            <div class="card-body">
                <h4 class="card-title">
                    <i class="fab fa-staylinked"></i> Ventas Declaradas a SUNAT
                </h4>
                <div class="card-sub">
                    Marque <strong>en el botón verde</strong> los comprobantes que desea <strong>revisar.</strong>
                </div>

                <div class="tablita-responsive">
                    <div class="table-responsive">
                        <table id="tabla_boletas" class="dataTable display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>SERIE</th>
                                    <th>CORRELATIVO</th>
                                    <th>Fecha Emisión</th>
                                    <th>Total</th>
                                    <th>SUNAT</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (listComprobantesDeclarados($_SESSION["sucursal_id"]) as $datos): ?>
                                    <tr>
                                        <td><?php echo $datos["id"] ?></td>
                                        <td><?php echo $datos["serie"] ?></td>
                                        <td><?php echo $datos["correlativo_texto"] ?></td>
                                        <td><?php echo $datos["fecha_emision"] ?></td>
                                        <td>S/ <?php echo number_format($datos["total"], 2) ?></td>
                                        <td>
                                            <?php if (!empty($datos["mensaje_sunat"])): ?>
                                                <span class="badge bg-success" style="font-size:.72rem;">
                                                    <i class="fas fa-check-circle me-1"></i><?php echo htmlspecialchars($datos["mensaje_sunat"]) ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                <button
                                                    class="btn btn-success btn-round btn-sm"
                                                    onclick='fn_ver_xml(
                                                        <?php echo json_encode($datos["nombrexml"] ?? ""); ?>,
                                                        <?php echo (int)($_SESSION["sucursal_id"] ?? 1); ?>
                                                    )'
                                                    title="Ver XML enviado a SUNAT">
                                                    <i class="fas fa-code me-1"></i>XML
                                                </button>
                                                <button
                                                    class="btn btn-secondary btn-round btn-sm"
                                                    onclick='fn_abrir_pdf(<?php echo $datos["venta_id"] ?>)'
                                                    title="Ver ticket PDF">
                                                    <i class="fas fa-file-pdf me-1"></i>PDF
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Modal XML ── -->
<div class="modal fade" id="modal_xml" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:14px; overflow:hidden;">
            <div class="modal-header" style="background:#2a2f5b; color:white; border:none;">
                <h5 class="modal-title mb-0">
                    <i class="fas fa-code me-2"></i>
                    XML enviado a SUNAT — <span id="xml_nombre" style="font-family:monospace; font-size:.9rem;"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" style="background:#1e1e2e;">

                <!-- Spinner de carga -->
                <div id="xml_spinner">
                    <div class="spinner-border text-secondary" role="status"></div>
                    <p class="mt-2" style="color:#6c7086; font-size:.85rem;">Cargando XML...</p>
                </div>

                <!-- Visor XML -->
                <div id="xml_visor" style="display:none;">
                    <div class="xml-toolbar">
                        <span id="xml_toolbar_nombre"></span>
                        <button class="btn-copiar-xml" onclick="fn_copiar_xml()" id="btn_copiar">
                            <i class="fas fa-copy me-1"></i>Copiar
                        </button>
                    </div>
                    <pre id="xml_contenido"></pre>
                </div>

                <!-- Error -->
                <div id="xml_error" style="display:none; padding:32px; text-align:center;">
                    <i class="fas fa-exclamation-circle fa-2x text-danger mb-2"></i>
                    <p id="xml_error_msg" style="color:#f38ba8;"></p>
                </div>

            </div>
            <div class="modal-footer" style="background:#181825; border:none;">
                <small style="color:#6c7086;">
                    <i class="fas fa-info-circle me-1"></i>
                    Archivo guardado en <code style="color:#89b4fa;">sucursales/{sucursal}/xml/</code>
                </small>
                <button type="button" class="btn btn-sm btn-secondary ms-auto" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    $(".dataTable").DataTable({
        language: {
            sProcessing   : "Procesando...",
            sLengthMenu   : "Mostrar _MENU_ registros",
            sZeroRecords  : "No se encontraron resultados",
            sEmptyTable   : "Ningún dato disponible",
            sInfo         : "Registros del _START_ al _END_ de _TOTAL_",
            sInfoEmpty    : "Registros del 0 al 0 de 0",
            sInfoFiltered : "(filtrado de _MAX_ registros)",
            sSearch       : "Buscar:",
            oPaginate     : { sFirst:"Primero", sPrevious:"Anterior", sNext:"Siguiente", sLast:"Último" }
        }
    });
});

// ── Ver XML ──────────────────────────────────────────────────
let _xmlTextoActual = '';

function fn_ver_xml(nombrexml, sucursal) {
    if (!nombrexml) {
        alert('Este comprobante no tiene XML registrado.');
        return;
    }

    // Reset modal
    document.getElementById('xml_spinner').style.display = 'block';
    document.getElementById('xml_visor').style.display   = 'none';
    document.getElementById('xml_error').style.display   = 'none';
    document.getElementById('xml_nombre').textContent     = nombrexml + '.XML';
    _xmlTextoActual = '';

    $('#modal_xml').modal('show');

    fetch(`ticket.php?accion=xml&nombrexml=${encodeURIComponent(nombrexml)}&sucursal=${sucursal}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('xml_spinner').style.display = 'none';

            if (data.error) {
                document.getElementById('xml_error_msg').textContent = data.error;
                document.getElementById('xml_error').style.display   = 'block';
                return;
            }

            _xmlTextoActual = data.xml;
            document.getElementById('xml_toolbar_nombre').textContent = nombrexml + '.XML';
            document.getElementById('xml_contenido').innerHTML = colorearXML(data.xml);
            document.getElementById('xml_visor').style.display = 'block';
        })
        .catch(err => {
            document.getElementById('xml_spinner').style.display = 'none';
            document.getElementById('xml_error_msg').textContent = 'Error de conexión: ' + err.message;
            document.getElementById('xml_error').style.display   = 'block';
        });
}

// ── Copiar al portapapeles ────────────────────────────────────
function fn_copiar_xml() {
    if (!_xmlTextoActual) return;
    navigator.clipboard.writeText(_xmlTextoActual).then(() => {
        const btn = document.getElementById('btn_copiar');
        btn.classList.add('copiado');
        btn.innerHTML = '<i class="fas fa-check me-1"></i>Copiado';
        setTimeout(() => {
            btn.classList.remove('copiado');
            btn.innerHTML = '<i class="fas fa-copy me-1"></i>Copiar';
        }, 2000);
    });
}

// ── Coloreado XML básico ──────────────────────────────────────
function colorearXML(xml) {
    const esc = s => s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    return esc(xml)
        // Declaración XML
        .replace(/(&lt;\?xml[^?]*\?&gt;)/g, '<span class="xml-decl">$1</span>')
        // Comentarios
        .replace(/(&lt;!--[\s\S]*?--&gt;)/g, '<span class="xml-comment">$1</span>')
        // Tags de cierre
        .replace(/(&lt;\/[\w:]+&gt;)/g, '<span class="xml-tag">$1</span>')
        // Tags de apertura con atributos
        .replace(/(&lt;[\w:]+)((?:\s+[\w:]+="[^"]*")*)(\/?)(&gt;)/g, (m, tag, attrs, self, end) => {
            const atColored = attrs.replace(/([\w:]+)(="[^"]*")/g,
                '<span class="xml-attr">$1</span><span class="xml-value">$2</span>');
            return `<span class="xml-tag">${tag}</span>${atColored}<span class="xml-tag">${self}${end}</span>`;
        });
}

// ── Abrir PDF ─────────────────────────────────────────────────
function fn_abrir_pdf(id_venta) {
    fetch("ticket.php?accion=token&id=" + parseInt(id_venta))
        .then(r => r.json())
        .then(data => window.open(data.url, "_blank"));
}
</script>

<?php include("pie.php"); ?>