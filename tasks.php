<?php

require_once "includes/auth_check.php";
require_once "config/db.php";

$userId = $_SESSION['user_id'];

$search       = trim($_GET['search'] ?? '');
$categoryId   = $_GET['category_id'] ?? '';
$statusFilter = $_GET['status'] ?? '';


$sql = "SELECT t.id, t.title, t.description, t.priority, t.status, t.due_date, c.name AS category_name
        FROM tasks t
        LEFT JOIN categories c ON t.category_id = c.id
        WHERE t.user_id = ?";

$types  = "i";
$params = [$userId];

if ($search !== '') {
    $sql .= " AND t.title LIKE ?";
    $types .= "s";
    $params[] = "%" . $search . "%";
}

if ($categoryId !== '') {
    $sql .= " AND t.category_id = ?";
    $types .= "i";
    $params[] = $categoryId;
}

if ($statusFilter !== '' && in_array($statusFilter, ['Pending', 'Completed'])) {
    $sql .= " AND t.status = ?";
    $types .= "s";
    $params[] = $statusFilter;
}

$sql .= " ORDER BY t.due_date IS NULL, t.due_date ASC, t.created_at DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$tasks = mysqli_stmt_get_result($stmt);

$catStmt = mysqli_prepare($conn, "SELECT id, name FROM categories WHERE user_id = ? ORDER BY name");
mysqli_stmt_bind_param($catStmt, "i", $userId);
mysqli_stmt_execute($catStmt);
$categories = mysqli_stmt_get_result($catStmt);

$pageTitle = "My Tasks";
require_once "includes/header.php";
?>

<div class="card filter-card">
    <form method="GET" action="tasks.php" class="filter-form">
        <input type="text" name="search" placeholder="Search tasks by title..." value="<?php echo htmlspecialchars($search); ?>" class="search-input">

        <select name="category_id" class="filter-select">
            <option value="">All Categories</option>
            <?php
                mysqli_data_seek($categories, 0);
                while ($cat = mysqli_fetch_assoc($categories)):
            ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo ($categoryId == $cat['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <select name="status" class="filter-select">
            <option value="">All Status</option>
            <option value="Pending" <?php echo ($statusFilter == 'Pending') ? 'selected' : ''; ?>>Pending</option>
            <option value="Completed" <?php echo ($statusFilter == 'Completed') ? 'selected' : ''; ?>>Completed</option>
        </select>

        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="tasks.php" class="btn btn-secondary">Reset</a>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3>All Tasks</h3>
        <a href="add_task.php" class="btn btn-small">+ Add Task</a>
    </div>

    <?php if (mysqli_num_rows($tasks) === 0): ?>
        <p class="empty-text">No tasks found. Try changing your filters or <a href="add_task.php">add a new task</a>.</p>
    <?php else: ?>
        <table class="task-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Priority</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($task = mysqli_fetch_assoc($tasks)): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($task['title']); ?></strong>
                            <?php if (!empty($task['description'])): ?>
                                <p class="task-desc"><?php echo htmlspecialchars($task['description'] ?? ''); ?></p>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($task['category_name'] ?? 'Uncategorized'); ?></td>
                        <td><span class="badge badge-<?php echo strtolower($task['priority']); ?>"><?php echo $task['priority']; ?></span></td>
                        <td><?php echo $task['due_date'] ? date('d M Y', strtotime($task['due_date'])) : '-'; ?></td>
                        <td><span class="badge badge-<?php echo strtolower($task['status']); ?>"><?php echo $task['status']; ?></span></td>
                        <td class="action-cell">
                            <a href="toggle_status.php?id=<?php echo $task['id']; ?>" class="action-link" title="Toggle status">
                                <?php echo $task['status'] === 'Pending' ? '&#9989;' : '&#8635;'; ?>
                            </a>
                            <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="action-link" title="Edit">&#9998;</a>
                            <a href="delete_task.php?id=<?php echo $task['id']; ?>" class="action-link delete-link" title="Delete" onclick="return confirmDelete('task')">&#128465;</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once "includes/footer.php"; ?>
