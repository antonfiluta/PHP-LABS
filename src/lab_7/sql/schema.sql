CREATE DATABASE IF NOT EXISTS school_diary
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE school_diary;

DROP TABLE IF EXISTS grades;
DROP TABLE IF EXISTS assignments;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS classes;

CREATE TABLE classes (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(100) NOT NULL,
    teacher      VARCHAR(100) NOT NULL,
    grade_level  INT NOT NULL
);

CREATE TABLE users (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    name           VARCHAR(100) NOT NULL,
    email          VARCHAR(150) UNIQUE NOT NULL,
    password_hash  VARCHAR(255) NOT NULL,
    role           ENUM('student', 'teacher', 'admin') DEFAULT 'student',
    class_id       INT NULL,
    subjects       JSON NULL,           -- список предметов для учителя
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL
);

CREATE TABLE lessons (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    class_id    INT NOT NULL,
    subject     VARCHAR(100) NOT NULL,
    lesson_date DATE NOT NULL,
    slot        TINYINT NOT NULL,
    homework    TEXT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_class_date_slot (class_id, lesson_date, slot),
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
);

CREATE TABLE grades (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    lesson_id   INT NOT NULL,
    user_id     INT NOT NULL,
    grade       INT NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_lesson_user (lesson_id, user_id),
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE
);