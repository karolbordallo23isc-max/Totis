USE loopbook;
-- Tabla Usuario
CREATE TABLE Usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    fecha_registro DATETIME NOT NULL
);
-- Tabla Curso
CREATE TABLE curso (
    id_curso INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT
);
-- Tabla Inscripción
CREATE TABLE inscripcion (
    id_inscripcion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_curso INT NOT NULL,
    estado_inscripcion ENUM('activo','completado','cancelado') NOT NULL,
    fecha_inscripcion DATETIME NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario),
    FOREIGN KEY (id_curso) REFERENCES curso(id_curso)
);

-- Tabla Módulos
CREATE TABLE modulos (
    id_modulo INT AUTO_INCREMENT PRIMARY KEY,
    id_curso INT NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    orden INT NOT NULL,
    FOREIGN KEY (id_curso) REFERENCES curso(id_curso)
);

-- Tabla Contenido
CREATE TABLE contenido (
    id_contenido INT AUTO_INCREMENT PRIMARY KEY,
    id_modulo INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    texto TEXT,
    tipo ENUM('texto','video','imagen') NOT NULL,
    url VARCHAR(255),
    orden INT NOT NULL,
    FOREIGN KEY (id_modulo) REFERENCES modulos(id_modulo)
);

-- Tabla Ejercicios
CREATE TABLE ejercicios (
    id_ejercicio INT AUTO_INCREMENT PRIMARY KEY,
    id_modulo INT NOT NULL,
    pregunta TEXT NOT NULL,
    retroalimentacion TEXT,
    tipo ENUM('opcion_multiple','verdadero_falso') NOT NULL,
    fecha_creacion DATETIME NOT NULL,
    FOREIGN KEY (id_modulo) 
REFERENCES modulos(id_modulo)
);

-- Tabla Opciones
CREATE TABLE opcion (
    id_opcion INT AUTO_INCREMENT PRIMARY KEY,
    id_ejercicio INT NOT NULL,
    texto VARCHAR(255) NOT NULL,
    es_correcta BOOLEAN NOT NULL,
    retroalimentacion TEXT,
    FOREIGN KEY (id_ejercicio) REFERENCES ejercicios(id_ejercicio)
);

-- Tabla Progreso
CREATE TABLE progreso (
    id_progreso INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_ejercicio INT NOT NULL,
    intentos INT,
    calificacion DECIMAL(5,2),
    completado BOOLEAN,
    fecha_progreso DATETIME,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario),
    FOREIGN KEY (id_ejercicio) REFERENCES ejercicios(id_ejercicio)
);