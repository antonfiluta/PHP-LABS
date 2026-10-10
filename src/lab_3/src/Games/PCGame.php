<?php
declare(strict_types=1);

namespace App\Games;

class PCGame extends Game
{
    public function __construct(
        string $title,
        float $price,
        float $sizeGb,
        private string $os = 'Windows 10+',
        private int $ramGb = 8,
        private string $gpu = 'GTX 1060',
    ) {
        parent::__construct($title, $price, $sizeGb);
    }

    public function getRequirements(): string
    {
        return "ОС: {$this->os}, RAM: {$this->ramGb} ГБ, GPU: {$this->gpu}";
    }

    public function info(): string
    {
        return parent::info() . ' (PC)';
    }
}