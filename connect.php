<?php
    $database = "db_deezNutz";
    $host = "localhost";
    $user ="root";
    $pass = "";

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    try{
        $conn = new mysqli($host, $user, $pass, $database);
        $conn->set_charset("utf8mb4");
    }
    catch (mysqli_sql_exception $error) {
        die("Connection failed: " . $error->getMessage());
    }
?>