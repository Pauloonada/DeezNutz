<?php 
    session_start();
    if(isset($_SESSION['user_id'])){
        $_SESSION['error'] = "You are already logged in.";
        header("Location: dashboard.php");
        die();
    }

    header("Location: login.php");
?>