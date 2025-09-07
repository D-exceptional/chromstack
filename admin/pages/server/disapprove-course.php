<?php

  require 'conn.php';

  $data = array();
  $encodedData = '';

 $courseID = mysqli_real_escape_string($conn, $_POST['courseID']);

 if (!empty($courseID)) {

    $query = mysqli_query($conn, "UPDATE uploaded_courses SET course_status = 'Pending' WHERE courseID = '$courseID'");

         if ($query === true) {

             $data = array('Info' => 'Course disapproved successfully');

         }else{  $data = array('Info' => 'Error disapproving course'); }
            
     }else{  $data = array('Info' => 'Course ID missing'); }
        

     $encodedData = json_encode($data, JSON_FORCE_OBJECT);

     echo $encodedData;

 mysqli_close($conn);

 exit();

?>