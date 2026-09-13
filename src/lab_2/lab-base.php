<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP | LAB 2</title>
    <link href="../output.css" rel="stylesheet">
</head>

<body class="min-h-screen antialiased relative">
    <div class="fixed inset-0 -z-10 overflow-hidden bg-linear-to-br from-stone-950 via-stone-950 to-stone-900">
        <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>
        <div
            class="absolute top-1/2 left-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full blur-3xl bg-sky-400/10">
        </div>
    </div>

    <header
        class="fixed top-0 z-10 w-full py-3.5 px-6 flex flex-wrap items-center gap-3 backdrop-blur-sm bg-stone-950/20 border-b border-white/5">
        <?php
        $links = [
            ['../index.php', ' PHP LABS'],
            ['../index.php', '← Prev Page'],
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
            <header class="mb-12 text-center">
                <p class="m-0 mb-2.5 text-[11px] tracking-[0.3em] uppercase font-semibold text-sky-400">
                    Лабораторная работа №2
                </p>
                <h1 class="php-h1">
                    Задание 3. <br> Список с размером шрифта
                </h1>
                <p class="mt-1.5 mb-0 text-stone-400 text-sm">
                    Вариант 13 · ВЯП
                </p>
            </header>

            <nav class="grid gap-3">
                <?php
                $tasks = [
                    ['task1.php', '01', 'Объединение массивов с уникальными значениями'],
                    ['task2.php', '02', 'Бесконечный итератор (InfiniteIterator)'],
                    ['task3.php', '03', 'Список с размером шрифта по длине'],
                ];
                foreach ($tasks as [$href, $num, $title]): ?>
                    <a href="<?= $href ?>"
                        class="block-bg overflow-hidden group flex items-center gap-4 px-4 py-4 transition-all duration-150 hover:bg-white/10!">
                        <span
                            class="flex min-w-12 justify-center px-1 text-center rounded-md bg-linear-to-br from-white/10 to-white/5 text-3xl font-bold text-white/60">
                            <?= $num ?>
                        </span>
                        <span class="flex-1 font-medium text-stone-100"><?= $title ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor"
                            class="h-5 w-5 text-stone-400 transition-all duration-200 group-hover:text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                <?php endforeach; ?>
            </nav>

            <footer class="mt-10 text-center text-xs text-stone-500">
                © <?= date('Y') ?> · PHP Labs
            </footer>
        </div>
    </main>
</body>

</html>