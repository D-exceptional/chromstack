<?php 

require 'conn.php';

mysqli_set_charset($conn, 'utf8');

$data = array();
$main_course = array();
$vendor_courses = array();
$user_id = 1;
$user_type = 'Admin';

$sql = mysqli_query($conn, "SELECT courseID, course_title, course_cover_page, course_type, course_amount, course_category, course_narration, sales_page, folder_path FROM uploaded_courses WHERE course_status = 'Approved'");

if(mysqli_num_rows($sql) > 0){

    while($row = mysqli_fetch_assoc($sql)){
        
        $id = $row['courseID'];
        $type = $row['course_type'];
        
        $short_link = '#';
        
        //Get buyers
        $buyers_query = mysqli_query($conn, "SELECT * FROM purchased_courses WHERE courseID = '$id' AND course_type = '$type' AND purchase_status = 'Completed'");
        $buyersCount = mysqli_num_rows($buyers_query);
        
        //Count reviews
        $reviews_query = mysqli_query($conn, "SELECT * FROM reviews WHERE courseID = '$id' AND course_type = '$type'");
        $reviewsCount = mysqli_num_rows($reviews_query);
        
        //Get author
        $authors_query = mysqli_query($conn, "SELECT course_authors FROM uploaded_courses WHERE courseID = '$id' AND course_type = '$type'");
        $retVal = mysqli_fetch_assoc($authors_query);
        $author = $retVal['course_authors'];
        
        //Get admin short link
        $short_link_query = mysqli_query($conn, "SELECT short_link FROM sales_short_links WHERE user_id = '$user_id' AND user_type = '$user_type' AND course_id = '$id' AND course_type = '$type'");
        if(mysqli_num_rows($short_link_query) > 0){
            $retval = mysqli_fetch_assoc($short_link_query);
            $short_link = $retval['short_link'];
        }

        $vendor_courses[] = array(
          'courseID' => $row['courseID'],
          'course_title' => $row['course_title'],
          'course_cover_page' => $row['course_cover_page'],
          'course_type' => $row['course_type'],
          'course_amount' => $row['course_amount'],
          'course_category' => $row['course_category'],
          'course_narration' => $row['course_narration'],
          'sales_page' => $row['sales_page'],
          'folder_path' => $row['folder_path'],
          'buyers' => $buyersCount,
          'reviews' => $reviewsCount,
          'author' => $author,
          'short_link' => $short_link
      );
    
    }
}

$query = mysqli_query($conn, "SELECT courseID, course_title, course_cover_page, course_type, course_amount, course_category, course_narration, sales_page, folder_path FROM affiliate_program_course WHERE course_status = 'Approved'");

if(mysqli_num_rows($query) > 0){

    while($result = mysqli_fetch_assoc($query)){
        
        $id = $result['courseID'];
        $type = $result['course_type'];
        
        //Get buyers
        $buyers_query = mysqli_query($conn, "SELECT * FROM purchased_courses WHERE courseID = '$id' AND course_type = '$type' AND purchase_status = 'Completed'");
        $buyersCount = mysqli_num_rows($buyers_query);
        
        //Count reviews
        $reviews_query = mysqli_query($conn, "SELECT * FROM reviews WHERE courseID = '$id' AND course_type = '$type'");
        $reviewsCount = mysqli_num_rows($reviews_query);

        //Get admin short link
        $short_link_query = mysqli_query($conn, "SELECT short_link FROM sales_short_links WHERE user_id = '$user_id' AND user_type = '$user_type' AND course_id = '$id' AND course_type = '$type'");
        $retval = mysqli_fetch_assoc($short_link_query);
        $short_link = $retval['short_link'];

        $main_course[] = array(
          'courseID' => $result['courseID'],
          'course_title' => $result['course_title'],
          'course_cover_page' => $result['course_cover_page'],
          'course_type' => $result['course_type'],
          'course_amount' => $result['course_amount'],
          'course_category' => $result['course_category'],
          'course_narration' => $result['course_narration'],
          'sales_page' => $result['sales_page'],
          'folder_path' => $result['folder_path'],
          'buyers' => $buyersCount,
          'reviews' => $reviewsCount,
          'short_link' => $short_link
      );
    
    }
}

$data[] = array(
    'main' => $main_course,
    'vendor' => $vendor_courses
);

$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);

?>