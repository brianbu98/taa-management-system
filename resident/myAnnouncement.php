<?php
include_once '../connection.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'resident') {
    header("Location: ../login.php");
    exit;
}

$ann_query = $con->query("
SELECT * FROM announcements 
WHERE status='active'
ORDER BY created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Announcements</title>
<link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
<link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
</head>

<body class="layout-top-nav dark-mode">

<div class="wrapper">
<div class="content-wrapper bg-dark d-flex align-items-center justify-content-center" style="min-height:100vh;">
<div class="container text-center">

<a href="dashboard.php" class="btn btn-secondary mb-4">
<i class="fas fa-arrow-left"></i> Back
</a>

<h1 class="text-warning mb-5" style="font-size:42px;">Announcements</h1>

<?php if($ann_query->num_rows > 0): ?>
<?php while($row = $ann_query->fetch_assoc()): ?>

<div class="mb-5 p-4" style="background:#1e1e1e; border-radius:10px;">
<h2 class="text-warning"><?= htmlspecialchars($row['title']) ?></h2>
<p class="text-muted" style="font-size:18px;">
<?= date("F d, Y h:i A", strtotime($row['created_at'])) ?>
</p>
<p style="font-size:20px;">
<?= nl2br(htmlspecialchars($row['message'])) ?>
</p>
</div>

<?php endwhile; ?>

<?php else: ?>
<h3 class="text-light">No announcements available.</h3>
<?php endif; ?>

</div>
</div>
</div>

</body>
</html>