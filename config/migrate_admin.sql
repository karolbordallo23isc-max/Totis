-- ============================================================
-- Migración: Agregar campo is_admin a tabla usuario
-- Ejecutar SOLO si ya tienes la BD instalada y NO quieres
-- reimportar loopbook.sql completo.
-- ============================================================

USE loopbook;

-- Agregar columna is_admin si no existe
ALTER TABLE `usuario`
  ADD COLUMN IF NOT EXISTS `is_admin` TINYINT(1) NOT NULL DEFAULT 0
  AFTER `contraseña`;

-- Dar permisos de admin al usuario 'prueba' (id=1)
UPDATE `usuario` SET `is_admin` = 1 WHERE `usuario` = 'prueba';

-- Verificar resultado
SELECT id_usuario, usuario, is_admin FROM usuario;
