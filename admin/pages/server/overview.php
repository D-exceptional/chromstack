<?php

require 'conn.php';
require 'format_number.php';

$email = $_GET['email'];

//Prepare data and send to frontend
$data = array();

//Count admins table
$query = mysqli_query($conn, "SELECT * FROM admins");
$adminCount = mysqli_num_rows($query);

//Count affiliates table
$query = mysqli_query($conn, "SELECT * FROM affiliates WHERE affiliate_status = 'Active'");
$affiliateCount = mysqli_num_rows($query);

//Count vendors table
$query = mysqli_query($conn, "SELECT * FROM vendors WHERE vendor_status = 'Active'");
$vendorCount = mysqli_num_rows($query);

//Count users table
$query = mysqli_query($conn, "SELECT * FROM users WHERE user_status = 'Active'");
$userCount = mysqli_num_rows($query);

//Count affiliate course table
$query = mysqli_query($conn, "SELECT * FROM affiliate_program_course");
$affiliate_course_count = mysqli_num_rows($query);

//Count uploaded courses table
$query = mysqli_query($conn, "SELECT * FROM uploaded_courses");
$uploaded_course_count = mysqli_num_rows($query);

//Count all courses
$courseCount = $affiliate_course_count + $uploaded_course_count;

//Count mailbox table
$query = mysqli_query($conn, "SELECT * FROM mail_listing");
$mailListCount = mysqli_num_rows($query);

//Count mail_listing table
$query = mysqli_query($conn, "SELECT * FROM mailbox WHERE mail_receiver = '$email'");
$mailCount = mysqli_num_rows($query);

//Counttickets table
$query = mysqli_query($conn, "SELECT * FROM tickets");
$ticket_count = mysqli_num_rows($query);

//Count contest table
$query = mysqli_query($conn, "SELECT * FROM contest WHERE contest_status = 'Active'");
$contestCount = mysqli_num_rows($query);

//Count overall main course sales table
$query = mysqli_query($conn, "SELECT * FROM affiliate_course_sales_backup WHERE sales_status = 'Completed'");
$overall_affiliate_course_sales = mysqli_num_rows($query);

//Count overall uploaded course sales table
$query = mysqli_query($conn, "SELECT * FROM uploaded_course_sales_backup WHERE sales_status = 'Completed'");
$overall_uploaded_course_sales = mysqli_num_rows($query);

//Overall earnings variables
$overall_affiliate_course_earnings = 0;
$overall_uploaded_course_earnings = 0;
$overall_earned_amount = 0;
$overall_earned_amount_in_usd = 0;

 //Overall main course earnings
 $affiliate_course_earnings_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS total_earnings FROM affiliate_course_sales_backup WHERE sales_status = 'Completed'");
 if (mysqli_num_rows($affiliate_course_earnings_query) > 0) {
    $value = mysqli_fetch_assoc($affiliate_course_earnings_query);
    $overall_earnings = $value['total_earnings'];
    $overall_affiliate_course_earnings = $overall_earnings;
 }
 else {
    $overall_affiliate_course_earnings = 0;
 }

  //Overall uploaded course earnings
  $uploaded_course_earnings_query = mysqli_query($conn, "SELECT SUM(sales_amount) AS total_earnings FROM uploaded_course_sales_backup WHERE sales_status = 'Completed'");
  if (mysqli_num_rows($uploaded_course_earnings_query) > 0) {
     $value = mysqli_fetch_assoc($uploaded_course_earnings_query);
     $overall_earnings = $value['total_earnings'];
     $overall_uploaded_course_earnings = $overall_earnings;
  }
  else {
     $overall_uploaded_course_earnings = 0;
  }

//Total payout amount
$overall_earned_amount = number_format((($overall_affiliate_course_earnings + $overall_uploaded_course_earnings)), 2, '.', ',');
$overall_earned_amount_in_usd = number_format(((($overall_affiliate_course_earnings + $overall_uploaded_course_earnings) / (1000))), 2, '.', ',');

//Get total weekly earnings in Naira and USD
$total_weekly_uploaded_course_earnings = 0;
$total_weekly_affiliate_course_earnings = 0;
$total_weekly_direct_registration_earnings = 0;

//Get weekly earnings from uploaded course sales table
$query = mysqli_query($conn, "SELECT SUM(sales_amount) AS total_earnings FROM uploaded_course_sales WHERE sales_status = 'Completed'");
if (mysqli_num_rows($query) > 0) {
    $result = mysqli_fetch_assoc($query);
    $totalAmount = $result['total_earnings'];
    $total_weekly_uploaded_course_earnings = $totalAmount;
    $weekly_uploaded_course_earnings = number_format($totalAmount, 2, '.', ',');
    $weekly_uploaded_course_earnings_in_usd = number_format(($totalAmount / 1000), 2, '.', ',');
}
else{
    $weekly_uploaded_course_earnings = 0;
    $weekly_uploaded_course_earnings_in_usd = 0;
}

//Get weekly earnings from main course sales table
$query = mysqli_query($conn, "SELECT SUM(sales_amount) AS total_earnings FROM affiliate_course_sales WHERE sales_status = 'Completed'");
if (mysqli_num_rows($query) > 0) {
    $result = mysqli_fetch_assoc($query);
    $totalAmount = $result['total_earnings'];
    $total_weekly_affiliate_course_earnings = $totalAmount;
    $weekly_affiliate_course_earnings = number_format($totalAmount, 2, '.', ',');
    $weekly_affiliate_course_earnings_in_usd = number_format(($totalAmount / 1000), 2, '.', ',');
}
else{
    $weekly_affiliate_course_earnings = 0;
    $weekly_affiliate_course_earnings_in_usd = 0;
}

//Get weekly earnings from membership_payment table
$query = mysqli_query($conn, "SELECT SUM(paid_amount) AS direct_registration_earnings FROM membership_payment WHERE payment_status = 'Completed'");
if (mysqli_num_rows($query) > 0) {
    $result = mysqli_fetch_assoc($query);
    $totalAmount = $result['direct_registration_earnings'];
    $total_weekly_direct_registration_earnings = $totalAmount;
}
else{
    $total_weekly_direct_registration_earnings = 0;
}

$total_weekly_sales_earnings = number_format(($total_weekly_uploaded_course_earnings + $total_weekly_affiliate_course_earnings + $total_weekly_direct_registration_earnings), 2, '.', ',');
$total_weekly_sales_earnings_in_usd = number_format((($total_weekly_uploaded_course_earnings + $total_weekly_affiliate_course_earnings + $total_weekly_direct_registration_earnings) / 1000), 2, '.', ',');

//Get wallet savings
$wallet_savings = 0;
$wallet_savings_in_usd = 0;
$savings_query = mysqli_query($conn, "SELECT SUM(wallet_amount) AS wallet_savings FROM wallet WHERE wallet_email = '$email' AND wallet_user = 'Admin'");
if(mysqli_num_rows($savings_query) > 0){
    $value = mysqli_fetch_assoc($savings_query);
    $calculated_savings = $value['wallet_savings'];
    $wallet_amount = $value['wallet_savings'];
    $wallet_savings = number_format(($calculated_savings), 2, '.', ',');
    $wallet_savings_in_usd = number_format(((($calculated_savings) / (1000))), 2, '.', ',');
}
else{
    $wallet_savings = number_format((0), 2, '.', ',');
    $wallet_savings_in_usd = number_format((((0) / (1000))), 2, '.', ',');
}

//Count chats table
$query = mysqli_query($conn, "SELECT * FROM chats");
$chatCount = mysqli_num_rows($query);

//Count general_notifications table
$query = mysqli_query($conn, "SELECT * FROM general_notifications");
$notificationCount = mysqli_num_rows($query);

//Count mail_listing table
$query = mysqli_query($conn, "SELECT * FROM mail_listing");
$listCount = mysqli_num_rows($query);

$data[] = array(
        'adminCount' => format_number($adminCount, 1),
        'affiliateCount' => format_number($affiliateCount, 1),
        'vendorCount' => format_number($vendorCount, 1),
        'userCount' => format_number($userCount, 1),
        'courseCount' => format_number($courseCount, 1),
        'mailCount' => format_number($mailCount, 1),
        'mailListCount' => format_number($mailListCount, 1),
        'contestCount' => format_number($contestCount, 1),
        'salesCount' => format_number($overall_uploaded_course_sales, 1),
        'affiliateSalesCount' => format_number($overall_affiliate_course_sales, 1),
        'totalUploadedCourseEarnings' => $weekly_uploaded_course_earnings,
        'totalAffiliateCourseEarnings' => $weekly_affiliate_course_earnings,
        'totalAffiliateCourseEarningsInUSD' => $weekly_affiliate_course_earnings_in_usd,
        'totalUploadedCourseEarningsInUSD' => $weekly_uploaded_course_earnings_in_usd,
        'chatCount' => format_number($chatCount, 1),
        'notificationCount' => format_number($notificationCount, 1),
        'listCount' => format_number($listCount, 1),
        'totalSalesEarnings' => $total_weekly_sales_earnings,
        'totalSalesEarningsInUSD' => $total_weekly_sales_earnings_in_usd,
        'overallEarnings' => $overall_earned_amount,
        'overallEarningsInUSD' => $overall_earned_amount_in_usd,
        'totalTickets' => $ticket_count,
        'walletBalance' => $wallet_savings,
        'walletBalanceInUSD' => $wallet_savings_in_usd,
        );
        
$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;
mysqli_close($conn);

?>
