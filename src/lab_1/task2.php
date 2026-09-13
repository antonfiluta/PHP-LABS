<?php
declare(strict_types=1);

/**
 * Собственный класс исключения для ошибок валидации.
 * Наследуется от базового Exception.
 */
class ValidationException extends Exception
{
    /** @var array Массив ошибок валидации (поле => сообщение) */
    private array $errors;

    public function __construct(
        array $errors,
        string $message = 'Ошибка валидации',
    ) {
        parent::__construct($message);
        $this->errors = $errors;
    }

    /** Возвращает список ошибок по полям */
    public function getErrors(): array
    {
        return $this->errors;
    }
}

/**
 * Чистая функция валидации данных.
 * Возвращает true, если всё корректно, иначе бросает ValidationException.
 */
function validateUserData(array $data): bool
{
    $errors = [];

    // Проверка имени: не пустое, минимум 2 символа
    $name = trim($data['name'] ?? '');
    if ($name === '') {
        $errors['name'] = 'Имя не может быть пустым.';
    } elseif (mb_strlen($name) < 2) {
        $errors['name'] = 'Имя должно содержать минимум 2 символа.';
    }

    // Проверка email через filter_var
    $email = trim($data['email'] ?? '');
    if ($email === '') {
        $errors['email'] = 'Email не может быть пустым.';
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Некорректный формат email.';
    }

    if (!empty($errors)) {
        throw new ValidationException($errors);
    }

    return true;
}

// --- Обработка формы ---
$result = null; // успешный результат
$errors = []; // ошибки валидации
$sentData = []; // отправленные данные (для повторного заполнения)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sentData = [
        'name' => $_POST['name'] ?? '',
        'email' => $_POST['email'] ?? '',
    ];

    try {
        validateUserData($sentData);
        $result = 'Данные успешно прошли валидацию!';
    } catch (ValidationException $e) {
        $errors = $e->getErrors();
    } catch (Throwable $e) {
        // На случай непредвиденных ошибок
        $errors['general'] = 'Непредвиденная ошибка: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Задание 2 — ValidationException</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 40px auto;
        }

        .field {
            margin-bottom: 12px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 4px;
        }

        input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        button {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .errors {
            background: #ffe0e0;
            border-left: 5px solid #d00;
            padding: 12px;
            margin: 15px 0;
        }

        .success {
            background: #e0ffe0;
            border-left: 5px solid #0a0;
            padding: 12px;
            margin: 15px 0;
        }

        .error-item {
            color: #d00;
        }
    </style>
</head>

<body>
    <h1>Задание 2. Валидация с ValidationException</h1>

    <?php if ($result !== null): ?>
        <div class="success"><?= htmlspecialchars($result) ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <strong>Найдены ошибки:</strong>
            <ul>
                <?php foreach ($errors as $field => $msg): ?>
                    <li class="error-item">
                        <?= htmlspecialchars($field) ?>: <?= htmlspecialchars(
                              $msg,
                          ) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="field">
            <label for="name">Имя:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($sentData['name'] ?? '') ?>">
        </div>
        <div class="field">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" value="<?= htmlspecialchars($sentData['email'] ?? '') ?>">
        </div>
        <button type="submit">Проверить</button>
    </form>

    <p><a href="index.php">← На главную</a></p>
</body>

</html>