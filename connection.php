<?php
/* ======================================================
   🔐 SESSION CONFIG — MUST BE FIRST (BEFORE ANY OUTPUT)
   ====================================================== */
ini_set('session.cookie_path', '/');
ini_set('session.cookie_domain', '');   // same domain (taa-app.com)
ini_set('session.cookie_secure', 0);    // set to 1 if HTTPS-only
ini_set('session.cookie_httponly', 1);

if (session_status() === PHP_SESSION_NONE) {
  
}

/* ======================================================
   🔌 DATABASE CONFIG
   ====================================================== */
$host = "localhost";
$path = $_SERVER['REQUEST_URI'];

// DEFAULT = MAIN
$db   = "u286236256_u123456_taaapp";
$user = "u286236256_u123456_admin";
$pass = "4~ySBY~g";

// DEV
if (strpos($path, "/dev/") !== false) {
    $db   = "u286236256_taaapp_dev";
    $user = "u286236256_taaapp_dev";
    $pass = "!1slk7j1Q";
}

// TEST
if (strpos($path, "/test/") !== false) {
    $db   = "u286236256_taaapp_test";
    $user = "u286236256_taaapp_test";
    $pass = "MDXzaEN~v8";
}

/* ======================================================
   🔗 CONNECT
   ====================================================== */
$con = new mysqli($host, $user, $pass, $db);

if ($con->connect_error) {
    die("DB Connection failed");
}
