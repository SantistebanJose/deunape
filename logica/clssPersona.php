<?php
include("bd.php");
session_start();

if (isset($_POST["accion"])) {
    $accion = $_POST["accion"];
    controladorVentaCorte($accion);
}

function controladorVentaCorte($accion)
{
    switch ($accion) {
        case 'CONSULTARRESERVA':
            $venta_id = $_POST["venta_id"];
            consultarDetalleReserva($venta_id);
            break;
        case 'OBTENERPERSONA':
            $id = $_POST["id"];
            consultarPersona($id);
            break;
        case 'REGISTRARPERSONARAPIDO':
            $data = json_decode($_POST["data"], true);
            if (isset($data['nombres']) && isset($data['apellidos'])) {
                registrar_persona_rapido($data);
            } elseif (isset($data['nombre_comercial']) && isset($data['razon_social'])) {
                registrar_empresa_rapido($data);
            }
            break;
        case 'REGISTRARPERSONAEMPLEADO':
            $data = json_decode($_POST["data"], true);
            if (isset($data['nombres']) && isset($data['apellidos'])) {
                registrar_empleado($data);
            }
            break;
        case 'REGISTRARPERSONA':
            $data = json_decode($_POST["data"], true);
            if (isset($data['nombres']) && isset($data['apellidos'])) {
                registrar_persona($data);
            } elseif (isset($data['nombre_comercial']) && isset($data['razon_social'])) {
                registrar_empresa($data);
            }
            break;
        case 'REGISTRAREMPLEADO':
            $data = json_decode($_POST["data"], true);
            if (isset($data['nombres']) && isset($data['apellidos'])) {
                registrar_empleado_trabajador($data);
            }
            break;
        case 'ACTUALIZAREMPLEADO':
            $data = json_decode($_POST["data"], true);
            if (isset($data['nombres']) && isset($data['apellidos'])) {
                actualizar_empleado_trabajador($data);
            }
            break;
        case 'ACTUALIZARPERSONA':
            $data = json_decode($_POST["data"], true);
            if (isset($data['nombres']) && isset($data['apellidos'])) {
                actualizar_persona($data);
            } elseif (isset($data['nombre_comercial']) && isset($data['razon_social'])) {
                actualizar_empresa($data);
            }
            break;
        case 'BLOQUEARPERSONA':
            $id = $_POST["id"];
            toggle_estado_persona($id, $accion);
            break;
        case 'DESBLOQUEARPERSONA':
            $id = $_POST["id"];
            toggle_estado_persona($id, $accion);
            break;
        case 'ELIMINARPERSONA':
            $id = $_POST["id"];
            eliminar_persona($id);
            break;
    }
}

/**
 * Obtiene la sucursal_id de la sesión actual.
 */
function getSucursalId() {
    return isset($_SESSION['sucursal_id']) && $_SESSION['sucursal_id'] !== ''
        ? intval($_SESSION['sucursal_id'])
        : null;
}

/**
 * Si el número de documento ya existe, intenta agregar la sucursal actual
 * al array sucursal_id de esa persona (si aún no está incluida).
 * Retorna: 
 *   - false si no existe (debe seguir con el INSERT normal)
 *   - true  si existía y ya se agregó (o ya estaba) la sucursal → termina flujo
 */
function manejarDocumentoExistente($numero_documento) {
    global $conectar;

    $sucursal_id = getSucursalId();

    // Buscar si ya existe el documento
    $stmt = $conectar->prepare("SELECT id, sucursal_id FROM persona WHERE numero_documento = :numero_documento");
    $stmt->bindParam(":numero_documento", $numero_documento);
    $stmt->execute();
    $persona = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$persona) {
        // No existe: hay que hacer INSERT normal
        return false;
    }

    // Existe: intentar agregar la sucursal si corresponde
    if ($sucursal_id === null) {
        // No hay sucursal en sesión, sólo informamos que ya existe
        echo json_encode([
            "success" => false,
            "message" => "El número de documento ya está registrado."
        ]);
        return true;
    }

    // Verificar si la sucursal ya está en el array usando = ANY(sucursal_id)
    $stmtCheck = $conectar->prepare(
        "SELECT COUNT(*) FROM persona WHERE numero_documento = :numero_documento AND :sucursal_id = ANY(sucursal_id)"
    );
    $stmtCheck->bindParam(":numero_documento", $numero_documento);
    $stmtCheck->bindParam(":sucursal_id", $sucursal_id, PDO::PARAM_INT);
    $stmtCheck->execute();
    $yaAsignada = $stmtCheck->fetchColumn();

    if ($yaAsignada > 0) {
        // Ya pertenece a esta sucursal
        echo json_encode([
            "success" => false,
            "message" => "Esta persona ya está registrada en esta sucursal."
        ]);
        return true;
    }

    // Agregar la sucursal al array con array_append
    $stmtUpdate = $conectar->prepare(
        "UPDATE persona SET sucursal_id = array_append(sucursal_id, :sucursal_id) WHERE numero_documento = :numero_documento"
    );
    $stmtUpdate->bindParam(":sucursal_id", $sucursal_id, PDO::PARAM_INT);
    $stmtUpdate->bindParam(":numero_documento", $numero_documento);
    $stmtUpdate->execute();

    echo json_encode([
        "success" => true,
        "message" => "La persona ya existía y fue asignada a esta sucursal correctamente.",
        "persona_id" => $persona['id']
    ]);
    return true;
}


function consultarPersona($id)
{
    global $conectar;

    try {
        $orden = $conectar->prepare("
        SELECT p.id,
        p.numero_documento, 
        p.tipo_persona,
        p.condicion, 
        p.nombres,
        p.apellidos,
        p.fecha_nacimiento,
        p.telefonofijo,
        p.telefonomovil,
        p.email,
        p.direccion,
        p.nombre_comercial, 
        p.razon_social,
        p.deleted_at,
        u.username,
        u.sueldo,
        u.cantidad_horas_trabajo as horas,
        u.cantidad_dias_semana as dias
        FROM persona p
        LEFT JOIN usuario u ON u.persona_id = p.id
        WHERE p.id = :id");

        $orden->bindParam(":id", $id);
        $orden->execute();

        $lista = $orden->fetch(PDO::FETCH_ASSOC);
        if ($lista) {
            echo json_encode(["success" => true, "data" => $lista]);
        } else {
            echo json_encode(["success" => false, "message" => "No se encontraron datos."]);
        }
    } catch (\Throwable $th) {
        echo json_encode(["success" => false, "error" => $th->getMessage()]);
    }
}

function registrar_empleado_trabajador($datos = array()) {
    global $conectar;

    try {
        // Si ya existe, agregar sucursal y salir
        if (manejarDocumentoExistente($datos['numero_documento'])) return;

        $conectar->beginTransaction();

        $sucursal_id = getSucursalId();
        $sucursalLiteral = $sucursal_id !== null ? "ARRAY[$sucursal_id]::bigint[]" : "ARRAY[]::bigint[]";

        $orden = $conectar->prepare("INSERT INTO persona (numero_documento, nombres, apellidos, telefonomovil, email, tipo_persona, direccion, condicion, sucursal_id)
                                     VALUES (:numero_documento, :nombres, :apellidos, :telefono_movil, :email, 'NATURAL', :direccion, :condicion, $sucursalLiteral)");
        $orden->bindParam(":numero_documento", $datos['numero_documento']);
        $orden->bindParam(":nombres", $datos['nombres']);
        $orden->bindParam(":apellidos", $datos['apellidos']);
        $orden->bindParam(":condicion", $datos['condicion']);

        $direccion = empty($datos['direccion']) ? null : $datos['direccion'];
        $orden->bindParam(":direccion", $direccion, is_null($direccion) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $telefonoMovil = empty($datos['telefono_movil']) ? null : $datos['telefono_movil'];
        $email = empty($datos['email']) ? null : $datos['email'];
        $orden->bindParam(":telefono_movil", $telefonoMovil, is_null($telefonoMovil) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden->bindParam(":email", $email, is_null($email) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $orden->execute();
        $persona_id = $conectar->lastInsertId();
        $orden->closeCursor();

        $username = !empty($datos['username']) ? $datos['username'] : null;
        $password = !empty($datos['password']) ? password_hash($datos['password'], PASSWORD_BCRYPT) : null;
        $rol = ($username && $password) ? "empleado" : null;

        $orden_usuario = $conectar->prepare("INSERT INTO usuario (persona_id, username, password, rol, sueldo, cantidad_horas_trabajo, cantidad_dias_semana)
                                             VALUES (:persona_id, :username, :password, :rol, :sueldo, :horas, :dias)");
        $orden_usuario->bindParam(":persona_id", $persona_id);
        $orden_usuario->bindParam(":username", $username, is_null($username) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden_usuario->bindParam(":password", $password, is_null($password) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden_usuario->bindParam(":rol", $rol, is_null($rol) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $sueldo = empty($datos['sueldo']) ? null : $datos['sueldo'];
        $horas  = empty($datos['horas'])  ? null : $datos['horas'];
        $dias   = empty($datos['dias'])   ? null : $datos['dias'];
        $orden_usuario->bindParam(":sueldo", $sueldo, is_null($sueldo) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden_usuario->bindParam(":horas",  $horas,  is_null($horas)  ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $orden_usuario->bindParam(":dias",   $dias,   is_null($dias)   ? PDO::PARAM_NULL : PDO::PARAM_INT);

        $orden_usuario->execute();
        $orden_usuario->closeCursor();

        $conectar->commit();
        echo json_encode(["success" => true, "persona_id" => $persona_id]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en registrar_empleado_trabajador: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function registrar_persona($datos = array()) {
    global $conectar;

    try {
        // Si ya existe, agregar sucursal y salir
        if (manejarDocumentoExistente($datos['numero_documento'])) return;

        $conectar->beginTransaction();

        $sucursal_id = getSucursalId();
        $sucursalLiteral = $sucursal_id !== null ? "ARRAY[$sucursal_id]::bigint[]" : "ARRAY[]::bigint[]";

        $orden = $conectar->prepare("INSERT INTO persona (numero_documento, nombres, apellidos, telefonomovil, email, tipo_persona, direccion, condicion, sucursal_id)
                                     VALUES (:numero_documento, :nombres, :apellidos, :telefono_movil, :email, 'NATURAL', :direccion, :condicion, $sucursalLiteral)");
        $orden->bindParam(":numero_documento", $datos['numero_documento']);
        $orden->bindParam(":nombres", $datos['nombres']);
        $orden->bindParam(":apellidos", $datos['apellidos']);
        $orden->bindParam(":condicion", $datos['condicion']);

        $direccion = empty($datos['direccion']) ? null : $datos['direccion'];
        $orden->bindParam(":direccion", $direccion, is_null($direccion) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $telefonoMovil = empty($datos['telefono_movil']) ? null : $datos['telefono_movil'];
        $email = empty($datos['email']) ? null : $datos['email'];
        $orden->bindParam(":telefono_movil", $telefonoMovil, is_null($telefonoMovil) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden->bindParam(":email", $email, is_null($email) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $orden->execute();
        $persona_id = $conectar->lastInsertId();
        $orden->closeCursor();

        $conectar->commit();
        echo json_encode(["success" => true, "persona_id" => $persona_id]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en registrar_persona: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function registrar_empresa($datos = array()) {
    global $conectar;

    try {
        // Si ya existe, agregar sucursal y salir
        if (manejarDocumentoExistente($datos['numero_documento'])) return;

        $conectar->beginTransaction();

        $sucursal_id = getSucursalId();
        $sucursalLiteral = $sucursal_id !== null ? "ARRAY[$sucursal_id]::bigint[]" : "ARRAY[]::bigint[]";

        $orden = $conectar->prepare("INSERT INTO persona (numero_documento, nombre_comercial, razon_social, telefonomovil, email, tipo_persona, direccion, condicion, sucursal_id)
                                     VALUES (:numero_documento, :nombre_comercial, :razon_social, :telefono_movil, :email, 'JURIDICA', :direccion, :condicion, $sucursalLiteral)");
        $orden->bindParam(":numero_documento", $datos['numero_documento']);
        $orden->bindParam(":nombre_comercial", $datos['nombre_comercial']);
        $orden->bindParam(":razon_social", $datos['razon_social']);
        $orden->bindParam(":condicion", $datos['condicion']);

        $direccion = empty($datos['direccion']) ? null : $datos['direccion'];
        $orden->bindParam(":direccion", $direccion, is_null($direccion) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $telefonoMovil = empty($datos['telefono_movil']) ? null : $datos['telefono_movil'];
        $email = empty($datos['email']) ? null : $datos['email'];
        $orden->bindParam(":telefono_movil", $telefonoMovil, is_null($telefonoMovil) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden->bindParam(":email", $email, is_null($email) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $orden->execute();
        $empresa_id = $conectar->lastInsertId();
        $orden->closeCursor();

        $conectar->commit();
        echo json_encode(["success" => true, "empresa_id" => $empresa_id]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en registrar_empresa: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function registrar_persona_rapido($datos = array()) {
    global $conectar;

    try {
        // Si ya existe, agregar sucursal y salir
        if (manejarDocumentoExistente($datos['numero_documento'])) return;

        $conectar->beginTransaction();

        $sucursal_id = getSucursalId();
        $sucursalLiteral = $sucursal_id !== null ? "ARRAY[$sucursal_id]::bigint[]" : "ARRAY[]::bigint[]";

        $orden = $conectar->prepare("INSERT INTO persona (numero_documento, nombres, apellidos, telefonomovil, email, tipo_persona, condicion, sucursal_id)
                                     VALUES (:numero_documento, :nombres, :apellidos, :telefono_movil, :email, 'NATURAL', 'CLIENTE', $sucursalLiteral)");
        $orden->bindParam(":numero_documento", $datos['numero_documento']);
        $orden->bindParam(":nombres", $datos['nombres']);
        $orden->bindParam(":apellidos", $datos['apellidos']);

        $telefonoMovil = empty($datos['telefono_movil']) ? null : $datos['telefono_movil'];
        $email = empty($datos['email']) ? null : $datos['email'];
        $orden->bindParam(":telefono_movil", $telefonoMovil, is_null($telefonoMovil) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden->bindParam(":email", $email, is_null($email) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $orden->execute();
        $persona_id = $conectar->lastInsertId();
        $orden->closeCursor();

        $conectar->commit();
        echo json_encode(["success" => true, "persona_id" => $persona_id]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en registrar_persona_rapido: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function registrar_empleado($datos = array()) {
    global $conectar;

    try {
        // Si ya existe, agregar sucursal y salir
        if (manejarDocumentoExistente($datos['numero_documento'])) return;

        $conectar->beginTransaction();

        $sucursal_id = getSucursalId();
        $sucursalLiteral = $sucursal_id !== null ? "ARRAY[$sucursal_id]::bigint[]" : "ARRAY[]::bigint[]";

        $orden = $conectar->prepare("INSERT INTO persona (numero_documento, nombres, apellidos, telefonomovil, email, tipo_persona, condicion, sucursal_id)
                                     VALUES (:numero_documento, :nombres, :apellidos, :telefono_movil, :email, 'NATURAL', 'EMPLEADO', $sucursalLiteral)");
        $orden->bindParam(":numero_documento", $datos['numero_documento']);
        $orden->bindParam(":nombres", $datos['nombres']);
        $orden->bindParam(":apellidos", $datos['apellidos']);

        $telefonoMovil = empty($datos['telefono_movil']) ? null : $datos['telefono_movil'];
        $email = empty($datos['email']) ? null : $datos['email'];
        $orden->bindParam(":telefono_movil", $telefonoMovil, is_null($telefonoMovil) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden->bindParam(":email", $email, is_null($email) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $orden->execute();
        $persona_id = $conectar->lastInsertId();
        $orden->closeCursor();

        $conectar->commit();
        echo json_encode(["success" => true, "persona_id" => $persona_id]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en registrar_empleado: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function registrar_empresa_rapido($datos = array()) {
    global $conectar;

    try {
        // Si ya existe, agregar sucursal y salir
        if (manejarDocumentoExistente($datos['numero_documento'])) return;

        $conectar->beginTransaction();

        $sucursal_id = getSucursalId();
        $sucursalLiteral = $sucursal_id !== null ? "ARRAY[$sucursal_id]::bigint[]" : "ARRAY[]::bigint[]";

        $orden = $conectar->prepare("INSERT INTO persona (numero_documento, nombre_comercial, razon_social, telefonomovil, email, tipo_persona, condicion, sucursal_id)
                                     VALUES (:numero_documento, :nombre_comercial, :razon_social, :telefono_movil, :email, 'JURIDICA', 'EMPRESA', $sucursalLiteral)");
        $orden->bindParam(":numero_documento", $datos['numero_documento']);
        $orden->bindParam(":nombre_comercial", $datos['nombre_comercial']);
        $orden->bindParam(":razon_social", $datos['razon_social']);

        $telefonoMovil = empty($datos['telefono_movil']) ? null : $datos['telefono_movil'];
        $email = empty($datos['email']) ? null : $datos['email'];
        $orden->bindParam(":telefono_movil", $telefonoMovil, is_null($telefonoMovil) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden->bindParam(":email", $email, is_null($email) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $orden->execute();
        $empresa_id = $conectar->lastInsertId();
        $orden->closeCursor();

        $conectar->commit();
        echo json_encode(["success" => true, "empresa_id" => $empresa_id]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en registrar_empresa_rapido: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function actualizar_empleado_trabajador($datos = array()) {
    global $conectar;

    try {
        $conectar->beginTransaction();

        $orden = $conectar->prepare("UPDATE persona SET 
            numero_documento = :numero_documento, 
            nombres = :nombres, 
            apellidos = :apellidos, 
            telefonomovil = :telefono_movil, 
            email = :email, 
            direccion = :direccion, 
            condicion = :condicion
            WHERE id = :id");

        $orden->bindParam(":id", $datos['id']);
        $orden->bindParam(":numero_documento", $datos['numero_documento']);
        $orden->bindParam(":nombres", $datos['nombres']);
        $orden->bindParam(":apellidos", $datos['apellidos']);
        $orden->bindParam(":condicion", $datos['condicion']);

        $direccion = empty($datos['direccion']) ? null : $datos['direccion'];
        $orden->bindParam(":direccion", $direccion, is_null($direccion) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $telefonoMovil = empty($datos['telefono_movil']) ? null : $datos['telefono_movil'];
        $email = empty($datos['email']) ? null : $datos['email'];
        $orden->bindParam(":telefono_movil", $telefonoMovil, is_null($telefonoMovil) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden->bindParam(":email", $email, is_null($email) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden->execute();
        $orden->closeCursor();

        $orden_usuario = $conectar->prepare("UPDATE usuario SET 
            username = :username, 
            password = COALESCE(:password, password), 
            rol = :rol, 
            sueldo = :sueldo, 
            cantidad_horas_trabajo = :horas, 
            cantidad_dias_semanas = :dias
            WHERE persona_id = :persona_id");

        $orden_usuario->bindParam(":persona_id", $datos['id']);

        $username = !empty($datos['username']) ? $datos['username'] : null;
        $password = !empty($datos['password']) ? password_hash($datos['password'], PASSWORD_BCRYPT) : null;
        $rol = ($username && $password) ? "empleado" : null;

        $orden_usuario->bindParam(":username", $username, is_null($username) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden_usuario->bindParam(":password", $password, is_null($password) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden_usuario->bindParam(":rol", $rol, is_null($rol) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $sueldo = empty($datos['sueldo']) ? null : $datos['sueldo'];
        $horas  = empty($datos['horas'])  ? null : $datos['horas'];
        $dias   = empty($datos['dias'])   ? null : $datos['dias'];
        $orden_usuario->bindParam(":sueldo", $sueldo, is_null($sueldo) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden_usuario->bindParam(":horas",  $horas,  is_null($horas)  ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $orden_usuario->bindParam(":dias",   $dias,   is_null($dias)   ? PDO::PARAM_NULL : PDO::PARAM_INT);

        $orden_usuario->execute();
        $orden_usuario->closeCursor();

        $conectar->commit();
        echo json_encode(["success" => true, "message" => "Persona y usuario actualizados correctamente"]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en actualizar_empleado_trabajador: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function actualizar_persona($datos = array()) {
    global $conectar;

    try {
        $conectar->beginTransaction();

        $orden = $conectar->prepare("UPDATE persona SET 
            numero_documento = :numero_documento, 
            nombres = :nombres, 
            apellidos = :apellidos, 
            telefonomovil = :telefono_movil, 
            email = :email, 
            direccion = :direccion, 
            condicion = :condicion
            WHERE id = :id");

        $orden->bindParam(":id", $datos['id']);
        $orden->bindParam(":numero_documento", $datos['numero_documento']);
        $orden->bindParam(":nombres", $datos['nombres']);
        $orden->bindParam(":apellidos", $datos['apellidos']);
        $orden->bindParam(":condicion", $datos['condicion']);

        $direccion = empty($datos['direccion']) ? null : $datos['direccion'];
        $orden->bindParam(":direccion", $direccion, is_null($direccion) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $telefonoMovil = empty($datos['telefono_movil']) ? null : $datos['telefono_movil'];
        $email = empty($datos['email']) ? null : $datos['email'];
        $orden->bindParam(":telefono_movil", $telefonoMovil, is_null($telefonoMovil) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden->bindParam(":email", $email, is_null($email) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $orden->execute();
        $orden->closeCursor();

        $conectar->commit();
        echo json_encode(["success" => true, "message" => "Persona actualizada correctamente"]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en actualizar_persona: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function actualizar_empresa($datos = array()) {
    global $conectar;

    try {
        $conectar->beginTransaction();

        $orden = $conectar->prepare("UPDATE persona SET 
            numero_documento = :numero_documento, 
            nombre_comercial = :nombre_comercial, 
            razon_social = :razon_social, 
            telefonomovil = :telefono_movil, 
            email = :email, 
            direccion = :direccion, 
            condicion = :condicion
            WHERE id = :id");

        $orden->bindParam(":id", $datos['id']);
        $orden->bindParam(":numero_documento", $datos['numero_documento']);
        $orden->bindParam(":nombre_comercial", $datos['nombre_comercial']);
        $orden->bindParam(":razon_social", $datos['razon_social']);
        $orden->bindParam(":condicion", $datos['condicion']);

        $direccion = empty($datos['direccion']) ? null : $datos['direccion'];
        $orden->bindParam(":direccion", $direccion, is_null($direccion) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $telefonoMovil = empty($datos['telefono_movil']) ? null : $datos['telefono_movil'];
        $email = empty($datos['email']) ? null : $datos['email'];
        $orden->bindParam(":telefono_movil", $telefonoMovil, is_null($telefonoMovil) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $orden->bindParam(":email", $email, is_null($email) ? PDO::PARAM_NULL : PDO::PARAM_STR);

        $orden->execute();
        $orden->closeCursor();

        $conectar->commit();
        echo json_encode(["success" => true, "message" => "Empresa actualizada correctamente"]);

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en actualizar_empresa: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function toggle_estado_persona($id, $accion) {
    global $conectar;

    try {
        $verificarPersona = $conectar->prepare("SELECT COUNT(*) FROM persona WHERE id = :id");
        $verificarPersona->bindParam(":id", $id);
        $verificarPersona->execute();
        $personaExistente = $verificarPersona->fetchColumn();

        if ($personaExistente == 0) {
            echo json_encode(["error" => true, "message" => "Persona no encontrada."]);
            return;
        }

        if ($accion == "BLOQUEARPERSONA") {
            $sql = "UPDATE persona SET deleted_at = NOW() WHERE id = :id";
        } elseif ($accion == "DESBLOQUEARPERSONA") {
            $sql = "UPDATE persona SET deleted_at = NULL WHERE id = :id";
        } else {
            echo json_encode(["error" => true, "message" => "Acción no válida."]);
            return;
        }

        $orden = $conectar->prepare($sql);
        $orden->bindParam(":id", $id);
        $orden->execute();

        echo json_encode(["success" => true, "message" => "Estado de la persona actualizado."]);

    } catch (\Throwable $th) {
        error_log("Error en toggle_estado_persona: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function eliminar_persona($id) {
    global $conectar;

    try {
        $verificarPersona = $conectar->prepare("SELECT COUNT(*) FROM persona WHERE id = :id");
        $verificarPersona->bindParam(":id", $id);
        $verificarPersona->execute();
        $personaExistente = $verificarPersona->fetchColumn();

        if ($personaExistente == 0) {
            echo json_encode(["error" => true, "message" => "Persona no encontrada."]);
            return;
        }

        $verificarRelacionCompra  = $conectar->prepare("SELECT COUNT(*) FROM compra WHERE cliente_id = :id");
        $verificarRelacionVenta   = $conectar->prepare("SELECT COUNT(*) FROM venta WHERE proveedor = :id");
        $verificarRelacionUsuario = $conectar->prepare("SELECT COUNT(*) FROM usuario WHERE persona_id = :id");

        $verificarRelacionCompra->bindParam(":id", $id);
        $verificarRelacionVenta->bindParam(":id", $id);
        $verificarRelacionUsuario->bindParam(":id", $id);

        $verificarRelacionCompra->execute();
        $verificarRelacionVenta->execute();
        $verificarRelacionUsuario->execute();

        $relacionCompra  = $verificarRelacionCompra->fetchColumn();
        $relacionVenta   = $verificarRelacionVenta->fetchColumn();
        $relacionUsuario = $verificarRelacionUsuario->fetchColumn();

        if ($relacionCompra > 0 || $relacionVenta > 0 || $relacionUsuario > 0) {
            echo json_encode(["error" => true, "message" => "No se puede eliminar a esta persona porque está asociada con compras, ventas o usuarios activos en el sistema."]);
            return;
        }

        $sql = "DELETE FROM persona WHERE id = :id";
        $orden = $conectar->prepare($sql);
        $orden->bindParam(":id", $id);
        $orden->execute();

        echo json_encode(["success" => true, "message" => "Persona eliminada con éxito."]);

    } catch (\Throwable $th) {
        error_log("Error en eliminar_persona: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}