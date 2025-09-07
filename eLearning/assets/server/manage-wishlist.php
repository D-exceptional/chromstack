<?php

// Set the time zone to Africa/Lagos
date_default_timezone_set("Africa/Lagos");

// Include database connection
//require 'conn.php';

// Database connection parameters
$hostname = 'localhost'; // Usually 'localhost' if the database is on the same server
$username = 'chroayol_root'; // Your MySQL username
$password = 'MySQLUser'; // Your MySQL password
$database = 'chroayol_store'; // The database you want to connect to

// Create a new mysqli object
$conn = new mysqli($hostname, $username, $password, $database);

// Check for connection errors
if ($conn->connect_error) {
    die('Connect Error (' . $conn->connect_errno . ') ' . $conn->connect_error);
}

// Define response array
$response = array();

// Handle AJAX request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input data
    $action = isset($_POST['action']) ? htmlspecialchars(trim($_POST['action'])) : '';
    $userID = isset($_POST['userid']) ? htmlspecialchars(trim($_POST['userid'])) : '';
    $userType = isset($_POST['usertype']) ? htmlspecialchars(trim($_POST['usertype'])) : '';
    $courseID = isset($_POST['courseid']) ? htmlspecialchars(trim($_POST['courseid'])) : '';
    $courseType = isset($_POST['coursetype']) ? htmlspecialchars(trim($_POST['coursetype'])) : '';
    $courseTitle = isset($_POST['coursetitle']) ? htmlspecialchars(trim($_POST['coursetitle'])) : '';
    $filename = isset($_POST['filename']) ? htmlspecialchars(trim($_POST['filename'])) : '';
    $wishlistID = isset($_POST['wishlist']) ? htmlspecialchars(trim($_POST['wishlist'])) : '';

    // Check if required parameters are present
    if (!empty($action) && !empty($userID) && !empty($userType) && !empty($courseID) && !empty($courseType) && !empty($courseTitle) && !empty($filename) && !empty($wishlistID)) {
        switch ($action) {
            case 'obtain':
                $response = getWishlist($userID, $userType, $courseID, $courseType, $courseTitle, $wishlistID);
            break;
            case 'track':
                $response = updateWishlist($filename, $wishlistID, $courseTitle, $userID, $userType, $courseType);
            break;
        }
    } else {
        $response = errorResponse('Missing parameters.');
    }
} else {
    $response = errorResponse('Invalid request method.');
}

// Encode response array to JSON and output
echo json_encode($response);

// Close database connection
$conn->close();

// Function to retrieve wishlist information
function getWishlist($userID, $userType, $courseID, $courseType, $courseTitle, $wishlistID) {
    global $conn;
    $response = array();

    // Prepare and execute query to check if wishlist exists
    $stmt = $conn->prepare("SELECT wishlistID FROM wishlist WHERE user_id = ? AND user_type = ? AND course_title = ?");
    $stmt->bind_param("sss", $userID, $userType, $courseTitle);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $wishlistID = $row['wishlistID'];
            // Update wishlist with course contents
            updateWishlistContents($courseID, $courseType, $wishlistID);
            $response = successResponse('Wishlist Found', array('id' => $wishlistID));
        } else {
            // Create new wishlist and update with course contents
            $wishlistID = createWishlist($courseID, $courseType, $courseTitle, $userID, $userType);
            if ($wishlistID) {
                $response = successResponse('Wishlist Created', array('id' => $wishlistID));
            } else {
                $response = errorResponse('Failed to create wishlist.');
            }
        }
    } else {
        $response = errorResponse('Database error.');
    }

    return $response;
}

// Function to create a new wishlist
function createWishlist($courseID, $courseType, $courseTitle, $userID, $userType) {
    global $conn;
    // Prepare and execute insert query for new wishlist record
    $stmt = $conn->prepare("INSERT INTO wishlist (course_title, course_status, course_progress, user_id, user_type) VALUES (?, 'Pending', 0, ?, ?)");
    $stmt->bind_param("sss", $courseTitle, $userID, $userType);
    if ($stmt->execute()) {
        // Retrieve wishlist ID
        $stmt = $conn->prepare("SELECT wishlistID FROM wishlist WHERE user_id = ? AND user_type = ? AND course_title = ?");
        $stmt->bind_param("sss", $userID, $userType, $courseTitle);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $wishlistID = $row['wishlistID'];
            // Update wishlist with course contents
            updateWishlistContents($courseID, $courseType, $wishlistID);
            return $wishlistID;
        }
    }
    return false;
}

// Function to update wishlist with course contents
function updateWishlistContents($courseID, $courseType, $wishlistID) {
    global $conn;
    $basePath = "../../courses/";

    // Get folder path based on course type
    switch ($courseType) {
        case 'Affiliate':
            $stmt = $conn->prepare("SELECT folder_path FROM affiliate_program_course WHERE courseID = ?");
            break;
        case 'Admin':
        case 'External':
            $stmt = $conn->prepare("SELECT folder_path FROM uploaded_courses WHERE courseID = ?");
            break;
        default:
            return; // Invalid course type
    }
    $stmt->bind_param("s", $courseID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $folderResult = $result->fetch_assoc();
        if ($folderResult) {
            $folderPath = $basePath . $folderResult['folder_path'];
            // Loop through course contents and update wishlist tracking
            if (is_dir($folderPath)) {
                $subfolders = glob($folderPath . '/*', GLOB_ONLYDIR);
                foreach ($subfolders as $folder) {
                    $files = glob($folder . '/*');
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            $fileName = pathinfo($file, PATHINFO_FILENAME);
                            $stmt = $conn->prepare("SELECT * FROM wishlist_tracking WHERE track_filename = ? AND wishlistID = ?");
                            $stmt->bind_param("ss", $fileName, $wishlistID);
                            $stmt->execute();
                            $check = $stmt->get_result();
                            if ($check && $check->num_rows === 0) {
                                $stmt = $conn->prepare("INSERT INTO wishlist_tracking (track_filename, track_status, wishlistID) VALUES (?, 'Pending', ?)");
                                $stmt->bind_param("ss", $fileName, $wishlistID);
                                $stmt->execute();
                            }
                        }
                    }
                }
            }
        }
    }
}

// Function to update wishlist item status
function updateWishlist($filename, $wishlistID, $courseTitle, $userID, $userType, $courseType) {
    global $conn;
    $response = array();
    // Check if item exists in wishlist
    $stmt = $conn->prepare("SELECT track_status FROM wishlist_tracking WHERE track_filename = ? AND wishlistID = ?");
    $stmt->bind_param("ss", $filename, $wishlistID);
    $stmt->execute();
    $itemQuery = $stmt->get_result();
    if ($itemQuery) {
        if ($itemQuery->num_rows > 0) {
            $row = $itemQuery->fetch_assoc();
            $status = $row['track_status'];
            if ($status === 'Pending') {
                $stmt = $conn->prepare("UPDATE wishlist_tracking SET track_status = 'Completed' WHERE track_filename = ? AND wishlistID = ?");
                $stmt->bind_param("ss", $filename, $wishlistID);
                if ($stmt->execute()) {
                    // Calculate course progress
                    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM wishlist_tracking WHERE wishlistID = ?");
                    $stmt->bind_param("s", $wishlistID);
                    $stmt->execute();
                    $itemsCountQuery = $stmt->get_result();

                    $stmt = $conn->prepare("SELECT COUNT(*) AS completed FROM wishlist_tracking WHERE wishlistID = ? AND track_status = 'Completed'");
                    $stmt->bind_param("s", $wishlistID);
                    $stmt->execute();
                    $completedItemsQuery = $stmt->get_result();

                    if ($itemsCountQuery && $completedItemsQuery) {
                        $itemsCountResult = $itemsCountQuery->fetch_assoc();
                        $completedItemsResult = $completedItemsQuery->fetch_assoc();
                        $progress = ($itemsCountResult['completed'] / $itemsCountResult['total']) * 100;
                        // Update course progress
                        $stmt = $conn->prepare("UPDATE wishlist SET course_progress = ? WHERE wishlistID = ?");
                        $stmt->bind_param("ds", $progress, $wishlistID);
                        if ($stmt->execute()) {
                            // Send certification email if course progress is 100%
                            if ($progress === 100) {
                                sendCertificationEmail($userID, $userType, $courseTitle);
                                $response = successResponse('Course Completion', array('message' => "You have successfully completed the course, $courseTitle! A certificate of course completion has been sent to your email address. Well done"));
                            } else {
                                $response = successResponse('Update Successful', array('message' => 'Course progress saved successfully'));
                            }
                        } else {
                            $response = errorResponse('Failed to update course progress.');
                        }
                    } else {
                        $response = errorResponse('Failed to retrieve items count.');
                    }
                } else {
                    $response = errorResponse('Error occurred while progress was being saved.');
                }
            } else {
                $response = errorResponse('Item status is not pending.');
            }
        } else {
            $response = errorResponse('The specified item does not exist.');
        }
    } else {
        $response = errorResponse('Database error.');
    }

    return $response;
}

// Function to send certification email
function sendCertificationEmail($userID, $userType, $courseTitle) {
    global $conn;
    $email = '';
    $fullname = '';
    // Get email
    switch ($userType) {
        case 'Admin':
            $stmt = $conn->prepare("SELECT email, fullname FROM admins WHERE adminID = ?");
            break;
        case 'Affiliate':
            $stmt = $conn->prepare("SELECT email, fullname FROM affiliates WHERE affiliateID = ?");
            break;
        case 'User':
            $stmt = $conn->prepare("SELECT email, fullname FROM users WHERE userID = ?");
            break;
    }
    $stmt->bind_param("s", $userID);
    $stmt->execute();
    $sql = $stmt->get_result();
    if ($sql) {
        $row = $sql->fetch_assoc();
        if ($row) {
            $email = $row['email'];
            $fullname = $row['fullname'];
            // Send email
            $subject = "Successful Course Completion";
            $link = '#'; // Link to certification if applicable
            $text = 'Congratulations';
            $message = "
                Congratulations, $fullname!  <br>
                You have successfully completed the course: <b>$courseTitle</b>  <br>
                We are glad you made it this far. <br>
                Below is your certification of completion.  <br>
                Best wishes from the Chromstack team! <br>
                <br>
                <br>
                <a href='$link' target='_blank'><b>$text</b></a>
            ";
            require 'mailer.php';
            send_email($subject, $email, $message);
        }
    }
}

// Function to generate success response
function successResponse($info, $details = array()) {
    return array('Info' => $info, 'details' => $details);
}

// Function to generate error response
function errorResponse($error) {
    return array('Info' => 'Error', 'details' => array('error' => $error));
}
?>
