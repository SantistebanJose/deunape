<?php
include("bd.php");

if (isset($_POST["accion"])) {
    $accion = $_POST["accion"];
    controladorLogin($accion);
}

function controladorLogin($accion)
{
    switch ($accion) {
        case 'LOGIN':
            $user = $_POST["user"];
            $pass = $_POST["password"];
            login($user, $pass);
            break;
        case 'ALTERCONTRASEÑA':
            $dni = $_POST["dni"];
            $pass = $_POST["password"];
            alter_contraseña($dni, $pass);
            break;
        case 'CAMBIARCONTRASEÑAUSURIO':
            $id = $_POST["dni"];
            $newpass = $_POST["password"];
            cambiar_contraseña_usuario($id, $newpass);
            break;
        case 'VALIDAR':
            // Implementar si es necesario
            break;
    }
}

function login($user, $pass)
{
    global $conectar;

    try {
        // ✅ QUERY ACTUALIZADA - Usando id_rol en lugar de id
        $orden = $conectar->prepare("
            SELECT 
                u.id, 
                u.username, 
                u.password, 
                u.sucursal_id, 
                u.id_rol,
                p.nombres, 
                p.apellidos, 
                p.email,
                r.nombre as nombre_rol, 
                r.descripcion as rol_descripcion,
                s.razon_social,
                s.nombre_comercial
            FROM usuario AS u 
            INNER JOIN persona AS p ON u.persona_id = p.id 
            INNER JOIN sucursal AS s ON s.id = u.sucursal_id
            LEFT JOIN roles AS r ON u.id_rol = r.id_rol

            WHERE u.deleted_at IS NULL 
                AND UPPER(u.username) = UPPER(:user)
        ");

        $orden->bindParam(":user", $user);
        $orden->execute();

        // Obtener los resultados
        $lista = $orden->fetch(PDO::FETCH_ASSOC);

        // Verificar si el usuario existe
        if ($lista) {
            // Verificar contraseña
            session_start();

            // Datos básicos del usuario
            $_SESSION['id'] = $lista["id"];
            $_SESSION['usuario'] = $lista["username"];
            $_SESSION['nombre'] = $lista["nombres"];
            $_SESSION['ape'] = $lista["apellidos"];
            $_SESSION['correo'] = $lista["email"];
            $_SESSION['sucursal_id'] = $lista["sucursal_id"];
            $_SESSION['nombre_comercial'] = $lista["nombre_comercial"];
            $_SESSION['razon_social'] = $lista["razon_social"];
            

            // ✅ DATOS DEL ROL PARA EL SISTEMA DE PERMISOS
            $_SESSION['id_rol'] = $lista["id_rol"] ?? 0;
            $_SESSION['nombre_rol'] = $lista["nombre_rol"] ?? 'Sin rol';
            $_SESSION['rol'] = $lista["nombre_rol"] ?? 'Sin rol'; // Compatibilidad
            $_SESSION['rol_descripcion'] = $lista["rol_descripcion"] ?? '';

            // Retornar respuesta exitosa
            echo json_encode([
                'success' => true,
                'id' => $lista["id"],
                'username' => $lista["username"],
                'nombre' => $lista["nombres"],
                'apellido' => $lista["apellidos"],
                'email' => $lista["email"],
                'rol' => $lista["nombre_rol"] ?? 'Sin rol',
                'id_rol' => $lista["id_rol"] ?? 0,
                'sucursal_id' => $lista["sucursal_id"],
                'mensaje' => 'Login exitoso'
            ]);
            /*
            if (password_verify($pass, $lista["password"])) {

                // ✅ INICIAR SESIÓN CON DATOS COMPLETOS


            } else {
                echo json_encode([
                    "success" => false,
                    "error" => "Credenciales inválidas"
                ]);
            }
             */
            
        } else {
            echo json_encode([
                "success" => false,
                "error" => "Usuario no encontrado"
            ]);
        }
    } catch (PDOException $e) {
        error_log("Error en login: " . $e->getMessage());
        echo json_encode([
            "success" => false,
            "error" => "Error en el sistema. Por favor contacte al administrador.",
            "details" => $e->getMessage() // Solo en desarrollo
        ]);
    }
}

function cambiar_contraseña_usuario($id, $newpass)
{
    global $conectar;

    try {
        // Verificar si el usuario existe
        $verificarUsuario = $conectar->prepare("SELECT COUNT(*) FROM usuario WHERE id = :id");
        $verificarUsuario->bindParam(":id", $id);
        $verificarUsuario->execute();
        $usuarioExistente = $verificarUsuario->fetchColumn();

        if ($usuarioExistente == 0) {
            echo json_encode(["error" => true, "message" => "Usuario no encontrado."]);
            return;
        }

        // Iniciar transacción
        $conectar->beginTransaction();
        $hashedPassword = password_hash($newpass, PASSWORD_BCRYPT);

        // Preparar la consulta para actualizar los datos
        $sql = "UPDATE usuario SET password = :password, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $orden = $conectar->prepare($sql);
        $orden->bindParam(":password", $hashedPassword);
        $orden->bindParam(":id", $id);

        $orden->execute();
        $conectar->commit();

        echo json_encode(["success" => true, "message" => "Contraseña actualizada con éxito."]);
    } catch (PDOException $e) {
        $conectar->rollBack();
        error_log("Error en cambiar_contraseña_usuario: " . $e->getMessage());
        echo json_encode(["error" => true, "message" => $e->getMessage()]);
    }
}

function alter_contraseña($dni, $newpass)
{
    global $conectar;

    try {
        // Buscar el ID del usuario basado en el DNI
        $buscarUsuario = $conectar->prepare("
            SELECT u.id FROM usuario u
            INNER JOIN persona p ON u.persona_id = p.id
            WHERE p.numero_documento = :dni
        ");
        $buscarUsuario->bindParam(":dni", $dni, PDO::PARAM_STR);
        $buscarUsuario->execute();
        $usuario = $buscarUsuario->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            echo json_encode(["error" => true, "message" => "Usuario no encontrado."]);
            return;
        }

        $id_usuario = $usuario['id'];

        // Iniciar transacción
        $conectar->beginTransaction();
        $hashedPassword = password_hash($newpass, PASSWORD_BCRYPT);

        // Actualizar la contraseña del usuario
        $actualizarPass = $conectar->prepare("
            UPDATE usuario 
            SET password = :password, updated_at = CURRENT_TIMESTAMP 
            WHERE id = :id
        ");
        $actualizarPass->bindParam(":password", $hashedPassword, PDO::PARAM_STR);
        $actualizarPass->bindParam(":id", $id_usuario, PDO::PARAM_INT);
        $actualizarPass->execute();

        // Confirmar cambios
        $conectar->commit();
        echo json_encode(["success" => true, "message" => "Contraseña actualizada con éxito."]);
    } catch (PDOException $e) {
        $conectar->rollBack();
        error_log("Error en alter_contraseña: " . $e->getMessage());
        echo json_encode(["error" => true, "message" => "Error al actualizar la contraseña."]);
    }
}
