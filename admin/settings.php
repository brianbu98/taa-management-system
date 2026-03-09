<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

include_once '../connection.php';

$sql = "SELECT * FROM taa_information LIMIT 1";

$result = $con->query($sql);

$row = $result->fetch_assoc();

print_r($row);