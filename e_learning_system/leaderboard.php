<?php
include __DIR__ . '/auth.php';
start_secure_session();
include __DIR__ . '/db_config.php';
include __DIR__ . '/layout.php';
require_login('leaderboard.php');

$user_id = (int) $_SESSION['user_id'];
$leaderboard_stmt = $conn->prepare(
    "SELECT u.id, u.name, COALESCE(s.total_xp, 0) AS total_xp,
            COALESCE(s.level, 1) AS level,
            RANK() OVER (ORDER BY COALESCE(s.total_xp, 0) DESC, u.id ASC) AS rank_position
     FROM users u
     LEFT JOIN user_learning_stats s ON s.user_id = u.id
     WHERE u.role = 'student'
     ORDER BY total_xp DESC, u.id ASC
     LIMIT 50"
);
$leaderboard_stmt->execute();
$leaders = $leaderboard_stmt->get_result();
$user_rank_stmt = $conn->prepare(
    "SELECT ranked.rank_position, ranked.total_xp
     FROM (
         SELECT u.id, COALESCE(s.total_xp, 0) AS total_xp,
                RANK() OVER (ORDER BY COALESCE(s.total_xp, 0) DESC, u.id ASC) AS rank_position
         FROM users u
         LEFT JOIN user_learning_stats s ON s.user_id = u.id
         WHERE u.role = 'student'
     ) ranked
     WHERE ranked.id = ?"
);
$user_rank_stmt->bind_param("i", $user_id);
$user_rank_stmt->execute();
$user_rank = $user_rank_stmt->get_result()->fetch_assoc();

render_header('Leaderboard', 'leaderboard');
?>
<div class="page-heading">
    <span class="eyebrow">Community progress</span>
    <h1>Leaderboard</h1>
    <p>See how learners are progressing through consistent practice and daily challenges.</p>
</div>
<section class="panel">
    <div class="section-heading">
        <h2>Your position</h2>
        <?php if ($user_rank): ?><span class="badge">Rank #<?= (int) $user_rank['rank_position'] ?></span><?php endif; ?>
    </div>
    <p class="muted">Your total: <strong><?= (int) ($user_rank['total_xp'] ?? 0) ?> XP</strong>. Keep learning to improve your position.</p>
</section>
<section class="panel">
    <h2>Top learners</h2>
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Rank</th><th>Learner</th><th>Level</th><th>XP</th></tr></thead>
            <tbody>
            <?php while ($leader = $leaders->fetch_assoc()): ?>
                <tr<?= (int) $leader['id'] === $user_id ? ' class="is-highlighted"' : '' ?>>
                    <td>#<?= (int) $leader['rank_position'] ?></td>
                    <td><?= htmlspecialchars($leader['name'] ?: 'Learner') ?><?= (int) $leader['id'] === $user_id ? ' <span class="badge">You</span>' : '' ?></td>
                    <td><?= (int) $leader['level'] ?></td>
                    <td><strong><?= (int) $leader['total_xp'] ?></strong></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>
<?php render_footer(); ?>
