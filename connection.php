<?php

/* ======================================================
   🌍 ENVIRONMENT SETUP
   Change this ONLY when deploying
   ====================================================== */
define('ENV', 'dev'); // dev | test | prod


/* ======================================================
   🔌 DATABASE CONFIG
   ====================================================== */
$host = "localhost";

switch (ENV) {

    case 'dev':
        $db   = "u286236256_taaapp_dev";
        $user = "u286236256_taaapp_dev";
        $pass = "!1slk7j1Q";
        break;

    case 'test':
        $db   = "u286236256_taaapp_test";
        $user = "u286236256_taaapp_test";
        $pass = "MDXzaEN~v8";
        break;

    case 'prod':
    default:
        $db   = "u286236256_u123456_taaapp";
        $user = "u286236256_u123456_admin";
        $pass = "4~ySBY~g";
        break;
}


/* ======================================================
   🔗 CONNECT
   ====================================================== */
$con = new mysqli($host, $user, $pass, $db);

if ($con->connect_error) {
    die("❌ DB Connection failed: " . $con->connect_error);
}


/* ======================================================
   ⚙️ OPTIONAL SETTINGS (recommended)
   ====================================================== */
$con->set_charset("utf8mb4");

// Debug mode (only for dev)
if (ENV === 'dev') {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
}