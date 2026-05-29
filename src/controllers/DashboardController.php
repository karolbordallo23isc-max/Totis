<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/Module.php';
require_once __DIR__ . '/../models/Progress.php';
require_once __DIR__ . '/../helpers.php';

class DashboardController {

    /**
     * Carga y muestra el dashboard principal del usuario.
     *
     * Para cada módulo calcula el porcentaje de progreso individual:
     *   porcentaje = (ejercicios completados / total de ejercicios del módulo) × 100
     *
     * El progreso global es el promedio de los porcentajes de todos los módulos.
     * Pasa a la vista: $modules, $progressData (indexado por id_modulo), $overallProgress.
     */
    public static function show(): void {
        require_auth();

        $userId  = $_SESSION['user_id'];
        $modules = Module::allWithStatus($userId);

        $progressData  = [];
        $totalProgress = 0;

        foreach ($modules as $module) {
            $moduleId           = (int)$module['id_modulo'];
            $totalExercises     = Progress::totalExercises($moduleId);
            $completedExercises = Progress::countCompleted($userId, $moduleId);
            $percent            = $totalExercises > 0 ? round(($completedExercises / $totalExercises) * 100) : 0;

            $progressData[$moduleId] = [
                'total'     => $totalExercises,
                'completed' => $completedExercises,
                'percent'   => $percent,
                'unlocked'  => $module['unlocked'],
            ];
            $totalProgress += $percent;
        }

        $overallProgress = count($modules) > 0 ? round($totalProgress / count($modules)) : 0;

        require __DIR__ . '/../views/dashboard.php';
    }
}
