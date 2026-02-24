<?php
require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: /login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = "";

/* ADMIN INFO (SAME AS DASHBOARD) */
$stmt_user = $con->prepare("SELECT * FROM users WHERE id=?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$row_user = $stmt_user->get_result()->fetch_assoc();

$first_name_user = $row_user['first_name'];
$last_name_user  = $row_user['last_name'];
$user_type       = $row_user['user_type'];
$user_image      = $row_user['image'];

/* LOGO (SAME AS DASHBOARD) */
$sql = "SELECT * FROM taa_information";
$query = $con->prepare($sql);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_assoc();

$image = $row['image'];
$image_path = $row['image_path'];

/* ADD BILL */
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
    $msg = "Bill created.";
}

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
<title>Payments</title>
<link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
</head>

<body class="hold-transition dark-mode sidebar-mini layout-footer-fixed">
<div class="wrapper">

<!-- NAVBAR (FROM DASHBOARD) -->
<nav class="main-header navbar navbar-expand navbar-dark">
<ul class="navbar-nav">
<li class="nav-item">
<a class="nav-link text-white" data-widget="pushmenu">
<i class="fas fa-bars"></i>
</a>
</li>
</ul>
</nav>

<!-- SIDEBAR (FROM DASHBOARD) -->
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
<a href="announcements.php" class="nav-link">
<i class="nav-icon fas fa-bullhorn"></i>
<p>Announcements</p>
</a>
</li>

<li class="nav-item">
<a href="payments.php" class="nav-link bg-indigo">
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
<div class="content-wrapper p-4">

<h3>Billing (Payments)</h3>

<?php if($msg): ?>
<div class="alert alert-success"><?= $msg ?></div>
<?php endif; ?>

<div class="card">
<div class="card-body">
<form method="post">
<select name="resident_id" class="form-control mb-2" required>
<option value="">Select Resident</option>
<?php while($r=$residents->fetch_assoc()): ?>
<option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
<?php endwhile; ?>
</select>

<input type="number" step="0.01" name="amount_due" class="form-control mb-2" placeholder="Amount Due" required>
<input type="text" name="description" class="form-control mb-2" placeholder="Description">
<button class="btn btn-success">Create Bill</button>
</form>
</div>
</div>

<div class="card mt-3">
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
<td><?= ucfirst($b['status']) ?></td>
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
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script>
$(function(){ $('#billsTable').DataTable(); });
</script>

</body>
</html>