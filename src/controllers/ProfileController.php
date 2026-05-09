<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers.php';

class ProfileController {

    /**
     * Lista blanca de avatares permitidos.
     * El avatar enviado por el formulario se valida contra esta constante
     * para evitar que se guarde cualquier valor arbitrario.
     */
    public const AVATARS = [
        '👤','😀','😎','🤓','🧑‍💻','👩‍💻','🧠','🚀','⭐','🔥',
        '🎯','💡','🎮','🐱','🐶','🦊','🐼','🦁','🐸','🤖',
    ];

    /**
     * Muestra la página de perfil del usuario autenticado.
     * Pasa a la vista los mensajes de éxito/error de la operación anterior, si existen.
     */
    public static function show(): void {
        require_auth();
        $user      = User::findById((int)$_SESSION['user_id']);
        $error     = $_SESSION['profile_error']   ?? '';
        $success   = $_SESSION['profile_success'] ?? '';
        unset($_SESSION['profile_error'], $_SESSION['profile_success']);
        $avatars   = self::AVATARS;
        $csrfToken = csrf_token();
        require __DIR__ . '/../views/profile.php';
    }

    /**
     * Procesa las actualizaciones del perfil (POST). Maneja dos acciones distintas
     * según el campo `action` del formulario:
     *
     * action = 'profile': actualiza nombre de usuario y avatar.
     *   - El avatar se valida contra la lista blanca AVATARS; si no está en la lista
     *     se asigna el avatar por defecto '👤'.
     *   - El username se valida con las mismas reglas que en el registro.
     *   - Se verifica que el nuevo username no esté en uso por otro usuario distinto.
     *
     * action = 'password': actualiza la contraseña.
     *   - Requiere la contraseña actual para confirmar la identidad del usuario.
     *   - La nueva contraseña debe tener mínimo 6 caracteres y coincidir con la confirmación.
     */
    public static function handleUpdate(): void {
        require_auth();
        csrf_verify();
        $userId = (int)$_SESSION['user_id'];
        $action = $_POST['action'] ?? '';

        if ($action === 'profile') {
            $username = trim($_POST['username'] ?? '');
            $avatar   = $_POST['avatar'] ?? '👤';

            if (!in_array($avatar, self::AVATARS, true)) {
                $avatar = '👤';
            }

            if (strlen($username) < 3 || !preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $_SESSION['profile_error'] = 'El usuario debe tener al menos 3 caracteres y solo letras, números o guiones bajos.';
                redirect(base_url('index.php?page=profile'));
            }

            $existing = User::findByUsername($username);
            if ($existing && (int)$existing['id_usuario'] !== $userId) {
                $_SESSION['profile_error'] = 'Ese nombre de usuario ya está en uso.';
                redirect(base_url('index.php?page=profile'));
            }

            User::updateProfile($userId, $username, $avatar);
            $_SESSION['username'] = $username;
            $_SESSION['avatar']   = $avatar;
            $_SESSION['profile_success'] = 'Perfil actualizado correctamente.';

        } elseif ($action === 'password') {
            $current = $_POST['current_password'] ?? '';
            $new     = $_POST['new_password']     ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            $user = User::findById($userId);

            if (!User::verifyPassword($current, $user['contraseña'])) {
                $_SESSION['profile_error'] = 'La contraseña actual no es correcta.';
                redirect(base_url('index.php?page=profile'));
            }
            if (strlen($new) < 6) {
                $_SESSION['profile_error'] = 'La nueva contraseña debe tener al menos 6 caracteres.';
                redirect(base_url('index.php?page=profile'));
            }
            if ($new !== $confirm) {
                $_SESSION['profile_error'] = 'Las contraseñas nuevas no coinciden.';
                redirect(base_url('index.php?page=profile'));
            }

            User::updatePassword($userId, $new);
            $_SESSION['profile_success'] = 'Contraseña actualizada correctamente.';
        }

        redirect(base_url('index.php?page=profile'));
    }
}
