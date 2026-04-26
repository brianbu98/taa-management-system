<?php 

include_once '../connection.php';

try{

  if(isset($_REQUEST['edit_residence_id'])){

    $edit_residence_id = $con->real_escape_string(trim($_REQUEST['edit_residence_id']));

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

    $query_incident_check = $con->prepare($sql_incident_check) or die ($con->error);
    $query_incident_check->bind_param('ss', $edit_residence_id, $edit_residence_id);
    $query_incident_check->execute();
    $result_incident_check = $query_incident_check->get_result();

    $totalDataIncident = $result_incident_check->num_rows;
    $totalFilteredIncident = $totalDataIncident;

    $data = [];

    while($row_incident_check = $result_incident_check->fetch_assoc()){

      date_default_timezone_set('Asia/Manila');

      $date_incident = date("m/d/Y - h:i A", strtotime($row_incident_check['date_incident']));
      $date_reported = date("m/d/Y - h:i A", strtotime($row_incident_check['date_reported']));

      // ==============================
      // STATUS DISPLAY COLOR BADGE
      // ==============================
      if($row_incident_check['status'] == 'NEW'){
        $status_incident = '<span class="badge badge-primary">'.$row_incident_check['status'] .'</span>';
      }else{
        $status_incident = '<span class="badge badge-warning">'.$row_incident_check['status'] .'</span>';
      }

      // ==============================
      // REMARKS DISPLAY COLOR BADGE
      // ==============================
      if($row_incident_check['remarks'] == 'CLOSED'){
        $remarks_incident = '<span class="badge badge-success">'.$row_incident_check['remarks'] .'</span>';
      }else{
        $remarks_incident = '<span class="badge badge-danger">'.$row_incident_check['remarks'] .'</span>';
      }

      // ==============================
      // CHECK IF PERSON IS COMPLAINANT OR RESPONDENT
      // ==============================
      if($row_incident_check['complainant_id'] == $edit_residence_id){

        $color = 1;

        $delete_record = '
        <i 
          style="cursor: pointer; color: red; text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;" 
          class="fa fa-times text-lg px-2 deleteRecordComplainant" 
          data-id="'.$row_incident_check['complainant_id'].'" 
          id="'.$row_incident_check['incident_main'].'">
        </i>
        ';

      }else{

        $color = 2;

        $delete_record = '
        <i 
          style="cursor: pointer; color: red; text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;" 
          class="fa fa-times text-lg px-2 deleteRecordPerson" 
          data-id="'.$row_incident_check['person_id'].'" 
          id="'.$row_incident_check['incident_main'].'">
        </i>
        ';
      }

      // ==============================
      // TABLE DATA ROWS
      // ==============================
      $subdata = [];

      // Color indicator (complainant or respondent)
      $subdata[] = $color;

      // Incident unique ID
      $subdata[] = $row_incident_check['incidentlog_id'];

      // Status badge
      $subdata[] = $status_incident;

      // Remarks badge
      $subdata[] = $remarks_incident;

      // Type of incident
      $subdata[] = $row_incident_check['type_of_incident'];

      // Location of incident
      $subdata[] = $row_incident_check['location_incident'];

      // Date of incident
      $subdata[] = $date_incident;

      // Date reported
      $subdata[] = $date_reported;

      // ==============================
      // OPTIONAL ACTION BUTTONS (commented)
      // ==============================
      // $subdata[] = '
      //   <i 
      //     style="cursor: pointer; color: yellow; text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;" 
      //     class="fa fa-book-open text-lg px-2 viewRecord" 
      //     id="'.$row_incident_check['incidentlog_id'].'">
      //   </i>
      //   '.$delete_record.'
      // ';

      // Push row data
      $data[] = $subdata;

    } // end while

    // ==============================
    // FINAL JSON OUTPUT
    // ==============================
    $json_data = [
      'draw' => intval($_REQUEST['draw']),
      'recordsTotal' => intval($totalDataIncident),
      'recordsFiltered' => intval($totalFilteredIncident),
      'data' => $data,
    ];

    echo json_encode($json_data);

  } // end if edit_residence_id

}catch(Exception $e){

  echo $e->getMessage();

}

?>
