<?php

function render_header($title, $active = '')
{
    $user_is_logged_in = isset($_SESSION['user_id']);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($title) ?> | E-Learning</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
    <div class="app-shell">
        <header class="topbar">
            <a class="brand" href="index.php">LearnSpace</a>
            <form class="nav-search" action="courses.php" method="get">
                <label class="sr-only" for="global-search">Search courses and topics</label>
                <input id="global-search" type="search" name="q" placeholder="Search courses and topics">
                <button type="submit" aria-label="Search">Search</button>
            </form>
            <nav class="topbar-actions" aria-label="Account navigation">
                <?php if ($user_is_logged_in): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <a href="profile.php">Profile</a>
                    <a class="button button-quiet" href="logout.php">Log out</a>
                <?php else: ?>
                    <a href="#" data-auth-open data-return-to="dashboard.php">Dashboard</a>
                    <a href="#" data-auth-open data-return-to="profile.php">Profile</a>
                    <a href="#" data-auth-open data-return-to="dashboard.php">Log in</a>
                    <a class="button button-primary" href="#" data-auth-open data-auth-mode="signup" data-return-to="dashboard.php">Create account</a>
                <?php endif; ?>
            </nav>
        </header>
        <div class="page-layout">
            <?php if ($user_is_logged_in): ?>
                <aside class="sidebar">
                    <p class="sidebar-label">Workspace</p>
                    <a class="<?= $active === 'dashboard' ? 'is-active' : '' ?>" href="dashboard.php">Overview</a>
                    <a class="<?= $active === 'courses' ? 'is-active' : '' ?>" href="courses.php">Courses</a>
                    <a class="<?= $active === 'practice' ? 'is-active' : '' ?>" href="quiz_list.php">Practice & quizzes</a>
                    <a class="<?= $active === 'problems' ? 'is-active' : '' ?>" href="practice.php">Problems</a>
                    <a class="<?= $active === 'challenge' ? 'is-active' : '' ?>" href="daily_challenge.php">Daily challenge</a>
                    <a class="<?= $active === 'leaderboard' ? 'is-active' : '' ?>" href="leaderboard.php">Leaderboard</a>
                    <p class="sidebar-label">Account</p>
                    <a class="<?= $active === 'profile' ? 'is-active' : '' ?>" href="profile.php">Profile settings</a>
                </aside>
            <?php endif; ?>
            <main class="page-content<?= $user_is_logged_in ? '' : ' page-content-public' ?>">
    <?php
}

function render_footer()
{
    $user_is_logged_in = isset($_SESSION['user_id']);
    ?>
            </main>
        </div>
        <footer class="site-footer">
            <div><strong>LearnSpace</strong><span>Practical learning for curious people.</span></div>
            <nav aria-label="Footer navigation"><a href="courses.php">Courses</a><a href="practice.php">Practice</a><a href="quiz_list.php">Quizzes</a></nav>
            <small>&copy; <?= date('Y') ?> LearnSpace. Learn at your own pace.</small>
        </footer>
        <?php if (!$user_is_logged_in): ?>
        <div id="auth-modal" class="auth-modal" hidden>
            <div class="auth-modal-backdrop"></div>
            <section class="auth-modal-card" role="dialog" aria-modal="true" aria-labelledby="auth-modal-title">
                <button class="auth-modal-close" type="button" aria-label="Close" data-auth-close>&times;</button>
                <div class="auth-modal-brand">LearnSpace</div>
                <div class="auth-tabs" role="tablist">
                    <button type="button" class="auth-tab is-active" data-auth-tab="login">Log in</button>
                    <button type="button" class="auth-tab" data-auth-tab="signup">Sign up</button>
                </div>
                <h2 id="auth-modal-title" data-auth-title>Welcome back</h2>
                <p class="muted" data-auth-subtitle>Log in to continue to your learning destination.</p>
                <div class="auth-modal-message" data-auth-message hidden></div>
                <form data-auth-form>
                    <input type="hidden" name="mode" value="login">
                    <input type="hidden" name="return_to" value="index.php">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                    <div data-auth-fields="login">
                        <label>Email or username<input type="email" name="email" autocomplete="email" required></label>
                        <label>Password<span class="password-field"><input id="modal-login-password" type="password" name="password" autocomplete="current-password" required><button type="button" data-password-toggle="modal-login-password">Show</button></span></label>
                        <div class="auth-options"><label class="checkbox-label"><input type="checkbox" name="remember"> Remember me</label><a href="login.php">Forgot password?</a></div>
                    </div>
                    <div data-auth-fields="signup" hidden>
                        <label>Name<input type="text" name="name" autocomplete="name"></label>
                        <label>Email<input type="email" name="signup_email" autocomplete="email"></label>
                        <label>Password<span class="password-field"><input id="modal-signup-password" type="password" name="signup_password" autocomplete="new-password"><button type="button" data-password-toggle="modal-signup-password">Show</button></span></label>
                        <label>Confirm password<span class="password-field"><input id="modal-confirm-password" type="password" name="confirm_password" autocomplete="new-password"><button type="button" data-password-toggle="modal-confirm-password">Show</button></span></label>
                    </div>
                    <button class="button button-primary auth-submit" type="submit">Log in</button>
                </form>
                <div class="social-login"><button type="button" disabled data-social-login>Continue with Google</button><button type="button" disabled data-social-login>Continue with Microsoft</button></div>
                <div class="auth-divider"><span>or continue with email</span></div>
                <p class="auth-modal-footnote">Social login coming soon. Email login is available now.</p>
            </section>
        </div>
        <?php endif; ?>
    </div>
    <script src="auth-modal.js"></script>
    </body>
    </html>
    <?php
}
