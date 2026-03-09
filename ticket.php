<?php
ob_start(); // captura cualquier output/warning antes de responder

// ═══════════════════════════════════════════════════════════════
// CLAVE SECRETA — cámbiala por algo único tuyo, nunca la publiques
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
// ROUTER — decide qué hacer según los parámetros recibidos
// ═══════════════════════════════════════════════════════════════
$accion  = $_GET['accion'] ?? '';
$idVenta = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$token   = isset($_GET['token']) ? trim($_GET['token']) : '';

// ── accion=token → JSON con URL firmada (llamado desde JS) ───
if ($accion === 'token') {
    ob_clean(); // descartar cualquier warning previo
    header('Content-Type: application/json');
    if (!$idVenta) {
        http_response_code(400);
        echo json_encode(['error' => 'ID inválido']);
        exit;
    }
    echo json_encode([
        'url' => 'ticket.php?id=' . $idVenta . '&token=' . ticket_token($idVenta)
    ]);
    exit;
}

// ── accion=xml → devuelve el XML de un comprobante ───────────
if ($accion === 'xml') {
    ob_clean();
    header('Content-Type: application/json');

    $nombrexml = isset($_GET['nombrexml']) ? basename($_GET['nombrexml']) : '';
    $sucursal  = isset($_GET['sucursal'])  ? (int)$_GET['sucursal']       : 1;

    if (!$nombrexml) {
        echo json_encode(['error' => 'Sin nombre de archivo']);
        exit;
    }

    // Construir ruta desde raíz del proyecto
    $base    = dirname(__FILE__) . "/sucursales/{$sucursal}/xml/";
    $rutaXML = $base . $nombrexml . '.XML';

    // Intentar también en minúsculas
    if (!file_exists($rutaXML)) {
        $rutaXML = $base . $nombrexml . '.xml';
    }

    if (!file_exists($rutaXML)) {
        echo json_encode(['error' => "Archivo no encontrado: {$nombrexml}.XML"]);
        exit;
    }

    $xmlContent = file_get_contents($rutaXML);

    // Formatear el XML con indentación
    $dom = new DOMDocument('1.0', 'utf-8');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput       = true;
    @$dom->loadXML($xmlContent);
    $xmlFormateado = $dom->saveXML();

    echo json_encode([
        'xml'       => $xmlFormateado,
        'nombrexml' => $nombrexml,
    ]);
    exit;
}

// ── Sin accion → generar PDF (requiere token válido) ─────────
require("logica/clssConsultas.php");
require('fpdf/fpdf.php');

if (!$idVenta || !$token) {
    http_response_code(400);
    die('Solicitud inválida.');
}
if (!ticket_token_valido($idVenta, $token)) {
    http_response_code(403);
    die('Acceso no autorizado.');
}

fnGenerarTicket($idVenta);


// ═══════════════════════════════════════════════════════════════
// GENERACIÓN DEL PDF
// ═══════════════════════════════════════════════════════════════
function fnGenerarTicket($idVenta): void
{
    $datosprueba = fnUltimaVentaPorIdVenta($idVenta)[0];
    $sucursal_id = $datosprueba["sucursal_id"] ?? null;
    $datoEmisor  = fnListadoDeEmisor($sucursal_id)[0] ?? null;

    if (!$datoEmisor) {
        error_log("No se encontraron datos del emisor para sucursal_id: " . $sucursal_id);
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
        "js_detalle_forma_pago" => $datosprueba["js_detalle_forma_pago"]
    ];

    $productos = json_decode($datosVenta["js_detalle"], true);
    $pagos     = json_decode($datosVenta["js_detalle_forma_pago"], true);

    ob_clean();

    $pdf = new FPDF('P', 'mm', array(80, 200));
    $pdf->AddPage();

    // Logo
    $logoPath = null;
    if (!empty($datoEmisor["ruta_logo"]) && file_exists($datoEmisor["ruta_logo"])) {
        $logoPath = $datoEmisor["ruta_logo"];
    } elseif (file_exists('logica/logo.jpeg')) {
        $logoPath = 'logica/logo.jpeg';
    }

    if ($logoPath) {
        $logoWidth = 20;
        $centerX   = (80 - $logoWidth) / 2;
        $pdf->Image($logoPath, $centerX, 5, $logoWidth);
        $pdf->Ln(20);
    } else {
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(60, 4, 'Logo no disponible', 0, 1, 'C');
        $pdf->Ln(5);
    }

    // Emisor
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(60, 4, utf8_decode($datoEmisor["razon_social"]), 0, 1, 'C');
    $pdf->Cell(60, 4, "RUC: " . $datoEmisor["ruc"], 0, 1, 'C');
    $pdf->SetFont('Arial', '', 6);
    $pdf->MultiCell(60, 4, utf8_decode($datoEmisor["direccion"]), 0, 'C');

    // Tipo de comprobante
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(60, 4, utf8_decode($datosprueba["tipo_comprobante"]) . ' DE VENTA ELECTRONICA', 0, 1, 'C');
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(60, 4, $datosVenta["codigo_tiket"], 0, 1, 'C');
    $pdf->Ln(1);

    // Cliente
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(60, 4, 'Cliente: '  . utf8_decode($datosVenta["cliente"]), 0, 1, 'L');
    $pdf->Cell(60, 4, 'DNI/RUC: ' . $datosVenta["numero_doc_cliente"], 0, 1, 'L');
    $pdf->Cell(60, 4, 'Fecha: '   . $datosVenta["fecha"] . ' ' . $datosVenta["hora"], 0, 1, 'L');
    $pdf->Ln(1);

    $pdf->Cell(60, 3, str_repeat('_', 25), 0, 1, 'C');
    $pdf->Ln(1);

    // Productos
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(30, 3, 'DESCRIPCION', 0, 0, 'L');
    $pdf->Cell(8,  3, 'CANT.',       0, 0, 'C');
    $pdf->Cell(12, 3, 'P.U',         0, 0, 'C');
    $pdf->Cell(10, 3, 'TOTAL',       0, 1, 'C');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Ln(1);

    foreach ($productos as $producto) {
        $yInicial   = $pdf->GetY();
        $pdf->MultiCell(30, 3, utf8_decode($producto["descripcion_2"]), 0, 'L');
        $yFinal     = $pdf->GetY();
        $alturaFila = $yFinal - $yInicial;
        $pdf->SetY($yInicial);
        $pdf->SetX(40);
        $pdf->Cell(8,  $alturaFila, $producto["cantidad"], 0, 0, 'C');
        $pdf->Cell(12, $alturaFila, 'S/ ' . number_format($producto["precio_unitario_articulo"], 2), 0, 0, 'C');
        $pdf->Cell(10, $alturaFila, 'S/ ' . number_format($producto["sub_total"], 2), 0, 1, 'C');
    }

    $pdf->Ln(1);
    $pdf->Cell(60, 3, str_repeat('_', 25), 0, 1, 'C');
    $pdf->Ln(1);

    // Estado y descuento
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(20, 3, 'Estado:', 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(15, 3, utf8_decode($datosVenta["estado_pago"]), 0, 1, 'L');
    $pdf->Ln(1);

    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(20, 3, 'Descuento:', 0, 0, 'L');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(15, 3, "S/ " . number_format($datosVenta["descuento"], 2), 0, 1, 'L');
    $pdf->Ln(1);

    // Formas de pago
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(30, 3, 'Forma de Pago', 0, 0, 'L');
    $pdf->Cell(20, 3, 'Monto',         0, 1, 'R');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Ln(1);
    foreach ($pagos as $x) {
        $pdf->Cell(30, 3, utf8_decode($x["forma_pago"]), 0, 0, 'L');
        $pdf->Cell(20, 3, 'S/ ' . number_format($x["monto"], 2), 0, 1, 'R');
    }

    $pdf->Ln(1);
    $pdf->Cell(60, 3, str_repeat('_', 25), 0, 1, 'C');
    $pdf->Ln(1);

    // Totales
    $pdf->SetFont('Arial', 'B', 5);
    $pdf->Cell(60, 4, 'TOTAL DE VENTA: S/ ' . number_format($datosVenta["total"], 2), 0, 1, 'C');
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(60, 4, 'TOTAL DE VENTA REAL: S/ ' . number_format($datosVenta["monto_venta_final"], 2), 0, 1, 'C');
    $pdf->Ln(1);

    $total_letras = strtoupper(number_format($datosVenta["total"], 2) . " /100 PEN");
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(60, 3, $total_letras, 0, 1, 'C');
    $pdf->Ln(1);

    $pdf->SetFont('Arial', 'B', 6);
    $pdf->Cell(60, 3, 'ATENDIDO POR: ' . utf8_decode($datosVenta["usuario_final"]), 0, 1, 'C');
    $pdf->Ln(1);

    $pdf->SetFont('Arial', '', 7);
    $pdf->MultiCell(60, 3, utf8_decode('Representacion Impresa de la ' . $datosprueba["tipo_comprobante"] . ' DE VENTA ELECTRONICA'), 0, 'C');
    $pdf->MultiCell(60, 3, 'Gracias por su preferencia', 0, 'C');

    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(60, 3, ' ', 0, 1, 'C');
    $pdf->Cell(60, 3, utf8_decode($datoEmisor["nombre_comercial"]), 0, 1, 'C');
    $pdf->Cell(60, 3, 'DESARROLLADO POR CARACOL SOFT', 0, 1, 'C');
    $pdf->Ln(4);

    ob_clean();
    $pdf->Output('I', 'ticket_venta.pdf');
}