<?php
/**
 * Configuration Manager
 * Loads environment variables from .env file
 */

// Define base path
define('BASE_PATH', __DIR__);

// Load .env file
$env_file = BASE_PATH . '/.env';
if (!file_exists($env_file)) {
    die("Error: .env file not found. Please copy .env.example to .env and configure it.");
}

// Parse .env file
function load_env($file) {
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue; // Skip comments
        if (strpos($line, '=') === false) continue;
        
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        if (!empty($key)) {
            $_ENV[$key] = $value;
        }
    }
}

load_env($env_file);

// Database Configuration
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'english_portal');

// Application Configuration
define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');
define('APP_DEBUG', $_ENV['APP_DEBUG'] === 'true' ? true : false);
define('SECURE_PASSWORD_HASHING', $_ENV['SECURE_PASSWORD_HASHING'] === 'true' ? true : false);

// Error reporting based on environment
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/logs/error.log');
}

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
?>
