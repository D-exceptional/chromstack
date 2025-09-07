<?php 

require 'conn.php';

$data = array();
$encodedData = '';

$sql = mysqli_query($conn, "SELECT * FROM affiliates WHERE affiliate_status = 'Pending' OR affiliate_status = 'Deactivated'");

if(mysqli_num_rows($sql) > 0){

    while($row = mysqli_fetch_assoc($sql)){
    
     $data[] = $row;
    
    }
    
    }else{ $data = array('Info' => 'No record found'); }

$encodedData = json_encode($data, JSON_FORCE_OBJECT);

echo $encodedData;

mysqli_close($conn);

exit();
    
?>