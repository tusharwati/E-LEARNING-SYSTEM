<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Validate course_id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<p class='error-msg'>Invalid Course ID.</p>");
}

$course_id = intval($_GET['id']);

// Fetch questions from the 'quizzes' table
$query = "SELECT * FROM quizzes WHERE course_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}

if (count($questions) == 0) {
    die("<p class='error-msg'>No quiz available for this course.</p>");
}

// Check answers when submitted
$score = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($questions as $q) {
        $q_id = $q['id'];
        if (isset($_POST["answer_$q_id"]) && strtolower(trim($_POST["answer_$q_id"])) == strtolower(trim($q['answer']))) {
            $score++;
        }
    }
}
?>

<html>
<head>
    <title>Quiz</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color:rgb(255, 244, 244);
        }
        /* Navbar */
        nav {
            background: rgba(254, 254, 254, 0.8);
            backdrop-filter: blur(10px);
            padding: 15px 30px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        }

        nav a{
            color:rgb(0, 0, 0);
            font-size: 1.6rem;
            font-weight: bold;
            text-decoration: none;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .container h2 {
            margin-bottom: 20px;
        }
        .question {
            margin-bottom: 20px;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            display: block;
            width: fit-content;
            margin: 30px auto 0;
            padding: 10px 20px;
            font-size: 18px;
            font-weight: bold;
            background: rgba(255, 245, 248, 0.96);
            color:rgb(0, 0, 0);
            
            transition: background 0.3s ease, transform 0.3s ease;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            
        }
        button:hover {
            background: #ddd;
            transform: scale(1.1);
        }
        .score {
            font-size: 1.2rem;
            color: green;
        }
        .error-msg {
            color: red;
            text-align: center;
            margin-top: 30px;
            font-weight: bold;
        }
        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: hsl(0, 0.00%, 100.00%);
            text-align: center;
            padding: 15px;
            color:rgb(0, 0, 0);
            font-weight: bold;
        }
    </style>
</head>
<body>

<nav>
    <a href="index.php">E-Learning System</a>
</nav>

<div class="container">
    <h2>Course Quiz</h2>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <h4>Your Score: <span class="score"><?= $score . " / " . count($questions); ?></span></h4>
        <a href="index.php#quiz_list.php">
            <button>Back to Course</button>
        </a>
    <?php else: ?>
        <form method="POST">
            <?php foreach ($questions as $index => $q): ?>
                <div class="question">
                    <strong><?= ($index + 1) . ". " . htmlspecialchars($q['question']); ?></strong>
                    <input type="text" name="answer_<?= $q['id']; ?>" required>
                </div>
            <?php endforeach; ?>
            <button type="submit">Submit Quiz</button>
        </form>
    <?php endif; ?>
</div>

<footer>
    &copy; 2025 E-Learning System | All Free Courses
</footer>

</body>
</html>
