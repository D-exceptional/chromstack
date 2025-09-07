<?php

    require 'conn.php';
    require 'functions.php';

    $data = array();

   $affiliateID = mysqli_real_escape_string($conn, $_POST['affiliateID']);
    if (!empty($affiliateID)) {
        $query = mysqli_query($conn, "UPDATE affiliates SET affiliate_status = 'Active' WHERE affiliateID = '$affiliateID'");
        if ($query === true) {
            $data = array('Info' => 'Affiliate status updated successfully');
            //Get details
            $sql = mysqli_query($conn, "SELECT fullname, email FROM affiliates WHERE affiliateID = '$affiliateID'");
            if(mysqli_num_rows($sql) > 0){
                $row = mysqli_fetch_assoc($sql);
                $fullname = $row['fullname'];
                $email = $row['email'];
                //Send email
                $text = 'Login';
                $link = 'https://chromstack.com/login';
                $subject = 'Account Activation';
                $message = "
                                <p style='font-size: 20px;'><b>$subject</b></p>
                                <br>
                                Hi, $fullname <br>
                                We are glad to inform you that your affiliate membership account has been activated successfully.
                                Your plights were carefully reviewed by us and we ensured that they were resolved.
                                Login to your account by clicking on the button below.
                                <br>
                                <br>
                                <a href='$link' target='_blank'><b>$text</b></a>
                            ";
                send_email($subject, $email, $message);
            }
        }
        else{  $data = array('Info' => 'Error updating affiliate status'); }
    }
    else{  $data = array('Info' => 'Affiliate ID missing'); }

    $encodedData = json_encode($data, JSON_FORCE_OBJECT);
    echo $encodedData;
    mysqli_close($conn);
    exit();


?>