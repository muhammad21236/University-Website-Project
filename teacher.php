<?php
session_start();
include('db.php');

if ($_SESSION['role'] != 'teacher') {
    header("Location: login.php");
    exit;
}

// Fetch Teacher Profile
function fetchProfile($conn, $username) {
    $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($username);
    $stmt->fetch();
    $stmt->close();
    return $username;
}

// Update Profile Information
function updateProfile($conn, $currentUsername, $newUsername) {
    $stmt = $conn->prepare("UPDATE users SET username = ? WHERE username = ?");
    $stmt->bind_param("ss", $newUsername, $currentUsername);
    $stmt->execute();
    $_SESSION['username'] = $newUsername;
    echo "<p>Profile updated successfully!</p>";
}

// Handle profile update
$username = $_SESSION['username'];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    updateProfile($conn, $username, $_POST['new_username']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Welcome to Teacher Dashboard, <?php echo $_SESSION['username']; ?>!</h2>
        
        <!-- View Profile Details -->
        <div class="section">
            <h3>Your Profile</h3>
            <p>Username: <?php echo fetchProfile($conn, $_SESSION['username']); ?></p>
        </div>

        <!-- Update Profile -->
        <div class="section">
            <h3>Update Profile</h3>
            <form action="teacher.php" method="post">
                <input type="text" name="new_username" placeholder="New Username" required>
                <button type="submit" name="update_profile">Update Profile</button>
            </form>
        </div>
    </div>
</body>
</html>

