<?php
session_start();

// Check if teacher is logged in
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'teacher'){
    header("Location: login.php");
    exit();
}

// Database connection
$host = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "english_portal"; // your database
$conn = new mysqli($host, $dbuser, $dbpass, $dbname);
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if(isset($_POST['submit'])){
    $module = $conn->real_escape_string($_POST['module']);
    $question = $conn->real_escape_string($_POST['question']);
    $keywords = $conn->real_escape_string($_POST['keywords']);
    $max_score = intval($_POST['max_score']);

    $sql = "INSERT INTO content (module, question, keywords, max_score) 
            VALUES ('$module', '$question', '$keywords', $max_score)";

    if($conn->query($sql) === TRUE){
        $message = "Exercise added successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Fetch existing content
$contentResult = $conn->query("SELECT * FROM content ORDER BY date_added DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Content</title>
    <style>
        body { font-family: Arial; background: #f1f5f9; text-align: center; }
        .container { max-width: 900px; margin: 30px auto; padding: 20px; background: white; border-radius: 10px; }
        h1 { color: #1e40af; }
        form { margin: 20px 0; }
        input, textarea, select { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #ccc; }
        input[type=submit] { background: #3b82f6; color: white; border: none; cursor: pointer; }
        input[type=submit]:hover { background: #2563eb; }
        table { margin: 20px auto; border-collapse: collapse; width: 95%; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #3b82f6; color: white; }
        a.btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #3b82f6; color: white; text-decoration: none; border-radius: 5px; }
        a.btn:hover { background: #2563eb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Exercise</h1>

        <?php if(isset($message)) echo "<p>$message</p>"; ?>

        <form method="POST" action="">
            <label>Module</label>
            <select name="module" required>
                <option value="">Select Module</option>
                <option value="Vocabulary">Vocabulary</option>
                <option value="Listening and speaking">Listening and speaking</option>
                <option value="Grammar">Grammar</option>
                <option value="Comprehension">Comprehension</option>
                <option value="Writing">Writing</option>
            </select>

            <label>Question / Passage</label>
            <textarea name="question" rows="5" required></textarea>

            <label>Keywords / Answers (comma-separated)</label>
            <textarea name="keywords" rows="3" required></textarea>

            <label>Maximum Score</label>
            <input type="number" name="max_score" required>

            <input type="submit" name="submit" value="Add Exercise">
        </form>

        <h2>Existing Exercises</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Module</th>
                <th>Question / Passage</th>
                <th>Keywords</th>
                <th>Max Score</th>
                <th>Date Added</th>
            </tr>
            <?php
            if($contentResult->num_rows > 0){
                while($row = $contentResult->fetch_assoc()){
                    echo "<tr>
                        <td>".$row['id']."</td>
                        <td>".$row['module']."</td>
                        <td>".$row['question']."</td>
                        <td>".$row['keywords']."</td>
                        <td>".$row['max_score']."</td>
                        <td>".$row['date_added']."</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No exercises added yet</td></tr>";
            }
            ?>
        </table>

        <a href="teacher_dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>

<?php $conn->close(); ?>