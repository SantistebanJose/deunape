<!DOCTYPE html>
<html lang="es">

<head>
    <script src="assets/js/plugin/webfont/webfont.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/img/logo-captain.png" type="image/x-icon" />
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Public Sans', sans-serif;
            overflow: hidden;
            height: 100vh;
            position: relative;
        }

        /* Fondo peruano mejorado con geometría */
        .geometric-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                linear-gradient(135deg, 
                    #0033A0 0%, 
                    #0055CC 25%,
                    #1a8f4a 50%,
                    #2ecc71 75%,
                    #ffffff 100%
                );
            overflow: hidden;
            z-index: 1;
        }

        .shape {
            position: absolute;
        }

        /* Círculos */
        .circle {
            border: 4px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
        }

        .circle-1 {
            width: 400px;
            height: 400px;
            top: -150px;
            right: -100px;
            border-color: rgba(255, 255, 255, 0.15);
            animation: float 10s ease-in-out infinite;
        }

        .circle-2 {
            width: 300px;
            height: 300px;
            bottom: -80px;
            left: -100px;
            border-color: rgba(0, 51, 160, 0.2);
            animation: float 12s ease-in-out infinite reverse;
        }

        .circle-3 {
            width: 200px;
            height: 200px;
            top: 40%;
            right: 12%;
            border-color: rgba(255, 255, 255, 0.18);
            animation: float 8s ease-in-out infinite;
        }

        .circle-4 {
            width: 150px;
            height: 150px;
            top: 15%;
            left: 20%;
            border-color: rgba(46, 204, 113, 0.25);
            animation: float 9s ease-in-out infinite reverse;
        }

        /* Triángulos mejorados */
        .triangle {
            width: 0;
            height: 0;
            border-style: solid;
            opacity: 0.12;
        }

        .triangle-1 {
            border-width: 0 120px 200px 120px;
            border-color: transparent transparent rgba(255, 255, 255, 0.3) transparent;
            top: 25%;
            left: 8%;
            animation: floatRotate 18s ease-in-out infinite;
        }

        .triangle-2 {
            border-width: 180px 100px 0 100px;
            border-color: rgba(0, 51, 160, 0.15) transparent transparent transparent;
            bottom: 15%;
            right: 20%;
            animation: floatRotate 20s ease-in-out infinite reverse;
        }

        .triangle-3 {
            border-width: 0 80px 140px 80px;
            border-color: transparent transparent rgba(46, 204, 113, 0.2) transparent;
            top: 60%;
            right: 5%;
            animation: floatRotate 15s ease-in-out infinite;
        }

        /* Cuadrados rotados */
        .square {
            background-color: rgba(255, 255, 255, 0.1);
            transform: rotate(45deg);
            opacity: 0.15;
        }

        .square-1 {
            width: 200px;
            height: 200px;
            top: 55%;
            left: 5%;
            background-color: rgba(0, 51, 160, 0.12);
            animation: pulse 8s ease-in-out infinite;
        }

        .square-2 {
            width: 140px;
            height: 140px;
            top: 12%;
            right: 30%;
            background-color: rgba(46, 204, 113, 0.15);
            animation: pulse 10s ease-in-out infinite;
        }

        .square-3 {
            width: 100px;
            height: 100px;
            bottom: 25%;
            left: 25%;
            background-color: rgba(255, 255, 255, 0.12);
            animation: pulse 7s ease-in-out infinite;
        }

        /* Hexágonos peruanos */
        .hexagon {
            width: 150px;
            height: 86px;
            background-color: rgba(0, 51, 160, 0.1);
            position: absolute;
            clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
            opacity: 0.2;
        }

        .hex-1 {
            top: 30%;
            left: 15%;
            animation: rotate 25s linear infinite;
        }

        .hex-2 {
            bottom: 20%;
            right: 15%;
            background-color: rgba(46, 204, 113, 0.12);
            animation: rotate 30s linear infinite reverse;
        }

        /* Líneas decorativas diagonales */
        .diagonal-line {
            position: absolute;
            height: 3px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transform: rotate(-45deg);
            opacity: 0.4;
        }

        .line-1 {
            width: 400px;
            top: 20%;
            left: -100px;
            animation: slideRight 15s ease-in-out infinite;
        }

        .line-2 {
            width: 500px;
            bottom: 30%;
            right: -150px;
            animation: slideLeft 18s ease-in-out infinite;
        }

        /* Puntos decorativos */
        .dot {
            position: absolute;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.3);
        }

        .dot-1 {
            width: 12px;
            height: 12px;
            top: 25%;
            right: 40%;
            animation: twinkle 3s ease-in-out infinite;
        }

        .dot-2 {
            width: 8px;
            height: 8px;
            bottom: 35%;
            left: 35%;
            animation: twinkle 4s ease-in-out infinite 1s;
        }

        .dot-3 {
            width: 10px;
            height: 10px;
            top: 50%;
            left: 15%;
            animation: twinkle 3.5s ease-in-out infinite 0.5s;
        }

        /* Gradientes superpuestos mejorados */
        .gradient-overlay {
            position: absolute;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 20%, rgba(0, 51, 160, 0.2) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(46, 204, 113, 0.2) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Animaciones mejoradas */
        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0);
            }
            50% {
                transform: translateY(-30px) translateX(20px);
            }
        }

        @keyframes floatRotate {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-25px) rotate(10deg);
            }
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: rotate(45deg) scale(1);
                opacity: 0.15;
            }
            50% {
                transform: rotate(45deg) scale(1.15);
                opacity: 0.25;
            }
        }

        @keyframes slideRight {
            0%, 100% {
                transform: translateX(0) rotate(-45deg);
            }
            50% {
                transform: translateX(100px) rotate(-45deg);
            }
        }

        @keyframes slideLeft {
            0%, 100% {
                transform: translateX(0) rotate(-45deg);
            }
            50% {
                transform: translateX(-100px) rotate(-45deg);
            }
        }

        @keyframes twinkle {
            0%, 100% {
                opacity: 0.3;
                transform: scale(1);
            }
            50% {
                opacity: 0.8;
                transform: scale(1.5);
            }
        }

        /* Contenedor principal */
        .login-container {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Tarjeta de login con toque peruano */
        .login-card {
            width: 100%;
            max-width: 460px;
            background: rgba(255, 255, 255, 0.97);
            border-radius: 30px;
            box-shadow: 
                0 25px 70px rgba(0, 51, 160, 0.25),
                0 10px 30px rgba(46, 204, 113, 0.15);
            padding: 50px 45px;
            backdrop-filter: blur(15px);
            animation: slideIn 0.7s ease-out;
            border: 3px solid rgba(255, 255, 255, 0.8);
            position: relative;
            overflow: hidden;
        }

        /* Decoración superior de la tarjeta */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #0033A0 0%, #0033A0 50%, #2ecc71 50%, #2ecc71 100%);
        }

        .login-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #2ecc71 0%, #2ecc71 50%, #0033A0 50%, #0033A0 100%);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header */
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-container {
            margin-bottom: 20px;
            position: relative;
        }

        .logo-container img {
            height: 120px;
            filter: drop-shadow(0 6px 12px rgba(0, 51, 160, 0.2));
            animation: logoFloat 3s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-12px);
            }
        }

        .login-title {
            font-size: 1.3rem;
            background: linear-gradient(135deg, #0033A0 0%, #0066CC 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            margin-top: 15px;
            letter-spacing: 0.5px;
        }

        .login-subtitle {
            font-size: 1.85rem;
            font-weight: 800;
            color: #1a1a1a;
            margin: 12px 0 5px 0;
            line-height: 1.3;
        }

        .login-description {
            font-size: 1rem;
            color: #555;
        }

        .highlight-text {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }

        /* Formulario */
        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-control {
            border: 2.5px solid #dfe6e9;
            border-radius: 14px;
            padding: 13px 20px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            border-color: #0066CC;
            box-shadow: 0 0 0 0.25rem rgba(0, 102, 204, 0.12);
            background-color: white;
            outline: none;
        }

        .input-group {
            position: relative;
        }

        .input-group .form-control {
            padding-right: 50px;
        }

        #togglePassword {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #7f8c8d;
            padding: 8px 12px;
            cursor: pointer;
            transition: color 0.3s ease;
            z-index: 10;
        }

        #togglePassword:hover {
            color: #0066CC;
        }

        .error-message {
            color: #e74c3c;
            font-size: 0.85rem;
            margin-top: 5px;
            display: block;
        }

        /* Botón peruano */
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #0033A0 0%, #0066CC 50%, #2ecc71 100%);
            color: white;
            border: none;
            border-radius: 14px;
            padding: 15px;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 
                0 6px 20px rgba(0, 51, 160, 0.3),
                0 3px 10px rgba(46, 204, 113, 0.2);
            margin-top: 10px;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.5px;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #002080 0%, #0055BB 50%, #27ae60 100%);
            transform: translateY(-3px);
            box-shadow: 
                0 8px 25px rgba(0, 51, 160, 0.4),
                0 5px 15px rgba(46, 204, 113, 0.3);
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #ecf0f1;
        }

        .footer-text {
            font-size: 0.9rem;
            color: #7f8c8d;
            line-height: 1.6;
        }

        .footer-text strong {
            color: #2c3e50;
            display: block;
            margin-bottom: 5px;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .login-card {
                padding: 40px 30px;
            }

            .login-subtitle {
                font-size: 1.5rem;
            }

            .shape {
                opacity: 0.08;
            }
        }
    </style>
</head>

<body>
    <!-- Fondo geométrico peruano -->
    <div class="geometric-background">
        <div class="gradient-overlay"></div>
        
        <!-- Círculos -->
        <div class="shape circle circle-1"></div>
        <div class="shape circle circle-2"></div>
        <div class="shape circle circle-3"></div>
        <div class="shape circle circle-4"></div>
        
        <!-- Triángulos -->
        <div class="shape triangle triangle-1"></div>
        <div class="shape triangle triangle-2"></div>
        <div class="shape triangle triangle-3"></div>
        
        <!-- Cuadrados -->
        <div class="shape square square-1"></div>
        <div class="shape square square-2"></div>
        <div class="shape square square-3"></div>
        
        <!-- Hexágonos -->
        <div class="shape hexagon hex-1"></div>
        <div class="shape hexagon hex-2"></div>
        
        <!-- Líneas diagonales -->
        <div class="shape diagonal-line line-1"></div>
        <div class="shape diagonal-line line-2"></div>
        
        <!-- Puntos brillantes -->
        <div class="shape dot dot-1"></div>
        <div class="shape dot dot-2"></div>
        <div class="shape dot dot-3"></div>
    </div>

    <!-- Contenedor de login -->
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-container">
                    <img src="assets/img/logo-captain.png" alt="DeUnaPe Logo" />
                </div>
                <div class="login-title">DeUnaPe - Captain</div>
                <h2 class="login-subtitle">Quieres más, tu negocio te exige.</h2>
                <p class="login-description"><span class="highlight-text">Aquí está el control que mereces</span></p>
            </div>
            

            <form id="form-login">
                <div class="form-group">
                    <label for="user" class="form-label">Cuenta de Usuario *</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="user" 
                        placeholder="Ingresa tu usuario"
                        autocomplete="username"
                    >
                    <span id="errorUserLog" class="error-message"></span>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            placeholder="Ingresa la contraseña"
                            autocomplete="current-password"
                        >
                        <button type="button" id="togglePassword">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                    <span id="errorPassLog" class="error-message"></span>
                </div>

                <button type="button" onclick="iniciarSesion()" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
            </form>

            <div class="login-footer">
                <p class="footer-text">
                    <strong>¿No recuerdas tu contraseña?</strong>
                    Contactar con la dueña para que recupere tu contraseña 🙂
                </p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/scriptlogin.js"></script>

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Toggle password visibility
        document.getElementById("togglePassword").addEventListener("click", function() {
            var passwordField = document.getElementById("password");
            var icon = this.querySelector("i");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        });

        // Prevenir submit con Enter
        document.getElementById("form-login").addEventListener("submit", function(e) {
            e.preventDefault();
        });
    </script>
</body>

</html>