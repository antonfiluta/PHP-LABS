<?php
declare(strict_types=1);
date_default_timezone_set('Europe/Minsk');

$currentDateTime = date('d.m.Y H:i');

$day = date('d');
$month = date('m');
$year = date('Y');
$hours = date('H');
$minutes = date('i');
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>PHP LABS | TASK 1</title>
    <link href="../output.css" rel="stylesheet">
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
            ['../index.php', ' PHP LABS'],
            ['./lab-base.php', '← Prev Page'],
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
            <div class="mb-12 text-center">
                <p class="php-variant">
                    Вариант 13
                </p>
                <h1 class="php-h1">
                    Задание 1. <br> Текущая дата и время
                </h1>
                <p class="text-lg text-stone-400">
                    Лабораторная работа 1
                </p>
            </div>

            <div>
                <p class="php-h2"> Формат дд.мм.гггг чч:мм: </p>
                <div class="code">
                    <?= $currentDateTime ?>
                </div>

                <p class="php-h2">Отдельные компоненты:</p>
                <ul class="code">
                    <li>День: <?= $day ?></li>
                    <li>Месяц: <?= $month ?></li>
                    <li>Год: <?= $year ?></li>
                    <li>Часы: <?= $hours ?></li>
                    <li>Минуты: <?= $minutes ?></li>
                </ul>
            </div>

            <footer class="mt-10 text-center text-xs text-stone-500 dark:text-stone-500">
                © <?= date('Y') ?> · PHP Labs
            </footer>
        </div>
    </main>
</body>

</html>