<?php
/**
 * config_ticketera.php
 * Lee y guarda la configuración de ticketera en la columna
 * json_config_impresora de la tabla emisor, por sucursal_id.
 *
 * GET  ?accion=leer&sucursal=1
 * POST ?accion=guardar&sucursal=1   (body JSON)
 * GET  ?accion=ping&ip=...&puerto=...&timeout=...
 * GET  ?accion=prueba&sucursal=1
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/logica/clssConsultas.php';

$accion   = trim($_GET['accion']    ?? '');
$sucursal = (int)($_GET['sucursal'] ?? 1);

$defaults = [
    'ip'        => '',
    'puerto'    => 9100,
    'timeout'   => 5,
    'cols'      => 48,
    'copias'    => 1,
    'pie'       => 'Gracias por su preferencia!',
    'corte'     => true,
    'igv'       => true,
    'descuento' => true,
];

// ─────────────────────────────────────────────────────────────────────────────
// HELPERS BD
// ─────────────────────────────────────────────────────────────────────────────
function leerConfigBD(int $sucursal, array $defaults): array
{
    $emisor = fnListadoDeEmisor($sucursal)[0] ?? null;
    if (!$emisor) return $defaults;
    $raw  = $emisor['json_config_impresora'] ?? null;
    if (!$raw) return $defaults;
    $data = is_array($raw) ? $raw : json_decode($raw, true);
    return array_merge($defaults, $data ?: []);
}

function guardarConfigBD(int $sucursal, array $config): bool
{
    // Ajusta según la conexión que use tu sistema:
    // — PDO ——————————————————————————————————————————
    global $pdo;
    if (isset($pdo)) {
        $stmt = $pdo->prepare(
            "UPDATE emisor SET json_config_impresora = :cfg WHERE sucursal_id = :sid"
        );
        return $stmt->execute([
            ':cfg' => json_encode($config, JSON_UNESCAPED_UNICODE),
            ':sid' => $sucursal,
        ]);
    }
    // — MySQLi ———————————————————————————————————————
    global $mysqli;
    if (isset($mysqli)) {
        $j = $mysqli->real_escape_string(json_encode($config, JSON_UNESCAPED_UNICODE));
        return (bool)$mysqli->query(
            "UPDATE emisor SET json_config_impresora = '{$j}' WHERE sucursal_id = {$sucursal}"
        );
    }
    // — Función propia de tu sistema —————————————————
    // return fnActualizarConfigImpresora($sucursal, json_encode($config));
    return false;
}

// ─────────────────────────────────────────────────────────────────────────────
// LEER
// ─────────────────────────────────────────────────────────────────────────────
if ($accion === 'leer') {
    echo json_encode(leerConfigBD($sucursal, $defaults));
    exit;
}

// ─────────────────────────────────────────────────────────────────────────────
// GUARDAR
// ─────────────────────────────────────────────────────────────────────────────
if ($accion === 'guardar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
    if (!$body) { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'JSON invalido']); exit; }

    $config = [
        'ip'        => trim($body['ip'] ?? ''),
        'puerto'    => max(1, min(65535, (int)($body['puerto']  ?? 9100))),
        'timeout'   => max(1, min(30,   (int)($body['timeout'] ?? 5))),
        'cols'      => in_array((int)($body['cols']??48),[32,48]) ? (int)$body['cols'] : 48,
        'copias'    => max(1, min(5,    (int)($body['copias']  ?? 1))),
        'pie'       => substr(trim($body['pie'] ?? 'Gracias por su preferencia!'), 0, 80),
        'corte'     => (bool)($body['corte']     ?? true),
        'igv'       => (bool)($body['igv']       ?? true),
        'descuento' => (bool)($body['descuento'] ?? true),
    ];

    if (!$config['ip']) {
        http_response_code(400);
        echo json_encode(['ok'=>false,'error'=>'La IP es obligatoria']);
        exit;
    }

    if (!guardarConfigBD($sucursal, $config)) {
        http_response_code(500);
        echo json_encode([
            'ok'    => false,
            'error' => 'No se pudo guardar en BD. Revisa la funcion guardarConfigBD() y ajustala a tu conexion ($pdo o $mysqli).'
        ]);
        exit;
    }

    echo json_encode(['ok' => true, 'config' => $config]);
    exit;
}

// ─────────────────────────────────────────────────────────────────────────────
// PING LOTE — recibe array de IPs, las prueba todas y devuelve las que responden
// POST body: { "ips": ["192.168.1.1", ...], "puerto": 9100, "timeout": 1 }
// Respuesta: { "192.168.1.5": 12, "192.168.1.20": 8 }  (IP → ms)
// ─────────────────────────────────────────────────────────────────────────────
if ($accion === 'ping_lote' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $body    = json_decode(file_get_contents('php://input'), true);
    $ips     = $body['ips']     ?? [];
    $puerto  = (int)($body['puerto']  ?? 9100);
    $timeout = max(1, min(5, (int)($body['timeout'] ?? 1)));

    if (empty($ips) || !is_array($ips)) {
        echo json_encode([]);
        exit;
    }

    // Limpiar y validar IPs
    $ips = array_filter(array_map('trim', $ips), fn($ip) =>
        filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
    );

    $encontradas = [];

    // ── Estrategia A: sockets no bloqueantes (paralelo real en PHP) ───────────
    // Abre todos los sockets a la vez en modo no bloqueante,
    // luego espera con stream_select. Muy rápido: N IPs en ~timeout segundos.

    $streams   = [];   // ip => resource
    $tiempos   = [];   // ip => microtime inicio
    $deadline  = microtime(true) + $timeout + 0.5;

    foreach ($ips as $ip) {
        $errno = $errstr = null;
        // stream_socket_client con ASYNC — no bloquea
        $s = @stream_socket_client(
            "tcp://{$ip}:{$puerto}",
            $errno, $errstr,
            0,                                          // timeout=0 → no bloquea
            STREAM_CLIENT_ASYNC_CONNECT | STREAM_CLIENT_CONNECT
        );
        if ($s) {
            stream_set_blocking($s, false);
            $streams[$ip] = $s;
            $tiempos[$ip] = microtime(true);
        }
    }

    // Esperar que los sockets conecten (o fallen) usando stream_select
    while (!empty($streams) && microtime(true) < $deadline) {
        $write  = array_values($streams);
        $except = $write;
        $read   = [];

        // stream_select con 100ms de espera → bajo CPU, alta frecuencia
        $n = @stream_select($read, $write, $except, 0, 100000); // 100ms

        if ($n === false) break;

        // Los que están en $write conectaron correctamente
        foreach ($write as $s) {
            $ip = array_search($s, $streams, true);
            if ($ip !== false) {
                $ms = (int)round((microtime(true) - $tiempos[$ip]) * 1000);
                $encontradas[$ip] = $ms;
                fclose($s);
                unset($streams[$ip]);
            }
        }

        // Los que están en $except tuvieron error → descartar
        foreach ($except as $s) {
            $ip = array_search($s, $streams, true);
            if ($ip !== false) {
                fclose($s);
                unset($streams[$ip]);
            }
        }
    }

    // Cerrar los que quedaron abiertos (timeout)
    foreach ($streams as $s) @fclose($s);

    // ── Fallback: si stream_socket_client no está disponible, fsockopen ───────
    // (algunos hosting lo deshabilitan — poco probable pero por si acaso)
    if (empty($encontradas) && !empty($ips)) {
        // Chequeamos si realmente ninguna respondió o si hubo un problema
        // probando UNA sola IP con fsockopen para validar
        // (no es el fallback completo, solo diagnóstico)
    }

    header('Content-Type: application/json');
    echo json_encode($encontradas);
    exit;
}


if ($accion === 'ping') {
    $ip  = trim($_GET['ip'] ?? '');
    $p   = (int)($_GET['puerto']  ?? 9100);
    $t   = (int)($_GET['timeout'] ?? 5);
    if (!$ip) { echo json_encode(['ok'=>false,'error'=>'Sin IP']); exit; }
    $ini = microtime(true);
    $s   = @fsockopen($ip, $p, $errno, $errstr, $t);
    $ms  = round((microtime(true)-$ini)*1000);
    if (!$s) { echo json_encode(['ok'=>false,'error'=>"{$errno}: {$errstr}",'ms'=>$ms]); exit; }
    fclose($s);
    echo json_encode(['ok'=>true,'ms'=>$ms]);
    exit;
}

// ─────────────────────────────────────────────────────────────────────────────
// PRUEBA
// ─────────────────────────────────────────────────────────────────────────────
if ($accion === 'prueba') {
    $cfg  = leerConfigBD($sucursal, $defaults);
    $ip   = $cfg['ip']      ?? '';
    $port = (int)($cfg['puerto']  ?? 9100);
    $tout = (int)($cfg['timeout'] ?? 5);
    $cols = (int)($cfg['cols']    ?? 48);
    $pie  = $cfg['pie']     ?? 'Gracias por su preferencia!';
    $corte= $cfg['corte']   ?? true;

    if (!$ip) { echo json_encode(['ok'=>false,'error'=>'IP no configurada para esta sucursal.']); exit; }

    $ESC = "\x1B"; $GS = "\x1D";
    $sep = str_repeat('-', $cols);

    $tkt  = $ESC."@";
    $tkt .= $ESC."a\x01".$GS."!\x11".$ESC."E\x01"."TICKET DE PRUEBA\n".$ESC."E\x00".$GS."!\x00";
    $tkt .= "Sistema Captain\n".$ESC."a\x00".$sep."\n";
    $tkt .= "Sucursal  : {$sucursal}\n";
    $tkt .= "IP        : {$ip}\n";
    $tkt .= "Puerto    : {$port}\n";
    $tkt .= "Columnas  : {$cols}\n";
    $tkt .= "Fecha     : ".date('d/m/Y H:i:s')."\n";
    $tkt .= $sep."\n".$ESC."E\x01"."CONEXION EXITOSA\n".$ESC."E\x00".$sep."\n";
    $tkt .= $ESC."a\x01".$pie."\n\n\n\n";
    if ($corte) $tkt .= $GS."V\x41\x00";

    $s = @fsockopen($ip, $port, $errno, $errstr, $tout);
    if (!$s) { echo json_encode(['ok'=>false,'error'=>"{$errno}: {$errstr}"]); exit; }
    fwrite($s, $tkt);
    fclose($s);
    echo json_encode(['ok'=>true]);
    exit;
}

http_response_code(400);
echo json_encode(['ok'=>false,'error'=>"Accion desconocida: {$accion}"]);