<?php
session_start();
require '../user_module/database.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $recipe_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Check if the recipe belongs to the logged-in user
    $sql = "SELECT user_id FROM Recipe WHERE recipe_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $stmt->bind_result($recipe_user_id);
    $stmt->fetch();
    $stmt->close();

    if ($recipe_user_id == $user_id) {
        // Delete the recipe
        $sql = "DELETE FROM Recipe WHERE recipe_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $recipe_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Recipe deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete recipe. Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "You are not authorized to delete this recipe.";
    }
} else {
    $_SESSION['error'] = "Invalid recipe ID.";
}

$con->close();
header("Location: recipe.php");
exit();
?>