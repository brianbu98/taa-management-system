<?php 
include_once '../connection.php';
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'resident'){
  header("Location: ../login.php");
  exit;
}

$user_id = $_SESSION['user_id'];


$sql_user = "SELECT * FROM users WHERE id = ?";
$stmt_user = $con->prepare($sql_user);
$stmt_user->bind_param('s',$user_id);
$stmt_user->execute();
$row_user = $stmt_user->get_result()->fetch_assoc();
$last_name_user = $row_user['last_name'];

$sql_taa = "SELECT * FROM taa_information";
$res_taa = $con->query($sql_taa);
$row_taa = $res_taa->fetch_assoc();
$image = $row_taa['image'];
$postal_address = $row_taa['postal_address'];

// Fetch Announcements
$ann_query = $con->query("SELECT * FROM announcements ORDER BY date_posted DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Announcements</title>

<link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">

<style>
.card {border:10px solid rgba(0,0,0,0.75); border-radius:0;}
.announcement-card {background:#1e1e1e; border-left:5px solid #2e8b5f; margin-bottom:15px; padding:20px; border-radius:4px;}
.announcement-title {font-weight:bold; color:#ffcc00; font-size:1.3rem;}
.announcement-date {font-size:0.9rem; color:#999;}
.announcement-text {margin-top:10px; color:#ddd; line-height:1.5;}
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
          <li class="nav-item"><a href="profile.php" class="nav-link text-white rightBar"><i class="fas fa-user-alt"></i> <?= htmlspecialchars($last_name_user) ?>-<?= htmlspecialchars($user_id) ?></a></li>
          <li class="nav-item"><a href="../logout.php" class="nav-link text-white rightBar"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
      </ul>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="content-wrapper" style="background-color: transparent">
    <div class="content">
      <div class="container-fluid pt-5">
        <div class="card mt-5 bg-dark">
          <div class="card-header">
            <h4 class="text-warning mb-0"><i class="fas fa-bullhorn"></i> Teremil Announcements</h4>
          </div>
          <div class="card-body">
            <?php if($ann_query->num_rows > 0): ?>
              <?php while($row = $ann_query->fetch_assoc()): ?>
                <div class="announcement-card">
                  <div class="announcement-title"><?= htmlspecialchars($row['title']) ?></div>
                  <div class="announcement-date"><?= date('F d, Y h:i A', strtotime($row['date_posted'])) ?></div>
                  <div class="announcement-text"><?= nl2br(htmlspecialchars($row['content'])) ?></div>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <div class="alert alert-info text-center bg-dark border-0 text-white">No announcements available.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="main-footer text-white" style="background-color: #2e8b5f">
    <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($postal_address) ?>
  </footer>
</div>

</body>
</html>
