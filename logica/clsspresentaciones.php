<?php
include("bd.php");

if (isset($_POST["accion"])) {
    $accion = $_POST["accion"];
    controladorPresentaciones($accion);
}


function controladorPresentaciones($accion) {
    switch ($accion) {
        case 'LISTAR':
            if (isset($_POST["sucursal_id"])) {
                listarPresentaciones($_POST["sucursal_id"]);
            }
            break;
            
        case 'OBTENER':
            if (isset($_POST["id"])) {
                obtenerPresentacion($_POST["id"]);
            }
            break;
            
        case 'CREAR':
            if (isset($_POST["codigo"]) && isset($_POST["presentacion"]) && 
                isset($_POST["cantidad_numero"]) && isset($_POST["sucursal_id"])) {
                crearPresentacion(
                    $_POST["codigo"],
                    $_POST["presentacion"],
                    $_POST["cantidad_numero"],
                    $_POST["sucursal_id"]
                );
            }
            break;
            
        case 'EDITAR':
            if (isset($_POST["id"]) && isset($_POST["presentacion"]) && 
                isset($_POST["cantidad_numero"])) {
                editarPresentacion(
                    $_POST["id"],
                    $_POST["presentacion"],
                    $_POST["cantidad_numero"]
                );
            }
            break;
            
        case 'ELIMINAR':
            if (isset($_POST["id"])) {
                eliminarPresentacion($_POST["id"]);
            }
            break;
    }
}

function listarPresentaciones($sucursal_id) {
    global $conectar;
    
    try {
        $sql = "SELECT 
                    id,
                    codigo,
                    presentacion,
                    cantidad_numero,
                    created_at,
                    updated_at
                FROM unidadescompra 
                WHERE sucursal_id = :sucursal_id 
                AND deleted_at IS NULL
                ORDER BY presentacion ASC";
        
        $stmt = $conectar->prepare($sql);
        $stmt->bindParam(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $presentaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'estado' => true,
            'datos' => $presentaciones,
            'mensaje' => 'Presentaciones cargadas correctamente'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'estado' => false,
            'mensaje' => 'Error al cargar presentaciones: ' . $e->getMessage()
        ]);
    }
}

function obtenerPresentacion($id) {
    global $conectar;
    
    try {
        $sql = "SELECT 
                    id,
                    codigo,
                    presentacion,
                    cantidad_numero,
                    sucursal_id,
                    created_at
                FROM unidadescompra 
                WHERE id = :id 
                AND deleted_at IS NULL";
        
        $stmt = $conectar->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $presentacion = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($presentacion) {
            echo json_encode([
                'estado' => true,
                'datos' => $presentacion,
                'mensaje' => 'Presentación encontrada'
            ]);
        } else {
            echo json_encode([
                'estado' => false,
                'mensaje' => 'Presentación no encontrada'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'estado' => false,
            'mensaje' => 'Error al obtener presentación: ' . $e->getMessage()
        ]);
    }
}

function crearPresentacion($codigo, $presentacion, $cantidad_numero, $sucursal_id) {
    global $conectar;
    
    try {
        // Verificar que el código no exista
        $sqlCheck = "SELECT COUNT(*) as existe 
                     FROM unidadescompra 
                     WHERE codigo = :codigo 
                     AND sucursal_id = :sucursal_id 
                     AND deleted_at IS NULL";
        
        $stmtCheck = $conectar->prepare($sqlCheck);
        $stmtCheck->bindParam(':codigo', $codigo);
        $stmtCheck->bindParam(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
        $stmtCheck->execute();
        
        $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        
        if ($result['existe'] > 0) {
            echo json_encode([
                'estado' => false,
                'mensaje' => 'Ya existe una presentación con el código "' . $codigo . '"'
            ]);
            return;
        }
        
        // Insertar nueva presentación
        $sql = "INSERT INTO unidadescompra 
                (codigo, presentacion, cantidad_numero, sucursal_id, created_at) 
                VALUES 
                (:codigo, :presentacion, :cantidad_numero, :sucursal_id, CURRENT_TIMESTAMP)";
        
        $stmt = $conectar->prepare($sql);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':presentacion', $presentacion);
        $stmt->bindParam(':cantidad_numero', $cantidad_numero);
        $stmt->bindParam(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            echo json_encode([
                'estado' => true,
                'mensaje' => 'Presentación creada correctamente',
                'id' => $conectar->lastInsertId()
            ]);
        } else {
            echo json_encode([
                'estado' => false,
                'mensaje' => 'No se pudo crear la presentación'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'estado' => false,
            'mensaje' => 'Error al crear presentación: ' . $e->getMessage()
        ]);
    }
}

function editarPresentacion($id, $presentacion, $cantidad_numero) {
    global $conectar;
    
    try {
        $sql = "UPDATE unidadescompra 
                SET presentacion = :presentacion,
                    cantidad_numero = :cantidad_numero,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id 
                AND deleted_at IS NULL";
        
        $stmt = $conectar->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':presentacion', $presentacion);
        $stmt->bindParam(':cantidad_numero', $cantidad_numero);
        
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    'estado' => true,
                    'mensaje' => 'Presentación actualizada correctamente'
                ]);
            } else {
                echo json_encode([
                    'estado' => false,
                    'mensaje' => 'No se realizaron cambios o la presentación no existe'
                ]);
            }
        } else {
            echo json_encode([
                'estado' => false,
                'mensaje' => 'No se pudo actualizar la presentación'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'estado' => false,
            'mensaje' => 'Error al actualizar presentación: ' . $e->getMessage()
        ]);
    }
}

function eliminarPresentacion($id) {
    global $conectar;
    
    try {
        // Verificar que no haya artículos usando esta presentación
        $sqlCheck = "SELECT COUNT(*) as cantidad 
                     FROM rel_articulounidadescompra 
                     WHERE unidadescompra_id = :id 
                     AND deleted_at IS NULL";
        
        $stmtCheck = $conectar->prepare($sqlCheck);
        $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtCheck->execute();
        
        $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        
        if ($result['cantidad'] > 0) {
            echo json_encode([
                'estado' => false,
                'mensaje' => 'No se puede eliminar. Hay ' . $result['cantidad'] . ' artículo(s) usando esta presentación'
            ]);
            return;
        }
        
        // Hacer soft delete
        $sql = "UPDATE unidadescompra 
                SET deleted_at = CURRENT_TIMESTAMP
                WHERE id = :id";
        
        $stmt = $conectar->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            echo json_encode([
                'estado' => true,
                'mensaje' => 'Presentación eliminada correctamente'
            ]);
        } else {
            echo json_encode([
                'estado' => false,
                'mensaje' => 'No se pudo eliminar la presentación'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'estado' => false,
            'mensaje' => 'Error al eliminar presentación: ' . $e->getMessage()
        ]);
    }
}
?>