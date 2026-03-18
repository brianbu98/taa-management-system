<?php
/* ======================================================
   🔐 CENTRAL SESSION HANDLER (HTTPS SAFE)
   ====================================================== */

// IMPORTANT: HTTPS requires secure cookies
ini_set('session.cookie_domain', '.taa-app.com');
ini_set('session.cookie_path', '/');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_secure', 1);

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
   CHECK IF LOGGED IN
   ====================================================== */
if (!isset($_SESSION['user_id'])) {
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

/* =====================================================
    CHECK SESSION EXPIRY
   ====================================================== */
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {

    // destroy session safely
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
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