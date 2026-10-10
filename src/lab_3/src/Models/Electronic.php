<?php
declare(strict_types=1);

namespace App\Models;

use App\Interfaces\Shippable;
use App\Traits\Loggable;

class Electronic extends Product implements Shippable
{
    use Loggable;

    private string $brand = '';
    private int $warrantyMonths = 12;
    private float $weightKg = 1.0;

    public function __construct(
        string $name,
        float $price,
        string $brand,
        int $warrantyMonths = 12,
        float $weightKg = 1.0,
    ) {
        parent::__construct($name, $price);
        $this->brand = $brand;
        $this->warrantyMonths = $warrantyMonths;
        $this->weightKg = $weightKg;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }
    public function getWarranty(): int
    {
        return $this->warrantyMonths;
    }
    public function getWeight(): float
    {
        return $this->weightKg;
    }

    public function calculateShipping(): float
    {
        return 15.0 + $this->price * 0.01 + $this->weightKg * 10.0;
    }

    public function applyDiscount(float $percent): void
    {
        parent::applyDiscount($percent);
        $this->log("Скидка {$percent}% применена к {$this->name}");
    }

    public function getInfo(): string
    {
        return "Электроника: {$this->name} (Бренд: {$this->brand}, гарантия "
            . "{$this->warrantyMonths} мес., {$this->weightKg} кг) — "
            . number_format($this->price, 2, '.', ' ') . " руб.";
    }
}