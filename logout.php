<?php
require_once "includes/session_init.php";

$_SESSION = [];
session_destroy();

session_start();
$_SESSION['flash_success'] = "You have been logged out successfully.";

header("Location: login.php");
exit();
?>
