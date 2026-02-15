<?php
include 'db_config.php';

if (!isset($_GET['id'])) {
    echo "Invalid Course ID";
    exit;
}

$course_id = intval($_GET['id']);
$sql = "SELECT * FROM courses WHERE id = $course_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Course not found.";
    exit;
}

$course = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($course['title']); ?> | Course Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        /* Reset and Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg,hsl(245, 100.00%, 89.20%),rgb(6, 97, 255));
            color: #fff;
            padding-bottom: 80px;
            animation: fadeIn 1s ease-in-out;
        }

        /* Navbar */
        nav {
            background: rgba(254, 254, 254, 0.8);
            backdrop-filter: blur(10px);
            padding: 15px 30px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        }

        nav .navbar-brand {
            color:rgb(0, 0, 0);
            font-size: 1.6rem;
            font-weight: bold;
            text-decoration: none;
        }

        /* Container */
        .container {
            max-width: 900px;
            margin: 60px auto;
            padding: 40px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.3);
            animation: slideIn 1s ease-in-out;
        }

        h2, h4 {
            text-align: center;
            font-weight: bold;
            color: #ffcc00;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
            line-height: 1.6;
            text-align: center;
            margin-bottom: 40px;
        }

        /* Lessons List */
        .list-group {
            list-style: none;
            padding: 0;
            margin-top: 20px;
        }

        .list-group-item {
            background: rgba(255, 255, 255, 0.2);
            margin-bottom: 12px;
            border-radius: 10px;
            transition: transform 0.3s ease, background 0.3s ease;
        }

        .list-group-item:hover {
            background: rgba(255, 255, 255, 0.4);
            transform: scale(1.05);
        }

        .list-group-item a {
            display: block;
            padding: 15px 20px;
            text-decoration: none;
            color:rgb(187, 254, 255);
            font-weight: 600;
            font-size: 17px;
            transition: color 0.3s ease;
        }

        .list-group-item a:hover {
            color:rgb(0, 251, 255);
        }

        /* Back Button */
        .btn-secondary {
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

        .btn-secondary:hover {
            background: #ddd;
            transform: scale(1.1);
        }

        /* Footer */
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

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav>
    <a href="index.php" class="navbar-brand">E-Learning System</a>
</nav>

<!-- Course Details -->
<div class="container">
    <h2><?= htmlspecialchars($course['title']); ?></h2>
    <p><?= htmlspecialchars($course['description']); ?></p>

    <h4>Course Lessons</h4>
    <ul class="list-group">
        <?php
        $lesson_sql = "SELECT * FROM lessons WHERE course_id = $course_id";
        $lesson_result = $conn->query($lesson_sql);
        
        if ($lesson_result->num_rows > 0) {
            while ($lesson = $lesson_result->fetch_assoc()) {
                echo '<li class="list-group-item">';
                echo '<a href="lesson.php?id=' . $lesson['id'] . '">' . htmlspecialchars($lesson['title']) . '</a>';
                echo '</li>';
            }
        } else {
            echo '<li class="list-group-item"><a>No lessons available.</a></li>';
        }
        ?>
    </ul>

    <a href="index.php#courses.php" class="btn-secondary">← Back to Course</a>
</div>

<!-- Footer -->
<footer>
    &copy; 2025 E-Learning System | All Free Courses
</footer>

</body>
</html>
