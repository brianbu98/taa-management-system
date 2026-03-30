<?php
/* ======================================================
   🔐 CENTRAL SESSION HANDLER (ENV-AWARE & SAFE)
   ====================================================== */

/* ======================================================
   DETECT ENVIRONMENT
   ====================================================== */
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
    $env = 'development';
} elseif (strpos($host, 'test') !== false) {
    $env = 'testing';
} else {
    $env = 'production';
}

/* ======================================================
   DETECT HTTPS
   ====================================================== */
$is_https = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    ($_SERVER['SERVER_PORT'] ?? null) == 443
);

/* ======================================================
   SESSION COOKIE SETTINGS
   ====================================================== */
ini_set('session.cookie_path', '/');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

switch ($env) {

    case 'production':
        ini_set('session.cookie_domain', '.taa-app.com');
        ini_set('session.cookie_secure', 1);
        break;

    case 'testing':
        // Use empty unless you explicitly want cross-subdomain sessions
        ini_set('session.cookie_domain', '');
        ini_set('session.cookie_secure', $is_https ? 1 : 0);
        break;

    case 'development':
    default:
        ini_set('session.cookie_domain', '');
        ini_set('session.cookie_secure', 0);
        break;
}

/* ======================================================
   START SESSION
   ====================================================== */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* ======================================================
   SESSION TIMEOUT SETTINGS
   ====================================================== */
$timeout_admin = 900;      // 15 minutes
$timeout_secretary = 1200; // 20 minutes
$timeout_resident = 1800;  // 30 minutes

/* ======================================================
   PREVENT REDIRECT LOOP (ALLOW LOGIN PAGE)
   ====================================================== */
$current_page = basename($_SERVER['PHP_SELF']);

/* ======================================================
   CHECK IF LOGGED IN
   ====================================================== */
if ($current_page !== 'login.php' && !isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit();
}

/* ======================================================
   SET TIMEOUT BASED ON ROLE
   ====================================================== */
$user_type = $_SESSION['user_type'] ?? 'resident';

switch ($user_type) {
    case 'admin':
        $timeout = $timeout_admin;
        break;
    case 'secretary':
        $timeout = $timeout_secretary;
        break;
    default:
        $timeout = $timeout_resident;
}

/* ======================================================
   CHECK SESSION EXPIRY
   ====================================================== */
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {

    // Clear session
    $_SESSION = [];

    // Destroy cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();

    header("Location: /login.php?expired=1");
    exit();
}

/* ======================================================
   UPDATE LAST ACTIVITY
   ====================================================== */
$_SESSION['LAST_ACTIVITY'] = time();
?>