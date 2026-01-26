<?php
session_start();
require_once 'connection.php';
require_once 'userInfo.php';

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

/* ⚠️ You are using PLAIN password comparison */
if ($password !== $row['password']) {
  exit('errorPassword');
}

/* ✅ SESSION */
$_SESSION['user_id']   = $row['id'];
$_SESSION['username']  = $row['username'];
$_SESSION['user_type'] = $row['user_type'] ?? 'resident';

/* ✅ ACTIVITY LOG (SAFE, AFTER AUTH) */
date_default_timezone_set('Asia/Manila');
$date_activity = date("j-n-Y g:i A");
$status_activity_log = 'login';

$roleLabel = strtoupper($row['user_type'] ?? 'RESIDENT');
$message = $roleLabel . ': ' . $row['first_name'] . ' ' . $row['last_name'] . ' | LOGIN';

$sql_log = "INSERT INTO activity_log (`message`, `date`, `status`) VALUES (?,?,?)";
$stmt_log = $con->prepare($sql_log);
$stmt_log->bind_param('sss', $message, $date_activity, $status_activity_log);
$stmt_log->execute();
$stmt_log->close();

/* 🔥 IMPORTANT: CLEAN OUTPUT ONLY */
$userType = strtolower(trim($row['user_type'] ?? 'resident'));
exit($userType);

