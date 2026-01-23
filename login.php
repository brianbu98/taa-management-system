<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/connection.php';

/* ===============================
   Redirect if already logged in
   =============================== */
if (isset($_SESSION['user_id'], $_SESSION['user_type'])) {
    switch ($_SESSION['user_type']) {
        case 'admin':
            header("Location: admin/dashboard.php");
            exit;
        case 'secretary':
            header("Location: secretary/dashboard.php");
            exit;
        default:
            header("Location: resident/dashboard.php");
            exit;
    }
}

/* ===============================
   Load system information
   =============================== */
$sql = "SELECT image, image_path, postal_address FROM taa_information LIMIT 1";
$stmt = $con->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$image = $row['image'] ?? '';
$image_path = $row['image_path'] ?? '';
$postal_address = $row['postal_address'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login</title>

<link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
<link rel="stylesheet" href="assets/plugins/sweetalert2/css/sweetalert2.min.css">

<style>
.logo {
  height: 150px;
  width: auto;
  max-width: 500px;
}
.content-wrapper {
  background-image: url('assets/logo/newcover.jpg');
  background-repeat: no-repeat;
  background-size: cover;
  width: 100%;
  height: 100%;
}
</style>
</head>

<body class="hold-transition layout-top-nav">

<div class="wrapper">

<!-- Navbar -->
<nav class="main-header navbar navbar-expand-md" style="background-color:#2e8b57">
  <div class="container">
    <a href="#" class="navbar-brand">
      <img src="assets/dist/img/<?= htmlspecialchars($image) ?>" class="brand-image img-circle">
      <span class="brand-text text-white font-weight-bold">
        TEREMIL ASSISTANCE APPLICATION
      </span>
    </a>
  </div>
</nav>

<!-- Content -->
<div class="content-wrapper">
  <div class="content px-4">
    <div class="container-fluid pt-5" style="background-color: rgba(0,0,0,0.75);">
      <div class="row justify-content-center">

        <form id="loginForm" method="post">
          <div class="card" style="border:10px solid rgba(0,54,175,.75)">
            <div class="card-body text-center text-white">

              <img src="assets/dist/img/<?= htmlspecialchars($image) ?>" class="img-circle logo">

              <h3 class="mt-3 text-primary font-weight-bold">
                TEREMIL ASSISTANCE APPLICATION
              </h3>

              <div class="form-group mt-4">
                <input type="text" name="username" id="username"
                       class="form-control" placeholder="USERNAME OR RESIDENT NUMBER">
              </div>

              <div class="form-group">
                <input type="password" name="password" id="password"
                       class="form-control" placeholder="PASSWORD">
              </div>

              <div class="text-right mb-3">
                <a href="forgot.php">Forgot Password?</a>
              </div>

              <button type="submit" class="btn btn-primary btn-block btn-lg">
                Sign In
              </button>

            </div>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<footer class="main-footer text-white" style="background-color:#2e8b57">
  <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($postal_address) ?>
</footer>

</div>

<!-- Scripts -->
<script src="assets/plugins/jquery/jquery.min.js"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/dist/js/adminlte.js"></script>
<script src="assets/plugins/sweetalert2/js/sweetalert2.all.min.js"></script>

<script>
$(function () {

  $("#loginForm").submit(function (e) {
    e.preventDefault();

    if ($("#username").val() === "" || $("#password").val() === "") {
      Swal.fire("Warning", "Username and Password are required", "warning");
      return;
    }

    $.ajax({
      url: "/loginForm.php",
      type: "POST",
      data: $(this).serialize(),
      success: function (data) {
        if (data === "errorUsername" || data === "errorPassword") {
          Swal.fire("Error", "Incorrect Username or Password", "error");
        } else if (data === "admin") {
          window.location.href = "admin/dashboard.php";
        } else if (data === "secretary") {
          window.location.href = "secretary/dashboard.php";
        } else if (data === "resident") {
          window.location.href = "resident/dashboard.php";
        } else {
          Swal.fire("Error", "Unexpected server response", "error");
        }
      }
    });
  });

});
</script>

</body>
</html>
