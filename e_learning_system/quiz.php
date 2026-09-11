<?php
include __DIR__ . '/auth.php';
start_secure_session();
include __DIR__ . '/db_config.php';
include __DIR__ . '/layout.php';
require_login('quiz.php?id=' . (int) ($_GET['id'] ?? 0));

$course_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$course_id || $course_id < 1) {
    http_response_code(400);
    exit('Invalid course.');
}

$course_stmt = $conn->prepare("SELECT title FROM courses WHERE id = ?");
$course_stmt->bind_param("i", $course_id);
$course_stmt->execute();
$course = $course_stmt->get_result()->fetch_assoc();
if (!$course) {
    http_response_code(404);
    exit('Course not found.');
}

$question_stmt = $conn->prepare(
    "SELECT id, question, explanation, topic
     FROM quiz_questions
     WHERE course_id = ?
     ORDER BY position, id"
);
$question_stmt->bind_param("i", $course_id);
$question_stmt->execute();
$question_result = $question_stmt->get_result();
$questions = [];
$option_stmt = $conn->prepare(
    "SELECT id, option_text, is_correct
     FROM quiz_options
     WHERE question_id = ?
     ORDER BY position, id"
);
while ($question = $question_result->fetch_assoc()) {
    $option_stmt->bind_param("i", $question['id']);
    $option_stmt->execute();
    $options = [];
    $option_result = $option_stmt->get_result();
    while ($option = $option_result->fetch_assoc()) {
        $options[(int) $option['id']] = $option;
    }
    $question['options'] = $options;
    $questions[(int) $question['id']] = $question;
}

if (!$questions) {
    exit('No quiz available for this course.');
}

$attempt = null;
$answers = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $score = 0;

    foreach ($questions as $question_id => $question) {
        $selected_id = filter_var(
            $_POST['answer_' . $question_id] ?? null,
            FILTER_VALIDATE_INT
        );
        $selected = $selected_id && isset($question['options'][$selected_id])
            ? $question['options'][$selected_id]
            : null;
        $is_correct = $selected && (int) $selected['is_correct'] === 1;
        if ($is_correct) {
            $score++;
        }
        $answers[$question_id] = [
            'selected_option_id' => $selected ? $selected_id : null,
            'is_correct' => $is_correct,
        ];
    }

    $conn->begin_transaction();
    try {
        $attempt_stmt = $conn->prepare(
            "INSERT INTO quiz_attempts (user_id, course_id, score, total_questions)
             VALUES (?, ?, ?, ?)"
        );
        $total_questions = count($questions);
        $attempt_stmt->bind_param("iiii", $_SESSION['user_id'], $course_id, $score, $total_questions);
        $attempt_stmt->execute();
        $attempt_id = $conn->insert_id;

        foreach ($answers as $question_id => $answer) {
            $selected_option_id = $answer['selected_option_id'];
            $is_correct_value = $answer['is_correct'] ? 1 : 0;
            if ($selected_option_id === null) {
                $answer_stmt = $conn->prepare(
                    "INSERT INTO quiz_attempt_answers
                     (attempt_id, question_id, selected_option_id, is_correct)
                     VALUES (?, ?, NULL, ?)"
                );
                $answer_stmt->bind_param("iii", $attempt_id, $question_id, $is_correct_value);
            } else {
                $answer_stmt = $conn->prepare(
                    "INSERT INTO quiz_attempt_answers
                     (attempt_id, question_id, selected_option_id, is_correct)
                     VALUES (?, ?, ?, ?)"
                );
                $answer_stmt->bind_param("iiii", $attempt_id, $question_id, $selected_option_id, $is_correct_value);
            }
            $answer_stmt->execute();
            $answer_stmt->close();
        }
        $conn->commit();
        $attempt = ['score' => $score, 'total_questions' => $total_questions];
    } catch (Throwable $error) {
        $conn->rollback();
        throw $error;
    }
}

$history_stmt = $conn->prepare(
    "SELECT score, total_questions, submitted_at
     FROM quiz_attempts
     WHERE user_id = ? AND course_id = ?
     ORDER BY submitted_at DESC
     LIMIT 5"
);
$history_stmt->bind_param("ii", $_SESSION['user_id'], $course_id);
$history_stmt->execute();
$history = $history_stmt->get_result();

render_header('Quiz: ' . $course['title'], 'practice');
?>
<div class="page-heading">
    <span class="eyebrow">Knowledge check</span>
    <h1><?= htmlspecialchars($course['title']) ?> quiz</h1>
    <p>Choose the best answer for each question. Your score and attempt history are saved securely.</p>
</div>

<?php if ($attempt): ?>
    <section class="panel">
        <h2>Attempt complete</h2>
        <p class="stat-value"><?= (int) $attempt['score'] ?> / <?= (int) $attempt['total_questions'] ?></p>
        <p class="muted">Review the explanations below, then try again whenever you are ready.</p>
    </section>
<?php endif; ?>

<section class="panel">
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <?php foreach ($questions as $number => $question): ?>
            <fieldset style="border: 0; border-bottom: 1px solid #e5e7eb; margin: 0 0 22px; padding: 0 0 22px;">
                <legend style="font-weight: 700; margin-bottom: 12px;">
                    <?= (int) $number ?>. <?= htmlspecialchars($question['question']) ?>
                </legend>
                <?php foreach ($question['options'] as $option): ?>
                    <label style="display: block; margin: 9px 0;">
                        <input type="radio" name="answer_<?= (int) $number ?>" value="<?= (int) $option['id'] ?>"
                            <?= $attempt && ($answers[$number]['selected_option_id'] ?? null) === (int) $option['id'] ? 'checked' : '' ?>>
                        <?= htmlspecialchars($option['option_text']) ?>
                    </label>
                <?php endforeach; ?>
                <?php if ($attempt): ?>
                    <p class="muted"><strong>Explanation:</strong> <?= htmlspecialchars($question['explanation'] ?? '') ?></p>
                <?php endif; ?>
            </fieldset>
        <?php endforeach; ?>
        <button class="button button-primary" type="submit">Submit quiz</button>
    </form>
</section>

<section class="panel">
    <h2>Recent attempts</h2>
    <?php if ($history->num_rows === 0): ?>
        <div class="empty-state"><strong>No attempts yet.</strong>Your quiz history will appear here.</div>
    <?php else: ?>
        <?php while ($past_attempt = $history->fetch_assoc()): ?>
            <p>
                <strong><?= (int) $past_attempt['score'] ?>/<?= (int) $past_attempt['total_questions'] ?></strong>
                <span class="muted"> · <?= htmlspecialchars($past_attempt['submitted_at']) ?></span>
            </p>
        <?php endwhile; ?>
    <?php endif; ?>
</section>
<?php render_footer(); ?>
