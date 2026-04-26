<?php
include_once '../connection.php';
session_start();

// --- SECURITY: Only admins can access ---
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$admin_id = $_SESSION['user_id'];
$msg = '';
$msg_type = '';

// --- Fetch Admin Info ---
$stmt_admin = $con->prepare("SELECT * FROM users WHERE id=?");
$stmt_admin->bind_param('i', $admin_id);
$stmt_admin->execute();
$row_admin = $stmt_admin->get_result()->fetch_assoc();
$first_name_user = $row_admin['first_name'] ?? '';
$last_name_user  = $row_admin['last_name'] ?? '';
$user_image      = $row_admin['image'] ?? 'image.png';

// --- Fetch TAA Info ---
$taa = $con->query("SELECT * FROM taa_information LIMIT 1")->fetch_assoc();
$taa_image_path = $taa['image'] ?? '../assets/dist/img/logo.png';
$postal_address = $taa['postal_address'] ?? '';

// --- Auto-generate reference number ---
$auto_reference = 'PAY-' . date('YmdHis') . '-' . rand(100,999);

// --- Handle Add Payment ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_payment') {
    $user_id   = intval($_POST['user_id'] ?? 0);
    $amount    = floatval($_POST['amount'] ?? 0);
    $reference = trim($_POST['reference'] ?? '');
    $method    = trim($_POST['method'] ?? '');

    $valid_methods = ['GCash','Bank Transfer','Cash'];

    if (!$user_id || $amount <= 0 || !in_array($method, $valid_methods)) {
        $msg = "Please complete all fields and provide a valid amount.";
        $msg_type = "error";
    } else {
        if (empty($reference)) {
            $reference = 'PAY-' . date('YmdHis') . '-' . rand(100,999);
        }

        $stmtI = $con->prepare("INSERT INTO payments (user_id, amount, reference_no, method, status, date_submitted) VALUES (?, ?, ?, ?, 'Completed', NOW())");
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

// --- Fetch Residents ---
$resResidents = $con->query("SELECT id, CONCAT(first_name,' ',last_name) AS name FROM users WHERE user_type='resident' ORDER BY last_name,first_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Payments | Admin Panel</title>

  <!-- STYLES -->
  <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  <link rel="stylesheet" href="../assets/plugins/sweetalert2/css/sweetalert2.min.css">
  <link rel="stylesheet" href="../assets/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="../assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
</head>

<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
<div class="wrapper">

  <!-- NAVBAR -->
  <nav class="main-header navbar navbar-expand navbar-dark">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-user"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="myProfile.php" class="dropdown-item">
            <div class="media">
              <img src="../assets/dist/img/<?= htmlspecialchars($user_image) ?>" class="img-size-50 mr-3 img-circle" alt="User Image">
              <div class="media-body">
                <h3 class="dropdown-item-title"><?= ucfirst($first_name_user).' '.ucfirst($last_name_user) ?></h3>
              </div>
            </div>
          </a>
          <div class="dropdown-divider"></div>
          <a href="../logout.php" class="dropdown-item dropdown-footer text-center text-danger">LOGOUT</a>
        </div>
      </li>
    </ul>
  </nav>

  <!-- SIDEBAR -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4 sidebar-no-expand">
    <a href="dashboard.php" class="brand-link text-center">
      <img src="<?= htmlspecialchars($taa_image_path) ?>" id="logo_image" class="img-circle elevation-3" alt="TAA Logo" style="width:70%;">
      <span class="brand-text font-weight-light">TAA Admin</span>
    </a>

    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="../assets/dist/img/<?= htmlspecialchars($user_image) ?>" class="img-circle elevation-2" alt="Admin Image">
        </div>
        <div class="info">
          <a href="myProfile.php" class="d-block"><?= ucfirst($first_name_user) . ' ' . ucfirst($last_name_user) ?></a>
          <small class="text-muted">Administrator</small>
        </div>
      </div>

      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu">
          <li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
          <li class="nav-item"><a href="allOfficial.php" class="nav-link"><i class="nav-icon fas fa-users-cog"></i><p>Officials</p></a></li>
          <li class="nav-item"><a href="allResidence.php" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Residents</p></a></li>
          <li class="nav-item"><a href="announcements.php" class="nav-link"><i class="nav-icon fas fa-bullhorn"></i><p>Announcements</p></a></li>
          <li class="nav-item"><a href="payments.php" class="nav-link active bg-indigo"><i class="nav-icon fas fa-money-bill-wave"></i><p>Payments</p></a></li>
          <li class="nav-item"><a href="report.php" class="nav-link"><i class="nav-icon fas fa-book"></i><p>Reports</p></a></li>
          <li class="nav-item"><a href="settings.php" class="nav-link"><i class="nav-icon fas fa-cog"></i><p>Settings</p></a></li>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- CONTENT -->
  <div class="content-wrapper p-3">

    <!-- ADD PAYMENT FORM -->
    <div class="card card-dark card-outline">
      <div class="card-header bg-indigo"><h5><i class="fas fa-plus-circle"></i> Add Payment</h5></div>
      <div class="card-body">
        <form method="post" class="row g-3">
          <input type="hidden" name="action" value="add_payment">
          <div class="col-md-4">
            <label>Resident</label>
            <select name="user_id" class="form-control" required>
              <option value="">-- Select Resident --</option>
              <?php while($r=$resResidents->fetch_assoc()): ?>
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
            <input type="text" name="reference" class="form-control" value="<?= htmlspecialchars($auto_reference) ?>">
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

    <!-- PAYMENTS TABLE -->
    <div class="card card-dark card-outline mt-3">
      <div class="card-header bg-indigo"><h5><i class="fas fa-table"></i> Payments Table</h5></div>
      <div class="card-body table-responsive">
        <table id="paymentsTable" class="table table-dark table-striped table-bordered w-100">
          <thead>
            <tr>
              <th>ID</th><th>Resident</th><th>Amount</th><th>Method</th>
              <th>Reference</th><th>Status</th><th>Date</th><th>Outstanding</th><th>Proof</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

  </div>

  <footer class="main-footer text-left">
    <strong><?= htmlspecialchars($postal_address) ?></strong>
  </footer>
</div>

<!-- SCRIPTS -->
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
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
    // Ensure the table element exists before initializing
    if (!$('#paymentsTable').length) {
        console.error("Table #paymentsTable not found.");
        return;
    }

    let table = $('#paymentsTable').DataTable({
        responsive: true,
        serverSide: true,
        ajax: 'ajax/payment_table.php',  // ? Corrected path (adjust if your structure differs, e.g., '../ajax/payment_table.php')
        columns: [
            { data: 0, orderable: false },  // Serial number
            { data: 1 },  // Name
            { data: 2 },  // Amount
            { data: 3 },  // Method
            { data: 4 },  // Reference No
            { data: 5, orderable: false },  // Status Badge
            { data: 6 },  // Date Submitted
            { data: 7, orderable: false },  // Outstanding
            { data: 8, orderable: false }   // Proof
        ],
        order: [[6, 'desc']],  // Default sort by date
        lengthChange: true,
        pageLength: 10,
        buttons: ["copy", "csv", "excel", "pdf", "print", "colvis"],
        language: {
            emptyTable: "No payments found.",
            loadingRecords: "Loading..."
        },
        error: function (xhr, error, thrown) {
            console.error("DataTables error:", error, xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error loading data',
                text: 'Check console for details or contact support.'
            });
        }
    });

    // Append buttons
    table.buttons().container().appendTo('#paymentsTable_wrapper .col-md-6:eq(0)');

    // Handle success/error messages (ensure $msg and $msg_type are set in your PHP)
    <?php
    if (!empty($msg)) {
        $icon = ($msg_type === "success") ? "success" : "error";
        $safeMsg = addslashes(htmlspecialchars($msg));
        echo "Swal.fire({ icon: '$icon', title: '$safeMsg' });";
    }
    ?>
});
</script>


</body>
</html>
