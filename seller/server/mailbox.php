<?php 

require 'conn.php';

$email = mysqli_real_escape_string($conn, $_GET['email']);

$data = array();

$sql = mysqli_query($conn, "SELECT * FROM mailbox WHERE mail_receiver = '$email'");

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