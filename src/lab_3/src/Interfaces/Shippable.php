<?php
declare(strict_types=1);

namespace App\Interfaces;

/**
 * Контракт для товаров, у которых есть стоимость доставки.
 */
interface Shippable
{
    /**
     * @return float Стоимость доставки в рублях.
     */
    public function calculateShipping(): float;
}