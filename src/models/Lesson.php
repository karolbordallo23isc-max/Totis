<?php
require_once __DIR__ . '/../../config/database.php';

class Lesson {

    /**
     * Devuelve todas las lecciones (filas de `contenido`) de un módulo,
     * ordenadas por su campo `orden` ascendente.
     *
     * Las columnas se renombran con alias para desacoplar la vista
     * de los nombres de columna de la base de datos.
     *
     * @param int $moduleId ID del módulo.
     * @return array Lista de lecciones con claves: id, module_id, title, content, sort_order.
     */
    public static function byModule(int $moduleId): array {
        $stmt = getDB()->prepare(
            'SELECT id_contenido AS id,
                    id_modulo    AS module_id,
                    titulo       AS title,
                    texto        AS content,
                    orden        AS sort_order
             FROM contenido
             WHERE id_modulo = ?
             ORDER BY orden ASC'
        );
        $stmt->execute([$moduleId]);
        return $stmt->fetchAll();
    }

    /**
     * Busca una lección por su ID primario.
     *
     * @param int $id ID de la lección (`id_contenido`).
     * @return array|false Fila de la lección o false si no existe.
     */
    public static function find(int $id): array|false {
        $stmt = getDB()->prepare(
            'SELECT id_contenido AS id,
                    id_modulo    AS module_id,
                    titulo       AS title,
                    texto        AS content,
                    orden        AS sort_order
             FROM contenido
             WHERE id_contenido = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Devuelve los ejercicios de una lección con sus opciones mezcladas aleatoriamente.
     *
     * Las opciones se obtienen en una sola query JOIN y luego se agrupan por ejercicio.
     * Después del shuffle, se recalcula el índice y el ID de la opción correcta para
     * que el cliente pueda mostrar las opciones en orden aleatorio sin exponer cuál es
     * la correcta. La verificación final siempre se hace por id_opcion en el servidor,
     * nunca por índice posicional, para evitar manipulación desde el cliente.
     *
     * @param int $lessonId ID de la lección (`id_contenido`).
     * @return array Lista de ejercicios. Cada ejercicio incluye:
     *               - id, question, explanation, type
     *               - options (textos mezclados), option_ids (IDs mezclados)
     *               - correct_answer (índice post-shuffle), correct_option_id (ID de la correcta)
     */
    public static function exercises(int $lessonId): array {
        $stmt = getDB()->prepare(
            'SELECT e.id_ejercicio AS ex_id,
                    e.pregunta     AS question,
                    e.retroalimentacion AS explanation,
                    e.tipo         AS type,
                    o.id_opcion    AS opt_id,
                    o.texto        AS opt_text,
                    o.es_correcta  AS opt_correct
             FROM ejercicios e
             JOIN opcion o ON o.id_ejercicio = e.id_ejercicio
             WHERE e.id_contenido = ?
             ORDER BY e.id_ejercicio ASC, o.id_opcion ASC'
        );
        $stmt->execute([$lessonId]);
        $rows = $stmt->fetchAll();

        if (empty($rows)) return [];

        $exercisesMap = [];
        foreach ($rows as $row) {
            $exId = $row['ex_id'];
            if (!isset($exercisesMap[$exId])) {
                $exercisesMap[$exId] = [
                    'id'          => $exId,
                    'question'    => $row['question'],
                    'explanation' => $row['explanation'],
                    'type'        => $row['type'],
                    'options_raw' => [],
                ];
            }
            $exercisesMap[$exId]['options_raw'][] = [
                'id'         => (int)$row['opt_id'],
                'text'       => $row['opt_text'],
                'is_correct' => (int)$row['opt_correct'],
            ];
        }

        $exercises = [];
        foreach ($exercisesMap as &$ex) {
            $options = $ex['options_raw'];
            shuffle($options);

            $ex['options']    = array_column($options, 'text');
            $ex['option_ids'] = array_column($options, 'id');

            $correctOptionId = 0;
            $correctIdx      = 0;
            foreach ($options as $i => $opt) {
                if ($opt['is_correct'] === 1) {
                    $correctIdx      = $i;
                    $correctOptionId = $opt['id'];
                    break;
                }
            }
            $ex['correct_answer']    = $correctIdx;
            $ex['correct_option_id'] = $correctOptionId;
            unset($ex['options_raw']);
            $exercises[] = $ex;
        }

        return $exercises;
    }
}
