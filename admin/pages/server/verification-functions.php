<?php

    // Get email sending file
    require 'functions.php';

    function verify_affiliate_course_purchase($reference) {
        global $conn;
        if (isset($reference) && !empty($reference)) {
            //Get details
            $email = '';
            $orderID = '';
            /************** Start Processing **************/
            //Get order details
            $sql = mysqli_query($conn, "SELECT sales_email, trackingID FROM affiliate_course_sales_backup WHERE sales_txref = '$reference'");
            if(mysqli_num_rows($sql) > 0){
                $row = mysqli_fetch_assoc($sql);
                $email = $row['sales_email'];
                $orderID = $row['trackingID'];
            }
            //Update required tables
            mysqli_query($conn, "UPDATE affiliate_course_sales SET sales_status = 'Completed' WHERE sales_txref = '$reference'");
            mysqli_query($conn, "UPDATE affiliate_course_sales_backup SET sales_status = 'Completed' WHERE sales_txref = '$reference'");
            mysqli_query($conn, "UPDATE purchased_courses SET purchase_status = 'Completed' WHERE buyer_email = '$email' AND trackingID = '$orderID'");
            //Close connection
            mysqli_close($conn);                                                                                           
        }
    }

    function verify_course_purchase($reference) {
        global $conn;
        //Start processing
        if (isset($reference) && !empty($reference)) {
            //User details
            $email = '';
            $orderID = '';
            /************** Start Processing **************/
            //Get order details
            $sql = mysqli_query($conn, "SELECT sales_email, trackingID FROM uploaded_course_sales_backup WHERE sales_txref = '$reference'");
            if(mysqli_num_rows($sql) > 0){
                $row = mysqli_fetch_assoc($sql);
                $email = $row['sales_email'];
                $orderID = $row['trackingID'];
            }
            //Update required tables
            mysqli_query($conn, "UPDATE uploaded_course_sales SET sales_status = 'Completed' WHERE sales_txref = '$reference'");
            mysqli_query($conn, "UPDATE uploaded_course_sales_backup SET sales_status = 'Completed' WHERE sales_txref = '$reference'");
            mysqli_query($conn, "UPDATE purchased_courses SET purchase_status = 'Completed' WHERE buyer_email = '$email' AND trackingID = '$orderID'");
            //Close connection to database
            mysqli_close($conn);
        }
    }

    function verify_payment($reference) {
        //Connect to database
        global $conn;
        //Start processing
        if (isset($reference) && !empty($reference)) {
            //Update membership_payment table
            $sql = mysqli_query($conn, "SELECT * FROM membership_payment WHERE payment_ref = '$reference'");
            if(mysqli_num_rows($sql) > 0){
                mysqli_query($conn, "UPDATE membership_payment SET payment_status = 'Completed' WHERE payment_ref = '$reference'");
                mysqli_query($conn, "UPDATE membership_payment_backup SET payment_status = 'Completed' WHERE payment_ref = '$reference'");
            }
        }
    }

    function update_transaction($reference, $status, $name, $amount) {
        //Connect to database
        global $conn;
        //Start processing
        if (!empty($reference) && !empty($status) && !empty($name) && !empty($amount)) {
            switch ($status) {
                case 'success':
                    $sql = mysqli_query($conn, "SELECT * FROM transaction_payments WHERE payment_txref = '$reference' AND payment_status = 'Pending'");
                    if(mysqli_num_rows($sql) > 0){
                        mysqli_query($conn, "UPDATE transaction_payments SET payment_status = 'Completed' WHERE payment_txref = '$reference'");
                        //Selectively send email
                        $email_check = mysqli_query($conn, "SELECT payment_email FROM transaction_payments WHERE payment_txref = '$reference' AND payment_status = 'Completed'");
                        $row = mysqli_fetch_assoc($email_check);
                        $email = $row['payment_email'];
                        $link = '#';
                        $text = 'Enjoy';
                        $subject = "Weekly Payment From Chromstack";
                        $message = "
                                    Congratulations &#128640;&#129392;, $name, <br>
                                    You have received a payment of <b>$$amount</b> from Chromstack. <br>
                                    We ensure to reward everyone on our payroll for their efforts. <br>
                                    Have a lovely weekend.
                                    <br>
                                    <br>
                                    <a href='$link' target='_blank'><b>$text</b></a>
                                ";
                        if ($email === "okekeebuka928@gmail.com") {
                            send_email($subject, "chukwuebukaokeke09@gmail.com", $message);
                        } else {
                            send_email($subject, $email, $message);
                        }
                        //Create notification
                        $notification_title = 'received a payment';
                        $notification_details = "Congratulations, $name! <br> You have received a payment of <b>$$amount</b> from Chromstack <br> Have a lovely weekend";
                        $notification_type = 'weekly_payout';
                        $notification_name = $name;
                        $notification_date = date('Y-m-d H:i:s');
                        $notification_status = 'Unseen';
                        //Create notification
                        mysqli_query($conn, "INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_name, notification_receiver_email, notification_date, notification_status) 
                        VALUES ('$notification_title', '$notification_details', '$notification_type', '$name', '$email', '$notification_date', '$notification_status')");
                    }
                break;
                case 'failed':
                    $sql = mysqli_query($conn, "SELECT * FROM transaction_payments WHERE payment_txref = '$reference' AND payment_status = 'Pending'");
                    if(mysqli_num_rows($sql) > 0){
                        mysqli_query($conn, "UPDATE transaction_payments SET payment_status = 'Failed' WHERE payment_txref = '$reference'");
                    }
                break;
                case 'reversed':
                    $sql = mysqli_query($conn, "SELECT * FROM transaction_payments WHERE payment_txref = '$reference' AND payment_status = 'Pending'");
                    if(mysqli_num_rows($sql) > 0){
                        mysqli_query($conn, "UPDATE transaction_payments SET payment_status = 'Reversed' WHERE payment_txref = '$reference'");
                    }
                break;
            }
        }
   }

?>