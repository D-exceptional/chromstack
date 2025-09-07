<?php

 require 'conn.php';

    $data = array();

    $id = $_GET['id'];
    if(!empty($id)){
        $sql = mysqli_query($conn, "SELECT course_type, courseID FROM contest WHERE contestID = '$id'");
        $row = mysqli_fetch_assoc($sql);
        $course_type = $row['course_type'];
        $course_id = $row['courseID'];
        $data = array(
            "Info" => "Data fetched successfully",
            "details" => array( 
                "id" => $course_id,
                "type" => $course_type 
            )
        );
    }
    else{
        $data = array('Info' => 'Some fields are empty');
    }

    $encodedData = json_encode($data, JSON_FORCE_OBJECT);
    echo $encodedData;
    mysqli_close($conn);

?>