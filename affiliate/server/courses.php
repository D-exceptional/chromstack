<?php 

require 'conn.php';

mysqli_set_charset($conn, 'utf8');

$user_id = mysqli_real_escape_string($conn, $_GET['id']);
$user_type = mysqli_real_escape_string($conn, $_GET['type']);

$data = array();

$sql = mysqli_query($conn, "SELECT courseID, course_title, course_description, course_cover_page, course_type, course_status, course_authors, course_amount, admin_percentage, affiliate_percentage, vendor_percentage, uploaded_on, folder_path FROM uploaded_courses WHERE course_status = 'Approved'");

if(mysqli_num_rows($sql) > 0){

    while($row = mysqli_fetch_assoc($sql)){
         $course_id = $row['courseID'];
         $course_type = $row['course_type'];

        //Get admin short link
        $short_link_query = mysqli_query($conn, "SELECT short_link FROM sales_short_links WHERE user_id = '$user_id' AND user_type = '$user_type' AND course_id = '$course_id' AND course_type = '$course_type'");
        $result = mysqli_fetch_assoc($short_link_query);
        $short_link = $result['short_link'];

      $data[] = array(
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
    
}else{ $data = array('Info' => 'No course found'); }

$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);

?>