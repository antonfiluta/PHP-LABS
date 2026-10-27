<?php
declare(strict_types=1);

class LessonRepository
{
    public const SLOTS = [
        1 => '09:00',
        2 => '10:00',
        3 => '11:00',
        4 => '12:00',
        5 => '13:00',
        6 => '14:00',
        7 => '15:00',
    ];

    /** Занятые слоты на выбранный день для класса. */
    public function usedSlots(int $classId, string $date): array
    {
        $stmt = db()->prepare(
            'SELECT slot FROM lessons WHERE class_id = ? AND lesson_date = ?'
        );
        $stmt->execute([$classId, $date]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    /** Уроки на день для одного урока — получить одну строку. */
    public function find(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM lessons WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /** Создать урок, вернуть id или выбросить исключение. */
    public function create(
        int $classId,
        string $subject,
        string $date,
        int $slot,
        ?string $homework = null
    ): int {
        if (!isset(self::SLOTS[$slot])) {
            throw new InvalidArgumentException('Неверный номер урока');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw new InvalidArgumentException('Неверная дата');
        }

        $stmt = db()->prepare(
            'INSERT INTO lessons (class_id, subject, lesson_date, slot, homework)
             VALUES (?, ?, ?, ?, ?)'
        );
        try {
            $stmt->execute([$classId, $subject, $date, $slot, $homework]);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new RuntimeException('Урок уже существует на это время');
            }
            throw $e;
        }
        return (int) db()->lastInsertId();
    }

    /** Удалить урок. */
    public function delete(int $id): void
    {
        $stmt = db()->prepare('DELETE FROM lessons WHERE id = ?');
        $stmt->execute([$id]);
    }

    /** Сохранить ДЗ. */
    public function setHomework(int $id, string $text): void
    {
        $stmt = db()->prepare('UPDATE lessons SET homework = ? WHERE id = ?');
        $stmt->execute([$text, $id]);
    }

    /** Предметы учителя (массив строк). */
    public function subjectsForTeacher(int $userId): array
    {
        $stmt = db()->prepare('SELECT subjects FROM users WHERE id = ? AND role = "teacher"');
        $stmt->execute([$userId]);
        $json = $stmt->fetchColumn();
        return $json ? (json_decode($json, true) ?: []) : [];
    }

    public function count(): int
    {
        return (int) db()->query('SELECT COUNT(*) FROM lessons')->fetchColumn();
    }

    /** Уроки класса на неделю, с необязательным фильтром по предмету. */
    public function weekForClass(int $classId, string $mondayDate, ?string $subject = null): array
    {
        $sql = 'SELECT l.*,
                   (SELECT COUNT(*) FROM grades g WHERE g.lesson_id = l.id) AS grades_count
            FROM lessons l
            WHERE l.class_id = ?
              AND l.lesson_date BETWEEN ? AND DATE_ADD(?, INTERVAL 6 DAY)';
        $params = [$classId, $mondayDate, $mondayDate];

        if ($subject !== null && $subject !== '') {
            $sql .= ' AND l.subject = ?';
            $params[] = $subject;
        }
        $sql .= ' ORDER BY l.lesson_date, l.slot';

        $stmt = db()->prepare($sql);
        $stmt->execute($params);

        $byDate = [];
        foreach ($stmt->fetchAll() as $row) {
            $byDate[$row['lesson_date']][(int) $row['slot']] = $row;
        }
        return $byDate;
    }

    /** Предметы, которые есть у класса в его уроках (для фильтра). */
    public function subjectsForClass(int $classId): array
    {
        $stmt = db()->prepare(
            'SELECT DISTINCT subject FROM lessons WHERE class_id = ? ORDER BY subject'
        );
        $stmt->execute([$classId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}