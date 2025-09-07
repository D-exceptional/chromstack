<?php

 //Set the time zone to AFrica
 date_default_timezone_set("Africa/Lagos");
 
require 'conn.php';

$data = array();

$name = mysqli_real_escape_string($conn, $_POST['name']);
$profile = mysqli_real_escape_string($conn, $_POST['profile']);
$comment = mysqli_real_escape_string($conn, $_POST['comment']);
$courseID = mysqli_real_escape_string($conn, $_POST['id']);
$courseType = mysqli_real_escape_string($conn, $_POST['type']);
$time = mysqli_real_escape_string($conn, $_POST['time']);

 if(!empty($name) && !empty($profile) && !empty($comment) && !empty($courseID) && !empty($courseType) && !empty($time)){

     $insert_query = mysqli_query($conn, "INSERT INTO reviews (fullname, profile, review_comment, review_time, courseID, course_type) VALUES ('$name', '$profile', '$comment', '$time', '$courseID', '$courseType')");
    
     if($insert_query === true){ 
    
          $data = array('Info' => 'Review submitted');
    
     }else{ $data = array('Info' => 'Error submitting review'); } 

}else{ $data = array('Info' => 'Some fields are empty'); }

$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);
exit();

?>