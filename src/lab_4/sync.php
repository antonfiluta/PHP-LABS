<?php
declare(strict_types=1);

require_once __DIR__ . '/src/session.php';
require_once __DIR__ . '/src/courses.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Не авторизован']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Клиент запрашивает прогресс с сервера
    echo json_encode([
        'progress' => $_SESSION['progress'] ?? [],
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $clientProgress = $input['progress'] ?? [];

    if (!is_array($clientProgress)) {
        http_response_code(400);
        echo json_encode(['error' => 'Некорректный формат']);
        exit;
    }

    $courses = get_courses();

    // Мержим: completed-модуль остаётся completed, если отмечен хотя бы на одной стороне
    foreach ($clientProgress as $moduleId => $data) {
        if (!is_string($moduleId) || find_module($courses, $moduleId) === null) {
            continue;
        }
        $clientDone = (bool) ($data['done'] ?? false);
        $serverDone = (bool) ($_SESSION['progress'][$moduleId]['done'] ?? false);

        $_SESSION['progress'][$moduleId] = [
            'done' => $clientDone || $serverDone,
            'ts' => (int) ($data['ts'] ?? time()),
        ];
    }

    echo json_encode([
        'status' => 'ok',
        'progress' => $_SESSION['progress'],
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);