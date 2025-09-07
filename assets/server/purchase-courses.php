<?php

require 'conn.php';
require 'functions.php';
require 'generate-links.php';

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
$referrerID = mysqli_real_escape_string($conn, $_POST['affiliate']);
$amount = mysqli_real_escape_string($conn, $_POST['amount']);
$currency = mysqli_real_escape_string($conn, $_POST['currency']);
$reference = mysqli_real_escape_string($conn, $_POST['reference']); // This should be unique per transaction
$date = date('Y-m-d');
$time = date('H:i');
$full_date = $date . ' ' . $time;
$tracking_id = 'Order-' . time(); // Unique for every purchase
$registration_status = 'Pending';
$purchase_status = 'Pending';
$sales_status = 'Pending';
$full_ref = $referrerID . ',' . $sales_type;
$phoneNumber = '';

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
                    $query = mysqli_query($conn, "SELECT affiliate_percentage FROM uploaded_courses WHERE courseID = '$course_id'");
                    $query_result = mysqli_fetch_assoc($query);
                    $affiliate_commission = substr($query_result['affiliate_percentage'], 0, -1);
                    $earned_commission = $amount * ($affiliate_commission / 100);

                    // Modify data
                    $fullname = ucwords($fullname);
                    $hashPassword = password_hash('user1', PASSWORD_BCRYPT); // Encrypt password
                    $formatted_contact = $dialing_code . substr($contact, 1);

                    // Update variable
                    $phoneNumber = $formatted_contact;

                    //Save to database
                    $insert_query = mysqli_query($conn, "INSERT INTO users (user_profile, fullname, email, contact, country, account_password, created_on, user_status)  VALUES ('null', '$fullname', '$email', '$formatted_contact', '$country', '$hashPassword', '$full_date', '$registration_status')");                            
                    if ($insert_query) {
                        // Insert the records into the tables, then update everything when the payment is verified
                        mysqli_query($conn, "INSERT INTO uploaded_course_sales (sales_email, sales_contact, sales_amount, sales_date, sales_time, sales_status, sales_txref, sales_type, buyer_type, sales_narration, sellerID, courseID, trackingID) VALUES ('$email', '$formatted_contact', '$amount', '$date', '$time', '$sales_status', '$reference', '$sales_type', '$buyer', '$sales_narration', '$referrerID', '$course_id', '$tracking_id')");
                        mysqli_query($conn, "INSERT INTO uploaded_course_sales_backup (sales_email, sales_contact, sales_amount, sales_date, sales_time, sales_status, sales_txref, sales_type, buyer_type, sales_narration, sellerID, courseID, affiliate_commission, trackingID) VALUES ('$email', '$formatted_contact', '$amount', '$date', '$time', '$sales_status', '$reference', '$sales_type', '$buyer', '$sales_narration', '$referrerID', '$course_id', '$earned_commission', '$tracking_id')");
                        mysqli_query($conn, "INSERT INTO purchased_courses (purchase_date, purchase_status, buyer_email, course_amount, courseID, course_type, trackingID)  VALUES ('$date', '$purchase_status', '$email', '$amount', '$course_id', '$course_type', '$tracking_id')");    

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
                $query = mysqli_query($conn, "SELECT affiliate_percentage FROM uploaded_courses WHERE courseID = '$course_id'");
                $query_result = mysqli_fetch_assoc($query);
                $affiliate_commission = substr($query_result['affiliate_percentage'], 0, -1);
                $earned_commission = $amount * ($affiliate_commission / 100);

                // Update variable
                $phoneNumber = $contact;
               
                //Save to database
                $insert_query = mysqli_query($conn, "INSERT INTO uploaded_course_sales (sales_email, sales_contact, sales_amount, sales_date, sales_time, sales_status, sales_txref, sales_type, buyer_type, sales_narration, sellerID, courseID, trackingID) VALUES ('$email', '$phoneNumber', '$amount', '$date', '$time', '$sales_status', '$reference', '$sales_type', '$buyer', '$sales_narration', '$referrerID', '$course_id', '$tracking_id')");                           
                if ($insert_query) {
                    // Insert the records into the tables, then update everything when the payment is verified
                    mysqli_query($conn, "INSERT INTO uploaded_course_sales_backup (sales_email, sales_contact, sales_amount, sales_date, sales_time, sales_status, sales_txref, sales_type, buyer_type, sales_narration, sellerID, courseID, affiliate_commission, trackingID) VALUES ('$email', '$phoneNumber', '$amount', '$date', '$time', '$sales_status', '$reference', '$sales_type', '$buyer', '$sales_narration', '$referrerID', '$course_id', '$earned_commission', '$tracking_id')");
                    mysqli_query($conn, "INSERT INTO purchased_courses (purchase_date, purchase_status, buyer_email, course_amount, courseID, course_type, trackingID)  VALUES ('$date', '$purchase_status', '$email', '$amount', '$course_id', '$course_type', '$tracking_id')");

                    $response = array('Info' => 'Purchase confirmed successfully');

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

//Get order details
$sql = mysqli_query($conn, "SELECT * FROM uploaded_course_sales_backup WHERE sales_txref = '$reference' AND sales_status = 'Pending'"); // Prevent double-processing
if(mysqli_num_rows($sql) > 0){
    $row = mysqli_fetch_assoc($sql);
    $sales_amount = $row['sales_amount'];
    $buyer_type = $row['buyer_type'];
    $sales_type = $row['sales_type'];
    $sellerID = $row['sellerID'];
    $courseID = $row['courseID'];
    $commission = '$' . ($row['affiliate_commission'] / 1000);
    $amount_paid_in_usd = $sales_amount / 1000;

    //Get course details
    $course_query = mysqli_query($conn, "SELECT * FROM uploaded_courses WHERE courseID = '$courseID'");
    $course_query_result = mysqli_fetch_assoc($course_query);
    $course_title = $course_query_result['course_title'];
    $course_type = $course_query_result['course_type'];
    $course_author = $course_query_result['course_authors'];
    $folder_path = $course_query_result['folder_path'];
    $admin_percentage = substr($course_query_result['admin_percentage'], 0, -1);
    $affiliate_percentage = substr($course_query_result['affiliate_percentage'], 0, -1);
    $vendor_percentage = substr($course_query_result['vendor_percentage'], 0, -1);

    //Update required table
    mysqli_query($conn, "UPDATE users SET user_status = 'Active' WHERE email = '$email'");
    mysqli_query($conn, "UPDATE uploaded_course_sales SET sales_status = 'Completed' WHERE sales_txref = '$reference'");
    mysqli_query($conn, "UPDATE uploaded_course_sales_backup SET sales_status = 'Completed' WHERE sales_txref = '$reference'");
    mysqli_query($conn, "UPDATE purchased_courses SET purchase_status = 'Completed' WHERE buyer_email = '$email' AND trackingID = '$tracking_id'");

    //Get commissions
    $admin_commission = $sales_amount * ($admin_percentage / 100);
    $affiliate_commission = $sales_amount * ($affiliate_percentage / 100);
    $vendor_commission = $sales_amount * ($vendor_percentage / 100);

    // Route payments
    $platform_earnings = $admin_commission;
    // Split payments
    $platform_savings = $platform_earnings * 0.50; // Company savings currently set to 50%
    $admin_savings = $platform_earnings * 0.50; // Admin savings currently set to 50%

    // Process Company Savings
    $company_query = mysqli_query($conn, "SELECT * FROM wallet WHERE wallet_email = 'companysavings@gmail.com'");
    if(mysqli_num_rows($company_query) > 0){
        $company_query_result = mysqli_fetch_assoc($company_query);
        $wallet_amount = $company_query_result['wallet_amount'];
        //Update wallet amount
        $new_balance = $wallet_amount + $platform_savings;
        mysqli_query($conn, "UPDATE wallet SET wallet_amount = '$new_balance' WHERE wallet_email = 'companysavings@gmail.com'");
    }

    // Update admin wallet
    $admin_query = mysqli_query($conn, "SELECT * FROM wallet WHERE wallet_user = 'Admin'");
    if(mysqli_num_rows($admin_query) > 0){
        while($admin_result = mysqli_fetch_assoc($admin_query)){
            $admin_email = $admin_result['wallet_email'];
            $wallet_amount = $admin_result['wallet_amount'];
            // Update wallet amount
            $new_balance = $wallet_amount + ($admin_savings / 3);
            mysqli_query($conn, "UPDATE wallet SET wallet_amount = '$new_balance' WHERE wallet_email = '$admin_email' AND wallet_user = 'Admin'");
        }
    }

    //Process seller data
    switch ($sales_type) {
        case 'Admin':
            $data_query = mysqli_query($conn, "SELECT email, fullname FROM admins WHERE adminID = '$sellerID'");
            $query_result = mysqli_fetch_assoc($data_query);
            $seller_email = $query_result['email'];
            $seller_name = $query_result['fullname'];
            $seller_login_link = 'https://chromstack.com/admin/index';
            // Update admin wallet
            $admin_query = mysqli_query($conn, "SELECT * FROM wallet WHERE wallet_user = 'Admin'");
            if(mysqli_num_rows($admin_query) > 0){
                while($admin_result = mysqli_fetch_assoc($admin_query)){
                    $admin_email = $admin_result['wallet_email'];
                    $wallet_amount = $admin_result['wallet_amount'];
                    // Update wallet amount
                    $new_balance = $wallet_amount + ($affiliate_commission / 3);
                    mysqli_query($conn, "UPDATE wallet SET wallet_amount = '$new_balance' WHERE wallet_email = '$admin_email' AND wallet_user = 'Admin'");
                }
            }
        break;
        case 'Affiliate':
            $data_query = mysqli_query($conn, "SELECT email, fullname FROM affiliates WHERE affiliateID = '$sellerID'");
            $query_result = mysqli_fetch_assoc($data_query);
            $seller_email = $query_result['email'];
            $seller_name = $query_result['fullname'];
            $seller_login_link = 'https://chromstack.com/login';
            //Update wallet
            $wallet_query = mysqli_query($conn, "SELECT * FROM wallet WHERE wallet_email = '$seller_email' AND wallet_user = 'Affiliate'");
            if(mysqli_num_rows($wallet_query) > 0){
                $wallet_result = mysqli_fetch_assoc($wallet_query);
                $wallet_amount = $wallet_result['wallet_amount'];
                //Update wallet amount
                $new_balance = $wallet_amount + $affiliate_commission;
                mysqli_query($conn, "UPDATE wallet SET wallet_amount = '$new_balance' WHERE wallet_email = '$seller_email' AND wallet_user = 'Affiliate'");
            }
            else{
                mysqli_query($conn, "INSERT INTO wallet (wallet_email, wallet_amount, wallet_user) VALUES ('$seller_email', '$affiliate_commission', 'Affiliate')");
            }
        break;
    }

    global $seller_email, $seller_name, $seller_login_link;

    //Create notifications
    $notification_title = 'made a sale';
    $notification_details = "Congratulations &#128640;&#129392;, $seller_name! <br> You have successfully made a sale of $$amount_paid_in_usd for the course: <b>$course_title</b>";
    $notification_type = 'course_sale';
    $notification_name = $seller_name;
    $notification_date = date('Y-m-d H:i:s');
    $notification_status = 'Unseen';
    mysqli_query($conn, "INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_name, notification_receiver_email, notification_date, notification_status) VALUES ('$notification_title', '$notification_details', '$notification_type', '$seller_name', '$seller_email', '$notification_date', '$notification_status')");

    //Send email
    $seller_subject = "Successful Course Sale";
    $seller_button_text = 'View Sale';
    $seller_message = "
        Congratulations &#128640;&#129392;, $seller_name!  <br>
        You have successfully made a sale of <b>$$amount_paid_in_usd</b> for the course: <b>$course_title</b>  <br>
        You have gotten <b>$commission</b> commission for this sale.  <br>
        An order ID of <b>$tracking_id</b> has been created to track this sale.  <br>
        Best wishes from the Chromstack team!
        <br>
        <br>
        <a href='$seller_login_link' target='_blank'><b>$seller_button_text</b></a>
    ";
    send_email($seller_subject, $seller_email, $seller_message);

    //Process vendor data
    switch ($course_type) {
        case 'Admin':
            $vendor_email = '';
            $vendor_details = mysqli_query($conn, "SELECT email FROM admins WHERE fullname = '$course_author'");
            $details_info = mysqli_fetch_assoc($vendor_details);
            $vendor_email = $details_info['email'];
            $vendor_login_link = 'https://chromstack.com/admin/index';
            //Update wallet
            $wallet_query = mysqli_query($conn, "SELECT * FROM wallet WHERE wallet_email = '$vendor_email' AND wallet_user = 'Admin'");
            if(mysqli_num_rows($wallet_query) > 0){
                $wallet_result = mysqli_fetch_assoc($wallet_query);
                $wallet_amount = $wallet_result['wallet_amount'];
                //Update wallet amount
                $new_balance = $wallet_amount + $vendor_commission;
                mysqli_query($conn, "UPDATE wallet SET wallet_amount = $new_balance WHERE wallet_email = '$vendor_email' AND wallet_user = 'Admin'");
            }
        break;
        case 'External':
            $vendor_details = mysqli_query($conn, "SELECT email FROM vendors WHERE fullname = '$course_author'");
            $details_info = mysqli_fetch_assoc($vendor_details);
            $vendor_email = $details_info['email'];
            $vendor_login_link = 'https://chromstack.com/login';
            //Update wallet
            $wallet_query = mysqli_query($conn, "SELECT * FROM wallet WHERE wallet_email = '$vendor_email' AND wallet_user = 'Vendor'");
            if(mysqli_num_rows($wallet_query) > 0){
                $wallet_result = mysqli_fetch_assoc($wallet_query);
                $wallet_amount = $wallet_result['wallet_amount'];
                //Update wallet amount
                $new_balance = $wallet_amount + $vendor_commission;
                mysqli_query($conn, "UPDATE wallet SET wallet_amount = $new_balance WHERE wallet_email = '$vendor_email' AND wallet_user = 'Vendor'");
            }
            else{
                mysqli_query($conn, "INSERT INTO wallet (wallet_email, wallet_amount, wallet_user) VALUES ('$vendor_email', $vendor_commission, 'Vendor')");
            }
        break;
    }

    global $vendor_email, $vendor_login_link;

    //Create notification
    $notification_title = "'s course was purchased";
    $notification_details = "Congratulations, $course_author! <br> Your course, <b>$course_title</b>, was successfully ordered and an order ID of <b>$tracking_id</b> has been created to track this order";
    $notification_type = 'course_purchase';
    $notification_name = $course_author;
    $notification_date = date('Y-m-d H:i:s');
    $notification_status = 'Unseen';
    mysqli_query($conn, "INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_name, notification_receiver_email, notification_date, notification_status) VALUES ('$notification_title', '$notification_details', '$notification_type', '$course_author', '$vendor_email', '$notification_date', '$notification_status')");

    //Vendor message template
    $vendor_subject = "Successful Course Order";
    $vendor_button_text = 'View Order';
    $vendor_message = "
        Congratulations &#128640;&#129392;, $course_author! <br>
        Your course, <b>$course_title</b>, was successfully ordered by a buyer with these details:  <br>
        <center>
            <ol style='list-style-type: none;'>
                <li>Fullname: $fullname</li>
                <br>
                <li>Email: $email</li>
                <br>
                <li>Contact: $phoneNumber</li>
                <br>
                <li>Amount Paid: $$amount_paid_in_usd</li>
                <br>
                <li>OrderID: $tracking_id</li>
            </ol>
        </center>
        <br>
        This order was faciliated by an affiliate with these details:  <br>
        <center>
            <ol  style='list-style-type: none;'>
                <li>Fullname: $seller_name</li>
                <br>
                <li>Commision: $commission</li>
                <br>
                <li>OrderID: $tracking_id</li>
            </ol>
        </center>
        <br>
        Best wishes from the Chromstack team!
        <br>
        <br>
        <a href='$vendor_login_link' target='_blank'><b>$vendor_button_text</b></a>
    ";

    //Send email
    send_email($vendor_subject, $vendor_email, $vendor_message);

    //Link to view some authors courses
    $stellar_publishing_guide_course_link = 'Stellar.abrahamkdp.com/register'; //Abraham
    $ghostwriting_income_generator_course_link = 'Gigcourse.com/aax'; //Sampson
    $grant_writing_pro_link = 'https://wa.me/message/QLBOOOZZG47GB1'; //Priscilla
    $affiliate_marketing_for_beginners = 'https://clementudoh.com.ng/amb/'; //Udoh Clement

    // Customer mail details
    $base_path = "../../../courses/";  
    $buyer_button_text = 'View Purchase';
    $buyer_subject = "Successful Course Purchase";
    
    //Send message beased on course type
    if($course_title == "Stellar Publishing Guide"){
        $buyer_couse_link = $stellar_publishing_guide_course_link;
    }
    else if($course_title == "Ghostwriting Income Generator"){
        $buyer_couse_link = $ghostwriting_income_generator_course_link;
    }
    else if($course_title == "Grant Writing Pro"){
        $buyer_couse_link = $grant_writing_pro_link;
    }
    else if($course_title == "AFFILIATE MARKETING FOR BEGINNERS (AMB)"){
        $buyer_couse_link = $affiliate_marketing_for_beginners;
    }
    else {
        $buyer_couse_link = "https://chromstack.com/login";
    }

    //Mail message template
    $buyer_message = "
        Hi,$fullname <br>
        We are glad that your purchase of the course, <b>$course_title</b>, was successful.  <br>
        To view this course, click on the button below
        <br>
        <br>
        <a href='$buyer_couse_link' target='_blank'><b>$buyer_button_text</b></a>
    ";  
    
    //Welcome message template
    $welcome_link = "https://chromstack.com/account-activation.php?email=$email&type=User";
    $welcome_text = 'Activate Account';
    $welcome_subject = 'Successful Registration';
    $welcome_message = "
        Hi,$fullname <br>
        We are glad that your registration and purchase of the course, <b>$course_title</b> was successful.  <br>
        To fully enjoy all that our platform has to offer, 
        kindly activate your account by clicking the button below <br><br>
        <a href='$welcome_link' target='_blank'><b>$welcome_text</b></a>
        <br>
        Then, use these details to login to your dashboard:
        <center>
            Email: <b>$email</b>
            <br>
            Password: <b>user1</b>
        </center>
        <br>
        Once you login, change your password for more security!
        <br>
        <br>
    ";

    //Get buyer details
    switch ($buyer_type) {
        case 'Affiliate':
            $affiliate_name_check = mysqli_query($conn, "SELECT affiliateID, contact, affiliate_status FROM affiliates WHERE email = '$email'");
            if(mysqli_num_rows($affiliate_name_check) > 0){
                $result = mysqli_fetch_assoc($affiliate_name_check);
                $user_id = $result['affiliateID'];
                $user_contact = $result['contact'];
                $user_status = $result['affiliate_status'];
                $user_type = 'Affiliate';
                //Create wishlist
                $fullPath = $base_path . $folder_path;
                createWishlist($fullPath, $course_title, $user_id, $user_type);
                //Send email    
                send_email($buyer_subject, $email, $buyer_message);
            }
        break;
        case 'User':
            $user_name_check = mysqli_query($conn, "SELECT userID, contact, user_status FROM users WHERE email = '$email'");
            if(mysqli_num_rows($user_name_check) > 0){
                $result = mysqli_fetch_assoc($user_name_check);
                $user_id = $result['userID'];
                $user_contact = $result['contact'];
                $user_status = $result['user_status'];
                $user_type = 'User';
                //Create wishlist
                $fullPath = $base_path . $folder_path;
                createWishlist($fullPath, $course_title, $user_id, $user_type);
                //Check status
                if ($user_status == "Pending") {
                    //Create notifications
                    $notification_title = 'registered on the site';
                    $notification_details = 'New user, ' . $fullname . ', registered on the site';
                    $notification_type = 'user_registration';
                    $notification_name = $fullname;
                    $notification_date = date('Y-m-d H:i:s');
                    $notification_status = 'Unseen';

                    $admin_email_query = mysqli_query($conn, "SELECT email FROM admins");
                    while ($row_data = mysqli_fetch_assoc($admin_email_query)) {
                        $admin_email = $row_data['email'];
                        mysqli_query($conn, "INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_name, notification_receiver_email, notification_date, notification_status) VALUES ('$notification_title', '$notification_details', '$notification_type', '$notification_name', '$admin_email', '$notification_date', '$notification_status')");
                    }
                    //Send welcome email
                    send_email($welcome_subject, $email, $welcome_message);
                    //Send email
                    send_email($buyer_subject, $email, $buyer_message);
                } 
                else {    
                    //Send email
                    send_email($buyer_subject, $email, $buyer_message);
                }
            }
        break;
        case 'Vendor':
            $vendor_name_check = mysqli_query($conn, "SELECT vendorID, contact, vendor_status FROM vendors WHERE email = '$email'");
            if(mysqli_num_rows($vendor_name_check) > 0){
                $result = mysqli_fetch_assoc($vendor_name_check);
                $user_id = $result['vendorID'];
                $user_contact = $result['contact'];
                $user_status = $result['vendor_status'];
                $user_type = 'Vendor';
                //Create wishlist
                $fullPath = $base_path . $folder_path;
                createWishlist($fullPath, $course_title, $user_id, $user_type);
                //Send email
                send_email($buyer_subject, $email, $buyer_message);
            }
        break;
    }
    //$response = array('Info' => 'Payment confirmed successfully');
}
else{
    $response = array('Info' => 'Payment record not found');
}

echo json_encode($response);
mysqli_close($conn);
?>
