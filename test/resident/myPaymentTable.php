<?php
include_once '../connection.php';
header('Content-Type: application/json; charset=utf-8');
ob_clean(); // clear any accidental whitespace

if (empty($_POST['user_id'])) {
    echo json_encode(['data' => []]);
    exit;
}

$user_id = intval($_POST['user_id']);

// Fetch payments for this user
$sql = "SELECT * FROM payments WHERE user_id = ? ORDER BY date_submitted DESC";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
$count = 1;

while ($row = $result->fetch_assoc()) {
    $amount = '? ' . number_format($row['amount'], 2);
    $date_paid = date("m/d/Y - h:i A", strtotime($row['date_submitted']));
    $method = htmlspecialchars(ucfirst($row['method']));
    $type = htmlspecialchars($row['type'] ?? '');
    $status = $row['status'] ?? 'Pending';

    // Status badge
    $status_badge = match($status) {
        'Completed' => '<span class="badge badge-success">Completed</span>',
        'Rejected'  => '<span class="badge badge-danger">Rejected</span>',
        default     => '<span class="badge badge-warning">Pending</span>',
    };

    // Proof link
    $proof = (!empty($row['proof']) && file_exists("../uploads/" . $row['proof']))
             ? "<a href='../uploads/" . htmlspecialchars($row['proof']) . "' target='_blank'>View</a>"
             : "<span class='text-muted'>No proof</span>";

    // Outstanding
    $outstanding = ($status !== 'Completed') ? 'Yes' : 'No';

    $data[] = [
        $count++,
        $type,
        $amount,
        $method,
        $proof,
        $status_badge,
        $date_paid,
        $outstanding
    ];
}

echo json_encode(["data" => $data], JSON_UNESCAPED_UNICODE);
exit;
