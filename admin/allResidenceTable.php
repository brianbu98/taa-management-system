<?php
header('Content-Type: application/json; charset=utf-8');
include_once '../connection.php';
session_start();

try {

    // FILTERS
    $first_name    = $con->real_escape_string($_POST['first_name'] ?? '');
    $middle_name   = $con->real_escape_string($_POST['middle_name'] ?? '');
    $last_name     = $con->real_escape_string($_POST['last_name'] ?? '');
    $status        = $con->real_escape_string($_POST['status'] ?? '');
    $age           = $con->real_escape_string($_POST['age'] ?? '');
    $resident_id   = $con->real_escape_string($_POST['resident_id'] ?? '');

    $whereParts = [];

    if ($first_name !== '')  $whereParts[] = "residence_information.first_name LIKE '%$first_name%'";
    if ($middle_name !== '') $whereParts[] = "residence_information.middle_name LIKE '%$middle_name%'";
    if ($last_name !== '')   $whereParts[] = "residence_information.last_name LIKE '%$last_name%'";
    if ($status !== '')      $whereParts[] = "residence_status.status = '$status'";
    if ($age !== '')         $whereParts[] = "residence_information.age = '$age'";
    if ($resident_id !== '') $whereParts[] = "residence_information.residence_id = '$resident_id'";

    $where = '';
    if (!empty($whereParts)) {
        $where = ' AND ' . implode(' AND ', $whereParts);
    }

    // BASE QUERY WITH HOUSEHOLD
    $sqlBase = "
    FROM residence_information
    LEFT JOIN residence_status 
        ON residence_information.residence_id = residence_status.residence_id
   LEFT JOIN households
ON residence_information.household_id = households.household_id
    WHERE (residence_status.archive IS NULL OR UPPER(residence_status.archive) != 'YES')
    $where
    ";

    // COUNT
    $countSql = "SELECT COUNT(*) AS total $sqlBase";
    $stmtCount = $con->prepare($countSql);
    $stmtCount->execute();
    $cres = $stmtCount->get_result();
    $totalData = intval($cres->fetch_assoc()['total'] ?? 0);
    $stmtCount->close();

    $totalFiltered = $totalData;

    // ORDERING (UPDATED FOR NEW COLUMNS)
    $columns = [
        0 => 'residence_information.image_path',
        1 => 'residence_information.residence_id',
        2 => 'residence_information.first_name',
        3 => 'residence_information.age',
        4 => 'households.household_no',
        5 => 'households.house_address',
        6 => 'residence_status.status',
        7 => 'residence_information.residence_id'
    ];

    $orderSql = " ORDER BY residence_information.residence_id DESC ";

    if (isset($_REQUEST['order'][0]['column'])) {
        $colIndex = intval($_REQUEST['order'][0]['column']);
        $dir = ($_REQUEST['order'][0]['dir'] === 'asc') ? 'ASC' : 'DESC';

        if (isset($columns[$colIndex])) {
            $orderSql = " ORDER BY ".$columns[$colIndex]." ".$dir;
        }
    }

    // LIMIT
    $start = intval($_REQUEST['start'] ?? 0);
    $length = intval($_REQUEST['length'] ?? 10);
    $limitSql = ($length != -1) ? " LIMIT $start, $length " : "";

    // FINAL QUERY
    $sql = "
    SELECT
        residence_information.residence_id,
        residence_information.first_name,
        residence_information.middle_name,
        residence_information.last_name,
        residence_information.age,
        residence_information.image,
        residence_information.image_path,
        residence_status.status,
       households.household_id,
CONCAT(households.first_name, ' ', households.last_name) AS household_name
    $sqlBase
    $orderSql
    $limitSql
    ";

    $stmt = $con->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];

    while ($row = $result->fetch_assoc()) {

        // IMAGE
        $imgPath = $row['image_path'] ?? '';
        $img = $imgPath !== ''
            ? '<span class="pop"><img src="'.$imgPath.'" class="img-circle" width="40"></span>'
            : '<span class="pop"><img src="../assets/dist/img/blank_image.png" class="img-circle" width="40"></span>';

        // NAME
        $middle = !empty($row['middle_name']) ? strtoupper($row['middle_name'][0]).'.' : '';
        $fullName = ucfirst($row['first_name']).' '.$middle.' '.ucfirst($row['last_name']);

        // HOUSEHOLD
      $household = $row['household_id'] ?? 'N/A';
      $address   = $row['household_name'] ?? 'N/A';

        // STATUS SWITCH
        $statusVal = $row['status'] ?? 'INACTIVE';
        $checked = ($statusVal === 'ACTIVE') ? 'checked' : '';

        $statusSwitch = '
        <label class="switch">
            <input type="checkbox" class="editStatus" id="'.$row['residence_id'].'" data-status="'.$statusVal.'" '.$checked.'>
            <div class="slider round">
                <span class="on">ACTIVE</span>
                <span class="off">INACTIVE</span>
            </div>
        </label>';

        // ACTIONS
        $actions = '
        <i class="fa fa-user-edit viewResidence" id="'.$row['residence_id'].'" style="color:yellow;cursor:pointer;"></i>
        <i class="fa fa-times deleteResidence" id="'.$row['residence_id'].'" style="color:red;cursor:pointer;"></i>';

        $data[] = [
            $img,
            $row['residence_id'],
            $fullName,
            $row['age'],
            $household,
            $address,
            $statusSwitch,
            $actions
        ];
    }

    echo json_encode([
        "draw" => intval($_POST['draw'] ?? 0),
        "recordsTotal" => $totalData,
        "recordsFiltered" => $totalFiltered,
        "data" => $data,
        "total" => $totalData
    ]);

} catch (Exception $e) {
    echo json_encode([
        "error" => $e->getMessage()
    ]);
}