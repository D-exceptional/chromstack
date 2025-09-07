<?php 
    session_start();
    unset($_SESSION['vendorID']);
    header('Location: /login');
?>