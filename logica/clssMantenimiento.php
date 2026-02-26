<?php
include("bd.php");
session_start();


if (isset($_POST["accion"])) {
    $accion = $_POST["accion"];
    controladorMantenimiento($accion);
}

function controladorMantenimiento($accion)
{
    switch ($accion) {
        case 'INSERT_SERVICIOS':
            $data = json_decode($_POST["jsDatos"], true);
            registrar_servicio($data);
            break;
        case 'REGISTAR_SERVICIO':
            $data = json_decode($_POST["jsDatos"], true);
            registrar_servicio($data);
            break;
        case 'EDITAR_SERVICIO':
            $data = json_decode($_POST["jsDatos"], true);
            editar_servicio($data);
            break;
        case 'REGISTAR_TIPO_ARTICULO':
            $data = json_decode($_POST["jsDatos"], true);
            registrar_tipo($data);
            break;
        case 'REGISTAR_CATEGORIA_ARTICULO':
            $data = json_decode($_POST["jsDatos"], true);
            registrar_categoria($data);
            break;
        case 'REGISTAR_ESCALA_ARTICULO':
            $data = json_decode($_POST["jsDatos"], true);
            registrar_escala($data);
            break;
        case 'REGISTAR_DIMENSION_ARTICULO':
            $data = json_decode($_POST["jsDatos"], true);
            registrar_dimension($data);
            break;
        case 'EDITAR_TIPO_ARTICULO':
            $data = json_decode($_POST["jsDatos"], true);
            editar_tipo($data);
            break;
        case 'EDITAR_CATEGORIA_ARTICULO':
            $data = json_decode($_POST["jsDatos"], true);
            editar_categoria($data);
            break;
        case 'EDITAR_ESCALA_ARTICULO':
            $data = json_decode($_POST["jsDatos"], true);
            editar_escala($data);
            break;
        case 'EDITAR_DIMENSION_ARTICULO':
            $data = json_decode($_POST["jsDatos"], true);
            editar_dimension($data);
            break;
        case 'BLOQUEAR_TIPO':
            $id = $_POST["id"];
            toggle_estado_tipo($id, $accion);
            break;
        case 'DESBLOQUEAR_TIPO':
            $id = $_POST["id"];
            toggle_estado_tipo($id, $accion);
            break;
        case 'BLOQUEAR_CATEGORIA':
            $id = $_POST["id"];
            toggle_estado_categoria($id, $accion);
            break;
        case 'DESBLOQUEAR_CATEGORIA':
            $id = $_POST["id"];
            toggle_estado_categoria($id, $accion);
            break;
        case 'BLOQUEAR_ESCALA':
            $id = $_POST["id"];
            toggle_estado_escala($id, $accion);
            break;
        case 'DESBLOQUEAR_ESCALA':
            $id = $_POST["id"];
            toggle_estado_escala($id, $accion);
            break;
        case 'BLOQUEAR_DIMENSION':
            $id = $_POST["id"];
            toggle_estado_dimension($id, $accion);
            break;
        case 'DESBLOQUEAR_DIMENSION':
            $id = $_POST["id"];
            toggle_estado_dimension($id, $accion);
            break;
    }
}


function registrar_tipo($datos = array()) {
    global $conectar;
    $sucursal_id = isset($_SESSION["sucursal_id"]) ? $_SESSION["sucursal_id"] : null;

    try {
        $conectar->beginTransaction();
        $orden = $conectar->prepare("INSERT INTO tipo (abreviatura, descripcion, sucursal_id)
                                     VALUES (UPPER(:abreviatura), :descripcion, :sucursal_id);");
        $orden->bindParam(":abreviatura", $datos['nombre']);
        $orden->bindParam(":descripcion", $datos['descripcion']);
        $orden->bindParam(":sucursal_id", $sucursal_id);
        $orden->execute();
        $tipo_id = $conectar->lastInsertId();
        $orden->closeCursor();
        $conectar->commit();
        echo json_encode(["estado" => true, "mensaje" => "Operación realizada con éxito", "tipo_id" => $tipo_id]);
    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en registrar_tipo: " . $th->getMessage());
        echo json_encode(["error" => true, "mensaje" => $th->getMessage()]);
    }
}

function registrar_categoria($datos = array()) {
    global $conectar;
    $sucursal_id = isset($_SESSION["sucursal_id"]) ? $_SESSION["sucursal_id"] : null;

    try {
        $conectar->beginTransaction();
        $orden = $conectar->prepare("INSERT INTO categoria (abreviatura, descripcion, sucursal_id)
                                     VALUES (UPPER(:abreviatura), :descripcion, :sucursal_id);");
        $orden->bindParam(":abreviatura", $datos['nombre']);
        $orden->bindParam(":descripcion", $datos['descripcion']);
        $orden->bindParam(":sucursal_id", $sucursal_id);
        $orden->execute();
        $categoria_id = $conectar->lastInsertId();
        $orden->closeCursor();
        $conectar->commit();
        echo json_encode(["estado" => true, "mensaje" => "Operación realizada con éxito", "categoria_id" => $categoria_id]);
    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en registrar_categoria: " . $th->getMessage());
        echo json_encode(["error" => true, "mensaje" => $th->getMessage()]);
    }
}

function registrar_escala($datos = array()) {
    global $conectar;
    $sucursal_id = isset($_SESSION["sucursal_id"]) ? $_SESSION["sucursal_id"] : null;

    try {
        $conectar->beginTransaction();
        $orden = $conectar->prepare("INSERT INTO escala (abreviatura, descripcion, sucursal_id)
                                     VALUES (UPPER(:abreviatura), :descripcion, :sucursal_id);");
        $orden->bindParam(":abreviatura", $datos['nombre']);
        $orden->bindParam(":descripcion", $datos['descripcion']);
        $orden->bindParam(":sucursal_id", $sucursal_id);
        $orden->execute();
        $escala_id = $conectar->lastInsertId();
        $orden->closeCursor();
        $conectar->commit();
        echo json_encode(["estado" => true, "mensaje" => "Operación realizada con éxito", "escala_id" => $escala_id]);
    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en registrar_escala: " . $th->getMessage());
        echo json_encode(["error" => true, "mensaje" => $th->getMessage()]);
    }
}

function registrar_dimension($datos = array()) {
    global $conectar;
    $sucursal_id = isset($_SESSION["sucursal_id"]) ? $_SESSION["sucursal_id"] : null;

    try {
        $conectar->beginTransaction();
        $orden = $conectar->prepare("INSERT INTO dimension (medida, descripcion, sucursal_id)
                                     VALUES (UPPER(:medida), :descripcion, :sucursal_id);");
        $orden->bindParam(":medida", $datos['nombre']);
        $orden->bindParam(":descripcion", $datos['descripcion']);
        $orden->bindParam(":sucursal_id", $sucursal_id);
        $orden->execute();
        $dimension_id = $conectar->lastInsertId();
        $orden->closeCursor();
        $conectar->commit();
        echo json_encode(["estado" => true, "mensaje" => "Operación realizada con éxito", "dimension_id" => $dimension_id]);
    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en registrar_dimension: " . $th->getMessage());
        echo json_encode(["error" => true, "mensaje" => $th->getMessage()]);
    }
}

function editar_tipo($datos = array()) {
    global $conectar;
    $sucursal_id = isset($_SESSION["sucursal_id"]) ? $_SESSION["sucursal_id"] : null;

    try {
        $verificarGenerico = $conectar->prepare("SELECT COUNT(*) FROM tipo WHERE id = :id AND sucursal_id = :sucursal_id");
        $verificarGenerico->bindParam(":id", $datos['id']);
        $verificarGenerico->bindParam(":sucursal_id", $sucursal_id);
        $verificarGenerico->execute();
        $Existente = $verificarGenerico->fetchColumn();

        if ($Existente == 0) {
            echo json_encode(["error" => true, "mensaje" => "Tipo no encontrado"]);
            return;
        }

        $conectar->beginTransaction();
        $sql = "UPDATE tipo SET abreviatura = UPPER(:abreviatura), descripcion = :descripcion WHERE id = :id AND sucursal_id = :sucursal_id";
        $orden = $conectar->prepare($sql);
        $orden->bindParam(":abreviatura", $datos['nombre']);
        $orden->bindParam(":descripcion", $datos['descripcion']);
        $orden->bindParam(":id", $datos['id']);
        $orden->bindParam(":sucursal_id", $sucursal_id);
        $orden->execute();
        $conectar->commit();
        echo json_encode(["estado" => true, "mensaje" => "Operación realizada con éxito"]);
    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en editar_tipo: " . $th->getMessage());
        echo json_encode(["error" => true, "mensaje" => $th->getMessage()]);
    }
}

function editar_categoria($datos = array()) {
    global $conectar;
    $sucursal_id = isset($_SESSION["sucursal_id"]) ? $_SESSION["sucursal_id"] : null;

    try {
        $verificarGenerico = $conectar->prepare("SELECT COUNT(*) FROM categoria WHERE id = :id AND sucursal_id = :sucursal_id");
        $verificarGenerico->bindParam(":id", $datos['id']);
        $verificarGenerico->bindParam(":sucursal_id", $sucursal_id);
        $verificarGenerico->execute();
        $Existente = $verificarGenerico->fetchColumn();

        if ($Existente == 0) {
            echo json_encode(["error" => true, "mensaje" => "Categoria no encontrada"]);
            return;
        }

        $conectar->beginTransaction();
        $sql = "UPDATE categoria SET abreviatura = UPPER(:abreviatura), descripcion = :descripcion WHERE id = :id AND sucursal_id = :sucursal_id";
        $orden = $conectar->prepare($sql);
        $orden->bindParam(":abreviatura", $datos['nombre']);
        $orden->bindParam(":descripcion", $datos['descripcion']);
        $orden->bindParam(":id", $datos['id']);
        $orden->bindParam(":sucursal_id", $sucursal_id);
        $orden->execute();
        $conectar->commit();
        echo json_encode(["estado" => true, "mensaje" => "Operación realizada con éxito"]);
    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en editar_categoria: " . $th->getMessage());
        echo json_encode(["error" => true, "mensaje" => $th->getMessage()]);
    }
}

function editar_escala($datos = array()) {
    global $conectar;
    $sucursal_id = isset($_SESSION["sucursal_id"]) ? $_SESSION["sucursal_id"] : null;

    try {
        $verificarGenerico = $conectar->prepare("SELECT COUNT(*) FROM escala WHERE id = :id AND sucursal_id = :sucursal_id");
        $verificarGenerico->bindParam(":id", $datos['id']);
        $verificarGenerico->bindParam(":sucursal_id", $sucursal_id);
        $verificarGenerico->execute();
        $Existente = $verificarGenerico->fetchColumn();

        if ($Existente == 0) {
            echo json_encode(["error" => true, "mensaje" => "Escala no encontrada."]);
            return;
        }

        $conectar->beginTransaction();
        $sql = "UPDATE escala SET abreviatura = UPPER(:abreviatura), descripcion = :descripcion WHERE id = :id AND sucursal_id = :sucursal_id";
        $orden = $conectar->prepare($sql);
        $orden->bindParam(":abreviatura", $datos['nombre']);
        $orden->bindParam(":descripcion", $datos['descripcion']);
        $orden->bindParam(":id", $datos['id']);
        $orden->bindParam(":sucursal_id", $sucursal_id);
        $orden->execute();
        $conectar->commit();
        echo json_encode(["estado" => true, "mensaje" => "Operación realizada con éxito"]);
    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en editar_escala: " . $th->getMessage());
        echo json_encode(["error" => true, "mensaje" => $th->getMessage()]);
    }
}

function editar_dimension($datos = array()) {
    global $conectar;
    $sucursal_id = isset($_SESSION["sucursal_id"]) ? $_SESSION["sucursal_id"] : null;

    try {
        $verificarGenerico = $conectar->prepare("SELECT COUNT(*) FROM dimension WHERE id = :id AND sucursal_id = :sucursal_id");
        $verificarGenerico->bindParam(":id", $datos['id']);
        $verificarGenerico->bindParam(":sucursal_id", $sucursal_id);
        $verificarGenerico->execute();
        $Existente = $verificarGenerico->fetchColumn();

        if ($Existente == 0) {
            echo json_encode(["error" => true, "mensaje" => "Dimension no encontrado."]);
            return;
        }

        $conectar->beginTransaction();
        $sql = "UPDATE dimension SET medida = UPPER(:medida), descripcion = :descripcion WHERE id = :id AND sucursal_id = :sucursal_id";
        $orden = $conectar->prepare($sql);
        $orden->bindParam(":medida", $datos['nombre']);
        $orden->bindParam(":descripcion", $datos['descripcion']);
        $orden->bindParam(":id", $datos['id']);
        $orden->bindParam(":sucursal_id", $sucursal_id);
        $orden->execute();
        $conectar->commit();
        echo json_encode(["estado" => true, "mensaje" => "Operación realizada con éxito"]);
    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en editar_dimension: " . $th->getMessage());
        echo json_encode(["error" => true, "mensaje" => $th->getMessage()]);
    }
}

function toggle_estado_tipo($id, $accion) {
    global $conectar;
    $sucursal_id = isset($_SESSION["sucursal_id"]) ? $_SESSION["sucursal_id"] : null;

    try {
        $verificarUsuario = $conectar->prepare("SELECT COUNT(*) FROM tipo WHERE id = :id AND sucursal_id = :sucursal_id");
        $verificarUsuario->bindParam(":id", $id);
        $verificarUsuario->bindParam(":sucursal_id", $sucursal_id);
        $verificarUsuario->execute();
        $usuarioExistente = $verificarUsuario->fetchColumn();

        if ($usuarioExistente == 0) {
            echo json_encode(["error" => true, "message" => "Tipo no encontrado."]);
            return;
        }

        if ($accion == "BLOQUEAR_TIPO") {
            $sql = "UPDATE tipo SET deleted_at = NOW() WHERE id = :id AND sucursal_id = :sucursal_id";
        } elseif ($accion == "DESBLOQUEAR_TIPO") {
            $sql = "UPDATE tipo SET deleted_at = NULL WHERE id = :id AND sucursal_id = :sucursal_id";
        } else {
            echo json_encode(["error" => true, "message" => "Acción no válida."]);
            return;
        }

        $orden = $conectar->prepare($sql);
        $orden->bindParam(":id", $id);
        $orden->bindParam(":sucursal_id", $sucursal_id);
        $orden->execute();
        echo json_encode(["success" => true, "message" => "Estado del tipo actualizado."]);
    } catch (\Throwable $th) {
        error_log("Error en toggle_estado_tipo: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function toggle_estado_categoria($id, $accion) {
    global $conectar;
    $sucursal_id = isset($_SESSION["sucursal_id"]) ? $_SESSION["sucursal_id"] : null;

    try {
        $verificarUsuario = $conectar->prepare("SELECT COUNT(*) FROM categoria WHERE id = :id AND sucursal_id = :sucursal_id");
        $verificarUsuario->bindParam(":id", $id);
        $verificarUsuario->bindParam(":sucursal_id", $sucursal_id);
        $verificarUsuario->execute();
        $usuarioExistente = $verificarUsuario->fetchColumn();

        if ($usuarioExistente == 0) {
            echo json_encode(["error" => true, "message" => "Categoria no encontrada."]);
            return;
        }

        if ($accion == "BLOQUEAR_CATEGORIA") {
            $sql = "UPDATE categoria SET deleted_at = NOW() WHERE id = :id AND sucursal_id = :sucursal_id";
        } elseif ($accion == "DESBLOQUEAR_CATEGORIA") {
            $sql = "UPDATE categoria SET deleted_at = NULL WHERE id = :id AND sucursal_id = :sucursal_id";
        } else {
            echo json_encode(["error" => true, "message" => "Acción no válida."]);
            return;
        }

        $orden = $conectar->prepare($sql);
        $orden->bindParam(":id", $id);
        $orden->bindParam(":sucursal_id", $sucursal_id);
        $orden->execute();
        echo json_encode(["success" => true, "message" => "Estado de la categoría actualizado."]);
    } catch (\Throwable $th) {
        error_log("Error en toggle_estado_categoria: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function toggle_estado_escala($id, $accion) {
    global $conectar;
    $sucursal_id = isset($_SESSION["sucursal_id"]) ? $_SESSION["sucursal_id"] : null;

    try {
        $verificarUsuario = $conectar->prepare("SELECT COUNT(*) FROM escala WHERE id = :id AND sucursal_id = :sucursal_id");
        $verificarUsuario->bindParam(":id", $id);
        $verificarUsuario->bindParam(":sucursal_id", $sucursal_id);
        $verificarUsuario->execute();
        $usuarioExistente = $verificarUsuario->fetchColumn();

        if ($usuarioExistente == 0) {
            echo json_encode(["error" => true, "message" => "Escala no encontrada."]);
            return;
        }

        if ($accion == "BLOQUEAR_ESCALA") {
            $sql = "UPDATE escala SET deleted_at = NOW() WHERE id = :id AND sucursal_id = :sucursal_id";
        } elseif ($accion == "DESBLOQUEAR_ESCALA") {
            $sql = "UPDATE escala SET deleted_at = NULL WHERE id = :id AND sucursal_id = :sucursal_id";
        } else {
            echo json_encode(["error" => true, "message" => "Acción no válida."]);
            return;
        }

        $orden = $conectar->prepare($sql);
        $orden->bindParam(":id", $id);
        $orden->bindParam(":sucursal_id", $sucursal_id);
        $orden->execute();
        echo json_encode(["success" => true, "message" => "Estado de la escala actualizado."]);
    } catch (\Throwable $th) {
        error_log("Error en toggle_estado_escala: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function toggle_estado_dimension($id, $accion) {
    global $conectar;
    $sucursal_id = isset($_SESSION["sucursal_id"]) ? $_SESSION["sucursal_id"] : null;

    try {
        $verificarUsuario = $conectar->prepare("SELECT COUNT(*) FROM dimension WHERE id = :id AND sucursal_id = :sucursal_id");
        $verificarUsuario->bindParam(":id", $id);
        $verificarUsuario->bindParam(":sucursal_id", $sucursal_id);
        $verificarUsuario->execute();
        $usuarioExistente = $verificarUsuario->fetchColumn();

        if ($usuarioExistente == 0) {
            echo json_encode(["error" => true, "message" => "Dimension no encontrada."]);
            return;
        }

        if ($accion == "BLOQUEAR_DIMENSION") {
            $sql = "UPDATE dimension SET deleted_at = NOW() WHERE id = :id AND sucursal_id = :sucursal_id";
        } elseif ($accion == "DESBLOQUEAR_DIMENSION") {
            $sql = "UPDATE dimension SET deleted_at = NULL WHERE id = :id AND sucursal_id = :sucursal_id";
        } else {
            echo json_encode(["error" => true, "message" => "Acción no válida."]);
            return;
        }

        $orden = $conectar->prepare($sql);
        $orden->bindParam(":id", $id);
        $orden->bindParam(":sucursal_id", $sucursal_id);
        $orden->execute();
        echo json_encode(["success" => true, "message" => "Estado de la dimensión actualizado."]);
    } catch (\Throwable $th) {
        error_log("Error en toggle_estado_dimension: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

// ============================================================
// SERVICIOS: medidas son OPCIONALES (array puede estar vacío)
// ============================================================

function registrar_servicio($datos = array()) {
    global $conectar;

    try {
        $conectar->beginTransaction();

        $sucursal_id = isset($datos['sucursal_id']) ? $datos['sucursal_id'] : null;

        if (!$sucursal_id) {
            throw new Exception("sucursal_id es requerido");
        }

        // Medidas opcionales: si viene vacío o no existe, guardar array vacío en PostgreSQL
        $medidas = isset($datos['medidas']) && is_array($datos['medidas']) && count($datos['medidas']) > 0
            ? '{' . implode(',', $datos['medidas']) . '}'
            : '{}';

        $orden = $conectar->prepare("INSERT INTO movimiento (descripcion, medidas, sucursal_id)
                                     VALUES (:descripcion, :medidas::text[], :sucursal_id);");
        $orden->bindParam(":descripcion", $datos['descripcion']);
        $orden->bindParam(":medidas", $medidas);
        $orden->bindParam(":sucursal_id", $sucursal_id);
        $orden->execute();
        $servicio_id = $conectar->lastInsertId();
        $orden->closeCursor();

        $conectar->commit();
        echo json_encode(["estado" => true, "mensaje" => "Servicio registrado con éxito", "servicio_id" => $servicio_id]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en registrar_servicio: " . $th->getMessage());
        echo json_encode(["estado" => false, "mensaje" => $th->getMessage()]);
    }
}

function editar_servicio($datos = array()) {
    global $conectar;
    $sucursal_id = isset($_SESSION["sucursal_id"]) ? $_SESSION["sucursal_id"] : null;

    try {
        $verificar = $conectar->prepare("SELECT COUNT(*) FROM movimiento WHERE id = :id AND sucursal_id = :sucursal_id");
        $verificar->bindParam(":id", $datos['id']);
        $verificar->bindParam(":sucursal_id", $sucursal_id);
        $verificar->execute();
        $existe = $verificar->fetchColumn();

        if ($existe == 0) {
            echo json_encode(["estado" => false, "mensaje" => "Servicio no encontrado"]);
            return;
        }

        $conectar->beginTransaction();

        // Medidas opcionales: si viene vacío o no existe, guardar array vacío en PostgreSQL
        $medidas = isset($datos['medidas']) && is_array($datos['medidas']) && count($datos['medidas']) > 0
            ? '{' . implode(',', $datos['medidas']) . '}'
            : '{}';

        $sql = "UPDATE movimiento SET descripcion = :descripcion, medidas = :medidas::text[] WHERE id = :id AND sucursal_id = :sucursal_id";
        $orden = $conectar->prepare($sql);
        $orden->bindParam(":descripcion", $datos['descripcion']);
        $orden->bindParam(":medidas", $medidas);
        $orden->bindParam(":id", $datos['id']);
        $orden->bindParam(":sucursal_id", $sucursal_id);
        $orden->execute();
        $conectar->commit();

        echo json_encode(["estado" => true, "mensaje" => "Servicio actualizado con éxito"]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en editar_servicio: " . $th->getMessage());
        echo json_encode(["estado" => false, "mensaje" => $th->getMessage()]);
    }
}