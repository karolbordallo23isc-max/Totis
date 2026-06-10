-- Migración: agrega columna imagen a la tabla modulos
-- Ejecutar una sola vez contra la base de datos loopbook
ALTER TABLE `modulos`
  ADD COLUMN `imagen` VARCHAR(255) DEFAULT NULL AFTER `descripcion`;
