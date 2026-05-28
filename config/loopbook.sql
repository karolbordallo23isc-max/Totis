-- ============================================================
-- Loopbook — Base de datos completa
-- Importar en phpMyAdmin: BD loopbook > Importar > este archivo
-- Un solo archivo, todo incluido, listo para usar.
-- ===========================================================

CREATE DATABASE IF NOT EXISTS loopbook CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE loopbook;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS progreso;
DROP TABLE IF EXISTS opcion;
DROP TABLE IF EXISTS ejercicios;
DROP TABLE IF EXISTS contenido;
DROP TABLE IF EXISTS modulos;
DROP TABLE IF EXISTS curso;
DROP TABLE IF EXISTS usuario;
SET FOREIGN_KEY_CHECKS = 1;

-- ── Usuarios ──────────────────────────────────────────────────
CREATE TABLE `usuario` (
  `id_usuario`     INT(11)      NOT NULL AUTO_INCREMENT,
  `nombre`         VARCHAR(100) NOT NULL,
  `avatar`         VARCHAR(10)  NOT NULL DEFAULT '👤',
  `usuario`        VARCHAR(100) NOT NULL UNIQUE,
  `correo`         VARCHAR(150) NOT NULL UNIQUE,
  `contraseña`     VARCHAR(255) NOT NULL,
  `fecha_registro` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Usuarios de prueba (contraseñas: prueba123 / test123)
INSERT INTO `usuario` (`id_usuario`, `nombre`, `avatar`, `usuario`, `correo`, `contraseña`, `fecha_registro`) VALUES
(1, 'Prueba', '👤', 'prueba', 'prueba@gmail.com', '$2y$10$AVQpnPEoIjBynjowNUj95e2y6J8CF/QavadIvA8MGe8IbMVB9mSNW', '2026-04-22 22:58:24'),
(2, 'Test',   '👤', 'test',   't@gmail.com',      '$2y$10$POvcAnwcJ1GlpoitPbSDPuN7OrGkv6KCoz6/7cT5DqB8ljkWL7jYa', '2026-04-22 23:27:53');

-- ── Curso ──────────────────────────────────────────────────────
CREATE TABLE `curso` (
  `id_curso`    INT(11)      NOT NULL AUTO_INCREMENT,
  `nombre`      VARCHAR(150) NOT NULL,
  `descripcion` TEXT         DEFAULT NULL,
  PRIMARY KEY (`id_curso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `curso` (`id_curso`, `nombre`, `descripcion`) VALUES
(1, 'Lenguajes de programación', 'Aprende los fundamentos de los lenguajes de programación');

-- ── Módulos ────────────────────────────────────────────────────
CREATE TABLE `modulos` (
  `id_modulo`   INT(11)      NOT NULL AUTO_INCREMENT,
  `id_curso`    INT(11)      NOT NULL,
  `nombre`      VARCHAR(150) NOT NULL,
  `descripcion` TEXT         DEFAULT NULL,
  `orden`       INT(11)      NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_modulo`),
  KEY `id_curso` (`id_curso`),
  CONSTRAINT `modulos_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id_curso`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `modulos` (`id_modulo`, `id_curso`, `nombre`, `descripcion`, `orden`) VALUES
(1, 1, '¿Qué son los Lenguajes de Programación?', 'Introducción a los lenguajes de programación y su historia', 1),
(2, 1, 'Lenguajes Compilados vs Interpretados',   'Diferencias, ventajas y ejemplos de cada tipo',              2),
(3, 1, 'Variables y Tipos de Datos',              'Aprende a almacenar y manipular información',                3),
(4, 1, 'Estructuras de Control',                  'Condicionales, bucles y control de flujo',                   4),
(5, 1, 'Funciones y Métodos',                     'Organiza tu código en bloques reutilizables',                5);

-- ── Contenido (lecciones) ──────────────────────────────────────
CREATE TABLE `contenido` (
  `id_contenido` INT(11)                        NOT NULL AUTO_INCREMENT,
  `id_modulo`    INT(11)                        NOT NULL,
  `titulo`       VARCHAR(150)                   NOT NULL,
  `texto`        TEXT                           DEFAULT NULL,
  `tipo`         ENUM('texto','video','imagen') NOT NULL DEFAULT 'texto',
  `url`          VARCHAR(255)                   DEFAULT NULL,
  `orden`        INT(11)                        NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_contenido`),
  KEY `id_modulo` (`id_modulo`),
  CONSTRAINT `contenido_ibfk_1` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `contenido` (`id_contenido`, `id_modulo`, `titulo`, `texto`, `tipo`, `orden`) VALUES
-- Módulo 1
(1,  1, '¿Qué es un lenguaje de programación?',
 'Un lenguaje de programación es un conjunto de reglas sintácticas y semánticas que permite a los desarrolladores escribir instrucciones para que las computadoras ejecuten tareas. Son la base de todo el software, sitios web y aplicaciones que usamos a diario.',
 'texto', 1),
(2,  1, 'Historia breve de los lenguajes',
 'Los primeros lenguajes surgieron en los años 50. Fortran (1957) fue uno de los primeros lenguajes de alto nivel, diseñado para cálculos científicos. Le siguieron COBOL, BASIC, C, y con el tiempo llegaron los lenguajes modernos como Python, JavaScript y Java que usamos hoy.',
 'texto', 2),
(3,  1, '¿Para qué sirven?',
 'Los lenguajes de programación sirven para crear software de todo tipo: aplicaciones móviles, páginas web, videojuegos, sistemas operativos, inteligencia artificial y mucho más. Cada lenguaje tiene sus fortalezas y se adapta mejor a ciertos tipos de proyectos.',
 'texto', 3),
-- Módulo 2
(4,  2, '¿Qué es un lenguaje compilado?',
 'Un lenguaje compilado traduce todo el código fuente a código máquina ANTES de ejecutarlo. Esto se hace mediante un programa llamado compilador. El resultado es un archivo ejecutable muy rápido. Ejemplos: C, C++, Go, Rust.',
 'texto', 1),
(5,  2, '¿Qué es un lenguaje interpretado?',
 'Un lenguaje interpretado traduce y ejecuta el código línea por línea EN TIEMPO REAL, usando un programa llamado intérprete. Es más flexible y fácil de depurar, aunque generalmente más lento. Ejemplos: Python, JavaScript, Ruby, PHP.',
 'texto', 2),
(6,  2, 'Comparación práctica',
 'Compilado: escribes el código → el compilador lo traduce todo → obtienes un ejecutable rápido. Interpretado: escribes el código → el intérprete lo lee y ejecuta línea a línea → más lento pero más portable. Hoy muchos lenguajes usan enfoques híbridos (como Java con su JVM).',
 'texto', 3),
-- Módulo 3
(7,  3, '¿Qué es una variable?',
 'Una variable es un espacio en la memoria del computador que tiene un nombre y almacena un valor que puede cambiar durante la ejecución del programa. Puedes imaginarla como una caja etiquetada donde guardas información.',
 'texto', 1),
(8,  3, 'Tipos de datos básicos',
 'Los tipos de datos más comunes son: int (números enteros como 5, -3), float (decimales como 3.14), string (texto como "Hola"), boolean (verdadero/falso). Cada tipo define qué clase de valor puede almacenar una variable y cuánta memoria ocupa.',
 'texto', 2),
(9,  3, 'Declaración y uso',
 'En Python: nombre = "Ana", edad = 25, precio = 9.99, activo = True. En JavaScript: let nombre = "Ana". En Java: String nombre = "Ana". La sintaxis varía según el lenguaje, pero el concepto es el mismo: asignar un valor a un nombre.',
 'texto', 3),
-- Módulo 4
(10, 4, 'Condicionales IF / ELSE',
 'Un condicional permite que el programa tome decisiones. Si una condición es verdadera, ejecuta un bloque de código; si no, ejecuta otro. Ejemplo: si la edad >= 18, mostrar "Mayor de edad", sino mostrar "Menor de edad".',
 'texto', 1),
(11, 4, 'Bucles: FOR y WHILE',
 'Un bucle FOR repite un bloque de código un número determinado de veces (cuando sabes cuántas iteraciones necesitas). Un bucle WHILE repite mientras una condición sea verdadera (cuando no sabes cuántas veces se repetirá). Ambos evitan escribir código repetido.',
 'texto', 2),
(12, 4, 'Bucle infinito y cómo evitarlo',
 'Un bucle infinito ocurre cuando la condición de parada nunca se vuelve falsa. Esto congela el programa. Para evitarlo: asegúrate de que la variable de control cambie en cada iteración, o usa una condición de salida (break) dentro del bucle.',
 'texto', 3),
-- Módulo 5
(13, 5, '¿Qué es una función?',
 'Una función es un bloque de código con nombre que realiza una tarea específica y puede ejecutarse cuantas veces sea necesario sin repetir el código. Se define una vez y se llama (invoca) cuando se necesita.',
 'texto', 1),
(14, 5, 'Parámetros y valor de retorno',
 'Las funciones pueden recibir datos de entrada llamados parámetros, y pueden devolver un resultado usando return. Ejemplo: función sumar(a, b) → devuelve a + b. Esto hace el código más flexible y reutilizable.',
 'texto', 2),
(15, 5, 'Funciones vs Métodos',
 'Una función es independiente y se llama directamente por su nombre. Un método es una función que pertenece a un objeto o clase, y se llama usando la notación punto: objeto.metodo(). En Python, len("hola") es una función; "hola".upper() es un método.',
 'texto', 3);

-- ── Ejercicios ─────────────────────────────────────────────────
CREATE TABLE `ejercicios` (
  `id_ejercicio`      INT(11)                                  NOT NULL AUTO_INCREMENT,
  `id_modulo`         INT(11)                                  NOT NULL,
  `id_contenido`      INT(11)                                  DEFAULT NULL,
  `pregunta`          TEXT                                     NOT NULL,
  `retroalimentacion` TEXT                                     DEFAULT NULL,
  `tipo`              ENUM('opcion_multiple','verdadero_falso') NOT NULL DEFAULT 'opcion_multiple',
  `fecha_creacion`    DATETIME                                 NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_ejercicio`),
  KEY `id_modulo`    (`id_modulo`),
  KEY `id_contenido` (`id_contenido`),
  CONSTRAINT `ejercicios_ibfk_1` FOREIGN KEY (`id_modulo`)    REFERENCES `modulos`   (`id_modulo`)   ON DELETE CASCADE,
  CONSTRAINT `ejercicios_ibfk_2` FOREIGN KEY (`id_contenido`) REFERENCES `contenido` (`id_contenido`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ejercicios` (`id_ejercicio`, `id_modulo`, `id_contenido`, `pregunta`, `retroalimentacion`, `tipo`) VALUES
-- Módulo 1: lección 1 (id_contenido=1) → pregunta sobre qué es un lenguaje
(1,  1, 1,  '¿Qué es un lenguaje de programación?',                              'Un lenguaje de programación es un sistema formal de instrucciones que permite comunicarse con la computadora.',                                                                    'opcion_multiple'),
-- Módulo 1: lección 2 (id_contenido=2) → pregunta sobre historia de los lenguajes
(2,  1, 2,  '¿En qué década surgieron los primeros lenguajes de alto nivel?',    'Fortran, uno de los primeros lenguajes de alto nivel, fue creado en 1957, en la década de los 50.',                                                                                 'opcion_multiple'),
-- Módulo 1: lección 3 (id_contenido=3) → pregunta sobre para qué sirven
(3,  1, 3,  '¿Cuál de los siguientes es un lenguaje de programación?',           'Python, Java y C++ son lenguajes de programación. HTML es un lenguaje de marcado, no de programación.',                                                                             'opcion_multiple'),
-- Módulo 2
(4,  2, 4,  '¿Cuál es la diferencia principal entre compilado e interpretado?',  'La diferencia clave es el momento en que el código se traduce a lenguaje máquina: antes (compilado) o durante la ejecución (interpretado).',                                       'opcion_multiple'),
(5,  2, 5,  '¿Cuál de estos es un lenguaje COMPILADO?',                          'C++ es compilado: necesita un compilador para convertir el código a ejecutable antes de correrlo. Python y JavaScript son interpretados.',                                           'opcion_multiple'),
(6,  2, 6,  '¿Cuál de estos es un lenguaje INTERPRETADO?',                       'Python es interpretado: su código se ejecuta línea por línea en tiempo real mediante un intérprete.',                                                                                'opcion_multiple'),
-- Módulo 3
(7,  3, 7,  '¿Qué es una variable en programación?',                             'Una variable es un espacio en memoria con un nombre que almacena un valor que puede cambiar durante la ejecución.',                                                                  'opcion_multiple'),
(8,  3, 8,  '¿Qué tipo de dato usarías para guardar el nombre de una persona?',  'El tipo String (cadena de texto) es el adecuado para almacenar texto como nombres.',                                                                                                  'opcion_multiple'),
(9,  3, 9,  '¿Cuál es la diferencia entre int y float?',                         'int almacena números enteros (sin decimales), float almacena números con punto decimal.',                                                                                             'opcion_multiple'),
-- Módulo 4
(10, 4, 10, '¿Qué hace la estructura IF en programación?',                       'IF evalúa una condición: si es verdadera ejecuta un bloque de código, si no, puede ejecutar el bloque ELSE.',                                                                        'opcion_multiple'),
(11, 4, 11, '¿Cuándo usarías un bucle WHILE en lugar de un FOR?',                'WHILE se usa cuando no sabes de antemano cuántas veces se repetirá el ciclo, ya que repite mientras la condición sea verdadera.',                                                   'opcion_multiple'),
(12, 4, 12, '¿Qué es un bucle infinito?',                                        'Un bucle infinito ocurre cuando la condición de parada nunca se vuelve falsa, haciendo que el programa se quede atascado.',                                                          'opcion_multiple'),
-- Módulo 5
(13, 5, 13, '¿Cuál es la ventaja principal de usar funciones?',                  'Las funciones permiten reutilizar código: defines la lógica una vez y la llamas cuantas veces necesites.',                                                                            'opcion_multiple'),
(14, 5, 14, '¿Qué hace la palabra clave return en una función?',                 'return devuelve un valor desde la función al lugar donde fue llamada y termina la ejecución de la función.',                                                                          'opcion_multiple'),
(15, 5, 15, '¿Cuál es la diferencia entre una función y un método?',             'Un método es una función que pertenece a un objeto o clase y se llama con la notación punto (objeto.metodo()).',                                                                     'opcion_multiple');

-- ── Opciones de respuesta ──────────────────────────────────────
CREATE TABLE `opcion` (
  `id_opcion`         INT(11)      NOT NULL AUTO_INCREMENT,
  `id_ejercicio`      INT(11)      NOT NULL,
  `texto`             VARCHAR(255) NOT NULL,
  `es_correcta`       TINYINT(1)   NOT NULL DEFAULT 0,
  `retroalimentacion` TEXT         DEFAULT NULL,
  PRIMARY KEY (`id_opcion`),
  KEY `id_ejercicio` (`id_ejercicio`),
  CONSTRAINT `opcion_ibfk_1` FOREIGN KEY (`id_ejercicio`) REFERENCES `ejercicios` (`id_ejercicio`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `opcion` (`id_ejercicio`, `texto`, `es_correcta`, `retroalimentacion`) VALUES
-- Ejercicio 1
(1, 'Un conjunto de reglas para escribir instrucciones que entiende la computadora', 1, '¡Correcto! Esa es la definición exacta.'),
(1, 'Un programa para navegar en internet',                                          0, 'No, eso describe un navegador web.'),
(1, 'Un tipo de hardware del computador',                                            0, 'No, los lenguajes son software, no hardware.'),
(1, 'Un sistema para almacenar archivos',                                            0, 'No, eso describe un sistema de archivos.'),
-- Ejercicio 2 (lección 2: Historia breve → pregunta sobre décadas)
(2, 'En los años 50', 1, '¡Correcto! Fortran fue creado en 1957.'),
(2, 'En los años 30', 0, 'En los 30 no existían computadoras programables modernas.'),
(2, 'En los años 70', 0, 'En los 70 ya existían varios lenguajes, pero los primeros de alto nivel fueron en los 50.'),
(2, 'En los años 90', 0, 'En los 90 surgieron lenguajes modernos como Java, pero los primeros fueron en los 50.'),
-- Ejercicio 3 (lección 3: ¿Para qué sirven? → pregunta sobre cuál es un lenguaje)
(3, 'Python',   1, '¡Correcto! Python es un lenguaje de programación muy popular.'),
(3, 'HTML',     0, 'HTML es un lenguaje de marcado para estructurar páginas web, no de programación.'),
(3, 'CSS',      0, 'CSS es un lenguaje de estilos, no de programación.'),
(3, 'Markdown', 0, 'Markdown es un lenguaje de formato de texto, no de programación.'),
-- Ejercicio 4
(4, 'El compilado traduce todo el código antes de ejecutarlo; el interpretado lo traduce línea a línea durante la ejecución', 1, '¡Correcto! Esa es la diferencia fundamental.'),
(4, 'El compilado es más lento que el interpretado',                                                                          0, 'Al contrario: los compilados suelen ser más rápidos en ejecución.'),
(4, 'No hay diferencia, ambos funcionan igual',                                                                               0, 'Sí hay diferencias importantes en velocidad y portabilidad.'),
(4, 'El interpretado solo funciona en Windows',                                                                               0, 'Los lenguajes interpretados funcionan en múltiples sistemas operativos.'),
-- Ejercicio 5
(5, 'C++',        1, '¡Correcto! C++ es un lenguaje compilado clásico.'),
(5, 'Python',     0, 'Python es interpretado, no compilado.'),
(5, 'JavaScript', 0, 'JavaScript es interpretado (aunque los motores modernos usan JIT).'),
(5, 'Ruby',       0, 'Ruby es un lenguaje interpretado.'),
-- Ejercicio 6
(6, 'Python', 1, '¡Correcto! Python es el ejemplo más conocido de lenguaje interpretado.'),
(6, 'C',      0, 'C es un lenguaje compilado.'),
(6, 'C++',    0, 'C++ es un lenguaje compilado.'),
(6, 'Go',     0, 'Go es un lenguaje compilado.'),
-- Ejercicio 7
(7, 'Un espacio en memoria con nombre que almacena un valor que puede cambiar', 1, '¡Correcto! Esa es la definición de variable.'),
(7, 'Un tipo de bucle que repite código',                                        0, 'Eso describe un bucle, no una variable.'),
(7, 'Una función que realiza cálculos',                                          0, 'Eso describe una función, no una variable.'),
(7, 'Un archivo donde se guarda el programa',                                    0, 'Eso describe un archivo de código fuente.'),
-- Ejercicio 8
(8, 'String (cadena de texto)',  1, '¡Correcto! String es el tipo adecuado para texto como nombres.'),
(8, 'int (entero)',              0, 'int es para números enteros, no para texto.'),
(8, 'float (decimal)',           0, 'float es para números decimales, no para texto.'),
(8, 'boolean (verdadero/falso)', 0, 'boolean solo puede ser true o false, no texto.'),
-- Ejercicio 9
(9, 'int almacena enteros sin decimales; float almacena números con punto decimal', 1, '¡Correcto! Esa es la diferencia clave.'),
(9, 'int es más grande que float',                                                   0, 'No es cuestión de tamaño sino de tipo de número.'),
(9, 'float es para texto y int para números',                                        0, 'Ambos son para números; la diferencia es si tienen decimales o no.'),
(9, 'Son exactamente lo mismo',                                                      0, 'No, tienen diferencias importantes en precisión y uso de memoria.'),
-- Ejercicio 10
(10, 'Evalúa una condición y ejecuta un bloque de código solo si es verdadera', 1, '¡Correcto! IF toma decisiones basadas en condiciones.'),
(10, 'Repite un bloque de código varias veces',                                  0, 'Eso describe un bucle (for/while), no un IF.'),
(10, 'Define una función reutilizable',                                           0, 'Eso describe la declaración de una función.'),
(10, 'Importa librerías externas',                                                0, 'Eso lo hace import/require, no IF.'),
-- Ejercicio 11
(11, 'Cuando no sabes de antemano cuántas veces se repetirá el ciclo', 1, '¡Correcto! WHILE es ideal cuando la cantidad de iteraciones depende de una condición dinámica.'),
(11, 'Cuando quieres repetir exactamente 10 veces',                     0, 'Para un número fijo de repeticiones, FOR es más apropiado.'),
(11, 'Cuando quieres recorrer una lista',                               0, 'Para recorrer listas, FOR es más natural y legible.'),
(11, 'Nunca, FOR siempre es mejor',                                     0, 'WHILE tiene casos de uso específicos donde es la mejor opción.'),
-- Ejercicio 12
(12, 'Un bucle cuya condición de parada nunca se vuelve falsa, haciendo que el programa se quede atascado', 1, '¡Correcto! Los bucles infinitos congelan el programa.'),
(12, 'Un bucle que se repite exactamente infinito veces y luego para',                                       0, 'Un bucle infinito nunca para por sí solo.'),
(12, 'Un bucle muy rápido',                                                                                   0, 'La velocidad no define si un bucle es infinito.'),
(12, 'Un bucle que solo funciona con números grandes',                                                        0, 'Los bucles infinitos no tienen relación con el tamaño de los números.'),
-- Ejercicio 13
(13, 'Permite reutilizar código: defines la lógica una vez y la llamas cuantas veces necesites', 1, '¡Correcto! La reutilización es la ventaja principal.'),
(13, 'Hace que el programa sea más lento',                                                        0, 'Las funciones no hacen el programa más lento; al contrario, lo organizan mejor.'),
(13, 'Solo sirven para hacer cálculos matemáticos',                                               0, 'Las funciones pueden hacer cualquier tipo de tarea, no solo matemáticas.'),
(13, 'Reemplazan completamente a los bucles',                                                     0, 'Las funciones y los bucles son herramientas diferentes con propósitos distintos.'),
-- Ejercicio 14
(14, 'Devuelve un valor desde la función al lugar donde fue llamada y termina su ejecución', 1, '¡Correcto! return es la forma de obtener resultados de una función.'),
(14, 'Imprime un valor en la pantalla',                                                       0, 'Para imprimir se usa print() o console.log(), no return.'),
(14, 'Declara una nueva variable',                                                            0, 'return no declara variables, devuelve valores.'),
(14, 'Inicia un bucle dentro de la función',                                                  0, 'return no tiene relación con bucles.'),
-- Ejercicio 15
(15, 'Un método pertenece a un objeto/clase y se llama con punto (objeto.metodo()); una función es independiente', 1, '¡Correcto! Esa es la distinción clave entre función y método.'),
(15, 'No hay diferencia, son exactamente lo mismo',                                                               0, 'Sí hay diferencia: los métodos están asociados a objetos.'),
(15, 'Las funciones son más rápidas que los métodos',                                                             0, 'La velocidad no es la diferencia entre función y método.'),
(15, 'Los métodos solo existen en Python',                                                                        0, 'Los métodos existen en todos los lenguajes orientados a objetos.');

-- ── Progreso ───────────────────────────────────────────────────
CREATE TABLE `progreso` (
  `id_progreso`    INT(11)      NOT NULL AUTO_INCREMENT,
  `id_usuario`     INT(11)      NOT NULL,
  `id_ejercicio`   INT(11)      NOT NULL,
  `intentos`       INT(11)      DEFAULT 0,
  `calificacion`   DECIMAL(5,2) DEFAULT NULL,
  `completado`     TINYINT(1)   DEFAULT 0,
  `fecha_progreso` DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_progreso`),
  UNIQUE KEY `unico_progreso` (`id_usuario`, `id_ejercicio`),
  KEY `id_usuario`   (`id_usuario`),
  KEY `id_ejercicio` (`id_ejercicio`),
  CONSTRAINT `progreso_ibfk_1` FOREIGN KEY (`id_usuario`)   REFERENCES `usuario`    (`id_usuario`)   ON DELETE CASCADE,
  CONSTRAINT `progreso_ibfk_2` FOREIGN KEY (`id_ejercicio`) REFERENCES `ejercicios` (`id_ejercicio`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;
