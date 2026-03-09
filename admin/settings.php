
<?php 

echo "PHP WORKING";


include_once '../connection.php';
session_start();


try{


  
  if(isset($_SESSION['user_id']) && isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin'){
  
    $user_id = $_SESSION['user_id'];
 $sql_user = "SELECT id, first_name, last_name, user_type, image 
             FROM users 
             WHERE id = ? 
             LIMIT 1";

$stmt_user = $con->prepare($sql_user);

$stmt_user->bind_param('i', $user_id);
$stmt_user->execute();

$stmt_user->bind_result($uid,$first_name_user,$last_name_user,$user_type,$user_image);
$stmt_user->fetch();
$stmt_user->close();
  
$sql = "SELECT id,address,postal_address,image,image_path FROM taa_information LIMIT 1";

$query = $con->prepare($sql);

if(!$query){
    die("SQL Error: " . $con->error);
}

$query->execute();

$query->bind_result($id,$address,$postal_address,$image,$image_path);
if(!$query->fetch()){
    $id = 0;
    $address = '';
    $postal_address = '';
    $image = '';
    $image_path = '';
}
$query->close();

$id = $id ?? 0;
$address = $address ?? '';
$postal_address = $postal_address ?? '';
$image = $image ?? '';
$image_path = $image_path ?? '';

$logoSrc = (!empty($image_path))
    ? $image_path
    : '../assets/logo/logo.png';
  
  
  }else{
  header("Location: ../login.php");
exit;
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
    
    #display_image{
      height: 200px;
      width:auto;
      max-width:500px;
    }
    
  </style>

</head>
<body class="hold-transition dark-mode sidebar-mini ">
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
                  echo '<img src="../assets/dist/img/'.$user_image.'" class="img-size-50 mr-3 img-circle" alt="User Image">';
                }else{
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
          <a href="../logout.php" class="dropdown-item dropdown-footer">LOGOUT</a>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
    <!-- Brand Logo -->
    <a href="#" class="brand-link text-center">
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
         <img src="<?= htmlspecialchars($logoSrc) ?>"
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
            <a href="dashboard.php" class="nav-link">
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
            <a href="settings.php" class="nav-link bg-indigo">
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
          <div class="col-sm-6" style="font-variant: small-caps;">
              <h3>Settings</h3>
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
          
           <div class="card">
             <div class="card-body">
                <form id="taaInformationForm" method="POST" enctype="multipart/form-data">
                <div class="row">
                  
                  <div class="col-sm-12 text-center">
                    <?php 
                    
                   <img src="<?= htmlspecialchars($logoSrc) ?>"
                 class="img-circle text-center"
                 alt="logo"
                 id="display_image"
                 style="cursor:pointer;">
                    ?>
                   
                    <input type="file" id="add_image" name="add_image" style="display: none;">
                  </div>
                  <div class="col-sm-2" style="display:none;">
                    <input type="hidden" id="id" name="id" value="<?= $id ?>">
                 </div>
                  <div class="col-sm-2">
                    <div class="form-group">
                      <label>Postal Address</label>
                      <input type="text" name="postal_address" value="<?= $postal_address ?>" id="postal_address" class="form-control">
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="form-group">
                      <label>City</label>
                      <input type="text" name="address" value="<?= $address ?>" id="address" class="form-control">
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <button type="submit" class="btn btn-success btn-block">SAVE</button>
                    </div>
                  </div>

                    
                </div>
                </form> 
             </div>
           </div>     


      </div><!--/. container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->



  <!-- Main Footer -->
  <footer class="main-footer">
    <strong>Copyright &copy; <?php echo date("Y"); ?> - <?php echo date('Y', strtotime('+1 year'));?> </strong>
    
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
<script src="../assets/plugins/popper/umd/popper.min.js"></script>
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../assets/plugins/jszip/jszip.min.js"></script>
<script src="../assets/plugins/pdfmake/pdfmake.min.js"></script>
<script src="../assets/plugins/pdfmake/vfs_fonts.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="../assets/plugins/sweetalert2/js/sweetalert2.all.min.js"></script>
<script src="../assets/plugins/select2/js/select2.full.min.js"></script>
<script src="../assets/plugins/moment/moment.min.js"></script>
<script src="../assets/plugins/chart.js/Chart.min.js"></script>

<script>
  $(document).ready(function(){

    $("#taaInformationForm").submit(function(e){
      e.preventDefault();

      var postal_address = $("#postal_address").val();
var address = $("#address").val();

if(postal_address == '' || address == ''){
            Swal.fire({
              title: '<strong class="text-danger">WARNING</strong>',
              type: 'warning',
              html: '<b>Please Fill-up The Blank<b>',
              width: '400px',
              confirmButtonColor: '#6610f2',
            })
      }else{
          $.ajax({
              url: 'updateSettings.php',
              type: 'POST',
              data: new FormData(this),
              contentType: false,
              processData: false,
              success:function(data){
                Swal.fire({
                  title: '<strong class="text-success">SUCCESS</strong>',
                  type: 'success',
                  html: '<b>Updated has Successfully<b>',
                  width: '400px',
                  confirmButtonColor: '#6610f2',
                  allowOutsideClick: false,
                  showConfirmButton: false,
                  timer: 2000,
                }).then(()=>{
                  window.location.reload();
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

    })

    $("#display_image").click(function(){
          $("#add_image").click();
      });
      

    function displayImage(input){
      if(input.files && input.files[0]){
        var reader = new FileReader();
        var add_image = $("#add_image").val().split('.').pop().toLowerCase();

        if(add_image != ''){
          if(jQuery.inArray(add_image,['gif','png','jpg','jpeg']) == -1){
            Swal.fire({
              title: '<strong class="text-danger">ERROR</strong>',
              type: 'error',
              html: '<b>Invalid Image File<b>',
              width: '400px',
              confirmButtonColor: '#6610f2',
            })
            $("#add_image").val('');
            return false;
          }
        }
        
        reader.onload = function(e){
          $("#display_image").attr('src', e.target.result);
          $("#logo_image").attr('src', e.target.result);
          $("#display_image").hide();
          $("#logo_image").hide();
          $("#display_image").fadeIn(650);
          $("#logo_image").fadeIn(650);
          
        }

        reader.readAsDataURL(input.files[0]);


      }

     
    }  
    $("#add_image").change(function(){
        displayImage(this);
      })
  })
     


</script>



<script>
// Restricts input for each element in the set of matched elements to the given inputFilter.
(function($) {
  $.fn.inputFilter = function(inputFilter) {
    return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
      if (inputFilter(this.value)) {
        this.oldValue = this.value;
        this.oldSelectionStart = this.selectionStart;
        this.oldSelectionEnd = this.selectionEnd;
      } else if (this.hasOwnProperty("oldValue")) {
        this.value = this.oldValue;
        this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
      } else {
        this.value = "";
      }
    });
  };
}(jQuery));



  $("#postal_address, #address").inputFilter(function(value) {
  return /^[0-9a-z, ., ]*$/i.test(value); 
  });

</script>
</body>
</html>
