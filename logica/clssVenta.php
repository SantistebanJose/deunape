<?php
include("bd.php");



function listarMovimientos2(): array {
    global $conectar;
    try {
        $orden = $conectar->prepare("
            SELECT 
            id, descripcion
            FROM
            movimiento
            where deleted_at is null
            ORDER BY id
        ");
        
        $orden->execute();
        $lista = $orden->fetchAll(PDO::FETCH_ASSOC);
        $orden->closeCursor();
    } catch (PDOException $e) {
        
        $lista = array(); 
    }
    
    return $lista;
}

// Se inserta la venta
if($_SERVER["REQUEST_METHOD"] == "POST") {
    insertarVenta();
}
function insertarVenta() {
    global $conectar;

    try {
        // Obtener el cuerpo de la solicitud
        $data = json_decode(file_get_contents("php://input"), true);

        //Obtengo los productos del detalle
        $detalle_venta = $data["detalleVenta"];

        //Iniciamos la transacción
        $conectar->beginTransaction();

        //Falta agregar cliente
        $sentencia_venta = $conectar->prepare("
            INSERT INTO venta (movimiento_id, usuario_id, fecha, estado_pago, total )
            VALUES (:movimientoId, :usuarioId, CURRENT_DATE, :estadoPago, :total)
        ");

        $sentencia_venta->bindParam(":movimientoId", $data["movimientoId"]);
        $sentencia_venta->bindParam(":usuarioId", $data["usuarioId"]);
        $sentencia_venta->bindValue(":estadoPago", "P"); // Modificar el estado
        $sentencia_venta->bindParam(":total", $data["total"]);

        $sentencia_venta->execute();
        $venta_id = $conectar->lastInsertId();

        //Insertar los detalles de la venta
        foreach($detalle_venta as $detalle) {
            $sentencia_detalle = $conectar->prepare("
                INSERT INTO rel_venta_articulo (venta_id, articulo_id, precio_unitario_articulo, sub_total, cantidad)
                VALUES (:ventaId, :articuloId,:precioUnitario, :subTotal, :cantidad)
            ");
            $sentencia_detalle->bindParam(":ventaId", $venta_id);
            $sentencia_detalle->bindParam(":articuloId", $detalle["id"]);
            $sentencia_detalle->bindParam(":precioUnitario", $detalle["precio_venta"]);
            $sentencia_detalle->bindParam(":subTotal", $detalle["subtotal"]);
            $sentencia_detalle->bindParam(":cantidad", $detalle["cantidad"]);
            $sentencia_detalle->execute();
        }

        $conectar->commit();

        echo json_encode(["mensaje" => "Venta insertada correctamente"]);

    } catch(PDOException $e) {
        $conectar->rollBack();
    }
}

?>