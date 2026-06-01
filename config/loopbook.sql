-- ============================================================
-- Loopbook — Base de datos completa
-- Importar en phpMyAdmin: BD loopbook > Importar > este archivo
-- Un solo archivo, todo incluido, listo para usar.
-- ============================================================

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
  `is_admin`       TINYINT(1)   NOT NULL DEFAULT 0,
  `fecha_registro` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Usuarios de prueba (contraseñas: prueba123 / test123)
-- prueba tiene is_admin=1 para acceder al panel de administración
INSERT INTO `usuario` (`id_usuario`, `nombre`, `avatar`, `usuario`, `correo`, `contraseña`, `is_admin`, `fecha_registro`) VALUES
(1, 'Prueba', '👤', 'prueba', 'prueba@gmail.com', '$2y$10$AVQpnPEoIjBynjowNUj95e2y6J8CF/QavadIvA8MGe8IbMVB9mSNW', 1, '2026-04-22 22:58:24'),
(2, 'Test',   '👤', 'test',   't@gmail.com',      '$2y$10$POvcAnwcJ1GlpoitPbSDPuN7OrGkv6KCoz6/7cT5DqB8ljkWL7jYa', 0, '2026-04-22 23:27:53');

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
(1, 1, '¿Qué son los Lenguajes de Programación?', 'Definición, historia desde Ada Lovelace hasta los lenguajes modernos, y clasificación por propósito y paradigma', 1),
(2, 1, 'Lenguajes Compilados vs Interpretados',   'Cómo funciona cada enfoque, sus ventajas y desventajas, y los enfoques híbridos como Java y JIT',              2),
(3, 1, 'Variables y Tipos de Datos',              'Cómo funciona la memoria, tipos primitivos y compuestos, alcance de variables y errores comunes',               3),
(4, 1, 'Estructuras de Control',                  'Condicionales IF/ELSE, operador ternario, bucles FOR/WHILE/DO-WHILE, break, continue y bucles infinitos',        4),
(5, 1, 'Funciones y Métodos',                     'Principio DRY, parámetros con valores por defecto, return, funciones puras y diferencia con métodos',           5);

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
-- ── Módulo 1: ¿Qué son los Lenguajes de Programación? ─────────
(1,  1, '¿Qué es un lenguaje de programación?',
 'Un lenguaje de programación es un sistema formal compuesto por un conjunto de reglas sintácticas (cómo se escribe el código) y semánticas (qué significa lo que se escribe) que permite a los programadores comunicar instrucciones precisas a una computadora. A diferencia del lenguaje humano, que tolera ambigüedades, los lenguajes de programación son completamente exactos: una coma mal puesta puede cambiar el comportamiento de todo un programa. Existen dos niveles: los lenguajes de bajo nivel (como el ensamblador) que se acercan al lenguaje máquina, y los de alto nivel (como Python o Java) que se acercan al lenguaje humano y son más fáciles de aprender. Todo el software que usamos —desde una app de mensajería hasta un sistema bancario— fue escrito en algún lenguaje de programación.',
 'texto', 1),
(2,  1, 'Historia y evolución de los lenguajes',
 'La historia de los lenguajes de programación comienza en 1843, cuando Ada Lovelace escribió el primer algoritmo pensado para ser ejecutado por una máquina. Sin embargo, los lenguajes modernos nacen en los años 50: Fortran (1957), creado por IBM para cálculos científicos, fue el primer lenguaje de alto nivel ampliamente adoptado. En 1959 llegó COBOL, diseñado para negocios y aún usado en sistemas bancarios hoy. En los 70 surgió C, que influyó en casi todos los lenguajes posteriores. Los 80 trajeron C++ (programación orientada a objetos) y los 90 vieron nacer Java (multiplataforma) y Python (simplicidad). En los 2000 llegaron lenguajes como Ruby, C# y PHP. Hoy, lenguajes como Kotlin, Swift, TypeScript y Rust representan la evolución más reciente, cada uno resolviendo problemas específicos de la industria.',
 'texto', 2),
(3,  1, 'Clasificación y usos de los lenguajes',
 'Los lenguajes de programación se clasifican según su propósito y paradigma. Por propósito: los de propósito general (Python, Java, C++) sirven para casi cualquier tarea; los de propósito específico se especializan en un área (SQL para bases de datos, HTML/CSS para estructura y estilo web, R para estadística, MATLAB para matemáticas). Por paradigma: los imperativos indican paso a paso qué hacer (C, Pascal); los orientados a objetos organizan el código en objetos con datos y comportamiento (Java, Python, C++); los funcionales tratan el código como funciones matemáticas sin efectos secundarios (Haskell, Erlang, partes de JavaScript). Elegir el lenguaje correcto depende del proyecto: Python domina en ciencia de datos e IA, JavaScript en desarrollo web, Swift en apps iOS, Kotlin en Android, y C/C++ en sistemas embebidos y videojuegos de alto rendimiento.',
 'texto', 3),
-- ── Módulo 2: Lenguajes Compilados vs Interpretados ───────────
(4,  2, '¿Qué es un lenguaje compilado?',
 'Un lenguaje compilado es aquel cuyo código fuente (el que escribe el programador) se traduce completamente a código máquina ANTES de ejecutarse, mediante un programa especializado llamado compilador. Este proceso genera un archivo ejecutable (.exe en Windows, binario en Linux/Mac) que la CPU puede correr directamente sin necesidad de ningún programa adicional. La ventaja principal es la velocidad: al estar ya traducido, el programa corre muy rápido. La desventaja es que el ejecutable generado es específico para un sistema operativo y arquitectura de procesador; si compilas en Windows para x86, ese ejecutable no correrá en Mac o Linux sin recompilarlo. Ejemplos clásicos: C (sistemas operativos, drivers), C++ (videojuegos, motores gráficos), Go (servidores de alto rendimiento), Rust (sistemas seguros y rápidos).',
 'texto', 1),
(5,  2, '¿Qué es un lenguaje interpretado?',
 'Un lenguaje interpretado es aquel cuyo código fuente se traduce y ejecuta línea por línea EN TIEMPO REAL, mediante un programa llamado intérprete. No se genera un ejecutable previo: cada vez que corres el programa, el intérprete lee el código y lo ejecuta al momento. Esto tiene ventajas importantes: es más fácil depurar (ver errores línea a línea), el mismo código corre en cualquier sistema que tenga el intérprete instalado (alta portabilidad), y el ciclo de desarrollo es más rápido porque no hay que compilar. La desventaja es que suele ser más lento que un compilado, ya que la traducción ocurre en tiempo de ejecución. Ejemplos: Python (ciencia de datos, automatización), JavaScript (web), PHP (servidores web), Ruby (aplicaciones web), Bash (scripts de sistema).',
 'texto', 2),
(6,  2, 'Enfoques híbridos y comparación práctica',
 'La distinción compilado/interpretado no siempre es absoluta. Java usa un enfoque híbrido: el compilador de Java traduce el código a "bytecode" (un formato intermedio), y luego la Máquina Virtual de Java (JVM) interpreta ese bytecode en cualquier sistema. Esto le da portabilidad ("escribe una vez, corre en cualquier lugar") con mejor rendimiento que un interpretado puro. Python también compila a bytecode (.pyc) antes de interpretar. Los motores modernos de JavaScript (como V8 de Google Chrome) usan compilación JIT (Just-In-Time): compilan partes del código en tiempo real para acelerar la ejecución. Resumen práctico: usa lenguajes compilados cuando necesitas máximo rendimiento (videojuegos, sistemas operativos, drivers); usa interpretados cuando priorizas velocidad de desarrollo, portabilidad y facilidad de depuración (scripts, web, prototipado).',
 'texto', 3),
-- ── Módulo 3: Variables y Tipos de Datos ─────────────────────
(7,  3, '¿Qué es una variable y cómo funciona en memoria?',
 'Una variable es un nombre simbólico que hace referencia a un espacio en la memoria RAM del computador donde se almacena un valor. Cuando declaras una variable, el sistema operativo reserva un bloque de memoria y asocia ese nombre a esa dirección. Imagínala como una caja etiquetada: la etiqueta es el nombre de la variable, y dentro de la caja está el valor. Lo que hace especial a una variable es que su valor puede cambiar durante la ejecución del programa (de ahí el nombre "variable"). Existen también las constantes, que son como variables pero cuyo valor no puede cambiar una vez asignado (en Python se usan por convención en MAYÚSCULAS; en JavaScript se declaran con const). Las variables tienen tres características clave: nombre (identificador), tipo (qué clase de dato almacena) y valor (el dato en sí).',
 'texto', 1),
(8,  3, 'Tipos de datos: primitivos y compuestos',
 'Los tipos de datos definen qué clase de información puede almacenar una variable y cuánta memoria ocupa. Tipos primitivos (básicos): int (enteros: -5, 0, 42), float/double (decimales: 3.14, -0.5), char (un solo carácter: "A"), boolean (solo true o false), string (cadena de texto: "Hola mundo"). Tipos compuestos (estructuras): arrays/listas (colección ordenada de valores del mismo tipo), objetos/diccionarios (pares clave-valor), tuplas (colección inmutable). La diferencia entre int y float es importante: int ocupa menos memoria y es exacto; float puede representar decimales pero tiene limitaciones de precisión (0.1 + 0.2 en float no da exactamente 0.3 por cómo se representan en binario). Elegir el tipo correcto mejora el rendimiento y evita errores difíciles de detectar.',
 'texto', 2),
(9,  3, 'Declaración, asignación y alcance de variables',
 'Declarar una variable es anunciar su existencia; asignarle un valor es guardar un dato en ella. En lenguajes de tipado estático (Java, C, C++) debes declarar el tipo explícitamente: int edad = 25; String nombre = "Ana";. En lenguajes de tipado dinámico (Python, JavaScript) el tipo se infiere automáticamente: edad = 25 (Python), let nombre = "Ana" (JavaScript). El alcance (scope) define desde dónde es accesible una variable: una variable local solo existe dentro de la función donde se declaró; una variable global existe en todo el programa. En JavaScript, var tiene alcance de función, mientras que let y const tienen alcance de bloque (más seguro). Una buena práctica es usar nombres descriptivos (precioTotal en lugar de x) y preferir el alcance más restringido posible para evitar errores inesperados.',
 'texto', 3),
-- ── Módulo 4: Estructuras de Control ─────────────────────────
(10, 4, 'Condicionales: IF, ELSE IF y ELSE',
 'Las estructuras condicionales permiten que un programa tome decisiones y ejecute diferentes bloques de código según si una condición es verdadera o falsa. La estructura básica es: IF (condición) → ejecuta bloque A; ELSE → ejecuta bloque B. Para múltiples condiciones se usa ELSE IF (o ELIF en Python). Ejemplo real: un sistema de calificaciones evalúa si nota >= 90 → "Excelente", else if nota >= 70 → "Aprobado", else → "Reprobado". Las condiciones usan operadores de comparación (==, !=, <, >, <=, >=) y operadores lógicos (AND, OR, NOT). Un error común es confundir = (asignación) con == (comparación): escribir if (x = 5) en lugar de if (x == 5) puede causar bugs difíciles de detectar. Algunos lenguajes también ofrecen el operador ternario: resultado = "mayor" if edad >= 18 else "menor" (Python), que es una forma compacta de escribir un IF-ELSE simple.',
 'texto', 1),
(11, 4, 'Bucles: FOR, WHILE y DO-WHILE',
 'Los bucles (o ciclos) permiten repetir un bloque de código múltiples veces sin escribirlo repetidamente. El bucle FOR es ideal cuando sabes de antemano cuántas veces repetir: "for i in range(5)" en Python repite 5 veces. También se usa para recorrer colecciones: "for fruta in lista_frutas". El bucle WHILE repite mientras una condición sea verdadera, sin importar cuántas veces: útil cuando el número de iteraciones depende de una condición dinámica (por ejemplo, seguir pidiendo contraseña hasta que sea correcta). El bucle DO-WHILE (en C, Java, JavaScript) garantiza que el bloque se ejecute AL MENOS una vez antes de verificar la condición. Dentro de cualquier bucle, break termina el bucle inmediatamente, y continue salta a la siguiente iteración sin ejecutar el resto del bloque. Elegir el bucle correcto hace el código más legible y menos propenso a errores.',
 'texto', 2),
(12, 4, 'Bucles infinitos, break y buenas prácticas',
 'Un bucle infinito ocurre cuando la condición de parada nunca se vuelve falsa, haciendo que el programa se quede ejecutando para siempre y consuma toda la CPU. Ejemplo clásico: while (true) { } sin un break interno. Causas comunes: olvidar incrementar el contador (i++ en un for), usar la condición incorrecta, o modificar la variable equivocada. Para evitarlos: siempre verifica que la variable de control cambie en cada iteración, usa un contador máximo de seguridad, o incluye un break con condición de salida. Sin embargo, los bucles infinitos tienen usos legítimos: servidores web, juegos y sistemas embebidos usan while(true) intencionalmente para mantenerse activos, controlando la salida con break o señales del sistema. La instrucción break sale del bucle inmediatamente; continue salta al inicio de la siguiente iteración. Usar estas instrucciones con moderación hace el código más claro.',
 'texto', 3),
-- ── Módulo 5: Funciones y Métodos ────────────────────────────
(13, 5, '¿Qué es una función y por qué usarlas?',
 'Una función es un bloque de código con nombre que encapsula una tarea específica y puede ejecutarse (invocarse) cuantas veces sea necesario desde cualquier parte del programa. El principio fundamental detrás de las funciones es DRY: "Don\'t Repeat Yourself" (No te repitas). Sin funciones, si necesitas calcular el área de un círculo en 10 partes del programa, escribirías la misma fórmula 10 veces; con una función, la escribes una vez y la llamas 10 veces. Las funciones también mejoran la legibilidad: un código dividido en funciones bien nombradas (calcularImpuesto(), enviarCorreo(), validarContraseña()) es mucho más fácil de entender que un bloque monolítico de 500 líneas. Además, facilitan el mantenimiento: si la lógica cambia, solo modificas la función, no cada lugar donde se usaba. En programación, una función que hace exactamente una cosa y la hace bien se llama función de responsabilidad única.',
 'texto', 1),
(14, 5, 'Parámetros, argumentos y valor de retorno',
 'Los parámetros son las variables que una función declara para recibir datos de entrada; los argumentos son los valores concretos que se pasan al llamar la función. Ejemplo: en def sumar(a, b), "a" y "b" son parámetros; en sumar(3, 5), "3" y "5" son argumentos. Una función puede tener cero parámetros (saludar()), uno o varios. La palabra clave return devuelve un valor al lugar donde se llamó la función y termina su ejecución inmediatamente. Si una función no tiene return (o tiene return sin valor), devuelve None/null/undefined según el lenguaje. Los parámetros con valor por defecto permiten llamar la función sin pasar ese argumento: def saludar(nombre="mundo") puede llamarse como saludar() o saludar("Ana"). En Python también existen *args (número variable de argumentos posicionales) y **kwargs (argumentos con nombre), que dan gran flexibilidad a las funciones.',
 'texto', 2),
(15, 5, 'Funciones vs Métodos y el concepto de scope',
 'Una función es independiente: se define en el nivel global o de módulo y se llama directamente por su nombre (calcular(), imprimir()). Un método es una función que pertenece a una clase u objeto y se llama usando la notación punto: objeto.metodo(). En Python, "hola".upper() llama al método upper() del objeto string "hola"; len("hola") llama a la función independiente len(). En Java y C#, casi todo son métodos porque el código se organiza en clases. El scope (alcance) de una función define qué variables puede ver: las variables locales solo existen dentro de la función; las globales son accesibles desde cualquier lugar pero deben usarse con cuidado para evitar efectos secundarios. Las funciones puras son aquellas que, dado el mismo input, siempre producen el mismo output y no modifican nada fuera de ellas (sin efectos secundarios). Son más fáciles de probar y depurar, y son la base de la programación funcional.',
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
-- ── Módulo 1, Lección 1: ¿Qué es un lenguaje de programación? ──
(1,  1, 1,
 '¿Cuál es la diferencia entre la sintaxis y la semántica de un lenguaje de programación?',
 'La sintaxis define las reglas de escritura (cómo se escribe el código); la semántica define el significado (qué hace ese código). Puedes tener código sintácticamente correcto pero semánticamente incorrecto: por ejemplo, sumar un número a un texto puede ser válido en sintaxis pero sin sentido semántico.',
 'opcion_multiple'),
-- ── Módulo 1, Lección 2: Historia y evolución ──────────────────
(2,  1, 2,
 '¿Cuál fue el primer lenguaje de programación de alto nivel ampliamente adoptado y en qué año fue creado?',
 'Fortran (FORmula TRANslation) fue creado por IBM en 1957 y se convirtió en el primer lenguaje de alto nivel de uso masivo. Fue diseñado para cálculos científicos y matemáticos, y su influencia se siente hasta hoy en lenguajes como Python y MATLAB.',
 'opcion_multiple'),
-- ── Módulo 1, Lección 3: Clasificación y usos ─────────────────
(3,  1, 3,
 'Un desarrollador necesita analizar grandes conjuntos de datos estadísticos y crear modelos de machine learning. ¿Qué lenguaje sería más adecuado?',
 'Python es el lenguaje dominante en ciencia de datos e inteligencia artificial gracias a bibliotecas como NumPy, Pandas, Scikit-learn y TensorFlow. R también es válido para estadística pura, pero Python tiene un ecosistema más amplio para machine learning.',
 'opcion_multiple'),
-- ── Módulo 2, Lección 4: Lenguajes compilados ─────────────────
(4,  2, 4,
 '¿Por qué un programa compilado en Windows generalmente NO puede ejecutarse directamente en Linux sin recompilarlo?',
 'El compilador genera código máquina específico para la arquitectura del procesador y el sistema operativo destino. Windows y Linux tienen formatos de ejecutable diferentes (PE vs ELF) y llamadas al sistema distintas. Por eso el mismo código fuente debe compilarse por separado para cada plataforma.',
 'opcion_multiple'),
-- ── Módulo 2, Lección 5: Lenguajes interpretados ──────────────
(5,  2, 5,
 '¿Cuál es la principal ventaja de un lenguaje interpretado sobre uno compilado durante el desarrollo de software?',
 'La principal ventaja es la velocidad del ciclo de desarrollo: puedes escribir código y ejecutarlo inmediatamente sin esperar una compilación. Además, los errores se muestran línea a línea, facilitando la depuración. Esto hace que los lenguajes interpretados sean ideales para prototipado rápido y scripting.',
 'opcion_multiple'),
-- ── Módulo 2, Lección 6: Enfoques híbridos ────────────────────
(6,  2, 6,
 'Java compila el código a "bytecode" que luego ejecuta la JVM. ¿Qué ventaja principal ofrece este enfoque híbrido?',
 'El bytecode de Java es independiente de la plataforma: se compila una sola vez y puede ejecutarse en cualquier sistema que tenga la JVM instalada (Windows, Linux, Mac). Esto cumple el principio "Write Once, Run Anywhere" (escribe una vez, corre en cualquier lugar), combinando portabilidad con mejor rendimiento que un interpretado puro.',
 'opcion_multiple'),
-- ── Módulo 3, Lección 7: Variables y memoria ──────────────────
(7,  3, 7,
 '¿Cuál es la diferencia fundamental entre una variable y una constante en programación?',
 'Una variable puede cambiar su valor durante la ejecución del programa; una constante mantiene el mismo valor desde que se asigna hasta que el programa termina. Las constantes se usan para valores que no deben cambiar (como PI = 3.14159 o MAX_INTENTOS = 3) y ayudan a evitar errores accidentales de modificación.',
 'opcion_multiple'),
-- ── Módulo 3, Lección 8: Tipos de datos ───────────────────────
(8,  3, 8,
 'En Python, al ejecutar: print(0.1 + 0.2), el resultado NO es exactamente 0.3. ¿Por qué ocurre esto?',
 'Los números float se almacenan en binario (base 2) y algunos decimales como 0.1 no tienen representación exacta en binario, igual que 1/3 no tiene representación exacta en decimal. Esto genera pequeños errores de redondeo. Para cálculos financieros precisos se usa el tipo Decimal, que evita este problema.',
 'opcion_multiple'),
-- ── Módulo 3, Lección 9: Declaración y alcance ────────────────
(9,  3, 9,
 '¿Cuál es la diferencia entre var, let y const en JavaScript?',
 'var tiene alcance de función y puede redeclararse (comportamiento confuso). let tiene alcance de bloque y puede reasignarse pero no redeclararse. const tiene alcance de bloque y no puede reasignarse ni redeclararse. La práctica moderna recomienda usar const por defecto y let cuando necesitas reasignar; evitar var.',
 'opcion_multiple'),
-- ── Módulo 4, Lección 10: Condicionales ───────────────────────
(10, 4, 10,
 'En el siguiente código Python: x = 5; resultado = "positivo" if x > 0 else "no positivo". ¿Qué tipo de estructura es esta?',
 'Es el operador ternario (o expresión condicional) de Python. Es una forma compacta de escribir un IF-ELSE en una sola línea. La sintaxis es: valor_si_verdadero if condición else valor_si_falso. Es útil para asignaciones simples, pero para lógica compleja es mejor usar IF-ELSE tradicional para mantener la legibilidad.',
 'opcion_multiple'),
-- ── Módulo 4, Lección 11: Bucles FOR y WHILE ──────────────────
(11, 4, 11,
 'Necesitas pedir al usuario que ingrese una contraseña y seguir pidiendo hasta que sea correcta. ¿Qué estructura de bucle es más apropiada?',
 'El bucle WHILE es el más apropiado porque no sabes de antemano cuántos intentos necesitará el usuario. La condición "mientras la contraseña sea incorrecta, seguir pidiendo" es exactamente el caso de uso de WHILE. Un FOR no sería natural aquí porque implicaría un número fijo de intentos.',
 'opcion_multiple'),
-- ── Módulo 4, Lección 12: Bucles infinitos ────────────────────
(12, 4, 12,
 '¿En cuál de estos casos un bucle while(True) es un uso LEGÍTIMO y correcto?',
 'Los servidores web, juegos y sistemas embebidos usan while(True) intencionalmente para mantenerse activos y procesar eventos continuamente. El bucle se controla con break cuando llega una señal de cierre o condición de salida. Este patrón se llama "event loop" y es fundamental en programación de sistemas y videojuegos.',
 'opcion_multiple'),
-- ── Módulo 5, Lección 13: ¿Qué es una función? ────────────────
(13, 5, 13,
 '¿Qué principio de programación promueve el uso de funciones para evitar duplicar código?',
 'El principio DRY: "Don\'t Repeat Yourself" (No te repitas). Establece que cada pieza de conocimiento o lógica debe tener una única representación en el sistema. Su opuesto es WET: "Write Everything Twice". Las funciones son la herramienta principal para aplicar DRY: defines la lógica una vez y la reutilizas cuantas veces necesites.',
 'opcion_multiple'),
-- ── Módulo 5, Lección 14: Parámetros y retorno ────────────────
(14, 5, 14,
 'En Python: def saludar(nombre="mundo"): return "Hola, " + nombre. ¿Qué ocurre si llamas saludar() sin argumentos?',
 'La función usa el valor por defecto del parámetro: devuelve "Hola, mundo". Los parámetros con valor por defecto son opcionales al llamar la función. Si llamas saludar("Ana"), devuelve "Hola, Ana". Esta característica hace las funciones más flexibles y fáciles de usar sin necesidad de pasar todos los argumentos siempre.',
 'opcion_multiple'),
-- ── Módulo 5, Lección 15: Funciones vs Métodos ────────────────
(15, 5, 15,
 '¿Qué característica define a una "función pura" en programación?',
 'Una función pura tiene dos características: (1) dado el mismo input, siempre produce el mismo output; (2) no tiene efectos secundarios (no modifica variables externas, no escribe en disco, no hace llamadas de red). Son más fáciles de probar, depurar y razonar. Son la base de la programación funcional. Ejemplo puro: sumar(a,b) → a+b. Ejemplo impuro: una función que modifica una variable global.',
 'opcion_multiple');

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
-- ── Ejercicio 1: Sintaxis vs Semántica ────────────────────────
(1, 'La sintaxis define cómo se escribe el código; la semántica define qué significa y qué hace',                    1, '¡Correcto! Sintaxis = reglas de escritura (como la gramática); semántica = significado (como el vocabulario). Ambas son necesarias para que el código sea válido y correcto.'),
(1, 'La sintaxis es para lenguajes compilados y la semántica para interpretados',                                    0, 'Incorrecto. Todos los lenguajes tienen tanto sintaxis como semántica, independientemente de si son compilados o interpretados.'),
(1, 'Son sinónimos: ambas se refieren a las reglas del lenguaje',                                                    0, 'No son sinónimos. La sintaxis trata la forma (estructura del código); la semántica trata el fondo (qué hace ese código). Son conceptos distintos y complementarios.'),
(1, 'La sintaxis es el conjunto de bibliotecas disponibles y la semántica es la velocidad de ejecución',             0, 'Incorrecto. Las bibliotecas son herramientas externas, no parte de la sintaxis. La velocidad tampoco define la semántica.'),
-- ── Ejercicio 2: Fortran y la historia ────────────────────────
(2, 'Fortran, creado en 1957 por IBM para cálculos científicos',                                                     1, '¡Correcto! Fortran (FORmula TRANslation) fue el primer lenguaje de alto nivel de uso masivo. Su legado es enorme: influyó en casi todos los lenguajes numéricos posteriores y aún se usa en computación científica.'),
(2, 'COBOL, creado en 1959 para aplicaciones de negocios',                                                           0, 'COBOL fue muy importante (y aún se usa en bancos), pero Fortran lo precedió en 1957. COBOL fue el primer lenguaje orientado a negocios, no el primero de alto nivel en general.'),
(2, 'C, creado en 1972 en los laboratorios Bell',                                                                    0, 'C es un lenguaje fundamental que influyó en casi todo lo que vino después, pero fue creado en 1972, 15 años después de Fortran. No fue el primero de alto nivel.'),
(2, 'Ada, creado en 1843 por Ada Lovelace',                                                                          0, 'Ada Lovelace escribió el primer algoritmo en 1843, pero no existían computadoras que lo ejecutaran. El lenguaje Ada (nombrado en su honor) fue creado en 1980. No fue el primer lenguaje de alto nivel adoptado masivamente.'),
-- ── Ejercicio 3: Lenguaje para ciencia de datos ───────────────
(3, 'Python, por su ecosistema de bibliotecas como NumPy, Pandas y TensorFlow',                                      1, '¡Correcto! Python domina en ciencia de datos e IA gracias a su ecosistema: NumPy (cálculo numérico), Pandas (análisis de datos), Matplotlib (visualización), Scikit-learn (machine learning) y TensorFlow/PyTorch (deep learning).'),
(3, 'JavaScript, porque es el lenguaje más popular del mundo',                                                       0, 'JavaScript es muy popular para desarrollo web, pero su ecosistema para ciencia de datos y machine learning es muy limitado comparado con Python. La popularidad general no implica idoneidad para todas las tareas.'),
(3, 'C++, porque es el lenguaje más rápido',                                                                         0, 'C++ es muy rápido, pero su complejidad lo hace poco práctico para análisis de datos interactivo. Irónicamente, muchas bibliotecas de Python (como NumPy) están implementadas en C++ internamente para aprovechar esa velocidad.'),
(3, 'HTML, porque es el lenguaje más usado en la web',                                                               0, 'HTML es un lenguaje de marcado para estructurar páginas web, no un lenguaje de programación. No puede realizar cálculos, análisis de datos ni machine learning.'),
-- ── Ejercicio 4: Portabilidad de compilados ─────────────────
(4, 'El compilador genera código máquina específico para cada sistema operativo y arquitectura de procesador',        1, '¡Correcto! Cada SO tiene su propio formato de ejecutable (PE en Windows, ELF en Linux, Mach-O en Mac) y sus propias llamadas al sistema. Por eso el mismo código fuente debe compilarse por separado para cada plataforma objetivo.'),
(4, 'Porque Windows usa más memoria RAM que Linux',                                                                   0, 'El uso de RAM no tiene relación con la compatibilidad de ejecutables. El problema es la diferencia en formatos de archivo ejecutable y llamadas al sistema entre plataformas.'),
(4, 'Porque los programas compilados son más lentos en Linux',                                                       0, 'Los programas compilados no son más lentos en Linux; de hecho, Linux es conocido por su eficiencia. El problema es de compatibilidad de formato, no de velocidad.'),
(4, 'Porque Linux no puede ejecutar programas, solo interpretarlos',                                                  0, 'Incorrecto. Linux ejecuta perfectamente programas compilados (de hecho, el kernel de Linux está escrito en C compilado). El problema es que el ejecutable compilado para Windows no es compatible con el formato de Linux.'),
-- ── Ejercicio 5: Ventaja de interpretados en desarrollo ──────
(5, 'Permite ejecutar el código inmediatamente sin compilar, acelerando el ciclo de desarrollo y depuración',         1, '¡Correcto! En lenguajes interpretados puedes escribir una línea y ejecutarla al instante. Esto es ideal para explorar ideas, depurar errores y prototipar rápidamente. Python, por ejemplo, tiene un REPL (Read-Eval-Print Loop) donde puedes probar código interactivamente.'),
(5, 'Los programas interpretados siempre son más rápidos en ejecución',                                              0, 'Al contrario: los compilados suelen ser más rápidos en ejecución porque ya están traducidos a código máquina. La ventaja de los interpretados está en la velocidad de DESARROLLO, no de ejecución.'),
(5, 'Los lenguajes interpretados no tienen errores de sintaxis',                                                      0, 'Todos los lenguajes tienen errores de sintaxis. La diferencia es que en los interpretados los errores se detectan línea a línea durante la ejecución, mientras que en los compilados se detectan todos antes de ejecutar.'),
(5, 'Los lenguajes interpretados generan ejecutables más pequeños',                                                   0, 'Los lenguajes interpretados generalmente no generan ejecutables independientes; necesitan el intérprete instalado para correr. No es una cuestión de tamaño de archivo.'),
-- ── Ejercicio 6: Enfoque híbrido de Java ─────────────────────
(6, 'Portabilidad: el bytecode corre en cualquier sistema con JVM instalada, sin recompilar',                         1, '¡Correcto! Este es el principio "Write Once, Run Anywhere" de Java. El bytecode es un formato intermedio independiente de la plataforma. La JVM (disponible para Windows, Linux, Mac, Android) lo ejecuta en cada sistema. Esto fue revolucionario en los 90 cuando Java fue creado.'),
(6, 'El bytecode es más rápido que el código máquina nativo',                                                        0, 'El código máquina nativo (de lenguajes compilados como C++) es generalmente más rápido que el bytecode ejecutado por la JVM, aunque la JVM moderna con compilación JIT reduce significativamente esta diferencia.'),
(6, 'Java no necesita instalación en ningún sistema',                                                                 0, 'Java requiere que la JVM esté instalada en el sistema para ejecutar programas. Precisamente la JVM es lo que permite la portabilidad, pero sí necesita estar presente.'),
(6, 'El bytecode de Java solo funciona en Windows',                                                                   0, 'Todo lo contrario: la portabilidad multiplataforma es la razón de ser del bytecode. La JVM existe para Windows, Linux, macOS, Android y muchos otros sistemas.'),
-- ── Ejercicio 7: Variable vs Constante ──────────────────────
(7, 'Una variable puede cambiar su valor; una constante mantiene el mismo valor durante toda la ejecución',           1, '¡Correcto! Las constantes se usan para valores que no deben cambiar: PI = 3.14159, MAX_USUARIOS = 100, TASA_IVA = 0.16. Usar constantes en lugar de "números mágicos" hace el código más legible y fácil de mantener.'),
(7, 'Una variable almacena texto y una constante almacena números',                                                   0, 'Incorrecto. Tanto variables como constantes pueden almacenar cualquier tipo de dato: texto, números, booleanos, etc. La diferencia es si el valor puede cambiar, no qué tipo de dato almacenan.'),
(7, 'Las constantes son más rápidas que las variables',                                                               0, 'La diferencia de rendimiento es mínima o nula. La razón para usar constantes es la seguridad del código (evitar modificaciones accidentales) y la legibilidad, no la velocidad.'),
(7, 'Las variables solo existen en lenguajes de alto nivel',                                                          0, 'Las variables existen en todos los niveles de programación, incluyendo lenguaje ensamblador (donde se llaman registros o posiciones de memoria). Son un concepto fundamental universal.'),
-- ── Ejercicio 8: Error de punto flotante ─────────────────────
(8, 'Los decimales como 0.1 no tienen representación exacta en binario, causando pequeños errores de redondeo',       1, '¡Correcto! En base 2 (binario), 0.1 es una fracción periódica infinita, igual que 1/3 en base 10. La computadora almacena una aproximación, y al sumar varias aproximaciones el error se acumula. Para dinero y finanzas se usa el tipo Decimal que trabaja en base 10.'),
(8, 'Python tiene un bug conocido que hace que 0.1 + 0.2 sea incorrecto',                                            0, 'No es un bug de Python; es una limitación fundamental de cómo los números de punto flotante se representan en binario según el estándar IEEE 754. Todos los lenguajes que usan float tienen este comportamiento: JavaScript, Java, C, etc.'),
(8, 'El operador + no funciona correctamente con decimales en Python',                                                0, 'El operador + funciona perfectamente. El problema es la representación interna de los números float en binario, no el operador de suma. La suma se realiza correctamente sobre las representaciones binarias aproximadas.'),
(8, 'Porque Python usa base 8 (octal) internamente para los decimales',                                              0, 'Python (como todos los lenguajes modernos) usa base 2 (binario) para representar números internamente, siguiendo el estándar IEEE 754. No usa base 8.'),
-- ── Ejercicio 9: var, let y const en JavaScript ──────────────
(9, 'var tiene alcance de función y puede redeclararse; let tiene alcance de bloque; const no puede reasignarse',     1, '¡Correcto! Esta es una distinción crucial en JavaScript moderno. La práctica recomendada es: usa const por defecto (para valores que no cambian), let cuando necesites reasignar, y evita var porque su alcance de función puede causar bugs difíciles de detectar.'),
(9, 'Son exactamente iguales, solo cambia el nombre por convención',                                                  0, 'Tienen diferencias importantes de comportamiento. var tiene alcance de función y hoisting (se "eleva" al inicio de la función); let y const tienen alcance de bloque y no tienen hoisting de la misma manera. Estas diferencias pueden causar bugs reales.'),
(9, 'const es para números y let es para texto',                                                                      0, 'const y let pueden almacenar cualquier tipo de dato. La diferencia es si el valor puede reasignarse (let sí, const no), no qué tipo de dato almacenan.'),
(9, 'var es la versión moderna y let es la antigua',                                                                  0, 'Al contrario: var es la forma antigua (ES5 y anterior); let y const son las formas modernas introducidas en ES6 (2015). La recomendación actual es preferir let y const sobre var.'),
-- ── Ejercicio 10: Operador ternario ─────────────────────────
(10, 'Es el operador ternario: una forma compacta de escribir IF-ELSE en una sola línea',                             1, '¡Correcto! El operador ternario en Python tiene la forma: valor_si_verdadero if condición else valor_si_falso. Es útil para asignaciones simples. En JavaScript/Java/C la sintaxis es: condición ? valor_si_verdadero : valor_si_falso.'),
(10, 'Es un bucle FOR que itera sobre los valores "positivo" y "no positivo"',                                        0, 'No es un bucle. No hay iteración. Es una expresión condicional que evalúa x > 0 y devuelve uno de dos valores según el resultado. Los bucles repiten código; los condicionales eligen entre opciones.'),
(10, 'Es una función lambda que evalúa si x es positivo',                                                             0, 'No es una lambda. Las lambdas en Python se escriben como: lambda x: x > 0. El operador ternario es una expresión condicional inline, no una función anónima.'),
(10, 'Es una declaración de variable con tipo condicional',                                                           0, 'Python no tiene tipos condicionales en la declaración de variables. Lo que hay aquí es una asignación donde el valor asignado se determina mediante una expresión condicional (operador ternario).'),
-- ── Ejercicio 11: Bucle para contraseña ──────────────────────
(11, 'WHILE, porque no sabes cuántos intentos necesitará el usuario hasta acertar',                                   1, '¡Correcto! WHILE es ideal aquí: "mientras la contraseña sea incorrecta, seguir pidiendo". El número de intentos es desconocido e ilimitado. Podrías agregar un contador de intentos máximos (3 intentos) con un IF dentro del WHILE para mayor seguridad.'),
(11, 'FOR con range(3), para dar exactamente 3 intentos',                                                             0, 'FOR con range(3) daría exactamente 3 intentos sin importar si el usuario acierta antes. Sería más correcto usar WHILE con un contador de intentos, que permite salir antes con break si el usuario acierta.'),
(11, 'IF-ELSE, porque solo hay dos opciones: correcto o incorrecto',                                                  0, 'IF-ELSE evalúa la condición una sola vez. Para repetir la solicitud múltiples veces necesitas un bucle. IF-ELSE sin bucle solo verificaría la contraseña una vez y terminaría.'),
(11, 'DO-WHILE, porque necesitas ejecutar el bloque al menos una vez antes de verificar',                             0, 'DO-WHILE también sería válido aquí (pides la contraseña al menos una vez antes de verificar), pero no todos los lenguajes tienen DO-WHILE (Python no lo tiene). WHILE es la respuesta más universal y directa.'),
-- ── Ejercicio 12: Uso legítimo de while(True) ────────────────
(12, 'Un servidor web que debe mantenerse activo procesando solicitudes continuamente',                                1, '¡Correcto! Los servidores, juegos y sistemas embebidos usan while(True) intencionalmente. El bucle se controla con break cuando llega una señal de cierre. Este patrón se llama "event loop" y es fundamental en programación de sistemas, videojuegos y servidores.'),
(12, 'Calcular el factorial de un número',                                                                            0, 'Para calcular un factorial se usa un FOR con un rango definido (for i in range(1, n+1)) o recursión. Un while(True) sería incorrecto aquí porque el número de iteraciones es conocido de antemano.'),
(12, 'Recorrer todos los elementos de una lista',                                                                     0, 'Para recorrer una lista se usa FOR (for elemento in lista), que es más claro y seguro. Un while(True) para recorrer una lista requeriría un índice manual y un break, lo cual es más propenso a errores.'),
(12, 'Imprimir los números del 1 al 100',                                                                             0, 'Para imprimir números del 1 al 100 se usa FOR con range(1, 101). El número de iteraciones es conocido (100), por lo que while(True) sería un antipatrón aquí.'),
-- ── Ejercicio 13: Principio DRY ─────────────────────────────
(13, 'DRY: "Don\'t Repeat Yourself" — cada lógica debe tener una única representación en el sistema',                 1, '¡Correcto! DRY es uno de los principios más importantes en ingeniería de software. Su opuesto es WET ("Write Everything Twice"). Las funciones son la herramienta principal para aplicar DRY: defines la lógica una vez y la reutilizas. Esto reduce errores, facilita el mantenimiento y mejora la legibilidad.'),
(13, 'SOLID: principios de diseño orientado a objetos',                                                               0, 'SOLID es un conjunto de 5 principios de diseño orientado a objetos (Single responsibility, Open/closed, Liskov substitution, Interface segregation, Dependency inversion). Son importantes, pero el principio específico que promueve evitar duplicación de código es DRY.'),
(13, 'KISS: "Keep It Simple, Stupid" — el código debe ser lo más simple posible',                                     0, 'KISS es un principio válido (la simplicidad es valiosa), pero no es el que específicamente promueve el uso de funciones para evitar duplicar código. Ese es DRY.'),
(13, 'YAGNI: "You Aren\'t Gonna Need It" — no implementes lo que no necesitas ahora',                                 0, 'YAGNI es un principio de desarrollo ágil que dice no implementar funcionalidades hasta que sean necesarias. Es útil, pero no es el que promueve el uso de funciones para evitar duplicación.'),
-- ── Ejercicio 14: Parámetros con valor por defecto ───────────
(14, 'Devuelve "Hola, mundo" usando el valor por defecto del parámetro nombre',                                       1, '¡Correcto! Los parámetros con valor por defecto son opcionales al llamar la función. Si no se pasa el argumento, se usa el valor por defecto. Esto hace las funciones más flexibles: saludar() → "Hola, mundo"; saludar("Ana") → "Hola, Ana"; saludar("Carlos") → "Hola, Carlos".'),
(14, 'Lanza un error porque falta el argumento requerido',                                                            0, 'No lanza error porque nombre tiene un valor por defecto ("mundo"). Solo lanzaría error si el parámetro fuera requerido (sin valor por defecto). Los parámetros con valor por defecto son siempre opcionales.'),
(14, 'Devuelve None porque no se pasó ningún argumento',                                                              0, 'La función devuelve "Hola, mundo", no None. None se devuelve cuando una función no tiene return o tiene return sin valor. Esta función sí tiene return y usa el valor por defecto del parámetro.'),
(14, 'Imprime "Hola, mundo" en la pantalla',                                                                          0, 'La función usa return, no print(). return devuelve el valor al lugar donde se llamó la función, pero no lo muestra en pantalla. Para verlo habría que hacer print(saludar()).'),
-- ── Ejercicio 15: Función pura ───────────────────────────────
(15, 'Dado el mismo input siempre produce el mismo output y no tiene efectos secundarios',                             1, '¡Correcto! Las funciones puras son predecibles y fáciles de probar. Ejemplo puro: sumar(2, 3) siempre devuelve 5. Ejemplo impuro: una función que lee la hora actual (output diferente cada vez) o que modifica una variable global (efecto secundario). Las funciones puras son la base de la programación funcional.'),
(15, 'Una función que no recibe parámetros',                                                                          0, 'Una función sin parámetros no es necesariamente pura. Por ejemplo, obtenerHoraActual() no recibe parámetros pero devuelve un valor diferente cada vez que se llama (no es pura). La pureza se refiere al comportamiento, no a la cantidad de parámetros.'),
(15, 'Una función que solo realiza operaciones matemáticas',                                                          0, 'Las operaciones matemáticas suelen producir funciones puras, pero no es una regla absoluta. Una función matemática que usa un número aleatorio no sería pura. Y funciones no matemáticas pueden ser puras (como una que formatea texto de manera determinista).'),
(15, 'Una función que está documentada con comentarios',                                                               0, 'La documentación es una buena práctica, pero no define si una función es pura o no. Una función puede estar perfectamente documentada y aun así tener efectos secundarios (ser impura).');

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

-- ============================================================
-- Mejoras v2: campo categoria, URLs de video, vistas y
-- procedimientos almacenados.
-- Ya integradas aquí — no se necesitan archivos de migración.
-- ============================================================

-- ── Campo categoria en modulos ────────────────────────────────
ALTER TABLE `modulos`
  ADD COLUMN IF NOT EXISTS `categoria` VARCHAR(80) NOT NULL DEFAULT 'Fundamentos'
  AFTER `descripcion`;

UPDATE `modulos` SET `categoria` = 'Fundamentos de Programación' WHERE `id_modulo` IN (1, 2);
UPDATE `modulos` SET `categoria` = 'Variables y Datos'           WHERE `id_modulo` = 3;
UPDATE `modulos` SET `categoria` = 'Control de Flujo'            WHERE `id_modulo` = 4;
UPDATE `modulos` SET `categoria` = 'Funciones'                   WHERE `id_modulo` = 5;

-- ── URLs de video en lecciones ────────────────────────────────
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

-- ── VISTAS ────────────────────────────────────────────────────

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
LEFT JOIN ejercicios e  ON e.id_modulo    = m.id_modulo
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

-- ── PROCEDIMIENTOS ALMACENADOS ────────────────────────────────

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
    (SELECT COUNT(*) FROM usuario WHERE is_admin = 0)            AS total_estudiantes,
    (SELECT COUNT(*) FROM modulos)                               AS total_modulos,
    (SELECT COUNT(*) FROM contenido)                             AS total_lecciones,
    (SELECT COUNT(*) FROM ejercicios)                            AS total_ejercicios,
    (SELECT COUNT(*) FROM progreso WHERE completado = 1)         AS total_completados,
    (SELECT ROUND(AVG(sub.pct), 1)
     FROM (
       SELECT ROUND(
         SUM(CASE WHEN p.completado = 1 THEN 1 ELSE 0 END)
         / NULLIF(COUNT(e.id_ejercicio), 0) * 100
       , 0) AS pct
       FROM usuario u
       LEFT JOIN progreso p ON p.id_usuario = u.id_usuario
       LEFT JOIN ejercicios e ON e.id_ejercicio = p.id_ejercicio
       WHERE u.is_admin = 0
       GROUP BY u.id_usuario
     ) sub
    )                                                            AS promedio_progreso_global;
END$$
DELIMITER ;

COMMIT;
