<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/userInfo.php';

/* 🔧 ENABLE THIS TEMPORARILY IF STILL FAILING */
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    exit('errorUsername');
}

/* 🔍 PREPARE QUERY */
$stmt = $con->prepare(
  "SELECT id, username, password, user_type, first_name, last_name
   FROM users
   WHERE username = ? OR id = ?
   LIMIT 1"
);

if (!$stmt) {
    exit('errorUsername');
}

$stmt->bind_param('ss', $username, $username);
$stmt->execute();
$result = $stmt->get_result();

/* ❌ USER NOT FOUND */
if ($result->num_rows === 0) {
    exit('errorUsername');
}

$row = $result->fetch_assoc();

/* 🔐 PASSWORD CHECK */

/* ✅ USE THIS IF PASSWORDS ARE HASHED */
if (strlen($row['password']) > 20) {
    if (!password_verify($password, $row['password'])) {
        exit('errorPassword');
    }
} else {
    /* ✅ FALLBACK: PLAIN TEXT */
    if ($password !== $row['password']) {
        exit('errorPassword');
    }
}

/* 🔐 REGENERATE SESSION */
session_regenerate_id(true);

/* ✅ SESSION */
$_SESSION['user_id']   = $row['id'];
$_SESSION['username']  = $row['username'];
$_SESSION['user_type'] = strtolower(trim($row['user_type'] ?? 'resident'));

/* 📝 ACTIVITY LOG */
date_default_timezone_set('Asia/Manila');

$date_activity = date("Y-m-d H:i:s"); // safer format
$status_activity_log = 'login';

$roleLabel = strtoupper($_SESSION['user_type']);
$message = $roleLabel . ': ' . $row['first_name'] . ' ' . $row['last_name'] . ' | LOGIN';

$sql_log = "INSERT INTO activity_log (`message`, `date`, `status`) VALUES (?,?,?)";
$stmt_log = $con->prepare($sql_log);

if ($stmt_log) {
    $stmt_log->bind_param('sss', $message, $date_activity, $status_activity_log);
    $stmt_log->execute();
    $stmt_log->close();
}

/* 🔥 FINAL OUTPUT (STRICT) */
echo $_SESSION['user_type'];
exit;