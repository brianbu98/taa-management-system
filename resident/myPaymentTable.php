<?php
include_once '../connection.php';

header('Content-Type: application/json');

$response = ["data" => []];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode($response);
    exit;
}

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
    echo json_encode($response);
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$count = 1;

while ($row = $result->fetch_assoc()) {

    $response["data"][] = [
        $count++,
        $row['payment_name'],
        '₱ ' . number_format((float)$row['amount_paid'], 2),
        ucfirst($row['payment_method']),
        $row['reference_no'] ?: '-',
        date("m/d/Y - h:i A", strtotime($row['created_at']))
    ];
}

echo json_encode($response);
exit;