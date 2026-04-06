<?php
$mysqli = new mysqli('localhost', 'root', '', 'english_portal');
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

$sql = "SELECT u.id as student_id, u.username, s.module, s.score, s.maximum_score, s.date_taken FROM scores s JOIN users u ON s.user_id = u.id WHERE u.role='student' ORDER BY u.username, s.module";
$result = $mysqli->query($sql);
if (!$result) {
    die('SQL error: ' . $mysqli->error);
}

echo "OK rows: " . $result->num_rows . "\n";

$mysqli->close();
