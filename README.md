# LoopBook - Plataforma Educativa de Programación
### Equipo Totis

---

## Descripción del Proyecto

LoopBook es una plataforma web diseñada para facilitar el aprendizaje de lógica y lenguajes de programación a estudiantes de nuevo ingreso. El sistema permite gestionar cursos, módulos y actividades, además de realizar el seguimiento del progreso individual de cada usuario de forma automática.

---

## Tecnologías y Arquitectura

Este proyecto está construido bajo una arquitectura de N-Capas:

- **Frontend:** Interfaz de usuario desarrollada con HTML5 y CSS3.
- **Backend:** Lógica de negocio y renderización de vistas dinámicas desarrollada en PHP 8.x.
- **Base de Datos:** Relacional, gestionada con MySQL, normalizada en 3FN.

La comunicación entre capas se realiza mediante PHP como intermediario: las vistas consumen directamente los datos procesados por el backend a través de consultas PDO parametrizadas, garantizando seguridad contra inyecciones SQL.

---

## Instrucciones de Ejecución (Local)

Para correr este proyecto en un entorno local con XAMPP:

1. **Clonación:** Descarga o clona este repositorio dentro de tu carpeta `C:/xampp/htdocs/`:
   ```
   git clone <url-del-repositorio> C:/xampp/htdocs/Totis
   ```

2. **Base de Datos:**
   - Abre phpMyAdmin en `http://localhost/phpmyadmin`.
   - Crea una base de datos llamada `loopbook`.
   - Importa el archivo localizado en `/Base de datos/loopbook.sql`.

3. **Configuración de conexión:**
   - Abre el archivo `diseñador.txt/conexion.php`.
   - Ajusta las credenciales de MySQL según tu entorno:
     ```php
     $usuario  = "tu_usuario";
     $password = "tu_contraseña";
     $port     = "3306"; // o 3307 según tu instalación
     ```

4. **Acceso:** Abre tu navegador e ingresa a:
   ```
   http://localhost/ubiCarpeta/iniciarSesion.php
   ```

---

## Flujo Principal del Sistema

El sistema sigue el siguiente flujo de navegación e interacción:

```
1. REGISTRO
   	El usuario crea una cuenta en Registrarse.php
	(nombre, usuario, correo y contraseña encriptada con password_hash)

2. INICIO DE SESIÓN
   	El usuario ingresa su correo y contraseña en iniciarSesion.php
    El sistema verifica con password_verify() contra la BD
    Credenciales correctas → Se inicia sesión ($_SESSION) → Cursos.php
     Credenciales incorrectas → Se muestra mensaje de error

3. PANTALLA DE MÓDULOS (Cursos.php)
    Se cargan todos los módulos desde la BD
    Por cada módulo se calcula el progreso real del usuario
    (ejercicios respondidos / total de ejercicios × 100)
    El usuario selecciona un módulo → Temas.php

4. VISTA DE TEMAS (Temas.php)
    Se muestra el módulo seleccionado con su progreso actual
    El usuario hace clic en "Comenzar" o "Continuar" → actividades.php

5. ACTIVIDADES (actividades.php)
    Se carga el contenido teórico del módulo desde la BD
    Se muestran los ejercicios de práctica (preguntas abiertas)
    El usuario escribe y guarda su respuesta
    (se almacena en respuesta_abierta con ON DUPLICATE KEY UPDATE)
    Al responder todos los ejercicios → "✔ Lección completada"
    El progreso se actualiza automáticamente en Cursos.php

6. CIERRE DE SESIÓN
   cerrarSesion.php destruye la sesión y redirige al login
```

---

## Estado Actual de Desarrollo

| Funcionalidad                              | Estado         |
|--------------------------------------------|----------------|
| Estructura de navegación completa          | ✅ Completado  |
| Registro de usuarios                       | ✅ Completado  |
| Inicio de sesión con manejo de sesiones    | ✅ Completado  |
| Conexión a base de datos (PDO)             | ✅ Completado  |
| Visualización dinámica de módulos          | ✅ Completado  |
| Carga de contenido y ejercicios desde BD   | ✅ Completado  |
| Guardado y actualización de respuestas     | ✅ Completado  |
| Cálculo automático de progreso por módulo  | ✅ Completado  |
| Pantalla de resultados                     | 🔄 En desarrollo |
| Panel de administración                    | 🔄 En desarrollo |