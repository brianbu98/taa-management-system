<?php
include_once '../connection.php';
header('Content-Type: application/json');

// Always return JSON structure
$response = ["data" => []];

// If no user_id provided, return empty data
if (!isset($_POST['user_id'])) {
    echo json_encode($response);
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

if (!$stmt) {
    echo json_encode(["data" => [], "error" => $con->error]);
    exit;
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$count = 1;

while ($row = $result->fetch_assoc()) {
    $response["data"][] = [
        $count++,
        htmlspecialchars($row['payment_name']),
        '₱ ' . number_format($row['amount_paid'], 2),
        htmlspecialchars($row['payment_method']),
        htmlspecialchars($row['reference_no'] ?? '-'),
        date("m/d/Y - h:i A", strtotime($row['created_at']))
    ];
}

echo json_encode($response);
exit;