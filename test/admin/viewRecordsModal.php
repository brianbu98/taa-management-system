<?php 
include_once '../connection.php';

try{
    if(isset($_REQUEST['record_id'])){
        $record_id = $con->real_escape_string(trim($_REQUEST['record_id']));
        $sql_record = "SELECT incident_record.*, incident_status.person_id, incident_complainant.complainant_id 
                       FROM incident_record
                       INNER JOIN incident_status ON incident_record.incidentlog_id = incident_status.incident_main
                       INNER JOIN incident_complainant ON incident_record.incidentlog_id = incident_status.incident_main 
                       WHERE incident_record.incidentlog_id = ?";
        $stmt_record = $con->prepare($sql_record) or die ($con->error);
        $stmt_record->bind_param('s',$record_id);
        $stmt_record->execute();
        $result_incident = $stmt_record->get_result();
        $row_record_incident = $result_incident->fetch_assoc();
    }
}catch(Exception $e){
    echo $e->getMessage();
}
?>

<!-- Modal -->
<div class="modal" id="viewIncidentRecordModal" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form id="editIncidentForm" method="post">

        <div class="modal-body">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-12">
                <input type="hidden" name="incidentlog_id" value="<?=$row_record_incident['incidentlog_id']?>">
              </div>

              <!-- Complainant Resident -->
              <div class="col-sm-12">
                <div class="form-group form-group-sm">
                  <label>Complainant Resident</label>
                  <select name="edit_complainant_residence[]" multiple="multiple" id="edit_complainant_residence" class="select2bs4" style="width: 100%;">
                    <option value="" id=""></option>
                    <?php 
                    $no = 'NO';
                    $sql_record_resident_id = "SELECT residence_information.residence_id,
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
                    while($row_record_resident_id = $result_resident_id->fetch_assoc()){
                        $record_person_middle = $row_record_resident_id['middle_name'] != '' ? $row_record_resident_id['middle_name'][0].'. ' : '';
                    ?>
                        <option value="<?= $row_record_resident_id['residence_id'] ?>" 
                          <?php
                          $sql_record_while_complainant = "SELECT incident_record.*, incident_status.person_id, incident_complainant.complainant_id 
                                                            FROM incident_record
                                                            INNER JOIN incident_status ON incident_record.incidentlog_id = incident_status.incident_main
                                                            INNER JOIN incident_complainant ON incident_record.incidentlog_id = incident_status.incident_main 
                                                            WHERE incident_complainant.incident_main = ?";
                          $stmt_record_while_complainant = $con->prepare($sql_record_while_complainant) or die($con->error);
                          $stmt_record_while_complainant->bind_param('s',$record_id);
                          $stmt_record_while_complainant->execute();
                          $result_incident_while_complainant = $stmt_record_while_complainant->get_result();
                          while($row_record_incident_while_complainant = $result_incident_while_complainant->fetch_assoc()){
                              if($row_record_resident_id['residence_id'] == $row_record_incident_while_complainant['complainant_id']){
                                  echo 'selected="selected"';
                              }else{
                                  echo '';
                              }
                          }

                          if($row_record_resident_id['image_path'] != '' || $row_record_resident_id['image_path'] != null || !empty($row_record_resident_id['image_path'])){
                              echo 'data-image="'.$row_record_resident_id['image_path'].'"';
                          }else{
                              echo 'data-image="../assets/dist/img/blank_image.png"';
                          }
                          ?>>
                          <?= $row_record_resident_id['last_name'].' '.$row_record_resident_id['first_name'].' '.$record_person_middle ?>
                        </option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <!-- Complainant Not Resident -->
              <div class="col-sm-12">
                <div class="form-group form-group-sm">
                  <label>Complainant Not Resident</label>
                  <textarea name="edit_complainant_not_residence" id="edit_complainant_not_residence" cols="57" class="bg-transparent text-white form-control"><?= $row_record_incident['complainant_not_residence'] ?></textarea>
                </div>
              </div>

              <!-- Complainant Statement -->
              <div class="col-sm-12">
                <div class="form-group form-group-sm">
                  <label>Complainant Statement</label>
                  <textarea name="edit_complainant_statement" id="edit_complainant_statement" cols="57" rows="3" class="bg-transparent text-white form-control"><?= $row_record_incident['statement'] ?></textarea>
                </div>
              </div>

              <!-- Respondent -->
              <div class="col-sm-12">
                <div class="form-group form-group-sm">
                  <label>Respondent</label>
                  <input name="edit_respodent" value="<?= $row_record_incident['respodent'] ?>" id="edit_respodent" class="form-control">
                </div>
              </div>

              <!-- Person Involved Resident -->
              <div class="col-sm-12">
                <div class="form-group form-group-sm">
                  <label>Person Involved Resident</label>
                  <select name="edit_person_involed[]" multiple="multiple" id="edit_person_involed" class="select2bs4" style="width: 100%;">
                    <option></option>
                    <?php 
                    $sql_person_id = "SELECT residence_information.residence_id,
                                             residence_information.first_name, 
                                             residence_information.middle_name,
                                             residence_information.last_name,
                                             residence_information.image,   
                                             residence_information.image_path
                                      FROM residence_information
                                      INNER JOIN residence_status ON residence_information.residence_id = residence_status.residence_id 
                                      WHERE archive = ? ORDER BY last_name ASC";
                    $query_person_id = $con->prepare($sql_person_id) or die ($con->error);
                    $query_person_id->bind_param('s',$no);
                    $query_person_id->execute();
                    $result_person_id = $query_person_id->get_result();
                    while($row_person_id = $result_person_id->fetch_assoc()){
                        $middle_person_id = $row_person_id['middle_name'] != '' ? $row_person_id['middle_name'][0].'. ' : '';
                    ?>
                        <option value="<?= $row_person_id['residence_id'] ?>" 
                          <?php
                          $sql_record_while_person = "SELECT incident_record.*, incident_status.person_id, incident_complainant.complainant_id 
                                                      FROM incident_record
                                                      INNER JOIN incident_status ON incident_record.incidentlog_id = incident_status.incident_main
                                                      INNER JOIN incident_complainant ON incident_record.incidentlog_id = incident_status.incident_main 
                                                      WHERE incident_status.incident_main = ?";
                          $stmt_record_while_person = $con->prepare($sql_record_while_person) or die ($con->error);
                          $stmt_record_while_person->bind_param('s',$record_id);
                          $stmt_record_while_person->execute();
                          $result_incident_while_person = $stmt_record_while_person->get_result();
                          while($row_record_incident_while_person = $result_incident_while_person->fetch_assoc()){
                              if($row_person_id['residence_id'] == $row_record_incident_while_person['person_id']){
                                  echo 'selected="selected"';
                              }else{
                                  echo '';
                              }
                          }

                          if($row_person_id['image_path'] != '' || $row_person_id['image_path'] != null || !empty($row_person_id['image_path'])){
                              echo 'data-image="'.$row_person_id['image_path'].'"';
                          }else{
                              echo 'data-image="../assets/dist/img/blank_image.png"';
                          }
                          ?>>
                          <?= $row_person_id['last_name'].' '.$row_person_id['first_name'].' '.$middle_person_id ?>
                        </option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <!-- Person Involved Not Resident -->
              <div class="col-sm-12">
                <div class="form-group form-group-sm">
                  <label>Person Involved Not Resident</label>
                  <textarea name="edit_person_involevd_not_resident" id="edit_person_involevd_not_resident" cols="57" class="bg-transparent text-white form-control"><?= $row_record_incident['involved_not_resident'] ?></textarea>
                </div>
              </div>

              <!-- Person Statement -->
              <div class="col-sm-12">
                <div class="form-group form-group-sm">
                  <label>Person Involved Statement</label>
                  <textarea name="edit_person_statement" id="edit_person_statement" cols="57" rows="3" class="bg-transparent text-white form-control"><?= $row_record_incident['statement_person'] ?></textarea>
                </div>
              </div>

              <!-- Location & Date -->
              <div class="col-sm-6">
                <div class="form-group form-group-sm">
                  <label>Location of Incident</label>
                  <input name="edit_location_incident" value="<?= $row_record_incident['location_incident'] ?>" id="edit_location_incident" class="form-control">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group form-group-sm">
                  <label>Date of Incident</label>
                  <input type="datetime-local" name="edit_date_of_incident" id="edit_date_of_incident" value="<?= $row_record_incident['date_incident']; ?>" class="form-control">
                </div>
              </div>

              <!-- Incident Type & Status -->
              <div class="col-sm-6">
                <div class="form-group form-group-sm">
                  <label>Incident</label>
                  <input name="edit_incident" id="edit_incident" class="form-control" value="<?= $row_record_incident['type_of_incident']; ?>">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group form-group-sm">
                  <label>Status</label>
                  <select name="edit_status" id="edit_status" class="form-control">
                    <option value="NEW" <?= $row_record_incident['status'] == 'NEW' ? 'selected': '' ?>>NEW</option>
                    <option value="ONGOING" <?= $row_record_incident['status'] == 'ONGOING' ? 'selected': '' ?>>ONGOING</option>
                  </select>
                </div>
              </div>

              <!-- Date Reported & Remarks -->
              <div class="col-sm-6">
                <div class="form-group form-group-sm">
                  <label>Date Reported</label>
                  <input type="datetime-local" name="edit_date_reported" id="edit_date_reported" class="form-control" value="<?= $row_record_incident['date_reported']; ?>">
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group form-group-sm">
                  <label>Remarks</label>
                  <select name="edit_remarks" id="edit_remarks" class="form-control">
                    <option value="OPEN" <?= $row_record_incident['remarks'] == 'OPEN' ? 'selected': '' ?>>OPEN</option>
                    <option value="CLOSED" <?= $row_record_incident['remarks'] == 'CLOSED' ? 'selected': '' ?>>CLOSED</option>
                  </select>
                </div>
              </div>

            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn bg-black elevation-5 px-3" data-dismiss="modal"><i class="fas fa-times"></i> CLOSE</button>
          <button type="submit" class="btn btn-primary elevation-5 px-3 btn-flat"><i class="fa fa-edit"></i> UPDATE CASE</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- JS Scripts -->
<script>
$(document).ready(function(){
    // Validation & AJAX (kept identical to old code)
    $.validator.setDefaults({
      submitHandler: function(form){
          $.ajax({
              url: 'editIncidentRecord.php',
              type: 'POST',
              data: $(form).serialize(),
              cache: false,
              success:function(){
                  $("#viewIncidentRecordModal").modal('hide');
                  Swal.fire({
                      title: '<strong class="text-success">SUCCESS</strong>',
                      type: 'success',
                      html: '<b>Added Record Incident has Successfully<b>',
                      width: '400px',
                      confirmButtonColor: '#6610f2',
                      allowOutsideClick: false,
                      showConfirmButton: false,
                      timer: 2000,
                  }).then(()=>{
                      $("#incidentRecordTable").DataTable().ajax.reload();
                      $('#edit_complainant_residence').empty();
                      $('#edit_person_involed').empty();
                  })
              }
          }).fail(function(){
              Swal.fire({
                  title: '<strong class="text-danger">Ooppss..</strong>',
                  type: 'error',
                  html: '<b>Something went wrong with ajax !<b>',
                  width: '400px',
                  confirmButtonColor: '#6610f2',
              })
          })
      }
    });

    $('#editIncidentForm').validate({
      ignore: "",
      rules: {
        edit_date_reported: { required: true },
        edit_incident: { required: true }
      },
      messages: {
        edit_date_reported: { required: "Please provide a Date Reported is Required" },
        edit_incident: { required: "Incident is Required" }
      },
      errorElement: 'span',
      errorPlacement: function(error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
      },
      highlight: function(element){ $(element).addClass('is-invalid'); },
      unhighlight: function(element){ $(element).removeClass('is-invalid'); }
    });

    // select2 & AJAX for showing residence info
    $("#edit_complainant_residence, #edit_person_involed").on('select2:select', function(e){
        var residence_id = e.params.data.id;
        $("#show_residence").html('');
        $.ajax({
            url: 'showResidence.php',
            type: 'POST',
            data:{ residence_id:residence_id },
            cache: false,
            success:function(data){
                $("#show_residence").html(data);
                $("#viewResidenceModal").modal('show');
            }
        }).fail(function(){
            Swal.fire({title:'<strong class="text-danger">Ooppss..</strong>', type:'error', html:'<b>Something went wrong with ajax !<b>', width:'400px', confirmButtonColor:'#6610f2'})
        })
    });

    // Input filters (identical to old code)
    $("#edit_complainant_not_residence, #edit_person_involevd_not_resident").inputFilter(function(value) {
        return /^[a-z, ]*$/i.test(value); 
    });
    $("#edit_complainant_statement, #edit_respodent,#edit_incident,#edit_location_incident,#edit_person_statement").inputFilter(function(value) {
        return /^[0-9a-z, ,-]*$/i.test(value); 
    });

    // select2 formatting (duplicated to mimic old code)
    function formatState (opt) {
      if (!opt.id) return opt.text.toUpperCase();
      var optimage = $(opt.element).attr('data-image'); 
      if(!optimage) return opt.text.toUpperCase();
      var $opt = $('<span><img class="img-circle pb-1" src="' + optimage + '" width="20px"/> ' + opt.text.toUpperCase() + '</span>');
      return $opt;
    }

    $('#edit_complainant_residence').select2({ templateResult: formatState, templateSelection: formatState, theme: 'bootstrap4', dropdownParent: $('.modal'), language:{ noResults: function(){ return "No Record"; }} });
    $('#edit_person_involed').select2({ templateResult: formatState, templateSelection: formatState, theme: 'bootstrap4', dropdownParent: $('.modal'), language:{ noResults: function(){ return "No Record"; }} });

});
</script>
