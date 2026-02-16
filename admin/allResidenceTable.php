<?php
// allResidenceTable.php (final corrected to match your schema)
// Returns JSON for DataTables
header('Content-Type: application/json; charset=utf-8');
include_once '../connection.php';
session_start();

try {
    // --- FILTERS (sanitized) ---
    $first_name    = $con->real_escape_string($_POST['first_name'] ?? '');
    $middle_name   = $con->real_escape_string($_POST['middle_name'] ?? '');
    $last_name     = $con->real_escape_string($_POST['last_name'] ?? '');
    $status        = $con->real_escape_string($_POST['status'] ?? '');
    $age           = $con->real_escape_string($_POST['age'] ?? '');
    $resident_id   = $con->real_escape_string($_POST['resident_id'] ?? '');

    $whereParts = [];
    if ($first_name !== '')    $whereParts[] = "residence_information.first_name LIKE '%$first_name%'";
    if ($middle_name !== '')   $whereParts[] = "residence_information.middle_name LIKE '%$middle_name%'";
    if ($last_name !== '')     $whereParts[] = "residence_information.last_name LIKE '%$last_name%'";
    if ($status !== '')        $whereParts[] = "residence_status.status = '$status'";
    if ($age !== '')           $whereParts[] = "residence_information.age = '$age'";
    if ($resident_id !== '')   $whereParts[] = "residence_information.residence_id = '$resident_id'";

    $where = '';
    if (count($whereParts) > 0) {
        $where = ' AND ' . implode(' AND ', $whereParts);
    }

    // --- Archive logic: exclude only rows explicitly archived = 'YES' in residence_status ---
    // Use only residence_status.archive because residence_information doesn't have 'archive' column.
    $archiveChecks = "1=1";


    // --- BASE SQL (LEFT JOIN so new rows without status still appear) ---
   $sqlBase = "
    FROM residence_information
    LEFT JOIN residence_status 
        ON residence_information.residence_id = residence_status.residence_id
    WHERE (residence_status.archive IS NULL OR residence_status.archive = 'NO')
    $where
";


    // --- COUNT matching rows ---
    $countSql = "SELECT COUNT(*) AS total $sqlBase";
    // Debug log (uncomment if you want to capture)
    // file_put_contents(__DIR__.'/allResidence_debug.txt', date('c')." COUNT SQL: ".$countSql.PHP_EOL, FILE_APPEND);

    $stmtCount = $con->prepare($countSql);
    if (!$stmtCount) {
        file_put_contents(__DIR__.'/allResidence_debug.txt', date('c')." COUNT PREPARE ERROR: ".$con->error.PHP_EOL, FILE_APPEND);
        throw new Exception($con->error);
    }
    $stmtCount->execute();
    $cres = $stmtCount->get_result();
    $totalData = intval($cres->fetch_assoc()['total'] ?? 0);
    $stmtCount->close();

    $totalFiltered = $totalData;

    // --- Column mapping for safe ORDER BY (index => column)
    // DataTables columns (from your UI): image, id, name, age, pwd_info, single_parent, voters, status switch, actions
    $columns = [
        0 => 'residence_information.image_path',
        1 => 'residence_information.residence_id',
        2 => 'residence_information.first_name',
        3 => 'residence_information.age',
        4 => 'residence_status.status',
        5 => 'residence_information.residence_id' // safe placeholder for actions column
    ];

    // Default safe ordering
    $orderSql = " ORDER BY residence_information.residence_id DESC ";

    if (isset($_REQUEST['order'][0]['column'])) {
        $colIndex = intval($_REQUEST['order'][0]['column']);
        $dir = (isset($_REQUEST['order'][0]['dir']) && strtolower($_REQUEST['order'][0]['dir']) === 'asc') ? 'ASC' : 'DESC';
        if (isset($columns[$colIndex])) {
            $orderSql = ' ORDER BY ' . $columns[$colIndex] . ' ' . $dir . ' ';
        }
    }

    // --- LIMIT for paging ---
    $limitSql = '';
    $start = intval($_REQUEST['start'] ?? 0);
    $length = intval($_REQUEST['length'] ?? 10);
    if ($length !== -1) {
        $limitSql = " LIMIT $start, $length ";
    }

    // --- Final SELECT ---
    $sql = "
        SELECT
            residence_information.residence_id,
            residence_information.first_name,
            residence_information.middle_name,
            residence_information.last_name,
            residence_information.age,
            residence_information.image,
            residence_information.image_path,
            residence_status.status
        $sqlBase
        $orderSql
        $limitSql
    ";

    // Debug log final SQL + REQUEST for troubleshooting (helpful if still missing rows)
    file_put_contents(__DIR__.'/allResidence_debug.txt', date('c')." FINAL SQL: ".$sql.PHP_EOL, FILE_APPEND);
    file_put_contents(__DIR__.'/allResidence_debug.txt', date('c')." REQUEST: ".json_encode($_REQUEST).PHP_EOL, FILE_APPEND);

    $stmt = $con->prepare($sql);
    if (!$stmt) {
        file_put_contents(__DIR__.'/allResidence_debug.txt', date('c')." SELECT PREPARE ERROR: ".$con->error.PHP_EOL, FILE_APPEND);
        throw new Exception($con->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $imgPath = $row['image_path'] ?? '';
        $img = $imgPath !== '' 
            ? '<span class="pop" style="cursor:pointer;"><img src="' . htmlspecialchars($imgPath, ENT_QUOTES) . '" class="img-circle" width="40" alt="residence_image"></span>'
            : '<span class="pop" style="cursor:pointer;"><img src="../assets/dist/img/blank_image.png" class="img-circle" width="40" alt="residence_image"></span>';

        $middleInit = '';
        if (!empty($row['middle_name'])) {
            $middleInit = strtoupper($row['middle_name'][0]) . '.';
        }

        $statusVal = $row['status'] ?? 'INACTIVE';
        $isActive = ($statusVal === 'ACTIVE') ? 'checked' : '';
        $statusSwitch = '
            <label class="switch">
                <input type="checkbox" class="editStatus" id="' . htmlspecialchars($row['residence_id'], ENT_QUOTES) . '" data-status="' . htmlspecialchars($statusVal, ENT_QUOTES) . '" ' . $isActive . '>
                <div class="slider round">
                    <span class="on">ACTIVE</span>
                    <span class="off">INACTIVE</span>
                </div>
            </label>';

        $actions = '
            <i class="fa fa-user-edit text-lg px-3 viewResidence" id="' . htmlspecialchars($row['residence_id'], ENT_QUOTES) . '" style="cursor:pointer;color:yellow;"></i>
            <i class="fa fa-times text-lg px-2 deleteResidence" id="' . htmlspecialchars($row['residence_id'], ENT_QUOTES) . '" style="cursor:pointer;color:red;"></i>
        ';

        $fullName = ucfirst($row['first_name'] ?? '') . ' ' . $middleInit . ' ' . ucfirst($row['last_name'] ?? '');

        $data[] = [
            $img,
            $row['residence_id'],
            $fullName,
            $row['age'] ?? '',
            $statusSwitch,
            $actions
        ];
    }

    $draw = intval($_POST['draw'] ?? $_GET['draw'] ?? 0);
    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => $totalData,
        "recordsFiltered" => $totalFiltered,
        "data" => $data,
        "total" => $totalData
    ]);
    exit;

} catch (Exception $e) {
    file_put_contents(__DIR__.'/allResidence_debug.txt', date('c')." EXCEPTION: ".$e->getMessage().PHP_EOL, FILE_APPEND);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}
