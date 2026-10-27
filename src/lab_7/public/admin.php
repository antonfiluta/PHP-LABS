<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/init.php';
requireRole('admin');

$userRepo   = new UserRepository();
$classRepo  = new ClassRepository();
$gradeRepo  = new GradeRepository();
$lessonRepo = new LessonRepository();

$ALL_SUBJECTS = ['Математика', 'Физика', 'Русский язык', 'История', 'Информатика', 'Химия'];
$errors = null; $message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? null)) {
        $errors = 'Ошибка CSRF';
    } else {
        $action = $_POST['action'] ?? '';

        // --- Ученики ---
        if ($action === 'create_student') {
            $name    = trim($_POST['name'] ?? '');
            $email   = trim($_POST['email'] ?? '');
            $pass    = $_POST['password'] ?? '';
            $classId = (int)($_POST['class_id'] ?? 0);

            if (mb_strlen($name) < 2) $errors = 'Имя: минимум 2 символа';
            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors = 'Некорректный email';
            elseif (mb_strlen($pass) < 6) $errors = 'Пароль: минимум 6 символов';
            elseif (!$classId) $errors = 'Выберите класс';
            elseif ($userRepo->findByEmail($email)) $errors = 'Email уже занят';
            else {
                $stmt = db()->prepare('INSERT INTO users (name, email, password_hash, role, class_id) VALUES (?, ?, ?, "student", ?)');
                $stmt->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT), $classId]);
                $message = 'Ученик создан';
            }
        }
        if ($action === 'update_student') {
            $stmt = db()->prepare('UPDATE users SET class_id = ? WHERE id = ? AND role = "student"');
            $stmt->execute([(int)$_POST['class_id'], (int)$_POST['id']]);
            $message = 'Класс обновлён';
        }

        // --- Учителя ---
        if ($action === 'create_teacher') {
            $name     = trim($_POST['name'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $pass     = $_POST['password'] ?? '';
            $subjects = $_POST['subjects'] ?? [];

            if (mb_strlen($name) < 2) $errors = 'Имя: минимум 2 символа';
            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors = 'Некорректный email';
            elseif (mb_strlen($pass) < 6) $errors = 'Пароль: минимум 6 символов';
            elseif (!$subjects) $errors = 'Выберите хотя бы один предмет';
            elseif ($userRepo->findByEmail($email)) $errors = 'Email уже занят';
            else {
                $stmt = db()->prepare('INSERT INTO users (name, email, password_hash, role, subjects) VALUES (?, ?, ?, "teacher", ?)');
                $stmt->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT), json_encode($subjects, JSON_UNESCAPED_UNICODE)]);
                $message = 'Учитель создан';
            }
        }
        if ($action === 'update_teacher') {
            $subjects = $_POST['subjects'] ?? [];
            $stmt = db()->prepare('UPDATE users SET subjects = ? WHERE id = ? AND role = "teacher"');
            $stmt->execute([json_encode($subjects, JSON_UNESCAPED_UNICODE), (int)$_POST['id']]);
            $message = 'Предметы обновлены';
        }

        // --- Удаление (только ученики и учителя) ---
        if ($action === 'delete_user') {
            $id = (int)$_POST['id'];
            if ($id === currentUser()['id']) $errors = 'Нельзя удалить себя';
            else {
                $stmt = db()->prepare('DELETE FROM users WHERE id = ? AND role IN ("student", "teacher")');
                $stmt->execute([$id]);
                $message = 'Пользователь удалён';
            }
        }

        // --- Классы ---
        if ($action === 'class_create') {
            $name    = trim($_POST['name'] ?? '');
            $teacher = trim($_POST['teacher'] ?? '');
            $grade   = (int)($_POST['grade_level'] ?? 0);
            if ($name === '' || $teacher === '' || $grade < 1 || $grade > 11) $errors = 'Заполните поля класса';
            else {
                $classRepo->create($name, $teacher, $grade);
                $message = 'Класс добавлен';
            }
        }
        if ($action === 'class_delete') {
            $classRepo->delete((int)$_POST['id']);
            $message = 'Класс удалён';
        }
    }
}

$students = db()->query('SELECT u.*, c.name AS class_name FROM users u LEFT JOIN classes c ON c.id = u.class_id WHERE u.role = "student" ORDER BY c.name, u.name')->fetchAll();
$teachers = db()->query('SELECT * FROM users WHERE role = "teacher" ORDER BY name')->fetchAll();
$classes  = $classRepo->all();
$stats    = $gradeRepo->stats();
$cntAssign = $lessonRepo->count();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Дневник | Админ-панель</title>
    <link href="../../output.css" rel="stylesheet">
</head>
<body class="min-h-screen antialiased">
    <div class="fixed inset-0 -z-10 overflow-hidden bg-linear-to-br from-stone-950 via-stone-950 to-stone-900">
        <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>
    </div>

    <header class="fixed top-4 left-4 right-4 z-20 flex flex-wrap items-center gap-3">
        <a href="index.php" class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium backdrop-blur-md hover:bg-white/10!">← Назад</a>
        <a href="logout.php" class="ml-auto rounded-lg border border-red-400/30 bg-red-500/10 px-4 py-2 text-sm font-medium text-red-300 hover:bg-red-500/20">Выход</a>
    </header>

    <main class="relative min-h-screen px-6 py-24">
        <div class="mx-auto w-full max-w-6xl">
            <div class="mb-8 text-center">
                <p class="php-variant">Вариант 13</p>
                <h1 class="php-h1">Админ-панель</h1>
            </div>

            <?php if ($errors): ?>
                <div class="mb-4 rounded-lg border border-red-400/30 bg-red-500/10 px-4 py-3 text-sm text-red-200"><?= e($errors) ?></div>
            <?php endif; ?>
            <?php if ($message): ?>
                <div class="mb-4 rounded-lg border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200"><?= e($message) ?></div>
            <?php endif; ?>

            <!-- Статистика -->
            <div class="grid gap-3 sm:grid-cols-4 mb-8">
                <?php
                $cards = [
                    ['Учеников', count($students), 'sky'],
                    ['Учителей', count($teachers), 'emerald'],
                    ['Классов',  count($classes),  'amber'],
                    ['Уроков',   $cntAssign,       'violet'],
                ];
                foreach ($cards as [$label, $value, $color]):
                ?>
                    <div class="block-bg p-4 text-center">
                        <div class="text-xs uppercase tracking-wider text-stone-500"><?= e($label) ?></div>
                        <div class="text-3xl font-bold text-<?= $color ?>-300 mt-2"><?= (int)$value ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Создать ученика -->
            <p class="php-h2">Добавить ученика</p>
            <form method="post" class="block-bg p-4 mb-6 grid gap-2 md:grid-cols-5">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="create_student">
                <input name="name" required placeholder="Имя" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                <input name="email" type="email" required placeholder="Email" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                <input name="password" type="password" required minlength="6" placeholder="Пароль" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                <select name="class_id" required class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                    <option value="">— класс —</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="rounded-lg bg-sky-500 px-4 py-2 font-semibold text-white hover:bg-sky-400">Добавить</button>
            </form>

            <!-- Создать учителя -->
            <p class="php-h2">Добавить учителя</p>
            <form method="post" class="block-bg p-4 mb-6 grid gap-2 md:grid-cols-4">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="create_teacher">
                <input name="name" required placeholder="Имя" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                <input name="email" type="email" required placeholder="Email" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                <input name="password" type="password" required minlength="6" placeholder="Пароль" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                <select name="subjects[]" multiple required class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100 h-24">
                    <?php foreach ($ALL_SUBJECTS as $s): ?>
                        <option value="<?= e($s) ?>"><?= e($s) ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="md:col-span-4 rounded-lg bg-sky-500 px-4 py-2 font-semibold text-white hover:bg-sky-400">Добавить учителя</button>
            </form>

            <!-- Учителя -->
            <p class="php-h2">Учителя (<?= count($teachers) ?>)</p>
            <div class="block-bg overflow-x-auto mb-6">
                <table class="w-full text-sm">
                    <thead class="bg-white/5 text-stone-300">
                        <tr>
                            <th class="px-3 py-2 text-left">Имя</th>
                            <th class="px-3 py-2 text-left">Email</th>
                            <th class="px-3 py-2 text-left">Предметы</th>
                            <th class="px-3 py-2 text-center">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teachers as $t):
                            $subjects = $t['subjects'] ? json_decode($t['subjects'], true) : [];
                        ?>
                            <tr class="border-t border-white/5">
                                <td class="px-3 py-2"><?= e($t['name']) ?></td>
                                <td class="px-3 py-2 text-stone-400"><?= e($t['email']) ?></td>
                                <td class="px-3 py-2">
                                    <form method="post" class="flex gap-1 items-center">
                                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="action" value="update_teacher">
                                        <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                                        <select name="subjects[]" multiple class="rounded border border-white/10 bg-white/5 px-2 py-1 text-xs text-stone-100 h-16">
                                            <?php foreach ($ALL_SUBJECTS as $s): ?>
                                                <option value="<?= e($s) ?>" <?= in_array($s, $subjects ?: [], true) ? 'selected' : '' ?>><?= e($s) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button class="rounded border border-sky-400/30 bg-sky-500/10 px-2 py-1 text-xs text-sky-300 hover:bg-sky-500/20">💾</button>
                                    </form>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <form method="post" onsubmit="return confirm('Удалить учителя?')">
                                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                                        <button class="rounded border border-red-400/30 bg-red-500/10 px-2 py-1 text-xs text-red-300 hover:bg-red-500/20">Удалить</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Ученики -->
            <p class="php-h2">Ученики (<?= count($students) ?>)</p>
            <div class="block-bg overflow-x-auto mb-6">
                <table class="w-full text-sm">
                    <thead class="bg-white/5 text-stone-300">
                        <tr>
                            <th class="px-3 py-2 text-left">Имя</th>
                            <th class="px-3 py-2 text-left">Email</th>
                            <th class="px-3 py-2 text-left">Класс</th>
                            <th class="px-3 py-2 text-center">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $s): ?>
                            <tr class="border-t border-white/5">
                                <td class="px-3 py-2"><?= e($s['name']) ?></td>
                                <td class="px-3 py-2 text-stone-400"><?= e($s['email']) ?></td>
                                <td class="px-3 py-2">
                                    <form method="post" class="flex gap-1 items-center">
                                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="action" value="update_student">
                                        <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                                        <select name="class_id" class="rounded border border-white/10 bg-white/5 px-2 py-1 text-xs text-stone-100">
                                            <?php foreach ($classes as $c): ?>
                                                <option value="<?= (int)$c['id'] ?>" <?= (int)$s['class_id'] === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button class="rounded border border-sky-400/30 bg-sky-500/10 px-2 py-1 text-xs text-sky-300 hover:bg-sky-500/20">💾</button>
                                    </form>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <form method="post" onsubmit="return confirm('Удалить ученика? Все его оценки будут удалены.')">
                                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                                        <button class="rounded border border-red-400/30 bg-red-500/10 px-2 py-1 text-xs text-red-300 hover:bg-red-500/20">Удалить</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Классы -->
            <p class="php-h2">Классы (<?= count($classes) ?>)</p>
            <form method="post" class="block-bg p-4 mb-4 grid gap-2 md:grid-cols-4">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="class_create">
                <input name="name" required placeholder="Название (например, 11А)" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                <input name="teacher" required placeholder="Кл. руководитель" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                <input name="grade_level" required type="number" min="1" max="11" placeholder="Параллель" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                <button class="rounded-lg bg-sky-500 px-4 py-2 font-semibold text-white hover:bg-sky-400">Добавить</button>
            </form>

            <div class="block-bg overflow-x-auto mb-6">
                <table class="w-full text-sm">
                    <thead class="bg-white/5 text-stone-300">
                        <tr>
                            <th class="px-3 py-2 text-left">Класс</th>
                            <th class="px-3 py-2 text-left">Кл. руководитель</th>
                            <th class="px-3 py-2 text-left">Параллель</th>
                            <th class="px-3 py-2 text-center">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($classes as $c): ?>
                            <tr class="border-t border-white/5">
                                <td class="px-3 py-2"><?= e($c['name']) ?></td>
                                <td class="px-3 py-2 text-stone-400"><?= e($c['teacher']) ?></td>
                                <td class="px-3 py-2 text-stone-400"><?= (int)$c['grade_level'] ?></td>
                                <td class="px-3 py-2 text-center">
                                    <form method="post" onsubmit="return confirm('Удалить класс? Все уроки и оценки будут удалены.')">
                                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="action" value="class_delete">
                                        <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                                        <button class="rounded border border-red-400/30 bg-red-500/10 px-2 py-1 text-xs text-red-300 hover:bg-red-500/20">Удалить</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Средние оценки -->
            <p class="php-h2">Средние оценки по предметам</p>
            <div class="block-bg overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-white/5 text-stone-300">
                        <tr>
                            <th class="px-4 py-2 text-left">Предмет</th>
                            <th class="px-4 py-2 text-left">Средний балл</th>
                            <th class="px-4 py-2 text-left">Оценок</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats as $s): ?>
                            <tr class="border-t border-white/5">
                                <td class="px-4 py-2"><?= e($s['subject']) ?></td>
                                <td class="px-4 py-2 text-sky-300"><?= e($s['avg_grade']) ?></td>
                                <td class="px-4 py-2 text-stone-400"><?= (int)$s['cnt'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>