<?php
declare(strict_types=1);

/**
 * Курсы и модули образовательной платформы.
 *
 * @return array<string, array{title: string, modules: array<int, array{id: string, title: string, hours: int}>}>
 */
function get_courses(): array
{
    return [
        'php' => [
            'title' => 'Высокоуровневые языки: PHP',
            'modules' => [
                ['id' => 'php_01', 'title' => 'Основы синтаксиса и типы данных', 'hours' => 4],
                ['id' => 'php_02', 'title' => 'Функции и области видимости', 'hours' => 6],
                ['id' => 'php_03', 'title' => 'Массивы и коллекции', 'hours' => 8],
                ['id' => 'php_04', 'title' => 'ООП: классы и наследование', 'hours' => 10],
                ['id' => 'php_05', 'title' => 'Сессии, куки, localStorage', 'hours' => 6],
            ],
        ],
        'js' => [
            'title' => 'Frontend: JavaScript',
            'modules' => [
                ['id' => 'js_01', 'title' => 'Переменные и типы', 'hours' => 3],
                ['id' => 'js_02', 'title' => 'DOM и события', 'hours' => 5],
                ['id' => 'js_03', 'title' => 'Fetch и асинхронность', 'hours' => 7],
            ],
        ],
    ];
}

/**
 * Находит модуль по его ID.
 *
 * @param array<string, array> $courses
 * @return array{id: string, title: string, hours: int}|null
 */
function find_module(array $courses, string $moduleId): ?array
{
    foreach ($courses as $course) {
        foreach ($course['modules'] as $module) {
            if ($module['id'] === $moduleId) {
                return $module;
            }
        }
    }
    return null;
}