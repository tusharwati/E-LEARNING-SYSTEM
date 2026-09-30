<?php
include __DIR__ . '/auth.php';
start_secure_session();
include __DIR__ . '/db_config.php';
require_login('index.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

require_valid_csrf();
$course_id = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);
if (!$course_id || $course_id < 1) {
    http_response_code(400);
    exit('Invalid course.');
}

$course_stmt = $conn->prepare("SELECT id FROM courses WHERE id = ?");
$course_stmt->bind_param("i", $course_id);
$course_stmt->execute();
if ($course_stmt->get_result()->num_rows === 0) {
    http_response_code(404);
    exit('Course not found.');
}

$stmt = $conn->prepare(
    "INSERT INTO course_enrollments (user_id, course_id) VALUES (?, ?)
     ON DUPLICATE KEY UPDATE enrolled_at = enrolled_at"
);
$stmt->bind_param("ii", $_SESSION['user_id'], $course_id);
$stmt->execute();

header('Location: course_detail.php?id=' . $course_id);
exit;
