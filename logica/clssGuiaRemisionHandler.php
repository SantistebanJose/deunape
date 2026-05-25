<?php
/**
 * clssGuiaRemisionHandler.php
 * Handler AJAX para declarar Guías de Remisión a SUNAT.
 * Usa clssGuiaRemision.php directamente — sin llamadas a API externas.
 *
 * Acciones:
 *   REGISTRAR_GUIA_REMITENTE      → Guía de Remisión Remitente (09)
 *   REGISTRAR_GUIA_TRANSPORTISTA  → Guía de Remisión Transportista (31)
 *   LISTAR_GUIAS                  → Lista guías con filtros
 *   OBTENER_GUIA                  → Obtiene una guía por ID
 *   ANULAR_GUIA                   → Marca una guía como anulada en BD
 *   OBTENER_ITEMS_VENTA           → Retorna los ítems de una venta para prellenar
 */

include("clssConsultas.php");   // $conectar y fnSiguienteCorrelativo()

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/clssGuiaRemision.php';

if (isset($_POST["accion"])) {
    controlador_guia($_POST["accion"]);
} elseif (isset($_GET["accion"])) {
    controlador_guia($_GET["accion"]);
}

function controlador_guia(string $accion): void
{
    switch ($accion) {
        case 'REGISTRAR_GUIA_REMITENTE':
            fn_procesar_guia($_POST["jsGuia"] ?? '', '09');
            break;
        case 'REGISTRAR_GUIA_TRANSPORTISTA':
            fn_procesar_guia($_POST["jsGuia"] ?? '', '31');
            break;
        case 'LISTAR_GUIAS':
            fn_listar_guias();
            break;
        case 'OBTENER_GUIA':
            fn_obtener_guia((int)($_GET['id'] ?? $_POST['id'] ?? 0));
            break;
        case 'ANULAR_GUIA':
            fn_anular_guia((int)($_POST['id'] ?? 0));
            break;
        case 'OBTENER_ITEMS_VENTA':
            fn_obtener_items_venta((int)($_GET['venta_id'] ?? $_POST['venta_id'] ?? 0));
            break;
        default:
            echo json_encode(['estado' => false, 'mensaje' => 'Acción no reconocida']);
    }
}

// =============================================================
// FUNCIÓN PRINCIPAL — procesar y enviar guía
// =============================================================
function fn_procesar_guia(string $jsDatos, string $tipo_forzado = '09'): void
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

    // ── 1. Extraer secciones ──────────────────────────────────
    $emisorRaw        = $dataJs['emisor']       ?? [];
    $destinatarioRaw  = $dataJs['destinatario'] ?? [];
    $cabeceraRaw      = $dataJs['cabecera']     ?? [];
    $detallesRaw      = $dataJs['detalles']     ?? [];

    // ── 2. Tipo y correlativo ─────────────────────────────────
    $tipo_comprobante = $tipo_forzado; // 09 o 31

    $serie = ($tipo_comprobante === '31')
        ? ($emisorRaw['serie_guia_transportista'] ?? 'V001')
        : ($emisorRaw['serie_guia_remitente']     ?? 'T001');

    $sucursal_id = $emisorRaw['sucursal_id'] ?? $emisorRaw['sucursal'] ?? 1;
    $sigCorr     = fnSiguienteCorrelativo($tipo_comprobante, $sucursal_id, $serie);
    $correlativo = $sigCorr[0]['correlativo_siguiente'] ?? 1;
    $corr_texto  = $sigCorr[0]['correlativo_texto']     ?? str_pad($correlativo, 8, '0', STR_PAD_LEFT);

    // ── 3. Armar $emisor ──────────────────────────────────────
    $emisor = [
        'sucursal'         => $emisorRaw['sucursal_id'] ?? $emisorRaw['sucursal'] ?? '1',
        'certificado'      => basename($emisorRaw['direccion_firma_digital'] ?? '') ?: ($emisorRaw['certificado'] ?? (($emisorRaw['ruc'] ?? '') . 'Mp12.pfx')),
        'pass_certificado' => (function() use ($emisorRaw) {
                               foreach ($emisorRaw as $k => $v) {
                                   if (stripos($k, 'contrase') !== false && stripos($k, 'firma') !== false) return $v;
                               }
                               return $emisorRaw['pass_certificado'] ?? $emisorRaw['clave_firma_digital'] ?? '';
                           })() ?? '',
        'tipo_documento'   => $emisorRaw['tipo_documento']   ?? 6,
        'ruc'              => $emisorRaw['ruc']              ?? '',
        'razon_social'     => $emisorRaw['razon_social']     ?? '',
        'ubigeo'           => $emisorRaw['ubigeo']           ?? '000000',
        'usuario_sol'      => $emisorRaw['usuario_sol']      ?? '',
        'clave_sol'        => $emisorRaw['clave_sol']        ?? '',
        'ambiente'         => $emisorRaw['ambiente']         ?? 'beta',
    ];

    // ── 4. Armar $destinatario ────────────────────────────────
    $numDoc = $destinatarioRaw['numero_doc'] ?? $destinatarioRaw['ruc'] ?? '';
    $destinatario = [
        'tipo_documento'    => $destinatarioRaw['tipo_documento'] ?? (strlen($numDoc) === 11 ? '6' : '1'),
        'numero_doc'        => $numDoc,
        'razon_social'      => $destinatarioRaw['razon_social'] ?? $destinatarioRaw['nombre'] ?? '',
        'ubigeo'            => $destinatarioRaw['ubigeo']            ?? '000000',
        'direccion'         => $destinatarioRaw['direccion']         ?? '',
        'ubigeo_llegada'    => $destinatarioRaw['ubigeo_llegada']    ?? $destinatarioRaw['ubigeo']   ?? '000000',
        'direccion_llegada' => $destinatarioRaw['direccion_llegada'] ?? $destinatarioRaw['direccion'] ?? '',
    ];

    // ── 5. Armar $cabecera ────────────────────────────────────
    $cabecera = [
        'venta_id'                        => $cabeceraRaw['venta_id']               ?? null,
        'tipo_comprobante'                => $tipo_comprobante,
        'serie'                           => $serie,
        'correlativo'                     => $corr_texto,
        'fecha_emision'                   => $cabeceraRaw['fecha_emision']           ?? date('Y-m-d'),
        'hora_emision'                    => $cabeceraRaw['hora_emision']            ?? date('H:i:s'),
        'fecha_traslado'                  => $cabeceraRaw['fecha_traslado']          ?? date('Y-m-d'),
        'motivo_traslado'                 => $cabeceraRaw['motivo_traslado']         ?? '01',
        'modalidad_traslado'              => $cabeceraRaw['modalidad_traslado']      ?? '02',
        'indicaciones'                    => $cabeceraRaw['indicaciones']            ?? '',
        'peso_bruto_total'                => (float)($cabeceraRaw['peso_bruto_total'] ?? 0),
        'unidad_peso'                     => $cabeceraRaw['unidad_peso']             ?? 'KGM',
        // Partida
        'ubigeo_partida'                  => $cabeceraRaw['ubigeo_partida']          ?? $emisorRaw['ubigeo'] ?? '000000',
        'direccion_partida'               => $cabeceraRaw['direccion_partida']       ?? $emisorRaw['direccion'] ?? '',
        // Vehículo
        'placa_vehiculo'                  => $cabeceraRaw['placa_vehiculo']          ?? '',
        // Conductor
        'conductor_tipo_doc'              => $cabeceraRaw['conductor_tipo_doc']      ?? '1',
        'conductor_doc'                   => $cabeceraRaw['conductor_doc']           ?? '',
        'conductor_nombres'               => $cabeceraRaw['conductor_nombres']       ?? '',
        'conductor_apellidos'             => $cabeceraRaw['conductor_apellidos']     ?? '',
        'conductor_licencia'              => $cabeceraRaw['conductor_licencia']      ?? '',
        // Transportista externo (solo modalidad pública)
        'transportista_tipo_doc'          => $cabeceraRaw['transportista_tipo_doc']  ?? '6',
        'transportista_ruc'               => $cabeceraRaw['transportista_ruc']       ?? '',
        'transportista_razon_social'      => $cabeceraRaw['transportista_razon_social'] ?? '',
        // Comprobante relacionado (factura/boleta origen)
        'comprobante_ref_tipo'            => $cabeceraRaw['comprobante_ref_tipo']    ?? '01',
        'comprobante_ref_serie'           => $cabeceraRaw['comprobante_ref_serie']   ?? '',
        'comprobante_ref_correlativo'     => $cabeceraRaw['comprobante_ref_correlativo'] ?? '',
        // GR Remitente relacionada (solo para tipo 31)
        'guia_remitente_serie'            => $cabeceraRaw['guia_remitente_serie']    ?? '',
        'guia_remitente_correlativo'      => $cabeceraRaw['guia_remitente_correlativo'] ?? '',
    ];

    // ── 6. Mapear $items ──────────────────────────────────────
    $items = [];
    foreach ($detallesRaw as $idx => $d) {
        $items[] = [
            'item'            => $idx + 1,
            'cantidad'        => (float)($d['cantidad'] ?? 1),
            'unidad'          => $d['unidad']           ?? 'NIU',
            'nombre'          => $d['nombre']           ?? $d['descripcion'] ?? 'PRODUCTO',
            'codigo_producto' => $d['codigo_producto']  ?? '001',
        ];
    }

    if (empty($items)) {
        echo json_encode(['estado' => false, 'mensaje' => 'La guía debe tener al menos un ítem']);
        return;
    }

    // ── 7. Enviar a SUNAT ─────────────────────────────────────
    $guia      = new SunatGuiaRemision();
    $resultado = $guia->enviar($emisor, $destinatario, $cabecera, $items);

    // ── 8. Guardar en BD ──────────────────────────────────────
    if ($resultado['estado']) {
        $tipo_label = $tipo_comprobante === '31' ? 'GUIA TRANSPORTISTA' : 'GUIA REMITENTE';
        fn_insertar_guia_bd($emisor, $destinatario, $cabecera, $correlativo, $corr_texto, $resultado, $tipo_label);
    }

    echo json_encode($resultado);
}

// =============================================================
// INSERT EN TABLA guia_remision
// =============================================================
function fn_insertar_guia_bd(
    array  $emisor,
    array  $destinatario,
    array  $cabecera,
    int    $correlativo,
    string $correlativo_texto,
    array  $resultado,
    string $tipo_label
): void {
    global $conectar;

    $sql = "
        INSERT INTO guia_remision (
            ruc_emisor, tipo_comprobante, serie, correlativo, correlativo_texto,
            fecha_emision, fecha_traslado,
            motivo_traslado, modalidad_traslado,
            peso_bruto_total, unidad_peso,
            ubigeo_partida, direccion_partida,
            ubigeo_llegada, direccion_llegada,
            placa_vehiculo,
            conductor_doc, conductor_nombres, conductor_apellidos, conductor_licencia,
            transportista_ruc, transportista_razon_social,
            numero_doc_destinatario, razon_social_destinatario,
            comprobante_ref_tipo, comprobante_ref_serie, comprobante_ref_correlativo,
            guia_remitente_serie, guia_remitente_correlativo,
            nombrexml, codigo_sunat, mensaje_sunat,
            estado_comprobante, estado_envio,
            tipo_guia, venta_id
        ) VALUES (
            :ruc_emisor, :tipo_comprobante, :serie, :correlativo, :correlativo_texto,
            :fecha_emision, :fecha_traslado,
            :motivo_traslado, :modalidad_traslado,
            :peso_bruto_total, :unidad_peso,
            :ubigeo_partida, :direccion_partida,
            :ubigeo_llegada, :direccion_llegada,
            :placa_vehiculo,
            :conductor_doc, :conductor_nombres, :conductor_apellidos, :conductor_licencia,
            :transportista_ruc, :transportista_razon_social,
            :numero_doc_destinatario, :razon_social_destinatario,
            :comprobante_ref_tipo, :comprobante_ref_serie, :comprobante_ref_correlativo,
            :guia_remitente_serie, :guia_remitente_correlativo,
            :nombrexml, :codigo_sunat, :mensaje_sunat,
            :estado_comprobante, :estado_envio,
            :tipo_guia, :venta_id
        )
    ";

    $stmt = $conectar->prepare($sql);

    $nombrexml  = $resultado['nombrexml']    ?? '';
    $msg_sunat  = $resultado['mensaje']      ?? '';
    $estado_num = $resultado['estado'] ? '1' : '0';
    $cod_sunat  = $resultado['codigo_sunat'] ?? '0';

    $stmt->bindParam(':ruc_emisor',                  $emisor['ruc']);
    $stmt->bindParam(':tipo_comprobante',             $cabecera['tipo_comprobante']);
    $stmt->bindParam(':serie',                        $cabecera['serie']);
    $stmt->bindParam(':correlativo',                  $correlativo);
    $stmt->bindParam(':correlativo_texto',            $correlativo_texto);
    $stmt->bindParam(':fecha_emision',                $cabecera['fecha_emision']);
    $stmt->bindParam(':fecha_traslado',               $cabecera['fecha_traslado']);
    $stmt->bindParam(':motivo_traslado',              $cabecera['motivo_traslado']);
    $stmt->bindParam(':modalidad_traslado',           $cabecera['modalidad_traslado']);
    $stmt->bindParam(':peso_bruto_total',             $cabecera['peso_bruto_total']);
    $stmt->bindParam(':unidad_peso',                  $cabecera['unidad_peso']);
    $stmt->bindParam(':ubigeo_partida',               $cabecera['ubigeo_partida']);
    $stmt->bindParam(':direccion_partida',            $cabecera['direccion_partida']);
    $stmt->bindParam(':ubigeo_llegada',               $destinatario['ubigeo_llegada']);
    $stmt->bindParam(':direccion_llegada',            $destinatario['direccion_llegada']);
    $stmt->bindParam(':placa_vehiculo',               $cabecera['placa_vehiculo']);
    $stmt->bindParam(':conductor_doc',                $cabecera['conductor_doc']);
    $stmt->bindParam(':conductor_nombres',            $cabecera['conductor_nombres']);
    $stmt->bindParam(':conductor_apellidos',          $cabecera['conductor_apellidos']);
    $stmt->bindParam(':conductor_licencia',           $cabecera['conductor_licencia']);
    $stmt->bindParam(':transportista_ruc',            $cabecera['transportista_ruc']);
    $stmt->bindParam(':transportista_razon_social',   $cabecera['transportista_razon_social']);
    $stmt->bindParam(':numero_doc_destinatario',      $destinatario['numero_doc']);
    $stmt->bindParam(':razon_social_destinatario',    $destinatario['razon_social']);
    $stmt->bindParam(':comprobante_ref_tipo',         $cabecera['comprobante_ref_tipo']);
    $stmt->bindParam(':comprobante_ref_serie',        $cabecera['comprobante_ref_serie']);
    $stmt->bindParam(':comprobante_ref_correlativo',  $cabecera['comprobante_ref_correlativo']);
    $stmt->bindParam(':guia_remitente_serie',         $cabecera['guia_remitente_serie']);
    $stmt->bindParam(':guia_remitente_correlativo',   $cabecera['guia_remitente_correlativo']);
    $stmt->bindParam(':nombrexml',                    $nombrexml);
    $stmt->bindParam(':codigo_sunat',                 $cod_sunat);
    $stmt->bindParam(':mensaje_sunat',                $msg_sunat);
    $stmt->bindParam(':estado_comprobante',           $estado_num);
    $stmt->bindValue(':estado_envio',                 $resultado['estado'], PDO::PARAM_BOOL);
    $stmt->bindParam(':tipo_guia',                    $tipo_label);
    $stmt->bindParam(':venta_id',                     $cabecera['venta_id']);

    try {
        $stmt->execute();
    } catch (Exception $e) {
        error_log('ERROR INSERT guia_remision: ' . $e->getMessage());
    }
}

// =============================================================
// LISTAR GUÍAS — con filtros opcionales por GET/POST
// =============================================================
function fn_listar_guias(): void
{
    global $conectar;

    $tipo   = $_REQUEST['tipo_comprobante'] ?? '';
    $estado = $_REQUEST['estado']           ?? '';
    $desde  = $_REQUEST['desde']            ?? date('Y-m-01');
    $hasta  = $_REQUEST['hasta']            ?? date('Y-m-d');
    $buscar = $_REQUEST['buscar']           ?? '';

    $where  = ['g.fecha_emision BETWEEN :desde AND :hasta'];
    $params = [':desde' => $desde, ':hasta' => $hasta];

    if ($tipo)   { $where[] = 'g.tipo_comprobante = :tipo';   $params[':tipo']   = $tipo;   }
    if ($estado !== '') { $where[] = 'g.estado_envio = :estado'; $params[':estado'] = $estado; }
    if ($buscar) {
        $where[] = '(g.serie_correlativo LIKE :buscar OR g.razon_social_destinatario LIKE :buscar OR g.numero_doc_destinatario LIKE :buscar)';
        $params[':buscar'] = '%' . $buscar . '%';
    }

    $sql = "
        SELECT
            g.id,
            g.tipo_guia,
            g.tipo_comprobante,
            CONCAT(g.serie, '-', g.correlativo_texto) AS serie_correlativo,
            g.fecha_emision,
            g.fecha_traslado,
            g.razon_social_destinatario,
            g.numero_doc_destinatario,
            g.motivo_traslado,
            g.modalidad_traslado,
            g.placa_vehiculo,
            g.peso_bruto_total,
            g.unidad_peso,
            g.estado_envio,
            g.estado_comprobante,
            g.mensaje_sunat,
            g.venta_id,
            CONCAT(g.comprobante_ref_serie, '-', g.comprobante_ref_correlativo) AS comprobante_ref
        FROM guia_remision g
        WHERE " . implode(' AND ', $where) . "
        ORDER BY g.id DESC
    ";

    try {
        $stmt = $conectar->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['estado' => true, 'datos' => $datos]);
    } catch (Exception $e) {
        echo json_encode(['estado' => false, 'mensaje' => $e->getMessage()]);
    }
}

// =============================================================
// OBTENER UNA GUÍA POR ID
// =============================================================
function fn_obtener_guia(int $id): void
{
    global $conectar;
    if ($id <= 0) {
        echo json_encode(['estado' => false, 'mensaje' => 'ID inválido']);
        return;
    }

    try {
        $sql  = "SELECT * FROM guia_remision WHERE id = :id LIMIT 1";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $guia = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$guia) {
            echo json_encode(['estado' => false, 'mensaje' => 'Guía no encontrada']);
            return;
        }

        // Obtener ítems de la guía
        $sqlItems  = "SELECT * FROM guia_remision_detalle WHERE guia_id = :id ORDER BY item";
        $stmtItems = $conectar->prepare($sqlItems);
        $stmtItems->bindValue(':id', $id);
        $stmtItems->execute();
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['estado' => true, 'guia' => $guia, 'items' => $items]);
    } catch (Exception $e) {
        echo json_encode(['estado' => false, 'mensaje' => $e->getMessage()]);
    }
}

// =============================================================
// ANULAR GUÍA (solo marca en BD, SUNAT no acepta anulación directa de GR)
// =============================================================
function fn_anular_guia(int $id): void
{
    global $conectar;
    if ($id <= 0) {
        echo json_encode(['estado' => false, 'mensaje' => 'ID inválido']);
        return;
    }

    try {
        $sql  = "UPDATE guia_remision SET estado_comprobante = '0', estado_envio = 0 WHERE id = :id";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        echo json_encode(['estado' => true, 'mensaje' => 'Guía anulada en el sistema']);
    } catch (Exception $e) {
        echo json_encode(['estado' => false, 'mensaje' => $e->getMessage()]);
    }
}

// =============================================================
// OBTENER ÍTEMS DE UNA VENTA (para prellenar la guía)
// =============================================================
function fn_obtener_items_venta(int $venta_id): void
{
    global $conectar;
    if ($venta_id <= 0) {
        echo json_encode(['estado' => false, 'mensaje' => 'venta_id inválido']);
        return;
    }

    try {
        // Ajusta la consulta a tus tablas reales
        $sql = "
            SELECT
                d.articulo_id        AS codigo_producto,
                d.descripcion        AS nombre,
                d.cantidad,
                d.unidad_medida      AS unidad,
                v.numero_doc_cliente AS numero_doc_destinatario,
                v.cliente            AS razon_social_destinatario,
                v.tipo_documento     AS tipo_documento_destinatario
            FROM detalle_venta d
            INNER JOIN venta v ON v.id = d.venta_id
            WHERE d.venta_id = :venta_id
            ORDER BY d.id
        ";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(':venta_id', $venta_id);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($items)) {
            echo json_encode(['estado' => false, 'mensaje' => 'No se encontraron ítems para esta venta']);
            return;
        }

        echo json_encode(['estado' => true, 'items' => $items]);
    } catch (Exception $e) {
        echo json_encode(['estado' => false, 'mensaje' => $e->getMessage()]);
    }
}