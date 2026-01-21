<?php
session_start(); // Iniciar la sesión, necesario para usar $_SESSION

// Verificar si el usuario ha iniciado sesión
if (isset($_SESSION['id'])) {
    header("Location: index.php");
}

?>

<!DOCTYPE html>
<html lang="en">


<head>
    <script src="assets/js/plugin/webfont/webfont.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link
        rel="icon"
        href="assets/img/caracoles.png"
        type="image/x-icon" />
    <title>Caracol Login</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["assets/css/fonts.min.css"],
            },
            active: function() {
                sessionStorage.fonts = true;
            },
        });
    </script>
    <style>
        body {
            background-color: #E9EEF1;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 100%;
            max-width: 500px;
            /* Aumenta el tamaño máximo del card */
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .login-header {
            background-color: #1a2035;
            color: #fff;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            padding: 20px;
            text-align: center;
        }

        .btn-custom {
            background-color: #1a2035;
            color: #fff;
        }

        .btn-custom:hover {
            background-color: #1a2035;
            color: #ffff;
        }
    </style>
</head>

<body>

    <div class="container login-container">


        <div class="card login-card">
            <div class="login-header">
                <img
                    src="assets/img/caracolesv2.png"
                    class="navbar-brand"
                    height="100" />
                <h3 style="color: white;" class="card-title">Caracol Soft - VYSAM</h3>
            </div>
            <div id="form-login" class="card-body">
                <div class="mb-4">
                    <label for="" class="form-label"> <strong>Usuario</strong></label>
                    <input type="text" min="0" class="form-control" id="user" placeholder="user0001">
                    <div id="errorUserLog" style="color: red; font-size: 1rem;"></div>
                </div>
                <div class="mb-4">
                    <label for="" class="form-label"> <strong>Contraseña:</strong></label>

                    <div class="input-group">
                        <input type="password" class="form-control" id="password" placeholder="*****">

                        <button type="button" id="togglePassword" class="btn btn-black"><i class="far fa-eye"></i></button>
                    </div>
                    <div id="errorPassLog" style="color: red; font-size: 1rem;"></div>
                </div>

                <div class="d-grid">
                    <button onclick="iniciarSesion()" class="btn btn-custom btn-block"><i class="fas fa-door-open"></i> Iniciar Sesión</button>
                </div>
                <div class="card-sub text-center">
                    <strong>¿No recuerdas tu contraseña?</strong>
                    <br>
                    Contactar con la dueña para que recupere tú contraseña 🙂
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/js/scriptlogin.js"></script>


<!-- CSS de SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- JS de SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</html>
<script>
    document.getElementById("togglePassword").addEventListener("click", function() {
        var passwordField = document.getElementById("password");
        var passwordFieldType = passwordField.type;

        // Si el tipo del input es password, lo cambiamos a text
        if (passwordFieldType === "password") {
            passwordField.type = "text";
            this.innerHTML = '<i class= "fas fa-eye-slash"></i>'; // Cambia el texto del botón
        } else {
            passwordField.type = "password"; // Si es text, lo cambiamos a password
            this.innerHTML = '<i class= "fas fa-eye"></i>'; // Cambia el texto del botón
        }
    });
</script>