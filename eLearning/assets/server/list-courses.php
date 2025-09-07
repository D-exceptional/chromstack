<?php 
    session_start();
    $data = array();
    if(isset( $_SESSION['courses'])){
        $data = array('Info' => 'Courses found', 'details' => $_SESSION['courses']);
    }
    else{
        $data = array('Info' => 'No courses found', 'details' => 'No courses purchased');
    }
    //Send response
    $response = json_encode($data, JSON_FORCE_OBJECT);
    echo $response;
?> 