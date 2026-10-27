<?php
declare(strict_types=1);

class ClassRepository
{
    public function all(): array
    {
        return db()->query(
            'SELECT c.*, (SELECT COUNT(*) FROM users u WHERE u.class_id = c.id) AS students
             FROM classes c ORDER BY c.grade_level, c.name'
        )->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM classes WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $name, string $teacher, int $grade): int
    {
        $stmt = db()->prepare('INSERT INTO classes (name, teacher, grade_level) VALUES (?, ?, ?)');
        $stmt->execute([$name, $teacher, $grade]);
        return (int) db()->lastInsertId();
    }

    public function delete(int $id): void
    {
        $stmt = db()->prepare('DELETE FROM classes WHERE id = ?');
        $stmt->execute([$id]);
    }

    public function count(): int
    {
        return (int) db()->query('SELECT COUNT(*) FROM classes')->fetchColumn();
    }
}