<?php
declare(strict_types=1);

namespace App\Games;

class MobileGame extends Game
{
    public function __construct(
        string $title,
        float $price,
        float $sizeGb,
        private string $minAndroid = 'Android 10',
        private string $minIos = 'iOS 14',
    ) {
        parent::__construct($title, $price, $sizeGb);
    }

    public function getRequirements(): string
    {
        return "Требования: {$this->minAndroid} или {$this->minIos}";
    }

    public function info(): string
    {
        return parent::info() . ' (Mobile)';
    }
}