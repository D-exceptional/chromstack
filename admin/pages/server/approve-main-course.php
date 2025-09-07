<?php

  require 'conn.php';

  $data = array();

 $courseID = mysqli_real_escape_string($conn, $_POST['courseID']);

 if (!empty($courseID)) {

    $query = mysqli_query($conn, "UPDATE affiliate_program_course SET course_status = 'Approved' WHERE courseID = '$courseID'");

         if ($query === true) {

             $data = array('Info' => 'Course approved successfully');

         }else{  $data = array('Info' => 'Error approving course'); }
            
     }else{  $data = array('Info' => 'Course ID missing'); }
        

     $encodedData = json_encode($data, JSON_FORCE_OBJECT);

     echo $encodedData;

 mysqli_close($conn);

 exit();

?>