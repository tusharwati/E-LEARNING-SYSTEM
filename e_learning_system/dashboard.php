<?php
include __DIR__ . '/auth.php';
start_secure_session();
include __DIR__ . '/db_config.php';
include __DIR__ . '/layout.php';
include __DIR__ . '/gamification.php';
include __DIR__ . '/recommendations.php';
require_login('dashboard.php');

$user_id = (int) $_SESSION['user_id'];
ensure_learning_stats($conn, $user_id);
$stmt_user = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

$course_count = $conn->query("SELECT COUNT(*) AS total FROM courses")->fetch_assoc()['total'];
$lesson_count = $conn->query("SELECT COUNT(*) AS total FROM lessons")->fetch_assoc()['total'];
$enrolled_count_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM course_enrollments WHERE user_id = ?");
$enrolled_count_stmt->bind_param("i", $user_id);
$enrolled_count_stmt->execute();
$enrolled_count = $enrolled_count_stmt->get_result()->fetch_assoc()['total'];
$completed_count_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM lesson_progress WHERE user_id = ?");
$completed_count_stmt->bind_param("i", $user_id);
$completed_count_stmt->execute();
$completed_count = $completed_count_stmt->get_result()->fetch_assoc()['total'];
$next_course_stmt = $conn->prepare(
    "SELECT courses.id, courses.title, courses.description, courses.thumbnail,
            COUNT(lessons.id) AS lesson_count,
            COUNT(lesson_progress.lesson_id) AS completed_lessons
     FROM course_enrollments
     JOIN courses ON courses.id = course_enrollments.course_id
     LEFT JOIN lessons ON lessons.course_id = courses.id
     LEFT JOIN lesson_progress ON lesson_progress.lesson_id = lessons.id
        AND lesson_progress.user_id = course_enrollments.user_id
     WHERE course_enrollments.user_id = ?
     GROUP BY courses.id
     ORDER BY course_enrollments.enrolled_at DESC
     LIMIT 1"
);
$next_course_stmt->bind_param("i", $user_id);
$next_course_stmt->execute();
$next_course = $next_course_stmt->get_result()->fetch_assoc();
$stats_stmt = $conn->prepare(
    "SELECT total_xp, level, current_streak FROM user_learning_stats WHERE user_id = ?"
);
$stats_stmt->bind_param("i", $user_id);
$stats_stmt->execute();
$learning_stats = $stats_stmt->get_result()->fetch_assoc();
$weak_topics = get_weak_topics($conn, $user_id);
$recommended_problems = get_recommended_problems($conn, $user_id, $weak_topics);
render_header('Dashboard', 'dashboard');
?>
<div class="page-heading">
    <span class="eyebrow">Your learning workspace</span>
    <h1>Welcome back, <?= htmlspecialchars($user['name'] ?? 'learner') ?>.</h1>
    <p>Pick up where you left off or explore a new topic today.</p>
</div>

<div class="stats-grid">
    <div class="stat-card"><span class="stat-label">Courses available</span><span class="stat-value"><?= (int) $course_count ?></span><span class="muted">Ready to explore</span></div>
    <div class="stat-card"><span class="stat-label">Lessons in library</span><span class="stat-value"><?= (int) $lesson_count ?></span><span class="muted">Across all courses</span></div>
    <div class="stat-card"><span class="stat-label">My courses</span><span class="stat-value"><?= (int) $enrolled_count ?></span><span class="muted"><?= (int) $completed_count ?> lessons completed</span></div>
    <div class="stat-card"><span class="stat-label">Learning XP</span><span class="stat-value"><?= (int) $learning_stats['total_xp'] ?></span><span class="muted">Level <?= (int) $learning_stats['level'] ?></span></div>
    <div class="stat-card"><span class="stat-label">Learning streak</span><span class="stat-value"><?= (int) $learning_stats['current_streak'] ?></span><span class="muted">days</span></div>
</div>

<div class="dashboard-grid">
    <div>
        <section class="panel">
            <div class="section-heading"><h2>Continue learning</h2><span class="badge">0% complete</span></div>
            <?php if ($next_course): ?>
                <h3><?= htmlspecialchars($next_course['title']) ?></h3>
                <p class="muted"><?= htmlspecialchars($next_course['description']) ?></p>
                <?php $course_progress = $next_course['lesson_count'] > 0 ? (int) round(($next_course['completed_lessons'] / $next_course['lesson_count']) * 100) : 0; ?>
                <div class="progress-track" aria-label="Course progress"><div class="progress-value" style="width: <?= $course_progress ?>%;"></div></div>
                <p class="muted"><?= $course_progress ?>% complete</p>
                <div class="actions"><a class="button button-primary" href="course_detail.php?id=<?= (int) $next_course['id'] ?>">Continue course</a></div>
            <?php else: ?>
                <div class="empty-state"><strong>Your learning queue is empty.</strong> Explore the course catalog to get started.</div>
            <?php endif; ?>
        </section>
        <section class="panel">
            <h2>Recent activity</h2>
            <div class="empty-state"><strong>No activity yet.</strong>Your completed lessons and quiz attempts will appear here.</div>
        </section>
        <section class="panel">
            <div class="section-heading"><h2>Recommended practice</h2><a href="practice.php">View all</a></div>
            <?php if ($recommended_problems): ?>
                <?php foreach ($recommended_problems as $problem): ?>
                    <p><a href="problem.php?id=<?= (int) $problem['id'] ?>"><?= htmlspecialchars($problem['title']) ?></a>
                        <span class="badge"><?= htmlspecialchars($problem['topic']) ?> · <?= htmlspecialchars($problem['difficulty']) ?></span></p>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state"><strong>Build your practice history.</strong> Complete a quiz or attempt a problem to receive topic-based suggestions.</div>
            <?php endif; ?>
        </section>
    </div>
    <aside>
        <section class="panel">
            <h2>Daily challenge</h2>
            <p class="muted">Answer one focused question today to earn XP and keep your streak moving.</p>
            <a class="button button-secondary" href="daily_challenge.php">Take today's challenge</a>
        </section>
        <section class="panel">
            <h2>Recommended topics</h2>
            <?php if ($weak_topics): ?>
                <p class="muted">These topics have the most incorrect answers in your recent learning activity.</p>
                <?php foreach ($weak_topics as $topic): ?>
                    <p><span class="badge"><?= htmlspecialchars($topic['name']) ?></span> <span class="muted"><?= (int) $topic['misses'] ?> review opportunities</span></p>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state"><strong>Recommendations are being prepared.</strong> Complete a quiz or attempt a problem to personalize this space.</div>
            <?php endif; ?>
            <a class="button button-secondary" href="leaderboard.php">View leaderboard</a>
        </section>
    </aside>
</div>
<?php render_footer(); ?>
