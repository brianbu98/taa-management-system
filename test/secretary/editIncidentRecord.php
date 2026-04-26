<?php
include_once '../connection.php';

try {
    // Begin a transaction for data consistency
    $con->begin_transaction();

    // ====== 1?? GET POST DATA ======
    $incidentlog_id = $_POST['incidentlog_id'];

    // Handle array-type POST fields safely
    $incident_complainant_ids = isset($_POST['edit_complainant_residence']) && !empty($_POST['edit_complainant_residence'])
        ? $_POST['edit_complainant_residence']
        : [];

    $incident_person_ids = isset($_POST['edit_person_involved']) && !empty($_POST['edit_person_involved'])
        ? $_POST['edit_person_involved']
        : [];

    // Sanitize single-value inputs (no need for real_escape_string since we use prepared statements)
    $edit_complainant_not_resident   = $_POST['edit_complainant_not_residence'] ?? '';
    $edit_complainant_statement      = $_POST['edit_complainant_statement'] ?? '';
    $edit_respondent                 = $_POST['edit_respondent'] ?? '';
    $edit_person_involved_not_resident = $_POST['edit_person_involved_not_resident'] ?? '';
    $edit_location_incident          = $_POST['edit_location_incident'] ?? '';
    $edit_date_of_incident           = $_POST['edit_date_of_incident'] ?? '';
    $edit_incident_type              = $_POST['edit_incident'] ?? '';
    $edit_status                     = $_POST['edit_status'] ?? '';
    $edit_date_reported              = $_POST['edit_date_reported'] ?? '';
    $edit_remarks                    = $_POST['edit_remarks'] ?? '';
    $edit_person_statement           = $_POST['edit_person_statement'] ?? '';


    // Helper function for generating unique IDs
    function generateUniqueId($prefix = 'id') {
        return md5($prefix . uniqid(mt_rand(), true));
    }

    date_default_timezone_set('Asia/Manila');

    // ====== 2?? UPDATE COMPLAINANTS ======

    // Get current complainants
    $sql_select_complainant = "SELECT complainant_id FROM incident_complainant WHERE incident_main = ?";
    $stmt = $con->prepare($sql_select_complainant);
    $stmt->bind_param('s', $incidentlog_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_complainants = array_column($result->fetch_all(MYSQLI_ASSOC), 'complainant_id');
    $stmt->close();

    // Insert new complainants if not existing
    foreach ($incident_complainant_ids as $complainant_id) {
        if (!in_array($complainant_id, $existing_complainants)) {
            $id = generateUniqueId('incident_complainant');
            $sql_insert = "INSERT INTO incident_complainant (`id`, `incident_main`, `complainant_id`) VALUES (?, ?, ?)";
            $stmt = $con->prepare($sql_insert);
            $stmt->bind_param('sss', $id, $incidentlog_id, $complainant_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Delete removed complainants
    foreach ($existing_complainants as $existing_id) {
        if (!in_array($existing_id, $incident_complainant_ids)) {
            $sql_delete = "DELETE FROM incident_complainant WHERE incident_main = ? AND complainant_id = ?";
            $stmt = $con->prepare($sql_delete);
            $stmt->bind_param('ss', $incidentlog_id, $existing_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    // ====== 3?? UPDATE PERSONS INVOLVED ======

    // Get current persons
    $sql_select_person = "SELECT person_id FROM incident_status WHERE incident_main = ?";
    $stmt = $con->prepare($sql_select_person);
    $stmt->bind_param('s', $incidentlog_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_persons = array_column($result->fetch_all(MYSQLI_ASSOC), 'person_id');
    $stmt->close();

    // Insert new persons
    foreach ($incident_person_ids as $person_id) {
        if (!in_array($person_id, $existing_persons)) {
            $id = generateUniqueId('incident_status');
            $sql_insert = "INSERT INTO incident_status (`incident_id`, `incident_main`, `person_id`) VALUES (?, ?, ?)";
            $stmt = $con->prepare($sql_insert);
            $stmt->bind_param('sss', $id, $incidentlog_id, $person_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Delete removed persons
    foreach ($existing_persons as $existing_person_id) {
        if (!in_array($existing_person_id, $incident_person_ids)) {
            $sql_delete = "DELETE FROM incident_status WHERE incident_main = ? AND person_id = ?";
            $stmt = $con->prepare($sql_delete);
            $stmt->bind_param('ss', $incidentlog_id, $existing_person_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    // ====== 4?? UPDATE MAIN INCIDENT RECORD ======

    $sql_update = "
        UPDATE `incident_record`
        SET 
            `complainant_not_resident` = ?,
            `statement` = ?,
            `respondent` = ?,
            `involved_not_resident` = ?,
            `date_incident` = ?,
            `date_reported` = ?,
            `type_of_incident` = ?,
            `location_incident` = ?,
            `status` = ?,
            `remarks` = ?,
            `statement_person` = ?
        WHERE `incidentlog_id` = ?
    ";
    $stmt = $con->prepare($sql_update);
    $stmt->bind_param(
        'ssssssssssss',
        $edit_complainant_not_resident,
        $edit_complainant_statement,
        $edit_respondent,
        $edit_person_involved_not_resident,
        $edit_date_of_incident,
        $edit_date_reported,
        $edit_incident_type,
        $edit_location_incident,
        $edit_status,
        $edit_remarks,
        $edit_person_statement,
        $incidentlog_id
    );
    $stmt->execute();
    $stmt->close();

    // Commit all changes
    $con->commit();

    echo json_encode(['status' => 'success', 'message' => 'Incident record updated successfully.']);

} catch (Exception $e) {
    // Rollback in case of any error
    $con->rollback();
    error_log("Incident update failed: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An error occurred while updating the incident.']);
}
?>
