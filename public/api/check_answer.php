<?php
/**
 * api/check_answer.php — Endpoint AJAX (POST) para verificar una respuesta.
 *
 * Delega toda la lógica a LessonController::checkAnswer(), que valida
 * los parámetros, consulta la BD y guarda el progreso del usuario.
 *
 * Parámetros POST requeridos:
 *   - exercise_id        (int) ID del ejercicio respondido
 *   - selected_option_id (int) ID de la opción seleccionada por el usuario
 *
 * Responde JSON: { success, correct, correctOptionId, feedback, explanation }
 */
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/helpers.php';
require_once __DIR__ . '/../../src/models/Progress.php';
require_once __DIR__ . '/../../src/controllers/LessonController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

LessonController::checkAnswer();
