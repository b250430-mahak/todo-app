<?php
/*
    Starts the PHP session if it is not already started.
    This file is included at the top of every page that
    needs to read or write session data.
*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
