<?php
declare(strict_types=1);

define('ROOT_DIR', dirname(__DIR__));

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

define('STORAGE_TYPE', $_SESSION['storage_type'] ?? 'json');

spl_autoload_register(function (string $class): void {
    foreach ([ROOT_DIR . '/app/controllers/', ROOT_DIR . '/app/models/'] as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});