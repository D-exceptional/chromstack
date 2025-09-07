<?php

  require 'conn.php';

  $data = array();

 $email = mysqli_real_escape_string($conn, $_POST['email']);
 $type = mysqli_real_escape_string($conn, $_POST['type']);

if(!empty($email) && !empty($type)){
   if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
      switch ($type) {
         case 'User':
            //Check users table
            $sql = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
            if(mysqli_num_rows($sql) > 0){
               $data = array('Info' => 'User');
            }
            else {
               $data = array('Info' => 'User not found');
            }
         break;
         case 'Affiliate':
            //Check affiliates table
            $sql = mysqli_query($conn, "SELECT email FROM affiliates WHERE email = '$email'");
            if(mysqli_num_rows($sql) > 0){
               $data = array('Info' => 'Affiliate');
            }
            else {
               $data = array('Info' => 'Affiliate not found');
            }
         break;
         case 'Vendor':
            //Check vendors table
            $sql = mysqli_query($conn, "SELECT email FROM vendors WHERE email = '$email'");
            if(mysqli_num_rows($sql) > 0){
               $data = array('Info' => 'Vendor');
            }
            else {
               $data = array('Info' => 'Vendor not found');
            }
         break;
      }
   } else {
      $data = array('Info' => 'Supplied email is not valid');
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