<?php

 //Set the time zone to AFrica
 date_default_timezone_set("Africa/Lagos");
 
require 'conn.php';

$data = array();

$email = mysqli_real_escape_string($conn, $_POST['email']);
$name = mysqli_real_escape_string($conn, $_POST['name']);
$subject = mysqli_real_escape_string($conn, $_POST['subject']);
$message = mysqli_real_escape_string($conn, $_POST['message']);
$date = date('Y-m-d');
$time = date('H:i');

 if(!empty($email) && !empty($name) && !empty($subject) && !empty($message)){
    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
        //Get all the admin emails and create this notification
        $admin_email_query = mysqli_query($conn, "SELECT email FROM admins");
        while ($row_data = mysqli_fetch_assoc($admin_email_query)) {
            $admin_email = $row_data['email'];
            $insert_query = mysqli_query($conn, "INSERT INTO mailbox (mail_type, mail_subject, mail_sender, mail_receiver, mail_date, mail_time, mail_message, mail_filename, mail_extension) VALUES ('Text', '$subject', '$name', '$admin_email', '$date', '$time', '$message', 'null', 'null')");
            if($insert_query === true){ 
                $data = array('Info' => 'Message sent successfully');
                //Create notification
                $notification_title = 'sent a mail';
                $notification_details = 'An incoming mail was received';
                $notification_type = 'incoming_mail';
                $notification_name = $name;
                $notification_date = date('Y-m-d H:i:s');
                $notification_status = 'Unseen';
                mysqli_query($conn, "INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_name, notification_receiver_email, notification_date, notification_status) VALUES ('$notification_title', '$notification_details', '$notification_type', '$notification_name', '$admin_email', '$notification_date', '$notification_status')");

            }else{ $data = array('Info' => 'Error sending message'); }
        }

    }else{ $data = array('Info' => 'Email is not valid'); }

}else{ $data = array('Info' => 'Fill out all fields before submitting'); }

$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);
exit();

?>