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
    $referrerID = mysqli_real_escape_string($conn, $_POST['affiliate']);
    $course_id = mysqli_real_escape_string($conn, $_POST['id']);
    $course_type = mysqli_real_escape_string($conn, $_POST['type']);
    $sales_type = mysqli_real_escape_string($conn, $_POST['sales']);
    $sales_narration = mysqli_real_escape_string($conn, $_POST['narration']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $currency = mysqli_real_escape_string($conn, $_POST['currency']);
    $reference = mysqli_real_escape_string($conn, $_POST['reference']); // This should be unique per transaction
    $date = date('Y-m-d');
    $time = date('H:i');
    $full_date = $date . ' ' . $time;
    $tracking_id = 'Order-' . time(); // Unique for every purchase
    $registration_status = 'Pending';
    $default_account_number = 0;
    $purchase_status = 'Pending';
    $sales_status = 'Pending';

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
                // Get affiliate percentage
                $query = mysqli_query($conn, "SELECT affiliate_percentage FROM affiliate_program_course WHERE courseID = '$course_id'");
                $query_result = mysqli_fetch_assoc($query);
                $affiliate_commission = substr($query_result['affiliate_percentage'], 0, -1);
                $earned_commission = $amount * ($affiliate_commission / 100);

                // Modify data
                $fullname = ucwords($fullname);
                $formatted_contact = $dialing_code . substr($contact, 1);
                $hashPassword = password_hash('user1', PASSWORD_BCRYPT); // Encrypt password
                $recipient_code = 'null';

                $insert_query = mysqli_query($conn, "INSERT INTO affiliates (affiliate_profile, fullname, email, contact, country, account_password, created_on, affiliate_status, instagram_link, tiktok_link, twitter_link, facebook_link, account_number, bank, bank_code, recipient_code)  VALUES ('null', '$fullname', '$email', '$formatted_contact', '$country', '$hashPassword', '$full_date', '$registration_status', 'null', 'null', 'null', 'null', '$default_account_number', 'null', 'null', '$recipient_code')");                            
                if ($insert_query) {
                    // Insert the records into the tables, then update everything when the payment is verified
                    mysqli_query($conn, "INSERT INTO affiliate_course_sales (sales_email, sales_contact, sales_amount, sales_date, sales_time, sales_status, sales_txref, sales_type, sales_narration, sellerID, courseID, trackingID) VALUES ('$email', '$formatted_contact', '$amount', '$date', '$time', '$sales_status', '$reference', '$sales_type', '$sales_narration', '$referrerID', '$course_id', '$tracking_id')");
                    mysqli_query($conn, "INSERT INTO affiliate_course_sales_backup (sales_email, sales_contact, sales_amount, sales_date, sales_time, sales_status, sales_txref, sales_type, sales_narration, sellerID, courseID, affiliate_commission, trackingID) VALUES ('$email', '$formatted_contact', '$amount', '$date', '$time', '$sales_status', '$reference', '$sales_type', '$sales_narration', '$referrerID', '$course_id', '$earned_commission', '$tracking_id')");
                    mysqli_query($conn, "INSERT INTO purchased_courses (purchase_date, purchase_status, buyer_email, course_amount, courseID, course_type, trackingID)  VALUES ('$date', '$purchase_status', '$email', '$amount', '$course_id', '$course_type', '$tracking_id')");  
                    
                    // Start processing
                    $sql = mysqli_query($conn, "SELECT * FROM affiliate_course_sales WHERE sales_txref = '$reference' AND sales_status = 'Pending'"); // Prevent double-processing
                    if(mysqli_num_rows($sql) > 0){
                        $result = mysqli_fetch_assoc($sql);
                        $sales_type = $result['sales_type'];
                        $sellerID = $result['sellerID'];
                        $courseID = $result['courseID'];
                        $sales_amount = $result['sales_amount'];

                        // Get affiliate percentage
                        $query = mysqli_query($conn, "SELECT affiliate_percentage FROM affiliate_program_course WHERE courseID = '$courseID'");
                        $query_result = mysqli_fetch_assoc($query); 
                        $affiliate_commission = substr($query_result['affiliate_percentage'], 0, -1);
                        $commission = '$' . (($sales_amount * ($affiliate_commission / 100)) / 1000);
                        $amount_paid_in_usd = $sales_amount / 1000;

                        // Get course details
                        $course_query = mysqli_query($conn, "SELECT course_title, folder_path FROM affiliate_program_course WHERE courseID = '$courseID'");
                        $course_query_result = mysqli_fetch_assoc($course_query);
                        $course_title = $course_query_result['course_title'];
                        $folder_path = $course_query_result['folder_path'];
                        
                        //Get details
                        switch ($sales_type) {
                            case 'Admin':
                                $data_query = mysqli_query($conn, "SELECT email, fullname FROM admins WHERE adminID = '$sellerID'");
                                $query_result = mysqli_fetch_assoc($data_query);
                                $seller_email = $query_result['email'];
                                $seller_name = $query_result['fullname'];
                                $seller_link = 'https://chromstack.com/admin/index';
                                
                                // Split payments
                                $admin_quota = $sales_amount;
                                $platform_savings = $admin_quota * 0.40; // Company savings currently set to 40%
                                $admin_savings = $admin_quota * 0.60; // Admin savings currently set to 60%
                                // Process Company Savings
                                $company_savings = $platform_savings * 0.50;
                                $sales_challenge_savings = $platform_savings * 0.50; // If sales challenge is not on this percentage will be 0
                                // Update company savings
                                $company_query = mysqli_query($conn, "SELECT * FROM wallet WHERE wallet_email = 'companysavings@gmail.com'");
                                if(mysqli_num_rows($company_query) > 0){
                                    $company_query_result = mysqli_fetch_assoc($company_query);
                                    $wallet_amount = $company_query_result['wallet_amount'];
                                    $new_balance = $wallet_amount + $company_savings;
                                    mysqli_query($conn, "UPDATE wallet SET wallet_amount = '$new_balance' WHERE wallet_email = 'companysavings@gmail.com'");
                                }
                                // Update sales challenge savings
                                $sales_challenge_query = mysqli_query($conn, "SELECT * FROM wallet WHERE wallet_email = 'saleschallengesavings@gmail.com'");
                                if(mysqli_num_rows($sales_challenge_query) > 0){
                                    $sales_challenge_query_result = mysqli_fetch_assoc($sales_challenge_query);
                                    $wallet_amount = $sales_challenge_query_result['wallet_amount'];
                                    $new_balance = $wallet_amount + $sales_challenge_savings;
                                    mysqli_query($conn, "UPDATE wallet SET wallet_amount = '$new_balance' WHERE wallet_email = 'saleschallengesavings@gmail.com'");
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
                            break;
                            case 'Affiliate':
                                $data_query = mysqli_query($conn, "SELECT email, fullname FROM affiliates WHERE affiliateID = '$sellerID'");
                                $query_result = mysqli_fetch_assoc($data_query);
                                $seller_email = $query_result['email'];
                                $seller_name = $query_result['fullname'];
                                $seller_link = 'https://chromstack.com/login';
                                // Get commissions
                                $admin_quota = $sales_amount * ($affiliate_commission / 100);
                                $affiliate_quota = $sales_amount * ($affiliate_commission / 100);
                                //Update wallet
                                $wallet_query = mysqli_query($conn, "SELECT * FROM wallet WHERE wallet_email = '$seller_email' AND wallet_user = 'Affiliate'");
                                if(mysqli_num_rows($wallet_query) > 0){
                                    $wallet_result = mysqli_fetch_assoc($wallet_query);
                                    $wallet_amount = $wallet_result['wallet_amount'];
                                    $new_balance = $wallet_amount + $affiliate_quota;
                                    mysqli_query($conn, "UPDATE wallet SET wallet_amount = '$new_balance' WHERE wallet_email = '$seller_email' AND wallet_user = 'Affiliate'");
                                }
                                else{
                                    mysqli_query($conn, "INSERT INTO wallet (wallet_email, wallet_amount, wallet_user) VALUES ('$seller_email', '$affiliate_quota', 'Affiliate')");
                                }
                                
                                /*
                                //INTEGRATE DOWNLINE EARNING FEATURE HERE AHEAD OF THE NEXT SALES CHALLENGE
                                // Get commissions
                                $affiliate_quota = $sales_amount * ($affiliate_commission / 100); // 50% of the sale made
                                $admin_quota = $sales_amount * (0.4444); // 44.44% of the sale made
                                $downline_quota = $sales_amount * (0.0556); // 5.56% of the sale made
                                $topup_quota = 0;

                                // Process affiliate earning here
                                $wallet_query = mysqli_query($conn, "SELECT * FROM wallet WHERE wallet_email = '$seller_email' AND wallet_user = 'Affiliate'");
                                if (mysqli_num_rows($wallet_query) > 0) {
                                    $wallet_result = mysqli_fetch_assoc($wallet_query);
                                    $wallet_amount = $wallet_result['wallet_amount'];
                                    $new_balance = $wallet_amount + $affiliate_quota;
                                    mysqli_query($conn, "UPDATE wallet SET wallet_amount = $new_balance WHERE wallet_email = '$seller_email' AND wallet_user = 'Affiliate'");
                                } else {
                                    mysqli_query($conn, "INSERT INTO wallet (wallet_email, wallet_amount, wallet_user) VALUES ('$seller_email', $affiliate_quota, 'Affiliate')");
                                }

                                // Process downline earning here
                                $integration_date = date('Y-m-d', strtotime('2024-12-27')); // Ensure correct format for date
                                $downline_query = mysqli_query($conn, "SELECT sales_date, sellerID FROM affiliate_course_sales_backup WHERE sales_email = '$seller_email'");
                                if (mysqli_num_rows($downline_query) > 0) {
                                    // Correct variable name for fetching row data
                                    $downline_result = mysqli_fetch_assoc($downline_query);
                                    $sales_date = $downline_result['sales_date'];
                                    $downlineID = $downline_result['sellerID'];

                                    // Get downline email
                                    $email_query = mysqli_query($conn, "SELECT email FROM affiliates WHERE affiliateID = '$downlineID'");
                                    $email_result = mysqli_fetch_assoc($email_query);
                                    $downline_email = $email_result['email'];

                                    // Check registration date
                                    if ($sales_date >= $integration_date) {
                                        // Process earning for downline
                                        $wallet_query = mysqli_query($conn, "SELECT * FROM wallet_bonus WHERE wallet_email = '$downline_email' AND wallet_user = 'Affiliate'");
                                        if (mysqli_num_rows($wallet_query) > 0) {
                                            $wallet_result = mysqli_fetch_assoc($wallet_query);
                                            $wallet_amount = $wallet_result['wallet_amount'];
                                            $new_balance = $wallet_amount + $downline_quota;
                                            mysqli_query($conn, "UPDATE wallet_bonus SET wallet_amount = $new_balance WHERE wallet_email = '$downline_email' AND wallet_user = 'Affiliate'");
                                        } else {
                                            mysqli_query($conn, "INSERT INTO wallet_bonus (wallet_email, wallet_amount, wallet_user) VALUES ('$downline_email', $downline_quota, 'Affiliate')");
                                        }
                                    } else {
                                        // Set topup_quota if not eligible
                                        $topup_quota = $downline_quota;
                                    }
                                } else {
                                    // No downline found, set topup_quota
                                    $topup_quota = $downline_quota;
                                }

                                // Process admin earning here
                                $total_admin_quota = $admin_quota + $topup_quota;

                                // Split payments
                                $platform_savings = $total_admin_quota * 0.40; // Company savings currently set to 40%
                                $admin_savings = $total_admin_quota * 0.60; // Admin savings currently set to 60%

                                // Process Company Savings
                                $company_savings = $platform_savings * 0.50;
                                $sales_challenge_savings = $platform_savings * 0.50;

                                // Update company savings
                                $company_query = mysqli_query($conn, "SELECT * FROM wallet WHERE wallet_email = 'companysavings@gmail.com'");
                                if (mysqli_num_rows($company_query) > 0) {
                                    $company_query_result = mysqli_fetch_assoc($company_query);
                                    $wallet_amount = $company_query_result['wallet_amount'];
                                    $new_balance = $wallet_amount + $company_savings;
                                    mysqli_query($conn, "UPDATE wallet SET wallet_amount = '$new_balance' WHERE wallet_email = 'companysavings@gmail.com'");
                                }

                                // Update sales challenge savings
                                $sales_challenge_query = mysqli_query($conn, "SELECT * FROM wallet WHERE wallet_email = 'saleschallengesavings@gmail.com'");
                                if (mysqli_num_rows($sales_challenge_query) > 0) {
                                    $sales_challenge_query_result = mysqli_fetch_assoc($sales_challenge_query);
                                    $wallet_amount = $sales_challenge_query_result['wallet_amount'];
                                    $new_balance = $wallet_amount + $sales_challenge_savings;
                                    mysqli_query($conn, "UPDATE wallet SET wallet_amount = '$new_balance' WHERE wallet_email = 'saleschallengesavings@gmail.com'");
                                }

                                // Update admin wallet
                                $admin_query = mysqli_query($conn, "SELECT * FROM wallet WHERE wallet_user = 'Admin'");
                                if (mysqli_num_rows($admin_query) > 0) {
                                    while ($admin_result = mysqli_fetch_assoc($admin_query)) {
                                        $admin_email = $admin_result['wallet_email'];
                                        $wallet_amount = $admin_result['wallet_amount'];
                                        $new_balance = $wallet_amount + ($admin_savings / 3); // Assuming 3 admins, distribute evenly
                                        mysqli_query($conn, "UPDATE wallet SET wallet_amount = '$new_balance' WHERE wallet_email = '$admin_email' AND wallet_user = 'Admin'");
                                    }
                                }

                                */

                                // Split payments
                                $platform_savings = $admin_quota * 0.40; // Company savings currently set to 40%
                                $admin_savings = $admin_quota * 0.60; // Admin savings currently set to 60%
                                // Process Company Savings
                                $company_savings = $platform_savings * 0.50;
                                $sales_challenge_savings = $platform_savings * 0.50;
                                // Update company savings
                                $company_query = mysqli_query($conn, "SELECT * FROM wallet WHERE wallet_email = 'companysavings@gmail.com'");
                                if(mysqli_num_rows($company_query) > 0){
                                    $company_query_result = mysqli_fetch_assoc($company_query);
                                    $wallet_amount = $company_query_result['wallet_amount'];
                                    $new_balance = $wallet_amount + $company_savings;
                                    mysqli_query($conn, "UPDATE wallet SET wallet_amount = $new_balance WHERE wallet_email = 'companysavings@gmail.com'");
                                }
                                // Update sales challenge savings
                                $sales_challenge_query = mysqli_query($conn, "SELECT * FROM wallet WHERE wallet_email = 'saleschallengesavings@gmail.com'");
                                if(mysqli_num_rows($sales_challenge_query) > 0){
                                    $sales_challenge_query_result = mysqli_fetch_assoc($sales_challenge_query);
                                    $wallet_amount = $sales_challenge_query_result['wallet_amount'];
                                    $new_balance = $wallet_amount + $sales_challenge_savings;
                                    mysqli_query($conn, "UPDATE wallet SET wallet_amount = $new_balance WHERE wallet_email = 'saleschallengesavings@gmail.com'");
                                }
                                // Update admin wallet
                                $admin_query = mysqli_query($conn, "SELECT * FROM wallet WHERE wallet_user = 'Admin'");
                                if(mysqli_num_rows($admin_query) > 0){
                                    while($admin_result = mysqli_fetch_assoc($admin_query)){
                                        $admin_email = $admin_result['wallet_email'];
                                        $wallet_amount = $admin_result['wallet_amount'];
                                        $new_balance = $wallet_amount + ($admin_savings / 3);
                                        mysqli_query($conn, "UPDATE wallet SET wallet_amount = $new_balance WHERE wallet_email = '$admin_email' AND wallet_user = 'Admin'");
                                    }
                                }
                            break;
                        }

                        global $seller_name, $seller_email, $seller_link;

                        //Create notification
                        $notification_title = 'made a sale';
                        $notification_details = "Congratulations &#128640;&#129392;, $seller_name! <br> You successfully made a sale of $$amount_paid_in_usd for the course: <b>$course_title</b>";
                        $notification_type = 'course_sale';
                        $notification_name = $seller_name;
                        $notification_date = date('Y-m-d H:i:s');
                        $notification_status = 'Unseen';
                        mysqli_query($conn, "INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_name, notification_receiver_email, notification_date, notification_status) VALUES ('$notification_title', '$notification_details', '$notification_type', '$seller_name', '$seller_email', '$notification_date', '$notification_status')");

                        //Send mail
                        $subject = 'Successful Course Sale';
                        $text = 'Login';
                        $message = "
                            Congratulations &#128640;&#129392;, $seller_name!  <br>
                            You have successfully made a sale of <b>$$amount_paid_in_usd</b> for the course: <b>$course_title</b>  <br>
                            You have gotten <b>$commission</b> commission for this sale.  <br>
                            An order ID of <b>$tracking_id</b> has been created to track this sale.  <br>
                            Best wishes from the Chromstack team!
                            <br>
                            <br>
                            <a href='$seller_link' target='_blank'><b>$text</b></a>
                        ";
                        send_email($subject, $seller_email, $message);

                        //Create notifications
                        $notification_title = 'registered on the site';
                        $notification_details = 'New affiliate, '. $fullname . ', registered on the site';
                        $notification_type = 'affiliate_registration';
                        $notification_name = $fullname;
                        $notification_date = date('Y-m-d H:i:s');
                        $notification_status = 'Unseen';
                    
                        $admin_email_query = mysqli_query($conn, "SELECT email FROM admins");
                        while ($row_data = mysqli_fetch_assoc($admin_email_query)) {
                            $admin_email = $row_data['email'];
                            mysqli_query($conn, "INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_name, notification_receiver_email, notification_date, notification_status) VALUES ('$notification_title', '$notification_details', '$notification_type', '$notification_name', '$admin_email', '$notification_date', '$notification_status')");
                        }

                        $query = mysqli_query($conn, "SELECT * FROM affiliates WHERE email = '$email'");
                        if(mysqli_num_rows($query) > 0){
                            $result = mysqli_fetch_assoc($query);
                            $user_id = $result['affiliateID'];
                            $user_type = 'Affiliate';

                            //Send account activation link
                            $activation_link = "https://chromstack.com/account-activation.php?email=$email&type=Affiliate";
                            $activation_text = 'Activate Account';
                            $activation_subject = 'Successful Registration';
                            $activation_message = "
                                Hi, $fullname <br>
                                We are glad that your registration and purchase of the course, <b>$course_title</b> was successful.  <br>
                                To fully enjoy all that our platform has to offer, 
                                activate your account by clicking the button below
                                <br>
                                <br>
                                <a href='$activation_link' target='_blank'><b>$activation_text</b></a>
                            ";
                            send_email($activation_subject, $email, $activation_message);
                            
                            //Send welcome message link
                            $welcome_link = 'https://chromstack.com/login';
                            $welcome_text = 'Login';
                            $welcome_subject = 'Welcome to Chromstack';
                            $welcome_message = "
                                Hello $fullname, <br>
                                We are thrilled to inform you that you have successfully registered on Chromstack.  <br>
                                Ensure to follow and engage with us across our social media handles. <br>
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
                            //Send welcome email
                            send_email($welcome_subject, $email, $welcome_message);

                            $base_path = "../../../courses/";                                                                                                                                                                                                                   
                            $fullPath = $base_path . $folder_path;
                            //Create wishlist
                            createWishlist($fullPath, $course_title, $user_id, $user_type);
                            //Generate links
                            generateAffiliateLinks($email);
                            //Generate links for contest if any
                            generateContestLinks($user_id, $user_type);

                            //Update required table
                            mysqli_query($conn, "UPDATE affiliates SET affiliate_status = 'Active' WHERE email = '$email'");
                            mysqli_query($conn, "UPDATE affiliate_course_sales SET sales_status = 'Completed' WHERE sales_txref = '$reference'");
                            mysqli_query($conn, "UPDATE affiliate_course_sales_backup SET sales_status = 'Completed' WHERE sales_txref = '$reference'");
                            mysqli_query($conn, "UPDATE purchased_courses SET purchase_status = 'Completed' WHERE buyer_email = '$email' AND trackingID = '$tracking_id'");
                            mysqli_query($conn, "INSERT INTO wallet (wallet_email, wallet_amount, wallet_user) VALUES ('$email', 0, 'Affiliate')");
                            mysqli_query($conn, "INSERT INTO wallet_bonus (wallet_email, wallet_amount, wallet_user) VALUES ('$email', 0, 'Affiliate')");

                            $response = array('Info' => 'Payment confirmed successfully');

                        }
                        else{
                            $response = array('Info' => 'Affiliate record not found');
                        }
                    }
                    else{
                        $response = array('Info' => 'Payment record not found');
                    }

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
