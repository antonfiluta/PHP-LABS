<?php
declare(strict_types=1);

/**
 * Простая эмуляция пользователей. Пароли хранятся как хеши.
 * В реальном проекте — таблица в БД.
 */
function get_users(): array
{
    return [
        'student' => [
            'password' => password_hash('study123', PASSWORD_DEFAULT),
            'name' => 'Антон',
            'group' => '477901',
        ],
        'teacher' => [
            'password' => password_hash('teach456', PASSWORD_DEFAULT),
            'name' => 'Владислав Олегович',
            'group' => 'Преподаватель',
        ],
    ];
}