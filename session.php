<?php
/* ======================================================
   🔐 TEST SESSION HANDLER
   ====================================================== */

// Security flags (safe for HTTPS)
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

ini_set('session.cookie_secure', $isHttps ? 1 : 0);

// IMPORTANT: do NOT force domain/path — let PHP isolate per folder

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('TAA_TEST_SESSION');
    session_start();
}
