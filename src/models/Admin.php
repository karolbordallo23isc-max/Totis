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
        $stmt = getDB()->prepare(
            'SELECT id_ejercicio, id_modulo, id_contenido, pregunta, retroalimentacion,
                    tipo, expected_output, code_instructions, code_hint
             FROM ejercicios WHERE id_ejercicio = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function createExercise(int $moduleId, int $lessonId, string $pregunta, string $retro, string $tipo, string $expected = '', string $codeInst = '', string $codeHint = ''): int {
        $stmt = getDB()->prepare(
            'INSERT INTO ejercicios (id_modulo, id_contenido, pregunta, retroalimentacion, tipo, expected_output, code_instructions, code_hint)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$moduleId, $lessonId, $pregunta, $retro, $tipo,
            $expected ?: null, $codeInst ?: null, $codeHint ?: null]);
        return (int)getDB()->lastInsertId();
    }

    public static function updateExercise(int $id, string $pregunta, string $retro, string $tipo, string $expected = '', string $codeInst = '', string $codeHint = ''): void {
        $stmt = getDB()->prepare(
            'UPDATE ejercicios SET pregunta = ?, retroalimentacion = ?, tipo = ?,
             expected_output = ?, code_instructions = ?, code_hint = ?
             WHERE id_ejercicio = ?'
        );
        $stmt->execute([$pregunta, $retro, $tipo,
            $expected ?: null, $codeInst ?: null, $codeHint ?: null, $id]);
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

    // ── USUARIOS ──────────────────────────────────────────────

    public static function allUsers(): array {
        $stmt = getDB()->query(
            'SELECT u.id_usuario, u.nombre, u.correo, u.usuario, u.is_admin, u.is_superadmin,
                    LEAST(
                      COUNT(DISTINCT CASE WHEN p.alguna_vez_correcto=1 THEN p.id_ejercicio END),
                      (SELECT COUNT(*) FROM ejercicios)
                    ) AS ejercicios_completados
             FROM usuario u
             LEFT JOIN progreso p ON p.id_usuario = u.id_usuario
             GROUP BY u.id_usuario
             ORDER BY ejercicios_completados DESC'
        );
        return $stmt->fetchAll();
    }

    public static function userRanking(int $limit = 10): array {
        $stmt = getDB()->prepare(
            'SELECT u.id_usuario, u.nombre, u.correo,
                    LEAST(
                      COUNT(DISTINCT CASE WHEN p.alguna_vez_correcto=1 THEN p.id_ejercicio END),
                      (SELECT COUNT(*) FROM ejercicios)
                    ) AS ejercicios_completados
             FROM usuario u
             LEFT JOIN progreso p ON p.id_usuario = u.id_usuario
             GROUP BY u.id_usuario
             ORDER BY ejercicios_completados DESC
             LIMIT ?'
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public static function userProgress(int $userId): array {
        $stmt = getDB()->prepare(
            'SELECT m.nombre AS modulo, m.orden AS modulo_orden,
                    c.titulo AS leccion, c.orden AS leccion_orden,
                    e.pregunta, e.tipo,
                    p.completado, p.intentos, p.fecha_progreso AS fecha_completado
             FROM progreso p
             JOIN ejercicios e ON e.id_ejercicio = p.id_ejercicio
             JOIN contenido c  ON c.id_contenido = e.id_contenido
             JOIN modulos m    ON m.id_modulo    = e.id_modulo
             WHERE p.id_usuario = ?
             ORDER BY m.orden ASC, c.orden ASC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /** Estadísticas de ejercicios filtradas por módulo */
    public static function statsExercisesByModule(int $moduleId): array {
        $stmt = getDB()->prepare(
            'SELECT e.id_ejercicio, e.pregunta, e.tipo,
                    c.titulo AS leccion, c.orden AS leccion_orden,
                    COUNT(p.id_progreso)                                              AS total_intentos,
                    SUM(CASE WHEN p.alguna_vez_correcto=1 THEN 1 ELSE 0 END)         AS aciertos,
                    ROUND(SUM(CASE WHEN p.alguna_vez_correcto=1 THEN 1 ELSE 0 END)
                          / NULLIF(COUNT(p.id_progreso),0) * 100, 0)                 AS tasa_acierto
             FROM ejercicios e
             JOIN contenido c ON c.id_contenido = e.id_contenido
             LEFT JOIN progreso p ON p.id_ejercicio = e.id_ejercicio
             WHERE e.id_modulo = ?
             GROUP BY e.id_ejercicio
             ORDER BY c.orden ASC, e.id_ejercicio ASC'
        );
        $stmt->execute([$moduleId]);
        return $stmt->fetchAll();
    }

    public static function findUser(int $id): array|false {
        $stmt = getDB()->prepare('SELECT * FROM usuario WHERE id_usuario = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Alterna el rol admin de un usuario (0→1 o 1→0).
     * No puede aplicarse al superadmin ni al propio usuario en sesión.
     */
    public static function toggleAdmin(int $userId): void {
        $stmt = getDB()->prepare(
            'UPDATE usuario SET is_admin = IF(is_admin = 1, 0, 1) WHERE id_usuario = ? AND is_superadmin = 0'
        );
        $stmt->execute([$userId]);
    }

    // ── ESTADÍSTICAS GENERALES ─────────────────────────────────

    public static function stats(): array {
        $db = getDB();
        return [
            'modulos'    => (int)$db->query('SELECT COUNT(*) FROM modulos')->fetchColumn(),
            'lecciones'  => (int)$db->query('SELECT COUNT(*) FROM contenido')->fetchColumn(),
            'ejercicios' => (int)$db->query('SELECT COUNT(*) FROM ejercicios')->fetchColumn(),
            'opciones'   => (int)$db->query('SELECT COUNT(*) FROM opcion')->fetchColumn(),
            'usuarios'   => (int)$db->query('SELECT COUNT(*) FROM usuario WHERE is_admin=0')->fetchColumn(),
            'progresos'  => (int)$db->query('SELECT COUNT(*) FROM progreso WHERE alguna_vez_correcto = 1')->fetchColumn(),
        ];
    }

    /** Progreso por módulo: cuántos usuarios completaron cada módulo */
    public static function statsByModule(): array {
        $stmt = getDB()->query(
            'SELECT m.id_modulo, m.nombre, m.orden,
                    COUNT(DISTINCT e.id_ejercicio)                    AS total_ejercicios,
                    (SELECT COUNT(*) FROM usuario WHERE is_admin = 0) AS total_usuarios,
                    COALESCE(comp.usuarios_completaron, 0)            AS usuarios_completaron
             FROM modulos m
             LEFT JOIN ejercicios e ON e.id_modulo = m.id_modulo
             LEFT JOIN (
               SELECT e2.id_modulo, COUNT(DISTINCT p.id_usuario) AS usuarios_completaron
               FROM progreso p
               JOIN ejercicios e2 ON e2.id_ejercicio = p.id_ejercicio
               WHERE p.alguna_vez_correcto = 1
               GROUP BY e2.id_modulo, p.id_usuario
               HAVING COUNT(DISTINCT p.id_ejercicio) = (
                 SELECT COUNT(*) FROM ejercicios ex WHERE ex.id_modulo = e2.id_modulo
               )
             ) comp ON comp.id_modulo = m.id_modulo
             GROUP BY m.id_modulo
             ORDER BY m.orden ASC'
        );
        return $stmt->fetchAll();
    }

    /** Estadísticas de ejercicios: intentos promedio y tasa de acierto */
    public static function statsExercises(): array {
        $stmt = getDB()->query(
            'SELECT e.id_ejercicio, e.pregunta, e.tipo,
                    c.titulo AS leccion, m.nombre AS modulo,
                    COUNT(p.id_progreso)                                    AS total_intentos,
                    SUM(CASE WHEN p.completado=1 THEN 1 ELSE 0 END)        AS aciertos,
                    ROUND(SUM(CASE WHEN p.completado=1 THEN 1 ELSE 0 END)
                          / NULLIF(COUNT(p.id_progreso),0) * 100, 0)       AS tasa_acierto
             FROM ejercicios e
             JOIN contenido c  ON c.id_contenido = e.id_contenido
             JOIN modulos m    ON m.id_modulo    = e.id_modulo
             LEFT JOIN progreso p ON p.id_ejercicio = e.id_ejercicio
             GROUP BY e.id_ejercicio
             ORDER BY m.orden ASC, c.orden ASC, e.id_ejercicio ASC'
        );
        return $stmt->fetchAll();
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
