<?php
// Database credentials. Override via environment variables in production.
// WARNING: Never commit real credentials to version control.
// Set DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT as environment variables on the server.
define('DB_HOST', getenv('DB_HOST') ?: 'sql100.infinityfree.com');
define('DB_USER', getenv('DB_USER') ?: 'if0_41229268');
define('DB_PASS', getenv('DB_PASS') ?: 'Porto2026');
define('DB_NAME', getenv('DB_NAME') ?: 'if0_41229268_ps');
define('DB_PORT', getenv('DB_PORT') ?: '3306');

function getConnection(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";port=" . DB_PORT . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return $pdo;
}
