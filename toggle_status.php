<?php

require_once "includes/auth_check.php";
require_once "config/db.php";
require_once "includes/flash.php";

$userId = $_SESSION['user_id'];
$taskId = $_GET['id'] ?? '';

if ($taskId !== '' && is_numeric($taskId)) {
    $stmt = mysqli_prepare($conn, "SELECT status FROM tasks WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $taskId, $userId);
    mysqli_stmt_execute($stmt);
    $task = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($task) {
        $newStatus = ($task['status'] === 'Pending') ? 'Completed' : 'Pending';

        $update = mysqli_prepare($conn, "UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($update, "sii", $newStatus, $taskId, $userId);
        mysqli_stmt_execute($update);
        mysqli_stmt_close($update);

        set_flash('success', "Task marked as $newStatus.");
    } else {
        set_flash('error', "Task not found.");
    }
} else {
    set_flash('error', "Invalid task.");
}

$redirectTo = $_SERVER['HTTP_REFERER'] ?? 'tasks.php';
header("Location: $redirectTo");
exit();
?>
