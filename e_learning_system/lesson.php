<?php
include __DIR__ . '/auth.php';
start_secure_session();
include __DIR__ . '/db_config.php';
include __DIR__ . '/layout.php';
$lesson_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
require_login('lesson.php?id=' . (int) $lesson_id);
if (!$lesson_id || $lesson_id < 1) { http_response_code(400); exit('Invalid lesson.'); }
$stmt = $conn->prepare("SELECT lessons.*, courses.title AS course_title FROM lessons JOIN courses ON lessons.course_id = courses.id WHERE lessons.id = ?");
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$lesson = $stmt->get_result()->fetch_assoc();
if (!$lesson) { http_response_code(404); exit('Lesson not found.'); }
$progress_stmt = $conn->prepare("SELECT completed_at FROM lesson_progress WHERE user_id = ? AND lesson_id = ?");
$progress_stmt->bind_param("ii", $_SESSION['user_id'], $lesson_id);
$progress_stmt->execute();
$completed = $progress_stmt->get_result()->num_rows > 0;
$note_path = trim($lesson['file_path'] ?? '');
render_header($lesson['title'], 'courses');
?>
<div class="lesson-header"><a class="back-link" href="course_detail.php?id=<?= (int) $lesson['course_id'] ?>">&lt;- <?= htmlspecialchars($lesson['course_title']) ?></a><span class="eyebrow">Lesson</span><h1><?= htmlspecialchars($lesson['title']) ?></h1><p class="muted">Learn at your pace, then mark this lesson complete when you are ready.</p></div>
<div class="lesson-layout">
    <main>
        <section class="panel video-panel"><div class="video-placeholder"><iframe src="<?= htmlspecialchars($lesson['video_url']) ?>" title="<?= htmlspecialchars($lesson['title']) ?>" allowfullscreen></iframe></div></section>
        <section class="panel"><span class="eyebrow">Lesson notes</span><h2>Key ideas</h2><p><?= nl2br(htmlspecialchars($lesson['content'])) ?></p><?php if ($note_path): ?><a class="button button-secondary" href="<?= htmlspecialchars($note_path) ?>" download>Download notes</a><?php endif; ?></section>
    </main>
    <aside class="lesson-sidebar panel"><span class="badge"><?= $completed ? 'Completed' : 'In progress' ?></span><h2>Keep your momentum</h2><p class="muted">Finish this lesson to keep your course progress up to date.</p><?php if (!$completed): ?><form action="complete_lesson.php" method="post"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>"><input type="hidden" name="lesson_id" value="<?= (int) $lesson_id ?>"><button class="button button-primary" type="submit">Mark lesson complete</button></form><?php else: ?><p class="success-text">Lesson completed successfully.</p><?php endif; ?></aside>
</div>
<?php render_footer(); ?>
