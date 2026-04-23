-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 23-04-2026 a las 03:00:52
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `loopbook`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido`
--

CREATE TABLE `contenido` (
  `id_contenido` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `texto` text DEFAULT NULL,
  `tipo` enum('texto','video','imagen') NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contenido`
--

INSERT INTO `contenido` (`id_contenido`, `id_modulo`, `titulo`, `texto`, `tipo`, `url`, `orden`) VALUES
(1, 1, 'Definición', 'Los lenguajes de programación son conjuntos de reglas sintácticas y semánticas que permiten a los desarrolladores crear instrucciones para que las computadoras ejecuten software, sitios web y aplicaciones.', 'texto', NULL, 1),
(2, 2, 'Diferencias', 'Las dos principales diferencias entre lenguajes compilados (como C++) e interpretados (como Python) son el momento de la traducción a código máquina y la velocidad de ejecución.', 'texto', NULL, 1),
(3, 5, '¿Qué es una función?', 'Una función es un bloque de código que realiza una tarea específica y puede ejecutarse cuantas veces sea necesario sin repetir el código.', 'texto', NULL, 1),
(4, 5, 'Ventajas de usar funciones', 'Las funciones permiten reutilizar código, facilitan la lectura del programa y hacen más sencillo encontrar y corregir errores.', 'texto', NULL, 2),
(5, 1, 'Historia de los lenguajes', 'Los primeros lenguajes de programación surgieron en los años 50. Fortran fue uno de los primeros lenguajes de alto nivel, diseñado para cálculos científicos. Con el tiempo fueron evolucionando hasta los lenguajes modernos que usamos hoy como Python, JavaScript y Java.', 'texto', NULL, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso`
--

CREATE TABLE `curso` (
  `id_curso` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `curso`
--

INSERT INTO `curso` (`id_curso`, `nombre`, `descripcion`) VALUES
(1, 'Lenguajes de programación', 'Aprende los fundamentos de los lenguajes de programación');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ejercicios`
--

CREATE TABLE `ejercicios` (
  `id_ejercicio` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `pregunta` text NOT NULL,
  `retroalimentacion` text DEFAULT NULL,
  `tipo` enum('opcion_multiple','verdadero_falso') NOT NULL,
  `fecha_creacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ejercicios`
--

INSERT INTO `ejercicios` (`id_ejercicio`, `id_modulo`, `pregunta`, `retroalimentacion`, `tipo`, `fecha_creacion`) VALUES
(1, 1, '¿Qué es un lenguaje de programación?', 'Recuerda que es un sistema formal de instrucciones para computadoras.', 'verdadero_falso', '2026-04-22 15:58:31'),
(2, 1, '¿Para qué sirven los lenguajes de programación?', 'Piensa en las aplicaciones que usas a diario.', 'verdadero_falso', '2026-04-22 15:58:31'),
(3, 2, '¿Cuál es la diferencia principal entre un lenguaje compilado e interpretado?', 'Piensa en cuándo se traduce el código a lenguaje máquina.', 'verdadero_falso', '2026-04-22 15:58:31'),
(4, 2, 'Menciona un ejemplo de lenguaje compilado y uno interpretado.', 'Ejemplos: C++ es compilado, Python es interpretado.', 'verdadero_falso', '2026-04-22 15:58:31'),
(5, 3, '¿Qué es una variable en programación?', 'Una variable es un espacio en memoria que almacena un valor.', 'verdadero_falso', '0000-00-00 00:00:00'),
(6, 3, '¿Cuál es la diferencia entre int y float?', 'int almacena enteros, float almacena decimales.', 'verdadero_falso', '0000-00-00 00:00:00'),
(7, 3, '¿Para qué sirven los tipos de datos?', 'Definen qué tipo de valor puede almacenar una variable.', 'verdadero_falso', '0000-00-00 00:00:00'),
(8, 4, '¿Qué es un condicional if?', 'Ejecuta un bloque de código solo si se cumple una condición.', 'verdadero_falso', '0000-00-00 00:00:00'),
(9, 4, '¿Cuál es la diferencia entre for y while?', 'for itera un número fijo de veces, while itera mientras se cumpla una condición.', 'verdadero_falso', '0000-00-00 00:00:00'),
(10, 4, '¿Qué es un bucle infinito?', 'Un bucle que nunca termina porque su condición siempre es verdadera.', 'verdadero_falso', '0000-00-00 00:00:00'),
(11, 1, '¿Puedes mencionar un lenguaje de programación que conozcas?', 'Ejemplos: Python, Java, C++, JavaScript.', 'verdadero_falso', '0000-00-00 00:00:00'),
(12, 2, '¿Por qué crees que Python es un lenguaje interpretado?', 'Porque su código se ejecuta línea por línea en tiempo real.', 'verdadero_falso', '0000-00-00 00:00:00'),
(13, 3, '¿Qué tipo de dato usarías para guardar el nombre de una persona?', 'Se usaría el tipo String o cadena de texto.', 'verdadero_falso', '0000-00-00 00:00:00'),
(14, 4, '¿Cuándo usarías un bucle while en lugar de un for?', 'Cuando no sabes de antemano cuántas veces se repetirá el ciclo.', 'verdadero_falso', '0000-00-00 00:00:00'),
(15, 5, '¿Qué es una función en programación?', 'Recuerda que una función agrupa código reutilizable bajo un nombre.', 'verdadero_falso', '0000-00-00 00:00:00'),
(16, 5, '¿Cuál es la ventaja principal de usar funciones?', 'Piensa en qué pasa cuando necesitas repetir la misma lógica varias veces.', 'verdadero_falso', '0000-00-00 00:00:00'),
(17, 5, '¿Qué diferencia hay entre una función y un método?', 'Un método es una función que pertenece a un objeto o clase.', 'verdadero_falso', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripcion`
--

CREATE TABLE `inscripcion` (
  `id_inscripcion` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `estado_inscripcion` enum('activo','completado','cancelado') NOT NULL,
  `fecha_inscripcion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id_modulo` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `orden` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id_modulo`, `id_curso`, `nombre`, `descripcion`, `orden`) VALUES
(1, 1, '¿Qué son los Lenguajes de programación?', 'Introducción a los lenguajes', 1),
(2, 1, '¿Qué diferencia hay entre lenguaje compilado e interpretado?', 'Compilado vs interpretado', 2),
(3, 1, 'Variables y tipos de datos', 'Aprenderás a almacenar y manipular información', 3),
(4, 1, 'Estructura de control', 'Condicionales, bucles y control de flujos', 4),
(5, 1, 'Funciones y métodos', 'Aprenderás a organizar tu código en bloques reutilizables', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opcion`
--

CREATE TABLE `opcion` (
  `id_opcion` int(11) NOT NULL,
  `id_ejercicio` int(11) NOT NULL,
  `texto` varchar(255) NOT NULL,
  `es_correcta` tinyint(1) NOT NULL,
  `retroalimentacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `progreso`
--

CREATE TABLE `progreso` (
  `id_progreso` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_ejercicio` int(11) NOT NULL,
  `intentos` int(11) DEFAULT NULL,
  `calificacion` decimal(5,2) DEFAULT NULL,
  `completado` tinyint(1) DEFAULT NULL,
  `fecha_progreso` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `progreso`
--

INSERT INTO `progreso` (`id_progreso`, `id_usuario`, `id_ejercicio`, `intentos`, `calificacion`, `completado`, `fecha_progreso`) VALUES
(1, 2, 1, 1, 100.00, 1, '2026-04-23 00:50:30'),
(2, 2, 2, 1, 100.00, 1, '2026-04-23 00:50:30'),
(3, 2, 3, 1, 100.00, 1, '2026-04-23 01:39:30'),
(4, 2, 4, 1, 100.00, 1, '2026-04-23 01:39:30'),
(5, 2, 15, 1, 100.00, 1, '2026-04-23 02:09:55'),
(6, 2, 16, 1, 100.00, 1, '2026-04-23 02:09:55'),
(7, 2, 17, 1, 100.00, 1, '2026-04-23 02:09:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuesta_abierta`
--

CREATE TABLE `respuesta_abierta` (
  `id_respuesta` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_ejercicio` int(11) NOT NULL,
  `respuesta` text NOT NULL,
  `fecha_respuesta` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `respuesta_abierta`
--

INSERT INTO `respuesta_abierta` (`id_respuesta`, `id_usuario`, `id_ejercicio`, `respuesta`, `fecha_respuesta`) VALUES
(1, 2, 1, 'asddd', '2026-04-23 01:33:55'),
(2, 1, 1, 'bar', '2026-04-23 00:03:43'),
(3, 1, 2, 'dxczcv', '2026-04-23 00:02:35'),
(7, 2, 2, 'f', '2026-04-23 00:59:33'),
(11, 2, 3, 'asdads', '2026-04-23 01:39:20'),
(12, 2, 4, 'asdaf', '2026-04-23 01:39:28'),
(13, 2, 15, 's', '2026-04-23 02:09:40'),
(14, 2, 17, 'ddd', '2026-04-23 02:09:52'),
(15, 2, 16, 'f', '2026-04-23 02:09:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `fecha_registro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `usuario`, `correo`, `contraseña`, `fecha_registro`) VALUES
(1, 'prueba', 'prueba', 'prueba@gmail.com', '$2y$10$AVQpnPEoIjBynjowNUj95e2y6J8CF/QavadIvA8MGe8IbMVB9mSNW', '2026-04-22 22:58:24'),
(2, 'test', 'test', 't@gmail.com', '$2y$10$POvcAnwcJ1GlpoitPbSDPuN7OrGkv6KCoz6/7cT5DqB8ljkWL7jYa', '2026-04-22 23:27:53');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `contenido`
--
ALTER TABLE `contenido`
  ADD PRIMARY KEY (`id_contenido`),
  ADD KEY `id_modulo` (`id_modulo`);

--
-- Indices de la tabla `curso`
--
ALTER TABLE `curso`
  ADD PRIMARY KEY (`id_curso`);

--
-- Indices de la tabla `ejercicios`
--
ALTER TABLE `ejercicios`
  ADD PRIMARY KEY (`id_ejercicio`),
  ADD KEY `id_modulo` (`id_modulo`);

--
-- Indices de la tabla `inscripcion`
--
ALTER TABLE `inscripcion`
  ADD PRIMARY KEY (`id_inscripcion`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_curso` (`id_curso`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id_modulo`),
  ADD KEY `id_curso` (`id_curso`);

--
-- Indices de la tabla `opcion`
--
ALTER TABLE `opcion`
  ADD PRIMARY KEY (`id_opcion`),
  ADD KEY `id_ejercicio` (`id_ejercicio`);

--
-- Indices de la tabla `progreso`
--
ALTER TABLE `progreso`
  ADD PRIMARY KEY (`id_progreso`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_ejercicio` (`id_ejercicio`);

--
-- Indices de la tabla `respuesta_abierta`
--
ALTER TABLE `respuesta_abierta`
  ADD PRIMARY KEY (`id_respuesta`),
  ADD UNIQUE KEY `unica_respuesta` (`id_usuario`,`id_ejercicio`),
  ADD KEY `id_ejercicio` (`id_ejercicio`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `contenido`
--
ALTER TABLE `contenido`
  MODIFY `id_contenido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `curso`
--
ALTER TABLE `curso`
  MODIFY `id_curso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ejercicios`
--
ALTER TABLE `ejercicios`
  MODIFY `id_ejercicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `inscripcion`
--
ALTER TABLE `inscripcion`
  MODIFY `id_inscripcion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `opcion`
--
ALTER TABLE `opcion`
  MODIFY `id_opcion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `progreso`
--
ALTER TABLE `progreso`
  MODIFY `id_progreso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `respuesta_abierta`
--
ALTER TABLE `respuesta_abierta`
  MODIFY `id_respuesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `contenido`
--
ALTER TABLE `contenido`
  ADD CONSTRAINT `contenido_ibfk_1` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`);

--
-- Filtros para la tabla `ejercicios`
--
ALTER TABLE `ejercicios`
  ADD CONSTRAINT `ejercicios_ibfk_1` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`);

--
-- Filtros para la tabla `inscripcion`
--
ALTER TABLE `inscripcion`
  ADD CONSTRAINT `inscripcion_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `inscripcion_ibfk_2` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id_curso`);

--
-- Filtros para la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD CONSTRAINT `modulos_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id_curso`);

--
-- Filtros para la tabla `opcion`
--
ALTER TABLE `opcion`
  ADD CONSTRAINT `opcion_ibfk_1` FOREIGN KEY (`id_ejercicio`) REFERENCES `ejercicios` (`id_ejercicio`);

--
-- Filtros para la tabla `progreso`
--
ALTER TABLE `progreso`
  ADD CONSTRAINT `progreso_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `progreso_ibfk_2` FOREIGN KEY (`id_ejercicio`) REFERENCES `ejercicios` (`id_ejercicio`);

--
-- Filtros para la tabla `respuesta_abierta`
--
ALTER TABLE `respuesta_abierta`
  ADD CONSTRAINT `respuesta_abierta_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `respuesta_abierta_ibfk_2` FOREIGN KEY (`id_ejercicio`) REFERENCES `ejercicios` (`id_ejercicio`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
