<?php

 require 'conn.php';
 
 mysqli_set_charset($conn, 'utf8');

     //Set the time zone to AFrica
     
    date_default_timezone_set("Africa/Lagos");

    $data = array();

     $id = mysqli_real_escape_string($conn, $_GET['id']);

    if(!empty($id)){

          $sql = mysqli_query($conn, "SELECT course_description FROM affiliate_program_course WHERE courseID = '$id'");

          $row = mysqli_fetch_assoc($sql);

          $course_description = $row['course_description'];

          $data = array(
               "Info" => "Description fetched successfully",
               "details" => array( "description" => $course_description)
          );

     }
     else{
          $data = array('Info' => 'Some fields are empty');
     }

     $encodedData = json_encode($data, JSON_FORCE_OBJECT);
     echo $encodedData;
     mysqli_close($conn);

?>