<?php
include_once '../connection.php';
session_start();

echo "Step 1 - Connection OK<br>";

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'resident') {
    echo "Step 2 - Session check failed<br>";
    exit;
}

echo "Step 2 - Session OK<br>";

$user_id = $_SESSION['user_id'];

$sql_user = "SELECT * FROM users WHERE id = ?";
$stmt_user = $con->prepare($sql_user);

if (!$stmt_user) {
    die("Prepare failed: " . $con->error);
}

echo "Step 3 - Prepare OK<br>";

$stmt_user->bind_param('s',$user_id);
$stmt_user->execute();

echo "Step 4 - Execute OK<br>";

$result = $stmt_user->get_result();

if (!$result) {
    die("Get result failed");
}

echo "Step 5 - Result OK<br>";

$row_user = $result->fetch_assoc();

echo "Step 6 - Fetch OK<br>";

echo "DONE";