<?php
require_once __DIR__ . '/../../config/database.php';

/**
 * PasswordReset — Gestiona los tokens de recuperación de contraseña.
 *
 * Flujo:
 *  1. create()     → genera token, lo guarda con expiración de 1 hora.
 *  2. findValid()  → busca un token que exista, no haya expirado y no se haya usado.
 *  3. markUsed()   → marca el token como usado tras el reset exitoso.
 *  4. deleteOld()  → limpia tokens anteriores del mismo usuario antes de crear uno nuevo.
 */
class PasswordReset {

    /**
     * Elimina todos los tokens previos de un usuario y crea uno nuevo.
     * El token expira en 1 hora.
     *
     * @param int $userId ID del usuario.
     * @return string El token generado (64 chars hex).
     */
    public static function create(int $userId): string {
        $db    = getDB();
        $token = bin2hex(random_bytes(32)); // 64 chars hex

        // Limpiar tokens anteriores del mismo usuario
        $db->prepare('DELETE FROM password_resets WHERE id_usuario = ?')->execute([$userId]);

        $stmt = $db->prepare(
            'INSERT INTO password_resets (id_usuario, token, expira_en)
             VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))'
        );
        $stmt->execute([$userId, $token]);

        return $token;
    }

    /**
     * Busca un token válido: existe, no expiró y no fue usado.
     *
     * @param string $token Token a verificar.
     * @return array|false Fila del reset o false si no es válido.
     */
    public static function findValid(string $token): array|false {
        $stmt = getDB()->prepare(
            'SELECT * FROM password_resets
             WHERE token = ? AND usado = 0 AND expira_en > NOW()
             LIMIT 1'
        );
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    /**
     * Marca un token como usado para que no pueda reutilizarse.
     *
     * @param string $token Token a invalidar.
     */
    public static function markUsed(string $token): void {
        $stmt = getDB()->prepare(
            'UPDATE password_resets SET usado = 1 WHERE token = ?'
        );
        $stmt->execute([$token]);
    }
}
