<?php
include("cabecera.php");

// ── Detectar IP base del servidor para sugerir el rango ───────────────────────
$server_ip = $_SERVER['SERVER_ADDR'] ?? gethostbyname(gethostname());
$ip_parts  = explode('.', $server_ip);
$ip_base   = count($ip_parts) === 4
    ? $ip_parts[0].'.'.$ip_parts[1].'.'.$ip_parts[2].'.'
    : '192.168.1.';
$ip_rango_sugerido = rtrim($ip_base, '.');

// ── Leer sucursales para el selector ─────────────────────────────────────────
require_once __DIR__ . '/logica/clssConsultas.php';
$emisores = fnListadoDeEmisor(null) ?? [];
session_start();
$sucursalActiva = (int)($_GET['sucursal'] ?? $_SESSION['sucursal_id'] ?? ($emisores[0]['sucursal_id'] ?? 1));
?>

<div class="container">
  <div class="page-inner">

    <!-- HEADER -->
    <div class="page-header mb-3">
      <h3 class="fw-bold mb-1">
        <i class="fa fa-print me-2" style="color:#e63946"></i>
        Configuración de Ticketera
      </h3>
      <p class="text-muted mb-0" style="font-size:13px">
        Detectá impresoras térmicas en tu red local y configurá una por sucursal.
      </p>
    </div>

    <div class="row g-3">

      <!-- ══════════════════════════════════════════════
           COLUMNA IZQUIERDA: Escáner de red
           ══════════════════════════════════════════════ -->
      <div class="col-lg-6">

        <!-- Card: Escanear red -->
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-header bg-dark text-white d-flex align-items-center gap-2 py-2">
            <i class="fa fa-wifi"></i>
            <span class="fw-semibold" style="font-size:13px">ESCANEAR RED LOCAL</span>
          </div>
          <div class="card-body">

            <div class="row g-2 mb-3">
              <div class="col-8">
                <label class="form-label text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px">Rango de red (primeros 3 octetos)</label>
                <div class="input-group input-group-sm">
                  <input type="text" id="ipBase" class="form-control font-monospace"
                         value="<?= htmlspecialchars($ip_rango_sugerido) ?>"
                         placeholder="192.168.1" maxlength="12">
                  <span class="input-group-text font-monospace text-muted">.1 → .254</span>
                </div>
                <small class="text-muted">Red detectada del servidor</small>
              </div>
              <div class="col-4">
                <label class="form-label text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px">Puerto</label>
                <input type="number" id="puertoScan" class="form-control form-control-sm font-monospace"
                       value="9100" min="1" max="65535">
              </div>
            </div>

            <div class="d-flex gap-2 mb-3">
              <button class="btn btn-danger btn-sm fw-semibold px-4" onclick="iniciarScan()" id="btnScan">
                <i class="fa fa-search me-1"></i> Escanear
              </button>
              <button class="btn btn-outline-secondary btn-sm" onclick="detenerScan()" id="btnDetener" style="display:none">
                <i class="fa fa-stop me-1"></i> Detener
              </button>
            </div>

            <!-- Progress -->
            <div id="scanProgress" style="display:none" class="mb-2">
              <div class="d-flex justify-content-between mb-1">
                <small class="text-muted" id="scanStatus">Escaneando...</small>
                <small class="text-muted font-monospace" id="scanPct">0%</small>
              </div>
              <div class="progress" style="height:6px">
                <div class="progress-bar bg-danger" id="progressBar" style="width:0%;transition:width .1s"></div>
              </div>
            </div>

            <!-- Resultados del scan -->
            <div id="resultadosScan">
              <div class="text-center text-muted py-4" style="font-size:13px" id="scanEmpty">
                <i class="fa fa-print fa-2x mb-2 d-block opacity-25"></i>
                Presioná "Escanear" para buscar ticketeras en la red
              </div>
            </div>

          </div>
        </div>

      </div>

      <!-- ══════════════════════════════════════════════
           COLUMNA DERECHA: Configuración
           ══════════════════════════════════════════════ -->
      <div class="col-lg-6">

        <!-- Card: Sucursal -->
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-header bg-dark text-white d-flex align-items-center gap-2 py-2">
            <i class="fa fa-building"></i>
            <span class="fw-semibold" style="font-size:13px">SUCURSAL</span>
          </div>
          <div class="card-body">
            <label class="form-label text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px">Configurando para</label>
            <select class="form-select form-select-sm font-monospace" id="selectSucursal"
                    onchange="cambiarSucursal(this.value)">
              <?php foreach ($emisores as $e):
                $sid  = (int)$e['sucursal_id'];
                $name = htmlspecialchars($e['nombre_comercial'] ?: $e['razon_social']);
                $sel  = $sid === $sucursalActiva ? 'selected' : '';
              ?>
              <option value="<?= $sid ?>" <?= $sel ?>><?= $name ?> — Sucursal <?= $sid ?></option>
              <?php endforeach; ?>
            </select>

            <!-- Estado actual -->
            <div id="estadoActual" class="mt-2 p-2 rounded" style="background:#f8f9fa;font-size:12px;display:none">
              <i class="fa fa-check-circle text-success me-1"></i>
              <span id="estadoActualTxt"></span>
            </div>
          </div>
        </div>

        <!-- Card: Configuración manual -->
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-header bg-dark text-white d-flex align-items-center gap-2 py-2">
            <i class="fa fa-cog"></i>
            <span class="fw-semibold" style="font-size:13px">CONFIGURACIÓN</span>
            <span class="badge bg-secondary ms-auto font-monospace" id="badgeSucursal" style="font-size:10px">
              SUC. <?= $sucursalActiva ?>
            </span>
          </div>
          <div class="card-body">

            <div class="row g-2 mb-2">
              <div class="col-8">
                <label class="form-label text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px">IP de la ticketera</label>
                <input type="text" id="ip" class="form-control form-control-sm font-monospace"
                       placeholder="192.168.1.100">
              </div>
              <div class="col-4">
                <label class="form-label text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px">Puerto</label>
                <input type="number" id="puerto" class="form-control form-control-sm font-monospace"
                       value="9100">
              </div>
            </div>

            <div class="row g-2 mb-2">
              <div class="col-6">
                <label class="form-label text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px">Papel</label>
                <select id="cols" class="form-select form-select-sm">
                  <option value="32">58 mm (32 col.)</option>
                  <option value="48" selected>80 mm (48 col.)</option>
                </select>
              </div>
              <div class="col-3">
                <label class="form-label text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px">Copias</label>
                <input type="number" id="copias" class="form-control form-control-sm" value="1" min="1" max="5">
              </div>
              <div class="col-3">
                <label class="form-label text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px">Timeout</label>
                <input type="number" id="timeout" class="form-control form-control-sm" value="5" min="1" max="30">
              </div>
            </div>

            <div class="mb-2">
              <label class="form-label text-muted" style="font-size:11px;text-transform:uppercase;letter-spacing:.5px">Pie del ticket</label>
              <input type="text" id="pie" class="form-control form-control-sm"
                     value="¡Gracias por su preferencia!" maxlength="80">
            </div>

            <!-- Toggles -->
            <div class="d-flex flex-wrap gap-3 mb-3 pt-1">
              <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="corte" checked>
                <label class="form-check-label" style="font-size:12px" for="corte">Corte auto</label>
              </div>
              <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="igv" checked>
                <label class="form-check-label" style="font-size:12px" for="igv">Mostrar IGV</label>
              </div>
              <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="descuento" checked>
                <label class="form-check-label" style="font-size:12px" for="descuento">Mostrar descuento</label>
              </div>
            </div>

            <!-- Botones acción -->
            <div class="d-flex gap-2 flex-wrap">
              <button class="btn btn-danger btn-sm fw-semibold" onclick="guardarConfig()" id="btnGuardar">
                <i class="fa fa-save me-1"></i> Guardar
              </button>
              <button class="btn btn-outline-dark btn-sm" onclick="probarConexion()" id="btnPing">
                <i class="fa fa-bolt me-1"></i> Probar conexión
              </button>
              <button class="btn btn-outline-secondary btn-sm" onclick="imprimirPrueba()">
                <i class="fa fa-print me-1"></i> Ticket de prueba
              </button>
            </div>

            <!-- Resultado ping -->
            <div id="pingResult" class="mt-2" style="display:none;font-size:12px"></div>

          </div>
        </div>

      </div>
    </div><!-- /row -->

  </div>
</div>

<!-- ── TOAST ─────────────────────────────────────────────────────────────────── -->
<div id="toastWrap" style="position:fixed;bottom:24px;right:24px;z-index:9999"></div>

<script>
// ── Globals ────────────────────────────────────────────────────────────────────
let sucursalActual = <?= $sucursalActiva ?>;
let scanActivo     = false;
let scanController = null;

// ── Toast ──────────────────────────────────────────────────────────────────────
function toast(msg, tipo = 'success') {
    const colores = { success:'#198754', danger:'#dc3545', warning:'#ffc107', info:'#0dcaf0' };
    const el = document.createElement('div');
    el.style.cssText = `background:#1a1a2e;color:#fff;border-left:4px solid ${colores[tipo]||'#6c757d'};
        border-radius:8px;padding:12px 18px;font-size:13px;margin-top:8px;
        box-shadow:0 4px 20px rgba(0,0,0,.3);max-width:320px;
        animation:slideIn .3s ease`;
    el.innerHTML = msg;
    document.getElementById('toastWrap').appendChild(el);
    setTimeout(() => el.remove(), 4000);
}

// ── Cargar config desde BD ─────────────────────────────────────────────────────
async function cargarConfig() {
    try {
        const r = await fetch(`config_ticketera.php?accion=leer&sucursal=${sucursalActual}`);
        const d = await r.json();

        document.getElementById('ip').value      = d.ip      || '';
        document.getElementById('puerto').value  = d.puerto  || 9100;
        document.getElementById('timeout').value = d.timeout || 5;
        document.getElementById('cols').value    = d.cols    || 48;
        document.getElementById('copias').value  = d.copias  || 1;
        document.getElementById('pie').value     = d.pie     || '¡Gracias por su preferencia!';
        document.getElementById('corte').checked     = d.corte     !== false;
        document.getElementById('igv').checked       = d.igv       !== false;
        document.getElementById('descuento').checked = d.descuento !== false;

        const estadoEl  = document.getElementById('estadoActual');
        const estadoTxt = document.getElementById('estadoActualTxt');
        if (d.ip) {
            estadoTxt.textContent = `Ticketera configurada: ${d.ip}:${d.puerto}`;
            estadoEl.style.display = 'block';
        } else {
            estadoEl.style.display = 'none';
        }
        document.getElementById('badgeSucursal').textContent = `SUC. ${sucursalActual}`;
    } catch(e) {
        console.warn('Sin config previa:', e);
    }
}

function cambiarSucursal(id) {
    sucursalActual = parseInt(id);
    cargarConfig();
}

// ── Guardar config ─────────────────────────────────────────────────────────────
async function guardarConfig() {
    const btn = document.getElementById('btnGuardar');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Guardando...';

    const cfg = {
        ip:        document.getElementById('ip').value.trim(),
        puerto:    parseInt(document.getElementById('puerto').value),
        timeout:   parseInt(document.getElementById('timeout').value),
        cols:      parseInt(document.getElementById('cols').value),
        copias:    parseInt(document.getElementById('copias').value),
        pie:       document.getElementById('pie').value.trim(),
        corte:     document.getElementById('corte').checked,
        igv:       document.getElementById('igv').checked,
        descuento: document.getElementById('descuento').checked,
    };

    if (!cfg.ip) {
        toast('Ingresá la IP de la ticketera.', 'danger');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-save me-1"></i> Guardar';
        return;
    }

    try {
        const r = await fetch(`config_ticketera.php?accion=guardar&sucursal=${sucursalActual}`, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify(cfg),
        });
        const d = await r.json();
        if (!d.ok) throw new Error(d.error);
        toast('✓ Configuración guardada en la base de datos', 'success');
        cargarConfig();
    } catch(e) {
        toast('Error: ' + e.message, 'danger');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-save me-1"></i> Guardar';
    }
}

// ── Probar conexión ────────────────────────────────────────────────────────────
async function probarConexion() {
    const ip      = document.getElementById('ip').value.trim();
    const puerto  = document.getElementById('puerto').value;
    const timeout = document.getElementById('timeout').value;
    const resEl   = document.getElementById('pingResult');

    if (!ip) { toast('Ingresá la IP primero.', 'warning'); return; }

    resEl.style.display = 'block';
    resEl.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Probando conexión...';

    try {
        const r = await fetch(`config_ticketera.php?accion=ping&ip=${encodeURIComponent(ip)}&puerto=${puerto}&timeout=${timeout}`);
        const d = await r.json();
        if (d.ok) {
            resEl.innerHTML = `<span class="text-success"><i class="fa fa-check-circle me-1"></i>Conectado en ${d.ms} ms</span>`;
        } else {
            resEl.innerHTML = `<span class="text-danger"><i class="fa fa-times-circle me-1"></i>${d.error || 'Sin respuesta'}</span>`;
        }
    } catch {
        resEl.innerHTML = `<span class="text-danger"><i class="fa fa-times-circle me-1"></i>Error de conexión</span>`;
    }
}

// ── Ticket de prueba ───────────────────────────────────────────────────────────
async function imprimirPrueba() {
    const ip = document.getElementById('ip').value.trim();
    if (!ip) { toast('Guardá la configuración primero.', 'warning'); return; }
    toast('<i class="fa fa-print me-1"></i> Enviando ticket de prueba...', 'info');
    try {
        const r = await fetch(`config_ticketera.php?accion=prueba&sucursal=${sucursalActual}`);
        const d = await r.json();
        d.ok ? toast('✓ Ticket de prueba enviado', 'success')
             : toast('Error: ' + (d.error || 'No se pudo imprimir'), 'danger');
    } catch {
        toast('Error de conexión con el servidor', 'danger');
    }
}

// ── Aplicar IP detectada desde el escáner ─────────────────────────────────────
function usarIP(ip, puerto) {
    document.getElementById('ip').value     = ip;
    document.getElementById('puerto').value = puerto;
    // Scroll suave a la sección de config
    document.getElementById('ip').scrollIntoView({ behavior: 'smooth', block: 'center' });
    document.getElementById('ip').focus();
    toast(`✓ IP ${ip} cargada en la configuración`, 'success');
}

// ── ESCÁNER DE RED (Promise.all en lotes) ─────────────────────────────────────
//
// Estrategia:
//   • Las 254 IPs se dividen en LOTES de N IPs (configurable).
//   • Cada lote se envía en UNA sola petición POST al backend (accion=ping_lote).
//   • El backend hace los fsockopen en paralelo con pcntl_fork o simplemente
//     los prueba uno a uno pero con timeout=1 y los devuelve todos juntos.
//   • Los lotes se lanzan de a CONCURRENCIA lotes simultáneos con Promise.all.
//   • Resultado: 254 IPs en ~5-15 segundos en vez de 3-5 minutos.
//
const LOTE_SIZE   = 20;   // IPs por petición al backend
const CONCURRENCIA = 6;   // lotes simultáneos (peticiones HTTP en paralelo)

async function iniciarScan() {
    const ipBase = document.getElementById('ipBase').value.trim();
    const puerto = parseInt(document.getElementById('puertoScan').value) || 9100;

    if (!ipBase || ipBase.split('.').length < 3) {
        toast('Ingresá un rango válido (ej: 192.168.1)', 'warning');
        return;
    }

    // ── UI: estado activo ──────────────────────────────────────────────────────
    scanActivo = true;
    document.getElementById('btnScan').style.display      = 'none';
    document.getElementById('btnDetener').style.display   = 'inline-block';
    document.getElementById('scanProgress').style.display = 'block';
    document.getElementById('scanEmpty').style.display    = 'none';

    const container = document.getElementById('resultadosScan');
    container.innerHTML = '';

    // ── Construir todos los lotes ──────────────────────────────────────────────
    // lotes = [ [1,2,...,20], [21,...,40], ... ]
    const lotes = [];
    for (let i = 1; i <= 254; i += LOTE_SIZE) {
        const lote = [];
        for (let j = i; j < i + LOTE_SIZE && j <= 254; j++) lote.push(j);
        lotes.push(lote);
    }

    const totalLotes  = lotes.length;
    let   lotesHechos = 0;
    let   encontradas = 0;

    // ── Función para procesar un lote ──────────────────────────────────────────
    const procesarLote = async (lote) => {
        if (!scanActivo) return;

        const ips = lote.map(n => `${ipBase}.${n}`);

        try {
            const r = await fetch('config_ticketera.php?accion=ping_lote', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify({ ips, puerto, timeout: 1 }),
            });
            const resultados = await r.json(); // { "192.168.1.5": 12, ... } ms por IP

            // Mostrar las que respondieron
            for (const [ip, ms] of Object.entries(resultados)) {
                if (!scanActivo) break;
                encontradas++;
                agregarResultado(container, ip, puerto, ms, encontradas);
            }
        } catch { /* lote fallido, seguimos */ }

        // Actualizar progreso
        lotesHechos++;
        const pct = Math.round(lotesHechos / totalLotes * 100);
        document.getElementById('progressBar').style.width = pct + '%';
        document.getElementById('scanPct').textContent     = pct + '%';
        const desde = `${ipBase}.${lote[0]}`;
        const hasta = `${ipBase}.${lote[lote.length - 1]}`;
        document.getElementById('scanStatus').textContent  = `Escaneando ${desde} → ${hasta}...`;
    };

    // ── Ejecutar lotes con concurrencia limitada ───────────────────────────────
    // Procesa CONCURRENCIA lotes a la vez, avanza conforme terminan
    for (let i = 0; i < lotes.length && scanActivo; i += CONCURRENCIA) {
        const grupo = lotes.slice(i, i + CONCURRENCIA);
        await Promise.all(grupo.map(procesarLote));
    }

    // ── Fin del scan ───────────────────────────────────────────────────────────
    scanActivo = false;
    document.getElementById('btnScan').style.display    = 'inline-block';
    document.getElementById('btnDetener').style.display = 'none';
    document.getElementById('progressBar').style.width  = '100%';
    document.getElementById('scanPct').textContent      = '100%';
    document.getElementById('scanStatus').textContent   = encontradas > 0
        ? `✓ Scan completado — ${encontradas} ticketera(s) encontrada(s)`
        : 'Scan completado — No se encontraron ticketeras en este rango';

    if (encontradas === 0) {
        container.innerHTML = `
        <div class="text-center text-muted py-4" style="font-size:13px">
            <i class="fa fa-exclamation-circle fa-2x mb-2 d-block text-warning opacity-75"></i>
            No se encontraron impresoras en el puerto ${puerto}.<br>
            <small>Verificá que la ticketera esté encendida y en la misma red.</small>
        </div>`;
    }
}

function agregarResultado(container, ip, puerto, ms, n) {
    const card = document.createElement('div');
    card.className = 'border rounded p-2 mb-2 d-flex align-items-center gap-2';
    card.style.cssText = 'background:#f8fff9;border-color:#b8dfc0!important;animation:fadeIn .3s ease';
    card.innerHTML = `
        <span class="badge bg-success rounded-pill" style="font-size:10px">${n}</span>
        <div class="flex-grow-1">
            <div class="fw-semibold font-monospace" style="font-size:13px">${ip}</div>
            <div class="text-muted" style="font-size:11px">
                Puerto ${puerto} &nbsp;·&nbsp;
                <span class="text-success">${ms} ms</span> &nbsp;·&nbsp;
                Ticketera ESC/POS
            </div>
        </div>
        <button class="btn btn-danger btn-sm" style="font-size:11px;white-space:nowrap"
                onclick="usarIP('${ip}', ${puerto})">
            <i class="fa fa-arrow-right me-1"></i> Usar esta
        </button>`;
    container.appendChild(card);
}

function detenerScan() {
    scanActivo = false;
    document.getElementById('btnScan').style.display    = 'inline-block';
    document.getElementById('btnDetener').style.display = 'none';
    document.getElementById('scanStatus').textContent   = 'Scan detenido por el usuario';
}

// ── CSS extra ──────────────────────────────────────────────────────────────────
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn { from { transform:translateX(20px);opacity:0 } to { transform:none;opacity:1 } }
    @keyframes fadeIn  { from { opacity:0;transform:translateY(4px) } to { opacity:1;transform:none } }
    .form-check-input:checked { background-color:#e63946;border-color:#e63946; }
`;
document.head.appendChild(style);

// ── Inicio ─────────────────────────────────────────────────────────────────────
cargarConfig();
</script>

<?php include("pie.php"); ?>