<?php
declare(strict_types=1);

class GradeRepository
{

    public function forLesson(int $lessonId): array
    {
        $sql = 'SELECT g.*, u.name AS student_name
                FROM grades g JOIN users u ON u.id = g.user_id
                WHERE g.lesson_id = ?
                ORDER BY u.name';
        $stmt = db()->prepare($sql);
        $stmt->execute([$lessonId]);
        return $stmt->fetchAll();
    }

    /** Выставить или обновить оценку. */
    public function upsert(int $lessonId, int $userId, int $grade): void
    {
        if ($grade < 1 || $grade > 10) {
            throw new InvalidArgumentException('Оценка должна быть от 1 до 10');
        }
        $stmt = db()->prepare(
            'INSERT INTO grades (lesson_id, user_id, grade) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE grade = VALUES(grade)'
        );
        $stmt->execute([$lessonId, $userId, $grade]);
    }

    public function delete(int $id): void
    {
        $stmt = db()->prepare('DELETE FROM grades WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function stats(): array
    {
        return db()->query(
            'SELECT l.subject,
                    ROUND(AVG(g.grade), 1) AS avg_grade,
                    COUNT(*)               AS cnt
             FROM grades g JOIN lessons l ON l.id = g.lesson_id
             GROUP BY l.subject ORDER BY l.subject'
        )->fetchAll();
    }

    public function count(): int
    {
        return (int) db()->query('SELECT COUNT(*) FROM grades')->fetchColumn();
    }

    /** Оценки ученика с необязательным фильтром по предмету. */
    public function forStudent(int $userId, ?string $subject = null): array
    {
        $sql = 'SELECT g.id, g.grade, l.subject, l.lesson_date, l.slot, l.homework
            FROM grades g
            JOIN lessons l ON l.id = g.lesson_id
            WHERE g.user_id = ?';
        $params = [$userId];

        if ($subject !== null && $subject !== '') {
            $sql .= ' AND l.subject = ?';
            $params[] = $subject;
        }
        $sql .= ' ORDER BY l.lesson_date DESC, l.slot ASC';

        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Список предметов, по которым у ученика есть оценки. */
    public function subjectsForStudent(int $userId): array
    {
        $stmt = db()->prepare(
            'SELECT DISTINCT l.subject
         FROM grades g JOIN lessons l ON l.id = g.lesson_id
         WHERE g.user_id = ?
         ORDER BY l.subject'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}