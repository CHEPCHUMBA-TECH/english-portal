<?php
ob_start();
session_start();
include "db.php";

// 1. SESSION CHECK (Matches your login.php names)
$user_role = isset($_SESSION['user_role']) ? strtolower(trim($_SESSION['user_role'])) : '';

if (!isset($_SESSION['user_id']) || $user_role !== 'student') {
    session_write_close(); 
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$module = "Writing";
$maxScore = 50;

// Determine current step
$step = $_GET['step'] ?? 'plan';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_final'])) {
        $score = 0;
        // Scoring logic based on transition words
        $keywords = ['because', 'therefore', 'however', 'finally', 'moreover', 'consequently'];
        $essay = trim(strtolower($_POST['essay_content'] ?? ''));

        foreach ($keywords as $word) {
            if (strpos($essay, $word) !== false) {
                $score += 8.5; // Adding points for each connector used
            }
        }
        if ($score > $maxScore) $score = $maxScore;

        // Database Insertion
        $stmt = $conn->prepare("INSERT INTO scores (user_id, module, score, maximum_score, date_taken) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("isii", $user_id, $module, $score, $maxScore);
        $stmt->execute();
        $stmt->close();

        session_write_close();
        $final_message = "🎉 Writing Assessment Submitted! Score: $score / $maxScore";
    }
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Writing Skills – Grade 7</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: 0; padding: 20px; min-height: 100vh; }
        .container { background: white; padding: 40px; max-width: 900px; margin: auto; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); position: relative; overflow: hidden; }
        .container::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 5px; background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4); }
        .progress-bar { width: 100%; height: 20px; background: #e0e0e0; border-radius: 10px; margin-bottom: 20px; overflow: hidden; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, #4ecdc4, #45b7d1); transition: width 0.3s ease; }
        h1 { color: #2c3e50; text-align: center; margin-bottom: 30px; font-weight: 600; }
        h2 { color: #2c3e50; margin-bottom: 20px; font-weight: 500; }
        .note { background: linear-gradient(135deg, #e3f2fd, #f3e5f5); padding: 20px; border-radius: 10px; margin-bottom: 20px; border-left: 5px solid #2196f3; }
        .task { margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #ffc107; }
        textarea { width: 100%; padding: 15px; margin: 10px 0; border: 2px solid #ecf0f1; border-radius: 8px; font-size: 16px; font-family: inherit; resize: vertical; min-height: 250px; transition: border-color 0.3s ease; }
        textarea:focus { border-color: #3498db; outline: none; box-shadow: 0 0 5px rgba(52, 152, 219, 0.3); }
        button { padding: 12px 25px; background: linear-gradient(45deg, #3498db, #2980b9); color: white; border: none; border-radius: 25px; cursor: pointer; margin: 10px 5px; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3); }
        button:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4); }
        .back-button { display: block; margin: 30px auto; background: linear-gradient(45deg, #10b981, #059669); color: white; padding: 12px 25px; text-align: center; border-radius: 25px; text-decoration: none; width: 200px; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); }
    </style>
</head>
<body>
<div class="container">
    <div class="progress-bar">
        <div class="progress-fill" style="width: <?php echo isset($final_message) ? '100%' : '50%'; ?>;"></div>
    </div>

    <h1>✍️ Writing Skills – Assessment</h1>

    <?php if(isset($final_message)): ?>
        <div class="note" style="text-align:center;">
            <h2 style="color: #059669;"><?php echo $final_message; ?></h2>
            <p>Your essay has been evaluated for the use of transition words and structure.</p>
        </div>
    <?php else: ?>
        <h2>Final Step: Creative Essay</h2>
        
        <div class="note">
            <strong>Tip:</strong> Use transition words like <em>"however"</em>, <em>"because"</em>, <em>"therefore"</em>, and <em>"finally"</em> to improve your score!
        </div>

        <div class="task">
            <p><strong>Prompt:</strong> Write a short essay (100-150 words) about the importance of reading books in the modern world. Why is it still important even with the internet?</p>
        </div>

        <form method="POST">
            <textarea name="essay_content" placeholder="Start writing your essay here..." required></textarea>
            <div style="text-align: right;">
                <button type="submit" name="submit_final">✅ Submit My Writing</button>
            </div>
        </form>
    <?php endif; ?>

    <a href="student_dashboard.php" class="back-button">Back to Dashboard</a>
</div>
</body>
</html>
