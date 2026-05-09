<?php
/**
 * api/module_preview.php — Endpoint AJAX (GET) para el tooltip de módulos.
 *
 * Devuelve el nombre del módulo y la lista de sus lecciones con el estado
 * de completado de cada una para el usuario autenticado. Se usa en el
 * dashboard para mostrar una vista previa al pasar el cursor sobre una tarjeta.
 *
 * Parámetros GET requeridos:
 *   - id (int) ID del módulo
 *
 * Responde JSON: { moduleName: string, lessons: [{ title, completed }] }
 */
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/helpers.php';
require_once __DIR__ . '/../../src/models/Module.php';
require_once __DIR__ . '/../../src/models/Lesson.php';
require_once __DIR__ . '/../../src/models/Progress.php';

header('Content-Type: application/json');
require_auth();

$moduleId = (int)($_GET['id'] ?? 0);
if ($moduleId <= 0) {
    echo json_encode(['error' => 'invalid']);
    exit;
}

$module  = Module::find($moduleId);
$lessons = Lesson::byModule($moduleId);
$userId  = $_SESSION['user_id'];

$result = [];
foreach ($lessons as $lesson) {
    $exercises = Lesson::exercises((int)$lesson['id']);
    $done      = true;
    foreach ($exercises as $ex) {
        if (!Progress::exerciseCompleted($userId, (int)$ex['id'])) {
            $done = false;
            break;
        }
    }
    $result[] = ['title' => $lesson['title'], 'completed' => $done];
}

echo json_encode(['moduleName' => $module['nombre'], 'lessons' => $result]);
