<?php 

require 'conn.php';

$short_code = mysqli_real_escape_string($conn, $_POST['code']);

//Get admin short link
$long_link_query = mysqli_query($conn, "SELECT long_link FROM contest_short_links WHERE short_code = '$short_code'");
if(mysqli_num_rows($long_link_query) > 0){
    $result = mysqli_fetch_assoc($long_link_query);
    $long_link = $result['long_link'];
    //Send response
    $data = array('Info' => 'Redirecting', 'link' => $long_link);
}
else{
    $data = array('Info' => 'Failed', 'details' => 'Link not resolved');
}

$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);
exit();
    
?>
