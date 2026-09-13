<?php
declare(strict_types=1);

class ValidationException extends \Exception
{
    public function __construct(
        string $message = 'Ошибка валидации',
    ) {
        parent::__construct($message);
    }
}

class UserData
{
    public function __construct(
        public string $username,
        public string $password,
        public string $email,
    ) {
    }

    public function validateUser(): bool
    {
        if (empty($this->username))
            throw new ValidationException('UserName не может быть пустым');
        if (empty($this->password))
            throw new ValidationException($this->username . ' забыл поставить пароль');
        if (empty($this->email))
            throw new ValidationException('Email не может быть пустым');
        return true;
    }

    public function getData(): string
    {
        return
            'Username: ' . $this->username . "<br>"
            . 'Password: ' . $this->password . "<br>"
            . 'Email: ' . $this->email . "<br>";
    }
}

?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>PHP LABS | TASK 2</title>
    <link href="../output.css" rel="stylesheet">
</head>

<body class="min-h-screen antialiased">
    <div class="fixed inset-0 -z-10 overflow-hidden bg-linear-to-br from-stone-950 via-stone-950 to-stone-900">
        <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl">
        </div>
        <div
            class="absolute top-1/2 left-1/2 h-96 w-96 -translate-x-1/2 -translate-y-1/2 rounded-full blur-3xl bg-sky-400/10">
        </div>
    </div>

    <header class="fixed top-4 left-4 right-4 z-20 flex flex-wrap items-center gap-3">
        <?php
        $links = [
            ['../index.php', ' PHP LABS'],
            ['./lab-base.php', '← Prev Page'],
        ];

        foreach ($links as [$href, $title]): ?>
            <a href="<?= htmlspecialchars($href) ?>"
                class=" rounded-lg flex items-center gap-2 border px-4 py-2 text-sm font-medium backdrop-blur-md transition-all duration-200 hover:shadow-lg hover:shadow-sky-500/5 border-white/10 bg-white/5 hover:bg-white/10!">
                <?= htmlspecialchars($title) ?>
            </a>
        <?php endforeach; ?>
    </header>

    <main class="relative flex min-h-screen items-center justify-center px-6 py-28">
        <div class="w-full max-w-3xl">
            <div class="mb-12 text-center">
                <p class="php-variant">
                    Вариант 13
                </p>
                <h1 class="php-h1">
                    Задание 2. <br> Валидация с ValidationException
                </h1>
                <p class="text-lg text-stone-400">
                    Лабораторная работа 1
                </p>
            </div>

            <p class="php-h2">Результат без ошибок</p>
            <div class="code">
                <?php
                $data = new UserData('Anton', 'my_sycret', 'anotn@mail.ru');

                try {
                    $result = $data->validateUser();

                    if ($result)
                        echo "Successfully create UserData: <br><br>" . $data->getData();
                } catch (ValidationException $e) {
                    echo "Got error: " . $e->getMessage();
                }
                ?>
            </div>

            <p class="php-h2">Результат с ошибкой</p>
            <div class="code">
                <?php
                $data = new UserData('Graph Monte-Christo', '', 'monte@mail.ru');

                try {
                    $result = $data->validateUser();

                    if ($result)
                        echo "Successfully create UserData: <br><br>" . $data->getData();
                } catch (ValidationException $e) {
                    echo "Got error: " . $e->getMessage();
                }
                ?>
            </div>

            <p class="php-h2">Результат без ошибок</p>
            <div class="code">
                <?php
                $data = new UserData('Sofia', 'murmur', '****');

                try {
                    $result = $data->validateUser();

                    if ($result)
                        echo "Successfully create UserData: <br><br>" . $data->getData();
                } catch (ValidationException $e) {
                    echo "Got error: " . $e->getMessage();
                }
                ?>
            </div>

            <footer class="mt-10 text-center text-xs text-stone-500 dark:text-stone-500">
                © <?= date('Y') ?> · PHP Labs
            </footer>
        </div>
    </main>
</body>

</html>