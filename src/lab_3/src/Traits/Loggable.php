<?php
declare(strict_types=1);

namespace App\Traits;

/**
 * Трейт логирования: добавляет метод log() в любой класс.
 */
trait Loggable
{
    protected function log(string $message): void
    {
        echo "<div class='text-sky-300 text-sm mt-2'>[LOG] "
            . htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
            . "</div>";
    }
}