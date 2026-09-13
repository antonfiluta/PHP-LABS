<?php
declare(strict_types=1);
date_default_timezone_set("Europe/Minsk");
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>PHP LABS | Examples</title>
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
                    Примеры из методички <br> «Введение в PHP»
                </h1>
                <p class="text-lg text-stone-400">
                    Лабораторная работа 1
                </p>
            </div>

            <h2 class="php-h2">1. Вывод: echo, print, &lt;?= ?&gt;</h2>
            <div class="code">
                <?php
                echo 'Hello, PHP!<br>';
                print 'Это print<br>';
                ?>
                <?= 'Сокращённый вывод через &lt;?= ?&gt;<br>'; ?>
            </div>

            <h2 class="php-h2">2. Переменные и типы данных</h2>
            <div class="code">
                <?php
                $name = 'Иван';
                $age = 25;
                $price = 99.99;
                $isStudent = true;
                $arr = [1, 2, 3];
                $nothing = null;

                echo "Имя: $name<br>";
                echo "Возраст: $age<br>";
                echo "Цена: $price<br>";
                echo 'Студент: ', ($isStudent ? 'да' : 'нет'), '<br>';
                echo 'Массив: ', implode(', ', $arr), '<br>';
                var_dump($nothing);
                ?>
            </div>

            <h2 class="php-h2">3. Арифметические и строковые операции</h2>
            <div class="code">
                <?php
                $a = 10;
                $b = 3;
                echo 'Сумма: ' . ($a + $b) . '<br>';
                echo 'Разность: ' . ($a - $b) . '<br>';
                echo 'Произведение: ' . $a * $b . '<br>';
                echo 'Деление: ' . round($a / $b, 4) . '<br>';
                echo 'Остаток: ' . $a % $b . '<br>';
                echo 'Степень: ' . pow($a, $b) . '<br>';
                echo 'Конкатенация: ' . 'Hello, ' . $name . '!<br>';
                ?>
            </div>

            <h2 class="php-h2">4. Условия и switch</h2>
            <div class="code">
                <?php
                $score = 85;
                if ($score >= 90) {
                    echo 'Отлично<br>';
                } elseif ($score >= 75) {
                    echo 'Хорошо<br>';
                } else {
                    echo 'Удовлетворительно<br>';
                }
                $op = '+';
                switch ($op) {
                    case '+':
                        echo '10 + 3 = ' . (10 + 3) . '<br>';
                        break;
                    case '-':
                        echo '10 - 3 = ' . (10 - 3) . '<br>';
                        break;
                    default:
                        echo 'Неизвестная операция<br>';
                }
                ?>
            </div>

            <h2 class="php-h2">5. Циклы</h2>
            <div class="code">
                <?php
                echo 'for: ';
                for ($i = 1; $i <= 5; $i++) {
                    echo $i . ' ';
                }
                echo '<br>';
                echo 'while: ';
                $i = 1;
                while ($i <= 5) {
                    echo $i . ' ';
                    $i++;
                }
                echo '<br>';
                echo 'foreach: ';
                foreach (['a', 'b', 'c'] as $item) {
                    echo $item . ' ';
                }
                echo '<br>';
                ?>
            </div>

            <h2 class="php-h2">6. Функции, анонимные функции, замыкания</h2>
            <div class="code">
                <?php
                function add(int $a, int $b): int
                {
                    return $a + $b;
                }
                echo 'add(2, 3) = ' . add(2, 3) . '<br>';

                $multiply = function (int $a, int $b) {
                    return $a * $b;
                };
                echo 'multiply(4, 5) = ' . $multiply(4, 5) . '<br>';

                $square = fn(int $x) => $x * $x;
                echo 'square(7) = ' . $square(7) . '<br>';

                $factor = 10;
                $scale = function (int $x) use ($factor) {
                    return $x * $factor;
                };
                echo 'scale(5) = ' . $scale(5) . '<br>';
                ?>
            </div>

            <h2 class="php-h2">7. Функции высшего порядка: array_map, array_filter, array_reduce</h2>
            <div class="code">
                <?php
                $nums = [1, 2, 3, 4, 5, 6];

                $doubled = array_map(fn($n) => $n * 2, $nums);
                echo 'array_map (*2): ' . implode(', ', $doubled) . '<br>';

                $evens = array_filter($nums, fn($n) => $n % 2 === 0);
                echo 'array_filter (чётные): ' . implode(', ', $evens) . '<br>';

                $sum = array_reduce($nums, fn($carry, $n) => $carry + $n, 0);
                echo "array_reduce (сумма): $sum<br>";
                ?>
            </div>

            <h2 class="php-h2">8. Генератор (yield)</h2>
            <div class="code">
                <?php
                function genRange(int $start, int $end): Generator
                {
                    for ($i = $start; $i <= $end; $i++) {
                        yield $i;
                    }
                }
                echo 'Генератор от 1 до 5: ';
                foreach (genRange(1, 5) as $num) {
                    echo $num . ' ';
                }
                echo '<br>';
                ?>
            </div>

            <h2 class="php-h2">9. Обработка исключений</h2>
            <div class="code">
                <?php
                function divide(float $a, float $b): float
                {
                    if ($b == 0) {
                        throw new InvalidArgumentException('Деление на ноль!');
                    }
                    return $a / $b;
                }

                try {
                    echo '10 / 2 = ' . divide(10, 2) . '<br>';
                    echo '5 / 0 = ' . divide(5, 0) . '<br>';
                } catch (InvalidArgumentException $e) {
                    echo 'Поймано исключение: ' . $e->getMessage() . '<br>';
                } finally {
                    echo 'Блок finally выполнен.<br>';
                }
                ?>
            </div>

            <h2 class="php-h2">10. Операторы ?? и ??=</h2>
            <div class="code">
                <?php
                $data = [];

                $id = $data['id'] ?? 'значение по умолчанию';
                echo "id: $id<br>";

                $data['count'] ??= 100;
                echo 'count: ' . $data['count'] . '<br>';
                ?>
            </div>

            <footer class="mt-10 text-center text-xs text-stone-500 dark:text-stone-500">
                © <?= date('Y') ?> · PHP Labs
            </footer>
        </div>
    </main>
</body>

</html>