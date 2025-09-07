<?php 

//include 'conn.php';

function generateShortCode()
{
  $n = 6;
  $code = bin2hex(random_bytes($n));
  return $code;
}

function generateUniqueCode()
{
  $n = 6;
  $code = bin2hex(random_bytes($n));
  return $code;
}

function generateAffiliateLinks($user) 
{
  include 'conn.php';
  $affiliate_details = mysqli_query($conn, "SELECT affiliateID FROM affiliates WHERE email = '$user'");
  $row = mysqli_fetch_assoc($affiliate_details);
  $user_id = $row['affiliateID'];
  $user_type = "Affiliate";

  //Generate short links for this affiliates for the main course
  $main_course_query = mysqli_query($conn, "SELECT courseID, course_type FROM affiliate_program_course");
  while($result = mysqli_fetch_assoc($main_course_query)){
    $course_id = $result['courseID'];
    $course_type = $result['course_type'];
    $short_code = generateShortCode();
    $short_link = "https://chromstack.com/r?$short_code";
    $long_link = "https://chromstack.com/main-course-purchase.php?ref=$user_id&id=$course_id&type=$course_type&sales=$user_type&narration=Regular";
    //Save details to database
    mysqli_query($conn, "INSERT INTO sales_short_links (course_id, course_type, user_id, user_type, short_link, long_link, short_code) VALUES ('$course_id', '$course_type', '$user_id', '$user_type', '$short_link', '$long_link', '$short_code')");
  }

  //Generate short links for this affiliates for other courses
  $other_courses_query = mysqli_query($conn, "SELECT courseID, course_type FROM uploaded_courses");
  while($result = mysqli_fetch_assoc($other_courses_query)){
    $course_id = $result['courseID'];
    $course_type = $result['course_type'];
    $short_code = generateShortCode();
    $short_link = "https://chromstack.com/r?$short_code";
    $long_link = "https://chromstack.com/course-purchase.php?ref=$user_id&id=$course_id&type=$course_type&sales=$user_type&narration=Regular";
    //Save details to database
    mysqli_query($conn, "INSERT INTO sales_short_links (course_id, course_type, user_id, user_type, short_link, long_link, short_code) VALUES ('$course_id', '$course_type', '$user_id', '$user_type', '$short_link', '$long_link', '$short_code')");
  }
}

function generateContestLinks($id, $type)  
{
  include 'conn.php';
  //Generate short links for this affiliates for the main course
  $contest_check_query = mysqli_query($conn, "SELECT * FROM contest");
    while($result = mysqli_fetch_assoc($contest_check_query)){
      $contest_title = $result['contest_title'];
      $course_id = $result['courseID'];
      $course_type = $result['course_type'];
      //Create contest links for affiliate
      if ($course_type === 'Affiliate') {
        $short_code = generateUniqueCode();
        $short_link = "https://chromstack.com/c?$short_code";
        $long_link = "https://chromstack.com/main-course-purchase.php?ref=$id&id=$course_id&type=$course_type&sales=$type&narration=Contest";
        //Save details to database
        mysqli_query($conn, "INSERT INTO contest_short_links (contest_name, course_id, course_type, user_id, user_type, short_link, long_link, short_code) VALUES ('$contest_title', '$course_id', '$course_type', '$id', '$type', '$short_link', '$long_link', '$short_code')");
      }
      else{
        $short_code = generateUniqueCode();
        $short_link = "https://chromstack.com/c?$short_code";
        $long_link = "https://chromstack.com/course-purchase.php?ref=$id&id=$course_id&type=$course_type&sales=$type&narration=Contest";
        //Save details to database
        mysqli_query($conn, "INSERT INTO contest_short_links (contest_name, course_id, course_type, user_id, user_type, short_link, long_link, short_code) VALUES ('$contest_title', '$course_id', '$course_type', '$id', '$type', '$short_link', '$long_link', '$short_code')");
      }
    }
}

// Function to display the contents of other sub folders
function createWishlist($directory, $title, $id, $type) 
{
  include 'conn.php';
  $wishlistID = 0;
  //Create wishlist
  if (mysqli_query($conn, "INSERT INTO wishlist (course_title, course_status, course_progress, user_id, user_type) VALUES ('$title', 'Pending', 0, '$id', '$type')") === true) {
    sleep(1);
    //Get wishlistID
    $wishlist_query = mysqli_query($conn, "SELECT wishlistID FROM wishlist WHERE user_id = '$id' AND user_type = '$type' AND course_title = '$title'");
    if(mysqli_num_rows($wishlist_query) > 0){
      $wishlist_details = mysqli_fetch_assoc($wishlist_query);
      $wishlistID = $wishlist_details['wishlistID'];
    }
    //List contents
    if (is_dir($directory)) {
      $subfolders = glob($directory . '/*', GLOB_ONLYDIR);
      foreach ($subfolders as $folder){
        $files = glob($folder . '/*');
        //Loop through the course contents
        foreach ($files as $file) {
          if(is_file($file)){
            //Get file details
            $filename = pathinfo($file, PATHINFO_FILENAME);
            //Add to table to enable tracking
            mysqli_query($conn, "INSERT INTO wishlist_tracking (track_filename, track_status, wishlistID) VALUES ('$filename', 'Pending', $wishlistID)");
          }
        }
      }
    }
  } 
}

?>