<?php

  require 'conn.php';

  $data = array();

  $vendorID = mysqli_real_escape_string($conn, $_POST['id']);
  $account_number = mysqli_real_escape_string($conn, $_POST['account']);
  $bank = mysqli_real_escape_string($conn, $_POST['bank']);
  $bank_code = mysqli_real_escape_string($conn, $_POST['code']);
  $currency = mysqli_real_escape_string($conn, $_POST['currency']);
  $fullname = mysqli_real_escape_string($conn, $_POST['name']); // Verified account name
  $recipient_code = 'Not available';

  if(!empty($account_number) && !empty($bank) && !empty($bank_code) && !empty($currency) && !empty($fullname)){
    //Start creation process
    /*$url = "https://api.paystack.co/transferrecipient";
    $secret_key = 'sk_live_dd519ff2272708c948e5e92b4149029ab52328ca';
    $fields = [
      "type" => "nuban",
      "name" => $fullname,
      "account_number" => $account_number,
      "bank_code" => $bank_code,
      "currency" => $currency
    ];
    $fields_string = http_build_query($fields);
    //open connection
    $curl = curl_init();
    //set the url, number of POST vars, POST data
    curl_setopt($curl,CURLOPT_URL, $url);
    curl_setopt($curl,CURLOPT_POST, true);
    curl_setopt($curl,CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      "Authorization: Bearer $secret_key",
      "Cache-Control: no-cache",
    ));
    //So that curl_exec returns the contents of the cURL; rather than echoing it
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, true); 
    //execute post
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        $data = array("Info" => "Error connecting to Paystack gateway");   
    }
    else {
      $result = json_decode($response, true);
      $status = $result['status'];
      $user_data = $result['data'];
      $recipient_code = $user_data['recipient_code'];

      if ($status === true) {
        $update_query = mysqli_query($conn, "UPDATE vendors SET account_number = '$account_number', bank = '$bank', bank_code = '$bank_code', recipient_code = '$recipient_code' WHERE vendorID = '$vendorID'");
        if($update_query === true){
            $data = array("Info" => "Details added successfully");
        }
        else { $data = array('Info' => 'Error adding details'); }
      }
      else {
          $data = array('Info' => 'Failed to get code status from Paystack');
      }
    }*/  
    
    $update_query = mysqli_query($conn, "UPDATE vendors SET account_number = '$account_number', bank = '$bank', bank_code = '$bank_code', recipient_code = '$recipient_code' WHERE vendorID = '$vendorID'");
    if($update_query === true){
        $data = array("Info" => "Details added successfully");
    }
    else { $data = array('Info' => 'Error adding details'); }
  }
  else{
      $data = array('Info' => 'Some fields are empty');
  }

  $encodedData = json_encode($data, JSON_FORCE_OBJECT);
  echo $encodedData;
  mysqli_close($conn);
  exit();

?>