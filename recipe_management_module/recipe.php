<?php
session_start();

// Check if the user is logged in and if isAdmin is set
if (isset($_SESSION['isAdmin'])) {
    if ($_SESSION['isAdmin'] == 1) {
        // Redirect to the admin recipe page
        header("Location: recipe_admin.php");
        exit();
    } else {
        // Redirect to the user recipe page
        header("Location: recipe_user.php");
        exit();
    }
} else {
    // If the user is not logged in, redirect to the login page
    header("Location: ../user_module/login.php");
    exit();
}
