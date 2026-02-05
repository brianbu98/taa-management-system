<?php
require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../connection.php';

/* 🔥 DESTROY SESSION FIRST */
$_SESSION = [];
session_unset();
session_destroy();

/* 🔥 REMOVE COOKIE */
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

/* 🔥 FORCE HARD REDIRECT */
header("Location: /test/login.php", true, 302);
exit;
