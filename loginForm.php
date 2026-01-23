<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/userInfo.php';

/* Validate input */
if (empty($_POST['username']) || empty($_POST['password'])) {
    exit('errorUsername');
}

$username = trim($_POST['username']);
$password = $_POST['password'];

try {

    $sql = "SELECT id, username, password, user_type, first_name, middle_name, last_name
            FROM users
            WHERE username = ? OR id = ?";

    $stmt = $con->prepare($sql);
    $stmt->bind_param('ss', $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        exit('errorUsername');
    }

    $row = $result->fetch_assoc();

    /* VERIFY PASSWORD (THIS IS THE BIG FIX) */
    if (!password_verify($password, $row['password'])) {
        exit('errorPassword');
    }

    /* SESSION */
    $_SESSION['user_id']   = $row['id'];
    $_SESSION['username']  = $row['username'];
    $_SESSION['user_type'] = $row['user_type'];

    /* ACTIVITY LOG */
    date_default_timezone_set('Asia/Manila');
    $date_activity = date("j-n-Y g:i A");

    $role = strtoupper($row['user_type']);
    $message = "{$role}: {$row['first_name']} {$row['last_name']} | LOGIN";
    $status = 'login';

    $log = $con->prepare(
        "INSERT INTO activity_log (message, date, status) VALUES (?, ?, ?)"
    );
    $log->bind_param('sss', $message, $date_activity, $status);
    $log->execute();

    /* RESPONSE TO AJAX */
    switch ($row['user_type']) {
        case 'admin':
            exit('admin');
        case 'secretary':
            exit('secretary');
        default:
            exit('resident');
    }

} catch (Exception $e) {
    http_response_code(500);
    exit('serverError');
}
