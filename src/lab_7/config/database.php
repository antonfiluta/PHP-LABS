<?php
declare(strict_types=1);

/**
 * Singleton PDO-подключения.
 * Настройки — через переменные окружения, с разумными дефолтами.
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $name = getenv('DB_NAME') ?: 'school_diary';
    $user = getenv('DB_USER') ?: 'diary_user';
    $pass = getenv('DB_PASS') ?: 'DiaryPass_2026!';

    $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}