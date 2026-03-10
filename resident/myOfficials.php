<?php
require_once '../session.php';
require_once '../connection.php';

if(isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'resident'){

$sql = "SELECT 
official.*, position.position 
FROM official 
LEFT JOIN position ON official.position_id = position.position_id
WHERE official.status = 'Active'
ORDER BY position.position_id ASC";

$result = $con->query($sql);

}else{
echo "<script>window.location.href='../login.php'</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Homeowner Officials</title>
<link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
<link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
</head>

<body class="hold-transition layout-top-nav">

<div class="container mt-5">

<h3 class="text-center mb-4">
CURRENT HOMEOWNERS OFFICIALS
</h3>

<div class="row">

<?php while($row = $result->fetch_assoc()){ ?>

<div class="col-md-3 text-center mb-4">

<img src="../assets/dist/img/<?php echo $row['image']; ?>"
class="img-circle"
width="120">

<h5 class="mt-2">
<?php echo $row['first_name'].' '.$row['last_name']; ?>
</h5>

<p class="text-muted">
<?php echo $row['position']; ?>
</p>

</div>

<?php } ?>

</div>

</div>

</body>
</html>