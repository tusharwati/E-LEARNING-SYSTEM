<?php
include __DIR__ . '/auth.php';
start_secure_session();
include __DIR__ . '/db_config.php';
include __DIR__ . '/layout.php';

$query = "SELECT courses.id, courses.title, courses.description, COUNT(quiz_questions.id) AS question_count
          FROM courses
          LEFT JOIN quiz_questions ON quiz_questions.course_id = courses.id
          GROUP BY courses.id
          ORDER BY courses.title";
$result = $conn->query($query);
render_header('Practice and quizzes', isset($_SESSION['user_id']) ? 'practice' : '');
?>
<div class="page-heading">
    <span class="eyebrow">Practice and quizzes</span>
    <h1>Check your understanding.</h1>
    <p>Use short quizzes to reinforce the concepts you are learning.</p>
</div>
<div class="course-grid">
    <?php while ($course = $result->fetch_assoc()): ?>
        <article class="course-card">
            <div class="course-card-body">
                <span class="badge"><?= (int) $course['question_count'] ?> questions</span>
                <h3><?= htmlspecialchars($course['title']) ?></h3>
                <p><?= htmlspecialchars($course['description']) ?></p>
                <?php $quiz_url = 'quiz.php?id=' . (int) $course['id']; ?>
                <a class="button button-secondary" href="<?= htmlspecialchars(isset($_SESSION['user_id']) ? $quiz_url : '#') ?>" <?= isset($_SESSION['user_id']) ? '' : 'data-auth-open data-return-to="' . htmlspecialchars($quiz_url) . '"' ?>>Attempt quiz</a>
            </div>
        </article>
    <?php endwhile; ?>
</div>
<?php render_footer(); ?>
