<?php

  require 'conn.php';

  $data = array();

 $email = mysqli_real_escape_string($conn, $_POST['email']);

if(!empty($email)){
   $sql = mysqli_query($conn, "SELECT email FROM affiliates WHERE email = '$email'");
   if(mysqli_num_rows($sql) > 0){

      $data = array('Info' => 'Email is registered');
   }
   else{
            $data = array('Info' => "Email is not registered");
    }
}
else{
     $data = array('Info' => 'Email field is empty');
}

$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);
exit();

?>