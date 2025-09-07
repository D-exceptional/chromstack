<?php 

require 'conn.php';

$data = array();

$user_id = $_GET['id'];
$user_type = 'Admin';

$today = date('Y-m-d');

$sql = mysqli_query($conn, "SELECT * FROM contest");
if(mysqli_num_rows($sql) > 0){
    while($row = mysqli_fetch_assoc($sql)){
     $contest_title = $row['contest_title'];
     $contest_end_date = $row['contest_end_date'];
     if ($contest_end_date < $today) {
        mysqli_query($conn, "UPDATE contest SET contest_status = 'Completed' WHERE contest_title = '$contest_title'");
     }
    }

    $query = mysqli_query($conn, "SELECT * FROM contest WHERE contest_status = 'Active'");
    if(mysqli_num_rows($query) > 0){
        while($row = mysqli_fetch_assoc($query)){
            $contest_title = $row['contest_title'];
            //Get link
            $link_query = mysqli_query($conn, "SELECT short_link FROM contest_short_links WHERE contest_name = '$contest_title' AND user_id = '$user_id' AND user_type = '$user_type'");
            $result = mysqli_fetch_assoc($link_query);
            $short_link = $result['short_link'];
            //Prepare response
            $data[] = array(
                'contestID' => $row['contestID'],
                'contest_title' => $row['contest_title'],
                'contest_description' => substr($row['contest_description'], 0, 50) . '...',
                'contest_start_date' => $row['contest_start_date'],
                'contest_end_date' => $row['contest_end_date'],
                'contest_status' => $row['contest_status'],
                'short_link' => $short_link
            );
        }
    }
}
else{ $data = array('Info' => 'No contest found'); }

$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);
    
?>