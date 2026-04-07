<?php
session_start();
require_once 'config.php';
require_once 'db.php';

// Only teachers can access this page
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'teacher'){
    header("Location: login.php");
    exit();
}

// Handle form submission
if(isset($_POST['update'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $teacher_id = $_SESSION['user_id'];

    // Hash password if enabled
    if (SECURE_PASSWORD_HASHING) {
        $password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE users SET username=?, password=? WHERE id=? AND role='teacher'");
    if ($stmt) {
        $stmt->bind_param("ssi", $username, $password, $teacher_id);
        $stmt->execute();
        $stmt->close();
        $message = "Profile updated successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Fetch current teacher info
$teacher_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE id=$teacher_id AND role='teacher'");
$teacher = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Teacher Settings</title>
<style>
body { font-family: Arial; background: #f1f5f9; text-align: center; }
.container { max-width: 500px; margin: 50px auto; padding: 20px; background: white; border-radius: 10px; }
h1 { color: #1e40af; }
input { width: 90%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #ccc; }
button { padding: 10px 20px; border-radius: 5px; background: #3b82f6; color: white; border: none; cursor: pointer; }
.message { color: green; }
a.btn { display: inline-block; margin-top: 20px; padding: 10px 15px; background: #3b82f6; color: white; border-radius: 5px; text-decoration: none; }
</style>
</head>
<body>
<div class="container">
<h1>Teacher Settings</h1>

<?php if(isset($message)) echo "<p class='message'>$message</p>"; ?>

<form method="POST" action="">
    <input type="text" name="username" value="<?php echo htmlspecialchars($teacher['username']); ?>" required placeholder="Username">
    <input type="text" name="password" value="<?php echo htmlspecialchars($teacher['password']); ?>" required placeholder="Password">
    <br><br>
    <button type="submit" name="update">Update Profile</button>
</form>

<a href="teacher_dashboard.php" class="btn">Back to Dashboard</a>
</div>
</body>
</html>

<?php $conn->close(); ?>