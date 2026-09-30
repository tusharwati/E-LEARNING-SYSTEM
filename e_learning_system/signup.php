<?php
include __DIR__ . '/auth.php';
start_secure_session();
include __DIR__ . '/db_config.php';
$return_to = safe_return_to($_GET['return_to'] ?? $_POST['return_to'] ?? 'index.php');
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($name === '' || strlen($name) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Enter a valid name and email address.';
    } elseif (!validate_password($password)) {
        $error_message = 'Password must be at least 8 characters and include uppercase, lowercase, and a number.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hash);
        if ($stmt->execute()) {
            header('Location: login.php?return_to=' . rawurlencode($return_to));
            exit;
        }
        $error_message = $stmt->errno === 1062 ? 'An account with that email already exists.' : 'Unable to create the account. Please try again.';
    }
}
?>
<!doctype html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Create account | LearnSpace</title><link rel="stylesheet" href="index.css"></head>
<body class="auth-page">
    <main class="auth-card">
        <a class="brand" href="index.php">LearnSpace</a>
        <span class="eyebrow" style="display:block;margin-top:36px;">Start your path</span>
        <h1>Create your account.</h1>
        <p class="muted">Save your progress and build a consistent learning habit.</p>
        <?php if ($error_message): ?><div class="error-message"><?= htmlspecialchars($error_message) ?></div><?php endif; ?>
        <form method="post">
            <input type="hidden" name="return_to" value="<?= htmlspecialchars($return_to) ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <label>Name<input type="text" name="name" autocomplete="name" required></label>
            <label>Email<input type="email" name="email" autocomplete="email" required></label>
            <label>Password<input type="password" name="password" autocomplete="new-password" minlength="8" required></label>
            <button class="button button-primary" type="submit">Create account</button>
        </form>
        <p class="muted">Already have an account? <a href="login.php?return_to=<?= rawurlencode($return_to) ?>">Log in</a></p>
    </main>
</body></html>
