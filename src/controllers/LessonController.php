<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Module.php';
require_once __DIR__ . '/../models/Lesson.php';
require_once __DIR__ . '/../models/Progress.php';
require_once __DIR__ . '/../helpers.php';

class LessonController {

    /**
     * Carga y muestra la vista de una lección con sus ejercicios.
     *
     * Valida que la lección pertenezca al módulo indicado para evitar
     * acceso a lecciones de otros módulos manipulando la URL.
     *
     * Calcula la navegación anterior/siguiente dentro del módulo y determina
     * si es la última lección (para mostrar el card del siguiente módulo).
     *
     * Una lección se considera completada cuando todos sus ejercicios
     * tienen completado = 1 en la tabla progreso para ese usuario.
     *
     * @param int $moduleId ID del módulo al que pertenece la lección.
     * @param int $lessonId ID de la lección a mostrar.
     */
    public static function show(int $moduleId, int $lessonId): void {
        require_auth();

        $userId = $_SESSION['user_id'];
        $module = Module::find($moduleId);
        $lesson = Lesson::find($lessonId);

        if (!$module || !$lesson || (int)$lesson['module_id'] !== $moduleId) {
            render_error(404, 'Lección no encontrada', base_url('index.php?page=dashboard'), 'Volver al dashboard');
        }

        $exercises = Lesson::exercises($lessonId);

        // Obtener tipo y URL del recurso multimedia de la lección
        $stmtMedia = getDB()->prepare('SELECT tipo, url FROM contenido WHERE id_contenido = ? LIMIT 1');
        $stmtMedia->execute([$lessonId]);
        $lessonMedia = $stmtMedia->fetch() ?: ['tipo' => 'texto', 'url' => null];

        $exerciseStatus = [];
        foreach ($exercises as $ex) {
            $exerciseStatus[$ex['id']] = Progress::exerciseCompleted($userId, $ex['id']);
        }
        $completed = !empty($exerciseStatus) && !in_array(false, $exerciseStatus, true);

        $allLessons = Lesson::byModule($moduleId);
        $ids        = array_column($allLessons, 'id');
        $currentIdx = array_search($lessonId, $ids);
        $prevLesson = $currentIdx > 0 ? $allLessons[$currentIdx - 1] : null;
        $nextLesson = isset($allLessons[$currentIdx + 1]) ? $allLessons[$currentIdx + 1] : null;
        $lessonNum  = (int)$currentIdx + 1;
        $totalCount = count($allLessons);

        $nextModule = $nextLesson === null ? Module::next($moduleId) : null;

        // El mensaje "módulo completado" solo aparece si es la última lección
        // Y además TODOS los ejercicios del módulo completo están respondidos
        $moduleCompleted = false;
        if ($nextLesson === null) {
            $totalModuleEx     = Progress::totalExercises($moduleId);
            $completedModuleEx = Progress::countCompleted($userId, $moduleId);
            $moduleCompleted   = $totalModuleEx > 0 && $completedModuleEx >= $totalModuleEx;
        }

        require __DIR__ . '/../views/lesson.php';
    }

    /**
     * Endpoint AJAX (POST): verifica la respuesta de un ejercicio y guarda el progreso.
     *
     * Recibe exercise_id y selected_option_id. Carga todas las opciones del ejercicio
     * desde la BD y determina si la opción seleccionada es la correcta comparando IDs,
     * no índices posicionales (las opciones se mezclan en el cliente).
     *
     * Responde JSON con:
     * - success (bool), correct (bool), correctOptionId (int),
     *   feedback (string), explanation (string)
     */
    public static function checkAnswer(): void {
        require_auth();
        header('Content-Type: application/json');

        $exerciseId       = (int)($_POST['exercise_id']        ?? 0);
        $selectedOptionId = (int)($_POST['selected_option_id'] ?? 0);
        $isCodeExercise   = !empty($_POST['is_code_exercise']);
        $userId           = $_SESSION['user_id'];

        if ($exerciseId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
            exit;
        }

        // Ejercicio de código: el cliente ya verificó la respuesta, solo guardamos progreso
        if ($isCodeExercise) {
            Progress::markExercise($userId, $exerciseId, true);
            echo json_encode(['success' => true, 'correct' => true]);
            exit;
        }

        if ($selectedOptionId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
            exit;
        }

        $stmt = getDB()->prepare(
            'SELECT id_opcion, texto, es_correcta, retroalimentacion
             FROM opcion WHERE id_ejercicio = ? ORDER BY id_opcion ASC'
        );
        $stmt->execute([$exerciseId]);
        $options = $stmt->fetchAll();

        if (empty($options)) {
            echo json_encode(['success' => false, 'message' => 'Ejercicio no encontrado']);
            exit;
        }

        $correctOptionId  = 0;
        $selectedFeedback = '';

        foreach ($options as $opt) {
            if ((int)$opt['es_correcta'] === 1) {
                $correctOptionId = (int)$opt['id_opcion'];
            }
            if ((int)$opt['id_opcion'] === $selectedOptionId) {
                $selectedFeedback = $opt['retroalimentacion'] ?? '';
            }
        }

        $isCorrect = ($selectedOptionId === $correctOptionId);

        $stmtEx = getDB()->prepare('SELECT retroalimentacion FROM ejercicios WHERE id_ejercicio = ? LIMIT 1');
        $stmtEx->execute([$exerciseId]);
        $exRow       = $stmtEx->fetch();
        $explanation = $exRow ? $exRow['retroalimentacion'] : '';

        Progress::markExercise($userId, $exerciseId, $isCorrect);

        echo json_encode([
            'success'         => true,
            'correct'         => $isCorrect,
            'correctOptionId' => $correctOptionId,
            'feedback'        => $selectedFeedback ?: ($isCorrect ? '¡Correcto!' : 'Respuesta incorrecta.'),
            'explanation'     => $explanation,
        ]);
        exit;
    }

    /**
     * Endpoint AJAX legacy mantenido por compatibilidad con versiones anteriores.
     * No realiza ninguna operación real.
     */
    public static function markComplete(): void {
        require_auth();
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
}
