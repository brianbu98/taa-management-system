<?php

/* ======================================================
   🔌 DATABASE CONFIG
   ====================================================== */
$host = "localhost";
$path = $_SERVER['REQUEST_URI'] ?? '';

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
