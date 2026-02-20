<?php 
include_once '../connection.php';
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'resident'){
  header("Location: ../login.php");
  exit;
}

// Fetch Announcements
$ann_query = $con->query("SELECT * FROM announcements WHERE status='active' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Announcements</title>

<link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
<link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">

<style>
body {
    background-color: #343a40;
}

.center-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
}

.announcement-container {
    width: 100%;
    max-width: 800px;
}

.announcement-card {
    background: #495057;
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 25px;
    box-shadow: 0 0 20px rgba(0,0,0,0.3);
}

.announcement-title {
    font-size: 28px;
    font-weight: bold;
    color: #ffc107;
}

.announcement-date {
    font-size: 14px;
    color: #ccc;
    margin-bottom: 15px;
}

.announcement-message {
    font-size: 20px;
    color: #fff;
    line-height: 1.6;
}

.back-btn {
    font-size: 18px;
    padding: 10px 25px;
}
</style>

</head>

<body class="dark-mode">

<div class="center-wrapper">
<div class="announcement-container text-center">

<h1 class="text-warning mb-4" style="font-size: 48px;">
    <i class="fas fa-bullhorn"></i> Announcements
</h1>

<a href="dashboard.php" class="btn btn-outline-warning back-btn mb-5">
    <i class="fas fa-arrow-left"></i> Back to Dashboard
</a>

<?php if($ann_query && $ann_query->num_rows > 0): ?>
    <?php while($row = $ann_query->fetch_assoc()): ?>
        <div class="announcement-card text-left">
            <div class="announcement-title">
                <?= htmlspecialchars($row['title']) ?>
            </div>
            <div class="announcement-date">
                <?= date('F d, Y h:i A', strtotime($row['created_at'])) ?>
            </div>
            <div class="announcement-message">
                <?= nl2br(htmlspecialchars($row['message'])) ?>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p class="text-white" style="font-size: 26px;">
        No announcements available.
    </p>
<?php endif; ?>

</div>
</div>

</body>
</html>