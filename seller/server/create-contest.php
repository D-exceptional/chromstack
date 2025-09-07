<?php

require 'conn.php';

function generateShortCode()
{
    $n = 6;
    $code = bin2hex(random_bytes($n));
    return $code;
}

//Start processing

$data = array();

$name = mysqli_real_escape_string($conn, $_POST['name']);
$description = mysqli_real_escape_string($conn, $_POST['description']);
$start = mysqli_real_escape_string($conn, $_POST['start']);
$end = mysqli_real_escape_string($conn, $_POST['end']);
$course_id = mysqli_real_escape_string($conn, $_POST['id']);
$course_type = mysqli_real_escape_string($conn, $_POST['type']);
$author = mysqli_real_escape_string($conn, $_POST['author']);
$status = 'Active';

if(!empty($name) && !empty($description) && !empty($start) && !empty($end) && !empty($author)){

    //Save data to database

    $insert_query = mysqli_query($conn, "INSERT INTO contest (contest_title, contest_description, contest_start_date, contest_end_date, contest_status, courseID, course_type) 
    
    VALUES ('$name', '$description', '$start', '$end', '$status', '$course_id', '$course_type')");

        if($insert_query === true){ 

            $data = array('Info' => 'New contest created sucsessfully');

            //Generate short links for affiliates for this contest
            $affiliate_details = mysqli_query($conn, "SELECT affiliateID FROM affiliates");
            if(mysqli_num_rows($affiliate_details) > 0){
                while($result = mysqli_fetch_assoc($affiliate_details)){
                    $user_id = $result['affiliateID'];
                    $user_type = "Affiliate";
                    $short_code = generateShortCode();
                    $short_link = "https://chromstack.com/c?$short_code";
                    $long_link = "https://chromstack.com/course-purchase.php?ref=$user_id&id=$course_id&type=$course_type&sales=$user_type&narration=Contest";
                    //Save details to database
                    mysqli_query($conn, "INSERT INTO contest_short_links (contest_name, course_id, course_type, user_id, user_type, short_link, long_link, short_code) VALUES ('$name', '$course_id', '$course_type', '$user_id', '$user_type', '$short_link', '$long_link', '$short_code')");
                }
            }

            //Generate short links for admins for this contest
            $admin_details = mysqli_query($conn, "SELECT adminID FROM admins");
            if(mysqli_num_rows($admin_details) > 0){
                while($row = mysqli_fetch_assoc($admin_details)){
                    $user_id = $row['adminID'];
                    $user_type = "Admin";
                    $short_code = generateShortCode();
                    $short_link = "https://chromstack.com/c?$short_code";
                    $long_link = "https://chromstack.com/course-purchase.php?ref=$user_id&id=$course_id&type=$course_type&sales=$user_type&narration=Contest";
                    //Save details to database
                    mysqli_query($conn, "INSERT INTO contest_short_links (contest_name, course_id, course_type, user_id, user_type, short_link, long_link, short_code) VALUES ('$name', '$course_id', '$course_type', '$user_id', '$user_type', '$short_link', '$long_link', '$short_code')");
                }
            }

            //Add to notifications
            $notification_title = 'created sales contest';
            $notification_details = 'New contest was created';
            $notification_type = 'contest_creation';
            $notification_name = $author;
            $notification_date = date('Y-m-d H:i:s');
            $notification_status = 'Unseen';
            //Get all the admin emails and create this notification
            $admin_email_query = mysqli_query($conn, "SELECT email FROM admins");
            while ($row_data = mysqli_fetch_assoc($admin_email_query)) {
                $admin_email = $row_data['email'];
                //Create notification
                mysqli_query($conn, "INSERT INTO general_notifications (notification_title, notification_details, notification_type, notification_name, notification_receiver_email, notification_date, notification_status) 
                VALUES ('$notification_title', '$notification_details', '$notification_type', '$notification_name', '$admin_email', '$notification_date', '$notification_status')");
            }
        }
        else{
            $data = array('Info' => 'Error creating new contest');
        }

}else{ $data = array('Info' => 'Some fields are empty'); }

$encodedData = json_encode($data, JSON_FORCE_OBJECT);

echo $encodedData;

mysqli_close($conn);

?>