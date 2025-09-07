<?php

    require 'conn.php';

    //Set the time zone to AFrica
    date_default_timezone_set("Africa/Lagos");

    $data = array();

    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);  
    $description = mysqli_real_escape_string($conn, $_POST['description']); 
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);  
    //Metadata
    $sales_page = mysqli_real_escape_string($conn, $_POST['page']);  
    $category = mysqli_real_escape_string($conn, $_POST['category']);  
    $product_format = mysqli_real_escape_string($conn, $_POST['product_type']);  
    //Ends
    $date = date('Y-m-d H:i:s');
    $status = 'Pending';
    $course_type = 'Admin';
    $affiliate_commission = mysqli_real_escape_string($conn, $_POST['affiliate_commission']);  
    $real_affiliate_commission = substr($affiliate_commission, 0, -1);
    $vendor_commission = (100 - (intval($real_affiliate_commission) + 10)) . '%';
    $admin_commission = '10%';  
    $foldername = 'null';

    if(isset($_FILES['course_cover_page']) && !empty($_FILES['course_cover_page'])){
        //Process course zip file
        $course_cover_image = $_FILES["course_cover_page"]["name"]; 
        $source = $_FILES["course_cover_page"]["tmp_name"]; 
        $file_ext = pathinfo($course_cover_image, PATHINFO_EXTENSION);
        $extensions = ["jpeg", "png", "jpg", "JPEG"];
        if (!in_array($file_ext, $extensions)) {
            $data = array('Info' => 'The file you are trying to upload is not an image!');
        }
        else{
            $path = "../../../assets/img/" . $course_cover_image;  // absolute path to the directory where zip file will be unzipped
            /* here it is really happening */ 
            if(move_uploaded_file($source, $path)) { 

                $insert_query = mysqli_query($conn, "INSERT INTO uploaded_courses (course_title, course_description, course_cover_page, course_type, course_status, course_authors, course_amount, sales_page, course_category, course_narration, admin_percentage, affiliate_percentage, vendor_percentage, uploaded_on, folder_path)

                VALUES ('$title', '$description', '$course_cover_image', '$course_type', '$status', '$fullname', '$amount', '$sales_page', '$category', '$product_format', '$admin_commission', '$affiliate_commission', '$vendor_commission', '$date', '$foldername')");

                if($insert_query === true){ 

                    $data = array('Info' => 'Course uploaded successfully'); 

                    //Add to notifications
                    $notification_title = 'uploaded a course';
                    $notification_details = 'A course with the title '. '<b>' . $title . '</b>, owned by ' . $fullname . ' was uploaded on the site';
                    $notification_type = 'course_upload';
                    $notification_name = $fullname;
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

                }else{ $data = array('Info' => 'Error processing course upload request');  }
                                            
            }
            else {	 
                $data = array('Info' => 'Error uploading product details'); 
            } 
        }
    }
    else{
        $data = array('Info' => 'Upload a valid image file'); 
    }

     $encodedData = json_encode($data, JSON_FORCE_OBJECT);
     echo $encodedData;
     mysqli_close($conn);

?>

