<?php
require_once __DIR__ . '/session.php';

/* ======================================================
   🔐 CENTRAL SESSION HANDLER (HTTPS SAFE)
   ====================================================== */

// IMPORTANT: HTTPS requires secure cookies
ini_set('session.cookie_domain', '.taa-app.com');
ini_set('session.cookie_path', '/');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_secure', 1); // 🔥 MUST be 1 on HTTPS

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('TAA_DEV_SESSION');
    session_start();

}
