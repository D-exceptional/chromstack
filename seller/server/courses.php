<?php 

require 'conn.php';

$fullname = mysqli_real_escape_string($conn, $_GET['fullname']);

$response = array();
$user_id = 1;
$user_type = 'Admin'; // Vendors sell their products through admin link

$sql = mysqli_query($conn, "SELECT courseID, course_title, course_description, course_cover_page, course_type, course_status, course_authors, course_amount, admin_percentage, affiliate_percentage, vendor_percentage, uploaded_on, folder_path FROM uploaded_courses WHERE course_authors = '$fullname' AND course_status = 'Approved'");

if(mysqli_num_rows($sql) > 0){

    while($row = mysqli_fetch_assoc($sql)){
        
        $id = $row['courseID'];
        $type = $row['course_type'];
        $short_link = '#';
        
        //Get admin short link
        $short_link_query = mysqli_query($conn, "SELECT short_link FROM sales_short_links WHERE user_id = '$user_id' AND user_type = '$user_type' AND course_id = '$id' AND course_type = '$type'");
        if(mysqli_num_rows($short_link_query) > 0){
            $retval = mysqli_fetch_assoc($short_link_query);
            $short_link = $retval['short_link'];
        }

      $response[] = array(
          'courseID' => $row['courseID'],
          'course_title' => $row['course_title'],
          'course_description' => substr($row['course_description'], 0, 50) . '...',
          'course_cover_page' => $row['course_cover_page'],
          'course_type' => $row['course_type'],
          'course_status' => $row['course_status'],
          'course_authors' => $row['course_authors'],
          'course_amount' => $row['course_amount'],
          'admin_percentage' => $row['admin_percentage'],
          'affiliate_percentage' => $row['affiliate_percentage'],
          'vendor_percentage' => $row['vendor_percentage'],
          'uploaded_on' => $row['uploaded_on'],
          'folder_path' => $row['folder_path'],
          'short_link' => $short_link
      );
    
    }
    
}else{ $response = array('Info' => 'No course found'); }

$encodedData = json_encode($response, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);
exit();
    
?>