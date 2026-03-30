<?php
include_once '../connection.php';
session_start();

if(!isset($_SESSION['user_id'])){
    echo "Unauthorized";
    exit;
}

$id = intval($_POST['id']);
$remark = trim($_POST['remark']);

$stmt = $con->prepare("
UPDATE residence_information 
SET report_remarks = ? 
WHERE id = ?
");

$stmt->bind_param("si", $remark, $id);

if($stmt->execute()){
    echo "Remark updated successfully!";
}else{
    echo "Error updating remark.";
}