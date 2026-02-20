<?php
include_once '../connection.php';
session_start();

echo "Step 1 - Connection OK<br>";

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'resident') {
    echo "Session failed";
    exit;
}

echo "Step 2 - Session OK<br>";

$user_id = $_SESSION['user_id'];

$sql_user = "SELECT * FROM users WHERE id = ?";
$stmt_user = $con->prepare($sql_user);
$stmt_user->bind_param('s',$user_id);
$stmt_user->execute();
$row_user = $stmt_user->get_result()->fetch_assoc();

echo "Step 3 - User Query OK<br>";

// ---- TAA QUERY ----
$sql_taa = "SELECT * FROM taa_information LIMIT 1";
$res_taa = $con->query($sql_taa);

if (!$res_taa) {
    die("TAA Query failed: " . $con->error);
}

$row_taa = $res_taa->fetch_assoc();

echo "Step 4 - TAA Query OK<br>";

// ---- ANNOUNCEMENT QUERY ----
$ann_query = $con->query("SELECT * FROM announcements WHERE status='active' ORDER BY created_at DESC");

if (!$ann_query) {
    die("Announcement Query failed: " . $con->error);
}

echo "Step 5 - Announcement Query OK<br>";

echo "ALL GOOD";