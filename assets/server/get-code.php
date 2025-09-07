<?php

session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../../vendor/autoload.php';

require 'conn.php';

// Function to generate a random 6-digit code
function generateRandomCode() {
    return rand(100000, 999999);
}

$data = array();

$email = mysqli_real_escape_string($conn, $_POST['email']);
$type = mysqli_real_escape_string($conn, $_POST['type']);

//Start processing
if(!empty($email)){ 
    if(filter_var($email, FILTER_VALIDATE_EMAIL)){ 
        switch ($type) {
            case 'First':
            case 'New':
                // Generate a 6-digit code
                $verificationCode = generateRandomCode();
                // Create a new PHPMailer instance
                $mail = new PHPMailer(true);
                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->SMTPDebug  = 0;
                    $mail->Host       = 'premium200.web-hosting.com'; // SMTP server
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'administrator@chromstack.com';    // SMTP username
                    $mail->Password   = '@Chromstack2024';    // SMTP password
                    $mail->SMTPSecure = 'tls';
                    $mail->Port       = 587;
                    $mail->setFrom('administrator@chromstack.com', 'Chromstack');
                    $mail->addAddress($email); // Add a recipient
                    $mail->isHTML(true);
                    $mail->Subject = 'Verification Code';
                    $mail->Body    = 'Your verification code is: ' . '<b>' . $verificationCode . '</b>' . ' and it expires in the next 20 minutes';
                    // Send the email
                    if ($mail->send()) {
                        $data = array( 'Info' => 'Verification code sent successfully');      
                        $_SESSION['otp'] = $verificationCode; //Set session OTP
                        $_SESSION['time'] = $_SERVER['REQUEST_TIME']; //Set session time
                    } 
                    else {
                        $data = array(
                            'Info' => 'Error sending code',
                            'details' => array('error' => 'Error occured while trying to send your code')
                        );
                    }

                } 
                catch (Exception $e) {
                    $data = array(
                        'Info' => 'Mailer Error',
                        'details' => array('error' => 'Verification code could not be sent due to the following error : ' . $mail->ErrorInfo)
                    );
                }
            break;
        }
    }
    else{
        $data = array(
            'Info' => 'Invalid email address',
            'details' => array('error' => 'The supplied email is not valid')
        );
    }
}
else{
    $data = array(
        'Info' => 'Empty field',
        'details' => array('error' => 'Email field is empty')
    );
}

$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);
exit();

?>
