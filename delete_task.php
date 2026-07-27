<?php
/*
    Delete Task
    -------------
    Deletes a task that belongs to the logged-in user.
    Confirmation is handled on the client side (JS confirm dialog)
    before this link is followed.
*/
require_once "includes/auth_check.php";
require_once "config/db.php";
require_once "includes/flash.php";

$userId = $_SESSION['user_id'];
$taskId = $_GET['id'] ?? '';

if ($taskId !== '' && is_numeric($taskId)) {
    $stmt = mysqli_prepare($conn, "DELETE FROM tasks WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $taskId, $userId);

    if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
        set_flash('success', "Task deleted successfully.");
    } else {
        set_flash('error', "Task not found or already deleted.");
    }
    mysqli_stmt_close($stmt);
} else {
    set_flash('error', "Invalid task.");
}

header("Location: tasks.php");
exit();
?>
