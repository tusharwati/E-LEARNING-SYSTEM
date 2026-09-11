<?php
include __DIR__ . '/auth.php';
start_secure_session();
include __DIR__ . '/db_config.php';
include __DIR__ . '/layout.php';
include __DIR__ . '/gamification.php';
require_login('daily_challenge.php');

$user_id = (int) $_SESSION['user_id'];
ensure_daily_challenge($conn);
$challenge_stmt = $conn->prepare(
    "SELECT id, challenge_date, question, explanation, xp_reward
     FROM daily_challenges WHERE challenge_date = CURRENT_DATE LIMIT 1"
);
$challenge_stmt->execute();
$challenge = $challenge_stmt->get_result()->fetch_assoc();
if (!$challenge) {
    http_response_code(404);
    exit('No daily challenge is available today.');
}

$options_stmt = $conn->prepare(
    "SELECT id, option_text, is_correct, position
     FROM daily_challenge_options WHERE challenge_id = ?
     ORDER BY position, id"
);
$options_stmt->bind_param("i", $challenge['id']);
$options_stmt->execute();
$options = $options_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$message = '';
$attempt = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $selected_id = filter_input(INPUT_POST, 'option_id', FILTER_VALIDATE_INT);
    $selected = null;
    foreach ($options as $option) {
        if ((int) $option['id'] === (int) $selected_id) {
            $selected = $option;
            break;
        }
    }
    if (!$selected) {
        $message = 'Choose an answer before submitting.';
    } else {
        $is_correct = (int) $selected['is_correct'] === 1;
        $xp_awarded = $is_correct ? (int) $challenge['xp_reward'] : 0;
        $conn->begin_transaction();
        try {
            $attempt_stmt = $conn->prepare(
                "INSERT INTO daily_challenge_attempts
                 (user_id, challenge_id, selected_option_id, is_correct, xp_awarded)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $attempt_stmt->bind_param(
                "iiiii",
                $user_id,
                $challenge['id'],
                $selected_id,
                $is_correct,
                $xp_awarded
            );
            $attempt_stmt->execute();
            if ($is_correct) {
                award_xp(
                    $conn,
                    $user_id,
                    'daily_challenge',
                    (int) $challenge['id'],
                    $xp_awarded,
                    $challenge['challenge_date'],
                    false
                );
            }
            $conn->commit();
            $attempt = [
                'is_correct' => $is_correct,
                'xp_awarded' => $xp_awarded,
            ];
            $message = $is_correct
                ? 'Correct answer. You earned ' . $xp_awarded . ' XP.'
                : 'Not quite. Review the explanation and try again tomorrow.';
        } catch (mysqli_sql_exception $error) {
            $conn->rollback();
            if ($error->getCode() === 1062) {
                $message = "You have already completed today's challenge.";
            } else {
                throw $error;
            }
        }
    }
}

$history_stmt = $conn->prepare(
    "SELECT is_correct, xp_awarded, completed_at
     FROM daily_challenge_attempts
     WHERE user_id = ? AND challenge_id = ?"
);
$history_stmt->bind_param("ii", $user_id, $challenge['id']);
$history_stmt->execute();
$existing_attempt = $history_stmt->get_result()->fetch_assoc();
$stats_stmt = $conn->prepare(
    "SELECT total_xp, level, current_streak, longest_streak
     FROM user_learning_stats WHERE user_id = ?"
);
$stats_stmt->bind_param("i", $user_id);
$stats_stmt->execute();
$stats = $stats_stmt->get_result()->fetch_assoc() ?: [
    'total_xp' => 0, 'level' => 1, 'current_streak' => 0, 'longest_streak' => 0
];

render_header('Daily challenge', 'challenge');
?>
<div class="page-heading">
    <span class="eyebrow">Daily challenge · <?= htmlspecialchars($challenge['challenge_date']) ?></span>
    <h1>Test one concept today.</h1>
    <p>Answer once per day and build a consistent learning streak.</p>
</div>
<section class="panel">
    <div class="section-heading">
        <h2><?= htmlspecialchars($challenge['question']) ?></h2>
        <span class="badge"><?= (int) $challenge['xp_reward'] ?> XP</span>
    </div>
    <?php if ($message): ?><p class="muted"><strong><?= htmlspecialchars($message) ?></strong></p><?php endif; ?>
    <?php if (!$existing_attempt && !$attempt): ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <?php foreach ($options as $option): ?>
                <label style="display: block; margin: 12px 0;">
                    <input type="radio" name="option_id" value="<?= (int) $option['id'] ?>" required>
                    <?= htmlspecialchars($option['option_text']) ?>
                </label>
            <?php endforeach; ?>
            <button class="button button-primary" type="submit">Submit answer</button>
        </form>
    <?php else: ?>
        <p class="badge"><?= ($existing_attempt['is_correct'] ?? $attempt['is_correct']) ? 'Correct' : 'Completed' ?></p>
        <h3>Explanation</h3>
        <p class="muted"><?= nl2br(htmlspecialchars($challenge['explanation'])) ?></p>
    <?php endif; ?>
</section>
<div class="stats-grid">
    <div class="stat-card"><span class="stat-label">Total XP</span><span class="stat-value"><?= (int) $stats['total_xp'] ?></span></div>
    <div class="stat-card"><span class="stat-label">Level</span><span class="stat-value"><?= (int) $stats['level'] ?></span></div>
    <div class="stat-card"><span class="stat-label">Current streak</span><span class="stat-value"><?= (int) $stats['current_streak'] ?></span><span class="muted">days</span></div>
    <div class="stat-card"><span class="stat-label">Longest streak</span><span class="stat-value"><?= (int) $stats['longest_streak'] ?></span><span class="muted">days</span></div>
</div>
<?php render_footer(); ?>
