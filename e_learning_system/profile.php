<?php
include __DIR__ . '/auth.php';
start_secure_session();
include __DIR__ . '/db_config.php';
include __DIR__ . '/layout.php';
require_login('profile.php');

$user_id = (int) $_SESSION['user_id'];
$stmt_user = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

render_header('Profile', 'profile');
?>
<div class="page-heading">
    <span class="eyebrow">Account</span>
    <h1>Your profile</h1>
    <p>Manage your account details and keep your learning identity up to date.</p>
</div>
<section class="panel" style="max-width: 680px;">
    <div class="profile-info">
        <p><strong>Name</strong><br><?= htmlspecialchars($user['name'] ?? '') ?></p>
        <p><strong>Email</strong><br><?= htmlspecialchars($user['email'] ?? '') ?></p>
        <p><strong>Password</strong><br><span class="muted">Your password is securely stored and hidden.</span></p>
    </div>
    <div class="actions">
        <a class="button button-primary" href="edit_profile.php">Edit profile</a>
        <a class="button button-secondary" href="dashboard.php">Back to dashboard</a>
    </div>
</section>
<?php render_footer(); ?>
