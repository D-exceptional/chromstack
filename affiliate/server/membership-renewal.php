<?php

require 'conn.php';

$name = mysqli_real_escape_string($conn, $_POST['name']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$contact = mysqli_real_escape_string($conn, $_POST['contact']);
$amount = mysqli_real_escape_string($conn, $_POST['amount']);  
$country = mysqli_real_escape_string($conn, $_POST['country']);
$payment_type = mysqli_real_escape_string($conn, $_POST['type']);
$txref = "txn".time(); //This should be unique per transaction
$date = date('M d, Y  H:i');
$payment_status = 'Pending';
$redirect_url = "http://localhost/tecwelth/assets/server/verify-payment.php"; // Set your redirect URL

//Insert the records into the tables, then update everything when the payment is verified
mysqli_query($conn, "INSERT INTO membership_payment (payment_email, payment_type, paid_amount, payment_date, payment_status, payment_ref) VALUES ('$email', '$payment_type', '$amount', '$date', '$payment_status', '$txref')");

 // Prepare payment request parameters
  $request = [
      'tx_ref' => $txref,
      'amount' => $amount,
      'redirect_url' => $redirect_url,
      'customer' => [
          'email' => $email,
          'phonenumber' => $contact
      ],
      'meta' => [
          'price' => $amount
      ],
      'customizations' => [
          'title' => 'Membership Renewal',
          'description' => 'Payment for membership renewal on Chromsatck platform'
      ]
  ];

//Initiate connection to flutterwave
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.flutterwave.com/v3/payments", //Flutterwave payment API
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => json_encode($request),
  CURLOPT_HTTPHEADER => [
    "content-type: application/json",
    "cache-control: no-cache",
    "Authorization: Bearer FLWSECK_TEST-51abcb5a51d6102d70a4bc6faa2262e0-X" //Get the one associated with TecWelth's dashboard
  ],
));

$response = curl_exec($curl);
$err = curl_error($curl);
if($err){
  //there was an error contacting the rave API
   $data = array(
    'Info' => 'Error connecting to Rave payment API',
    'details' => array('error' => 'Curl returned the following error: '.$err)
   );
   //$encodedData = json_encode($data, JSON_FORCE_OBJECT);
   //echo $encodedData;
   //mysqli_close($conn);
   //exit();
}

$transaction = json_decode($response);
if(!$transaction->data && !$transaction->data->link){
  // there was an error from the API
   $data = array(
    'Info' => 'Rave payment API error ocuured',
    'details' => array('error' => 'API returned the following error: '.$transaction->message)
   );
   //$encodedData = json_encode($data, JSON_FORCE_OBJECT);
   //echo $encodedData;
   //mysqli_close($conn);
   //exit();
}
else{
     $data = array(
        'Info' => 'Redirecting to payment page',
        'details' => array('link' => $transaction->data->link)
    );
     //$encodedData = json_encode($data, JSON_FORCE_OBJECT);
     //echo $encodedData;
    // mysqli_close($conn);
    // exit();
}

$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);
exit();

//redirect to page so User can pay
//header('Location: ' . $transaction->data->link);

?>