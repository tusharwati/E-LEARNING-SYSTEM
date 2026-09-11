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
$lesson_id = filter_input(INPUT_POST, 'lesson_id', FILTER_VALIDATE_INT);
if (!$lesson_id || $lesson_id < 1) {
    http_response_code(400);
    exit('Invalid lesson.');
}

$lesson_stmt = $conn->prepare("SELECT id, course_id FROM lessons WHERE id = ?");
$lesson_stmt->bind_param("i", $lesson_id);
$lesson_stmt->execute();
$lesson = $lesson_stmt->get_result()->fetch_assoc();
if (!$lesson) {
    http_response_code(404);
    exit('Lesson not found.');
}

$stmt = $conn->prepare(
    "INSERT INTO lesson_progress (user_id, lesson_id, completed_at)
     VALUES (?, ?, CURRENT_TIMESTAMP)
     ON DUPLICATE KEY UPDATE completed_at = CURRENT_TIMESTAMP"
);
$stmt->bind_param("ii", $_SESSION['user_id'], $lesson_id);
$stmt->execute();

header('Location: lesson.php?id=' . $lesson_id);
exit;
