<?php
/*
    Logout Page
    -------------
    Destroys the session and sends the user back to login.
*/
require_once "includes/session_init.php";

$_SESSION = [];        // clear all session variables
session_destroy();     // destroy the session itself

session_start();       // start a fresh session so we can show a message
$_SESSION['flash_success'] = "You have been logged out successfully.";

header("Location: login.php");
exit();
?>
