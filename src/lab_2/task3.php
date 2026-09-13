<?php
declare(strict_types=1);

$defaultInput = "Привет\nИСИТ\nБГУИР\nПрограммирование\nPHP";
$rawInput = $_POST['lines'] ?? $defaultInput;
$lines = [];

foreach (explode("\n", str_replace(["\r\n", "\r"], "\n", $rawInput)) as $line) {
    $line = trim($line);
    if ($line !== '') {
        $lines[] = $line;
    }
}

function font_size_for_length(int $length): int
{
    return min(32, 14 + $length * 2);
}

function color_for_length(int $length): string
{
    return match (true) {
        $length >= 10 => 'text-sky-300',
        $length >= 6 => 'text-stone-100',
        default => 'text-stone-400',
    };
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
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

            <p class="php-h2">Форма ввода</p>
            <form method="post" class="block-bg p-4 mb-6">
                <label for="lines" class="block mb-2 text-sm font-medium text-stone-300">
                    Введите строки (по одной на строку):
                </label>
                <textarea id="lines" name="lines" rows="6" required
                    class="w-full rounded-lg border border-white/10 bg-white/5 p-3 text-stone-100 font-mono text-sm focus:border-sky-400 focus:outline-none focus:ring-1 focus:ring-sky-400"><?= htmlspecialchars($rawInput, ENT_QUOTES, 'UTF-8') ?></textarea>
                <div class="mt-3 flex gap-3">
                    <button type="submit"
                        class="rounded-lg bg-sky-500 px-5 py-2 text-sm font-semibold text-white transition-colors hover:bg-sky-400">
                        Построить список
                    </button>
                    <button type="reset"
                        class="rounded-lg border border-white/10 bg-white/5 px-5 py-2 text-sm font-medium text-stone-300 transition-colors hover:bg-white/10">
                        Очистить
                    </button>
                </div>
            </form>

            <p class="php-h2">Список с размером шрифта по длине</p>
            <div class="code">
                <?php if (empty($lines)): ?>
                    <p class="text-stone-500">Нет данных для отображения.</p>
                <?php else: ?>
                    <ul class="pl-6 list-disc space-y-2">
                        <?php foreach ($lines as $line):
                            $len = mb_strlen($line, 'UTF-8');
                            $size = font_size_for_length($len);
                            $color = color_for_length($len);
                            ?>
                            <li style="font-size: <?= $size ?>px;" class="<?= $color ?>">
                                <?= htmlspecialchars($line, ENT_QUOTES, 'UTF-8') ?>
                                <span class="text-xs text-stone-500 ml-2">(<?= $len ?> симв.)</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <footer class="mt-10 text-center text-xs text-stone-500">
                © <?= date('Y') ?> · PHP Labs
            </footer>
        </div>
    </main>
</body>

</html>