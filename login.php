<?php
// login.php
session_start();
require_once 'config.php';
require_once 'db.php';

$error = "";

// Handle login form submission
if(isset($_POST['login'])){
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        $error = "An error occurred. Please try again later.";
    } else {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows === 1){
            $row = $result->fetch_assoc();

            // Use password_verify for hashed passwords
            if (SECURE_PASSWORD_HASHING) {
                $password_valid = password_verify($password, $row['password']);
            } else {
                // Fallback for non-hashed passwords (development only)
                $password_valid = ($password === $row['password']);
            }

            if ($password_valid) {
                // Store session variables
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['user_role'] = $row['role'];

                // Redirect based on role
                if($row['role'] === 'teacher'){
                    header("Location: teacher_dashboard.php");
                } else {
                    header("Location: student_dashboard.php");
                }
                exit();
            } else {
                $error = "Invalid username or password!";
            }
        } else {
            $error = "Invalid username or password!";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - My Portal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            width: 350px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        h2 { margin-bottom: 15px; color: #333; }
        input { width: 90%; padding: 10px; margin: 8px 0; border-radius: 6px; border: 1px solid #ccc; }
        button {
            padding: 10px;
            width: 95%;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover { background: #2563eb; }
        .error { color: red; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Login</h2>

    <?php if($error) echo "<div class='error'>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</div>"; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit" name="login">Login</button>
    </form>
</div>

</body>
</html>