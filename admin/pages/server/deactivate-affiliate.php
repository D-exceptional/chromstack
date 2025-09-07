<?php

  require 'conn.php';

  $data = array();
  $encodedData = '';

 $affiliateID = mysqli_real_escape_string($conn, $_POST['affiliateID']);

 if (!empty($affiliateID)) {

    $query = mysqli_query($conn, "UPDATE affiliates SET affiliate_status = 'Deactivated' WHERE affiliateID = '$affiliateID'");

         if ($query === true) {

             $data = array('Info' => 'Affiliate status updated successfully');

         }else{  $data = array('Info' => 'Error updating status'); }
            
     }else{  $data = array('Info' => 'Affiliate ID missing'); }
        

     $encodedData = json_encode($data, JSON_FORCE_OBJECT);

     echo $encodedData;

 mysqli_close($conn);

 exit();

?>