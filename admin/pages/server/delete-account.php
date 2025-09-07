<?php

  require 'conn.php';

  $data = array();

  $accountID = mysqli_real_escape_string($conn, $_POST['id']);
  $accountType = mysqli_real_escape_string($conn, $_POST['type']);
  $name = mysqli_real_escape_string($conn, $_POST['name']);

  if (!empty($accountID) && !empty($accountType) && !empty($name)) {
    switch ($accountType) {
      case 'Affiliate':
        $sql = mysqli_query($conn, "SELECT * FROM affiliates WHERE affiliateID = '$accountID'");
        if(mysqli_num_rows($sql) > 0){
          $row = mysqli_fetch_assoc($sql);
          $email = $row['email'];
          //Free account check
          $query = mysqli_query($conn, "SELECT email FROM created_affiliates WHERE email = '$email'");
          if(mysqli_num_rows($query) > 0){
            mysqli_query($conn, "DELETE FROM created_affiliates WHERE email = '$email'");
          }
          //Free signup check
          $check = mysqli_query($conn, "SELECT email FROM free_signups WHERE email = '$email'");
          if(mysqli_num_rows($query) > 0){
            mysqli_query($conn, "DELETE FROM free_signups WHERE email = '$email'");
          }
          //Link check
          $link_check = mysqli_query($conn, "SELECT affiliate_email FROM affiliate_link_clicks WHERE affiliate_email = '$email'");
          if(mysqli_num_rows($query) > 0){
            mysqli_query($conn, "DELETE FROM affiliate_link_clicks WHERE affiliate_email = '$email'");
          }
          //Delete account
          if (mysqli_query($conn, "DELETE FROM affiliates WHERE affiliateID = '$accountID'") === true) {
            $data = array('Info' => 'Account deleted successfully');
          } else {
            $data = array('Info' => 'Error deleting account');
          }
        }
        else{
          $data = array('Info' => "No record found for affiliate : $name");
        }
      break;
      case 'User':
        $sql = mysqli_query($conn, "SELECT * FROM users WHERE userID = '$accountID'");
        if(mysqli_num_rows($sql) > 0){
          //Delete account
          if (mysqli_query($conn, "DELETE FROM users WHERE userID = '$accountID'") === true) {
            $data = array('Info' => 'Account deleted successfully');
          } else {
            $data = array('Info' => 'Error deleting account');
          }
        }
        else{
          $data = array('Info' => "No record found for user : $name");
        }
      break;
      case 'Vendor':
        $sql = mysqli_query($conn, "SELECT * FROM vendors WHERE vendorID = '$accountID'");
        if(mysqli_num_rows($sql) > 0){
          //Delete account
          if (mysqli_query($conn, "DELETE FROM vendors WHERE vendorID = '$accountID'") === true) {
            $data = array('Info' => 'Account deleted successfully');
          } else {
            $data = array('Info' => 'Error deleting account');
          }
        }
        else{
          $data = array('Info' => "No record found for vendor : $name");
        }
      break;
    }
  } 
  else {
    $data = array('Info' => 'Some fields are empty');
  }

  $encodedData = json_encode($data, JSON_FORCE_OBJECT);
  echo $encodedData;
  mysqli_close($conn);
  exit();

?>