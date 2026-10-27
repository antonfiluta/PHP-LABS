<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>PHP | LAB 7</title>
    <link href="../output.css" rel="stylesheet">
</head>

<body class="min-h-screen antialiased">
    <div class="fixed inset-0 -z-10 overflow-hidden bg-linear-to-br from-stone-950 via-stone-950 to-stone-900">
        <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>
    </div>

    <header class="fixed top-4 left-4 right-4 z-20 flex flex-wrap items-center gap-3">
        <a href="../index.php"
            class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium backdrop-blur-md hover:bg-white/10!">PHP
            LABS</a>
    </header>

    <main class="relative flex min-h-screen items-center justify-center px-6 py-28">
        <div class="w-full max-w-3xl">
            <header class="mb-12 text-center">
                <p class="mb-3 text-xs font-semibold tracking-[0.3em] text-sky-400 uppercase">Вариант 13</p>
                <h1 class="php-h1">Лабораторная работа №7</h1>
                <p class="text-lg text-stone-400">Базы данных и защита веб-приложений</p>
            </header>

            <nav class="grid gap-3">
                <a href="public/login.php"
                    class="block-bg overflow-hidden group flex items-center gap-4 px-4 py-4 transition-all duration-150 hover:bg-white/10!">
                    <span
                        class="flex min-w-12 justify-center rounded-md bg-linear-to-br from-white/10 to-white/5 text-3xl font-bold text-white/60">→</span>
                    <span class="flex-1">
                        <span class="block font-medium text-stone-100">Войти в «Школьный дневник»</span>
                        <span class="block text-xs text-stone-500">admin / teacher / student</span>
                    </span>
                </a>
                <a href="public/seed.php"
                    class="block-bg overflow-hidden group flex items-center gap-4 px-4 py-4 transition-all duration-150 hover:bg-white/10!">
                    <span
                        class="flex min-w-12 justify-center rounded-md bg-linear-to-br from-white/10 to-white/5 text-3xl font-bold text-white/60">⚙</span>
                    <span class="flex-1">
                        <span class="block font-medium text-stone-100">Заполнить тестовыми данными</span>
                        <span class="block text-xs text-stone-500">12 пользователей, 5 классов, 60 заданий</span>
                    </span>
                </a>
            </nav>
        </div>
    </main>
</body>

</html>