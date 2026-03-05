<?php

include_once '../connection.php';

$archive_status = 'YES';

$first_name  = isset($_POST['first_name']) ? $con->real_escape_string($_POST['first_name']) : '';
$middle_name = isset($_POST['middle_name']) ? $con->real_escape_string($_POST['middle_name']) : '';
$last_name   = isset($_POST['last_name']) ? $con->real_escape_string($_POST['last_name']) : '';
$resident_id = isset($_POST['resident_id']) ? $con->real_escape_string($_POST['resident_id']) : '';

$whereClause = [];

if(!empty($resident_id)){
    $whereClause[] = "residence_information.residence_id = '$resident_id'";
}

if(!empty($first_name)){
    $whereClause[] = "residence_information.first_name LIKE '%$first_name%'";
}

if(!empty($middle_name)){
    $whereClause[] = "residence_information.middle_name LIKE '%$middle_name%'";
}

if(!empty($last_name)){
    $whereClause[] = "residence_information.last_name LIKE '%$last_name%'";
}

$where = '';
if(count($whereClause) > 0){
    $where .= ' AND ' . implode(' AND ', $whereClause);
}

$sql = "SELECT 
residence_information.residence_id,
residence_information.first_name,
residence_information.last_name,
residence_information.middle_name,
residence_information.age,
residence_information.image,
residence_information.image_path,
residence_status.status,
residence_status.archive,
residence_status.date_added
FROM residence_information
INNER JOIN residence_status 
ON residence_information.residence_id = residence_status.residence_id
WHERE residence_status.archive = '$archive_status' $where";

$query = $con->query($sql) or die($con->error);
$totalData = $query->num_rows;
$totalFiltered = $totalData;

# Column mapping for DataTables sorting
$columns = [
0 => 'residence_information.image',
1 => 'residence_information.residence_id',
2 => 'residence_information.first_name',
3 => 'residence_information.age',
4 => 'residence_status.status'
];

# ORDER BY
if(isset($_REQUEST['order'])){
    $column_index = $_REQUEST['order'][0]['column'];
    $column_name = $columns[$column_index];
    $column_dir = $_REQUEST['order'][0]['dir'];

    $sql .= " ORDER BY $column_name $column_dir ";
}else{
    $sql .= " ORDER BY residence_status.date_added DESC ";
}

# LIMIT
if($_REQUEST['length'] != -1){
    $start = $_REQUEST['start'];
    $length = $_REQUEST['length'];
    $sql .= " LIMIT $start , $length";
}

$query = $con->query($sql) or die($con->error);

$data = [];

while($row = $query->fetch_assoc()){

    # Image
    if(!empty($row['image'])){
        $image = '<span style="cursor:pointer;" class="pop">
                    <img src="'.$row['image_path'].'" class="img-circle" width="40">
                  </span>';
    }else{
        $image = '<span style="cursor:pointer;" class="pop">
                    <img src="../assets/dist/img/blank_image.png" class="img-circle" width="40">
                  </span>';
    }

    # Middle initial
    if(!empty($row['middle_name'])){
        $middle_name = ucfirst($row['middle_name'])[0].'.';
    }else{
        $middle_name = '';
    }

    # Status switch
    if($row['status'] == 'ACTIVE'){
        $status = '<label class="switch">
                    <input type="checkbox" checked disabled>
                    <div class="slider round">
                        <span class="on">ACTIVE</span>
                        <span class="off">INACTIVE</span>
                    </div>
                  </label>';
    }else{
        $status = '<label class="switch">
                    <input type="checkbox" disabled>
                    <div class="slider round">
                        <span class="off">INACTIVE</span>
                        <span class="on">ACTIVE</span>
                    </div>
                  </label>';
    }

    $subdata = [];
    $subdata[] = $image;
    $subdata[] = $row['residence_id'];
    $subdata[] = ucfirst($row['first_name']).' '.$middle_name.' '.ucfirst($row['last_name']);
    $subdata[] = $row['age'];
    $subdata[] = $status;

    $subdata[] = '
    <i style="cursor:pointer;color:yellow;text-shadow:-1px 0 black,0 1px black,1px 0 black,0 -1px black;"
    class="fa fa-user-edit text-lg px-3 viewResidence"
    id="'.$row['residence_id'].'"></i>

    <i style="cursor:pointer;color:red;text-shadow:-1px 0 black,0 1px black,1px 0 black,0 -1px black;"
    class="fa fa-times text-lg px-2 unArchiveResidence"
    id="'.$row['residence_id'].'"></i>';

    $data[] = $subdata;
}

$json_data = [
    "draw" => intval($_REQUEST['draw']),
    "recordsTotal" => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data,
    "total" => intval($totalData)
];

echo json_encode($json_data);

?>