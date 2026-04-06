<?php
// DB.php - database connection for your English portal

// Database credentials
$servername = "localhost";  // Usually "localhost" for XAMPP
$username = "root";         // Default MySQL username in XAMPP
$password = "";             // Default MySQL password in XAMPP (usually empty)
$dbname = "english_portal"; // The database you created in phpMyAdmin

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: for testing you can uncomment
// echo "Database connected successfully!";
?>