<?php
include_once '../connection.php';
header('Content-Type: application/json');

if (empty($_POST['user_id'])) {
    echo json_encode(['data' => []]);
    exit;
}

$user_id = intval($_POST['user_id']);

$sql = "
SELECT pr.*, p.payment_name
FROM payment_records pr
JOIN payments p ON pr.payment_id = p.id
WHERE p.user_id = ?
ORDER BY pr.created_at DESC
";

$stmt = $con->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
$count = 1;

while ($row = $result->fetch_assoc()) {

    $data[] = [
        $count++,
        htmlspecialchars($row['payment_name']),
        '₱ ' . number_format($row['amount_paid'],2),
        htmlspecialchars($row['payment_method']),
        htmlspecialchars($row['reference_no'] ?? '-'),
        date("m/d/Y - h:i A", strtotime($row['created_at']))
    ];
}

echo json_encode(["data" => $data]);
exit;
