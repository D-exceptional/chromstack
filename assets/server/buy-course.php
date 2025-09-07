<?php

require 'conn.php';
require 'functions.php';

// Set the time zone to Africa/Lagos
date_default_timezone_set("Africa/Lagos");

$response = array();

// Get parameters
$membership = mysqli_real_escape_string($conn, $_POST['membership']);
$fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$buyer = mysqli_real_escape_string($conn, $_POST['user']);
$contact = mysqli_real_escape_string($conn, $_POST['contact']);
$dialing_code = mysqli_real_escape_string($conn, $_POST['code']);
$country = mysqli_real_escape_string($conn, $_POST['country']);
$course_id = mysqli_real_escape_string($conn, $_POST['id']);
$course_type = mysqli_real_escape_string($conn, $_POST['type']);
$sales_type = mysqli_real_escape_string($conn, $_POST['sales']);
$sales_narration = mysqli_real_escape_string($conn, $_POST['narration']);
$referrerID = mysqli_real_escape_string($conn, $_POST['ref']);
$amount = mysqli_real_escape_string($conn, $_POST['amount']);
$currency = mysqli_real_escape_string($conn, $_POST['currency']);
$reference = mysqli_real_escape_string($conn, $_POST['txn']); // This should be unique per transaction
$date = date('Y-m-d');
$time = date('H:i');
$full_date = $date . ' ' . $time;
$tracking_id = 'Order-' . time(); // Unique for every purchase
$registration_status = 'Pending';
$purchase_status = 'Pending';
$sales_status = 'Pending';
$payment_status = 'Pending';
$full_ref = $referrerID . ',' . $sales_type;
$admin_emails = ['chukwuebukaokeke09@gmail.com', 'izuchukwuokuzu@gmail.com', 'mrwisdom8086@gmail.com'];

//Start processing
switch ($membership) {
    case 'New':
        // Continue processing
        if (!empty($fullname) && !empty($email) && !empty($contact) && !empty($country) && !empty($dialing_code) && !empty($reference)) {
            // Prevent multiple registration with the same email
            $sql = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
            if (mysqli_num_rows($sql) > 0) {
                $response = array('Info' => 'Email has been previously registered');
                echo json_encode($response);
                mysqli_close($conn);
                exit();
            } else {
                // Proceed with registration and payment
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    // Get affiliate percentage
                    $query = mysqli_query($conn, "SELECT course_title, affiliate_percentage FROM uploaded_courses WHERE courseID = '$course_id'");
                    $query_result = mysqli_fetch_assoc($query);
                    $course_title = $query_result['course_title'];
                    $affiliate_commission = substr($query_result['affiliate_percentage'], 0, -1);
                    $earned_commission = $amount * ($affiliate_commission / 100);
                    $amount_paid_in_usd = $amount / 1000;
                    $unique_commission = $earned_commission / 1000;
                    // Modify data
                    $fullname = ucwords($fullname);
                    $formatted_contact = $dialing_code . substr($contact, 1);
                    $hashPassword = password_hash('user1', PASSWORD_BCRYPT); // Encrypt password
                    //Save to database
                    $insert_query = mysqli_query($conn, "INSERT INTO users (user_profile, fullname, email, contact, country, account_password, created_on, user_status)  VALUES ('null', '$fullname', '$email', '$formatted_contact', '$country', '$hashPassword', '$full_date', '$registration_status')");                            
                    if ($insert_query) {
                        // Insert the records into the tables, then update everything when the payment is verified
                        mysqli_query($conn, "INSERT INTO uploaded_course_sales (sales_email, sales_contact, sales_amount, sales_date, sales_time, sales_status, sales_txref, sales_type, buyer_type, sales_narration, sellerID, courseID, trackingID) VALUES ('$email', '$formatted_contact', '$amount', '$date', '$time', '$sales_status', '$reference', '$sales_type', '$buyer', '$sales_narration', '$referrerID', '$course_id', '$tracking_id')");
                        mysqli_query($conn, "INSERT INTO uploaded_course_sales_backup (sales_email, sales_contact, sales_amount, sales_date, sales_time, sales_status, sales_txref, sales_type, buyer_type, sales_narration, sellerID, courseID, affiliate_commission, trackingID) VALUES ('$email', '$formatted_contact', '$amount', '$date', '$time', '$sales_status', '$reference', '$sales_type', '$buyer', '$sales_narration', '$referrerID', '$course_id', '$earned_commission', '$tracking_id')");
                        mysqli_query($conn, "INSERT INTO purchased_courses (purchase_date, purchase_status, buyer_email, course_amount, courseID, course_type, trackingID)  VALUES ('$date', '$purchase_status', '$email', '$amount', '$course_id', '$course_type', '$tracking_id')");    
                        //Send payment verification message to user
                        $subject = "Successful Onboarding";
                        $link = '#';
                        $text = 'Congratulations';
                        $message = "
                            Congratulations &#128640;&#129392;, $fullname!  <br>
                            We have successfully received your onboarding request for user membership on Chromstack. <br>
                            Your payment of <b>$$amount_paid_in_usd</b> for the course: <b>$course_title</b> is currently under verification and we will respond once everything is verified. <br>
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
                        $admin_subject = 'New User Payment';
                        $admin_message = "
                            Hello Admin, <br>
                            We are thrilled to inform you that a new user just made membership payment on Chromstack.  <br>
                            Ensure to verify the payment and take necessary actions. <br>
                            Here are the details of the new user: <br>
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
    break;
    case 'Old':
        // Continue processing
        if (!empty($email) && !empty($reference)) {
            // Proceed with registration and payment
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                // Get affiliate percentage
                $query = mysqli_query($conn, "SELECT course_title, affiliate_percentage FROM uploaded_courses WHERE courseID = '$course_id'");
                $query_result = mysqli_fetch_assoc($query);
                $course_title = $query_result['course_title'];
                $affiliate_commission = substr($query_result['affiliate_percentage'], 0, -1);
                $earned_commission = $amount * ($affiliate_commission / 100);
                $amount_paid_in_usd = $amount / 1000;
                $unique_commission = $earned_commission / 1000;
                //Get details
                switch ($buyer) {
                    case 'Affiliate':
                        $check = mysqli_query($conn, "SELECT fullname, contact FROM affiliates WHERE email = '$email'");
                        $check_result = mysqli_fetch_assoc($check);
                        $user_name = $check_result['fullname'];
                        $user_contact = $check_result['contact'];
                    break;
                    case 'User':
                        $check = mysqli_query($conn, "SELECT fullname, contact FROM users WHERE email = '$email'");
                        $check_result = mysqli_fetch_assoc($check);
                        $user_name = $check_result['fullname'];
                        $user_contact = $check_result['contact'];
                    break;
                    case 'Vendor':
                        $check = mysqli_query($conn, "SELECT fullname, contact FROM vendors WHERE email = '$email'");
                        $check_result = mysqli_fetch_assoc($check);
                        $user_name = $check_result['fullname'];
                        $user_contact = $check_result['contact'];
                    break;
                }
               //Global variables
                global $user_name;
                global $user_contact;
                //Save to database
                $insert_query = mysqli_query($conn, "INSERT INTO uploaded_course_sales (sales_email, sales_contact, sales_amount, sales_date, sales_time, sales_status, sales_txref, sales_type, buyer_type, sales_narration, sellerID, courseID, trackingID) VALUES ('$email', '$user_contact', '$amount', '$date', '$time', '$sales_status', '$reference', '$sales_type', '$buyer', '$sales_narration', '$referrerID', '$course_id', '$tracking_id')");                           
                if ($insert_query) {
                    // Insert the records into the tables, then update everything when the payment is verified
                    mysqli_query($conn, "INSERT INTO uploaded_course_sales_backup (sales_email, sales_contact, sales_amount, sales_date, sales_time, sales_status, sales_txref, sales_type, buyer_type, sales_narration, sellerID, courseID, affiliate_commission, trackingID) VALUES ('$email', '$user_contact', '$amount', '$date', '$time', '$sales_status', '$reference', '$sales_type', '$buyer', '$sales_narration', '$referrerID', '$course_id', '$earned_commission', '$tracking_id')");
                    mysqli_query($conn, "INSERT INTO purchased_courses (purchase_date, purchase_status, buyer_email, course_amount, courseID, course_type, trackingID)  VALUES ('$date', '$purchase_status', '$email', '$amount', '$course_id', '$course_type', '$tracking_id')");    
                    //Send payment verification message to affiliate
                    $subject = "Successful Purchase Request";
                    $link = '#';
                    $text = 'Congratulations';
                    $message = "
                        Congratulations &#128640;&#129392;, $user_name!  <br>
                        Your payment of <b>$$amount_paid_in_usd</b> for the course: <b>$course_title</b> is currently under verification and we will respond once everything is verified. <br>
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
                    $admin_subject = 'New Course Purchase';
                    $admin_message = "
                        Hello Admin, <br>
                        We are thrilled to inform you that a new user just made payment for a course on Chromstack.  <br>
                        Ensure to verify the payment and take necessary actions. <br>
                        Here are the details of the new affiiate: <br>
                        <center>
                            Fullname: <b>$user_name</b>
                            <br>
                            Email: <b>$email</b>
                            <br>
                            Contact: <b>$user_contact</b>
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

                    $response = array('Info' => 'Purchase request sent successfully');

                } else {
                    $response = array('Info' => 'Error occurred during request sending');
                }

            } else {
                $response = array('Info' => 'Supplied email is not valid');
            }

        } else {
            $response = array('Info' => 'Some fields are empty');
        }
    break;
}

echo json_encode($response);
mysqli_close($conn);
?>
