<?php

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'loopbook');
define('DB_USER', 'root');
define('DB_PASS', '');

/**
 * Devuelve la instancia PDO compartida (singleton).
 * Si la conexión falla, registra el error técnico en el log del servidor
 * y muestra un mensaje genérico al usuario para no exponer datos internos.
 *
 * @return PDO Instancia de conexión a MySQL.
 */
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            error_log('[Loopbook] Error de conexión a BD: ' . $e->getMessage());
            http_response_code(500);
            die('No se pudo conectar al servidor. Por favor intenta más tarde.');
        }
    }
    return $pdo;
}
