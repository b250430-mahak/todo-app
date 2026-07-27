<?php
require_once "includes/auth_check.php";
require_once "config/db.php";

$userId = $_SESSION['user_id'];

$stmt = mysqli_prepare($conn, "SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) AS completed,
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending
    FROM tasks WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$counts = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

$totalTasks     = $counts['total'] ?? 0;
$completedTasks = $counts['completed'] ?? 0;
$pendingTasks   = $counts['pending'] ?? 0;

$stmt = mysqli_prepare($conn, "SELECT t.id, t.title, t.priority, t.status, t.due_date, c.name AS category_name
    FROM tasks t
    LEFT JOIN categories c ON t.category_id = c.id
    WHERE t.user_id = ?
    ORDER BY t.created_at DESC
    LIMIT 5");
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$recentTasks = mysqli_stmt_get_result($stmt);

$pageTitle = "Dashboard";
require_once "includes/header.php";
?>

<div class="stats-grid">
    <div class="stat-card stat-total">
        <div class="stat-icon">&#128203;</div>
        <div class="stat-info">
            <span class="stat-number"><?php echo $totalTasks; ?></span>
            <span class="stat-label">Total Tasks</span>
        </div>
    </div>

    <div class="stat-card stat-completed">
        <div class="stat-icon">&#9989;</div>
        <div class="stat-info">
            <span class="stat-number"><?php echo $completedTasks; ?></span>
            <span class="stat-label">Completed</span>
        </div>
    </div>

    <div class="stat-card stat-pending">
        <div class="stat-icon">&#9203;</div>
        <div class="stat-info">
            <span class="stat-number"><?php echo $pendingTasks; ?></span>
            <span class="stat-label">Pending</span>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Recent Tasks</h3>
        <a href="tasks.php" class="btn btn-small">View All</a>
    </div>

    <?php if (mysqli_num_rows($recentTasks) === 0): ?>
        <p class="empty-text">You have no tasks yet. <a href="add_task.php">Add your first task</a>.</p>
    <?php else: ?>
        <table class="task-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Priority</th>
                    <th>Due Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($task = mysqli_fetch_assoc($recentTasks)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($task['title']); ?></td>
                        <td><?php echo htmlspecialchars($task['category_name'] ?? 'Uncategorized'); ?></td>
                        <td><span class="badge badge-<?php echo strtolower($task['priority']); ?>"><?php echo $task['priority']; ?></span></td>
                        <td><?php echo $task['due_date'] ? date('d M Y', strtotime($task['due_date'])) : '-'; ?></td>
                        <td><span class="badge badge-<?php echo strtolower($task['status']); ?>"><?php echo $task['status']; ?></span></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once "includes/footer.php"; ?>
