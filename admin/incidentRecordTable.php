<?php
include_once '../connection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// DataTables parameters
$draw   = intval($_POST['draw'] ?? 0);
$start  = intval($_POST['start'] ?? 0);
$length = intval($_POST['length'] ?? 10);
$search = $_POST['search']['value'] ?? '';

// Total records
$totalQuery = $con->query("SELECT COUNT(*) as total FROM incident_record");
$totalData = $totalQuery->fetch_assoc()['total'];

// Base query
$sql = "SELECT * FROM incident_record";

// Search
if (!empty($search)) {
    $search = $con->real_escape_string($search);
    $sql .= " WHERE 
        incidentlog_id LIKE '%$search%' OR
        status LIKE '%$search%' OR
        remarks LIKE '%$search%' OR
        type_of_incident LIKE '%$search%' OR
        location_incident LIKE '%$search%' OR
        date_incident LIKE '%$search%' OR
        date_reported LIKE '%$search%'";
}

// Count filtered
$queryFiltered = $con->query($sql);
$recordsFiltered = $queryFiltered->num_rows;

// Order
$columns = [
    0 => 'incidentlog_id',
    1 => 'incidentlog_id',
    2 => 'status',
    3 => 'remarks',
    4 => 'type_of_incident',
    5 => 'location_incident',
    6 => 'date_incident',
    7 => 'date_reported'
];

if (isset($_POST['order'])) {
    $colIndex = $_POST['order'][0]['column'];
    $colName  = $columns[$colIndex];
    $colDir   = $_POST['order'][0]['dir'];

    $sql .= " ORDER BY $colName $colDir";
} else {
    $sql .= " ORDER BY date_reported DESC";
}

// Limit
if ($length != -1) {
    $sql .= " LIMIT $start, $length";
}

// Fetch data
$result = $con->query($sql);

$data = [];

while ($row = $result->fetch_assoc()) {

    $date_incident = date("m/d/Y h:i A", strtotime($row['date_incident']));
    $date_reported = date("m/d/Y h:i A", strtotime($row['date_reported']));

    $status = ($row['status'] == 'NEW')
        ? '<span class="badge badge-primary">NEW</span>'
        : '<span class="badge badge-warning">'.$row['status'].'</span>';

    $remarks = ($row['remarks'] == 'CLOSED')
        ? '<span class="badge badge-success">CLOSED</span>'
        : '<span class="badge badge-danger">'.$row['remarks'].'</span>';

    $subdata = [];
    $subdata[] = '<input type="checkbox" class="sub_checkbox" id="'.$row['incidentlog_id'].'">';
    $subdata[] = $row['incidentlog_id'];
    $subdata[] = $status;
    $subdata[] = $remarks;
    $subdata[] = $row['type_of_incident'];
    $subdata[] = $row['location_incident'];
    $subdata[] = $date_incident;
    $subdata[] = $date_reported;
    $subdata[] = '<button class="btn btn-sm btn-info viewRecords" data-id="'.$row['incidentlog_id'].'">View</button>';

    $data[] = $subdata;
}

// Output JSON
echo json_encode([
    "draw" => $draw,
    "recordsTotal" => $totalData,
    "recordsFiltered" => $recordsFiltered,
    "data" => $data
]);