<?php
/*
    Delete Category
    ------------------
    Deletes a category owned by the logged-in user.
    Any tasks using this category are NOT deleted; their
    category_id is simply set to NULL (see the database's
    ON DELETE SET NULL rule) so no task data is lost.
*/
require_once "includes/auth_check.php";
require_once "config/db.php";
require_once "includes/flash.php";

$userId = $_SESSION['user_id'];
$catId  = $_GET['id'] ?? '';

if ($catId !== '' && is_numeric($catId)) {
    $stmt = mysqli_prepare($conn, "DELETE FROM categories WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $catId, $userId);

    if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
        set_flash('success', "Category deleted. Tasks in it are now uncategorized.");
    } else {
        set_flash('error', "Category not found or already deleted.");
    }
    mysqli_stmt_close($stmt);
} else {
    set_flash('error', "Invalid category.");
}

header("Location: categories.php");
exit();
?>
