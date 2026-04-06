<?php
session_start();

// Simulate a logged-in teacher (replace with actual login check)
$_SESSION['user_id'] = 5;
$_SESSION['username'] = "Claudia";
$_SESSION['user_role'] = "teacher";

// Redirect if not a teacher
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'teacher') {
    header("Location: login.php");
    exit();
}

// Database connection
$host = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "english_portal";

$conn = new mysqli($host, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch students
$sql = "SELECT * FROM users WHERE role='student'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <style>
        body { font-family: Arial; background: #f1f5f9; text-align: center; }
        .container { max-width: 900px; margin: 30px auto; padding: 20px; background: white; border-radius: 10px; }
        h1 { color: #1e40af; }
        .btn { display: inline-block; margin: 10px; padding: 15px 25px; background: #3b82f6; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; }
        .btn:hover { background: #2563eb; }
        table { margin: 20px auto; border-collapse: collapse; width: 90%; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #3b82f6; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo $_SESSION['username']; ?></h1>

        <!-- Dashboard Buttons -->
        <a href="addcontent.php" class="btn">Add Content</a>
        <a href="managestudents.php" class="btn">Manage Students</a>
        <a href="viewscores.php" class="btn">View Scores</a>
        <a href="settings.php" class="btn">Settings</a>

        <!-- List of Students -->
        <h2>Students List</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>".$row['id']."</td>";
                    echo "<td>".$row['username']."</td>";
                    echo "<td>".$row['role']."</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No students found</td></tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>

<?php $conn->close(); ?>