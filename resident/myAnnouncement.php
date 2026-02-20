<?php
include_once '../connection.php';

echo "Before connection check<br>";

if (!$con) {
    die("Connection failed");
}

echo "Connection OK";