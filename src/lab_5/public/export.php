<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

$model = new DisciplineModel();
$items = $model->getAll();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=disciplines.csv');

$out = fopen('php://output', 'w');
// BOM для корректного отображения в Excel
fwrite($out, "\xEF\xBB\xBF");

fputcsv($out, ['ID', 'Название', 'Преподаватель', 'Семестр', 'Часы', 'Факультет'], ';');
foreach ($items as $item) {
    fputcsv($out, [
        $item['id'],
        $item['name'],
        $item['teacher'],
        $item['semester'],
        $item['hours'],
        $item['faculty'],
    ], ';');
}
fclose($out);