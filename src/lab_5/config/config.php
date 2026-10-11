<?php
declare(strict_types=1);

/**
 * Конфигурация приложения: автозагрузка классов, выбор хранилища.
 */

define('ROOT_DIR', dirname(__DIR__));

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Выбор хранилища из сессии (JSON по умолчанию)
define('STORAGE_TYPE', $_SESSION['storage_type'] ?? 'json');

/**
 * Автозагрузка классов из app/controllers и app/models.
 */
spl_autoload_register(function (string $class): void {
    foreach ([ROOT_DIR . '/app/controllers/', ROOT_DIR . '/app/models/'] as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});