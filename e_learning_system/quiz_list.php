
<?php
session_start();
include 'db_config.php';

$query = "SELECT * FROM courses";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Let's quiz | E-Learning</title>
</head>
<body>
    <div class="content">
        <h2>Quiz Courses</h2>
        <div class="course-list">
            <?php while ($course = $result->fetch_assoc()): ?>
                <div class="course">
                    <h3><?= htmlspecialchars($course['title']) ?></h3>
                    <a href="quiz.php?id=<?= $course['id'] ?>">Start quiz</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
