<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP LABS — Лабораторные работы</title>
    <link href="output.css" rel="stylesheet">
</head>

<body class="min-h-screen antialiased">

    <div class="fixed inset-0 -z-10 overflow-hidden bg-linear-to-br from-stone-950 via-stone-950 to-stone-900">
        <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl">
        </div>
        <div
            class="absolute top-1/2 left-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full blur-3xl bg-sky-400/10">
        </div>
    </div>

    <header class="fixed top-4 left-4 right-4 z-20 flex flex-wrap items-center gap-3">
        <?php
        $links = [
            ['index.php', ' PHP LABS'],
            ['', '← Prev Page'],
        ];

        foreach ($links as [$href, $title]): ?>
            <a href="<?= htmlspecialchars($href) ?>"
                class=" rounded-lg flex items-center gap-2 border px-4 py-2 text-sm font-medium backdrop-blur-md transition-all duration-200 hover:shadow-lg hover:shadow-sky-500/5 border-white/10 bg-white/5 hover:bg-white/10!">
                <?= htmlspecialchars($title) ?>
            </a>
        <?php endforeach; ?>
    </header>

    <main class="relative flex min-h-screen items-center justify-center px-6 py-28">
        <div class="w-full max-w-3xl">

            <header class="mb-12 text-center">
                <p class="mb-3 text-xs font-semibold tracking-[0.3em] text-sky-600 uppercase dark:text-sky-400">
                    Вариант 13
                </p>
                <h1
                    class="mb-4 bg-linear-to-br from-stone-900 to-stone-600 bg-clip-text text-4xl font-bold tracking-tight text-transparent sm:text-5xl dark:from-white dark:to-stone-400">
                    Лабораторные работы
                </h1>
                <p class="text-lg text-stone-600 dark:text-stone-400">
                    по предмету <span class="font-semibold text-stone-800 dark:text-stone-200">ВЯП</span>
                </p>
                <p class="mt-2 text-sm text-stone-500 dark:text-stone-500">
                    Антон Филюта Дмитриевич
                </p>
            </header>

            <nav class="grid gap-3">
                <?php
                $labs = [
                    ['lab_1/lab-base.php', '01', 'Основные конструкции PHP, функциональный стиль, исключения'],
                    ['lab_2/lab-base.php', '02', '????'],
                    ['lab_3/lab-base.php', '03', '????'],
                    ['lab_3/lab-base.php', '04', '????'],
                    ['lab_3/lab-base.php', '05', '????'],
                    ['lab_3/lab-base.php', '07', '????'],
                ];
                foreach ($labs as [$href, $num, $title]): ?>
                    <a href="<?= htmlspecialchars($href) ?>"
                        class="block-bg overflow-hidden group flex items-center gap-4 px-4 py-4 transition-all duration-150 hover:bg-white/10!">
                        <span
                            class="flex px-1 text-center rounded-md bg-linear-to-br from-white/10 to-white/5 text-3xl font-bold text-white/60">
                            <?= htmlspecialchars($num) ?>
                        </span>
                        <span class="flex-1 font-medium text-stone-100"><?= htmlspecialchars($title) ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor"
                            class="h-5 w-5 text-stone-400 transition-all duration-200 group-hover:text-white">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                <?php endforeach; ?>
            </nav>

            <footer class="mt-10 text-center text-xs text-stone-500 dark:text-stone-500">
                © <?= date('Y') ?> · PHP Labs
            </footer>
        </div>
    </main>

</body>

</html>