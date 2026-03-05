<?php
include_once '../connection.php';

try {

    // ----------------------------
    // INPUTS
    // ----------------------------
    $first_name  = $_POST['first_name']  ?? '';
    $middle_name = $_POST['middle_name'] ?? '';
    $last_name   = $_POST['last_name']   ?? '';
    $status      = $_POST['status']      ?? '';
    $age         = $_POST['age']         ?? '';
    $resident_id = $_POST['resident_id'] ?? '';

    $draw   = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;
    $start  = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
    $length = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 10;

    // ----------------------------
    // WHERE FILTER
    // ----------------------------
    $whereClause = [];

    if ($first_name !== '') {
        $whereClause[] = "residence_information.first_name LIKE '%" . $con->real_escape_string($first_name) . "%'";
    }

    if ($middle_name !== '') {
        $whereClause[] = "residence_information.middle_name LIKE '%" . $con->real_escape_string($middle_name) . "%'";
    }

    if ($last_name !== '') {
        $whereClause[] = "residence_information.last_name LIKE '%" . $con->real_escape_string($last_name) . "%'";
    }

    if ($status !== '') {
        $whereClause[] = "residence_status.status = '" . $con->real_escape_string($status) . "'";
    }

    if ($age !== '') {
        $whereClause[] = "residence_information.age = '" . $con->real_escape_string($age) . "'";
    }

    if ($resident_id !== '') {
        $whereClause[] = "residence_information.residence_id = '" . $con->real_escape_string($resident_id) . "'";
    }

    $where = '';
    if (!empty($whereClause)) {
        $where = " AND " . implode(" AND ", $whereClause);
    }

    // ----------------------------
    // BASE QUERY
    // ----------------------------
    $baseQuery = "
        FROM residence_information
        INNER JOIN residence_status
            ON residence_information.residence_id = residence_status.residence_id
       WHERE residence_status.archive = 'NO'
        $where
    ";

    // ----------------------------
    // TOTAL RECORDS (WITHOUT FILTER)
    // ----------------------------
    $totalQuery = "
        SELECT COUNT(*) as total
        FROM residence_information
        INNER JOIN residence_status
            ON residence_information.residence_id = residence_status.residence_id
        WHERE residence_information.archive = 'NO'
    ";

    $totalResult = $con->query($totalQuery);
    $recordsTotal = $totalResult->fetch_assoc()['total'];

    // ----------------------------
    // FILTERED COUNT
    // ----------------------------
    $filteredQuery = "SELECT COUNT(*) as total " . $baseQuery;
    $filteredResult = $con->query($filteredQuery);
    $recordsFiltered = $filteredResult->fetch_assoc()['total'];

    // ----------------------------
    // COLUMN MAPPING (DataTables)
    // ----------------------------
    $columns = [
        0 => 'residence_information.image',
        1 => 'residence_information.residence_id',
        2 => 'residence_information.first_name',
        3 => 'residence_information.age',
        4 => 'residence_status.status'
    ];

    // ----------------------------
    // MAIN SELECT
    // ----------------------------
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
        $baseQuery
    ";

    // ----------------------------
    // ORDERING
    // ----------------------------
    if (isset($_REQUEST['order'][0]['column'])) {
        $columnIndex = intval($_REQUEST['order'][0]['column']);
        $columnDir   = $_REQUEST['order'][0]['dir'] === 'asc' ? 'ASC' : 'DESC';

        if (array_key_exists($columnIndex, $columns)) {
            $sql .= " ORDER BY " . $columns[$columnIndex] . " " . $columnDir;
        }
    } else {
        $sql .= " ORDER BY residence_information.residence_id DESC";
    }

    // ----------------------------
    // PAGINATION
    // ----------------------------
    if ($length != -1) {
        $sql .= " LIMIT $start, $length";
    }

    $query = $con->query($sql);

    if (!$query) {
        die("SQL Error: " . $con->error);
    }

    $data = [];

    while ($row = $query->fetch_assoc()) {

        // IMAGE
        if (!empty($row['image'])) {
            $image = '<img src="' . $row['image_path'] . '" class="img-circle" width="40">';
        } else {
            $image = '<img src="../assets/dist/img/blank_image.png" class="img-circle" width="40">';
        }

        // MIDDLE INITIAL
        $middle = '';
        if (!empty($row['middle_name'])) {
            $middle = strtoupper($row['middle_name'][0]) . '.';
        }

        // STATUS SWITCH
        $checked = $row['status'] === 'ACTIVE' ? 'checked' : '';
        $statusBtn = '
            <label class="switch">
                <input type="checkbox" class="editStatus"
                       id="' . $row['residence_id'] . '" ' . $checked . '>
                <div class="slider round"></div>
            </label>';

        $data[] = [
            $image,
            $row['residence_id'],
            ucfirst($row['first_name']) . ' ' . $middle . ' ' . ucfirst($row['last_name']),
            $row['age'],
            $statusBtn,
            '
            <i class="fa fa-user-edit viewResidence"
               id="' . $row['residence_id'] . '" style="color:gold;cursor:pointer;"></i>
            <i class="fa fa-times deleteResidence"
               id="' . $row['residence_id'] . '" style="color:red;cursor:pointer;"></i>
            '
        ];
    }

    // ----------------------------
    // JSON RESPONSE
    // ----------------------------
    echo json_encode([
        "draw" => $draw,
        "recordsTotal" => intval($recordsTotal),
        "recordsFiltered" => intval($recordsFiltered),
        "data" => $data
    ]);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>