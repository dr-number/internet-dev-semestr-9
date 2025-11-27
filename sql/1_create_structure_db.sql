-- Создание базы данных
CREATE DATABASE IF NOT EXISTS developer_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE developer_portfolio;

-- Основная информация
CREATE TABLE developer_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255),
    format_file_resume VARCHAR(10),
    name VARCHAR(255),
    date_born DATE,
    citizenship VARCHAR(100),
    career_objective TEXT,
    location TEXT,
    salary TEXT,
    employment VARCHAR(100),
    schedule TEXT,
    time_to_work VARCHAR(100),
    level_education VARCHAR(100)
);

-- Специализации
CREATE TABLE specializations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    developer_id INT,
    name VARCHAR(255),
    FOREIGN KEY (developer_id) REFERENCES developer_info(id)
);

-- Контакты
CREATE TABLE contacts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    developer_id INT,
    email VARCHAR(255),
    phone VARCHAR(50),
    FOREIGN KEY (developer_id) REFERENCES developer_info(id)
);

-- Ссылки на социальные сети
CREATE TABLE contact_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    contact_id INT,
    name VARCHAR(100),
    icon VARCHAR(100),
    link VARCHAR(500),
    FOREIGN KEY (contact_id) REFERENCES contacts(id)
);

-- Навыки
CREATE TABLE skills (
    id INT PRIMARY KEY AUTO_INCREMENT,
    developer_id INT,
    name VARCHAR(255),
    percentage INT,
    FOREIGN KEY (developer_id) REFERENCES developer_info(id)
);

-- Поднавыки
CREATE TABLE sub_skills (
    id INT PRIMARY KEY AUTO_INCREMENT,
    skill_id INT,
    name VARCHAR(255),
    percentage INT,
    FOREIGN KEY (skill_id) REFERENCES skills(id)
);

-- Проекты
CREATE TABLE projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    developer_id INT,
    name TEXT,
    link VARCHAR(500),
    FOREIGN KEY (developer_id) REFERENCES developer_info(id)
);

-- Достижения
CREATE TABLE achievements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    developer_id INT,
    description TEXT,
    FOREIGN KEY (developer_id) REFERENCES developer_info(id)
);

-- Опыт работы
CREATE TABLE work_experience (
    id INT PRIMARY KEY AUTO_INCREMENT,
    developer_id INT,
    company VARCHAR(255),
    job_title VARCHAR(255),
    time_period VARCHAR(100),
    FOREIGN KEY (developer_id) REFERENCES developer_info(id)
);

-- Обязанности в работе
CREATE TABLE work_responsibilities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    work_experience_id INT,
    description TEXT,
    FOREIGN KEY (work_experience_id) REFERENCES work_experience(id)
);

-- Курсы
CREATE TABLE courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    developer_id INT,
    name VARCHAR(255),
    description TEXT,
    time_period VARCHAR(100),
    FOREIGN KEY (developer_id) REFERENCES developer_info(id)
);

-- Репозитории
CREATE TABLE repositories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    developer_id INT,
    icon VARCHAR(100),
    name VARCHAR(255),
    link VARCHAR(500),
    FOREIGN KEY (developer_id) REFERENCES developer_info(id)
);

-- Хобби
CREATE TABLE hobbies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    developer_id INT,
    icon VARCHAR(100),
    name VARCHAR(255),
    link VARCHAR(500),
    FOREIGN KEY (developer_id) REFERENCES developer_info(id)
);

-- Личные качества
CREATE TABLE qualities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    developer_id INT,
    description TEXT,
    FOREIGN KEY (developer_id) REFERENCES developer_info(id)
);

-- Языки
CREATE TABLE languages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    developer_id INT,
    description TEXT,
    FOREIGN KEY (developer_id) REFERENCES developer_info(id)
);