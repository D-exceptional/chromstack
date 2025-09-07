<?php 

require 'conn.php';

mysqli_set_charset($conn, 'utf8');

$data = array();

$sql = mysqli_query($conn, "SELECT ticketID, banner, title, event_description, ownerID FROM tickets WHERE ticket_status = 'Approved'");

if(mysqli_num_rows($sql) > 0){
    while($row = mysqli_fetch_assoc($sql)){
        $id = $row['ticketID'];
        $banner = $row['banner'];
        $title = ucwords($row['title']);
        $description = $row['event_description'];
        $ownerID = $row['ownerID'];
        //Get organizer's name
        $query = mysqli_query($conn, "SELECT fullname FROM ticket_owners WHERE ownerID = '$ownerID'");
        $result = mysqli_fetch_assoc($query);
        $organizer = $result['fullname'];

      $data[] = array(
          'id' => $id,
          'banner' => $banner,
          'title' => $title,
          'description' => $description,
          'organizer' => $organizer,
      );
    }
    
}else{ $data = array('Info' => 'No ticket found'); }

$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);
    
?>