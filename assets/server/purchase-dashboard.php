<?php

require 'conn.php';
require 'functions.php';
require 'generate-links.php';

// Set the time zone to Africa/Lagos
date_default_timezone_set("Africa/Lagos");

$response = array();

// Get parameters
$fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$contact = mysqli_real_escape_string($conn, $_POST['contact']);
$country = mysqli_real_escape_string($conn, $_POST['country']);
$dialing_code = mysqli_real_escape_string($conn, $_POST['code']);
$amount = mysqli_real_escape_string($conn, $_POST['amount']);
$reference = mysqli_real_escape_string($conn, $_POST['reference']); // This should be unique per transaction
$date = date('Y-m-d');
$time = date('H:i');
$full_date = $date . ' ' . $time;
$registration_status = 'Pending';
$default_account_number = 0;
$payment_status = 'Pending';
$payment_type = 'Affiliate';
$tracking_id = 'payment_' . uniqid();
$referrerID = 1;
$ref_type = 'Admin';
$full_ref = $referrerID . ',' . $ref_type;
$amount_paid_in_usd = $amount / 1000;

// Continue processing
if (!empty($fullname) && !empty($email) && !empty($contact) && !empty($country) && !empty($dialing_code) && !empty($reference)) {
    // Prevent multiple registration with the same email
    $sql = mysqli_query($conn, "SELECT email FROM affiliates WHERE email = '$email'");
    if (mysqli_num_rows($sql) > 0) {
        $response = array('Info' => 'Email has been previously registered');
        echo json_encode($response);
        mysqli_close($conn);
        exit();
    } else {
        // Proceed with registration and payment
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Modify data
            $fullname = ucwords($fullname);
            $formatted_contact = $dialing_code . substr($contact, 1);
            $hashPassword = password_hash('user1', PASSWORD_BCRYPT); // Encrypt password
            $recipient_code = 'null';

            //Save details
            $insert_query = mysqli_query($conn, "INSERT INTO affiliates (affiliate_profile, fullname, email, contact, country, account_password, created_on, affiliate_status, instagram_link, tiktok_link, twitter_link, facebook_link, account_number, bank, bank_code, recipient_code)  VALUES ('null', '$fullname', '$email', '$formatted_contact', '$country', '$hashPassword', '$full_date', '$registration_status', 'null', 'null', 'null', 'null', '$default_account_number', 'null', 'null', '$recipient_code')");                            
            if ($insert_query) {
                // Insert the records into the tables, then update everything when the payment is verified
                mysqli_query($conn, "INSERT INTO membership_payment (payment_email, payment_type, paid_amount, payment_date, payment_status, payment_ref) VALUES ('$email', '$payment_type', '$amount', '$full_date', '$payment_status', '$reference')");
                mysqli_query($conn, "INSERT INTO membership_payment_backup (payment_email, payment_type, paid_amount, payment_date, payment_status, payment_ref) VALUES ('$email', '$payment_type', '$amount', '$full_date', '$payment_status', '$reference')");

                //Update records
                mysqli_query($conn, "UPDATE membership_payment SET payment_status = 'Completed' WHERE payment_ref = '$reference'");
                mysqli_query($conn, "UPDATE membership_payment_backup SET payment_status = 'Completed' WHERE payment_ref = '$reference'");
                mysqli_query($conn, "INSERT INTO wallet (wallet_email, wallet_amount, wallet_user) VALUES ('$email', 0, 'Affiliate')");
                mysqli_query($conn, "INSERT INTO wallet_bonus (wallet_email, wallet_amount, wallet_user) VALUES ('$email', 0, 'Affiliate')");

                // Route payments
                $admin_quota = $amount;
                // Split payments
                $platform_savings = $admin_quota * 0.50; // Company savings currently set to 50%
                $admin_savings = $admin_quota * 0.50; // Admin savings currently set to 50%
                // Process Company Savings
                $company_query = mysqli_query($conn, "SELECT * FROM wallet WHERE wallet_email = 'companysavings@gmail.com'");
                if(mysqli_num_rows($company_query) > 0){
                    $company_query_result = mysqli_fetch_assoc($company_query);
                    $wallet_amount = $company_query_result['wallet_amount'];
                    $new_balance = $wallet_amount + $platform_savings;
                    mysqli_query($conn, "UPDATE wallet SET wallet_amount = '$new_balance' WHERE wallet_email = 'companysavings@gmail.com'");
                }
                // Update admin wallet
                $admin_query = mysqli_query($conn, "SELECT * FROM wallet WHERE wallet_user = 'Admin'");
                if(mysqli_num_rows($admin_query) > 0){
                    while($admin_result = mysqli_fetch_assoc($admin_query)){
                    $admin_email = $admin_result['wallet_email'];
                    $wallet_amount = $admin_result['wallet_amount'];
                    $new_balance = $wallet_amount + ($admin_savings / 3);
                    mysqli_query($conn, "UPDATE wallet SET wallet_amount = '$new_balance' WHERE wallet_email = '$admin_email' AND wallet_user = 'Admin'");
                    }
                }

                //Send account activation link
                $link = "https://chromstack.com/account-activation.php?email=$email&type=Affiliate";
                $text = 'Activate Account';
                $subject = 'Successful Registration';
                $message = "
                    Hi $fullname, <br>
                    We are glad that your registration was successful.  <br>
                    To fully enjoy all that our platform has to offer, 
                    activate your account by clicking the button below
                    <br>
                    <br>
                    <a href='$link' target='_blank'><b>$text</b></a>
                ";
                //Send email
                send_email($subject, $email, $message);

                //Welcome mail details
                $welcome_link = 'https://chromstack.com/login';
                $welcome_text = 'Login';
                $welcome_subject = 'Welcome to Chromstack';
                $welcome_message = "
                    Hello $fullname, <br>
                    We are thrilled to inform you that you have successfully registered on Chromstack.
                    Kindly login to your dashboard to get started.
                    Ensure to follow and engage with us across our social media handles.
                    Login to your dashboard using these details: <br>
                    <center>
                        Email: <b>$email</b>
                        <br>
                        Password: <b>user1</b>
                    </center>
                    <br>
                    Once you login, change your password for more security!
                    <br>
                    <br>
                    <a href='$welcome_link' target='_blank'><b>$welcome_text</b></a>
                ";
                send_email($welcome_subject, $email, $welcome_message);

                //Add to notifications
                $notification_title = 'registered on the site';
                $notification_details = 'New affiliate, '. $fullname . ', registered on the site';
                $notification_type = 'affiliate_registration';
                $notification_name = $fullname;
                $notification_date = date('Y-m-d H:i:s');
                $notification_status = 'Unseen';

                //Create notification
                $admin_email_query = mysqli_query($conn, "SELECT email FROM admins");
                while ($row_data = mysqli_fetch_assoc($admin_email_query)) {
                    $admin_email = $row_data['email'];
                    mysqli_query($conn, "INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_name, notification_receiver_email, notification_date, notification_status) VALUES ('$notification_title', '$notification_details', '$notification_type', '$notification_name', '$admin_email', '$notification_date', '$notification_status')");
                }

                //Generate links for contest if any
                $details_query = mysqli_query($conn, "SELECT affiliateID FROM affiliates WHERE email = '$email'");
                $details_result = mysqli_fetch_assoc($details_query);
                $user_id = $details_result['affiliateID'];
                $user_type = 'Affiliate';
                //Generate links for new affiliate
                generateAffiliateLinks($email);
                //Generate links for contest if any
                generateContestLinks($user_id, $user_type);

                $response = array('Info' => 'You have registered successfully');

            } else {
                $response = array('Info' => 'Error occurred during registration');
            }

        } else {
            $response = array('Info' => 'Supplied email is not valid');
        }
    }

} else {
    $response = array('Info' => 'Some fields are empty');
}

echo json_encode($response);
mysqli_close($conn);
?>
