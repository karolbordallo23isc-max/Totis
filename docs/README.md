# LoopBook

LoopBook es una plataforma web de aprendizaje diseñada para estudiantes de nuevo ingreso que tienen poco o ningún conocimiento previo en programación. El sistema ofrece módulos teóricos con ejercicios interactivos de opción múltiple, seguimiento automático del progreso por usuario y retroalimentación inmediata en cada respuesta.

**Problema que resuelve:** Los estudiantes de nuevo ingreso llegan sin bases en programación, lo que genera confusión, desmotivación y abandono temprano. LoopBook les da un punto de entrada estructurado y accesible.

**Público objetivo:** Estudiantes de nuevo ingreso a carreras de tecnología y personas de la comunidad interesadas en aprender programación desde cero.

---

## Tecnologías utilizadas

| Capa | Tecnología |
|---|---|
| Frontend | HTML5, CSS3 modular (17 archivos por componente, sin frameworks) |
| Backend | PHP 8.x (sin framework, arquitectura MVC manual) |
| Base de datos | MySQL — normalizada en 3FN |
| Acceso a datos | PDO con prepared statements (prevención de SQL injection) |
| Autenticación | Sesiones PHP + bcrypt (`password_hash` / `password_verify`) |
| Interactividad | JavaScript vanilla (Web Audio API, fetch para AJAX) |
| Seguridad | Tokens CSRF + rate limiting en login (bloqueo 15 min tras 5 intentos) |
| Tipografía | Plus Jakarta Sans (Google Fonts) |

> No se utilizó ningún framework de PHP (Laravel, Symfony, Slim), ni librería de JavaScript (jQuery, React, Vue), ni framework de CSS (Bootstrap, Tailwind).

---

## Qué hace el JavaScript (`public/js/app.js`)

El archivo `app.js` maneja toda la interactividad del lado del cliente:

- **Sonidos generados en tiempo real** — usa Web Audio API con osciladores para producir sonidos sin archivos externos: acorde mayor al responder correcto, tonos descendentes al fallar, fanfarria al completar módulo, click suave al seleccionar opción.
- **Modo oscuro** — persiste la preferencia del usuario en `localStorage`. Inyecta el botón 🌙/☀️ en el header y aplica/quita la clase `dark` en el `<html>`.
- **Tooltips de módulos** — al pasar el cursor sobre una tarjeta del dashboard, hace `fetch` a `api/module_preview.php` y muestra las lecciones del módulo con su estado de completado.
- **Verificación de ejercicios** — `checkAnswer()` hace `fetch POST` a `api/check_answer.php`, recibe el resultado y colorea las opciones (verde correcto, rojo incorrecto), muestra feedback y explicación.
- **Pantalla de celebración** — al completar el último ejercicio de la última lección de un módulo, muestra un overlay con trofeo y lanza confetti en dos oleadas.
- **Confetti** — genera divs animados con CSS `@keyframes` que caen desde arriba. Sin librerías externas.

---

## Estructura del proyecto

```
loopbook/
├── .env.example                # Variables de entorno de referencia
├── .gitignore                  # Excluye .env, logs, vendor, etc.
│
├── config/
│   ├── database.php            # Conexión PDO a MySQL (singleton estático)
│   └── loopbook.sql            # Schema completo + datos de prueba
│
├── public/                     # Raíz pública — único directorio expuesto al navegador
│   ├── .htaccess               # Redirige URLs inválidas al Front Controller; activa mod_rewrite
│   ├── index.php               # Front Controller: router central de toda la aplicación
│   ├── api/
│   │   ├── check_answer.php    # POST — verifica respuesta y guarda progreso
│   │   ├── module_preview.php  # GET  — devuelve lecciones de un módulo (tooltip)
│   │   └── reset_progress.php  # POST — reinicia progreso de lección o módulo
│   ├── css/
│   │   ├── styles.css          # Hoja principal — importa variables y módulos
│   │   └── modules/            # CSS dividido por componente (16 archivos)
│   │       ├── variables.css   # Paleta de colores, fuentes y tokens de diseño
│   │       ├── base.css        # Estilos globales del body y reset
│   │       ├── buttons.css     # Sistema de botones y variantes
│   │       ├── cards.css       # Tarjetas de módulos y lecciones
│   │       ├── forms.css       # Inputs y formularios
│   │       ├── header.css      # Barra de navegación
│   │       ├── layout.css      # Estructura de páginas
│   │       ├── auth.css        # Login y registro
│   │       ├── exercises.css   # Ejercicios y opciones
│   │       ├── progress.css    # Barras de progreso
│   │       ├── profile.css     # Página de perfil
│   │       ├── celebration.css # Pantalla de celebración al completar módulo
│   │       ├── dark-mode.css   # Modo oscuro
│   │       ├── tooltips.css    # Tooltips del dashboard
│   │       ├── mobile-menu.css # Menú móvil
│   │       ├── admin.css       # Panel de administración
│   │       └── responsive.css  # Media queries
│   ├── img/
│   │   └── loopbook_logo.png   # Logo del proyecto (PNG transparente)
│   └── js/
│       └── app.js              # Sonidos Web Audio API, modo oscuro, tooltips, confetti, AJAX
│
├── src/                        # Todo el código PHP de la aplicación
│   ├── helpers.php             # Funciones globales: redirect, base_url, require_auth,
│   │                           # e(), csrf_token, csrf_verify, is_login_blocked, etc.
│   ├── controllers/            # Lógica de negocio y orquestación
│   │   ├── AdminController.php
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ModuleController.php
│   │   ├── LessonController.php
│   │   └── ProfileController.php
│   ├── models/                 # Acceso a base de datos
│   │   ├── Admin.php
│   │   ├── User.php
│   │   ├── Module.php
│   │   ├── Lesson.php
│   │   └── Progress.php
│   └── views/                  # Plantillas HTML — fuera de public/, no accesibles por URL
│       ├── login.php
│       ├── register.php
│       ├── dashboard.php
│       ├── module.php
│       ├── lesson.php
│       ├── profile.php
│       └── partials/
│           ├── header.php
│           └── next_module_card.php
│
└── docs/
    ├── README.md                       # Documentación principal del proyecto (Dev Líder)
    ├── estructura_repositorio.md       # Estructura y decisiones de arquitectura (Dev Líder)
    ├── mejoras_interfaz.md             # Documentación de mejoras visuales (Diseñador)
    ├── Actualización de interfaz.pdf   # Reporte visual del diseño (Diseñador)
    ├── evidencia_ejecucion.pdf         # Evidencia de ejecución del proyecto (Dev Líder)
    ├── criterios_aceptacion.md         # Criterios de aceptación (Analista)
    ├── test_report.md                  # Reporte de pruebas y casos de QA (QA/Tester)
    └── bitacora_sprint4.pdf            # Bitácora del sprint (Coordinador)
```

---

## Instalación y ejecución local

**Requisitos previos:** XAMPP (o equivalente con Apache + MySQL + PHP 8.x)

### 1. Clonar el repositorio

Descarga o clona este repositorio dentro de la carpeta `htdocs` de XAMPP:

```bash
git clone https://github.com/karolbordallo23isc-max/Totis.git C:/xampp/htdocs/loopbook
```

### 2. Crear la base de datos

1. Inicia XAMPP y activa los servicios **Apache** y **MySQL**
2. Abre **phpMyAdmin** dando clic en el botón **Admin** de MySQL en el panel de XAMPP
3. Ve a la pestaña **Importar** → selecciona el archivo `config/loopbook.sql` → clic en **Continuar**

### 3. Configurar las variables de entorno

En la carpeta del proyecto verás un archivo llamado `.env.example`. Tienes que crear una copia de ese archivo y llamarla `.env`.

**En Windows (Explorador de archivos):**
1. Busca el archivo `.env.example` en la raíz del proyecto
2. Cópialo y pégalo en la misma carpeta
3. Renombra la copia a `.env`

**O desde la terminal en la carpeta del proyecto:**
```bash
copy .env.example .env
```

Abre el archivo `.env` con cualquier editor de texto y verifica que las credenciales coincidan con tu XAMPP:

```
DB_HOST=localhost
DB_PORT=3306
DB_NAME=loopbook
DB_USER=root
DB_PASS=
```

> En XAMPP el campo `DB_PASS` normalmente está vacío. Si tu MySQL tiene contraseña, escríbela ahí.

### 4. Acceder al sistema

Abre tu navegador e ingresa a:

```
http://localhost/loopbook/public
```

El router detecta automáticamente si hay sesión activa y redirige al dashboard o al login.

---

## Usuarios de prueba

El archivo `loopbook.sql` incluye dos usuarios listos para usar:

| Usuario | Contraseña | Correo |
|---|---|---|
| `prueba` | `prueba123` | prueba@gmail.com |
| `test` | `test123` | t@gmail.com |

---

## Flujo principal del sistema

Todas las rutas pasan por `public/index.php` usando el parámetro `?page=`.

```
1. REGISTRO (?page=register)
   └── El usuario ingresa nombre, usuario, correo y contraseña.
       Validaciones: longitud mínima, formato de correo, usuario único,
       contraseñas coincidentes. Contraseña almacenada con bcrypt.
       Si hay error de validación, la vista se renderiza directamente
       sin redirigir — usuario y email se conservan, contraseñas se limpian.
       Token CSRF verificado antes de procesar cualquier dato.

2. INICIO DE SESIÓN (?page=login)
   └── Token CSRF verificado. Verifica si la IP está bloqueada por intentos
       fallidos antes de consultar la BD.
       Correcto → sesión iniciada ($_SESSION) → ?page=dashboard
       Incorrecto → mensaje de error con intentos restantes. Al llegar a 5
       intentos fallidos, bloqueo de 15 minutos con contador visible.
       El mensaje de error es genérico para no revelar si el usuario existe.
       Si hay error, el campo usuario se conserva sin redirigir.

3. DASHBOARD (?page=dashboard)
   └── Muestra todos los módulos del curso con su barra de progreso.
       El porcentaje se calcula en tiempo real:
       (ejercicios completados / total de ejercicios del módulo) × 100
       Al pasar el cursor sobre una tarjeta, un tooltip AJAX muestra
       las lecciones del módulo y su estado de completado.

4. MÓDULO (?page=module&id=X)
   └── Lista las lecciones del módulo seleccionado.
       Cada lección muestra si está completada (todos sus ejercicios
       respondidos correctamente).

5. LECCIÓN (?page=lesson&module_id=X&lesson_id=Y)
   └── Muestra el contenido teórico de la lección.
       Presenta los ejercicios de opción múltiple con opciones mezcladas.
       Al responder, se hace una llamada AJAX a api/check_answer.php:
         - Correcto → sonido + confetti + progreso guardado en BD
         - Incorrecto → retroalimentación inmediata, puede reintentar
       Al completar todos los ejercicios de la última lección del módulo
       → pantalla de celebración con fanfarria.

6. PERFIL (?page=profile)
   └── El usuario puede cambiar su nombre de usuario, avatar (emoji)
       y contraseña. Requiere confirmar la contraseña actual para cambiarla.

7. CIERRE DE SESIÓN (?page=logout)
   └── Destruye la sesión y redirige al login.

8. PANEL DE ADMINISTRACIÓN (?page=admin)
   └── Accesible solo para usuarios con is_admin = 1.
       Dashboard con estadísticas globales (módulos, lecciones, ejercicios,
       usuarios, progreso promedio) y acceso rápido a cada sección.
       Gestión completa de módulos: crear, editar, eliminar.
       Gestión de lecciones por módulo: crear, editar, eliminar.
       Gestión de ejercicios por lección: crear, editar, eliminar,
       con sus opciones de respuesta (texto, correcta, retroalimentación).
       Todas las operaciones de escritura validan token CSRF.
```

---

## Seguridad implementada

| Mecanismo | Archivo | Descripción |
|---|---|---|
| **Tokens CSRF** | `src/helpers.php` — `csrf_token()` / `csrf_verify()` | Cada formulario incluye un token de 64 caracteres aleatorios. Se verifica con `hash_equals()` antes de procesar cualquier POST. Previene que sitios externos envíen formularios en nombre del usuario. |
| **Rate limiting** | `src/helpers.php` — `is_login_blocked()` / `register_failed_login()` | Tras 5 intentos fallidos de login, bloqueo de 15 minutos almacenado en sesión. El formulario se deshabilita y muestra el tiempo restante. |
| **Bcrypt** | `src/models/User.php` — `create()` / `updatePassword()` | Las contraseñas se hashean con `PASSWORD_BCRYPT` antes de insertarse. Nunca se almacenan en texto plano. |
| **SQL Injection** | Todos los modelos | PDO con prepared statements y `ATTR_EMULATE_PREPARES = false`. Ninguna query concatena variables directamente. |
| **XSS** | `src/helpers.php` — `e()` | Toda salida de datos en vistas pasa por `htmlspecialchars()` con `ENT_QUOTES`. |
| **Protección de rutas** | `src/helpers.php` — `require_auth()` | Todas las páginas protegidas verifican sesión activa. Sin sesión → redirige al login. |

---

## Limitaciones actuales del sistema

- **Progreso por ejercicio, no por lección:** El sistema rastrea ejercicios individuales. Una lección se considera completada solo cuando todos sus ejercicios están respondidos correctamente.
- **Sin soporte para contenido multimedia en vistas:** El schema de BD contempla `tipo` (texto/video/imagen) y el admin permite configurarlo, pero la vista de lección solo renderiza texto actualmente.
- **Entorno local únicamente:** No está configurado para despliegue en producción (credenciales en `.env` local, sin configuración de servidor remoto).
