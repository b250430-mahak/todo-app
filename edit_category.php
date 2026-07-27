<?php
require_once "includes/auth_check.php";
require_once "config/db.php";
require_once "includes/flash.php";

$userId = $_SESSION['user_id'];
$errors = [];

$catId = $_GET['id'] ?? $_POST['category_id'] ?? '';
if ($catId === '' || !is_numeric($catId)) {
    set_flash('error', "Invalid category.");
    header("Location: categories.php");
    exit();
}
$catId = (int) $catId;

$stmt = mysqli_prepare($conn, "SELECT * FROM categories WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $catId, $userId);
mysqli_stmt_execute($stmt);
$category = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$category) {
    set_flash('error', "Category not found.");
    header("Location: categories.php");
    exit();
}

$name = $category['name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    if ($name === '') {
        $errors[] = "Category name is required.";
    } elseif (strlen($name) > 50) {
        $errors[] = "Category name is too long (max 50 characters).";
    } else {
        $check = mysqli_prepare($conn, "SELECT id FROM categories WHERE user_id = ? AND name = ? AND id != ?");
        mysqli_stmt_bind_param($check, "isi", $userId, $name, $catId);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);
        if (mysqli_stmt_num_rows($check) > 0) {
            $errors[] = "You already have a category with this name.";
        }
        mysqli_stmt_close($check);
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "UPDATE categories SET name = ? WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, "sii", $name, $catId, $userId);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            set_flash('success', "Category updated successfully.");
            header("Location: categories.php");
            exit();
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
    }
}

$pageTitle = "Edit Category";
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

    <form method="POST" action="edit_category.php?id=<?php echo $catId; ?>" onsubmit="return validateCategoryForm()" novalidate>
        <input type="hidden" name="category_id" value="<?php echo $catId; ?>">
        <div class="form-group">
            <label for="name">Category Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>">
            <small class="error-text" id="categoryError"></small>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Category</button>
            <a href="categories.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once "includes/footer.php"; ?>
