<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$module = "Vocabulary";
$maxScore = 20;

$steps = [
    'synonyms' => [
        'revision' => 'Mini Revision: Synonyms are words with similar meanings. Example: fast - quick.',
        'exercise' => 'Exercise: Write 2 synonyms for the word "happy".',
        'keywords' => ['joyful','cheerful','glad','happy']
    ],
    'antonyms' => [
        'revision' => 'Mini Revision: Antonyms are words with opposite meanings. Example: hard - easy.',
        'exercise' => 'Exercise: Write 2 antonyms for the word "difficult".',
        'keywords' => ['easy','simple','light']
    ],
    'homonyms' => [
        'revision' => 'Mini Revision: Homonyms are words that sound alike but have different meanings. Example: bat (animal) / bat (sports).',
        'exercise' => 'Exercise: Give an example of a homonym for "bank".',
        'keywords' => ['river','money','financial','side']
    ],
    'context' => [
        'revision' => 'Mini Revision: Context questions test your understanding of word usage in sentences.',
        'exercise' => 'Exercise: Use the word "ambitious" correctly in a sentence.',
        'keywords' => ['ambitious','determined','goal-oriented']
    ]
];

$step = $_GET['step'] ?? 'synonyms';
if (!isset($_SESSION['vocab_scores'])) {
    $_SESSION['vocab_scores'] = array_fill_keys(array_keys($steps), 0);
}

function checkKeywords($text, $keywords) {
    $text = strtolower($text);
    foreach ($keywords as $k) {
        if (strpos($text, strtolower($k)) !== false) return true;
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic = $_POST['topic'];
    $answer = $_POST['answer'] ?? '';
    $score = 0;

    if (isset($steps[$topic])) {
        if (checkKeywords($answer, $steps[$topic]['keywords'])) $score = 5;
        $_SESSION['vocab_scores'][$topic] = $score;
    }

    $step_keys = array_keys($steps);
    $current_index = array_search($topic, $step_keys);
    if ($current_index < count($step_keys) - 1) {
        $next_step = $step_keys[$current_index + 1];
        header("Location: vocab.php?step=$next_step");
        exit();
    } else {
        $total_score = array_sum($_SESSION['vocab_scores']);
        $stmt = $conn->prepare("INSERT INTO scores(user_id,module,score,maximum_score,date_taken) VALUES(?,?,?,?,NOW())");
        $stmt->bind_param("isii", $user_id, $module, $total_score, $maxScore);
        $stmt->execute();
        $stmt->close();
        unset($_SESSION['vocab_scores']);
        $final_message = "🎉 Vocabulary Completed! Score: $total_score / $maxScore";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Vocabulary Exercise – Grade 7</title>
<style>
body { font-family: Arial, sans-serif; background:#f1f5f9; padding:20px; }
.container { max-width:800px; margin:auto; background:white; padding:30px; border-radius:12px; box-shadow:0 10px 25px rgba(0,0,0,0.2); }
h1,h2 { text-align:center; color:#1e40af; }
.note { background:#e0f2fe; padding:15px; border-radius:10px; margin-bottom:15px; font-style:italic; }
.task { margin-bottom:20px; padding:15px; background:#f8f9fa; border-radius:8px; border-left:4px solid #ffc107; }
textarea { width:100%; padding:12px; margin:10px 0; border-radius:8px; border:2px solid #ccc; min-height:100px; }
button { padding:12px 25px; background:#3b82f6; color:white; border:none; border-radius:25px; cursor:pointer; font-size:16px; }
button:hover { background:#2563eb; }
.back-button { display:block; margin:20px auto; background:#10b981; color:white; padding:12px 25px; text-align:center; border-radius:25px; text-decoration:none; width:200px; font-size:16px; }
.back-button:hover { background:#059669; }
.message { text-align:center; font-weight:bold; color:green; margin-top:20px; }
</style>
</head>
<body>
<div class="container">
<h1>✏️ Vocabulary Exercise</h1>

<?php if(isset($final_message)): ?>
<h2 class="message"><?php echo $final_message; ?></h2>
<a href="student_dashboard.php" class="back-button">Back to Dashboard</a>
<?php else: ?>
<h2>Step: <?php echo ucfirst($step); ?></h2>
<div class="note"><?php echo $steps[$step]['revision']; ?></div>
<div class="task">
<p><b><?php echo $steps[$step]['exercise']; ?></b></p>
<form method="POST">
<input type="hidden" name="topic" value="<?php echo $step; ?>">
<textarea name="answer" required></textarea>
<button type="submit"><?php echo (array_search($step, array_keys($steps)) === count($steps)-1) ? "✅ Finish Module" : "Next ➜"; ?></button>
</form>
</div>
<a href="student_dashboard.php" class="back-button">Back to Dashboard</a>
<?php endif; ?>
</div>
</body>
</html>