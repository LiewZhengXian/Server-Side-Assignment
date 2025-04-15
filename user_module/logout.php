<?php
session_start();

// Destroy the session
session_destroy();

// Clear cookies for user login
if (isset($_COOKIE['user_id'])) {
    setcookie("user_id", "", time() - 3600, "/"); // Expire the cookie
}
if (isset($_COOKIE['username'])) {
    setcookie("username", "", time() - 3600, "/"); // Expire the cookie
}
if (isset($_COOKIE['email'])) {
    setcookie("email", "", time() - 3600, "/"); // Expire the cookie
}

// Redirect to the login page
header("Location: login.php");
exit();
