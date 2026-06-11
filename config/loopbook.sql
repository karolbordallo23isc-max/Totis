-- ========================================================
-- Loopbook v5 — Consolidado final
-- Incluye: tablas, módulos, lecciones, ejercicios, opciones
-- Videos corregidos · Ejercicios para todas las lecciones
-- Columna alguna_vez_correcto en progreso
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
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS usuario;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `usuario` (
  `id_usuario` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `avatar` VARCHAR(10) NOT NULL DEFAULT '👤',
  `usuario` VARCHAR(100) NOT NULL UNIQUE,
  `correo` VARCHAR(150) NOT NULL UNIQUE,
  `contraseña` VARCHAR(255) NOT NULL,
  `is_admin` TINYINT(1) NOT NULL DEFAULT 0,
  `is_superadmin` TINYINT(1) NOT NULL DEFAULT 0,
  `fecha_registro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- prueba123 / test123
INSERT INTO `usuario` VALUES
(1,'Prueba','👤','prueba','prueba@gmail.com','$2y$10$AVQpnPEoIjBynjowNUj95e2y6J8CF/QavadIvA8MGe8IbMVB9mSNW',1,1,'2026-04-22 22:58:24'),
(2,'Test','👤','test','t@gmail.com','$2y$10$POvcAnwcJ1GlpoitPbSDPuN7OrGkv6KCoz6/7cT5DqB8ljkWL7jYa',0,0,'2026-04-22 23:27:53');

CREATE TABLE `password_resets` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` INT(11) NOT NULL,
  `token` VARCHAR(64) NOT NULL UNIQUE,
  `expira_en` DATETIME NOT NULL,
  `usado` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `pr_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `curso` (
  `id_curso` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  `descripcion` TEXT DEFAULT NULL,
  PRIMARY KEY (`id_curso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `curso` VALUES (1,'Ingeniería en Sistemas Computacionales','Fundamentos de programación y desarrollo de software');

CREATE TABLE `modulos` (
  `id_modulo` INT(11) NOT NULL AUTO_INCREMENT,
  `id_curso` INT(11) NOT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `categoria` VARCHAR(80) NOT NULL DEFAULT 'Programación',
  `orden` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_modulo`),
  KEY `id_curso` (`id_curso`),
  CONSTRAINT `modulos_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `curso` (`id_curso`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `modulos` (`id_modulo`,`id_curso`,`nombre`,`descripcion`,`categoria`,`orden`) VALUES
(1,1,'Fundamentos de Programación','Algoritmos, variables, tipos de datos, estructuras de control y funciones. Base de toda la carrera.','Fundamentos',1),
(2,1,'Programación Orientada a Objetos','Clases, objetos, herencia, polimorfismo y encapsulamiento. El paradigma dominante en la industria.','POO',2),
(3,1,'Estructura de Datos','Arreglos, listas enlazadas, pilas, colas, árboles y grafos. Cómo organizar datos eficientemente.','Estructuras',3),
(4,1,'Bases de Datos','Modelo relacional, SQL, normalización y diseño de esquemas. Fundamento de cualquier aplicación.','Bases de Datos',4),
(5,1,'Sistemas Operativos','Procesos, hilos, memoria, sistema de archivos y concurrencia. Cómo funciona el software debajo.','Sistemas',5),
(6,1,'Ingeniería de Software','Ciclo de vida, metodologías ágiles, patrones de diseño y buenas prácticas profesionales.','Ingeniería',6),
(7,1,'Redes de Computadoras','Modelo OSI, TCP/IP, protocolos, HTTP y seguridad básica en redes.','Redes',7),
(8,1,'Inteligencia Artificial','Machine learning, redes neuronales y aplicaciones prácticas de IA.','IA',8);

CREATE TABLE `contenido` (
  `id_contenido` INT(11) NOT NULL AUTO_INCREMENT,
  `id_modulo` INT(11) NOT NULL,
  `titulo` VARCHAR(150) NOT NULL,
  `texto` TEXT DEFAULT NULL,
  `tipo` ENUM('texto','video','imagen') NOT NULL DEFAULT 'texto',
  `url` VARCHAR(255) DEFAULT NULL,
  `orden` INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_contenido`),
  KEY `id_modulo` (`id_modulo`),
  CONSTRAINT `contenido_ibfk_1` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ── Contenido: lecciones con videos corregidos ───────────────
-- Lecciones con video: 1,2,3,4,5,6,9,10,11,13,14,16,19,20,22,25,26
-- Lecciones solo texto: 7,8,12,15,17,18,21,23,24,27
INSERT INTO `contenido` (`id_contenido`,`id_modulo`,`titulo`,`texto`,`tipo`,`url`,`orden`) VALUES
(1,1,'Algoritmos y pensamiento computacional',
'Un algoritmo es una secuencia finita de pasos bien definidos para resolver un problema. El pensamiento computacional incluye: descomposición (dividir el problema), reconocimiento de patrones, abstracción (ignorar detalles irrelevantes) y diseño de algoritmos. Un algoritmo puede expresarse en pseudocódigo, diagrama de flujo o código. La eficiencia importa: dos algoritmos pueden resolver el mismo problema pero uno puede ser miles de veces más rápido. La notación Big O describe cómo crece el tiempo de ejecución según el tamaño de la entrada: O(1) constante, O(n) lineal, O(n²) cuadrático.',
'video','https://www.youtube.com/embed/U3CGMyjzlvM',1),

(2,1,'Variables, tipos de datos y operadores',
'Una variable es un espacio en memoria con nombre que almacena un valor. Los tipos de datos definen qué clase de valor puede guardar: enteros (int), decimales (float), texto (string), booleanos (bool). Los operadores permiten manipular valores: aritméticos (+,-,*,/,%), comparación (==,!=,<,>,<=,>=) y lógicos (&&,||,!). El tipado estático (Java, C++) requiere declarar el tipo; el dinámico (Python, JS) lo infiere. Error clásico: confundir = (asignación) con == (comparación). En JS, typeof null devuelve "object" por un bug histórico del lenguaje.',
'texto',NULL,2),

(3,1,'Estructuras de control: condicionales y bucles',
'Las estructuras de control determinan el flujo de ejecución. Condicionales (if/else, switch) ejecutan bloques según una condición. Bucles (for, while, do-while) repiten bloques mientras se cumpla una condición. El for es ideal cuando conoces el número de iteraciones; while cuando dependes de una condición dinámica; do-while garantiza al menos una ejecución. Palabras clave: break (salir del bucle), continue (saltar iteración). Error frecuente: off-by-one error, donde el bucle itera una vez de más o de menos por un error en la condición de parada.',
'texto',NULL,3),

(4,1,'Funciones y modularidad',
'Una función encapsula código reutilizable con nombre, parámetros y valor de retorno. El principio DRY (Don\'t Repeat Yourself) dice que cada lógica debe existir en un solo lugar. Conceptos clave: scope (alcance de variables), recursión (función que se llama a sí misma con un caso base), funciones puras (mismo input → mismo output, sin efectos secundarios). El call stack rastrea las llamadas activas; un stack overflow ocurre cuando la recursión no tiene caso base y se llama infinitamente.',
'texto',NULL,4),

(5,2,'Clases, objetos y encapsulamiento',
'La POO organiza el código en objetos que combinan datos (atributos) y comportamiento (métodos). Una clase es el molde; un objeto es una instancia concreta. El encapsulamiento oculta los detalles internos mediante modificadores de acceso: public (accesible desde cualquier lugar), private (solo dentro de la clase), protected (clase y subclases). Los getters y setters permiten acceder y modificar atributos privados con validación. Ejemplo: una clase CuentaBancaria encapsula el saldo (privado) y expone depositar() y retirar() que validan las operaciones antes de modificarlo.',
'video','https://www.youtube.com/embed/DlphYPc_HKk',1),

(6,2,'Herencia y polimorfismo',
'La herencia permite que una clase hija reutilice atributos y métodos de una clase padre. Si Animal tiene respirar(), Perro y Gato lo heredan sin redefinirlo. El polimorfismo permite que objetos de distintas clases respondan al mismo mensaje de formas diferentes: hacerSonido() en Perro ladra, en Gato maúlla. Override (sobrescritura) redefine un método heredado en tiempo de ejecución. Overload (sobrecarga) define múltiples métodos con el mismo nombre pero distintos parámetros. El "problema del diamante" en herencia múltiple se resuelve con interfaces.',
'video','https://www.youtube.com/embed/tTPeP5dVuA4',2),

(7,2,'Abstracción e interfaces',
'La abstracción modela solo los aspectos relevantes de un objeto, ignorando detalles innecesarios. Las clases abstractas definen estructura común pero no pueden instanciarse; obligan a las subclases a implementar ciertos métodos. Las interfaces son contratos: definen qué métodos debe tener una clase sin especificar cómo. Una clase puede implementar múltiples interfaces. Principio clave: "programa hacia interfaces, no hacia implementaciones". Esto hace el código más flexible: si dependes de una interfaz BaseDeDatos, puedes cambiar de MySQL a PostgreSQL sin tocar el resto del código.',
'texto',NULL,3),

(8,2,'Los 4 pilares de la POO en práctica',
'Los cuatro pilares trabajan juntos. Encapsulamiento: ocultar datos internos con private/protected. Herencia: reutilizar código de clases padre con extends. Polimorfismo: mismo método, distinto comportamiento según la clase. Abstracción: modelar solo lo relevante con clases abstractas e interfaces. Ejemplo integrado: Figura (abstracta) define calcularArea(); Círculo y Rectángulo la heredan e implementan diferente (polimorfismo); el radio es privado (encapsulamiento). Dominar estos pilares es la diferencia entre código que funciona y código mantenible y extensible.',
'texto',NULL,4),

(9,3,'Arreglos y listas enlazadas',
'Un arreglo almacena elementos en posiciones contiguas de memoria. Acceso por índice: O(1). Insertar/eliminar en el medio: O(n) por desplazamiento. Una lista enlazada almacena cada elemento en un nodo con dato + puntero al siguiente. Inserción/eliminación: O(1) si tienes el nodo. Acceso por posición: O(n). Cuándo usar cada una: arreglos para acceso rápido por índice; listas cuando insertas/eliminas frecuentemente. Las listas doblemente enlazadas tienen puntero al nodo anterior también, permitiendo recorrido en ambas direcciones.',
'video','https://www.youtube.com/embed/RBSGKlAvoiM',1),

(10,3,'Pilas y colas',
'Pila (stack): LIFO — el último en entrar es el primero en salir. Operaciones: push, pop, peek. Usos: historial de navegación, deshacer/rehacer, call stack del programa. Cola (queue): FIFO — el primero en entrar es el primero en salir. Operaciones: enqueue, dequeue. Usos: cola de impresión, procesamiento de tareas, BFS en grafos. Cola de prioridad: los elementos salen según su prioridad, no su orden de llegada. Deque (double-ended queue): permite insertar y eliminar por ambos extremos.',
'video','https://www.youtube.com/embed/wjI1WNcIntg',2),

(11,3,'Árboles y grafos',
'Árbol: estructura jerárquica con nodo raíz y nodos hijos. Árbol binario de búsqueda (BST): menores a la izquierda, mayores a la derecha. Búsqueda/inserción/eliminación: O(log n) si está balanceado. Recorridos: inorden (da elementos ordenados), preorden, postorden. Grafo: nodos conectados por aristas, dirigido o no, ponderado o no. Algoritmos: BFS (búsqueda en anchura, usa cola), DFS (búsqueda en profundidad, usa pila/recursión), Dijkstra (camino más corto con pesos). Aplicaciones: redes sociales, mapas, compiladores.',
'video','https://www.youtube.com/embed/oSWTXtMglKE',3),

(12,3,'Algoritmos de ordenamiento y búsqueda',
'Bubble Sort: compara pares adyacentes, O(n²), simple pero lento. Merge Sort: divide y fusiona recursivamente, O(n log n), estable. Quick Sort: elige pivote y particiona, O(n log n) promedio, O(n²) peor caso. Búsqueda lineal: O(n), revisa uno a uno. Búsqueda binaria: O(log n), requiere datos ordenados, divide el espacio a la mitad. Big O mide cómo escala el tiempo con el tamaño de la entrada. Regla práctica: para n > 10,000, evita algoritmos O(n²).',
'texto',NULL,4);

INSERT INTO `contenido` (`id_contenido`,`id_modulo`,`titulo`,`texto`,`tipo`,`url`,`orden`) VALUES
(13,4,'Modelo relacional y diseño de esquemas',
'El modelo relacional organiza datos en tablas con filas y columnas. Clave primaria (PK): identifica únicamente cada fila. Clave foránea (FK): establece relaciones entre tablas. Normalización elimina redundancia: 1FN (sin grupos repetitivos), 2FN (sin dependencias parciales), 3FN (sin dependencias transitivas). Diagrama ER: modela entidades y relaciones antes de crear tablas. Regla de oro: si un dato se repite en múltiples filas, probablemente necesita su propia tabla. Relaciones: uno a uno, uno a muchos, muchos a muchos (tabla intermedia).',
'video','https://www.youtube.com/embed/FR4QIeZaPeM',1),

(14,4,'SQL: consultas y manipulación de datos',
'DDL: CREATE, ALTER, DROP (estructura). DML: SELECT, INSERT, UPDATE, DELETE (datos). SELECT básico: SELECT cols FROM tabla WHERE condición ORDER BY col LIMIT n. JOINs: INNER JOIN (solo coincidencias), LEFT JOIN (todos de la izquierda + coincidencias de la derecha). Agregación: COUNT, SUM, AVG, MAX, MIN con GROUP BY. Subconsultas: SELECT dentro de SELECT. WHERE filtra filas individuales; HAVING filtra grupos después de GROUP BY. Índices aceleran consultas a costa de espacio en disco.',
'video','https://www.youtube.com/embed/OuJerKzV5T0',2),

(15,4,'Transacciones, índices y optimización',
'Transacción: conjunto de operaciones atómicas — COMMIT (confirmar) o ROLLBACK (deshacer). Propiedades ACID: Atomicidad, Consistencia, Aislamiento, Durabilidad. Índice B-tree: acelera búsquedas de O(n) a O(log n). Indexa columnas frecuentes en WHERE, JOIN, ORDER BY. Antipatrón N+1: hacer una consulta por cada elemento en vez de un JOIN. EXPLAIN muestra el plan de ejecución de una consulta para identificar cuellos de botella. Vistas: consultas guardadas como tablas virtuales.',
'texto',NULL,3),

(16,5,'Procesos e hilos',
'Proceso: programa en ejecución con su propio espacio de memoria. Hilo (thread): unidad de ejecución dentro de un proceso, comparte memoria con otros hilos. Context switching: el SO alterna entre procesos/hilos simulando paralelismo. Problemas de concurrencia: condición de carrera (dos hilos modifican el mismo dato), deadlock (dos procesos esperan recursos que el otro tiene), starvation (un proceso nunca obtiene recursos). Soluciones: mutex (exclusión mutua), semáforos, monitores. Paralelismo real en CPUs multinúcleo con multithreading.',
'video','https://www.youtube.com/embed/exbKr6fnoUw',1),

(17,5,'Gestión de memoria',
'El SO asigna y libera memoria RAM para los procesos. Memoria virtual: usa disco como extensión de RAM (swap). Paginación: divide memoria en bloques fijos (páginas). Segmentación: bloques de tamaño variable. Garbage collector (Java, Python): libera automáticamente memoria no referenciada. En C/C++: gestión manual con malloc/free o new/delete. Memory leak: memoria reservada que nunca se libera, el programa consume cada vez más RAM. Stack: memoria para variables locales y llamadas a funciones. Heap: memoria dinámica asignada en tiempo de ejecución.',
'texto',NULL,2),

(18,5,'Sistema de archivos y entrada/salida',
'El sistema de archivos organiza datos en jerarquía de directorios y archivos. Sistemas comunes: NTFS (Windows), ext4 (Linux), APFS (macOS). E/S es la operación más lenta: acceder a disco es ~100,000x más lento que RAM. Buffering y caching reducen este impacto. Descriptor de archivo: identificador numérico que el SO asigna a cada archivo abierto. Pipes: comunicación entre procesos mediante flujo de datos. "Todo es un archivo" en Unix: dispositivos, redes y procesos se acceden con la misma interfaz.',
'texto',NULL,3),

(19,6,'Ciclo de vida del software y metodologías ágiles',
'SDLC: análisis de requisitos → diseño → implementación → pruebas → despliegue → mantenimiento. Cascada: fases secuenciales, predecible pero rígido ante cambios. Ágil: iteraciones cortas (sprints 1-4 semanas), entrega incremental. Scrum: roles (Product Owner, Scrum Master, equipo), artefactos (Product Backlog, Sprint Backlog), ceremonias (Daily, Sprint Review, Retrospectiva). Manifiesto Ágil: individuos sobre procesos, software funcionando sobre documentación, colaboración con el cliente sobre contratos, respuesta al cambio sobre seguir un plan.',
'video','https://www.youtube.com/embed/HhC75IonpOU',1),

(20,6,'Patrones de diseño',
'Soluciones reutilizables a problemas recurrentes. Creacionales: Singleton (una sola instancia), Factory (delegar creación), Builder (construir paso a paso). Estructurales: Adapter (compatibilizar interfaces), Decorator (agregar funcionalidad sin modificar la clase), Facade (simplificar interfaz compleja). Comportamiento: Observer (notificar cambios a múltiples objetos), Strategy (intercambiar algoritmos en tiempo de ejecución), Command (encapsular acciones como objetos). Conocer patrones es hablar el mismo idioma que otros desarrolladores.',
'video','https://www.youtube.com/embed/cwfuydUHZ7o',2),

(21,6,'Principios SOLID y código limpio',
'S: Single Responsibility — una clase, una razón para cambiar. O: Open/Closed — abierto para extensión, cerrado para modificación. L: Liskov Substitution — las subclases deben poder reemplazar a sus padres. I: Interface Segregation — interfaces específicas mejor que una general. D: Dependency Inversion — depender de abstracciones, no de implementaciones. Código limpio: nombres descriptivos, funciones pequeñas que hacen una sola cosa, sin comentarios que expliquen código confuso (mejor reescribir el código), tests que documenten el comportamiento.',
'texto',NULL,3),

(22,7,'Modelo OSI y TCP/IP',
'OSI: 7 capas — Física, Enlace, Red, Transporte, Sesión, Presentación, Aplicación. TCP/IP: 4 capas — Acceso a red, Internet, Transporte, Aplicación. TCP: garantiza entrega ordenada y sin errores (ACK), más lento pero confiable. UDP: no garantiza entrega, más rápido, ideal para streaming y videojuegos. IP: direccionamiento lógico y enrutamiento. IPv4: 32 bits (~4 mil millones de direcciones). IPv6: 128 bits (prácticamente ilimitado). Puerto: número que identifica el servicio dentro de un host (HTTP:80, HTTPS:443, SSH:22).',
'video','https://www.youtube.com/embed/qTaOZrDnMzQ',1),

(23,7,'HTTP, DNS y protocolos de aplicación',
'HTTP: protocolo de la web. Métodos: GET (obtener), POST (crear), PUT (actualizar), DELETE (eliminar). Códigos de estado: 200 OK, 201 Created, 301 Redirect, 400 Bad Request, 401 Unauthorized, 404 Not Found, 500 Server Error. HTTPS agrega cifrado TLS. DNS: traduce dominios a IPs, la "agenda telefónica" de internet. Otros protocolos: SMTP/IMAP (correo), FTP (archivos), SSH (acceso remoto seguro), WebSocket (tiempo real bidireccional). REST: arquitectura para APIs usando HTTP.',
'texto',NULL,2),

(24,7,'Seguridad en redes',
'Ataques comunes: Man-in-the-Middle (interceptar comunicación), DDoS (saturar servidor), SQL Injection (inyectar código SQL), XSS (inyectar scripts en páginas web), phishing (engañar para obtener credenciales). Cifrado simétrico: misma clave para cifrar y descifrar (AES). Asimétrico: clave pública cifra, privada descifra (RSA). HTTPS usa TLS que combina ambos. Firewall: filtra tráfico según reglas. VPN: túnel cifrado sobre red pública. Buenas prácticas: 2FA, principio de mínimo privilegio, actualizaciones regulares.',
'texto',NULL,3),

(25,8,'Fundamentos de IA y aprendizaje automático',
'IA: sistemas que realizan tareas que requieren inteligencia humana. ML: los sistemas aprenden de datos sin ser programados explícitamente. Tipos: supervisado (datos etiquetados, ej: clasificar spam), no supervisado (sin etiquetas, ej: segmentar clientes), por refuerzo (prueba y error con recompensas, ej: juegos). Flujo: recolectar datos → preprocesar → entrenar → evaluar → desplegar. Métricas: precisión, recall, F1-score, matriz de confusión. Overfitting: el modelo memoriza los datos de entrenamiento pero no generaliza.',
'video','https://www.youtube.com/embed/KytW151dpqU',1),

(26,8,'Redes neuronales y deep learning',
'Red neuronal: capas de neuronas artificiales con pesos ajustables. Capa entrada → capas ocultas (extraen características) → capa salida. Entrenamiento: backpropagation calcula gradientes, descenso de gradiente ajusta pesos para minimizar el error. Deep learning: redes con muchas capas ocultas. CNNs para imágenes, RNNs/Transformers para texto. Frameworks: TensorFlow, PyTorch, Keras. Overfitting: el modelo memoriza en vez de generalizar. Soluciones: más datos, regularización, dropout, validación cruzada.',
'video','https://www.youtube.com/embed/MRIv2IwFTPg',2),

(27,8,'IA generativa y aplicaciones prácticas',
'IA generativa: crea contenido nuevo — texto (GPT, LLaMA), imágenes (DALL-E, Stable Diffusion), código (GitHub Copilot). LLMs: entrenados con enormes cantidades de texto, predicen el siguiente token. Prompt engineering: formular instrucciones efectivas para obtener mejores resultados. Aplicaciones en programación: autocompletar código, generar tests, explicar código, detectar bugs, documentar funciones. Consideraciones éticas: sesgos, derechos de autor, desinformación. La IA amplifica las capacidades del programador, no lo reemplaza.',
'texto',NULL,3);

CREATE TABLE `ejercicios` (
  `id_ejercicio` INT(11) NOT NULL AUTO_INCREMENT,
  `id_modulo` INT(11) NOT NULL,
  `id_contenido` INT(11) DEFAULT NULL,
  `pregunta` TEXT NOT NULL,
  `retroalimentacion` TEXT DEFAULT NULL,
  `tipo` ENUM('opcion_multiple','verdadero_falso','codigo') NOT NULL DEFAULT 'opcion_multiple',
  `expected_output` VARCHAR(500) DEFAULT NULL,
  `code_instructions` TEXT DEFAULT NULL,
  `code_hint` TEXT DEFAULT NULL,
  `fecha_creacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_ejercicio`),
  KEY `id_modulo` (`id_modulo`),
  KEY `id_contenido` (`id_contenido`),
  CONSTRAINT `ejercicios_ibfk_1` FOREIGN KEY (`id_modulo`) REFERENCES `modulos` (`id_modulo`) ON DELETE CASCADE,
  CONSTRAINT `ejercicios_ibfk_2` FOREIGN KEY (`id_contenido`) REFERENCES `contenido` (`id_contenido`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `ejercicios` (`id_ejercicio`,`id_modulo`,`id_contenido`,`pregunta`,`retroalimentacion`,`tipo`,`expected_output`,`code_instructions`,`code_hint`) VALUES
-- Módulo 1 · Lección 1: Algoritmos
(1,1,1,'¿Cuál de estas propiedades NO es característica de un algoritmo válido?',
'Un algoritmo debe ser finito (termina), definido (pasos claros) y efectivo (cada paso es realizable). La "creatividad" no es una propiedad formal.','opcion_multiple',NULL,NULL,NULL),
(2,1,1,'Un algoritmo tarda 1 segundo con 1,000 datos y 1,000 segundos con 1,000,000. ¿Cuál es su complejidad?',
'Si al multiplicar los datos por 1,000 el tiempo se multiplica por 1,000, la relación es lineal: O(n). Si fuera O(n²), el tiempo se multiplicaría por 1,000,000.','opcion_multiple',NULL,NULL,NULL),
(3,1,1,'Verdadero o Falso: El pseudocódigo debe seguir la sintaxis exacta de un lenguaje de programación.',
'FALSO. El pseudocódigo es una descripción informal de un algoritmo en lenguaje natural estructurado. No tiene sintaxis fija; su objetivo es comunicar la lógica, no ejecutarse en una computadora.','verdadero_falso',NULL,NULL,NULL),
-- Módulo 1 · Lección 2: Variables
(4,1,2,'¿Qué imprime este código JavaScript?\nlet x = "5";\nlet y = 2;\nconsole.log(x + y);',
'En JS, "5" + 2 = "52" porque el operador + con un string hace concatenación, no suma. Para sumar numéricamente habría que convertir: Number(x) + y = 7. Este es un error clásico de coerción de tipos.','opcion_multiple',NULL,NULL,NULL),
(5,1,2,'🖥️ Práctica: Declara nombre = "Ana" y edad = 20. Imprime exactamente: "Hola Ana, tienes 20 años"',
'Los template literals (backticks) permiten insertar variables directamente en el string con ${variable}. Es más legible que concatenar con +.','codigo','Hola Ana, tienes 20 años',
'Declara:\n  let nombre = "Ana";\n  let edad = 20;\nLuego imprime exactamente: Hola Ana, tienes 20 años\n\nUsa template literals: console.log(`Hola ${nombre}, tienes ${edad} años`);',
'let nombre = "Ana";\nlet edad = 20;\nconsole.log(`Hola ${nombre}, tienes ${edad} años`);'),
(6,1,2,'¿Cuál es la diferencia entre null y undefined en JavaScript?',
'undefined significa que una variable fue declarada pero no se le asignó valor. null es un valor asignado explícitamente que representa "sin valor". typeof undefined = "undefined"; typeof null = "object" (bug histórico de JS).','opcion_multiple',NULL,NULL,NULL),
-- Módulo 1 · Lección 3: Estructuras de control
(7,1,3,'¿Qué imprime este código?\nfor(let i=0; i<5; i++) {\n  if(i===3) break;\n  console.log(i);\n}',
'break termina el bucle cuando i===3. Se imprimen 0, 1, 2. Cuando i llega a 3, el break sale del bucle antes de ejecutar console.log(3).','opcion_multiple',NULL,NULL,NULL),
(8,1,3,'🖥️ Práctica: Imprime solo los números impares del 1 al 9, uno por línea.',
'Un número es impar si n % 2 !== 0 (o n % 2 === 1). El bucle recorre del 1 al 9 y el if filtra los impares: 1, 3, 5, 7, 9.','codigo','1\n3\n5\n7\n9',
'Usa un bucle for del 1 al 9.\nDentro, usa if para verificar si el número es impar (n % 2 !== 0).\nImprime solo los impares, uno por línea.',
'for (let i = 1; i <= 9; i++) {\n  if (i % 2 !== 0) console.log(i);\n}'),
(9,1,3,'Verdadero o Falso: El bucle do-while siempre ejecuta su cuerpo al menos una vez.',
'VERDADERO. El do-while evalúa la condición DESPUÉS de ejecutar el cuerpo. Por eso, aunque la condición sea falsa desde el inicio, el cuerpo se ejecuta una vez. El while evalúa ANTES.','verdadero_falso',NULL,NULL,NULL),
-- Módulo 1 · Lección 4: Funciones
(10,1,4,'¿Qué devuelve esta función con n=5?\nfunction f(n) {\n  if (n <= 1) return 1;\n  return n * f(n-1);\n}',
'Es el factorial recursivo. f(5) = 5 * f(4) = 5 * 4 * f(3) = 5*4*3*2*1 = 120. El caso base es n<=1 que devuelve 1, deteniendo la recursión.','opcion_multiple',NULL,NULL,NULL),
(11,1,4,'🖥️ Práctica: Crea una función "maximo" que reciba dos números y devuelva el mayor. Imprime maximo(7, 3) y maximo(2, 9).',
'La función compara los dos parámetros y retorna el mayor. maximo(7,3) = 7; maximo(2,9) = 9.','codigo','7\n9',
'Crea una función llamada "maximo" que:\n- Reciba dos parámetros (a, b)\n- Retorne el mayor de los dos\nLuego imprime maximo(7, 3) y maximo(2, 9) en líneas separadas.',
'function maximo(a, b) {\n  return a > b ? a : b;\n}\nconsole.log(maximo(7, 3));\nconsole.log(maximo(2, 9));'),
(12,1,4,'¿Qué problema tiene esta función?\nlet contador = 0;\nfunction incrementar() {\n  contador++;\n}',
'La función modifica una variable global (contador), lo que es un efecto secundario. Esto hace la función impura y difícil de testear. Una función pura recibiría el contador como parámetro y devolvería el nuevo valor sin modificar nada externo.','opcion_multiple',NULL,NULL,NULL);

INSERT INTO `ejercicios` (`id_ejercicio`,`id_modulo`,`id_contenido`,`pregunta`,`retroalimentacion`,`tipo`,`expected_output`,`code_instructions`,`code_hint`) VALUES
-- Módulo 2 · Lección 5: Clases
(13,2,5,'¿Cuál es la diferencia entre una clase y un objeto?',
'La clase es el molde o plantilla (define estructura y comportamiento). El objeto es una instancia concreta creada a partir de esa clase con new. Puedes tener una clase Perro y crear múltiples objetos: miPerro, tuPerro, cada uno con sus propios valores de atributos.','opcion_multiple',NULL,NULL,NULL),
(14,2,5,'¿Por qué es mala práctica hacer todos los atributos de una clase públicos?',
'Si los atributos son públicos, cualquier código externo puede modificarlos sin validación. El encapsulamiento protege la integridad: un setter puede validar que la edad no sea negativa antes de asignarla. Sin encapsulamiento, el objeto puede quedar en un estado inválido.','opcion_multiple',NULL,NULL,NULL),
(15,2,5,'Verdadero o Falso: En POO, el constructor se llama automáticamente al crear un objeto con new.',
'VERDADERO. El constructor inicializa el estado del objeto cuando se crea. Si no defines uno, el lenguaje usa un constructor por defecto vacío. En Java/C# se llama igual que la clase; en Python es __init__; en JS es constructor().','verdadero_falso',NULL,NULL,NULL),
-- Módulo 2 · Lección 6: Herencia
(16,2,6,'Tienes: Animal (padre), Perro y Gato (hijos). ¿Qué método tiene más sentido poner en Animal?',
'respirar() es común a todos los animales y debe estar en la clase padre. ladrar() es específico de Perro y maullar() de Gato. La herencia sirve para compartir comportamiento común; lo específico va en cada subclase.','opcion_multiple',NULL,NULL,NULL),
(17,2,6,'¿Qué es el polimorfismo en términos simples?',
'El polimorfismo permite tratar objetos de distintas clases de forma uniforme. Puedes tener un arreglo de Figuras y llamar calcularArea() en cada una; cada figura lo calcula a su manera sin que el código que las usa necesite saber el tipo específico.','opcion_multiple',NULL,NULL,NULL),
(18,2,6,'Verdadero o Falso: Una subclase puede sobrescribir (override) cualquier método de su clase padre.',
'FALSO. Los métodos marcados como final (Java) o sealed (C#) no pueden sobrescribirse. Además, los métodos privados no son heredados y por tanto no pueden sobrescribirse en la subclase.','verdadero_falso',NULL,NULL,NULL),
-- Módulo 2 · Lección 7: Abstracción
(19,2,7,'¿Cuál es la diferencia clave entre una clase abstracta y una interfaz?',
'Una clase abstracta puede tener implementación parcial (métodos con código) y atributos de instancia. Una interfaz solo define contratos (qué métodos debe tener). Una clase puede implementar múltiples interfaces pero solo heredar de una clase abstracta (en Java/C#).','opcion_multiple',NULL,NULL,NULL),
(20,2,7,'¿Por qué el principio "programa hacia interfaces" hace el código más flexible?',
'Si tu código depende de una interfaz (abstracción) en vez de una clase concreta, puedes cambiar la implementación sin modificar el código que la usa. Ejemplo: si usas una interfaz Repositorio, puedes cambiar de MySQL a MongoDB sin tocar la lógica de negocio.','opcion_multiple',NULL,NULL,NULL),
-- Módulo 2 · Lección 8: 4 pilares POO (nuevos)
(21,2,8,'¿Cuál pilar de la POO describe la capacidad de que el mismo método produzca resultados distintos según el objeto que lo ejecuta?',
'El polimorfismo permite que calcularArea() en Círculo y Rectángulo hagan cosas distintas. El encapsulamiento oculta datos. La herencia reutiliza código. La abstracción modela lo relevante.','opcion_multiple',NULL,NULL,NULL),
(22,2,8,'Verdadero o Falso: Una clase puede aplicar los 4 pilares de la POO simultáneamente.',
'VERDADERO. Los pilares se complementan: una clase puede encapsular atributos (encapsulamiento), heredar de otra (herencia), implementar métodos de forma diferente (polimorfismo) y modelar solo lo relevante (abstracción).','verdadero_falso',NULL,NULL,NULL);

INSERT INTO `ejercicios` (`id_ejercicio`,`id_modulo`,`id_contenido`,`pregunta`,`retroalimentacion`,`tipo`,`expected_output`,`code_instructions`,`code_hint`) VALUES
-- Módulo 3 · Lección 9: Arreglos
(23,3,9,'Tienes un arreglo de 1 millón de elementos. ¿Qué operación es O(1)?',
'Acceder por índice en un arreglo es O(1): la dirección de memoria se calcula directamente (dirección_base + índice * tamaño_elemento). Insertar al inicio es O(n) por desplazamiento. Buscar sin índice es O(n).','opcion_multiple',NULL,NULL,NULL),
(24,3,9,'¿Cuándo preferirías una lista enlazada sobre un arreglo?',
'Las listas enlazadas son mejores cuando insertas/eliminas frecuentemente al inicio o en el medio, porque no requieren desplazar elementos. Los arreglos son mejores para acceso aleatorio por índice y cuando el tamaño es fijo.','opcion_multiple',NULL,NULL,NULL),
(25,3,9,'🖥️ Práctica: Dado [3,1,4,1,5,9,2,6], imprime solo los elementos mayores a 4, uno por línea.',
'Recorre el arreglo con forEach y filtra con if. Los elementos mayores a 4 son: 5, 9, 6.','codigo','5\n9\n6',
'Dado el arreglo: [3,1,4,1,5,9,2,6]\nRecórrelo e imprime solo los elementos mayores a 4, uno por línea.',
'const arr = [3,1,4,1,5,9,2,6];\narr.forEach(n => {\n  if (n > 4) console.log(n);\n});'),
-- Módulo 3 · Lección 10: Pilas y colas
(26,3,10,'¿Qué estructura de datos usa el navegador para implementar el botón "Atrás"?',
'El historial de navegación es una pila (LIFO): cada página visitada se apila, y al presionar "Atrás" se desapila la última. El botón "Adelante" usa otra pila con las páginas que se han retrocedido.','opcion_multiple',NULL,NULL,NULL),
(27,3,10,'Un sistema de atención al cliente recibe solicitudes de múltiples usuarios. ¿Qué estructura es más justa?',
'Una cola (FIFO) es la estructura correcta: el primer usuario en llegar es el primero en ser atendido. Esto es justo y predecible. Una pila atendería primero al último en llegar, lo cual sería injusto.','opcion_multiple',NULL,NULL,NULL),
(28,3,10,'Verdadero o Falso: Una pila puede implementarse usando un arreglo.',
'VERDADERO. La pila usa push() para agregar al final y pop() para quitar del final del arreglo. Ambas operaciones son O(1) en un arreglo dinámico. También puede implementarse con una lista enlazada.','verdadero_falso',NULL,NULL,NULL),
-- Módulo 3 · Lección 11: Árboles
(29,3,11,'En un BST con raíz 10, ¿dónde se inserta el valor 7?',
'En un BST, los valores menores van a la izquierda. 7 < 10, entonces va al subárbol izquierdo de la raíz. Si ya hubiera un nodo a la izquierda, se compararía con ese nodo y se continuaría bajando.','opcion_multiple',NULL,NULL,NULL),
(30,3,11,'¿Cuál algoritmo usarías para encontrar el camino más corto entre dos ciudades en un mapa con distancias?',
'Dijkstra encuentra el camino más corto en grafos con pesos no negativos. BFS encuentra el camino con menos aristas (sin pesos). DFS no garantiza el camino más corto. A* es más eficiente que Dijkstra con una heurística.','opcion_multiple',NULL,NULL,NULL),
-- Módulo 3 · Lección 12: Ordenamiento (nuevos)
(31,3,12,'Tienes 10,000 elementos desordenados. ¿Qué algoritmo elegirías para ordenarlos eficientemente?',
'Merge Sort y Quick Sort son O(n log n) en promedio, mucho más eficientes que Bubble Sort O(n²) para grandes volúmenes. Para 10,000 elementos, O(n²) haría 100 millones de operaciones; O(n log n) haría ~130,000.','opcion_multiple',NULL,NULL,NULL),
(32,3,12,'¿Por qué la búsqueda binaria requiere que los datos estén ordenados?',
'La búsqueda binaria divide el espacio a la mitad en cada paso comparando con el elemento del medio. Si los datos no están ordenados, no puede saber en qué mitad buscar y el algoritmo no funciona correctamente.','opcion_multiple',NULL,NULL,NULL);

INSERT INTO `ejercicios` (`id_ejercicio`,`id_modulo`,`id_contenido`,`pregunta`,`retroalimentacion`,`tipo`,`expected_output`,`code_instructions`,`code_hint`) VALUES
-- Módulo 4 · Lección 13: Modelo relacional
(33,4,13,'¿Qué problema resuelve la normalización en bases de datos?',
'La normalización elimina redundancia y anomalías de actualización. Si el nombre de un cliente está en 100 filas y cambia, sin normalización hay que actualizar 100 filas. Con normalización (tabla Clientes separada), solo 1.','opcion_multiple',NULL,NULL,NULL),
(34,4,13,'Tienes una tabla Pedidos con: id, cliente_nombre, cliente_email, producto, precio. ¿Qué problema tiene?',
'Si un cliente hace 100 pedidos, su nombre y email se repiten 100 veces (redundancia). Si cambia su email, hay que actualizar 100 filas (anomalía de actualización). La solución: separar en tablas Clientes y Pedidos con FK.','opcion_multiple',NULL,NULL,NULL),
-- Módulo 4 · Lección 14: SQL
(35,4,14,'¿Qué devuelve esta consulta?\nSELECT departamento, COUNT(*) as total\nFROM empleados\nGROUP BY departamento\nHAVING COUNT(*) > 5;',
'Devuelve los departamentos que tienen MÁS de 5 empleados, junto con su conteo. HAVING filtra grupos después de GROUP BY. WHERE no puede usarse con funciones de agregación como COUNT.','opcion_multiple',NULL,NULL,NULL),
(36,4,14,'¿Cuál es la diferencia entre INNER JOIN y LEFT JOIN?',
'INNER JOIN devuelve solo las filas que tienen coincidencia en AMBAS tablas. LEFT JOIN devuelve TODAS las filas de la tabla izquierda, más las coincidencias de la derecha; si no hay coincidencia, las columnas de la derecha son NULL.','opcion_multiple',NULL,NULL,NULL),
(37,4,14,'Verdadero o Falso: Un índice siempre mejora el rendimiento de una base de datos.',
'FALSO. Los índices aceleran las lecturas (SELECT) pero ralentizan las escrituras (INSERT, UPDATE, DELETE) porque el índice debe actualizarse. También ocupan espacio en disco. Deben usarse estratégicamente en columnas frecuentemente consultadas.','verdadero_falso',NULL,NULL,NULL),
-- Módulo 4 · Lección 15: Transacciones (nuevos)
(38,4,15,'Un banco transfiere $500 de la cuenta A a la cuenta B. El sistema descuenta de A pero falla antes de acreditar en B. ¿Qué propiedad ACID evita que esto quede así?',
'La Atomicidad garantiza que todas las operaciones de una transacción se completan o ninguna. Si falla en el medio, se hace ROLLBACK y la cuenta A recupera los $500. Sin atomicidad, el dinero desaparecería.','opcion_multiple',NULL,NULL,NULL),
(39,4,15,'Verdadero o Falso: Agregar un índice a una columna siempre mejora el rendimiento de la base de datos.',
'FALSO. Los índices aceleran las lecturas (SELECT) pero ralentizan las escrituras (INSERT, UPDATE, DELETE) porque el índice debe actualizarse. En tablas con muchas escrituras, demasiados índices pueden ser contraproducentes.','verdadero_falso',NULL,NULL,NULL),
-- Módulo 5 · Lección 16: Procesos
(40,5,16,'¿Cuál es la diferencia principal entre un proceso y un hilo?',
'Los procesos tienen memoria independiente (aislados entre sí, más seguros). Los hilos comparten la memoria del proceso padre (más eficientes pero pueden interferir). Crear un hilo es más rápido y usa menos recursos que crear un proceso.','opcion_multiple',NULL,NULL,NULL),
(41,5,16,'Dos hilos incrementan la misma variable simultáneamente: ambos leen 5, calculan 6, y escriben 6. El resultado es 6 en vez de 7. ¿Cómo se llama este problema?',
'Es una condición de carrera (race condition). Ocurre cuando múltiples hilos acceden y modifican datos compartidos sin sincronización. La solución es usar un mutex para que solo un hilo acceda a la variable a la vez.','opcion_multiple',NULL,NULL,NULL),
(42,5,16,'Verdadero o Falso: Un deadlock puede ocurrir con solo dos procesos.',
'VERDADERO. Proceso A tiene el recurso 1 y espera el 2. Proceso B tiene el recurso 2 y espera el 1. Ninguno puede avanzar. Es el escenario mínimo de deadlock. Solución: ordenar la adquisición de recursos o usar timeouts.','verdadero_falso',NULL,NULL,NULL),
-- Módulo 5 · Lección 17: Gestión de memoria (nuevos)
(43,5,17,'¿Qué es un memory leak y por qué es problemático?',
'Un memory leak ocurre cuando el programa reserva memoria pero nunca la libera. Con el tiempo, el programa consume cada vez más RAM hasta que el sistema se queda sin memoria y el programa falla o se vuelve muy lento.','opcion_multiple',NULL,NULL,NULL),
(44,5,17,'¿Cuál es la diferencia entre Stack y Heap en la gestión de memoria?',
'El Stack almacena variables locales y llamadas a funciones; se gestiona automáticamente (LIFO). El Heap almacena memoria dinámica asignada en tiempo de ejecución; debe liberarse manualmente en C/C++ o la gestiona el garbage collector en Java/Python.','opcion_multiple',NULL,NULL,NULL),
-- Módulo 5 · Lección 18: Sistema de archivos (nuevos)
(45,5,18,'¿Por qué las operaciones de E/S (lectura/escritura en disco) son el cuello de botella más común en aplicaciones?',
'Acceder a disco es ~100,000 veces más lento que acceder a RAM. Una lectura de RAM tarda nanosegundos; una de disco tarda milisegundos. Por eso el caching y buffering son críticos para el rendimiento.','opcion_multiple',NULL,NULL,NULL),
(46,5,18,'Verdadero o Falso: En sistemas Unix/Linux, los dispositivos de hardware como el teclado se acceden igual que un archivo.',
'VERDADERO. El principio "todo es un archivo" en Unix unifica el acceso a archivos, dispositivos, pipes y sockets bajo la misma interfaz. Esto simplifica enormemente la programación del sistema.','verdadero_falso',NULL,NULL,NULL);

INSERT INTO `ejercicios` (`id_ejercicio`,`id_modulo`,`id_contenido`,`pregunta`,`retroalimentacion`,`tipo`,`expected_output`,`code_instructions`,`code_hint`) VALUES
-- Módulo 6 · Lección 19: Metodologías
(47,6,19,'¿Cuál es la principal ventaja de Scrum sobre el modelo en cascada?',
'Scrum entrega valor en sprints cortos y se adapta a cambios. En cascada, si los requisitos cambian a mitad del proyecto, el costo es enorme porque todo el diseño previo puede quedar obsoleto. Scrum detecta problemas temprano.','opcion_multiple',NULL,NULL,NULL),
(48,6,19,'En Scrum, ¿quién decide qué funcionalidades entran en el próximo sprint?',
'El equipo de desarrollo decide cuánto puede completar (velocidad). El Product Owner prioriza el backlog según valor de negocio. Juntos negocian qué entra en el sprint. El Scrum Master facilita pero no decide el contenido.','opcion_multiple',NULL,NULL,NULL),
-- Módulo 6 · Lección 20: Patrones
(49,6,20,'¿Qué patrón garantiza que una clase tenga solo una instancia en toda la aplicación?',
'El patrón Singleton garantiza una única instancia. Se usa para recursos compartidos: conexión a BD, configuración global, logs. Su implementación hace el constructor privado y expone un método estático getInstance() que crea la instancia solo si no existe.','opcion_multiple',NULL,NULL,NULL),
(50,6,20,'¿Qué patrón usarías para que múltiples objetos sean notificados automáticamente cuando cambia el estado de otro?',
'El patrón Observer (Publish-Subscribe) permite que múltiples "observadores" se suscriban a un "sujeto" y reciban notificaciones automáticas cuando su estado cambia. Es la base de los eventos en JavaScript y los sistemas reactivos.','opcion_multiple',NULL,NULL,NULL),
-- Módulo 6 · Lección 21: SOLID (nuevos)
(51,6,21,'Tienes una clase Usuario que maneja autenticación, envío de emails y generación de reportes. ¿Qué principio SOLID viola?',
'Viola el principio S (Single Responsibility): una clase debe tener una sola razón para cambiar. Si cambia la lógica de emails, de reportes o de autenticación, hay que modificar la misma clase. Mejor: tres clases separadas.','opcion_multiple',NULL,NULL,NULL),
(52,6,21,'¿Qué significa que una clase esté "abierta para extensión pero cerrada para modificación"?',
'Es el principio Open/Closed (O de SOLID). Puedes agregar nuevo comportamiento creando subclases o implementando interfaces, sin modificar el código existente que ya funciona. Esto evita introducir bugs en código probado.','opcion_multiple',NULL,NULL,NULL),
-- Módulo 7 · Lección 22: OSI y TCP/IP
(53,7,22,'¿En qué capa del modelo OSI opera el protocolo IP?',
'IP opera en la capa 3 (Red). Se encarga del direccionamiento lógico y el enrutamiento de paquetes entre redes. TCP/UDP operan en la capa 4 (Transporte). HTTP opera en la capa 7 (Aplicación). Ethernet opera en la capa 2 (Enlace).','opcion_multiple',NULL,NULL,NULL),
(54,7,22,'¿Cuándo usarías UDP en vez de TCP?',
'UDP es preferible cuando la velocidad importa más que la confiabilidad: videollamadas, streaming, videojuegos en tiempo real. Un paquete perdido en una videollamada es menos grave que el retraso de esperar su retransmisión. TCP es para transferencias donde cada byte importa.','opcion_multiple',NULL,NULL,NULL),
(55,7,22,'Verdadero o Falso: IPv6 fue creado principalmente para solucionar el agotamiento de direcciones IPv4.',
'VERDADERO. IPv4 tiene ~4 mil millones de direcciones, que se agotaron. IPv6 tiene 2¹²⁸ direcciones. También mejora el enrutamiento, la seguridad (IPSec integrado) y elimina la necesidad de NAT.','verdadero_falso',NULL,NULL,NULL),
-- Módulo 7 · Lección 23: HTTP
(56,7,23,'Tu API recibe una petición para crear un nuevo usuario. ¿Qué código de estado HTTP deberías devolver si fue exitoso?',
'201 Created indica que la petición fue exitosa y se creó un nuevo recurso. 200 OK es para peticiones exitosas que no crean recursos. 204 No Content es para operaciones exitosas sin cuerpo de respuesta.','opcion_multiple',NULL,NULL,NULL),
(57,7,23,'¿Qué hace el DNS cuando escribes "github.com" en el navegador?',
'El DNS resuelve el nombre de dominio a una dirección IP. El navegador consulta el servidor DNS configurado (generalmente el del ISP o 8.8.8.8 de Google), que devuelve la IP de github.com. Luego el navegador se conecta a esa IP por TCP.','opcion_multiple',NULL,NULL,NULL),
-- Módulo 7 · Lección 24: Seguridad (nuevos)
(58,7,24,'Un atacante intercepta la comunicación entre un usuario y su banco, leyendo y modificando los mensajes. ¿Cómo se llama este ataque?',
'Es un ataque Man-in-the-Middle (MitM). El atacante se posiciona entre el cliente y el servidor. HTTPS con TLS previene esto porque cifra la comunicación y verifica la identidad del servidor con certificados.','opcion_multiple',NULL,NULL,NULL),
(59,7,24,'Verdadero o Falso: HTTPS garantiza que el sitio web es legítimo y no malicioso.',
'FALSO. HTTPS solo garantiza que la comunicación está cifrada y que el dominio es quien dice ser. Un sitio malicioso puede tener HTTPS perfectamente válido. El candado verde no significa que el sitio sea seguro o confiable.','verdadero_falso',NULL,NULL,NULL);

INSERT INTO `ejercicios` (`id_ejercicio`,`id_modulo`,`id_contenido`,`pregunta`,`retroalimentacion`,`tipo`,`expected_output`,`code_instructions`,`code_hint`) VALUES
-- Módulo 8 · Lección 25: ML
(60,8,25,'¿Cuál es la diferencia entre aprendizaje supervisado y no supervisado?',
'Supervisado: el modelo aprende de datos etiquetados (ejemplos con respuesta correcta). No supervisado: el modelo encuentra patrones en datos sin etiquetas. Supervisado para clasificación/regresión; no supervisado para clustering y reducción de dimensionalidad.','opcion_multiple',NULL,NULL,NULL),
(61,8,25,'Un modelo tiene 99% de precisión en entrenamiento pero 60% en datos nuevos. ¿Qué ocurre?',
'Es overfitting (sobreajuste): el modelo memorizó los datos de entrenamiento en vez de aprender patrones generalizables. Soluciones: más datos de entrenamiento, regularización (L1/L2), dropout, validación cruzada, simplificar el modelo.','opcion_multiple',NULL,NULL,NULL),
(62,8,25,'Verdadero o Falso: El aprendizaje por refuerzo requiere un conjunto de datos etiquetados para entrenar.',
'FALSO. El aprendizaje por refuerzo aprende por prueba y error interactuando con un entorno. El agente recibe recompensas o penalizaciones según sus acciones. No necesita datos etiquetados previos; aprende de la experiencia.','verdadero_falso',NULL,NULL,NULL),
-- Módulo 8 · Lección 26: Redes neuronales
(63,8,26,'¿Qué función de activación se usa típicamente en la capa de salida para clasificación binaria?',
'La función sigmoide produce un valor entre 0 y 1, interpretable como probabilidad. Para clasificación multiclase se usa softmax. Para regresión se usa una función lineal (sin activación). ReLU se usa en capas ocultas, no en la salida.','opcion_multiple',NULL,NULL,NULL),
(64,8,26,'¿Qué es el backpropagation en el entrenamiento de redes neuronales?',
'Backpropagation calcula el gradiente del error respecto a cada peso de la red, propagando el error desde la capa de salida hacia la entrada. Luego el descenso de gradiente ajusta los pesos en la dirección que reduce el error.','opcion_multiple',NULL,NULL,NULL),
-- Módulo 8 · Lección 27: IA generativa (nuevos)
(65,8,27,'¿Qué es el "prompt engineering" y por qué es importante para trabajar con LLMs?',
'El prompt engineering es la habilidad de formular instrucciones efectivas para obtener mejores resultados de un modelo de lenguaje. Un prompt bien diseñado puede hacer la diferencia entre una respuesta genérica y una precisa y útil.','opcion_multiple',NULL,NULL,NULL),
(66,8,27,'Verdadero o Falso: Los modelos de IA generativa como GPT pueden reemplazar completamente a los programadores.',
'FALSO. La IA generativa es una herramienta que amplifica las capacidades del programador. Puede generar código, detectar bugs y documentar, pero requiere supervisión humana para verificar corrección, seguridad y contexto del negocio.','verdadero_falso',NULL,NULL,NULL);

CREATE TABLE `opcion` (
  `id_opcion` INT(11) NOT NULL AUTO_INCREMENT,
  `id_ejercicio` INT(11) NOT NULL,
  `texto` VARCHAR(255) NOT NULL,
  `es_correcta` TINYINT(1) NOT NULL DEFAULT 0,
  `retroalimentacion` TEXT DEFAULT NULL,
  PRIMARY KEY (`id_opcion`),
  KEY `id_ejercicio` (`id_ejercicio`),
  CONSTRAINT `opcion_ibfk_1` FOREIGN KEY (`id_ejercicio`) REFERENCES `ejercicios` (`id_ejercicio`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ── Opciones: longitudes equilibradas, correcta no siempre la más larga ──
INSERT INTO `opcion` (`id_ejercicio`,`texto`,`es_correcta`,`retroalimentacion`) VALUES
-- Ej 1: Propiedad que NO es de un algoritmo
(1,'Ser creativo e impredecible',1,'Correcto. La creatividad no es una propiedad formal. Un algoritmo debe ser finito, definido y efectivo.'),
(1,'Ser finito: debe terminar en algún momento',0,'Ser finito SÍ es requerido. Un algoritmo que no termina no sirve.'),
(1,'Tener pasos claros y sin ambigüedad',0,'Los pasos definidos SÍ son requeridos. Cada instrucción debe ser precisa.'),
(1,'Producir un resultado para cada entrada válida',0,'Producir resultados SÍ es requerido. Sin salida no hay solución.'),
-- Ej 2: Complejidad O(n)
(2,'O(n) — lineal',1,'Correcto. Datos x1000 → tiempo x1000. Eso es crecimiento lineal.'),
(2,'O(1) — constante',0,'O(1) significa que el tiempo no cambia sin importar el tamaño de la entrada.'),
(2,'O(n²) — cuadrático',0,'O(n²) significaría que datos x1000 → tiempo x1,000,000. Aquí solo x1000.'),
(2,'O(log n) — logarítmico',0,'O(log n) crece muy lento. Datos x1000 → tiempo solo x10 aproximadamente.'),
-- Ej 3: Pseudocódigo (V/F)
(3,'Verdadero',0,'FALSO. El pseudocódigo es informal y no sigue ninguna sintaxis de lenguaje específico.'),
(3,'Falso',1,'Correcto. El pseudocódigo comunica lógica en lenguaje natural estructurado, sin sintaxis fija.'),
-- Ej 4: "5" + 2 en JS
(4,'"52" — concatenación de string',1,'Correcto. En JS, string + número hace concatenación. "5" + 2 = "52".'),
(4,'7 — suma numérica',0,'Para sumar habría que convertir: Number("5") + 2 = 7. Sin conversión, JS concatena.'),
(4,'Error de tipo en tiempo de ejecución',0,'JS no lanza error, hace coerción automática y concatena el número como string.'),
(4,'NaN — Not a Number',0,'NaN aparece en operaciones matemáticas inválidas, no en concatenación con strings.'),
-- Ej 6: null vs undefined
(6,'undefined: sin valor asignado; null: vacío intencional',1,'Correcto. undefined lo pone JS automáticamente; null lo asigna el programador a propósito.'),
(6,'Son sinónimos, solo cambia el nombre',0,'No son lo mismo. Tienen orígenes y usos distintos en JavaScript.'),
(6,'null es número cero; undefined es string vacío',0,'null es un tipo especial (typeof null = "object"). undefined es su propio tipo primitivo.'),
(6,'undefined es un error; null es un valor válido',0,'undefined no es un error, es el estado por defecto de variables no inicializadas.'),
-- Ej 7: break en bucle
(7,'0, 1, 2',1,'Correcto. break sale cuando i===3, antes de imprimir 3. Se imprimen 0, 1 y 2.'),
(7,'0, 1, 2, 3',0,'Cuando i===3, el break se ejecuta ANTES de console.log. El 3 no se imprime.'),
(7,'1, 2, 3',0,'El bucle empieza en i=0, así que 0 sí se imprime. El break ocurre en i===3.'),
(7,'0, 1, 2, 3, 4',0,'El break termina el bucle en i===3. Nunca llega a i===4.'),
-- Ej 9: do-while (V/F)
(9,'Verdadero',1,'Correcto. do-while evalúa la condición DESPUÉS del cuerpo, garantizando al menos una ejecución.'),
(9,'Falso',0,'VERDADERO. A diferencia del while, el do-while siempre ejecuta el cuerpo al menos una vez.'),
-- Ej 10: factorial recursivo
(10,'120',1,'Correcto. f(5) = 5×4×3×2×1 = 120. Es el factorial con caso base n≤1 retorna 1.'),
(10,'25',0,'25 sería 5². El factorial multiplica todos los enteros hasta 1: 5! = 120.'),
(10,'15',0,'15 sería 5+4+3+2+1. El factorial usa multiplicación, no suma.'),
(10,'5',0,'5 sería si la función solo devolviera n sin recursión. La recursión multiplica hasta llegar a 1.'),
-- Ej 12: efecto secundario
(12,'Modifica una variable global sin recibirla como parámetro',1,'Correcto. Eso es un efecto secundario. Una función pura no debe tocar estado externo.'),
(12,'No tiene parámetros de entrada',0,'No tener parámetros no es el problema. El problema es modificar una variable global.'),
(12,'No retorna ningún valor',0,'No retornar no es siempre un error. El problema real es el efecto secundario.'),
(12,'El nombre de la función no es descriptivo',0,'"incrementar" es bastante descriptivo. El problema es el efecto secundario sobre la variable global.');

INSERT INTO `opcion` (`id_ejercicio`,`texto`,`es_correcta`,`retroalimentacion`) VALUES
-- Ej 13: clase vs objeto
(13,'La clase es el molde; el objeto es una instancia concreta',1,'Correcto. La clase define la estructura; el objeto es una instancia en memoria con valores propios.'),
(13,'Son lo mismo, cambia el nombre según el contexto',0,'No son lo mismo. La clase es la definición; el objeto es una instancia real en memoria.'),
(13,'La clase se ejecuta; el objeto se declara',0,'Al revés: la clase se declara; el objeto se crea (instancia) y existe en memoria.'),
(13,'Un objeto puede existir sin una clase',0,'En POO clásica, todo objeto es instancia de una clase. Sin clase no hay objeto.'),
-- Ej 14: atributos públicos
(14,'Cualquier código puede modificarlos sin validación',1,'Correcto. Sin encapsulamiento, el objeto puede quedar en estados inválidos.'),
(14,'Los atributos públicos son más rápidos de acceder',0,'La diferencia de rendimiento es mínima. El problema es la falta de control.'),
(14,'Los atributos públicos no pueden heredarse',0,'Los atributos públicos SÍ se heredan. El problema es la falta de encapsulamiento.'),
(14,'No hay ningún problema, es una buena práctica',0,'Es una mala práctica. Sin encapsulamiento el objeto puede quedar en estados inválidos.'),
-- Ej 15: constructor (V/F)
(15,'Verdadero',1,'Correcto. El constructor se llama automáticamente con new e inicializa el estado del objeto.'),
(15,'Falso',0,'VERDADERO. El constructor es especial precisamente porque se llama solo al crear el objeto.'),
-- Ej 16: método en Animal
(16,'respirar()',1,'Correcto. Es común a todos los animales. Lo específico (ladrar, maullar) va en cada subclase.'),
(16,'ladrar()',0,'ladrar() es específico de Perro. Ponerlo en Animal obligaría a Gato a tenerlo también.'),
(16,'maullar()',0,'maullar() es específico de Gato. La herencia comparte lo común; lo específico va en la subclase.'),
(16,'tenerNombre()',0,'El nombre es un atributo, no un método de comportamiento. No es el mejor ejemplo de herencia.'),
-- Ej 17: polimorfismo
(17,'Mismo método, distinto comportamiento según la clase',1,'Correcto. calcularArea() en Círculo y Rectángulo hacen cosas distintas con el mismo nombre.'),
(17,'Una clase puede tener múltiples constructores',0,'Eso es sobrecarga de constructores, no polimorfismo.'),
(17,'Un objeto cambia de clase en tiempo de ejecución',0,'Los objetos no cambian de clase. El polimorfismo trata objetos distintos de forma uniforme.'),
(17,'Dos clases pueden tener el mismo nombre',0,'Eso causaría conflictos. El polimorfismo es sobre métodos con el mismo nombre y distinto comportamiento.'),
-- Ej 18: override (V/F)
(18,'Verdadero',0,'FALSO. Los métodos final/sealed no pueden sobrescribirse. Los privados tampoco se heredan.'),
(18,'Falso',1,'Correcto. Los métodos final (Java) o sealed (C#) no pueden sobrescribirse.'),
-- Ej 19: clase abstracta vs interfaz
(19,'Clase abstracta puede tener código; interfaz solo declara contratos',1,'Correcto. La clase abstracta puede tener métodos implementados; la interfaz solo define qué métodos debe haber.'),
(19,'Son exactamente lo mismo',0,'No son lo mismo. La clase abstracta puede tener código; la interfaz es un contrato puro.'),
(19,'La interfaz puede instanciarse directamente',0,'Ninguna puede instanciarse directamente. Ambas requieren implementación concreta.'),
(19,'Una clase puede heredar de múltiples clases abstractas',0,'En Java/C# solo se hereda de UNA clase. Sí se pueden implementar múltiples interfaces.'),
-- Ej 20: programar hacia interfaces
(20,'Puedes cambiar la implementación sin tocar el código que la usa',1,'Correcto. Si dependes de una interfaz, cambiar MySQL por MongoDB no requiere tocar la lógica de negocio.'),
(20,'Las interfaces son más rápidas que las clases concretas',0,'No hay diferencia de rendimiento significativa. La ventaja es el desacoplamiento.'),
(20,'Las interfaces permiten crear objetos directamente',0,'Las interfaces no pueden instanciarse. Su ventaja es el desacoplamiento.'),
(20,'Programar hacia interfaces evita usar herencia',0,'No evita la herencia; la complementa. Puedes usar ambas juntas.'),
-- Ej 21: pilares POO - polimorfismo
(21,'Polimorfismo',1,'Correcto. Mismo método, distinto comportamiento según el objeto.'),
(21,'Encapsulamiento',0,'El encapsulamiento oculta datos internos, no define comportamiento diferente por objeto.'),
(21,'Herencia',0,'La herencia reutiliza código de la clase padre, no define comportamiento diferente por objeto.'),
(21,'Abstracción',0,'La abstracción modela solo lo relevante del problema, no define comportamiento diferente por objeto.'),
-- Ej 22: 4 pilares simultáneos (V/F)
(22,'Verdadero',1,'Correcto. Los 4 pilares se complementan y pueden aplicarse juntos en una misma clase.'),
(22,'Falso',0,'VERDADERO. Los pilares no son excluyentes; se diseñan para trabajar juntos.');

INSERT INTO `opcion` (`id_ejercicio`,`texto`,`es_correcta`,`retroalimentacion`) VALUES
-- Ej 23: O(1) en arreglo
(23,'Acceder por índice: arr[500000]',1,'Correcto. El acceso por índice es O(1): dirección = base + índice × tamaño. Instantáneo.'),
(23,'Insertar al inicio del arreglo',0,'Insertar al inicio es O(n): hay que desplazar todos los elementos una posición.'),
(23,'Buscar un elemento sin conocer su índice',0,'Buscar sin índice es O(n): hay que revisar elemento por elemento.'),
(23,'Eliminar el elemento del medio',0,'Eliminar del medio es O(n): hay que desplazar todos los elementos posteriores.'),
-- Ej 24: lista enlazada vs arreglo
(24,'Cuando insertas o eliminas frecuentemente al inicio o en el medio',1,'Correcto. Las listas no requieren desplazar elementos al insertar/eliminar, solo actualizar punteros.'),
(24,'Cuando necesitas acceso rápido por índice',0,'Para acceso por índice, el arreglo es mejor: O(1) vs O(n) en lista enlazada.'),
(24,'Cuando el tamaño es fijo y conocido de antemano',0,'Si el tamaño es fijo, el arreglo es más eficiente en memoria (sin overhead de punteros).'),
(24,'Cuando necesitas ordenar los datos frecuentemente',0,'Ordenar una lista enlazada es más complejo. Para ordenamiento frecuente, el arreglo es mejor.'),
-- Ej 26: historial del navegador
(26,'Pila (stack) — LIFO',1,'Correcto. Cada página se apila; "Atrás" desapila la última visitada.'),
(26,'Cola (queue) — FIFO',0,'Una cola atendería páginas en orden de visita, no en orden inverso. No sirve para "Atrás".'),
(26,'Árbol binario de búsqueda',0,'Un árbol no tiene estructura natural para el historial secuencial de navegación.'),
(26,'Lista enlazada simple',0,'Una lista podría implementarlo, pero la abstracción correcta es la pila (LIFO).'),
-- Ej 27: cola de atención
(27,'Cola (queue) — FIFO',1,'Correcto. El primero en llegar es el primero en ser atendido. Justo y predecible.'),
(27,'Pila (stack) — LIFO',0,'Una pila atendería primero al último en llegar. Injusto para quienes esperan más.'),
(27,'Árbol de prioridad',0,'Válido si hay prioridades distintas, pero para atención igualitaria la cola simple es mejor.'),
(27,'Arreglo desordenado',0,'Un arreglo desordenado no garantiza ningún orden de atención.'),
-- Ej 28: pila con arreglo (V/F)
(28,'Verdadero',1,'Correcto. push() agrega al final y pop() quita del final, ambos O(1). Implementación válida.'),
(28,'Falso',0,'VERDADERO. Los arreglos tienen push() y pop() nativos que implementan perfectamente una pila.'),
-- Ej 29: inserción en BST
(29,'Al subárbol izquierdo de la raíz',1,'Correcto. 7 < 10, va a la izquierda. En BST: menores a la izquierda, mayores a la derecha.'),
(29,'Al subárbol derecho de la raíz',0,'7 < 10, así que va a la izquierda. La derecha es para valores mayores que la raíz.'),
(29,'Reemplaza la raíz',0,'En un BST los valores nuevos nunca reemplazan la raíz en una inserción normal.'),
(29,'No se puede insertar porque ya existe la raíz',0,'Un BST puede tener múltiples nodos. La raíz es solo el punto de entrada.'),
-- Ej 30: camino más corto
(30,'Dijkstra',1,'Correcto. Dijkstra encuentra el camino más corto en grafos con pesos no negativos.'),
(30,'BFS (Búsqueda en anchura)',0,'BFS encuentra el camino con menos aristas (saltos), no el de menor distancia/peso.'),
(30,'DFS (Búsqueda en profundidad)',0,'DFS no garantiza el camino más corto. Explora tan profundo como puede antes de retroceder.'),
(30,'Bubble Sort',0,'Bubble Sort es un algoritmo de ordenamiento, no de búsqueda de caminos en grafos.'),
-- Ej 31: algoritmo eficiente para 10,000 elementos
(31,'Merge Sort o Quick Sort — O(n log n)',1,'Correcto. Para 10,000 elementos, O(n log n) hace ~130,000 operaciones vs 100 millones de Bubble Sort.'),
(31,'Bubble Sort — fácil de implementar',0,'Bubble Sort es O(n²). Para 10,000 elementos haría 100 millones de comparaciones. Muy lento.'),
(31,'Búsqueda binaria — divide el espacio a la mitad',0,'La búsqueda binaria es para buscar, no para ordenar. Son problemas distintos.'),
(31,'Selection Sort — selecciona el mínimo en cada paso',0,'Selection Sort también es O(n²). Para grandes volúmenes es igual de lento que Bubble Sort.'),
-- Ej 32: búsqueda binaria requiere orden
(32,'Porque necesita saber en qué mitad buscar en cada paso',1,'Correcto. Sin orden, no puede determinar si el elemento está en la mitad izquierda o derecha.'),
(32,'Porque es más rápida con datos ordenados',0,'No es solo más rápida: directamente no funciona correctamente con datos desordenados.'),
(32,'Porque los datos ordenados ocupan menos memoria',0,'El orden no afecta el uso de memoria. El problema es lógico: no puede dividir correctamente sin orden.'),
(32,'Porque fue diseñada solo para números enteros',0,'La búsqueda binaria funciona con cualquier tipo de dato comparable. El requisito es que estén ordenados.');

INSERT INTO `opcion` (`id_ejercicio`,`texto`,`es_correcta`,`retroalimentacion`) VALUES
-- Ej 33: normalización
(33,'Elimina redundancia y anomalías de actualización',1,'Correcto. Evita que el mismo dato esté en múltiples lugares, reduciendo inconsistencias.'),
(33,'Hace las consultas SELECT más rápidas',0,'La normalización puede hacer algunas consultas más lentas por los JOINs adicionales.'),
(33,'Permite almacenar más datos en menos espacio',0,'Su objetivo principal es la integridad de datos, no la compresión de almacenamiento.'),
(33,'Elimina la necesidad de claves primarias',0,'La normalización requiere claves primarias para identificar filas únicas. No las elimina.'),
-- Ej 34: diseño de tabla Pedidos
(34,'Redundancia: nombre y email del cliente se repiten en cada pedido',1,'Correcto. Si el cliente hace 100 pedidos, sus datos se repiten 100 veces. Solución: tabla Clientes separada.'),
(34,'La tabla tiene demasiadas columnas',0,'5 columnas no es demasiado. El problema es la redundancia de datos del cliente.'),
(34,'No tiene clave primaria definida',0,'La tabla tiene "id" como clave primaria. El problema es la redundancia de datos.'),
(34,'Los nombres de columnas son incorrectos',0,'Los nombres son descriptivos. El problema de diseño es la redundancia de datos del cliente.'),
-- Ej 35: GROUP BY + HAVING
(35,'Los departamentos con más de 5 empleados y su conteo',1,'Correcto. GROUP BY agrupa, COUNT(*) cuenta, HAVING filtra grupos con más de 5.'),
(35,'Todos los empleados de departamentos grandes',0,'La consulta devuelve departamentos agrupados, no empleados individuales.'),
(35,'Los 5 empleados con más antigüedad',0,'No hay ORDER BY ni LIMIT 5, y no se menciona antigüedad en la consulta.'),
(35,'Un error porque HAVING no funciona con COUNT',0,'HAVING sí funciona con COUNT y otras funciones de agregación. Es precisamente para eso.'),
-- Ej 36: INNER vs LEFT JOIN
(36,'INNER: solo coincidencias en ambas tablas; LEFT: todas las filas de la izquierda',1,'Correcto. LEFT JOIN incluye filas sin coincidencia de la tabla izquierda, con NULL en la derecha.'),
(36,'Son exactamente lo mismo',0,'No son lo mismo. INNER excluye filas sin coincidencia; LEFT las incluye con NULL.'),
(36,'LEFT JOIN siempre es más lento',0,'El rendimiento depende de índices y datos, no del tipo de JOIN en sí.'),
(36,'INNER JOIN solo funciona con claves primarias',0,'INNER JOIN funciona con cualquier condición de igualdad, no solo con PKs.'),
-- Ej 37: índices (V/F)
(37,'Verdadero',0,'FALSO. Los índices ralentizan escrituras y ocupan espacio. Deben usarse estratégicamente.'),
(37,'Falso',1,'Correcto. Los índices aceleran lecturas pero ralentizan INSERT/UPDATE/DELETE. Úsalos con criterio.'),
-- Ej 38: atomicidad
(38,'Atomicidad — todo o nada',1,'Correcto. La atomicidad garantiza que si falla cualquier paso, se hace ROLLBACK de toda la transacción.'),
(38,'Consistencia — los datos siempre son válidos',0,'La consistencia garantiza que las reglas de negocio se cumplan, pero no revierte operaciones parciales.'),
(38,'Durabilidad — los datos persisten tras un commit',0,'La durabilidad garantiza que los datos confirmados no se pierden, pero no maneja fallos a mitad de transacción.'),
(38,'Aislamiento — las transacciones no se interfieren',0,'El aislamiento evita que transacciones concurrentes se interfieran, pero no revierte operaciones parciales.'),
-- Ej 39: índices V/F (lección 15)
(39,'Verdadero',0,'FALSO. Los índices ralentizan escrituras y ocupan espacio. Deben usarse estratégicamente.'),
(39,'Falso',1,'Correcto. Los índices aceleran lecturas pero ralentizan INSERT/UPDATE/DELETE. Úsalos con criterio.'),
-- Ej 40: proceso vs hilo
(40,'Procesos: memoria independiente; hilos: comparten memoria del proceso',1,'Correcto. Procesos están aislados (más seguros). Hilos comparten memoria (más eficientes).'),
(40,'Los hilos son más lentos que los procesos',0,'Al contrario: los hilos son más rápidos de crear y usan menos recursos.'),
(40,'Un proceso solo puede tener un hilo',0,'Un proceso puede tener múltiples hilos. El hilo principal es solo uno de ellos.'),
(40,'Los procesos comparten memoria entre sí por defecto',0,'Los procesos tienen memoria independiente. Para compartirla necesitan mecanismos especiales (IPC).');

INSERT INTO `opcion` (`id_ejercicio`,`texto`,`es_correcta`,`retroalimentacion`) VALUES
-- Ej 41: condición de carrera
(41,'Condición de carrera (race condition)',1,'Correcto. Dos hilos leen el mismo valor, calculan el mismo resultado y escriben, perdiendo una actualización.'),
(41,'Deadlock',0,'Deadlock es cuando dos procesos esperan recursos que el otro tiene. Aquí el problema es acceso concurrente.'),
(41,'Stack overflow',0,'Stack overflow ocurre por recursión infinita. Aquí el problema es acceso concurrente sin sincronización.'),
(41,'Memory leak',0,'Memory leak es memoria no liberada. Aquí el problema es acceso concurrente a datos compartidos.'),
-- Ej 42: deadlock con 2 procesos (V/F)
(42,'Verdadero',1,'Correcto. A espera recurso que tiene B; B espera recurso que tiene A. Deadlock mínimo con 2 procesos.'),
(42,'Falso',0,'VERDADERO. El deadlock mínimo requiere 2 procesos y 2 recursos. Es el escenario clásico.'),
-- Ej 43: memory leak
(43,'Memoria reservada que nunca se libera, consumiendo RAM progresivamente',1,'Correcto. Con el tiempo el programa consume toda la RAM disponible y falla o se vuelve muy lento.'),
(43,'Un error que hace que el programa se cierre inmediatamente',0,'Un memory leak no cierra el programa de inmediato; lo degrada gradualmente con el tiempo.'),
(43,'Cuando dos procesos acceden a la misma variable simultáneamente',0,'Eso es una condición de carrera (race condition), no un memory leak.'),
(43,'Cuando el programa usa más memoria de la que tiene disponible',0,'Eso es un out-of-memory error. Un memory leak es la causa que puede llevar a ese error.'),
-- Ej 44: Stack vs Heap
(44,'Stack: variables locales, automático (LIFO); Heap: memoria dinámica, manual o GC',1,'Correcto. El Stack se gestiona solo; el Heap requiere gestión explícita o garbage collector.'),
(44,'Stack es más grande que el Heap',0,'Al contrario: el Heap es generalmente mucho más grande que el Stack. El Stack tiene un límite pequeño.'),
(44,'Son lo mismo, solo cambia el nombre',0,'Son regiones de memoria distintas con propósitos, tamaños y formas de gestión completamente diferentes.'),
(44,'El Heap almacena el código del programa',0,'El código del programa se almacena en el segmento de texto/código. El Heap es para datos dinámicos.'),
-- Ej 45: E/S lenta
(45,'Acceder a disco es ~100,000 veces más lento que acceder a RAM',1,'Correcto. RAM: nanosegundos. Disco HDD: milisegundos. SSD: microsegundos. La diferencia es enorme.'),
(45,'El disco duro tiene menos capacidad que la RAM',0,'Al contrario: el disco tiene mucha más capacidad que la RAM. El problema es la velocidad, no la capacidad.'),
(45,'Las operaciones de E/S requieren más líneas de código',0,'La cantidad de código no determina la velocidad. El problema es físico: el disco es mecánicamente lento.'),
(45,'El sistema operativo no puede optimizar las operaciones de disco',0,'El SO sí optimiza con buffering y caching. Pero incluso optimizado, el disco sigue siendo mucho más lento que la RAM.'),
-- Ej 46: todo es un archivo V/F
(46,'Verdadero',1,'Correcto. En Unix, dispositivos, pipes y sockets se acceden con la misma interfaz que los archivos.'),
(46,'Falso',0,'VERDADERO. El principio "todo es un archivo" en Unix unifica el acceso a hardware y software bajo una sola interfaz.'),
-- Ej 47: Scrum vs cascada
(47,'Entrega valor en iteraciones cortas y se adapta a cambios',1,'Correcto. Scrum detecta problemas temprano y permite cambiar el rumbo sin desperdiciar todo el trabajo.'),
(47,'Es más barato de implementar',0,'Ágil no es necesariamente más barato. Su ventaja es la adaptabilidad y entrega continua.'),
(47,'No requiere ningún tipo de documentación',0,'Ágil valora software funcionando sobre documentación exhaustiva, pero no la elimina por completo.'),
(47,'Garantiza que el proyecto termine a tiempo',0,'Ninguna metodología garantiza esto. Ágil ayuda a detectar problemas temprano y ajustar el alcance.'),
-- Ej 48: quién decide el sprint
(48,'El equipo decide cuánto puede hacer; el PO prioriza qué entra',1,'Correcto. Es una negociación: el PO prioriza por valor de negocio, el equipo por capacidad técnica.'),
(48,'Solo el Scrum Master decide el contenido',0,'El Scrum Master facilita el proceso pero no decide el contenido del sprint.'),
(48,'El cliente externo decide directamente',0,'El cliente trabaja con el Product Owner, quien representa sus intereses en el equipo.'),
(48,'El equipo decide todo sin consultar al PO',0,'El equipo decide cuánto puede hacer, pero el PO decide qué es más valioso para el negocio.');

INSERT INTO `opcion` (`id_ejercicio`,`texto`,`es_correcta`,`retroalimentacion`) VALUES
-- Ej 49: Singleton
(49,'Singleton',1,'Correcto. Singleton garantiza una única instancia con constructor privado y método estático getInstance().'),
(49,'Factory',0,'Factory delega la creación de objetos a subclases, pero no garantiza una única instancia.'),
(49,'Observer',0,'Observer notifica cambios a múltiples objetos. No tiene relación con el número de instancias.'),
(49,'Prototype',0,'Prototype crea objetos clonando una instancia existente. No garantiza una única instancia.'),
-- Ej 50: Observer
(50,'Observer (Publish-Subscribe)',1,'Correcto. Observer permite que múltiples objetos se suscriban y reciban notificaciones automáticas.'),
(50,'Singleton',0,'Singleton garantiza una única instancia. No gestiona notificaciones entre objetos.'),
(50,'Factory',0,'Factory crea objetos. No gestiona notificaciones entre objetos.'),
(50,'Adapter',0,'Adapter compatibiliza interfaces incompatibles. No gestiona notificaciones entre objetos.'),
-- Ej 51: Single Responsibility
(51,'S — Single Responsibility: una clase, una razón para cambiar',1,'Correcto. Una clase con autenticación, emails y reportes tiene tres razones para cambiar. Viola SRP.'),
(51,'O — Open/Closed: abierto para extensión',0,'Open/Closed se viola cuando modificas código existente para agregar funcionalidad. Aquí el problema es tener demasiadas responsabilidades.'),
(51,'D — Dependency Inversion: depender de abstracciones',0,'Dependency Inversion se viola cuando dependes de clases concretas en vez de interfaces. Aquí el problema es tener demasiadas responsabilidades.'),
(51,'I — Interface Segregation: interfaces específicas',0,'Interface Segregation se viola cuando una interfaz tiene demasiados métodos. Aquí el problema es la clase con demasiadas responsabilidades.'),
-- Ej 52: Open/Closed
(52,'Agregar comportamiento nuevo sin modificar el código existente',1,'Correcto. Extiendes creando subclases o implementando interfaces, sin tocar código que ya funciona.'),
(52,'Que la clase puede usarse en cualquier proyecto',0,'La reutilización es una consecuencia del buen diseño, no la definición de Open/Closed.'),
(52,'Que el código fuente está disponible públicamente',0,'Open/Closed no tiene relación con código abierto (open source). Es un principio de diseño orientado a objetos.'),
(52,'Que la clase no puede tener métodos privados',0,'Los métodos privados son parte del encapsulamiento y no tienen relación con el principio Open/Closed.'),
-- Ej 53: capa de IP
(53,'Capa 3 — Red',1,'Correcto. IP opera en la capa 3: direccionamiento lógico y enrutamiento entre redes.'),
(53,'Capa 4 — Transporte',0,'La capa 4 es donde operan TCP y UDP. IP está en la capa 3.'),
(53,'Capa 7 — Aplicación',0,'La capa 7 es donde operan HTTP, DNS, SMTP. IP está en la capa 3.'),
(53,'Capa 1 — Física',0,'La capa 1 maneja señales eléctricas. IP está en la capa 3.'),
-- Ej 54: UDP vs TCP
(54,'Streaming, videojuegos y videollamadas en tiempo real',1,'Correcto. En tiempo real, un paquete perdido es menos grave que el retraso de retransmitirlo.'),
(54,'Transferencia de archivos importantes',0,'Para archivos, TCP es mejor: garantiza que cada byte llegue correctamente y en orden.'),
(54,'Cuando la seguridad es la prioridad',0,'La seguridad depende del cifrado (TLS/DTLS), no del protocolo de transporte.'),
(54,'Cuando necesitas garantizar entrega ordenada',0,'Para entrega garantizada y ordenada, TCP es la elección correcta, no UDP.'),
-- Ej 55: IPv6 (V/F)
(55,'Verdadero',1,'Correcto. IPv4 se agotó (~4 mil millones de IPs). IPv6 tiene 2¹²⁸ direcciones.'),
(55,'Falso',0,'VERDADERO. El agotamiento de IPv4 fue la razón principal para crear IPv6.'),
-- Ej 56: código 201
(56,'201 Created',1,'Correcto. 201 indica que la petición fue exitosa y se creó un nuevo recurso.'),
(56,'200 OK',0,'200 OK es para peticiones exitosas en general. Para creación de recursos, 201 es más específico.'),
(56,'204 No Content',0,'204 es para operaciones exitosas sin cuerpo de respuesta, como un DELETE exitoso.'),
(56,'200 Created',0,'200 Created no existe como código HTTP estándar. Son 200 OK y 201 Created por separado.'),
-- Ej 57: DNS
(57,'Traduce el nombre de dominio a una dirección IP',1,'Correcto. DNS es la "agenda telefónica" de internet: convierte nombres legibles en IPs numéricas.'),
(57,'Descarga el contenido de la página web',0,'El DNS solo resuelve el nombre a IP. La descarga la hace HTTP después de obtener la IP.'),
(57,'Verifica si el sitio web es seguro',0,'La seguridad la gestiona TLS/HTTPS. El DNS solo resuelve nombres de dominio.'),
(57,'Asigna una dirección IP a tu computadora',0,'Tu IP la asigna el servidor DHCP de tu red local. El DNS resuelve nombres externos.');

INSERT INTO `opcion` (`id_ejercicio`,`texto`,`es_correcta`,`retroalimentacion`) VALUES
-- Ej 58: Man-in-the-Middle
(58,'Man-in-the-Middle (MitM)',1,'Correcto. El atacante se interpone entre cliente y servidor, leyendo y modificando la comunicación.'),
(58,'DDoS (Denegación de servicio)',0,'DDoS satura un servidor con tráfico para dejarlo inaccesible. No intercepta comunicaciones.'),
(58,'SQL Injection',0,'SQL Injection inyecta código malicioso en consultas de base de datos. No intercepta comunicaciones de red.'),
(58,'Phishing',0,'Phishing engaña al usuario para que entregue sus credenciales. No intercepta comunicaciones en tránsito.'),
-- Ej 59: HTTPS V/F
(59,'Verdadero',0,'FALSO. HTTPS solo cifra la comunicación y verifica el dominio. Un sitio malicioso puede tener HTTPS válido.'),
(59,'Falso',1,'Correcto. HTTPS garantiza cifrado y autenticidad del dominio, no que el sitio sea seguro o confiable.'),
-- Ej 60: supervisado vs no supervisado
(60,'Supervisado: datos etiquetados; No supervisado: sin etiquetas',1,'Correcto. Supervisado necesita ejemplos con respuesta correcta. No supervisado descubre estructura solo.'),
(60,'Son lo mismo, solo cambia el nombre',0,'Son fundamentalmente diferentes. Supervisado necesita etiquetas; no supervisado no.'),
(60,'Supervisado siempre es más rápido de entrenar',0,'La velocidad depende del algoritmo y los datos, no de si es supervisado o no.'),
(60,'No supervisado siempre da mejores resultados',0,'Depende del problema. Para clasificación con etiquetas disponibles, el supervisado suele ser mejor.'),
-- Ej 61: overfitting
(61,'Overfitting: memorizó los datos de entrenamiento y no generaliza',1,'Correcto. Alta precisión en entrenamiento y baja en datos nuevos es la señal clásica de overfitting.'),
(61,'Underfitting: el modelo es demasiado simple',0,'Underfitting da baja precisión tanto en entrenamiento como en datos nuevos. Aquí el entrenamiento es 99%.'),
(61,'El conjunto de datos de prueba está mal construido',0,'Podría ser, pero 99% en entrenamiento y 60% en prueba apunta claramente a overfitting.'),
(61,'El modelo necesita más épocas de entrenamiento',0,'Más entrenamiento empeoraría el overfitting. La solución es regularización o más datos.'),
-- Ej 62: aprendizaje por refuerzo (V/F)
(62,'Verdadero',0,'FALSO. El aprendizaje por refuerzo aprende por prueba y error con recompensas, sin datos etiquetados.'),
(62,'Falso',1,'Correcto. El aprendizaje por refuerzo no necesita datos etiquetados. Aprende interactuando con el entorno.'),
-- Ej 63: función de activación
(63,'Sigmoide — valores entre 0 y 1',1,'Correcto. Sigmoide es ideal para clasificación binaria: produce probabilidades entre 0 y 1.'),
(63,'ReLU — rectified linear unit',0,'ReLU se usa en capas ocultas para evitar el gradiente desvaneciente, no en la salida binaria.'),
(63,'Tanh — tangente hiperbólica',0,'Tanh produce valores entre -1 y 1. Para probabilidades (0 a 1), sigmoide es mejor.'),
(63,'Lineal — sin activación',0,'La función lineal se usa en regresión (valores continuos), no en clasificación binaria.'),
-- Ej 64: backpropagation
(64,'Calcula gradientes del error y los propaga de salida hacia entrada',1,'Correcto. Backpropagation usa la regla de la cadena para calcular gradientes y ajustar pesos.'),
(64,'Propaga los datos de entrada hacia adelante para obtener predicción',0,'Eso es forward propagation. Backpropagation va en sentido contrario: de salida hacia entrada.'),
(64,'Elimina neuronas con menor activación para simplificar la red',0,'Eso es pruning (poda). Backpropagation calcula gradientes, no elimina neuronas.'),
(64,'Divide la red en capas para procesarlas en paralelo',0,'La paralelización es una técnica de implementación. Backpropagation es el algoritmo de gradientes.'),
-- Ej 65: prompt engineering
(65,'Formular instrucciones efectivas para obtener mejores resultados de un LLM',1,'Correcto. Un buen prompt puede hacer la diferencia entre una respuesta genérica y una precisa y útil.'),
(65,'Programar el modelo de IA desde cero',0,'El prompt engineering no implica programar el modelo. Es sobre cómo comunicarse efectivamente con él.'),
(65,'Optimizar el hardware donde corre el modelo',0,'La optimización de hardware es ingeniería de infraestructura. El prompt engineering es sobre las instrucciones al modelo.'),
(65,'Traducir código a lenguaje natural automáticamente',0,'Eso es una aplicación de los LLMs, no la definición de prompt engineering.'),
-- Ej 66: IA reemplaza programadores V/F
(66,'Verdadero',0,'FALSO. La IA amplifica las capacidades del programador pero requiere supervisión humana para verificar corrección y contexto.'),
(66,'Falso',1,'Correcto. La IA es una herramienta poderosa pero necesita al programador para verificar, contextualizar y tomar decisiones.');

CREATE TABLE `progreso` (
  `id_progreso` INT(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` INT(11) NOT NULL,
  `id_ejercicio` INT(11) NOT NULL,
  `intentos` INT(11) DEFAULT 0,
  `calificacion` DECIMAL(5,2) DEFAULT NULL,
  `completado` TINYINT(1) DEFAULT 0,
  `alguna_vez_correcto` TINYINT(1) NOT NULL DEFAULT 0,
  `fecha_progreso` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_progreso`),
  UNIQUE KEY `unico_progreso` (`id_usuario`,`id_ejercicio`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_ejercicio` (`id_ejercicio`),
  CONSTRAINT `progreso_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `progreso_ibfk_2` FOREIGN KEY (`id_ejercicio`) REFERENCES `ejercicios` (`id_ejercicio`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE OR REPLACE VIEW `v_ranking_usuarios` AS
SELECT u.id_usuario, u.nombre, u.usuario, u.avatar, u.fecha_registro,
       LEAST(COUNT(DISTINCT CASE WHEN p.alguna_vez_correcto=1 THEN p.id_ejercicio END),
             (SELECT COUNT(*) FROM ejercicios)) AS total_completados,
       MAX(p.fecha_progreso) AS ultima_actividad
FROM usuario u
LEFT JOIN progreso p ON p.id_usuario=u.id_usuario
WHERE u.is_admin=0 GROUP BY u.id_usuario ORDER BY total_completados DESC;

CREATE OR REPLACE VIEW `v_modulos_curso` AS
SELECT m.id_modulo, m.id_curso, m.nombre, m.descripcion, m.categoria, m.orden,
       COUNT(e.id_ejercicio) AS total_ejercicios
FROM modulos m LEFT JOIN ejercicios e ON e.id_modulo=m.id_modulo
GROUP BY m.id_modulo ORDER BY m.orden ASC;

COMMIT;
