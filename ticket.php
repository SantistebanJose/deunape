<?php
ob_start();

// ═══════════════════════════════════════════════════════════════
// CLAVE SECRETA
// ═══════════════════════════════════════════════════════════════
define('TICKET_SECRET', 'c@r@c0l_s3cr3t_2025_xK9!mZpQ');

function ticket_token(int $id): string
{
    return hash_hmac('sha256', (string)$id, TICKET_SECRET);
}

function ticket_token_valido(int $id, string $token): bool
{
    return hash_equals(ticket_token($id), $token);
}

// ═══════════════════════════════════════════════════════════════
// ROUTER
// ═══════════════════════════════════════════════════════════════
$accion  = $_GET['accion']  ?? '';
$idVenta = isset($_GET['id'])     ? (int)$_GET['id']         : 0;
$token   = isset($_GET['token'])  ? trim($_GET['token'])      : '';
$formato = isset($_GET['formato']) ? trim($_GET['formato'])   : 'ticket'; // ticket | a4 | a5 | pantalla

// ── accion=token → JSON con URLs firmadas por formato ───────
if ($accion === 'token') {
    ob_clean();
    header('Content-Type: application/json');
    if (!$idVenta) {
        http_response_code(400);
        echo json_encode(['error' => 'ID inválido']);
        exit;
    }
    $tk = ticket_token($idVenta);
    echo json_encode([
        'ticket'   => "ticket.php?id={$idVenta}&token={$tk}&formato=ticket",
        'a4'       => "ticket.php?id={$idVenta}&token={$tk}&formato=a4",
        'a5'       => "ticket.php?id={$idVenta}&token={$tk}&formato=a5",
        'pantalla' => "ticket.php?id={$idVenta}&token={$tk}&formato=pantalla",
    ]);
    exit;
}

// ── accion=xml → devuelve el XML de un comprobante ───────────
if ($accion === 'xml') {
    ob_clean();
    header('Content-Type: application/json');
    $nombrexml = isset($_GET['nombrexml']) ? basename($_GET['nombrexml']) : '';
    $sucursal  = isset($_GET['sucursal'])  ? (int)$_GET['sucursal']       : 1;
    if (!$nombrexml) { echo json_encode(['error' => 'Sin nombre de archivo']); exit; }
    $base    = dirname(__FILE__) . "/sucursales/{$sucursal}/xml/";
    $rutaXML = $base . $nombrexml . '.XML';
    if (!file_exists($rutaXML)) $rutaXML = $base . $nombrexml . '.xml';
    if (!file_exists($rutaXML)) { echo json_encode(['error' => "Archivo no encontrado: {$nombrexml}.XML"]); exit; }
    $dom = new DOMDocument('1.0', 'utf-8');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput       = true;
    @$dom->loadXML(file_get_contents($rutaXML));
    echo json_encode(['xml' => $dom->saveXML(), 'nombrexml' => $nombrexml]);
    exit;
}

// ── Sin accion → generar comprobante (requiere token válido) ──
require("logica/clssConsultas.php");
require('fpdf/fpdf.php');

if (!$idVenta || !$token) { http_response_code(400); die('Solicitud inválida.'); }
if (!ticket_token_valido($idVenta, $token)) { http_response_code(403); die('Acceso no autorizado.'); }

// Enrutar al formato correspondiente
switch ($formato) {
    case 'a4':       fnGenerarA4($idVenta);       break;
    case 'a5':       fnGenerarA5($idVenta);       break;
    case 'pantalla': fnGenerarPantalla($idVenta); break;
    default:         fnGenerarTicket($idVenta);   break;
}
exit;


// ═══════════════════════════════════════════════════════════════
// HELPERS COMPARTIDOS
// ═══════════════════════════════════════════════════════════════

/** Carga y valida los datos base de una venta */
function fnCargarDatosVenta(int $idVenta): array
{
    $datosprueba = fnUltimaVentaPorIdVenta($idVenta)[0];
    $sucursal_id = $datosprueba["sucursal_id"] ?? null;
    $datoEmisor  = fnListadoDeEmisor($sucursal_id)[0] ?? null;
    if (!$datoEmisor) {
        http_response_code(500);
        die("Error: No se encontraron datos del emisor para esta sucursal");
    }
    $datosVenta = [
        "codigo_tiket"          => $datosprueba["codigo_tiket"],
        "tipo_comprobante"      => $datosprueba["tipo_comprobante"],
        "fecha"                 => $datosprueba["fecha"],
        "hora"                  => $datosprueba["hora"],
        "cliente"               => $datosprueba["cliente"],
        "numero_doc_cliente"    => $datosprueba["numero_doc_cliente"],
        "usuario_inicial"       => $datosprueba["usuario"],
        "usuario_final"         => $datosprueba["atencion_final_usuario"],
        "total"                 => $datosprueba["total"],
        "monto_venta_final"     => $datosprueba["monto_venta_final"],
        "estado_pago"           => $datosprueba["estado_pago"],
        "estado_final"          => $datosprueba["estado_final"],
        "descuento"             => $datosprueba["perdida_utilidad"],
        "js_detalle"            => $datosprueba["js_detalle"],
        "js_detalle_forma_pago" => $datosprueba["js_detalle_forma_pago"],
    ];
    $productos = json_decode($datosVenta["js_detalle"], true);
    $pagos     = json_decode($datosVenta["js_detalle_forma_pago"], true);
    return [$datosVenta, $datoEmisor, $productos, $pagos];
}

/** Resuelve la ruta del logo */
function fnResolverLogo(array $datoEmisor): ?string
{
    if (!empty($datoEmisor["ruta_logo"]) && file_exists($datoEmisor["ruta_logo"]))
        return $datoEmisor["ruta_logo"];
    if (file_exists('logica/logo.jpeg'))
        return 'logica/logo.jpeg';
    return null;
}

/** Genera el subtotal IGV para comprobantes A4/A5 */
function fnCalcularImpuestos(array $productos, float $total): array
{
    $subtotal = round($total / 1.18, 2);
    $igv      = round($total - $subtotal, 2);
    return ['subtotal' => $subtotal, 'igv' => $igv];
}


// ═══════════════════════════════════════════════════════════════
// FORMATO 1: TICKET / POS (80 mm) — original mejorado
// ═══════════════════════════════════════════════════════════════
function fnGenerarTicket(int $idVenta): void
{
    [$datosVenta, $datoEmisor, $productos, $pagos] = fnCargarDatosVenta($idVenta);

    ob_clean();
    $pdf = new FPDF('P', 'mm', [80, 200]);
    $pdf->AddPage();
    $pdf->SetMargins(3, 3, 3);

    // Logo
    $logoPath = fnResolverLogo($datoEmisor);
    if ($logoPath) {
        $lw = 20;
        $pdf->Image($logoPath, (80 - $lw) / 2, 5, $lw);
        $pdf->Ln(22);
    } else {
        $pdf->Ln(4);
    }

    // Emisor
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(74, 4, utf8_decode($datoEmisor["razon_social"]), 0, 1, 'C');
    $pdf->Cell(74, 4, "RUC: " . $datoEmisor["ruc"], 0, 1, 'C');
    $pdf->SetFont('Arial', '', 6);
    $pdf->MultiCell(74, 3.5, utf8_decode($datoEmisor["direccion"]), 0, 'C');
    $pdf->Ln(1);

    // Tipo comprobante
    $pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(74, 5, utf8_decode($datosVenta["tipo_comprobante"] . ' ELECTRÓNICA'), 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(74, 4, $datosVenta["codigo_tiket"], 0, 1, 'C');
    $pdf->Ln(1);
    $pdf->SetFont('Arial', '', 6);
    $pdf->Cell(74, 3, str_repeat('-', 46), 0, 1, 'C');
    $pdf->Ln(1);

    // Datos cliente
    $pdf->SetFont('Arial', 'B', 6.5);
    $pdf->Cell(74, 3.5, 'Cliente : ' . utf8_decode($datosVenta["cliente"]), 0, 1, 'L');
    $pdf->Cell(74, 3.5, 'DNI/RUC: ' . $datosVenta["numero_doc_cliente"], 0, 1, 'L');
    $pdf->Cell(74, 3.5, 'Fecha   : ' . $datosVenta["fecha"] . ' ' . $datosVenta["hora"], 0, 1, 'L');
    $pdf->Ln(1);
    $pdf->Cell(74, 3, str_repeat('-', 46), 0, 1, 'C');
    $pdf->Ln(1);

    // Cabecera productos
    $pdf->SetFont('Arial', 'B', 6.5);
    $pdf->Cell(32, 3.5, 'DESCRIPCION', 0, 0, 'L');
    $pdf->Cell(9,  3.5, 'CANT.',       0, 0, 'C');
    $pdf->Cell(17, 3.5, 'P.UNIT.',     0, 0, 'C');
    $pdf->Cell(16, 3.5, 'TOTAL',       0, 1, 'R');
    $pdf->SetFont('Arial', '', 6.5);
    $pdf->Ln(1);

    foreach ($productos as $p) {
        $yIni = $pdf->GetY();
        $pdf->MultiCell(32, 3.5, utf8_decode($p["descripcion_2"]), 0, 'L');
        $yFin = $pdf->GetY();
        $h    = $yFin - $yIni;
        $pdf->SetY($yIni); $pdf->SetX(35);
        $pdf->Cell(9,  $h, $p["cantidad"],                                    0, 0, 'C');
        $pdf->Cell(17, $h, 'S/ ' . number_format($p["precio_unitario_articulo"], 2), 0, 0, 'C');
        $pdf->Cell(16, $h, 'S/ ' . number_format($p["sub_total"], 2),         0, 1, 'R');
    }

    $pdf->Ln(1);
    $pdf->Cell(74, 3, str_repeat('-', 46), 0, 1, 'C');
    $pdf->Ln(1);

    // Estado / Descuento
    $pdf->SetFont('Arial', 'B', 6.5);
    $pdf->Cell(25, 3.5, 'Estado:',    0, 0, 'L');
    $pdf->SetFont('Arial', '', 6.5);
    $pdf->Cell(49, 3.5, utf8_decode($datosVenta["estado_pago"]), 0, 1, 'L');
    $pdf->SetFont('Arial', 'B', 6.5);
    $pdf->Cell(25, 3.5, 'Descuento:', 0, 0, 'L');
    $pdf->SetFont('Arial', '', 6.5);
    $pdf->Cell(49, 3.5, 'S/ ' . number_format($datosVenta["descuento"], 2), 0, 1, 'L');
    $pdf->Ln(1);

    // Formas de pago
    $pdf->SetFont('Arial', 'B', 6.5);
    $pdf->Cell(40, 3.5, 'FORMA DE PAGO', 0, 0, 'L');
    $pdf->Cell(34, 3.5, 'MONTO',         0, 1, 'R');
    $pdf->SetFont('Arial', '', 6.5);
    foreach ($pagos as $x) {
        $pdf->Cell(40, 3.5, utf8_decode($x["forma_pago"]), 0, 0, 'L');
        $pdf->Cell(34, 3.5, 'S/ ' . number_format($x["monto"], 2), 0, 1, 'R');
    }
    $pdf->Ln(1);
    $pdf->Cell(74, 3, str_repeat('-', 46), 0, 1, 'C');
    $pdf->Ln(1);

    // Totales
    $impuestos = fnCalcularImpuestos($productos, (float)$datosVenta["total"]);
    $pdf->SetFont('Arial', '', 6.5);
    $pdf->Cell(50, 3.5, 'OP. GRAVADA',    0, 0, 'L');
    $pdf->Cell(24, 3.5, 'S/ ' . number_format($impuestos['subtotal'], 2), 0, 1, 'R');
    $pdf->Cell(50, 3.5, 'IGV 18%',        0, 0, 'L');
    $pdf->Cell(24, 3.5, 'S/ ' . number_format($impuestos['igv'], 2),      0, 1, 'R');
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(50, 5, 'IMPORTE TOTAL',    0, 0, 'L');
    $pdf->Cell(24, 5, 'S/ ' . number_format($datosVenta["total"], 2),     0, 1, 'R');
    $pdf->Ln(1);

    // Total letras
    $pdf->SetFont('Arial', '', 6);
    $total_letras = 'SON: ' . strtoupper(number_format($datosVenta["total"], 2) . ' /100 SOLES');
    $pdf->MultiCell(74, 3, utf8_decode($total_letras), 0, 'C');
    $pdf->Ln(1);

    $pdf->SetFont('Arial', 'B', 6);
    $pdf->Cell(74, 3.5, 'ATENDIDO POR: ' . utf8_decode($datosVenta["usuario_final"]), 0, 1, 'C');
    $pdf->Ln(1);
    $pdf->SetFont('Arial', '', 6);
    $pdf->MultiCell(74, 3, utf8_decode('Representación impresa de la ' . $datosVenta["tipo_comprobante"] . ' ELECTRÓNICA'), 0, 'C');
    $pdf->MultiCell(74, 3, utf8_decode('Gracias por su preferencia'), 0, 'C');
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 6);
    $pdf->Cell(74, 3, utf8_decode($datoEmisor["nombre_comercial"]), 0, 1, 'C');
    $pdf->Cell(74, 3, utf8_decode('DESARROLLADO POR CARACOL SOFT'), 0, 1, 'C');
    $pdf->Ln(4);

    ob_clean();
    $pdf->Output('I', 'ticket_venta.pdf');
}


// ═══════════════════════════════════════════════════════════════
// FORMATO 2: A4 (210 × 297 mm) — Factura / Boleta formal
// ═══════════════════════════════════════════════════════════════
function fnGenerarA4(int $idVenta): void
{
    [$datosVenta, $datoEmisor, $productos, $pagos] = fnCargarDatosVenta($idVenta);
    $impuestos = fnCalcularImpuestos($productos, (float)$datosVenta["total"]);

    ob_clean();
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->SetMargins(15, 15, 15);
    $pdf->AddPage();

    $ancho = 180; // ancho útil

    // ── CABECERA: Logo + Emisor + Cuadro comprobante ──────────
    $logoPath = fnResolverLogo($datoEmisor);
    $xLogo    = 15;
    $yLogo    = 15;
    if ($logoPath) {
        $pdf->Image($logoPath, $xLogo, $yLogo, 35);
    }

    // Datos emisor (columna central)
    $pdf->SetXY(55, 15);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(80, 6, utf8_decode($datoEmisor["razon_social"]), 0, 1, 'C');
    $pdf->SetX(55);
    $pdf->SetFont('Arial', '', 8);
    $pdf->MultiCell(80, 4, utf8_decode($datoEmisor["direccion"]), 0, 'C');
    $pdf->SetX(55);
    $pdf->Cell(80, 4, 'RUC: ' . $datoEmisor["ruc"], 0, 1, 'C');

    // Cuadro comprobante (columna derecha)
    $pdf->SetXY(140, 15);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(55, 8, utf8_decode($datosVenta["tipo_comprobante"]), 1, 1, 'C', true);
    $pdf->SetX(140);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(55, 9, utf8_decode('ELECTRÓNICA'), 1, 1, 'C');    $pdf->SetX(140);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(55, 8, $datosVenta["codigo_tiket"], 1, 1, 'C');

    $pdf->Ln(5);

    // ── DATOS CLIENTE ─────────────────────────────────────────
    $pdf->SetFillColor(50, 50, 50);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell($ancho, 5.5, '  DATOS DEL CLIENTE', 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetFillColor(245, 245, 245);

    // Fila 1
    $pdf->Cell(28, 5.5, 'Cliente:', 'LT', 0, 'L', true);
    $pdf->Cell(92, 5.5, utf8_decode($datosVenta["cliente"]), 'T', 0, 'L');
    $pdf->Cell(28, 5.5, 'DNI / RUC:', 'T', 0, 'L', true);
    $pdf->Cell(32, 5.5, $datosVenta["numero_doc_cliente"], 'RT', 1, 'L');
    // Fila 2
    $pdf->Cell(28, 5.5, 'Fecha:', 'LB', 0, 'L', true);
    $pdf->Cell(92, 5.5, $datosVenta["fecha"] . '  ' . $datosVenta["hora"], 'B', 0, 'L');
    $pdf->Cell(28, 5.5, 'Estado:', 'B', 0, 'L', true);
    $pdf->Cell(32, 5.5, utf8_decode($datosVenta["estado_pago"]), 'RB', 1, 'L');

    $pdf->Ln(4);

    // ── TABLA DE PRODUCTOS ────────────────────────────────────
    $pdf->SetFillColor(50, 50, 50);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 8);
    $colDesc  = 75;
    $colCant  = 20;
    $colUni   = 20;
    $colPU    = 30;
    $colTotal = 35;
$pdf->Cell($colDesc,  6, utf8_decode('DESCRIPCIÓN'),     'B', 0, 'C', true);
    $pdf->Cell($colCant,  6, utf8_decode('CANT.'),            'B', 0, 'C', true);
    $pdf->Cell($colUni,   6, utf8_decode('UND.'),             'B', 0, 'C', true);
    $pdf->Cell($colPU,    6, utf8_decode('PRECIO UNIT.'),     'B', 0, 'C', true);
    $pdf->Cell($colTotal, 6, utf8_decode('TOTAL'),            'B', 1, 'C', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 8);

    $fill = false;
    foreach ($productos as $i => $p) {
        $pdf->SetFillColor($fill ? 248 : 255, $fill ? 248 : 255, $fill ? 248 : 255);
        $yIni = $pdf->GetY();
        $pdf->MultiCell($colDesc, 5, utf8_decode($p["descripcion_2"]), 0, 'L', $fill);
        $yFin = $pdf->GetY();
        $h = $yFin - $yIni;
        $pdf->SetY($yIni); $pdf->SetX(15 + $colDesc);
        $pdf->Cell($colCant,  $h, $p["cantidad"],                                         0, 0, 'C', $fill);
        $pdf->Cell($colUni,   $h, $p["unidad"] ?? 'UND',                                  0, 0, 'C', $fill);
        $pdf->Cell($colPU,    $h, 'S/ ' . number_format($p["precio_unitario_articulo"], 2), 0, 0, 'R', $fill);
        $pdf->Cell($colTotal, $h, 'S/ ' . number_format($p["sub_total"], 2),               0, 1, 'R', $fill);
        $fill = !$fill;
    }

    $pdf->SetDrawColor(200, 200, 200);
    $pdf->Cell($ancho, 0.2, '', 'T', 1);
    $pdf->Ln(3);

    // ── SECCIÓN INFERIOR: Pago + Totales ──────────────────────
    $xLeft  = 15;
    $xRight = 120;
    $yBase  = $pdf->GetY();

    // Forma de pago (izquierda)
    $pdf->SetXY($xLeft, $yBase);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(50, 50, 50);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(95, 5.5, '  FORMA DE PAGO', 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 8);
    foreach ($pagos as $x) {
        $pdf->SetX($xLeft);
        $pdf->Cell(60, 5, utf8_decode($x["forma_pago"]), 'LB', 0, 'L');
        $pdf->Cell(35, 5, 'S/ ' . number_format($x["monto"], 2), 'RB', 1, 'R');
    }

    // Totales (derecha)
    $pdf->SetXY($xRight, $yBase);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(50, 50, 50);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(75, 5.5, '  RESUMEN', 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 8);

    $filas = [
        ['OP. GRAVADA',  'S/ ' . number_format($impuestos['subtotal'], 2)],
        ['IGV 18%',      'S/ ' . number_format($impuestos['igv'], 2)],
        ['DESCUENTO',    'S/ ' . number_format($datosVenta["descuento"], 2)],
    ];
    foreach ($filas as $f) {
        $pdf->SetX($xRight);
        $pdf->Cell(45, 5, $f[0], 'LB', 0, 'L');
        $pdf->Cell(30, 5, $f[1], 'RB', 1, 'R');
    }
    // Total destacado
    $pdf->SetX($xRight);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor(30, 30, 30);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(45, 7, utf8_decode('IMPORTE TOTAL'), 'LB', 0, 'L', true);
    $pdf->Cell(30, 7, 'S/ ' . number_format($datosVenta["total"], 2), 'RB', 1, 'R', true);
    $pdf->SetTextColor(0, 0, 0);

    $pdf->Ln(5);

    // ── PIE ───────────────────────────────────────────────────
    $total_letras = 'SON: ' . strtoupper(number_format($datosVenta["total"], 2) . ' /100 SOLES');
    $pdf->SetFont('Arial', 'I', 7.5);
    $pdf->Cell($ancho, 4, $total_letras, 0, 1, 'C');
    $pdf->Ln(1);
    $pdf->SetFont('Arial', '', 7);
    $pdf->MultiCell($ancho, 3.5, utf8_decode('Representación impresa de la ' . $datosVenta["tipo_comprobante"] . ' ELECTRÓNICA. Puede consultar este comprobante en: https://e-consulta.sunat.gob.pe/'), 0, 'C');
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell($ancho, 4, utf8_decode($datoEmisor["nombre_comercial"] . '  |  Atendido por: ' . $datosVenta["usuario_final"] . '  |  DESARROLLADO POR CARACOL SOFT'), 0, 1, 'C');

    ob_clean();
    $pdf->Output('I', 'comprobante_a4.pdf');
}


// ═══════════════════════════════════════════════════════════════
// FORMATO 3: A5 (148 × 210 mm) — Medio oficio
// ═══════════════════════════════════════════════════════════════
function fnGenerarA5(int $idVenta): void
{
    [$datosVenta, $datoEmisor, $productos, $pagos] = fnCargarDatosVenta($idVenta);
    $impuestos = fnCalcularImpuestos($productos, (float)$datosVenta["total"]);

    ob_clean();
    $pdf = new FPDF('P', 'mm', 'A5');
    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();

    $ancho = 128; // ancho útil (148 - 20)

    // ── CABECERA ──────────────────────────────────────────────
    $logoPath = fnResolverLogo($datoEmisor);
    if ($logoPath) {
        $pdf->Image($logoPath, 10, 10, 22);
    }

    $pdf->SetXY(36, 10);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(55, 5, utf8_decode($datoEmisor["razon_social"]), 0, 1, 'C');
    $pdf->SetX(36);
    $pdf->SetFont('Arial', '', 7);
    $pdf->MultiCell(55, 3.5, utf8_decode($datoEmisor["direccion"]), 0, 'C');
    $pdf->SetX(36);
    $pdf->Cell(55, 3.5, 'RUC: ' . $datoEmisor["ruc"], 0, 1, 'C');

    // Cuadro comprobante (derecha)
    $pdf->SetXY(96, 10);
    $pdf->SetFont('Arial', 'B', 7.5);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(42, 6, utf8_decode($datosVenta["tipo_comprobante"]), 1, 1, 'C', true);
    $pdf->SetX(96);
    $pdf->Cell(42, 5.5, utf8_decode('ELECTRÓNICA'), 1, 1, 'C');
    $pdf->SetX(96);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(42, 5.5, utf8_decode($datosVenta["codigo_tiket"]), 1, 1, 'C');

    $pdf->Ln(3);

    // ── DATOS CLIENTE ─────────────────────────────────────────
    $pdf->SetFillColor(50, 50, 50);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell($ancho, 5, utf8_decode('  DATOS DEL CLIENTE'), 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 7);
    $pdf->SetFillColor(245, 245, 245);
    $pdf->Cell(22, 5, utf8_decode('Cliente:'), 'LTB', 0, 'L', true);
    $pdf->Cell(68, 5, utf8_decode($datosVenta["cliente"]), 'TB', 0, 'L');
    $pdf->Cell(18, 5, utf8_decode('DNI/RUC:'), 'TB', 0, 'L', true);
    $pdf->Cell(20, 5, utf8_decode($datosVenta["numero_doc_cliente"]), 'RTB', 1, 'L');
    $pdf->Cell(22, 5, utf8_decode('Fecha:'), 'LB', 0, 'L', true);
    $pdf->Cell(68, 5, utf8_decode($datosVenta["fecha"] . '  ' . $datosVenta["hora"]), 'B', 0, 'L');
    $pdf->Cell(18, 5, utf8_decode('Estado:'), 'B', 0, 'L', true);
    $pdf->Cell(20, 5, utf8_decode($datosVenta["estado_pago"]), 'RB', 1, 'L');

    $pdf->Ln(3);

    // ── PRODUCTOS ─────────────────────────────────────────────
    $pdf->SetFillColor(50, 50, 50);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 7);
    $cD = 52; $cC = 14; $cU = 14; $cP = 24; $cT = 24;
    $pdf->Cell($cD, 5, utf8_decode('DESCRIPCIÓN'), 'B', 0, 'C', true);
    $pdf->Cell($cC, 5, utf8_decode('CANT.'),       'B', 0, 'C', true);
    $pdf->Cell($cU, 5, utf8_decode('UND.'),        'B', 0, 'C', true);
    $pdf->Cell($cP, 5, utf8_decode('P.UNIT.'),     'B', 0, 'C', true);
    $pdf->Cell($cT, 5, utf8_decode('TOTAL'),       'B', 1, 'C', true);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 7);

    $fill = false;
    foreach ($productos as $p) {
        $pdf->SetFillColor($fill ? 248 : 255, $fill ? 248 : 255, $fill ? 248 : 255);
        $yIni = $pdf->GetY();
        $pdf->MultiCell($cD, 4.5, utf8_decode($p["descripcion_2"]), 0, 'L', $fill);
        $yFin = $pdf->GetY(); $h = $yFin - $yIni;
        $pdf->SetY($yIni); $pdf->SetX(10 + $cD);
        $pdf->Cell($cC, $h, $p["cantidad"],                                               0, 0, 'C', $fill);
        $pdf->Cell($cU, $h, $p["unidad"] ?? 'UND',                                        0, 0, 'C', $fill);
        $pdf->Cell($cP, $h, 'S/ ' . number_format($p["precio_unitario_articulo"], 2),     0, 0, 'R', $fill);
        $pdf->Cell($cT, $h, 'S/ ' . number_format($p["sub_total"], 2),                   0, 1, 'R', $fill);
        $fill = !$fill;
    }

    $pdf->SetDrawColor(180, 180, 180);
    $pdf->Cell($ancho, 0.2, '', 'T', 1);
    $pdf->Ln(2);

    // ── TOTALES + PAGOS ───────────────────────────────────────
    $xLeft  = 10;
    $xRight = 80;
    $yBase  = $pdf->GetY();

    // Pagos
    $pdf->SetXY($xLeft, $yBase);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->SetFillColor(50, 50, 50); $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(65, 5, '  FORMA DE PAGO', 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0); $pdf->SetFont('Arial', '', 7);
    foreach ($pagos as $x) {
        $pdf->SetX($xLeft);
        $pdf->Cell(42, 4.5, utf8_decode($x["forma_pago"]), 'LB', 0, 'L');
        $pdf->Cell(23, 4.5, 'S/ ' . number_format($x["monto"], 2), 'RB', 1, 'R');
    }

    // Totales
    $pdf->SetXY($xRight, $yBase);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->SetFillColor(50, 50, 50); $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(58, 5, '  RESUMEN', 0, 1, 'L', true);
    $pdf->SetTextColor(0, 0, 0); $pdf->SetFont('Arial', '', 7);
    $rows = [
        ['OP. GRAVADA', 'S/ ' . number_format($impuestos['subtotal'], 2)],
        ['IGV 18%',     'S/ ' . number_format($impuestos['igv'], 2)],
        ['DESCUENTO',   'S/ ' . number_format($datosVenta["descuento"], 2)],
    ];
    foreach ($rows as $r) {
        $pdf->SetX($xRight);
        $pdf->Cell(33, 4.5, $r[0], 'LB', 0, 'L');
        $pdf->Cell(25, 4.5, $r[1], 'RB', 1, 'R');
    }
    $pdf->SetX($xRight);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(30, 30, 30); $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(33, 6, utf8_decode('TOTAL'), 'LB', 0, 'L', true);
    $pdf->Cell(25, 6, 'S/ ' . number_format($datosVenta["total"], 2), 'RB', 1, 'R', true);
    $pdf->SetTextColor(0, 0, 0);

    $pdf->Ln(4);
    $pdf->SetFont('Arial', 'I', 6.5);
    $pdf->Cell($ancho, 3.5, utf8_decode('SON: ' . strtoupper(number_format($datosVenta["total"], 2) . ' /100 SOLES')), 0, 1, 'C');
    $pdf->SetFont('Arial', '', 6);
    $pdf->MultiCell($ancho, 3, utf8_decode('Representación impresa de la ' . $datosVenta["tipo_comprobante"] . ' ELECTRÓNICA'), 0, 'C');
    $pdf->SetFont('Arial', 'B', 6);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Cell($ancho, 3.5, utf8_decode($datoEmisor["nombre_comercial"] . '  |  CARACOL SOFT'), 0, 1, 'C');

    ob_clean();
    $pdf->Output('I', 'comprobante_a5.pdf');
}


// ═══════════════════════════════════════════════════════════════
// FORMATO 4: PANTALLA — HTML responsivo + QR (WhatsApp/Email)
// ═══════════════════════════════════════════════════════════════
function fnGenerarPantalla(int $idVenta): void
{
    [$datosVenta, $datoEmisor, $productos, $pagos] = fnCargarDatosVenta($idVenta);
    $impuestos = fnCalcularImpuestos($productos, (float)$datosVenta["total"]);

    $razonSocial    = htmlspecialchars($datoEmisor["razon_social"]);
    $ruc            = htmlspecialchars($datoEmisor["ruc"]);
    $direccion      = htmlspecialchars($datoEmisor["direccion"]);
    $nombreComercial= htmlspecialchars($datoEmisor["nombre_comercial"]);
    $tipoComp       = htmlspecialchars($datosVenta["tipo_comprobante"]);
    $codigoTicket   = htmlspecialchars($datosVenta["codigo_tiket"]);
    $cliente        = htmlspecialchars($datosVenta["cliente"]);
    $docCliente     = htmlspecialchars($datosVenta["numero_doc_cliente"]);
    $fecha          = htmlspecialchars($datosVenta["fecha"] . ' ' . $datosVenta["hora"]);
    $estadoPago     = htmlspecialchars($datosVenta["estado_pago"]);
    $total          = number_format($datosVenta["total"], 2);
    $totalReal      = number_format($datosVenta["monto_venta_final"], 2);
    $descuento      = number_format($datosVenta["descuento"], 2);
    $subtotal       = number_format($impuestos['subtotal'], 2);
    $igv            = number_format($impuestos['igv'], 2);
    $usuarioFinal   = htmlspecialchars($datosVenta["usuario_final"]);

    // QR: apunta a consulta SUNAT (si tienes la URL real, reemplázala)
    $urlQR = urlencode("https://e-consulta.sunat.gob.pe/?ruc={$ruc}&tipo={$tipoComp}&numero={$codigoTicket}");
    $qrSrc = "https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={$urlQR}";

    // Logo base64 si existe
    $logoTag = '';
    $logoPath = fnResolverLogo($datoEmisor);
    if ($logoPath) {
        $ext     = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
        $mime    = ($ext === 'png') ? 'image/png' : 'image/jpeg';
        $logoB64 = base64_encode(file_get_contents($logoPath));
        $logoTag = "<img src='data:{$mime};base64,{$logoB64}' alt='Logo' class='logo'>";
    }

    // Filas de productos
    $filasProductos = '';
    foreach ($productos as $i => $p) {
        $desc  = htmlspecialchars($p["descripcion_2"]);
        $cant  = htmlspecialchars($p["cantidad"]);
        $pu    = 'S/ ' . number_format($p["precio_unitario_articulo"], 2);
        $sub   = 'S/ ' . number_format($p["sub_total"], 2);
        $cls   = ($i % 2 === 0) ? '' : ' class="alt"';
        $filasProductos .= "<tr{$cls}><td>{$desc}</td><td class='center'>{$cant}</td><td class='right'>{$pu}</td><td class='right'>{$sub}</td></tr>\n";
    }

    // Filas de pagos
    $filasPagos = '';
    foreach ($pagos as $x) {
        $fp  = htmlspecialchars($x["forma_pago"]);
        $mto = 'S/ ' . number_format($x["monto"], 2);
        $filasPagos .= "<tr><td>{$fp}</td><td class='right'>{$mto}</td></tr>\n";
    }

    ob_clean();
    header('Content-Type: text/html; charset=utf-8');

    echo <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{$tipoComp} {$codigoTicket}</title>
<style>
  :root {
    --bg:       #f2f4f7;
    --card:     #ffffff;
    --dark:     #1a1a2e;
    --accent:   #e63946;
    --text:     #2d2d2d;
    --muted:    #6b7280;
    --border:   #e5e7eb;
    --alt-row:  #f8f9fb;
    --radius:   12px;
    --shadow:   0 4px 24px rgba(0,0,0,.10);
  }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    background: var(--bg);
    font-family: 'Segoe UI', system-ui, sans-serif;
    color: var(--text);
    padding: 24px 16px 48px;
    min-height: 100vh;
  }
  .comprobante {
    max-width: 620px;
    margin: 0 auto;
    background: var(--card);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
  }

  /* HEADER */
  .header {
    background: var(--dark);
    color: #fff;
    padding: 24px 28px;
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
  }
  .logo { width: 60px; height: 60px; object-fit: contain; border-radius: 8px; background:#fff; padding:4px; }
  .emisor-info { flex: 1; min-width: 160px; }
  .emisor-info h1 { font-size: 15px; font-weight: 700; line-height: 1.3; }
  .emisor-info p  { font-size: 11.5px; color: #cbd5e1; margin-top: 3px; line-height: 1.4; }
  .badge {
    background: var(--accent);
    border-radius: 8px;
    padding: 10px 16px;
    text-align: center;
    min-width: 140px;
  }
  .badge .tipo  { font-size: 10px; font-weight: 600; letter-spacing: .6px; text-transform: uppercase; opacity: .85; }
  .badge .num   { font-size: 13px; font-weight: 700; margin-top: 4px; letter-spacing: .5px; }

  /* SECCIONES */
  .section { padding: 20px 28px; border-bottom: 1px solid var(--border); }
  .section:last-child { border-bottom: none; }
  .section-title {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: var(--muted);
    margin-bottom: 12px;
  }
  .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
  .field label { font-size: 10px; color: var(--muted); display: block; margin-bottom: 2px; }
  .field span  { font-size: 13px; font-weight: 500; }

  /* TABLA */
  table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
  thead th {
    background: var(--dark);
    color: #fff;
    padding: 8px 10px;
    text-align: left;
    font-size: 10.5px;
    font-weight: 600;
    letter-spacing: .3px;
  }
  tbody td { padding: 8px 10px; border-bottom: 1px solid var(--border); vertical-align: top; }
  tbody tr.alt td { background: var(--alt-row); }
  .center { text-align: center; }
  .right  { text-align: right; }

  /* TOTALES */
  .totales-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start; }
  .total-row { display: flex; justify-content: space-between; font-size: 12.5px; margin-bottom: 8px; }
  .total-row.main {
    background: var(--dark);
    color: #fff;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 14px;
    font-weight: 700;
    margin-top: 4px;
  }

  /* QR */
  .qr-block { text-align: center; }
  .qr-block img { width: 120px; height: 120px; border: 2px solid var(--border); border-radius: 8px; }
  .qr-block p { font-size: 10px; color: var(--muted); margin-top: 6px; line-height: 1.5; }

  /* BOTONES */
  .acciones {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    padding: 20px 28px;
    background: var(--alt-row);
    border-top: 1px solid var(--border);
  }
  .btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    border: none;
    transition: opacity .15s;
  }
  .btn:hover { opacity: .85; }
  .btn-primary { background: var(--dark); color: #fff; }
  .btn-wa      { background: #25d366; color: #fff; }
  .btn-outline { background: transparent; border: 1.5px solid var(--border); color: var(--text); }

  /* PIE */
  .pie {
    background: var(--dark);
    color: #9ca3af;
    text-align: center;
    font-size: 10px;
    padding: 14px 20px;
    line-height: 1.6;
  }

  @media print {
    body { background: #fff; padding: 0; }
    .comprobante { box-shadow: none; border-radius: 0; }
    .acciones { display: none; }
  }
  @media (max-width: 480px) {
    .header { flex-direction: column; text-align: center; }
    .grid-2 { grid-template-columns: 1fr; }
    .totales-grid { grid-template-columns: 1fr; }
    .badge { width: 100%; }
  }
</style>
</head>
<body>
<div class="comprobante">

  <!-- ENCABEZADO -->
  <div class="header">
    {$logoTag}
    <div class="emisor-info">
      <h1>{$razonSocial}</h1>
      <p>RUC: {$ruc}</p>
      <p>{$direccion}</p>
    </div>
    <div class="badge">
      <div class="tipo">{$tipoComp}</div>
      <div class="num">{$codigoTicket}</div>
    </div>
  </div>

  <!-- DATOS CLIENTE -->
  <div class="section">
    <div class="section-title">Datos del cliente</div>
    <div class="grid-2">
      <div class="field"><label>Cliente</label><span>{$cliente}</span></div>
      <div class="field"><label>DNI / RUC</label><span>{$docCliente}</span></div>
      <div class="field"><label>Fecha</label><span>{$fecha}</span></div>
      <div class="field"><label>Estado</label><span>{$estadoPago}</span></div>
    </div>
  </div>

  <!-- DETALLE PRODUCTOS -->
  <div class="section">
    <div class="section-title">Detalle de productos</div>
    <table>
      <thead><tr>
        <th>Descripción</th>
        <th class="center">Cant.</th>
        <th class="right">P. Unit.</th>
        <th class="right">Total</th>
      </tr></thead>
      <tbody>{$filasProductos}</tbody>
    </table>
  </div>

  <!-- TOTALES + QR -->
  <div class="section">
    <div class="totales-grid">
      <div>
        <div class="section-title">Forma de pago</div>
        <table>
          <tbody>{$filasPagos}</tbody>
        </table>
        <br>
        <div class="section-title">Resumen</div>
        <div class="total-row"><span>Op. Gravada</span><span>S/ {$subtotal}</span></div>
        <div class="total-row"><span>IGV 18%</span><span>S/ {$igv}</span></div>
        <div class="total-row"><span>Descuento</span><span>S/ {$descuento}</span></div>
        <div class="total-row main"><span>TOTAL</span><span>S/ {$total}</span></div>
      </div>
      <div class="qr-block">
        <div class="section-title" style="text-align:center">Validar en SUNAT</div>
        <img src="{$qrSrc}" alt="Código QR SUNAT" loading="lazy">
        <p>Escanea para consultar<br>este comprobante</p>
      </div>
    </div>
  </div>

  <!-- BOTONES DE ACCIÓN -->
  <div class="acciones">
    <a href="?id={$idVenta}&token=TOKEN&formato=a4" class="btn btn-primary" target="_blank">
      ⬇ Descargar PDF A4
    </a>
    <a href="?id={$idVenta}&token=TOKEN&formato=ticket" class="btn btn-outline" target="_blank">
      🖨 Imprimir Ticket
    </a>
    <button onclick="window.print()" class="btn btn-outline">🖨 Imprimir pantalla</button>
    <a href="https://wa.me/?text=Tu%20comprobante%20electr%C3%B3nico%3A%20" class="btn btn-wa" target="_blank">
      WhatsApp
    </a>
  </div>

  <!-- PIE -->
  <div class="pie">
    Representación impresa de la {$tipoComp} ELECTRÓNICA<br>
    {$nombreComercial} &nbsp;|&nbsp; Atendido por: {$usuarioFinal} &nbsp;|&nbsp; DESARROLLADO POR CARACOL SOFT
  </div>

</div>
</body>
</html>
HTML;
    exit;
}