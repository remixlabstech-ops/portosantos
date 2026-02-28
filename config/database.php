<?php
/**
 * Database Configuration - PDO Connection
 * Porto Santos - Sistema ERP Jurídico
 *
 * IMPORTANTE: Este arquivo contém credenciais sensíveis.
 * Adicionar ao .gitignore em produção.
 */

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'db_user');
define('DB_PASS', getenv('DB_PASS') ?: 'db_password');
define('DB_NAME', getenv('DB_NAME') ?: 'db_name');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_CHARSET', 'utf8mb4');
