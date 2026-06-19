<?php

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/connection.php';

$userId = $_SESSION['user_id'] ?? null;
$userType = $_SESSION['user_type'] ?? null;

if ($userType === 'admin') {
    $user_type_log = 'ADMIN';
} elseif ($userType === 'secretary') {
    $user_type_log = 'OFFICIAL';
} else {
    $user_type_log = 'RESIDENT';
}

$first_name = '';
$last_name = '';

if ($userId) {
    $sql_user = "SELECT first_name, last_name FROM users WHERE id = ?";
    $stmt_user = $con->prepare($sql_user);

    if ($stmt_user) {
        $stmt_user->bind_param('s', $userId);
        $stmt_user->execute();

        $result_user = $stmt_user->get_result();
        $row_user = $result_user->fetch_assoc();

        if ($row_user) {
            $first_name = $row_user['first_name'];
            $last_name = $row_user['last_name'];
        }

        $stmt_user->close();
    }
}

$status_activity_log = 'logout';
$date_activity = date("j-n-Y g:i A");
$message = trim($user_type_log . ': ' . $first_name . ' ' . $last_name . ' | LOGOUT');

$sql_system_logs = "INSERT INTO activity_log (`message`, `date`, `status`) VALUES (?, ?, ?)";
$query_system_logs = $con->prepare($sql_system_logs);

if ($query_system_logs) {
    $query_system_logs->bind_param('sss', $message, $date_activity, $status_activity_log);
    $query_system_logs->execute();
    $query_system_logs->close();
}

unset($_SESSION['user_id']);
unset($_SESSION['user_type']);
$_SESSION = array();
session_unset();
session_destroy();

header('Location: ' . $base_path . '/');
exit;
?>