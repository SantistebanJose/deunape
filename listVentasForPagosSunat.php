<?php
include("cabecera.php");
?>

<style>
    :root {
        --primary: #2a2f5b;
        --accent: #667eea;
        --success: #11998e;
        --danger: #dc3545;
        --border-soft: #e3e6f5;
        --bg-card: #f8f9ff;
        --shadow-card: 0 4px 20px rgba(42,47,91,.10);
    }

    .sunat-header {
        background: #0033A0;
        border-radius: 18px;
        color: white;
        padding: 26px 32px 20px;
        margin-bottom: 28px;
        box-shadow: 0 8px 30px rgba(0,51,160,.25);
        position: relative;
        overflow: hidden;
    }
    .sunat-header::before {
        content: "📋";
        position: absolute;
        right: 28px; top: 16px;
        font-size: 3.2rem;
        opacity: .9;
    }
    .sunat-header h3 { font-weight: 800; margin-bottom: 4px; }
    .sunat-header p  { opacity: .85; margin: 0; font-size: .95rem; }

    .card-sunat {
        border: none;
        border-radius: 16px;
        box-shadow: var(--shadow-card);
    }

    /* Tabla */
    #tabla_boletas thead tr {
        background: var(--primary);
        color: white;
    }
    #tabla_boletas thead th {
        padding: 11px 14px;
        font-size: .82rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
        border: none;
    }
    #tabla_boletas thead th:first-child { border-radius: 8px 0 0 8px; }
    #tabla_boletas thead th:last-child  { border-radius: 0 8px 8px 0; }
    #tabla_boletas tbody tr {
        border-bottom: 1px solid #f0f3ff;
        transition: background .15s;
    }
    #tabla_boletas tbody tr:hover { background: #f8f9ff; }
    #tabla_boletas tbody td {
        padding: 10px 14px;
        font-size: .88rem;
        vertical-align: middle;
    }

    /* Badges tipo comprobante */
    .badge-boleta  { background: #dbeafe; color: #1d4ed8; border-radius: 20px; padding: 3px 12px; font-weight: 700; font-size: .75rem; }
    .badge-factura { background: #d1fae5; color: #065f46; border-radius: 20px; padding: 3px 12px; font-weight: 700; font-size: .75rem; }

    /* Estado envío */
    .badge-pendiente { background: #fef3c7; color: #92400e; border-radius: 20px; padding: 3px 12px; font-weight: 700; font-size: .75rem; }
    .badge-enviado   { background: #d1fae5; color: #065f46; border-radius: 20px; padding: 3px 12px; font-weight: 700; font-size: .75rem; }
    .badge-error     { background: #fee2e2; color: #991b1b; border-radius: 20px; padding: 3px 12px; font-weight: 700; font-size: .75rem; }

    /* Botón enviar */
    .btn-enviar-sunat {
        background: #0033A0;
        border: none;
        color: white;
        border-radius: 10px;
        padding: 6px 16px;
        font-size: .82rem;
        font-weight: 700;
        cursor: pointer;
        transition: all .2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-enviar-sunat:hover {
        background: #002080;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,51,160,.35);
        color: white;
    }
    .btn-enviar-sunat:disabled {
        background: #9ca3af;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    .btn-enviar-sunat.enviado {
        background: var(--success);
        cursor: default;
    }

    /* Spinner inline */
    .spinner-sm {
        width: 14px; height: 14px;
        border: 2px solid rgba(255,255,255,.4);
        border-top-color: white;
        border-radius: 50%;
        animation: spin .7s linear infinite;
        display: inline-block;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Respuesta SUNAT inline */
    .resp-sunat {
        font-size: .75rem;
        margin-top: 4px;
        color: var(--success);
        font-weight: 600;
    }
    .resp-sunat.error { color: var(--danger); }
</style>

<div class="container">
    <div class="page-inner">

        <!-- HEADER -->
        <div class="sunat-header">
            <h3><i class="fas fa-paper-plane me-2"></i>Declarar Comprobantes a SUNAT</h3>
            <p>Envía boletas y facturas al sistema de SUNAT. Cada envío genera y firma el XML automáticamente.</p>
        </div>

        <div class="card card-sunat">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-0" style="color:var(--primary);">
                            <i class="fas fa-list-ul me-2" style="color:var(--accent);"></i>
                            Ventas pendientes de declarar
                        </h5>
                        <small class="text-muted">Presiona el botón verde para enviar cada comprobante individualmente.</small>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="tabla_boletas" class="table table-hover table-sm mb-0">
                        <thead>
                            <tr>
                                <th>ID Venta</th>
                                <th>Tipo</th>
                                <th>N° Documento</th>
                                <th>Cliente</th>
                                <th>Monto</th>
                                <th>Fecha</th>
                                <th style="text-align:center;">Enviar a SUNAT</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach (listarVentasPagadasParaComprobantes($_SESSION["sucursal_id"]) as $datos):
                            $emisor        = fnListarEmisor($_SESSION["sucursal_id"])[0];
                            
                            #$emisorDebug = fnListarEmisor($_SESSION["sucursal_id"])[0];
                            #echo '<pre>';
                            #echo 'sucursal: ' . $emisorDebug['sucursal'] . "\n";
                            #echo 'certificado: ' . $emisorDebug['certificado'] . "\n";
                            #echo 'direccion_firma_digital: ' . $emisorDebug['direccion_firma_digital'] . "\n";
                            #echo '</pre>';
                            $tipo_comp     = strtoupper($datos['tipo_comprobante'] ?? 'BOLETA');
                            $tipo_ref      = $tipo_comp === 'BOLETA' ? '03' : '01';

                            // Payload completo para el JS
                            $payload = json_encode([
                                'datos_query' => $datos,
                                'js_detalle'  => $datos['js_detalle_venta'],
                                'emisor'      => $emisor,
                            ], JSON_HEX_APOS | JSON_HEX_QUOT);
                        ?>
                            <tr id="fila-<?php echo $datos['venta_id'] ?>">
                                <td><strong>#<?php echo $datos['venta_id'] ?></strong></td>
                                <td>
                                    <?php if($tipo_comp === 'BOLETA'): ?>
                                        <span class="badge-boleta">Boleta</span>
                                    <?php else: ?>
                                        <span class="badge-factura">Factura</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $datos['ca_cliente_numero_documento_sunat'] ?></td>
                                <td><?php echo $datos['ca_cliente_cliente_sunat'] ?></td>
                                <td><strong>S/ <?php echo number_format($datos['monto_venta_final'], 2) ?></strong></td>
                                <td><?php echo date('d/m/Y', strtotime($datos['fecha'])) ?></td>
                                <td style="text-align:center;">
                                    <div id="accion-<?php echo $datos['venta_id'] ?>">
                                        <button
                                            class="btn-enviar-sunat"
                                            onclick='fn_enviar_sunat_por_fila(<?php echo $payload ?>, this)'>
                                            <i class="fas fa-paper-plane"></i> Enviar
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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function fn_enviar_sunat_por_fila(jsDatos, btnEl) {

    var jsDatosEmisor    = jsDatos["emisor"];
    var jsonDetalleArray = JSON.parse(jsDatos["js_detalle"]);
    var datos_query      = jsDatos["datos_query"];

    // ── Calcular totales con descuento ────────────────────────
    var descuentoTotal   = parseFloat(datos_query["descuento"] || 0);
    var operaciones_gravadas = 0;
    var igv = 0;

    let valorTotalVentas = 0;
    jsonDetalleArray.forEach(item => {
        valorTotalVentas += parseFloat(item["pu_sin_igv"]) * parseFloat(item["cantidad_sunat"]);
    });

    var js_detalles = [];
    jsonDetalleArray.forEach((item, index) => {

        let descuentoPorArticulo = valorTotalVentas > 0
            ? (descuentoTotal * (parseFloat(item["pu_sin_igv"]) * item["cantidad_sunat"])) / valorTotalVentas
            : 0;

        let valorUnitarioConDescuento = parseFloat(item["pu_sin_igv"] / item["cantidad_sunat"]) - descuentoPorArticulo;

        js_detalles.push({
            "item"           : index + 1,
            "cantidad"       : item["cantidad_sunat"],
            "unidad"         : "NIU",
            "nombre"         : item["descripcion_articulo"],
            "valor_unitario" : parseFloat(valorUnitarioConDescuento).toFixed(6),
            "precio_lista"   : parseFloat(valorUnitarioConDescuento * 1.18).toFixed(2),
            "pu_con_igv"     : parseFloat(valorUnitarioConDescuento * 1.18).toFixed(2),
            "pu_sin_igv"     : parseFloat(valorUnitarioConDescuento).toFixed(6),
            "total_impuestos": parseFloat((valorUnitarioConDescuento * 0.18) * item["cantidad_sunat"]).toFixed(2),
            "igv"            : parseFloat(valorUnitarioConDescuento * 0.18).toFixed(2),
            "valor_total"    : parseFloat(valorUnitarioConDescuento * item["cantidad_sunat"]).toFixed(2),
            "tipo_impuesto"  : "IGV"
        });

        operaciones_gravadas += valorUnitarioConDescuento * item["cantidad_sunat"];
        igv += parseFloat(item["IGV"] || 0);
    });

    // ── Tipo de comprobante ───────────────────────────────────
    const tipo_comprobante_ref = datos_query["tipo_comprobante"] === "BOLETA" ? "03" : "01";

    // ── Armar cliente ─────────────────────────────────────────
    const numDoc = datos_query["ca_cliente_numero_documento_sunat"] || '';
    const datos_cliente = {
        "tipo_documento"     : datos_query["ca_cliente_tipo_documento_sunat"] || (numDoc.length === 11 ? '6' : '1'),
        "numero_doc_cliente" : numDoc,
        "cliente"            : datos_query["ca_cliente_cliente_sunat"] || 'CLIENTE VARIOS',
        "direccion"          : datos_query["ca_cliente_direccion_sunat"] || ''
    };

    // ── Armar cabecera ────────────────────────────────────────
    const datos_cabecera = {
        "venta_id"                : datos_query["venta_id"],
        "tipo_operacion"          : "0101",
        "tipo_comprobante"        : tipo_comprobante_ref,
        "moneda"                  : "PEN",
        "serie"                   : datos_query["serie"],
        "forma_pago"              : "Contado",
        "total_op_gravadas"       : parseFloat(operaciones_gravadas).toFixed(2),
        "igv"                     : parseFloat(igv).toFixed(2),
        "icbper"                  : 0,
        "total_op_exoneradas"     : 0.0,
        "total_op_inafectas"      : 0.0,
        "total_antes_impuestos"   : parseFloat(operaciones_gravadas).toFixed(2),
        "total_impuestos"         : parseFloat(igv).toFixed(2),
        "total_despues_impuestos" : (parseFloat(operaciones_gravadas) + parseFloat(igv)).toFixed(2),
        "total_a_pagar"           : (parseFloat(operaciones_gravadas) + parseFloat(igv)).toFixed(2),
        "tipo_comp_ref"           : "03",
        "serie_correletaivo_ref"  : datos_query["serie_correltavio_referencial"] || '',
        "fecha_emision"           : datos_query["fecha"],
        "fecha_vencimiento"       : datos_query["fecha"],
        "hora_emision"            : datos_query["hora"] || '00:00:00',
        "descuento"               : parseFloat(datos_query["descuento"] || 0).toFixed(2)
    };

    var jsDatosEnvio = {
        "emisor"  : jsDatosEmisor,
        "cliente" : datos_cliente,
        "cabecera": datos_cabecera,
        "detalles": js_detalles
    };

    // ── UI: spinner mientras envía ────────────────────────────
    const ventaId   = datos_query["venta_id"];
    const contenedor = document.getElementById('accion-' + ventaId);

    btnEl.disabled = true;
    btnEl.innerHTML = '<span class="spinner-sm"></span> Enviando...';

    // ── AJAX ──────────────────────────────────────────────────
    $.ajax({
        url  : 'logica/clssComprobante.php',
        type : 'POST',
        data : {
            accion        : 'REGISTROCOMPROBANTESBD',
            jsComprobantes: JSON.stringify(jsDatosEnvio)
        },
        success: function(response) {
            let res;
            try { res = JSON.parse(response); } catch(e) { res = { estado: false, mensaje: response }; }

            if (res.estado) {
                // ── Éxito ─────────────────────────────────────
                contenedor.innerHTML = `
                    <button class="btn-enviar-sunat enviado" disabled>
                        <i class="fas fa-check-circle"></i> Enviado
                    </button>
                    <div class="resp-sunat">${res.mensaje || 'Aceptado por SUNAT'}</div>`;

                // Marcar fila visualmente
                const fila = document.getElementById('fila-' + ventaId);
                if (fila) {
                    fila.style.background = '#f0fff8';
                    fila.style.transition = 'background .4s';
                }

                Swal.fire({
                    title            : '¡Enviado!',
                    html             : `<b>${tipo_comprobante_ref === '03' ? 'Boleta' : 'Factura'}</b> declarada correctamente.<br><small class="text-muted">${res.mensaje || ''}</small>`,
                    icon             : 'success',
                    timer            : 2500,
                    showConfirmButton: false
                });

            } else {
                // ── Error SUNAT ───────────────────────────────
                contenedor.innerHTML = `
                    <button class="btn-enviar-sunat" onclick='fn_enviar_sunat_por_fila(${JSON.stringify(jsDatos).replace(/'/g,"&#39;")}, this)'>
                        <i class="fas fa-redo"></i> Reintentar
                    </button>
                    <div class="resp-sunat error">${res.mensaje || 'Error al enviar'}</div>`;

                Swal.fire({
                    title: 'Error SUNAT',
                    text : res.mensaje || 'No se pudo enviar el comprobante',
                    icon : 'error'
                });
            }
        },
        error: function(xhr) {
            // ── Error de red ──────────────────────────────────
            btnEl.disabled = false;
            btnEl.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar';

            Swal.fire({
                title: 'Error de conexión',
                text : 'No se pudo conectar con el servidor. Inténtalo de nuevo.',
                icon : 'error'
            });

            console.error('Error:', xhr.responseText);
        }
    });
}
</script>

<?php include("pie.php"); ?>