<?php
require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: /login.php");
    exit;
}

$records = $con->query("
    SELECT pr.*, CONCAT(u.first_name,' ',u.last_name) AS resident
    FROM payment_records pr
    JOIN payments p ON pr.payment_id = p.id
    JOIN users u ON p.user_id = u.id
    ORDER BY pr.created_at DESC
");
?>

<h3>Payment Records</h3>

<table border="1" cellpadding="8">
<thead>
<tr>
<th>ID</th>
<th>Resident</th>
<th>Amount Paid</th>
<th>Method</th>
<th>Reference</th>
<th>Date</th>
</tr>
</thead>
<tbody>
<?php while ($r = $records->fetch_assoc()): ?>
<tr>
<td><?= $r['id'] ?></td>
<td><?= htmlspecialchars($r['resident']) ?></td>
<td>₱ <?= number_format($r['amount_paid'],2) ?></td>
<td><?= htmlspecialchars($r['payment_method']) ?></td>
<td><?= htmlspecialchars($r['reference_no']) ?></td>
<td><?= date('M d, Y h:i A', strtotime($r['created_at'])) ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>