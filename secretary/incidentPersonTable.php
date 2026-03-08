<?php 

include_once '../connection.php';

try {

  $edit_residence_id = $con->real_escape_string($_POST['edit_residence_id']);

  $sql_incident_check = "
    SELECT 
      incident_record.*, 
      incident_status.*, 
      incident_complainant.*, 
      incident_record.incident_id AS incidentlog_id 
    FROM 
      `incident_record` 
    INNER JOIN 
      incident_complainant 
      ON incident_record.incident_id = incident_complainant.incident_main 
    INNER JOIN 
      incident_status 
      ON incident_record.incident_id = incident_status.incident_main 
    WHERE 
      person_id = ? 
      OR complainant_id = ? 
    GROUP BY 
      incident_record.incident_id
  ";

  $query_incident_check = $con->prepare($sql_incident_check) or die($con->error);
  $query_incident_check->bind_param('ss', $edit_residence_id, $edit_residence_id);
  $query_incident_check->execute();
  $result_incident_check = $query_incident_check->get_result();

  $totalDataIncident = $result_incident_check->num_rows;
  $totalFilteredIncident = $totalDataIncident;

  $data = [];

  while ($row_incident_check = $result_incident_check->fetch_assoc()) {

    date_default_timezone_set('Asia/Manila');
    $date_incident = date("m/d/Y - h:i A", strtotime($row_incident_check['date_incident']));
    $date_reported = date("m/d/Y - h:i A", strtotime($row_incident_check['date_reported']));

    if ($row_incident_check['status'] == 'NEW') {
      $status_incident = '<span class="badge badge-primary">' . $row_incident_check['status'] . '</span>';
    } else {
      $status_incident = '<span class="badge badge-warning">' . $row_incident_check['status'] . '</span>';
    }

    if ($row_incident_check['remarks'] == 'CLOSED') {
      $remarks_incident = '<span class="badge badge-success">' . $row_incident_check['remarks'] . '</span>';
    } else {
      $remarks_incident = '<span class="badge badge-danger">' . $row_incident_check['remarks'] . '</span>';
    }

    if ($row_incident_check['complainant_id'] == $edit_residence_id) {
      $color = 1;
      $delete_record = '
    <i 
      style="cursor: pointer; color: red; text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;" 
      class="fa fa-times text-lg px-2 deleteRecordComplainant" 
      data-id="' . $row_incident_check['complainant_id'] . '" 
      id="' . $row_incident_check['incident_main'] . '">
    </i>
';
    } else {
      $color = 2;
      $delete_record = '
        <i 
          style="cursor: pointer; color: red; text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;" 
          class="fa fa-times text-lg px-2 deleteRecordPerson" 
          data-id="' . $row_incident_check['person_id'] . '" 
          id="' . $row_incident_check['incident_main'] . '">
        </i>
      ';
    }

    $subdata = [];

   $subdata[] = '<input type="checkbox" class="sub_checkbox" id="'.$row_incident_check['incidentlog_id'].'">';
    $subdata[] = $row_incident_check['incidentlog_id'];
    $subdata[] = $status_incident;
    $subdata[] = $remarks_incident;
    $subdata[] = $row_incident_check['type_of_incident'];
    $subdata[] = $row_incident_check['location_incident'];
    $subdata[] = $date_incident;
    $subdata[] = $date_reported;
    $subdata[] = '<i style="cursor: pointer; color: yellow; text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;" class="fa fa-book-open text-lg px-2 viewRecords" id="' . $row_incident_check['incidentlog_id'] . '"></i>' . $delete_record;

    $data[] = $subdata;
  }

  $json_data = [
    'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
    'recordsTotal' => intval($totalDataIncident),
    'recordsFiltered' => intval($totalFilteredIncident),
    'data' => $data,
  ];

  echo json_encode($json_data);

} catch (Exception $e) {
  echo $e->getMessage();
}

?>
