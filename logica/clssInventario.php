<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

include("bd.php");
session_start();

header('Content-Type: application/json; charset=utf-8');

if (isset($_POST["accion"])) {
    ob_clean();
    $accion = $_POST["accion"];
    controladorInventario($accion);
} else {
    ob_clean();
    echo json_encode(["error" => true, "message" => "Accion no especificada."]);
}

function controladorInventario($accion)
{
    switch ($accion) {
        case 'LISTAR':
            listar_inventario();
            break;
        case 'OBTENER':
            $id = $_POST["id"];
            obtener_inventario($id);
            break;
        case 'REGISTRAR':
            $data = json_decode($_POST["data"], true);
            registrar_inventario($data);
            break;
        case 'ACTUALIZAR':
            $data = json_decode($_POST["data"], true);
            actualizar_inventario($data);
            break;
        case 'ELIMINAR':
            $id = $_POST["id"];
            eliminar_inventario($id);
            break;
        case 'BUSCAR_ARTICULO':
            $term        = $_POST["term"]        ?? '';
            $sucursal_id = $_POST["sucursal_id"] ?? '';
            buscar_articulo($term, $sucursal_id);
            break;
        case 'LISTAR_LOCACIONES':
            listar_locaciones();
            break;
        case 'LISTAR_ESTRUCTURAS':
            $locacion_id = $_POST["locacion_id"];
            listar_estructuras($locacion_id);
            break;
        default:
            echo json_encode(["error" => true, "message" => "Accion desconocida: $accion"]);
    }
}

function getSucursalId()
{
    return isset($_SESSION['sucursal_id']) && $_SESSION['sucursal_id'] !== ''
        ? intval($_SESSION['sucursal_id'])
        : null;
}

function listar_inventario()
{
    global $conectar;

    $draw   = intval($_POST['draw']   ?? 1);
    $start  = intval($_POST['start']  ?? 0);
    $length = intval($_POST['length'] ?? 10);

    // search puede llegar como array o string segun la version de DataTables
    $searchRaw = $_POST['search'] ?? '';
    $search    = trim(is_array($searchRaw) ? ($searchRaw['value'] ?? '') : $searchRaw);

    $sucursal_id = getSucursalId();

    try {
        $whereBase = "WHERE i.sucursal_id = :sucursal_id";
        $params    = [':sucursal_id' => $sucursal_id];

        if ($search !== '') {
            $whereBase .= " AND (
                a.nombre ILIKE :search
                OR a.codigo ILIKE :search
                OR l.nombre ILIKE :search
                OR el.nombre ILIKE :search
            )";
            $params[':search'] = "%" . $search . "%";
        }

        // Total sin filtro
        $stmtTotal = $conectar->prepare("
            SELECT COUNT(*) FROM inventario i
            JOIN articulo a ON a.id = i.articulo_id
            JOIN locacion  l ON l.id = i.locacion_id
            LEFT JOIN estructura_locacion el ON el.id = i.estructura_id
            WHERE i.sucursal_id = :sucursal_id
        ");
        $stmtTotal->execute([':sucursal_id' => $sucursal_id]);
        $totalRecords = (int) $stmtTotal->fetchColumn();

        // Total filtrado
        $stmtFiltered = $conectar->prepare("
            SELECT COUNT(*) FROM inventario i
            JOIN articulo a ON a.id = i.articulo_id
            JOIN locacion  l ON l.id = i.locacion_id
            LEFT JOIN estructura_locacion el ON el.id = i.estructura_id
            $whereBase
        ");
        $stmtFiltered->execute($params);
        $totalFiltered = (int) $stmtFiltered->fetchColumn();

        // Datos paginados
        $sql = "
            SELECT
                i.id,
                a.id      AS articulo_id,
                a.codigo  AS codigo_articulo,
                a.nombre  AS nombre_articulo,
                l.id      AS locacion_id,
                l.nombre  AS nombre_locacion,
                el.id     AS estructura_id,
                el.nombre AS nombre_estructura,
                el.tipo   AS tipo_estructura,
                i.stock
            FROM inventario i
            JOIN articulo a ON a.id = i.articulo_id
            JOIN locacion  l ON l.id = i.locacion_id
            LEFT JOIN estructura_locacion el ON el.id = i.estructura_id
            $whereBase
            ORDER BY i.id DESC
            LIMIT :limit OFFSET :offset
        ";

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
            $rowJson = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
            $data[] = [
                "id"                => $row['id'],
                "codigo_articulo"   => $row['codigo_articulo'],
                "nombre_articulo"   => $row['nombre_articulo'],
                "nombre_locacion"   => $row['nombre_locacion'],
                "nombre_estructura" => $row['nombre_estructura'] ?? '<span class="text-muted">Sin estructura</span>',
                "tipo_estructura"   => $row['tipo_estructura']   ?? '-',
                "stock"             => '<span class="badge ' . ($row['stock'] > 0 ? 'bg-success' : 'bg-danger') . '">' . $row['stock'] . '</span>',
                "acciones"          => '
                    <button class="btn btn-warning btn-sm rounded-5 me-1" onclick="fn_editar_inventario(\'' . $rowJson . '\')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm rounded-5" onclick="fn_eliminar_inventario(' . $row['id'] . ')">
                        <i class="fas fa-trash"></i>
                    </button>
                '
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
            "draw"            => $draw,
            "recordsTotal"    => 0,
            "recordsFiltered" => 0,
            "data"            => [],
            "error"           => $th->getMessage()
        ]);
    }
}

function obtener_inventario($id)
{
    global $conectar;
    try {
        $stmt = $conectar->prepare("
            SELECT i.*,
                a.nombre AS nombre_articulo,
                a.codigo AS codigo_articulo,
                l.nombre AS nombre_locacion,
                el.nombre AS nombre_estructura
            FROM inventario i
            JOIN articulo a ON a.id = i.articulo_id
            JOIN locacion  l ON l.id = i.locacion_id
            LEFT JOIN estructura_locacion el ON el.id = i.estructura_id
            WHERE i.id = :id
        ");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            echo json_encode(["success" => true, "data" => $row]);
        } else {
            echo json_encode(["success" => false, "message" => "Registro no encontrado."]);
        }
    } catch (\Throwable $th) {
        echo json_encode(["success" => false, "error" => $th->getMessage()]);
    }
}

function registrar_inventario($datos = [])
{
    global $conectar;
    try {
        $sucursal_id   = getSucursalId();
        $estructura_id = (!isset($datos['estructura_id']) || $datos['estructura_id'] === '' || $datos['estructura_id'] === null)
            ? null : intval($datos['estructura_id']);

        $sqlCheck = "SELECT id FROM inventario
            WHERE articulo_id = :articulo_id
            AND   locacion_id = :locacion_id
            AND   sucursal_id = :sucursal_id
            AND   estructura_id " . ($estructura_id === null ? "IS NULL" : "= :estructura_id");

        $stmtCheck = $conectar->prepare($sqlCheck);
        $stmtCheck->bindValue(":articulo_id", intval($datos['articulo_id']), PDO::PARAM_INT);
        $stmtCheck->bindValue(":locacion_id", intval($datos['locacion_id']), PDO::PARAM_INT);
        $stmtCheck->bindValue(":sucursal_id", $sucursal_id,                 PDO::PARAM_INT);
        if ($estructura_id !== null) {
            $stmtCheck->bindValue(":estructura_id", $estructura_id, PDO::PARAM_INT);
        }
        $stmtCheck->execute();

        if ($stmtCheck->fetch()) {
            echo json_encode(["success" => false, "message" => "Este articulo ya tiene inventario en esa ubicacion. Use Editar para modificar el stock."]);
            return;
        }

        $stmt = $conectar->prepare("
            INSERT INTO inventario (locacion_id, sucursal_id, estructura_id, articulo_id, stock)
            VALUES (:locacion_id, :sucursal_id, :estructura_id, :articulo_id, :stock)
        ");
        $stmt->bindValue(":locacion_id",   intval($datos['locacion_id']), PDO::PARAM_INT);
        $stmt->bindValue(":sucursal_id",   $sucursal_id,                  PDO::PARAM_INT);
        $stmt->bindValue(":estructura_id", $estructura_id,                $estructura_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(":articulo_id",   intval($datos['articulo_id']), PDO::PARAM_INT);
        $stmt->bindValue(":stock",         $datos['stock']);
        $stmt->execute();

        echo json_encode(["success" => true, "id" => $conectar->lastInsertId(), "message" => "Inventario registrado correctamente."]);

    } catch (\Throwable $th) {
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function actualizar_inventario($datos = [])
{
    global $conectar;
    try {
        $estructura_id = (!isset($datos['estructura_id']) || $datos['estructura_id'] === '' || $datos['estructura_id'] === null)
            ? null : intval($datos['estructura_id']);

        $stmt = $conectar->prepare("
            UPDATE inventario SET
                locacion_id   = :locacion_id,
                estructura_id = :estructura_id,
                articulo_id   = :articulo_id,
                stock         = :stock
            WHERE id = :id
        ");
        $stmt->bindValue(":id",            intval($datos['id']),           PDO::PARAM_INT);
        $stmt->bindValue(":locacion_id",   intval($datos['locacion_id']),  PDO::PARAM_INT);
        $stmt->bindValue(":estructura_id", $estructura_id,                 $estructura_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(":articulo_id",   intval($datos['articulo_id']),  PDO::PARAM_INT);
        $stmt->bindValue(":stock",         $datos['stock']);
        $stmt->execute();

        echo json_encode(["success" => true, "message" => "Inventario actualizado correctamente."]);

    } catch (\Throwable $th) {
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function eliminar_inventario($id)
{
    global $conectar;
    try {
        $stmt = $conectar->prepare("DELETE FROM inventario WHERE id = :id");
        $stmt->bindValue(":id", intval($id), PDO::PARAM_INT);
        $stmt->execute();
        echo json_encode(["success" => true, "message" => "Registro eliminado correctamente."]);
    } catch (\Throwable $th) {
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function buscar_articulo($term, $sucursal_id)
{
    global $conectar;
    try {
        $stmt = $conectar->prepare("
            SELECT id, codigo, nombre, precio_venta AS precio
            FROM articulo
            WHERE sucursal_id = :sucursal_id
              AND (nombre ILIKE :term OR codigo ILIKE :term)
              AND estado = true
            LIMIT 10
        ");
        $stmt->bindValue(":sucursal_id", intval($sucursal_id), PDO::PARAM_INT);
        $stmt->bindValue(":term", "%" . $term . "%");
        $stmt->execute();
        echo json_encode(["success" => true, "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } catch (\Throwable $th) {
        echo json_encode(["success" => false, "error" => $th->getMessage()]);
    }
}

function listar_locaciones()
{
    global $conectar;
    $sucursal_id = getSucursalId();
    try {
        $stmt = $conectar->prepare("
            SELECT id, nombre, tipo FROM locacion
            WHERE sucursal_id = :sucursal_id AND estado = true
            ORDER BY nombre
        ");
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
        $stmt = $conectar->prepare("
            SELECT id, nombre, tipo, posicion FROM estructura_locacion
            WHERE locacion_id = :locacion_id
            ORDER BY posicion, nombre
        ");
        $stmt->bindValue(":locacion_id", intval($locacion_id), PDO::PARAM_INT);
        $stmt->execute();
        echo json_encode(["success" => true, "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } catch (\Throwable $th) {
        echo json_encode(["success" => false, "error" => $th->getMessage()]);
    }
}