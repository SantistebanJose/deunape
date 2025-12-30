<?php
include("bd.php");

if (isset($_POST["accion"])) {
    $accion = $_POST["accion"];
    controladorMantenimiento($accion);
}

function controladorMantenimiento($accion)
{
    switch ($accion) {
        case 'REGISTAR_TIPO_ARTICULO':
            $data = json_decode($_POST["jsDatos"], true); // Decodificar JSON
            registrar_tipo($data);
            break;
        case 'REGISTAR_CATEGORIA_ARTICULO':
            $data = json_decode($_POST["jsDatos"], true); // Decodificar JSON
            registrar_categoria($data);
            break;
        case 'REGISTAR_ESCALA_ARTICULO':
            $data = json_decode($_POST["jsDatos"], true); // Decodificar JSON
            registrar_escala($data);
            break;
        case 'REGISTAR_DIMENSION_ARTICULO':
            $data = json_decode($_POST["jsDatos"], true); // Decodificar JSON
            registrar_dimension($data);
            break;
        case 'EDITAR_TIPO_ARTICULO':
            $data = json_decode($_POST["jsDatos"], true); // Decodificar JSON
            editar_tipo($data);
            break;
        case 'EDITAR_CATEGORIA_ARTICULO':
            $data = json_decode($_POST["jsDatos"], true); // Decodificar JSON
            editar_categoria($data);
            break;
        case 'EDITAR_ESCALA_ARTICULO':
            $data = json_decode($_POST["jsDatos"], true); // Decodificar JSON
            editar_escala($data);
            break;
        case 'EDITAR_DIMENSION_ARTICULO':
            $data = json_decode($_POST["jsDatos"], true); // Decodificar JSON
            editar_dimension($data);
            break;
        case 'BLOQUEAR_TIPO':
            $id = $_POST["id"]; // Decodificar JSON
            toggle_estado_tipo($id, $accion);
            break;
        case 'DESBLOQUEAR_TIPO':
            $id = $_POST["id"]; // Decodificar JSON
            toggle_estado_tipo($id, $accion);
            break;
        case 'BLOQUEAR_CATEGORIA':
            $id = $_POST["id"]; // Decodificar JSON
            toggle_estado_categoria($id, $accion);
            break;
        case 'DESBLOQUEAR_CATEGORIA':
            $id = $_POST["id"]; // Decodificar JSON
            toggle_estado_categoria($id, $accion);
            break;
        case 'BLOQUEAR_ESCALA':
            $id = $_POST["id"]; // Decodificar JSON
            toggle_estado_escala($id, $accion);
            break;
        case 'DESBLOQUEAR_ESCALA':
            $id = $_POST["id"]; // Decodificar JSON
            toggle_estado_escala($id, $accion);
            break;
        case 'BLOQUEAR_DIMENSION':
            $id = $_POST["id"]; // Decodificar JSON
            toggle_estado_dimension($id, $accion);
            break;
        case 'DESBLOQUEAR_DIMENSION':
            $id = $_POST["id"]; // Decodificar JSON
            toggle_estado_dimension($id, $accion);
            break;

    }
}


function registrar_tipo($datos = array()) {
    global $conectar;
    // IMPORTANTE: Obtener sucursal_id de la sesión
    session_start();
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
    // IMPORTANTE: Obtener sucursal_id de la sesión
    session_start();
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
    // IMPORTANTE: Obtener sucursal_id de la sesión
    session_start();
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
    // IMPORTANTE: Obtener sucursal_id de la sesión
    session_start();
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

    try {
        // Verificar si el usuario existe
        $verificarGenerico = $conectar->prepare("SELECT COUNT(*) FROM tipo WHERE id = :id");
        $verificarGenerico->bindParam(":id", $datos['id']);
        $verificarGenerico->execute();
        $Existente = $verificarGenerico->fetchColumn();

        if ($Existente == 0) {
            // Si no existe el usuario, retornar un error
            echo json_encode(["error" => true, "mensaje" => "Tipo no encontrado"]);
            return;
        }

        // Iniciar transacción
        $conectar->beginTransaction();
        
        // Preparar la consulta para actualizar los datos
        $sql = "UPDATE tipo SET abreviatura = UPPER(:abreviatura), descripcion = :descripcion";
        $sql .= " WHERE id = :id";
        
        $orden = $conectar->prepare($sql);
        $orden->bindParam(":abreviatura", $datos['nombre']);
        $orden->bindParam(":descripcion", $datos['descripcion']);
        $orden->bindParam(":id", $datos['id']);
        

        $orden->execute();
        $conectar->commit();

        echo json_encode(["estado" => true, "mensaje" => "Operación realizada con éxito"]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en editar_usuario: " . $th->getMessage());
        echo json_encode(["error" => true, "mensaje" => $th->getMessage()]);
    }
}

function editar_categoria($datos = array()) {
    global $conectar;

    try {
        // Verificar si el usuario existe
        $verificarGenerico = $conectar->prepare("SELECT COUNT(*) FROM categoria WHERE id = :id");
        $verificarGenerico->bindParam(":id", $datos['id']);
        $verificarGenerico->execute();
        $Existente = $verificarGenerico->fetchColumn();

        if ($Existente == 0) {
            // Si no existe el usuario, retornar un error
            echo json_encode(["error" => true,  "mensaje" => "Categoria no encontrada"]);
            return;
        }

        // Iniciar transacción
        $conectar->beginTransaction();
        
        // Preparar la consulta para actualizar los datos
        $sql = "UPDATE categoria SET abreviatura = UPPER(:abreviatura), descripcion = :descripcion";
        $sql .= " WHERE id = :id";
        
        $orden = $conectar->prepare($sql);
        $orden->bindParam(":abreviatura", $datos['nombre']);
        $orden->bindParam(":descripcion", $datos['descripcion']);
        $orden->bindParam(":id", $datos['id']);
        

        $orden->execute();
        $conectar->commit();

        echo json_encode(["estado" => true,  "mensaje" => "Operación realizada con éxito"]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en editar_usuario: " . $th->getMessage());
        echo json_encode(["error" => true, "mensaje" => $th->getMessage()]);
    }
}

function editar_escala($datos = array()) {
    global $conectar;

    try {
        // Verificar si el usuario existe
        $verificarGenerico = $conectar->prepare("SELECT COUNT(*) FROM escala WHERE id = :id");
        $verificarGenerico->bindParam(":id", $datos['id']);
        $verificarGenerico->execute();
        $Existente = $verificarGenerico->fetchColumn();

        if ($Existente == 0) {
            // Si no existe el usuario, retornar un error
            echo json_encode(["error" => true, "mensaje" => "Escala no encontrado."]);
            return;
        }

        // Iniciar transacción
        $conectar->beginTransaction();
        
        // Preparar la consulta para actualizar los datos
        $sql = "UPDATE escala SET abreviatura = UPPER(:abreviatura), descripcion = :descripcion";
        $sql .= " WHERE id = :id";
        
        $orden = $conectar->prepare($sql);
        $orden->bindParam(":abreviatura", $datos['nombre']);
        $orden->bindParam(":descripcion", $datos['descripcion']);
        $orden->bindParam(":id", $datos['id']);
        

        $orden->execute();
        $conectar->commit();

        echo json_encode(["estado" => true,  "mensaje" => "Operación realizada con éxito"]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en editar_usuario: " . $th->getMessage());
        echo json_encode(["error" => true, "mensaje" => $th->getMessage()]);
    }
}

function editar_dimension($datos = array()) {
    global $conectar;

    try {
        // Verificar si el usuario existe
        $verificarGenerico = $conectar->prepare("SELECT COUNT(*) FROM dimension WHERE id = :id");
        $verificarGenerico->bindParam(":id", $datos['id']);
        $verificarGenerico->execute();
        $Existente = $verificarGenerico->fetchColumn();

        if ($Existente == 0) {
            // Si no existe el usuario, retornar un error
            echo json_encode(["error" => true, "mensaje" => "Dimension no encontrado."]);
            return;
        }

        // Iniciar transacción
        $conectar->beginTransaction();
        
        // Preparar la consulta para actualizar los datos
        $sql = "UPDATE dimension SET medida = UPPER(:medida), descripcion = :descripcion";
        $sql .= " WHERE id = :id";
        
        $orden = $conectar->prepare($sql);
        $orden->bindParam(":medida", $datos['nombre']);
        $orden->bindParam(":descripcion", $datos['descripcion']);
        $orden->bindParam(":id", $datos['id']);
        

        $orden->execute();
        $conectar->commit();

        echo json_encode(["estado" => true,  "mensaje" => "Operación realizada con éxito"]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en editar_usuario: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}



function toggle_estado_tipo($id, $accion) {
    global $conectar;

    try {
        // Verificar si el usuario existe
        $verificarUsuario = $conectar->prepare("SELECT COUNT(*) FROM tipo WHERE id = :id");
        $verificarUsuario->bindParam(":id", $id);
        $verificarUsuario->execute();
        $usuarioExistente = $verificarUsuario->fetchColumn();

        if ($usuarioExistente == 0) {
            // Si no existe el usuario, retornar un error
            echo json_encode(["error" => true, "message" => "Usuario no encontrado."]);
            return;
        }

        // Determinar la acción
        if ($accion == "BLOQUEAR_TIPO") {
            // Bloquear usuario (poner deleted_at)
            $sql = "UPDATE tipo SET deleted_at = NOW() WHERE id = :id";
        } elseif ($accion == "DESBLOQUEAR_TIPO") {
            // Desbloquear usuario (eliminar deleted_at)
            $sql = "UPDATE tipo SET deleted_at = NULL WHERE id = :id";
        } else {
            echo json_encode(["error" => true, "message" => "Acción no válida."]);
            return;
        }

        // Ejecutar la actualización
        $orden = $conectar->prepare($sql);
        $orden->bindParam(":id", $id);
        $orden->execute();

        echo json_encode(["success" => true, "message" => "Estado del usuario actualizado."]);

    } catch (\Throwable $th) {
        error_log("Error en toggle_estado_usuario: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function toggle_estado_categoria($id, $accion) {
    global $conectar;

    try {
        // Verificar si el usuario existe
        $verificarUsuario = $conectar->prepare("SELECT COUNT(*) FROM categoria WHERE id = :id");
        $verificarUsuario->bindParam(":id", $id);
        $verificarUsuario->execute();
        $usuarioExistente = $verificarUsuario->fetchColumn();

        if ($usuarioExistente == 0) {
            // Si no existe el usuario, retornar un error
            echo json_encode(["error" => true, "message" => "Usuario no encontrado."]);
            return;
        }

        // Determinar la acción
        if ($accion == "BLOQUEAR_CATEGORIA") {
            // Bloquear usuario (poner deleted_at)
            $sql = "UPDATE categoria SET deleted_at = NOW() WHERE id = :id";
        } elseif ($accion == "DESBLOQUEAR_CATEGORIA") {
            // Desbloquear usuario (eliminar deleted_at)
            $sql = "UPDATE categoria SET deleted_at = NULL WHERE id = :id";
        } else {
            echo json_encode(["error" => true, "message" => "Acción no válida."]);
            return;
        }

        // Ejecutar la actualización
        $orden = $conectar->prepare($sql);
        $orden->bindParam(":id", $id);
        $orden->execute();

        echo json_encode(["success" => true, "message" => "Estado del usuario actualizado."]);

    } catch (\Throwable $th) {
        error_log("Error en toggle_estado_usuario: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function toggle_estado_escala($id, $accion) {
    global $conectar;

    try {
        // Verificar si el usuario existe
        $verificarUsuario = $conectar->prepare("SELECT COUNT(*) FROM escala WHERE id = :id");
        $verificarUsuario->bindParam(":id", $id);
        $verificarUsuario->execute();
        $usuarioExistente = $verificarUsuario->fetchColumn();

        if ($usuarioExistente == 0) {
            // Si no existe el usuario, retornar un error
            echo json_encode(["error" => true, "message" => "Usuario no encontrado."]);
            return;
        }

        // Determinar la acción
        if ($accion == "BLOQUEAR_ESCALA") {
            // Bloquear usuario (poner deleted_at)
            $sql = "UPDATE escala SET deleted_at = NOW() WHERE id = :id";
        } elseif ($accion == "DESBLOQUEAR_ESCALA") {
            // Desbloquear usuario (eliminar deleted_at)
            $sql = "UPDATE escala SET deleted_at = NULL WHERE id = :id";
        } else {
            echo json_encode(["error" => true, "message" => "Acción no válida."]);
            return;
        }

        // Ejecutar la actualización
        $orden = $conectar->prepare($sql);
        $orden->bindParam(":id", $id);
        $orden->execute();

        echo json_encode(["success" => true, "message" => "Estado del usuario actualizado."]);

    } catch (\Throwable $th) {
        error_log("Error en toggle_estado_usuario: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function toggle_estado_dimension($id, $accion) {
    global $conectar;

    try {
        // Verificar si el usuario existe
        $verificarUsuario = $conectar->prepare("SELECT COUNT(*) FROM dimension WHERE id = :id");
        $verificarUsuario->bindParam(":id", $id);
        $verificarUsuario->execute();
        $usuarioExistente = $verificarUsuario->fetchColumn();

        if ($usuarioExistente == 0) {
            // Si no existe el usuario, retornar un error
            echo json_encode(["error" => true, "message" => "Usuario no encontrado."]);
            return;
        }

        // Determinar la acción
        if ($accion == "BLOQUEAR_DIMENSION") {
            // Bloquear usuario (poner deleted_at)
            $sql = "UPDATE dimension SET deleted_at = NOW() WHERE id = :id";
        } elseif ($accion == "DESBLOQUEAR_DIMENSION") {
            // Desbloquear usuario (eliminar deleted_at)
            $sql = "UPDATE dimension SET deleted_at = NULL WHERE id = :id";
        } else {
            echo json_encode(["error" => true, "message" => "Acción no válida."]);
            return;
        }

        // Ejecutar la actualización
        $orden = $conectar->prepare($sql);
        $orden->bindParam(":id", $id);
        $orden->execute();

        echo json_encode(["success" => true, "message" => "Estado del usuario actualizado."]);

    } catch (\Throwable $th) {
        error_log("Error en toggle_estado_usuario: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}
