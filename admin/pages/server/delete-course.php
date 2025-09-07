<?php

require 'conn.php';

function deleteChildFolderContents($parentFolder, $childFolder) {
    $folderPath = rtrim($parentFolder, '/') . '/' . rtrim($childFolder, '/') . '/';

    // Delete all files in the folder
    $files = glob($folderPath . '*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        } elseif (is_dir($file)) {
            // Recursively delete subdirectories
            deleteChildFolderContents($folderPath, basename($file));
        }
    }

    // Delete the folder itself
    if (is_dir($folderPath)) {
        rmdir($folderPath);
    }
}

$data = array();

$courseID = mysqli_real_escape_string($conn, $_POST['courseID']);
$sql = mysqli_query($conn, "SELECT * FROM uploaded_courses WHERE courseID = '$courseID'");

if (mysqli_num_rows($sql) > 0) {
    $row = mysqli_fetch_assoc($sql);
    $parentFolder = "../../../courses/";
    $childFolder = $row['folder_path'];
    $fullPath = $parentFolder . $childFolder;

    if (is_dir($fullPath)) {
        deleteChildFolderContents($parentFolder, $childFolder);

        $query = mysqli_query($conn, "DELETE FROM uploaded_courses WHERE courseID = '$courseID'");
        if ($query) {
            $data = array('Info' => 'Course deleted successfully');
        } else {
            $data = array('Info' => 'Error deleting course');
        }
    } else {
        $data = array('Info' => 'Course directory not found');
    }
} else {
    $data = array('Info' => 'Course does not exist');
}

$encodedData = json_encode($data, JSON_FORCE_OBJECT);
echo $encodedData;

mysqli_close($conn);

?>
