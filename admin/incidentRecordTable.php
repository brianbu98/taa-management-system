<?php 

include_once '../connection.php';

try {

$sql_total = "SELECT COUNT(*) as total FROM incident_record";
$query_total = $con->query($sql_total);
$totalData = $query_total->fetch_assoc()['total'];

  $sql_incident_check = "SELECT * FROM incident_record ";
  $search = $_POST['search']['value'] ?? '';


   if(!empty($search)){
  $sql_incident_check .= " WHERE status LIKE '%$search%' ";
  $sql_incident_check .= " OR incidentlog_id LIKE '%$search%' ";
  $sql_incident_check .= " OR remarks LIKE '%$search%' ";
  $sql_incident_check .= " OR type_of_incident LIKE '%$search%' ";
  $sql_incident_check .= " OR location_incident LIKE '%$search%' ";
  $sql_incident_check .= " OR date_incident LIKE '%$search%' ";
  $sql_incident_check .= " OR date_reported LIKE '%$search%' ";
}



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
  $column_index = $_REQUEST['order'][0]['column'];
  $column_name = $columns[$column_index];
  $column_dir = $_REQUEST['order'][0]['dir'];

  $sql_incident_check .= " ORDER BY $column_name $column_dir ";
}else{
  $sql_incident_check .= " ORDER BY date_reported DESC ";
}

// Count filtered records BEFORE LIMIT
$query_total_filtered = $con->prepare($sql_incident_check) or die ($con->error);
$query_total_filtered->execute();
$result_total_filtered = $query_total_filtered->get_result();
$recordsFiltered = $result_total_filtered->num_rows;

// Apply LIMIT after counting
if($_REQUEST['length'] != -1){
  $sql_incident_check .= ' LIMIT '.$_REQUEST['start'].' , '.$_REQUEST['length'];
}

$query_filtered = $con->prepare($sql_incident_check) or die ($con->error);
$query_filtered->execute();
$result_filtered = $query_filtered->get_result();

$data = [];   

  while($row_incident_check = $result_filtered->fetch_assoc()) {

    date_default_timezone_set('Asia/Manila');
    $date_incident = date("m/d/Y - h:i A", strtotime($row_incident_check['date_incident']));
    $date_reported = date("m/d/Y - h:i A", strtotime($row_incident_check['date_reported']));

    if($row_incident_check['status'] == 'NEW'){
      $status_incident = '<span class="badge badge-primary">'.$row_incident_check['status'].'</span>';
    }else{
      $status_incident = '<span class="badge badge-warning">'.$row_incident_check['status'].'</span>';
    }

    if($row_incident_check['remarks'] == 'CLOSED'){
      $remarks_incident = '<span class="badge badge-success">'.$row_incident_check['remarks'].'</span>';
    }else{
      $remarks_incident = '<span class="badge badge-danger">'.$row_incident_check['remarks'].'</span>';
    }

    $subdata = [];
    $subdata[] = '<input type="checkbox" id="'. $row_incident_check['incidentlog_id'].'" class="sub_checkbox">';
    $subdata[] = $row_incident_check['incidentlog_id'];
    $subdata[] = $status_incident;
    $subdata[] = $remarks_incident;
    $subdata[] = $row_incident_check['type_of_incident'];
    $subdata[] = $row_incident_check['location_incident'];
    $subdata[] = $date_incident;
    $subdata[] = $date_reported;
    $subdata[] = '<i style="cursor:pointer;color:yellow;text-shadow:-1px 0 black,0 1px black,1px 0 black,0 -1px black;" class="fa fa-book-open text-lg px-2 viewRecords" id="'.$row_incident_check['incidentlog_id'].'"></i>';

    $data[] = $subdata;

}


 $json_data = [
  'draw' => intval($_REQUEST['draw'] ?? 0),
  'recordsTotal' => intval($totalData),
  'recordsFiltered' => intval($recordsFiltered),
  'data' => $data,
];
  echo json_encode($json_data);

}catch(Exception $e){
  echo $e->getMessage();
}

?>
