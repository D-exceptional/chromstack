<?php

require 'conn.php';
require 'format_number.php';

// Sanitize incoming variables
$vendor_email = mysqli_real_escape_string($conn, $_GET['email']);
$vendor = mysqli_real_escape_string($conn, $_GET['name']);

// Initialize response data
$data = [];
$salesCount = $contestCount = 0;

// 1. Fetch all courseIDs by vendor
$authorCourses = [];
$result = mysqli_query($conn, "SELECT courseID FROM uploaded_courses WHERE course_authors = '$vendor' AND course_status = 'Approved'");
while ($row = mysqli_fetch_assoc($result)) {
    $authorCourses[] = $row['courseID'];
}
$courseCount = count($authorCourses);

// 2. Count sent mails
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM mailbox WHERE mail_receiver = '$vendor_email'");
$sentMailCount = mysqli_fetch_assoc($result)['count'] ?? 0;

// 3. Count external active contests
if (!empty($authorCourses)) {
    $idList = "'" . implode("','", $authorCourses) . "'";
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM contest WHERE courseID IN ($idList) AND contest_status = 'Active' AND course_type = 'External'");
    $contestCount = mysqli_fetch_assoc($result)['count'] ?? 0;
}

// 4. Count sales from uploaded_course_sales_backup
if (!empty($authorCourses)) {
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM uploaded_course_sales_backup WHERE courseID IN ($idList) AND sales_status = 'Completed'");
    $salesCount = mysqli_fetch_assoc($result)['count'] ?? 0;
}

// 5. Count total chats
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM chats");
$chatCount = mysqli_fetch_assoc($result)['count'] ?? 0;

// 6. Wallet savings in USD
$wallet_savings_in_usd = '0.00';
$result = mysqli_query($conn, "SELECT SUM(wallet_amount) AS total FROM wallet WHERE wallet_email = '$vendor_email' AND wallet_user = 'Vendor'");
if ($row = mysqli_fetch_assoc($result)) {
    $wallet_savings_in_usd = number_format(floatval($row['total']) / 1000, 2, '.', ',');
}

// 7. Weekly earnings (active sales)
$weeklySalesEarningsInUSD = '0.00';
if (!empty($authorCourses)) {
    $result = mysqli_query($conn, "SELECT SUM(sales_amount) AS total FROM uploaded_course_sales WHERE courseID IN ($idList) AND sales_status = 'Completed'");
    $amount = mysqli_fetch_assoc($result)['total'] ?? 0;
    $weeklySalesEarningsInUSD = number_format($amount / 1000, 2, '.', ',');
}

// 8. Overall earnings (backup sales)
$overallEarningsInUSD = '0.00';
if (!empty($authorCourses)) {
    $result = mysqli_query($conn, "SELECT SUM(sales_amount) AS total FROM uploaded_course_sales_backup WHERE courseID IN ($idList) AND sales_status = 'Completed'");
    $amount = mysqli_fetch_assoc($result)['total'] ?? 0;
    $overallEarningsInUSD = number_format($amount / 1000, 2, '.', ',');
}

// 9. Compile and send data
$data[] = [
    'courseCount' => format_number($courseCount, 1),
    'sentMail' => format_number($sentMailCount, 1),
    'contestCount' => format_number($contestCount, 1),
    'salesCount' => format_number($salesCount, 1),
    'chatCount' => format_number($chatCount, 1),
    'totalSalesEarningsInUSD' => $weeklySalesEarningsInUSD,
    'overallEarnings' => $overallEarningsInUSD,
    'walletSavings' => $wallet_savings_in_usd
];

echo json_encode($data, JSON_FORCE_OBJECT);
mysqli_close($conn);

?>
