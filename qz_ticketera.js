/**
 * qz_ticketera.js
 * Integración de QZ Tray para imprimir el ticket PDF directo en la ticketera.
 *
 * DEPENDENCIAS — agregar en tu cabecera.php (o donde cargás los scripts):
 *   <script src="https://cdn.jsdelivr.net/npm/qz-tray@2.2.4/qz-tray.js"></script>
 *   <script src="https://cdn.jsdelivr.net/npm/js-sha256@0.9.0/src/sha256.js"></script>
 *   <script src="https://cdn.jsdelivr.net/npm/jsrsasign@10.8.6/lib/jsrsasign-all-min.js"></script>
 *
 * CÓMO FUNCIONA:
 *   1. Al cargar la página intenta conectar a QZ Tray (WebSocket ws://localhost:8181)
 *   2. Lee la config de la ticketera desde tu BD (config_ticketera.php)
 *   3. Al hacer clic en "Imprimir en Ticketera":
 *      a. Obtiene la URL del PDF de ticket.php
 *      b. Le dice a QZ Tray que imprima ese PDF en la IP:Puerto configurada
 *      c. QZ Tray lo manda por TCP a la ticketera
 */

// ── Configuración ──────────────────────────────────────────────────────────────
const QZ_CONFIG = {
    host:           'localhost',
    port:           { secure: [8181, 8282, 8383, 8484], insecure: [8182, 8283, 8384, 8485] },
    usingSecure:    false,   // false para HTTP (sin certificado). true si tenés HTTPS en QZ.
    keepAlive:      20,
    retries:        3,
    delay:          1,
};

// ── Estado global ──────────────────────────────────────────────────────────────
let qzConectado  = false;
let qzConectando = false;
let configCache  = {};       // cache de config por sucursal { sucursal_id: {...} }

// ═══════════════════════════════════════════════════════════════════════════════
// 1. CONEXIÓN A QZ TRAY
// ═══════════════════════════════════════════════════════════════════════════════

/**
 * Conecta a QZ Tray. Llama automáticamente al cargar la página.
 * Si QZ no está instalado, muestra un aviso al cajero.
 */
async function qzConectar() {
    if (qzConectado || qzConectando) return;
    if (typeof qz === 'undefined') {
        console.warn('QZ Tray JS no cargado — verificá los scripts en cabecera.php');
        return;
    }

    qzConectando = true;

    // Sin certificado (modo desarrollo / HTTP)
    // Si tu sistema usa HTTPS, necesitás firmar con el certificado de QZ Tray.
    qz.security.setCertificatePromise(() => Promise.resolve(''));
    qz.security.setSignatureAlgorithm('SHA512');
    qz.security.setSignaturePromise(() => Promise.resolve(''));

    try {
        await qz.websocket.connect(QZ_CONFIG);
        qzConectado = true;
        console.log('[QZ] Conectado');
        qzActualizarIndicador(true);
    } catch (e) {
        console.warn('[QZ] No se pudo conectar:', e.message || e);
        qzActualizarIndicador(false);
    } finally {
        qzConectando = false;
    }
}

async function qzDesconectar() {
    if (!qzConectado) return;
    try {
        await qz.websocket.disconnect();
    } catch {}
    qzConectado = false;
}

/** Actualiza el badge de estado en la página si existe */
function qzActualizarIndicador(conectado) {
    const badge = document.getElementById('qz-badge');
    if (!badge) return;
    badge.textContent    = conectado ? '● QZ Tray conectado' : '● QZ Tray desconectado';
    badge.className      = conectado ? 'badge bg-success' : 'badge bg-danger';
}

// ═══════════════════════════════════════════════════════════════════════════════
// 2. LEER CONFIG DE LA TICKETERA (BD vía config_ticketera.php)
// ═══════════════════════════════════════════════════════════════════════════════

async function qzObtenerConfig(sucursal_id) {
    if (configCache[sucursal_id]) return configCache[sucursal_id];
    try {
        const r = await fetch(`config_ticketera.php?accion=leer&sucursal=${sucursal_id}`);
        const d = await r.json();
        if (d.ip) {
            configCache[sucursal_id] = d;
            return d;
        }
    } catch {}
    return null;
}

// ═══════════════════════════════════════════════════════════════════════════════
// 3. IMPRIMIR PDF CON QZ TRAY
// ═══════════════════════════════════════════════════════════════════════════════

/**
 * Imprime el PDF del ticket en la ticketera usando QZ Tray.
 *
 * @param {string} pdfUrl   - URL completa del PDF (ticket.php?id=...&token=...&formato=ticket)
 * @param {object} cfg      - Config de la ticketera { ip, puerto, cols, copias, corte }
 */
async function qzImprimirPDF(pdfUrl, cfg) {
    if (!qzConectado) {
        throw new Error('QZ Tray no está conectado. Instalalo y reiniciá el navegador.');
    }

    const anchoMM = (cfg.cols ?? 48) >= 48 ? 80 : 58;

    // Configurar la impresora como dispositivo de red TCP/IP (RAW)
    // QZ Tray puede imprimir en impresoras de red directamente por IP:Puerto
    const printer = {
        host:     cfg.ip,
        port:     cfg.puerto ?? 9100,
        options:  { encoding: 'UTF-8' },
    };

    // Configuración del trabajo de impresión
    const config = qz.configs.create(printer, {
        colorType:   'blackWhite',
        duplex:      false,
        copies:      cfg.copias ?? 1,
        scaleContent: true,
        size:        { width: anchoMM, height: null },  // alto auto
        units:       'mm',
        margins:     { top: 0, right: 0, bottom: 0, left: 0 },
    });

    // Datos: PDF como URL (QZ lo descarga y lo manda)
    const data = [{
        type:   'pixel',
        format: 'pdf',
        flavor: 'file',
        data:   pdfUrl,
    }];

    await qz.print(config, data);
}

// ═══════════════════════════════════════════════════════════════════════════════
// 4. FUNCIÓN PRINCIPAL — reemplaza fn_abrir_pdf
// ═══════════════════════════════════════════════════════════════════════════════

function fn_abrir_pdf(id_venta) {
    fetch("ticket.php?accion=token&id=" + parseInt(id_venta))
        .then(r => r.json())
        .then(urls => {
            const token       = new URLSearchParams(urls.ticket.split('?')[1]).get('token');
            const qzDisponible = qzConectado;
            const qzLabel     = qzDisponible
                ? '🖨️ Imprimir en Ticketera<br><small style="font-weight:400;opacity:.8;">QZ Tray — Envío directo</small>'
                : '🖨️ Imprimir en Ticketera<br><small style="font-weight:400;opacity:.6;">QZ Tray no detectado</small>';

            Swal.fire({
                title: '¿Cómo deseas ver el comprobante?',
                html: `
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:8px;">

                    <button onclick="window.open('${urls.ticket}','_blank');Swal.close();location.reload();"
                        style="background:#2a2f5b;color:white;border:none;border-radius:12px;padding:14px 10px;cursor:pointer;font-weight:700;font-size:.88rem;">
                        🖨️ Ticket PDF<br><small style="font-weight:400;opacity:.8;">80mm / POS</small>
                    </button>

                    <button onclick="window.open('${urls.a4}','_blank');Swal.close();location.reload();"
                        style="background:#2a2f5b;color:white;border:none;border-radius:12px;padding:14px 10px;cursor:pointer;font-weight:700;font-size:.88rem;">
                        📄 A4<br><small style="font-weight:400;opacity:.8;">Hoja completa</small>
                    </button>

                    <button onclick="window.open('${urls.a5}','_blank');Swal.close();location.reload();"
                        style="background:#2a2f5b;color:white;border:none;border-radius:12px;padding:14px 10px;cursor:pointer;font-weight:700;font-size:.88rem;">
                        📋 A5<br><small style="font-weight:400;opacity:.8;">Medio oficio</small>
                    </button>

                    <button onclick="window.open('${urls.pantalla}','_blank');Swal.close();location.reload();"
                        style="background:#11998e;color:white;border:none;border-radius:12px;padding:14px 10px;cursor:pointer;font-weight:700;font-size:.88rem;">
                        🌐 Pantalla<br><small style="font-weight:400;opacity:.8;">HTML / WhatsApp</small>
                    </button>

                    <button
                        onclick="fn_imprimir_qz(${id_venta}, '${token}', '${urls.ticket}');Swal.close();"
                        style="background:${qzDisponible ? '#e63946' : '#6c757d'};color:white;border:none;border-radius:12px;padding:14px 10px;cursor:pointer;font-weight:700;font-size:.88rem;grid-column:span 2;">
                        ${qzLabel}
                    </button>

                </div>`,
                showConfirmButton: false,
                showCloseButton:   true,
                width:             360,
            });
        })
        .catch(() => Swal.fire('Error', 'No se pudo obtener el token del comprobante.', 'error'));
}

// ═══════════════════════════════════════════════════════════════════════════════
// 5. HANDLER DEL BOTÓN TICKETERA
// ═══════════════════════════════════════════════════════════════════════════════

async function fn_imprimir_qz(id_venta, token, pdfUrl) {

    // ── Verificar QZ Tray ──────────────────────────────────────────────────────
    if (!qzConectado) {
        Swal.fire({
            icon:  'warning',
            title: 'QZ Tray no está corriendo',
            html:  `
            <p style="font-size:14px;margin-bottom:12px">
                QZ Tray es un programa gratuito que debe estar instalado en esta PC
                para imprimir directo en la ticketera.
            </p>
            <a href="https://qz.io/download/" target="_blank"
               style="display:inline-block;background:#e63946;color:white;padding:10px 20px;
                      border-radius:8px;text-decoration:none;font-weight:700;font-size:13px;">
                ⬇ Descargar QZ Tray
            </a>
            <p style="font-size:11px;color:#888;margin-top:10px">
                Después de instalarlo, recargá esta página.
            </p>`,
            confirmButtonText:  'Reintentar conexión',
            confirmButtonColor: '#2a2f5b',
        }).then(async (res) => {
            if (res.isConfirmed) {
                await qzConectar();
                if (qzConectado) fn_imprimir_qz(id_venta, token, pdfUrl);
            }
        });
        return;
    }

    // ── Obtener config de la sucursal desde BD ─────────────────────────────────
    // sucursal_id viene de la sesión PHP — lo incrustamos en el HTML
    const sucursal_id = window.SUCURSAL_ID ?? 1;
    const cfg = await qzObtenerConfig(sucursal_id);

    if (!cfg || !cfg.ip) {
        Swal.fire({
            icon:  'error',
            title: 'Sin ticketera configurada',
            text:  'Ingresá a Configuración → Ticketera y guardá la IP de esta sucursal.',
            confirmButtonColor: '#2a2f5b',
        });
        return;
    }

    // ── Imprimir ───────────────────────────────────────────────────────────────
    Swal.fire({
        title:           'Imprimiendo...',
        html:            `<small>Enviando PDF a ${cfg.ip}:${cfg.puerto ?? 9100}</small>`,
        allowOutsideClick: false,
        didOpen:         () => Swal.showLoading(),
    });

    try {
        await qzImprimirPDF(pdfUrl, cfg);

        Swal.fire({
            icon:              'success',
            title:             '¡Ticket impreso!',
            html:              `<p>Enviado a <code>${cfg.ip}:${cfg.puerto ?? 9100}</code></p>`,
            timer:             2500,
            showConfirmButton: false,
        });
        setTimeout(() => location.reload(), 2600);

    } catch (err) {
        Swal.fire({
            icon:               'error',
            title:              'Error al imprimir',
            html:               `<p>${err.message || err}</p>
                                 <small style="color:#888">
                                   Verificá que QZ Tray esté corriendo y que
                                   la ticketera esté encendida en la red.
                                 </small>`,
            confirmButtonColor: '#2a2f5b',
        });
    }
}

// ═══════════════════════════════════════════════════════════════════════════════
// 6. AUTO-CONECTAR AL CARGAR LA PÁGINA
// ═══════════════════════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    qzConectar();
    // Re-intentar cada 15s si no está conectado (el cajero puede abrir QZ después)
    setInterval(() => { if (!qzConectado) qzConectar(); }, 15000);
});