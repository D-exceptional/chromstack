<?php

     require 'conn.php';

     //Set the time zone to AFrica
     date_default_timezone_set("Africa/Lagos");

    $data = array();

    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);  
    $description = mysqli_real_escape_string($conn, $_POST['description']); 
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);  
    $date = date('Y-m-d H:i:s');
    $status = 'Pending';
    $course_type = 'External';
    $affiliate_commission = mysqli_real_escape_string($conn, $_POST['affiliate_commission']);  
    $real_affiliate_commission = substr($affiliate_commission, 0, -1);
    $vendor_commission = (100 - (intval($real_affiliate_commission) + 10)) . '%';
    $admin_commission = '10%';  

    if(isset($_FILES['course_main_file']) && !empty($_FILES['course_main_file'])){
        //Process course zip file
        $filename = $_FILES["course_main_file"]["name"]; 
        $source = $_FILES["course_main_file"]["tmp_name"]; 
        $type = $_FILES["course_main_file"]["type"]; 
        $accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed'); 
        if (!in_array($type, $accepted_types)) {
            $data = array('Info' => 'The file you are trying to upload is not a zip file!');
        }
        else{
            $path = "../../courses/";  // absolute path to the directory where zip file will be unzipped
            $foldername = pathinfo($filename, PATHINFO_FILENAME);
            $targetdir = $path . $foldername; // target directory 
            $targetzip = $path . $filename; // target zip file  
            
            /* here it is really happening */ 
            if(move_uploaded_file($source, $targetzip)) { 
                $zip = new ZipArchive(); 
                $x = $zip->open($targetzip);  // open the zip file to extract 
                //Proceed to extract zip to folder
                if ($x === true) { 
                    //Extract zip file
                    $zip->extractTo($path); // place in the directory with same name   
                    $zip->close(); 
                    //Delete former zip file
                    unlink($targetzip); 
                }
                
                //Wait for 3secs
                sleep(5);

                //Get cover page
                if(is_dir($targetdir)) {
                    $files = glob($targetdir . "/*");
                    if(count($files) > 0){
                        // Loop through retuned array
                        foreach($files as $file){
                            if(!is_dir($file)){
                                //Get file extension
                                $extension = pathinfo($file, PATHINFO_EXTENSION);
                                $name_info = pathinfo($file, PATHINFO_FILENAME);

                                if (in_array($name_info, ['cover_page', 'cover-page', 'course_cover_page', 'course-cover-page'])) {

                                    //Set new image name
                                    $course_cover_image = $name_info . '.' . $extension;

                                    $insert_query = mysqli_query($conn, "INSERT INTO uploaded_courses (course_title, course_description, course_cover_page, course_type, course_status, course_authors, course_amount, admin_percentage, affiliate_percentage, vendor_percentage, uploaded_on, folder_path)

                                    VALUES ('$title', '$description', '$course_cover_image', '$course_type', '$status', '$fullname', '$amount', '$admin_commission', '$affiliate_commission', '$vendor_commission', '$date', '$foldername')");
                
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

                                    break;
                                    
                                }else{ $data = array('Info' => 'No cover page found in uploaded file');  }
                            }
                        }
                    }else{ $data = array('Info' => 'No file found in uploaded file'); }

                }else{ $data = array('Info' => 'Course directory not found'); }
            }
            else {	 
                $data = array('Info' => 'Error uploading course for processing'); 
            } 
        }
    }
    else{
        $data = array('Info' => 'Upload a valid zip file'); 
    }

     $encodedData = json_encode($data, JSON_FORCE_OBJECT);
     echo $encodedData;
     mysqli_close($conn);

?>

