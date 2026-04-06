<?php
include 'db.php';
session_start();

// Block access if user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$module = "Reading";
$maxScore = 20;

// Determine step
$step = $_GET['step'] ?? 'skim';

// Initialize session scores
if (!isset($_SESSION['reading_scores'])) {
    $_SESSION['reading_scores'] = [
        'skim' => 0,
        'scan' => 0,
        'mainidea' => 0,
        'context' => 0
    ];
}

// Keyword checker function
function checkKeywords($text, $keywords) {
    $text = strtolower($text);
    foreach ($keywords as $k) {
        if (strpos($text, strtolower($k)) !== false) {
            return true;
        }
    }
    return false;
}

// Handle submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic = $_POST['topic'];
    $answer = $_POST['answer'] ?? '';
    $score = 0;

    if ($topic === 'skim') {
        if (checkKeywords($answer, ['library','books','project'])) $score = 5;
        $_SESSION['reading_scores']['skim'] = $score;
        header("Location: reading.php?step=scan");
        exit();
    }

    if ($topic === 'scan') {
        if (checkKeywords($answer, ['science','storybook','dictionary'])) $score = 5;
        $_SESSION['reading_scores']['scan'] = $score;
        header("Location: reading.php?step=mainidea");
        exit();
    }

    if ($topic === 'mainidea') {
        if (checkKeywords($answer, ['project','reading','help'])) $score = 5;
        $_SESSION['reading_scores']['mainidea'] = $score;
        header("Location: reading.php?step=context");
        exit();
    }

    if ($topic === 'context') {
        if (checkKeywords($answer, ['fun','younger brother','summarize'])) $score = 5;
        $_SESSION['reading_scores']['context'] = $score;

        // Final total
        $total_score = array_sum($_SESSION['reading_scores']);

        $stmt = $conn->prepare("
            INSERT INTO scores (user_id,module,score,maximum_score,date_taken)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("isii", $user_id, $module, $total_score, $maxScore);
        $stmt->execute();
        $stmt->close();

        unset($_SESSION['reading_scores']);
        $final_message = "🎉 Reading Complete! Score: $total_score / $maxScore";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reading Skills – Grade 7</title>
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
textarea { width: 100%; padding: 12px; margin: 10px 0; border: 2px solid #ecf0f1; border-radius: 8px; font-size: 16px; font-family: inherit; resize: vertical; min-height: 120px; transition: border-color 0.3s ease; }
textarea:focus { border-color: #3498db; outline: none; box-shadow: 0 0 5px rgba(52, 152, 219, 0.3); }
button { padding: 12px 25px; background: linear-gradient(45deg, #3498db, #2980b9); color: white; border: none; border-radius: 25px; cursor: pointer; margin: 10px 5px; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3); }
button:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4); }
.back-button { display: block; margin: 30px auto; background: linear-gradient(45deg, #10b981, #059669); color: white; padding: 12px 25px; text-align: center; border-radius: 25px; text-decoration: none; width: 200px; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); }
.back-button:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4); }
</style>
</head>
<body>
<div class="container">
<?php 
$steps = ['skim', 'scan', 'mainidea', 'context'];
$current_index = array_search($step, $steps);
$progress = ($current_index / 3) * 100;
?>
<div class="progress-bar">
    <div class="progress-fill" style="width: <?php echo $progress; ?>%;"></div>
</div>

<h1>📖 Reading Skills – Grade 7</h1>

<?php if(isset($final_message)) echo "<h2 style='color:green;'>$final_message</h2>"; ?>

<?php if($step==='skim'): ?>
<h2>Step 1: Skimming</h2>
<div class="note">
Skimming is reading quickly to get the <em>main idea</em>.
</div>
<div class="task">
<p><b>Paragraph:</b> Mary went to the library to borrow books for her school project. She borrowed a science book, a storybook, and a dictionary. After returning home, she read the science book first because her project was due tomorrow. Her younger brother helped her summarize the storybook for fun.</p>
<p>What is the main idea of the paragraph?</p>
</div>
<form method="POST">
<input type="hidden" name="topic" value="skim">
<textarea name="answer" required></textarea>
<button type="submit">Next ➜</button>
</form>

<?php elseif($step==='scan'): ?>
<h2>Step 2: Scanning</h2>
<div class="note">
Scanning is reading to find <em>specific details</em>.
</div>
<div class="task">
<p><b>Paragraph:</b> Mary went to the library to borrow books for her school project. She borrowed a science book, a storybook, and a dictionary. After returning home, she read the science book first because her project was due tomorrow. Her younger brother helped her summarize the storybook for fun.</p>
<p>List the specific books Mary borrowed.</p>
</div>
<form method="POST">
<input type="hidden" name="topic" value="scan">
<textarea name="answer" required></textarea>
<button type="submit">Next ➜</button>
</form>

<?php elseif($step==='mainidea'): ?>
<h2>Step 3: Main Idea</h2>
<div class="note">
The main idea is the most important point of the paragraph.
</div>
<div class="task">
<p><b>Paragraph:</b> Mary went to the library to borrow books for her school project. She borrowed a science book, a storybook, and a dictionary. After returning home, she read the science book first because her project was due tomorrow. Her younger brother helped her summarize the storybook for fun.</p>
<p>What does this paragraph tell you about Mary?</p>
</div>
<form method="POST">
<input type="hidden" name="topic" value="mainidea">
<textarea name="answer" required></textarea>
<button type="submit">Next ➜</button>
</form>

<?php elseif($step==='context'): ?>
<h2>Step 4: Context Clues</h2>
<div class="note">
Context clues help understand the meaning of words in the paragraph.
</div>
<div class="task">
<p><b>Paragraph:</b> Mary went to the library to borrow books for her school project. She borrowed a science book, a storybook, and a dictionary. After returning home, she read the science book first because her project was due tomorrow. Her younger brother helped her summarize the storybook for fun.</p>
<p>What does the word <b>summarize</b> mean in this context?</p>
</div>
<form method="POST">
<input type="hidden" name="topic" value="context">
<textarea name="answer" required></textarea>
<button type="submit">✅ Finish Module</button>
</form>
<?php endif; ?>

<a href="student_dashboard.php" class="back-button">Back to Dashboard</a>
</div>
</body>
</html>