<?php
declare(strict_types=1);

require_once __DIR__ . '/src/session.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$theme = $input['theme'] ?? 'dark';
$lang = $input['lang'] ?? 'ru';

$allowedThemes = ['dark', 'light'];
$allowedLangs = ['ru', 'en'];

if (!in_array($theme, $allowedThemes, true)) {
    $theme = 'dark';
}
if (!in_array($lang, $allowedLangs, true)) {
    $lang = 'ru';
}

// Устанавливаем безопасные куки на 30 дней
$options = [
    'expires' => time() + 86400 * 30,
    'path' => '/',
    'httponly' => false,   // тема нужна JS для мгновенного применения
    'samesite' => 'Lax',
];

setcookie('theme', $theme, $options);
setcookie('lang', $lang, $options);

echo json_encode(['status' => 'ok', 'theme' => $theme, 'lang' => $lang]);