<?php

 require '../server/conn.php';

  $account_number = mysqli_real_escape_string($conn, $_GET['account']);
  $bank_code = mysqli_real_escape_string($conn, $_GET['code']);

  $data = array();

  $secret_key = 'sk_live_dd519ff2272708c948e5e92b4149029ab52328ca';

  if (!empty($account_number) && !empty($bank_code)) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.paystack.co/bank/resolve?account_number=$account_number&bank_code=$bank_code",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer $secret_key",
        "Cache-Control: no-cache",
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
                        
    if ($err) {
      $data = array(
        'Info' => 'Error connecting to Paystack gateway',
        'details' => array( 'error' => $err )
    );
    } else {
        $result = json_decode($response, true);
        $details = $result['data'];
        $account_name = $details['account_name'];

        if ($account_name !== 'null' && $account_name !== null) {
           $data = array(
            'Info' => 'Account number verified successfully',
            'details' => array( 'name' =>  $account_name )
          );
        } else {
           $data = array(
            'Info' => 'Account number verification failed',
            'details' => array( 'error' =>  'No account name found for the specified account number.' )
          );
        }
    }
  } else {
     $data = array(
          'Info' => 'Empty fields',
          'details' => array( 'error' => 'Some fields are empty' )
        );
  }

  $encodedData = json_encode($data, JSON_FORCE_OBJECT);
  echo $encodedData;
  exit();
  
?>