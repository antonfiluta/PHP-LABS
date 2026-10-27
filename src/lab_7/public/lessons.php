<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/init.php';
requireRole('teacher', 'admin');

$user = currentUser();
$isAdmin = $user['role'] === 'admin';
$lessonRepo = new LessonRepository();
$classRepo = new ClassRepository();

$classes = $classRepo->all();
if (!$classes) {
    exit('Нет классов в базе. Запустите seed.php.');
}

$classId = (int) ($_GET['class'] ?? ($classes[0]['id'] ?? 0));
$date = $_GET['date'] ?? date('Y-m-d');
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    $date = date('Y-m-d');
}

$classExists = false;
foreach ($classes as $c) {
    if ((int) $c['id'] === $classId) {
        $classExists = true;
        break;
    }
}
if (!$classExists) {
    $classId = (int) ($classes[0]['id'] ?? 0);
}

$errors = null;
$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? null)) {
        $errors = 'Ошибка CSRF';
    } else {
        $action = $_POST['action'] ?? '';

        try {
            if ($action === 'create') {
                $postClassId = (int) ($_POST['class_id'] ?? 0);
                $subject = trim($_POST['subject'] ?? '');
                $postDate = $_POST['date'] ?? '';
                $slot = (int) ($_POST['slot'] ?? 0);
                $homework = trim($_POST['homework'] ?? '') ?: null;

                if ($postClassId <= 0) {
                    $errors = 'Выберите класс';
                } elseif ($subject === '') {
                    $errors = 'Укажите предмет';
                } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $postDate)) {
                    $errors = 'Некорректная дата';
                } elseif (!isset(LessonRepository::SLOTS[$slot])) {
                    $errors = 'Некорректное время';
                } else {
                    if (!$isAdmin) {
                        $allowed = $lessonRepo->subjectsForTeacher((int) $user['id']);
                        if (!in_array($subject, $allowed, true)) {
                            throw new RuntimeException('Нет прав на этот предмет');
                        }
                    }
                    $lessonRepo->create($postClassId, $subject, $postDate, $slot, $homework);
                    $message = 'Урок добавлен';
                }
            }

            if ($action === 'delete') {
                $lessonRepo->delete((int) $_POST['id']);
                $message = 'Урок удалён';
            }

            if ($action === 'set_homework') {
                $lessonRepo->setHomework((int) $_POST['id'], trim($_POST['homework'] ?? ''));
                $message = 'ДЗ сохранено';
            }

            if ($action === 'clear_homework') {
                $lessonRepo->setHomework((int) $_POST['id'], '');
                $message = 'ДЗ удалено';
            }
        } catch (Throwable $e) {
            $errors = $e->getMessage();
        }
    }
}

$used = $lessonRepo->usedSlots($classId, $date);

if ($isAdmin) {
    $subjects = ['Математика', 'Физика', 'Русский язык', 'История', 'Информатика', 'Химия'];
} else {
    $subjects = $lessonRepo->subjectsForTeacher((int) $user['id']);
    if (!$subjects) {
        $subjects = [];
    }
}

$weekLessons = $lessonRepo->weekForClass($classId, $date);

$allLessonsFlat = [];
foreach ($weekLessons as $bySlot) {
    foreach ($bySlot as $l) {
        $allLessonsFlat[] = $l;
    }
}
usort($allLessonsFlat, fn($a, $b) => [$a['lesson_date'], $a['slot']] <=> [$b['lesson_date'], $b['slot']]);

$currentClassName = '';
foreach ($classes as $c) {
    if ((int) $c['id'] === $classId) {
        $currentClassName = $c['name'];
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Дневник | Уроки и ДЗ</title>
    <link href="../../output.css" rel="stylesheet">
</head>

<body class="min-h-screen antialiased">
    <div class="fixed inset-0 -z-10 overflow-hidden bg-linear-to-br from-stone-950 via-stone-950 to-stone-900">
        <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>
        <div
            class="absolute top-1/2 left-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full blur-3xl bg-sky-400/10">
        </div>
    </div>

    <header class="fixed top-4 left-4 right-4 z-20 flex flex-wrap items-center gap-3">
        <a href="index.php?class=<?= (int) $classId ?>"
            class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium backdrop-blur-md hover:bg-white/10!">
            ← Назад
        </a>
        <a href="grade_add.php"
            class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium backdrop-blur-md hover:bg-white/10!">
            Оценки
        </a>
        <?php if ($isAdmin): ?>
            <a href="admin.php"
                class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium backdrop-blur-md hover:bg-white/10!">
                Админ-панель
            </a>
        <?php endif; ?>
        <a href="logout.php"
            class="ml-auto rounded-lg border border-red-400/30 bg-red-500/10 px-4 py-2 text-sm font-medium text-red-300 hover:bg-red-500/20">
            Выход
        </a>
    </header>

    <main class="relative min-h-screen px-6 py-24">
        <div class="mx-auto w-full max-w-5xl">

            <div class="mb-8 text-center">
                <p class="php-variant">Вариант 13</p>
                <h1 class="php-h1">Уроки и ДЗ<?= $currentClassName ? ' — ' . e($currentClassName) : '' ?></h1>
                <p class="text-lg text-stone-400">Управление уроками и домашними заданиями</p>
            </div>

            <?php if ($errors !== null): ?>
                <div class="mb-4 rounded-lg border border-red-400/30 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    <?= e($errors) ?>
                </div>
            <?php endif; ?>
            <?php if ($message !== null): ?>
                <div
                    class="mb-4 rounded-lg border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                    <?= e($message) ?>
                </div>
            <?php endif; ?>

            <!-- === ВЫБОР КЛАССА И ДАТЫ === -->
            <form method="get" class="block-bg p-4 mb-6 flex flex-wrap gap-3 items-end">
                <label class="flex flex-col gap-1 text-sm text-stone-300">
                    <span>Класс</span>
                    <select name="class" onchange="this.form.submit()"
                        class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                        <?php foreach ($classes as $c): ?>
                            <option value="<?= (int) $c['id'] ?>" <?= (int) $c['id'] === $classId ? 'selected' : '' ?>>
                                <?= e($c['name']) ?> (<?= (int) $c['grade_level'] ?> кл.)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="flex flex-col gap-1 text-sm text-stone-300">
                    <span>Дата (начало недели)</span>
                    <input type="date" name="date" value="<?= e($date) ?>" onchange="this.form.submit()"
                        class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                </label>
                <div class="text-xs text-stone-500">
                    Показаны уроки с <?= e(date('d.m.Y', strtotime($date))) ?>
                    по <?= e(date('d.m.Y', strtotime("$date +6 day"))) ?>
                </div>
            </form>

            <!-- === СОЗДАНИЕ УРОКА === -->
            <p class="php-h2">Добавить урок</p>
            <?php if (empty($subjects)): ?>
                <div class="block-bg p-4 mb-6 text-sm text-amber-300">
                    У вас не назначены предметы. Обратитесь к администратору.
                </div>
            <?php else: ?>
                <form method="post" class="block-bg p-4 mb-6 grid gap-3 md:grid-cols-5">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" name="class_id" value="<?= (int) $classId ?>">
                    <input type="hidden" name="date" value="<?= e($date) ?>">

                    <label class="flex flex-col gap-1 text-sm text-stone-300 md:col-span-2">
                        <span>Предмет</span>
                        <select name="subject" required
                            class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                            <?php foreach ($subjects as $s): ?>
                                <option value="<?= e($s) ?>"><?= e($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="flex flex-col gap-1 text-sm text-stone-300">
                        <span>Время</span>
                        <select name="slot" required
                            class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                            <?php foreach (LessonRepository::SLOTS as $n => $time): ?>
                                <?php $isUsed = in_array($n, $used, true); ?>
                                <option value="<?= $n ?>" <?= $isUsed ? 'disabled' : '' ?>>
                                    <?= e($time) ?>         <?= $isUsed ? ' — занято' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="flex flex-col gap-1 text-sm text-stone-300 md:col-span-2">
                        <span>ДЗ (необязательно)</span>
                        <input name="homework" placeholder="Например: №5, стр. 12"
                            class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                    </label>

                    <div class="md:col-span-5">
                        <button type="submit"
                            class="rounded-lg bg-sky-500 px-5 py-2 font-semibold text-white hover:bg-sky-400">
                            Добавить урок
                        </button>
                    </div>
                </form>
            <?php endif; ?>

            <!-- === СПИСОК УРОКОВ === -->
            <p class="php-h2">Уроки на неделю (<?= count($allLessonsFlat) ?>)</p>
            <div class="block-bg overflow-x-auto">
                <?php if (empty($allLessonsFlat)): ?>
                    <p class="p-6 text-stone-500">На эту неделю уроков нет.</p>
                <?php else: ?>
                    <table class="w-full text-sm">
                        <thead class="bg-white/5 text-stone-300">
                            <tr>
                                <th class="px-3 py-2 text-left">Дата</th>
                                <th class="px-3 py-2 text-left">Время</th>
                                <th class="px-3 py-2 text-left">Предмет</th>
                                <th class="px-3 py-2 text-left min-w-[260px]">ДЗ</th>
                                <th class="px-3 py-2 text-center">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allLessonsFlat as $l): ?>
                                <tr class="border-t border-white/5">
                                    <td class="px-3 py-2 text-stone-400 whitespace-nowrap">
                                        <?= e($l['lesson_date']) ?>
                                    </td>
                                    <td class="px-3 py-2 text-stone-400 whitespace-nowrap">
                                        <?= e(LessonRepository::SLOTS[(int) $l['slot']] ?? '') ?>
                                    </td>
                                    <td class="px-3 py-2 text-sky-300 whitespace-nowrap">
                                        <?= e($l['subject']) ?>
                                    </td>
                                    <td class="px-3 py-2">
                                        <form method="post" class="flex gap-1">
                                            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                            <input type="hidden" name="action" value="set_homework">
                                            <input type="hidden" name="id" value="<?= (int) $l['id'] ?>">
                                            <input name="homework" value="<?= e($l['homework'] ?? '') ?>" placeholder="—"
                                                class="w-full min-w-[180px] rounded border border-white/10 bg-white/5 px-2 py-1 text-xs text-stone-100 focus:border-sky-400 focus:outline-none">
                                            <button type="submit" title="Сохранить ДЗ"
                                                class="rounded border border-sky-400/30 bg-sky-500/10 px-2 py-1 text-xs text-sky-300 hover:bg-sky-500/20">
                                                💾
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="flex flex-wrap justify-center gap-1">
                                            <?php if (!empty($l['homework'])): ?>
                                                <form method="post" class="inline" onsubmit="return confirm('Удалить ДЗ?')">
                                                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                                    <input type="hidden" name="action" value="clear_homework">
                                                    <input type="hidden" name="id" value="<?= (int) $l['id'] ?>">
                                                    <button type="submit" title="Удалить ДЗ"
                                                        class="rounded border border-amber-400/30 bg-amber-500/10 px-2 py-1 text-xs text-amber-300 hover:bg-amber-500/20 whitespace-nowrap">
                                                        ДЗ ✕
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <a href="grade_add.php?lesson=<?= (int) $l['id'] ?>"
                                                class="rounded border border-emerald-400/30 bg-emerald-500/10 px-2 py-1 text-xs text-emerald-300 hover:bg-emerald-500/20 whitespace-nowrap">
                                                оценки
                                            </a>

                                            <form method="post" class="inline"
                                                onsubmit="return confirm('Удалить урок? Все оценки по нему будут удалены.')">
                                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= (int) $l['id'] ?>">
                                                <button type="submit"
                                                    class="rounded border border-red-400/30 bg-red-500/10 px-2 py-1 text-xs text-red-300 hover:bg-red-500/20 whitespace-nowrap">
                                                    удалить
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <footer class="mt-10 text-center text-xs text-stone-500">
                © <?= date('Y') ?> · PHP Labs · ЛР7
            </footer>
        </div>
    </main>
</body>

</html>