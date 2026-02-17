<?php
include("bd.php");

if (isset($_POST["accion"])){
    $accion = $_POST["accion"];
    controladorFiltro($accion);
}

function controladorFiltro($accion){
    switch($accion){
        case 'FILTROPERSONA':
            $data = $_POST["data"];
            $sucursal_id = $_POST["sucursal_id"];
            consultapersonaventa($data);
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

function consultapersonaventa($data, $sucursal_id): void
{
    global $conectar;

    try {
        $query = $conectar->prepare("
            SELECT DISTINCT
                p.id, 
                CONCAT(p.numero_documento, ' - ', p.nombres, ' ', p.apellidos, p.razon_social) AS persona_concatenada,
                p.nombres,
                p.apellidos,
                p.numero_documento,
                COALESCE(NULLIF(p.telefonomovil, ''), 'Sin número') AS telefonomovil,
                COALESCE(NULLIF(p.email, ''), 'Sin correo') AS email
            FROM persona p
            INNER JOIN venta v ON v.persona_id = p.id
            WHERE 
                v.sucursal_id = :sucursal_id
                AND (
                    p.numero_documento ILIKE :data OR
                    p.nombres         ILIKE :data OR
                    p.apellidos       ILIKE :data
                )
            LIMIT 10;
        ");

        $likeData = '%' . $data . '%';

        $query->bindValue(':data',        $likeData,    PDO::PARAM_STR);
        $query->bindValue(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
        $query->execute();
        
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);

    } catch (\Throwable $th) {
        echo json_encode([
            "error"   => true,
            "message" => $th->getMessage()
        ]);
    }
}

function consultarPersonaSinSerEmpleado($data): void
{
    global $conectar;

    try {
        // Consulta SQL con la función ILIKE para hacer la comparación insensible a mayúsculas y minúsculas
        $query = $conectar->prepare("
        SELECT 
            p.id, 
            condicion,
            CONCAT(p.numero_documento, ' - ', p.nombres, ' ', p.apellidos) AS persona_concatenada,
            p.nombres,
            p.apellidos,
            p.numero_documento,
            COALESCE(NULLIF(p.telefonomovil, ''), 'Sin número') AS telefonomovil,
            COALESCE(NULLIF(p.email, ''), 'Sin correo') AS email
        FROM persona p
        LEFT JOIN usuario u ON u.persona_id=p.id
        WHERE u.persona_id is null
        AND condicion IN ('CLIENTE')
        AND numero_documento ILIKE :data 
        LIMIT 10;
        ");

        
        $likeData = '%' . $data . '%';  // Añadimos los comodines en PHP

        
        $query->bindValue(':data', $likeData, PDO::PARAM_STR);
        $query->execute();
        
        
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        // Devolver los resultados como JSON
        echo json_encode($result);
    } catch (\Throwable $th) {
        // En caso de error, devolver un JSON con el mensaje de error
        echo json_encode([
            "error" => true,
            "message" => $th->getMessage()
        ]);
    }
}

function consultapersonaempleado($data): void
{
    global $conectar;

    try {
        // Consulta SQL con la función LOWER para hacer la comparación insensible a mayúsculas y minúsculas
        $query = $conectar->prepare("
            SELECT id, 
                CONCAT(numero_documento, ' - ', nombres, ' ', apellidos) AS persona_concatenada,
                COALESCE(NULLIF(telefonomovil, ''), 'Sin número') AS telefonomovil,
                COALESCE(NULLIF(email, ''), 'Sin correo') AS email
            FROM persona
            WHERE condicion = 'EMPLEADO' 
            AND (LOWER(numero_documento) LIKE LOWER(:data)
                OR LOWER(nombres) LIKE LOWER(:data)
                OR LOWER(apellidos) LIKE LOWER(:data))
            LIMIT 10;

        ");

        // Pasamos el parámetro con los signos de porcentaje en PHP
        $likeData = "%" . $data . "%";

        // Usamos bindValue para pasar el parámetro
        $query->bindValue(':data', $likeData, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        // Devolver los resultados como JSON
        echo json_encode($result);
    } catch (\Throwable $th) {
        // En caso de error, devolver un JSON con el mensaje
        echo json_encode([
            "error" => true,
            "message" => $th->getMessage()
        ]);
    }
}

