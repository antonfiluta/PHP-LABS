<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/init.php';

set_time_limit(120);

db()->exec('SET FOREIGN_KEY_CHECKS=0');
foreach (['grades', 'lessons', 'users', 'classes'] as $t) {
    db()->exec("TRUNCATE TABLE {$t}");
}
db()->exec('SET FOREIGN_KEY_CHECKS=1');

echo '<pre style="padding:20px;color:#7dd3fc;background:#0c0a09;font-family:monospace;">';
echo "Сброс выполнен\n";

// ---------- Классы ----------
$classRepo = new ClassRepository();
$classes = [
    ['7А', 'Иванова М.П.', 7],
    ['7Б', 'Петрова А.С.', 7],
    ['8А', 'Сидорова О.В.', 8],
    ['9А', 'Кузнецов П.И.', 9],
    ['10А', 'Смирнова Е.А.', 10],
];
$classIds = [];
foreach ($classes as [$name, $teacher, $grade]) {
    $classIds[$name] = $classRepo->create($name, $teacher, $grade);
    echo "Класс {$name} → id={$classIds[$name]}\n";
}

// ---------- Пользователи ----------
$insertUser = db()->prepare(
    'INSERT INTO users (name, email, password_hash, role, class_id, subjects)
     VALUES (?, ?, ?, ?, ?, ?)'
);

// Админ
$insertUser->execute([
    'Администратор',
    'admin@diary.local',
    password_hash('admin123', PASSWORD_DEFAULT),
    'admin',
    null,
    null,
]);

// Учителя (4 шт., у каждого — свой набор предметов)
$teachers = [
    ['Казюцич В.О.', 'teacher@diary.local', ['Информатика', 'Математика']],
    ['Петрова А.С.', 'teacher2@diary.local', ['Физика', 'Химия']],
    ['Сидорова О.В.', 'teacher3@diary.local', ['Русский язык', 'История']],
    ['Кузнецов П.И.', 'teacher4@diary.local', ['Математика', 'История']],
];
foreach ($teachers as [$name, $email, $subjects]) {
    $insertUser->execute([
        $name,
        $email,
        password_hash('teacher123', PASSWORD_DEFAULT),
        'teacher',
        null,
        json_encode($subjects, JSON_UNESCAPED_UNICODE),
    ]);
    echo "Учитель {$name} ({$email})\n";
}

// Ученики: по 5 на класс
$studentStmt = db()->prepare(
    'INSERT INTO users (name, email, password_hash, role, class_id)
     VALUES (?, ?, ?, "student", ?)'
);
$studentsByClass = [];
$firstNames = [
    'Алексей',
    'Мария',
    'Дмитрий',
    'Анна',
    'Сергей',
    'Ольга',
    'Иван',
    'Екатерина',
    'Никита',
    'Елена'
];
$lastNames = [
    'Иванов',
    'Петров',
    'Смирнов',
    'Кузнецов',
    'Попов',
    'Васильев',
    'Соколов',
    'Михайлов',
    'Новиков',
    'Фёдоров'
];

foreach ($classIds as $className => $cid) {
    for ($i = 0; $i < 5; $i++) {
        $idx = ($cid * 5 + $i) % 10;
        $name = $firstNames[$idx] . ' ' . $lastNames[$idx];
        $email = strtolower("student{$cid}_{$i}@diary.local");
        $studentStmt->execute([
            $name,
            $email,
            password_hash('student123', PASSWORD_DEFAULT),
            $cid,
        ]);
        $studentsByClass[$cid][] = (int) db()->lastInsertId();
    }
}

// Демо-ученик из 7А с предсказуемым email
$studentStmt->execute([
    'Алексей Иванов',
    'student@diary.local',
    password_hash('student123', PASSWORD_DEFAULT),
    $classIds['7А'],
]);
$studentsByClass[$classIds['7А']][] = (int) db()->lastInsertId();

echo "Создано учеников: " . array_sum(array_map('count', $studentsByClass)) . "\n";

// ---------- Уроки на 2 недели ----------
$lessonRepo = new LessonRepository();
$subjectsAll = [
    'Математика',
    'Физика',
    'Русский язык',
    'История',
    'Информатика',
    'Химия'
];

$today = new DateTimeImmutable('today');
$monday = $today->modify('monday this week');
$lessonCount = 0;
$gradeCount = 0;
$gradeStmt = db()->prepare(
    'INSERT INTO grades (lesson_id, user_id, grade) VALUES (?, ?, ?)'
);

for ($week = 0; $week < 2; $week++) {
    $weekMonday = $monday->modify("+{$week} week");

    for ($d = 0; $d < 6; $d++) {
        $date = $weekMonday->modify("+{$d} day")->format('Y-m-d');

        foreach ($classIds as $cid) {
            $slotCount = random_int(4, 7);
            $allSlots = array_keys(LessonRepository::SLOTS);
            shuffle($allSlots);
            $slots = array_slice($allSlots, 0, $slotCount);
            sort($slots);

            foreach ($slots as $slot) {
                $subject = $subjectsAll[array_rand($subjectsAll)];
                $homework = random_int(1, 10) <= 8
                    ? "Домашнее задание по {$subject} от {$date}"
                    : null;

                try {
                    $lessonId = $lessonRepo->create($cid, $subject, $date, $slot, $homework);
                } catch (Throwable $e) {
                    continue;
                }
                $lessonCount++;

                foreach ($studentsByClass[$cid] as $sid) {
                    if (random_int(1, 10) <= 7) {
                        $gradeStmt->execute([$lessonId, $sid, random_int(3, 10)]);
                        $gradeCount++;
                    }
                }
            }
        }
    }
}

echo "Создано уроков: {$lessonCount}\n";
echo "Создано оценок: {$gradeCount}\n";
echo "Готово. Можно возвращаться к приложению.";
echo '</pre>';