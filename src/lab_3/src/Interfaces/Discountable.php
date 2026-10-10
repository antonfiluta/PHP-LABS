<?php
declare(strict_types=1);

namespace App\Interfaces;

/**
 * Контракт для товаров, к которым применима скидка.
 */
interface Discountable
{
    /**
     * @param float $percent Процент скидки (0–100).
     */
    public function applyDiscount(float $percent): void;
}