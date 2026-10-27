<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/init.php';

$classes = (new ClassRepository())->all();
$errors = [];
$sent = ['name' => '', 'email' => '', 'class_id' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? null)) {
        $errors[] = 'Ошибка CSRF';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pass = $_POST['password'] ?? '';
        $classId = (int) ($_POST['class_id'] ?? 0) ?: null;
        $sent = compact('name', 'email');

        if ($name === '' || mb_strlen($name) < 2)
            $errors[] = 'Имя: минимум 2 символа';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            $errors[] = 'Некорректный email';
        if (mb_strlen($pass) < 6)
            $errors[] = 'Пароль: минимум 6 символов';

        $users = new UserRepository();
        if (!$errors && $users->findByEmail($email))
            $errors[] = 'Пользователь с таким email уже есть';

        if (!$errors) {
            $id = $users->create($name, $email, $pass, 'student', $classId);
            $_SESSION['user'] = [
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'role' => 'student',
            ];
            redirect('index.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Школьный дневник | Регистрация</title>
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

    <main class="flex min-h-screen items-center justify-center px-6 py-16">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <p class="php-variant">Вариант 13</p>
                <h1 class="php-h1">Регистрация ученика</h1>
                <p class="text-stone-400">Школьный дневник</p>
            </div>

            <?php if ($errors): ?>
                <div class="mb-4 rounded-lg border border-red-400/30 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    <ul class="list-disc pl-5">
                        <?php foreach ($errors as $err): ?>
                            <li>
                                <?= e($err) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" class="block-bg p-6 space-y-4">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <div>
                    <label class="block mb-1 text-sm text-stone-300">Имя</label>
                    <input name="name" required value="<?= e($sent['name']) ?>"
                        class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100 focus:border-sky-400 focus:outline-none">
                </div>
                <div>
                    <label class="block mb-1 text-sm text-stone-300">Email</label>
                    <input name="email" type="email" required value="<?= e($sent['email']) ?>"
                        class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100 focus:border-sky-400 focus:outline-none">
                </div>
                <div>
                    <label class="block mb-1 text-sm text-stone-300">Пароль</label>
                    <input name="password" type="password" required minlength="6"
                        class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100 focus:border-sky-400 focus:outline-none">
                </div>
                <div>
                    <label class="block mb-1 text-sm text-stone-300">Класс</label>
                    <select name="class_id"
                        class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100">
                        <option value="">— не выбран —</option>
                        <?php foreach ($classes as $c): ?>
                            <option value="<?= (int) $c['id'] ?>">
                                <?= e($c['name']) ?> (
                                <?= (int) $c['grade_level'] ?> класс)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit"
                    class="w-full rounded-lg bg-sky-500 px-4 py-2 font-semibold text-white hover:bg-sky-400">
                    Зарегистрироваться
                </button>
                <p class="text-xs text-stone-500 text-center">
                    Уже есть аккаунт? <a href="login.php" class="text-sky-300 hover:underline">Войти</a>
                </p>
            </form>
        </div>
    </main>
</body>

</html>