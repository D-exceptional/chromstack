<?php

     require 'conn.php';

    //Set the time zone to AFrica
    date_default_timezone_set("Africa/Lagos");

    $data = array();

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $date = date('Y-m-d');
    $time = date('H:i');
    $type = 'Multimedia';
     
     if(!empty($email) && !empty($subject) && !empty($message)){

          if(filter_var($email, FILTER_VALIDATE_EMAIL)){ 

               if(isset($_FILES['attachment']) && !empty($_FILES['attachment'])){ 

                    $targetDir = "../../attachments/";
                    $filename = $_FILES['attachment']['name'];
                    $filetype = $_FILES['attachment']['type'];
                    $tmp_name = $_FILES['attachment']['tmp_name'];
                    $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
                    $extensions = ["jpeg", "png", "jpg", "pdf", "mp3", "mp4", "docx"];

                    if(in_array($file_ext, $extensions) === true){ 

                         if(move_uploaded_file($tmp_name, $targetDir.$filename)){

                            $insert_query =  mysqli_query($conn, "INSERT INTO mailbox (mail_type, mail_subject, mail_sender, mail_receiver, mail_date, mail_time, mail_message, mail_filename, mail_extension) VALUES ('$type', '$subject', '$sender', '$email', '$date', '$time', '$message', '$filename', '$file_ext')");

                              if($insert_query === true){ 

                                   $data = array('Info' => 'Message sent successfully');

                                   //Create notification
                                   $notification_title = 'sent a mail';
                                   $notification_details = 'An incoming mail was received';
                                   $notification_type = 'incoming_mail';
                                   $notification_name = $name;
                                   $notification_date = date('Y-m-d H:i:s');
                                   $notification_status = 'Unseen';
                                   mysqli_query($conn, "INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_name, notification_receiver_email, notification_date, notification_status) VALUES ('$notification_title', '$notification_details', '$notification_type', '$notification_name', '$email', '$notification_date', '$notification_status')");
                             
                              }else{ $data = array('Info' => 'Error sending message'); } 

                         }else{ $data = array('Info' => 'Failed to upload attachment'); } 

                    }else{ $data = array('Info' => 'Attachment must have either .jpg, .jpeg, .png, .pdf, .mp3, .mp4 or .docx extension'); } 

               }else{ $data = array('Info' => 'Please, upload a valid attachment'); }

          }else{ $data = array('Info' => 'Supplied email is not valid'); } 

     }else{ $data = array('Info' => 'All fields must be filled up'); } 

     $encodedData = json_encode($data, JSON_FORCE_OBJECT);
     echo $encodedData;
     mysqli_close($conn);

?>
