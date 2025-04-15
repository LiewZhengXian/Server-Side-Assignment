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
    // Redirect to the user recipe page
    header("Location: recipe_user.php");
    exit();
}
