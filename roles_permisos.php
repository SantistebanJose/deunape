<?php
// roles_permisos.php - Sistema completo de Roles y Permisos
session_start();

// ============================================
// CONFIGURACIÓN DE CONEXIÓN A POSTGRESQL
// ============================================
$server = "aws-1-us-east-1.pooler.supabase.com";
$bd = "postgres";
$user = "postgres.jsrtcyygjhxnrtgbmwrp";
$pass = "LqBG4VVUrK6_jcy";
$port = "5432";

try {
    $pdo = new PDO("pgsql:host=$server;port=$port;dbname=$bd", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de Conexión: " . $e->getMessage());
}

// ============================================
// CREAR TABLAS SI NO EXISTEN
// ============================================
function inicializarBaseDatos($pdo) {
    try {
        // Tabla de roles
        $pdo->exec("CREATE TABLE IF NOT EXISTS roles (
            id_rol SERIAL PRIMARY KEY,
            nombre_rol VARCHAR(100) NOT NULL,
            descripcion VARCHAR(255),
            sucursal_id INTEGER DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Tabla de módulos
        $pdo->exec("CREATE TABLE IF NOT EXISTS modulos (
            id_modulo SERIAL PRIMARY KEY,
            nombre_modulo VARCHAR(100) NOT NULL,
            icono VARCHAR(50),
            identificador VARCHAR(50)
        )");

        // Tabla de submódulos
        $pdo->exec("CREATE TABLE IF NOT EXISTS submodulos (
            id_submodulo SERIAL PRIMARY KEY,
            id_modulo INTEGER REFERENCES modulos(id_modulo),
            nombre_submodulo VARCHAR(100) NOT NULL,
            url VARCHAR(255)
        )");

        // Tabla de permisos de módulos
        $pdo->exec("CREATE TABLE IF NOT EXISTS permisos_modulo (
            id SERIAL PRIMARY KEY,
            id_rol INTEGER REFERENCES roles(id_rol) ON DELETE CASCADE,
            id_modulo INTEGER REFERENCES modulos(id_modulo)
        )");

        // Tabla de permisos de submódulos
        $pdo->exec("CREATE TABLE IF NOT EXISTS permisos_submodulo (
            id SERIAL PRIMARY KEY,
            id_rol INTEGER REFERENCES roles(id_rol) ON DELETE CASCADE,
            id_submodulo INTEGER REFERENCES submodulos(id_submodulo)
        )");

        // Verificar si hay datos, si no, insertar datos de ejemplo
        $count = $pdo->query("SELECT COUNT(*) FROM modulos")->fetchColumn();
        
        if ($count == 0) {
            // Insertar módulos de ejemplo
            $pdo->exec("INSERT INTO modulos (nombre_modulo, icono, identificador) VALUES 
                ('Administrador', 'fas fa-cog', 'administrador'),
                ('Negocio', 'fas fa-store-alt', 'negocio'),
                ('Facturador SUNAT', 'fab fa-stripe-s', 'facturador_sunat'),
                ('Crédito', 'fas fa-user-lock', 'credito'),
                ('Venta', 'fas fa-cart-plus', 'venta'),
                ('Pago', 'fas fa-credit-card', 'pago')");

            // Insertar submódulos de ejemplo
            $pdo->exec("INSERT INTO submodulos (id_modulo, nombre_submodulo, url) VALUES 
                (1, 'Trabajadores', 'Empleados.php'),
                (1, 'Usuarios', 'usuario.php'),
                (1, 'Artículos', 'articulos.php'),
                (2, 'Gestionar Compras', 'compra.php'),
                (2, 'Caja Chica', 'cajaChica.php'),
                (3, 'Datos de Emisor', 'emisor.php'),
                (4, 'Realizar Abono', 'pagoCredito.php'),
                (5, 'Venta Rápida', 'venta_rapida_v2.php'),
                (6, 'Listado de Pagos', 'listadoPagos.php')");

            // Insertar roles de ejemplo
            $pdo->exec("INSERT INTO roles (nombre_rol, descripcion, sucursal_id) VALUES 
                ('Super Administrador', 'Acceso total al sistema', 1),
                ('Vendedor', 'Acceso a ventas y pagos', 1),
                ('Cajero', 'Manejo de caja y pagos', 1)");
        }
        
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Inicializar la base de datos
inicializarBaseDatos($pdo);

// ============================================
// PROCESAR ACCIONES AJAX
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'crear_rol':
            $nombre_rol = $_POST['nombre_rol'];
            $descripcion = $_POST['descripcion'];
            $sucursal_id = $_POST['sucursal_id'] ?? 1;
            
            $stmt = $pdo->prepare("INSERT INTO roles (nombre_rol, descripcion, sucursal_id) VALUES (?, ?, ?)");
            $stmt->execute([$nombre_rol, $descripcion, $sucursal_id]);
            
            echo json_encode(['success' => true, 'id_rol' => $pdo->lastInsertId()]);
            exit;
            
        case 'guardar_permisos':
            $id_rol = $_POST['id_rol'];
            $permisos_modulos = json_decode($_POST['permisos_modulos'], true);
            $permisos_submodulos = json_decode($_POST['permisos_submodulos'], true);
            
            // Eliminar permisos existentes
            $pdo->prepare("DELETE FROM permisos_modulo WHERE id_rol = ?")->execute([$id_rol]);
            $pdo->prepare("DELETE FROM permisos_submodulo WHERE id_rol = ?")->execute([$id_rol]);
            
            // Insertar nuevos permisos de módulos
            $stmt = $pdo->prepare("INSERT INTO permisos_modulo (id_rol, id_modulo) VALUES (?, ?)");
            foreach ($permisos_modulos as $id_modulo => $activo) {
                if ($activo) {
                    $stmt->execute([$id_rol, $id_modulo]);
                }
            }
            
            // Insertar nuevos permisos de submódulos
            $stmt = $pdo->prepare("INSERT INTO permisos_submodulo (id_rol, id_submodulo) VALUES (?, ?)");
            foreach ($permisos_submodulos as $id_submodulo => $activo) {
                if ($activo) {
                    $stmt->execute([$id_rol, $id_submodulo]);
                }
            }
            
            echo json_encode(['success' => true]);
            exit;
            
        case 'obtener_permisos':
            $id_rol = $_POST['id_rol'];
            
            $modulos = $pdo->prepare("SELECT id_modulo FROM permisos_modulo WHERE id_rol = ?");
            $modulos->execute([$id_rol]);
            $permisos_modulos = $modulos->fetchAll(PDO::FETCH_COLUMN);
            
            $submodulos = $pdo->prepare("SELECT id_submodulo FROM permisos_submodulo WHERE id_rol = ?");
            $submodulos->execute([$id_rol]);
            $permisos_submodulos = $submodulos->fetchAll(PDO::FETCH_COLUMN);
            
            echo json_encode([
                'modulos' => $permisos_modulos,
                'submodulos' => $permisos_submodulos
            ]);
            exit;
    }
}

// ============================================
// OBTENER DATOS PARA LA VISTA
// ============================================
$roles = $pdo->query("SELECT * FROM roles ORDER BY nombre_rol")->fetchAll(PDO::FETCH_ASSOC);
$modulos = $pdo->query("SELECT * FROM modulos ORDER BY nombre_modulo")->fetchAll(PDO::FETCH_ASSOC);
$submodulos = $pdo->query("SELECT * FROM submodulos ORDER BY id_modulo, nombre_submodulo")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Roles y Permisos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f9fafb;
            padding: 24px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: linear-gradient(135deg, #2563eb 0%, #9333ea 100%);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
        }

        .header-content i {
            font-size: 40px;
        }

        .header-title h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .header-title p {
            color: #bfdbfe;
            font-size: 14px;
        }

        .btn-nuevo {
            background: white;
            color: #2563eb;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s;
        }

        .btn-nuevo:hover {
            background-color: #eff6ff;
        }

        .mensaje {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: none;
            align-items: center;
            gap: 8px;
        }

        .mensaje.show {
            display: flex;
        }

        .mensaje.success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .mensaje.error {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .formulario {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: none;
        }

        .formulario.show {
            display: block;
        }

        .formulario h2 {
            margin-bottom: 16px;
            font-size: 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 10px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2563eb;
        }

        .form-buttons {
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s;
        }

        .btn-primary {
            background-color: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        .btn-secondary {
            background-color: #d1d5db;
            color: #374151;
        }

        .btn-secondary:hover {
            background-color: #9ca3af;
        }

        .btn-success {
            background-color: #16a34a;
            color: white;
        }

        .btn-success:hover {
            background-color: #15803d;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 24px;
        }

        .roles-panel {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .roles-panel h2 {
            font-size: 20px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rol-item {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.2s;
            background-color: #f9fafb;
        }

        .rol-item:hover {
            background-color: #f3f4f6;
        }

        .rol-item.active {
            background-color: #dbeafe;
            border: 2px solid #2563eb;
        }

        .rol-item-titulo {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .rol-item-desc {
            font-size: 14px;
            color: #6b7280;
        }

        .permisos-panel {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .permisos-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .permisos-header h2 {
            font-size: 20px;
        }

        .empty-state {
            text-align: center;
            padding: 48px;
        }

        .empty-state i {
            font-size: 64px;
            color: #d1d5db;
            margin-bottom: 16px;
        }

        .empty-state p {
            color: #6b7280;
            font-size: 18px;
        }

        .modulo {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 16px;
            overflow: hidden;
        }

        .modulo-header {
            padding: 16px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.2s;
            background-color: #f9fafb;
        }

        .modulo-header.active {
            background-color: #eff6ff;
        }

        .modulo-header:hover {
            background-color: #f3f4f6;
        }

        .modulo-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modulo-info i {
            font-size: 20px;
        }

        .modulo-info span {
            font-weight: 600;
            font-size: 18px;
        }

        .checkbox {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
        }

        .checkbox.active {
            background-color: #16a34a;
        }

        .checkbox.inactive {
            background-color: #d1d5db;
        }

        .checkbox i {
            color: white;
            font-size: 14px;
        }

        .submodulos {
            background-color: white;
            padding: 16px;
            display: none;
        }

        .submodulos.show {
            display: block;
        }

        .submodulo-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .submodulo-item:hover {
            background-color: #f9fafb;
        }

        .submodulo-item span {
            color: #374151;
        }

        .checkbox-small {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .checkbox-small.active {
            background-color: #16a34a;
        }

        .checkbox-small.inactive {
            background-color: #d1d5db;
        }

        .checkbox-small i {
            color: white;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <i class="fas fa-shield-alt"></i>
                <div class="header-title">
                    <h1>Gestión de Roles y Permisos</h1>
                    <p>Administra el acceso al sistema por roles de usuario</p>
                </div>
            </div>
            <button class="btn-nuevo" onclick="mostrarFormulario()">
                <i class="fas fa-plus"></i>
                Nuevo Rol
            </button>
        </div>

        <div class="mensaje" id="mensaje">
            <i class="fas fa-exclamation-circle"></i>
            <span id="mensajeTexto"></span>
        </div>

        <div class="formulario" id="formularioRol">
            <h2>Crear Nuevo Rol</h2>
            <form onsubmit="crearRol(event)">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nombre del Rol</label>
                        <input type="text" id="nombre_rol" name="nombre_rol" placeholder="Ej: Gerente de Ventas" required>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <input type="text" id="descripcion" name="descripcion" placeholder="Descripción del rol">
                    </div>
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Guardar Rol
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="ocultarFormulario()">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>

        <div class="main-grid">
            <div class="roles-panel">
                <h2>
                    <i class="fas fa-users"></i>
                    Roles del Sistema
                </h2>
                <div id="listaRoles">
                    <?php foreach ($roles as $rol): ?>
                    <div class="rol-item" onclick="seleccionarRol(<?= $rol['id_rol'] ?>, '<?= htmlspecialchars($rol['nombre_rol']) ?>')">
                        <div class="rol-item-titulo"><?= htmlspecialchars($rol['nombre_rol']) ?></div>
                        <div class="rol-item-desc"><?= htmlspecialchars($rol['descripcion']) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="permisos-panel">
                <div id="permisosVacio" class="empty-state">
                    <i class="fas fa-cog"></i>
                    <p>Selecciona un rol para gestionar sus permisos</p>
                </div>

                <div id="permisosContenido" style="display: none;">
                    <div class="permisos-header">
                        <h2>Permisos de: <span id="rolNombre"></span></h2>
                        <button class="btn btn-success" onclick="guardarPermisos()">
                            <i class="fas fa-save"></i>
                            Guardar Cambios
                        </button>
                    </div>

                    <div id="listaModulos">
                        <?php 
                        foreach ($modulos as $modulo):
                            $subsDelModulo = array_filter($submodulos, function($s) use ($modulo) {
                                return $s['id_modulo'] == $modulo['id_modulo'];
                            });
                        ?>
                        <div class="modulo" data-modulo="<?= $modulo['id_modulo'] ?>">
                            <div class="modulo-header" onclick="toggleModulo(<?= $modulo['id_modulo'] ?>)">
                                <div class="modulo-info">
                                    <i class="<?= $modulo['icono'] ?>"></i>
                                    <span><?= htmlspecialchars($modulo['nombre_modulo']) ?></span>
                                </div>
                                <div class="checkbox inactive" id="check-modulo-<?= $modulo['id_modulo'] ?>">
                                    <i class="fas fa-times"></i>
                                </div>
                            </div>

                            <?php if (count($subsDelModulo) > 0): ?>
                            <div class="submodulos" id="subs-<?= $modulo['id_modulo'] ?>">
                                <?php foreach ($subsDelModulo as $sub): ?>
                                <div class="submodulo-item" onclick="toggleSubmodulo(<?= $sub['id_submodulo'] ?>, <?= $modulo['id_modulo'] ?>)">
                                    <span><?= htmlspecialchars($sub['nombre_submodulo']) ?></span>
                                    <div class="checkbox-small inactive" id="check-sub-<?= $sub['id_submodulo'] ?>">
                                        <i class="fas fa-times"></i>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let rolActual = null;
        let permisos = {
            modulos: {},
            submodulos: {}
        };

        const submodulosPorModulo = <?= json_encode(
            array_reduce($submodulos, function($carry, $item) {
                $carry[$item['id_modulo']][] = $item['id_submodulo'];
                return $carry;
            }, [])
        ) ?>;

        function mostrarFormulario() {
            document.getElementById('formularioRol').classList.add('show');
        }

        function ocultarFormulario() {
            document.getElementById('formularioRol').classList.remove('show');
            document.getElementById('nombre_rol').value = '';
            document.getElementById('descripcion').value = '';
        }

        function mostrarMensaje(tipo, texto) {
            const mensaje = document.getElementById('mensaje');
            const mensajeTexto = document.getElementById('mensajeTexto');
            
            mensaje.className = 'mensaje show ' + tipo;
            mensajeTexto.textContent = texto;
            
            setTimeout(() => {
                mensaje.classList.remove('show');
            }, 3000);
        }

        function crearRol(event) {
            event.preventDefault();
            
            const formData = new FormData();
            formData.append('action', 'crear_rol');
            formData.append('nombre_rol', document.getElementById('nombre_rol').value);
            formData.append('descripcion', document.getElementById('descripcion').value);
            formData.append('sucursal_id', 1);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensaje('success', 'Rol creado correctamente');
                    ocultarFormulario();
                    setTimeout(() => location.reload(), 1500);
                }
            })
            .catch(error => {
                mostrarMensaje('error', 'Error al crear el rol');
            });
        }

        function seleccionarRol(idRol, nombreRol) {
            rolActual = idRol;
            
            document.querySelectorAll('.rol-item').forEach(item => {
                item.classList.remove('active');
            });
            event.currentTarget.classList.add('active');
            
            document.getElementById('permisosVacio').style.display = 'none';
            document.getElementById('permisosContenido').style.display = 'block';
            document.getElementById('rolNombre').textContent = nombreRol;
            
            cargarPermisos(idRol);
        }

        function cargarPermisos(idRol) {
            const formData = new FormData();
            formData.append('action', 'obtener_permisos');
            formData.append('id_rol', idRol);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                permisos.modulos = {};
                permisos.submodulos = {};
                
                data.modulos.forEach(id => {
                    permisos.modulos[id] = true;
                });
                
                data.submodulos.forEach(id => {
                    permisos.submodulos[id] = true;
                });
                
                actualizarUI();
            });
        }

        function toggleModulo(idModulo) {
            const nuevoEstado = !permisos.modulos[idModulo];
            permisos.modulos[idModulo] = nuevoEstado;
            
            if (submodulosPorModulo[idModulo]) {
                submodulosPorModulo[idModulo].forEach(idSub => {
                    permisos.submodulos[idSub] = nuevoEstado;
                });
            }
            
            actualizarUI();
        }

        function toggleSubmodulo(idSubmodulo, idModulo) {
            event.stopPropagation();
            permisos.submodulos[idSubmodulo] = !permisos.submodulos[idSubmodulo];
            actualizarUI();
        }

        function actualizarUI() {
            Object.keys(permisos.modulos).forEach(id => {
                const checkbox = document.getElementById('check-modulo-' + id);
                const header = checkbox.closest('.modulo-header');
                const submodulos = document.getElementById('subs-' + id);
                
                if (permisos.modulos[id]) {
                    checkbox.className = 'checkbox active';
                    checkbox.innerHTML = '<i class="fas fa-check"></i>';
                    header.classList.add('active');
                    if (submodulos) submodulos.classList.add('show');
                } else {
                    checkbox.className = 'checkbox inactive';
                    checkbox.innerHTML = '<i class="fas fa-times"></i>';
                    header.classList.remove('active');
                    if (submodulos) submodulos.classList.remove('show');
                }
            });
            
            Object.keys(permisos.submodulos).forEach(id => {
                const checkbox = document.getElementById('check-sub-' + id);
                if (checkbox) {
                    if (permisos.submodulos[id]) {
                        checkbox.className = 'checkbox-small active';
                        checkbox.innerHTML = '<i class="fas fa-check"></i>';
                    } else {
                        checkbox.className = 'checkbox-small inactive';
                        checkbox.innerHTML = '<i class="fas fa-times"></i>';
                    }
                }
            });
        }

        function guardarPermisos() {
            if (!rolActual) return;
            
            const formData = new FormData();
            formData.append('action', 'guardar_permisos');
            formData.append('id_rol', rolActual);
            formData.append('permisos_modulos', JSON.stringify(permisos.modulos));
            formData.append('permisos_submodulos', JSON.stringify(permisos.submodulos));
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensaje('success', 'Permisos guardados correctamente');
                }
            })
            .catch(error => {
                mostrarMensaje('error', 'Error al guardar permisos');
            });
        }
    </script>
</body>
</html>