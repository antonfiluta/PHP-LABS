<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/init.php';
requireRole('teacher', 'admin');

$user = currentUser();
$isAdmin = $user['role'] === 'admin';

$lessonRepo = new LessonRepository();
$classRepo = new ClassRepository();
$gradeRepo = new GradeRepository();

$errors = null;
$message = null;

// ----- Обработка POST -----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? null)) {
        $errors = 'Ошибка CSRF';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'set') {
            $lessonId = (int) $_POST['lesson_id'];
            $userId = (int) $_POST['user_id'];
            $grade = (int) $_POST['grade'];

            // Проверка: учитель может ставить оценку только по своим предметам
            $lesson = $lessonRepo->find($lessonId);
            $can = false;
            if ($lesson && $isAdmin) {
                $can = true;
            } elseif ($lesson) {
                $subjects = $lessonRepo->subjectsForTeacher($user['id']);
                $can = in_array($lesson['subject'], $subjects, true);
            }

            if (!$can) {
                $errors = 'Нет прав на выставление оценки по этому предмету';
            } else {
                try {
                    $gradeRepo->upsert($lessonId, $userId, $grade);
                    $message = 'Оценка выставлена';
                } catch (Throwable $e) {
                    $errors = $e->getMessage();
                }
            }
        }

        if ($action === 'delete') {
            $gradeRepo->delete((int) $_POST['id']);
            $message = 'Оценка удалена';
        }
    }
}

// ----- Подготовка данных для формы -----
$classId = (int) ($_GET['class'] ?? 0);
$lessonId = (int) ($_GET['lesson'] ?? 0);

$classes = $classRepo->all();
$lessons = [];
$students = [];
$currentLesson = null;

if ($classId) {
    $monday = date('Y-m-d', strtotime('monday this week'));
    $lessons = $lessonRepo->weekForClass($classId, $monday);
}
if ($lessonId) {
    $currentLesson = $lessonRepo->find($lessonId);
    if ($currentLesson) {
        $stmt = db()->prepare('SELECT id, name FROM users WHERE role = "student" AND class_id = ? ORDER BY name');
        $stmt->execute([$currentLesson['class_id']]);
        $students = $stmt->fetchAll();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Дневник | Выставить оценку</title>
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
        <div class="mx-auto w-full max-w-3xl">
            <div class="mb-10 text-center">
                <p class="php-variant">Вариант 13</p>
                <h1 class="php-h1">Выставить оценку</h1>
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

            <!-- Шаг 1. Выбрать класс -->
            <form method="get" class="block-bg p-4 mb-4 flex flex-wrap gap-2 items-center">
                <select name="class" required onchange="this.form.submit()"
                    class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                    <option value="">— класс —</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= (int) $c['id'] ?>" <?= (int) $c['id'] === $classId ? 'selected' : '' ?>>
                            <?= e($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="text-sm text-stone-500">← выберите класс</span>
            </form>

            <!-- Шаг 2. Выбрать урок -->
            <?php if ($classId && $lessons): ?>
                <form method="get" class="block-bg p-4 mb-4 flex flex-wrap gap-2 items-center">
                    <input type="hidden" name="class" value="<?= (int) $classId ?>">
                    <select name="lesson" required onchange="this.form.submit()"
                        class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                        <option value="">— урок —</option>
                        <?php foreach ($lessons as $date => $bySlot): ?>
                            <?php foreach ($bySlot as $slot => $l): ?>
                                <option value="<?= (int) $l['id'] ?>" <?= (int) $l['id'] === $lessonId ? 'selected' : '' ?>>
                                    <?= e($l['lesson_date']) ?>             <?= e(LessonRepository::SLOTS[$slot]) ?> — <?= e($l['subject']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </select>
                </form>
            <?php endif; ?>

            <!-- Шаг 3. Выставить оценку ученику -->
            <?php if ($currentLesson && $students): ?>
                <div class="block-bg p-4">
                    <div class="mb-3 text-sm text-stone-400">
                        <span class="text-sky-300"><?= e($currentLesson['subject']) ?></span>
                        · <?= e($currentLesson['lesson_date']) ?>
                        · <?= e(LessonRepository::SLOTS[(int) $currentLesson['slot']]) ?>
                    </div>

                    <?php $existing = $gradeRepo->forLesson((int) $currentLesson['id']); ?>
                    <?php if ($existing): ?>
                        <ul class="mb-4 space-y-1">
                            <?php foreach ($existing as $g): ?>
                                <li class="flex items-center justify-between rounded border border-white/5 px-2 py-1 text-sm">
                                    <span><?= e($g['student_name']) ?></span>
                                    <span class="text-sky-300 font-semibold"><?= (int) $g['grade'] ?></span>
                                    <form method="post" class="inline">
                                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $g['id'] ?>">
                                        <button class="text-xs text-red-300 hover:underline">удалить</button>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <form method="post" class="grid gap-3 md:grid-cols-3 items-end">
                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="set">
                        <input type="hidden" name="lesson_id" value="<?= (int) $currentLesson['id'] ?>">

                        <label class="block">
                            <span class="block mb-1 text-sm text-stone-300">Ученик</span>
                            <select name="user_id" required
                                class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                                <option value="">— выберите —</option>
                                <?php foreach ($students as $s): ?>
                                    <option value="<?= (int) $s['id'] ?>"><?= e($s['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <label class="block">
                            <span class="block mb-1 text-sm text-stone-300">Оценка</span>
                            <select name="grade" required
                                class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </label>

                        <button class="rounded-lg bg-sky-500 px-4 py-2 font-semibold text-white hover:bg-sky-400">
                            Выставить
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>