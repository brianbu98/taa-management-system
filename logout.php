<?php
require_once __DIR__ . '/../session.php';

$_SESSION = [];
session_unset();
session_destroy();

if (ini_get('session.use_cookies')) {
    setcookie(session_name(), '', time() - 42000, '/');
}

header("Location: /test/login.php");
exit;
