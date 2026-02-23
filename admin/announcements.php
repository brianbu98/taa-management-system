<?php
include_once '../connection.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = "";

/* ================= ADMIN INFO ================= */
$stmt_user = $con->prepare("SELECT first_name, last_name, user_type, image FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

$first_name_user = $user['first_name'] ?? '';
$last_name_user  = $user['last_name'] ?? '';
$user_type       = $user['user_type'] ?? '';
$user_image      = $user['image'] ?? 'image.png';

/* ================= HANDLE FORM ACTIONS ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    // ADD
    if ($action === 'add') {
        $title   = trim($_POST['title'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($title && $message) {
            $stmt = $con->prepare("
                INSERT INTO announcements (title, message, posted_by, status)
                VALUES (?, ?, ?, 'active')
            ");
            $stmt->bind_param("ssi", $title, $message, $user_id);
            $stmt->execute();
            $msg = "Announcement published successfully.";
        } else {
            $msg = "Please fill in all fields.";
        }
    }

    // EDIT
    if ($action === 'edit') {
        $id      = intval($_POST['id']);
        $title   = trim($_POST['title'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $status  = $_POST['status'] ?? 'active';

        if ($id && $title && $message) {
            $stmt = $con->prepare("
                UPDATE announcements
                SET title = ?, message = ?, status = ?
                WHERE id = ?
            ");
            $stmt->bind_param("sssi", $title, $message, $status, $id);
            $stmt->execute();
            $msg = "Announcement updated successfully.";
        }
    }

    // DELETE
    if ($action === 'delete') {
        $id = intval($_POST['id']);
        $stmt = $con->prepare("DELETE FROM announcements WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $msg = "Announcement deleted.";
    }
}

/* ================= FETCH ANNOUNCEMENTS ================= */
$result = $con->query("
    SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) AS author
    FROM announcements a
    LEFT JOIN users u ON a.posted_by = u.id
    ORDER BY a.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Announcements</title>

<link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
<link rel="stylesheet" href="../assets/plugins/sweetalert2/css/sweetalert2.min.css">
</head>

<body class="hold-transition dark-mode sidebar-mini">
<div class="wrapper">

<div class="content-wrapper p-4">

<h3 class="mb-3">Announcements</h3>

<?php if ($msg): ?>
<script>
document.addEventListener('DOMContentLoaded', function(){
    Swal.fire({
        icon: 'success',
        title: "<?= htmlspecialchars($msg, ENT_QUOTES) ?>",
        timer: 1500,
        showConfirmButton: false
    });
});
</script>
<?php endif; ?>

<!-- ADD ANNOUNCEMENT -->
<div class="card card-outline card-primary">
    <div class="card-header"><h5>Add Announcement</h5></div>
    <div class="card-body">
        <form method="post">
            <input type="hidden" name="action" value="add">

            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Message</label>
                <textarea name="message" rows="4" class="form-control" required></textarea>
            </div>

            <button class="btn btn-success">Publish</button>
        </form>
    </div>
</div>

<!-- ANNOUNCEMENTS TABLE -->
<div class="card card-outline card-primary mt-4">
<div class="card-header"><h5>All Announcements</h5></div>
<div class="card-body table-responsive">

<table id="annTable" class="table table-dark table-bordered table-striped">
<thead>
<tr>
<th>ID</th>
<th>Title</th>
<th>Message</th>
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
<td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
<td>
<?php if ($row['status'] === 'active'): ?>
<span class="badge badge-success">Active</span>
<?php else: ?>
<span class="badge badge-secondary">Inactive</span>
<?php endif; ?>
</td>
<td><?= htmlspecialchars($row['author']) ?></td>
<td><?= date('M d, Y h:i A', strtotime($row['created_at'])) ?></td>
<td>

<button class="btn btn-warning btn-sm editBtn"
data-id="<?= $row['id'] ?>"
data-title="<?= htmlspecialchars($row['title'], ENT_QUOTES) ?>"
data-message="<?= htmlspecialchars($row['message'], ENT_QUOTES) ?>"
data-status="<?= $row['status'] ?>">
<i class="fas fa-edit"></i>
</button>

<form method="post" style="display:inline;" onsubmit="return confirm('Delete this announcement?');">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="id" value="<?= $row['id'] ?>">
<button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
</form>

</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

</div>
</div>

</div>
</div>

<script src="../assets/plugins/jquery/jquery.min.js"></script>
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../assets/plugins/sweetalert2/js/sweetalert2.all.min.js"></script>

<script>
$(function(){
    $('#annTable').DataTable();

    $('.editBtn').click(function(){
        let id = $(this).data('id');
        let title = $(this).data('title');
        let message = $(this).data('message');
        let status = $(this).data('status');

        Swal.fire({
            title: 'Edit Announcement',
            html:
                '<input id="swal-title" class="swal2-input" value="'+title+'">' +
                '<textarea id="swal-message" class="swal2-textarea">'+message+'</textarea>',
            showCancelButton: true,
            confirmButtonText: 'Save',
            preConfirm: () => {
                let form = $('<form method="post"></form>');
                form.append('<input type="hidden" name="action" value="edit">');
                form.append('<input type="hidden" name="id" value="'+id+'">');
                form.append('<input type="hidden" name="title" value="'+$('#swal-title').val()+'">');
                form.append('<input type="hidden" name="message" value="'+$('#swal-message').val()+'">');
                form.append('<input type="hidden" name="status" value="'+status+'">');
                $('body').append(form);
                form.submit();
            }
        });
    });
});
</script>

</body>
</html>
