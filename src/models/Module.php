<?php
require_once __DIR__ . '/../../config/database.php';

class Module {

    /**
     * Devuelve todos los módulos ordenados por su campo `orden` ascendente.
     *
     * @return array Lista de módulos como arrays asociativos.
     */
    public static function all(): array {
        return getDB()
            ->query('SELECT * FROM modulos ORDER BY orden ASC')
            ->fetchAll();
    }

    /**
     * Busca un módulo por su ID primario.
     *
     * @param int $id ID del módulo (`id_modulo`).
     * @return array|false Fila del módulo o false si no existe.
     */
    public static function find(int $id): array|false {
        $stmt = getDB()->prepare('SELECT * FROM modulos WHERE id_modulo = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Verifica si un módulo está desbloqueado para el usuario.
     * El primer módulo (orden=1) siempre está desbloqueado.
     * Los siguientes requieren que el módulo anterior esté 100% completado.
     */
    public static function isUnlocked(int $userId, int $moduleId): bool {
        $stmt = getDB()->prepare('SELECT orden FROM modulos WHERE id_modulo = ? LIMIT 1');
        $stmt->execute([$moduleId]);
        $row = $stmt->fetch();
        if (!$row) return false;
        if ((int)$row['orden'] === 1) return true;

        // Módulo anterior (orden inmediatamente menor)
        $stmt2 = getDB()->prepare(
            'SELECT id_modulo FROM modulos WHERE orden < ? ORDER BY orden DESC LIMIT 1'
        );
        $stmt2->execute([(int)$row['orden']]);
        $prev = $stmt2->fetch();
        if (!$prev) return true;

        $prevId = (int)$prev['id_modulo'];

        $stmtT = getDB()->prepare('SELECT COUNT(*) FROM ejercicios WHERE id_modulo = ?');
        $stmtT->execute([$prevId]);
        $total = (int)$stmtT->fetchColumn();
        if ($total === 0) return true;

        $stmtC = getDB()->prepare(
            'SELECT COUNT(*) FROM progreso p
             JOIN ejercicios e ON e.id_ejercicio = p.id_ejercicio
             WHERE p.id_usuario = ? AND e.id_modulo = ? AND p.completado = 1'
        );
        $stmtC->execute([$userId, $prevId]);
        $completed = (int)$stmtC->fetchColumn();

        return $completed >= $total;
    }

    /**
     * Devuelve todos los módulos con estado de desbloqueo para un usuario.
     */
    public static function allWithStatus(int $userId): array {
        $modules = self::all();
        foreach ($modules as &$m) {
            $m['unlocked'] = self::isUnlocked($userId, (int)$m['id_modulo']);
        }
        unset($m);
        return $modules;
    }

    /**
     * Devuelve el siguiente módulo en la secuencia del curso, comparando por `orden`.
     * Retorna false si el módulo actual es el último.
     *
     * @param int $currentModuleId ID del módulo actual.
     * @return array|false Fila del siguiente módulo o false si no hay uno.
     */
    public static function next(int $currentModuleId): array|false {
        $stmt = getDB()->prepare(
            'SELECT * FROM modulos
             WHERE orden > (SELECT orden FROM modulos WHERE id_modulo = ? LIMIT 1)
             ORDER BY orden ASC
             LIMIT 1'
        );
        $stmt->execute([$currentModuleId]);
        return $stmt->fetch();
    }

    /**
     * Devuelve los bloques de contenido (lecciones) de un módulo, ordenados por `orden`.
     *
     * @param int $moduleId ID del módulo.
     * @return array Lista de filas de la tabla `contenido`.
     */
    public static function contenido(int $moduleId): array {
        $stmt = getDB()->prepare(
            'SELECT * FROM contenido WHERE id_modulo = ? ORDER BY orden ASC'
        );
        $stmt->execute([$moduleId]);
        return $stmt->fetchAll();
    }
}
