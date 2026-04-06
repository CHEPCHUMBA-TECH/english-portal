
<?php
session_start();

// Only teachers can access
if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'teacher'){
    header("Location: login.php");
    exit();
}

// Database connection
$host = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "english_portal"; 
$conn = new mysqli($host, $dbuser, $dbpass, $dbname);
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete
if(isset($_GET['delete_id'])){
    $id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM users WHERE id=$id AND role='student'");
    header("Location: managestudents.php");
    exit();
}

// Handle edit
if(isset($_POST['edit'])){
    $id = intval($_POST['id']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']); 
    $conn->query("UPDATE users SET username='$username', password='$password' WHERE id=$id AND role='student'");
    header("Location: managestudents.php");
    exit();
}

// Search functionality
$search = "";
if(isset($_GET['search'])){
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM users WHERE role='student' AND username LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM users WHERE role='student'";
}

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Students</title>
<style>
body { font-family: Arial; background: #f1f5f9; text-align: center; }
.container { max-width: 800px; margin: 30px auto; padding: 20px; background: white; border-radius: 10px; }
h1 { color: #1e40af; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
th { background: #3b82f6; color: white; }
a, button { text-decoration: none; padding: 5px 10px; border-radius: 5px; }
a.delete { background: #ef4444; color: white; }
a.edit { background: #f59e0b; color: white; }
a.btn { background: #3b82f6; color: white; margin-top: 20px; display: inline-block; }
</style>
</head>
<body>
<div class="container">
<h1>Manage Students</h1>

<form method="GET" action="">
    <input type="text" name="search" placeholder="Search by username" value="<?php echo htmlspecialchars($search); ?>">
    <input type="submit" value="Search">
</form>

<table>
<tr>
    <th>ID</th>
    <th>Username</th>
    <th>Password</th>
    <th>Actions</th>
</tr>

<?php
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        echo "<tr>
            <td>".$row['id']."</td>
            <td>".$row['username']."</td>
            <td>".$row['password']."</td>
            <td>
                <a href='?delete_id=".$row['id']."' class='delete' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                <a href='#' onclick='editStudent(".$row['id'].",\"".$row['username']."\",\"".$row['password']."\")' class='edit'>Edit</a>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No students found</td></tr>";
}
?>
</table>

<a href="teacher_dashboard.php" class="btn">Back to Dashboard</a>

<!-- Edit Modal -->
<div id="editModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:white; padding:20px; border-radius:10px; border:1px solid #ccc;">
<h2>Edit Student</h2>
<form method="POST" action="">
    <input type="hidden" name="id" id="edit_id">
    <label>Username</label>
    <input type="text" name="username" id="edit_username" required>
    <label>Password</label>
    <input type="text" name="password" id="edit_password" required>
    <br><br>
    <button type="submit" name="edit">Save</button>
    <button type="button" onclick="closeModal()">Cancel</button>
</form>
</div>

<script>
function editStudent(id, username, password){
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_password').value = password;
    document.getElementById('editModal').style.display = 'block';
}

function closeModal(){
    document.getElementById('editModal').style.display = 'none';
}
</script>

</div>
</body>
</html>

<?php $conn->close(); ?>