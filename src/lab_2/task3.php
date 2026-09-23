<?php
declare(strict_types=1);

/**
 * Строки для отображения. Размер шрифта будет пропорционален длине.
 *
 * @var array<int, string> $lines
 */
$lines = [
    'PHP',
    'Массивы',
    'Коллекции',
    'Итераторы',
    'Программирование',
    'Информационные системы',
    'БГУИР',
];

/**
 * Размер шрифта в пикселях в зависимости от длины строки.
 * Формула: 14 + длина * 2, ограничение сверху — 32px.
 *
 * @param int $length Длина строки в символах.
 * @return int Размер шрифта в пикселях.
 */
function font_size_for_length(int $length): int
{
    return min(32, 14 + $length * 2);
}

/**
 * CSS-класс цвета текста в зависимости от длины строки.
 *
 * @param int $length Длина строки в символах.
 * @return string Имя CSS-класса Tailwind.
 */
function color_for_length(int $length): string
{
    return match (true) {
        $length >= 15 => 'text-sky-300',
        $length >= 8 => 'text-stone-100',
        default => 'text-stone-400',
    };
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP LABS | TASK 3</title>
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

    <header
        class="fixed top-0 z-10 w-full py-3.5 px-6 flex flex-wrap items-center gap-3 backdrop-blur-sm bg-stone-950/20 border-b border-white/5">
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
                    Задание 3. <br> Список с размером шрифта
                </h1>
                <p class="mt-1.5 mb-0 text-stone-400 text-sm">
                    Вариант 13 · ВЯП
                </p>
            </div>

            <p class="php-h2">Исходные строки</p>
            <div class="code">
                <ul class="pl-4 list-disc">
                    <?php foreach ($lines as $line): ?>
                        <li class="text-stone-400"><?= $line ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <p class="php-h2">Размер шрифта пропорционален длине</p>
            <div class="code">
                <ul class="pl-6 list-disc space-y-2">
                    <?php foreach ($lines as $line):
                        $len = mb_strlen($line, 'UTF-8');
                        $size = font_size_for_length($len);
                        $color = color_for_length($len);
                        ?>
                        <li style="font-size: <?= $size ?>px;" class="<?= $color ?>">
                            <?= $line ?>
                            <span class="text-xs text-stone-500 ml-2">(<?= $len ?> симв.)</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <footer class="mt-10 text-center text-xs text-stone-500">
                © <?= date('Y') ?> · PHP Labs
            </footer>
        </div>
    </main>
</body>

</html>