<?php
declare(strict_types=1);

namespace App\Games;

class ConsoleGame extends Game
{
    public function __construct(
        string $title,
        float $price,
        float $sizeGb,
        private string $platform = 'PlayStation 5',
    ) {
        parent::__construct($title, $price, $sizeGb);
    }

    public function getPlatform(): string
    {
        return $this->platform;
    }

    public function getRequirements(): string
    {
        return "Платформа: {$this->platform}";
    }

    public function info(): string
    {
        return parent::info() . " ({$this->platform})";
    }
}