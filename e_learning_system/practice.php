<?php
include __DIR__ . '/auth.php';
start_secure_session();
include __DIR__ . '/db_config.php';
include __DIR__ . '/layout.php';

$topic = trim($_GET['topic'] ?? '');
$difficulty = $_GET['difficulty'] ?? '';
$sql = "SELECT p.id, p.title, p.difficulty, p.statement, t.name AS topic_name
        FROM practice_problems p
        JOIN practice_topics t ON t.id = p.topic_id
        WHERE 1=1";
$types = '';
$params = [];
if ($topic !== '') {
    $sql .= " AND t.slug = ?";
    $types .= 's';
    $params[] = $topic;
}
if (in_array($difficulty, ['Easy', 'Medium', 'Hard'], true)) {
    $sql .= " AND p.difficulty = ?";
    $types .= 's';
    $params[] = $difficulty;
}
$sql .= " ORDER BY p.id";
$stmt = $conn->prepare($sql);
if ($types === 's') {
    $stmt->bind_param('s', $params[0]);
} elseif ($types === 'ss') {
    $stmt->bind_param('ss', $params[0], $params[1]);
}
$stmt->execute();
$problems = $stmt->get_result();
$topics = $conn->query("SELECT name, slug FROM practice_topics ORDER BY name");

render_header('Practice problems', isset($_SESSION['user_id']) ? 'problems' : '');
?>
<div class="page-heading">
    <span class="eyebrow">Practice problems</span>
    <h1>Build problem-solving confidence.</h1>
    <p>Practice by topic and difficulty. Code execution is intentionally disabled for safety.</p>
</div>
<form class="panel" method="get" style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px;">
    <label>Topic
        <select name="topic">
            <option value="">All topics</option>
            <?php while ($item = $topics->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($item['slug']) ?>" <?= $topic === $item['slug'] ? 'selected' : '' ?>><?= htmlspecialchars($item['name']) ?></option>
            <?php endwhile; ?>
        </select>
    </label>
    <label>Difficulty
        <select name="difficulty">
            <option value="">All levels</option>
            <?php foreach (['Easy', 'Medium', 'Hard'] as $level): ?>
                <option value="<?= $level ?>" <?= $difficulty === $level ? 'selected' : '' ?>><?= $level ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <button class="button button-primary" type="submit">Filter problems</button>
</form>
<div class="course-grid">
    <?php if ($problems->num_rows === 0): ?>
        <div class="empty-state"><strong>No problems found.</strong> Try a different topic or difficulty.</div>
    <?php else: ?>
        <?php while ($problem = $problems->fetch_assoc()): ?>
            <article class="course-card">
                <div class="course-card-body">
                    <span class="badge"><?= htmlspecialchars($problem['topic_name']) ?> · <?= htmlspecialchars($problem['difficulty']) ?></span>
                    <h3><?= htmlspecialchars($problem['title']) ?></h3>
                    <p><?= htmlspecialchars($problem['statement']) ?></p>
                    <?php $problem_url = 'problem.php?id=' . (int) $problem['id']; ?>
                    <a class="button button-secondary" href="<?= htmlspecialchars(isset($_SESSION['user_id']) ? $problem_url : '#') ?>" <?= isset($_SESSION['user_id']) ? '' : 'data-auth-open data-return-to="' . htmlspecialchars($problem_url) . '"' ?>>Solve problem</a>
                </div>
            </article>
        <?php endwhile; ?>
    <?php endif; ?>
</div>
<?php render_footer(); ?>
