<?php 

require 'conn.php';

$data = array();
$encodedData = '';

$email = mysqli_real_escape_string($conn, $_GET['email']);

$sql = mysqli_query($conn, "SELECT * FROM mailbox WHERE mail_receiver = '$email' OR mail_receiver = 'Admin'");
if(mysqli_num_rows($sql) > 0){

    while($row = mysqli_fetch_assoc($sql)){
    
     $data[] = $row;
    
    }
    
    }else{ $data = array('Info' => 'No mail found'); }

$encodedData = json_encode($data, JSON_FORCE_OBJECT);

echo $encodedData;

mysqli_close($conn);

exit();
    
?>