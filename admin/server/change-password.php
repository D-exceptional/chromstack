<?php 

require 'conn.php';

$data = array();
$encodedData = '';

$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

if(!empty($email) && !empty($password)){

    $sql = mysqli_query($conn, "SELECT email FROM admins WHERE email = '$email'");

    if(mysqli_num_rows($sql) > 0){
        
        //Reset the password
        
        if(mysqli_query($conn, "UPDATE admins SET account_password = '$hashedPassword' WHERE email = '$email'") === true){

            //Redirect securely to dashboard

            $data = array(
                'Info' => 'Password changed successfully',
                'page' => array( 'link' => "http://localhost/chromstack/admin/index.html")
            ); 
                
        }
        else{
                $data = array("Info" => "Error changing password"); 
        }
    }
    else{ 
        $data = array("Info" => "No record found"); 
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