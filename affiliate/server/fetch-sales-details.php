<?php 

require 'conn.php';

$data = array();

$affiliateID = mysqli_real_escape_string($conn, $_GET['id']);
$type = mysqli_real_escape_string($conn, $_GET['type']);   
if(!empty($affiliateID) && !empty($type)){
    switch ($type) {
        case 'daily':
            /**** Variables ****/
            $uploaded_course_commission = 0;
            $main_course_commission = 0;
            /****** End ******/
            $today = date('Y-m-d');
            $one_day_ago = date('Y-m-d', strtotime($today . ' - 1 day'));
            //Check uploaded course sales
            $uploaded_course_check = mysqli_query($conn, "SELECT SUM(affiliate_commission) AS total_earnings FROM uploaded_course_sales_backup WHERE sellerID = '$affiliateID' AND sales_date BETWEEN '$one_day_ago' AND '$today' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
            $uploaded_course_rows = mysqli_num_rows($uploaded_course_check);
            if($uploaded_course_rows > 0){ 
                while ($row = mysqli_fetch_assoc($uploaded_course_check)) {
                    $uploaded_course_commission += $row['total_earnings'];
                }
            }
           
            //Check main course sales
            $main_course_check = mysqli_query($conn, "SELECT SUM(affiliate_commission) AS total_commission FROM affiliate_course_sales_backup WHERE sellerID = '$affiliateID' AND sales_date BETWEEN '$one_day_ago' AND '$today' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
            $main_course_rows = mysqli_num_rows($main_course_check);
            if($main_course_rows > 0){ 
                while ($value = mysqli_fetch_assoc($main_course_check)) {
                    $main_course_commission += $value['total_commission'];
                }
            }
           
            //Prepare values
            $total_sales = (($uploaded_course_commission + $main_course_commission) / 10000) * 2;
            $total_sales_amount = number_format(($uploaded_course_commission + $main_course_commission), 2, '.', ',');
            $total_sales_amount_in_usd = number_format(((($uploaded_course_commission + $main_course_commission) / (1000))), 2, '.', ','); 
            
            //Prepare response
            $data = array(
                    'Info' => 'Details fetched',
                    'details' => array(
                      'sales' => $total_sales,
                      'amount' => '$' . $total_sales_amount_in_usd
                    )
                );
        break;
        case 'weekly':
             /**** Variables ****/
            $uploaded_course_commission = 0;
            $main_course_commission = 0;
            /****** End ******/
            $today = date('Y-m-d');
            $one_week_ago = date('Y-m-d', strtotime($today . ' - 7 days'));
            //Check uploaded course sales
            $uploaded_course_check = mysqli_query($conn, "SELECT SUM(affiliate_commission) AS total_earnings FROM uploaded_course_sales_backup WHERE sellerID = '$affiliateID' AND sales_date BETWEEN '$one_week_ago' AND '$today' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
            $uploaded_course_rows = mysqli_num_rows($uploaded_course_check);
            if($uploaded_course_rows > 0){ 
                while ($row = mysqli_fetch_assoc($uploaded_course_check)) {
                     $uploaded_course_commission += $row['total_earnings'];
                }
            }
           
            //Check main course sales
            $main_course_check = mysqli_query($conn, "SELECT SUM(affiliate_commission) AS total_commission FROM affiliate_course_sales_backup WHERE sellerID = '$affiliateID' AND sales_date BETWEEN '$one_week_ago' AND '$today' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
            $main_course_rows = mysqli_num_rows($main_course_check);
            if($main_course_rows > 0){ 
               while ($value = mysqli_fetch_assoc($main_course_check)) {
                    $main_course_commission += $value['total_commission'];
                }
            }
            
            //Prepare values
            $total_sales = (($uploaded_course_commission + $main_course_commission) / 10000) * 2;
            $total_sales_amount = number_format(($uploaded_course_commission + $main_course_commission), 2, '.', ',');
            $total_sales_amount_in_usd = number_format(((($uploaded_course_commission + $main_course_commission) / (1000))), 2, '.', ',');  
            
            //Prepare response
            $data = array(
                    'Info' => 'Details fetched',
                    'details' => array(
                      'sales' => $total_sales,
                      'amount' => '$' . $total_sales_amount_in_usd
                    )
                );
        break;
        case 'monthly':
             /**** Variables ****/
            $uploaded_course_commission = 0;
            $main_course_commission = 0;
            /****** End ******/
            $today = date('Y-m-d');
            $one_month_ago = date('Y-m-d', strtotime($today . ' - 1 month'));
            //Check uploaded course sales
            $uploaded_course_check = mysqli_query($conn, "SELECT SUM(affiliate_commission) AS total_earnings FROM uploaded_course_sales_backup WHERE sellerID = '$affiliateID' AND sales_date BETWEEN '$one_month_ago' AND '$today' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
            $uploaded_course_rows = mysqli_num_rows($uploaded_course_check);
            if($uploaded_course_rows > 0){ 
                while ($row = mysqli_fetch_assoc($uploaded_course_check)) {
                    $uploaded_course_commission += $row['total_earnings'];
                }
            }
           
            //Check main course sales
            $main_course_check = mysqli_query($conn, "SELECT SUM(affiliate_commission) AS total_commission FROM affiliate_course_sales_backup WHERE sellerID = '$affiliateID' AND sales_date BETWEEN '$one_month_ago' AND '$today' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
            $main_course_rows = mysqli_num_rows($main_course_check);
            if($main_course_rows > 0){ 
                while ($value = mysqli_fetch_assoc($main_course_check)) {
                    $main_course_commission += $value['total_commission'];
                }
            }
            
            //Prepare values
            $total_sales = (($uploaded_course_commission + $main_course_commission) / 10000) * 2;
            $total_sales_amount = number_format(($uploaded_course_commission + $main_course_commission), 2, '.', ',');
            $total_sales_amount_in_usd = number_format(((($uploaded_course_commission + $main_course_commission) / (1000))), 2, '.', ',');    
            
            //Prepare response
            $data = array(
                    'Info' => 'Details fetched',
                    'details' => array(
                      'sales' => $total_sales,
                      'amount' => '$' . $total_sales_amount_in_usd
                    )
                );
        break;
        case 'yearly':
            /**** Variables ****/
            $uploaded_course_commission = 0;
            $main_course_commission = 0;
            /****** End ******/
            $today = date('Y-m-d');
            $one_year_ago = date('Y-m-d', strtotime($today . ' - 1 year'));
            //Check uploaded course sales
            $uploaded_course_check = mysqli_query($conn, "SELECT SUM(affiliate_commission) AS total_earnings FROM uploaded_course_sales_backup WHERE sellerID = '$affiliateID' AND sales_date BETWEEN '$one_year_ago' AND '$today' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
            $uploaded_course_rows = mysqli_num_rows($uploaded_course_check);
            if($uploaded_course_rows > 0){ 
                while ($row = mysqli_fetch_assoc($uploaded_course_check)) {
                    $uploaded_course_commission += $row['total_earnings'];
                }
            }
           
            //Check main course sales
            $main_course_check = mysqli_query($conn, "SELECT SUM(affiliate_commission) AS total_commission FROM affiliate_course_sales_backup WHERE sellerID = '$affiliateID' AND sales_date BETWEEN '$one_year_ago' AND '$today' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
            $main_course_rows = mysqli_num_rows($main_course_check);
            if($main_course_rows > 0){ 
                while ($value = mysqli_fetch_assoc($main_course_check)) {
                    $main_course_commission += $value['total_commission'];
                }
            }
            
            //Prepare values
            $total_sales = (($uploaded_course_commission + $main_course_commission) / 10000) * 2;
            $total_sales_amount = number_format(($uploaded_course_commission + $main_course_commission), 2, '.', ',');
            $total_sales_amount_in_usd = number_format(((($uploaded_course_commission + $main_course_commission) / (1000))), 2, '.', ',');    
           
            //Prepare response
            $data = array(
                    'Info' => 'Details fetched',
                    'details' => array(
                      'sales' => $total_sales,
                      'amount' => '$' . $total_sales_amount_in_usd
                    )
                );
        break;
        case 'custom':
            /**** Variables ****/
            $uploaded_course_commission = 0;
            $main_course_commission = 0;
            /****** End ******/
            $from = mysqli_real_escape_string($conn, $_GET['from']);
            $to = mysqli_real_escape_string($conn, $_GET['to']); 
            //Check uploaded course sales
            $uploaded_course_check = mysqli_query($conn, "SELECT SUM(affiliate_commission) AS total_earnings FROM uploaded_course_sales_backup WHERE sellerID = '$affiliateID' AND sales_date BETWEEN '$from' AND '$to' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
            $uploaded_course_rows = mysqli_num_rows($uploaded_course_check);
            if($uploaded_course_rows > 0){ 
                while ($row = mysqli_fetch_assoc($uploaded_course_check)) {
                    $uploaded_course_commission += $row['total_earnings'];
                }
            }
           
            //Check main course sales
            $main_course_check = mysqli_query($conn, "SELECT SUM(affiliate_commission) AS total_commission FROM affiliate_course_sales_backup WHERE sellerID = '$affiliateID' AND sales_date BETWEEN '$from' AND '$to' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
            $main_course_rows = mysqli_num_rows($main_course_check);
            if($main_course_rows > 0){ 
               while ($value = mysqli_fetch_assoc($main_course_check)) {
                    $main_course_commission += $value['total_commission'];
                }
            }
            
            //Prepare values
            $total_sales = (($uploaded_course_commission + $main_course_commission) / 10000) * 2;
            $total_sales_amount = number_format(($uploaded_course_commission + $main_course_commission), 2, '.', ',');
            $total_sales_amount_in_usd = number_format(((($uploaded_course_commission + $main_course_commission) / (1000))), 2, '.', ','); 
            
            //Prepare response
            $data = array(
                    'Info' => 'Details fetched',
                    'details' => array(
                      'sales' => $total_sales,
                      'amount' => '$' . $total_sales_amount_in_usd
                    )
                );
        break;
    } 
}
else{  
    $data = array("Info" => "Some parameters are empty"); 
}

$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);
exit();
    
?>