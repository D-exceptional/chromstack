<?php

  require 'conn.php';

  $data = array();

 $email = mysqli_real_escape_string($conn, $_POST['email']);

if(!empty($email)){
   if(filter_var($email, FILTER_VALIDATE_EMAIL)){
         $sql = mysqli_query($conn, "SELECT mail_address FROM mail_listing WHERE mail_address = '$email'");
         if(mysqli_num_rows($sql) > 0){
            $data = array('Info' => 'Email is available and can receive notifications from us');
         }
         else{
               $query = mysqli_query($conn, "INSERT INTO mail_listing (mail_address) VALUES ('$email')");
               if ($query === true) {
                  $data = array('Info' => 'Email has been added and can now receive notifications from us');
                  //Add to notifications
                  $notification_title = 'subscribed to mailing service';
                  $notification_details = 'An email address, '. '<b>' . $email . '</b>, subscribed to mailing service';
                  $notification_type = 'email_subscription';
                  $notification_name = $email;
                  $notification_date = date('Y-m-d H:i:s');
                  $notification_status = 'Unseen';
                  //Get all the admin emails and create this notification
                  $admin_email_query = mysqli_query($conn, "SELECT email FROM admins");
                  while ($row_data = mysqli_fetch_assoc($admin_email_query)) {
                        $admin_email = $row_data['email'];
                        //Create notification
                        mysqli_query($conn, "INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_name, notification_receiver_email, notification_date, notification_status) VALUES ('$notification_title', '$notification_details', '$notification_type', '$notification_name', '$admin_email', '$notification_date', '$notification_status')");
                  }
               } 
               else {
                  $data = array('Info' => 'Error adding email address');
               }
            }
   }
   else{
      $data = array('Info' => 'The supplied email is not valid');
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