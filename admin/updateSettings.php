<?php  

include_once '../connection.php';

$id = $_POST['id'];
$address = $_POST['address'];
$postal_address = $_POST['postal_address'];

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

if(isset($_FILES['add_image']) && $_FILES['add_image']['error'] == 0){

    $image = $_FILES['add_image']['name'];
    $type = pathinfo($image, PATHINFO_EXTENSION);

    $new_image_name = uniqid().'.'.$type;
    $new_image_path = '../assets/dist/img/'.$new_image_name;

    move_uploaded_file($_FILES['add_image']['tmp_name'],$new_image_path);

    if(!empty($old_image_path) && file_exists($old_image_path)){
        unlink($old_image_path);
    }

}else{

    $new_image_name = $old_image;
    $new_image_path = $old_image_path;

}

$sql_update = "UPDATE taa_information 
               SET image=?, image_path=?, address=?, postal_address=? 
               WHERE id=?";

$stmt_update = $con->prepare($sql_update);
$stmt_update->bind_param("ssssi",$new_image_name,$new_image_path,$address,$postal_address,$id);
$stmt_update->execute();

echo "success";