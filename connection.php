<?php
$servername = "localhost";
$username   = "u286236256_u123456_admin";
$password   = "4~ySBY~g";
$dbname     = "u286236256_u123456_taaapp";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
