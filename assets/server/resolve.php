<?php

require 'conn.php';
require 'functions.php';

function resolveIssue($name, $prospect_email, $prospect_type) {
    // Send account activation link
    $activation_link = "https://chromstack.com/account-activation.php?email=$prospect_email&type=$prospect_type";
    $activation_text = 'Activate Account';
    $activation_subject = 'Successful Registration';
    $activation_message = "
        Hi, $name <br>
        We are glad that your registration was successful.  <br>
        To fully enjoy all that our platform has to offer, 
        activate your account by clicking the button below
        <br>
        <br>
        <a href='$activation_link' target='_blank'><b>$activation_text</b></a>
    ";
    // Send activation email
    send_email($activation_subject, $prospect_email, $activation_message);

    // Send welcome message link
    $welcome_link = 'https://chromstack.com/login';
    $welcome_text = 'Login';
    $welcome_subject = 'Welcome to Chromstack';
    $welcome_message = "
        Hello $name, <br>
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
            Email: <b>$prospect_email</b>
            <br>
            Password: <b>user1</b>
        </center>
        <br>
        Once you login, change your password for more security!
        <br>
        <br>
        <a href='$welcome_link' target='_blank'><b>$welcome_text</b></a>
    ";
    // Send welcome email
    send_email($welcome_subject, $prospect_email, $welcome_message);
}

$response = array();

$prospect_email = mysqli_real_escape_string($conn, $_POST['email']);
$prospect_type = mysqli_real_escape_string($conn, $_POST['type']);

if (!empty($prospect_email) && !empty($prospect_type)) {
    switch ($prospect_type) {
        case 'Affiliate':
            $query = mysqli_query($conn, "SELECT * FROM affiliates WHERE email = '$prospect_email'");
            if (mysqli_num_rows($query) > 0) {
                $result = mysqli_fetch_assoc($query);
                $fullname = $result['fullname'];
                // Resolve
                resolveIssue($fullname, $prospect_email, $prospect_type);
                // Prepare response
                $response = array('Info' => 'Resolution mails sent');
            } else {
                $response = array('Info' => 'No record found');
            }
            break;
        case 'User':
            $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$prospect_email'");
            if (mysqli_num_rows($query) > 0) {
                $result = mysqli_fetch_assoc($query);
                $fullname = $result['fullname'];
                // Resolve
                resolveIssue($fullname, $prospect_email, $prospect_type);
                // Prepare response
                $response = array('Info' => 'Resolution mails sent');
            } else {
                $response = array('Info' => 'No record found');
            }
            break;
    }
} else {
    $response = array('Info' => 'Some fields are empty');
}

$encodedData = json_encode($response, JSON_FORCE_OBJECT);
echo $encodedData;

mysqli_close($conn);
exit();
?>
