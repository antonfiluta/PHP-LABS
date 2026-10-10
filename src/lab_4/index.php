<?php
declare(strict_types=1);

require_once __DIR__ . '/src/session.php';
require_once __DIR__ . '/src/courses.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$courses = get_courses();
$progress = $_SESSION['progress'] ?? [];
$completed = array_keys(array_filter($progress, fn(array $p): bool => $p['done'] ?? false));

$totalModules = array_sum(array_map(fn(array $c): int => count($c['modules']), $courses));
$doneCount = count($completed);
$percent = $totalModules > 0 ? round($doneCount / $totalModules * 100) : 0;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" data-theme="<?= htmlspecialchars($theme) ?>"
    data-lang="<?= htmlspecialchars($lang) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP LABS | Образовательная платформа</title>
    <link href="../output.css" rel="stylesheet">
</head>

<body class="min-h-screen antialiased">
    <div class="fixed inset-0 -z-10 overflow-hidden bg-linear-to-br from-stone-950 via-stone-950 to-stone-900">
        <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>
        <div
            class="absolute top-1/2 left-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full blur-3xl bg-sky-400/10">
        </div>
    </div>

    <!-- Toast-уведомление -->
    <div id="toast"
        class="fixed top-20 right-6 z-50 hidden rounded-lg border border-sky-400/30 bg-sky-500/10 px-4 py-3 text-sm text-sky-200 backdrop-blur transition-opacity duration-300">
    </div>

    <header class="fixed top-4 left-4 right-4 z-20 flex flex-wrap items-center gap-3">
        <a href="../index.php"
            class="rounded-lg flex items-center gap-2 border px-4 py-2 text-sm font-medium backdrop-blur-md border-white/10 bg-white/5 hover:bg-white/10!">
            PHP LABS
        </a>
        <a href="logout.php"
            class="rounded-lg flex items-center gap-2 border px-4 py-2 text-sm font-medium backdrop-blur-md border-red-400/20 bg-red-500/5 text-red-200 hover:bg-red-500/15">
            Выход
        </a>
    </header>

    <main class="relative min-h-screen px-6 py-28">
        <div class="mx-auto w-full max-w-5xl">

            <div class="mb-10 text-center">
                <p class="php-variant">Вариант 13</p>
                <h1 class="php-h1">
                    <span data-i18n="greeting">Привет</span>, <?= htmlspecialchars($_SESSION['name']) ?>!
                </h1>
                <p class="text-lg text-stone-400">
                    <span data-i18n="group">Группа</span>: <?= htmlspecialchars($_SESSION['group']) ?>
                    · <span data-i18n="completed">Завершено</span>:
                    <span class="text-sky-300">
                        <span id="progressCount"><?= $doneCount ?></span> /
                        <span id="progressTotal"><?= $totalModules ?></span>
                    </span>
                    (<span id="progressPercent"><?= $percent ?></span>%)
                </p>
            </div>

            <div class="mb-8 h-2 w-full overflow-hidden rounded-full bg-white/5">
                <div id="progressBar" class="h-full bg-sky-500 transition-all duration-300"
                    style="width: <?= $percent ?>%"></div>
            </div>

            <p class="php-h2" data-i18n="settings">Настройки</p>
            <div class="block-bg p-4 mb-8 flex flex-wrap gap-4 items-center">
                <label class="flex items-center gap-2 text-sm text-stone-300">
                    <span data-i18n="theme">Тема</span>:
                    <select id="themeSelect"
                        class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-stone-100">
                        <option value="dark" <?= $theme === 'dark' ? 'selected' : '' ?> data-i18n="dark">Тёмная</option>
                        <option value="light" <?= $theme === 'light' ? 'selected' : '' ?> data-i18n="light">Светлая
                        </option>
                    </select>
                </label>
                <label class="flex items-center gap-2 text-sm text-stone-300">
                    <span data-i18n="language">Язык</span>:
                    <select id="langSelect"
                        class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-stone-100">
                        <option value="ru" <?= $lang === 'ru' ? 'selected' : '' ?> data-i18n="russian">Русский</option>
                        <option value="en" <?= $lang === 'en' ? 'selected' : '' ?> data-i18n="english">English</option>
                    </select>
                </label>

                <div class="ml-auto flex gap-2">
                    <button id="syncBtn"
                        class="rounded-lg bg-sky-500 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-400"
                        data-i18n="sync">
                        Синхронизировать прогресс
                    </button>
                    <button id="resetBtn"
                        class="rounded-lg border border-red-400/30 bg-red-500/10 px-4 py-2 text-sm font-semibold text-red-300 hover:bg-red-500/20"
                        data-i18n="reset">
                        Сбросить прогресс
                    </button>
                </div>
            </div>

            <?php foreach ($courses as $course): ?>
                <p class="php-h2"><?= htmlspecialchars($course['title']) ?></p>
                <div class="code mb-8 space-y-2">
                    <?php foreach ($course['modules'] as $module):
                        $isDone = in_array($module['id'], $completed, true);
                        ?>
                        <div class="flex items-center gap-3 rounded-lg border border-white/10 bg-white/5 px-4 py-3"
                            data-module="<?= htmlspecialchars($module['id']) ?>">
                            <input type="checkbox"
                                class="module-toggle h-4 w-4 rounded border-white/20 bg-white/5 text-sky-500 focus:ring-sky-400"
                                data-module="<?= htmlspecialchars($module['id']) ?>" <?= $isDone ? 'checked' : '' ?>>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-stone-100">
                                    <?= htmlspecialchars($module['title']) ?>
                                </div>
                                <div class="text-xs text-stone-500">
                                    ID: <?= htmlspecialchars($module['id']) ?> · <?= $module['hours'] ?> ч.
                                </div>
                            </div>
                            <span class="module-status text-xs <?= $isDone ? 'text-sky-300' : 'text-stone-500' ?>">
                                <?= $isDone ? 'Завершён' : 'Не начат' ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <footer class="mt-10 text-center text-xs text-stone-500">
                © <?= date('Y') ?> · PHP Labs
            </footer>
        </div>
    </main>

    <script>
        window.__SERVER_PROGRESS__ = <?= json_encode($progress, JSON_UNESCAPED_UNICODE) ?>;
        window.__USER__ = <?= json_encode($_SESSION['user']) ?>;
        window.__THEME__ = <?= json_encode($theme) ?>;
        window.__LANG__ = <?= json_encode($lang) ?>;
    </script>
    <script src="assets/app.js"></script>
</body>

</html>