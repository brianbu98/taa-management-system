<?php
require_once '../connection.php';
session_start();

if(isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'resident'){

    $sql = "SELECT 
        oi.first_name,
        oi.last_name,
        oi.image,
        oi.image_path,
        p.position
    FROM official_status os
    INNER JOIN official_information oi 
        ON os.official_id = oi.official_id
    INNER JOIN position p 
        ON os.position = p.position_id
    WHERE os.status = 'ACTIVE'";

    $result = $con->query($sql);

    // DEBUG (optional - remove later)
    if(!$result){
        die("Query Error: " . $con->error);
    }

}else{
    echo "<script>window.location.href='../login.php'</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Homeowners Officials</title>

<link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
<link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">

</head>

<body class="hold-transition layout-top-nav">

<div class="container mt-5">

<h3 class="text-center mb-4">
CURRENT HOMEOWNERS OFFICIALS
</h3>

<div class="row">

<?php if($result && $result->num_rows > 0){ ?>

    <?php while($row = $result->fetch_assoc()){ ?>

    <div class="col-md-3 text-center mb-4">

        <img 
        src="<?php echo !empty($row['image_path']) ? $row['image_path'] : '../assets/dist/img/blank_image.png'; ?>" 
        class="img-circle"
        width="120">

        <h5 class="mt-2">
        <?php echo ucfirst($row['first_name'])." ".ucfirst($row['last_name']); ?>
        </h5>

        <p class="text-muted">
        <?php echo strtoupper($row['position']); ?>
        </p>

    </div>

    <?php } ?>

<?php } else { ?>

    <div class="col-12 text-center">
        <p class="text-muted">No officials available</p>
    </div>

<?php } ?>

</div>

</div>

</body>
</html>