<?php
session_start();
include 'db_config.php';

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Secure password check using password_verify
        if ($password === $row['password']) {
            $_SESSION['user_id'] = $row['id'];
            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert('Invalid Password! Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Invalid User! Please try again.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login | E-Learning</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="email-container">
        <form action="login.php" method="POST">
            <fieldset>
                <legend>LOGIN</legend>

                <div class="input-container">
                    <input type="email" id="email" name="email" required>
                    <label for="email">Enter your email</label>
                </div>

                <div class="input-container">
                    <input type="password" id="password" name="password" required 
                           pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                           title="Password must contain at least:
                           - One lowercase letter
                           - One uppercase letter
                           - One number
                           - Minimum 8 characters">
                    <label for="password">PASSWORD</label>
                </div>

                <br><br>
                <button type="submit">SUBMIT</button>

                <p style="margin-top: 27px; text-align: center; text-decoration: none;">
                    Don't have an account? <a href="signup.php">Signup here</a>
                </p>

            </fieldset>
        </form>  
    </div>
</body>
</html>
