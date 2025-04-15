<?php
session_start();

// Determine the redirection based on user role
if (!empty($_SESSION['isAdmin'])) {
    $redirectPage = ($_SESSION['isAdmin'] == 1) ? 'recipe_admin.php' : 'recipe_user.php';
} else {
    $redirectPage = 'recipe_user.php';
}

// Redirect to the appropriate page
header("Location: $redirectPage");
exit();
