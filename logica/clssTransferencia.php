<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

include("bd.php");
session_start();

header('Content-Type: application/json; charset=utf-8');

if (isset($_POST["accion"])) {
    ob_clean();
    controladorTransferencia($_POST["accion"]);
} else {
    ob_clean();
    echo json_encode(["error" => true, "message" => "Accion no especificada."]);
}

function controladorTransferencia($accion)
{
    switch ($accion) {
        case 'LISTAR_HISTORIAL':
            listar_historial();
            break;
        case 'TRANSFERIR':
            $data = json_decode($_POST["data"], true);
            transferir_stock($data);
            break;
        case 'LISTAR_LOCACIONES':
            listar_locaciones();
            break;
        case 'LISTAR_ESTRUCTURAS':
            listar_estructuras($_POST["locacion_id"]);
            break;
        case 'LISTAR_ARTICULOS_LOCACION':
            listar_articulos_locacion($_POST["locacion_id"], $_POST["estructura_id"] ?? null);
            break;
        default:
            echo json_encode(["error" => true, "message" => "Accion desconocida."]);
    }
}

function getSucursalId()
{
    return isset($_SESSION['sucursal_id']) && $_SESSION['sucursal_id'] !== ''
        ? intval($_SESSION['sucursal_id'])
        : null;
}

function getUsuarioId()
{
    return isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : null;
}

// ============================================================
// TRANSFERIR STOCK — transacción atómica
// ============================================================
function transferir_stock($datos = [])
{
    global $conectar;

    $sucursal_id = getSucursalId();
    $usuario_id  = getUsuarioId();

    try {
        // --- Validaciones básicas ---
        $cantidad = floatval($datos['cantidad'] ?? 0);
        if ($cantidad <= 0) {
            echo json_encode(["success" => false, "message" => "La cantidad debe ser mayor a 0."]);
            return;
        }
        if ($datos['locacion_origen_id'] == $datos['locacion_destino_id']
            && ($datos['estructura_origen_id'] ?? null) == ($datos['estructura_destino_id'] ?? null)) {
            echo json_encode(["success" => false, "message" => "El origen y destino no pueden ser iguales."]);
            return;
        }

        $articulo_id          = intval($datos['articulo_id']);
        $locacion_origen_id   = intval($datos['locacion_origen_id']);
        $locacion_destino_id  = intval($datos['locacion_destino_id']);
        $estructura_origen_id  = (!empty($datos['estructura_origen_id']))  ? intval($datos['estructura_origen_id'])  : null;
        $estructura_destino_id = (!empty($datos['estructura_destino_id'])) ? intval($datos['estructura_destino_id']) : null;
        $motivo               = trim($datos['motivo'] ?? '');

        $conectar->beginTransaction();

        // --- 1. Verificar stock disponible en ORIGEN ---
        $sqlOrigen = "SELECT id, stock FROM inventario
            WHERE articulo_id  = :articulo_id
            AND   locacion_id  = :locacion_id
            AND   sucursal_id  = :sucursal_id
            AND   estructura_id " . ($estructura_origen_id === null ? "IS NULL" : "= :estructura_id");

        $stmtOrigen = $conectar->prepare($sqlOrigen);
        $stmtOrigen->bindValue(":articulo_id", $articulo_id,        PDO::PARAM_INT);
        $stmtOrigen->bindValue(":locacion_id", $locacion_origen_id, PDO::PARAM_INT);
        $stmtOrigen->bindValue(":sucursal_id", $sucursal_id,        PDO::PARAM_INT);
        if ($estructura_origen_id !== null) {
            $stmtOrigen->bindValue(":estructura_id", $estructura_origen_id, PDO::PARAM_INT);
        }
        $stmtOrigen->execute();
        $inventarioOrigen = $stmtOrigen->fetch(PDO::FETCH_ASSOC);

        if (!$inventarioOrigen) {
            $conectar->rollBack();
            echo json_encode(["success" => false, "message" => "No se encontro inventario del articulo en el origen seleccionado."]);
            return;
        }

        if (floatval($inventarioOrigen['stock']) < $cantidad) {
            $conectar->rollBack();
            echo json_encode([
                "success" => false,
                "message" => "Stock insuficiente en origen. Disponible: " . $inventarioOrigen['stock']
            ]);
            return;
        }

        // --- 2. Restar del ORIGEN ---
        $stmtRestar = $conectar->prepare("UPDATE inventario SET stock = stock - :cantidad WHERE id = :id");
        $stmtRestar->bindValue(":cantidad", $cantidad);
        $stmtRestar->bindValue(":id", intval($inventarioOrigen['id']), PDO::PARAM_INT);
        $stmtRestar->execute();

        // --- 3. Buscar si ya existe inventario en DESTINO ---
        $sqlDestino = "SELECT id, stock FROM inventario
            WHERE articulo_id  = :articulo_id
            AND   locacion_id  = :locacion_id
            AND   sucursal_id  = :sucursal_id
            AND   estructura_id " . ($estructura_destino_id === null ? "IS NULL" : "= :estructura_id");

        $stmtDestino = $conectar->prepare($sqlDestino);
        $stmtDestino->bindValue(":articulo_id", $articulo_id,         PDO::PARAM_INT);
        $stmtDestino->bindValue(":locacion_id", $locacion_destino_id, PDO::PARAM_INT);
        $stmtDestino->bindValue(":sucursal_id", $sucursal_id,         PDO::PARAM_INT);
        if ($estructura_destino_id !== null) {
            $stmtDestino->bindValue(":estructura_id", $estructura_destino_id, PDO::PARAM_INT);
        }
        $stmtDestino->execute();
        $inventarioDestino = $stmtDestino->fetch(PDO::FETCH_ASSOC);

        if ($inventarioDestino) {
            // Ya existe → sumar stock
            $stmtSumar = $conectar->prepare("UPDATE inventario SET stock = stock + :cantidad WHERE id = :id");
            $stmtSumar->bindValue(":cantidad", $cantidad);
            $stmtSumar->bindValue(":id", intval($inventarioDestino['id']), PDO::PARAM_INT);
            $stmtSumar->execute();
        } else {
            // No existe → crear registro en destino
            $stmtInsertar = $conectar->prepare("INSERT INTO inventario
                (locacion_id, sucursal_id, estructura_id, articulo_id, stock)
                VALUES (:locacion_id, :sucursal_id, :estructura_id, :articulo_id, :stock)");
            $stmtInsertar->bindValue(":locacion_id",   $locacion_destino_id,  PDO::PARAM_INT);
            $stmtInsertar->bindValue(":sucursal_id",   $sucursal_id,          PDO::PARAM_INT);
            $stmtInsertar->bindValue(":estructura_id", $estructura_destino_id, $estructura_destino_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmtInsertar->bindValue(":articulo_id",   $articulo_id,          PDO::PARAM_INT);
            $stmtInsertar->bindValue(":stock",         $cantidad);
            $stmtInsertar->execute();
        }

        // --- 4. Registrar en historial ---
        $stmtHistorial = $conectar->prepare("INSERT INTO transferencia_inventario
            (sucursal_id, articulo_id,
             locacion_origen_id, estructura_origen_id,
             locacion_destino_id, estructura_destino_id,
             cantidad, motivo, usuario_id)
            VALUES
            (:sucursal_id, :articulo_id,
             :locacion_origen_id, :estructura_origen_id,
             :locacion_destino_id, :estructura_destino_id,
             :cantidad, :motivo, :usuario_id)");

        $stmtHistorial->bindValue(":sucursal_id",           $sucursal_id,            PDO::PARAM_INT);
        $stmtHistorial->bindValue(":articulo_id",           $articulo_id,            PDO::PARAM_INT);
        $stmtHistorial->bindValue(":locacion_origen_id",    $locacion_origen_id,     PDO::PARAM_INT);
        $stmtHistorial->bindValue(":estructura_origen_id",  $estructura_origen_id,   $estructura_origen_id  === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmtHistorial->bindValue(":locacion_destino_id",   $locacion_destino_id,    PDO::PARAM_INT);
        $stmtHistorial->bindValue(":estructura_destino_id", $estructura_destino_id,  $estructura_destino_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmtHistorial->bindValue(":cantidad",              $cantidad);
        $stmtHistorial->bindValue(":motivo",                $motivo ?: null,         $motivo ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmtHistorial->bindValue(":usuario_id",            $usuario_id,             $usuario_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmtHistorial->execute();

        $conectar->commit();
        echo json_encode(["success" => true, "message" => "Transferencia realizada correctamente."]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en transferir_stock: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

// ============================================================
// HISTORIAL — DataTables server-side
// ============================================================
function listar_historial()
{
    global $conectar;

    $draw      = intval($_POST['draw']   ?? 1);
    $start     = intval($_POST['start']  ?? 0);
    $length    = intval($_POST['length'] ?? 10);
    $searchRaw = $_POST['search'] ?? '';
    $search    = trim(is_array($searchRaw) ? ($searchRaw['value'] ?? '') : $searchRaw);

    $sucursal_id = getSucursalId();

    try {
        $whereBase = "WHERE t.sucursal_id = :sucursal_id";
        $params    = [':sucursal_id' => $sucursal_id];

        if ($search !== '') {
            $whereBase .= " AND (
                a.nombre ILIKE :search
                OR lo.nombre ILIKE :search
                OR ld.nombre ILIKE :search
            )";
            $params[':search'] = "%" . $search . "%";
        }

        $stmtTotal = $conectar->prepare("SELECT COUNT(*) FROM transferencia_inventario t
            JOIN articulo a ON a.id = t.articulo_id
            JOIN locacion lo ON lo.id = t.locacion_origen_id
            JOIN locacion ld ON ld.id = t.locacion_destino_id
            WHERE t.sucursal_id = :sucursal_id");
        $stmtTotal->execute([':sucursal_id' => $sucursal_id]);
        $totalRecords = (int) $stmtTotal->fetchColumn();

        $stmtFiltered = $conectar->prepare("SELECT COUNT(*) FROM transferencia_inventario t
            JOIN articulo a ON a.id = t.articulo_id
            JOIN locacion lo ON lo.id = t.locacion_origen_id
            JOIN locacion ld ON ld.id = t.locacion_destino_id
            $whereBase");
        $stmtFiltered->execute($params);
        $totalFiltered = (int) $stmtFiltered->fetchColumn();

        $sql = "SELECT
                t.id,
                a.nombre                AS nombre_articulo,
                lo.nombre               AS locacion_origen,
                eo.nombre               AS estructura_origen,
                ld.nombre               AS locacion_destino,
                ed.nombre               AS estructura_destino,
                t.cantidad,
                t.motivo,
                to_char(t.created_at, 'DD/MM/YYYY HH24:MI') AS fecha
            FROM transferencia_inventario t
            JOIN articulo a  ON a.id  = t.articulo_id
            JOIN locacion lo ON lo.id = t.locacion_origen_id
            JOIN locacion ld ON ld.id = t.locacion_destino_id
            LEFT JOIN estructura_locacion eo ON eo.id = t.estructura_origen_id
            LEFT JOIN estructura_locacion ed ON ed.id = t.estructura_destino_id
            $whereBase
            ORDER BY t.id DESC
            LIMIT :limit OFFSET :offset";

        $stmt = $conectar->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit',  $length, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $start,  PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = [];
        foreach ($rows as $row) {
            $origen  = $row['locacion_origen']  . ($row['estructura_origen']  ? ' → ' . $row['estructura_origen']  : '');
            $destino = $row['locacion_destino'] . ($row['estructura_destino'] ? ' → ' . $row['estructura_destino'] : '');
            $data[] = [
                "id"              => $row['id'],
                "nombre_articulo" => $row['nombre_articulo'],
                "origen"          => $origen,
                "destino"         => $destino,
                "cantidad"        => '<span class="badge bg-primary">' . $row['cantidad'] . '</span>',
                "motivo"          => $row['motivo'] ?? '<span class="text-muted">—</span>',
                "fecha"           => $row['fecha']
            ];
        }

        echo json_encode([
            "draw"            => $draw,
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data
        ]);

    } catch (\Throwable $th) {
        echo json_encode([
            "draw" => $draw, "recordsTotal" => 0,
            "recordsFiltered" => 0, "data" => [],
            "error" => $th->getMessage()
        ]);
    }
}

// ============================================================
// ARTÍCULOS CON STOCK EN UNA LOCACIÓN/ESTRUCTURA
// ============================================================
function listar_articulos_locacion($locacion_id, $estructura_id = null)
{
    global $conectar;
    $sucursal_id = getSucursalId();
    try {
        $estructura_id = ($estructura_id === '' || $estructura_id === null) ? null : intval($estructura_id);

        $sql = "SELECT i.id AS inventario_id, a.id AS articulo_id, a.nombre, i.stock
            FROM inventario i
            JOIN articulo a ON a.id = i.articulo_id
            WHERE i.locacion_id  = :locacion_id
            AND   i.sucursal_id  = :sucursal_id
            AND   i.stock > 0
            AND   i.estructura_id " . ($estructura_id === null ? "IS NULL" : "= :estructura_id") . "
            ORDER BY a.nombre";

        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(":locacion_id",  intval($locacion_id), PDO::PARAM_INT);
        $stmt->bindValue(":sucursal_id",  $sucursal_id,         PDO::PARAM_INT);
        if ($estructura_id !== null) {
            $stmt->bindValue(":estructura_id", $estructura_id, PDO::PARAM_INT);
        }
        $stmt->execute();
        echo json_encode(["success" => true, "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } catch (\Throwable $th) {
        echo json_encode(["success" => false, "error" => $th->getMessage()]);
    }
}

// ============================================================
// LOCACIONES Y ESTRUCTURAS (reutilizadas)
// ============================================================
function listar_locaciones()
{
    global $conectar;
    $sucursal_id = getSucursalId();
    try {
        $stmt = $conectar->prepare("SELECT id, nombre, tipo FROM locacion
            WHERE sucursal_id = :sucursal_id AND estado = true ORDER BY nombre");
        $stmt->bindValue(":sucursal_id", $sucursal_id, PDO::PARAM_INT);
        $stmt->execute();
        echo json_encode(["success" => true, "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } catch (\Throwable $th) {
        echo json_encode(["success" => false, "error" => $th->getMessage()]);
    }
}

function listar_estructuras($locacion_id)
{
    global $conectar;
    try {
        $stmt = $conectar->prepare("SELECT id, nombre, tipo FROM estructura_locacion
            WHERE locacion_id = :locacion_id ORDER BY posicion, nombre");
        $stmt->bindValue(":locacion_id", intval($locacion_id), PDO::PARAM_INT);
        $stmt->execute();
        echo json_encode(["success" => true, "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } catch (\Throwable $th) {
        echo json_encode(["success" => false, "error" => $th->getMessage()]);
    }
}