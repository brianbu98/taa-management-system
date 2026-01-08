<?php 
include_once '../connection.php';
session_start();

/*
  Handle AJAX actions (add/delete) posted back to this same file.
  Responses are plain text messages that JS will display via SweetAlert.
*/
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if(!isset($_SESSION['user_id'])) {
        echo "Unauthorized.";
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $user_type = $_SESSION['user_type'];

    // --- ADD Record ---
    if($_POST['action'] === 'add') {
        $incident = trim($_POST['incident'] ?? '');
        $location_of_incident = trim($_POST['location_of_incident'] ?? '');
        $date_incident = trim($_POST['date_incident'] ?? '');
        $remarks = trim($_POST['remarks'] ?? '');
        $status = trim($_POST['status'] ?? 'Pending');

        if ($user_type !== 'admin') {
            $status = 'Pending';
        }

        if ($incident === '' || $location_of_incident === '' || $date_incident === '' || $remarks === '') {
            echo "Please fill all required fields.";
            exit;
        }

        // Generate random incident number
        $year = date('Y');
        $rand = rand(1000,9999);
        $incident_number = "BLT-{$year}-{$rand}";
        $date_reported = date('Y-m-d');

        // ? Insert query (make sure your DB has `incident_number` column)
        $sql = "INSERT INTO `incident_record`
        (`incident_number`, `status`, `remarks`, `type_of_incident`, `location_incident`, `date_incident`, `date_reported`, `complainant_not_residence`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $con->prepare($sql);
        if(!$stmt){
            die("SQL Error: " . $con->error);
        }

        $stmt->bind_param("ssssssss", $incident_number, $status, $remarks, $incident, $location_of_incident, $date_incident, $date_reported, $user_id);

        if($stmt->execute()){
            echo "Record successfully added! (Incident #: $incident_number)";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        exit;
    }

    // --- DELETE Record ---
    if($_POST['action'] === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if($id <= 0){
            echo "Invalid record ID.";
            exit;
        }

        $check = $con->prepare("SELECT id FROM incident_record WHERE id = ? AND complainant_not_residence = ?");
        if(!$check){
            error_log("Prepare failed: ".$con->error);
            echo "Database error.";
            exit;
        }
        $check->bind_param("ii", $id, $user_id);
        $check->execute();
        $res = $check->get_result();
        if($res->num_rows === 0){
            echo "Unauthorized action or record not found.";
            $check->close();
            exit;
        }
        $check->close();

        $del = $con->prepare("DELETE FROM incident_record WHERE id = ?");
        if(!$del){
            error_log("Prepare failed: ".$con->error);
            echo "Database error.";
            exit;
        }
        $del->bind_param("i", $id);
        if($del->execute()){
            echo "Record deleted successfully!";
        } else {
            error_log("Delete failed: ".$del->error);
            echo "Failed to delete record.";
        }
        $del->close();
        exit;
    }

    echo "Unknown action.";
    exit;
}

/* ------------------- Below: regular page output ------------------- */

try{
  if(isset($_SESSION['user_id']) && isset($_SESSION['user_type'])){

    $user_id = $_SESSION['user_id'];
    $user_type = $_SESSION['user_type'];

    $sql_user = "SELECT * FROM `users` WHERE `id` = ? ";
    $stmt_user = $con->prepare($sql_user) or die ($con->error);
    $stmt_user->bind_param('s',$user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $row_user = $result_user->fetch_assoc();
    $first_name_user = $row_user['first_name'];
    $last_name_user = $row_user['last_name'];
    $user_image = $row_user['image'];

    $sql_resident = "SELECT * FROM residence_information WHERE residence_id = '$user_id'";
    $query_resident = $con->query($sql_resident) or die ($con->error);
    $row_resident = $query_resident->fetch_assoc();

    $sql = "SELECT * FROM `taa_information`";
    $query = $con->prepare($sql) or die ($con->error);
    $query->execute();
    $result = $query->get_result();
    while($row = $result->fetch_assoc()){
        $image = $row['image'];
        $image_path = $row['image_path'];
        $id = $row['id'];
        $postal_address = $row['postal_address'];
    }

  } else {
   echo '<script>window.location.href = "../login.php";</script>';
  }

}catch(Exception $e){
  echo $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Record</title>

  <!-- Stylesheets -->
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/sweetalert2/css/sweetalert2.min.css">
  <link rel="stylesheet" href="../assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="../assets/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

  <style>
    .rightBar:hover{ border-bottom: 3px solid red; }
    #sample_logo{ height: 150px; width:auto; max-width:500px; }
    .logo{ height: 150px; width:auto; max-width:500px; }
    .wrapper{ background-image: url('../assets/logo/newcover.jpg'); background-repeat:no-repeat; background-size: cover; background-position:center; width: 100%; height: auto; }
    .card { border: 10px solid rgba(0, 0, 0, 0.75); border-radius: 0; }
  </style>
</head>
<body class="layout-top-nav dark-mode">

<div class="wrapper p-0 bg-transparent">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md" style="background-color: #2e8b5f">
    <div class="container">
      <a href="#" class="navbar-brand">
        <img src="../assets/dist/img/<?= htmlspecialchars($image) ?>" alt="logo" class="brand-image img-circle">
        <span class="brand-text text-white font-weight-bold">TEREMIL ASSISTANCE APPLICATION</span>
      </a>

      <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
          <li class="nav-item"><a href="dashboard.php" class="nav-link text-white rightBar"><i class="fas fa-home"></i> DASHBOARD</a></li>
          <li class="nav-item"><a href="profile.php" class="nav-link text-white rightBar" style="text-transform:uppercase;"><i class="fas fa-user-alt"></i> <?= htmlspecialchars($last_name_user) ?>-<?= htmlspecialchars($user_id) ?></a></li>
          <li class="nav-item"><a href="../logout.php" class="nav-link text-white rightBar"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
      </ul>
    </div>
  </nav>

  <!-- Content -->
  <div class="content-wrapper" style="background-color: transparent">
    <div class="content">
      <div class="container-fluid pt-5">
        <input type="hidden" value="<?= htmlspecialchars($user_id); ?>" id="edit_residence_id">
        <div class="card mt-5">
          <div class="card-header d-flex justify-content-between align-items-center">
            <div class="card-title"><h4>Record List</h4></div>
            <div>
              <button type="button" id="addRecordBtn" class="btn btn-warning text-dark"><i class="fas fa-plus"></i> New Record</button>
            </div>
          </div>
          <div class="card-body">
            <table class="table table-striped table-hover" id="myRecordTable">
              <thead>
                <tr>
                  <th class="d-none test">Color</th>
                  <th>Incident Number</th>
                  <th>Status</th>
                  <th>Remarks</th>
                  <th>Incident</th>
                  <th>Location of Incident</th>
                  <th>Date Incident</th>
                  <th>Date Reported</th>
                  <th>Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="main-footer text-white" style="background-color: #2e8b5f">
    <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($postal_address) ?>
  </footer>
</div>

<!-- ADD RECORD MODAL -->
<div class="modal fade" id="addRecordModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content bg-dark">
      <div class="modal-header">
        <h5 class="modal-title text-warning">Add New Record</h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <form id="addRecordForm">
          <div class="form-row">

            <?php if ($_SESSION['user_type'] === 'admin'): ?>
              <div class="form-group col-md-6">
                <label>Status</label>
                <select name="status" id="status" class="form-control" required>
                  <option value="Pending">Pending</option>
                  <option value="Ongoing">Ongoing</option>
                  <option value="Settled">Settled</option>
                </select>
              </div>
            <?php else: ?>
              <input type="hidden" name="status" id="status" value="Pending">
            <?php endif; ?>

            <div class="form-group col-md-6">
              <label>Date of Incident</label>
              <input type="date" name="date_incident" id="date_incident" class="form-control" required>
            </div>
          </div>

          <div class="form-group">
            <label>Incident</label>
            <input type="text" name="incident" id="incident" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Location of Incident</label>
            <input type="text" name="location_of_incident" id="location_of_incident" class="form-control" required>
          </div>

          <div class="form-group">
            <label>Remarks</label>
            <textarea name="remarks" id="remarks" class="form-control" rows="3" required></textarea>
          </div>

          <div class="text-right">
            <button type="button" class="btn btn-default" data-dismiss="modal">CLOSE</button>
            <button type="submit" class="btn btn-warning text-dark">NEW RECORD</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- JS -->
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/plugins/sweetalert2/js/sweetalert2.all.min.js"></script>

<script>
$(document).ready(function(){

  function loadTable(){
    var edit_residence_id = $("#edit_residence_id").val();
    $('#myRecordTable').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      destroy: true,
      order: [],
      ajax:{
        url: 'myRecordTable.php',
        type: 'POST',
        data: { edit_residence_id: edit_residence_id }
      },
      columnDefs:[
        { targets: 0, visible: false },
        { targets: 8, orderable: false }
      ]
    });
  }

  loadTable();

  $('#addRecordBtn').on('click', function(){
    $('#addRecordForm')[0].reset();
    $('#addRecordModal').modal('show');
  });

  $('#addRecordForm').on('submit', function(e){
    e.preventDefault();
    var postData = $(this).serialize() + '&action=add';

    if($.trim($('input[name="incident"]').val()) === '') {
      Swal.fire('Error','Incident is required.','error');
      return;
    }

    $.ajax({
      url: 'myRecord.php',
      method: 'POST',
      data: postData,
      success: function(resp){
        Swal.fire({
          title: 'Success',
          text: resp,
          icon: 'success',
          timer: 1500,
          showConfirmButton: false
        });
        $('#addRecordModal').modal('hide');
        $('#myRecordTable').DataTable().ajax.reload();
      },
      error: function(){
        Swal.fire('Error','Failed to add record.','error');
      }
    });
  });
});
</script>

</body>
</html>
