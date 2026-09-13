<?php
declare(strict_types=1);

/**
 * Массив студентов: имя + оценки по предметам.
 */
$students = [
    [
        'name' => 'Иванов И.И.',
        'grades' => ['Математика' => 5, 'Физика' => 4, 'Информатика' => 5],
    ],
    [
        'name' => 'Петров П.П.',
        'grades' => ['Математика' => 3, 'Физика' => 4, 'Информатика' => 4],
    ],
    [
        'name' => 'Сидорова А.А.',
        'grades' => ['Математика' => 4, 'Физика' => 4, 'Информатика' => 4],
    ],
    [
        'name' => 'Кузнецов К.К.',
        'grades' => ['Математика' => 5, 'Физика' => 5, 'Информатика' => 5],
    ],
    [
        'name' => 'Смирнова О.О.',
        'grades' => ['Математика' => 4, 'Физика' => 3, 'Информатика' => 5],
    ],
];

/**
 * Чистая функция: проверяет, все ли оценки >= 4.
 */
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
    <title>Задание 3 — Студенты</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 40px auto;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin: 15px 0;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #007bff;
            color: white;
        }

        .good {
            background: #e0ffe0;
        }

        a {
            color: #007bff;
        }
    </style>
</head>

<body>
    <h1>Задание 3. Студенты без оценок ниже 4</h1>

    <h2>Все студенты</h2>
    <table>
        <tr>
            <th>Имя</th>
            <th>Математика</th>
            <th>Физика</th>
            <th>Информатика</th>
            <th>Средний балл</th>
        </tr>
        <?php foreach ($students as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['name']) ?></td>
                <?php foreach ($s['grades'] as $grade): ?>
                    <td><?= $grade ?></td>
                <?php endforeach; ?>
                <td><?= averageGrade($s['grades']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2>Студенты без оценок ниже 4</h2>
    <?php if (empty($excellentStudents)): ?>
        <p>Нет студентов, удовлетворяющих условию.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Имя</th>
                <th>Оценки</th>
                <th>Средний балл</th>
            </tr>
            <?php foreach ($excellentStudents as $s): ?>
                <tr class="good">
                    <td><?= htmlspecialchars($s['name']) ?></td>
                    <td>
                        <?php
                        $parts = [];
                        foreach ($s['grades'] as $subject => $g) {
                            $parts[] = htmlspecialchars($subject) . ': ' . $g;
                        }
                        echo implode(', ', $parts);
                        ?>
                    </td>
                    <td><?= averageGrade($s['grades']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <p><a href="index.php">← На главную</a></p>
</body>

</html>