<?php
declare(strict_types=1);

/** Сгенерировать (или вернуть существующий) CSRF-токен. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/** Проверить CSRF-токен. */
function csrf_check(?string $token): bool
{
    return isset($_SESSION['csrf'])
        && is_string($token)
        && hash_equals($_SESSION['csrf'], $token);
}

/** Экранирование HTML (защита от XSS). */
function e(mixed $v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Редирект и выход. */
function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}