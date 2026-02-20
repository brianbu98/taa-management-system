<?php 
include_once '../connection.php';
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'resident'){
  header("Location: ../login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

// Fetch TAA Info
$sql_taa = "SELECT * FROM taa_information LIMIT 1";
$res_taa = $con->query($sql_taa);
$row_taa = $res_taa->fetch_assoc();
$image = $row_taa['image'] ?? '';
$postal_address = $row_taa['postal_address'] ?? '';

// Fetch Announcements
$ann_query = $con->query("SELECT * FROM announcements WHERE status='active' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Announcements</title>
<link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
<link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
</head>
<body class="layout-top-nav dark-mode">

<div class="wrapper">

<div class="content-wrapper bg-dark p-5">
<div class="container">
<h3 class="text-warning mb-4">Announcements</h3>

<?php if($ann_query && $ann_query->num_rows > 0): ?>
<?php while($row = $ann_query->fetch_assoc()): ?>
<div class="card bg-secondary mb-3 p-3">
<h5 class="text-warning"><?= htmlspecialchars($row['title']) ?></h5>
<small><?= date('F d, Y h:i A', strtotime($row['created_at'])) ?></small>
<p class="mt-2"><?= nl2br(htmlspecialchars($row['message'])) ?></p>
</div>
<?php endwhile; ?>
<?php else: ?>
<p class="text-white">No announcements available.</p>
<?php endif; ?>

</div>
</div>

<footer class="main-footer text-white bg-success p-3">
<i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($postal_address) ?>
</footer>

</div>

</body>
</html>