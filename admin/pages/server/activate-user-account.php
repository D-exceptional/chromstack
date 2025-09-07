<?php

    require 'conn.php';
    require 'functions.php';

    $data = array();

    $userID = mysqli_real_escape_string($conn, $_POST['id']);
    if (!empty($userID)) {
        $query = mysqli_query($conn, "UPDATE users SET user_status = 'Active' WHERE userID = '$userID'");
        if ($query === true) {
            $data = array('Info' => 'User status updated successfully');
            //Get details
            $sql = mysqli_query($conn, "SELECT fullname, email FROM users WHERE userID = '$userID'");
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
                                We are glad to inform you that your user membership account has been activated successfully.
                                Your plights were carefully reviewed by us and we ensured that they were resolved.
                                Login to your account by clicking on the button below.
                                <br>
                                <br>
                                <a href='$link' target='_blank'><b>$text</b></a>
                            ";
                send_email($subject, $email, $message);
            }
        }
        else{  $data = array('Info' => 'Error updating user status'); }
    }
    else{  $data = array('Info' => 'User ID missing'); }

    $encodedData = json_encode($data, JSON_FORCE_OBJECT);
    echo $encodedData;
    mysqli_close($conn);
    exit();

?>