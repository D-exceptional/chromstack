<?php

 require 'conn.php';

     //Set the time zone to AFrica
     
    date_default_timezone_set("Africa/Lagos");

    $data = array();
    $encodedData = '';

     $id = mysqli_real_escape_string($conn, $_POST['id']);
     $description = mysqli_real_escape_string($conn, $_POST['description']);

    if(!empty($id) && !empty($description)){

      $update_query = mysqli_query($conn, "UPDATE contest SET contest_description = '$description' WHERE contestID = '$id'");

        if($update_query === true){

            $sql = mysqli_query($conn, "SELECT contest_description FROM contest WHERE contestID = '$id'");

                $row = mysqli_fetch_assoc($sql);

                $contest_description = substr($row['contest_description'], 0, 100) . '...';

                $data = array(
                    "Info" => "Description updated successfully",
                    "details" => array( "description" => $contest_description )
                );

        }else{ $data = array('Info' => 'Error updating description'); }    

     }else{
          $data = array('Info' => 'Some fields are empty');
     }

     $encodedData = json_encode($data, JSON_FORCE_OBJECT);

     echo $encodedData;

     mysqli_close($conn);

     exit();

?>