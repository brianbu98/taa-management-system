<?php  

include_once '../connection.php';

$id = $_POST['id'];
$address = $_POST['address'] ?? '';
$postal_address = $_POST['postal_address'] ?? '';
$bg_color = $_POST['bg_color'] ?? '#343a40';
$dark_mode = $_POST['dark_mode'] ?? 1;

$new_image_name = '';
$new_image_path = '';

$sql = "SELECT id,image,image_path FROM taa_information WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param('i',$id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$old_image = $row['image'] ?? '';
$old_image_path = $row['image_path'] ?? '';

/* CHECK IF NEW IMAGE WAS UPLOADED */
if(isset($_FILES['add_image']) && $_FILES['add_image']['error'] == 0){

    $image = $_FILES['add_image']['name'];
    $type = strtolower(pathinfo($image, PATHINFO_EXTENSION));

    /* OPTIONAL: validate image type */
    $allowed = ['jpg','jpeg','png','gif'];

    if(in_array($type,$allowed)){

        $new_image_name = uniqid().'.'.$type;
        $new_image_path = '../assets/dist/img/'.$new_image_name;

        move_uploaded_file($_FILES['add_image']['tmp_name'],$new_image_path);

        /* DELETE OLD IMAGE */
        if(!empty($old_image_path) && file_exists($old_image_path)){
            unlink($old_image_path);
        }

    } else {
        echo "invalid_image";
        exit;
    }

}else{

    $new_image_name = $old_image;
    $new_image_path = $old_image_path;

}

$sql_update = "UPDATE taa_information 
               SET image=?, 
                   image_path=?, 
                   address=?, 
                   postal_address=?, 
                   bg_color=?, 
                   dark_mode=? 
               WHERE id=?";

$stmt_update = $con->prepare($sql_update);

$stmt_update->bind_param(
    "sssssii",
    $new_image_name,
    $new_image_path,
    $address,
    $postal_address,
    $bg_color,
    $dark_mode,
    $id
);

$stmt_update->execute();

echo "success";

?>