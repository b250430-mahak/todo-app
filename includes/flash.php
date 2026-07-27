<?php
/*
    Flash Message Helper
    ----------------------
    Small helper functions to set and show one-time success/error
    messages after actions like add, edit, delete, login, etc.
*/

// Call this before redirecting to store a message for the next page
function set_flash($type, $message) {
    // $type should be 'success' or 'error'
    $_SESSION['flash_' . $type] = $message;
}

// Call this on the page that should display the message
function show_flash() {
    if (!empty($_SESSION['flash_success'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['flash_success']) . '</div>';
        unset($_SESSION['flash_success']);
    }
    if (!empty($_SESSION['flash_error'])) {
        echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['flash_error']) . '</div>';
        unset($_SESSION['flash_error']);
    }
}
?>
