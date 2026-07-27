<?php
// Figure out which page is currently open so we can highlight
// the matching sidebar link (simple active-state check).
$current = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <span class="logo-icon">&#10003;</span> TaskManager
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="<?php echo $current == 'dashboard.php' ? 'active' : ''; ?>">
            &#128202; Dashboard
        </a>
        <a href="tasks.php" class="<?php echo $current == 'tasks.php' ? 'active' : ''; ?>">
            &#128221; My Tasks
        </a>
        <a href="add_task.php" class="<?php echo $current == 'add_task.php' ? 'active' : ''; ?>">
            &#10133; Add Task
        </a>
        <a href="categories.php" class="<?php echo $current == 'categories.php' ? 'active' : ''; ?>">
            &#128193; Categories
        </a>
        <a href="logout.php" class="logout-link">
            &#128682; Logout
        </a>
    </nav>
</div>
