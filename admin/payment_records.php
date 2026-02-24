<?php
require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

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

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark">
<ul class="navbar-nav">
<li class="nav-item">
<a class="nav-link" data-widget="pushmenu" href="#">
<i class="fas fa-bars"></i>
</a>
</li>
</ul>
</nav>

<!-- Sidebar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
<a href="dashboard.php" class="brand-link text-center">
<span class="brand-text">ADMIN</span>
</a>

<div class="sidebar">
<nav class="mt-2">
<ul class="nav nav-pills nav-sidebar flex-column">

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
Copyright © <?= date('Y') ?>
</footer>

</div>

<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="../assets/dist/js/adminlte.js"></script>

</body>
</html>