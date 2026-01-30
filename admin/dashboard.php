<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../connection.php';

if (!isset($_SESSION['user_id'], $_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}


// INIT ARRAYS (for charts)
$year = [];
$totalIncident = [];
$official_postition = [];
$position_color = [];
$total_per_official = [];

// INIT COUNTERS (dashboard safety)
$count_users_yes = 0;
$count_users_no = 0;
$count_total_residence = 0;
$count_announcements = 0;
$count_active_announcements = 0;
$total_payment_amount = 0;
$total_incident_record = 0;


try {

    /* 👤 ADMIN INFO */
    $user_id = $_SESSION['user_id'];

    $sql_user = "SELECT * FROM users WHERE id = ?";
    $stmt_user = $con->prepare($sql_user);
    $stmt_user->bind_param('i', $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $row_user = $result_user->fetch_assoc();

if (!$row_user) {
    // Session exists but user record is missing (DB mismatch, deleted user, wrong DB)
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit;
}

$first_name_user = $row_user['first_name'] ?? '';
$last_name_user  = $row_user['last_name'] ?? '';
$user_type       = $row_user['user_type'] ?? '';
$user_image      = $row_user['image'] ?? null;


    /* 🏷️ APP INFO */
    $sql = "SELECT * FROM taa_information";
    $query = $con->prepare($sql);
    $query->execute();
    $result = $query->get_result();

    while ($row = $result->fetch_assoc()) {
        $image_path = $row['image_path'];
        $id = $row['id'];
    }


    // continue your remaining queries HERE (still inside try)

    

    $yes= 'YES';
    $no = 'NO';


    // USERS COUNTERS BASED ON USER TYPE
    $sql_users_yes = "SELECT 1 FROM users WHERE user_type = 'admin'";
    $stmt_users_yes = $con->prepare($sql_users_yes);
    $stmt_users_yes->execute();
    $stmt_users_yes->store_result();
    $count_users_yes = $stmt_users_yes->num_rows;

    $sql_users_no = "SELECT 1 FROM users WHERE user_type = 'resident'";
    $stmt_users_no = $con->prepare($sql_users_no);
    $stmt_users_no->execute();
    $stmt_users_no->store_result();
    $count_users_no = $stmt_users_no->num_rows;

   $sql_total_residence = "SELECT residence_id FROM residence_status WHERE archive = ?";
$query_total_residence = $con->prepare($sql_total_residence);
if (!$query_total_residence) {
    throw new Exception($con->error);
}

$query_total_residence->bind_param('s', $no);
$query_total_residence->execute();

    $query_total_residence->store_result();
    $count_total_residence = $query_total_residence->num_rows;

    $sql_incident ="SELECT date_added as yyyy, count(incidentlog_id) as comp from incident_record group by date_added order by yyyy";
    $result_incident = $con->query($sql_incident) or die ($con->error);
    $count_incident_result = $result_incident->num_rows;
    if($count_incident_result > 0){
      while ($row_incident = $result_incident->fetch_array()) { 
        $year[]  = $row_incident['yyyy']  ;
        $totalIncident[] = (int)$row_incident['comp'];

      }
    }else{
      $year[]  = ['0000','1000'];
      $totalIncident[] = ['100','200'];
    }

    $sql_gender ="SELECT COUNT(CASE WHEN gender = 'Male' THEN residence_information.residence_id END) as male,
    COUNT(CASE WHEN gender = 'Female' THEN residence_information.residence_id END) as female
    FROM residence_information
    INNER JOIN residence_status ON residence_information.residence_id = residence_status.residence_id
    WHERE  archive = 'NO' ";
    $result_gender = $con->query($sql_gender) or die ($con->error);
    while ($row_gender = $result_gender->fetch_assoc()) { 
      $genderMale  = $row_gender['male'];
      $genderFemale  = $row_gender['female'];
    }

    $sql_total_incident = "SELECT incidentlog_id FROM incident_record";
    $stmt_total_incident = $con->prepare($sql_total_incident) or die ($con->error);
    $stmt_total_incident->execute();
    $result_total_incident = $stmt_total_incident->get_result();
    $total_incident_record = $result_total_incident->num_rows;

    $sql_count_official =  "SELECT COUNT(official_id) AS total_official FROM official_status";
    $stmt_total_official = $con->prepare($sql_count_official) or die ($con->error);
    $stmt_total_official->execute();
    $result_total_official = $stmt_total_official->get_result();
    $row_total_official = $result_total_official->fetch_assoc();

    $sql_official_position = "SELECT COUNT(*) AS dis,  position.color, position.position AS official_position, position.color, official_status.position FROM position
    INNER JOIN official_status ON position.position_id = official_status.position GROUP BY official_status.position,position.position";
    $stmt_official_position = $con->prepare($sql_official_position) or die ($con->error);
    $stmt_official_position->execute();
    $result_official_position = $stmt_official_position->get_result();
    $count_result_official = $result_official_position->num_rows;
    if($count_result_official > 0){
      while($row_official_position = $result_official_position->fetch_assoc()){
        $official_postition[] = strtoupper($row_official_position['official_position']);
        $position_color[] = $row_official_position['color'];
        $total_per_official[] = $row_official_position['dis'];
      }
    }else{
      $official_postition[] = ['BLANK'];
      $position_color[] = ['red'];
      $total_per_official[] = ['1'];

    }
   // ================= ANNOUNCEMENTS =================
$sql_announce = "
    SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active
    FROM announcements
";
$result_announce = $con->query($sql_announce);
$row_announce = $result_announce->fetch_assoc();

$count_announcements = (int)$row_announce['total'];
$count_active_announcements = (int)$row_announce['active'];


// ================= PAYMENTS (BILLS / OBLIGATIONS) =================
$sql_payments = "
    SELECT
        COUNT(*) AS total_bills,
        SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) AS paid_bills,
        SUM(CASE WHEN status = 'unpaid' THEN 1 ELSE 0 END) AS unpaid_bills,
        SUM(CASE WHEN status = 'partial' THEN 1 ELSE 0 END) AS partial_bills,
        IFNULL(SUM(amount_due), 0) AS total_amount_due
    FROM payments
";
$result_payments = $con->query($sql_payments);
$row_payments = $result_payments->fetch_assoc();

$total_bills          = (int)$row_payments['total_bills'];
$total_paid_bills     = (int)$row_payments['paid_bills'];
$total_unpaid_bills   = (int)$row_payments['unpaid_bills'];
$total_partial_bills  = (int)$row_payments['partial_bills'];
$total_amount_due     = (float)$row_payments['total_amount_due'];


// ================= PAYMENT RECORDS (MONEY RECEIVED) =================
$sql_payment_records = "
    SELECT
        COUNT(*) AS total_records,
        IFNULL(SUM(amount_paid), 0) AS total_collected
    FROM payment_records
";
$result_payment_records = $con->query($sql_payment_records);
$row_payment_records = $result_payment_records->fetch_assoc();

$total_payment_records = (int)$row_payment_records['total_records'];
$total_payment_amount  = (float)$row_payment_records['total_collected'];





} catch (Throwable $e) {
    die("Dashboard fatal error: " . $e->getMessage());
}


  
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title></title>

 
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/sweetalert2/css/sweetalert2.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="../assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="../assets/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <style>
   #official_body .scrollOfficial{
    height: 52vh;
    overflow-y: auto;
    }
   #official_body .scrollOfficial::-webkit-scrollbar {
        width: 5px;
    }                                                    
                            
  #official_body  .scrollOfficial::-webkit-scrollbar-thumb {
        background: #6c757d; 
        --webkit-box-shadow: inset 0 0 6px #6c757d; 
    }
  #official_body  .scrollOfficial::-webkit-scrollbar-thumb:window-inactive {
      background: #6c757d; 
    }
  </style>
</head>
<body class="hold-transition dark-mode sidebar-mini   layout-footer-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__wobble " src="../assets/dist/img/loader.gif" alt="AdminLTELogo" height="70" width="70">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <h5><a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></h5>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-user"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="myProfile.php" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <?php 
            if (!empty($user_image)) {
                echo '<img src="../assets/dist/img/'.htmlspecialchars($user_image).'" class="img-size-50 mr-3 img-circle" alt="User Image">';
            } else {
                echo '<img src="../assets/dist/img/image.png" class="img-size-50 mr-3 img-circle" alt="User Image">';
            }
            ?>
   
              <div class="media-body">
                <h3 class="dropdown-item-title py-3">
                  <?= ucfirst($first_name_user) .' '. ucfirst($last_name_user) ?>
                </h3>
              </div>
            </div>
            <!-- Message End -->
          </a>         
          <div class="dropdown-divider"></div>
          <a href="logout.php" class="dropdown-item dropdown-footer">LOGOUT</a>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
    <!-- Brand Logo -->
    <a href="#" class="brand-link text-center">
    <?php 
        $logoSrc = (!empty($image_path))
    ? '../' . ltrim($image_path, '/')
    : '../assets/logo/logo.png';
?>

<img src="<?= htmlspecialchars($logoSrc) ?>"
     id="logo_image"
     class="img-circle elevation-5 img-bordered-sm"
     alt="logo"
     style="width:70%;">

    
      <span class="brand-text font-weight-light"></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
    

    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
         <?php
        $sideLogo = (!empty($image_path))
            ? '../' . ltrim($image_path, '/')
            : '../assets/logo/logo.png';
        ?>

        <img src="<?= htmlspecialchars($sideLogo) ?>"
             class="img-circle elevation-5 img-bordered-sm"
             alt="Admin Logo">
        </div>
        <div class="info text-center">
          <a href="#" class="d-block text-bold"><?= strtoupper($user_type) ?></a>
        </div>
      </div>
      <!-- Sidebar Menu -->
      <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link bg-indigo">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-users-cog"></i>
              <p>
              Homeowner Officials
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="newOfficial.php" class="nav-link ">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>New Official</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="allOfficial.php" class="nav-link">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>List of Official</p>
                </a>
              </li>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link ">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Residence
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="newResidence.php" class="nav-link ">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>New Residence</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="allResidence.php" class="nav-link ">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>All Residence</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="archiveResidence.php" class="nav-link ">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>Archive Residence</p>
                </a>
              </li>
            </ul>
          </li>
              </p>
            </a>
          </li>
          <li class="nav-item ">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-user-shield"></i>
              <p>
                Users
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="usersResident.php" class="nav-link ">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>Resident</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="userAdministrator.php" class="nav-link">
                  <i class="fas fa-circle nav-icon text-red"></i>
                  <p>Administrator</p>
                </a>
              </li>

            </ul>
          </li>
          <li class="nav-item">
            <a href="position.php" class="nav-link">
              <i class="nav-icon fas fa-user-tie"></i>
              <p>
                Position
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="incidentRecord.php" class="nav-link">
              <i class="nav-icon fas fa-clipboard"></i>
              <p>
                Incident Record
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="report.php" class="nav-link">
              <i class="nav-icon fas fa-bookmark"></i>
              <p>
                Reports
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="announcements.php" class="nav-link">
              <i class="nav-icon fas fa-bullhorn"></i>
              <p>
              Announcements
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="payments.php" class="nav-link">
              <i class="nav-icon fas fa-money-bill-wave"></i>
              <p>
              Payments
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="settings.php" class="nav-link">
              <i class="nav-icon fas fa-cog"></i>
              <p>
                Settings
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="systemLog.php" class="nav-link">
              <i class="nav-icon fas fa-history"></i>
              <p>
                System Logs
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="backupRestore.php" class="nav-link">
              <i class="nav-icon fas fa-database"></i>
              <p>
                Backup/Restore
              </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

                
            <div class="row">

                <div class="col-sm-4">
                  <div class="row">
                      <div class="col-sm-12">
                      <!-- small box -->
                      <div class="small-box bg-info">
                        <div class="inner">
                          <h3><?= number_format($count_total_residence ?? 0); ?></h3>
                          <p>ACTIVE RESIDENTS</p>
                        </div>
                        <div class="icon">
                          <i class="fas fa-users"></i>
                        </div>
                    
                      </div>
                    </div>
                    <!-- ./col -->

                    <div class="col-sm-12">
                      <!-- small box -->
                      <div class="small-box bg-success">
                        <div class="inner">
                          <h3><?= number_format($count_admin_users ?? 0); ?></h3>
                          <p>UPSTANDING USERS</p>
                        </div>
                        <div class="icon">
                          <i class="fas fa-user-check"></i>
                        </div>
                      </div>
                    </div>
                    <!-- ./col -->

                    <div class="col-sm-12">
                      <!-- small box -->
                      <div class="small-box bg-warning">
                        <div class="inner">
                          <h3 class="text-white"><?= number_format($count_resident_users ?? 0); ?></h3>
                          <p class="text-white">DISREPUTABLE RESIDENTS</p>
                        </div>
                        <div class="icon">
                          <i class="fas fa-user-times"></i>
                        </div>
                      </div>
                    </div>

                    <!-- ./col -->  
                    <div class="col-sm-12">
                      <!-- small box -->
                      <div class="small-box bg-indigo">
                        <div class="inner">
                          <h3><?= number_format($total_incident_record ?? 0) ?><sup style="font-size: 20px"></sup></h3>

                          <p>INCIDENTS</p>
                        </div>
                        <div class="icon">
                          <i class="fas fa-book"></i>
                        </div>

                         </div>
                    </div>
                  
                    <!-- ./col -->  

                  </div>
                </div>
                <div class="col-sm-8">

                  <div class="row">
                    <div class="col-sm-12">

                              <!-- USERS LIST -->
                          <div class="card card-outline card-indigo"  id="official_body">
                            <div class="card-header">
                            <h1 class="card-title" style="font-weight:  700;"> <i class="fas fa-users-cog"></i> OFFICIAL MEMBERS <span class="badge badge-secondary text-lg"><?= $row_total_official['total_official'] ?? 0?></span></h1>   

                              <div class="card-tools">
                              
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                  <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                  <i class="fas fa-times"></i>
                                </button>
                              </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-0 text-white">
                              <div class="row">
                                <div class="col-sm-6 scrollOfficial">

                                    <ul class="users-list clearfix ">

                                          <?php 

                                          $sql_official = "SELECT position.color, position.position AS position_official, official_information.first_name, official_information.last_name, official_information.image_path, official_status.status,official_status.official_id FROM  official_status 
                                          INNER JOIN official_information ON  official_status.official_id = official_information.official_id
                                          INNER JOIN position ON  official_status.position = position.position_id ORDER BY position.position";
                                          $stmt_official = $con->prepare($sql_official) or die ($con->error);
                                          $stmt_official->execute();
                                          $result_official = $stmt_official->get_result();
                                          while($row_official = $result_official->fetch_assoc()){

                                          if($row_official['image_path'] != ''){

                                          if($row_official['status'] == 'ACTIVE'){
                                            $official_image = '  <img src="'.$row_official['image_path'].'" class="w-50" style="border: 3px solid lime" alt="Official Image">';
                                          }else{
                                            $official_image = '  <img src="'.$row_official['image_path'].'" class="w-50" style="border: 3px solid red" alt="Official Image">';
                                          }


                                          }else{
                                          if($row_official['status'] == 'ACTIVE'){
                                            $official_image = '  <img src="../assets/dist/img/image.png" class="w-50" style="border: 3px solid lime" alt="Official Image">';
                                          }else{
                                            $official_image = '  <img src="../assets/dist/img/image.png" class="w-50" style="border: 3px solid red" alt="Official Image">';
                                          }


                                          }


                                          ?>

                                          <li id="<?= $row_official['official_id'] ?>" class="viewOfficial" style="cursor: pointer">
                                            <?= $official_image; ?>
                                            <p class="users-list-name m-0 text-white" ><?= $row_official['first_name'].' '. $row_official['last_name'] ?> </p>
                                            <span class="users-list-date text-white" style="font-weight: 900"><?= strtoupper($row_official['position_official']) ?></span>
                                          </li>

                                          <?php
                                          }



                                          ?>


                                          </ul>
                                          <!-- /.users-list -->

                                </div>
                                <div class="col-sm-6">
                              
                                  <canvas id="donutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                
                                
                                </div>
                              </div>
                            
                            </div>
                            <!-- /.card-body -->
                          
                          </div>
                          <!--/.card -->
                      
                    </div>
                    <div class="col-sm-12">
                        <div class="card card-outline card-indigo">
                          <div class="card-body">
                            <div class="row">
                              <div class="col-sm-6">
                                <p class="text-center">
                                <strong>INCIDENTS YEARLY</strong>
                                </p>
                                  <canvas id="myChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                              </div>

                              <div class="col-sm-6">
                              <p class="text-center">
                                <strong>GENDER</strong>
                                </p>
                                <canvas  id="genderChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div> 
                     
                  </div>

                </div>
                
            </div>

         







      
     
          
      </div><!--/. container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

 

  <!-- Main Footer -->
  <footer class="main-footer">
    <strong>Copyright &copy; <?php echo date("Y"); ?> - <?php echo date('Y', strtotime('+1 year'));  ?> </strong>
    
    <div class="float-right d-none d-sm-inline-block">
    </div>
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- overlayScrollbars -->
<script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="../assets/dist/js/adminlte.js"></script>

<script src="../assets/plugins/chart.js/Chart.min.js"></script>
<div id="showOfficial"></div>

<script src="../assets/plugins/sweetalert2/js/sweetalert2.min.js"></script>




<script>


let myChart = document.getElementById('myChart').getContext('2d');

let massPopChart = new Chart(myChart,{
  type: 'line',
  data:{
    labels:<?php echo json_encode($year) ?>,
    datasets:[{
      label:'Record',
      fill: true,
      data: <?php echo json_encode($totalIncident)?>,
      pointBorderColor: "aqua",
      borderWidth: 4,

      borderColor: 'red',
      hoverBorderWith: 4,
      hoverBorderColor: '#fff',
      borderDash: [2, 2],
      backgroundColor:  "rgba(255, 0, 0, 0.4)",

      
    }]
  },
  options:{
    responsive: true,
    
    title:{
      display:false,
      text: "Incident",
      fontSize: 35,
      fontColor: '#fff',
    },
   
    legend:{
      display: false,
    },
    scales: {
        yAxes: [{
            ticks: {
                fontSize: 15,
                fontColor: '#fff',
                userCallback: function(label, index, labels) {
                     // when the floored value is the same as the value we have a whole number
                     if (Math.floor(label) === label) {
                         return label;
                     }
                 },
            },
            gridLines: {
                color: "#000",
            },
           
        }],
        xAxes: [{
            ticks: {
                fontSize: 15,
                fontColor: '#fff',
            },
            gridLines: {
                color: "#000",
            }
        }]
        
    }

  }
})










</script>

<script>
  new Chart("genderChart", {
  type: "doughnut",
  data: {
    labels: [
      'Male',
      'Female'
    ],
    datasets: [{
      backgroundColor: [
      "blue",
      "#00aba9",  
      ], 
      data: [<?= $genderMale ?>, <?= $genderFemale ?>]
    }]
  },
  options: {
    responsive: true,
    title: {
      display: false,
      text: "Gender",
      fontSize: 35,
      fontColor: '#fff',
    
    
    },
     legend:{
      display: true,
      fontColor: '#fff',
      labels: {
                fontSize: 15,
                fontColor: '#fff',
            }
    },
  
  }
});
</script>

<script>

new Chart("donutChart", {
  type: "pie",
  data: {
    labels: <?php echo json_encode($official_postition)?>,
      datasets: [
        {
          data: <?php echo json_encode($total_per_official)?>,
          backgroundColor : <?php echo json_encode($position_color)?>,
        }
      ]
  },
  options: {
    responsive: true,
    title: {
      display: false,
    
      fontSize: 35,
      fontColor: '#fff',
    
    
    },
     legend:{
      display: true,
      fontColor: '#fff',
      labels: {
                fontSize: 15,
                fontColor: '#fff',
            },
          
    },
  
  }
});
  
</script>
<script>
  $(document).ready(function(){

    $(document).on('click','.viewOfficial', function(){
      

      var official_id = $(this).attr('id');

      $("#showOfficial").html('');

      $.ajax({
          url: 'viewOfficialModal.php',
          type: 'POST',
          dataType: 'html',
          cache: false,
          data: {
            official_id:official_id
          },
          success:function(data){
            $("#showOfficial").html(data);
            $("#viewOfficialModal").modal('show');              
          }
        }).fail(function(){
              Swal.fire({
              icon: 'error',
              title: 'Oops...',
              html: '<b>Something went wrong with ajax!</b>',
              width: 400,
              confirmButtonColor: '#6610f2'
            });

        })
     

    })
    

  })
</script>


</body>
</html>
