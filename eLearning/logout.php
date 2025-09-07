<?php 
    $access_type = $_GET['access'];
    //$access_id = $_GET['accessID'];
    switch ($access_type) {
        case 'Admin':
            header("Location: https://chromstack.com/admin/pages/index.php");
        break;
        case 'Affiliate':
            header("Location: https://chromstack.com/affiliate/index.php");
        break;
        case 'User':
            header("Location: https://chromstack.com/login");
        break;
    }
?>

