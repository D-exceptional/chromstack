<?php 

require 'conn.php';

$data = array();

$today = date('Y-m-d');

$sql = mysqli_query($conn, "SELECT * FROM contest WHERE contest_end_date < '$today'");
if(mysqli_num_rows($sql) > 0){
    while($row = mysqli_fetch_assoc($sql)){
        $data[] = array(
            'contestID' => $row['contestID'],
            'contest_title' => $row['contest_title'],
            'contest_description' => substr($row['contest_description'], 0, 50) . '...',
            'contest_start_date' => $row['contest_start_date'],
            'contest_end_date' => $row['contest_end_date'],
            'contest_status' => $row['contest_status']       
         );
    }
    
    }else{ $data = array('Info' => 'No contest found'); }

$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);
    
?>