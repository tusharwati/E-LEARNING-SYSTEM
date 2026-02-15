<?php
session_start();
include 'db_config.php';

$query = "SELECT * FROM courses";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Courses | E-Learning</title>
</head>
<body>
    <div class="content">
        <h2>Available Free Courses</h2>
        <div class="course-list">
            <?php while ($course = $result->fetch_assoc()): ?>
                <div class="course">
                    <img src="thumbnails/<?= htmlspecialchars($course['thumbnail'] ?? 'default.jpg') ?>" alt="Course Thumbnail" width="200">
                    <h3><?= htmlspecialchars($course['title']) ?></h3>
                    <p><?= htmlspecialchars($course['description']) ?></p>
                    <a href="course_detail.php?id=<?= $course['id'] ?>">Start Learning</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>