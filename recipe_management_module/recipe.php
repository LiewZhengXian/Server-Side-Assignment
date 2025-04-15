<?php
session_start();

$redirectPage = (isset($_SESSION['isAdmin']) && $_SESSION["isAdmin"] == 1) 
    ? 'recipe_admin.php'
    : 'recipe_user.php';

// Redirect to the appropriate page
header("Location: $redirectPage");
exit();
