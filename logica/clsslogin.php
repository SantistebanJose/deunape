<?php
include("bd.php");

if (isset($_POST["accion"])){
    $accion = $_POST["accion"];
    controladorLogin($accion);
}

function controladorLogin($accion){
    switch($accion){
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

function login($user, $pass){
    global $conectar;

    try{
        // Consulta para obtener los datos del usuario INCLUYENDO EL ROL
        $orden = $conectar->prepare("SELECT u.id, u.username, u.password, u.sucursal_id, u.id_rol,
                                            p.nombres, p.apellidos, p.email,
                                            r.nombre_rol, r.descripcion as rol_descripcion
                                     FROM usuario AS u 
                                     INNER JOIN persona AS p ON u.persona_id = p.id 
                                     LEFT JOIN roles AS r ON u.id_rol = r.id_rol
                                     WHERE u.deleted_at IS NULL AND UPPER(u.username) = UPPER(:user);");
        $orden->bindParam(":user", $user);
        $orden->execute();

        // Obtener los resultados
        $lista = $orden->fetch(PDO::FETCH_ASSOC);

        // Verificar si el usuario existe
        if ($lista) {
            // Verificar si la contraseña ingresada coincide con la almacenada (usando password_verify)
            if (password_verify($pass, $lista["password"])) {
                // Inicia sesión y guarda los datos en la sesión
                session_start();
                $_SESSION['id'] = $lista["id"];
                $_SESSION['usuario'] = $lista["username"];
                $_SESSION['rol'] = $lista["nombre_rol"] ?? 'Sin rol';  // CORREGIDO: usar nombre_rol en lugar de rol
                $_SESSION['nombre'] = $lista["nombres"];
                $_SESSION['ape'] = $lista["apellidos"];
                $_SESSION['correo'] = $lista["email"];
                $_SESSION['sucursal_id'] = $lista["sucursal_id"];
                
                // NUEVOS DATOS PARA EL SISTEMA DE PERMISOS
                $_SESSION['id_rol'] = $lista["id_rol"] ?? 0;
                $_SESSION['nombre_rol'] = $lista["nombre_rol"] ?? 'Sin rol';
                $_SESSION['rol_descripcion'] = $lista["rol_descripcion"] ?? '';

                // Retorna los datos del usuario en formato JSON
                echo json_encode([
                    'success' => true,
                    'id' => $lista["id"],
                    'username' => $lista["username"],
                    'nombre' => $lista["nombres"],
                    'apellido' => $lista["apellidos"],
                    'email' => $lista["email"],
                    'rol' => $lista["nombre_rol"] ?? 'Sin rol',
                    'sucursal_id' => $lista["sucursal_id"]
                ]);
            } else {
                echo json_encode(["error" => "Credenciales inválidas"]);  // Contraseña incorrecta
            }
        } else {
            echo json_encode(["error" => "Usuario no encontrado"]);  // Usuario no encontrado
        }
    } catch (\Throwable $th) {
        echo json_encode(["error" => $th->getMessage()]);  // Captura cualquier error y lo devuelve
    }
}

function cambiar_contraseña_usuario($id, $newpass) {
    global $conectar;

    try {
        // Verificar si el usuario existe
        $verificarUsuario = $conectar->prepare("SELECT COUNT(*) FROM usuario WHERE id = :id");
        $verificarUsuario->bindParam(":id", $id);
        $verificarUsuario->execute();
        $usuarioExistente = $verificarUsuario->fetchColumn();

        if ($usuarioExistente == 0) {
            // Si no existe el usuario, retornar un error
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

    } catch (\Throwable $th) {
        $conectar->rollBack();
        error_log("Error en cambiar_contraseña_usuario: " . $th->getMessage());
        echo json_encode(["error" => true, "message" => $th->getMessage()]);
    }
}

function alter_contraseña($dni, $newpass) {
    global $conectar;

    try {
        // Buscar el ID del usuario basado en el DNI de la tabla persona
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
            UPDATE usuario SET password = :password, updated_at = CURRENT_TIMESTAMP WHERE id = :id
        ");
        $actualizarPass->bindParam(":password", $hashedPassword, PDO::PARAM_STR);
        $actualizarPass->bindParam(":id", $id_usuario, PDO::PARAM_INT);
        $actualizarPass->execute();

        // Confirmar cambios
        $conectar->commit();
        echo json_encode(["success" => true, "message" => "Contraseña actualizada con éxito."]);

    } catch (Exception $e) {
        $conectar->rollBack();
        error_log("Error en alter_contraseña: " . $e->getMessage());
        echo json_encode(["error" => true, "message" => "Error al actualizar la contraseña."]);
    }
}
?>