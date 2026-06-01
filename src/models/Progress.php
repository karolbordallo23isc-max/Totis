<?php
require_once __DIR__ . '/../../config/database.php';

class Progress {

    /**
     * Registra o actualiza el intento de un usuario en un ejercicio.
     *
     * Si ya existe un registro para ese par (usuario, ejercicio), incrementa
     * el contador de intentos y actualiza la calificación y el estado.
     * Usa ON DUPLICATE KEY UPDATE para garantizar un único registro por par.
     *
     * Calificación: 100.00 si correcto, 0.00 si incorrecto.
     *
     * @param int  $userId     ID del usuario autenticado.
     * @param int  $exerciseId ID del ejercicio respondido.
     * @param bool $correct    true si la respuesta seleccionada es la correcta.
     */
    public static function markExercise(int $userId, int $exerciseId, bool $correct): void {
        $calificacion = $correct ? 100.00 : 0.00;
        $completed    = $correct ? 1 : 0;

        $stmt = getDB()->prepare(
            'INSERT INTO progreso (id_usuario, id_ejercicio, intentos, calificacion, completado, alguna_vez_correcto, fecha_progreso)
             VALUES (?, ?, 1, ?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE
               intentos             = intentos + 1,
               calificacion         = ?,
               completado           = ?,
               alguna_vez_correcto  = IF(? = 1, 1, alguna_vez_correcto),
               fecha_progreso       = NOW()'
        );
        $stmt->execute([$userId, $exerciseId, $calificacion, $completed, $completed,
                        $calificacion, $completed, $completed]);
    }

    /**
     * Cuenta cuántos ejercicios de un módulo completó correctamente el usuario.
     * Un ejercicio se considera completado cuando `completado = 1` en la tabla progreso.
     *
     * @param int $userId   ID del usuario.
     * @param int $moduleId ID del módulo.
     * @return int Número de ejercicios completados correctamente.
     */
    public static function countCompleted(int $userId, int $moduleId): int {
        $stmt = getDB()->prepare(
            'SELECT COUNT(*) FROM progreso p
             JOIN ejercicios e ON e.id_ejercicio = p.id_ejercicio
             WHERE p.id_usuario = ? AND e.id_modulo = ? AND p.completado = 1'
        );
        $stmt->execute([$userId, $moduleId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Devuelve el total de ejercicios que tiene un módulo.
     * Se usa junto con countCompleted() para calcular el porcentaje de progreso.
     *
     * @param int $moduleId ID del módulo.
     * @return int Total de ejercicios del módulo.
     */
    public static function totalExercises(int $moduleId): int {
        $stmt = getDB()->prepare('SELECT COUNT(*) FROM ejercicios WHERE id_modulo = ?');
        $stmt->execute([$moduleId]);
        return (int)$stmt->fetchColumn();
    }

    /** Total de intentos del usuario en un módulo (suma de todos los intentos de todos los ejercicios) */
    public static function totalAttempts(int $userId, int $moduleId): int {
        $stmt = getDB()->prepare(
            'SELECT COALESCE(SUM(p.intentos), 0)
             FROM progreso p
             JOIN ejercicios e ON e.id_ejercicio = p.id_ejercicio
             WHERE p.id_usuario = ? AND e.id_modulo = ?'
        );
        $stmt->execute([$userId, $moduleId]);
        return (int)$stmt->fetchColumn();
    }

    /** Cuántas veces el usuario ha completado el módulo al 100% históricamente */
    public static function timesCompleted(int $userId, int $moduleId): int {
        // Aproximación: cuántos ejercicios del módulo tienen alguna_vez_correcto=1
        $stmt = getDB()->prepare(
            'SELECT COUNT(*) FROM progreso p
             JOIN ejercicios e ON e.id_ejercicio = p.id_ejercicio
             WHERE p.id_usuario = ? AND e.id_modulo = ? AND p.alguna_vez_correcto = 1'
        );
        $stmt->execute([$userId, $moduleId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Verifica si un ejercicio específico fue respondido correctamente por el usuario.
     * Consulta directamente el campo `completado` en la tabla progreso.
     *
     * @param int $userId     ID del usuario.
     * @param int $exerciseId ID del ejercicio.
     * @return bool true si el ejercicio tiene completado = 1 para ese usuario.
     */
    public static function exerciseCompleted(int $userId, int $exerciseId): bool {
        $stmt = getDB()->prepare(
            'SELECT completado FROM progreso WHERE id_usuario = ? AND id_ejercicio = ? LIMIT 1'
        );
        $stmt->execute([$userId, $exerciseId]);
        $row = $stmt->fetch();
        return $row && (int)$row['completado'] === 1;
    }

    /**
     * Elimina el progreso del usuario para un conjunto específico de ejercicios.
     * Se usa para reiniciar una lección individual.
     *
     * @param int   $userId      ID del usuario.
     * @param int[] $exerciseIds IDs de los ejercicios a reiniciar.
     */
    public static function resetLesson(int $userId, array $exerciseIds): void {
        if (empty($exerciseIds)) return;
        $placeholders = implode(',', array_fill(0, count($exerciseIds), '?'));
        // Marca como no completado pero conserva intentos para estadísticas
        $stmt = getDB()->prepare(
            "UPDATE progreso SET completado = 0, fecha_progreso = NOW()
             WHERE id_usuario = ? AND id_ejercicio IN ($placeholders)"
        );
        $stmt->execute(array_merge([$userId], $exerciseIds));
    }

    /**
     * Elimina el progreso del usuario para todos los ejercicios de un módulo completo.
     * Se usa para reiniciar un módulo desde cero.
     *
     * @param int $userId   ID del usuario.
     * @param int $moduleId ID del módulo a reiniciar.
     */
    public static function resetModule(int $userId, int $moduleId): void {
        // Marca como no completado pero conserva intentos para estadísticas
        $stmt = getDB()->prepare(
            'UPDATE progreso p
             JOIN ejercicios e ON e.id_ejercicio = p.id_ejercicio
             SET p.completado = 0, p.fecha_progreso = NOW()
             WHERE p.id_usuario = ? AND e.id_modulo = ?'
        );
        $stmt->execute([$userId, $moduleId]);
    }
}
