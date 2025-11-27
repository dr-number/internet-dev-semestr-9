-- Вставка основной информации
INSERT INTO developer_info (
    title, format_file_resume, name, date_born, citizenship, 
    career_objective, location, salary, employment, schedule, 
    time_to_work, level_education
) VALUES (
    'Web_Developer_Backend',
    'pdf',
    'ФИО',
    '1980-04-25',
    'Россия',
    'Web разработчик (backend, django)',
    'Адрес',
    'минимум +1005000000000 руб.',
    'полная',
    'удаленная работа, полный день',
    'не более часа',
    'Среднее'
);

SET @dev_id = LAST_INSERT_ID();

-- Специализации
INSERT INTO specializations (developer_id, name) VALUES
(@dev_id, 'Разработчик, программист');

-- Контакты
INSERT INTO contacts (developer_id, email, phone) VALUES
(@dev_id, 'email@yandex.ru', '8 XXX XXX XX-XX');

SET @contact_id = LAST_INSERT_ID();

-- Ссылки на соцсети
INSERT INTO contact_links (contact_id, name, icon, link) VALUES
(@contact_id, 'Telegram', 'fab fa-telegram-plane', 'https://t.me/XXXXXX'),
(@contact_id, 'Whatsapp', 'fab fa-whatsapp', 'https://wa.me/XXXXXXXXXX');

-- Навыки и поднавыки (исправленная версия)
INSERT INTO skills (developer_id, name, percentage) VALUES
(@dev_id, 'Python', 40);

SET @python_id = LAST_INSERT_ID();

INSERT INTO skills (developer_id, name, percentage) VALUES
(@dev_id, 'Работа с Legacy-кодом', 45),
(@dev_id, 'Docker; Docker Compose', 25),
(@dev_id, 'Базы данных', 30);

SET @db_id = LAST_INSERT_ID();

INSERT INTO skills (developer_id, name, percentage) VALUES
(@dev_id, 'Автоматизация задач', 35);

SET @auto_id = LAST_INSERT_ID();

INSERT INTO skills (developer_id, name, percentage) VALUES
(@dev_id, 'Java; Kotlin', 15),
(@dev_id, 'Js', 15);

-- Поднавыки
INSERT INTO sub_skills (skill_id, name, percentage) VALUES
(@python_id, 'Django', 30),
(@python_id, 'Celery', 25),
(@python_id, 'Redis', 25),
(@db_id, 'PostgreSQL', 25),
(@db_id, 'MySQL', 20),
(@auto_id, 'Bash-скрипты', 15);

-- Проекты
INSERT INTO projects (developer_id, name, link) VALUES
(@dev_id, 'Работа с API платежной системы Stripe (Python Django)', 'https://github.com/xxxxxxxx.git'),
(@dev_id, 'Погодный telegram bot (Python)', 'https://github.com/xxxxxxx/MyWeather_telegram_bot'),
(@dev_id, 'REST API (Python Flask)', 'https://github.com/xxxxxxx/Base_flask_rest_API.git'),
(@dev_id, 'Тестирование websocket (Python Django)', 'https://gitlab.com/dr.number/python_docker_django_test.git');

-- Достижения
INSERT INTO achievements (developer_id, description) VALUES
(@dev_id, 'Оптимизировал скорость генерации документов примерно в 10 раз (с ~30 сек. до ~3 сек.)'),
(@dev_id, 'Осужествил интеграцию backend с брокерскими системами "Tradernet by freedom finance", "T‑Bank Invest API", "Finam Trade API"'),
(@dev_id, 'Осужествил интеграцию backend с платежной системой "Robokassa"'),
(@dev_id, 'Разработал систему подписания документов'),
(@dev_id, 'Разработал мини-систему защиты от DDoS-атак'),
(@dev_id, 'Осуществил интеграцию с системой расслки смс "GreenSMS"'),
(@dev_id, 'Разрабатывал backend для мобильного приложения'),
(@dev_id, 'Разработал систему автоматического формирования статистики (отчетов) из БД в форматы xlsx'),
(@dev_id, 'Объединил в единую систему 3 продукта'),
(@dev_id, 'Осужествил интеграцию Django с системой двухфакторной аутентификации (OTP)'),
(@dev_id, 'Отладка django бэкенда/celery/telegram ботов/jupiter-notebook скриптов с помощью debugpy, в том числе в docker контейнере'),
(@dev_id, 'Автоматизировал обновление SSL сертификатов (bash/crontab)'),
(@dev_id, 'Принимал участие в найме (тестировании) backend разработчиков');

-- Опыт работы
INSERT INTO work_experience (developer_id, company, job_title, time_period) VALUES
(@dev_id, 'Компани нэйм', 'Web-разработчик; Android-разработчик', 'Июнь 2018 — Февраль 2020');

SET @work_id = LAST_INSERT_ID();

INSERT INTO work_responsibilities (work_experience_id, description) VALUES
(@work_id, 'Разработка мобильного приложения и сервера связанного с ним.'),
(@work_id, 'Создание сайтов, работа с CMS (дополнительно).');

-- Курсы
INSERT INTO courses (developer_id, name, description, time_period) VALUES
(@dev_id, '1C Предприятие. Управление торговлей', 'ГБПОУ', '2021'),
(@dev_id, 'SkyEng', 'SkyEng', '2020');

-- Репозитории
INSERT INTO repositories (developer_id, icon, name, link) VALUES
(@dev_id, 'fab fa-github', 'github.com/xxxxxx', 'https://github.com/xxxxx'),
(@dev_id, 'fab fa-gitlab', 'gitlab.com/xxxxxxx', 'https://gitlab.com/xxxxxx');

-- Хобби
INSERT INTO hobbies (developer_id, icon, name, link) VALUES
(@dev_id, 'fas fa-cube', '3d моделирование', 'https://www.youtube.com/channel/Uxxxxxxxxxxqlw');

-- Личные качества
INSERT INTO qualities (developer_id, description) VALUES
(@dev_id, 'Ответственность.'),
(@dev_id, 'Пунктуальность.'),
(@dev_id, 'Трудолюбие.'),
(@dev_id, 'Обуаемость.'),
(@dev_id, 'Нацеленность на результат.');

-- Языки
INSERT INTO languages (developer_id, description) VALUES
(@dev_id, 'Английский на уровне чтения технической документации');