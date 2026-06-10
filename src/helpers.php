<?php

/**
 * Redirige al navegador a la URL indicada y termina la ejecución.
 *
 * @param string $path URL absoluta o relativa de destino.
 */
function redirect(string $path): void {
    header('Location: ' . $path);
    exit;
}

/**
 * Construye la URL base del proyecto detectando automáticamente
 * la subcarpeta dentro de htdocs.
 *
 * Todas las rutas de la aplicación pasan por index.php usando el
 * parámetro ?page=. Ejemplo: base_url('index.php?page=dashboard').
 * Los assets estáticos (css, js) se referencian directamente:
 * base_url('css/styles.css').
 *
 * @param string $path Ruta relativa a partir de /public/.
 * @return string URL completa desde la raíz del servidor.
 */
function base_url(string $path = ''): string {
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
    // Si el script está dentro de una subcarpeta/.../public/, usamos esa base.
    // Si está directo en la raíz (ej: /index.php), la base es solo '/'.
    if (str_contains($script, '/public/')) {
        $parts = explode('/public/', $script);
        $base  = rtrim($parts[0], '/') . '/public';
    } else {
        // El DocumentRoot ya apunta a /public, así que la base es la raíz
        $base = '';
    }
    return $base . '/' . ltrim($path, '/');
}

/**
 * Verifica que el usuario tenga una sesión activa y que no haya expirado
 * por inactividad. La sesión expira tras 7 minutos sin actividad.
 *
 * Si no hay sesión o expiró, redirige al login con un mensaje informativo.
 * Actualiza el timestamp de última actividad en cada request autenticado.
 */
function require_auth(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['user_id'])) {
        redirect(base_url('index.php?page=login'));
    }

    // Verificar inactividad — 7 minutos = 420 segundos
    $timeout = 420;
    $lastActivity = $_SESSION['last_activity'] ?? time();

    if ((time() - $lastActivity) > $timeout) {
        session_destroy();
        session_start();
        $_SESSION['auth_error'] = 'Tu sesión expiró por inactividad. Por favor inicia sesión de nuevo.';
        redirect(base_url('index.php?page=login'));
    }

    // Actualizar timestamp de última actividad
    $_SESSION['last_activity'] = time();

    // Sincronizar is_superadmin desde BD si aún no está en sesión
    if (!isset($_SESSION['is_superadmin'])) {
        $stmt = getDB()->prepare('SELECT is_admin, is_superadmin FROM usuario WHERE id_usuario = ? LIMIT 1');
        $stmt->execute([$_SESSION['user_id']]);
        $row = $stmt->fetch();
        if ($row) {
            $_SESSION['is_admin']      = !empty($row['is_admin']);
            $_SESSION['is_superadmin'] = !empty($row['is_superadmin']);
        }
    }
}

/**
 * Escapa una cadena para salida HTML segura.
 * Previene XSS convirtiendo caracteres especiales a entidades HTML.
 *
 * @param string $str Cadena a escapar.
 * @return string Cadena segura para insertar en HTML.
 */
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Genera y almacena un token CSRF en sesión si no existe ya uno.
 * Devuelve el token para incluirlo en formularios como campo hidden.
 *
 * @return string Token CSRF de 32 bytes en hexadecimal.
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica que el token CSRF del formulario coincida con el de la sesión.
 * Si no coincide (token expirado, recarga de página con POST, o ataque),
 * redirige al login con mensaje amigable en lugar de mostrar error técnico.
 * Regenera el token después de validarlo para evitar reutilización.
 */
function csrf_verify(): void {
    $submitted = $_POST['csrf_token'] ?? '';
    $stored    = $_SESSION['csrf_token'] ?? '';

    if ($stored === '' || !hash_equals($stored, $submitted)) {
        // Token inválido — puede ser recarga de página o token expirado.
        // Redirigir limpiamente en lugar de mostrar error técnico.
        $page = $_GET['page'] ?? 'login';
        $allowed = ['login', 'register', 'profile'];
        $target  = in_array($page, $allowed, true) ? $page : 'login';
        redirect(base_url('index.php?page=' . $target));
    }

    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Verifica si el usuario está bloqueado por demasiados intentos fallidos de login.
 * Bloquea durante 15 minutos tras 5 intentos fallidos consecutivos.
 * El contador se reinicia al iniciar sesión correctamente.
 *
 * @return bool true si está bloqueado y no puede intentar login.
 */
function is_login_blocked(): bool {
    $attempts  = $_SESSION['login_attempts']   ?? 0;
    $blockedAt = $_SESSION['login_blocked_at'] ?? 0;

    if ($attempts >= 5 && $blockedAt > 0) {
        $secondsLeft = ($blockedAt + 900) - time();
        if ($secondsLeft > 0) {
            return true;
        }
        $_SESSION['login_attempts']   = 0;
        $_SESSION['login_blocked_at'] = 0;
    }
    return false;
}

/**
 * Registra un intento fallido de login.
 * Al llegar a 5 intentos, guarda el timestamp de inicio del bloqueo.
 */
function register_failed_login(): void {
    $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
    if ($_SESSION['login_attempts'] >= 5) {
        $_SESSION['login_blocked_at'] = time();
    }
}

/**
 * Devuelve el tiempo restante del bloqueo de login como texto legible.
 *
 * @return string Tiempo restante, ej: "14 min 32 seg".
 */
function login_block_remaining(): string {
    $blockedAt   = $_SESSION['login_blocked_at'] ?? 0;
    $secondsLeft = max(0, ($blockedAt + 900) - time());
    $mins        = (int)floor($secondsLeft / 60);
    $secs        = $secondsLeft % 60;
    return "{$mins} min {$secs} seg";
}

/**
 * Reinicia el contador de intentos fallidos de login.
 * Se llama al iniciar sesión correctamente.
 */
function reset_login_attempts(): void {
    $_SESSION['login_attempts']   = 0;
    $_SESSION['login_blocked_at'] = 0;
}

/**
 * Renderiza una página de error HTTP con mensaje y enlace de regreso.
 * Termina la ejecución después de mostrar la página.
 *
 * @param int    $code      Código HTTP del error (ej. 404, 403).
 * @param string $message   Mensaje descriptivo del error.
 * @param string $backUrl   URL del enlace "volver". Por defecto: dashboard.
 * @param string $backLabel Texto del enlace de regreso.
 */
function render_error(int $code, string $message, string $backUrl = '', string $backLabel = 'Volver al inicio'): never {
    http_response_code($code);
    if ($backUrl === '') {
        $backUrl = base_url('index.php?page=dashboard');
    }
    $showHeader = !empty($_SESSION['user_id']);
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Error <?= $code ?> — Loopbook</title>
      <link rel="stylesheet" href="<?= base_url('css/styles.css') ?>">
    </head>
    <body>
    <?php if ($showHeader): require __DIR__ . '/views/partials/header.php'; endif; ?>
    <main class="main-content">
      <div class="page-container page-container--narrow" style="text-align:center; padding-top:3rem;">
        <p style="font-size:4rem; margin-bottom:.5rem;">😕</p>
        <h1 class="page-title gradient-text" style="font-size:1.6rem;"><?= e($message) ?></h1>
        <p class="page-subtitle" style="margin-top:.5rem;">Código de error: <?= $code ?></p>
        <a href="<?= e($backUrl) ?>" class="btn btn-primary" style="margin-top:1.5rem;"><?= e($backLabel) ?></a>
      </div>
    </main>
    </body>
    </html>
    <?php
    exit;
}
