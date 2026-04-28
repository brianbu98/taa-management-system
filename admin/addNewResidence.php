<?php
// addNewResidence.php (corrected to match your tables)
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../connection.php';
session_start();

try {
    // Auth check
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'not_authorized']);
        exit;
    }

    // generate a unique residence id
    $number = time() . mt_rand(1000, 9999);

    // collect/sanitize inputs
    $add_first_name       = trim($con->real_escape_string($_POST['add_first_name'] ?? ''));
    $add_middle_name      = trim($con->real_escape_string($_POST['add_middle_name'] ?? ''));
    $add_last_name        = trim($con->real_escape_string($_POST['add_last_name'] ?? ''));
    $add_suffix           = $con->real_escape_string($_POST['add_suffix'] ?? '');
    $add_alias = $con->real_escape_string($_POST['add_alias'] ?? '');
    $add_gender           = $con->real_escape_string($_POST['add_gender'] ?? '');
    $add_civil_status     = $con->real_escape_string($_POST['add_civil_status'] ?? '');
    $add_religion         = $con->real_escape_string($_POST['add_religion'] ?? '');
    $add_nationality      = $con->real_escape_string($_POST['add_nationality'] ?? '');
    $add_contact_number   = $con->real_escape_string($_POST['add_contact_number'] ?? '');
    $add_email_address    = $con->real_escape_string($_POST['add_email_address'] ?? '');
    $add_address          = $con->real_escape_string($_POST['add_address'] ?? '');
    $add_birth_date       = $con->real_escape_string($_POST['add_birth_date'] ?? '');
    $add_birth_place      = $con->real_escape_string($_POST['add_birth_place'] ?? '');
    $add_house_number     = $con->real_escape_string($_POST['add_house_number'] ?? '');
    $add_street           = $con->real_escape_string($_POST['add_street'] ?? '');
    $add_fathers_name     = $con->real_escape_string($_POST['add_fathers_name'] ?? '');
    $add_mothers_name     = $con->real_escape_string($_POST['add_mothers_name'] ?? '');
    $add_guardian         = $con->real_escape_string($_POST['add_guardian'] ?? '');
    $add_guardian_contact = $con->real_escape_string($_POST['add_guardian_contact'] ?? '');

    $household_id = $con->real_escape_string($_POST['household_id'] ?? '');

    

    $add_status = 'ACTIVE';
    $archive = 'NO';

    // compute age
    $age_value = '';
    if (!empty($add_birth_date)) {
        $today = date("Y-m-d");
        $ageDiff = date_diff(date_create($add_birth_date), date_create($today));
        $years = intval($ageDiff->format("%y"));
        if ($years > 0) $age_value = (string)$years;
    }

    $senior = ($age_value !== '' && intval($age_value) >= 60) ? 'YES' : 'NO';

    if ($add_first_name === '' || $add_last_name === '') {
        echo json_encode(['success' => false, 'message' => 'missing_required_fields']);
        exit;
    }

    // image upload (optional)
    $new_image_name = '';
    $new_image_path = '';
    if (!empty($_FILES['add_image']['tmp_name'])) {
        $uploadDir = __DIR__ . '/../assets/dist/img/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $originalName = $_FILES['add_image']['name'];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if (in_array($ext, $allowed)) {
            $new_image_name = 'residence_' . $number . '.' . $ext;
            $target = $uploadDir . $new_image_name;
            if (move_uploaded_file($_FILES['add_image']['tmp_name'], $target)) {
                $new_image_path = 'assets/dist/img/' . $new_image_name;
            } else {
                file_put_contents(__DIR__.'/addNewResidence_debug.txt', date('c')." Move failed for $originalName\n", FILE_APPEND);
            }
        } else {
            file_put_contents(__DIR__.'/addNewResidence_debug.txt', date('c')." Invalid image type: $originalName\n", FILE_APPEND);
        }
    }

    // transaction
    $con->begin_transaction();

    // insert residence_information (no date_added here)
    $sql_info = "INSERT INTO residence_information


      (residence_id, first_name, middle_name, last_name, age, suffix, alias, gender, civil_status, religion, nationality, contact_number, email_address, address, birth_date, birth_place, house_number, street, fathers_name, mothers_name, guardian, guardian_contact, image, image_path, household_id)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

      (residence_id, first_name, middle_name, last_name, age, suffix, alias, gender, civil_status, religion, nationality, contact_number, email_address, address, birth_date, birth_place, house_number, street, fathers_name, mothers_name, guardian, guardian_contact, image, image_path)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


      (residence_id, first_name, middle_name, last_name, age, suffix, alias, gender, civil_status, religion, nationality, contact_number, email_address, address, birth_date, birth_place, house_number, street, fathers_name, mothers_name, guardian, guardian_contact, image, image_path)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $con->prepare($sql_info);
    if (!$stmt) {
        file_put_contents(__DIR__.'/addNewResidence_debug.txt', date('c')." PREPARE ERROR INFO: ".$con->error.PHP_EOL, FILE_APPEND);
        $con->rollback();
        echo json_encode(['success' => false, 'message' => 'prepare_failed_info', 'error' => $con->error]);
        exit;
    }
    $stmt->bind_param(
        'ssssssssssssssssssssssss',
        $number,
        $add_first_name,
        $add_middle_name,
        $add_last_name,
        $age_value,
        $add_suffix,
        $add_alias,
        $add_gender,
        $add_civil_status,
        $add_religion,
        $add_nationality,
        $add_contact_number,
        $add_email_address,
        $add_address,
        $add_birth_date,
        $add_birth_place,
        $add_house_number,
        $add_street,
        $add_fathers_name,
        $add_mothers_name,
        $add_guardian,
        $add_guardian_contact,
        $new_image_name,
        $new_image_path,
        $household_id
    );
    if (!$stmt->execute()) {
        file_put_contents(__DIR__.'/addNewResidence_debug.txt', date('c')." EXECUTE ERROR INFO: ".$stmt->error.PHP_EOL, FILE_APPEND);
        $con->rollback();
        echo json_encode(['success' => false, 'message' => 'execute_failed_info', 'error' => $stmt->error]);
        exit;
    }
    $stmt->close();

    $is_approved = 'YES';

    // insert residence_status (with date_added)
    $sql_status = "INSERT INTO residence_status (residence_id, status, is_approved, archive, date_added)
                   VALUES (?, ?, ?, ?, NOW())";
    $stmt2 = $con->prepare($sql_status);
    if (!$stmt2) {
        file_put_contents(__DIR__.'/addNewResidence_debug.txt', date('c')." PREPARE ERROR STATUS: ".$con->error.PHP_EOL, FILE_APPEND);
        $con->rollback();
        echo json_encode(['success' => false, 'message' => 'prepare_failed_status', 'error' => $con->error]);
        exit;
    }
    $stmt2->bind_param('ssss', $number, $add_status, $is_approved, $archive);
    if (!$stmt2->execute()) {
        file_put_contents(__DIR__.'/addNewResidence_debug.txt', date('c')." EXECUTE ERROR STATUS: ".$stmt2->error.PHP_EOL, FILE_APPEND);
        $con->rollback();
        echo json_encode(['success' => false, 'message' => 'execute_failed_status', 'error' => $stmt2->error]);
        exit;
    }
    $stmt2->close();

    // insert users
    $user_type = 'resident';
    $password = $number; // legacy plain; consider hashing

    $sql_user = "INSERT INTO users (id, first_name, middle_name, last_name, username, password, user_type, contact_number, image, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_user = $con->prepare($sql_user);
    if (!$stmt_user) {
        file_put_contents(__DIR__.'/addNewResidence_debug.txt', date('c')." PREPARE ERROR USER: ".$con->error.PHP_EOL, FILE_APPEND);
        $con->rollback();
        echo json_encode(['success' => false, 'message' => 'prepare_failed_user', 'error' => $con->error]);
        exit;
    }
    $stmt_user->bind_param('ssssssssss', $number, $add_first_name, $add_middle_name, $add_last_name, $number, $password, $user_type, $add_contact_number, $new_image_name, $new_image_path);
    if (!$stmt_user->execute()) {
        file_put_contents(__DIR__.'/addNewResidence_debug.txt', date('c')." EXECUTE ERROR USER: ".$stmt_user->error.PHP_EOL, FILE_APPEND);
        $con->rollback();
        echo json_encode(['success' => false, 'message' => 'execute_failed_user', 'error' => $stmt_user->error]);
        exit;
    }
    $stmt_user->close();

    // activity log (best-effort)
    $date_activity = date("j-n-Y g:i A");
    $admin = strtoupper('ADMIN') . ': ADDED RESIDENT - ' . $number . ' | ' . $add_first_name . ' ' . $add_last_name . ' ' . $add_suffix;
    $status_activity_log = 'create';
    $sql_activity_log = "INSERT INTO activity_log (`message`,`date`,`status`) VALUES (?, ?, ?)";
    $stmt_activity = $con->prepare($sql_activity_log);
    if ($stmt_activity) {
        $stmt_activity->bind_param('sss', $admin, $date_activity, $status_activity_log);
        $stmt_activity->execute();
        $stmt_activity->close();
    }

    $con->commit();

    file_put_contents(__DIR__.'/save_debug.txt', "Saved resident ID: ".$number.PHP_EOL, FILE_APPEND);

    echo json_encode(['success' => true, 'id' => $number, 'message' => 'inserted']);
    exit;

} catch (Exception $e) {
    if ($con->errno) $con->rollback();
    file_put_contents(__DIR__.'/addNewResidence_debug.txt', date('c')." EXCEPTION: ".$e->getMessage().PHP_EOL, FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'exception', 'error' => $e->getMessage()]);
    exit;
}
