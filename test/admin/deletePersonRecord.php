<?php 

include_once '../connection.php';

try {

  if (isset($_REQUEST['incidentlog_id']) && isset($_REQUEST['person_id'])) {
    $incidentlog_id = $con->real_escape_string($_REQUEST['incidentlog_id']);
    $person_id = $con->real_escape_string($_REQUEST['person_id']);
    $blank = '';

    // Updated SQL query
    $sql_delete_person_record = "UPDATE incident_status SET person_id = ? WHERE incident_main = ? AND person_id = ?";
    $stmt_delete_person_record = $con->prepare($sql_delete_person_record) or die($con->error);
    $stmt_delete_person_record->bind_param('sss', $blank, $incidentlog_id, $person_id);
    $stmt_delete_person_record->execute();
    $stmt_delete_person_record->close();

  }

} catch (Exception $e) {
  echo $e->getMessage();
}

?>
