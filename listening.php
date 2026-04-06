<?php
// listening_speaking.php
include 'db.php';
session_start();

// Block access if user is not logged in
if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$module = "Listening & Speaking";

// Define all topics
$topics = [
    1 => [
        "title" => "Greetings & Introductions",
        "notes" => "Greeting someone is how you start a conversation. Examples: 'Hello!', 'Hi!', 'Good morning!'. Introductions: You say your name, age, class, or hobby. Smile and make eye contact.",
        "questions" => [
            "Imagine meeting a new student. Write how you would greet and introduce yourself, including your name, age, and hobby.",
            "Write 2 alternative greetings you could use with someone older than you."
        ],
        "keywords" => ['hello','hi','name','hobby','age']
    ],
    2 => [
        "title" => "Polite Expressions",
        "notes" => "Polite expressions make communication smooth and respectful. Examples: 'Please', 'Thank you', 'Sorry', 'Excuse me'. Use them when asking, apologizing, or thanking someone.",
        "questions" => [
            "Write a sentence asking for help politely.",
            "Write a sentence apologizing politely.",
            "Write a sentence thanking someone politely."
        ],
        "keywords" => ['please','thank','sorry','excuse']
    ],
    3 => [
        "title" => "Polite Interruptions",
        "notes" => "Sometimes you need to interrupt politely to ask a question or clarify. Examples: 'Excuse me, may I ask something?', 'Sorry to interrupt, but...'",
        "questions" => [
            "Write a polite interruption to ask a question in class.",
            "Write a polite interruption to clarify what someone just said."
        ],
        "keywords" => ['sorry','excuse','pardon','wait']
    ],
    4 => [
        "title" => "Turn-Taking",
        "notes" => "Turn-taking ensures everyone in a conversation or group discussion gets a chance to speak. Examples: 'It’s my turn to answer', 'May I speak next?', 'Go ahead'.",
        "questions" => [
            "Write how you would politely ask for your turn in a group discussion.",
            "Write how you would allow someone else to take their turn."
        ],
        "keywords" => ['listen','turn','speak','question','answer']
    ]
];

// Get current topic page (default 1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if(!isset($topics[$page])){
    $page = 1;
}

// Initialize session scores array
if(!isset($_SESSION['LS_scores'])){
    $_SESSION['LS_scores'] = [];
}

$feedback = "";
$isLastTopic = ($page == count($topics));

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Score current topic
    $topic_score = 0;
    foreach($topics[$page]['questions'] as $i => $q){
        $answer = $_POST['answer'.$i] ?? '';
        foreach($topics[$page]['keywords'] as $word){
            if(stripos($answer, $word) !== false){
                $topic_score += 1;
            }
        }
    }

    // Save topic score in session
    $_SESSION['LS_scores'][$page] = $topic_score;

    // Maximum score for this topic
    $maxScore = count($topics[$page]['keywords']);

    // Feedback for this page
    $feedback = "✅ Your work for '{$topics[$page]['title']}' is scored. Score: $topic_score / $maxScore";

    // If last topic, sum total and store in DB
    if($isLastTopic){
        $total_score = array_sum($_SESSION['LS_scores']);
        $max_total = 0;
        foreach($topics as $t){
            $max_total += count($t['keywords']);
        }

        $stmt = $conn->prepare("INSERT INTO scores (user_id, module, score, maximum_score, date_taken) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("isii", $user_id, $module, $total_score, $max_total);
        $stmt->execute();
        $stmt->close();

        $feedback .= "<br>🎉 Total Listening & Speaking score saved: $total_score / $max_total";

        // Clear session scores
        unset($_SESSION['LS_scores']);
    } else {
        // Move to next topic automatically
        $nextPage = $page + 1;
        header("Location: ?page=$nextPage");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Listening & Speaking – <?php echo $topics[$page]['title']; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: 0; padding: 20px; min-height: 100vh; }
.container { background: white; padding: 40px; max-width: 900px; margin: auto; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); position: relative; overflow: hidden; }
.container::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 5px; background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4); }
.progress-bar { width: 100%; height: 20px; background: #e0e0e0; border-radius: 10px; margin-bottom: 20px; overflow: hidden; }
.progress-fill { height: 100%; background: linear-gradient(90deg, #4ecdc4, #45b7d1); transition: width 0.3s ease; }
h1 { color: #2c3e50; text-align: center; margin-bottom: 30px; font-weight: 600; }
h2 { color: #2c3e50; margin-bottom: 20px; font-weight: 500; }
.box { background: linear-gradient(135deg, #e3f2fd, #f3e5f5); padding: 20px; border-radius: 10px; margin-bottom: 20px; border-left: 5px solid #2196f3; }
textarea { width: 100%; padding: 12px; margin: 10px 0; border: 2px solid #ecf0f1; border-radius: 8px; font-size: 16px; font-family: inherit; resize: vertical; min-height: 80px; transition: border-color 0.3s ease; }
textarea:focus { border-color: #3498db; outline: none; box-shadow: 0 0 5px rgba(52, 152, 219, 0.3); }
button { padding: 12px 25px; background: linear-gradient(45deg, #3498db, #2980b9); color: white; border: none; border-radius: 25px; cursor: pointer; margin: 10px 5px; font-size: 16px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3); }
button:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4); }
.feedback { margin-top: 15px; font-weight: bold; color: green; background: #d4edda; padding: 10px; border-radius: 8px; border: 1px solid #c3e6cb; }
</style>
</head>
<body>
<div class="container">
<?php $progress = (($page - 1) / 3) * 100; ?>
<div class="progress-bar">
    <div class="progress-fill" style="width: <?php echo $progress; ?>%;"></div>
</div>

<h1>Listening & Speaking – <?php echo $topics[$page]['title']; ?></h1>

<?php if($feedback): ?>
<div class="feedback"><?php echo $feedback; ?></div>
<?php endif; ?>

<div class="box">
<h2>Revision Notes</h2>
<p><?php echo $topics[$page]['notes']; ?></p>
</div>

<form method="POST">
<?php foreach($topics[$page]['questions'] as $i => $q): ?>
<div class="box">
<p><strong>Question <?php echo $i+1; ?>:</strong> <?php echo $q; ?></p>
<textarea name="answer<?php echo $i; ?>" rows="3" placeholder="Write your answer here..."></textarea>
</div>
<?php endforeach; ?>

<button type="submit"><?php echo $isLastTopic ? "Submit Final Score" : "Next Topic"; ?></button>
</form>

</div>
</body>
</html>