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
$stmt_user = $con->prepare("SELECT first_name, last_name, user_type, image FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

$first_name_user = $user['first_name'] ?? '';
$last_name_user  = $user['last_name'] ?? '';
$user_type       = $user['user_type'] ?? '';
$user_image      = $user['image'] ?? 'image.png';

/* ================= TAA INFO (LOGO) ================= */
$stmt_taa = $con->query("SELECT * FROM taa_information LIMIT 1");
$taa = $stmt_taa->fetch_assoc();

$image_path = $taa['image_path'] ?? '../assets/logo/logo.png';

/* ================= HANDLE FORM ACTIONS ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $title   = trim($_POST['title']);
        $message = trim($_POST['message']);

        if ($title && $message) {
            $stmt = $con->prepare("
                INSERT INTO announcements (title, message, posted_by, status)
                VALUES (?, ?, ?, 'active')
            ");
            $stmt->bind_param("ssi", $title, $message, $user_id);
            $stmt->execute();
            $msg = "Announcement published successfully.";
        }
    }

    if ($action === 'edit') {
        $id      = intval($_POST['id']);
        $title   = trim($_POST['title']);
        $message = trim($_POST['message']);
        $status  = $_POST['status'];

        $stmt = $con->prepare("
            UPDATE announcements
            SET title=?, message=?, status=?
            WHERE id=?
        ");
        $stmt->bind_param("sssi", $title, $message, $status, $id);
        $stmt->execute();
        $msg = "Announcement updated.";
    }

    if ($action === 'delete') {
        $id = intval($_POST['id']);
        $stmt = $con->prepare("DELETE FROM announcements WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $msg = "Announcement deleted.";
    }
}

/* ================= FETCH ANNOUNCEMENTS ================= */
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
<link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
<link rel="stylesheet" href="../assets/plugins/sweetalert2/css/sweetalert2.min.css">
</head>

<body class="hold-transition dark-mode sidebar-mini layout-fixed">
<div class="wrapper">

<!-- NAVBAR -->
<nav class="main-header navbar navbar-expand navbar-dark">
<ul class="navbar-nav">
<li class="nav-item">
<a class="nav-link text-white" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
</li>
</ul>

<ul class="navbar-nav ml-auto">
<li class="nav-item dropdown">
<a class="nav-link" data-toggle="dropdown" href="#"><i class="far fa-user"></i></a>
<div class="dropdown-menu dropdown-menu-right">
<a href="myProfile.php" class="dropdown-item">
<img src="../assets/dist/img/<?= htmlspecialchars($user_image) ?>" class="img-size-50 img-circle mr-2">
<?= ucfirst($first_name_user).' '.ucfirst($last_name_user) ?>
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
<li class="nav-item"><a href="allOfficial.php" class="nav-link"><i class="nav-icon fas fa-users-cog"></i><p>Officials</p></a></li>
<li class="nav-item"><a href="allResidence.php" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Residents</p></a></li>
<li class="nav-item"><a href="announcements.php" class="nav-link active bg-indigo"><i class="nav-icon fas fa-bullhorn"></i><p>Announcements</p></a></li>
<li class="nav-item"><a href="payments.php" class="nav-link"><i class="nav-icon fas fa-money-bill-wave"></i><p>Payments</p></a></li>
<li class="nav-item"><a href="settings.php" class="nav-link"><i class="nav-icon fas fa-cog"></i><p>Settings</p></a></li>
</ul>
</nav>

</div>
</aside>

<!-- CONTENT -->
<div class="content-wrapper p-4">

<h3>Announcements</h3>

<?php if ($msg): ?>
<script>
document.addEventListener('DOMContentLoaded', function(){
Swal.fire({icon:'success',title:"<?= htmlspecialchars($msg) ?>",timer:1500,showConfirmButton:false});
});
</script>
<?php endif; ?>

<div class="card card-outline card-primary">
<div class="card-header"><h5>Add Announcement</h5></div>
<div class="card-body">
<form method="post">
<input type="hidden" name="action" value="add">
<div class="form-group">
<label>Title</label>
<input type="text" name="title" class="form-control" required>
</div>
<div class="form-group">
<label>Message</label>
<textarea name="message" rows="4" class="form-control" required></textarea>
</div>
<button class="btn btn-success">Publish</button>
</form>
</div>
</div>

<div class="card mt-4">
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
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['title']) ?></td>
<td>
<?php if($row['status']=='active'): ?>
<span class="badge badge-success">Active</span>
<?php else: ?>
<span class="badge badge-secondary">Inactive</span>
<?php endif; ?>
</td>
<td><?= htmlspecialchars($row['author']) ?></td>
<td><?= date('M d, Y h:i A', strtotime($row['created_at'])) ?></td>
<td>
<form method="post" style="display:inline;">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="id" value="<?= $row['id'] ?>">
<button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
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
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/plugins/sweetalert2/js/sweetalert2.all.min.js"></script>
<script src="../assets/dist/js/adminlte.js"></script>

<script>
$(function(){ $('#annTable').DataTable(); });
</script>

</body>
</html>