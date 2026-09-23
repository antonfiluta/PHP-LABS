<?php
declare(strict_types=1);

require_once __DIR__ . '/src/infinite-cycler.php';

$colors = ['#f00' => 'Красный', '#0f0' => 'Зелёный', '#00f' => 'Синий'];

$cycler = new InfiniteCycler($colors);

$error = null;
$firstEight = [];
try {
    $firstEight = $cycler->take(100);
} catch (InvalidArgumentException $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>PHP LABS | TASK 2</title>
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

    <header class="fixed top-0 z-10 w-full py-3.5 px-6 flex flex-wrap items-center gap-3 backdrop-blur-sm bg-stone-950/20
        border-b border-white/5">
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
        <div class="w-full max-w-3xl">
            <div class="mb-12 text-center">
                <p class="m-0 mb-2.5 text-[11px] tracking-[0.3em] uppercase font-semibold text-sky-400">
                    Лабораторная работа №2
                </p>
                <h1 class="php-h1">
                    Задание 2. <br> Бесконечный итератор
                </h1>
                <p class="mt-1.5 mb-0 text-stone-400 text-sm">
                    Вариант 13 · ВЯП
                </p>
            </div>

            <p class="php-h2">Исходный массив</p>
            <div class="code">
                <pre class="text-stone-400"><?php print_r($colors); ?></pre>
            </div>

            <p class="php-h2">Первые 8 элементов бесконечного цикла</p>
            <div class="code">
                <?php if ($error !== null): ?>
                    <p class="text-red-400">
                        <?= htmlspecialchars($error) ?>
                    </p>
                <?php else: ?>
                    <ul class="pl-4 list-disc">
                        <?php foreach ($firstEight as $index => $value): ?>
                            <li>
                                <span class="text-stone-500">
                                    [<?= $index ?>]
                                </span>
                                <span class="text-sky-300">
                                    <?= $value ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="mt-4 text-sm text-stone-500">
                        InfiniteIterator бесконечно повторяет массив — вывод ограничен методом take().
                    </p>
                <?php endif; ?>
            </div>

            <footer class="mt-10 text-center text-xs text-stone-500">
                ©
                <?= date('Y') ?> · PHP Labs
            </footer>
        </div>
    </main>
</body>

</html>