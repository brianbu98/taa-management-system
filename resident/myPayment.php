<?php
include_once '../connection.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'resident') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$sql_user = "SELECT * FROM users WHERE id = ?";
$stmt_user = $con->prepare($sql_user);
$stmt_user->bind_param('i', $user_id);
$stmt_user->execute();
$row_user = $stmt_user->get_result()->fetch_assoc();
$last_name_user = $row_user['last_name'] ?? '';

// Fetch TAA info
$sql_taa = "SELECT * FROM taa_information LIMIT 1";
$res_taa = $con->query($sql_taa);
$row_taa = $res_taa->fetch_assoc();
$image = $row_taa['image'] ?? '';
$postal_address = $row_taa['postal_address'] ?? '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $method = $_POST['method'];
    $type = $_POST['type'];
    $proof = null;

    // Handle file upload
    if (isset($_FILES['proof']) && $_FILES['proof']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

        $filename = time() . '_' . basename($_FILES['proof']['name']);
        $target_path = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['proof']['tmp_name'], $target_path)) {
            $proof = $filename;
        }
    }

    // Insert payment into payments_record
    // Insert into payments
$stmt = $con->prepare("INSERT INTO payments (user_id, type, amount, method, proof, status, date_submitted)
                       VALUES (?, ?, ?, ?, ?, 'Pending', NOW())");
$stmt->bind_param('isdss', $user_id, $type, $amount, $method, $proof);
$stmt->execute();

echo "<script>alert('Payment submitted successfully!'); window.location.replace(window.location.href);</script>";
exit;
}

// Fetch Outstanding Balance
$sql_outstanding = "SELECT type, amount, status FROM payments WHERE user_id = ? AND status != 'Completed'";
$stmt_out = $con->prepare($sql_outstanding);
$stmt_out->bind_param('i', $user_id);
$stmt_out->execute();
$res_out = $stmt_out->get_result();

$outstanding_data = [];
$total_outstanding = 0;

while ($row = $res_out->fetch_assoc()) {
    $outstanding_data[] = [
        'type' => htmlspecialchars($row['type']),
        'amount' => '? ' . number_format($row['amount'], 2),
        'status' => $row['status']
    ];
    $total_outstanding += $row['amount'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payments</title>
<link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<style>
body { background: url('../assets/dist/img/your-background.jpg') no-repeat center center fixed; background-size: cover; }
.content-wrapper { background-color: transparent; }
.navbar, .main-footer { background-color: #2e8b5f; }
.card-section { background: rgba(30,30,30,0.85); padding: 25px; border-radius: 8px; border-left: 5px solid #2e8b5f; margin-bottom: 30px; }
.card-section h4, .card-section h2 { color: #ffcc00; text-align: center; margin-bottom: 20px; }
.table th { background: #2e8b5f; color: white; }
.table td { color: #ddd; }
.main-footer { background-color: #2e8b5f; color: white; padding: 10px 20px; border-top: none; position: fixed; bottom: 0; left: 0; width: 100%; text-align: left; }
label { font-weight: bold; color: #fff; }
</style>
</head>
<body class="layout-top-nav dark-mode">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md">
    <div class="container">
      <a href="#" class="navbar-brand">
        <img src="../assets/dist/img/<?= htmlspecialchars($image) ?>" alt="logo" class="brand-image img-circle">
        <span class="brand-text text-white font-weight-bold">TEREMIL ASSISTANCE APPLICATION</span>
      </a>
      <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
          <li class="nav-item"><a href="dashboard.php" class="nav-link text-white"><i class="fas fa-home"></i> DASHBOARD</a></li>
          <li class="nav-item"><a href="profile.php" class="nav-link text-white"><i class="fas fa-user-alt"></i> <?= htmlspecialchars($last_name_user) ?> - <?= htmlspecialchars($user_id) ?></a></li>
          <li class="nav-item"><a href="../logout.php" class="nav-link text-white"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
      </ul>
    </div>
  </nav>

  <div class="content-wrapper">
    <div class="content pt-5">
      <div class="container">

        <!-- Payment Form -->
        <div class="card-section">
          <h4><i class="fas fa-money-bill-wave"></i> Submit a Payment</h4>
          <form method="post" enctype="multipart/form-data">
            <div class="form-group">
              <label>Payment Type</label>
              <select name="type" class="form-control" required>
                <option value="">-- Select --</option>
                <option value="Association Dues">Association Dues</option>
                <option value="Car Sticker">Car Sticker</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <div class="form-group">
              <label>Amount (?)</label>
              <input type="number" step="0.01" name="amount" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Payment Method</label>
              <select name="method" class="form-control" required>
                <option value="">-- Select --</option>
                <option value="GCash">GCash</option>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="Cash">Cash</option>
              </select>
            </div>
            <div class="form-group">
              <label>Upload Proof of Payment (optional)</label>
              <input type="file" name="proof" accept="image/*" class="form-control">
            </div>
            <button type="submit" class="btn btn-warning text-dark font-weight-bold"><i class="fas fa-check"></i> Submit Payment</button>
          </form>
        </div>

        <!-- Outstanding Balance -->
        <div class="card-section">
          <h4><i class="fas fa-exclamation-circle text-danger"></i> Outstanding Balance</h4>
          <?php if (count($outstanding_data) > 0): ?>
          <table class="table table-bordered table-striped">
            <thead>
              <tr><th>Type</th><th>Amount</th><th>Status</th></tr>
            </thead>
            <tbody>
              <?php foreach($outstanding_data as $ob): ?>
              <tr>
                <td><?= $ob['type'] ?></td>
                <td><?= $ob['amount'] ?></td>
                <td>
                  <?php
                  echo match($ob['status']) {
                      'Pending' => '<span class="badge badge-warning">Pending</span>',
                      'Rejected' => '<span class="badge badge-danger">Rejected</span>',
                      default => '<span class="badge badge-secondary">' . htmlspecialchars($ob['status']) . '</span>',
                  };
                  ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr><th>Total</th><th>? <?= number_format($total_outstanding, 2) ?></th><th></th></tr>
            </tfoot>
          </table>
          <?php else: ?>
            <p class="text-white text-center">No outstanding balances.</p>
          <?php endif; ?>
        </div>

        <!-- Payment History -->
        <div class="card-section">
          <h2><i class="fas fa-receipt text-success"></i> My Payment History</h2>
          <table id="paymentTable" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Proof</th>
                <th>Status</th>
                <th>Date Submitted</th>
                <th>Outstanding</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

      </div>
    </div>
  </div>

  <footer class="main-footer text-white">
    <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($postal_address) ?>
  </footer>
</div>

<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    const userId = <?= json_encode($user_id) ?>;
    console.log("Loading payments for user:", userId);

    $('#paymentTable').DataTable({
        ajax: {
            url: "myPaymentTable.php",
            type: "POST",
            data: function(d) {
                d.user_id = userId;
            },
            dataSrc: function(json) {
                console.log("Response:", json);
                return json.data;
            }
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7 }
        ],
        language: { emptyTable: "No payments found." },
        responsive: true
    });
});

</script>
</body>
</html>
