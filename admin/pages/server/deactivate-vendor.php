<?php

  require 'conn.php';

  $data = array();
  $encodedData = '';

 $vendorID = mysqli_real_escape_string($conn, $_POST['vendorID']);

 if (!empty($vendorID)) {

    $query = mysqli_query($conn, "UPDATE vendors SET vendor_status = 'Deactivated' WHERE vendorID = '$vendorID'");

         if ($query === true) {

             $data = array('Info' => 'Vendor status updated successfully');

         }else{  $data = array('Info' => 'Error updating status'); }
            
     }else{  $data = array('Info' => 'Vendor ID missing'); }
        

     $encodedData = json_encode($data, JSON_FORCE_OBJECT);

     echo $encodedData;

 mysqli_close($conn);

 exit();

?>