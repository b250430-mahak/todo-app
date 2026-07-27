<?php
$host     = "mysql-30e07e41-todo-app123.h.aivencloud.com";
$port     = 20096;
$user     = "avnadmin";
$password = "AVNS_D9rniLXb-ZJ3fotim-D";
$dbname   = "defaultdb";

$conn = mysqli_connect($host, $user, $password, $dbname, $port);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>
