<?php 

include_once '../connection.php';

try{

  if(isset($_REQUEST['record_id'])){

    $record_id = $con->real_escape_string(trim($_REQUEST['record_id']));
    $sql_record = "SELECT incident_record.*, incident_status.person_id, incident_complainant.complainant_id FROM incident_record
    INNER JOIN incident_status ON incident_record.incidentlog_id = incident_status.incident_main
    INNER JOIN incident_complainant ON incident_record.incidentlog_id = incident_status.incident_main WHERE incident_record.incidentlog_id = ?";
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

<style>
  .dark-mode .select2-selection{
      background-color: #343a40;
        border-color: #6c757d;
    }
    .select2-container--bootstrap4.select2-container--disabled .select2-selection, .select2-container--bootstrap4.select2-container--disabled.select2-container--focus .select2-selection{
      background-color: transparent;
      background: transparent;
    
    }
    .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice{
      border: none;
    }
     
.modal-body{
    height: 80vh;
    overflow-y: auto;
}
.modal-body::-webkit-scrollbar {
    width: 5px;
}                                                    
.modal-body::-webkit-scrollbar-thumb {
    background: #6c757d; 
    --webkit-box-shadow: inset 0 0 6px #6c757d; 
}
.modal-body::-webkit-scrollbar-thumb:window-inactive {
  background: #6c757d; 
}
</style>

<!-- Modal -->
<div class="modal " id="viewIncidentRecordModal" data-backdrop="static" data-keyboard="false"  role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <form id="editRecordForm" method="post">

      <div class="modal-body">
        <div class="container-fluid">

          <div class="row">
              <div class="col-sm-12"></div>

                <div class="col-sm-12">
                    <div class="form-group form-group-sm">
                        <label>Complainant Resident</label>
                      <select name="edit_complainant_residence[]" multiple="multiple" id="edit_complainant_residence" class="select2bs4"  style="width: 100%;" disabled>
                        <option value="" id=""></option>
                        <?php 
                        $no = 'NO';
                          $sql_record_resdident_id = "SELECT
                          residence_information.residence_id,
                          residence_information.first_name, 
                          residence_information.middle_name,
                          residence_information.last_name,
                          residence_information.image,   
                          residence_information.image_path
                          FROM residence_information
                          INNER JOIN residence_status ON residence_information.residence_id = residence_status.residence_id WHERE archive = ?
                          ORDER BY last_name ASC ";
                          $query_record_resident_id = $con->prepare($sql_record_resdident_id) or die ($con->error);
                          $query_record_resident_id->bind_param('s',$no);
                          $query_record_resident_id->execute();
                          $result_resident_id = $query_record_resident_id->get_result();
                          while($row_record_resident_id = $result_resident_id->fetch_assoc()){
                            if($row_record_resident_id['middle_name'] != ''){
                              $record_person_middle = $row_record_resident_id['middle_name'][0].'.'.' '; 
                            }else{
                              $record_person_middle = $row_record_resident_id['middle_name'].' '; 
                            }
                            ?>
                              <option  value="<?= $row_record_resident_id['residence_id'] ?>" <?php

                                $sql_record_while_complainant = "SELECT incident_record.*, incident_status.person_id, incident_complainant.complainant_id FROM incident_record
                                INNER JOIN incident_status ON incident_record.incidentlog_id = incident_status.incident_main
                                INNER JOIN incident_complainant ON incident_record.incidentlog_id = incident_status.incident_main WHERE incident_complainant.incident_main = ?";
                                $stmt_record_while_complainant = $con->prepare($sql_record_while_complainant) or die ($con->error);
                                $stmt_record_while_complainant->bind_param('s',$record_id);
                                $stmt_record_while_complainant->execute();
                                $result_incident_while_complainant = $stmt_record_while_complainant->get_result();
                                while($row_record_incident_while_complainant = $result_incident_while_complainant->fetch_assoc()){

                                  if($row_record_resident_id['residence_id']  == $row_record_incident_while_complainant['complainant_id']){
                                    echo  'selected="selected"';
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
                            <?= $row_record_resident_id['last_name'] .' '. $row_record_resident_id['first_name'] .' '.  $record_person_middle  ?></option>
                            <?php
                          }   
                        ?>
                      </select>
                    </div>
                  </div>

                  <div class="col-sm-12 ">
                    <div class="form-group form-group-sm">
                      <label>Complainant Not Resident</label>
                      <textarea name="edit_complainant_not_residence" disabled id="edit_complainant_not_residence" cols="57"  class="bg-transparent text-white form-control"><?= $row_record_incident['complainant_not_residence'] ?></textarea>
                    </div>
                  </div>
                  <div class="col-sm-12 ">
                    <div class="form-group form-group-sm">
                      <label>Complainant Statement</label>
                      <textarea name="edit_complainant_statement" disabled id="edit_complainant_statement" cols="57" rows="3" class="bg-transparent text-white form-control"><?= $row_record_incident['statement'] ?></textarea>
                    </div>
                  </div>
                  <div class="col-sm-12 ">
                    <div class="form-group form-group-sm">
                      <label>Respodent</label>
                        <input name="edit_respodent" disabled value="<?= $row_record_incident['respodent'] ?>" id="edit_respodent"  class=" form-control">
                    </div>
                  </div>

                  <div class="col-sm-12">
                    <div class="form-group form-group-sm">
                        <label>Person Involved Resident</label>
                      <select name="edit_person_involed[]"disabled  multiple="multiple" id="edit_person_involed" class="select2bs4"  style="width: 100%;">
                        <option></option>
                        <?php 
                          $sql_person_id = "SELECT
                          residence_information.residence_id,
                          residence_information.first_name, 
                          residence_information.middle_name,
                          residence_information.last_name,
                          residence_information.image,   
                          residence_information.image_path
                          FROM residence_information
                          INNER JOIN residence_status ON residence_information.residence_id = residence_status.residence_id 
                          WHERE archive = ?
                          ORDER BY last_name ASC  ";
                          $query_preson_id = $con->prepare($sql_person_id) or die ($con->error);
                          $query_preson_id->bind_param('s',$no);
                          $query_preson_id->execute();
                          $result_person_id = $query_preson_id->get_result();
                          while($row_person_id = $result_person_id->fetch_assoc()){
                            if($row_person_id['middle_name'] != ''){
                              $middle_person_id = $row_person_id['middle_name'][0].'.'.' '; 
                            }else{
                              $middle_person_id = $row_person_id['middle_name'].' '; 
                            }
                            ?>
                              <option value="<?= $row_person_id['residence_id'] ?>" <?php 

                                    $sql_record_while_person = "SELECT incident_record.*, incident_status.person_id, incident_complainant.complainant_id FROM incident_record
                                    INNER JOIN incident_status ON incident_record.incidentlog_id = incident_status.incident_main
                                    INNER JOIN incident_complainant ON incident_record.incidentlog_id = incident_status.incident_main WHERE incident_status.incident_main = ?";
                                    $stmt_record_while_person = $con->prepare($sql_record_while_person) or die ($con->error);
                                    $stmt_record_while_person->bind_param('s',$record_id);
                                    $stmt_record_while_person->execute();
                                    $result_incident_while_complainant = $stmt_record_while_person->get_result();
                                    while($row_record_incident_while_person = $result_incident_while_complainant->fetch_assoc()){

                                      if($row_person_id['residence_id']  == $row_record_incident_while_person['person_id']){
                                        echo  'selected="selected"';
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
                            <?=   $row_person_id['last_name'] .' '.  $row_person_id['first_name'] .' '.  $middle_person_id  ?></option>
                            <?php
                          }   
                        ?>
                      </select>
                    </div>
                  </div>

                  <div class="col-sm-12 ">
                    <div class="form-group form-group-sm">
                      <label>Person Involved Not Resident</label>
                      <textarea name="edit_person_involevd_not_resident" disabled  id="edit_person_involevd_not_resident" cols="57"  class="bg-transparent text-white form-control"><?= $row_record_incident['involved_not_resident'] ?></textarea>
                    </div>
                  </div> 
                  <div class="col-sm-12 ">
                    <div class="form-group form-group-sm">
                      <label>Person Involved Statement</label>
                      <textarea name="edit_person_statement" disabled id="edit_person_statement" cols="57" rows="3" class="bg-transparent text-white form-control"><?= $row_record_incident['statement_person'] ?></textarea>
                    </div>
