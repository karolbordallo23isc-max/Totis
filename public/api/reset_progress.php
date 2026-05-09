<?php
/**
 * api/reset_progress.php — Endpoint AJAX (POST) para reiniciar progreso.
 *
 * Acepta dos modos según el campo `type`:
 *   - 'lesson': elimina el progreso del usuario en los ejercicios de una lección.
 *   - 'module': elimina el progreso del usuario en todos los ejercicios de un módulo.
 *
 * Parámetros POST requeridos:
 *   - type      (string) 'lesson' | 'module'
 *   - module_id (int)    ID del módulo
 *   - lesson_id (int)    ID de la lección (solo requerido si type = 'lesson')
 *
 * Responde JSON: { success: true } o { success: false, message: string }
 */
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/helpers.php';
require_once __DIR__ . '/../../src/models/Lesson.php';
require_once __DIR__ . '/../../src/models/Progress.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

require_auth();

$userId   = $_SESSION['user_id'];
$type     = $_POST['type']      ?? '';
$moduleId = (int)($_POST['module_id'] ?? 0);
$lessonId = (int)($_POST['lesson_id'] ?? 0);

if ($type === 'lesson' && $lessonId > 0 && $moduleId > 0) {
    $exercises   = Lesson::exercises($lessonId);
    $exerciseIds = array_column($exercises, 'id');
    Progress::resetLesson($userId, $exerciseIds);
    echo json_encode(['success' => true]);

} elseif ($type === 'module' && $moduleId > 0) {
    Progress::resetModule($userId, $moduleId);
    echo json_encode(['success' => true]);

} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
}
exit;
