<?php

require 'conn.php';

$data = array();

//Get incoming request data from JS
$count = $_GET['count'];

$sql = mysqli_query($conn, "SELECT payment_email, payment_status FROM transaction_payments ORDER BY paymentID DESC LIMIT $count");
if(mysqli_num_rows($sql) > 0){
    while($row = mysqli_fetch_assoc($sql)){
        $data[] = array(
            'email' => $row['payment_email'],
            'status' => $row['payment_status']
        );
    }
    //Add response info
    array_push($data, array('Info' => 'Details fetched'));
}
else{ $data = array('Info' => 'No transfers found'); }

$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);
exit();

?>
