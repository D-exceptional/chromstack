<?php 
    session_start();
    unset($_SESSION['affiliateID']);
    header('Location: /login');
?>