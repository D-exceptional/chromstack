<?php

    require 'conn.php';
    require 'functions.php';

    $data = array();

    $name = '';
    $email = '';

    $ticketID = mysqli_real_escape_string($conn, $_POST['ticketID']);
    $action = mysqli_real_escape_string($conn, $_POST['action']);
    if (!empty($ticketID)) {
        switch ($action) {
            case 'approve':
                $query = mysqli_query($conn, "UPDATE tickets SET ticket_status = 'Approved' WHERE ticketID = '$ticketID'");
                if ($query === true) {
                    $data = array('Info' => 'Ticket approved successfully');
                    //Get details
                    $sql = mysqli_query($conn, "SELECT ownerID FROM tickets WHERE ticketID = '$ticketID'");
                    if (mysqli_num_rows($sql) > 0) {
                        $row = mysqli_fetch_assoc($sql);
                        $ownerID = $row['ownerID'];
                        //Continue
                        $details = mysqli_query($conn, "SELECT fullname, email FROM ticket_owners WHERE ownerID = '$ownerID'");
                        if (mysqli_num_rows($details) > 0) {
                            $result = mysqli_fetch_assoc($details);
                            $name = $result['fullname'];
                            $email = $result['email'];
                            //Send email
                            $link = "https://chromstack.com/buy-ticket.php?ticketID=$ticketID";
                            $subject = 'Ticket Approval';
                            $message = "<p>
                                            Hi $name, <br>
                                            We are glad to inform you that your ticket has been approved for sales on our platform. 
                                            Your ticket link is <a href='$link'>$link</a>.
                                            Copy and share the link to across for people to start buying!
                                            Thank you for choosing Chromstack for your ticket sales.
                                        </p>
                                        ";
                            //Send out email
                            send_email($subject, $email, $message);
                        }
                        else{
                            $data = array('Info' => 'Event owner details not found'); // Cusomize this message very well soon
                        }
                       
                    } else {
                       $data = array('Info' => 'Ticket details not found'); // Cusomize this message very well soon
                    }
                }
                else{
                    $data = array('Info' => 'Error approving ticket');
                }
            break;
            case 'disapprove':
                $query = mysqli_query($conn, "UPDATE tickets SET ticket_status = 'Pending' WHERE ticketID = '$ticketID'");
                if ($query === true) {
                    $data = array('Info' => 'Ticket disapproved successfully');
                }
                else{  
                    $data = array('Info' => 'Error disapproving ticket'); 
                }
            break;
            case 'delete':
               $query = mysqli_query($conn, "SELECT * FROM tickets WHERE ticketID  = '$ticketID'");
               if(mysqli_num_rows($query) > 0){
                    $row = mysqli_fetch_assoc($query);
                    $banner = $row['banner'];
                    $dir = "../../../tickets/";
                    if (file_exists($dir . $banner)) {
                        //Delete the course cover page from the courses folder
                        unlink($dir . $banner);
                        //Delete the records from the uploaded_courses table
                        $query = mysqli_query($conn, "DELETE FROM tickets WHERE ticketID  = '$ticketID'");
                        if ($query === true) {
                            $data = array('Info' => 'Ticket deleted successfully');
                        }
                        else{
                            $data = array('Info' => 'Error deleting ticket');
                        }
                    }
                    else{
                        $data = array('Info' => 'Ticket flyer not found');
                    }
                }
                else{
                    $data = array('Info' => 'Ticket details not found');
                }
            break;
        }

    }
    else{
        $data = array('Info' => 'Ticket ID missing');
    }

    $encodedData = json_encode($data, JSON_FORCE_OBJECT);
    echo $encodedData;
    mysqli_close($conn);
    exit();

?>