<?php
session_start();
include 'db.php';

// Example session username
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = 'Student1'; // Replace with login logic
}
$username = $_SESSION['username'];
$role = 'student'; // Replace with role from DB after login
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - My Portal</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background-color: #f1f5f9;
    }
    header {
      background-color: #1e40af;
      color: white;
      padding: 20px;
      text-align: center;
    }
    header h1 {
      margin: 0;
      font-size: 24px;
    }
    header p {
      margin: 5px 0 0;
      font-size: 16px;
    }
    .container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 20px;
      max-width: 1000px;
      margin: 30px auto;
      padding: 0 20px;
    }
    .card {
      background: white;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      transition: transform 0.2s, box-shadow 0.2s;
      text-decoration: none;
      color: #1e40af;
      font-weight: bold;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
      color: #2563eb;
    }
    .card h2 {
      margin: 0;
      font-size: 18px;
    }
    footer {
      text-align: center;
      padding: 20px;
      background: #1e40af;
      color: white;
      margin-top: 40px;
    }
    @media (max-width: 500px) {
      .container {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>Welcome to English Portal</h1>
    <p>User: <?php echo htmlspecialchars($username); ?> | Role: <?php echo htmlspecialchars($role); ?></p>
  </header>

  <div class="container">
    <a href="reading.php" class="card">
      <h2>Reading</h2>
    </a>
    <a href="writing.php" class="card">
      <h2>Writing</h2>
    </a>
    <a href="grammar.php" class="card">
      <h2>Grammar</h2>
    </a>
    <a href="listening.php" class="card">
      <h2>Listening</h2>
    </a>
    <a href="vocab.php" class="card">
      <h2>Vocabulary</h2>
    </a>
    <?php if($role === 'teacher'): ?>
      <a href="teacher_dashboard.php" class="card">
        <h2>Teacher Panel</h2>
      </a>
    <?php endif; ?>
  </div>

  <footer>
    &copy; <?php echo date("Y"); ?> English Portal. All rights reserved.
  </footer>
</body>
</html>