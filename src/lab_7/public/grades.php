<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/init.php';
requireRole('student');

$user = currentUser();
$repo = new GradeRepository();

$filterSubject = $_GET['subject'] ?? null;
$subjects = $repo->subjectsForStudent((int) $user['id']);
$grades = $repo->forStudent((int) $user['id'], $filterSubject);

$avg = $grades
    ? round(array_sum(array_column($grades, 'grade')) / count($grades), 1)
    : '—';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Дневник | Оценки</title>
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
        <div class="mx-auto w-full max-w-5xl">
            <div class="mb-8 text-center">
                <p class="php-variant">Вариант 13</p>
                <h1 class="php-h1">Мои оценки</h1>
                <p class="text-lg text-stone-400">
                    Средний балл: <span class="text-sky-300"><?= e($avg) ?></span>
                </p>
            </div>

            <form method="get" class="block-bg p-4 mb-6 flex flex-wrap gap-2 items-center">
                <span class="text-sm text-stone-400">Предмет:</span>
                <select name="subject" onchange="this.form.submit()"
                    class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                    <option value="">— все —</option>
                    <?php foreach ($subjects as $s): ?>
                        <option value="<?= e($s) ?>" <?= $s === $filterSubject ? 'selected' : '' ?>><?= e($s) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($filterSubject): ?>
                    <a href="grades.php"
                        class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm text-stone-300 hover:bg-white/10">Сбросить</a>
                <?php endif; ?>
            </form>

            <div class="block-bg overflow-x-auto">
                <?php if (empty($grades)): ?>
                    <p class="p-6 text-stone-500">Оценок нет<?= $filterSubject ? ' по этому предмету' : '' ?>.</p>
                <?php else: ?>
                    <table class="w-full text-sm">
                        <thead class="bg-white/5 text-stone-300">
                            <tr>
                                <th class="px-4 py-3 text-left">Дата</th>
                                <th class="px-4 py-3 text-left">Время</th>
                                <th class="px-4 py-3 text-left">Предмет</th>
                                <th class="px-4 py-3 text-left">ДЗ</th>
                                <th class="px-4 py-3 text-left">Оценка</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($grades as $g): ?>
                                <tr class="border-t border-white/5">
                                    <td class="px-4 py-2 text-stone-400"><?= e($g['lesson_date']) ?></td>
                                    <td class="px-4 py-2 text-stone-400">
                                        <?= e(LessonRepository::SLOTS[(int) $g['slot']] ?? '') ?>
                                    </td>
                                    <td class="px-4 py-2"><?= e($g['subject']) ?></td>
                                    <td class="px-4 py-2 text-stone-500 truncate max-w-xs"
                                        title="<?= e($g['homework'] ?? '') ?>">
                                        <?= e($g['homework'] ?: '—') ?>
                                    </td>
                                    <td class="px-4 py-2">
                                        <span class="rounded-md px-2 py-0.5 font-semibold <?= (int) $g['grade'] >= 6
                                            ? 'bg-emerald-500/20 text-emerald-300'
                                            : 'bg-red-500/20 text-red-300' ?>"><?= (int) $g['grade'] ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>

</html>