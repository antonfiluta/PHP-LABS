<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/init.php';
requireRole('teacher', 'admin');

$assignmentRepo = new AssignmentRepository();
$classRepo = new ClassRepository();

$errors = null;
$message = null;

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? null)) {
        $errors = 'Ошибка CSRF';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'create') {
            $classId = (int) ($_POST['class_id'] ?? 0);
            $subject = trim($_POST['subject'] ?? '');
            $title = trim($_POST['title'] ?? '');
            $dueDate = $_POST['due_date'] ?? '';
            $maxScore = (int) ($_POST['max_score'] ?? 100);

            if (!$classId)
                $errors = 'Выберите класс';
            elseif ($subject === '')
                $errors = 'Укажите предмет';
            elseif ($title === '')
                $errors = 'Укажите название';
            elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate))
                $errors = 'Некорректная дата';
            elseif ($maxScore < 1 || $maxScore > 100)
                $errors = 'Макс. балл: 1–100';
            else {
                $assignmentRepo->create($classId, $subject, $title, $dueDate, $maxScore);
                $message = 'Задание добавлено';
            }
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0) {
                $assignmentRepo->delete($id);
                $message = 'Задание удалено';
            }
        }
    }
}

// READ с фильтром
$filterClass = isset($_GET['class']) ? (int) $_GET['class'] : null;
$filterSubject = $_GET['subject'] ?? null;
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 20;

$assignments = $assignmentRepo->filtered($filterClass, $filterSubject, $page, $perPage);
$classes = $classRepo->all();
$subjects = $assignmentRepo->subjects();
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Школьный дневник | Задания</title>
    <link href="../../output.css" rel="stylesheet">
</head>

<body class="min-h-screen antialiased">
    <div class="fixed inset-0 -z-10 overflow-hidden bg-linear-to-br from-stone-950 via-stone-950 to-stone-900">
        <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>
    </div>

    <header class="fixed top-4 left-4 right-4 z-20 flex flex-wrap items-center gap-3">
        <a href="index.php"
            class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium backdrop-blur-md hover:bg-white/10!">←
            Назад</a>
        <a href="logout.php"
            class="ml-auto rounded-lg border border-red-400/30 bg-red-500/10 px-4 py-2 text-sm font-medium text-red-300 hover:bg-red-500/20">Выход</a>
    </header>

    <main class="relative min-h-screen px-6 py-24">
        <div class="mx-auto w-full max-w-6xl">
            <div class="mb-10 text-center">
                <p class="php-variant">Вариант 13</p>
                <h1 class="php-h1">Управление заданиями</h1>
            </div>

            <?php if ($errors): ?>
                <div class="mb-4 rounded-lg border border-red-400/30 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    <?= e($errors) ?></div>
            <?php endif; ?>
            <?php if ($message): ?>
                <div
                    class="mb-4 rounded-lg border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                    <?= e($message) ?></div>
            <?php endif; ?>

            <form method="post" class="block-bg p-5 mb-6 grid gap-3 md:grid-cols-5">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="create">
                <select name="class_id" required
                    class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                    <option value="">Класс</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= (int) $c['id'] ?>"><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input name="subject" required placeholder="Предмет"
                    class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                <input name="title" required placeholder="Название задания"
                    class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100 md:col-span-2">
                <input name="due_date" type="date" required
                    class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                <input name="max_score" type="number" min="1" max="100" value="100"
                    class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                <button type="submit"
                    class="md:col-span-5 rounded-lg bg-sky-500 px-4 py-2 font-semibold text-white hover:bg-sky-400">
                    Добавить задание
                </button>
            </form>

            <form method="get" class="mb-6 flex flex-wrap gap-2">
                <select name="class" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                    <option value="">Все классы</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= (int) $c['id'] ?>" <?= $filterClass === (int) $c['id'] ? 'selected' : '' ?>>
                            <?= e($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="subject" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                    <option value="">Все предметы</option>
                    <?php foreach ($subjects as $s): ?>
                        <option value="<?= e($s) ?>" <?= $filterSubject === $s ? 'selected' : '' ?>>
                            <?= e($s) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button
                    class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-sm text-stone-300 hover:bg-white/10">Фильтровать</button>
                <a href="assignments.php"
                    class="rounded-lg px-4 py-2 text-sm text-stone-500 hover:text-stone-300">Сбросить</a>
            </form>

            <div class="block-bg overflow-x-auto">
                <?php if (empty($assignments)): ?>
                    <p class="p-6 text-stone-500">Нет заданий по заданному фильтру.</p>
                <?php else: ?>
                    <table class="w-full text-sm">
                        <thead class="bg-white/5 text-stone-300">
                            <tr>
                                <th class="px-4 py-3 text-left">ID</th>
                                <th class="px-4 py-3 text-left">Класс</th>
                                <th class="px-4 py-3 text-left">Предмет</th>
                                <th class="px-4 py-3 text-left">Задание</th>
                                <th class="px-4 py-3 text-left">Срок</th>
                                <th class="px-4 py-3 text-left">Макс.</th>
                                <th class="px-4 py-3 text-center">Действие</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assignments as $a): ?>
                                <tr class="border-t border-white/5">
                                    <td class="px-4 py-2 text-stone-500"><?= (int) $a['id'] ?></td>
                                    <td class="px-4 py-2"><?= e($a['class_name']) ?></td>
                                    <td class="px-4 py-2 text-sky-300"><?= e($a['subject']) ?></td>
                                    <td class="px-4 py-2"><?= e($a['title']) ?></td>
                                    <td class="px-4 py-2 text-stone-400"><?= e($a['due_date']) ?></td>
                                    <td class="px-4 py-2 text-stone-400"><?= (int) $a['max_score'] ?></td>
                                    <td class="px-4 py-2 text-center">
                                        <form method="post" onsubmit="return confirm('Удалить задание?')">
                                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                                            <button
                                                class="rounded border border-red-400/30 bg-red-500/10 px-3 py-1 text-xs text-red-300 hover:bg-red-500/20">
                                                Удалить
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <div class="mt-4 flex justify-between text-sm">
                <?php if ($page > 1): ?>
                    <a href="?<?= http_build_query(['class' => $filterClass, 'subject' => $filterSubject, 'page' => $page - 1]) ?>"
                        class="rounded border border-white/10 bg-white/5 px-4 py-2 text-stone-300 hover:bg-white/10">←
                        Назад</a>
                <?php else: ?><span></span><?php endif; ?>

                <span class="text-stone-500">Страница <?= $page ?></span>

                <?php if (count($assignments) === $perPage): ?>
                    <a href="?<?= http_build_query(['class' => $filterClass, 'subject' => $filterSubject, 'page' => $page + 1]) ?>"
                        class="rounded border border-white/10 bg-white/5 px-4 py-2 text-stone-300 hover:bg-white/10">Вперёд
                        →</a>
                <?php else: ?><span></span><?php endif; ?>
            </div>
        </div>
    </main>
</body>

</html>