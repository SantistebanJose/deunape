<?php
/**
 * clssLocaciones.php
 * CRUD unificado para:
 *   - public.locacion
 *   - public.estructura_locacion
 *
 * Acciones disponibles:
 *   LISTAR_TODO    → locaciones + estructuras anidadas + stats
 *   REGISTRAR_LOC  → nueva locación
 *   ACTUALIZAR_LOC → editar locación
 *   ELIMINAR_LOC   → eliminar locación (valida dependencias)
 *   REGISTRAR_EST  → nueva estructura dentro de una locación
 *   ACTUALIZAR_EST → editar estructura
 *   ELIMINAR_EST   → eliminar estructura (valida dependencias)
 */

session_start();
include("bd.php");
$pdo = $conectar;

header('Content-Type: application/json; charset=utf-8');

$accion      = $_POST['accion']      ?? '';
$sucursal_id = $_POST['sucursal_id'] ?? ($_SESSION['sucursal_id'] ?? null);
$sucursal_id = ($sucursal_id !== '' && $sucursal_id !== null) ? (int)$sucursal_id : null;

switch ($accion) {
    case 'LISTAR_TODO':    listarTodo($pdo, $sucursal_id);    break;
    case 'REGISTRAR_LOC':  registrarLoc($pdo, $sucursal_id);  break;
    case 'ACTUALIZAR_LOC': actualizarLoc($pdo, $sucursal_id); break;
    case 'ELIMINAR_LOC':   eliminarLoc($pdo, $sucursal_id);   break;
    case 'REGISTRAR_EST':  registrarEst($pdo, $sucursal_id);  break;
    case 'ACTUALIZAR_EST': actualizarEst($pdo, $sucursal_id); break;
    case 'ELIMINAR_EST':   eliminarEst($pdo, $sucursal_id);   break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no reconocida.']);
        break;
}

/* ════════════════════════════════════════════════════════════════════
   LISTAR TODO
   Retorna todas las locaciones con sus estructuras anidadas y stats
════════════════════════════════════════════════════════════════════ */
function listarTodo(PDO $pdo, ?int $sucursal_id): void
{
    $where  = 'WHERE 1=1';
    $params = [];

    if ($sucursal_id !== null) {
        $where .= ' AND l.sucursal_id = :sucursal_id';
        $params[':sucursal_id'] = $sucursal_id;
    }

    /* ── locaciones ── */
    $sqlLoc = "
        SELECT
            l.id,
            l.nombre,
            l.descripcion,
            l.tipo,
            l.direccion,
            l.estado,
            l.sucursal_id
        FROM locacion l
        $where
        ORDER BY l.nombre ASC
    ";
    $stmtLoc = $pdo->prepare($sqlLoc);
    foreach ($params as $k => $v) $stmtLoc->bindValue($k, $v);
    $stmtLoc->execute();
    $locaciones = $stmtLoc->fetchAll(PDO::FETCH_ASSOC);

    if (empty($locaciones)) {
        echo json_encode([
            'success' => true,
            'data'    => [],
            'stats'   => ['locaciones' => 0, 'estructuras' => 0, 'andamios' => 0, 'estantes' => 0],
        ]);
        return;
    }

    /* ── estructuras de todas las locaciones de esta sucursal ── */
    $locIds      = array_column($locaciones, 'id');
    $placeholders = implode(',', array_fill(0, count($locIds), '?'));

    $sqlEst = "
        SELECT
            el.id,
            el.locacion_id,
            el.nombre,
            el.tipo,
            el.posicion,
            el.referencia
        FROM estructura_locacion el
        WHERE el.locacion_id IN ($placeholders)
        ORDER BY el.locacion_id, el.posicion ASC NULLS LAST, el.nombre ASC
    ";
    $stmtEst = $pdo->prepare($sqlEst);
    $stmtEst->execute($locIds);
    $estructuras = $stmtEst->fetchAll(PDO::FETCH_ASSOC);

    /* ── indexar estructuras por locacion_id ── */
    $estPorLoc = [];
    foreach ($estructuras as $e) {
        $estPorLoc[$e['locacion_id']][] = $e;
    }

    /* ── anidar en cada locación ── */
    foreach ($locaciones as &$loc) {
        $loc['estado']      = (bool)$loc['estado'];
        $loc['estructuras'] = $estPorLoc[$loc['id']] ?? [];
    }
    unset($loc);

    /* ── stats globales ── */
    $totalEst    = count($estructuras);
    $totalAnd    = count(array_filter($estructuras, fn($e) => $e['tipo'] === 'ANDAMIO'));
    $totalEstant = count(array_filter($estructuras, fn($e) => $e['tipo'] === 'ESTANTE'));

    echo json_encode([
        'success' => true,
        'data'    => $locaciones,
        'stats'   => [
            'locaciones'  => count($locaciones),
            'estructuras' => $totalEst,
            'andamios'    => $totalAnd,
            'estantes'    => $totalEstant,
        ],
    ]);
}

/* ════════════════════════════════════════════════════════════════════
   REGISTRAR LOCACIÓN
════════════════════════════════════════════════════════════════════ */
function registrarLoc(PDO $pdo, ?int $sucursal_id): void
{
    $datos = parsearDatos($_POST['data'] ?? '{}');
    if ($datos === null) { respuestaError('JSON inválido.'); return; }

    $nombre      = trim($datos['nombre']      ?? '');
    $tipo        = trim($datos['tipo']        ?? '');
    $direccion   = trim($datos['direccion']   ?? '') ?: null;
    $descripcion = trim($datos['descripcion'] ?? '') ?: null;
    $estado      = isset($datos['estado']) ? (bool)$datos['estado'] : true;

    if (!$nombre || !$tipo) {
        respuestaError('Nombre y tipo son obligatorios.');
        return;
    }

    if (!validarTipoLoc($tipo)) {
        respuestaError('Tipo de locación no válido.');
        return;
    }

    $stmt = $pdo->prepare("
        INSERT INTO locacion (sucursal_id, nombre, descripcion, tipo, direccion, estado)
        VALUES (:sucursal_id, :nombre, :descripcion, :tipo, :direccion, :estado)
        RETURNING id
    ");
    $stmt->execute([
        ':sucursal_id' => $sucursal_id,
        ':nombre'      => $nombre,
        ':descripcion' => $descripcion,
        ':tipo'        => $tipo,
        ':direccion'   => $direccion,
        ':estado'      => $estado ? 'true' : 'false',
    ]);

    $id = (int)$stmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'message' => "Locación registrada correctamente.",
        'id'      => $id,
    ]);
}

/* ════════════════════════════════════════════════════════════════════
   ACTUALIZAR LOCACIÓN
════════════════════════════════════════════════════════════════════ */
function actualizarLoc(PDO $pdo, ?int $sucursal_id): void
{
    $datos = parsearDatos($_POST['data'] ?? '{}');
    if ($datos === null) { respuestaError('JSON inválido.'); return; }

    $id          = isset($datos['id']) && $datos['id'] !== '' ? (int)$datos['id'] : 0;
    $nombre      = trim($datos['nombre']      ?? '');
    $tipo        = trim($datos['tipo']        ?? '');
    $direccion   = trim($datos['direccion']   ?? '') ?: null;
    $descripcion = trim($datos['descripcion'] ?? '') ?: null;
    $estado      = isset($datos['estado']) ? (bool)$datos['estado'] : true;

    if (!$id || !$nombre || !$tipo) {
        respuestaError('Datos incompletos para actualizar.');
        return;
    }

    if (!validarTipoLoc($tipo)) {
        respuestaError('Tipo de locación no válido.');
        return;
    }

    /* verificar pertenencia */
    if (!locPertenece($pdo, $id, $sucursal_id)) {
        respuestaError('Locación no encontrada o sin permiso.');
        return;
    }

    $sql = "
        UPDATE locacion SET
            nombre      = :nombre,
            tipo        = :tipo,
            descripcion = :descripcion,
            direccion   = :direccion,
            estado      = :estado
        WHERE id = :id
    ";
    if ($sucursal_id !== null) $sql .= " AND sucursal_id = :sucursal_id";

    $bind = [
        ':nombre'      => $nombre,
        ':tipo'        => $tipo,
        ':descripcion' => $descripcion,
        ':direccion'   => $direccion,
        ':estado'      => $estado ? 'true' : 'false',
        ':id'          => $id,
    ];
    if ($sucursal_id !== null) $bind[':sucursal_id'] = $sucursal_id;

    $pdo->prepare($sql)->execute($bind);

    echo json_encode(['success' => true, 'message' => 'Locación actualizada correctamente.']);
}

/* ════════════════════════════════════════════════════════════════════
   ELIMINAR LOCACIÓN
════════════════════════════════════════════════════════════════════ */
function eliminarLoc(PDO $pdo, ?int $sucursal_id): void
{
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) { respuestaError('ID inválido.'); return; }

    if (!locPertenece($pdo, $id, $sucursal_id)) {
        respuestaError('Locación no encontrada o sin permiso.');
        return;
    }

    /* verificar estructuras hijas */
    $chk = $pdo->prepare("SELECT COUNT(*) FROM estructura_locacion WHERE locacion_id = :id");
    $chk->execute([':id' => $id]);
    if ((int)$chk->fetchColumn() > 0) {
        respuestaError('No se puede eliminar: la locación tiene estructuras asociadas. Elimínalas primero.');
        return;
    }

    /* verificar inventario directo en la locación */
    $chkInv = $pdo->prepare("SELECT COUNT(*) FROM inventario WHERE locacion_id = :id");
    $chkInv->execute([':id' => $id]);
    if ((int)$chkInv->fetchColumn() > 0) {
        respuestaError('No se puede eliminar: la locación tiene inventario asociado.');
        return;
    }

    $pdo->prepare("DELETE FROM locacion WHERE id = :id")->execute([':id' => $id]);

    echo json_encode(['success' => true, 'message' => 'Locación eliminada correctamente.']);
}

/* ════════════════════════════════════════════════════════════════════
   REGISTRAR ESTRUCTURA
════════════════════════════════════════════════════════════════════ */
function registrarEst(PDO $pdo, ?int $sucursal_id): void
{
    $datos = parsearDatos($_POST['data'] ?? '{}');
    if ($datos === null) { respuestaError('JSON inválido.'); return; }

    $nombre      = trim($datos['nombre']     ?? '');
    $tipo        = trim($datos['tipo']       ?? '');
    $locacion_id = isset($datos['locacion_id']) && $datos['locacion_id'] !== ''
                    ? (int)$datos['locacion_id'] : null;
    $referencia  = trim($datos['referencia'] ?? '') ?: null;
    $posicion    = isset($datos['posicion']) && $datos['posicion'] !== ''
                    ? (int)$datos['posicion'] : null;

    if (!$nombre || !$tipo || !$locacion_id) {
        respuestaError('Nombre, tipo y locación son obligatorios.');
        return;
    }

    if (!validarTipoEst($tipo)) {
        respuestaError('Tipo de estructura no válido.');
        return;
    }

    /* verificar que la locación pertenece a esta sucursal */
    if (!locPertenece($pdo, $locacion_id, $sucursal_id)) {
        respuestaError('Locación no encontrada o sin permiso.');
        return;
    }

    $stmt = $pdo->prepare("
        INSERT INTO estructura_locacion (locacion_id, sucursal_id, nombre, tipo, posicion, referencia)
        VALUES (:locacion_id, :sucursal_id, :nombre, :tipo, :posicion, :referencia)
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

    $id = (int)$stmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'message' => 'Estructura registrada correctamente.',
        'id'      => $id,
    ]);
}

/* ════════════════════════════════════════════════════════════════════
   ACTUALIZAR ESTRUCTURA
════════════════════════════════════════════════════════════════════ */
function actualizarEst(PDO $pdo, ?int $sucursal_id): void
{
    $datos = parsearDatos($_POST['data'] ?? '{}');
    if ($datos === null) { respuestaError('JSON inválido.'); return; }

    $id          = isset($datos['id']) && $datos['id'] !== '' ? (int)$datos['id'] : 0;
    $nombre      = trim($datos['nombre']     ?? '');
    $tipo        = trim($datos['tipo']       ?? '');
    $locacion_id = isset($datos['locacion_id']) && $datos['locacion_id'] !== ''
                    ? (int)$datos['locacion_id'] : null;
    $referencia  = trim($datos['referencia'] ?? '') ?: null;
    $posicion    = isset($datos['posicion']) && $datos['posicion'] !== ''
                    ? (int)$datos['posicion'] : null;

    if (!$id || !$nombre || !$tipo || !$locacion_id) {
        respuestaError('Datos incompletos para actualizar.');
        return;
    }

    if (!validarTipoEst($tipo)) {
        respuestaError('Tipo de estructura no válido.');
        return;
    }

    /* verificar pertenencia */
    if ($sucursal_id !== null) {
        $chk = $pdo->prepare(
            "SELECT id FROM estructura_locacion WHERE id = :id AND sucursal_id = :sid"
        );
        $chk->execute([':id' => $id, ':sid' => $sucursal_id]);
        if (!$chk->fetch()) {
            respuestaError('Estructura no encontrada o sin permiso.');
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

    $bind = [
        ':locacion_id' => $locacion_id,
        ':nombre'      => $nombre,
        ':tipo'        => $tipo,
        ':posicion'    => $posicion,
        ':referencia'  => $referencia,
        ':id'          => $id,
    ];
    if ($sucursal_id !== null) $bind[':sucursal_id'] = $sucursal_id;

    $pdo->prepare($sql)->execute($bind);

    echo json_encode(['success' => true, 'message' => 'Estructura actualizada correctamente.']);
}

/* ════════════════════════════════════════════════════════════════════
   ELIMINAR ESTRUCTURA
════════════════════════════════════════════════════════════════════ */
function eliminarEst(PDO $pdo, ?int $sucursal_id): void
{
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) { respuestaError('ID inválido.'); return; }

    /* verificar inventario */
    $chkInv = $pdo->prepare("SELECT COUNT(*) FROM inventario WHERE estructura_id = :id");
    $chkInv->execute([':id' => $id]);
    if ((int)$chkInv->fetchColumn() > 0) {
        respuestaError('No se puede eliminar: la estructura tiene inventario asociado.');
        return;
    }

    /* verificar transferencias */
    $chkTrf = $pdo->prepare("
        SELECT COUNT(*) FROM transferencia_inventario
        WHERE estructura_origen_id = :id OR estructura_destino_id = :id
    ");
    $chkTrf->execute([':id' => $id]);
    if ((int)$chkTrf->fetchColumn() > 0) {
        respuestaError('No se puede eliminar: existen transferencias que referencian esta estructura.');
        return;
    }

    $sql = "DELETE FROM estructura_locacion WHERE id = :id";
    if ($sucursal_id !== null) $sql .= " AND sucursal_id = :sucursal_id";

    $bind = [':id' => $id];
    if ($sucursal_id !== null) $bind[':sucursal_id'] = $sucursal_id;

    $pdo->prepare($sql)->execute($bind);

    echo json_encode(['success' => true, 'message' => 'Estructura eliminada correctamente.']);
}

/* ════════════════════════════════════════════════════════════════════
   HELPERS
════════════════════════════════════════════════════════════════════ */

/** Parsear JSON del POST['data'] */
function parsearDatos(string $raw): ?array
{
    $datos = json_decode($raw, true);
    return json_last_error() === JSON_ERROR_NONE ? $datos : null;
}

/** Respuesta de error estándar */
function respuestaError(string $msg): void
{
    echo json_encode(['success' => false, 'message' => $msg]);
}

/** Verificar que tipo de locación sea válido según el CHECK constraint */
function validarTipoLoc(string $tipo): bool
{
    return in_array($tipo, ['SUCURSAL', 'ALMACEN', 'PUNTO_VENTA'], true);
}

/** Verificar que tipo de estructura sea válido */
function validarTipoEst(string $tipo): bool
{
    return in_array($tipo, ['ANDAMIO', 'ESTANTE', 'OTRO'], true);
}

/**
 * Verificar que una locación existe y pertenece a la sucursal.
 * Si $sucursal_id es null (sin multitenancy), solo verifica existencia.
 */
function locPertenece(PDO $pdo, int $locId, ?int $sucursal_id): bool
{
    $sql = "SELECT id FROM locacion WHERE id = :id";
    if ($sucursal_id !== null) $sql .= " AND sucursal_id = :sucursal_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $locId, PDO::PARAM_INT);
    if ($sucursal_id !== null) $stmt->bindValue(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
    $stmt->execute();
    return (bool)$stmt->fetch();
}