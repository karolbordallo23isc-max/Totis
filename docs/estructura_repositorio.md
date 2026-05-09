# Estructura del Repositorio — LoopBook

Este documento describe la organización del repositorio, el propósito de cada carpeta y los pasos exactos para ejecutar el proyecto desde cero en cualquier máquina.

---

## 1. Diagrama de Árbol

```
loopbook/
├── .env.example
├── .gitignore
│
├── config/
│   ├── database.php
│   └── loopbook.sql
│
├── docs/
│   ├── README.md
│   └── estructura_repositorio.md
│
├── public/
│   ├── .htaccess
│   ├── index.php
│   ├── api/
│   │   ├── check_answer.php
│   │   ├── module_preview.php
│   │   └── reset_progress.php
│   ├── css/
│   │   ├── styles.css
│   │   └── modules/
│   │       ├── variables.css
│   │       ├── base.css
│   │       ├── buttons.css
│   │       ├── cards.css
│   │       ├── forms.css
│   │       ├── header.css
│   │       ├── layout.css
│   │       ├── auth.css
│   │       ├── exercises.css
│   │       ├── progress.css
│   │       ├── profile.css
│   │       ├── celebration.css
│   │       ├── dark-mode.css
│   │       ├── tooltips.css
│   │       ├── mobile-menu.css
│   │       └── responsive.css
│   ├── img/
│   │   └── loopbook_logo.png
│   └── js/
│       └── app.js
│
└── src/
    ├── helpers.php
    ├── controllers/
    │   ├── AuthController.php
    │   ├── DashboardController.php
    │   ├── LessonController.php
    │   ├── ModuleController.php
    │   └── ProfileController.php
    ├── models/
    │   ├── Lesson.php
    │   ├── Module.php
    │   ├── Progress.php
    │   └── User.php
    └── views/
        ├── dashboard.php
        ├── lesson.php
        ├── login.php
        ├── module.php
        ├── profile.php
        ├── register.php
        └── partials/
            ├── header.php
            └── next_module_card.php
```

---

## 2. Explicación de Carpetas

| Carpeta / Archivo | Qué contiene |
|---|---|
| `config/` | Configuración de la base de datos y schema SQL con datos de prueba. |
| `config/database.php` | Función `getDB()` — conexión PDO singleton a MySQL. Único punto de acceso a la BD. |
| `config/loopbook.sql` | Schema completo de la base de datos con tablas, relaciones y datos de prueba listos para importar. |
| `docs/` | Documentación del proyecto. No contiene código ejecutable. |
| `public/` | Único directorio expuesto al navegador. Todo lo que el usuario puede acceder directamente está aquí. |
| `public/index.php` | Front Controller — punto de entrada único. Recibe todas las peticiones y las enruta al controlador correspondiente según el parámetro `?page=`. |
| `public/.htaccess` | Configuración de Apache: desactiva listado de directorios y redirige todo al Front Controller. |
| `public/api/` | Endpoints AJAX. Reciben peticiones fetch desde el navegador y responden JSON. No generan HTML. |
| `public/css/styles.css` | Hoja de estilos principal. Importa las variables globales y todos los módulos CSS. |
| `public/css/modules/` | CSS dividido por componente. Cada archivo es responsable de un área visual específica del sistema. |
| `public/img/` | Imágenes estáticas del proyecto (logo). |
| `public/js/app.js` | Toda la interactividad del cliente: sonidos Web Audio API, modo oscuro, tooltips AJAX, verificación de ejercicios y confetti. |
| `src/` | Todo el código PHP de la aplicación. No es accesible directamente por URL. |
| `src/helpers.php` | Funciones globales reutilizables: `redirect()`, `base_url()`, `require_auth()`, `e()`, `csrf_token()`, `csrf_verify()`, rate limiting de login. |
| `src/controllers/` | Lógica de negocio. Cada controlador maneja una sección del sistema: autenticación, dashboard, módulos, lecciones y perfil. |
| `src/models/` | Acceso a la base de datos. Cada modelo representa una entidad y contiene las queries PDO correspondientes. |
| `src/views/` | Plantillas HTML+PHP. Se renderizan desde los controladores. No son accesibles por URL directa. |
| `src/views/partials/` | Fragmentos reutilizables de HTML: header de navegación y tarjeta del siguiente módulo. |

---

## 3. Instrucciones de Ejecución

**Requisitos previos:** XAMPP instalado (Apache + MySQL + PHP 8.x)

### Paso 1 — Clonar el repositorio

```bash
git clone https://github.com/karolbordallo23isc-max/Totis.git C:/xampp/htdocs/loopbook
```

### Paso 2 — Crear la base de datos

1. Iniciar XAMPP y activar **Apache** y **MySQL**
2. Abrir **phpMyAdmin** en `http://localhost/phpmyadmin`
3. Crear una base de datos llamada `loopbook` o importar directamente sin crear la base de datos.
4. Seleccionar la base de datos → pestaña **Importar** → seleccionar `config/loopbook.sql` → clic en **Continuar**

### Paso 3 — Verificar credenciales

Abrir `config/database.php` y confirmar que coincidan con el entorno local:

```php
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');   // XAMPP usa 3306 por defecto
define('DB_NAME', 'loopbook');
define('DB_USER', 'root');
define('DB_PASS', '');       // En XAMPP el password está vacío por defecto
```

### Paso 4 — Abrir en el navegador

```
http://localhost/loopbook/public
```

El sistema detecta automáticamente si hay sesión activa y redirige al dashboard o al login.

### Usuarios de prueba

| Usuario | Contraseña |
|---|---|
| `prueba` | `prueba123` |
| `test` | `test123` |

---

## Gestión de dependencias

Este proyecto **no utiliza `composer.json`** porque no depende de ninguna librería externa de PHP. Todo el código es escrito a mano:

- Sin frameworks (no Laravel, no Symfony, no Slim)
- Sin librerías de terceros
- Sin gestor de paquetes

La conexión a la base de datos usa PDO nativo de PHP. La autenticación usa las funciones `password_hash()` y `password_verify()` incluidas en PHP 8.x. No hay nada que instalar — el proyecto se clona y ejecuta directamente.

De igual forma, **no existe `node_modules/`** porque no se usa Node.js ni ningún bundler. El JavaScript es vanilla puro sin dependencias.

---

## Decisiones de diseño

- **`public/` como única raíz expuesta:** Los controladores, modelos y vistas viven en `src/` fuera del alcance del navegador. Solo `public/` es accesible por URL, lo que evita exponer lógica interna.
- **Front Controller (`index.php`):** Todas las rutas pasan por un único punto de entrada usando `?page=`. Esto centraliza la autenticación, el enrutamiento y la gestión de sesiones.
- **CSS modular:** Los estilos están divididos en 16 archivos por componente dentro de `public/css/modules/`. Facilita encontrar y modificar estilos sin tocar un archivo monolítico.
- **Sin dependencias externas:** No se usa Composer, npm ni ningún gestor de paquetes. El proyecto se clona y ejecuta directamente sin pasos de instalación adicionales.
