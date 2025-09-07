<?php 

  require 'conn.php';
  require 'functions.php';

  $response = array();
 
  $name =  mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $amount = mysqli_real_escape_string($conn, $_POST['amount']);
  $bank = mysqli_real_escape_string($conn, $_POST['bank']);
  $narration = mysqli_real_escape_string($conn, $_POST['narration']);
  $balance = mysqli_real_escape_string($conn, $_POST['balance']);
  $date =  date('Y-m-d H:i');
  $status = 'Pending';

  if(!empty($name) && !empty($email) && !empty($amount) && !empty($bank) && !empty($narration) && !empty($balance)){
    //Check for pending withdrawal
    $check = mysqli_query($conn, "SELECT * FROM withdrawals WHERE withdrawal_email = '$email' AND withdrawal_status = 'Pending'");
    if(mysqli_num_rows($check) > 0){
      //Prepare response
      $response = array('Info' => "Withdrawal request have been previously placed");
      $encodedData = json_encode($response, JSON_FORCE_OBJECT);
      echo $encodedData;
      mysqli_close($conn);
      exit(); 
    }
    else{
      //Store details
      if (mysqli_query($conn, "INSERT INTO withdrawals (withdrawal_email, withdrawal_amount, withdrawal_bank, withdrawal_date, withdrawal_status, withdrawal_narration) VALUES ('$email', '$amount', '$bank', '$date', '$status', '$narration')") === true) {
        //Check for savings record
        $sql = mysqli_query($conn, "SELECT * FROM wallet WHERE wallet_email = '$email' AND wallet_user = 'Admin'");
        if(mysqli_num_rows($sql) > 0){
          //Update record
          if(mysqli_query($conn, "UPDATE wallet SET wallet_amount = $balance WHERE wallet_email = '$email' AND wallet_user = 'Admin'") === true){
            //Prepare response
            $response = array('Info' => "Withdrawal request placed successfully"); 
            //Send confirmatory email affiliate
            $subject = "Successful Withdrawal Request";
            $link = 'https://chromstack.com/admin/index';
            $text = 'Track Request';
            $message = "
              Congratulations &#128640;&#129392;, $name!  <br>
              You have successfully placed a withdrawal request for <b>$$amount</b>  <br>
              Your payout of <b>$$amount</b> should be in your bank account soon.  <br>
              Best wishes from the Chromstack team!
              <br>
              <br>
              <a href='$link' target='_blank'><b>$text</b></a>
            ";
            send_email($subject, $email, $message);
          }
          else{
            //Prepare response
            $response = array('Info' => "Failed to update wallet balance. Withdrawal request failed."); 
          }
        }
        else{
          //Insert record
          if(mysqli_query($conn, "INSERT INTO wallet (wallet_email, wallet_amount, wallet_user) VALUES ('$email', $savings, 'Admin')") === true){
            //Prepare response
            $response = array('Info' => "Withdrawal request placed successfully"); 
            //Send confirmatory email affiliate
            $subject = "Successful Withdrawal Request";
            $link = 'https://chromstack.com/admin/index';
            $text = 'Track Request';
            $message = "
              Congratulations &#128640;&#129392;, $name!  <br>
              You have successfully placed a withdrawal request for <b>$$amount</b>  <br>
              Your payout of <b>$$amount</b> should be in your bank account soon.  <br>
              Best wishes from the Chromstack team!
              <br>
              <br>
              <a href='$link' target='_blank'><b>$text</b></a>
            ";
            send_email($subject, $email, $message);
          }
          else{
            //Prepare response
            $response = array('Info' => "Failed to create wallet. Withdrawal request failed."); 
          }
        } 
      } else {
        //Prepare response
        $response = array('Info' => "Withdrawal request failed."); 
      }
    }
  }
  else{
    //Prepare response
    $response = array('Info' => "Some fields are empty."); 
  }

  $encodedData = json_encode($response, JSON_FORCE_OBJECT);
  echo $encodedData;
  mysqli_close($conn);
  exit();

?>  