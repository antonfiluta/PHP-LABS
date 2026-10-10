<?php
declare(strict_types=1);

require_once __DIR__ . '/src/Interfaces/Discountable.php';
require_once __DIR__ . '/src/Interfaces/Shippable.php';
require_once __DIR__ . '/src/Traits/Loggable.php';
require_once __DIR__ . '/src/Models/Product.php';
require_once __DIR__ . '/src/Models/Book.php';
require_once __DIR__ . '/src/Models/Electronic.php';
require_once __DIR__ . '/src/Models/Cart.php';

use App\Models\Book;
use App\Models\Electronic;
use App\Models\Cart;

session_start();

if (!isset($_SESSION['products'])) {
    $_SESSION['products'] = [
        new Book('PHP для начинающих', 80, 'Иван Иванов', 320),
        new Book('Паттерны проектирования', 60, 'Гамма и др.', 180),
        new Electronic('Смартфон Honor 200', 1200, 'HonorBrand', 24, 0.3),
        new Electronic('Наушники ProMax', 120, 'SoundMax', 12, 0.15),
    ];
}
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = new Cart();
}

$action = $_POST['action'] ?? null;
$pid = $_POST['product_id'] ?? null;

if ($action && $pid) {
    $product = null;
    foreach ($_SESSION['products'] as $p) {
        if ($p->getId() === $pid) {
            $product = $p;
            break;
        }
    }
    if ($product) {
        switch ($action) {
            case 'add_to_cart':
                $_SESSION['cart']->add($product);
                $_SESSION['message'] = "«{$product->getName()}» добавлен в корзину.";
                break;
            case 'remove_from_cart':
                $_SESSION['cart']->remove($product->getId());
                $_SESSION['message'] = "«{$product->getName()}» убран из корзины.";
                break;
            case 'discount_10':
                $product->applyDiscount(10);
                $_SESSION['message'] = "Скидка 10% применена к «{$product->getName()}».";
                break;
            case 'discount_20':
                $product->applyDiscount(20);
                $_SESSION['message'] = "Скидка 20% применена к «{$product->getName()}».";
                break;
        }
    }
}
if (($action ?? null) === 'checkout') {
    $total = $_SESSION['cart']->total();
    $_SESSION['message'] = "Заказ оформлен. Итог: " . number_format($total, 2, '.', ' ') . " руб.";
    $_SESSION['cart']->clear();
}
if (($action ?? null) === 'clear_cart') {
    $_SESSION['cart']->clear();
    $_SESSION['message'] = "Корзина очищена.";
}
if (($action ?? null) === 'clear_session') {
    session_destroy();
    header('Location: task1.php');
    exit;
}

$category = $_GET['category'] ?? 'all';
$filteredProducts = array_filter(
    $_SESSION['products'],
    fn(object $p): bool => match ($category) {
        'book' => $p instanceof Book,
        'electronic' => $p instanceof Electronic,
        default => true,
    },
);

$shippingTotal = array_reduce(
    $filteredProducts,
    fn(float $sum, $p): float => $sum + $p->calculateShipping(),
    0.0,
);

$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP LABS | TASK 1</title>
    <link href="../output.css" rel="stylesheet">
</head>

<body class="min-h-screen antialiased">
    <div class="fixed inset-0 -z-10 overflow-hidden bg-linear-to-br from-stone-950 via-stone-950 to-stone-900">
        <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>
        <div
            class="absolute top-1/2 left-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full blur-3xl bg-sky-400/10">
        </div>
    </div>

    <header class="fixed top-4 left-4 right-4 z-20 flex flex-wrap items-center gap-3">
        <?php
        $links = [
            ['../index.php', ' PHP LABS'],
            ['./lab-base.php', '← Prev Page'],
        ];
        foreach ($links as [$href, $title]): ?>
            <a href="<?= htmlspecialchars($href) ?>"
                class="rounded-lg flex items-center gap-2 border px-4 py-2 text-sm font-medium backdrop-blur-md transition-all duration-200 hover:shadow-lg hover:shadow-sky-500/5 border-white/10 bg-white/5 hover:bg-white/10!">
                <?= htmlspecialchars($title) ?>
            </a>
        <?php endforeach; ?>
    </header>

    <main class="relative flex min-h-screen items-center justify-center px-6 py-28">
        <div class="w-full max-w-6xl">
            <div class="mb-12 text-center">
                <p class="php-variant">Вариант 13</p>
                <h1 class="php-h1">
                    Задание 1. <br> Shippable + Фильтр
                </h1>
                <p class="text-lg text-stone-400">Лабораторная работа 3</p>
            </div>

            <?php if ($message !== null): ?>
                <div class="mb-6 rounded-lg border border-sky-400/30 bg-sky-500/10 px-4 py-3 text-sm text-sky-200">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <div class="grid gap-6 lg:grid-cols-[2fr_1fr]">

                <section>
                    <p class="php-h2">Фильтр по категории</p>
                    <div class="flex flex-wrap gap-2 mb-6">
                        <?php
                        $filters = [
                            'all' => 'Все товары',
                            'book' => 'Книги',
                            'electronic' => 'Электроника',
                        ];
                        foreach ($filters as $key => $label):
                            $active = $category === $key;
                            $classes = $active
                                ? 'bg-sky-500 text-white border-sky-500'
                                : 'bg-white/5 text-stone-300 border-white/10 hover:bg-white/10';
                            ?>
                            <a href="?category=<?= urlencode($key) ?>"
                                class="rounded-lg border px-4 py-2 text-sm font-medium transition-colors <?= $classes ?>">
                                <?= htmlspecialchars($label) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <p class="php-h2">Каталог (<?= count($filteredProducts) ?> товаров)</p>
                    <div class="code space-y-3">
                        <?php if (empty($filteredProducts)): ?>
                            <p class="text-stone-500">Нет товаров в этой категории.</p>
                        <?php else: ?>
                            <?php foreach ($filteredProducts as $product): ?>
                                <div class="border border-white/10 rounded-lg p-3">
                                    <div class="font-medium text-stone-100">
                                        <?= htmlspecialchars($product->getInfo()) ?>
                                    </div>
                                    <div class="text-sm text-stone-400 mt-1">
                                        Категория:
                                        <span class="text-sky-300">
                                            <?= $product instanceof Book ? 'Книга' : 'Электроника' ?>
                                        </span>
                                        · Доставка:
                                        <span class="text-sky-300">
                                            <?= number_format($product->calculateShipping(), 2, '.', ' ') ?> руб.
                                        </span>
                                    </div>
                                    <form method="post" class="mt-3 flex flex-wrap gap-2">
                                        <input type="hidden" name="product_id"
                                            value="<?= htmlspecialchars($product->getId()) ?>">
                                        <button name="action" value="add_to_cart"
                                            class="rounded-lg bg-sky-500 px-4 py-1.5 text-xs font-semibold text-white hover:bg-sky-400">
                                            В корзину
                                        </button>
                                        <button name="action" value="discount_10"
                                            class="rounded-lg border border-white/10 bg-white/5 px-4 py-1.5 text-xs text-stone-300 hover:bg-white/10">
                                            Скидка 10%
                                        </button>
                                        <button name="action" value="discount_20"
                                            class="rounded-lg border border-white/10 bg-white/5 px-4 py-1.5 text-xs text-stone-300 hover:bg-white/10">
                                            Скидка 20%
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                            <p class="mt-4 text-sm text-stone-400">
                                Суммарная доставка по фильтру:
                                <strong class="text-sky-300">
                                    <?= number_format($shippingTotal, 2, '.', ' ') ?> руб.
                                </strong>
                            </p>
                        <?php endif; ?>
                    </div>
                </section>

                <aside>
                    <p class="php-h2">Корзина</p>
                    <div class="code">
                        <?php $cartItems = $_SESSION['cart']->getItems(); ?>
                        <?php if (empty($cartItems)): ?>
                            <p class="text-stone-500">Корзина пуста.</p>
                        <?php else: ?>
                            <ul class="space-y-2 list-none p-0 mb-4">
                                <?php foreach ($cartItems as $item): ?>
                                    <li
                                        class="flex justify-between items-center gap-2 border border-white/10 rounded-lg p-2 text-sm">
                                        <span class="text-stone-200 flex-1"><?= htmlspecialchars($item->getName()) ?></span>
                                        <span class="text-sky-300"><?= number_format($item->getPrice(), 2, '.', ' ') ?>
                                            руб.</span>
                                        <form method="post">
                                            <input type="hidden" name="product_id"
                                                value="<?= htmlspecialchars($item->getId()) ?>">
                                            <button name="action" value="remove_from_cart"
                                                class="rounded-md border border-red-400/30 bg-red-500/10 px-2 py-1 text-xs text-red-300 hover:bg-red-500/20">
                                                Убрать
                                            </button>
                                        </form>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <p class="text-sm text-stone-300 mb-3">
                                Итого:
                                <strong class="text-sky-300">
                                    <?= number_format($_SESSION['cart']->total(), 2, '.', ' ') ?> руб.
                                </strong>
                            </p>
                        <?php endif; ?>

                        <form method="post" class="mt-2">
                            <button name="action" value="checkout"
                                class="w-full rounded-lg bg-sky-500 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-400">
                                Оформить заказ
                            </button>
                        </form>
                    </div>

                    <div class="mt-4 grid gap-2">
                        <form method="post">
                            <button name="action" value="clear_cart"
                                class="w-full rounded-lg border border-amber-500/30 bg-amber-500/10 px-4 py-2 text-sm text-amber-300 hover:bg-amber-500/20">
                                Очистить корзину
                            </button>
                        </form>
                        <form method="post">
                            <button name="action" value="clear_session"
                                class="w-full rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-2 text-sm text-red-300 hover:bg-red-500/20">
                                Сбросить сессию
                            </button>
                        </form>
                    </div>
                </aside>
            </div>

            <footer class="mt-10 text-center text-xs text-stone-500">
                © <?= date('Y') ?> · PHP Labs
            </footer>
        </div>
    </main>
</body>

</html>