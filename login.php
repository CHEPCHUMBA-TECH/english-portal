<?php
session_start();
include 'db.php'; // Make sure this connects to your database

$error = "";

// Handle login form submission
if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use prepared statements to avoid SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1){
        $row = $result->fetch_assoc();

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
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - My Portal</title>
    <style>
        body {
            font-family: Arial;
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

        h2 { margin-bottom: 10px; color: #333; }
        .section { margin-bottom: 20px; padding: 15px; border-radius: 10px; }
        .student { background: #e0f2fe; }
        .teacher { background: #fef3c7; }
        input { width: 90%; padding: 10px; margin: 8px 0; }
        button {
            padding: 10px;
            width: 95%;
            background: #3b82f6;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover { background: #2563eb; }
        .error { color: red; }
    </style>
</head>
<body>

<div class="container">
    <h2>My Portal Login</h2>

    <?php if($error) echo "<p class='error'>$error</p>"; ?>

    <!-- STUDENT LOGIN -->
    <div class="section student">
        <h3>Student Login</h3>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login as Student</button>
        </form>
    </div>

    <!-- TEACHER LOGIN -->
    <div class="section teacher">
        <h3>Teacher Login</h3>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login as Teacher</button>
        </form>
    </div>

</div>

</body>
</html>