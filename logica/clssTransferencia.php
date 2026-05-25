<?php
/**
 * clssTransferencia.php
 * CRUD de transferencias de stock entre locaciones/estructuras.
 *
 * Acciones:
 *   BUSCAR_ARTICULO          → búsqueda con stock total en la sucursal (paso 1)
 *   STOCK_POR_UBICACION      → breakdown de stock por locación/estructura (paso 1)
 *   LISTAR_LOCACIONES        → select de locaciones activas
 *   LISTAR_ESTRUCTURAS       → estructuras por locación
 *   TRANSFERIR               → transacción atómica
 *   LISTAR_HISTORIAL         → DataTables server-side
 *   GET_STATS                → stats del encabezado
 */

ob_start();
error_reporting(0);
ini_set('display_errors', 0);

session_start();
include("bd.php");

header('Content-Type: application/json; charset=utf-8');

$accion = $_POST['accion'] ?? '';
ob_clean();

switch ($accion) {
    case 'BUSCAR_ARTICULO':      buscarArticulo();         break;
    case 'STOCK_POR_UBICACION':  stockPorUbicacion();      break;
    case 'LISTAR_LOCACIONES':    listarLocaciones();        break;
    case 'LISTAR_ESTRUCTURAS':   listarEstructuras();       break;
    case 'TRANSFERIR':           transferirStock();         break;
    case 'LISTAR_HISTORIAL':     listarHistorial();         break;
    case 'GET_STATS':            getStats();                break;
    default:
        echo json_encode(['error' => true, 'message' => 'Acción no reconocida.']);
}

/* ─────────────────────────────────────────
   HELPERS
───────────────────────────────────────── */
function getSucursalId(): ?int
{
    return isset($_SESSION['sucursal_id']) && $_SESSION['sucursal_id'] !== ''
        ? (int)$_SESSION['sucursal_id'] : null;
}
function getUsuarioId(): ?int
{
    return isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : null;
}
function respErr(string $msg): void
{
    echo json_encode(['success' => false, 'message' => $msg]);
}

/* ═════════════════════════════════════════════
   BUSCAR ARTÍCULO — paso 1
   Devuelve artículos que tienen stock > 0 en la
   sucursal y coinciden con el término de búsqueda.
═════════════════════════════════════════════ */
function buscarArticulo(): void
{
    global $conectar;
    $sucursal_id = getSucursalId();
    $q = trim($_POST['q'] ?? '');

    if (strlen($q) < 2) {
        echo json_encode(['success' => true, 'data' => []]);
        return;
    }

    try {
        $sql = "
            SELECT
                a.id,
                a.nombre,
                SUM(i.stock) AS stock_total
            FROM articulo a
            JOIN inventario i ON i.articulo_id = a.id
            WHERE i.sucursal_id = :sucursal_id
              AND i.stock > 0
              AND (
                  a.nombre ILIKE :q
              )
            GROUP BY a.id, a.nombre
            HAVING SUM(i.stock) > 0
            ORDER BY a.nombre
            LIMIT 20
        ";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
        $stmt->bindValue(':q', '%' . $q . '%');
        $stmt->execute();

        echo json_encode([
            'success' => true,
            'data'    => $stmt->fetchAll(PDO::FETCH_ASSOC),
        ]);
    } catch (\Throwable $th) {
        respErr('Error al buscar: ' . $th->getMessage());
    }
}

/* ═════════════════════════════════════════════
   STOCK POR UBICACIÓN — paso 1 (breakdown)
   Devuelve el stock de un artículo desglosado
   por cada locación/estructura de la sucursal.
═════════════════════════════════════════════ */
function stockPorUbicacion(): void
{
    global $conectar;
    $sucursal_id = getSucursalId();
    $articulo_id = (int)($_POST['articulo_id'] ?? 0);

    if (!$articulo_id) { respErr('ID de artículo inválido.'); return; }

    try {
        $stmtArt = $conectar->prepare("
            SELECT
                a.id,
                a.nombre,
                COALESCE(SUM(i.stock), 0) AS stock_total
            FROM articulo a
            LEFT JOIN inventario i
                ON i.articulo_id = a.id AND i.sucursal_id = :sucursal_id
            WHERE a.id = :articulo_id
            GROUP BY a.id, a.nombre
        ");
        $stmtArt->bindValue(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
        $stmtArt->bindValue(':articulo_id', $articulo_id, PDO::PARAM_INT);
        $stmtArt->execute();
        $articulo = $stmtArt->fetch(PDO::FETCH_ASSOC);

        if (!$articulo) { respErr('Artículo no encontrado.'); return; }

        $stmtBrk = $conectar->prepare("
            SELECT
                l.id                AS locacion_id,
                l.nombre            AS locacion_nombre,
                el.id               AS estructura_id,
                el.nombre           AS estructura_nombre,
                i.stock
            FROM inventario i
            JOIN locacion l ON l.id = i.locacion_id
            LEFT JOIN estructura_locacion el ON el.id = i.estructura_id
            WHERE i.articulo_id  = :articulo_id
              AND i.sucursal_id  = :sucursal_id
              AND i.stock > 0
            ORDER BY l.nombre, el.nombre NULLS FIRST
        ");
        $stmtBrk->bindValue(':articulo_id', $articulo_id, PDO::PARAM_INT);
        $stmtBrk->bindValue(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
        $stmtBrk->execute();

        // ← AGREGA ESTO TEMPORALMENTE para ver qué devuelve
        $rows = $stmtBrk->fetchAll(PDO::FETCH_ASSOC);
        error_log("STOCK_POR_UBICACION art={$articulo_id} suc={$sucursal_id} rows=" . count($rows) . " data=" . json_encode($rows));

        echo json_encode([
            'success'  => true,
            'articulo' => $articulo,
            'data'     => $rows,
        ]);
    } catch (\Throwable $th) {
        error_log("stockPorUbicacion ERROR: " . $th->getMessage());
        respErr('Error: ' . $th->getMessage());
    }
}
/* ═════════════════════════════════════════════
   LISTAR LOCACIONES — select destino
═════════════════════════════════════════════ */
function listarLocaciones(): void
{
    global $conectar;
    $sucursal_id = getSucursalId();
    try {
        $sql    = 'SELECT id, nombre, tipo FROM locacion WHERE estado = true';
        $params = [];
        if ($sucursal_id !== null) {
            $sql .= ' AND sucursal_id = :sucursal_id';
            $params[':sucursal_id'] = $sucursal_id;
        }
        $sql .= ' ORDER BY nombre';
        $stmt = $conectar->prepare($sql);
        $stmt->execute($params);
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } catch (\Throwable $th) {
        echo json_encode(['success' => false, 'data' => []]);
    }
}

/* ═════════════════════════════════════════════
   LISTAR ESTRUCTURAS — select destino
═════════════════════════════════════════════ */
function listarEstructuras(): void
{
    global $conectar;
    $locacion_id = (int)($_POST['locacion_id'] ?? 0);
    if (!$locacion_id) { echo json_encode(['success' => false, 'data' => []]); return; }
    try {
        $stmt = $conectar->prepare(
            'SELECT id, nombre, tipo
             FROM estructura_locacion
             WHERE locacion_id = :locacion_id
             ORDER BY posicion ASC NULLS LAST, nombre ASC'
        );
        $stmt->bindValue(':locacion_id', $locacion_id, PDO::PARAM_INT);
        $stmt->execute();
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } catch (\Throwable $th) {
        echo json_encode(['success' => false, 'data' => []]);
    }
}

/* ═════════════════════════════════════════════
   TRANSFERIR — transacción atómica
═════════════════════════════════════════════ */
function transferirStock(): void
{
    global $conectar;
    $sucursal_id = getSucursalId();
    $usuario_id  = getUsuarioId();

    $datos = json_decode($_POST['data'] ?? '{}', true);
    if (json_last_error() !== JSON_ERROR_NONE) { respErr('JSON inválido.'); return; }

    try {
        $cantidad = floatval($datos['cantidad'] ?? 0);
        if ($cantidad <= 0) { respErr('La cantidad debe ser mayor a 0.'); return; }

        $lo = (int)($datos['locacion_origen_id']  ?? 0);
        $ld = (int)($datos['locacion_destino_id'] ?? 0);
        $eo = !empty($datos['estructura_origen_id'])  ? (int)$datos['estructura_origen_id']  : null;
        $ed = !empty($datos['estructura_destino_id']) ? (int)$datos['estructura_destino_id'] : null;

        if ($lo === $ld && $eo === $ed) { respErr('El origen y destino no pueden ser la misma ubicación.'); return; }

        $articulo_id = (int)($datos['articulo_id'] ?? 0);
        $motivo      = trim($datos['motivo'] ?? '');

        if (!$articulo_id || !$lo || !$ld) { respErr('Datos incompletos para la transferencia.'); return; }

        $conectar->beginTransaction();

        /* 1. verificar stock en origen */
        $sqlO = "SELECT id, stock FROM inventario
                 WHERE articulo_id = :art AND locacion_id = :loc AND sucursal_id = :suc
                   AND estructura_id " . ($eo === null ? 'IS NULL' : '= :est');
        $stmtO = $conectar->prepare($sqlO);
        $stmtO->bindValue(':art', $articulo_id, PDO::PARAM_INT);
        $stmtO->bindValue(':loc', $lo,          PDO::PARAM_INT);
        $stmtO->bindValue(':suc', $sucursal_id, PDO::PARAM_INT);
        if ($eo !== null) $stmtO->bindValue(':est', $eo, PDO::PARAM_INT);
        $stmtO->execute();
        $invO = $stmtO->fetch(PDO::FETCH_ASSOC);

        if (!$invO) { $conectar->rollBack(); respErr('No se encontró inventario del artículo en el origen seleccionado.'); return; }
        if ((float)$invO['stock'] < $cantidad) { $conectar->rollBack(); respErr('Stock insuficiente. Disponible: ' . $invO['stock']); return; }

        /* 2. restar origen */
        $conectar->prepare('UPDATE inventario SET stock = stock - :c WHERE id = :id')
                 ->execute([':c' => $cantidad, ':id' => (int)$invO['id']]);

        /* 3. sumar o crear destino */
        $sqlD = "SELECT id FROM inventario
                 WHERE articulo_id = :art AND locacion_id = :loc AND sucursal_id = :suc
                   AND estructura_id " . ($ed === null ? 'IS NULL' : '= :est');
        $stmtD = $conectar->prepare($sqlD);
        $stmtD->bindValue(':art', $articulo_id, PDO::PARAM_INT);
        $stmtD->bindValue(':loc', $ld,          PDO::PARAM_INT);
        $stmtD->bindValue(':suc', $sucursal_id, PDO::PARAM_INT);
        if ($ed !== null) $stmtD->bindValue(':est', $ed, PDO::PARAM_INT);
        $stmtD->execute();
        $invD = $stmtD->fetch(PDO::FETCH_ASSOC);

        if ($invD) {
            $conectar->prepare('UPDATE inventario SET stock = stock + :c WHERE id = :id')
                     ->execute([':c' => $cantidad, ':id' => (int)$invD['id']]);
        } else {
            $stmtI = $conectar->prepare("
                INSERT INTO inventario (locacion_id, sucursal_id, estructura_id, articulo_id, stock)
                VALUES (:loc, :suc, :est, :art, :stock)
            ");
            $stmtI->bindValue(':loc',   $ld,          PDO::PARAM_INT);
            $stmtI->bindValue(':suc',   $sucursal_id, PDO::PARAM_INT);
            $stmtI->bindValue(':est',   $ed,          $ed === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmtI->bindValue(':art',   $articulo_id, PDO::PARAM_INT);
            $stmtI->bindValue(':stock', $cantidad);
            $stmtI->execute();
        }

        /* 4. historial */
        $stmtH = $conectar->prepare("
            INSERT INTO transferencia_inventario
                (sucursal_id, articulo_id,
                 locacion_origen_id, estructura_origen_id,
                 locacion_destino_id, estructura_destino_id,
                 cantidad, motivo, usuario_id)
            VALUES (:suc, :art, :lo, :eo, :ld, :ed, :c, :m, :u)
        ");
        $stmtH->bindValue(':suc', $sucursal_id,  PDO::PARAM_INT);
        $stmtH->bindValue(':art', $articulo_id,  PDO::PARAM_INT);
        $stmtH->bindValue(':lo',  $lo,            PDO::PARAM_INT);
        $stmtH->bindValue(':eo',  $eo,            $eo === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmtH->bindValue(':ld',  $ld,            PDO::PARAM_INT);
        $stmtH->bindValue(':ed',  $ed,            $ed === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmtH->bindValue(':c',   $cantidad);
        $stmtH->bindValue(':m',   $motivo ?: null, $motivo ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmtH->bindValue(':u',   $usuario_id,    $usuario_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmtH->execute();

        $conectar->commit();
        echo json_encode(['success' => true, 'message' => 'Transferencia realizada correctamente.']);

    } catch (\Throwable $th) {
        if ($conectar->inTransaction()) $conectar->rollBack();
        error_log('transferirStock: ' . $th->getMessage());
        respErr('Error interno: ' . $th->getMessage());
    }
}

/* ═════════════════════════════════════════════
   HISTORIAL — DataTables server-side
═════════════════════════════════════════════ */
function listarHistorial(): void
{
    global $conectar;
    $draw        = (int)($_POST['draw']   ?? 1);
    $start       = (int)($_POST['start']  ?? 0);
    $length      = (int)($_POST['length'] ?? 10);
    $raw         = $_POST['search'] ?? '';
    $search      = trim(is_array($raw) ? ($raw['value'] ?? '') : $raw);
    $sucursal_id = getSucursalId();

    try {
        $where  = 'WHERE t.sucursal_id = :sucursal_id';
        $params = [':sucursal_id' => $sucursal_id];

        if ($search !== '') {
            $where .= " AND (a.nombre ILIKE :s OR lo.nombre ILIKE :s OR ld.nombre ILIKE :s)";
            $params[':s'] = '%' . $search . '%';
        }

        $base = "FROM transferencia_inventario t
                 JOIN articulo a  ON a.id  = t.articulo_id
                 JOIN locacion lo ON lo.id = t.locacion_origen_id
                 JOIN locacion ld ON ld.id = t.locacion_destino_id";

        $total = (int)$conectar->prepare("SELECT COUNT(*) $base WHERE t.sucursal_id = :sucursal_id")
                               ->execute([':sucursal_id' => $sucursal_id]) ? 0 : 0;

        $stmtT = $conectar->prepare("SELECT COUNT(*) $base WHERE t.sucursal_id = :sucursal_id");
        $stmtT->execute([':sucursal_id' => $sucursal_id]);
        $total = (int)$stmtT->fetchColumn();

        $stmtF = $conectar->prepare("SELECT COUNT(*) $base $where");
        $stmtF->execute($params);
        $filtered = (int)$stmtF->fetchColumn();

        $stmtD = $conectar->prepare("
            SELECT
                t.id,
                a.nombre                                      AS nombre_articulo,
                lo.nombre                                     AS locacion_origen,
                eo.nombre                                     AS estructura_origen,
                ld.nombre                                     AS locacion_destino,
                ed.nombre                                     AS estructura_destino,
                t.cantidad, t.motivo,
                to_char(t.created_at, 'DD/MM/YYYY HH24:MI')  AS fecha
            $base
            LEFT JOIN estructura_locacion eo ON eo.id = t.estructura_origen_id
            LEFT JOIN estructura_locacion ed ON ed.id = t.estructura_destino_id
            $where
            ORDER BY t.id DESC
            LIMIT :limit OFFSET :offset
        ");
        foreach ($params as $k => $v) $stmtD->bindValue($k, $v);
        $stmtD->bindValue(':limit',  $length, PDO::PARAM_INT);
        $stmtD->bindValue(':offset', $start,  PDO::PARAM_INT);
        $stmtD->execute();
        $rows = $stmtD->fetchAll(PDO::FETCH_ASSOC);

        $data = array_map(function ($r) {
            return [
                'id'              => $r['id'],
                'nombre_articulo' => $r['nombre_articulo'],
                'origen'          => $r['locacion_origen']  . ($r['estructura_origen']  ? ' → ' . $r['estructura_origen']  : ''),
                'destino'         => $r['locacion_destino'] . ($r['estructura_destino'] ? ' → ' . $r['estructura_destino'] : ''),
                'cantidad'        => $r['cantidad'],
                'motivo'          => $r['motivo'] ?? '',
                'fecha'           => $r['fecha'],
            ];
        }, $rows);

        echo json_encode([
            'draw' => $draw, 'recordsTotal' => $total,
            'recordsFiltered' => $filtered, 'data' => $data,
        ]);

    } catch (\Throwable $th) {
        echo json_encode(['draw' => $draw, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
    }
}

/* ═════════════════════════════════════════════
   STATS
═════════════════════════════════════════════ */
function getStats(): void
{
    global $conectar;
    $sucursal_id = getSucursalId();
    try {
        $stmt = $conectar->prepare("
            SELECT
                COUNT(*) FILTER (WHERE created_at::date = CURRENT_DATE)                   AS hoy,
                COALESCE(SUM(cantidad) FILTER (WHERE created_at::date = CURRENT_DATE), 0) AS unidades,
                COUNT(*)                                                                   AS total
            FROM transferencia_inventario
            WHERE sucursal_id = :sucursal_id
        ");
        $stmt->bindValue(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
        $stmt->execute();
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode([
            'success' => true,
            'data'    => ['hoy' => (int)$r['hoy'], 'unidades' => (int)$r['unidades'], 'total' => (int)$r['total']],
        ]);
    } catch (\Throwable $th) {
        echo json_encode(['success' => false, 'data' => []]);
    }
}