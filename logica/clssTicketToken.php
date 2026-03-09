<?php

require_once dirname(__DIR__) . '/ticket.php'; // carga ticket_token()

header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID inválido']);
    exit;
}

echo json_encode([
    'url' => '../ticket.php?id=' . $id . '&token=' . ticket_token($id)
]);