<?php
session_start();
include 'db_config.php';

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$sql_user = "SELECT name FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

// Fetch free courses
$sql = "SELECT * FROM courses";
$result = $conn->query($sql);
?>

<html>
<head>
    <title>Dashboard | E-Learning</title>
    <link rel="stylesheet" href="index.css?v=<?php echo time(); ?>"> <!-- Custom CSS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div>
        <!-- Sidebar -->
        <nav>
            <h3 class="text-center">E-Learning</h3>
            <ul>
                <a class="nav-link" href="index.php" onclick="loadContent('home')">🏠︎ Home</a>
                <a class="nav-link" href="profile.php">👤 Profile</a>
                <a class="nav-link" href="#courses.php" onclick="loadContent('courses')">👨🏽‍💻 Courses</a>
                <a class="nav-link" href="#quiz_list.php" onclick="loadContent('quiz_list')">💭Quiz</a>
                <a class="nav-link" href="login.php">↪ Logout</a>
            </ul>
        </nav>
        
        <!-- Main Content -->
<div class="main-content" id="content">
    <div class="welcome-box">
        <h2>👋🏻 Welcome, <?= htmlspecialchars($user['name']) ?>!</h2>
        <p>All courses are 100% free. 🚀 Start learning and level up your skills today!</p>
    </div>

    <div class="feature-cards">
        <div class="card" onclick="loadContent('courses')">
            <h3>👨🏽‍💻 Courses</h3>
            <p>Browse all available learning resources</p>
        </div>
        <div class="card" onclick="loadContent('quiz_list')">
            <h3>📝 Quiz</h3>
            <p>Test your knowledge with exciting quizzes</p>
        </div>
        <div class="card" onclick="window.location.href='profile.php'">
            <h3>👤 Profile</h3>
            <p>Update your personal information</p>
        </div>
    </div>
</div>

    </div>

    <script>
        function loadContent(page) {
            $.get(page + ".php", function(data) {
                $("#content").html(data);
            });
        }
    </script>
</body>
</html> 