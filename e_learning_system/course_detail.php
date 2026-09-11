<?php
include __DIR__ . '/auth.php';
start_secure_session();
include __DIR__ . '/db_config.php';
include __DIR__ . '/layout.php';
$course_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$course_id || $course_id < 1) { http_response_code(400); exit('Invalid course.'); }
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();
if (!$course) { http_response_code(404); exit('Course not found.'); }
$category_stmt = $conn->prepare("SELECT categories.name FROM categories JOIN course_categories ON course_categories.category_id = categories.id WHERE course_categories.course_id = ? ORDER BY categories.name");
$category_stmt->bind_param("i", $course_id);
$category_stmt->execute();
$categories = $category_stmt->get_result();
$lesson_stmt = $conn->prepare("SELECT lessons.id, lessons.title, modules.title AS module_title FROM lessons LEFT JOIN modules ON modules.id = lessons.module_id WHERE lessons.course_id = ? ORDER BY modules.position, lessons.position, lessons.id");
$lesson_stmt->bind_param("i", $course_id);
$lesson_stmt->execute();
$lessons = $lesson_stmt->get_result();
$first_stmt = $conn->prepare("SELECT id FROM lessons WHERE course_id = ? ORDER BY position, id LIMIT 1");
$first_stmt->bind_param("i", $course_id);
$first_stmt->execute();
$first_row = $first_stmt->get_result()->fetch_assoc();
$first_lesson_id = $first_row ? (int) $first_row['id'] : null;
$enrolled = false;
if (isset($_SESSION['user_id'])) {
    $enroll_stmt = $conn->prepare("SELECT id FROM course_enrollments WHERE user_id = ? AND course_id = ?");
    $enroll_stmt->bind_param("ii", $_SESSION['user_id'], $course_id);
    $enroll_stmt->execute();
    $enrolled = $enroll_stmt->get_result()->num_rows > 0;
}
render_header($course['title'], isset($_SESSION['user_id']) ? 'courses' : '');
?>
<div class="course-hero panel">
    <div>
        <div class="tag-row"><?php while ($category = $categories->fetch_assoc()): ?><span class="badge"><?= htmlspecialchars($category['name']) ?></span><?php endwhile; ?></div>
        <h1><?= htmlspecialchars($course['title']) ?></h1>
        <p class="lead"><?= htmlspecialchars($course['description']) ?></p>
        <?php if ($enrolled): ?><p class="success-text">You are enrolled in this course.</p><?php endif; ?>
        <div class="actions">
            <?php if ($first_lesson_id !== null): $start_url = 'lesson.php?id=' . $first_lesson_id; ?><a class="button button-primary" href="<?= htmlspecialchars(isset($_SESSION['user_id']) ? $start_url : '#') ?>" <?= isset($_SESSION['user_id']) ? '' : 'data-auth-open data-return-to="' . htmlspecialchars($start_url) . '"' ?>>Start learning</a><?php endif; ?>
            <?php if (isset($_SESSION['user_id']) && !$enrolled): ?><form action="enroll.php" method="post"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>"><input type="hidden" name="course_id" value="<?= $course_id ?>"><button class="button button-secondary" type="submit">Enroll in course</button></form><?php elseif (!isset($_SESSION['user_id'])): ?><a class="button button-secondary" href="#" data-auth-open data-return-to="course_detail.php?id=<?= $course_id ?>">Enroll in course</a><?php endif; ?>
        </div>
    </div>
    <div class="course-hero-art"><span><?= strtoupper(substr($course['title'], 0, 1)) ?></span><small>Structured course</small></div>
</div>
<section class="panel lesson-outline">
    <div class="section-heading"><div><span class="eyebrow">Course outline</span><h2>Lessons and modules</h2></div><span class="muted">Preview the curriculum</span></div>
    <?php $current_module = null; while ($lesson = $lessons->fetch_assoc()): if ($current_module !== $lesson['module_title']): $current_module = $lesson['module_title']; ?><h3 class="module-heading"><?= htmlspecialchars($current_module ?: 'Lessons') ?></h3><?php endif; ?><a class="lesson-row" href="<?= htmlspecialchars(isset($_SESSION['user_id']) ? 'lesson.php?id=' . (int) $lesson['id'] : '#') ?>" <?= isset($_SESSION['user_id']) ? '' : 'data-auth-open data-return-to="lesson.php?id=' . (int) $lesson['id'] . '"' ?>><span class="lesson-number"><?= (int) $lesson['id'] ?></span><span><?= htmlspecialchars($lesson['title']) ?></span><span class="lesson-arrow">-&gt;</span></a><?php endwhile; ?>
</section>
<?php render_footer(); ?>
