<?php
declare(strict_types=1);

namespace App\Models;

/**
 * Корзина: хранит товары по их ID, считает сумму.
 */
class Cart
{
    /** @var array<string, Product> */
    private array $items = [];

    public function add(Product $product): void
    {
        $this->items[$product->getId()] = $product;
    }

    public function remove(string $productId): void
    {
        unset($this->items[$productId]);
    }

    /** @return Product[] */
    public function getItems(): array
    {
        return array_values($this->items);
    }

    public function total(): float
    {
        return array_reduce(
            $this->items,
            fn(float $sum, Product $p): float => $sum + $p->getPrice(),
            0.0,
        );
    }

    public function clear(): void
    {
        $this->items = [];
    }
}