<?php
declare(strict_types=1);

require_once __DIR__ . '/src/session.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Не авторизован']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$_SESSION['progress'] = [];

echo json_encode([
    'status' => 'ok',
    'progress' => [],
], JSON_UNESCAPED_UNICODE);