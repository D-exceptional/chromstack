<?php 

session_start();

require 'conn.php';

$data = array();

$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);
$type = mysqli_real_escape_string($conn, $_POST['type']);

if(!empty($email) && !empty($password) && !empty($type)){
    switch ($type) {
        case 'User':
            $sql = mysqli_query($conn, "SELECT userID, email, account_password, user_status FROM users WHERE email = '$email'");
            if(mysqli_num_rows($sql) > 0){
                $row = mysqli_fetch_assoc($sql);
                $userID = $row['userID'];
                $databaseEmail = $row['email'];
                $databasePassword = $row['account_password'];
                $user_status = $row['user_status'];
                //Proceed
                if ($user_status === 'Pending' || $user_status === 'Deactivated') {
                    $data = array("Info" => "Access denied!"); 
                }
                else {
                    //Verify the passwords and attempt user login
                    if(password_verify($password, $databasePassword) && $email === $databaseEmail){
                        //Redirect securely to dashboard
                        $data = array(
                                'Info' => 'You have successfully logged in',
                                'user' => array('link' => "https://chromstack.com/eLearning/index.php?access=User&accessID=$userID")
                            ); 
                        //Set session variable for identification
                        $_SESSION['userID'] = $userID; 
                    }
                    else{
                        $data = array("Info" => "Check your email or password again"); 
                    }
                }
            }
            else{ 
                $data = array("Info" => "No record found"); 
            } 
        break;
        case 'Affiliate':
            $sql = mysqli_query($conn, "SELECT affiliateID, email, account_password, affiliate_status FROM affiliates WHERE email = '$email'");
            if(mysqli_num_rows($sql) > 0){
                $row = mysqli_fetch_assoc($sql);
                $affiliateID = $row['affiliateID'];
                $databaseEmail = $row['email'];
                $databasePassword = $row['account_password'];
                $affiliate_status = $row['affiliate_status'];
                //Proceed
                if ($affiliate_status === 'Pending' || $affiliate_status === 'Deactivated') {
                    $data = array("Info" => "Access denied!"); 
                }
                else {
                     //Verify the passwords and attempt user login
                    if(password_verify($password, $databasePassword) && $email === $databaseEmail){
                        //Redirect securely to dashboard
                        $data = array(
                                'Info' => 'You have successfully logged in',
                                'user' => array( 'link' => "https://chromstack.com/affiliate/index.php")
                            ); 
                        //Set session variable for identification
                        $_SESSION['affiliateID'] = $affiliateID; 
                    }
                    else{
                        $data = array("Info" => "Check your email or password again"); 
                    }
                }
            }
            else{ 
                $data = array("Info" => "No record found"); 
            } 
        break;
        case 'Vendor':
            $sql = mysqli_query($conn, "SELECT vendorID, email, account_password, vendor_status FROM vendors WHERE email = '$email'");
            if(mysqli_num_rows($sql) > 0){
                $row = mysqli_fetch_assoc($sql);
                $vendorID = $row['vendorID'];
                $databaseEmail = $row['email'];
                $databasePassword = $row['account_password'];
                $vendor_status = $row['vendor_status'];
                //Proceed
                if ($vendor_status === 'Pending' || $vendor_status === 'Deactivated') {
                    $data = array("Info" => "Access denied!"); 
                }
                else {
                    //Verify the passwords and attempt user login
                    if(password_verify($password, $databasePassword) && $email === $databaseEmail){
                        //Redirect securely to dashboard
                        $data = array(
                                'Info' => 'You have successfully logged in',
                                'user' => array( 'link' => "https://chromstack.com/seller/index.php")
                            ); 
                        //Set session variable for identification
                        $_SESSION['vendorID'] = $vendorID; 
                    }
                    else{
                        $data = array("Info" => "Check your email or password again"); 
                    }
                }
            }
            else{ 
                $data = array("Info" => "No record found"); 
            } 
        break;
    } 
}
else{  
    $data = array("Info" => "Some fields are empty"); 
}

$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);
exit();
    
?>