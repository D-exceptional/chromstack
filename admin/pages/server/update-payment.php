<?php

    require 'conn.php';
    require 'functions.php';
    require 'generate-links.php';

    $response = array();

    $payment_name = mysqli_real_escape_string($conn, $_POST['fullname']);
    $payment_email = mysqli_real_escape_string($conn, $_POST['email']);
    $payment_type = mysqli_real_escape_string($conn, $_POST['type']);
    $payment_track = mysqli_real_escape_string($conn, $_POST['track']);
    $payment_reference = mysqli_real_escape_string($conn, $_POST['reference']);

    if (!empty($payment_name) && !empty($payment_email) && !empty($payment_type) && !empty($payment_track) && !empty($payment_reference)) {
        switch ($payment_type) {
            case 'Affiliate Membership':
                $sql = mysqli_query($conn, "SELECT * FROM affiliate_course_sales WHERE sales_txref = '$payment_reference' AND sales_status = 'Pending'"); // Prevent double-processing
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
                            /*
                            // Split payments
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
                            }*/
                            
                            //INTEGRATE DOWNLINE EARNING FEATURE HERE AHEAD OF THE NEXT SALES CHALLENGE
                            $affiliate_quota = $sales_amount * ($affiliate_commission / 100); 
                            $admin_quota = ($sales_amount * ($affiliate_commission / 100)) - 1000; 
                            $downline_quota = 1000; 
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
                                    $wallet_query = mysqli_query($conn, "SELECT * FROM wallet_downline WHERE wallet_email = '$downline_email' AND wallet_user = 'Affiliate'");
                                    if (mysqli_num_rows($wallet_query) > 0) {
                                        $wallet_result = mysqli_fetch_assoc($wallet_query);
                                        $wallet_amount = $wallet_result['wallet_amount'];
                                        $new_balance = $wallet_amount + $downline_quota;
                                        mysqli_query($conn, "UPDATE wallet_downline SET wallet_amount = $new_balance WHERE wallet_email = '$downline_email' AND wallet_user = 'Affiliate'");
                                    } else {
                                        mysqli_query($conn, "INSERT INTO wallet_downline (wallet_email, wallet_amount, wallet_user) VALUES ('$downline_email', $downline_quota, 'Affiliate')");
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
                        An order ID of <b>$payment_track</b> has been created to track this sale.  <br>
                        Best wishes from the Chromstack team!
                        <br>
                        <br>
                        <a href='$seller_link' target='_blank'><b>$text</b></a>
                    ";
                    send_email($subject, $seller_email, $message);

                    //Create notifications
                    $notification_title = 'registered on the site';
                    $notification_details = 'New affiliate, '. $payment_name . ', registered on the site';
                    $notification_type = 'affiliate_registration';
                    $notification_name = $payment_name;
                    $notification_date = date('Y-m-d H:i:s');
                    $notification_status = 'Unseen';
                   
                    $admin_email_query = mysqli_query($conn, "SELECT email FROM admins");
                    while ($row_data = mysqli_fetch_assoc($admin_email_query)) {
                        $admin_email = $row_data['email'];
                        mysqli_query($conn, "INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_name, notification_receiver_email, notification_date, notification_status) VALUES ('$notification_title', '$notification_details', '$notification_type', '$notification_name', '$admin_email', '$notification_date', '$notification_status')");
                    }

                    $query = mysqli_query($conn, "SELECT * FROM affiliates WHERE email = '$payment_email'");
                    if(mysqli_num_rows($query) > 0){
                        $result = mysqli_fetch_assoc($query);
                        $user_id = $result['affiliateID'];
                        $user_type = 'Affiliate';

                        //Send account activation link
                        $activation_link = "https://chromstack.com/account-activation.php?email=$payment_email&type=Affiliate";
                        $activation_text = 'Activate Account';
                        $activation_subject = 'Successful Registration';
                        $activation_message = "
                            Hi, $payment_name <br>
                            We are glad that your registration and purchase of the course, <b>$course_title</b> was successful.  <br>
                            To fully enjoy all that our platform has to offer, 
                            activate your account by clicking the button below
                            <br>
                            <br>
                            <a href='$activation_link' target='_blank'><b>$activation_text</b></a>
                        ";
                        send_email($activation_subject, $payment_email, $activation_message);
                        
                        //Send welcome message link
                        $welcome_link = 'https://chromstack.com/login';
                        $welcome_text = 'Login';
                        $welcome_subject = 'Welcome to Chromstack';
                        $welcome_message = "
                            Hello $payment_name, <br>
                            We are thrilled to inform you that you have successfully registered on Chromstack.  <br>
                            Ensure to follow and engage with us across our social media handles via: <br>
                            <center>
                                <b>X: </b> <a href='https://x.com/chromstack?s=21'>Follow Chromstack On X</a>
                                <br>
                                <b>WhatsApp: </b> <a href='https://chat.whatsapp.com/LjgB5DhGbh9KCrHgvNtQ5z'>Join Chromstack WhatsApp Group</a>
                                <br>
                                <b>Telegram: </b> <a href='https://t.me/+gc9Fr20Y70A0NTdk'>Join Chromstack Telegram Group</a>
                            </center>
                            <br>
                            Login to your dashboard using these details: <br>
                            <center>
                                Email: <b>$payment_email</b>
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
                        send_email($welcome_subject, $payment_email, $welcome_message);

                        $base_path = "../../../courses/";                                                                                                                                                                                                                   
                        $fullPath = $base_path . $folder_path;
                        //Create wishlist
                        createWishlist($fullPath, $course_title, $user_id, $user_type);
                        //Generate links
                        generateAffiliateLinks($payment_email);
                        //Generate links for contest if any
                        generateContestLinks($user_id, $user_type);

                        //Update required table
                        mysqli_query($conn, "UPDATE affiliates SET affiliate_status = 'Active' WHERE email = '$payment_email'");
                        mysqli_query($conn, "UPDATE affiliate_course_sales SET sales_status = 'Completed' WHERE sales_txref = '$payment_reference'");
                        mysqli_query($conn, "UPDATE affiliate_course_sales_backup SET sales_status = 'Completed' WHERE sales_txref = '$payment_reference'");
                        mysqli_query($conn, "UPDATE purchased_courses SET purchase_status = 'Completed' WHERE buyer_email = '$payment_email' AND trackingID = '$payment_track'");
                        mysqli_query($conn, "INSERT INTO wallet (wallet_email, wallet_amount, wallet_user) VALUES ('$payment_email', 0, 'Affiliate')");
                        mysqli_query($conn, "INSERT INTO wallet_downline (wallet_email, wallet_amount, wallet_user) VALUES ('$payment_email', 0, 'Affiliate')");

                        $response = array('Info' => 'Payment updated successfully');

                    }
                    else{
                        $response = array('Info' => 'Affiliate record not found');
                    }
                }
                else{
                    $response = array('Info' => 'Payment record not found or has been processed');
                }
            break;
            case 'Regular Membership':
                //Get the amount that corresponds to this reference
                $sql = mysqli_query($conn, "SELECT * FROM membership_payment WHERE payment_ref = '$payment_reference' AND payment_status = 'Pending'"); // Prevent double-processing
                if(mysqli_num_rows($sql) > 0){
                    $row = mysqli_fetch_assoc($sql);
                    $paid_amount = $row['paid_amount'];
                    $amount_paid_in_usd = '$' . ($row['paid_amount'] / 1000);
                    //Update records
                    mysqli_query($conn, "UPDATE membership_payment SET payment_status = 'Completed' WHERE payment_ref = '$payment_reference'");
                    mysqli_query($conn, "UPDATE membership_payment_backup SET payment_status = 'Completed' WHERE payment_ref = '$payment_reference'");
                    mysqli_query($conn, "INSERT INTO wallet (wallet_email, wallet_amount, wallet_user) VALUES ('$payment_email', 0, 'Affiliate')");
                    mysqli_query($conn, "INSERT INTO wallet_downline (wallet_email, wallet_amount, wallet_user) VALUES ('$payment_email', 0, 'Affiliate')");
                    // Route payments
                    $admin_quota = $paid_amount;
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
                    $link = "https://chromstack.com/account-activation.php?email=$payment_email&type=Affiliate";
                    $text = 'Activate Account';
                    $subject = 'Successful Registration';
                    $message = "
                        Hi $payment_name, <br>
                        We are glad that your registration was successful.  <br>
                        To fully enjoy all that our platform has to offer, 
                        activate your account by clicking the button below
                        <br>
                        <br>
                        <a href='$link' target='_blank'><b>$text</b></a>
                    ";
                    //Send email
                    send_email($subject, $payment_email, $message);

                    //Welcome mail details
                    $welcome_link = 'https://chromstack.com/login';
                    $welcome_text = 'Login';
                    $welcome_subject = 'Welcome to Chromstack';
                    $welcome_message = "
                        Hello $payment_name, <br>
                        We are thrilled to inform you that you have successfully registered on Chromstack. <br>
                        Ensure to follow and engage with us across our social media handles via: <br>
                        <center>
                            <b>X: </b> <a href='https://x.com/chromstack?s=21'>Follow Chromstack On X</a>
                            <br>
                            <b>WhatsApp: </b> <a href='https://chat.whatsapp.com/LjgB5DhGbh9KCrHgvNtQ5z'>Join Chromstack WhatsApp Group</a>
                            <br>
                            <b>Telegram: </b> <a href='https://t.me/+gc9Fr20Y70A0NTdk'>Join Chromstack Telegram Group</a>
                        </center>
                        <br>
                        Login to your dashboard using these details: <br>
                        <center>
                            Email: <b>$payment_email</b>
                            <br>
                            Password: <b>user1</b>
                        </center>
                        <br>
                        Once you login, change your password for more security!
                        <br>
                        <br>
                        <a href='$welcome_link' target='_blank'><b>$welcome_text</b></a>
                    ";
                    send_email($welcome_subject, $payment_email, $welcome_message);

                    //Add to notifications
                    $notification_title = 'registered on the site';
                    $notification_details = 'New affiliate, '. $payment_name . ', registered on the site';
                    $notification_type = 'affiliate_registration';
                    $notification_name = $payment_name;
                    $notification_date = date('Y-m-d H:i:s');
                    $notification_status = 'Unseen';

                    //Create notification
                    $admin_email_query = mysqli_query($conn, "SELECT email FROM admins");
                    while ($row_data = mysqli_fetch_assoc($admin_email_query)) {
                       $admin_email = $row_data['email'];
                       mysqli_query($conn, "INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_name, notification_receiver_email, notification_date, notification_status) VALUES ('$notification_title', '$notification_details', '$notification_type', '$notification_name', '$admin_email', '$notification_date', '$notification_status')");
                    }

                    //Generate links for contest if any
                    $details_query = mysqli_query($conn, "SELECT affiliateID FROM affiliates WHERE email = '$payment_email'");
                    $details_result = mysqli_fetch_assoc($details_query);
                    $user_id = $details_result['affiliateID'];
                    $user_type = 'Affiliate';
                    //Generate links for new affiliate
                    generateAffiliateLinks($payment_email);
                    //Generate links for contest if any
                    generateContestLinks($user_id, $user_type);

                    $response = array('Info' => 'Payment updated successfully');
                }
                else{
                    $response = array('Info' => 'Payment record not found');
                }
            break;
            case 'Course Purchase':
                 //Get order details
                $sql = mysqli_query($conn, "SELECT * FROM uploaded_course_sales_backup WHERE sales_txref = '$payment_reference' AND sales_status = 'Pending'"); // Prevent double-processing
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
                    mysqli_query($conn, "UPDATE users SET user_status = 'Active' WHERE email = '$payment_email'");
                    mysqli_query($conn, "UPDATE uploaded_course_sales SET sales_status = 'Completed' WHERE sales_txref = '$payment_reference'");
                    mysqli_query($conn, "UPDATE uploaded_course_sales_backup SET sales_status = 'Completed' WHERE sales_txref = '$payment_reference'");
                    mysqli_query($conn, "UPDATE purchased_courses SET purchase_status = 'Completed' WHERE buyer_email = '$payment_email' AND trackingID = '$payment_track'");

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
                        An order ID of <b>$payment_track</b> has been created to track this sale.  <br>
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
                    $notification_details = "Congratulations, $course_author! <br> Your course, <b>$course_title</b>, was successfully ordered and an order ID of <b>$payment_track</b> has been created to track this order";
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
                                <li>Fullname: $payment_name</li>
                                <br>
                                <li>Email: $payment_email</li>
                                <br>
                                <li>Contact: $user_contact</li>
                                <br>
                                <li>Amount Paid: $$amount_paid_in_usd</li>
                                <br>
                                <li>OrderID: $payment_track</li>
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
                                <li>OrderID: $payment_track</li>
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
                    $base_path = "../../../courses/";  
                    $buyer_button_text = 'View Purchase';
                    $buyer_subject = "Successful Course Purchase";
                    $stellar_publishing_guide_course_link = 'Stellar.abrahamkdp.com/register'; //Abraham
                    $ghostwriting_income_generator_course_link = 'Gigcourse.com/aax'; //Sampson
                    $grant_writing_pro_link = 'https://wa.me/message/QLBOOOZZG47GB1'; //Priscilla
                    $affiliate_marketing_for_beginners = 'https://clementudoh.com.ng/amb/'; //Udoh Clement
                    
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
                        Hi, $payment_name <br>
                        We are glad that your purchase of the course, <b>$course_title</b>, was successful.  <br>
                        To view this course, click on the button below
                        <br>
                        <br>
                        <a href='$buyer_couse_link' target='_blank'><b>$buyer_button_text</b></a>
                    ";  
                    
                    //Welcome message template
                    $welcome_link = "https://chromstack.com/account-activation.php?email=$payment_email&type=User";
                    $welcome_text = 'Activate Account';
                    $welcome_subject = 'Successful Registration';
                    $welcome_message = "
                        Hi, $payment_name <br>
                        We are glad that your registration and purchase of the course, <b>$course_title</b> was successful.  <br>
                        To fully enjoy all that our platform has to offer, 
                        kindly activate your account by clicking the button below <br><br>
                        <a href='$welcome_link' target='_blank'><b>$welcome_text</b></a>
                        <br>
                        Then, use these details to login to your dashboard:
                        <center>
                            Email: <b>$payment_email</b>
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
                            $affiliate_name_check = mysqli_query($conn, "SELECT affiliateID, contact, affiliate_status FROM affiliates WHERE email = '$payment_email'");
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
                                send_email($buyer_subject, $payment_email, $buyer_message);
                            }
                        break;
                        case 'User':
                            $user_name_check = mysqli_query($conn, "SELECT userID, contact, user_status FROM users WHERE email = '$payment_email'");
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
                                    $notification_details = 'New user, '. $payment_name . ', registered on the site';
                                    $notification_type = 'user_registration';
                                    $notification_name = $payment_name;
                                    $notification_date = date('Y-m-d H:i:s');
                                    $notification_status = 'Unseen';

                                    $admin_email_query = mysqli_query($conn, "SELECT email FROM admins");
                                    while ($row_data = mysqli_fetch_assoc($admin_email_query)) {
                                        $admin_email = $row_data['email'];
                                        mysqli_query($conn, "INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_name, notification_receiver_email, notification_date, notification_status) VALUES ('$notification_title', '$notification_details', '$notification_type', '$notification_name', '$admin_email', '$notification_date', '$notification_status')");
                                    }
                                    //Send welcome email
                                    send_email($welcome_subject, $payment_email, $welcome_message);
                                    //Send email
                                    send_email($buyer_subject, $payment_email, $buyer_message);
                                } 
                                else {    
                                    //Send email
                                    send_email($buyer_subject, $payment_email, $buyer_message);
                                }
                            }
                        break;
                        case 'Vendor':
                            $vendor_name_check = mysqli_query($conn, "SELECT vendorID, contact, vendor_status FROM vendors WHERE email = '$payment_email'");
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
                                send_email($buyer_subject, $payment_email, $buyer_message);
                            }
                        break;
                    }
                    $response = array('Info' => 'Payment updated successfully');
                }
                else{
                    $response = array('Info' => 'Payment record not found');
                }
            break;
        }
    } 
    else {
       $response = array('Info' => 'Some fields are empty');
    }

    $encodedData = json_encode($response, JSON_FORCE_OBJECT);
    echo $encodedData;
    mysqli_close($conn);
    exit();

?>