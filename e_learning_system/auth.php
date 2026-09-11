<?php

function start_secure_session()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
    ]);
    session_start();
}

function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token)
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function require_valid_csrf()
{
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        http_response_code(419);
        exit('Your session has expired. Please go back and try again.');
    }
}

function validate_password($password)
{
    return is_string($password)
        && strlen($password) >= 8
        && preg_match('/[a-z]/', $password)
        && preg_match('/[A-Z]/', $password)
        && preg_match('/\d/', $password);
}

function safe_return_to($value, $fallback = 'index.php')
{
    if (!is_string($value) || $value === '' || $value[0] === '/' && substr($value, 0, 2) !== '//') {
        return $fallback;
    }

    $parsed = parse_url($value);
    if ($parsed === false || isset($parsed['scheme']) || isset($parsed['host'])) {
        return $fallback;
    }

    return ltrim($value, '/');
}

function require_login($return_to = null)
{
    if (isset($_SESSION['user_id'])) {
        return;
    }

    $return_to = safe_return_to($return_to ?: $_SERVER['REQUEST_URI']);
    $_SESSION['auth_message'] = 'Create an account to continue learning.';
    header('Location: login.php?return_to=' . rawurlencode($return_to));
    exit;
}

function protected_url($path)
{
    return 'login.php?return_to=' . rawurlencode(safe_return_to($path));
}

function is_admin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
