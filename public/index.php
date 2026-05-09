<?php
/**
 * public/index.php — Punto de entrada único del sistema (Front Controller).
 *
 * Todas las peticiones pasan por aquí. El parámetro GET `page` determina
 * qué controller se ejecuta. Las rutas públicas (login, register) redirigen
 * al dashboard si ya existe una sesión activa.
 */
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/models/User.php';
require_once __DIR__ . '/../src/models/Module.php';
require_once __DIR__ . '/../src/models/Lesson.php';
require_once __DIR__ . '/../src/models/Progress.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/controllers/DashboardController.php';
require_once __DIR__ . '/../src/controllers/ModuleController.php';
require_once __DIR__ . '/../src/controllers/LessonController.php';
require_once __DIR__ . '/../src/controllers/ProfileController.php';

$page = $_GET['page'] ?? '';

if (in_array($page, ['login', 'register', ''])) {
    if (!empty($_SESSION['user_id'])) {
        redirect(base_url('index.php?page=dashboard'));
    }
}

switch ($page) {

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            AuthController::handleLogin();
        } else {
            AuthController::showLogin();
        }
        break;

    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            AuthController::handleRegister();
        } else {
            AuthController::showRegister();
        }
        break;

    case 'dashboard':
        DashboardController::show();
        break;

    case 'module':
        $moduleId = (int)($_GET['id'] ?? 0);
        if ($moduleId <= 0) {
            redirect(base_url('index.php?page=dashboard'));
        }
        ModuleController::show($moduleId);
        break;

    case 'lesson':
        $moduleId = (int)($_GET['module_id'] ?? 0);
        $lessonId = (int)($_GET['lesson_id'] ?? 0);
        if ($moduleId <= 0 || $lessonId <= 0) {
            redirect(base_url('index.php?page=dashboard'));
        }
        LessonController::show($moduleId, $lessonId);
        break;

    case 'profile':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ProfileController::handleUpdate();
        } else {
            ProfileController::show();
        }
        break;

    case 'logout':
        AuthController::logout();
        break;

    default:
        if (!empty($_SESSION['user_id'])) {
            redirect(base_url('index.php?page=dashboard'));
        } else {
            redirect(base_url('index.php?page=login'));
        }
        break;
}
