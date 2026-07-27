<?php
/*
    Entry point of the application.
    Sends the visitor to the dashboard if logged in,
    otherwise sends them to the login page.
*/
require_once "includes/session_init.php";

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
} else {
    header("Location: login.php");
}
exit();
?>
