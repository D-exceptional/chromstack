<?php

     require 'conn.php';
     
    if (isset($_GET['txref'])) {
        //Get value
        $ref = $_GET['txref'];
        //Global variables
        $amount = '';
        $email = '';

        //Get the details that corresponds to this transaction ref
        $sql = mysqli_query($conn, "SELECT paid_amount, payment_email FROM membership_payment WHERE payment_ref = '$ref'");
        if(mysqli_num_rows($sql) > 0){
            $row = mysqli_fetch_assoc($sql);
            $amount = $row['paid_amount'];
            $email = $row['payment_email'];
        }

        //Correct Currency from Server
        //$currency = "NGN";

        $query = array(
            "SECKEY" => "FLWSECK_TEST-59c2b8240bf35f32696fe178763f0e66-X", //Get the one associated with TecWelth's dashboard
            "txref" => $ref
        );

        $data_string = json_encode($query);
                
        $ch = curl_init(`https://api.flutterwave.com/v3/transactions/{$ref}/verify`);                                                                      
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                              
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        curl_close($ch);
        $resp = json_decode($response, true);
        //Get details
      	$transactionStatus = $resp['data']['status'];
        $chargeResponsecode = $resp['data']['chargecode'];
        $chargedAmount = $resp['data']['amount'];
        $chargeCurrency = $resp['data']['currency'];
        $transactionRef = $resp['data']['tx_ref'];
        $transactionType = $resp['data']['payment_type'];
        $transactionNarration = $resp['data']['narration'];

        if (($chargeResponsecode === "00" || $chargeResponsecode === "0") && ($chargedAmount === $amount)) {
          // transaction was successful...
           $sql = mysqli_query($conn, "SELECT payment_ref FROM membership_payment WHERE payment_ref = '$ref'");
            if(mysqli_num_rows($sql) > 0){
                mysqli_query($conn, "UPDATE membership_payment SET payment_status = 'Completed' WHERE payment_ref = '$ref'");
            }
             //Redirect to success page or telegram group oe wherever necessary
            mysqli_close($conn);
            header("Location: successful_payment.html?amount=$amount&date=$date&reference=$ref&type=$transactionType&narration=$transactionNarration&status=$transactionStatus");
            exit();
        } 
        else {
          //Dont Give Value and return to Failure page
          //var_dump($resp);
          mysqli_close($conn);
          header('Location: error_payment.html');
          exit();
        }
    }
	else {
        exit('No reference supplied');
    }

?>