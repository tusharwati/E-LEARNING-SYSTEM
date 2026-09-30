<?php
include __DIR__ . '/auth.php';
start_secure_session();
include __DIR__ . '/db_config.php';
include __DIR__ . '/layout.php';

$result = $conn->query("SELECT * FROM courses ORDER BY id DESC");
$category_result = $conn->query("SELECT name, slug FROM categories ORDER BY name");
render_header('Explore courses');
?>
<section class="hero">
    <div>
        <span class="eyebrow">Build skills that last</span>
        <h1>Learn with structure, practice with purpose.</h1>
        <p>Explore practical courses and learning resources designed to help you build confidence one lesson at a time.</p>
        <form class="hero-search" action="courses.php" method="get">
            <label class="sr-only" for="hero-search">Search courses, topics, and practice problems</label>
            <input id="hero-search" type="search" name="q" placeholder="Search courses, topics, and practice problems">
            <button class="button button-primary" type="submit">Search library</button>
        </form>
        <div class="actions"><a class="button button-secondary" href="#courses">Explore courses</a><a class="text-link" href="practice.php">Browse practice problems -&gt;</a></div>
    </div>
    <div class="hero-panel">
        <span class="badge">Open learning library</span>
        <strong><?= (int) $result->num_rows ?> courses</strong>
        <span class="muted">Start browsing freely. Create an account when you are ready to learn.</span>
    </div>
</section>

<section class="section-block">
    <div class="section-heading"><div><span class="eyebrow">Learn by direction</span><h2>Find a path that fits your goals</h2></div></div>
    <div class="category-grid">
        <?php while ($category = $category_result->fetch_assoc()): ?>
            <a class="category-card" href="courses.php?category=<?= urlencode($category['slug']) ?>"><span class="category-icon"><?= strtoupper(substr($category['name'], 0, 1)) ?></span><strong><?= htmlspecialchars($category['name']) ?></strong><span>Explore courses -&gt;</span></a>
        <?php endwhile; ?>
    </div>
</section>

<section id="courses">
    <div class="section-heading">
        <div>
            <span class="eyebrow">Course catalog</span>
            <h2>Choose your next topic</h2>
        </div>
        <a href="courses.php">View all</a>
    </div>
    <div class="course-grid">
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
</section>
<section class="feature-strip">
    <div><span class="eyebrow">A better way to learn</span><h2>Learn, practise, and track real progress.</h2><p>Build a steady routine with structured lessons, focused quizzes, and practice that helps you remember.</p></div>
    <div class="feature-points"><span><strong>01</strong> Structured learning paths</span><span><strong>02</strong> Practice with clear feedback</span><span><strong>03</strong> Progress that stays yours</span></div>
</section>
<?php render_footer(); ?>
