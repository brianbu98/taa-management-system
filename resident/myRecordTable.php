<?php
header('Content-Type: application/json');
include_once '../connection.php';

try {
    $edit_residence_id = $con->real_escape_string($_REQUEST['edit_residence_id'] ?? '');
    $draw = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 0;

    $sql = "
      SELECT 
          incident_record.*, 
          incident_status.*, 
          incident_complainant.*, 
          incident_record.incidentlog_id AS comp
   FROM incident_record
LEFT JOIN incident_complainant 
    ON incident_record.incidentlog_id = incident_complainant.incident_main
LEFT JOIN incident_status 
    ON incident_record.incidentlog_id = incident_status.incident_main
WHERE incident_record.complainant_not_residence = ?
      GROUP BY incident_record.incidentlog_id
    ";

    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new Exception("Database prepare failed: " . $con->error);
    }

   $stmt->bind_param('s', $edit_residence_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    date_default_timezone_set('Asia/Manila');

    while ($row = $result->fetch_assoc()) {
        $date_incident = date("m/d/Y - h:i A", strtotime($row['date_incident']));
        $date_reported = date("m/d/Y - h:i A", strtotime($row['date_reported']));

        // --- Status badge ---
        $status = ($row['status'] == 'NEW')
            ? '<span class="badge badge-primary">'.$row['status'].'</span>'
            : '<span class="badge badge-warning">'.$row['status'].'</span>';

        // --- Remarks badge ---
        $remarks = ($row['remarks'] == 'CLOSED')
            ? '<span class="badge badge-success">'.$row['remarks'].'</span>'
            : '<span class="badge badge-danger">'.$row['remarks'].'</span>';

        // --- Color (who filed the report) ---
        $color = ($row['complainant_id'] == $edit_residence_id) ? 1 : 2;

        // --- Action buttons ---
        $viewBtn = '
            <i class="fa fa-book-open text-lg px-2 viewRecords" 
               style="cursor:pointer;color:yellow;text-shadow:-1px 0 black,0 1px black,1px 0 black,0 -1px black;" 
               id="'.$row['comp'].'"></i>
        ';

        // ? Now we include the new `incident_number`
        $data[] = [
            $color,
            htmlspecialchars($row['incident_number']),  // new column
            $status,
            $remarks,
            htmlspecialchars($row['type_of_incident']),
            htmlspecialchars($row['location_incident']),
            $date_incident,
            $date_reported,
            $viewBtn
        ];
    }

    // DataTables JSON response
    $json_data = [
        "draw" => $draw,
        "recordsTotal" => count($data),
        "recordsFiltered" => count($data),
        "data" => $data
    ];

    echo json_encode($json_data);

} catch (Exception $e) {
    echo json_encode([
        "draw" => 0,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => $e->getMessage()
    ]);
}
?>
