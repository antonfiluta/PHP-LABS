<?php
declare(strict_types=1);

$students = [
    [
        'name' => 'Пархоменко Г.А.',
        'grades' => ['Математика' => 5, 'Физика' => 4, 'Информатика' => 5],
    ],
    [
        'name' => 'Петров П.П.',
        'grades' => ['Математика' => 3, 'Физика' => 4, 'Информатика' => 4],
    ],
    [
        'name' => 'Суханова С.С.',
        'grades' => ['Математика' => 5, 'Физика' => 5, 'Информатика' => 5],
    ],
    [
        'name' => 'Кузнецов К.К.',
        'grades' => ['Математика' => 3, 'Физика' => 5, 'Информатика' => 5],
    ],
    [
        'name' => 'Филюта А.Д.',
        'grades' => ['Математика' => 4, 'Физика' => 4, 'Информатика' => 5],
    ],
];

function hasAllGradesAtLeastFour(array $student): bool
{
    foreach ($student['grades'] as $grade) {
        if ($grade < 4) {
            return false;
        }
    }
    return true;
}

$excellentStudents = array_filter(
    $students,
    fn($s) => hasAllGradesAtLeastFour($s),
);

function averageGrade(array $grades): float
{
    return round(array_sum($grades) / count($grades), 2);
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
                    Задание 3.<br> Студенты без оценок ниже 4
                </h1>
                <p class="text-lg text-stone-400">
                    Лабораторная работа 1
                </p>
            </div>

            <h2 class="php-h2">Все студенты</h2>
            <div class="code">
                <?php
                foreach ($students as $student): ?>
                    <p class="font-bold"><?= $student['name'] ?>:</p>
                    <ul class="pl-2 mb-3">
                        <?php foreach ($student['grades'] as $subject => $grade): ?>
                            <li><?= $subject . ': ' . $grade ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endforeach; ?>
            </div>

            <h2 class="php-h2">Студенты без оценок ниже 4</h2>
            <div class="code">
                <?php
                if (count($students) < 0) {
                    echo 'Список пуст';
                }
                ?>

                <?php
                foreach ($excellentStudents as $student): ?>
                    <p class="font-bold"><?= $student['name'] ?>:</p>
                    <ul class="pl-2 mb-3">
                        <?php foreach ($student['grades'] as $subject => $grade): ?>
                            <li><?= $subject . ': ' . $grade ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endforeach; ?>
            </div>

            <footer class="mt-10 text-center text-xs text-stone-500 dark:text-stone-500">
                © <?= date('Y') ?> · PHP Labs
            </footer>
        </div>
    </main>
</body>

</html>