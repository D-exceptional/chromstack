<?php

    require 'conn.php';
    require 'functions.php';

    $data = array();

    //Set the time zone to AFrica
    date_default_timezone_set("Africa/Lagos");

    //Begin processing
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $business = mysqli_real_escape_string($conn, $_POST['business']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $code = mysqli_real_escape_string($conn, $_POST['code']);
    $formatted_contact = $code . substr($contact, 1);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $date = date('Y-m-d H:i');
    $event_status = 'Pending';
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    //Directory details
    $targetDir = "../../tickets/";
    $extensions = ["jpeg", "png", "jpg"];


    switch($status){
        case 'Registered':
            $name = '';
            $query = mysqli_query($conn, "SELECT ownerID, fullname FROM ticket_owners WHERE email = '$email'");
            if (mysqli_num_rows($query) > 0) {
                $row = mysqli_fetch_assoc($query);
                $ownerID = $row['ownerID'];
                $name = $row['fullname'];
                $event_status = 'Pending';
                //Save ticket details
                $title_check = mysqli_query($conn, "SELECT title FROM tickets WHERE title = '$title' AND ownerID = '$ownerID'");
                if (mysqli_num_rows($title_check) > 0) {
                    $data = array('Info' => 'An event with this title has been associated with a user'); // Cusomize this message very well soon
                }
                else{
                    //Upload banner
                    if(isset($_FILES['banner']) && !empty($_FILES['banner'])){
                        $img_name = $_FILES['banner']['name'];
                        $img_type = $_FILES['banner']['type'];
                        $tmp_name = $_FILES['banner']['tmp_name'];
                        $img_ext = pathinfo($img_name, PATHINFO_EXTENSION);
                        //Set new image name
                        $new_img_name = pathinfo($img_name, PATHINFO_FILENAME) . time() . '.' . $img_ext;
                        if(in_array($img_ext, $extensions) === true){
                            if(move_uploaded_file($tmp_name, $targetDir . $new_img_name)){ 
                                $insert_query = mysqli_query($conn, "INSERT INTO tickets (banner, title, event_description, created_on, ticket_status, ownerID) VALUES ('$new_img_name', '$title', '$description', '$date', '$event_status', '$ownerID')");
                                if($insert_query === true){
                                    $data = array("Info" => "Details saved successfully");
                                    //Send email
                                    $link = '#';
                                    $text = 'Congratulations';
                                    $subject = "Successful Ticket Upload";
                                    $message = "
                                                    Hi $name, <br>
                                                    We are glad to inform you that your ticket upload to our platform was successful. 
                                                    We will notify you once we are done reviewing your details.
                                                    Thank you for choosing Chromstack for your ticket sales.
                                                ";
                                    //Send out email
                                    send_email($subject, $email, $message, $link, $text);
                                }else{ $data = array('Info' => 'Something went wrong'); }
                            }else{ $data = array('Info' => 'Failed to upload image'); }
                        }else{ $data = array('Info' => 'Image must have either .jpeg, .png or .jpg extension'); }
                    }else{ $data = array('Info' => 'Upload a valid image'); }
                }
            }
            else{
                $data = array('Info' => 'ID not found'); // Cusomize this message very well soon
            }
        break;
        case 'Unregistered':
            //Save personal details
            if (!empty($fullname) && !empty($email) &&! empty($contact) && !empty($business) && !empty($country) && !empty($code) && !empty($title) && !empty($description)) {
                if(mysqli_query($conn, "INSERT INTO ticket_owners (fullname, email, contact, country, created_on, business_name) VALUES ('$fullname', '$email', '$formatted_contact', '$country', '$date', '$business')") === true){
                    $query = mysqli_query($conn, "SELECT ownerID FROM ticket_owners WHERE email = '$email'");
                    if (mysqli_num_rows($query) > 0) {
                        $row = mysqli_fetch_assoc($query);
                        $ownerID = $row['ownerID'];
                        //Save ticket details
                        $title_check = mysqli_query($conn, "SELECT title FROM tickets WHERE title = '$title' AND ownerID = '$ownerID'");
                        if (mysqli_num_rows($title_check) > 0) {
                            $data = array('Info' => 'An event with this title has been associated with a user'); // Cusomize this message very well soon
                        }
                        else{
                            //Upload banner
                            if(isset($_FILES['banner']) && !empty($_FILES['banner'])){
                                $img_name = $_FILES['banner']['name'];
                                $img_type = $_FILES['banner']['type'];
                                $tmp_name = $_FILES['banner']['tmp_name'];
                                $img_ext = pathinfo($img_name, PATHINFO_EXTENSION);
                                //Set new image name
                                $new_img_name = pathinfo($img_name, PATHINFO_FILENAME) . time() . '.' . $img_ext;
                                if(in_array($img_ext, $extensions) === true){
                                    if(move_uploaded_file($tmp_name, $targetDir . $new_img_name)){ 
                                        $insert_query = mysqli_query($conn, "INSERT INTO tickets (banner, title, event_description, created_on, ticket_status, ownerID) VALUES ('$new_img_name', '$title', '$description', '$date', '$event_status', '$ownerID')");
                                        if($insert_query === true){
                                            $data = array("Info" => "Details saved successfully");
                                            //Send email
                                            $subject = "Successful Ticket Upload";
                                            $message = "<p>
                                                            Hi $fullname, <br>
                                                            We are glad to inform you that your ticket has been uploaded on our platform was successful. 
                                                            We will notify you once we are done reviewing your details.
                                                            Thank you for choosing Chromstack for your ticket sales.
                                                        </p>
                                                        ";
                                            //Send out email
                                            send_email($subject, $email, $message);
                                        }else{ $data = array('Info' => 'Something went wrong'); }
                                    }else{ $data = array('Info' => 'Failed to upload image'); }
                                }else{ $data = array('Info' => 'Image must have either .jpeg, .png or .jpg extension'); }
                            }else{ $data = array('Info' => 'Upload a valid image'); }
                        }
                    }
                    else{
                        $data = array('Info' => 'ID not found'); // Cusomize this message very well soon
                    }
                }
                else{
                    $data = array('Info' => 'Error saving details'); // Cusomize this message very well soon
                }
            } else {
               $data = array('Info' => 'Some fields are empty'); // Cusomize this message very well soon
            }
        break;
    }


    $encodedData = json_encode($data, JSON_FORCE_OBJECT);
    echo $encodedData;
    mysqli_close($conn);
    exit();

?>

