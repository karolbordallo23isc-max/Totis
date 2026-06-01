<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/PasswordReset.php';
require_once __DIR__ . '/../helpers.php';

/**
 * PasswordResetController — Recuperación de contraseña por correo.
 *
 * Rutas:
 *   GET  ?page=forgot          → formulario "ingresa tu correo"
 *   POST ?page=forgot          → genera token y envía email
 *   GET  ?page=reset&token=... → formulario "nueva contraseña"
 *   POST ?page=reset           → guarda nueva contraseña
 */
class PasswordResetController {

    // ── PASO 1: Formulario de correo ───────────────────────────

    public static function showForgot(): void {
        $error   = $_SESSION['reset_error']   ?? '';
        $success = $_SESSION['reset_success'] ?? '';
        unset($_SESSION['reset_error'], $_SESSION['reset_success']);
        $csrfToken = csrf_token();
        require __DIR__ . '/../views/forgot_password.php';
    }

    public static function handleForgot(): void {
        csrf_verify();

        $email = trim($_POST['email'] ?? '');

        $fail = function(string $msg): never {
            $_SESSION['reset_error'] = $msg;
            redirect(base_url('index.php?page=forgot'));
        };

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $fail('Ingresa un correo electrónico válido.');
        }

        $user = User::findByEmail($email);

        // Siempre mostrar el mismo mensaje para no revelar si el correo existe
        $_SESSION['reset_success'] = 'Si ese correo está registrado, recibirás un enlace en breve.';

        if ($user) {
            $token = PasswordReset::create((int)$user['id_usuario']);
            self::sendResetEmail($email, $user['nombre'], $token);
        }

        redirect(base_url('index.php?page=forgot'));
    }

    // ── PASO 2: Formulario de nueva contraseña ─────────────────

    public static function showReset(): void {
        $token = trim($_GET['token'] ?? '');

        if ($token === '' || !PasswordReset::findValid($token)) {
            $_SESSION['reset_error'] = 'El enlace no es válido o ya expiró. Solicita uno nuevo.';
            redirect(base_url('index.php?page=forgot'));
        }

        $error = $_SESSION['reset_error'] ?? '';
        unset($_SESSION['reset_error']);
        $csrfToken = csrf_token();
        require __DIR__ . '/../views/reset_password.php';
    }

    public static function handleReset(): void {
        csrf_verify();

        $token           = trim($_POST['token']            ?? '');
        $password        = $_POST['password']              ?? '';
        $confirmPassword = $_POST['confirm_password']      ?? '';

        $fail = function(string $msg) use ($token): never {
            $_SESSION['reset_error'] = $msg;
            redirect(base_url('index.php?page=reset&token=' . urlencode($token)));
        };

        $reset = $token !== '' ? PasswordReset::findValid($token) : false;

        if (!$reset) {
            $_SESSION['reset_error'] = 'El enlace no es válido o ya expiró. Solicita uno nuevo.';
            redirect(base_url('index.php?page=forgot'));
        }

        if (strlen($password) < 6) {
            $fail('La contraseña debe tener al menos 6 caracteres.');
        }

        if ($password !== $confirmPassword) {
            $fail('Las contraseñas no coinciden.');
        }

        User::updatePassword((int)$reset['id_usuario'], $password);
        PasswordReset::markUsed($token);

        $_SESSION['auth_error'] = ''; // limpiar posibles errores previos
        $_SESSION['reset_success_login'] = '✅ Contraseña actualizada. Ya puedes iniciar sesión.';
        redirect(base_url('index.php?page=login'));
    }

    // ── Envío de email ─────────────────────────────────────────

    private static function sendResetEmail(string $email, string $nombre, string $token): void {
        $link    = base_url('index.php?page=reset&token=' . urlencode($token));
        $subject = 'Recuperar contraseña — Loopbook';

        $body = "Hola, {$nombre}.\n\n"
              . "Recibimos una solicitud para restablecer la contraseña de tu cuenta en Loopbook.\n\n"
              . "Haz clic en el siguiente enlace para crear una nueva contraseña:\n"
              . "{$link}\n\n"
              . "Este enlace expira en 1 hora.\n\n"
              . "Si no solicitaste esto, ignora este mensaje. Tu contraseña no cambiará.\n\n"
              . "— El equipo de Loopbook";

        $headers = implode("\r\n", [
            'From: Loopbook <no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'loopbook.com') . '>',
            'Reply-To: no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'loopbook.com'),
            'X-Mailer: PHP/' . PHP_VERSION,
            'Content-Type: text/plain; charset=UTF-8',
        ]);

        mail($email, $subject, $body, $headers);
    }
}
