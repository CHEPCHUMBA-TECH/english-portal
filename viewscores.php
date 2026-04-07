<?php
session_start();
require_once 'config.php';
require_once 'db.php';

// Check if teacher is logged in
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'teacher') {
    header("Location: login.php");
    exit();
}

// Build student filter
$selectedStudentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$filterCondition = '';
if ($selectedStudentId > 0) {
    $filterCondition = ' AND u.id = ?';
}

// Fetch latest student scores per module (optionally filtered by student)
$sql = "
SELECT u.id AS student_id, u.username, s.module, s.score, s.maximum_score AS max_score, s.date_taken
FROM scores s
JOIN users u ON s.user_id = u.id
JOIN (
    SELECT user_id, module, MAX(date_taken) AS latest_date
    FROM scores
    GROUP BY user_id, module
) latest_scores ON s.user_id = latest_scores.user_id 
               AND s.module = latest_scores.module 
               AND s.date_taken = latest_scores.latest_date
WHERE u.role = 'student'" . $filterCondition . "
ORDER BY u.username, s.module
";

if ($selectedStudentId > 0) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $selectedStudentId);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

// Fetch student list for dropdown
$students = [];
$studentsResult = $conn->query("SELECT id, username FROM users WHERE role='student' ORDER BY username");
if ($studentsResult) {
    while ($row = $studentsResult->fetch_assoc()) {
        $students[] = $row;
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Scores</title>
    <style>
        body { font-family: Arial; background: #f1f5f9; text-align: center; }
        .container { max-width: 900px; margin: 30px auto; padding: 20px; background: white; border-radius: 10px; }
        h1 { color: #1e40af; }
        table { margin: 20px auto; border-collapse: collapse; width: 95%; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background-color: #3b82f6; color: white; }
        a.btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #3b82f6; color: white; text-decoration: none; border-radius: 5px; }
        a.btn:hover { background: #2563eb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>All Student Scores</h1>

        <form method="GET" style="margin-bottom:20px;">
            <label for="student_id">Filter by student:</label>
            <select name="student_id" id="student_id" style="padding:6px 10px; margin-left:10px;">
                <option value="0">All students</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?php echo $student['id']; ?>" <?php echo ($selectedStudentId == $student['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($student['username']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" style="padding:6px 12px; margin-left:10px;">Filter</button>
        </form>

        <table>
            <tr>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Module</th>
                <th>Score</th>
                <th>Maximum Score</th>
                <th>Date Taken</th>
            </tr>
            <?php
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>".$row['student_id']."</td>";
                    echo "<td>".$row['username']."</td>";
                    echo "<td>".$row['module']."</td>";
                    echo "<td>".$row['score']."</td>";
                    echo "<td>".$row['max_score']."</td>";
                    echo "<td>".$row['date_taken']."</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No scores found</td></tr>";
            }
            ?>
        </table>
        <a href="teacher_dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>

<?php $conn->close(); ?>