<?php
include("bd.php");

if (isset($_POST["accion"])) {
    $accion = $_POST["accion"];

    $sucursal_id = isset($_POST['sucursal_id']) ? intval($_POST['sucursal_id']) : null;
    $proveedor   = isset($_POST['proveedor'])   ? trim($_POST['proveedor'])   : '';
    $usuario     = isset($_POST['usuario'])     ? trim($_POST['usuario'])     : '';
    $fecha_desde = isset($_POST['fecha_desde']) ? trim($_POST['fecha_desde']) : '';
    $fecha_hasta = isset($_POST['fecha_hasta']) ? trim($_POST['fecha_hasta']) : '';

    switch ($accion) {

        case 'FILTRAR_COMPRAS':
            echo json_encode(
                fnFiltrarCompras($sucursal_id, $proveedor, $usuario, $fecha_desde, $fecha_hasta)
            );
            break;

        case 'STATS_COMPRAS':
            echo json_encode(
                fnStatsCompras($sucursal_id, $proveedor, $usuario, $fecha_desde, $fecha_hasta)
            );
            break;

        default:
            echo json_encode(['error' => 'Acción no reconocida']);
            break;
    }
}

/* ═══════════════════════════════════════════════════════════════
   FUNCIÓN HELPER DE CONSULTAS
═══════════════════════════════════════════════════════════════ */
function executeQuery(string $query, array $params = []): array
{
    global $conectar;
    try {
        $stmt = $conectar->prepare($query);
        $stmt->execute($params);
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $datos;
    } catch (PDOException $e) {
        error_log("Error executeQuery: " . $e->getMessage());
        return [];
    }
}

/* ═══════════════════════════════════════════════════════════════
   FILTRAR COMPRAS
   Devuelve las filas de la tabla según los filtros aplicados.
═══════════════════════════════════════════════════════════════ */
function fnFiltrarCompras($sucursal_id, $proveedor = '', $usuario = '', $fecha_desde = '', $fecha_hasta = ''): array
{
    if (!$sucursal_id) return [];

    $conditions = ["c.sucursal_id = :sucursal_id"];
    $params      = ['sucursal_id' => $sucursal_id];

    // ── JOIN necesario para filtrar por usuario o proveedor ──────
    $needsPersonaJoin    = !empty($usuario);
    $needsProveedorJoin  = !empty($proveedor);

    if (!empty($proveedor)) {
        $conditions[] = "LOWER(COALESCE(proveedor.nombre_comercial, '')) LIKE LOWER(:proveedor)";
        $params['proveedor'] = '%' . $proveedor . '%';
    }

    if (!empty($usuario)) {
        $conditions[] = "LOWER(CONCAT(us.nombres, ' ', us.apellidos)) LIKE LOWER(:usuario)";
        $params['usuario'] = '%' . $usuario . '%';
    }

    if (!empty($fecha_desde)) {
        $conditions[] = "c.fecha >= :fecha_desde";
        $params['fecha_desde'] = $fecha_desde;
    }

    if (!empty($fecha_hasta)) {
        $conditions[] = "c.fecha <= :fecha_hasta";
        $params['fecha_hasta'] = $fecha_hasta;
    }

    $where = implode(' AND ', $conditions);

    $query = "
        SELECT
            c.id                                                         AS compra_id,
            CONCAT(us.nombres, ' ', us.apellidos)                        AS realizada_por,
            CASE
                WHEN c.proveedor_id IS NOT NULL
                    THEN CONCAT(proveedor.numero_documento, ' - ', UPPER(proveedor.nombre_comercial))
                ELSE 'SIN REGISTRO DE PROVEEDOR'
            END                                                          AS proveedor,
            proveedor.numero_documento                                   AS proveedor_num_doc,
            UPPER(proveedor.nombre_comercial)                            AS nombre_comercial_proveedor,
            CASE
                WHEN c.fecha IS NULL THEN 'SIN REGISTRO'
                ELSE TO_CHAR(c.fecha, 'YYYY-MM-DD')
            END                                                          AS fecha_compra,
            c.numero_comprobante,
            CASE
                WHEN c.total IS NULL THEN 'SIN REGISTRO'
                ELSE CONCAT('S/ ', c.total)
            END                                                          AS total,
            c.created_at::DATE                                           AS fecha_registro,
            TO_CHAR(c.created_at, 'HH12:MI:SS AM')                      AS hora,
            c.js_detalle_compra,
            c.created_at                                                 AS fecha_hora_registro
        FROM compra c
        JOIN  usuario  u   ON u.id          = c.usuario_id
        JOIN  persona  us  ON u.persona_id  = us.id
        LEFT JOIN persona  proveedor ON c.proveedor_id = proveedor.id
        WHERE $where
        ORDER BY c.id DESC
    ";

    return executeQuery($query, $params);
}

/* ═══════════════════════════════════════════════════════════════
   ESTADÍSTICAS / TARJETAS
═══════════════════════════════════════════════════════════════ */
function fnStatsCompras($sucursal_id, $proveedor = '', $usuario = '', $fecha_desde = '', $fecha_hasta = ''): array
{
    if (!$sucursal_id) {
        return [
            'gran_total_historico'     => 0,
            'total_compras_historico'  => 0,
            'total_compras_filtrado'   => 0,
            'total_productos_filtrado' => 0,
        ];
    }

    // ── Gran total histórico de la sucursal (sin filtros) ────────
    $historico = executeQuery("
        SELECT
            COUNT(DISTINCT c.id)                                        AS total_compras_historico,
            COALESCE(SUM((item->>'sub_total_')::NUMERIC), 0)            AS gran_total_historico
        FROM compra c,
        LATERAL jsonb_array_elements(c.js_detalle_compra::jsonb) AS item
        WHERE c.js_detalle_compra IS NOT NULL
          AND c.sucursal_id = :sucursal_id
    ", ['sucursal_id' => $sucursal_id]);

    // ── Totales con filtros ──────────────────────────────────────
    $conditions = ["c.sucursal_id = :sucursal_id", "c.js_detalle_compra IS NOT NULL"];
    $params      = ['sucursal_id' => $sucursal_id];
    $needsJoin   = !empty($proveedor) || !empty($usuario);

    if (!empty($proveedor)) {
        $conditions[] = "LOWER(COALESCE(proveedor.nombre_comercial, '')) LIKE LOWER(:proveedor)";
        $params['proveedor'] = '%' . $proveedor . '%';
    }

    if (!empty($usuario)) {
        $conditions[] = "LOWER(CONCAT(us.nombres, ' ', us.apellidos)) LIKE LOWER(:usuario)";
        $params['usuario'] = '%' . $usuario . '%';
    }

    if (!empty($fecha_desde)) {
        $conditions[] = "c.fecha >= :fecha_desde";
        $params['fecha_desde'] = $fecha_desde;
    }

    if (!empty($fecha_hasta)) {
        $conditions[] = "c.fecha <= :fecha_hasta";
        $params['fecha_hasta'] = $fecha_hasta;
    }

    $where = implode(' AND ', $conditions);

    // Incluir JOINs solo si se filtran por usuario o proveedor
    $joinClause = $needsJoin
        ? "JOIN  usuario  u   ON u.id         = c.usuario_id
           JOIN  persona  us  ON u.persona_id = us.id
           LEFT JOIN persona  proveedor ON c.proveedor_id = proveedor.id"
        : "";

    // Si NO hay join pero SÍ hay filtro de fecha, igual necesitamos al menos el join básico
    // (la tabla compra ya tiene sucursal_id, fecha, etc. — no hace falta join para fechas)

    $filtrado = executeQuery("
        SELECT
            COUNT(DISTINCT c.id)                                    AS total_compras_filtrado,
            COALESCE(SUM((item->>'sub_total_')::NUMERIC), 0)        AS total_productos_filtrado
        FROM compra c
        $joinClause,
        LATERAL jsonb_array_elements(c.js_detalle_compra::jsonb) AS item
        WHERE $where
    ", $params);

    return [
        'gran_total_historico'     => $historico[0]['gran_total_historico']     ?? 0,
        'total_compras_historico'  => $historico[0]['total_compras_historico']  ?? 0,
        'total_compras_filtrado'   => $filtrado[0]['total_compras_filtrado']    ?? 0,
        'total_productos_filtrado' => $filtrado[0]['total_productos_filtrado']  ?? 0,
    ];
}