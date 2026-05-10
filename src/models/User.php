<?php
require_once __DIR__ . '/../../config/database.php';

class User {

    /**
     * Busca un usuario por su nombre de usuario.
     *
     * @param string $username Nombre de usuario exacto (campo `usuario` en BD).
     * @return array|false Fila del usuario o false si no existe.
     */
    public static function findByUsername(string $username): array|false {
        $stmt = getDB()->prepare('SELECT * FROM usuario WHERE usuario = ? LIMIT 1');
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    /**
     * Busca un usuario por su correo electrónico.
     *
     * @param string $email Correo electrónico del usuario.
     * @return array|false Fila del usuario o false si no existe.
     */
    public static function findByEmail(string $email): array|false {
        $stmt = getDB()->prepare('SELECT * FROM usuario WHERE correo = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Busca un usuario por su ID primario.
     *
     * @param int $id ID del usuario (`id_usuario`).
     * @return array|false Fila del usuario o false si no existe.
     */
    public static function findById(int $id): array|false {
        $stmt = getDB()->prepare('SELECT * FROM usuario WHERE id_usuario = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Crea un nuevo usuario. La contraseña se hashea con bcrypt antes de insertarse.
     * Si $nombre está vacío, se usa $username como nombre visible.
     *
     * @param string $username Nombre de usuario único.
     * @param string $email    Correo electrónico único.
     * @param string $password Contraseña en texto plano (se hashea internamente).
     * @param string $nombre   Nombre visible. Si se omite, se usa $username.
     * @return int ID del usuario recién creado.
     */
    public static function create(string $username, string $email, string $password, string $nombre = ''): int {
        $db     = getDB();
        $hash   = password_hash($password, PASSWORD_BCRYPT);
        $nombre = $nombre !== '' ? $nombre : $username;
        $stmt   = $db->prepare(
            'INSERT INTO usuario (nombre, usuario, correo, contraseña, fecha_registro)
             VALUES (?, ?, ?, ?, NOW())'
        );
        $stmt->execute([$nombre, $username, $email, $hash]);
        return (int) $db->lastInsertId();
    }

    /**
     * Verifica una contraseña en texto plano contra su hash bcrypt almacenado.
     *
     * @param string $plain Contraseña ingresada por el usuario.
     * @param string $hash  Hash bcrypt almacenado en la base de datos.
     * @return bool true si la contraseña es correcta.
     */
    public static function verifyPassword(string $plain, string $hash): bool {
        return password_verify($plain, $hash);
    }

    /**
     * Actualiza el nombre de usuario y el avatar de un usuario.
     * No valida unicidad del username — esa responsabilidad es del controller.
     *
     * @param int    $userId   ID del usuario a actualizar.
     * @param string $username Nuevo nombre de usuario.
     * @param string $avatar   Emoji de avatar seleccionado.
     * @return bool true si la query se ejecutó sin errores.
     */
    public static function updateProfile(int $userId, string $username, string $avatar): bool {
        $stmt = getDB()->prepare(
            'UPDATE usuario SET usuario = ?, avatar = ? WHERE id_usuario = ?'
        );
        $stmt->execute([$username, $avatar, $userId]);
        return $stmt->rowCount() >= 0;
    }

    /**
     * Actualiza la contraseña de un usuario. La nueva contraseña se hashea con bcrypt.
     * No verifica la contraseña actual — esa responsabilidad es del controller.
     *
     * @param int    $userId      ID del usuario.
     * @param string $newPassword Nueva contraseña en texto plano.
     */
    public static function updatePassword(int $userId, string $newPassword): void {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = getDB()->prepare('UPDATE usuario SET contraseña = ? WHERE id_usuario = ?');
        $stmt->execute([$hash, $userId]);
    }
}
