<?php 

include_once '../connection.php';

try {

date_default_timezone_set('Asia/Manila');

/* TOTAL RECORDS */
$sql_total = "SELECT COUNT(*) as total FROM incident_record";
$result_total = $con->query($sql_total);
$row_total = $result_total->fetch_assoc();
$recordsTotal = $row_total['total'];

$search = $_REQUEST['search']['value'] ?? '';

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
    date_reported LIKE '%$search%'
  ";
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

if(isset($_REQUEST['order'])){
  $columnIndex = $_REQUEST['order'][0]['column'];
  $columnName = $columns[$columnIndex];
  $columnSortOrder = $_REQUEST['order'][0]['dir'];

  $sql .= " ORDER BY $columnName $columnSortOrder";
}else{
  $sql .= " ORDER BY date_reported DESC";
}

/* LIMIT */
if($_REQUEST['length'] != -1){
  $sql .= " LIMIT ".$_REQUEST['start']." , ".$_REQUEST['length'];
}

$query = $con->prepare($sql);
$query->execute();
$result = $query->get_result();

$data = [];

while($row = $result->fetch_assoc()){

  $date_incident = date("m/d/Y - h:i A", strtotime($row['date_incident']));
  $date_reported = date("m/d/Y - h:i A", strtotime($row['date_reported']));

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

$json_data = [
'draw' => intval($_REQUEST['draw']),
'recordsTotal' => intval($recordsTotal),
'recordsFiltered' => intval($recordsTotal),
'data' => $data
];

echo json_encode($json_data);

} catch(Exception $e){
 echo $e->getMessage();
}

?>