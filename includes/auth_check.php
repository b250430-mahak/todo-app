<?php
/*
    Authentication Guard
    ---------------------
    Include this file at the very top of any page that should
    only be visible to logged-in users. It checks the session
    and redirects to the login page if the user is not logged in.
*/
require_once "includes/session_init.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
