<?php

 require 'conn.php';
 
    $vendorID = mysqli_real_escape_string($conn, $_POST['vendorID']);
    $currentPassword = mysqli_real_escape_string($conn, $_POST['current_password']);
    $newPassword = mysqli_real_escape_string($conn, $_POST['new_password']);
    $hashPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    if(!empty($currentPassword) && !empty($newPassword)){

          $check = mysqli_query($conn, "SELECT account_password FROM vendors WHERE vendorID = '$vendorID'");
          $value = mysqli_fetch_assoc($check);
          $dbPassword = $value['account_password'];

          if (password_verify($currentPassword, $dbPassword)) {

               $update_query = mysqli_query($conn, "UPDATE vendors SET account_password = '$hashPassword' WHERE vendorID = '$vendorID'");

               if($update_query === true){

                    $data = array("Info" => "Password updated successfully");

               }else{ $data = array('Info' => 'Something went wrong'); }    
          }
          else {
               $data = array("Info" => "Current password does not match");
          }
     
    }else{ $data = array('Info' => 'Some fields are empty');  }
       
    $encodedData = json_encode($data, JSON_FORCE_OBJECT);

    echo $encodedData;

    mysqli_close($conn);

    exit();

?>