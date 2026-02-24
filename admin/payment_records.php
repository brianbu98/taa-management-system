<?php
require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: /login.php");
    exit;
}

/* LOGO (SAME AS DASHBOARD) */
$sql = "SELECT * FROM taa_information";
$query = $con->prepare($sql);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_assoc();
$image = $row['image'];
$image_path = $row['image_path'];

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
<title>Payment Records</title>
<link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
</head>

<body class="hold-transition dark-mode sidebar-mini layout-footer-fixed">
<div class="wrapper">

<nav class="main-header navbar navbar-expand navbar-dark">
<ul class="navbar-nav">
<li class="nav-item">
<a class="nav-link text-white" data-widget="pushmenu">
<i class="fas fa-bars"></i>
</a>
</li>
</ul>
</nav>

<aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
<a href="dashboard.php" class="brand-link text-center">
<?php
if($image != '' || !empty($image)){
echo '<img src="'.$image_path.'" class="img-circle elevation-5 img-bordered-sm" style="width:70%;">';
}else{
echo '<img src="../assets/logo/logo.png" class="img-circle elevation-5 img-bordered-sm" style="width:70%;">';
}
?>
</a>

<div class="sidebar">
<nav class="mt-2">
<ul class="nav nav-pills nav-sidebar flex-column nav-child-indent">

<li class="nav-item">
<a href="dashboard.php" class="nav-link">
<i class="nav-icon fas fa-tachometer-alt"></i>
<p>Dashboard</p>
</a>
</li>

<li class="nav-item">
<a href="payments.php" class="nav-link">
<i class="nav-icon fas fa-money-bill-wave"></i>
<p>Payments</p>
</a>
</li>

<li class="nav-item">
<a href="payment_records.php" class="nav-link bg-indigo">
<i class="nav-icon fas fa-receipt"></i>
<p>Payment Records</p>
</a>
</li>

</ul>
</nav>
</div>
</aside>

<div class="content-wrapper p-4">

<h3>Payment Records</h3>

<div class="card">
<div class="card-body table-responsive">
<table id="recordsTable" class="table table-dark table-bordered">
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
<?php while($r=$records->fetch_assoc()): ?>
<tr>
<td><?= $r['id'] ?></td>
<td><?= htmlspecialchars($r['resident']) ?></td>
<td>₱ <?= number_format($r['amount_paid'],2) ?></td>
<td><?= htmlspecialchars($r['payment_method']) ?></td>
<td><?= htmlspecialchars($r['reference_no']) ?></td>
<td><?= date('M d, Y h:i A',strtotime($r['created_at'])) ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>

</div>

<footer class="main-footer text-center">
<strong>&copy; <?= date("Y") ?></strong>
</footer>

</div>

<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script>
$(function(){ $('#recordsTable').DataTable(); });
</script>

</body>
</html>