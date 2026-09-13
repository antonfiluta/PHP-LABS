<?php
declare(strict_types=1);

// Устанавливаем часовой пояс (для Беларуси — Europe/Minsk)
date_default_timezone_set('Europe/Minsk');

// date() возвращает отформатированную строку.
// d — день (01–31), m — месяц (01–12), Y — год (4 цифры),
// H — часы (00–23), i — минуты (00–59).
$currentDateTime = date('d.m.Y H:i');

// Для наглядности получим отдельные компоненты
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
    <title>Задание 1 — Дата и время</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 700px;
            margin: 40px auto;
        }

        .result {
            background: #f0f8ff;
            padding: 20px;
            border-left: 5px solid #007bff;
        }

        a {
            color: #007bff;
        }
    </style>
</head>

<body>
    <h1>Задание 1. Текущая дата и время</h1>

    <div class="result">
        <p><strong>Формат дд.мм.гггг чч:мм:</strong> <?= htmlspecialchars(
            $currentDateTime,
        ) ?></p>
        <p><strong>Отдельные компоненты:</strong></p>
        <ul>
            <li>День: <?= $day ?></li>
            <li>Месяц: <?= $month ?></li>
            <li>Год: <?= $year ?></li>
            <li>Часы: <?= $hours ?></li>
            <li>Минуты: <?= $minutes ?></li>
        </ul>
    </div>

    <p><a href="index.php">← На главную</a></p>
</body>

</html>