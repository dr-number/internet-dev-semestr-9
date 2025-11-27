<?php
// Включение/выключение ошибок
if (true) {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Параметры подключения из переменных окружения
$mysql_host = "db"; // Используем имя сервиса из docker-compose
$mysql_user = getenv('MYSQL_USER');
$mysql_password = getenv('MYSQL_PASSWORD');
$mysql_database = getenv('MYSQL_DATABASE');

if (!$mysql_user || !$mysql_password || !$mysql_database) {
    die("Error: Failed to get environment variables");
}

// functions.php - функции для работы с БД
function getDBConnection() {
    global $mysql_host, $mysql_user, $mysql_password, $mysql_database;
    
    $dsn = "mysql:host=" . $mysql_host . ";dbname=" . $mysql_database . ";charset=utf8";
    try {
        $pdo = new PDO($dsn, $mysql_user, $mysql_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        die("Ошибка подключения: " . $e->getMessage());
    }
}

function getDeveloperInfo($pdo) {
    $stmt = $pdo->query("SELECT * FROM developer_info LIMIT 1");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getSpecializations($pdo, $devId) {
    $stmt = $pdo->prepare("SELECT name FROM specializations WHERE developer_id = ?");
    $stmt->execute(array($devId));
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function getContacts($pdo, $devId) {
    $stmt = $pdo->prepare("SELECT c.* FROM contacts c WHERE c.developer_id = ?");
    $stmt->execute(array($devId));
    $contacts = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($contacts) {
        $stmt = $pdo->prepare("SELECT name, icon, link FROM contact_links WHERE contact_id = ?");
        $stmt->execute(array($contacts['id']));
        $contacts['hrefs'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    return $contacts;
}

function getSkills($pdo, $devId) {
    $stmt = $pdo->prepare("
        SELECT s.id, s.name, s.percentage 
        FROM skills s 
        WHERE s.developer_id = ?
        ORDER BY s.percentage DESC
    ");
    $stmt->execute(array($devId));
    $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($skills as &$skill) {
        $stmt = $pdo->prepare("SELECT name, percentage FROM sub_skills WHERE skill_id = ?");
        $stmt->execute(array($skill['id']));
        $subSkills = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($subSkills) {
            $skill['sub_technologes'] = $subSkills;
        }
    }
    
    return $skills;
}

function getProjects($pdo, $devId) {
    $stmt = $pdo->prepare("SELECT name, link FROM projects WHERE developer_id = ?");
    $stmt->execute(array($devId));
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAchievements($pdo, $devId) {
    $stmt = $pdo->prepare("SELECT description FROM achievements WHERE developer_id = ?");
    $stmt->execute(array($devId));
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getWorkExperience($pdo, $devId) {
    $stmt = $pdo->prepare("
        SELECT we.id, we.company, we.job_title, we.time_period 
        FROM work_experience we 
        WHERE we.developer_id = ?
    ");
    $stmt->execute(array($devId));
    $work = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($work as &$job) {
        $stmt = $pdo->prepare("
            SELECT wr.description 
            FROM work_responsibilities wr 
            WHERE wr.work_experience_id = ?
        ");
        $stmt->execute(array($job['id']));
        $responsibilities = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        $job['responsibilities'] = $responsibilities;
    }
    
    return $work;
}

function getCourses($pdo, $devId) {
    $stmt = $pdo->prepare("SELECT name, description, time_period FROM courses WHERE developer_id = ?");
    $stmt->execute(array($devId));
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getRepositories($pdo, $devId) {
    $stmt = $pdo->prepare("SELECT icon, name, link FROM repositories WHERE developer_id = ?");
    $stmt->execute(array($devId));
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getHobbies($pdo, $devId) {
    $stmt = $pdo->prepare("SELECT icon, name, link FROM hobbies WHERE developer_id = ?");
    $stmt->execute(array($devId));
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getQualities($pdo, $devId) {
    $stmt = $pdo->prepare("SELECT description FROM qualities WHERE developer_id = ?");
    $stmt->execute(array($devId));
    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

function getLanguages($pdo, $devId) {
    $stmt = $pdo->prepare("SELECT description FROM languages WHERE developer_id = ?");
    $stmt->execute(array($devId));
    return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
}

// Основная логика страницы
try {
    $pdo = getDBConnection();
    $developer = getDeveloperInfo($pdo);
    
    if (!$developer) {
        die("Данные разработчика не найдены в базе данных");
    }
    
    $devId = $developer['id'];

    $data = array(
        'specializations' => getSpecializations($pdo, $devId),
        'contacts' => getContacts($pdo, $devId),
        'skills' => getSkills($pdo, $devId),
        'projects' => getProjects($pdo, $devId),
        'achievements' => getAchievements($pdo, $devId),
        'work_experience' => getWorkExperience($pdo, $devId),
        'courses' => getCourses($pdo, $devId),
        'repositories' => getRepositories($pdo, $devId),
        'hobby' => getHobbies($pdo, $devId),
        'qualities' => getQualities($pdo, $devId),
        'language' => getLanguages($pdo, $devId)
    );
} catch (Exception $e) {
    die("Ошибка при загрузке данных: " . $e->getMessage());
}

// Функция для безопасного вывода данных
function safeEcho($value, $default = '') {
    if (isset($value)) {
        return htmlspecialchars($value);
    }
    return htmlspecialchars($default);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo safeEcho(isset($developer['title']) ? $developer['title'] : 'Резюме разработчика'); ?> - Резюме</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px 0; }
        .header-content { text-align: center; }
        .name { font-size: 2.5em; margin-bottom: 10px; }
        .title { font-size: 1.3em; opacity: 0.9; }
        .section { background: white; margin: 20px 0; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .section-title { color: #667eea; margin-bottom: 20px; font-size: 1.5em; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
        .contact-info { display: flex; justify-content: center; flex-wrap: wrap; gap: 20px; margin: 20px 0; }
        .contact-item { display: flex; align-items: center; gap: 10px; }
        .skills-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .skill-item { margin-bottom: 15px; }
        .skill-name { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .skill-bar { background: #e0e0e0; border-radius: 10px; overflow: hidden; }
        .skill-level { background: #667eea; height: 8px; border-radius: 10px; transition: width 0.5s ease; }
        .project-list, .achievement-list, .qualities-list { list-style: none; }
        .project-item, .achievement-item, .quality-item { margin-bottom: 10px; padding-left: 20px; position: relative; }
        .project-item:before, .achievement-item:before, .quality-item:before { content: "▸"; position: absolute; left: 0; color: #667eea; }
        .social-links { display: flex; gap: 15px; justify-content: center; margin-top: 20px; flex-wrap: wrap; }
        .social-link { color: #667eea; font-size: 1.5em; transition: color 0.3s; text-decoration: none; display: flex; align-items: center; gap: 8px; }
        .social-link:hover { color: #764ba2; }
        .social-link span { font-size: 0.8em; }
        .work-item, .course-item { margin-bottom: 20px; padding: 15px; border-left: 3px solid #667eea; background: #f9f9f9; }
        .company { font-weight: bold; color: #667eea; }
        .job-title { font-style: italic; margin: 5px 0; }
        .time-period { color: #666; font-size: 0.9em; }
        .responsibilities { margin-top: 10px; padding-left: 20px; }
        .responsibilities li { margin-bottom: 5px; }
        
        @media (max-width: 768px) {
            .contact-info { flex-direction: column; align-items: center; }
            .skills-grid { grid-template-columns: 1fr; }
            .social-links { flex-direction: column; align-items: center; }
            .name { font-size: 2em; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="header-content">
                <h1 class="name"><?php echo safeEcho($developer['name']); ?></h1>
                <div class="title"><?php echo safeEcho($developer['career_objective']); ?></div>
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span><?php echo safeEcho(isset($data['contacts']['email']) ? $data['contacts']['email'] : ''); ?></span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span><?php echo safeEcho(isset($data['contacts']['phone']) ? $data['contacts']['phone'] : ''); ?></span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo safeEcho($developer['location']); ?></span>
                    </div>
                </div>
                <?php if (!empty($data['contacts']['hrefs'])): ?>
                <div class="social-links">
                    <?php foreach ($data['contacts']['hrefs'] as $link): ?>
                        <a href="<?php echo safeEcho($link['link']); ?>" class="social-link" target="_blank" title="<?php echo safeEcho($link['name']); ?>">
                            <i class="<?php echo safeEcho($link['icon']); ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- О себе -->
        <div class="section">
            <h2 class="section-title">О себе</h2>
            <p><strong>Гражданство:</strong> <?php echo safeEcho($developer['citizenship']); ?></p>
            <p><strong>Дата рождения:</strong> <?php echo date('d.m.Y', strtotime($developer['date_born'])); ?></p>
            <p><strong>Образование:</strong> <?php echo safeEcho($developer['level_education']); ?></p>
            <p><strong>Занятость:</strong> <?php echo safeEcho($developer['employment']); ?></p>
            <p><strong>График работы:</strong> <?php echo safeEcho($developer['schedule']); ?></p>
        </div>

        <!-- Навыки -->
        <?php if (!empty($data['skills'])): ?>
        <div class="section">
            <h2 class="section-title">Навыки</h2>
            <div class="skills-grid">
                <?php foreach ($data['skills'] as $skill): ?>
                    <div class="skill-item">
                        <div class="skill-name">
                            <span><?php echo safeEcho($skill['name']); ?></span>
                            <span><?php echo $skill['percentage']; ?>%</span>
                        </div>
                        <div class="skill-bar">
                            <div class="skill-level" style="width: <?php echo $skill['percentage']; ?>%"></div>
                        </div>
                        <?php if (isset($skill['sub_technologes'])): ?>
                            <?php foreach ($skill['sub_technologes'] as $sub): ?>
                                <div style="margin-left: 20px; margin-top: 10px;">
                                    <div class="skill-name">
                                        <span><?php echo safeEcho($sub['name']); ?></span>
                                        <span><?php echo $sub['percentage']; ?>%</span>
                                    </div>
                                    <div class="skill-bar">
                                        <div class="skill-level" style="width: <?php echo $sub['percentage']; ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Проекты -->
        <?php if (!empty($data['projects'])): ?>
        <div class="section">
            <h2 class="section-title">Проекты</h2>
            <ul class="project-list">
                <?php foreach ($data['projects'] as $project): ?>
                    <li class="project-item">
                        <a href="<?php echo safeEcho($project['link']); ?>" target="_blank">
                            <?php echo safeEcho($project['name']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Достижения -->
        <?php if (!empty($data['achievements'])): ?>
        <div class="section">
            <h2 class="section-title">Достижения</h2>
            <ul class="achievement-list">
                <?php foreach ($data['achievements'] as $achievement): ?>
                    <li class="achievement-item"><?php echo safeEcho($achievement['description']); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Опыт работы -->
        <?php if (!empty($data['work_experience'])): ?>
        <div class="section">
            <h2 class="section-title">Опыт работы</h2>
            <?php foreach ($data['work_experience'] as $work): ?>
                <div class="work-item">
                    <div class="company"><?php echo safeEcho($work['company']); ?></div>
                    <div class="job-title"><?php echo safeEcho($work['job_title']); ?></div>
                    <div class="time-period"><?php echo safeEcho($work['time_period']); ?></div>
                    <?php if (!empty($work['responsibilities'])): ?>
                    <ul class="responsibilities">
                        <?php foreach ($work['responsibilities'] as $resp): ?>
                            <li><?php echo safeEcho($resp); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Курсы -->
        <?php if (!empty($data['courses'])): ?>
        <div class="section">
            <h2 class="section-title">Курсы</h2>
            <?php foreach ($data['courses'] as $course): ?>
                <div class="course-item">
                    <div class="course-name"><strong><?php echo safeEcho($course['name']); ?></strong></div>
                    <div class="course-description"><?php echo safeEcho($course['description']); ?></div>
                    <div class="time-period"><?php echo safeEcho($course['time_period']); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Личные качества -->
        <?php if (!empty($data['qualities'])): ?>
        <div class="section">
            <h2 class="section-title">Личные качества</h2>
            <ul class="qualities-list">
                <?php foreach ($data['qualities'] as $quality): ?>
                    <li class="quality-item"><?php echo safeEcho($quality); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Языки -->
        <?php if (!empty($data['language'])): ?>
        <div class="section">
            <h2 class="section-title">Языки</h2>
            <ul class="qualities-list">
                <?php foreach ($data['language'] as $lang): ?>
                    <li class="quality-item"><?php echo safeEcho($lang); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Хобби -->
        <?php if (!empty($data['hobby'])): ?>
        <div class="section">
            <h2 class="section-title">Хобби</h2>
            <div class="social-links">
                <?php foreach ($data['hobby'] as $hobby): ?>
                    <a href="<?php echo safeEcho($hobby['link']); ?>" class="social-link" target="_blank">
                        <i class="<?php echo safeEcho($hobby['icon']); ?>"></i>
                        <span><?php echo safeEcho($hobby['name']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Репозитории -->
        <?php if (!empty($data['repositories'])): ?>
        <div class="section">
            <h2 class="section-title">Репозитории</h2>
            <div class="social-links">
                <?php foreach ($data['repositories'] as $repo): ?>
                    <a href="<?php echo safeEcho($repo['link']); ?>" class="social-link" target="_blank">
                        <i class="<?php echo safeEcho($repo['icon']); ?>"></i>
                        <span><?php echo safeEcho($repo['name']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Анимация прогресс-баров при скролле
        document.addEventListener('DOMContentLoaded', function() {
            var skillLevels = document.querySelectorAll('.skill-level');
            
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var skillLevel = entry.target;
                        var width = skillLevel.style.width;
                        skillLevel.style.width = '0%';
                        setTimeout(function() {
                            skillLevel.style.width = width;
                        }, 100);
                    }
                });
            }, { threshold: 0.5 });
            
            for (var i = 0; i < skillLevels.length; i++) {
                observer.observe(skillLevels[i]);
            }
        });
    </script>
</body>
</html>
