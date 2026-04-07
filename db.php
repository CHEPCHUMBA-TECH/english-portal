<?php
/**
 * Database Connection
 * Uses environment configuration from config.php
 */

// Load configuration
require_once __DIR__ . '/config.php';

// Create connection with credentials from environment
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    // Log error securely
    error_log("Database Connection Error: " . $conn->connect_error);
    
    if (APP_DEBUG) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        die("Database connection failed. Please contact support.");
    }
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// Optional: for testing in debug mode
if (APP_DEBUG) {
    // echo "Database connected successfully!";
}
?>