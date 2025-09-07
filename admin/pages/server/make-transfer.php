<?php

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get email sending file
require 'functions.php';

// Set the time zone to Africa/Lagos
date_default_timezone_set("Africa/Lagos");

// Connection parameters
$hostname = "localhost";
$username = "chroayol_root";
$password = "MySQLUser";
$dbname = "chroayol_store";

// Create connection
$conn = mysqli_connect($hostname, $username, $password, $dbname);

// Check connection
if (!$conn) {
    $data = array('Info' => 'Database connection error: ' . mysqli_connect_error());
    echo json_encode($data, JSON_FORCE_OBJECT);
    exit();
}

// Global variables
$date = date('Y-m-d H:i:s');

// Output stream
$data = array();

// Function to generate a random alphanumeric code
function generateRandomCode($length = 20) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return 'trf' . $code;
}

// Get incoming request data from JS
$fullname = mysqli_real_escape_string($conn, $_POST['name']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$account = mysqli_real_escape_string($conn, $_POST['account']);
$bank = mysqli_real_escape_string($conn, $_POST['bank']);
$amount = mysqli_real_escape_string($conn, $_POST['amount']);
$currency = mysqli_real_escape_string($conn, $_POST['currency']);
$recipient = mysqli_real_escape_string($conn, $_POST['recipient']);
$reason = mysqli_real_escape_string($conn, $_POST['reason']);
$reference = generateRandomCode();
$status = 'Completed'; // Change to `Pending` once we get a working gateway
$formatted_amount = $amount / 100;
$payout_amount = $formatted_amount / 1000; //Remove this line once we get a working online payment processor.

if (!empty($fullname) && !empty($email) && !empty($account) && !empty($bank) && !empty($amount) && !empty($currency) && !empty($recipient) && !empty($reason)) {
    // Store in database
    $insert_query = "INSERT INTO transaction_payments (payment_email, payment_amount, payment_account, payment_bank, payment_date, payment_status, payment_txref) VALUES ('$email', '$formatted_amount', '$account', '$bank', '$date', '$status', '$reference')";

    if (!mysqli_query($conn, $insert_query)) {
        $data = array('Info' => 'Database insert error: ' . mysqli_error($conn));
        echo json_encode($data, JSON_FORCE_OBJECT);
        mysqli_close($conn);
        exit();
    }
    
    //Back up
    mysqli_query($conn, "INSERT INTO transaction_payments_backup (payment_email, payment_amount, payment_account, payment_bank, payment_date, payment_status, payment_txref) VALUES ('$email', '$formatted_amount', '$account', '$bank', '$date', '$status', '$reference')");
    
    //Update withdrawal status
    mysqli_query($conn, "UPDATE withdrawals SET withdrawal_status = 'Completed' WHERE withdrawal_email = '$email'");
    
    /*
    Send an email to the beneficiaries. 
    Remove these email code once we get a working online payment processor.
    Email should be sent to beneficiaies after via webhook trigger
    */

    $link = '#';
    $text = 'Enjoy';
    $subject = "Chromstack Payout";
    $message = "
                Congratulations &#128640;&#129392;, $fullname, <br>
                You have received a payment of <b>$$payout_amount</b> from Chromstack. <br>
                We ensure to reward everyone on our payroll for their efforts. <br>
                Have a lovely weekend.
                <br>
                <br>
                <a href='$link' target='_blank'><b>$text</b></a>
            ";
    send_email($subject, $email, $message);
    
    //Create notification
    $notification_title = 'received a payment';
    $notification_details = "Congratulations, $fullname! <br> You have received a payment of <b>$$payout_amount</b> from Chromstack <br> Have a lovely weekend";
    $notification_type = 'weekly_payout';
    $notification_name = $fullname;
    $notification_date = date('Y-m-d H:i:s');
    $notification_status = 'Unseen';
    //Create notification
    mysqli_query($conn, "INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_name, notification_receiver_email, notification_date, notification_status) VALUES ('$notification_title', '$notification_details', '$notification_type', '$fullname', '$email', '$notification_date', '$notification_status')");
    
    //Check for status
    $sql = mysqli_query($conn, "SELECT payment_status FROM transaction_payments WHERE payment_txref = '$reference'");
    if (mysqli_num_rows($sql) > 0) {
        $row = mysqli_fetch_assoc($sql);
        $status = $row['payment_status'];
        switch ($status) {
            case 'Completed':
                $data = array('Info' => 'Transfer was successful');
                break;
            case 'Failed':
                $data = array('Info' => 'Transfer failed');
                break;
            case 'Reversed':
                $data = array('Info' => 'Transfer was reversed');
                break;
            case 'Pending':
                $data = array('Info' => 'Transfer is processing');
                break;
        }
    } else {
        $data = array('Info' => 'Transfer details not found');
    }
    
    /* Initialize process
    $fields = [
        'source' => "balance",
        'amount' => $amount,
        "reference" => $reference,
        'recipient' => $recipient,
        'reason' => $reason
    ];

    // Set variable
    $fields_string = http_build_query($fields);

    // Define the API endpoint
    $url = "https://api.paystack.co/transfer";

    // Initialize cURL session
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer sk_live_dd519ff2272708c948e5e92b4149029ab52328ca",
        "Cache-Control: no-cache"
    ]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL and get the response
    $response = curl_exec($curl);
    $error = curl_error($curl);

    // Close cURL session
    curl_close($curl);

    // Check for errors
    if ($error) {
        $data = array('Info' => 'Transfer API error occurred: ' . $error);
        echo json_encode($data, JSON_FORCE_OBJECT);
        mysqli_close($conn);
        exit();
    } else {
        //Check for status
        $sql = mysqli_query($conn, "SELECT payment_status FROM transaction_payments WHERE payment_txref = '$reference'");
        if (mysqli_num_rows($sql) > 0) {
            $row = mysqli_fetch_assoc($sql);
            $status = $row['payment_status'];
            switch ($status) {
                case 'Completed':
                    $data = array('Info' => 'Transfer was successful');
                    break;
                case 'Failed':
                    $data = array('Info' => 'Transfer failed');
                    break;
                case 'Reversed':
                    $data = array('Info' => 'Transfer was reversed');
                    break;
                case 'Pending':
                    $data = array('Info' => 'Transfer is processing');
                    break;
            }
        } else {
            $data = array('Info' => 'Transfer details not found');
        }
    }*/
} else {
    $data = array('Info' => 'Transfer details not set');
}

// Set the Content-Type header to application/json
header('Content-Type: application/json');

// Encode and output data
echo json_encode($data, JSON_FORCE_OBJECT);

// Close the database connection
mysqli_close($conn);

exit();
?>
