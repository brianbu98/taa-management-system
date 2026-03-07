<?php
include_once '../connection.php';

date_default_timezone_set('Asia/Manila');

$draw = intval($_POST['draw']);
$start  = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

/* TOTAL RECORDS */
$totalQuery = $con->query("SELECT COUNT(*) as total FROM incident_record");
$totalRow = $totalQuery->fetch_assoc();
$recordsTotal = $totalRow['total'];

$sql = "SELECT 
incidentlog_id,
status,
remarks,
type_of_incident,
location_incident,
date_incident,
date_reported
FROM incident_record";

/* SEARCH */
if(!empty($search)){

$search = $con->real_escape_string($search);

$sql .= " WHERE 
status LIKE '%$search%' OR
incidentlog_id LIKE '%$search%' OR
remarks LIKE '%$search%' OR
type_of_incident LIKE '%$search%' OR
location_incident LIKE '%$search%' OR
date_incident LIKE '%$search%' OR
date_reported LIKE '%$search%'";
}

if(!empty($search)){
    $filteredQuery = $con->query($sql);
    $recordsFiltered = $filteredQuery->num_rows;
}else{
    $recordsFiltered = $recordsTotal;
}

/* ORDER */
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

if(isset($_POST['order'])){
$columnIndex = $_POST['order'][0]['column'];
$columnSortOrder = $_POST['order'][0]['dir'];
$columnName = $columns[$columnIndex];

$sql .= " ORDER BY $columnName $columnSortOrder";
}else{
$sql .= " ORDER BY date_reported DESC";
}

/* LIMIT */
$sql .= " LIMIT $start,$length";

$result = $con->query($sql) or die($con->error);

$data = [];

while($row = $result->fetch_assoc()){

$date_incident = !empty($row['date_incident']) 
    ? date("m/d/Y - h:i A", strtotime($row['date_incident'])) 
    : '';

$date_reported = !empty($row['date_reported']) 
    ? date("m/d/Y - h:i A", strtotime($row['date_reported'])) 
    : '';

$status = ($row['status'] == 'NEW')
? '<span class="badge badge-primary">'.$row['status'].'</span>'
: '<span class="badge badge-warning">'.$row['status'].'</span>';

$remarks = ($row['remarks'] == 'CLOSED')
? '<span class="badge badge-success">'.$row['remarks'].'</span>'
: '<span class="badge badge-danger">'.$row['remarks'].'</span>';

$subdata = [];

$subdata[] = '<input type="checkbox" id="'.$row['incidentlog_id'].'" class="sub_checkbox">';
$subdata[] = $row['incidentlog_id'];
$subdata[] = $status;
$subdata[] = $remarks;
$subdata[] = $row['type_of_incident'];
$subdata[] = $row['location_incident'];
$subdata[] = $date_incident;
$subdata[] = $date_reported;

$subdata[] = '<i style="cursor:pointer;color:yellow;text-shadow:-1px 0 black,0 1px black,1px 0 black,0 -1px black;" class="fa fa-book-open text-lg px-2 viewRecords" id="'.$row['incidentlog_id'].'"></i>';

$data[] = $subdata;
}

$output = [
"draw" => $draw,
"recordsTotal" => $recordsTotal,
"recordsFiltered" => $recordsFiltered,
"data" => $data ?? []
];

header('Content-Type: application/json');
echo json_encode($output);
exit;
?>