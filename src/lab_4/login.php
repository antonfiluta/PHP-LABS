<?php
declare(strict_types=1);

require_once __DIR__ . '/src/session.php';
require_once __DIR__ . '/src/users.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $pass = $_POST['pass'] ?? '';

    $users = get_users();

    if (isset($users[$login]) && password_verify($pass, $users[$login]['password'])) {
        session_regenerate_id(true);   // защита от фиксации
        $_SESSION['user'] = $login;
        $_SESSION['name'] = $users[$login]['name'];
        $_SESSION['group'] = $users[$login]['group'];
        header('Location: index.php');
        exit;
    }
    $error = 'Неверный логин или пароль.';
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP LABS | Вход</title>
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

    <main class="relative flex min-h-screen items-center justify-center px-6 py-28">
        <div class="w-full max-w-md">
            <div class="mb-10 text-center">
                <p class="php-variant">Вариант 13</p>
                <h1 class="php-h1">Образовательная платформа</h1>
                <p class="text-lg text-stone-400">Вход в систему</p>
            </div>

            <?php if ($error !== null): ?>
                <div class="mb-4 rounded-lg border border-red-400/30 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" class="block-bg p-6 space-y-4">
                <div>
                    <label for="login" class="block mb-1 text-sm text-stone-300">Логин</label>
                    <input type="text" id="login" name="login" required
                        class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100 focus:border-sky-400 focus:outline-none">
                </div>
                <div>
                    <label for="pass" class="block mb-1 text-sm text-stone-300">Пароль</label>
                    <input type="password" id="pass" name="pass" required
                        class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-stone-100 focus:border-sky-400 focus:outline-none">
                </div>
                <button type="submit"
                    class="w-full rounded-lg bg-sky-500 px-4 py-2 font-semibold text-white hover:bg-sky-400">
                    Войти
                </button>
                <p class="text-xs text-stone-500 text-center">
                    Демо-доступ: <code class="text-sky-300">student / study123</code>
                </p>
            </form>
        </div>
    </main>
</body>

</html>