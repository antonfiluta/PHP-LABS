<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$model = new DisciplineModel();
$items = $model->getAll();

// --- Формируем HTML для PDF ---
$html = '<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<style>
    body   { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
    h1     { color: #0284c7; text-align: center; margin-bottom: 8px; }
    .meta  { text-align: center; color: #555; font-size: 10px; margin-bottom: 20px; }
    table  { width: 100%; border-collapse: collapse; }
    th     { background: #0284c7; color: #fff; padding: 8px; text-align: left; }
    td     { border: 1px solid #ccc; padding: 6px; }
    tr:nth-child(even) td { background: #f5f5f5; }
    .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #888; }
</style>
</head>
<body>
<h1>Список учебных дисциплин</h1>
<div class="meta">Дата формирования: ' . date('d.m.Y H:i') . ' · Всего записей: ' . count($items) . '</div>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Преподаватель</th>
            <th>Семестр</th>
            <th>Часы</th>
            <th>Факультет</th>
        </tr>
    </thead>
    <tbody>';

foreach ($items as $item) {
    $html .= '<tr>'
        . '<td>' . htmlspecialchars((string) $item['id']) . '</td>'
        . '<td>' . htmlspecialchars((string) $item['name']) . '</td>'
        . '<td>' . htmlspecialchars((string) $item['teacher']) . '</td>'
        . '<td>' . htmlspecialchars((string) $item['semester']) . '</td>'
        . '<td>' . htmlspecialchars((string) $item['hours']) . '</td>'
        . '<td>' . htmlspecialchars((string) $item['faculty']) . '</td>'
        . '</tr>';
}

$html .= '</tbody></table>
<div class="footer">Сформировано автоматически в PHP Labs · ВЯП ЛР 5-6, вариант 13</div>
</body>
</html>';

// --- Рендер PDF ---
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Отдаём файл в браузер
$dompdf->stream('disciplines.pdf', ['Attachment' => true]);