<?php 

require 'conn.php';

$data = array();

$vendor = mysqli_real_escape_string($conn, $_GET['vendor']);

$sql = mysqli_query($conn, "SELECT courseID FROM uploaded_courses WHERE course_authors = '$vendor' AND course_type = 'External'");
if(mysqli_num_rows($sql) > 0){
    while($row = mysqli_fetch_assoc($sql)){
        $courseID = $row['courseID'];
        $query = mysqli_query($conn, "SELECT * FROM contest WHERE courseID = '$courseID' AND contest_status = 'Active'");
        //if(mysqli_num_rows($query) > 0){
            while($result = mysqli_fetch_assoc($query)){
                $data[] = array(
                    'contestID' => $result['contestID'],
                    'contest_title' => $result['contest_title'],
                    'contest_description' => substr($result['contest_description'], 0, 50) . '...',
                    'contest_start_date' => $result['contest_start_date'],
                    'contest_end_date' => $result['contest_end_date'],
                    'contest_status' => $result['contest_status']
                );
            }
            
       // }else{ $data = array('Info' => 'No contest set'); }
    }
    
}else{ $data = array('Info' => 'No course found'); }

$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);
    
?>