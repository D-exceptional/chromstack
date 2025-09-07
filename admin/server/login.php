<?php 

session_start();

require 'conn.php';

$data = array();

$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

if(!empty($email) && !empty($password)){
    $sql = mysqli_query($conn, "SELECT adminID, email, account_password FROM admins WHERE email = '$email'");
    if(mysqli_num_rows($sql) > 0){
        $row = mysqli_fetch_assoc($sql);
        $adminID = $row['adminID'];
        $databaseEmail = $row['email'];
        $databasePassword = $row['account_password'];
        //Verify the passwords and attempt admin login
        if(password_verify($password, $databasePassword) && $email === $databaseEmail){
            //Redirect securely to dashboard
            $data = array(
                    'Info' => 'You have successfully logged in',
                    'admin' => array( 'link' => "https://chromstack.com/admin/pages/index.php")
                ); 

            // Create sessions so the server knows who the logged in user is
            $_SESSION['adminID'] = $adminID; 
            $_SESSION['userType'] = 'Admin'; 
        }
        else{
            $data = array("Info" => "Invalid credentials. Check your email or password again"); 
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