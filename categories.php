<?php
/*
    Categories Page
    ------------------
    Lists all categories created by the user, shows how many
    tasks are in each one, and provides a form to add a new
    category. Editing and deleting are handled by separate
    small pages (edit_category.php, delete_category.php).
*/
require_once "includes/auth_check.php";
require_once "config/db.php";
require_once "includes/flash.php";

$userId = $_SESSION['user_id'];
$errors = [];
$name = "";

// ---------- Handle "Add Category" form ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    if ($name === '') {
        $errors[] = "Category name is required.";
    } elseif (strlen($name) > 50) {
        $errors[] = "Category name is too long (max 50 characters).";
    } else {
        // Check for duplicate category name for this user
        $check = mysqli_prepare($conn, "SELECT id FROM categories WHERE user_id = ? AND name = ?");
        mysqli_stmt_bind_param($check, "is", $userId, $name);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);
        if (mysqli_stmt_num_rows($check) > 0) {
            $errors[] = "You already have a category with this name.";
        }
        mysqli_stmt_close($check);
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO categories (user_id, name) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "is", $userId, $name);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            set_flash('success', "Category added successfully.");
            header("Location: categories.php");
            exit();
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
    }
}

// ---------- Get all categories with a task count for each ----------
$stmt = mysqli_prepare($conn, "SELECT c.id, c.name, COUNT(t.id) AS task_count
    FROM categories c
    LEFT JOIN tasks t ON t.category_id = c.id
    WHERE c.user_id = ?
    GROUP BY c.id, c.name
    ORDER BY c.name");
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$categories = mysqli_stmt_get_result($stmt);

$pageTitle = "Categories";
require_once "includes/header.php";
?>

<div class="card form-card">
    <h3>Add New Category</h3>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="categories.php" class="inline-form" onsubmit="return validateCategoryForm()" novalidate>
        <input type="text" name="name" placeholder="e.g. Fitness, Travel..." value="<?php echo htmlspecialchars($name); ?>">
        <small class="error-text" id="categoryError"></small>
        <button type="submit" class="btn btn-primary">Add Category</button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3>Your Categories</h3>
    </div>

    <?php if (mysqli_num_rows($categories) === 0): ?>
        <p class="empty-text">No categories yet. Add one above.</p>
    <?php else: ?>
        <div class="category-grid">
            <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                <div class="category-card">
                    <div class="category-name"><?php echo htmlspecialchars($cat['name']); ?></div>
                    <div class="category-count"><?php echo $cat['task_count']; ?> task(s)</div>
                    <div class="category-actions">
                        <a href="edit_category.php?id=<?php echo $cat['id']; ?>" class="action-link" title="Edit">&#9998; Edit</a>
                        <a href="delete_category.php?id=<?php echo $cat['id']; ?>" class="action-link delete-link" title="Delete" onclick="return confirmDelete('category')">&#128465; Delete</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once "includes/footer.php"; ?>
