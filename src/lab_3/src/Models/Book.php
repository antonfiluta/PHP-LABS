<?php
declare(strict_types=1);

namespace App\Models;

use App\Interfaces\Shippable;

class Book extends Product implements Shippable
{
    private string $author = '';
    private int $pages = 200;      // ← дефолт для старых объектов

    public function __construct(string $name, float $price, string $author, int $pages = 200)
    {
        parent::__construct($name, $price);
        $this->author = $author;
        $this->pages = $pages;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }
    public function getPages(): int
    {
        return $this->pages;
    }

    public function calculateShipping(): float
    {
        return 5.0 + $this->pages * 0.05;
    }

    public function getInfo(): string
    {
        return "Книга: {$this->name} (Автор: {$this->author}, {$this->pages} стр.) — "
            . number_format($this->price, 2, '.', ' ') . " руб.";
    }
}