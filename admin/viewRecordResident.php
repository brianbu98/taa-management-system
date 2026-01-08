<?php 
include_once '../connection.php';

try {
  if (isset($_REQUEST['id'])) {

    $id = $con->real_escape_string(trim($_REQUEST['id']));

    // Main incident record query
    $sql_record = "SELECT ir.*, ist.person_id, ic.complainant_id
                   FROM incident_record ir
                   INNER JOIN incident_status ist ON ir.incidentlog_id = ist.incident_main
                   INNER JOIN incident_complainant ic ON ir.incidentlog_id = ic.incident_main
                   WHERE ir.incidentlog_id = ?";
    $stmt_record = $con->prepare($sql_record) or die($con->error);
    $stmt_record->bind_param('s', $id);
    $stmt_record->execute();
    $result_incident = $stmt_record->get_result();
    $row_record_incident = $result_incident->fetch_assoc();

    if (!$row_record_incident) {
      echo "<div class='alert alert-warning'>No incident record found.</div>";
      exit;
    }

    // Fetch complainant IDs once
    $complainant_ids = [];
    $sql_complainants = "SELECT complainant_id FROM incident_complainant WHERE incident_main = ?";
    $stmt_c = $con->prepare($sql_complainants);
    $stmt_c->bind_param('s', $id);
    $stmt_c->execute();
    $result_c = $stmt_c->get_result();
    while ($row_c = $result_c->fetch_assoc()) {
      $complainant_ids[] = $row_c['complainant_id'];
    }

    // Fetch person involved IDs once
    $person_ids = [];
    $sql_persons = "SELECT person_id FROM incident_status WHERE incident_main = ?";
    $stmt_p = $con->prepare($sql_persons);
    $stmt_p->bind_param('s', $id);
    $stmt_p->execute();
    $result_p = $stmt_p->get_result();
    while ($row_p = $result_p->fetch_assoc()) {
      $person_ids[] = $row_p['person_id'];
    }

  }
} catch (Exception $e) {
  echo $e->getMessage();
}
?>

<style>
.dark-mode .select2-selection {
  background-color: #343a40;
  border-color: #6c757d;
}
.select2-container--bootstrap4.select2-container--disabled .select2-selection,
.select2-container--bootstrap4.select2-container--disabled.select2-container--focus .select2-selection {
  background-color: transparent;
  background: transparent;
}
.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
  border: none;
}
</style>

<!-- Modal -->
<div class="modal" id="viewResidentRecordModal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">

            <!-- Complainant Resident -->
            <div class="col-sm-12">
              <div class="form-group form-group-sm">
                <label>Complainant Resident</label>
                <select name="edit_complainant_residence[]" multiple="multiple" id="edit_complainant_residence" class="select2bs4" style="width: 100%;" disabled>
                  <option value=""></option>
                  <?php 
                    $no = 'NO';
                    $sql_record_resident_id = "SELECT
                      residence_information.residence_id,
                      residence_information.first_name, 
                      residence_information.middle_name,
                      residence_information.last_name,
                      residence_information.image,   
                      residence_information.image_path
                      FROM residence_information
                      INNER JOIN residence_status ON residence_information.residence_id = residence_status.residence_id
                      WHERE archive = ?
                      ORDER BY last_name ASC";
                    $query_record_resident_id = $con->prepare($sql_record_resident_id) or die($con->error);
                    $query_record_resident_id->bind_param('s', $no);
                    $query_record_resident_id->execute();
                    $result_resident_id = $query_record_resident_id->get_result();
                    while ($row_record_resident_id = $result_resident_id->fetch_assoc()) {
                      $record_person_middle = $row_record_resident_id['middle_name'] != '' 
                        ? $row_record_resident_id['middle_name'][0] . '. ' 
                        : '';
                      $selected = in_array($row_record_resident_id['residence_id'], $complainant_ids) ? 'selected' : '';
                      $imagePath = (!empty($row_record_resident_id['image_path'])) ? $row_record_resident_id['image_path'] : '../assets/dist/img/blank_image.png';
                  ?>
                    <option value="<?= $row_record_resident_id['residence_id'] ?>" <?= $selected ?> data-image="<?= $imagePath ?>">
                      <?= $row_record_resident_id['last_name'] . ' ' . $row_record_resident_id['first_name'] . ' ' . $record_person_middle ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <!-- Complainant Not Resident -->
            <div class="col-sm-12">
              <div class="form-group form-group-sm">
                <label>Complainant Not Resident</label>
                <textarea name="edit_complainant_not_residence" id="edit_complainant_not_residence" cols="57" disabled class="bg-transparent text-white form-control"><?= htmlspecialchars($row_record_incident['complainant_not_residence'] ?? '') ?></textarea>
              </div>
            </div>

            <!-- Complainant Statement -->
            <div class="col-sm-12">
              <div class="form-group form-group-sm">
                <label>Complainant Statement</label>
                <textarea name="edit_complainant_statement" id="edit_complainant_statement" cols="57" disabled rows="3" class="bg-transparent text-white form-control"><?= htmlspecialchars($row_record_incident['statement'] ?? '') ?></textarea>
              </div>
            </div>

            <!-- Respondent -->
            <div class="col-sm-12">
              <div class="form-group form-group-sm">
                <label>Respondent</label>
                <input name="edit_respondent" value="<?= htmlspecialchars($row_record_incident['respondent'] ?? '') ?>" id="edit_respondent" disabled class="form-control">
              </div>
            </div>

            <!-- Person Involved Resident -->
            <div class="col-sm-12">
              <div class="form-group form-group-sm">
                <label>Person Involved Resident</label>
                <select name="edit_person_involved[]" multiple="multiple" id="edit_person_involved" class="select2bs4" disabled style="width: 100%;">
                  <option></option>
                  <?php 
                    $query_person_id = $con->prepare($sql_record_resident_id) or die($con->error);
                    $query_person_id->bind_param('s', $no);
                    $query_person_id->execute();
                    $result_person_id = $query_person_id->get_result();
                    while ($row_person_id = $result_person_id->fetch_assoc()) {
                      $middle_person_id = $row_person_id['middle_name'] != '' ? $row_person_id['middle_name'][0] . '. ' : '';
                      $selected = in_array($row_person_id['residence_id'], $person_ids) ? 'selected' : '';
                      $imagePath = (!empty($row_person_id['image_path'])) ? $row_person_id['image_path'] : '../assets/dist/img/blank_image.png';
                  ?>
                    <option value="<?= $row_person_id['residence_id'] ?>" <?= $selected ?> data-image="<?= $imagePath ?>">
                      <?= $row_person_id['last_name'] . ' ' . $row_person_id['first_name'] . ' ' . $middle_person_id ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
            </div>

            <!-- Person Involved Not Resident -->
            <div class="col-sm-12">
              <div class="form-group form-group-sm">
                <label>Person Involved Not Resident</label>
                <textarea name="edit_person_involved_not_resident" disabled id="edit_person_involved_not_resident" cols="57" class="bg-transparent text-white form-control"><?= htmlspecialchars($row_record_incident['involved_not_resident'] ?? '') ?></textarea>
              </div>
            </div>

            <!-- Person Involved Statement -->
            <div class="col-sm-12">
              <div class="form-group form-group-sm">
                <label>Person Involved Statement</label>
                <textarea name="edit_person_statement" disabled id="edit_person_statement" cols="57" rows="3" class="bg-transparent text-white form-control"><?= htmlspecialchars($row_record_incident['statement_person'] ?? '') ?></textarea>
              </div>
            </div>

            <!-- Location of Incident -->
            <div class="col-sm-6">
              <div class="form-group form-group-sm">
                <label>Location of Incident</label>
                <input name="edit_location_incident" disabled value="<?= htmlspecialchars($row_record_incident['location_incident'] ?? '') ?>" id="edit_location_incident" class="form-control">
              </div>
            </div>

            <!-- Date of Incident -->
            <div class="col-sm-6">
              <div class="form-group form-group-sm">
                <label>Date of Incident</label>
                <input type="datetime-local" disabled name="edit_date_of_incident" id="edit_date_of_incident" value="<?= $row_record_incident['date_incident'] ?? '' ?>" class="form-control">
              </div>
            </div>

            <!-- Type of Incident -->
            <div class="col-sm-6">
              <div class="form-group form-group-sm">
                <label>Incident</label>
                <input name="edit_incident" disabled id="edit_incident" class="form-control" value="<?= htmlspecialchars($row_record_incident['type_of_incident'] ?? '') ?>">
              </div>
            </div>

            <!-- Status -->
            <div class="col-sm-6">
              <div class="form-group form-group-sm">
                <label>Status</label>
                <select name="edit_status" id="edit_status" class="form-control" disabled>
                  <option value="NEW" <?= ($row_record_incident['status'] ?? '') == 'NEW' ? 'selected' : '' ?>>NEW</option>
                  <option value="ONGOING" <?= ($row_record_incident['status'] ?? '') == 'ONGOING' ? 'selected' : '' ?>>ONGOING</option>
                </select>
              </div>
            </div>

            <!-- Date Reported -->
            <div class="col-sm-6">
              <div class="form-group form-group-sm">
                <label>Date Reported</label>
                <input type="datetime-local" disabled name="edit_date_reported" id="edit_date_reported" class="form-control" value="<?= $row_record_incident['date_reported'] ?? '' ?>">
              </div>
            </div>

            <!-- Remarks -->
            <div class="col-sm-6">
              <div class="form-group form-group-sm">
                <label>Remarks</label>
                <select name="edit_remarks" id="edit_remarks" class="form-control" disabled>
                  <option value="OPEN" <?= ($row_record_incident['remarks'] ?? '') == 'OPEN' ? 'selected' : '' ?>>OPEN</option>
                  <option value="CLOSED" <?= ($row_record_incident['remarks'] ?? '') == 'CLOSED' ? 'selected' : '' ?>>CLOSED</option>
                </select>
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn bg-black elevation-5 px-3" data-dismiss="modal"><i class="fas fa-times"></i> CLOSE</button>
      </div>
    </div>
  </div>
</div>

<script>
function formatState(opt) {
  if (!opt.id) return opt.text.toUpperCase();
  var optimage = $(opt.element).attr('data-image');
  if (!optimage) return opt.text.toUpperCase();
  var $opt = $('<span><img class="img-circle pb-1" src="' + optimage + '" width="20px" /> ' + opt.text.toUpperCase() + '</span>');
  return $opt;
}

$('#edit_complainant_residence, #edit_person_involved').select2({
  templateResult: formatState,
  templateSelection: formatState,
  theme: 'bootstrap4',
  dropdownParent: $('.modal'),
  language: { noResults: function() { return "No Record"; } }
});
</script>
