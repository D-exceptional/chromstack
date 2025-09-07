<?php

  require 'conn.php';

  $data = array();
  $encodedData = '';

 $email = mysqli_real_escape_string($conn, $_POST['email']);

if(!empty($email)){

   //Check admins table

   $sql = mysqli_query($conn, "SELECT email FROM admins WHERE email = '$email'");

   if(mysqli_num_rows($sql) > 0){

      $data = array(
         'Info' => 'Email is available',
         'page' => array( 'link' => "https://chromstack.com/admin/recover-password.html?email=$email")
     ); 

   }
   else{
      $data = array('Info' => 'No record found');
   }

}
else{
     $data = array('Info' => 'Email field is empty');
}

$encodedData = json_encode($data, JSON_FORCE_OBJECT);

echo $encodedData;

 mysqli_close($conn);

?>