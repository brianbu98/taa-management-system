
<?php 
ini_set('display_errors', 0);
error_reporting(0);


require_once __DIR__ . '/connection.php';





try{

if (isset($_SESSION['user_id'], $_SESSION['user_type'])) {


  $user_id = $_SESSION['user_id'];

  $sql = "SELECT * FROM users WHERE id = ?";
  $stmt = $con->prepare($sql);
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $row = $stmt->get_result()->fetch_assoc();

  if ($row['user_type'] === 'admin') {
    header("Location: /admin/dashboard.php");
    exit;
  } elseif ($row['user_type'] === 'secretary') {
    header("Location: /secretary/dashboard.php");
    exit;
  } else {
    header("Location: /resident/dashboard.php");
    exit;
  }





}

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
<link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">

  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="assets/plugins/sweetalert2/css/sweetalert2.min.css">
 

  <style>
    .rightBar:hover{
      border-bottom: 3px solid red;
     
    }
    


    
  

    .logo{
      height: 150px;
      width:auto;
      max-width:500px;
    }
    .content-wrapper{
      background-image: url('assets/logo/newcover.jpg');
      background-repeat: no-repeat;
      background-size: cover;
      width: 100%;
        height: 100%;
        animation-name: example;
        animation-duration: 5s;
       
       
    }


@keyframes example {
  from {opacity: 0;}
  to {opacity: 1.5;}
}





  </style>


</head>
<body  class="hold-transition layout-top-nav">


<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md " style="background-color: #2e8b57">
    <div class="container">
      <a href="" class="navbar-brand">
        <img src="assets/dist/img/<?= $image  ?>" alt="logo" class="brand-image img-circle " >
        <span class="brand-text  text-white" style="font-weight: 700">TEREMIL ASSISTANCE APPLICATION</span>
      </a>

      <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse order-3" id="navbarCollapse">
        <!-- Left navbar links -->


       
      </div>

      <!-- Right navbar links -->
      <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto " >
          
      </ul>
    </div>
  </nav>
  <!-- /.navbar -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" >
    <!-- Content Header (Page header) -->
 
    
  
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content px-4" >
      <div class="container-fluid pt-5 "  style="background-color:  rgba(0, 0, 0, 0.75);">
      <br>
      <br>
        <div class="row justify-content-center">
         <form id="loginForm" method="post">
          <div class="card " style="border: 10px solid rgba(0,54,175,.75); border-radius: 0;">
            <div class="card-body text-center text-white">
              <div class="col-sm-12">
                <img src="assets/dist/img/<?= $image;?>" alt="logo" class="img-circle logo">
              </div>
              <div class="col-sm-12">
                <h1 class="card-text" style="font-weight: 1000; color: #0036af">TEREMIL ASSISTANCE APPLICATION</h1>
              </div>
             
              <div class="col-sm-12 mt-4">
                <div class="form-group">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text bg-transparent"><i class="fas fa-user"></i></span>
                    </div>
                    <input type="text" id="username" name="username" class="form-control" placeholder="USERNAME OR RESIDENT NUMBER" >
                  </div>
                </div>
              </div>
              <div class="col-sm-12 mt-4">
                <div  class="form-group">
                  <div class="input-group mb-3" id="show_hide_password">
                    <div class="input-group-prepend">
                      <span class="input-group-text bg-transparent"><i class="fas fa-key"></i></span>
                    </div>
                    <input type="password"  id="password" name="password" class="form-control" placeholder="PASSWORD"  style="border-right: none;">
                    <div class="input-group-append bg">
                      <span class="input-group-text bg-transparent"> <a href="" style=" text-decoration:none;"><i class="fas fa-eye-slash" aria-hidden="true"></i></a></span>
                    </div>
                  </div>
                </div>
              </div>
            <div class="col-sm-12 text-right">
                    <a href="forgot.php">Forgot Password</a>
            </div>
            <div class="col-sm-12 mt-4">
                <button type="submit" class="btn btn-flat bg-blue btn-lg btn-block">Sign In</button>
            </div>
          </div>
          </form>
        </div>

  
      

      </div>


      <br>
        <br>
        <br>
        
       
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

 

 


</div>
<!-- ./wrapper -->
<footer class="main-footer text-white" style="background-color: #2e8b57">
    <div class="float-right d-none d-sm-block">
    
    </div>
  <i class="fas fa-map-marker-alt"></i> <?= $postal_address?> 
  </footer>




<!-- jQuery -->
<script src="assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="assets/dist/js/adminlte.js"></script>
<script src="assets/plugins/sweetalert2/js/sweetalert2.all.min.js"></script>

<script>
$(document).ready(function () {

  $("#loginForm").on("submit", function (e) {
    e.preventDefault();

    $.ajax({
      url: "/loginForm.php",
      type: "POST",
      data: $(this).serialize(),
      success: function (data) {

        data = data.trim();
        console.log("SERVER RESPONSE:", data);

        // 🔴 HARD PROOF
        alert("Server returned: " + data);

        if (data === "admin" || data === "secretary" || data === "resident") {
          // 🔥 FORCE REDIRECT — NO SWEETALERT
          window.location.replace("/" + data + "/dashboard.php");
          return;
        }

        alert("Login failed: " + data);
      },
      error: function () {
        alert("AJAX ERROR");
      }
    });
  });

});
</script>


</body>
</html>
