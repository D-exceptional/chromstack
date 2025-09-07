<?php

 require 'conn.php';

     //Set the time zone to AFrica
     
    date_default_timezone_set("Africa/Lagos");

     $adminID = mysqli_real_escape_string($conn, $_POST['adminID']);
     $name = mysqli_real_escape_string($conn, $_POST['name']);
     $instagram = mysqli_real_escape_string($conn, $_POST['instagram']);
     $twitter = mysqli_real_escape_string($conn, $_POST['twitter']);
     $tiktok = mysqli_real_escape_string($conn, $_POST['tiktok']);
     $facebook = mysqli_real_escape_string($conn, $_POST['facebook']);
     $date = date('Y-m-d H:i:s');

    if(!empty($adminID) && !empty($name) && !empty($instagram) && !empty($twitter)  && !empty($tiktok) && !empty($facebook)){

      $update_query = mysqli_query($conn, "UPDATE admins SET fullname = '$name', instagram_link = '$instagram', tiktok_link = '$tiktok', twitter_link = '$twitter', facebook_link = '$facebook' WHERE adminID = '$adminID'");

                if($update_query === true){

                     $data = array("Info" => "Details updated successfully");

                      mysqli_query($conn, "INSERT INTO notifications (title, action, created) VALUES ('Account details Update', 'An admin account details was updated', '$date')");

                }else{ $data = array('Info' => 'Something went wrong'); }      
     }else{
          $data = array('Info' => 'Some fields are empty');
     }

     $encodedData = json_encode($data, JSON_FORCE_OBJECT);

     echo $encodedData;

     mysqli_close($conn);

     exit();

?>