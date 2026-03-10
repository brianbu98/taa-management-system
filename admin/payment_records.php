<?php
require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt_user = $con->prepare("SELECT first_name,last_name,image FROM users WHERE id=?");
$stmt_user->bind_param("i",$user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

$first_name = $user['first_name'] ?? '';
$last_name  = $user['last_name'] ?? '';
$user_image = $user['image'] ?? '';

$records = $con->query("
SELECT pr.*, CONCAT(u.first_name,' ',u.last_name) AS resident
FROM payment_records pr
JOIN payments p ON pr.payment_id = p.id
JOIN users u ON p.user_id = u.id
ORDER BY pr.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Payment Records</title>

<link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
<link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">

</head>

<body class="hold-transition dark-mode sidebar-mini layout-footer-fixed">

<div class="wrapper">

<!-- PRELOADER -->
<div class="preloader flex-column justify-content-center align-items-center">
  <img class="animation__wobble"
       src="../assets/dist/img/loader.gif"
       alt="Loader"
       height="70"
       width="70">
</div>

<!-- NAVBAR -->
<nav class="main-header navbar navbar-expand navbar-dark">

<ul class="navbar-nav">
<li class="nav-item">
<a class="nav-link" data-widget="pushmenu" href="#">
<i class="fas fa-bars"></i>
</a>
</li>
</ul>

<ul class="navbar-nav ml-auto">

<li class="nav-item dropdown">

<a class="nav-link" data-toggle="dropdown" href="#">
<i class="far fa-user"></i>
</a>

<div class="dropdown-menu dropdown-menu-right">

<a class="dropdown-item">

<div class="media">

<?php if(!empty($user_image)): ?>

<img src="../assets/dist/img/<?= $user_image ?>"
class="img-size-50 img-circle mr-3">

<?php else: ?>

<img src="../assets/dist/img/image.png"
class="img-size-50 img-circle mr-3">

<?php endif; ?>

<div class="media-body">

<h3 class="dropdown-item-title py-3">
<?= ucfirst($first_name).' '.ucfirst($last_name) ?>
</h3>

</div>

</div>

</a>

<div class="dropdown-divider"></div>

<a href="../logout.php" class="dropdown-item dropdown-footer">
LOGOUT
</a>

</div>

</li>

</ul>

</nav>


<!-- SIDEBAR -->

<aside class="main-sidebar sidebar-dark-primary elevation-4">

<a href="dashboard.php" class="brand-link text-center">

<img src="../assets/logo/logo.png"
class="img-circle elevation-3"
style="width:70%;">

</a>

<div class="sidebar">

<div class="user-panel mt-3 pb-3 mb-3 d-flex">

<div class="image">
<img src="../assets/logo/logo.png"
class="img-circle elevation-2">
</div>

<div class="info">
<a class="d-block text-bold">ADMIN</a>
</div>

</div>

<nav class="mt-2">

<ul class="nav nav-pills nav-sidebar flex-column nav-child-indent"
data-widget="treeview"
role="menu"
data-accordion="false">

<li class="nav-item">
<a href="dashboard.php" class="nav-link">
<i class="nav-icon fas fa-tachometer-alt"></i>
<p>Dashboard</p>
</a>
</li>

<li class="nav-item">
<a href="announcements.php" class="nav-link">
<i class="nav-icon fas fa-bullhorn"></i>
<p>Announcements</p>
</a>
</li>

<li class="nav-item">
<a href="payments.php" class="nav-link">
<i class="nav-icon fas fa-money-bill-wave"></i>
<p>Payments</p>
</a>
</li>

<li class="nav-item">
<a href="payment_records.php" class="nav-link active">
<i class="nav-icon fas fa-receipt"></i>
<p>Payment Records</p>
</a>
</li>

<li class="nav-item">
<a href="settings.php" class="nav-link">
<i class="nav-icon fas fa-cog"></i>
<p>Settings</p>
</a>
</li>

<li class="nav-item">
<a href="systemLog.php" class="nav-link">
<i class="nav-icon fas fa-history"></i>
<p>System Logs</p>
</a>
</li>

<li class="nav-item">
<a href="backupRestore.php" class="nav-link">
<i class="nav-icon fas fa-database"></i>
<p>Backup / Restore</p>
</a>
</li>

</ul>

</nav>

</div>

</aside>


<!-- CONTENT -->

<div class="content-wrapper">

<section class="content pt-3">

<div class="container-fluid">

<div class="card card-outline card-indigo">

<div class="card-header">
<h3 class="card-title">Payment Records</h3>
</div>

<div class="card-body">

<table class="table table-bordered table-hover">

<thead>

<tr>
<th>ID</th>
<th>Resident</th>
<th>Amount Paid</th>
<th>Method</th>
<th>Reference</th>
<th>Date</th>
</tr>

</thead>

<tbody>

<?php while ($r = $records->fetch_assoc()): ?>

<tr>

<td><?= $r['id'] ?></td>

<td><?= htmlspecialchars($r['resident']) ?></td>

<td>₱ <?= number_format($r['amount_paid'],2) ?></td>

<td><?= htmlspecialchars($r['payment_method']) ?></td>

<td><?= htmlspecialchars($r['reference_no']) ?></td>

<td><?= date('M d, Y h:i A', strtotime($r['created_at'])) ?></td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

</div>

</div>

</section>

</div>


<footer class="main-footer text-center">

<strong>
Copyright © <?= date('Y') ?> - <?= date('Y',strtotime('+1 year')) ?>
</strong>

</footer>

</div>


<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="../assets/dist/js/adminlte.js"></script>

</body>
</html>