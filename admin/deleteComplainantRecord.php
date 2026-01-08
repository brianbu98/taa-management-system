







<?php 

include_once '../connection.php';

try{

  if(isset($_REQUEST['incidentlog_id']) && isset($_REQUEST['complainant_id'])){
    $incidentlog_id = $con->real_escape_string($_REQUEST['incidentlog_id']);
    $complainant_id = $con->real_escape_string($_REQUEST['complainant_id']);
    $blank = '';

    $sql_delete_complainant_record = "UPDATE incident_complainant SET complainant_id = ? WHERE incident_main = ? AND complainant_id = ?";
    $stmt_delete_complainant_record = $con->prepare($sql_delete_complainant_record) or die ($con->error);
    $stmt_delete_complainant_record->bind_param('sss',$blank,$incidentlog_id,$complainant_id);
    $stmt_delete_complainant_record->execute();
    $stmt_delete_complainant_record->close();

  }

}catch(Exception $e){
  echo $e->getMessage();
}

?>
