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
