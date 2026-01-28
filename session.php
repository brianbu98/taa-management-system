<?php
/* ======================================================
   🔐 CENTRAL SESSION HANDLER (PROD FIX)
   ====================================================== */

// IMPORTANT: allow session across www / non-www
ini_set('session.cookie_domain', '.taa-app.com');
ini_set('session.cookie_path', '/');
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

// set to 1 ONLY if HTTPS is enforced
ini_set('session.cookie_secure', 0);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
