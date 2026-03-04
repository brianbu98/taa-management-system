<?php 
include_once '../connection.php';
session_start();

/*
  Handle AJAX DELETE action only.
*/
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if(!isset($_SESSION['user_id'])) {
        echo "Unauthorized.";
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // --- DELETE Record ---
    if($_POST['action'] === 'delete') {

        $id = intval($_POST['id'] ?? 0);

        if($id <= 0){
            echo "Invalid record ID.";
            exit;
        }

        // Verify ownership
        $check = $con->prepare("SELECT id FROM incident_record WHERE id = ? AND complainant_not_residence = ?");
        $check->bind_param("ii", $id, $user_id);
        $check->execute();
        $res = $check->get_result();

        if($res->num_rows === 0){
            echo "Unauthorized action or record not found.";
            $check->close();
            exit;
        }
        $check->close();

        // Delete record
        $del = $con->prepare("DELETE FROM incident_record WHERE id = ?");
        $del->bind_param("i", $id);

        if($del->execute()){
            echo "Record deleted successfully!";
        } else {
            echo "Failed to delete record.";
        }

        $del->close();
        exit;
    }

    echo "Unknown action.";
    exit;
}

/* ------------------- Page Output ------------------- */

if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])){
    echo '<script>window.location.href = "../login.php";</script>';
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$sql_user = "SELECT * FROM users WHERE id = ?";
$stmt_user = $con->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$row_user = $result_user->fetch_assoc();
$last_name_user = $row_user['last_name'];
$user_image = $row_user['image'];
$stmt_user->close();

// Fetch resident info (FIXED - prepared statement)
$sql_resident = "SELECT * FROM residence_information WHERE residence_id = ?";
$stmt_resident = $con->prepare($sql_resident);
$stmt_resident->bind_param("i", $user_id);
$stmt_resident->execute();
$query_resident = $stmt_resident->get_result();
$row_resident = $query_resident->fetch_assoc();
$stmt_resident->close();

// Fetch TAA info
$sql = "SELECT * FROM taa_information LIMIT 1";
$query = $con->prepare($sql);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_assoc();
$image = $row['image'];
$postal_address = $row['postal_address'];
$query->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>My Record</title>

<link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
<link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="../assets/plugins/sweetalert2/css/sweetalert2.min.css">

<style>
.wrapper{
    background-image: url('../assets/logo/newcover.jpg');
    background-size: cover;
}
.card{
    border: 10px solid rgba(0,0,0,0.75);
    border-radius: 0;
}
</style>
</head>

<body class="layout-top-nav dark-mode">

<div class="wrapper">

<!-- Navbar -->
<nav class="main-header navbar navbar-expand-md" style="background-color:#2e8b5f">
<div class="container">
<a href="#" class="navbar-brand">
<img src="../assets/dist/img/<?= htmlspecialchars($image) ?>" class="brand-image img-circle">
<span class="brand-text text-white font-weight-bold">
TEREMIL ASSISTANCE APPLICATION
</span>
</a>

<ul class="navbar-nav ml-auto">
<li class="nav-item">
<a href="dashboard.php" class="nav-link text-white">
<i class="fas fa-home"></i> DASHBOARD
</a>
</li>
<li class="nav-item">
<a href="profile.php" class="nav-link text-white">
<i class="fas fa-user"></i>
<?= strtoupper(htmlspecialchars($last_name_user)) ?>-<?= htmlspecialchars($user_id) ?>
</a>
</li>
<li class="nav-item">
<a href="../logout.php" class="nav-link text-white">
<i class="fas fa-sign-out-alt"></i> Logout
</a>
</li>
</ul>
</div>
</nav>

<!-- Content -->
<div class="content-wrapper bg-transparent">
<div class="content">
<div class="container pt-5">

<input type="hidden" value="<?= htmlspecialchars($user_id); ?>" id="edit_residence_id">

<div class="card mt-5">
<div class="card-header">
<h4>Record List</h4>
</div>

<div class="card-body">
<table class="table table-striped table-hover" id="myRecordTable">
<thead>
<tr>
<th class="d-none">Color</th>
<th>Incident Number</th>
<th>Status</th>
<th>Remarks</th>
<th>Incident</th>
<th>Location</th>
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

<footer class="main-footer text-white" style="background-color:#2e8b5f">
<i class="fas fa-map-marker-alt"></i>
<?= htmlspecialchars($postal_address) ?>
</footer>

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
        processing:true,
        serverSide:true,
        responsive:true,
        destroy:true,
        order:[],
        ajax:{
            url:'myRecordTable.php',
            type:'POST',
            data:{ edit_residence_id:edit_residence_id }
        },
        columnDefs:[
            { targets:0, visible:false },
            { targets:8, orderable:false }
        ]
    });
}

loadTable();

});
</script>

</body>
</html>