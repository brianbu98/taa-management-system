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

$msg = "";

/* HANDLE FORM */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_POST['action'] === 'add') {

        $title   = trim($_POST['title']);
        $message = trim($_POST['message']);
        $user_id = $_SESSION['user_id'];

        $stmt = $con->prepare("
            INSERT INTO announcements (title, message, posted_by, status)
            VALUES (?, ?, ?, 'active')
        ");
        $stmt->bind_param("ssi", $title, $message, $user_id);
        $stmt->execute();
        $stmt->close();

        $msg = "Announcement added successfully.";
    }

    if ($_POST['action'] === 'delete') {

        $id = intval($_POST['id']);
        $stmt = $con->prepare("DELETE FROM announcements WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $msg = "Announcement deleted.";
    }
}

$result = $con->query("
SELECT a.*, CONCAT(u.first_name,' ',u.last_name) AS author
FROM announcements a
LEFT JOIN users u ON a.posted_by = u.id
ORDER BY a.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Announcements</title>

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
<a href="announcements.php" class="nav-link active">
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
<a href="payment_records.php" class="nav-link">
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
<h3 class="card-title">Announcements</h3>
</div>

<div class="card-body">

<?php if ($msg): ?>
<div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<form method="POST" class="mb-3">
<input type="hidden" name="action" value="add">

<div class="row">

<div class="col-md-4">
<input type="text" name="title" class="form-control" placeholder="Title" required>
</div>

<div class="col-md-6">
<textarea name="message" class="form-control" placeholder="Message" required></textarea>
</div>

<div class="col-md-2">
<button class="btn btn-primary w-100">Publish</button>
</div>

</div>

</form>

<table class="table table-bordered table-hover">

<thead>

<tr>
<th>ID</th>
<th>Title</th>
<th>Status</th>
<th>Author</th>
<th>Date</th>
<th>Action</th>
</tr>

</thead>

<tbody>

<?php while ($row = $result->fetch_assoc()): ?>

<tr>

<td><?= $row['id'] ?></td>

<td><?= htmlspecialchars($row['title']) ?></td>

<td><?= ucfirst($row['status']) ?></td>

<td><?= htmlspecialchars($row['author']) ?></td>

<td><?= date('M d, Y h:i A', strtotime($row['created_at'])) ?></td>

<td>

<form method="POST" onsubmit="return confirm('Delete this announcement?');">

<input type="hidden" name="action" value="delete">
<input type="hidden" name="id" value="<?= $row['id'] ?>">

<button class="btn btn-danger btn-sm">Delete</button>

</form>

</td>

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