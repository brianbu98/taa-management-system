<?php
// secretary/announcements.php
include_once '../connection.php';
session_start();

try {
  if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'secretary') {
    header("Location: ../login.php");
    exit;
  }

  $user_id = $_SESSION['user_id'];

  // Fetch secretary info
  $stmt_user = $con->prepare("SELECT * FROM users WHERE id = ?");
  $stmt_user->bind_param('s', $user_id);
  $stmt_user->execute();
  $user = $stmt_user->get_result()->fetch_assoc();

  
  $sql_logo = "SELECT * FROM taa_information LIMIT 1";
$stmt_logo = $con->prepare($sql_logo) or die($con->error);
$stmt_logo->execute();
$result_logo = $stmt_logo->get_result();
$row_logo = $result_logo->fetch_assoc();

$image_path = $row_logo['image_path'] ?? '';

$logoSrc = (!empty($image_path))
    ? '../' . ltrim($image_path, '/')
    : '../assets/logo/logo.png';

  $first_name_user = $user['first_name'] ?? '';
  $last_name_user = $user['last_name'] ?? '';
  $user_type = $user['user_type'] ?? '';
  $user_image = $user['image'] ?? '';
  $msg = '';

// Handle Add / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    $action  = $_POST['action'];
    $title   = trim($_POST['title'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $status  = $_POST['status'] ?? 'Active';

    // ADD
    if ($action === 'add') {

        if ($title && $message) {

            $stmt = $con->prepare("
                INSERT INTO announcements 
                (title, message, posted_by, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, NOW(), NOW())
            ");

            $stmt->bind_param('ssis', $title, $message, $user_id, $status);
            $stmt->execute();

            $msg = "Announcement added successfully.";

        } else {
            $msg = "Please fill in all fields.";
        }
    }

    // EDIT
    if ($action === 'edit') {

        $id = intval($_POST['id']);

        if ($id && $title && $message) {

            $stmt = $con->prepare("
                UPDATE announcements
                SET title = ?, message = ?, status = ?, updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->bind_param('sssi', $title, $message, $status, $id);
            $stmt->execute();

            $msg = "Announcement updated successfully.";

        } else {
            $msg = "Invalid data provided.";
        }
    }
}

  // Fetch all announcements
$res = $con->query("
    SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) AS posted_name
    FROM announcements a
    LEFT JOIN users u ON a.posted_by = u.id
    ORDER BY a.created_at DESC
");

} catch (Exception $e) {
  echo $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- ? Page setup -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Secretary | Announcements</title>

  <!-- ? All your existing CSS links -->
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="../assets/plugins/pace-progress/themes/black/pace-theme-flat-top.css">
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/sweetalert2/css/sweetalert2.min.css">

  <!-- ? ?? ADD THIS PART HERE (the sidebar consistency fix) -->
  <style>
    .nav-sidebar .nav-link {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 15px;
    }

    .nav-sidebar .nav-link.active {
      background-color: #6f42c1 !important; /* Purple highlight */
      color: #fff !important;
    }

    .nav-sidebar .nav-link.active i {
      color: #fff !important;
    }

    .nav-sidebar .nav-link i {
      width: 20px;
      text-align: center;
      font-size: 16px;
    }

    .nav-sidebar .nav-link:hover {
      background-color: rgba(111, 66, 193, 0.6);
      color: #fff !important;
    }
  </style>
</head>
<body class="hold-transition dark-mode sidebar-mini layout-footer-fixed">

<div class="preloader flex-column justify-content-center align-items-center">
  <img class="animation__shake"
       src="<?= htmlspecialchars($logoSrc) ?>"
       alt="Logo"
       height="60"
       width="60">
</div>


<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-dark">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#"><i class="far fa-user"></i></a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="myProfile.php" class="dropdown-item">
            <div class="media">
              <img src="../assets/dist/img/<?= $user_image ?: 'image.png' ?>" class="img-size-50 mr-3 img-circle" alt="User Image">
              <div class="media-body">
                <h3 class="dropdown-item-title"><?= ucfirst($first_name_user) . ' ' . ucfirst($last_name_user) ?></h3>
              </div>
            </div>
          </a>
          <div class="dropdown-divider"></div>
          <a href="../logout.php" class="dropdown-item dropdown-footer">LOGOUT</a>
        </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">

  <!-- Brand Logo -->
  <a href="#" class="brand-link text-center">
  <img src="<?= htmlspecialchars($logoSrc) ?>"
       id="logo_image"
       class="img-circle elevation-5 img-bordered-sm"
       style="width:70%;">
  <span class="brand-text font-weight-light"></span>
</a>

<div class="user-panel mt-3 pb-3 mb-3 d-flex">
  <div class="image">
    <img src="<?= htmlspecialchars($logoSrc) ?>" 
         class="img-circle elevation-5 img-bordered-sm" 
         alt="Logo">
  </div>
  <div class="info text-center">
    <a href="#" class="d-block text-bold text-white">OFFICIAL</a>
  </div>
</div>

 <!-- Sidebar -->
<div class="sidebar">
<nav class="mt-2">

<ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">

<!-- Dashboard -->
<li class="nav-item">
<a href="dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active bg-purple':'' ?>">
<i class="nav-icon fas fa-tachometer-alt"></i>
<p>Dashboard</p>
</a>
</li>

<!-- Homeowner Officials -->
<li class="nav-item <?= in_array(basename($_SERVER['PHP_SELF']),['allOfficial.php','officialEndTerm.php'])?'menu-open':'' ?>">
<a href="#" class="nav-link <?= in_array(basename($_SERVER['PHP_SELF']),['allOfficial.php'])?'active':'' ?>">
<i class="nav-icon fas fa-users-cog"></i>
<p>
Homeowner Officials
<i class="right fas fa-angle-left"></i>
</p>
</a>

<ul class="nav nav-treeview">

<li class="nav-item">
<a href="allOfficial.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='allOfficial.php'?'active bg-purple':'' ?>">
<i class="fas fa-circle nav-icon text-red"></i>
<p>List of Official</p>
</a>
</li>

</ul>
</li>

<!-- Residence -->
<li class="nav-item <?= in_array(basename($_SERVER['PHP_SELF']),['newResidence.php','allResidence.php','archiveResidence.php'])?'menu-open':'' ?>">

<a href="#" class="nav-link <?= in_array(basename($_SERVER['PHP_SELF']),['newResidence.php','allResidence.php','archiveResidence.php'])?'active':'' ?>">

<i class="nav-icon fas fa-users"></i>
<p>
Residence
<i class="right fas fa-angle-left"></i>
</p>
</a>

<ul class="nav nav-treeview">

<li class="nav-item">
<a href="newResidence.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='newResidence.php'?'active bg-purple':'' ?>">
<i class="fas fa-circle nav-icon text-red"></i>
<p>New Residence</p>
</a>
</li>

<li class="nav-item">
<a href="allResidence.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='allResidence.php'?'active bg-purple':'' ?>">
<i class="fas fa-circle nav-icon text-red"></i>
<p>All Residence</p>
</a>
</li>

<li class="nav-item">
<a href="archiveResidence.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='archiveResidence.php'?'active bg-purple':'' ?>">
<i class="fas fa-circle nav-icon text-red"></i>
<p>Archive Residence</p>
</a>
</li>

</ul>
</li>

<!-- Users -->
<li class="nav-item <?= basename($_SERVER['PHP_SELF'])=='usersResident.php'?'menu-open':'' ?>">

<a href="#" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='usersResident.php'?'active':'' ?>">

<i class="nav-icon fas fa-user-shield"></i>
<p>
Users
<i class="right fas fa-angle-left"></i>
</p>
</a>

<ul class="nav nav-treeview">

<li class="nav-item">
<a href="usersResident.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='usersResident.php'?'active bg-purple':'' ?>">
<i class="fas fa-circle nav-icon text-red"></i>
<p>Resident</p>
</a>
</li>

</ul>
</li>

<!-- Incident -->
<li class="nav-item">
<a href="incidentRecord.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='incidentRecord.php'?'active bg-purple':'' ?>">
<i class="nav-icon fas fa-clipboard"></i>
<p>Incident Management</p>
</a>
</li>

<!-- Reports -->
<li class="nav-item">
<a href="report.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='report.php'?'active bg-purple':'' ?>">
<i class="nav-icon fas fa-bookmark"></i>
<p>Reports</p>
</a>
</li>

<!-- Announcements -->
<li class="nav-item">
<a href="announcements.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='announcements.php'?'active bg-purple':'' ?>">
<i class="nav-icon fas fa-bullhorn"></i>
<p>Announcements</p>
</a>
</li>

<!-- Payments -->
<li class="nav-item">
<a href="payments.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='payments.php'?'active bg-purple':'' ?>">
<i class="nav-icon fas fa-money-bill-wave"></i>
<p>Payments</p>
</a>
</li>

</ul>
</nav>
</div>
</aside>


  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <h3 style="font-variant: small-caps;">Announcements</h3>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">

        <?php if (!empty($msg)): ?>
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                icon: 'info',
                title: '<?= htmlspecialchars($msg, ENT_QUOTES) ?>',
                confirmButtonColor: '#6610f2',
                timer: 2000,
                showConfirmButton: false
              });
            });
          </script>
        <?php endif; ?>

        <!-- Add Announcement -->
        <div class="card">
          <div class="card-header bg-indigo"><h5 class="card-title mb-0">Add New Announcement</h5></div>
          <div class="card-body">
            <form method="post">
              <input type="hidden" name="action" value="add">
              <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control" required>
              </div>
              <div class="form-group">
                <label>Content</label>
                <textarea name="content" rows="4" class="form-control" required></textarea>
              </div>
              <button class="btn btn-success float-right">Publish</button>
            </form>
          </div>
        </div>

        <!-- All Announcements -->
        <div class="card mt-4">
          <div class="card-header bg-indigo"><h5 class="card-title mb-0">All Announcements</h5></div>
          <div class="card-body">
            <table id="announcementsTable" class="table table-bordered table-hover table-dark">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Title</th>
                  <th>Content</th>
                  <th>Date Posted</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
            <?php while ($a = $res->fetch_assoc()): ?>
            <tr>
                <td><?= $a['id'] ?></td>
                <td><?= htmlspecialchars($a['title']) ?></td>
                <td><?= nl2br(htmlspecialchars($a['message'])) ?></td>
                <td>
                    <span class="badge <?= $a['status']=='Active' ? 'badge-success':'badge-danger' ?>">
                        <?= $a['status'] ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($a['posted_name'] ?? '—') ?></td>
                <td><?= date('M d, Y h:i A', strtotime($a['created_at'])) ?></td>
            </tr>
            <?php endwhile; ?>
            </tbody>
            </table>
          </div>
        </div>

      </div>
    </section>
  </div>

 <footer class="main-footer text-left">
  <strong>Copyright &copy; <?= date("Y") ?> - <?= date('Y', strtotime('+1 year')) ?></strong>
</footer>



</div>

<!-- JS -->
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<script src="../assets/plugins/pace-progress/pace.min.js"></script>
<script src="../assets/dist/js/adminlte.js"></script>
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../assets/plugins/sweetalert2/js/sweetalert2.all.min.js"></script>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header bg-indigo">
        <h5 class="modal-title">Edit Announcement</h5>
        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" id="edit_id">
        <div class="form-group">
          <label>Title</label>
          <input id="edit_title" name="title" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Content</label>
          <textarea id="edit_content" name="content" class="form-control" rows="5" required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button class="btn btn-success">Save changes</button>
      </div>
    </form>
  </div>
</div>

<script>
$(function() {
  $('#announcementsTable').DataTable({
    responsive: true,
    autoWidth: false,
    order: [[0, 'desc']]
  });

  $('.editBtn').on('click', function() {
    $('#edit_id').val($(this).data('id'));
    $('#edit_title').val($(this).data('title'));
    $('#edit_content').val($(this).data('content'));
    $('#editModal').modal('show');
  });
});
</script>
</body>
</html>
