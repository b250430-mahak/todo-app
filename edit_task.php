<?php
/*
    Edit Task Page
    -----------------
    Loads an existing task (only if it belongs to the logged-in
    user) and lets the user update it.
*/
require_once "includes/auth_check.php";
require_once "config/db.php";
require_once "includes/flash.php";

$userId = $_SESSION['user_id'];
$errors = [];

// Task id must be given in the URL, e.g. edit_task.php?id=5
$taskId = $_GET['id'] ?? $_POST['task_id'] ?? '';

if ($taskId === '' || !is_numeric($taskId)) {
    set_flash('error', "Invalid task.");
    header("Location: tasks.php");
    exit();
}
$taskId = (int) $taskId;

// Fetch the task, making sure it belongs to this user
$stmt = mysqli_prepare($conn, "SELECT * FROM tasks WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $taskId, $userId);
mysqli_stmt_execute($stmt);
$task = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$task) {
    set_flash('error', "Task not found.");
    header("Location: tasks.php");
    exit();
}

// Pre-fill values with the existing task (overwritten if form was submitted)
$title       = $task['title'];
$description = $task['description'] ?? '';
$priority    = $task['priority'];
$due_date    = $task['due_date'] ?? '';
$category_id = $task['category_id'];
$status      = $task['status'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority    = $_POST['priority'] ?? 'Medium';
    $due_date    = trim($_POST['due_date'] ?? '');
    $category_id = $_POST['category_id'] ?? '';
    $status      = $_POST['status'] ?? 'Pending';

    // ---------- Server-side validation ----------
    if ($title === '') {
        $errors[] = "Task title is required.";
    } elseif (strlen($title) > 150) {
        $errors[] = "Task title is too long (max 150 characters).";
    }

    if (!in_array($priority, ['High', 'Medium', 'Low'])) {
        $errors[] = "Invalid priority selected.";
    }

    if (!in_array($status, ['Pending', 'Completed'])) {
        $errors[] = "Invalid status selected.";
    }

    if ($due_date !== '') {
        $d = DateTime::createFromFormat('Y-m-d', $due_date);
        if (!$d || $d->format('Y-m-d') !== $due_date) {
            $errors[] = "Invalid due date format.";
        }
    }

    $categoryIdToSave = null;
    if ($category_id !== '') {
        $checkCat = mysqli_prepare($conn, "SELECT id FROM categories WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($checkCat, "ii", $category_id, $userId);
        mysqli_stmt_execute($checkCat);
        mysqli_stmt_store_result($checkCat);
        if (mysqli_stmt_num_rows($checkCat) === 1) {
            $categoryIdToSave = (int) $category_id;
        } else {
            $errors[] = "Invalid category selected.";
        }
        mysqli_stmt_close($checkCat);
    }

    if (empty($errors)) {
        $dueDateToSave = $due_date !== '' ? $due_date : null;

        $stmt = mysqli_prepare($conn, "UPDATE tasks
            SET title = ?, description = ?, priority = ?, due_date = ?, status = ?, category_id = ?
            WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, "sssssiii",
            $title, $description, $priority, $dueDateToSave, $status, $categoryIdToSave, $taskId, $userId);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            set_flash('success', "Task updated successfully.");
            header("Location: tasks.php");
            exit();
        } else {
            $errors[] = "Something went wrong while updating the task.";
        }
    }
}

// Get categories for the dropdown
$catStmt = mysqli_prepare($conn, "SELECT id, name FROM categories WHERE user_id = ? ORDER BY name");
mysqli_stmt_bind_param($catStmt, "i", $userId);
mysqli_stmt_execute($catStmt);
$categories = mysqli_stmt_get_result($catStmt);

$pageTitle = "Edit Task";
require_once "includes/header.php";
?>

<div class="card form-card">
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="edit_task.php?id=<?php echo $taskId; ?>" onsubmit="return validateTaskForm()" novalidate>
        <input type="hidden" name="task_id" value="<?php echo $taskId; ?>">

        <div class="form-group">
            <label for="title">Task Title *</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>">
            <small class="error-text" id="titleError"></small>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id">
                    <option value="">Uncategorized</option>
                    <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($category_id == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="priority">Priority</label>
                <select id="priority" name="priority">
                    <option value="High" <?php echo ($priority == 'High') ? 'selected' : ''; ?>>High</option>
                    <option value="Medium" <?php echo ($priority == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                    <option value="Low" <?php echo ($priority == 'Low') ? 'selected' : ''; ?>>Low</option>
                </select>
            </div>

            <div class="form-group">
                <label for="due_date">Due Date</label>
                <input type="date" id="due_date" name="due_date" value="<?php echo htmlspecialchars($due_date ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="Pending" <?php echo ($status == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="Completed" <?php echo ($status == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Task</button>
            <a href="tasks.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once "includes/footer.php"; ?>
