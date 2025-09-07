<?php

    require 'conn.php';

    //Set the time zone to AFrica
    date_default_timezone_set("Africa/Lagos");

    $adminID = mysqli_real_escape_string($conn, $_POST['id']);
    $date = date('Y-m-d H:i:s');
    
    if(!empty($adminID)){

    if(!empty($_FILES['profile'])){

        $query = mysqli_query($conn, "SELECT fullname, admin_profile FROM admins WHERE adminID = '$adminID'");
            if (mysqli_num_rows($query) > 0) {
                $row = mysqli_fetch_assoc($query);
                $profile = $row['admin_profile'];
                $name = $row['fullname'];

                if (file_exists('../../../uploads/'.$profile)) {
                    unlink('../../../uploads/'.$profile);
                }
        }

    $targetDir = "../../../uploads/";
    $img_name = $_FILES['profile']['name'];
    $img_type = $_FILES['profile']['type'];
    $tmp_name = $_FILES['profile']['tmp_name'];
    $img_ext = pathinfo($img_name, PATHINFO_EXTENSION);
    $extensions = ["jpeg", "png", "jpg"];

    if(in_array($img_ext, $extensions) === true){
            
            if(move_uploaded_file($tmp_name, $targetDir.$img_name)){ 

                //Set new image name
                $new_img_name = pathinfo($img_name, PATHINFO_FILENAME).time().'.webp';

                //Compress the uploaded image ans store in database

                switch($img_ext){ 
                    case 'jpeg':
                    case 'jpg':  
                            $image = imagecreatefromjpeg($targetDir.$img_name); 
                            //Quality can be a value between 1-100
                            $quality = 100; 
                            //Create the webp version of the uploaded image.
                            imagewebp($image, $targetDir.$new_img_name, $quality);
                            //Destroy the image object
                            imagedestroy($image);
                            //delete initial uploaded image
                            unlink($targetDir.$img_name);
                    break; 
                    case 'png': 
                            $image = imagecreatefrompng($targetDir.$img_name); 
                            //Quality can be a value between 1-100
                            $quality = 100; 
                            //Create the webp version of the uploaded image.
                            imagewebp($image, $targetDir.$new_img_name, $quality);
                            //Destroy the image object
                            imagedestroy($image);
                            //delete initial uploaded image
                            unlink($targetDir.$img_name);
                    break; 
                } 
                
                $insert_query = mysqli_query($conn, "UPDATE admins SET admin_profile = '$new_img_name' WHERE adminID = '$adminID'");

                if($insert_query === true){

                    $data = array("Info" => "Profile updated successfully");
                    
                     //Update profile data on reviews table
                    mysqli_query($conn, "UPDATE reviews SET profile = '$new_img_name' WHERE fullname = '$name'");

                    }else{ $data = array('Info' => 'Something went wrong'); }
                
            }else{ $data = array('Info' => 'Failed to upload image'); }

    }else{ $data = array('Info' => 'Image must have either .jpeg, .png or .jpg extension'); }

    }else{ $data = array('Info' => 'Upload a valid image'); }

    }else{ $data = array('Info' => 'All fields must be filled up'); }


    $encodedData = json_encode($data, JSON_FORCE_OBJECT);

    echo $encodedData;

    mysqli_close($conn);

    exit();

?>

