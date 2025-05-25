<?php

    function firstPassword($username){
        $name3 = substr($username, 0, 3);   // 3 primeiras letras do nome
        $randNum = rand(100, 999);          // número aleatório
        $curYear = date("y");               // ano atual (últimos 2 dígitos)
        
        return ucfirst($name3) . $randNum . $curYear;
    }

    function showAlert(){
    if (isset($_SESSION['success']) || isset($_SESSION['error'])) {
        $type = isset($_SESSION['success']) ? 'success' : 'danger';
        $message = $_SESSION['success'] ?? $_SESSION['error'];

        echo "
        <div class='alert alert-$type alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 z-3 shadow' role='alert' style='width: 90%; max-width: 600px;'>
            <strong>" . ucfirst($type) . ":</strong> $message
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";

        unset($_SESSION['success'], $_SESSION['error']);
        }
    }

    function checkLogin(){
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "Please log in first.";
            header("Location: login.php");
            die();
        }

        if (isset($_SESSION['login_count']) && $_SESSION['login_count'] == 0) {
            $_SESSION['error'] = "You need to change your password before accessing any pages.";
            header("Location: change_password.php");
            die();
        }
    }