<?php

require 'conn.php';
require 'functions.php';

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
$reference = mysqli_real_escape_string($conn, $_POST['txn']); // This should be unique per transaction
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
$admin_emails = ['chukwuebukaokeke09@gmail.com', 'izuchukwuokuzu@gmail.com', 'mrwisdom8086@gmail.com'];

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

                //Send payment verification message to affiliate
                $subject = "Successful Application";
                $link = '#';
                $text = 'Congratulations';
                $message = "
                    Congratulations &#128640;&#129392;, $fullname!  <br>
                    We have successfully received your application for affiliate membership on Chromstack. <br>
                    Your payment of <b>$$amount_paid_in_usd</b> is currently under verification and we will respond once everything is verified. <br>
                    Expect to hear from us within the next one hour.  <br>
                    Best wishes from the Chromstack team!
                    <br>
                    <br>
                    <a href='$link' target='_blank'><b>$text</b></a>
                ";
                send_email($subject, $email, $message);

                //Send payment notification message to admin
                $admin_link = 'https://chromstack.com/admin/index';
                $admin_text = 'Visit Dashboard';
                $admin_subject = 'New Affiliate Payment';
                $admin_message = "
                    Hello Admin, <br>
                    We are thrilled to inform you that a new affiliate just made membership payment on Chromstack.  <br>
                    Ensure to verify the payment and take necessary actions. <br>
                    Here are the details of the new affiiate: <br>
                    <center>
                        Fullname: <b>$fullname</b>
                        <br>
                        Email: <b>$email</b>
                        <br>
                        Contact: <b>$formatted_contact</b>
                        <br>
                        Amount paid: <b>$$amount_paid_in_usd</b>
                        <br>
                        Reference: <b>$reference</b>
                        <br>
                        orderID: <b>$tracking_id</b>
                        <br>
                        Date: <b>$full_date</b>
                        <br>
                    </center>
                    <br>
                    <a href='$admin_link' target='_blank'><b>$admin_text</b></a>
                ";
                foreach($admin_emails as $mail){
                    send_email($admin_subject, $mail, $admin_message);
                }

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
