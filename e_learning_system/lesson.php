<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid Lesson ID.";
    exit;
}

$lesson_id = intval($_GET['id']);

$lesson_sql = "SELECT lessons.*, courses.title AS course_title 
               FROM lessons 
               JOIN courses ON lessons.course_id = courses.id 
               WHERE lessons.id = ?";
$stmt = $conn->prepare($lesson_sql);
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$lesson_result = $stmt->get_result();

if ($lesson_result->num_rows == 0) {
    echo "Lesson not found.";
    exit;
}

$lesson = $lesson_result->fetch_assoc();
$note_file_path = $lesson['file_path'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($lesson['title']); ?> | Lesson</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Global Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #eef2f3, #8e9eab);
            margin: 0;
            padding: 0;
            color: #333;
        }

        nav {
            background: #1f2937;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background: #ffffffdd;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            animation: fadeIn 1s ease-out;
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        p {
            color: #666;
        }

        iframe.video-frame {
            width: 100%;
            height: 500px;
            border: none;
            border-radius: 10px;
            margin: 20px 0;
        }

        .lesson-notes {
            padding: 20px;
            border-left: 5px solid #0077cc;
            background: #f0f8ff;
            border-radius: 10px;
        }

        .pdf-viewer {
            width: 100%;
            height: 600px;
            border: 2px solid #ccc;
            border-radius: 8px;
            margin-top: 20px;
        }

        .btn {
            display: block;
            width: fit-content;
            margin: 30px auto 0;
            padding: 10px 20px;
            font-size: 18px;
            font-weight: bold;
            background: #fff;
            color: #333;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .btn:hover {
            background: #ddd;
            transform: scale(1.1);
        }

        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: hsl(0, 0.00%, 100.00%);
            text-align: center;
            padding: 15px;
            color: #000;
            font-weight: bold;
        }

        @keyframes fadeIn {
            0% {opacity: 0; transform: translateY(20px);}
            100% {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>

<nav>
    <a href="index.php">📖 E-Learning System</a>
</nav>

<div class="container">
    <h2><?= htmlspecialchars($lesson['title']); ?></h2>
    <p>Part of: <strong><?= htmlspecialchars($lesson['course_title']); ?></strong></p>

    <iframe class="video-frame" src="<?= htmlspecialchars($lesson['video_url']); ?>" allowfullscreen></iframe>

    <div class="lesson-notes">
        <h3>📄 Lesson Notes</h3>

        <?php if (!empty($note_file_path) && file_exists($note_file_path)): ?>
            <a href="<?= htmlspecialchars($note_file_path); ?>" class="btn" download>📥 Download PDF</a>
            <iframe class="pdf-viewer" src="<?= htmlspecialchars($note_file_path); ?>"></iframe>
        <?php else: ?>
            <p style="color: #999;">No notes available for this lesson.</p>
        <?php endif; ?>
    </div>

    <a href="course_detail.php?id=<?= $lesson['course_id']; ?>" class="btn">← Back to Course</a>
</div>

<footer>
    &copy; 2025 E-Learning System | All Free Courses
</footer>

</body>
</html>
