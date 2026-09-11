<?php
include __DIR__ . '/auth.php';
start_secure_session();
include __DIR__ . '/db_config.php';
include __DIR__ . '/layout.php';

$query = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$sql = "SELECT DISTINCT courses.* FROM courses LEFT JOIN course_categories ON course_categories.course_id = courses.id LEFT JOIN categories ON categories.id = course_categories.category_id WHERE 1=1";
$types = '';
$params = [];
if ($query !== '') { $sql .= " AND (courses.title LIKE ? OR courses.description LIKE ?)"; $types .= 'ss'; $params[] = "%$query%"; $params[] = "%$query%"; }
if ($category !== '') { $sql .= " AND categories.slug = ?"; $types .= 's'; $params[] = $category; }
$sql .= " ORDER BY courses.id DESC";
$stmt = $conn->prepare($sql);
if ($types === 's') { $stmt->bind_param('s', $params[0]); }
elseif ($types === 'ss') { $stmt->bind_param('ss', $params[0], $params[1]); }
elseif ($types === 'sss') { $stmt->bind_param('sss', $params[0], $params[1], $params[2]); }
$stmt->execute();
$result = $stmt->get_result();
render_header('Courses', isset($_SESSION['user_id']) ? 'courses' : '');
?>
<div class="page-heading">
    <span class="eyebrow">Course catalog</span>
    <h1>Learn something useful today.</h1>
    <p>Browse the full learning library. Course previews are open to everyone.</p>
</div>
<form class="filter-bar" method="get">
    <label class="sr-only" for="course-query">Search the course catalog</label>
    <input id="course-query" type="search" name="q" value="<?= htmlspecialchars($query) ?>" placeholder="Search the course catalog">
    <button class="button button-primary" type="submit">Search</button>
</form>
<div class="course-grid">
    <?php if ($result->num_rows === 0): ?><div class="empty-state"><strong>No courses found.</strong> Try a different search.</div><?php endif; ?>
    <?php while ($course = $result->fetch_assoc()): ?>
        <article class="course-card">
            <img src="thumbnails/<?= htmlspecialchars(trim($course['thumbnail'] ?? 'default.jpg')) ?>" alt="<?= htmlspecialchars($course['title']) ?>">
            <div class="course-card-body">
                <h3><?= htmlspecialchars($course['title']) ?></h3>
                <p><?= htmlspecialchars($course['description']) ?></p>
                <a class="button button-secondary" href="course_detail.php?id=<?= (int) $course['id'] ?>">View course</a>
            </div>
        </article>
    <?php endwhile; ?>
</div>
<?php render_footer(); ?>
