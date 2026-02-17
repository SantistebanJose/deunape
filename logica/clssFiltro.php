<?php
// clssFiltro.php
session_start();
include("bd.php");

if (isset($_POST["accion"])) {
    $accion = $_POST["accion"];
    controladorFiltro($accion);
}

function controladorFiltro($accion)
{
    switch ($accion) {
        case 'FILTROPERSONA':
            $data        = $_POST["data"];
            // Prioridad: POST → sesión → null
            $sucursal_id = isset($_POST['sucursal_id']) && $_POST['sucursal_id'] !== ''
                ? intval($_POST['sucursal_id'])
                : (isset($_SESSION['sucursal_id']) && $_SESSION['sucursal_id'] !== ''
                    ? intval($_SESSION['sucursal_id'])
                    : null);
            consultapersonaventa($data, $sucursal_id);
            break;

        case 'FILTROEMPLEADO':
            $data = $_POST["data"];
            consultapersonaempleado($data);
            break;

        case 'FILTROPERSONASINSEREMPLEADO':
            $data = $_POST["data"];
            consultarPersonaSinSerEmpleado($data);
            break;

        case 'CAMBIARCONTRASEÑA':
            break;
    }
}

/**
 * Busca personas por nombre/documento filtrando por sucursal.
 * Usa ? = ANY(p.sucursal_id) porque la columna sucursal_id es bigint[].
 * Ya NO filtra por venta.sucursal_id — filtra directamente en persona.
 */
function consultapersonaventa($data, $sucursal_id = null): void
{
    global $conectar;

    try {
        $likeData = '%' . $data . '%';
        $params   = [];

        // Fragmento WHERE para sucursal (solo si se recibe)
        $whereSucursal = '';
        if ($sucursal_id !== null) {
            $whereSucursal = 'AND ? = ANY(p.sucursal_id)';
            $params[]      = $sucursal_id;
        }

        // 4 parámetros de búsqueda (documento, nombres, apellidos, razon_social)
        $params = array_merge($params, [$likeData, $likeData, $likeData, $likeData]);

        $sql = "
            SELECT
                p.id,
                CASE
                    WHEN p.nombres IS NOT NULL AND p.apellidos IS NOT NULL
                        THEN CONCAT(p.numero_documento, ' - ', p.nombres, ' ', p.apellidos)
                    WHEN p.razon_social IS NOT NULL
                        THEN CONCAT(p.numero_documento, ' - ', p.razon_social)
                    ELSE p.numero_documento
                END AS persona_concatenada,
                p.nombres,
                p.apellidos,
                p.numero_documento,
                COALESCE(NULLIF(p.telefonomovil, ''), 'Sin número') AS telefonomovil,
                COALESCE(NULLIF(p.email, ''),         'Sin correo')  AS email
            FROM persona p
            WHERE p.deleted_at IS NULL
            $whereSucursal
            AND (
                p.numero_documento ILIKE ?
                OR p.nombres       ILIKE ?
                OR p.apellidos     ILIKE ?
                OR p.razon_social  ILIKE ?
            )
            ORDER BY persona_concatenada
            LIMIT 10
        ";

        $query = $conectar->prepare($sql);
        $query->execute($params);

        echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));

    } catch (\Throwable $th) {
        error_log("Error en consultapersonaventa: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

/**
 * Personas que aún no son empleados (sin usuario asociado).
 * Sin filtro de sucursal — se usa para asignación global de empleados.
 */
function consultarPersonaSinSerEmpleado($data): void
{
    global $conectar;

    try {
        $query = $conectar->prepare("
            SELECT
                p.id,
                p.condicion,
                CONCAT(p.numero_documento, ' - ', p.nombres, ' ', p.apellidos) AS persona_concatenada,
                p.nombres,
                p.apellidos,
                p.numero_documento,
                COALESCE(NULLIF(p.telefonomovil, ''), 'Sin número') AS telefonomovil,
                COALESCE(NULLIF(p.email, ''),         'Sin correo')  AS email
            FROM persona p
            LEFT JOIN usuario u ON u.persona_id = p.id
            WHERE u.persona_id IS NULL
              AND p.condicion IN ('CLIENTE')
              AND p.numero_documento ILIKE :data
            LIMIT 10
        ");

        $query->bindValue(':data', '%' . $data . '%', PDO::PARAM_STR);
        $query->execute();

        echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));

    } catch (\Throwable $th) {
        error_log("Error en consultarPersonaSinSerEmpleado: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

/**
 * Empleados — sin filtro de sucursal.
 */
function consultapersonaempleado($data): void
{
    global $conectar;

    try {
        $query = $conectar->prepare("
            SELECT
                id,
                CONCAT(numero_documento, ' - ', nombres, ' ', apellidos) AS persona_concatenada,
                COALESCE(NULLIF(telefonomovil, ''), 'Sin número') AS telefonomovil,
                COALESCE(NULLIF(email, ''),         'Sin correo')  AS email
            FROM persona
            WHERE condicion = 'EMPLEADO'
              AND (
                  numero_documento ILIKE :data
                  OR nombres       ILIKE :data
                  OR apellidos     ILIKE :data
              )
            LIMIT 10
        ");

        $query->bindValue(':data', '%' . $data . '%', PDO::PARAM_STR);
        $query->execute();

        echo json_encode($query->fetchAll(PDO::FETCH_ASSOC));

    } catch (\Throwable $th) {
        error_log("Error en consultapersonaempleado: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}