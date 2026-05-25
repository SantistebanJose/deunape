<?php
/**
 * imprimir.php
 * 1. Llama a ticket.php?formato=ticket para obtener el PDF en memoria.
 * 2. Rasteriza el PDF a imagen (Imagick o Ghostscript, detecta automático).
 * 3. Convierte la imagen a comandos ESC/POS gráfico (GS v 0).
 * 4. Envía por TCP/IP a la ticketera térmica.
 *
 * POST body JSON: { "id_venta": 45, "token": "abc123..." }
 * El token se obtiene con: ticket.php?accion=token&id=45
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Solo POST']);
    exit;
}

// ── 1. Parámetros ─────────────────────────────────────────────────────────────
$body = json_decode(file_get_contents('php://input'), true);
if (!$body) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'JSON inválido']);
    exit;
}

$id_venta = (int)($body['id_venta'] ?? 0);
$token    = trim($body['token']     ?? '');

if (!$id_venta || !$token) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Faltan id_venta o token']);
    exit;
}

// ── 2. Config de ticketera desde BD ───────────────────────────────────────────
require_once __DIR__ . '/logica/clssConsultas.php';

$venta       = fnUltimaVentaPorIdVenta($id_venta)[0] ?? null;
$sucursal_id = (int)($venta['sucursal_id'] ?? 1);
$emisor      = fnListadoDeEmisor($sucursal_id)[0] ?? null;

if (!$emisor) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => "Emisor no encontrado para sucursal {$sucursal_id}"]);
    exit;
}

$raw_cfg      = $emisor['json_config_impresora'] ?? null;
$cfg          = $raw_cfg ? (is_array($raw_cfg) ? $raw_cfg : json_decode($raw_cfg, true)) : [];
$PRINTER_IP   = $cfg['ip']     ?? '';
$PRINTER_PORT = (int)($cfg['puerto']  ?? 9100);
$TIMEOUT      = (int)($cfg['timeout'] ?? 5);
$COPIAS       = max(1, min(5, (int)($cfg['copias'] ?? 1)));
$PAPER_W_PX   = ((int)($cfg['cols'] ?? 48)) >= 48 ? 576 : 384; // 80mm=576px | 58mm=384px a 203dpi
$CORTE        = $cfg['corte'] ?? true;

if (!$PRINTER_IP) {
    http_response_code(500);
    echo json_encode([
        'ok'    => false,
        'error' => "Sucursal {$sucursal_id} sin ticketera configurada. Abrí configuracion_ticketera.html."
    ]);
    exit;
}

// ── 3. Obtener el PDF de ticket.php vía HTTP interno ─────────────────────────
$protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
$base_path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$url       = "{$protocol}://{$host}{$base_path}/ticket.php"
           . "?id={$id_venta}&token={$token}&formato=ticket";

$ctx = stream_context_create([
    'http' => [
        'method'          => 'GET',
        'timeout'         => 20,
        'follow_location' => 1,
        'header'          => 'Cookie: ' . ($_SERVER['HTTP_COOKIE'] ?? ''),
    ],
    'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false],
]);

$pdf_bytes = @file_get_contents($url, false, $ctx);

if (!$pdf_bytes || substr($pdf_bytes, 0, 4) !== '%PDF') {
    http_response_code(500);
    echo json_encode([
        'ok'    => false,
        'error' => 'No se pudo obtener el PDF de ticket.php. URL: ' . $url,
    ]);
    exit;
}

// ── 4. Guardar PDF temporal ────────────────────────────────────────────────────
$tmp     = sys_get_temp_dir();
$pdf_tmp = "{$tmp}/tkt_{$id_venta}_" . time() . '.pdf';
$png_tmp = "{$tmp}/tkt_{$id_venta}_" . time() . '.png';
file_put_contents($pdf_tmp, $pdf_bytes);

// ── 5. Rasterizar PDF → PNG ────────────────────────────────────────────────────
$ok_raster = false;
$err_info  = '';
$DPI       = 203; // DPI real de la mayoría de ticketeras térmicas

// ── Intento A: Imagick ────────────────────────────────────────────────────────
if (!$ok_raster && extension_loaded('imagick')) {
    try {
        $im = new Imagick();
        $im->setResolution($DPI, $DPI);
        $im->readImage($pdf_tmp . '[0]');           // solo página 1
        $im->setImageColorspace(Imagick::COLORSPACE_GRAY);
        $im->setImageFormat('png');
        // Redimensionar al ancho exacto del papel
        $orig_w = $im->getImageWidth();
        $orig_h = $im->getImageHeight();
        $new_h  = (int)round($orig_h * $PAPER_W_PX / $orig_w);
        $im->resizeImage($PAPER_W_PX, $new_h, Imagick::FILTER_LANCZOS, 1);
        // Binarizar (blanco/negro puro, mejor para térmicas)
        $im->thresholdImage(0.55 * Imagick::getQuantum());
        $im->writeImage($png_tmp);
        $im->destroy();
        $ok_raster = true;
    } catch (Throwable $e) {
        $err_info .= '[Imagick] ' . $e->getMessage() . ' | ';
    }
}

// ── Intento B: Ghostscript ────────────────────────────────────────────────────
if (!$ok_raster) {
    foreach (['gs', '/usr/bin/gs', '/usr/local/bin/gs', 'gswin64c', 'gswin32c'] as $gs) {
        $ver = @shell_exec($gs . ' --version 2>&1');
        if (!$ver || !is_numeric(trim($ver))) continue;

        $cmd = $gs
             . ' -dBATCH -dNOPAUSE -dSAFER'
             . ' -sDEVICE=pnggray'
             . " -r{$DPI}"
             . ' -dFirstPage=1 -dLastPage=1'
             . ' -sOutputFile=' . escapeshellarg($png_tmp)
             . ' ' . escapeshellarg($pdf_tmp)
             . ' 2>&1';
        $out = @shell_exec($cmd);

        if (file_exists($png_tmp) && filesize($png_tmp) > 100) {
            // Redimensionar con GD al ancho correcto
            $src = @imagecreatefrompng($png_tmp);
            if ($src) {
                $sw  = imagesx($src);
                $sh  = imagesy($src);
                $nh  = (int)round($sh * $PAPER_W_PX / $sw);
                $dst = imagecreate($PAPER_W_PX, $nh);
                imagecolorallocate($dst, 255, 255, 255); // fondo blanco
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $PAPER_W_PX, $nh, $sw, $sh);
                imagepng($dst, $png_tmp);
                imagedestroy($src);
                imagedestroy($dst);
            }
            $ok_raster = true;
            break;
        }
        $err_info .= "[{$gs}] " . ($out ?? 'sin output') . ' | ';
    }
}

// ── Sin rasterizador ──────────────────────────────────────────────────────────
if (!$ok_raster) {
    @unlink($pdf_tmp);
    http_response_code(500);
    echo json_encode([
        'ok'      => false,
        'error'   => 'El servidor necesita Imagick o Ghostscript para convertir el PDF. Detalle: ' . $err_info,
        'fix'     => [
            'Ubuntu/Debian' => 'sudo apt install php-imagick ghostscript && sudo service apache2 restart',
            'CentOS'        => 'sudo yum install php-imagick ghostscript',
            'cPanel'        => 'cPanel → Módulos PHP → activar "imagick"',
            'Windows IIS'   => 'Descargar Ghostscript desde ghostscript.com y agregar al PATH',
        ],
    ]);
    exit;
}

// ── 6. PNG → datos ESC/POS (GS v 0 — raster bit image) ───────────────────────
$img = @imagecreatefrompng($png_tmp);
@unlink($pdf_tmp);
@unlink($png_tmp);

if (!$img) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'No se pudo leer el PNG rasterizado']);
    exit;
}

$img_w = imagesx($img);
$img_h = imagesy($img);

// El ancho debe ser múltiplo de 8 (requerimiento ESC/POS)
$print_w_bytes = (int)ceil($img_w / 8); // bytes por fila
$print_w_px    = $print_w_bytes * 8;    // px redondeado arriba

$xL = $print_w_bytes % 256;
$xH = (int)($print_w_bytes / 256);
$yL = $img_h % 256;
$yH = (int)($img_h / 256);

// Comando GS v 0: imagen raster
$ESC = "\x1B";
$GS  = "\x1D";

$escpos  = $ESC . "@";          // Inicializar
$escpos .= $ESC . "a\x00";     // Alinear izquierda (la imagen ya tiene el ancho exacto)

// Cabecera GS v 0
$escpos .= $GS . 'v' . '0' . chr(0) // m=0: sin escalar
         . chr($xL) . chr($xH)
         . chr($yL) . chr($yH);

// Datos: fila por fila, píxel por píxel → bit (oscuro=1, claro=0)
for ($y = 0; $y < $img_h; $y++) {
    for ($bx = 0; $bx < $print_w_bytes; $bx++) {
        $byte = 0;
        for ($bit = 0; $bit < 8; $bit++) {
            $px = $bx * 8 + $bit;
            if ($px < $img_w) {
                $color = imagecolorat($img, $px, $y);
                // Extraer luminancia (imagen ya es gris pero por si acaso)
                $r   = ($color >> 16) & 0xFF;
                $g   = ($color >>  8) & 0xFF;
                $b   = ($color)       & 0xFF;
                $lum = (int)(0.299 * $r + 0.587 * $g + 0.114 * $b);
                if ($lum < 128) {
                    $byte |= (0x80 >> $bit); // pixel oscuro = punto impreso
                }
            }
        }
        $escpos .= chr($byte);
    }
}

imagedestroy($img);

// Avance de papel y corte
$escpos .= "\n\n\n\n";
if ($CORTE) $escpos .= $GS . "V\x41\x00"; // corte parcial

// ── 7. Enviar por TCP ──────────────────────────────────────────────────────────
$socket = @fsockopen($PRINTER_IP, $PRINTER_PORT, $errno, $errstr, $TIMEOUT);

if (!$socket) {
    http_response_code(500);
    echo json_encode([
        'ok'    => false,
        'error' => "No se pudo conectar a {$PRINTER_IP}:{$PRINTER_PORT} — {$errno}: {$errstr}",
    ]);
    exit;
}

for ($i = 0; $i < $COPIAS; $i++) {
    fwrite($socket, $escpos);
    // Pequeña pausa entre copias para no saturar el buffer
    if ($i < $COPIAS - 1) usleep(300000);
}
fclose($socket);

echo json_encode([
    'ok'      => true,
    'mensaje' => "PDF enviado ({$COPIAS} copia/s) a {$PRINTER_IP}:{$PRINTER_PORT}",
    'bytes'   => strlen($escpos),
    'resolucion' => "{$img_w}x{$img_h}px a {$DPI}dpi",
]);