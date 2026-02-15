<?php
include 'C:\xampp\htdocs\e_learning_system\db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['pass']; // plain text

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
    echo "<script>alert('Signup successful!'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="email-container">
        <form action="" method="POST">
            <fieldset>
                <legend>SIGNUP</legend>

                <div class="input-container">
                    <input type="text" id="name" name="name" required>
                    <label for="name">NAME</label>
                </div>

                <div class="input-container">
                    <input type="email" id="email" name="email" required>
                    <label for="email">Enter your email</label>
                </div>

                <div class="input-container">
                    <input type="password" id="pass" name="pass" required 
                        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                        title="Password must contain at least: 
                        - One lowercase letter 
                        - One uppercase letter 
                        - One number 
                        - Minimum 8 characters">
                    <label for="pass">PASSWORD</label>
                </div>

                <br><br>
                <button type="submit">SIGNUP</button>
            </fieldset>
        </form>  
    </div>
</body>
</html>
