<?php
/**
 * Password Migration Script
 * Run this ONCE to convert plaintext passwords to hashed passwords
 * Then delete this file
 * 
 * Usage: php migrate_passwords.php
 */

require_once 'config.php';
require_once 'db.php';

echo "Starting password migration...\n";

// Fetch all users with plaintext passwords
$result = $conn->query("SELECT id, username, password FROM users");

if (!$result) {
    die("Error: " . $conn->error . "\n");
}

$migrated = 0;
$skipped = 0;

while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $username = $row['username'];
    $password = $row['password'];
    
    // Check if password is already hashed (hashes start with $2)
    if (strpos($password, '$2') === 0) {
        echo "[SKIPPED] User '$username' - password already hashed\n";
        $skipped++;
        continue;
    }
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    
    // Update database
    $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
    if (!$stmt) {
        echo "[ERROR] User '$username' - " . $conn->error . "\n";
        continue;
    }
    
    $stmt->bind_param("si", $hashed_password, $id);
    if ($stmt->execute()) {
        echo "[SUCCESS] User '$username' password hashed\n";
        $migrated++;
    } else {
        echo "[ERROR] User '$username' - " . $stmt->error . "\n";
    }
    $stmt->close();
}

echo "\n=== Migration Complete ===\n";
echo "Migrated: $migrated\n";
echo "Skipped: $skipped\n";
echo "\n⚠️  IMPORTANT: Delete this file after running migration!\n";
echo "Command: rm migrate_passwords.php\n";
?>
