<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers.php';

class AuthController {

    /**
     * Muestra el formulario de inicio de sesión.
     * Recupera error, usuario previo y estado de bloqueo desde sesión.
     */
    public static function showLogin(): void {
        $error        = $_SESSION['auth_error']    ?? '';
        $lastUsername = $_SESSION['auth_username'] ?? '';
        $blocked      = is_login_blocked();
        $blockTime    = $blocked ? login_block_remaining() : '';
        $blockSeconds = $blocked ? max(0, ($_SESSION['login_blocked_at'] ?? 0) + 900 - time()) : 0;
        unset($_SESSION['auth_error'], $_SESSION['auth_username']);
        $csrfToken = csrf_token();
        require __DIR__ . '/../views/login.php';
    }

    /**
     * Procesa el formulario de inicio de sesión (POST).
     *
     * Usa Post/Redirect/Get: guarda el error en sesión y redirige con GET.
     * Esto evita el "¿Volver a enviar el formulario?" al dar atrás.
     * El campo usuario se conserva entre intentos.
     */
    public static function handleLogin(): void {
        csrf_verify();

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password']      ?? '';

        $fail = function(string $msg) use ($username): never {
            $_SESSION['auth_error']    = $msg;
            $_SESSION['auth_username'] = $username;
            redirect(base_url('index.php?page=login'));
        };

        if (is_login_blocked()) {
            $fail('Demasiados intentos fallidos. Espera ' . login_block_remaining() . ' antes de intentar de nuevo.');
        }

        if ($username === '' || $password === '') {
            $fail('Por favor completa todos los campos');
        }

        $user = User::findByUsername($username);

        if (!$user || !User::verifyPassword($password, $user['contraseña'])) {
            register_failed_login();
            $attempts = $_SESSION['login_attempts'] ?? 0;
            if ($attempts >= 5) {
                $fail('Demasiados intentos fallidos. Cuenta bloqueada por 15 minutos.');
            }
            $remaining = 5 - $attempts;
            $fail('Credenciales inválidas. Te quedan ' . $remaining . ' intento(s) antes del bloqueo.');
        }

        reset_login_attempts();
        $_SESSION['user_id']  = $user['id_usuario'];
        $_SESSION['username'] = $user['usuario'];
        $_SESSION['nombre']   = $user['nombre'];
        $_SESSION['avatar']   = $user['avatar'] ?? '👤';
        $_SESSION['is_admin'] = !empty($user['is_admin']) ? true : false;
        redirect(base_url('index.php?page=dashboard'));
    }

    /**
     * Muestra el formulario de registro.
     * Recupera error, usuario y email previos desde sesión.
     */
    public static function showRegister(): void {
        $error        = $_SESSION['auth_error']    ?? '';
        $lastUsername = $_SESSION['auth_reg_user'] ?? '';
        $lastEmail    = $_SESSION['auth_reg_email'] ?? '';
        unset($_SESSION['auth_error'], $_SESSION['auth_reg_user'], $_SESSION['auth_reg_email']);
        $csrfToken = csrf_token();
        require __DIR__ . '/../views/register.php';
    }

    /**
     * Procesa el formulario de registro (POST).
     *
     * Usa Post/Redirect/Get: guarda error y valores en sesión, redirige con GET.
     * Usuario y email se conservan entre intentos. Contraseñas no se repopulan.
     *
     * Reglas de validación:
     * - Todos los campos obligatorios.
     * - Usuario: mínimo 3 caracteres, solo [a-zA-Z0-9_].
     * - Correo: formato válido según FILTER_VALIDATE_EMAIL.
     * - Contraseña: mínimo 6 caracteres y debe coincidir con confirmación.
     * - Usuario y correo únicos en la base de datos.
     */
    public static function handleRegister(): void {
        csrf_verify();

        $username        = trim($_POST['username']        ?? '');
        $email           = trim($_POST['email']           ?? '');
        $password        = $_POST['password']             ?? '';
        $confirmPassword = $_POST['confirm_password']     ?? '';

        $fail = function(string $msg) use ($username, $email): never {
            $_SESSION['auth_error']     = $msg;
            $_SESSION['auth_reg_user']  = $username;
            $_SESSION['auth_reg_email'] = $email;
            redirect(base_url('index.php?page=register'));
        };

        if ($username === '' || $email === '' || $password === '' || $confirmPassword === '') {
            $fail('Por favor completa todos los campos');
        }
        if (strlen($username) < 3) {
            $fail('El nombre de usuario debe tener al menos 3 caracteres');
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $fail('El usuario solo puede contener letras, números y guiones bajos');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $fail('El correo electrónico no es válido');
        }
        if (strlen($password) < 6) {
            $fail('La contraseña debe tener al menos 6 caracteres');
        }
        if ($password !== $confirmPassword) {
            $fail('Las contraseñas no coinciden');
        }
        if (User::findByUsername($username)) {
            $fail('El nombre de usuario ya está en uso');
        }
        if (User::findByEmail($email)) {
            $fail('El correo electrónico ya está registrado');
        }

        try {
            $userId = User::create($username, $email, $password, $username);
        } catch (\Exception $e) {
            $fail('No se pudo crear la cuenta. Intenta de nuevo.');
        }

        $_SESSION['user_id']  = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['nombre']   = $username;
        $_SESSION['avatar']   = '👤';
        redirect(base_url('index.php?page=dashboard'));
    }

    /**
     * Cierra la sesión del usuario destruyendo todos los datos de sesión
     * y redirige al login.
     */
    public static function logout(): void {
        session_destroy();
        redirect(base_url('index.php?page=login'));
    }
}
