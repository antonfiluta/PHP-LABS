<?php
declare(strict_types=1);

/**
 * Безопасная инициализация сессии и загрузка настроек из куки.
 */

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.use_strict_mode', '1');
    session_start();
}

// Автологаут через 30 минут бездействия
if (isset($_SESSION['last_action']) && time() - $_SESSION['last_action'] > 1800) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['last_action'] = time();

// Тема и язык из куки (с дефолтами)
$theme = $_COOKIE['theme'] ?? 'dark';
$lang = $_COOKIE['lang'] ?? 'ru';

// Прогресс пользователя из сессии
if (!isset($_SESSION['progress'])) {
    $_SESSION['progress'] = [];
}