<?php

   require 'conn.php';
 
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    if(!empty($email)){
          $check = mysqli_query($conn, "SELECT * FROM general_notifications WHERE notification_receiver_email = '$email' AND notification_status = 'Unseen'");
          if (mysqli_num_rows($check) > 0) {
               $update_query = mysqli_query($conn, "UPDATE general_notifications SET notification_status = 'Seen' WHERE notification_receiver_email = '$email'");
               if($update_query === true){
                    $data = array("Info" => "Status updated successfully");
               }
               else{ $data = array('Info' => 'Error updating status'); }    
          } 
          else {
               $data = array("Info" => "No notifications available");
          }
    }
    else{ $data = array('Info' => 'No email supplied');  }
       
    $encodedData = json_encode($data, JSON_FORCE_OBJECT);
    echo $encodedData;
    mysqli_close($conn);
    exit();

?>