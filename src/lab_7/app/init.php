<?php
declare(strict_types=1);

session_start();
date_default_timezone_set('Europe/Minsk');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/auth.php';

// Автозагрузка репозиториев
spl_autoload_register(function (string $class): void {
    $file = __DIR__ . '/Repository/' . $class . '.php';
    if (file_exists($file))
        require_once $file;
});