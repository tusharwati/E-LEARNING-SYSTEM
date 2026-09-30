<?php
include __DIR__ . '/auth.php';
start_secure_session();
include __DIR__ . '/db_config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Request method not allowed.']);
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    http_response_code(419);
    echo json_encode(['success' => false, 'message' => 'Your session expired. Refresh the page and try again.']);
    exit;
}

$mode = $_POST['mode'] ?? '';
$return_to = safe_return_to($_POST['return_to'] ?? 'index.php');

if ($mode === 'login') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Enter a valid email and password.']);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $valid = $row && password_verify($password, $row['password']);
    $legacy = $row && !$valid && !password_get_info($row['password'])['algo']
        && hash_equals($row['password'], $password);
    if (!$valid && !$legacy) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        exit;
    }
    if ($legacy) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $upgrade = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $upgrade->bind_param("si", $hash, $row['id']);
        $upgrade->execute();
    }
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $row['id'];
    $_SESSION['role'] = $row['role'] ?? 'student';
    echo json_encode(['success' => true, 'redirect' => $return_to]);
    exit;
}

if ($mode === 'signup') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['signup_email'] ?? $_POST['email'] ?? '');
    $password = $_POST['signup_password'] ?? $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    if ($name === '' || strlen($name) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Enter a valid name and email address.']);
        exit;
    }
    if (!validate_password($password)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters and include uppercase, lowercase, and a number.']);
        exit;
    }
    if ($password !== $confirm_password) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
        exit;
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hash);
    if (!$stmt->execute()) {
        http_response_code($stmt->errno === 1062 ? 409 : 500);
        echo json_encode([
            'success' => false,
            'message' => $stmt->errno === 1062
                ? 'An account with that email already exists.'
                : 'Unable to create the account. Please try again.',
        ]);
        exit;
    }
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $conn->insert_id;
    $_SESSION['role'] = 'student';
    echo json_encode(['success' => true, 'redirect' => $return_to]);
    exit;
}

http_response_code(422);
echo json_encode(['success' => false, 'message' => 'Choose a valid authentication action.']);
