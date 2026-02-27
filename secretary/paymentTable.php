<?php
// secretary/payment_table.php
include_once '../connection.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'secretary') {
    header("Location: ../login.php");
    exit;
}

// AJAX endpoint - limited view (secretary can see all or by barangay if you use barangay_id)
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    $draw = intval($_GET['draw'] ?? 1);
    $start = intval($_GET['start'] ?? 0);
    $length = intval($_GET['length'] ?? 10);
    $search = $con->real_escape_string($_GET['search']['value'] ?? '');

    $where = "1=1";
    if ($search !== '') {
        $where .= " AND (u.first_name LIKE '%$search%' OR u.last_name LIKE '%$search%' OR p.reference_no LIKE '%$search%')";
    }

    $total = $con->query("SELECT COUNT(*) AS cnt FROM payments p")->fetch_assoc()['cnt'];
    $filtered = $con->query("SELECT COUNT(*) AS cnt FROM payments p LEFT JOIN users u ON p.user_id = u.id WHERE $where")->fetch_assoc()['cnt'];

    $sql = "
    SELECT pr.id,
           CONCAT(u.first_name,' ',u.last_name) AS name,
           pr.amount_paid,
           pr.reference_no,
           pr.payment_method,
           pr.created_at
    FROM payment_records pr
    LEFT JOIN payments p ON pr.payment_id = p.id
    LEFT JOIN users u ON p.user_id = u.id
    ORDER BY pr.created_at DESC
    LIMIT $start,$length
    ";

    $data = [];
    while ($r = $res->fetch_assoc()) {
        $row = [];
        $row[] = htmlspecialchars($r['id']);
        $row[] = htmlspecialchars($r['name']);
        $row[] = '₱' . number_format($r['amount_paid'],2);
        $row[] = htmlspecialchars($r['reference_no']);
        $row[] = htmlspecialchars($r['payment_method']);
        $row[] = date('M d, Y h:i A', strtotime($r['created_at']));
        $row[] = '<button class="btn btn-sm btn-info viewPayment" data-id="'.$r['id'].'">View</button>';
        $data[] = $row;
    }

    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => intval($total),
        'recordsFiltered' => intval($filtered),
        'data' => $data
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Secretary - Payments Table</title>
<link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
</head>
<body class="hold-transition dark-mode sidebar-mini">
<div class="wrapper">

  <div class="content-wrapper">
    <div class="content p-3">
      <h4 class="text-warning"><i class="fas fa-table"></i> Payments</h4>
      <div class="card bg-dark">
        <div class="card-body">
          <table id="paymentsTable" class="table table-dark table-striped w-100">
            <thead><tr><th>ID</th><th>Resident</th><th>Amount</th><th>Reference</th><th>Method</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script>
$(function(){
  $('#paymentsTable').DataTable({
    processing:true,
    serverSide:true,
    ajax: 'payment_table.php?ajax=1',
    columns: [null,null,null,null,null,null,{orderable:false, searchable:false}]
  });
});
</script>
</body>
</html>
