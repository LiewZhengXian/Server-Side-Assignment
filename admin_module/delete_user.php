<?php
session_start();
require '../user_module/database.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
    header("Location: ../index.php"); // Redirect non-admin users to the homepage
    exit();
}

// Check if user_id is provided via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);

    // Prevent deletion of admin accounts
    $sql = "SELECT isAdmin FROM User WHERE user_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($isAdmin);
    $stmt->fetch();
    $stmt->close();

    if ($isAdmin == 1) {
        // Redirect back with an error message if trying to delete an admin
        $_SESSION['error'] = "Admin accounts cannot be deleted.";
        header("Location: manage_user.php");
        exit();
    }

    // Delete the user from the database
    $sql = "DELETE FROM User WHERE user_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        // Set a success message in the session
        $_SESSION['success'] = "User deleted successfully!";
    } else {
        // Set an error message in the session
        $_SESSION['error'] = "Failed to delete the user. Please try again.";
    }

    $stmt->close();
} else {
    // Redirect back with an error message if no user_id is provided
    $_SESSION['error'] = "Invalid request.";
}

// Redirect back to the manage_user.php page
header("Location: manage_user.php");
exit();
