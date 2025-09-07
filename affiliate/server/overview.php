<?php

require 'conn.php';
require 'format_number.php';

//Incoming variables
$affiliate_email = mysqli_real_escape_string($conn, $_GET['email']);
$date = date('Y-m-d');

//Prepare data & send to frontend
$data = array();

//Get affiliate ID
$sql = mysqli_query($conn, "SELECT affiliateID FROM affiliates WHERE email = '$affiliate_email'");
$row = mysqli_fetch_assoc($sql);
$affiliateID = $row['affiliateID'];

//Count affiliate course table
$query = mysqli_query($conn, "SELECT * FROM affiliate_program_course");
$affiliate_course_count = mysqli_num_rows($query);

//Count uploaded courses table
$query = mysqli_query($conn, "SELECT * FROM uploaded_courses WHERE course_status = 'Approved'");
$uploaded_course_count = mysqli_num_rows($query);

//Count all courses
$courseCount = $affiliate_course_count + $uploaded_course_count;

//Count sent mailbox table
$query = mysqli_query($conn, "SELECT * FROM mailbox WHERE mail_receiver = '$affiliate_email'");
$mailCount = mysqli_num_rows($query);

//Count contest table
$query = mysqli_query($conn, "SELECT * FROM contest WHERE contest_status = 'Active'");
$contestCount = mysqli_num_rows($query);

//Count sales table
$query = mysqli_query($conn, "SELECT * FROM affiliate_course_sales_backup WHERE sellerID = '$affiliateID' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
$affiliateSalesCount = mysqli_num_rows($query);

//Count sales table
$query = mysqli_query($conn, "SELECT * FROM uploaded_course_sales_backup WHERE sellerID = '$affiliateID' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
$salesCount = mysqli_num_rows($query);

$totalSales = $affiliateSalesCount + $salesCount;

//Count chats table
$query = mysqli_query($conn, "SELECT * FROM chats");
$chatCount = mysqli_num_rows($query);

//Count completed sales
$completed_uploaded_course_sales_query = mysqli_query($conn, "SELECT * FROM uploaded_course_sales WHERE sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_status = 'Completed'");
$completed_uploaded_course_sales_count = mysqli_num_rows($completed_uploaded_course_sales_query);
//Get total affiliate program course sold
$completed_affiliate_course_sales_query = mysqli_query($conn, "SELECT * FROM affiliate_course_sales WHERE sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_status = 'Completed'");
$completed_affiliate_course_sales_count = mysqli_num_rows($completed_affiliate_course_sales_query);
//Total courses sold
$total_completed_course_sales = number_format(($completed_uploaded_course_sales_count + $completed_affiliate_course_sales_count), 2, '.', ',');

//Count pending sales
$pending_uploaded_course_sales_query = mysqli_query($conn, "SELECT * FROM uploaded_course_sales WHERE sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_status = 'Pending'");
$pending_uploaded_course_sales_count = mysqli_num_rows($pending_uploaded_course_sales_query);
//Get total affiliate program course sold
$pending_affiliate_course_sales_query = mysqli_query($conn, "SELECT * FROM affiliate_course_sales WHERE sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_status = 'Pending'");
$pending_affiliate_course_sales_count = mysqli_num_rows($pending_affiliate_course_sales_query);
//Total courses sold
$total_pending_course_sales = number_format(($pending_uploaded_course_sales_count + $pending_affiliate_course_sales_count), 2, '.', ',');

//Today's sales
$today_main_course_sales = mysqli_query($conn, "SELECT * FROM affiliate_course_sales_backup WHERE sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_date = '$date' AND sales_status = 'Completed'");
$today_main_course_sales_count =  mysqli_num_rows($today_main_course_sales);
$today_uploaded_course_sales = mysqli_query($conn, "SELECT * FROM uploaded_course_sales_backup WHERE sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_date = '$date' AND sales_status = 'Completed'");
$today_uploaded_course_sales_count =  mysqli_num_rows($today_uploaded_course_sales);
$today_total_sales = $today_main_course_sales_count + $today_uploaded_course_sales_count;

//Today's total earnings
$today_affiliate_commission_amount = 0; 
$query = mysqli_query($conn, "SELECT SUM(affiliate_commission) AS commission_amount FROM affiliate_course_sales_backup WHERE sellerID = '$affiliateID' AND sales_date = '$date' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
if(mysqli_num_rows($query) > 0){ 
    $val = mysqli_fetch_assoc($query);
    $today_affiliate_commission_amount = $val['commission_amount'];
}
$today_course_commission_amount = 0; 
$query = mysqli_query($conn, "SELECT SUM(affiliate_commission) AS earning_amount FROM uploaded_course_sales_backup WHERE sellerID = '$affiliateID' AND sales_date = '$date' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
if(mysqli_num_rows($query) > 0){ 
    $val = mysqli_fetch_assoc($query);
    $today_course_commission_amount = $val['earning_amount'];
}
$today_total_earnings = number_format((($today_affiliate_commission_amount + $today_course_commission_amount) / 1000), 2, '.', ',');
$today_total_earnings_in_naira = intval($today_affiliate_commission_amount + $today_course_commission_amount);

//Earnings variable
$total_uploaded_course_earnings = 0;
$total_affiliate_course_earnings = 0;
$weekly_payout_amount = 0;
$weekly_payout_amount_in_usd = 0;
$wallet_amount = 0;
$wallet_savings = 0;
$wallet_savings_in_usd = 0;
$wallet_savings_in_naira = 0;

//Get wallet savings
$savings_query = mysqli_query($conn, "SELECT SUM(wallet_amount) AS wallet_savings FROM wallet WHERE wallet_email = '$affiliate_email' AND wallet_user = 'Affiliate'");
if(mysqli_num_rows($savings_query) > 0){
    $value = mysqli_fetch_assoc($savings_query);
    $calculated_savings = $value['wallet_savings'];
    $wallet_amount = $value['wallet_savings'];
    $wallet_savings = number_format(($calculated_savings), 2, '.', ',');
    $wallet_savings_in_usd = number_format(((($calculated_savings) / (1000))), 2, '.', ',');
    $wallet_savings_in_naira = intval($calculated_savings);
}
else{
    $wallet_savings = number_format((0), 2, '.', ',');
    $wallet_savings_in_usd = number_format((((0) / (1000))), 2, '.', ',');
}

//Overall earnings
$affiliate_commission_amount = 0; 
$query = mysqli_query($conn, "SELECT SUM(affiliate_commission) AS commission_amount FROM affiliate_course_sales_backup WHERE sellerID = '$affiliateID' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
if(mysqli_num_rows($query) > 0){ 
    $val = mysqli_fetch_assoc($query);
    $affiliate_commission_amount = $val['commission_amount'];
}
$course_commission_amount = 0; 
$query = mysqli_query($conn, "SELECT SUM(affiliate_commission) AS earning_amount FROM uploaded_course_sales_backup WHERE sellerID = '$affiliateID' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
if(mysqli_num_rows($query) > 0){ 
    $val = mysqli_fetch_assoc($query);
    $course_commission_amount = $val['earning_amount'];
}
$total_transaction_payments_in_usd = number_format(((($affiliate_commission_amount + $course_commission_amount) / (1000))), 2, '.', ',');
$total_transaction_payments_in_naira = intval($affiliate_commission_amount + $course_commission_amount);

//Get weekly sales details
$main_course_query = mysqli_query($conn, "SELECT * FROM affiliate_course_sales WHERE sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_status = 'Completed'");
if(mysqli_num_rows($main_course_query) > 0){ 
    $value = mysqli_fetch_assoc($main_course_query);
    $courseID = $value['courseID'];
    //Get fixed affiliate commission
    $commission_sql = mysqli_query($conn, "SELECT affiliate_percentage FROM affiliate_program_course");
    $result = mysqli_fetch_assoc($commission_sql);
    $fixed_affiliate_commission = substr($result['affiliate_percentage'], 0, -1);
    //Get unique sales amount
    $main_course_sales_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS affiliate_unique_commission FROM affiliate_course_sales WHERE sellerID = '$affiliateID' AND sales_type = 'Affiliate' AND sales_status = 'Completed' GROUP BY sellerID");
    $val = mysqli_fetch_assoc($main_course_sales_query);
    $calculated_affiliate_commission = $val['affiliate_unique_commission'];
    //Get unique earnings
    $total_affiliate_course_earnings = ($calculated_affiliate_commission) * ($fixed_affiliate_commission / 100);
}
else{
    $total_affiliate_course_earnings = 0;
}

$all_course_query = mysqli_query($conn, "SELECT * FROM uploaded_course_sales WHERE sales_type = 'Affiliate' AND sellerID = '$affiliateID' AND sales_status = 'Completed' GROUP BY courseID");
if(mysqli_num_rows($all_course_query) > 0){ 
    while ($value = mysqli_fetch_assoc($all_course_query)) {
        $courseID = $value['courseID'];
        //Get fixed affiliate commission
        $commission_sql = mysqli_query($conn, "SELECT affiliate_percentage FROM uploaded_courses WHERE courseID = '$courseID'");
        $result = mysqli_fetch_assoc($commission_sql);
        $fixed_affiliate_commission = substr($result['affiliate_percentage'], 0, -1);
        //Get unique sales amount
        $other_course_sales_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS affiliate_unique_commission FROM uploaded_course_sales WHERE courseID = '$courseID' AND sellerID = '$affiliateID' AND sales_type = 'Affiliate' AND sales_status = 'Completed'");
        $val = mysqli_fetch_assoc($other_course_sales_query);
        $calculated_affiliate_commission = $val['affiliate_unique_commission'];
        //Get unique earnings
        $total_uploaded_course_earnings += ($calculated_affiliate_commission) * ($fixed_affiliate_commission / 100);
    }
}
else{
    $total_uploaded_course_earnings = 0;
}

$weekly_payout_amount = number_format(($total_uploaded_course_earnings + $total_affiliate_course_earnings), 2, '.', ',');
$weekly_payout_amount_in_usd = number_format(((($total_uploaded_course_earnings + $total_affiliate_course_earnings) / (1000))), 2, '.', ',');

//Prepare data to send to front end
$data[] = array(
        'courseCount' => format_number($courseCount, 1),
        'mailCount' => format_number($mailCount, 1),
        'contestCount' => format_number($contestCount, 1),
        'salesCount' => format_number($salesCount, 1),
        'totalSales' => format_number($totalSales, 1),
        'affiliateSalesCount' => format_number($affiliateSalesCount, 1),
        //'totalAffiliateCourseEarningsInUSD' => $membership_commission_history,
        //'totalUploadedCourseEarningsInUSD' => $membership_earning_history,
        'totalWeeklySalesEarnings' => $weekly_payout_amount,
        'totalWeeklySalesEarningsInUSD' => $weekly_payout_amount_in_usd,
        'completedSales' => $total_completed_course_sales,
        'pendingSales' => $total_pending_course_sales,
        'overallEarnings' => $total_transaction_payments_in_usd,
        'walletSavings' => $wallet_savings_in_usd,
        'todaySales' => $today_total_sales,
        'todayEarnings' => $today_total_earnings,
        'overallEarningsInNaira' => $total_transaction_payments_in_naira,
        'walletSavingsInNaira' => $wallet_savings_in_naira,
        'todayEarningsInNaira' => $today_total_earnings_in_naira,
    );
        
$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);
exit();
   
?>
