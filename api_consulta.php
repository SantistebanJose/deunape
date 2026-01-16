<?php
// api_consulta.php
// Endpoint intermedio para consultar la API de Decolecta de forma segura

header('Content-Type: application/json');

// Incluir la configuración con el token
require_once 'config_api.php';

// Verificar que la petición sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

// Obtener los datos enviados
$tipo = $_POST['tipo'] ?? '';
$numero = $_POST['numero'] ?? '';

// Validar datos
if (empty($tipo) || empty($numero)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Parámetros incompletos'
    ]);
    exit;
}

// Validar tipo de consulta
if (!in_array($tipo, ['dni', 'ruc'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Tipo de consulta inválido'
    ]);
    exit;
}

// Validar formato del número
if ($tipo === 'dni' && !preg_match('/^\d{8}$/', $numero)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'DNI debe tener 8 dígitos'
    ]);
    exit;
}

if ($tipo === 'ruc' && !preg_match('/^\d{11}$/', $numero)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'RUC debe tener 11 dígitos'
    ]);
    exit;
}

// Construir la URL según el tipo
if ($tipo === 'dni') {
    $url = DECOLECTA_API_DNI . '?numero=' . $numero;
} else {
    $url = DECOLECTA_API_RUC . '?numero=' . $numero;
}

// Configurar cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . DECOLECTA_API_TOKEN,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// Ejecutar la petición
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Verificar errores de cURL
if ($error) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al conectar con el servicio: ' . $error
    ]);
    exit;
}

// Verificar código de respuesta HTTP
if ($httpCode !== 200) {
    http_response_code($httpCode);
    echo json_encode([
        'success' => false,
        'message' => 'Error en la consulta',
        'http_code' => $httpCode,
        'response' => json_decode($response)
    ]);
    exit;
}

// Decodificar la respuesta
$data = json_decode($response, true);

// Verificar si la decodificación fue exitosa
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar la respuesta de la API'
    ]);
    exit;
}

// Formatear la respuesta según el tipo
if ($tipo === 'dni') {
    // Respuesta para DNI
    if (isset($data['success']) && $data['success'] === true && isset($data['data'])) {
        echo json_encode([
            'success' => true,
            'data' => [
                'nombres' => $data['data']['nombres'] ?? '',
                'apellidoPaterno' => $data['data']['apellido_paterno'] ?? '',
                'apellidoMaterno' => $data['data']['apellido_materno'] ?? '',
                'nombres_completo' => ($data['data']['nombres'] ?? '') . ' ' . 
                                     ($data['data']['apellido_paterno'] ?? '') . ' ' . 
                                     ($data['data']['apellido_materno'] ?? '')
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se encontraron datos para el DNI proporcionado'
        ]);
    }
} else {
    // Respuesta para RUC
    if (isset($data['success']) && $data['success'] === true && isset($data['data'])) {
        echo json_encode([
            'success' => true,
            'data' => [
                'razonSocial' => $data['data']['razon_social'] ?? '',
                'nombreComercial' => $data['data']['nombre_comercial'] ?? ($data['data']['razon_social'] ?? ''),
                'estado' => $data['data']['estado'] ?? '',
                'condicion' => $data['data']['condicion'] ?? '',
                'direccion' => $data['data']['direccion'] ?? '',
                'departamento' => $data['data']['departamento'] ?? '',
                'provincia' => $data['data']['provincia'] ?? '',
                'distrito' => $data['data']['distrito'] ?? ''
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se encontraron datos para el RUC proporcionado'
        ]);
    }
}
?>