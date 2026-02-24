<?php
require_once __DIR__ . '/../session.php';
require_once __DIR__ . '/../connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: /login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = "";

/* ADD / DELETE */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_POST['action'] === 'add') {
        $title   = trim($_POST['title']);
        $message = trim($_POST['message']);

        $stmt = $con->prepare("
            INSERT INTO announcements (title, message, posted_by, status)
            VALUES (?, ?, ?, 'active')
        ");
        $stmt->bind_param("ssi", $title, $message, $user_id);
        $stmt->execute();

        $msg = "Announcement added successfully.";
    }

    if ($_POST['action'] === 'delete') {
        $id = intval($_POST['id']);
        $stmt = $con->prepare("DELETE FROM announcements WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $msg = "Announcement deleted.";
    }
}

$result = $con->query("
    SELECT a.*, CONCAT(u.first_name,' ',u.last_name) AS author
    FROM announcements a
    LEFT JOIN users u ON a.posted_by = u.id
    ORDER BY a.created_at DESC
");
?>

<h3>Announcements</h3>

<?php if ($msg): ?>
<div class="alert alert-success"><?= $msg ?></div>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="action" value="add">
    <input type="text" name="title" placeholder="Title" required>
    <textarea name="message" placeholder="Message" required></textarea>
    <button type="submit">Publish</button>
</form>

<hr>

<table border="1" cellpadding="8">
<thead>
<tr>
<th>ID</th>
<th>Title</th>
<th>Status</th>
<th>Author</th>
<th>Date</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['title']) ?></td>
<td><?= ucfirst($row['status']) ?></td>
<td><?= htmlspecialchars($row['author']) ?></td>
<td><?= date('M d, Y h:i A', strtotime($row['created_at'])) ?></td>
<td>
<form method="post">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="id" value="<?= $row['id'] ?>">
<button type="submit">Delete</button>
</form>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>