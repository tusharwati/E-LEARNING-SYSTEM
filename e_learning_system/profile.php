<?php
session_start();
include 'db_config.php';

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$sql_user = "SELECT name, email, password FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | E-Learning</title>
    <link rel="stylesheet" href="index.css?v=<?php echo time(); ?>"> <!-- Use same CSS as homepage -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Profile Page Styling - Matches Homepage */

/* General Styling */
body {
    font-family: Arial, sans-serif;
    background-color: #ffecec;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    transition: background-color 0.5s ease-in-out;
}

/* Profile Container */
.profile-container {
    width: 45%;
    background: #fff;
    padding: 25px;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
    border-radius: 10px;
    text-align: center;
    animation: fadeIn 0.5s ease-in-out;
}

/* Profile Info */
.profile-info {
    text-align: left;
    margin-bottom: 20px;
}

.profile-info p {
    font-size: 16px;
    margin: 12px 0;
    transition: color 0.3s ease-in-out;
}

/* Password Input */
input[type="password"] {
    border: none;
    font-size: 16px;
    width: 160px;
    text-align: center;
    background: #f4f4f4;
    padding: 5px;
    border-radius: 5px;
    transition: background 0.3s ease-in-out;
}

input[type="password"]:focus {
    background: #e8e8e8;
    outline: none;
}

/* Toggle Password Button */
.toggle-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 18px;
    margin-left: 5px;
    transition: transform 0.3s ease-in-out;
}

.toggle-btn:hover {
    transform: scale(1.1);
}

/* Buttons */
.btn {
    display: inline-block;
    padding: 10px 20px;
    background: rgb(53, 41, 129);
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: all 0.3s ease-in-out;
    margin: 10px 5px;
}

.btn:hover {
    background-color: rgb(174, 167, 255);
    transform: scale(1.05);
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

    </style>
</head>
<body>
    <div class="profile-container">
        <h2>Profile</h2>
        <div class="profile-info">
            <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Password:</strong> 
                <input type="password" id="passwordField" value="<?= htmlspecialchars($user['password']) ?>" readonly>
                <button class="toggle-btn" onclick="togglePassword()">👁</button>
            </p>
        </div>
        <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
        <a href="index.php" class="btn btn-primary">Back</a>
    </div>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("passwordField");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</body>
</html>
