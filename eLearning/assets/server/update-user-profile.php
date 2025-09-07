<?php

   require 'conn.php';
   
   $userID = mysqli_real_escape_string($conn, $_POST['id']);
   $name = mysqli_real_escape_string($conn, $_POST['name']);
     
 if(!empty($userID)){
    if(!empty($_FILES['profile'])){
        //Define variables
        $targetDir = "../../../uploads/";
        $img_name = $_FILES['profile']['name'];
        $img_type = $_FILES['profile']['type'];
        $tmp_name = $_FILES['profile']['tmp_name'];
        $img_ext = pathinfo($img_name, PATHINFO_EXTENSION);
        $extensions = ["jpeg", "png", "jpg"];
        //Start processing
        $check_user = mysqli_query($conn, "SELECT * FROM users WHERE userID = '$userID'");
        if (mysqli_num_rows($check_user) > 0) {
            $row = mysqli_fetch_assoc($check_user);
            $profile = $row['user_profile'];
            //Check if user currently has a valid profile image set
            if ($profile !== 'null' && $profile !== '' && $profile !== null) {
                //Delete current profile
                if (file_exists('../../../uploads/' . $profile)) {
                     unlink('../../../uploads/' . $profile);
                }
                //Continue
                if(in_array($img_ext, $extensions)){
                    if(move_uploaded_file($tmp_name, $targetDir.$img_name)){
                        $update_query = mysqli_query($conn, "UPDATE users SET user_profile = '$img_name' WHERE userID = '$userID'");
                        if($update_query === true){
                            $data = array(
                                "Info" => "Profile updated successfully"    
                                );
                            //Update profile data on reviews table
                            mysqli_query($conn, "UPDATE reviews SET profile = '$img_name' WHERE fullname = '$name'");
                        }
                        else{ $data = array('Info' => 'Something went wrong'); }
                        
                    }else{ $data = array('Info' => 'Failed to upload image'); }

                }else{ $data = array('Info' => 'Image must have either .jpeg, .png or .jpg extension'); }
            }
            else {
                 //Continue
                if(in_array($img_ext, $extensions)){
                    if(move_uploaded_file($tmp_name, $targetDir.$img_name)){
                        $insert_query = mysqli_query($conn, "UPDATE users SET user_profile = '$img_name' WHERE userID = '$userID'");
                        if($insert_query === true){
                            $data = array(
                                "Info" => "Profile set successfully"
                            );
                            //Update profile data on reviews table
                            mysqli_query($conn, "UPDATE reviews SET profile = '$img_name' WHERE fullname = '$name'");
                        }
                        else{ $data = array('Info' => 'Something went wrong'); }
                        
                    }else{ $data = array('Info' => 'Failed to upload image'); }

                }else{ $data = array('Info' => 'Image must have either .jpeg, .png or .jpg extension'); }
            }
        }
        else{
            $data = array('Info' => 'User details not found');
        }
 
    }else{ $data = array('Info' => 'Upload a valid file'); }
  
}else{ $data = array('Info' => 'Missing parameters'); }


$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);

?>

