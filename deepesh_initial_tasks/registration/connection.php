<?php
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "deepesh_initial_tasks";

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_errno) {
        echo "Database connection error " . $conn->connect_error . "<br/>";
        return false;
    }
?>