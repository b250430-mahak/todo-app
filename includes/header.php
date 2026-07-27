<?php

if (!isset($pageTitle)) {
    $pageTitle = "To-Do List";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> | To-Do App</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body onload="applySavedTheme()">

<div class="app-wrapper">

    <?php include "includes/sidebar.php"; ?>

    <div class="main-content">

        <!-- Top bar -->
        <div class="topbar">
            <button id="menuToggle" class="menu-toggle" onclick="toggleSidebar()">&#9776;</button>
            <h2 class="page-title"><?php echo htmlspecialchars($pageTitle); ?></h2>
            <div class="topbar-right">
                <span class="welcome-text">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <button class="theme-btn" onclick="toggleTheme()" title="Toggle dark/light mode">&#9788;</button>
            </div>
        </div>

        <div class="page-content">
            <?php
                require_once "includes/flash.php";
                show_flash();
            ?>
