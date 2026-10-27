<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/init.php';

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? null)) {
        $error = 'Ошибка CSRF';
    } else {
        $email = trim($_POST['email'] ?? '');
        $pass = $_POST['password'] ?? '';

        $users = new UserRepository();
        $user = $users->findByEmail($email);

        if ($user && password_verify($pass, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => (int) $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'class_id' => $user['class_id'] ? (int) $user['class_id'] : null,
            ];
            redirect('index.php');
        }
        $error = 'Неверный email или пароль';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Школьный дневник | Вход</title>
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
                <h1 class="php-h1">Школьный дневник</h1>
                <p class="text-stone-400">Вход в систему</p>
            </div>

            <?php if ($error): ?>
                <div class="mb-4 rounded-lg border border-red-400/30 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" class="block-bg p-6 space-y-4">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <div>
                    <label class="block mb-1 text-sm text-stone-300">Email</label>
                    <input name="email" type="email" required
                        class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100 focus:border-sky-400 focus:outline-none">
                </div>
                <div>
                    <label class="block mb-1 text-sm text-stone-300">Пароль</label>
                    <input name="password" type="password" required
                        class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100 focus:border-sky-400 focus:outline-none">
                </div>
                <button type="submit"
                    class="w-full rounded-lg bg-sky-500 px-4 py-2 font-semibold text-white hover:bg-sky-400">
                    Войти
                </button>
                <p class="text-xs text-stone-500 text-center">
                    Демо: <code class="text-sky-300">admin@diary.local / admin123</code><br>
                    <code class="text-sky-300">student@diary.local / student123</code>
                </p>
                <p class="text-xs text-stone-500 text-center">
                    Нет аккаунта? <a href="register.php" class="text-sky-300 hover:underline">Зарегистрироваться</a>
                </p>
            </form>
        </div>
    </main>
</body>

</html>