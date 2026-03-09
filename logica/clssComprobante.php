<?php
/**
 * clssComprobante.php
 * Handler AJAX para declarar comprobantes a SUNAT.
 * Usa clssSunat.php directamente — sin llamadas a API externas.
 *
 * Acciones:
 *   REGISTROCOMPROBANTESBD  → boleta (03) o factura (01)
 *   REGISTRARNOTACREDITO    → nota de crédito (07)
 */

include("clssConsultas.php");  // Tu conexión $conectar y fnSiguienteCorrelativo()

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/clssSunat.php';

if (isset($_POST["accion"])) {
    controlador_clss_comprobante($_POST["accion"]);
}

function controlador_clss_comprobante(string $accion): void
{
    switch ($accion) {
        case 'REGISTROCOMPROBANTESBD':
            fn_procesar_comprobante($_POST["jsComprobantes"] ?? '', 'BOLETA_FACTURA');
            break;
        case 'REGISTRARNOTACREDITO':
            fn_procesar_comprobante($_POST["jsComprobantes"] ?? '', 'NOTA_CREDITO');
            break;
        default:
            echo json_encode(['estado' => false, 'mensaje' => 'Acción no reconocida']);
    }
}

// =============================================================
// FUNCIÓN PRINCIPAL
// =============================================================
function fn_procesar_comprobante(string $jsDatos, string $modo): void
{
    if (!$jsDatos) {
        echo json_encode(['estado' => false, 'mensaje' => 'Sin datos recibidos']);
        return;
    }

    $dataJs = json_decode($jsDatos, true);
    if (!$dataJs) {
        echo json_encode(['estado' => false, 'mensaje' => 'JSON inválido']);
        return;
    }

    // ── 1. Extraer datos del JS ───────────────────────────────
    $emisorRaw   = $dataJs['emisor']   ?? [];
    $clienteRaw  = $dataJs['cliente']  ?? [];
    $cabeceraRaw = $dataJs['cabecera'] ?? [];
    $detallesRaw = $dataJs['detalles'] ?? [];

    // ── 2. Tipo de comprobante y correlativo desde BD ─────────
    $tipo_comprobante = $modo === 'NOTA_CREDITO' ? '07' : ($cabeceraRaw['tipo_comprobante'] ?? '03');

    // Serie según tipo — usa la configurada en el emisor
    $serie_correlativo = ($tipo_comprobante === '01')
        ? ($emisorRaw['serie_factura'] ?? 'F001')
        : ($emisorRaw['serie_boleta']  ?? 'B001');

    // Correlativo filtrado por sucursal + serie (via JOIN con emisor)
    $sucursal_id_corr  = $emisorRaw['sucursal_id'] ?? $emisorRaw['sucursal'] ?? 1;
    $sigCorr           = fnSiguienteCorrelativo($tipo_comprobante, $sucursal_id_corr, $serie_correlativo);
    $correlativo       = $sigCorr[0]['correlativo_siguiente'] ?? 1;
    $correlativo_texto = $sigCorr[0]['correlativo_texto']     ?? str_pad($correlativo, 8, '0', STR_PAD_LEFT);

    // ── 3. Armar $emisor ─────────────────────────────────────
    $emisor = [
        'sucursal'         => $emisorRaw['sucursal_id'] ?? $emisorRaw['sucursal'] ?? '1',
        'certificado'      => basename($emisorRaw['direccion_firma_digital'] ?? '') ?: ($emisorRaw['certificado'] ?? (($emisorRaw['ruc'] ?? '') . 'Mp12.pfx')),
        'pass_certificado' => $emisorRaw['contraseña_firma_digital']
                           ?? $emisorRaw['pass_certificado']
                           ?? $emisorRaw['clave_firma_digital']
                           ?? '',
        'tipo_documento'   => $emisorRaw['tipo_documento']         ?? 6,
        'ruc'              => $emisorRaw['ruc']                    ?? '',
        'razon_social'     => $emisorRaw['razon_social']           ?? '',
        'nombre_comercial' => $emisorRaw['nombre_comercial']       ?? '',
        'departamento'     => $emisorRaw['departamento']           ?? '',
        'provincia'        => $emisorRaw['provincia']              ?? '',
        'distrito'         => $emisorRaw['distrito']               ?? '',
        'direccion'        => $emisorRaw['direccion']              ?? '',
        'ubigeo'           => $emisorRaw['ubigeo']                 ?? '000000',
        'usuario_sol'      => $emisorRaw['usuario_sol']            ?? '',
        'clave_sol'        => $emisorRaw['clave_sol']              ?? '',
        'ambiente'         => $emisorRaw['ambiente']               ?? 'beta', // ← cambiar a 'produccion' cuando corresponda
    ];

    // ── 4. Armar $cliente ────────────────────────────────────
    $numDoc    = $clienteRaw['numero_doc_cliente'] ?? $clienteRaw['ruc'] ?? '';
    $tdCliente = $clienteRaw['tipo_documento']     ?? (strlen($numDoc) === 11 ? '6' : '1');

    $cliente = [
        'tipo_documento'     => $tdCliente,
        'numero_doc_cliente' => $numDoc,
        'razon_social'       => $clienteRaw['cliente']      ?? $clienteRaw['razon_social'] ?? 'CLIENTE VARIOS',
        'direccion'          => $clienteRaw['direccion']    ?? '',
    ];

    // ── 5. Armar $cabecera ───────────────────────────────────
    // Descomponer serie_correletaivo_ref → "B001-000001" para la nota de crédito
    $serieRef      = '';
    $correlativoRef = '';
    if (!empty($cabeceraRaw['serie_correletaivo_ref'])) {
        $partes        = explode('-', $cabeceraRaw['serie_correletaivo_ref'], 2);
        $serieRef      = $partes[0] ?? '';
        $correlativoRef = $partes[1] ?? '';
    }

    $cabecera = [
        'venta_id'                => $cabeceraRaw['venta_id']               ?? null,
        'tipo_operacion'          => $cabeceraRaw['tipo_operacion']          ?? '0101',
        'tipo_comprobante'        => $tipo_comprobante,
        'moneda'                  => $cabeceraRaw['moneda']                  ?? 'PEN',
        // Serie desde el emisor (BD) según tipo de comprobante — ignora lo que venga del JS
        'serie'                   => ($tipo_comprobante === '01')
                                   ? ($emisorRaw['serie_factura'] ?? $cabeceraRaw['serie'] ?? 'F001')
                                   : ($emisorRaw['serie_boleta']  ?? $cabeceraRaw['serie'] ?? 'B001'),
        'correlativo'             => $correlativo_texto,
        'forma_pago'              => $cabeceraRaw['forma_pago']              ?? 'Contado',
        'total_op_gravadas'       => (float)($cabeceraRaw['total_op_gravadas']       ?? 0),
        'igv'                     => (float)($cabeceraRaw['igv']                     ?? 0),
        'icbper'                  => (float)($cabeceraRaw['icbper']                  ?? 0),
        'total_op_exoneradas'     => (float)($cabeceraRaw['total_op_exoneradas']     ?? 0),
        'total_op_inafectas'      => (float)($cabeceraRaw['total_op_inafectas']      ?? 0),
        'total_antes_impuestos'   => (float)($cabeceraRaw['total_antes_impuestos']   ?? 0),
        'total_impuestos'         => (float)($cabeceraRaw['total_impuestos']         ?? 0),
        'total_despues_impuestos' => (float)($cabeceraRaw['total_despues_impuestos'] ?? 0),
        'total_a_pagar'           => (float)($cabeceraRaw['total_a_pagar']           ?? 0),
        'fecha_emision'           => $cabeceraRaw['fecha_emision']           ?? date('Y-m-d'),
        'hora_emision'            => $cabeceraRaw['hora_emision']            ?? date('H:i:s'),
        'fecha_vencimiento'       => $cabeceraRaw['fecha_vencimiento']       ?? $cabeceraRaw['fecha_emision'] ?? date('Y-m-d'),
        // Campos para nota de crédito
        'tipo_comp_ref'           => $cabeceraRaw['tipo_comp_ref']           ?? '03',
        'serie_correletaivo_ref'  => $cabeceraRaw['serie_correletaivo_ref']  ?? '',
        'serie_ref'               => $cabeceraRaw['serie_ref']               ?? $serieRef,
        'correlativo_ref'         => $cabeceraRaw['correlativo_ref']         ?? $correlativoRef,
        'tipo_nota'               => $cabeceraRaw['tipo_nota']               ?? '01',
        'motivo_nota'             => $cabeceraRaw['motivo']                  ?? $cabeceraRaw['motivo_nota'] ?? 'Anulación de la operación',
    ];

    // ── 6. Mapear $items ─────────────────────────────────────
    $items = [];
    foreach ($detallesRaw as $idx => $d) {
        // Porcentaje de impuesto dinámico desde BD (porcentaje_div: 0.18, 0.00, etc.)
        $tipoImp  = strtoupper($d['tipo_impuesto'] ?? $d['nombre_impuesto'] ?? 'IGV');
        $porcDiv  = (float)($d['porcentaje_div'] ?? 0.18); // divisor real del artículo
        $divisor  = 1 + $porcDiv;                          // 1.18 para IGV, 1.00 para exonerado

        $cantidad    = (float)($d['cantidad']    ?? $d['cantidad_sunat'] ?? 1);
        $puSinIgv    = (float)($d['valor_unitario'] ?? $d['pu_sin_igv'] ?? 0);
        // Si no vino pu_sin_igv, lo calculamos con el divisor correcto
        if ($puSinIgv == 0.0 && !empty($d['pu_con_igv'])) {
            $puSinIgv = (float)$d['pu_con_igv'] / $divisor;
        }

        $icbperItem  = (float)($d['icbper'] ?? 0);
        $precioLista = (float)($d['precio_lista'] ?? $d['pu_con_igv'] ?? round($puSinIgv * $divisor, 2));
        $valorTotal  = round($puSinIgv * $cantidad, 2);

        // ── IGV recalculado con el porcentaje real del artículo ──────────────
        // SUNAT verifica: round(TaxableAmount * porcDiv, 2) === TaxAmount
        $igvItem = match ($tipoImp) {
            'EXONERADO', 'INAFECTO' => 0.0,
            'ICBPER'                => 0.0,
            default                 => round($valorTotal * $porcDiv, 2),
        };

        // Resolver codigos[] de impuesto
        $codigos = $d['codigos'] ?? match ($tipoImp) {
            'EXONERADO' => ['E', '20', '9997', 'EXO', 'VAT'],
            'INAFECTO'  => ['O', '30', '9998', 'INA', 'FRE'],
            default     => ['S', '10', '1000', 'IGV', 'VAT'],
        };

        $items[] = [
            'item'                  => $idx + 1,
            'cantidad'              => $cantidad,
            'unidad'                => $d['unidad']           ?? 'NIU',
            'nombre'                => $d['nombre']           ?? $d['descripcion_articulo'] ?? 'PRODUCTO',
            'valor_unitario'        => round($puSinIgv, 6),
            'precio_lista'          => round($precioLista, 2),
            'valor_total'           => round($valorTotal, 2),
            'igv'                   => round($igvItem, 2),
            'icbper'                => round($icbperItem, 2),
            'factor_icbper'         => (float)($d['factor_icbper'] ?? 0.50),
            'total_antes_impuestos' => round($valorTotal, 2),
            'total_impuestos'       => round($igvItem + $icbperItem, 2),
            'codigos'               => $codigos,
            'tipo_impuesto'         => $tipoImp,
            'porcentaje_div'        => $porcDiv,
            'codigo_producto'       => $d['codigo_producto']  ?? '001',
        ];
    }

    // ── 6b. Recalcular totales de cabecera desde los items (evita error 3103) ──
    $igv_real           = 0.0;
    $op_gravadas_real   = 0.0;
    $op_exoneradas_real = 0.0;
    $op_inafectas_real  = 0.0;

    foreach ($items as $it) {
        // IGV recalculado con el porcentaje real de cada item
        $igv_real += round($it['valor_total'] * ($it['porcentaje_div'] ?? 0.18), 2);
        switch ($it['tipo_impuesto']) {
            case 'EXONERADO': $op_exoneradas_real += $it['valor_total']; break;
            case 'INAFECTO':  $op_inafectas_real  += $it['valor_total']; break;
            default:          $op_gravadas_real   += $it['valor_total']; break;
        }
    }

    $igv_real           = round($igv_real, 2);
    $op_gravadas_real   = round($op_gravadas_real, 2);
    $op_exoneradas_real = round($op_exoneradas_real, 2);
    $op_inafectas_real  = round($op_inafectas_real, 2);
    $total_antes        = round($op_gravadas_real + $op_exoneradas_real + $op_inafectas_real, 2);
    $total_despues      = round($total_antes + $igv_real, 2);

    // Sobreescribir cabecera con valores recalculados
    $cabecera['igv']                     = $igv_real;
    $cabecera['total_op_gravadas']       = $op_gravadas_real;
    $cabecera['total_op_exoneradas']     = $op_exoneradas_real;
    $cabecera['total_op_inafectas']      = $op_inafectas_real;
    $cabecera['total_antes_impuestos']   = $total_antes;
    $cabecera['total_impuestos']         = $igv_real;
    $cabecera['total_despues_impuestos'] = $total_despues;
    $cabecera['total_a_pagar']           = $total_despues;

    // ── 7. Enviar a SUNAT vía clssSunat ──────────────────────
    $sunat     = new SunatComprobante();
    $resultado = $sunat->enviar($emisor, $cliente, $cabecera, $items);

    // ── 8. Guardar en BD (solo si SUNAT respondió OK) ────────
    if ($resultado['estado']) {
        $tipoLabel = match ($tipo_comprobante) {
            '01'    => 'FACTURA',
            '07'    => 'NOTA CREDITO',
            default => 'BOLETA',
        };
        fn_insertar_comprobante_bd($emisor, $cliente, $cabecera, $correlativo, $correlativo_texto, $resultado, $tipoLabel);
    }

    echo json_encode($resultado);
}

// =============================================================
// INSERT EN TABLA comprobante
// =============================================================
function fn_insertar_comprobante_bd(
    array  $emisor,
    array  $cliente,
    array  $cabecera,
    int    $correlativo,
    string $correlativo_texto,
    array  $resultado,
    string $tipo_label
): void {
    global $conectar;

    $sql = "
        INSERT INTO comprobante (
            ruc_emisor, tipo_comprobante, serie, correlativo, correlativo_texto,
            forma_pago, fecha_emision, fecha_vencimiento, moneda,
            op_gravadas, op_exoneradas, op_inafectas, igv, total,
            numero_doc_cliente, tipo_comp_ref, serie_correletaivo_ref, codmotivo,
            nombrexml, xmlbase64, hash,
            codigo_sunat, mensaje_sunat, estado_comprobante, estado_envio,
            comprobante, venta_id
        ) VALUES (
            :ruc_emisor, :tipo_comprobante, :serie, :correlativo, :correlativo_texto,
            :forma_pago, :fecha_emision, :fecha_vencimiento, :moneda,
            :op_gravadas, :op_exoneradas, :op_inafectas, :igv, :total,
            :numero_doc_cliente, :tipo_comp_ref, :serie_correletaivo_ref, :codmotivo,
            :nombrexml, :xmlbase64, :hash,
            :codigo_sunat, :mensaje_sunat, :estado_comprobante, :estado_envio,
            :comprobante, :venta_id
        )
    ";

    $stmt = $conectar->prepare($sql);

    $nombrexml  = $resultado['nombrexml']    ?? '';
    $msg_sunat  = $resultado['mensaje']      ?? '';
    $estado_num = $resultado['estado'] ? '1' : '0';
    $cod_sunat  = $resultado['codigo_sunat'] ?? '0';
    $xmlBase64  = '';   // Si deseas guardar el XML en base64, leerlo aquí del archivo generado
    $codMotivo  = $cabecera['tipo_nota']     ?? '';

    $stmt->bindParam(':ruc_emisor',             $emisor['ruc']);
    $stmt->bindParam(':tipo_comprobante',       $cabecera['tipo_comprobante']);
    $stmt->bindParam(':serie',                  $cabecera['serie']);
    $stmt->bindParam(':correlativo',            $correlativo);
    $stmt->bindParam(':correlativo_texto',      $correlativo_texto);
    $stmt->bindParam(':forma_pago',             $cabecera['forma_pago']);
    $stmt->bindParam(':fecha_emision',          $cabecera['fecha_emision']);
    $stmt->bindParam(':fecha_vencimiento',      $cabecera['fecha_vencimiento']);
    $stmt->bindParam(':moneda',                 $cabecera['moneda']);
    $stmt->bindParam(':op_gravadas',            $cabecera['total_op_gravadas']);
    $stmt->bindParam(':op_exoneradas',          $cabecera['total_op_exoneradas']);
    $stmt->bindParam(':op_inafectas',           $cabecera['total_op_inafectas']);
    $stmt->bindParam(':igv',                    $cabecera['igv']);
    $stmt->bindParam(':total',                  $cabecera['total_despues_impuestos']);
    $stmt->bindParam(':numero_doc_cliente',     $cliente['numero_doc_cliente']);
    $stmt->bindParam(':tipo_comp_ref',          $cabecera['tipo_comp_ref']);
    $stmt->bindParam(':serie_correletaivo_ref', $cabecera['serie_correletaivo_ref']);
    $stmt->bindParam(':codmotivo',              $codMotivo);
    $stmt->bindParam(':nombrexml',              $nombrexml);
    $stmt->bindParam(':xmlbase64',              $xmlBase64);
    $stmt->bindParam(':hash',                   $nombrexml);  // usamos el nombre del CDR como hash
    $stmt->bindParam(':codigo_sunat',           $cod_sunat);
    $stmt->bindParam(':mensaje_sunat',          $msg_sunat);
    $stmt->bindParam(':estado_comprobante',     $estado_num);
    $stmt->bindValue(':estado_envio',           $resultado['estado'], PDO::PARAM_BOOL);
    $stmt->bindParam(':comprobante',            $tipo_label);
    $stmt->bindParam(':venta_id',               $cabecera['venta_id']);

    try {
        $stmt->execute();
    } catch (Exception $e) {
        // El comprobante ya fue enviado a SUNAT, solo logueamos el error de BD
        error_log('ERROR INSERT comprobante: ' . $e->getMessage());
    }
}