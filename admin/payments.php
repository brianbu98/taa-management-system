<?php
require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$msg = "";

// ================= CREATE BILL =================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $resident_id = intval($_POST['resident_id']);
    $amount_due  = floatval($_POST['amount_due']);
    $description = trim($_POST['description']);

    $stmt = $con->prepare("
        INSERT INTO payments (user_id, amount_due, description, status)
        VALUES (?, ?, ?, 'unpaid')
    ");
    $stmt->bind_param("ids", $resident_id, $amount_due, $description);
    $stmt->execute();
    $stmt->close();

    $msg = "Bill created successfully.";
}

// ================= FETCH DATA =================
$residents = $con->query("
    SELECT id, CONCAT(first_name,' ',last_name) AS name
    FROM users WHERE user_type='resident'
");

$bills = $con->query("
    SELECT p.*, CONCAT(u.first_name,' ',u.last_name) AS resident
    FROM payments p
    JOIN users u ON p.user_id = u.id
    ORDER BY p.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Payments</title>

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
<a href="payments.php" class="nav-link active">
<i class="nav-icon fas fa-money-bill-wave"></i>
<p>Payments</p>
</a>
</li>

<li class="nav-item">
<a href="payment_records.php" class="nav-link">
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
<h3 class="card-title">Create Bill</h3>
</div>

<div class="card-body">

<?php if ($msg): ?>
<div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<form method="POST" class="row mb-4">

<div class="col-md-4">
<select name="resident_id" class="form-control" required>
<option value="">Select Resident</option>
<?php while ($r = $residents->fetch_assoc()): ?>
<option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-3">
<input type="number" step="0.01" name="amount_due" class="form-control" placeholder="Amount Due" required>
</div>

<div class="col-md-3">
<input type="text" name="description" class="form-control" placeholder="Description">
</div>

<div class="col-md-2">
<button class="btn btn-primary w-100">Create</button>
</div>

</form>

<hr>

<h5>All Bills</h5>

<table class="table table-bordered table-hover">
<thead>
<tr>
<th>ID</th>
<th>Resident</th>
<th>Amount</th>
<th>Status</th>
<th>Date</th>
</tr>
</thead>
<tbody>

<?php while ($b = $bills->fetch_assoc()): ?>
<tr>
<td><?= $b['id'] ?></td>
<td><?= htmlspecialchars($b['resident']) ?></td>
<td>₱ <?= number_format($b['amount_due'],2) ?></td>
<td>
<?php
$statusClass = 'badge badge-secondary';
if ($b['status'] === 'paid') $statusClass = 'badge badge-success';
if ($b['status'] === 'unpaid') $statusClass = 'badge badge-danger';
if ($b['status'] === 'partial') $statusClass = 'badge badge-warning';
?>
<span class="<?= $statusClass ?>">
<?= ucfirst($b['status']) ?>
</span>
</td>
<td><?= date('M d, Y', strtotime($b['created_at'])) ?></td>
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