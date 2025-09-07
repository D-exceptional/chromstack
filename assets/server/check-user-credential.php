<?php 

require 'conn.php';

$response = array();

$email = mysqli_real_escape_string($conn, $_GET['email']);
$type = mysqli_real_escape_string($conn, $_GET['type']);

if(!empty($email) && !empty($type)){
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
       switch ($type) {
            case 'User':
                $sql = mysqli_query($conn, "SELECT email, fullname, contact FROM users WHERE email = '$email'");
                if(mysqli_num_rows($sql) > 0){
                    $result = mysqli_fetch_assoc($sql);
                    $response = array(
                        "Info" => "User exists",
                        "details" => array("fullname" => $result['fullname'], "contact" => $result['contact'] )
                    ); 
                }
                else{ 
                    $response = array("Info" => "User does not exist"); 
                } 
            break;
            case 'Affiliate':
                $sql = mysqli_query($conn, "SELECT email, fullname, contact FROM affiliates WHERE email = '$email'");
                if(mysqli_num_rows($sql) > 0){
                    $result = mysqli_fetch_assoc($sql);
                    $response = array(
                        "Info" => "Affiliate exists",
                        "details" => array("fullname" => $result['fullname'], "contact" => $result['contact'] )
                    ); 
                }
                else{ 
                    $response = array("Info" => "Affiliate does not exist"); 
                } 
            break;
            case 'Vendor':
                $sql = mysqli_query($conn, "SELECT email, fullname, contact FROM vendors WHERE email = '$email'");
                if(mysqli_num_rows($sql) > 0){
                    $result = mysqli_fetch_assoc($sql);
                    $response = array(
                        "Info" => "Vendor exists",
                        "details" => array("fullname" => $result['fullname'], "contact" => $result['contact'] )
                    ); 
                }
                else{ 
                    $response = array("Info" => "Vendor does not exist"); 
                } 
            break;
        } 
    } else {
         $response = array("Info" => "Supplied email is not valid"); 
    }
}
else{  
    $response = array("Info" => "Some fields are empty"); 
}

$encodedData = json_encode($response, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);
exit();
    
?>