<?php

require 'conn.php';
require 'functions.php';

function generateShortCode()
{
    $n = 6;
    $code = bin2hex(random_bytes($n));
    return $code;
}

$data = array();

$courseID = mysqli_real_escape_string($conn, $_POST['courseID']);
if (!empty($courseID)) {
    $query = mysqli_query($conn, "UPDATE uploaded_courses SET course_status = 'Approved' WHERE courseID = '$courseID'");
    if ($query === true) {
        $data = array('Info' => 'Course approved successfully');
        //Get course title
        $sql = mysqli_query($conn, "SELECT course_title, course_type, course_authors FROM uploaded_courses WHERE courseID = '$courseID'");
        $row = mysqli_fetch_assoc($sql);
        $course_title  = $row['course_title'];
        $course_author  = $row['course_authors'];
        $course_type  = $row['course_type'];

        //Create sales links for admins 
        $admin_details = mysqli_query($conn, "SELECT * FROM admins");
        while($result = mysqli_fetch_assoc($admin_details)){
            $user_id = $result['adminID'];
            $user_type = "Admin";
            $short_code = generateShortCode();
            $short_link = "https://chromstack.com/r?$short_code";
            $long_link = "https://chromstack.com/course-purchase.php?ref=$user_id&id=$courseID&type=$course_type&sales=$user_type&narration=Regular";
            //Save details to database
            mysqli_query($conn, "INSERT INTO sales_short_links (course_id, course_type, user_id, user_type, short_link, long_link, short_code) VALUES ('$courseID', '$course_type', '$user_id', '$user_type', '$short_link', '$long_link', '$short_code')");
        }
        //Create sales links for affiliates 
        $affiliate_details = mysqli_query($conn, "SELECT * FROM affiliates");
        while($info = mysqli_fetch_assoc($affiliate_details)){
            $user_id = $info['affiliateID'];
            $user_type = "Affiliate";
            $short_code = generateShortCode();
            $short_link = "https://chromstack.com/r?$short_code";
            $long_link = "https://chromstack.com/course-purchase.php?ref=$user_id&id=$courseID&type=$course_type&sales=$user_type&narration=Regular";
            //Save details to database
            mysqli_query($conn, "INSERT INTO sales_short_links (course_id, course_type, user_id, user_type, short_link, long_link, short_code) VALUES ('$courseID', '$course_type', '$user_id', '$user_type', '$short_link', '$long_link', '$short_code')");
        }

        //Get author details
        switch ($course_type) {
            case 'Admin':
                $details_query = mysqli_query($conn, "SELECT email FROM admins WHERE fullname = '$course_author'");
                $details_result = mysqli_fetch_assoc($details_query);
                $author_email = $details_result['email'];
            break;
            case 'External':
                $details_query = mysqli_query($conn, "SELECT email FROM vendors WHERE fullname = '$course_author'");
                $details_result = mysqli_fetch_assoc($details_query);
                $author_email = $details_result['email'];
            break;
        }
       
        //Add to notifications
        $notification_title = 'was vetted and approved';
        $notification_details = 'An admin or external course, by the title' . '<b>' . $course_title . '</b>' . ' was vetted and approved';
        $notification_type = 'course_approval';
        $notification_name = $course_title;
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
        
        global $author_email;

        //Send notification mail to author
        $subject = "Successful Course Approval";
        $message = "Congratulations &#128640;&#129392;, $course_author! <br> Your course, <b>$course_title</b> has been approved. <br> It is now listed on our marketplace for affiliates to begin promotion and sales.";
        send_email($subject, $author_email, $message);


    }else{  $data = array('Info' => 'Error approving course'); }
}else{  $data = array('Info' => 'Course ID missing'); }
        
$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);
exit();

?>