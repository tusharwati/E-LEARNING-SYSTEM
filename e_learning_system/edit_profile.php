<?php
session_start();
include 'db_config.php';

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql_user = "SELECT name, email, password FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

$name = $user['name'];
$email = $user['email'];
$password = $user['password'];
$error_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']); // Updated password field

    if (empty($name) || empty($email) || empty($password)) {
        $error_message = "All fields are required.";
    } else {
        $update_sql = "UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("sssi", $name, $email, $password, $user_id);
        
        if ($stmt_update->execute()) {
            header("Location: profile.php");
            exit();
        } else {
            $error_message = "An error occurred. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | E-Learning</title>
    <style>
       /* General Styles */
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(to right, #f8f9fa, #ffecec);
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    animation: fadeIn 0.6s ease-in-out;
}

/* Profile Container */
.edit-profile-container {
    background: #fff;
    padding: 25px;
    box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
    border-radius: 12px;
    text-align: center;
    width: 100%;
    max-width: 450px;
    transition: transform 0.3s ease-in-out;
}

.edit-profile-container:hover {
    transform: scale(1.02);
}

/* Form Styles */
.form-group {
    margin-bottom: 20px;
    text-align: left;
}

label {
    font-weight: bold;
    display: block;
    color: #333;
}

input {
    width: 100%;
    padding: 10px;
    border: 2px solid #ccc;
    border-radius: 8px;
    margin-top: 5px;
    transition: border-color 0.3s ease-in-out;
    font-size: 16px;
}

input:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0px 0px 5px rgba(0, 123, 255, 0.4);
}

/* Buttons */
.btn {
    display: inline-block;
    margin-top: 15px;
    padding: 12px 18px;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    text-align: center;
    font-size: 16px;
    transition: all 0.3s ease-in-out;
}

.btn-primary {
    background:rgb(53, 41, 129);
    color: white;
    border: none;
}

.btn-primary:hover {
    background:rgb(174, 167, 255);
    transform: scale(1.05);
}

.btn-secondary {
    background: rgb(53, 41, 129);
    color: white;
    border: none;
}

.btn-secondary:hover {
    background:rgb(174, 167, 255);
    transform: scale(1.05);
}

/* Error Message */
.error {
    color: red;
    font-size: 14px;
    margin-bottom: 15px;
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 600px) {
    .edit-profile-container {
        width: 90%;
        padding: 20px;
    }
}

    </style>
</head>
<body>
    <div class="edit-profile-container">
        <h2>Edit Profile</h2>
        <?php if (!empty($error_message)): ?>
            <p class="error"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>
        <form action="edit_profile.php" method="post">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" value="<?= htmlspecialchars($password) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="profile.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
