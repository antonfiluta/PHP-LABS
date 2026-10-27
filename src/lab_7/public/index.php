<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/init.php';
requireLogin();

$user = currentUser();
$isStudent = $user['role'] === 'student';
$isTeacher = in_array($user['role'], ['teacher', 'admin'], true);
$isAdmin = $user['role'] === 'admin';

$classRepo = new ClassRepository();
$lessonRepo = new LessonRepository();
$classes = $classRepo->all();

if ($isStudent) {
    $classId = (int) ($user['class_id'] ?? 0);
} else {
    $classId = (int) ($_GET['class'] ?? ($classes[0]['id'] ?? 0));
    if (!$classes)
        exit('Нет классов. Запустите seed.php.');
}

$filterSubject = $_GET['subject'] ?? null;
$subjects = $isStudent
    ? (new GradeRepository())->subjectsForStudent((int) $user['id'])
    : $lessonRepo->subjectsForClass($classId);

$monday = $_GET['monday'] ?? date('Y-m-d', strtotime('monday this week'));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $monday)) {
    $monday = date('Y-m-d', strtotime('monday this week'));
}

$lessons = $lessonRepo->weekForClass($classId, $monday, $filterSubject);

$days = [];
for ($i = 0; $i < 6; $i++) {
    $d = date('Y-m-d', strtotime("$monday +$i day"));
    $days[$d] = $lessons[$d] ?? [];
}
$dayNames = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'];

$currentClassName = '';
foreach ($classes as $c) {
    if ((int) $c['id'] === $classId) {
        $currentClassName = $c['name'];
        break;
    }
}

function buildUrl(array $params): string
{
    $clean = array_filter($params, fn($v) => $v !== null && $v !== '');
    return '?' . http_build_query($clean);
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Дневник | Расписание</title>
    <link href="../../output.css" rel="stylesheet">
</head>

<body class="min-h-screen antialiased">
    <div class="fixed inset-0 -z-10 overflow-hidden bg-linear-to-br from-stone-950 via-stone-950 to-stone-900">
        <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>
    </div>

    <header class="fixed top-4 left-4 right-4 z-20 flex flex-wrap items-center gap-3">
        <a href="../../index.php"
            class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium backdrop-blur-md hover:bg-white/10!">PHP
            LABS</a>
        <?php if ($isStudent): ?>
            <a href="grades.php"
                class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium backdrop-blur-md hover:bg-white/10!">Оценки</a>
        <?php endif; ?>
        <?php if ($isTeacher): ?>
            <a href="lessons.php"
                class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium backdrop-blur-md hover:bg-white/10!">Уроки</a>
            <a href="grade_add.php"
                class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium backdrop-blur-md hover:bg-white/10!">Оценки</a>
        <?php endif; ?>
        <?php if ($isAdmin): ?>
            <a href="admin.php"
                class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium backdrop-blur-md hover:bg-white/10!">Админ-панель</a>
        <?php endif; ?>
        <a href="logout.php"
            class="ml-auto rounded-lg border border-red-400/30 bg-red-500/10 px-4 py-2 text-sm font-medium text-red-300 hover:bg-red-500/20">Выход</a>
    </header>

    <main class="relative min-h-screen px-6 py-24">
        <div class="mx-auto w-full max-w-7xl">
            <div class="mb-8 text-center">
                <p class="php-variant">Вариант 13</p>
                <h1 class="php-h1">Расписание<?= $currentClassName ? ' — ' . e($currentClassName) : '' ?></h1>
            </div>

            <form method="get" class="block-bg p-4 mb-6 flex flex-wrap gap-2 items-center">
                <?php if ($isTeacher): ?>
                    <span class="text-sm text-stone-400">Класс:</span>
                    <select name="class" onchange="this.form.submit()"
                        class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                        <?php foreach ($classes as $c): ?>
                            <option value="<?= (int) $c['id'] ?>" <?= (int) $c['id'] === $classId ? 'selected' : '' ?>>
                                <?= e($c['name']) ?> (<?= (int) $c['grade_level'] ?> кл.)
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <input type="hidden" name="class" value="<?= $classId ?>">
                <?php endif; ?>

                <span class="text-sm text-stone-400 ml-2">Предмет:</span>
                <select name="subject" onchange="this.form.submit()"
                    class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                    <option value="">— все —</option>
                    <?php foreach ($subjects as $s): ?>
                        <option value="<?= e($s) ?>" <?= $s === $filterSubject ? 'selected' : '' ?>><?= e($s) ?></option>
                    <?php endforeach; ?>
                </select>

                <?php if ($filterSubject): ?>
                    <a href="<?= e(buildUrl(['class' => $isTeacher ? $classId : null])) ?>"
                        class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm text-stone-300 hover:bg-white/10">Сбросить
                        фильтр</a>
                <?php endif; ?>

                <div class="ml-auto flex gap-2 text-sm">
                    <a href="<?= e(buildUrl(['class' => $isTeacher ? $classId : null, 'subject' => $filterSubject, 'monday' => date('Y-m-d', strtotime("$monday -7 days"))])) ?>"
                        class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-stone-300 hover:bg-white/10">←</a>
                    <a href="<?= e(buildUrl(['class' => $isTeacher ? $classId : null, 'subject' => $filterSubject, 'monday' => date('Y-m-d', strtotime('monday this week'))])) ?>"
                        class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-stone-300 hover:bg-white/10">Текущая</a>
                    <a href="<?= e(buildUrl(['class' => $isTeacher ? $classId : null, 'subject' => $filterSubject, 'monday' => date('Y-m-d', strtotime("$monday +7 days"))])) ?>"
                        class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-stone-300 hover:bg-white/10">→</a>
                </div>
            </form>

            <div class="grid gap-4 lg:grid-cols-2 xl:grid-cols-3">
                <?php $i = 0;
                foreach ($days as $date => $lessonsBySlot):
                    $i++; ?>
                    <section class="block-bg p-4 <?= date('N', strtotime($date)) === 6 ? 'opacity-80' : '' ?>">
                        <header class="mb-3 flex items-baseline justify-between">
                            <div>
                                <div class="text-sky-300 font-semibold"><?= e($dayNames[$i - 1]) ?></div>
                                <div class="text-xs text-stone-500"><?= e(date('d.m.Y', strtotime($date))) ?></div>
                            </div>
                            <?php if ($isTeacher): ?>
                                <a href="lessons.php?date=<?= e($date) ?>&class=<?= (int) $classId ?>"
                                    class="rounded border border-emerald-400/30 bg-emerald-500/10 px-2 py-0.5 text-xs text-emerald-300 hover:bg-emerald-500/20">+
                                    урок</a>
                            <?php endif; ?>
                        </header>

                        <ol class="space-y-1">
                            <?php foreach (LessonRepository::SLOTS as $slotNum => $time): ?>
                                <?php $lesson = $lessonsBySlot[$slotNum] ?? null; ?>
                                <li class="flex items-start gap-2 rounded-md px-2 py-1 <?= $lesson ? 'bg-white/5' : '' ?>">
                                    <div class="w-14 shrink-0 text-xs text-stone-500"><?= e($time) ?></div>
                                    <div class="flex-1 min-w-0">
                                        <?php if ($lesson): ?>
                                            <div class="text-sm text-stone-100"><?= e($lesson['subject']) ?></div>
                                            <?php if ($lesson['homework']): ?>
                                                <div class="text-xs text-stone-400 truncate" title="<?= e($lesson['homework']) ?>">
                                                    ДЗ: <?= e($lesson['homework']) ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <div class="text-sm text-stone-600">— свободно —</div>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </section>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
</body>

</html>