<?php
include 'db.php';
session_start();

// 1. Fix login check to match your login.php session names
$user_role = isset($_SESSION['user_role']) ? strtolower(trim($_SESSION['user_role'])) : '';

if (!isset($_SESSION['user_id']) || $user_role !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$module = "Grammar";
$maxScore = 20;

// Determine current step
$step = $_GET['step'] ?? 'nouns';

// Initialize session scores
if (!isset($_SESSION['grammar_scores'])) {
    $_SESSION['grammar_scores'] = [
        'nouns' => 0,
        'verbs' => 0,
        'adjectives' => 0,
        'adverbs' => 0
    ];
}

// Keyword checker function
function checkKeywords($text, $keywords) {
    $text = strtolower(trim($text));
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

    if ($topic === 'nouns') {
        if (checkKeywords($answer, ['student', 'teacher', 'classroom', 'school'])) $score = 5;
        $_SESSION['grammar_scores']['nouns'] = $score;
        header("Location: grammar.php?step=verbs");
        exit();
    }

    if ($topic === 'verbs') {
        if (checkKeywords($answer, ['running', 'walking', 'playing', 'studying', 'barked'])) $score = 5;
        $_SESSION['grammar_scores']['verbs'] = $score;
        header("Location: grammar.php?step=adjectives");
        exit();
    }

    if ($topic === 'adjectives') {
        if (checkKeywords($answer, ['beautiful', 'large', 'red', 'smart', 'happy'])) $score = 5;
        $_SESSION['grammar_scores']['adjectives'] = $score;
        header("Location: grammar.php?step=adverbs");
        exit();
    }

    if ($topic === 'adverbs') {
        if (checkKeywords($answer, ['quickly', 'slowly', 'peacefully', 'happily'])) $score = 5;
        $_SESSION['grammar_scores']['adverbs'] = $score;

        // Calculate final total
        $total_score = array_sum($_SESSION['grammar_scores']);

        $stmt = $conn->prepare("
            INSERT INTO scores (user_id, module, score, maximum_score, date_taken)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("isii", $user_id, $module, $total_score, $maxScore);
        $stmt->execute();
        $stmt->close();

        unset($_SESSION['grammar_scores']);
        $final_message = "✅ Grammar Assessment Complete! Score: $total_score / $maxScore";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Grammar Skills – Grade 7</title>
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
textarea { width: 100%; padding: 12px; margin: 10px 0; border: 2px solid #ecf0f1; border-radius: 8px; font-size: 16px; font-family: inherit; resize: vertical; min-height: 100px; transition: border-color 0.3s ease; }
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
$steps = ['nouns', 'verbs', 'adjectives', 'adverbs'];
$current_index = array_search($step, $steps);
$progress = ($current_index / 3) * 100;
?>
<div class="progress-bar">
    <div class="progress-fill" style="width: <?php echo $progress; ?>%;"></div>
</div>

<h1>📝 Grammar Skills – Assessment</h1>

<?php if(isset($final_message)) echo "<h2 style='color:green; text-align:center;'>$final_message</h2>"; ?>

<?php if($step==='nouns'): ?>
<h2>Step 1: Nouns (Naming Words)</h2>
<div class="note">A <strong>noun</strong> is a word that names a person, place, thing, or idea.</div>
<div class="task">
    <p>Identify the nouns in the following sentence:</p>
    <p><em>"The student walked into the large classroom at school."</em></p>
</div>
<form method="POST">
    <input type="hidden" name="topic" value="nouns">
    <textarea name="answer" placeholder="Type the nouns you found here..." required></textarea>
    <button type="submit">Next Step ➜</button>
</form>

<?php elseif($step==='verbs'): ?>
<h2>Step 2: Verbs (Action Words)</h2>
<div class="note">A <strong>verb</strong> describes an action, state, or occurrence.</div>
<div class="task">
    <p>Complete the sentence with an appropriate action verb:</p>
    <p><em>"Yesterday, the hungry dog ___ loudly at the mailman."</em></p>
</div>
<form method="POST">
    <input type="hidden" name="topic" value="verbs">
    <textarea name="answer" placeholder="Type the verb here..." required></textarea>
    <button type="submit">Next Step ➜</button>
</form>

<?php elseif($step==='adjectives'): ?>
<h2>Step 3: Adjectives (Describing Words)</h2>
<div class="note">An <strong>adjective</strong> describes or modifies a noun.</div>
<div class="task">
    <p>Identify the adjective in this sentence:</p>
    <p><em>"She wore a beautiful red dress to the party."</em></p>
</div>
<form method="POST">
    <input type="hidden" name="topic" value="adjectives">
    <textarea name="answer" placeholder="Type the adjective here..." required></textarea>
    <button type="submit">Next Step ➜</button>
</form>

<?php elseif($step==='adverbs'): ?>
<h2>Step 4: Adverbs</h2>
<div class="note">An <strong>adverb</strong> describes how, when, or where an action happens (often ends in -ly).</div>
<div class="task">
    <p>Write a sentence using the adverb <b>'peacefully'</b>.</p>
</div>
<form method="POST">
    <input type="hidden" name="topic" value="adverbs">
    <textarea name="answer" placeholder="Type your sentence here..." required></textarea>
    <button type="submit">✅ Finish Assessment</button>
</form>
<?php endif; ?>

<a href="student_dashboard.php" class="back-button">Back to Dashboard</a>
</div>
</body>
</html>
