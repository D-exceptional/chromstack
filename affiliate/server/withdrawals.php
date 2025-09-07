<?php 

  require '../server/conn.php';
  require '../../assets/server/functions.php'; //For sending email

  $response = array();
 
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $amount = mysqli_real_escape_string($conn, $_POST['amount']);
  $bank = mysqli_real_escape_string($conn, $_POST['bank']);
  $narration = mysqli_real_escape_string($conn, $_POST['narration']);
  $balance = mysqli_real_escape_string($conn, $_POST['balance']);
  $date = date('Y-m-d');
  $status = 'Pending';

  if (!empty($name) && !empty($email) && !empty($amount) && !empty($bank) && !empty($narration) && !empty($balance)) {
    // Check if there is already a pending withdrawal request
    $check = mysqli_query($conn, "SELECT * FROM withdrawals WHERE withdrawal_email = '$email' AND withdrawal_status = 'Pending'");
    if (mysqli_num_rows($check) > 0) {
      // Prepare response if a pending withdrawal already exists
      $response = array('Info' => "A pending withdrawal request has already been placed.");
      $encodedData = json_encode($response, JSON_FORCE_OBJECT);
      echo $encodedData;
      mysqli_close($conn);
      exit();
    } else {
      // Proceed with inserting the withdrawal request
      $insertWithdrawal = mysqli_query($conn, "INSERT INTO withdrawals (withdrawal_email, withdrawal_amount, withdrawal_bank, withdrawal_date, withdrawal_status, withdrawal_narration)  VALUES ('$email', '$amount', '$bank', '$date', '$status', '$narration')");
      if ($insertWithdrawal) {
        // Check for savings record (wallet)
        $sql = mysqli_query($conn, "SELECT * FROM wallet WHERE wallet_email = '$email' AND wallet_user = 'Affiliate'");
        if (mysqli_num_rows($sql) > 0) {
          // Update the existing record if found
          if (mysqli_query($conn, "UPDATE wallet SET wallet_amount = '$balance' WHERE wallet_email = '$email' AND wallet_user = 'Affiliate'") === true) {
            // Prepare response
            $response = array('Info' => "Withdrawal request placed successfully.");
            // Send confirmatory email to affiliate
            $subject = "Successful Withdrawal Request";
            $link = 'https://chromstack.com/login';
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
          } else {
            // Error handling for wallet update failure
            $response = array('Info' => "Failed to update wallet balance. Withdrawal request failed.");
          }
        } else {
          // Insert new record in wallet table if not found
          if (mysqli_query($conn, "INSERT INTO wallet (wallet_email, wallet_amount, wallet_user) VALUES ('$email', '$balance', 'Affiliate')") === true) {
            // Prepare response
            $response = array('Info' => "Withdrawal request placed successfully.");
            // Send confirmatory email to affiliate
            $subject = "Successful Withdrawal Request";
            $link = 'https://chromstack.com/login';
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
          } else {
            // Error handling for wallet insert failure
            $response = array('Info' => "Failed to create wallet record. Withdrawal request failed.");
          }
        }
      } else {
        // Prepare response if insertion into withdrawals table fails
        $response = array('Info' => "Withdrawal request failed. Please try again.");
      }
    }
  } else {
    // Prepare response if any required fields are empty
    $response = array('Info' => "Some fields are empty.");
  }

  // Output the response as JSON
  $encodedData = json_encode($response, JSON_FORCE_OBJECT);
  echo $encodedData;
  mysqli_close($conn);
  exit();

?>
