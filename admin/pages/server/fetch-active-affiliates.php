<?php 

require 'conn.php';

// Check database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$data = array();

// Execute the query
$sql = mysqli_query($conn, "SELECT * FROM affiliates WHERE affiliate_status = 'Active' ORDER BY affiliateID DESC LIMIT 200");

if (!$sql) {
    die("Error in query execution: " . mysqli_error($conn));
}

// Process results
if (mysqli_num_rows($sql) > 0) {
    while ($row = mysqli_fetch_assoc($sql)) {
        $data[] = $row;
    }
} else {
    $data = array('Info' => 'No record found');
}

// Encode data to JSON format
$encodedData = json_encode($data, JSON_FORCE_OBJECT);  // or JSON_PRETTY_PRINT for readable format

// Output the encoded data
echo $encodedData;

// Close the database connection
mysqli_close($conn);

// Exit the script
exit();

?>
