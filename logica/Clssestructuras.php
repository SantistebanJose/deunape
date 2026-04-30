<?php
/**
 * clssEstructuras.php
 * Lógica CRUD para la tabla: public.estructura_locacion
 */

session_start();
include("bd.php");
$pdo = $conectar;

$accion      = $_POST['accion']      ?? '';
$sucursal_id = $_POST['sucursal_id'] ?? ($_SESSION['sucursal_id'] ?? null);
$sucursal_id = ($sucursal_id !== '' && $sucursal_id !== null) ? (int)$sucursal_id : null;

switch ($accion) {
    case 'LISTAR':            listar($pdo, $sucursal_id);            break;
    case 'LISTAR_LOCACIONES': listarLocaciones($pdo, $sucursal_id);  break;
    case 'REGISTRAR':         registrar($pdo, $sucursal_id);         break;
    case 'ACTUALIZAR':        actualizar($pdo, $sucursal_id);        break;
    case 'ELIMINAR':          eliminar($pdo, $sucursal_id);          break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no reconocida.']);
        break;
}

// ════════════════════════════════════════════════════════════════════════════════
// LISTAR
// ════════════════════════════════════════════════════════════════════════════════
function listar(PDO $pdo, ?int $sucursal_id): void
{
    $draw   = (int)($_POST['draw']   ?? 1);
    $start  = (int)($_POST['start']  ?? 0);
    $length = (int)($_POST['length'] ?? 10);
    $search = trim($_POST['search']['value'] ?? '');

    $orderableColumns = [
        0 => 'el.id',
        1 => 'el.nombre',
        2 => 'l.nombre',
        3 => 'el.tipo',
        4 => 'el.posicion',
        5 => 'el.referencia',
    ];
    $orderColIdx = (int)($_POST['order'][0]['column'] ?? 0);
    $orderCol    = $orderableColumns[$orderColIdx] ?? 'el.id';
    $orderDir    = strtoupper($_POST['order'][0]['dir'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

    $params = [];

    // WHERE con alias "el" — para queries con JOIN
    $whereSucursalEl    = '';
    // WHERE sin alias  — para queries simples (total, stats)
    $whereSucursalPlain = '';

    if ($sucursal_id !== null) {
        $whereSucursalEl    = ' AND el.sucursal_id = :sucursal_id';
        $whereSucursalPlain = ' AND sucursal_id = :sucursal_id';
        $params[':sucursal_id'] = $sucursal_id;
    }

    $whereSearch = '';
    if ($search !== '') {
        $whereSearch = " AND (
            el.nombre        ILIKE :search
            OR el.tipo       ILIKE :search
            OR el.referencia ILIKE :search
            OR l.nombre      ILIKE :search
        )";
        $params[':search'] = '%' . $search . '%';
    }

    // WHERE para queries con JOIN (filtrado + datos)
    $whereAll = "WHERE 1=1 $whereSucursalEl $whereSearch";

    // ── Total sin filtro (sin alias, sin JOIN)
    $sqlTotal = "
        SELECT COUNT(*)
        FROM estructura_locacion
        WHERE 1=1 $whereSucursalPlain
    ";
    $stmtTotal = $pdo->prepare($sqlTotal);
    if ($sucursal_id !== null) $stmtTotal->bindValue(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
    $stmtTotal->execute();
    $recordsTotal = (int)$stmtTotal->fetchColumn();

    // ── Total filtrado (con JOIN y alias el)
    $sqlFiltered = "
        SELECT COUNT(*)
        FROM estructura_locacion el
        LEFT JOIN locacion l ON l.id = el.locacion_id
        $whereAll
    ";
    $stmtFiltered = $pdo->prepare($sqlFiltered);
    foreach ($params as $k => $v) $stmtFiltered->bindValue($k, $v);
    $stmtFiltered->execute();
    $recordsFiltered = (int)$stmtFiltered->fetchColumn();

    // ── Datos paginados
    $sqlData = "
        SELECT
            el.id,
            el.nombre,
            el.tipo,
            el.posicion   AS capacidad,
            el.referencia AS codigo,
            el.locacion_id,
            l.nombre      AS nombre_locacion
        FROM estructura_locacion el
        LEFT JOIN locacion l ON l.id = el.locacion_id
        $whereAll
        ORDER BY $orderCol $orderDir
        LIMIT  :limit
        OFFSET :offset
    ";
    $stmtData = $pdo->prepare($sqlData);
    foreach ($params as $k => $v) $stmtData->bindValue($k, $v);
    $stmtData->bindValue(':limit',  $length, PDO::PARAM_INT);
    $stmtData->bindValue(':offset', $start,  PDO::PARAM_INT);
    $stmtData->execute();
    $data = $stmtData->fetchAll(PDO::FETCH_ASSOC);

    // ── Stats (sin alias, sin JOIN)
    $sqlStats = "
        SELECT
            COUNT(*)                                     AS total,
            COUNT(*) FILTER (WHERE tipo = 'ANDAMIO')    AS andamios,
            COUNT(*) FILTER (WHERE tipo = 'ESTANTE')    AS estantes,
            COUNT(DISTINCT locacion_id)                 AS locaciones
        FROM estructura_locacion
        WHERE 1=1 $whereSucursalPlain
    ";
    $stmtStats = $pdo->prepare($sqlStats);
    if ($sucursal_id !== null) $stmtStats->bindValue(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
    $stmtStats->execute();
    $stats = $stmtStats->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'draw'            => $draw,
        'recordsTotal'    => $recordsTotal,
        'recordsFiltered' => $recordsFiltered,
        'data'            => $data,
        'stats'           => $stats,
    ]);
}

// ════════════════════════════════════════════════════════════════════════════════
// LISTAR_LOCACIONES
// ════════════════════════════════════════════════════════════════════════════════
function listarLocaciones(PDO $pdo, ?int $sucursal_id): void
{
    $sql    = "SELECT id, nombre, tipo FROM locacion WHERE estado = true";
    $params = [];

    if ($sucursal_id !== null) {
        $sql .= " AND sucursal_id = :sucursal_id";
        $params[':sucursal_id'] = $sucursal_id;
    }

    $sql .= " ORDER BY nombre ASC";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) $stmt->bindValue($k, $v);
    $stmt->execute();

    echo json_encode([
        'success' => true,
        'data'    => $stmt->fetchAll(PDO::FETCH_ASSOC),
    ]);
}

// ════════════════════════════════════════════════════════════════════════════════
// REGISTRAR
// ════════════════════════════════════════════════════════════════════════════════
function registrar(PDO $pdo, ?int $sucursal_id): void
{
    $datos = json_decode($_POST['data'] ?? '{}', true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'message' => 'JSON de datos inválido.']);
        return;
    }

    $nombre      = trim($datos['nombre']      ?? '');
    $tipo        = trim($datos['tipo']        ?? '');
    $locacion_id = isset($datos['locacion_id']) && $datos['locacion_id'] !== ''
                    ? (int)$datos['locacion_id'] : null;
    $referencia  = trim($datos['codigo']      ?? '') ?: null;
    $posicion    = isset($datos['capacidad']) && $datos['capacidad'] !== ''
                    ? (int)$datos['capacidad'] : null;

    if (!$nombre || !$tipo || !$locacion_id) {
        echo json_encode(['success' => false,
                          'message' => 'Nombre, tipo y locación son obligatorios.']);
        return;
    }

    $stmt = $pdo->prepare("
        INSERT INTO estructura_locacion
            (locacion_id, sucursal_id, nombre, tipo, posicion, referencia)
        VALUES
            (:locacion_id, :sucursal_id, :nombre, :tipo, :posicion, :referencia)
        RETURNING id
    ");

    $stmt->execute([
        ':locacion_id' => $locacion_id,
        ':sucursal_id' => $sucursal_id,
        ':nombre'      => $nombre,
        ':tipo'        => $tipo,
        ':posicion'    => $posicion,
        ':referencia'  => $referencia,
    ]);

    $id = $stmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'message' => "Estructura registrada correctamente (ID: $id).",
        'id'      => (int)$id,
    ]);
}

// ════════════════════════════════════════════════════════════════════════════════
// ACTUALIZAR
// ════════════════════════════════════════════════════════════════════════════════
function actualizar(PDO $pdo, ?int $sucursal_id): void
{
    $datos = json_decode($_POST['data'] ?? '{}', true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['success' => false, 'message' => 'JSON de datos inválido.']);
        return;
    }

    $id          = isset($datos['id']) && $datos['id'] !== '' ? (int)$datos['id'] : 0;
    $nombre      = trim($datos['nombre']      ?? '');
    $tipo        = trim($datos['tipo']        ?? '');
    $locacion_id = isset($datos['locacion_id']) && $datos['locacion_id'] !== ''
                    ? (int)$datos['locacion_id'] : null;
    $referencia  = trim($datos['codigo']      ?? '') ?: null;
    $posicion    = isset($datos['capacidad']) && $datos['capacidad'] !== ''
                    ? (int)$datos['capacidad'] : null;

    if (!$id || !$nombre || !$tipo || !$locacion_id) {
        echo json_encode(['success' => false,
                          'message' => 'Datos incompletos para actualizar.']);
        return;
    }

    if ($sucursal_id !== null) {
        $check = $pdo->prepare(
            "SELECT id FROM estructura_locacion WHERE id = :id AND sucursal_id = :sucursal_id"
        );
        $check->execute([':id' => $id, ':sucursal_id' => $sucursal_id]);
        if (!$check->fetch()) {
            echo json_encode(['success' => false,
                              'message' => 'Estructura no encontrada o sin permiso.']);
            return;
        }
    }

    $sql = "
        UPDATE estructura_locacion SET
            locacion_id = :locacion_id,
            nombre      = :nombre,
            tipo        = :tipo,
            posicion    = :posicion,
            referencia  = :referencia
        WHERE id = :id
    ";
    if ($sucursal_id !== null) $sql .= " AND sucursal_id = :sucursal_id";

    $stmt = $pdo->prepare($sql);

    $bindData = [
        ':locacion_id' => $locacion_id,
        ':nombre'      => $nombre,
        ':tipo'        => $tipo,
        ':posicion'    => $posicion,
        ':referencia'  => $referencia,
        ':id'          => $id,
    ];
    if ($sucursal_id !== null) $bindData[':sucursal_id'] = $sucursal_id;

    $stmt->execute($bindData);

    echo json_encode(['success' => true, 'message' => 'Estructura actualizada correctamente.']);
}

// ════════════════════════════════════════════════════════════════════════════════
// ELIMINAR
// ════════════════════════════════════════════════════════════════════════════════
function eliminar(PDO $pdo, ?int $sucursal_id): void
{
    $id = (int)($_POST['id'] ?? 0);

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID inválido.']);
        return;
    }

    // Verificar dependencias en inventario
    $chkInv = $pdo->prepare(
        "SELECT COUNT(*) FROM inventario WHERE estructura_id = :id"
    );
    $chkInv->execute([':id' => $id]);
    if ((int)$chkInv->fetchColumn() > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'No se puede eliminar: la estructura tiene stock de inventario asociado.',
        ]);
        return;
    }

    // Verificar dependencias en transferencias
    $chkTrf = $pdo->prepare("
        SELECT COUNT(*) FROM transferencia_inventario
        WHERE estructura_origen_id = :id OR estructura_destino_id = :id
    ");
    $chkTrf->execute([':id' => $id]);
    if ((int)$chkTrf->fetchColumn() > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'No se puede eliminar: existen transferencias que referencian esta estructura.',
        ]);
        return;
    }

    $stmt = $pdo->prepare("DELETE FROM estructura_locacion WHERE id = :id");
    $stmt->execute([':id' => $id]);

    echo json_encode(['success' => true, 'message' => 'Estructura eliminada correctamente.']);
}