<?php
declare(strict_types=1);

namespace App\Games;

/**
 * Абстрактный базовый класс игры.
 */
abstract class Game
{
    public function __construct(
        protected string $title,
        protected float $price,
        protected float $sizeGb,
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }
    public function getPrice(): float
    {
        return $this->price;
    }
    public function getSize(): float
    {
        return $this->sizeGb;
    }

    /** Установить игру. */
    public function install(): string
    {
        return "Установка «{$this->title}» ({$this->sizeGb} ГБ)...";
    }

    /** Удалить игру. */
    public function uninstall(): string
    {
        return "Удаление «{$this->title}»...";
    }

    /** Обновить игру. */
    public function update(): string
    {
        return "Обновление «{$this->title}»...";
    }

    /**
     * Абстрактный метод: требования зависят от платформы.
     */
    abstract public function getRequirements(): string;

    /** Краткая информация. */
    public function info(): string
    {
        return "{$this->title}: " . number_format($this->price, 2, '.', ' ') . " руб., {$this->sizeGb} ГБ";
    }
}