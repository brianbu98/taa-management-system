<?php 

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once '../connection.php';
session_start();

try{

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin'){
    exit('unauthorized');
}

$user_id = $_SESSION['user_id'];

$sql_user = "SELECT * FROM users WHERE id = ?";
$stmt_user = $con->prepare($sql_user);
$stmt_user->bind_param('s',$user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$row_user = $result_user->fetch_assoc();

$first_name_user = $row_user['first_name'];
$last_name_user  = $row_user['last_name'];


/* INPUTS */
$add_position = intval($_POST['add_position'] ?? 0);
$add_first_name = $_POST['add_first_name'] ?? '';
$add_middle_name = $_POST['add_middle_name'] ?? '';
$add_last_name = $_POST['add_last_name'] ?? '';
$add_suffix = $_POST['add_suffix'] ?? '';
$add_gender = $_POST['add_gender'] ?? '';
$add_civil_status = $_POST['add_civil_status'] ?? '';
$add_religion = $_POST['add_religion'] ?? '';
$add_nationality = $_POST['add_nationality'] ?? '';
$add_contact_number = $_POST['add_contact_number'] ?? '';
$add_email_address = $_POST['add_email_address'] ?? '';
$add_address = $_POST['add_address'] ?? '';
$add_birth_date = $_POST['add_birth_date'] ?? '';
$add_birth_place = $_POST['add_birth_place'] ?? '';
$add_province = $_POST['add_province'] ?? '';
$add_zip = $_POST['add_zip'] ?? '';
$add_city = $_POST['add_city'] ?? '';
$add_house_number = $_POST['add_house_number'] ?? '';
$add_street = $_POST['add_street'] ?? '';
$add_fathers_name = $_POST['add_fathers_name'] ?? '';
$add_mothers_name = $_POST['add_mothers_name'] ?? '';
$add_guardian = $_POST['add_guardian'] ?? '';
$add_guardian_contact = $_POST['add_guardian_contact'] ?? '';
$add_image = $_FILES['add_image']['name'] ?? '';

$add_status = 'ACTIVE';


/* VALIDATION */
if(empty($add_position)){
    exit('error');
}


/* IMAGE UPLOAD */
if(!empty($add_image) && isset($_FILES['add_image']['tmp_name'])){

$type = pathinfo($add_image, PATHINFO_EXTENSION);
$new_image_name = uniqid(rand(), true).'.'.$type;
$new_image_path = '../assets/dist/img/'.$new_image_name;

if(!move_uploaded_file($_FILES['add_image']['tmp_name'],$new_image_path)){
    die("Image upload failed");
}

}else{

$new_image_name = '';
$new_image_path = '';

}


/* CHECK POSITION LIMIT */

$sql_position = "SELECT COUNT(position) AS count_position 
FROM official_status 
WHERE position = ? AND status='ACTIVE'";

$stmt_position = $con->prepare($sql_position);
$stmt_position->bind_param('i',$add_position);
$stmt_position->execute();
$result_position = $stmt_position->get_result();
$row_position = $result_position->fetch_assoc();


$sql_limit_position = "SELECT position_limit, position 
FROM position 
WHERE position_id = ?";

$stmt_position_limit = $con->prepare($sql_limit_position);
$stmt_position_limit->bind_param('i',$add_position);
$stmt_position_limit->execute();
$result_position_limit = $stmt_position_limit->get_result();
$row_position_limit = $result_position_limit->fetch_assoc();

if(!$row_position_limit){
    die("Position not found in database");
}

if($row_position['count_position'] >= $row_position_limit['position_limit']){
    exit('error');
}


/* AGE CALCULATION */

date_default_timezone_set('Asia/Manila');

$today = date("Y-m-d");

if(!empty($add_birth_date)){

$age = date_diff(date_create($add_birth_date), date_create($today));
$add_age_date = $age->format("%y");

}else{

$add_age_date = 0;

}


/* IDS */

$date = new DateTime();
$official_id = $date->format("mdYHisv").$add_age_date;
$date_added = date("Y-m-d H:i:s");


/* INSERT OFFICIAL INFORMATION */

$sql = "INSERT INTO official_information
(
official_id,
first_name,
middle_name,
last_name,
suffix,
birth_date,
birth_place,
gender,
age,
civil_status,
religion,
nationality,
house_number,
street,
address,
email_address,
contact_number,
fathers_name,
mothers_name,
guardian,
guardian_contact,
image,
image_path,
province,
zip,
city
)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

$stmt = $con->prepare($sql);

$stmt->bind_param(
'ssssssssssssssssssssssssss',
$official_id,
$add_first_name,
$add_middle_name,
$add_last_name,
$add_suffix,
$add_birth_date,
$add_birth_place,
$add_gender,
$add_age_date,
$add_civil_status,
$add_religion,
$add_nationality,
$add_house_number,
$add_street,
$add_address,
$add_email_address,
$add_contact_number,
$add_fathers_name,
$add_mothers_name,
$add_guardian,
$add_guardian_contact,
$new_image_name,
$new_image_path,
$add_province,
$add_zip,
$add_city
);

if(!$stmt->execute()){
    echo $stmt->error;
    exit;
}

$stmt->close();


/* INSERT OFFICIAL STATUS */

$sql_official_status = "INSERT INTO official_status
(official_id,status,position,date_added)
VALUES (?,?,?,?)";

$stmt_official_status = $con->prepare($sql_official_status);
$stmt_official_status->bind_param('ssss',$official_id,$add_status,$add_position,$date_added);

if(!$stmt_official_status->execute()){
    echo $stmt_official_status->error;
    exit;
}

$stmt_official_status->close();


/* ACTIVITY LOG */

$date_activity = date("j-n-Y g:i A");

$activity_log_position = strtoupper($row_position_limit['position']);

$admin = 'ADMIN: ADDED OFFICIAL - '.$official_id.' | '.$activity_log_position.' '.$add_first_name.' '.$add_last_name.' '.$add_suffix;

$status_activity_log = 'create';


$sql_activity_log = "INSERT INTO activity_log (message,date,status)
VALUES (?,?,?)";

$stmt_activity_log = $con->prepare($sql_activity_log);
$stmt_activity_log->bind_param('sss',$admin,$date_activity,$status_activity_log);
$stmt_activity_log->execute();
$stmt_activity_log->close();


/* SUCCESS */

echo 'success';
exit;

}catch(Exception $e){

echo $e->getMessage();

}

?>