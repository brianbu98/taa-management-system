<?php
require_once __DIR__ . '/../connection.php';
require_once __DIR__ . '/../session.php';

/* OPTIONAL: LOG ACTIVITY */
if (isset($_SESSION['user_id'], $_SESSION['user_type'])) {

    $user_type_log = match ($_SESSION['user_type']) {
        'admin' => 'ADMIN',
        'secretary' => 'OFFICIAL',
        default => 'RESIDENT'
    };

    $stmt = $con->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    $message = $user_type_log . ': ' .
        ($row['first_name'] ?? '') . ' ' .
        ($row['last_name'] ?? '') . ' | LOGOUT';

    $date = date("j-n-Y g:i A");
    $status = 'logout';

    $log = $con->prepare(
        "INSERT INTO activity_log (`message`, `date`, `status`) VALUES (?, ?, ?)"
    );
    $log->bind_param('sss', $message, $date, $status);
    $log->execute();
}

/* 🔥 HARD LOGOUT */
$_SESSION = [];
session_unset();
session_destroy();

/* 🔥 HARD COOKIE KILL (THIS IS THE FIX) */
setcookie(session_name(), '', time() - 42000, '/');
setcookie(session_name(), '', time() - 42000, '/test');

session_write_close();

/* REDIRECT */
header("Location: /test/login.php");
exit;
