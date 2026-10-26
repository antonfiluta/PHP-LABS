<?php
declare(strict_types=1);

namespace App\Interfaces;

interface Shippable
{
    /**
     * @return float Стоимость доставки в рублях.
     */
    public function calculateShipping(): float;
}