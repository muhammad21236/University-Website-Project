<?php
session_start();
include('db.php');

if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Function to view all users
function viewAllUsers($conn) {
    $result = $conn->query("SELECT id, username, role FROM users");
    echo "<h3>All Users</h3>";
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Action</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['username']}</td>
                <td>{$row['role']}</td>
                <td><a href='admin.php?delete={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this user?');\">Delete</a></td>
              </tr>";
    }
    echo "</table>";
}

// Function to add a new teacher
function addTeacher($conn, $username, $password) {
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $role = 'teacher';
    
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $passwordHash, $role);
    $stmt->execute();
    $stmt->close();
    
    echo "<p>Teacher added successfully!</p>";
}

// Function to delete a user
function deleteUser($conn, $id) {
    $conn->query("DELETE FROM users WHERE id = $id");
    echo "<p>User deleted successfully!</p>";
}

// Handle form submission and deletion requests
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_teacher'])) {
    addTeacher($conn, $_POST['username'], $_POST['password']);
} elseif (isset($_GET['delete'])) {
    deleteUser($conn, $_GET['delete']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Welcome to Admin Dashboard, <?php echo $_SESSION['username']; ?>!</h2>
        
        <!-- Add New Teacher -->
        <div class="section">
            <h3>Add New Teacher</h3>
            <form action="admin.php" method="post">
                <input type="text" name="username" placeholder="Teacher Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="add_teacher">Add Teacher</button>
            </form>
        </div>

        <!-- View All Users -->
        <div class="section">
            <?php viewAllUsers($conn); ?>
        </div>
    </div>
</body>
</html>
