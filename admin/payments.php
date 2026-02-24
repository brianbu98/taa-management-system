<?php
require_once __DIR__ . '/../connection.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = "";

/* ================= ADMIN INFO ================= */
$stmt_user = $con->prepare("SELECT first_name,last_name,user_type,image FROM users WHERE id=?");
$stmt_user->bind_param("i",$user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

$first_name_user = $user['first_name'];
$last_name_user  = $user['last_name'];
$user_type       = $user['user_type'];
$user_image      = $user['image'] ?? 'image.png';

/* ================= TAA LOGO ================= */
$taa = $con->query("SELECT * FROM taa_information LIMIT 1")->fetch_assoc();
$image_path = $taa['image_path'] ?? '../assets/logo/logo.png';

/* ================= ADD BILL ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add_bill') {

    $resident_id = intval($_POST['resident_id']);
    $amount_due  = floatval($_POST['amount_due']);
    $description = trim($_POST['description']);

    if ($resident_id && $amount_due > 0) {
        $stmt = $con->prepare("
            INSERT INTO payments (user_id, amount_due, description, status)
            VALUES (?, ?, ?, 'unpaid')
        ");
        $stmt->bind_param("ids",$resident_id,$amount_due,$description);
        $stmt->execute();
        $msg = "Bill created successfully.";
    }
}

/* ================= FETCH DATA ================= */
$residents = $con->query("
    SELECT id, CONCAT(first_name,' ',last_name) AS name
    FROM users WHERE user_type='resident'
    ORDER BY last_name
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
<title>Payments</title>

<link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
</head>

<body class="hold-transition dark-mode sidebar-mini layout-fixed">
<div class="wrapper">

<!-- NAVBAR -->
<nav class="main-header navbar navbar-expand navbar-dark">
<ul class="navbar-nav">
<li class="nav-item">
<a class="nav-link text-white" data-widget="pushmenu"><i class="fas fa-bars"></i></a>
</li>
</ul>

<ul class="navbar-nav ml-auto">
<li class="nav-item dropdown">
<a class="nav-link" data-toggle="dropdown"><i class="far fa-user"></i></a>
<div class="dropdown-menu dropdown-menu-right">
<a href="#" class="dropdown-item">
<img src="../assets/dist/img/<?= htmlspecialchars($user_image) ?>" class="img-size-50 img-circle mr-2">
<?= ucfirst($first_name_user)." ".ucfirst($last_name_user) ?>
</a>
<div class="dropdown-divider"></div>
<a href="../logout.php" class="dropdown-item text-danger text-center">Logout</a>
</div>
</li>
</ul>
</nav>

<!-- SIDEBAR -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
<a href="dashboard.php" class="brand-link text-center">
<img src="<?= htmlspecialchars($image_path) ?>" class="img-circle elevation-3" style="width:70%;">
</a>

<div class="sidebar">

<div class="user-panel mt-3 pb-3 mb-3 d-flex">
<div class="image">
<img src="../assets/dist/img/<?= htmlspecialchars($user_image) ?>" class="img-circle elevation-2">
</div>
<div class="info">
<a href="#" class="d-block"><?= strtoupper($user_type) ?></a>
</div>
</div>

<nav class="mt-2">
<ul class="nav nav-pills nav-sidebar flex-column nav-child-indent">
<li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
<li class="nav-item"><a href="announcements.php" class="nav-link"><i class="nav-icon fas fa-bullhorn"></i><p>Announcements</p></a></li>
<li class="nav-item"><a href="payments.php" class="nav-link active bg-indigo"><i class="nav-icon fas fa-money-bill-wave"></i><p>Payments</p></a></li>
<li class="nav-item"><a href="payment_records.php" class="nav-link"><i class="nav-icon fas fa-receipt"></i><p>Payment Records</p></a></li>
<li class="nav-item"><a href="settings.php" class="nav-link"><i class="nav-icon fas fa-cog"></i><p>Settings</p></a></li>
</ul>
</nav>

</div>
</aside>

<!-- CONTENT -->
<div class="content-wrapper p-4">

<h3>Billing (Payments)</h3>

<?php if($msg): ?>
<div class="alert alert-success"><?= $msg ?></div>
<?php endif; ?>

<div class="card">
<div class="card-header"><h5>Create Bill</h5></div>
<div class="card-body">
<form method="post">
<input type="hidden" name="action" value="add_bill">

<div class="form-group">
<label>Resident</label>
<select name="resident_id" class="form-control" required>
<option value="">Select</option>
<?php while($r=$residents->fetch_assoc()): ?>
<option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
<?php endwhile; ?>
</select>
</div>

<div class="form-group">
<label>Amount Due</label>
<input type="number" step="0.01" name="amount_due" class="form-control" required>
</div>

<div class="form-group">
<label>Description</label>
<input type="text" name="description" class="form-control">
</div>

<button class="btn btn-success">Create Bill</button>
</form>
</div>
</div>

<div class="card mt-4">
<div class="card-body table-responsive">
<table id="billsTable" class="table table-dark table-bordered">
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
<?php while($b=$bills->fetch_assoc()): ?>
<tr>
<td><?= $b['id'] ?></td>
<td><?= htmlspecialchars($b['resident']) ?></td>
<td>₱ <?= number_format($b['amount_due'],2) ?></td>
<td>
<?php if($b['status']=='paid'): ?>
<span class="badge badge-success">Paid</span>
<?php elseif($b['status']=='partial'): ?>
<span class="badge badge-warning">Partial</span>
<?php else: ?>
<span class="badge badge-danger">Unpaid</span>
<?php endif; ?>
</td>
<td><?= date('M d, Y',strtotime($b['created_at'])) ?></td>
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
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script>
$(function(){ $('#billsTable').DataTable(); });
</script>

</body>
</html>