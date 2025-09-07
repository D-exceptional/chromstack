<?php

    require 'conn.php';
    require 'functions.php';

    //Set the time zone to AFrica
    date_default_timezone_set("Africa/Lagos");

    $data = array();

    $fullname = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $dialing_code = mysqli_real_escape_string($conn, $_POST['code']);
    $creator = mysqli_real_escape_string($conn, $_POST['creator']);
     
     if(!empty($fullname) && !empty($email) && !empty($contact)){ 
          if(filter_var($email, FILTER_VALIDATE_EMAIL)){ 
               $sql = mysqli_query($conn, "SELECT email FROM vendors WHERE email = '$email'");
               if(mysqli_num_rows($sql) > 0){
                    $data = array('Info' => 'Email has been registered on the site!');
                    $encodedData = json_encode($data, JSON_FORCE_OBJECT);
                    echo $encodedData;
                    exit();
               }
               else{
                    $fullname = ucwords($fullname);
                    $newContact = substr($contact, 1);
                    $formattedContact = $dialing_code . $newContact;
                    $status = 'Active';
                    $account = 0;
                    $recipient_code = 'null';
                    $date = date('Y-m-d H:i:s');
                    $hashPassword = password_hash('user1', PASSWORD_BCRYPT);
                    //Save details
                    $insert_query = mysqli_query($conn, "INSERT INTO vendors (vendor_profile, fullname, email, contact, country, account_password, created_on, vendor_status, instagram_link, tiktok_link, twitter_link, facebook_link, account_number, bank, bank_code, recipient_code) VALUES ('null', '$fullname', '$email', '$formattedContact', '$country', '$hashPassword', '$date', '$status', 'null', 'null', 'null', 'null', '$account', 'null', 'null', '$recipient_code')");
                    if($insert_query === true){
                         //Prepare response
                         $data = array('Info' => 'New vendor created successfully');
                         //Add to notifications
                         $notification_title = 'created a vendor member';
                         $notification_details = 'New vendor member, ' . $fullname. ',   was created';
                         $notification_type = 'member_creation';
                         $notification_name = $creator;
                         $notification_date = date('Y-m-d H:i:s');
                         $notification_status = 'Unseen';
                         //Get all the admin emails and create this notification
                         $admin_email_query = mysqli_query($conn, "SELECT email FROM admins");
                         while ($row_data = mysqli_fetch_assoc($admin_email_query)) {
                              $admin_email = $row_data['email'];
                              //Create notification
                              mysqli_query($conn, "INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_name, notification_receiver_email, notification_date, notification_status) 
                              VALUES ('$notification_title', '$notification_details', '$notification_type', '$notification_name', '$admin_email', '$notification_date', '$notification_status')");
                         }

                         //Send welcome message link
                         $text = 'Login';
                         $link = 'https://chromstack.com/login';
                         $subject = 'Welcome Message';
                         $message = "
                                        Hello $fullname, <br>
                                        We are thrilled to inform you that you have been successfully registered on Chromstack. <br>
                                        Kindly login to your account and access your dashboard to get started. <br>
                                        Ensure to follow and engage with us across our social media handles. <br>
                                        Login to your account using these details: <br>
                                        <center>
                                            Email: <b>$email</b>
                                            Password: <b>user1</b>
                                        </center>
                                        <br>
                                        Once you login, change your password for more security!
                                        <br>
                                        <br>
                                        <br>
                                        <a href='$link' target='_blank'><b>$text</b></a>
                                    ";
                        //Send email
                        send_email($subject, $email, $message);
                        
                    }else{ $data = array('Info' => 'Error creating vendor'); } 

               }

          }else{ $data = array('Info' => 'Supplied email is not valid'); } 

     }else{ $data = array('Info' => 'Some fields are empty'); } 

     $encodedData = json_encode($data, JSON_FORCE_OBJECT);
     echo $encodedData;
     mysqli_close($conn);
     exit();

?>
