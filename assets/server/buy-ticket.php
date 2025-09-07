<?php

    require 'conn.php';

    $response = array();
    
    function generateUniqueCode()
    {
        $n = 10;
        $code = bin2hex(random_bytes($n));
        return 'guest_' . $code;
    }

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $ticketID = mysqli_real_escape_string($conn, $_POST['id']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $dialing_code = mysqli_real_escape_string($conn, $_POST['code']);
    $formatted_contact = $dialing_code . substr($contact, 1);
    $formatted_amount = $amount / 100;
    $currency = 'NGN';  
    $reference = "payment-" . time(); //This should be unique per transaction
    $unique_code = generateUniqueCode(); //This should be unique per transaction
    $date = date('Y-m-d H:i');
    $status = 'Pending';
    $callback_url = "https://chromstack.com/assets/server/verify-ticket-purchase.php"; // Set your redirect URL
    // Set your Paystack secret key
    $secret_key = 'sk_live_dd519ff2272708c948e5e92b4149029ab52328ca';

    if (!empty($name) &&  !empty($email) && !empty($contact) && !empty($amount) && !empty($ticketID) && !empty($code)) {

        //Insert the records into the tables, then update everything when the payment is verified
        if (mysqli_query($conn, "INSERT INTO ticket_sales (name, email, contact, amount, reference, sales_status, created, code, ticketID) VALUES ('$name', '$email', '$formatted_contact', '$formatted_amount', '$reference', '$status', '$date', '$unique_code', '$ticketID')") === true) {
            
            // Prepare payment request parameters
            $request = [
                'email' => $email,
                'amount' => $amount,
                'reference' => $reference,
                'callback_url' => $callback_url,
                'currency' => $currency
            ];

            // Function to create a payment
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://api.paystack.co/transaction/initialize');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $secret_key,
                'Content-Type: application/json',
            ]);

            //Execute curl
            $result = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if($err){
                //there was an error contacting the rave API
                $response = array(
                    'Info' => 'Error connecting to Paystack payment API',
                    'details' => array('error' => 'Curl returned the following error: ' . $err)
                );
            }

            $payment = json_decode($result);
            $payment_data = $payment->data;
            if(!$payment_data && !$payment_data->authorization_url){
                // there was an error from the API
                $response = array(
                    'Info' => 'Paystack payment API error occured',
                    'details' => array('error' => 'API returned the following error: ' . $payment->message)
                );
            }
            else{
                $response = array(
                    'Info' => 'Redirecting to payment page',
                    'details' => array('link' => $payment_data->authorization_url)
                );
            }
        } else {
            $response = array(
                'Info' => 'Details error',
                'details' => array('error' => 'Error saving details')
            );
        }
    } else {
        $response = array(
            'Info' => 'Empty or missing fields',
            'details' => array('error' => 'Some fields are empty or missing')
        );
    }

    $encodedData = json_encode($response, JSON_FORCE_OBJECT);
    echo $encodedData;
    mysqli_close($conn);

?>