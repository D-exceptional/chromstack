<?php 
    session_start();
    unset($_SESSION['adminID']);
    header('Location: /admin/index');
?>