<?php 

    session_start();
    
    //Retrieve details
    $session_time = $_SESSION['time'];
    $session_otp = $_SESSION['otp'];

    require 'conn.php';
    
    $data = array();
    
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $otp = mysqli_real_escape_string($conn, $_POST['otp']);
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    //Time when this request is received
    $timestamp = $_SERVER['REQUEST_TIME'];

    if(!empty($email) && !empty($password)){
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) { 
             //Check if OTP has expired
            if (($timestamp - $session_time) > 1200) {
                $data = array('Info' => 'OTP has expired. Get a new one');
                $encodedData = json_encode($data, JSON_FORCE_OBJECT);
                echo $encodedData;
                exit();
            } else {
                if (intval($otp) !== ($session_otp)) {
                    $data = array('Info' => 'Incorrect OTP code');
                    $encodedData = json_encode($data, JSON_FORCE_OBJECT);
                    echo $encodedData;
                    exit();
                }
                else{
                    unset($_SESSION['otp']);
                    unset($_SESSION['time']);
                }
            }
            //Continue processing
            switch ($type) {
                case 'User':
                    $sql = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
                    if(mysqli_num_rows($sql) > 0){
                        //Reset the password
                        if(mysqli_query($conn, "UPDATE users SET account_password = '$hashedPassword' WHERE email = '$email'") === true){
                            //Redirect securely to dashboard
                            $data = array('Info' => 'Password changed successfully'); 
                        }
                        else{
                            $data = array("Info" => "Error changing password"); 
                        }
                    }
                    else{ 
                        $data = array("Info" => "No record found"); 
                    } 
                break;
                case 'Affiliate':
                    $sql = mysqli_query($conn, "SELECT email FROM affiliates WHERE email = '$email'");
                    if(mysqli_num_rows($sql) > 0){
                        //Reset the password
                        if(mysqli_query($conn, "UPDATE affiliates SET account_password = '$hashedPassword' WHERE email = '$email'") === true){
                            //Redirect securely to dashboard
                            $data = array('Info' => 'Password changed successfully');    
                        }
                        else{
                            $data = array("Info" => "Error changing password"); 
                        }
                    }
                    else{ 
                        $data = array("Info" => "No record found"); 
                    } 
                break;
                case 'Vendor':
                    $sql = mysqli_query($conn, "SELECT email FROM vendors WHERE email = '$email'");
                    if(mysqli_num_rows($sql) > 0){
                        //Reset the password
                        if(mysqli_query($conn, "UPDATE vendors SET account_password = '$hashedPassword' WHERE email = '$email'") === true){
                            //Redirect securely to dashboard
                            $data = array('Info' => 'Password changed successfully');     
                        }
                        else{
                            $data = array("Info" => "Error changing password"); 
                        }
                    }
                    else{ 
                        $data = array("Info" => "No record found"); 
                    } 
                break;
            } 
        } else {
            $data = array("Info" => "Supplied email is invalid"); 
        }
    }
    else{  
        $data = array("Info" => "All inputs must be filled out"); 
    }
    
    $encodedData = json_encode($data, JSON_FORCE_OBJECT);
    echo $encodedData;
    mysqli_close($conn);
    exit();
    
?>