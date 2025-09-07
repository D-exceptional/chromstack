<?php

require 'conn.php';
//require 'assets/server/conn.php';

$data = array();

$sql = mysqli_query($conn, "SELECT course_title FROM uploaded_courses");
if(mysqli_num_rows($sql) > 0){
   while($row = mysqli_fetch_assoc($sql)){
      $course_title = $row['course_title'];
      $data[] = array('title' => $course_title);
   }
}

$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);
exit();

?>