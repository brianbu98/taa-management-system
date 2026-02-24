<?php
require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: /login.php");
    exit;
}

$msg = "";

/* CREATE BILL */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resident_id = intval($_POST['resident_id']);
    $amount_due  = floatval($_POST['amount_due']);
    $description = trim($_POST['description']);

    $stmt = $con->prepare("
        INSERT INTO payments (user_id, amount_due, description, status)
        VALUES (?, ?, ?, 'unpaid')
    ");
    $stmt->bind_param("ids", $resident_id, $amount_due, $description);
    $stmt->execute();

    $msg = "Bill created successfully.";
}

$residents = $con->query("
    SELECT id, CONCAT(first_name,' ',last_name) AS name
    FROM users WHERE user_type='resident'
");

$bills = $con->query("
    SELECT p.*, CONCAT(u.first_name,' ',u.last_name) AS resident
    FROM payments p
    JOIN users u ON p.user_id = u.id
    ORDER BY p.created_at DESC
");
?>

<h3>Create Bill</h3>

<?php if ($msg): ?>
<div class="alert alert-success"><?= $msg ?></div>
<?php endif; ?>

<form method="post">
<select name="resident_id" required>
<option value="">Select Resident</option>
<?php while ($r = $residents->fetch_assoc()): ?>
<option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
<?php endwhile; ?>
</select>

<input type="number" step="0.01" name="amount_due" placeholder="Amount Due" required>
<input type="text" name="description" placeholder="Description">
<button type="submit">Create Bill</button>
</form>

<hr>

<h3>All Bills</h3>

<table border="1" cellpadding="8">
<thead>
<tr>
<th>ID</th>
<th>Resident</th>
<th>Amount</th>
<th>Status</th>
<th>Date</th>
</tr>
</thead>
<tbody>
<?php while ($b = $bills->fetch_assoc()): ?>
<tr>
<td><?= $b['id'] ?></td>
<td><?= htmlspecialchars($b['resident']) ?></td>
<td>₱ <?= number_format($b['amount_due'],2) ?></td>
<td><?= ucfirst($b['status']) ?></td>
<td><?= date('M d, Y', strtotime($b['created_at'])) ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>