<?php
$servername = "localhost";
$username   = "u286236256_u123456_admin";
$password   = "4~ySBY~g";
$dbname     = "u286236256_u123456_taaapp";

$con = mysqli_connect($servername, $username, $password, $dbname);

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

