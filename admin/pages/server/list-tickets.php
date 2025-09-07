<?php 

    require 'conn.php';

    $data = array();

    $sql = mysqli_query($conn, "SELECT ticketID, banner, title, event_description, created_on, ticket_status, ownerID FROM tickets");
    if(mysqli_num_rows($sql) > 0){
        while($row = mysqli_fetch_assoc($sql)){
            $ticketID = $row['ticketID'];
            $ownerID = $row['ownerID'];
            //Get details
            $query = mysqli_query($conn, "SELECT fullname FROM ticket_owners WHERE ownerID = '$ownerID'");
            $result = mysqli_fetch_assoc($query);
            $name = $result['fullname'];
            //Get sales
            $sales_query = mysqli_query($conn, "SELECT * FROM ticket_sales WHERE ticketID = '$ticketID' AND sales_status = 'Completed'");
            $total_sales = mysqli_num_rows($sales_query);
            //Revenue data
            $revenue_query = mysqli_query($conn, "SELECT SUM(amount) AS total_amount FROM ticket_sales WHERE ticketID = '$ticketID' AND sales_status = 'Completed'");
            $revenue_query_result = mysqli_fetch_assoc($revenue_query);
            $total_amount = $revenue_query_result['total_amount'];
            $total_generated_amount = number_format(($total_amount), 2, '.', ',');
            //Prepare response
            $data[] = array(
                'ticketID' => $row['ticketID'],
                'banner' => $row['banner'],
                'title' => $row['title'],
                'description' => substr($row['event_description'], 0, 50) . '...',
                'created' => $row['created_on'],
                'status' => $row['ticket_status'],
                'owner' => $name,
                'sales' => $total_sales,
                'amount' => $total_generated_amount
            );
        }
    }else{ $data = array('Info' => 'No ticket found'); }

    $encodedData = json_encode($data, JSON_FORCE_OBJECT);
    echo $encodedData;
    mysqli_close($conn);
    exit();

?>