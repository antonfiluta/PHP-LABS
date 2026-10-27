<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json; charset=utf-8');

$model = new DisciplineModel();
$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true) ?? [];

try {
    switch ($action) {

        case 'get':
            $items = $model->getAll();

            $semester = isset($input['semester']) && $input['semester'] !== ''
                ? (int) $input['semester'] : null;
            $teacher = $input['teacher'] ?? null;
            $sortBy = $input['sortBy'] ?? 'name';
            $sortDir = $input['sortDir'] ?? 'asc';

            $items = $model->filter($items, $semester, $teacher);
            $items = $model->sort($items, $sortBy, $sortDir);

            echo json_encode([
                'success' => true,
                'items' => $items,
                'stats' => $model->stats($model->getAll()),
                'storage' => $model->getStorageType(),
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'add':
            $name = trim((string) ($input['name'] ?? ''));
            $teacher = trim((string) ($input['teacher'] ?? ''));
            $semester = (int) ($input['semester'] ?? 0);
            $hours = (int) ($input['hours'] ?? 0);
            $faculty = trim((string) ($input['faculty'] ?? ''));

            if ($name === '' || $teacher === '' || $faculty === '') {
                echo json_encode(['success' => false, 'error' => 'Заполните все поля'], JSON_UNESCAPED_UNICODE);
                break;
            }
            if ($semester < 1 || $semester > 12) {
                echo json_encode(['success' => false, 'error' => 'Семестр: 1–12'], JSON_UNESCAPED_UNICODE);
                break;
            }
            if ($hours < 1 || $hours > 500) {
                echo json_encode(['success' => false, 'error' => 'Часы: 1–500'], JSON_UNESCAPED_UNICODE);
                break;
            }

            $item = $model->add(compact('name', 'teacher', 'semester', 'hours', 'faculty'));
            echo json_encode(['success' => true, 'item' => $item], JSON_UNESCAPED_UNICODE);
            break;

        case 'edit':
            $id = (int) ($input['id'] ?? 0);
            $field = (string) ($input['field'] ?? '');
            $value = $input['value'] ?? '';

            if ($id <= 0) {
                echo json_encode(['success' => false, 'error' => 'Некорректный id'], JSON_UNESCAPED_UNICODE);
                break;
            }
            if ($value === '' && in_array($field, ['name', 'teacher', 'faculty'], true)) {
                echo json_encode(['success' => false, 'error' => 'Поле не может быть пустым'], JSON_UNESCAPED_UNICODE);
                break;
            }

            $ok = $model->update($id, $field, $value);
            echo json_encode(['success' => $ok], JSON_UNESCAPED_UNICODE);
            break;

        case 'delete':
            $id = (int) ($input['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['success' => false, 'error' => 'Некорректный id'], JSON_UNESCAPED_UNICODE);
                break;
            }
            echo json_encode(['success' => $model->delete($id)], JSON_UNESCAPED_UNICODE);
            break;

        case 'switch':
            $type = (string) ($input['type'] ?? 'json');
            $model->switchStorage($type);
            echo json_encode(['success' => true, 'storage' => $type], JSON_UNESCAPED_UNICODE);
            break;

        default:
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Неизвестное действие'], JSON_UNESCAPED_UNICODE);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}