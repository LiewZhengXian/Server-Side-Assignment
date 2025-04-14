<?php
session_start();
require '../user_module/database.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to delete a recipe.";
    header("Location: ../user_module/login.php");
    exit();
}

// Validate the recipe ID
if (!isset($_GET['recipe_id']) || !is_numeric($_GET['recipe_id'])) {
    $_SESSION['error'] = "Invalid recipe ID.";
    header("Location: recipe.php");
    exit();
}

$recipe_id = intval($_GET['recipe_id']);
$user_id = $_SESSION['user_id'];

try {
    // Check if the recipe belongs to the logged-in user
    $sql = "SELECT user_id FROM Recipe WHERE recipe_id = ?";
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $con->error);
    }

    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $stmt->bind_result($recipe_user_id);
    if (!$stmt->fetch()) {
        $_SESSION['error'] = "Recipe not found.";
        $stmt->close();
        header("Location: recipe.php");
        exit();
    }
    $stmt->close();

    // Check if the logged-in user owns the recipe
    if ($recipe_user_id != $user_id) {
        $_SESSION['error'] = "You are not authorized to delete this recipe.";
        header("Location: recipe.php");
        exit();
    }

    // Delete the recipe
    $sql = "DELETE FROM Recipe WHERE recipe_id = ?";
    $stmt = $con->prepare($sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $con->error);
    }

    $stmt->bind_param("i", $recipe_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Recipe deleted successfully!";
    } else {
        throw new Exception("Failed to delete recipe. Error: " . $stmt->error);
    }
    $stmt->close();
} catch (Exception $e) {
    $_SESSION['error'] = "An error occurred: " . $e->getMessage();
} finally {
    $con->close();
    header("Location: recipe.php");
    exit();
}
