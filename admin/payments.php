<?php
require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* ADMIN INFO */

$stmt_user = $con->prepare("SELECT first_name,last_name,image FROM users WHERE id=?");
$stmt_user->bind_param("i",$user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

$first_name = $user['first_name'] ?? '';
$last_name  = $user['last_name'] ?? '';
$user_image = $user['image'] ?? '';

/* SYSTEM LOGO */

$sql = "SELECT image_path FROM taa_information LIMIT 1";
$result = $con->query($sql);
$row = $result->fetch_assoc();

$logoSrc = (!empty($row['image_path']))
    ? '../' . ltrim($row['image_path'], '/')
    : '../assets/logo/logo.png';

$msg = "";

/* CREATE BILL */

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

/* FETCH DATA */

$residents = $con->query("
SELECT id, CONCAT(first_name,' ',last_name) AS name
FROM users
WHERE user_type='resident'
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

<img src="<?= htmlspecialchars($logoSrc) ?>"
class="img-circle elevation-3"
style="width:70%;">

</a>


<div class="sidebar">


<div class="user-panel mt-3 pb-3 mb-3 d-flex">

<div class="image">

<img src="<?= htmlspecialchars($logoSrc) ?>"
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


<!-- HOMEOWNER OFFICIALS -->

<li class="nav-item">

<a href="#" class="nav-link">

<i class="nav-icon fas fa-users-cog"></i>

<p>
Homeowner Officials
<i class="right fas fa-angle-left"></i>
</p>

</a>

<ul class="nav nav-treeview">

<li class="nav-item">
<a href="newOfficial.php" class="nav-link">
<i class="fas fa-circle nav-icon text-red"></i>
<p>New Official</p>
</a>
</li>

<li class="nav-item">
<a href="allOfficial.php" class="nav-link">
<i class="fas fa-circle nav-icon text-red"></i>
<p>List of Official</p>
</a>
</li>

</ul>

</li>


<!-- RESIDENCE -->

<li class="nav-item">

<a href="#" class="nav-link">

<i class="nav-icon fas fa-home"></i>

<p>
Residence
<i class="right fas fa-angle-left"></i>
</p>

</a>

<ul class="nav nav-treeview">

<li class="nav-item">
<a href="newResidence.php" class="nav-link">
<i class="fas fa-circle nav-icon text-red"></i>
<p>New Residence</p>
</a>
</li>

<li class="nav-item">
<a href="allResidence.php" class="nav-link">
<i class="fas fa-circle nav-icon text-red"></i>
<p>All Residence</p>
</a>
</li>

<li class="nav-item">
<a href="archiveResidence.php" class="nav-link">
<i class="fas fa-circle nav-icon text-red"></i>
<p>Archive Residence</p>
</a>
</li>

</ul>

</li>


<!-- USERS -->

<li class="nav-item">

<a href="#" class="nav-link">

<i class="nav-icon fas fa-user-shield"></i>

<p>
Users
<i class="right fas fa-angle-left"></i>
</p>

</a>

<ul class="nav nav-treeview">

<li class="nav-item">
<a href="usersResident.php" class="nav-link">
<i class="fas fa-circle nav-icon text-red"></i>
<p>Resident</p>
</a>
</li>

<li class="nav-item">
<a href="userAdministrator.php" class="nav-link">
<i class="fas fa-circle nav-icon text-red"></i>
<p>Administrator</p>
</a>
</li>

</ul>

</li>


<li class="nav-item">
<a href="position.php" class="nav-link">
<i class="nav-icon fas fa-user-tie"></i>
<p>Position</p>
</a>
</li>


<li class="nav-item">
<a href="incidentRecord.php" class="nav-link">
<i class="nav-icon fas fa-clipboard"></i>
<p>Incident Record</p>
</a>
</li>


<li class="nav-item">
<a href="report.php" class="nav-link">
<i class="nav-icon fas fa-bookmark"></i>
<p>Reports</p>
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
<h3 class="card-title">Create Bill</h3>
</div>


<div class="card-body">


<?php if ($msg): ?>

<div class="alert alert-success">
<?= htmlspecialchars($msg) ?>
</div>

<?php endif; ?>


<form method="POST" class="row mb-4">


<div class="col-md-4">

<select name="resident_id" class="form-control" required>

<option value="">Select Resident</option>

<?php while ($r = $residents->fetch_assoc()): ?>

<option value="<?= $r['id'] ?>">
<?= htmlspecialchars($r['name']) ?>
</option>

<?php endwhile; ?>

</select>

</div>


<div class="col-md-3">

<input type="number"
step="0.01"
name="amount_due"
class="form-control"
placeholder="Amount Due"
required>

</div>


<div class="col-md-3">

<input type="text"
name="description"
class="form-control"
placeholder="Description">

</div>


<div class="col-md-2">

<button class="btn btn-primary w-100">
Create
</button>

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


<footer class="main-footer">

<strong>
Copyright &copy; <?php echo date("Y"); ?> - <?php echo date('Y', strtotime('+1 year'));?>
</strong>

</footer>


</div>


<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="../assets/dist/js/adminlte.js"></script>

</body>

</html>