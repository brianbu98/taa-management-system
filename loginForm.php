<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/userInfo.php';

ini_set('display_errors', 0);
error_reporting(0);

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $con->prepare(
  "SELECT id, username, password, user_type, first_name, last_name
   FROM users
   WHERE username = ? OR id = ?"
);
$stmt->bind_param('ss', $username, $username);
$stmt->execute();
$result = $stmt->get_result();

if (!$row = $result->fetch_assoc()) {
    exit('errorUsername');
}

/* ⚠️ Plain password comparison (OK for now) */
if ($password !== $row['password']) {
    exit('errorPassword');
}

session_set_cookie_params([
    'path' => dirname($_SERVER['SCRIPT_NAME']) . '/',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_set_cookie_params([
    'path' => dirname($_SERVER['SCRIPT_NAME']) . '/',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'httponly' => true,
    'samesite' => 'Lax'
]);


/* 🔐 Regenerate session ID FIRST */
session_regenerate_id(true);

/* ✅ SESSION */
$_SESSION['user_id']   = $row['id'];
$_SESSION['username'] = $row['username'];
$_SESSION['user_type'] = $row['user_type'] ?? 'resident';

/* 📝 ACTIVITY LOG */
date_default_timezone_set('Asia/Manila');
$date_activity = date("j-n-Y g:i A");
$status_activity_log = 'login';

$roleLabel = strtoupper($_SESSION['user_type']);
$message = $roleLabel . ': ' . $row['first_name'] . ' ' . $row['last_name'] . ' | LOGIN';

$sql_log = "INSERT INTO activity_log (`message`, `date`, `status`) VALUES (?,?,?)";
$stmt_log = $con->prepare($sql_log);
$stmt_log->bind_param('sss', $message, $date_activity, $status_activity_log);
$stmt_log->execute();
$stmt_log->close();

/* 🔥 OUTPUT MUST BE CLEAN */
exit($_SESSION['user_type']);
