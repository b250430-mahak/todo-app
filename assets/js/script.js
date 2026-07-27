/* ============================================================
   To-Do List App — Client-side JavaScript
   Handles: dark/light theme toggle, mobile sidebar toggle,
   simple form validation, and delete confirmation dialogs.
   ============================================================ */

/* ---------- Dark / Light theme ---------- */

// Runs on every page load (called from <body onload="applySavedTheme()">)
function applySavedTheme() {
    var savedTheme = localStorage.getItem("theme");
    if (savedTheme === "dark") {
        document.body.classList.add("dark-theme");
    }
}

// Runs when the user clicks the theme toggle button
function toggleTheme() {
    document.body.classList.toggle("dark-theme");

    if (document.body.classList.contains("dark-theme")) {
        localStorage.setItem("theme", "dark");
    } else {
        localStorage.setItem("theme", "light");
    }
}

/* ---------- Mobile sidebar toggle ---------- */
function toggleSidebar() {
    var sidebar = document.getElementById("sidebar");
    if (sidebar) {
        sidebar.classList.toggle("open");
    }
}

/* ---------- Delete confirmation ---------- */
function confirmDelete(itemType) {
    return confirm("Are you sure you want to delete this " + itemType + "? This cannot be undone.");
}

/* ============================================================
   Form Validation (client-side)
   Note: The server (PHP) ALSO validates everything again.
   Client-side validation is just for a faster, friendlier
   experience — it is never trusted on its own.
   ============================================================ */

// ---------- Registration form ----------
function validateRegisterForm() {
    var name = document.getElementById("name").value.trim();
    var email = document.getElementById("email").value.trim();
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirm_password").value;

    var isValid = true;

    clearError("nameError");
    clearError("emailError");
    clearError("passwordError");
    clearError("confirmError");

    if (name === "") {
        showError("nameError", "Name is required.");
        isValid = false;
    }

    if (!isValidEmail(email)) {
        showError("emailError", "Please enter a valid email address.");
        isValid = false;
    }

    if (password.length < 6) {
        showError("passwordError", "Password must be at least 6 characters.");
        isValid = false;
    }

    if (password !== confirmPassword) {
        showError("confirmError", "Passwords do not match.");
        isValid = false;
    }

    return isValid;
}

// ---------- Login form ----------
function validateLoginForm() {
    var email = document.getElementById("email").value.trim();
    var password = document.getElementById("password").value;

    var isValid = true;

    clearError("emailError");
    clearError("passwordError");

    if (!isValidEmail(email)) {
        showError("emailError", "Please enter a valid email address.");
        isValid = false;
    }

    if (password === "") {
        showError("passwordError", "Password is required.");
        isValid = false;
    }

    return isValid;
}

// ---------- Add / Edit task form ----------
function validateTaskForm() {
    var title = document.getElementById("title").value.trim();
    var isValid = true;

    clearError("titleError");

    if (title === "") {
        showError("titleError", "Task title is required.");
        isValid = false;
    } else if (title.length > 150) {
        showError("titleError", "Title must be under 150 characters.");
        isValid = false;
    }

    return isValid;
}

// ---------- Add / Edit category form ----------
function validateCategoryForm() {
    var nameField = document.querySelector('input[name="name"]');
    var name = nameField.value.trim();
    var isValid = true;

    clearError("categoryError");

    if (name === "") {
        showError("categoryError", "Category name is required.");
        isValid = false;
    } else if (name.length > 50) {
        showError("categoryError", "Category name must be under 50 characters.");
        isValid = false;
    }

    return isValid;
}

/* ---------- Small helper functions ---------- */
function isValidEmail(email) {
    var pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return pattern.test(email);
}

function showError(elementId, message) {
    var el = document.getElementById(elementId);
    if (el) {
        el.textContent = message;
    }
}

function clearError(elementId) {
    var el = document.getElementById(elementId);
    if (el) {
        el.textContent = "";
    }
}
