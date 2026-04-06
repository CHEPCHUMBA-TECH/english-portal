<?php
$mysqli = new mysqli('localhost', 'root', '', 'english_portal');
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

$result = $mysqli->query('DESCRIBE scores');
if (!$result) {
    die('Query failed: ' . $mysqli->error);
}

while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . "\t" . $row['Type'] . "\n";
}

$mysqli->close();
