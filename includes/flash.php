<?php



function set_flash($type, $message) {
    
    $_SESSION['flash_' . $type] = $message;
}


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
