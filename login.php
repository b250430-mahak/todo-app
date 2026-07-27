<?php
require_once "includes/session_init.php";
require_once "config/db.php";
require_once "includes/flash.php";

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$errors = [];
$email = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = "Both email and password are required.";
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT id, name, password FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            header("Location: dashboard.php");
            exit();
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | To-Do App</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-body" onload="applySavedTheme()">

    <div class="auth-card">
        <h1 class="auth-title">&#9989; Welcome Back</h1>
        <p class="auth-subtitle">Login to manage your tasks</p>

        <?php require_once "includes/flash.php"; show_flash(); ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" onsubmit="return validateLoginForm()" novalidate>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Enter your email">
                <small class="error-text" id="emailError"></small>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password">
                <small class="error-text" id="passwordError"></small>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>

        <p class="auth-footer">Don't have an account? <a href="register.php">Register here</a></p>
    </div>

<script src="assets/js/script.js"></script>
</body>
</html>
