<?php
// Include the database connection
include "db.php";

// Simple test message
echo "Testing database connection...<br>";

// Try fetching something simple from your users table
$sql = "SELECT * FROM users"; // make sure you have a 'users' table
$result = $conn->query($sql);

if($result) {
    echo "✅ Database is connected!<br>";
    echo "Number of users in table: " . $result->num_rows . "<br>";

    // Optional: show the first user
    if($result->num_rows > 0){
        $user = $result->fetch_assoc();
        echo "First user in table: " . $user['username'];
    }
} else {
    echo "❌ Error: " . $conn->error;
}
?>