<?php

 require 'conn.php';

     //Set the time zone to AFrica
     
    date_default_timezone_set("Africa/Lagos");

    $data = array();
    $encodedData = '';

     $id = mysqli_real_escape_string($conn, $_POST['id']);
     $title = mysqli_real_escape_string($conn, $_POST['title']);
     $amount = mysqli_real_escape_string($conn, $_POST['amount']);
     $admin_commission = mysqli_real_escape_string($conn, $_POST['admin']);
     $affiliate_commission = mysqli_real_escape_string($conn, $_POST['affiliate']);
     $vendor_commission = mysqli_real_escape_string($conn, $_POST['vendor']);
     $date = date('Y-m-d H:i:s');

    if(!empty($id) && !empty($title) && !empty($amount)  && !empty($admin_commission) && !empty($affiliate_commission)  && !empty($vendor_commission)){

      $update_query = mysqli_query($conn, "UPDATE uploaded_courses SET course_title = '$title', course_amount = '$amount', admin_percentage = '$admin_commission', affiliate_percentage = '$affiliate_commission', vendor_percentage = '$vendor_commission' WHERE courseID = '$id'");

                if($update_query === true){

                     $data = array("Info" => "Details updated successfully");

                      mysqli_query($conn, "INSERT INTO notifications (title, action, created) VALUES ('Uploaded course details Update', 'The uploaded course, $title, was updated', '$date')");

                }else{ $data = array('Info' => 'Error updating course details'); }      
     }else{
          $data = array('Info' => 'Some fields are empty');
     }

     $encodedData = json_encode($data, JSON_FORCE_OBJECT);

     echo $encodedData;

     mysqli_close($conn);

     exit();

?>