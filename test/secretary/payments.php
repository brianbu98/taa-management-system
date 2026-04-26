<?php
// secretary/payments.php
include_once '../connection.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'secretary') {
    header("Location: ../login.php");
    exit;
}

$sec_id = $_SESSION['user_id'];
$msg = "";
$msg_type = "";

// Fetch secretary info
$stmt = $con->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param('i', $sec_id);
$stmt->execute();
$sec = $stmt->get_result()->fetch_assoc();
$first_name_user = $sec['first_name'] ?? '';
$last_name_user  = $sec['last_name'] ?? '';
$user_image      = $sec['image'] ?? '';

$brgy = $con->query("SELECT * FROM taa_information LIMIT 1")->fetch_assoc() ?: [];
$taa_image_path = $brgy['image_path'] ?? '../assets/dist/img/logo.png';

// Handle Add Payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_payment') {
    $user_id   = intval($_POST['user_id'] ?? 0);
    $amount    = floatval($_POST['amount'] ?? 0);
    $reference = trim($_POST['reference'] ?? '');
    $method    = trim($_POST['method'] ?? '');

    if (!$user_id || $amount <= 0 || $reference === '' || $method === '') {
        $msg = "Please complete all fields and provide a valid amount.";
        $msg_type = "error";
    } else {
        $stmtI = $con->prepare("INSERT INTO payments (user_id, amount, reference_no, method, date_submitted) VALUES (?, ?, ?, ?, NOW())");
        $stmtI->bind_param('idss', $user_id, $amount, $reference, $method);
        if ($stmtI->execute()) {
            $msg = "Payment recorded successfully.";
            $msg_type = "success";
        } else {
            $msg = "Error: " . $stmtI->error;
            $msg_type = "error";
        }
    }
}

// Fetch residents for dropdown
$resResidents = $con->query("SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM users WHERE user_type = 'resident' ORDER BY last_name, first_name");

// Fetch payments
$res = $con->query("SELECT p.*, CONCAT(u.first_name, ' ', u.last_name) AS name FROM payments p LEFT JOIN users u ON p.user_id = u.id ORDER BY p.date_submitted DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Secretary - Payments</title>

<link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
<link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
<link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="../assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed">
<div class="wrapper">

  <!-- Navbar -->
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
            <div class="media">
              <img src="../assets/dist/img/<?= htmlspecialchars($user_image ?: 'image.png') ?>" class="img-size-50 mr-3 img-circle" alt="User Image">
              <div class="media-body">
                <h3 class="dropdown-item-title"><?= htmlspecialchars(ucfirst($first_name_user) . ' ' . ucfirst($last_name_user)) ?></h3>
              </div>
            </div>
          </a>
          <div class="dropdown-divider"></div>
          <a href="../logout.php" class="dropdown-item dropdown-footer">LOGOUT</a>
        </div>
      </li>
    </ul>
  </nav>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">

  <!-- Brand Logo -->
  <div class="text-center mt-3">
    <img src="<?= htmlspecialchars($taa_image_path) ?>" 
         alt="Sample Logo" 
         class="brand-image img-circle elevation-3" 
         style="width:100px; height:100px; object-fit:cover;">
  </div>

  <!-- Role Label -->
  <div class="text-center mt-2 mb-2">
    <i class="fas fa-cog text-info"></i>
    <span class="text-white font-weight-bold ml-1">OFFICIAL</span>
  </div>

  <!-- Sidebar -->
  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">

        <!-- Dashboard -->
        <li class="nav-item">
          <a href="dashboard.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active bg-purple' : '' ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <!-- Homeowner Officials -->
        <li class="nav-item">
          <a href="allOfficial.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'allOfficial.php' ? 'active bg-purple' : '' ?>">
            <i class="nav-icon fas fa-users-cog"></i>
            <p>Homeowner Officials</p>
          </a>
        </li>

        <!-- Residence -->
        <li class="nav-item">
          <a href="allResidence.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'allResidence.php' ? 'active bg-purple' : '' ?>">
            <i class="nav-icon fas fa-users"></i>
            <p>Residence</p>
          </a>
        </li>

        <!-- Users -->
        <li class="nav-item">
          <a href="usersResident.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'usersResident.php' ? 'active bg-purple' : '' ?>">
            <i class="nav-icon fas fa-user-shield"></i>
            <p>Users</p>
          </a>
        </li>

        <!-- Incident Management -->
        <li class="nav-item">
          <a href="incidentRecord.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'incidentRecord.php' ? 'active bg-purple' : '' ?>">
            <i class="nav-icon fas fa-clipboard"></i>
            <p>Incident Management</p>
          </a>
        </li>

        <!-- Reports -->
        <li class="nav-item">
          <a href="report.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'report.php' ? 'active bg-purple' : '' ?>">
            <i class="nav-icon fas fa-bookmark"></i>
            <p>Reports</p>
          </a>
        </li>

        <!-- Announcements -->
        <li class="nav-item">
          <a href="announcements.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'announcements.php' ? 'active bg-purple' : '' ?>">
            <i class="nav-icon fas fa-bullhorn"></i>
            <p>Announcements</p>
          </a>
        </li>

        <!-- Payments -->
        <li class="nav-item">
          <a href="payments.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'payments.php' ? 'active bg-purple' : '' ?>">
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
        <div class="row mb-2"><div class="col-sm-6"><h3 class="m-0 text-white">Manage Payments</h3></div></div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <!-- Add Payment Form -->
        <div class="card card-dark card-outline">
          <div class="card-header bg-indigo">
            <h5 class="mb-0 text-white"><i class="fas fa-plus-circle"></i> Add Payment</h5>
          </div>
          <div class="card-body">
            <form method="post" id="addPaymentForm" class="row g-3">
              <input type="hidden" name="action" value="add_payment">
              <div class="col-md-4">
                <label>Resident</label>
                <select name="user_id" class="form-control" required>
                  <option value="">-- Select Resident --</option>
                  <?php while($r = $resResidents->fetch_assoc()): ?>
                    <option value="<?= intval($r['id']) ?>"><?= htmlspecialchars($r['name']) ?></option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div class="col-md-2">
                <label>Amount (?)</label>
                <input type="number" step="0.01" name="amount" class="form-control" required>
              </div>
              <div class="col-md-3">
                <label>Reference No.</label>
                <input type="text" name="reference" class="form-control" required>
              </div>
              <div class="col-md-2">
                <label>Method</label>
                <select name="method" class="form-control" required>
                  <option value="">-- Select --</option>
                  <option value="GCash">GCash</option>
                  <option value="Bank Transfer">Bank Transfer</option>
                  <option value="Cash">Cash</option>
                </select>
              </div>
              <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-warning w-100">Submit</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Payments Table -->
        <div class="card card-dark card-outline mt-3">
          <div class="card-header bg-indigo">
            <h5 class="mb-0 text-white"><i class="fas fa-table"></i> Payments Table</h5>
          </div>
          <div class="card-body table-responsive">
            <table id="paymentsTable" class="table table-dark table-striped table-bordered">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Resident</th>
                  <th>Amount</th>
                  <th>Method</th>
                  <th>Reference</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <?php while($rw = $res->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($rw['id']) ?></td>
                    <td><?= htmlspecialchars($rw['name'] ?? '—') ?></td>
                    <td>?<?= number_format($rw['amount'], 2) ?></td>
                    <td><?= htmlspecialchars($rw['method']) ?></td>
                    <td><?= htmlspecialchars($rw['reference_no']) ?></td>
                    <td><?= htmlspecialchars(date('M d, Y h:i A', strtotime($rw['date_submitted']))) ?></td>
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
<script src="../assets/dist/js/adminlte.js"></script>

<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="../assets/plugins/jszip/jszip.min.js"></script>
<script src="../assets/plugins/pdfmake/pdfmake.min.js"></script>
<script src="../assets/plugins/pdfmake/vfs_fonts.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="../assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function() {
  $("#paymentsTable").DataTable({
    responsive: true,
    autoWidth: false,
    lengthChange: true,
    pageLength: 10,
    order: [[5, "desc"]],
    buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"]
  }).buttons().container().appendTo('#paymentsTable_wrapper .col-md-6:eq(0)');

  <?php if (!empty($msg)): ?>
    Swal.fire({
      icon: '<?= $msg_type === "success" ? "success" : ($msg_type === "error" ? "error" : "info") ?>',
      title: '<?= addslashes(htmlspecialchars($msg)) ?>',
      showConfirmButton: true
    });
  <?php endif; ?>
});
</script>
</body>
</html>
