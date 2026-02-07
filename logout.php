<?php
$NO_SESSION_START = true;
require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../connection.php';

/* Now manually start session so we can destroy it */
session_start();

/* Destroy session completely */
$_SESSION = [];
session_unset();
session_destroy();

/* Remove session cookie */
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

/* Redirect to login */
header("Location: /test/login.php");
exit;
