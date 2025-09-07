<?php

 require 'conn.php';

     //Set the time zone to AFrica
     
    date_default_timezone_set("Africa/Lagos");

    $data = array();
    $encodedData = '';

     $id = mysqli_real_escape_string($conn, $_POST['id']);
     $title = mysqli_real_escape_string($conn, $_POST['title']);
     $end_date = mysqli_real_escape_string($conn, $_POST['date']);

    if(!empty($id) && !empty($title) && !empty($end_date)){

      $update_query = mysqli_query($conn, "UPDATE contest SET contest_title = '$title', contest_end_date = '$end_date' WHERE contestID = '$id'");

                if($update_query === true){

                     $data = array("Info" => "Details updated successfully");

                }else{ $data = array('Info' => 'Error updating details'); }      
     }else{
          $data = array('Info' => 'Some fields are empty');
     }

     $encodedData = json_encode($data, JSON_FORCE_OBJECT);

     echo $encodedData;

     mysqli_close($conn);

     exit();

?>