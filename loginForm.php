<?php
require_once __DIR__ . '/connection.php';
session_start();

/**
 * loginForm.php
 * Handles AJAX login authentication ONLY
 * Returns: errorUsername | errorPassword | admin | secretary | resident
 */

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

// Get inputs safely
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validate empty fields
if ($username === '' || $password === '') {
    echo 'errorUsername';
    exit;
}

// Secure query (prepared statement)
$sql = "
    SELECT id, password, user_type
    FROM users
    WHERE username = ? OR resident_no = ?
    LIMIT 1
";

$stmt = $con->prepare($sql);
if (!$stmt) {
    echo 'errorUsername';
    exit;
}

$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

// User not found
if ($result->num_rows !== 1) {
    echo 'errorUsername';
    exit;
}

$user = $result->fetch_assoc();

// Password check
$isValidPassword = false;

// New hashed passwords
if (password_verify($password, $user['password'])) {
    $isValidPassword = true;
}

// Old MD5 passwords (backward compatibility)
if (!$isValidPassword && md5($password) === $user['password']) {
    $isValidPassword = true;
}

if (!$isValidPassword) {
    echo 'errorPassword';
    exit;
}

// LOGIN SUCCESS — SET SESSION
$_SESSION['user_id']   = $user['id'];
$_SESSION['user_type'] = $user['user_type'];

// Return role to AJAX
switch ($user['user_type']) {
    case 'admin':
        echo 'admin';
        break;

    case 'secretary':
        echo 'secretary';
        break;

    case 'resident':
        echo 'resident';
        break;

    default:
        echo 'errorUsername';
        break;
}

exit;
