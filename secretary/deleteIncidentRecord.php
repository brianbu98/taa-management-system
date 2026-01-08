<?php 

include_once '../connection.php';
session_start();

if(isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'secretary'){
  
  $user_id = $_SESSION['user_id'];
  $sql_user = "SELECT * FROM `users` WHERE `id` = ? ";
  $stmt_user = $con->prepare($sql_user) or die ($con->error);
  $stmt_user->bind_param('s',$user_id);
  $stmt_user->execute();
  $result_user = $stmt_user->get_result();
  $row_user = $result_user->fetch_assoc();
  $first_name_user = $row_user['first_name'];
  $last_name_user = $row_user['last_name'];
  $user_type = $row_user['user_type'];
  $user_image = $row_user['image'];
  
}else{
  echo '<script>
          window.location.href = "../login.php";
        </script>';
}

try {

  if(isset($_REQUEST['id'])){
    $incidentlog_id = $con->real_escape_string($_REQUEST['id']);

    $sql_incident = "SELECT * FROM incident_record WHERE incidentlog_id IN ($incidentlog_id)";
    $stmt_incident = $con->prepare($sql_incident) or die ($con->error);
    $stmt_incident->execute();
    $result_incident = $stmt_incident->get_result();
    $row_incident = $result_incident->fetch_assoc();

    $old_date_incident = $row_incident['date_incident'];
    $old_date_reported = $row_incident['date_reported'];
    $old_location_incident = $row_incident['location_incident'];

    $date_activity = $now = date("j-n-Y g:i A");  
    $admin = strtoupper('OFFICIAL').': ' .$first_name_user.' '.$last_name_user. ' - ' .$user_id.' | '.  'DELETED INCIDENT RECORD - '.' ' .$incidentlog_id.' | ' . $old_date_incident.' ' . $old_date_reported. ' ' . $old_location_incident;
    $status_activity_log = 'delete';
    $sql_activity_log = "INSERT INTO activity_log (`message`,`date`,`status`) VALUES (?,?,?)";
    $stmt_activity_log = $con->prepare($sql_activity_log) or die ($con->error);
    $stmt_activity_log->bind_param('sss',$admin,$date_activity,$status_activity_log);
    $stmt_activity_log->execute();
    $stmt_activity_log->close();

    $sql_delete_record = "DELETE FROM incident_record WHERE incidentlog_id IN ($incidentlog_id)";
    $stmt_delete_record = $con->query($sql_delete_record) or die ($con->error);

    $sql_record_complainant = "DELETE FROM incident_complainant WHERE incident_main IN ($incidentlog_id)";
    $stmt_record_complainant = $con->query($sql_record_complainant) or die ($con->error);

    $sql_incident_person = "DELETE FROM incident_status WHERE incident_main IN ($incidentlog_id)";
    $stmt_incident_person = $con->query($sql_incident_person) or die ($con->error);

  }

} catch(Exception $e) {
  echo $e->getMessage();
}

?>
