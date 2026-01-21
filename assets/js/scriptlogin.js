async function hashPassword(password) {
    const encoder = new TextEncoder();
    const data = encoder.encode(password);
    const hash = await crypto.subtle.digest('SHA-256', data);
    return Array.from(new Uint8Array(hash))
        .map(b => b.toString(16).padStart(2, '0'))
        .join('');
}

async function iniciarSesion() {
    var usserLogin = document.getElementById("user").value;
    var passLogin = document.getElementById("password").value;
    var errorUserLog = document.getElementById("errorUserLog");
    var errorPassLog = document.getElementById("errorPassLog");

    errorUserLog.innerHTML = "";
    errorPassLog.innerHTML = "";

    // Validaciones con SweetAlert
    if(usserLogin.trim() === "" && passLogin.trim() === ""){
        Swal.fire({
            icon: 'warning',
            title: 'Campos vacíos',
            text: 'Por favor, ingresa tu usuario y contraseña',
            confirmButtonColor: '#3085d6'
        });
        return;
    }
    
    if (usserLogin.trim() === "") {
        Swal.fire({
            icon: 'warning',
            title: 'Usuario requerido',
            text: 'Por favor, ingresa tu usuario',
            confirmButtonColor: '#3085d6'
        });
        return; 
    }

    if (passLogin.trim() === "") {
        Swal.fire({
            icon: 'warning',
            title: 'Contraseña requerida',
            text: 'Por favor, ingresa tu contraseña',
            confirmButtonColor: '#3085d6'
        });
        return; 
    }

    // Mostrar loading mientras se procesa
    Swal.fire({
        title: 'Iniciando sesión...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Realizar el AJAX
    $.ajax({
        method: "POST",
        url: "logica/clsslogin.php",
        data: {
            "accion": "LOGIN",
            "user": usserLogin,
            "password": passLogin
        }
    }).done(async function (text) {
        console.log(text);
        try {
            var userData = JSON.parse(text);
            if(userData){
                if(userData.error){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de autenticación',
                        text: userData.error,
                        confirmButtonColor: '#d33'
                    });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Bienvenido!',
                        text: 'Inicio de sesión exitoso',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = "index.php"; 
                    });
                }
            }
        } catch(e) {
            console.log(e);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al iniciar sesión. Intenta nuevamente.',
                confirmButtonColor: '#d33'
            });
        }
    }).fail(function() {
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor',
            confirmButtonColor: '#d33'
        });
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("form-login");
    if (loginForm) {
        loginForm.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                iniciarSesion();
            }
        });
    }
});