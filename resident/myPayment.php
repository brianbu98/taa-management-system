<?php
include_once '../connection.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'resident') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* =========================
   FETCH USER BILLS
========================= */
$bills = $con->prepare("SELECT * FROM payments WHERE user_id=? ORDER BY created_at DESC");
$bills->bind_param("i", $user_id);
$bills->execute();
$bills_result = $bills->get_result();

/* =========================
   HANDLE PAYMENT SUBMIT
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $payment_id    = intval($_POST['payment_id']);
    $amount_paid   = floatval($_POST['amount_paid']);
    $payment_method= $_POST['payment_method'];
    $reference_no  = $_POST['reference_no'] ?? '';

    $stmt = $con->prepare("
        INSERT INTO payment_records
        (payment_id, amount_paid, payment_method, reference_no, received_by, created_at)
        VALUES (?, ?, ?, ?, 0, NOW())
    ");

    $stmt->bind_param("idss", $payment_id, $amount_paid, $payment_method, $reference_no);
    $stmt->execute();

    echo "<script>alert('Payment submitted successfully'); window.location.reload();</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payments</title>

<link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
<link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
</head>

<body class="layout-top-nav dark-mode">

<div class="wrapper">
<div class="content-wrapper p-5 bg-dark">
<div class="container">

<a href="dashboard.php" class="btn btn-secondary mb-4">
<i class="fas fa-arrow-left"></i> Back
</a>

<h3 class="text-warning mb-4">Submit Payment</h3>

<form method="post">

<div class="form-group">
<label>Select Bill</label>
<select name="payment_id" class="form-control" required>
<option value="">-- Select --</option>
<?php while($bill = $bills_result->fetch_assoc()): ?>
<option value="<?= $bill['id'] ?>">
<?= htmlspecialchars($bill['payment_name']) ?> 
(₱ <?= number_format($bill['amount_due'],2) ?>)
</option>
<?php endwhile; ?>
</select>
</div>

<div class="form-group">
<label>Amount Paid</label>
<input type="number" step="0.01" name="amount_paid" class="form-control" required>
</div>

<div class="form-group">
<label>Payment Method</label>
<select name="payment_method" class="form-control" required>
<option value="cash">Cash</option>
<option value="gcash">GCash</option>
</select>
</div>

<div class="form-group">
<label>Reference No</label>
<input type="text" name="reference_no" class="form-control">
</div>

<button type="submit" class="btn btn-warning">Submit</button>
</form>

<hr class="bg-secondary">

<h3 class="text-success mt-5">Payment History</h3>

<table id="paymentTable" class="table table-bordered table-striped">
<thead>
<tr>
<th>#</th>
<th>Payment</th>
<th>Amount Paid</th>
<th>Method</th>
<th>Reference</th>
<th>Date</th>
</tr>
</thead>
<tbody></tbody>
</table>

</div>
</div>
</div>

<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {

$('#paymentTable').DataTable({
    processing: true,
    ajax: {
        url: "myPaymentTable.php",
        type: "POST",
        data: { user_id: <?= $user_id ?> }
    },
    columns: [
        { data: 0 },
        { data: 1 },
        { data: 2 },
        { data: 3 },
        { data: 4 },
        { data: 5 }
    ]
});

});
</script>

</body>
</html>