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

/* ACTIONS */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_POST['action'] === 'add') {
        $title   = trim($_POST['title']);
        $message = trim($_POST['message']);

        $stmt = $con->prepare("INSERT INTO announcements (title,message,posted_by,status) VALUES (?,?,?,'active')");
        $stmt->bind_param("ssi", $title, $message, $user_id);
        $stmt->execute();
        $msg = "Announcement added.";
    }

    if ($_POST['action'] === 'delete') {
        $id = intval($_POST['id']);
        $stmt = $con->prepare("DELETE FROM announcements WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $msg = "Announcement deleted.";
    }
}

$result = $con->query("
SELECT a.*, CONCAT(u.first_name,' ',u.last_name) AS author
FROM announcements a
LEFT JOIN users u ON a.posted_by=u.id
ORDER BY a.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Announcements</title>

<link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
</head>

<body class="hold-transition dark-mode sidebar-mini layout-footer-fixed">
<div class="wrapper">

<!-- NAVBAR (COPIED FROM DASHBOARD) -->
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
<a href="myProfile.php" class="dropdown-item">
<div class="media">
<img src="../assets/dist/img/<?= $user_image ?: 'image.png' ?>" class="img-size-50 mr-3 img-circle">
<div class="media-body">
<h3 class="dropdown-item-title">
<?= ucfirst($first_name_user).' '.ucfirst($last_name_user) ?>
</h3>
</div>
</div>
</a>
<div class="dropdown-divider"></div>
<a href="../logout.php" class="dropdown-item dropdown-footer">LOGOUT</a>
</div>
</li>
</ul>
</nav>

<!-- SIDEBAR (EXACT COPY FROM DASHBOARD) -->
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
<a href="announcements.php" class="nav-link bg-indigo">
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

</ul>
</nav>
</div>
</aside>

<!-- CONTENT -->
<div class="content-wrapper p-4">

<h3>Announcements</h3>

<?php if($msg): ?>
<div class="alert alert-success"><?= $msg ?></div>
<?php endif; ?>

<div class="card">
<div class="card-body">
<form method="post">
<input type="hidden" name="action" value="add">
<input type="text" name="title" class="form-control mb-2" placeholder="Title" required>
<textarea name="message" class="form-control mb-2" placeholder="Message" required></textarea>
<button class="btn btn-success">Publish</button>
</form>
</div>
</div>

<div class="card mt-3">
<div class="card-body table-responsive">
<table id="annTable" class="table table-dark table-bordered">
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
<?php while($row=$result->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['title']) ?></td>
<td><?= ucfirst($row['status']) ?></td>
<td><?= htmlspecialchars($row['author']) ?></td>
<td><?= date('M d, Y h:i A',strtotime($row['created_at'])) ?></td>
<td>
<form method="post">
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

<footer class="main-footer text-center">
<strong>&copy; <?= date("Y") ?></strong>
</footer>

</div>

<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script>
$(function(){ $('#annTable').DataTable(); });
</script>

</body>
</html>