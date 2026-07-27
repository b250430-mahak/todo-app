# To-Do List with Category Management System

A simple, beginner-friendly college mini-project built with **Core PHP, MySQL,
HTML, CSS, and JavaScript** (no frameworks).

## Features

- User Registration, Login, Logout (session-based)
- Add / Edit / Delete tasks
- Mark tasks as Completed / Pending
- Create, edit, delete categories (Study, Work, Personal, Shopping, Health...)
- Assign a category to each task
- Search tasks by title
- Filter tasks by category and by status
- Set due date and priority (High / Medium / Low)
- Dashboard with total / completed / pending task counts
- Responsive layout (mobile, tablet, desktop)
- Dark / Light mode toggle
- Client-side + server-side form validation
- Prepared statements everywhere (SQL injection protection)
- Passwords hashed with `password_hash()` (never stored in plain text)

## Folder Structure

```
todo-app/
│
├── config/
│   └── db.php                 -> database connection settings
│
├── includes/
│   ├── session_init.php       -> starts PHP session
│   ├── auth_check.php         -> blocks pages from guests
│   ├── flash.php              -> success/error message helper
│   ├── header.php             -> top of every logged-in page
│   ├── sidebar.php            -> left navigation menu
│   └── footer.php             -> bottom of every logged-in page
│
├── assets/
│   ├── css/style.css          -> all styling (light + dark theme)
│   └── js/script.js           -> validation, theme toggle, sidebar toggle
│
├── database/
│   └── todo_db.sql            -> run this to create the database
│
├── index.php                  -> redirects to login or dashboard
├── register.php
├── login.php
├── logout.php
├── dashboard.php
├── tasks.php                  -> task list + search + filters
├── add_task.php
├── edit_task.php
├── delete_task.php
├── toggle_status.php          -> flips Pending <-> Completed
├── categories.php             -> list + add category
├── edit_category.php
└── delete_category.php
```

## Setup Instructions (using XAMPP / WAMP)

1. Install **XAMPP** (or WAMP) and start **Apache** and **MySQL**.
2. Copy the `todo-app` folder into your server's web root:
   - XAMPP: `C:\xampp\htdocs\todo-app`
   - WAMP: `C:\wamp64\www\todo-app`
   - Linux/macOS (with a local PHP/MySQL server): your configured web root
3. Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
4. Click **Import**, choose `database/todo_db.sql`, and click **Go**.
   This creates the `todo_db` database with the `users`, `categories`,
   and `tasks` tables.
5. Open `config/db.php` and check the database settings match your setup
   (default XAMPP settings — user `root`, no password — already work).
6. Visit `http://localhost/todo-app/` in your browser.
7. Click **Register**, create an account, then log in.
   Five default categories (Study, Work, Personal, Shopping, Health) are
   created automatically for every new account.

## How It Works (quick explanation for viva)

- **Sessions**: `session_init.php` starts the session; `auth_check.php` is
  included at the top of every protected page and redirects to `login.php`
  if `$_SESSION['user_id']` is not set.
- **Security**: All database queries use `mysqli_prepare()` with `?`
  placeholders and `mysqli_stmt_bind_param()`, so user input is never
  concatenated directly into SQL (prevents SQL injection). Passwords are
  hashed with `password_hash()` and checked with `password_verify()`.
  All output printed back to the page goes through `htmlspecialchars()`
  to prevent XSS.
- **Validation**: Every form is validated twice — once in JavaScript
  (`assets/js/script.js`) for instant feedback, and again in PHP on the
  server (since JavaScript can be bypassed).
- **Categories & Tasks**: Each category and task row is linked to the
  logged-in user through a `user_id` foreign key, so users only ever see
  their own data. Deleting a category does not delete its tasks — it just
  sets their `category_id` to `NULL` (see `ON DELETE SET NULL` in the SQL
  file), so no task data is lost.
- **Search & Filter**: `tasks.php` reads `search`, `category_id`, and
  `status` from the URL (`$_GET`) and builds one SQL query with only the
  filters that were actually used, still using prepared statements.
- **Dark/Light Mode**: A CSS class `dark-theme` on `<body>` swaps a set of
  CSS variables (colors). The chosen theme is saved in the browser's
  `localStorage` so it stays the same on the next visit.

## Notes

- This project intentionally avoids frameworks (no Laravel, no Bootstrap,
  no React) so every line of code is easy to read and explain.
- Default MySQL credentials assume a local development setup. Change them
  in `config/db.php` for production use, and never commit real database
  passwords to a public repository.
