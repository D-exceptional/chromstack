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
    //Time when this request is received
    $timestamp = $_SERVER['REQUEST_TIME'];
     
    if(!empty($fullname) && !empty($email) && !empty($contact) && !empty($country) && !empty($dialing_code)){ 
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){ 
                //Begin registration process
               $sql = mysqli_query($conn, "SELECT email FROM vendors WHERE email = '$email'");
               if(mysqli_num_rows($sql) > 0){
                    $data = array('Info' => 'You have registered on this site');
                    $encodedData = json_encode($data, JSON_FORCE_OBJECT);
                    echo $encodedData;
                    exit();
               }
               else{
                        $fullname = ucwords($fullname);
                        $newContact = substr($contact, 1);
                        $formattedContact = $dialing_code . $newContact;
                        $status = 'Pending';
                        $date = date('Y-m-d H:i:s');
                        $hashPassword = password_hash('user1', PASSWORD_BCRYPT); //Encrypt password
                        $account = 0;
                        $recipient_code = 'null';
                        $insert_query = mysqli_query($conn, "INSERT INTO vendors (vendor_profile, fullname, email, contact, country, account_password, created_on, vendor_status, instagram_link, tiktok_link, twitter_link, facebook_link, account_number, bank, bank_code, recipient_code) VALUES ('null', '$fullname', '$email', '$formattedContact', '$country', '$hashPassword', '$date', '$status', 'null', 'null', 'null', 'null', '$account', 'null', 'null', '$recipient_code')");
                        if($insert_query === true){ 
                            $data = array(
                            'Info' => 'You have registered successfully',
                            'details' => array(
                                    'email' => $email,
                                    'contact' => $formattedContact,
                                    'fullname' => $fullname
                                )
                            ); 
                            
                            // Create wallet
                            mysqli_query($conn, "INSERT INTO wallet (wallet_email, wallet_amount, wallet_user) VALUES ('$email', '0', 'Vendor')");
                            
                            //Add to notifications
                            $notification_title = 'registered on the site';
                            $notification_details = 'New vendor, '. $fullname . ', registered on the site';
                            $notification_type = 'vendor_registration';
                            $notification_name = $fullname;
                            $notification_date = date('Y-m-d H:i:s');
                            $notification_status = 'Unseen';
                            //Get all the admin emails and create this notification
                            $admin_email_query = mysqli_query($conn, "SELECT email FROM admins");
                            while ($row_data = mysqli_fetch_assoc($admin_email_query)) {
                                $admin_email = $row_data['email'];
                                //Create notification
                                mysqli_query($conn, "INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_name, notification_receiver_email, notification_date, notification_status) VALUES ('$notification_title', '$notification_details', '$notification_type', '$notification_name', '$admin_email', '$notification_date', '$notification_status')");
                            }
                            
                            //Send account activation link
                            $link = "https://chromstack.com/account-activation.php?email=$email&type=Vendor";
                            $text = 'Activate Account';
                            $subject = 'Successful Registration';
                            $message = "
                                Hi $fullname, <br>
                                We are glad that your registration was successful. 
                                To fully enjoy all that our platform has to offer, 
                                activate your account by clicking the button below
                                <br>
                                <br>
                                <a href='$link' target='_blank'><b>$text</b></a>
                            ";
                            //Send email
                            send_email($subject, $email, $message);

                        }else{ $data = array('Info' => 'Error occured while registering you'); } 
                    }

            }else{ $data = array('Info' => 'Supplied email is not valid'); } 

     }else{ $data = array('Info' => 'All fields must be filled up'); } 

    $encodedData = json_encode($data, JSON_FORCE_OBJECT);
    echo $encodedData;
    mysqli_close($conn);

?>
