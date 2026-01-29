<?php
include_once '../connection.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("HTTP/1.1 403 Forbidden");
    exit('Access denied');
}

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// DataTables variables
$draw   = intval($_GET['draw'] ?? 1);
$start  = intval($_GET['start'] ?? 0);
$length = intval($_GET['length'] ?? 10);
$search = $con->real_escape_string($_GET['search']['value'] ?? '');

// WHERE clause
$where = "1=1";
$params = [];
$types  = '';

if ($search !== '') {
    $where .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR p.reference_no LIKE ?)";
    $params = ["%$search%", "%$search%", "%$search%"];
    $types  = 'sss';
}

// Total count
$totalQuery = $con->query("SELECT COUNT(*) AS cnt FROM payments");
$total = $totalQuery->fetch_assoc()['cnt'] ?? 0;

// Filtered count
$sqlFiltered = "SELECT COUNT(*) AS cnt FROM payments p 
                LEFT JOIN users u ON p.user_id = u.id 
                WHERE $where";
$stmtCount = $con->prepare($sqlFiltered);
if ($params) $stmtCount->bind_param($types, ...$params);
$stmtCount->execute();
$filtered = $stmtCount->get_result()->fetch_assoc()['cnt'] ?? 0;

// Fetch data
$sql = "SELECT p.*, CONCAT(u.first_name, ' ', u.last_name) AS name
        FROM payments p
        LEFT JOIN users u ON p.user_id = u.id
        WHERE $where
        ORDER BY p.date_submitted DESC
        LIMIT ?, ?";
$stmt = $con->prepare($sql);

if ($params) {
    $types .= 'ii';
    $params[] = $start;
    $params[] = $length;
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param('ii', $start, $length);
}

$stmt->execute();
$res = $stmt->get_result();

$data = [];
$count = $start + 1;

while ($row = $res->fetch_assoc()) {
    $status = $row['status'] ?? 'Pending';
    $status_badge = match ($status) {
        'Completed' => '<span class="badge badge-success">Completed</span>',
        'Rejected'  => '<span class="badge badge-danger">Rejected</span>',
        default     => '<span class="badge badge-warning">Pending</span>',
    };

    $proof = (!empty($row['proof']) && file_exists("../uploads/" . $row['proof']))
        ? "<a href='../uploads/" . htmlspecialchars($row['proof']) . "' target='_blank' class='text-warning'><i class='fas fa-file-alt'></i> View</a>"
        : "<span class='text-muted'>No proof</span>";

    $outstanding = ($status !== 'Completed') ? 'Yes' : 'No';

    $data[] = [
        $count++,
        htmlspecialchars($row['name']),
        '? ' . number_format($row['amount'], 2),
        htmlspecialchars($row['method']),
        htmlspecialchars($row['reference_no']),
        $status_badge,
        date('M d, Y h:i A', strtotime($row['date_submitted'])),
        $outstanding,
        $proof
    ];
}

echo json_encode([
    'draw'            => $draw,
    'recordsTotal'    => intval($total),
    'recordsFiltered' => intval($filtered),
    'data'            => $data
], JSON_UNESCAPED_UNICODE);
exit;
?>
