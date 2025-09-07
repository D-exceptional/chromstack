<?php
// Function to check if the browser is Google Chrome
function isChrome() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    // Ensure the user-agent contains 'Chrome', does not contain 'Edge', 'Chromium', or 'Safari'
    return (strpos($userAgent, 'Chrome') !== false && strpos($userAgent, 'Edge') === false && strpos($userAgent, 'Chromium') === false && strpos($userAgent, 'Safari') === false);
}

// If the browser is not Chrome, redirect or show a warning
/*if (!isChrome()) {
    // Redirect the user to a different page or show a message
    switch ($access_type) {
        case 'Admin':
            echo "<script> window.location = 'https://chromstack.com/admin/pages/index.php'; </script>";
            exit("This site can only be viewed using Google Chrome. Please switch to Chrome to continue.");
        break;
        case 'Affiliate':
            echo "<script> window.location = 'https://chromstack.com/affiliate/index.php'; </script>";
            exit("This site can only be viewed using Google Chrome. Please switch to Chrome to continue.");
        break;
        case 'User':
            echo "<script> window.location = 'https://chromstack.com/login'; </script>";
            exit("This site can only be viewed using Google Chrome. Please switch to Chrome to continue.");
        break;
    }
}*/

// Define default allowed emails
$default_allowed_emails = ['ifeomaagatha366@gmail.com', 'ugwunjezeokwuchukwu@gmail.com', 'nneamekauzoma@gmail.com', 'peculiarnjoku831@gmail.com'];

// Add admin emails
$sql = $conn->prepare("SELECT email FROM admins");
$sql->execute();
$result = $sql->get_result();
while ($row = $result->fetch_assoc()) {
    array_push($default_allowed_emails, $row['email']);
}

// Add affiliate emails
$sql = $conn->prepare("SELECT email FROM affiliates WHERE created_on BETWEEN '2023-06-01 00:00:00' AND '2024-04-07 16:20:00'");
$sql->execute();
$result = $sql->get_result();
while ($row = $result->fetch_assoc()) {
    array_push($default_allowed_emails, $row['email']);
}

// Define variables
$email = '';
$profile = '';
$fullname = '';
$contact = '';
$country = '';
$enrolled_courses = '';
$user_profile = '';
$membership_type = '';
$facebook_link = 'Facebook link unavailable';
$twitter_link = 'Twitter link unavailable';
$instagram_link = 'Instagram link unavailable';
$tiktok_link = 'TikTok link unavailable';

// Check access based on the user type
switch ($access_type) {
    case 'Admin':
        $admin_id = $access_id;
        // Prepare and execute query
        $stmt = $conn->prepare("SELECT * FROM admins WHERE adminID = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $profile = $row['admin_profile'];
        $email = $row['email'];
        $fullname = $row['fullname'];
        $contact = $row['contact'];
        $country = $row['country'];
        $membership_type = 'Admin';

        // Check links
        if (!empty($row['facebook_link'])) $facebook_link = $row['facebook_link'];
        if (!empty($row['twitter_link'])) $twitter_link = $row['twitter_link'];
        if (!empty($row['instagram_link'])) $instagram_link = $row['instagram_link'];
        if (!empty($row['tiktok_link'])) $tiktok_link = $row['tiktok_link'];

        // Check if this person is assigned the main course by default
        if (in_array($email, $default_allowed_emails)) {
            $course_query = $conn->prepare("SELECT * FROM purchased_courses WHERE buyer_email = ? AND purchase_status = 'Completed'");
            $course_query->bind_param("s", $email);
            $course_query->execute();
            $course_result = $course_query->get_result();
            $enrolled_courses = $course_result->num_rows + 1;
        }
    break;

    case 'Affiliate':
        $affiliate_id = $access_id;
        // Prepare and execute query
        $stmt = $conn->prepare("SELECT * FROM affiliates WHERE affiliateID = ?");
        $stmt->bind_param("i", $affiliate_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $profile = $row['affiliate_profile'];
        $email = $row['email'];
        $fullname = $row['fullname'];
        $contact = $row['contact'];
        $country = $row['country'];
        $membership_type = 'Affiliate';

        // Check if this person is assigned the main course by default
        if (in_array($email, $default_allowed_emails)) {
            $course_query = $conn->prepare("SELECT * FROM purchased_courses WHERE buyer_email = ? AND purchase_status = 'Completed'");
            $course_query->bind_param("s", $email);
            $course_query->execute();
            $course_result = $course_query->get_result();
            $enrolled_courses = $course_result->num_rows + 1;
        } else {
            // Check if this person has bought a course
            $course_query = $conn->prepare("SELECT * FROM purchased_courses WHERE buyer_email = ? AND purchase_status = 'Completed'");
            $course_query->bind_param("s", $email);
            $course_query->execute();
            $course_result = $course_query->get_result();
            $enrolled_courses = $course_result->num_rows;
            if ($enrolled_courses <= 0) {
                echo "<script> window.location = 'https://chromstack.com/affiliate/index.php'; </script>";
                exit;
            }
        }

        // Check links
        if (!empty($row['facebook_link'])) $facebook_link = $row['facebook_link'];
        if (!empty($row['twitter_link'])) $twitter_link = $row['twitter_link'];
        if (!empty($row['instagram_link'])) $instagram_link = $row['instagram_link'];
        if (!empty($row['tiktok_link'])) $tiktok_link = $row['tiktok_link'];
    break;

    case 'User':
        $user_id = $access_id;
        // Prepare and execute query
        $stmt = $conn->prepare("SELECT * FROM users WHERE userID = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $profile = $row['user_profile'];
        $email = $row['email'];
        $fullname = $row['fullname'];
        $contact = $row['contact'];
        $country = $row['country'];
        $membership_type = 'User';

        // Check if this person has bought a course
        $course_query = $conn->prepare("SELECT * FROM purchased_courses WHERE buyer_email = ? AND purchase_status = 'Completed'");
        $course_query->bind_param("s", $email);
        $course_query->execute();
        $course_result = $course_query->get_result();
        $enrolled_courses = $course_result->num_rows;
        if ($enrolled_courses <= 0) {
            echo "<script> window.location = 'https://chromstack.com/login'; </script>";
            exit;
        }
    break;
}

// Function definition for time ago
function timeAgo($time_ago) {
    $time_ago = new DateTime($time_ago);
    $current_time = new DateTime();
    $interval = $time_ago->diff($current_time);

    if ($interval->y > 0) {
        return $interval->y . " years ago";
    }
    if ($interval->m > 0) {
        return $interval->m . " months ago";
    }
    if ($interval->d > 0) {
        return $interval->d . " days ago";
    }
    if ($interval->h > 0) {
        return $interval->h . " hours ago";
    }
    if ($interval->i > 0) {
        return $interval->i . " minutes ago";
    }
    return "Just now";
}
?>
