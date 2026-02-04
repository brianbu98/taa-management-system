<?php
/* ======================================================
   🔐 DEV SESSION HANDLER
   ====================================================== */

ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
ini_set('session.cookie_secure', $isHttps ? 1 : 0);

/*
  IMPORTANT:
  - Do NOT force cookie domain
  - Do NOT force cookie path
*/

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('TAA_DEV_SESSION');
    session_start();
}
session_start();

}
