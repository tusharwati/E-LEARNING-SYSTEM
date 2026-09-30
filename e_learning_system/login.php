<?php
include __DIR__ . '/auth.php';
start_secure_session();
include __DIR__ . '/db_config.php';

$error_message = '';
$return_to = safe_return_to($_GET['return_to'] ?? $_POST['return_to'] ?? 'index.php');
$auth_message = $_SESSION['auth_message'] ?? '';
unset($_SESSION['auth_message']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $valid = $row && password_verify($password, $row['password']);
    $legacy = $row && !$valid && !password_get_info($row['password'])['algo'] && hash_equals($row['password'], $password);
    if ($valid || $legacy) {
        if ($legacy) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $upgrade = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $upgrade->bind_param("si", $hash, $row['id']);
            $upgrade->execute();
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $row['id'];
        $_SESSION['role'] = $row['role'] ?? 'student';
        header('Location: ' . $return_to);
        exit;
    }
    $error_message = 'Invalid email or password.';
}
?>
<!doctype html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Log in | LearnSpace</title><link rel="stylesheet" href="index.css"></head>
<body class="auth-page">
    <main class="auth-card">
        <a class="brand" href="index.php">LearnSpace</a>
        <span class="eyebrow" style="display:block;margin-top:36px;">Welcome back</span>
        <h1>Continue learning.</h1>
        <p class="muted">Log in to access your courses, progress, quizzes, and practice.</p>
        <?php if ($auth_message): ?><div class="notice"><?= htmlspecialchars($auth_message) ?></div><?php endif; ?>
        <?php if ($error_message): ?><div class="error-message"><?= htmlspecialchars($error_message) ?></div><?php endif; ?>
        <form method="post">
            <input type="hidden" name="return_to" value="<?= htmlspecialchars($return_to) ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <label>Email<input type="email" name="email" autocomplete="email" required></label>
            <label>Password<input type="password" name="password" autocomplete="current-password" required></label>
            <button class="button button-primary" type="submit">Log in</button>
        </form>
        <p class="muted">New to LearnSpace? <a href="signup.php?return_to=<?= rawurlencode($return_to) ?>">Create an account</a></p>
    </main>
</body></html>
