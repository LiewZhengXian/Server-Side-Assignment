<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


$currentPage = basename($_SERVER['PHP_SELF']);

$allowedPublicPages = ['index.php', 'login.php'];

if (!isset($_SESSION['user_id']) && !in_array($currentPage, $allowedPublicPages)) {


    $loginPageUrl = '/Server-Side-Assignment/user_module/login.php';

    // Perform the redirect
    // Use "Location:" followed by the URL.
    header("Location: " . $loginPageUrl);

    exit();
}


?>