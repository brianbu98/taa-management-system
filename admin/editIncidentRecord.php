<?php 

include_once '../connection.php';

try {

  $incidentlog_id = $_POST['incidentlog_id'];

  // If complainant residence is provided, use it, else set empty value
  if (isset($_POST['edit_complainant_residence']) && $_POST['edit_complainant_residence'] != '') {
    $complainant_incident_id = $_POST['edit_complainant_residence'];
  } else {
    $complainant_incident_id = [''];
  }

  // If person involved is provided, use it, else set empty value
  if (isset($_POST['edit_person_involed']) && $_POST['edit_person_involed'] != '') {
    $person_incident_id = $_POST['edit_person_involed'];
  } else {
    $person_incident_id = [''];
  }

  // Sanitize and store other POST data
  $edit_complainant_not_residence = $con->real_escape_string($_POST['edit_complainant_not_residence']);
  $edit_complainant_statement = $con->real_escape_string($_POST['edit_complainant_statement']);
  $edit_respodent = $con->real_escape_string($_POST['edit_respodent']);
  $edit_person_involevd_not_resident = $con->real_escape_string($_POST['edit_person_involevd_not_resident']);
  $edit_location_incident = $con->real_escape_string($_POST['edit_location_incident']);
  $edit_date_of_incident = $con->real_escape_string($_POST['edit_date_of_incident']);
  $edit_incident = $con->real_escape_string($_POST['edit_incident']);
  $edit_status = $con->real_escape_string($_POST['edit_status']);
  $edit_date_reported = $con->real_escape_string($_POST['edit_date_reported']);
  $edit_remarks = $con->real_escape_string($_POST['edit_remarks']);
  $edit_person_statement = $con->real_escape_string($_POST['edit_person_statement']);

  // Select complainants for the given incidentlog_id
  $sql_incident_select = "SELECT * FROM incident_complainant WHERE incident_main = ?";
  $stmt_incident_select = $con->prepare($sql_incident_select) or die ($con->error);
  $stmt_incident_select->bind_param('s', $incidentlog_id);
  $stmt_incident_select->execute();
  $result_incident_select = $stmt_incident_select->get_result();
  $stmt_incident_select->close();

  $complainant_array = [];
  foreach ($result_incident_select as $fetch_incident_select) {
    $complainant_array[] = $fetch_incident_select['complainant_id'];
  }

  // Insert new complainants
  foreach ($complainant_incident_id as $insertIncidentValue) {
    date_default_timezone_set('Asia/Manila');
    $date = new DateTime();
    $uniqid = uniqid(mt_rand() . $date->format("mDYHisv") . rand());
    $generate = md5(('see=') . $uniqid);
    $id = uniqid(rand()) . $generate;

    if (!in_array($insertIncidentValue, $complainant_array)) {
      $sql_incident_insert = "INSERT INTO incident_complainant (`id`, `incident_main`, `complainant_id`) VALUES (?, ?, ?)";
      $stmt_incident_insert = $con->prepare($sql_incident_insert) or die ($con->error);
      $stmt_incident_insert->bind_param('sss', $id, $incidentlog_id, $insertIncidentValue);
      $stmt_incident_insert->execute();
      $stmt_incident_insert->close();
    }
  }

  // Delete removed complainants
  foreach ($complainant_array as $fetch_incident_select) {
    if (!in_array($fetch_incident_select, $complainant_incident_id)) {
      $sql_incident_delete = "DELETE FROM incident_complainant WHERE incident_main = ? AND complainant_id = ?";
      $stmt_incident_delete = $con->prepare($sql_incident_delete) or die ($con->error);
      $stmt_incident_delete->bind_param('ss', $incidentlog_id, $fetch_incident_select);
      $stmt_incident_delete->execute();
      $stmt_incident_delete->close();
    }
  }

  // Select persons involved for the given incidentlog_id
  $sql_incident_select_person = "SELECT * FROM incident_status WHERE incident_main = ?";
  $stmt_incident_select_person = $con->prepare($sql_incident_select_person) or die ($con->error);
  $stmt_incident_select_person->bind_param('s', $incidentlog_id);
  $stmt_incident_select_person->execute();
  $result_incident_select_person = $stmt_incident_select_person->get_result();
  $stmt_incident_select_person->close();

  $person_array = [];
  foreach ($result_incident_select_person as $fetch_incident_select_person) {
    $person_array[] = $fetch_incident_select_person['person_id'];
  }

  // Insert new persons involved
  foreach ($person_incident_id as $insertIncidentValuePerson) {
    date_default_timezone_set('Asia/Manila');
    $date = new DateTime();
    $uniqid = uniqid(mt_rand() . $date->format("mDYHisv") . rand());
    $generate = md5(('seae=') . $uniqid);
    $ids = $generate . uniqid(rand());

    if (!in_array($insertIncidentValuePerson, $person_array)) {
      $sql_incident_insert_person = "INSERT INTO incident_status (`incident_id`, `incident_main`, `person_id`) VALUES (?, ?, ?)";
      $stmt_incident_insert_person = $con->prepare($sql_incident_insert_person) or die ($con->error);
      $stmt_incident_insert_person->bind_param('sss', $ids, $incidentlog_id, $insertIncidentValuePerson);
      $stmt_incident_insert_person->execute();
      $stmt_incident_insert_person->close();
    }
  }

  // Delete removed persons involved
  foreach ($person_array as $fetch_incident_select_person) {
    if (!in_array($fetch_incident_select_person, $person_incident_id)) {
      $sql_incident_delete_person = "DELETE FROM incident_status WHERE incident_main = ? AND person_id = ?";
      $stmt_incident_delete_person = $con->prepare($sql_incident_delete_person) or die ($con->error);
      $stmt_incident_delete_person->bind_param('ss', $incidentlog_id, $fetch_incident_select_person);
      $stmt_incident_delete_person->execute();
      $stmt_incident_delete_person->close();
    }
  }

  // Update the incident record
  $sql_update_record = "UPDATE `incident_record` SET 
  `complainant_not_residence`= ?, 
  `statement`= ?, 
  `respodent`= ?, 
  `involved_not_resident`= ?, 
  `date_incident`= ?, 
  `date_reported`= ?, 
  `type_of_incident`= ?, 
  `location_incident`= ?, 
  `status`= ?, 
  `remarks`= ?, 
  `statement_person` = ? 
  WHERE `incidentlog_id` = ?";
  $stmt_update_record = $con->prepare($sql_update_record) or die ($con->error);
  $stmt_update_record->bind_param('sssssssssssss',
    $edit_complainant_not_residence,
    $edit_complainant_statement,
    $edit_respodent,
    $edit_person_involevd_not_resident,
    $edit_date_of_incident,
    $edit_date_reported,
    $edit_incident,
    $edit_location_incident,
    $edit_status,
    $edit_remarks,
    $edit_person_statement,
    $incidentlog_id);
  $stmt_update_record->execute();
  $stmt_update_record->close();

} catch (Exception $e) {
  echo $e->getMessage();
}

?>
