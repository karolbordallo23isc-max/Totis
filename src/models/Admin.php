<?php
require_once __DIR__ . '/../../config/database.php';

/**
 * Admin — Modelo para operaciones CRUD del panel de administración.
 *
 * Cubre módulos, lecciones (contenido), ejercicios y opciones de respuesta.
 * Todos los métodos usan sentencias preparadas para prevenir SQL injection.
 */
class Admin {

    // ── MÓDULOS ────────────────────────────────────────────────

    public static function allModules(): array {
        return getDB()
            ->query('SELECT m.*, c.nombre AS curso_nombre,
                            (SELECT COUNT(*) FROM contenido WHERE id_modulo = m.id_modulo) AS total_lecciones,
                            (SELECT COUNT(*) FROM ejercicios WHERE id_modulo = m.id_modulo) AS total_ejercicios
                     FROM modulos m JOIN curso c ON c.id_curso = m.id_curso
                     ORDER BY m.orden ASC')
            ->fetchAll();
    }

    public static function createModule(int $cursoId, string $nombre, string $descripcion, int $orden): int {
        $stmt = getDB()->prepare(
            'INSERT INTO modulos (id_curso, nombre, descripcion, orden) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$cursoId, $nombre, $descripcion, $orden]);
        return (int)getDB()->lastInsertId();
    }

    public static function updateModule(int $id, string $nombre, string $descripcion, int $orden): void {
        $stmt = getDB()->prepare(
            'UPDATE modulos SET nombre = ?, descripcion = ?, orden = ? WHERE id_modulo = ?'
        );
        $stmt->execute([$nombre, $descripcion, $orden, $id]);
    }

    public static function deleteModule(int $id): void {
        $stmt = getDB()->prepare('DELETE FROM modulos WHERE id_modulo = ?');
        $stmt->execute([$id]);
    }

    // ── LECCIONES (contenido) ──────────────────────────────────

    public static function lessonsByModule(int $moduleId): array {
        $stmt = getDB()->prepare(
            'SELECT c.*,
                    (SELECT COUNT(*) FROM ejercicios WHERE id_contenido = c.id_contenido) AS total_ejercicios
             FROM contenido c WHERE c.id_modulo = ? ORDER BY c.orden ASC'
        );
        $stmt->execute([$moduleId]);
        return $stmt->fetchAll();
    }

    public static function findLesson(int $id): array|false {
        $stmt = getDB()->prepare('SELECT * FROM contenido WHERE id_contenido = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function createLesson(int $moduleId, string $titulo, string $texto, string $tipo, string $url, int $orden): int {
        $stmt = getDB()->prepare(
            'INSERT INTO contenido (id_modulo, titulo, texto, tipo, url, orden) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$moduleId, $titulo, $texto, $tipo, $url ?: null, $orden]);
        return (int)getDB()->lastInsertId();
    }

    public static function updateLesson(int $id, string $titulo, string $texto, string $tipo, string $url, int $orden): void {
        $stmt = getDB()->prepare(
            'UPDATE contenido SET titulo = ?, texto = ?, tipo = ?, url = ?, orden = ? WHERE id_contenido = ?'
        );
        $stmt->execute([$titulo, $texto, $tipo, $url ?: null, $orden, $id]);
    }

    public static function deleteLesson(int $id): void {
        $stmt = getDB()->prepare('DELETE FROM contenido WHERE id_contenido = ?');
        $stmt->execute([$id]);
    }

    // ── EJERCICIOS ─────────────────────────────────────────────

    public static function exercisesByLesson(int $lessonId): array {
        $stmt = getDB()->prepare(
            'SELECT e.*,
                    (SELECT COUNT(*) FROM opcion WHERE id_ejercicio = e.id_ejercicio) AS total_opciones
             FROM ejercicios e WHERE e.id_contenido = ? ORDER BY e.id_ejercicio ASC'
        );
        $stmt->execute([$lessonId]);
        return $stmt->fetchAll();
    }

    public static function findExercise(int $id): array|false {
        $stmt = getDB()->prepare('SELECT * FROM ejercicios WHERE id_ejercicio = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function createExercise(int $moduleId, int $lessonId, string $pregunta, string $retro, string $tipo): int {
        $stmt = getDB()->prepare(
            'INSERT INTO ejercicios (id_modulo, id_contenido, pregunta, retroalimentacion, tipo) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$moduleId, $lessonId, $pregunta, $retro, $tipo]);
        return (int)getDB()->lastInsertId();
    }

    public static function updateExercise(int $id, string $pregunta, string $retro, string $tipo): void {
        $stmt = getDB()->prepare(
            'UPDATE ejercicios SET pregunta = ?, retroalimentacion = ?, tipo = ? WHERE id_ejercicio = ?'
        );
        $stmt->execute([$pregunta, $retro, $tipo, $id]);
    }

    public static function deleteExercise(int $id): void {
        $stmt = getDB()->prepare('DELETE FROM ejercicios WHERE id_ejercicio = ?');
        $stmt->execute([$id]);
    }

    // ── OPCIONES ───────────────────────────────────────────────

    public static function optionsByExercise(int $exerciseId): array {
        $stmt = getDB()->prepare(
            'SELECT * FROM opcion WHERE id_ejercicio = ? ORDER BY id_opcion ASC'
        );
        $stmt->execute([$exerciseId]);
        return $stmt->fetchAll();
    }

    public static function createOption(int $exerciseId, string $texto, bool $esCorrecta, string $retro): int {
        $stmt = getDB()->prepare(
            'INSERT INTO opcion (id_ejercicio, texto, es_correcta, retroalimentacion) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$exerciseId, $texto, $esCorrecta ? 1 : 0, $retro]);
        return (int)getDB()->lastInsertId();
    }

    public static function updateOption(int $id, string $texto, bool $esCorrecta, string $retro): void {
        $stmt = getDB()->prepare(
            'UPDATE opcion SET texto = ?, es_correcta = ?, retroalimentacion = ? WHERE id_opcion = ?'
        );
        $stmt->execute([$texto, $esCorrecta ? 1 : 0, $retro, $id]);
    }

    public static function deleteOption(int $id): void {
        $stmt = getDB()->prepare('DELETE FROM opcion WHERE id_opcion = ?');
        $stmt->execute([$id]);
    }

    /** Elimina todas las opciones de un ejercicio y las reemplaza en bloque. */
    public static function replaceOptions(int $exerciseId, array $options): void {
        $db = getDB();
        $db->prepare('DELETE FROM opcion WHERE id_ejercicio = ?')->execute([$exerciseId]);
        $stmt = $db->prepare(
            'INSERT INTO opcion (id_ejercicio, texto, es_correcta, retroalimentacion) VALUES (?, ?, ?, ?)'
        );
        foreach ($options as $opt) {
            $stmt->execute([
                $exerciseId,
                $opt['texto'],
                isset($opt['es_correcta']) && $opt['es_correcta'] ? 1 : 0,
                $opt['retroalimentacion'] ?? '',
            ]);
        }
    }

    // ── ESTADÍSTICAS GENERALES ─────────────────────────────────

    public static function stats(): array {
        $db = getDB();
        return [
            'modulos'    => (int)$db->query('SELECT COUNT(*) FROM modulos')->fetchColumn(),
            'lecciones'  => (int)$db->query('SELECT COUNT(*) FROM contenido')->fetchColumn(),
            'ejercicios' => (int)$db->query('SELECT COUNT(*) FROM ejercicios')->fetchColumn(),
            'opciones'   => (int)$db->query('SELECT COUNT(*) FROM opcion')->fetchColumn(),
            'usuarios'   => (int)$db->query('SELECT COUNT(*) FROM usuario')->fetchColumn(),
            'progresos'  => (int)$db->query('SELECT COUNT(*) FROM progreso WHERE completado = 1')->fetchColumn(),
        ];
    }

    public static function allCourses(): array {
        return getDB()->query('SELECT * FROM curso ORDER BY id_curso ASC')->fetchAll();
    }

    public static function nextModuleOrder(): int {
        $val = getDB()->query('SELECT COALESCE(MAX(orden),0)+1 FROM modulos')->fetchColumn();
        return (int)$val;
    }

    public static function nextLessonOrder(int $moduleId): int {
        $stmt = getDB()->prepare('SELECT COALESCE(MAX(orden),0)+1 FROM contenido WHERE id_modulo = ?');
        $stmt->execute([$moduleId]);
        return (int)$stmt->fetchColumn();
    }
}
