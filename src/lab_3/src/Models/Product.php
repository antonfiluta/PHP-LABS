<?php
declare(strict_types=1);

namespace App\Models;

use App\Interfaces\Discountable;

abstract class Product implements Discountable
{
    protected string $id = '';
    protected string $name = '';
    protected float $price = 0.0;

    public function __construct(string $name, float $price)
    {
        $this->id = uniqid('p_', true);
        $this->name = $name;
        $this->price = $price;
    }

    public function getId(): string
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getPrice(): float
    {
        return $this->price;
    }

    public function applyDiscount(float $percent): void
    {
        $percent = max(0.0, min(100.0, $percent));
        $this->price -= $this->price * ($percent / 100);
    }

    abstract public function getInfo(): string;
}