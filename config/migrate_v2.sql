-- ============================================================
-- Loopbook v2 — Migración completa
-- Ejecutar sobre la BD loopbook existente.
-- Agrega: categorías, campo is_admin, vistas, procedimientos
-- almacenados, y datos de ejemplo actualizados.
-- ============================================================

USE loopbook;

-- ── 1. Campo is_admin (si no existe) ──────────────────────────
ALTER TABLE `usuario`
  ADD COLUMN IF NOT EXISTS `is_admin` TINYINT(1) NOT NULL DEFAULT 0 AFTER `contraseña`;

UPDATE `usuario` SET `is_admin` = 1 WHERE `usuario` = 'prueba';

-- ── 2. Campo categoria en modulos (si no existe) ──────────────
ALTER TABLE `modulos`
  ADD COLUMN IF NOT EXISTS `categoria` VARCHAR(80) NOT NULL DEFAULT 'Fundamentos'
  AFTER `descripcion`;

-- Asignar categorías a los módulos existentes
UPDATE `modulos` SET `categoria` = 'Fundamentos de Programación' WHERE `id_modulo` IN (1,2);
UPDATE `modulos` SET `categoria` = 'Variables y Datos'           WHERE `id_modulo` = 3;
UPDATE `modulos` SET `categoria` = 'Control de Flujo'            WHERE `id_modulo` = 4;
UPDATE `modulos` SET `categoria` = 'Funciones'                   WHERE `id_modulo` = 5;

-- ── 3. Campo url en contenido (si no existe) ──────────────────
ALTER TABLE `contenido`
  ADD COLUMN IF NOT EXISTS `url` VARCHAR(255) DEFAULT NULL AFTER `tipo`;

-- Agregar URLs de video a lecciones existentes (YouTube embed)
UPDATE `contenido` SET
  `tipo` = 'video',
  `url`  = 'https://www.youtube.com/embed/zOjov-2OZ0E'
WHERE `id_contenido` = 1;

UPDATE `contenido` SET
  `tipo` = 'video',
  `url`  = 'https://www.youtube.com/embed/nUgl_K80-SQ'
WHERE `id_contenido` = 7;

UPDATE `contenido` SET
  `tipo` = 'video',
  `url`  = 'https://www.youtube.com/embed/p1Le5a1xnAg'
WHERE `id_contenido` = 10;

-- ── 4. VISTAS ──────────────────────────────────────────────────

-- Vista: resumen de progreso por usuario y módulo
CREATE OR REPLACE VIEW `v_progreso_usuario_modulo` AS
SELECT
  u.id_usuario,
  u.nombre        AS nombre_usuario,
  u.usuario,
  m.id_modulo,
  m.nombre        AS nombre_modulo,
  m.orden         AS orden_modulo,
  m.categoria,
  COUNT(e.id_ejercicio)                                          AS total_ejercicios,
  COUNT(p.id_progreso)                                           AS ejercicios_intentados,
  SUM(CASE WHEN p.completado = 1 THEN 1 ELSE 0 END)             AS ejercicios_completados,
  ROUND(
    SUM(CASE WHEN p.completado = 1 THEN 1 ELSE 0 END)
    / NULLIF(COUNT(e.id_ejercicio), 0) * 100
  , 0)                                                           AS porcentaje,
  MAX(p.fecha_progreso)                                          AS ultima_actividad
FROM usuario u
CROSS JOIN modulos m
LEFT JOIN ejercicios e  ON e.id_modulo   = m.id_modulo
LEFT JOIN progreso   p  ON p.id_ejercicio = e.id_ejercicio
                       AND p.id_usuario   = u.id_usuario
WHERE u.is_admin = 0
GROUP BY u.id_usuario, m.id_modulo;

-- Vista: ranking de usuarios por progreso global
CREATE OR REPLACE VIEW `v_ranking_usuarios` AS
SELECT
  u.id_usuario,
  u.nombre,
  u.usuario,
  u.avatar,
  u.fecha_registro,
  COUNT(DISTINCT p.id_ejercicio)                                 AS total_completados,
  ROUND(AVG(CASE WHEN p.completado = 1 THEN 100 ELSE 0 END), 0) AS promedio_calificacion,
  MAX(p.fecha_progreso)                                          AS ultima_actividad
FROM usuario u
LEFT JOIN progreso p ON p.id_usuario = u.id_usuario AND p.completado = 1
WHERE u.is_admin = 0
GROUP BY u.id_usuario
ORDER BY total_completados DESC, promedio_calificacion DESC;

-- Vista: detalle de lecciones con conteo de ejercicios
CREATE OR REPLACE VIEW `v_lecciones_detalle` AS
SELECT
  c.id_contenido,
  c.id_modulo,
  c.titulo,
  c.texto,
  c.tipo,
  c.url,
  c.orden,
  m.nombre        AS nombre_modulo,
  m.categoria,
  COUNT(e.id_ejercicio) AS total_ejercicios
FROM contenido c
JOIN modulos m ON m.id_modulo = c.id_modulo
LEFT JOIN ejercicios e ON e.id_contenido = c.id_contenido
GROUP BY c.id_contenido;

-- Vista: módulos con estado de desbloqueo (para uso en PHP)
CREATE OR REPLACE VIEW `v_modulos_curso` AS
SELECT
  m.id_modulo,
  m.id_curso,
  m.nombre,
  m.descripcion,
  m.categoria,
  m.orden,
  COUNT(e.id_ejercicio) AS total_ejercicios
FROM modulos m
LEFT JOIN ejercicios e ON e.id_modulo = m.id_modulo
GROUP BY m.id_modulo
ORDER BY m.orden ASC;

-- ── 5. PROCEDIMIENTOS ALMACENADOS ─────────────────────────────

DROP PROCEDURE IF EXISTS `sp_progreso_usuario`;
DELIMITER $$
CREATE PROCEDURE `sp_progreso_usuario`(IN p_usuario_id INT)
BEGIN
  SELECT
    m.id_modulo,
    m.nombre        AS modulo,
    m.orden,
    m.categoria,
    COUNT(e.id_ejercicio)                                        AS total,
    SUM(CASE WHEN pr.completado = 1 THEN 1 ELSE 0 END)          AS completados,
    ROUND(
      SUM(CASE WHEN pr.completado = 1 THEN 1 ELSE 0 END)
      / NULLIF(COUNT(e.id_ejercicio), 0) * 100
    , 0)                                                         AS porcentaje
  FROM modulos m
  LEFT JOIN ejercicios e  ON e.id_modulo    = m.id_modulo
  LEFT JOIN progreso   pr ON pr.id_ejercicio = e.id_ejercicio
                         AND pr.id_usuario   = p_usuario_id
  GROUP BY m.id_modulo
  ORDER BY m.orden ASC;
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS `sp_modulo_completado`;
DELIMITER $$
CREATE PROCEDURE `sp_modulo_completado`(
  IN  p_usuario_id INT,
  IN  p_modulo_id  INT,
  OUT p_completado TINYINT
)
BEGIN
  DECLARE v_total     INT DEFAULT 0;
  DECLARE v_completos INT DEFAULT 0;

  SELECT COUNT(*) INTO v_total
  FROM ejercicios WHERE id_modulo = p_modulo_id;

  SELECT COUNT(*) INTO v_completos
  FROM progreso p
  JOIN ejercicios e ON e.id_ejercicio = p.id_ejercicio
  WHERE p.id_usuario = p_usuario_id
    AND e.id_modulo  = p_modulo_id
    AND p.completado = 1;

  SET p_completado = IF(v_total > 0 AND v_total = v_completos, 1, 0);
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS `sp_reiniciar_modulo`;
DELIMITER $$
CREATE PROCEDURE `sp_reiniciar_modulo`(
  IN p_usuario_id INT,
  IN p_modulo_id  INT
)
BEGIN
  DELETE p FROM progreso p
  JOIN ejercicios e ON e.id_ejercicio = p.id_ejercicio
  WHERE p.id_usuario = p_usuario_id
    AND e.id_modulo  = p_modulo_id;
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS `sp_estadisticas_admin`;
DELIMITER $$
CREATE PROCEDURE `sp_estadisticas_admin`()
BEGIN
  SELECT
    (SELECT COUNT(*) FROM usuario WHERE is_admin = 0)  AS total_estudiantes,
    (SELECT COUNT(*) FROM modulos)                      AS total_modulos,
    (SELECT COUNT(*) FROM contenido)                    AS total_lecciones,
    (SELECT COUNT(*) FROM ejercicios)                   AS total_ejercicios,
    (SELECT COUNT(*) FROM progreso WHERE completado = 1) AS total_completados,
    (SELECT ROUND(AVG(sub.pct),1)
     FROM (
       SELECT ROUND(
         SUM(CASE WHEN p.completado=1 THEN 1 ELSE 0 END)
         / NULLIF(COUNT(e.id_ejercicio),0)*100
       ,0) AS pct
       FROM usuario u
       LEFT JOIN progreso p ON p.id_usuario = u.id_usuario
       LEFT JOIN ejercicios e ON e.id_ejercicio = p.id_ejercicio
       WHERE u.is_admin = 0
       GROUP BY u.id_usuario
     ) sub
    ) AS promedio_progreso_global;
END$$
DELIMITER ;

COMMIT;
