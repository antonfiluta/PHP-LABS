<?php
declare(strict_types=1);
date_default_timezone_set("Europe/Minsk");
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Примеры из лекции</title>
    <link href="../output.css" rel="stylesheet">
</head>

<body>
    <h1 class="text-3xl">Примеры из лекции «Введение в PHP»</h1>

    <h2>1. Вывод: echo, print, &lt;?= ?&gt;</h2>
    <div class="code">
        <?php
        echo 'Hello, PHP!<br>';
        print 'Это print<br>';
        ?>
        <?= 'Сокращённый вывод через &lt;?= ?&gt;<br>' ?>
    </div>

    <h2>2. Переменные и типы данных</h2>
    <div class="code">
        <?php
        $name = 'Иван'; // string
        $age = 25; // int
        $price = 99.99; // float
        $isStudent = true; // bool
        $arr = [1, 2, 3]; // array
        $nothing = null;
        // null
        echo "Имя: $name<br>";
        echo "Возраст: $age<br>";
        echo "Цена: $price<br>";
        echo 'Студент: ' . ($isStudent ? 'да' : 'нет') . '<br>';
        echo 'Массив: ' . implode(', ', $arr) . '<br>';
        var_dump($nothing);
        ?>
    </div>

    <h2>3. Арифметические и строковые операции</h2>
    <div class="code">
        <?php
        $a = 10;
        $b = 3;
        echo 'Сумма: ' . ($a + $b) . '<br>';
        echo 'Разность: ' . ($a - $b) . '<br>';
        echo 'Произведение: ' . $a * $b . '<br>';
        echo 'Деление: ' . round($a / $b, 2) . '<br>';
        echo 'Остаток: ' . $a % $b . '<br>';
        echo 'Степень: ' . pow($a, $b) . '<br>';
        echo 'Конкатенация: ' . 'Hello, ' . $name . '!<br>';
        ?>
    </div>

    <h2>4. Условия и switch</h2>
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

    <h2>5. Циклы</h2>
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

    <h2>6. Функции, анонимные функции, замыкания</h2>
    <div class="code">
        <?php
        function add(int $a, int $b): int
        {
            return $a + $b;
        }
        echo 'add(2, 3) = ' . add(2, 3) . '<br>'; // Анонимная функция
        $multiply = function ($a, $b) {
            return $a * $b;
        };
        echo 'multiply(4, 5) = ' . $multiply(4, 5) . '<br>'; // Стрелочная функция
        $square = fn($x) => $x * $x;
        echo 'square(7) = ' . $square(7) . '<br>'; // Замыкание с use
        $factor = 10;
        $scale = function ($x) use ($factor) {
            return $x * $factor;
        };
        echo 'scale(5) = ' . $scale(5) . '<br>';
        ?>
    </div>

    <h2>7. Функции высшего порядка: array_map, array_filter, array_reduce</h2>
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

    <h2>8. Генератор (yield)</h2>
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

    <h2>9. Обработка исключений</h2>
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

    <h2>10. Операторы ?? и ??=</h2>
    <div class="code">
        <?php
        $data = [];
        $id = $data['id'] ?? 'значение по умолчанию';
        echo "id: $id<br>";
        $data['count'] ??= 100;
        echo 'count: ' . $data['count'] . '<br>';
        ?>
    </div>

    <p><a href="index.php">← На главную</a></p>
</body>

</html>