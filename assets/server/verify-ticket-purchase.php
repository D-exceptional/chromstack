<?php

    require 'conn.php';
    require 'functions.php';
    $name = '';
    $email = '';
    $title = '';
    $db_amount = '';
    $code = '';

    if (isset($_GET['reference']) && !empty($_GET['reference'])) {
        //Ref from Paystack
        $reference = $_GET['reference'];
        //Get the details that corresponds to this transaction ref
        $sql = mysqli_query($conn, "SELECT name, email, amount, code, ticketID FROM ticket_sales WHERE reference = '$reference'");
        if(mysqli_num_rows($sql) > 0){
            $row = mysqli_fetch_assoc($sql);
            $name = $result['name'];
            $email = $row['email'];
            $code = $row['code'];
            $db_amount = '&#8358;' . number_format(($row['amount']), 2, '.', ',');
            $ticketID = $row['ticketID'];
            //Get event title
            $query = mysqli_query($conn, "SELECT title FROM tickets WHERE ticketID = '$ticketID'");
            $result = mysqli_fetch_assoc($query);
            $title = $result['title'];
        }

        $secret_key = "sk_live_dd519ff2272708c948e5e92b4149029ab52328ca"; // Replace with your Paystack Secret Key

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://api.paystack.co/transaction/verify/$reference");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $secret_key,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if($err){
           header("Location: https://chromstack.com/error-payment");
           exit();
        }
        else{
            $result = json_decode($response, true); //Get the result of executing the query
            $status = $result['status']; //Get the status of the API call
            $data = $result['data']; //Data array
            $amount_paid = $data['amount'] / 100; // Amount is in kobo, convert to naira (or your currency)
            $payment_currency = $data['currency']; //Get the currency of the payment
            $payment_status = $data['status']; //Get the status of the payment
            $formatted_amount = '&#8358;' . number_format(($amount_paid), 2, '.', ',');

            if ($status === true && $payment_status === 'success') {  // Payment is successful
                //Update membership_payment table
                $sql = mysqli_query($conn, "SELECT * FROM ticket_sales WHERE reference = '$reference'");
                if(mysqli_num_rows($sql) > 0){
                    mysqli_query($conn, "UPDATE ticket_sales SET sales_status = 'Completed' WHERE reference = '$reference'");
                }

                //Send account activation link
                $link = '#';
                $text = 'Enjoy';
                $subject = 'Successful Ticket Purchase';
                $message = "
                                Hi $name, <br>
                                Your ticket purchase for the event, <b>$title</b>, was successful.  <br>
                                The confirmed amount you paid is: $formatted_amount.  <br>
                                Your unique code for this event is: <b>$code</b>.  <br>
                                Thank you for buying ticket through our platform.  <br>
                                Warm regards from the Chromstack team.
                                <br>
                                <br>
                                <a href='$link' target='_blank'><b>$text</b></a>
                            ";
                //Send email
                send_email($subject, $email, $message);

                //Redirect to status page
                header("Location: https://chromstack.com/ticket-payment-status.php?name=$name&amount=$formatted_amount&status=success");
            } 
            else {
                //Redirect to status page
                header("Location: https://chromstack.com/ticket-payment-status.php?name=$name&amount=$formatted_amount&status=failed");
            }
        }
    }
    else{
        die('No reference supplied');
    }

?>
