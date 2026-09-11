<?php
include __DIR__ . '/auth.php';
start_secure_session();
include __DIR__ . '/db_config.php';
include __DIR__ . '/layout.php';
require_login('problem.php?id=' . (int) ($_GET['id'] ?? 0));

$problem_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$problem_id || $problem_id < 1) {
    http_response_code(400);
    exit('Invalid problem.');
}
$stmt = $conn->prepare(
    "SELECT p.*, t.name AS topic_name
     FROM practice_problems p
     JOIN practice_topics t ON t.id = p.topic_id
     WHERE p.id = ?"
);
$stmt->bind_param("i", $problem_id);
$stmt->execute();
$problem = $stmt->get_result()->fetch_assoc();
if (!$problem) {
    http_response_code(404);
    exit('Problem not found.');
}

$message = '';
$is_correct = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $answer = trim($_POST['answer'] ?? '');
    if ($answer === '') {
        $message = 'Submit an answer before checking your work.';
    } else {
        $normalize = static function ($value) {
            return strtolower(trim(preg_replace('/\s+/', ' ', $value)));
        };
        $is_correct = hash_equals($normalize($problem['expected_answer']), $normalize($answer));
        $submit_stmt = $conn->prepare(
            "INSERT INTO practice_submissions (user_id, problem_id, answer_text, is_correct)
             VALUES (?, ?, ?, ?)"
        );
        $correct_value = $is_correct ? 1 : 0;
        $submit_stmt->bind_param("iisi", $_SESSION['user_id'], $problem_id, $answer, $correct_value);
        $submit_stmt->execute();
        $message = $is_correct ? 'Correct answer. Nice work.' : 'Not quite. Review the hint and try again.';
    }
}

$history_stmt = $conn->prepare(
    "SELECT is_correct, submitted_at FROM practice_submissions
     WHERE user_id = ? AND problem_id = ? ORDER BY submitted_at DESC LIMIT 5"
);
$history_stmt->bind_param("ii", $_SESSION['user_id'], $problem_id);
$history_stmt->execute();
$history = $history_stmt->get_result();

render_header($problem['title'], 'practice');
?>
<div class="page-heading">
    <span class="eyebrow"><?= htmlspecialchars($problem['topic_name']) ?> · <?= htmlspecialchars($problem['difficulty']) ?></span>
    <h1><?= htmlspecialchars($problem['title']) ?></h1>
</div>
<section class="panel">
    <h2>Problem statement</h2>
    <p><?= nl2br(htmlspecialchars($problem['statement'])) ?></p>
    <?php if ($problem['examples']): ?><h3>Example</h3><p class="muted"><?= nl2br(htmlspecialchars($problem['examples'])) ?></p><?php endif; ?>
    <?php if ($problem['constraints_text']): ?><h3>Constraints</h3><p class="muted"><?= nl2br(htmlspecialchars($problem['constraints_text'])) ?></p><?php endif; ?>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <label for="answer">Your answer</label>
        <textarea id="answer" name="answer" rows="4" required style="display: block; margin: 8px 0 14px; width: 100%;"></textarea>
        <button class="button button-primary" type="submit">Check answer</button>
    </form>
    <?php if ($message): ?><p class="muted" style="margin-top: 18px;"><strong><?= htmlspecialchars($message) ?></strong></p><?php endif; ?>
</section>
<section class="panel">
    <h2>Hint and editorial</h2>
    <h3>Hint</h3><p class="muted"><?= nl2br(htmlspecialchars($problem['hint'] ?? '')) ?></p>
    <?php if ($is_correct === true || $history->num_rows > 0): ?>
        <h3>Editorial</h3><p class="muted"><?= nl2br(htmlspecialchars($problem['editorial'] ?? '')) ?></p>
    <?php endif; ?>
</section>
<section class="panel">
    <h2>Submission history</h2>
    <?php if ($history->num_rows === 0): ?>
        <div class="empty-state"><strong>No submissions yet.</strong>Your attempts will appear here.</div>
    <?php else: ?>
        <?php while ($submission = $history->fetch_assoc()): ?>
            <p><span class="badge"><?= $submission['is_correct'] ? 'Correct' : 'Needs review' ?></span> <span class="muted"><?= htmlspecialchars($submission['submitted_at']) ?></span></p>
        <?php endwhile; ?>
    <?php endif; ?>
</section>
<?php render_footer(); ?>
