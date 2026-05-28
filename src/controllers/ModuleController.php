<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Module.php';
require_once __DIR__ . '/../models/Lesson.php';
require_once __DIR__ . '/../models/Progress.php';
require_once __DIR__ . '/../helpers.php';

class ModuleController {

    /**
     * Carga y muestra la vista de un módulo con su lista de lecciones.
     *
     * Para cada lección determina si está completada: una lección se considera
     * completada solo cuando TODOS sus ejercicios tienen completado = 1 en la
     * tabla progreso. Las lecciones sin ejercicios asignados se marcan como
     * no completadas.
     *
     * Pasa a la vista: $module, $lessons (con clave 'completed' inyectada),
     * $progressPct, $totalLessons, $completed, $nextModule.
     *
     * @param int $moduleId ID del módulo a mostrar.
     */
    public static function show(int $moduleId): void {
        require_auth();

        $userId = $_SESSION['user_id'];
        $module = Module::find($moduleId);

        if (!$module) {
            render_error(404, 'Módulo no encontrado', base_url('index.php?page=dashboard'), 'Volver al dashboard');
        }

        // Validación secuencial: verificar que el módulo esté desbloqueado
        if (!Module::isUnlocked($userId, $moduleId)) {
            $_SESSION['admin_error'] = '🔒 Debes completar el módulo anterior antes de acceder a este.';
            redirect(base_url('index.php?page=dashboard'));
        }

        $lessons     = Lesson::byModule($moduleId);
        $totalEx     = Progress::totalExercises($moduleId);
        $completedEx = Progress::countCompleted($userId, $moduleId);
        $progressPct = $totalEx > 0 ? round(($completedEx / $totalEx) * 100) : 0;

        $totalLessons = count($lessons);
        $completed    = $completedEx;
        $nextModule   = Module::next($moduleId);

        foreach ($lessons as &$lesson) {
            $lessonExercises = Lesson::exercises((int)$lesson['id']);
            if (empty($lessonExercises)) {
                $lesson['completed'] = false;
                continue;
            }
            $allDone = true;
            foreach ($lessonExercises as $ex) {
                if (!Progress::exerciseCompleted($userId, (int)$ex['id'])) {
                    $allDone = false;
                    break;
                }
            }
            $lesson['completed'] = $allDone;
        }
        unset($lesson);

        require __DIR__ . '/../views/module.php';
    }
}
