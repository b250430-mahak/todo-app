<?php


$host    = "localhost";   
$db_user = "root";        
$db_pass = "";            
$db_name = "todo_db";      


$conn = mysqli_connect($host, $db_user, $db_pass, $db_name);


if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}


mysqli_set_charset($conn, "utf8mb4");
?>
