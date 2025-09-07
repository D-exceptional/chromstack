<?php

 require 'conn.php';

     //Set the time zone to AFrica
     
    date_default_timezone_set("Africa/Lagos");

    $data = array();
    $encodedData = '';

     $id = mysqli_real_escape_string($conn, $_POST['id']);
     $description = mysqli_real_escape_string($conn, $_POST['description']);
     $date = date('Y-m-d H:i:s');
    if(!empty($id) && !empty($description)){

          $update_query = mysqli_query($conn, "UPDATE uploaded_courses SET course_description = '$description' WHERE courseID = '$id'");

          if($update_query === true){

               $sql = mysqli_query($conn, "SELECT course_description FROM uploaded_courses WHERE courseID = '$id'");

               $row = mysqli_fetch_assoc($sql);

               $course_description = substr($row['course_description'], 0, 100) . '...';

               $data = array(
                    "Info" => "Description updated successfully",
                    "details" => array( "description" => $course_description )
               );

               mysqli_query($conn, "INSERT INTO notifications (title, action, created) VALUES ('Uploaded course description Update', 'An uploadaed course description was updated', '$date')");

          }else{ $data = array('Info' => 'Error updating course description'); }      
     }
     else{
          $data = array('Info' => 'Some fields are empty');
     }

     $encodedData = json_encode($data, JSON_FORCE_OBJECT);

     echo $encodedData;

     mysqli_close($conn);

     exit();

?>