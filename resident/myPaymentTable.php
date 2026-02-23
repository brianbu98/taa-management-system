<?php
include_once '../connection.php';

header('Content-Type: application/json');

if (!isset($_POST['user_id'])) {
    echo json_encode(["data"=>[]]);
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
    echo json_encode([
        "data"=>[],
        "error"=>$con->error
    ]);
    exit;
}

$stmt->bind_param("i", $user_id);

if (!$stmt->execute()) {
    echo json_encode([
        "data"=>[],
        "error"=>$stmt->error
    ]);
    exit;
}

$result = $stmt->get_result();

$data = [];
$count = 1;

while ($row = $result->fetch_assoc()) {
    $data[] = [
        $count++,
        $row['payment_name'],
        $row['amount_paid'],
        $row['payment_method'],
        $row['reference_no'],
        $row['created_at']
    ];
}

echo json_encode(["data"=>$data]);
exit;